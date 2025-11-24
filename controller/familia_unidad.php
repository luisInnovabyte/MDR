<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Familia_unidad.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/familia_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] familia.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);


//  id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_familia VARCHAR(20) NOT NULL UNIQUE,
//     nombre_familia VARCHAR(100) NOT NULL,
//     descr_familia VARCHAR(255),
//     activo_familia BOOLEAN DEFAULT TRUE,
//     created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


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

// Función para procesar imagen de familia
function procesarImagenFamilia($archivo)
{
    try {
        // Log de información del archivo recibido
        writeToLog([
            'action' => 'procesarImagenFamilia_inicio',
            'archivo_info' => [
                'name' => $archivo['name'] ?? 'no_name',
                'type' => $archivo['type'] ?? 'no_type',
                'size' => $archivo['size'] ?? 0,
                'error' => $archivo['error'] ?? 'no_error',
                'tmp_name' => $archivo['tmp_name'] ?? 'no_tmp'
            ]
        ]);
        
        // Verificar si hay errores en la subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            writeToLog(['error' => 'Error en subida de archivo', 'upload_error' => $archivo['error']]);
            return false;
        }
        
        // Verificar que el archivo temporal existe
        if (!file_exists($archivo['tmp_name'])) {
            writeToLog(['error' => 'Archivo temporal no existe', 'tmp_name' => $archivo['tmp_name']]);
            return false;
        }
        
        // Directorio de destino - usar ruta absoluta
        $directorio = __DIR__ . "/../public/img/familia/";
        writeToLog(['action' => 'directorio_calculado', 'path' => $directorio, 'realpath' => realpath(dirname($directorio))]);
        
        // Verificar que el directorio existe
        if (!is_dir($directorio)) {
            writeToLog(['action' => 'creando_directorio', 'path' => $directorio]);
            if (!mkdir($directorio, 0755, true)) {
                writeToLog(['error' => 'No se pudo crear el directorio', 'path' => $directorio]);
                return false;
            }
        }
        
        // Validar el archivo
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        $tipoArchivo = $archivo['type'];
        $tamañoArchivo = $archivo['size'];
        
        // Validar tipo con finfo para mayor seguridad
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoReal = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        writeToLog(['info' => 'Validación de tipos', 'tipo_reportado' => $tipoArchivo, 'tipo_real' => $tipoReal]);
        
        // Validar tipo (usar el tipo real detectado)
        if (!in_array($tipoReal, $tiposPermitidos)) {
            writeToLog(['error' => 'Tipo de archivo no permitido', 'tipo_real' => $tipoReal, 'tipos_permitidos' => $tiposPermitidos]);
            return false;
        }
        
        // Validar tamaño (2MB máximo)
        if ($tamañoArchivo > 2 * 1024 * 1024) {
            writeToLog(['error' => 'Archivo demasiado grande', 'tamaño' => $tamañoArchivo, 'máximo' => 2 * 1024 * 1024]);
            return false;
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'familia_' . uniqid() . '.' . $extension;
        $rutaCompleta = $directorio . $nombreArchivo;
        
        writeToLog([
            'action' => 'moviendo_archivo',
            'desde' => $archivo['tmp_name'],
            'hacia' => $rutaCompleta,
            'nombre_final' => $nombreArchivo
        ]);
        
        // Mover el archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            writeToLog(['success' => 'Archivo movido exitosamente', 'archivo' => $nombreArchivo]);
            return $nombreArchivo; // Retornar solo el nombre del archivo
        } else {
            writeToLog(['error' => 'Error al mover archivo', 'tmp_name' => $archivo['tmp_name'], 'destino' => $rutaCompleta]);
            return false;
        }
        
    } catch (Exception $e) {
        writeToLog(['error' => 'Excepción procesando imagen', 'details' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return false;
    }
}

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$familia = new Familia();

switch ($_GET["op"]) {

    case "listar":
        $datos = $familia->get_familia();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_familia" => $row["id_familia"],
                "id_grupo" => $row["id_grupo"] ?? null,
                "codigo_grupo" => $row["codigo_grupo"] ?? null,
                "nombre_grupo" => $row["nombre_grupo"] ?? null,
                "descripcion_grupo" => $row["descripcion_grupo"] ?? null,
                "codigo_familia" => $row["codigo_familia"],
                "nombre_familia" => $row["nombre_familia"],
                "name_familia" => $row["name_familia"],
                "descr_familia" => $row["descr_familia"],
                "imagen_familia" => $row["imagen_familia"] ?? '',
                "observaciones_presupuesto_familia" => $row["observaciones_presupuesto_familia"] ?? '',
                "observations_budget_familia" => $row["observations_budget_familia"] ?? '',
                "orden_obs_familia" => $row["orden_obs_familia"] ?? 100,
                "coeficiente_familia" => $row["coeficiente_familia"] ?? null,
                "id_unidad_familia" => $row["id_unidad_familia"] ?? null,
                "nombre_unidad" => $row["nombre_unidad"] ?? null,
                "simbolo_unidad" => $row["simbolo_unidad"] ?? null,
                "descr_unidad" => $row["descr_unidad"] ?? null,
                "activo_familia" => $row["activo_familia"],
                "created_at_familia" => $row["created_at_familia"],
                "updated_at_familia" => $row["updated_at_familia"]
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
            $datos = $familia->get_familia_disponible();
            $data = array();
            foreach ($datos as $row) {
            $data[] = array(
                "id_familia" => $row["id_familia"],
                "id_grupo" => $row["id_grupo"] ?? null,
                "codigo_grupo" => $row["codigo_grupo"] ?? null,
                "nombre_grupo" => $row["nombre_grupo"] ?? null,
                "descripcion_grupo" => $row["descripcion_grupo"] ?? null,
                "codigo_familia" => $row["codigo_familia"],
                "nombre_familia" => $row["nombre_familia"],
                "name_familia" => $row["name_familia"],
                "descr_familia" => $row["descr_familia"],
                "imagen_familia" => $row["imagen_familia"] ?? '',
                "observaciones_presupuesto_familia" => $row["observaciones_presupuesto_familia"] ?? '',
                "observations_budget_familia" => $row["observations_budget_familia"] ?? '',
                "orden_obs_familia" => $row["orden_obs_familia"] ?? 100,
                "coeficiente_familia" => $row["coeficiente_familia"] ?? null,
                "id_unidad_familia" => $row["id_unidad_familia"] ?? null,
                "nombre_unidad" => $row["nombre_unidad"] ?? null,
                "simbolo_unidad" => $row["simbolo_unidad"] ?? null,
                "descr_unidad" => $row["descr_unidad"] ?? null,
                "activo_familia" => $row["activo_familia"],
                "created_at_familia" => $row["created_at_familia"],
                "updated_at_familia" => $row["updated_at_familia"]
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
                $nombre_familia = $_POST["nombre_familia"] ?? '';
                $codigo_familia = $_POST["codigo_familia"] ?? '';
                $name_familia = $_POST["name_familia"] ?? '';
                $descr_familia = $_POST["descr_familia"] ?? '';
                $activo_familia = isset($_POST["activo_familia"]) ? (int)$_POST["activo_familia"] : 1;
                $id_grupo = !empty($_POST["id_grupo"]) ? (int)$_POST["id_grupo"] : null;
                $id_unidad_familiaR = !empty($_POST["id_unidad_familia"]) ? (int)$_POST["id_unidad_familia"] : null;
                $coeficiente_familiaR = isset($_POST["coeficiente_familia"]) ? (float)$_POST["coeficiente_familia"] : 1;
                $observaciones_presupuesto_familia = $_POST["observaciones_presupuesto_familia"] ?? '';
                $observations_budget_familia = $_POST["observations_budget_familia"] ?? '';
                $orden_obs_familia = isset($_POST["orden_obs_familia"]) ? (int)$_POST["orden_obs_familia"] : 100;
                
                // Log de información recibida
                writeToLog([
                    'action' => 'guardaryeditar_inicio',
                    'observations_budget_familia' => $observations_budget_familia,
                    'observaciones_presupuesto_familia' => $observaciones_presupuesto_familia,
                    'files_info' => $_FILES,
                    'post_info' => array_diff_key($_POST, ['imagen_actual' => '']) // Excluir imagen_actual para no llenar el log
                ]);
                
                // Procesar imagen si se subió una
                $imagen_familia = '';
                if (isset($_FILES["imagen_familia"]) && $_FILES["imagen_familia"]["error"] == 0) {
                    writeToLog(['action' => 'procesando_imagen_nueva']);
                    $imagen_familia = procesarImagenFamilia($_FILES["imagen_familia"]);
                    if ($imagen_familia === false) {
                        writeToLog(['error' => 'Falló el procesamiento de imagen']);
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al procesar la imagen. Revise el archivo de logs para más detalles."
                        ]);
                        exit;
                    } else {
                        writeToLog(['success' => 'Imagen procesada correctamente', 'nombre_archivo' => $imagen_familia]);
                    }
                } elseif (isset($_POST["imagen_actual"])) {
                    // Mantener imagen actual si existe
                    $imagen_familia = $_POST["imagen_actual"];
                    writeToLog(['action' => 'manteniendo_imagen_actual', 'imagen' => $imagen_familia]);
                } else {
                    writeToLog(['action' => 'sin_imagen']);
                }
                
                if (empty($_POST["id_familia"])) {
                    // Insertar nueva familia
                $resultado = $familia->insert_familia(
                    $nombre_familia,
                    $codigo_familia,
                    $name_familia,
                    $descr_familia,
                    $imagen_familia,
                    $id_unidad_familiaR,
                    $coeficiente_familiaR,
                    $id_grupo,
                    $observaciones_presupuesto_familia,
                    $orden_obs_familia,
                    $observations_budget_familia
                );                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'familia.php',
                            'Guardar la familia',
                            "Familia guardada exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Familia insertada correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar la familia"
                        ]);
                    }
        
                } else {
                    // Actualizar familia existente
                    $resultado = $familia->update_familia(
                        $_POST["id_familia"],
                        $nombre_familia,
                        $codigo_familia,
                        $name_familia,
                        $descr_familia,
                        $imagen_familia,
                        $id_unidad_familiaR,
                        $coeficiente_familiaR,
                        $id_grupo,
                        $observaciones_presupuesto_familia,
                        $orden_obs_familia,
                        $observations_budget_familia
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'familia.php',
                            'Actualizar la familia',
                            "Familia actualizada exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Familia actualizada correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar la familia"
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
            // Obtenemos la familia por ID
            $datos = $familia->get_familiaxid($_POST["id_familia"]);
            // Si hay datos, los devolvemos; si no, mandamos un JSON de error
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener la familia solicitada"
                ]);
            }
        
             break;
            

    case "eliminar":
        $familia->delete_familiaxid($_POST["id_familia"]);
        break;

    case "activar":
        $familia->activar_familiaxid($_POST["id_familia"]);
        break;

    case "verificarFamilia":
            // Obtener el nombre de la familia, código y el ID (si se está editando)
            $nombreFamilia = isset($_GET["nombre_familia"]) ? trim($_GET["nombre_familia"]) : '';
            $nameFamilia = isset($_GET["name_familia"]) ? trim($_GET["name_familia"]) : '';
            $codigoFamilia = isset($_GET["codigo_familia"]) ? trim($_GET["codigo_familia"]) : '';
            $idFamilia = isset($_GET["id_familia"]) ? trim($_GET["id_familia"]) : null;

            // Validar que el nombre de la familia no esté vacío
            if (empty($nombreFamilia) && empty($codigoFamilia)) {
                $registro->registrarActividad(
                    'admin',
                    'familia.php',
                    'Verificar familia',
                    'Intento de verificación con nombre y código vacíos',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El nombre o código de la familia no pueden estar vacíos."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $familia->verificarFamilia($nombreFamilia, $codigoFamilia, $idFamilia, $nameFamilia);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'familia.php',
                    'Verificar familia',
                    'Error al verificar familia: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar la familia: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Familia duplicada detectada: ' . $nombreFamilia : 
                'Familia disponible: ' . $nombreFamilia;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'familia.php',
                'Verificar familia',
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
