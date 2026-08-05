<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);

// Add list font-size after p selector
$c = str_replace(
    ".content-area p, #primary p { font-size: 17px !important; line-height: 1.8 !important; } .sp-toc-wrap",
    ".content-area p, #primary p { font-size: 17px !important; line-height: 1.8 !important; } .content-area li, #primary li { font-size: 19px !important; line-height: 1.8 !important; } .sp-toc-wrap",
    $c
);

// Ensure ToC sub items don't inherit list font-size
$c = str_replace(
    ".sp-toc-sub li { margin: 4px 0; } .sp-toc-sub li a",
    ".sp-toc-sub li { margin: 4px 0; font-size: inherit !important; } .sp-toc-sub li a",
    $c
);

file_put_contents($f, $c);
echo "OK\n";
