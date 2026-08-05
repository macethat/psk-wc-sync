#!/bin/bash
for url in proteina-forzagen-5-lb-creatina-nutrex-60-serv l-carnitina-3500-lipocide-evogen primeval-labs-proteinas-creatina-nutrex prolive-bio5-bum-pre-blue-razz creatina-evogen-60-serv-beta-alanina-raw evp-xtreme-n-o-polar-cherry-frost cla-landerfit-l-carnitina-3500 nitro-up-5-lb-creatina-nutrex-60-serv proteina-gold-standard-1-47-lb-creatina-categoria-5 lean-gainer-8-lb-creatina-forzagen-72-serv; do
  code=$(curl -sI -o /dev/null -w '%{http_code}' "https://suplementospanama.net/product/$url/")
  echo "$url: $code"
done
