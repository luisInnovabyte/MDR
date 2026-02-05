# 📋 REPORTE DE INVESTIGACIÓN: SISTEMA DE VERSIONES DE PRESUPUESTOS MDR

**Fecha de Investigación**: 30 de enero de 2026  
**Proyecto**: MDR ERP Manager  
**Versión del Documento**: 1.0  
**Investigador**: GitHub Copilot (Claude Sonnet 4.5)

---

## 🎯 RESUMEN EJECUTIVO

He realizado una investigación exhaustiva del sistema de versiones de presupuestos en el proyecto MDR. El sistema está **PARCIALMENTE IMPLEMENTADO** a nivel de base de datos con triggers y estructura completa, pero **FALTA TODA LA IMPLEMENTACIÓN EN EL BACKEND** (modelos y controladores) y frontend (vistas).

### Estado General del Sistema:

| Componente | Estado | Completitud | Observaciones |
|------------|--------|-------------|---------------|
| **Base de Datos** | ✅ Implementada | 100% | Estructura completa con campos estándar |
| **Triggers** | ✅ Implementados | 100% | 10 triggers funcionales documentados |
| **Modelos PHP** | ❌ Sin implementar | 10% | Solo 1 método básico de 8 necesarios |
| **Controladores** | ❌ Sin implementar | 5% | Solo 1 operación de 7 críticas |
| **Frontend** | ❌ Sin implementar | 0% | No existe UI para versiones |
| **Documentación** | ✅ Completa | 100% | Archivos detallados en ./BD/docs/ |

### Conclusión:
El sistema de versiones está **ESTRUCTURALMENTE COMPLETO** a nivel de base de datos pero **COMPLETAMENTE INOPERATIVO** desde la perspectiva del usuario final.

**Tiempo estimado de implementación**: 43-51 horas distribuidas en 6 fases.

---

## 📊 1. ESTRUCTURA DE BASE DE DATOS

### 1.1 Tabla `presupuesto` (Cabecera)

**Campos relacionados con versiones:**

```sql
CREATE TABLE presupuesto (
    -- ... otros campos ...
    
    version_actual_presupuesto INT UNSIGNED DEFAULT 1 
        COMMENT 'Número de versión activa actual (la que se muestra/edita)',
    
    estado_general_presupuesto ENUM('borrador', 'enviado', 'aprobado', 'rechazado', 'cancelado') 
        DEFAULT 'borrador' 
        COMMENT 'Estado general del presupuesto (sincronizado con version_actual)',
    
    -- ... otros campos ...
    
    INDEX idx_version_actual_presupuesto (version_actual_presupuesto),
    INDEX idx_estado_general_presupuesto (estado_general_presupuesto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

**Campos clave:**
- `version_actual_presupuesto`: Indica qué versión está activa (la que se muestra/edita)
- `estado_general_presupuesto`: Estado sincronizado automáticamente con la versión actual

### 1.2 Tabla `presupuesto_version` (Control de Versiones)

**Estructura completa:**

```sql
CREATE TABLE presupuesto_version (
    -- Identificación
    id_version_presupuesto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_presupuesto INT UNSIGNED NOT NULL COMMENT 'FK a presupuesto (cabecera)',
    numero_version_presupuesto INT UNSIGNED NOT NULL COMMENT 'Número lógico (1, 2, 3...)',
    version_padre_presupuesto INT UNSIGNED NULL COMMENT 'FK autorreferencial (genealogía)',
    
    -- Control de estado
    estado_version_presupuesto ENUM('borrador', 'enviado', 'aprobado', 'rechazado', 'cancelado') 
        NOT NULL DEFAULT 'borrador',
    motivo_modificacion_version TEXT NULL COMMENT 'Razón de creación de la versión',
    
    -- Auditoría de creación
    fecha_creacion_version TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    creado_por_version INT UNSIGNED NOT NULL DEFAULT 1 COMMENT 'Usuario creador',
    
    -- Auditoría de envío
    fecha_envio_version DATETIME NULL COMMENT 'Fecha envío al cliente',
    enviado_por_version INT UNSIGNED NULL COMMENT 'Usuario que envió',
    
    -- Auditoría de aprobación/rechazo
    fecha_aprobacion_version DATETIME NULL COMMENT 'Fecha aprobación cliente',
    fecha_rechazo_version DATETIME NULL COMMENT 'Fecha rechazo cliente',
    motivo_rechazo_version TEXT NULL COMMENT 'Motivo rechazo',
    
    -- Gestión de documentos
    ruta_pdf_version VARCHAR(255) NULL COMMENT 'Ruta PDF generado',
    
    -- Campos estándar obligatorios
    activo_version TINYINT(1) NOT NULL DEFAULT 1,
    created_at_version TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_version TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Foreign Keys
    CONSTRAINT fk_version_presupuesto 
        FOREIGN KEY (id_presupuesto) 
        REFERENCES presupuesto(id_presupuesto)
        ON DELETE CASCADE ON UPDATE CASCADE,
    
    CONSTRAINT fk_version_padre 
        FOREIGN KEY (version_padre_presupuesto) 
        REFERENCES presupuesto_version(id_version_presupuesto)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    -- Índices
    INDEX idx_id_presupuesto_version (id_presupuesto),
    INDEX idx_numero_version (numero_version_presupuesto),
    INDEX idx_version_padre (version_padre_presupuesto),
    INDEX idx_estado_version (estado_version_presupuesto),
    INDEX idx_fecha_creacion_version (fecha_creacion_version),
    INDEX idx_fecha_envio_version (fecha_envio_version),
    INDEX idx_presupuesto_numero_version (id_presupuesto, numero_version_presupuesto),
    INDEX idx_creado_por (creado_por_version),
    INDEX idx_enviado_por (enviado_por_version),
    
    -- Restricciones
    UNIQUE KEY uk_presupuesto_numero_version (id_presupuesto, numero_version_presupuesto)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Control de versiones de presupuestos';
```

**Características importantes:**

1. **Genealogía**: `version_padre_presupuesto` permite rastrear de qué versión se creó cada nueva versión
2. **Estados independientes**: Cada versión tiene su propio estado y fechas
3. **Auditoría completa**: Registra quién y cuándo hizo cada acción
4. **Inmutabilidad**: Las versiones no-borrador no pueden modificarse (protegido por triggers)

### 1.3 Tabla `linea_presupuesto` (Líneas de Detalle)

**Campo clave para versiones:**

```sql
CREATE TABLE linea_presupuesto (
    -- ... otros campos ...
    
    id_version_presupuesto INT UNSIGNED NOT NULL 
        COMMENT 'FK: Versión del presupuesto a la que pertenece esta línea',
    
    -- ... otros campos ...
    
    CONSTRAINT fk_linea_ppto_version 
        FOREIGN KEY (id_version_presupuesto) 
        REFERENCES presupuesto_version(id_version_presupuesto) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

**Importante**: 
- Cada versión tiene su **propio conjunto completo de líneas**
- No se modifican líneas existentes, se duplican en nuevas versiones
- La relación es directa: `linea_presupuesto` → `presupuesto_version` (no a `presupuesto`)

---

## ⚙️ 2. TRIGGERS IMPLEMENTADOS

### 2.1 Trigger: Crear Versión 1 Automáticamente

```sql
DELIMITER $$

CREATE TRIGGER trg_presupuesto_after_insert
AFTER INSERT ON presupuesto
FOR EACH ROW
BEGIN
    INSERT INTO presupuesto_version (
        id_presupuesto,
        numero_version_presupuesto,
        version_padre_presupuesto,
        estado_version_presupuesto,
        creado_por_version,
        motivo_modificacion_version,
        fecha_creacion_version
    ) VALUES (
        NEW.id_presupuesto,
        1,
        NULL,
        'borrador',
        1,
        'Versión inicial',
        NOW()
    );
END$$

DELIMITER ;
```

**Función**: Al crear un presupuesto, automáticamente se crea la versión 1 en estado borrador.

### 2.2 Trigger: Proteger Líneas No-Borrador

```sql
DELIMITER $$

CREATE TRIGGER trg_linea_presupuesto_before_update
BEFORE UPDATE ON linea_presupuesto
FOR EACH ROW
BEGIN
    DECLARE estado_version VARCHAR(20);
    
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden modificar líneas de versiones que no están en borrador';
    END IF;
END$$

DELIMITER ;
```

**Función**: Impide modificar líneas de versiones que no están en estado 'borrador'.

### 2.3 Trigger: Prevenir Eliminación de Líneas

```sql
DELIMITER $$

CREATE TRIGGER trg_linea_presupuesto_before_delete
BEFORE DELETE ON linea_presupuesto
FOR EACH ROW
BEGIN
    DECLARE estado_version VARCHAR(20);
    
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar líneas de versiones que no están en borrador';
    END IF;
END$$

DELIMITER ;
```

**Función**: Impide eliminar líneas de versiones no-borrador.

### 2.4 Trigger: Validar Creación de Nuevas Versiones

```sql
DELIMITER $$

CREATE TRIGGER trg_presupuesto_version_before_insert_validar
BEFORE INSERT ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE estado_actual VARCHAR(20);
    DECLARE hay_borrador INT;
    
    -- Validar que el presupuesto no esté aprobado o cancelado
    SELECT estado_general_presupuesto INTO estado_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto;
    
    IF estado_actual IN ('aprobado', 'cancelado') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden crear nuevas versiones de presupuestos aprobados o cancelados';
    END IF;
    
    -- Validar que no exista ya una versión en borrador
    SELECT COUNT(*) INTO hay_borrador
    FROM presupuesto_version
    WHERE id_presupuesto = NEW.id_presupuesto
    AND estado_version_presupuesto = 'borrador'
    AND activo_version = 1;
    
    IF hay_borrador > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Ya existe una versión en borrador. Complete o cancele esa versión antes de crear una nueva';
    END IF;
    
    -- Validar que solo se creen nuevas versiones desde estados válidos
    IF NEW.numero_version_presupuesto > 1 THEN
        SELECT estado_version_presupuesto INTO estado_actual
        FROM presupuesto_version
        WHERE id_version_presupuesto = NEW.version_padre_presupuesto;
        
        IF estado_actual NOT IN ('enviado', 'rechazado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Solo se pueden crear nuevas versiones desde versiones enviadas o rechazadas';
        END IF;
    END IF;
END$$

DELIMITER ;
```

**Reglas validadas:**
1. No crear versiones si el presupuesto está aprobado o cancelado
2. No crear nueva versión si ya existe una en borrador
3. Solo crear desde estados 'enviado' o 'rechazado'

### 2.5 Trigger: Validar Transiciones de Estado

```sql
DELIMITER $$

CREATE TRIGGER trg_version_validar_transicion_estado
BEFORE UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    -- Validar transiciones de estado permitidas
    IF OLD.estado_version_presupuesto = 'borrador' THEN
        IF NEW.estado_version_presupuesto NOT IN ('borrador', 'enviado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde borrador solo se puede pasar a enviado o cancelado';
        END IF;
    ELSEIF OLD.estado_version_presupuesto = 'enviado' THEN
        IF NEW.estado_version_presupuesto NOT IN ('enviado', 'aprobado', 'rechazado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde enviado solo se puede pasar a aprobado, rechazado o cancelado';
        END IF;
    ELSEIF OLD.estado_version_presupuesto = 'aprobado' THEN
        IF NEW.estado_version_presupuesto != 'aprobado' THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Una versión aprobada no puede cambiar de estado';
        END IF;
    ELSEIF OLD.estado_version_presupuesto = 'rechazado' THEN
        IF NEW.estado_version_presupuesto NOT IN ('rechazado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde rechazado solo se puede pasar a cancelado';
        END IF;
    ELSEIF OLD.estado_version_presupuesto = 'cancelado' THEN
        IF NEW.estado_version_presupuesto != 'cancelado' THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Una versión cancelada no puede cambiar de estado';
        END IF;
    END IF;
END$$

DELIMITER ;
```

**Workflow permitido:**
```
borrador → enviado | cancelado
enviado → aprobado | rechazado | cancelado
aprobado → [INMUTABLE]
rechazado → cancelado
cancelado → [INMUTABLE]
```

### 2.6 Trigger: Auto-establecer Fechas

```sql
DELIMITER $$

CREATE TRIGGER trg_version_auto_fechas
BEFORE UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    -- Si cambia a enviado, establecer fecha_envio
    IF NEW.estado_version_presupuesto = 'enviado' AND OLD.estado_version_presupuesto != 'enviado' THEN
        SET NEW.fecha_envio_version = NOW();
    END IF;
    
    -- Si cambia a aprobado, establecer fecha_aprobacion
    IF NEW.estado_version_presupuesto = 'aprobado' AND OLD.estado_version_presupuesto != 'aprobado' THEN
        SET NEW.fecha_aprobacion_version = NOW();
    END IF;
    
    -- Si cambia a rechazado, establecer fecha_rechazo
    IF NEW.estado_version_presupuesto = 'rechazado' AND OLD.estado_version_presupuesto != 'rechazado' THEN
        SET NEW.fecha_rechazo_version = NOW();
    END IF;
END$$

DELIMITER ;
```

**Función**: Establece automáticamente las fechas de envío, aprobación y rechazo.

### 2.7 Trigger: Auto-generar Ruta PDF

```sql
DELIMITER $$

CREATE TRIGGER trg_version_auto_ruta_pdf
BEFORE UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE numero_ppto VARCHAR(50);
    
    -- Si cambia a enviado y no tiene ruta PDF, generarla
    IF NEW.estado_version_presupuesto = 'enviado' AND OLD.estado_version_presupuesto != 'enviado' 
       AND NEW.ruta_pdf_version IS NULL THEN
        
        SELECT numero_presupuesto INTO numero_ppto
        FROM presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
        
        SET NEW.ruta_pdf_version = CONCAT('/documentos/presupuestos/', numero_ppto, '_v', NEW.numero_version_presupuesto, '.pdf');
    END IF;
END$$

DELIMITER ;
```

**Formato**: `/documentos/presupuestos/{numero_presupuesto}_v{numero_version}.pdf`

### 2.8 Trigger: Sincronizar Estado Cabecera

```sql
DELIMITER $$

CREATE TRIGGER trg_version_sync_estado_cabecera
AFTER UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE es_version_actual INT;
    
    -- Verificar si esta es la versión actual
    SELECT COUNT(*) INTO es_version_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto
    AND version_actual_presupuesto = NEW.numero_version_presupuesto;
    
    -- Si es la versión actual, sincronizar estado
    IF es_version_actual > 0 THEN
        UPDATE presupuesto
        SET estado_general_presupuesto = NEW.estado_version_presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
    END IF;
END$$

DELIMITER ;
```

**Función**: Mantiene sincronizado `estado_general_presupuesto` con el estado de la versión actual.

### 2.9 Trigger: Prevenir Eliminación con Dependencias

```sql
DELIMITER $$

CREATE TRIGGER trg_presupuesto_version_before_delete
BEFORE DELETE ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE tiene_lineas INT;
    DECLARE tiene_hijas INT;
    
    -- Verificar si tiene líneas asociadas
    SELECT COUNT(*) INTO tiene_lineas
    FROM linea_presupuesto
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    IF tiene_lineas > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene líneas asociadas';
    END IF;
    
    -- Verificar si tiene versiones hijas
    SELECT COUNT(*) INTO tiene_hijas
    FROM presupuesto_version
    WHERE version_padre_presupuesto = OLD.id_version_presupuesto;
    
    IF tiene_hijas > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene versiones hijas';
    END IF;
    
    -- Solo permitir eliminar versiones en borrador
    IF OLD.estado_version_presupuesto != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Solo se pueden eliminar versiones en estado borrador';
    END IF;
END$$

DELIMITER ;
```

**Validaciones:**
- Bloquea si tiene líneas asociadas
- Bloquea si tiene versiones hijas
- Bloquea si no está en borrador

### 2.10 Trigger: Auto-calcular Número de Versión

```sql
DELIMITER $$

CREATE TRIGGER trg_presupuesto_version_before_insert_numero
BEFORE INSERT ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE siguiente_numero INT;
    
    -- Si no se especificó número de versión, calcularlo
    IF NEW.numero_version_presupuesto IS NULL OR NEW.numero_version_presupuesto = 0 THEN
        SELECT COALESCE(MAX(numero_version_presupuesto), 0) + 1 INTO siguiente_numero
        FROM presupuesto_version
        WHERE id_presupuesto = NEW.id_presupuesto;
        
        SET NEW.numero_version_presupuesto = siguiente_numero;
    END IF;
END$$

DELIMITER ;
```

**Función**: Calcula automáticamente el siguiente `numero_version_presupuesto` si no se especifica.

---

## 💾 3. ESTADO DE LOS MODELOS PHP

### 3.1 Archivo: `models/Presupuesto.php`

**Ubicación**: `W:\MDR\models\Presupuesto.php`

#### Métodos Relacionados con Versiones:

##### ✅ Implementado:

```php
public function get_info_version($id_version_presupuesto)
{
    // Obtiene información básica de una versión
    // Incluye datos del presupuesto, versión y cliente
    // PROBLEMA: No obtiene las líneas de la versión
}
```

##### ❌ NO Implementados (CRÍTICOS FALTANTES):

**1. crear_nueva_version($id_presupuesto, $motivo, $id_usuario)**
```php
// FUNCIÓN: Crear nueva versión completa
// - Validar que se pueda crear nueva versión
// - Obtener la versión actual (padre)
// - Crear registro en presupuesto_version
// - Duplicar líneas de la versión anterior
// - Actualizar version_actual_presupuesto
// - Usar transacción PDO para atomicidad
// RETORNA: id_version_presupuesto nuevo o false
```

**2. duplicar_lineas_version($id_version_origen, $id_version_destino)**
```php
// FUNCIÓN: Duplicar líneas entre versiones
// - Copia TODAS las líneas de origen a destino
// - Mantiene jerarquía (padres/hijos si aplica)
// - Actualiza número de línea y orden
// - Maneja errores con transacción
// RETORNA: true/false
```

**3. get_versiones_presupuesto($id_presupuesto)**
```php
// FUNCIÓN: Listar todas las versiones de un presupuesto
// - Incluye datos de estados, fechas, usuarios
// - Ordenado por numero_version DESC
// - Con información de versión padre
// RETORNA: Array de versiones
```

**4. cambiar_estado_version($id_version, $nuevo_estado, $motivo_rechazo = null)**
```php
// FUNCIÓN: Cambiar estado de una versión
// - Valida transición de estado permitida
// - Actualiza estado_version_presupuesto
// - Registra motivo si es rechazo
// - Sincroniza con presupuesto cabecera
// RETORNA: true/false
```

**5. obtener_version_actual($id_presupuesto)**
```php
// FUNCIÓN: Obtener versión activa
// - Retorna datos de la versión actualmente activa
// - Incluye número de líneas, totales, estado
// RETORNA: Array con datos de versión
```

**6. comparar_versiones($id_version_1, $id_version_2)**
```php
// FUNCIÓN: Comparar dos versiones
// - Compara líneas entre versiones
// - Identifica: añadidos, eliminados, modificados
// - Calcula diferencias de precio
// RETORNA: Array con diferencias
```

**7. establecer_version_actual($id_presupuesto, $numero_version)**
```php
// FUNCIÓN: Cambiar versión activa
// - Cambia la versión activa del presupuesto
// - Actualiza version_actual_presupuesto
// - Valida que la versión existe
// - Sincroniza estado general
// RETORNA: true/false
```

**8. get_historial_versiones($id_presupuesto)**
```php
// FUNCIÓN: Historial completo de versiones
// - Incluye línea de tiempo de cambios
// - Con información de usuarios y fechas
// - Para reportes y auditoría
// RETORNA: Array ordenado cronológicamente
```

### 3.2 Archivo: `models/LineaPresupuesto.php`

**Ubicación**: `W:\MDR\models\LineaPresupuesto.php`

#### Estado: ✅ FUNCIONAL CON VERSIONES

**Métodos implementados que trabajan correctamente con versiones:**

```php
get_lineas_version($id_version_presupuesto)     // Obtiene líneas de una versión
get_totales_version($id_version_presupuesto)    // Obtiene totales (PIE)
get_lineaxid($id_linea_ppto)                    // Obtiene una línea por ID
insert_linea($datos)                             // Inserta nueva línea
update_linea($id_linea, $datos)                 // Actualiza línea
delete_lineaxid($id_linea)                      // Soft delete
activar_lineaxid($id_linea)                     // Reactiva línea
```

**Observación**: Este modelo **SÍ está preparado** para trabajar con versiones, ya que todas las operaciones reciben o usan `id_version_presupuesto`.

---

## 🎮 4. ESTADO DE LOS CONTROLADORES

### 4.1 Archivo: `controller/presupuesto.php`

**Ubicación**: `W:\MDR\controller\presupuesto.php`

#### Operaciones Implementadas:

```php
✅ "listar"              // Lista todos los presupuestos
✅ "guardaryeditar"      // INSERT/UPDATE de presupuesto
✅ "mostrar"             // Obtiene presupuesto por ID
✅ "eliminar"            // Soft delete presupuesto
✅ "activar"             // Reactiva presupuesto
✅ "desactivar"          // Desactiva presupuesto
✅ "verificar"           // Valida unicidad
✅ "listar_disponibles"  // Solo activos
✅ "estadisticas"        // Métricas del sistema
✅ "get_info_version"    // Info básica de versión
✅ "get_fechas_evento"   // Obtiene fechas del presupuesto
```

#### Operaciones FALTANTES (CRÍTICAS):

```php
❌ "crear_version"            // Crear nueva versión
❌ "duplicar_version"         // Duplicar versión con líneas
❌ "listar_versiones"         // Listar versiones de un presupuesto
❌ "cambiar_estado_version"   // Cambiar estado (borrador→enviado→aprobado)
❌ "comparar_versiones"       // Comparar dos versiones
❌ "obtener_version_actual"   // Get versión activa
❌ "establecer_version_actual" // Cambiar versión activa
```

#### Estructura de Operaciones Faltantes:

**Ejemplo 1: crear_version**
```php
case "crear_version":
    // Recibe: id_presupuesto, motivo
    $id_presupuesto = $_POST["id_presupuesto"];
    $motivo = htmlspecialchars(trim($_POST["motivo"]), ENT_QUOTES, 'UTF-8');
    $id_usuario = 1; // TODO: Obtener de sesión
    
    try {
        $resultado = $presupuesto->crear_nueva_version($id_presupuesto, $motivo, $id_usuario);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Nueva versión creada correctamente',
                'id_version' => $resultado
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al crear la versión'
            ], JSON_UNESCAPED_UNICODE);
        }
    } catch (Exception $e) {
        $registro->registrarActividad('admin', 'presupuesto.php', 'crear_version',
            "Error: " . $e->getMessage(), 'error');
        
        echo json_encode([
            'success' => false,
            'message' => $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    break;
```

**Ejemplo 2: listar_versiones**
```php
case "listar_versiones":
    // Recibe: id_presupuesto
    $id_presupuesto = $_POST["id_presupuesto"];
    $datos = $presupuesto->get_versiones_presupuesto($id_presupuesto);
    
    $data = array();
    foreach ($datos as $row) {
        $data[] = array(
            "id_version" => $row["id_version_presupuesto"],
            "numero_version" => $row["numero_version_presupuesto"],
            "estado" => $row["estado_version_presupuesto"],
            "fecha_creacion" => $row["fecha_creacion_version"],
            "motivo" => $row["motivo_modificacion_version"],
            "es_actual" => $row["es_actual"],
            "opciones" => '...' // Botones de acción
        );
    }
    
    $results = array(
        "draw" => 1,
        "recordsTotal" => count($data),
        "recordsFiltered" => count($data),
        "data" => $data
    );
    
    header('Content-Type: application/json');
    echo json_encode($results, JSON_UNESCAPED_UNICODE);
    break;
```

### 4.2 Archivo: `controller/lineapresupuesto.php`

**Ubicación**: `W:\MDR\controller\lineapresupuesto.php`

#### Estado: ✅ FUNCIONAL CON VERSIONES

**Operaciones implementadas:**
```php
✅ "listar"              // Lista líneas de una versión
✅ "totales"             // Obtiene totales de versión
✅ "mostrar"             // Obtiene línea por ID
✅ "duplicar"            // Duplica una línea
✅ "guardaryeditar"      // INSERT/UPDATE línea
✅ "desactivar"          // Soft delete
✅ "eliminar"            // Soft delete
✅ "activar"             // Reactiva línea
✅ "eliminar_fisico"     // DELETE físico (peligroso)
✅ "validar_totales"     // Valida cálculos
```

**Observación**: Este controlador **SÍ está preparado** para trabajar con versiones, ya que todas las operaciones reciben `id_version_presupuesto`.

---

## 🖥️ 5. ESTADO DEL FRONTEND

### 5.1 Vista: `view/Presupuesto/mntpresupuesto.php` (Listado)

**Ubicación**: `W:\MDR\view\Presupuesto\mntpresupuesto.php`

**Estado actual:**
- ✅ Lista todos los presupuestos
- ✅ Muestra datos básicos (número, fecha, cliente, estado)
- ✅ Incluye campo `version_actual_presupuesto` en los datos
- ❌ **NO muestra información de versiones**
- ❌ **NO tiene botón "Ver Versiones"**
- ❌ **NO tiene botón "Nueva Versión"**

**Modificaciones necesarias:**

1. Añadir columna "Versiones" en DataTable:
```javascript
{
    data: null,
    render: function(data) {
        return `<span class="badge bg-info">v${data.version_actual_presupuesto}</span>
                <button class="btn btn-sm btn-secondary" onclick="verVersiones(${data.id_presupuesto})">
                    <i class="fa fa-history"></i> Ver Versiones
                </button>`;
    }
}
```

2. Añadir badge de estado de versión actual

### 5.2 Vista: `view/Presupuesto/formularioPresupuesto.php`

**Ubicación**: `W:\MDR\view\Presupuesto\formularioPresupuesto.php`

**Estado estimado:**
- ✅ Formulario de edición de presupuesto
- ❌ **NO gestiona versiones**
- ❌ **NO muestra selector de versiones**
- ❌ **NO indica si está editando una versión específica**

**Modificaciones necesarias:**

1. Añadir selector de versión en header
2. Indicador visual de versión actual
3. Botón "Nueva Versión" en toolbar
4. Bloquear edición si versión no es borrador
5. Alert si está viendo versión histórica (no actual)

### 5.3 Vistas FALTANTES (Nuevas a Crear)

#### Vista 1: `view/Presupuesto/modalVersiones.php`

**Propósito**: Modal para gestionar versiones de un presupuesto

**Estructura necesaria:**
```html
<div class="modal fade" id="modalVersiones">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Versiones del Presupuesto: <span id="numeroPptoVersiones"></span></h5>
                <button type="button" class="btn btn-primary" onclick="crearNuevaVersion()">
                    <i class="fa fa-plus"></i> Nueva Versión
                </button>
            </div>
            <div class="modal-body">
                <table id="tblVersiones" class="table table-striped">
                    <thead>
                        <tr>
                            <th>Versión</th>
                            <th>Estado</th>
                            <th>Fecha Creación</th>
                            <th>Motivo</th>
                            <th>Usuario</th>
                            <th>PDF</th>
                            <th>Actual</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
```

**Funcionalidades:**
- DataTable con todas las versiones
- Badge indicando versión actual
- Botones de acción por versión:
  - 🔍 Ver detalles
  - 📄 Ver PDF (si existe)
  - 📊 Comparar con otra
  - ⚙️ Cambiar estado
  - ✅ Establecer como actual
- Botón "Nueva Versión" (solo si es posible según reglas)

#### Vista 2: `view/Presupuesto/modalComparar.php`

**Propósito**: Modal para comparar dos versiones

**Estructura necesaria:**
```html
<div class="modal fade" id="modalComparar">
    <div class="modal-dialog modal-fullscreen">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Comparar Versiones</h5>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-6">
                        <label>Versión 1:</label>
                        <select id="version1" class="form-control"></select>
                    </div>
                    <div class="col-6">
                        <label>Versión 2:</label>
                        <select id="version2" class="form-control"></select>
                    </div>
                </div>
                
                <div id="resultadoComparacion">
                    <!-- Tabla comparativa con diferencias resaltadas -->
                </div>
            </div>
        </div>
    </div>
</div>
```

**Funcionalidades:**
- Selectores de dos versiones
- Tabla comparativa de líneas
- Resaltar diferencias con colores:
  - Verde: Añadidos en v2
  - Rojo: Eliminados de v1
  - Amarillo: Modificados
- Resumen de diferencias de totales

### 5.4 JavaScript FALTANTE

**Archivo nuevo**: `view/Presupuesto/versiones.js`

**Funciones necesarias:**

```javascript
// 1. Listar versiones de un presupuesto
function verVersiones(id_presupuesto) {
    // Cargar modal con DataTable de versiones
    // Llamada AJAX a controller.php?op=listar_versiones
}

// 2. Crear nueva versión
function crearNuevaVersion() {
    // Modal para ingresar motivo
    // Validar que se pueda crear
    // Llamada AJAX a controller.php?op=crear_version
    // Mostrar loading mientras duplica líneas
}

// 3. Cambiar estado de versión
function cambiarEstadoVersion(id_version, estado_actual) {
    // Validar transición permitida
    // Si es rechazo, pedir motivo
    // Confirmación con SweetAlert
    // Llamada AJAX a controller.php?op=cambiar_estado_version
}

// 4. Comparar versiones
function compararVersiones(id_version_1, id_version_2) {
    // Abrir modal de comparación
    // Llamada AJAX a controller.php?op=comparar_versiones
    // Renderizar tabla comparativa
}

// 5. Establecer versión actual
function establecerVersionActual(id_presupuesto, numero_version) {
    // Confirmación
    // Llamada AJAX a controller.php?op=establecer_version_actual
    // Recargar página o actualizar indicador
}

// 6. Ver PDF de versión
function verPdfVersion(ruta_pdf) {
    // Abrir en nueva ventana o descargar
}

// 7. Ver detalles de versión
function verDetallesVersion(id_version) {
    // Modal con información completa
    // Incluir líneas de esa versión
}
```

---

## 📋 6. FLUJO ACTUAL vs. FLUJO CON VERSIONES

### 6.1 Flujo ACTUAL (Sin Versiones Operativas)

```
Usuario crea presupuesto
  ↓
Sistema crea registro en presupuesto
  ↓
Trigger auto-crea versión 1 en presupuesto_version
  ↓
Usuario edita presupuesto directamente
  ↓
Sistema modifica la misma versión (sin control)
  ↓
NO HAY GESTIÓN DE VERSIONES
```

**PROBLEMA**: Aunque existe la versión 1 en la BD, todas las modificaciones se hacen sobre ella. No hay flujo para crear versión 2, 3, etc.

### 6.2 Flujo ESPERADO (Con Versiones Completas)

```
┌─────────────────────────────────────────────────────────────┐
│ FASE 1: CREACIÓN DE PRESUPUESTO                             │
└─────────────────────────────────────────────────────────────┘

Usuario: Crea nuevo presupuesto con datos básicos
  ↓
Sistema: 
  - Inserta en tabla `presupuesto`
  - Trigger auto-crea versión 1 (estado: borrador)
  - version_actual_presupuesto = 1
  - estado_general_presupuesto = 'borrador'

┌─────────────────────────────────────────────────────────────┐
│ FASE 2: TRABAJO EN BORRADOR                                 │
└─────────────────────────────────────────────────────────────┘

Usuario: 
  - Edita cabecera del presupuesto
  - Añade líneas de artículos/kits
  - Modifica cantidades, precios, descuentos
  ↓
Sistema: 
  - Modifica versión 1 libremente (estado = borrador)
  - Triggers NO bloquean edición
  - Calcula totales en tiempo real

┌─────────────────────────────────────────────────────────────┐
│ FASE 3: ENVÍO AL CLIENTE                                    │
└─────────────────────────────────────────────────────────────┘

Usuario: Click en botón "Enviar al Cliente"
  ↓
Sistema:
  - Cambia estado versión 1 a 'enviado'
  - fecha_envio_version = NOW()
  - Genera PDF: PPTO-2025-001_v1.pdf
  - ruta_pdf_version = '/documentos/presupuestos/PPTO-2025-001_v1.pdf'
  - Triggers BLOQUEAN edición de líneas
  - Usuario recibe confirmación y link al PDF

Cliente: Recibe PDF y revisa el presupuesto

┌─────────────────────────────────────────────────────────────┐
│ FASE 4A: CLIENTE APRUEBA (Caso exitoso)                     │
└─────────────────────────────────────────────────────────────┘

Cliente: Aprueba el presupuesto v1
  ↓
Usuario: Marca versión como "Aprobado"
  ↓
Sistema:
  - estado_version_presupuesto v1 = 'aprobado'
  - fecha_aprobacion_version = NOW()
  - estado_general_presupuesto = 'aprobado'
  - Triggers IMPIDEN crear más versiones
  - Presupuesto pasa a fase de producción/ejecución
  
✅ FIN DEL FLUJO EXITOSO

┌─────────────────────────────────────────────────────────────┐
│ FASE 4B: CLIENTE RECHAZA O PIDE CAMBIOS                     │
└─────────────────────────────────────────────────────────────┘

Cliente: 
  - Rechaza presupuesto
  - O solicita modificaciones (precio, cantidades, artículos)
  ↓
Usuario: Marca versión como "Rechazada" (opcional)
Sistema:
  - estado_version_presupuesto v1 = 'rechazado'
  - fecha_rechazo_version = NOW()
  - motivo_rechazo_version = "Cliente solicita reducción de precio"

┌─────────────────────────────────────────────────────────────┐
│ FASE 5: CREAR NUEVA VERSIÓN                                 │
└─────────────────────────────────────────────────────────────┘

Usuario: Click "Nueva Versión desde v1"
  ↓
Sistema:
  1. Valida que se puede crear (v1 está en 'enviado' o 'rechazado')
  2. Crea registro en presupuesto_version:
     - id_presupuesto = mismo
     - numero_version_presupuesto = 2
     - version_padre_presupuesto = id_version_1
     - estado_version_presupuesto = 'borrador'
     - motivo_modificacion_version = "Cliente solicitó reducción 10%"
  3. DUPLICA TODAS LAS LÍNEAS de v1 a v2:
     - Copia cada línea con id_version_presupuesto = id_version_2
     - Mantiene jerarquía padre/hijo
  4. Actualiza presupuesto:
     - version_actual_presupuesto = 2
     - estado_general_presupuesto = 'borrador'
  5. Usuario ve formulario con v2 cargada

Observación: v1 permanece INMUTABLE, toda la historia se conserva

┌─────────────────────────────────────────────────────────────┐
│ FASE 6: MODIFICAR NUEVA VERSIÓN                             │
└─────────────────────────────────────────────────────────────┘

Usuario: 
  - Modifica cantidades en líneas de v2
  - Aplica descuentos adicionales
  - Añade o elimina artículos
  - Cambia precios según negociación
  ↓
Sistema: 
  - Solo modifica líneas de v2
  - v1 permanece intacta (para comparación posterior)
  - Triggers permiten edición porque v2 está en 'borrador'

┌─────────────────────────────────────────────────────────────┐
│ FASE 7: ENVIAR NUEVA VERSIÓN                                │
└─────────────────────────────────────────────────────────────┘

Usuario: Click "Enviar v2 al Cliente"
  ↓
Sistema:
  - Cambia estado v2 a 'enviado'
  - fecha_envio_version = NOW()
  - Genera PDF: PPTO-2025-001_v2.pdf
  - Triggers BLOQUEAN edición de v2
  - Usuario puede comparar v1 vs v2 en cualquier momento

Cliente: Recibe nuevo PDF con cambios

┌─────────────────────────────────────────────────────────────┐
│ FASE 8: APROBACIÓN FINAL                                    │
└─────────────────────────────────────────────────────────────┘

Cliente: Aprueba versión 2
  ↓
Usuario: Marca v2 como "Aprobado"
  ↓
Sistema:
  - estado_version_presupuesto v2 = 'aprobado'
  - fecha_aprobacion_version = NOW()
  - estado_general_presupuesto = 'aprobado'
  - Triggers IMPIDEN crear más versiones
  - Histórico completo: v1 (rechazada) + v2 (aprobada)
  
✅ FIN DEL FLUJO CON APROBACIÓN EN V2

┌─────────────────────────────────────────────────────────────┐
│ CASO ESPECIAL: MÚLTIPLES VERSIONES                          │
└─────────────────────────────────────────────────────────────┘

Si el cliente rechaza v2, se puede crear v3 desde v2
Si rechaza v3, se puede crear v4 desde v3
...y así sucesivamente

Genealogía: v1 → v2 → v3 → v4
Cada versión mantiene referencia a su padre
Comparaciones posibles: v1 vs v4, v2 vs v3, etc.
```

### 6.3 Diagrama de Estados de Versión

```
┌──────────┐
│ BORRADOR │◄─── Estado inicial (versión recién creada)
└────┬─────┘
     │
     ├──────────► [ENVIADO] ───┬──────► [APROBADO] (INMUTABLE)
     │                          │
     │                          ├──────► [RECHAZADO] ───► [CANCELADO]
     │                          │
     │                          └──────► [CANCELADO]
     │
     └──────────► [CANCELADO] (INMUTABLE)

Leyenda:
- BORRADOR: Editable, se pueden modificar líneas
- ENVIADO: Bloqueado, no se pueden modificar líneas
- APROBADO: Inmutable, no se pueden crear más versiones
- RECHAZADO: Bloqueado, permite crear nueva versión
- CANCELADO: Inmutable
```

---

## 📊 7. MATRIZ DE PRIORIDADES DE IMPLEMENTACIÓN

| Componente | Prioridad | Complejidad | Tiempo Est. | Dependencias | Riesgo |
|------------|-----------|-------------|-------------|--------------|--------|
| **FASE 1: Backend Core** |
| Modelo: crear_nueva_version | 🔴 CRÍTICA | Alta | 4-6 horas | Ninguna | Alto |
| Modelo: duplicar_lineas_version | 🔴 CRÍTICA | Alta | 3-4 horas | crear_nueva_version | Alto |
| Modelo: get_versiones_presupuesto | 🔴 CRÍTICA | Media | 2 horas | Ninguna | Bajo |
| Controller: crear_version | 🔴 CRÍTICA | Media | 2 horas | Modelos | Medio |
| Controller: listar_versiones | 🔴 CRÍTICA | Baja | 1 hora | Modelo get_versiones | Bajo |
| **FASE 2: Gestión de Estados** |
| Modelo: cambiar_estado_version | 🟡 ALTA | Media | 2 horas | Ninguna | Medio |
| Modelo: obtener_version_actual | 🟡 ALTA | Media | 1-2 horas | Ninguna | Bajo |
| Modelo: establecer_version_actual | 🟡 ALTA | Media | 2 horas | Ninguna | Medio |
| Controller: cambiar_estado_version | 🟡 ALTA | Baja | 1 hora | Modelo | Bajo |
| Controller: establecer_version_actual | 🟡 ALTA | Baja | 1 hora | Modelo | Bajo |
| **FASE 3: Frontend Básico** |
| Vista: modalVersiones.php | 🟡 ALTA | Media | 4 horas | Controladores | Medio |
| JavaScript: versiones.js | 🟡 ALTA | Media | 3 horas | Controladores | Medio |
| Modificar: mntpresupuesto.php | 🟡 ALTA | Baja | 2 horas | modalVersiones | Bajo |
| **FASE 4: Comparación** |
| Modelo: comparar_versiones | 🟢 MEDIA | Alta | 4 horas | get_versiones | Medio |
| Controller: comparar_versiones | 🟢 MEDIA | Media | 2 horas | Modelo comparar | Bajo |
| Vista: modalComparar.php | 🟢 MEDIA | Media | 3 horas | Controller | Bajo |
| JS: Renderizado comparación | 🟢 MEDIA | Media | 2 horas | modalComparar | Bajo |
| **FASE 5: Mejoras UI** |
| Modificar: formularioPresupuesto.php | 🟢 MEDIA | Media | 3 horas | Selectores versión | Bajo |
| Selector de versión en header | 🟢 MEDIA | Baja | 1 hora | Backend completo | Bajo |
| Bloqueo edición según estado | 🟢 MEDIA | Media | 2 horas | Backend completo | Medio |
| Indicadores visuales | 🟢 MEDIA | Baja | 1 hora | Backend completo | Bajo |
| **FASE 6: PDFs y Extras** |
| Generación PDFs versionados | 🔵 BAJA | Alta | 6 horas | Sistema PDF existente | Alto |
| Visualizador PDFs históricos | 🔵 BAJA | Media | 2 horas | PDFs generados | Bajo |
| Dashboard métricas versiones | 🔵 BAJA | Media | 4 horas | Todos anteriores | Bajo |
| Modelo: get_historial_versiones | 🔵 BAJA | Baja | 1 hora | Ninguna | Bajo |

**TOTAL ESTIMADO**: 58-66 horas

**Distribución por fase:**
- Fase 1 (Backend Core): 12-15 horas - **CRÍTICO**
- Fase 2 (Estados): 7-9 horas - **IMPORTANTE**
- Fase 3 (Frontend Básico): 9 horas - **IMPORTANTE**
- Fase 4 (Comparación): 11 horas - **OPCIONAL**
- Fase 5 (Mejoras UI): 7 horas - **OPCIONAL**
- Fase 6 (PDFs/Extras): 13 horas - **OPCIONAL**

---

## 🚀 8. PLAN DE IMPLEMENTACIÓN DETALLADO

### FASE 1: Backend Core (CRÍTICO) ⏱️ 12-15 horas

**Objetivo**: Permitir crear versiones y gestionarlas desde código (sin UI)

#### Día 1 (6-8 horas)

**1.1 Implementar `crear_nueva_version()` en Presupuesto.php**
```php
Ubicación: models/Presupuesto.php

Funcionalidad:
- Validar que se pueda crear nueva versión (trigger lo valida, pero añadir validación PHP)
- Obtener id_version_presupuesto de la versión actual (padre)
- Usar transacción PDO:
  - BEGIN TRANSACTION
  - INSERT en presupuesto_version con datos completos
  - Obtener id_version_presupuesto nuevo
  - Llamar a duplicar_lineas_version()
  - UPDATE presupuesto SET version_actual = nuevo_numero
  - COMMIT (o ROLLBACK si falla)
- Logging de operación
- Retornar id_version_presupuesto nuevo

Tiempo: 3-4 horas
Complejidad: Alta (manejo de transacciones)
```

**1.2 Implementar `duplicar_lineas_version()` en Presupuesto.php**
```php
Ubicación: models/Presupuesto.php

Funcionalidad:
- Obtener TODAS las líneas de id_version_origen
- Para cada línea:
  - INSERT nueva línea con id_version = destino
  - Copiar todos los campos excepto id_linea_ppto
  - Mantener orden y jerarquía
- Usar prepared statements
- Manejo de errores (devolver false si falla)
- Logging de cantidad de líneas duplicadas

Tiempo: 3-4 horas
Complejidad: Alta (preservar jerarquía)
```

#### Día 2 (6-7 horas)

**1.3 Implementar `get_versiones_presupuesto()` en Presupuesto.php**
```php
Ubicación: models/Presupuesto.php

Funcionalidad:
- SELECT de todas las versiones de un id_presupuesto
- JOIN con usuario para nombres (cuando exista tabla)
- Incluir:
  - Datos básicos de versión
  - Estado actual
  - Fechas relevantes
  - Indicador de versión activa
  - Número de líneas asociadas
- ORDER BY numero_version DESC
- Retornar array completo

Tiempo: 2 horas
Complejidad: Media
```

**1.4 Crear operación `crear_version` en presupuesto.php**
```php
Ubicación: controller/presupuesto.php

Funcionalidad:
- Recibir: id_presupuesto, motivo
- Sanitizar inputs
- Obtener id_usuario de sesión (temporal: usar 1)
- Llamar a $presupuesto->crear_nueva_version()
- Respuesta JSON:
  - success: true/false
  - message: descripción
  - id_version: nuevo id
  - numero_version: número lógico
- Logging de actividad

Tiempo: 2 horas
Complejidad: Media
```

**1.5 Crear operación `listar_versiones` en presupuesto.php**
```php
Ubicación: controller/presupuesto.php

Funcionalidad:
- Recibir: id_presupuesto
- Llamar a $presupuesto->get_versiones_presupuesto()
- Formatear para DataTables:
  - draw, recordsTotal, recordsFiltered, data
  - Añadir columna "opciones" con botones HTML
  - Badge para estado
  - Icono estrella para versión actual
- Header JSON
- Logging

Tiempo: 1 hora
Complejidad: Baja
```

**1.6 Pruebas con Postman/Curl**
```bash
Tiempo: 1 hora

Tests:
1. Crear presupuesto nuevo → verificar versión 1
2. Añadir líneas a versión 1
3. Crear versión 2 desde v1 (via Postman)
4. Verificar duplicación de líneas
5. Intentar crear v3 con v1 en borrador (debe fallar)
6. Listar versiones y verificar JSON
```

**Entregable Fase 1**: 
- ✅ Sistema funcional para crear versiones desde backend
- ✅ Duplicación automática de líneas
- ✅ Listado de versiones disponible
- ✅ Pruebas básicas exitosas

---

### FASE 2: Gestión de Estados (IMPORTANTE) ⏱️ 7-9 horas

**Objetivo**: Workflow completo de estados de versiones

#### Día 3 (4-5 horas)

**2.1 Implementar `cambiar_estado_version()` en Presupuesto.php**
```php
Ubicación: models/Presupuesto.php

Funcionalidad:
- Recibir: id_version, nuevo_estado, motivo_rechazo (opcional)
- Obtener estado actual
- Validar transición permitida (aunque trigger lo valida)
- UPDATE estado_version_presupuesto
- Si es rechazo, UPDATE motivo_rechazo_version
- Trigger automático de fechas
- Sincronización automática con cabecera (trigger)
- Logging detallado
- Retornar true/false

Tiempo: 2 horas
Complejidad: Media
```

**2.2 Implementar `obtener_version_actual()` en Presupuesto.php**
```php
Ubicación: models/Presupuesto.php

Funcionalidad:
- Recibir: id_presupuesto
- Obtener version_actual_presupuesto
- SELECT de presupuesto_version con ese número
- Incluir:
  - Datos completos de versión
  - Número de líneas (COUNT)
  - Totales (SUM de líneas)
  - Estado actual
- Retornar array asociativo

Tiempo: 1-2 horas
Complejidad: Media
```

**2.3 Implementar `establecer_version_actual()` en Presupuesto.php**
```php
Ubicación: models/Presupuesto.php

Funcionalidad:
- Recibir: id_presupuesto, numero_version
- Validar que la versión existe
- Validar que no está en estado 'borrador' (opcional según reglas de negocio)
- UPDATE presupuesto SET version_actual = numero_version
- Sincronizar estado_general con estado de nueva versión actual
- Logging de cambio
- Retornar true/false

Tiempo: 1-2 horas
Complejidad: Media
```

#### Día 3-4 (3-4 horas)

**2.4 Crear operación `cambiar_estado_version` en controller**
```php
Ubicación: controller/presupuesto.php

Funcionalidad:
- Recibir: id_version, nuevo_estado, motivo_rechazo (opcional)
- Sanitizar inputs
- Validar que nuevo_estado es válido (ENUM)
- Llamar a $presupuesto->cambiar_estado_version()
- Respuesta JSON con success/message
- Logging

Tiempo: 1 hora
Complejidad: Baja
```

**2.5 Crear operación `establecer_version_actual` en controller**
```php
Ubicación: controller/presupuesto.php

Funcionalidad:
- Recibir: id_presupuesto, numero_version
- Validar inputs
- Llamar a $presupuesto->establecer_version_actual()
- Respuesta JSON
- Logging

Tiempo: 1 hora
Complejidad: Baja
```

**2.6 Crear operación `obtener_version_actual` en controller**
```php
Ubicación: controller/presupuesto.php

Funcionalidad:
- Recibir: id_presupuesto
- Llamar a $presupuesto->obtener_version_actual()
- Respuesta JSON con datos completos
- Header JSON
- Logging

Tiempo: 30 min
Complejidad: Baja
```

**2.7 Pruebas de workflow completo**
```bash
Tiempo: 1-1.5 horas

Tests:
1. Crear presupuesto con versión 1 (borrador)
2. Añadir líneas
3. Cambiar v1 a 'enviado' → verificar fecha_envio
4. Intentar editar líneas (debe fallar por trigger)
5. Crear versión 2 desde v1 enviada
6. Cambiar v2 a 'enviado'
7. Cambiar v2 a 'aprobado' → verificar fecha_aprobacion
8. Intentar crear v3 (debe fallar porque v2 aprobada)
9. Verificar estado_general_presupuesto en cada paso
10. Establecer v1 como actual y verificar cambio
```

**Entregable Fase 2**: 
- ✅ Workflow completo de estados funcional
- ✅ Validaciones robustas
- ✅ Sincronización automática con cabecera
- ✅ Tests exhaustivos pasados

---

### FASE 3: Frontend Básico (IMPORTANTE) ⏱️ 9 horas

**Objetivo**: UI mínima para que usuarios finales puedan usar versiones

#### Día 5 (5 horas)

**3.1 Crear `view/Presupuesto/modalVersiones.php`**
```html
Tiempo: 3 horas
Complejidad: Media

Estructura:
- Modal Bootstrap 5 con modal-xl
- Header con:
  - Título dinámico "Versiones del Presupuesto: PPTO-2025-001"
  - Botón "Nueva Versión" (solo si es posible)
- Body con:
  - DataTable #tblVersiones
  - Columnas: Versión, Estado, Fecha Creación, Motivo, Usuario, PDF, Actual, Acciones
  - Renderizado personalizado:
    - Badge para estado con colores
    - Icono estrella para versión actual
    - Link a PDF si existe
    - Botones de acción por fila
- Footer con botón cerrar
- Integración con DataTables
- Diseño responsive

Acciones por fila:
- Ver detalles (modal info)
- Ver PDF (nueva ventana)
- Comparar con... (abrir modal comparar)
- Cambiar estado (según estado actual)
- Establecer como actual (si no lo es)
```

**3.2 Crear `view/Presupuesto/versiones.js`**
```javascript
Tiempo: 2 horas
Complejidad: Media

Funciones a implementar:

1. verVersiones(id_presupuesto)
   - Cargar modal
   - Inicializar DataTable con AJAX
   - Endpoint: controller/presupuesto.php?op=listar_versiones
   - Configurar columnas con renderizado personalizado

2. crearNuevaVersion(id_presupuesto)
   - SweetAlert para ingresar motivo
   - Validar motivo no vacío
   - Loading spinner
   - AJAX POST a controller.php?op=crear_version
   - Recargar DataTable al éxito
   - Mensaje de confirmación

3. cambiarEstadoVersion(id_version, estado_actual)
   - Determinar estados permitidos según estado_actual
   - Si es rechazo, pedir motivo con SweetAlert
   - Confirmación de acción
   - AJAX POST a controller.php?op=cambiar_estado_version
   - Actualizar fila en DataTable

4. establecerVersionActual(id_presupuesto, numero_version)
   - Confirmación con SweetAlert
   - AJAX POST a controller.php?op=establecer_version_actual
   - Recargar DataTable
   - Actualizar indicador en página principal

5. verDetallesVersion(id_version)
   - AJAX GET info de versión
   - Modal con información detallada
   - Incluir número de líneas, totales, fechas

Configuración DataTables:
- AJAX source
- Columnas personalizadas
- Idioma español
- Orden por número versión DESC
- Botones de acción dinámicos según estado
```

#### Día 6 (4 horas)

**3.3 Modificar `view/Presupuesto/mntpresupuesto.php`**
```html
Tiempo: 2 horas
Complejidad: Baja

Cambios:
1. Añadir columna "Versiones" en DataTable:
   columns: [
     // ... columnas existentes ...
     {
       data: null,
       orderable: false,
       render: function(data, type, row) {
         let badge = '<span class="badge bg-info">v' + row.version_actual_presupuesto + '</span>';
         let btn = '<button class="btn btn-sm btn-outline-secondary ms-2" onclick="verVersiones(' + row.id_presupuesto + ')">' +
                   '<i class="fa fa-history"></i> Ver Versiones</button>';
         return badge + btn;
       }
     }
   ]

2. Incluir script versiones.js:
   <script src="versiones.js"></script>

3. Incluir modal modalVersiones.php:
   <?php include 'modalVersiones.php'; ?>
```

**3.4 Pruebas de integración frontend**
```bash
Tiempo: 2 horas

Tests en navegador:
1. Abrir listado de presupuestos
2. Verificar columna "Versiones" con badge v1
3. Click "Ver Versiones" → debe abrir modal
4. Verificar listado de versiones en DataTable
5. Click "Nueva Versión" → ingreso de motivo
6. Verificar creación de v2 y recarga de tabla
7. Verificar botones de acción según estado
8. Cambiar estado v2 a "enviado"
9. Verificar que botón "Nueva Versión" se deshabilita (o habilita según reglas)
10. Establecer v1 como actual y verificar actualización
11. Verificar responsiveness en móvil/tablet
12. Verificar mensajes de error si hay problemas
```

**Entregable Fase 3**: 
- ✅ UI funcional para gestión de versiones
- ✅ Usuario puede ver lista de versiones
- ✅ Usuario puede crear nuevas versiones con motivo
- ✅ Usuario puede cambiar estados
- ✅ Usuario puede establecer versión actual
- ✅ Integración completa con backend
- ✅ Tests de usabilidad pasados

---

### FASE 4: Comparación y Reportes (OPCIONAL) ⏱️ 11 horas

**Objetivo**: Análisis detallado de cambios entre versiones

#### Día 7 (6 horas)

**4.1 Implementar `comparar_versiones()` en Presupuesto.php**
```php
Ubicación: models/Presupuesto.php

Funcionalidad:
- Recibir: id_version_1, id_version_2
- Obtener todas las líneas de v1
- Obtener todas las líneas de v2
- Comparar por codigo_articulo o id_articulo:
  
  a) Líneas AÑADIDAS (existen en v2, no en v1):
     - Array de líneas nuevas
     - Marcadas con flag 'tipo_cambio' => 'añadido'
  
  b) Líneas ELIMINADAS (existen en v1, no en v2):
     - Array de líneas eliminadas
     - Marcadas con flag 'tipo_cambio' => 'eliminado'
  
  c) Líneas MODIFICADAS (existen en ambas pero con diferencias):
     - Comparar: cantidad, precio, descuento, total
     - Calcular diferencias absolutas y porcentuales
     - Marcadas con flag 'tipo_cambio' => 'modificado'
     - Incluir valores antiguos y nuevos
  
  d) Líneas IGUALES (sin cambios):
     - Opcional: incluir o no según parámetro
     - Marcadas con flag 'tipo_cambio' => 'igual'

- Calcular resumen de diferencias:
  - Total v1 vs Total v2
  - Diferencia absoluta
  - Diferencia porcentual
  - Número de líneas añadidas/eliminadas/modificadas

- Retornar array estructurado:
  {
    "resumen": {
      "total_v1": 1500.00,
      "total_v2": 1350.00,
      "diferencia": -150.00,
      "diferencia_porcentual": -10.00,
      "lineas_anadidas": 2,
      "lineas_eliminadas": 1,
      "lineas_modificadas": 3,
      "lineas_iguales": 10
    },
    "lineas_anadidas": [ ... ],
    "lineas_eliminadas": [ ... ],
    "lineas_modificadas": [ ... ],
    "lineas_iguales": [ ... ] // opcional
  }

Tiempo: 4 horas
Complejidad: Alta (lógica de comparación)
```

**4.2 Crear operación `comparar_versiones` en controller**
```php
Ubicación: controller/presupuesto.php

Funcionalidad:
- Recibir: id_version_1, id_version_2
- Validar que ambas versiones existen y pertenecen al mismo presupuesto
- Llamar a $presupuesto->comparar_versiones()
- Respuesta JSON con estructura completa
- Header JSON
- Logging de comparación

Tiempo: 1 hora
Complejidad: Baja
```

**4.3 Crear `view/Presupuesto/modalComparar.php`**
```html
Tiempo: 1 hora
Complejidad: Media

Estructura:
- Modal fullscreen para máximo espacio
- Header con selectores de versiones
- Body dividido en secciones:
  
  1. RESUMEN DE DIFERENCIAS (arriba):
     - Cards con métricas:
       - Total v1 vs Total v2
       - Diferencia (€ y %)
       - Líneas añadidas (verde)
       - Líneas eliminadas (rojo)
       - Líneas modificadas (amarillo)
  
  2. TABLA COMPARATIVA (principal):
     - Columnas:
       - Estado (icono)
       - Artículo/Descripción
       - Cantidad v1 / v2
       - Precio v1 / v2
       - Descuento v1 / v2
       - Total v1 / v2
       - Diferencia Total
     - Colores por tipo:
       - Verde claro: Líneas añadidas
       - Rojo claro: Líneas eliminadas
       - Amarillo claro: Líneas modificadas
       - Blanco: Líneas iguales
     - Números con diferencias resaltadas en negrita

- Footer con:
  - Botón "Exportar a PDF" (futuro)
  - Botón "Cerrar"

- Diseño responsive con scroll horizontal
```

#### Día 8 (5 horas)

**4.4 JavaScript para renderizado de comparación**
```javascript
Ubicación: view/Presupuesto/versiones.js (añadir funciones)

Funciones:

1. compararVersiones(id_version_1, id_version_2)
   - Si no se pasan parámetros, mostrar modal selector
   - AJAX GET a controller.php?op=comparar_versiones
   - Al recibir datos, llamar a renderizarComparacion()

2. mostrarModalComparar(id_presupuesto)
   - Cargar listado de versiones en selectores
   - Deshabilitar comparar mismo con mismo
   - Botón "Comparar" activa compararVersiones()

3. renderizarComparacion(datos)
   - Limpiar modal
   - Renderizar resumen con cards Bootstrap
   - Crear tabla HTML con todas las líneas
   - Aplicar clases de color según tipo_cambio
   - Resaltar diferencias numéricas
   - Formatear monedas y porcentajes
   - Añadir iconos según estado
   - Tooltips explicativos

4. exportarComparacionPDF()
   - Preparar datos para backend
   - AJAX POST para generar PDF
   - Descargar archivo generado
   - (Implementación futura)

Tiempo: 3 horas
Complejidad: Media
```

**4.5 Integración y pruebas de comparación**
```bash
Tiempo: 2 horas

Tests:
1. Crear presupuesto con 10 líneas
2. Enviar v1
3. Crear v2 desde v1
4. Modificar:
   - Añadir 2 líneas nuevas
   - Eliminar 1 línea existente
   - Modificar cantidad de 3 líneas
   - Modificar precio de 2 líneas
5. Comparar v1 vs v2
6. Verificar resumen de diferencias
7. Verificar tabla con colores correctos
8. Verificar cálculos de diferencias
9. Verificar que líneas iguales no se resaltan
10. Probar comparar v1 vs v1 (debe mostrar todo igual)
11. Verificar responsiveness de la tabla
```

**Entregable Fase 4**: 
- ✅ Sistema de comparación funcional
- ✅ Visualización clara de diferencias
- ✅ Resumen ejecutivo de cambios
- ✅ Tabla comparativa detallada
- ✅ Código reutilizable para futuras mejoras

---

### FASE 5: Mejoras UI y Experiencia de Usuario (OPCIONAL) ⏱️ 7 horas

**Objetivo**: Pulir interfaz y añadir características de usabilidad

#### Día 9 (4 horas)

**5.1 Modificar formulario de presupuesto**
```html
Ubicación: view/Presupuesto/formularioPresupuesto.php

Cambios:

1. HEADER CON SELECTOR DE VERSIÓN:
   <div class="row mb-3">
     <div class="col-md-6">
       <label>Versión Actual:</label>
       <select id="selectorVersion" class="form-control">
         <!-- Cargado dinámicamente con versiones -->
       </select>
     </div>
     <div class="col-md-6">
       <span class="badge bg-success" id="estadoVersion">BORRADOR</span>
       <button class="btn btn-primary" id="btnNuevaVersion">
         <i class="fa fa-plus"></i> Nueva Versión
       </button>
     </div>
   </div>

2. BLOQUEO DE EDICIÓN SEGÚN ESTADO:
   - Verificar estado de versión al cargar
   - Si estado != 'borrador':
     - Deshabilitar inputs de líneas
     - Deshabilitar botones Añadir/Eliminar líneas
     - Mostrar alert informativo
     - Habilitar solo botón "Cambiar Estado"

3. INDICADOR VISUAL DE VERSIÓN:
   - Badge grande en esquina superior derecha
   - Formato: "v2 de 3 versiones"
   - Color según estado:
     - Verde: borrador
     - Azul: enviado
     - Verde oscuro: aprobado
     - Rojo: rechazado
     - Gris: cancelado

4. ALERT SI ES VERSIÓN HISTÓRICA:
   <div class="alert alert-warning" id="alertVersionHistorica" style="display:none;">
     <i class="fa fa-info-circle"></i> Estás visualizando la versión 1 (histórica).
     La versión actual es la v2.
     <button class="btn btn-sm btn-primary" onclick="cargarVersionActual()">
       Cargar Versión Actual
     </button>
   </div>

Tiempo: 3 horas
Complejidad: Media
```

**5.2 JavaScript para gestión de formulario versionado**
```javascript
Ubicación: view/Presupuesto/formularioPresupuesto.js (modificar existente)

Funciones nuevas:

1. cargarVersiones(id_presupuesto)
   - AJAX GET versiones disponibles
   - Popular selector de versión
   - Marcar versión actual

2. cambiarVersion(id_version)
   - Confirmación si hay cambios sin guardar
   - Cargar datos de la versión seleccionada
   - Actualizar líneas del presupuesto
   - Actualizar badge de estado
   - Verificar si debe bloquear edición

3. bloquearEdicionSegunEstado(estado)
   - Si estado != 'borrador':
     - $('.input-cantidad').prop('disabled', true)
     - $('.input-precio').prop('disabled', true)
     - $('.btn-anadir-linea').prop('disabled', true)
     - $('.btn-eliminar-linea').prop('disabled', true)
   - Mostrar tooltip explicativo

4. verificarVersionHistorica()
   - Comparar versión cargada vs versión actual
   - Si no coinciden, mostrar alert
   - Habilitar botón para cargar versión actual

5. cargarVersionActual()
   - Obtener id_version de versión actual
   - Cargar esa versión en formulario
   - Ocultar alert

Tiempo: 1 hora
Complejidad: Baja
```

#### Día 9-10 (3 horas)

**5.3 Indicadores visuales adicionales**
```html
Tiempo: 1 hora

Mejoras:
1. Timeline de versiones en sidebar
   - Mostrar genealogía: v1 → v2 → v3
   - Con fechas e iconos

2. Badge de "Nueva versión disponible"
   - Si usuario está en v2 y existe v3
   - Notificación visual

3. Tooltips informativos
   - Sobre cada badge de estado
   - Sobre botones deshabilitados
   - Con explicación de por qué está bloqueado

4. Animaciones suaves
   - Al cambiar de versión
   - Al bloquear/desbloquear edición
   - Al mostrar/ocultar alerts
```

**5.4 Mejoras de usabilidad**
```javascript
Tiempo: 1 hora

Características:
1. Keyboard shortcuts
   - Ctrl+Shift+V: Ver versiones
   - Ctrl+Shift+N: Nueva versión (si es posible)
   - Esc: Cerrar modales

2. Confirmaciones inteligentes
   - Antes de cambiar versión con cambios sin guardar
   - Antes de cambiar estado que bloquea edición

3. Guardado automático en borrador
   - Auto-save cada 30 segundos si hay cambios
   - Indicador de "Guardando..."

4. Historial de acciones
   - Mini-log en footer
   - "Versión 2 creada hace 5 minutos"
   - "Cambio a estado Enviado hace 1 hora"
```

**5.5 Pruebas de usabilidad**
```bash
Tiempo: 1 hora

Tests con usuarios:
1. Usuario crea presupuesto (v1)
2. Añade líneas
3. Guarda y envía
4. Intenta editar → verificar bloqueo claro
5. Crea v2 desde modal versiones
6. Verifica que carga v2 automáticamente
7. Añade más líneas
8. Cambia manualmente a v1 en selector
9. Verifica alert de versión histórica
10. Click "Cargar Versión Actual" → vuelve a v2
11. Compara v1 vs v2
12. Envía v2
13. Verifica que todo está bloqueado
14. Verificar navegación con teclado
```

**Entregable Fase 5**: 
- ✅ Interfaz pulida y profesional
- ✅ Indicadores claros de versión y estado
- ✅ Bloqueo automático según estado
- ✅ Navegación fluida entre versiones
- ✅ Experiencia de usuario optimizada
- ✅ Feedback visual constante

---

### FASE 6: PDFs, Extras y Producción (OPCIONAL) ⏱️ 13 horas

**Objetivo**: Características avanzadas y preparación para producción

#### Día 11 (6 horas)

**6.1 Generación de PDFs versionados**
```php
Ubicación: controller/presupuesto.php o nuevo controller/pdf.php

Funcionalidad:
- Operación: "generar_pdf_version"
- Recibir: id_version_presupuesto
- Cargar datos completos de la versión:
  - Cabecera presupuesto
  - Datos versión (número, estado, fechas)
  - Todas las líneas de esa versión
  - Totales calculados
- Usar librería PDF existente (TCPDF, FPDF, DomPDF)
- Generar PDF con:
  - Encabezado: "Presupuesto PPTO-2025-001 - Versión 2"
  - Información de versión
  - Tabla de líneas
  - Totales
  - Pie de página con fecha generación
- Guardar en ruta: /public/documentos/presupuestos/PPTO-2025-001_v2.pdf
- UPDATE presupuesto_version SET ruta_pdf_version
- Retornar URL del PDF

Tiempo: 4 horas
Complejidad: Alta (depende de sistema PDF existente)
```

**6.2 Visualizador de PDFs históricos**
```html
Ubicación: view/Presupuesto/pdfViewer.php (nuevo)

Funcionalidad:
- Modal o página para visualizar PDFs
- Integración con PDF.js o iframe
- Navegación entre versiones
- Botón descargar
- Botón imprimir
- Comparación visual lado a lado (opcional)

Tiempo: 2 horas
Complejidad: Media
```

#### Día 12 (4 horas)

**6.3 Dashboard de métricas de versiones**
```php
Ubicación: view/Dashboard/metricas_versiones.php (nuevo)

Métricas a mostrar:
1. Promedio de versiones por presupuesto
2. Tasa de aprobación en primera versión
3. Tiempo promedio entre versiones
4. Versión con mayor diferencia de precio
5. Presupuestos con más versiones
6. Gráfico de distribución de estados
7. Timeline de actividad de versiones
8. Top 10 motivos de rechazo

Visualización:
- Charts.js para gráficos
- Tablas interactivas
- Filtros por fecha, estado, cliente
- Exportar a Excel

Tiempo: 3 horas
Complejidad: Media
```

**6.4 Modelo: get_historial_versiones()**
```php
Ubicación: models/Presupuesto.php

Funcionalidad:
- Obtener todas las versiones con detalle completo
- Incluir:
  - Cambios de estado con fechas
  - Usuarios que realizaron acciones
  - Motivos de modificación
  - Totales de cada versión
- Ordenado cronológicamente
- Formatear para timeline visual

Tiempo: 1 hora
Complejidad: Baja
```

#### Día 13 (3 horas)

**6.5 Testing exhaustivo y bugfixing**
```bash
Tiempo: 2 horas

Tests de regresión:
1. Crear múltiples presupuestos
2. Crear múltiples versiones (hasta v5)
3. Cambiar estados en diversos órdenes
4. Comparar versiones no consecutivas
5. Intentar acciones prohibidas (verificar errores claros)
6. Verificar integridad de triggers
7. Cargar testing: crear 100 versiones
8. Verificar performance de listados
9. Comprobar memoria en duplicación de líneas grandes
10. Validar PDFs generados
```

**6.6 Documentación de usuario final**
```markdown
Tiempo: 1 hora

Crear documento: "Manual de Usuario - Sistema de Versiones"

Secciones:
1. Introducción: ¿Qué son las versiones?
2. Crear un presupuesto nuevo
3. Trabajar en modo borrador
4. Enviar presupuesto al cliente
5. Gestionar respuestas del cliente
6. Crear nueva versión
7. Comparar versiones
8. Aprobar un presupuesto
9. Ver historial de versiones
10. Generar PDFs
11. Preguntas frecuentes
12. Troubleshooting

Formato: PDF ilustrado con capturas de pantalla
```

**Entregable Fase 6**: 
- ✅ Sistema completo de generación de PDFs
- ✅ Dashboard de análisis y métricas
- ✅ Documentación completa
- ✅ Testing exhaustivo aprobado
- ✅ Sistema listo para producción

---

## ⚠️ 9. PROBLEMAS Y RIESGOS IDENTIFICADOS

### 9.1 Riesgos de Integridad de Datos

**1. 🔴 CRÍTICO: Modificación Directa Sin Validación PHP**

**Problema**: 
Actualmente se puede modificar directamente la versión 1 incluso después de "enviarla" desde el frontend, porque **NO hay validación en PHP**. Los triggers protegen a nivel SQL, pero si alguien modifica desde el código PHP sin respetar el flujo, hay riesgo.

**Solución**:
- Añadir validación en modelos PHP antes de UPDATE/DELETE
- Verificar estado de versión antes de permitir modificaciones
- Usar transacciones para operaciones críticas
- Logging exhaustivo de intentos de modificación

**Código de ejemplo**:
```php
public function update_linea($id_linea, $datos) {
    // VALIDAR ESTADO DE VERSIÓN PRIMERO
    $sql = "SELECT estado_version_presupuesto 
            FROM presupuesto_version pv
            INNER JOIN linea_presupuesto lp ON pv.id_version_presupuesto = lp.id_version_presupuesto
            WHERE lp.id_linea_ppto = ?";
    
    $stmt = $this->conexion->prepare($sql);
    $stmt->execute([$id_linea]);
    $estado = $stmt->fetchColumn();
    
    if ($estado !== 'borrador') {
        throw new Exception("No se pueden modificar líneas de versiones no-borrador");
    }
    
    // Continuar con UPDATE...
}
```

**2. 🔴 CRÍTICO: Duplicación de Líneas Sin Transacción**

**Problema**: 
Si falla la duplicación de líneas a mitad de proceso, se puede crear una versión inconsistente.

**Solución**:
```php
public function duplicar_lineas_version($id_version_origen, $id_version_destino) {
    try {
        $this->conexion->beginTransaction();
        
        // Obtener líneas
        $lineas = $this->get_lineas_version($id_version_origen);
        
        // Duplicar cada una
        foreach ($lineas as $linea) {
            // INSERT con nuevo id_version
        }
        
        $this->conexion->commit();
        return true;
        
    } catch (Exception $e) {
        $this->conexion->rollBack();
        $this->registro->registrarActividad(..., 'error');
        return false;
    }
}
```

**3. 🟡 MEDIO: Campo version_actual No Sincronizado**

**Problema**: 
El campo `version_actual_presupuesto` se maneja manualmente. Si se olvida actualizarlo, hay inconsistencia.

**Solución**:
- Crear método dedicado `actualizar_version_actual()`
- Llamarlo siempre desde `crear_nueva_version()`
- Añadir trigger de validación (opcional)
- Verificación en tests automáticos

**4. 🟢 BAJO: PDFs con Nombres Duplicados**

**Problema**: 
Si se regenera PDF de una versión, puede sobrescribir el anterior.

**Solución**:
- Añadir timestamp al nombre: `PPTO-2025-001_v2_20260130_143522.pdf`
- O versionar PDFs: `PPTO-2025-001_v2_gen1.pdf`
- Mantener historial de PDFs generados

### 9.2 Inconsistencias de Nomenclatura

**1. Sufijos de Tabla Inconsistentes**

**Problema**:
La tabla se llama `presupuesto_version` pero los sufijos son `_version` sin el nombre de la tabla completo. Esto rompe la convención de nombrado del proyecto (`_presupuesto_version`).

**Estado**: Deuda técnica heredada de la documentación original.

**Decisión**: 
- **Mantener nomenclatura actual** para evitar refactorización masiva
- Documentar claramente la excepción
- En futuras tablas, seguir convención estricta

**2. Usuario por Defecto Hardcoded**

**Problema**:
Los campos `creado_por_version` y `enviado_por_version` usan `1` por defecto, pero no hay FK definida (pendiente de tabla `usuario`).

**Solución Temporal**:
- Usar valor 1 como usuario "Sistema"
- Documentar que se actualizará cuando exista tabla usuario

**Solución Definitiva**:
```sql
-- Cuando exista tabla usuario:
ALTER TABLE presupuesto_version
ADD CONSTRAINT fk_version_creado_por
    FOREIGN KEY (creado_por_version)
    REFERENCES usuario(id_usuario)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;

ALTER TABLE presupuesto_version
ADD CONSTRAINT fk_version_enviado_por
    FOREIGN KEY (enviado_por_version)
    REFERENCES usuario(id_usuario)
    ON DELETE RESTRICT
    ON UPDATE CASCADE;
```

### 9.3 Limitaciones Actuales de UI

**1. Sin Interfaz para Versiones**

**Estado**: No hay UI para:
- Ver lista de versiones de un presupuesto ❌
- Comparar dos versiones ❌
- Crear nueva versión ❌
- Cambiar estado de versión ❌

**Impacto**: Sistema completamente inoperativo para usuarios finales.

**Prioridad**: 🔴 CRÍTICA - Fase 3

**2. Sin API Endpoints**

**Estado**: No hay endpoints en controladores para operaciones de versiones.

**Impacto**: Imposible interactuar con versiones desde frontend.

**Prioridad**: 🔴 CRÍTICA - Fase 1-2

**3. Sin Lógica en Modelos**

**Estado**: Los modelos no tienen métodos para gestionar versiones.

**Impacto**: Base para todo lo demás.

**Prioridad**: 🔴 CRÍTICA - Fase 1

### 9.4 Riesgos de Performance

**1. Duplicación de Líneas en Presupuestos Grandes**

**Escenario**: Presupuesto con 500 líneas

**Problema**: 
- Duplicar 500 líneas puede tardar varios segundos
- Posible timeout en servidor
- Uso intensivo de memoria

**Solución**:
```php
// Usar INSERT múltiple en vez de bucle:
INSERT INTO linea_presupuesto (...)
SELECT ..., {nuevo_id_version} as id_version_presupuesto
FROM linea_presupuesto
WHERE id_version_presupuesto = {id_version_origen}

// Esto es mucho más rápido que:
foreach ($lineas as $linea) {
    INSERT INTO linea_presupuesto...
}
```

**2. Listado de Versiones con Muchos Registros**

**Escenario**: Presupuesto con 50 versiones (caso extremo)

**Problema**: 
- Query lenta si no hay índices adecuados
- Renderizado pesado en DataTables

**Solución**:
- Paginación server-side en DataTables
- Límite de versiones mostradas por defecto (últimas 10)
- Índices compuestos:
```sql
INDEX idx_presupuesto_numero_activo (id_presupuesto, numero_version_presupuesto, activo_version)
```

### 9.5 Riesgos de Migración a Producción

**⚠️ CRÍTICO: Presupuestos Existentes Sin Versiones**

**Problema**: 
Si hay presupuestos creados ANTES de implementar el sistema de versiones, sus líneas no tienen `id_version_presupuesto` asignado.

**Diagnóstico**:
```sql
-- Verificar líneas huérfanas
SELECT COUNT(*) 
FROM linea_presupuesto 
WHERE id_version_presupuesto IS NULL;
```

**Solución - Script de Migración**:
```sql
-- 1. Para cada presupuesto existente, obtener su versión 1
-- 2. Actualizar todas sus líneas para vincularlas a esa versión

UPDATE linea_presupuesto lp
INNER JOIN presupuesto p ON lp.id_presupuesto = p.id_presupuesto
INNER JOIN presupuesto_version pv ON p.id_presupuesto = pv.id_presupuesto 
    AND pv.numero_version_presupuesto = 1
SET lp.id_version_presupuesto = pv.id_version_presupuesto
WHERE lp.id_version_presupuesto IS NULL;
```

**Validación Post-Migración**:
```sql
-- No deben quedar líneas sin versión
SELECT COUNT(*) FROM linea_presupuesto WHERE id_version_presupuesto IS NULL;
-- Debe devolver 0

-- Verificar que cada presupuesto tiene al menos versión 1
SELECT COUNT(*) FROM presupuesto p
LEFT JOIN presupuesto_version pv ON p.id_presupuesto = pv.id_presupuesto
WHERE pv.id_version_presupuesto IS NULL;
-- Debe devolver 0
```

---

## 📝 10. MEJORAS FUTURAS (POST-IMPLEMENTACIÓN)

### 10.1 Versionado Automático Inteligente

**Concepto**: Al cambiar estado a "enviado", preguntar si desea crear automáticamente nueva versión en caso de futuras modificaciones.

**Flujo**:
```
Usuario: Marca v1 como "Enviado"
  ↓
Sistema: "¿Desea crear versión 2 automáticamente si necesita modificaciones?"
  ↓
Si acepta:
  - Crea v2 en borrador
  - Duplica líneas
  - Establece v2 como actual
  - Bloquea v1
```

**Ventaja**: Usuario no tiene que recordar crear nueva versión manualmente.

### 10.2 Plantillas de Modificaciones

**Concepto**: Guardar configuraciones típicas de modificaciones para aplicarlas rápidamente.

**Ejemplos de Plantillas**:
- "Reducción 10%": Aplica -10% a todos los precios
- "Eliminar extras": Elimina líneas de categoría "extras"
- "Solo equipos básicos": Filtra por código de artículo

**Implementación**:
```sql
CREATE TABLE plantilla_modificacion (
    id_plantilla_mod INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_plantilla VARCHAR(100) NOT NULL,
    tipo_plantilla ENUM('descuento_porcentual', 'descuento_fijo', 'filtro_categoria', 'custom'),
    valor_plantilla VARCHAR(255),
    activo_plantilla BOOLEAN DEFAULT TRUE
);
```

### 10.3 Aprobación por Líneas

**Concepto**: Permitir que el cliente apruebe/rechace líneas individuales, no toda la versión.

**Flujo**:
```
Cliente recibe presupuesto v1
  ↓
Cliente marca:
  - Línea 1: Aprobada ✅
  - Línea 2: Aprobada ✅
  - Línea 3: Rechazada ❌ (demasiado cara)
  - Línea 4: Aprobada ✅
  ↓
Sistema crea v2:
  - Solo con líneas aprobadas
  - O marca líneas rechazadas para modificación
```

**Implementación**:
```sql
ALTER TABLE linea_presupuesto
ADD estado_aprobacion_linea ENUM('pendiente', 'aprobada', 'rechazada', 'modificada') DEFAULT 'pendiente',
ADD comentario_cliente_linea TEXT NULL;
```

### 10.4 Notificaciones por Email

**Concepto**: Enviar emails automáticos en eventos clave.

**Eventos a notificar**:
1. Nueva versión creada → Notificar al comercial
2. Versión enviada al cliente → Notificar al cliente con link
3. Cliente aprueba/rechaza → Notificar al comercial
4. Presupuesto aprobado → Notificar a producción

**Implementación**:
```php
// En controller al cambiar estado
if ($nuevo_estado == 'enviado') {
    $email->enviarNotificacion([
        'destinatario' => $datos_cliente['email'],
        'asunto' => "Presupuesto {$numero_ppto} - Versión {$numero_version}",
        'plantilla' => 'presupuesto_enviado',
        'adjuntos' => [$ruta_pdf]
    ]);
}
```

### 10.5 Integración con CRM

**Concepto**: Sincronizar estados de versiones con sistema de seguimiento comercial.

**Campos a sincronizar**:
- Estado del presupuesto
- Versión actual
- Fecha última modificación
- Probabilidad de cierre (según versión)

**API Webhook**:
```php
// Al cambiar estado
public function cambiar_estado_version($id_version, $nuevo_estado) {
    // ... lógica actual ...
    
    // Sincronizar con CRM
    $crm->sincronizarPresupuesto([
        'id_externo' => $id_presupuesto,
        'estado' => $nuevo_estado,
        'version' => $numero_version,
        'fecha' => date('Y-m-d H:i:s')
    ]);
}
```

### 10.6 Análisis Predictivo

**Concepto**: Usar histórico de versiones para predecir probabilidad de cierre.

**Métricas a analizar**:
- Número promedio de versiones antes de aprobar
- Tiempo promedio entre versiones
- Tipo de modificaciones más comunes
- Tasa de éxito según cliente/sector

**Dashboard Predictivo**:
```
Presupuesto PPTO-2025-001 (v2)
Estado: Enviado
Probabilidad de aprobación: 73%
Recomendación: Esperar 3 días antes de crear v3
```

### 10.7 Modo Colaborativo

**Concepto**: Múltiples usuarios trabajando en diferentes versiones simultáneamente.

**Escenario**:
- Usuario A trabaja en v2 (versión actual)
- Usuario B revisa v1 (versión histórica)
- Usuario C compara v1 vs v2

**Implementación**:
- WebSockets para actualización en tiempo real
- Indicadores de "Usuario X está editando"
- Bloqueos optimistas en lugar de pesimistas

### 10.8 Exportación a Otros Formatos

**Formatos adicionales**:
- Excel: Con comparación de versiones en hojas separadas
- Word: Presupuesto con formato de contrato
- XML/JSON: Para integraciones con otros sistemas

**Exportación comparativa**:
```
Archivo: PPTO-2025-001_comparacion_v1_v2.xlsx

Hoja 1: Versión 1
Hoja 2: Versión 2
Hoja 3: Diferencias (tabla comparativa)
Hoja 4: Resumen ejecutivo
```

---

## ✅ 11. CHECKLIST DE VALIDACIÓN PRE-PRODUCCIÓN

### 11.1 Base de Datos

- [ ] Todas las tablas creadas correctamente
- [ ] Todos los campos con tipos y restricciones correctos
- [ ] Foreign Keys funcionando (ON DELETE, ON UPDATE)
- [ ] Índices creados en campos clave
- [ ] Charset utf8mb4_spanish_ci en todas las tablas
- [ ] Triggers instalados y funcionando
- [ ] Vistas SQL creadas (si aplica)
- [ ] Script de migración de datos existentes probado
- [ ] Backup de base de datos antes de migración
- [ ] Rollback plan documentado

### 11.2 Backend - Modelos

- [ ] Clase Presupuesto con todos los métodos
- [ ] Clase LineaPresupuesto validada
- [ ] Transacciones PDO en métodos críticos
- [ ] Validaciones de estado antes de operaciones
- [ ] Manejo de errores con try-catch
- [ ] Logging de actividades en RegistroActividad
- [ ] Retornos consistentes (ID, boolean, array)
- [ ] Comentarios PHPDoc en métodos públicos
- [ ] Tests unitarios básicos
- [ ] Sin SQL injection (prepared statements siempre)

### 11.3 Backend - Controladores

- [ ] Todas las operaciones implementadas
- [ ] Sanitización de inputs (htmlspecialchars, trim)
- [ ] Validación de tipos de datos
- [ ] Respuestas JSON estandarizadas
- [ ] Headers Content-Type correctos
- [ ] JSON_UNESCAPED_UNICODE en json_encode
- [ ] Manejo de excepciones
- [ ] Logging de operaciones críticas
- [ ] Tests de integración con Postman
- [ ] Documentación de endpoints

### 11.4 Frontend - Vistas

- [ ] Modal de versiones creado
- [ ] Modal de comparación creado
- [ ] Modificación de mntpresupuesto.php
- [ ] Modificación de formularioPresupuesto.php
- [ ] HTML5 semántico
- [ ] Bootstrap 5 correcto
- [ ] Responsive en móvil/tablet
- [ ] Accesibilidad (ARIA labels)
- [ ] Sin lógica de negocio en vistas
- [ ] Validación client-side (complementaria)

### 11.5 Frontend - JavaScript

- [ ] Archivo versiones.js creado
- [ ] Todas las funciones implementadas
- [ ] AJAX con manejo de errores
- [ ] Promesas correctamente gestionadas
- [ ] Loading spinners durante operaciones largas
- [ ] SweetAlert2 para confirmaciones
- [ ] DataTables configurado correctamente
- [ ] Idioma español en componentes
- [ ] Sin console.log en producción
- [ ] Código comentado y legible

### 11.6 Seguridad

- [ ] Prepared statements en 100% de queries
- [ ] Sanitización de todos los inputs
- [ ] Validación server-side siempre
- [ ] No exponer detalles de errores SQL
- [ ] Credenciales en JSON externo (.gitignore)
- [ ] Sin contraseñas hardcoded
- [ ] Logging de intentos de acceso no autorizado
- [ ] Validación de sesiones de usuario
- [ ] CSRF tokens (si aplica)
- [ ] Rate limiting en operaciones críticas

### 11.7 Testing

- [ ] Tests unitarios de modelos
- [ ] Tests de integración de controladores
- [ ] Tests de UI con usuarios reales
- [ ] Tests de carga (100+ versiones)
- [ ] Tests de regresión completos
- [ ] Validación de todos los triggers
- [ ] Tests de workflows completos
- [ ] Tests de casos extremos
- [ ] Tests de manejo de errores
- [ ] Tests cross-browser (Chrome, Firefox, Edge)

### 11.8 Documentación

- [ ] README.md actualizado
- [ ] Manual de usuario final
- [ ] Documentación técnica de API
- [ ] Diagramas de flujo
- [ ] ERD actualizado
- [ ] CHANGELOG.md con versiones
- [ ] Guía de troubleshooting
- [ ] Video tutorial (opcional)
- [ ] FAQs documentadas
- [ ] Contacto de soporte definido

### 11.9 Performance

- [ ] Queries optimizadas con EXPLAIN
- [ ] Índices adecuados en tablas
- [ ] Caché de versiones activas (opcional)
- [ ] Paginación en listados grandes
- [ ] Compresión de respuestas JSON
- [ ] Lazy loading de imágenes/PDFs
- [ ] Minificación de JS/CSS
- [ ] CDN para librerías externas
- [ ] Monitoreo de tiempos de respuesta
- [ ] Plan de escalado si crece

### 11.10 Deployment

- [ ] Servidor de producción configurado
- [ ] PHP 8+ instalado
- [ ] MySQL/MariaDB actualizado
- [ ] Permisos de archivos correctos
- [ ] Directorios de logs creados
- [ ] Backup automático configurado
- [ ] Monitoreo de errores (Sentry, etc.)
- [ ] SSL/HTTPS habilitado
- [ ] Variables de entorno configuradas
- [ ] Rollback plan documentado

---

## 📚 12. RECURSOS Y REFERENCIAS

### 12.1 Documentación Interna

| Archivo | Ubicación | Descripción |
|---------|-----------|-------------|
| Sistema de Versiones | `./BD/docs/sistema_versiones.md` | Arquitectura completa (720 líneas) |
| Triggers del Sistema | `./BD/docs/triggers_sistema_versiones.sql` | 10 triggers documentados (449 líneas) |
| Campos Línea Calculada | `./BD/campos_linea_presupuesto_calculada.md` | Estructura de cálculos |
| Vista Totales | `./BD/v_presupuesto_totales.sql` | Vista SQL para totales |

### 12.2 Archivos Clave del Proyecto

| Archivo | Ubicación | Estado | Descripción |
|---------|-----------|--------|-------------|
| Presupuesto Model | `models/Presupuesto.php` | ⚠️ Incompleto | Modelo principal (falta 80%) |
| Línea Model | `models/LineaPresupuesto.php` | ✅ Completo | Modelo de líneas funcional |
| Presupuesto Controller | `controller/presupuesto.php` | ⚠️ Incompleto | Controlador principal (falta 70%) |
| Línea Controller | `controller/lineapresupuesto.php` | ✅ Completo | Controlador de líneas funcional |
| Vista Listado | `view/Presupuesto/mntpresupuesto.php` | ⚠️ A modificar | Listado de presupuestos |
| Vista Formulario | `view/Presupuesto/formularioPresupuesto.php` | ⚠️ A modificar | Edición de presupuesto |

### 12.3 Tecnologías Utilizadas

| Tecnología | Versión | Uso |
|------------|---------|-----|
| PHP | 8.x | Backend |
| MySQL/MariaDB | 8.0+ | Base de datos |
| Bootstrap | 5.0.2 | Framework CSS |
| jQuery | 3.7.1 | Manipulación DOM |
| DataTables | Latest | Tablas interactivas |
| SweetAlert2 | 11.7.32 | Alertas |
| Font Awesome | 6.4.2 | Iconos |
| PDO | Nativo PHP | Conexión BD |

### 12.4 Convenciones del Proyecto

- **Patrón**: MVC estricto sin frameworks
- **Nomenclatura BD**: Tablas singular, campos con sufijo `_tabla`
- **Soft Delete**: Campo `activo_tabla` (nunca DELETE físico)
- **Timestamps**: `created_at_tabla`, `updated_at_tabla`
- **Prepared Statements**: SIEMPRE (seguridad SQL injection)
- **JSON Responses**: `JSON_UNESCAPED_UNICODE`
- **Logging**: Clase `RegistroActividad` en logs diarios
- **Zona Horaria**: `Europe/Madrid`

### 12.5 Enlaces Externos

- PHP PDO: https://www.php.net/manual/es/book.pdo.php
- Bootstrap 5: https://getbootstrap.com/docs/5.0/
- DataTables: https://datatables.net/
- SweetAlert2: https://sweetalert2.github.io/
- jQuery API: https://api.jquery.com/

---

## 📊 13. RESUMEN DE TIEMPOS Y COSTOS

### 13.1 Desglose por Fase

| Fase | Descripción | Tiempo (horas) | Prioridad |
|------|-------------|----------------|-----------|
| **Fase 1** | Backend Core | 12-15 | 🔴 CRÍTICA |
| **Fase 2** | Gestión de Estados | 7-9 | 🟡 ALTA |
| **Fase 3** | Frontend Básico | 9 | 🟡 ALTA |
| **Fase 4** | Comparación | 11 | 🟢 MEDIA |
| **Fase 5** | Mejoras UI | 7 | 🟢 MEDIA |
| **Fase 6** | PDFs y Extras | 13 | 🔵 BAJA |
| **TOTAL** | | **59-64 horas** | |

### 13.2 Distribución por Componente

| Componente | Tiempo (horas) | % del Total |
|------------|----------------|-------------|
| Modelos PHP | 14-16 | 24% |
| Controladores | 8-10 | 14% |
| Vistas HTML | 8-10 | 14% |
| JavaScript | 10-12 | 18% |
| PDFs | 6-8 | 10% |
| Testing | 8-10 | 14% |
| Documentación | 3-4 | 6% |

### 13.3 Hitos Clave

| Hito | Fecha Estimada | Entregable |
|------|----------------|------------|
| **Hito 1** | Día 2 | Backend funcional desde Postman |
| **Hito 2** | Día 4 | Workflow de estados completo |
| **Hito 3** | Día 6 | UI básica funcional |
| **Hito 4** | Día 8 | Sistema de comparación |
| **Hito 5** | Día 10 | UI pulida y completa |
| **Hito 6** | Día 13 | Sistema completo en producción |

### 13.4 Mínimo Viable (MVP)

**Para tener funcionalidad básica de versiones:**

- ✅ Fase 1: Backend Core (12-15h)
- ✅ Fase 2: Gestión Estados (7-9h)
- ✅ Fase 3: Frontend Básico (9h)

**Total MVP**: 28-33 horas

**Funcionalidades MVP:**
- Crear nuevas versiones
- Cambiar estados de versiones
- Ver lista de versiones
- Duplicación automática de líneas
- Workflow básico completo

### 13.5 Sistema Completo

**Para tener todas las características:**

- ✅ Fases 1-6 completas

**Total Completo**: 59-64 horas

**Funcionalidades Adicionales:**
- Comparación visual de versiones
- Generación de PDFs versionados
- Dashboard de métricas
- UI avanzada con bloqueos
- Documentación completa

---

## 🎯 14. CONCLUSIONES Y RECOMENDACIONES FINALES

### 14.1 Estado Actual del Sistema

El sistema de versiones de presupuestos en MDR está en un **estado de implementación parcial muy avanzada a nivel de infraestructura** pero **completamente inoperativo desde el punto de vista funcional**.

**Fortalezas:**
- ✅ Diseño de base de datos robusto y escalable
- ✅ Triggers implementados y funcionales (100%)
- ✅ Documentación técnica exhaustiva
- ✅ Arquitectura bien pensada con genealogía de versiones
- ✅ Validaciones a nivel SQL sólidas

**Debilidades:**
- ❌ Modelos PHP sin implementar (90% pendiente)
- ❌ Controladores sin operaciones de versiones
- ❌ Frontend sin UI para versiones
- ❌ No hay forma de usar versiones desde la aplicación

### 14.2 Recomendación de Prioridad

**OPCIÓN A: Implementación Completa (Recomendado)**

Si el negocio requiere:
- Trazabilidad completa de cambios en presupuestos
- Histórico inmutable para auditorías
- Gestión profesional de negociaciones con clientes
- Cumplimiento con políticas de calidad

➡️ **Implementar TODAS las fases (59-64 horas)**

**Beneficios:**
- Sistema profesional y completo
- Ventaja competitiva en gestión comercial
- Reduce errores y malentendidos con clientes
- Facilita análisis de ventas
- Mejora imagen corporativa

**OPCIÓN B: MVP Rápido (Alternativa)**

Si el negocio necesita:
- Funcionalidad básica urgente
- Reducir presupuesto inicial
- Validar concepto antes de inversión completa

➡️ **Implementar solo Fases 1-3 (28-33 horas)**

**Funcionalidades disponibles:**
- Crear versiones manualmente
- Cambiar estados
- Ver listado de versiones
- Workflow básico funcional

**Limitaciones:**
- Sin comparación visual
- Sin PDFs automáticos
- Sin métricas/dashboard
- UI básica sin pulir

### 14.3 Orden de Implementación Recomendado

**Semana 1: Backend Core (URGENTE)**
- Días 1-2: Fase 1 (Backend Core) - 12-15h
- Días 3-4: Fase 2 (Gestión Estados) - 7-9h

**Semana 2: Frontend Básico (IMPORTANTE)**
- Días 5-6: Fase 3 (Frontend Básico) - 9h
- Testing y bugfixing inicial

**Semana 3: Comparación (OPCIONAL)**
- Días 7-8: Fase 4 (Comparación) - 11h

**Semana 4: Pulido (OPCIONAL)**
- Días 9-10: Fase 5 (Mejoras UI) - 7h
- Días 11-13: Fase 6 (PDFs/Extras) - 13h
- Testing exhaustivo y producción

### 14.4 Riesgos a Gestionar

**1. Cambio de Workflow para Usuarios**

**Riesgo**: Los usuarios están acostumbrados a editar presupuestos directamente sin pensar en versiones.

**Mitigación**:
- Capacitación antes del lanzamiento
- Video tutoriales paso a paso
- Soporte activo en primera semana
- Mensajes informativos en la UI

**2. Migración de Datos Existentes**

**Riesgo**: Presupuestos antiguos sin versiones asociadas.

**Mitigación**:
- Script de migración probado en desarrollo
- Backup completo antes de producción
- Rollback plan documentado
- Validación post-migración exhaustiva

**3. Performance con Muchas Versiones**

**Riesgo**: Degradación de performance con presupuestos de 10+ versiones.

**Mitigación**:
- Índices adecuados en BD
- Paginación en listados
- Caché de versiones activas
- Monitoreo de tiempos de respuesta

### 14.5 Mantenimiento Futuro

**Actividades Recomendadas:**

1. **Mensual**: Revisar logs de errores relacionados con versiones
2. **Trimestral**: Analizar métricas de uso (promedio versiones/presupuesto)
3. **Semestral**: Optimizar queries según patrones de uso
4. **Anual**: Evaluar nuevas funcionalidades basadas en feedback

**KPIs a Monitorear:**

- Promedio de versiones por presupuesto
- Tasa de aprobación en primera versión
- Tiempo medio entre creación y aprobación
- Número de presupuestos con 5+ versiones (outliers)
- Tiempo de duplicación de líneas (performance)

### 14.6 Decisión Final

Basado en la investigación exhaustiva realizada, **RECOMIENDO ENCARECIDAMENTE** proceder con la implementación completa del sistema de versiones (Fases 1-6) por las siguientes razones:

1. **ROI Alto**: La inversión de 59-64 horas se recupera rápidamente con:
   - Reducción de errores en presupuestos
   - Mejor seguimiento comercial
   - Menor tiempo en gestionar cambios
   - Mayor profesionalidad percibida por clientes

2. **Base Sólida Ya Existente**: El 50% del trabajo (BD + triggers) ya está hecho.

3. **Ventaja Competitiva**: Pocos ERPs de alquiler tienen gestión de versiones tan robusta.

4. **Escalabilidad**: Sistema preparado para crecer con el negocio.

5. **Cumplimiento**: Facilita auditorías y control de calidad.

### 14.7 Próximos Pasos Inmediatos

**1. DECISIÓN**: Definir alcance (MVP o Completo)

**2. PLANIFICACIÓN**: Asignar desarrollador(es) y calendario

**3. ENTORNO**: Preparar entorno de desarrollo con copia de BD producción

**4. MIGRACIÓN**: Ejecutar script de migración de datos existentes en DEV

**5. DESARROLLO**: Comenzar Fase 1 (Backend Core)

**6. TESTING**: Tests continuos en cada fase

**7. CAPACITACIÓN**: Preparar material de training

**8. PRODUCCIÓN**: Deploy con plan de rollback

**9. SOPORTE**: Acompañamiento activo primera semana

**10. OPTIMIZACIÓN**: Ajustes según feedback real

---

## 📞 15. CONTACTO Y SOPORTE

**Documentación creada por**: GitHub Copilot (Claude Sonnet 4.5)  
**Fecha**: 30 de enero de 2026  
**Versión del Documento**: 1.0  

**Para consultas técnicas**:
- Revisar documentación en `./BD/docs/sistema_versiones.md`
- Consultar triggers en `./BD/docs/triggers_sistema_versiones.sql`
- Verificar estado de implementación en este documento

**Actualizaciones de este documento**:
- Actualizar al completar cada fase
- Añadir sección "Cambios Implementados"
- Documentar problemas encontrados y soluciones
- Registrar decisiones de diseño tomadas

---

**FIN DEL DOCUMENTO**

---

*Este documento es la guía maestra para la implementación del sistema de versiones de presupuestos en MDR ERP Manager. Debe mantenerse actualizado conforme avanza el desarrollo.*