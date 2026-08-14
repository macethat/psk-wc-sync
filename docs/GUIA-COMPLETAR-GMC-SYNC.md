# Mini-guía: completar Google Merchant Center (GMC) y activar el sync de productos

> Estado detectado el 2026-08-13: Merchant Center está **conectado** (merchant ID `5825178723`, verificado), pero **ningún producto se sincroniza** al feed de Google. La cuenta de Google Ads se creó pero su invite está **sin aceptar**, y el onboarding de GLA quedó a medias.

## Qué falta exactamente

| Ítem | Estado | Dónde se ve |
|---|---|---|
| Merchant Center conectado | ✅ OK (ID `5825178723`, verified/claimed) | GLA en wp-admin |
| Site verification meta | ✅ Presente (`INv26I0qwEqDUhHG74QuIeiH1zBX1mv_ZYaWGnQx9QE`) | Home HTML |
| Sync de productos → GMC | ❌ **No hay** (0 de 164 productos tienen meta `_wc_gla_*`) | BD |
| Cuenta Google Ads | ❌ Invite **sin aceptar** (`account_access=-1`) | GLA opciones |
| Link Ads ↔ Merchant | ❌ `PERMISSION_DENIED` | GLA opciones |

## Opción A (RECOMENDADA) — Solo Merchant Center, sin Ads ni método de pago

**El feed de Merchant Center (compras gratuitas / free listings) funciona sin cuenta de Google Ads y sin tarjeta.** El método de pago solo es necesario si quieres campañas pagadas.

1. Ve a `wp-admin → Marketing → Google Listings & Ads`.
2. Completa el wizard de configuración hasta el final:
   - Merchant Center: seleccionar la cuenta `5825178723` existente.
   - País de venta: `PA` (ya configurado).
   - Envíos: mantener `automatic` (ya configurado).
   - En el paso de **Google Ads**: elegir **"Omitir por ahora" / "Skip"** (o desvincular la subcuenta creada `8418809792`). GLA permite continuar solo con Merchant Center.
3. En la pestaña **Product feed** (o "Productos"), verifica que el interruptor de sincronización esté **ON** y pulsa **"Sync now" / "Sincronizar"** si existe.
4. GLA dispara los jobs `gla/jobs/sync_products` vía ActionScheduler. La primera sincronización de 164 productos puede tardar minutos.
5. El estado `account_access=-1` deja de importar: ese paso es de Ads y no bloquea el feed de Merchant Center.

> Nota: si el wizard no te deja saltar el paso de Ads, ver "Paso 1" de la Opción B (aceptar el invite sin cobro) para desbloquear y luego continuar solo con MC.

## Opción B — Aceptar el invite de Google Ads (sin pagar nada aún)

El **aceptar la invitación no cobra nada ni requiere registrar una tarjeta de inmediato**; el billing solo se pide cuando lanzas una campaña. Esto desbloquea el onboarding si la opción "Skip" no aparece.

1. Entra al servidor y obtén el enlace del invite (única opción que tiene el token):
   ```bash
   wp option get gla_ads_billing_url
   ```
   Devuelve una URL del tipo `https://ads.google.com/nav/startacceptinvite?ivid=...&ocid=8453601065&eivid=...`
2. Abre esa URL con el **mismo correo Google** que administra el Merchant Center.
3. Acepta la invitación de la cuenta de Ads (sub-account `8418809792`). **No se cobra nada**; si te piden datos de pago, puedes dejarlo en pausa (se configura solo cuando se crea una campaña).
4. Si el correo del invite no es el correcto, reenviarlo desde el dashboard de GLA.

Verificación: tras aceptar, en GLA la opción `gla_ads_account_state` debe mostrar `account_access.status = 1`.

## Paso 3 — Completar el onboarding de GLA y disparar el sync (si elegiste la Opción B)

1. Ve a `wp-admin → Marketing → Google Listings & Ads`.
2. Si el wizard de configuración reaparece, complétalo hasta el final:
   - Merchant Center: seleccionar la cuenta `5825178723` existente.
   - País de venta: `PA` (ya configurado).
   - Envíos: mantener `automatic` (ya configurado).
   - **Google Ads**: ya aceptado tras el paso 1 — puedes dejar la subcuenta vinculada o volver a elegir "Omitir" si no quieres Ads.
3. En la pestaña **Product feed** (o "Productos"), verifica que el interruptor de sincronización esté **ON** y pulsa **"Sync now" / "Sincronizar"** si existe.
4. GLA dispara los jobs `gla/jobs/sync_products` vía ActionScheduler. La primera sincronización de 164 productos puede tardar minutos.

## Verificación — Verificar que el sync funcionó

Re-chequear 24–48 h después (o cuando el client lo reporte). Comandos vía SSH:

```bash
# 1) Productos con google_ids (debe ser > 0)
wp db query "SELECT meta_key, COUNT(*) FROM cid_postmeta WHERE meta_key LIKE '%wc_gla%' GROUP BY meta_key;"

# 2) Jobs de sync en ActionScheduler (deben aparecer sync_products / update_products)
wp db query "SELECT hook, status, COUNT(*) FROM cid_actionscheduler_actions WHERE hook LIKE '%gla/jobs/%' GROUP BY hook, status ORDER BY 3 DESC LIMIT 15;"

# 3) Estado de la cuenta Ads (account_access debe ser 1)
wp option get gla_ads_account_state
```

Verificación manual en Google:
- Entra a `merchant.google.com` con la misma cuenta → **Productos → Todos los productos**: debe listar los 164.
- En el front-end Firecrawl ya no debería marcar Merchant Center como pendiente; el "no visible en código" seguirá apareciendo (es normal, el sync es vía API, no hay feed XML público).

## Notas

- El endpoint `https://suplementospanama.net/wp-json/wc/gla/v1/product-feed` da **404 intencional**: GLA 3.x no expone feed público; el 404 no es un error.
- **GMB API**: Firecrawl lo marcará siempre porque no existe "GMB API en el código" — el sitio ya tiene el schema `HealthAndBeautyBusiness` por sucursal con `sameAs` a Google Maps (verificado 2026-08-13). No requiere acción.
- Si tras aceptar el invite el sync sigue sin correr, comprobar que no haya bloqueos de SG Optimizer cache y consultar el log: `wp-content/uploads/woocommerce-logs/google-listings-and-ads-*.log`.
