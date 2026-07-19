# Historial de Cambios — Blog Power Rack

## 2026-07-15 — Artículo #2 "Whey vs Proteína Vegetal" (ID 21698)

### Correcciones listado de categorías (mobile)

| # | Problema | Solución |
|---|----------|----------|
| 1 | CSS no cargaba en páginas de categoría | Agregado `is_category()` a condición del `wp_head` |
| 2 | Título pegado a imagen de post siguiente | Reordenado con Flexbox: título arriba, luego meta, luego imagen |
| 3 | Títulos muy pequeños en listado | `font-size: 22px` en `.entry-title` y `.entry-title a` |
| 4 | Título montado sobre meta | `margin-bottom: 24px` entre título y meta |
| 5 | Separación entre posts muy amplia | `row-gap: 25px` en `.blog-style-grid` |

### Correcciones post-publicación

| # | Problema | Solución |
|---|----------|----------|
| 1 | Acentos corruptos (`proteína` → `prote├¡na`) al pasar contenido por PowerShell | Restaurado desde revisión vía `wp eval-file` con PHP puro en servidor |
| 2 | Highlights usaban colores lila del artículo anterior | Agregado post meta `highlight_accent` con CSS override en functions.php |
| 3 | Reel de productos tenía 4 items | Reducido a 3 IDs (`21454,21335,18861`) |
| 4 | Cover image repetida dentro del contenido | Eliminado el `<figure>` duplicado, mantenida solo como Featured Image |
| 5 | Lists sin font-size definido | Agregado `li { font-size: 17px }` (igual que párrafos) |
| 6 | "Panamá" cortado en tabla comparativa | Div contenedor con `overflow-x:auto`, mobile: `width:auto` + `white-space:nowrap` en celdas + scroll horizontal |
| 7 | Cover image original tenía typo | Reemplazada por nueva imagen `proteina-wey-vs-vegetal-cover-sp.jpg` (ID 21710) |

### Mejoras en functions.php

- `highlight_accent` meta: permite color de viñeta/flecha por artículo
- `.content-area table td, #primary table td { white-space: normal }` para desktop
- `@media (max-width: 767px)`: tabla `width:auto`, celdas `white-space:nowrap`
- `.content-area li, #primary li { font-size: 17px }`

## 2026-07-16 — Últimos arreglos

| # | Cambio | Detalle |
|---|--------|---------|
| 1 | FAQ en Highlights (Creatina) | Convertidas preguntas de `<p><strong>` a `<h3>` para que aparezcan como desplegables en ToC |
| 2 | Texto botón CTA personalizable | Agregado post meta `reel_btn_text` para texto de botón personalizado (ej: "creatinas" plural) |
| 3 | Reporte GSC | Script `gsc_search_analytics.py` + PDF en `suplementos/reporte_gsc.pdf` |
| 4 | Lista packs excluidos | Script `generar_lista_packs.py` + PDF con productos padre excluidos de `--update-prices` |
| 5 | Corrección blog listing mobile | CSS ahora carga en `is_category()`, título arriba con Flexbox, row-gap 25px, font-size 22px |

### Mejoras en functions.php

- `reel_btn_text` meta: texto personalizado para botón CTA
- Condición `wp_head` ampliada a `is_category()` para que CSS aplique en páginas de categoría
- Blog listing mobile: Flexbox reorder, `row-gap: 25px`, `font-size: 22px` en títulos

## 2026-07-17 — Structured Data: Fix combos + categorías

| # | Problema | Solución |
|---|----------|----------|
| 1 | Error GSC "Invalid object type for field" en combos | Removido `hasMerchantReturnPolicy` y `shippingDetails` de offers en variantes (`hasVariant`) — filter `rank_math/json_ld` |
| 2 | Error GSC "Either offers, review, or aggregateRating should be specified" en categorías | Removido `@type: Product` de items en `itemListElement` vía filter `rank_math/json_ld` en `is_product_category()`, `is_shop()`, `is_product_tag()` |

## 2026-07-17 — Meta Pixel + CAPI para Combos

Configuración completa de tracking para campaña Meta Ads de combos:

| Componente | Pixel ID | Detalle |
|-----------|----------|---------|
| **Dataset** | `Combos suplementos` (1366708898928630) | Nuevo, dedicado solo a combos |
| **Pixel base** | 1366708898928630 | `PageView` en todas las páginas |
| **ViewContent** | Cliente (fbq) + CAPI | Fichas de producto combo, `content_type: product_group` |
| **AddToCart** | Cliente (fbq) + CAPI | Al agregar producto hijo de combo al carrito |
| **InitiateCheckout** | Cliente (fbq) + CAPI | Checkout si el carrito contiene combos |
| **Purchase** | Cliente (fbq) + CAPI | Thank you, con email/phone hash para match quality |
| **CAPI Access Token** | EAAbq... (truncado) | Token generado desde Meta, configurado vía `sp_capi_send()` |
| **Catálogo Meta** | 23 combos importados | CSV generado desde WordPress vía script PHP en servidor |

### Archivos relevantes
- `functions.php`: Secciones `SP_META_PIXEL_ID`, `SP_META_CAPI_TOKEN`, `sp_capi_send()`, eventos pixel + CAPI
- `.ssh-config`: Config SSH para conexión (gitignored)
- `ssh-key-nopass`: Clave SSH (gitignored)

### Pixeles conviviendo
- **1366708898928630** (nuevo): Combos, `content_type: product_group`
- **1171744165106406** (existente): PixelYourSite, productos individuales, `content_type: product`

### Blog listing grid — restauración post-reeescritura

El CSS del blog listing se perdió al sobreescribir functions.php con el Meta Pixel. Restaurado con valores canónicos:

| Propiedad | Valor desktop | Valor mobile |
|-----------|--------------|--------------|
| `.blog-style-grid` `row-gap` | `35px` | `20px` |
| `.blog-style-grid .entry-title` | `22px` | `22px` |
| `.blog-style-grid .entry-title a` | `22px` | `22px` |
| `.blog-style-grid .entry-title` `margin-bottom` | `24px` | `24px` |
| Flexbox order | `entry-header(1)` → `entry-meta(2)` → `post-thumbnail(3)` | igual |
| Condición `wp_head` | `is_single()`, `is_home()`, `is_category()` | — |
| **Lección**: Al editar functions.php, verificar que no se pierdan bloques CSS anteriores. Hacer diff antes de subir.

### Lecciones aprendidas

1. **No usar PowerShell string manipulation para contenido con acentos** → usar PHP `file_put_contents` / `wp eval-file` en servidor
2. **Base64 + `base64 -d`** es el método confiable para transferir archivos PHP sin corrupción de encoding
3. **Para tablas responsivas**: quitar `width:100%` inline, controlar por CSS, y usar `overflow-x:auto` en contenedor padre
4. **Meta Event Setup Tool** no detecta pixel en Firefox, solo en Chrome con Meta Pixel Helper
5. **CAPI Access Token** debe generarse desde Business Settings > Data Sources > Settings > Conversiones API; Meta no guarda el token
6. **Event Match Quality** bajo (3.4/10) es normal con eventos de prueba; sube automáticamente con tráfico real
7. **SSH SiteGround**: puerto 18765, host ssh.suplementospanama.net, usuario u1910-kbd9lgn9dh44
8. **`sp_capi_send()`** usa `blocking: false` para no ralentizar páginas; para pruebas usar `blocking: true`

## 2026-07-18 — Retiro en Sucursal (Local Pickup)

Inicio de implementación de selección de sucursal para retiro en tienda (local pickup).

### Estado actual
- Migrado a rama `feature/retiro-sucursal`
- Descubiertas 6 sucursales retail desde PSK Cloud API (`/Api/Almacenes`)
- Descubierto endpoint `/Api/Existencias` con stock por almacén
- Pendiente: direcciones físicas de sucursales

### Sucursales (PSK → Display)
| id_almacen | Nombre PSK | Display name |
|---|---|---|
| 7 | POWER CLUB SAN FRANCISCO | SP San Francisco |
| 6 | SP ATRIO COSTA DEL ESTE | SP Atrio Mall |
| 1 | SP CANGREJO | SP El Cangrejo |
| 5 | SP MEGAPOLIS | SP Megapolis |
| 10 | SP METROMALL | SP Metromall |
| 8 | POWER CLUB ALTOS DE PANAMA | SP Altos de Panamá |

### Implementado ✓

| # | Funcionalidad | Estado |
|---|---------------|--------|
| 1 | Mostrar disponibilidad por sucursal en ficha de producto | ✓ |
| 2 | Selector de sucursal en checkout al elegir retiro local | ✓ |
| 3 | Guardar sucursal seleccionada en order meta | ✓ |

## 2026-07-18 — Checkout: sucursal en resumen + layout fixes

| # | Problema | Solución |
|---|----------|----------|
| 1 | El resumen del checkout aparecía muy abajo | Código duplicado creaba `<div>` extra rompiendo el layout. Eliminado el bloque duplicado de `sp-sucursal-selected-info` y `</div>` extra |
| 2 | IDs duplicados en el DOM (`id="sp_sucursal_retiro_field"` en wrapper y en `woocommerce_form_field`) | Wrapper cambió a clase `sp-sucursal-wrap` en vez de ID |
| 3 | Sucursal no aparecía en resumen del checkout (sección Envío) después de seleccionarla en el carrito | El AJAX de WooCommerce en el carrito solo envía el método de envío, no el campo sucursal. Agregado `spBindSucursalChange('sp_sucursal_retiro_cart')` que guarda en sesión via AJAX directo (`sp_save_sucursal_ajax`) |
| 4 | Sucursal no aparecía en resumen del checkout (sección Envío) | Usado hook `woocommerce_review_order_after_shipping` + fragmento AJAX `woocommerce_update_order_review_fragments` + JS polling cada 800ms como fallback |
| 5 | Palabras cortadas en dirección de sucursal (2 líneas) | Agregado `word-break:keep-all;overflow-wrap:break-word` al `<td>` y `<span>` contenedor |

### Nuevos hooks y funciones agregados

- `sp_save_sucursal_ajax()` — endpoint `wp_ajax_sp_save_sucursal` / `wp_ajax_nopriv_sp_save_sucursal`
- `sp_review_order_sucursal()` — muestra fila en resumen checkout
- `sp_sucursal_fragment()` — fragmento AJAX para actualizar la fila
- `spBindSucursalChange(selId)` — listener genérico para selects de sucursal (carrito y checkout)

### Archivos modificados

- `local/functions_current.php` — ~1200 líneas, todas las funciones de sucursales

### Flujo final

1. Carrito: seleccionas "Recoger en local" → aparece selector de sucursal → seleccionas sucursal → se guarda en sesión via AJAX directo
2. Checkout: al cargar, la sesión tiene la sucursal pre-seleccionada + aparece "Sucursal: nombre / dirección" debajo de "Envío" en el resumen
3. Cambio de sucursal en checkout: actualiza inline + fila en resumen vía JS directo + AJAX + polling

## 2026-07-19 — Magnesio: highlights color + registro de workflow colores consistentes

| # | Problema | Solución |
|---|----------|----------|
| 1 | Viñetas highlights en artículo Magnesio (post 21716) usaban lila default `#d8bfe8` en vez del tono lila de la cover image | Seteado `highlight_accent = #A078C8` vía WP-CLI |
| 2 | Líneas horizontales separadoras (`.sp-toc-hr`, `.sp-section-hr`) siempre grises, no se adaptaban al fondo | Agregado soporte para meta `highlight_hr_color` en functions.php — si se setea, cambia el color de borde de ambos `<hr>`. Si no, mantiene gris `#e0d6d0` por defecto |
| 3 | No había registro de que los colores de highlights deben coincidir con los tonos de la cover image | Documentado en pseudo-template-blog-power-rack.md: el prompt de IA ahora incluye hex color exacto, y ese mismo hex debe usarse en `highlight_accent` |

### Nuevos meta
- `highlight_hr_color` — color de las líneas `<hr>` en ToC y entre secciones. Útil para fondos oscuros donde el gris no destaca.
- `highlight_bullet_color` — color de la viñeta ● (más tenue/suave que `highlight_accent`). Si no se setea, usa el mismo `highlight_accent`.

### Archivos modificados
- `local/functions_current.php:876-879` — lógica de colores extendida para incluir `highlight_hr_color`
- `local/functions_current.php` — subido a servidor (funciona en functions.php del child theme)
- `docs/pseudo-template-blog-power-rack.md` — sección de prompts actualizada con ejemplo real + workflow de colores
- `docs/historial-cambios.md` — este registro

### Workflow documentado para artículos futuros
1. Al generar la cover image, especificar en el prompt los hex colors exactos (ej: `#A078C8 lilac purple`)
2. Asignar `highlight_accent` con el mismo hex del prompt
3. Si el fondo (`highlight_bg`) es oscuro, asignar `highlight_hr_color = #ffffff`
4. Si el fondo es claro, no asignar `highlight_hr_color` (usa gris default)
