<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);

// Add table word-break to mobile section
$search = 'content-area h3, #primary h3 { font-size: 19px !important; } }';
$replace = 'content-area h3, #primary h3 { font-size: 19px !important; } .content-area td, #primary td, .content-area th, #primary th { overflow-wrap: break-word !important; word-break: break-word !important; } }';
$c = str_replace($search, $replace, $c);

// Also update the general table rule to use break-word
$search2 = 'white-space: normal !important; } .sp-toc-wrap';
$replace2 = 'white-space: normal !important; overflow-wrap: break-word !important; word-break: break-word !important; } .sp-toc-wrap';
$c = str_replace($search2, $replace2, $c);

file_put_contents($f, $c);
echo "OK\n";
