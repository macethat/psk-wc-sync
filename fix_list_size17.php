<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
$c = str_replace(
    '.content-area li, #primary li { font-size: 19px !important; line-height: 1.8 !important; }',
    '.content-area li, #primary li { font-size: 17px !important; line-height: 1.8 !important; }',
    $c
);
file_put_contents($f, $c);
echo "OK\n";
