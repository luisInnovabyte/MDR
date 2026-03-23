# Documentación de Vistas SQL

## Introducción

Las **vistas SQL** en el proyecto MDR son consultas almacenadas que combinan datos de múltiples tablas relacionadas, proporcionando una capa de abstracción que simplifica las consultas desde los modelos PHP. Todas las vistas siguen patrones consistentes de diseño y nomenclatura.

---

## Vistas Implementadas en el Proyecto

El proyecto cuenta con las siguientes vistas SQL:

| Vista | Propósito | Tablas Relacionadas |
|-------|-----------|---------------------|
| `contacto_cantidad_cliente` | Clientes con formas de pago y cantidad de contactos | cliente, forma_pago, metodo_pago, contacto_cliente |
| `contacto_cantidad_proveedor` | Proveedores con formas de pago y cantidad de contactos | proveedor, forma_pago, metodo_pago, contacto_proveedor |
| `familia_unidad_media` | Familias con unidades de medida y grupos | familia, unidad_medida, grupo_articulo |
| `vista_articulo_completa` | Artículos con familia, grupo, unidad y campos calculados | articulo, familia, grupo_articulo, unidad_medida |
| `vista_elemento_completa` | Elementos activos con toda la jerarquía | elemento, articulo, familia, grupo_articulo, marca, estado_elemento |
| `vista_elementos_completa` | Todos los elementos (activos e inactivos) | elemento, articulo, familia, grupo_articulo, marca, estado_elemento |
| `vista_presupuesto_completa` | Presupuestos con cliente, contacto, estado, pagos y campos calculados | presupuesto, cliente, contacto_cliente, estado_presupuesto, forma_pago, metodo_pago, metodos_contacto |

---

## Patrón General de Construcción de Vistas

### 1. Estructura Básica

Todas las vistas siguen esta estructura estándar:

```sql
DROP VIEW IF EXISTS nombre_vista;

CREATE VIEW nombre_vista AS
SELECT 
    -- =====================================================
    -- =====================================================
    tabla_principal.campo1,
    tabla_principal.campo2,
    ...
    
    -- =====================================================
    -- SECCIÓN 2: DATOS DE TABLA RELACIONADA 1
    -- =====================================================
    tabla_relacionada1.campo1,
    tabla_relacionada1.campo2,
    ...
    
    -- =====================================================
    -- SECCIÓN 3: DATOS DE TABLA RELACIONADA 2
    -- =====================================================
    tabla_relacionada2.campo1,
    tabla_relacionada2.campo2,
    ...
    
    -- =====================================================
    -- SECCIÓN N: CAMPOS CALCULADOS
    -- =====================================================
    -- Cálculos con CASE, CONCAT, funciones de fecha, etc.
    CASE 
        WHEN condicion THEN resultado1
        ELSE resultado2
    END AS campo_calculado1,
    
    -- Concatenaciones para direcciones completas
    CONCAT_WS(', ', campo1, campo2, campo3) AS campo_concatenado,
    
    -- Subconsultas para contadores
    (SELECT COUNT(*) FROM tabla WHERE condicion) AS cantidad_campo

FROM tabla_principal tp
INNER JOIN tabla_relacionada1 tr1 ON tp.id_fk = tr1.id_pk
LEFT JOIN tabla_relacionada2 tr2 ON tp.id_fk2 = tr2.id_pk
...
```

### 2. Convenciones de Nomenclatura

#### Nombres de Vistas

| Tipo de Vista | Patrón | Ejemplo |
|---------------|--------|---------|
| Vista simple con relaciones | `tabla_relacion` | `familia_unidad_media` |
| Vista con contador | `contador_tabla` | `contacto_cantidad_cliente` |
| Vista completa con todas las relaciones | `vista_tabla_completa` | `vista_articulo_completa` |
| Vista con variación (activos/todos) | `vista_tabla[s]_completa` | `vista_elemento_completa` (solo activos)<br>`vista_elementos_completa` (todos) |

#### Alias en SELECT

- Todos los campos mantienen el sufijo de la tabla de origen
- Campos calculados usan sufijo de la tabla principal
- Ejemplo: `nombre_cliente`, `nombre_estado_ppto`, `direccion_completa_cliente`

---

## Patrones de Diseño Identificados

### Patrón 1: Inclusión de Todos los Campos de la Tabla Principal

✅ **Regla:** Incluir TODOS los campos de la tabla principal tal cual están.

```sql
-- =====================================================
-- DATOS DEL PRESUPUESTO
-- =====================================================
p.id_presupuesto,
p.numero_presupuesto,
p.fecha_presupuesto,
p.fecha_validez_presupuesto,
p.observaciones_cabecera_presupuesto,
p.activo_presupuesto,
p.created_at_presupuesto,
p.updated_at_presupuesto,
-- ... TODOS los campos de la tabla
```

**Justificación:** Los modelos PHP pueden necesitar cualquier campo, y es mejor tenerlos todos disponibles desde la vista.

---

### Patrón 2: Inclusión Selectiva de Tablas Relacionadas

✅ **Regla:** De las tablas relacionadas, incluir:
- Campos de identificación (ID, código)
- Campos descriptivos (nombre, descripción)
- Campos de estado (activo, color, orden)
- **OMITIR:** campos de fechas de auditoría (created_at, updated_at) de tablas relacionadas

```sql
-- =====================================================
-- DATOS DEL ESTADO DEL PRESUPUESTO
-- =====================================================
ep.id_estado_ppto,
ep.codigo_estado_ppto,        -- ✅ Incluir
ep.nombre_estado_ppto,         -- ✅ Incluir
ep.color_estado_ppto,          -- ✅ Incluir
ep.orden_estado_ppto,          -- ✅ Incluir
-- ep.created_at_estado_ppto   -- ❌ NO incluir
-- ep.updated_at_estado_ppto   -- ❌ NO incluir
```

---

### Patrón 3: Secciones Claramente Delimitadas

✅ **Regla:** Usar comentarios con líneas visuales para separar secciones.

```sql
-- =====================================================
-- SECCIÓN: DESCRIPCIÓN CLARA
-- =====================================================
campo1,
campo2,
campo3,
```

**Orden estándar de secciones:**
1. Datos de la tabla principal (completos)
2. Datos de tablas relacionadas directas (ID, códigos, nombres, estados)
3. Datos de tablas relacionadas indirectas (a través de otras tablas)
4. Subconsultas para contadores/agregaciones
5. Campos calculados

---

### Patrón 4: Campos Calculados al Final

✅ **Regla:** Todos los campos calculados van en la última sección.

**Tipos comunes de campos calculados:**

#### A) Concatenaciones de Direcciones

```sql
-- Patrón estándar para direcciones completas
CONCAT_WS(', ',
    tabla.direccion,
    CONCAT(tabla.cp, ' ', tabla.poblacion),
    tabla.provincia
) AS direccion_completa_tabla,
```

**Ejemplo real:**
```sql
CONCAT_WS(', ',
    c.direccion_cliente,
    CONCAT(c.cp_cliente, ' ', c.poblacion_cliente),
    c.provincia_cliente
) AS direccion_completa_cliente,
```

#### B) Concatenaciones de Jerarquías

```sql
-- Jerarquía completa con separador '>'
CONCAT_WS(' > ',
    COALESCE(tabla_nivel1.nombre, 'Sin nivel1'),
    tabla_nivel2.nombre,
    tabla_nivel3.nombre,
    tabla_nivel4.descripcion
) AS jerarquia_completa_entidad,
```

**Ejemplo real:**
```sql
CONCAT_WS(' > ',
    COALESCE(g.nombre_grupo, 'Sin grupo'),
    f.nombre_familia,
    a.nombre_articulo,
    e.descripcion_elemento
) AS jerarquia_completa_elemento,
```

#### C) Indicadores Booleanos

```sql
-- Para indicar si existe una condición
CASE 
    WHEN tabla.campo IS NOT NULL THEN TRUE
    ELSE FALSE
END AS tiene_campo_entidad,
```

**Ejemplo real:**
```sql
CASE 
    WHEN c.direccion_facturacion_cliente IS NOT NULL THEN TRUE
    ELSE FALSE
END AS tiene_direccion_facturacion_diferente,
```

#### D) Clasificaciones con CASE

```sql
-- Para clasificar registros en categorías
CASE 
    WHEN condicion1 THEN 'Categoría 1'
    WHEN condicion2 THEN 'Categoría 2'
    WHEN condicion3 THEN 'Categoría 3'
    ELSE 'Categoría por defecto'
END AS tipo_entidad,
```

**Ejemplo real:**
```sql
CASE 
    WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 'Pago único'
    WHEN fp.porcentaje_anticipo_pago < 100.00 THEN 'Pago fraccionado'
    ELSE 'Sin forma de pago'
END AS tipo_pago_presupuesto,
```

#### E) Estados Basados en Fechas

```sql
-- Para determinar estados según fechas
CASE 
    WHEN fecha < CURDATE() THEN 'Pasado'
    WHEN fecha = CURDATE() THEN 'Hoy'
    WHEN fecha BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL n DAY) THEN 'Próximo'
    WHEN fecha > DATE_ADD(CURDATE(), INTERVAL n DAY) THEN 'Futuro'
    ELSE 'Sin fecha'
END AS estado_fecha_entidad,
```

**Ejemplo real:**
```sql
CASE 
    WHEN p.fecha_validez_presupuesto IS NULL THEN 'Sin fecha de validez'
    WHEN p.fecha_validez_presupuesto < CURDATE() THEN 'Caducado'
    WHEN p.fecha_validez_presupuesto = CURDATE() THEN 'Caduca hoy'
    WHEN (TO_DAYS(p.fecha_validez_presupuesto) - TO_DAYS(CURDATE())) <= 7 THEN 'Próximo a caducar'
    ELSE 'Vigente'
END AS estado_validez_presupuesto,
```

#### F) Cálculos de Diferencia de Días

```sql
-- Para calcular días entre fechas
(TO_DAYS(fecha_futura) - TO_DAYS(CURDATE())) AS dias_hasta_fecha,
(TO_DAYS(CURDATE()) - TO_DAYS(fecha_pasada)) AS dias_desde_fecha,
```

**Ejemplo real:**
```sql
(TO_DAYS(p.fecha_validez_presupuesto) - TO_DAYS(CURDATE())) AS dias_validez_restantes,
(TO_DAYS(CURDATE()) - TO_DAYS(p.fecha_presupuesto)) AS dias_desde_emision,
```

#### G) Cálculos de Duración

```sql
-- Para calcular duración entre dos fechas
((TO_DAYS(fecha_fin) - TO_DAYS(fecha_inicio)) + 1) AS duracion_dias,
```

**Ejemplo real:**
```sql
((TO_DAYS(p.fecha_fin_evento_presupuesto) - TO_DAYS(p.fecha_inicio_evento_presupuesto)) + 1) AS duracion_evento_dias,
```

#### H) Cálculos de Antigüedad

```sql
-- Antigüedad en días
DATEDIFF(CURDATE(), fecha_alta) AS dias_en_servicio,

-- Antigüedad en años (redondeado)
ROUND(DATEDIFF(CURDATE(), fecha_alta) / 365.25, 2) AS anios_en_servicio,
```

**Ejemplo real:**
```sql
DATEDIFF(CURDATE(), e.fecha_alta_elemento) AS dias_en_servicio_elemento,
ROUND(DATEDIFF(CURDATE(), e.fecha_alta_elemento) / 365.25, 2) AS anios_en_servicio_elemento,
```

#### I) Descripciones Compuestas

```sql
-- Construir descripciones complejas combinando múltiples campos
CASE 
    WHEN tabla_fk.id IS NULL THEN 'Sin relación'
    WHEN condicion1 THEN 
        CONCAT(
            tabla1.campo,
            ' - ',
            tabla2.campo,
            CASE WHEN campo_opcional > 0 THEN CONCAT(' (Info: ', campo_opcional, ')') ELSE '' END
        )
    ELSE CONCAT(tabla1.campo, ' - ', campo_opcional)
END AS descripcion_completa_entidad,
```

**Ejemplo real:**
```sql
CASE 
    WHEN fp.id_pago IS NULL THEN 'Sin forma de pago asignada'
    WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 
        CONCAT(
            mp.nombre_metodo_pago,
            ' - ',
            fp.nombre_pago,
            CASE 
                WHEN fp.descuento_pago > 0 THEN CONCAT(' (Dto: ', fp.descuento_pago, '%)')
                ELSE ''
            END
        )
    ELSE 
        CONCAT(
            mp.nombre_metodo_pago,
            ' - ',
            fp.porcentaje_anticipo_pago, '% + ',
            fp.porcentaje_final_pago, '%'
        )
END AS descripcion_completa_forma_pago,
```

#### J) Cálculos de Fechas Futuras

```sql
-- Calcular fechas futuras basadas en intervalos
CASE 
    WHEN dias = 0 THEN fecha_base
    WHEN dias > 0 THEN (fecha_base + INTERVAL dias DAY)
    WHEN dias < 0 AND otra_fecha IS NOT NULL THEN (otra_fecha + INTERVAL dias DAY)
    ELSE NULL
END AS fecha_calculada,
```

**Ejemplo real:**
```sql
CASE 
    WHEN fp.dias_anticipo_pago = 0 THEN p.fecha_presupuesto
    ELSE (p.fecha_presupuesto + INTERVAL fp.dias_anticipo_pago DAY)
END AS fecha_vencimiento_anticipo,

CASE 
    WHEN fp.dias_final_pago = 0 AND p.fecha_fin_evento_presupuesto IS NOT NULL THEN p.fecha_fin_evento_presupuesto
    WHEN fp.dias_final_pago > 0 THEN (p.fecha_presupuesto + INTERVAL fp.dias_final_pago DAY)
    WHEN fp.dias_final_pago < 0 AND p.fecha_inicio_evento_presupuesto IS NOT NULL THEN (p.fecha_inicio_evento_presupuesto + INTERVAL fp.dias_final_pago DAY)
    ELSE NULL
END AS fecha_vencimiento_final,
```

---

### Patrón 5: Subconsultas para Contadores

✅ **Regla:** Usar subconsultas correlacionadas para contar registros relacionados.

```sql
-- =====================================================
-- CANTIDAD DE REGISTROS RELACIONADOS
-- =====================================================
(SELECT COUNT(tabla_relacionada.id)
 FROM tabla_relacionada
 WHERE tabla_relacionada.id_fk = tabla_principal.id_pk
) AS cantidad_registros_relacionados,
```

**Ejemplo real:**
```sql
-- =====================================================
-- CANTIDAD DE CONTACTOS
-- =====================================================
(SELECT COUNT(cc.id_contacto_cliente)
 FROM contacto_cliente cc
 WHERE cc.id_cliente = c.id_cliente
) AS cantidad_contactos_cliente,
```

---

### Patrón 6: Uso de COALESCE para Valores por Defecto

✅ **Regla:** Usar `COALESCE()` para proporcionar valores por defecto cuando un campo puede ser NULL.

```sql
-- Para textos
COALESCE(tabla.campo_opcional, 'Valor por defecto') AS campo_con_defecto,

-- Para concatenaciones que pueden tener NULL
CONCAT_WS(' > ',
    COALESCE(tabla1.nombre, 'Sin nivel1'),
    tabla2.nombre,
    tabla3.nombre
) AS jerarquia_completa,
```

**Ejemplo real:**
```sql
-- En concatenación de jerarquía
CONCAT_WS(' > ',
    COALESCE(g.nombre_grupo, 'Sin grupo'),
    f.nombre_familia,
    a.nombre_articulo
) AS jerarquia_completa,

-- Para imágenes con fallback
COALESCE(a.imagen_articulo, f.imagen_familia) AS imagen_efectiva,
```

---

### Patrón 7: Tipos de JOIN

✅ **Reglas para elegir el tipo de JOIN:**

| Tipo de JOIN | Cuándo Usar | Ejemplo |
|--------------|-------------|---------|
| `INNER JOIN` | Cuando la relación es **obligatoria** (FK NOT NULL) | `INNER JOIN cliente c ON p.id_cliente = c.id_cliente` |
| `LEFT JOIN` | Cuando la relación es **opcional** (FK permite NULL) | `LEFT JOIN contacto_cliente cc ON p.id_contacto_cliente = cc.id_contacto_cliente` |

**Ejemplo de combinación:**
```sql
FROM presupuesto p
INNER JOIN cliente c ON p.id_cliente = c.id_cliente                      -- Cliente obligatorio
LEFT JOIN contacto_cliente cc ON p.id_contacto_cliente = cc.id_contacto_cliente  -- Contacto opcional
INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto       -- Estado obligatorio
LEFT JOIN forma_pago fp ON p.id_forma_pago = fp.id_pago                  -- Forma pago opcional
```

---

### Patrón 8: WHERE en Vistas

✅ **Regla:** Las vistas pueden o no incluir cláusula WHERE:

**CON WHERE (para filtrar registros):**
```sql
FROM elemento e
INNER JOIN articulo a ON e.id_articulo_elemento = a.id_articulo
WHERE e.activo_elemento = TRUE;  -- Solo elementos activos
```

**SIN WHERE (para mostrar todos):**
```sql
FROM presupuesto p
INNER JOIN cliente c ON p.id_cliente = c.id_cliente;  -- Todos los presupuestos
```

**Convención:**
- `vista_entidad_completa` → **CON WHERE** (solo activos)
- `vista_entidades_completa` → **SIN WHERE** (todos)
- Ejemplo: `vista_elemento_completa` (solo activos) vs `vista_elementos_completa` (todos)

---

## Plantilla para Crear Nuevas Vistas

### Plantilla Básica

```sql
-- ========================================================
-- VISTA: nombre_vista
-- DESCRIPCIÓN: Breve descripción del propósito de la vista
-- ========================================================

DROP VIEW IF EXISTS nombre_vista;

CREATE VIEW nombre_vista AS
SELECT 
    -- =====================================================
    -- DATOS DE LA TABLA PRINCIPAL (Nombre Tabla)
    -- =====================================================
    tp.id_principal,
    tp.codigo_principal,
    tp.nombre_principal,
    tp.campo1_principal,
    tp.campo2_principal,
    tp.campo3_principal,
    tp.observaciones_principal,
    tp.activo_principal,
    tp.created_at_principal,
    tp.updated_at_principal,
    
    -- =====================================================
    -- DATOS DE TABLA RELACIONADA 1 (Nombre Relación 1)
    -- =====================================================
    tr1.id_relacion1,
    tr1.codigo_relacion1,
    tr1.nombre_relacion1,
    tr1.activo_relacion1,
    
    -- =====================================================
    -- DATOS DE TABLA RELACIONADA 2 (Nombre Relación 2)
    -- =====================================================
    tr2.id_relacion2,
    tr2.codigo_relacion2,
    tr2.nombre_relacion2,
    tr2.activo_relacion2,
    
    -- =====================================================
    -- CANTIDAD DE REGISTROS RELACIONADOS (si aplica)
    -- =====================================================
    (SELECT COUNT(sub.id)
     FROM tabla_secundaria sub
     WHERE sub.id_fk = tp.id_principal
    ) AS cantidad_secundaria,
    
    -- =====================================================
    -- CAMPOS CALCULADOS
    -- =====================================================
    
    -- Concatenación de dirección completa
    CONCAT_WS(', ',
        tp.direccion_principal,
        CONCAT(tp.cp_principal, ' ', tp.poblacion_principal),
        tp.provincia_principal
    ) AS direccion_completa_principal,
    
    -- Jerarquía completa
    CONCAT_WS(' > ',
        COALESCE(tr1.nombre_relacion1, 'Sin categoría'),
        tr2.nombre_relacion2,
        tp.nombre_principal
    ) AS jerarquia_completa_principal,
    
    -- Indicador booleano
    CASE 
        WHEN tp.campo_opcional IS NOT NULL THEN TRUE
        ELSE FALSE
    END AS tiene_campo_opcional,
    
    -- Clasificación con CASE
    CASE 
        WHEN tp.campo_numerico > 100 THEN 'Alto'
        WHEN tp.campo_numerico > 50 THEN 'Medio'
        ELSE 'Bajo'
    END AS nivel_principal,
    
    -- Estado basado en fechas
    CASE 
        WHEN tp.fecha_principal IS NULL THEN 'Sin fecha'
        WHEN tp.fecha_principal < CURDATE() THEN 'Vencido'
        WHEN tp.fecha_principal = CURDATE() THEN 'Hoy'
        WHEN (TO_DAYS(tp.fecha_principal) - TO_DAYS(CURDATE())) <= 7 THEN 'Próximo'
        ELSE 'Futuro'
    END AS estado_fecha_principal,
    
    -- Cálculo de días
    (TO_DAYS(tp.fecha_principal) - TO_DAYS(CURDATE())) AS dias_hasta_fecha,
    
    -- Antigüedad
    DATEDIFF(CURDATE(), tp.created_at_principal) AS dias_antiguedad

FROM tabla_principal tp
INNER JOIN tabla_relacionada1 tr1 ON tp.id_fk1 = tr1.id_relacion1
LEFT JOIN tabla_relacionada2 tr2 ON tp.id_fk2 = tr2.id_relacion2
WHERE tp.activo_principal = TRUE;  -- Opcional, según necesidad
```

---

## Caso de Estudio: Vista de Presupuestos

### Análisis de `vista_presupuesto_completa`

Esta es la vista más compleja del proyecto y ejemplifica todos los patrones:

**Tablas involucradas:** 7
- `presupuesto` (principal)
- `cliente` (INNER JOIN - obligatorio)
- `contacto_cliente` (LEFT JOIN - opcional)
- `estado_presupuesto` (INNER JOIN - obligatorio)
- `forma_pago` (LEFT JOIN - opcional)
- `metodo_pago` (LEFT JOIN - a través de forma_pago)
- `metodos_contacto` (LEFT JOIN - opcional)
- `forma_pago` de nuevo (LEFT JOIN - forma pago habitual del cliente)

**Campos totales:** ~120 campos

**Estructura:**
1. **Datos del presupuesto** (20 campos)
2. **Datos del cliente** (17 campos + 2 calculados de direcciones)
3. **Forma de pago habitual del cliente** (2 campos)
4. **Contacto del cliente** (7 campos + 1 calculado nombre completo)
5. **Estado del presupuesto** (5 campos)
6. **Forma de pago del presupuesto** (8 campos)
7. **Método de pago** (3 campos)
8. **Método de contacto** (2 campos)
9. **Total presupuesto** (1 campo placeholder)
10. **Campos calculados - Validez** (2 campos)
11. **Campos calculados - Evento** (4 campos)
12. **Campos calculados - Pagos** (4 campos)
13. **Campos calculados - Antigüedad** (1 campo)

**Campos calculados destacados:**

```sql
-- Estado de validez (4 niveles)
CASE
    WHEN p.fecha_validez_presupuesto IS NULL THEN 'Sin fecha de validez'
    WHEN p.fecha_validez_presupuesto < CURDATE() THEN 'Caducado'
    WHEN p.fecha_validez_presupuesto = CURDATE() THEN 'Caduca hoy'
    WHEN (TO_DAYS(p.fecha_validez_presupuesto) - TO_DAYS(CURDATE())) <= 7 THEN 'Próximo a caducar'
    ELSE 'Vigente'
END AS estado_validez_presupuesto,

-- Estado del evento (6 niveles)
CASE
    WHEN p.fecha_inicio_evento_presupuesto IS NULL THEN 'Sin fecha de evento'
    WHEN p.fecha_fin_evento_presupuesto < CURDATE() THEN 'Evento finalizado'
    WHEN p.fecha_inicio_evento_presupuesto <= CURDATE() AND p.fecha_fin_evento_presupuesto >= CURDATE() THEN 'Evento en curso'
    WHEN p.fecha_inicio_evento_presupuesto = CURDATE() THEN 'Evento inicia hoy'
    WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) <= 7 THEN 'Evento próximo'
    ELSE 'Evento futuro'
END AS estado_evento_presupuesto,

-- Descripción completa de forma de pago (3 niveles)
CASE
    WHEN fp.id_pago IS NULL THEN 'Sin forma de pago asignada'
    WHEN fp.porcentaje_anticipo_pago = 100.00 THEN
        CONCAT(mp.nombre_metodo_pago, ' - ', fp.nombre_pago,
            CASE WHEN fp.descuento_pago > 0 THEN CONCAT(' (Dto: ', fp.descuento_pago, '%)') ELSE '' END
        )
    ELSE CONCAT(mp.nombre_metodo_pago, ' - ', fp.porcentaje_anticipo_pago, '% + ', fp.porcentaje_final_pago, '%')
END AS descripcion_completa_forma_pago_presupuesto,

-- Fecha de vencimiento de anticipo (cálculo condicional)
CASE
    WHEN fp.dias_anticipo_pago = 0 THEN p.fecha_presupuesto
    ELSE (p.fecha_presupuesto + INTERVAL fp.dias_anticipo_pago DAY)
END AS fecha_vencimiento_anticipo,

-- Fecha de vencimiento final (cálculo complejo con 3 condiciones)
CASE
    WHEN fp.dias_final_pago = 0 AND p.fecha_fin_evento_presupuesto IS NOT NULL THEN p.fecha_fin_evento_presupuesto
    WHEN fp.dias_final_pago > 0 THEN (p.fecha_presupuesto + INTERVAL fp.dias_final_pago DAY)
    WHEN fp.dias_final_pago < 0 AND p.fecha_inicio_evento_presupuesto IS NOT NULL THEN (p.fecha_inicio_evento_presupuesto + INTERVAL fp.dias_final_pago DAY)
    ELSE NULL
END AS fecha_vencimiento_final
```

---

## Beneficios de las Vistas

### ✅ **1. Simplificación de Consultas en Modelos**

**Sin vista:**
```php
// En el modelo, necesitarías hacer múltiples JOINs cada vez
$sql = "SELECT p.*, c.nombre_cliente, c.email_cliente, 
        ep.nombre_estado_ppto, fp.nombre_pago, mp.nombre_metodo_pago,
        cc.nombre_contacto_cliente, cc.apellidos_contacto_cliente,
        CONCAT_WS(', ', c.direccion_cliente, ...) AS direccion_completa
        FROM presupuesto p
        INNER JOIN cliente c ON p.id_cliente = c.id_cliente
        LEFT JOIN contacto_cliente cc ON p.id_contacto_cliente = cc.id_contacto_cliente
        INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
        LEFT JOIN forma_pago fp ON p.id_forma_pago = fp.id_pago
        LEFT JOIN metodo_pago mp ON fp.id_metodo_pago = mp.id_metodo_pago
        WHERE p.id_presupuesto = :id";
```

**Con vista:**
```php
// ¡Mucho más simple!
$sql = "SELECT * FROM vista_presupuesto_completa WHERE id_presupuesto = :id";
```

### ✅ **2. Reutilización de Lógica**

Los campos calculados se definen una vez en la vista y se reutilizan en todos los modelos:
- `estado_validez_presupuesto`
- `dias_hasta_inicio_evento`
- `descripcion_completa_forma_pago`
- `jerarquia_completa_elemento`

### ✅ **3. Mantenimiento Centralizado**

Si cambia la lógica de un campo calculado, solo se modifica la vista, no cada consulta en los modelos.

### ✅ **4. Abstracción de Complejidad**

El modelo PHP no necesita conocer la complejidad de los JOINS ni los cálculos; solo consulta la vista.

### ✅ **5. Optimización de Rendimiento**

MySQL optimiza internamente las vistas, y los índices de las tablas base se aprovechan automáticamente.

---

## Buenas Prácticas

### ✅ **DO (Hacer)**

1. **Usar `DROP VIEW IF EXISTS` antes de crear** para evitar errores
2. **Incluir TODOS los campos de la tabla principal**
3. **Organizar campos en secciones con comentarios visuales**
4. **Usar alias descriptivos para los campos calculados**
5. **Incluir sufijo de tabla en todos los campos** (`_cliente`, `_presupuesto`, `_elemento`)
6. **Poner campos calculados al final**
7. **Usar `COALESCE()` para valores por defecto en concatenaciones**
8. **Documentar casos especiales con comentarios inline**
9. **Usar `LEFT JOIN` para relaciones opcionales, `INNER JOIN` para obligatorias**
10. **Nombrar vistas descriptivamente**

### ❌ **DON'T (No hacer)**

1. **NO incluir created_at/updated_at de tablas relacionadas** (solo de la principal)
2. **NO hacer cálculos pesados que ralenticen la vista** (dejar para consultas específicas)
3. **NO usar `SELECT *` en subconsultas** (especificar campos necesarios)
4. **NO omitir el prefijo/sufijo de tabla en campos**
5. **NO mezclar campos calculados con campos directos** (separarlos en secciones)
6. **NO usar vistas para operaciones transaccionales** (INSERT/UPDATE/DELETE)
7. **NO crear vistas con datos sensibles sin controlar acceso**
8. **NO duplicar lógica de cálculo** (definir una vez en la vista)

---

## Checklist para Crear una Nueva Vista

Antes de crear una vista, asegúrate de:

- [ ] Identificar la tabla principal y todas las tablas relacionadas necesarias
- [ ] Definir qué campos de cada tabla se necesitan
- [ ] Identificar campos calculados que se reutilizarán
- [ ] Determinar si la relación es obligatoria (INNER) u opcional (LEFT)
- [ ] Decidir si la vista incluirá filtro WHERE (ej: solo activos)
- [ ] Nombrar la vista siguiendo convenciones del proyecto
- [ ] Organizar campos en secciones con comentarios visuales
- [ ] Poner `DROP VIEW IF EXISTS` antes del CREATE
- [ ] Incluir todos los campos de la tabla principal
- [ ] Omitir created_at/updated_at de tablas relacionadas
- [ ] Definir campos calculados al final
- [ ] Usar alias descriptivos con sufijos de tabla
- [ ] Probar la vista con consultas SELECT
- [ ] Documentar la vista en este archivo

---

## Consultas de Ejemplo con Vistas

### Listar Todos los Registros

```sql
SELECT * FROM vista_presupuesto_completa
WHERE activo_presupuesto = TRUE
ORDER BY fecha_presupuesto DESC;
```

### Obtener un Registro Específico

```sql
SELECT * FROM vista_presupuesto_completa
WHERE id_presupuesto = 42;
```

### Filtrar por Campo Calculado

```sql
SELECT 
    numero_presupuesto,
    nombre_cliente,
    estado_validez_presupuesto,
    dias_validez_restantes
FROM vista_presupuesto_completa
WHERE estado_validez_presupuesto IN ('Caduca hoy', 'Próximo a caducar')
ORDER BY dias_validez_restantes;
```

### Agrupar y Contar

```sql
SELECT 
    nombre_estado_ppto,
    COUNT(*) as total
FROM vista_presupuesto_completa
WHERE activo_presupuesto = TRUE
GROUP BY id_estado_ppto, nombre_estado_ppto
ORDER BY total DESC;
```

---

## Relación con Modelos PHP

Las vistas se utilizan en los métodos `get_entidades()` de los modelos:

```php
// En models/Presupuesto.php
public function get_presupuestos()
{
    $sql = "SELECT * FROM vista_presupuesto_completa 
            WHERE activo_presupuesto = TRUE
            ORDER BY fecha_presupuesto DESC";
    
    return $this->conexion->obtenerTodos($sql);
}

public function get_presupuestoxid($id)
{
    $sql = "SELECT * FROM vista_presupuesto_completa 
            WHERE id_presupuesto = :id";
    
    $params = [':id' => $id];
    return $this->conexion->obtenerUno($sql, $params);
}
```

**Ventaja:** El modelo no necesita saber cómo se construyen los JOINs ni los campos calculados.

---

## Resumen

Las **vistas SQL** en el proyecto MDR siguen patrones consistentes que:

1. ✅ **Simplifican las consultas** en los modelos PHP
2. ✅ **Centralizan la lógica** de campos calculados
3. ✅ **Organizan la información** en secciones claramente delimitadas
4. ✅ **Incluyen todos los campos** de la tabla principal
5. ✅ **Seleccionan campos relevantes** de tablas relacionadas
6. ✅ **Calculan campos derivados** al final de la vista
7. ✅ **Usan nomenclatura consistente** con sufijos de tabla
8. ✅ **Aplican convenciones claras** para JOINS y WHERE

Siguiendo estos patrones, las nuevas vistas se integrarán perfectamente con la arquitectura existente del proyecto.

---

## Enlaces Relacionados

- [Documentación de Models](models.md) - Cómo los modelos usan las vistas
- [Documentación de Controllers](controller.md) - Flujo de datos desde vistas SQL hasta la interfaz
- [Documentación de Conexión](conexion.md) - Sistema de consultas con PDO
- [Estructura de Carpetas](estructura_carpetas.md) - Arquitectura general del proyecto
