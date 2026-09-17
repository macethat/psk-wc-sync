# Playbook — Meta Pixel/Dataset + Conversions API + Whaticket (Suplementos Panamá)

> **Uso:** entrega este documento a Claude (o al agente con acceso a la cuenta de Meta de Suplementos Panamá) como instrucción maestra. Claude **no genera el pixel**, pero sí puede guiar/ejecutar la configuración vía Business Manager / Events Manager / API y preparar la integración con el CRM Whaticket.

---

## 0. Propósito

Implementar la medición de extremo a extremo del embudo de captación de Suplementos Panamá:

**Meta Ads → WhatsApp (Click-to-WhatsApp) / Web → Whaticket (CRM) → conversión**

El objetivo es que **toda la información capturada por los vendedores en Whaticket** (lead, calificación, cierre/venta, valor) vuelva a Meta como eventos de conversión de calidad, para que el algoritmo optimice por **leads que convierten**, no solo por clics o mensajes.

Resultado esperado:
- Un **Dataset** (Pixel + Conversions API) único y bien configurado.
- **Eventos estándar y personalizados** que reflejan cada etapa del CRM.
- **Atribución correcta** (incluido `ctwa_clid` de Click-to-WhatsApp).
- **KPIs y conversiones personalizadas** en Meta que alimenten la optimización (valor/ROAS).

---

## 1. Rol y reglas para Claude

Claude debe actuar como **implementador de medición de Meta**, con estas reglas:

1. **Inventariar antes de crear/editar.** No asumir IDs ni configuraciones existentes.
2. **No inventar datos.** Todo ID (Business, Ad Account, Dataset, WABA, dominio) debe obtenerse de Meta.
3. **Confirmar accesos** antes de ejecutar; si falta un permiso, pedirlo y detenerse.
4. **No exponer PII en claro.** Todo dato personal (email, teléfono, nombre) va **hasheado SHA-256**.
5. **Respetar consentimiento y políticas** de datos de Meta y de privacidad del usuario.
6. **Documentar cada paso** con evidencia: ID, captura, respuesta de API.
7. **No romper producción.** Cambios en el sitio/CRM se hacen con respaldo y en ventana controlada.
8. **Un cambio a la vez**, validando con **Test Events** antes de dar por bueno.

---

## 2. Arquitectura objetivo

```
 Meta Ads (FB/IG)
   │  clic a WhatsApp (CTWA)              │  clic a Web (fbclid)
   ▼                                      ▼
 WhatsApp (WABA)                       Sitio web
   │  referral.ctwa_clid                  │  Pixel (navegador) + fbc/fbp
   ▼                                      ▼
 Whaticket (CRM)  ──►  Servicio de eventos (CAPI)  ──►  Dataset Meta
   │  etapas del pipeline (lead → cierre)                     │
   ▼                                                          ▼
 Vendedores / conversión real            Optimización de campañas (valor/ROAS)
```

**Fuentes de datos a conectar al Dataset:**
| Fuente | Medio | Qué aporta |
|--------|-------|------------|
| Pixel web | Navegador | PageView, ViewContent, AddToCart, InitiateCheckout, Purchase |
| Conversions API (web) | Servidor | Refuerzo + eventos que el navegador no ve |
| Business Messaging (WhatsApp) | Servidor | Eventos CTWA con `ctwa_clid` |
| Offline / CRM (Whaticket) | Servidor | Lead, Lead calificado, Cierre/Venta con valor |

---

## 3. Fase 0 — Inventario y accesos (obligatorio)

Claude debe completar y devolver esta tabla **antes** de tocar nada:

| Elemento | Valor / ID | Dónde se obtiene | Estado |
|---|---|---|---|
| Business Manager (Portafolio) | | business.facebook.com | |
| Ad Account | | Business Settings → Cuentas de anuncios | |
| Página de Facebook / IG | | Business Settings | |
| Dataset (Pixel) existente | | Events Manager → Orígenes de datos | |
| WABA (WhatsApp Business Account) | | Business Settings → WhatsApp | |
| Número de WhatsApp | | WABA | |
| Dominio del sitio | suplementospanama.net | Business Settings → Dominios | |
| Whaticket: versión | | panel/admin de Whaticket | |
| Whaticket: tipo de conexión WhatsApp | Baileys (no oficial) / WABA Cloud API | Whaticket | |
| Whaticket: API/Webhooks disponibles | | docs/instalación | |
| Sitio: ¿checkout en web o solo WhatsApp? | | suplementospanama.net | |
| GTM / gestor de etiquetas | | GTM-PR4ZSMC7 (detectado) | |

> **Punto crítico:** si Whaticket usa **WABA (Cloud API)**, se puede capturar `ctwa_clid` y atribuir conversiones de WhatsApp correctamente. Si usa **Baileys (API no oficial)**, `ctwa_clid` **no** está disponible y habrá que usar el parámetro `ref`/`source_id` del enlace + reglas de atribución alternativas. Claude debe determinar esto primero.

---

## 4. Fase 1 — Crear/verificar el Dataset (Pixel)

1. En **Events Manager**, verificar si ya existe un Dataset (Pixel) para SP.
   - Si **existe**: usarlo. Anotar su **Dataset ID / Pixel ID**.
   - Si **no existe**: crear uno (`Crear origen de datos → Pixel`). Nombrar `SP - Web + CRM`.
2. Activar **Conversions API** dentro del mismo Dataset (Events Manager → Configuración → Conversions API → Crear token de acceso).
3. Guardar el **token de acceso de CAPI** de forma segura (no en repos públicos).
4. Instalar el pixel base en el sitio (vía GTM o código) si hay tráfico web con acciones relevantes.
5. **Verificar el dominio** (Business Settings → Marcas/Seguridad de la marca → Dominios) con el método DNS o meta-tag.

---

## 5. Fase 2 — Conversions API (CAPI)

1. Definir el **método de envío**:
   - **Directo** (recomendado si Whaticket puede hacer llamadas HTTP): POST a `https://graph.facebook.com/v<XX>.0/<DATASET_ID>/events?access_token=<TOKEN>`.
   - **Gateway** (si no hay servidor propio): Conversions API Gateway alojado por Meta.
2. Configurar **parámetros comunes** en todos los eventos:
   - `event_name`, `event_time` (Unix, UTC), `event_id` (único, para deduplicar), `action_source`.
   - `user_data`: `em`, `ph`, `fn`, `ln`, `ct`, `st`, `zp`, `country`, `external_id`, `fbp`, `fbc`, `client_ip_address`, `client_user_agent` (todo PII hasheado con SHA-256, salvo `fbp`/`fbc`/IP/UA).
   - `custom_data`: `value`, `currency` (USD), `content_ids`, `contents`, `content_type`, `order_id`.
3. Activar **deduplicación**: el mismo `event_name` + `event_id` enviado por Pixel y CAPI se une (ventana 48 h).
4. Para WhatsApp: usar `action_source: "business_messaging"` y `messaging_channel: "whatsapp"` con `user_data.ctwa_clid`.

---

## 6. Fase 3 — Modelo de eventos y KPIs (el núcleo)

Cada **etapa del CRM Whaticket** debe mapearse a un **evento de Meta** con su valor. Esta es la tabla que Claude debe implementar y validar:

| Etapa en Whaticket | Evento Meta | Tipo | Parámetros clave | KPI que habilita |
|---|---|---|---|---|
| Clic en anuncio → abre WhatsApp | `Contact` | Estándar | `ctwa_clid`, `action_source=business_messaging` | CTR/CPC a WhatsApp |
| Primer mensaje / ticket nuevo | `Lead` | Estándar | `ctwa_clid`, `event_id`, `value` (opcional) | **CPL** (costo por lead) |
| Lead calificado (tag/cola/estado) | `Lead` + conversión personalizada `LeadCalificado` | Estándar + Custom | `value` estimado, `content_name=Calificado` | **CPQL** (costo por lead calificado) |
| Cotización enviada | `InitiateCheckout` o custom `Cotizacion` | Estándar/Custom | `value`, `currency`, `content_ids` | Tasa de cotización |
| Venta cerrada | `Purchase` | Estándar | `value` (monto real), `currency=USD`, `order_id` | **CPA**, **ROAS** |
| Venta perdida | custom `LeadPerdido` | Custom | `value=0`, motivo (custom) | Tasa de cierre |
| Recompra / upsell | `Purchase` | Estándar | `value`, `order_id` | LTV, ROAS |

**KPIs a configurar en Meta (Custom Conversions + columnas):**
1. **CPL** — Costo por `Lead`.
2. **CPQL** — Costo por `LeadCalificado` (conversión personalizada).
3. **CPA** — Costo por `Purchase`.
4. **ROAS** — Retorno por `Purchase` con `value`.
5. **Tasa de calificación** = LeadCalificado / Lead.
6. **Tasa de cierre** = Purchase / LeadCalificado.
7. **Valor medio por lead** = valor total / leads.
8. **Tiempo medio de conversión** (análisis en CRM, no nativo de Meta).

**Reglas de oro del modelo:**
- Enviar **`value` + `currency`** en `Purchase` y, si se puede, un **valor estimado** en `LeadCalificado` → habilita **optimización por valor (VO/ROAS)**.
- **No** enviar solo `Lead` genérico: el valor está en distinguir **calificado** y **cerrado**.
- Usar **`event_id` estable** (ej. `sp-lead-<ticketId>`) para deduplicar.

---

## 7. Fase 4 — Captura del click ID (atribución)

**A) Web (si aplica):**
- Capturar `fbclid` de la URL y persistirlo como `fbc` con formato `fb.1.<timestamp>.<fbclid>`.
- Leer la cookie `_fbp` (Pixel) y enviarla como `fbp`.
- Persistir ambos en la sesión/CRM para adjuntarlos a los eventos posteriores.

**B) WhatsApp (CTWA):**
- El webhook del WABA entrega un objeto `referral` con `ctwa_clid`, `source_id` (ID del anuncio), `source_type`, `headline`.
- **Persistir `ctwa_clid` y `source_id` en el contacto/ticket de Whaticket** (campo personalizado).
- Enviarlos en `user_data.ctwa_clid` en cada evento posterior (`Lead`, `LeadCalificado`, `Purchase`).
- Si Whaticket usa Baileys: usar el parámetro `ref` en el enlace de WhatsApp (`wa.me/...?text=...&ref=<codigo>`) y mapearlo a la campaña; la atribución será menos precisa.

---

## 8. Fase 5 — Integración Whaticket → CAPI

1. **Definir el disparador**: cambio de estado/tag del ticket o del contacto en Whaticket (lead nuevo, calificado, cerrado).
2. **Implementar un servicio** que:
   - Escuche los eventos de Whaticket (webhook o polling a su API).
   - Construya el payload de CAPI (evento + user_data hasheado + ctwa_clid/fbc/fbp).
   - Haga POST al endpoint de CAPI con `event_id` estable y reintentos con backoff.
3. **Mapear** cada cambio de estado a la tabla de la Fase 3.
4. **Registrar** cada envío (log) para auditoría y reintentos.
5. **Probar** con **Test Events** (Events Manager → Probar eventos) usando el `test_event_code`.

> Claude debe inspeccionar la documentación/instalación de Whaticket para determinar si conviene webhook, API REST o una integración custom, y proponer el diseño del servicio (lenguaje, hosting, cola de reintentos).

---

## 9. Fase 6 — Eventos Offline / CRM

- Los eventos del CRM se envían como CAPI con `action_source` adecuado:
  - `business_messaging` (WhatsApp) o
  - `website` / `system_generated` (si proviene del sistema).
- Alternativa: **Offline Conversions** (subir CSV) si no hay integración en vivo — menos óptimo, pero válido para arrancar.
- Enviar `order_id`/`ticket_id` como `external_id` para trazabilidad y deduplicación.

---

## 10. Fase 7 — Calidad, AEM y verificación de dominio

1. **Verificación de dominio** completa en Business Manager.
2. **Aggregated Event Measurement (AEM)**: configurar y **priorizar hasta 8 eventos** (los más valiosos primero):
   `Purchase` → `LeadCalificado` → `Lead` → `Contact` → `InitiateCheckout` → …
3. **Dataset Quality API**: monitorear el % de coincidencia de `user_data` (email/teléfono/ctwa_clid) y la deduplicación Pixel↔CAPI.
4. Objetivo de **Event Match Quality**: bueno o excelente.

---

## 11. Fase 8 — Audiencias y optimización

1. **Audiencias personalizadas** desde el CRM:
   - Clientes que compraron (Customer List, PII hasheada).
   - Leads calificados que no compraron (para retargeting).
   - Leads perdidos (para re-engagement).
2. **Públicos similares (Lookalike)** a partir de compradores y de leads calificados (no de leads fríos).
3. **Conversiones personalizadas** para cada etapa y **optimización por valor** en los conjuntos de anuncios de cierre.
4. **Excluir** compradores de campañas de captación para no pagar dos veces.

---

## 12. Fase 9 — Validación y monitoreo

- **Test Events**: validar cada evento con `test_event_code`.
- **Events Manager → Diagnóstico**: errores, deduplicación, match quality.
- **Comparar** (últimos 7/28 días): leads en Meta vs. tickets en Whaticket vs. ventas reales.
- **Reporte semanal** con: CPL, CPQL, CPA, ROAS, tasa de calificación y cierre.
- Detectar discrepancias (Meta reporta más/menos que el CRM) y corregir mapeo.

---

## 13. Payloads de ejemplo (CAPI)

**Lead desde WhatsApp (CTWA):**
```json
{
  "data": [
    {
      "event_name": "Lead",
      "event_time": 1760000000,
      "event_id": "sp-lead-12345",
      "action_source": "business_messaging",
      "messaging_channel": "whatsapp",
      "user_data": {
        "ctwa_clid": "<ctwa_clid>",
        "ph": "<sha256_telefono>",
        "em": "<sha256_email>",
        "external_id": "<sha256_contacto_id>"
      },
      "custom_data": { "currency": "USD", "value": "0.00", "content_name": "Lead WhatsApp" }
    }
  ]
}
```

**Lead calificado:**
```json
{
  "event_name": "LeadCalificado",
  "event_time": 1760003600,
  "event_id": "sp-qual-12345",
  "action_source": "business_messaging",
  "messaging_channel": "whatsapp",
  "user_data": { "ctwa_clid": "<ctwa_clid>", "ph": "<sha256_telefono>" },
  "custom_data": { "currency": "USD", "value": "10.00", "content_name": "Calificado" }
}
```

**Venta cerrada:**
```json
{
  "event_name": "Purchase",
  "event_time": 1760090000,
  "event_id": "sp-order-98765",
  "action_source": "business_messaging",
  "messaging_channel": "whatsapp",
  "user_data": { "ctwa_clid": "<ctwa_clid>", "ph": "<sha256_telefono>", "external_id": "<sha256_contacto_id>" },
  "custom_data": {
    "currency": "USD",
    "value": "59.99",
    "order_id": "98765",
    "content_ids": ["SKU-123"],
    "content_type": "product"
  }
}
```

---

## 14. Checklist de aceptación

- [ ] Dataset (Pixel) identificado/creado con ID documentado.
- [ ] Conversions API activa con token guardado de forma segura.
- [ ] Dominio verificado.
- [ ] Pixel base instalado (si hay web) y sin errores.
- [ ] Eventos estándar mapeados: `Contact`, `Lead`, `Purchase` (+ `InitiateCheckout` si aplica).
- [ ] Conversiones personalizadas creadas: `LeadCalificado`, `LeadPerdido`.
- [ ] `ctwa_clid` capturado y persistido en Whaticket (o plan alternativo si Baileys).
- [ ] `fbclid`/`fbc`/`fbp` capturados en web.
- [ ] Deduplicación Pixel↔CAPI por `event_id` funcionando.
- [ ] `value` + `currency` enviados en `Purchase` y valor en `LeadCalificado`.
- [ ] AEM configurado con prioridad de eventos.
- [ ] Audiencias (compradores, calificados, perdidos) y Lookalikes creadas.
- [ ] Test Events validados y Dataset Quality revisada.
- [ ] Reporte de KPIs (CPL/CPQL/CPA/ROAS) operativo.

---

## 15. Guardrails (privacidad y compliance)

- **Hashear (SHA-256)** todo PII antes de enviarlo (`em`, `ph`, `fn`, `ln`, etc.).
- Enviar solo datos que el usuario haya consentido tratar.
- No enviar datos sensibles ni categorías especiales.
- Documentar la base legal y el consentimiento en la política de privacidad del sitio.
- Limitar el acceso al token de CAPI (gestor de secretos).

---

## 16. Entregables que Claude debe devolver

1. Inventario completo (Fase 0) con IDs.
2. Dataset/Pixel configurado + token de CAPI (referenciado, no expuesto).
3. Tabla de eventos/KPIs final (Fase 3) implementada.
4. Diseño técnico de la integración Whaticket → CAPI (Fase 5).
5. Evidencia de Test Events y calidad de coincidencia.
6. Configuración de AEM, conversiones personalizadas y audiencias.
7. Reporte inicial de KPIs (CPL/CPQL/CPA/ROAS) con línea base.
