<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Clientes.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$cliente = new Clientes();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $cliente->get_clientes();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                // Datos básicos del cliente
                "id_cliente" => $row["id_cliente"],
                "codigo_cliente" => $row["codigo_cliente"],
                "nombre_cliente" => $row["nombre_cliente"],
                
                // Dirección principal
                "direccion_cliente" => $row["direccion_cliente"],
                "cp_cliente" => $row["cp_cliente"],
                "poblacion_cliente" => $row["poblacion_cliente"],
                "provincia_cliente" => $row["provincia_cliente"],
                
                // Datos fiscales y contacto
                "nif_cliente" => $row["nif_cliente"],
                "telefono_cliente" => $row["telefono_cliente"],
                "fax_cliente" => $row["fax_cliente"],
                "web_cliente" => $row["web_cliente"],
                "email_cliente" => $row["email_cliente"],
                
                // Dirección de facturación
                "nombre_facturacion_cliente" => $row["nombre_facturacion_cliente"],
                "direccion_facturacion_cliente" => $row["direccion_facturacion_cliente"],
                "cp_facturacion_cliente" => $row["cp_facturacion_cliente"],
                "poblacion_facturacion_cliente" => $row["poblacion_facturacion_cliente"],
                "provincia_facturacion_cliente" => $row["provincia_facturacion_cliente"],
                
                // Observaciones y estado
                "observaciones_cliente" => $row["observaciones_cliente"],
                "activo_cliente" => $row["activo_cliente"],
                "created_at_cliente" => $row["created_at_cliente"],
                "updated_at_cliente" => $row["updated_at_cliente"],
                
                // Sistema de descuentos
                "porcentaje_descuento_cliente" => $row["porcentaje_descuento_cliente"] ?? 0.00,
                "categoria_descuento_cliente" => $row["categoria_descuento_cliente"] ?? 'Sin descuento',
                "tiene_descuento_cliente" => isset($row["tiene_descuento_cliente"]) ? (bool)$row["tiene_descuento_cliente"] : false,
                
                // Datos de la forma de pago habitual
                "id_forma_pago_habitual" => $row["id_forma_pago_habitual"],
                "codigo_pago" => $row["codigo_pago"] ?? null,
                "nombre_pago" => $row["nombre_pago"] ?? null,
                "descuento_pago" => $row["descuento_pago"] ?? null,
                "porcentaje_anticipo_pago" => $row["porcentaje_anticipo_pago"] ?? null,
                "dias_anticipo_pago" => $row["dias_anticipo_pago"] ?? null,
                "porcentaje_final_pago" => $row["porcentaje_final_pago"] ?? null,
                "dias_final_pago" => $row["dias_final_pago"] ?? null,
                "observaciones_pago" => $row["observaciones_pago"] ?? null,
                "activo_pago" => $row["activo_pago"] ?? null,
                
                // Datos del método de pago
                "id_metodo_pago" => $row["id_metodo_pago"] ?? null,
                "codigo_metodo_pago" => $row["codigo_metodo_pago"] ?? null,
                "nombre_metodo_pago" => $row["nombre_metodo_pago"] ?? null,
                "observaciones_metodo_pago" => $row["observaciones_metodo_pago"] ?? null,
                "activo_metodo_pago" => $row["activo_metodo_pago"] ?? null,
                
                // Cantidad de contactos (manteniendo nombre original)
                "cantidad_contactos" => isset($row["cantidad_contactos_cliente"]) ? intval($row["cantidad_contactos_cliente"]) : 0,
                
                // Campos calculados
                "tipo_pago_cliente" => $row["tipo_pago_cliente"] ?? null,
                "descripcion_forma_pago_cliente" => $row["descripcion_forma_pago_cliente"] ?? null,
                "direccion_completa_cliente" => $row["direccion_completa_cliente"] ?? null,
                "direccion_facturacion_completa_cliente" => $row["direccion_facturacion_completa_cliente"] ?? null,
                "tiene_direccion_facturacion_diferente" => isset($row["tiene_direccion_facturacion_diferente"]) ? (bool)$row["tiene_direccion_facturacion_diferente"] : false,
                "estado_forma_pago_cliente" => $row["estado_forma_pago_cliente"] ?? null
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
                'id_forma_pago_habitual_raw' => $_POST["id_forma_pago_habitual"] ?? 'NO_EXISTE',
                'id_forma_pago_habitual_empty' => empty($_POST["id_forma_pago_habitual"]) ? 'SI' : 'NO',
                'POST_completo' => $_POST
            ]);
            
            if (empty($_POST["id_cliente"])) {
                // Procesar id_forma_pago_habitual
                $id_forma_pago = null;
                if (isset($_POST["id_forma_pago_habitual"]) && $_POST["id_forma_pago_habitual"] !== '' && $_POST["id_forma_pago_habitual"] !== 'null') {
                    $id_forma_pago = intval($_POST["id_forma_pago_habitual"]);
                }
                
                writeToLog([
                    'id_forma_pago_procesado' => $id_forma_pago,
                    'tipo' => gettype($id_forma_pago)
                ]);
                
                // Procesar porcentaje_descuento_cliente (valor por defecto 0.00)
                $porcentaje_descuento = 0.00;
                if (isset($_POST["porcentaje_descuento_cliente"]) && $_POST["porcentaje_descuento_cliente"] !== '') {
                    $porcentaje_descuento = floatval($_POST["porcentaje_descuento_cliente"]);
                }
                
                // *** PUNTO 17: Procesar campos de exención de IVA ***
                // Checkbox exento_iva_cliente (viene como '1' si está marcado, no viene si no está marcado)
                $exento_iva = isset($_POST["exento_iva_cliente"]) ? 1 : 0;
                
                // Textarea justificacion_exencion_iva_cliente (sanitizar con htmlspecialchars)
                $justificacion_iva = null;
                if (isset($_POST["justificacion_exencion_iva_cliente"]) && trim($_POST["justificacion_exencion_iva_cliente"]) !== '') {
                    $justificacion_iva = htmlspecialchars(trim($_POST["justificacion_exencion_iva_cliente"]), ENT_QUOTES, 'UTF-8');
                }
                
                // DEBUG PUNTO 17: Log de valores recibidos
                writeToLog([
                    'DEBUG PUNTO 17 - INSERT' => [
                        'exento_iva_POST_isset' => isset($_POST["exento_iva_cliente"]),
                        'exento_iva_POST_value' => $_POST["exento_iva_cliente"] ?? 'NO_EXISTE',
                        'exento_iva_procesado' => $exento_iva,
                        'justificacion_POST_isset' => isset($_POST["justificacion_exencion_iva_cliente"]),
                        'justificacion_POST_value' => $_POST["justificacion_exencion_iva_cliente"] ?? 'NO_EXISTE',
                        'justificacion_procesado' => $justificacion_iva
                    ]
                ]);
                
                $resultado = $cliente->insert_cliente(
                    $_POST["codigo_cliente"], 
                    $_POST["nombre_cliente"], 
                    $_POST["direccion_cliente"], 
                    $_POST["cp_cliente"], 
                    $_POST["poblacion_cliente"], 
                    $_POST["provincia_cliente"], 
                    $_POST["nif_cliente"], 
                    $_POST["telefono_cliente"], 
                    $_POST["fax_cliente"], 
                    $_POST["web_cliente"], 
                    $_POST["email_cliente"], 
                    $_POST["nombre_facturacion_cliente"], 
                    $_POST["direccion_facturacion_cliente"], 
                    $_POST["cp_facturacion_cliente"], 
                    $_POST["poblacion_facturacion_cliente"], 
                    $_POST["provincia_facturacion_cliente"],
                    $id_forma_pago,
                    $porcentaje_descuento,
                    $_POST["observaciones_cliente"],
                    $exento_iva,
                    $justificacion_iva
                );
                
                if ($resultado !== false && $resultado > 0) {
                    $registro->registrarActividad(
                        'admin',
                        'cliente.php',
                        'Guardar el cliente',
                        "Cliente guardado exitosamente con ID: $resultado",
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Cliente guardado exitosamente',
                        'id_cliente' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al insertar el cliente en la base de datos'
                    ], JSON_UNESCAPED_UNICODE);
                }
                
            } else {
                // Procesar id_forma_pago_habitual para update
                $id_forma_pago = null;
                if (isset($_POST["id_forma_pago_habitual"]) && $_POST["id_forma_pago_habitual"] !== '' && $_POST["id_forma_pago_habitual"] !== 'null') {
                    $id_forma_pago = intval($_POST["id_forma_pago_habitual"]);
                }
                
                // Procesar porcentaje_descuento_cliente (valor por defecto 0.00)
                $porcentaje_descuento = 0.00;
                if (isset($_POST["porcentaje_descuento_cliente"]) && $_POST["porcentaje_descuento_cliente"] !== '') {
                    $porcentaje_descuento = floatval($_POST["porcentaje_descuento_cliente"]);
                }
                
                // *** PUNTO 17: Procesar campos de exención de IVA ***
                // Checkbox exento_iva_cliente (viene como '1' si está marcado, no viene si no está marcado)
                $exento_iva = isset($_POST["exento_iva_cliente"]) ? 1 : 0;
                
                // Textarea justificacion_exencion_iva_cliente (sanitizar con htmlspecialchars)
                $justificacion_iva = null;
                if (isset($_POST["justificacion_exencion_iva_cliente"]) && trim($_POST["justificacion_exencion_iva_cliente"]) !== '') {
                    $justificacion_iva = htmlspecialchars(trim($_POST["justificacion_exencion_iva_cliente"]), ENT_QUOTES, 'UTF-8');
                }
                
                $resultado = $cliente->update_cliente(
                    $_POST["id_cliente"],
                    $_POST["codigo_cliente"], 
                    $_POST["nombre_cliente"], 
                    $_POST["direccion_cliente"], 
                    $_POST["cp_cliente"], 
                    $_POST["poblacion_cliente"], 
                    $_POST["provincia_cliente"], 
                    $_POST["nif_cliente"], 
                    $_POST["telefono_cliente"], 
                    $_POST["fax_cliente"], 
                    $_POST["web_cliente"], 
                    $_POST["email_cliente"], 
                    $_POST["nombre_facturacion_cliente"], 
                    $_POST["direccion_facturacion_cliente"], 
                    $_POST["cp_facturacion_cliente"], 
                    $_POST["poblacion_facturacion_cliente"], 
                    $_POST["provincia_facturacion_cliente"],
                    $id_forma_pago,
                    $porcentaje_descuento,
                    $_POST["observaciones_cliente"],
                    $exento_iva,
                    $justificacion_iva
                );
                
                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'cliente.php',
                        'Actualizar el cliente',
                        "Cliente actualizado exitosamente ID: " . $_POST["id_cliente"],
                        "info"
                    );
                    
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Cliente actualizado exitosamente',
                        'id_cliente' => intval($_POST["id_cliente"])
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Error al actualizar el cliente en la base de datos'
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
        $datos = $cliente->get_clientexid($_POST["id_cliente"]);

        $registro->registrarActividad(
            'admin',
            'cliente.php',
            'Obtener cliente seleccionado',
            "Cliente obtenido exitosamente ",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $cliente->delete_clientexid($_POST["id_cliente"]);

        $registro->registrarActividad(
            'admin',
            'cliente.php',
            'Eliminar cliente seleccionado',
            "Cliente eliminado exitosamente ",
            "info"
        );

        break;

    case "activar":
        $cliente->activar_clientexid($_POST["id_cliente"]);

        $registro->registrarActividad(
            'admin',
            'cliente.php',
            'Activar cliente seleccionado',
            "Cliente activado exitosamente ",
            "info"
        );

        break;

    case "verificar":
        $resultado = $cliente->verificarCliente(
            $_POST["codigo_cliente"],
            $_POST["nombre_cliente"] ?? null,
            $_POST["id_cliente"] ?? null
        );
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "listar_disponibles":
        $datos = $cliente->get_clientes_disponibles();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_cliente" => $row["id_cliente"],
                "codigo_cliente" => $row["codigo_cliente"],
                "nombre_cliente" => $row["nombre_cliente"],
                "direccion_cliente" => $row["direccion_cliente"],
                "cp_cliente" => $row["cp_cliente"],
                "poblacion_cliente" => $row["poblacion_cliente"],
                "provincia_cliente" => $row["provincia_cliente"],
                "nif_cliente" => $row["nif_cliente"],
                "telefono_cliente" => $row["telefono_cliente"],
                "email_cliente" => $row["email_cliente"],
                "activo_cliente" => $row["activo_cliente"]
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