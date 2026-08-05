#!/bin/bash
WP="wp post get --post_type=product --path=/home/customer/www/suplementospanama.net/public_html --field=ID --name="
for slug in proteina-forzagen-5-lb-creatina-nutrex-60-serv proteina-gold-standard-1-47-lb-creatina-categoria-5 proteinas-iso-nutrex-2-lb-creatina-nutrex-60-serv prolive-bio5-bum-pre-blue-razz proteina-vms-5-lb-creatina-nutrex-200-serv l-carnitina-3500-lipocide-evogen primeval-labs-proteinas-creatina-nutrex proteina-vms-5-lb-creatina-evogen-60-serv nitro-up-5-lb-creatina-nutrex-60-serv proteina-vms-5-lb-creatina-forzagen-72-serv; do
  echo -n "$slug: "
  $WP "$slug" 2>/dev/null || echo "NOT FOUND"
done
