<?php
/**
 * Plugin Name: SP Checkbox Términos y Condiciones en fichas
 * Description: Exige marcar un recuadro de verificación que confirma haber leído los Términos y Condiciones antes de agregar al carrito, en todas las fichas de producto (simple, variable y combos grouped). El texto enlaza a /terminosycondiciones/.
 * Author: SP
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'SP_TC_AGREE_FIELD', 'sp_accept_tc' );
define( 'SP_TC_PAGE_URL', home_url( '/terminosycondiciones/' ) );

/**
 * 1) Render: checkbox debajo del botón "Añadir al carrito".
 * Hook común a simple/variable/grouped.
 */
add_action( 'woocommerce_after_add_to_cart_button', 'sp_tc_agree_render', 5 );
function sp_tc_agree_render() {
	global $product;
	if ( ! $product || ! is_object( $product ) ) {
		return;
	}
	$pid = $product->get_id();
	$tc_url = esc_url( SP_TC_PAGE_URL );
	$id = SP_TC_AGREE_FIELD . '_' . $pid;
	?>
	<div class="sp-tc-agree" data-product-id="<?php echo esc_attr( $pid ); ?>" style="margin:8px 0 2px;padding:9px 12px;background:#f7f7f7;border:1px solid #eee;border-radius:6px;line-height:1.5;">
		<label for="<?php echo esc_attr( $id ); ?>" style="display:flex;align-items:flex-start;gap:8px;font-size:13px;color:#444;cursor:pointer;font-weight:400;margin:0;">
			<input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( SP_TC_AGREE_FIELD ); ?>" value="1" style="margin-top:2px;min-width:15px;min-height:15px;cursor:pointer;" aria-required="true" />
			<span>He leído y acepto los <a href="<?php echo $tc_url; ?>" target="_blank" rel="noopener" style="color:#C0392B;text-decoration:underline;">Términos y Condiciones</a> de Suplementos Panamá.</span>
		</label>
		<div class="sp-tc-agree-msg" role="alert" aria-live="polite" style="display:none;margin-top:8px;padding:8px 10px;background:#fdecea;border:1px solid #f5c6cb;border-radius:4px;color:#c0392b;font-size:13px;line-height:1.4;"></div>
	</div>
	<?php
}

/**
 * 2) JS: bloquear el envío del form si el checkbox no está marcado.
 * NO manipula .disabled del botón (para no interferir con la lógica del theme
 * en combos que deshabilita por variaciones sin elegir). Intercepta submit/click.
 */
add_action( 'wp_footer', 'sp_tc_agree_js', 99 );
function sp_tc_agree_js() {
	if ( ! is_product() ) {
		return;
	}
	?>
	<script>
	jQuery(function($){
		$('form.cart, form.grouped_form').each(function(){
			var $form = $(this);
			var $check = $form.find('input[name="<?php echo esc_js( SP_TC_AGREE_FIELD ); ?>"]');
			if (!$check.length) return;
			var $box = $check.closest('.sp-tc-agree');
			var $msg = $box.find('.sp-tc-agree-msg');

			var msg = 'Debes marcar la casilla de Términos y Condiciones para continuar.';

			function hideMsg(){
				if ($msg.length) { $msg.hide().html(''); }
			}
			function showMsg(){
				if (!$msg.length) return;
				$msg.html(msg).show();
				try {
					$msg[0].scrollIntoView({ behavior: 'smooth', block: 'center' });
				} catch(e) {
					$('html, body').animate({ scrollTop: $box.offset().top - 120 }, 400);
				}
				$check.first().trigger('focus');
			}
			function isChecked(){
				var ok = false;
				$check.each(function(){ if ($(this).prop('checked')) ok = true; });
				return ok;
			}

			// Al marcar/desmarcar se oculta o muestra el aviso en tiempo real
			$check.on('change', function(){
				if (isChecked()) hideMsg();
			});

			// Bloquea el submit (cubre Enter y envíos programáticos)
			$form.on('submit', function(e){
				if (!isChecked()) {
					e.preventDefault();
					showMsg();
					return false;
				}
				hideMsg();
			});

			// Refuerzo: click en el botón sin haber marcado
			var $btn = $form.find('button.single_add_to_cart_button');
			$btn.on('click', function(e){
				if (!isChecked()) {
					e.preventDefault();
					showMsg();
					return false;
				}
			});
		});
	});
	</script>
	<?php
}

/**
 * 3) Validación server-side: exigir el checkbox en el add-to-cart.
 * Prioridad 1 -> corre antes que la validación de combos (prioridad 5) que
 * hace exit en AJAX, para que el mensaje de error salga correctamente.
 */
add_filter( 'woocommerce_add_to_cart_validation', 'sp_tc_agree_validate', 1, 3 );
function sp_tc_agree_validate( $passed, $product_id, $qty ) {
	if ( empty( $_POST[ SP_TC_AGREE_FIELD ] ) ) {
		sp_tc_agree_reject();
		return false;
	}
	return $passed;
}

/**
 * 3b) Los combos grouped con _combo_price usan un handler CUSTOM ('combo')
 * (combo-price.php, prioridad 10) que se ejecuta SIN pasar por
 * woocommerce_add_to_cart_validation. Para bloquearlos con mensaje visible,
 * interceptamos el filtro woocommerce_add_to_cart_handler con prioridad 20
 * (después de combo-price que usa prioridad 10): si el usuario no marcó el
 * checkbox, NO activamos el handler 'combo' y dejamos que WooCommerce use el
 * flujo 'grouped' estándar, que sí pasa por la validación (nuestro filtro
 * prioridad 1) y muestra el error.
 */
add_filter( 'woocommerce_add_to_cart_handler', 'sp_tc_agree_force_grouped_handler', 20, 2 );
function sp_tc_agree_force_grouped_handler( $handler, $product ) {
	if ( empty( $_POST[ SP_TC_AGREE_FIELD ] ) && isset( $_REQUEST['add-to-cart'] ) && $product && $product->is_type( 'grouped' ) ) {
		$combo_price = get_post_meta( $product->get_id(), '_combo_price', true );
		if ( $combo_price !== '' && $combo_price !== false ) {
			// Dejar que siga como grouped -> pasará por validación y mostrará el error.
			return $product->get_type();
		}
	}
	return $handler;
}

/**
 * Helper: agregar el notice de error común.
 */
function sp_tc_agree_reject() {
	wc_add_notice(
		__( 'Debes aceptar los <a href="' . esc_url( SP_TC_PAGE_URL ) . '" target="_blank" rel="noopener">Términos y Condiciones</a> para agregar este producto al carrito.', 'nutritix' ),
		'error'
	);
}
