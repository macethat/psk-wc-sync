<?php
$c = get_post(21678)->post_content;
$old = 'Nuestro equipo está entrenado para asesorarte en tu proceso de entrenamiento. <strong>¡Lleva tu rendimiento al siguiente nivel!</strong>';
$new = 'Nuestro equipo de profesionales está capacitado para asesorarte en tu proceso individual de suplementación. <strong>¡Lleva tu rendimiento al siguiente nivel!</strong>';
$c = str_replace($old, $new, $c);
wp_update_post(array('ID' => 21678, 'post_content' => $c));
echo "Text updated.\n";
