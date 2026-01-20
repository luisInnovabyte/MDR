-- ============================================
-- TRIGGERS PARA SISTEMA DE VERSIONES DE PRESUPUESTOS
-- ============================================
-- Proyecto: MDR ERP Manager
-- Base de datos: toldos_db
-- Fecha: 20 de enero de 2026
-- Descripción: Triggers que automatizan y protegen el sistema de versiones
-- ============================================

-- INSTRUCCIONES DE USO:
-- 1. Hacer backup de la base de datos antes de ejecutar
-- 2. Ejecutar este script completo en toldos_db
-- 3. Verificar que todos los triggers se crearon correctamente:
--    SHOW TRIGGERS LIKE '%version%';
-- 4. Probar cada trigger con casos de prueba

-- ============================================
-- CONFIGURACIÓN INICIAL
-- ============================================

USE toldos_db;

-- Establecer delimitador para los triggers
DELIMITER //

-- ============================================
-- TRIGGER 1: AUTO-CREAR VERSIÓN 1 AL INSERTAR PRESUPUESTO
-- ============================================
-- Descripción: Crea automáticamente la versión 1 cuando se inserta un presupuesto nuevo
-- Ventaja: Garantiza que todo presupuesto tenga su versión inicial sin intervención manual
-- Prioridad: ALTA - CRÍTICO
-- ============================================

DROP TRIGGER IF EXISTS trg_presupuesto_after_insert//

CREATE TRIGGER trg_presupuesto_after_insert
AFTER INSERT ON presupuesto
FOR EACH ROW
BEGIN
    -- Crear automáticamente la versión 1
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
        1,                              -- Siempre es versión 1
        NULL,                           -- No tiene padre
        'borrador',                     -- Empieza como borrador
        NEW.creado_por_presupuesto,    -- Usuario que creó el presupuesto
        'Versión inicial',              -- Motivo por defecto
        NOW()                           -- Fecha actual
    );
    
    -- Actualizar el presupuesto para apuntar a la versión 1
    UPDATE presupuesto
    SET 
        version_actual_presupuesto = 1,
        estado_general_presupuesto = 'borrador'
    WHERE id_presupuesto = NEW.id_presupuesto;
END//

-- ============================================
-- TRIGGER 2: PREVENIR MODIFICACIÓN DE LÍNEAS NO-BORRADOR
-- ============================================
-- Descripción: Bloquea modificaciones de líneas en versiones que no están en borrador
-- Ventaja: Garantiza inmutabilidad de versiones enviadas/aprobadas/rechazadas
-- Prioridad: ALTA - CRÍTICO
-- ============================================

DROP TRIGGER IF EXISTS trg_linea_presupuesto_before_update//

CREATE TRIGGER trg_linea_presupuesto_before_update
BEFORE UPDATE ON linea_presupuesto
FOR EACH ROW
BEGIN
    DECLARE estado_version VARCHAR(20);
    
    -- Obtener estado de la versión a la que pertenece esta línea
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = NEW.id_version_presupuesto;
    
    -- Bloquear si no es borrador
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden modificar líneas de versiones que no están en borrador. Para hacer cambios, cree una nueva versión.';
    END IF;
END//

-- ============================================
-- TRIGGER 3: PREVENIR ELIMINACIÓN DE LÍNEAS NO-BORRADOR
-- ============================================
-- Descripción: Bloquea eliminación de líneas en versiones que no están en borrador
-- Ventaja: Mantiene histórico completo de líneas en versiones enviadas
-- Prioridad: ALTA - CRÍTICO
-- ============================================

DROP TRIGGER IF EXISTS trg_linea_presupuesto_before_delete//

CREATE TRIGGER trg_linea_presupuesto_before_delete
BEFORE DELETE ON linea_presupuesto
FOR EACH ROW
BEGIN
    DECLARE estado_version VARCHAR(20);
    
    -- Obtener estado de la versión
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si no es borrador
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar líneas de versiones que no están en borrador. El histórico debe permanecer inmutable.';
    END IF;
END//

-- ============================================
-- TRIGGER 4: VALIDAR CREACIÓN DE NUEVAS VERSIONES
-- ============================================
-- Descripción: Valida que se puedan crear nuevas versiones según reglas de negocio
-- Ventaja: Previene versiones inválidas (ej: crear v3 mientras v2 está en borrador)
-- Prioridad: ALTA - CRÍTICO
-- ============================================

DROP TRIGGER IF EXISTS trg_presupuesto_version_before_insert_validar//

CREATE TRIGGER trg_presupuesto_version_before_insert_validar
BEFORE INSERT ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE estado_actual VARCHAR(20);
    DECLARE version_actual INT;
    
    -- Obtener estado y número de la versión actual
    SELECT 
        pv.estado_version_presupuesto,
        p.version_actual_presupuesto
    INTO 
        estado_actual,
        version_actual
    FROM presupuesto p
    LEFT JOIN presupuesto_version pv 
        ON pv.id_presupuesto = p.id_presupuesto 
        AND pv.numero_version_presupuesto = p.version_actual_presupuesto
    WHERE p.id_presupuesto = NEW.id_presupuesto;
    
    -- REGLA 1: No crear versiones si está aprobada o cancelada
    IF estado_actual IN ('aprobado', 'cancelado') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden crear nuevas versiones de presupuestos aprobados o cancelados. El presupuesto está cerrado.';
    END IF;
    
    -- REGLA 2: No crear nueva versión si existe una en borrador
    IF estado_actual = 'borrador' AND NEW.numero_version_presupuesto > 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede crear una nueva versión mientras existe una versión en borrador. Complete o envíe la versión actual primero.';
    END IF;
    
    -- REGLA 3: Solo se pueden crear versiones desde estados 'enviado' o 'rechazado'
    IF NEW.numero_version_presupuesto > 1 
       AND estado_actual NOT IN ('enviado', 'rechazado') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Solo se pueden crear nuevas versiones desde estados enviado o rechazado.';
    END IF;
END//

-- ============================================
-- TRIGGER 5: VALIDAR TRANSICIONES DE ESTADO
-- ============================================
-- Descripción: Controla que los cambios de estado sigan el workflow permitido
-- Ventaja: Previene transiciones ilógicas (ej: borrador → aprobado directamente)
-- Prioridad: MEDIA
-- ============================================

DROP TRIGGER IF EXISTS trg_version_validar_transicion_estado//

CREATE TRIGGER trg_version_validar_transicion_estado
BEFORE UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    -- Solo validar si cambió el estado
    IF OLD.estado_version_presupuesto != NEW.estado_version_presupuesto THEN
        
        -- DESDE BORRADOR: solo puede ir a 'enviado' o 'cancelado'
        IF OLD.estado_version_presupuesto = 'borrador' 
           AND NEW.estado_version_presupuesto NOT IN ('enviado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde borrador solo se puede pasar a enviado o cancelado. Workflow inválido.';
        END IF;
        
        -- DESDE ENVIADO: solo puede ir a 'aprobado', 'rechazado' o 'cancelado'
        IF OLD.estado_version_presupuesto = 'enviado' 
           AND NEW.estado_version_presupuesto NOT IN ('aprobado', 'rechazado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde enviado solo se puede pasar a aprobado, rechazado o cancelado. Workflow inválido.';
        END IF;
        
        -- ESTADOS FINALES: 'aprobado' y 'cancelado' no pueden cambiar
        IF OLD.estado_version_presupuesto IN ('aprobado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: No se puede cambiar el estado de versiones aprobadas o canceladas. Son estados finales e inmutables.';
        END IF;
        
        -- DESDE RECHAZADO: solo puede ir a 'cancelado'
        IF OLD.estado_version_presupuesto = 'rechazado' 
           AND NEW.estado_version_presupuesto != 'cancelado' THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Una versión rechazada solo puede cancelarse. Para nuevos intentos, cree una nueva versión.';
        END IF;
    END IF;
END//

-- ============================================
-- TRIGGER 6: AUTO-ESTABLECER FECHAS SEGÚN CAMBIO DE ESTADO
-- ============================================
-- Descripción: Asigna automáticamente fechas cuando cambia el estado
-- Ventaja: Auditoría automática sin intervención manual
-- Prioridad: MEDIA
-- ============================================

DROP TRIGGER IF EXISTS trg_version_auto_fechas//

CREATE TRIGGER trg_version_auto_fechas
BEFORE UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    -- Cuando se marca como 'enviado'
    IF NEW.estado_version_presupuesto = 'enviado' 
       AND OLD.estado_version_presupuesto != 'enviado' 
       AND NEW.fecha_envio_version IS NULL THEN
        SET NEW.fecha_envio_version = NOW();
    END IF;
    
    -- Cuando se marca como 'aprobado'
    IF NEW.estado_version_presupuesto = 'aprobado' 
       AND OLD.estado_version_presupuesto != 'aprobado' 
       AND NEW.fecha_aprobacion_version IS NULL THEN
        SET NEW.fecha_aprobacion_version = NOW();
    END IF;
    
    -- Cuando se marca como 'rechazado'
    IF NEW.estado_version_presupuesto = 'rechazado' 
       AND OLD.estado_version_presupuesto != 'rechazado' 
       AND NEW.fecha_rechazo_version IS NULL THEN
        SET NEW.fecha_rechazo_version = NOW();
    END IF;
END//

-- ============================================
-- TRIGGER 7: AUTO-GENERAR RUTA DE PDF
-- ============================================
-- Descripción: Genera automáticamente la ruta del PDF al enviar
-- Ventaja: Nombres consistentes y estandarizados
-- Prioridad: BAJA (comodidad)
-- ============================================

DROP TRIGGER IF EXISTS trg_version_auto_ruta_pdf//

CREATE TRIGGER trg_version_auto_ruta_pdf
BEFORE UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE numero_ppto VARCHAR(50);
    
    -- Solo generar ruta cuando se envía y no existe ruta
    IF NEW.estado_version_presupuesto = 'enviado' 
       AND OLD.estado_version_presupuesto != 'enviado'
       AND (NEW.ruta_pdf_version IS NULL OR NEW.ruta_pdf_version = '') THEN
        
        -- Obtener número de presupuesto
        SELECT numero_presupuesto INTO numero_ppto
        FROM presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
        
        -- Generar ruta: /documentos/presupuestos/PPTO-2025-001_v2.pdf
        SET NEW.ruta_pdf_version = CONCAT(
            '/documentos/presupuestos/',
            numero_ppto,
            '_v',
            NEW.numero_version_presupuesto,
            '.pdf'
        );
    END IF;
END//

-- ============================================
-- TRIGGER 8: SINCRONIZAR ESTADO DE CABECERA CON VERSIÓN ACTUAL
-- ============================================
-- Descripción: Actualiza automáticamente el estado general del presupuesto
-- Ventaja: Mantiene sincronización entre presupuesto y versión actual
-- Prioridad: ALTA - CRÍTICO
-- ============================================

DROP TRIGGER IF EXISTS trg_version_sync_estado_cabecera//

CREATE TRIGGER trg_version_sync_estado_cabecera
AFTER UPDATE ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE version_actual INT;
    
    -- Obtener la versión actual del presupuesto
    SELECT version_actual_presupuesto INTO version_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto;
    
    -- Si esta es la versión actual, sincronizar estado en la cabecera
    IF NEW.numero_version_presupuesto = version_actual THEN
        UPDATE presupuesto
        SET estado_general_presupuesto = NEW.estado_version_presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
    END IF;
END//

-- ============================================
-- TRIGGER 9: PREVENIR ELIMINACIÓN DE VERSIONES CON DEPENDENCIAS
-- ============================================
-- Descripción: Bloquea eliminación de versiones que tienen líneas o versiones hijas
-- Ventaja: Protege integridad referencial del histórico
-- Prioridad: BAJA (protección adicional)
-- ============================================

DROP TRIGGER IF EXISTS trg_presupuesto_version_before_delete//

CREATE TRIGGER trg_presupuesto_version_before_delete
BEFORE DELETE ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE num_lineas INT;
    DECLARE tiene_hijos INT;
    
    -- Contar líneas asociadas a esta versión
    SELECT COUNT(*) INTO num_lineas
    FROM linea_presupuesto
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si tiene líneas
    IF num_lineas > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene líneas asociadas. Elimine primero las líneas.';
    END IF;
    
    -- Contar versiones hijas (que tienen esta como padre)
    SELECT COUNT(*) INTO tiene_hijos
    FROM presupuesto_version
    WHERE version_padre_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si tiene versiones hijas
    IF tiene_hijos > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene versiones hijas. Esto rompería la cadena genealógica.';
    END IF;
    
    -- Bloquear si no está en borrador (versiones enviadas/aprobadas/rechazadas deben permanecer)
    IF OLD.estado_version_presupuesto != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar versiones que no están en borrador. El histórico debe ser inmutable.';
    END IF;
END//

-- ============================================
-- TRIGGER 10: AUTO-CALCULAR NÚMERO DE VERSIÓN
-- ============================================
-- Descripción: Asigna automáticamente el siguiente número de versión
-- Ventaja: Evita conflictos y errores de numeración manual
-- Prioridad: ALTA - CRÍTICO
-- ============================================

DROP TRIGGER IF EXISTS trg_presupuesto_version_before_insert_numero//

CREATE TRIGGER trg_presupuesto_version_before_insert_numero
BEFORE INSERT ON presupuesto_version
FOR EACH ROW
BEGIN
    DECLARE max_version INT;
    
    -- Si no se especificó número de versión, calcularlo automáticamente
    IF NEW.numero_version_presupuesto IS NULL OR NEW.numero_version_presupuesto = 0 THEN
        
        -- Obtener el número de versión más alto actual para este presupuesto
        SELECT COALESCE(MAX(numero_version_presupuesto), 0) INTO max_version
        FROM presupuesto_version
        WHERE id_presupuesto = NEW.id_presupuesto;
        
        -- Asignar el siguiente número de versión
        SET NEW.numero_version_presupuesto = max_version + 1;
    END IF;
END//

-- ============================================
-- RESTAURAR DELIMITADOR
-- ============================================

DELIMITER ;

-- ============================================
-- VERIFICACIÓN DE INSTALACIÓN
-- ============================================

-- Listar todos los triggers creados
SELECT 
    TRIGGER_NAME AS 'Trigger',
    EVENT_MANIPULATION AS 'Evento',
    EVENT_OBJECT_TABLE AS 'Tabla',
    ACTION_TIMING AS 'Momento'
FROM information_schema.TRIGGERS
WHERE TRIGGER_SCHEMA = 'toldos_db'
AND TRIGGER_NAME LIKE 'trg_%version%'
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION;

-- ============================================
-- NOTAS IMPORTANTES
-- ============================================

-- 1. ORDEN DE EJECUCIÓN:
--    Los triggers BEFORE se ejecutan antes que los AFTER
--    Si hay múltiples triggers del mismo tipo, se ejecutan en orden alfabético
--
-- 2. PRUEBAS RECOMENDADAS:
--    - Crear un presupuesto y verificar que se crea la v1
--    - Intentar modificar líneas en una versión enviada (debe fallar)
--    - Crear nueva versión desde una rechazada (debe funcionar)
--    - Intentar crear v3 mientras v2 está en borrador (debe fallar)
--
-- 3. DESACTIVAR TRIGGERS TEMPORALMENTE (si es necesario):
--    SET @DISABLE_TRIGGER = 1;
--    -- Realizar operación
--    SET @DISABLE_TRIGGER = NULL;
--
-- 4. ELIMINAR TODOS LOS TRIGGERS (si es necesario):
--    DROP TRIGGER IF EXISTS trg_presupuesto_after_insert;
--    DROP TRIGGER IF EXISTS trg_linea_presupuesto_before_update;
--    -- ... etc
--
-- 5. LOG DE ERRORES:
--    Los errores generados por SIGNAL SQLSTATE aparecerán en:
--    - La respuesta de PHP con PDOException
--    - Los logs de MySQL (si están habilitados)

-- ============================================
-- FIN DEL SCRIPT
-- ============================================
