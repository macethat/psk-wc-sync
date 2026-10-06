<?php
/**
 * Plugin Name: SP - Combo stock status sync
 * Description: Mantiene el _stock_status de los combos (grouped) alineado con la disponibilidad de sus productos hijos. Asi el filtro "ocultar agotados" de la tienda/categorias (y el home) oculta el combo cuando falta un componente.
 * Version: 1.0.0
 */

defined('ABSPATH') || exit;

/** Devuelve true (todos los hijos en stock), false (alguno agotado) o null (no es combo grouped). */
function sp_combo_child_stock_ok($combo_id) {
    $p = wc_get_product($combo_id);
    if (!$p || !$p->is_type('grouped')) {
        return null;
    }
    foreach ($p->get_children() as $cid) {
        $c = wc_get_product($cid);
        if (!$c || !$c->is_in_stock()) {
            return false;
        }
    }
    return true;
}

/** Recalcula el _stock_status de todos los combos (o de uno). */
function sp_combos_sync_status($only_id = 0) {
    $ids = $only_id ? array((int) $only_id) : get_posts(array(
        'post_type'      => 'product',
        'post_status'    => array('publish', 'draft'),
        'posts_per_page' => -1,
        'fields'         => 'ids',
        'tax_query'      => array(array('taxonomy' => 'product_cat', 'field' => 'slug', 'terms' => 'combos')),
    ));
    $changed = 0;
    foreach ($ids as $id) {
        $ok = sp_combo_child_stock_ok($id);
        if ($ok === null) {
            continue;
        }
        $status = $ok ? 'instock' : 'outofstock';
        if (get_post_meta($id, '_stock_status', true) !== $status) {
            update_post_meta($id, '_stock_status', $status);
            wc_delete_product_transients($id);
            $changed++;
        }
    }
    return $changed;
}

/* Marca "sucio" cuando cambia el stock de cualquier producto/variacion, y
   recalcula una sola vez en la siguiente carga (para no cargar cada request). */
add_action('woocommerce_product_set_stock_status', function () { update_option('sp_combo_stock_dirty', 1); }, 20);
add_action('woocommerce_variation_set_stock_status', function () { update_option('sp_combo_stock_dirty', 1); }, 20);
add_action('init', function () {
    if (get_option('sp_combo_stock_dirty')) {
        sp_combos_sync_status();
        delete_option('sp_combo_stock_dirty');
    }
});
