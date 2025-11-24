<?php
require_once "../config/conexion.php";
require_once "../models/Paises.php";

$pais = new Paises();

switch ($_GET["op"]) {

    case "listar":
        $datos = $pais->get_pais();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "idpaises" => $row["idpaises"],
                "descrPaises" => $row["descrPaises"],
                "imagen" => $row["imagen"],
                "fech_crea" => $row["fech_crea"],
                "fech_modi" => $row["fech_modi"],
                "fech_elim" => $row["fech_elim"],
                "est" => $row["est"],
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
            // Recoger los datos del formulario
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
                // Si se ha subido una imagen, procesarla
    
                // Obtener la fecha y hora actual con microsegundos
                $fecha_hora = microtime(true);  // Obtener el timestamp con microsegundos
                $microsegundos = sprintf("%06d", ($fecha_hora - floor($fecha_hora)) * 1000000);  // Extraer los microsegundos
    
                // Formatear el nombre de la imagen con el formato 'dd-mm-aaaa_hh-mm-ss-microsegundos'
                $nombre_imagen = date("d-m-Y_H-i-s") . "-" . $microsegundos . "." . pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION);
    
                // Ruta donde se guardarán las imágenes
                $target_dir = "../public/assets/images/"; 
                $target_file = $target_dir . basename($nombre_imagen);  // Ruta completa del archivo
    
                // Verificar si la carpeta 'images' existe, si no, crearla
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);  // Crear la carpeta si no existe
                }
    
                // Mover la imagen a la carpeta
                if (move_uploaded_file($_FILES['imagen']['tmp_name'], $target_file)) {
                    // Si la imagen se sube correctamente, guardamos el registro
                    if (empty($_POST["idpaises"])) {
                        // Si es un nuevo país, insertamos el país con la imagen
                        $pais->insert_pais($_POST["descrPaises"], $nombre_imagen); // Insertamos la imagen con el nombre generado
                    } else {
                        // Si es una edición, actualizamos el país con el nuevo nombre de la imagen
                        $pais->update_pais($_POST["idpaises"], $nombre_imagen, $_POST["descrPaises"], $_POST["est"]);
                    }
                } else {
                    echo "Error al subir la imagen.";
                }
            } else {
                // Si no se sube una imagen, solo actualizamos los datos del país
                if (empty($_POST["idpaises"])) {
                    // Si es nuevo, insertamos el país sin imagen
                    $pais->insert_pais($_POST["descrPaises"]);
                } else {
                    // Si es una edición, actualizamos el país sin cambiar la imagen
                    $pais->update_pais($_POST["idpaises"], null, $_POST["descrPaises"], $_POST["est"]);
                }
            }
            break;
    

    case "mostrar":
        $datos = $pais->get_paisxid($_POST["idpaises"]);
        // if (is_array($datos) == true and count($datos) > 0) {
        //     foreach ($datos as $row) {
        //         $output["prod_id"] = $row["prod_id"];
        //         $output["prod_nom"] = $row["prod_nom"];
        //     }
        // }
        //echo json_encode($output);
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;


    case "eliminar":
        $pais->delete_paisxid($_POST["idpaises"]);
        break;

    case "activar":
        $pais->activar_paisxid($_POST["idpaises"]);
        break;
}
