## Goal
- Sincronizar inventario, clientes y pedidos entre PSK Cloud (Premium Soft) y WooCommerce. Actualización de stock corre directamente en SiteGround vía cron. Herramienta `psk-create-product.py` para crear productos faltantes en WC con scraping de marca + traducción EN→ES (MyMemory ~60%). Sub-agente `marketing-descriptions` para descripciones SEO completas en español.

## Constraints & Preferences
- Carpeta `C:\suplementos\psk-create-product\` para el script de creación de productos; fotos en `fotos/<SKU>.jpg`.
- Productos nuevos se crean en estado **Borrador (draft)** para agregar descripciones después.
- Precio: se muestra el de PSK (#3 DETAL) pero el usuario puede sobrescribirlo.
- Descripciones: scrapea el sitio web oficial de la marca en inglés, traduce a español vía MyMemory API (~60% efectividad por límite 500 bytes/request).
- Si es variación de producto existente: solo datos + foto, **sin** descripciones.
- Si es producto variable nuevo: crea padre + todas las variaciones con sus datos y fotos.
- Regla `post term set` usa **slug** (no term_id numérico) para asignar marca/categoría.
- SSH sin `shell=True`, argumentos como lista para evitar problemas de quoting en Windows.
- `stdin=subprocess.DEVNULL` en SSH para que no consuma el stdin del script interactivo.
- User-Agent Chrome real para evitar 403 en sitios como evogennutrition.com.
- El sub-agente `marketing-descriptions` (opencode) completa/traduce y optimiza descripciones con SEO + GEO local.

## Progress
### Done
- Analizar matching inicial: 298 coincidencias, 386 solo PSK, 0 solo WC.
- Construir `daily_stock_update.py` con extracción SSH+WP-CLI y PSK Cloud API.
- Crear skill OpenCode, generar llaves SSH separadas (SiteGround + GitHub).
- Resolver bloqueo SG-CAPTCHA (IP `195.242.214.38`).
- Corregir bug de status (compara status real, no solo umbral).
- Agregar flags `--api`, `--fecha`, auto-commit GitHub tras `--live` exitoso.
- Renombrar repo a `psk-wc-sync`, crear README.md y Wiki (4 páginas).
- Explorar y documentar endpoints API PSK (Articulos, Existencias, Almacenes, Agencias, etc.).
- Confirmar filtros Clientes por `doc_identificacion` y `email`.
- Confirmar `id_tipo_moneda=23`, `id_tipo_documento=2` (Pedido).
- Descubrir formato ProcesarDoc: `operti` (cabecera) + `opermv` (líneas).
- Probar ProcesarDoc exitosamente: respuesta `{"status":2,"msj":"Documento Procesado","id_operti":"21642","consecutivo":"0000000015"}`.
- Crear `sync_orders.py` (pedidos + clientes).
- Crear plugin `psk-wc-checkout-field.php` (Cédula/Pasaporte/RUC en checkout).
- Configurar y luego **desactivar** Task Scheduler local (reemplazado por cron en SiteGround).
- Migrar `daily_stock_update.py` a SiteGround (`psk-sync/`), sin SSH.
- Configurar cron Site Tools: `0 21 * * * /bin/bash /home/customer/www/.../psk-sync/run_sync.sh`.
- Aplicar precios manualmente vía WP-CLI en SiteGround: 4 Gold Standard Whey, 15 Amino Energy, ISOJECT.
- Verificar que 3 de 4 SKU faltantes ya existen en WC: `748927026825` (ID 299), `748927068238` (ID 11841), `748927060522` (ID 11842).
- Crear `psk-create-product.py` con funcionalidades: scraping web, traducción EN→ES (MyMemory chunked), precio override, creación en Borrador con marca/categoría vía slug, fotos vía SCP+WP-CLI, soporte variaciones y variables.
- Mejorar traducción: byte-safe chunking (≤480 bytes), Gmail email en `de` para 50k chars/día, retry + 3s delay.
- **Crear ISOJECT Vainilla (SKU 817189020510)**: ID 21335, $58.99, stock 24, Proteína Aislada (ID 247), EVOGEN, foto OK. Estado: Borrador.
- Crear sub-agente de marketing en `.opencode/agents/marketing-descriptions.md` con prompt SEO + GEO local + estructura HTML.

### In Progress
- (ninguno)

### Blocked
- Pedidos/clientes (`sync_orders.py`) pausados hasta que haya productos nuevos y pedidos reales en la tienda.

## Key Decisions
- Precio: se muestra el de PSK (#3 DETAL) pero el usuario puede sobrescribirlo con `input()`.
- Descripciones: scrapear sitio oficial inglés → traducir parcialmente con MyMemory (~60%) → agente marketing completa/traduce/optimiza.
- `post term set` usa **slug** (no term_id) porque WP-CLI interpreta el ID numérico como nombre de nuevo término.
- SSH ejecutado como lista de argumentos (sin `shell=True`) para compatibilidad Windows.
- `stdin=subprocess.DEVNULL` en SSH para que no consuma stdin del pipe.
- User-Agent Chrome real para evadir bloqueos 403.
- Traducción chunked con límite 480 bytes/chunk (MyMemory free: 500 bytes/request, 50k chars/día con email `de`).
- MyMemory no es 100% confiable; el agente marketing completa lo que falte.

## Next Steps
1. Invocar `@marketing-descriptions` para mejorar las descripciones del ISOJECT (ID 21335) y publicarlo.
2. Agregar más URLs al diccionario `KNOWN_PRODUCT_URLS` para otras marcas.
3. Cuando haya pedidos reales, activar `sync_orders.py`.

## Critical Context
- Bloqueo SG-CAPTCHA impide API REST WooCommerce; se trabaja vía SSH+WP-CLI. IP a whitelistear: `195.242.214.38`.
- En SiteGround los comandos `wp` corren directo (sin SSH). WP-CLI 2.12.0, PHP 8.2.31, Python 3.14.5.
- SSH SiteGround: `u1910-kbd9lgn9dh44@ssh.suplementospanama.net:18765`, llave `ssh-key-nopass`.
- API PSK Cloud: `adm.premium-soft.com/Api/`, PIN `46558`, clave `BQxQrt5/FwARtlVUwT0GFw==`.
- MyMemory API free: 500 bytes/request, 50000 chars/día con email `de`.
- EVOGEN bloquea scraping con User-Agent genérico (403); funciona con Chrome UA.
- 684 artículos en PSK, ~435 productos en WC (ahora con ISOJECT ID 21335).
- Marcos conocidos ya en WC: `748927026825` (ID 299), `748927068238` (ID 11841), `748927060522` (ID 11842). Solo restaba `817189020510` → creado.
- Marcas WC: taxonomía `product_brand`, ~29 marcas. Categorías: jerarquía con IDs.
- Corrida cron diaria 9 PM en SiteGround; OPENBLAS_NUM_THREADS=1 para numpy/pandas.

## Relevant Files
- `C:\suplementos\psk-create-product\psk-create-product.py`: script principal de creación de productos con scraping + traducción.
- `C:\suplementos\psk-create-product\fotos\`: fotos de productos (`<SKU>.jpg`).
- `C:\suplementos\psk-create-product\ssh-key-nopass`: llave SSH SiteGround.
- `C:\suplementos\stock-suplementos\.opencode\agents\marketing-descriptions.md`: sub-agente marketing para descripciones SEO.
- `~/www/suplementospanama.net/psk-sync/daily_stock_update.py`: versión activa en SiteGround (sin SSH).
- `~/www/suplementospanama.net/psk-sync/run_sync.sh`: wrapper cron.
- `C:\suplementos\stock-suplementos\local\sync_orders.py`: script pedidos + clientes (archivado).
- `C:\suplementos\stock-suplementos\psk-wc-checkout-field.php`: plugin campo Cédula en checkout.
- `C:\suplementos\stock-suplementos\payload_procesardoc.json`: JSON ejemplo funcional de ProcesarDoc.
- `C:\suplementos\stock-suplementos\almacenes_vs_agencias.csv`: mapa almacenes vs agencias.
- `https://github.com/macethat/psk-wc-sync`: repositorio GitHub (rama `master`).
