<?php
/**
 * Plugin Name: SP - Compra online y ahorras X%
 * Description: Muestra el % de ahorro en la ficha, en las miniaturas de listas y lo expone para el home.
 * Version: 1.0
 */

defined('ABSPATH') || exit;

/** % de ahorro de un producto (simple o variable). */
function sp_pct_ahorro($product) {
    if (!$product || !is_a($product, 'WC_Product') || !$product->is_on_sale()) {
        return 0;
    }
    if ($product->is_type('variable')) {
        $reg  = (float) $product->get_variation_regular_price('min', true);
        $sale = (float) $product->get_variation_sale_price('min', true);
    } else {
        $reg  = (float) $product->get_regular_price();
        $sale = (float) $product->get_sale_price();
        if ($sale <= 0) $sale = (float) $product->get_price();
    }
    if ($reg <= 0 || $sale <= 0 || $sale >= $reg) return 0;
    return (int) round(100 * (1 - $sale / $reg));
}

/** 1) Miniaturas de listas: reemplaza el flash "¡Oferta!" por el % de ahorro. */
add_filter('woocommerce_sale_flash', 'sp_sale_flash_ahorro', 20, 3);
function sp_sale_flash_ahorro($html, $post, $product) {
    $pct = sp_pct_ahorro($product);
    if ($pct <= 0) return $html;
    return '<span class="onsale sp-ahorro-badge">Ahorras ' . $pct . '%</span>';
}

/** 2) Ficha: badge debajo del precio. */
add_action('woocommerce_single_product_summary', 'sp_single_ahorro_badge', 11);
function sp_single_ahorro_badge() {
    global $product;
    $pct = sp_pct_ahorro($product);
    if ($pct <= 0) return;
    echo '<div class="sp-single-ahorro">Compra online y ahorras <strong>' . $pct . '%</strong></div>';
}

/** 3) Estilos. */
add_action('wp_head', 'sp_ahorro_styles', 99);
function sp_ahorro_styles() {
    echo '<style id="sp-ahorro-css">'
        . '.sp-ahorro-badge{background:#E20613 !important;color:#fff !important;border-radius:6px !important;font-family:inherit;font-weight:700;letter-spacing:.02em;}'
        . '.sp-single-ahorro{display:inline-block;background:#e6f6ea;color:#00832f;border:1px solid #bfe6c8;font-family:inherit;font-weight:700;font-size:14px;padding:8px 14px;border-radius:8px;margin:6px 0 10px;}'
        . '.sp-single-ahorro strong{color:#00832f;}'
        . '</style>';
}
