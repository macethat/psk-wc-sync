# RETIRO EN SUCURSAL — Funcionalidad protegida (recovery guide)

> **ADVERTENCIA: NO TOCAR esta funcionalidad** en arreglos de otra índole sin avisar primero (ver sección [REGLA DE ORO](#regla-de-oro) y `AGENTS.md`).
> Este documento es la fuente de verdad para diagnosticar y reparar rápido si el retiro en sucursal se vuelve a dañar.

---

## 1. Qué hace esta funcionalidad

Permite que el cliente elija **retiro en sucursal** en lugar de envío:

1. **Ficha de producto** → muestra stock disponible por sucursal (`sp_show_sucursal_stock`, productos normales, variables y combos grouped).
2. **Carrito** → al marcar "Recoger en local" aparece un `<select>` de sucursal (`woocommerce_cart_totals_after_shipping` → `sp_cart_sucursal_field`).
3. **Persistencia** → el select del carrito está **FUERA del form** del carrito; el único mecanismo que guarda la sucursal en la sesión es el AJAX `sp_save_sucursal`.
4. **Checkout** → campo de sucursal + fila "Sucursal" en el review de envío, preseleccionada según sesión.
5. **Pedido** → la sucursal se guarda como meta del order y se muestra en admin y emails.

## 2. Dónde vive el código

| Recurso | Ubicación |
|---|---|
| **Código fuente (editable)** | `C:\suplementos\stock-suplementos\local\functions_current.php` |
| **Servidor (desplegado)** | `www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php` |
| **Plantilla checkout** | parent `nutritix` → `woocommerce/checkout/form-checkout.php` (nativa, dispara `woocommerce_review_order_after_shipping`) |
| **Combos en carrito** | mu-plugin `wp-content/mu-plugins/combo-price.php` (agrega combos como 1 item con `combo_children[]`) |
| **Submódulo sucursales** | `psk-sucursales` (solo frontend páginas sucursal, SIN código de checkout) |

## 3. Funciones PHP clave (en `functions.php` del child)

| Función | Rol |
|---|---|
| `sp_get_cart_item_ids($item)` | Devuelve los ids reales de un cart item. Si el item es combo (`combo_children[]`), devuelve los ids de los HIJOS (variation_id o product_id). |
| `sp_get_valid_sucursales_for_cart()` | Sucursales válidas = TODAS donde TODOS los items del carrito (y todos los hijos de cada combo) tienen stock (`_sucursales_disponibles`). |
| `sp_filter_shipping_methods()` | Hook `woocommerce_package_rates`. Elimina `local_pickup` si NINGÚN item (ni hijo de combo) tiene stock de sucursal. |
| `sp_save_sucursal_session()` + `sp_save_sucursal_ajax()` | Persisten la sucursal en sesión. El AJAX es el único camino desde el carrito (select fuera del form). |
| `sp_is_local_pickup_selected()` | Detecta pickup desde POST (`shipping_method`), session (`chosen_shipping_methods`) o fallback por intención (sucursal en sesión + rate local_pickup disponible). |
| `sp_get_cart_shipping_rate_ids()` | Extrae los rate ids del paquete (ojo: `calculate_shipping_for_package` devuelve `rates['rates']` anidado, no array directo). |
| `sp_get_sucursal_review_html()` | Construye la fila `tr.sp-sucursal-review` del review. |
| `sp_sucursal_fragment()` | Hook `woocommerce_update_order_review_fragments`. SIEMPRE emite la clave `tr.sp-sucursal-review` (aunque sea vacía) para que el AJAX de WooCommerce la elimine al cambiar a flat_rate. |
| `sp_validate_sucursal_field()` | Hook `woocommerce_checkout_process`. Valida que se eligió sucursal si pickup está activo. |
| `sp_save_sucursal_order_meta()` | Guarda la sucursal en el order. |

### JS (en `sp_sucursal_js()`, se imprime inline en carrito/checkout/producto)

| Bloque | Rol |
|---|---|
| `spBindSucursalToggle()` | Muestra/oculta el campo de sucursal según el radio de shipping marcado. NO borra valores. Corre cada 800ms (setInterval) para sobrevivir re-renders AJAX. |
| `spUpdateSucursalInfo()` | Actualiza la fila `tr.sp-sucursal-review` y el div de info con la sucursal elegida. **NO debe contener `!isPickup` en el early-return** (borraría la fila que el servidor ya renderizó). Corre cada 800ms. |
| Delegación de eventos (change) | Listener en `document` para `#sp_sucursal_retiro` y `#sp_sucursal_retiro_cart` → sobrevive re-renders AJAX de WooCommerce. Dispara AJAX `sp_save_sucursal` + `update_checkout`. |
| `spUpdateStock()` (ficha producto variable) | Actualiza el cuadro de stock por sucursal según la variación elegida. **Regla stock mínimo**: si la variación tiene `_stock_status=outofstock` pero stock físico > 0 en sucursales, reemplaza el cuadro por el aviso "Disponible solo para compra por sucursal en: X, Y, Z" (usa `data-sp-status` y `data-sp-names` del contenedor). |

## 4. Historial de bugs corregidos (2026-08-07)

### BUG A — Combos: solo aparecía "Envío", sin "Recoger en local"
- **Síntoma**: en el carrito, los combos (grouped) no mostraban la opción de retiro ni el select de sucursal.
- **Causa raíz**: `sp_filter_shipping_methods()`/`sp_get_valid_sucursales_for_cart()` usaban el `product_id` del COMBO, que no tiene `_sucursales_disponibles` (vive en los hijos). `$any_sucursal=false` → se eliminaba `local_pickup`.
- **Fix**: helper `sp_get_cart_item_ids()` + iterar hijos de `combo_children[]`.
- **Síntoma de re-ocurrencia**: un combo sin "Recoger en local" en el carrito.

### BUG B — La sucursal elegida en el carrito no quedaba indicada en el checkout
- **Síntoma**: en el checkout solo aparecía "Recoger en local" marcado, sin sucursal seleccionada ni fila "Sucursal".
- **Causa raíz (doble)**: (1) `spBindSucursalToggle()` hacía `sel.value=''` borrando la preselección cuando el método marcado no era local_pickup (default = flat_rate); (2) `chosen_shipping_methods` en sesión suele estar vacío al llegar al checkout, por lo que las funciones no detectaban pickup.
- **Fix**: `spBindSucursalToggle()` ya no borra el valor; nuevos helpers `sp_is_local_pickup_selected()` y `sp_get_cart_shipping_rate_ids()` con fallback por intención.
- **Síntoma de re-ocurrencia**: el select de sucursal aparece vacío en checkout aunque el cliente la eligió en el carrito.

### BUG C (regresión JS) — La fila "Sucursal" del review se borraba en el navegador
- **Síntoma**: en navegador real el backend renderiza la fila, pero desaparece al momento (aunque "Recoger en local" esté marcado).
- **Causa raíz**: en la sesión 2 se añadió `!isPickup` al early-return de `spUpdateSucursalInfo()`. Al llegar al checkout WooCommerce marca `flat_rate` por defecto → `isPickup=false` → el JS ejecutaba `stale.remove()` y borraba la fila server-side.
- **Fix**: eliminar `!isPickup` del early-return; `sp_sucursal_fragment()` siempre emite la clave review. Commit `e59d459`.
- **Síntoma de re-ocurrencia**: la fila "Sucursal" aparece y desaparece sola en el checkout.
- **Cómo verificar**: en el combined-js servido debe existir `if (!isPickup) return` y **NO** debe existir `!isPickup ||`.

### BUG D — El guardado desde el carrito se perdía (listener no sobrevivía re-render AJAX)
- **Síntoma**: aún con navegador limpio/incógnito, la sucursal no llegaba al checkout.
- **Causa raíz**: el select del carrito (`#sp_sucursal_retiro_cart`) está FUERA del form; la persistencia depende 100% del AJAX `sp_save_sucursal`. El listener directo `spBindSucursalChange()` se perdía cuando WooCommerce re-renderizaba el carrito por AJAX (al marcar "Recoger en local") → `sp_save_sucursal` nunca se disparaba → sesión vacía.
- **Fix**: delegación de eventos en `document` (sobrevive re-renders). Commit `463200d`.
- **Síntoma de re-ocurrencia**: elegir sucursal en el carrito y verificar que al ir al checkout `<!-- SP_DEBUG -->` muestra `selected=` vacío. Si pasa eso, el listener del select murió de nuevo.

### BUG E — Cuadro de ficha mostraba "Disponible para retiro" en producto agotado online (regla stock mínimo)
- **Síntoma**: la ficha de un producto/variación con `_stock_status=outofstock` (por la regla "≤6 → outofstock") pero con stock físico en sucursales, seguía mostrando el cuadro "Disponible para retiro en: X (n unid.)" como si se pudiera retirar online.
- **Causa raíz**: el cuadro de sucursal usaba el stock físico por sucursal (`_sucursal_X_stock`), independiente del `_stock_status`. Al estar outofstock online, el cliente no puede retirar/pedir online, solo comprar presencialmente en la sucursal.
- **Fix** (session 2026-08-07): 
  - PHP: se captura `_stock_status` por variación (`$variation_status`) y se expone al JS en `data-sp-status`; los nombres de sucursal se exponen en `data-sp-names`. Para productos simples, si `$product->get_stock_status()==='outofstock'` y hay stock en sucursal, se renderiza server-side el aviso de compra presencial.
  - JS `spUpdateStock()`: si la variación elegida está outofstock pero tiene stock>0 en alguna sucursal, reemplaza el cuadro por "Disponible solo para compra por sucursal en: <nombres>."
- **Regla de negocio**: la regla "≤6 → outofstock" se CONSERVA (reserva mínima para ventas por sucursal). El aviso solo informa al cliente que puede comprarlo presencial.
- **Cómo verificar**: en la ficha del producto agotado por regla debe verse el aviso naranja (`.sp-store-msg`); en un producto instock con stock de sucursal debe seguir el cuadro verde normal. El combined-js debe contener `sp-store-msg`.

## 5. Cómo diagnosticar rápido (checklist)

1. **Ver el debug server-side**: en el HTML del checkout buscar `<!-- SP_DEBUG selected=X method=Y -->` (emitido por `sp_checkout_sucursal_field`).
   - `selected=1 method=local_pickup` → backend OK.
   - `selected=` vacío → la sesión nunca recibió la sucursal → problema en el carrito/AJAX (BUG D).
   - `method=` vacío → el fallback de intención no detecta pickup → revisar `sp_is_local_pickup_selected()`.
2. **Ver la fila review en el HTML**: buscar `tr.sp-sucursal-review` en el HTML servido.
   - Presente en el HTML pero desaparece en navegador → BUG C (JS, combined-js viejo o `!isPickup`).
   - Ausente en el HTML → problema PHP (BUG B o combinación de B y D).
3. **Ver el combined-js de SG Optimizer**: el checkout debe tener el archivo `combined-js-*.js` que contiene las functions de sucursal. Descargarlo y validar:
   - `node -e "new Function(require('fs').readFileSync('combined.js','utf8'))"` → sintaxis OK.
   - `if (!isPickup) return` presente; `!isPickup ||` ausente; `spBindSucursalChange` ausente; delegación `document.addEventListener('change'...)` con `sp_sucursal_retiro_cart` presente.
4. **Flujo curl end-to-end** (con cookie + combo real 21525):
   - AJAX: `POST admin-ajax.php action=sp_save_sucursal&sucursal=1` → `{"success":true}`.
   - Carrito: `POST ?wc-ajax=update_cart` o `/cart/` con `shipping_method[0]=local_pickup:4` + `sp_sucursal_retiro=1`.
   - Checkout: `GET /checkout/` → `SP_DEBUG selected=1 method=local_pickup` + fila review + radio checked + select preseleccionado.

## 6. Cómo reparar rápido

1. **Siempre respaldar primero** en el servidor: `cp functions.php functions.php.bak-$(date +%Y%m%d-%H%M%S)`.
2. Editar `C:\suplementos\stock-suplementos\local\functions_current.php` (copiar hacia el servidor el bloque `sp_sucursal_js()` y las functions PHP).
3. Validar sintaxis: `php -l functions.php`.
4. Desplegar vía SSH/SCP a `.../themes/nutritix-child/functions.php`.
5. **Forzar regeneración del combined-js de SG Optimizer**: al visitar el checkout SG Optimizer lo regenera si el HTML/PHP cambió (el hash cambia). Si no, purgar cachés: `wp sg purge`, `wp cache flush`. Confirmar el hash nuevo del `combined-js-*.js`.
6. Verificar con el checklist de la sección 5.
7. Commit en rama `feature/retiro-sucursal` del repo `macethat/psk-wc-sync` + actualizar este documento y `historial-chat.md`.

## 7. REGLA DE ORO

- **NO modificar** las funciones `sp_*` del child (las de sucursal/carrito/checkout), el mu-plugin `combo-price.php`, ni el JS de `sp_sucursal_js()` como parte de **arreglos no relacionados** (CSS de blog, SEO, analytics, newsletter, menús, etc.).
- Si un arreglo de otra índole debe tocar `functions.php` del child, **ANUNCIAR primero** qué bloques va a tocar y verificar después con el checklist que el retiro en sucursal sigue OK (los 4 flujos: producto, carrito, checkout, order meta).
- Las regresiones típicas por culpa de tocar sin cuidado:
  - Añadir `!isPickup ||` al early-return de `spUpdateSucursalInfo()` (BUG C).
  - Quitar la delegación o volver a listener directo (BUG D).
  - Cambiar `sp_get_meta_id()` por id del combo en vez de los hijos (BUG A).
  - Editar `_elementor_data` con `update_post_meta` en vez de `update_metadata` (rompe JSON — lección de la newsletter).
