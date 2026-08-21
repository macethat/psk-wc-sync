<?php
/**
 * Plugin Name: SP Bloquear auto-actualizaciones
 * Description: Desactiva las actualizaciones automaticas de plugins, core y traducciones en Suplementos Panama.
 * Author: SP
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

add_filter( 'auto_update_plugin', '__return_false', 10, 2 );
add_filter( 'auto_update_core', '__return_false', 10, 2 );
add_filter( 'auto_update_major', '__return_false', 10, 2 );
add_filter( 'auto_update_minor', '__return_false', 10, 2 );
add_filter( 'auto_update_translation', '__return_false', 10, 2 );
add_filter( 'allow_minor_auto_core_updates', '__return_false' );
add_filter( 'allow_major_auto_core_updates', '__return_false' );
add_filter( 'allow_dev_auto_core_updates', '__return_false' );
add_filter( 'auto_update_theme', '__return_false', 10, 2 );
