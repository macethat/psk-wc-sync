#!/bin/bash
WP="/home/customer/www/suplementospanama.net/public_html"
LOG="/var/log/apache2/suplementospanama.net-access.log"

echo "=== 404s today ==="
zgrep " 404 " $LOG 2>/dev/null | grep -vE '\.(ico|css|js|woff2?|ttf|eot|svg|png|gif|webp|jpg|jpeg|png|map|txt)$' | grep -oP 'GET \K[^ ]+' | sort | uniq -c | sort -rn | head -30

echo ""
echo "=== All 404 counts (including assets) ==="
zgrep " 404 " $LOG 2>/dev/null | grep -oP 'GET \K[^ ]+' | sort | uniq -c | sort -rn | head -30
