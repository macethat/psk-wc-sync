<?php
$f = "/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php";
$c = file_get_contents($f);

$old = <<<'PHP'
            $btn = '<div style="text-align:center;margin:30px 0"><a href="' . esc_url($cat_url) . '" style="display:inline-block;background:#c0392b;color:#fff;padding:12px 30px;text-decoration:none;font-size:16px;font-weight:600">Ver todas nuestras ' . esc_html(strtolower($cat_name)) . '</a></div>';
PHP;

$new = <<<'PHP'
            $btn_text = get_post_meta(get_the_ID(), 'reel_btn_text', true);
            if (!$btn_text) {
                $btn_text = 'Ver todas nuestras ' . strtolower($cat_term->name);
            }
            $btn = '<div style="text-align:center;margin:30px 0"><a href="' . esc_url($cat_url) . '" style="display:inline-block;background:#c0392b;color:#fff;padding:12px 30px;text-decoration:none;font-size:16px;font-weight:600">' . esc_html($btn_text) . '</a></div>';
PHP;

$c = str_replace($old, $new, $c);
file_put_contents($f, $c);
echo "OK\n";
