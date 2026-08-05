#!/bin/bash
urls=(
vms-triple-stack
prolive-full-stack
iso-total-pack
evofusion-choc-pb-creatina-vms
prolive-bio6-creatina-nutrex
)
for u in "${urls[@]}"; do
    code=$(curl -sI -o /dev/null -w "%{http_code}" "https://suplementospanama.net/product/$u/" 2>/dev/null)
    echo "$u -> HTTP $code"
done
echo ""
echo "=== Checking DB directly ==="
mysql -h localhost -u u1910_kGdIBHVGwu -p'R5fuHEG28o' u1910_kdi1h4rfhr -e "SELECT post_name, post_status FROM wp_posts WHERE post_name LIKE '%iso-total%' OR post_name LIKE '%prolive-full%' OR post_name LIKE '%vms-triple%' LIMIT 10" 2>/dev/null
