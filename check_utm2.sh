#!/bin/bash
echo "=== All log files ==="
ls -la /var/log/apache2/suplementospanama.net-access.log* 2>/dev/null
echo ""
echo "=== Checking all rotated logs ==="
for f in /var/log/apache2/suplementospanama.net-access.log*; do
    echo "--- $f ---"
    zgrep -c "facebook\|instagram" "$f" 2>/dev/null || echo "0"
done
echo ""
echo "=== UTM across all logs ==="
for f in /var/log/apache2/suplementospanama.net-access.log*; do
    count=$(zgrep -c "utm_" "$f" 2>/dev/null || echo "0")
    if [ "$count" -gt 0 ]; then
        echo "$f: $count UTM hits"
        zgrep "utm_" "$f" 2>/dev/null | awk '{print $7}' | sort | uniq -c | sort -rn | head -10
    fi
done
