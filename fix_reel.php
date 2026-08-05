<?php
$post = get_post(21698);
$c = $post->post_content;
$c = str_replace(
    '[products ids="21454,21335,18861,19121"]',
    '[products ids="21454,21335,18861"]',
    $c
);
wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
