<?php
/**
 * Plugin Name: SP GBP Products Sync
 * Description: Sincroniza el top de proteínas de WooCommerce como "Productos" (Productos de la ficha) en cada Google Business Profile de sucursal.
 * Version: 1.0.0
 *
 * Reutiliza el OAuth de GBP ya existente (functions-additions.php: sp_gbp_get_access_token, sp_gbp_request,
 * sp_gbp_get_accounts, sp_gbp_get_locations, sp_gbp_find_location).
 *
 * Uso manual:  wp sp-gbp-sync --dry-run          (no escribe, solo muestra)
 *              wp sp-gbp-sync --branch=megapolis (una sucursal)
 *              wp sp-gbp-sync                    (todas)
 *
 * Cron: semanal (sp_gbp_products_sync). Requiere aumento de cuota (por defecto 1 req/min).
 */

if (!defined('ABSPATH')) exit;

define('SP_GBP_PRODUCT_CATEGORY', 'Proteínas');
define('SP_GBP_PRODUCT_LIMIT', 15);
define('SP_GBP_PRODUCTS_HOOK', 'sp_gbp_products_sync');
define('SP_GBP_API_BASE', 'https://mybusiness.googleapis.com/v4/');

/* ------------------------------------------------------------------ */
/* Request con método (POST/PATCH) + JSON, reutilizando el token OAuth */
/* ------------------------------------------------------------------ */
function sp_gbp_api_request($method, $url, $body = null) {
    if (!function_exists('sp_gbp_get_access_token')) {
        return array('error' => 'Sin integración GBP (functions-additions)');
    }
    $token = sp_gbp_get_access_token();
    if (!$token) {
        return array('error' => 'No access token');
    }
    $args = array(
        'method'  => $method,
        'timeout' => 25,
        'headers' => array(
            'Authorization' => 'Bearer ' . $token,
            'Content-Type'  => 'application/json',
        ),
    );
    if ($body !== null) {
        $args['body'] = wp_json_encode($body);
    }
    $resp = wp_remote_request($url, $args);
    if (is_wp_error($resp)) {
        return array('error' => $resp->get_error_message());
    }
    $code = wp_remote_retrieve_response_code($resp);
    $json = json_decode(wp_remote_retrieve_body($resp), true);
    return array('code' => $code, 'body' => $json);
}

/* ------------------------------------------------------------------ */
/* Top de proteínas a sincronizar (categoría "proteinas", sin combos)  */
/* ------------------------------------------------------------------ */
function sp_gbp_top_proteins($limit = SP_GBP_PRODUCT_LIMIT) {
    if (!function_exists('wc_get_products')) {
        return array();
    }
    $args = array(
        'limit'    => max(1, (int) $limit),
        'status'   => 'publish',
        'category' => array('proteinas'),
        'orderby'  => 'popularity',
        'order'    => 'DESC',
        'return'   => 'objects',
    );
    $args = apply_filters('sp_gbp_top_proteins_args', $args, $limit);
    $products = wc_get_products($args);
    $out = array();
    foreach ($products as $p) {
        if (!$p) continue;
        if ($p->get_type() === 'grouped' || get_post_meta($p->get_id(), '_combo_price', true) !== '') continue;
        if (!$p->is_in_stock()) continue;
        $out[] = $p;
    }
    return $out;
}

/* ------------------------------------------------------------------ */
/* Construir el payload de un producto GBP a partir de un WC_Product    */
/* ------------------------------------------------------------------ */
function sp_gbp_product_payload($product) {
    $price = (float) $product->get_price();
    if ($price <= 0) {
        return null; // sin precio no se expone (evita datos inventados)
    }
    $img_id = $product->get_image_id();
    $img = $img_id ? wp_get_attachment_image_url($img_id, 'full') : '';
    if (!$img) {
        $img = wc_placeholder_img_src('full');
    }
    if (strpos($img, 'http') !== 0) {
        $img = home_url($img);
    }
    $url = $product->get_permalink();
    $url = add_query_arg(array(
        'utm_source'   => 'gbp',
        'utm_medium'   => 'producto',
        'utm_campaign' => 'local',
    ), $url);

    return array(
        'productName' => mb_substr($product->get_name(), 0, 100),
        'category'    => SP_GBP_PRODUCT_CATEGORY,
        'description' => mb_substr(wp_strip_all_tags($product->get_short_description() ?: $product->get_name()), 0, 550),
        'price'       => array(
            'currencyCode' => function_exists('get_woocommerce_currency') ? get_woocommerce_currency() : 'USD',
            'units'        => (string) (int) floor($price),
            'nanos'        => (int) round(($price - floor($price)) * 1000000000),
        ),
        'imageUrl'    => $img,
        'url'         => $url,
    );
}

/* ------------------------------------------------------------------ */
/* Listar productos existentes de una location                         */
/* ------------------------------------------------------------------ */
function sp_gbp_list_products($location_name) {
    $url = SP_GBP_API_BASE . $location_name . '/products?pageSize=100';
    $r = sp_gbp_api_request('GET', $url);
    if (!empty($r['error'])) return array('error' => $r['error']);
    if (isset($r['code']) && $r['code'] >= 400) return array('error' => 'HTTP ' . $r['code'] . ' ' . wp_json_encode($r['body']));
    return array('products' => isset($r['body']['products']) ? $r['body']['products'] : array());
}

/* ------------------------------------------------------------------ */
/* Sincronizar una location                                            */
/* ------------------------------------------------------------------ */
function sp_gbp_sync_location($location_name, $limit, $dry = true) {
    $out = array('location' => $location_name, 'created' => 0, 'updated' => 0, 'skipped' => 0, 'errors' => array());
    $products = sp_gbp_top_proteins($limit);
    if (!$products) {
        $out['errors'][] = 'Sin proteínas elegibles';
        return $out;
    }

    $existing = sp_gbp_list_products($location_name);
    if (!empty($existing['error'])) {
        $out['errors'][] = 'List: ' . $existing['error'];
        return $out;
    }
    $by_name = array();
    foreach ($existing['products'] as $ep) {
        $by_name[strtolower(trim($ep['productName'] ?? ''))] = $ep;
    }

    foreach ($products as $p) {
        $payload = sp_gbp_product_payload($p);
        if (!$payload) { $out['skipped']++; continue; }
        $key = strtolower(trim($payload['productName']));

        if (isset($by_name[$key])) {
            $pid = $by_name[$key]['productId'] ?? '';
            if ($dry) { $out['updated']++; continue; }
            if (!$pid) { $out['skipped']++; continue; }
            $url = SP_GBP_API_BASE . $location_name . '/products/' . rawurlencode($pid);
            $r = sp_gbp_api_request('PATCH', $url, $payload);
            if (!empty($r['error']) || (isset($r['code']) && $r['code'] >= 400)) {
                $out['errors'][] = 'Patch ' . $payload['productName'] . ': ' . ($r['error'] ?? ('HTTP ' . $r['code']));
                if (isset($r['code']) && $r['code'] === 429) break; // quota
            } else {
                $out['updated']++;
            }
        } else {
            if ($dry) { $out['created']++; continue; }
            $url = SP_GBP_API_BASE . $location_name . '/products';
            $r = sp_gbp_api_request('POST', $url, $payload);
            if (!empty($r['error']) || (isset($r['code']) && $r['code'] >= 400)) {
                $out['errors'][] = 'Create ' . $payload['productName'] . ': ' . ($r['error'] ?? ('HTTP ' . $r['code']));
                if (isset($r['code']) && $r['code'] === 429) break; // quota
            } else {
                $out['created']++;
            }
        }
        sleep(2); // suaviza el rate limit
    }
    return $out;
}

/* ------------------------------------------------------------------ */
/* Sincronizar todas las sucursales                                    */
/* ------------------------------------------------------------------ */
function sp_gbp_sync_all($branch = null, $limit = SP_GBP_PRODUCT_LIMIT, $dry = true) {
    if (!function_exists('sp_get_sucursales') || !function_exists('sp_gbp_find_location')) {
        return 'Faltan dependencias (sucursales/GBP)';
    }
    $sucursales = sp_get_sucursales(); // hub: keyed numerico {name,address} -> usar data.php con id
    $data = @include get_stylesheet_directory() . '/sucursales/data.php';
    if (!is_array($data)) return 'No se pudo leer sucursales/data.php';

    $results = array();
    foreach ($data as $s) {
        if ($branch && $s['id'] !== $branch) continue;
        $loc = sp_gbp_find_location($s);
        if (!$loc) {
            $results[] = array('branch' => $s['id'], 'error' => 'No se encontró location GBP');
            continue;
        }
        $results[] = sp_gbp_sync_location($loc, $limit, $dry);
    }
    update_option('sp_gbp_products_last_sync', current_time('mysql'));
    return $results;
}

/* ------------------------------------------------------------------ */
/* Cron semanal (opt-in: solo si la opción está activada)             */
/* Requiere cuota de la Business Profile API > 0 (ver README abajo).  */
/* Activar con:  wp option update sp_gbp_products_enabled 1           */
/* ------------------------------------------------------------------ */
add_action(SP_GBP_PRODUCTS_HOOK, function () {
    if (!get_option('sp_gbp_products_enabled')) return;
    sp_gbp_sync_all(null, SP_GBP_PRODUCT_LIMIT, false);
});
add_action('init', function () {
    if (get_option('sp_gbp_products_enabled') && !wp_next_scheduled(SP_GBP_PRODUCTS_HOOK)) {
        wp_schedule_event(time() + 300, 'weekly', SP_GBP_PRODUCTS_HOOK);
    }
});
register_deactivation_hook(__FILE__, function () {
    wp_clear_scheduled_hook(SP_GBP_PRODUCTS_HOOK);
});

/* ------------------------------------------------------------------ */
/* WP-CLI                                                             */
/* ------------------------------------------------------------------ */
if (defined('WP_CLI') && WP_CLI) {
    WP_CLI::add_command('sp-gbp-sync', function ($args, $assoc) {
        $branch = $assoc['branch'] ?? null;
        $limit  = isset($assoc['limit']) ? (int) $assoc['limit'] : SP_GBP_PRODUCT_LIMIT;
        $dry    = isset($assoc['dry-run']);
        WP_CLI::log('Sync GBP productos | branch=' . ($branch ?: 'todas') . ' limit=' . $limit . ' dry=' . ($dry ? 'si' : 'no'));
        $res = sp_gbp_sync_all($branch, $limit, $dry);
        if (is_string($res)) { WP_CLI::error($res); }
        foreach ($res as $r) {
            if (!empty($r['error'])) { WP_CLI::warning(wp_json_encode($r)); continue; }
            WP_CLI::log("loc={$r['location']} created={$r['created']} updated={$r['updated']} skipped={$r['skipped']}");
            foreach ($r['errors'] as $e) { WP_CLI::warning($e); }
        }
        WP_CLI::success('Listo');
    });
}
