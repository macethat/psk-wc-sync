#!/bin/bash
DB="dbjrplogxonwvk"
USER="uvzuoggwpe9ny"
PASS="abjshgllpgju"
HOST="127.0.0.1"

echo "=== Direct DB check for 3 slugs ==="
mysql -h "$HOST" -u "$USER" -p"$PASS" "$DB" -e "SELECT ID, post_name, post_type, post_status FROM wp_posts WHERE post_name IN ('iso-total-pack', 'prolive-full-stack', 'vms-triple-stack') AND post_type='product' LIMIT 10" 2>/dev/null

echo ""
echo "=== URL test ==="
for u in iso-total-pack prolive-full-stack vms-triple-stack; do
    code=$(curl -sI -o /dev/null -w "%{http_code}" "https://suplementospanama.net/product/$u/" 2>/dev/null)
    echo "$u -> HTTP $code"
done
