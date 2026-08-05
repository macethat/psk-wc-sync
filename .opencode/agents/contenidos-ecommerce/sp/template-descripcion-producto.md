# Template Oficial de Descripción de Producto

Basado en MASS EXTREME 2500 MUTANT (ID 21391) — estructura aprobada.

## Descripción Corta (Excerpt)

Resumen de 1-2 líneas con datos clave del producto. Template:

> **[NOMBRE PRODUCTO]** es un/a [TIPO PRODUCTO] con [CANTIDAD] de [COMPONENTE PRINCIPAL] y [OTRO COMPONENTE CLAVE]. Ideal para [AUDIENCIA OBJETIVO] que buscan [BENEFICIO PRINCIPAL].

Ejemplo: *Mass Extreme 2500 de MUTANT es un gainer premium con 1,070 kcal, 30g de proteína de 3 fuentes y carbohidratos de alimentos reales como batata y avena. Ideal para hardgainers que buscan volumen muscular sin rellenos.*

## Estructura General

```
[H3 Título de Enganche]
[H2 CARACTERÍSTICAS] (5 bullets sin viñeta, borde inferior gris)
[--- hr ---]
[H2 PERFIL NUTRICIONAL] (tarjetas responsivas 2-línea, sin tabla HTML)
[--- hr ---]
[H2 MODO DE USO]
[--- hr ---]
[H2 ¿PARA QUIÉN ES?] (4 bullets)
[--- hr ---]
[H2 LO QUE NO CONTIENE] (opcional, investigar info)
[--- hr ---]
[H2 PREGUNTAS FRECUENTES] (máx 3-4)
[--- hr ---]
[H2 STACK RECOMENDADO] (3 productos reales de la tienda con enlaces)
[--- hr ---]
[AUTORIDAD Y CONFIANZA] (div bg #f5f5f5, 4 bullets)
[ADVERTENCIA LEGAL] (p bg #fcfcfc, border #eee)
[--- espacio ---]
[CTA WHATSAPP] (div bg #f7f7f7, SVG 26x26, wa.me/50760153257)
```

## Reglas de Estilo

- **Colores**: texto `#333333`, bordes izquierdos de H2 rojo `#e23636` (5px solid), valores numéricos en perfil nutricional `#e23636`
- **Sin emojis** en ninguna parte
- **H2** en `#333333`, `font-size: 22px`, `font-weight: bold`, `border-left: 5px solid #e23636`, `padding-left: 15px`
- **H3** primer título en `#333333`, `font-size: 26px`, `margin-bottom: 8px`
- **Párrafo intro** `font-size: 16px`, `margin-bottom: 20px`
- **hr**: `border: 1px solid #ddd`, `margin: 25px 0`
- **Características**: `<ul style="list-style: none; padding: 0; margin-bottom: 25px;">` cada `<li style="padding: 10px 0; border-bottom: 1px solid #eee; font-size: 15px;">`
- **Sin tablas HTML** para perfil nutricional — usar tarjetas div responsivas
- **ACENTOS Y ORTOGRAFÍA --- REGLA ESTRICTA**: todo el texto debe usar vocales acentuadas correctas en español (á, é, í, ó, ú). NO usar vocales sin acento donde corresponde. Ejemplos obligatorios: `proteína` (no `proteina`), `absorción` (no `absorcion`), `recuperación` (no `recuperacion`), `suplementación` (no `suplementacion`), `información` (no `informacion`), `nutrición` (no `nutricion`), `después` (no `despues`), `también` (no `tambien`), `según` (no `segun`), `rápido/rápida` (no `rapido/rapida`), `más` (no `mas`), `músculo/músculos` (no `musculo/musculos`), `Panamá` (no `Panama`), `años` (no `anos`), `científico` (no `cientifico`), `dietético` (no `dietetico`), `farmacológico` (no `farmacologico`), `técnica` (no `tecnica`), `práctica` (no `practica`), `médico` (no `medico`), `línea` (no `linea`), `específico` (no `especifico`), `cafeína` (no `cafeina`), `combinación` (no `combinacion`).
- **Stack Recomendado**: buscar productos existentes en la tienda, link rojo `#e23636` sin subrayado

## Perfil Nutricional (tarjetas 2-línea)

```html
<h2 style="color: #333333; font-size: 22px; font-weight: bold; margin-bottom: 15px; border-left: 5px solid #e23636; padding-left: 15px;">PERFIL NUTRICIONAL POR SERVICIO (Xg / Y Scoops)</h2>
<div style="display: flex; flex-direction: column; gap: 8px; margin-bottom: 25px;">
  <!-- fila impar bg #f9f9f9, par bg #fff -->
  <div style="background: #f9f9f9; border-radius: 6px; padding: 10px 14px;">
    <div style="display: flex; justify-content: space-between; align-items: baseline; font-size: 14px;">
      <span style="font-weight: bold;">Nutriente</span>
      <span style="font-weight: 800; color: #e23636; white-space: nowrap;">Valor</span>
    </div>
    <div style="font-size: 12px; color: #555; line-height: 1.4; margin-top: 2px;">Descripción breve</div>
  </div>
</div>
```

Sin borde rojo en filas, sin tabla HTML.

## Advertencia Legal (fija)

```html
<p style="font-size: 12px; color: #888; text-align: justify; margin-bottom: 20px; padding: 15px; background: #fcfcfc; border: 1px solid #eee; border-radius: 5px;"><strong> ADVERTENCIA LEGAL:</strong> Este producto es un suplemento dietético. No está destinado a diagnosticar, tratar, curar o prevenir ninguna enfermedad. No excedas la dosis recomendada. Consulta a un profesional de la salud antes de iniciar cualquier programa de suplementación, especialmente si estás embarazada, en lactancia, padeces alguna condición médica o estás bajo tratamiento farmacológico. Los resultados pueden variar según el individuo, su entrenamiento, dieta y metabolismo. No sustituye una alimentación variada y equilibrada.</p>
```

## CTA WhatsApp (fijo)

**IMPORTANTE:** El bloque completo debe incluir SIEMPRE el SVG del icono de WhatsApp (path con `d="M17.472..."`) y el link `wa.me/50760153257`. NO reemplazar el SVG por `&nbsp;` ni por texto plano. El botón de WhatsApp debe ser un `<a>` con el SVG dentro, visible y funcional.

```html
<div style="margin-top: 20px; padding: 15px; background-color: #f7f7f7; border-radius: 10px; text-align: center;">
<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 10px;">
  <span style="color: #333333; text-align: center;">Solicita más información sobre <strong>NOMBRE DEL PRODUCTO</strong> con uno de nuestros asesores expertos</span>
  <a href="https://wa.me/50760153257?text=Hola%2C%20quiero%20informaci%C3%B3n%20sobre%20NOMBRE%20DEL%20PRODUCTO" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; gap: 8px; color: #25D366; text-decoration: none; font-weight: bold; font-size: 15px;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" height="26" fill="#25D366" style="display: block; flex-shrink: 0;">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
  </a>
</div>
</div>
```

## Autoridad y Confianza

```html
<div style="background: #f5f5f5; padding: 20px; border-radius: 8px; margin-bottom: 25px;">
<p style="color: #333333; font-size: 15px; margin-bottom: 10px;"><strong> AUTORIDAD Y CONFIANZA</strong></p>
<ul style="font-size: 15px; margin-bottom: 0;">
  <li>Fabricado bajo estrictos estándares GMP en instalaciones certificadas.</li>
  <li>Testeado por laboratorios independientes para pureza, metales pesados y sustancias prohibidas.</li>
  <li>MARCA es una marca global con presencia en más de 60 países, elegida por atletas de alto rendimiento.</li>
  <li>Lote trazable y etiqueta 100% transparente: cada ingrediente y su dosis están declarados sin mezclas propietarias ocultas.</li>
</ul>
</div>
```

## Flujo de Contenido por Sección

| Sección | Fuente de Datos | Si no hay datos |
|---------|----------------|-----------------|
| H3 Enganche + Párrafo intro | Scrape oficial + traducción | Agente genera basado en spec sheet |
| CARACTERÍSTICAS (5) | Scrape oficial | Agente extrae de datos disponibles |
| PERFIL NUTRICIONAL | Supplement Facts label | Omitir sección |
| MODO DE USO | Scrape / etiqueta | Agente genera dosis estándar del tipo de producto |
| ¿PARA QUIÉN ES? | Agente investiga | Agente genera 4 perfiles de usuario |
| LO QUE NO CONTIENE | Investigar ingredientes dañinos de competidores | Bloquear sección si no hay data confiable |
| PREGUNTAS FRECUENTES | Scrape FAQ oficiales | Agente genera 3-4 preguntas lógicas |
| STACK RECOMENDADO | Agente investiga + busca en stock tienda | 3 productos reales con enlaces a la tienda |
| AUTORIDAD Y CONFIANZA | Info de marca | Personalizar nombre de marca |
| ADVERTENCIA LEGAL | Template fijo | Usar template exacto |
| CTA WhatsApp | Template fijo | Usar template exacto con nombre del producto |
