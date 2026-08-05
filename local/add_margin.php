<?php
$c = get_post(21678)->post_content;
$c = str_replace('<figure>', '<figure style="margin-bottom:25px">', $c);
wp_update_post(array('ID' => 21678, 'post_content' => $c));
echo "Margin added.\n";
