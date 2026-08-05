<?php
$post = get_post(21698);
$c = $post->post_content;
$remove = '<figure style="margin-bottom:25px"><img src="https://suplementospanama.net/wp-content/uploads/2026/07/protenia-wey-vs-vegetal-sp-01.jpg" alt="whey vs proteina vegetal comparativa panama suplementos" width="600" height="900" loading="lazy" class="aligncenter size-full"><figcaption style="text-align:center;font-size:13px;color:#666">Whey vs Proteína Vegetal: Comparativa completa para elegir la mejor opción según tus objetivos</figcaption></figure>

';
$c = str_replace($remove, '', $c);
wp_update_post(array('ID' => 21698, 'post_content' => $c));
echo "OK\n";
