# Diagnóstico Actual — Suplementos Panamá

## Estado del Tracking

| Componente | Estado | Detalle |
|------------|--------|---------|
| Google Tag Manager | ✅ Activo | Container `GTM-PR4ZSMC7` vía plugin GTM Kit |
| Google Analytics 4 | ❌ No configurado | No hay propiedad GA4 identificada ni Measurement ID |
| Google Search Console | ✅ Activo | Vinculado con Rank Math, data disponible |
| Meta Pixel | ✅ Activo | ID `1366708898928630` vía código directo en header |
| Enhanced Ecommerce | ❌ No configurado | Sin eventos ecommerce GA4 |
| Consent Mode v2 | ❌ No implementado | Sin banner de cookies configurado |
| WooCommerce Analytics | ✅ Jetpack Stats | Estadísticas básicas vía Jetpack |

## Plugins Relacionados

| Plugin | Estado | Versión | Función |
|--------|--------|---------|---------|
| gtm-kit | Activo | 2.16.4 | Inyecta GTM container |
| seo-by-rank-math | Activo | 1.0.274.1 | SEO + integración GSC |
| jetpack | Activo | 16.0.1 | Stats básicas |
| google-listings-and-ads | Activo | 3.7.3 | Google Merchant Center |
| pixelyoursite | Activo | 11.2.1 | Meta Pixel (alternativo) |
| facebook-for-woocommerce | Activo | 3.7.5 | Facebook Feed |

## Cuentas Google Asociadas

- `suplementoscostaricashop@gmail.com` — email principal del negocio (Schema)
- `suplementospanamacrm@gmail.com` — test user para GSC API
- Proyecto Cloud: `suplementos-panama`
- Credenciales OAuth: `credentials.json` en raíz del repo

## Próximos Pasos

1. Crear propiedad GA4 en la cuenta Google apropiada
2. Configurar Measurement ID en GTM container
3. Activar Enhanced Measurement
4. Vincular GSC con GA4
5. Configurar eventos ecommerce (WooCommerce)
6. Implementar Consent Mode v2
