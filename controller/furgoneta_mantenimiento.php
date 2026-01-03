<?php
require_once "../config/conexion.php";
require_once "../models/Furgoneta_mantenimiento.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$mantenimiento = new Furgoneta_mantenimiento();

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt";
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

switch ($_GET["op"]) {

    case "listar":
        $datos = $mantenimiento->get_mantenimientos();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_mantenimiento" => $row["id_mantenimiento"],
                "id_furgoneta" => $row["id_furgoneta"],
                "matricula_furgoneta" => $row["matricula_furgoneta"] ?? '',
                "marca_furgoneta" => $row["marca_furgoneta"] ?? '',
                "modelo_furgoneta" => $row["modelo_furgoneta"] ?? '',
                "fecha_mantenimiento" => $row["fecha_mantenimiento"],
                "tipo_mantenimiento" => $row["tipo_mantenimiento"],
                "descripcion_mantenimiento" => $row["descripcion_mantenimiento"],
                "kilometraje_mantenimiento" => $row["kilometraje_mantenimiento"] ?? '',
                "costo_mantenimiento" => $row["costo_mantenimiento"],
                "numero_factura_mantenimiento" => $row["numero_factura_mantenimiento"] ?? '',
                "taller_mantenimiento" => $row["taller_mantenimiento"] ?? '',
                "telefono_taller_mantenimiento" => $row["telefono_taller_mantenimiento"] ?? '',
                "direccion_taller_mantenimiento" => $row["direccion_taller_mantenimiento"] ?? '',
                "resultado_itv" => $row["resultado_itv"] ?? '',
                "fecha_proxima_itv" => $row["fecha_proxima_itv"] ?? '',
                "garantia_hasta_mantenimiento" => $row["garantia_hasta_mantenimiento"] ?? '',
                "observaciones_mantenimiento" => $row["observaciones_mantenimiento"] ?? '',
                "activo_mantenimiento" => $row["activo_mantenimiento"],
                "created_at_mantenimiento" => $row["created_at_mantenimiento"],
                "updated_at_mantenimiento" => $row["updated_at_mantenimiento"]
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

    case "listar_todos":
        $datos = $mantenimiento->get_todos_mantenimientos();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_mantenimiento" => $row["id_mantenimiento"],
                "id_furgoneta" => $row["id_furgoneta"],
                "matricula_furgoneta" => $row["matricula_furgoneta"] ?? '',
                "fecha_mantenimiento" => $row["fecha_mantenimiento"],
                "tipo_mantenimiento" => $row["tipo_mantenimiento"],
                "descripcion_mantenimiento" => $row["descripcion_mantenimiento"],
                "costo_mantenimiento" => $row["costo_mantenimiento"],
                "activo_mantenimiento" => $row["activo_mantenimiento"]
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

        $datos = $mantenimiento->get_mantenimientos_por_furgoneta($_POST["id_furgoneta"]);
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_mantenimiento" => $row["id_mantenimiento"],
                "fecha_mantenimiento" => $row["fecha_mantenimiento"],
                "tipo_mantenimiento" => $row["tipo_mantenimiento"],
                "descripcion_mantenimiento" => $row["descripcion_mantenimiento"],
                "kilometraje_mantenimiento" => $row["kilometraje_mantenimiento"] ?? '',
                "costo_mantenimiento" => $row["costo_mantenimiento"] ?? '0.00',
                "numero_factura_mantenimiento" => $row["numero_factura_mantenimiento"] ?? '',
                "taller_mantenimiento" => $row["taller_mantenimiento"] ?? '',
                "telefono_taller_mantenimiento" => $row["telefono_taller_mantenimiento"] ?? '',
                "direccion_taller_mantenimiento" => $row["direccion_taller_mantenimiento"] ?? '',
                "resultado_itv" => $row["resultado_itv"] ?? '',
                "fecha_proxima_itv" => $row["fecha_proxima_itv"] ?? '',
                "garantia_hasta_mantenimiento" => $row["garantia_hasta_mantenimiento"] ?? '',
                "observaciones_mantenimiento" => $row["observaciones_mantenimiento"] ?? '',
                "activo_mantenimiento" => $row["activo_mantenimiento"],
                "created_at_mantenimiento" => $row["created_at_mantenimiento"],
                "updated_at_mantenimiento" => $row["updated_at_mantenimiento"]
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
            if (empty($_POST["id_furgoneta"]) || empty($_POST["fecha_mantenimiento"]) || 
                empty($_POST["tipo_mantenimiento"]) || empty($_POST["descripcion_mantenimiento"])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan campos requeridos: id_furgoneta, fecha_mantenimiento, tipo_mantenimiento, descripcion_mantenimiento'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Procesar campos opcionales
            $kilometraje = isset($_POST["kilometraje_mantenimiento"]) && $_POST["kilometraje_mantenimiento"] !== '' ? intval($_POST["kilometraje_mantenimiento"]) : null;
            $costo = isset($_POST["costo_mantenimiento"]) && $_POST["costo_mantenimiento"] !== '' ? floatval($_POST["costo_mantenimiento"]) : 0.00;
            $numero_factura = isset($_POST["numero_factura_mantenimiento"]) && $_POST["numero_factura_mantenimiento"] !== '' ? $_POST["numero_factura_mantenimiento"] : null;
            $taller = isset($_POST["taller_mantenimiento"]) && $_POST["taller_mantenimiento"] !== '' ? $_POST["taller_mantenimiento"] : null;
            $telefono_taller = isset($_POST["telefono_taller_mantenimiento"]) && $_POST["telefono_taller_mantenimiento"] !== '' ? $_POST["telefono_taller_mantenimiento"] : null;
            $direccion_taller = isset($_POST["direccion_taller_mantenimiento"]) && $_POST["direccion_taller_mantenimiento"] !== '' ? $_POST["direccion_taller_mantenimiento"] : null;
            $resultado_itv = isset($_POST["resultado_itv"]) && $_POST["resultado_itv"] !== '' ? $_POST["resultado_itv"] : null;
            $fecha_proxima_itv = isset($_POST["fecha_proxima_itv"]) && $_POST["fecha_proxima_itv"] !== '' ? $_POST["fecha_proxima_itv"] : null;
            $garantia_hasta = isset($_POST["garantia_hasta_mantenimiento"]) && $_POST["garantia_hasta_mantenimiento"] !== '' ? $_POST["garantia_hasta_mantenimiento"] : null;
            $observaciones = isset($_POST["observaciones_mantenimiento"]) && $_POST["observaciones_mantenimiento"] !== '' ? $_POST["observaciones_mantenimiento"] : null;

            if (empty($_POST["id_mantenimiento"])) {
                // INSERT
                $resultado = $mantenimiento->insert_mantenimiento(
                    $_POST["id_furgoneta"],
                    $_POST["fecha_mantenimiento"],
                    $_POST["tipo_mantenimiento"],
                    $_POST["descripcion_mantenimiento"],
                    $kilometraje,
                    $costo,
                    $numero_factura,
                    $taller,
                    $telefono_taller,
                    $direccion_taller,
                    $resultado_itv,
                    $fecha_proxima_itv,
                    $garantia_hasta,
                    $observaciones
                );

                if ($resultado !== false && is_numeric($resultado)) {
                    $registro->registrarActividad(
                        'admin',
                        'furgoneta_mantenimiento.php',
                        'guardaryeditar',
                        "Mantenimiento guardado exitosamente con ID: $resultado - Furgoneta ID: " . $_POST["id_furgoneta"] . " - Tipo: " . $_POST["tipo_mantenimiento"],
                        'info'
                    );

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Mantenimiento guardado exitosamente',
                        'id_mantenimiento' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => is_array($resultado) && isset($resultado['error']) ? $resultado['error'] : 'Error al insertar el mantenimiento'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // UPDATE
                $resultado = $mantenimiento->update_mantenimiento(
                    $_POST["id_mantenimiento"],
                    $_POST["id_furgoneta"],
                    $_POST["fecha_mantenimiento"],
                    $_POST["tipo_mantenimiento"],
                    $_POST["descripcion_mantenimiento"],
                    $kilometraje,
                    $costo,
                    $numero_factura,
                    $taller,
                    $telefono_taller,
                    $direccion_taller,
                    $resultado_itv,
                    $fecha_proxima_itv,
                    $garantia_hasta,
                    $observaciones
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'furgoneta_mantenimiento.php',
                        'guardaryeditar',
                        "Mantenimiento actualizado exitosamente ID: " . $_POST["id_mantenimiento"],
                        'info'
                    );

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Mantenimiento actualizado exitosamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => is_array($resultado) && isset($resultado['error']) ? $resultado['error'] : 'Error al actualizar el mantenimiento'
                    ], JSON_UNESCAPED_UNICODE);
                }
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
        if (empty($_POST["id_mantenimiento"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de mantenimiento es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $mantenimiento->get_mantenimientoxid($_POST["id_mantenimiento"]);

        if ($datos) {
            $registro->registrarActividad(
                'admin',
                'furgoneta_mantenimiento.php',
                'mostrar',
                "Mantenimiento obtenido exitosamente ID: " . $_POST["id_mantenimiento"],
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Mantenimiento no encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "eliminar":
        if (empty($_POST["id_mantenimiento"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de mantenimiento es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $mantenimiento->delete_mantenimientoxid($_POST["id_mantenimiento"]);

        if ($resultado) {
            $registro->registrarActividad(
                'admin',
                'furgoneta_mantenimiento.php',
                'eliminar',
                "Mantenimiento eliminado exitosamente ID: " . $_POST["id_mantenimiento"],
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Mantenimiento desactivado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo desactivar el mantenimiento'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "activar":
        if (empty($_POST["id_mantenimiento"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de mantenimiento es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $mantenimiento->activar_mantenimientoxid($_POST["id_mantenimiento"]);

        if ($resultado) {
            $registro->registrarActividad(
                'admin',
                'furgoneta_mantenimiento.php',
                'activar',
                "Mantenimiento activado exitosamente ID: " . $_POST["id_mantenimiento"],
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Mantenimiento activado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo activar el mantenimiento'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "desactivar":
        if (empty($_POST["id_mantenimiento"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de mantenimiento es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $resultado = $mantenimiento->delete_mantenimientoxid($_POST["id_mantenimiento"]);

        if ($resultado) {
            $registro->registrarActividad(
                'admin',
                'furgoneta_mantenimiento.php',
                'desactivar',
                "Mantenimiento desactivado exitosamente ID: " . $_POST["id_mantenimiento"],
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Mantenimiento desactivado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo desactivar el mantenimiento'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "listar_por_tipo":
        if (empty($_POST["id_furgoneta"]) || empty($_POST["tipo_mantenimiento"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta y tipo de mantenimiento son requeridos'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $mantenimiento->get_mantenimientos_por_tipo($_POST["id_furgoneta"], $_POST["tipo_mantenimiento"]);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "historial_itv":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $mantenimiento->get_historial_itv($_POST["id_furgoneta"]);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "ultima_itv":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $mantenimiento->get_ultima_itv($_POST["id_furgoneta"]);

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
                'message' => 'No se encontró historial de ITV para esta furgoneta'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "ultimo_mantenimiento":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $mantenimiento->get_ultimo_mantenimiento($_POST["id_furgoneta"]);

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
                'message' => 'No se encontraron mantenimientos para esta furgoneta'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "calcular_costo":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $fecha_inicio = isset($_POST["fecha_inicio"]) && $_POST["fecha_inicio"] !== '' ? $_POST["fecha_inicio"] : null;
        $fecha_fin = isset($_POST["fecha_fin"]) && $_POST["fecha_fin"] !== '' ? $_POST["fecha_fin"] : null;

        $datos = $mantenimiento->calcular_costo_total($_POST["id_furgoneta"], $fecha_inicio, $fecha_fin);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "garantias_vigentes":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $mantenimiento->get_mantenimientos_con_garantia($_POST["id_furgoneta"]);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "estadisticas":
        if (empty($_POST["id_furgoneta"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de furgoneta es requerido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $estadisticas = $mantenimiento->obtener_estadisticas($_POST["id_furgoneta"]);

        if ($estadisticas) {
            $registro->registrarActividad(
                'admin',
                'furgoneta_mantenimiento.php',
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
        break;

    case "garantias_proximas":
        $dias = isset($_POST["dias"]) ? intval($_POST["dias"]) : 30;
        $datos = $mantenimiento->get_garantias_proximas_vencer($dias);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "resumen_itv_todas":
        $datos = $mantenimiento->get_resumen_itv_todas();

        $registro->registrarActividad(
            'admin',
            'furgoneta_mantenimiento.php',
            'resumen_itv_todas',
            "Resumen de ITV de todas las furgonetas obtenido",
            'info'
        );

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "costo_promedio_tipo":
        $datos = $mantenimiento->get_costo_promedio_por_tipo();

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "talleres_mas_utilizados":
        $limite = isset($_POST["limite"]) ? intval($_POST["limite"]) : 10;
        $datos = $mantenimiento->get_talleres_mas_utilizados($limite);

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
        break;

    case "listar_por_fecha":
        if (empty($_POST["id_furgoneta"]) || empty($_POST["fecha_inicio"]) || empty($_POST["fecha_fin"])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Faltan parámetros requeridos: id_furgoneta, fecha_inicio, fecha_fin'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $mantenimiento->get_mantenimientos_por_fecha(
            $_POST["id_furgoneta"],
            $_POST["fecha_inicio"],
            $_POST["fecha_fin"]
        );

        header('Content-Type: application/json');
        echo json_encode([
            'success' => true,
            'data' => $datos
        ], JSON_UNESCAPED_UNICODE);
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
