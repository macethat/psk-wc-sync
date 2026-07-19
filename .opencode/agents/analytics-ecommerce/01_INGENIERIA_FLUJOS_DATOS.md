# INFORME TÉCNICO Y MANUAL DE OPERACIONES DE CONFIGURACIÓN GA4 

Este documento establece el marco operativo y técnico obligatorio para la implementación, auditoría y optimización de **Google Analytics 4 (GA4)**. Como Director de Ingeniería de Datos, defino que la integridad de la arquitectura de datos es la base de cualquier decisión de negocio; una configuración técnica deficiente no es un error de reporte, es una **fuga de ROI de marketing**. La fiabilidad de los datos es nuestra prioridad absoluta.

---

## 1. INGENIERÍA DE FLUJOS DE DATOS Y CERTIFICACIÓN DE FIABILIDAD

### Contexto Estratégico
La arquitectura de datos debe ser tratada como **infraestructura crítica**. Sin una captura precisa, los modelos de atribución y la optimización de presupuestos carecen de validez. La implementación debe garantizar que cada bit de información sea procesable y veraz.

---

### Arquitectura Basada en Eventos
GA4 opera exclusivamente bajo un **modelo de eventos**, eliminando la jerarquía de sesiones de Universal Analytics.

**Mandato de Ejecución para el Agente:**  
Trate cada interacción como un evento único e independiente. Debe configurar **parámetros personalizados** para cada evento para inyectar contexto semántico, evitando el uso de estructuras genéricas. Utilice la guía de *Google Developers* para validar la estructura del esquema antes de la ingesta.

---

### Manual de Saneamiento y Exclusión
Para erradicar el tráfico "Direct / Not Set", ejecute el siguiente protocolo:

1. **Etiquetado Preventivo:** Valide que todos los puntos de entrada cuenten con parámetros UTM consistentes antes de disparar eventos personalizados.

2. **Exclusión de Tráfico Interno:**
   - Navegue a *Administración > Flujos de datos > Seleccionar Flujo > Configurar ajustes de etiquetas > Definir tráfico interno*.
   - Cree una regla donde el parámetro `traffic_type` tenga el valor `internal` para las IPs de la organización.

3. **Validación:** Verifique en *Ajustes de datos > Filtros de datos* que el filtro "Internal Traffic" esté en modo **"Activo"** tras las pruebas.

---

### Privacidad y Consentimiento (Consent Mode v2)
Es **obligatorio** implementar el Consent Mode v2 para habilitar la modelización de datos en zonas bajo el RGPD.

**Comandos Técnicos de Configuración:**  
Ejecute el comando de consentimiento por defecto antes de cualquier otra carga de etiquetas:

```javascript
gtag('consent', 'default', {
  'ad_storage': 'denied',
  'analytics_storage': 'denied',
  'ad_user_data': 'denied',
  'ad_personalization': 'denied',
  'wait_for_update': 500
});