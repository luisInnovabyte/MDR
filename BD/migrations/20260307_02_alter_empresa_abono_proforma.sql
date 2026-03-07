-- ============================================================
-- Migration: 20260307_02_alter_empresa_abono_proforma.sql
-- Descripción: Añadir serie y contador para abonos de factura
--              proforma (serie_abono_factura_proforma_empresa y
--              numero_actual_abono_factura_proforma_empresa) +
--              Añadir rama 'abono_proforma' a ambos SPs de numeración.
-- Ejecutar en: toldos_db
-- ============================================================

-- ─── 1. Nuevas columnas en tabla empresa ────────────────────

ALTER TABLE empresa
    ADD COLUMN serie_abono_factura_proforma_empresa VARCHAR(10) DEFAULT 'RP'
        COMMENT 'Serie para abonos de facturas proforma'
        AFTER numero_actual_abono_empresa,
    ADD COLUMN numero_actual_abono_factura_proforma_empresa INT UNSIGNED DEFAULT 0
        COMMENT 'Contador de abonos de facturas proforma'
        AFTER serie_abono_factura_proforma_empresa;


-- ─── 2. sp_obtener_siguiente_numero (con rama abono_proforma) ─

DROP PROCEDURE IF EXISTS sp_obtener_siguiente_numero;

DELIMITER $$

CREATE PROCEDURE `sp_obtener_siguiente_numero` (
    IN  `p_codigo_empresa`  VARCHAR(20),
    IN  `p_tipo_documento`  VARCHAR(30),
    OUT `p_numero_completo` VARCHAR(50)
)
BEGIN
    DECLARE v_serie         VARCHAR(10);
    DECLARE v_numero_actual INT;
    DECLARE v_anio          VARCHAR(4);

    SET v_anio = YEAR(CURDATE());

    IF p_tipo_documento = 'presupuesto' THEN
        SELECT serie_presupuesto_empresa, numero_actual_presupuesto_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci
          AND  activo_empresa = TRUE;
        -- Formato: P-0001/2026
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    ELSEIF p_tipo_documento = 'factura' THEN
        SELECT serie_factura_empresa, numero_actual_factura_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci
          AND  activo_empresa = TRUE;
        -- Formato: FE-0003/2026
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    ELSEIF p_tipo_documento = 'abono' THEN
        SELECT serie_abono_empresa, numero_actual_abono_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci
          AND  activo_empresa = TRUE;
        -- Formato: R-0001/2026
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    ELSEIF p_tipo_documento = 'abono_proforma' THEN
        SELECT serie_abono_factura_proforma_empresa, numero_actual_abono_factura_proforma_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci
          AND  activo_empresa = TRUE;
        -- Formato: RP-0001/2026
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        SELECT serie_factura_proforma_empresa, numero_actual_factura_proforma_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci
          AND  activo_empresa = TRUE;
        -- Formato: FP-0001/2026
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    END IF;
END$$

DELIMITER ;


-- ─── 3. sp_actualizar_contador_empresa (con rama abono_proforma) ─

DROP PROCEDURE IF EXISTS sp_actualizar_contador_empresa;

DELIMITER $$

CREATE PROCEDURE `sp_actualizar_contador_empresa` (
    IN `p_id_empresa`     INT UNSIGNED,
    IN `p_tipo_documento` VARCHAR(30)
)
BEGIN
    IF p_tipo_documento = 'presupuesto' THEN
        UPDATE empresa
        SET    numero_actual_presupuesto_empresa = numero_actual_presupuesto_empresa + 1
        WHERE  id_empresa = p_id_empresa;

    ELSEIF p_tipo_documento = 'factura' THEN
        UPDATE empresa
        SET    numero_actual_factura_empresa = numero_actual_factura_empresa + 1
        WHERE  id_empresa = p_id_empresa;

    ELSEIF p_tipo_documento = 'abono' THEN
        UPDATE empresa
        SET    numero_actual_abono_empresa = numero_actual_abono_empresa + 1
        WHERE  id_empresa = p_id_empresa;

    ELSEIF p_tipo_documento = 'abono_proforma' THEN
        UPDATE empresa
        SET    numero_actual_abono_factura_proforma_empresa = numero_actual_abono_factura_proforma_empresa + 1
        WHERE  id_empresa = p_id_empresa;

    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        UPDATE empresa
        SET    numero_actual_factura_proforma_empresa = numero_actual_factura_proforma_empresa + 1
        WHERE  id_empresa = p_id_empresa;

    END IF;
END$$

DELIMITER ;


-- ─── Verificación post-migración ────────────────────────────
-- CALL sp_obtener_siguiente_numero('MDR02', 'abono',          @n); SELECT @n;  -- R-0001/2026
-- CALL sp_obtener_siguiente_numero('MDR02', 'abono_proforma', @n); SELECT @n;  -- RP-0001/2026
