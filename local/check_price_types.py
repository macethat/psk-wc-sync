import http.client, json
conn = http.client.HTTPSConnection("adm.premium-soft.com")
conn.request("GET", "/Api/Tipo_precios?pin=46558", headers={"clave-api-business": "BQxQrt5/FwARtlVUwT0GFw=="})
r = conn.getresponse()
items = json.loads(r.read())
print("=== TIPOS DE PRECIO ===")
for t in items:
    print(f"  ID {t['id_tipo_precio']}: {t['nombre_precios']} (cod: {t.get('codigo_tipo_precio','')})")

# Also check a product with offer data
conn2 = http.client.HTTPSConnection("adm.premium-soft.com")
conn2.request("GET", "/Api/Articulos?pin=46558&pagina=0&cant_pagina=200&en_oferta=1",
              headers={"clave-api-business": "BQxQrt5/FwARtlVUwT0GFw=="})
r2 = conn2.getresponse()
offers = json.loads(r2.read())
print(f"\n=== PRODUCTOS EN OFERTA (en_oferta=1) ===")
print(f"Total en oferta: {len(offers)}")
for o in offers[:3]:
    print(f"  {o['codigo']}: {o['nombre'][:40]}")
    print(f"    monto_descuento={o.get('monto_descuento')}, prc_descuento={o.get('prc_descuento')}")
    print(f"    id_oferta={o.get('id_oferta')}")
    for p in o.get('precios', []):
        if p['id_tipo_precio'] in ('1','3'):
            print(f"    Precio tipo {p['id_tipo_precio']} ({p['nombre_precios']}): ${float(p['precio_neto']):.2f}")
