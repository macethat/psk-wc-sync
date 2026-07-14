import json

with open('gsc_pages.json', encoding='utf-8') as f:
    data = json.load(f)

combos = [r for r in data if 'combo' in r['keys'][0].lower() or 'stack' in r['keys'][0].lower()]

print(f"Total páginas en GSC con datos: {len(data)}")
print(f"Páginas con 'combo' o 'stack': {len(combos)}")

if combos:
    print("\nCombos encontrados en GSC:")
    for r in combos:
        url = r['keys'][0]
        imp = r['impressions']
        clicks = r['clicks']
        pos = r['position']
        print(f"  {url}")
        print(f"    Impresiones: {imp}, Clicks: {clicks}, Posición: {pos:.1f}")

product_urls = [r for r in data if '/product/' in r['keys'][0]]
producto_urls = [r for r in data if '/producto/' in r['keys'][0]]
print(f"\nURLs con /product/: {len(product_urls)}")
print(f"URLs con /producto/: {len(producto_urls)}")
print(f"\nTop 10 URLs más visitadas:")
data_sorted = sorted(data, key=lambda r: r['clicks'], reverse=True)
for i, r in enumerate(data_sorted[:10]):
    url = r['keys'][0]
    clicks = r['clicks']
    imp = r['impressions']
    print(f"  {i+1}. {url} ({clicks} clics, {imp} imp)")
