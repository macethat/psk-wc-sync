#!/bin/bash
cd /home/u1910-kbd9lgn9dh44/www/suplementospanama.net/psk-sync
sed -n '20,35p' daily_stock_update.py | grep -oE '[0-9]{9,13}' | sort -u > /tmp/excl.txt
echo "SKUs por caja excluidos: $(wc -l < /tmp/excl.txt)"
echo "---"
echo "Cruzando contra cambios_precios.csv de hoy..."
for sku in $(cat /tmp/excl.txt); do
  if grep -q "$sku" update_03-08-2026/cambios_precios.csv; then
    echo "ALERTA: $sku SI fue modificado"
  fi
done
echo "Fin del cruce (sin ALERTA = ningun excluido fue tocado)"
echo "---"
echo "Total precios modificados hoy: $(($(wc -l < update_03-08-2026/cambios_precios.csv)-1))"
echo "---"
echo "Muestra de excluidos en comparativa (estado previo):"
grep -E "CARNIVOR|AMINO ENERGY RTD|C4 PERFORMANCE|BATIDO" update_03-08-2026/comparativa_previa.csv | head -4 | cut -d, -f1,2,10,11,12