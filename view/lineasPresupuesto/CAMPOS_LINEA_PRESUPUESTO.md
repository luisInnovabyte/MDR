# Documentación de Campos - Líneas de Presupuesto

> **Proyecto:** MDR ERP Manager  
> **Módulo:** Líneas de Presupuesto  
> **Fecha:** 23 de enero de 2025  
> **Tabla:** `linea_presupuesto`

---

## 📋 ÍNDICE

1. [Resumen Ejecutivo](#resumen-ejecutivo)
2. [Definición de la Tabla en Base de Datos](#definición-de-la-tabla-en-base-de-datos)
3. [Campos Utilizados en DataTables](#campos-utilizados-en-datatables)
4. [Campos Utilizados en Formulario](#campos-utilizados-en-formulario)
5. [Campos NO Utilizados](#campos-no-utilizados)
6. [Mapeo Completo de Campos](#mapeo-completo-de-campos)

---

## 📊 RESUMEN EJECUTIVO

### Estadísticas de Uso de Campos

| Categoría | Cantidad | Porcentaje |
|-----------|----------|------------|
| **Total de campos en BD** | 33 | 100% |
| **Usados en DataTables** | 10 | 30.3% |
| **Usados en Formulario** | 28 | 84.8% |
| **NO utilizados** | 5 | 15.2% |

### Campos Críticos NO Utilizados

1. ❌ `numero_linea_ppto` - Número de línea visual (puede ser útil para ordenación manual)
2. ❌ `nivel_jerarquia` - Nivel de anidamiento para KITs (no implementado en UI)
3. ❌ `tipo_linea_ppto` - Tipo de línea (artículo/kit/sección/texto/subtotal)
4. ❌ `created_at_linea_ppto` - Auditoría de creación
5. ❌ `updated_at_linea_ppto` - Auditoría de actualización

---

## 🗄️ DEFINICIÓN DE LA TABLA EN BASE DE DATOS

```sql
CREATE TABLE `linea_presupuesto` (
    -- IDENTIFICADORES Y RELACIONES
    `id_linea_ppto` int unsigned NOT NULL AUTO_INCREMENT PRIMARY KEY,
    `id_version_presupuesto` int unsigned NOT NULL COMMENT 'FK: Versión del presupuesto a la que pertenece esta línea',
    `id_articulo` int unsigned DEFAULT NULL COMMENT 'FK: Artículo original (NULL para líneas tipo texto/sección)',
    `id_linea_padre` int unsigned DEFAULT NULL COMMENT 'FK: Línea padre para componentes de KIT (NULL si es línea principal)',
    `id_ubicacion` int unsigned DEFAULT NULL COMMENT 'FK: Ubicación específica de montaje',
    `id_coeficiente` int unsigned DEFAULT NULL COMMENT 'FK: Coeficiente reductor aplicado',
    `id_impuesto` int DEFAULT NULL COMMENT 'FK: Tipo de impuesto/IVA aplicado (INT sin UNSIGNED por compatibilidad con tabla impuesto)',
    
    -- CONFIGURACIÓN DE LÍNEA
    `numero_linea_ppto` int NOT NULL COMMENT 'Número de línea visual en el presupuesto',
    `tipo_linea_ppto` enum('articulo','kit','componente_kit','seccion','texto','subtotal') 
        COLLATE utf8mb4_spanish2_ci DEFAULT 'articulo' COMMENT 'Tipo de línea',
    `nivel_jerarquia` tinyint DEFAULT '0' COMMENT 'Nivel de anidamiento: 0=principal, 1=componente KIT, 2=sub-componente',
    `orden_linea_ppto` int DEFAULT '0' COMMENT 'Orden de visualización',
    
    -- DATOS DEL ARTÍCULO
    `codigo_linea_ppto` varchar(50) COLLATE utf8mb4_spanish2_ci DEFAULT NULL COMMENT 'Código del artículo',
    `descripcion_linea_ppto` text COLLATE utf8mb4_spanish2_ci NOT NULL COMMENT 'Descripción de la línea',
    
    -- FECHAS
    `fecha_montaje_linea_ppto` date DEFAULT NULL COMMENT 'Fecha orientativa de montaje (informativa para planning)',
    `fecha_desmontaje_linea_ppto` date DEFAULT NULL COMMENT 'Fecha orientativa de desmontaje (informativa para planning)',
    `fecha_inicio_linea_ppto` date DEFAULT NULL COMMENT 'Fecha REAL de inicio para el cobro (heredada pero modificable)',
    `fecha_fin_linea_ppto` date DEFAULT NULL COMMENT 'Fecha REAL de fin para el cobro (heredada pero modificable)',
    
    -- PRECIOS Y CANTIDADES
    `cantidad_linea_ppto` decimal(10,2) DEFAULT '1.00' COMMENT 'Cantidad de unidades',
    `precio_unitario_linea_ppto` decimal(10,2) DEFAULT '0.00' COMMENT 'Precio unitario base (heredado del artículo pero modificable)',
    `descuento_linea_ppto` decimal(5,2) DEFAULT '0.00' COMMENT 'Descuento porcentual específico de la línea (%)',
    
    -- COEFICIENTES
    `aplicar_coeficiente_linea_ppto` tinyint(1) DEFAULT '0' COMMENT 'Si se aplica coeficiente reductor (SÍ/No)',
    `valor_coeficiente_linea_ppto` decimal(10,2) DEFAULT NULL COMMENT 'Valor del coeficiente aplicado',
    `jornadas_linea_ppto` int DEFAULT NULL COMMENT 'Número de jornadas para cálculo del coeficiente',
    
    -- IVA
    `porcentaje_iva_linea_ppto` decimal(5,2) DEFAULT '21.00' COMMENT 'Porcentaje de IVA aplicado',
    
    -- OBSERVACIONES Y CONFIGURACIÓN
    `observaciones_linea_ppto` text COLLATE utf8mb4_spanish2_ci COMMENT 'Observaciones específicas de esta línea',
    `mostrar_obs_articulo_linea_ppto` tinyint(1) DEFAULT '1' COMMENT 'Si mostrar las observaciones del artículo original',
    `ocultar_detalle_kit_linea_ppto` tinyint(1) DEFAULT '0' COMMENT 'TRUE: no mostrar desglose del KIT | FALSE: mostrar componentes',
    `mostrar_en_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si se muestra al cliente en el presupuesto',
    `es_opcional` tinyint(1) DEFAULT '0' COMMENT 'Si es una línea opcional',
    
    -- AUDITORÍA
    `activo_linea_ppto` tinyint(1) DEFAULT '1' COMMENT 'Estado activo/inactivo',
    `created_at_linea_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    `updated_at_linea_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
    
    -- ÍNDICES
    KEY `fk_linea_ppto_coeficiente` (`id_coeficiente`),
    KEY `idx_id_version_presupuesto_linea` (`id_version_presupuesto`),
    KEY `idx_id_articulo_linea` (`id_articulo`),
    KEY `idx_orden_linea_ppto` (`orden_linea_ppto`),
    KEY `idx_tipo_linea` (`tipo_linea_ppto`),
    KEY `idx_linea_padre` (`id_linea_padre`),
    KEY `idx_fecha_montaje` (`fecha_montaje_linea_ppto`),
    KEY `idx_fecha_inicio` (`fecha_inicio_linea_ppto`),
    KEY `idx_ubicacion` (`id_ubicacion`),
    KEY `idx_impuesto` (`id_impuesto`),
    KEY `idx_activo` (`activo_linea_ppto`),
    
    -- FOREIGN KEYS
    CONSTRAINT `fk_linea_ppto_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_linea_ppto_coeficiente` FOREIGN KEY (`id_coeficiente`) REFERENCES `coeficiente` (`id_coeficiente`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_linea_ppto_impuesto` FOREIGN KEY (`id_impuesto`) REFERENCES `impuesto` (`id_impuesto`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_linea_ppto_linea_padre` FOREIGN KEY (`id_linea_padre`) REFERENCES `linea_presupuesto` (`id_linea_ppto`) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_linea_ppto_ubicacion` FOREIGN KEY (`id_ubicacion`) REFERENCES `cliente_ubicacion` (`id_ubicacion`) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    CONSTRAINT `fk_linea_ppto_version` FOREIGN KEY (`id_version_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) 
        ON DELETE CASCADE ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci 
COMMENT='Líneas de detalle de versiones de presupuesto con soporte para KITs jerárquicos';
```

---

## 📊 CAMPOS UTILIZADOS EN DATATABLES

**Archivo:** `view/lineasPresupuesto/lineasPresupuesto.js`  
**Líneas:** 230-241

### Columnas Visibles

| # | Nombre Columna | Campo BD | Tipo Display | Ordenable | Buscable |
|---|----------------|----------|--------------|-----------|----------|
| 1 | **Detalles** | - | Botón | ❌ | ❌ |
| 2 | **Orden** | `orden_linea_ppto` | Número | ✅ | ❌ |
| 3 | **Localización** | `localizacion_linea_ppto` | Texto | ✅ | ✅ |
| 4 | **Código** | `codigo_linea_ppto` | Texto | ✅ | ✅ |
| 5 | **Descripción** | `descripcion_linea_ppto` | Texto | ✅ | ✅ |
| 6 | **Fecha Inicio** | `fecha_inicio_linea_ppto` | Fecha | ✅ | ✅ |
| 7 | **Fecha Fin** | `fecha_fin_linea_ppto` | Fecha | ✅ | ✅ |
| 8 | **Días Duración** | - | Calculado | ❌ | ❌ |
| 9 | **Coeficiente** | `valor_coeficiente_linea_ppto` | Decimal | ✅ | ✅ |
| 10 | **Total Línea** | `total_linea` | Moneda | ✅ | ❌ |
| 11 | **Activo** | `activo_linea_ppto` | Boolean | ✅ | ❌ |
| 12 | **Acciones** | - | Botones | ❌ | ❌ |

### Campos Adicionales Necesarios (no visibles pero usados)

Estos campos se obtienen de la BD pero no se muestran como columnas, se usan en la lógica:

- `id_linea_ppto` - PK para identificar registro al editar/eliminar
- `id_version_presupuesto` - FK para filtrar líneas por versión
- `id_articulo` - FK para obtener datos del artículo
- `aplicar_coeficiente_linea_ppto` - Para determinar si se aplicó coeficiente

### Total Campos Usados por DataTables: **10 campos**

---

## 📝 CAMPOS UTILIZADOS EN FORMULARIO

**Archivo:** `view/lineasPresupuesto/formularioLinea.php`  
**Líneas:** 15-350 (dispersos en el formulario)

### Campos Ocultos (Hidden)

| Campo | Tipo | Propósito |
|-------|------|-----------|
| `id_linea_ppto` | INT | PK - Identificador de línea (vacío en INSERT) |
| `id_version_presupuesto` | INT | FK - Versión del presupuesto (se pasa al abrir modal) |
| `numero_linea_ppto` | INT | ⚠️ DEFINIDO pero NO se usa en interfaz |
| `tipo_linea_ppto` | ENUM | ⚠️ DEFINIDO pero NO se usa en interfaz |
| `nivel_jerarquia` | TINYINT | ⚠️ DEFINIDO pero NO se usa en interfaz |
| `orden_linea_ppto` | INT | Control de ordenación visual |
| `mostrar_obs_articulo_linea_ppto` | BOOLEAN | Si mostrar observaciones del artículo |
| `mostrar_en_presupuesto` | BOOLEAN | Si mostrar línea al cliente |
| `es_opcional` | BOOLEAN | Si es línea opcional |
| `activo_linea_ppto` | BOOLEAN | Estado activo/inactivo |
| `id_impuesto` | INT | FK - Tipo de impuesto/IVA (heredado de artículo) |

### SECCIÓN 1: Artículo y Descripción

| Campo | Tipo | Label | Requerido | Notas |
|-------|------|-------|-----------|-------|
| `id_articulo` | SELECT | Seleccionar Artículo | ✅ Sí | Dispara carga automática de datos |
| `descripcion_linea_ppto` | TEXTAREA | Descripción | ✅ Sí | Se carga desde artículo pero editable |
| `codigo_linea_ppto` | TEXT | Código Artículo | ❌ No | Se hereda de artículo, readonly |

### SECCIÓN 2: Fechas

| Campo | Tipo | Label | Requerido | Notas |
|-------|------|-------|-----------|-------|
| `fecha_montaje_linea_ppto` | DATE | Fecha Montaje (Planificación) | ❌ No | Solo informativa |
| `fecha_desmontaje_linea_ppto` | DATE | Fecha Desmontaje (Planificación) | ❌ No | Solo informativa |
| `fecha_inicio_linea_ppto` | DATE | Fecha Inicio Evento | ✅ Sí | Para cálculo cobro/coeficientes |
| `fecha_fin_linea_ppto` | DATE | Fecha Fin Evento | ✅ Sí | Para cálculo cobro/coeficientes |

### SECCIÓN 3: Cantidad, Precio y Descuento

| Campo | Tipo | Label | Requerido | Notas |
|-------|------|-------|-----------|-------|
| `cantidad_linea_ppto` | NUMBER | Cantidad | ✅ Sí | Default: 1 |
| `precio_unitario_linea_ppto` | NUMBER | Precio Unitario (sin IVA) | ✅ Sí | Heredado de artículo, readonly |
| `descuento_linea_ppto` | NUMBER | Descuento % | ❌ No | 0-100%, default: 0 |
| `porcentaje_iva_linea_ppto` | NUMBER | IVA | ❌ No | Heredado de artículo, readonly |

### SECCIÓN 4: Coeficiente Reductor (Opcional)

| Campo | Tipo | Label | Requerido | Notas |
|-------|------|-------|-----------|-------|
| `aplicar_coeficiente_linea_ppto` | CHECKBOX | Aplicar Coeficiente Reductor | ❌ No | Activa/desactiva sección |
| `jornadas_linea_ppto` | HIDDEN | Jornadas | ❌ No | Calculado automáticamente |
| `id_coeficiente` | HIDDEN | ID Coeficiente | ❌ No | FK al coeficiente aplicado |
| `valor_coeficiente_linea_ppto` | HIDDEN | Valor Coeficiente | ❌ No | Valor aplicado (ej: 8.20) |

**Visualización (solo lectura):**
- `vista_coeficiente` - Muestra factor aplicado (ej: "8.20x")
- `preview_precio_coef` - Muestra precio con coeficiente aplicado

### SECCIÓN 5: Ubicación y Configuración

| Campo | Tipo | Label | Requerido | Notas |
|-------|------|-------|-----------|-------|
| `id_ubicacion` | SELECT | Lugar de Montaje | ❌ No | Ubicaciones del cliente |
| `ocultar_detalle_kit_linea_ppto` | CHECKBOX | Ocultar Detalles del KIT | ❌ No | Solo visible si artículo es KIT |

### SECCIÓN 6: Observaciones

| Campo | Tipo | Label | Requerido | Notas |
|-------|------|-------|-----------|-------|
| `observaciones_linea_ppto` | TEXTAREA | Observaciones | ❌ No | Máx 500 caracteres |

### Total Campos Usados en Formulario: **28 campos**

---

## ❌ CAMPOS NO UTILIZADOS

Estos campos existen en la tabla de BD pero **NO se utilizan** en la interfaz de usuario:

### 1. `numero_linea_ppto` ⚠️ POTENCIALMENTE ÚTIL

```sql
numero_linea_ppto int NOT NULL COMMENT 'Número de línea visual en el presupuesto'
```

**Problema:** Definido como campo oculto en formulario pero NO se usa ni se muestra.

**Uso Potencial:** 
- Podría usarse para ordenación manual de líneas por el usuario
- Diferente de `orden_linea_ppto` que se usa para ordenación automática

**Recomendación:** 
- ✅ Implementar control numérico para que usuario asigne número de línea
- ✅ O eliminar si no se necesita (usar solo `orden_linea_ppto`)

---

### 2. `tipo_linea_ppto` ⚠️ CAMPO CRÍTICO NO IMPLEMENTADO

```sql
tipo_linea_ppto enum('articulo','kit','componente_kit','seccion','texto','subtotal') 
    DEFAULT 'articulo' COMMENT 'Tipo de línea'
```

**Problema:** Campo ENUM con 6 tipos posibles pero NO se usa en interfaz.

**Uso Potencial:**
- `articulo` - Línea de artículo normal
- `kit` - Línea que es un KIT maestro
- `componente_kit` - Línea que es componente de un KIT
- `seccion` - Línea de título/separador
- `texto` - Línea de texto libre
- `subtotal` - Línea de subtotal calculado

**Recomendación:**
- ✅ Implementar tipos de línea para permitir secciones y textos
- ✅ Diferenciar automáticamente entre artículo, kit y componente_kit
- ✅ Agregar opciones en formulario para crear líneas tipo "sección" y "texto"

---

### 3. `nivel_jerarquia` ⚠️ IMPORTANTE PARA KITS

```sql
nivel_jerarquia tinyint DEFAULT '0' COMMENT 'Nivel de anidamiento: 0=principal, 1=componente KIT, 2=sub-componente'
```

**Problema:** Definido pero NO se calcula ni se muestra jerárquicamente en DataTables.

**Uso Potencial:**
- Nivel 0: Línea principal
- Nivel 1: Componente de KIT
- Nivel 2: Sub-componente

**Recomendación:**
- ✅ Implementar indentación visual en DataTables según nivel
- ✅ Calcular automáticamente al insertar componentes de KIT

---

### 4. `id_linea_padre` ⚠️ CAMPO CLAVE PARA JERARQUÍA

```sql
id_linea_padre int unsigned DEFAULT NULL COMMENT 'FK: Línea padre para componentes de KIT'
```

**Problema:** Campo FK para jerarquía de KITs pero NO se usa en formulario.

**Uso Potencial:**
- Relacionar componentes de KIT con su línea padre
- Crear estructura de árbol en DataTables

**Recomendación:**
- ✅ Implementar al desglosar KITs en sus componentes
- ✅ Usar para mostrar estructura jerárquica en tabla

---

### 5. `created_at_linea_ppto` y `updated_at_linea_ppto` 📅 AUDITORÍA

```sql
created_at_linea_ppto timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación'
updated_at_linea_ppto timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización'
```

**Problema:** Campos de auditoría que NO se muestran en interfaz.

**Uso Potencial:**
- Tracking de cambios
- Historial de modificaciones
- Auditoría de usuario

**Recomendación:**
- ✅ Mostrar en modal de detalles o tooltip
- ✅ O dejar solo para logs internos (OK no mostrar)

---

## 🗺️ MAPEO COMPLETO DE CAMPOS

### Campos por Uso

```
TOTAL: 33 campos
├─ ✅ USADOS EN FORMULARIO: 28 campos (84.8%)
│  ├─ Visible + Editable: 15 campos
│  ├─ Visible + Solo lectura: 4 campos
│  ├─ Hidden (necesario para backend): 9 campos
│  └─ Calculados (no BD directa): 2 campos
│
├─ ✅ USADOS EN DATATABLES: 10 campos (30.3%)
│  ├─ Columnas visibles: 7 campos
│  └─ Columnas ocultas (agrupación): 3 campos
│
└─ ❌ NO USADOS: 5 campos (15.2%)
   ├─ numero_linea_ppto (⚠️ potencialmente útil)
   ├─ tipo_linea_ppto (⚠️ crítico para funcionalidad extendida)
   ├─ nivel_jerarquia (⚠️ importante para jerarquía visual)
   ├─ id_linea_padre (⚠️ clave para estructura KITs)
   └─ created_at/updated_at (✅ OK no mostrar, para auditoría)
```

---

## 📋 TABLA COMPARATIVA COMPLETA

| # | Campo BD | Formulario | DataTables | Estado |
|---|----------|------------|------------|--------|
| 1 | `id_linea_ppto` | ✅ Hidden | ✅ Usado internamente | ✅ OK |
| 2 | `id_version_presupuesto` | ✅ Hidden | ✅ Usado internamente | ✅ OK |
| 3 | `id_articulo` | ✅ SELECT | ✅ Usado internamente | ✅ OK |
| 4 | `id_linea_padre` | ❌ | ❌ | ⚠️ NO USADO |
| 5 | `id_ubicacion` | ✅ SELECT | ❌ | ✅ OK |
| 6 | `id_coeficiente` | ✅ Hidden | ❌ | ✅ OK |
| 7 | `id_impuesto` | ✅ Hidden | ❌ | ✅ OK |
| 8 | `numero_linea_ppto` | ⚠️ Hidden no usado | ❌ | ⚠️ NO USADO |
| 9 | `tipo_linea_ppto` | ❌ | ❌ | ⚠️ NO USADO |
| 10 | `nivel_jerarquia` | ❌ | ❌ | ⚠️ NO USADO |
| 11 | `orden_linea_ppto` | ✅ Hidden | ✅ Columna oculta | ✅ OK |
| 12 | `codigo_linea_ppto` | ✅ Readonly | ✅ Columna visible | ✅ OK |
| 13 | `descripcion_linea_ppto` | ✅ TEXTAREA | ✅ Columna visible | ✅ OK |
| 14 | `fecha_montaje_linea_ppto` | ✅ DATE | ❌ | ✅ OK |
| 15 | `fecha_desmontaje_linea_ppto` | ✅ DATE | ❌ | ✅ OK |
| 16 | `fecha_inicio_linea_ppto` | ✅ DATE | ✅ Columna visible | ✅ OK |
| 17 | `fecha_fin_linea_ppto` | ✅ DATE | ✅ Columna visible | ✅ OK |
| 18 | `cantidad_linea_ppto` | ✅ NUMBER | ❌ | ✅ OK |
| 19 | `precio_unitario_linea_ppto` | ✅ Readonly | ❌ | ✅ OK |
| 20 | `descuento_linea_ppto` | ✅ NUMBER | ❌ | ✅ OK |
| 21 | `aplicar_coeficiente_linea_ppto` | ✅ CHECKBOX | ✅ Usado internamente | ✅ OK |
| 22 | `valor_coeficiente_linea_ppto` | ✅ Hidden + Preview | ✅ Columna visible | ✅ OK |
| 23 | `jornadas_linea_ppto` | ✅ Hidden calculado | ❌ | ✅ OK |
| 24 | `porcentaje_iva_linea_ppto` | ✅ Readonly | ❌ | ✅ OK |
| 25 | `observaciones_linea_ppto` | ✅ TEXTAREA | ❌ | ✅ OK |
| 26 | `mostrar_obs_articulo_linea_ppto` | ✅ Hidden | ❌ | ✅ OK |
| 27 | `ocultar_detalle_kit_linea_ppto` | ✅ CHECKBOX | ❌ | ✅ OK |
| 28 | `mostrar_en_presupuesto` | ✅ Hidden | ❌ | ✅ OK |
| 29 | `es_opcional` | ✅ Hidden | ❌ | ✅ OK |
| 30 | `activo_linea_ppto` | ✅ Hidden | ✅ Columna visible | ✅ OK |
| 31 | `created_at_linea_ppto` | ❌ | ❌ | ✅ OK (auditoría) |
| 32 | `updated_at_linea_ppto` | ❌ | ❌ | ✅ OK (auditoría) |
| 33 | `total_linea` (calculada en vista) | ❌ | ✅ Columna visible | ✅ OK |

---

## 🎯 RECOMENDACIONES

### Campos a Implementar (Prioridad Alta)

1. **`tipo_linea_ppto`** - Permitir líneas tipo "sección" y "texto" además de artículos
   - Agregar selector en formulario
   - Renderizar diferente en DataTables según tipo

2. **`id_linea_padre` + `nivel_jerarquia`** - Implementar jerarquía visual de KITs
   - Indentar componentes de KIT en DataTables
   - Calcular automáticamente al desglosar KIT

3. **`numero_linea_ppto`** - Decidir si usar o eliminar
   - Si se usa: Agregar control numérico en formulario
   - Si no: Eliminar campo de tabla

### Campos OK Como Están (No Cambiar)

- ✅ `created_at_linea_ppto` y `updated_at_linea_ppto` - Auditoría interna, OK no mostrar
- ✅ Campos calculados (`total_linea`, `dias_duracion`) - Se generan en backend/vista

### Campos Bien Implementados

- ✅ Sección de coeficientes funciona correctamente
- ✅ Fechas separadas para planificación vs cobro
- ✅ Herencia de datos desde artículo con posibilidad de edición

---

## 📊 VISTA SQL RELACIONADA

El módulo usa la vista `v_linea_presupuesto_calculada` que agrega campos calculados:

- `total_sin_iva` = cantidad × precio_unitario × (1 - descuento/100)
- `total_iva` = total_sin_iva × (porcentaje_iva / 100)
- `total_linea` = total_sin_iva + total_iva
- `dias_duracion` = DATEDIFF(fecha_fin, fecha_inicio)

---

**Última actualización:** 23 de enero de 2025  
**Responsable:** Luis - Innovabyte  
**Versión:** 1.0
