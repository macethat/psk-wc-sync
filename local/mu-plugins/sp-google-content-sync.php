<?php
/**
 * Plugin Name: SP Google Content API (Merchant Center) Sync
 * Description: Sincroniza productos de WooCommerce a Google Merchant Center vía Content API for Shopping (service account). Automático por cron.
 * Version: 1.0.0
 *
 * Requisitos (ver docs/GUIA-GMC-CONTENT-API.md):
 *  - Content API for Shopping habilitada en el proyecto GCP.
 *  - Service account con su JSON de clave.
 *  - El email del service account agregado como usuario en Merchant Center.
 *  - Definir en wp-config.php:
 *       define('SP_GC_MERCHANT_ID', '5825178723');
 *       define('SP_GC_SA_PATH', '/home/customer/sp-gcp-content-sa.json');  // FUERA del web root
 *
 * Uso:  wp sp-gc-sync [--dry-run] [--limit=N]
 */

if (!defined('ABSPATH')) exit;

define('SP_GC_SYNC_HOOK', 'sp_gc_content_sync');
define('SP_GC_LANG', 'es');
define('SP_GC_COUNTRY', 'PA');

/* ------------------------------------------------------------------ */
/* Auth: service account -> access token (scope content)              */
/* ------------------------------------------------------------------ */
function sp_gc_service_account() {
    $path = defined('SP_GC_SA_PATH') ? SP_GC_SA_PATH : '';
    if (!$path || !file_exists($path)) return null;
    $json = json_decode(file_get_contents($path), true);
    return (is_array($json) && !empty($json['client_email']) && !empty($json['private_key'])) ? $json : null;
}

function sp_gc_b64url($data) {
    return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
}

function sp_gc_access_token() {
    $cached = get_transient('sp_gc_token');
    if ($cached) return $cached;

    $sa = sp_gc_service_account();
    if (!$sa) return null;

    $now = time();
    $header = array('alg' => 'RS256', 'typ' => 'JWT');
    $claims = array(
        'iss'   => $sa['client_email'],
        'scope' => 'https://www.googleapis.com/auth/content',
        'aud'   => 'https://oauth2.googleapis.com/token',
        'iat'   => $now,
        'exp'   => $now + 3600,
    );
    $signing = sp_gc_b64url(wp_json_encode($header)) . '.' . sp_gc_b64url(wp_json_encode($claims));
    $key = openssl_pkey_get_private($sa['private_key']);
    if (!$key) return null;
    if (!openssl_sign($signing, $sig, $key, 'SHA256')) return null;
    $jwt = $signing . '.' . sp_gc_b64url($sig);

    $resp = wp_remote_post('https://oauth2.googleapis.com/token', array(
        'timeout' => 25,
        'body'    => array(
            'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
            'assertion'  => $jwt,
        ),
    ));
    if (is_wp_error($resp)) return null;
    $body = json_decode(wp_remote_retrieve_body($resp), true);
    if (empty($body['access_token'])) return null;
    set_transient('sp_gc_token', $body['access_token'], 50 * MINUTE_IN_SECONDS);
    return $body['access_token'];
}

/* ------------------------------------------------------------------ */
/* Construir el recurso "product" (Content API v2.1)                   */
/* ------------------------------------------------------------------ */
function sp_gc_product_resource($product, $group_id = '', $offer_attr = '') {
    if (!$product) return null;
    $price = (float) $product->get_price();
    if ($price <= 0 && $product->is_type('grouped')) {
        $combo = get_post_meta($product->get_id(), '_combo_price', true);
        if ($combo !== '' && $combo !== false) $price = (float) $combo;
    }
    if ($price <= 0) return null;

    $pid = $product->get_id();
    $sku = $product->get_sku() ?: ((string) $pid);
    if ($group_id === '') {
        // variación: group = padre; simple: offerId = sku
    }
    $img_id = $product->get_image_id();
    $img = $img_id ? wp_get_attachment_image_url($img_id, 'full') : '';
    if (!$img) $img = wc_placeholder_img_src('full');
    if (strpos($img, 'http') !== 0) $img = home_url($img);

    $brand = '';
    foreach (array('product_brand', 'pa_brand', 'pa_marca', 'pwb-brand') as $tax) {
        if (taxonomy_exists($tax)) {
            $terms = wp_get_post_terms($pid, $tax, array('fields' => 'names'));
            if (!is_wp_error($terms) && !empty($terms)) { $brand = $terms[0]; break; }
        }
    }

    $digits = preg_replace('/[^0-9]/', '', (string) $sku);
    $gtin = in_array(strlen($digits), array(8, 12, 13, 14), true) ? $digits : '';

    $offer_id = $group_id !== '' ? ($sku . '-' . md5($offer_attr)) : $sku;
    if ($offer_id === '') $offer_id = (string) $pid;

    $res = array(
        'offerId'         => $offer_id,
        'title'           => mb_substr($product->get_name(), 0, 150),
        'description'     => mb_substr(wp_strip_all_tags($product->get_short_description() ?: $product->get_name()), 0, 5000),
        'link'            => $product->get_permalink(),
        'imageLink'       => $img,
        'availability'    => $product->is_in_stock() ? 'in stock' : 'out of stock',
        'condition'       => 'new',
        'price'           => array('value' => number_format($price, 2, '.', ''), 'currency' => (function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'USD')),
        'channel'         => 'online',
        'contentLanguage' => SP_GC_LANG,
        'targetCountry'   => SP_GC_COUNTRY,
    );
    if ($brand !== '') $res['brand'] = $brand;
    if ($gtin !== '') {
        $res['gtin'] = $gtin;
    } else {
        $res['identifierExists'] = false;
    }
    if ($group_id !== '') $res['itemGroupId'] = $group_id;

    return $res;
}

/* ------------------------------------------------------------------ */
/* Seleccionar productos (todos, simples + variaciones + combos)        */
/* ------------------------------------------------------------------ */
function sp_gc_collect($limit = 0) {
    $products = wc_get_products(array('limit' => $limit > 0 ? $limit : -1, 'status' => 'publish'));
    $out = array();
    foreach ($products as $p) {
        if (!$p) continue;
        if ($p->get_type() === 'variable') {
            $parent_sku = $p->get_sku() ?: (string) $p->get_id();
            foreach ($p->get_children() as $vid) {
                $v = wc_get_product($vid);
                if (!$v) continue;
                $attrs = array();
                foreach ($v->get_attributes() as $k => $val) { $attrs[] = $k . '=' . $val; }
                $res = sp_gc_product_resource($v, $parent_sku, implode('|', $attrs));
                if ($res) $out[] = $res;
            }
            continue;
        }
        $res = sp_gc_product_resource($p);
        if ($res) $out[] = $res;
    }
    return $out;
}

/* ------------------------------------------------------------------ */
/* Push a Merchant Center (products.custombatch, lotes de 200)          */
/* ------------------------------------------------------------------ */
function sp_gc_push($resources, $dry = true) {
    $mid = defined('SP_GC_MERCHANT_ID') ? SP_GC_MERCHANT_ID : get_option('gla_merchant_id', '');
    if (!$mid) return array('error' => 'Falta SP_GC_MERCHANT_ID');
    $token = null;
    if (!$dry) {
        $token = sp_gc_access_token();
        if (!$token) return array('error' => 'No se pudo obtener access token (revisar service account)');
    }

    $ok = 0; $err = 0; $errors = array();
    foreach (array_chunk($resources, 200) as $chunk) {
        $entries = array();
        $i = 0;
        foreach ($chunk as $res) {
            $i++;
            $entries[] = array(
                'batchId'    => $i,
                'merchantId' => $mid,
                'method'     => 'insert',
                'product'    => $res,
            );
        }
        if ($dry) { $ok += count($entries); continue; }

        $resp = wp_remote_post('https://shoppingcontent.googleapis.com/content/v2.1/products/custombatch', array(
            'timeout' => 60,
            'headers' => array('Authorization' => 'Bearer ' . $token, 'Content-Type' => 'application/json'),
            'body'    => wp_json_encode(array('entries' => $entries)),
        ));
        if (is_wp_error($resp)) { $err += count($entries); $errors[] = $resp->get_error_message(); continue; }
        $body = json_decode(wp_remote_retrieve_body($resp), true);
        foreach (($body['entries'] ?? array()) as $e) {
            if (!empty($e['errors'])) { $err++; foreach ($e['errors']['errors'] as $er) { $errors[] = $er['message'] ?? 'error'; } }
            else { $ok++; }
        }
        sleep(1);
    }
    return array('ok' => $ok, 'err' => $err, 'errors' => array_slice($errors, 0, 20));
}

/* ------------------------------------------------------------------ */
/* Cron diario (opt-in) + WP-CLI                                       */
/* ------------------------------------------------------------------ */
add_action(SP_GC_SYNC_HOOK, function () {
    if (!get_option('sp_gc_sync_enabled')) return;
    sp_gc_push(sp_gc_collect(), false);
});
add_action('init', function () {
    if (get_option('sp_gc_sync_enabled') && !wp_next_scheduled(SP_GC_SYNC_HOOK)) {
        wp_schedule_event(time() + 180, 'daily', SP_GC_SYNC_HOOK);
    }
});
register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook(SP_GC_SYNC_HOOK);
});

if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('sp-gc-sync', function ($args, $assoc) {
        $dry = isset($assoc['dry-run']);
        $limit = isset($assoc['limit']) ? (int) $assoc['limit'] : 0;
        $res = sp_gc_collect($limit);
        WP_CLI::log('Productos recolectados: ' . count($res));
        if (defined('SP_GC_MERCHANT_ID')) WP_CLI::log('Merchant: ' . SP_GC_MERCHANT_ID);
        $push = sp_gc_push($res, $dry);
        if (!empty($push['error'])) { WP_CLI::error($push['error']); }
        WP_CLI::log('ok=' . $push['ok'] . ' err=' . $push['err']);
        foreach ($push['errors'] as $e) { WP_CLI::warning($e); }
        WP_CLI::success($dry ? 'Dry-run listo' : 'Sync listo');
    });
}
