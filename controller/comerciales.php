
<?php
require_once "../config/conexion.php";
require_once "../models/Comerciales.php";
require_once "../config/funciones.php";

$comercial = new Comerciales();

switch ($_GET["op"]) {

    case "listar":
        $datos = $comercial->get_comercial();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_comercial" => $row["id_comercial"],
                "nombre" => $row["nombre"],
                "apellidos" => $row["apellidos"],
                "movil" => $row["movil"],
                "telefono" => $row["telefono"],
                "email" => $row["email"],
                "activo" => $row["activo"],
                "id_usuario" => $row["id_usuario"],
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
        if (empty($_POST["id_comercial"])) {
            // --- NUEVO COMERCIAL ---
            $resultado = $comercial->insert_comercial(
                $_POST["nombre"],
                $_POST["apellidos"],
                $_POST["movil"],
                $_POST["telefono"],
                $_POST["id_usuario"] // Nuevo parámetro
            );
            
            // Si se proporcionó una firma y se insertó correctamente el comercial
            if ($resultado && !empty($_POST["firma_base64"]) && $_POST["firma_base64"] !== 'null') {
                // Actualizar la firma del comercial por su id_usuario
                $comercial->update_firma_by_usuario(
                    $_POST["id_usuario"],
                    $_POST["firma_base64"]
                );
            }
            
        } else {
            // --- ACTUALIZACIÓN DE COMERCIAL EXISTENTE ---
            $comercial->update_comercial(
                $_POST["id_comercial"],
                $_POST["nombre"],
                $_POST["apellidos"],
                $_POST["movil"],
                $_POST["telefono"],
                $_POST["id_usuario"] // Nuevo parámetro
            );
            
            // Si se proporcionó una firma, actualizarla
            if (!empty($_POST["firma_base64"]) && $_POST["firma_base64"] !== 'null') {
                $comercial->update_firma_by_usuario(
                    $_POST["id_usuario"],
                    $_POST["firma_base64"]
                );
            }
        }
        break;


    case "mostrar":
        $datos = $comercial->get_comercialxid($_POST["id_comercial"]);
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
        $comercial->delete_comercialxid($_POST["id_comercial"]);

      

        break;

    case "activar":
        $comercial->activar_comercialxid($_POST["id_comercial"]);

      

        break;
}
