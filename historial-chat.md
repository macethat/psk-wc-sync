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

## 2026-08-05 — Header y footer de la home aplicados a la página shop (HFE)

### Problema
- La página shop (`/shop/`, ID 5) mostraba un header y footer **diferentes** a los de la home
- La shop usa la plantilla normal del tema con templates HFE (Header Footer Elementor): header `20649` y footer `414`
- La home (ID 18625) usa plantilla `elementor_canvas`: su header ("1 Pannel", "2 Panel", "3 Panel") y su footer (contenedores 16/17/18) viven **dentro** del propio `_elementor_data` de la página, no en templates HFE

### Decisión (confirmada con el usuario)
- **Método**: editar los templates HFE (aplica a la shop y a todas las páginas con HFE), en vez de convertir la shop a canvas (más arriesgado, rompe archive/paginación de productos)
- **Alcance del header**: solo la franja superior "1 Pannel" (`696739`) de la home; se mantienen logo/búsqueda/carrito/menú actuales de la shop
- **Footer**: reemplazado completo por los contenedores 16 (`3205c4`), 17 (`afa928`) y 18 (`9fe537`) de la home

### Cambios realizados
- **Header HFE `20649`**: reemplazado solo el contenedor `e6dccf2` ("1 Panel") por el "1 Pannel" de la home (`696739`); se conservan los contenedores `274df59` (2 Panel: logo) y `087078a` (3 Panel: menú)
- **Footer HFE `414`**: reemplazado todo su `_elementor_data` (secciones viejas con columns) por los 3 contenedores de la home (`3205c4`, `afa928`, `9fe537`)
- Script `apply_hfe_home.php` (lee `new_header_data.json` / `new_footer_data.json` con los contenedores extraídos de la home) vía `wp eval-file`
- Cachés purgadas: `wp elementor flush_css --force`, `wp cache flush`, `wp sg purge`

### Backups (en servidor, `~/backups_hfe/`)
- `header_20649_20260805-053222.json`
- `footer_414_20260805-053222.json`
- `home_18625_20260805-053222.json`
- `shop_5_20260805-053222.json` (vacío: la shop no tiene `_elementor_data`, es esperado)

### Verificación
- `https://suplementospanama.net/shop/?nocache=hfe1` (200):
  - Header: `data-id="696739"` presente, `e6dccf2` eliminado, menú `087078a` conservado
  - Footer: `3205c4`, `afa928`, `9fe537` presentes; `03090b5` (footer viejo) eliminado
  - CSS de Elementor regenerado: `.elementor-element-696739`, `-3205c4`, `-afa928`, `-9fe537` presentes
- Nota: las capturas de pantalla generadas no pudieron revisarse (modelo sin soporte de imágenes); la verificación se hizo por HTML/CSS del render

## 2026-08-05 — Formulario de newsletter en el footer (MC4WP + Mailchimp)

### Contexto
- El footer HFE `414` (ya unificado con la home) debía capturar suscripciones de email/WhatsApp
- La cuenta Mailchimp conectada es `agenciadigitalterceracto@gmail.com` (ajena a la empresa) → se planificó migración a cuenta corporativa nueva
- Solo existe MC4WP (API key `<redactada>-us14`); no hay WooCommerce Subscriptions; MailPoet sin forms

### Cambios realizados
- **Form MC4WP `417`** (`update_form417.php`): inputs `FNAME` ("Tu nombre"), `EMAIL` ("Correo Electronico"), `PHONE` ("WhatsApp") — todos required; doble opt-in activo (`double_optin=1`); botón "suscribete"; lista `b7303a6210` ("Agencia Digital Tercer Acto")
- **Footer HFE `414`**: sección newsletter `nwsltr01` insertada al inicio (`add_newsletter_footer.php`): heading "SUSCRÍBETE AHORA", text-editor "Manténgase actualizado..." y widget shortcode `[mc4wp_form id="417"]`
  - El widget nativo `nutritix-mailchmip` no renderiza: el tema usa `nutritix_is_mailchimp_activated()` → `function_exists('_mc4wp_load_plugin')`, no definida en MC4WP 4.14 → sustituido por widget shortcode (`fix_newsletter_widget.php`)
- Verificado en `/shop/`: `mc4wp-form-417` + inputs FNAME/EMAIL/PHONE presentes
- Nota: la home (18625, plantilla `elementor_canvas`) NO usa el footer HFE: sus paneles 14/15 ("SUSCRÍBETE AHORA") no tienen form real (solo heading + icono)

### Exportación de contactos (para migrar)
- 9 suscriptores (todos `subscribed`, sin FNAME/PHONE, últimos opt-in oct-2024) exportados de la lista `b7303a6210` → `mailchimp_export.csv` (local en `C:\suplementos\stock-suplementos\mailchimp_export.csv`)
- Contactos: `agenciadigitalterceracto@gmail.com`, `jjean1661@gmail.com`, `joel76346@gmail.com`, `didimotrejos5@gmail.com`, `franciscobarreiro12@gmail.com`, `Jjovane@gmail.com`, `ja.panama@mailo.com`, `lexdom21@gmail.com`, `dapena5793@gmail.com`
- Archivo del servidor (`~/mailchimp_export.csv`) eliminado tras la copia local

### Backups (en servidor, `~/`)
- `backup_footer414_pre_newsletter_20260805-135214.json`
- `backup_form417_content_20260805-135214.txt`
- `backup_form417_settings_20260805-135214.txt`

### Migración ejecutada (2026-08-05)
- Usuario creó la cuenta nueva en mailchimp.com y entregó API key `<redactada>-us1` (DC **us1**)
- La cuenta nueva ya tenía la audiencia por defecto **"Suplementos Panamá" (id `27b4cb9f8c`)**, dueño `suplementospanamashop@gmail.com`
- Script `~/migrate_mailchimp.php` (subido al servidor) ejecutado: `wp eval-file ~/migrate_mailchimp.php -- <API_KEY> 27b4cb9f8c`
  - Nota: `wp eval-file` pasa `--` como primer `$args`; el script lo filtra
- **Resultado**:
  - Backup de la config previa (API key antigua) guardado en `~/backup_mc4wp_<fecha>.json`
  - Los 9 contactos reimportados vía batch (id `38rov03dn5`): **finished 9/9, errored 0**, todos `subscribed` + el dueño de la cuenta = 10 miembros
  - Opción `mc4wp` → api_key nueva (`-us1`); form 417 → lista `27b4cb9f8c` (doble opt-in sigue activo)
  - Cachés purgadas (`wp elementor flush_css --force`, `wp cache flush`)
- **Verificación en vivo** `/shop/?nocache=mig2`: `mc4wp-form-417` presente, campos FNAME/EMAIL/PHONE + "SUSCRÍBETE AHORA" renderizados, sin errores MC4WP
- **Prueba de suscripción real**: el filtro anti-spam de MC4WP bloqueó el POST automatizado ("Tu envío ha sido marcado como spam") → confirma que el form procesa contra la cuenta nueva; los envíos de navegador real no se ven afectados

### Estado tras la migración
- Form del footer capturando contra la cuenta corporativa nueva (`suplementospanamashop@gmail.com`, lista `27b4cb9f8c`)
- Pendiente (opcional, del lado del usuario): cerrar/bloquear la cuenta vieja `agenciadigitalterceracto@gmail.com` desde su panel de Mailchimp (la API no lo permite)

## 2026-08-05 — Newsletter: form en todas las páginas + estilos (mensaje, botón, texto) + incidente JSON

### Contexto
- El form del newsletter debía aparecer en **todas** las páginas (incluida la home)
- El mensaje de confirmación se veía gris oscuro (CSS del tema `#0f834d` sobre fondo `#151515`), el form estaba mal alineado y en mobile se veía un **bloque de código** debajo del form
- El correo de confirmación llegaba en **inglés** y caía en **spam**

### Form en todas las páginas
- Verificado: el form aparecía en shop, producto y sucursales (via footer HFE 414), pero **NO** en la home (18625, plantilla `elementor_canvas` — no usa footer HFE)
- Se insertó el widget shortcode `[mc4wp_form id="417"]` (`homenwsltrf`) en el panel 15 de la home (`781140`, tras el text-editor `59db94`) → form ahora presente en home, shop y producto

### Correo de confirmación (Mailchimp)
- **Inglés**: la lista `27b4cb9f8c` tenía `language=en` → corregido a `es` (PATCH lists/27b4cb9f8c con `campaign_defaults.language=es` + `permission_reminder` en español)
- **Spam**: el remitente es un Gmail (`suplementospanamashop@gmail.com`) sin dominio verificado → pendiente verificar dominio `suplementospanama.net` en Mailchimp (requiere acceso DNS del usuario)
- Los correos de confirmación los genera Mailchimp (doble opt-in); la personalización (logo, textos) se hace en **Audience → Signup forms → Form builder → "Opt-in confirmation email"**

### Incidente JSON de Elementor (importante)
- Primer intento de agregar widgets al `_elementor_data` usó `update_post_meta(...)`, que aplica `wp_unslash` y **rompe las comillas escapadas del JSON** → el footer dejó de renderizar por completo (página shop 351K→297K, sin `nwsltr01`/`3205c4`)
- **Fix**: restaurar con `update_metadata('post', $id, '_elementor_data', wp_slash($json))` (método correcto) + `wp elementor flush_css --force` + `wp sg purge`
- **Lección**: para editar `_elementor_data` siempre usar `update_metadata` + `wp_slash`, nunca `update_post_meta`
- Widgets modificados/insertados: el primer widget HTML con CSS (`nwsltrcss`) perdió su tag `<style>` al renderizar y quedó como **texto visible** (el "bloque de código" en mobile) → se eliminó y el CSS se movió a `functions-additions.php` via `wp_head`

### Estilos (en `functions-additions.php` del child theme, inyectados via `wp_head` en `<style id="sp-mc4wp-alert">`)
- **Mensaje de confirmación**: `color:#b0ffaf` (verde claro) con fondo `rgba(255,255,255,.10)`, `!important` para no ser pisado por el tema
- **Mensaje de error**: `color:#ffc1b8`
- **Botón "suscribete"**: `background:#C0392B` (rojo), hover `#96281B`
- **Alineación del form**: `.mc4wp-form-fields{display:flex;flex-wrap:wrap;gap:10px;justify-content:center;align-items:center;width:100%}`; inputs `flex:1 1 200px` con fondo blanco/borde; mobile (`@media max-width:767px`): columnas apiladas al 100%
- **Texto sobre el form** ("Manténgase actualizado..."): `color:#CACACA` — aplicado en footer (`nwslrte1`) y home (`59db94`) tanto en `_elementor_data` como via regla CSS `.elementor-element-59db94,.elementor-element-nwslrte1{color:#CACACA!important}` (porque el CSS de Elementor de la home canvas se carga como archivo externo y no reflejaba el cambio)

### Backups (en servidor, `~/backups_newsletter2/`)
- `footer414_pre_fix2.json`, `home18625_pre_fix2.json` (previos a reaplicar estilos)
- `functions-additions_pre_newsletter_css.php` (previo a anexar el CSS de newsletter)

### Verificación final en vivo
- Shop y home: `mc4wp-form-417` presente, `<style id="sp-mc4wp-alert">` con `#b0ffaf`/`#C0392B`/`#CACACA`, sin bloque de código visible, sin errores PHP

### Pendiente
- Verificar dominio remitente `suplementospanama.net` en Mailchimp para evitar spam (requiere acceso DNS)
- Documentar (este mismo registro) cubre todos los cambios de hoy

## 2026-08-05 — Dominio `suplementospanama.net` verificado y autenticado en Mailchimp (fin del spam)

### Problema
- El correo de confirmación del doble opt-in caía en **spam** porque el remitente era un Gmail público (`suplementospanamashop@gmail.com`) y el dominio de envío no estaba verificado ni autenticado en Mailchimp
- La API de Mailchimp **no permite** registrar/verificar dominios (`/verified_domains` → 404); solo la interfaz web (`mailchimp.com`), por lo que estos pasos fueron manuales del lado del usuario con guía

### Preparación del buzón del dominio
- No existía buzón en el servidor para el dominio (el correo de SiteGround se gestiona por panel, no por SSH)
- Se creó un **forwarder** en SiteGround (Site Tools → Email → Forwarders): `newsletter@suplementospanama.net` → `suplementospanamacrm@gmail.com` (el forwarder crea la dirección automáticamente, no necesita buzón previo)
- Verificado: los MX del dominio apuntan a `antispam.mailspamprotection.com` (SiteGround), así que el forwarder recibe el correo

### Verificación del dominio (manual, Mailchimp web)
- **avatar → Account & billing → Domains → Email Domains → Add & Verify Domain** con `newsletter@suplementospanama.net`
- Mailchimp envió el correo de verificación (que llegó al Gmail vía forwarder) → clic **Verify Domain Access** → dominio verificado

### Autenticación del dominio (SPF + DKIM + DMARC)
- **Importante**: la opción "conectar dominio" del website builder NO es la correcta; el flujo real está en **Account & billing → Domains**
- Mailchimp generó los registros (usuario probó primero el flujo automático "Connected" que no propagó, luego el **manual** "Other → manually authenticate"):
  - **CNAME `k2._domainkey`** → `dkim2.mcsv.net`
  - **CNAME `k3._domainkey`** → `dkim3.mcsv.net`
  - **TXT `_dmarc`** → `v=DMARC1; p=none;`
- Se agregaron/editaron en **SiteGround → Site Tools → Domain → DNS Zone Editor**:
  - 2 CNAME nuevos (`k2._domainkey`, `k3._domainkey`)
  - TXT `_dmarc` **editado** (no duplicado — solo puede existir uno): de `v=DMARC1; p=none; aspf=r; adkim=r;` → `v=DMARC1; p=none;`
  - TXT SPF de la raíz **editado** (no duplicado): `v=spf1 +a +mx include:suplementospanama.net.spf.auto.dnssmarthost.net ~all` → **+ `include:servers.mcsv.net`** → `v=spf1 +a +mx include:suplementospanama.net.spf.auto.dnssmarthost.net include:servers.mcsv.net ~all`
- Verificación desde local (nslookup): los CNAME, `_dmarc` y SPF propagaron correctamente en los nameservers autoritativos (`ns1/ns2.siteground.net`); el SPF visto "viejo" en 8.8.8.8 era caché temporal de Google DNS
- Mailchimp marcó el dominio como **Authenticated**

### Remitente de la audiencia (API, desde el servidor SSH — local daba timeout)
- PATCH `lists/27b4cb9f8c` (`~/set_sender.sh`):
  - `campaign_defaults.from_email` → `newsletter@suplementospanama.net`
  - `campaign_defaults.from_name` → `Suplementos Panama`
  - `campaign_defaults.subject` → `Noticias y ofertas de Suplementos Panama`
  - `campaign_defaults.language` → `es`
  - `notify_on_subscribe` / `notify_on_unsubscribe` → `suplementospanamacrm@gmail.com`
- Se detectó que el PATCH dejó `double_optin=false` → corregido a `true` (`~/set_doi.sh`) para mantener la confirmación por correo

### Prueba real anti-spam
- Contacto `andrescastillob@gmail.com` borrado (DELETE 204) y resuscrito como `pending` → llegó la confirmación desde el dominio; pero la prueba **no es concluyente** porque el usuario ya lo había sacado de spam manualmente (Google ya lo tenía "aprendido")
- Prueba limpia: suscripción de `suplementospanamacrm@gmail.com` (correo que nunca había recibido correo del remitente) como `pending` → **confirmado que llega bien en bandeja de entrada desde `newsletter@suplementospanama.net`, sin spam**

### Resultado
- El dominio `suplementospanama.net` queda verificado y autenticado (SPF + DKIM `k2/k3` + DMARC) en la cuenta corporativa Mailchimp (`suplementospanamashop@gmail.com`, lista `27b4cb9f8c`)
- Todos los correos de confirmación y futuras campañas saldrán de `newsletter@suplementospanama.net` con autenticación válida → ya no deberían caer en spam
- Doble opt-in confirmado activo

### Notas
- Los scripts (`set_sender.sh`, `set_doi.sh`, `test_subscribe_new.sh`, `test_crm.sh`, `check_doi.sh`) fueron temporales en el servidor y se eliminaron tras ejecutar
- API key `<redactada>-us1` (nunca escribirla real en este documento para no romper el push a GitHub)

## 2026-08-05 — Agentes ecommerce movidos a config global de opencode (multi-proyecto)

### Decisión
- Los agentes vivían en `.opencode/agents/` del proyecto (`C:\suplementos\stock-suplementos\.opencode\agents\`), por lo que **solo eran operativos en este repo**
- Para usarlos en **cualquier proyecto** (patrón multi-proyecto ya diseñado: recursos genéricos + carpeta `sp/` por proyecto), se movieron a la config global de opencode
- **Motivo de "mover" y no "copiar"**: el proyecto tiene precedencia sobre el global; dos copias implicaban divergencias (versiones viejas del repo pisarían las globales). Una sola fuente de verdad en global

### Cambios realizados
- Copiado `C:\suplementos\stock-suplementos\.opencode\agents\` → `C:\Users\Usuario\.config\opencode\agents\` (51 archivos)
- Verificado: árbol completo (5 agentes + subcarpetas `sp/`, `skills/`, `competitors/`, `output/` + `summary.md` + `marketing-descriptions.md`) migrado; referencias relativas internas (`../contenidos-ecommerce/skills/`, `../contenidos-ecommerce/competitors/`) se movieron juntas y siguen válidas
- Eliminado `.opencode/agents/` local del proyecto
- Config global `~/.config/opencode/opencode.json` no requirió cambios (solo modelo deepseek-v4-flash)

### Resultado
- Los 5 agentes ecommerce (analytics, contenidos, geo, seo, structured-data-schema) + sus subagentes quedan **operativos en cualquier proyecto** de esta máquina
- Para un nuevo proyecto: crear su carpeta específica dentro de cada agente global (ej. `sp/` para Suplementos Panamá, `clientex/` para otro cliente) y referenciarla desde el `.md` principal
- El proyecto conserva `.opencode/skills/stock-update/` (skill específico de inventario Suplementos Panamá, no es un agente global)

## 2026-08-05 — Chat WhatsApp activado en las 6 sucursales (Google Business Profile)

### Funcionalidad
- Google Business Profile permite añadir una opción de chat directo (WhatsApp o texto) al perfil de negocio: **Edit profile → Contact → Chat** (documentación oficial: support.google.com/business/answer/15013580)
- El número usado es el del CRM Kommo: **+507 6015-3257** (`https://wa.me/50760153257`)
- La opción "Chat" está **disponible en los 6 perfiles** de las sucursales
- **Regla importante**: si se configuran WhatsApp + Text message a la vez, Google solo muestra la opción de texto → elegir **solo WhatsApp**

### Enlaces click-to-chat por sucursal (configurados en cada perfil GBP)
| Sucursal | URL configurada |
|---|---|
| El Cangrejo | `https://wa.me/50760153257?text=Hola%20estoy%20revisando%20su%20perfil%20de%20google%20en%20El%20Cangrejo%20y%20necesito%20informaci%C3%B3n` |
| Megapolis | `https://wa.me/50760153257?text=Hola%20estoy%20revisando%20su%20perfil%20de%20google%20en%20Megapolis%20y%20necesito%20informaci%C3%B3n` |
| Atrio Mall | `https://wa.me/50760153257?text=Hola%20estoy%20revisando%20su%20perfil%20de%20google%20en%20Atrio%20Mall%20y%20necesito%20informaci%C3%B3n` |
| San Francisco | `https://wa.me/50760153257?text=Hola%20estoy%20revisando%20su%20perfil%20de%20google%20en%20San%20Francisco%20y%20necesito%20informaci%C3%B3n` |
| Altos de Panamá | `https://wa.me/50760153257?text=Hola%20estoy%20revisando%20su%20perfil%20de%20google%20en%20Altos%20de%20Panam%C3%A1%20y%20necesito%20informaci%C3%B3n` |
| Metromall | `https://wa.me/50760153257?text=Hola%20estoy%20revisando%20su%20perfil%20de%20google%20en%20Metromall%20y%20necesito%20informaci%C3%B3n` |

### Beneficios
- El cliente llega directo a WhatsApp (CRM Kommo) con mensaje precargado indicando la sucursal → conversación calificada y trazable en Kommo
- El mismo número se puede usar en varios perfiles (permitido por Google)
- Métricas de rendimiento del chat disponibles en el panel de GBP

## 2026-08-07 — Unificación NAP: número del footer a `+507 6015-3257` (CRM Kommo)

### Problema (detectado por agente geo-ecommerce)
- Inconsistencia NAP: el footer mostraba `+(507) 6811-1649` mientras WhatsApp/GBP/schema usan `+507 6015-3257`
- Para las IAs generativas (GEO) la inconsistencia de número rompe la confiabilidad de la entidad "Suplementos Panamá"

### Decisión del usuario
- Unificar todo al número del CRM: **+507 6015-3257**

### Auditoría previa (WP-CLI)
- Solo 3 posts publicados tenían el número viejo:
  - **Footer HFE (414)** — usado por shop, producto, sucursales, combos y todas las páginas con HFE
  - **Inicio (18625)** — página canvas con footer inline (widget text-editor `b2a393` → `+(507) 6811-1649`)
  - **Términos y Condiciones (9)** — contenido Gutenberg (`Teléfono/WhatsApp: +507 6811-1649`, 2 ocurrencias)
- Otras canvas (8485 "Próximamente", 20668 "Nuevo Inicio de Celular") NO contienen el número
- Sin coincidencias en metas `rank_math_*`

### Cambios realizados
- `fix_nap.php` vía `wp eval-file`: reemplazo de `6811-1649` → `6015-3257`
  - 414 y 18625: `update_metadata('post', $id, '_elementor_data', wp_slash($nuevo))` (método correcto para JSON de Elementor)
  - 9: `wp_update_post` con `post_content` corregido
- Backups en `~/backups_nap_20260807-150518/` (`414_pre.json`, `18625_pre.json`, `9_pre_content.html`)
- Cachés purgadas: `wp cache flush`, `wp sg purge`, `wp elementor flush_css --force`

### Verificación en vivo (con ?napcheck=1)
- Home, shop, términos, ficha de producto, sucursales y combos: `6811-1649` = **0 ocurrencias**, `6015-3257` = **1** → NAP unificado

## 2026-08-07 — Metamensajes optimizados (SEO + GEO) aplicados en HOME y COMBOS

### Contexto
- Se usaron 3 agentes (`seo-ecommerce`, `contenidos-ecommerce`, `geo-ecommerce`) para generar meta títulos y meta descripciones optimizados para Google y buscadores IA (ChatGPT, Gemini, Perplexity, AI Overviews). El 4º solicitado (`marketing-descriptions`) está obsoleto y su rol lo cubrió `contenidos-ecommerce`
- **Hallazgo transversal de geo-ecommerce**: inconsistencia NAP (footer `+(507) 6811-1649` vs WhatsApp `+507 6015-3257`) → resuelto en la entrada anterior (unificación al número del CRM)

### Metas aplicados (Rank Math)
**HOME (post 18625)** — recomendación seo-ecommerce/geo-ecommerce:
- **Title**: `Suplementos Deportivos en Panamá | Proteínas y Creatina`
- **Meta description**: `Suplementos deportivos en Panamá: proteínas, creatina, pre-entrenos y más. Envío gratis desde $150, retiro en 6 sucursales y asesoría experta.`

**COMBOS (product_cat term id 284, slug `combos`)** — recomendación seo-ecommerce:
- **Title**: `Combos de Suplementos | Ahorra Hasta $84.98 | Panamá`
- **Meta description**: `Compra combos de proteína, creatina y pre-entreno en Panamá. 22 paquetes con ahorro real de hasta $84.98. Envío gratis en pedidos mayores a $150.`

Criterios: keyword principal al inicio, datos verificables del catálogo (6 sucursales, envío gratis >$150, 22 paquetes, ahorro hasta $84.98 = combo Elite Performance Stack), entidad geográfica "Panamá" en el title (que el de combos perdía), formato conversacional para extractabilidad de IA.

### Implementación
- `apply_metas.php` vía `wp eval-file`: `update_post_meta(18625, rank_math_title/description, ...)` + `update_term_meta(284, rank_math_title/description, ...)`
- Backups en `~/backups_metas_20260807-150750/` (`home_18625_pre.txt`, `combos_284_pre.txt`)
- Cachés purgadas (`wp cache flush`, `wp sg purge`)

### Verificación en vivo
- Home: title `Suplementos Deportivos En Panamá | Proteínas Y Creatina` (Rank Math capitaliza iniciales automáticamente) + desc nueva
- Combos: title `Combos De Suplementos | Ahorra Hasta $84.98 | Panamá` + desc nueva
- Nota: la capitalización automática de Rank Math se ajusta en **Rank Math → Titles & Meta → General → Capitalización** si se quiere el literal en minúsculas

## 2026-08-07 — Schema `SiteNavigationElement` inyectado (refuerzo para sitelinks y GEO)

### Contexto
- El objetivo: que al buscar "Suplementos Panamá" en Google aparezcan sitelinks hacia Sucursales, Combos, Creatina y Proteínas
- **Aclaración importante**: los sitelinks los genera Google automáticamente según la estructura de enlaces internos; NO hay configurarlos directamente. `SiteNavigationElement` es una señal semántica de refuerzo (SEO + GEO), no un rich result que garantice sitelinks
- La estructura ya existía: menú/footer con enlaces a Sucursales (hub + 6), Combos, Creatina, Proteínas — verificado en el HTML

### Generación (agente structured-data-schema)
- El agente auditó la navegación real (header `menu-1-7c0b47`, menú vertical de categorías `menu-1-bb00b2`) y generó:
  - JSON-LD `SiteNavigationElement` con 54 nodos: navegación principal + 6 sucursales + promociones (combos/descuentos) + creatina + proteínas (7 sub) + aminoácidos + pre-entrenos + quemadores + vitaminas + salud + snacks + diuréticos, con jerarquía `hasPart` y URLs absolutas
  - JSON-LD `BreadcrumbList` de ejemplo (combos)
- Recomendó inyección vía child theme `wp_head` (más seguro que Custom Schema de Rank Math, que es page-scoped y se fusionaría con su @graph)

### Implementación
- Backup: `~/backups_schema_nav_20260807.php` (functions-additions.php completo)
- Sección 13 anexada a `functions-additions.php` del child:
  - `sp_emitir_schema_navegacion()` → emite `<script id="sp-schema-navegacion">` con el @graph de `SiteNavigationElement` en `wp_head` (priority 1)
  - `sp_schema_breadcrumbs()` → emite `BreadcrumbList` para home y categoría combos SOLO si Rank Math no lo hace (evita duplicados; se auto-desactiva porque Rank Math ya emite el suyo)
- `php -l` sin errores; cachés purgadas

### Verificación en vivo
- Home y combos: `sp-schema-navegacion` presente, **54 nodos** `SiteNavigationElement` cada una
- Combos: `BreadcrumbList` = 1 (el de Rank Math; el mío no se duplica) → sin doble breadcrumb
- Confirmado que no se duplican tipos existentes (WebSite/Organization/Place siguen siendo solo de Rank Math)

### Notas del agente (recomendaciones aparte, no bloqueantes)
- El ítem "POWER RACK" del menú apunta a `/blog/` (label confuso) → corregir etiqueta en Apariencia → Menús
- El ítem "Marcas" usa `href="#"` (sin URL) → mapeado a `/shop/` en el schema
- Slug real de snacks es `snacks-y-bedidas` (con typo) → candidato a 301 al slug correcto
- Validar en Rich Results Test + GSC URL Inspection a los 2-3 días

## 2026-08-07 — Página directorio /marcas/ + schema actualizado (Marcas → /marcas/, Blog → "Power Rack")

### Contexto
- El ítem de menú "MARCAS" (db_id 13059) usaba `href="#"` y el schema apuntaba a `/shop/` como fallback
- El usuario aclaró que el blog se llama comercialmente **"Power Rack"** (la etiqueta del menú ya era correcta; lo que estaba mal era mi schema que decía "Blog")
- Se pidió crear una página tipo directorio con los enlaces de todas las marcas

### Auditoría de marcas (taxonomía `product_brand`)
- 31 términos de marca en la taxonomía, pero el campo `count` estaba desactualizado (0 en todas)
- Con `WP_Query` real: **25 marcas con productos publicados**, 6 vacías (ABE, ANS, REFRI BUM, REFRI LABRADA, REFRI QHUSH, USN)
- Los archives `/product-brand/{slug}/` devuelven 200
- El dropdown de MARCAS del menú tenía 21 items (algunos a marcas vacías)

### Implementación
- **Sección 14** anexada a `functions-additions.php` del child: shortcode `[sp_directorio_marcas]` que:
  - Lista `product_brand` ordenado por nombre (solo las que tienen productos, contados con SQL directo sobre `term_relationships` + `posts` publicados)
  - Renderiza grid responsive de tarjetas con nombre + número de productos, enlazando a `/product-brand/{slug}/`
- **Sección 13 actualizada**: nodo `marcas` → `url: /marcas/`; nodo `blog` → `name: "Power Rack"` (antes "Blog")
- Página creada: **ID 21919** `/marcas/`, template default, contenido = shortcode
- SEO Rank Math: title `Marcas de Suplementos Deportivos | Suplementos Panamá`, description con directorio + marcas destacadas
- Menú: item 13059 actualizado de `#` → `/marcas/` (vía `wp post meta update _menu_item_url`, porque `wp menu item update --url` no toca items custom). El dropdown de sub-marcas se mantuvo intacto
- `php -l` OK; permalinks flusheados; cachés purgadas

### Verificación en vivo
- `GET /marcas/` → 200, **25 marcas** en el grid (excluye las 6 vacías)
- Title visible: `Marcas De Suplementos Deportivos | Suplementos Panamá` (Rank Math capitaliza iniciales)
- Schema en home: Marcas → `https://suplementospanama.net/marcas/`, Blog → `Power Rack`
- Página /marcas/ también emite `sp-schema-navegacion` y 1 BreadcrumbList (Rank Math) sin duplicados
- Menú en vivo: `MARCAS` href = `/marcas/`, dropdown con sub-marcas intacto

## 2026-08-07 — Revisión API de reseñas (GBP vs Places) — My Business APIs ya estaban habilitadas

### Estado diagnosticado
- Constantes definidas en `wp-config.php`: `GOOGLE_PLACES_API_KEY`, `GBP_CLIENT_ID`, `GBP_CLIENT_SECRET`, `GBP_REFRESH_TOKEN`
- **OAuth autorizado** ✅: refresh token renueva bien, scope `https://www.googleapis.com/auth/business.manage` en la respuesta
- **My Business API: HTTP 429 "Quota exceeded" con `quota_limit_value: 0`** ⚠️ → sin embargo, el usuario confirmó que TODAS las APIs del proyecto YA están habilitadas "de antes" → el 429 con cuota 0 es otra cosa (posible restricción de cuota diaria/per-minute del proyecto, o la cuenta GBP no está vinculada como propietaria, o el proyecto usa otra credencial que la de la cuenta correcta)
- **Places API funciona** ✅ → el sitio sirve reseñas reales (5 por sucursal) vía fallback de `sp_fetch_google_reviews()`
- Verificado en vivo en `/sucursales/el-cangrejo/`: reseña real "Yummy Yummy" visible (servida por Places API)

### Datos del proyecto (referencia para Google Cloud Console)
- Project number: `531692178989`
- Nombre del proyecto: **suplementos-panama-gbp**
- OAuth client ID: `531692178989-si942blvg5n57t6emd69m5f0jna0voj8.apps.googleusercontent.com`
- Cuenta Business Profile que agrupa las 6 sucursales: **suplementospanamashop@gmail.com**
- **APIs habilitadas en el proyecto (confirmadas por el usuario el 2026-08-07, todas "de antes")**:
  1. My Business API (`mybusiness.googleapis.com`) → la usa `sp_gbp_get_reviews()` (v4 .../reviews)
  2. My Business Account Management API (`mybusinessaccountmanagement.googleapis.com`) → la usa `sp_gbp_get_accounts()`
  3. My Business Business Information API (`mybusinessbusinessinformation.googleapis.com`) → la usa `sp_gbp_get_locations()`
  4. My Business Notifications API
  5. My Business Lodging API
  6. My Business Q&A API
  7. My Business Place Actions API
  8. My Business Verifications API
  9. Business Profile Performance API

### Próximo paso (aun pendiente)
- **CAUSA RAÍZ CONFIRMADA**: error 429 con `quota_limit_value=0` y `DefaultRequestsPerMinutePerProject` es un problema conocido de Google: las My Business APIs (accountmanagement, businessinformation, mybusiness v4) tienen cuota **0 RPM por defecto** para el proyecto, aunque estén habilitadas. No es una falta de habilitación ni un error del código.
- **DATO CLAVE de la consola**: en "Quotas and system limits" la métrica **"Requests per minute" → Value 0 → Adjustable: No** → la cuota está bloqueada en 0 y **NO se puede aumentar desde la consola** (ni siquiera aparece opción de editar). Es un límite inicial que Google impone a estas APIs.
- **Solución** (confirmado por múltiples reportes públicos y la documentación oficial):
  1. **Contactar Google Cloud Support** pidiendo habilitar el acceso a las Business Profile APIs para el proyecto `suplementos-panama-gbp` (project number `531692178989`) → URL oficial: https://cloud.google.com/docs/quotas/help/request_increase — los reportes de la comunidad indican que soporte lo habilita directamente sin más trámite
  2. La cuota NO se puede solicitar por la UI (Adjustable: No), por lo que el formulario de soporte/request increase es la única vía
- **2026-08-07 (mismo día)**: el usuario **activó Quota adjuster** (Configurations) en el proyecto. Re-test inmediato: sigue 429 con `quota_limit_value: 0` → el adjuster **no es instantáneo** (ajusta según uso acumulado; puede tardar horas/días). Pendiente re-testear en 24-48h.
- Tras la aprobación: purgar transients (`sp_gbp_access_token`, `sp_gbp_accounts`, `sp_gbp_locs_*`, `sp_gbp_locmap_*`) y re-testear `GET accounts` — si da 200, las reseñas pasarán a servirse por GBP (más campos: respuesta del negocio, historial completo)

### Nota operativa
- No hay urgencia: el sitio ya muestra reseñas reales de Google por Places API

## 2026-08-07 � Fix: retiro en sucursal no visible para combos en el carrito

### Problema
- Para productos normales las opciones "Envio" / "Recoger en local" (y el selector de sucursal) se mostraban en el carrito/checkout, pero para combos NO aparec�an (solo "Envio").
- Los combos son productos grouped; el mu-plugin combo-price.php los agrega al carrito como un solo item con product_id = COMBO_ID (sin variaci�n) y los componentes en cart_item_data['combo_children'].
- La meta _sucursales_disponibles vive en los productos hijos, NO en el COMBO_ID.

### Causa ra�z
- sp_filter_shipping_methods() y sp_get_valid_sucursales_for_cart() (functions.php del child) usaban sp_get_meta_id() -> COMBO_ID -> sin meta -> $any_sucursal=false -> se eliminaba local_pickup y el select de sucursal no se renderizaba.

### Fix (functions.php del child, copia local en local/functions_current.php)
- Nuevo helper sp_get_cart_item_ids(): si el cart item tiene combo_children[], devuelve los ids reales de los hijos (variation_id o product_id); si no, el id normal.
- sp_filter_shipping_methods(): itera todos los ids por item (hijos incluidos); conserva local_pickup si cualquier hijo tiene meta.
- sp_get_valid_sucursales_for_cart(): una sucursal es v�lida solo si TODOS los items del carrito (y todos los hijos de cada combo) tienen esa sucursal en _sucursales_disponibles.

### Verificaci�n (WP-CLI en vivo)
- php -l OK sobre el functions.php nuevo.
- Audit de los 22 combos grouped: 5 tienen TODOS sus hijos con _sucursales_disponibles (21525, 21521, 21515, 21511, 21510), 16 parciales y 1 sin ninguno (esos siguen siendo solo Delivery, correcto).
- Simulaci�n de carrito con combo 21525 (IsoJect + Raw Pre + Creatina VMS): sp_get_valid_sucursales_for_cart devuelve 4 sucursales (SP El Cangrejo, Atrio Mall, San Francisco, Altos de Panam�) y sp_filter_shipping_methods conserva local_pickup.

### Despliegue
- Backup en servidor: unctions.php.bak-20260807.
- wp cache flush ejecutado.

## 2026-08-07 (2) � Fix: sucursal elegida en carrito no quedaba indicada en checkout

### Problema
- Con el fix de combos activo, el selector de sucursal aparecia en el carrito y el usuario podia elegir, pero al pasar al checkout la sucursal NO quedaba indicada (solo "Recoger en local").
- El dato es critico: quien recibe el pedido necesita saber donde lo retirara el cliente.

### Causa raiz (doble)
1. **JS spBindSucursalToggle()** (functions.php del child, corre cada 800ms): si el metodo de envio marcado no era local_pickup (default = flat_rate), hacia sel.value='' BORRANDO la sucursal preseleccionada del checkout que venia de la session del carrito.
2. **PHP**: al cargar el checkout, chosen_shipping_methods en session suele estar vacio (WooCommerce no lo persiste hasta que el usuario toca un metodo), por lo que sp_get_sucursal_review_html() y sp_checkout_sucursal_field() no detectaban local_pickup y no renderizaban la fila "Sucursal".

### Fix (functions.php del child, copia local en local/functions_current.php)
- spBindSucursalToggle(): solo muestra/oculta el campo; ya NO borra el valor del select.
- spUpdateSucursalInfo(): construye la fila del review solo cuando local_pickup esta activo.
- Nuevo helper sp_is_local_pickup_selected(): detecta pickup desde POST (shipping_method), session (chosen_shipping_methods), o fallback � si hay sucursal elegida en session y local_pickup esta entre los rates del paquete, asume retiro (intencion del usuario).
- Nuevo helper sp_get_cart_shipping_rate_ids(): extrae los rate ids del paquete (corrige que calculate_shipping_for_package devuelve el paquete con ates['rates'] anidados, no el array directo).
- sp_get_sucursal_review_html() y sp_validate_sucursal_field() usan el helper.

### Verificacion (WP-CLI + curl end-to-end)
- 4 casos de backend OK (POST pickup, session chosen, fallback primer-load desde carrito, flat_rate default).
- Flujo real con curl (combo 21525 + session sucursal=1 + update_cart local_pickup:4 -> /checkout/): SP_DEBUG selected=1 method=local_pickup, fila "Sucursal: SP El Cangrejo" en review, select preseleccionado, campo visible.
- php -l OK, wp cache flush.

## 2026-08-07 (3) - Fix: fila "Sucursal" del review se borraba en el navegador (regresion JS)

### Problema
- El usuario reporto que en el checkout real seguia sin verse la sucursal elegida en la seccion Envio, aunque la opcion "Recoger en local" estaba marcada y el backend (verificado en sesion 2) renderizaba la fila correctamente.
- Busqueda en GitHub/repos confirmo que la implementacion documentada del commit `cdf7bc9` (Jul 18 2026, "feat: sucursal info appears in checkout review") es la fuente de verdad.

### Causa raiz
- **Regresion JS introducida en la sesion 2**: `spUpdateSucursalInfo()` (corre cada 800ms via setInterval) se le anadio `!isPickup` al early-return. Como al llegar al checkout WooCommerce marca por defecto `flat_rate` (chosen_shipping_methods no se persiste hasta que el usuario toca un radio), `isPickup` era `false` y el JS ejecutaba `stale.remove()` **borrando la fila `tr.sp-sucursal-review` que el servidor ya habia renderizado** por el fallback de intencion (sucursal en session + rate local_pickup disponible).
- El commit `cdf7bc9` NO tenia ese check; solo validaba `!sel || !sel.value || !field`.

### Fix (functions.php del child, copia local en local/functions_current.php)
- `spUpdateSucursalInfo()`: el early-return ya no incluye `!isPickup`. Ahora solo limpia la fila si no hay select con valor o no existe el campo. Si no es pickup pero hay sucursal elegida, hace `return` sin tocar la fila server-side (respeta la decision del fallback).
- `sp_sucursal_fragment()`: ahora SIEMPRE emite la clave `tr.sp-sucursal-review` (aunque sea vacia) para que WooCommerce AJAX `update_order_review` la elimine cuando el usuario cambia explicitamente a flat_rate.

### Verificacion
- php -l OK. Deploy con backup `functions.php.bak-20260807-fixcheckout`.
- SG Optimizer regenero el combined-js (hash nuevo 4e229aad): confirmado que el JS servido ya NO contiene `!isPickup ||` y SI contiene `if (!isPickup) return`.
- Flujo end-to-end con cookie con combo 21525 + POST cart (shipping_method local_pickup:4 + sp_sucursal_retiro=1) -> /checkout/: SP_DEBUG selected=1 method=local_pickup, fila "Sucursal: SP El Cangrejo" presente, radio local_pickup checked, select preseleccionado.
- wp cache flush. El fix es puramente JS/fragment; el HTML server-side ya era correcto en la sesion 2.

## 2026-08-07 (4) - Fix: el guardado de la sucursal desde el carrito se perdia (listener no sobrevivia re-render AJAX)

### Problema
- El usuario confirmo con navegador limpio e incognito que la sucursal elegida seguia sin aparecer en el bloque envio del checkout (backing del session 3 no bastaba).

### Causa raiz
- El select de sucursal del CARRITO (`#sp_sucursal_retiro_cart`) esta FUERA del form del carrito (en el hook `woocommerce_cart_totals_after_shipping`). El unico mecanismo que persiste la sucursal a la session es el AJAX `sp_save_sucursal`.
- `spBindSucursalChange()` enlazaba un **listener directo** al select al cargar la pagina. Cuando el usuario marcaba "Recoger en local" en el carrito, WooCommerce re-renderiza el carrito vía AJAX (`update_shipping_method` / `update_cart`) y reemplaza el DOM del select, perdiendo el listener -> el AJAX `sp_save_sucursal` nunca se disparaba -> la session llegaba vacía al checkout -> el servidor no preseleccionaba la sucursal.

### Fix (functions.php del child)
- Reemplazado el listener directo por **delegacion de eventos** en `document` para los ids `sp_sucursal_retiro` (checkout) y `sp_sucursal_retiro_cart` (carrito). La delegacion sobrevive a los re-renders de WooCommerce.

### Verificacion
- php -l OK. Deploy con backup `functions.php.bak-20260807-delegacion`.
- SG Optimizer regenero el combined-js (hash 89c082cd): confirmado que contiene la delegacion, no contiene `spBindSucursalChange`, sintaxis JS valida con node.
- Flujo end-to-end: AJAX sp_save_sucursal (success:true) + POST cart con shipping_method local_pickup -> /checkout/: SP_DEBUG selected=1 method=local_pickup, fila review presente, radio pickup checked, select preseleccionado, div info sucursal visible.
- wp cache flush.

## 2026-08-07 (5) - Documentacion de la funcionalidad protegida "Retiro en sucursal" + aviso para agentes

### Qué se hizo
- Se creo **`docs/RETIRO-SUCURSAL.md`**: guia de recuperacion completa y unica fuente de verdad para la funcionalidad de retiro en sucursal (carrito/checkout/combos).
  - Arquitectura: donde vive el codigo, tabla de funciones PHP clave y bloques JS con su rol.
  - Historial de los 4 bugs corregidos (A combos, B preseleccion checkout, C regresion JS `!isPickup`, D delegacion de eventos) con sintoma de re-ocurrencia para diagnostico rapido.
  - Checklist de diagnostico (debug `SP_DEBUG`, fila review en HTML, validacion combined-js con node, flujo curl end-to-end).
  - Procedimiento de reparacion rapida (backup, editar local, php -l, deploy, purgar cachés SG Optimizer, verificar, commit).
  - REGLA DE ORO: que no tocar y errores clasicos que la rompen.
- Se creo **`AGENTS.md`** en la raiz del repo: aviso permanente para que cualquier agente futuro sepa que NO debe tocar `sp_*`, `combo-price.php`, `sp_sucursal_js()` ni `_sucursales_disponibles` en arreglos no relacionados, y que si un cambio de otra indole toca `functions.php` del child debe AVISAR primero y verificar con el checklist.
- `historial-chat.md` actualizado con esta entrada.

### Motivo
- La funcionalidad se ha roto 4 veces y es fragil. Si vuelve a dañarse, cualquier agente/desarrollador debe poder diagnosticar y reparar en minutos leyendo `docs/RETIRO-SUCURSAL.md`, sin depender de la memoria de las sesiones previas.

### Verificacion
- Docs en repo local (pendiente commit/push).

## 2026-08-07 (6) - Aviso en ficha para productos agotados online (regla stock minimo) con stock en sucursal

### Contexto
- Producto Proteina Whey Forzagen 2 lbs (18933, variable, variacion 18939 Dutch Chocolate): tiene 6 unidades fisicas en sucursales (1,6,7,8) pero `_stock_status=outofstock` por la regla de reserva minima ("stock <=6 -> outofstock").
- El usuario pregunto por que la ficha mostraba "Disponible para retiro en" (cuadro verde con unidades) si el producto no tiene existencia para venta online.
- Explicacion: el cuadro de sucursal usa el stock FISICO por sucursal (`_sucursal_X_stock`), que es independiente del `_stock_status`. Al estar outofstock online, el cliente NO puede retirar/pedir online; solo puede ir a comprarlo presencial en la sucursal.

### Regla de negocio (confirmada por el usuario)
- La regla "<=6 -> outofstock" SE CONSERVA: es el minimo de reserva para ventas por sucursal. El fix NO cambia la regla; solo informa mejor al cliente.

### Cambio (functions.php del child, copia local en local/functions_current.php)
- PHP `sp_show_sucursal_stock()`:
  - Captura `_stock_status` por variacion (`$variation_status`) y lo expone al JS en el atributo `data-sp-status` del contenedor; los nombres de sucursal se exponen en `data-sp-names`.
  - Para productos SIMPLES: si `$product->get_stock_status()==='outofstock'` pero hay stock>0 en sucursal, renderiza server-side el aviso `sp-store-msg`: "Disponible solo para compra por sucursal en: <nombres>."
- JS `spUpdateStock()` (ficha variable): si la variacion elegida esta outofstock (`spStatus[vid]==='outofstock'`) pero tiene stock>0 en alguna sucursal (`storeList`), reemplaza el cuadro por el aviso naranja `.sp-store-msg`. Si no, comportamiento normal (lista verde o "Solo disponible para Delivery").

### Despliegue
- Backup: `functions.php.bak-20260807-aviso-sucursal`.
- php -l OK; wp cache flush; SG Optimizer regenero combined-js (hash `a8a7fabb97ab7bdd5557e0535e3e538a`) con `sp-store-msg` presente; sintaxis JS validada con node.

### Verificacion en vivo
- 18933 (2 lbs, outofstock por regla): data-sp-status contiene `18939:outofstock`; el JS reemplaza el cuadro por el aviso de compra presencial (verificado en combined-js; el render server-side es variable -> el aviso lo aplica JS al seleccionar la variacion).
- 18932 (5 lbs, instock): data-sp-status `18934:instock,18935:instock`; cuadro "Disponible para retiro en" normal = 1, aviso compra presencial = 0 (no se rompe el flujo).
- Checklist seccion 5 de docs/RETIRO-SUCURSAL.md OK (producto/carrito/checkout intactos).
- Docs actualizados: `docs/RETIRO-SUCURSAL.md` (BUG E + tabla JS), `historial-chat.md` (esta entrada).

## 2026-08-08 — Fix directorio /marcas/ + Términos y Condiciones

### Fix /marcas/
- **Causa raíz**: el deploy `f29c55d` (aviso presencial, 8-ago 00:14) sobrescribió `functions.php` desde la copia local `local/functions_current.php`, la cual no incluía la línea `require_once get_stylesheet_directory() . '/functions-additions.php';` que cargaba el shortcode `[sp_directorio_marcas]` (sección 14) y el schema de navegación (sección 13). El backup `functions.php.bak-20260807` sí contenía el require en su línea 1553.
- **Fix**: backup del functions.php actual (`functions.php.bak-20260808-marcas`), append quirúrgico del `require_once` al final del archivo vía SSH (evitando manipulación local que pudiera alterar CRLF o indentación). `php -l` OK.
- **Verificación**: `/marcas/` HTTP 200, 27 marcas, shortcode REGISTRADO. Schema navegación 54 nodos en home y combos. JS sucursal (`spUpdateStock`) presente en combined-js. Flujos protegidos OK.
- **Copia local sincronizada**: `local/functions_current.php` descargado del servidor post-fix (MD5 idéntico).

### Términos y Condiciones
- Generado `suplementos/terminos_y_condiciones_SP.html` con: índice con enlaces, proceso de compra paso a paso, tabla de métodos de pago, nota ACH vía WhatsApp, cláusula de problemas de plataforma (sección 4), estructura reorganizada.

## 2026-08-10 — Varios: retiro de productos, cambios de precio, fix sabor en combos, T&C

### Retiro de productos obsoletos
- **Brownie con Colágeno (ID 19043)**: variaciones ya stock=0, trashed. 301 redirect `/product/brownie-con-colageno/` → `/snacks-y-bedidas/brownies-y-galletas/` vía mu-plugin `redirect-brownie.php`.
- **Galleta Proteica (ID 19055)**: variación 19057 stock=9→0 y outofstock, trashed. 301 redirect `/product/galleta-proteica/` → `/snacks-y-bedidas/brownies-y-galletas/` vía mu-plugin `redirect-galleta.php`.
- Ambos ya solicitados para desindexación en GSC.

### Cambios de precio en combos
- **Elite Performance Stack (ID 21660)**: `_combo_price` 101.99→106.99. Contenido: ahorro $84.98→$79.98, $101.99→$106.99, 45%→42%.
- **ISO Total Pack (ID 21655)**: `_combo_price` 68.50→69.99. Contenido: ahorro $41.47→$39.98, $68.50→$69.99, 38%→36%.
- **ProLive Full Stack (ID 21647)**: `_combo_price` 94.99→99.99. Contenido: ahorro $60.98→$55.98, $94.99→$99.99, 39%→35%.
- **VMS Triple Stack (ID 21643)**: `_combo_price` 94.99→99.99. Contenido: ahorro $60.98→$55.98, $94.99→$99.99, 39%→35%. Descripción corta: "Ahorra $60.98"→"Ahorra $55.98". Verificado en vivo: HTTP 200, muestra 99.99, sin restos de 94.99.
- Imagen ISO Total Pack reemplazada: nueva `iso total pack.jpg` subida como attachment 21931, SEO data transferido, old attach 21654 eliminado.

### Fix: sabor de proteína no se guardaba en combos
- **Causa raíz**: `combo_add_to_cart_handler()` en `combo-price.php` no capturaba los atributos de variación (`$v->get_attributes()`), solo guardaba `variation_id`. En el carrito se usaba `'variation' => array()`, por lo que el sabor seleccionado no se persistía en carrito/checkout/pedido.
- **Fix en `combo-price.php`**:
  - Al construir `$combo_items[]` se añadió `'attributes' => $v->get_attributes()`.
  - Al actualizar cantidad en carrito existente, se refresca `combo_children` con los datos actualizados.
  - Nuevo hook `woocommerce_get_item_data` que muestra cada hijo del combo con su nombre+SKU en carrito/checkout.
  - En `woocommerce_checkout_create_order_line_item` se guarda meta visible "Incluye" con lista de componentes.
  - CSS `word-break: keep-all` para evitar cortes de palabra en metadatos.
- **Archivos**: `combo-price.php` (mu-plugin). Backup: `combo-price.php.bak-20260810`.

### Términos y Condiciones (ediciones posteriores)
- Sección "Provincias del interior" renombrada a "Para compras desde las provincias" con reglas actualizadas (Yappy espera confirmación, logística post-pedido, costo delivery, asistente virtual).
- Eliminada sección 5.3 "Envíos internacionales".
- Requisitos de entrega: identificación obligatoria para retiro, autorización para terceros.
- Sección 6 reescrita: revisión delante del despachador, foto+WhatsApp si anomalía, 24h plazo.
- Motivos de devolución: solo producto defectuoso o equivocado.
- Texto más empático sobre proceso de verificación de devoluciones.
- Regla de cambios post-envío con excepción a criterios de devolución.
- Condiciones de cambio por diferencia de precio (pagar diferencia o crédito restante).
- Yappy: "Pago telefónico con la app de Banco General".
- Nota: métodos online solo TDC/TDD y Yappy, otros requieren consulta.
- Sección 11: advertencia de responsabilidad del cliente por consumo sin supervisión médica.

## 2026-08-11 — Actualización masiva de precios: 18 combos según PDF

### Origen
- PDF `C:\suplementos\combos\combos-precio-y-nuevos.pdf` con la lista de precios de los combos activos (duos/trios numerados 1-18) y combos nuevos propuestos.
- Extraído con PyMuPDF (`fitz`); los 4 tríos (VMS Triple Stack, ProLive Full Stack, ISO Total Pack, Elite Performance Stack) ya estaban a precio nuevo, por lo que solo faltaban los 18 duos/trios.

### Cambios realizados (vía WP-CLI `eval-file` en producción)
- Script `update_prices.php` (dry-run previo: 18/18 con cambios detectados; luego `$DRY=false`).
- Cada combo: `_combo_price` actualizado + `post_content` con nuevo precio/ahorro/porcentaje + `post_excerpt` donde aplicaba.
- Mapa ID → precio nuevo:
  - 21510 Evofusion+Creatina VMS: 79.99→84.99 (ahorro ~$35)
  - 21511 IsoJect+Creatina VMS: 49.99→54.99 (contenido corregido: estaba $78/$65/$13, quedó total real $88.98 / combo $54.99 / ahorro $34)
  - 21633 ISO100 FP+Creatina Angry: 48.99→58.99 (ahorro $20.99, 26%)
  - 21512 VMS Bios Active+Creatina: 84.99→89.99 (ahorro ~$36, excerpt)
  - 21513 ProLive Bio5+Creatina: 84.99→89.99
  - 21514 ProLive Bio6+Creatina Nutrex: 101.99→98.99 (ahorro $32.99)
  - 21515 Evofusion+BUM Pre Blue: 80.99→85.99 (ahorro ~$54)
  - 21639 ISO100 FP+Raw Pre Orange: 54.99→59.99 (ahorro $34.99, 36%; entidades HTML `m&aacute;s`)
  - 21516 ProLive Bio5+BUM Pre Blue: 84.99→94.99 (ahorro ~$51)
  - 21517 VMS Bios Active+Raw Pre Orange: 89.99→94.99 (ahorro ~$41)
  - 21518 Evofusion+BCAA VMS: 84.99→89.99 (ahorro ~$30, excerpt)
  - 21519 IsoJect+BCAA: 54.99→59.99 (ahorro ~$29)
  - 21520 ProLive Bio6+BCAA: 96.99→98.99 (ahorro ~$38)
  - 21521 IsoJect+Glutamina: 54.99→59.99 (ahorro ~$29)
  - 21522 VMS Bios Active+Glutamina: 84.99→89.99 (ahorro ~$36)
  - 21523 ProLive Bio5+Glutamina: 84.99→89.99 (ahorro ~$36)
  - 21524 Evofusion+Creatina+BCAA: 95.99→98.99 (ahorro ~$51, excerpt)
  - 21525 IsoJect+Raw Pre+Creatina: 64.99→69.99 (ahorro ~$59, excerpt)

### Ajuste extra
- 21518 tenía `_price`/`_regular_price` residuales = 84.99 (precio viejo) que alimentaban tracking (og:price, dataLayer, PixelYourSite). Sincronizados a 89.99 para consistencia (no afecta precio visible ni carrito, ambos usan `_combo_price`).

### Reversión posterior
- **21514 ProLive Bio6 + Creatina Nutrex** se devolvió a su precio anterior **$101.99** por decisión del usuario. Restaurados `_combo_price` (98.99→101.99), `post_content` (ahorro $32.99→$29.99) y `post_excerpt` desde backup original. Verificado en vivo: 101.99 presente, 0 restos de 98.99/32.99.

### Imágenes nuevas de 2 combos ISO 100
- Origen: `C:\suplementos\psk-create-product\fotos\combos\` (archivos con el nombre exacto del producto).
- **21633 ISO 100 FP + Creatina Angry**: adjunto 21955 (nueva `ISO-100-Fruity-Pebbles-Creatina-Angry-Supplements.jpg`), reemplazó 21632.
- **21639 ISO 100 FP + Raw Pre Orange**: adjunto 21956 (nueva `ISO-100-Fruity-Pebbles-Raw-Pre-Workout-Orange.jpg`), reemplazó 21638.
- Datos SEO transferidos de la imagen vieja a la nueva (title `combo-iso100-creatine`/`combo-iso100-rawpre`, alt/caption/desc vacíos), slug restaurado a `combo-iso100-creatine`/`combo-iso100-rawpre`, featured image asignada y adjuntos viejos eliminados. Verificado en vivo: og:image y main img sirven la imagen nueva.

### Verificación
- BD: los 18 `_combo_price` nuevos confirmados vía `eval-file`.
- URLs: 18/18 HTTP 200.
- HTML renderizado: precio nuevo presente en las 18; sin restos del precio viejo (los "old=True" iniciales eran caché vieja, precios de hijos en tabla grouped/schema/dataLayer, o navegación prev/next entre combos).
- 21518: 0 ocurrencias de 84.99 tras el fix; og:price ya muestra 89.99.

### Archivos
- `update_prices.php`, `check_prices.php`, `check_price_meta.php`, `fix_21518_price.php`: temporales locales (`Temp\opencode`) y borrados del servidor (/tmp).
- Backups de contenido/excerpt originales en `Temp\opencode\combo_contents\` y `/home/u1910-kbd9lgn9dh44/combo_contents/` (servidor).

## 2026-08-11 — Creación de 3 combos nuevos (PDF: combos 1, 2 y 4)

### Origen
- PDF `combos-precio-y-nuevos.pdf` proponía 4 combos nuevos. El **combo 3 (Primeval Labs + Pre-Work Raw BUM)** quedó **en pausa** por decisión del usuario (precios distintos entre 9602 RAW-ESSENTIAL $39.99 y 21457 BUM Blue Raspberry $49.99, no son el mismo producto; usuario investiga).

### Productos creados (tipo `grouped`, vía WP-CLI `eval-file`)
| ID | Título | Slug | Combo | Hijos | Categorías | Imagen |
|----|--------|------|-------|-------|------------|--------|
| 21960 | Creatina VMS 80 Serv + Glutamina VMS 80 Serv | `creatina-vms-80-serv-glutamina-vms-80-serv` | $24.99 (suma $59.98, ahorro ~$35) | 21455 simple + 21460 simple | Combos(284), Creatina(258), Glutamina(289), Promociones(219) | adj 21963 |
| 21961 | Mutant Mass Gainer Extreme 2500 + Creatina Angry 60 Serv | `mutant-mass-gainer-extreme-2500-creatina-angry-60-serv` | $44.99 (suma $74.98, ahorro ~$30) | 21391 variable (3 sabores) + 21456 simple | Combos(284), MUTANT(299), Ganadores de Masa/Mass Gainer(249), Creatina(258), Promociones(219) | adj 21964 |
| 21962 | Proteína Primeval Labs 4.8 lb + Creatina Angry 60 Serv | `proteina-primeval-labs-4-8-lb-creatina-angry-60-serv` | $69.99 (suma $84.98, ahorro ~$15) | 18977 variable (CC/Vanilla) + 21456 simple | Combos(284), Proteína de Suero/Whey(246), Proteínas(18), Creatina(258), Promociones(219) | adj 21965 |

- Estructura replicada del combo 21633: `_combo_price`, `_children` (serializado), `_combo_variations_<hijo>` vacío = todas las variaciones disponibles, `_manage_stock=no`, `_stock_status=instock`, `post_content` HTML completo (template combo: sinergia → características → modo de uso → para quién → FAQ → autoridad → advertencia legal → CTA WhatsApp) y `post_excerpt`.
- Contenidos generados por el agente `contenidos-ecommerce` (template `template-descripcion-combo.md`).

### Imágenes (adjuntos nuevos)
- Origen: `C:\suplementos\psk-create-product\fotos\combos\` (PNG con nombre exacto del producto).
- Importadas con `wp media import --porcelain`. **Cuidado**: el primer intento usó `--skip-copy` y dejó las URLs apuntando a `/tmp/...`; se corrigió copiando los archivos a `wp-content/uploads/2026/08/`, re-importando sin `--skip-copy`, actualizando `_wp_attached_file` y regenerando thumbnails. Adjuntos: 21963, 21964, 21965; SEO con title=slug y alt descriptivo.

### Verificación
- BD: los 3 son `grouped`, `publish`, `instock`, `_combo_price` correcto, children correctos.
- URLs: 3/3 HTTP 200.
- HTML renderizado: precio del combo + "Ahorras ~$X" visible en las 3; imagen y og:image apuntan a `uploads/2026/08/`; botones `single_add_to_cart_button`/`combo_qty` y tabla grouped presentes; selectores de variación presentes en 21961 y 21962 (sabores).
- Carrito: add-to-cart vía AJAX de 21960 → carrito muestra "Creatina VMS 80" a $24.99.
- Retiro en sucursal (checklist sección 5): AJAX `sp_save_sucursal` → `{"success":true}`; checkout → `SP_DEBUG selected=1 method=local_pickup` + fila `sp-sucursal-review` presente. No se tocó `functions.php` ni `combo-price.php`.

### Pendiente
- Combo 3 (Primeval Labs + Pre-Work Raw BUM) queda en pausa hasta que el usuario defina el producto de pre-work correcto y su precio.

### Corrección posterior: CTA de WhatsApp
- El CTA inferior de los 3 combos nuevos se generó como **botón verde sólido con texto "Escríbenos por WhatsApp"** (`display:inline-block; background:#25D366; color:#fff; padding:15px 30px; border-radius:50px`), pero los combos existentes usan un **enlace verde con solo el icono de WhatsApp** (`display:inline-flex; align-items:center; gap:8px; color:#25D366; font-size:15px` + SVG 26x26 `fill:#25D366`, sin texto).
- Corrección 1: se cambió a `inline-flex` verde con icono 26x26 (sin texto), pero quedaba como `<p>` suelto y el enlace en otro `<p>`.
- **Corrección final (coincide con los existentes)**: el CTA es un **contenedor gris** (`div style="margin-top:20px; padding:15px; background-color:#f7f7f7; border-radius:10px; text-align:center"`) con un **flex interno** (`display:flex; flex-wrap:wrap; align-items:center; justify-content:center; gap:10px`) que agrupa el texto "Solicita más información sobre..." dentro de un `<span style="color:#333333; text-align:center">` + el enlace verde con icono WhatsApp 26x26. Replicada esa estructura exacta en 21960/21961/21962. Verificado en vivo: 3/3 con contenedor gris + flex + span + enlace verde, HTTP 200.
- **Alineación vertical del texto (desktop)**: el span del texto ahora lleva `display:inline-flex; align-items:center; justify-content:center;` y el texto va en la misma línea que la apertura del span (para evitar el `<br />` que wpautop insertaba por el salto de línea y que empujaba el texto). Con esto el texto queda centrado verticalmente en el taco gris. Verificado en vivo en los 3 combos.
