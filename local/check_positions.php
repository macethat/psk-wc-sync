<?php
global $wpdb;
$data = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id=18625 AND meta_key='_elementor_data'");
$elements = json_decode($data, true);

// Show S10 to S16 with widget preview
for ($i = 0; $i < count($elements) && $i < 25; $i++) {
    $e = $elements[$i];
    $widgets = array();
    foreach ($e['elements'] as $col) {
        foreach ($col['elements'] as $w) {
            $wt = $w['widgetType'] ?? $w['elType'] ?? '?';
            $txt = '';
            if ($wt === 'text-editor') {
                $txt = substr(strip_tags($w['settings']['editor'] ?? ''), 0, 60);
            } elseif ($wt === 'container') {
                $txt = 'NESTED';
            }
            $widgets[] = $wt . ($txt ? "($txt)" : '');
        }
    }
    echo "S{$i}: id={$e['id']} widgets=" . implode(',', $widgets) . "\n";
}
