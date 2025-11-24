
<?php
require_once "../config/conexion.php";
require_once "../models/Llamadas.php";
require_once "../models/Adjuntos.php";
require_once "../config/funciones.php";

$llamada = new Llamadas();
$adjunto = new Adjuntos();
$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $llamada->get_llamada();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_llamada" => $row["id_llamada"],
                "id_metodo" => $row["id_metodo"],
                "nombre_comunicante" => $row["nombre_comunicante"],
                "domicilio_instalacion" => $row["domicilio_instalacion"],
                "telefono_fijo" => $row["telefono_fijo"],
                "telefono_movil" => $row["telefono_movil"],
                "email_contacto" => $row["email_contacto"],
                "fecha_hora_preferida" => $row["fecha_hora_preferida"],
                "observaciones" => $row["observaciones"],
                "id_comercial_asignado" => $row["id_comercial_asignado"],
                "estado" => $row["estado"],
                "fecha_recepcion" => $row["fecha_recepcion"],  // Corregido aquí si es necesario
                "fecha_primer_contacto" => $row["fecha_primer_contacto"],
                "activo_llamada" => $row["activo_llamada"],
                "nombre_comercial" => $row["nombre_comercial"],
                "nombre_metodo" => $row["nombre_metodo"],  // Asegúrate de que este campo esté presente en la vista
                "descripcion_estado" => $row["descripcion_estado"],  // Corregido el nombre del campo
                "archivos_adjuntos" => $row["archivos_adjuntos"],
                "imagen_metodo" => $row["imagen_metodo"],  // Asegúrate de que este campo esté presente en la vista
                "tiene_contactos" => $row["tiene_contactos"],  // Nuevo campo
                "tiene_adjuntos" => $row["tiene_adjuntos"],  // Nuevo campo
                "estado_es_3" => $row["estado_es_3"],  // Nuevo campo
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

        case "listarPorComercial":
        // Suponemos que el id_comercial viene por GET o POST
        $id_comercial = $_GET['id_comercial'];

        if ($id_comercial > 0) {
            $datos = $llamada->get_llamada_por_comercial($id_comercial);
            $data = array();

            foreach ($datos as $row) {
                $data[] = array(
                    "id_llamada" => $row["id_llamada"],
                    "id_metodo" => $row["id_metodo"],
                    "nombre_comunicante" => $row["nombre_comunicante"],
                    "domicilio_instalacion" => $row["domicilio_instalacion"],
                    "telefono_fijo" => $row["telefono_fijo"],
                    "telefono_movil" => $row["telefono_movil"],
                    "email_contacto" => $row["email_contacto"],
                    "fecha_hora_preferida" => $row["fecha_hora_preferida"],
                    "observaciones" => $row["observaciones"],
                    "id_comercial_asignado" => $row["id_comercial_asignado"],
                    "estado" => $row["estado"],
                    "fecha_recepcion" => $row["fecha_recepcion"],
                    "fecha_primer_contacto" => $row["fecha_primer_contacto"],
                    "activo_llamada" => $row["activo_llamada"],
                    "nombre_comercial" => $row["nombre_comercial"],
                    "nombre_metodo" => $row["nombre_metodo"],
                    "descripcion_estado" => $row["descripcion_estado"],
                    "archivos_adjuntos" => $row["archivos_adjuntos"],
                    "imagen_metodo" => $row["imagen_metodo"],
                    "tiene_contactos" => $row["tiene_contactos"],
                    "tiene_adjuntos" => $row["tiene_adjuntos"],
                    "estado_es_3" => $row["estado_es_3"],
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

        } else {
            // Respuesta vacía o error si no hay id_comercial válido
            echo json_encode(array(
                "draw" => 1,
                "recordsTotal" => 0,
                "recordsFiltered" => 0,
                "data" => []
            ));
        }
        break;


        case "guardaryeditar":
            // Validar que los datos requeridos estén presentes
            if (empty($_POST["id_metodo"]) || 
                empty($_POST["nombre_comunicante"]) || 
                empty($_POST["domicilio_instalacion"]) || 
                empty($_POST["id_comercial_asignado"]) || 
                empty($_POST["estado"]) || 
                empty($_POST["fecha_recepcion"])) {
                
                echo json_encode([
                    "success" => false,
                    "message" => "Faltan datos obligatorios."
                ]);
                exit; // Terminamos aquí si falta información
            }
        
            if (empty($_POST["id_llamada"])) {
                // Convertir campos vacíos a null para los campos nullables
                $telefono_fijo = !empty($_POST["telefono_fijo"]) ? $_POST["telefono_fijo"] : null;
                $telefono_movil = !empty($_POST["telefono_movil"]) ? $_POST["telefono_movil"] : null;
                $email_contacto = !empty($_POST["email_contacto"]) ? $_POST["email_contacto"] : null;
                $fecha_hora_preferida = !empty($_POST["fecha_hora_preferida"]) ? $_POST["fecha_hora_preferida"] : null;
                $observaciones = !empty($_POST["observaciones"]) ? $_POST["observaciones"] : null;
                
                $llamada->insert_llamada(
                    $_POST["id_metodo"],
                    $_POST["nombre_comunicante"],
                    $_POST["domicilio_instalacion"],
                    $telefono_fijo,
                    $telefono_movil,
                    $email_contacto,
                    $fecha_hora_preferida,
                    $observaciones,
                    $_POST["id_comercial_asignado"],
                    $_POST["estado"],
                    $_POST["fecha_recepcion"]
                );
        
                $registro->registrarActividad(
                    'admin',
                    'llamadas.php',
                    'Guardar la llamada',
                    "Llamada guardada exitosamente",
                    "info"
                );
        
                // Respuesta de éxito
                echo json_encode([
                    "success" => true,
                    "message" => "Llamada guardada exitosamente"
                ]);
            } else {
                // Este es el camino para actualización
                // Convertir campos vacíos a null para los campos nullables
                $telefono_fijo = !empty($_POST["telefono_fijo"]) ? $_POST["telefono_fijo"] : null;
                $telefono_movil = !empty($_POST["telefono_movil"]) ? $_POST["telefono_movil"] : null;
                $email_contacto = !empty($_POST["email_contacto"]) ? $_POST["email_contacto"] : null;
                $fecha_hora_preferida = !empty($_POST["fecha_hora_preferida"]) ? $_POST["fecha_hora_preferida"] : null;
                $observaciones = !empty($_POST["observaciones"]) ? $_POST["observaciones"] : null;
        
                $llamada->update_llamada(
                    $_POST["id_llamada"],
                    $_POST["id_metodo"],
                    $_POST["nombre_comunicante"],
                    $_POST["domicilio_instalacion"],
                    $telefono_fijo,
                    $telefono_movil,
                    $email_contacto,
                    $fecha_hora_preferida,
                    $observaciones,
                    $_POST["id_comercial_asignado"],
                    $_POST["estado"],
                    $_POST["fecha_recepcion"]
                );
        
                $registro->registrarActividad(
                    'admin',
                    'llamadas.php',
                    'Actualizar la llamada',
                    "Llamada actualizada exitosamente",
                    "info"
                );
        
                // Respuesta de éxito
                echo json_encode([
                    "success" => true,
                    "message" => "Llamada actualizada exitosamente"
                ]);
            }
            break;

    case "mostrar":
        $datos = $llamada->get_llamadaxid($_POST["id_llamada"]);
      
        $registro->registrarActividad(
            'admin',
            'llamadas.php',
            'Obtener llamada seleccionada',
            "Llamada obtenida exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;


    case "eliminar":
        $llamada->delete_llamadaxid($_POST["id_llamada"]);

        $registro->registrarActividad(
            'admin',
            'llamadas.php',
            'Eliminar llamada seleccionada',
            "Llamada eliminada exitosamente ",
            "info"
        );

        break;

    case "activar":
        $llamada->activar_llamadaxid($_POST["id_llamada"]);

        $registro->registrarActividad(
            'admin',
            'llamadas.php',
            'Obtener llamada seleccionada',
            "Llamada activada exitosamente ",
            "info"
        );

        break;

        case "guardar_adjunto":
            // Configuración básica
            date_default_timezone_set('Europe/Madrid');
            $uploadDir = "../public/documentos/adjuntos/";
            
            // Tipos MIME permitidos y tamaño máximo (2MB)
            $allowedTypes = [
                'application/pdf' => 'pdf',
                'image/jpeg' => 'jpg',
                'image/png' => 'png'
            ];
            $maxSize = 2 * 1024 * 1024; // 2MB en bytes
        
            // Validación de ID de llamada
            if (empty($_POST['id_llamada'])) {
                $registro->registrarActividad(
                    'user',
                    'llamadas.php',
                    'Error validación',
                    'Intento de adjuntar archivo sin ID de llamada',
                    'error'
                );
                http_response_code(400);
                echo json_encode(['error' => 'ID de llamada no proporcionado']);
                exit;
            }
            $idLlamada = $_POST['id_llamada'];
        
            // Validación básica de archivos
            if (!isset($_FILES["archivo_adjunto"]) || empty($_FILES["archivo_adjunto"]["name"][0])) {
                $registro->registrarActividad(
                    'user',
                    'llamadas.php',
                    'Error archivo',
                    'No se recibieron archivos adjuntos para llamada ID: ' . $idLlamada,
                    'error'
                );
                http_response_code(400);
                echo json_encode(['error' => 'No se recibieron archivos adjuntos']);
                exit;
            }
        
            $fileCount = count($_FILES["archivo_adjunto"]["name"]);
            $documentos = []; // Array para almacenar los documentos procesados
        
            for ($i = 0; $i < $fileCount; $i++) {
                $fileName = $_FILES["archivo_adjunto"]["name"][$i];
                $fileTmpName = $_FILES["archivo_adjunto"]["tmp_name"][$i];
                $fileError = $_FILES["archivo_adjunto"]["error"][$i];
                $fileType = $_FILES["archivo_adjunto"]["type"][$i];
                $fileSize = $_FILES["archivo_adjunto"]["size"][$i];
                $fileDestination = $uploadDir . $fileName;
        
                // Validación de tipo
                if (!array_key_exists($fileType, $allowedTypes)) {
                    $registro->registrarActividad(
                        'user',
                        'llamadas.php',
                        'Tipo no permitido',
                        'Intento de subir tipo no permitido (' . $fileType . ') para llamada ID: ' . $idLlamada,
                        'warning'
                    );
                    http_response_code(415);
                    echo json_encode(['error' => 'Solo se permiten archivos PDF, JPG o PNG']);
                    exit;
                }
        
                // Validación de tamaño
                if ($fileSize > $maxSize) {
                    $registro->registrarActividad(
                        'user',
                        'llamadas.php',
                        'Archivo muy grande',
                        'Intento de subir archivo demasiado grande (' . round($fileSize/1024/1024, 2) . 'MB) para llamada ID: ' . $idLlamada,
                        'warning'
                    );
                    http_response_code(413);
                    echo json_encode(['error' => 'El archivo excede el tamaño máximo de 2MB']);
                    exit;
                }
        
                // Sistema de nombres únicos
                $fileInfo = pathinfo($fileDestination);
                $baseName = $fileInfo['filename'];
                $extension = $allowedTypes[$fileType];
                $nameDestination = $baseName . "." . $extension;
                $ii = 1;
                while (file_exists($uploadDir . $nameDestination)) {
                    $nameDestination = $baseName . "_" . $ii . "." . $extension;
                    $ii++;
                }
                $fileDestination = $uploadDir . $nameDestination;
        
                // Mover el archivo
                if ($fileError === UPLOAD_ERR_OK) {
                    if (move_uploaded_file($fileTmpName, $fileDestination)) {
                        $documentos[] = [
                            'nombre_original' => $fileName,
                            'nombre_guardado' => $nameDestination,
                            'tipo' => $fileType,
                            'ruta' => $fileDestination,
                            'fecha' => date('Y-m-d H:i:s')
                        ];
                    } else {
                        $registro->registrarActividad(
                            'user',
                            'llamadas.php',
                            'Error al mover archivo',
                            'Error al mover archivo adjunto para llamada ID: ' . $idLlamada,
                            'error'
                        );
                        http_response_code(500);
                        echo json_encode(['error' => 'Error al mover el archivo al servidor']);
                        exit;
                    }
                } else {
                    $registro->registrarActividad(
                        'user',
                        'llamadas.php',
                        'Error al subir archivo',
                        'Error al subir archivo adjunto para llamada ID: ' . $idLlamada . ' - Código error: ' . $fileError,
                        'error'
                    );
                    http_response_code(500);
                    echo json_encode(['error' => 'Error al subir el archivo']);
                    exit;
                }
            }
        
            try {
                // Insertar todos los adjuntos en una sola operación
                $resultado = $adjunto->insert_adjunto(
                    $idLlamada,
                    $documentos
                );
        
                // Registro de éxito
                $registro->registrarActividad(
                    'user',
                    'llamadas.php',
                    'Adjuntos guardados',
                    count($documentos) . ' adjuntos guardados para llamada ID: ' . $idLlamada,
                    'success'
                );
                
                echo json_encode([
                    'success' => true,
                    'total_archivos' => count($documentos),
                    'archivos' => array_column($documentos, 'nombre_guardado'),
                    'mensaje' => 'Archivos adjuntados correctamente'
                ]);
        
            } catch (PDOException $e) {
                // Eliminar archivos subidos si falla la BD
                foreach ($documentos as $documento) {
                    if (file_exists($documento['ruta'])) {
                        unlink($documento['ruta']);
                    }
                }
                
                $registro->registrarActividad(
                    'user',
                    'llamadas.php',
                    'Error BD',
                    'Error al insertar adjuntos para llamada ID: ' . $idLlamada . ' - ' . $e->getMessage(),
                    'error'
                );
                
                http_response_code(500);
                echo json_encode([
                    'error' => 'Error en base de datos',
                    'detalle' => $e->getMessage()
                ]);
            }
            break;

}
