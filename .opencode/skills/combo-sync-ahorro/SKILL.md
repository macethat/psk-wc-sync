name: combo-sync-ahorro
description: Detecta cambios de precio en los productos hijo que conforman combos (grouped) y actualiza el ahorro + los textos de las fichas (contenido y excerpt) manteniendo SIEMPRE el _combo_price fijo. Activar cuando suban/bajen precios de productos, tras el cron diario de stock/precios, o para auditar combos. No altera el precio del combo.

---

## Regla de oro (NO VIOLAR)

- El `_combo_price` de cada combo **NO se modifica nunca** en este proceso. Solo cambia cuando el cliente lo pide explícitamente.
- Al subir/bajar el precio de un producto hijo, el **ahorro** (retail actual − _combo_price) cambia automáticamente en el badge (combo-price.php usa `get_price()` de los hijos) y hay que **sincronizar los textos** de la ficha (post_content + post_excerpt) con el retail y ahorro nuevos.

## Contexto / acceso

- Servidor: `ssh.suplementospanama.net:18765`, user `u1910-kbd9lgn9dh44`, llave `C:\suplementos\stock-suplementos\ssh-key-nopass`
- WP-CLI en: `cd ~/www/suplementospanama.net/public_html`
- Copia local del repo: `C:\suplementos\stock-suplementos` (rama `feature/retiro-sucursal`)
- Los combos grouped tienen meta `_combo_price`; los hijos se guardan en meta `_children` (no en post_parent).

## Bug conocido del cron (IMPORTANTE)

- El script del servidor (`~/www/.../psk-sync/daily_stock_update.py`) actualiza `_regular_price` con `post meta update` directo, lo que **NO recalcula el caché `_price`** de WooCommerce (el que usa `get_price()`).
- Resultado: `_regular_price` puede ser nuevo pero `_price` viejo → el front y el ahorro del combo muestran el precio viejo.
- **Siempre** al detectar cambio de precio, verificar/corregir `_price` de la variación y del padre (ver paso 2).

## Proceso

### 1. Auditoría: retail real vs cifras de las fichas

Ejecutar en el servidor (crear el script PHP en /tmp y correr con `wp eval-file`):

```php
// analizar_todos_combos.php — listar retail/ahorro real por combo
// (incluido en el historial de la sesion 2026-08-31)
```

Objetivo: identificar combos donde la ficha declara cifras (retail "por separado"/"suman"/"individual", ahorro) que difieren > $0.50 del retail real (`get_price()` de hijos).

### 2. Corregir caché `_price` si hay desfase

- Variaciones sin `_sale_price`: `UPDATE cid_postmeta SET meta_value=_regular_price WHERE meta_key='_price'` (JOIN por post_id, solo donde `_price != _regular_price` y sin `_sale_price`).
- Padres variables: `_price` del padre = MIN(`_price` de sus variaciones).
- **NO tocar** variaciones con `_sale_price` (ahí `_price` = precio de oferta, correcto).
- Backup previo de los valores en `/tmp/`.

### 3. Actualizar textos de la ficha

- Calcular: `retail = SUM(get_price(hijos))`, `ahorro = retail − _combo_price` (redondear 2 decimales).
- Reemplazar en `post_content` y `post_excerpt`:
  - Retail declarado (frases "por separado", "suman", "regular individual", "$XX.XX") → retail real.
  - Ahorro declarado ("Ahorra ~$X", "ahorras aproximadamente $X", "$X de ahorro") → ahorro real.
  - Precios individuales de hijos si se mencionan explícitamente (ej. "X cuesta $95.99") → precio nuevo del hijo.
- Usar `wp_update_post` con backup previo (JSON en /tmp). Verificar después que no quedan cifras viejas.

### 4. Verificación

- `wp eval-file` con script que compara retail/ahorro real vs cifras declaradas en los 27 combos → 0 desfases.
- Front: `curl` a la URL de cada combo editado → cifras nuevas presentes, viejas ausentes.
- Checklist retiro en sucursal: `wp eval-file test_checkout.php` (local/test_checkout.php) → `SP_DEBUG selected=1 method=local_pickup` + fila review + wrap preseleccionado. (NO tocar funciones sp_*).

### 5. Registro y commit

- Actualizar `historial-chat.md` y `docs/historial-cambios.md`.
- Commit en rama `feature/retiro-sucursal`.

## Automatización en el cron diario (2026-08-31)

- `run_sync.sh` (cron de SiteGround, 02:00) ejecuta después del sync: `wp eval-file psk-sync/sp_auditar_combos.php -- fix >> cron.log`.
- El paso `fix` corrige **automáticamente** el caché `_price` desfasado (variaciones sin sale + padres variables). NO toca `_combo_price` ni los textos de las fichas.
- El reporte en `cron.log` marca qué combos tienen **textos desactualizados** (retail/ahorro en ficha ≠ real). Esos requieren la intervención del skill (edición de `post_content`/`post_excerpt`) y quedan pendientes de revisión.
- Scripts: `psk-sync/sp_auditar_combos.php` (servidor) y copia local `local/sp_auditar_combos.php`; `run_sync.sh` con backup `run_sync.sh.bak-20260831`.

### Cuando correr el skill manualmente

- Tras cada cron diario, revisar el final de `psk-sync/cron.log` (sección "AUDITORIA DE COMBOS"). Si marca combos con cifras desactualizadas, aplicar pasos 1-5.
- O ante cualquier cambio de precio de un producto (aunque no esté en el cron).

## Historial de combos corregidos (referencia)

## Historial de combos corregidos (referencia)

- 2026-08-31: 21512, 21513, 21514, 21516, 21517, 21520, 21522, 21523, 21643, 21647 (subida IMPULSE/VMS: ahorros +$4~$9) y 21525, 21660, 21524, 21515 (RAW Pre Orange +$8, Bio6 +$9).
- Los 27 combos quedaron auditados y sincronizados a esa fecha. El cron desde entonces corrige el caché `_price` automáticamente y avisa en `cron.log` cuando una ficha requiere actualización de texto.
