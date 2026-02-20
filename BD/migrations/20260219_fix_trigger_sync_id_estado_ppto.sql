-- ==========================================================================
-- MIGRACIÓN: Fix trg_version_sync_estado_cabecera → sincronizar id_estado_ppto
-- Fecha: 2026-02-19
-- Descripción: 
--   El trigger trg_version_sync_estado_cabecera solo actualizaba el campo
--   estado_general_presupuesto (ENUM) pero NO id_estado_ppto (FK al catálogo
--   estado_presupuesto), que es el que alimenta nombre_estado_ppto en la
--   vista y en el DataTable principal.
--
--   Este script también desactiva el estado 'PROC' (activo=0), que no debe
--   aparecer en el formulario ya que es gestionado automáticamente por versiones.
-- ==========================================================================

-- ==========================================================================
-- PASO 1: Eliminar trigger antiguo
-- ==========================================================================
DROP TRIGGER IF EXISTS `trg_version_sync_estado_cabecera`;

-- ==========================================================================
-- PASO 2: Recrear trigger con sincronización de id_estado_ppto
-- ==========================================================================
DELIMITER $$

CREATE TRIGGER `trg_version_sync_estado_cabecera`
AFTER UPDATE ON `presupuesto_version`
FOR EACH ROW
BEGIN
    DECLARE version_actual INT;
    DECLARE nuevo_id_estado INT;

    -- Obtener la versión actual del presupuesto
    SELECT version_actual_presupuesto
    INTO version_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto;

    -- Solo sincronizar si esta es la versión activa/actual
    IF NEW.numero_version_presupuesto = version_actual THEN

        -- Resolver el id_estado_ppto según el estado de la versión
        SELECT id_estado_ppto
        INTO nuevo_id_estado
        FROM estado_presupuesto
        WHERE codigo_estado_ppto = CASE NEW.estado_version_presupuesto
            WHEN 'borrador'  THEN 'BORRADOR'
            WHEN 'enviado'   THEN 'ESPE-RESP'
            WHEN 'aprobado'  THEN 'APROB'
            WHEN 'rechazado' THEN 'RECH'
            WHEN 'cancelado' THEN 'CANC'
            ELSE 'BORRADOR'
        END
        LIMIT 1;

        -- Actualizar ambos campos en la cabecera del presupuesto
        UPDATE presupuesto
        SET
            estado_general_presupuesto = NEW.estado_version_presupuesto,
            id_estado_ppto             = nuevo_id_estado
        WHERE id_presupuesto = NEW.id_presupuesto;

    END IF;
END$$

DELIMITER ;

-- ==========================================================================
-- PASO 3: Desactivar estado 'PROC' (no debe mostrarse en el formulario)
-- ==========================================================================
UPDATE estado_presupuesto
SET activo_estado_ppto = 0
WHERE codigo_estado_ppto = 'PROC';

-- ==========================================================================
-- VERIFICACIÓN: Estado actual después de la migración
-- ==========================================================================
SELECT 
    id_estado_ppto,
    codigo_estado_ppto,
    nombre_estado_ppto,
    activo_estado_ppto
FROM estado_presupuesto
ORDER BY orden_estado_ppto;
