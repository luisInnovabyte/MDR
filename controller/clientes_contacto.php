<?php
require_once "../config/conexion.php";
require_once "../models/Clientes_contacto.php";
require_once '../config/funciones.php';

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/clientes_contacto_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] clientes_contacto.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    
    $logDirs = [
        __DIR__ . "/../public/logs/",
        "W:/TOLDOS_AMPLIADO/public/logs/",
        sys_get_temp_dir() . "/toldos_logs/"
    ];
    
    foreach ($logDirs as $logDir) {
        try {
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . "clientes_contacto_debug_" . date("Ymd") . ".txt";
            
            $result = file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
            
            if ($result !== false) {
                break;
            }
        } catch (Exception $e) {
            continue;
        }
    }
}

$registro = new RegistroActividad();
$clientes_contacto = new Clientes_contacto();

switch ($_GET["op"]) {

    case "listar":
        // Verificar si se proporciona un ID de cliente para filtrar
        if (isset($_GET["id_cliente"]) && !empty($_GET["id_cliente"])) {
            $datos = $clientes_contacto->get_contactos_by_cliente($_GET["id_cliente"]);
        } else {
            $datos = $clientes_contacto->get_contactos_cliente();
        }
        
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_contacto_cliente" => $row["id_contacto_cliente"],
                "id_cliente" => $row["id_cliente"],
                "nombre_cliente" => $row["nombre_cliente"] ?? '',
                "nombre_contacto_cliente" => $row["nombre_contacto_cliente"],
                "apellidos_contacto_cliente" => $row["apellidos_contacto_cliente"],
                "cargo_contacto_cliente" => $row["cargo_contacto_cliente"],
                "departamento_contacto_cliente" => $row["departamento_contacto_cliente"],
                "telefono_contacto_cliente" => $row["telefono_contacto_cliente"],
                "movil_contacto_cliente" => $row["movil_contacto_cliente"],
                "email_contacto_cliente" => $row["email_contacto_cliente"],
                "extension_contacto_cliente" => $row["extension_contacto_cliente"],
                "principal_contacto_cliente" => $row["principal_contacto_cliente"],
                "observaciones_contacto_cliente" => $row["observaciones_contacto_cliente"],
                "activo_contacto_cliente" => $row["activo_contacto_cliente"],
                "created_at_contacto_cliente" => $row["created_at_contacto_cliente"],
                "updated_at_contacto_cliente" => $row["updated_at_contacto_cliente"]
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
        writeToLog(['action' => 'guardaryeditar_iniciado', 'timestamp' => date('Y-m-d H:i:s')]);
        
        try {
            $id_cliente = $_POST["id_cliente"] ?? '';
            $nombre_contacto_cliente = $_POST["nombre_contacto_cliente"] ?? '';
            $apellidos_contacto_cliente = $_POST["apellidos_contacto_cliente"] ?? '';
            $cargo_contacto_cliente = $_POST["cargo_contacto_cliente"] ?? '';
            $departamento_contacto_cliente = $_POST["departamento_contacto_cliente"] ?? '';
            $telefono_contacto_cliente = $_POST["telefono_contacto_cliente"] ?? '';
            $movil_contacto_cliente = $_POST["movil_contacto_cliente"] ?? '';
            $email_contacto_cliente = $_POST["email_contacto_cliente"] ?? '';
            $extension_contacto_cliente = $_POST["extension_contacto_cliente"] ?? '';
            $principal_contacto_cliente = isset($_POST["principal_contacto_cliente"]) ? (int)$_POST["principal_contacto_cliente"] : 0;
            $observaciones_contacto_cliente = $_POST["observaciones_contacto_cliente"] ?? '';
            
            writeToLog([
                'action' => 'guardaryeditar_inicio',
                'post_info' => $_POST
            ]);
            
            if (empty($_POST["id_contacto_cliente"])) {
                // Insertar nuevo contacto de cliente
                $resultado = $clientes_contacto->insert_contacto_cliente(
                    $id_cliente,
                    $nombre_contacto_cliente,
                    $apellidos_contacto_cliente,
                    $cargo_contacto_cliente,
                    $departamento_contacto_cliente,
                    $telefono_contacto_cliente,
                    $movil_contacto_cliente,
                    $email_contacto_cliente,
                    $extension_contacto_cliente,
                    $principal_contacto_cliente,
                    $observaciones_contacto_cliente
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'clientes_contacto.php',
                        'Guardar contacto cliente',
                        "Contacto cliente guardado exitosamente",
                        "info"
                    );

                    echo json_encode([
                        "success" => true,
                        "message" => "Contacto cliente insertado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al insertar el contacto cliente"
                    ]);
                }

            } else {
                // Actualizar contacto cliente existente
                $resultado = $clientes_contacto->update_contacto_cliente(
                    $_POST["id_contacto_cliente"],
                    $id_cliente,
                    $nombre_contacto_cliente,
                    $apellidos_contacto_cliente,
                    $cargo_contacto_cliente,
                    $departamento_contacto_cliente,
                    $telefono_contacto_cliente,
                    $movil_contacto_cliente,
                    $email_contacto_cliente,
                    $extension_contacto_cliente,
                    $principal_contacto_cliente,
                    $observaciones_contacto_cliente
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'clientes_contacto.php',
                        'Actualizar contacto cliente',
                        "Contacto cliente actualizado exitosamente",
                        "info"
                    );

                    echo json_encode([
                        "success" => true,
                        "message" => "Contacto cliente actualizado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al actualizar el contacto cliente"
                    ]);
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Excepción: " . $e->getMessage()
            ]);
        }
        break;

    case "mostrar": 
        header('Content-Type: application/json; charset=utf-8');
        $datos = $clientes_contacto->get_contacto_clientexid($_POST["id_contacto_cliente"]);
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener el contacto cliente solicitado"
            ]);
        }
        break;

    case "eliminar":
        $resultado = $clientes_contacto->desactivar_contacto_clientexid($_POST["id_contacto_cliente"]);
        echo json_encode([
            "success" => $resultado,
            "message" => $resultado ? "Contacto desactivado correctamente" : "Error al desactivar el contacto"
        ]);
        break;

    case "activar":
        $resultado = $clientes_contacto->activar_contacto_clientexid($_POST["id_contacto_cliente"]);
        echo json_encode([
            "success" => $resultado,
            "message" => $resultado ? "Contacto activado correctamente" : "Error al activar el contacto"
        ]);
        break;

    case "verificarContactoCliente":
        $nombreContacto = isset($_POST["nombre_contacto_cliente"]) ? trim($_POST["nombre_contacto_cliente"]) : 
                         (isset($_GET["nombre_contacto_cliente"]) ? trim($_GET["nombre_contacto_cliente"]) : '');
        
        $idCliente = isset($_POST["id_cliente"]) ? trim($_POST["id_cliente"]) : 
                    (isset($_GET["id_cliente"]) ? trim($_GET["id_cliente"]) : '');

        $idContactoCliente = isset($_POST["id_contacto_cliente"]) ? trim($_POST["id_contacto_cliente"]) : 
                           (isset($_GET["id_contacto_cliente"]) ? trim($_GET["id_contacto_cliente"]) : null);

        writeToLog([
            'action' => 'verificarContactoCliente',
            'nombre_contacto_cliente' => $nombreContacto,
            'id_cliente' => $idCliente,
            'id_contacto_cliente' => $idContactoCliente,
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST,
            'get_data' => $_GET
        ]);

        if (empty($nombreContacto) || empty($idCliente)) {
            $registro->registrarActividad(
                'admin',
                'clientes_contacto.php',
                'Verificar contacto cliente',
                'Intento de verificación con campos obligatorios vacíos',
                'warning'
            );
        
            echo json_encode([
                "success" => false,
                "message" => "El nombre del contacto y el cliente son obligatorios."
            ]);
            break;
        }
        
        $resultado = $clientes_contacto->verificarContactoCliente($nombreContacto, $idCliente, $idContactoCliente);
        
        if (isset($resultado['error'])) {
            $registro->registrarActividad(
                'admin',
                'clientes_contacto.php',
                'Verificar contacto cliente',
                'Error al verificar contacto cliente: ' . $resultado['error'],
                'error'
            );
            
            echo json_encode([
                "success" => false,
                "message" => "Error al verificar el contacto cliente: " . $resultado['error']
            ]);
            break;
        }
        
        $mensajeActividad = $resultado['existe'] ? 
            'Contacto cliente duplicado detectado: ' . $nombreContacto : 
            'Contacto cliente disponible: ' . $nombreContacto;
        
        $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
        
        $registro->registrarActividad(
            'admin',
            'clientes_contacto.php',
            'Verificar contacto cliente',
            $mensajeActividad,
            $tipoActividad
        );
        
        echo json_encode([
            "success" => true,
            "existe" => $resultado['existe']
        ]);
        break;
}