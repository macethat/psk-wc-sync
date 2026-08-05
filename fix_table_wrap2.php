<?php
$post = get_post(21698);
$c = $post->post_content;

// 1. Fix table style - remove previous extra attrs, add white-space
$c = str_replace(
    'style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;table-layout: auto;word-break: normal;overflow-wrap: normal;white-space: normal">',
    'style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;white-space:normal">',
    $c
);
$c = str_replace(
    'style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;table-layout: auto;word-break: normal;overflow-wrap: normal">',
    'style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;white-space:normal">',
    $c
);
$c = str_replace(
    'style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd">',
    'style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;white-space:normal">',
    $c
);

// 2. Add white-space:normal to all th cells
$c = str_replace(
    'border-bottom: 2px solid #ddd">Caracter',
    'border-bottom: 2px solid #ddd;white-space:normal">Caracter',
    $c
);
$c = str_replace(
    'border-bottom: 2px solid #ddd">Whey Protein',
    'border-bottom: 2px solid #ddd;white-space:normal">Whey Protein',
    $c
);
$c = str_replace(
    'border-bottom: 2px solid #ddd">Prote',
    'border-bottom: 2px solid #ddd;white-space:normal">Prote',
    $c
);

// 3. Add white-space:normal to td cells that have border-bottom
$c = str_replace(
    'border-bottom: 1px solid #eee">',
    'border-bottom: 1px solid #eee;white-space:normal">',
    $c
);

// 4. Add white-space:normal to last row td cells (no border-bottom)
$c = str_replace(
    '<td style="padding: 10px 15px"><strong>',
    '<td style="padding: 10px 15px;white-space:normal"><strong>',
    $c
);
$c = str_replace(
    '<td style="padding: 10px 15px">Amplia',
    '<td style="padding: 10px 15px;white-space:normal">Amplia',
    $c
);
$c = str_replace(
    '<td style="padding: 10px 15px">Oferta',
    '<td style="padding: 10px 15px;white-space:normal">Oferta',
    $c
);

wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
