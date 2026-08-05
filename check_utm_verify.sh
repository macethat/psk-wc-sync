#!/bin/bash
LOGDIR="/home/customer/www/suplementospanama.net/logs"
echo "=== Checking UTM hits from meta campaigns ==="
for f in "$LOGDIR"/suplementospanama.net-2026-07-1[7-9].gz "$LOGDIR"/suplementospanama.net-2026-07-2[0-1].gz; do
    [ -f "$f" ] || continue
    utm_count=$(zgrep -c "utm_source=meta" "$f" 2>/dev/null)
    clics_count=$(zgrep -c "facebook.com\|l.facebook\|instagram" "$f" 2>/dev/null)
    echo "$(basename $f): UTM=$utm_count, FB/IG clics=$clics_count"
    if [ "$utm_count" -gt 0 ]; then
        echo "  Sample UTMs:"
        zgrep "utm_source=meta" "$f" 2>/dev/null | awk '{print $7}' | sort | uniq -c | sort -rn | head -5
    fi
done
