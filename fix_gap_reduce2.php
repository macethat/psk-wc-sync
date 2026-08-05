<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
$c = str_replace(
    'row-gap: 35px',
    'row-gap: 25px',
    $c
);
file_put_contents($f, $c);
echo "OK\n";
