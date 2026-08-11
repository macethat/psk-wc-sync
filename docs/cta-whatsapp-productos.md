# CTA WhatsApp en fichas de producto (Suplementos Panamá)

Referencia de uso interno. Estructura verificada en producción el 2026-08-11
(combos 21521, 21516, 21960-21962).

## Reglas generales

- **NO usar botón verde sólido** (`display:inline-block; background:#25D366; color:#fff; padding:15px 30px; border-radius:50px`).
- **NO escribir texto** "Escríbenos por WhatsApp" dentro del enlace: el CTA es solo el icono.
- El CTA es un **contenedor gris** con un **flex interno** que agrupa el texto
  (`<span>`) + el enlace verde con solo el icono de WhatsApp (26x26).
- El `<span>` del texto lleva `display: inline-flex; align-items: center; justify-content: center;`
  para centrar verticalmente el texto en el taco gris en desktop.
- El texto va **en la misma línea** que la apertura del `<span>`: si queda un salto de línea,
  wpautop inserta un `<br />` que empuja el texto y rompe la alineación vertical.
- URL: `Hola%2C` (coma), `%20` para espacios, `%2B` para el "+" del combo.

## Snippet HTML (reemplazar [PRODUCTO A] y [PRODUCTO B])

```html
<div style="margin-top: 20px; padding: 15px; background-color: #f7f7f7; border-radius: 10px; text-align: center;">
<div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: center; gap: 10px;">
  <span style="color: #333333; text-align: center; display: inline-flex; align-items: center; justify-content: center;">Solicita más información sobre <strong>Combo [PRODUCTO A] + [PRODUCTO B]</strong> con uno de nuestros asesores expertos</span>
  <a href="https://wa.me/50760153257?text=Hola%2C%20quiero%20informaci%C3%B3n%20sobre%20Combo%20[PRODUCTO%20A%20%2B%20PRODUCTO%20B]" target="_blank" rel="noopener" style="display: inline-flex; align-items: center; gap: 8px; color: #25D366; text-decoration: none; font-weight: bold; font-size: 15px;">
    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="26" height="26" fill="#25D366" style="display: block; flex-shrink: 0;">
      <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
    </svg>
  </a>
</div>
</div>
```

## Ejemplo real (21960)

https://suplementospanama.net/product/creatina-vms-80-serv-glutamina-vms-80-serv/
(Combo Creatina VMS 80 Serv + Glutamina VMS 80 Serv)
