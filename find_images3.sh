#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== WooCommerce category with promociones slug ==="
wp db query "SELECT t.term_id, tt.parent, tt.count FROM wp_terms t JOIN wp_term_taxonomy tt ON t.term_id=tt.term_id WHERE t.slug='promociones' AND tt.taxonomy='product_cat'" --path=$WP 2>/dev/null
echo "=== All product categories ==="
wp term list product_cat --fields=term_id,name,slug --format=csv --path=$WP 2>/dev/null | head -30
echo "=== Search for 'promociones' page ==="
wp post list --post_type=page --s="promociones" --fields=ID,title,name --format=csv --path=$WP 2>/dev/null
echo "=== Check category description for image refs ==="
wp db query "SELECT term_id, description FROM wp_term_taxonomy WHERE description LIKE '%6.jpg%' OR description LIKE '%banner-1920x400%' OR description LIKE '%uploads%' LIMIT 10" --path=$WP 2>/dev/null
echo "=== Check if there are images similar (6.*) in uploads ==="
find $WP/wp-content/uploads -name '6.*' -type f 2>/dev/null | head -10
