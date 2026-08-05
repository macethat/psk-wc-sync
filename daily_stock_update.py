import os
os.environ["OPENBLAS_NUM_THREADS"] = "1"
os.environ["OMP_NUM_THREADS"] = "1"
os.environ["MKL_NUM_THREADS"] = "1"

import pandas as pd
import json
import sys
import subprocess
import shutil
from datetime import datetime

CARPETA_BASE = os.path.dirname(os.path.abspath(__file__))
STOCK_LIMIT = 6
WP_DIR = os.path.expanduser("~/www/suplementospanama.net/public_html")
EXPORT_PHP = os.path.expanduser("~/wc_export_ssh.php")

# Productos excluidos temporalmente de actualizacion de precios (pack products)
# Stock se actualiza normal, solo se salta el precio
PRICE_EXCLUDE_SKUS = {
    # RTD Carnivor 500ml
    "891597004034", "891597006137", "891597006427", "891597006113",
    # Batido Proteinas 325ml 12 Pack
    "748927066593", "748927069815", "748927066586",
    # ON Amino Energy Pack 12
    "748927060621", "748927062731", "748927060607", "748927062748",
    "748927060614", "748927068269", "748927063486",
    # C4 Ultimate Pack 12
    "842595131741", "842595135541", "842595131727", "842595135534", "842595135558",
    # C4 Performance Pack 12
    "842595111071", "842595106572", "842595131291", "842595131628",
    "842595134957", "842595131253", "842595121766", "842595121759",
    "842595134971", "842595106596", "842595131277", "842595133608",
    "842595111095", "842595134964",
}

PSK_PIN = "46558"
PSK_API_KEY = "BQxQrt5/FwARtlVUwT0GFw=="
PSK_API_HOST = "adm.premium-soft.com"

def run_wp(cmd, timeout=60):
    full = f"wp --user=Fersho --path={WP_DIR} {cmd}"
    r = subprocess.run(full, shell=True, capture_output=True, timeout=timeout)
    if r.returncode != 0:
        err = r.stderr.decode('utf-8', errors='replace').strip()
        print(f"  ERROR WP-CLI ({r.returncode}): {err[:200]}")
        return ""
    return r.stdout.decode('utf-8', errors='replace').strip()

def get_wc_export(suffix=""):
    print("Exportando productos localmente...")
    out = run_wp(f'eval-file {EXPORT_PHP}')
    if not out:
        print("  ERROR: salida vacia de WP-CLI")
        return None
    try:
        data = json.loads(out)
    except json.JSONDecodeError as e:
        print(f"  ERROR: JSON invalido - {e}")
        print(f"  Primeros 200 chars: {out[:200]}")
        return None
    local = os.path.join(CARPETA_BASE, f"tmp_wc_export{suffix}.json")
    with open(local, 'w', encoding='utf-8') as f:
        json.dump(data, f, ensure_ascii=False)
    print(f"  Exportados: {len(data)} productos")
    return local

def fetch_from_psk_api():
    import http.client
    print("Extrayendo inventario desde PSK Cloud API...")
    conn = http.client.HTTPSConnection(PSK_API_HOST)
    conn.request('GET', f'/Api/Articulos?pin={PSK_PIN}&pagina=0&cant_pagina=99999&precios=1',
                 headers={'clave-api-business': PSK_API_KEY})
    r = conn.getresponse()
    if r.status != 200:
        print(f"ERROR: API respondio con status {r.status}")
        sys.exit(1)
    data = json.loads(r.read().decode())
    if not isinstance(data, list):
        print(f"ERROR: Respuesta inesperada de API: {data}")
        sys.exit(1)
    articulo_to_codigo = {}
    rows = []
    for a in data:
        cod = a.get('codigo', '')
        nom = a.get('nombre', '')
        ext = a.get('existencias', '0')
        try:
            ext = int(float(ext))
        except:
            ext = 0
        # Build id_articulo -> codigo mapping for sucursal stock
        id_art = a.get('id_articulo')
        if id_art and cod:
            articulo_to_codigo[id_art] = cod
        precio = None
        for p in a.get('precios', []):
            if p['id_tipo_precio'] == '3':
                try:
                    precio = round(float(p['precio_neto']), 2)
                except:
                    pass
                break
        rows.append({"Codigo": cod, "Nombre": nom, "Cant.Total": ext, "Precio": precio})
    df = pd.DataFrame(rows)
    df["Codigo"] = df["Codigo"].str.strip()
    print(f"  Extraidos {len(df)} articulos desde PSK Cloud API")
    return df, articulo_to_codigo

# === SUCURSALES CONFIG ===
SUCURSALES = {
    "1":  {"display": "SP El Cangrejo",      "meta_key": "_sucursal_1_stock"},
    "5":  {"display": "SP Megapolis",        "meta_key": "_sucursal_5_stock"},
    "6":  {"display": "SP Atrio Mall",       "meta_key": "_sucursal_6_stock"},
    "7":  {"display": "SP San Francisco",    "meta_key": "_sucursal_7_stock"},
    "8":  {"display": "SP Altos de Panamá",  "meta_key": "_sucursal_8_stock"},
    "10": {"display": "SP Metromall",        "meta_key": "_sucursal_10_stock"},
}

def fetch_sucursal_stock(articulo_to_codigo):
    """Fetch stock per warehouse from PSK API and map to SKUs.
    
    Args:
        articulo_to_codigo: dict mapping id_articulo -> codigo (SKU)
    
    Returns:
        dict: {codigo: {id_almacen: stock_qty}} 
    """
    import http.client
    print("Extrayendo stock por sucursal desde PSK Cloud API...")
    conn = http.client.HTTPSConnection(PSK_API_HOST)
    
    sucursal_ids = set(SUCURSALES.keys())
    
    conn.request('GET', f'/Api/Existencias?pin={PSK_PIN}',
                 headers={'clave-api-business': PSK_API_KEY})
    r = conn.getresponse()
    data = json.loads(r.read().decode())
    if not isinstance(data, list):
        print(f"  ERROR: respuesta inesperada de Existencias: {type(data)}")
        return {}
    
    print(f"  Registros de existencia: {len(data)}")
    
    result = {}
    for ex in data:
        id_art = ex.get('id_articulo')
        id_alm = ex.get('id_almacen')
        if id_alm not in sucursal_ids:
            continue
        codigo = articulo_to_codigo.get(id_art)
        if not codigo:
            continue
        try:
            qty = int(float(ex.get('existencia', 0)))
        except:
            qty = 0
        if codigo not in result:
            result[codigo] = {}
        result[codigo][id_alm] = qty
    
    # Print summary for each sucursal
    for aid in sucursal_ids:
        count = sum(1 for v in result.values() if aid in v and v[aid] > 0)
        print(f"  {SUCURSALES[aid]['display']}: {count} productos con stock")
    
    return result


def git_commit_and_push(carpeta, ok, fail, disc):
    git_key = os.path.join(CARPETA_BASE, "github-key-nopass")
    msg = f"update {os.path.basename(carpeta).replace('update_', '')}: {ok} OK, {fail} fail, {disc} disc"
    repo_dir = CARPETA_BASE
    env = os.environ.copy()
    env["GIT_SSH_COMMAND"] = f'ssh -i "{git_key}" -o StrictHostKeyChecking=no'
    try:
        subprocess.run(["git", "add", carpeta], cwd=repo_dir, capture_output=True, env=env)
        subprocess.run(["git", "commit", "-m", msg], cwd=repo_dir, capture_output=True, env=env)
        r = subprocess.run(["git", "push", "origin", "master"], cwd=repo_dir, capture_output=True, timeout=30, env=env)
        out = r.stdout.decode() + r.stderr.decode()
        print(f"  GitHub: {out.splitlines()[-1] if out.splitlines() else 'ok'}")
    except Exception as e:
        print(f"  GitHub WARN: {e}")

def main():
    dry_run = "--live" not in sys.argv
    fecha_arg = None
    for i, a in enumerate(sys.argv):
        if a == "--fecha" and i+1 < len(sys.argv):
            fecha_arg = sys.argv[i+1]
            sys.argv.pop(i+1); sys.argv.pop(i)
            break

    update_prices = "--update-prices" in sys.argv
    print("=== ACTUALIZACION DIARIA DE STOCK (SiteGround) ===")
    print(f"Fecha: {datetime.now().strftime('%d/%m/%Y %H:%M')}\n")
    if update_prices:
        print("  [Modo: stock + precios]\n")
    else:
        print("  [Modo: solo stock]\n")

    fec = datetime.strptime(fecha_arg, "%d-%m-%Y") if fecha_arg else datetime.now()
    carpeta = os.path.join(CARPETA_BASE, f"update_{fec.strftime('%d-%m-%Y')}")
    os.makedirs(carpeta, exist_ok=True)

    df_inv, art_to_cod = fetch_from_psk_api()
    
    # Fetch stock per sucursal
    sucursal_stock = fetch_sucursal_stock(art_to_cod)
    
    # Save sucursal stock report
    rows_ss = []
    for codigo, almacenes in sorted(sucursal_stock.items()):
        row = {"SKU": codigo}
        for aid in sorted(SUCURSALES.keys()):
            row[SUCURSALES[aid]["display"]] = almacenes.get(aid, 0)
        rows_ss.append(row)
    if rows_ss:
        pd.DataFrame(rows_ss).to_csv(os.path.join(carpeta, "stock_por_sucursal.csv"), index=False)
        print(f"  Stock por sucursal: {len(rows_ss)} productos con datos")
    
    df_inv.to_csv(os.path.join(carpeta, "ListaInvFisic.csv"), index=False)

    wc_json = get_wc_export(suffix="_1")
    if wc_json is None:
        sys.exit(1)
    wc_path = os.path.join(carpeta, "wc_export.json")
    shutil.copy2(wc_json, wc_path)

    with open(wc_json, encoding='utf-8') as f:
        wc_products = json.load(f)

    wc_by_sku = {}
    for p in wc_products:
        sku = p["sku"].strip() if p["sku"] else ""
        if sku:
            wc_by_sku[sku] = p

    cod_inv = set(df_inv["Codigo"].dropna().unique())
    sku_wc = set(wc_by_sku.keys())
    coinciden = cod_inv & sku_wc

    rows_comp = []
    for sku in coinciden:
        wc_p = wc_by_sku[sku]
        inv_row = df_inv[df_inv["Codigo"] == sku].iloc[0]
        old_s = wc_p["stock_qty"] if wc_p["stock_qty"] is not None else 0
        try:
            ns = int(inv_row["Cant.Total"])
        except:
            continue
        ns_status = "outofstock" if ns <= STOCK_LIMIT else "instock"
        diff = ns - old_s
        # Price comparison
        wc_price = wc_p.get("regular_price")
        inv_price = inv_row.get("Precio")
        price_diff = ""
        price_change = ""
        if update_prices and inv_price is not None and wc_price is not None:
            try:
                wp = float(wc_price)
                ip = float(inv_price)
                if abs(wp - ip) > 0.01:
                    price_diff = f"{ip:.2f}"
                    price_change = "SI"
                else:
                    price_diff = f"{ip:.2f}"
                    price_change = "no"
            except:
                pass
        elif inv_price is not None:
            price_diff = f"{inv_price:.2f}" if isinstance(inv_price, float) else str(inv_price)
            price_change = "nuevo" if not wc_price else ""
        rows_comp.append({
            "sku": sku, "nombre": inv_row.get("Nombre", wc_p.get("name", "")),
            "tipo": wc_p["type"], "wc_stock": old_s, "wc_status": wc_p["stock_st"],
            "inv_stock": ns, "nuevo_status": ns_status, "diferencia": diff,
            "wc_price": wc_price, "inv_price": price_diff,
            "price_change": price_change,
            "cambiara": "SI" if (diff != 0 or wc_p["stock_st"] != ns_status or price_change == "SI") else "no"
        })
    for sku in (sku_wc - cod_inv):
        wc_p = wc_by_sku[sku]
        old_s = wc_p["stock_qty"] if wc_p["stock_qty"] is not None else 0
        if old_s <= STOCK_LIMIT:
            rows_comp.append({
                "sku": sku, "nombre": wc_p.get("name", ""),
                "tipo": wc_p["type"], "wc_stock": old_s, "wc_status": wc_p["stock_st"],
                "inv_stock": "(solo WC)", "nuevo_status": "outofstock", "diferencia": "-",
                "wc_price": wc_p.get("regular_price"), "inv_price": "", "price_change": "",
                "cambiara": "SI"
            })
    comp_df = pd.DataFrame(rows_comp)
    comp_df.to_csv(os.path.join(carpeta, "comparativa_previa.csv"), index=False)
    cambiaran = sum(1 for r in rows_comp if r["cambiara"] == "SI")
    print(f"  Comparativa previa: {len(rows_comp)} productos ({cambiaran} cambiaran)")

    updates = []
    for sku in coinciden:
        wc_p = wc_by_sku[sku]
        inv_row = df_inv[df_inv["Codigo"] == sku].iloc[0]
        old_stock = wc_p["stock_qty"] if wc_p["stock_qty"] is not None else 0
        try:
            ns = int(inv_row["Cant.Total"])
        except:
            continue
        inv_price = inv_row.get("Precio")
        updates.append({
            "id": wc_p["id"], "parent": wc_p["parent"],
            "tipo": wc_p["type"], "sku": sku,
            "nombre": inv_row.get("Nombre", wc_p.get("name", "")),
            "old_stock": old_stock, "new_stock": ns,
            "new_status": "outofstock" if ns <= STOCK_LIMIT else "instock",
            "old_status": wc_p["stock_st"],
            "manage": wc_p["manage"],
            "old_price": wc_p.get("regular_price"),
            "new_price": f"{inv_price:.2f}" if inv_price is not None else None,
        })

    for sku in (sku_wc - cod_inv):
        wc_p = wc_by_sku[sku]
        cs = wc_p["stock_qty"] if wc_p["stock_qty"] is not None else 0
        if cs <= STOCK_LIMIT:
            updates.append({
                "id": wc_p["id"], "parent": wc_p["parent"],
                "tipo": wc_p["type"], "sku": sku,
                "nombre": wc_p.get("name", ""),
                "old_stock": cs, "new_stock": cs,
                "new_status": "outofstock",
                "old_status": wc_p["stock_st"],
                "manage": wc_p["manage"],
                "old_price": wc_p.get("regular_price"),
                "new_price": None,
            })

    df_prev = pd.DataFrame(updates)
    df_prev.to_csv(os.path.join(carpeta, "reporte_preview.csv"), index=False)
    print(f"\n{'='*70}")
    print(f"  {'SIMULACION' if dry_run else 'ACTUALIZACION EN VIVO'}")
    print(f"  Total a procesar: {len(updates)} productos")
    print(f"  Coincidencias: {len(coinciden)} | Solo WC (low stock): {len(updates)-len(coinciden)}")
    print(f"{'='*70}")

    needs_wp = []
    price_updates = []
    for u in updates:
        diff = u["new_stock"] - u["old_stock"]
        signo = "+" if diff > 0 else ""
        stock_changed = (diff != 0) or (u["new_status"] != u["old_status"])

        # Check price change (skip excluded SKUs)
        price_changed = False
        if update_prices and u["sku"] not in PRICE_EXCLUDE_SKUS and u["new_price"] is not None and u["old_price"] is not None:
            try:
                op = float(u["old_price"])
                np = float(u["new_price"])
                price_changed = abs(op - np) > 0.01
            except:
                pass
        elif update_prices and u["sku"] in PRICE_EXCLUDE_SKUS:
            if u["tipo"] == "variation":
                print(f"  (precio excluido para {u['sku']})", end="")

        needs_update = stock_changed or price_changed

        price_tag = ""
        if price_changed:
            price_tag = f" ${u['old_price']}->${u['new_price']}"
            price_updates.append(u)

        print(f"  {u['tipo']:<9} ID:{u['id']:<6} SKU:{u['sku']:<15} "
              f"{u['old_stock']:>4}->{u['new_stock']:>4} ({signo}{diff}) "
              f"{u['new_status']:<10}{price_tag}", end="")

        pid = u["id"]

        # Update stock per sucursal for this product (always, regardless of total stock change)
        sku = u["sku"]
        has_sucursal_data = False
        if sku in sucursal_stock:
            for aid, sqty in sorted(sucursal_stock[sku].items()):
                mkey = SUCURSALES[aid]["meta_key"]
                needs_wp.append(f"post meta update {pid} {mkey} {sqty}")
            avail = [str(aid) for aid in sorted(SUCURSALES.keys())
                     if sucursal_stock[sku].get(aid, 0) > 0]
            needs_wp.append(f"post meta update {pid} _sucursales_disponibles " + (",".join(avail) if avail else ""))
            has_sucursal_data = True

        if not needs_update and not has_sucursal_data:
            print(" -> SIN CAMBIOS")
            continue

        if needs_update:
            needs_wp.append(f"post meta update {pid} _stock {u['new_stock']}")
            needs_wp.append(f"post meta update {pid} _stock_status {u['new_status']}")
            if not u["manage"] or u["manage"] == "parent":
                needs_wp.append(f"post meta update {pid} _manage_stock yes")
            if price_changed:
                needs_wp.append(f"post meta update {pid} _regular_price {u['new_price']}")

        if dry_run:
            status_parts = []
            if stock_changed:
                status_parts.append('outofstock' if u['new_stock'] <= STOCK_LIMIT else 'instock')
            if has_sucursal_data:
                status_parts.append('sucursales')
            if price_changed:
                status_parts.append('precio')
            print(f" -> {' + '.join(status_parts) if status_parts else 'solo sucursal'} (dry)")
        else:
            print(" -> pendiente")

    cambios_df = pd.DataFrame([u for u in updates if u["new_stock"] != u["old_stock"] or u["new_status"] != u["old_status"]])
    if not cambios_df.empty:
        cambios_df["tipo_cambio"] = cambios_df.apply(
            lambda r: "solo_status" if r["new_stock"] == r["old_stock"] else "stock+/-", axis=1)
        cambios_df.to_csv(os.path.join(carpeta, "cambios.csv"), index=False)
        cambios_stock = cambios_df[cambios_df["new_stock"] != cambios_df["old_stock"]]
        if not cambios_stock.empty:
            cambios_stock.to_csv(os.path.join(carpeta, "cambios_stock.csv"), index=False)
            print(f"  Cambios reales guardados: {len(cambios_df)} en cambios.csv ({len(cambios_stock)} con cambio de stock en cambios_stock.csv)")

    if price_updates:
        precios_df = pd.DataFrame(price_updates)
        precios_df.to_csv(os.path.join(carpeta, "cambios_precios.csv"), index=False)
        print(f"  Cambios de precio: {len(price_updates)} en cambios_precios.csv")

    if dry_run:
        cambios = sum(1 for u in updates if u["new_stock"] != u["old_stock"])
        print(f"\n  Con cambio stock: {cambios}  Solo status: {len(updates)-cambios}")
        print(f"  Comandos WP-CLI generados: {len(needs_wp)}")
    else:
        # Generate bulk PHP script for all updates (single wp eval-file call)
        php = "<?php\n"
        for u in updates:
            if u['new_stock'] != u['old_stock'] or u['new_status'] != u['old_status'] or u['sku'] in sucursal_stock:
                pid = int(u['id'])
                sku = u['sku']
                php += f"update_post_meta({pid},'_stock',{int(u['new_stock'])});\n"
                php += f"update_post_meta({pid},'_stock_status','{u['new_status']}');\n"
                if not u.get('manage') or u['manage'] == 'parent':
                    php += f"update_post_meta({pid},'_manage_stock','yes');\n"
                if u['new_price'] is not None and u['old_price'] is not None and sku not in PRICE_EXCLUDE_SKUS:
                    try:
                        if abs(float(u['old_price']) - float(u['new_price'])) > 0.01:
                            php += f"update_post_meta({pid},'_regular_price','{u['new_price']}');\n"
                    except:
                        pass
                # Sucursal stock
                if sku in sucursal_stock:
                    for aid, sqty in sorted(sucursal_stock[sku].items()):
                        mkey = SUCURSALES[aid]['meta_key']
                        php += f"update_post_meta({pid},'{mkey}',{sqty});\n"
                    avail = ','.join(str(aid) for aid in sorted(SUCURSALES.keys())
                                     if sucursal_stock[sku].get(aid, 0) > 0)
                    php += f"update_post_meta({pid},'_sucursales_disponibles','{avail}');\n"
                php += "echo 'OK';\n"
        php += "echo 'DONE';\n"

        php_path = os.path.join(carpeta, "bulk_update.php")
        with open(php_path, 'w', encoding='utf-8') as f:
            f.write(php)

        print(f"\n  Ejecutando bulk update via wp eval-file...")
        out = run_wp(f'eval-file {php_path}', timeout=300)
        ok = out.count("OK")
        fail = len([u for u in updates if u['new_stock'] != u['old_stock'] or u['new_status'] != u['old_status'] or u['sku'] in sucursal_stock]) - ok
        print(f"  OK: {ok}  Fallidos: {max(0, fail)}")
        if out.strip():
            print(f"  Ultima linea: {out.strip().split(chr(10))[-1]}")

        print("\n--- VERIFICACION ---", flush=True)
        wc_json2 = get_wc_export(suffix="_2")
        if wc_json2 is None:
            disc = ok + fail
            print(f"  No se pudo verificar. {ok} OK, {fail} fail")
            df_result = pd.DataFrame(updates)
            df_result.to_csv(os.path.join(carpeta, "reporte_actualizacion.csv"), index=False)
            os.remove(wc_json)
            git_commit_and_push(carpeta, ok, fail, disc)
            return
        with open(wc_json2, encoding='utf-8') as f:
            wc2 = json.load(f)
        wc2_by_sku = {}
        for p in wc2:
            sku = p["sku"].strip() if p["sku"] else ""
            if sku:
                wc2_by_sku[sku] = p

        disc = 0
        disc_precio = 0
        for u in updates:
            wc2_p = wc2_by_sku.get(u["sku"])
            if not wc2_p:
                continue
            actual = wc2_p["stock_qty"] if wc2_p["stock_qty"] is not None else 0
            if actual != u["new_stock"]:
                disc += 1
                print(f"  DISCREPANCIA stock: {u['sku']} esperado={u['new_stock']} actual={actual}")
            # Verify price (skip excluded SKUs)
            if update_prices and u["sku"] not in PRICE_EXCLUDE_SKUS and u["new_price"] is not None:
                actual_price = wc2_p.get("regular_price")
                if actual_price is not None:
                    try:
                        if abs(float(actual_price) - float(u["new_price"])) > 0.01:
                            disc_precio += 1
                            print(f"  DISCREPANCIA precio: {u['sku']} esperado={u['new_price']} actual={actual_price}")
                    except:
                        pass

        if disc == 0 and disc_precio == 0:
            print("  Todas las actualizaciones verificadas correctamente.")
        else:
            if disc > 0:
                print(f"  {disc} discrepancias de stock encontradas.")
            if disc_precio > 0:
                print(f"  {disc_precio} discrepancias de precio encontradas.")

        df_result = pd.DataFrame(updates)
        if not df_result.empty:
            df_result["aplicado"] = df_result.apply(
                lambda r: "si" if (r["new_stock"] != r["old_stock"] or r["new_stock"] <= STOCK_LIMIT) else "no_omitido",
                axis=1
            )
        df_result.to_csv(os.path.join(carpeta, "reporte_actualizacion.csv"), index=False)
        with open(os.path.join(carpeta, "verificacion.txt"), "w") as f:
            f.write(f"Discrepancias: {disc}\n")
            f.write(f"OK: {ok}  Fallidos: {fail}\n")
        os.remove(wc_json2)

    os.remove(wc_json)
    print(f"\nArchivos en: {carpeta}")
    for f in sorted(os.listdir(carpeta)):
        print(f"  - {f}")

    if not dry_run:
        git_commit_and_push(carpeta, ok, fail, disc)

if __name__ == "__main__":
    main()
