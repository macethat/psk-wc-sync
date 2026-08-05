<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);
$c = str_replace(
    "\\ = get_post_meta(get_the_ID(), 'highlight_accent', true);\n    if (\\) {\n        echo '<style>.sp-toc-bullet { background: ' . esc_attr(\\) . ' !important; } .sp-toc-toggle { color: ' . esc_attr(\\) . ' !important; }</style>' . \"\\n\";\n    }});",
    "    \$accent = get_post_meta(get_the_ID(), 'highlight_accent', true);\n    if (\$accent) {\n        echo '<style>.sp-toc-bullet { background: ' . esc_attr(\$accent) . ' !important; } .sp-toc-toggle { color: ' . esc_attr(\$accent) . ' !important; }</style>' . \"\\n\";\n    }\n});",
    $c
);
file_put_contents($f, $c);
echo "DONE\n";
