<?php
$post = get_post(21678);
$c = $post->post_content;

$searches = array(
    '<h2>Mitos',
    '<h2>Beneficios',
    '<h2>Dosis',
    '<h2>Preguntas',
    '<h2>Conclusi',
    '<h3>"La creatina retiene',
    '<h3>"La creatina es solo',
    '<h3>"Engorda"</h3>',
    '<h3>Fuerza',
    '<h3>Composici',
    '<h3>Funci',
    '<h3>Recuperaci',
);
$replacements = array(
    '<h2 style="color:#000000">Mitos',
    '<h2 style="color:#000000">Beneficios',
    '<h2 style="color:#000000">Dosis',
    '<h2 style="color:#000000">Preguntas',
    '<h2 style="color:#000000">Conclusi',
    '<h3 style="color:#000000">"La creatina retiene',
    '<h3 style="color:#000000">"La creatina es solo',
    '<h3 style="color:#000000">"Engorda"</h3>',
    '<h3 style="color:#000000">Fuerza',
    '<h3 style="color:#000000">Composici',
    '<h3 style="color:#000000">Funci',
    '<h3 style="color:#000000">Recuperaci',
);
$c = str_replace($searches, $replacements, $c);
wp_update_post(array('ID' => 21678, 'post_content' => $c));
echo "H3 colors fixed.\n";
