# Documentación de Vistas SQL

> Estándar de diseño, nomenclatura y construcción de vistas SQL para el proyecto

---

## Introducción

Las **vistas SQL** son consultas almacenadas que combinan datos de múltiples tablas relacionadas, proporcionando una capa de abstracción que simplifica las consultas desde los modelos PHP. Todas las vistas siguen patrones consistentes de diseño y nomenclatura.

---

## Nomenclatura de Vistas

### Convenciones de Nombres

| Tipo de Vista | Patrón | Ejemplo |
|---------------|--------|---------|
| Vista completa con todas las relaciones | `vista_<<tabla>>_completa` | `vista_articulo_completa` |
| Vista con filtro específico | `vista_<<tabla>>_<<filtro>>` | `vista_elemento_activos` |
| Vista con contador | `<<tabla>>_cantidad_<<relacion>>` | `contacto_cantidad_cliente` |
| Vista simple con relaciones | `<<tabla>>_<<relacion>>` | `familia_unidad_media` |
| Vista todos los registros (sin filtro) | `vista_<<tablas>>_completa` | `vista_elementos_completa` |

**Convención singular/plural:**
- `vista_elemento_completa` → **Solo activos** (WHERE activo = 1)
- `vista_elementos_completa` → **Todos** (sin filtro WHERE)

---

## Estructura Estándar de una Vista

```sql
-- ========================================================
-- VISTA: vista_<<tabla>>_completa
-- DESCRIPCIÓN: <<Propósito de la vista>>
-- DEPENDENCIAS: <<Lista de tablas/vistas que utiliza>>
-- ========================================================

DROP VIEW IF EXISTS vista_<<tabla>>_completa;

CREATE VIEW vista_<<tabla>>_completa AS
SELECT 
    -- =====================================================
    -- DATOS DE LA TABLA PRINCIPAL
    -- =====================================================
    tp.id_<<tabla>>,
    tp.codigo_<<tabla>>,
    tp.nombre_<<tabla>>,
    -- ... todos los campos de la tabla principal
    tp.activo_<<tabla>>,
    tp.created_at_<<tabla>>,
    tp.updated_at_<<tabla>>,
    
    -- =====================================================
    -- DATOS DE TABLA RELACIONADA 1
    -- =====================================================
    tr1.id_<<relacion1>>,
    tr1.codigo_<<relacion1>>,
    tr1.nombre_<<relacion1>>,
    -- NO incluir: created_at, updated_at de tablas relacionadas
    
    -- =====================================================
    -- DATOS DE TABLA RELACIONADA 2
    -- =====================================================
    tr2.id_<<relacion2>>,
    tr2.nombre_<<relacion2>>,
    
    -- =====================================================
    -- SUBCONSULTAS PARA CONTADORES (si aplica)
    -- =====================================================
    (SELECT COUNT(sub.id_<<subtabla>>)
     FROM <<subtabla>> sub
     WHERE sub.id_<<tabla>> = tp.id_<<tabla>>
    ) AS cantidad_<<subtabla>>,
    
    -- =====================================================
    -- CAMPOS CALCULADOS
    -- =====================================================
    -- Concatenaciones, CASE, cálculos de fechas, etc.

FROM <<tabla_principal>> tp
INNER JOIN <<tabla_relacionada1>> tr1 ON tp.id_<<fk1>> = tr1.id_<<pk1>>
LEFT JOIN <<tabla_relacionada2>> tr2 ON tp.id_<<fk2>> = tr2.id_<<pk2>>
WHERE tp.activo_<<tabla>> = 1;  -- Opcional según tipo de vista
```

---

## Patrones de Diseño

### Patrón 1: Inclusión de Campos

#### Tabla Principal
✅ **Incluir TODOS los campos** de la tabla principal, incluyendo `created_at` y `updated_at`.

#### Tablas Relacionadas
✅ **Incluir selectivamente:**
- Campos de identificación (ID, código)
- Campos descriptivos (nombre, descripción)
- Campos de estado (activo, color, orden)

❌ **NO incluir:**
- `created_at_<<tabla_relacionada>>`
- `updated_at_<<tabla_relacionada>>`

```sql
-- ✅ CORRECTO
ep.id_estado_ppto,
ep.codigo_estado_ppto,
ep.nombre_estado_ppto,
ep.color_estado_ppto,

-- ❌ INCORRECTO - No incluir timestamps de tablas relacionadas
-- ep.created_at_estado_ppto,
-- ep.updated_at_estado_ppto,
```

---

### Patrón 2: Tipos de JOIN

| Tipo | Cuándo Usar | FK en Tabla Principal |
|------|-------------|----------------------|
| `INNER JOIN` | Relación **obligatoria** | `NOT NULL` |
| `LEFT JOIN` | Relación **opcional** | `NULL` permitido |

```sql
FROM presupuesto p
INNER JOIN cliente c ON p.id_cliente = c.id_cliente           -- Obligatorio
LEFT JOIN contacto_cliente cc ON p.id_contacto_cliente = cc.id_contacto_cliente  -- Opcional
INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto  -- Obligatorio
LEFT JOIN forma_pago fp ON p.id_forma_pago = fp.id_pago       -- Opcional
```

---

### Patrón 3: Campos Calculados

Los campos calculados van **siempre al final** de la vista, organizados por tipo.

#### A) Concatenaciones de Direcciones

```sql
CONCAT_WS(', ',
    c.direccion_cliente,
    CONCAT(c.cp_cliente, ' ', c.poblacion_cliente),
    c.provincia_cliente
) AS direccion_completa_cliente,
```

#### B) Concatenaciones de Jerarquías

```sql
CONCAT_WS(' > ',
    COALESCE(g.nombre_grupo, 'Sin grupo'),
    f.nombre_familia,
    a.nombre_articulo
) AS jerarquia_completa_articulo,
```

#### C) Indicadores Booleanos

```sql
CASE 
    WHEN c.direccion_facturacion_cliente IS NOT NULL THEN TRUE
    ELSE FALSE
END AS tiene_direccion_facturacion_diferente,
```

#### D) Clasificaciones con CASE

```sql
CASE 
    WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 'Pago único'
    WHEN fp.porcentaje_anticipo_pago < 100.00 THEN 'Pago fraccionado'
    ELSE 'Sin forma de pago'
END AS tipo_pago_presupuesto,
```

#### E) Estados Basados en Fechas

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
(TO_DAYS(p.fecha_validez_presupuesto) - TO_DAYS(CURDATE())) AS dias_validez_restantes,
(TO_DAYS(CURDATE()) - TO_DAYS(p.fecha_presupuesto)) AS dias_desde_emision,
```

#### G) Cálculos de Duración

```sql
((TO_DAYS(p.fecha_fin_evento_presupuesto) - TO_DAYS(p.fecha_inicio_evento_presupuesto)) + 1) AS duracion_evento_dias,
```

#### H) Antigüedad

```sql
DATEDIFF(CURDATE(), e.fecha_alta_elemento) AS dias_en_servicio_elemento,
ROUND(DATEDIFF(CURDATE(), e.fecha_alta_elemento) / 365.25, 2) AS anios_en_servicio_elemento,
```

#### I) Descripciones Compuestas

```sql
CASE 
    WHEN fp.id_pago IS NULL THEN 'Sin forma de pago asignada'
    WHEN fp.porcentaje_anticipo_pago = 100.00 THEN 
        CONCAT(mp.nombre_metodo_pago, ' - ', fp.nombre_pago,
            CASE WHEN fp.descuento_pago > 0 THEN CONCAT(' (Dto: ', fp.descuento_pago, '%)') ELSE '' END
        )
    ELSE CONCAT(mp.nombre_metodo_pago, ' - ', fp.porcentaje_anticipo_pago, '% + ', fp.porcentaje_final_pago, '%')
END AS descripcion_completa_forma_pago,
```

#### J) Cálculos de Fechas Futuras

```sql
CASE 
    WHEN fp.dias_anticipo_pago = 0 THEN p.fecha_presupuesto
    ELSE (p.fecha_presupuesto + INTERVAL fp.dias_anticipo_pago DAY)
END AS fecha_vencimiento_anticipo,
```

---

### Patrón 4: Subconsultas para Contadores

```sql
(SELECT COUNT(cc.id_contacto_cliente)
 FROM contacto_cliente cc
 WHERE cc.id_cliente = c.id_cliente
) AS cantidad_contactos_cliente,
```

---

### Patrón 5: Uso de COALESCE

Para proporcionar valores por defecto cuando un campo puede ser NULL:

```sql
-- En concatenaciones
CONCAT_WS(' > ',
    COALESCE(g.nombre_grupo, 'Sin grupo'),
    f.nombre_familia,
    a.nombre_articulo
) AS jerarquia_completa,

-- Para imágenes con fallback
COALESCE(a.imagen_articulo, f.imagen_familia) AS imagen_efectiva,
```

---

## Dependencias entre Vistas

> Gestión del orden de creación y mantenimiento cuando una vista referencia a otra vista

---

### Concepto de Dependencia

Una **dependencia** ocurre cuando una vista utiliza otra vista como origen de datos en lugar de una tabla base. Esto crea una cadena de dependencias que debe respetarse tanto en la creación como en la modificación.

```
tabla_base
    └── vista_nivel_1 (depende de tabla_base)
            └── vista_nivel_2 (depende de vista_nivel_1)
                    └── vista_nivel_3 (depende de vista_nivel_2)
```

---

### Reglas de Dependencia en MySQL

| Operación | Comportamiento |
|-----------|----------------|
| `CREATE VIEW` | Falla si la vista referenciada no existe |
| `DROP VIEW` | Falla si otra vista depende de ella (sin CASCADE) |
| `ALTER VIEW` | Las vistas dependientes pueden fallar si cambian columnas referenciadas |
| `DROP VIEW ... CASCADE` | Elimina la vista y todas las que dependen de ella |

---

### Orden de Creación

**Regla fundamental**: Crear siempre de menor a mayor nivel de dependencia.

#### Ejemplo Práctico

Si tienes esta estructura de dependencias:

```
cliente (tabla)
    └── vista_cliente_completa
            └── vista_cliente_con_estadisticas

presupuesto (tabla)
    └── vista_presupuesto_completa (usa vista_cliente_completa)
            └── vista_presupuesto_dashboard
```

**Orden correcto de creación:**

```sql
-- ============================================
-- NIVEL 0: Tablas base (ya existen)
-- ============================================
-- cliente, presupuesto, estado_presupuesto, etc.

-- ============================================
-- NIVEL 1: Vistas que solo dependen de tablas
-- ============================================
CREATE VIEW vista_cliente_completa AS ...;
CREATE VIEW vista_estado_presupuesto_completa AS ...;

-- ============================================
-- NIVEL 2: Vistas que dependen de vistas nivel 1
-- ============================================
CREATE VIEW vista_cliente_con_estadisticas AS 
SELECT ... FROM vista_cliente_completa ...;

CREATE VIEW vista_presupuesto_completa AS 
SELECT ... FROM presupuesto p
INNER JOIN vista_cliente_completa vc ON p.id_cliente = vc.id_cliente ...;

-- ============================================
-- NIVEL 3: Vistas que dependen de vistas nivel 2
-- ============================================
CREATE VIEW vista_presupuesto_dashboard AS 
SELECT ... FROM vista_presupuesto_completa ...;
```

---

### Documentar Dependencias

Es recomendable incluir un comentario de dependencias en cada vista:

```sql
-- ============================================
-- Vista: vista_presupuesto_dashboard
-- Descripción: Resumen para dashboard de presupuestos
-- Fecha: 2024-12-14
-- 
-- DEPENDENCIAS:
--   - vista_presupuesto_completa (nivel 2)
--     - vista_cliente_completa (nivel 1)
--       - cliente (tabla)
--     - presupuesto (tabla)
--     - estado_presupuesto (tabla)
-- ============================================
CREATE VIEW vista_presupuesto_dashboard AS
SELECT 
    ...
FROM vista_presupuesto_completa
WHERE activo_presupuesto = 1;
```

---

### Identificar Dependencias Existentes

#### Consultar dependencias de una vista específica

```sql
-- Ver de qué objetos depende una vista
SELECT 
    TABLE_NAME AS vista,
    REFERENCED_TABLE_NAME AS depende_de
FROM information_schema.VIEW_TABLE_USAGE
WHERE TABLE_SCHEMA = 'nombre_base_datos'
AND TABLE_NAME = 'vista_presupuesto_completa';
```

#### Consultar todas las dependencias de la base de datos

```sql
-- Mapa completo de dependencias de vistas
SELECT 
    TABLE_NAME AS vista,
    GROUP_CONCAT(DISTINCT REFERENCED_TABLE_NAME ORDER BY REFERENCED_TABLE_NAME) AS depende_de
FROM information_schema.VIEW_TABLE_USAGE
WHERE TABLE_SCHEMA = 'nombre_base_datos'
GROUP BY TABLE_NAME
ORDER BY TABLE_NAME;
```

#### Identificar vistas que dependen de una tabla o vista específica

```sql
-- ¿Qué vistas se verían afectadas si modifico 'vista_cliente_completa'?
SELECT DISTINCT TABLE_NAME AS vistas_afectadas
FROM information_schema.VIEW_TABLE_USAGE
WHERE TABLE_SCHEMA = 'nombre_base_datos'
AND REFERENCED_TABLE_NAME = 'vista_cliente_completa';
```

---

### Modificar Vistas con Dependencias

#### Opción 1: CREATE OR REPLACE (Recomendada)

Si solo cambias la lógica interna sin modificar columnas de salida:

```sql
CREATE OR REPLACE VIEW vista_cliente_completa AS
SELECT 
    -- Mismas columnas, lógica diferente
    ...
FROM cliente c
...;
```

**Ventaja**: Las vistas dependientes no se ven afectadas si las columnas de salida permanecen iguales.

#### Opción 2: Recreación en Cascada

Si necesitas modificar columnas de salida que otras vistas utilizan:

```sql
-- 1. Guardar definición de vistas dependientes (o tenerlas en scripts)

-- 2. Eliminar en orden inverso (de mayor a menor nivel)
DROP VIEW IF EXISTS vista_presupuesto_dashboard;
DROP VIEW IF EXISTS vista_presupuesto_completa;
DROP VIEW IF EXISTS vista_cliente_completa;

-- 3. Recrear en orden correcto (de menor a mayor nivel)
CREATE VIEW vista_cliente_completa AS ...;  -- Con cambios
CREATE VIEW vista_presupuesto_completa AS ...;  -- Ajustada a cambios
CREATE VIEW vista_presupuesto_dashboard AS ...;  -- Ajustada a cambios
```

---

### Script de Gestión de Dependencias

Para proyectos con múltiples vistas, es útil mantener un script maestro:

```sql
-- ============================================
-- Script: recrear_vistas.sql
-- Descripción: Recrea todas las vistas en orden de dependencia
-- Última actualización: 2024-12-14
-- ============================================

-- ============================================
-- PASO 1: Eliminar vistas (orden inverso)
-- ============================================
DROP VIEW IF EXISTS vista_presupuesto_dashboard;
DROP VIEW IF EXISTS vista_presupuesto_completa;
DROP VIEW IF EXISTS vista_cliente_con_estadisticas;
DROP VIEW IF EXISTS vista_cliente_completa;
DROP VIEW IF EXISTS vista_elemento_presupuesto_completa;

-- ============================================
-- PASO 2: Crear vistas (orden de dependencia)
-- ============================================

-- NIVEL 1
SOURCE vistas/vista_cliente_completa.sql;
SOURCE vistas/vista_elemento_presupuesto_completa.sql;

-- NIVEL 2
SOURCE vistas/vista_cliente_con_estadisticas.sql;
SOURCE vistas/vista_presupuesto_completa.sql;

-- NIVEL 3
SOURCE vistas/vista_presupuesto_dashboard.sql;

-- ============================================
-- PASO 3: Verificación
-- ============================================
SELECT TABLE_NAME, TABLE_TYPE 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_TYPE = 'VIEW'
ORDER BY TABLE_NAME;
```

---

### Buenas Prácticas de Dependencias

| Práctica | Descripción |
|----------|-------------|
| **Minimizar niveles** | Evitar más de 3 niveles de dependencia para facilitar mantenimiento |
| **Documentar siempre** | Incluir comentario de dependencias en cada vista |
| **Scripts versionados** | Mantener scripts SQL de cada vista en control de versiones |
| **Evitar dependencias circulares** | Vista A → Vista B → Vista A (MySQL no lo permite) |
| **Probar en cascada** | Al modificar una vista, verificar todas las dependientes |

---

### Diagrama de Dependencias (Ejemplo)

```
┌─────────────────────────────────────────────────────────────────┐
│                     TABLAS BASE (Nivel 0)                       │
├─────────────────────────────────────────────────────────────────┤
│ cliente │ presupuesto │ elemento_presupuesto │ estado_ppto │ ...│
└────┬────┴──────┬──────┴──────────┬───────────┴──────┬──────┴────┘
     │           │                 │                  │
     ▼           │                 ▼                  │
┌─────────────┐  │    ┌────────────────────────────┐  │
│vista_cliente│  │    │vista_elemento_presupuesto  │  │
│  _completa  │  │    │        _completa           │  │
└──────┬──────┘  │    └─────────────┬──────────────┘  │
       │         │                  │                 │
       │         ▼                  │                 │
       │  ┌─────────────────────────┴─────────────────┴──┐
       └─►│      vista_presupuesto_completa              │
          │         (NIVEL 2)                            │
          └──────────────────┬───────────────────────────┘
                             │
                             ▼
          ┌──────────────────────────────────────────────┐
          │      vista_presupuesto_dashboard             │
          │              (NIVEL 3)                       │
          └──────────────────────────────────────────────┘
```

---

## Plantilla para Nuevas Vistas

### Plantilla Básica

```sql
-- ========================================================
-- VISTA: vista_<<tabla>>_completa
-- DESCRIPCIÓN: <<Breve descripción del propósito>>
-- FECHA: <<YYYY-MM-DD>>
-- 
-- DEPENDENCIAS:
--   - <<tabla1>> (tabla)
--   - <<tabla2>> (tabla)
--   - <<vista_x>> (vista nivel N) [si aplica]
-- ========================================================

DROP VIEW IF EXISTS vista_<<tabla>>_completa;

CREATE VIEW vista_<<tabla>>_completa AS
SELECT 
    -- =====================================================
    -- DATOS DE LA TABLA PRINCIPAL (<<Nombre Tabla>>)
    -- =====================================================
    tp.id_<<tabla>>,
    tp.codigo_<<tabla>>,
    tp.nombre_<<tabla>>,
    tp.campo1_<<tabla>>,
    tp.campo2_<<tabla>>,
    tp.observaciones_<<tabla>>,
    tp.activo_<<tabla>>,
    tp.created_at_<<tabla>>,
    tp.updated_at_<<tabla>>,
    
    -- =====================================================
    -- DATOS DE TABLA RELACIONADA 1 (<<Nombre Relación>>)
    -- =====================================================
    tr1.id_<<relacion1>>,
    tr1.codigo_<<relacion1>>,
    tr1.nombre_<<relacion1>>,
    
    -- =====================================================
    -- CANTIDAD DE REGISTROS RELACIONADOS (si aplica)
    -- =====================================================
    (SELECT COUNT(sub.id_<<subtabla>>)
     FROM <<subtabla>> sub
     WHERE sub.id_<<tabla>> = tp.id_<<tabla>>
    ) AS cantidad_<<subtabla>>,
    
    -- =====================================================
    -- CAMPOS CALCULADOS
    -- =====================================================
    
    -- Concatenación de dirección completa
    CONCAT_WS(', ',
        tp.direccion_<<tabla>>,
        CONCAT(tp.cp_<<tabla>>, ' ', tp.poblacion_<<tabla>>),
        tp.provincia_<<tabla>>
    ) AS direccion_completa_<<tabla>>,
    
    -- Estado basado en fechas
    CASE 
        WHEN tp.fecha_<<tabla>> IS NULL THEN 'Sin fecha'
        WHEN tp.fecha_<<tabla>> < CURDATE() THEN 'Vencido'
        WHEN tp.fecha_<<tabla>> = CURDATE() THEN 'Hoy'
        WHEN (TO_DAYS(tp.fecha_<<tabla>>) - TO_DAYS(CURDATE())) <= 7 THEN 'Próximo'
        ELSE 'Futuro'
    END AS estado_fecha_<<tabla>>,
    
    -- Cálculo de días
    (TO_DAYS(tp.fecha_<<tabla>>) - TO_DAYS(CURDATE())) AS dias_hasta_fecha_<<tabla>>

FROM <<tabla_principal>> tp
INNER JOIN <<tabla_relacionada1>> tr1 ON tp.id_<<fk1>> = tr1.id_<<pk1>>
LEFT JOIN <<tabla_relacionada2>> tr2 ON tp.id_<<fk2>> = tr2.id_<<pk2>>
WHERE tp.activo_<<tabla>> = 1;
```

---

## Beneficios de las Vistas

### 1. Simplificación de Consultas en Modelos

**Sin vista:**
```php
$sql = "SELECT p.*, c.nombre_cliente, c.email_cliente, 
        ep.nombre_estado_ppto, fp.nombre_pago,
        CONCAT_WS(', ', c.direccion_cliente, ...) AS direccion_completa
        FROM presupuesto p
        INNER JOIN cliente c ON p.id_cliente = c.id_cliente
        INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
        LEFT JOIN forma_pago fp ON p.id_forma_pago = fp.id_pago
        WHERE p.id_presupuesto = :id";
```

**Con vista:**
```php
$sql = "SELECT * FROM vista_presupuesto_completa WHERE id_presupuesto = :id";
```

### 2. Reutilización de Lógica

Los campos calculados se definen una vez y se reutilizan en todos los modelos.

### 3. Mantenimiento Centralizado

Si cambia la lógica de un campo calculado, solo se modifica la vista.

### 4. Abstracción de Complejidad

El modelo PHP no necesita conocer la complejidad de los JOINs.

### 5. Optimización de Rendimiento

MySQL optimiza internamente las vistas y aprovecha los índices de las tablas base.

---

## Buenas Prácticas

### ✅ DO (Hacer)

1. Usar `DROP VIEW IF EXISTS` antes de crear
2. Incluir TODOS los campos de la tabla principal
3. Organizar campos en secciones con comentarios visuales
4. Usar alias descriptivos para campos calculados
5. Incluir sufijo de tabla en todos los campos
6. Poner campos calculados al final
7. Usar `COALESCE()` para valores por defecto
8. Documentar dependencias en comentarios
9. Usar `LEFT JOIN` para relaciones opcionales, `INNER JOIN` para obligatorias
10. Nombrar vistas descriptivamente

### ❌ DON'T (No hacer)

1. NO incluir created_at/updated_at de tablas relacionadas
2. NO hacer cálculos pesados que ralenticen la vista
3. NO usar `SELECT *` en subconsultas
4. NO omitir el prefijo/sufijo de tabla en campos
5. NO mezclar campos calculados con campos directos
6. NO usar vistas para INSERT/UPDATE/DELETE
7. NO crear más de 3 niveles de dependencia entre vistas
8. NO duplicar lógica de cálculo

---

## Checklist para Crear una Nueva Vista

- [ ] Identificar la tabla principal y tablas relacionadas
- [ ] Definir qué campos de cada tabla se necesitan
- [ ] Identificar campos calculados que se reutilizarán
- [ ] Determinar tipo de JOIN (INNER vs LEFT) según obligatoriedad
- [ ] Decidir si incluye filtro WHERE (solo activos vs todos)
- [ ] Nombrar la vista siguiendo convenciones
- [ ] Documentar dependencias en el encabezado
- [ ] Organizar campos en secciones con comentarios
- [ ] Incluir `DROP VIEW IF EXISTS` antes del CREATE
- [ ] Incluir todos los campos de la tabla principal
- [ ] Omitir created_at/updated_at de tablas relacionadas
- [ ] Definir campos calculados al final
- [ ] Usar alias descriptivos con sufijos de tabla
- [ ] Probar la vista con consultas SELECT
- [ ] Verificar que no rompe vistas dependientes

---

## Consultas de Verificación

### Listar todas las vistas de la base de datos

```sql
SELECT TABLE_NAME 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
AND TABLE_TYPE = 'VIEW'
ORDER BY TABLE_NAME;
```

### Ver definición de una vista

```sql
SHOW CREATE VIEW vista_presupuesto_completa;
```

### Verificar dependencias de una vista

```sql
SELECT 
    TABLE_NAME AS vista,
    GROUP_CONCAT(DISTINCT REFERENCED_TABLE_NAME) AS depende_de
FROM information_schema.VIEW_TABLE_USAGE
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'vista_presupuesto_completa'
GROUP BY TABLE_NAME;
```

---

## Relación con Modelos PHP

Las vistas se utilizan en los métodos `get_entidades()` de los modelos:

```php
// En models/Presupuesto.php
public function get_presupuestos()
{
    $sql = "SELECT * FROM vista_presupuesto_completa 
            WHERE activo_presupuesto = 1
            ORDER BY fecha_presupuesto DESC";
    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

public function get_presupuestoxid($id_presupuesto)
{
    $sql = "SELECT * FROM vista_presupuesto_completa 
            WHERE id_presupuesto = ?";
    $stmt = $this->conexion->prepare($sql);
    $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetch(PDO::FETCH_ASSOC);
}
```

---

## Prompt para Solicitar Nueva Vista

```
NUEVA VISTA
===========
Nombre: vista_<<tabla>>_completa
Descripción: <<propósito de la vista>>

TABLA PRINCIPAL: <<nombre_tabla>>

RELACIONES:
- <<tabla_relacionada>>: [INNER|LEFT] - <<descripción>>
- <<tabla_relacionada>>: [INNER|LEFT] - <<descripción>>

CAMPOS CALCULADOS:
- <<nombre_campo>>: <<descripción del cálculo>>
- <<nombre_campo>>: <<descripción del cálculo>>

CONTADORES (si aplica):
- cantidad_<<subtabla>>: Contar registros de <<subtabla>>

FILTRO WHERE:
- [Solo activos | Todos los registros]

DEPENDENCIAS:
- <<lista de vistas que utiliza, si aplica>>
```

---

## Enlaces Relacionados

- [Documentación de Models](models.md) - Cómo los modelos usan las vistas
- [Documentación de Controllers](controller.md) - Flujo de datos desde vistas hasta la interfaz
- [Documentación de Conexión](conexion.md) - Sistema de consultas con PDO
- [Índices y Foreign Keys](04_indices_foreign_keys.md) - Optimización de consultas

---

*Documento: 07 - Vistas SQL | Última actualización: Diciembre 2024*
