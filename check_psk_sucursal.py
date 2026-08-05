import http.client, json

PSK_PIN = "46558"
PSK_API_KEY = "BQxQrt5/FwARtlVUwT0GFw=="
PSK_API_HOST = "adm.premium-soft.com"

# Search for stock of article ID 1521
conn = http.client.HTTPSConnection(PSK_API_HOST)
conn.request('GET', f'/Api/Existencias?pin={PSK_PIN}',
             headers={'clave-api-business': PSK_API_KEY})
r = conn.getresponse()
data = json.loads(r.read().decode())

found = [ex for ex in data if ex.get('id_articulo') == '1521']

if found:
    print("=== Stock por sucursal ===")
    for ex in found:
        print(f"ID Almacen: {ex.get('id_almacen')}, Existencia: {ex.get('existencia')}")
else:
    print("Sin registros de existencia por sucursal")
