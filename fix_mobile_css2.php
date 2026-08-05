<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);

$search = 'word-break: break-word !important; } }</style>';
$replace = 'word-break: break-word !important; } .content-area table, #primary table { width: auto !important; } .content-area table td, .content-area table th, #primary table td, #primary table th { white-space: nowrap !important; } }</style>';
$c = str_replace($search, $replace, $c);

file_put_contents($f, $c);
echo "OK\n";
