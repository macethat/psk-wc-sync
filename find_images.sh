#!/bin/bash
WP_PATH="/home/customer/www/suplementospanama.net/public_html"
echo "=== Images reference in Elementor data ==="
wp db query "SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key='_elementor_data' AND meta_value LIKE '%6.jpg%' LIMIT 5" --path=$WP_PATH 2>/dev/null
echo "---"
wp db query "SELECT post_id, meta_value FROM wp_postmeta WHERE meta_key='_elementor_data' AND meta_value LIKE '%banner-1920x400%' LIMIT 5" --path=$WP_PATH 2>/dev/null
echo "=== Image files in uploads/2025/06 ==="
ls -la $WP_PATH/wp-content/uploads/2025/06/ | head -20
echo "=== Check if files exist ==="
for f in 6.jpg banner-1920x400-copia-scaled.jpg; do
  find $WP_PATH/wp-content/uploads -name "$f" -type f 2>/dev/null | head -3
done
echo "=== Pages referencing these ==="
wp db query "SELECT ID, post_title, post_name, post_type FROM wp_posts WHERE ID IN (SELECT post_id FROM wp_postmeta WHERE meta_key='_elementor_data' AND (meta_value LIKE '%6.jpg%' OR meta_value LIKE '%banner-1920x400%'))" --path=$WP_PATH 2>/dev/null
