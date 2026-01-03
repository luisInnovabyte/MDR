<?php
require_once "../config/conexion.php";
require_once "../models/Furgoneta.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$furgoneta = new Furgoneta();

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt";
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

switch ($_GET["op"]) {

    case "listar":
        $datos = $furgoneta->get_furgonetas();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_furgoneta" => $row["id_furgoneta"],
                "matricula_furgoneta" => $row["matricula_furgoneta"],
                "marca_furgoneta" => $row["marca_furgoneta"] ?? '',
                "modelo_furgoneta" => $row["modelo_furgoneta"] ?? '',
                "anio_furgoneta" => $row["anio_furgoneta"] ?? '',
                "numero_bastidor_furgoneta" => $row["numero_bastidor_furgoneta"] ?? '',
                "kilometros_entre_revisiones_furgoneta" => $row["kilometros_entre_revisiones_furgoneta"] ?? 10000,
                "fecha_proxima_itv_furgoneta" => $row["fecha_proxima_itv_furgoneta"] ?? '',
                "fecha_vencimiento_seguro_furgoneta" => $row["fecha_vencimiento_seguro_furgoneta"] ?? '',
                "compania_seguro_furgoneta" => $row["compania_seguro_furgoneta"] ?? '',
                "numero_poliza_seguro_furgoneta" => $row["numero_poliza_seguro_furgoneta"] ?? '',
                "capacidad_carga_kg_furgoneta" => $row["capacidad_carga_kg_furgoneta"] ?? '',
                "capacidad_carga_m3_furgoneta" => $row["capacidad_carga_m3_furgoneta"] ?? '',
                "tipo_combustible_furgoneta" => $row["tipo_combustible_furgoneta"] ?? '',
                "consumo_medio_furgoneta" => $row["consumo_medio_furgoneta"] ?? '',
                "taller_habitual_furgoneta" => $row["taller_habitual_furgoneta"] ?? '',
                "telefono_taller_furgoneta" => $row["telefono_taller_furgoneta"] ?? '',
                "estado_furgoneta" => $row["estado_furgoneta"] ?? 'operativa',
                "observaciones_furgoneta" => $row["observaciones_furgoneta"] ?? '',
                "activo_furgoneta" => $row["activo_furgoneta"],
                "created_at_furgoneta" => $row["created_at_furgoneta"],
                "updated_at_furgoneta" => $row["updated_at_furgoneta"]
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

    case "listar_disponibles":
        $datos = $furgoneta->get_furgonetas_disponibles();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_furgoneta" => $row["id_furgoneta"],
                "matricula_furgoneta" => $row["matricula_furgoneta"],
                "marca_furgoneta" => $row["marca_furgoneta"] ?? '',
                "modelo_furgoneta" => $row["modelo_furgoneta"] ?? '',
                "estado_furgoneta" => $row["estado_furgoneta"] ?? 'operativa'
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

    case "listar_operativas":
        $datos = $furgoneta->get_furgonetas_operativas();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_furgoneta" => $row["id_furgoneta"],
                "matricula_furgoneta" => $row["matricula_furgoneta"],
                "marca_furgoneta" => $row["marca_furgoneta"] ?? '',
                "modelo_furgoneta" => $row["modelo_furgoneta"] ?? ''
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

    case "verificarFurgoneta":
        try {
            $matricula_furgoneta = $_GET["matricula_furgoneta"] ?? '';
            $id_furgoneta = $_GET["id_furgoneta"] ?? null;
            
            if (empty($matricula_furgoneta)) {
                echo json_encode([
                    'success' => false,
                    'message' => 'La matrícula es requerida'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            $resultado = $furgoneta->verificarFurgoneta($matricula_furgoneta, $id_furgoneta);
            
            if (isset($resultado['existe'])) {
                echo json_encode([
                    'success' => true,
                    'existe' => $resultado['existe'],
                    'message' => $resultado['existe'] ? 'La matrícula ya existe' : 'La matrícula está disponible'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'existe' => false,
                    'message' => 'Error al verificar la matrícula'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'existe' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "guardaryeditar":
        try {
            writeToLog([
                'action' => 'guardaryeditar',
                'POST_completo' => $_POST
            ]);

            // Procesar campos opcionales
            $marca = isset($_POST["marca_furgoneta"]) && $_POST["marca_furgoneta"] !== '' ? $_POST["marca_furgoneta"] : null;
            $modelo = isset($_POST["modelo_furgoneta"]) && $_POST["modelo_furgoneta"] !== '' ? $_POST["modelo_furgoneta"] : null;
            $anio = isset($_POST["anio_furgoneta"]) && $_POST["anio_furgoneta"] !== '' ? intval($_POST["anio_furgoneta"]) : null;
            $numero_bastidor = isset($_POST["numero_bastidor_furgoneta"]) && $_POST["numero_bastidor_furgoneta"] !== '' ? $_POST["numero_bastidor_furgoneta"] : null;
            $kilometros_entre_revisiones = isset($_POST["kilometros_entre_revisiones_furgoneta"]) && $_POST["kilometros_entre_revisiones_furgoneta"] !== '' ? intval($_POST["kilometros_entre_revisiones_furgoneta"]) : 10000;
            $fecha_proxima_itv = isset($_POST["fecha_proxima_itv_furgoneta"]) && $_POST["fecha_proxima_itv_furgoneta"] !== '' ? $_POST["fecha_proxima_itv_furgoneta"] : null;
            $fecha_vencimiento_seguro = isset($_POST["fecha_vencimiento_seguro_furgoneta"]) && $_POST["fecha_vencimiento_seguro_furgoneta"] !== '' ? $_POST["fecha_vencimiento_seguro_furgoneta"] : null;
            $compania_seguro = isset($_POST["compania_seguro_furgoneta"]) && $_POST["compania_seguro_furgoneta"] !== '' ? $_POST["compania_seguro_furgoneta"] : null;
            $numero_poliza = isset($_POST["numero_poliza_seguro_furgoneta"]) && $_POST["numero_poliza_seguro_furgoneta"] !== '' ? $_POST["numero_poliza_seguro_furgoneta"] : null;
            $capacidad_kg = isset($_POST["capacidad_carga_kg_furgoneta"]) && $_POST["capacidad_carga_kg_furgoneta"] !== '' ? $_POST["capacidad_carga_kg_furgoneta"] : null;
            $capacidad_m3 = isset($_POST["capacidad_carga_m3_furgoneta"]) && $_POST["capacidad_carga_m3_furgoneta"] !== '' ? $_POST["capacidad_carga_m3_furgoneta"] : null;
            $tipo_combustible = isset($_POST["tipo_combustible_furgoneta"]) && $_POST["tipo_combustible_furgoneta"] !== '' ? $_POST["tipo_combustible_furgoneta"] : null;
            $consumo_medio = isset($_POST["consumo_medio_furgoneta"]) && $_POST["consumo_medio_furgoneta"] !== '' ? $_POST["consumo_medio_furgoneta"] : null;
            $taller_habitual = isset($_POST["taller_habitual_furgoneta"]) && $_POST["taller_habitual_furgoneta"] !== '' ? $_POST["taller_habitual_furgoneta"] : null;
            $telefono_taller = isset($_POST["telefono_taller_furgoneta"]) && $_POST["telefono_taller_furgoneta"] !== '' ? $_POST["telefono_taller_furgoneta"] : null;
            $estado = isset($_POST["estado_furgoneta"]) && $_POST["estado_furgoneta"] !== '' ? $_POST["estado_furgoneta"] : 'operativa';
            $observaciones = isset($_POST["observaciones_furgoneta"]) && $_POST["observaciones_furgoneta"] !== '' ? $_POST["observaciones_furgoneta"] : null;

            if (empty($_POST["id_furgoneta"])) {
                // INSERT
                $resultado = $furgoneta->insert_furgoneta(
                    $_POST["matricula_furgoneta"],
                    $marca,
                    $modelo,
                    $anio,
                    $numero_bastidor,
                    $kilometros_entre_revisiones,
                    $fecha_proxima_itv,
                    $fecha_vencimiento_seguro,
                    $compania_seguro,
                    $numero_poliza,
                    $capacidad_kg,
                    $capacidad_m3,
                    $tipo_combustible,
                    $consumo_medio,
                    $taller_habitual,
                    $telefono_taller,
                    $estado,
                    $observaciones
                );

                if ($resultado !== false && $resultado > 0) {
                    $registro->registrarActividad(
                        'admin',
                        'furgoneta.php',
                        'guardaryeditar',
                        "Furgoneta guardada exitosamente con ID: $resultado - Matrícula: " . $_POST["matricula_furgoneta"],
                        'info'
                    );

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Furgoneta guardada exitosamente',
                        'id_furgoneta' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al insertar la furgoneta en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // UPDATE
                $resultado = $furgoneta->update_furgoneta(
                    $_POST["id_furgoneta"],
                    $_POST["matricula_furgoneta"],
                    $marca,
                    $modelo,
                    $anio,
                    $numero_bastidor,
                    $kilometros_entre_revisiones,
                    $fecha_proxima_itv,
                    $fecha_vencimiento_seguro,
                    $compania_seguro,
                    $numero_poliza,
                    $capacidad_kg,
                    $capacidad_m3,
                    $tipo_combustible,
                    $consumo_medio,
                    $taller_habitual,
                    $telefono_taller,
                    $estado,
                    $observaciones
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'furgoneta.php',
                        'guardaryeditar',
                        "Furgoneta actualizada exitosamente ID: " . $_POST["id_furgoneta"] . " - Matrícula: " . $_POST["matricula_furgoneta"],
                        'info'
                    );

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Furgoneta actualizada exitosamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar la furgoneta en la base de datos'
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
        $datos = $furgoneta->get_furgonetaxid($_POST["id_furgoneta"]);

        $registro->registrarActividad(
            'admin',
            'furgoneta.php',
            'mostrar',
            "Furgoneta obtenida exitosamente ID: " . $_POST["id_furgoneta"],
            'info'
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $resultado = $furgoneta->delete_furgonetaxid($_POST["id_furgoneta"]);

        if ($resultado) {
            $registro->registrarActividad(
                'admin',
                'furgoneta.php',
                'eliminar',
                "Furgoneta eliminada exitosamente ID: " . $_POST["id_furgoneta"],
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Furgoneta desactivada correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudo desactivar la furgoneta'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "activar":
        try {
            $resultado = $furgoneta->activar_furgonetaxid($_POST["id_furgoneta"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'furgoneta.php',
                    'activar',
                    "Furgoneta activada exitosamente ID: " . $_POST["id_furgoneta"],
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Furgoneta activada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo activar la furgoneta'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar la furgoneta: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "desactivar":
        try {
            $resultado = $furgoneta->delete_furgonetaxid($_POST["id_furgoneta"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'furgoneta.php',
                    'desactivar',
                    "Furgoneta desactivada exitosamente ID: " . $_POST["id_furgoneta"],
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Furgoneta desactivada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo desactivar la furgoneta'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar la furgoneta: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "verificar":
        $resultado = $furgoneta->verificarMatricula(
            $_POST["matricula_furgoneta"],
            $_POST["id_furgoneta"] ?? null
        );

        if (!isset($resultado['success'])) {
            $resultado['success'] = !isset($resultado['error']);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "cambiar_estado":
        try {
            if (empty($_POST["id_furgoneta"]) || empty($_POST["estado"])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Faltan parámetros requeridos'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            $resultado = $furgoneta->cambiar_estado($_POST["id_furgoneta"], $_POST["estado"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'furgoneta.php',
                    'cambiar_estado',
                    "Estado cambiado exitosamente ID: " . $_POST["id_furgoneta"] . " - Nuevo estado: " . $_POST["estado"],
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Estado actualizado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo actualizar el estado'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al cambiar estado: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "proximas_itv":
        try {
            $dias = isset($_POST["dias"]) ? intval($_POST["dias"]) : 30;
            $datos = $furgoneta->get_furgonetas_proximas_itv($dias);
            
            $registro->registrarActividad(
                'admin',
                'furgoneta.php',
                'proximas_itv',
                "Consultadas furgonetas próximas a ITV (días: $dias)",
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $datos
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "seguro_proximo":
        try {
            $dias = isset($_POST["dias"]) ? intval($_POST["dias"]) : 30;
            $datos = $furgoneta->get_furgonetas_seguro_proximo($dias);
            
            $registro->registrarActividad(
                'admin',
                'furgoneta.php',
                'seguro_proximo',
                "Consultadas furgonetas con seguro próximo a vencer (días: $dias)",
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $datos
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "estadisticas":
        try {
            $estadisticas = $furgoneta->obtenerEstadisticas();

            if (empty($estadisticas)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudieron obtener las estadísticas'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                $registro->registrarActividad(
                    'admin',
                    'furgoneta.php',
                    'estadisticas',
                    "Estadísticas obtenidas correctamente",
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $estadisticas
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

    default:
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Operación no válida'
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>
