<?php
global $wpdb;

$data = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id=18625 AND meta_key='_elementor_data'");
$elements = json_decode($data, true);

// Convert non-hex IDs to proper 6-char hex
function fixIds(&$el) {
    if (isset($el['id'])) {
        if (!ctype_xdigit($el['id']) || strlen($el['id']) !== 6) {
            $old = $el['id'];
            $el['id'] = substr(bin2hex(random_bytes(3)), 0, 6);
            echo "  Changed id: {$old} -> {$el['id']}\n";
        }
    }
    if (isset($el['elements'])) {
        foreach ($el['elements'] as &$child) {
            fixIds($child);
        }
    }
}

foreach ($elements as &$container) {
    fixIds($container);
}

$new_json = json_encode($elements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$escaped = $wpdb->_real_escape($new_json);
$wpdb->query("UPDATE cid_postmeta SET meta_value = '{$escaped}' WHERE post_id = 18625 AND meta_key = '_elementor_data'");

wp_cache_delete(18625, 'post_meta');
clean_post_cache(18625);
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
}
echo "Fixed.\n";
