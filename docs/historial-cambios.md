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

## 2026-07-19 — Documentación: estructura psk-create-product

Se documenta la estructura del proyecto auxiliar `C:\suplementos\psk-create-product\` para creación y subida de productos nuevos.

### Estructura

```
C:\suplementos\psk-create-product\
├── fotos/                         # Imágenes de productos (para fichas nuevas)
│   ├── 608631655486.jpg           # Archivos por SKU/código de barras
│   ├── 650076635196.jpg
│   ├── ... (26 imágenes)
│   ├── combos/                    # Imágenes de combos específicos
│   └── home/                      # Imágenes promocionales para homepage
│       ├── combo-dymatize-iso-100-fruity-pebbles-730g-angry-suppl-creatine-mono.png
│       ├── combo-dymatize-iso-100-fruity-pebbles-730g-raw-pre-workout-orange-399g.png
│       ├── combo-evofusion-intl-choc-pb-4.76-lb-raw-pre-workout-blue-raspberry-618g.png
│       ├── combo-evofusion-intl-choc-pb-PB-4.76-lb+raw+pre-workout-blue-raspberry-618g.png
│       └── combo-iso-total-pack-dymatize-iso-100-fruity-pebbles-730g-bcaa-vms-fruit-punch-30-serv-angry-suppl-creatine-mono.png
│
├── *.py                   # Scripts Python para automatización (200+ archivos)
├── *.php                  # Scripts PHP para ejecución vía WP-CLI
├── *.sh                   # Scripts shell para SSH
├── psk-create-product.py  # Script principal de creación de productos
├── propuesta_sku.csv      # Catálogo de SKUs propuestos
├── propuesta_sku_combos.csv   # SKUs de combos
├── wc_sin_sku.csv         # Productos sin SKU
├── ssh-key-nopass         # Llave SSH para conexión al servidor
└── (no hay README — documentación implícita en nombres de scripts)
```

### Reglas de uso

- `fotos/` — imágenes para asignar a productos nuevos (subir a WooCommerce)
- `fotos/home/` — banners promocionales para la homepage (combos)
- `fotos/combos/` — imágenes para fichas de productos combos
- Los scripts `.py` se ejecutan desde esta carpeta, no desde `stock-suplementos`
- La llave `ssh-key-nopass` es la misma que en `stock-suplementos`
- `fotos/home/prompts-titulos-nanobanana.md` — prompts para insertar títulos bilingües (EN+ES) en imágenes de combos

## 2026-07-20 — Fix: visor de sucursales en productos grouped (combos)

| # | Problema | Solución |
|---|----------|----------|
| 1 | El visor "Disponible para retiro en" mostraba "Solo disponible para Delivery" en todos los combos (productos grouped), aunque sus productos componentes tuvieran stock en sucursales | `sp_show_sucursal_stock()` solo manejaba `variable` y `simple`. Agregado `$is_grouped` check: si es grouped, obtiene los children con `$product->get_children()` y suma el stock de cada uno |

### Archivos modificados
- `local/functions_current.php:290` — línea `$product_ids` extendida para incluir grouped

## 2026-08-11 — Creación de 3 combos nuevos (productos grouped)

Del PDF `combos-precio-y-nuevos.pdf` (combos 1, 2 y 4; el 3 queda en pausa: Primeval Labs + Pre-Work Raw BUM hasta definir producto/precio):

| ID | Combo | Precio | Hijos |
|----|-------|--------|-------|
| 21960 | Creatina VMS 80 Serv + Glutamina VMS 80 Serv | $24.99 | 21455 + 21460 (simples) |
| 21961 | Mutant Mass Gainer Extreme 2500 + Creatina Angry 60 Serv | $44.99 | 21391 (variable 3 sabores) + 21456 |
| 21962 | Proteína Primeval Labs 4.8 lb + Creatina Angry 60 Serv | $69.99 | 18977 (variable CC/Vanilla) + 21456 |

- Estructura replicada del combo 21633: `_combo_price`, `_children`, `_combo_variations_<hijo>` vacío (todas las variaciones), `_manage_stock=no`, `_stock_status=instock`.
- Contenido HTML (template combo del agente) + excerpt + categorías (Combos/Promociones + categorías de los hijos).
- Imágenes desde `fotos/combos/` importadas a `uploads/2026/08/` (adjuntos 21963/21964/21965). Ojo: no usar `wp media import --skip-copy` (deja URLs apuntando a /tmp).
- Verificado: URLs 200, precio+ahorro visibles, og:image correcta, add-to-cart a precio de combo, retiro en sucursal OK (SP_DEBUG selected=1 method=local_pickup + fila review).

### Archivos
- Temporales: `Temp\opencode\combo_contents\NEW-*.content.html` (contenidos de los 3 combos), `create_new_combos.php`.

## 2026-08-11 — CTA WhatsApp de fichas de producto: estructura oficial + registro para reuso
La estructura del CTA pasó por 2 correcciones en los combos 21960/21961/21962:
1. Botón verde sólido → enlace verde con icono (inline-flex, sin texto).
2. Añadido contenedor gris + flex interno (estructura de los combos existentes 21521/21516).
3. Alineación vertical en desktop: `<span>` con `display:inline-flex; align-items:center; justify-content:center;` y el texto en la **misma línea** que la apertura del span (evita el `<br />` que inserta wpautop por saltos de línea y que empujaba el texto).

### Reglas (NO romper)
- No usar botón sólido verde (`background:#25D366; color:#fff; padding...; border-radius:50px`).
- No escribir texto "Escríbenos por WhatsApp" dentro del enlace (solo icono).
- Texto siempre en la misma línea que el `<span>` para evitar `<br />` de wpautop.
- URL: `Hola%2C` (coma) + `%20` espacios + `%2B` para el "+".

### Archivos
- Guía de referencia: `docs/cta-whatsapp-productos.md` (snippet HTML completo + reglas + ejemplo real 21960).
- Template del agente actualizado: `~/.config/opencode/agents/contenidos-ecommerce/sp/template-descripcion-combo.md` → sección "CTA WhatsApp".

## 2026-08-11 — Combo 3 del PDF: Proteína Primeval Labs 4.8 lb + BUM Esencial No-Stim Blue Raspberry (ID 21982)

| # | Campo | Valor |
|---|-------|-------|
| 1 | ID | 21982 |
| 2 | Título | Proteína Primeval Labs 4.8 lb + BUM Esencial No-Stim Blue Raspberry |
| 3 | Slug | `proteina-primeval-labs-4-8-lb-bum-esencial-no-stim-blue-raspberry` |
| 4 | Precio | $74.99 (suma regular $124.98, ahorro ~$50) |
| 5 | Hijos | 18977 variable (CC/Vanilla) + 21457 simple (BUM Esencial No-Stim Blue Raspberry) |
| 6 | Categorías | Combos(284), Promociones(219), Proteína de Suero(246), Proteínas(18), Pre-Entrenamientos Sin Estimulantes(263) |
| 7 | Imagen | adj 21981 `uploads/2026/08/primeval-bum-combo.png` (origen: `C:\suplementos\psk-create-product\fotos\combos\Primeval Labs 5lb + Pre-Work Raw Bum - Blueberry u.png`) |

- Estructura replicada de los combos previos (`_combo_price`, `_children`, `_manage_stock=no`, `_stock_status=instock`, contenido HTML con CTA WhatsApp oficial).
- Verificado: HTTP 200, precio $74.99 + "Ahorra $49.99", add-to-cart AJAX OK, retiro en sucursal OK (`SP_DEBUG selected=1 method=local_pickup` + fila `sp-sucursal-review`). No se tocó `functions.php` ni `combo-price.php`.

## 2026-08-11 — Fix ahorro de combos: `combo_get_children_total()` ahora usa precio actual

- **Síntoma**: en el combo 21982 la suma de los productos es $109.98 (proteína $59.99 en oferta + BUM $49.99), pero el badge decía "Ahorra $49.99" porque `combo_get_children_total()` sumaba precios regulares ($74.99 + $49.99 = $124.98).
- **Cambio** (`combo-price.php`): usar `get_price()` en hijos simples y `min($prices['price'])` en variables, en vez de `regular_price`.
- **Efecto**:
  | Combo | retail antes | retail después | Badge antes | Badge después |
  |-------|-------------|---------------|-------------|---------------|
  | 21982 | 124.98 | 109.98 | Ahorra $49.99 | Ahorra $34.99 |
  | 21962 | 99.98 | 84.98 | Ahorra $29.99 | Ahorra $14.99 |
  | 21960 | 59.98 | 59.98 | Ahorra $34.99 | Ahorra $34.99 |
  | 21516 | 145.98 | 145.98 | Ahorra $50.99 | Ahorra $50.99 |
- Backup: `wp-content/mu-plugins/combo-price.php.bak-20260811-ahorro`. Verificado checklist retiro en sucursal OK tras el cambio.

## 2026-08-11 — Sustitución de imágenes en 2 productos

| SKU | Producto | Adj viejo | Adj nuevo |
|-----|----------|-----------|-----------|
| 810121050286 | 21454 EVOFUSION CHOC. PEANUT BUTTER 4.78 LBS EVOGEN (simple) | 21565 `2026/07/810121050286.jpg` | 21986 `2026/08/evofusion-chocolate-peanut-butter-4-78-lbs-evogen.jpeg` |
| 650076635257 | 19064 VMS Bios Active 5 lb - Strawberry (variación) | 21464 `2026/07/650076635257.jpg` | 21987 `2026/08/vms-bios-active-5-lb-strawberry.jpg` |

- SEO creado (title=slug + alt descriptivo), adjuntos viejos eliminados con `wp_delete_attachment(..., true)`.
- Verificado: og:image + main + variación apuntan a la nueva; viejas 404.

