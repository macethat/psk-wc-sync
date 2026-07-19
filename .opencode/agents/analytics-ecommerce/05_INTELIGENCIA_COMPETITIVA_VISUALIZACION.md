## 5. INTELIGENCIA COMPETITIVA Y AUTOMATIZACIÓN DE VISUALIZACIÓN

### Contexto Estratégico
Los datos de GA4 no pueden vivir en un vacío. Deben contrastarse con el mercado para identificar el **"Content Gap"** o brecha de contenido.

---

### Ingesta de Datos Externos
Utilice herramientas como **Semrush** o **Ahrefs** para realizar ingeniería inversa (metodología *Neil Patel*) de las palabras clave de los competidores. Identifique qué términos están capturando su tráfico de conversión y cruce esta información con sus propios datos de conversión en GA4.

---

### Modelado de Visualización en Looker Studio
Mapee los eventos certificados de GA4 hacia **Looker Studio** utilizando el conector oficial.

- ⚠️ **Advertencia Técnica de Ingeniería de Datos:** Sea consciente de los **límites de cuota (API Quotas)** de GA4 en Looker Studio. Para evitar errores de visualización, considere el uso de **BigQuery** como capa intermedia para conjuntos de datos de alto volumen.

- 📊 **Panel Unificado:** Integre datos internos de GA4 con estimaciones externas de mercado vía **Supermetrics** para visualizar la cuota de voz (*Share of Voice*).

---

### Comando Final de Operaciones de Cierre

**Mandato Final:**  
El agente debe ejecutar una auditoría completa de la interfaz de usuario de GA4 basada en las guías de *Analytics Mania*. Esto incluye:

1. ✅ La limpieza de la biblioteca de informes.
2. ✅ La eliminación de métricas estándar que no cumplan con los KPIs definidos.
3. ✅ La verificación de que todos los nodos anteriores (desde Consent Mode hasta el tráfico GEO) se visualicen en cuadros de mando personalizados, interactivos y, sobre todo, **accionables**.