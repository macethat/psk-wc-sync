<?php
$post = get_post(21698);
$c = $post->post_content;

// Fix wrapping div + table style
$c = str_replace(
    '<div style="margin-bottom: 25px">
<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd;table-layout: auto;word-break: normal;overflow-wrap: normal">',
    '<div style="overflow-x:auto;max-width:100%;margin-bottom:25px">
<table style="width: 100%;border-collapse: collapse;font-size: 15px;border: 1px solid #ddd">',
    $c
);

wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
