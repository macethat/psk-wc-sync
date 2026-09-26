<?php
/**
 * Plugin Name: SP - Evitar sesion PHP de PixelYourSite (arregla cache SG)
 * Description: Fuerza pys_skip_session=true para que PYS no inicie sesion PHP (PHPSESSID) en el frontend y SiteGround Optimizer pueda servir el cache.
 * Version: 1.0
 */

defined('ABSPATH') || exit;

add_filter('pys_skip_session', '__return_true');
