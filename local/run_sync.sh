#!/bin/bash
export OPENBLAS_NUM_THREADS=1
export OMP_NUM_THREADS=1
export MKL_NUM_THREADS=1

DIR="$(cd "$(dirname "$0")" && pwd)"
cd "$DIR"

# 1) Sync diario de stock y precios (PSK -> WooCommerce)
/usr/bin/python3 daily_stock_update.py --live --update-prices >> cron.log 2>&1

# 2) Auditoria de combos: corrige cache _price desfasado (bug del sync que
#    escribe _regular_price sin recalcular _price) y reporta combos con
#    cifras desactualizadas en la ficha. Mantiene SIEMPRE _combo_price fijo.
#    Regla: los textos de ahorro/retail de las fichas se actualizan via el
#    skill combo-sync-ahorro cuando el reporte marque pendientes.
echo "" >> cron.log
echo "=== AUDITORIA DE COMBOS $(date '+%Y-%m-%d %H:%M') ===" >> cron.log
cd ~/www/suplementospanama.net/public_html
wp eval-file "$DIR/sp_auditar_combos.php" -- fix >> "$DIR/cron.log" 2>&1
