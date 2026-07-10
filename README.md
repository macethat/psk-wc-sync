# PSK-WC-SYNC

Sincronización bidireccional entre **PSK Cloud** (Premium Soft) y **WooCommerce** para gestión de inventario, clientes y pedidos.

## Arquitectura

```
PSK Cloud API ──> daily_stock_update.py ──> WooCommerce (via SSH+WP-CLI)
       │                                          │
       └── Clientes, Pedidos, Stock               └── Pedidos web
```

- **Origen de datos**: API REST de PSK Cloud (`adm.premium-soft.com/Api/`)
- **Destino**: WooCommerce vía SSH + WP-CLI
- **Frecuencia**: Diaria (bajo demanda o programable vía Windows Task Scheduler)

## Procesos

### 1. Sincronización de Inventario (Implementado ✅)

Extrae stock total (suma de todos los almacenes) desde PSK Cloud y actualiza WooCommerce.

**Uso:**
```bash
# Dry-run (solo genera reportes)
python daily_stock_update.py --api

# Live (actualiza WooCommerce)
python daily_stock_update.py --live --api

# Con fecha forzada (si el inventario es del día anterior)
python daily_stock_update.py --live --api --fecha DD-MM-YYYY

# Sin flag --api usa ListaInvFisic.csv manual
python daily_stock_update.py --live
```

**Regla de negocio:**
- Stock > 6 → `instock`
- Stock ≤ 6 → `outofstock`

**Salida por corrida (carpeta `update_DD-MM-YYYY/`):**
- `ListaInvFisic.csv` — inventario extraído de la API
- `wc_export.json` — exportación de productos WooCommerce
- `comparativa_previa.csv` — comparación antes de actualizar
- `cambios.csv` — todos los cambios (stock + status)
- `cambios_stock.csv` — solo cambios de cantidad
- `reporte_actualizacion.csv` — resultado final
- `verificacion.txt` — discrepancias post-actualización

### 2. Gestión de Clientes (Pendiente 🔄)

Al recibir un pedido en WooCommerce, buscar al cliente en PSK Cloud por documento/email. Si no existe, crearlo vía `Clientes_GuardarRapido`.

**Endpoint:** `POST /Api/Clientes_GuardarRapido?pin=46558`

### 3. Pedidos y Descuento de Stock (Pendiente 🔄)

Crear pedidos en PSK Cloud desde WooCommerce con prioridad de almacén:
1. SP CANGREJO (id=1)
2. SP MEGAPOLIS (id=5)
3. Resto (sucursal con mayor stock)

**Endpoint:** `POST /Api/ProcesarDoc?pin=46558`

## Configuración

### Credenciales

| Variable | Descripción |
|---|---|
| PSK PIN | `46558` |
| PSK API Key | configurada en script |
| SSH Host | `ssh.suplementospanama.net:18765` |
| SSH User | `u1910-kbd9lgn9dh44` |
| WP Path | `~/www/suplementospanama.net/public_html` |

### Almacenes (id_almacen)

| ID | Nombre |
|---|---|
| 1 | SP CANGREJO |
| 5 | SP MEGAPOLIS |
| 6 | SP ATRIO COSTA DEL ESTE |
| 7 | POWER CLUB SAN FRANCISCO |
| 8 | POWER CLUB ALTOS DE PANAMA |
| 9 | SP BODEGA |
| 10 | SP METROMALL |
| 11 | BODEGA MEGAPOLIS |
| 12 | BODEGA ATRIO |

### Documentos

| Tipo | id_tipo_documento |
|---|---|
| Pedido web | 2 (origen_tipo=3) |
| Factura interna web | 35 |

### Usuarios / Vendedores

| Rol | ID | Nombre |
|---|---|---|
| Usuario pedidos web | 143 | VENTAS WEB |
| Vendedor pedidos web | 25 | VENTAS WEB |

## API PSK Cloud - Endpoints Utilizados

| Endpoint | Método | Uso |
|---|---|---|
| `/Api/Articulos` | GET | Listar productos con stock total |
| `/Api/Almacenes` | GET | Obtener lista de almacenes |
| `/Api/Existencias` | GET | Stock por artículo por almacén |
| `/Api/Tipo_documentos` | GET | Tipos de documento disponibles |
| `/Api/Clientes` | GET | Buscar clientes |
| `/Api/Clientes_GuardarRapido` | POST | Crear/actualizar cliente |
| `/Api/ProcesarDoc` | POST | Crear pedido (cabecera + líneas + cobros) |
| `/Api/Vendedores` | GET | Listar vendedores |
| `/Api/Usuarios` | GET | Listar usuarios del sistema |

### 4. Productos Combo (Agrupados con Precio Fijo)

Sistema de productos combo tipo `grouped` con precio fijo personalizado, bypass de stock del padre y descuento de stock de componentes al confirmar el pedido.

**Archivos involucrados:**
- `combo-price.php` — MU plugin (meta box, cart handler, stock deduction, filters, restriction by variations)
- `grouped.php` — override en child theme `nutritix-child` (template grouped con variaciones)
- `combo-debug.php` — MU plugin auxiliar (debug logs, override `is_in_stock` con threshold)
- `combo-fix-reservestock.php` — parche para WooCommerce 10.x ReserveStock

**Configuración de un combo:**
1. Crear producto tipo agrupado (`grouped`)
2. Agregar hijos (productos simples/variables que lo componen) en la sección "Productos agrupados"
3. En el meta box "Configuración del Combo" (sidebar), fijar el precio
4. Si hay productos variables entre los hijos, usar "Variaciones permitidas" (máx. 3 campos) para restringir qué variaciones están disponibles. Si se deja vacío, se muestran todas.
5. Asignar la meta `_children` con los IDs de los productos relacionados separados por coma (o usar la UI de WooCommerce)
6. Dejar `_manage_stock=no`, `_stock` vacío (el stock lo manejan los hijos)

**Regla de stock (threshold):**
- Si un hijo maneja stock (`manage_stock=true`) y su cantidad es ≤ 6, se considera no disponible (outofstock) hasta que se reponga inventario
- Esto aplica tanto en la ficha del producto como en el handler del carrito
- Productos sin manejo de stock usan `is_in_stock()` estándar

**Variaciones restringidas:**
- Los combos pueden tener hijos variables con variaciones específicas permitidas
- Meta `_combo_variations_{child_id}` contiene IDs de variación separados por coma
- En el template `grouped.php`: si solo 1 variación permitida → auto-seleccionada sin dropdown; si 2+ → dropdown con solo esas opciones
- Las variaciones no permitidas no se muestran ni son seleccionables

**Flujo en carrito:**
- El producto padre se agrega como 1 ítem con precio `_combo_price`
- Bypass completo de `WC()->cart->add_to_cart()` (inserción directa a `cart_contents`)
- Los hijos NO se agregan como items separados
- Antes de agregar, el handler verifica que TODOS los hijos tengan stock disponible (incluyendo threshold ≤ 6)
- Si falta stock, muestra error y rechaza la operación

**Descuento de stock:**
- Se descuenta stock de cada hijo al confirmar el pedido (`woocommerce_checkout_order_processed`)
- Cada combo resta 1 unidad de cada componente por cada unidad comprada

**Fix WooCommerce 10.x — ReserveStock:**
- WooCommerce 10.x introdujo `ReserveStock` que lee `_stock` directamente de la BD (bypasea filtros PHP)
- Para combos con `_manage_stock=no` y `_stock` vacío, la consulta SQL devuelve 0 y rechaza el pedido
- Solución: filtro `woocommerce_hold_stock_for_checkout` que desactiva ReserveStock cuando hay combos en el carrito

**Stock status del combo:**
- El filtro `woocommerce_product_get_stock_status` retorna `outofstock` si algún hijo no está disponible
- El filtro `woocommerce_product_is_in_stock` (en combo-debug.php) retorna `false` si algún hijo no está disponible
- El filtro `woocommerce_product_get_stock_quantity` retorna 0 si algún hijo no está disponible, 9999 si todos ok
- Template `grouped.php`: deshabilita quantity inputs, botón y muestra "Producto no disponible" en cada fila sin stock

**Ahorro mostrado en ficha:**
- `Ahorra $X.xx` en rojo, `font-size:0.8em`, `margin-left:12px`, al lado del precio

## Notas Técnicas

- **Autenticación API**: Header `clave-api-business` (usar guiones `clave-api-business`, no guiones bajos)
- **WooCommerce**: Actualmente bloqueado por SG-CAPTCHA en API REST directa → se usa SSH+WP-CLI
- **Auto-commit**: Cada corrida `--live` genera commit + push automático al repo
- **Bug conocido (resuelto)**: `existencias` viene como `"258.00"` (string) → se convierte a `int(float())`
