<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Metodos.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$metodo = new Metodos();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $metodo->get_metodo();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_metodo" => $row["id_metodo"],
                "nombre" => $row["nombre"],
                "permite_adjuntos" => $row["permite_adjuntos"],
                "estado" => $row["estado"],
                "imagen_metodo" => $row["imagen_metodo"],
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
            $uploadDir = "../public/img/metodos/";
            
            if (empty($_POST["id_metodo"])) {
                /////////////////////////////////////////////
                // INSERCIÓN (Nuevo método)               //
                /////////////////////////////////////////////
                $lastInsert = $metodo->insert_metodo($_POST["nombre"], $_POST["permite_adjuntos"]);
                
                // Manejo de imagen (1 archivo)
                if (isset($_FILES["imagen_metodo"]) && !empty($_FILES["imagen_metodo"]["name"])) {
                    $fileName = $_FILES["imagen_metodo"]["name"];
                    $fileTmpName = $_FILES["imagen_metodo"]["tmp_name"];
                    $fileError = $_FILES["imagen_metodo"]["error"];
                    $fileDestination = $uploadDir . $fileName;
        
                    // Sistema de nombres únicos
                    $fileInfo = pathinfo($fileDestination);
                    $baseName = $fileInfo['filename'];
                    $extension = $fileInfo['extension'];
                    $nameDestination = $baseName . "." . $extension;
                    $ii = 1;
                    while (file_exists($fileDestination)) {
                        $nameDestination = $baseName . "_" . $ii . "." . $extension;
                        $fileDestination = $uploadDir . $nameDestination;
                        $ii++;
                    }
        
                    if ($fileError === UPLOAD_ERR_OK) {
                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            $metodo->update_imagen_metodo($lastInsert, $nameDestination);
                        } else {
                            $registro->registrarActividad(
                                'admin',
                                'metodos.php',
                                'move_uploaded_file',
                                "Error al subir la imagen " . $fileDestination,
                                "error"
                            );
                            
                            http_response_code(510);
                            echo 'error: Destino no encontrado';
                            exit;
                        }
                    } else {
                        $registro->registrarActividad(
                            'admin',
                            'metodos.php',
                            'move_uploaded_file',
                            "Error al subir la imagen: UPLOAD_ERROR " . $fileDestination,
                            "error"
                        );
                        http_response_code(520);
                        echo 'error: UPLOAD_ERROR';
                        exit;
                    }
                }
                ///////////////////////////////////////////
                //          FIN DE LA INSERCIÓN          //
                ///////////////////////////////////////////
            } else {
                ///////////////////////////////////////////
                //         EDICIÓN DE MÉTODO             //
                ///////////////////////////////////////////
                $id_metodo = $_POST["id_metodo"];
                
                // 1. Obtener la imagen anterior antes de actualizar
                $imagen_anterior = $metodo->get_imagen_metodo($id_metodo);
                
                // 2. Actualizar los datos del método
                $metodo->update_metodo($id_metodo, $_POST["nombre"], $_POST["permite_adjuntos"]);
                
                // 3. Manejo de imagen (1 archivo)
                if (isset($_FILES["imagen_metodo"]) && !empty($_FILES["imagen_metodo"]["name"])) {
                    $fileName = $_FILES["imagen_metodo"]["name"];
                    $fileTmpName = $_FILES["imagen_metodo"]["tmp_name"];
                    $fileError = $_FILES["imagen_metodo"]["error"];
                    $fileDestination = $uploadDir . $fileName;
        
                    // Sistema de nombres únicos
                    $fileInfo = pathinfo($fileDestination);
                    $baseName = $fileInfo['filename'];
                    $extension = $fileInfo['extension'];
                    $nameDestination = $baseName . "." . $extension;
                    $ii = 1;
                    while (file_exists($fileDestination)) {
                        $nameDestination = $baseName . "_" . $ii . "." . $extension;
                        $fileDestination = $uploadDir . $nameDestination;
                        $ii++;
                    }
        
                    if ($fileError === UPLOAD_ERR_OK) {
                        // Eliminar la imagen anterior si existe
                        if (!empty($imagen_anterior)) {
                            $ruta_anterior = $uploadDir . $imagen_anterior;
                            if (file_exists($ruta_anterior)) {
                                if (!unlink($ruta_anterior)) {
                                    $registro->registrarActividad(
                                        'admin',
                                        'metodos.php',
                                        'unlink',
                                        "Error al eliminar imagen anterior: " . $ruta_anterior,
                                        "error"
                                    );
                                }
                            }
                        }
        
                        // Subir la nueva imagen
                        if (move_uploaded_file($fileTmpName, $fileDestination)) {
                            $metodo->update_imagen_metodo($id_metodo, $nameDestination);
                        } else {
                            $registro->registrarActividad(
                                'admin',
                                'metodos.php',
                                'move_uploaded_file',
                                "Error al subir la imagen " . $fileDestination,
                                "error"
                            );
        
                            http_response_code(520);
                            echo 'error: UPLOAD_ERROR';
                            exit;
                        }
                    }
                }
            }
            
            // Registro de actividad (común para inserción y edición)
            $registro->registrarActividad(
                'admin',
                'metodos.php',
                empty($_POST["id_metodo"]) ? 'Guardar método' : 'Actualizar método',
                "Operación realizada exitosamente",
                "info"
            );
            break;

    case "mostrar":
        $datos = $metodo->get_metodoxid($_POST["id_metodo"]);
        // if (is_array($datos) == true and count($datos) > 0) {
        //     foreach ($datos as $row) {
        //         $output["prod_id"] = $row["prod_id"];
        //         $output["prod_nom"] = $row["prod_nom"];
        //     }
        // }
        //echo json_encode($output);

        $registro->registrarActividad(
            'admin',
            'metodos.php',
            'Obtener metodo seleccionado',
            "Metodo obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $metodo->delete_metodoxid($_POST["id_metodo"]);

        $registro->registrarActividad(
            'admin',
            'metodos.php',
            'Eliminar metodo seleccionado',
            "Metodo eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $metodo->activar_metodoxid($_POST["id_metodo"]);

        $registro->registrarActividad(
            'admin',
            'metodos.php',
            'Obtener metodo seleccionado',
            "Metodo activado exitosamente ",
            "info"
        );

        break;

        case "borrarImagen":
            // Obtener parámetros
            $id_metodo = $_POST["id_metodo"];
            $imageName = $_POST["imageName"];
            
            // Log de desarrollo
            writeToLog("Intento de borrar imagen - ID Método: $id_metodo, Imagen: $imageName");
        
            // Ruta completa de la imagen
            $camino = "../public/img/metodos/";
            $ruta = $camino . $imageName;
            writeToLog("Ruta completa: $ruta");
        
            if (file_exists($ruta)) {
                // Eliminar la imagen físicamente
                if (unlink($ruta)) {
                    writeToLog("Archivo eliminado físicamente: $ruta");
        
                    // Eliminar referencia de la imagen en la base de datos
                    if ($metodo->delete_imagen_metodoxid($id_metodo, $imageName)) {
                        writeToLog("Referencia de imagen eliminada en BD para método ID: $id_metodo");
        
                        // Asignar la imagen predeterminada a este método
                        $imagenPredeterminada = 'default_method.png'; // Nombre de la imagen predeterminada
                        if ($metodo->actualizar_imagen_metodoxid($id_metodo, $imagenPredeterminada)) {
                            writeToLog("Imagen predeterminada asignada correctamente para el método ID: $id_metodo");
                            
                            // Log de producción
                            $registro->registrarActividad(
                                'admin',
                                'metodos.php',
                                'borrarImagen',
                                "Imagen $imageName eliminada y reemplazada con imagen predeterminada correctamente para método ID: $id_metodo",
                                "info"
                            );
                        } else {
                            $errorMsg = "Error al asignar imagen predeterminada en BD";
                            writeToLog($errorMsg);
        
                            $registro->registrarActividad(
                                'admin',
                                'metodos.php',
                                'borrarImagen',
                                "$errorMsg - Método ID: $id_metodo",
                                "error"
                            );
                            http_response_code(500);
                            echo 'error: ' . $errorMsg;
                            exit;
                        }
                    } else {
                        $errorMsg = "Error al eliminar referencia en BD";
                        writeToLog($errorMsg);
        
                        $registro->registrarActividad(
                            'admin',
                            'metodos.php',
                            'borrarImagen',
                            "$errorMsg - Método ID: $id_metodo",
                            "error"
                        );
                        http_response_code(500);
                        echo 'error: ' . $errorMsg;
                        exit;
                    }
                } else {
                    $errorMsg = "No es posible borrar la imagen físicamente";
                    writeToLog($errorMsg . " - Ruta: $ruta");
        
                    $registro->registrarActividad(
                        'admin',
                        'metodos.php',
                        'borrarImagen',
                        "$errorMsg: $ruta",
                        "error"
                    );
                    http_response_code(500);
                    echo 'error: ' . $errorMsg;
                    exit;
                }
            } else {
                $errorMsg = "El fichero no existe";
                writeToLog($errorMsg . " - Ruta: $ruta");
        
                $registro->registrarActividad(
                    'admin',
                    'metodos.php',
                    'borrarImagen',
                    "$errorMsg: $ruta",
                    "error"
                );
                http_response_code(500);
                echo 'error: ' . $errorMsg;
                exit;
            }
            break;

            case "obtenerImagenMetodo":
            $id_metodo = $_POST["id_metodo"];

            // Log de desarrollo
            writeToLog("Intento de obtener imagen - ID Método: $id_metodo");

            $datos = $metodo->get_imagen_metodo($id_metodo);

            $registro->registrarActividad(
            'admin',
            'metodos.php',
            'ObtenerImagen',
            "Metodo Obtener Imagen exitosamente ",
            "info"
        );

            header('Content-Type: application/json');
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            break;
}
