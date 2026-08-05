<?php
$post = get_post(21698);
$c = $post->post_content;
$c = str_replace(
    '<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd">',
    '<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;table-layout: auto;word-break: normal;overflow-wrap: normal">',
    $c
);
wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
