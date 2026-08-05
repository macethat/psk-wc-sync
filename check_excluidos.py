#!/usr/bin/env python3
import os, csv

BASE = "/home/u1910-kbd9lgn9dh44/www/suplementospanama.net/psk-sync"
UPD = os.path.join(BASE, "update_03-08-2026")

# leer lista de exclusion desde el .py
excl = set()
started = False
for line in open(os.path.join(BASE, "daily_stock_update.py")):
    ls = line.strip()
    if ls.startswith("PRICE_EXCLUDE_SKUS"):
        started = True
        continue
    if started:
        if ls.startswith("}"):
            break
        sku = ls.strip(",").strip('"')
        if sku.isdigit() and len(sku) >= 9:
            excl.add(sku)
print("fila? total SKUs excluidos:", len(excl))

# leer cambios_precios.csv
cambios_precios_present = os.path.exists(os.path.join(UPD, "cambios_precios.csv"))
print("existe cambios_precios.csv:", cambios_precios_present)
if cambios_precios_present:
    with open(os.path.join(UPD, "cambios_precios.csv")) as f:
        header = f.readline().strip()
        print("header:", header)
        rows = [l.strip().split(",") for l in f if l.strip()]
    print("cambios de precio aplicados:", len(rows))
    for r in rows:
        sku = r[3]
        marca = "EXCLUIDO!!" if sku in excl else "ok"
        print(f"  sku={sku} old={r[-2]} new={r[-1]} [{marca}]")

print("\nSKUs por caja EXCLUIDOS (no deben aparecer arriba):")
for s in sorted(excl):
    print("  ", s)