<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Proveedores_contacto.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$proveedores_contacto = new Proveedores_contacto();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $proveedores_contacto->get_contactos_proveedor();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_contacto_proveedor" => $row["id_contacto_proveedor"],
                "id_proveedor" => $row["id_proveedor"],
                "nombre_contacto_proveedor" => $row["nombre_contacto_proveedor"],
                "apellidos_contacto_proveedor" => $row["apellidos_contacto_proveedor"],
                "cargo_contacto_proveedor" => $row["cargo_contacto_proveedor"],
                "departamento_contacto_proveedor" => $row["departamento_contacto_proveedor"],
                "telefono_contacto_proveedor" => $row["telefono_contacto_proveedor"],
                "movil_contacto_proveedor" => $row["movil_contacto_proveedor"],
                "email_contacto_proveedor" => $row["email_contacto_proveedor"],
                "extension_contacto_proveedor" => $row["extension_contacto_proveedor"],
                "principal_contacto_proveedor" => $row["principal_contacto_proveedor"],
                "observaciones_contacto_proveedor" => $row["observaciones_contacto_proveedor"],
                "activo_contacto_proveedor" => $row["activo_contacto_proveedor"],
                "created_at_contacto_proveedor" => $row["created_at_contacto_proveedor"],
                "updated_at_contacto_proveedor" => $row["updated_at_contacto_proveedor"]
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

    case "listar_por_proveedor":
        $datos = $proveedores_contacto->get_contactos_by_proveedor($_POST["id_proveedor"]);
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_contacto_proveedor" => $row["id_contacto_proveedor"],
                "id_proveedor" => $row["id_proveedor"],
                "nombre_contacto_proveedor" => $row["nombre_contacto_proveedor"],
                "apellidos_contacto_proveedor" => $row["apellidos_contacto_proveedor"],
                "cargo_contacto_proveedor" => $row["cargo_contacto_proveedor"],
                "departamento_contacto_proveedor" => $row["departamento_contacto_proveedor"],
                "telefono_contacto_proveedor" => $row["telefono_contacto_proveedor"],
                "movil_contacto_proveedor" => $row["movil_contacto_proveedor"],
                "email_contacto_proveedor" => $row["email_contacto_proveedor"],
                "extension_contacto_proveedor" => $row["extension_contacto_proveedor"],
                "principal_contacto_proveedor" => $row["principal_contacto_proveedor"],
                "observaciones_contacto_proveedor" => $row["observaciones_contacto_proveedor"],
                "activo_contacto_proveedor" => $row["activo_contacto_proveedor"],
                "created_at_contacto_proveedor" => $row["created_at_contacto_proveedor"],
                "updated_at_contacto_proveedor" => $row["updated_at_contacto_proveedor"]
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
            if (empty($_POST["id_contacto_proveedor"])) {
                $resultado = $proveedores_contacto->insert_contacto_proveedor(
                    $_POST["id_proveedor"], 
                    $_POST["nombre_contacto_proveedor"], 
                    $_POST["apellidos_contacto_proveedor"], 
                    $_POST["cargo_contacto_proveedor"], 
                    $_POST["departamento_contacto_proveedor"], 
                    $_POST["telefono_contacto_proveedor"], 
                    $_POST["movil_contacto_proveedor"], 
                    $_POST["email_contacto_proveedor"], 
                    $_POST["extension_contacto_proveedor"], 
                    isset($_POST["principal_contacto_proveedor"]) ? 1 : 0, 
                    $_POST["observaciones_contacto_proveedor"]
                );
                
                if ($resultado !== false && $resultado > 0) {
                    $registro->registrarActividad(
                        'admin',
                        'proveedores_contacto.php',
                        'Guardar el contacto proveedor',
                        "Contacto proveedor guardado exitosamente con ID: $resultado",
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Contacto proveedor guardado exitosamente',
                        'id_contacto_proveedor' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al insertar el contacto proveedor en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
                
            } else {
                $resultado = $proveedores_contacto->update_contacto_proveedor(
                    $_POST["id_contacto_proveedor"],
                    $_POST["id_proveedor"], 
                    $_POST["nombre_contacto_proveedor"], 
                    $_POST["apellidos_contacto_proveedor"], 
                    $_POST["cargo_contacto_proveedor"], 
                    $_POST["departamento_contacto_proveedor"], 
                    $_POST["telefono_contacto_proveedor"], 
                    $_POST["movil_contacto_proveedor"], 
                    $_POST["email_contacto_proveedor"], 
                    $_POST["extension_contacto_proveedor"], 
                    isset($_POST["principal_contacto_proveedor"]) ? 1 : 0, 
                    $_POST["observaciones_contacto_proveedor"]
                );
                
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'proveedores_contacto.php',
                        'Actualizar el contacto proveedor',
                        "Contacto proveedor actualizado exitosamente ID: " . $_POST["id_contacto_proveedor"],
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Contacto proveedor actualizado exitosamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al actualizar el contacto proveedor en la base de datos'
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
        $datos = $proveedores_contacto->get_contacto_proveedorxid($_POST["id_contacto_proveedor"]);

        $registro->registrarActividad(
            'admin',
            'proveedores_contacto.php',
            'Obtener contacto proveedor seleccionado',
            "Contacto proveedor obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $proveedores_contacto->delete_contacto_proveedorxid($_POST["id_contacto_proveedor"]);

        $registro->registrarActividad(
            'admin',
            'proveedores_contacto.php',
            'Eliminar contacto proveedor seleccionado',
            "Contacto proveedor eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $proveedores_contacto->activar_contacto_proveedorxid($_POST["id_contacto_proveedor"]);

        $registro->registrarActividad(
            'admin',
            'proveedores_contacto.php',
            'Activar contacto proveedor seleccionado',
            "Contacto proveedor activado exitosamente ",
            "info"
        );

        break;

    case "verificar":
        $resultado = $proveedores_contacto->verificarContactoProveedor(
            $_POST["nombre_contacto_proveedor"],
            $_POST["id_proveedor"],
            $_POST["id_contacto_proveedor"] ?? null
        );
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "verificar_principal":
        $resultado = $proveedores_contacto->verificarContactoPrincipal(
            $_POST["id_proveedor"],
            $_POST["id_contacto_proveedor"] ?? null
        );
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "listar_disponibles":
        $datos = $proveedores_contacto->get_contactos_proveedor_disponibles();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_contacto_proveedor" => $row["id_contacto_proveedor"],
                "id_proveedor" => $row["id_proveedor"],
                "nombre_contacto_proveedor" => $row["nombre_contacto_proveedor"],
                "apellidos_contacto_proveedor" => $row["apellidos_contacto_proveedor"],
                "cargo_contacto_proveedor" => $row["cargo_contacto_proveedor"],
                "departamento_contacto_proveedor" => $row["departamento_contacto_proveedor"],
                "telefono_contacto_proveedor" => $row["telefono_contacto_proveedor"],
                "movil_contacto_proveedor" => $row["movil_contacto_proveedor"],
                "email_contacto_proveedor" => $row["email_contacto_proveedor"],
                "principal_contacto_proveedor" => $row["principal_contacto_proveedor"],
                "activo_contacto_proveedor" => $row["activo_contacto_proveedor"]
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