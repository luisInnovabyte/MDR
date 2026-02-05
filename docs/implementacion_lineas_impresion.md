# Implementación de Líneas en Impresión de Presupuestos

**Fecha:** 5 de febrero de 2026  
**Rama:** impresionPresu  
**Archivos modificados:**
- `models/ImpresionPresupuesto.php`
- `controller/impresionpresupuesto.php`

---

## 📋 Resumen

Se ha implementado la funcionalidad completa para mostrar el detalle de líneas de presupuesto en el documento de impresión para cliente (`cli_esp`), con agrupación por fecha de inicio y ubicación de montaje, incluyendo subtotales y totales generales.

---

## ✅ Cambios Implementados

### 1. Modelo: `ImpresionPresupuesto.php`

**Nuevo método:** `get_lineas_impresion($id_presupuesto)`

- **Fuente de datos:** Vista `v_linea_presupuesto_calculada`
- **Filtros aplicados:**
  - Solo versión actual del presupuesto
  - Solo líneas activas (`activo_linea_ppto = 1`)
  - Solo líneas visibles (`mostrar_en_presupuesto = 1`)
- **Ordenamiento:**
  1. `fecha_inicio_linea_ppto` ASC
  2. `id_ubicacion` ASC
  3. `nombre_articulo` ASC

**Campos obtenidos:**
- `fecha_inicio_linea_ppto` - Fecha de inicio del cobro
- `fecha_fin_linea_ppto` - Fecha de fin del cobro
- `jornadas_linea_ppto` - Número de días/jornadas
- `id_ubicacion` - ID de la ubicación de montaje
- `nombre_ubicacion` - Nombre de la ubicación
- `ubicacion_completa_agrupacion` - Dirección completa
- `nombre_articulo` - Descripción del material
- `codigo_articulo` - Código del artículo
- `cantidad_linea_ppto` - Cantidad
- `precio_unitario_linea_ppto` - Precio unitario
- `descuento_linea_ppto` - % de descuento
- `porcentaje_iva_linea_ppto` - % de IVA
- `valor_coeficiente_linea_ppto` - Coeficiente aplicado
- `base_imponible` - Base imponible calculada
- `importe_iva` - Importe del IVA
- `total_linea` - Total de la línea

---

### 2. Controller: `impresionpresupuesto.php`

#### A. Lógica de Agrupación

**Estructura de datos creada:**
```php
$lineas_agrupadas = [
    'FECHA_INICIO' => [
        'ubicaciones' => [
            ID_UBICACION => [
                'nombre_ubicacion' => string,
                'ubicacion_completa' => string,
                'lineas' => array,
                'subtotal_ubicacion' => float,
                'total_iva_ubicacion' => float,
                'total_ubicacion' => float
            ]
        ],
        'subtotal_fecha' => float,
        'total_iva_fecha' => float,
        'total_fecha' => float
    ]
]

$totales_generales = [
    'subtotal' => float,
    'total_iva' => float,
    'total' => float
]
```

**Proceso:**
1. Obtener líneas del modelo
2. Agrupar por `fecha_inicio_linea_ppto`
3. Sub-agrupar por `id_ubicacion`
4. Calcular subtotales por ubicación
5. Calcular totales por fecha
6. Calcular totales generales

#### B. Estilos CSS Añadidos

**Nuevos selectores:**
- `.lineas-section` - Contenedor principal
- `.fecha-header` - Encabezado de fecha (azul degradado)
- `.ubicacion-header` - Encabezado de ubicación (celeste con borde)
- `.lineas-table` - Tabla de líneas
- `.subtotal-row` - Fila de subtotal por ubicación
- `.total-fecha-row` - Fila de total por fecha
- `.total-general-section` - Sección de totales finales
- `.total-general-table` - Tabla de totales generales

**Características de diseño:**
- Tamaños de fuente optimizados para impresión (7.5pt - 9pt)
- Colores coherentes con el diseño existente
- `page-break-inside: avoid` en elementos críticos
- Responsive y optimizado para A4

#### C. HTML Generado

**Estructura:**
```html
<div class="lineas-section">
    <!-- Por cada fecha -->
    <div class="fecha-header">📅 Fecha de inicio: DD/MM/YYYY</div>
    
    <!-- Por cada ubicación dentro de la fecha -->
    <div class="ubicacion-header">📍 Nombre Ubicación (ID: XXX)</div>
    
    <table class="lineas-table">
        <thead>
            <!-- 11 columnas: Fechas, Días, Coef, Descripción, Cant, P.Unit, %Dto, %IVA, Base, Total -->
        </thead>
        <tbody>
            <!-- Líneas de la ubicación -->
            <tr class="subtotal-row">
                <!-- Subtotal por ubicación -->
            </tr>
        </tbody>
    </table>
    
    <!-- Total por fecha -->
    <table class="lineas-table">
        <tr class="total-fecha-row">
            <!-- Total de la fecha -->
        </tr>
    </table>
    
    <!-- Totales generales -->
    <div class="total-general-section">
        <table class="total-general-table">
            <tr><td>Subtotal (Base Imponible):</td><td>XXX,XX €</td></tr>
            <tr><td>Total IVA:</td><td>XXX,XX €</td></tr>
            <tr class="total-final"><td>TOTAL PRESUPUESTO:</td><td>XXX,XX €</td></tr>
        </table>
    </div>
</div>
```

---

## 📊 Columnas de la Tabla

| # | Columna | Ancho | Alineación | Descripción |
|---|---------|-------|------------|-------------|
| 1 | Fecha Inicio | 8% | Izquierda | Fecha de inicio del cobro (dd/mm/yyyy) |
| 2 | Fecha Fin | 8% | Izquierda | Fecha de fin del cobro (dd/mm/yyyy) |
| 3 | Días | 5% | Centro | Número de jornadas |
| 4 | Coef. | 6% | Centro | Coeficiente aplicado (1,00 por defecto) |
| 5 | Descripción | 28% | Izquierda | Nombre del artículo/material |
| 6 | Cant. | 6% | Centro | Cantidad de elementos |
| 7 | P. Unit. | 9% | Derecha | Precio unitario con símbolo € |
| 8 | %Dto | 5% | Centro | Porcentaje de descuento |
| 9 | %IVA | 5% | Centro | Porcentaje de IVA |
| 10 | Base Imp. | 11% | Derecha | Base imponible con símbolo € |
| 11 | Total | 9% | Derecha | Total de la línea con símbolo € |

---

## 🎯 Características Implementadas

### ✅ Agrupación
- [x] Primer nivel: Fecha de inicio del cobro
- [x] Segundo nivel: Ubicación de montaje (por ID)
- [x] Tercer nivel: Ordenación por nombre de artículo

### ✅ Identificación de Ubicación
- [x] Muestra `id_ubicacion` entre paréntesis
- [x] Muestra `nombre_ubicacion` como texto principal
- [x] Fallback a "Sin ubicación" si no existe

### ✅ Subtotales
- [x] Subtotal por ubicación (Base Imponible + Total)
- [x] Total por fecha (Base Imponible + Total)
- [x] Totales generales (Subtotal, IVA, Total)

### ✅ Formato de Datos
- [x] Fechas en formato europeo (dd/mm/yyyy)
- [x] Números con separadores españoles (1.234,56)
- [x] Símbolo de euro (€) en importes
- [x] Coeficientes con 2 decimales
- [x] Cantidades sin decimales

### ✅ Diseño
- [x] Coherente con estilos existentes
- [x] Optimizado para impresión A4
- [x] `page-break-inside: avoid` en tablas
- [x] Filas alternadas con hover effect
- [x] Iconos emoji para mejor visual (📅 📍)

---

## 🧪 Casos de Uso

### 1. Presupuesto con 1 fecha y 1 ubicación
```
📅 Fecha: 15/03/2026
  📍 Ubicación Principal (ID: 5)
  [Tabla con líneas]
  Subtotal Ubicación Principal: 1.234,56 € | 1.481,17 €
  TOTAL FECHA 15/03/2026: 1.234,56 € | 1.481,17 €

TOTALES:
  Subtotal: 1.234,56 €
  Total IVA: 246,61 €
  TOTAL: 1.481,17 €
```

### 2. Presupuesto con múltiples fechas y ubicaciones
```
📅 Fecha: 01/04/2026
  📍 Sala Principal (ID: 10)
  [Líneas...]
  Subtotal: XXX €
  
  📍 Jardín (ID: 12)
  [Líneas...]
  Subtotal: XXX €
  
  TOTAL FECHA: XXX €

📅 Fecha: 02/04/2026
  📍 Terraza (ID: 15)
  [Líneas...]
  Subtotal: XXX €
  
  TOTAL FECHA: XXX €

TOTALES GENERALES: XXX €
```

---

## 🔍 Validaciones

- Si no hay líneas → No se muestra la sección (array vacío)
- Si `id_ubicacion` es 0 o NULL → Muestra "Sin ubicación"
- Valores numéricos vacíos → Defaultean a 0
- Fechas vacías → Muestran "-"
- Coeficiente vacío → Defaultea a "1,00"

---

## 📌 Notas Importantes

1. **Vista utilizada:** `v_linea_presupuesto_calculada`
   - Contiene todos los cálculos necesarios
   - Ya incluye base imponible, IVA y totales
   - Filtros aplicados: activo=1, mostrar_en_presupuesto=1

2. **Versión del presupuesto:**
   - Se obtiene automáticamente la versión actual (`version_actual_presupuesto`)
   - No es necesario pasar el número de versión

3. **Rendimiento:**
   - Una sola consulta SQL para todas las líneas
   - Agrupación y cálculos en PHP (más flexible)
   - No requiere vistas SQL adicionales

4. **Compatibilidad:**
   - Compatible con navegadores modernos
   - Funcionalidad de impresión nativa (Ctrl+P)
   - Exportación a PDF desde el navegador

---

## 🚀 Próximas Mejoras Sugeridas

- [ ] Opción para ocultar columnas (coeficiente, descuento)
- [ ] Diferentes formatos de impresión (compacto, detallado)
- [ ] Filtro por ubicación específica
- [ ] Opción para agrupar por familia de artículos
- [ ] Traducción a inglés/otros idiomas
- [ ] Código QR con link al presupuesto online
- [ ] Firma digital del cliente

---

## 📝 Testing Realizado

- [x] Sintaxis PHP válida (sin errores)
- [x] Estructura HTML correcta
- [x] CSS coherente con diseño existente
- [ ] Prueba con presupuesto real (pendiente)
- [ ] Prueba de impresión en diferentes navegadores (pendiente)
- [ ] Validación con múltiples fechas y ubicaciones (pendiente)

---

## 👥 Autor

**Luis - Innovabyte**  
Fecha: 5 de febrero de 2026  
Rama: `impresionPresu`
