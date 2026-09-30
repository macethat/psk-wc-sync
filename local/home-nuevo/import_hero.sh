#!/bin/bash
cd ~/www/suplementospanama.net/public_html || exit 1
echo "=== importando imagenes hero ==="
for f in /tmp/hero-imgs/combo-*.png; do
  base=$(basename "$f" .png)
  ID=$(wp media import "$f" --title="$base" --porcelain 2>/dev/null)
  URL=$(wp eval "echo wp_get_attachment_url($ID);" 2>/dev/null)
  echo "$base => id=$ID url=$URL"
done
