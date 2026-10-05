<?php
$post_id = 22030;

// backup
$raw = get_post_meta($post_id, '_elementor_data', true);
$backup = '/tmp/elementor_data_backup_22030_' . date('Ymd-His') . '.json';
file_put_contents($backup, $raw);
echo "backup: $backup (" . strlen($raw) . " bytes)\n";

$data = json_decode($raw, true);
if (!is_array($data)) { echo "ERROR: no se pudo parsear el JSON\n"; return; }

$nav = '<p class="sp-legal-nav"><strong>Documentos legales:</strong> '
     . '<a href="/terminosycondiciones/">Términos y Condiciones</a> &middot; '
     . '<a href="/politica-de-privacidad/">Política de Privacidad</a> &middot; '
     . '<a href="/politica-de-devoluciones/">Política de Devoluciones</a></p>';

$changed = false;

function sp_modify_editor(&$node, $nav, &$changed) {
    if (!is_array($node)) return;
    if (isset($node['widgetType']) && $node['widgetType'] === 'text-editor' && isset($node['settings']['editor'])) {
        $html = $node['settings']['editor'];

        // 1) nav después del "Última actualización"
        $html = preg_replace('#(<p class="fecha">.*?</p>)#s', '$1' . "\n" . $nav, $html, 1);

        // 2) sección 7 -> link a Política de Devoluciones
        $html = preg_replace(
            '#<h2 id="s7">.*?(?=<h2 id="s8">)#s',
            "<h2 id=\"s7\">7. Política de devolución y reembolsos</h2>\n<p>Las devoluciones y reembolsos se rigen por nuestra <a href=\"/politica-de-devoluciones/\"><strong>Política de Devoluciones y Reembolsos</strong></a>: allí encontrarás los motivos que aplican, el plazo de 24 horas para reportar, el procedimiento y las opciones de resolución (reemplazo o crédito en tienda).</p>\n\n",
            $html, 1
        );

        // 3) sección 10 -> link a Política de Privacidad
        $html = preg_replace(
            '#<h2 id="s10">.*?(?=<h2 id="s11">)#s',
            "<h2 id=\"s10\">10. Protección de datos</h2>\n<p>El tratamiento de tus datos personales se rige por nuestra <a href=\"/politica-de-privacidad/\"><strong>Política de Privacidad</strong></a>, conforme a la Ley 81 de 2019 de Protección de Datos Personales de la República de Panamá.</p>\n\n",
            $html, 1
        );

        // 4) WhatsApp viejo -> nuevo
        $html = str_replace('+507 6811-1649', '+507 6010-0948', $html);

        if ($html !== $node['settings']['editor']) {
            $node['settings']['editor'] = $html;
            $changed = true;
        }
        return;
    }
    foreach ($node as &$v) { sp_modify_editor($v, $nav, $changed); }
    unset($v);
}

sp_modify_editor($data, $nav, $changed);

if (!$changed) { echo "Sin cambios (¿no se encontró el editor?)\n"; return; }

$new_json = wp_json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
update_metadata('post', $post_id, '_elementor_data', wp_slash($new_json));
delete_post_meta($post_id, '_elementor_css');
echo "actualizado. nuevo tamaño: " . strlen($new_json) . " bytes\n";

// verificación rápida
$check = get_post_meta($post_id, '_elementor_data', true);
echo "contiene /politica-de-devoluciones/: " . (strpos($check, '/politica-de-devoluciones/') !== false ? 'SI' : 'NO') . "\n";
echo "contiene /politica-de-privacidad/: " . (strpos($check, '/politica-de-privacidad/') !== false ? 'SI' : 'NO') . "\n";
echo "contiene 6010-0948: " . (strpos($check, '6010-0948') !== false ? 'SI' : 'NO') . "\n";
echo "JSON valido: " . (json_decode($new_json) !== null ? 'SI' : 'NO') . "\n";
