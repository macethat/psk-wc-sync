import os
import sys
import json
import time
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build
from googleapiclient.errors import HttpError

SCOPES = ['https://www.googleapis.com/auth/webmasters']
SITE_DOMAIN = 'sc-domain:suplementospanama.net'
base = os.path.dirname(os.path.abspath(__file__))
TOKEN_FILE = os.path.join(base, 'token.json')

print("Cargando productos...", flush=True)
with open(os.path.join(base, 'products.json'), 'r', encoding='utf-8') as f:
    products = json.load(f)
print(f"Total: {len(products)}", flush=True)

creds = Credentials.from_authorized_user_file(TOKEN_FILE, SCOPES)
service = build('searchconsole', 'v1', credentials=creds)
print("GSC conectado", flush=True)

results = []
total = len(products)
for i, p in enumerate(products):
    url = p['url'].rstrip('/')
    try:
        body = {'inspectionUrl': url, 'siteUrl': SITE_DOMAIN}
        resp = service.urlInspection().index().inspect(body=body).execute()
        result = resp.get('inspectionResult', {})
        idx = result.get('indexStatusResult', {})
        verdict = idx.get('verdict', 'N/A')
        coverage = idx.get('coverageState', 'N/A')
        p['indexed'] = verdict == 'PASS'
        p['coverage'] = coverage
    except Exception as e:
        p['indexed'] = False
        p['coverage'] = f"ERROR: {e}"
        print(f"  ERROR {i+1}/{total}: {e}", flush=True)
    
    results.append(p)
    status = "OK" if p['indexed'] else "NO"
    print(f"  {i+1}/{total} [{status}] {p['type']:8s} {p['name'][:50]}", flush=True)
    
    if (i + 1) % 15 == 0 and i + 1 < total:
        time.sleep(2)

not_indexed = [p for p in results if not p['indexed']]

print(f"\n{'='*60}", flush=True)
print(f"INDEXADOS: {len(results) - len(not_indexed)} de {len(results)}", flush=True)
print(f"NO INDEXADOS: {len(not_indexed)}", flush=True)

if not_indexed:
    print(f"\nPOR CATEGORIA:", flush=True)
    by_cat = {}
    for p in not_indexed:
        for cat in p['categories'].split(','):
            c = cat.strip()
            if c:
                by_cat.setdefault(c, []).append(p)
    for cat in sorted(by_cat.keys()):
        items = by_cat[cat]
        print(f"\n  {cat.upper()} ({len(items)}):", flush=True)
        for p in items:
            print(f"    [{p['type']}] {p['name']} | {p['url']} | Coverage: {p.get('coverage','')}", flush=True)
    
    csv_path = os.path.join(base, 'productos_no_indexados.csv')
    with open(csv_path, 'w', encoding='utf-8') as f:
        f.write("ID,Name,SKU,URL,Categories,Type,Stock,Coverage\n")
        for p in not_indexed:
            f.write(f"{p['id']},\"{p['name']}\",{p['sku']},{p['url']},\"{p['categories']}\",{p['type']},{p['stock_status']},{p.get('coverage','')}\n")
    print(f"\nCSV: {csv_path}", flush=True)
