<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Observaciones.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/observaciones_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] observaciones.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);


//CREATE TABLE observacion_general (
//    id_obs_general INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_obs_general VARCHAR(20) NOT NULL UNIQUE,
//    titulo_obs_general VARCHAR(100) NOT NULL,
//    title_obs_general VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Título en inglés',
//    texto_obs_general TEXT NOT NULL,
//    text_obs_general TEXT NOT NULL COMMENT 'Texto en inglés',
//    orden_obs_general INT DEFAULT 0,
//    tipo_obs_general ENUM('condiciones', 'tecnicas', 'legales', 'comerciales', 'otras') DEFAULT 'otras',
//    obligatoria_obs_general BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, siempre aparece en presupuestos',
//    activo_obs_general BOOLEAN DEFAULT TRUE,
//    created_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    INDEX idx_orden_obs_general (orden_obs_general),
//    INDEX idx_obligatoria_obs_general (obligatoria_obs_general)
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
            
            $logFile = $logDir . "observaciones_debug_" . date("Ymd") . ".txt";
            
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




//CREATE TABLE observacion_general (
//    id_obs_general INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_obs_general VARCHAR(20) NOT NULL UNIQUE,
//    titulo_obs_general VARCHAR(100) NOT NULL,
//    texto_obs_general TEXT NOT NULL,
//    orden_obs_general INT DEFAULT 0,
//    tipo_obs_general ENUM('condiciones', 'tecnicas', 'legales', 'comerciales', 'otras') DEFAULT 'otras',
//    obligatoria_obs_general BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, siempre aparece en presupuestos',
//    activo_obs_general BOOLEAN DEFAULT TRUE,
//    created_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_obs_general TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//    INDEX idx_orden_obs_general (orden_obs_general),
//    INDEX idx_obligatoria_obs_general (obligatoria_obs_general)
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$observaciones = new Observaciones();

switch ($_GET["op"]) {

    case "listar":
        $datos = $observaciones->get_observaciones();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_obs_general" => $row["id_obs_general"],
                "codigo_obs_general" => $row["codigo_obs_general"],
                "titulo_obs_general" => $row["titulo_obs_general"],
                "title_obs_general" => $row["title_obs_general"] ?? '',
                "texto_obs_general" => $row["texto_obs_general"],
                "text_obs_general" => $row["text_obs_general"] ?? '',
                "orden_obs_general" => $row["orden_obs_general"],
                "tipo_obs_general" => $row["tipo_obs_general"],
                "obligatoria_obs_general" => $row["obligatoria_obs_general"],
                "activo_obs_general" => $row["activo_obs_general"],
                "created_at_obs_general" => $row["created_at_obs_general"],
                "updated_at_obs_general" => $row["updated_at_obs_general"]
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
            $datos = $observaciones->get_observaciones_disponible();
            $data = array();
            foreach ($datos as $row) {
            $data[] = array(
                "id_obs_general" => $row["id_obs_general"],
                "codigo_obs_general" => $row["codigo_obs_general"],
                "titulo_obs_general" => $row["titulo_obs_general"],
                "title_obs_general" => $row["title_obs_general"] ?? '',
                "texto_obs_general" => $row["texto_obs_general"],
                "text_obs_general" => $row["text_obs_general"] ?? '',
                "orden_obs_general" => $row["orden_obs_general"],
                "tipo_obs_general" => $row["tipo_obs_general"],
                "obligatoria_obs_general" => $row["obligatoria_obs_general"],
                "activo_obs_general" => $row["activo_obs_general"],
                "created_at_obs_general" => $row["created_at_obs_general"],
                "updated_at_obs_general" => $row["updated_at_obs_general"]
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
                $codigo_obs_general = $_POST["codigo_obs_general"] ?? '';
                $titulo_obs_general = $_POST["titulo_obs_general"] ?? '';
                $title_obs_general = $_POST["title_obs_general"] ?? '';
                $texto_obs_general = $_POST["texto_obs_general"] ?? '';
                $text_obs_general = $_POST["text_obs_general"] ?? '';
                $orden_obs_general = isset($_POST["orden_obs_general"]) ? (int)$_POST["orden_obs_general"] : 0;
                $tipo_obs_general = $_POST["tipo_obs_general"] ?? 'otras';
                $obligatoria_obs_general = isset($_POST["obligatoria_obs_general"]) ? (int)$_POST["obligatoria_obs_general"] : 1;
                
                // Log de información recibida
                writeToLog([
                    'action' => 'guardaryeditar_inicio',
                    'post_info' => $_POST
                ]);
                
                                
                if (empty($_POST["id_obs_general"])) {
                    // Insertar nueva observación
                    $resultado = $observaciones->insert_observaciones(
                        $codigo_obs_general,
                        $titulo_obs_general,
                        $title_obs_general,
                        $texto_obs_general,
                        $text_obs_general,
                        $orden_obs_general,
                        $tipo_obs_general,
                        $obligatoria_obs_general
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'observaciones.php',
                            'Guardar observación',
                            "Observación guardada exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Observación insertada correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar la observación"
                        ]);
                    }
        
                } else {
                    // Actualizar observación existente
                    $resultado = $observaciones->update_observaciones(
                        $_POST["id_obs_general"],
                        $codigo_obs_general,
                        $titulo_obs_general,
                        $title_obs_general,
                        $texto_obs_general,
                        $text_obs_general,
                        $orden_obs_general,
                        $tipo_obs_general,
                        $obligatoria_obs_general
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'observaciones.php',
                            'Actualizar observación',
                            "Observación actualizada exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Observación actualizada correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar la observación"
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
            // Obtenemos la observación por ID
            $datos = $observaciones->get_observacionesxid($_POST["id_obs_general"]);
            // Si hay datos, los devolvemos; si no, mandamos un JSON de error
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener la observación solicitada"
                ]);
            }
        
             break;
            

    case "eliminar":
        $observaciones->delete_observacionesxid($_POST["id_obs_general"]);
        break;

    case "activar":
        $observaciones->activar_observacionesxid($_POST["id_obs_general"]);
        break;

    case "verificarObservaciones":
            // Obtener el código de la observación y el ID (si se está editando)
            $codigoObsGeneral = isset($_GET["codigo_obs_general"]) ? trim($_GET["codigo_obs_general"]) : '';

            $idObsGeneral = isset($_GET["id_obs_general"]) ? trim($_GET["id_obs_general"]) : null;

            // Validar que el código de la observación no esté vacío
            if (empty($codigoObsGeneral)) {
                $registro->registrarActividad(
                    'admin',
                    'observaciones.php',
                    'Verificar observación',
                    'Intento de verificación con codigo_obs_general vacío',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El código de la observación no puede estar vacío."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $observaciones->verificarObservaciones($codigoObsGeneral, $idObsGeneral);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'observaciones.php',
                    'Verificar observación',
                    'Error al verificar observación: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar la observación: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Observación duplicada detectada: ' . $codigoObsGeneral : 
                'Observación disponible: ' . $codigoObsGeneral;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'observaciones.php',
                'Verificar observación',
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
