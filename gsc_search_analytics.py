import os
import sys
import json
import io
sys.stdout = io.TextIOWrapper(sys.stdout.buffer, encoding='utf-8', errors='replace')
from google.auth.transport.requests import Request
from google.oauth2.credentials import Credentials
from googleapiclient.discovery import build

SCOPES = ['https://www.googleapis.com/auth/webmasters']
SITE_DOMAIN = 'sc-domain:suplementospanama.net'
base = os.path.dirname(os.path.abspath(__file__))
TOKEN_FILE = os.path.join(base, 'token.json')

creds = Credentials.from_authorized_user_file(TOKEN_FILE, SCOPES)
service = build('searchconsole', 'v1', credentials=creds)
print("GSC conectado\n", flush=True)

def query_gsc(start_date, end_date, dimension, row_limit=15):
    body = {
        'startDate': start_date,
        'endDate': end_date,
        'dimensions': [dimension],
        'rowLimit': row_limit
    }
    resp = service.searchanalytics().query(siteUrl=SITE_DOMAIN, body=body).execute()
    return resp.get('rows', [])

print("=== TOP QUERIES (últimos 90 días) ===")
rows = query_gsc('2026-04-16', '2026-07-14', 'query')
print(f"{'Pos':<4} {'Query':<45} {'Clics':<7} {'Impr.':<8} {'CTR':<7} {'Pos':<5}")
print("-"*76)
for i, r in enumerate(rows[:15]):
    q = r['keys'][0][:44]
    clics = r['clicks']
    impr = r['impressions']
    ctr = f"{r.get('ctr', 0)*100:.1f}%"
    pos = f"{r.get('averagePosition', 0):.1f}"
    print(f"{i+1:<4} {q:<45} {clics:<7} {impr:<8} {ctr:<7} {pos:<5}")

print("\n=== TOP PAGES (últimos 90 días) ===")
rows2 = query_gsc('2026-04-16', '2026-07-14', 'page')
print(f"{'Pos':<4} {'Page':<55} {'Clics':<7} {'Impr.':<8} {'CTR':<7} {'Pos':<5}")
print("-"*84)
for i, r in enumerate(rows2[:15]):
    p = r['keys'][0][:54]
    clics = r['clicks']
    impr = r['impressions']
    ctr = f"{r.get('ctr', 0)*100:.1f}%"
    pos = f"{r.get('averagePosition', 0):.1f}"
    print(f"{i+1:<4} {p:<55} {clics:<7} {impr:<8} {ctr:<7} {pos:<5}")

print("\n=== QUERIES POR CATEGORÍA (creatina, proteina, whey, etc.) ===")
cats = ['creatina', 'proteina', 'whey', 'suplementos', 'vitaminas', 'quemador', 'pre entreno']
for cat in cats:
    body = {
        'startDate': '2026-04-16',
        'endDate': '2026-07-14',
        'dimensions': ['query'],
        'dimensionFilterGroups': [{
            'groupType': 'and',
            'filters': [{
                'dimension': 'query',
                'operator': 'contains',
                'expression': cat
            }]
        }],
        'rowLimit': 5
    }
    resp = service.searchanalytics().query(siteUrl=SITE_DOMAIN, body=body).execute()
    cat_rows = resp.get('rows', [])
    total_clics = sum(r['clicks'] for r in cat_rows)
    total_impr = sum(r['impressions'] for r in cat_rows)
    avg_pos = sum(r.get('averagePosition', 0) * r['impressions'] for r in cat_rows) / total_impr if total_impr else 0
    print(f"\n--- {cat.upper()} ({total_clics} clics, {total_impr} impr, pos {avg_pos:.1f}) ---")
    for r in cat_rows[:3]:
        print(f"  {r['keys'][0][:50]:<52} {r['clicks']:<5} clics  {r['impressions']:<6} impr  pos {r.get('averagePosition', 0):.1f}")

print("\n=== TREND MENSUAL (últimos 3 meses) ===")
for m in ['2026-05', '2026-06', '2026-07']:
    sd = f"{m}-01"
    ed = f"{m}-28" if m == '2026-07' else f"{m}-30"
    body = {
        'startDate': sd,
        'endDate': ed,
        'dimensions': ['query'],
        'rowLimit': 1
    }
    resp = service.searchanalytics().query(siteUrl=SITE_DOMAIN, body=body).execute()
    rows_all = resp.get('rows', [])
    clics = sum(r['clicks'] for r in rows_all)
    impr = sum(r['impressions'] for r in rows_all)
    print(f"  {m}: {clics:>5} clics  {impr:>6} impresiones")

print("\n✅ Listo")
