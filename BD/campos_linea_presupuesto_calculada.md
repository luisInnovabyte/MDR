# Análisis de Campos de la Vista `v_linea_presupuesto_calculada`

> **Fecha de análisis**: 30 de enero de 2026  
> **Archivo analizado**: `view/lineasPresupuesto/lineasPresupuesto.js`  
> **Objetivo**: Identificar campos utilizados y no utilizados en la vista SQL

---

## 📊 RESUMEN EJECUTIVO

- **Total de campos en la vista SQL**: ~150+ campos
- **Campos utilizados en JS**: 60 campos
- **Campos NO utilizados**: ~90 campos (aproximadamente 60%)
- **Conclusión**: Hay una cantidad significativa de campos en la vista que no se están utilizando en la interfaz

---

## ✅ CAMPOS UTILIZADOS EN `lineasPresupuesto.js`

### 📦 1. Campos de Línea Presupuesto (tabla `linea_presupuesto`)

| Campo | Uso en JS | Descripción |
|-------|-----------|-------------|
| `id_linea_ppto` | ✅ Múltiples | ID único de la línea - Usado en child-row, botones de acción |
| `id_version_presupuesto` | ✅ | Referencia a versión - Mostrado en info técnica |
| `tipo_linea_ppto` | ✅ | Tipo de línea - Mostrado con badge de color |
| `numero_linea_ppto` | ✅ | Número correlativo - Info técnica |
| `codigo_linea_ppto` | ✅ | Código de la línea - Mostrado en info general |
| `descripcion_linea_ppto` | ✅ | Descripción - Columna principal en tabla |
| `observaciones_linea_ppto` | ✅ | Observaciones - Alert en child-row |
| `ocultar_detalle_kit_linea_ppto` | ✅ | Control de visualización de kit - Badge en child-row |
| `nivel_jerarquia` | ✅ | Nivel jerárquico - Info técnica |
| `cantidad_linea_ppto` | ✅ | Cantidad - Detalle económico |
| `precio_unitario_linea_ppto` | ✅ | Precio unitario - Detalle económico |
| `descuento_linea_ppto` | ✅ | Descuento % - Detalle económico |
| `porcentaje_iva_linea_ppto` | ✅ | IVA % - Detalle económico |
| `fecha_inicio_linea_ppto` | ✅ | Fecha de inicio - Localización y fechas |
| `fecha_fin_linea_ppto` | ✅ | Fecha de fin - Localización y fechas |
| `fecha_montaje_linea_ppto` | ✅ | Fecha montaje - Planificación |
| `fecha_desmontaje_linea_ppto` | ✅ | Fecha desmontaje - Planificación |
| `jornadas_linea_ppto` | ✅ | Jornadas - Detalle económico |
| `dias_evento_linea_ppto` | ✅ | Días del evento - Planificación |
| `dias_planificacion_linea_ppto` | ✅ | Días planificación - Planificación |
| `localizacion_linea_ppto` | ✅ | Localización - Badge en child-row |
| `notas_linea_ppto` | ✅ | Notas adicionales - Alert en child-row |
| `aplicar_coeficiente_linea_ppto` | ✅ | Flag coeficiente - Alert en child-row |
| `valor_coeficiente_linea_ppto` | ✅ | Valor del coeficiente - Detalle económico |
| `id_coeficiente` | ✅ | ID del coeficiente - Info técnica |
| `activo_linea_ppto` | ✅ | Estado activo/inactivo - Badge en info técnica |
| `created_at_linea_ppto` | ✅ | Fecha de creación - Auditoría |
| `updated_at_linea_ppto` | ✅ | Fecha de actualización - Auditoría |

### 💰 2. Campos Calculados Estándar (sin descuento cliente)

| Campo | Uso en JS | Descripción |
|-------|-----------|-------------|
| `base_imponible` | ✅ | Base imponible normal - Detalle económico |
| `importe_iva` | ✅ | Importe IVA normal - Detalle económico |
| `total_linea` | ✅ | Total con IVA normal - Detalle económico (destacado) |

### 🏨 3. Campos Calculados Hotel (con descuento cliente)

| Campo | Uso en JS | Descripción |
|-------|-----------|-------------|
| `precio_unitario_linea_ppto_hotel` | ✅ | Precio unitario con descuento cliente - Precios Hotel |
| `base_imponible_hotel` | ✅ | Base imponible con descuento cliente - Precios Hotel |
| `importe_descuento_linea_ppto_hotel` | ✅ | Importe del descuento de línea - Precios Hotel |
| `TotalImporte_descuento_linea_ppto_hotel` | ✅ | Total sin IVA después de descuentos - Precios Hotel |
| `importe_iva_linea_ppto_hotel` | ✅ | IVA calculado sobre precio hotel - Precios Hotel |
| `TotalImporte_iva_linea_ppto_hotel` | ✅ | Total con IVA hotel - Precios Hotel (destacado) |

### 📦 4. Campos de Artículo

| Campo | Uso en JS | Descripción |
|-------|-----------|-------------|
| `codigo_articulo` | ✅ | Código del artículo - Fallback si no hay código línea |
| `nombre_articulo` | ✅ | Nombre del artículo - Alt de imagen |
| `imagen_articulo` | ✅ | Imagen del artículo - Preview clickeable en child-row |
| `es_kit_articulo` | ✅ | Flag si es kit - Muestra sección de kit |
| `no_facturar_articulo` | ✅ | Flag no facturable - Alert de advertencia |
| `permitir_descuentos_articulo` | ✅ | Flag permite descuentos - Alerts y validaciones |
| `notas_presupuesto_articulo` | ✅ | Observaciones español - Sección observaciones artículo |
| `notes_budget_articulo` | ✅ | Observaciones inglés - Sección observaciones artículo |
| `orden_obs_articulo` | ✅ | Orden de observación - Badge en observaciones |

### 🗂️ 5. Campos de Familia de Artículo

| Campo | Uso en JS | Descripción |
|-------|-----------|-------------|
| `observaciones_presupuesto_familia` | ✅ | Observaciones español - Sección observaciones familia |
| `observations_budget_familia` | ✅ | Observaciones inglés - Sección observaciones familia |
| `orden_obs_familia` | ✅ | Orden de observación - Badge en observaciones |

### 📐 6. Campos de Unidad de Medida

| Campo | Uso en JS | Descripción |
|-------|-----------|-------------|
| `simbolo_unidad` | ✅ | Símbolo de la unidad - Mostrado en info general |
| `nombre_unidad` | ✅ | Nombre de la unidad - Mostrado en info general |

### 💳 7. Campos de Presupuesto/Cliente

| Campo | Uso en JS | Descripción |
|-------|-----------|-------------|
| `porcentaje_descuento_cliente` | ✅ | Descuento del cliente - Badge en precios hotel |
| `mostrar_obs_articulos_presupuesto` | ✅ | Flag para mostrar obs. artículos - Control de visibilidad |
| `mostrar_obs_familias_presupuesto` | ✅ | Flag para mostrar obs. familias - Control de visibilidad |

---

## ❌ CAMPOS NO UTILIZADOS EN LA INTERFAZ

### 📦 1. Campos de Línea NO Utilizados

| Campo | Tipo | Posible uso futuro |
|-------|------|-------------------|
| `id_articulo` | INT | Solo en backend |
| `id_linea_padre` | INT | Jerarquía (no mostrada) |
| `id_ubicacion` | INT | Solo en backend |
| `orden_linea_ppto` | INT | Ordenamiento (manejado en JS) |
| `mostrar_obs_articulo_linea_ppto` | BOOLEAN | Redundante con flag presupuesto |
| `mostrar_en_presupuesto` | BOOLEAN | No implementado en UI |
| `es_opcional` | BOOLEAN | No implementado en UI |
| `id_impuesto` | INT | Solo en backend |

### 💰 2. Campos Calculados NO Utilizados

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `dias_linea` | INT | Cálculo de días (no mostrado explícitamente) |
| `subtotal_sin_coeficiente` | DECIMAL | Cálculo intermedio (no mostrado) |

### 🧮 3. Campos de Coeficiente NO Utilizados

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `jornadas_coeficiente` | INT | De tabla coeficiente |
| `valor_coeficiente` | DECIMAL | De tabla coeficiente (se usa el de línea) |
| `observaciones_coeficiente` | TEXT | No mostrado |
| `activo_coeficiente` | BOOLEAN | Solo en backend |

### 📦 4. Campos de Artículo NO Utilizados

| Campo | Tipo | Posible uso |
|-------|------|-------------|
| `name_articulo` | VARCHAR | Nombre en inglés (no usado) |
| `precio_alquiler_articulo` | DECIMAL | Precio base (se usa el de línea) |
| `coeficiente_articulo` | DECIMAL | No usado explícitamente |
| `control_total_articulo` | BOOLEAN | No implementado en UI |
| `observaciones_articulo` | TEXT | No usado (se usan las de presupuesto) |
| `activo_articulo` | BOOLEAN | Solo en backend |
| `id_familia` | INT | Solo en backend |
| `created_at_articulo` | TIMESTAMP | No mostrado |
| `updated_at_articulo` | TIMESTAMP | No mostrado |

### 🏷️ 5. Campos de Impuesto NO Utilizados (Artículo)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_impuesto_articulo` | INT | ID impuesto del artículo |
| `tipo_impuesto_articulo` | VARCHAR | Tipo de impuesto |
| `tasa_impuesto_articulo` | DECIMAL | Tasa del impuesto |
| `descr_impuesto_articulo` | VARCHAR | Descripción |
| `activo_impuesto_articulo` | BOOLEAN | Estado |

### 🏷️ 6. Campos de Impuesto NO Utilizados (Línea)

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `tipo_impuesto` | VARCHAR | Tipo de impuesto de línea |
| `tasa_impuesto` | DECIMAL | Tasa de impuesto de línea |
| `descr_impuesto` | VARCHAR | Descripción de impuesto |
| `activo_impuesto` | BOOLEAN | Estado de impuesto |

### 📐 7. Campos de Unidad NO Utilizados

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_unidad` | INT | ID de unidad |
| `name_unidad` | VARCHAR | Nombre en inglés |
| `descr_unidad` | VARCHAR | Descripción |
| `activo_unidad` | BOOLEAN | Estado |

### 🗂️ 8. Campos de Familia NO Utilizados

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_grupo` | INT | ID del grupo |
| `codigo_familia` | VARCHAR | Código de familia |
| `nombre_familia` | VARCHAR | Nombre español |
| `name_familia` | VARCHAR | Nombre inglés |
| `descr_familia` | VARCHAR | Descripción |
| `imagen_familia` | VARCHAR | Imagen de familia |
| `coeficiente_familia` | DECIMAL | Coeficiente |
| `permite_descuento_familia` | BOOLEAN | Flag descuento |
| `activo_familia_relacionada` | BOOLEAN | Estado |

### 📋 9. Campos de Versión Presupuesto NO Utilizados

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_presupuesto` | INT | Solo backend |
| `numero_version_presupuesto` | INT | No mostrado |
| `estado_version_presupuesto` | VARCHAR | No mostrado |
| `fecha_creacion_version` | TIMESTAMP | No mostrado |
| `fecha_envio_version` | TIMESTAMP | No mostrado |
| `fecha_aprobacion_version` | TIMESTAMP | No mostrado |

### 📄 10. Campos de Presupuesto NO Utilizados (Vista Completa)

**Hay ~40+ campos de presupuesto que NO se usan en el child-row:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `numero_presupuesto` | VARCHAR | No usado en child-row |
| `fecha_presupuesto` | DATE | No usado |
| `fecha_validez_presupuesto` | DATE | No usado |
| `nombre_evento_presupuesto` | VARCHAR | No usado |
| `fecha_inicio_evento_presupuesto` | DATE | No usado |
| `fecha_fin_evento_presupuesto` | DATE | No usado |
| `id_cliente` | INT | Solo backend |
| `id_estado_ppto` | INT | Solo backend |
| `activo_presupuesto` | BOOLEAN | Solo backend |
| `nombre_cliente` | VARCHAR | No usado |
| `nif_cliente` | VARCHAR | No usado |
| `email_cliente` | VARCHAR | No usado |
| `telefono_cliente` | VARCHAR | No usado |
| `direccion_cliente` | VARCHAR | No usado |
| `cp_cliente` | VARCHAR | No usado |
| `poblacion_cliente` | VARCHAR | No usado |
| `provincia_cliente` | VARCHAR | No usado |
| `duracion_evento_dias` | INT | No usado |
| `dias_hasta_inicio_evento` | INT | No usado |
| `dias_hasta_fin_evento` | INT | No usado |
| `estado_evento_presupuesto` | VARCHAR | No usado |
| `prioridad_presupuesto` | VARCHAR | No usado |
| `tipo_pago_presupuesto` | VARCHAR | No usado |
| `descripcion_completa_forma_pago` | TEXT | No usado |
| `fecha_vencimiento_anticipo` | DATE | No usado |
| `fecha_vencimiento_final` | DATE | No usado |
| `comparacion_descuento` | VARCHAR | No usado |
| `estado_descuento_presupuesto` | VARCHAR | No usado |
| `aplica_descuento_presupuesto` | BOOLEAN | No usado |
| `diferencia_descuento` | DECIMAL | No usado |
| `tiene_direccion_facturacion_diferente` | BOOLEAN | No usado |
| `dias_desde_emision` | INT | No usado |
| `id_version_actual` | INT | No usado |
| `numero_version_actual` | INT | No usado |
| `estado_version_actual` | VARCHAR | No usado |
| `fecha_creacion_version_actual` | TIMESTAMP | No usado |
| `estado_general_presupuesto` | VARCHAR | No usado |

### 📍 11. Campos de Ubicación Cliente NO Utilizados

**Todos los campos de `cliente_ubicacion` NO se usan:**

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `nombre_ubicacion` | VARCHAR | No mostrado |
| `direccion_ubicacion` | VARCHAR | No mostrado |
| `codigo_postal_ubicacion` | VARCHAR | No mostrado |
| `poblacion_ubicacion` | VARCHAR | No mostrado |
| `provincia_ubicacion` | VARCHAR | No mostrado |
| `pais_ubicacion` | VARCHAR | No mostrado |
| `persona_contacto_ubicacion` | VARCHAR | No mostrado |
| `telefono_contacto_ubicacion` | VARCHAR | No mostrado |
| `email_contacto_ubicacion` | VARCHAR | No mostrado |
| `observaciones_ubicacion` | TEXT | No mostrado |
| `es_principal_ubicacion` | BOOLEAN | No mostrado |
| `activo_ubicacion` | BOOLEAN | No mostrado |

### 🗺️ 12. Campos de Agrupación NO Utilizados

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `ubicacion_agrupacion` | VARCHAR | Se usa en DataTables (controller) |
| `ubicacion_completa_agrupacion` | VARCHAR | Se usa en DataTables (controller) |

---

## 🎯 RECOMENDACIONES

### 1. **Optimización Inmediata**

Los siguientes campos pueden ser **removidos de la vista** ya que NO se usan en ninguna parte de la interfaz:

#### Alta Prioridad (Campos Duplicados/Redundantes):
- `mostrar_obs_articulo_linea_ppto` - Redundante con `mostrar_obs_articulos_presupuesto`
- `observaciones_articulo` - Se usan `notas_presupuesto_articulo` y `notes_budget_articulo`
- `precio_alquiler_articulo` - Se usa `precio_unitario_linea_ppto`
- `valor_coeficiente` (de tabla coeficiente) - Se usa `valor_coeficiente_linea_ppto`

#### Media Prioridad (Campos de Auditoría no mostrados):
- `created_at_articulo`, `updated_at_articulo`
- Todos los campos de estados e impuestos no usados

#### Baja Prioridad (Datos de contexto que podrían usarse en futuro):
- Campos de ubicación (si se planea mostrar ubicación detallada)
- Campos de presupuesto (podrían ser útiles para otras vistas)

### 2. **Campos a Mantener**

Aunque no se usen en `lineasPresupuesto.js`, estos campos deben mantenerse porque se usan en:

- **Controller** (`lineapresupuesto.php`): Campos de agrupación, IDs de referencia
- **Otras vistas**: Los campos de presupuesto pueden usarse en listados
- **Backend**: IDs de relaciones (id_articulo, id_ubicacion, etc.)

### 3. **Optimización de Vista SQL**

**Propuesta de vista optimizada** con solo campos usados:

```sql
CREATE VIEW v_linea_presupuesto_calculada_slim AS
SELECT 
    -- IDs y referencias (backend)
    lp.id_linea_ppto,
    lp.id_version_presupuesto,
    lp.id_articulo,
    lp.id_ubicacion,
    
    -- Campos de línea usados
    lp.tipo_linea_ppto,
    lp.numero_linea_ppto,
    lp.codigo_linea_ppto,
    lp.descripcion_linea_ppto,
    lp.observaciones_linea_ppto,
    lp.ocultar_detalle_kit_linea_ppto,
    lp.nivel_jerarquia,
    lp.cantidad_linea_ppto,
    lp.precio_unitario_linea_ppto,
    lp.descuento_linea_ppto,
    lp.porcentaje_iva_linea_ppto,
    lp.fecha_inicio_linea_ppto,
    lp.fecha_fin_linea_ppto,
    lp.fecha_montaje_linea_ppto,
    lp.fecha_desmontaje_linea_ppto,
    lp.jornadas_linea_ppto,
    lp.dias_evento_linea_ppto,
    lp.dias_planificacion_linea_ppto,
    lp.localizacion_linea_ppto,
    lp.notas_linea_ppto,
    lp.aplicar_coeficiente_linea_ppto,
    lp.valor_coeficiente_linea_ppto,
    lp.id_coeficiente,
    lp.activo_linea_ppto,
    lp.created_at_linea_ppto,
    lp.updated_at_linea_ppto,
    
    -- Cálculos estándar
    ... AS base_imponible,
    ... AS importe_iva,
    ... AS total_linea,
    
    -- Cálculos hotel
    ... AS precio_unitario_linea_ppto_hotel,
    ... AS base_imponible_hotel,
    ... AS importe_descuento_linea_ppto_hotel,
    ... AS TotalImporte_descuento_linea_ppto_hotel,
    ... AS importe_iva_linea_ppto_hotel,
    ... AS TotalImporte_iva_linea_ppto_hotel,
    
    -- Artículo (solo campos usados)
    a.codigo_articulo,
    a.nombre_articulo,
    a.imagen_articulo,
    a.es_kit_articulo,
    a.no_facturar_articulo,
    a.permitir_descuentos_articulo,
    a.notas_presupuesto_articulo,
    a.notes_budget_articulo,
    a.orden_obs_articulo,
    
    -- Familia (solo observaciones)
    a.observaciones_presupuesto_familia,
    a.observations_budget_familia,
    a.orden_obs_familia,
    
    -- Unidad
    a.simbolo_unidad,
    a.nombre_unidad,
    
    -- Presupuesto (solo flags de control)
    p.porcentaje_descuento_cliente,
    p.mostrar_obs_articulos_presupuesto,
    p.mostrar_obs_familias_presupuesto

FROM linea_presupuesto lp
-- JOINs necesarios...
```

### 4. **Beneficios de Optimizar**

- ⚡ **Rendimiento**: Menor cantidad de datos transferidos
- 💾 **Memoria**: Menos uso de RAM en servidor
- 🚀 **Velocidad**: Queries más rápidas
- 🧹 **Mantenibilidad**: Vista más clara y fácil de mantener
- 📊 **Claridad**: Solo campos realmente utilizados

---

## 📝 NOTAS FINALES

1. **Este análisis es específico para `lineasPresupuesto.js`**
   - Otros archivos pueden usar campos adicionales
   - Verificar antes de eliminar campos de la vista

2. **Campos en Controller vs Vista**
   - El controller puede usar campos para filtrado/ordenamiento
   - Verificar `controller/lineapresupuesto.php` antes de optimizar

3. **Campos de Agrupación**
   - `ubicacion_agrupacion` y `ubicacion_completa_agrupacion` se usan en DataTables
   - Son calculados y usados en el controller

4. **Mantenimiento Futuro**
   - Documentar nuevos campos agregados a la vista
   - Revisar periódicamente campos no utilizados

---

**Generado automáticamente**: 30 de enero de 2026  
**Revisión recomendada**: Trimestral
