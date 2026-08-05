<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);

// Add blog grid gap in mobile media query
$search = 'white-space: nowrap !important; } .blog-style-grid .column-item { margin-bottom: 45px !important; padding-bottom: 25px !important; border-bottom: 1px solid #e0d6d0 !important; } .blog-style-grid .column-item .entry-title { margin-bottom: 0 !important; } }</style>';
$replace = 'white-space: nowrap !important; } .blog-style-grid { row-gap: 50px !important; } .blog-style-grid .column-item { margin-bottom: 0 !important; } }</style>';
$c = str_replace($search, $replace, $c);

file_put_contents($f, $c);
echo "OK\n";
