# AGENTS.md — Suplementos Panamá

Reglas para agentes que trabajen en este repositorio o en el sitio (WooCommerce + child theme `nutritix-child`).

## ⚠️ FUNCIONALIDAD PROTEGIDA: Retiro en sucursal (checkout/carrito)

La funcionalidad de **retiro en sucursal** (selección de sucursal en carrito y checkout, fila "Sucursal" en el review de envío, stock por sucursal, combos con retiro) es **frágil y se ha roto 4 veces**. Está documentada a fondo en `docs/RETIRO-SUCURSAL.md`.

**NO TOQUES** en arreglos de otra índole:
- Las funciones `sp_*` de sucursal/carrito/checkout en `wp-content/themes/nutritix-child/functions.php` (copia local: `local/functions_current.php`).
- El mu-plugin `combo-price.php` (combos grouped).
- El bloque JS `sp_sucursal_js()` (selector de sucursal, fila review, AJAX `sp_save_sucursal`).
- La meta `_sucursales_disponibles` de los productos (la mantiene el sync de stock).

**Si un cambio de otra índole debe tocar `functions.php` del child**: AVISA primero, haz backup en el servidor, y verifica después con el checklist de la sección 5 de `docs/RETIRO-SUCURSAL.md` que siguen OK los 4 flujos (producto, carrito, checkout, order meta).

Errores clásicos que la rompen:
- Añadir `!isPickup ||` al early-return de `spUpdateSucursalInfo()` → borra la fila "Sucursal" en el navegador.
- Reemplazar la delegación de eventos por listener directo → el guardado AJAX se pierde al re-renderizar el carrito.
- Usar el id del combo en vez de los hijos (`sp_get_cart_item_ids`) → desaparece "Recoger en local" para combos.
- Editar `_elementor_data` con `update_post_meta` (usar `update_metadata` + `wp_slash`) → rompe el JSON.

## Comandos/notas de contexto

- Acceso SSH al servidor SiteGround: ver `.ssh-config` y `README.md` (puerto 18765, key `ssh-key-nopass`). La rama de trabajo activa del repo es `feature/retiro-sucursal`.
- El historial completo de sesiones está en `historial-chat.md` (raíz) y `docs/historial-cambios.md`.
- El sync de inventario se hace con el skill `stock-update` (ver `.opencode/skills/stock-update/SKILL.md`).
- API keys: nunca escribir las reales en docs/historial (redactarlas) para no filtrarlas en GitHub.
