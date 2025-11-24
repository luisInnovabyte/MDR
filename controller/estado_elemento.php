<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Estado_elemento.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/estado_elemento_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] estado_elemento.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);


//  id_estado_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_estado_elemento VARCHAR(20) NOT NULL UNIQUE,
//     descripcion_estado_elemento VARCHAR(50) NOT NULL,
//     color_estado_elemento VARCHAR(7),
//     permite_alquiler_estado_elemento BOOLEAN DEFAULT TRUE,
//     observaciones_estado_elemento TEXT,
//     activo_estado_elemento BOOLEAN DEFAULT TRUE,
//     created_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP




$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$estado_elemento = new Estado_elemento();

// Log de parámetros recibidos
file_put_contents(__DIR__ . "/../public/logs/estado_elemento_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] GET: " . json_encode($_GET) . " | POST: " . json_encode($_POST) . "\n", 
                  FILE_APPEND | LOCK_EX);

// Obtener 'op' de GET o POST (prioridad a GET)
$op = $_GET["op"] ?? $_POST["op"] ?? null;

if (!$op) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode([
        "status" => "error",
        "message" => "Parámetro 'op' no proporcionado"
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

switch ($op) {

    case "listar":
        $datos = $estado_elemento->get_estado_elemento();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_estado_elemento" => $row["id_estado_elemento"],
                "codigo_estado_elemento" => $row["codigo_estado_elemento"],
                "descripcion_estado_elemento" => $row["descripcion_estado_elemento"],
                "color_estado_elemento" => $row["color_estado_elemento"],
                "permite_alquiler_estado_elemento" => $row["permite_alquiler_estado_elemento"],
                "observaciones_estado_elemento" => $row["observaciones_estado_elemento"],
                "activo_estado_elemento" => $row["activo_estado_elemento"],
                "created_at_estado_elemento" => $row["created_at_estado_elemento"],
                "updated_at_estado_elemento" => $row["updated_at_estado_elemento"]
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
            $datos = $estado_elemento->get_estado_elemento_disponible();
            $data = array();
            foreach ($datos as $row) {
            $data[] = array(
                "id_estado_elemento" => $row["id_estado_elemento"],
                "codigo_estado_elemento" => $row["codigo_estado_elemento"],
                "descripcion_estado_elemento" => $row["descripcion_estado_elemento"],
                "color_estado_elemento" => $row["color_estado_elemento"],
                "permite_alquiler_estado_elemento" => $row["permite_alquiler_estado_elemento"],
                "observaciones_estado_elemento" => $row["observaciones_estado_elemento"],
                "activo_estado_elemento" => $row["activo_estado_elemento"],
                "created_at_estado_elemento" => $row["created_at_estado_elemento"],
                "updated_at_estado_elemento" => $row["updated_at_estado_elemento"]
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
            try {
                // Obtener datos del formulario
                $codigo_estado_elemento = $_POST["codigo_estado_elemento"] ?? '';
                $descripcion_estado_elemento = $_POST["descripcion_estado_elemento"] ?? '';
                $color_estado_elemento = $_POST["color_estado_elemento"] ?? '';
                $permite_alquiler_estado_elemento = isset($_POST["permite_alquiler_estado_elemento"]) ? (int)$_POST["permite_alquiler_estado_elemento"] : 1;
                $observaciones_estado_elemento = $_POST["observaciones_estado_elemento"] ?? '';
                
                // Debug: Log de los datos recibidos
                file_put_contents(__DIR__ . "/../public/logs/estado_elemento_debug_" . date("Ymd") . ".txt", 
                                  "[" . date("Y-m-d H:i:s") . "] Datos recibidos: " . print_r($_POST, true) . "\n", 
                                  FILE_APPEND | LOCK_EX);
                
                // Validar campos obligatorios
                if (empty($codigo_estado_elemento) || empty($descripcion_estado_elemento)) {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Los campos código y descripción son obligatorios"
                    ]);
                    break;
                }
                
                if (empty($_POST["id_estado_elemento"])) {
                    // Insertar nuevo estado de elemento                                      
                    $resultado = $estado_elemento->insert_estado_elemento(
                        $codigo_estado_elemento,
                        $descripcion_estado_elemento,
                        $color_estado_elemento,
                        $permite_alquiler_estado_elemento,
                        $observaciones_estado_elemento
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'estado_elemento.php',
                            'Guardar el estado de elemento',
                            "Estado de elemento guardado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "status" => "success",
                            "message" => "Estado de elemento insertado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "status" => "error",
                            "message" => "Error al insertar el estado de elemento"
                        ]);
                    }
        
                } else {
                    // Actualizar estado de elemento existente
                    $resultado = $estado_elemento->update_estado_elemento(
                        $_POST["id_estado_elemento"],
                        $codigo_estado_elemento,
                        $descripcion_estado_elemento,
                        $color_estado_elemento,
                        $permite_alquiler_estado_elemento,
                        $observaciones_estado_elemento
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'estado_elemento.php',
                            'Actualizar el estado de elemento',
                            "Estado de elemento actualizado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "status" => "success",
                            "message" => "Estado de elemento actualizado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "status" => "error",
                            "message" => "Error al actualizar el estado de elemento"
                        ]);
                    }
                }
            } catch (Exception $e) {
                echo json_encode([
                    "status" => "error",
                    "message" => "Excepción: " . $e->getMessage()
                ]);
            }
            break;
        

        case "mostrar": 
            // Encabezado para indicar que se devuelve JSON
            header('Content-Type: application/json; charset=utf-8');
            
            try {
                // Log de inicio
                $logFile = __DIR__ . "/../public/logs/estado_elemento_debug_" . date("Ymd") . ".txt";
                file_put_contents($logFile, 
                                  "[" . date("Y-m-d H:i:s") . "] === INICIO MOSTRAR ===\n", 
                                  FILE_APPEND | LOCK_EX);
                
                // Validar que se recibió el ID
                if (!isset($_POST["id_estado_elemento"]) || empty($_POST["id_estado_elemento"])) {
                    file_put_contents($logFile, 
                                      "[" . date("Y-m-d H:i:s") . "] ERROR: ID no proporcionado\n", 
                                      FILE_APPEND | LOCK_EX);
                    echo json_encode([
                        "status" => "error",
                        "message" => "ID de estado de elemento no proporcionado"
                    ], JSON_UNESCAPED_UNICODE);
                    exit;
                }
                
                $id = $_POST["id_estado_elemento"];
                
                file_put_contents($logFile, 
                                  "[" . date("Y-m-d H:i:s") . "] ID recibido: " . $id . " (tipo: " . gettype($id) . ")\n", 
                                  FILE_APPEND | LOCK_EX);
                
                // Obtenemos el estado de elemento por ID
                $datos = $estado_elemento->get_estado_elementoxid($id);
                
                // Log del tipo de dato retornado
                file_put_contents($logFile, 
                                  "[" . date("Y-m-d H:i:s") . "] Tipo de \$datos: " . gettype($datos) . "\n", 
                                  FILE_APPEND | LOCK_EX);
                
                file_put_contents($logFile, 
                                  "[" . date("Y-m-d H:i:s") . "] Contenido de \$datos: " . var_export($datos, true) . "\n", 
                                  FILE_APPEND | LOCK_EX);
                
                // Si hay datos, los devolvemos; si no, mandamos un JSON de error
                if ($datos && is_array($datos) && count($datos) > 0) {
                    file_put_contents($logFile, 
                                      "[" . date("Y-m-d H:i:s") . "] ✅ Datos encontrados, enviando respuesta\n", 
                                      FILE_APPEND | LOCK_EX);
                    
                    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
                } else {
                    file_put_contents($logFile, 
                                      "[" . date("Y-m-d H:i:s") . "] ❌ No se encontraron datos (is_array: " . (is_array($datos) ? 'true' : 'false') . ", count: " . (is_array($datos) ? count($datos) : 'N/A') . ")\n", 
                                      FILE_APPEND | LOCK_EX);
                    
                    echo json_encode([
                        "status" => "error",
                        "message" => "No se encontró el estado de elemento con ID: " . $id
                    ], JSON_UNESCAPED_UNICODE);
                }
                
                file_put_contents($logFile, 
                                  "[" . date("Y-m-d H:i:s") . "] === FIN MOSTRAR ===\n\n", 
                                  FILE_APPEND | LOCK_EX);
                
            } catch (Exception $e) {
                $logFile = __DIR__ . "/../public/logs/estado_elemento_debug_" . date("Ymd") . ".txt";
                file_put_contents($logFile, 
                                  "[" . date("Y-m-d H:i:s") . "] ⚠️ EXCEPCIÓN: " . $e->getMessage() . "\n", 
                                  FILE_APPEND | LOCK_EX);
                file_put_contents($logFile, 
                                  "[" . date("Y-m-d H:i:s") . "] Stack trace: " . $e->getTraceAsString() . "\n\n", 
                                  FILE_APPEND | LOCK_EX);
                
                echo json_encode([
                    "status" => "error",
                    "message" => "Error al obtener el estado de elemento: " . $e->getMessage()
                ], JSON_UNESCAPED_UNICODE);
            }
            break;
            

    case "eliminar":
        $estado_elemento->delete_estado_elementoxid($_POST["id_estado_elemento"]);
        break;

    case "activar":
        $estado_elemento->activar_estado_elementoxid($_POST["id_estado_elemento"]);
        break;

    case "verificarEstadoElemento":
            // Obtener el código y descripción del estado de elemento, y el ID (si se está editando)
            $codigoEstado = isset($_POST["codigo_estado_elemento"]) ? trim($_POST["codigo_estado_elemento"]) : '';
            $descripcionEstado = isset($_POST["descripcion_estado_elemento"]) ? trim($_POST["descripcion_estado_elemento"]) : '';
            $idEstado = isset($_POST["id_estado_elemento"]) ? trim($_POST["id_estado_elemento"]) : null;

            // Validar que el código del estado de elemento no esté vacío
            if (empty($codigoEstado) && empty($descripcionEstado)) {
                $registro->registrarActividad(
                    'admin',
                    'estado_elemento.php',
                    'Verificar estado de elemento',
                    'Intento de verificación con código y descripción vacíos',
                    'warning'
                );
            
                echo json_encode([
                    "status" => "error",
                    "message" => "El código o descripción del estado de elemento no pueden estar vacíos."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $estado_elemento->verificarEstadoElemento($codigoEstado, $descripcionEstado, $idEstado);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'estado_elemento.php',
                    'Verificar estado de elemento',
                    'Error al verificar estado de elemento: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "status" => "error",
                    "message" => "Error al verificar el estado de elemento: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Estado de elemento duplicado detectado: ' . $descripcionEstado : 
                'Estado de elemento disponible: ' . $descripcionEstado;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'estado_elemento.php',
                'Verificar estado de elemento',
                $mensajeActividad,
                $tipoActividad
            );
            
            // Devolver el resultado como JSON
            echo json_encode([
                "status" => "success",
                "existe" => $resultado['existe']  // Accedemos a la clave 'existe' del array
            ]);
            break;
            
}
?>
