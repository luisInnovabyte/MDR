<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Estados.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$estado = new Estados();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $estado->get_estado();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_estado" => $row["id_estado"],
                "desc_estado" => $row["desc_estado"],
                "defecto_estado" => $row["defecto_estado"],
                "activo_estado" => $row["activo_estado"],
                "peso_estado" => $row["peso_estado"]
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
            if (empty($_POST["id_estado"])) {
                $estado->insert_estado($_POST["desc_estado"], $_POST["peso_estado"], $_POST["defecto_estado"]);
                
                $registro->registrarActividad(
                    'admin',
                    'estados.php',
                    'Guardar el estado',
                    "Estado guardado exitosamente",
                    "info"
                );
            } else {
                // Actualizado para incluir defecto_estado
                $estado->update_estado(
                    $_POST["id_estado"],
                    $_POST["desc_estado"],
                    $_POST["peso_estado"],
                    $_POST["defecto_estado"]
                );
                
                $registro->registrarActividad(
                    'admin',
                    'estados.php',
                    'Actualizar el estado',
                    "Estado actualizado exitosamente",
                    "info"
                );
            }
            break;

    case "mostrar":
        $datos = $estado->get_estadoxid($_POST["id_estado"]);
        // if (is_array($datos) == true and count($datos) > 0) {
        //     foreach ($datos as $row) {
        //         $output["prod_id"] = $row["prod_id"];
        //         $output["prod_nom"] = $row["prod_nom"];
        //     }
        // }
        //echo json_encode($output);

        $registro->registrarActividad(
            'admin',
            'estados.php',
            'Obtener estado seleccionado',
            "Estado obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $estado->delete_estadoxid($_POST["id_estado"]);

        $registro->registrarActividad(
            'admin',
            'estados.php',
            'Eliminar estado seleccionado',
            "Estado eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $estado->activar_estadoxid($_POST["id_estado"]);

        $registro->registrarActividad(
            'admin',
            'estados.php',
            'Obtener estados seleccionado',
            "Estado activado exitosamente ",
            "info"
        );

        break;

        case "comprobarPredeterminado":
            // Lógica para comprobar si hay un estado predeterminado
            $resultado = $estado->comprobarPredeterminado(); // Asumiendo que este método existe en el modelo
            header('Content-Type: application/json');
            echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
            break;
    
        case "quitarPredeterminado":
            // Lógica para quitar el estado predeterminado
            if (!empty($_POST["id_estado_predeterminado"])) {
                $estado->quitarPredeterminado($_POST["id_estado_predeterminado"]); // Método para quitar el predeterminado
                header('Content-Type: application/json');
                echo json_encode(["status" => "success", "message" => "Estado predeterminado quitado."]);
            } else {
                header('Content-Type: application/json');
                echo json_encode(["status" => "error", "message" => "ID de estado predeterminado no proporcionado."]);
            }
            break;
}
