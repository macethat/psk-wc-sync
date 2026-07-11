# Historial de Sesiones de Chat

Este archivo registra las sesiones de trabajo con el asistente para preservar contexto entre chats.

---

## 2026-07-11

### Tema
Configuración del repositorio de historial de chat y solución responsive en template de combos.

### Decisiones
- Se crea `docs/historial-chat.md` para registrar sesiones de trabajo
- Se crea `guardar-sesion.ps1` para facilitar el commit/push del historial
- El historial se almacena en el repo `psk-wc-sync` en GitHub

### Plataformas conectadas en el proyecto
1. **PSK Cloud (Premium Soft)** — API REST (inventario, clientes, pedidos)
2. **WooCommerce** — API REST + WP-CLI vía SiteGround SSH
3. **SiteGround** — Servidor SSH para WP-CLI
4. **GitHub** (`macethat/psk-wc-sync`) — Repositorio de código
5. **MyMemory API** — Traducción EN→ES
6. **Apify** — Web scraping
7. **WhatsApp Business** — Enlace wa.me para contacto

### Cambios realizados en grouped.php
- **Problema**: En mobile, los productos hijos del combo solo mostraban nombre + precio, sin imagen
- **Solución**: 
  - Se agregó `get_image(70,70)` clickable dentro del `label` cell
  - Se agregó `.combo-mobile-row` con flex: imagen (70px) a la izquierda + precio a la derecha
  - CSS responsive: ≤768px la tabla se vuelve block, `td.price` se oculta, se muestra la nueva fila
  - ≥769px la fila mobile permanece oculta, desktop intacto
- **Archivo**: `grouped.php` (override en child theme `nutritix-child`)
- **Template del combo**: productos agrupados tipo `grouped` con precio fijo, bypass de stock, variaciones restringidas

### Estado actual
- Script `generar_diferencias.py` tiene error `KeyError: 'Codigo'` por merge mal configurado (pendiente)
- Template `grouped.php` modificado con estructura responsive mobile completada
- Sistema de combos funcional con `combo-price.php` (MU plugin)

### Próximos pasos
- Corregir `generar_diferencias.py`: `left_on='Codigo', right_on='SKU'` y `stock_status` (≤6 = out_of_stock)
- Probar el responsive del template grouped.php en producción
- Agregar entrada de sesión al finalizar cada día de trabajo
