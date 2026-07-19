## 2. AUDITORÍA PROCEDIMENTAL DE COMPORTAMIENTO Y ENGAGEMENT

### Contexto Estratégico
La métrica de "rebote" ha sido sustituida por el "engagement". Según **Bounteous**, el éxito ya no se mide por la ausencia de acción, sino por la profundidad de la interacción.

---

### Rastreo Automatizado (Enhanced Measurement)

**Mandato de Ejecución para el Agente:**  
Debe auditar la activación de los eventos de medición mejorada (`scroll`, `click`, `file_download`, `view_search_results`) utilizando **DebugView** o **Tag Assistant**. 

**Verificaciones obligatorias:**
- ✅ El scroll debe dispararse **exclusivamente al 90% de profundidad**.
- ✅ Los clics de salida deben capturar la **URL de destino correcta**.

---

### Taxonomía Semántica de Negocio

Implemente los eventos estandarizados de Google para evitar la fragmentación de informes:

| Vertical | Eventos Recomendados | Parámetros / Dimensiones Personalizadas |
| :--- | :--- | :--- |
| **Retail** | `view_item`, `purchase`, `add_to_cart` | `item_category`, `transaction_id` |
| **Media** | `select_content`, `share` | `author`, `article_section`, `word_count` |
| **Leads** | `generate_lead`, `view_promotion` | `lead_source`, `form_id` |
| **Travel** | `view_item_list`, `select_item` | `destination`, `origin`, `travel_class` |

---

### Análisis de Atribución y Rutas

Establezca el modelo **Data-Driven** como predeterminado en la configuración de la propiedad.

**Instrucción Técnica:**  
Ejecute informes de **Path Exploration** (Exploración de rutas) en el módulo de Exploraciones de GA4 para mapear los cuellos de botella de conversión. Siga la metodología de **MeasureSchool** para identificar loops de navegación infinitos que degradan la experiencia.