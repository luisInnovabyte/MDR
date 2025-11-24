<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Unidad.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/unidad_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] unidad.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);


//CREATE TABLE unidad_medida (
//    id_unidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    nombre_unidad VARCHAR(50) NOT NULL,
//    name_unidad VARCHAR(50) NOT NULL COMMENT 'Nombre en inglés',
//    descr_unidad VARCHAR(255),
//    simbolo_unidad VARCHAR(10),
//    activo_unidad boolean default true, 
//    created_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//);


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
            
            $logFile = $logDir . "unidad_debug_" . date("Ymd") . ".txt";
            
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




//CREATE TABLE unidad_medida (
//    id_unidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    nombre_unidad VARCHAR(50) NOT NULL,
//    name_unidad VARCHAR(50) NOT NULL COMMENT 'Nombre en inglés',
//    descr_unidad VARCHAR(255),
//    simbolo_unidad VARCHAR(10),
//    activo_unidad boolean default true, 
//    created_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//);


$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$unidad = new Unidad();

switch ($_GET["op"]) {

    case "listar":
        $datos = $unidad->get_unidad();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_unidad" => $row["id_unidad"],
                "nombre_unidad" => $row["nombre_unidad"],
                "name_unidad" => $row["name_unidad"],
                "descr_unidad" => $row["descr_unidad"],
                "simbolo_unidad" => $row["simbolo_unidad"],
                "activo_unidad" => $row["activo_unidad"],
                "created_at_unidad" => $row["created_at_unidad"],
                "updated_at_unidad" => $row["updated_at_unidad"]
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
            $datos = $unidad->get_unidad_disponible();
            $data = array();
            foreach ($datos as $row) {
            $data[] = array(
                "id_unidad" => $row["id_unidad"],
                "nombre_unidad" => $row["nombre_unidad"],
                "name_unidad" => $row["name_unidad"],
                "descr_unidad" => $row["descr_unidad"],
                "simbolo_unidad" => $row["simbolo_unidad"],
                "activo_unidad" => $row["activo_unidad"],
                "created_at_unidad" => $row["created_at_unidad"],
                "updated_at_unidad" => $row["updated_at_unidad"]
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
                $nombre_unidad = $_POST["nombre_unidad"] ?? '';
                $name_unidad = $_POST["name_unidad"] ?? '';
                $descr_unidad = $_POST["descr_unidad"] ?? '';
                $simbolo_unidad = $_POST["simbolo_unidad"] ?? '';
                $activo_unidad = isset($_POST["activo_unidad"]) ? (int)$_POST["activo_unidad"] : 1;
                
                // Log de información recibida
                writeToLog([
                    'action' => 'guardaryeditar_inicio',
                    'files_info' => $_FILES,
                    'post_info' => array_diff_key($_POST, ['imagen_actual' => '']) // Excluir imagen_actual para no llenar el log
                ]);
                
                                
                if (empty($_POST["id_unidad"])) {
                    // Insertar nueva unidad
                    $resultado = $unidad->insert_unidad(
                        $nombre_unidad,
                        $name_unidad,
                        $descr_unidad,
                        $simbolo_unidad
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'unidad.php',
                            'Guardar unidad',
                            "Unidad guardada exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Unidad insertada correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar la unidad"
                        ]);
                    }
        
                } else {
                    // Actualizar unidad existente
                    $resultado = $unidad->update_unidad(
                        $_POST["id_unidad"],
                        $nombre_unidad,
                        $name_unidad,
                        $descr_unidad,
                        $simbolo_unidad
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'unidad.php',
                            'Actualizar la unidad',
                            "Unidad actualizada exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Unidad actualizada correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar la unidad"
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
            // Obtenemos la unidad por ID
            $datos = $unidad->get_unidadxid($_POST["id_unidad"]);
            // Si hay datos, los devolvemos; si no, mandamos un JSON de error
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener la unidad solicitada"
                ]);
            }
        
             break;
            

    case "eliminar":
        $unidad->delete_unidadxid($_POST["id_unidad"]);
        break;

    case "activar":
        $unidad->activar_unidadxid($_POST["id_unidad"]);
        break;

    case "verificarUnidad":
            // Obtener el nombre de la unidad, nombre en inglés, símbolo y el ID (si se está editando)
            // Verificar si los datos vienen por POST o GET
            $nombreUnidad = isset($_POST["nombre_unidad"]) ? trim($_POST["nombre_unidad"]) : 
                           (isset($_GET["nombre_unidad"]) ? trim($_GET["nombre_unidad"]) : '');
            
            $nameUnidad = isset($_POST["name_unidad"]) ? trim($_POST["name_unidad"]) : 
                         (isset($_GET["name_unidad"]) ? trim($_GET["name_unidad"]) : '');

            $simboloUnidad = isset($_POST["simbolo_unidad"]) ? trim($_POST["simbolo_unidad"]) : 
                            (isset($_GET["simbolo_unidad"]) ? trim($_GET["simbolo_unidad"]) : '');

            $idUnidad = isset($_POST["id_unidad"]) ? trim($_POST["id_unidad"]) : 
                       (isset($_GET["id_unidad"]) ? trim($_GET["id_unidad"]) : null);

            // Log para debugging
            writeToLog([
                'action' => 'verificarUnidad',
                'nombre_unidad' => $nombreUnidad,
                'name_unidad' => $nameUnidad,
                'simbolo_unidad' => $simboloUnidad,
                'id_unidad' => $idUnidad,
                'method' => $_SERVER['REQUEST_METHOD'],
                'post_data' => $_POST,
                'get_data' => $_GET
            ]);

            // Validar que al menos uno de los campos no esté vacío
            if (empty($nombreUnidad) && empty($nameUnidad) && empty($simboloUnidad)) {
                $registro->registrarActividad(
                    'admin',
                    'unidad.php',
                    'Verificar unidad',
                    'Intento de verificación con todos los campos vacíos',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El nombre, nombre en inglés o símbolo de la unidad no pueden estar todos vacíos."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $unidad->verificarUnidad($nombreUnidad, $nameUnidad, $simboloUnidad, $idUnidad);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'unidad.php',
                    'Verificar unidad',
                    'Error al verificar unidad: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar la unidad: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Unidad duplicada detectada: ' . $nombreUnidad : 
                'Unidad disponible: ' . $nombreUnidad;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'unidad.php',
                'Verificar unidad',
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