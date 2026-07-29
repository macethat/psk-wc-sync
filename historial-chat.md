# Historial de Chat — Suplementos Panamá

## 2026-07-27 — Despliegue sucursales: imágenes reales y enlaces WooCommerce

### Problema
- Las imágenes de productos en sucursales (hub y single) usaban emojis (🏋️🧪⚡) en vez de imágenes reales
- Los enlaces "Ver →" apuntaban a `#` en vez de productos reales
- El servidor en vivo tenía código viejo (JS y CSS sin cambios)

### Cambios realizados (archivos fuente en `psk-sucursales/src/theme/`)
- **`assets/js/sucursales.js`**: reemplazó `icon: '🏋️'` por `img: 'https://...'` con URLs reales de WooCommerce; cambió `<span>p.icon</span>` por `<img src="p.img">`; los enlaces usan `p.url` (slug real del producto)
- **`assets/css/sucursales.css`**: añadió `.sp-product-image img` con `object-fit: contain`; `h3` de `15px` a `17px`
- **`functions-additions.php`**: versión de enqueue bump de `'1.0'` a `'1.1'` para cache busting

### Despliegue (SCP a SiteGround)
- Copiados los 3 archivos via SSH a `www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/`
- Verificado que se sirven con `?ver=1.1`
- Hub: https://suplementospanama.net/sucursales/
- Single: https://suplementospanama.net/sucursales/el-cangrejo/

## 2026-07-27 — Productos corregidos: Creatina Micronizada Evogen, combos inactivos eliminados

### Problema
- El producto "Creatina Evogen 60 Serv" (2º en `populares`, 1º en `creatina`, 2º en `todos`) usaba URL `/product/creatina-evogen-60-serv-beta-alanina-raw/` que redirige a `/product/creatina-micronizada-monohidratada/` (mismo producto real: Creatina Micronizada Monohidratada Evogen 60 Serv a $29.99)
- Nombre, subtítulo ("+ Beta Alanina RAW"), precio ($49.99) e imagen eran incorrectos
- 2 combos apuntaban a productos 404: "Proteína 5 lb + Creatina Super ATP" y combos VMS
- URL de Creatina Super ATP era incorrecta

### Cambios realizados
- **`assets/js/sucursales.js`**:
  - Reemplazó "Creatina Evogen 60 Serv" ($49.99, imagen Beta Alanina RAW) por **Creatina Micronizada Evogen** 60 Serv $29.99 con imagen e URL correctas (3 ocurrencias: `populares`, `creatina`, `todos`)
  - Reemplazó combo 404 "Proteína 5 lb + Creatina Super ATP 80 Serv" por **ProLive Bio5 Impulse** 5 lb $95.99 en `populares`, `proteinas` y `todos`
  - Eliminó "Proteína VMS + Creatina Nutrex 200 Serv" en `proteinas` (redirige a VMS Bios Active)
  - Reemplazó "Proteína VMS + Creatina Evogen 60 Serv" en `creatina` por **Creatina Monohidratada Forzagen** 72 Serv $29.99
  - Corrigió URL de Creatina Super ATP a `/product/creatina-super-atp/` y precio a $29.99
- **`functions-additions.php`**: versión de enqueue bump de `'1.1'` a `'1.4'`

### Despliegue (SCP a SiteGround)
- Copiados JS y PHP via scp a `nutritix-child/` en SiteGround
- Commit en `psk-sucursales` (`2254d00`) y en `stock-suplementos` (`67b4335`)

## 2026-07-27 — Tarjeta de producto completamente enlazada

### Cambio
- El `<div class="sp-product-card">` se cambió a `<a href="p.url" class="sp-product-card">` para que toda la tarjeta (imagen, nombre, precio) sea cliqueable y lleve a la ficha del producto
- El `<a>` interno "Ver →" se reemplazó por un `<span>` para evitar anidamiento de links
- CSS: `.sp-product-card` ahora tiene `display: block; text-decoration: none; color: inherit;`

### Despliegue
- JS, CSS y PHP actualizados via SCP con versión `1.5`
- Commit `1ca4a35` en `psk-sucursales` (pusheado)
- Commit `657e6b2` en `stock-suplementos` (pusheado)

## 2026-07-29 — Hero hub rediseñado + fix CSS cascade que rompía botón geolocalizador

### Hero hub
- Stats bar eliminada del hero
- Título alineado abajo (`align-items: flex-end; padding-bottom: 120px`)
- Subtítulo eliminado, solo título permanece
- Overlay más claro: `rgba(0,0,0,.4)` → `rgba(0,0,0,.15)`
- Título `color: #f0f0f0; font-size: 44px; font-weight: 900`
- Imagen hero hub 2752×1536 optimizada a 481KB desplegada en `sucursales/images/hero-hub.jpg`
- 6 imágenes hero de sucursal (1250×675) desplegadas en `sucursales/{id}/images/hero-{id}.jpg`
- Schema JSON-LD dinámico con `ImageObject` via `getimagesize()` en single template
- `.sp-card-image` cambiado de `height: 200px` a `aspect-ratio: 16/9`
- CSS responsive: `.sp-section-tag` 14px, `.sp-branch-status` 13px, icono reloj `margin-right: 6px`, `.sp-btn` 15/14px, `.sp-branch-link-name` 18px mobile, `.sp-product-card h3` 22px mobile, `.sp-faq-question` 13px mobile

### Bug CSS — geolocalizador roto
- **Causa raíz**: en `sucursales.css` una línea `letter-spacing: 0.05em;` quedó huérfana fuera de cualquier selector (entre el cierre de `.sp-hero-content h1` y la apertura de `.sp-geolocator-header`), invalidando todo el CSS posterior
- **Síntoma**: el botón "Detectar mi ubicación" no se posicionaba correctamente porque `.sp-geolocator-header` (flex, space-between, wrap) era ignorado por el navegador
- **Fix**: se eliminaron las líneas 110-113 del CSS (letter-spacing huérfano + `}` extra)
- **Despliegue**: SCP a `nutritix-child/assets/css/sucursales.css` + purga de archivos combinados de SG Optimizer (se eliminaron los `.css` combinados para forzar regeneración)

### Commits
- `6326695` en `psk-sucursales` — fix(css): remove orphaned letter-spacing
- `df1ff47` en `psk-sucursales` — hero: overlay más claro
- `6774631` en `psk-sucursales` — hero: sin subtítulo, solo título
- Previamente: hero padding, título color/font, etc.
