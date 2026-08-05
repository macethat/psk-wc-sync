<?php
$schema = '{
    "@context": "https://schema.org",
    "@type": "Article",
    "headline": "Creatina para mujeres: Lo que la ciencia revela en 2026 sobre fuerza y cerebro",
    "description": "Descubre los nuevos estudios sobre creatina en mujeres: beneficios para fuerza, composición corporal y función cognitiva. Mitos y verdades basados en ciencia 2025-2026.",
    "wordCount": 650,
    "timeRequired": "PT2M",
    "image": {
        "@type": "ImageObject",
        "url": "https://suplementospanama.net/wp-content/uploads/2026/07/creatina-mujeres-sp-01.jpg",
        "width": 1424,
        "height": 752,
        "caption": "Creatina para mujeres: beneficios, fuerza y cerebro 2026"
    },
    "author": {
        "@type": "Organization",
        "name": "Suplementos Panamá",
        "url": "https://suplementospanama.net"
    },
    "publisher": {
        "@type": "Organization",
        "name": "Suplementos Panamá",
        "logo": {
            "@type": "ImageObject",
            "url": "https://suplementospanama.net/wp-content/uploads/2025/11/sp-header-logo.png",
            "width": 600,
            "height": 60
        }
    },
    "datePublished": "2026-07-15",
    "dateModified": "2026-07-15",
    "mainEntityOfPage": {
        "@type": "WebPage",
        "@id": "https://suplementospanama.net/creatina-para-mujeres-lo-que-la-ciencia-revela-en-2026-sobre-fuerza-y-cerebro/"
    },
    "keywords": "cognitivo, creatina, fuerza, mujeres",
    "articleSection": "Suplementos",
    "inLanguage": "es",
    "isAccessibleForFree": true,
    "about": [
        {"@type": "Thing", "name": "Creatina"},
        {"@type": "Thing", "name": "Mujeres"},
        {"@type": "Thing", "name": "Fuerza"}
    ]
}';
update_post_meta(21678, 'power_rack_schema', $schema);
echo "Schema saved.\n";
