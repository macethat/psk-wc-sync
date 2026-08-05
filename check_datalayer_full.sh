#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== dataLayer pushes on product page ==="
wp eval '
$url = home_url("/product/vms-triple-stack/");
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$html = curl_exec($ch);
curl_close($ch);
preg_match_all("/dataLayer\.push\(([^)]+)\)/", $html, $matches);
$count = 0;
foreach ($matches[1] as $m) {
    if (strpos($m, "ecommerce") !== false || strpos($m, "view_item") !== false || strpos($m, "add_to_cart") !== false) {
        echo substr($m, 0, 400) . "\n---\n";
        $count++;
        if ($count >= 5) break;
    }
}
if ($count == 0) {
    echo "NO ecommerce dataLayer pushes found\n";
    // Show all dataLayer pushes
    foreach ($matches[1] as $m) {
        echo substr($m, 0, 300) . "\n";
    }
}
' --path=$WP 2>/dev/null