#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Check creatina slug ==="
wp db query "SELECT ID, post_name, post_status FROM wp_posts WHERE post_name='creatina-micronizada-mohidratada'" --path=$WP 2>/dev/null
echo "=== Check iso100 draft ==="
wp db query "SELECT ID, post_name, post_status FROM wp_posts WHERE post_name='proteina-iso100-dymatize-1-34-lbs'" --path=$WP 2>/dev/null
echo "=== Source of 6.jpg refs ==="
grep -rl '2025/06/6\.jpg' $WP/wp-content/uploads/siteground-optimizer-assets/ 2>/dev/null | head -3
echo "=== SG Optimizer cache age ==="
ls -la $WP/wp-content/uploads/siteground-optimizer-assets/siteground-optimizer-combined-css-*.css 2>/dev/null | head -3