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
    conn.request('GET', f'/Api/Articulos?pin={PSK_PIN}&pagina=0&cant_pagina=99999',
                 headers={'clave-api-business': PSK_API_KEY})
    r = conn.getresponse()
    if r.status != 200:
        print(f"ERROR: API respondio con status {r.status}")
        sys.exit(1)
    data = json.loads(r.read().decode())
    if not isinstance(data, list):
        print(f"ERROR: Respuesta inesperada de API: {data}")
        sys.exit(1)
    rows = []
    for a in data:
        cod = a.get('codigo', '')
        nom = a.get('nombre', '')
        ext = a.get('existencias', '0')
        try:
            ext = int(float(ext))
        except:
            ext = 0
        rows.append({"Codigo": cod, "Nombre": nom, "Cant.Total": ext})
    df = pd.DataFrame(rows)
    df["Codigo"] = df["Codigo"].str.strip()
    print(f"  Extraidos {len(df)} articulos desde PSK Cloud API")
    return df

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

    print("=== ACTUALIZACION DIARIA DE STOCK (SiteGround) ===")
    print(f"Fecha: {datetime.now().strftime('%d/%m/%Y %H:%M')}\n")

    fec = datetime.strptime(fecha_arg, "%d-%m-%Y") if fecha_arg else datetime.now()
    carpeta = os.path.join(CARPETA_BASE, f"update_{fec.strftime('%d-%m-%Y')}")
    os.makedirs(carpeta, exist_ok=True)

    df_inv = fetch_from_psk_api()
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
        rows_comp.append({
            "sku": sku, "nombre": inv_row.get("Nombre", wc_p.get("name", "")),
            "tipo": wc_p["type"], "wc_stock": old_s, "wc_status": wc_p["stock_st"],
            "inv_stock": ns, "nuevo_status": ns_status, "diferencia": diff,
            "cambiara": "SI" if (diff != 0 or wc_p["stock_st"] != ns_status) else "no"
        })
    for sku in (sku_wc - cod_inv):
        wc_p = wc_by_sku[sku]
        old_s = wc_p["stock_qty"] if wc_p["stock_qty"] is not None else 0
        if old_s <= STOCK_LIMIT:
            rows_comp.append({
                "sku": sku, "nombre": wc_p.get("name", ""),
                "tipo": wc_p["type"], "wc_stock": old_s, "wc_status": wc_p["stock_st"],
                "inv_stock": "(solo WC)", "nuevo_status": "outofstock", "diferencia": "-",
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
        updates.append({
            "id": wc_p["id"], "parent": wc_p["parent"],
            "tipo": wc_p["type"], "sku": sku,
            "nombre": inv_row.get("Nombre", wc_p.get("name", "")),
            "old_stock": old_stock, "new_stock": ns,
            "new_status": "outofstock" if ns <= STOCK_LIMIT else "instock",
            "old_status": wc_p["stock_st"],
            "manage": wc_p["manage"],
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
            })

    df_prev = pd.DataFrame(updates)
    df_prev.to_csv(os.path.join(carpeta, "reporte_preview.csv"), index=False)
    print(f"\n{'='*70}")
    print(f"  {'SIMULACION' if dry_run else 'ACTUALIZACION EN VIVO'}")
    print(f"  Total a procesar: {len(updates)} productos")
    print(f"  Coincidencias: {len(coinciden)} | Solo WC (low stock): {len(updates)-len(coinciden)}")
    print(f"{'='*70}")

    needs_wp = []
    for u in updates:
        diff = u["new_stock"] - u["old_stock"]
        signo = "+" if diff > 0 else ""
        needs_update = (diff != 0) or (u["new_status"] != u["old_status"])

        print(f"  {u['tipo']:<9} ID:{u['id']:<6} SKU:{u['sku']:<15} "
              f"{u['old_stock']:>4}->{u['new_stock']:>4} ({signo}{diff}) "
              f"{u['new_status']:<10}", end="")

        if not needs_update:
            print(" -> SIN CAMBIOS")
            continue

        pid = u["id"]
        needs_wp.append(f"post meta update {pid} _stock {u['new_stock']}")
        needs_wp.append(f"post meta update {pid} _stock_status {u['new_status']}")
        if not u["manage"] or u["manage"] == "parent":
            needs_wp.append(f"post meta update {pid} _manage_stock yes")

        if dry_run:
            print(f" -> {'outofstock' if u['new_stock'] <= STOCK_LIMIT else 'instock'} (dry)")
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

    if dry_run:
        cambios = sum(1 for u in updates if u["new_stock"] != u["old_stock"])
        print(f"\n  Con cambio stock: {cambios}  Solo status: {len(updates)-cambios}")
        print(f"  Comandos WP-CLI generados: {len(needs_wp)}")
    else:
        print(f"\n  Ejecutando {len(needs_wp)} comandos WP-CLI...")
        ok = fail = 0
        for i, cmd in enumerate(needs_wp, 1):
            out = run_wp(cmd, timeout=60)
            sys.stdout.write(f"\r  [{i}/{len(needs_wp)}] ")
            sys.stdout.flush()
            if out and "Success:" in out:
                ok += 1
            else:
                fail += 1
        sys.stdout.write("\n")
        sys.stdout.flush()

        print(f"\n  OK: {ok}  Fallidos: {fail}")

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
        for u in updates:
            wc2_p = wc2_by_sku.get(u["sku"])
            if not wc2_p:
                continue
            actual = wc2_p["stock_qty"] if wc2_p["stock_qty"] is not None else 0
            if actual != u["new_stock"]:
                disc += 1
                print(f"  DISCREPANCIA: {u['sku']} esperado={u['new_stock']} actual={actual}")

        if disc == 0:
            print("  Todas las actualizaciones verificadas correctamente.")
        else:
            print(f"  {disc} discrepancias encontradas.")

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
