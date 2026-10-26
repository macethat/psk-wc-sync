# Análisis de compras no completadas — agosto-octubre 2026

> Estado: **análisis guardado, SIN cambios aplicados**. Pendiente de decidir con el negocio.
> Datos extraídos de WooCommerce el 2026-10-26 (últimos 60 días).

## 1. Panorama de órdenes

| Estado | Órdenes | Monto | Ticket prom. |
|---|---|---|---|
| Completadas | 54 | $3.025,11 | $56,02 |
| En proceso | 6 | $246,44 | $41,07 |
| Canceladas | 12 | $777,87 | $64,82 |
| Fallidas | 4 | $124,94 | $31,24 |
| **Total** | **76** | — | — |

Tasa de no finalización: **21% (16 de 76)**. El ticket de las canceladas es **15% mayor** que el de las completadas.

## 2. Causales

### A. Abandono en el paso de pago (10 de 12 canceladas)
La orden quedó en "Pendiente de pago" y se canceló sola al vencer el bloqueo de stock (60 min).
Casos de mayor monto: #22253 Elite Performance Stack $123,49 · #22322 ProLive Bio6+Creatina $109,49 · #22279 ProLive Bio5 $107,49 · #22286 ProLive Full Stack $99,99.

### B. Pagos declinados (4 fallidas)
Todas del mismo cliente (`fdelcastillo910@gmail.com`): "Insufficient Funds", "Issuer Decline", "Autenticación denegada" (PagueloFacil). No es falla sistémica del gateway.

### C. Órdenes "Procesando → Cancelado" (2)
#22322 ($109,49) y #22324 ($43,49) pasaron a Procesando vía Yappy y luego se cancelaron. **Requiere aclaración con el equipo**: si Yappy aprobó el cobro y se canceló, puede haber dinero cobrado sin pedido.

### D. Carritos abandonados (12 sesiones activas)
Carritos recientes sin orden (~$37 promedio): Mass Tech 6lb, Mass Extreme (×2), Whey Nutrex 2lb, L-Carnitina ×2, RAW Essential (×2), Evofusion+BUM, Lipo Stim Free, Creatina Nutrex 200, ProLive Bio6+Creatina.
**Limitación:** `wp_woocommerce_sessions` solo guarda ~48 h. No hay plugin de abandono, así que no hay histórico de carritos de meses anteriores.

## 3. Por método de pago (60 días)

| Gateway | Completadas | Canceladas | Fallidas | Finalización |
|---|---|---|---|---|
| PagueloFacil | 31 | 7 | 4 | 70,5% |
| Yappy | 23 | 5 | 0 | 71,9% |

Ambos parejos → el problema no es un gateway concreto sino el **paso de pago**.

## 4. Checkout: campos y fricción

22 campos en total, **17 requeridos**.

| Campo | Requerido | Observación |
|---|---|---|
| `billing_doc_identificacion` (Cédula/Pasaporte/RUC) | Sí | **Se mantiene**: se necesita para reclamos posteriores (decisión del negocio) |
| `shipping_postcode` (Código postal/ZIP) | Sí | En Panamá casi no se usa → candidato a opcional |
| `shipping_address_1`, city, state, phone | Sí | También se exigen en **retiro en sucursal** → candidato a ocultar |
| `billing_address_1`, city, state | Sí | — |

Plugins activos de campos: `woo-checkout-field-editor-pro` (Checkout Field Editor) y `psk-wc-checkout-field` (PSK WC Checkout Field). Revisar si hay redundancia/conflicto antes de tocar nada.

## 5. Recomendaciones (pendientes de aprobación)

1. **Recuperación de carritos**: instalar Cart Abandonment Recovery o FunnelKit (hoy no hay).
2. **Recordatorio de pago** a órdenes "Pendiente de pago" a los 15–20 min.
3. **Campaña manual** de WhatsApp/email a las 10 canceladas por timeout (ver CSV).
4. **Checkout**: evaluar `shipping_postcode` opcional y ocultar dirección en retiro. **NO** quitar `billing_doc_identificacion`.
5. **Aclarar** las 2 órdenes "Procesando → Cancelado".

## 6. Archivo de recuperación

`local/recuperacion-ordenes-oct-2026.csv` — 16 órdenes (12 canceladas + 4 fallidas) con email, nombre, teléfono, productos y motivo. Valor en riesgo: **$902,81**. Contiene datos personales de clientes: no publicar.
