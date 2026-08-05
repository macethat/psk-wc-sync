#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
slugs=(
"vms-triple-stack"
"isoject-vanilla-creatina-vms"
"evofusion-choc-pb-creatina-vms"
"vms-bios-active-glutamina-vms"
"evofusion-choc-pb-creatina-vms-bcaa-vms-fruit-punch"
"prolive-bio6-creatina-nutrex"
"prolive-full-stack"
"prolive-bio5-bum-pre-blue-raspberry"
"evofusion-choc-pb-bum-pre-blue-raspberry"
"iso-total-pack"
"combo-iso-100-fruity-pebbles-creatina-angry-supplements"
"prolive-bio6-bcaa-vms-30-servings"
"elite-performance-stack"
"vms-bios-active-raw-pre-orange"
"vms-bios-active-creatina-vms"
"prolive-bio5-glutamina-vms"
"isoject-vanilla-bcaa-vms-30-servings"
"isoject-vanilla-glutamina-vms"
"iso-100-fruity-pebbles-raw-pre-workout-orange"
"evofusion-choc-pb-bcaa-vms-fruit-punch"
)
echo "Verifying slugs..."
for s in "${slugs[@]}"; do
    result=$(wp db query "SELECT ID, post_type, post_status FROM wp_posts WHERE post_name='$s' AND post_type='product'" --path=$WP 2>/dev/null | tail -n+2)
    if [ -z "$result" ]; then
        echo "NOT FOUND: $s"
    else
        echo "OK: $s -> $result"
    fi
done
echo ""
echo "=== All products with 'combo' in post_name ==="
wp db query "SELECT post_name, post_status FROM wp_posts WHERE post_name LIKE '%combo%' AND post_type='product' AND post_status='publish' ORDER BY post_name" --path=$WP 2>/dev/null
echo ""
echo "=== Promociones page URL check ==="
wp post get $(wp db query "SELECT ID FROM wp_posts WHERE post_name='combos' AND post_type='page' LIMIT 1" --path=$WP 2>/dev/null | tail -n+2) --fields=ID,post_title,post_name,url --path=$WP 2>/dev/null
echo ""
echo "=== Category combos ==="
wp term get combos product_cat --path=$WP 2>/dev/null | head -5
