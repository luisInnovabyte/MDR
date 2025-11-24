<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Proveedores.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$proveedor = new Proveedores();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $proveedor->get_proveedores();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_proveedor" => $row["id_proveedor"],
                "codigo_proveedor" => $row["codigo_proveedor"],
                "nombre_proveedor" => $row["nombre_proveedor"],
                "direccion_proveedor" => $row["direccion_proveedor"],
                "cp_proveedor" => $row["cp_proveedor"],
                "poblacion_proveedor" => $row["poblacion_proveedor"],
                "provincia_proveedor" => $row["provincia_proveedor"],
                "nif_proveedor" => $row["nif_proveedor"],
                "telefono_proveedor" => $row["telefono_proveedor"],
                "fax_proveedor" => $row["fax_proveedor"],
                "web_proveedor" => $row["web_proveedor"],
                "email_proveedor" => $row["email_proveedor"],
                "persona_contacto_proveedor" => $row["persona_contacto_proveedor"],
                "direccion_sat_proveedor" => $row["direccion_sat_proveedor"],
                "cp_sat_proveedor" => $row["cp_sat_proveedor"],
                "poblacion_sat_proveedor" => $row["poblacion_sat_proveedor"],
                "provincia_sat_proveedor" => $row["provincia_sat_proveedor"],
                "telefono_sat_proveedor" => $row["telefono_sat_proveedor"],
                "fax_sat_proveedor" => $row["fax_sat_proveedor"],
                "email_sat_proveedor" => $row["email_sat_proveedor"],
                "observaciones_proveedor" => $row["observaciones_proveedor"],
                "cantidad_contactos" => isset($row["cantidad_contactos"]) ? intval($row["cantidad_contactos"]) : 0,
                "activo_proveedor" => $row["activo_proveedor"],
                "created_at_proveedor" => $row["created_at_proveedor"],
                "updated_at_proveedor" => $row["updated_at_proveedor"]
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
            if (empty($_POST["id_proveedor"])) {
                $resultado = $proveedor->insert_proveedor(
                    $_POST["codigo_proveedor"], 
                    $_POST["nombre_proveedor"], 
                    $_POST["direccion_proveedor"], 
                    $_POST["cp_proveedor"], 
                    $_POST["poblacion_proveedor"], 
                    $_POST["provincia_proveedor"], 
                    $_POST["nif_proveedor"], 
                    $_POST["telefono_proveedor"], 
                    $_POST["fax_proveedor"], 
                    $_POST["web_proveedor"], 
                    $_POST["email_proveedor"], 
                    $_POST["persona_contacto_proveedor"], 
                    $_POST["direccion_sat_proveedor"], 
                    $_POST["cp_sat_proveedor"], 
                    $_POST["poblacion_sat_proveedor"], 
                    $_POST["provincia_sat_proveedor"], 
                    $_POST["telefono_sat_proveedor"], 
                    $_POST["fax_sat_proveedor"], 
                    $_POST["email_sat_proveedor"], 
                    $_POST["observaciones_proveedor"]
                );
                
                if ($resultado !== false && $resultado > 0) {
                    $registro->registrarActividad(
                        'admin',
                        'proveedor.php',
                        'Guardar el proveedor',
                        "Proveedor guardado exitosamente con ID: $resultado",
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Proveedor guardado exitosamente',
                        'id_proveedor' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al insertar el proveedor en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
                
            } else {
                $resultado = $proveedor->update_proveedor(
                    $_POST["id_proveedor"],
                    $_POST["codigo_proveedor"], 
                    $_POST["nombre_proveedor"], 
                    $_POST["direccion_proveedor"], 
                    $_POST["cp_proveedor"], 
                    $_POST["poblacion_proveedor"], 
                    $_POST["provincia_proveedor"], 
                    $_POST["nif_proveedor"], 
                    $_POST["telefono_proveedor"], 
                    $_POST["fax_proveedor"], 
                    $_POST["web_proveedor"], 
                    $_POST["email_proveedor"], 
                    $_POST["persona_contacto_proveedor"], 
                    $_POST["direccion_sat_proveedor"], 
                    $_POST["cp_sat_proveedor"], 
                    $_POST["poblacion_sat_proveedor"], 
                    $_POST["provincia_sat_proveedor"], 
                    $_POST["telefono_sat_proveedor"], 
                    $_POST["fax_sat_proveedor"], 
                    $_POST["email_sat_proveedor"], 
                    $_POST["observaciones_proveedor"]
                );
                
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'proveedor.php',
                        'Actualizar el proveedor',
                        "Proveedor actualizado exitosamente ID: " . $_POST["id_proveedor"],
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Proveedor actualizado exitosamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al actualizar el proveedor en la base de datos'
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
        $datos = $proveedor->get_proveedorxid($_POST["id_proveedor"]);

        $registro->registrarActividad(
            'admin',
            'proveedor.php',
            'Obtener proveedor seleccionado',
            "Proveedor obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $proveedor->delete_proveedorxid($_POST["id_proveedor"]);

        $registro->registrarActividad(
            'admin',
            'proveedor.php',
            'Eliminar proveedor seleccionado',
            "Proveedor eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $proveedor->activar_proveedorxid($_POST["id_proveedor"]);

        $registro->registrarActividad(
            'admin',
            'proveedor.php',
            'Activar proveedor seleccionado',
            "Proveedor activado exitosamente ",
            "info"
        );

        break;

    case "verificar":
        $resultado = $proveedor->verificarProveedor(
            $_POST["codigo_proveedor"],
            $_POST["nombre_proveedor"] ?? null,
            $_POST["id_proveedor"] ?? null
        );
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "listar_disponibles":
        $datos = $proveedor->get_proveedores_disponibles();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_proveedor" => $row["id_proveedor"],
                "codigo_proveedor" => $row["codigo_proveedor"],
                "nombre_proveedor" => $row["nombre_proveedor"],
                "direccion_proveedor" => $row["direccion_proveedor"],
                "cp_proveedor" => $row["cp_proveedor"],
                "poblacion_proveedor" => $row["poblacion_proveedor"],
                "provincia_proveedor" => $row["provincia_proveedor"],
                "nif_proveedor" => $row["nif_proveedor"],
                "telefono_proveedor" => $row["telefono_proveedor"],
                "email_proveedor" => $row["email_proveedor"],
                "persona_contacto_proveedor" => $row["persona_contacto_proveedor"],
                "activo_proveedor" => $row["activo_proveedor"]
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