<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Productos.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$producto = new Productos();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $producto->get_producto();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id" => $row["id"],
                "nombre" => $row["nombre"],
                "categoria_id" => $row["categoria_id"]
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
            header('Content-Type: application/json'); // Añade esta línea
            
            try {
                if (empty($_POST["id"])) {
                    $result = $producto->insert_producto($_POST["nombre"], $_POST["categoria_id"]);
                    
                    $registro->registrarActividad(
                        'admin',
                        'productos.php', // Corregido a productos.php
                        'Guardar producto',
                        "Producto guardado exitosamente",
                        "info"
                    );
        
                    echo json_encode([
                        'success' => true,
                        'message' => 'Producto creado correctamente'
                    ]);
                    exit; // Añade exit después del echo
                } else {
                    $producto->update_producto(
                        $_POST["id"],
                        $_POST["nombre"],
                        $_POST["categoria_id"]
                    );
                    
                    $registro->registrarActividad(
                        'admin',
                        'productos.php',
                        'Actualizar producto',
                        "Producto actualizado exitosamente",
                        "info"
                    );
        
                    echo json_encode([
                        'success' => true,
                        'message' => 'Producto actualizado correctamente'
                    ]);
                    exit; // Añade exit después del echo
                }
            } catch (Exception $e) {
                http_response_code(500);
                echo json_encode([
                    'success' => false,
                    'message' => 'Error: ' . $e->getMessage()
                ]);
                exit;
            }
            break;

    case "mostrar":
        $datos = $producto->get_productoxid($_POST["id"]);
        $registro->registrarActividad(
            'admin',
            'productos.php',
            'Obtener producto seleccionado',
            "Producto obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $producto->delete_productoxid($_POST["id"]);

        $registro->registrarActividad(
            'admin',
            'productos.php',
            'Eliminar producto seleccionado',
            "Producto eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $producto->activar_productoxid($_POST["id"]);

        $registro->registrarActividad(
            'admin',
            'productos.php',
            'Obtener producto seleccionado',
            "Producto activado exitosamente ",
            "info"
        );

        break;

        case "escogerPorCategoria":
            $datos = $producto->get_productos_por_id_categoria($_GET["id"]);
            
            $registro->registrarActividad(
                'admin',
                'productos.php',
                'Obtener producto seleccionado',
                "Producto obtenido exitosamente ",
                "info"
            );
        
            header('Content-Type: application/json');
            echo json_encode(["data" => $datos], JSON_UNESCAPED_UNICODE);
            break;
        
}
