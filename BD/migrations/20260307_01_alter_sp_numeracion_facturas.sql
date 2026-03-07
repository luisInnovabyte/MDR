-- ============================================================
-- Migration: 20260307_01_alter_sp_numeracion_facturas.sql
-- Descripción: Cambiar formato de numeración de facturas y abonos
--              ANTES: FE2026/0003
--              AHORA: FE-0003/2026  (mismo patrón que presupuestos)
--              Además añade soporte 'factura_proforma' al ENUM
--              de ambos SPs (antes solo en PHP, ahora también en BD)
-- Ejecutar en: toldos_db
-- ============================================================

-- ─── 1. sp_obtener_siguiente_numero ─────────────────────────

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


-- ─── 2. sp_actualizar_contador_empresa ──────────────────────

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

    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        UPDATE empresa
        SET    numero_actual_factura_proforma_empresa = numero_actual_factura_proforma_empresa + 1
        WHERE  id_empresa = p_id_empresa;

    END IF;
END$$

DELIMITER ;


-- ─── Verificación post-migración ────────────────────────────
-- Probar con empresa MDR02:
-- CALL sp_obtener_siguiente_numero('MDR02', 'factura',         @n); SELECT @n;
-- CALL sp_obtener_siguiente_numero('MDR02', 'abono',           @n); SELECT @n;
-- CALL sp_obtener_siguiente_numero('MDR02', 'factura_proforma',@n); SELECT @n;
-- Resultado esperado: FE-0003/2026 · R-0001/2026 · FP-0001/2026
