#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Shop page (ID 5) content ==="
wp post get 5 --field=post_content --path=$WP 2>/dev/null | grep -o 'uploads[^\"'\'' ]*' | head -10
echo "=== Shop page Elementor data (short) ==="
wp post meta get 5 _elementor_data --path=$WP 2>/dev/null | grep -o 'uploads[^\"'\'']*' | head -20
echo "=== Category promociones (219) description ==="
wp term get 219 product_cat --field=description --path=$WP 2>/dev/null | grep -o 'uploads[^\"'\'']*' | head -10
echo "=== Category combos (284) description ==="
wp term get 284 product_cat --field=description --path=$WP 2>/dev/null | grep -o 'uploads[^\"'\'']*' | head -10
echo "=== Checking elementor global widgets ==="
wp db query "SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key='_elementor_data' AND meta_value LIKE '%promociones%' LIMIT 3" --path=$WP 2>/dev/null | head -20
