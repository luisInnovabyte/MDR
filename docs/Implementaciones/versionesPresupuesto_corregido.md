# 📋 Sistema de Versiones de Presupuestos

## 🎯 Objetivo

Implementar un sistema de control de versiones para presupuestos que permita mantener un historial completo de todas las modificaciones solicitadas por el cliente, con trazabilidad completa de cambios, estados y aprobaciones.

---

## 🗂️ Arquitectura del Sistema

### Principios Fundamentales

1. **Inmutabilidad**: Las versiones enviadas/aprobadas/rechazadas no se pueden modificar
2. **Secuencialidad**: Las versiones son secuenciales (v1 → v2 → v3), no ramificadas
3. **Trazabilidad completa**: Cada cambio queda registrado con fecha, usuario y motivo
4. **Independencia de líneas**: Cada versión tiene su propio conjunto completo de líneas de presupuesto

---

## 📊 Estructura de Tablas

### Tabla 1: `presupuesto` (Cabecera)

La tabla principal de presupuestos se modifica para incluir campos de control de versiones.

#### ALTER TABLE: Añadir campos de versionado

```sql
-- ============================================
-- ALTER TABLE: presupuesto
-- Descripción: Añade campos para sistema de versiones
-- Fecha: 2025-01-12
-- ============================================

ALTER TABLE presupuesto
    -- Versión actual activa del presupuesto
    ADD COLUMN version_actual_presupuesto INT UNSIGNED NOT NULL DEFAULT 1 
        COMMENT 'Número de versión activa actual' 
        AFTER id_estado_ppto,
    
    -- Estado general del presupuesto (puede diferir del estado de cada versión)
    ADD COLUMN estado_general_presupuesto ENUM(
        'borrador', 
        'enviado', 
        'aprobado', 
        'rechazado', 
        'cancelado'
    ) NOT NULL DEFAULT 'borrador' 
        COMMENT 'Estado general del presupuesto (sincronizado con version_actual)' 
        AFTER version_actual_presupuesto,
    
    -- Índice para búsquedas por versión actual
    ADD INDEX idx_version_actual_presupuesto (version_actual_presupuesto),
    
    -- Índice para búsquedas por estado general
    ADD INDEX idx_estado_general_presupuesto (estado_general_presupuesto);
```

#### Campos añadidos

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `version_actual_presupuesto` | INT UNSIGNED | Número de versión actualmente activa |
| `estado_general_presupuesto` | ENUM | Estado global del presupuesto |

#### Notas Importantes

- **NO se duplica** la tabla `presupuesto`
- Solo existe **UN registro** por presupuesto
- El campo `version_actual_presupuesto` apunta siempre a la versión con la que se está trabajando
- El campo `estado_general_presupuesto` se sincroniza con el estado de la versión actual

---

### Tabla 2: `presupuesto_version`

Nueva tabla que almacena cada versión del presupuesto con su historial completo.

```sql
-- ============================================
-- Tabla: presupuesto_version
-- Descripción: Control de versiones de presupuestos
-- Fecha: 2025-01-12
-- ============================================

CREATE TABLE presupuesto_version (
    -- =====================================================
    -- IDENTIFICACIÓN
    -- =====================================================
    id_version_presupuesto INT UNSIGNED NOT NULL AUTO_INCREMENT,
    
    -- =====================================================
    -- RELACIONES
    -- =====================================================
    id_presupuesto INT UNSIGNED NOT NULL
        COMMENT 'FK a presupuesto',
    
    -- =====================================================
    -- CONTROL DE VERSIONES
    -- =====================================================
    numero_version_presupuesto INT UNSIGNED NOT NULL 
        COMMENT 'Número secuencial de versión (1, 2, 3...)',
    
    version_padre_presupuesto INT UNSIGNED NULL 
        COMMENT 'ID de la versión anterior (NULL si es la versión original)',
    
    -- =====================================================
    -- ESTADO Y SEGUIMIENTO
    -- =====================================================
    estado_version_presupuesto ENUM(
        'borrador',
        'enviado',
        'aprobado',
        'rechazado',
        'cancelado'
    ) NOT NULL DEFAULT 'borrador'
        COMMENT 'Estado específico de esta versión',
    
    motivo_modificacion_version TEXT
        COMMENT 'Razón por la que se creó esta versión',
    
    -- =====================================================
    -- FECHAS Y TRAZABILIDAD
    -- =====================================================
    fecha_creacion_version TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Fecha de creación de esta versión',
    
    creado_por_version INT UNSIGNED NOT NULL
        COMMENT 'ID del usuario que creó esta versión',
    
    fecha_envio_version DATETIME NULL
        COMMENT 'Fecha de envío al cliente',
    
    enviado_por_version INT UNSIGNED NULL
        COMMENT 'ID del usuario que envió esta versión',
    
    fecha_aprobacion_version DATETIME NULL
        COMMENT 'Fecha en que el cliente aprobó esta versión',
    
    fecha_rechazo_version DATETIME NULL
        COMMENT 'Fecha en que el cliente rechazó esta versión',
    
    motivo_rechazo_version TEXT
        COMMENT 'Motivo del rechazo del cliente',
    
    ruta_pdf_version VARCHAR(255)
        COMMENT 'Ruta del archivo PDF generado para esta versión',
    
    -- =====================================================
    -- CONTROL
    -- =====================================================
    activo_version BOOLEAN DEFAULT TRUE,
    created_at_version TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_version TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- =====================================================
    -- CLAVE PRIMARIA
    -- =====================================================
    PRIMARY KEY (id_version_presupuesto),
    
    -- =====================================================
    -- CLAVES FORÁNEAS
    -- =====================================================
    CONSTRAINT fk_version_presupuesto 
        FOREIGN KEY (id_presupuesto) 
        REFERENCES presupuesto(id_presupuesto) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_version_padre 
        FOREIGN KEY (version_padre_presupuesto) 
        REFERENCES presupuesto_version(id_version_presupuesto) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    -- =====================================================
    -- ÍNDICES DE OPTIMIZACIÓN
    -- =====================================================
    INDEX idx_id_presupuesto_version (id_presupuesto),
    INDEX idx_numero_version (numero_version_presupuesto),
    INDEX idx_version_padre (version_padre_presupuesto),
    INDEX idx_estado_version (estado_version_presupuesto),
    INDEX idx_fecha_creacion_version (fecha_creacion_version),
    INDEX idx_fecha_envio_version (fecha_envio_version),
    INDEX idx_presupuesto_numero_version (id_presupuesto, numero_version_presupuesto),
    INDEX idx_creado_por (creado_por_version),
    INDEX idx_enviado_por (enviado_por_version)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Tabla de control de versiones de presupuestos. Cada registro representa una versión específica con su historial completo de cambios y estados';
```

#### Campos Principales

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `id_version_presupuesto` | INT UNSIGNED | PK autoincremental |
| `id_presupuesto` | INT UNSIGNED | FK a tabla `presupuesto` |
| `numero_version_presupuesto` | INT UNSIGNED | Número secuencial (1, 2, 3...) |
| `version_padre_presupuesto` | INT UNSIGNED | FK autorreferencial a versión anterior |
| `estado_version_presupuesto` | ENUM | Estado de esta versión específica |
| `motivo_modificacion_version` | TEXT | Por qué se creó esta versión |

#### Campos de Auditoría

| Campo | Tipo | Descripción |
|-------|------|-------------|
| `fecha_creacion_version` | TIMESTAMP | Cuándo se creó |
| `creado_por_version` | INT UNSIGNED | Quién la creó |
| `fecha_envio_version` | DATETIME | Cuándo se envió al cliente |
| `enviado_por_version` | INT UNSIGNED | Quién la envió |
| `fecha_aprobacion_version` | DATETIME | Cuándo fue aprobada |
| `fecha_rechazo_version` | DATETIME | Cuándo fue rechazada |
| `motivo_rechazo_version` | TEXT | Por qué fue rechazada |
| `ruta_pdf_version` | VARCHAR(255) | Ruta del PDF generado |

---

### Tabla 3: `linea_presupuesto` (Modificación)

Las líneas de presupuesto deben apuntar a `presupuesto_version` en lugar de a `presupuesto`.

#### Modificación necesaria

```sql
-- ============================================
-- ALTER TABLE: linea_presupuesto
-- Descripción: Modificar para que apunte a versiones
-- Fecha: 2025-01-12
-- ============================================

-- NOTA: Este cambio requiere planificación cuidadosa
-- Si ya tienes datos, necesitarás migración

-- Opción 1: Si NO tienes datos aún
ALTER TABLE linea_presupuesto
    DROP FOREIGN KEY fk_linea_ppto_presupuesto,
    CHANGE COLUMN id_presupuesto id_version_presupuesto INT UNSIGNED NOT NULL,
    ADD CONSTRAINT fk_linea_version_presupuesto 
        FOREIGN KEY (id_version_presupuesto) 
        REFERENCES presupuesto_version(id_version_presupuesto) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE;

-- Opción 2: Si ya tienes datos
-- Requiere script de migración personalizado
```

---

## 🔄 Flujo de Trabajo del Sistema

### 1. Creación del Presupuesto Inicial (Versión 1)

Cuando se crea un presupuesto nuevo:

```
presupuesto
├── id_presupuesto: 1
├── version_actual_presupuesto: 1
└── estado_general_presupuesto: 'borrador'

presupuesto_version
├── id_version_presupuesto: 1
├── id_presupuesto: 1
├── numero_version_presupuesto: 1
├── version_padre_presupuesto: NULL
├── estado_version_presupuesto: 'borrador'
└── motivo_modificacion_version: NULL

linea_presupuesto (múltiples registros)
└── id_version_presupuesto: 1
```

---

### 2. Modificación de Presupuesto en Borrador

**Caso A: El presupuesto está en estado 'borrador'** (nunca enviado al cliente)

- ✅ Se modifican directamente las líneas existentes
- ❌ NO se crea nueva versión
- Se trabaja sobre las líneas con `id_version_presupuesto = 1`

---

### 3. Modificación de Presupuesto Enviado/Aprobado/Rechazado

**Caso B: El presupuesto ya fue enviado al cliente**

#### Paso 1: Crear nueva versión

```sql
INSERT INTO presupuesto_version (
    id_presupuesto,
    numero_version_presupuesto,
    version_padre_presupuesto,
    estado_version_presupuesto,
    creado_por_version,
    motivo_modificacion_version
) VALUES (
    1,                              -- Mismo presupuesto
    2,                              -- Nueva versión
    1,                              -- Versión padre
    'borrador',                     -- Empieza como borrador
    5,                              -- ID usuario
    'Cliente solicita 2 focos más'  -- Motivo
);
```

#### Paso 2: Duplicar líneas de la versión anterior

```sql
INSERT INTO linea_presupuesto (
    id_version_presupuesto,  -- Nueva versión
    numero_linea_ppto,
    tipo_linea_ppto,
    codigo_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto
    -- ... resto de campos
)
SELECT 
    2,                       -- ID de la nueva versión
    numero_linea_ppto,
    tipo_linea_ppto,
    codigo_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto
    -- ... resto de campos
FROM linea_presupuesto
WHERE id_version_presupuesto = 1;  -- Copiar desde versión anterior
```

#### Paso 3: Actualizar cabecera

```sql
UPDATE presupuesto
SET 
    version_actual_presupuesto = 2,
    estado_general_presupuesto = 'borrador'
WHERE id_presupuesto = 1;
```

#### Paso 4: Hacer modificaciones

El usuario ahora modifica las líneas de la versión 2:
- Añadir nuevas líneas
- Modificar cantidades
- Cambiar precios
- Ajustar descuentos

---

### 4. Envío al Cliente

```sql
UPDATE presupuesto_version
SET 
    estado_version_presupuesto = 'enviado',
    fecha_envio_version = NOW(),
    enviado_por_version = 5,
    ruta_pdf_version = '/documentos/presupuestos/PPTO-2025-001_v2.pdf'
WHERE id_version_presupuesto = 2;

UPDATE presupuesto
SET estado_general_presupuesto = 'enviado'
WHERE id_presupuesto = 1;
```

---

### 5. Respuesta del Cliente

#### Caso A: Cliente aprueba

```sql
UPDATE presupuesto_version
SET 
    estado_version_presupuesto = 'aprobado',
    fecha_aprobacion_version = NOW()
WHERE id_version_presupuesto = 2;

UPDATE presupuesto
SET estado_general_presupuesto = 'aprobado'
WHERE id_presupuesto = 1;
```

**Resultado**: El presupuesto queda cerrado, no se permiten más versiones.

#### Caso B: Cliente rechaza

```sql
UPDATE presupuesto_version
SET 
    estado_version_presupuesto = 'rechazado',
    fecha_rechazo_version = NOW(),
    motivo_rechazo_version = 'Precio muy elevado'
WHERE id_version_presupuesto = 2;

UPDATE presupuesto
SET estado_general_presupuesto = 'rechazado'
WHERE id_presupuesto = 1;
```

**Resultado**: El presupuesto queda disponible para crear versión 3.

#### Caso C: Cliente pide más modificaciones

Se repite el proceso desde el Paso 1, creando versión 3:
- `version_padre_presupuesto = 2`
- Se forma la cadena: v1 → v2 → v3

---

## 📋 Reglas de Negocio

### Reglas Obligatorias

1. ✅ **Solo la versión actual en 'borrador' puede modificarse**
2. ✅ **Versiones en estado 'enviado', 'aprobado' o 'rechazado' son INMUTABLES**
3. ✅ **Una versión 'aprobada' cierra el presupuesto** (no permite más versiones)
4. ✅ **Cada versión tiene su propio conjunto COMPLETO de líneas**
5. ✅ **El campo `version_padre_presupuesto` permite reconstruir el árbol de cambios**
6. ✅ **Todos los PDFs generados se almacenan con indicación de versión visible**

### Estados Permitidos

| Estado | Permite Modificación | Permite Nueva Versión | Final |
|--------|---------------------|----------------------|-------|
| `borrador` | ✅ Sí | ❌ No (se modifica la actual) | ❌ |
| `enviado` | ❌ No | ✅ Sí | ❌ |
| `aprobado` | ❌ No | ❌ No | ✅ |
| `rechazado` | ❌ No | ✅ Sí | ❌ |
| `cancelado` | ❌ No | ❌ No | ✅ |

---

## 🔍 Consultas Útiles

### Ver todas las versiones de un presupuesto

```sql
SELECT 
    v.numero_version_presupuesto,
    v.estado_version_presupuesto,
    v.fecha_creacion_version,
    v.motivo_modificacion_version,
    v.fecha_envio_version,
    v.fecha_aprobacion_version,
    v.fecha_rechazo_version
FROM presupuesto_version v
WHERE v.id_presupuesto = 1
ORDER BY v.numero_version_presupuesto;
```

### Ver líneas de una versión específica

```sql
SELECT 
    l.numero_linea_ppto,
    l.descripcion_linea_ppto,
    l.cantidad_linea_ppto,
    l.precio_unitario_linea_ppto,
    l.total_linea_ppto
FROM linea_presupuesto l
WHERE l.id_version_presupuesto = 2
ORDER BY l.numero_linea_ppto;
```

### Ver árbol genealógico de versiones

```sql
WITH RECURSIVE arbol_versiones AS (
    -- Versión raíz (v1)
    SELECT 
        id_version_presupuesto,
        numero_version_presupuesto,
        version_padre_presupuesto,
        estado_version_presupuesto,
        1 as nivel
    FROM presupuesto_version
    WHERE version_padre_presupuesto IS NULL
    AND id_presupuesto = 1
    
    UNION ALL
    
    -- Versiones hijas
    SELECT 
        v.id_version_presupuesto,
        v.numero_version_presupuesto,
        v.version_padre_presupuesto,
        v.estado_version_presupuesto,
        a.nivel + 1
    FROM presupuesto_version v
    INNER JOIN arbol_versiones a ON v.version_padre_presupuesto = a.id_version_presupuesto
)
SELECT * FROM arbol_versiones
ORDER BY numero_version_presupuesto;
```

### Comparar dos versiones (diferencias)

```sql
-- Líneas añadidas en v2 que no estaban en v1
SELECT 
    'AÑADIDO' as accion,
    l2.*
FROM linea_presupuesto l2
LEFT JOIN linea_presupuesto l1 
    ON l1.id_articulo = l2.id_articulo 
    AND l1.id_version_presupuesto = 1
WHERE l2.id_version_presupuesto = 2
AND l1.id_linea_ppto IS NULL

UNION ALL

-- Líneas eliminadas de v1 que no están en v2
SELECT 
    'ELIMINADO' as accion,
    l1.*
FROM linea_presupuesto l1
LEFT JOIN linea_presupuesto l2 
    ON l2.id_articulo = l1.id_articulo 
    AND l2.id_version_presupuesto = 2
WHERE l1.id_version_presupuesto = 1
AND l2.id_linea_ppto IS NULL

UNION ALL

-- Líneas modificadas
SELECT 
    'MODIFICADO' as accion,
    l2.*
FROM linea_presupuesto l1
INNER JOIN linea_presupuesto l2 
    ON l2.id_articulo = l1.id_articulo
WHERE l1.id_version_presupuesto = 1
AND l2.id_version_presupuesto = 2
AND (
    l1.cantidad_linea_ppto != l2.cantidad_linea_ppto OR
    l1.precio_unitario_linea_ppto != l2.precio_unitario_linea_ppto OR
    l1.descuento_linea_ppto != l2.descuento_linea_ppto
);
```

---

## ⚠️ Consideraciones Importantes

### Foreign Keys con Tabla Usuario

La tabla `presupuesto_version` tiene referencias a usuarios pero sin FK definidas:
- `creado_por_version`
- `enviado_por_version`

**Cuando se cree la tabla `usuario`**, añadir las FK:

```sql
ALTER TABLE presupuesto_version
    ADD CONSTRAINT fk_version_creado_por 
        FOREIGN KEY (creado_por_version) 
        REFERENCES usuario(id_usuario) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    ADD CONSTRAINT fk_version_enviado_por 
        FOREIGN KEY (enviado_por_version) 
        REFERENCES usuario(id_usuario) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE;
```

### Migración de Datos Existentes

Si ya tienes presupuestos en el sistema:

1. Crear backup de la base de datos
2. Añadir campos a `presupuesto`
3. Crear tabla `presupuesto_version`
4. Crear versión 1 para cada presupuesto existente
5. Actualizar `linea_presupuesto` para apuntar a versiones
6. Verificar integridad de datos

---

## 🔧 Triggers Recomendados

### Trigger: Auto-calcular número de versión

```sql
DELIMITER //

CREATE TRIGGER trg_presupuesto_version_before_insert
BEFORE INSERT ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE max_version INT;
    
    -- Obtener el número de versión más alto actual
    SELECT COALESCE(MAX(numero_version_presupuesto), 0) INTO max_version
    FROM presupuesto_version
    WHERE id_presupuesto = NEW.id_presupuesto;
    
    -- Asignar el siguiente número de versión
    SET NEW.numero_version_presupuesto = max_version + 1;
END//

DELIMITER ;
```

### Trigger: Validar estado inmutable

```sql
DELIMITER //

CREATE TRIGGER trg_presupuesto_version_before_update
BEFORE UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    -- Impedir modificación de versiones no-borrador
    IF OLD.estado_version_presupuesto != 'borrador' 
       AND OLD.estado_version_presupuesto != NEW.estado_version_presupuesto THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se pueden modificar versiones que no están en borrador';
    END IF;
END//

DELIMITER ;
```

### Trigger: Sincronizar estado de cabecera

```sql
DELIMITER //

CREATE TRIGGER trg_version_sync_estado_cabecera
AFTER UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE version_actual INT;
    
    -- Obtener la versión actual del presupuesto
    SELECT version_actual_presupuesto INTO version_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto;
    
    -- Si esta es la versión actual, sincronizar estado
    IF NEW.numero_version_presupuesto = version_actual THEN
        UPDATE presupuesto
        SET estado_general_presupuesto = NEW.estado_version_presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
    END IF;
END//

DELIMITER ;
```

---

## 🎨 Interfaz de Usuario Recomendada

### Vista de Listado de Versiones

```
┌─────────────────────────────────────────────────────────────┐
│ PRESUPUESTO: PPTO-2025-001                                  │
│ Cliente: ACME Corporation                                   │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ ┌─────┬────────────┬───────────┬──────────────────────┐   │
│ │ v.  │ Estado     │ Fecha     │ Acciones             │   │
│ ├─────┼────────────┼───────────┼──────────────────────┤   │
│ │ 3   │ BORRADOR   │ 12/01/25  │ [Editar] [Enviar]   │   │
│ │ 2   │ RECHAZADO  │ 10/01/25  │ [Ver PDF] [Ver]      │   │
│ │ 1   │ ENVIADO    │ 08/01/25  │ [Ver PDF] [Ver]      │   │
│ └─────┴────────────┴───────────┴──────────────────────┘   │
│                                                             │
│ [+ Nueva Versión]                                          │
└─────────────────────────────────────────────────────────────┘
```

### Vista de Comparación de Versiones

```
┌─────────────────────────────────────────────────────────────┐
│ COMPARAR VERSIONES                                          │
│ Versión 1 (08/01/25) ↔ Versión 2 (10/01/25)              │
├─────────────────────────────────────────────────────────────┤
│                                                             │
│ Artículo              │ v1         │ v2         │ Cambio   │
│ ──────────────────────┼────────────┼────────────┼──────────│
│ Pantalla LED 3x2      │ 2 uds      │ 2 uds      │ =        │
│ Foco PAR 64           │ 10 uds     │ 12 uds     │ +2 🟢   │
│ Mesa mezclas          │ 1 ud       │ 1 ud       │ =        │
│ Cable XLR (NUEVO)     │ -          │ 20 uds     │ NUEVO 🟢│
│                                                             │
│ TOTAL v1: 2.450,00 €                                       │
│ TOTAL v2: 2.680,00 € (+230,00 € / +9,4%)                  │
└─────────────────────────────────────────────────────────────┘
```

---

## 📦 Conclusión

Este sistema de versiones proporciona:

✅ **Trazabilidad completa** de todos los cambios  
✅ **Historial inmutable** de aprobaciones y rechazos  
✅ **Auditoría perfecta** para disputas comerciales  
✅ **Flexibilidad** para modificaciones iterativas  
✅ **Transparencia** total con el cliente  

---

*Documento: versiones_presupuestos.md | Versión: 1.0 | Fecha: 13 Enero 2025*
