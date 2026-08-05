#!/bin/bash
LOGDIR="/home/customer/www/suplementospanama.net/logs"
echo "=== Checking last 5 days for UTMs ==="
for f in "$LOGDIR"/suplementospanama.net-2026-07-1[7-9].gz "$LOGDIR"/suplementospanama.net-2026-07-2[0-1].gz; do
    [ -f "$f" ] || continue
    count=$(zgrep -c "utm_" "$f" 2>/dev/null)
    echo "$(basename $f): $count UTM hits"
    if [ "$count" -gt 0 ]; then
        zgrep "utm_" "$f" 2>/dev/null | awk '{print $7}' | sort | uniq -c | sort -rn | head -5
    fi
done
echo ""
echo "=== Facebook/Meta traffic last 5 days ==="
for f in "$LOGDIR"/suplementospanama.net-2026-07-1[7-9].gz "$LOGDIR"/suplementospanama.net-2026-07-2[0-1].gz; do
    [ -f "$f" ] || continue
    count=$(zgrep -ci "facebook\|\.fb\.\|l\.facebook\|meta\." "$f" 2>/dev/null)
    echo "$(basename $f): $count hits"
done
echo ""
echo "=== Sample FB traffic (last 5 days) ==="
for f in "$LOGDIR"/suplementospanama.net-2026-07-2[0-1].gz; do
    [ -f "$f" ] || continue
    zgrep -i "facebook\|instagram" "$f" 2>/dev/null | awk '{print $7, $11}' | sort | uniq -c | sort -rn | head -5
done
