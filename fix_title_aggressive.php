<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
// Remove old rule and replace with more specific version targeting the <a> too
$old = '.blog-style-grid .entry-title { order: 1 !important; margin-bottom: 12px !important; font-size: 22px !important; } .blog-style-grid .entry-title a { font-size: 22px !important; }';
$new = '.blog-style-grid .entry-title { order: 1 !important; margin-bottom: 12px !important; font-size: 22px !important; line-height: 1.3 !important; } .blog-style-grid .entry-title a { font-size: 22px !important; line-height: 1.3 !important; }';
$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "OK\n";
