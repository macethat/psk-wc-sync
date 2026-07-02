#!/usr/bin/env python3
import os, sys, csv, subprocess, json
from datetime import datetime
import http.client
sys.stdout.reconfigure(encoding='utf-8')

CARPETA_BASE = os.path.dirname(os.path.abspath(__file__))
STOCK_LIMIT = 6
WP_DIR = os.path.expanduser("~/www/suplementospanama.net/public_html")
EXPORT_PHP = os.path.expanduser("~/wc_export_ssh.php")
WP_PATH = os.path.expanduser("~/www/suplementospanama.net/public_html")

PSK_PIN = "46558"
PSK_API_KEY = "BQxQrt5/FwARtlVUwT0GFw=="
PSK_API_HOST = "adm.premium-soft.com"

def run_wp(cmd, timeout=60):
    full = f"wp --user=Fersho --path={WP_PATH} {cmd}"
    r = subprocess.run(full, shell=True, capture_output=True, timeout=timeout)
    if r.returncode != 0:
        err = r.stderr.decode('utf-8', errors='replace').strip()
        print(f"  ERROR WP-CLI ({r.returncode}): {err[:200]}")
        return ""
    return r.stdout.decode('utf-8', errors='replace').strip()

def get_wc_export(suffix=""):
    print("Exportando productos localmente...")
    out = run_wp(f'eval-file {EXPORT_PHP}')
    local = os.path.join(CARPETA_BASE, f"tmp_wc_export{suffix}.json")
    with open(local, 'w', encoding='utf-8') as f:
        f.write(out)
    if not out:
        print("  ERROR: salida vacia de WP-CLI")
        return None
    try:
        data = json.loads(out)
        print(f"  Exportados: {len(data)} productos")
    except json.JSONDecodeError as e:
        print(f"  ERROR: JSON invalido - {e}")
        print(f"  Primeros 200 chars: {out[:200]}")
        return None
    return local

def fetch_from_psk_api():
    print("Extrayendo inventario desde PSK Cloud API...")
    conn = http.client.HTTPSConnection(PSK_API_HOST)
    conn.request('GET', f'/Api/Articulos?pin={PSK_PIN}&pagina=0&cant_pagina=99999',
                 headers={'clave-api-business': PSK_API_KEY})
    r = conn.getresponse()
    if r.status != 200:
        print(f"ERROR: API respondio con status {r.status}")
        sys.exit(1)
    data = json.loads(r.read().decode('utf-8'))
    print(f"  Extraidos {len(data)} articulos desde PSK Cloud API")
    articulos = {}
    for a in data:
        cod = a.get('codigo', '').strip()
        nom = a.get('nombre', '').strip()
        try:
            cant = int(float(str(a.get('existencias', '0'))))
        except:
            cant = 0
        if cod:
            articulos[cod] = {'nombre': nom, 'cantidad': cant}
    return articulos

def check_salidas():
    try:
        conn = http.client.HTTPSConnection(PSK_API_HOST)
        conn.request('GET', f'/Api/Salidas?pin={PSK_PIN}&pagina=0&cant_pagina=99999',
                     headers={'clave-api-business': PSK_API_KEY})
        r = conn.getresponse()
        if r.status == 200:
            return json.loads(r.read().decode('utf-8'))
    except:
        pass
    return []

def main():
    import argparse
    parser = argparse.ArgumentParser()
    parser.add_argument('--live', action='store_true', help='Ejecutar actualizacion real (no solo preview)')
    parser.add_argument('--api', action='store_true', help='Usar API PSK en vez de CSV local')
    parser.add_argument('--fecha', help='Fecha especifica DD-MM-YYYY')
    args = parser.parse_args()
    fecha_arg = args.fecha
    fec = datetime.strptime(fecha_arg, "%d-%m-%Y") if fecha_arg else datetime.now()
    carpeta = os.path.join(CARPETA_BASE, f"update_{fec.strftime('%d-%m-%Y')}")
    os.makedirs(carpeta, exist_ok=True)

    print("=" * 62)
    print(f"=== ACTUALIZACION DIARIA DE STOCK (SiteGround) ===")
    print(f"Fecha: {fec.strftime('%d/%m/%Y %H:%M')}")
    print()

    if args.api:
        articulos = fetch_from_psk_api()
    else:
        csv_path = os.path.join(CARPETA_BASE, "ListaInvFisic.csv")
        print(f"Leyendo inventario fisico desde {csv_path} ...")
        if not os.path.exists(csv_path):
            print(f"ERROR: No se encuentra {csv_path}")
            sys.exit(1)
        with open(csv_path, encoding='utf-8') as f:
            reader = csv.DictReader(f)
            articulos = {r['Codigo'].strip(): {'nombre': r['Nombre'].strip(), 'cantidad': int(r['Cant.Total'])} for r in reader}
        print(f"  Leidos {len(articulos)} productos del CSV")

    df_inv = [{'codigo': k, 'nombre': v['nombre'], 'stock_fisico': v['cantidad'], 'stock_psk': v['cantidad']} for k, v in sorted(articulos.items())]
    with open(os.path.join(carpeta, "ListaInvFisic.csv"), 'w', newline='', encoding='utf-8') as f:
        w = csv.DictWriter(f, fieldnames=['codigo', 'nombre', 'stock_fisico', 'stock_psk'])
        w.writeheader()
        w.writerows(df_inv)

    wc_json = get_wc_export(suffix="_1")
    if wc_json is None:
        sys.exit(1)
    wc_path = os.path.join(carpeta, "wc_export.json")
    with open(wc_path, 'w', encoding='utf-8') as f:
        json.dump(json.load(open(wc_json, encoding='utf-8')), f, ensure_ascii=False)

    with open(wc_json, encoding='utf-8') as f:
        wc_prods = json.load(f)
    wc_idx = {}
    for p in wc_prods:
        sku = p.get('sku', '').strip().upper()
        if sku:
            wc_idx[sku] = p
    total_wc = len(wc_prods)

    comp = []
    for cod, info in sorted(articulos.items()):
        sku_up = cod.upper().strip()
        wc = wc_idx.get(sku_up, {})
        wc_id = wc.get('id', '')
        wc_sku = wc.get('sku', '')
        wc_stock = wc.get('stock_qty')
        if wc_stock is None:
            wc_stock_str = wc.get('stock_st', '')
        else:
            wc_stock_str = str(wc_stock)
        wc_type = wc.get('type', '')
        wc_parent = wc.get('parent', 0)
        wc_name = wc.get('name', '')
        delta = (info['cantidad'] - (wc_stock if isinstance(wc_stock, (int, float)) else 0)) if isinstance(wc_stock, (int, float)) else ''
        comp.append({
            'codigo': cod,
            'nombre_psk': info['nombre'],
            'nombre_wc': wc_name,
            'wc_id': wc_id,
            'wc_sku': wc_sku,
            'wc_type': wc_type,
            'wc_parent': wc_parent,
            'stock_psk': info['cantidad'],
            'stock_wc': wc_stock_str,
            'delta': delta,
        })

    solo_psk = [c for c in comp if not c['wc_id']]
    coinciden = [c for c in comp if c['wc_id']]
    print(f"\n=== CRUCE: {len(coinciden)} coincidencias, {len(solo_psk)} solo PSK, {total_wc - len(coinciden)} solo WC ===")

    with open(os.path.join(carpeta, "comparativa_previa.csv"), 'w', newline='', encoding='utf-8') as f:
        w = csv.DictWriter(f, fieldnames=comp[0].keys())
        w.writeheader()
        w.writerows(comp)

    cambios = [c for c in comp if c['wc_id'] and isinstance(c['delta'], (int, float)) and c['delta'] != 0]
    cambios_stock = [c for c in cambios if c['wc_type'] in ('simple', 'variation') and c['delta'] != 0]
    preview = [{'codigo': c['codigo'], 'nombre': c['nombre_psk'], 'stock_actual': c['stock_wc'], 'stock_nuevo': c['stock_psk'], 'diferencia': c['delta']} for c in cambios]

    with open(os.path.join(carpeta, "reporte_preview.csv"), 'w', newline='', encoding='utf-8') as f:
        w = csv.DictWriter(f, fieldnames=['codigo', 'nombre', 'stock_actual', 'stock_nuevo', 'diferencia'])
        w.writeheader()
        w.writerows(preview)

    print(f"\n=== PREVIEW ===")
    print(f"  Productos con cambios: {len(cambios)}")
    print(f"  Cambios de stock (simples/variations): {len(cambios_stock)}")
    if cambios:
        print(f"  Ejemplos:")
        for c in cambios[:10]:
            print(f"    {c['codigo']}: {c['stock_wc']} -> {c['stock_psk']} ({c['delta']:+d})")

    if not args.live:
        print(f"\nSolo preview. Para ejecutar usar --live")
        guardar_y_salir(carpeta, args)
        return

    print(f"\n=== EJECUTANDO CAMBIOS ({len(cambios_stock)} actualizaciones) ===")
    ok, fail, disc = 0, 0, 0
    resultados = []
    for c in cambios_stock:
        pid = c['wc_id']
        nuevo_stock = max(0, c['stock_psk'])
        try:
            r = run_wp(f'post meta update {pid} _stock {nuevo_stock}')
            if r == '' or 'Success' in r:
                new_status = 'instock' if nuevo_stock > STOCK_LIMIT else 'outofstock'
                run_wp(f'post meta update {pid} _stock_status {new_status}')
                ok += 1
                resultados.append({'codigo': c['codigo'],'nombre': c['nombre_psk'],'wc_id': pid,'stock_anterior': c['stock_wc'],'stock_nuevo': nuevo_stock,'status_nuevo': new_status,'resultado': 'OK'})
                print(f"  OK #{pid} {c['codigo']}: {c['stock_wc']} -> {nuevo_stock} ({new_status})")
            else:
                fail += 1
                resultados.append({'codigo': c['codigo'],'nombre': c['nombre_psk'],'wc_id': pid,'stock_anterior': c['stock_wc'],'stock_nuevo': nuevo_stock,'status_nuevo': '','resultado': f'FAIL: {r[:100]}'})
                print(f"  FAIL #{pid} {c['codigo']}: {r[:100]}")
        except subprocess.TimeoutExpired:
            fail += 1
            resultados.append({'codigo': c['codigo'],'nombre': c['nombre_psk'],'wc_id': pid,'stock_anterior': c['stock_wc'],'stock_nuevo': nuevo_stock,'status_nuevo': '','resultado': 'TIMEOUT'})
            print(f"  TIMEOUT #{pid} {c['codigo']}")
        except Exception as e:
            fail += 1
            resultados.append({'codigo': c['codigo'],'nombre': c['nombre_psk'],'wc_id': pid,'stock_anterior': c['stock_wc'],'stock_nuevo': nuevo_stock,'status_nuevo': '','resultado': str(e)[:100]})
            print(f"  ERROR #{pid} {c['codigo']}: {e}")

    if resultados:
        with open(os.path.join(carpeta, "cambios.csv"), 'w', newline='', encoding='utf-8') as f:
            w = csv.DictWriter(f, fieldnames=resultados[0].keys())
            w.writeheader()
            w.writerows(resultados)

    with open(os.path.join(carpeta, "cambios_stock.csv"), 'w', newline='', encoding='utf-8') as f:
        w = csv.DictWriter(f, fieldnames=['codigo', 'nombre', 'stock_actual', 'stock_nuevo', 'diferencia'])
        w.writeheader()
        w.writerows(preview)

    print(f"\n=== VERIFICACION ===")
    wc_json2 = get_wc_export(suffix="_2")
    if wc_json2 is None:
        disc = ok + fail
        guardar_y_salir(carpeta, ok, fail, disc)
        return
    with open(wc_json2, encoding='utf-8') as f:
        wc2 = json.load(f)
    wc2_idx = {}
    for p in wc2:
        sku = p.get('sku', '').strip().upper()
        if sku:
            wc2_idx[sku] = p
    ver_ok, ver_fail = 0, 0
    resultados_v = []
    for c in cambios_stock:
        sku_up = c['codigo'].upper().strip()
        w2 = wc2_idx.get(sku_up, {})
        w2_stock = w2.get('stock_qty')
        esperado = max(0, c['stock_psk'])
        if w2_stock == esperado:
            ver_ok += 1
        else:
            ver_fail += 1
        resultados_v.append({'codigo': c['codigo'], 'nombre': c['nombre_psk'], 'esperado': esperado, 'real': w2_stock, 'estado': 'OK' if w2_stock == esperado else 'FAIL'})

    if resultados_v:
        with open(os.path.join(carpeta, "verificacion.csv"), 'w', newline='', encoding='utf-8') as f:
            w = csv.DictWriter(f, fieldnames=resultados_v[0].keys())
            w.writeheader()
            w.writerows(resultados_v)

    with open(os.path.join(carpeta, "reporte_actualizacion.csv"), 'w', newline='', encoding='utf-8') as f:
        w = csv.DictWriter(f, fieldnames=resultados[0].keys())
        w.writeheader()
        w.writerows(resultados)

    with open(os.path.join(carpeta, "verificacion.txt"), 'w') as f:
        f.write(f"Verificacion: {ver_ok} OK, {ver_fail} fail\n")

    print(f"\n  Verificacion: {ver_ok} OK, {ver_fail} fail")
    print(f"\nArchivos en: {carpeta}")
    for fn in sorted(os.listdir(carpeta)):
        fp = os.path.join(carpeta, fn)
        print(f"  {fn} ({os.path.getsize(fp)} bytes)")
    print(f"\nResumen: {ok} OK, {fail} fail, {disc} disc")
    guardar_y_salir(carpeta, ok, fail, disc)

def guardar_y_salir(carpeta, *args):
    try:
        repo_dir = CARPETA_BASE
        env = os.environ.copy()
        git_key = os.path.join(CARPETA_BASE, "github-key-nopass")
        env['GIT_SSH_COMMAND'] = f"ssh -i {git_key} -o StrictHostKeyChecking=no"
        subprocess.run(["git", "add", carpeta], cwd=repo_dir, capture_output=True, env=env)
        msg = f"update {os.path.basename(carpeta).replace('update_', '')}: {args[0] if args else '?'} OK"
        subprocess.run(["git", "commit", "-m", msg], cwd=repo_dir, capture_output=True, env=env)
        r = subprocess.run(["git", "push", "origin", "master"], cwd=repo_dir, capture_output=True, timeout=30, env=env)
        if r.returncode == 0:
            print(f"  Git push OK")
        else:
            print(f"  Git push: {r.stderr.decode()[:200]}")
    except Exception as e:
        print(f"  Git: {e}")
    sys.exit(0)

if __name__ == '__main__':
    main()
