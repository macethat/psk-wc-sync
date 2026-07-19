# Quick Start — Configuración GA4 desde Cero

Guía paso a paso para cualquier proyecto ecommerce que no tenga Google Analytics configurado.

## 1. Crear Propiedad GA4

1. Ir a https://analytics.google.com/
2. Admin → Crear Propiedad → **GA4**
3. Nombre: `[Proyecto] - Web`
4. Zona horaria: `(GMT-5) America/Panama`
5. Moneda: `USD`

## 2. CrearFlujo de Datos Web

1. En la nueva propiedad: Admin → Flujos de datos → Web
2. URL: `https://[dominio]`
3. Nombre del flujo: `[Proyecto] Web Stream`
4. Anotar el **Measurement ID** (`G-XXXXXXXX`)

## 3. Activar Enhanced Measurement

En el flujo de datos, activar:
- Page views ✓
- Scrolls ✓ (solo al 90%)
- Outbound clicks ✓
- Site search ✓ (param: `s`)
- Video engagement ✓
- File downloads ✓

## 4. Vincular con GTM

1. En GTM, crear etiqueta **Google Analytics: GA4 Event**
2. Config:
   - Measurement ID: `G-XXXXXXXX`
   - Event Name: `{{event}}` (para pasar todos los eventos)
3. Trigger: `All Pages`
4. Publicar contenedor

## 5. Verificar

- **DebugView** en GA4 (Admin → DebugView)
- **Tag Assistant** (extensión Chrome)
- **GA4 Reportes en Tiempo Real**

## 6. Siguientes Pasos

- Configurar eventos ecommerce (ver `09_CONFIGURACION_WOOCOMMERCE.md`)
- Vincular Google Search Console
- Configurar exclusiones de tráfico interno
- Establecer modelo de atribución Data-Driven
