<?php
$post_id = 414;
$raw = get_post_meta($post_id, '_elementor_data', true);
$backup = '/tmp/footer414_backup_' . date('Ymd-His') . '.json';
file_put_contents($backup, $raw);
echo "backup: $backup (" . strlen($raw) . " bytes)\n";

$data = json_decode($raw, true);
if (!is_array($data)) { echo "ERROR parse JSON\n"; return; }

$link = function ($url) { return array('url' => $url, 'is_external' => '', 'nofollow' => '', 'custom_attributes' => ''); };
$new_list = array(
    array('text' => 'SUCURSALES',              'selected_icon' => array('value' => '', 'library' => ''), '_id' => 'eb4eaf2', 'link' => $link('/sucursales/')),
    array('text' => 'POWER RACK',              'selected_icon' => array('value' => '', 'library' => ''), '_id' => '16824da', 'link' => $link('/blog/')),
    array('text' => 'TÉRMINOS / CONDICIONES',  'selected_icon' => array('value' => '', 'library' => ''), '_id' => '193ce65', 'link' => $link('/terminosycondiciones/')),
    array('text' => 'POLÍTICA DE PRIVACIDAD',  'selected_icon' => array('value' => '', 'library' => ''), '_id' => 'f3a9c21', 'link' => $link('/politica-de-privacidad/')),
    array('text' => 'POLÍTICA DE DEVOLUCIONES','selected_icon' => array('value' => '', 'library' => ''), '_id' => 'b7d4e08', 'link' => $link('/politica-de-devoluciones/')),
);

$changed = false;
function sp_set_list(&$node, $new_list, &$changed) {
    if (!is_array($node)) return;
    if (isset($node['id']) && $node['id'] === '43b777' && isset($node['settings']['icon_list'])) {
        $node['settings']['icon_list'] = $new_list;
        $changed = true;
        return;
    }
    foreach ($node as &$v) { sp_set_list($v, $new_list, $changed); }
    unset($v);
}
sp_set_list($data, $new_list, $changed);

if (!$changed) { echo "ERROR: no se encontró el widget 43b777\n"; return; }

$new_json = wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
update_metadata('post', $post_id, '_elementor_data', wp_slash($new_json));
// limpiar caches de Elementor
foreach (array('_elementor_element_cache','_elementor_css','_elementor_page_assets') as $k) { delete_post_meta($post_id, $k); }
echo "actualizado ($post_id). nuevo tamaño: " . strlen($new_json) . " bytes\n";

$check = get_post_meta($post_id, '_elementor_data', true);
foreach (array('SHOPPING','SOPORTE','POLÍTICA DE PRIVACIDAD','POLÍTICA DE DEVOLUCIONES') as $t) {
    echo "  " . $t . ": " . (strpos($check, $t) !== false ? 'SI' : 'NO') . "\n";
}
echo "JSON valido: " . (json_decode($new_json) !== null ? 'SI' : 'NO') . "\n";
