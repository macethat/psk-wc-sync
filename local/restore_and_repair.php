<?php
global $wpdb;

// The ed_full.json was saved from the original wp post meta get
// Try to repair it
$file = '/home/u1910-kbd9lgn9dh44/ed_full.json';
$raw = file_get_contents($file);
echo 'File length: ' . strlen($raw) . "\n";

// Check for non-JSON content at the start or end
// wp post meta get might have added a line like "Success: ..." or similar
$first_line = strtok($raw, "\n");
$json_start = strpos($raw, '[');
if ($json_start > 0) {
    echo "Found non-JSON prefix: '" . substr($raw, 0, $json_start) . "'\n";
    $raw = substr($raw, $json_start);
}

// Remove trailing whitespace/newlines
$raw = rtrim($raw);

// Try parsing
$elements = json_decode($raw, true);
if ($elements === null) {
    echo "Parse failed: " . json_last_error_msg() . "\n";
    echo "First 200: " . substr($raw, 0, 200) . "\n";
    echo "Last 200: " . substr($raw, -200) . "\n";
    exit;
}

echo 'Valid JSON. Containers: ' . count($elements) . "\n";
for ($i = 0; $i < min(5, count($elements)); $i++) {
    echo "S{$i}: id={$elements[$i]['id']} type={$elements[$i]['elType']}\n";
}

// Insert our container at position 3
$new = array(
    'id' => 'seoin1',
    'elType' => 'container',
    'settings' => array(
        'flex_direction' => 'column',
        'content_width' => 'full',
        'padding' => array('unit' => 'px', 'top' => 15, 'right' => 15, 'bottom' => 5, 'left' => 15, 'isLinked' => false),
        'padding_mobile' => array('unit' => 'px', 'top' => 10, 'right' => 15, 'bottom' => 5, 'left' => 15, 'isLinked' => false),
    ),
    'elements' => array(
        array(
            'id' => 'seoin2',
            'elType' => 'container',
            'settings' => array('flex_direction' => 'column', 'content_width' => 'full', 'width' => array('unit' => '%', 'size' => 100)),
            'elements' => array(
                array(
                    'id' => 'seoin3',
                    'elType' => 'widget',
                    'widgetType' => 'text-editor',
                    'settings' => array(
                        'editor' => '<p style="text-align: center; font-size: 1.15em; line-height: 1.7;"><strong>¿Lo quieres?</strong> Proteína, creatina, aminoácidos, pre-entrenos... <strong>Lo tienes</strong> en Suplementos Panamá. Las mejores marcas, los mejores precios y envíos a todo el país. <strong>Lleva tu rendimiento al siguiente nivel. ¡Pídelo ahora!</strong></p>',
                    ),
                ),
            ),
        ),
    ),
);

array_splice($elements, 3, 0, array($new));
$new_json = json_encode($elements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);

// Save directly to DB
$result = $wpdb->update('cid_postmeta', array('meta_value' => $new_json), array('post_id' => 18625, 'meta_key' => '_elementor_data'));
if ($result !== false) {
    echo "Saved " . strlen($new_json) . " chars to DB.\n";
    wp_cache_delete(18625, 'post_meta');
    clean_post_cache(18625);
    // Also clear Elementor caches
    if (class_exists('\Elementor\Plugin')) {
        \Elementor\Plugin::$instance->files_manager->clear_cache();
        echo "Elementor file cache cleared.\n";
    }
    echo "Done.\n";
} else {
    echo "DB update failed.\n";
}
