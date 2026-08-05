#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Recent orders from DB ==="
wp db query "SELECT ID, post_status, post_date FROM wp_posts WHERE post_type='shop_order' AND post_status NOT LIKE '%draft%' ORDER BY ID DESC LIMIT 5" --path=$WP 2>/dev/null
echo ""
echo "=== Check thank you page source ==="
wp eval '
$url = home_url("/checkout/order-received/");
$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_TIMEOUT, 10);
$html = curl_exec($ch);
curl_close($ch);
echo "Page title: " . wp_get_document_title() . "\n";
echo "Has dataLayer: " . (strpos($html, "dataLayer") !== false ? "YES" : "NO") . "\n";
preg_match_all("/dataLayer\s*=\s*\[\]/", $html, $m1);
echo "dataLayer init count: " . count($m1[0]) . "\n";
preg_match_all("/dataLayer\.push\(/", $html, $m2);
echo "dataLayer.push count: " . count($m2[0]) . "\n";
if (count($m2[0]) > 0) {
    preg_match_all("/dataLayer\.push\(({[^}]+})\)/", $html, $m3);
    foreach ($m3[1] as $push) {
        if (strpos($push, "ecommerce") !== false || strpos($push, "purchase") !== false) {
            echo "ECOMMERCE PUSH: " . substr($push, 0, 300) . "\n";
        }
    }
}
' --path=$WP 2>/dev/null