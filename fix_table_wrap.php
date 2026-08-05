<?php
$post = get_post(21698);
$c = $post->post_content;
// Fix table: remove problematic table-layout, ensure proper wrapping
$old_table = '<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;table-layout: auto;word-break: normal;overflow-wrap: normal">';
$new_table = '<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd">';
$c = str_replace($old_table, $new_table, $c);
// Add white-space:normal to all th and td cells
$c = str_replace(
    'border-bottom: 2px solid #ddd">Característica</th>',
    'border-bottom: 2px solid #ddd;white-space:normal">Característica</th>',
    $c
);
$c = str_replace(
    'border-bottom: 2px solid #ddd">Whey Protein</th>',
    'border-bottom: 2px solid #ddd;white-space:normal">Whey Protein</th>',
    $c
);
$c = str_replace(
    'border-bottom: 2px solid #ddd">Proteína Vegetal</th>',
    'border-bottom: 2px solid #ddd;white-space:normal">Proteína Vegetal</th>',
    $c
);
// Fix td cells that have border-bottom
$c = preg_replace(
    '/(<td style="padding: 10px 15px;border-bottom: 1px solid #eee)([^>]*>)/',
    '\$1;white-space:normal\$2',
    $c
);
// Fix td cells without border-bottom (last row)
$c = str_replace(
    '<td style="padding: 10px 15px"><strong>Disponibilidad en Panamá</strong></td>',
    '<td style="padding: 10px 15px;white-space:normal"><strong>Disponibilidad en Panamá</strong></td>',
    $c
);
$c = str_replace(
    '<td style="padding: 10px 15px">Amplia oferta en Suplementos Panamá</td>',
    '<td style="padding: 10px 15px;white-space:normal">Amplia oferta en Suplementos Panamá</td>',
    $c
);
$c = str_replace(
    '<td style="padding: 10px 15px">Oferta creciente (Impulse, LanderFit)</td>',
    '<td style="padding: 10px 15px;white-space:normal">Oferta creciente (Impulse, LanderFit)</td>',
    $c
);
wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
