<?php
global $wpdb;
$data = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id=18625 AND meta_key='_elementor_data'");
$elements = json_decode($data, true);

// Check all sections from our detailed to the end for subscription content
for ($i = 13; $i < count($elements); $i++) {
    echo "=== S{$i} (id={$elements[$i]['id']}) ===\n";
    foreach ($elements[$i]['elements'] as $j => $col) {
        foreach ($col['elements'] as $k => $w) {
            $wt = $w['widgetType'] ?? $w['elType'] ?? '?';
            $txt = '';
            if ($wt === 'text-editor') {
                $editor = $w['settings']['editor'] ?? '';
                $txt = substr(strip_tags($editor), 0, 80);
            } elseif ($wt === 'social-icons') {
                $txt = 'SOCIAL ICONS';
            } elseif ($wt === 'image') {
                $txt = 'IMAGE';
            }
            echo "  Col{$j} W{$k}: {$wt} -> {$txt}\n";
        }
    }
}
