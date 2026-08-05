<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);

// Add before the mobile media query
$search = '} .sp-toc-wrap { text-align: left; }';
$replace = '} .content-area table, #primary table { width: 100%; } .sp-toc-wrap { text-align: left; }';
$c = str_replace($search, $replace, $c);

// Add mobile table rule inside the existing media query
$search2 = 'content-area h3, #primary h3 { font-size: 19px !important; } }';
$replace2 = 'content-area h3, #primary h3 { font-size: 19px !important; } .content-area table, #primary table { width: auto !important; } .content-area table td, .content-area table th, #primary table td, #primary table th { white-space: nowrap !important; } }';
$c = str_replace($search2, $replace2, $c);

file_put_contents($f, $c);
echo "OK\n";
