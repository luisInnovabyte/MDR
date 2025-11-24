<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Categorias.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$categoria = new Categorias();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $categoria->get_categoria();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id" => $row["id"],
                "nombre" => $row["nombre"],
                "fecha" => $row["fecha"]
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
            if (empty($_POST["id"])) {
                $categoria->insert_categoria($_POST["nombre"], $_POST["fecha"]);
                
                $registro->registrarActividad(
                    'admin',
                    'categoria.php',
                    'Guardar el estado',
                    "Estado guardado exitosamente",
                    "info"
                );
            } else {
                // Actualizado para incluir defecto_estado
                $categoria->update_categoria(
                    $_POST["id"],
                    $_POST["nombre"],
                    $_POST["fecha"]
                );
                
                $registro->registrarActividad(
                    'admin',
                    'categorias.php',
                    'Actualizar la categoria',
                    "Categoria actualizado exitosamente",
                    "info"
                );
            }
            break;

    case "mostrar":
        $datos = $categoria->get_categoriaxid($_POST["id"]);
        $registro->registrarActividad(
            'admin',
            'categorias.php',
            'Obtener categoria seleccionado',
            "Categoria obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $categoria->delete_categoriaxid($_POST["id"]);

        $registro->registrarActividad(
            'admin',
            'categorias.php',
            'Eliminar categoria seleccionado',
            "Categoria eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $categoria->activar_categoriaxid($_POST["id"]);

        $registro->registrarActividad(
            'admin',
            'categorias.php',
            'Obtener categorias seleccionado',
            "Categoria activada exitosamente ",
            "info"
        );

        break;
}
