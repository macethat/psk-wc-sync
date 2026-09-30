<?php
/**
 * Plugin Name: SP - Ocultar agotados sin stock en sucursal
 * Description: Oculta del catalogo (tienda, categorias, marcas, busqueda, relacionados y listas del home) los productos agotados que NO tienen stock en ninguna sucursal. Los que SI tienen stock en sucursal se mantienen (aviso de retiro).
 * Version: 2.0
 */

defined('ABSPATH') || exit;

/** ¿El producto esta disponible? (online o con stock en alguna sucursal) */
function sp_disponible($product) {
    if (!$product || !is_a($product, 'WC_Product')) {
        return false;
    }
    if ($product->is_in_stock()) {
        return true;
    }
    $sd = get_post_meta($product->get_id(), '_sucursales_disponibles', true);
    return ($sd !== '' && $sd !== false && $sd !== null);
}

/** 1) Filtro del catalogo: tienda, categorias, etiquetas, marcas, busqueda. */
add_action('pre_get_posts', 'sp_ocultar_agotados_query', 20);
function sp_ocultar_agotados_query($q) {
    if (is_admin() || !$q->is_main_query()) {
        return;
    }
    if (!function_exists('is_shop')) {
        return;
    }
    if (!(is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy() || is_search())) {
        return;
    }

    $meta   = (array) $q->get('meta_query');
    $meta[] = array(
        'relation' => 'OR',
        array('key' => '_stock_status', 'value' => 'instock', 'compare' => '='),
        array('key' => '_sucursales_disponibles', 'value' => '', 'compare' => '!='),
    );
    $q->set('meta_query', $meta);
}

/** 2) Productos relacionados. */
add_filter('woocommerce_related_products', 'sp_ocultar_agotados_related', 20, 3);
function sp_ocultar_agotados_related($related, $args, $product_id) {
    if (!is_array($related)) return $related;
    return array_values(array_filter($related, function ($id) {
        return sp_disponible(wc_get_product($id));
    }));
}
