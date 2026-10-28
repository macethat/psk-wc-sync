<?php
/**
 * Plugin Name: SP - Header icons (carrito + favoritos)
 * Description: Cambia el icono del carrito (bolsa -> carrito) y agranda los iconos de carrito y favoritos en el header, desktop y movil.
 */

defined('ABSPATH') || exit;

add_action('wp_head', function () {
    echo '<style id="sp-header-icons-css">
/* Carrito: glifo de carrito (e06a) en lugar de la bolsa (e00d) */
.site-header-cart .cart-contents::before{content:"\e06a" !important;}

/* Desktop */
.header-group-action .site-header-cart .cart-contents:before{font-size:28px !important;}
.header-group-action .site-header-wishlist a i{font-size:26px !important;}

/* Contadores reubicados por el mayor tamano */
.header-group-action .site-header-cart .count{left:14px;bottom:-7px;}
.header-group-action .site-header-wishlist .count{left:14px;bottom:-6px;}

/* Movil */
.site-header-cart.header-cart-mobile .cart-contents:before{font-size:26px !important;}
.site-header-cart.header-cart-mobile .count{left:14px;bottom:-8px;}
</style>';
}, 100);
