#!/bin/bash
LOGDIR="/home/customer/www/suplementospanama.net/logs"
echo "=== UTM hits today (Jul 21-22) ==="
for f in "$LOGDIR"/suplementospanama.net-2026-07-2[1-2].gz "$LOGDIR"/suplementospanama.net-2026-07-22.gz; do
    [ -f "$f" ] || continue
    total=$(zgrep -c "utm_source=meta\|utm_medium=paid_social" "$f" 2>/dev/null)
    echo "$(basename $f): $total UTM hits"
    if [ "$total" -gt 0 ]; then
        echo "  Top URLs con UTM:"
        zgrep "utm_source=meta" "$f" 2>/dev/null | awk '{print $7}' | sort | uniq -c | sort -rn | head -10
        echo "  Campaign names:"
        zgrep "utm_campaign=" "$f" 2>/dev/null | grep -oP 'utm_campaign=[^&\s]+' | sort | uniq -c | sort -rn | head -10
    fi
done