import http.client, json
conn = http.client.HTTPSConnection("adm.premium-soft.com")
conn.request("GET", "/Api/Articulos?pin=46558&pagina=0&cant_pagina=100",
             headers={"clave-api-business": "BQxQrt5/FwARtlVUwT0GFw=="})
r = conn.getresponse()
items = json.loads(r.read())
con_precio = 0
sin_precio = 0
for i in items:
    precios = [p for p in i.get("precios", []) if p["id_tipo_precio"] == "3" and float(p["precio_neto"]) > 0]
    if precios:
        con_precio += 1
    else:
        sin_precio += 1
print(f"Total: {len(items)}, Con precio tipo 3: {con_precio}, Sin precio: {sin_precio}")
for i in items[:5]:
    p3 = [p["precio_neto"] for p in i.get("precios", []) if p["id_tipo_precio"] == "3"]
    print(f"  {i['codigo']}: {i['nombre'][:40]} -> precio tipo 3: {p3[0] if p3 else 'N/A'}")
