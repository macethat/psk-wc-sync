# Troubleshooting — GA4

## Problemas Comunes y Soluciones

### 1. No aparecen datos en Tiempo Real

**Causas posibles:**
- GTM container no publicado
- Measurement ID incorrecto
- Ad-blocker bloqueando gtag.js
- Tag no disparada (ver Tag Assistant)

**Solución:**
1. Abrir DebugView en GA4 + Tag Assistant
2. Verificar que el tag GA4 se dispare en la página
3. Confirmar Measurement ID en GTM coincide con el flujo de datos
4. Probar en incógnito (sin extensiones)

### 2. Discrepancia GSC vs GA4

**Es normal** que no coincidan por:
- Latencia de carga del script GA4
- Rechazo de cookies
- Clics múltiples en una misma sesión

Diferencia esperada: GA4 muestra ~15-25% menos sesiones que clics en GSC.

### 3. Tráfico "Direct / Not Set" dominante (>30%)

**Causas:**
- Campañas sin UTM
- Tráfico interno sin excluir
- Redirects/server-side sin tracking

**Solución:**
1. Implementar UTM en todas las campañas
2. Configurar filtro de tráfico interno
3. Usar Measurement Protocol para server-side

### 4. Eventos ecommerce no aparecen

**Causas:**
- dataLayer no empuja correctamente
- GTM no tiene triggers para eventos ecommerce
- Plugins de WooCommerce sobrescriben dataLayer

**Solución:**
1. Ver dataLayer en consola: `dataLayer`
2. Verificar que `ecommerce` objects tengan estructura correcta
3. Usar GTM Preview Mode para depurar

### 5. Error "This does not seem to be a WordPress installation" (WP-CLI)

**Solución:**
Siempre usar `--path=` con la ruta completa al `public_html`:
```bash
wp plugin list --path=/home/customer/www/[dominio]/public_html
```

### 6. GTM container sin datos

**Causa:** Container creado pero sin etiquetas configuradas.

**Solución:**
1. Crear al menos un tag GA4: Event Measurement ID + All Pages trigger
2. Publicar contenedor
3. Verificar con Preview Mode
