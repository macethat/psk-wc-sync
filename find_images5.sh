#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== SG Optimizer CSS files ==="
grep -rl '6.jpg\|banner-1920x400' $WP/wp-content/uploads/siteground-optimizer-assets/ 2>/dev/null | head -5
echo "=== Elementor global CSS ==="
grep -rl '6.jpg\|banner-1920x400' $WP/wp-content/uploads/elementor/ 2>/dev/null | head -5
echo "=== Theme style.css ==="
grep '6.jpg\|banner-1920x400' $WP/wp-content/themes/nutritix/style.css 2>/dev/null | head -5
echo "=== Child theme style.css ==="
grep '6.jpg\|banner-1920x400' $WP/wp-content/themes/nutritix-child/style.css 2>/dev/null | head -5
echo "=== All uploads images named 6.* ==="
find $WP/wp-content/uploads -name '6.*' -not -name '6.webp' -type f 2>/dev/null | head -10
echo "=== Checking if images exist with different extension ==="
for ext in jpg jpeg png gif webp; do
  find $WP/wp-content/uploads -name "6.$ext" -type f 2>/dev/null
  find $WP/wp-content/uploads -name "banner-1920x400-copia*" -type f 2>/dev/null
done
echo "=== Checking Elementor Kit settings ==="
wp post list --post_type=elementor_library --posts_per_page=5 --fields=ID,title --format=csv --path=$WP 2>/dev/null
echo "=== Elementor global settings ==="
wp db query "SELECT option_value FROM wp_options WHERE option_name='elementor_active_kit' LIMIT 1" --path=$WP 2>/dev/null
