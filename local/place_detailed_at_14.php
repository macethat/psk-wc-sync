<?php
global $wpdb;

$data = $wpdb->get_var("SELECT meta_value FROM cid_postmeta WHERE post_id=18625 AND meta_key='_elementor_data'");
$elements = json_decode($data, true);

// Remove current seodet1
foreach ($elements as $i => $e) {
    if ($e['id'] === 'seodet1') {
        array_splice($elements, $i, 1);
        echo "Removed from index $i.\n";
        break;
    }
}

// Insert at index 14 (above panel 14)
$new = array(
    'id' => 'seodet1',
    'elType' => 'container',
    'settings' => array(
        'flex_direction' => 'column',
        'content_width' => 'full',
        'padding' => array('unit' => 'px', 'top' => 30, 'right' => 15, 'bottom' => 20, 'left' => 15, 'isLinked' => false),
        'padding_mobile' => array('unit' => 'px', 'top' => 25, 'right' => 25, 'bottom' => 15, 'left' => 25, 'isLinked' => false),
    ),
    'elements' => array(
        array(
            'id' => 'seodet2',
            'elType' => 'container',
            'settings' => array('flex_direction' => 'column', 'content_width' => 'full', 'width' => array('unit' => '%', 'size' => 100)),
            'elements' => array(
                array(
                    'id' => 'seodet3',
                    'elType' => 'widget',
                    'widgetType' => 'text-editor',
                    'settings' => array(
                        'editor' => '<h3 style="text-align: center; font-size: 22px; line-height: 1.5; font-weight: 500; margin: 0 0 8px 0; padding: 0; color: #000000;">Suplementos Deportivos en Panamá — Calidad y Resultados Reales</h3><p style="text-align: center; font-size: 16px; line-height: 1.6; margin: 0; padding: 0; color: #444444; font-family: \'Plus Jakarta Sans\', sans-serif;">En Suplementos Panamá encontrarás todo lo que necesitas para potenciar tu rendimiento: desde proteína whey y aislada para recuperación muscular, creatina monohidratada para fuerza y explosividad, hasta pre-entrenos con y sin estimulantes para maximizar cada sesión. También ofrecemos aminoácidos BCAA, glutamina, quemadores de grasa y multivitamínicos. Trabajamos con marcas como Optimum Nutrition, Evogen, Nutrex, Dymatize, Cellucor, VMS Nutrition y más. Haz tu pedido hoy y recibe en tu casa u oficina con envío gratis en compras mayores a $150. ¡Transforma tu físico con los mejores suplementos en Panamá!</p>',
                    ),
                ),
            ),
        ),
    ),
);

array_splice($elements, 14, 0, array($new));

$new_json = json_encode($elements, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
$escaped = $wpdb->_real_escape($new_json);
$wpdb->query("UPDATE cid_postmeta SET meta_value = '{$escaped}' WHERE post_id = 18625 AND meta_key = '_elementor_data'");

wp_cache_delete(18625, 'post_meta');
clean_post_cache(18625);
if (class_exists('\Elementor\Plugin')) {
    \Elementor\Plugin::$instance->files_manager->clear_cache();
}
echo "Done. Total: " . count($elements) . " containers.\n";
echo "S13: id={$elements[13]['id']}\n";
echo "S14: id={$elements[14]['id']} (detailed text)\n";
echo "S15: id={$elements[15]['id']}\n";
