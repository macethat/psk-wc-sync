<?php
if (!function_exists('wc')) { wp_load(); }
if (!wc()->session) { wc()->session = new WC_Session_Handler(); wc()->session->init(); }
if (!wc()->cart)   { wc()->cart   = new WC_Cart();   wc()->cart->init(); }
wc()->cart->empty_cart();

$product_id = 21525;
$product = wc_get_product($product_id);
$combo_items = array();
foreach ($product->get_children() as $child_id) {
    $child = wc_get_product($child_id);
    if (!$child) continue;
    $combo_items[] = array('product_id' => $child_id, 'variation_id' => 0, 'name' => $child->get_name(), 'sku' => $child->get_sku());
}
$cart_item_data = array('combo_children' => $combo_items);
$cart_id = wc()->cart->generate_cart_id($product_id, 0, array(), $cart_item_data);
wc()->cart->cart_contents[$cart_id] = apply_filters('woocommerce_add_cart_item', array(
    'key'            => $cart_id,
    'product_id'     => $product_id,
    'variation_id'   => 0,
    'variation'      => array(),
    'quantity'       => 1,
    'data'           => $product,
    'data_hash'      => wc_get_cart_item_data_hash($product),
    'combo_children' => $combo_items,
), $cart_id);
wc()->session->set('cart', wc()->cart->get_cart_for_session());

echo "== valid sucursales ==";
$valid = sp_get_valid_sucursales_for_cart();
echo " (".count($valid)."): ".implode(',', array_keys($valid))."\n";

echo "== simular session elegida: sucursal 1 ==";
wc()->session->set('sp_sucursal_retiro', '1');
wc()->session->set('chosen_shipping_methods', array('local_pickup:1'));
$html = sp_get_sucursal_review_html();
echo "review_html: " . ($html === '' ? "(VACIO)" : $html) . "\n";

echo "== simular POST checkout (como en update_order_review) ==";
$_POST['sp_sucursal_retiro'] = '1';
$_POST['shipping_method'] = array('local_pickup:1');
$html2 = sp_get_sucursal_review_html();
echo "review_html_post: " . ($html2 === '' ? "(VACIO)" : $html2) . "\n";

echo "== checkout field render ==";
ob_start();
sp_checkout_sucursal_field(new WC_Checkout());
$field = ob_get_clean();
echo "field_len=".strlen($field)."\n";
echo (strpos($field, 'sp-sucursal-wrap') !== false ? "  wrap presente\n" : "  wrap AUSENTE\n");
echo (strpos($field, 'selected="selected"') !== false || strpos($field, "selected='selected'") !== false ? "  preseleccionada OK\n" : "  NO preseleccionada\n");
echo $field . "\n";
