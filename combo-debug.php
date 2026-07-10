<?php
// Debug: log what happens during stock validation for combo products
add_action('woocommerce_check_cart_items', function() {
    foreach (WC()->cart->get_cart() as $key => $item) {
        if (!empty($item['combo_children'])) {
            $p = $item['data'];
            error_log('[COMBO DEBUG] Cart item ' . $p->get_id() . ' qty=' . $item['quantity'] . ' manage=' . var_export($p->managing_stock(), true) . ' stock=' . var_export($p->get_stock_quantity(), true) . ' in_stock=' . var_export($p->is_in_stock(), true) . ' enough=' . var_export($p->has_enough_stock($item['quantity']), true));
        }
    }
}, 0);

add_action('woocommerce_checkout_process', function() {
    error_log('[COMBO DEBUG] woocommerce_checkout_process fired');
});

add_filter('woocommerce_cart_item_product', function($product, $cart_item, $cart_key) {
    if (!empty($cart_item['combo_children'])) {
        error_log('[COMBO DEBUG] cart_item_product filter: id=' . $product->get_id() . ' type=' . $product->get_type());
    }
    return $product;
}, 10, 3);

// Force the stock check to pass for combo items
add_filter('woocommerce_product_is_in_stock', function($in_stock, $product) {
    if ($product->is_type('grouped') && get_post_meta($product->get_id(), '_combo_price', true) !== '') {
        $children = $product->get_children();
        foreach ($children as $cid) {
            $child = wc_get_product($cid);
            if (!$child) continue;
            if ($child->is_type('variable')) {
                $vars = $child->get_children();
                $has_stock = false;
                foreach ($vars as $vid) {
                    $v = wc_get_product($vid);
                    if ($v && combo_child_is_available($v)) { $has_stock = true; break; }
                }
                if (!$has_stock) {
                    error_log('[COMBO DEBUG] is_in_stock returning false for ' . $product->get_id() . ' - child ' . $cid . ' no stock');
                    return false;
                }
            } else {
                if (!combo_child_is_available($child)) {
                    error_log('[COMBO DEBUG] is_in_stock returning false for ' . $product->get_id() . ' - child ' . $cid . ' no stock');
                    return false;
                }
            }
        }
        error_log('[COMBO DEBUG] is_in_stock filter returning true for ' . $product->get_id());
        return true;
    }
    return $in_stock;
}, 999, 2);

// Force has_enough_stock check
add_action('woocommerce_before_checkout_process', function() {
    error_log('[COMBO DEBUG] before_checkout_process');
    foreach (WC()->cart->get_cart() as $key => $item) {
        if (!empty($item['combo_children'])) {
            $p = $item['data'];
            error_log('[COMBO DEBUG] combo item: id=' . $p->get_id() . ' type=' . $p->get_type());
        }
    }
});