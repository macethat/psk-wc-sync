import json
from gsc_query import get_service, SITE_URL

svc = get_service()

body = {
    'startDate': '2023-01-01',
    'endDate': '2026-07-13',
    'dimensions': ['page'],
    'rowLimit': 25000,
}

print("Consultando páginas indexadas en GSC (Search Analytics)...", flush=True)
result = svc.searchanalytics().query(siteUrl=SITE_URL, body=body).execute()
rows = result.get('rows', [])
print(f"Total páginas con datos: {len(rows)}", flush=True)

with open('gsc_pages.json', 'w', encoding='utf-8') as f:
    json.dump(rows, f, indent=2, ensure_ascii=False)
print("Guardado en gsc_pages.json", flush=True)

for i, r in enumerate(rows[:15]):
    url = r["keys"][0]
    imp = r["impressions"]
    clicks = r["clicks"]
    pos = r["position"]
    print(f'{i+1}. {url} - {imp} imp, {clicks} clics, pos {pos:.1f}')
