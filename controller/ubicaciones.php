<?php
require_once "../config/conexion.php";
require_once "../models/Ubicaciones.php";
require_once '../config/funciones.php';

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/ubicaciones_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] ubicaciones.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    
    $logDirs = [
        __DIR__ . "/../public/logs/",
        "W:/TOLDOS_AMPLIADO/public/logs/",
        sys_get_temp_dir() . "/toldos_logs/"
    ];
    
    foreach ($logDirs as $logDir) {
        try {
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . "ubicaciones_debug_" . date("Ymd") . ".txt";
            
            $result = file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
            
            if ($result !== false) {
                break;
            }
        } catch (Exception $e) {
            continue;
        }
    }
}

$registro = new RegistroActividad();
$ubicaciones = new Ubicaciones();

switch ($_GET["op"]) {

    case "listar":
        // Verificar si se proporciona un ID de cliente para filtrar
        if (isset($_GET["id_cliente"]) && !empty($_GET["id_cliente"])) {
            $datos = $ubicaciones->get_ubicaciones_by_cliente($_GET["id_cliente"]);
        } else {
            $datos = $ubicaciones->get_ubicaciones_cliente();
        }
        
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_ubicacion" => $row["id_ubicacion"],
                "id_cliente" => $row["id_cliente"],
                "nombre_cliente" => $row["nombre_cliente"] ?? '',
                "nombre_ubicacion" => $row["nombre_ubicacion"],
                "direccion_ubicacion" => $row["direccion_ubicacion"],
                "codigo_postal_ubicacion" => $row["codigo_postal_ubicacion"],
                "poblacion_ubicacion" => $row["poblacion_ubicacion"],
                "provincia_ubicacion" => $row["provincia_ubicacion"],
                "pais_ubicacion" => $row["pais_ubicacion"],
                "persona_contacto_ubicacion" => $row["persona_contacto_ubicacion"],
                "telefono_contacto_ubicacion" => $row["telefono_contacto_ubicacion"],
                "email_contacto_ubicacion" => $row["email_contacto_ubicacion"],
                "observaciones_ubicacion" => $row["observaciones_ubicacion"],
                "es_principal_ubicacion" => $row["es_principal_ubicacion"],
                "activo_ubicacion" => $row["activo_ubicacion"],
                "created_at_ubicacion" => $row["created_at_ubicacion"] ?? '',
                "updated_at_ubicacion" => $row["updated_at_ubicacion"] ?? ''
            );
        }

        $results = array(
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;

    case "guardaryeditar":
        writeToLog(['action' => 'guardaryeditar_iniciado', 'timestamp' => date('Y-m-d H:i:s')]);
        
        try {
            $id_cliente = $_POST["id_cliente"] ?? '';
            $nombre_ubicacion = $_POST["nombre_ubicacion"] ?? '';
            $direccion_ubicacion = $_POST["direccion_ubicacion"] ?? '';
            $codigo_postal_ubicacion = $_POST["codigo_postal_ubicacion"] ?? '';
            $poblacion_ubicacion = $_POST["poblacion_ubicacion"] ?? '';
            $provincia_ubicacion = $_POST["provincia_ubicacion"] ?? '';
            $pais_ubicacion = $_POST["pais_ubicacion"] ?? 'España';
            $persona_contacto_ubicacion = $_POST["persona_contacto_ubicacion"] ?? '';
            $telefono_contacto_ubicacion = $_POST["telefono_contacto_ubicacion"] ?? '';
            $email_contacto_ubicacion = $_POST["email_contacto_ubicacion"] ?? '';
            $observaciones_ubicacion = $_POST["observaciones_ubicacion"] ?? '';
            $es_principal_ubicacion = isset($_POST["es_principal_ubicacion"]) ? (int)$_POST["es_principal_ubicacion"] : 0;
            
            writeToLog([
                'action' => 'guardaryeditar_inicio',
                'post_info' => $_POST
            ]);
            
            if (empty($_POST["id_ubicacion"])) {
                // Insertar nueva ubicación
                $resultado = $ubicaciones->insert_ubicacion(
                    $id_cliente,
                    $nombre_ubicacion,
                    $direccion_ubicacion,
                    $codigo_postal_ubicacion,
                    $poblacion_ubicacion,
                    $provincia_ubicacion,
                    $pais_ubicacion,
                    $persona_contacto_ubicacion,
                    $telefono_contacto_ubicacion,
                    $email_contacto_ubicacion,
                    $observaciones_ubicacion,
                    $es_principal_ubicacion
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'ubicaciones.php',
                        'Guardar ubicación',
                        "Ubicación guardada exitosamente",
                        "info"
                    );

                    echo json_encode([
                        "success" => true,
                        "message" => "Ubicación insertada correctamente",
                        "id_ubicacion" => $resultado
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al insertar la ubicación"
                    ]);
                }

            } else {
                // Actualizar ubicación existente
                $resultado = $ubicaciones->update_ubicacion(
                    $_POST["id_ubicacion"],
                    $id_cliente,
                    $nombre_ubicacion,
                    $direccion_ubicacion,
                    $codigo_postal_ubicacion,
                    $poblacion_ubicacion,
                    $provincia_ubicacion,
                    $pais_ubicacion,
                    $persona_contacto_ubicacion,
                    $telefono_contacto_ubicacion,
                    $email_contacto_ubicacion,
                    $observaciones_ubicacion,
                    $es_principal_ubicacion
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'ubicaciones.php',
                        'Actualizar ubicación',
                        "Ubicación actualizada exitosamente",
                        "info"
                    );

                    echo json_encode([
                        "success" => true,
                        "message" => "Ubicación actualizada correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al actualizar la ubicación"
                    ]);
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Excepción: " . $e->getMessage()
            ]);
        }
        break;

    case "mostrar": 
        header('Content-Type: application/json; charset=utf-8');
        $datos = $ubicaciones->get_ubicacionxid($_POST["id_ubicacion"]);
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener la ubicación solicitada"
            ]);
        }
        break;

    case "eliminar":
        $resultado = $ubicaciones->desactivar_ubicacionxid($_POST["id_ubicacion"]);
        echo json_encode([
            "success" => $resultado,
            "message" => $resultado ? "Ubicación desactivada correctamente" : "Error al desactivar la ubicación"
        ]);
        break;

    case "activar":
        $resultado = $ubicaciones->activar_ubicacionxid($_POST["id_ubicacion"]);
        echo json_encode([
            "success" => $resultado,
            "message" => $resultado ? "Ubicación activada correctamente" : "Error al activar la ubicación"
        ]);
        break;

    case "verificarUbicacion":
        $nombreUbicacion = isset($_POST["nombre_ubicacion"]) ? trim($_POST["nombre_ubicacion"]) : 
                         (isset($_GET["nombre_ubicacion"]) ? trim($_GET["nombre_ubicacion"]) : '');
        
        $idCliente = isset($_POST["id_cliente"]) ? trim($_POST["id_cliente"]) : 
                    (isset($_GET["id_cliente"]) ? trim($_GET["id_cliente"]) : '');

        $idUbicacion = isset($_POST["id_ubicacion"]) ? trim($_POST["id_ubicacion"]) : 
                           (isset($_GET["id_ubicacion"]) ? trim($_GET["id_ubicacion"]) : null);

        writeToLog([
            'action' => 'verificarUbicacion',
            'nombre_ubicacion' => $nombreUbicacion,
            'id_cliente' => $idCliente,
            'id_ubicacion' => $idUbicacion,
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST,
            'get_data' => $_GET
        ]);

        if (empty($nombreUbicacion) || empty($idCliente)) {
            $registro->registrarActividad(
                'admin',
                'ubicaciones.php',
                'Verificar ubicación',
                'Intento de verificación con campos obligatorios vacíos',
                'warning'
            );
        
            echo json_encode([
                "success" => false,
                "message" => "El nombre de la ubicación y el cliente son obligatorios."
            ]);
            break;
        }
        
        $resultado = $ubicaciones->verificarUbicacion($nombreUbicacion, $idCliente, $idUbicacion);
        
        if (isset($resultado['error'])) {
            $registro->registrarActividad(
                'admin',
                'ubicaciones.php',
                'Verificar ubicación',
                'Error al verificar ubicación: ' . $resultado['error'],
                'error'
            );
            
            echo json_encode([
                "success" => false,
                "message" => "Error al verificar la ubicación: " . $resultado['error']
            ]);
            break;
        }
        
        $mensajeActividad = $resultado['existe'] ? 
            'Ubicación duplicada detectada: ' . $nombreUbicacion : 
            'Ubicación disponible: ' . $nombreUbicacion;
        
        $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
        
        $registro->registrarActividad(
            'admin',
            'ubicaciones.php',
            'Verificar ubicación',
            $mensajeActividad,
            $tipoActividad
        );
        
        echo json_encode([
            "success" => true,
            "existe" => $resultado['existe']
        ]);
        break;

    case "selectByCliente":
        $idCliente = isset($_POST["id_cliente"]) ? trim($_POST["id_cliente"]) : 
                    (isset($_GET["id_cliente"]) ? trim($_GET["id_cliente"]) : '');

        writeToLog([
            'action' => 'selectByCliente',
            'id_cliente' => $idCliente,
            'method' => $_SERVER['REQUEST_METHOD']
        ]);

        if (empty($idCliente)) {
            echo json_encode([
                "success" => false,
                "message" => "El ID del cliente es obligatorio.",
                "data" => []
            ]);
            break;
        }

        $ubicaciones_lista = $ubicaciones->get_ubicaciones_by_cliente($idCliente);
        
        $data = array();
        foreach ($ubicaciones_lista as $row) {
            // Solo devolver ubicaciones activas
            if ($row["activo_ubicacion"] == 1) {
                $data[] = array(
                    "id_ubicacion" => $row["id_ubicacion"],
                    "nombre_ubicacion" => $row["nombre_ubicacion"],
                    "direccion_ubicacion" => $row["direccion_ubicacion"] ?? '',
                    "codigo_postal_ubicacion" => $row["codigo_postal_ubicacion"] ?? '',
                    "poblacion_ubicacion" => $row["poblacion_ubicacion"] ?? '',
                    "provincia_ubicacion" => $row["provincia_ubicacion"] ?? '',
                    "pais_ubicacion" => $row["pais_ubicacion"] ?? 'España',
                    "persona_contacto_ubicacion" => $row["persona_contacto_ubicacion"] ?? '',
                    "telefono_contacto_ubicacion" => $row["telefono_contacto_ubicacion"] ?? '',
                    "email_contacto_ubicacion" => $row["email_contacto_ubicacion"] ?? '',
                    "es_principal_ubicacion" => $row["es_principal_ubicacion"]
                );
            }
        }

        $registro->registrarActividad(
            'admin',
            'ubicaciones.php',
            'Listar ubicaciones por cliente',
            'Se listaron ' . count($data) . ' ubicaciones del cliente ID: ' . $idCliente,
            'info'
        );

        echo json_encode([
            "success" => true,
            "data" => $data
        ]);
        break;
}
