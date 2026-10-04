# Guía: Google Merchant Center por Content API for Shopping

Sincroniza el catálogo de WooCommerce a GMC de forma **automática** (cron diario), sin subir archivos ni depender de GLA.

## 1) Google Cloud (proyecto `suplementos-panama-gbp` = `531692178989`)
1. **APIs & Services → Library** → busca **"Content API for Shopping"** → **Enable/Habilitar**.
2. **IAM & Admin → Service Accounts** → **Create service account**:
   - Nombre: `gmc-content-sync` (el ID da un email tipo `gmc-content-sync@suplementos-panama-gbp.iam.gserviceaccount.com`).
3. Entra al service account → **Keys → Add key → Create new key → JSON** → descarga el archivo.

## 2) Merchant Center
1. `merchants.google.com` → cuenta **5825178723**.
2. **Configuración → Acceso de usuarios → Agregar usuario**:
   - Email: el del **service account** (`...@suplementos-panama-gbp.iam.gserviceaccount.com`).
   - Acceso: **Administrador** (o al menos "Estándar").

## 3) Servidor (SiteGround)
1. Sube el JSON **fuera del web root**, por ejemplo:
   ```
   scp -P 18765 -i ssh-key-nopass "ruta/gmc-content-sync-XXXX.json" \
       u1910-kbd9lgn9dh44@ssh.suplementospanama.net:/home/customer/sp-gcp-content-sa.json
   ```
2. En `wp-config.php` agrega:
   ```php
   define('SP_GC_MERCHANT_ID', '5825178723');
   define('SP_GC_SA_PATH', '/home/customer/sp-gcp-content-sa.json');
   ```

## 4) Ejecutar / activar
```bash
# prueba sin escribir nada
wp sp-gc-sync --dry-run --limit=20

# push real
wp sp-gc-sync

# activar el cron diario
wp option update sp_gc_sync_enabled 1
```
El mu-plugin es `wp-content/mu-plugins/sp-google-content-sync.php` (copia en `local/mu-plugins/`).

## 5) Verificación
- `wp sp-gc-sync` imprime `ok=.. err=..`.
- En GMC: **Productos → Todos los productos** deben aparecer ~350 (productos + variaciones).
- Revisa **Diagnóstico** para avisos.

## Notas
- El envío (gratis desde $150) y los impuestos se configuran en **GMC → Configuración**, no en el push.
- Para actualizar precio/stock basta con volver a correr el sync (insert sobrescribe el mismo `offerId`).
- Si hay duplicados con la fuente automática vieja, elimina/oculta esa fuente en GMC.
- El feed XML (`/google-feed.xml/`) queda como respaldo/manual por si se prefiere subir archivo.
