<?php
$content = file_get_contents("/tmp/post_good.txt");
if ($content === false) { echo "ERROR: cannot read file\n"; exit(1); }
// Replace 4 products with 3
$content = str_replace(
    '[products ids="21454,21335,18861,19121"]',
    '[products ids="21454,21335,18861"]',
    $content
);
$result = wp_update_post(array("ID" => 21698, "post_content" => $content));
if ($result == 0) { echo "ERROR: wp_update_post failed\n"; exit(1); }
echo "OK: $result\n";
