<?php
/**
 * Endpoint AJAX para obtener la firma digital del comercial
 * Punto 14: Nueva Funcionalidad - Firma de Empleado
 * 
 * Retorna: JSON con firma en base64 si existe
 */

session_start();

// Sistema de debug
function debug_log($mensaje, $tipo = 'INFO') {
    $log_dir = __DIR__ . '/../public/logs/';
    if (!file_exists($log_dir)) {
        mkdir($log_dir, 0777, true);
    }
    $log_file = $log_dir . 'firma_debug_' . date('Y-m-d') . '.log';
    $timestamp = date('Y-m-d H:i:s');
    $log_msg = "[$timestamp] [$tipo] $mensaje" . PHP_EOL;
    file_put_contents($log_file, $log_msg, FILE_APPEND);
}

debug_log("=== INICIO ajax_obtener_firma.php ===");

// Headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Validar sesión activa
if (!isset($_SESSION['sesion_iniciada']) || !$_SESSION['sesion_iniciada']) {
    debug_log("ERROR: Sesión no válida", 'ERROR');
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../config/conexion.php';
require_once '../models/Comerciales.php';

$comercialesModel = new Comerciales();

try {
    // Verificar si viene un id_comercial específico (desde admin)
    // o usar el id_usuario de la sesión (desde perfil)
    $id_comercial_param = $_GET['id_comercial'] ?? null;
    
    if (!empty($id_comercial_param)) {
        // Modo admin: buscar firma por id_comercial
        $comercial = $comercialesModel->get_comercialxid($id_comercial_param);
        
        if (!$comercial) {
            echo json_encode([
                'success' => false,
                'message' => 'Comercial no encontrado'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $id_usuario = $comercial['id_usuario'];
        
    } else {
        // Modo usuario: usar sesión actual
        $id_usuario = $_SESSION['id_usuario'] ?? null;
        debug_log("Modo usuario - ID Usuario: " . ($id_usuario ?? 'NULL'));
        
        if (empty($id_usuario)) {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo identificar al usuario'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Verificar que el usuario tiene un comercial asociado
        debug_log("Verificando comercial para usuario ID: $id_usuario");
        $comercial = $comercialesModel->get_comercial_by_usuario($id_usuario);
        debug_log("Comercial encontrado: " . ($comercial ? 'SÍ' : 'NO'));
        
        if (!$comercial) {
            debug_log("ERROR: Usuario sin comercial asociado", 'ERROR');
            echo json_encode([
                'success' => false,
                'message' => 'El usuario no tiene un perfil de comercial asociado'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
    }

    // Obtener la firma por id_usuario
    debug_log("Obteniendo firma para usuario ID: $id_usuario");
    $firma = $comercialesModel->get_firma_by_usuario($id_usuario);
    debug_log("Firma obtenida: " . (!empty($firma) ? 'SÍ (' . strlen($firma) . ' bytes)' : 'NO'));
    
    echo json_encode([
        'success' => true,
        'tiene_firma' => !empty($firma),
        'firma_base64' => $firma,
        'comercial' => [
            'nombre' => $comercial['nombre'],
            'apellidos' => $comercial['apellidos']
        ]
    ], JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Ocurrió un error al obtener la firma'
    ], JSON_UNESCAPED_UNICODE);
}
