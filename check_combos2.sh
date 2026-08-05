#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Products in Combos category ==="
wp db query "SELECT p.ID, p.post_title, p.post_name, p.post_status, pm.meta_value as stock_status FROM wp_posts p JOIN wp_term_relationships tr ON p.ID=tr.object_id JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id=tt.term_id JOIN wp_terms t ON tt.term_id=t.term_id LEFT JOIN wp_postmeta pm ON p.ID=pm.post_id AND pm.meta_key='_stock_status' WHERE t.slug='combos' AND p.post_type='product' AND p.post_status='publish' ORDER BY p.post_title" --path=$WP 2>/dev/null
echo ""
echo "=== Total published combos ==="
wp db query "SELECT COUNT(*) as total FROM wp_posts p JOIN wp_term_relationships tr ON p.ID=tr.object_id JOIN wp_term_taxonomy tt ON tr.term_taxonomy_id=tt.term_id JOIN wp_terms t ON tt.term_id=t.term_id WHERE t.slug='combos' AND p.post_type='product' AND p.post_status='publish'" --path=$WP 2>/dev/null
