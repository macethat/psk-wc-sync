# Pseudo-Template para Artículos de Power Rack

⚠️ **IMPORTANTE**: Cada artículo nuevo debe crearse en estado **`draft`** (borrador). El usuario lo revisa antes de publicar.

## Estructura de cada artículo

```
1. TÍTULO SEO (H1)
2. INTRODUCCIÓN (2-3 párrafos, lead paragraph)
3. IMAGEN INTERNA (600×900 vertical, optimizada mobile)
   - Alt text con keyword focus
   - Figcaption con descripción
4. DESARROLLO (H2, H3 con color #000000)
   - Mitos y realidades (formato QA)
   - Beneficios (listado)
   - Guías / Dosis
   - FAQ
5. CONCLUSIÓN
6. SUCURSALES + DELIVERY (párrafo fijo, ver abajo)
7. PRODUCTOS RELACIONADOS (WooCommerce shortcode)
```

## Párrafo fijo de sucursales

```
En <strong>Suplementos Panamá</strong> puedes encontrar estos y muchos más productos en nuestras sucursales: 
<strong>El Cangrejo, Megapolis, San Francisco, Atrio Mall, Altos de Panamá y MetroMall</strong> 
(dentro de Power Club San Francisco y Altos de Panamá). 
También puedes pedir por <a href="https://suplementospanama.net">nuestra tienda online</a> con 
<strong>delivery gratis en órdenes mayores a $150</strong> o retirar sin costo en la sucursal de tu preferencia. 
Nuestro equipo de profesionales está capacitado para asesorarte en tu proceso individual de suplementación. 
<strong>¡Lleva tu rendimiento al siguiente nivel!</strong>
```

## Tabla de Contenidos (Highlights)

Se genera automáticamente al inicio del artículo via `functions.php`:
- Título: "Highlights"
- Viñeta y flecha configurables por artículo (post meta: `highlight_accent`)
- Default viñeta: `#d8bfe8`, default flecha: `#9b59b6`
- Color de fondo configurable por artículo (post meta: `highlight_bg`)
- Default fondo: gris suave `#f0ebe6`
- Flecha (▸) absolutamente posicionada para evitar espacio extra
- Caja autoajustable (`inline-block`), alineada a la izquierda
- Línea horizontal (`<hr>`) entre cada sección principal en el ToC

### Configuración de color por artículo

Los colores de fondo, viñetas y líneas deben **corresponder con los tonos predominantes de la cover image** de cada artículo.

| Meta | Valor | Ejemplo |
|------|-------|---------|
| `highlight_bg` | Color fondo ToC | `#f0ebe6`, `#E8F5E9` |
| `highlight_accent` | Color flecha ▸ (toggle) | `#A078C8`, `#6a808d` |
| `highlight_bullet_color` | Color viñeta ● (más tenue que accent) | `#E0D0EC`, `#D4D4D4` |
| `highlight_hr_color` | Color líneas horizontales separadoras en ToC y entre secciones | `#e0d6d0` (gris claro), `#ffffff` (blanco para fondos oscuros) |

**Reglas:**
- `highlight_accent`: color de la flecha ▸, debe ser el tono más predominante de la cover image
- `highlight_bullet_color`: **siempre más tenue/suave que `highlight_accent`** — la viñeta debe ser sutil, no competitiva
- `highlight_hr_color`: si el fondo (`highlight_bg`) es oscuro, usar blanco `#ffffff`. Si es claro, mantener gris `#e0d6d0`
- Si no se especifica `highlight_accent`, usa defaults lila (`accent: #d8bfe8`, `bullet: #E0D0EC`)

## Productos relacionados

Usar shortcode de WooCommerce al final del artículo:

```
[products category="CATEGORIA" limit="4" columns="4"]
```

O por IDs específicos (recomendado para evitar productos agotados):

```
[products ids="123,456,789" limit="3" columns="3"]
```

## Botón CTA a categoría

Colocar un botón justo encima del reel de productos enlazando a la categoría del artículo.

### Configuración por artículo
Agregar post meta `reel_category` con el slug de la categoría de producto (ej: `creatina`, `proteina-aislada-isolate`). El botón se genera automáticamente via `the_content` filter.

Si se necesita un texto personalizado (ej: plural "creatinas" en lugar de "creatina"), agregar post meta `reel_btn_text` con el texto exacto del botón.

### Valores por defecto si no se especifica `reel_category`
- Texto: "Ver todos los productos"
- Link: categoría por defecto o `/productos/`

### Diseño del botón
- Fondo: rojo marca `#c0392b`
- Texto: blanco `#fff`
- Bordes rectos (sin border-radius)
- Centrado, margins 30px arriba/abajo
- `display: inline-block`, padding 12px 30px
- Font: 16px, weight 600

## Imágenes

| Tipo | Tamaño (reales) | Ratio | Alt text |
|------|----------------|-------|----------|
| Portada (Featured) | **1424×751 px** (ideal ≥1200px ancho) | ~16:9 | Keyword focus descriptivo |
| Interna (vertical, en contenido) | **848×1264 px** | ~2:3 | "Infografia [tema] dosis beneficios" |

### Nomenclatura de archivos
- Cover: `[tema]-cover-sp.jpg` (ej: `magnesio-sueno-recuperacion-cover-sp.jpg`)
- Interna: `[tema]-sp-02.jpg` (ej: `proteina-wey-vs-vegetal-sp-02.jpg`)

### Prompt para generar imágenes (IA)

El prompt debe especificar los **tonos de color exactos** que se usarán en el artículo. Esos mismos colores se aplican después a los highlights del artículo.

```
Prompt cubierta horizontal — dimensiones 1424×751 px:
"Illustration of [tema], clean minimal style, soft pastel tones, 
[#HEX] [color-name] and [#HEX] [secondary-color] color palette,
suplementos deportivos aesthetic, no text overlay, 16:9 ratio, 
high quality product photography style"

Prompt imagen vertical interna — dimensiones 848×1264 px:
"Vertical infographic style illustration about [tema], 
clean white background, [#HEX] [color-name] accent palette, 2:3 ratio, 
educational supplement content aesthetic"
```

**Ejemplo real (artículo Magnesio, post 21716):**
```
"Illustration of magnesium for sleep and muscle recovery, clean minimal style, 
soft pastel tones, #A078C8 lilac purple and #D8BFE8 light lavender palette,
suplementos deportivos aesthetic, no text overlay, 16:9 ratio, 
high quality product photography style"
```

**Regla:** el color `#HEX` principal del prompt debe coincidir con el meta `highlight_accent` del artículo.

## Schema Article (JSON-LD)

Guardar en post meta `power_rack_schema`. Campos obligatorios:
- headline, description, image (ImageObject con dimensiones)
- author, publisher (Organization con logo)
- datePublished, dateModified
- mainEntityOfPage (URL canónica)
- keywords, articleSection, inLanguage, wordCount, timeRequired

## CSS aplicado via functions.php

- H2, H3, H4 color: #000000 !important
- P font-size: 17px, line-height: 1.8
- LI font-size: 17px, line-height: 1.8 (mismo que párrafos)
- Título desktop: 48px, mobile: 27px
- H2 mobile: 22px, H3 mobile: 19px
- Aplica a single posts, blog archive y categorías (is_single, is_home, is_category)

### Tabla comparativa

- Desktop: `width: 100%`, `td { white-space: normal }`
- Mobile (≤767px): `width: auto`, `td { white-space: nowrap }`, scroll horizontal vía `overflow-x:auto` en contenedor
- Contenedor padre: `<div style="overflow-x:auto;max-width:100%">`
- La tabla NO lleva `width:100%` inline (se controla por CSS) para permitir scroll en móvil

### Blog listing (categorías) en mobile

- Orden de cada entrada: Título → Meta → Imagen (reordenado con Flexbox `order`)
- Título: `font-size: 22px`, `margin-bottom: 4px`
- Separación entre posts: `row-gap: 0px` mobile, `35px` desktop en `.blog-style-grid`
- CSS aplica en `@media (max-width: 767px)`

## Cover image

- Asignar como Featured Image (post thumbnail, `_thumbnail_id`)
- NO incluir la misma imagen dentro del contenido del artículo (evitar duplicado)
- La imagen interna (infografía) va dentro del contenido con `<figure>` y `<figcaption>`

## Categorías del blog

- Training (antes Entrenamientos)
- Come Bien (antes Recetas)
- Suplementos
