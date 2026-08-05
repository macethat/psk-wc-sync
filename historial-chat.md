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

## 2026-07-29 — Página single de sucursal: mapa GBP, FAQ dinámico, galería con fallback

### Mapa con coordenadas exactas de GBP
- El embed del mapa cambió de text search (`maps?q=NOMBRE+DIRECC`) a coordenadas precisas (`maps?q=LAT,LNG&z=17`) usando los valores de cada sucursal
- Coordenadas de El Cangrejo actualizadas a las del perfil de Google Business (`8.9842946, -79.5302391`)

### FAQ con horarios reales del branch
- Título cambiado de "Preguntas Frecuentes — {sucursal}" a "Lo que necesitas saber" (consistente con hub)
- La pregunta de horarios ahora genera dinámicamente el texto agrupando días con el mismo rango horario desde `data.php`
- Ejemplo El Cangrejo: "Lunes, Martes, Miércoles, Jueves, Viernes de 9:00am - 8:00pm; Sábado, Domingo de 9:00am - 5:30pm"
- Incluye "Horarios sujetos a cambios en días feriados"
- Aplica a todas las 6 sucursales automáticamente

### Galería con fallback
- Las imágenes secundarias (`sucursal-1.jpg`, `sucursal-2.jpg`) no existen en el servidor
- El template ahora verifica `file_exists()` y oculta el sidebar si no hay imágenes
- Cuando solo hay hero, la galería usa `sp-gallery-single` (1 columna, centrada)
- CSS añadido: `.sp-gallery-single` con `grid-template-columns: 1fr`

### Cambios generales (se replican a las 6 sucursales)
- **`page-sucursal-single.php`**: mapa con coordenadas, FAQ dinámico, galería condicional
- **`sucursales.css`**: reglas para galería single-column
- **`functions-additions.php`**: version bump a `1.13` (cache busting)
- **`data.php`**: coordenadas El Cangrejo actualizadas

### Commits
- `6873a1c` en `psk-sucursales` — feat(single): mapa coordenadas GBP, FAQ horarios reales, galería fallback
- `519b48f` en `stock-suplementos` — docs: historial actualizado + submodule reference

## 2026-07-30 — Sucursales cercanas con miniatura, horarios sincronizados a Panama

### Problema
- Las imágenes de sucursales cercanas no se veían (absolute positioning no funcionaba)
- En desktop la miniatura quedaba al borde de la card en vez de dentro del contenedor
- Se necesitaba miniatura landscape en desktop, cuadrada en mobile
- El día "Hoy" usaba `date('l')` (hora del servidor) en vez de `current_time('l')` (hora Panama)

### Cambios realizados
- **CSS**: imagen dentro de `.sp-branch-card-body` con `display:flex; gap:16px`; desktop: 200px landscape (16/9) sin bordes redondeados; mobile: cuadrada (1/1) debajo de datos
- **JS**: template reordenado: datos primero, imagen después, dentro del body
- **PHP**: `page-sucursal-single.php:85` — `date('l')` reemplazado por `current_time('l')` para sincronizar con timezone WordPress (America/Panama)
- **Versiones**: CSS `1.18`, JS `1.14`

### Despliegue
- SCP a SiteGround de JS (`1.14`), CSS (`1.18`), PHP
- Eliminados archivos combinados CSS y JS de SG Optimizer
- `wp cache flush` ejecutado

### Commits
- `9a9ce44` en `psk-sucursales` — feat: sucursales cercanas con miniatura landscape, timezone Panama fix

## 2026-08-04 — Fix combo 21647 (ProLive Full Stack), redirect contacto→sucursales, favicon completo

### Fix combo 21647 — ProLive Full Stack
- **Problema**: al agregar al carrito el combo 21647 (ProLive Full Stack), el amino BCAA no aparecía y el sistema rechazaba la compra ("producto no disponible")
- **Causa raíz**: el hijo 21459 (amino) NO existía en WooCommerce. El BCAA correcto es el padre variable 21545 con variaciones 21546 (Fruit Punch) y 21547 (Grape)
- **Corrección**:
  - `_children` de `[18186,21455,21459]` → `[18186,21455,21545]`
  - Meta nueva `_combo_variations_21545 = 21547` (BCAA Grape, SKU 650076635202, PSK id 1789)
  - Backup en `/home/u1910-kbd9lgn9dh44/backup_combo_21647_20260804-233604/`
- **Verificación**: simulación add-to-cart con Cookies and Cream (V18191) + creatina 21455 + BCAA Grape 21547 = OK; Choco Coco (V18193) rechazado por outofstock; página renderiza selector `combo_variation_id[21545]`

### Redirect 301 /contacto/ → /sucursales/
- Regla en `.htaccess` antes de `# BEGIN WordPress`:
  - `RewriteCond %{REQUEST_URI} ^/contacto/?$ [NC]` + `RewriteRule ^ /sucursales/ [L,R=301]`
- Backup `backup_htaccess_20260804-160223.bak`
- Verificado: `/contacto/` y `/contacto` → 301 → `https://suplementospanama.net/sucursales/` (200); página Sucursales = ID 21793 publish

### Favicon completo
- `site_icon` = 21801 (`sp-favicon-01.png` 512×512); links `rel=icon` 32/192 + apple-touch en head; Googlebot 200; robots.txt no bloquea; `favicon.ico` → 302 → PNG 200
- Pendiente: solicitar indexación de la home en Search Console (suplementospanamacrm@gmail.com)

### WhatsApp
- Enlace generado: `https://wa.me/50760153257?text=Hola%20quiero%20informaci%C3%B3n%20sobre%3A`

## 2026-08-05 — Auditoría de 22 combos + fix SKU duplicado BCAA + redirect legacy

### Auditoría global de combos (grouped publicados = 22)
- Script `audit_combos.php` (query por tax_query product_type=grouped) verifica por combo: hijos inexistentes, meta `_combo_variations_{hijo}` faltantes, variaciones inexistentes, `_sucursales_disponibles` vacíos
- **Resultado**: NINGÚN combo tiene hijos inexistentes (el error del 21647 no se repite)
- 10 combos con proteínas variables sin meta `_combo_variaciones_` (muestran todas las variaciones, no es bug): 21639, 21633, 21523, 21522, 21520, 21517, 21516, 21514, 21513, 21512
- 3 combos usaban BCAA Fruit Punch (21546) sin stock de sucursal: 21518, 21520, 21524

### Fix SKU duplicado BCAA
- **Problema**: dos productos con el mismo SKU `650076635196` (Fruit Punch): 21458 (simple legacy, creado 08-07) y 21546 (variación del padre variable 21545, creado 09-07). El script `daily_stock_update.py` construye `wc_by_sku[sku]=p`, la colisión hacía que las sucursales se escribieran en el simple 21458 y la variación 21546 nunca recibía sus metas (quedaba sin `_sucursales_disponibles`)
- **Confirmado via API PSK**: el Fruit Punch (id_articulo 1790) SÍ tiene stock en sucursales (El Cangrejo 21, Megapolis 2, Atrio 4, San Francisco 7, Altos 3, Metromall 22 = 59)
- **Corrección** (backup `backup_bcaa_fix_20260805-001440`):
  - Variación 21546: stock corregido a 78 y sucursales `1,5,6,7,8,10` desde PSK
  - Combo 21655 (ISO Total Pack) migrado al patrón variable: `_children = 21625,21545,21456` + `_combo_variaciones_21545=21546`
  - Simple 21458 movido a draft (papelera, recuperable) y sin `_sku`
- **Verificación**: sin SKUs duplicados restantes; 0 combos referenciando 21458; combo 21655 renderiza hijos correctos

### Redirect 301 producto legacy → variable
- `/product/bcaa/` → 301 → `/product/bcaa-1211-vms-30-servings/` (para no perder indexación del legacy)
- Backup `backup_htaccess_20260805-002220.bak`
- Verificado: `/product/bcaa/` y `/product/bcaa` → 301 → variable (200)
