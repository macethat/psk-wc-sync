<?php
/**
 * Plugin Name: SP Related mobile 1 columna
 * Description: En movil (<=767px) la seccion de productos relacionados pasa a una sola columna al 100% de ancho, con la miniatura completa y el texto debajo. Sobrescribe el CSS del child (que dejaba 2 columnas/50%) sin tocar functions.php.
 * Author: SP
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'sp_related_movil_css', 99 );
function sp_related_movil_css() {
	if ( ! is_product() && ! is_shop() && ! is_product_category() && ! is_product_tag() ) {
		return;
	}
	?>
	<style id="sp-related-movil-css">
	@media (max-width: 767px) {
		.related.products ul.products li.product,
		.upsells.products ul.products li.product {
			width: 100% !important;
			max-width: 100% !important;
			flex: 0 0 100% !important;
			margin-left: 0 !important;
			margin-right: 0 !important;
		}
		.related.products ul.products li.product .product-block,
		.upsells.products ul.products li.product .product-block {
			flex-direction: column !important;
			align-items: stretch !important;
		}
		.related.products ul.products li.product .product-transition,
		.upsells.products ul.products li.product .product-transition {
			width: 100% !important;
			max-width: 100% !important;
			order: 1;
		}
		.related.products ul.products li.product .product-caption,
		.upsells.products ul.products li.product .product-caption {
			order: 2;
			width: 100% !important;
			max-width: 100% !important;
			display: block !important;
			padding: 0 !important;
		}
		.related.products ul.products li.product .price,
		.upsells.products ul.products li.product .price {
			order: 3;
			width: 100% !important;
			max-width: 100% !important;
			text-align: left !important;
			margin: 0 !important;
			padding: 0 !important;
		}
		.related.products ul.products li.product .woocommerce-loop-product__title,
		.upsells.products ul.products li.product .woocommerce-loop-product__title {
			font-size: 14px !important;
			margin: 6px 0 2px !important;
		}
	}
	</style>
	<?php
}
