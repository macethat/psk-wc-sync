---
tool: Apify
plan: Gratuito ($5/mes credito)
token: configurado en `psk-create-product/apify_config.json` (local, no subido a git)
estado: Operacional
---

# Integracion Apify

Usamos la API REST de Apify con autenticacion Bearer token.

## Autenticacion

Token guardado en `C:\suplementos\psk-create-product\apify_config.json`:

```json
{"api_token": "apfy_api_...", "base_url": "https://api.apify.com/v2"}
```

Usar header `Authorization: Bearer {token}` en todas las requests.

**Importante**: No usar query param `?token=`, solo Bearer header.

## API Usage

- Actors store: `GET /store` (listar actores publicos)
- Actor info: `GET /acts/{owner}~{name}` (nota: tilde `~`, no slash `/`)
- Run actor: `POST /acts/{owner}~{name}/runs` con input en body
- Check run: `GET /acts/{owner}~{name}/runs/{runId}`
- Get results: `GET /datasets/{datasetId}/items`

## Helper Python (urllib nativo, sin dependencias)

Archivo: `C:\suplementos\psk-create-product\apify_client.py`

```python
import urllib.request, json, time

def load_config():
    import os
    cfg = os.path.join(os.path.dirname(__file__), 'apify_config.json')
    with open(cfg) as f:
        return json.load(f)['api_token']

TOKEN = load_config()
BASE = 'https://api.apify.com/v2'
HEADERS = {'Authorization': f'Bearer {TOKEN}', 'Content-Type': 'application/json'}

def _req(method, path, data=None):
    url = f'{BASE}{path}'
    payload = json.dumps(data).encode() if data else None
    req = urllib.request.Request(url, data=payload, headers=HEADERS, method=method)
    return json.loads(urllib.request.urlopen(req).read())

def get(path):
    return _req('GET', path)

def post(path, data):
    return _req('POST', path, data)

def run_and_wait(actor_id, run_input, poll_sec=3):
    """Run actor and wait for completion. Returns dataset items."""
    run = post(f'/acts/{actor_id}/runs', run_input)
    rid = run['data']['id']
    while True:
        time.sleep(poll_sec)
        r = get(f'/acts/{actor_id}/runs/{rid}')
        st = r['data']['status']
        if st == 'SUCCEEDED':
            did = r['data']['defaultDatasetId']
            return get(f'/datasets/{did}/items')
        elif st in ('FAILED', 'ABORTED', 'TIMED-OUT'):
            raise Exception(f'Run {rid} {st}: {r["data"].get("errorMessage","?")}')

def google_search(query, count=5):
    """Search Google via Apify. Returns list of organic results (title, url, snippet)."""
    items = run_and_wait('apify~google-search-scraper', {
        'queries': query,
        'resultsPerPage': count,
        'maxPagesPerQuery': 1,
        'maxConcurrency': 1,
        'mobileResults': False,
        'saveHtml': False,
        'includeUnfilteredResults': False
    })
    results = []
    for it in items:
        results.extend(it.get('organicResults', []))
    return results
```

## Actores recomendados

| Actor | ID | Permisos | Uso |
|-------|----|----------|-----|
| Google Search Scraper | `apify~google-search-scraper` | LIMITED | Buscar product info en Google |
| Website Content Crawler | `apify~website-content-crawler` | LIMITED | Crawlear sitio oficial |
| Web Scraper | `apify~web-scraper` | FULL (aprob. manual) | Scrap generico con jQuery |
| Cheerio Scraper | `apify~cheerio-scraper` | FULL (aprob. manual) | Scrap rapido HTML |

**Nota**: Los actores FULL_PERMISSIONS requieren aprobacion manual desde la web:
`https://console.apify.com/actors/{id}?approvePermissions=true`

## Limites plan gratuito

- $5/mes credito
- Google Search: ~$0.50/1000 resultados
- Web Scraper: ~$0.025/hora
- Dataset: 10GB almacenamiento
