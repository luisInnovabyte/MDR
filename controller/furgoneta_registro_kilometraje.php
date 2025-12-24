<?php
require_once "../config/conexion.php";
require_once "../models/Furgoneta_registro_kilometraje.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$registroKm = new Furgoneta_registro_kilometraje();

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt";
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

switch ($_GET["op"]) {

    case "listar":
        $datos = $registroKm->get_registros_km();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_registro_km" => $row["id_registro_km"],
                "id_furgoneta" => $row["id_furgoneta"],
                "matricula_furgoneta" => $row["matricula_furgoneta"] ?? '',
                "fecha_registro_km" => $row["fecha_registro_km"],
                "kilometraje_registrado_km" => $row["kilometraje_registrado_km"],
                "tipo_registro_km" => $row["tipo_registro_km"] ?? 'manual',
                "observaciones_registro_km" => $row["observaciones_registro_km"] ?? '',
                "created_at_registro_km" => $row["created_at_registro_km"]
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

    case "listar_por_furgoneta":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $registroKm->get_registros_por_furgoneta($_POST["id_furgoneta"]);
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_registro_km" => $row["id_registro_km"],
                "id_furgoneta" => $row["id_furgoneta"],
                "fecha_registro_km" => $row["fecha_registro_km"],
                "kilometraje_registrado_km" => $row["kilometraje_registrado_km"],
                "tipo_registro_km" => $row["tipo_registro_km"] ?? 'manual',
                "observaciones_registro_km" => $row["observaciones_registro_km"] ?? '',
                "created_at_registro_km" => $row["created_at_registro_km"]
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
            writeToLog([
                'action' => 'guardaryeditar',
                'POST_completo' => $_POST
            ]);

            // Validar campos requeridos
            if (empty($_POST["id_furgoneta"]) || empty($_POST["fecha_registro_km"]) || empty($_POST["kilometraje_registrado_km"])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan campos requeridos: id_furgoneta, fecha_registro_km, kilometraje_registrado_km'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Procesar campos opcionales
            $tipo_registro = isset($_POST["tipo_registro_km"]) && $_POST["tipo_registro_km"] !== '' ? $_POST["tipo_registro_km"] : 'manual';
            $observaciones = isset($_POST["observaciones_registro_km"]) && $_POST["observaciones_registro_km"] !== '' ? $_POST["observaciones_registro_km"] : null;

            // Solo INSERT - Los registros de kilometraje no se actualizan
            $resultado = $registroKm->insert_registro_km(
                $_POST["id_furgoneta"],
                $_POST["fecha_registro_km"],
                intval($_POST["kilometraje_registrado_km"]),
                $tipo_registro,
                $observaciones
            );

            if ($resultado !== false && is_numeric($resultado)) {
                $registro->registrarActividad(
                    'admin',
                    'furgoneta_registro_kilometraje.php',
                    'guardaryeditar',
                    "Registro de kilometraje guardado exitosamente con ID: $resultado - Furgoneta ID: " . $_POST["id_furgoneta"] . " - KM: " . $_POST["kilometraje_registrado_km"],
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Registro de kilometraje guardado exitosamente',
                    'id_registro_km' => $resultado
                ], JSON_UNESCAPED_UNICODE);
            } else {
                // El resultado puede contener un mensaje de error (validación de KM)
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => is_string($resultado) ? $resultado : 'Error al insertar el registro de kilometraje'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error detallado: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "mostrar":
        if (empty($_POST["id_registro_km"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de registro es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $registroKm->get_registro_kmxid($_POST["id_registro_km"]);

        if ($datos) {
            $registro->registrarActividad(
                'admin',
                'furgoneta_registro_kilometraje.php',
                'mostrar',
                "Registro obtenido exitosamente ID: " . $_POST["id_registro_km"],
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "ultimo_registro":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $registroKm->get_ultimo_registro($_POST["id_furgoneta"]);

        if ($datos) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $datos
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se encontraron registros para esta furgoneta'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "kilometraje_actual":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $registroKm->get_kilometraje_actual($_POST["id_furgoneta"]);

        if ($datos) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $datos
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo obtener el kilometraje actual'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "calcular_km_periodo":
        if (empty($_POST["id_furgoneta"]) || empty($_POST["fecha_inicio"]) || empty($_POST["fecha_fin"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Faltan parámetros requeridos: id_furgoneta, fecha_inicio, fecha_fin'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $registroKm->calcular_km_periodo(
            $_POST["id_furgoneta"],
            $_POST["fecha_inicio"],
            $_POST["fecha_fin"]
        );

        if ($resultado !== false) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $resultado
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al calcular kilometraje del período'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "km_desde_revision":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $registroKm->calcular_km_desde_ultima_revision($_POST["id_furgoneta"]);

        if ($resultado !== false) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $resultado
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo calcular el kilometraje desde la última revisión'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "verificar_revision":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $registroKm->verificar_necesita_revision($_POST["id_furgoneta"]);

        if ($resultado !== false) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $resultado
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo verificar el estado de revisión'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "eliminar":
        if (empty($_POST["id_registro_km"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de registro es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $registroKm->delete_registro_kmxid($_POST["id_registro_km"]);

        if ($resultado) {
            $registro->registrarActividad(
                'admin',
                'furgoneta_registro_kilometraje.php',
                'eliminar',
                "Registro eliminado exitosamente ID: " . $_POST["id_registro_km"],
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Registro eliminado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo eliminar el registro'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "estadisticas":
        try {
            if (empty($_POST["id_furgoneta"])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de furgoneta es requerido'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            $estadisticas = $registroKm->obtener_estadisticas($_POST["id_furgoneta"]);

            if ($estadisticas) {
                $registro->registrarActividad(
                    'admin',
                    'furgoneta_registro_kilometraje.php',
                    'estadisticas',
                    "Estadísticas obtenidas correctamente para furgoneta ID: " . $_POST["id_furgoneta"],
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $estadisticas
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudieron obtener las estadísticas'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener estadísticas: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "historico_completo":
        try {
            if (empty($_POST["id_furgoneta"])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de furgoneta es requerido'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            $datos = $registroKm->get_historico_completo($_POST["id_furgoneta"]);

            if ($datos !== false) {
                $registro->registrarActividad(
                    'admin',
                    'furgoneta_registro_kilometraje.php',
                    'historico_completo',
                    "Histórico completo obtenido para furgoneta ID: " . $_POST["id_furgoneta"],
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $datos
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo obtener el histórico completo'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener histórico: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Operación no válida'
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>
