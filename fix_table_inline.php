<?php
$post = get_post(21698);
$c = $post->post_content;
// Fix the table style directly
$c = str_replace(
    '<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;table-layout: auto;word-break: normal;overflow-wrap: normal">',
    '<table style="width: 100%;border-collapse: collapse;font-size: 14px;border: 1px solid #ddd">',
    $c
);
// Add overflow-wrap to all td/th
$c = str_replace(
    'border-bottom: 2px solid #ddd">',
    'border-bottom: 2px solid #ddd;overflow-wrap:break-word">',
    $c
);
$c = str_replace(
    'border-bottom: 1px solid #eee">',
    'border-bottom: 1px solid #eee;overflow-wrap:break-word">',
    $c
);
$c = str_replace(
    'padding: 10px 15px"><strong>Disponibilidad',
    'padding: 10px 15px;overflow-wrap:break-word"><strong>Disponibilidad',
    $c
);
$c = str_replace(
    'padding: 10px 15px">Amplia oferta en Suplementos Panam',
    'padding: 10px 15px;overflow-wrap:break-word">Amplia oferta en Suplementos Panam',
    $c
);
$c = str_replace(
    'padding: 10px 15px">Oferta creciente (Impulse, LanderFit)',
    'padding: 10px 15px;overflow-wrap:break-word">Oferta creciente (Impulse, LanderFit)',
    $c
);
wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
