<?php
/**
 * Plugin Name: SP i18n - textos en espanol
 * Description: Traduce al espanol textos sueltos que quedan en ingles (buscador del header, etc.).
 */
if (!defined('ABSPATH')) exit;

if (!function_exists('sp_i18n_map')) {
    function sp_i18n_map($text) {
        $t = str_replace(array('&hellip;', '&#8230;'), '…', $text);
        $t = trim($t);
        $map = array(
            'Search products…'              => 'Encuentra tu suplemento',
            'Search products...'            => 'Encuentra tu suplemento',
            'Search for products…'          => 'Encuentra tu suplemento',
            'Type any keyword to search...' => 'Encuentra tu suplemento',
            'Sign In / Register'            => 'Iniciar sesión / Registrarse',
            'Sign in'                       => 'Iniciar sesión',
            'Sign In'                       => 'Iniciar sesión',
            'Log in'                        => 'Iniciar sesión',
            'Login'                         => 'Iniciar sesión',
            'Register'                      => 'Registrarse',
            'Sign up'                       => 'Registrarse',
        );
        return isset($map[$t]) ? $map[$t] : null;
    }
}

add_filter('gettext', function ($translation, $text, $domain) {
    $m = sp_i18n_map($text);
    return $m !== null ? $m : $translation;
}, 10, 3);

add_filter('gettext_with_context', function ($translation, $text, $context, $domain) {
    $m = sp_i18n_map($text);
    return $m !== null ? $m : $translation;
}, 10, 4);
