<?php
global $wpdb;

$data = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id=18625 AND meta_key='_elementor_data'");
$elements = json_decode($data, true);

// Find detailed text (has text-editor with "Suplementos Deportivos")
$detailed_idx = -1;
foreach ($elements as $i => $e) {
    foreach ($e['elements'] as $col) {
        foreach ($col['elements'] as $w) {
            if (($w['widgetType'] ?? '') === 'text-editor' && strpos($w['settings']['editor'] ?? '', 'Suplementos Deportivos') !== false) {
                $detailed_idx = $i;
                break 3;
            }
        }
    }
}
if ($detailed_idx < 0) { echo "Detailed text not found.\n"; exit; }

echo "Detailed text found at S{$detailed_idx} (id={$elements[$detailed_idx]['id']})\n";
echo "Target: before S13 (index 12, id={$elements[12]['id']})\n";

// Remove from current position, then insert at index 12
$item = array_splice($elements, $detailed_idx, 1)[0];

// If removed index < 12, adjust target
$target = 12;
if ($detailed_idx < $target) $target--;

array_splice($elements, $target, 0, array($item));

$new_json = json_encode($elements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$escaped = $wpdb->_real_escape($new_json);
$wpdb->query("UPDATE cid_postmeta SET meta_value = '{$escaped}' WHERE post_id = 18625 AND meta_key = '_elementor_data'");

wp_cache_delete(18625, 'post_meta');
clean_post_cache(18625);
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
}
echo "Swapped. Now S14: id={$elements[14]['id']}, S15: id={$elements[15]['id']}\n";
