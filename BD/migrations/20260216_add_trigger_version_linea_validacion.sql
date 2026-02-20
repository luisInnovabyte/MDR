-- =============================================================================
-- Migración: Trigger de validación de estado de versión en líneas de presupuesto
-- Fecha: 2026-02-16
-- Proyecto: MDR ERP Manager
-- Descripción: Impide insertar o actualizar líneas sobre versiones no borradores.
-- =============================================================================

-- -----------------------------------------------
-- TRIGGER: Validar estado ANTES de INSERT
-- -----------------------------------------------
DROP TRIGGER IF EXISTS trg_linea_presupuesto_before_insert;

DELIMITER //
CREATE TRIGGER trg_linea_presupuesto_before_insert
BEFORE INSERT ON linea_presupuesto
FOR EACH ROW
BEGIN
    DECLARE v_estado VARCHAR(20);

    SELECT estado_version_presupuesto
      INTO v_estado
      FROM presupuesto_version
     WHERE id_version_presupuesto = NEW.id_version_presupuesto;

    IF v_estado IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: La versión de presupuesto indicada no existe.';
    END IF;

    IF v_estado != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden añadir líneas a una versión que no está en estado borrador.';
    END IF;
END//
DELIMITER ;

-- -----------------------------------------------
-- TRIGGER: Validar estado ANTES de UPDATE
-- -----------------------------------------------
DROP TRIGGER IF EXISTS trg_linea_presupuesto_before_update;

DELIMITER //
CREATE TRIGGER trg_linea_presupuesto_before_update
BEFORE UPDATE ON linea_presupuesto
FOR EACH ROW
BEGIN
    DECLARE v_estado VARCHAR(20);

    SELECT estado_version_presupuesto
      INTO v_estado
      FROM presupuesto_version
     WHERE id_version_presupuesto = NEW.id_version_presupuesto;

    IF v_estado IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: La versión de presupuesto indicada no existe.';
    END IF;

    IF v_estado != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden modificar líneas de una versión que no está en estado borrador.';
    END IF;
END//
DELIMITER ;

-- -----------------------------------------------
-- TRIGGER: Validar estado ANTES de DELETE
-- -----------------------------------------------
DROP TRIGGER IF EXISTS trg_linea_presupuesto_before_delete;

DELIMITER //
CREATE TRIGGER trg_linea_presupuesto_before_delete
BEFORE DELETE ON linea_presupuesto
FOR EACH ROW
BEGIN
    DECLARE v_estado VARCHAR(20);

    SELECT estado_version_presupuesto
      INTO v_estado
      FROM presupuesto_version
     WHERE id_version_presupuesto = OLD.id_version_presupuesto;

    IF v_estado IS NOT NULL AND v_estado != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar líneas de una versión que no está en estado borrador.';
    END IF;
END//
DELIMITER ;

-- =============================================================================
-- FIN DE MIGRACIÓN
-- =============================================================================
