import http.client, json

# Check products with offers
conn = http.client.HTTPSConnection("adm.premium-soft.com")
conn.request("GET", "/Api/Articulos?pin=46558&pagina=0&cant_pagina=500&en_oferta=1&precios=1",
             headers={"clave-api-business": "BQxQrt5/FwARtlVUwT0GFw=="})
r = conn.getresponse()
offers = json.loads(r.read())
print(f"Products with en_oferta=1: {len(offers)}")
for o in offers[:5]:
    p3 = [p for p in o.get("precios",[]) if p["id_tipo_precio"]=="3"]
    p3_val = float(p3[0]["precio_neto"]) if p3 else 0
    print(f"  {o['codigo']}: {o['nombre'][:35]}")
    print(f"    retail={p3_val:.2f}, id_oferta={o.get('id_oferta')}, "
          f"prc_desc={o.get('prc_descuento')}, monto_desc={o.get('monto_descuento')}")

# Check a few normal products
conn2 = http.client.HTTPSConnection("adm.premium-soft.com")
conn2.request("GET", "/Api/Articulos?pin=46558&pagina=0&cant_pagina=5&precios=1",
              headers={"clave-api-business": "BQxQrt5/FwARtlVUwT0GFw=="})
r2 = conn2.getresponse()
normal = json.loads(r2.read())
print(f"\nNormal products:")
for n in normal[:5]:
    print(f"  {n['codigo']}: id_oferta={n.get('id_oferta')}")
