# Estado del Harness — Fix carrusel home (desktop)

Actualizado: 2026-09-17

## Objetivo
En suplementospanama.net, que los 2 botones "Comprar Ahora" del carrusel superior del home (versión desktop) que daban 404 apunten a `https://suplementospanama.net/promociones/combos/`, sin tocar mobile.

## Criterios de aceptación
| # | Criterio | Estado | Evidencia |
|---|----------|--------|-----------|
| C1 | Desktop: 2 botones → `/promociones/combos/` | ✅ | Carrusel desktop (contenedor `4a92d4`): 2 `href="https://suplementospanama.net/promociones/combos/"` en el HTML servido |
| C2 | Mobile sin cambios | ✅ | Carrusel móvil (`a58a0a`) mantiene sus hrefs; el carrusel editado tiene `hide_mobile` |
| C3 | Sin 404 | ✅ | URLs viejas = 0 ocurrencias en el HTML; `/promociones/combos/` → HTTP 200 |

## Workstreams
| # | Workstream | Responsable | Estado | Entregable |
|---|-----------|-------------|--------|-----------|
| 1 | Editar `_elementor_data` (home 18625) widgets `17c3ee` y `171e63` | `executor` | ✅ hecho | `link.url` → `/promociones/combos/` |
| 2 | Verificación independiente | `supervisor` | ✅ hecho | Evidencia arriba |

## Bitácora
- 2026-09-17 — Recon: home ID 18625; carrusel Elementor `nested-carousel`; botones Primeval `17c3ee`, Nutrex `171e63`, VMS `c51113`.
- 2026-09-17 — `executor`: confirmó `hide_mobile` en contenedor `4a92d4`; backup `/tmp/home_el_backup_20260917-0000.json` (174378 bytes, md5 `99788c41b25fb142b0ddafa94ba73e2d`) + copia en `~/`; editó con `update_metadata`+`wp_slash`; purgó caché.
- 2026-09-17 — `supervisor`: verificación independiente contra HTML servido → C1/C2/C3 ✅.

## Pendiente de decisión del usuario
- El contenedor `4a92d4` tiene `hide_mobile` pero **no** `hide_tablet`: el carrusel desktop también se ve en tablet (768–1024 px). Si se requiere "solo desktop estricto", habría que añadir `hide_tablet` a `4a92d4` (no se hizo por estar fuera de alcance).

## Bloqueos
- Ninguno.
