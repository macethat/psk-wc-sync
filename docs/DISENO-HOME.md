# Plan: Rediseño del Home de Suplementos Panamá — Vitrina de Ventas

> Estado: Plan v1 · Fecha: 2026-09-26

## 1. Objetivo
Convertir el **body del home** en una **vitrina de ventas** que:
- Exhiba el inventario de la tienda (proteínas, creatina, pre-entrenos, aminoácidos, etc.).
- **Promocione los combos** (clave: solo se venden a través del website).
- Destaque productos con **descuento web-only** (se indicarán cuáles).
- Maximice conversión con CTAs claros, prueba social y beneficios.

**Se mantienen intactos:** esquema de colores, fuentes de texto, **header y footer** del tema.

## 2. Restricciones técnicas
- **Libertad de diseño**: preferir **código/funciones propias** (HTML/PHP/JS en el child theme `nutritix-child` o template a medida). Se puede prescindir de Elementor para el body si conviene.
- **Velocidad**: no sacrificar rendimiento. Objetivo: render fresco < ~3 s, TTFB caché < 0.1 s, sin añadir plugins pesados.
- Respetar la funcionalidad protegida (retiro en sucursal, combos, schema) — ver `AGENTS.md`.

## 3. Recursos de marca (mantener)
- **Colores** (CSS vars del tema):
  - Primario: `#E20613` (rojo) · Texto: `#464646` · Texto claro: `#888888`
  - Acento: `#151515` · Fondo claro: `#F4F4F4` · Borde: `#E8E8E8`
- **Fuentes**: Plus Jakarta Sans (headings, "Nutritix-Heading") · Roboto / Open Sans (body).
- **Contacto/CTA**: WhatsApp `wa.me/50760153257` · sitio `suplementospanama.net`.

## 4. Insumos a recolectar (pendientes)
- [ ] Lista de **combos activos** con `_combo_price` y ahorro (se obtiene de WooCommerce).
- [ ] Lista de **productos con descuento web-only** (el cliente indicará cuáles).
- [ ] **Imágenes** para hero/combo/productos (diseñador gráfico).
- [ ] Copy y alt text (apoyo de `marketing-descriptions`).

## 5. Estructura propuesta del body (orientada a ventas)
1. **Hero / banner promocional** (combos del mes, CTA a `/promociones/combos/`).
2. **Barra de confianza**: envío gratis desde $150, retiro en 6 sucursales, asesoría por WhatsApp, original garantizado.
3. **Vitrina de categorías** (tarjetas clicables a las categorías principales).
4. **Combos destacados** (solo web) — cards con precio combo + ahorro vs. compra individual.
5. **Productos con descuento web-only** — cards con badge "Precio web" y precio regular tachado.
6. **Productos destacados / más vendidos** (carrusel o grilla liviana).
7. **Prueba social** (reseñas/valoraciones destacadas).
8. **CTA final WhatsApp** + FAQ corto (opcional).

## 6. Proceso de diseño
1. **Generar opciones** de diseño con **Stitch** (brief de marketing/ventas + marca SP).
2. **Elegir** 1-2 opciones con el cliente.
3. **Diseñador gráfico** produce imágenes/alt (con detalle de `marketing-descriptions`).
4. **Codificar** el diseño elegido (child theme, funciones propias, render liviano).
5. **Verificar**: home 200, TTFB caché < 0.1 s, primera carga < 3 s, checkout/carrito OK, schema intacto.

## 7. Agentes/herramientas
- **Stitch**: generación de opciones de diseño (HTML/React).
- **marketing-descriptions**: copy de ventas y alt text para el diseñador.
- **Nosotros**: codificación final en el child theme.

## 8. Entregables
- Opciones de diseño generadas (Stitch).
- Diseño final codificado en `nutritix-child`.
- Documentación del rediseño y reversión.

---

## 9. Mejoras acordadas (a implementar en fase 2)

### 9.1 Slider automático en el HERO de combos (sección 1)
- Reemplazar el showcase estático del hero por un **slideshow automático** que rote varios combos (imagen + ahorro + precio combo).
- **Sin flechas**: avance automático cada ~4-5 s, pausa en hover, indicador de progreso (dots/barras), IntersectionObserver para detener fuera de viewport.
- Cada slide **enlazable** a la ficha del combo.
- **Transición sugerida:** fade + zoom suave (Ken Burns) con cambio de texto/CTA. Alternativas: slide horizontal, parallax, blur-in.
- Técnica: JS nativo (sin librerías) + transiciones CSS (GPU). <1 KB.

### 9.2 Nueva sección: videos verticales 9:16 de marcas
- Fila con **3-4 videos verticales (proporción 9:16)**, **sin sonido**, para reforzar presencia de marcas e identificación con Suplementos Panamá.
- **Selección de marcas (confirmada):**
  - **Top (consolidar):** EVOGEN, MUTANT
  - **Refuerzo:** RAW Nutrition, LANDERFIT
- Alternativas de refuerzo si cambia la prioridad: Optimum Nutrition (referente global), Dymatize (ISO 100).

### 9.3 Dimensiones de imágenes (nota técnica)
- Las imágenes generadas por Stitch (`lh3.googleusercontent.com/aida-public/...`) admiten **parámetros de tamaño** en la URL:
  - original = 512×279 px (solo para preview).
  - `=w800` → 800×436 px (cards de combos/productos, ~55 KB).
  - `=w1200` → 1200×655 px.
  - `=w1600` → **1408×768 px (máximo disponible, para el hero)**.
- **No se pixela en pantallas grandes** usando `=w1600` en el hero y `=w800` en las cards.
- Nota: al codificar, reemplazar estas imágenes AI por las **imágenes reales de los productos/combos** (subidas en WordPress) con `srcset` para retina.
