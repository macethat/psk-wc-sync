<?php
/**
 * Plugin Name: SP - Redirect /black-weekend/ -> /online-sale-sp/
 * Description: 301 de la pagina Black Weekend renombrada a Online Sale SP.
 */
defined('ABSPATH') || exit;

add_action('template_redirect', function () {
    $path = trim((string) parse_url($_SERVER['REQUEST_URI'] ?? '', PHP_URL_PATH), '/');
    if ($path === 'black-weekend') {
        wp_redirect(home_url('/online-sale-sp/'), 301);
        exit;
    }
}, 5);
