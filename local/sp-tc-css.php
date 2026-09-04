<?php
/**
 * Plugin Name: SP CSS T&C
 * Description: Inyecta el CSS de la pagina Terminos y Condiciones (solo en esa pagina) para que no dependa del sanitizer de Elementor text-editor (que elimina etiquetas <style>).
 * Author: SP
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_action( 'wp_head', 'sp_tc_css_inject', 99 );
function sp_tc_css_inject() {
	if ( ! is_page( 'terminosycondiciones' ) ) {
		return;
	}
	echo '<style id="sp-tc-css">' . "\n";
	echo '.sp-tc-wrap{font-family:Arial,Helvetica,sans-serif;line-height:1.6;color:#222;max-width:800px;margin:0 auto}
.sp-tc-wrap h1{font-size:26px;color:#C0392B;border-bottom:2px solid #C0392B;padding-bottom:10px;margin:0 0 4px}
.sp-tc-wrap .fecha{color:#888;font-size:14px;margin:0 0 20px}
.sp-tc-wrap h2{font-size:20px;color:#151515;margin-top:34px;border-bottom:1px solid #eee;padding-bottom:6px}
.sp-tc-wrap h3{font-size:16px;color:#333;margin-top:22px}
.sp-tc-wrap table{width:100%;border-collapse:collapse;margin:16px 0;table-layout:auto}
.sp-tc-wrap table th,.sp-tc-wrap table td{border:1px solid #ddd;padding:10px 12px;text-align:left;font-size:14px;word-break:normal!important;overflow-wrap:normal!important;-webkit-hyphens:none;-moz-hyphens:none;hyphens:none;white-space:normal}
.sp-tc-wrap table th{background:#f5f5f5;font-weight:700}
.sp-tc-wrap table th{word-break:normal!important;overflow-wrap:normal!important;white-space:normal}
.sp-tc-wrap ul,.sp-tc-wrap ol{margin:10px 0 10px 20px;padding-left:0}
.sp-tc-wrap li{margin-bottom:6px;font-size:15px}
.sp-tc-wrap blockquote{border-left:4px solid #C0392B;padding:10px 16px;margin:12px 0;background:#fafafa;color:#555;font-style:italic}
.sp-tc-wrap hr{border:none;border-top:1px solid #ddd;margin:28px 0}
.sp-tc-wrap p{margin:10px 0;font-size:15px}
.sp-tc-wrap strong{color:#151515}
.sp-tc-wrap a{color:#C0392B;text-decoration:underline}
.sp-tc-wrap .indice{background:#f9f9f9;padding:16px 20px;border-radius:8px;margin:20px 0}
.sp-tc-wrap .indice a{color:#C0392B;text-decoration:none}
.sp-tc-wrap .indice a:hover{text-decoration:underline}
.sp-tc-wrap .indice ol{margin:6px 0 0 0;padding-left:22px}
.sp-tc-wrap .indice li{margin-bottom:4px;font-size:14px}
.sp-tc-wrap .footer{text-align:center;color:#888;font-size:13px;margin-top:44px;padding-top:16px;border-top:1px solid #ddd}
@media (max-width:767px){
  .sp-tc-wrap h1{font-size:22px}
  .sp-tc-wrap h2{font-size:18px}
  .sp-tc-wrap table{font-size:13px;display:block;overflow-x:auto}
  .sp-tc-wrap table th,.sp-tc-wrap table td{padding:8px;font-size:13px}
  .sp-tc-wrap .indice{padding:12px 16px}
}' . "\n";
	echo '</style>' . "\n";
}
