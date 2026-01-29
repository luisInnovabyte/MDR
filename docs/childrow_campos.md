# Documentación Child-Row - DataTables Líneas de Presupuesto

> **Archivo:** `view/lineasPresupuesto/lineasPresupuesto.js` - Función `formatLineaDetalle(d)`  
> **Fecha:** 29 de enero de 2026  
> **Descripción:** Detalle expandible de cada línea de presupuesto mostrado al hacer clic en el botón [+]

---

## 📋 Estructura Visual

El child-row se divide en **3 columnas principales** + **1 fila adicional** con información técnica:

```
┌─────────────────────────────────────────────────────────────────┐
│  Detalle Completo de la Línea                                  │
├─────────────────┬─────────────────┬─────────────────────────────┤
│  COLUMNA 1      │  COLUMNA 2      │  COLUMNA 3                  │
│  Información    │  Detalle        │  Localización y Fechas      │
│  General        │  Económico      │                             │
├─────────────────┴─────────────────┴─────────────────────────────┤
│  FILA ADICIONAL: Información Técnica y Sistema                  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 📊 COLUMNA 1: Información General

### Campos Principales

| Campo | Nombre BD | Tipo | Descripción |
|-------|-----------|------|-------------|
| **ID Línea** | `id_linea_ppto` | INT | Identificador único de la línea |
| **Tipo** | `tipo_linea_ppto` | ENUM | Tipo de línea: `articulo`, `kit`, `seccion`, `texto` |
| **Código** | `codigo_linea_ppto` / `codigo_articulo` | VARCHAR | Código del artículo/línea (fallback a código del artículo si no hay específico) |
| **Descripción** | `descripcion_linea_ppto` | TEXT | Descripción de la línea |

### Visualización de Tipo

- **Artículo**: Badge azul (`bg-primary`)
- **Kit**: Badge verde (`bg-success`)
- **Sección**: Badge amarillo (`bg-warning`)
- **Texto**: Badge celeste (`bg-info`)

### Sección: Observaciones (condicional)

**Campo:** `observaciones_linea_ppto`  
**Visualización:** Alerta info con icono de chat  
**Nota especial:** Se marca con `**` para indicar que se imprimirán en el presupuesto  
**Se muestra:** Solo si el campo tiene contenido

### Sección: Artículo/Kit Asociado (condicional)

**Condición:** Se muestra solo si `es_kit_articulo == 1`

| Campo | Nombre BD | Valores |
|-------|-----------|---------|
| **Ocultar Detalle Kit** | `ocultar_detalle_kit_linea_ppto` | `null` = No aplica (badge gris)<br>`1` = Se ocultarán detalles (badge amarillo)<br>`0` = Se mostrarán detalles (badge verde) |

---

## 💰 COLUMNA 2: Detalle Económico

### Campos de Cantidad y Precio

| Campo | Nombre BD | Formato | Descripción |
|-------|-----------|---------|-------------|
| **Cantidad** | `cantidad_linea_ppto` | Número | Cantidad de artículos/unidades |
| **Precio Unitario** | `precio_unitario_linea_ppto` | Moneda (€) | Precio por unidad sin descuento |
| **Descuento** | `descuento_linea_ppto` | Porcentaje (%) | Descuento aplicado (2 decimales) |
| **Coeficiente** | `valor_coeficiente_linea_ppto` | Decimal | Coeficiente multiplicador (por defecto 1.00) |
| **Jornadas** | `jornadas_linea_ppto` | INT | Número de jornadas (opcional) |

### Campos de Totales

| Campo | Nombre BD | Formato | Descripción |
|-------|-----------|---------|-------------|
| **Base Imponible*** | `base_imponible` | Moneda (€) | Total sin IVA (con descuento y coeficiente aplicados) |
| **IVA** | `importe_iva` | Moneda (€) | Importe del IVA calculado |
| **% IVA** | `porcentaje_iva_linea_ppto` | Porcentaje | Porcentaje de IVA aplicado (por defecto 21%) |
| **TOTAL*** | `total_linea` | Moneda (€) | Total final con IVA (destacado en verde) |

**Nota:** El asterisco (*) indica que **NO incluye el descuento global del cliente**.

### Indicadores Visuales

#### Coeficiente Aplicado
- **Condición:** `aplicar_coeficiente_linea_ppto == 1`
- **Visualización:** Alerta amarilla con icono de calculadora
- **Mensaje:** "Se ha aplicado un coeficiente reductor basado en X jornada(s)"

#### Artículo No Facturable
- **Condición:** `no_facturar_articulo == 1`
- **Visualización:** Alerta roja con icono de exclamación
- **Mensaje:** "Artículo marcado como no facturable"

#### No Permite Descuentos
- **Condición:** `permitir_descuentos_articulo == 0`
- **Visualización:** Alerta amarilla con icono de slash-circle
- **Mensaje:** "Artículo marcado como no permitir descuentos"

---

## 📍 COLUMNA 3: Localización y Fechas

### Sección: Localización y Fechas

| Campo | Nombre BD | Formato | Descripción |
|-------|-----------|---------|-------------|
| **Localización** | `localizacion_linea_ppto` | Texto | Ubicación física del evento/montaje |
| **Inicio** | `fecha_inicio_linea_ppto` | DATE | Fecha de inicio del evento |
| **Fin** | `fecha_fin_linea_ppto` | DATE | Fecha de finalización del evento |
| **Duración** | *Calculado* | Badge info | Diferencia en días entre inicio y fin |

**Formato de fecha:** dd/mm/yyyy (localización española)

**Cálculo de duración:**
```javascript
let dias = Math.ceil((fin - inicio) / (1000 * 60 * 60 * 24)) + 1;
```

### Sección: Planificación

| Campo | Nombre BD | Formato | Descripción |
|-------|-----------|---------|-------------|
| **Montaje** | `fecha_montaje_linea_ppto` | DATE | Fecha prevista de montaje |
| **Desmontaje** | `fecha_desmontaje_linea_ppto` | DATE | Fecha prevista de desmontaje |
| **Días Evento** | `dias_evento_linea_ppto` | INT | Número de días del evento (badge info) |
| **Días Planificación** | `dias_planificacion_linea_ppto` | INT | Días totales de planificación (badge amarillo) |

**Nota:** Los campos de "Días Evento" y "Días Planif." son opcionales y solo se muestran si tienen valor.

### Sección: Notas (condicional)

**Campo:** `notas_linea_ppto`  
**Visualización:** Alerta secundaria con formato de texto enriquecido  
**Se muestra:** Solo si el campo tiene contenido  
**Diferencia con Observaciones:** Las notas son internas, no se imprimen en el presupuesto

---

## 🔧 FILA ADICIONAL: Información Técnica y Sistema

### Columna Izquierda: Información Técnica

| Campo | Nombre BD | Descripción |
|-------|-----------|-------------|
| **ID Versión Presupuesto** | `id_version_presupuesto` | Identificador de la versión del presupuesto |
| **Número Línea** | `numero_linea_ppto` | Número de orden de la línea (opcional) |
| **Nivel Jerarquía** | `nivel_jerarquia` | Nivel en la jerarquía de líneas (opcional) |
| **ID Coeficiente** | `id_coeficiente` | ID del coeficiente aplicado (opcional) |
| **Estado** | `activo_linea_ppto` | `1` = Activo (verde) / `0` = Inactivo (rojo) |

### Columna Derecha: Registro (Timestamps)

| Campo | Nombre BD | Formato | Descripción |
|-------|-----------|---------|-------------|
| **Creado** | `created_at_linea_ppto` | TIMESTAMP | Fecha y hora de creación del registro |
| **Actualizado** | `updated_at_linea_ppto` | TIMESTAMP | Fecha y hora de última actualización |

**Formato de timestamps:** dd/mm/yyyy hh:mm:ss (localización española)

---

## 🎨 Estilos y Clases CSS

### Estructura de la Card

```html
<div class="card border-0 shadow-sm">
    <div class="card-header bg-primary text-white py-2">
        <!-- Título con icono -->
    </div>
    <div class="card-body py-2">
        <!-- Contenido en columnas -->
    </div>
</div>
```

### Clases de Badges

| Tipo | Clase CSS | Color |
|------|-----------|-------|
| Artículo | `badge bg-primary` | Azul |
| Kit | `badge bg-success` | Verde |
| Sección | `badge bg-warning` | Amarillo |
| Texto | `badge bg-info` | Celeste |
| Activo | `badge bg-success` | Verde |
| Inactivo | `badge bg-danger` | Rojo |
| Localización | `badge bg-info` | Celeste |
| Días | `badge bg-info` | Celeste |
| Planificación | `badge bg-warning` | Amarillo |

### Iconos (Bootstrap Icons)

- **Información General:** `bi-box-seam`
- **Económico:** `bi-currency-euro`
- **Localización:** `bi-geo-alt`
- **Fecha Inicio:** `bi-calendar-check` (verde)
- **Fecha Fin:** `bi-calendar-x` (amarillo)
- **Planificación:** `bi-calendar-range`
- **Técnico:** `bi-gear`
- **Registro:** `bi-clock-history`
- **Observaciones:** `bi-chat-left-text`
- **Coeficiente:** `bi-calculator`
- **Alertas:** `bi-exclamation-triangle` / `bi-exclamation-circle`

---

## 🔍 Validaciones y Valores Nulos

La función utiliza un helper para manejar valores nulos/indefinidos:

```javascript
const val = (value) => value !== null && value !== undefined && value !== '' 
    ? value 
    : '<span class="text-muted">-</span>';
```

### Campos con Validación Especial

1. **Código**: Intenta `codigo_linea_ppto`, si no existe usa `codigo_articulo`
2. **Localización**: Muestra badge si existe, sino "No especificada"
3. **Fechas**: Muestra fecha formateada o "No definida"
4. **Observaciones/Notas**: Solo se renderizan si tienen contenido
5. **Campos opcionales**: Se envuelven en condicionales para no mostrar filas vacías

---

## 📌 Notas Técnicas

### Formato de Moneda

Utiliza la función `formatearMoneda()` definida en el archivo JavaScript:

```javascript
function formatearMoneda(numero) {
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(numero);
}
```

### Renderizado Condicional

El child-row utiliza template literals con operadores ternarios y bloques condicionales:

```javascript
${condicion ? `HTML a renderizar` : ''}
```

### Performance

- El child-row se genera bajo demanda al hacer clic en el botón [+]
- No se pre-carga la información, reduciendo carga inicial
- Al cerrar, se destruye el child-row (`row.child.hide()`)

---

## 🔄 Interacción con el Usuario

### Botón de Control

```javascript
$tableBody.on('click', 'button.details-control', function () {
    var tr = $(this).closest('tr');
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

### Estados del Botón

- **Cerrado:** Icono `bi-plus-circle` (círculo con +)
- **Abierto:** Icono `bi-dash-circle` (círculo con -)
- **Clase CSS:** `shown` se añade al `<tr>` cuando está expandido

---

## 📦 Resumen de Campos por Tabla BD

### Tabla: `linea_presupuesto`

**Campos de identificación:**
- `id_linea_ppto`
- `id_version_presupuesto`
- `numero_linea_ppto`

**Campos descriptivos:**
- `tipo_linea_ppto`
- `codigo_linea_ppto`
- `descripcion_linea_ppto`
- `observaciones_linea_ppto`
- `notas_linea_ppto`

**Campos económicos:**
- `cantidad_linea_ppto`
- `precio_unitario_linea_ppto`
- `descuento_linea_ppto`
- `porcentaje_iva_linea_ppto`
- `base_imponible` (calculado)
- `importe_iva` (calculado)
- `total_linea` (calculado)

**Campos de coeficiente:**
- `aplicar_coeficiente_linea_ppto`
- `id_coeficiente`
- `valor_coeficiente_linea_ppto`
- `jornadas_linea_ppto`

**Campos de fechas:**
- `fecha_inicio_linea_ppto`
- `fecha_fin_linea_ppto`
- `fecha_montaje_linea_ppto`
- `fecha_desmontaje_linea_ppto`
- `dias_evento_linea_ppto`
- `dias_planificacion_linea_ppto`

**Campos de ubicación:**
- `localizacion_linea_ppto`

**Campos de kit:**
- `ocultar_detalle_kit_linea_ppto`

**Campos de jerarquía:**
- `nivel_jerarquia`

**Campos de estado:**
- `activo_linea_ppto`

**Campos de auditoría:**
- `created_at_linea_ppto`
- `updated_at_linea_ppto`

### Tabla: `articulo` (JOIN)

- `codigo_articulo` (fallback para código)
- `es_kit_articulo`
- `no_facturar_articulo`
- `permitir_descuentos_articulo`

---

## 🎯 Casos de Uso

### 1. Línea de Artículo Normal
- Muestra todos los campos económicos
- Sin alertas de coeficiente ni kit

### 2. Línea de Kit
- Muestra sección adicional "Artículo/Kit Asociado"
- Campo `ocultar_detalle_kit_linea_ppto` visible

### 3. Línea con Coeficiente Aplicado
- Alerta amarilla en sección económica
- Icono de advertencia junto al valor de coeficiente

### 4. Línea con Restricciones
- Alertas de "No Facturable" o "No Permite Descuentos"
- Según valores de artículo asociado

### 5. Línea con Observaciones
- Alerta info destacada con marcador `**`
- Indica que se imprimirá en el presupuesto

### 6. Línea con Notas Internas
- Alerta secundaria al final de columna 3
- No se imprime en presupuesto

---

**Autor:** Sistema MDR ERP  
**Última actualización:** 29 de enero de 2026  
**Versión:** 1.0
