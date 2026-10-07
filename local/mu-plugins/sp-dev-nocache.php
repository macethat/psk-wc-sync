<?php
/**
 * Plugin Name: SP Dev - No cache (home-nuevo / online-sale-sp)
 * Description: Fuerza no-cache SOLO en las paginas de desarrollo (/home-nuevo/ y /online-sale-sp/) para ver cambios al instante mientras se construye el nuevo home. Quitar cuando se publique.
 */
if (!defined('ABSPATH')) exit;

add_action('template_redirect', function () {
    if (is_page('home-nuevo') || is_page('online-sale-sp')) {
        nocache_headers();
        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: 0');
    }
}, 1);

// Tambien evita que SiteGround marque la pagina como cacheable cuando estas paginas estan en $wp_query
add_filter('siteground_optimizer_is_cacheable', function ($cacheable) {
    if (is_page('home-nuevo') || is_page('online-sale-sp')) {
        return false;
    }
    return $cacheable;
});
