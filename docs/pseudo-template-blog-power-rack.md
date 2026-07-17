# Pseudo-Template para Artículos de Power Rack

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

| Meta | Valor | Ejemplo |
|------|-------|---------|
| `highlight_bg` | Color fondo ToC | `#f0ebe6`, `#E8F5E9` |
| `highlight_accent` | Color viñeta + flecha | `#d8bfe8`, `#6a808d` |

Si no se especifica `highlight_accent`, usa los defaults lila.

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

| Tipo | Tamaño | Alt text |
|------|--------|----------|
| Portada (Featured) | 1200×630 px | Keyword focus descriptivo |
| Interna | 600×900 px vertical | "Infografia [tema] dosis beneficios" |

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
- Título: `font-size: 22px`, `margin-bottom: 24px`
- Separación entre posts: `row-gap: 25px` en `.blog-style-grid`
- CSS aplica en `@media (max-width: 767px)`

## Cover image

- Asignar como Featured Image (post thumbnail, `_thumbnail_id`)
- NO incluir la misma imagen dentro del contenido del artículo (evitar duplicado)
- La imagen interna (infografía) va dentro del contenido con `<figure>` y `<figcaption>`

## Categorías del blog

- Training (antes Entrenamientos)
- Come Bien (antes Recetas)
- Suplementos
