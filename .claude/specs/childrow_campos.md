# Spec — Child-Row DataTables: Líneas de Presupuesto

> **Archivo destino:** `view/lineasPresupuesto/lineasPresupuesto.js`  
> **Función:** `formatLineaDetalle(d)`  
> **Descripción:** Fila expandible de cada línea de presupuesto al pulsar el botón [+] de DataTables

---

## 1. Estructura visual

El child-row se renderiza como una card Bootstrap con **3 columnas** y **1 fila adicional**:

```
┌─────────────────────────────────────────────────────────────────┐
│  Detalle Completo de la Línea  (card-header bg-primary)        │
├─────────────────┬─────────────────┬─────────────────────────────┤
│  COL 1          │  COL 2          │  COL 3                      │
│  Info General   │  Detalle Econ.  │  Localización y Fechas      │
├─────────────────┴─────────────────┴─────────────────────────────┤
│  FILA ADICIONAL: Información Técnica y Sistema                  │
└─────────────────────────────────────────────────────────────────┘
```

**HTML base:**
```html
<div class="card border-0 shadow-sm">
  <div class="card-header bg-primary text-white py-2">
    <i class="bi bi-list-ul"></i> Detalle Completo de la Línea
  </div>
  <div class="card-body py-2">
    <div class="row">
      <!-- COL 1, COL 2, COL 3 -->
    </div>
    <div class="row mt-2">
      <!-- FILA ADICIONAL -->
    </div>
  </div>
</div>
```

---

## 2. Helper de valores nulos

```javascript
const val = (value) => value !== null && value !== undefined && value !== ''
    ? value
    : '<span class="text-muted">-</span>';
```

Usar `val()` en TODOS los campos para evitar mostrar `null` o `undefined`.

---

## 3. Helper de moneda

```javascript
function formatearMoneda(numero) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency', currency: 'EUR',
        minimumFractionDigits: 2, maximumFractionDigits: 2
    }).format(numero);
}
```

---

## 3.bis. Helper de fechas

```javascript
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return '<span class="text-muted">-</span>';
    try {
        const fecha    = new Date(fechaString);
        if (isNaN(fecha.getTime())) return 'Fecha inválida';
        const dia      = fecha.getDate().toString().padStart(2, '0');
        const mes      = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const año      = fecha.getFullYear();
        const horas    = fecha.getHours().toString().padStart(2, '0');
        const minutos  = fecha.getMinutes().toString().padStart(2, '0');
        const segundos = fecha.getSeconds().toString().padStart(2, '0');
        return `${dia}/${mes}/${año} ${horas}:${minutos}:${segundos}`;
    } catch (e) {
        return 'Error en fecha';
    }
}
```

> Definir **fuera** de `$(document).ready()` para que sea accesible globalmente. Transforma `"2024-06-15 09:30:00"` → `"15/06/2024 09:30:00"`.

---

## 4. COLUMNA 1 — Información General

**Icono:** `bi-box-seam`

### Campos principales

| Campo BD | Tipo | Descripción |
|----------|------|-------------|
| `id_linea_ppto` | INT | ID único |
| `tipo_linea_ppto` | ENUM | `articulo` / `kit` / `seccion` / `texto` |
| `codigo_linea_ppto` / `codigo_articulo` | VARCHAR | Código (fallback al del artículo si la línea no tiene) |
| `descripcion_linea_ppto` | TEXT | Descripción |

### Badges de tipo

| Valor | Clase |
|-------|-------|
| `articulo` | `badge bg-primary` |
| `kit` | `badge bg-success` |
| `seccion` | `badge bg-warning` |
| `texto` | `badge bg-info` |

### Código (fallback)
```javascript
const codigo = d.codigo_linea_ppto || d.codigo_articulo || '-';
```

### Observaciones (condicional)
- **Campo:** `observaciones_linea_ppto`
- **Solo si tiene contenido**
- Icono: `bi-chat-left-text`
- Clase: `alert alert-info`
- Nota visual: prefijo `**` para indicar que se imprimirán en el presupuesto
- Texto largo: usar `style="white-space: pre-wrap; overflow-wrap: break-word;"` en el contenedor para respetar saltos de línea guardados en BD

```javascript
${d.observaciones_linea_ppto ? `
  <div class="alert alert-info py-1 mt-2">
    <i class="bi bi-chat-left-text"></i>
    <strong>Observaciones:</strong> ** ${val(d.observaciones_linea_ppto)}
  </div>` : ''}
```

### Sección Kit/Artículo asociado (condicional)
- **Condición:** `d.es_kit_articulo == 1`
- Campo: `ocultar_detalle_kit_linea_ppto`

| Valor | Badge |
|-------|-------|
| `null` | `badge bg-secondary` — "No aplica" |
| `1` | `badge bg-warning` — "Se ocultarán detalles" |
| `0` | `badge bg-success` — "Se mostrarán detalles" |

---

## 5. COLUMNA 2 — Detalle Económico

**Icono:** `bi-currency-euro`

### Campos de cantidad y precio

| Campo BD | Formato | Descripción |
|----------|---------|-------------|
| `cantidad_linea_ppto` | Número | Cantidad |
| `precio_unitario_linea_ppto` | `formatearMoneda()` | Precio unitario |
| `descuento_linea_ppto` | `X.XX %` | Descuento (2 decimales) |
| `valor_coeficiente_linea_ppto` | Decimal | Coeficiente (default 1.00) |
| `jornadas_linea_ppto` | INT | Jornadas (opcional, mostrar si tiene valor) |

### Campos de totales

| Campo BD | Formato | Nota |
|----------|---------|------|
| `base_imponible` | `formatearMoneda()` | Campo calculado — NO incluye descuento global cliente |
| `importe_iva` | `formatearMoneda()` | Campo calculado |
| `porcentaje_iva_linea_ppto` | `X %` | Default 21% |
| `total_linea` | `formatearMoneda()` | Destacado en verde (`text-success fw-bold`) |

### Alertas económicas

#### Coeficiente aplicado
- **Condición:** `d.aplicar_coeficiente_linea_ppto == 1`
- Clase: `alert alert-warning`
- Icono: `bi-calculator`
- Mensaje: `"Se ha aplicado un coeficiente reductor basado en X jornada(s)"`

#### Artículo no facturable
- **Condición:** `d.no_facturar_articulo == 1`
- Clase: `alert alert-danger`
- Icono: `bi-exclamation-circle`
- Mensaje: `"Artículo marcado como no facturable"`

#### No permite descuentos
- **Condición:** `d.permitir_descuentos_articulo == 0`
- Clase: `alert alert-warning`
- Icono: `bi-slash-circle`
- Mensaje: `"Artículo marcado como no permitir descuentos"`

---

## 6. COLUMNA 3 — Localización y Fechas

**Icono:** `bi-geo-alt`

### Localización
- **Campo:** `localizacion_linea_ppto`
- Si tiene valor: `badge bg-info`
- Si no tiene valor: texto "No especificada"

### Fechas de evento

| Campo BD | Icono | Color icono |
|----------|-------|-------------|
| `fecha_inicio_linea_ppto` | `bi-calendar-check` | Verde |
| `fecha_fin_linea_ppto` | `bi-calendar-x` | Amarillo |

**Formato:** `dd/mm/yyyy` (localización española)  
**Si no tiene valor:** mostrar "No definida"

**Cálculo duración automatico:**
```javascript
const inicio = new Date(d.fecha_inicio_linea_ppto);
const fin    = new Date(d.fecha_fin_linea_ppto);
const dias   = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
// Mostrar como badge bg-info
```

### Planificación

| Campo BD | Descripción | Badge |
|----------|-------------|-------|
| `fecha_montaje_linea_ppto` | Fecha de montaje | — |
| `fecha_desmontaje_linea_ppto` | Fecha de desmontaje | — |
| `dias_evento_linea_ppto` | Días del evento (opcional) | `badge bg-info` |
| `dias_planificacion_linea_ppto` | Días de planificación (opcional) | `badge bg-warning` |

**Icono planificación:** `bi-calendar-range`  
`dias_evento` y `dias_planificacion` solo se renderizan si tienen valor.

### Notas internas (condicional)
- **Campo:** `notas_linea_ppto`
- **Solo si tiene contenido**
- Clase: `alert alert-secondary`
- **Diferencia con Observaciones:** las notas son internas, NO se imprimen en el presupuesto
- Texto largo: usar `style="white-space: pre-wrap; overflow-wrap: break-word;"` en el contenedor para respetar saltos de línea guardados en BD

---

## 7. FILA ADICIONAL — Información Técnica y Sistema

### Columna izquierda: Información técnica

**Icono:** `bi-gear`

| Campo BD | Descripción |
|----------|-------------|
| `id_version_presupuesto` | ID de versión |
| `numero_linea_ppto` | Número de orden (opcional) |
| `nivel_jerarquia` | Nivel en jerarquía (opcional) |
| `id_coeficiente` | ID del coeficiente (opcional) |
| `activo_linea_ppto` | `1` → `badge bg-success` "Activo" / `0` → `badge bg-danger` "Inactivo" |

### Columna derecha: Timestamps

**Icono:** `bi-clock-history`

| Campo BD | Formato |
|----------|---------|
| `created_at_linea_ppto` | `dd/mm/yyyy hh:mm:ss` |
| `updated_at_linea_ppto` | `dd/mm/yyyy hh:mm:ss` |

---

## 8. Interacción — botón expand/collapse

```javascript
$tableBody.on('click', 'button.details-control', function () {
    var tr  = $(this).closest('tr');
    var row = tabla.row(tr);
    var btn = $(this);

    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
        btn.html('<i class="bi bi-plus-circle"></i>');
    } else {
        row.child(formatLineaDetalle(row.data())).show();
        tr.addClass('shown');
        btn.html('<i class="bi bi-dash-circle"></i>');
    }
});
```

**Estados del botón:**
- Cerrado: `bi-plus-circle`
- Abierto: `bi-dash-circle`
- Clase `shown` se añade al `<tr>` cuando está expandido

---

## 9. Tablas BD implicadas

### `linea_presupuesto`
Identificación: `id_linea_ppto`, `id_version_presupuesto`, `numero_linea_ppto`  
Descriptivos: `tipo_linea_ppto`, `codigo_linea_ppto`, `descripcion_linea_ppto`, `observaciones_linea_ppto`, `notas_linea_ppto`  
Económicos: `cantidad_linea_ppto`, `precio_unitario_linea_ppto`, `descuento_linea_ppto`, `porcentaje_iva_linea_ppto`, `base_imponible`(*), `importe_iva`(*), `total_linea`(*)  
Coeficiente: `aplicar_coeficiente_linea_ppto`, `id_coeficiente`, `valor_coeficiente_linea_ppto`, `jornadas_linea_ppto`  
Fechas: `fecha_inicio_linea_ppto`, `fecha_fin_linea_ppto`, `fecha_montaje_linea_ppto`, `fecha_desmontaje_linea_ppto`, `dias_evento_linea_ppto`, `dias_planificacion_linea_ppto`  
Ubicación: `localizacion_linea_ppto`  
Kit: `ocultar_detalle_kit_linea_ppto`  
Jerarquía: `nivel_jerarquia`  
Estado: `activo_linea_ppto`  
Auditoría: `created_at_linea_ppto`, `updated_at_linea_ppto`

(*) Campos calculados — NO incluyen descuento global del cliente

### `articulo` (JOIN)
`codigo_articulo`, `es_kit_articulo`, `no_facturar_articulo`, `permitir_descuentos_articulo`

> **Regla del controller:** Todo campo `d.campo` referenciado en `formatLineaDetalle()` debe estar presente en el array del `case "listar"`, aunque no tenga columna visible en la tabla. Para campos opcionales usar `$row['campo'] ?? null` para evitar errores de clave inexistente.

---

## 10. Checklist de implementación

- [ ] Helper `val()` definido
- [ ] Helper `formatearMoneda()` definido
- [ ] Fallback de código: `codigo_linea_ppto || codigo_articulo`
- [ ] Badges de tipo correctos por valor de `tipo_linea_ppto`
- [ ] Observaciones renderizan solo si tienen contenido
- [ ] Sección kit se muestra solo si `es_kit_articulo == 1`
- [ ] Totales sin descuento global documentados visualmente (nota con `*`)
- [ ] Las 3 alertas económicas (coeficiente, no facturable, sin descuentos) condicionadas
- [ ] Duración calculada si existen ambas fechas inicio/fin
- [ ] Días evento y planificación opcionales (solo si tienen valor)
- [ ] Notas internas renderizan solo si tienen contenido
- [ ] Timestamps formateados a dd/mm/yyyy hh:mm:ss
- [ ] Botón toggle `bi-plus-circle` / `bi-dash-circle` con clase `shown` en `<tr>`
- [ ] Helper `formatoFechaEuropeo()` definido fuera de `$(document).ready()`
- [ ] Campos de texto largo (observaciones, notas) con `white-space: pre-wrap; overflow-wrap: break-word`
- [ ] Todos los campos `d.campo` usados en `formatLineaDetalle()` presentes en el array del controller
- [ ] Renderizado bajo demanda (no pre-carga)

---

## 11. Prompt de activación

Para pedir a la IA que implemente o refactorize el child-row:

```
Lee `.claude/specs/childrow_campos.md` e implementa (o actualiza) la función
`formatLineaDetalle(d)` en `view/lineasPresupuesto/lineasPresupuesto.js`
siguiendo exactamente la estructura de columnas, badges, iconos,
condiciones y helpers descritos en el spec.
```

Para añadir un campo nuevo al child-row:

```
Lee `.claude/specs/childrow_campos.md` y añade el campo `[nombre_campo_bd]`
en la [columna / fila] correspondiente siguiendo los patrones del spec.
```
