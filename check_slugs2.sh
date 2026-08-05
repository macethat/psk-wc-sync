#!/bin/bash
for id in 19835 19826 19817 19800 19778 21516 21511 21513 21523 18977 19734 18869; do
  echo -n "ID $id: "
  wp post get $id --field=post_name --path=/home/customer/www/suplementospanama.net/public_html 2>/dev/null
done
