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

### Lecciones aprendidas

1. **No usar PowerShell string manipulation para contenido con acentos** → usar PHP `file_put_contents` / `wp eval-file` en servidor
2. **Base64 + `base64 -d`** es el método confiable para transferir archivos PHP sin corrupción de encoding
3. **Para tablas responsivas**: quitar `width:100%` inline, controlar por CSS, y usar `overflow-x:auto` en contenedor padre
