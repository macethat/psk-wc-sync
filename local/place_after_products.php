<?php
global $wpdb;

$data = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id=18625 AND meta_key='_elementor_data'");
$elements = json_decode($data, true);

// Find detailed text
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
if ($detailed_idx < 0) { echo "NOT FOUND.\n"; exit; }
echo "Detailed at S{$detailed_idx}\n";

// Remove it
$item = array_splice($elements, $detailed_idx, 1)[0];

// Find product list section - S6 has nested-carousel (product grid)
// Insert right after S6 (before the first empty spacer)
$target = 7; // after S6
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
echo "Moved detailed text to index {$target}. Total: " . count($elements) . "\n";
echo "New S6: id={$elements[6]['id']}\n";
echo "New S7: id={$elements[7]['id']}\n";
