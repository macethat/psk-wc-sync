# Historial de Sesiones de Chat

Este archivo registra las sesiones de trabajo con el asistente para preservar contexto entre chats.
Actualizado por última vez: 2026-07-11

---

## 2026-07-11

### Tema
Configuración del repositorio de historial de chat.

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

### Estado actual
- Script principal: `generar_diferencias.py` (en progreso, error `KeyError: 'Codigo'` por merge mal configurado)
- Tiene skill definido en `.opencode/skills/stock-update/SKILL.md`

### Próximos pasos
- Corregir merge: `left_on='Codigo', right_on='SKU'`
- Corregir lógica de `stock_status`: `out_of_stock` si `Cant.Total <= 6`
- Definir proceso para guardar sesiones diariamente
