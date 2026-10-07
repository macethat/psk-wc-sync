<?php
/**
 * Plugin Name: SP - Footer form (MC4WP) responsive
 * Description: En movil, hace full-width y centra los campos del formulario de suscripcion del footer (widget Elementor shortcode width-auto).
 */

defined('ABSPATH') || exit;

add_action('wp_head', function () {
    echo '<style id="sp-footer-form-css">
@media (max-width:767px){
  .elementor-element-nwsltrf1{width:100% !important;max-width:100% !important;}
  .elementor-widget-shortcode:has(.mc4wp-form){width:100% !important;max-width:100% !important;}
  .elementor-element-nwsltrf1 .mc4wp-form,
  .elementor-element-nwsltrf1 .mc4wp-form-fields{width:100% !important;max-width:100% !important;display:block !important;}
  .elementor-element-nwsltrf1 .mc4wp-form-fields input[type="text"],
  .elementor-element-nwsltrf1 .mc4wp-form-fields input[type="email"],
  .elementor-element-nwsltrf1 .mc4wp-form-fields input[type="tel"],
  .elementor-element-nwsltrf1 .mc4wp-form-fields input[type="submit"]{
    width:100% !important;max-width:100% !important;box-sizing:border-box !important;display:block !important;margin:0 auto 10px !important;text-align:center !important;
  }
}
</style>';
}, 100);
