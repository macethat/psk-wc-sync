<?php
$content = file_get_contents('/home/u1910-kbd9lgn9dh44/post_body.html');
$post_id = wp_insert_post(array(
    'post_type'    => 'post',
    'post_status'  => 'draft',
    'post_title'   => 'Creatina para mujeres: Lo que la ciencia 2026 revela sobre fuerza y cerebro',
    'post_content' => $content,
    'post_category' => array(294),
    'tags_input'   => array('creatina', 'mujeres', 'fuerza', 'cognitivo'),
));
if ($post_id) {
    echo "Post created: {$post_id}\n";
    echo "URL: https://suplementospanama.net/?p={$post_id}\n";
} else {
    echo "ERROR: Failed to create post\n";
}
