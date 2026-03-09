<?php
/**
 * Script temporal de migración — ELIMINAR TRAS EJECUTAR
 * Recrea sp_obtener_siguiente_numero y sp_actualizar_contador_empresa
 * con el nuevo formato: {serie}-{num}/{anio}  (ej: FE-0003/2026)
 */

require_once __DIR__ . '/config/conexion.php';

$pdo = (new Conexion())->getConexion();
$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$steps = [];
$errors = [];

// ─── SP 1: sp_obtener_siguiente_numero ───────────────────────

$steps[] = "DROP sp_obtener_siguiente_numero";
$pdo->exec("DROP PROCEDURE IF EXISTS sp_obtener_siguiente_numero");

$steps[] = "CREATE sp_obtener_siguiente_numero";
$pdo->exec("
CREATE PROCEDURE `sp_obtener_siguiente_numero` (
    IN  `p_codigo_empresa`  VARCHAR(20),
    IN  `p_tipo_documento`  ENUM('presupuesto','factura','abono','factura_proforma'),
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
        WHERE  codigo_empresa = p_codigo_empresa AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    ELSEIF p_tipo_documento = 'factura' THEN
        SELECT serie_factura_empresa, numero_actual_factura_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    ELSEIF p_tipo_documento = 'abono' THEN
        SELECT serie_abono_empresa, numero_actual_abono_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        SELECT serie_factura_proforma_empresa, numero_actual_factura_proforma_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);

    END IF;
END
");

// ─── SP 2: sp_actualizar_contador_empresa ────────────────────

$steps[] = "DROP sp_actualizar_contador_empresa";
$pdo->exec("DROP PROCEDURE IF EXISTS sp_actualizar_contador_empresa");

$steps[] = "CREATE sp_actualizar_contador_empresa";
$pdo->exec("
CREATE PROCEDURE `sp_actualizar_contador_empresa` (
    IN `p_id_empresa`     INT UNSIGNED,
    IN `p_tipo_documento` ENUM('presupuesto','factura','abono','factura_proforma')
)
BEGIN
    IF p_tipo_documento = 'presupuesto' THEN
        UPDATE empresa SET numero_actual_presupuesto_empresa = numero_actual_presupuesto_empresa + 1
        WHERE id_empresa = p_id_empresa;
    ELSEIF p_tipo_documento = 'factura' THEN
        UPDATE empresa SET numero_actual_factura_empresa = numero_actual_factura_empresa + 1
        WHERE id_empresa = p_id_empresa;
    ELSEIF p_tipo_documento = 'abono' THEN
        UPDATE empresa SET numero_actual_abono_empresa = numero_actual_abono_empresa + 1
        WHERE id_empresa = p_id_empresa;
    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        UPDATE empresa SET numero_actual_factura_proforma_empresa = numero_actual_factura_proforma_empresa + 1
        WHERE id_empresa = p_id_empresa;
    END IF;
END
");

// ─── Verificación ─────────────────────────────────────────────

$steps[] = "Verificar sp_obtener_siguiente_numero con factura (MDR02)";
$pdo->exec("CALL sp_obtener_siguiente_numero('MDR02', 'factura', @n)");
$row = $pdo->query("SELECT @n AS numero")->fetch(PDO::FETCH_ASSOC);
$steps[] = "  → Resultado: " . ($row['numero'] ?? 'NULL');

$pdo->exec("CALL sp_obtener_siguiente_numero('MDR02', 'factura_proforma', @n)");
$row = $pdo->query("SELECT @n AS numero")->fetch(PDO::FETCH_ASSOC);
$steps[] = "Verificar sp_obtener_siguiente_numero con factura_proforma (MDR02)";
$steps[] = "  → Resultado: " . ($row['numero'] ?? 'NULL');

// ─── Output ───────────────────────────────────────────────────

header('Content-Type: text/plain; charset=utf-8');
echo "=== MIGRACIÓN 20260307_01 ===\n\n";
foreach ($steps as $s) {
    echo "✅ $s\n";
}
echo "\n✅ COMPLETADO — Puedes eliminar este archivo.\n";
