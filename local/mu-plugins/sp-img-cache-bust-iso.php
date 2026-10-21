<?php
/**
 * Plugin Name: SP Cache-bust imágenes ISO Surge
 * Description: Añade ?ver= a las imágenes de ISO Surge (22304/22305) para forzar recarga del navegador tras reemplazar archivos con la misma URL.
 * Version: 1.0.0
 */
if (!defined('ABSPATH')) { exit; }

add_filter('wp_get_attachment_image_src', function ($image, $attachment_id) {
    if (in_array((int) $attachment_id, array(22304, 22305), true) && !empty($image[0])) {
        $image[0] = add_query_arg('ver', '20261021', $image[0]);
    }
    return $image;
}, 10, 2);

add_filter('wp_calculate_image_srcset', function ($sources, $size_array, $image_src, $image_meta, $attachment_id) {
    if (in_array((int) $attachment_id, array(22304, 22305), true) && is_array($sources)) {
        foreach ($sources as $k => $s) {
            if (!empty($s['url'])) {
                $sources[$k]['url'] = add_query_arg('ver', '20261021', $s['url']);
            }
        }
    }
    return $sources;
}, 10, 5);

add_filter('wp_get_attachment_image_url', function ($url, $attachment_id) {
    if (in_array((int) $attachment_id, array(22304, 22305), true) && $url) {
        return add_query_arg('ver', '20261021', $url);
    }
    return $url;
}, 10, 2);
