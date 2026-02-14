<?php
/**
 * Endpoint AJAX para obtener la firma digital del comercial
 * Punto 14: Nueva Funcionalidad - Firma de Empleado
 * 
 * Retorna: JSON con firma en base64 si existe
 */

session_start();

// Headers para JSON
header('Content-Type: application/json; charset=utf-8');

// Validar sesión activa
if (!isset($_SESSION['sesion_iniciada']) || !$_SESSION['sesion_iniciada']) {
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
        exit;
    }

    // Obtener la firma
    $firma = $comercialesModel->get_firma_by_usuario($id_usuario);
    
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
