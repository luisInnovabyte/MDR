<?php
/**
 * Endpoint AJAX para guardar la firma digital del comercial
 * Punto 14: Nueva Funcionalidad - Firma de Empleado
 * 
 * Recibe: POST con base64 de imagen PNG
 * Valida: Sesión activa, usuario es comercial, formato válido
 * Actualiza: comerciales.firma_comercial
 */

session_start();

// Headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Validar sesión activa
if (!isset($_SESSION['sesion_iniciada']) || !$_SESSION['sesion_iniciada']) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida. Por favor, inicie sesión.'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Validar que venga por POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'success' => false,
        'message' => 'Método no permitido'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

require_once '../config/conexion.php';
require_once '../config/funciones.php';
require_once '../models/Comerciales.php';

$registro = new RegistroActividad();
$comercialesModel = new Comerciales();

// Sistema de debug - Log en archivo accesible
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

debug_log("=== INICIO ajax_guardar_firma.php ===");
debug_log("Usuario en sesión: " . ($_SESSION['email'] ?? 'NO DISPONIBLE'));
debug_log("ID Usuario: " . ($_SESSION['id_usuario'] ?? 'NO DISPONIBLE'));

try {
    // Verificar si viene un id_comercial específico (desde admin)
    // o usar el id_usuario de la sesión (desde perfil)
    $id_comercial_param = $_POST['id_comercial'] ?? null;
    debug_log("ID Comercial recibido: " . ($id_comercial_param ?? 'NULL'));
    
    if (!empty($id_comercial_param)) {
        // Modo admin: buscar comercial por id_comercial
        $comercial = $comercialesModel->get_comercialxid($id_comercial_param);
        
        if (!$comercial) {
            echo json_encode([
                'success' => false,
                'message' => 'Comercial no encontrado'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        $id_usuario = $comercial['id_usuario'];
        $usuario_email = $_SESSION['email'] ?? 'admin';
        
    } else {
        // Modo usuario: usar sesión actual
        $id_usuario = $_SESSION['id_usuario'] ?? null;
        
        if (empty($id_usuario)) {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo identificar al usuario'
            ], JSON_UNESCAPED_UNICODE);
            exit;
        }
        
        // Verificar que el usuario tiene un comercial asociado
        debug_log("Buscando comercial para usuario ID: $id_usuario");
        $comercial = $comercialesModel->get_comercial_by_usuario($id_usuario);
        debug_log("Comercial encontrado: " . ($comercial ? 'SÍ' : 'NO'));
        
        if (!$comercial) {
            debug_log("ERROR: Usuario no tiene comercial asociado", 'ERROR');
            echo json_encode([
                'success' => false,
                'message' => 'El usuario no tiene un perfil de comercial asociado'
            ], JSON_UNESCAPED_UNICODE);
            
            $registro->registrarActividad(
                $_SESSION['email'] ?? 'unknown',
                'ajax_guardar_firma',
                'guardar_firma',
                "Usuario {$id_usuario} intentó guardar firma sin ser comercial",
                'warning'
            );
            
            exit;
        }
        
        $usuario_email = $_SESSION['email'] ?? 'unknown';
    }

    // Obtener la firma en base64 del POST
    $firma_base64 = $_POST['firma_base64'] ?? '';
    
    // Si la firma viene como 'null' (string), tratarla como vacía (para eliminar)
    if ($firma_base64 === 'null' || $firma_base64 === null) {
        $firma_base64 = null;
    }
    
    // Validar que venga la firma (excepto si es null para eliminar)
    if ($firma_base64 === null) {
        // Eliminar firma
        $resultado = $comercialesModel->update_firma_by_usuario($id_usuario, null);
        
        if ($resultado) {
            $registro->registrarActividad(
                $usuario_email,
                'ajax_guardar_firma',
                'eliminar_firma',
                "Firma eliminada para comercial: {$comercial['nombre']} {$comercial['apellidos']} (ID: {$comercial['id_comercial']})",
                'info'
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Firma eliminada correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo eliminar la firma'
            ], JSON_UNESCAPED_UNICODE);
        }
        exit;
    }
    
    if (empty($firma_base64)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se recibió la firma'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validar formato base64 PNG
    if (!preg_match('/^data:image\/png;base64,/', $firma_base64)) {
        echo json_encode([
            'success' => false,
            'message' => 'Formato de firma inválido. Debe ser PNG en base64'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Validar tamaño aproximado (máximo ~500KB en base64)
    if (strlen($firma_base64) > 700000) { // ~500KB en base64
        echo json_encode([
            'success' => false,
            'message' => 'La firma es demasiado grande. Intente con una imagen más simple.'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Actualizar la firma en la base de datos
    debug_log("Actualizando firma para usuario: $id_usuario");
    debug_log("Tamaño firma base64: " . strlen($firma_base64) . " bytes");
    $resultado = $comercialesModel->update_firma_by_usuario($id_usuario, $firma_base64);
    debug_log("Resultado actualización: " . ($resultado ? 'EXITOSO' : 'FALLIDO'), $resultado ? 'INFO' : 'ERROR');
    
    if ($resultado) {
        // Log de éxito
        $registro->registrarActividad(
            $usuario_email,
            'ajax_guardar_firma',
            'guardar_firma',
            "Firma guardada exitosamente para comercial: {$comercial['nombre']} {$comercial['apellidos']} (ID: {$comercial['id_comercial']})",
            'info'
        );
        
        echo json_encode([
            'success' => true,
            'message' => 'Firma guardada correctamente',
            'comercial' => [
                'nombre' => $comercial['nombre'],
                'apellidos' => $comercial['apellidos']
            ]
        ], JSON_UNESCAPED_UNICODE);
    } else {
        // Log de advertencia
        $registro->registrarActividad(
            $usuario_email,
            'ajax_guardar_firma',
            'guardar_firma',
            "No se pudo actualizar la firma para usuario {$id_usuario}",
            'warning'
        );
        
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo guardar la firma. Intente nuevamente.'
        ], JSON_UNESCAPED_UNICODE);
    }

} catch (Exception $e) {
    // Log de error
    $registro->registrarActividad(
        $_SESSION['email'] ?? 'unknown',
        'ajax_guardar_firma',
        'guardar_firma',
        "Error al guardar firma: " . $e->getMessage(),
        'error'
    );
    
    echo json_encode([
        'success' => false,
        'message' => 'Ocurrió un error al procesar la solicitud'
    ], JSON_UNESCAPED_UNICODE);
}
