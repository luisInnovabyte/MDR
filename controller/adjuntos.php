<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Adjuntos.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$adjunto = new Adjuntos();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $adjunto->get_adjunto();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_adjunto" => $row["id_adjunto"],
                "id_llamada" => $row["id_llamada"],
                "nombre_archivo" => $row["nombre_archivo"],
                "tipo" => $row["tipo"],
                "fecha_subida" => $row["fecha_subida"],
                "estado" => $row["estado"],
                "nombre_comunicante" => $row["nombre_comunicante"],
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
            if (empty($_POST["id_adjunto"])) {
                $adjunto->insert_adjunto($_POST["id_llamada"], $_POST["nombre_archivo"], $_POST["tipo"], $_POST["fecha_subida"]);
                
                $registro->registrarActividad(
                    'admin',
                    'adjuntos.php',
                    'Guardar el adjunto',
                    "Adjunto guardado exitosamente",
                    "info"
                );
            } else {
                
                $adjunto->update_adjunto(
                    $_POST["id_adjunto"],
                    $_POST["id_llamada"],
                    $_POST["nombre_archivo"],
                    $_POST["tipo"],
                    $_POST["fecha_subida"]
                );
                
                $registro->registrarActividad(
                    'admin',
                    'adjuntos.php',
                    'Actualizar el adjunto',
                    "Adjunto actualizado exitosamente",
                    "info"
                );
            }
            break;

    case "mostrar":
        $datos = $adjunto->get_adjuntoxid($_POST["id_adjunto"]);
        // if (is_array($datos) == true and count($datos) > 0) {
        //     foreach ($datos as $row) {
        //         $output["prod_id"] = $row["prod_id"];
        //         $output["prod_nom"] = $row["prod_nom"];
        //     }
        // }
        //echo json_encode($output);

        $registro->registrarActividad(
            'admin',
            'adjuntos.php',
            'Obtener adjunto seleccionado',
            "Adjunto obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $adjunto->delete_adjuntoxid($_POST["id_adjunto"]);

        $registro->registrarActividad(
            'admin',
            'adjuntos.php',
            'Eliminar adjunto seleccionado',
            "Adjunto eliminado exitosamente ",
            "info"
        );

        break;

        case "eliminar_adjunto":
            try {
                // Validar parámetros
                if (!isset($_POST['id_adjunto'])) {
                    throw new Exception("No se proporcionó ID de adjunto");
                }
        
                $id_adjunto = $_POST['id_adjunto'];
                
                // 1. Obtener información del adjunto
                $info_adjunto = $adjunto->get_adjunto_info($id_adjunto);
                
                if (!$info_adjunto) {
                    throw new Exception("Adjunto no encontrado en la base de datos");
                }
        
                $nombre_archivo = $info_adjunto['nombre_archivo'];
                $ruta_archivo = '../../public/documentos/adjuntos/' . $nombre_archivo;
                
                // 2. Eliminar archivo físico
                if (file_exists($ruta_archivo)) {
                    if (!unlink($ruta_archivo)) {
                        throw new Exception("No se pudo eliminar el archivo físico");
                    }
                    
                    // Registrar eliminación física
                    $registro->registrarActividad(
                        'admin',
                        'adjuntos.php',
                        'eliminar_adjunto',
                        "Archivo físico eliminado: $nombre_archivo",
                        "info"
                    );
                } else {
                    // Registrar que el archivo no existía físicamente
                    $registro->registrarActividad(
                        'admin',
                        'adjuntos.php',
                        'eliminar_adjunto',
                        "Archivo físico no encontrado: $nombre_archivo (pero se procederá a eliminar el registro)",
                        "warning"
                    );
                }
                
                // 3. Eliminar registro en BD
                if ($adjunto->delete_adjunto($id_adjunto)) {
                    // Registrar éxito
                    $registro->registrarActividad(
                        'admin',
                        'adjuntos.php',
                        'eliminar_adjunto',
                        "Adjunto eliminado completamente - ID: $id_adjunto, Archivo: $nombre_archivo",
                        "info"
                    );
                    
                    echo json_encode(['success' => true]);
                } else {
                    throw new Exception("No se pudo eliminar el registro en la base de datos");
                }
                
            } catch (Exception $e) {
                // Registrar error
                $registro->registrarActividad(
                    'admin',
                    'adjuntos.php',
                    'eliminar_adjunto',
                    "Error al eliminar adjunto ID: $id_adjunto | " . $e->getMessage(),
                    "error"
                );
                
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'error' => $e->getMessage()
                ]);
            }
            break;

    case "activar":
        $adjunto->activar_adjuntoxid($_POST["id_adjunto"]);

        $registro->registrarActividad(
            'admin',
            'adjuntos.php',
            'Obtener adjuntos seleccionado',
            "Adjunto activado exitosamente ",
            "info"
        );

        break;

    case "adjuntoDesdeLlamada":
        $adjunto->activar_adjuntoxid($_POST["id_adjunto"]);
    
        $registro->registrarActividad(
            'admin',
            'adjuntos.php',
            'Obtener adjuntos seleccionado',
            "Adjunto activado exitosamente ",
            "info"
        );
    
        break;

    case "obtener_adjuntos_por_llamada":
        $datos = $adjunto->obtenerAdjuntosPorIdLlamada($_POST["id_llamada"]);
        
        $registro->registrarActividad(
            'admin',
            'adjuntos.php',
            'Obtener adjuntos por llamada',
            "Adjuntos obtenidos para la llamada ID: " . $_POST["id_llamada"],
            "info"
        );
        
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;
}
