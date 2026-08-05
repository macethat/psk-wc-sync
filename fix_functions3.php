<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$lines = file($f);
$new = array();
foreach ($lines as $i => $line) {
    $n = $i + 1;
    if ($n >= 467 && $n <= 470) {
        continue;
    }
    $new[] = $line;
    if ($n == 466) {
        $new[] = "    \$accent = get_post_meta(get_the_ID(), 'highlight_accent', true);\n";
        $new[] = "    if (\$accent) {\n";
        $new[] = "        echo '<style>.sp-toc-bullet { background: ' . esc_attr(\$accent) . ' !important; } .sp-toc-toggle { color: ' . esc_attr(\$accent) . ' !important; }</style>' . \"\\n\";\n";
        $new[] = "    }\n";
    }
}
file_put_contents($f, implode('', $new));
echo "DONE\n";
