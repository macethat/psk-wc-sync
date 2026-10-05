<?php
// 1) T&C -> pagina normal (sin Elementor)
$html = file_get_contents('/tmp/tc_current.html');
if (strlen($html) < 1000) { echo "ERROR: HTML T&C vacio\n"; return; }
wp_update_post(array('ID' => 22030, 'post_content' => $html));
$el_metas = array('_elementor_edit_mode','_elementor_data','_elementor_element_cache','_elementor_css','_elementor_controls_usage','_elementor_page_assets','_elementor_version','_elementor_pro_version','_elementor_template_type','_elementor_page_settings');
foreach ($el_metas as $k) { delete_post_meta(22030, $k); }
echo "T&C (22030) convertido a pagina normal. post_content: " . strlen($html) . " bytes\n";
echo "  _elementor_edit_mode ahora: " . (get_post_meta(22030,'_elementor_edit_mode',true) ?: '(ninguno)') . "\n";

// 2) Envolver las 2 nuevas en .sp-tc-wrap
foreach (array(22259, 22260) as $id) {
    $c = get_post_field('post_content', $id);
    if (strpos($c, 'sp-tc-wrap') === false) {
        wp_update_post(array('ID' => $id, 'post_content' => '<div class="sp-tc-wrap">' . "\n" . $c . "\n" . '</div>'));
        echo "$id envuelto en sp-tc-wrap\n";
    } else {
        echo "$id ya tiene sp-tc-wrap\n";
    }
}
