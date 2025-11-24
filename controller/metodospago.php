<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/MetodosPago.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/metodospago_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] metodospago.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);


//CREATE TABLE metodo_pago (
//    id_metodo_pago INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_metodo_pago VARCHAR(20) NOT NULL UNIQUE,
//    nombre_metodo_pago VARCHAR(100) NOT NULL,
//    observaciones_metodo_pago TEXT,
//    activo_metodo_pago BOOLEAN DEFAULT TRUE,
//    created_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    INDEX idx_codigo_metodo_pago (codigo_metodo_pago)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    
    // Lista de directorios donde intentar escribir
    $logDirs = [
        __DIR__ . "/../public/logs/",
        "W:/TOLDOS_AMPLIADO/public/logs/",
        sys_get_temp_dir() . "/toldos_logs/"
    ];
    
    foreach ($logDirs as $logDir) {
        try {
            // Asegurar que el directorio existe
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . "metodospago_debug_" . date("Ymd") . ".txt";
            
            // Intentar escribir
            $result = file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
            
            if ($result !== false) {
                // Éxito, salir del bucle
                break;
            }
        } catch (Exception $e) {
            // Continuar con el siguiente directorio
            continue;
        }
    }
}




//CREATE TABLE metodo_pago (
//    id_metodo_pago INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_metodo_pago VARCHAR(20) NOT NULL UNIQUE,
//    nombre_metodo_pago VARCHAR(100) NOT NULL,
//    observaciones_metodo_pago TEXT,
//    activo_metodo_pago BOOLEAN DEFAULT TRUE,
//    created_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_metodo_pago TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    INDEX idx_codigo_metodo_pago (codigo_metodo_pago)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$metodospago = new MetodosPago();

switch ($_GET["op"]) {

    case "listar":
        $datos = $metodospago->get_metodo_pago();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_metodo_pago" => $row["id_metodo_pago"],
                "codigo_metodo_pago" => $row["codigo_metodo_pago"],
                "nombre_metodo_pago" => $row["nombre_metodo_pago"],
                "observaciones_metodo_pago" => $row["observaciones_metodo_pago"],
                "activo_metodo_pago" => $row["activo_metodo_pago"],
                "created_at_metodo_pago" => $row["created_at_metodo_pago"],
                "updated_at_metodo_pago" => $row["updated_at_metodo_pago"]
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

        case "listarDisponibles":
            $datos = $metodospago->get_metodo_pago_disponible();
            $data = array();
            foreach ($datos as $row) {
            $data[] = array(
                "id_metodo_pago" => $row["id_metodo_pago"],
                "codigo_metodo_pago" => $row["codigo_metodo_pago"],
                "nombre_metodo_pago" => $row["nombre_metodo_pago"],
                "observaciones_metodo_pago" => $row["observaciones_metodo_pago"],
                "activo_metodo_pago" => $row["activo_metodo_pago"],
                "created_at_metodo_pago" => $row["created_at_metodo_pago"],
                "updated_at_metodo_pago" => $row["updated_at_metodo_pago"]
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
            // Log inicial para confirmar que se ejecuta esta sección
            writeToLog(['action' => 'guardaryeditar_iniciado', 'timestamp' => date('Y-m-d H:i:s')]);
            
            try {
                // Obtener datos del formulario
                $codigo_metodo_pago = $_POST["codigo_metodo_pago"] ?? '';
                $nombre_metodo_pago = $_POST["nombre_metodo_pago"] ?? '';
                $observaciones_metodo_pago = $_POST["observaciones_metodo_pago"] ?? '';
                $activo_metodo_pago = isset($_POST["activo_metodo_pago"]) ? (int)$_POST["activo_metodo_pago"] : 1;
                
                // Log de información recibida
                writeToLog([
                    'action' => 'guardaryeditar_inicio',
                    'post_info' => $_POST
                ]);
                
                                
                if (empty($_POST["id_metodo_pago"])) {
                    // Insertar nuevo método de pago
                    $resultado = $metodospago->insert_metodo_pago(
                        $codigo_metodo_pago,
                        $nombre_metodo_pago,
                        $observaciones_metodo_pago
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'metodospago.php',
                            'Guardar método de pago',
                            "Método de pago guardado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Método de pago insertado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar el método de pago"
                        ]);
                    }
        
                } else {
                    // Actualizar método de pago existente
                    $resultado = $metodospago->update_metodo_pago(
                        $_POST["id_metodo_pago"],
                        $codigo_metodo_pago,
                        $nombre_metodo_pago,
                        $observaciones_metodo_pago
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'metodospago.php',
                            'Actualizar método de pago',
                            "Método de pago actualizado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Método de pago actualizado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar el método de pago"
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
            // Encabezado para indicar que se devuelve JSON
            header('Content-Type: application/json; charset=utf-8');
            // Obtenemos el método de pago por ID
            $datos = $metodospago->get_metodo_pagoxid($_POST["id_metodo_pago"]);
            // Si hay datos, los devolvemos; si no, mandamos un JSON de error
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener el método de pago solicitado"
                ]);
            }
        
             break;
            

    case "eliminar":
        $metodospago->delete_metodo_pagoxid($_POST["id_metodo_pago"]);
        break;

    case "activar":
        $metodospago->activar_metodo_pagoxid($_POST["id_metodo_pago"]);
        break;

    case "verificarMetodoPago":
            // Obtener el código y nombre del método de pago, y el ID (si se está editando)
            $codigoMetodoPago = isset($_GET["codigo_metodo_pago"]) ? trim($_GET["codigo_metodo_pago"]) : '';
            
            $nombreMetodoPago = isset($_GET["nombre_metodo_pago"]) ? trim($_GET["nombre_metodo_pago"]) : '';

            $idMetodoPago = isset($_GET["id_metodo_pago"]) ? trim($_GET["id_metodo_pago"]) : null;

            // Validar que el código o nombre del método de pago no estén vacíos
            if (empty($codigoMetodoPago) && empty($nombreMetodoPago)) {
                $registro->registrarActividad(
                    'admin',
                    'metodospago.php',
                    'Verificar método de pago',
                    'Intento de verificación con código y nombre vacíos',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El código o nombre del método de pago no pueden estar vacíos."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $metodospago->verificarMetodoPago($codigoMetodoPago, $nombreMetodoPago, $idMetodoPago);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'metodospago.php',
                    'Verificar método de pago',
                    'Error al verificar método de pago: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar el método de pago: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Método de pago duplicado detectado: ' . $codigoMetodoPago : 
                'Método de pago disponible: ' . $codigoMetodoPago;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'metodospago.php',
                'Verificar método de pago',
                $mensajeActividad,
                $tipoActividad
            );
            
            // Devolver el resultado como JSON
            echo json_encode([
                "success" => true,
                "existe" => $resultado['existe']  // Accedemos a la clave 'existe' del array
            ]);
            break;
            
}
