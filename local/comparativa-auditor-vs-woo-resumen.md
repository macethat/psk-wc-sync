# Comparativa: Inventario auditor externo (PDF) vs WooCommerce

Fecha: 6 de octubre de 2026
Entrada: `listado-productos-suplementospanama.pdf` (auditor externo, revisado 6-oct-2026 sobre /shop/, 157 productos)
Base de comparación: export de WooCommerce del 6-oct-2026 (171 productos publicados)
Salida: `local/comparativa-auditor-vs-woo.csv`

---

## 1. Estado de la sincronización PSKloud (verificado antes de comparar)

- Cron diario **02:00** → `psk-sync/run_sync.sh` → `daily_stock_update.py --live --update-prices` + auditoría de combos.
- Última corrida (6-oct 02:00): **320 updates, 0 fallidos**, verificación OK, push a GitHub (`9fa17a2..e80d13c`).
- Auditoría de combos: "OK — ninguno de los 27 combos tiene cache `_price` desfasado".
- **Conclusión: la sincronización PSKloud funciona correctamente.**

---

## 2. Resultado de la comparación

| Métrica | Resultado |
|---|---|
| Productos en la lista del auditor | 157 |
| Encontrados y verificados en Woo | **157 (100%)** |
| Diferencias de **precio** | **0** |
| Diferencias de **estado** (disponible/agotado) | **0** |
| Productos publicados en Woo **no** listados por el auditor | 14 |

**Conclusión:** la web que auditó el cliente **coincide con la base de datos de WooCommerce** (precios y disponibilidad). No hay desfases de sincronización.

Los **14 productos de Woo que el auditor no vio** están **todos agotados** y se **ocultan de la tienda** por el mu-plugin `sp-ocultar-agotados.php` (comportamiento esperado). Aparecen igual en el sitemap de Rank Math → pueden seguir indexados aunque no se listen en /shop/.

---

## 3. Cómo se manejaron los combos (nota)

Los **27 combos** (productos `grouped`) **no existen en PSKloud**, por lo que **no reciben actualización de stock/precio** del sync y usan campos propios:
- Precio: meta `_combo_price` (no `_regular_price`/`_price`).
- Stock: derivado de los productos hijos.
- Campos adicionales: `_combo_hero_image_id`, textos de ahorro, etc.

En la comparativa se usó `_combo_price` para los combos → **coinciden con el precio de la web**. La sincronización de stock de los hijos sí viene de PSKloud; el armado del combo es independiente.

---

## 4. Hallazgos de calidad de datos (del auditor, verificados en la BD)

1. **Combos IsoJect agotados siguen en el carrusel del home.** Los 4 combos `IsoJect Vanilla + …` están `outofstock` (porque IsoJect Vanilla 1.85 lb está agotado) pero se muestran en "Combos Online". Causa: `home-nuevo.php` arma el carrusel con una **lista fija de IDs** (`$sp_hero_ids`) filtrada solo por `post_status === 'publish'`, **sin filtrar por stock**. → Recomendación: filtrar combos sin stock del carrusel (o marcarlos "agotado").
2. **C4 Ultimate Energy Carb WWP – 16 oz "Pack de 12"** (ID 20430): el nombre dice *Pack de 12* pero cada variación vale **$3.80** (los otros C4 de 12 unidades valen **$45.60**). → **Precio mal cargado** (o producto mal nombrado). Revisar.
3. **URLs en el sitemap que no están en la tienda:** `tribulus-1400-nutrex` (9546), `raw-test` (10709), `raptor-x5-impulse` (12945), `coq10-nutricost` (13373), `vitamina-b12-nutricost` (13387). Son productos **publicados pero agotados** (ocultos de /shop/). Decidir si se excluyen del sitemap o se dejan.

---

## 5. Detalle en el CSV

`local/comparativa-auditor-vs-woo.csv` (delimitado por comas, UTF-8 con BOM):
- 157 filas = cada producto del auditor con su match en Woo (ID, SKU, nombre, tipo, status, precios, stock, categorías) y columna **Discrepancias** (todas "OK").
- 14 filas finales con `Categoria = EN WOO NO EN AUDITOR` (productos agotados ocultos).
