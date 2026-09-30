<?php
/**
 * Plugin Name: SP - Ocultar agotados sin stock en sucursal
 * Description: Oculta del catalogo (tienda, categorias, marcas) los productos agotados que NO tienen stock en ninguna sucursal. Los que SI tienen stock en sucursal se mantienen (con el aviso de retiro).
 * Version: 1.0
 */

defined('ABSPATH') || exit;

add_action('pre_get_posts', 'sp_ocultar_agotados_query', 20);
function sp_ocultar_agotados_query($q) {
    if (is_admin() || !$q->is_main_query()) {
        return;
    }
    if (!function_exists('is_shop')) {
        return;
    }
    if (!(is_shop() || is_product_category() || is_product_tag() || is_product_taxonomy())) {
        return;
    }

    $meta   = (array) $q->get('meta_query');
    $meta[] = array(
        'relation' => 'OR',
        // Se queda si esta en stock online...
        array('key' => '_stock_status', 'value' => 'instock', 'compare' => '='),
        // ...o si tiene stock disponible en alguna sucursal (no vacio).
        array('key' => '_sucursales_disponibles', 'value' => '', 'compare' => '!='),
    );
    $q->set('meta_query', $meta);
}
