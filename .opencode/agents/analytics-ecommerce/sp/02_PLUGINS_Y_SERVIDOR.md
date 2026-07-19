# Plugins y Configuración de Servidor — Suplementos Panamá

## Stack Técnico

- **Hosting:** SiteGround
- **Servidor:** SSH `ssh.suplementospanama.net:18765`
- **Usuario SSH:** `u1910-kbd9lgn9dh44`
- **Llave:** `ssh-key-nopass` (sin contraseña)
- **WordPress:** `public_html` en `/home/customer/www/suplementospanama.net/public_html`
- **WP-CLI:** 2.12.0 disponible en servidor

## Plugins Activos Relevantes

| Plugin | Versión | Rol en Analytics |
|--------|---------|------------------|
| gtm-kit | 2.16.4 | Contenedor GTM `GTM-PR4ZSMC7` |
| seo-by-rank-math | 1.0.274.1 | GSC data, Schema, SEO |
| google-listings-and-ads | 3.7.3 | Google Merchant Center |
| pixelyoursite | 11.2.1 | Meta Pixel |
| facebook-for-woocommerce | 3.7.5 | Catálogo Facebook |
| jetpack | 16.0.1 | Stats básicas |

## Plugins Must-Use (MU)

| Plugin | Función |
|--------|---------|
| branch-manager-menu | Menú por sucursal |
| combo-debug | Debug de combos |
| combo-fix-reservestock | Fix stock en combos |
| combo-price | Precios de combos |
| rankmath-itemlist | Schema itemList |
| sp-global-product-schema | Schema global de producto |

## Comandos Útiles WP-CLI

```bash
# Ruta base
--path=/home/customer/www/suplementospanama.net/public_html

# Listar plugins
wp plugin list --path=/home/customer/www/suplementospanama.net/public_html

# Buscar posts
wp post list --post_type=post --s="keyword" --fields=ID,post_title --format=csv --path=/home/customer/www/suplementospanama.net/public_html

# Gestionar meta
wp post meta set {post_id} {meta_key} '{meta_value}' --path=/home/customer/www/suplementospanama.net/public_html
wp post meta get {post_id} {meta_key} --path=/home/customer/www/suplementospanama.net/public_html
```

## Funciones PHP Custom

Las funciones de tracking y personalización viven en:
- **Local:** `C:\suplementos\stock-suplementos\local\functions_current.php`
- **Servidor:** `/home/customer/www/suplementospanama.net/public_html/wp-content/themes/nutritix-child/functions.php`
- Se sincroniza vía SCP
