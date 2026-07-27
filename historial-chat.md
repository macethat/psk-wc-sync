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
