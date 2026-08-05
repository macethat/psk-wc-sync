<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
$bad = '    \ = get_post_meta(get_the_ID(), \'highlight_accent\', true);
    if (\) {
        echo \'<style>.sp-toc-bullet { background: \' . esc_attr(\) . \' !important; } .sp-toc-toggle { color: \' . esc_attr(\) . \' !important; }</style>\' . "\n";
    }});
$good = '    $accent = get_post_meta(get_the_ID(), \'highlight_accent\', true);
    if ($accent) {
        echo \'<style>.sp-toc-bullet { background: \' . esc_attr($accent) . \' !important; } .sp-toc-toggle { color: \' . esc_attr($accent) . \' !important; }</style>\' . "\n";
    }');
$c = str_replace($bad, $good, $c);
file_put_contents($f, $c);
echo "DONE\n";
