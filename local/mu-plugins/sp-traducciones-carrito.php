<?php
/**
 * Plugin Name: SP - Traducciones carrito lateral
 * Description: Traduce al espanol los textos del panel lateral del carrito que el tema trae sin traducir (dominio "nutritix").
 */

defined('ABSPATH') || exit;

add_filter('gettext', function ($translated, $text, $domain) {
    if ('nutritix' !== $domain) {
        return $translated;
    }

    $map = array(
        'Shopping cart'            => 'CARRITO',
        'Shopping Cart'            => 'CARRITO',
        'close'                    => 'Cerrar',
        'Close'                    => 'Cerrar',
        'No products in the cart.' => 'Aún no hay productos en el carrito',
    );

    return isset($map[$text]) ? $map[$text] : $translated;
}, 20, 3);
