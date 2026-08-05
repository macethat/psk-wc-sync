<?php
$post = get_post(21678);
$c = $post->post_content;

// Find the image block (from <figure> to </figure>)
$img_start = strpos($c, '<figure><img src="https://suplementospanama.net/wp-content/uploads/2026/07/creatina-mujeres-sp-02.jpg');
$img_end = strpos($c, '</figure>', $img_start) + 9;
$img_block = substr($c, $img_start, $img_end - $img_start);

// Remove it from current position
$c = substr_replace($c, '', $img_start, $img_end - $img_start);

// Insert it before "<h2>Preguntas frecuentes</h2>"
$insert_pos = strpos($c, '<h2>Preguntas frecuentes</h2>');
if ($insert_pos === false) { echo "Target not found\n"; exit; }
$c = substr_replace($c, $img_block . "\n\n", $insert_pos, 0);

wp_update_post(array('ID' => 21678, 'post_content' => $c));
echo "Image moved.\n";
