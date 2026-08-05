<?php
$post_id = 21678;

// Get current content
$post = get_post($post_id);
$content = $post->post_content;

// Insert internal image after the first paragraph
$img_tag = "\n\n<figure><img src=\"https://suplementospanama.net/wp-content/uploads/2026/07/creatina-mujeres-sp-02.jpg\" alt=\"Infografia creatina para mujeres dosis beneficios y mitos\" width=\"600\" height=\"900\" loading=\"lazy\" class=\"aligncenter size-full\"><figcaption style=\"text-align:center;font-size:13px;color:#666\">Creatina para mujeres: beneficios, dosis y mitos derribados por la ciencia 2026</figcaption></figure>\n\n";

$parts = explode("</p>", $content, 2);
$new_content = $parts[0] . "</p>" . $img_tag . $parts[1];

wp_update_post(array(
    'ID' => $post_id,
    'post_content' => $new_content,
));

// Add Article schema
$schema = array(
    "@context" => "https://schema.org",
    "@type" => "Article",
    "headline" => "Creatina para mujeres: Lo que la ciencia 2026 revela sobre fuerza y cerebro",
    "description" => "Descubre los nuevos estudios sobre creatina en mujeres: beneficios para fuerza, composicion corporal y funcion cognitiva. Mitos y verdades basados en ciencia 2025-2026.",
    "author" => array(
        "@type" => "Organization",
        "name" => "Suplementos Panamá",
    ),
    "publisher" => array(
        "@type" => "Organization",
        "name" => "Suplementos Panamá",
        "logo" => array(
            "@type" => "ImageObject",
            "url" => "https://suplementospanama.net/wp-content/uploads/2025/11/sp-header-logo.png",
        ),
    ),
    "image" => "https://suplementospanama.net/wp-content/uploads/2026/07/creatina-mujeres-sp-01.jpg",
    "datePublished" => date("Y-m-d"),
    "dateModified" => date("Y-m-d"),
    "mainEntityOfPage" => array(
        "@type" => "WebPage",
        "@id" => "https://suplementospanama.net/?p=21678",
    ),
);
update_post_meta($post_id, 'power_rack_schema', json_encode($schema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

echo "Post updated. Images embedded.\n";
