<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
$c = str_replace(
    'if (!is_single() && !is_home()) return;',
    'if (!is_single() && !is_home() && !is_category()) return;',
    $c
);
file_put_contents($f, $c);
echo "OK\n";
