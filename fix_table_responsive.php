<?php
$post = get_post(21698);
$c = $post->post_content;

// Add overflow-x:auto to wrapping div and reduce font
$c = str_replace(
    '<div style="margin-bottom: 25px">
<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd">',
    '<div style="overflow-x:auto;max-width:100%;margin-bottom:25px">
<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;overflow-wrap:break-word;word-break:break-word">',
    $c
);

wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
