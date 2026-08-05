#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
echo "=== Combos activos en categoria combos ==="
wp wc product list --category="combos" --field=id,name,sku,slug,stock_status --format=csv --path=$WP 2>/dev/null
echo "=== Products with 'combo' in name ==="
wp wc product list --search="combo" --field=id,name,sku,slug,stock_status --format=csv --path=$WP 2>/dev/null
