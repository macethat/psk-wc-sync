# Esquema de Configuración GA4 — Suplementos Panamá

> **Propósito:** Implementación real desde cero de Google Analytics 4 para
> `https://suplementospanama.net/` (WooCommerce, suplementos deportivos,
> múltiples sucursales + delivery nacional, Panamá).

---

## 1. Estructura de Propiedad GA4

### 1.1 Crear Propiedad

| Campo | Valor |
|---|---|
| Nombre de propiedad | `Suplementos Panamá - Web` |
| Moneda | `USD` (Panamá no tiene moneda local, usa USD) |
| Zona horaria | `(GMT-5) America/Panama` |
| Cuenta asociada | `suplementospanamacrm@gmail.com` |
| Tipo de industria | `Compras / Retail` |
| Tamaño de empresa | `Pequeña` |

### 1.2 Flujo de Datos Web

| Campo | Valor |
|---|---|
| Tipo | `Web` |
| URL del sitio | `https://suplementospanama.net/` |
| Nombre del stream | `Suplementos Panama Web Stream` |
| Measurement ID resultante | `G-XXXXXXXX` (anotar inmediatamente) |

### 1.3 Etiquetado Vía GTM (No gtag directo)

Como **GTM Kit** ya inyecta `GTM-PR4ZSMC7`, NO se debe agregar el código
gtag directamente al `<head>`. En su lugar:

1. Abrir **Tag Manager** → Contenedor `GTM-PR4ZSMC7`
2. Crear etiqueta:
   - **Tipo:** `Google Analytics: GA4 Event`
   - **Measurement ID:** `G-XXXXXXXX`
   - **Event Name:** `{{Event}}` (para pasar todos los eventos del dataLayer)
   - **Trigger:** `All Pages`
3. Publicar contenedor
4. Verificar con **Tag Assistant** y **GA4 DebugView**

> **Alternativa:** GTM Kit tiene opción nativa "Activar etiqueta de Google
> (ga4)" en Ajustes → GTM Kit → General. Si aparece el campo para ingresar
> el Measurement ID, usarlo directamente. Esto evita crear etiquetas manuales
> en GTM. Verificar que los datos lleguen a DebugView.

---

## 2. Eventos Ecommerce Clave

### 2.1 Eventos Estándar (Enhanced Ecommerce)

WooCommerce con GTM Kit debe empujar estos eventos al dataLayer. Si no lo
hace automáticamente, se deben configurar triggers personalizados en GTM.

#### view_item

Se dispara al ver la página de un producto individual.

```javascript
dataLayer.push({ ecommerce: null });
dataLayer.push({
  event: "view_item",
  ecommerce: {
    currency: "USD",
    value: 49.99,
    items: [{
      item_id: "PROT-001",            // SKU de WooCommerce
      item_name: "ISO WHEY 5lbs",     // Título del producto
      item_brand: "EVOGEN",           // Marca (custom taxonomy)
      item_category: "Proteínas",     // Categoría principal
      item_category2: "Aislada",      // Subcategoría (opcional)
      item_variant: "5lbs / Vainilla",// Variación (peso + sabor)
      price: 49.99,
      quantity: 1
    }]
  }
});
```

#### view_item_list

Se dispara al ver una lista/categoría de productos.

```javascript
dataLayer.push({
  event: "view_item_list",
  ecommerce: {
    item_list_id: "cat-proteinas",
    item_list_name: "Categoría: Proteínas",
    items: [{
      item_id: "PROT-001",
      item_name: "ISO WHEY 5lbs",
      item_brand: "EVOGEN",
      item_category: "Proteínas",
      price: 49.99,
      index: 1
    }, {
      item_id: "PROT-002",
      item_name: "MASS EXTREME 7lbs",
      item_brand: "EVOGEN",
      item_category: "Masa Muscular",
      price: 58.99,
      index: 2
    }]
  }
});
```

#### select_item

Se dispara al hacer clic en un producto de una lista.

```javascript
dataLayer.push({
  event: "select_item",
  ecommerce: {
    item_list_id: "cat-proteinas",
    item_list_name: "Categoría: Proteínas",
    items: [{
      item_id: "PROT-001",
      item_name: "ISO WHEY 5lbs",
      item_brand: "EVOGEN",
      item_category: "Proteínas",
      price: 49.99,
      index: 1
    }]
  }
});
```

#### add_to_cart

```javascript
dataLayer.push({ ecommerce: null });
dataLayer.push({
  event: "add_to_cart",
  ecommerce: {
    currency: "USD",
    value: 49.99,
    items: [{
      item_id: "PROT-001",
      item_name: "ISO WHEY 5lbs",
      item_brand: "EVOGEN",
      item_category: "Proteínas",
      item_variant: "5lbs / Vainilla",
      price: 49.99,
      quantity: 1
    }]
  }
});
```

#### remove_from_cart

```javascript
dataLayer.push({ ecommerce: null });
dataLayer.push({
  event: "remove_from_cart",
  ecommerce: {
    currency: "USD",
    value: 49.99,
    items: [{
      item_id: "PROT-001",
      item_name: "ISO WHEY 5lbs",
      item_brand: "EVOGEN",
      item_category: "Proteínas",
      item_variant: "5lbs / Vainilla",
      price: 49.99,
      quantity: 1
    }]
  }
});
```

#### begin_checkout

Se dispara al iniciar el checkout (clic en "Finalizar compra" o similar).

```javascript
dataLayer.push({ ecommerce: null });
dataLayer.push({
  event: "begin_checkout",
  ecommerce: {
    currency: "USD",
    value: 150.00,
    coupon: "BIENVENIDO10",          // Si aplica cupón
    items: [{
      item_id: "PROT-001",
      item_name: "ISO WHEY 5lbs",
      item_brand: "EVOGEN",
      item_category: "Proteínas",
      price: 49.99,
      quantity: 2
    }, {
      item_id: "CREA-001",
      item_name: "CREATINA MONOHIDRATO 300g",
      item_brand: "EVOGEN",
      item_category: "Creatinas",
      price: 25.99,
      quantity: 1
    }]
  }
});
```

#### add_shipping_info

```javascript
dataLayer.push({
  event: "add_shipping_info",
  ecommerce: {
    currency: "USD",
    value: 150.00,
    shipping_tier: "Delivery",       // "Recogida Tienda" o "Delivery"
    items: [/* array de items */]
  }
});
```

#### add_payment_info

```javascript
dataLayer.push({
  event: "add_payment_info",
  ecommerce: {
    currency: "USD",
    value: 150.00,
    payment_type: "Tarjeta",         // "Efectivo", "Tarjeta", "Yappy", "Transferencia"
    items: [/* array de items */]
  }
});
```

#### purchase

**Evento más importante.** Se dispara en la página de "Gracias por tu compra"
(Order Received).

```javascript
dataLayer.push({ ecommerce: null });
dataLayer.push({
  event: "purchase",
  ecommerce: {
    transaction_id: "WC-12345",      // Número de orden de WooCommerce
    affiliation: "Suplementos Panamá",
    value: 150.00,                   // Total final (incluye shipping e impuestos si hay)
    currency: "USD",
    tax: 0.00,
    shipping: 5.99,
    coupon: "BIENVENIDO10",
    items: [{
      item_id: "PROT-001",
      item_name: "ISO WHEY 5lbs",
      item_brand: "EVOGEN",
      item_category: "Proteínas",
      item_variant: "5lbs / Vainilla",
      price: 49.99,
      quantity: 2
    }, {
      item_id: "CREA-001",
      item_name: "CREATINA MONOHIDRATO 300g",
      item_brand: "EVOGEN",
      item_category: "Creatinas",
      price: 25.99,
      quantity: 1
    }]
  }
});
```

### 2.2 Eventos Personalizados Recomendados

#### add_to_wishlist

```javascript
dataLayer.push({
  event: "add_to_wishlist",
  ecommerce: {
    currency: "USD",
    value: 49.99,
    items: [{
      item_id: "PROT-001",
      item_name: "ISO WHEY 5lbs",
      item_brand: "EVOGEN",
      item_category: "Proteínas",
      price: 49.99
    }]
  }
});
```

#### view_promotion

Para banners promocionales en homepage o categorías.

```javascript
dataLayer.push({
  event: "view_promotion",
  ecommerce: {
    promotion_id: "PROMO-JULIO-2026",
    promotion_name: "Promo Julio 2026 - 15% OFF en Proteínas",
    creative_slot: "home-banner-1",
    items: [/* productos involucrados */]
  }
});
```

#### select_promotion

Cuando el usuario hace clic en la promoción.

#### view_cart (personalizado)

No es un evento estándar de Google pero es útil. Se dispara al ver la
página del carrito.

```javascript
dataLayer.push({
  event: "view_cart",
  ecommerce: {
    currency: "USD",
    value: 150.00,
    items: [/* array de items */]
  }
});
```

#### combo_view / combo_purchase

Eventos específicos para **combos agrupados** (productos agrupados de
WooCommerce). El `item_id` debe ser el ID del combo, no de los
productos individuales.

```javascript
dataLayer.push({
  event: "combo_view",
  ecommerce: {
    currency: "USD",
    value: 89.99,
    items: [{
      item_id: "COMBO-001",
      item_name: "Combo ISO 100 + Creatina",
      item_brand: "EVOGEN / EVOGEN",
      item_category: "Combos",
      item_category2: "Proteína + Creatina",
      price: 89.99,
      quantity: 1
    }]
  }
});
```

### 2.3 Mapa de Eventos vs. Momento en el Ecommerce

| Evento | Momento | Prioridad |
|---|---|---|
| `view_item` | Página de producto | Alta |
| `view_item_list` | Categorías / búsquedas | Alta |
| `select_item` | Click en producto desde lista | Alta |
| `add_to_cart` | Click "Añadir al carrito" | Alta |
| `remove_from_cart` | Click "Eliminar del carrito" | Media |
| `begin_checkout` | Click "Finalizar compra" | Alta |
| `add_shipping_info` | Seleccionar método de envío | Media |
| `add_payment_info` | Seleccionar método de pago | Media |
| `purchase` | Página de "Gracias por tu compra" | Crítica |
| `view_cart` | Página del carrito | Media |
| `add_to_wishlist` | Click "Agregar a favoritos" | Baja |
| `combo_view` | Vista de combo agrupado | Media |
| `view_promotion` | Banner promocional visto | Media |
| `select_promotion` | Click en banner promocional | Media |

---

## 3. Enhanced Measurement

### 3.1 Configuración Recomendada

En el flujo de datos web, sección **Enhanced Measurement**:

| Evento | Activar | Notas |
|---|---|---|
| Page views | **SI** | Activado por defecto |
| Scrolls | **SI** | Configurar al **90%** (no al 50%, evita ruido) |
| Outbound clicks | **SI** | Rastrea clics a afiliados, redes sociales |
| Site search | **NO** | Su sitio usa `?s=` de WordPress, pero GA4 lo detecta. Evaluar si genera ruido vs. el search interno real de productos |
| Video engagement | **SI** | Si hay videos de productos o blog |
| File downloads | **SI** | PDFs de guías, tablas nutricionales |

### 3.2 Site Search — Decisión

**Recomendación:** Activar **Site Search** con parámetro `s` (WordPress nativo)
pero solo tras validar que no hay tráfico de bots generando consultas masivas.

- Parámetro de búsqueda: `s`
- Si hay demasiado ruido (miles de searches sin conversión), desactivar y
  configurar manualmente un evento `view_search_results` con un trigger GTM
  controlado.

---

## 4. Dimensiones Personalizadas Sugeridas

### 4.1 Dimensiones a Nivel de Ítem (Event-scoped)

| Dimensión | Parámetro en GA4 | Propósito |
|---|---|---|
| Marca | `item_brand` | Analizar ventas por marca (EVOGEN, ISO100, BSN, etc.) |
| Sabor | `item_variant` | Sabor del producto (Vainilla, Chocolate, etc.) |
| Presentación | `item_variant` (o `item_category3`) | 2lbs, 5lbs, 300g, etc. |
| Tipo de Producto | `item_category` | Proteína, Creatina, Pre-entreno, Quemador, etc. |
| Subcategoría | `item_category2` | Aislada, Concentrada, Monohidrato, HCL, etc. |
| Es Combo | `item_category4` | "Sí" / "No" — para filtrar combos |

### 4.2 Dimensiones a Nivel de Evento o Usuario

| Dimensión | Parámetro | Alcance | Propósito |
|---|---|---|---|
| Sucursal de Retiro | `pickup_location` | Event | Analizar qué sucursal prefieren los clientes |
| Método de Envío | `shipping_tier` | Event | Delivery vs Recogida en Tienda |
| Método de Pago | `payment_type` | Event | Efectivo, Tarjeta, Yappy, Transferencia |
| Tipo de Cliente | `customer_type` | User | "Nuevo" vs "Recurrente" |
| Provincia de Envío | `shipping_region` | Event | Panamá, Colón, Chiriquí, etc. |
| Canal de Llegada | `session_source` (default) | Session | google / organic, facebook / cpc, direct |

### 4.3 Registro en GA4

Para crear dimensiones personalizadas en GA4:

1. **Admin → Definiciones personalizadas → Dimensiones personalizadas**
2. Crear cada dimensión con:
   - **Nombre:** legible (ej: `Marca del producto`)
   - **Parámetro del evento:** `item_brand`
   - **Alcance:** `Event` (o `Item` si aplica)
   - **Descripción:** opcional pero útil

**Dimensiones a crear explícitamente:**

| Nombre | Parámetro | Alcance |
|---|---|---|
| Marca del Producto | `item_brand` | Item |
| Sabor / Variante | `item_variant` | Item |
| Subcategoría | `item_category2` | Item |
| Es Combo | `item_category4` | Item |
| Sucursal de Retiro | `pickup_location` | Event |
| Método de Envío | `shipping_tier` | Event |
| Método de Pago | `payment_type` | Event |
| Tipo de Cliente | `customer_type` | User |

### 4.4 Métricas Personalizadas

Actualmente no son críticas. Si en el futuro se necesita medir `profit_margin`
o `shipping_cost` como número, se crean como métricas personalizadas.

---

## 5. Integración Google Search Console (GSC) con GA4

### 5.1 Prerrequisitos

- ✅ GSC activo con el correo `suplementospanamacrm@gmail.com`
- ✅ Propiedad verificada en GSC: `https://suplementospanama.net/` (o `scoped:`)
- ✅ GA4 propiedad creada con acceso de Administrador para el mismo correo

### 5.2 Pasos para Vincular

1. En **GA4** → Admin → **Google Search Console**
2. Click en **"Vincular"**
   - Si la propiedad GSC no aparece, verificar que el correo tenga acceso
3. Seleccionar la propiedad GSC correspondiente
4. Elegir el **Flujo de datos web** creado (el único)
5. Elegir el **dominio** (https://suplementospanama.net)
6. Confirmar

### 5.3 Verificación

- En GA4 → **Informes → Adquisición → Search Console**
- Deben aparecer: Impresiones, Clics, CTR, Posición promedio
- Datos desde el día de vinculación (no retroactivos completamente)

### 5.4 Beneficio Clave

Poder segmentar las consultas de búsqueda que generan tráfico y cruzarlo
con conversiones. Por ejemplo: para la consulta "proteína Panama", ver
cuántas sesiones generan, cuántas convierten, y el revenue asociado.

---

## 6. Conversiones Recomendadas

### 6.1 Eventos a Marcar como Conversiones

En **GA4 → Admin → Eventos**, marcar como **marcar como conversión**:

| Evento | Prioridad | Notas |
|---|---|---|
| `purchase` | **Crítica** | Evento principal de revenue |
| `add_to_cart` | Alta | Indica intención de compra |
| `begin_checkout` | Alta | Micro-conversión crítica |
| `add_payment_info` | Media | Cuello de botella en checkout |
| `combo_view` | Baja | Interés en combos (opcional) |
| `generate_lead` | Baja | Si implementan formulario de "Cotización" |

### 6.2 Configuración

1. Ir a **Admin → Eventos** (en la columna Propiedad)
2. Buscar cada evento listado
3. Activar el toggle **"Marcar como conversión"**

### 6.3 Valor de Conversión

GA4 usará automáticamente el parámetro `value` del evento `purchase` como
valor de conversión. No requiere configuración extra.

---

## 7. Modelo de Atribución

### 7.1 Configuración

1. **Admin → Atribución → Modelo de atribución**
2. Seleccionar: **"Data-Driven"**
3. **Ventana de conversión:** 30 días para tráfico pagado, 90 días para
   tráfico orgánico (valores por defecto recomendados)
4. Guardar

### 7.2 Justificación

| Modelo | Cuándo usarlo |
|---|---|
| **Data-Driven** | **Recomendado.** Usa machine learning para distribuir el crédito según el impacto real de cada punto de contacto. Requiere ~400 clics en anuncios y ~400 conversiones en 30 días para activarse |
| Último clic | Útil como referencia, pero pierde impacto de canales de descubrimiento (Meta, influencers) |
| Primer clic | Para entender canales top-of-funnel |
| Lineal | Reparte crédito equitativamente |

**Nota:** Panama tiene alta penetración de Meta (Facebook/Instagram) y uso
creciente de TikTok. Data-Driven capturará mejor el rol de estos canales
en el customer journey. Si el volumen de datos es insuficiente inicialmente,
GA4 usará Último Clic como fallback automático mientras aprende.

---

## 8. Exclusiones

### 8.1 Tráfico Interno

1. **Admin → Flujos de datos → [Seleccionar stream] → Ajustes de etiquetas → Definir tráfico interno**
2. Crear regla:
   - Nombre: `Tráfico Interno Suplementos Panamá`
   - Tipo: `IP addresses`
   - Valor: IP de SiteGround (donde está alojado el sitio)
3. Crear reglas adicionales para:
   - IP de la oficina/sucursales físicas de Suplementos Panamá
   - IP del equipo de desarrollo (VPN si aplica)
   - IP del hosting (SiteGround)

**Luego:**
4. **Admin → Ajustes de datos → Filtros de datos**
5. Crear filtro:
   - Nombre: `Internal Traffic Filter`
   - Tipo de tráfico: `Internal Traffic`
   - Estado: `Testing` primero, luego `Active` tras verificar en DebugView

### 8.2 Exclusión de Referrers (Referrals)

Si la tienda usa pasarelas de pago externas que redirigen de vuelta al sitio,
excluirlas para evitar que aparezcan como fuente de tráfico propia.

1. **Admin → Flujos de datos → [Stream] → Ajustes de etiquetas → Mostrar más → Lista de referidos no deseados**
2. Agregar:
   - `*.paypal.com`
   - `*.pagadito.com` (si usan)
   - `*.yappy.com.pa` (si usan Yappy de Banco General)
   - Cualquier otra pasarela de pago externa que redirija al sitio

### 8.3 Exclusión de Bots y Crawlers

GA4 ya filtra bots y spiders conocidos (Googlebot, Bingbot, etc.)
por defecto. No requiere configuración adicional.

---

## 9. Consent Mode v2

### 9.1 ¿Aplica para Panamá?

**Panamá no tiene una ley RGPD equivalente** (como la Ley de Protección de
Datos Personales 81 de 2019, que aplica principalmente a datos
gubernamentales y no exige consentimiento explícito para cookies de
analítica). **No es obligatorio legalmente.**

### 9.2 Recomendación: Buenas Prácticas

Sin embargo, implementar **Consent Mode v2 básico** porque:

- Google puede requerir consent mode para ciertas funcionalidades de
  anuncios en el futuro (ya es requisito en Europa, y Google lo está
  expandiendo)
- Si en el futuro venden a clientes desde la UE (turistas, residentes),
  necesitarán consent mode
- Buenas prácticas de transparencia con el usuario

### 9.3 Configuración Mínima Recomendada

**Opción A — Sin banner de cookies (mínima):**

No implementar consent mode por ahora. Panamá no lo exige. Google Analytics
funcionará sin restricciones.

**Opción B — Banner simple + Consent Mode (recomendada para escalar):**

1. Instalar plugin de consentimiento compatible (Complianz, Cookiebot, o
   el que trae GTM Kit si lo soporta)
2. Configurar consent default en GTM:
   ```javascript
   gtag('consent', 'default', {
     'ad_storage': 'denied',
     'analytics_storage': 'denied',
     'ad_user_data': 'denied',
     'ad_personalization': 'denied',
     'wait_for_update': 500
   });
   ```
3. En el banner, ofrecer opciones: "Aceptar" / "Solo necesarias"
4. Si el usuario acepta, actualizar a `granted`
5. **Beneficio:** Google modelizará los datos de usuarios que rechacen
   usando machine learning (modeled data), recuperando ~60-80% de los datos
   perdidos

> **Decisión:** Arrancar con **Opción A** (sin consent mode). Evaluar
> implementar **Opción B** cuando el negocio crezca o si Google lo exige
> para la región.

---

## 10. Dashboard en Looker Studio

### 10.1 Panel Ejecutivo — Métricas Clave

Conectar GA4 a Looker Studio usando el conector nativo de Google Analytics.

#### Página 1: Resumen Ejecutivo (Top Line)

| Métrica | Visualización |
|---|---|
| Revenue total (últimos 30 días) | Big number card |
| Revenue vs. período anterior | Big number card con cambio % |
| Transacciones totales | Big number card |
| Tasa de conversión ecommerce | Big number card |
| Ingreso promedio por pedido (AOV) | Big number card |
| Usuarios totales | Big number card |
| Tendencia de revenue (diario/30d) | Time series chart |
| Top 10 productos por revenue | Bar chart horizontal |

#### Página 2: Adquisición

| Métrica | Visualización |
|---|---|
| Sesiones por canal (organic, direct, social, paid) | Pie chart o stacked bar |
| Tasa de conversión por canal | Table |
| Costo estimado por adquisición (si hay datos de Google Ads) | Table |
| Consultas GSC que generan más conversiones | Table |
| CTR y posición promedio desde GSC | Table |

#### Página 3: Comportamiento de Producto

| Métrica | Visualización |
|---|---|
| Productos más vistos (view_item) | Bar chart |
| Productos más añadidos al carrito (add_to_cart) | Bar chart |
| Tasa add_to_cart → purchase por producto | Table |
| Revenue por categoría de producto | Pie chart |
| Revenue por marca (item_brand) | Bar chart |
| Rendimiento de combos vs. productos individuales | Table comparativa |

#### Página 4: Checkout y Logística

| Métrica | Visualización |
|---|---|
| Embudo de conversión: view_item → add_to_cart → begin_checkout → purchase | Funnel chart |
| Abandono de carrito (add_to_cart sin purchase) | Gauge o single value |
| Método de envío preferido (Delivery vs Recogida) | Pie chart |
| Método de pago más usado (payment_type) | Pie chart |
| Sucursales con más retiros (pickup_location) | Bar chart |
| Provincias con más envíos (shipping_region) | Geo map de Panamá |

#### Página 5: Tendencias y Estacionalidad

| Métrica | Visualización |
|---|---|
| Revenue por día de la semana | Bar chart |
| Revenue por hora del día | Heatmap (día vs hora) |
| Revenue semanal comparado (últimos 90 días) | Time series |
| Días pico de compra (quincena/fin de mes) | Time series |

### 10.2 Filtros Globales del Dashboard

- **Rango de fechas** (selector de fecha estándar)
- **Tipo de producto** (Proteína, Creatina, Pre-entreno, Quemador, etc.)
- **Marca**
- **Fuente / Medio** (Google / organic, Facebook / cpc, etc.)

### 10.3 Consideraciones Técnicas

- GA4 tiene **límites de cuota API** en Looker Studio (aprox. 50k requests/día
  para cuentas gratuitas). Si el dashboard tiene muchos widgets, algunos
  pueden fallar con `Resources Exhausted`
- **Solución:** Usar BigQuery como capa intermedia cuando el volumen crezca
- **Frecuencia de actualización:** Cada 4-6 horas es suficiente. No en tiempo
  real salvo que sea crítico

---

## Checklist de Implementación

### Fase 1 — Creación (Día 1)

- [ ] Crear propiedad GA4 con moneda USD y timezone America/Panama
- [ ] Crear flujo de datos web
- [ ] Anotar Measurement ID `G-XXXXXXXX`
- [ ] Configurar etiqueta GA4 en GTM Kit o GTM manual
- [ ] Publicar contenedor GTM
- [ ] Verificar con Tag Assistant y DebugView
- [ ] Activar Enhanced Measurement (scroll al 90%, outbound clicks, file downloads)

### Fase 2 — Eventos (Día 2-3)

- [ ] Verificar que WooCommerce empuja eventos `view_item`, `add_to_cart`, `purchase`
- [ ] Si no llegan automáticamente, configurar triggers GTM
- [ ] Verificar parámetros: `item_id`, `item_name`, `item_brand`, `item_category`, `price`, `quantity`
- [ ] Agregar eventos faltantes: `begin_checkout`, `add_shipping_info`, `add_payment_info`
- [ ] Agregar dimensión personalizada `item_brand` (si no viene por defecto)
- [ ] Agregar dimensión personalizada `pickup_location` (para sucursales)

### Fase 3 — Conversiones y Exclusiones (Día 3-4)

- [ ] Marcar `purchase`, `add_to_cart`, `begin_checkout` como conversiones
- [ ] Configurar modelo de atribución Data-Driven
- [ ] Crear filtro de tráfico interno (IP oficina + hosting)
- [ ] Excluir referrers de pasarelas de pago
- [ ] Verificar en DebugView que el tráfico interno no se registre

### Fase 4 — Integraciones y Dashboard (Día 5-7)

- [ ] Vincular GSC con GA4
- [ ] Verificar datos de Search Console en GA4
- [ ] Crear Looker Studio Dashboard (o empezar con plantilla)
- [ ] Configurar alertas: caída de conversiones >20%, cero purchases en 24h
- [ ] Capacitar al equipo: cómo leer los reportes, qué métricas priorizar

### Fase 5 — Post-lanzamiento (Semana 2-4)

- [ ] Comparar datos de GA4 con WooCommerce Analytics (discrepancia normal <5-10%)
- [ ] Ajustar dimensiones personalizadas según necesidades que surjan
- [ ] Evaluar si implementar Consent Mode v2 (Opción B)
- [ ] Revisar modelo de atribución: si hay suficientes datos, confirmar Data-Driven
- [ ] Configurar audiencias: "Visitantes que vieron proteínas pero no compraron"

---

## Referencias Rápidas

| Concepto | Dónde encontrarlo en GA4 |
|---|---|
| Medición mejorada | Admin → Flujos de datos → [Stream] → Enhanced Measurement |
| Eventos | Admin → Eventos |
| Conversiones | Admin → Conversiones |
| Dimensiones personalizadas | Admin → Definiciones personalizadas → Dimensiones |
| Filtros de tráfico | Admin → Ajustes de datos → Filtros de datos |
| Atribución | Admin → Atribución → Modelo de atribución |
| Vincular GSC | Admin → Google Search Console |
| DebugView | Admin → DebugView |
| Informes | Informes → Ciclo de vida (Adquisición, Interacción, Monetización) |
| Exploraciones | Explorar → Formulario libre |
