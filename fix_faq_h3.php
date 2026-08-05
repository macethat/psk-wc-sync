<?php
$post = get_post(21678);
$c = $post->post_content;

$c = str_replace(
    '<p><strong>¿La creatina afecta las hormonas femeninas?</strong><br>No. Múltiples estudios confirman que la creatina no altera los niveles hormonales femeninos.</p>',
    '<h3>¿La creatina afecta las hormonas femeninas?</h3><p>No. Múltiples estudios confirman que la creatina no altera los niveles hormonales femeninos.</p>',
    $c
);

$c = str_replace(
    '<p><strong>¿Puedo tomarla si estoy en definición?</strong><br>Sí. La creatina ayuda a preservar músculo durante el déficit calórico, lo que acelera la definición.</p>',
    '<h3>¿Puedo tomarla si estoy en definición?</h3><p>Sí. La creatina ayuda a preservar músculo durante el déficit calórico, lo que acelera la definición.</p>',
    $c
);

$c = str_replace(
    '<p><strong>¿Sirve para mujeres mayores de 40?</strong><br>Sí. Los beneficios cognitivos y óseos son especialmente relevantes en mujeres postmenopáusicas.</p>',
    '<h3>¿Sirve para mujeres mayores de 40?</h3><p>Sí. Los beneficios cognitivos y óseos son especialmente relevantes en mujeres postmenopáusicas.</p>',
    $c
);

$c = str_replace(
    '<p><strong>¿Se puede combinar con proteína?</strong><br>Sí, es la combinación más recomendada: creatina para fuerza y proteína para recuperación muscular.</p>',
    '<h3>¿Se puede combinar con proteína?</h3><p>Sí, es la combinación más recomendada: creatina para fuerza y proteína para recuperación muscular.</p>',
    $c
);

wp_update_post(array('ID' => 21678, 'post_content' => $c));
echo "OK\n";
