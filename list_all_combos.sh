#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"

echo "=== All products in Promociones (219) category ==="
wp db query "SELECT p.ID, p.post_title, p.post_name, p.post_status FROM wp_posts p JOIN wp_term_relationships tr ON p.ID=tr.object_id JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id=tt.term_id WHERE tt.term_id=219 AND p.post_type='product' AND p.post_status='publish' ORDER BY p.post_title" --path=$WP 2>/dev/null

echo ""
echo "=== All products in Combos (284) category ==="
wp db query "SELECT p.ID, p.post_title, p.post_name, p.post_status FROM wp_posts p JOIN wp_term_relationships tr ON p.ID=tr.object_id JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id=tt.term_id WHERE tt.term_id=284 AND p.post_type='product' AND p.post_status='publish' ORDER BY p.post_title" --path=$WP 2>/dev/null

echo ""
echo "=== All products with 'combo' or 'stack' or 'pack' in post_name ==="
wp db query "SELECT p.post_name, p.post_status FROM wp_posts p WHERE p.post_type='product' AND p.post_status='publish' AND (p.post_name LIKE '%-combo%' OR p.post_name LIKE '%-stack%' OR p.post_name LIKE '%-pack' OR p.post_name LIKE 'prolive%' OR p.post_name LIKE 'isoject%' OR p.post_name LIKE 'evofusion%' OR p.post_name LIKE 'vms-bios%') ORDER BY p.post_name" --path=$WP 2>/dev/null
