# Historial de Sesiones de Chat

Este archivo registra las sesiones de trabajo con el asistente para preservar contexto entre chats.

---

## 2026-07-11

### Tema
Configuración del repositorio de historial de chat y solución responsive en template de combos.

### Decisiones
- Se crea `docs/historial-chat.md` para registrar sesiones de trabajo
- Se crea `guardar-sesion.ps1` para facilitar el commit/push del historial
- El historial se almacena en el repo `psk-wc-sync` en GitHub

### Plataformas conectadas en el proyecto
1. **PSK Cloud (Premium Soft)** — API REST (inventario, clientes, pedidos)
2. **WooCommerce** — API REST + WP-CLI vía SiteGround SSH
3. **SiteGround** — Servidor SSH para WP-CLI
4. **GitHub** (`macethat/psk-wc-sync`) — Repositorio de código
5. **MyMemory API** — Traducción EN→ES
6. **Apify** — Web scraping
7. **WhatsApp Business** — Enlace wa.me para contacto

### Cambios realizados en grouped.php
- **Problema**: En mobile, los productos hijos del combo solo mostraban nombre + precio, sin imagen
- **Solución**: 
  - Se agregó `get_image(70,70)` clickable dentro del `label` cell
  - Se agregó `.combo-mobile-row` con flex: imagen (70px) a la izquierda + precio a la derecha
  - CSS responsive: ≤768px la tabla se vuelve block, `td.price` se oculta, se muestra la nueva fila
  - ≥769px la fila mobile permanece oculta, desktop intacto
- **Archivo**: `grouped.php` (override en child theme `nutritix-child`)
- **Template del combo**: productos agrupados tipo `grouped` con precio fijo, bypass de stock, variaciones restringidas

### Estado actual
- Script `generar_diferencias.py` tiene error `KeyError: 'Codigo'` por merge mal configurado (pendiente)
- Template `grouped.php` modificado con estructura responsive mobile completada
- Sistema de combos funcional con `combo-price.php` (MU plugin)

### Solución responsive productos relacionados (functions.php child theme)
- **Problema**: Los productos relacionados (`section.related.products`) adoptaban el layout horizontal del theme (imagen 35% + precio 65%) en mobile, quedando muy apretados. No era causado por cambios en `grouped.php` sino por CSS preexistente en `nutritix-child/functions.php`
- **Solución**: Se agregó `@media (max-width: 500px)` dentro del bloque `@media (max-width: 767px)` que:
  - Cambia `flex-direction: column` para apilar verticalmente
  - Setea image y precio al 100% de ancho
  - Mantiene el layout horizontal en pantallas > 500px
- **Archivo**: `wp-content/themes/nutritix-child/functions.php`
- **Backup**: `functions.php.bak`

### Corrección de precios en producto Elite Performance Stack
- **Problema**: Precio del combo cambiado a $101.99, pero descripción larga y excerpt tenían valores viejos ($68.50, $118.47, 63%)
- **Solución**: Se restauró descripción desde revisión 21662 y se reemplazaron valores: $118.47→$84.98, $68.50→$101.99, 63%→45%
- **Vía**: Script PHP vía SSH + WP-CLI

### Cambios realizados en grouped.php
- **Solución final**: Se eliminó todo el CSS mobile que rompía la tabla. Solo se agregó `get_image(50,50)` inline con el nombre del producto dentro del label cell
- **Archivo**: `grouped.php` (child theme `nutritix-child`)
- El combo mantiene estructura de tabla original, la imagen aparece inline con el nombre

## 2026-07-13

### Tema
Conexión Google Search Console API y script de consulta de datos.

### Decisiones
- Se usa el proyecto existente "suplementos-panama" en Google Cloud Console
- Se creó credencial OAuth 2.0 tipo "Aplicación de escritorio" para GSC API
- Se agregó `suplementospanamacrm@gmail.com` como usuario de prueba
- Se creó `gsc_query.py` como script principal de consulta
- Se agregaron `credentials.json` y `token.json` a `.gitignore`

### Plataformas conectadas en el proyecto (actualizado)
8. **Google Search Console API** — OAuth 2.0, credenciales de escritorio

### Archivos creados/modificados
- `gsc_query.py`: Script Python que consulta GSC (queries, clicks, impresiones, CTR, posición)
- `.gitignore`: Se agregaron `credentials.json` y `token.json`

### Verificación
- Script funcional: consulta última semana (2026-07-06 a 2026-07-13)
- Top query: "suplementos panama" (51 clicks, 147 impresiones, posición 1.6)

### Inspección de combos en GSC
- Se encontraron **24 productos grouped** (combos) via WP-CLI + SSH
- Se inspeccionaron las URLs una por una en GSC via `urlInspection.index.inspect`
- **2 indexados** (`isoject-vanilla-creatina-vms` y `creatina-evogen-60-serv-beta-alanina-raw`)
- **22 no indexados** (creados 10-11 julio, Google no los ha descubierto aún)
- El `product-sitemap.xml` fue enviado a Google
- Se corrigió error: URLs de productos usan `/product/` no `/producto/`
- Archivos creados: `gsc_inspect_combos.py`, `combos_inspection.json`, `gsc_fetch_pages.py`, `gsc_analyze_pages.py`, `gsc_pages.json`

### Archivos creados/modificados
- `gsc_query.py`: Agregadas funciones de sitemaps, URL inspection, menú interactivo, modo CLI
- `gsc_inspect_combos.py`: Inspección batch de los 24 combos en GSC
- `gsc_fetch_pages.py`: Descarga lista de páginas indexadas desde Search Analytics
- `gsc_analyze_pages.py`: Análisis de las páginas obtenidas
- `local/get_combos.py`: Script para extraer combos del export de WooCommerce

### Plataformas conectadas en el proyecto (actualizado)
8. **Google Search Console API** — OAuth 2.0, credenciales de escritorio (analytics, sitemaps, URL inspection)
9. **SiteGround SSH** — WP-CLI para WooCommerce

### Asignación de categorías a combos desde sus componentes
- **Problema**: Los 24 combos solo tenían categorías "Combos" y "Promociones", sin las categorías de sus productos componentes
- **Solución**: Script PHP via SSH+WP-CLI que para cada grouped product:
  1. Obtiene los productos hijos (_children)
  2. Obtiene las categorías de cada hijo
  3. Asigna al combo las categorías existentes + las de los hijos sin duplicar
- **Archivos**: `local/assign_combo_cats.php` (subido temporalmente al servidor, luego eliminado)
- **Resultado**: Todos los combos ahora tienen las categorías correctas (ej: Creatina, Proteína de Suero / Whey Protein, BCAA, etc. según los componentes)
- Uno de los 24 combos (ID 21088) no fue procesado por el PHP, se corrigió manualmente via `wp post term set`

### Estado actual
- ✅ Productos del combo: imagen visible en mobile y desktop
- ✅ Productos relacionados: layout vertical en ≤500px, horizontal >500px
- ✅ Precios del Elite Performance Stack corregidos
- ✅ Google Search Console API conectada y funcional
- ✅ 24 combos identificados, 2 indexados, 22 pendientes
- ✅ Categorías de componentes asignadas a los 24 combos
- ⏳ Esperar a que Google procese el sitemap para indexar los 22 combos restantes
- Pendiente: corregir `generar_diferencias.py`
