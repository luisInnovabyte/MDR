<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Impuesto.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/impuesto_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] impuesto.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);


//CREATE TABLE impuesto (
//  id_impuesto INT AUTO_INCREMENT PRIMARY KEY,
//  tipo_impuesto VARCHAR(20) NOT NULL COMMENT 'Tipo de impuesto (e.g., IVA, GST)',
//  tasa_impuesto DECIMAL(5,2) NOT NULL comment 'Tasa del impuesto en porcentaje',
//  descr_impuesto VARCHAR(255),
//  activo_impuesto boolean default true, 
//  created_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//  updated_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
            
            $logFile = $logDir . "familia_debug_" . date("Ymd") . ".txt";
            
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




//CREATE TABLE impuesto (
//  id_impuesto INT AUTO_INCREMENT PRIMARY KEY,
//  tipo_impuesto VARCHAR(20) NOT NULL COMMENT 'Tipo de impuesto (e.g., IVA, GST)',
//  tasa_impuesto DECIMAL(5,2) NOT NULL comment 'Tasa del impuesto en porcentaje',
//  descr_impuesto VARCHAR(255),
//  activo_impuesto boolean default true, 
//  created_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//  updated_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//);


$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$impuesto = new Impuesto();

switch ($_GET["op"]) {

    case "listar":
        $datos = $impuesto->get_impuesto();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_impuesto" => $row["id_impuesto"],
                "tipo_impuesto" => $row["tipo_impuesto"],
                "tasa_impuesto" => $row["tasa_impuesto"],
                "descr_impuesto" => $row["descr_impuesto"],
                "activo_impuesto" => $row["activo_impuesto"],
                "created_at_impuesto" => $row["created_at_impuesto"],
                "updated_at_impuesto" => $row["updated_at_impuesto"]
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
            $datos = $impuesto->get_impuesto_disponible();
            $data = array();
            foreach ($datos as $row) {
            $data[] = array(
                "id_impuesto" => $row["id_impuesto"],
                "tipo_impuesto" => $row["tipo_impuesto"],
                "tasa_impuesto" => $row["tasa_impuesto"],
                "descr_impuesto" => $row["descr_impuesto"],
                "activo_impuesto" => $row["activo_impuesto"],
                "created_at_impuesto" => $row["created_at_impuesto"],
                "updated_at_impuesto" => $row["updated_at_impuesto"]
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
                $tipo_impuesto = $_POST["tipo_impuesto"] ?? '';
                $tasa_impuesto = $_POST["tasa_impuesto"] ?? '';
                $descr_impuesto = $_POST["descr_impuesto"] ?? '';
                $activo_impuesto = isset($_POST["activo_impuesto"]) ? (int)$_POST["activo_impuesto"] : 1;
                
                // Log de información recibida
                writeToLog([
                    'action' => 'guardaryeditar_inicio',
                    'files_info' => $_FILES,
                    'post_info' => array_diff_key($_POST, ['imagen_actual' => '']) // Excluir imagen_actual para no llenar el log
                ]);
                
                                
                if (empty($_POST["id_impuesto"])) {
                    // Insertar nuevo impuesto
                    $resultado = $impuesto->insert_impuesto(
                        $tipo_impuesto,
                        $tasa_impuesto,
                        $descr_impuesto,
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'impuesto.php',
                            'Guardar  impuesto',
                            "Impuesto guardado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Impuesto insertado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar el impuesto"
                        ]);
                    }
        
                } else {
                    // Actualizar impuesto existente
                    $resultado = $impuesto->update_impuesto(
                        $_POST["id_impuesto"],
                        $tipo_impuesto,
                        $tasa_impuesto,
                        $descr_impuesto
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'impuesto.php',
                            'Actualizar el impuesto',
                            "Impuesto actualizado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Impuesto actualizado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar el impuesto"
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
            // Obtenemos el impuesto por ID
            $datos = $impuesto->get_impuestoxid($_POST["id_impuesto"]);
            // Si hay datos, los devolvemos; si no, mandamos un JSON de error
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener el impuesto solicitado"
                ]);
            }
        
             break;
            

    case "eliminar":
        $impuesto->delete_impuestoxid($_POST["id_impuesto"]);
        break;

    case "activar":
        $impuesto->activar_impuestoxid($_POST["id_impuesto"]);
        break;

    case "verificarImpuesto":
            // Obtener el nombre de la tipo_impuesto, tasa_impuesto y el ID (si se está editando)
            $tipoImpuesto = isset($_GET["tipo_impuesto"]) ? trim($_GET["tipo_impuesto"]) : '';
            
            $tasa_impuesto = isset($_GET["tasa_impuesto"]) ? trim($_GET["tasa_impuesto"]) : '';

            $idImpuesto = isset($_GET["id_impuesto"]) ? trim($_GET["id_impuesto"]) : null;

            // Validar que el nombre del impuesto no esté vacío
            if (empty($tipoImpuesto) && empty($tasa_impuesto)) {
                $registro->registrarActividad(
                    'admin',
                    'impuesto.php',
                    'Verificar impuesto',
                    'Intento de verificación con tipo_impuesto y tasa_impuesto vacíos',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El tipo o tasa del impuesto no pueden estar vacíos."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $impuesto->verificarImpuesto($tipoImpuesto, $tasa_impuesto, $idImpuesto);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'impuesto.php',
                    'Verificar impuesto',
                    'Error al verificar impuesto: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar el impuesto: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Impuesto duplicado detectado: ' . $tipoImpuesto : 
                'Impuesto disponible: ' . $tipoImpuesto;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'impuesto.php',
                'Verificar impuesto',
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
