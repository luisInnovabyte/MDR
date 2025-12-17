<?php
require_once "../config/conexion.php";
require_once "../models/Documento.php";
require_once '../config/funciones.php';

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/documento_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] documento.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);

// CREATE TABLE documento (
//     id_documento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     titulo_documento VARCHAR(255) NOT NULL,
//     descripcion_documento TEXT,
//     ruta_documento VARCHAR(500) NOT NULL COMMENT 'Ruta relativa del archivo PDF',
//     id_tipo_documento_documento INT UNSIGNED NOT NULL,
//     fecha_publicacion_documento DATE,
//     activo_documento BOOLEAN DEFAULT TRUE,
//     fecha_creacion_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     fecha_modificacion_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
//     
//     FOREIGN KEY (id_tipo_documento_documento) REFERENCES tipo_documento(id_tipo_documento)
//         ON DELETE RESTRICT ON UPDATE CASCADE,
//     INDEX idx_tipo_documento(id_tipo_documento_documento),
//     INDEX idx_activo_documento(activo_documento)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

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
            
            $logFile = $logDir . "documento_debug_" . date("Ymd") . ".txt";
            
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

// Función para procesar documento
function procesarDocumento($archivo)
{
    try {
        // Log de información del archivo recibido
        writeToLog([
            'action' => 'procesarDocumento_inicio',
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
        $directorio = __DIR__ . "/../public/img/documentos/";
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
        
        // Validar el archivo (PDF principalmente, pero puede aceptar otros documentos)
        $tiposPermitidos = [
            'application/pdf',
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'text/plain',
            'image/jpeg',
            'image/png'
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
        
        // Validar tamaño (10MB máximo para documentos)
        if ($tamañoArchivo > 10 * 1024 * 1024) {
            writeToLog(['error' => 'Archivo demasiado grande', 'tamaño' => $tamañoArchivo, 'máximo' => 10 * 1024 * 1024]);
            return false;
        }
        
        // Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'documento_' . uniqid() . '.' . $extension;
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
        writeToLog(['error' => 'Excepción procesando documento', 'details' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
        return false;
    }
}

$registro = new RegistroActividad();
$documento = new Documento();

switch ($_GET["op"]) {

    case "listar":
        // Verificar si se solicita filtrado por tipo de documento
        if (isset($_GET["id_tipo_documento_documento"]) && !empty($_GET["id_tipo_documento_documento"])) {
            $datos = $documento->get_documentos_por_tipo($_GET["id_tipo_documento_documento"]);
        } else {
            $datos = $documento->get_documento();
        }
        
        $data = array();
        
        if ($datos !== false) {
            foreach ($datos as $row) {
                $data[] = array(
                    "id_documento" => $row["id_documento"],
                    "titulo_documento" => $row["titulo_documento"],
                    "descripcion_documento" => $row["descripcion_documento"] ?? '',
                    "ruta_documento" => $row["ruta_documento"],
                    "id_tipo_documento_documento" => $row["id_tipo_documento_documento"],
                    "codigo_tipo_documento" => $row["codigo_tipo_documento"] ?? '',
                    "nombre_tipo_documento" => $row["nombre_tipo_documento"] ?? '',
                    "fecha_publicacion_documento" => $row["fecha_publicacion_documento"],
                    "activo_documento" => $row["activo_documento"],
                    "fecha_creacion_documento" => $row["fecha_creacion_documento"],
                    "fecha_modificacion_documento" => $row["fecha_modificacion_documento"]
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
        $datos = $documento->get_documento_disponible();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_documento" => $row["id_documento"],
                "titulo_documento" => $row["titulo_documento"],
                "descripcion_documento" => $row["descripcion_documento"] ?? '',
                "ruta_documento" => $row["ruta_documento"],
                "id_tipo_documento_documento" => $row["id_tipo_documento_documento"],
                "fecha_publicacion_documento" => $row["fecha_publicacion_documento"],
                "activo_documento" => $row["activo_documento"],
                "fecha_creacion_documento" => $row["fecha_creacion_documento"],
                "fecha_modificacion_documento" => $row["fecha_modificacion_documento"]
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

    case "listarPorTipo":
        if (!isset($_POST["id_tipo_documento_documento"])) {
            echo json_encode([
                "success" => false,
                "message" => "ID de tipo de documento no proporcionado"
            ]);
            break;
        }

        $datos = $documento->get_documentos_por_tipo($_POST["id_tipo_documento_documento"]);
        $data = array();
        
        if ($datos !== false) {
            foreach ($datos as $row) {
                $data[] = array(
                    "id_documento" => $row["id_documento"],
                    "titulo_documento" => $row["titulo_documento"],
                    "descripcion_documento" => $row["descripcion_documento"] ?? '',
                    "ruta_documento" => $row["ruta_documento"],
                    "id_tipo_documento_documento" => $row["id_tipo_documento_documento"],
                    "fecha_publicacion_documento" => $row["fecha_publicacion_documento"],
                    "activo_documento" => $row["activo_documento"],
                    "fecha_creacion_documento" => $row["fecha_creacion_documento"],
                    "fecha_modificacion_documento" => $row["fecha_modificacion_documento"]
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
            $titulo_documento = $_POST["titulo_documento"] ?? '';
            $descripcion_documento = !empty($_POST["descripcion_documento"]) ? $_POST["descripcion_documento"] : null;
            $id_tipo_documento_documento = $_POST["id_tipo_documento_documento"] ?? '';
            $fecha_publicacion_documento = !empty($_POST["fecha_publicacion_documento"]) ? $_POST["fecha_publicacion_documento"] : null;
            
            // Log de información recibida
            writeToLog([
                'action' => 'guardaryeditar_inicio',
                'files_info' => $_FILES,
                'post_info' => array_diff_key($_POST, ['ruta_actual' => ''])
            ]);
            
            // Procesar archivo si se subió uno
            $ruta_documento = '';
            if (isset($_FILES["ruta_documento"]) && $_FILES["ruta_documento"]["error"] == 0) {
                writeToLog(['action' => 'procesando_documento_nuevo']);
                $ruta_documento = procesarDocumento($_FILES["ruta_documento"]);
                if ($ruta_documento === false) {
                    writeToLog(['error' => 'Falló el procesamiento del documento']);
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al procesar el documento. Revise el archivo de logs para más detalles."
                    ]);
                    exit;
                } else {
                    writeToLog(['success' => 'Documento procesado correctamente', 'nombre_archivo' => $ruta_documento]);
                }
            } elseif (isset($_POST["ruta_actual"])) {
                // Mantener archivo actual si existe
                $ruta_documento = $_POST["ruta_actual"];
                writeToLog(['action' => 'manteniendo_archivo_actual', 'archivo' => $ruta_documento]);
            } else {
                writeToLog(['action' => 'sin_archivo']);
            }
            
            // Validar que se tenga un archivo (nuevo o existente)
            if (empty($ruta_documento)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Es necesario proporcionar un archivo de documento"
                ]);
                exit;
            }
            
            // Validar campos obligatorios
            if (empty($titulo_documento) || empty($id_tipo_documento_documento)) {
                echo json_encode([
                    "success" => false,
                    "message" => "El título y el tipo de documento son obligatorios"
                ]);
                exit;
            }
            
            if (empty($_POST["id_documento"])) {
                // Insertar nuevo documento
                writeToLog([
                    'action' => 'antes_de_insertar',
                    'titulo_documento' => $titulo_documento,
                    'ruta_documento' => $ruta_documento,
                    'id_tipo_documento_documento' => $id_tipo_documento_documento,
                    'descripcion_documento' => $descripcion_documento,
                    'fecha_publicacion_documento' => $fecha_publicacion_documento
                ]);
                
                $resultado = $documento->insert_documento(
                    $titulo_documento,
                    $ruta_documento,
                    $id_tipo_documento_documento,
                    $descripcion_documento,
                    $fecha_publicacion_documento
                );
                
                writeToLog(['action' => 'resultado_insert', 'resultado' => $resultado]);
    
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'documento.php',
                        'Guardar documento',
                        "Documento guardado exitosamente",
                        "info"
                    );
    
                    echo json_encode([
                        "success" => true,
                        "message" => "Documento insertado correctamente"
                    ]);
                } else {
                    writeToLog(['error' => 'Fallo al insertar en base de datos', 'resultado' => $resultado]);
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al insertar el documento"
                    ]);
                }
    
            } else {
                // Actualizar documento existente
                $resultado = $documento->update_documento(
                    $_POST["id_documento"],
                    $titulo_documento,
                    $ruta_documento,
                    $id_tipo_documento_documento,
                    $descripcion_documento,
                    $fecha_publicacion_documento
                );
    
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'documento.php',
                        'Actualizar documento',
                        "Documento actualizado exitosamente",
                        "info"
                    );
    
                    echo json_encode([
                        "success" => true,
                        "message" => "Documento actualizado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al actualizar el documento"
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
        // Obtenemos el documento por ID
        $datos = $documento->get_documentoxid($_POST["id_documento"]);
        // Si hay datos, los devolvemos; si no, mandamos un JSON de error
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener el documento solicitado"
            ]);
        }
        break;

    case "eliminar":
        $documento->delete_documentoxid($_POST["id_documento"]);
        break;

    case "activar":
        $documento->activar_documentoxid($_POST["id_documento"]);
        break;

}
