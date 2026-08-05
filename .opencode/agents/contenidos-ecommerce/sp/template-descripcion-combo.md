# Template Oficial de Descripción de Producto — COMBO (Agrupado)

## Imagen del Combo
- Los combos no tienen código de barras.
- El archivo de imagen va en `\fotos\` con el **nombre del combo** (sin SKU).
- Ejemplo: `\fotos\Proteina 5 lb Impulse + Creatina Super ATP 80 Serv.jpg`
- El script `psk-create-product.py` debe buscar la imagen por nombre del producto cuando el tipo sea combo/grouped.

## Descripción Corta (Excerpt)

Diseñamos para ti este Combo **[PRODUCTO A] + [PRODUCTO B]** —/— que aporta **[BENEFICIO CLAVE AL CONSUMIRLOS JUNTOS]**.

Ejemplo: *Diseñamos para ti este Combo Proteína 5 lb Impulse + Creatina Super ATP 80 Serv que aporta proteína de alta calidad + energía explosiva para maximizar tu fuerza y recuperación.*

## Estructura General

```
[H3 Título de Enganche del Combo]
[--- párrafo de enganche ---]
[--- hr ---]
[H2 CARACTERÍSTICAS] (combinación de características de cada producto, 4-5 bullets)
[--- hr ---]
[H2 MODO DE USO] (modo de uso de cada producto por separado)
[--- hr ---]
[H2 ¿PARA QUIÉN ES?] (perfil del consumidor según los productos)
[--- hr ---]
[H2 PREGUNTAS FRECUENTES] (orientadas a estimular la compra — resultados al combinar)
[--- hr ---]
[AUTORIDAD Y CONFIANZA] (div bg #f5f5f5 — info de Suplementos Panamá)
[ADVERTENCIA LEGAL] (mismo template estándar)
[--- espacio ---]
[CTA WHATSAPP] (mismo template estándar con nombre del combo)
```

## Secciones que NO aplican en combo
- ~~PERFIL NUTRICIONAL~~ → no aplica
- ~~LO QUE NO CONTIENE~~ → no aplica
- ~~STACK RECOMENDADO~~ → no aplica

## Reglas de Estilo

Mismas que el template de producto individual:
- **Colores**: texto `#333333`, bordes izquierdos H2 rojo `#e23636` (5px solid)
- **Sin emojis**
- **H2**: `#333333`, `font-size: 22px`, `font-weight: bold`, `border-left: 5px solid #e23636`, `padding-left: 15px`
- **H3** primer título: `#333333`, `font-size: 26px`, `margin-bottom: 8px`
- **Párrafo intro**: `font-size: 16px`, `margin-bottom: 20px`
- **hr**: `border: 1px solid #ddd`, `margin: 25px 0`
- **Características**: `<ul style="list-style: none; padding: 0; margin-bottom: 25px;">` cada `<li style="padding: 10px 0; border-bottom: 1px solid #eee; font-size: 15px;">`
- **ACENTOS Y ORTOGRAFÍA --- REGLA ESTRICTA**: todo el texto debe usar vocales acentuadas correctas en español (á, é, í, ó, ú). NO usar vocales sin acento donde corresponde. Ejemplos obligatorios: `proteína` (no `proteina`), `absorción` (no `absorcion`), `recuperación` (no `recuperacion`), `suplementación` (no `suplementacion`), `información` (no `informacion`), `nutrición` (no `nutricion`), `después` (no `despues`), `también` (no `tambien`), `según` (no `segun`), `rápido/rápida` (no `rapido/rapida`), `más` (no `mas`), `músculo/músculos` (no `musculo/musculos`), `Panamá` (no `Panama`), `años` (no `anos`), `científico` (no `cientifico`), `dietético` (no `dietetico`), `farmacológico` (no `farmacologico`), `técnica` (no `tecnica`), `práctica` (no `practica`), `médico` (no `medico`), `línea` (no `linea`), `específico` (no `especifico`), `cafeína` (no `cafeina`), `combinación` (no `combinacion`).

## Contenido de Cada Sección

### H3 Enganche + Párrafo intro
Título llamativo enfocado en el beneficio del combo (ej: "LA DUPLA DEFINITIVA PARA MASA MUSCULAR").

Párrafo que explica por qué estos dos productos funcionan mejor juntos. Mencionar sinergia.

### CARACTERÍSTICAS
Combinar 2-3 características de cada producto. Ej:
- **Proteína de Suero de Alta Calidad** – de [PRODUCTO A]: [descripción]
- **Creatina con Transporte Avanzado** – de [PRODUCTO B]: [descripción]
- **Recuperación Acelerada** – la proteína repara fibras mientras la creatina recarga ATP
- **Energía para el Entreno** – [descripción de beneficio combinado]
- **Resultados Más Rápidos** – [descripción de sinergia]

### MODO DE USO
Explicar el modo de uso de cada producto por separado. Ej:
> **[Producto A]:** X scoops en Y ml de agua, [cuándo tomarlo].
> **[Producto B]:** X scoops en Y ml de agua, [cuándo tomarlo].
> **Combinación recomendada:** [Producto A] post-entreno + [Producto B] pre-entreno o con comida.

### ¿PARA QUIÉN ES?
Perfiles de usuario según los productos incluidos. 3-4 bullets.

### PREGUNTAS FRECUENTES
Orientadas a estimular la compra. Enfocarse en:
- Resultados que se obtienen al combinar los productos (upgrade vs comprar por separado)
- Ahorro / valor del combo
- Sinergia entre productos
- Máx 3-4 preguntas con respuestas persuasivas

Ejemplo de pregunta:
> **¿Por qué comprar el combo en lugar de los productos por separado?**
> Porque [beneficio del combo + ahorro + resultados superiores].

### AUTORIDAD Y CONFIANZA (Suplementos Panamá)

```html
<div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
<p style="color: #333333; font-size: 15px; margin-bottom: 10px;"><strong> AUTORIDAD Y CONFIANZA</strong></p>
<ul style="font-size: 15px; margin-bottom: 0;">
  <li>Somos distribuidores autorizados de las marcas más reconocidas en suplementación deportiva.</li>
  <li>Todos nuestros productos son originales, importados directamente y con lote trazable.</li>
  <li>Más de 5 años asesorando a atletas y personas activas en Panamá para alcanzar sus metas fitness.</li>
  <li>Compra 100% segura con envío rápido a todo Panamá y atención personalizada post-venta.</li>
</ul>
</div>
```

### ADVERTENCIA LEGAL
Mismo template estándar del producto individual.

### CTA WhatsApp
Mismo template estándar con el nombre del combo en el texto y la URL.

## Flujo de Contenido

| Sección | Fuente de Datos |
|---------|----------------|
| Enganche + Párrafo | Agente genera basado en los 2 productos del combo |
| CARACTERÍSTICAS | Combinar características de cada producto |
| MODO DE USO | Instrucciones de cada producto individual |
| ¿PARA QUIÉN ES? | Agente genera 3-4 perfiles |
| PREGUNTAS FRECUENTES | Agente genera enfocadas en venta y sinergia |
| AUTORIDAD Y CONFIANZA | Template Suplementos Panamá (fijo) |
| ADVERTENCIA LEGAL | Template estándar (fijo) |
| CTA WhatsApp | Template estándar con nombre del combo |
