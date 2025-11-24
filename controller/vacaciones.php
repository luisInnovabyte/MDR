<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Vacaciones.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$vacacion = new Vacaciones();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $vacacion->get_vacacion();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_vacacion" => $row["id_vacacion"],
                "id_comercial" => $row["id_comercial"],
                "fecha_inicio" => $row["fecha_inicio"],
                "fecha_fin" => $row["fecha_fin"],
                "descripcion" => $row["descripcion"],
                "activo_vacacion" => $row["activo_vacacion"],
                "nombre_comercial" => $row["nombre_comercial"] // Añadido el nombre_comercial
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
            // 1. Primero verificamos solapamiento (validación backend)
            $solapamiento = $vacacion->verificarSolapamiento(
                $_POST['id_comercial'],
                $_POST['fecha_inicio'],
                $_POST['fecha_fin'],
                $_POST['id_vacacion'] ?? null
            );
            
            if ($solapamiento) {
                // Registro de intento fallido
                $registro->registrarActividad(
                    'admin',
                    'vacaciones.php',
                    'Intento de guardar vacación',
                    'Fallido por solapamiento de fechas para comercial ID: ' . $_POST['id_comercial'],
                    'warning'
                );
                
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'El comercial ya tiene vacaciones programadas en ese período'
                ]);
                exit;
            }
        
            // 2. Lógica original de guardar/editar
            if (empty($_POST["id_vacacion"])) {
                $resultado = $vacacion->insert_vacacion(
                    $_POST["id_comercial"],
                    $_POST["fecha_inicio"],
                    $_POST["fecha_fin"],
                    $_POST["descripcion"] ?? '' // Añadido valor por defecto
                );
                
                $accion = 'Guardar';
                $mensaje = 'Vacación guardada exitosamente';
            } else {
                $resultado = $vacacion->update_vacacion(
                    $_POST["id_vacacion"],
                    $_POST["id_comercial"],
                    $_POST["fecha_inicio"],
                    $_POST["fecha_fin"],
                    $_POST["descripcion"] ?? '' // Añadido valor por defecto
                );
                
                $accion = 'Actualizar';
                $mensaje = 'Vacación actualizada exitosamente';
            }
        
            // 3. Registro de actividad (mejorado con más detalles)
            $registro->registrarActividad(
                'admin',
                'vacaciones.php',
                $accion . ' la vacación',
                $mensaje . ' - Comercial ID: ' . $_POST['id_comercial'] . 
                           ' | Fechas: ' . $_POST['fecha_inicio'] . ' al ' . $_POST['fecha_fin'],
                'info'
            );
        
            // 4. Respuesta JSON consistente
            header('Content-Type: application/json');
            echo json_encode([
                'success' => $resultado,
                'message' => $resultado ? $mensaje : 'Error al procesar la solicitud'
            ]);
            break;

    case "mostrar":
        $datos = $vacacion->get_vacacionxid($_POST["id_vacacion"]);
        // if (is_array($datos) == true and count($datos) > 0) {
        //     foreach ($datos as $row) {
        //         $output["prod_id"] = $row["prod_id"];
        //         $output["prod_nom"] = $row["prod_nom"];
        //     }
        // }
        //echo json_encode($output);

        $registro->registrarActividad(
            'admin',
            'vacaciones.php',
            'Obtener vacacion seleccionada',
            "Vacacion obtenida exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $vacacion->delete_vacacionxid($_POST["id_vacacion"]);

        $registro->registrarActividad(
            'admin',
            'vacaciones.php',
            'Eliminar vacacion seleccionada',
            "Vacacion eliminada exitosamente ",
            "info"
        );

        break;

    case "activar":
        $vacacion->activar_vacacionxid($_POST["id_vacacion"]);

        $registro->registrarActividad(
            'admin',
            'vacaciones.php',
            'Obtener vacacion seleccionada',
            "Vacacion activada exitosamente ",
            "info"
        );

        break;

        case "verificarSolapamiento":
            // Validar que los datos requeridos están presentes
            if (empty($_POST['id_comercial']) || empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
                http_response_code(400);
                echo json_encode(['error' => 'Datos incompletos']);
                exit;
            }
        
            $response = [
                'solapamiento' => $vacacion->verificarSolapamiento(
                    $_POST['id_comercial'],
                    $_POST['fecha_inicio'],
                    $_POST['fecha_fin'],
                    $_POST['id_vacacion'] ?? null
                )
            ];
        
            header('Content-Type: application/json');
            echo json_encode($response);
            break;

            case "filtrarPorFecha":
                // Validar que las fechas están presentes en GET
                if (empty($_GET['fecha_inicio']) || empty($_GET['fecha_fin'])) {
                    http_response_code(400);
                    echo json_encode(['error' => 'Debes seleccionar ambas fechas']);
                    exit;
                }
            
                try {
                    // Las fechas ya vienen en formato YYYY-MM-DD desde el frontend
                    $fechaInicio = $_GET['fecha_inicio'];
                    $fechaFin = $_GET['fecha_fin'];
            
                    // Validar que fecha inicio <= fecha fin
                    if (strtotime($fechaInicio) > strtotime($fechaFin)) {
                        throw new Exception('La fecha de inicio no puede ser mayor que la fecha fin');
                    }
            
                    // Obtener datos filtrados
                    $datosFiltrados = $vacacion->obtenerPorRangoFechas($fechaInicio, $fechaFin);
            
                    // Respuesta para DataTables
                    echo json_encode([
                        'draw' => $_GET['draw'] ?? 1,
                        'recordsTotal' => count($datosFiltrados),
                        'recordsFiltered' => count($datosFiltrados),
                        'data' => $datosFiltrados
                    ]);
            
                } catch (Exception $e) {
                    http_response_code(400);
                    echo json_encode(['error' => $e->getMessage()]);
                }
                break;
}
