<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
$c = str_replace(
    '.blog-style-grid { row-gap: 50px !important; }',
    '.blog-style-grid { row-gap: 35px !important; }',
    $c
);
file_put_contents($f, $c);
echo "OK\n";
