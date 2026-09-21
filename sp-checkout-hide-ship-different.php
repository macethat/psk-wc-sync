<?php
/**
 * Plugin Name: SP Checkout - Ocultar "Enviar a una direccion diferente"
 * Description: Oculta el checkbox de envio a otra direccion y su formulario en el checkout (los clientes se confunden con el segundo formulario).
 * Version: 1.0
 */

defined('ABSPATH') || exit;

add_action('wp_head', 'sp_hide_ship_different_css', 99);
function sp_hide_ship_different_css() {
    if (!function_exists('is_checkout') || !is_checkout()) {
        return;
    }
    echo '<style id="sp-hide-ship-different">'
        . '#ship-to-different-address{display:none !important;}'
        . '.woocommerce-shipping-fields__field-wrapper{display:none !important;}'
        . '</style>';
}
