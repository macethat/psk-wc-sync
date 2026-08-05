# 📘 Guía Tutorial: Optimización de Datos Estructurados para Jerarquía y Visibilidad en Ecommerce (SEO & GEO)

## Arquitectura Técnica para Dominio Visual en SERPs

---

## 📌 Introducción: Los Datos Estructurados como Activo Estratégico

Como Estrategas de SEO Técnico, debemos abordar los datos estructurados no como un simple requisito de código, sino como un **activo estratégico fundamental**. Representan el **mapa semántico** que permite a Google interpretar con precisión la arquitectura de información de un ecommerce, traduciendo un catálogo complejo en entidades claras.

Una implementación impecable no solo facilita el rastreo, sino que es el **motor principal** para dominar el "visual real estate" en las SERPs; una mejora en la interpretación de la jerarquía impacta directamente en la tasa de clics (CTR), capturando la atención del usuario antes de que este siquiera visite el sitio.

### El Impacto de la Jerarquía en el Negocio

| Área de Impacto | Beneficio Esperado |
|-----------------|-------------------|
| **Visibilidad en SERPs** | Dominio del espacio visual con breadcrumbs y rich results |
| **CTR** | Incremento del 20-35% en tasas de clics |
| **Comprensión del catálogo** | Google entiende la profundidad taxonómica |
| **Autoridad temática** | Refuerzo de Topic Authority |
| **GEO** | Citaciones en motores de IA para búsquedas jerárquicas |

> 💡 **Concepto Clave:** La jerarquía bien estructurada no es solo navegación para usuarios. Es el mapa semántico que permite a Google y a los motores de IA entender la relación entre categorías, subcategorías y productos.

---

## 1. 🎯 Fundamentos Estratégicos de los Datos Estructurados

### Habilitar vs. Garantizar Resultados Enriquecidos

Es imperativo distinguir entre **habilitar** y **garantizar** resultados enriquecidos. El marcado técnico correcto simplemente habilita la elegibilidad; sin embargo, Google solo garantiza la visualización si se cumplen estrictamente las directrices de calidad (relevancia y completitud).

**Factores que determinan la visualización:**
- ✅ Historial de búsqueda del usuario
- ✅ Ubicación geográfica
- ✅ Tipo de dispositivo (móvil vs desktop)
- ✅ Competencia en la SERP
- ✅ Calidad general del sitio
- ✅ Historial de cumplimiento de políticas

> ⚠️ **ADVERTENCIA CRÍTICA:** Si los datos están ocultos, son engañosos o no representan el contenido principal, la visibilidad competitiva se verá anulada.

### Formatos Soportados por Google

Para maximizar esta visibilidad, Google soporta tres formatos, con una recomendación clara:

#### 1. JSON-LD (Recomendación Principal) ⭐⭐⭐⭐⭐

**Descripción:** Es un script separado del cuerpo del HTML.

**Ventajas arquitectónicas:**
- ✅ Permite a los desarrolladores gestionar los datos sin ensuciar el marcado visual
- ✅ Reduce el riesgo de errores de sintaxis
- ✅ Elimina duplicidad de código
- ✅ Facilita el mantenimiento
- ✅ Compatible con frameworks modernos
- ✅ Inyección dinámica de datos

**Ejemplo de JSON-LD:**

```html
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Zapatillas Running Pro - Mi Tienda Online</title>
  
  <!-- Datos Estructurados JSON-LD -->
  <script type="application/ld+json">
  {
    "@context": "https://schema.org",
    "@type": "Product",
    "name": "Zapatillas Running Pro",
    "image": "https://ejemplo.com/fotos/zapatillas.jpg",
    "description": "Zapatillas ideales para principiantes en maratón",
    "brand": {
      "@type": "Brand",
      "name": "MarcaDeportiva"
    },
    "sku": "ZP-12345",
    "offers": {
      "@type": "Offer",
      "price": "99.99",
      "priceCurrency": "USD",
      "availability": "https://schema.org/InStock"
    }
  }
  </script>
</head>
<body>
  <!-- Contenido visible para el usuario -->
</body>
</html>
```

#### 2. Microdatos ⭐⭐⭐

**Descripción:** Atributos incrustados directamente en el HTML.

**Desventajas:**
- ❌ Mezcla datos con presentación
- ❌ Difícil de mantener
- ❌ Propenso a errores

**Ejemplo de Microdatos:**

```html
<div itemscope itemtype="https://schema.org/Product">
  <h1 itemprop="name">Zapatillas Running Pro</h1>
  <img itemprop="image" src="https://ejemplo.com/fotos/zapatillas.jpg" />
  <div itemprop="offers" itemscope itemtype="https://schema.org/Offer">
    <span itemprop="price">$99.99</span>
    <meta itemprop="priceCurrency" content="USD" />
  </div>
</div>
```

#### 3. RDFa ⭐⭐

**Descripción:** Extensiones de etiquetas HTML5.

**Desventajas:**
- ❌ Sintaxis compleja
- ❌ Poco usado
- ❌ Difícil de implementar

### Comparación de Formatos

| Característica | JSON-LD | Microdata | RDFa |
|----------------|---------|-----------|------|
| **Recomendación Google** | ✅ Sí (Principal) | ✅ Sí | ✅ Sí |
| **Separación de datos/presentación** | ✅ Completa | ❌ Mezclada | ❌ Mezclada |
| **Mantenibilidad** | ⭐⭐⭐⭐⭐ | ⭐⭐ | ⭐⭐ |
| **Compatibilidad con JS** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ |
| **Legibilidad** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ |

> 💡 **RECOMENDACIÓN:** JSON-LD es el estándar de facto para implementaciones modernas. Su separación limpia de datos y presentación lo hace ideal para ecommerce escalable.

### La Base de la Interpretación Semántica

La base de una interpretación semántica coherente comienza con la **navegación**, lo que nos lleva a la cimentación de la jerarquía del sitio. Sin una jerarquía clara, los motores de búsqueda no pueden comprender la relación entre categorías y productos.

---

## 2. 🧭 Definición de la Jerarquía: Implementación de BreadcrumbList

### La Importancia Estratégica de BreadcrumbList

El marcado `BreadcrumbList` es **vital** para que los motores de búsqueda comprendan la posición exacta de una página dentro de la jerarquía del catálogo. Más allá de ayudar al usuario, este esquema comunica la estructura del inventario de forma que Google pueda categorizar el contenido de manera eficiente.

### Beneficios de BreadcrumbList

| Beneficio | Descripción |
|-----------|-------------|
| **Comprensión de jerarquía** | Google entiende la profundidad taxonómica |
| **Mejora estética** | URLs crípticas → rutas legibles en SERPs |
| **Aumento de CTR** | Mejor presentación en resultados de búsqueda |
| **Autoridad temática** | Refuerza Topic Authority |
| **Navegación clara** | Experiencia de usuario mejorada |

### El Rol Crítico de @id

> 🔴 **REGLA CRÍTICA:** El uso de la propiedad `@id` es **innegociable**.

**¿Por qué @id es esencial?**

`@id` actúa como el "pegamento" que vincula el elemento de la miga de pan con la entidad real de la página en el gráfico de conocimiento.

**Sin @id:**
- ❌ Riesgo de generar "datos huérfanos"
- ❌ Google detecta el marcado pero no lo conecta con la entidad raíz
- ❌ Dilución de la autoridad de la ruta de navegación
- ❌ Pérdida de señales semánticas

**Con @id:**
- ✅ Vinculación sólida con entidades del Knowledge Graph
- ✅ Consolidación de autoridad temática
- ✅ Mejor comprensión de relaciones semánticas
- ✅ Trazabilidad completa

### Estructura Lógica de Marcado Jerárquico

Para un ecommerce, la estructura debe ser un **reflejo descendente** del gráfico de conocimiento:

```
Nivel 1 (Home) → Nodo raíz de la organización
    ↓
Nivel 2 (Categoría) → Agrupación semántica (ej. "Calzado Deportivo")
    ↓
Nivel 3 (Subcategoría) → Especialización (ej. "Running")
    ↓
Nivel 4 (Producto) → Nodo final de conversión
```

### Implementación Completa de BreadcrumbList con @id

```json
{
  "@context": "https://schema.org",
  "@type": "BreadcrumbList",
  "itemListElement": [
    {
      "@type": "ListItem",
      "@id": "https://ejemplo.com/#home",
      "position": 1,
      "name": "Inicio",
      "item": "https://ejemplo.com"
    },
    {
      "@type": "ListItem",
      "@id": "https://ejemplo.com/calzado/#category",
      "position": 2,
      "name": "Calzado Deportivo",
      "item": "https://ejemplo.com/calzado"
    },
    {
      "@type": "ListItem",
      "@id": "https://ejemplo.com/calzado/running/#subcategory",
      "position": 3,
      "name": "Running",
      "item": "https://ejemplo.com/calzado/running"
    },
    {
      "@type": "ListItem",
      "@id": "https://ejemplo.com/calzado/running/zapatillas-pro/#product",
      "position": 4,
      "name": "Zapatillas Running Pro"
    }
  ]
}
```

### Vinculación Semántica con @graph

Para implementaciones complejas, usa `@graph` para vincular múltiples entidades:

```json
{
  "@context": "https://schema.org",
  "@graph": [
    {
      "@type": "BreadcrumbList",
      "@id": "https://ejemplo.com/calzado/running/zapatillas-pro/#breadcrumb",
      "itemListElement": [
        {
          "@type": "ListItem",
          "position": 1,
          "name": "Inicio",
          "item": "https://ejemplo.com"
        },
        {
          "@type": "ListItem",
          "position": 2,
          "name": "Calzado",
          "item": "https://ejemplo.com/calzado"
        },
        {
          "@type": "ListItem",
          "position": 3,
          "name": "Running",
          "item": "https://ejemplo.com/calzado/running"
        },
        {
          "@type": "ListItem",
          "position": 4,
          "name": "Zapatillas Running Pro"
        }
      ]
    },
    {
      "@type": "Product",
      "@id": "https://ejemplo.com/calzado/running/zapatillas-pro/#product",
      "name": "Zapatillas Running Pro",
      "category": {
        "@id": "https://ejemplo.com/calzado/running/#subcategory"
      },
      "isPartOf": {
        "@id": "https://ejemplo.com/calzado/#category"
      }
    }
  ]
}
```

### Requisitos Técnicos Críticos

#### 1. URLs Absolutas

**🔴 REGLA CRÍTICA:** El campo `item` debe contener **siempre** la URL completa.

**✅ CORRECTO:**
```json
{
  "item": "https://ejemplo.com/calzado/running"
}
```

**❌ INCORRECTO:**
```json
{
  "item": "/calzado/running"  // ❌ URL relativa
}
```

> ⚠️ **Advertencia:** El uso de URLs relativas es un **error crítico** que rompe el grafo de conocimiento.

#### 2. Posición Secuencial

Secuencia numérica estricta iniciando en 1.

**✅ CORRECTO:**
```json
{
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 1,
      "name": "Inicio",
      "item": "https://ejemplo.com"
    },
    {
      "@type": "ListItem",
      "position": 2,
      "name": "Calzado",
      "item": "https://ejemplo.com/calzado"
    },
    {
      "@type": "ListItem",
      "position": 3,
      "name": "Running",
      "item": "https://ejemplo.com/calzado/running"
    }
  ]
}
```

**❌ INCORRECTO:**
```json
{
  "itemListElement": [
    {
      "@type": "ListItem",
      "position": 2,  // ❌ No inicia en 1
      "name": "Inicio"
    },
    {
      "@type": "ListItem",
      "position": 2,  // ❌ Posición duplicada
      "name": "Calzado"
    }
  ]
}
```

#### 3. Coincidencia con HTML Visible

**🔴 REGLA DE ORO:** Las migas de pan deben reflejar la **navegación real visible**.

**❌ VIOLACIÓN:**
```
HTML Visible: Inicio > Calzado > Zapatillas
Schema: Inicio > Calzado > Running > Zapatillas

❌ Violación: El schema incluye "Running" que no está visible
```

**✅ COINCIDENCIA PERFECTA:**
```
HTML Visible: Inicio > Calzado > Running > Zapatillas
Schema: Inicio > Calzado > Running > Zapatillas

✅ Coincidencia exacta entre HTML y schema
```

### Errores Comunes en BreadcrumbList

#### ❌ Error 1: URLs Relativas

```json
// INCORRECTO
{
  "item": "/calzado/running"
}

// CORRECTO
{
  "item": "https://ejemplo.com/calzado/running"
}
```

#### ❌ Error 2: Falta el Último Elemento

```json
// INCORRECTO: Falta el producto actual
{
  "itemListElement": [
    {
      "position": 1,
      "name": "Inicio",
      "item": "https://ejemplo.com"
    },
    {
      "position": 2,
      "name": "Calzado",
      "item": "https://ejemplo.com/calzado"
    }
    // ❌ Falta el producto actual
  ]
}

// CORRECTO: Incluye todos los niveles
{
  "itemListElement": [
    {
      "position": 1,
      "name": "Inicio",
      "item": "https://ejemplo.com"
    },
    {
      "position": 2,
      "name": "Calzado",
      "item": "https://ejemplo.com/calzado"
    },
    {
      "position": 3,
      "name": "Zapatillas Running Pro"
      // ✅ No necesita "item" porque es la página actual
    }
  ]
}
```

#### ❌ Error 3: Nombres No Descriptivos

```json
// INCORRECTO
{
  "name": "cat_123"  // ❌ No es legible
}

// CORRECTO
{
  "name": "Zapatillas Running"  // ✅ Descriptivo y claro
}
```

### Optimización Visual del Snippet

Más allá de la navegación, las breadcrumbs optimizan la **apariencia visual** de la URL en el snippet de búsqueda.

**Antes de BreadcrumbList:**
```
https://ejemplo.com/cat/123/prod/456?ref=abc
```

**Después de BreadcrumbList:**
```
Inicio › Calzado › Running › Zapatillas Running Pro
https://ejemplo.com/calzado/running/zapatillas-pro
```

**Beneficios visuales:**
- ✅ Mejora la estética del snippet
- ✅ Comunica la profundidad del inventario
- ✅ Refuerza la relación semántica
- ✅ Mayor claridad para el usuario
- ✅ Posible aumento de CTR

### Checklist de BreadcrumbList

- [ ] URLs absolutas en todos los elementos
- [ ] Secuencia numérica desde posición 1
- [ ] Coincidencia exacta con breadcrumbs visibles
- [ ] Último elemento sin propiedad `item`
- [ ] Nombres descriptivos y concisos
- [ ] Profundidad razonable (máximo 5-6 niveles)
- [ ] Refleja la estructura real del sitio
- [ ] Uso de `@id` para vinculación semántica
- [ ] Implementación con `@graph` para entidades complejas

---

## 3. 🏷️ Inteligencia de Producto: ProductGroup y Variantes

### El Desafío de las Variantes

Gestionar variantes (talla, color, material) es un desafío técnico que, si se ignora, produce:

- ❌ Duplicidad de contenido
- ❌ Fragmentación de autoridad de página
- ❌ Experiencia de usuario deficiente
- ❌ Rechazo en Merchant Listings

### La Solución: ProductGroup

La clase `ProductGroup` agrupa estas variantes bajo un "padre" conceptual, optimizando la experiencia en los Merchant Listings.

### Estrategias de Arquitectura

#### Opción A: Página Única (Selección Dinámica)

**Características:**
- Todas las variantes se cargan en una URL
- Selección mediante parámetros (ej: `?size=XL&color=blue`)
- Sin recarga de página
- JavaScript maneja la selección dinámica

**Regla Crítica:**
> 🔴 Debe existir una **única URL canónica** para el ProductGroup general (la página base sin preselección).

**Estructura de URLs:**
```
URL canónica: https://ejemplo.com/zapatillas-running-pro
Variantes:
  - ?color=rojo&size=42
  - ?color=azul&size=43
  - ?color=negro&size=44
```

#### Opción B: Sitios Multipágina (URLs Distintas)

**Características:**
- Cada variante tiene su propia URL única
- URLs separadas para cada combinación de atributos
- Cada página es independiente

**Regla de Seniority:**
> 🔴 Para sitios de páginas múltiples, el estándar de seniority exige que la **definición completa de ProductGroup se duplique en cada una de las URLs de las variantes**. Esto asegura que Google comprenda los atributos comunes (marca, material) sin necesidad de saltar entre páginas.

**Estructura de URLs:**
```
https://ejemplo.com/zapatillas-running-pro-rojo-42
https://ejemplo.com/zapatillas-running-pro-azul-43
https://ejemplo.com/zapatillas-running-pro-negro-44
```

### Propiedades Estratégicas de ProductGroup

| Propiedad | Tipo | Impacto Estratégico |
|-----------|------|---------------------|
| `name` | **Requerida** | Identificador semántico del grupo |
| `productGroupID` | **Requerida** | El "Parent SKU" que consolida el gráfico de variantes |
| `brand` | **Recomendada** | Establece la autoridad institucional de la marca |
| `description` | **Recomendada** | Contexto rico; debe ser más general que la del producto individual |
| `aggregateRating` | **Recomendada** | Consolida la prueba social de todas las variantes bajo un solo impacto visual |
| `hasAdultConsideration` | **Evaluada** | Crítica para cumplimiento legal en contenido sensible (solo `https://schema.org/SexualContentConsideration`) |

### URLs Completas de Schema.org para `variesBy`

**🔴 REGLA CRÍTICA:** Las propiedades de variación en `variesBy` deben referenciarse mediante su **URL completa de Schema.org** para evitar fallos de validación comunes.

**✅ URLs válidas soportadas por Google:**

| Propiedad | URL de Schema.org |
|-----------|-------------------|
| Color | `https://schema.org/color` |
| Talla | `https://schema.org/size` |
| Material | `https://schema.org/material` |
| Patrón | `https://schema.org/pattern` |
| Edad sugerida | `https://schema.org/suggestedAge` |
| Género sugerido | `https://schema.org/suggestedGender` |

**✅ Ejemplo correcto:**

```json
{
  "variesBy": [
    "https://schema.org/color",
    "https://schema.org/size",
    "https://schema.org/material"
  ]
}
```

**❌ Ejemplo incorrecto:**

```json
{
  "variesBy": [
    "color",
    "size",
    "material"
  ]
}
```

### Implementación Completa de ProductGroup

```json
{
  "@context": "https://schema.org/",
  "@type": "ProductGroup",
  "@id": "https://ejemplo.com/zapatillas-running-pro/#productgroup",
  "name": "Zapatillas Running Pro",
  "productGroupID": "ZAPATILLAS-PRO-2026",
  "variesBy": [
    "https://schema.org/color",
    "https://schema.org/size"
  ],
  "brand": {
    "@type": "Brand",
    "name": "MarcaDeportiva"
  },
  "description": "Zapatillas ideales para principiantes en maratón, disponibles en múltiples colores y tallas",
  "hasVariant": [
    {
      "@type": "Product",
      "@id": "https://ejemplo.com/zapatillas-running-pro-rojo-42/#product",
      "name": "Zapatillas Running Pro - Rojo - Talla 42",
      "sku": "ZP-ROJO-42",
      "gtin13": "1234567890123",
      "color": "Rojo",
      "size": "42",
      "url": "https://ejemplo.com/zapatillas-running-pro-rojo-42",
      "image": "https://ejemplo.com/fotos/zapatillas-rojo-42.jpg",
      "offers": {
        "@type": "Offer",
        "price": "99.99",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock"
      }
    },
    {
      "@type": "Product",
      "@id": "https://ejemplo.com/zapatillas-running-pro-azul-43/#product",
      "name": "Zapatillas Running Pro - Azul - Talla 43",
      "sku": "ZP-AZUL-43",
      "gtin13": "1234567890124",
      "color": "Azul",
      "size": "43",
      "url": "https://ejemplo.com/zapatillas-running-pro-azul-43",
      "image": "https://ejemplo.com/fotos/zapatillas-azul-43.jpg",
      "offers": {
        "@type": "Offer",
        "price": "99.99",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock"
      }
    },
    {
      "@type": "Product",
      "@id": "https://ejemplo.com/zapatillas-running-pro-negro-44/#product",
      "name": "Zapatillas Running Pro - Negro - Talla 44",
      "sku": "ZP-NEGRO-44",
      "gtin13": "1234567890125",
      "color": "Negro",
      "size": "44",
      "url": "https://ejemplo.com/zapatillas-running-pro-negro-44",
      "image": "https://ejemplo.com/fotos/zapatillas-negro-44.jpg",
      "offers": {
        "@type": "Offer",
        "price": "109.99",
        "priceCurrency": "USD",
        "availability": "https://schema.org/OutOfStock"
      }
    }
  ],
  "aggregateRating": {
    "@type": "AggregateRating",
    "ratingValue": "4.5",
    "reviewCount": "234"
  }
}
```

### Implementación para Sitios Multipágina

En cada página de variante, el ProductGroup debe estar completo:

```json
{
  "@context": "https://schema.org/",
  "@type": "ProductGroup",
  "@id": "https://ejemplo.com/zapatillas-running-pro/#productgroup",
  "name": "Zapatillas Running Pro",
  "productGroupID": "ZAPATILLAS-PRO-2026",
  "variesBy": [
    "https://schema.org/color",
    "https://schema.org/size"
  ],
  "hasVariant": [
    {
      "@type": "Product",
      "@id": "https://ejemplo.com/zapatillas-running-pro-rojo-42/#product",
      "name": "Zapatillas Running Pro - Rojo - Talla 42",
      "sku": "ZP-ROJO-42",
      "url": "https://ejemplo.com/zapatillas-running-pro-rojo-42",
      "offers": {
        "@type": "Offer",
        "price": "99.99",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock"
      }
    },
    {
      "@type": "Product",
      "@id": "https://ejemplo.com/zapatillas-running-pro-azul-43/#product",
      "name": "Zapatillas Running Pro - Azul - Talla 43",
      "sku": "ZP-AZUL-43",
      "url": "https://ejemplo.com/zapatillas-running-pro-azul-43",
      "offers": {
        "@type": "Offer",
        "price": "99.99",
        "priceCurrency": "USD",
        "availability": "https://schema.org/InStock"
      }
    }
  ]
}
```

### Checklist de ProductGroup

- [ ] `productGroupID` definido y consistente en todas las variantes
- [ ] `variesBy` con URLs completas de Schema.org
- [ ] Cada variante con SKU o GTIN único
- [ ] URLs canónicas correctas configuradas
- [ ] Todas las variantes bajo `hasVariant`
- [ ] Precios y disponibilidad precisos por variante
- [ ] Imágenes específicas para cada variante
- [ ] `aggregateRating` consolidado a nivel de grupo
- [ ] `brand` definido a nivel de grupo (no repetido)
- [ ] Uso de `@id` para vinculación semántica
- [ ] ProductGroup duplicado en cada página (sitios multipágina)

---

## 4. 🌍 Optimización de SEO Local (GEO) y Confianza del Comercio

### La Relevancia Geográfica (GEO)

La relevancia geográfica (GEO) y la confianza institucional se establecen mediante la anidación de políticas en las entidades `Organization` o `LocalBusiness`.

### Propiedades Clave para GEO

**`applicableCountry` en MerchantReturnPolicy:**
- ✅ Actúa como señal local
- ✅ Google utiliza estas señales para mostrar condiciones específicas en paneles de conocimiento regionales
- ✅ Usa códigos ISO 3166-1 alpha-2

### Restricciones Geográficas de MemberProgram

> ⚠️ **ADVERTENCIA CRÍTICA:** Funciones avanzadas como `MemberProgram` (programas de lealtad) están **geográficamente restringidas**.

**Países actualmente soportados:**

| País | Código | Estado |
|------|--------|--------|
| 🇦🇺 Australia | AU | ✅ Disponible |
| 🇧🇷 Brasil | BR | ✅ Disponible |
| 🇨🇦 Canadá | CA | ✅ Disponible |
| 🇫🇷 Francia | FR | ✅ Disponible |
| 🇩🇪 Alemania | DE | ✅ Disponible |
| 🇲🇽 México | MX | ✅ Disponible |
| 🇬🇧 Reino Unido | GB | ✅ Disponible |
| 🇺🇸 EE. UU. | US | ✅ Disponible |

> 💡 **INSIGHT:** Implementar `MemberProgram` en mercados no soportados es una **ineficiencia de recursos**.

### Protocolo de Implementación de Confianza

#### Paso 1: Entidad Raíz

Crear el bloque `Organization` en la página de contacto o "Sobre nosotros".

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "@id": "https://ejemplo.com/#organization",
  "name": "Mi Tienda Online",
  "url": "https://ejemplo.com",
  "logo": "https://ejemplo.com/logo.png"
}
```

#### Paso 2: Anidación de Políticas

Utilizar `hasMerchantReturnPolicy` para definir plazos y condiciones de devolución.

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "@id": "https://ejemplo.com/#organization",
  "name": "Mi Tienda Online",
  "hasMerchantReturnPolicy": {
    "@type": "MerchantReturnPolicy",
    "applicableCountry": "MX",
    "returnPolicyCategory": "https://schema.org/MerchantReturnFiniteReturnWindow",
    "merchantReturnDays": 30,
    "returnMethod": "https://schema.org/ReturnByMail",
    "returnFees": "https://schema.org/FreeReturn"
  }
}
```

#### Paso 3: Configuración de Membresía

Anidar `MemberProgram` dentro de `Organization` para detallar beneficios de lealtad.

```json
{
  "@context": "https://schema.org",
  "@type": "Organization",
  "@id": "https://ejemplo.com/#organization",
  "name": "Mi Tienda Online",
  "memberProgram": {
    "@type": "MemberProgram",
    "name": "Programa de Fidelidad Premium",
    "hasTier": [
      {
        "@type": "MemberProgramTier",
        "name": "Bronce",
        "hasTierBenefit": [
          {
            "@type": "MemberProgramTierBenefit",
            "name": "Acumulación de Puntos",
            "description": "1 punto por cada dólar gastado"
          }
        ]
      },
      {
        "@type": "MemberProgramTier",
        "name": "Plata",
        "hasTierBenefit": [
          {
            "@type": "MemberProgramTierBenefit",
            "name": "Descuento Plata",
            "description": "5% de descuento en todos los productos"
          }
        ]
      }
    ]
  }
}
```

### Pro-Tip de Precedencia

> 💡 **INSIGHT CRÍTICO:** El marcado actúa como una capa de validación o fallback.

**Jerarquía de Precedencia (de mayor a menor):**

| Prioridad | Fuente de Datos | Descripción |
|-----------|-----------------|-------------|
| **1** | **Content API for Shopping** | Configuración directa en el feed (señal más fuerte) |
| **2** | **Merchant Center o Search Console** | Overrides manuales |
| **3** | **Marcado a nivel de producto (Offer)** | Usado solo para excepciones |
| **4** | **Marcado a nivel de organización (Organization)** | Estándar global (más débil) |

> 🔴 **NOTA TÉCNICA:** Si existe configuración en Merchant Center o Search Console, Google dará prioridad a estas plataformas sobre el marcado del sitio. La consistencia entre ambas fuentes es obligatoria.

---

## 5. 🔍 Protocolo de Implementación, Validación y Mantenimiento

### El Riesgo de una Implementación Deficiente

> ⚠️ **ADVERTENCIA CRÍTICA:** El riesgo de una implementación deficiente no es la pérdida de ranking (Google afirma que las acciones manuales por spam en datos estructurados no afectan el ranking general en la búsqueda web), sino algo mucho más grave: **la aniquilación total del CTR**.

Perder la elegibilidad para resultados enriquecidos en categorías competitivas es, a efectos prácticos, una **sentencia de muerte** para el tráfico orgánico del ecommerce.

### Herramientas de Validación

#### 1. Rich Results Test

**URL:** https://search.google.com/test/rich-results

**Propósito:** Validación técnica de la sintaxis JSON-LD

**Qué verificar:**
- ✅ Sintaxis JSON-LD válida
- ✅ Propiedades requeridas presentes
- ✅ URLs absolutas y accesibles
- ✅ Valores correctos para enumeraciones
- ✅ Previsualización del rich result

#### 2. URL Inspection Tool

**URL:** https://search.google.com/search-console

**Propósito:** Verificación en tiempo real de cómo Googlebot renderiza el marcado

**Por qué es esencial:**
- ✅ Googlebot puede ejecutar JavaScript
- ✅ El marcado dinámico debe ser accesible después del render
- ✅ Detecta problemas de renderizado que los validadores estáticos no ven
- ✅ Crucial para detectar contenido oculto por JS

**Cómo usar:**
1. Ir a Google Search Console
2. Ingresar URL en la barra de inspección
3. Click en "Probar URL en vivo"
4. Revisar "Ver página probada"
5. Verificar que el JSON-LD está presente en el DOM renderizado

### Flujo de Trabajo de Ciclo Cerrado

#### Paso 1: Construcción

**Objetivo:** Modelado de datos JSON-LD según las especificaciones de Schema.org

**Actividades:**
- Definir estructura de datos
- Identificar propiedades requeridas y recomendadas
- Crear templates dinámicos
- Documentar decisiones arquitectónicas

#### Paso 2: Validación de Sintaxis

**Herramienta:** Rich Results Test

**Proceso:**
1. Validar con Rich Results Test
2. Corregir errores críticos
3. Revisar advertencias
4. Validar nuevamente

#### Paso 3: Auditoría de Renderizado

**Herramienta:** URL Inspection Tool

**Proceso:**
1. Inspeccionar URL en Search Console
2. Verificar renderizado de JavaScript
3. Confirmar presencia de JSON-LD
4. Detectar recursos bloqueados

#### Paso 4: Despliegue y Notificación

**Herramienta:** Search Console Sitemap API

**Objetivo:** Automatizar el aviso de cambios en precios o stock, acelerando el re-rastreo

**Implementación:**

```python
import requests

def notify_google_of_changes(url, api_key):
    """Notifica a Google sobre cambios en una página"""
    
    endpoint = "https://www.googleapis.com/indexing/v3/urlNotifications:publish"
    
    payload = {
        "url": url,
        "type": "URL_UPDATED"
    }
    
    headers = {
        "Authorization": f"Bearer {api_key}",
        "Content-Type": "application/json"
    }
    
    response = requests.post(endpoint, json=payload, headers=headers)
    
    if response.status_code == 200:
        print(f"✅ Notificación enviada para {url}")
    else:
        print(f"❌ Error: {response.status_code}")
    
    return response.json()

# Ejemplo de uso
notify_google_of_changes(
    "https://ejemplo.com/producto/zapatillas-running-pro",
    "TU_API_KEY"
)
```

#### Paso 5: Monitoreo Preventivo

**Herramienta:** Search Console Reports

**Informes críticos a monitorear:**

**1. Unparsable Structured Data Report**
```
Ruta: Search Console > Resultados > Datos estructurados no analizables
```
- Detecta errores de sintaxis a nivel de sitio
- Identifica fallos catastróficos que impiden el procesamiento
- Revisar semanalmente

**2. Informe de Acciones Manuales**
```
Ruta: Search Console > Seguridad y acciones manuales > Acciones manuales
```
- Detecta sanciones por violaciones de política
- Requiere acción inmediata
- Proceso de recuperación documentado

**3. Informe de Resultados Enriquecidos**
```
Ruta: Search Console > Mejoras > [Tipo de schema]
```
- Monitorea páginas válidas con schema
- Identifica errores y advertencias
- Muestra tendencias históricas

### Diagnóstico de Errores Comunes

#### ❌ Problema 1: Contenido Oculto

**Descripción:** Datos en JSON-LD que no son visibles para el usuario en el HTML.

**Ejemplo de violación:**
```
HTML Visible: Página de producto con información básica
Schema JSON-LD: Incluye reseñas que no están visibles en la página

❌ VIOLACIÓN GRAVE: Contenido oculto marcado
```

**Solución:**
- Asegurar que todo el contenido marcado sea visible
- Sincronizar schema con contenido visible
- Eliminar marcado de contenido oculto

#### ❌ Problema 2: Marcado Engañoso

**Descripción:** Discrepancia entre el tipo de esquema y la realidad del negocio.

**Ejemplo de violación:**
```
HTML Visible: Tienda de ropa
Schema JSON-LD: Restaurant

❌ VIOLACIÓN: Tipo de schema incorrecto
```

**Solución:**
- Usar el tipo de schema más específico y preciso
- Asegurar que el schema refleje el negocio real
- Validar con Rich Results Test

#### ❌ Problema 3: Datos Desactualizados

**Descripción:** Precios o disponibilidades que no coinciden con la oferta real.

**Ejemplo de violación:**
```
HTML Visible: <span class="price">$99.99</span>
Schema JSON-LD: "price": "89.99"

❌ VIOLACIÓN: Discrepancia de precio
```

**Solución:**
- Sincronizar precios en tiempo real
- Actualizar disponibilidad inmediatamente
- Implementar validación automática

### Acciones Manuales: Entendiendo el Impacto

> 💡 **INSIGHT CRÍTICO:** Una Acción Manual por datos estructurados inhabilita la elegibilidad para resultados enriquecidos, pero —a diferencia de otras penalizaciones— **no afecta directamente el ranking orgánico en la búsqueda web general**.

**Impacto de una Acción Manual:**

| Área | Impacto |
|------|---------|
| **Rich Results** | ❌ Eliminados completamente |
| **CTR** | ❌ Aniquilación total |
| **Ranking Orgánico** | ✅ No afectado directamente |
| **Tráfico General** | ⚠️ Reducción indirecta severa |

**Proceso de Recuperación:**

```
1. Identificar la infracción específica
   ↓
2. Corregir todo el marcado problemático
   ↓
3. Documentar los cambios realizados
   ↓
4. Solicitar revisión en Search Console
   ↓
5. Esperar respuesta de Google (2-4 semanas)
```

### Script de Validación Automática

```python
import requests
import json
from datetime import datetime
from bs4 import BeautifulSoup

def validate_product_schema(url):
    """Valida schema de una página de producto"""
    
    try:
        # Obtener HTML de la página
        response = requests.get(url, timeout=10)
        soup = BeautifulSoup(response.text, 'html.parser')
        
        # Extraer JSON-LD
        scripts = soup.find_all('script', type='application/ld+json')
        
        errors = []
        warnings = []
        
        for script in scripts:
            try:
                schema = json.loads(script.string)
                
                # Validar Product Schema
                if schema.get('@type') == 'Product':
                    # Verificar propiedades requeridas
                    if 'name' not in schema:
                        errors.append("Missing 'name' in Product Schema")
                    if 'image' not in schema:
                        errors.append("Missing 'image' in Product Schema")
                    if 'offers' not in schema:
                        errors.append("Missing 'offers' in Product Schema")
                    else:
                        if 'price' not in schema['offers']:
                            errors.append("Missing 'price' in Offer Schema")
                        if 'priceCurrency' not in schema['offers']:
                            errors.append("Missing 'priceCurrency' in Offer Schema")
                        if 'availability' not in schema['offers']:
                            errors.append("Missing 'availability' in Offer Schema")
                    
                    # Verificar propiedades recomendadas
                    if 'brand' not in schema:
                        warnings.append("Missing 'brand' (recommended)")
                    if 'aggregateRating' not in schema:
                        warnings.append("Missing 'aggregateRating' (recommended)")
                        
            except json.JSONDecodeError:
                errors.append(f"Invalid JSON in schema")
        
        return {
            'url': url,
            'timestamp': datetime.now().isoformat(),
            'valid': len(errors) == 0,
            'errors': errors,
            'warnings': warnings
        }
    
    except Exception as e:
        return {
            'url': url,
            'timestamp': datetime.now().isoformat(),
            'valid': False,
            'errors': [f"Request failed: {str(e)}"],
            'warnings': []
        }

# Validar múltiples URLs
pages = [
    'https://ejemplo.com/producto/zapatillas-running-pro',
    'https://ejemplo.com/producto/camiseta-premium',
    'https://ejemplo.com/producto/laptop-gaming'
]

results = [validate_product_schema(page) for page in pages]

# Generar reporte
print("=" * 60)
print("REPORTE DE VALIDACIÓN DE SCHEMA")
print("=" * 60)

for result in results:
    print(f"\n{result['url']}")
    print(f"Timestamp: {result['timestamp']}")
    
    if result['valid']:
        print("✅ VÁLIDO")
    else:
        print("❌ INVÁLIDO")
        print("\nErrores:")
        for error in result['errors']:
            print(f"  - {error}")
    
    if result['warnings']:
        print("\nAdvertencias:")
        for warning in result['warnings']:
            print(f"  ⚠️ {warning}")

print("\n" + "=" * 60)
```

### Integración con CI/CD

```yaml
# .github/workflows/schema-validation.yml
name: Schema Validation

on:
  push:
    branches: [main]
  schedule:
    - cron: '0 0 * * 1'  # Cada lunes a medianoche

jobs:
  validate-schema:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v3
      
      - name: Set up Python
        uses: actions/setup-python@v4
        with:
          python-version: '3.10'
      
      - name: Install dependencies
        run: |
          pip install requests beautifulsoup4
      
      - name: Validate schema
        run: python validate_schema.py
      
      - name: Notify on errors
        if: failure()
        uses: actions/github-script@v6
        with:
          script: |
            github.rest.issues.create({
              owner: context.repo.owner,
              repo: context.repo.repo,
              title: '🚨 Schema Validation Errors Detected',
              body: 'Errors were found in schema markup during automated validation. Please review and fix immediately.',
              labels: ['bug', 'seo', 'priority-high']
            })
      
      - name: Upload validation report
        if: always()
        uses: actions/upload-artifact@v3
        with:
          name: schema-validation-report
          path: validation-report.json
```

### Métricas Clave de Monitoreo

| Métrica | Objetivo | Frecuencia | Acción si no se cumple |
|---------|----------|------------|------------------------|
| **Páginas válidas** | 100% de páginas de producto | Semanal | Corregir errores inmediatamente |
| **Errores críticos** | 0 errores | Diario | Priorizar corrección en <48 horas |
| **Advertencias** | <5% de páginas | Semanal | Corregir en siguiente sprint |
| **CTR de rich results** | Aumento del 15% en 3 meses | Mensual | Optimizar schema y contenido |
| **Impresiones** | Aumento del 20% en 3 meses | Mensual | Expandir cobertura de schema |

### Checklist de Mantenimiento Continuo

#### Diario
- [ ] Revisar alertas de Search Console
- [ ] Verificar que no haya nuevos errores críticos
- [ ] Monitorear acciones manuales

#### Semanal
- [ ] Revisar informe de "Resultados Enriquecidos"
- [ ] Validar páginas clave con Rich Results Test
- [ ] Verificar consistencia de precios y disponibilidad
- [ ] Inspeccionar URLs críticas con URL Inspection Tool

#### Mensual
- [ ] Auditoría completa con Screaming Frog
- [ ] Revisión manual de cumplimiento de políticas
- [ ] Actualización de sitemaps
- [ ] Análisis de tendencias y métricas
- [ ] Revisión de informe de datos no analizables

#### Trimestral
- [ ] Revisión de estrategia de schema
- [ ] Análisis competitivo
- [ ] Actualización de documentación interna
- [ ] Capacitación del equipo en nuevas directrices
- [ ] Evaluación de ROI de rich results

---

## 📋 Checklist Final de Arquitectura (Senior SEO)

### ✅ Acceso Total
- [ ] Verificar en `robots.txt` que Googlebot no tenga restricciones en las páginas con marcado
- [ ] Confirmar que no hay etiquetas `noindex` en páginas con schema
- [ ] Validar que no hay muros de autenticación que bloqueen el rastreo

### ✅ Consistencia Absoluta
- [ ] Validar que los datos en el JSON-LD coincidan exactamente con lo que el usuario ve en la interfaz (UI)
- [ ] Verificar que precios, disponibilidad y nombres sean idénticos
- [ ] Confirmar que no hay contenido oculto marcado
- [ ] Discrepancias aquí disparan acciones manuales

### ✅ Duplicación de Parent Data
- [ ] En sitios multi-página, confirmar que el ProductGroup está presente en cada URL de variante
- [ ] Verificar que `productGroupID` sea consistente en todas las variantes
- [ ] Asegurar que `variesBy` esté completo en cada página

### ✅ URLs de Schema
- [ ] Asegurar que `variesBy` use URLs completas (ej. `https://schema.org/material`)
- [ ] Validar que todas las URLs en el schema sean absolutas
- [ ] Confirmar que las URLs sean accesibles públicamente

### ✅ Validación de @id
- [ ] Confirmar que todos los elementos de BreadcrumbList tengan un `@id` vinculado a la entidad real
- [ ] Verificar que los `@id` sean únicos y consistentes
- [ ] Validar la vinculación semántica con `@graph`

### ✅ Envío vía API
- [ ] Utilizar la API de Search Console para enviar sitemaps tras cambios masivos en el catálogo
- [ ] Notificar cambios críticos en precios y disponibilidad
- [ ] Automatizar el proceso de notificación

### ✅ Monitoreo Continuo
- [ ] Revisar Search Console diariamente
- [ ] Validar con Rich Results Test después de cada cambio
- [ ] Auditoría mensual con Screaming Frog
- [ ] Monitoreo de métricas de rich results

---

## 🎯 Conclusión: Dominio Visual de tu Categoría

### Los Datos Estructurados como Proceso Iterativo

Los datos estructurados son un **proceso iterativo de mejora continua**; su optimización constante es lo que garantiza que un ecommerce no solo sea indexado, sino que **domine visualmente su categoría**.

### Los Tres Pilares del Éxito

#### 1. Jerarquía Clara
- ✅ BreadcrumbList con URLs absolutas
- ✅ Uso de `@id` para vinculación semántica
- ✅ Coincidencia con navegación visible
- ✅ Profundidad taxonómica bien definida

#### 2. Inteligencia de Producto
- ✅ ProductGroup para consolidar variantes
- ✅ URLs completas de Schema.org en `variesBy`
- ✅ Identificadores únicos (SKU/GTIN) por variante
- ✅ Duplicación en sitios multipágina

#### 3. Confianza y GEO
- ✅ Organization como entidad raíz
- ✅ MerchantReturnPolicy con señales locales
- ✅ MemberProgram (solo en países soportados)
- ✅ Consistencia con Merchant Center

### El Impacto en el Negocio

| Área de Impacto | Beneficio Esperado |
|-----------------|-------------------|
| **Visibilidad** | Dominio del espacio visual en SERPs |
| **CTR** | Incremento del 20-35% en tasas de clics |
| **Autoridad** | Topic Authority consolidada |
| **Comprensión** | Google entiende tu catálogo completamente |
| **GEO** | Citaciones en motores de IA |
| **Conversión** | Reducción de fricción en el embudo |

### Llamado a la Acción

> **Implementa esta arquitectura hoy.** Cada día que pasa sin datos estructurados correctos es una oportunidad perdida de dominar visualmente tu categoría en las SERPs.

**Tu competencia ya está implementando estas estrategias. La pregunta no es si deberías hacerlo, sino qué tan rápido puedes comenzar.**

**Acciones inmediatas:**
1. Audita tu jerarquía actual con Rich Results Test
2. Implementa BreadcrumbList con `@id` en todas las páginas
3. Configura ProductGroup para tus variantes
4. Valida y monitorea continuamente
5. Optimiza para GEO con señales de confianza

**El dominio visual de tu categoría comienza con una jerarquía semántica impecable.**

---

## 💡 Resumen Ejecutivo

### Puntos Clave de la Guía

1. **JSON-LD es el estándar:** Formato recomendado por Google para implementaciones modernas
2. **BreadcrumbList es fundamental:** Comunica la jerarquía del sitio a Google
3. **@id es innegociable:** Vincula elementos con entidades del Knowledge Graph
4. **ProductGroup consolida variantes:** Evita fragmentación de autoridad
5. **URLs completas de Schema.org:** Obligatorias en `variesBy`
6. **GEO requiere señales locales:** applicableCountry, MemberProgram (restringido geográficamente)
7. **Consistencia es crítica:** Schema debe coincidir con contenido visible
8. **Validación continua:** Rich Results Test + URL Inspection Tool + Search Console

### Checklist Final de Implementación

#### Fundamentos
- [ ] JSON-LD implementado en todas las páginas
- [ ] Googlebot tiene acceso total (sin bloqueos)
- [ ] Marcado coincide con contenido visible
- [ ] Todas las propiedades requeridas presentes

#### Jerarquía
- [ ] BreadcrumbList en todas las páginas
- [ ] URLs absolutas en todos los elementos
- [ ] Secuencia lógica desde posición 1
- [ ] Uso de `@id` para vinculación semántica
- [ ] Coincidencia con breadcrumbs visibles

#### Productos y Variantes
- [ ] ProductGroup para consolidar variantes
- [ ] `productGroupID` consistente
- [ ] `variesBy` con URLs completas de Schema.org
- [ ] Identificadores únicos (SKU/GTIN) por variante
- [ ] Duplicación en sitios multipágina

#### Confianza y GEO
- [ ] Organization como entidad raíz
- [ ] MerchantReturnPolicy implementado
- [ ] MemberProgram (solo en países soportados)
- [ ] Consistencia con Merchant Center

#### Validación y Mantenimiento
- [ ] Validación con Rich Results Test
- [ ] Inspección de URLs con URL Inspection Tool
- [ ] Monitoreo en Search Console
- [ ] Auditoría mensual con Screaming Frog
- [ ] Scripts de validación automática
- [ ] Proceso de notificación de cambios vía API

---

*Guía Tutorial: Optimización de Datos Estructurados para Jerarquía y Visibilidad en Ecommerce (SEO & GEO) - Julio 2026*

*Arquitectura Técnica para Dominio Visual en SERPs*