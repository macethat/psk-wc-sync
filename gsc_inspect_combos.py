"""Inspecciona cada combo en GSC para ver si está indexado."""
import json
from gsc_query import get_service, SITE_DOMAIN

slugs = [
    "elite-performance-stack","iso-total-pack","prolive-full-stack",
    "vms-triple-stack","iso-100-fruity-pebbles-raw-pre-workout-orange",
    "combo-iso-100-fruity-pebbles-creatina-angry-supplements",
    "isoject-vanilla-raw-pre-orange-creatina-vms",
    "evofusion-choc-pb-creatina-vms-bcaa-vms-fruit-punch",
    "prolive-bio5-glutamina-vms","vms-bios-active-glutamina-vms",
    "isoject-vanilla-glutamina-vms","prolive-bio6-bcaa-vms-30-servings",
    "isoject-vanilla-bcaa-vms-30-servings",
    "evofusion-choc-pb-bcaa-vms-fruit-punch","vms-bios-active-raw-pre-orange",
    "prolive-bio5-bum-pre-blue-raspberry","evofusion-choc-pb-bum-pre-blue-raspberry",
    "prolive-bio6-creatina-nutrex","prolive-bio5-creatina-vms",
    "vms-bios-active-creatina-vms","isoject-vanilla-creatina-vms",
    "evofusion-choc-pb-creatina-vms",
    "proteina-5-lb-impulse-creatina-super-atp-80-serv",
    "creatina-evogen-60-serv-beta-alanina-raw",
]

svc = get_service()

results = []
for slug in slugs:
    url = f"https://suplementospanama.net/product/{slug}/"
    body = {
        "inspectionUrl": url,
        "siteUrl": SITE_DOMAIN,
    }
    try:
        resp = svc.urlInspection().index().inspect(body=body).execute()
        r = resp.get("inspectionResult", {})
        index = r.get("indexStatusResult", {})
        verdict = index.get("verdict", "N/A")
        coverage = index.get("coverageState", "N/A")
        state = "INDEXADO" if verdict == "PASS" else "NO INDEXADO"
        results.append({"slug": slug, "url": url, "verdict": verdict, "state": state, "coverage": coverage})
    except Exception as e:
        results.append({"slug": slug, "url": url, "verdict": "ERROR", "state": "ERROR", "coverage": str(e)[:60]})
    mark = "[OK]" if state == "INDEXADO" else "[--]"
    print(f"{mark} {slug:<55} {state}")

print(f"\n{'='*70}")
print(f"Indexados: {sum(1 for r in results if r['state'] == 'INDEXADO')} de {len(results)}")
print(f"No indexados: {sum(1 for r in results if r['state'] == 'NO INDEXADO')} de {len(results)}")

with open("combos_inspection.json", "w", encoding="utf-8") as f:
    json.dump(results, f, indent=2, ensure_ascii=False)
print("Resultados guardados en combos_inspection.json")
