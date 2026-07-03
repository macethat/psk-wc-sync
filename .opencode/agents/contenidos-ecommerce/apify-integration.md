---
tool: Apify
plan: Gratuito ($5/mes credito)
estado: Configuracion inicial
---

# Integracion Apify

Usamos la API REST de Apify para web scraping, no el CLI (por restricciones de ejecucion en el entorno).

## Setup

1. Crear cuenta en https://apify.com (plan gratuito)
2. Obtener API token desde Apify Console > Integrations > API
3. Guardar token en variable de entorno `APIFY_TOKEN`

## Uso via Python

```python
import requests, json

APIFY_TOKEN = "tu_token_aqui"
APIFY_BASE = "https://api.apify.com/v2"

# Ejemplo: Ejecutar Web Scraper en una URL
actor_id = "apify/web-scraper"  # Actor generico
resp = requests.post(
    f"{APIFY_BASE}/acts/{actor_id}/runs",
    params={"token": APIFY_TOKEN},
    json={
        "runInput": {
            "startUrls": [{"url": "https://ejemplo.com"}],
            "pageFunction": """async function pageFunction(context) {
    const $ = context.jQuery;
    return {
        title: $('title').text(),
        description: $('meta[name=description]').attr('content'),
        body: $('body').text().substring(0, 5000)
    };
}"""
        }
    }
)
run_id = resp.json()["data"]["id"]

# Esperar a que termine y obtener resultados
import time
while True:
    r = requests.get(f"{APIFY_BASE}/acts/{actor_id}/runs/{run_id}", params={"token": APIFY_TOKEN})
    status = r.json()["data"]["status"]
    if status == "SUCCEEDED":
        dataset_id = r.json()["data"]["defaultDatasetId"]
        data = requests.get(f"{APIFY_BASE}/datasets/{dataset_id}/items", params={"token": APIFY_TOKEN, "format": "json"})
        print(data.json())
        break
    elif status in ("FAILED", "ABORTED"):
        print("Error:", status)
        break
    time.sleep(3)
```

## Actores recomendados

| Actor | ID | Uso |
|-------|----|-----|
| Web Scraper | `apify/web-scraper` | Scrap generico con jQuery |
| Cheerio Scraper | `apify/cheerio-scraper` | Scrap rapido (HTML parse) |
| Google Search Scraper | `apify/google-search-scraper` | Resultados de busqueda Google |
| Amazon Product Scraper | `jungle-scout/amazon-product-scraper` | Productos Amazon |
| Instagram Scraper | `apify/instagram-scraper` | Contenido redes |

## Limites plan gratuito

- $5 de credito mensual
- $0.025 por hora de computo (Web Scraper)
- ~200 horas/mes de scraper ligero
- Dataset: 10GB almacenamiento
