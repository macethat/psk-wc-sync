#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Search for 'marca' in posts ==="
wp db query "SELECT ID, post_title, post_name, post_type, post_status FROM wp_posts WHERE post_name LIKE '%marca%' AND post_status='publish' LIMIT 10" --path=$WP 2>/dev/null
echo "=== Menu items ==="
wp menu list --path=$WP 2>/dev/null
echo "=== Search nav menu items ==="
wp db query "SELECT p.ID, p.post_title, p.post_name, pm.meta_value FROM wp_posts p JOIN wp_postmeta pm ON p.ID=pm.post_id WHERE p.post_type='nav_menu_item' AND pm.meta_key='_menu_item_url' AND pm.meta_value LIKE '%marca%'" --path=$WP 2>/dev/null
echo "=== Any widget with marcas ==="
wp db query "SELECT option_name, option_value FROM wp_options WHERE option_name LIKE '%widget%' AND option_value LIKE '%marca%'" --path=$WP 2>/dev/null