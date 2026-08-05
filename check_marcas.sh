#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Check product-brand taxonomy ==="
wp db query "SELECT * FROM wp_term_taxonomy WHERE taxonomy LIKE '%brand%' LIMIT 5" --path=$WP 2>/dev/null
echo "=== Check if 'marcas' term exists ==="
wp db query "SELECT t.*, tt.taxonomy FROM wp_terms t JOIN wp_term_taxonomy tt ON t.term_id=tt.term_id WHERE t.name='marcas' OR t.slug='marcas'" --path=$WP 2>/dev/null
echo "=== All brands with product count ==="
wp term list product-brand --fields=term_id,name,slug,count --format=csv --path=$WP 2>/dev/null | head -20
echo "=== Where does the link come from? ==="
zgrep "product-brand/marcas" /home/customer/www/suplementospanama.net/logs/suplementospanama.net-2026-07-2[0-1].gz 2>/dev/null | awk '{print $11}' | sort | uniq -c | sort -rn | head -5