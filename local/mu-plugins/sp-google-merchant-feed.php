<?php
/**
 * Plugin Name: SP Google Merchant Feed
 * Description: Genera un feed XML (RSS 2.0 + namespace g:) para Google Merchant Center en /google-feed.xml
 * Version: 1.0.0
 *
 * - Simple  -> 1 item
 * - Variable -> 1 item por variación (con g:item_group_id)
 * - Grouped (combos) -> 1 item con _combo_price
 *
 * No inventa datos: si falta marca/GTIN, se omite; si no hay precio, se omite el producto.
 */

if (!defined('ABSPATH')) exit;

/* ------------------------------------------------------------- */
/* Ruta /google-feed.xml                                          */
/* ------------------------------------------------------------- */
add_action('init', function () {
    add_rewrite_rule('^google-feed/?$', 'index.php?sp_gmc_feed=1', 'top');
    add_rewrite_rule('^google-feed\.xml/?$', 'index.php?sp_gmc_feed=1', 'top');
});
add_filter('query_vars', function ($vars) {
    $vars[] = 'sp_gmc_feed';
    return $vars;
});
add_action('template_redirect', function () {
    if (get_query_var('sp_gmc_feed')) {
        sp_gmc_render_feed();
        exit;
    }
});

/* ------------------------------------------------------------- */
/* Helpers                                                        */
/* ------------------------------------------------------------- */
function sp_gmc_brand($product_id) {
    foreach (array('product_brand', 'pa_brand', 'pa_marca', 'pwb-brand') as $tax) {
        if (taxonomy_exists($tax)) {
            $terms = wp_get_post_terms($product_id, $tax, array('fields' => 'names'));
            if (!is_wp_error($terms) && !empty($terms)) {
                return $terms[0];
            }
        }
    }
    return '';
}

function sp_gmc_gtin($sku) {
    $digits = preg_replace('/[^0-9]/', '', (string) $sku);
    $len = strlen($digits);
    return in_array($len, array(8, 12, 13, 14), true) ? $digits : '';
}

function sp_gmc_image($product) {
    $img_id = $product->get_image_id();
    $img = $img_id ? wp_get_attachment_image_url($img_id, 'full') : '';
    if (!$img) {
        $img = wc_placeholder_img_src('full');
    }
    if (strpos($img, 'http') !== 0) {
        $img = home_url($img);
    }
    return $img;
}

function sp_gmc_price($product) {
    $price = (float) $product->get_price();
    if ($price <= 0 && $product->is_type('grouped')) {
        $combo = get_post_meta($product->get_id(), '_combo_price', true);
        if ($combo !== '' && $combo !== false) {
            $price = (float) $combo;
        }
    }
    return $price > 0 ? $price : 0;
}

function sp_gmc_category_path($product_id) {
    $terms = wp_get_post_terms($product_id, 'product_cat', array('fields' => 'ids'));
    if (is_wp_error($terms) || empty($terms)) return '';
    $skip = array('descuento-online', 'promociones', 'sin_categoria', 'uncategorized', 'combos');
    $names = array();
    foreach ($terms as $tid) {
        $t = get_term($tid, 'product_cat');
        if (!$t || is_wp_error($t) || in_array($t->slug, $skip, true)) continue;
        $chain = array_reverse(get_ancestors($tid, 'product_cat'));
        $chain[] = $tid;
        $parts = array();
        foreach ($chain as $cid) {
            $tt = get_term($cid, 'product_cat');
            if ($tt && !is_wp_error($tt)) $parts[] = $tt->name;
        }
        $names[] = implode(' > ', $parts);
    }
    return isset($names[0]) ? $names[0] : '';
}

function sp_gmc_item($args) {
    $g = 'http://base.google.com/ns/1.0';
    $out  = '<item>' . "\n";
    $out .= '  <g:id>' . esc_html($args['id']) . "</g:id>\n";
    $out .= '  <title>' . esc_html($args['title']) . "</title>\n";
    $out .= '  <description>' . esc_html($args['description']) . "</description>\n";
    $out .= '  <link>' . esc_url($args['link']) . "</link>\n";
    $out .= '  <g:image_link>' . esc_url($args['image']) . "</g:image_link>\n";
    $out .= '  <g:availability>' . ($args['in_stock'] ? 'in_stock' : 'out_of_stock') . "</g:availability>\n";
    $out .= '  <g:price>' . esc_html($args['price']) . "</g:price>\n";
    $out .= '  <g:condition>new</g:condition>' . "\n";
    if (!empty($args['brand']))   { $out .= '  <g:brand>' . esc_html($args['brand']) . "</g:brand>\n"; }
    if (!empty($args['gtin']))    { $out .= '  <g:gtin>' . esc_html($args['gtin']) . "</g:gtin>\n"; }
    if (!empty($args['mpn']))     { $out .= '  <g:mpn>' . esc_html($args['mpn']) . "</g:mpn>\n"; }
    if (!empty($args['group_id'])){ $out .= '  <g:item_group_id>' . esc_html($args['group_id']) . "</g:item_group_id>\n"; }
    if (!empty($args['category'])){ $out .= '  <g:product_type>' . esc_html($args['category']) . "</g:product_type>\n"; }
    $out .= '  <g:google_product_category>Health &amp; Beauty &gt; Health Care &gt; Fitness &amp; Nutrition &gt; Sports Nutrition</g:google_product_category>' . "\n";
    if (!empty($args['shipping'])) {
        $out .= "  <g:shipping>\n    <g:country>PA</g:country>\n    <g:service>Estándar</g:service>\n    <g:price>0.00 " . esc_html($args['currency']) . "</g:price>\n  </g:shipping>\n";
    }
    $out .= '</item>' . "\n";
    return $out;
}

/* ------------------------------------------------------------- */
/* Render del feed                                                */
/* ------------------------------------------------------------- */
function sp_gmc_render_feed() {
    if (!function_exists('wc_get_products')) {
        status_header(500);
        echo 'WooCommerce no disponible';
        return;
    }
    $currency = get_woocommerce_currency();
    $home = home_url('/');

    header('Content-Type: application/xml; charset=UTF-8');

    echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
    echo '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0">' . "\n";
    echo '<channel>' . "\n";
    echo '  <title>' . esc_html(get_bloginfo('name')) . '</title>' . "\n";
    echo '  <link>' . esc_url($home) . '</link>' . "\n";
    echo '  <description>' . esc_html(get_bloginfo('description') ?: get_bloginfo('name')) . '</description>' . "\n";

    $products = wc_get_products(array(
        'limit'   => -1,
        'status'  => 'publish',
        'orderby' => 'date',
        'order'   => 'DESC',
    ));

    foreach ($products as $p) {
        if (!$p) continue;
        $pid  = $p->get_id();
        $type = $p->get_type();

        if ($type === 'variable') {
            $parent_sku = $p->get_sku() ?: (string) $pid;
            foreach ($p->get_children() as $vid) {
                $v = wc_get_product($vid);
                if (!$v) continue;
                $price = sp_gmc_price($v);
                if ($price <= 0) continue;
                $vsku = $v->get_sku() ?: ((string) $vid);
                echo sp_gmc_item(array(
                    'id'          => $vsku,
                    'group_id'    => $parent_sku,
                    'title'       => $v->get_name(),
                    'description' => wp_strip_all_tags($v->get_short_description() ?: $v->get_name()),
                    'link'        => $v->get_permalink(),
                    'image'       => sp_gmc_image($v),
                    'in_stock'    => $v->is_in_stock(),
                    'price'       => number_format($price, 2, '.', '') . ' ' . $currency,
                    'currency'    => $currency,
                    'brand'       => sp_gmc_brand($vid) ?: sp_gmc_brand($pid),
                    'gtin'        => sp_gmc_gtin($vsku),
                    'mpn'         => '',
                    'category'    => sp_gmc_category_path($pid),
                    'shipping'    => false,
                ));
            }
            continue;
        }

        // simple o grouped (combo)
        $price = sp_gmc_price($p);
        if ($price <= 0) continue;
        $sku = $p->get_sku() ?: (string) $pid;
        echo sp_gmc_item(array(
            'id'          => $sku,
            'title'       => $p->get_name(),
            'description' => wp_strip_all_tags($p->get_short_description() ?: $p->get_name()),
            'link'        => $p->get_permalink(),
            'image'       => sp_gmc_image($p),
            'in_stock'    => $p->is_in_stock(),
            'price'       => number_format($price, 2, '.', '') . ' ' . $currency,
            'currency'    => $currency,
            'brand'       => sp_gmc_brand($pid),
            'gtin'        => sp_gmc_gtin($sku),
            'mpn'         => '',
            'category'    => sp_gmc_category_path($pid),
            'shipping'    => false,
        ));
    }

    echo '</channel>' . "\n";
    echo '</rss>' . "\n";
}
