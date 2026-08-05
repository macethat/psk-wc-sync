#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== post_content references ==="
wp db query "SELECT ID, post_title, post_type FROM wp_posts WHERE post_content LIKE '%6.jpg%' OR post_content LIKE '%banner-1920x400%' LIMIT 10" --path=$WP 2>/dev/null
echo "=== widget references ==="
wp db query "SELECT option_name FROM wp_options WHERE option_value LIKE '%6.jpg%' OR option_value LIKE '%banner-1920x400%' LIMIT 10" --path=$WP 2>/dev/null
echo "=== Elementor with banner references ==="
wp db query "SELECT post_id FROM wp_postmeta WHERE meta_key='_elementor_data' AND (meta_value LIKE '%6.jpg%' OR meta_value LIKE '%banner-1920x400%') LIMIT 5" --path=$WP 2>/dev/null
echo "=== theme files with full path ==="
grep -rn '6.jpg\|banner-1920x400' $WP/wp-content/themes/nutritix/ --include='*.php' --include='*.css' 2>/dev/null | grep -v 'merlin-config' | head -10
echo "=== any redirect in .htaccess ==="
grep -i '6.jpg\|banner-1920x400' $WP/.htaccess 2>/dev/null
echo "=== upload structure check ==="
find $WP/wp-content/uploads -maxdepth 1 -type d | sort | tail -20
