#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Check if GTM Kit has purchase event configured ==="
wp db query "SELECT option_value FROM wp_options WHERE option_name='gtmkit_config'" --path=$WP 2>/dev/null | head -5
echo ""
echo "=== Recent orders (last 5) ==="
wp wc order list --posts_per_page=5 --field=id,status,total,date_created --path=$WP 2>/dev/null
echo ""
echo "=== Check if purchase meta exists ==="
wp db query "SELECT COUNT(*) as total FROM wp_postmeta WHERE meta_key='_ga4_purchase_sent' OR meta_key='_gtmkit_purchase_sent'" --path=$WP 2>/dev/null
echo ""
echo "=== Check order received page source for dataLayer ==="
ORDER_ID=$(wp wc order list --posts_per_page=1 --field=id --path=$WP 2>/dev/null)
if [ -n "$ORDER_ID" ]; then
    wp eval "echo file_get_contents( home_url( '/checkout/order-received/' . $ORDER_ID . '/' ) );" --path=$WP 2>/dev/null | grep -oP 'dataLayer[^<]+' | head -3
fi