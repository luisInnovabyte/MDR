<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";

header('Content-Type: application/json');

$registro = new RegistroActividad();
$accion   = $_POST['accion'] ?? '';

if (!in_array($accion, ['crear', 'limpiar'])) {
    echo json_encode(['success' => false, 'message' => 'Acción no válida.', 'log' => '']);
    exit;
}

$scriptPath = __DIR__ . '/../BD/seeder_presupuestos_test.php';

if (!file_exists($scriptPath)) {
    echo json_encode(['success' => false, 'message' => 'Script seeder no encontrado.', 'log' => '']);
    exit;
}

// Construir comando PHP con argumento si es limpiar
$phpBin = PHP_BINARY ?: 'php';
$args   = ($accion === 'limpiar') ? ' --clean' : '';
$cmd    = escapeshellcmd($phpBin) . ' ' . escapeshellarg($scriptPath) . $args . ' 2>&1';

$output     = [];
$returnCode = 0;
exec($cmd, $output, $returnCode);

$log     = implode("\n", $output);
$success = ($returnCode === 0);

$mensaje = $success
    ? ($accion === 'crear' ? '4 presupuestos de prueba creados correctamente.' : 'Datos de prueba eliminados correctamente.')
    : 'El script terminó con errores. Revisa el log.';

$registro->registrarActividad(
    'admin',
    'seeder_tmp.php',
    $accion,
    $success ? $mensaje : "Error (código $returnCode): " . substr($log, -300),
    $success ? 'info' : 'error'
);

echo json_encode([
    'success' => $success,
    'message' => $mensaje,
    'log'     => $log,
], JSON_UNESCAPED_UNICODE);
