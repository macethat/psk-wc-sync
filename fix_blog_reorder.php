<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);

$search = 'row-gap: 50px !important; } .blog-style-grid .column-item { margin-bottom: 0 !important; } }</style>';
$replace = 'row-gap: 50px !important; } .blog-style-grid .column-item { margin-bottom: 0 !important; } .blog-style-grid .post-inner { display: flex !important; flex-direction: column !important; } .blog-style-grid .post-thumbnail { order: 2 !important; } .blog-style-grid .entry-content { order: 1 !important; display: flex !important; flex-direction: column !important; } .blog-style-grid .entry-title { order: 1 !important; margin-bottom: 12px !important; } .blog-style-grid .entry-meta { order: 2 !important; } }</style>';
$c = str_replace($search, $replace, $c);

file_put_contents($f, $c);
echo "OK\n";
