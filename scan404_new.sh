#!/bin/bash
LOGDIR="/home/customer/www/suplementospanama.net/logs"
echo "=== 404s in last 3 days (excluding assets) ==="
for f in "$LOGDIR"/suplementospanama.net-2026-07-1[9].gz "$LOGDIR"/suplementospanama.net-2026-07-2[0-1].gz; do
    [ -f "$f" ] || continue
    echo "--- $(basename $f) ---"
    zgrep " 404 " "$f" 2>/dev/null | grep -vE '\.(ico|css|js|woff2?|ttf|eot|svg|png|gif|webp|jpg|jpeg|map|txt|woff)$' | awk '{print $7}' | sort | uniq -c | sort -rn | head -20
done
echo ""
echo "=== 404 URLs with the most hits (daily avg) ==="
zgrep " 404 " "$LOGDIR"/suplementospanama.net-2026-07-2[0-1].gz 2>/dev/null | grep -vE '\.(ico|css|js|woff2?|ttf|eot|svg|png|gif|webp|jpg|jpeg|map|txt|woff)$' | awk '{print $7}' | sort | uniq -c | sort -rn | head -15