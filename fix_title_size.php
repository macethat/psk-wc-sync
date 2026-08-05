<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
$c = str_replace(
    '.blog-style-grid .entry-title { order: 1 !important; margin-bottom: 12px !important; }',
    '.blog-style-grid .entry-title { order: 1 !important; margin-bottom: 12px !important; font-size: 22px !important; }',
    $c
);
file_put_contents($f, $c);
echo "OK\n";
