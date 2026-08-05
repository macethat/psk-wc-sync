#!/bin/bash
LOG="/var/log/apache2/suplementospanama.net-access.log"
echo "=== Facebook/Meta referrers ==="
zgrep -i "facebook\|\.fb\.\|l\.facebook\|meta\." $LOG 2>/dev/null | awk '{print $11}' | sort | uniq -c | sort -rn | head -10
echo ""
echo "=== Any UTM params in URLs ==="
zgrep -c "utm_" $LOG 2>/dev/null
echo ""
echo "=== Instagram referrers ==="
zgrep -i "instagram\|l\.instagram" $LOG 2>/dev/null | awk '{print $11}' | sort | uniq -c | sort -rn | head -10
echo ""
echo "=== Sample of recent social traffic ==="
zgrep -i "facebook\|instagram" $LOG 2>/dev/null | tail -3
