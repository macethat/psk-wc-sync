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
.site-header-cart.header-cart-mobile .cart-contents:before{font-size:24px !important;}
.site-header-cart.header-cart-mobile .count{left:14px;bottom:-8px;}

/* Movil: evitar que el icono mas grande rompa la fila del header (Elementor usa --flex-wrap-mobile:wrap) */
@media (max-width:1023px){
  .elementor-20649 .elementor-element.elementor-element-881cea9{--flex-wrap-mobile:nowrap !important;flex-wrap:nowrap !important;--row-gap:0px;--column-gap:10px;}
  .elementor-20649 .elementor-element.elementor-element-881cea9 > .e-con-inner{flex-wrap:nowrap !important;}
  .header-group-action{flex-wrap:nowrap !important;align-items:center !important;}
  .header-group-action > div{flex:0 0 auto;}
}
</style>';
}, 100);
