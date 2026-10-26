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

## 2026-08-11 — Combo 4: Proteína Primeval Labs 4.8 lb + Raw Pre Workout Orange (ID 21989)

| # | Campo | Valor |
|---|-------|-------|
| 1 | ID | 21989 |
| 2 | Título | Proteína Primeval Labs 4.8 lb + Raw Pre Workout Orange |
| 3 | Slug | `proteina-primeval-labs-4-8-lb-raw-pre-workout-orange` |
| 4 | Precio | $74.99 (suma actual $99.98, ahorro $24.99) |
| 5 | Hijos | 18977 variable (CC/Vanilla) + 9602 simple (RAW-ESSENTIAL - ORANGE, SKU 850039445668) |
| 6 | Categorías | Combos(284), Promociones(219), Proteína de Suero(246), Proteínas(18), Sin Categoría(15) |
| 7 | Imagen | adj 21988 `uploads/2026/08/proteina-primeval-labs-4-8-lb-raw-pre-workout-orange.jpg` (SEO title + alt aplicados) |

- Estructura replicada de los combos previos (`_combo_price=74.99`, `_children`, `_manage_stock=no`, `_stock_status=instock`, contenido HTML con CTA WhatsApp oficial).
- Verificado: HTTP 200, precio $74.99 + "Ahorra $24.99" (datalayer "Ahorras $24.99"), selector sabores `combo_variation_id[18977]` (CC/Vanilla), add-to-cart AJAX OK, retiro en sucursal OK (`SP_DEBUG selected=1 method=local_pickup` + fila `sp-sucursal-review`). Aparece en `/promociones/combos/`. No se tocó `functions.php` ni `combo-price.php`.

## 2026-08-11 — Sitemap: URLs de los combos nuevos añadidas a `product-sitemap.xml` (Rank Math)

- **Situación**: los 5 combos (21960/21961/21962/21982/21989) no aparecían en `product-sitemap.xml` (163 URLs vs 164 productos publicados). Sin noindex ni exclude (Rank Math), el cache del sitemap estaba stale.
- **Fix**: `wp_cache_flush()`, borrado de transients/options `rank_math_sitemap_*` y `\RankMath\Sitemap\Cache::invalidate_storage()` (archivos cacheados → 0).
- **Resultado**: `product-sitemap.xml` regenerado con **165 URLs** incluyendo los 5 combos; `sitemap_index.xml` con lastmod actualizado (`2026-08-12T01:46:27+00:00`). Verificado por loopback PHP y curl externo (HTTP 200, headers no-store).


## 2026-08-11 � Fix schema.org combos: hasVariant -> hasPart (error critico GSC)

- **Problema**: GSC reportaba error critico "El tipo de objeto del campo <parent_node> no es valido" en el combo 21960 (hijos CREATINA/GLUTAMINA). El schema del combo usaba hasVariant (solo valido para variaciones del mismo producto), no para combos de productos distintos.
- **Cambio**: sp_output_combo_structured_data() en functions.php del child ahora emite 'hasPart' en vez de 'hasVariant' (mantiene hijos como @type: Product completos con offers). Backup: functions.php.bak-20260811-schema-hasPart.
- **Verificado**: combo 21960 y 21989 sirven hasPart (2 hijos), sin hasVariant, JSON valido, offers del combo intactos. Checklist retiro en sucursal OK (SP_DEBUG selected=1 method=local_pickup + fila sp-sucursal-review). local/functions_current.php sincronizado.

## 2026-08-11 � Mailchimp: desactivacion doble opt-in (form 417)

- Form 417 -> _mc4wp_settings: double_optin 1->0. Nuevos suscriptores quedan subscribed de inmediato.
- Contexto: 2 nuevos (henrybatista24@icloud.com 11-ago, xeniayazmin27@gmail.com 06-ago) permanecian pending sin confirmar el opt-in.

## 2026-08-11 � Combo 21961 precio 44.99 -> 54.99

- _combo_price 54.99, ahorro 19.99 (retail 74.98). Descripcion actualizada (~\ -> ~\, precios \.99). Verificado en vivo.

## 2026-08-13 � Schema home: sameAs a Google Maps en Store/Organization + diagnostico GLA

- functions.php filtro rank_math/json_ld: en front page se a�ade sameAs con URL Maps de El Cangrejo al Store (conservando Facebook). Backup functions.php.bak-20260813-sameas.
- Verificado: home sameAs doble (FB + Maps), checklist retiro OK.
- Diagnostico Firecrawl: Merchant Center conectado (GLA 3.9.0) pero SIN sync de productos (no hay _wc_gla_* meta, no hay jobs sync_products). Google Ads invite sin aceptar (account_access=-1). GMB API = falso positivo (no existe en codigo; sucursales ya tienen HealthAndBeautyBusiness + sameAs Maps).

## 2026-08-13 � GUIA-COMPLETAR-GMC-SYNC.md: v�a sin m�todo de pago

- Reescrita: Opcion A (recomendada) solo Merchant Center sin Ads ni tarjeta (free listings), Opcion B aceptar invite sin cobro. El feed de MC no requiere billing; metodo de pago solo para campa�as pagadas.

## 2026-08-20 � Fix cron sync diario (faltaba wc_export_ssh.php en servidor)

- El cron diario (02:00, psk-sync) fallaba desde 17-08 porque faltaba ~/wc_export_ssh.php (export auxiliar). Repuesto desde local/wc_export_ssh.php. Dry-run OK (704 articulos PSK -> 387 productos WC, 163 cambios).
- Documentado Troubleshooting en docs/proceso_actualizacion_diaria.md.

## 2026-08-21 � Fix critico: Elementor 4.2.3 corrupto (HTTP 500) -> reinstalado

- La auto-actualizacion de Elementor a 4.2.3 dejo el directorio core/data/ vacio (clase Elementor\Data\V2\Manager ausente) -> fatal + HTTP 500 y .maintenance (503).
- Reinstalado Elementor 4.2.3 desde ZIP oficial de wordpress.org (backup del directorio corrupto y luego eliminado). Clase restaurada en data/v2/manager.php. Eliminado .maintenance.
- Verificado: home/cart/producto/sucursales 200, checkout 302 normal, wp-cli OK, flujo retiro OK (SP_DEBUG selected=1 method=local_pickup + fila review). NO se toco child theme ni funciones sp_*.
- Recomendacion: limitar auto-updates de Elementor o validar integridad post-update.
- 2026-08-21: diseno roto post-reinstalacion Elementor por combined-css viejo del SG Optimizer. Purgado cache SG (wp sg purge + wp cache flush); combined-css regenerado con estilos Elementor. Verificado home y paginas internas HTTP 200.
## 2026-08-21 � Bloqueadas auto-actualizaciones de WordPress

- Creado mu-plugin sp-bloquear-auto-updates.php (wp-content/mu-plugins) que fuerza false en auto_update_plugin/core/major/minor/translation/theme. Verificado en vivo: BLOQUEADO para plugins, themes, core y traducciones.
- Vaciada la opcion auto_update_plugins (backup en /tmp/backup_auto_update_plugins_20260821.json). Copia local en local/sp-bloquear-auto-updates.php.
- Motivo: Elementor 4.2.3 se auto-actualizo y quedo corrupto (HTTP 500) el 2026-08-21.

## 2026-08-31 � Combos: _combo_price fijo, ahorro actualizado por subida de hijos IMPULSE/VMS

- Fix cache _price: 69 variaciones + 4 padres (VMS/Bio5/Bio6/Vegana) desfasados por post meta update directo del script (no recalcula _price). Ahora front y badge usan precios nuevos.
- Contenidos de 7 combos (21512,21514,21517,21520,21522,21643,21647): retail y ahorro actualizados (ej. VMS+Creatina .99, Bio6+Nutrex .99, Triple Stack .98). Backup en /tmp.
- Verificado: badge y textos .99, sin cifras viejas, retiro OK.
## 2026-08-31 � Auditoria completa combos + automatizacion

- Auditados los 27 combos: corregidos 4 adicionales (21525 IsoJect+RawPre+Creatina ahorro \.98/retail \.97; 21660 Elite ahorro \.98/retail \.97; 21524 ahorro \.98; 21515 ahorro \.99). Todos _combo_price intactos.
- Creado skill combo-sync-ahorro y script sp_auditar_combos.php (audita cache _price + cifras de fichas). Integrado al cron: run_sync.sh ejecuta el fix de cache _price y reporta pendientes en cron.log.

## 2026-09-04 � T&C nuevo publicado en /terminosycondiciones/

- Sustituido el contenido viejo (pagina 9, 22/03/2025) por el nuevo (08/08/2026, indice 13 secciones) creando pagina nueva ID 22030 con mismo slug. La 9 quedo draft. Backup en /tmp/backup_tc_pagina9_20260904.json.
- Leccion: reemplazar el _elementor_data de una pagina publicada no se refleja en el front por cache (elementor_element_cache + SG/CDN); recrear la pagina con meta Elementor completo (_elementor_edit_mode=builder, _elementor_template_type=wp-page, versiones) + purgar caches resuelve.
- Verificado desktop/movil HTTP 200.
## 2026-09-04 � Fix T&C: CSS crudo visible

- El sanitizer de Elementor (text-editor) elimina etiquetas <style> del contenido -> el CSS se mostraba como texto. Limpiado el editor y movido el CSS a un mu-plugin (sp-tc-css.php) que lo inyecta en <head> solo en /terminosycondiciones/. Verificado desktop/movil.
## 2026-09-04 � Fix corte de palabras en tabla T&C (metodos de pago)

- En sp-tc-css.php: word-break:normal!important + overflow-wrap:normal!important + hyphens:none en th/td + table-layout:auto. Ya no se cortan palabras en ninguna columna.
## 2026-09-04 � Checkbox obligatorio Terminos y Condiciones en fichas de producto

- Nuevo mu-plugin sp-tc-agree.php: checkbox 'He le�do y acepto los Terminos y Condiciones' (enlace a /terminosycondiciones/) debajo del boton A�adir al carrito en simple/variable/combo. Bloqueo JS + server-side.
- Clave tecnica: los combos con _combo_price usan handler custom 'combo' que bypasea woocommerce_add_to_cart_validation. Se intercepta woocommerce_add_to_cart_handler con prioridad 20 (despues de combo-price) para forzar handler 'grouped' sin checkbox (asi pasa por validacion y muestra el error). No se modifico combo-price.php ni functions.php del child.
- Verificado simple/variable/combo: sin checkbox -> error + carrito vacio; con checkbox -> OK. Checklist retiro OK.
## 2026-09-07 � Fix imagenes de combos (tama�os faltantes en srcset)

- Combos 21960, 21962, 21961 (subidos 11-08): sus PNG tenian metadata con tama�os pero faltaban 4 archivos fisicos (-1024x1024, -150x150, -1070x510, -100x100) -> srcset con 404, imagen podia no verse segun viewport. Regenerados attachments 21963/21965/21964 con wp media regenerate. Auditados los 27 combos: todos OK.
## 2026-09-07 � Fix CTA WhatsApp en fichas de combos (movil)

- En 27 combos grouped: reescrito el bloque CTA del post_content. Icono WhatsApp (viewBox ok) arriba centrado + texto en <p> fluido debajo (antes un <span> con inline-flex partia el texto en 3 bloques en movil y el logo no se veia). Backup /tmp/backup_cta_todos_20260907.json.

## 2026-09-11 � Cierre lote 26 productos nuevos

- 26/26 publicados (EVOGEN 9, LANDERFIT 7, MUTANT 2, RAW 8): imagenes, contenido template, SEO, stock por sucursal.
- Perfiles validados con etiqueta real: EVP AQ, Omega 3, CLA 2000, Vitamina C. Pendientes de validar: Fiber, Xtreme, ISO Surge, Proteina RAW, RAW Glycerol, Lipo Stim Free, Testolander, Lipo Thermogenic.
- RAW Glycerol outofstock (bodega Via Brasil sin distribuir).

## 2026-09-17 — Fix carrusel home (desktop): botones 404 -> /promociones/combos/

- Home ID 18625 (Elementor `_elementor_data`). El carrusel superior de escritorio (widget `nested-carousel`, contenedor `4a92d4` con `hide_mobile`) tenia 2 botones "Comprar Ahora" que daban 404:
  - widget `17c3ee` (Proteina + Creatina Primeval Labs) -> /product/primeval-labs-proteinas-primeval-labs-creatinas/
  - widget `171e63` (Proteina + Creatina Nutrex) -> /product/proteinas-iso-nutrex-2lbs-creatina-nutrex-60-serv/
- Cambio: ambos `link.url` -> https://suplementospanama.net/promociones/combos/
- Metodo: backup del meta (/tmp/home_el_backup_20260917-0000.json, 174378 bytes, md5 99788c41b25fb142b0ddafa94ba73e2d + copia en ~/); edicion con update_metadata('post',18625,'_elementor_data', wp_slash($json)) (NUNCA update_post_meta); purga SG + object cache + Elementor CSS.
- Verificacion independiente (contra HTML servido): 2 hrefs a /promociones/combos/ en el carrusel desktop; URLs viejas = 0 ocurrencias; /promociones/combos/ = HTTP 200; carrusel movil (a58a0a) sin cambios; boton VMS (c51113) intacto.
- Pendiente de decision: el contenedor 4a92d4 tiene hide_mobile pero no hide_tablet -> el carrusel tambien se ve en tablet.
- Ejecutado con el harness de agentes: supervisor (orquesto/verifico) + executor (edito). Estado en docs/harness-estado.md.

## 2026-09-17 - Playbook Meta Pixel/Dataset + CAPI + Whaticket (para Claude)

- Creado docs/playbook-meta-pixel-capi-whaticket.md: instruccion maestra (16 secciones) para que Claude (con acceso a la cuenta Meta de SP) guie/ejecute la implementacion del Pixel/Dataset, Conversions API, eventos/KPIs y la integracion con el CRM Whaticket.
- Cubre: inventario/accesos (Fase 0), Dataset+CAPI, modelo de eventos y KPIs (Contact/Lead/LeadCalificado/Purchase), captura de click ID (fbclid/fbc/fbp y ctwa_clid de Click-to-WhatsApp), integracion Whaticket->CAPI, AEM, conversiones personalizadas, audiencias, payloads JSON de ejemplo, checklist de aceptacion y guardrails de privacidad (PII hasheada).
- KPIs: CPL, CPQL, CPA, ROAS, tasa de calificacion, tasa de cierre.
- Punto critico: el tipo de conexion de Whaticket (WABA Cloud API vs Baileys) determina la disponibilidad de ctwa_clid y la precision de atribucion; debe resolverse en Fase 0.

## 2026-09-17 - WooCommerce: metodo de pago manual "Transferencias" (TEMPORAL)

- Gateway BACS (id `bacs`) renombrado de "Deposito Bancario" a "Transferencias" y habilitado (`enabled=yes`).
- Opcion modificada: `woocommerce_bacs_settings`. Backup: /tmp/bacs_backup_20260917.json = {"enabled":"no","title":"Deposito Bancario","description":"","instructions":"","account_details":[]}.
- Verificado: `wp wc payment_gateway list --user=3` -> `bacs | Transferencias | 1`.
- Otros metodos activos: paguelofacil_gateway, yappy_payment.
- Cache purgada (SG + object cache).
- ES TEMPORAL (para pruebas). Para revertir: `wp option patch update woocommerce_bacs_settings enabled no` (o restaurar el JSON del backup).

## 2026-09-17 - WooCommerce: deshabilitado metodo "Transferencias" (fin de pruebas)

- Gateway BACS deshabilitado (`enabled=no`). El gateway sigue existiendo (id `bacs`, titulo "Transferencias") pero ya no aparece en checkout.
- Verificado: `wp wc payment_gateway list --user=3` -> `bacs | Transferencias |` (sin enabled).
- Metodos activos: paguelofacil_gateway, yappy_payment. Cache purgada.
- Backup original disponible en /tmp/bacs_backup_20260917.json.

## 2026-09-21 - Checkout: validacion Cedula/Pasaporte/RUC acepta formatos panamenos (ej. E-8-201079)

- Problema: el campo billing_doc_identificacion (Checkout Form Designer / THWCFD) tenia validate=["number"] -> is_numeric() rechazaba cedulas con letras/guiones (E-8-201079, 8-123-456, PE-8-1234).
- Fix 1: se quito la validacion "number" del campo (opcion wc_fields_billing -> validate: []). Backup: /tmp/wc_fields_billing_backup_20260921-174420.json.
- Fix 2: mu-plugin nuevo wp-content/mu-plugins/sp-cedula-validation.php, hook woocommerce_after_checkout_validation (prioridad 20): admite letras/numeros/guiones (sin espacios ni simbolos, sin guiones dobles ni en extremos), minimo 4 digitos, longitud 5-25.
- Casos probados: E-8-201079, 8-123-456, PE-8-1234, N-20-1234, 8-123-4567 -> VALIDO; 123*, 8-123-456*, 8--123, -8-123, hola, 123, 8-12, "PE 8 1234" -> INVALIDO.
- Verificado: php -l sin errores, home HTTP 200, validate=[] en el campo. Copia local en el repo: sp-cedula-validation.php.

## 2026-09-21 - WooCommerce: reactivado metodo "Transferencias" (nueva prueba)

- Gateway BACS habilitado de nuevo (`enabled=yes`, titulo "Transferencias") para una prueba.
- Verificado: `wp wc payment_gateway list --user=3` -> `bacs | Transferencias | 1`. Cache purgada.
- Recordatorio: es temporal; se desactiva con `wp option patch update woocommerce_bacs_settings enabled no`.

## 2026-09-21 - Checkout: ocultado checkbox "Enviar a una direccion diferente" (segundo formulario)

- El checkout mostraba, justo debajo del email, el checkbox #ship-to-different-address ("Enviar a una direccion diferente?") que desplegaba el formulario de envio (woocommerce-shipping-fields__field-wrapper); los clientes se confundian.
- Fix: mu-plugin nuevo wp-content/mu-plugins/sp-checkout-hide-ship-different.php -> inyecta CSS en checkout ocultando #ship-to-different-address y .woocommerce-shipping-fields__field-wrapper (display:none !important).
- Verificado: php -l OK, checkout HTTP 200, style presente. El pedido usara la direccion de facturacion como envio (comportamiento por defecto al no marcar).
- Copia local en el repo: sp-checkout-hide-ship-different.php.

## 2026-09-21 - WooCommerce: deshabilitado "Transferencias" (nuevamente)

- Gateway BACS deshabilitado (`enabled=no`) mientras se prueba el checkout. Titulo sigue "Transferencias" (oculto en checkout).
- Verificado: `wp wc payment_gateway list --user=3` -> `bacs | Transferencias |` (sin enabled). Cache purgada.
- Activos: paguelofacil_gateway, yappy_payment.

## 2026-09-21 - Eliminados 2 pedidos de prueba (Transferencias) y restaurado stock

- Pedidos eliminados (force): #22204 (Andres Castillo; Amino K.E.M. Citrus Candy; variacion 19255; retiro SP El Cangrejo) y #22203 (Andres Castillo; BCAA 12:1:1 VMS Grape; variacion 21547; retiro SP Atrio Mall).
- Ambos tenian _order_stock_reduced=yes -> WooCommerce habia descontado stock GLOBAL. Restaurado con wc_increase_stock_levels: 19255 6->7; 21547 59->60.
- El stock por sucursal (_sucursal_N_stock) NO se descuenta al crear pedidos (no existe codigo que lo haga), por lo que no se modifico.
- Verificado: los pedidos no existen; stock 19255=7 y 21547=60; status instock. Cache purgada.

## 2026-09-23 - Combo Elite Performance Stack (21660): precio 106.99 -> 115.99 + contenido

- _combo_price: 106.99 -> 115.99. Hijos suman 195.97 (ProLive Bio6 6lb 115.99 + BUM Essential No-Stim 49.99 + Glutamina VMS 29.99).
- Nuevo ahorro: $79.98 (195.97 - 115.99), ~40.8%.
- Contenido (excerpt + descripcion) actualizado: $88.98->$79.98, $106.99->$115.99, 42%->40%.
- Schema de combos (sp-combo-schema) dinamico: muestra "Ahorras $79.98 al comprar este combo vs. comprar los productos por separado".
- Backup del estado previo: /tmp/combo_21660_backup_20260923-211016.json.
- Verificado en front: precio 115.99 (8 refs), 106.99=0, 88.98=0, ahorro $79.98 (12 refs). Cache purgada.

## 2026-09-26 - Rendimiento: arreglado el cache de SiteGround (causa raiz)

- Diagnostico: home tardaba ~8s TTFB porque el frontend enviaba Set-Cookie: PHPSESSID -> SG Optimizer saltaba el cache (x-proxy-cache SKIP_CACHE_SET_COOKIE) -> cada visita regeneraba PHP.
- Causa: PixelYourSite (pixelyoursite/includes/class-pys.php) llamaba session_start() en cada visita normal del frontend (solo se salta en AJAX/REST/robots).
- Fix aplicado:
  1. mu-plugin nuevo wp-content/mu-plugins/sp-pys-no-session.php -> add_filter('pys_skip_session','__return_true') -> PYS ya no inicia sesion PHP.
  2. Desactivada Order Attribution de WooCommerce (woocommerce_feature_order_attribution_enabled yes->no; backup /tmp/oa_backup.txt).
- Resultado (desde el servidor): TTFB con cache ~0.03-0.05s (antes ~8s); request 1 (cache miss tras purgar) ~10.7s (regenera). Ya no hay Set-Cookie PHPSESSID. Home OK (HTTP 200).
- Copia local del mu-plugin en el repo: sp-pys-no-session.php.
- Pendientes recomendados (siguiente paso): limpiar Action Scheduler (16.7k acciones + 50k logs), revisar 8 acciones failed diarias, reducir 35 plugins activos, aligerar home (3.1MB HTML / 365 bloques producto), limpiar tabla sgs_log_events (11MB).

## 2026-09-26 - Rendimiento fase 2: limpieza Action Scheduler + logs + verificacion

- Acciones failed eran hook fetch_patterns (Rank Math) que fallaba a diario (8 fallidas) -> desprogramado con as_unschedule_all_actions('fetch_patterns').
- Purgadas acciones complete/failed/cancelled + sus logs: actions 16760 -> 30, logs 50226 -> 37. Se conservaron pending (27) y running (0).
- DB optimizada (wp db optimize) + ANALYZE de las tablas AS. Tamano DB: 140.1 MB -> 98 MB (-42 MB).
- Verificado: carrito OK (add_to_cart 200 con campo T&C), checkout HTTP 200 con campos (billing_email, ship-to-different-address, woocommerce-checkout), home con cache TTFB ~0.03s.
- Siguiente candidato de limpieza: tabla sgs_log_events (11.1 MB, 26k filas) de SG Security.

## 2026-09-26 - Optimizacion fase 3: logs de SG Security

- sgs_log_events registraba cada visita/bot (27.677 filas, 11 MB) con retencion de 12 meses (sgs_activity_log_lifetime default 12) -> bloat + escrituras continuas en DB.
- Accion: truncadas sgs_log_events y sgs_log_visitors (audit logs, no criticos); retencion reducida a 1 mes (update_option sgs_activity_log_lifetime=1).
- Resultado: DB 98 -> 85.8 MB. Con fase 2 (Action Scheduler) el total bajo de 140 MB -> 85.8 MB (-54 MB en la sesion).
- Verificado: events reales 1, visitors 1, home con cache TTFB ~0.028s.
- Pendiente recomendado: evaluar desactivar el activity log de visitas (sg_security_disable_activity_log=1) si el trafico de bots sigue generando muchos eventos.

## 2026-09-26 - Optimizacion fase 4: desactivacion de plugins de bajo uso

- Desactivados (8): google-listings-and-ads, automatic-translator-addon-for-loco-translate, loco-translate, make-column-clickable-elementor, wordpress-starter, siteground-migrator, woo-update-manager, woocommerce-legacy-rest-api.
- jetpack NO se pudo desactivar (sigue activo). woo-smart-quick-view y woo-smart-wishlist se reactivaron tras auto-desactivarse.
- Incidente controlado: un error de proceso desactivo TODOS los plugins (flag --all). Se restauro activando WooCommerce primero con --skip-themes (el tema usa WC_Shipping_Zones en functions.php:379, que falla sin WooCommerce). Quedaron 28 plugins activos.
- Bootstrap: 245.7MB / ~1.6s -> 228.4MB / ~1.4s. TTFB cache ~0.05s. Render fresco (cache miss) sigue ~10s -> el cuello de botella es el home (365 tarjetas producto, carruseles, HTML 3.1MB), no el bootstrap.
- Proximo paso recomendado: aligerar el home (menos productos/secciones), no seguir desactivando plugins.

## 2026-09-26 - Optimizacion home: limit de carruseles de productos 200 -> 12

- Los 2 widgets nutritix-products del home (f77ad6, 42bbbd) tenian limit=200 cada uno -> consultaban 400 productos en el render (causa del ~1 minuto de las listas en movil).
- Cambio en _elementor_data del home (18625): limit 200 -> 12 en ambos (update_metadata + wp_slash; backup /tmp/home_el_backup_before_limit_20260926-172456.json).
- Resultado: render fresco ~10s -> ~4s; HTML 3.1MB -> 616KB; tarjetas 365 -> 47; TTFB cache ~0.03s. Home HTTP 200.
- El widget woocommerce-products (89c21b) consulta por 6 categorias (limit por defecto) - pendiente de revisar si tambien conviene acotar.

## 2026-10-02 - Verificacion retiro en sucursal (carrito) — reporte "no aparece el select"

- Reporte: "en el carrito no aparece la opcion de seleccionar sucursal para retiro" (funcionalidad protegida). Se diagnostico a fondo.
- Codigo desplegado (child `functions.php`) INTACTO: presentes todas las funciones `sp_*` y el JS inline. md5 `ed6779fdc763209f7e5adfb54668b4a3` (identico a `local/functions_current.php` del repo git).
- Regresiones conocidas OK: BUG C -> `if (!isPickup) return` presente, `!isPickup ||` ausente; BUG D -> sin listener directo `spBindSucursalChange`.
- Shipping: zona "Panama Centro" con `flat_rate` + `local_pickup` habilitados.
- Metas de sucursal OK: simples (9190, 21456, 21457, 22135) con `_sucursales_disponibles`; variaciones (p.ej. 22159) con meta. Combos grouped usan hijos via `sp_get_cart_item_ids()`.
- Simulacion de carrito (`wp eval-file`): simple 9190, combo 21516, combo 21961 y variable 19364 -> `sp_get_valid_sucursales_for_cart()` = `[1,5,6,7,8,10]` y `sp_cart_sucursal_field()` SI renderiza el `<select id="sp_sucursal_retiro_cart">` (oculto por defecto; el JS lo muestra al marcar "Recoger en local").
- Conclusion: el retiro en sucursal NO esta roto. El select aparece al seleccionar "Recoger en local" en el carrito. Verificado simple/combo/variable (flujo carrito). Codigo del repo = codigo desplegado.

## 2026-10-06 a 2026-10-10 - Sesion: Online Sale + productos nuevos + NUEVO + footer + SEO creatina + fixes sucursales

### Online Sale (home / campana)
- Pagina "Black Weekend" renombrada a **Online Sale SP** (slug `online-sale-sp`, titulo "Online Sale SP"); **301** de `/black-weekend/` -> `/online-sale-sp/` (mu-plugin `sp-redirect-black-weekend.php`).
- Menu principal: item **ONLINE SALE** -> `/online-sale-sp/` (antes BLACK WEEKEND); item **INICIO** arreglado (apuntaba a la pagina vieja 18625 en borrador -> 404) ahora -> home y en primera posicion.
- Hero del home: badge/subtitulo naranja cambiado a **"MAS POWER SIN EXCUSAS >>"**.
- Header de la pagina Online Sale: titulo **"DESCUENTOS EXCLUSIVOS SI COMPRAS AQUI"** y badge (arriba) **"Facil, Rapido y pagas menos"**.
- Banners sustituidos por los de `C:\suplementos\home_nuevo\banner-online-sale` optimizados (JPG q82 + WebP q80, 3 medidas 1920x500 / 1200x480 / 1080x720) sobre `assets/banner-bw/`. Alt actualizado. `sp-dev-nocache.php` actualizado al slug nuevo.

### Productos nuevos (2) creados
- `745560722091` -> **BCAA + Glutamina Pina Colada - Impulse Suplement - 30 Serv** (ID 22273, $29.99).
- `811662020172` -> **Creakong Creatina - Mutant - 75 Servings** (ID 22275, $25.99).
- Cada uno: ficha (contenido del template), excerpt, imagen en Media (SEO alt/slug), categorias, marca (product_brand), metas Rank Math, stock global + por sucursal (`_sucursal_*`, `_sucursales_disponibles`). Entregables de agentes en `local/content_*|excerpt_*|marketing_*|seo_*|schema_*`.

### Badge NUEVO
- mu-plugin **`sp-nuevo-badge.php`**: marca con "NUEVO" (fondo naranja `#F65000`) los productos creados en los ultimos **14 dias**, en home (tarjetas + reel "Lo que mas nos piden"), ficha y grid de tienda/categoria (via clase `sp-es-nuevo` en el `<li>` y `woocommerce_post_class`).
- Los productos nuevos se fuerzan al inicio del reel "Lo que mas nos piden".

### Sync / combos
- `daily_stock_update.py`: **excluidos de precio** los SKUs `842595106619`, `842595121650` (C4 Ultimate Energy Carb WWP Pack 12) y precio corregido a **$58.99**.
- mu-plugin **`sp-combo-stock-sync.php`**: mantiene el `_stock_status` de los combos (grouped) alineado con sus hijos; integrado al sync diario (`run_sync.sh`). Home y `/promociones/combos/` ya no muestran combos agotados (IsoJect).

### Footer
- mu-plugin **`sp-footer-form-css.php`**: formulario de suscripcion MC4WP full-width y centrado en movil (el widget Elementor tenia `__width-auto`).

### SEO - categoria /creatina/
- Optimizada para **"creatina en Panama"**: Rank Math title/description/focus/OG, intro (~204 palabras) en el term description, H1 real + FAQ visible, schema `sp-schema-creatinas.php` (CollectionPage + ItemList + Store + 6 sucursales; elimina el ItemList roto de Rank Math).
- Subcategorias (259/260/288) con metas formato+local (sin canibalizar el generico). Enlaces internos: `/proteinas/` -> `/creatina/`.
- (Pendiente sugerido: medir en GSC 30/60/90 dias.)

### Sucursales (hub y ficha)
- Reel **"Suplementos recomendados para ti"** homologado: renderizado en **servidor** en hub (`page-sucursales.php`) y ficha (`page-sucursal-single.php`) con productos/precios/imagenes reales; se elimino el array hardcodeado del JS (`PRODUCTS`) y `initProductReel()` ahora solo muestra/oculta por filtro.
- Hub **"Encuentra tu sucursal"**: miniatura (imagen hero) por sucursal dentro de la tarjeta, alineada con la linea de estado en desktop (CSS grid `#sp-branch-list`); movil apilado.
- Ficha **"Otras sucursales cercanas"**: renderizada en servidor (3 mas cercanas a la sucursal) para que no quede vacia sin geolocalizacion; hint + boton **"Ordenar por mi ubicacion"** y mensajes de ayuda.
- Fix puntual: imagen (miniatura) del combo "ISO 100 Fruity Pebbles + Creatina Angry" en el reel (URL 404 -> correcta) — luego reemplazado por el render en servidor.
- Versiones: CSS `1.26`, JS `1.17`.

### Commits / repos
- Submodulo `psk-sucursales` (rama `master`) y repo principal `psk-wc-sync` (rama `feature/retiro-sucursal`): al dia con `origin`. Funcionalidad de retiro en sucursal NO tocada (`functions.php` md5 `ed6779fdc763209f7e5adfb54668b4a3`).

## 2026-10-15 - Perfil "Encargado de Sucursal" para la App WooCommerce

- **Motivo**: la app movil de WooCommerce solo acepta los roles `administrator` o `shop_manager`. Las cuentas de sucursal tenian el rol custom `branch_manager` -> la app las rechazaba.
- **Solucion**: usar el rol **`shop_manager` recortado** para las sucursales.
- **Backup del rol original** (93 permisos): `/tmp/shop_manager_caps_backup_20261015_163128.json` en el servidor.
- **Permisos finales de `shop_manager` (12)**: `read`, `manage_woocommerce`, `edit_shop_order`, `edit_shop_orders`, `edit_others_shop_orders`, `edit_published_shop_orders`, `read_shop_order`, `read_private_shop_orders`, `view_woocommerce_reports` (estadisticas, solo lectura), `edit_products`, `read_product`, `read_private_products` (catalogo, solo lectura).
- **Catalogo SOLO LECTURA**: mu-plugin `sp-shop-manager-menu.php` filtra `user_has_cap` y niega `edit_product`, `publish_products`, `delete_*`, `create_products` y terminos de producto -> pueden VER pero no crear/editar/borrar.
- **Menu wp-admin**: solo WooCommerce -> Pedidos, Productos, Informes. Guard que redirige/bloquea Escritorio, Ajustes (WC y WP), Home/Analytics, Plugins, Usuarios, etc. Oculta el boton "Anadir pedido/producto" y bloquea `post-new.php` (pedidos y productos).
- **Cuentas migradas** de `branch_manager` a `shop_manager` (6): cangrejosp (242), megapolissp (243), atriomallsp (244), centennialsp (245), sanfranciscosp (246), metromallsp (247).
- **Pendiente/verificar**: que las 6 conecten en la app (Pedidos + Productos lectura + Estadisticas). El rol `branch_manager` queda sin uso.

## 2026-10-25 - GBP API: solicitud enviada + avance combos/resenas

### GBP API (Google Business Profile)
- Se envio la **"Application for Basic API Access"** para el proyecto `suplementos-panama-gbp` (**project number 531692178989**, email owner `suplementospanamashop@gmail.com`, sitio `https://suplementospanama.net`).
- Caso abierto: **9-3202000042136**. Tiempo de revision estimado: **7-10 dias habiles**.
- **Seguimiento**: entre el **30-oct-2026** y el **4-nov-2026**; si no hay respuesta, reenviar citando el numero de caso.
- Motivo declarado: monitorear/responder resenas de las 6 sucursales verificadas y medir actividad por sucursal en panel interno (sin redistribuir datos).
- Requisitos cumplidos: perfil El Cangrejo verificado y activo desde 2024 (>60 dias); proyecto GCP activo (no estaba en allowlist).

### Resenas por sucursal (octubre 2026)
- Consulta via **Places API (New)** por Place ID (la GBP API sigue en cuota 0). Solo devuelve las **5 mas recientes** por ficha.
- Conteo verificado en octubre 2026: **Metromall 5, Atrio/Costa del Este 4, San Francisco 3, Megapolis 3, Altos 3, El Cangrejo 1** (total 19). Con el listado completo del panel, octubre ronda **~34**.
- Ranking de efectividad pidiendo resenas: **Metromall y Atrio** lideran; **El Cangrejo** el mas flojo en momentum (1 en el mes pese a 57 historicas). Personal mencionado: Mirelis, Zamary, Mariangel, Fabiola, Nicole.
- Limite: el API mezcla/limita; para el detalle completo por dia hace falta la GBP API aprobada.

### Combos nuevos y precios
- **22306** Lipo Revolution Thermogenic + Stim Free - LanderFit: `_combo_price` **$27.99** (regular $40.49, ahorra $12.50 / 31%).
- **22308** RAW Essential Orange + BUM Essential No-Stim Blue Raspberry: `_combo_price` **$49.99** (regular $97.98, ahorra $47.99 / 49%).
- Descripciones actualizadas con precio y ahorro reales (commit `e6c047f`). Ambos fuera del hero del home (`$sp_hero_ids` intacto).
- Componentes: **9186** -> "LIPO REVOLUTION THERMOGENIC LANDERFIT 60 CAPS"; **21457** -> "BUM ESSENTIAL PERFORMANCE NO-STIM BLUE RASPBERRY" (slugs sin cambio). **9602** agregado a categoria Pre-Entrenos (22).
- Precio de oferta web de los componentes del combo 2 (9602 y 21457) subido de **$24.99 a $35.99** (regular igual).
- El "Ahorra" del tema sigue calculandose contra **precio full** (decision del negocio).

### Badge NUEVO / ISO Surge
- `sp-nuevo-badge.php`: badge de ficha movido a **arriba-izquierda** (`left:14px`) para no chocar con Prev/Next (commit `0daf760`).
- ISO Surge (22136, atts 22304/22305): las imagenes nuevas estaban en servidor pero el navegador mostraba las viejas por `Cache-Control` de 1 ano. Solucion: mu-plugin `sp-img-cache-bust-iso.php` agrega `?ver=20261021` (commit `b26e9fe`).
- `combo-price.php` y `functions.php` del child **NO** se tocaron (retiro en sucursal intacto).

### Fix imagenes padre (productos variables)
- **Sintoma**: en `proteina-blend-vms-nutrition-5-lb` (19061) el sabor seleccionado mostraba la imagen nueva, pero al cargar la ficha y en las miniaturas seguian las viejas.
- **Causa raiz**: un reemplazo anterior de imagenes actualizo solo las **variaciones** y no el **padre**. El padre conservaba `_thumbnail_id=15924` (2025/10) y `_product_image_gallery=15922,17137` (antiguas), mientras las variaciones ya apuntaban a 21465 (Vainilla), 21463 (Chocolate) y 21987 (Strawberry).
- **Fix 19061**: thumb -> `21465`, galeria -> `21463,21987`. Verificado en vivo: 0 apariciones de la imagen vieja, las 3 nuevas presentes.
- **Auditoria** (`local/sp_audit_parent_imgs.php`): recorre los variables y alerta cuando el padre tiene imagenes mas antiguas que la variacion mas vieja. Detecto ademas **9171** (Amino Revolution): el padre mostraba Grape (imagen 2024, sabor sin variacion). Fix: thumb -> `16432` (Coconut), galeria -> `15668` (Raspberry Lemonade).
- **18917** (C4 Original) revisado: padre = Fruit Punch (sabor valido) y galeria con Ice Blue Razz + Pink Lemonde; sin cambios.
- **Regla para futuros reemplazos**: al cambiar imagenes de un variable, actualizar SIEMPRE variaciones **y** padre (`_thumbnail_id` + `_product_image_gallery`), luego `wc_delete_product_transients()`. El script original era ad-hoc (`wp eval-file`, no versionado); la auditoria queda versionada para detectar el patron.


## 2026-10-26 - Hero: imagenes de los 2 combos nuevos + analisis de checkout

### Hero combos 22306 / 22308
- Imagenes recibidas en `C:\suplementos\home_nuevo\imagenes-hero\new261026` (1672x941). Normalizadas a **1698x926** (estandar del hero, recorte centrado minimo) con JPG quality 85 (~240 KB).
- Subidas a Media como attachments **22326** (Lipo) y **22327** (RAW+BUM), con title/alt/caption/description SEO. Se renombraron a sufijo `-hero` para no chocar con la imagen de ficha del combo.
- Meta `_combo_hero_image_id` asignada a 22306 -> 22326 y 22308 -> 22327.
- `local/home-nuevo/home-nuevo.php`: agregados **22306, 22308** a `$sp_hero_ids`. Desplegado al child theme.
- Verificado: home con **25 slides** (antes 23), imagenes nuevas HTTP 200 y enlaces a `/product/combo-lipo-revolution-thermogenic-stim-free-landerfit/` y `/product/combo-raw-essential-orange-bum-essential-performance-no-stim/`.
- `local/home-nuevo/hero-combos/map.json` actualizado (29 entradas). La carpeta de imagenes hero sigue **sin versionar** (22,6 MB) por convencion del repo.

### Analisis de compras no completadas
- Documento `docs/ANALISIS-CHECKOUT-ABANDONO.md` (60 dias, ago-oct 2026): 76 ordenes, 21% no finalizadas (12 canceladas + 4 fallidas), $902,81 en riesgo.
- Principal causal: abandono en el paso de pago (10 de 12 canceladas por timeout de 60 min). Fallidas: 1 solo cliente con fondos insuficientes.
- Checkout: 22 campos, 17 requeridos. `billing_doc_identificacion` **se mantiene** (necesario para reclamos). Candidatos a relajar: `shipping_postcode` y datos de envio cuando es retiro en sucursal.
- CSV de recuperacion: `local/recuperacion-ordenes-oct-2026.csv` (16 ordenes con PII, excluido de git via `.gitignore`).
- **Sin cambios de checkout aplicados** a la espera de definir con el negocio.

## 2026-10-26 - Banner Halloween Sale en el home (reemplaza Online Sale)

### Arte y optimizacion
- Fuentes en `C:\suplementos\home_nuevo\banner-halloween` (3840x1000 / 2400x960 / 2160x1440, 2-2,7 MB cada una).
- Optimizadas a los 3 tamanos del sitio en `local/home-nuevo/assets/banner-halloween/`:
  - `banner-halloween-1920x500.jpg` 248 KB + `.webp` 195 KB
  - `banner-halloween-1200x480.jpg` 172 KB + `.webp` 143 KB
  - `banner-halloween-1080x720.jpg` 206 KB + `.webp` 164 KB
- JPG q82 progressive + WebP q80. Subidas al child theme en `assets/banner-halloween/`.

### Cambios en la plantilla (`page-templates/home-nuevo.php`)
- Seccion 5: el bloque "BLACK WEEKEND BANNER" pasa a **BANNER HALLOWEEN**.
- Se conserva el enlace exacto: `href="/online-sale-sp/"` (imagen + CTA). No se toco la URL ni el 301 de `/black-weekend/`.
- **Sin copy visible** (decision del negocio): la imagen funciona sola y enlazada. Se conserva unicamente un boton CTA con el texto `MONSTER SALE`.
- Los metadatos SEO/GEO se concentran en el `alt` de la imagen; no se agregan H2 ni parrafos al contenedor.
- Imagen: `alt="Banner de Halloween Sale con proteina isolate, creatina y glutamina VMS Nutrition y Evogen"`, `width="1920" height="500"`, `loading="lazy"`, `decoding="async"`.
- CSS: `aspect-ratio` por breakpoint (1920/500, 1200/480, 1080/720) para evitar CLS; modulo `.sp-hw` con fondo #070707 y CTA centrado.
- Sin schema nuevo (decision SEO/GEO: no inventar Event/Offer sin fechas ni porcentajes verificables).

### Metadata de la landing `/online-sale-sp/`
- Rank Math actualizado (antes decia "Black Weekend"): title `Halloween Sale Panama | Descuentos Online en Suplementos`, description, focus keyword `halloween sale panama`, OG title y OG description.
- Imagen OG nueva 1200x630 (`banner-halloween-og-1200x630.jpg`, attachment **22328**) asignada como **featured image** de la pagina: el modulo Social de Rank Math no esta activo, por lo que el meta `rank_math_facebook_image` no se aplicaba y `og:image` seguia saliendo con el logo.
- Backup de la metadata anterior: `/tmp/backup_rankmath_22216.json` (servidor).
- **Sitemap regenerado**: se borro la cache de `uploads/rank-math/*.xml` (generada el 12-ago, antes de existir la pagina) y `online-sale-sp` ya aparece en `page-sitemap.xml`.

### Verificacion en produccion
- Home: 6 variantes de imagen referenciadas, 0 referencias a `banner-bw`.
- `href` a `/online-sale-sp/`: wrapper de imagen + CTA.
- Imagenes HTTP 200 (JPG y WebP, 3 tamanos).
- `php -l` OK; cache SG purgada.

### Pendientes detectados por los agentes
1. `sp-dev-nocache.php` sigue activo para `/online-sale-sp/`.
2. El isolate VMS que muestra el arte no esta listado hoy en la landing.
3. Plan de reversion sugerido: 1-nov-2026 (volver a banner neutro/Black Friday manteniendo la misma URL).
