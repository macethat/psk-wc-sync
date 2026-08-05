<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
$search = '} .sp-toc-wrap { text-align: left; }';
$replace = '} .content-area table td, .content-area table th, #primary table td, #primary table th { white-space: normal !important; } .sp-toc-wrap { text-align: left; }';
$c = str_replace($search, $replace, $c);
file_put_contents($f, $c);
echo "OK\n";
