<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);

$search = '.content-area td, #primary td, .content-area th, #primary th { overflow-wrap: break-word !important; word-break: break-word !important; } }';
$replace = '.content-area td, #primary td, .content-area th, #primary th { overflow-wrap: break-word !important; word-break: break-word !important; } }';
$c = str_replace($search, $replace, $c);

file_put_contents($f, $c);
echo "OK\n";
