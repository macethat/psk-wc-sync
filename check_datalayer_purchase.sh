#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Recent completed orders ==="
wp wc order list --posts_per_page=5 --status=completed --field=id,status,total,date_created --path=$WP 2>/dev/null
echo ""
echo "=== Last order ID ==="
LAST=$(wp wc order list --posts_per_page=1 --field=id --path=$WP 2>/dev/null)
echo "Last order: $LAST"
if [ -n "$LAST" ]; then
    echo "=== Order $LAST received page ==="
    wp eval '
        $url = home_url("/checkout/order-received/$LAST/");
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        $html = curl_exec($ch);
        curl_close($ch);
        preg_match_all("/dataLayer\.push\(([^)]+)\)/", $html, $matches);
        foreach ($matches[0] as $m) {
            if (strpos($m, "purchase") !== false || strpos($m, "ecommerce") !== false) {
                echo substr($m, 0, 500) . "\n---\n";
            }
        }
    ' --path=$WP 2>/dev/null
fi