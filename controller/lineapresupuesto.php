<?php
require_once "../config/conexion.php";
require_once "../models/LineaPresupuesto.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$lineaPresupuesto = new LineaPresupuesto();

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt";
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

switch ($_GET["op"]) {

    // =========================================================
    // CASE: listar
    // Obtiene líneas de una versión de presupuesto
    // =========================================================
    case "listar":
        $id_version_presupuesto = $_POST["id_version_presupuesto"] ?? null;
        
        if (!$id_version_presupuesto) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de versión no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $lineaPresupuesto->get_lineas_version($id_version_presupuesto);
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                // Identificación
                "id_linea_ppto" => $row["id_linea_ppto"],
                "id_version_presupuesto" => $row["id_version_presupuesto"],
                "id_articulo" => $row["id_articulo"],
                "numero_linea_ppto" => $row["numero_linea_ppto"],
                "tipo_linea_ppto" => $row["tipo_linea_ppto"],
                "orden_linea_ppto" => $row["orden_linea_ppto"],
                
                // Datos del artículo
                "codigo_linea_ppto" => $row["codigo_linea_ppto"] ?? null,
                "descripcion_linea_ppto" => $row["descripcion_linea_ppto"],
                "codigo_articulo" => $row["codigo_articulo"] ?? null,
                "nombre_articulo" => $row["nombre_articulo"] ?? null,
                
                // Cantidades y precios
                "cantidad_linea_ppto" => $row["cantidad_linea_ppto"],
                "precio_unitario_linea_ppto" => $row["precio_unitario_linea_ppto"],
                "descuento_linea_ppto" => $row["descuento_linea_ppto"],
                "porcentaje_iva_linea_ppto" => $row["porcentaje_iva_linea_ppto"],
                
                // Coeficiente
                "jornadas_linea_ppto" => $row["jornadas_linea_ppto"] ?? null,
                "valor_coeficiente_linea_ppto" => $row["valor_coeficiente_linea_ppto"] ?? null,
                
                // Cálculos (desde la vista)
                "subtotal_sin_coeficiente" => $row["subtotal_sin_coeficiente"],
                "base_imponible" => $row["base_imponible"],
                "importe_iva" => $row["importe_iva"],
                "total_linea" => $row["total_linea"],
                
                // Impuesto
                "tipo_impuesto" => $row["tipo_impuesto"] ?? null,
                "tasa_impuesto" => $row["tasa_impuesto"] ?? null,
                
                // Estado
                "activo_linea_ppto" => (bool)$row["activo_linea_ppto"]
            );
        }

        $registro->registrarActividad(
            $_SESSION['usuario'] ?? 'admin',
            'lineapresupuesto.php',
            'Listar líneas de versión',
            "Versión ID: {$id_version_presupuesto}",
            'info'
        );

        $results = array(
            "draw" => isset($_POST['draw']) ? intval($_POST['draw']) : 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================
    // OBTENER TOTALES (PIE) DE UNA VERSIÓN
    // =========================================================
    case "totales":
        $id_version_presupuesto = $_POST["id_version_presupuesto"] ?? null;
        
        if (!$id_version_presupuesto) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de versión no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $totales = $lineaPresupuesto->get_totales_version($id_version_presupuesto);
        
        if ($totales) {
            $registro->registrarActividad(
                $_SESSION['usuario'] ?? 'admin',
                'lineapresupuesto.php',
                'Obtener totales de versión',
                "Versión ID: {$id_version_presupuesto}",
                'info'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $totales
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'No se pudieron obtener los totales'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // =========================================================
    // MOSTRAR UNA LÍNEA POR ID
    // =========================================================
    case "mostrar":
        $id_linea_ppto = $_POST["id_linea_ppto"] ?? null;
        
        if (!$id_linea_ppto) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de línea no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $linea = $lineaPresupuesto->get_lineaxid($id_linea_ppto);
        
        $registro->registrarActividad(
            $_SESSION['usuario'] ?? 'admin',
            'lineapresupuesto.php',
            'Mostrar línea',
            "Línea ID: {$id_linea_ppto}",
            'info'
        );

        header('Content-Type: application/json');
        echo json_encode($linea, JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================
    // GUARDAR Y EDITAR LÍNEA
    // =========================================================
    case "guardaryeditar":
        try {
            // Preparar datos
            $datos = [
                'id_version_presupuesto' => $_POST['id_version_presupuesto'],
                'id_articulo' => null,
                'id_linea_padre' => null,
                'id_ubicacion' => null,
                'id_coeficiente' => null,
                'id_impuesto' => null,
                'numero_linea_ppto' => $_POST['numero_linea_ppto'],
                'tipo_linea_ppto' => $_POST['tipo_linea_ppto'] ?? 'articulo',
                'nivel_jerarquia' => $_POST['nivel_jerarquia'] ?? 0,
                'orden_linea_ppto' => $_POST['orden_linea_ppto'] ?? 0,
                'codigo_linea_ppto' => null,
                'descripcion_linea_ppto' => $_POST['descripcion_linea_ppto'],
                'cantidad_linea_ppto' => $_POST['cantidad_linea_ppto'] ?? 1.00,
                'precio_unitario_linea_ppto' => $_POST['precio_unitario_linea_ppto'] ?? 0.00,
                'descuento_linea_ppto' => $_POST['descuento_linea_ppto'] ?? 0.00,
                'jornadas_linea_ppto' => null,
                'valor_coeficiente_linea_ppto' => null,
                'porcentaje_iva_linea_ppto' => $_POST['porcentaje_iva_linea_ppto'] ?? 21.00,
                'observaciones_linea_ppto' => null,
                'mostrar_obs_articulo_linea_ppto' => $_POST['mostrar_obs_articulo_linea_ppto'] ?? 1,
                'ocultar_detalle_kit_linea_ppto' => $_POST['ocultar_detalle_kit_linea_ppto'] ?? 0,
                'mostrar_en_presupuesto' => $_POST['mostrar_en_presupuesto'] ?? 1,
                'es_opcional' => $_POST['es_opcional'] ?? 0,
                'activo_linea_ppto' => $_POST['activo_linea_ppto'] ?? 1
            ];

            // Campos opcionales
            if (isset($_POST["id_articulo"]) && $_POST["id_articulo"] !== '' && $_POST["id_articulo"] !== 'null') {
                $datos['id_articulo'] = intval($_POST["id_articulo"]);
            }

            if (isset($_POST["id_linea_padre"]) && $_POST["id_linea_padre"] !== '' && $_POST["id_linea_padre"] !== 'null') {
                $datos['id_linea_padre'] = intval($_POST["id_linea_padre"]);
            }

            if (isset($_POST["id_ubicacion"]) && $_POST["id_ubicacion"] !== '' && $_POST["id_ubicacion"] !== 'null') {
                $datos['id_ubicacion'] = intval($_POST["id_ubicacion"]);
            }

            if (isset($_POST["id_coeficiente"]) && $_POST["id_coeficiente"] !== '' && $_POST["id_coeficiente"] !== 'null') {
                $datos['id_coeficiente'] = intval($_POST["id_coeficiente"]);
            }

            if (isset($_POST["id_impuesto"]) && $_POST["id_impuesto"] !== '' && $_POST["id_impuesto"] !== 'null') {
                $datos['id_impuesto'] = intval($_POST["id_impuesto"]);
            }

            if (isset($_POST["codigo_linea_ppto"]) && $_POST["codigo_linea_ppto"] !== '' && $_POST["codigo_linea_ppto"] !== 'null') {
                $datos['codigo_linea_ppto'] = $_POST["codigo_linea_ppto"];
            }

            if (isset($_POST["jornadas_linea_ppto"]) && $_POST["jornadas_linea_ppto"] !== '' && $_POST["jornadas_linea_ppto"] !== 'null') {
                $datos['jornadas_linea_ppto'] = intval($_POST["jornadas_linea_ppto"]);
            }

            if (isset($_POST["valor_coeficiente_linea_ppto"]) && $_POST["valor_coeficiente_linea_ppto"] !== '' && $_POST["valor_coeficiente_linea_ppto"] !== 'null') {
                $datos['valor_coeficiente_linea_ppto'] = floatval($_POST["valor_coeficiente_linea_ppto"]);
            }

            if (isset($_POST["observaciones_linea_ppto"]) && $_POST["observaciones_linea_ppto"] !== '' && $_POST["observaciones_linea_ppto"] !== 'null') {
                $datos['observaciones_linea_ppto'] = $_POST["observaciones_linea_ppto"];
            }

            // Determinar si es INSERT o UPDATE
            if (empty($_POST["id_linea_ppto"])) {
                // INSERT
                $id_linea = $lineaPresupuesto->insert_linea($datos);
                
                if ($id_linea) {
                    writeToLog(['operacion' => 'INSERT', 'id_linea' => $id_linea]);
                    
                    $registro->registrarActividad(
                        $_SESSION['usuario'] ?? 'admin',
                        'lineapresupuesto.php',
                        'Guardar línea',
                        "Línea creada ID: {$id_linea}",
                        'success'
                    );

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Línea guardada correctamente',
                        'id_linea_ppto' => $id_linea
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    throw new Exception('Error al insertar la línea');
                }
            } else {
                // UPDATE
                $id_linea_ppto = intval($_POST["id_linea_ppto"]);
                $resultado = $lineaPresupuesto->update_linea($id_linea_ppto, $datos);
                
                if ($resultado) {
                    writeToLog(['operacion' => 'UPDATE', 'id_linea' => $id_linea_ppto]);
                    
                    $registro->registrarActividad(
                        $_SESSION['usuario'] ?? 'admin',
                        'lineapresupuesto.php',
                        'Actualizar línea',
                        "Línea actualizada ID: {$id_linea_ppto}",
                        'success'
                    );

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Línea actualizada correctamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    throw new Exception('Error al actualizar la línea');
                }
            }

        } catch (Exception $e) {
            writeToLog(['error' => $e->getMessage()]);
            
            $registro->registrarActividad(
                $_SESSION['usuario'] ?? 'admin',
                'lineapresupuesto.php',
                'Guardar/Editar línea',
                "Error: " . $e->getMessage(),
                'error'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // =========================================================
    // ELIMINAR LÍNEA (SOFT DELETE)
    // =========================================================
    case "eliminar":
        $id_linea_ppto = intval($_POST["id_linea_ppto"]);
        
        $resultado = $lineaPresupuesto->delete_lineaxid($id_linea_ppto);
        
        if ($resultado) {
            $registro->registrarActividad(
                $_SESSION['usuario'] ?? 'admin',
                'lineapresupuesto.php',
                'Eliminar línea',
                "Línea eliminada ID: {$id_linea_ppto}",
                'warning'
            );

            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'message' => 'Línea eliminada correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al eliminar la línea'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // =========================================================
    // ACTIVAR LÍNEA
    // =========================================================
    case "activar":
        try {
            $id_linea_ppto = intval($_POST["id_linea_ppto"]);
            
            $resultado = $lineaPresupuesto->activar_lineaxid($id_linea_ppto);
            
            if ($resultado) {
                $registro->registrarActividad(
                    $_SESSION['usuario'] ?? 'admin',
                    'lineapresupuesto.php',
                    'Activar línea',
                    "Línea activada ID: {$id_linea_ppto}",
                    'success'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Línea activada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                throw new Exception('No se pudo activar la línea');
            }

        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar línea: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // =========================================================
    // VALIDAR TOTALES
    // =========================================================
    case "validar_totales":
        $id_version_presupuesto = $_POST["id_version_presupuesto"] ?? null;
        
        if (!$id_version_presupuesto) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de versión no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $validacion = $lineaPresupuesto->validar_totales($id_version_presupuesto);
        
        header('Content-Type: application/json');
        echo json_encode($validacion, JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================
    // OPERACIÓN NO RECONOCIDA
    // =========================================================
    default:
        header('Content-Type: application/json');
        echo json_encode([
            'success' => false,
            'message' => 'Operación no reconocida'
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>
