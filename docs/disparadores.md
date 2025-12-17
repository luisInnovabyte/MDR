# Documentación de Disparadores (Triggers) SQL

## Introducción

Los **disparadores (triggers)** son objetos de base de datos que se ejecutan automáticamente en respuesta a eventos específicos en una tabla. En el proyecto MDR, los triggers se utilizan para mantener la integridad de datos, sincronizar estados, generar códigos automáticos y validar reglas de negocio.

---

## Disparadores Implementados en el Proyecto

El proyecto cuenta con los siguientes triggers activos:

| Trigger | Tabla | Momento | Evento | Propósito |
|---------|-------|---------|--------|-----------|
| `trg_elemento_before_insert` | elemento | BEFORE | INSERT | Generar código automático de elemento y establecer estado por defecto |
| `trg_empresa_validar_ficticia_principal` | empresa | BEFORE | INSERT | Validar que solo exista una empresa ficticia principal activa |
| `trg_presupuesto_before_desactivar` | presupuesto | BEFORE | UPDATE | Cambiar estado a CANCELADO al desactivar presupuesto |
| `trg_presupuesto_before_reactivar` | presupuesto | BEFORE | UPDATE | Cambiar estado a EN PROCESO al reactivar presupuesto |
| `trg_presupuesto_estado_cancelado` | presupuesto | BEFORE | UPDATE | Desactivar presupuesto al cambiar estado a CANCELADO |
| `trg_presupuesto_estado_no_cancelado` | presupuesto | BEFORE | UPDATE | Reactivar presupuesto al cambiar desde CANCELADO a otro estado |

---

## Estructura General de un Trigger

### 1. Sintaxis Básica

```sql
DELIMITER $$

DROP TRIGGER IF EXISTS nombre_trigger$$

CREATE TRIGGER nombre_trigger
{BEFORE | AFTER} {INSERT | UPDATE | DELETE} ON nombre_tabla
FOR EACH ROW
BEGIN
    -- Variables locales (opcional)
    DECLARE variable1 tipo;
    DECLARE variable2 tipo;
    
    -- Lógica del trigger
    -- Puede usar OLD.campo para valores anteriores (UPDATE/DELETE)
    -- Puede usar NEW.campo para valores nuevos (INSERT/UPDATE)
    -- Puede modificar NEW.campo en triggers BEFORE
    
    -- Condicionales
    IF condición THEN
        -- Acciones
    END IF;
    
    -- Señales de error
    SIGNAL SQLSTATE '45000'
    SET MESSAGE_TEXT = 'Mensaje de error';
END$$

DELIMITER ;
```

### 2. Componentes Clave

| Componente | Descripción | Ejemplo |
|------------|-------------|---------|
| **DELIMITER** | Cambia el delimitador temporal para permitir `;` dentro del trigger | `DELIMITER $$` |
| **DROP IF EXISTS** | Elimina el trigger anterior si existe (para recrearlo) | `DROP TRIGGER IF EXISTS trg_nombre$$` |
| **Momento** | BEFORE (antes) o AFTER (después) del evento | `BEFORE UPDATE` |
| **Evento** | INSERT, UPDATE o DELETE | `UPDATE ON presupuesto` |
| **FOR EACH ROW** | Se ejecuta por cada fila afectada | Obligatorio |
| **OLD.campo** | Valor anterior del campo (UPDATE/DELETE) | `OLD.activo_presupuesto` |
| **NEW.campo** | Valor nuevo del campo (INSERT/UPDATE) | `NEW.activo_presupuesto` |
| **SET NEW.campo** | Modificar valor nuevo (solo BEFORE) | `SET NEW.id_estado = 5` |

---

## Patrones de Diseño Identificados

### Patrón 1: Generación Automática de Códigos Correlativos

**Propósito:** Generar códigos únicos secuenciales basados en un prefijo.

**Cuándo usar:** Para códigos que siguen el patrón `PREFIJO-NNN` (ej: `ART-001`, `ART-002`).

**Estructura:**
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_tabla_before_insert$$

CREATE TRIGGER trg_tabla_before_insert
BEFORE INSERT ON tabla
FOR EACH ROW
BEGIN
    DECLARE v_prefijo VARCHAR(50);
    DECLARE v_max_correlativo INT;
    
    -- 1. Obtener el prefijo desde tabla relacionada
    SELECT campo_prefijo 
    INTO v_prefijo
    FROM tabla_relacionada
    WHERE id = NEW.id_fk;
    
    -- 2. Calcular siguiente correlativo
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(campo_codigo, '-', -1) AS UNSIGNED)), 0) + 1 
    INTO v_max_correlativo
    FROM tabla
    WHERE id_fk = NEW.id_fk;
    
    -- 3. Generar código completo
    SET NEW.campo_codigo = CONCAT(v_prefijo, '-', LPAD(v_max_correlativo, 3, '0'));
END$$

DELIMITER ;
```

**Ejemplo real del proyecto:**
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_elemento_before_insert$$

CREATE TRIGGER trg_elemento_before_insert
BEFORE INSERT ON elemento
FOR EACH ROW
BEGIN
    DECLARE codigo_art VARCHAR(50);
    DECLARE max_correlativo INT;
    
    -- Obtener código del artículo
    SELECT codigo_articulo 
    INTO codigo_art
    FROM articulo
    WHERE id_articulo = NEW.id_articulo_elemento;
    
    -- Establecer estado por defecto si es NULL
    IF NEW.id_estado_elemento IS NULL THEN
        SET NEW.id_estado_elemento = 1;
    END IF;
    
    -- Calcular siguiente correlativo para este artículo
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(codigo_elemento, '-', -1) AS UNSIGNED)), 0) + 1 
    INTO max_correlativo
    FROM elemento
    WHERE id_articulo_elemento = NEW.id_articulo_elemento;
    
    -- Generar código: ART-001, ART-002, etc.
    SET NEW.codigo_elemento = CONCAT(codigo_art, '-', LPAD(max_correlativo, 3, '0'));
END$$

DELIMITER ;
```

**Desglose de la lógica:**

1. **SUBSTRING_INDEX(codigo_elemento, '-', -1)**: Extrae la parte después del último `-`
   - `'ART-025'` → `'025'`
   
2. **CAST(... AS UNSIGNED)**: Convierte string a número
   - `'025'` → `25`
   
3. **MAX(...)**: Obtiene el número más alto
   - `25, 30, 15` → `30`
   
4. **COALESCE(..., 0) + 1**: Si no hay registros, empieza en 1
   - Si hay registros: `30 + 1` = `31`
   - Si no hay registros: `0 + 1` = `1`
   
5. **LPAD(max_correlativo, 3, '0')**: Rellena con ceros a la izquierda
   - `31` → `'031'`
   - `1` → `'001'`
   
6. **CONCAT()**: Une el prefijo con el correlativo
   - `CONCAT('ART', '-', '031')` → `'ART-031'`

**Ventajas:**
- ✅ Códigos únicos automáticos
- ✅ Formato consistente
- ✅ No requiere lógica en la aplicación
- ✅ Seguro con transacciones

---

### Patrón 2: Establecer Valor por Defecto Condicional

**Propósito:** Asignar un valor por defecto solo si el campo viene NULL.

**Cuándo usar:** Cuando el valor por defecto en la definición de columna no es suficiente o depende de lógica.

**Estructura:**
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_tabla_before_insert$$

CREATE TRIGGER trg_tabla_before_insert
BEFORE INSERT ON tabla
FOR EACH ROW
BEGIN
    -- Si el campo es NULL, establecer valor por defecto
    IF NEW.campo IS NULL THEN
        SET NEW.campo = valor_por_defecto;
    END IF;
END$$

DELIMITER ;
```

**Ejemplo del proyecto:**
```sql
-- Dentro de trg_elemento_before_insert
IF NEW.id_estado_elemento IS NULL THEN
    SET NEW.id_estado_elemento = 1;  -- Estado "Disponible" por defecto
END IF;
```

**Casos de uso:**
- Establecer estado inicial
- Asignar categoría por defecto
- Inicializar contadores
- Asignar usuario creador

---

### Patrón 3: Validación con Lanzamiento de Error

**Propósito:** Validar reglas de negocio y rechazar la operación si no se cumplen.

**Cuándo usar:** Para validaciones críticas que deben detener la operación.

**Estructura:**
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_tabla_before_insert$$

CREATE TRIGGER trg_tabla_before_insert
BEFORE INSERT ON tabla
FOR EACH ROW
BEGIN
    DECLARE v_contador INT;
    
    -- Verificar condición de negocio
    IF NEW.campo_critico = valor_no_permitido THEN
        
        -- Contar registros conflictivos
        SELECT COUNT(*) INTO v_contador
        FROM tabla
        WHERE condicion_conflicto;
        
        -- Si hay conflicto, lanzar error
        IF v_contador > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Mensaje de error descriptivo';
        END IF;
        
    END IF;
END$$

DELIMITER ;
```

**Ejemplo real del proyecto:**
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_empresa_validar_ficticia_principal$$

CREATE TRIGGER trg_empresa_validar_ficticia_principal
BEFORE INSERT ON empresa
FOR EACH ROW
BEGIN
    DECLARE v_existe_principal INT;
    
    -- Si estamos marcando esta empresa como principal ficticia
    IF NEW.empresa_ficticia_principal = TRUE THEN
        
        -- Verificar si ya existe otra empresa ficticia principal activa
        SELECT COUNT(*) INTO v_existe_principal
        FROM empresa
        WHERE empresa_ficticia_principal = TRUE
        AND activo_empresa = TRUE;
        
        -- Si ya existe una, lanzar error
        IF v_existe_principal > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Ya existe una empresa ficticia principal activa. Solo puede haber una.';
        END IF;
        
    END IF;
END$$

DELIMITER ;
```

**Códigos SQLSTATE comunes:**
| Código | Significado |
|--------|-------------|
| `'45000'` | Error genérico de usuario |
| `'23000'` | Violación de integridad |
| `'22001'` | Dato demasiado largo |
| `'22003'` | Valor numérico fuera de rango |

---

### Patrón 4: Sincronización Bidireccional de Estados

**Propósito:** Mantener sincronizados dos campos relacionados que se afectan mutuamente.

**Cuándo usar:** Cuando un campo booleano (activo/inactivo) debe estar sincronizado con un campo de estado enum.

**Estructura (4 triggers necesarios):**

#### Trigger 1: Desactivar → Cambiar Estado
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_tabla_before_desactivar$$

CREATE TRIGGER trg_tabla_before_desactivar
BEFORE UPDATE ON tabla
FOR EACH ROW
BEGIN
    -- Si se está desactivando (1 → 0)
    IF OLD.activo = 1 AND NEW.activo = 0 THEN
        
        -- Cambiar a estado INACTIVO/CANCELADO
        SET NEW.id_estado = (
            SELECT id_estado 
            FROM estados 
            WHERE codigo_estado = 'INACTIVO' 
            LIMIT 1
        );
        
    END IF;
END$$

DELIMITER ;
```

#### Trigger 2: Reactivar → Cambiar Estado
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_tabla_before_reactivar$$

CREATE TRIGGER trg_tabla_before_reactivar
BEFORE UPDATE ON tabla
FOR EACH ROW
BEGIN
    -- Si se está reactivando (0 → 1)
    IF OLD.activo = 0 AND NEW.activo = 1 THEN
        
        -- Cambiar a estado ACTIVO/EN PROCESO
        SET NEW.id_estado = (
            SELECT id_estado 
            FROM estados 
            WHERE codigo_estado = 'ACTIVO' 
            LIMIT 1
        );
        
    END IF;
END$$

DELIMITER ;
```

#### Trigger 3: Estado Inactivo → Desactivar
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_tabla_estado_inactivo$$

CREATE TRIGGER trg_tabla_estado_inactivo
BEFORE UPDATE ON tabla
FOR EACH ROW
BEGIN
    DECLARE v_codigo_estado VARCHAR(20);
    
    -- Obtener código del nuevo estado
    SELECT codigo_estado 
    INTO v_codigo_estado
    FROM estados 
    WHERE id_estado = NEW.id_estado;
    
    -- Si el nuevo estado es INACTIVO/CANCELADO
    IF v_codigo_estado = 'INACTIVO' THEN
        -- Desactivar automáticamente
        SET NEW.activo = 0;
    END IF;
    
END$$

DELIMITER ;
```

#### Trigger 4: Estado Activo → Reactivar
```sql
DELIMITER $$

DROP TRIGGER IF EXISTS trg_tabla_estado_activo$$

CREATE TRIGGER trg_tabla_estado_activo
BEFORE UPDATE ON tabla
FOR EACH ROW
BEGIN
    DECLARE v_codigo_viejo VARCHAR(20);
    DECLARE v_codigo_nuevo VARCHAR(20);
    
    -- Obtener código del estado antiguo
    SELECT codigo_estado 
    INTO v_codigo_viejo
    FROM estados 
    WHERE id_estado = OLD.id_estado;
    
    -- Obtener código del estado nuevo
    SELECT codigo_estado 
    INTO v_codigo_nuevo
    FROM estados 
    WHERE id_estado = NEW.id_estado;
    
    -- Si cambia de INACTIVO a cualquier otro estado
    IF v_codigo_viejo = 'INACTIVO' AND v_codigo_nuevo != 'INACTIVO' THEN
        -- Reactivar automáticamente
        SET NEW.activo = 1;
    END IF;
    
END$$

DELIMITER ;
```

**Ejemplo completo del proyecto: Presupuestos**

```sql
-- ========================================================
-- DISPARADOR 1: AL DESACTIVAR PRESUPUESTO
-- Cambia automáticamente el estado a CANCELADO
-- ========================================================

DELIMITER $$

DROP TRIGGER IF EXISTS trg_presupuesto_before_desactivar$$

CREATE TRIGGER trg_presupuesto_before_desactivar
BEFORE UPDATE ON presupuesto
FOR EACH ROW
BEGIN
    -- Si se está desactivando (1 → 0)
    IF OLD.activo_presupuesto = 1 AND NEW.activo_presupuesto = 0 THEN
        
        -- Cambiar a CANCELADO
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'CANC' 
            LIMIT 1
        );
        
    END IF;
END$$

DELIMITER ;

-- ========================================================
-- DISPARADOR 2: AL REACTIVAR PRESUPUESTO
-- Cambia automáticamente el estado a EN PROCESO
-- ========================================================

DELIMITER $$

DROP TRIGGER IF EXISTS trg_presupuesto_before_reactivar$$

CREATE TRIGGER trg_presupuesto_before_reactivar
BEFORE UPDATE ON presupuesto
FOR EACH ROW
BEGIN
    -- Si se está reactivando (0 → 1)
    IF OLD.activo_presupuesto = 0 AND NEW.activo_presupuesto = 1 THEN
        
        -- Cambiar a EN PROCESO
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'PROC' 
            LIMIT 1
        );
        
    END IF;
END$$

DELIMITER ;

-- ========================================================
-- DISPARADOR 3: AL CAMBIAR ESTADO A CANCELADO
-- Desactiva automáticamente el presupuesto
-- ========================================================

DELIMITER $$

DROP TRIGGER IF EXISTS trg_presupuesto_estado_cancelado$$

CREATE TRIGGER trg_presupuesto_estado_cancelado
BEFORE UPDATE ON presupuesto
FOR EACH ROW
BEGIN
    DECLARE v_codigo_cancelado VARCHAR(20);
    
    -- Obtener el código de estado CANCELADO
    SELECT codigo_estado_ppto 
    INTO v_codigo_cancelado
    FROM estado_presupuesto 
    WHERE id_estado_ppto = NEW.id_estado_ppto;
    
    -- Si el nuevo estado es CANCELADO (código 'CANC')
    IF v_codigo_cancelado = 'CANC' THEN
        -- Desactivar el presupuesto automáticamente
        SET NEW.activo_presupuesto = 0;
    END IF;
    
END$$

DELIMITER ;

-- ========================================================
-- DISPARADOR 4: AL CAMBIAR ESTADO DESDE CANCELADO
-- Reactiva automáticamente el presupuesto
-- ========================================================

DELIMITER $$

DROP TRIGGER IF EXISTS trg_presupuesto_estado_no_cancelado$$

CREATE TRIGGER trg_presupuesto_estado_no_cancelado
BEFORE UPDATE ON presupuesto
FOR EACH ROW
BEGIN
    DECLARE v_codigo_viejo VARCHAR(20);
    DECLARE v_codigo_nuevo VARCHAR(20);
    
    -- Obtener el código del estado antiguo
    SELECT codigo_estado_ppto 
    INTO v_codigo_viejo
    FROM estado_presupuesto 
    WHERE id_estado_ppto = OLD.id_estado_ppto;
    
    -- Obtener el código del estado nuevo
    SELECT codigo_estado_ppto 
    INTO v_codigo_nuevo
    FROM estado_presupuesto 
    WHERE id_estado_ppto = NEW.id_estado_ppto;
    
    -- Si el estado anterior era CANCELADO y el nuevo NO es CANCELADO
    IF v_codigo_viejo = 'CANC' AND v_codigo_nuevo != 'CANC' THEN
        -- Reactivar el presupuesto automáticamente
        SET NEW.activo_presupuesto = 1;
    END IF;
    
END$$

DELIMITER ;
```

**Diagrama de flujo de sincronización:**

```
Usuario cambia activo_presupuesto de 1 a 0
    ↓
trg_presupuesto_before_desactivar se activa
    ↓
SET NEW.id_estado_ppto = ID de 'CANC'
    ↓
Presupuesto queda: activo=0, estado=CANCELADO
```

```
Usuario cambia id_estado_ppto a CANCELADO
    ↓
trg_presupuesto_estado_cancelado se activa
    ↓
SET NEW.activo_presupuesto = 0
    ↓
Presupuesto queda: activo=0, estado=CANCELADO
```

**Beneficios:**
- ✅ Coherencia automática entre campos
- ✅ No requiere lógica en la aplicación
- ✅ Imposible tener estados inconsistentes
- ✅ Sincronización bidireccional completa

---

## Convenciones de Nomenclatura

### Nombres de Triggers

| Patrón | Formato | Ejemplo |
|--------|---------|---------|
| General | `trg_{tabla}_{momento}_{acción}` | `trg_elemento_before_insert` |
| Validación | `trg_{tabla}_validar_{regla}` | `trg_empresa_validar_ficticia_principal` |
| Sincronización | `trg_{tabla}_{momento}_{operación}` | `trg_presupuesto_before_desactivar` |
| Por estado | `trg_{tabla}_estado_{nombre_estado}` | `trg_presupuesto_estado_cancelado` |

**Componentes del nombre:**
- **trg_**: Prefijo obligatorio para identificar triggers
- **{tabla}**: Nombre de la tabla en singular
- **{momento}**: `before` o `after`
- **{acción}**: `insert`, `update`, `delete`, o descripción específica

### Nombres de Variables

```sql
BEGIN
    -- Variables con prefijo v_
    DECLARE v_contador INT;
    DECLARE v_codigo VARCHAR(50);
    DECLARE v_existe BOOLEAN;
    
    -- Variables descriptivas
    DECLARE codigo_articulo VARCHAR(50);    -- Sin prefijo si es más legible
    DECLARE max_correlativo INT;
END
```

**Convenciones:**
- Usar `v_` como prefijo para variables locales (opcional pero recomendado)
- Nombres descriptivos en español
- Snake_case preferido
- Coincidencia con nombres de campos cuando sea apropiado

---

## Momentos de Ejecución

### BEFORE vs AFTER

| Característica | BEFORE | AFTER |
|----------------|--------|-------|
| **Puede modificar NEW** | ✅ Sí | ❌ No |
| **Puede acceder a OLD** | ✅ Sí (UPDATE/DELETE) | ✅ Sí (UPDATE/DELETE) |
| **Puede acceder a NEW** | ✅ Sí (INSERT/UPDATE) | ✅ Sí (INSERT/UPDATE) |
| **Puede cancelar operación** | ✅ Sí (SIGNAL) | ✅ Sí (SIGNAL) |
| **Puede ver ID autogenerado** | ❌ No (aún no existe) | ✅ Sí (ya existe) |
| **Uso típico** | Validar, modificar, generar valores | Logging, notificaciones, auditoría |

**Regla de oro:**
- **BEFORE**: Si necesitas **modificar** o **validar** datos
- **AFTER**: Si necesitas **registrar** o **propagar** cambios

---

## Tipos de Eventos

### INSERT

```sql
CREATE TRIGGER trg_tabla_before_insert
BEFORE INSERT ON tabla
FOR EACH ROW
BEGIN
    -- Solo NEW está disponible
    -- Puedes modificar NEW.campo
    SET NEW.codigo = generar_codigo();
END$$
```

**Uso típico:**
- Generar códigos automáticos
- Establecer valores por defecto
- Validar datos antes de insertar

### UPDATE

```sql
CREATE TRIGGER trg_tabla_before_update
BEFORE UPDATE ON tabla
FOR EACH ROW
BEGIN
    -- OLD y NEW están disponibles
    -- Puedes comparar OLD.campo con NEW.campo
    IF OLD.estado != NEW.estado THEN
        -- Hacer algo cuando cambia el estado
    END IF;
END$$
```

**Uso típico:**
- Sincronizar campos relacionados
- Validar cambios de estado
- Mantener coherencia de datos
- Auditar modificaciones

### DELETE

```sql
CREATE TRIGGER trg_tabla_before_delete
BEFORE DELETE ON tabla
FOR EACH ROW
BEGIN
    -- Solo OLD está disponible
    -- No puedes modificar nada (el registro se borrará)
    -- Puedes validar si se puede borrar
    IF OLD.tiene_dependencias = TRUE THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'No se puede eliminar: tiene dependencias';
    END IF;
END$$
```

**Uso típico:**
- Validar si se puede eliminar
- Eliminar registros relacionados en cascada
- Archivar datos antes de eliminar
- Auditar eliminaciones

---

## Plantillas Reutilizables

### Plantilla 1: Generación de Código Automático

```sql
-- ========================================================
-- TRIGGER: Generación automática de código
-- TABLA: {nombre_tabla}
-- DESCRIPCIÓN: Genera código único en formato PREFIJO-NNN
-- ========================================================

DELIMITER $$

DROP TRIGGER IF EXISTS trg_{tabla}_before_insert$$

CREATE TRIGGER trg_{tabla}_before_insert
BEFORE INSERT ON {tabla}
FOR EACH ROW
BEGIN
    DECLARE v_prefijo VARCHAR(50);
    DECLARE v_max_correlativo INT;
    
    -- Obtener prefijo desde tabla relacionada
    SELECT {campo_prefijo} 
    INTO v_prefijo
    FROM {tabla_relacionada}
    WHERE {id} = NEW.{id_fk};
    
    -- Calcular siguiente correlativo
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX({campo_codigo}, '-', -1) AS UNSIGNED)), 0) + 1 
    INTO v_max_correlativo
    FROM {tabla}
    WHERE {id_fk} = NEW.{id_fk};
    
    -- Generar código completo
    SET NEW.{campo_codigo} = CONCAT(v_prefijo, '-', LPAD(v_max_correlativo, 3, '0'));
END$$

DELIMITER ;
```

### Plantilla 2: Validación con Error

```sql
-- ========================================================
-- TRIGGER: Validación de regla de negocio
-- TABLA: {nombre_tabla}
-- DESCRIPCIÓN: {descripción de la regla}
-- ========================================================

DELIMITER $$

DROP TRIGGER IF EXISTS trg_{tabla}_validar_{regla}$$

CREATE TRIGGER trg_{tabla}_validar_{regla}
BEFORE {INSERT|UPDATE} ON {tabla}
FOR EACH ROW
BEGIN
    DECLARE v_contador INT;
    
    -- Verificar condición
    IF NEW.{campo} = {valor_critico} THEN
        
        -- Contar registros conflictivos
        SELECT COUNT(*) INTO v_contador
        FROM {tabla}
        WHERE {condicion_conflicto};
        
        -- Si hay conflicto, rechazar operación
        IF v_contador > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = '{Mensaje de error descriptivo}';
        END IF;
        
    END IF;
END$$

DELIMITER ;
```

### Plantilla 3: Sincronización de Estados (Set Completo)

```sql
-- ========================================================
-- TRIGGERS: Sincronización bidireccional de estados
-- TABLA: {nombre_tabla}
-- DESCRIPCIÓN: Mantiene sincronizados campo activo con campo estado
-- ========================================================

-- TRIGGER 1: Desactivar → Estado Inactivo
DELIMITER $$

DROP TRIGGER IF EXISTS trg_{tabla}_before_desactivar$$

CREATE TRIGGER trg_{tabla}_before_desactivar
BEFORE UPDATE ON {tabla}
FOR EACH ROW
BEGIN
    IF OLD.activo_{tabla} = 1 AND NEW.activo_{tabla} = 0 THEN
        SET NEW.id_estado = (
            SELECT id_estado 
            FROM estado_{tabla}
            WHERE codigo_estado = '{CODIGO_INACTIVO}'
            LIMIT 1
        );
    END IF;
END$$

DELIMITER ;

-- TRIGGER 2: Reactivar → Estado Activo
DELIMITER $$

DROP TRIGGER IF EXISTS trg_{tabla}_before_reactivar$$

CREATE TRIGGER trg_{tabla}_before_reactivar
BEFORE UPDATE ON {tabla}
FOR EACH ROW
BEGIN
    IF OLD.activo_{tabla} = 0 AND NEW.activo_{tabla} = 1 THEN
        SET NEW.id_estado = (
            SELECT id_estado 
            FROM estado_{tabla}
            WHERE codigo_estado = '{CODIGO_ACTIVO}'
            LIMIT 1
        );
    END IF;
END$$

DELIMITER ;

-- TRIGGER 3: Estado Inactivo → Desactivar
DELIMITER $$

DROP TRIGGER IF EXISTS trg_{tabla}_estado_inactivo$$

CREATE TRIGGER trg_{tabla}_estado_inactivo
BEFORE UPDATE ON {tabla}
FOR EACH ROW
BEGIN
    DECLARE v_codigo VARCHAR(20);
    
    SELECT codigo_estado INTO v_codigo
    FROM estado_{tabla}
    WHERE id_estado = NEW.id_estado;
    
    IF v_codigo = '{CODIGO_INACTIVO}' THEN
        SET NEW.activo_{tabla} = 0;
    END IF;
END$$

DELIMITER ;

-- TRIGGER 4: Estado Activo → Reactivar
DELIMITER $$

DROP TRIGGER IF EXISTS trg_{tabla}_estado_activo$$

CREATE TRIGGER trg_{tabla}_estado_activo
BEFORE UPDATE ON {tabla}
FOR EACH ROW
BEGIN
    DECLARE v_codigo_viejo VARCHAR(20);
    DECLARE v_codigo_nuevo VARCHAR(20);
    
    SELECT codigo_estado INTO v_codigo_viejo
    FROM estado_{tabla}
    WHERE id_estado = OLD.id_estado;
    
    SELECT codigo_estado INTO v_codigo_nuevo
    FROM estado_{tabla}
    WHERE id_estado = NEW.id_estado;
    
    IF v_codigo_viejo = '{CODIGO_INACTIVO}' AND v_codigo_nuevo != '{CODIGO_INACTIVO}' THEN
        SET NEW.activo_{tabla} = 1;
    END IF;
END$$

DELIMITER ;
```

### Plantilla 4: Establecer Valor por Defecto

```sql
-- ========================================================
-- TRIGGER: Establecer valor por defecto
-- TABLA: {nombre_tabla}
-- DESCRIPCIÓN: Asigna valor por defecto si viene NULL
-- ========================================================

DELIMITER $$

DROP TRIGGER IF EXISTS trg_{tabla}_before_insert$$

CREATE TRIGGER trg_{tabla}_before_insert
BEFORE INSERT ON {tabla}
FOR EACH ROW
BEGIN
    -- Si el campo es NULL, establecer valor por defecto
    IF NEW.{campo} IS NULL THEN
        SET NEW.{campo} = {valor_por_defecto};
    END IF;
END$$

DELIMITER ;
```

---

## Buenas Prácticas

### ✅ **DO (Hacer)**

1. **Usar `DROP TRIGGER IF EXISTS` antes de crear** para evitar errores
2. **Cambiar DELIMITER a `$$`** para permitir `;` dentro del trigger
3. **Restaurar DELIMITER a `;`** al final
4. **Declarar todas las variables al inicio del BEGIN**
5. **Comentar claramente el propósito del trigger**
6. **Usar nombres descriptivos para triggers y variables**
7. **Validar condiciones antes de ejecutar lógica costosa**
8. **Usar `LIMIT 1` en subconsultas que deben devolver un solo registro**
9. **Preferir BEFORE para modificaciones, AFTER para auditoría**
10. **Documentar la lógica compleja con comentarios inline**
11. **Probar exhaustivamente con diferentes escenarios**
12. **Considerar el rendimiento** (los triggers se ejecutan en cada operación)

### ❌ **DON'T (No hacer)**

1. **NO usar lógica de negocio compleja** (mejor en stored procedures)
2. **NO hacer consultas pesadas** que ralenticen operaciones
3. **NO crear triggers recursivos** (trigger que modifica tabla que tiene trigger)
4. **NO usar triggers para tareas que pueden hacerse en la aplicación** (logging simple)
5. **NO omitir manejo de errores** en operaciones críticas
6. **NO depender del orden de ejecución** de múltiples triggers (es impredecible)
7. **NO modificar tablas relacionadas** sin considerar cascadas
8. **NO usar `SELECT` sin `INTO`** (no tiene efecto)
9. **NO crear triggers sin documentación** (dificulta mantenimiento)
10. **NO usar triggers para validaciones simples** (mejor constraints)

---

## Ventajas de los Triggers

### ✅ **1. Automatización Total**

Los triggers se ejecutan automáticamente sin necesidad de código en la aplicación:
- Generación de códigos
- Sincronización de estados
- Validaciones de negocio

### ✅ **2. Integridad de Datos Garantizada**

Los triggers se ejecutan SIEMPRE, independientemente de:
- Qué aplicación realiza la operación
- Qué usuario ejecuta la consulta
- Si la operación es manual o automática

### ✅ **3. Centralización de Lógica**

La lógica está en la base de datos, no replicada en múltiples aplicaciones o servicios.

### ✅ **4. Transparencia**

Las reglas se aplican automáticamente sin que el código de la aplicación tenga que preocuparse.

### ✅ **5. Prevención de Estados Inconsistentes**

Los triggers evitan situaciones imposibles como:
- Presupuesto activo con estado CANCELADO
- Elemento sin código
- Múltiples empresas ficticias principales

---

## Desventajas y Consideraciones

### ⚠️ **1. Debugging Difícil**

Los triggers se ejecutan "invisiblemente", lo que puede dificultar:
- Rastrear errores
- Entender por qué cambia un valor
- Depurar lógica compleja

**Solución:** Documentar exhaustivamente y usar logging.

### ⚠️ **2. Rendimiento**

Los triggers se ejecutan en CADA operación, lo que puede:
- Ralentizar INSERT/UPDATE masivos
- Afectar transacciones grandes
- Consumir recursos en operaciones frecuentes

**Solución:** Mantener triggers simples y eficientes.

### ⚠️ **3. Mantenimiento**

Los triggers pueden olvidarse porque no están en el código de la aplicación.

**Solución:** Documentar en repositorio y mantener inventario actualizado.

### ⚠️ **4. Testing**

Los triggers complican las pruebas unitarias porque modifican datos automáticamente.

**Solución:** Considerar desactivar triggers en entornos de test cuando sea apropiado.

---

## Listar Triggers en la Base de Datos

### Ver Todos los Triggers

```sql
-- Ver todos los triggers de la base de datos
SHOW TRIGGERS;

-- Ver triggers de una tabla específica
SHOW TRIGGERS FROM nombre_base_datos WHERE `Table` = 'nombre_tabla';
```

### Ver Definición de un Trigger

```sql
-- Ver código completo de un trigger
SHOW CREATE TRIGGER nombre_trigger;
```

### Información de Triggers desde INFORMATION_SCHEMA

```sql
-- Lista completa de triggers con información detallada
SELECT 
    TRIGGER_SCHEMA,
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING,
    ACTION_STATEMENT
FROM INFORMATION_SCHEMA.TRIGGERS
WHERE TRIGGER_SCHEMA = 'nombre_base_datos'
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION;
```

---

## Eliminar Triggers

```sql
-- Eliminar un trigger específico
DROP TRIGGER IF EXISTS trg_nombre_trigger;

-- Con schema explícito
DROP TRIGGER IF EXISTS nombre_base_datos.trg_nombre_trigger;
```

---

## Checklist para Crear un Nuevo Trigger

Antes de crear un trigger, asegúrate de:

- [ ] Identificar claramente el propósito y la regla de negocio
- [ ] Determinar tabla, momento (BEFORE/AFTER) y evento (INSERT/UPDATE/DELETE)
- [ ] Verificar que no existe un trigger similar
- [ ] Diseñar la lógica de forma eficiente
- [ ] Considerar casos edge y valores NULL
- [ ] Documentar el trigger con comentarios claros
- [ ] Usar nomenclatura consistente
- [ ] Incluir `DROP TRIGGER IF EXISTS`
- [ ] Usar `DELIMITER $$` y restaurar al final
- [ ] Declarar variables al inicio del BEGIN
- [ ] Probar en entorno de desarrollo
- [ ] Validar con múltiples escenarios
- [ ] Documentar en este archivo (disparadores.md)
- [ ] Comunicar al equipo la existencia del nuevo trigger

---

## Relación con Otros Componentes

### Con Modelos PHP

Los modelos no necesitan saber que existen triggers:

```php
// En models/Elemento.php
public function insert_elemento($id_articulo, $descripcion, ...)
{
    // NO es necesario generar codigo_elemento, el trigger lo hace
    $sql = "INSERT INTO elemento (id_articulo_elemento, descripcion_elemento, ...)
            VALUES (:id_articulo, :descripcion, ...)";
    
    // El trigger trg_elemento_before_insert genera automáticamente el codigo_elemento
    return $this->conexion->ejecutar($sql, $params);
}
```

### Con Controllers

Los controllers tampoco necesitan lógica especial:

```php
// En controller/presupuesto.php
case "desactivar":
    // Solo cambiar el campo activo
    $resultado = $presupuesto->desactivar_presupuestoxid($_POST["id_presupuesto"]);
    
    // El trigger trg_presupuesto_before_desactivar cambia automáticamente
    // el estado a CANCELADO, no hace falta hacerlo manualmente
    break;
```

---

## Casos de Uso del Proyecto

### 1. Generación Automática de Códigos de Elementos

**Problema:** Cada elemento necesita un código único en formato `ARTICULO-001`.

**Solución:** `trg_elemento_before_insert`

**Resultado:** Al insertar un elemento, automáticamente se le asigna el siguiente código correlativo para ese artículo.

---

### 2. Una Sola Empresa Ficticia Principal

**Problema:** El sistema necesita exactamente una empresa ficticia como principal para presupuestos.

**Solución:** `trg_empresa_validar_ficticia_principal`

**Resultado:** Si intentas crear/activar una segunda empresa ficticia principal, la operación se rechaza con error.

---

### 3. Coherencia entre Estado y Campo Activo en Presupuestos

**Problema:** Un presupuesto puede tener activo=1 pero estado=CANCELADO (inconsistente).

**Solución:** 4 triggers de sincronización bidireccional

**Resultado:** Es imposible tener un presupuesto con estado inconsistente. Si desactivas el presupuesto, automáticamente se marca como CANCELADO. Si cambias el estado a CANCELADO, automáticamente se desactiva.

---

## Resumen

Los **disparadores (triggers)** en el proyecto MDR:

1. ✅ **Automatizan tareas repetitivas** como generación de códigos
2. ✅ **Garantizan integridad de datos** con validaciones automáticas
3. ✅ **Sincronizan estados relacionados** manteniendo coherencia
4. ✅ **Centralizan reglas de negocio** en la base de datos
5. ✅ **Simplifican la lógica** en modelos y controllers
6. ✅ **Previenen estados inconsistentes** entre campos relacionados
7. ✅ **Siguen patrones consistentes** reutilizables en nuevos triggers

**Patrones principales identificados:**
- Generación de códigos correlativos
- Establecer valores por defecto
- Validación con lanzamiento de error
- Sincronización bidireccional de estados (4 triggers)

Siguiendo estos patrones y plantillas, los nuevos triggers se integrarán perfectamente con la arquitectura existente.

---

## Enlaces Relacionados

- [Documentación de Vistas](vistas.md) - Vistas SQL que usan las tablas con triggers
- [Documentación de Models](models.md) - Cómo los modelos interactúan con triggers
- [Documentación de Controllers](controller.md) - Flujo de datos con triggers automáticos
- [Estructura de Carpetas](estructura_carpetas.md) - Arquitectura general del proyecto
