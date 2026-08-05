<?php
$post = get_post(21698);
$c = $post->post_content;

// Remove width:100% from table inline style
$c = str_replace(
    '<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd">',
    '<table style="border-collapse: collapse;font-size: 15px;border: 1px solid #ddd">',
    $c
);

wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
