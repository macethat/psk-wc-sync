<?php
/**
 * Plugin Name: SP - Badge NUEVO
 * Description: Marca con "NUEVO" los productos recien creados (primera subida) en la tienda, categorias y loops.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

/** ¿El producto es "nuevo"? (creado en los ultimos N dias) */
if (!function_exists('sp_es_nuevo')) {
    function sp_es_nuevo($product, $dias = 14) {
        if (!$product || !is_a($product, 'WC_Product')) {
            return false;
        }
        $d = $product->get_date_created();
        if (!$d) {
            return false;
        }
        return (time() - $d->getTimestamp()) <= ((int) $dias * DAY_IN_SECONDS);
    }
}

function sp_nuevo_badge_html() {
    return '<span class="sp-nuevo-badge">NUEVO</span>';
}

/* CSS del badge */
add_action('wp_head', function () {
    echo '<style id="sp-nuevo-badge-css">'
       . '.sp-nuevo-badge{position:absolute;top:12px;right:12px;z-index:6;background:#F65000;color:#fff;font-family:inherit;font-weight:800;font-size:12px;letter-spacing:.06em;text-transform:uppercase;padding:6px 10px;line-height:1;pointer-events:none}'
       . '.woocommerce ul.products li.product,.woocommerce-page ul.products li.product{position:relative}'
       . '.woocommerce ul.products li.product .product-inner,.woocommerce ul.products li.product .product-thumb,.product-inner{position:relative}'
       . '.sp-nuevo-badge--single{top:14px;right:14px;font-size:13px}'
       . 'li.product.sp-es-nuevo .product-transition{position:relative}'
       . 'li.product.sp-es-nuevo .product-transition::after{content:"NUEVO";position:absolute;top:12px;right:12px;z-index:6;background:#F65000;color:#fff;font-weight:800;font-size:12px;letter-spacing:.06em;text-transform:uppercase;padding:6px 10px;line-height:1}'
       . '</style>';
}, 20);

/* Badge en loops (tienda, categorias, relacionados, busqueda) */
add_action('woocommerce_before_shop_loop_item_title', function () {
    global $product;
    if ($product && sp_es_nuevo($product)) {
        echo sp_nuevo_badge_html();
    }
}, 4);

/* El tema Nutritix usa su propio hook para el loop */
add_action('nutritix_woocommerce_before_shop_loop_item_title', function () {
    global $product;
    if ($product && sp_es_nuevo($product)) {
        echo sp_nuevo_badge_html();
    }
}, 25);

/* Clase en el <li> del producto (para pintar el badge via CSS en el grid del tema) */
add_filter('woocommerce_post_class', function ($classes, $product) {
    if ($product && sp_es_nuevo($product)) {
        $classes[] = 'sp-es-nuevo';
    }
    return $classes;
}, 20, 2);

/* Badge en la ficha de producto (sobre la galeria) */
add_action('woocommerce_before_single_product_summary', function () {
    global $product;
    if ($product && sp_es_nuevo($product)) {
        echo '<span class="sp-nuevo-badge sp-nuevo-badge--single">NUEVO</span>';
    }
}, 4);
