<?php
require_once "../config/conexion.php";
require_once "../models/Kit.php";
require_once '../config/funciones.php';

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/kit_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] kit.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
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
            
            $logFile = $logDir . "kit_debug_" . date("Ymd") . ".txt";
            
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
$kit = new Kit();

// Aceptar 'op' desde GET o POST
$op = $_GET["op"] ?? $_POST["op"] ?? null;

if (!$op) {
    echo json_encode(["status" => "error", "message" => "Parámetro 'op' no proporcionado"]);
    exit;
}

switch ($op) {

    case "listar":
        // DEBUG: Log del parámetro recibido
        $id_articulo_maestro = $_GET["id_articulo_maestro"] ?? null;
        error_log("=== DEBUG CONTROLLER KIT ===");
        error_log("id_articulo_maestro recibido: " . var_export($id_articulo_maestro, true));
        error_log("GET completo: " . print_r($_GET, true));
        
        // Verificar si se proporciona un filtro por artículo maestro
        if (isset($_GET["id_articulo_maestro"]) && !empty($_GET["id_articulo_maestro"])) {
            $datos = $kit->get_kits_by_articulo_maestro($_GET["id_articulo_maestro"]);
            error_log("Registros obtenidos: " . count($datos));
        } else {
            $datos = $kit->get_kits();
            error_log("Usando get_kits() - sin filtro");
        }
        
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                // Datos del kit
                "id_kit" => $row["id_kit"],
                "cantidad_kit" => $row["cantidad_kit"],
                "activo_kit" => $row["activo_kit"],
                "created_at_kit" => $row["created_at_kit"],
                "updated_at_kit" => $row["updated_at_kit"],
                
                // Artículo maestro (el KIT)
                "id_articulo_maestro" => $row["id_articulo_maestro"],
                "codigo_articulo_maestro" => $row["codigo_articulo_maestro"],
                "nombre_articulo_maestro" => $row["nombre_articulo_maestro"],
                "name_articulo_maestro" => $row["name_articulo_maestro"] ?? '',
                "precio_articulo_maestro" => $row["precio_articulo_maestro"],
                "es_kit_articulo_maestro" => $row["es_kit_articulo_maestro"],
                "activo_articulo_maestro" => $row["activo_articulo_maestro"],
                
                // Artículo componente
                "id_articulo_componente" => $row["id_articulo_componente"],
                "codigo_articulo_componente" => $row["codigo_articulo_componente"],
                "nombre_articulo_componente" => $row["nombre_articulo_componente"],
                "name_articulo_componente" => $row["name_articulo_componente"] ?? '',
                "precio_articulo_componente" => $row["precio_articulo_componente"],
                "es_kit_articulo_componente" => $row["es_kit_articulo_componente"],
                "activo_articulo_componente" => $row["activo_articulo_componente"],
                
                // Campos calculados
                "subtotal_componente" => $row["subtotal_componente"],
                "total_componentes_kit" => $row["total_componentes_kit"],
                "precio_total_kit" => $row["precio_total_kit"]
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

    case "listarDisponibles":
        // Listar solo kits activos
        $datos = $kit->get_kits_disponibles();
        
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_kit" => $row["id_kit"],
                "cantidad_kit" => $row["cantidad_kit"],
                "nombre_articulo_maestro" => $row["nombre_articulo_maestro"],
                "nombre_articulo_componente" => $row["nombre_articulo_componente"],
                "precio_articulo_componente" => $row["precio_articulo_componente"],
                "subtotal_componente" => $row["subtotal_componente"]
            );
        }

        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        break;

    case "obtenerArticuloMaestro":
        // Obtener información del artículo maestro (KIT)
        $id_articulo = $_POST["id_articulo"] ?? $_GET["id_articulo"] ?? '';
        
        if (empty($id_articulo)) {
            echo json_encode([
                "status" => "error",
                "message" => "ID del artículo es obligatorio"
            ]);
            break;
        }
        
        $datos = $kit->get_articulo_maestro($id_articulo);
        
        if ($datos) {
            echo json_encode([
                "status" => "success",
                "data" => $datos
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo obtener el artículo maestro"
            ]);
        }
        break;

    case "listarArticulosDisponibles":
        // Obtener artículos disponibles para agregar como componentes
        $id_articulo_maestro = $_POST["id_articulo_maestro"] ?? $_GET["id_articulo_maestro"] ?? '';
        
        if (empty($id_articulo_maestro)) {
            echo json_encode([
                "status" => "error",
                "message" => "ID del artículo maestro es obligatorio"
            ]);
            break;
        }
        
        $datos = $kit->get_articulos_disponibles_para_kit($id_articulo_maestro);
        
        header('Content-Type: application/json');
        echo json_encode([
            "status" => "success",
            "data" => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "obtenerPrecioTotal":
        // Obtener el precio total del kit
        $id_articulo_maestro = $_POST["id_articulo_maestro"] ?? $_GET["id_articulo_maestro"] ?? '';
        
        if (empty($id_articulo_maestro)) {
            echo json_encode([
                "status" => "error",
                "message" => "ID del artículo maestro es obligatorio"
            ]);
            break;
        }
        
        $precioTotal = $kit->get_precio_total_kit($id_articulo_maestro);
        
        if ($precioTotal !== false) {
            echo json_encode([
                "status" => "success",
                "precio_total" => $precioTotal
            ]);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "Error al calcular el precio total del kit"
            ]);
        }
        break;

    case "obtenerTotalComponentes":
        // Obtener el número total de componentes del kit
        $id_articulo_maestro = $_POST["id_articulo_maestro"] ?? $_GET["id_articulo_maestro"] ?? '';
        
        if (empty($id_articulo_maestro)) {
            echo json_encode([
                "status" => "error",
                "message" => "ID del artículo maestro es obligatorio"
            ]);
            break;
        }
        
        $totalComponentes = $kit->get_total_componentes_kit($id_articulo_maestro);
        
        echo json_encode([
            "status" => "success",
            "total_componentes" => $totalComponentes
        ]);
        break;

    case "guardaryeditar":
        writeToLog(['action' => 'guardaryeditar_iniciado', 'timestamp' => date('Y-m-d H:i:s')]);
        
        try {
            $id_articulo_maestro = $_POST["id_articulo_maestro"] ?? '';
            $id_articulo_componente = $_POST["id_articulo_componente"] ?? '';
            $cantidad_kit = $_POST["cantidad_kit"] ?? 1;
            
            // Validar datos obligatorios
            if (empty($id_articulo_maestro)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "El artículo maestro es obligatorio"
                ]);
                break;
            }
            
            if (empty($id_articulo_componente)) {
                echo json_encode([
                    "status" => "error",
                    "message" => "El artículo componente es obligatorio"
                ]);
                break;
            }
            
            if ($cantidad_kit <= 0) {
                echo json_encode([
                    "status" => "error",
                    "message" => "La cantidad debe ser mayor a 0"
                ]);
                break;
            }
            
            writeToLog([
                'action' => 'guardaryeditar_inicio',
                'post_info' => $_POST
            ]);
            
            if (empty($_POST["id_kit"])) {
                // Insertar nuevo componente en el kit
                $resultado = $kit->insert_kit(
                    $id_articulo_maestro,
                    $id_articulo_componente,
                    $cantidad_kit
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'kit.php',
                        'Guardar componente en kit',
                        "Componente agregado al kit exitosamente",
                        "info"
                    );

                    echo json_encode([
                        "status" => "success",
                        "message" => "Componente agregado al kit correctamente",
                        "id_kit" => $resultado
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error al agregar el componente al kit"
                    ]);
                }

            } else {
                // Actualizar componente existente
                $resultado = $kit->update_kit(
                    $_POST["id_kit"],
                    $id_articulo_maestro,
                    $id_articulo_componente,
                    $cantidad_kit
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'kit.php',
                        'Actualizar componente en kit',
                        "Componente del kit actualizado exitosamente",
                        "info"
                    );

                    echo json_encode([
                        "status" => "success",
                        "message" => "Componente del kit actualizado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error al actualizar el componente del kit"
                    ]);
                }
            }
        } catch (Exception $e) {
            // Capturar errores de triggers de BD
            $errorMsg = $e->getMessage();
            
            writeToLog([
                'action' => 'guardaryeditar_error',
                'error' => $errorMsg
            ]);
            
            echo json_encode([
                "status" => "error",
                "message" => $errorMsg
            ]);
        }
        break;

    case "mostrar": 
        header('Content-Type: application/json; charset=utf-8');
        $datos = $kit->get_kitxid($_POST["id_kit"]);
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo obtener el componente del kit solicitado"
            ]);
        }
        break;

    case "eliminar":
        // DELETE físico (no soft delete)
        $resultado = $kit->delete_kitxid($_POST["id_kit"]);
        echo json_encode([
            "status" => $resultado ? "success" : "error",
            "message" => $resultado ? "Componente eliminado del kit correctamente" : "Error al eliminar el componente del kit"
        ]);
        break;

    case "desactivar":
        // Soft delete
        $resultado = $kit->desactivar_kitxid($_POST["id_kit"]);
        echo json_encode([
            "status" => $resultado ? "success" : "error",
            "message" => $resultado ? "Componente desactivado correctamente" : "Error al desactivar el componente"
        ]);
        break;

    case "activar":
        $resultado = $kit->activar_kitxid($_POST["id_kit"]);
        echo json_encode([
            "status" => $resultado ? "success" : "error",
            "message" => $resultado ? "Componente activado correctamente" : "Error al activar el componente"
        ]);
        break;

    case "verificarComponente":
        // Verificar si un componente ya existe en el kit
        $id_articulo_maestro = isset($_POST["id_articulo_maestro"]) ? trim($_POST["id_articulo_maestro"]) : 
                               (isset($_GET["id_articulo_maestro"]) ? trim($_GET["id_articulo_maestro"]) : '');

        $id_articulo_componente = isset($_POST["id_articulo_componente"]) ? trim($_POST["id_articulo_componente"]) : 
                                  (isset($_GET["id_articulo_componente"]) ? trim($_GET["id_articulo_componente"]) : '');

        $id_kit = isset($_POST["id_kit"]) ? trim($_POST["id_kit"]) : 
                  (isset($_GET["id_kit"]) ? trim($_GET["id_kit"]) : null);

        writeToLog([
            'action' => 'verificarComponente',
            'id_articulo_maestro' => $id_articulo_maestro,
            'id_articulo_componente' => $id_articulo_componente,
            'id_kit' => $id_kit,
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST,
            'get_data' => $_GET
        ]);

        if (empty($id_articulo_maestro)) {
            echo json_encode([
                "status" => "error",
                "message" => "El artículo maestro es obligatorio."
            ]);
            break;
        }

        if (empty($id_articulo_componente)) {
            echo json_encode([
                "status" => "error",
                "message" => "El artículo componente es obligatorio."
            ]);
            break;
        }
        
        $resultado = $kit->verificar_componente_en_kit($id_articulo_maestro, $id_articulo_componente, $id_kit);
        
        if (isset($resultado['error'])) {
            $registro->registrarActividad(
                'admin',
                'kit.php',
                'Verificar componente',
                'Error al verificar componente: ' . $resultado['error'],
                'error'
            );
            
            echo json_encode([
                "status" => "error",
                "message" => "Error al verificar el componente: " . $resultado['error']
            ]);
            break;
        }
        
        $mensajeActividad = $resultado['existe'] ? 
            'Componente duplicado detectado en el kit' : 
            'Componente disponible para agregar al kit';
        
        $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
        
        $registro->registrarActividad(
            'admin',
            'kit.php',
            'Verificar componente',
            $mensajeActividad,
            $tipoActividad
        );
        
        echo json_encode([
            "status" => "success",
            "existe" => $resultado['existe']
        ]);
        break;

    default:
        echo json_encode([
            "status" => "error",
            "message" => "Operación no válida"
        ]);
        break;
}

?>
