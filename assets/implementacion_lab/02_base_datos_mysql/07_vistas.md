# Vistas MySQL

> Estándar para creación de vistas con relaciones completas y campos calculados

---

## Nomenclatura de Vistas

### Reglas

| Tipo de vista | Formato | Ejemplo |
|---------------|---------|---------|
| Vista completa (con todos los JOINs) | `vista_<<tabla_principal>>_completa` | `vista_cliente_completa` |
| Vista filtrada | `vista_<<tabla_principal>>_<<filtro>>` | `vista_cliente_activos` |

### Ejemplos válidos

```
vista_cliente_completa
vista_cliente_activos
vista_cliente_inactivos
vista_pedido_completa
vista_pedido_pendientes
vista_articulo_completa
vista_articulo_con_stock
vista_factura_completa
vista_factura_pagadas
vista_factura_vencidas
```

---

## Estructura de una Vista Completa

Una vista completa debe incluir:

1. **Todos los campos** de la tabla principal
2. **JOINs** con todas las tablas relacionadas (FK)
3. **Campos descriptivos** de las tablas relacionadas (no solo IDs)
4. **Campos calculados** útiles para listados y reportes

### Plantilla base

```sql
-- ============================================
-- Vista: vista_<<tabla_principal>>_completa
-- Descripción: <<descripción breve>>
-- Fecha: <<fecha_creación>>
-- ============================================

CREATE OR REPLACE VIEW vista_<<tabla_principal>>_completa AS
SELECT 
    -- ----------------------------------------
    -- Campos de la tabla principal
    -- ----------------------------------------
    t.id_<<tabla_principal>>,
    t.campo1_<<tabla_principal>>,
    t.campo2_<<tabla_principal>>,
    -- ... todos los campos relevantes ...
    
    -- ----------------------------------------
    -- Campos de tablas relacionadas
    -- ----------------------------------------
    r1.nombre_<<tabla_rel1>> AS nombre_<<tabla_rel1>>,
    r2.nombre_<<tabla_rel2>> AS nombre_<<tabla_rel2>>,
    
    -- ----------------------------------------
    -- Campos calculados
    -- ----------------------------------------
    -- <<campo_calculado>> AS <<alias_descriptivo>>,
    
    -- ----------------------------------------
    -- Campos de control
    -- ----------------------------------------
    t.activo_<<tabla_principal>>,
    t.created_at_<<tabla_principal>>,
    t.updated_at_<<tabla_principal>>

FROM <<tabla_principal>> t
    -- JOINs obligatorios (FK NOT NULL)
    INNER JOIN <<tabla_rel1>> r1 ON t.id_<<tabla_rel1>> = r1.id_<<tabla_rel1>>
    -- JOINs opcionales (FK NULL)
    LEFT JOIN <<tabla_rel2>> r2 ON t.id_<<tabla_rel2>> = r2.id_<<tabla_rel2>>
;
```

---

## Ejemplo 1: Vista completa simple

```sql
-- ============================================
-- Vista: vista_cliente_completa
-- Descripción: Clientes con datos de comercial asignado
-- Fecha: 2024-12-01
-- ============================================

CREATE OR REPLACE VIEW vista_cliente_completa AS
SELECT 
    -- Campos de cliente
    c.id_cliente,
    c.codigo_cliente,
    c.nombre_cliente,
    c.apellido_cliente,
    c.email_cliente,
    c.telefono_cliente,
    c.direccion_cliente,
    c.nif_cliente,
    
    -- Campos de comercial (FK opcional)
    c.id_comercial,
    com.nombre_comercial,
    com.email_comercial AS email_comercial_asignado,
    
    -- Campos calculados
    CONCAT(c.nombre_cliente, ' ', IFNULL(c.apellido_cliente, '')) AS nombre_completo_cliente,
    
    -- Campos de control
    c.activo_cliente,
    c.created_at_cliente,
    c.updated_at_cliente

FROM cliente c
    LEFT JOIN comercial com ON c.id_comercial = com.id_comercial
;
```

---

## Ejemplo 2: Vista completa con múltiples JOINs

```sql
-- ============================================
-- Vista: vista_pedido_completa
-- Descripción: Pedidos con cliente, comercial y totales
-- Fecha: 2024-12-01
-- ============================================

CREATE OR REPLACE VIEW vista_pedido_completa AS
SELECT 
    -- Campos de pedido
    p.id_pedido,
    p.numero_pedido,
    p.fecha_pedido,
    p.observaciones_pedido,
    p.total_pedido,
    p.estado_pedido,
    
    -- Campos de cliente
    p.id_cliente,
    cl.nombre_cliente,
    cl.apellido_cliente,
    CONCAT(cl.nombre_cliente, ' ', IFNULL(cl.apellido_cliente, '')) AS nombre_completo_cliente,
    cl.email_cliente,
    cl.telefono_cliente,
    
    -- Campos de comercial (a través de cliente)
    cl.id_comercial,
    com.nombre_comercial,
    
    -- Campos calculados
    (SELECT COUNT(*) FROM linea_pedido lp WHERE lp.id_pedido = p.id_pedido AND lp.activo_linea_pedido = 1) AS total_lineas,
    (SELECT SUM(lp.cantidad_linea_pedido) FROM linea_pedido lp WHERE lp.id_pedido = p.id_pedido AND lp.activo_linea_pedido = 1) AS total_unidades,
    DATEDIFF(CURRENT_DATE, p.fecha_pedido) AS dias_desde_pedido,
    
    -- Campos de control
    p.activo_pedido,
    p.created_at_pedido,
    p.updated_at_pedido

FROM pedido p
    INNER JOIN cliente cl ON p.id_cliente = cl.id_cliente
    LEFT JOIN comercial com ON cl.id_comercial = com.id_comercial
;
```

---

## Ejemplo 3: Vista filtrada

```sql
-- ============================================
-- Vista: vista_cliente_activos
-- Descripción: Solo clientes activos con comercial
-- Fecha: 2024-12-01
-- ============================================

CREATE OR REPLACE VIEW vista_cliente_activos AS
SELECT 
    c.id_cliente,
    c.codigo_cliente,
    c.nombre_cliente,
    c.apellido_cliente,
    c.email_cliente,
    c.telefono_cliente,
    
    c.id_comercial,
    com.nombre_comercial,
    
    CONCAT(c.nombre_cliente, ' ', IFNULL(c.apellido_cliente, '')) AS nombre_completo_cliente,
    
    c.created_at_cliente,
    c.updated_at_cliente

FROM cliente c
    LEFT JOIN comercial com ON c.id_comercial = com.id_comercial
WHERE c.activo_cliente = 1
;
```

```sql
-- ============================================
-- Vista: vista_cliente_inactivos
-- Descripción: Solo clientes inactivos (eliminados lógicamente)
-- Fecha: 2024-12-01
-- ============================================

CREATE OR REPLACE VIEW vista_cliente_inactivos AS
SELECT 
    c.id_cliente,
    c.codigo_cliente,
    c.nombre_cliente,
    c.apellido_cliente,
    c.email_cliente,
    
    c.id_comercial,
    com.nombre_comercial,
    
    CONCAT(c.nombre_cliente, ' ', IFNULL(c.apellido_cliente, '')) AS nombre_completo_cliente,
    
    c.created_at_cliente,
    c.updated_at_cliente

FROM cliente c
    LEFT JOIN comercial com ON c.id_comercial = com.id_comercial
WHERE c.activo_cliente = 0
;
```

---

## Ejemplo 4: Vista con campos calculados complejos

```sql
-- ============================================
-- Vista: vista_factura_completa
-- Descripción: Facturas con cliente, totales y estado de pago
-- Fecha: 2024-12-01
-- ============================================

CREATE OR REPLACE VIEW vista_factura_completa AS
SELECT 
    -- Campos de factura
    f.id_factura,
    f.numero_factura,
    f.fecha_factura,
    f.fecha_vencimiento_factura,
    f.base_imponible_factura,
    f.iva_factura,
    f.total_factura,
    f.estado_factura,
    
    -- Campos de cliente
    f.id_cliente,
    cl.nombre_cliente,
    cl.nif_cliente,
    CONCAT(cl.nombre_cliente, ' ', IFNULL(cl.apellido_cliente, '')) AS nombre_completo_cliente,
    
    -- Campos de pedido (si existe)
    f.id_pedido,
    p.numero_pedido,
    
    -- Campos calculados
    DATEDIFF(f.fecha_vencimiento_factura, CURRENT_DATE) AS dias_para_vencimiento,
    CASE 
        WHEN f.estado_factura = 'pagada' THEN 'Pagada'
        WHEN f.fecha_vencimiento_factura < CURRENT_DATE THEN 'Vencida'
        WHEN DATEDIFF(f.fecha_vencimiento_factura, CURRENT_DATE) <= 7 THEN 'Próxima a vencer'
        ELSE 'Vigente'
    END AS estado_vencimiento,
    
    -- Campos de control
    f.activo_factura,
    f.created_at_factura,
    f.updated_at_factura

FROM factura f
    INNER JOIN cliente cl ON f.id_cliente = cl.id_cliente
    LEFT JOIN pedido p ON f.id_pedido = p.id_pedido
;
```

---

## Campos Calculados Útiles

### Concatenación de nombres

```sql
CONCAT(c.nombre_cliente, ' ', IFNULL(c.apellido_cliente, '')) AS nombre_completo_cliente
```

### Conteos de registros relacionados

```sql
(SELECT COUNT(*) FROM linea_pedido lp WHERE lp.id_pedido = p.id_pedido AND lp.activo_linea_pedido = 1) AS total_lineas
```

### Sumas de importes

```sql
(SELECT SUM(lp.total_linea_pedido) FROM linea_pedido lp WHERE lp.id_pedido = p.id_pedido) AS suma_lineas
```

### Diferencia de fechas

```sql
DATEDIFF(CURRENT_DATE, p.fecha_pedido) AS dias_desde_pedido
DATEDIFF(f.fecha_vencimiento_factura, CURRENT_DATE) AS dias_para_vencimiento
```

### Estados calculados con CASE

```sql
CASE 
    WHEN f.estado_factura = 'pagada' THEN 'Pagada'
    WHEN f.fecha_vencimiento_factura < CURRENT_DATE THEN 'Vencida'
    ELSE 'Pendiente'
END AS estado_calculado
```

### Formato de fechas

```sql
DATE_FORMAT(p.fecha_pedido, '%d/%m/%Y') AS fecha_pedido_formato
DATE_FORMAT(p.fecha_pedido, '%d/%m/%Y %H:%i') AS fecha_hora_pedido
```

---

## Buenas Prácticas

1. **Usar alias de tabla** (`c`, `p`, `cl`) para legibilidad
2. **Ordenar campos**: primero tabla principal, luego relacionadas, luego calculados, finalmente control
3. **Nombrar campos relacionados** con sufijo descriptivo cuando hay ambigüedad
4. **Filtrar por `activo_` = 1** en vistas filtradas, no en completas (para poder ver también inactivos)
5. **Usar `IFNULL()`** para evitar concatenaciones con NULL
6. **Documentar** con comentario de cabecera

---

## Prompt para Solicitar Vistas

```
NUEVA VISTA
===========
Tabla principal: <<nombre>>
Tipo: [completa|filtrada]
Filtro (si aplica): <<condición>>

RELACIONES A INCLUIR:
- <<tabla_relacionada>>: <<campos a mostrar>>
- ...

CAMPOS CALCULADOS:
- <<descripción del cálculo>>
- ...
```

---

*Documento: 02-07 | Última actualización: Diciembre 2024*
