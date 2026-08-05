<?php
global $wpdb;
$data = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id=18625 AND meta_key='_elementor_data'");
$elements = json_decode($data, true);

// Search ALL sections for "suscrib" or "newsletter" or form
foreach ($elements as $idx => $sec) {
    $json = json_encode($sec);
    if (stripos($json, 'suscrib') !== false || stripos($json, 'newsletter') !== false || stripos($json, 'shortcode') !== false || stripos($json, 'form') !== false) {
        echo "S{$idx} (id={$sec['id']}): FOUND\n";
    }
}

// Show S13 structure
$s13 = $elements[13];
echo "\nS13 (id={$s13['id']}) structure:\n";
echo json_encode(array_keys($s13)) . "\n";
echo "elements count: " . count($s13['elements'] ?? []) . "\n";
$col0 = $s13['elements'][0] ?? [];
echo "col0 elements count: " . count($col0['elements'] ?? []) . "\n";
// Show first nested widget
$first = $col0['elements'][0] ?? [];
echo "first nested: " . json_encode(array_keys($first)) . "\n";
echo "first nested type: " . ($first['widgetType'] ?? $first['elType'] ?? '?') . "\n";
