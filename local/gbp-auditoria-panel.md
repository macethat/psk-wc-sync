# Auditoría GBP por sucursal (panel) + correcciones pendientes

Fecha: 6 de octubre de 2026
Objetivo: completar la revisión de las 6 fichas de Google Business Profile para la optimización de "proteína en Panamá" y categorías.
Nota: lo público se obtuvo con la Google Places API (dato real de Google hoy). Lo interno (panel) lo completa el equipo.

---

## 1. Hallazgo importante: rating/review_count de `data.php` NO coinciden con Google

`data.php` (`psk-sucursales/src/theme/sucursales/data.php`) alimenta:
- La landing de sucursal: **`page-sucursal-single.php` líneas 268-269** → "⭐ {rating}" y "{review_count} reseñas en Google".
- El **schema `aggregateRating`** de esas landings: **líneas 77-82**.

Comparación:

| Sucursal | data.php (en el sitio) | Google real (Places API) | Coincide |
|---|---|---|---|
| El Cangrejo | 4.8 / 152 | 4.8 / **52** | No (conteo) |
| Megapolis | 4.7 / 98 | **sin rating** / 0 | No |
| Atrio Mall | 4.6 / 75 | 4.0 / **3** | No |
| San Francisco | 4.9 / 203 | **sin rating** / 0 | No |
| Altos de Panamá | 4.5 / 62 | 5.0 / **1** | No |
| Metromall | 4.7 / 88 | 5.0 / **1** | No |

**Acción pendiente:** verificar en el panel el conteo real de reseñas de cada ficha y:
1. Corregir `rating`/`review_count` en `data.php` (o quitarlos de la landing hasta que sean reales).
2. Revisar el `aggregateRating` del schema (riesgo de "reseñas auto-servicio" de Google). Decisión: quitarlo o dejarlo solo si refleja reseñas reales del negocio en la página.

---

## 2. Estado público de las 6 fichas (Google, hoy)

| Sucursal | Nombre en Google | Dirección | Fotos | Web landing | Horario |
|---|---|---|---|---|---|
| El Cangrejo | `Suplementos Panama` (sin sucursal; falta tilde) | Plaza El Cangrejo, Av. Manuel Espinosa Batista | 10 | ✓ `/sucursales/el-cangrejo/` | ✓ |
| Megapolis | `Suplementos Panamá MEGAPOLIS` (CAPS) | Multicentro Megapolis, frente al Smartfit | 10 | ✓ | ✓ |
| Atrio Mall | `Suplementos Panamá Costa del Este` (no dice Atrio) | Av Marina del Nte (**incompleta**) | 7 | ✓ | ✓ |
| San Francisco | `Suplementos Panama Powerclub san francisco` | Plaza Arval, C. 72 Este | 3 | ✓ | ✓ |
| Altos de Panamá | `Suplementos Panamá Altos de Panamá` | Plaza Caminos del Centennial piso 1 dentro de PowerCLUB | 6 | ✓ | ✓ |
| Metromall | `Suplementos Panamá MetroMall` (CAPS) | Av. Domingo Díaz (**incompleta**) | 8 | ✓ | ✓ |

- Todas `OPERATIONAL`. **No se detectaron fichas duplicadas** por nombre (una por sucursal).
- Nombre objetivo a estandarizar: `Suplementos Panamá [Sucursal]`.
- Benchmark competencia (reseñas): Fitness Game 26-50, Vitamin Shoppe 8-37, Inner Boosters 66, BodyBuildingLatino Centennial 19.

---

## 3. Plantilla para completar desde el panel (por sucursal)

Rellenar para cada una de las 6 fichas. Copiar el bloque y anotar la respuesta.

```
### Sucursal: ____________________

1. Categoría PRINCIPAL (exacta como aparece en el selector): ____________________
   Categorías SECUNDARIAS: __________________________________________________

2. Catálogo de "Productos": ¿cargado? [ ] Sí  [ ] No
   Categorías/productos cargados: __________________________________________

3. Posts/Publicaciones: ¿hay actividad? [ ] Sí  [ ] No
   Última fecha: ____________  Tema: ____________________

4. Preguntas y respuestas (Q&A): ¿hay preguntas? [ ] Sí  [ ] No  ¿respondidas? [ ] Sí [ ] No
   Cuántas: ______

5. Servicios y Atributos marcados: __________________________________________
   (retiro en tienda / delivery / estacionamiento / tarjeta / Yappy / accesibilidad / etc.)

6. Mensajería: [ ] Activa  [ ] Inactiva (¿hay chat/WhatsApp configurado?)

7. Verificación y acceso: [ ] Verificada  [ ] Sin verificar
   Propietario/admin de la cuenta: ____________________________

8. Reseñas reales (para corregir data.php):
   Rating actual: ______   Total de reseñas: ______
```

---

## 4. Checklist de acciones GBP (recordatorio)

- [ ] Estandarizar el nombre de las 6 fichas a `Suplementos Panamá [Sucursal]`.
- [ ] Corregir categoría principal a "Sports nutrition store" o "Vitamin and supplements store".
- [ ] Completar direcciones de Atrio y Metromall (mall + nivel + referencia).
- [ ] Subir fotos hasta ≥10 por ficha (prioridad: San Francisco, Altos, Atrio).
- [ ] Cargar catálogo de Productos "Proteínas" (10-20 top sellers por ficha).
- [ ] Corregir `rating`/`review_count` de `data.php` y revisar el `aggregateRating` del schema.
- [ ] Sistema de reseñas (post-compra WhatsApp + QR por sucursal).
- [ ] Solicitar acceso a la Business Profile API (form "Application for Basic API Access").
