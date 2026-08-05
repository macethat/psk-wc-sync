#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Post 5179 ==="
wp post get 5179 --fields=ID,post_title,post_name,post_type,post_parent --path=$WP 2>/dev/null
echo "=== Elementor data for 5179 (grep) ==="
wp post meta get 5179 _elementor_data --path=$WP 2>/dev/null | grep -o 'uploads[^\"'\'']*' | head -20
echo "=== CSS files with refs ==="
grep -o 'uploads[^\"'\'')]*\.\(jpg\|JPG\)' $WP/wp-content/uploads/elementor/css/post-5179.css 2>/dev/null | head -10
grep -o 'uploads[^\"'\'')]*6\.\(jpg\|JPG\)' $WP/wp-content/uploads/siteground-optimizer-assets/siteground-optimizer-combined-css-27d67caceb8703895d6059266612a1a4.css 2>/dev/null | head -5
echo "=== How the image is referenced in CSS ==="
grep -o '6\.jpg[^\"'\'']*' $WP/wp-content/uploads/siteground-optimizer-assets/siteground-optimizer-combined-css-27d67caceb8703895d6059266612a1a4.css 2>/dev/null | head -5
