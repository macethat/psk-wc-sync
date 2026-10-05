<?php
$pages = array(
    'politica-de-privacidad' => array(
        'title'     => 'Política de Privacidad',
        'file'      => '/tmp/politica-privacidad.html',
        'meta_title'=> 'Política de Privacidad | Suplementos Panamá',
        'meta_desc' => 'Conoce cómo Suplementos Panamá recopila, usa y protege tus datos personales, conforme a la Ley 81 de 2019 de Panamá.',
    ),
    'politica-de-devoluciones' => array(
        'title'     => 'Política de Devoluciones',
        'file'      => '/tmp/politica-devoluciones.html',
        'meta_title'=> 'Política de Devoluciones y Reembolsos | Suplementos Panamá',
        'meta_desc' => 'Cómo solicitar una devolución, plazos, motivos que aplican y cómo gestionamos reemplazos, créditos y reembolsos.',
    ),
);

foreach ($pages as $slug => $info) {
    if (!file_exists($info['file'])) { echo "FALTA archivo {$info['file']}\n"; continue; }
    $content = file_get_contents($info['file']);
    $existing = get_page_by_path($slug);
    if ($existing) {
        wp_update_post(array('ID' => $existing->ID, 'post_content' => $content, 'post_status' => 'publish'));
        $id = $existing->ID;
        echo "UPDATE $slug -> $id\n";
    } else {
        $id = wp_insert_post(array(
            'post_title'     => $info['title'],
            'post_name'      => $slug,
            'post_content'   => $content,
            'post_status'    => 'publish',
            'post_type'      => 'page',
            'comment_status' => 'closed',
            'ping_status'    => 'closed',
        ), true);
        if (is_wp_error($id)) { echo "ERROR $slug: " . $id->get_error_message() . "\n"; continue; }
        echo "CREATE $slug -> $id\n";
    }
    update_post_meta($id, 'rank_math_title', $info['meta_title']);
    update_post_meta($id, 'rank_math_description', $info['meta_desc']);
    update_post_meta($id, 'rank_math_robots', array('index', 'follow'));
    echo '  permalink: ' . get_permalink($id) . "\n";
}

$priv = get_page_by_path('politica-de-privacidad');
if ($priv) {
    update_option('wp_page_for_privacy_policy', $priv->ID);
    echo "wp_page_for_privacy_policy = {$priv->ID}\n";
}
// rank math privacy page (opcional)
update_option('rank_math_privacy_policy', $priv ? $priv->ID : 0);
