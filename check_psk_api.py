import http.client, json

PSK_PIN = "46558"
PSK_API_KEY = "BQxQrt5/FwARtlVUwT0GFw=="
PSK_API_HOST = "adm.premium-soft.com"

conn = http.client.HTTPSConnection(PSK_API_HOST)
conn.request('GET', f'/Api/Articulos?pin={PSK_PIN}&pagina=0&cant_pagina=99999&precios=1',
             headers={'clave-api-business': PSK_API_KEY})
r = conn.getresponse()
data = json.loads(r.read().decode())

# Search by barcode 811445021105
found = [a for a in data if '811445021105' in a.get('codigo', '') or '811445021105' in a.get('codigo_barras', '') or a.get('codigo', '') == '811445021105']

if found:
    for a in found:
        print(f"ID: {a.get('id_articulo')}")
        print(f"Codigo: {a.get('codigo')}")
        print(f"Nombre: {a.get('nombre')}")
        print(f"Existencias: {a.get('existencias')}")
        print(f"CodigoBarra: {a.get('codigo_barras')}")
        print(f"Precios: {a.get('precios')}")
        print("---")
else:
    print("NO ENCONTRADO")
    # Try a broader search
    for a in data[:5]:
        print(f"Sample - Codigo: {a.get('codigo')}, Nombre: {a.get('nombre')}")
