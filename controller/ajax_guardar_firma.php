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

try {
    // Obtener id_usuario de sesión
    $id_usuario = $_SESSION['id_usuario'] ?? null;
    
    if (empty($id_usuario)) {
        echo json_encode([
            'success' => false,
            'message' => 'No se pudo identificar al usuario'
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    // Verificar que el usuario tiene un comercial asociado
    $comercial = $comercialesModel->get_comercial_by_usuario($id_usuario);
    
    if (!$comercial) {
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

    // Obtener la firma en base64 del POST
    $firma_base64 = $_POST['firma_base64'] ?? '';
    
    // Validar que venga la firma
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
    $resultado = $comercialesModel->update_firma_by_usuario($id_usuario, $firma_base64);
    
    if ($resultado) {
        // Log de éxito
        $registro->registrarActividad(
            $_SESSION['email'] ?? 'unknown',
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
            $_SESSION['email'] ?? 'unknown',
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
