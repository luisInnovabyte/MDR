<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Grupo_articulo.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/grupo_articulo_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] grupo_articulo.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);


//CREATE TABLE grupo_articulo (
//    id_grupo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_grupo VARCHAR(20) NOT NULL UNIQUE,
//    nombre_grupo VARCHAR(100) NOT NULL,
//    descripcion_grupo VARCHAR(255),
//    observaciones_grupo TEXT,
//    activo_grupo BOOLEAN DEFAULT TRUE,
//    created_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
            
            $logFile = $logDir . "grupo_articulo_debug_" . date("Ymd") . ".txt";
            
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




//CREATE TABLE grupo_articulo (
//    id_grupo INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//    codigo_grupo VARCHAR(20) NOT NULL UNIQUE,
//    nombre_grupo VARCHAR(100) NOT NULL,
//    descripcion_grupo VARCHAR(255),
//    observaciones_grupo TEXT,
//    activo_grupo BOOLEAN DEFAULT TRUE,
//    created_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//    updated_at_grupo TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
//) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$grupo_articulo = new Grupo_articulo();

switch ($_GET["op"]) {

    case "listar":
        $datos = $grupo_articulo->get_grupo_articulo();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_grupo" => $row["id_grupo"],
                "codigo_grupo" => $row["codigo_grupo"],
                "nombre_grupo" => $row["nombre_grupo"],
                "descripcion_grupo" => $row["descripcion_grupo"],
                "observaciones_grupo" => $row["observaciones_grupo"],
                "activo_grupo" => $row["activo_grupo"],
                "created_at_grupo" => $row["created_at_grupo"],
                "updated_at_grupo" => $row["updated_at_grupo"]
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
            $datos = $grupo_articulo->get_grupo_articulo_disponible();
            $data = array();
            foreach ($datos as $row) {
                $data[] = array(
                    "id_grupo" => $row["id_grupo"],
                    "codigo_grupo" => $row["codigo_grupo"],
                    "nombre_grupo" => $row["nombre_grupo"],
                    "descripcion_grupo" => $row["descripcion_grupo"] ?? '',
                    "observaciones_grupo" => $row["observaciones_grupo"] ?? '',
                    "activo_grupo" => $row["activo_grupo"],
                    "created_at_grupo" => $row["created_at_grupo"],
                    "updated_at_grupo" => $row["updated_at_grupo"]
                );
            }

            header('Content-Type: application/json');
            echo json_encode($data, JSON_UNESCAPED_UNICODE);
            break;

        case "guardaryeditar":
            // Log inicial para confirmar que se ejecuta esta sección
            writeToLog(['action' => 'guardaryeditar_iniciado', 'timestamp' => date('Y-m-d H:i:s')]);
            
            try {
                // Obtener datos del formulario
                $codigo_grupo = $_POST["codigo_grupo"] ?? '';
                $nombre_grupo = $_POST["nombre_grupo"] ?? '';
                $descripcion_grupo = $_POST["descripcion_grupo"] ?? '';
                $observaciones_grupo = $_POST["observaciones_grupo"] ?? '';
                $activo_grupo = isset($_POST["activo_grupo"]) ? (int)$_POST["activo_grupo"] : 1;
                
                // Log de información recibida
                writeToLog([
                    'action' => 'guardaryeditar_inicio',
                    'post_info' => $_POST
                ]);
                
                                
                if (empty($_POST["id_grupo"])) {
                    // Insertar nuevo grupo de artículo
                    $resultado = $grupo_articulo->insert_grupo_articulo(
                        $codigo_grupo,
                        $nombre_grupo,
                        $descripcion_grupo,
                        $observaciones_grupo
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'grupo_articulo.php',
                            'Guardar grupo de artículo',
                            "Grupo de artículo guardado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Grupo de artículo insertado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar el grupo de artículo"
                        ]);
                    }
        
                } else {
                    // Actualizar grupo de artículo existente
                    $resultado = $grupo_articulo->update_grupo_articulo(
                        $_POST["id_grupo"],
                        $codigo_grupo,
                        $nombre_grupo,
                        $descripcion_grupo,
                        $observaciones_grupo
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'grupo_articulo.php',
                            'Actualizar el grupo de artículo',
                            "Grupo de artículo actualizado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Grupo de artículo actualizado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar el grupo de artículo"
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
            // Obtenemos el grupo de artículo por ID
            $datos = $grupo_articulo->get_grupo_articuloxid($_POST["id_grupo"]);
            // Si hay datos, los devolvemos; si no, mandamos un JSON de error
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener el grupo de artículo solicitado"
                ]);
            }
        
             break;
            

    case "eliminar":
        $grupo_articulo->delete_grupo_articuloxid($_POST["id_grupo"]);
        break;

    case "activar":
        $grupo_articulo->activar_grupo_articuloxid($_POST["id_grupo"]);
        break;

    case "verificarGrupoArticulo":
            // Obtener el código del grupo, nombre y el ID (si se está editando)
            $codigoGrupo = isset($_GET["codigo_grupo"]) ? trim($_GET["codigo_grupo"]) : '';
            
            $nombreGrupo = isset($_GET["nombre_grupo"]) ? trim($_GET["nombre_grupo"]) : '';

            $idGrupo = isset($_GET["id_grupo"]) ? trim($_GET["id_grupo"]) : null;

            // Validar que el código o nombre del grupo no estén vacíos
            if (empty($codigoGrupo) && empty($nombreGrupo)) {
                $registro->registrarActividad(
                    'admin',
                    'grupo_articulo.php',
                    'Verificar grupo de artículo',
                    'Intento de verificación con codigo_grupo y nombre_grupo vacíos',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El código o nombre del grupo no pueden estar vacíos."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $grupo_articulo->verificarGrupoArticulo($codigoGrupo, $nombreGrupo, $idGrupo);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'grupo_articulo.php',
                    'Verificar grupo de artículo',
                    'Error al verificar grupo de artículo: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar el grupo de artículo: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Grupo de artículo duplicado detectado: ' . $codigoGrupo : 
                'Grupo de artículo disponible: ' . $codigoGrupo;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'grupo_articulo.php',
                'Verificar grupo de artículo',
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
