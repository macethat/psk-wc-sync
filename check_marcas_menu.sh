#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Main menu items ==="
wp menu item list main-menu --path=$WP 2>/dev/null
echo "=== Menu de Inicio items ==="
wp menu item list menu-de-inicio --path=$WP 2>/dev/null | head -40
echo "=== Categorias menu items ==="
wp menu item list categorias --path=$WP 2>/dev/null | head -40
echo "=== Search all menu items for 'marca' ==="
wp db query "SELECT p.ID, p.post_title, p.post_content, p.post_name, pm.meta_value FROM wp_posts p JOIN wp_postmeta pm ON p.ID=pm.post_id WHERE p.post_type='nav_menu_item' AND (p.post_title LIKE '%Marca%' OR p.post_title LIKE '%marca%' OR pm.meta_key='_menu_item_url' AND pm.meta_value LIKE '%marca%')" --path=$WP 2>/dev/null