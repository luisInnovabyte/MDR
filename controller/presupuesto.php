<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Presupuesto.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$presupuesto = new Presupuesto();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $presupuesto->get_presupuestos();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                // Datos básicos del presupuesto
                "id_presupuesto" => $row["id_presupuesto"],
                "numero_presupuesto" => $row["numero_presupuesto"],
                "fecha_presupuesto" => $row["fecha_presupuesto"],
                "fecha_validez_presupuesto" => $row["fecha_validez_presupuesto"],
                "fecha_inicio_evento_presupuesto" => $row["fecha_inicio_evento_presupuesto"],
                "fecha_fin_evento_presupuesto" => $row["fecha_fin_evento_presupuesto"],
                "numero_pedido_cliente_presupuesto" => $row["numero_pedido_cliente_presupuesto"],
                "nombre_evento_presupuesto" => $row["nombre_evento_presupuesto"],
                
                // Ubicación del evento (4 campos separados)
                "direccion_evento_presupuesto" => $row["direccion_evento_presupuesto"] ?? null,
                "poblacion_evento_presupuesto" => $row["poblacion_evento_presupuesto"] ?? null,
                "cp_evento_presupuesto" => $row["cp_evento_presupuesto"] ?? null,
                "provincia_evento_presupuesto" => $row["provincia_evento_presupuesto"] ?? null,
                "ubicacion_completa_evento_presupuesto" => $row["ubicacion_completa_evento_presupuesto"] ?? null,
                
                // Observaciones
                "observaciones_cabecera_presupuesto" => $row["observaciones_cabecera_presupuesto"],
                "observaciones_pie_presupuesto" => $row["observaciones_pie_presupuesto"],
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
                
                // Dirección principal del cliente
                "direccion_cliente" => $row["direccion_cliente"],
                "cp_cliente" => $row["cp_cliente"],
                "poblacion_cliente" => $row["poblacion_cliente"],
                "provincia_cliente" => $row["provincia_cliente"],
                
                // Dirección de facturación
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
                
                // Datos del método de pago
                "id_metodo_pago" => $row["id_metodo_pago"] ?? null,
                "codigo_metodo_pago" => $row["codigo_metodo_pago"] ?? null,
                "nombre_metodo_pago" => $row["nombre_metodo_pago"] ?? null,
                "observaciones_metodo_pago" => $row["observaciones_metodo_pago"] ?? null,
                
                // Datos del método de contacto
                "id_metodo" => $row["id_metodo"] ?? null,
                "nombre" => $row["nombre"] ?? null,
                
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
                
                // Campos calculados - Información adicional
                "tiene_direccion_facturacion_diferente" => isset($row["tiene_direccion_facturacion_diferente"]) ? (bool)$row["tiene_direccion_facturacion_diferente"] : false,
                "dias_desde_emision" => $row["dias_desde_emision"] ?? null,
                "prioridad_presupuesto" => $row["prioridad_presupuesto"] ?? null
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
            // DEBUG: Log para ver qué se está recibiendo
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
                    $_POST["nombre_evento_presupuesto"], 
                    $_POST["direccion_evento_presupuesto"] ?? '', 
                    $_POST["poblacion_evento_presupuesto"] ?? '', 
                    $_POST["cp_evento_presupuesto"] ?? '', 
                    $_POST["provincia_evento_presupuesto"] ?? '', 
                    $_POST["observaciones_cabecera_presupuesto"], 
                    $_POST["observaciones_pie_presupuesto"], 
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
                    $_POST["nombre_evento_presupuesto"], 
                    $_POST["direccion_evento_presupuesto"] ?? '', 
                    $_POST["poblacion_evento_presupuesto"] ?? '', 
                    $_POST["cp_evento_presupuesto"] ?? '', 
                    $_POST["provincia_evento_presupuesto"] ?? '', 
                    $_POST["observaciones_cabecera_presupuesto"], 
                    $_POST["observaciones_pie_presupuesto"], 
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
        $presupuesto->activar_presupuestoxid($_POST["id_presupuesto"]);

        $registro->registrarActividad(
            'admin',
            'presupuesto.php',
            'Activar presupuesto seleccionado',
            "Presupuesto activado exitosamente ",
            "info"
        );

        break;

    case "verificar":
        $resultado = $presupuesto->verificarPresupuesto(
            $_POST["numero_presupuesto"],
            $_POST["id_presupuesto"] ?? null
        );
        
        // Agregar campo success si no está presente
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
}
?>