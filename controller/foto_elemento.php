<?php
require_once "../config/conexion.php";
require_once "../models/Foto_elemento.php";
require_once '../config/funciones.php';

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/foto_elemento_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] foto_elemento.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);

// CREATE TABLE foto_elemento (
//     id_foto_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     id_elemento INT UNSIGNED NOT NULL,
//     descripcion_foto_elemento TEXT,
//     archivo_foto VARCHAR(500) NOT NULL,
//     privado_foto BOOLEAN DEFAULT FALSE,
//     observaciones_foto TEXT,
//     activo_foto BOOLEAN DEFAULT TRUE,
//     created_at_foto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_foto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
            
            $logFile = $logDir . "foto_elemento_debug_" . date("Ymd") . ".txt";
            
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

// Función para procesar foto de elemento
function procesarFotoElemento($archivo)
{
    try {
        // Log de información del archivo recibido
        writeToLog([
            'action' => 'procesarFotoElemento_inicio',
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
        $directorio = __DIR__ . "/../public/img/fotos_elementos/";
        writeToLog(['action' => 'directorio_calculado', 'path' => $directorio, 'realpath' => realpath(dirname($directorio))]);
        
        // Verificar que el directorio existe
        if (!is_dir($directorio)) {
            writeToLog(['action' => 'creando_directorio', 'path' => $directorio]);
            if (!mkdir($directorio, 0777, true)) {
                writeToLog(['error' => 'No se pudo crear el directorio', 'path' => $directorio]);
                return false;
            }
            // Asegurar permisos después de crear
            chmod($directorio, 0777);
        }
        
        // Verificar que el directorio es escribible
        if (!is_writable($directorio)) {
            writeToLog(['error' => 'Directorio no es escribible', 'path' => $directorio, 'perms' => substr(sprintf('%o', fileperms($directorio)), -4)]);
            // Intentar cambiar permisos
            @chmod($directorio, 0777);
            if (!is_writable($directorio)) {
                writeToLog(['error' => 'No se pudieron ajustar permisos del directorio']);
                return false;
            }
        }
        
        // Validar el archivo (solo imágenes)
        $tiposPermitidos = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        $tipoArchivo = $archivo['type'];
        $tamañoArchivo = $archivo['size'];
        
        // Validar tipo con finfo para mayor seguridad
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoReal = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        writeToLog(['info' => 'Validación de tipos', 'tipo_reportado' => $tipoArchivo, 'tipo_real' => $tipoReal]);
        
        // Validar tipo (usar el tipo real detectado o el reportado si el real falla)
        if (!in_array($tipoReal, $tiposPermitidos) && !in_array($tipoArchivo, $tiposPermitidos)) {
            writeToLog(['error' => 'Tipo de archivo no permitido', 'tipo_real' => $tipoReal, 'tipo_reportado' => $tipoArchivo, 'tipos_permitidos' => $tiposPermitidos]);
            return false;
        }
        
        // Validar tamaño (5MB máximo para imágenes)
        if ($tamañoArchivo > 5 * 1024 * 1024) {
            writeToLog(['error' => 'Archivo demasiado grande', 'tamaño' => $tamañoArchivo, 'máximo' => 5 * 1024 * 1024]);
            return false;
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'foto_elemento_' . uniqid() . '.' . $extension;
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
        writeToLog(['error' => 'Excepción procesando foto', 'details' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return false;
    }
}

$registro = new RegistroActividad();
$foto_elemento = new Foto_elemento();

switch ($_GET["op"]) {

    case "listar":
        // Verificar si se solicita filtrado por elemento
        if (isset($_GET["id_elemento"]) && !empty($_GET["id_elemento"])) {
            $datos = $foto_elemento->get_fotos_por_elemento($_GET["id_elemento"]);
        } else {
            $datos = $foto_elemento->get_foto_elemento();
        }
        
        $data = array();
        
        if ($datos !== false) {
            foreach ($datos as $row) {
                $data[] = array(
                    "id_foto_elemento" => $row["id_foto_elemento"],
                    "id_elemento" => $row["id_elemento"],
                    "codigo_elemento" => $row["codigo_elemento"] ?? '',
                    "nombre_elemento" => $row["nombre_elemento"] ?? '',
                    "descripcion_foto_elemento" => $row["descripcion_foto_elemento"] ?? '',
                    "archivo_foto" => $row["archivo_foto"],
                    "privado_foto" => $row["privado_foto"],
                    "observaciones_foto" => $row["observaciones_foto"] ?? '',
                    "activo_foto" => $row["activo_foto"],
                    "created_at_foto" => $row["created_at_foto"],
                    "updated_at_foto" => $row["updated_at_foto"]
                );
            }
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
        $datos = $foto_elemento->get_foto_elemento_disponible();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_foto_elemento" => $row["id_foto_elemento"],
                "id_elemento" => $row["id_elemento"],
                "descripcion_foto_elemento" => $row["descripcion_foto_elemento"] ?? '',
                "archivo_foto" => $row["archivo_foto"],
                "privado_foto" => $row["privado_foto"],
                "observaciones_foto" => $row["observaciones_foto"] ?? '',
                "activo_foto" => $row["activo_foto"],
                "created_at_foto" => $row["created_at_foto"],
                "updated_at_foto" => $row["updated_at_foto"]
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

    case "listarPorElemento":
        if (!isset($_POST["id_elemento"])) {
            echo json_encode([
                "success" => false,
                "message" => "ID de elemento no proporcionado"
            ]);
            break;
        }

        $datos = $foto_elemento->get_fotos_por_elemento($_POST["id_elemento"]);
        $data = array();
        
        if ($datos !== false) {
            foreach ($datos as $row) {
                $data[] = array(
                    "id_foto_elemento" => $row["id_foto_elemento"],
                    "id_elemento" => $row["id_elemento"],
                    "descripcion_foto_elemento" => $row["descripcion_foto_elemento"] ?? '',
                    "archivo_foto" => $row["archivo_foto"],
                    "privado_foto" => $row["privado_foto"],
                    "observaciones_foto" => $row["observaciones_foto"] ?? '',
                    "activo_foto" => $row["activo_foto"],
                    "created_at_foto" => $row["created_at_foto"],
                    "updated_at_foto" => $row["updated_at_foto"]
                );
            }
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
            $id_elemento = $_POST["id_elemento"] ?? '';
            $descripcion_foto_elemento = !empty($_POST["descripcion_foto_elemento"]) ? $_POST["descripcion_foto_elemento"] : null;
            $privado_foto = isset($_POST["privado_foto"]) ? (bool)$_POST["privado_foto"] : false;
            $observaciones_foto = !empty($_POST["observaciones_foto"]) ? $_POST["observaciones_foto"] : null;
            
            // Log de información recibida
            writeToLog([
                'action' => 'guardaryeditar_inicio',
                'files_info' => $_FILES,
                'post_info' => array_diff_key($_POST, ['archivo_actual' => ''])
            ]);
            
            // Procesar archivo si se subió uno
            $archivo_foto = '';
            if (isset($_FILES["archivo_foto"]) && $_FILES["archivo_foto"]["error"] == 0) {
                writeToLog(['action' => 'procesando_foto_nueva']);
                $archivo_foto = procesarFotoElemento($_FILES["archivo_foto"]);
                if ($archivo_foto === false) {
                    writeToLog(['error' => 'Falló el procesamiento de la foto']);
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al procesar la foto. Revise el archivo de logs para más detalles."
                    ]);
                    exit;
                } else {
                    writeToLog(['success' => 'Foto procesada correctamente', 'nombre_archivo' => $archivo_foto]);
                }
            } elseif (isset($_POST["archivo_actual"])) {
                // Mantener archivo actual si existe
                $archivo_foto = $_POST["archivo_actual"];
                writeToLog(['action' => 'manteniendo_archivo_actual', 'archivo' => $archivo_foto]);
            } else {
                writeToLog(['action' => 'sin_archivo']);
            }
            
            // Validar que se tenga un archivo (nuevo o existente)
            if (empty($archivo_foto)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Es necesario proporcionar un archivo de foto"
                ]);
                exit;
            }
            
            if (empty($_POST["id_foto_elemento"])) {
                // Insertar nueva foto
                writeToLog([
                    'action' => 'antes_de_insertar',
                    'id_elemento' => $id_elemento,
                    'archivo_foto' => $archivo_foto,
                    'descripcion_foto_elemento' => $descripcion_foto_elemento,
                    'privado_foto' => $privado_foto,
                    'observaciones_foto' => $observaciones_foto
                ]);
                
                $resultado = $foto_elemento->insert_foto_elemento(
                    $id_elemento,
                    $archivo_foto,
                    $descripcion_foto_elemento,
                    $privado_foto,
                    $observaciones_foto
                );
                
                writeToLog(['action' => 'resultado_insert', 'resultado' => $resultado]);
    
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'foto_elemento.php',
                        'Guardar foto de elemento',
                        "Foto de elemento guardada exitosamente",
                        "info"
                    );
    
                    echo json_encode([
                        "success" => true,
                        "message" => "Foto de elemento insertada correctamente"
                    ]);
                } else {
                    writeToLog(['error' => 'Fallo al insertar en base de datos', 'resultado' => $resultado]);
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al insertar la foto de elemento"
                    ]);
                }
    
            } else {
                // Actualizar foto existente
                $resultado = $foto_elemento->update_foto_elemento(
                    $_POST["id_foto_elemento"],
                    $id_elemento,
                    $archivo_foto,
                    $descripcion_foto_elemento,
                    $privado_foto,
                    $observaciones_foto
                );
    
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'foto_elemento.php',
                        'Actualizar foto de elemento',
                        "Foto de elemento actualizada exitosamente",
                        "info"
                    );
    
                    echo json_encode([
                        "success" => true,
                        "message" => "Foto de elemento actualizada correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al actualizar la foto de elemento"
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
        // Obtenemos la foto por ID
        $datos = $foto_elemento->get_foto_elementoxid($_POST["id_foto_elemento"]);
        // Si hay datos, los devolvemos; si no, mandamos un JSON de error
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener la foto de elemento solicitada"
            ]);
        }
        break;

    case "eliminar":
        $foto_elemento->delete_foto_elementoxid($_POST["id_foto_elemento"]);
        break;

    case "activar":
        $foto_elemento->activar_foto_elementoxid($_POST["id_foto_elemento"]);
        break;

}
