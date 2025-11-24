<?php
require_once "../config/conexion.php";
require_once "../models/FormasPago.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$formaPago = new FormasPago();

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt";
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

switch ($_GET["op"]) {

    case "listar":
        $datos = $formaPago->get_formas_pago();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_pago" => $row["id_pago"],
                "codigo_pago" => $row["codigo_pago"],
                "nombre_pago" => $row["nombre_pago"],
                "id_metodo_pago" => $row["id_metodo_pago"],
                "nombre_metodo_pago" => $row["nombre_metodo_pago"],
                "descuento_pago" => $row["descuento_pago"],
                "porcentaje_anticipo_pago" => $row["porcentaje_anticipo_pago"],
                "dias_anticipo_pago" => $row["dias_anticipo_pago"],
                "porcentaje_final_pago" => $row["porcentaje_final_pago"],
                "dias_final_pago" => $row["dias_final_pago"],
                "tipo_pago" => $row["tipo_pago"],
                "observaciones_pago" => $row["observaciones_pago"],
                "activo_pago" => $row["activo_pago"],
                "created_at_pago" => $row["created_at_pago"],
                "updated_at_pago" => $row["updated_at_pago"]
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
        $datos = $formaPago->get_formas_pago_disponibles();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_pago" => $row["id_pago"],
                "codigo_pago" => $row["codigo_pago"],
                "nombre_pago" => $row["nombre_pago"],
                "id_metodo_pago" => $row["id_metodo_pago"],
                "nombre_metodo_pago" => $row["nombre_metodo_pago"],
                "descuento_pago" => $row["descuento_pago"],
                "porcentaje_anticipo_pago" => $row["porcentaje_anticipo_pago"],
                "dias_anticipo_pago" => $row["dias_anticipo_pago"],
                "porcentaje_final_pago" => $row["porcentaje_final_pago"],
                "dias_final_pago" => $row["dias_final_pago"],
                "tipo_pago" => $row["tipo_pago"],
                "observaciones_pago" => $row["observaciones_pago"],
                "activo_pago" => $row["activo_pago"]
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
            if (empty($_POST["id_pago"])) {
                $resultado = $formaPago->insert_forma_pago(
                    $_POST["codigo_pago"],
                    $_POST["nombre_pago"],
                    $_POST["id_metodo_pago"],
                    $_POST["descuento_pago"],
                    $_POST["porcentaje_anticipo_pago"],
                    $_POST["dias_anticipo_pago"],
                    $_POST["porcentaje_final_pago"],
                    $_POST["dias_final_pago"],
                    $_POST["observaciones_pago"]
                );
                
                if ($resultado !== false && $resultado > 0) {
                    $registro->registrarActividad(
                        'admin',
                        'formaspago.php',
                        'Guardar forma de pago',
                        "Forma de pago guardada exitosamente con ID: $resultado",
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Forma de pago guardada exitosamente',
                        'id_pago' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al insertar la forma de pago en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
                
            } else {
                $resultado = $formaPago->update_forma_pago(
                    $_POST["id_pago"],
                    $_POST["codigo_pago"],
                    $_POST["nombre_pago"],
                    $_POST["id_metodo_pago"],
                    $_POST["descuento_pago"],
                    $_POST["porcentaje_anticipo_pago"],
                    $_POST["dias_anticipo_pago"],
                    $_POST["porcentaje_final_pago"],
                    $_POST["dias_final_pago"],
                    $_POST["observaciones_pago"]
                );
                
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'formaspago.php',
                        'Actualizar forma de pago',
                        "Forma de pago actualizada exitosamente ID: " . $_POST["id_pago"],
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Forma de pago actualizada exitosamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al actualizar la forma de pago en la base de datos'
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
        $datos = $formaPago->get_forma_pagoxid($_POST["id_pago"]);

        $registro->registrarActividad(
            'admin',
            'formaspago.php',
            'Obtener forma de pago seleccionada',
            "Forma de pago obtenida exitosamente",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $formaPago->delete_forma_pagoxid($_POST["id_pago"]);

        $registro->registrarActividad(
            'admin',
            'formaspago.php',
            'Eliminar forma de pago seleccionada',
            "Forma de pago eliminada exitosamente",
            "info"
        );

        break;

    case "activar":
        $formaPago->activar_forma_pagoxid($_POST["id_pago"]);

        $registro->registrarActividad(
            'admin',
            'formaspago.php',
            'Activar forma de pago seleccionada',
            "Forma de pago activada exitosamente",
            "info"
        );

        break;

    case "verificar":
        try {
            $resultado = $formaPago->verificarFormaPago(
                $_POST["codigo_pago"],
                $_POST["nombre_pago"] ?? null,
                $_POST["id_pago"] ?? null
            );
            
            // Verificar si hubo un error en la consulta
            if (isset($resultado['error'])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'error',
                    'message' => 'Error al verificar la forma de pago: ' . $resultado['error']
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => 'success',
                    'existe' => $resultado['existe']
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'error',
                'message' => 'Error al verificar la forma de pago: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
}
?>
