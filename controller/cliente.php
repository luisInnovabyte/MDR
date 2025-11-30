<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Clientes.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$cliente = new Clientes();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $cliente->get_clientes();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_cliente" => $row["id_cliente"],
                "codigo_cliente" => $row["codigo_cliente"],
                "nombre_cliente" => $row["nombre_cliente"],
                "direccion_cliente" => $row["direccion_cliente"],
                "cp_cliente" => $row["cp_cliente"],
                "poblacion_cliente" => $row["poblacion_cliente"],
                "provincia_cliente" => $row["provincia_cliente"],
                "nif_cliente" => $row["nif_cliente"],
                "telefono_cliente" => $row["telefono_cliente"],
                "fax_cliente" => $row["fax_cliente"],
                "web_cliente" => $row["web_cliente"],
                "email_cliente" => $row["email_cliente"],
                "nombre_facturacion_cliente" => $row["nombre_facturacion_cliente"],
                "direccion_facturacion_cliente" => $row["direccion_facturacion_cliente"],
                "cp_facturacion_cliente" => $row["cp_facturacion_cliente"],
                "poblacion_facturacion_cliente" => $row["poblacion_facturacion_cliente"],
                "provincia_facturacion_cliente" => $row["provincia_facturacion_cliente"],
                "cantidad_contactos" => isset($row["cantidad_contactos_cliente"]) ? intval($row["cantidad_contactos_cliente"]) : 0,
                "observaciones_cliente" => $row["observaciones_cliente"],
                "activo_cliente" => $row["activo_cliente"],
                "created_at_cliente" => $row["created_at_cliente"],
                "updated_at_cliente" => $row["updated_at_cliente"]
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
        try {
            if (empty($_POST["id_cliente"])) {
                $resultado = $cliente->insert_cliente(
                    $_POST["codigo_cliente"], 
                    $_POST["nombre_cliente"], 
                    $_POST["direccion_cliente"], 
                    $_POST["cp_cliente"], 
                    $_POST["poblacion_cliente"], 
                    $_POST["provincia_cliente"], 
                    $_POST["nif_cliente"], 
                    $_POST["telefono_cliente"], 
                    $_POST["fax_cliente"], 
                    $_POST["web_cliente"], 
                    $_POST["email_cliente"], 
                    $_POST["nombre_facturacion_cliente"], 
                    $_POST["direccion_facturacion_cliente"], 
                    $_POST["cp_facturacion_cliente"], 
                    $_POST["poblacion_facturacion_cliente"], 
                    $_POST["provincia_facturacion_cliente"], 
                    $_POST["observaciones_cliente"]
                );
                
                if ($resultado !== false && $resultado > 0) {
                    $registro->registrarActividad(
                        'admin',
                        'cliente.php',
                        'Guardar el cliente',
                        "Cliente guardado exitosamente con ID: $resultado",
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Cliente guardado exitosamente',
                        'id_cliente' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al insertar el cliente en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
                
            } else {
                $resultado = $cliente->update_cliente(
                    $_POST["id_cliente"],
                    $_POST["codigo_cliente"], 
                    $_POST["nombre_cliente"], 
                    $_POST["direccion_cliente"], 
                    $_POST["cp_cliente"], 
                    $_POST["poblacion_cliente"], 
                    $_POST["provincia_cliente"], 
                    $_POST["nif_cliente"], 
                    $_POST["telefono_cliente"], 
                    $_POST["fax_cliente"], 
                    $_POST["web_cliente"], 
                    $_POST["email_cliente"], 
                    $_POST["nombre_facturacion_cliente"], 
                    $_POST["direccion_facturacion_cliente"], 
                    $_POST["cp_facturacion_cliente"], 
                    $_POST["poblacion_facturacion_cliente"], 
                    $_POST["provincia_facturacion_cliente"], 
                    $_POST["observaciones_cliente"]
                );
                
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'cliente.php',
                        'Actualizar el cliente',
                        "Cliente actualizado exitosamente ID: " . $_POST["id_cliente"],
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Cliente actualizado exitosamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al actualizar el cliente en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Error detallado: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "mostrar":
        $datos = $cliente->get_clientexid($_POST["id_cliente"]);

        $registro->registrarActividad(
            'admin',
            'cliente.php',
            'Obtener cliente seleccionado',
            "Cliente obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $cliente->delete_clientexid($_POST["id_cliente"]);

        $registro->registrarActividad(
            'admin',
            'cliente.php',
            'Eliminar cliente seleccionado',
            "Cliente eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $cliente->activar_clientexid($_POST["id_cliente"]);

        $registro->registrarActividad(
            'admin',
            'cliente.php',
            'Activar cliente seleccionado',
            "Cliente activado exitosamente ",
            "info"
        );

        break;

    case "verificar":
        $resultado = $cliente->verificarCliente(
            $_POST["codigo_cliente"],
            $_POST["nombre_cliente"] ?? null,
            $_POST["id_cliente"] ?? null
        );
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "listar_disponibles":
        $datos = $cliente->get_clientes_disponibles();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_cliente" => $row["id_cliente"],
                "codigo_cliente" => $row["codigo_cliente"],
                "nombre_cliente" => $row["nombre_cliente"],
                "direccion_cliente" => $row["direccion_cliente"],
                "cp_cliente" => $row["cp_cliente"],
                "poblacion_cliente" => $row["poblacion_cliente"],
                "provincia_cliente" => $row["provincia_cliente"],
                "nif_cliente" => $row["nif_cliente"],
                "telefono_cliente" => $row["telefono_cliente"],
                "email_cliente" => $row["email_cliente"],
                "activo_cliente" => $row["activo_cliente"]
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
}
?>