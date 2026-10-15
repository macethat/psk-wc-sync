<?php
/**
 * Plugin Name: SP - Encargado de Sucursal (Pedidos + Catalogo/Estadisticas solo lectura)
 * Description: Rol shop_manager acotado. Menu: Solo WooCommerce (Pedidos, Productos, Informes). No puede crear/editar/borrar productos ni crear pedidos. Sin Ajustes, plugins ni configuracion de WordPress.
 * Version: 2.0.0
 */

defined('ABSPATH') || exit;

function sp_es_encargado_sucursal() {
    $u = wp_get_current_user();
    return $u && $u->exists() && in_array('shop_manager', (array) $u->roles, true);
}
function sp_sm_orders_url() { return admin_url('edit.php?post_type=shop_order'); }
function sp_sm_products_url() { return admin_url('edit.php?post_type=product'); }

/* 1) Menu: dejar solo WooCommerce (Pedidos, Productos, Informes) */
add_action('admin_menu', function () {
    if (!sp_es_encargado_sucursal()) return;

    global $menu, $submenu;

    remove_menu_page('index.php');
    remove_menu_page('edit.php');
    remove_menu_page('upload.php');
    remove_menu_page('edit.php?post_type=page');
    remove_menu_page('edit-comments.php');
    remove_menu_page('themes.php');
    remove_menu_page('plugins.php');
    remove_menu_page('users.php');
    remove_menu_page('tools.php');
    remove_menu_page('options-general.php');

    // Dejar solo WooCommerce e Informes (wc-reports)
    if (is_array($menu)) {
        foreach ($menu as $k => $item) {
            $slug = isset($item[2]) ? $item[2] : '';
            if ($slug === '' || $slug === 'separator' || $slug === 'woocommerce' || $slug === 'wc-reports') continue;
            unset($menu[$k]);
        }
    }

    // Dentro de WooCommerce: Pedidos, Productos, Informes
    if (isset($submenu['woocommerce']) && is_array($submenu['woocommerce'])) {
        $keep = array('edit.php?post_type=shop_order', 'edit.php?post_type=product', 'wc-reports');
        foreach ($submenu['woocommerce'] as $k => $s) {
            $slug = isset($s[2]) ? $s[2] : '';
            if (!in_array($slug, $keep, true)) {
                unset($submenu['woocommerce'][$k]);
            }
        }
    }
}, 9999);

/* 2) Guard: bloquear accesos no permitidos */
add_action('admin_init', function () {
    if (!sp_es_encargado_sucursal() || wp_doing_ajax()) return;

    $pagenow = isset($GLOBALS['pagenow']) ? $GLOBALS['pagenow'] : '';
    $page    = isset($_GET['page']) ? sanitize_text_field(wp_unslash($_GET['page'])) : '';
    $ptype   = isset($_GET['post_type']) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : '';

    if ($pagenow === 'index.php') { wp_safe_redirect(sp_sm_orders_url()); exit; }

    if ($pagenow === 'admin.php' && in_array($page, array('wc-admin', 'wc-settings', 'wc-status', 'wc-addons', 'wc-addons-section'), true)) {
        wp_safe_redirect(sp_sm_orders_url());
        exit;
    }
    // Productos: sin crear ni editar -> lista de productos
    if (in_array($pagenow, array('post-new.php', 'post.php'), true) && $ptype === 'product') {
        wp_safe_redirect(sp_sm_products_url());
        exit;
    }
    // Pedidos: sin crear -> lista de pedidos
    if ($pagenow === 'post-new.php' && $ptype === 'shop_order') {
        wp_safe_redirect(sp_sm_orders_url());
        exit;
    }
    // Listas de otros tipos (cupones, etc.) -> pedidos
    if ($pagenow === 'edit.php' && $ptype !== '' && !in_array($ptype, array('shop_order', 'product'), true)) {
        wp_safe_redirect(sp_sm_orders_url());
        exit;
    }
}, 1);

/* 3) Ocultar botones "Anadir ..." en Pedidos y Productos */
add_action('admin_head', function () {
    if (!sp_es_encargado_sucursal()) return;
    $ptype = isset($_GET['post_type']) ? sanitize_text_field(wp_unslash($_GET['post_type'])) : '';
    if (in_array($ptype, array('shop_order', 'product'), true)) {
        echo '<style>.page-title-action{display:none!important}</style>';
    }
});

/* 4) Catalogo SOLO LECTURA: bloquear escritura de productos */
add_filter('user_has_cap', function ($allcaps, $caps, $args, $user) {
    if (empty($user) || empty($user->roles) || !in_array('shop_manager', (array) $user->roles, true)) {
        return $allcaps;
    }
    $deny = array(
        'edit_product', 'edit_published_products', 'edit_others_products', 'edit_private_products',
        'publish_products',
        'delete_product', 'delete_products', 'delete_published_products', 'delete_others_products', 'delete_private_products',
        'create_products',
        'edit_product_terms', 'manage_product_terms', 'assign_product_terms',
    );
    foreach ((array) $caps as $cap) {
        if (in_array($cap, $deny, true)) {
            $allcaps[$cap] = false;
        }
    }
    return $allcaps;
}, 100, 4);
