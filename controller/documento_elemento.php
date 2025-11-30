<?php
require_once "../config/conexion.php";
require_once "../models/Documento_elemento.php";
require_once '../config/funciones.php';

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/documento_elemento_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] documento_elemento.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);

// CREATE TABLE documento_elemento (
//     id_documento_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     id_elemento INT UNSIGNED NOT NULL,
//     descripcion_documento_elemento TEXT,
//     tipo_documento_elemento VARCHAR(100),
//     archivo_documento VARCHAR(500) NOT NULL,
//     privado_documento BOOLEAN DEFAULT FALSE,
//     observaciones_documento TEXT,
//     activo_documento BOOLEAN DEFAULT TRUE,
//     created_at_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_documento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
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
            
            $logFile = $logDir . "documento_elemento_debug_" . date("Ymd") . ".txt";
            
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

// Función para procesar documento de elemento
function procesarDocumentoElemento($archivo)
{
    try {
        // Log de información del archivo recibido
        writeToLog([
            'action' => 'procesarDocumentoElemento_inicio',
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
        $directorio = __DIR__ . "/../public/img/docs_elementos/";
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
        $nombreArchivo = 'documento_elemento_' . uniqid() . '.' . $extension;
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
$documento_elemento = new Documento_elemento();

switch ($_GET["op"]) {

    case "listar":
        // Verificar si se solicita filtrado por elemento
        if (isset($_GET["id_elemento"]) && !empty($_GET["id_elemento"])) {
            $datos = $documento_elemento->get_documentos_por_elemento($_GET["id_elemento"]);
        } else {
            $datos = $documento_elemento->get_documento_elemento();
        }
        
        $data = array();
        
        if ($datos !== false) {
            foreach ($datos as $row) {
                $data[] = array(
                    "id_documento_elemento" => $row["id_documento_elemento"],
                    "id_elemento" => $row["id_elemento"],
                    "codigo_elemento" => $row["codigo_elemento"] ?? '',
                    "nombre_elemento" => $row["nombre_elemento"] ?? '',
                    "descripcion_documento_elemento" => $row["descripcion_documento_elemento"] ?? '',
                    "tipo_documento_elemento" => $row["tipo_documento_elemento"] ?? '',
                    "archivo_documento" => $row["archivo_documento"],
                    "privado_documento" => $row["privado_documento"],
                    "observaciones_documento" => $row["observaciones_documento"] ?? '',
                    "activo_documento" => $row["activo_documento"],
                    "created_at_documento" => $row["created_at_documento"],
                    "updated_at_documento" => $row["updated_at_documento"]
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
        $datos = $documento_elemento->get_documento_elemento_disponible();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_documento_elemento" => $row["id_documento_elemento"],
                "id_elemento" => $row["id_elemento"],
                "descripcion_documento_elemento" => $row["descripcion_documento_elemento"] ?? '',
                "tipo_documento_elemento" => $row["tipo_documento_elemento"] ?? '',
                "archivo_documento" => $row["archivo_documento"],
                "privado_documento" => $row["privado_documento"],
                "observaciones_documento" => $row["observaciones_documento"] ?? '',
                "activo_documento" => $row["activo_documento"],
                "created_at_documento" => $row["created_at_documento"],
                "updated_at_documento" => $row["updated_at_documento"]
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

        $datos = $documento_elemento->get_documentos_por_elemento($_POST["id_elemento"]);
        $data = array();
        
        if ($datos !== false) {
            foreach ($datos as $row) {
                $data[] = array(
                    "id_documento_elemento" => $row["id_documento_elemento"],
                    "id_elemento" => $row["id_elemento"],
                    "descripcion_documento_elemento" => $row["descripcion_documento_elemento"] ?? '',
                    "tipo_documento_elemento" => $row["tipo_documento_elemento"] ?? '',
                    "archivo_documento" => $row["archivo_documento"],
                    "privado_documento" => $row["privado_documento"],
                    "observaciones_documento" => $row["observaciones_documento"] ?? '',
                    "activo_documento" => $row["activo_documento"],
                    "created_at_documento" => $row["created_at_documento"],
                    "updated_at_documento" => $row["updated_at_documento"]
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
            $descripcion_documento_elemento = !empty($_POST["descripcion_documento_elemento"]) ? $_POST["descripcion_documento_elemento"] : null;
            $tipo_documento_elemento = !empty($_POST["tipo_documento_elemento"]) ? $_POST["tipo_documento_elemento"] : null;
            $privado_documento = isset($_POST["privado_documento"]) ? (bool)$_POST["privado_documento"] : false;
            $observaciones_documento = !empty($_POST["observaciones_documento"]) ? $_POST["observaciones_documento"] : null;
            
            // Log de información recibida
            writeToLog([
                'action' => 'guardaryeditar_inicio',
                'files_info' => $_FILES,
                'post_info' => array_diff_key($_POST, ['archivo_actual' => ''])
            ]);
            
            // Procesar archivo si se subió uno
            $archivo_documento = '';
            if (isset($_FILES["archivo_documento"]) && $_FILES["archivo_documento"]["error"] == 0) {
                writeToLog(['action' => 'procesando_documento_nuevo']);
                $archivo_documento = procesarDocumentoElemento($_FILES["archivo_documento"]);
                if ($archivo_documento === false) {
                    writeToLog(['error' => 'Falló el procesamiento del documento']);
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al procesar el documento. Revise el archivo de logs para más detalles."
                    ]);
                    exit;
                } else {
                    writeToLog(['success' => 'Documento procesado correctamente', 'nombre_archivo' => $archivo_documento]);
                }
            } elseif (isset($_POST["archivo_actual"])) {
                // Mantener archivo actual si existe
                $archivo_documento = $_POST["archivo_actual"];
                writeToLog(['action' => 'manteniendo_archivo_actual', 'archivo' => $archivo_documento]);
            } else {
                writeToLog(['action' => 'sin_archivo']);
            }
            
            // Validar que se tenga un archivo (nuevo o existente)
            if (empty($archivo_documento)) {
                echo json_encode([
                    "success" => false,
                    "message" => "Es necesario proporcionar un archivo de documento"
                ]);
                exit;
            }
            
            if (empty($_POST["id_documento_elemento"])) {
                // Insertar nuevo documento
                writeToLog([
                    'action' => 'antes_de_insertar',
                    'id_elemento' => $id_elemento,
                    'archivo_documento' => $archivo_documento,
                    'descripcion_documento_elemento' => $descripcion_documento_elemento,
                    'tipo_documento_elemento' => $tipo_documento_elemento,
                    'privado_documento' => $privado_documento,
                    'observaciones_documento' => $observaciones_documento
                ]);
                
                $resultado = $documento_elemento->insert_documento_elemento(
                    $id_elemento,
                    $archivo_documento,
                    $descripcion_documento_elemento,
                    $tipo_documento_elemento,
                    $privado_documento,
                    $observaciones_documento
                );
                
                writeToLog(['action' => 'resultado_insert', 'resultado' => $resultado]);
    
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'documento_elemento.php',
                        'Guardar documento de elemento',
                        "Documento de elemento guardado exitosamente",
                        "info"
                    );
    
                    echo json_encode([
                        "success" => true,
                        "message" => "Documento de elemento insertado correctamente"
                    ]);
                } else {
                    writeToLog(['error' => 'Fallo al insertar en base de datos', 'resultado' => $resultado]);
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al insertar el documento de elemento"
                    ]);
                }
    
            } else {
                // Actualizar documento existente
                $resultado = $documento_elemento->update_documento_elemento(
                    $_POST["id_documento_elemento"],
                    $id_elemento,
                    $archivo_documento,
                    $descripcion_documento_elemento,
                    $tipo_documento_elemento,
                    $privado_documento,
                    $observaciones_documento
                );
    
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'documento_elemento.php',
                        'Actualizar documento de elemento',
                        "Documento de elemento actualizado exitosamente",
                        "info"
                    );
    
                    echo json_encode([
                        "success" => true,
                        "message" => "Documento de elemento actualizado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al actualizar el documento de elemento"
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
        $datos = $documento_elemento->get_documento_elementoxid($_POST["id_documento_elemento"]);
        // Si hay datos, los devolvemos; si no, mandamos un JSON de error
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener el documento de elemento solicitado"
            ]);
        }
        break;

    case "eliminar":
        $documento_elemento->delete_documento_elementoxid($_POST["id_documento_elemento"]);
        break;

    case "activar":
        $documento_elemento->activar_documento_elementoxid($_POST["id_documento_elemento"]);
        break;

}
