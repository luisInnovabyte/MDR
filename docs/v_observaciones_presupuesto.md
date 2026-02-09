# Vista: `v_observaciones_presupuesto`

> **Fecha de creación**: 6 de febrero de 2026  
> **Autor**: Luis - MDR ERP Manager  
> **Versión**: 1.1 (Actualizada)

---

## 🤖 EXPLICACIÓN PARA ASISTENTES DE IA

Esta sección está diseñada para que otras IAs (como GitHub Copilot, Claude, GPT, etc.) comprendan rápidamente la estructura y propósito de esta vista.

### Contexto del Sistema

**Sistema**: MDR ERP Manager - Sistema de gestión de alquiler de equipos audiovisuales  
**Base de datos**: MySQL 8.0+  
**Charset**: `utf8mb4_spanish_ci` o `utf8mb4_spanish2_ci`  
**Empresa**: MDR Audiovisuales S.L. (España)

### Problema que Resuelve

En el sistema de presupuestos:
1. Los **artículos** pueden tener observaciones específicas (ej: "Requiere alimentación 220V")
2. Las **familias** de artículos pueden tener observaciones generales (ej: "Todos los equipos de sonido incluyen cables XLR")
3. Cada **presupuesto** tiene flags para controlar qué observaciones mostrar
4. Se necesita una lista ÚNICA (sin duplicados) de observaciones por presupuesto
5. Las observaciones deben estar ordenadas (familias primero, artículos después)
6. Soporte bilingüe: español e inglés

### Arquitectura de Datos

```
presupuesto (tabla principal)
    ├── id_presupuesto
    ├── mostrar_obs_familias_presupuesto (TINYINT: 0/1)
    ├── mostrar_obs_articulos_presupuesto (TINYINT: 0/1)
    └── version_actual_presupuesto (INT)
    
presupuesto_version (versiones del presupuesto)
    ├── id_version_presupuesto
    ├── id_presupuesto (FK)
    └── numero_version_presupuesto
    
linea_presupuesto (líneas/items del presupuesto)
    ├── id_linea_ppto
    ├── id_version_presupuesto (FK)
    ├── id_articulo (FK)
    ├── mostrar_obs_articulo_linea_ppto (TINYINT: 0/1)
    └── activo_linea_ppto (TINYINT: 0/1)
    
articulo (artículos/productos)
    ├── id_articulo
    ├── id_familia (FK)
    ├── notas_presupuesto_articulo (TEXT: observación ES)
    ├── notes_budget_articulo (TEXT: observación EN)
    ├── orden_obs_articulo (INT: default 200)
    └── activo_articulo (TINYINT: 0/1)
    
familia (categorías de artículos)
    ├── id_familia
    ├── observaciones_presupuesto_familia (TEXT: observación ES)
    ├── observations_budget_familia (TEXT: observación EN)
    ├── orden_obs_familia (INT: default 100)
    └── activo_familia (TINYINT: 0/1)
```

### Flujo de Datos de la Vista

```sql
-- QUERY 1: FAMILIAS
presupuesto 
  → vista_presupuesto_completa (obtener nombre_cliente)
  → presupuesto_version (filtrar por versión actual)
  → linea_presupuesto (obtener líneas activas)
  → articulo (obtener artículos de las líneas)
  → familia (obtener familias de los artículos)
  WHERE mostrar_obs_familias_presupuesto = 1
  GROUP BY id_presupuesto, id_familia  -- ÚNICO por familia

UNION ALL

-- QUERY 2: ARTÍCULOS
presupuesto 
  → vista_presupuesto_completa (obtener nombre_cliente)
  → presupuesto_version (filtrar por versión actual)
  → linea_presupuesto (obtener líneas activas)
  → articulo (obtener artículos)
  WHERE mostrar_obs_articulos_presupuesto = 1
    AND mostrar_obs_articulo_linea_ppto = 1
  GROUP BY id_presupuesto, id_articulo  -- ÚNICO por artículo

ORDER BY id_presupuesto, orden_observacion
```

### Garantía de Unicidad

**CRÍTICO**: La vista usa `GROUP BY` para garantizar que:
- Cada **familia** aparece **máximo 1 vez** por presupuesto
- Cada **artículo** aparece **máximo 1 vez** por presupuesto

Ejemplo:
```
Presupuesto #123:
  - Línea 1: Foco LED (familia: Iluminación)
  - Línea 2: Foco LED (familia: Iluminación)  ← DUPLICADO
  - Línea 3: Mesa XLR (familia: Sonido)
  
Resultado de la vista:
  ✅ 1 observación de familia "Iluminación" (no 2)
  ✅ 1 observación de familia "Sonido"
  ✅ 1 observación de artículo "Foco LED" (no 2)
  ✅ 1 observación de artículo "Mesa XLR"
```

### Campos de Salida de la Vista

| Campo | Tipo | Descripción | Ejemplo |
|-------|------|-------------|---------|
| `id_presupuesto` | INT | ID del presupuesto | 123 |
| `id_familia` | INT/NULL | ID familia (NULL si es artículo) | 5 |
| `id_articulo` | INT/NULL | ID artículo (NULL si es familia) | 42 |
| `tipo_observacion` | VARCHAR | 'familia' o 'articulo' | 'familia' |
| `codigo_familia`/`codigo_articulo` | VARCHAR | Código del elemento | 'ILU-001' |
| `nombre_familia`/`nombre_articulo` | VARCHAR | Nombre español | 'Iluminación' |
| `name_familia`/`name_articulo` | VARCHAR | Nombre inglés | 'Lighting' |
| `observacion_es` | TEXT | Observación español | 'Incluye cables...' |
| `observacion_en` | TEXT | Observación inglés | 'Includes cables...' |
| `orden_observacion` | INT | Orden presentación | 100 |
| `mostrar_observacion` | TINYINT | Flag visibilidad | 1 |
| `numero_presupuesto` | VARCHAR | Nº presupuesto | 'PPTO-2026-001' |
| `nombre_evento_presupuesto` | VARCHAR | Nombre evento | 'Concierto Rock' |
| `id_cliente` | INT | ID cliente | 7 |
| `nombre_cliente` | VARCHAR | Nombre cliente | 'Hotel Palace' |
| `activo_origen` | TINYINT | Si origen está activo | 1 |
| `activo_presupuesto` | TINYINT | Si presupuesto activo | 1 |

### Lógica de Filtrado

```python
# Pseudocódigo de la lógica de la vista

for presupuesto in presupuestos_activos:
    # PASO 1: Obtener observaciones de FAMILIAS
    if presupuesto.mostrar_obs_familias_presupuesto == 1:
        familias_usadas = get_familias_de_articulos_en_presupuesto(presupuesto)
        for familia in familias_usadas:
            if familia.tiene_observaciones() and familia.activo:
                yield {
                    'tipo': 'familia',
                    'observacion_es': familia.observaciones_presupuesto_familia,
                    'observacion_en': familia.observations_budget_familia,
                    'orden': familia.orden_obs_familia  # típicamente 100
                }
    
    # PASO 2: Obtener observaciones de ARTÍCULOS
    if presupuesto.mostrar_obs_articulos_presupuesto == 1:
        articulos_usados = get_articulos_en_presupuesto(presupuesto)
        for articulo in articulos_usados:
            # Verificar flag de línea
            linea = get_linea_del_articulo(articulo, presupuesto)
            if linea.mostrar_obs_articulo_linea_ppto == 1:
                if articulo.tiene_observaciones() and articulo.activo:
                    yield {
                        'tipo': 'articulo',
                        'observacion_es': articulo.notas_presupuesto_articulo,
                        'observacion_en': articulo.notes_budget_articulo,
                        'orden': articulo.orden_obs_articulo  # típicamente 200
                    }

# PASO 3: Ordenar resultados
results.sort(by=['id_presupuesto', 'orden_observacion', 'tipo_observacion'])
```

### Casos de Uso Típicos

**1. Generar PDF de presupuesto:**
```sql
SELECT 
    tipo_observacion,
    COALESCE(nombre_familia, nombre_articulo) AS nombre,
    observacion_es
FROM v_observaciones_presupuesto
WHERE id_presupuesto = ?
  AND observacion_es IS NOT NULL
ORDER BY orden_observacion;
```

**2. API REST (JSON):**
```sql
SELECT JSON_OBJECT(
    'tipo', tipo_observacion,
    'nombre_es', COALESCE(nombre_familia, nombre_articulo),
    'nombre_en', COALESCE(name_familia, name_articulo),
    'texto_es', observacion_es,
    'texto_en', observacion_en
) AS observacion
FROM v_observaciones_presupuesto
WHERE id_presupuesto = ?;
```

**3. Contar observaciones:**
```sql
SELECT 
    COUNT(*) AS total,
    SUM(tipo_observacion = 'familia') AS total_familias,
    SUM(tipo_observacion = 'articulo') AS total_articulos
FROM v_observaciones_presupuesto
WHERE id_presupuesto = ?;
```

### Dependencias

**Tablas requeridas:**
- `presupuesto`
- `presupuesto_version`
- `linea_presupuesto`
- `articulo`
- `familia`
- `cliente` (indirectamente vía `vista_presupuesto_completa`)

**Vistas requeridas:**
- `vista_presupuesto_completa` (proporciona `nombre_cliente`)

**Índices recomendados** (ver archivo `indices_y_pruebas_observaciones.sql`):
- `idx_presupuesto_mostrar_obs`
- `idx_familia_orden_obs`
- `idx_articulo_orden_obs`
- `idx_linea_ppto_mostrar_obs`

### Convenciones del Proyecto

1. **Nomenclatura**: `v_<<nombre>>` para vistas
2. **Sufijos**: Todos los campos terminan en `_<<tabla>>` (ej: `nombre_cliente`, `codigo_familia`)
3. **Charset**: `utf8mb4_spanish_ci` (soporta ñ, acentos, emojis)
4. **Campos obligatorios en tablas**: `id_`, `activo_`, `created_at_`, `updated_at_`
5. **Soft delete**: Usar `activo_<<tabla>> = 0` en lugar de DELETE
6. **Bilingüe**: Campos duplicados con sufijo `_es` (español) sin sufijo y `_en` (inglés) con sufijo

### Errores Comunes a Evitar

❌ **Error**: Usar `p.nombre_cliente`
✅ **Correcto**: Usar `vp.nombre_cliente` (desde vista_presupuesto_completa)

❌ **Error**: Olvidar campos en GROUP BY (MySQL strict mode)
✅ **Correcto**: Incluir TODOS los campos no agregados en GROUP BY

❌ **Error**: No filtrar por `activo_<<tabla>>`
✅ **Correcto**: Siempre filtrar registros inactivos

❌ **Error**: Asumir que familia/artículo siempre tienen observaciones
✅ **Correcto**: Verificar `IS NOT NULL` antes de usar

### Pruebas Sugeridas

```sql
-- Test 1: Verificar unicidad
SELECT id_presupuesto, id_familia, id_articulo, COUNT(*) AS cnt
FROM v_observaciones_presupuesto
GROUP BY id_presupuesto, id_familia, id_articulo
HAVING COUNT(*) > 1;
-- Debe retornar 0 filas

-- Test 2: Verificar orden
SELECT id_presupuesto, tipo_observacion, orden_observacion
FROM v_observaciones_presupuesto
WHERE id_presupuesto = 1
ORDER BY orden_observacion;
-- Familias (100) deben aparecer antes que artículos (200)

-- Test 3: Verificar filtrado por flags
SELECT COUNT(*) 
FROM v_observaciones_presupuesto vop
JOIN presupuesto p ON vop.id_presupuesto = p.id_presupuesto
WHERE (vop.tipo_observacion = 'familia' AND p.mostrar_obs_familias_presupuesto = 0)
   OR (vop.tipo_observacion = 'articulo' AND p.mostrar_obs_articulos_presupuesto = 0);
-- Debe retornar 0 (no debe haber obs. cuando flag = 0)
```

---

## 📋 DESCRIPCIÓN

Vista que consolida todas las observaciones (de familias y artículos) que deben mostrarse en cada presupuesto, respetando los flags de control de visibilidad y aplicando el ordenamiento correcto.

---

## 🎯 PROPÓSITO

La vista `v_observaciones_presupuesto` resuelve la necesidad de:

1. **Consolidar observaciones** de familias y artículos en una sola consulta
2. **Respetar flags de visibilidad** a nivel de presupuesto
3. **Aplicar ordenamiento correcto** según `orden_obs_familia` y `orden_obs_articulo`
4. **Evitar duplicados** cuando un artículo/familia aparece múltiples veces en el presupuesto
5. **Soporte bilingüe** con campos en español e inglés

---

## 📊 ESTRUCTURA DE LA VISTA

### Campos Principales

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_presupuesto` | `INT` | ID del presupuesto |
| `id_familia` | `INT` | ID de la familia (NULL si es observación de artículo) |
| `id_articulo` | `INT` | ID del artículo (NULL si es observación de familia) |
| `tipo_observacion` | `ENUM` | 'familia' o 'articulo' |
| `codigo_familia` / `codigo_articulo` | `VARCHAR` | Código del elemento |
| `nombre_familia` / `nombre_articulo` | `VARCHAR` | Nombre en español |
| `name_familia` / `name_articulo` | `VARCHAR` | Nombre en inglés |
| `observacion_es` | `TEXT` | Observación en español |
| `observacion_en` | `TEXT` | Observación en inglés |
| `orden_observacion` | `INT` | Orden de presentación |
| `mostrar_observacion` | `TINYINT(1)` | Flag de visibilidad |
| `numero_presupuesto` | `VARCHAR` | Número del presupuesto |
| `nombre_evento_presupuesto` | `VARCHAR` | Nombre del evento |
| `id_cliente` | `INT` | ID del cliente |
| `nombre_cliente` | `VARCHAR` | Nombre del cliente |
| `activo_origen` | `TINYINT(1)` | Si el origen (familia/artículo) está activo |
| `activo_presupuesto` | `TINYINT(1)` | Si el presupuesto está activo |

---

## 🔄 LÓGICA DE LA VISTA

### Estructura UNION ALL

La vista utiliza **UNION ALL** para combinar dos consultas:

```sql
SELECT ... FROM ... -- Observaciones de FAMILIAS
UNION ALL
SELECT ... FROM ... -- Observaciones de ARTÍCULOS
ORDER BY id_presupuesto, orden_observacion, tipo_observacion
```

### Primera Query: Observaciones de Familias

**Condiciones:**
- ✅ Presupuesto activo (`activo_presupuesto = 1`)
- ✅ Flag activado (`mostrar_obs_familias_presupuesto = 1`)
- ✅ Familia tiene observaciones (al menos en un idioma)
- ✅ Familia está activa (`activo_familia = 1`)
- ✅ **GROUP BY** para evitar duplicados de familias

**Origen de datos:**
```
presupuesto → presupuesto_version → linea_presupuesto → articulo → familia
```

### Segunda Query: Observaciones de Artículos

**Condiciones:**
- ✅ Presupuesto activo (`activo_presupuesto = 1`)
- ✅ Flag activado (`mostrar_obs_articulos_presupuesto = 1`)
- ✅ Artículo tiene observaciones (al menos en un idioma)
- ✅ Artículo está activo (`activo_articulo = 1`)
- ✅ Línea permite mostrar obs. (`mostrar_obs_articulo_linea_ppto = 1`)
- ✅ **GROUP BY** para evitar duplicados de artículos

**Origen de datos:**
```
presupuesto → presupuesto_version → linea_presupuesto → articulo
```

---

## 📐 ORDENAMIENTO

El ordenamiento final se aplica sobre el resultado del UNION:

```sql
ORDER BY 
    id_presupuesto,       -- Agrupa por presupuesto
    orden_observacion,    -- Orden numérico (100, 200, etc.)
    tipo_observacion      -- Desempate: 'articulo' < 'familia' alfabéticamente
```

### Valores Típicos de Orden

| Tipo | Campo de Orden | Valor por Defecto |
|------|----------------|-------------------|
| Familia | `orden_obs_familia` | **100** |
| Artículo | `orden_obs_articulo` | **200** |

**Resultado:** Las observaciones de familias aparecen primero (100), luego las de artículos (200).

---

## 💡 CASOS DE USO

### Caso 1: Presupuesto con Ambos Flags Activados

```
Presupuesto #123
├── mostrar_obs_familias_presupuesto = 1
└── mostrar_obs_articulos_presupuesto = 1

Resultado de la vista:
1. [familia] Sonido - Orden: 100
2. [familia] Iluminación - Orden: 100
3. [articulo] Mesa de Mezclas XLR - Orden: 200
4. [articulo] Foco LED 100W - Orden: 200
```

### Caso 2: Solo Observaciones de Familias

```
Presupuesto #124
├── mostrar_obs_familias_presupuesto = 1
└── mostrar_obs_articulos_presupuesto = 0

Resultado de la vista:
1. [familia] Sonido - Orden: 100
2. [familia] Iluminación - Orden: 100
```

### Caso 3: Solo Observaciones de Artículos

```
Presupuesto #125
├── mostrar_obs_familias_presupuesto = 0
└── mostrar_obs_articulos_presupuesto = 1

Resultado de la vista:
1. [articulo] Mesa de Mezclas XLR - Orden: 200
2. [articulo] Foco LED 100W - Orden: 200
```

### Caso 4: Sin Observaciones

```
Presupuesto #126
├── mostrar_obs_familias_presupuesto = 0
└── mostrar_obs_articulos_presupuesto = 0

Resultado de la vista:
(vacío - no se muestra ninguna observación)
```

---

## 🔍 EJEMPLOS DE CONSULTA

### Ejemplo 1: Todas las Observaciones de un Presupuesto

```sql
SELECT * 
FROM v_observaciones_presupuesto 
WHERE id_presupuesto = 123
ORDER BY orden_observacion;
```

### Ejemplo 2: Solo Observaciones en Español

```sql
SELECT 
    tipo_observacion,
    COALESCE(codigo_familia, codigo_articulo) AS codigo,
    COALESCE(nombre_familia, nombre_articulo) AS nombre,
    observacion_es,
    orden_observacion
FROM v_observaciones_presupuesto 
WHERE id_presupuesto = 123 
  AND observacion_es IS NOT NULL
ORDER BY orden_observacion;
```

### Ejemplo 3: Solo Observaciones en Inglés

```sql
SELECT 
    tipo_observacion,
    COALESCE(codigo_familia, codigo_articulo) AS codigo,
    COALESCE(name_familia, name_articulo) AS name,
    observacion_en,
    orden_observacion
FROM v_observaciones_presupuesto 
WHERE id_presupuesto = 123 
  AND observacion_en IS NOT NULL
ORDER BY orden_observacion;
```

### Ejemplo 4: Estadísticas de Observaciones

```sql
SELECT 
    id_presupuesto,
    numero_presupuesto,
    nombre_evento_presupuesto,
    COUNT(*) AS total_observaciones,
    SUM(CASE WHEN tipo_observacion = 'familia' THEN 1 ELSE 0 END) AS obs_familias,
    SUM(CASE WHEN tipo_observacion = 'articulo' THEN 1 ELSE 0 END) AS obs_articulos,
    SUM(CASE WHEN observacion_es IS NOT NULL THEN 1 ELSE 0 END) AS obs_con_espanol,
    SUM(CASE WHEN observacion_en IS NOT NULL THEN 1 ELSE 0 END) AS obs_con_ingles
FROM v_observaciones_presupuesto
GROUP BY id_presupuesto, numero_presupuesto, nombre_evento_presupuesto;
```

### Ejemplo 5: Listado para Impresión (Español)

```sql
SELECT 
    CONCAT(
        UPPER(tipo_observacion), 
        ': ', 
        COALESCE(nombre_familia, nombre_articulo)
    ) AS titulo,
    observacion_es AS texto
FROM v_observaciones_presupuesto 
WHERE id_presupuesto = 123 
  AND observacion_es IS NOT NULL
ORDER BY orden_observacion;
```

**Salida ejemplo:**
```
FAMILIA: Sonido
  Todos los equipos de sonido incluyen cables XLR profesionales...

FAMILIA: Iluminación
  Se incluye técnico especializado para programación de luces...

ARTICULO: Mesa de Mezclas Behringer X32
  Requiere alimentación 220V trifásica...
```

### Ejemplo 6: Listado para Impresión (Inglés)

```sql
SELECT 
    CONCAT(
        UPPER(tipo_observacion), 
        ': ', 
        COALESCE(name_familia, name_articulo)
    ) AS title,
    observacion_en AS text
FROM v_observaciones_presupuesto 
WHERE id_presupuesto = 123 
  AND observacion_en IS NOT NULL
ORDER BY orden_observacion;
```

---

## 🎨 USO EN FRONTEND

### Ejemplo en PHP

```php
<?php
// Controller: obtener observaciones del presupuesto
public function obtenerObservaciones($id_presupuesto, $idioma = 'es') {
    $campo_observacion = ($idioma === 'en') ? 'observacion_en' : 'observacion_es';
    $campo_nombre = ($idioma === 'en') ? 
        'COALESCE(name_familia, name_articulo)' : 
        'COALESCE(nombre_familia, nombre_articulo)';
    
    $sql = "SELECT 
                tipo_observacion,
                {$campo_nombre} AS nombre,
                {$campo_observacion} AS observacion,
                orden_observacion
            FROM v_observaciones_presupuesto 
            WHERE id_presupuesto = :id_presupuesto 
              AND {$campo_observacion} IS NOT NULL
            ORDER BY orden_observacion";
    
    $stmt = $this->db->prepare($sql);
    $stmt->execute(['id_presupuesto' => $id_presupuesto]);
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>
```

### Ejemplo en JavaScript (DataTables)

```javascript
// Cargar observaciones en un DataTable
$('#tableObservaciones').DataTable({
    ajax: {
        url: 'api/presupuesto/observaciones.php',
        data: { id_presupuesto: 123, idioma: 'es' }
    },
    columns: [
        { 
            data: 'tipo_observacion',
            render: function(data) {
                return data === 'familia' 
                    ? '<span class="badge bg-success">Familia</span>'
                    : '<span class="badge bg-info">Artículo</span>';
            }
        },
        { data: 'nombre' },
        { data: 'observacion' },
        { data: 'orden_observacion' }
    ],
    order: [[3, 'asc']] // Ordenar por orden_observacion
});
```

---

## ⚠️ CONSIDERACIONES IMPORTANTES

### 1. Rendimiento

- **GROUP BY** evita duplicados pero puede afectar rendimiento en presupuestos muy grandes
- Se recomienda crear índices en las tablas base (ver comentarios en el SQL)
- Para presupuestos con >1000 líneas, considerar cachear los resultados

### 2. Versiones de Presupuesto

- La vista **solo considera la versión actual** del presupuesto
- Si se necesitan observaciones de versiones históricas, modificar el JOIN con `presupuesto_version`

### 3. Observaciones Vacías

- La vista **filtra** elementos sin observaciones (NULL en ambos idiomas)
- Si un artículo tiene solo observación en inglés, se incluye aunque `observacion_es` sea NULL

### 4. Flag de Línea

- Para artículos, se respeta `linea_presupuesto.mostrar_obs_articulo_linea_ppto`
- Permite ocultar observaciones de artículos específicos aunque el presupuesto tenga el flag general activado

---

## 📌 MANTENIMIENTO

### Modificar Ordenamiento

Para cambiar el ordenamiento por defecto de familias/artículos, modificar en las tablas base:

```sql
-- Cambiar orden por defecto de familias
ALTER TABLE familia 
MODIFY orden_obs_familia INT DEFAULT 50;

-- Cambiar orden por defecto de artículos  
ALTER TABLE articulo 
MODIFY orden_obs_articulo INT DEFAULT 150;
```

### Añadir Filtros Adicionales

Para añadir más condiciones (ej: solo mostrar en presupuestos aprobados):

```sql
WHERE 
    p.activo_presupuesto = 1
    AND p.id_estado_ppto = 3 -- Solo aprobados
    AND p.mostrar_obs_familias_presupuesto = 1
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

- [x] Vista creada con nomenclatura estándar (`v_observaciones_presupuesto`)
- [x] UNION ALL para combinar familias y artículos
- [x] Respeta flags de visibilidad del presupuesto
- [x] Ordenamiento por `orden_obs_familia` y `orden_obs_articulo`
- [x] Evita duplicados con GROUP BY
- [x] Soporte bilingüe (español/inglés)
- [x] Campos de control incluidos
- [x] Documentación completa
- [x] Ejemplos de uso proporcionados
- [ ] Índices creados en tablas base (recomendado)
- [ ] Pruebas con datos reales
- [ ] Integración en controllers PHP
- [ ] Integración en frontend JavaScript

---

**Documento**: `v_observaciones_presupuesto.md`  
**Sistema**: MDR ERP Manager  
**Última actualización**: 6 de febrero de 2026
