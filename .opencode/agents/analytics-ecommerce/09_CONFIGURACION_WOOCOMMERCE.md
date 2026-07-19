# Configuración GA4 para WooCommerce

## Eventos Ecommerce Estándar

WooCommerce + GTM Kit (o GTM manual) deben empujar estos eventos al dataLayer:

### view_item
```javascript
dataLayer.push({ ecommerce: null });
dataLayer.push({
  event: "view_item",
  ecommerce: {
    currency: "USD",
    value: 58.99,
    items: [{
      item_id: "SKU123",
      item_name: "Product Name",
      item_category: "Proteínas",
      price: 58.99,
      quantity: 1
    }]
  }
});
```

### add_to_cart / remove_from_cart
```javascript
dataLayer.push({ ecommerce: null });
dataLayer.push({
  event: "add_to_cart",
  ecommerce: {
    currency: "USD",
    value: 58.99,
    items: [{
      item_id: "SKU123",
      item_name: "Product Name",
      item_category: "Proteínas",
      price: 58.99,
      quantity: 1
    }]
  }
});
```

### purchase
```javascript
dataLayer.push({ ecommerce: null });
dataLayer.push({
  event: "purchase",
  ecommerce: {
    transaction_id: "WC-12345",
    value: 150.00,
    currency: "USD",
    tax: 0,
    shipping: 0,
    items: [{
      item_id: "SKU123",
      item_name: "Product Name",
      item_category: "Proteínas",
      price: 58.99,
      quantity: 2
    }]
  }
});
```

## Plugin: GTM Kit

Si el sitio usa **GTM Kit**:
1. Configurar GTM Container ID en Ajustes → GTM Kit
2. Activar opción "Activar etiqueta de Google (ga4)" si está disponible
3. Los eventos ecommerce se empujan automáticamente via WooCommerce integration
4. Verificar dataLayer en consola del navegador

## Plugin: WooCommerce Google Analytics Integration

Si usa el plugin oficial de WooCommerce:
1. WooCommerce → Ajustes → Integración → Google Analytics
2. Ingresar ID de medición (`G-XXXXXXXX`)
3. Activar "Eventos de comercio electrónico mejorados"
4. Configurar eventos adicionales según necesidad

## Dimensiones Personalizadas Recomendadas

| Dimensión | Parámetro | Alcance | Uso |
|-----------|-----------|---------|-----|
| Product Brand | `item_brand` | Item | Análisis por marca |
| Product Type | `item_variant` | Item | Simple/variable/agrupado |
| Customer ID | `user_id` | User | Tracking cross-device |
| Sucursal | `pickup_location` | Event | Análisis retiro en tienda |

## Verificación Rápida

1. Abrir consola JS: `dataLayer`
2. Navegar producto: buscar `view_item` event
3. Agregar al carrito: buscar `add_to_cart` event
4. Completar compra: buscar `purchase` event con `transaction_id`
5. En GA4 DebugView deben aparecer los eventos con sus parámetros
