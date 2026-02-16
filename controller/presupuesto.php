<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Presupuesto.php";

require_once '../config/funciones.php'; // âœ… Se incluye correctamente el archivo de conexiÃ³n

$registro = new RegistroActividad(); // âœ… Se crea una instancia de la clase RegistroActividad
$presupuesto = new Presupuesto();


// FunciÃ³n para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

$op = $_GET['op'] ?? $_POST['op'] ?? null;
switch ($op) {

    case "listar":
        $datos = $presupuesto->get_presupuestos();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                // Datos bÃ¡sicos del presupuesto
                "id_presupuesto" => $row["id_presupuesto"],
                "id_empresa" => $row["id_empresa"] ?? 1,
                "numero_presupuesto" => $row["numero_presupuesto"],
                "version_actual_presupuesto" => $row["version_actual_presupuesto"] ?? 1,
                "fecha_presupuesto" => $row["fecha_presupuesto"],
                "fecha_validez_presupuesto" => $row["fecha_validez_presupuesto"],
                "fecha_inicio_evento_presupuesto" => $row["fecha_inicio_evento_presupuesto"],
                "fecha_fin_evento_presupuesto" => $row["fecha_fin_evento_presupuesto"],
                "numero_pedido_cliente_presupuesto" => $row["numero_pedido_cliente_presupuesto"],
                "aplicar_coeficientes_presupuesto" => isset($row["aplicar_coeficientes_presupuesto"]) ? (bool)$row["aplicar_coeficientes_presupuesto"] : true,
                "descuento_presupuesto" => $row["descuento_presupuesto"] ?? 0.00,
                "nombre_evento_presupuesto" => $row["nombre_evento_presupuesto"],
                
                // UbicaciÃ³n del evento (4 campos separados)
                "direccion_evento_presupuesto" => $row["direccion_evento_presupuesto"] ?? null,
                "poblacion_evento_presupuesto" => $row["poblacion_evento_presupuesto"] ?? null,
                "cp_evento_presupuesto" => $row["cp_evento_presupuesto"] ?? null,
                "provincia_evento_presupuesto" => $row["provincia_evento_presupuesto"] ?? null,
                "ubicacion_completa_evento_presupuesto" => $row["ubicacion_completa_evento_presupuesto"] ?? null,
                
                // Observaciones
                "observaciones_cabecera_presupuesto" => $row["observaciones_cabecera_presupuesto"],
                "observaciones_cabecera_ingles_presupuesto" => $row["observaciones_cabecera_ingles_presupuesto"] ?? null,
                "observaciones_pie_presupuesto" => $row["observaciones_pie_presupuesto"],
                "observaciones_pie_ingles_presupuesto" => $row["observaciones_pie_ingles_presupuesto"] ?? null,
                "mostrar_obs_familias_presupuesto" => $row["mostrar_obs_familias_presupuesto"],
                "mostrar_obs_articulos_presupuesto" => $row["mostrar_obs_articulos_presupuesto"],
                "observaciones_internas_presupuesto" => $row["observaciones_internas_presupuesto"],
                
                // Estado y fechas de control
                "activo_presupuesto" => $row["activo_presupuesto"],
                "created_at_presupuesto" => $row["created_at_presupuesto"],
                "updated_at_presupuesto" => $row["updated_at_presupuesto"],
                
                // Datos del cliente
                "id_cliente" => $row["id_cliente"],
                "codigo_cliente" => $row["codigo_cliente"],
                "nombre_cliente" => $row["nombre_cliente"],
                "nif_cliente" => $row["nif_cliente"],
                "telefono_cliente" => $row["telefono_cliente"],
                "email_cliente" => $row["email_cliente"],
                "porcentaje_descuento_cliente" => $row["porcentaje_descuento_cliente"] ?? 0.00,
                
                // DirecciÃ³n principal del cliente
                "direccion_cliente" => $row["direccion_cliente"],
                "cp_cliente" => $row["cp_cliente"],
                "poblacion_cliente" => $row["poblacion_cliente"],
                "provincia_cliente" => $row["provincia_cliente"],
                
                // DirecciÃ³n de facturaciÃ³n
                "nombre_facturacion_cliente" => $row["nombre_facturacion_cliente"],
                "direccion_facturacion_cliente" => $row["direccion_facturacion_cliente"],
                "cp_facturacion_cliente" => $row["cp_facturacion_cliente"],
                "poblacion_facturacion_cliente" => $row["poblacion_facturacion_cliente"],
                "provincia_facturacion_cliente" => $row["provincia_facturacion_cliente"],
                "direccion_completa_cliente" => $row["direccion_completa_cliente"],
                "direccion_facturacion_completa_cliente" => $row["direccion_facturacion_completa_cliente"],
                
                // Datos del contacto del cliente
                "id_contacto_cliente" => $row["id_contacto_cliente"] ?? null,
                "nombre_contacto_cliente" => $row["nombre_contacto_cliente"] ?? null,
                "apellidos_contacto_cliente" => $row["apellidos_contacto_cliente"] ?? null,
                "nombre_completo_contacto" => $row["nombre_completo_contacto"] ?? null,
                "cargo_contacto_cliente" => $row["cargo_contacto_cliente"] ?? null,
                "departamento_contacto_cliente" => $row["departamento_contacto_cliente"] ?? null,
                "telefono_contacto_cliente" => $row["telefono_contacto_cliente"] ?? null,
                "movil_contacto_cliente" => $row["movil_contacto_cliente"] ?? null,
                "email_contacto_cliente" => $row["email_contacto_cliente"] ?? null,
                "extension_contacto_cliente" => $row["extension_contacto_cliente"] ?? null,
                "principal_contacto_cliente" => $row["principal_contacto_cliente"] ?? null,
                
                // Datos del estado del presupuesto
                "id_estado_ppto" => $row["id_estado_ppto"],
                "codigo_estado_ppto" => $row["codigo_estado_ppto"],
                "nombre_estado_ppto" => $row["nombre_estado_ppto"],
                "color_estado_ppto" => $row["color_estado_ppto"],
                "orden_estado_ppto" => $row["orden_estado_ppto"],
                
                // Datos de la forma de pago
                "id_forma_pago" => $row["id_forma_pago"] ?? null,
                "codigo_pago" => $row["codigo_pago"] ?? null,
                "nombre_pago" => $row["nombre_pago"] ?? null,
                "descuento_pago" => $row["descuento_pago"] ?? null,
                "porcentaje_anticipo_pago" => $row["porcentaje_anticipo_pago"] ?? null,
                "dias_anticipo_pago" => $row["dias_anticipo_pago"] ?? null,
                "porcentaje_final_pago" => $row["porcentaje_final_pago"] ?? null,
                "dias_final_pago" => $row["dias_final_pago"] ?? null,
                "observaciones_pago" => $row["observaciones_pago"] ?? null,
                
                // Datos del mÃ©todo de pago
                "id_metodo_pago" => $row["id_metodo_pago"] ?? null,
                "codigo_metodo_pago" => $row["codigo_metodo_pago"] ?? null,
                "nombre_metodo_pago" => $row["nombre_metodo_pago"] ?? null,
                "observaciones_metodo_pago" => $row["observaciones_metodo_pago"] ?? null,
                
                // Datos del mÃ©todo de contacto
                "id_metodo" => $row["id_metodo"] ?? null,
                "nombre_metodo_contacto" => $row["nombre_metodo_contacto"] ?? null,
                
                // Total del presupuesto
                "total_presupuesto" => $row["total_presupuesto"] ?? 0,
                
                // Campos calculados - Fechas
                "duracion_evento_dias" => $row["duracion_evento_dias"] ?? null,
                "dias_hasta_inicio_evento" => $row["dias_hasta_inicio_evento"] ?? null,
                "dias_hasta_fin_evento" => $row["dias_hasta_fin_evento"] ?? null,
                "estado_evento_presupuesto" => $row["estado_evento_presupuesto"] ?? null,
                "dias_validez_restantes" => $row["dias_validez_restantes"] ?? null,
                "estado_validez_presupuesto" => $row["estado_validez_presupuesto"] ?? null,
                
                // Campos calculados - Pagos
                "tipo_pago_presupuesto" => $row["tipo_pago_presupuesto"] ?? null,
                "descripcion_completa_forma_pago" => $row["descripcion_completa_forma_pago"] ?? null,
                "fecha_vencimiento_anticipo" => $row["fecha_vencimiento_anticipo"] ?? null,
                "fecha_vencimiento_final" => $row["fecha_vencimiento_final"] ?? null,
                
                // Campos calculados - Descuentos
                "comparacion_descuento" => $row["comparacion_descuento"] ?? null,
                "estado_descuento_presupuesto" => $row["estado_descuento_presupuesto"] ?? null,
                "aplica_descuento_presupuesto" => isset($row["aplica_descuento_presupuesto"]) ? (bool)$row["aplica_descuento_presupuesto"] : false,
                "diferencia_descuento" => $row["diferencia_descuento"] ?? 0.00,
                
                // Campos calculados - InformaciÃ³n adicional
                "tiene_direccion_facturacion_diferente" => isset($row["tiene_direccion_facturacion_diferente"]) ? (bool)$row["tiene_direccion_facturacion_diferente"] : false,
                "dias_desde_emision" => $row["dias_desde_emision"] ?? null,
                "prioridad_presupuesto" => $row["prioridad_presupuesto"] ?? null,
                
                // Datos de la versiÃ³n actual
                "id_version_actual" => $row["id_version_actual"] ?? null,
                "numero_version_actual" => $row["numero_version_actual"] ?? null,
                "estado_version_actual" => $row["estado_version_actual"] ?? null,
                "fecha_creacion_version_actual" => $row["fecha_creacion_version_actual"] ?? null,
                
                // Datos de peso del presupuesto
                "peso_total_kg" => $row["peso_total_kg"] ?? null,
                "peso_articulos_normales_kg" => $row["peso_articulos_normales_kg"] ?? null,
                "peso_kits_kg" => $row["peso_kits_kg"] ?? null,
                "total_lineas" => $row["total_lineas"] ?? null,
                "lineas_con_peso" => $row["lineas_con_peso"] ?? null,
                "lineas_sin_peso" => $row["lineas_sin_peso"] ?? null,
                "porcentaje_completitud_peso" => $row["porcentaje_completitud_peso"] ?? null
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
            // DEBUG: Log para ver quÃ© se estÃ¡ recibiendo
            writeToLog([
                'action' => 'guardaryeditar',
                'POST_completo' => $_POST
            ]);
            
            if (empty($_POST["id_presupuesto"])) {
                // Procesar campos opcionales
                $id_contacto_cliente = null;
                if (isset($_POST["id_contacto_cliente"]) && $_POST["id_contacto_cliente"] !== '' && $_POST["id_contacto_cliente"] !== 'null') {
                    $id_contacto_cliente = intval($_POST["id_contacto_cliente"]);
                }
                
                $id_forma_pago = null;
                if (isset($_POST["id_forma_pago"]) && $_POST["id_forma_pago"] !== '' && $_POST["id_forma_pago"] !== 'null') {
                    $id_forma_pago = intval($_POST["id_forma_pago"]);
                }
                
                $id_metodo = null;
                if (isset($_POST["id_metodo"]) && $_POST["id_metodo"] !== '' && $_POST["id_metodo"] !== 'null') {
                    $id_metodo = intval($_POST["id_metodo"]);
                }
                
                $fecha_validez_presupuesto = null;
                if (isset($_POST["fecha_validez_presupuesto"]) && $_POST["fecha_validez_presupuesto"] !== '' && $_POST["fecha_validez_presupuesto"] !== 'null') {
                    $fecha_validez_presupuesto = $_POST["fecha_validez_presupuesto"];
                }
                
                $fecha_inicio_evento_presupuesto = null;
                if (isset($_POST["fecha_inicio_evento_presupuesto"]) && $_POST["fecha_inicio_evento_presupuesto"] !== '' && $_POST["fecha_inicio_evento_presupuesto"] !== 'null') {
                    $fecha_inicio_evento_presupuesto = $_POST["fecha_inicio_evento_presupuesto"];
                }
                
                $fecha_fin_evento_presupuesto = null;
                if (isset($_POST["fecha_fin_evento_presupuesto"]) && $_POST["fecha_fin_evento_presupuesto"] !== '' && $_POST["fecha_fin_evento_presupuesto"] !== 'null') {
                    $fecha_fin_evento_presupuesto = $_POST["fecha_fin_evento_presupuesto"];
                }
                
                // Procesar campo aplicar_coeficientes_presupuesto (boolean)
                $aplicar_coeficientes_presupuesto = isset($_POST["aplicar_coeficientes_presupuesto"]) ? (bool)$_POST["aplicar_coeficientes_presupuesto"] : true;
                
                // Procesar campo descuento_presupuesto (decimal)
                $descuento_presupuesto = isset($_POST["descuento_presupuesto"]) && $_POST["descuento_presupuesto"] !== '' ? floatval($_POST["descuento_presupuesto"]) : 0.00;
                
                writeToLog([
                    'id_contacto_cliente' => $id_contacto_cliente,
                    'id_forma_pago' => $id_forma_pago,
                    'id_metodo' => $id_metodo
                ]);
                
                $resultado = $presupuesto->insert_presupuesto(
                    $_POST["numero_presupuesto"], 
                    $_POST["id_cliente"], 
                    $id_contacto_cliente, 
                    $_POST["id_estado_ppto"], 
                    $id_forma_pago, 
                    $id_metodo, 
                    $_POST["fecha_presupuesto"], 
                    $fecha_validez_presupuesto, 
                    $fecha_inicio_evento_presupuesto, 
                    $fecha_fin_evento_presupuesto, 
                    $_POST["numero_pedido_cliente_presupuesto"], 
                    $aplicar_coeficientes_presupuesto, 
                    $descuento_presupuesto, 
                    $_POST["nombre_evento_presupuesto"], 
                    $_POST["direccion_evento_presupuesto"] ?? '', 
                    $_POST["poblacion_evento_presupuesto"] ?? '', 
                    $_POST["cp_evento_presupuesto"] ?? '', 
                    $_POST["provincia_evento_presupuesto"] ?? '', 
                    $_POST["observaciones_cabecera_presupuesto"], 
                    $_POST["observaciones_cabecera_ingles_presupuesto"] ?? '', 
                    $_POST["observaciones_pie_presupuesto"], 
                    $_POST["observaciones_pie_ingles_presupuesto"] ?? '', 
                    isset($_POST["mostrar_obs_familias_presupuesto"]) ? $_POST["mostrar_obs_familias_presupuesto"] : 1, 
                    isset($_POST["mostrar_obs_articulos_presupuesto"]) ? $_POST["mostrar_obs_articulos_presupuesto"] : 1, 
                    $_POST["observaciones_internas_presupuesto"]
                );
                
                if ($resultado !== false && $resultado > 0) {
                    $registro->registrarActividad(
                        'admin',
                        'presupuesto.php',
                        'Guardar el presupuesto',
                        "Presupuesto guardado exitosamente con ID: $resultado",
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Presupuesto guardado exitosamente',
                        'id_presupuesto' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al insertar el presupuesto en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
                
            } else {
                // Procesar campos opcionales para update
                $id_contacto_cliente = null;
                if (isset($_POST["id_contacto_cliente"]) && $_POST["id_contacto_cliente"] !== '' && $_POST["id_contacto_cliente"] !== 'null') {
                    $id_contacto_cliente = intval($_POST["id_contacto_cliente"]);
                }
                
                $id_forma_pago = null;
                if (isset($_POST["id_forma_pago"]) && $_POST["id_forma_pago"] !== '' && $_POST["id_forma_pago"] !== 'null') {
                    $id_forma_pago = intval($_POST["id_forma_pago"]);
                }
                
                $id_metodo = null;
                if (isset($_POST["id_metodo"]) && $_POST["id_metodo"] !== '' && $_POST["id_metodo"] !== 'null') {
                    $id_metodo = intval($_POST["id_metodo"]);
                }
                
                $fecha_validez_presupuesto = null;
                if (isset($_POST["fecha_validez_presupuesto"]) && $_POST["fecha_validez_presupuesto"] !== '' && $_POST["fecha_validez_presupuesto"] !== 'null') {
                    $fecha_validez_presupuesto = $_POST["fecha_validez_presupuesto"];
                }
                
                $fecha_inicio_evento_presupuesto = null;
                if (isset($_POST["fecha_inicio_evento_presupuesto"]) && $_POST["fecha_inicio_evento_presupuesto"] !== '' && $_POST["fecha_inicio_evento_presupuesto"] !== 'null') {
                    $fecha_inicio_evento_presupuesto = $_POST["fecha_inicio_evento_presupuesto"];
                }
                
                $fecha_fin_evento_presupuesto = null;
                if (isset($_POST["fecha_fin_evento_presupuesto"]) && $_POST["fecha_fin_evento_presupuesto"] !== '' && $_POST["fecha_fin_evento_presupuesto"] !== 'null') {
                    $fecha_fin_evento_presupuesto = $_POST["fecha_fin_evento_presupuesto"];
                }
                
                // Procesar campo aplicar_coeficientes_presupuesto (boolean)
                $aplicar_coeficientes_presupuesto = isset($_POST["aplicar_coeficientes_presupuesto"]) ? (bool)$_POST["aplicar_coeficientes_presupuesto"] : true;
                
                // Procesar campo descuento_presupuesto (decimal)
                $descuento_presupuesto = isset($_POST["descuento_presupuesto"]) && $_POST["descuento_presupuesto"] !== '' ? floatval($_POST["descuento_presupuesto"]) : 0.00;
                
                $resultado = $presupuesto->update_presupuesto(
                    $_POST["id_presupuesto"],
                    $_POST["numero_presupuesto"], 
                    $_POST["id_cliente"], 
                    $id_contacto_cliente, 
                    $_POST["id_estado_ppto"], 
                    $id_forma_pago, 
                    $id_metodo, 
                    $_POST["fecha_presupuesto"], 
                    $fecha_validez_presupuesto, 
                    $fecha_inicio_evento_presupuesto, 
                    $fecha_fin_evento_presupuesto, 
                    $_POST["numero_pedido_cliente_presupuesto"], 
                    $aplicar_coeficientes_presupuesto, 
                    $descuento_presupuesto, 
                    $_POST["nombre_evento_presupuesto"], 
                    $_POST["direccion_evento_presupuesto"] ?? '', 
                    $_POST["poblacion_evento_presupuesto"] ?? '', 
                    $_POST["cp_evento_presupuesto"] ?? '', 
                    $_POST["provincia_evento_presupuesto"] ?? '', 
                    $_POST["observaciones_cabecera_presupuesto"], 
                    $_POST["observaciones_cabecera_ingles_presupuesto"] ?? '', 
                    $_POST["observaciones_pie_presupuesto"], 
                    $_POST["observaciones_pie_ingles_presupuesto"] ?? '', 
                    isset($_POST["mostrar_obs_familias_presupuesto"]) ? $_POST["mostrar_obs_familias_presupuesto"] : 1, 
                    isset($_POST["mostrar_obs_articulos_presupuesto"]) ? $_POST["mostrar_obs_articulos_presupuesto"] : 1, 
                    $_POST["observaciones_internas_presupuesto"]
                );
                
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'presupuesto.php',
                        'Actualizar el presupuesto',
                        "Presupuesto actualizado exitosamente ID: " . $_POST["id_presupuesto"],
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Presupuesto actualizado exitosamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar el presupuesto en la base de datos'
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
        $datos = $presupuesto->get_presupuestoxid($_POST["id_presupuesto"]);

        $registro->registrarActividad(
            'admin',
            'presupuesto.php',
            'Obtener presupuesto seleccionado',
            "Presupuesto obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $presupuesto->delete_presupuestoxid($_POST["id_presupuesto"]);

        $registro->registrarActividad(
            'admin',
            'presupuesto.php',
            'Eliminar presupuesto seleccionado',
            "Presupuesto eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        try {
            $resultado = $presupuesto->activar_presupuestoxid($_POST["id_presupuesto"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'presupuesto.php',
                    'Activar presupuesto seleccionado',
                    "Presupuesto activado exitosamente ",
                    "info"
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Presupuesto activado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo activar el presupuesto'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar el presupuesto: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "desactivar":
        try {
            $resultado = $presupuesto->desactivar_presupuestoxid($_POST["id_presupuesto"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'presupuesto.php',
                    'Desactivar presupuesto seleccionado',
                    "Presupuesto desactivado exitosamente ",
                    "info"
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Presupuesto desactivado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo desactivar el presupuesto'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar el presupuesto: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "verificar":
        $resultado = $presupuesto->verificarPresupuesto(
            $_POST["numero_presupuesto"],
            $_POST["id_presupuesto"] ?? null
        );
        
        // Agregar campo success si no estÃ¡ presente
        if (!isset($resultado['success'])) {
            $resultado['success'] = !isset($resultado['error']);
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "listar_disponibles":
        $datos = $presupuesto->get_presupuestos_disponibles();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_presupuesto" => $row["id_presupuesto"],
                "numero_presupuesto" => $row["numero_presupuesto"],
                "fecha_presupuesto" => $row["fecha_presupuesto"],
                "nombre_cliente" => $row["nombre_cliente"],
                "nombre_evento_presupuesto" => $row["nombre_evento_presupuesto"],
                "nombre_estado_ppto" => $row["nombre_estado_ppto"],
                "activo_presupuesto" => $row["activo_presupuesto"]
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

    case "estadisticas":
        // Obtener estadÃ­sticas completas de presupuestos
        $estadisticas = $presupuesto->obtenerEstadisticas();
        
        if (isset($estadisticas['error'])) {
            // Error al obtener estadÃ­sticas
            $response = array(
                "success" => false,
                "mensaje" => "Error al obtener estadÃ­sticas: " . $estadisticas['mensaje']
            );
            
            // Registrar error
            $registro->registrarActividad(
                $_SESSION['id_usuario'] ?? null,
                'Presupuesto',
                'estadisticas',
                "Error al obtener estadÃ­sticas: " . $estadisticas['mensaje'],
                'error'
            );
        } else {
            // EstadÃ­sticas obtenidas correctamente
            $response = array(
                "success" => true,
                "data" => $estadisticas
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        break;
    case "getMaintenanceEvents":

    // Obtener mes y aÃ±o de los parÃ¡metros
    $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
    $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
    
    // Usar el mÃ©todo que filtra por mes y aÃ±o
    $datos = $presupuesto->get_presupuestos_por_mes($month, $year);

$events = [];

foreach ($datos as $row) {

    // Ignorar si no hay fechas de evento
    if (
        empty($row["fecha_inicio_evento_presupuesto"]) ||
        empty($row["fecha_fin_evento_presupuesto"])
    ) {
        continue;
    }

    $events[] = [
        "id"    => (int)$row["id_presupuesto"],

        // TÃ­tulo visible en el calendario
        "title" => $row["nombre_evento_presupuesto"] . " - " . $row["nombre_cliente"],

        "start" => $row["fecha_inicio_evento_presupuesto"],
        "end"   => $row["fecha_fin_evento_presupuesto"],

        "color" => $row["color_estado_ppto"],

        // ðŸ”¥ AQUÃ VA TODA LA CHICHA
        "extendedProps" => [

            // ===== PRESUPUESTO =====
            "id_presupuesto"              => (int)$row["id_presupuesto"],
            "numero_presupuesto"          => $row["numero_presupuesto"],
            "fecha_presupuesto"           => $row["fecha_presupuesto"],
            "fecha_validez_presupuesto"   => $row["fecha_validez_presupuesto"],
            "estado_validez_presupuesto"  => $row["estado_validez_presupuesto"],
            "prioridad_presupuesto"       => $row["prioridad_presupuesto"],
            "total_presupuesto"           => (float)$row["total_presupuesto"],

            // ===== ESTADO =====
            "id_estado_ppto"     => (int)$row["id_estado_ppto"],
            "codigo_estado"      => $row["codigo_estado_ppto"],
            "nombre_estado"      => $row["nombre_estado_ppto"],
            "color_estado"       => $row["color_estado_ppto"],

            // ===== EVENTO =====
            "nombre_evento"      => $row["nombre_evento_presupuesto"],
            "fecha_inicio_evento"=> $row["fecha_inicio_evento_presupuesto"],
            "fecha_fin_evento"   => $row["fecha_fin_evento_presupuesto"],
            "duracion_evento"    => (int)$row["duracion_evento_dias"],
            "estado_evento"      => $row["estado_evento_presupuesto"],

            "direccion_evento"   => $row["direccion_evento_presupuesto"],
            "cp_evento"          => $row["cp_evento_presupuesto"],
            "poblacion_evento"   => $row["poblacion_evento_presupuesto"],
            "provincia_evento"   => $row["provincia_evento_presupuesto"],

            // ===== CLIENTE =====
            "id_cliente"         => (int)$row["id_cliente"],
            "codigo_cliente"     => $row["codigo_cliente"],
            "nombre_cliente"     => $row["nombre_cliente"],
            "nif_cliente"        => $row["nif_cliente"],
            "telefono_cliente"   => $row["telefono_cliente"],
            "email_cliente"      => $row["email_cliente"],
            "direccion_cliente"  => $row["direccion_completa_cliente"],

            // ===== CONTACTO =====
            "nombre_contacto"    => $row["nombre_completo_contacto"],
            "telefono_contacto"  => $row["telefono_contacto_cliente"],
            "email_contacto"     => $row["email_contacto_cliente"],
            "cargo_contacto"     => $row["cargo_contacto_cliente"],

            // ===== PAGO =====
            "tipo_pago"          => $row["tipo_pago_presupuesto"],
            "forma_pago"         => $row["descripcion_completa_forma_pago"],
            "fecha_vencimiento_anticipo" => $row["fecha_vencimiento_anticipo"],
            "fecha_vencimiento_final"    => $row["fecha_vencimiento_final"],

            // ===== OBSERVACIONES =====
            "obs_cabecera"       => $row["observaciones_cabecera_presupuesto"],
            "obs_pie"            => $row["observaciones_pie_presupuesto"],
            "obs_internas"       => $row["observaciones_internas_presupuesto"],

            // ===== FLAGS =====
            "activo"             => (bool)$row["activo_presupuesto"],
            "tiene_facturacion_diferente" => (bool)$row["tiene_direccion_facturacion_diferente"]
        ]
    ];
}

echo json_encode([
    "status" => "success",
    "data"   => $events
], JSON_UNESCAPED_UNICODE);

exit;

    // =========================================================
    // CASE: get_info_version
    // Obtiene informaciÃ³n de una versiÃ³n especÃ­fica de presupuesto
    // =========================================================
    case "get_info_version":
        $id_version_presupuesto = $_POST["id_version_presupuesto"] ?? null;
        
        if (!$id_version_presupuesto) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de versiÃ³n no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        $datos = $presupuesto->get_info_version($id_version_presupuesto);
        
        // Si hay un error SQL, mostrarlo
        if (is_array($datos) && isset($datos['error'])) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error SQL: ' . $datos['error'],
                'sql_error' => $datos['error']
            ], JSON_UNESCAPED_UNICODE);
            break;
        }
        
        if ($datos) {
            $registro->registrarActividad(
                $_SESSION['usuario'] ?? 'admin',
                'presupuesto.php',
                'get_info_version',
                "Info de versiÃ³n obtenida: ID {$id_version_presupuesto}",
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
                'message' => 'No se pudo obtener la informaciÃ³n de la versiÃ³n'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    // =========================================================
    // CASE: get_fechas_evento
    // Obtiene las fechas del evento para inicializar lÃ­neas
    // =========================================================
    case "get_fechas_evento":
        $id_version_presupuesto = $_POST["id_version_presupuesto"] ?? null;
        
        if (!$id_version_presupuesto) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de versiÃ³n no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Obtener fechas desde el presupuesto
        $sql = "SELECT 
                    p.fecha_inicio_evento_presupuesto,
                    p.fecha_fin_evento_presupuesto
                FROM presupuesto_version pv
                INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                WHERE pv.id_version_presupuesto = ?";
        
        try {
            $conexion = (new Conexion())->getConexion();
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(1, $id_version_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($datos) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'fecha_inicio_evento' => $datos['fecha_inicio_evento_presupuesto'],
                        'fecha_fin_evento' => $datos['fecha_fin_evento_presupuesto']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontraron fechas para esta versiÃ³n'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener fechas: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "mostrar":
        $datos = $presupuesto->get_presupuestoxid($_POST["id_presupuesto"]);

        $registro->registrarActividad(
            'admin',
            'presupuesto.php',
            'Obtener presupuesto seleccionado',
            "Presupuesto obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $presupuesto->delete_presupuestoxid($_POST["id_presupuesto"]);

        $registro->registrarActividad(
            'admin',
            'presupuesto.php',
            'Eliminar presupuesto seleccionado',
            "Presupuesto eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        try {
            $resultado = $presupuesto->activar_presupuestoxid($_POST["id_presupuesto"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'presupuesto.php',
                    'Activar presupuesto seleccionado',
                    "Presupuesto activado exitosamente ",
                    "info"
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Presupuesto activado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo activar el presupuesto'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar el presupuesto: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "desactivar":
        try {
            $resultado = $presupuesto->desactivar_presupuestoxid($_POST["id_presupuesto"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'presupuesto.php',
                    'Desactivar presupuesto seleccionado',
                    "Presupuesto desactivado exitosamente ",
                    "info"
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Presupuesto desactivado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo desactivar el presupuesto'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar el presupuesto: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "verificar":
        $resultado = $presupuesto->verificarPresupuesto(
            $_POST["numero_presupuesto"],
            $_POST["id_presupuesto"] ?? null
        );
        
        // Agregar campo success si no estÃ¡ presente
        if (!isset($resultado['success'])) {
            $resultado['success'] = !isset($resultado['error']);
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "listar_disponibles":
        $datos = $presupuesto->get_presupuestos_disponibles();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_presupuesto" => $row["id_presupuesto"],
                "numero_presupuesto" => $row["numero_presupuesto"],
                "fecha_presupuesto" => $row["fecha_presupuesto"],
                "nombre_cliente" => $row["nombre_cliente"],
                "nombre_evento_presupuesto" => $row["nombre_evento_presupuesto"],
                "nombre_estado_ppto" => $row["nombre_estado_ppto"],
                "activo_presupuesto" => $row["activo_presupuesto"]
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

    case "estadisticas":
        // Obtener estadÃ­sticas completas de presupuestos
        $estadisticas = $presupuesto->obtenerEstadisticas();
        
        if (isset($estadisticas['error'])) {
            // Error al obtener estadÃ­sticas
            $response = array(
                "success" => false,
                "mensaje" => "Error al obtener estadÃ­sticas: " . $estadisticas['mensaje']
            );
            
            // Registrar error
            $registro->registrarActividad(
                $_SESSION['id_usuario'] ?? null,
                'Presupuesto',
                'estadisticas',
                "Error al obtener estadÃ­sticas: " . $estadisticas['mensaje'],
                'error'
            );
        } else {
            // EstadÃ­sticas obtenidas correctamente
            $response = array(
                "success" => true,
                "data" => $estadisticas
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        break;
    case "getMaintenanceEvents":

    // Obtener mes y aÃ±o de los parÃ¡metros
    $month = isset($_POST['month']) ? intval($_POST['month']) : date('n');
    $year = isset($_POST['year']) ? intval($_POST['year']) : date('Y');
    
    // Usar el mÃ©todo que filtra por mes y aÃ±o
    $datos = $presupuesto->get_presupuestos_por_mes($month, $year);

$events = [];

foreach ($datos as $row) {

    // Ignorar si no hay fechas de evento
    if (
        empty($row["fecha_inicio_evento_presupuesto"]) ||
        empty($row["fecha_fin_evento_presupuesto"])
    ) {
        continue;
    }

    $events[] = [
        "id"    => (int)$row["id_presupuesto"],

        // TÃ­tulo visible en el calendario
        "title" => $row["nombre_evento_presupuesto"] . " - " . $row["nombre_cliente"],

        "start" => $row["fecha_inicio_evento_presupuesto"],
        "end"   => $row["fecha_fin_evento_presupuesto"],

        "color" => $row["color_estado_ppto"],

        // ðŸ”¥ AQUÃ VA TODA LA CHICHA
        "extendedProps" => [

            // ===== PRESUPUESTO =====
            "id_presupuesto"              => (int)$row["id_presupuesto"],
            "numero_presupuesto"          => $row["numero_presupuesto"],
            "fecha_presupuesto"           => $row["fecha_presupuesto"],
            "fecha_validez_presupuesto"   => $row["fecha_validez_presupuesto"],
            "estado_validez_presupuesto"  => $row["estado_validez_presupuesto"],
            "prioridad_presupuesto"       => $row["prioridad_presupuesto"],
            "total_presupuesto"           => (float)$row["total_presupuesto"],

            // ===== ESTADO =====
            "id_estado_ppto"     => (int)$row["id_estado_ppto"],
            "codigo_estado"      => $row["codigo_estado_ppto"],
            "nombre_estado"      => $row["nombre_estado_ppto"],
            "color_estado"       => $row["color_estado_ppto"],

            // ===== EVENTO =====
            "nombre_evento"      => $row["nombre_evento_presupuesto"],
            "fecha_inicio_evento"=> $row["fecha_inicio_evento_presupuesto"],
            "fecha_fin_evento"   => $row["fecha_fin_evento_presupuesto"],
            "duracion_evento"    => (int)$row["duracion_evento_dias"],
            "estado_evento"      => $row["estado_evento_presupuesto"],

            "direccion_evento"   => $row["direccion_evento_presupuesto"],
            "cp_evento"          => $row["cp_evento_presupuesto"],
            "poblacion_evento"   => $row["poblacion_evento_presupuesto"],
            "provincia_evento"   => $row["provincia_evento_presupuesto"],

            // ===== CLIENTE =====
            "id_cliente"         => (int)$row["id_cliente"],
            "codigo_cliente"     => $row["codigo_cliente"],
            "nombre_cliente"     => $row["nombre_cliente"],
            "nif_cliente"        => $row["nif_cliente"],
            "telefono_cliente"   => $row["telefono_cliente"],
            "email_cliente"      => $row["email_cliente"],
            "direccion_cliente"  => $row["direccion_completa_cliente"],

            // ===== CONTACTO =====
            "nombre_contacto"    => $row["nombre_completo_contacto"],
            "telefono_contacto"  => $row["telefono_contacto_cliente"],
            "email_contacto"     => $row["email_contacto_cliente"],
            "cargo_contacto"     => $row["cargo_contacto_cliente"],

            // ===== PAGO =====
            "tipo_pago"          => $row["tipo_pago_presupuesto"],
            "forma_pago"         => $row["descripcion_completa_forma_pago"],
            "fecha_vencimiento_anticipo" => $row["fecha_vencimiento_anticipo"],
            "fecha_vencimiento_final"    => $row["fecha_vencimiento_final"],

            // ===== OBSERVACIONES =====
            "obs_cabecera"       => $row["observaciones_cabecera_presupuesto"],
            "obs_pie"            => $row["observaciones_pie_presupuesto"],
            "obs_internas"       => $row["observaciones_internas_presupuesto"],

            // ===== FLAGS =====
            "activo"             => (bool)$row["activo_presupuesto"],
            "tiene_facturacion_diferente" => (bool)$row["tiene_direccion_facturacion_diferente"]
        ]
    ];
}

header('Content-Type: application/json');
echo json_encode([
    "status" => "success",
    "data"   => $events
], JSON_UNESCAPED_UNICODE);
break;

    case "obtener_modelo_impresion":
        // Obtener el modelo de impresiÃ³n configurado para una empresa
        try {
            require_once "../models/ImpresionPresupuesto.php";
            
            $id_empresa = $_POST["id_empresa"] ?? null;
            
            if (empty($id_empresa)) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de empresa no proporcionado'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }
            
            $impresion = new ImpresionPresupuesto();
            $modelo = $impresion->get_modelo_impresion($id_empresa);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'modelo' => $modelo
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin',
                'presupuesto.php',
                'obtener_modelo_impresion',
                "Error: " . $e->getMessage(),
                'error'
            );
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener modelo de impresiÃ³n: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
}
?>
