<?php
/**
 * Migration Runner - Sistema de Pagos
 * Ejecuta todas las migraciones de BD en orden
 *
 * Uso: php BD/migrations/run_migrations_pagos.php
 *
 * Fecha: 04 de marzo de 2026
 */

set_time_limit(120);
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ─── Conexión ─────────────────────────────────────────────
$config_file = __DIR__ . '/../../config/conexion.json';
if (!file_exists($config_file)) {
    die("ERROR: No se encuentra config/conexion.json\n");
}
$config = json_decode(file_get_contents($config_file), true);

try {
    $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
    $pdo = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]);
    $pdo->exec("SET time_zone = 'Europe/Madrid'");
    echo "✅ Conexión establecida con {$config['database']} en {$config['host']}:{$config['port']}\n\n";
} catch (PDOException $e) {
    die("ERROR de conexión: " . $e->getMessage() . "\n");
}

// ─── Helper ────────────────────────────────────────────────
function run_sql_file(PDO $pdo, string $label, string $file): void
{
    echo "▶ {$label}\n";
    echo "  Archivo: {$file}\n";
    if (!file_exists($file)) {
        echo "  ⚠️  Archivo no encontrado, saltando.\n\n";
        return;
    }
    $sql = file_get_contents($file);
    // Eliminar comentarios de línea y dividir por ;
    $statements = array_filter(
        array_map('trim', explode(';', $sql)),
        fn($s) => strlen($s) > 5
    );
    $ok = 0;
    foreach ($statements as $stmt) {
        try {
            $pdo->exec($stmt);
            $ok++;
        } catch (PDOException $e) {
            // Ignorar "already exists" para índices y columnas duplicadas
            $msg = $e->getMessage();
            if (
                str_contains($msg, 'Duplicate column name') ||
                str_contains($msg, 'Duplicate key name')    ||
                str_contains($msg, 'already exists')        ||
                str_contains($msg, "Table '{$config['database']}")
            ) {
                echo "  ⚠️  Ya existe (saltando): " . substr($msg, 0, 100) . "\n";
            } else {
                echo "  ❌ ERROR: {$msg}\n";
                echo "  SQL: " . substr($stmt, 0, 300) . "\n\n";
                throw $e;
            }
        }
    }
    echo "  ✅ {$ok} sentencia(s) ejecutada(s)\n\n";
}

function run_procedure(PDO $pdo, string $name, string $sql): void
{
    echo "▶ Stored Procedure: {$name}\n";
    try {
        $pdo->exec("DROP PROCEDURE IF EXISTS `{$name}`");
        $pdo->exec($sql);
        echo "  ✅ Creado correctamente\n\n";
    } catch (PDOException $e) {
        echo "  ❌ ERROR: " . $e->getMessage() . "\n\n";
        throw $e;
    }
}

$base = __DIR__;

// ══════════════════════════════════════════════════════════
// MIGRATION 01: ALTER TABLE empresa (campos proforma)
// ══════════════════════════════════════════════════════════
run_sql_file($pdo, 'M01 — ALTER TABLE empresa (serie/numero proforma)',
    "{$base}/20260304_01_alter_empresa_proforma.sql");

// ══════════════════════════════════════════════════════════
// MIGRATION 02: CREATE TABLE documento_presupuesto
// ══════════════════════════════════════════════════════════
run_sql_file($pdo, 'M02 — CREATE TABLE documento_presupuesto',
    "{$base}/20260304_02_create_documento_presupuesto.sql");

// ══════════════════════════════════════════════════════════
// MIGRATION 03: CREATE TABLE pago_presupuesto
// ══════════════════════════════════════════════════════════
run_sql_file($pdo, 'M03 — CREATE TABLE pago_presupuesto',
    "{$base}/20260304_03_create_pago_presupuesto.sql");

// ══════════════════════════════════════════════════════════
// MIGRATION 04: STORED PROCEDURES (inline — requieren bloque BEGIN/END)
// ══════════════════════════════════════════════════════════

run_procedure($pdo, 'sp_obtener_siguiente_numero', <<<'SQL'
CREATE PROCEDURE sp_obtener_siguiente_numero(
    IN  p_codigo_empresa VARCHAR(20),
    IN  p_tipo_documento ENUM('presupuesto','factura','factura_proforma','abono'),
    OUT p_numero_completo VARCHAR(50)
)
BEGIN
    DECLARE v_serie       VARCHAR(10);
    DECLARE v_numero_actual INT;
    DECLARE v_anio        VARCHAR(4);

    SET v_anio = YEAR(CURDATE());

    IF p_tipo_documento = 'presupuesto' THEN
        SELECT serie_presupuesto_empresa,
               numero_actual_presupuesto_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa AND activo_empresa = TRUE;

        SET p_numero_completo = CONCAT(v_serie, v_anio, '-', LPAD(v_numero_actual, 4, '0'));

    ELSEIF p_tipo_documento = 'factura' THEN
        SELECT serie_factura_empresa,
               numero_actual_factura_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa AND activo_empresa = TRUE;

        SET p_numero_completo = CONCAT(v_serie, v_anio, '/', LPAD(v_numero_actual, 4, '0'));

    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        SELECT serie_factura_proforma_empresa,
               numero_actual_factura_proforma_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa AND activo_empresa = TRUE;

        -- Formato: FP2024/0001
        SET p_numero_completo = CONCAT(v_serie, v_anio, '/', LPAD(v_numero_actual, 4, '0'));

    ELSEIF p_tipo_documento = 'abono' THEN
        SELECT serie_abono_empresa,
               numero_actual_abono_empresa + 1
        INTO   v_serie, v_numero_actual
        FROM   empresa
        WHERE  codigo_empresa = p_codigo_empresa AND activo_empresa = TRUE;

        -- Formato: R2024/0001
        SET p_numero_completo = CONCAT(v_serie, v_anio, '/', LPAD(v_numero_actual, 4, '0'));

    END IF;
END
SQL);

run_procedure($pdo, 'sp_actualizar_contador_empresa', <<<'SQL'
CREATE PROCEDURE sp_actualizar_contador_empresa(
    IN p_id_empresa      INT UNSIGNED,
    IN p_tipo_documento  ENUM('presupuesto','factura','factura_proforma','abono')
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

    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        UPDATE empresa
        SET    numero_actual_factura_proforma_empresa = numero_actual_factura_proforma_empresa + 1
        WHERE  id_empresa = p_id_empresa;

    ELSEIF p_tipo_documento = 'abono' THEN
        UPDATE empresa
        SET    numero_actual_abono_empresa = numero_actual_abono_empresa + 1
        WHERE  id_empresa = p_id_empresa;

    END IF;
END
SQL);

// ══════════════════════════════════════════════════════════
// MIGRATION 05: VISTAS
// ══════════════════════════════════════════════════════════
run_sql_file($pdo, 'M05 — VISTAS v_documentos_presupuesto + v_pagos_presupuesto',
    "{$base}/20260304_05_create_vistas_pagos.sql");

// ══════════════════════════════════════════════════════════
// MIGRATION 06: Eliminar filtro activo de vista_presupuesto_completa
// Los presupuestos desactivados deben permanecer visibles en el DataTable
// con la columna "Activo" mostrando ✗ en lugar de desaparecer.
// ══════════════════════════════════════════════════════════
run_sql_file($pdo, 'M06 — ALTER VIEW vista_presupuesto_completa (sin filtro activo_presupuesto)',
    "{$base}/20260304_06_alter_vista_presupuesto_completa_sin_filtro_activo.sql");

// ══════════════════════════════════════════════════════════
// VERIFICACIÓN FINAL
// ══════════════════════════════════════════════════════════
echo "═══════════════════════════════════════════════\n";
echo "VERIFICACIÓN FINAL\n";
echo "═══════════════════════════════════════════════\n\n";

// Columnas proforma en empresa
$row = $pdo->query("SELECT serie_factura_proforma_empresa, numero_actual_factura_proforma_empresa FROM empresa LIMIT 1")->fetch();
echo "✅ empresa.serie_factura_proforma_empresa    = '{$row['serie_factura_proforma_empresa']}'\n";
echo "✅ empresa.numero_actual_factura_proforma_empresa = {$row['numero_actual_factura_proforma_empresa']}\n\n";

// Tablas creadas
foreach (['documento_presupuesto', 'pago_presupuesto'] as $tabla) {
    $exists = $pdo->query("SHOW TABLES LIKE '{$tabla}'")->rowCount() > 0;
    echo ($exists ? '✅' : '❌') . " Tabla {$tabla}\n";
}
echo "\n";

// Stored procedures
foreach (['sp_obtener_siguiente_numero', 'sp_actualizar_contador_empresa'] as $sp) {
    $exists = $pdo->query("SELECT ROUTINE_NAME FROM information_schema.ROUTINES WHERE ROUTINE_SCHEMA='toldos_db' AND ROUTINE_NAME='{$sp}'")->rowCount() > 0;
    echo ($exists ? '✅' : '❌') . " SP {$sp}\n";
}
echo "\n";

// Vistas
foreach (['v_documentos_presupuesto', 'v_pagos_presupuesto'] as $vista) {
    $exists = $pdo->query("SHOW TABLES LIKE '{$vista}'")->rowCount() > 0;
    echo ($exists ? '✅' : '❌') . " Vista {$vista}\n";
}

echo "\n✅ Migraciones completadas.\n";
