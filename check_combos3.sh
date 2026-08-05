#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== term for combos ==="
wp db query "SELECT t.term_id, t.name, t.slug, tt.taxonomy, tt.count FROM wp_terms t JOIN wp_term_taxonomy tt ON t.term_id=tt.term_id WHERE t.slug='combos'" --path=$WP 2>/dev/null
echo ""
echo "=== All combos products (by term_id 284) ==="
wp db query "SELECT p.ID, p.post_title, p.post_name, p.post_status FROM wp_posts p JOIN wp_term_relationships tr ON p.ID=tr.object_id WHERE tr.term_taxonomy_id=284 AND p.post_type='product' AND p.post_status='publish' ORDER BY p.post_title" --path=$WP 2>/dev/null
echo ""
echo "=== Also check term 219 (Promociones) ==="
wp db query "SELECT p.ID, p.post_title, p.post_name FROM wp_posts p JOIN wp_term_relationships tr ON p.ID=tr.object_id WHERE tr.term_taxonomy_id=219 AND p.post_type='product' AND p.post_status='publish' ORDER BY p.post_title" --path=$WP 2>/dev/null
