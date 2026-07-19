# Checklist de Auditoría GA4 — Proyecto Genérico

## Estado del Tracking

- [ ] Propiedad GA4 creada y activa
- [ ] Measurement ID documentado
- [ ] Enhanced Measurement activado
- [ ] GTM container configurado y publicado
- [ ] DebugView muestra datos en tiempo real

## Calidad de Datos

- [ ] Sin tráfico "Direct / Not Set" excesivo (>20%)
- [ ] Tráfico interno excluido (filtro activo)
- [ ] Consent Mode v2 implementado (si aplica RGPD)
- [ ] Parámetros UTM consistentes en campañas
- [ ] Sin hits duplicados (verificar en DebugView)

## Eventos Ecommerce

- [ ] `view_item` — vista de producto
- [ ] `add_to_cart` — agregar al carrito
- [ ] `remove_from_cart` — quitar del carrito
- [ ] `view_cart` — ver carrito
- [ ] `begin_checkout` — iniciar checkout
- [ ] `add_payment_info` — agregar info de pago
- [ ] `purchase` — compra completada
- [ ] `view_item_list` — vista de listado de productos
- [ ] `select_item` — click en producto desde listado

## Integraciones

- [ ] Google Search Console vinculado a GA4
- [ ] Google Ads vinculado (si aplica)
- [ ] BigQuery vinculado (si volumen > 10M eventos/mes)

## Atribución

- [ ] Modelo Data-Driven activado
- [ ] Canales personalizados definidos (si necesario)
- [ ] Ventana de conversión configurada

## Privacidad

- [ ] Consent Mode v2 implementado
- [ ] IP anonymization activada
- [ ] Política de privacidad actualizada
