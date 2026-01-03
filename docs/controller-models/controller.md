# Documentación del Controller - Presupuesto

## Introducción

Los controllers en la arquitectura MVC son responsables de **recibir las peticiones del cliente** (normalmente AJAX desde las vistas), **procesarlas**, **invocar los métodos correspondientes del modelo**, y **devolver las respuestas en formato JSON**.

## Convención de Nomenclatura

> **IMPORTANTE:** El nombre del archivo del controller debe ser **el mismo que el del modelo**, pero comenzando en **minúsculas**.

### Ejemplos:
| Modelo | Controller |
|--------|------------|
| `Presupuesto.php` | `presupuesto.php` |
| `Cliente.php` | `cliente.php` |
| `Familia.php` | `familia.php` |
| `Proveedor.php` | `proveedor.php` |

Esta convención facilita la identificación y mantenimiento del código, estableciendo una correspondencia clara entre la capa de modelo y la capa de control.

---

## Código Completo: presupuesto.php

```php
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

    case "estadisticas":
        // Obtener estadísticas completas de presupuestos
        $estadisticas = $presupuesto->obtenerEstadisticas();
        
        if (isset($estadisticas['error'])) {
            // Error al obtener estadísticas
            $response = array(
                "success" => false,
                "mensaje" => "Error al obtener estadísticas: " . $estadisticas['mensaje']
            );
            
            // Registrar error
            $registro->registrarActividad(
                $_SESSION['id_usuario'] ?? null,
                'Presupuesto',
                'estadisticas',
                "Error al obtener estadísticas: " . $estadisticas['mensaje'],
                'error'
            );
        } else {
            // Estadísticas obtenidas correctamente
            $response = array(
                "success" => true,
                "data" => $estadisticas
            );
        }
        
        header('Content-Type: application/json');
        echo json_encode($response, JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

---

## Estructura y Funcionamiento del Controller

### 1. Encabezado e Inicialización

```php
<?php
require_once "../config/conexion.php";
require_once "../models/Presupuesto.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$presupuesto = new Presupuesto();
```

**Explicación:**
- Se incluyen las dependencias necesarias: configuración de conexión, el modelo `Presupuesto` y funciones auxiliares.
- Se crean instancias de las clases `RegistroActividad` (para logging) y `Presupuesto` (el modelo).
- Estas instancias se reutilizan en todos los casos del switch.

---

### 2. Función auxiliar de Logging

```php
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt";
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}
```

**Explicación:**
- Función para debugging y desarrollo.
- Escribe logs en archivos diarios en la carpeta `public/logs/`.
- Útil para rastrear peticiones y depurar problemas.

---

### 3. Switch Principal - Manejo de Operaciones

```php
switch ($_GET["op"]) {
    case "listar":
        // ...
    case "guardaryeditar":
        // ...
    case "mostrar":
        // ...
    // ... más casos
}
```

**Explicación:**
- El controller recibe el parámetro `op` por GET que indica la operación a realizar.
- Cada `case` maneja una operación específica.
- Las peticiones AJAX desde la vista especifican el `op` en la URL: `../controller/presupuesto.php?op=listar`

---

## Operaciones Disponibles

### 📋 **case "listar"**

**Propósito:** Obtener todos los presupuestos para mostrar en DataTables.

**Flujo:**
1. Llama al método `get_presupuestos()` del modelo.
2. Recorre los resultados construyendo un array con todos los campos necesarios.
3. Estructura la respuesta en formato DataTables con `draw`, `recordsTotal`, `recordsFiltered`, y `data`.
4. Devuelve JSON con `JSON_UNESCAPED_UNICODE` para mantener caracteres especiales.

**Características destacadas:**
- Maneja **más de 80 campos** incluyendo datos del presupuesto, cliente, contacto, estado, formas de pago y **campos calculados** de la vista SQL.
- Usa operador null coalescing (`??`) para campos opcionales.
- Convierte booleanos explícitamente donde es necesario.

**Respuesta JSON:**
```json
{
  "draw": 1,
  "recordsTotal": 25,
  "recordsFiltered": 25,
  "data": [
    {
      "id_presupuesto": 1,
      "numero_presupuesto": "PPTO-2025-001",
      "nombre_cliente": "Cliente Demo",
      ...
    }
  ]
}
```

---

### 💾 **case "guardaryeditar"**

**Propósito:** Crear un nuevo presupuesto o actualizar uno existente.

**Flujo:**
1. Verifica si `id_presupuesto` está vacío para determinar si es INSERT o UPDATE.
2. **Procesa campos opcionales:** convierte strings vacíos y "null" a `null` real de PHP.
3. Llama a `insert_presupuesto()` o `update_presupuesto()` según corresponda.
4. Registra la actividad en el log del sistema.
5. Devuelve respuesta JSON con `success` y `message`.

**Manejo de campos opcionales:**
```php
$id_contacto_cliente = null;
if (isset($_POST["id_contacto_cliente"]) && $_POST["id_contacto_cliente"] !== '' && $_POST["id_contacto_cliente"] !== 'null') {
    $id_contacto_cliente = intval($_POST["id_contacto_cliente"]);
}
```

**Campos opcionales procesados:**
- `id_contacto_cliente`
- `id_forma_pago`
- `id_metodo`
- `fecha_validez_presupuesto`
- `fecha_inicio_evento_presupuesto`
- `fecha_fin_evento_presupuesto`

**Control de errores:**
- Usa `try-catch` para capturar excepciones.
- Escribe logs de debugging con `writeToLog()`.

**Respuestas JSON:**

✅ Éxito (INSERT):
```json
{
  "success": true,
  "message": "Presupuesto guardado exitosamente",
  "id_presupuesto": 42
}
```

✅ Éxito (UPDATE):
```json
{
  "success": true,
  "message": "Presupuesto actualizado exitosamente"
}
```

❌ Error:
```json
{
  "success": false,
  "message": "Error al insertar el presupuesto en la base de datos"
}
```

---

### 🔍 **case "mostrar"**

**Propósito:** Obtener un presupuesto específico por su ID.

**Flujo:**
1. Recibe `id_presupuesto` por POST.
2. Llama al método `get_presupuestoxid()` del modelo.
3. Registra la actividad.
4. Devuelve el presupuesto en formato JSON.

**Uso típico:** Cargar datos en el formulario de edición.

---

### 🗑️ **case "eliminar"**

**Propósito:** Eliminar un presupuesto (borrado lógico).

**Flujo:**
1. Recibe `id_presupuesto` por POST.
2. Llama al método `delete_presupuestoxid()` del modelo.
3. Registra la actividad.

**Nota:** No devuelve JSON explícito, solo ejecuta la operación.

---

### ✅ **case "activar"**

**Propósito:** Activar un presupuesto desactivado.

**Flujo:**
1. Recibe `id_presupuesto` por POST.
2. Llama al método `activar_presupuestoxid()` del modelo.
3. Registra la actividad si tiene éxito.
4. Devuelve respuesta JSON con `success` y `message`.

**Control de errores:** Usa `try-catch` para manejar excepciones.

**Respuesta JSON:**
```json
{
  "success": true,
  "message": "Presupuesto activado correctamente"
}
```

---

### ❌ **case "desactivar"**

**Propósito:** Desactivar un presupuesto activo.

**Flujo:** Idéntico al caso "activar", pero invoca `desactivar_presupuestoxid()`.

**Nota importante:** Este caso está vinculado a los **triggers de sincronización** que automáticamente establecen el estado como "Cancelado" cuando se desactiva un presupuesto.
Es una nota excepcional ya documentada.
---

### 🔍 **case "verificar"**

**Propósito:** Verificar si un número de presupuesto ya existe (para evitar duplicados).

**Flujo:**
1. Recibe `numero_presupuesto` y opcionalmente `id_presupuesto` (para excluir el actual en ediciones).
2. Llama al método `verificarPresupuesto()` del modelo.
3. Añade campo `success` si no está presente.
4. Devuelve respuesta JSON.

**Uso típico:** Validación en tiempo real mientras el usuario escribe el número de presupuesto.

**Respuesta JSON:**
```json
{
  "success": true,
  "existe": false
}
```

---

### 📊 **case "listar_disponibles"**

**Propósito:** Listar presupuestos activos con información reducida.

**Flujo:**
1. Llama al método `get_presupuestos_disponibles()` del modelo.
2. Construye array con campos básicos (7 campos vs 80+ del listado completo).
3. Devuelve respuesta en formato DataTables.

**Uso típico:** Selector de presupuestos en otros formularios o listados simplificados.

**Campos devueltos:**
- `id_presupuesto`
- `numero_presupuesto`
- `fecha_presupuesto`
- `nombre_cliente`
- `nombre_evento_presupuesto`
- `nombre_estado_ppto`
- `activo_presupuesto`

---

### 📈 **case "estadisticas"** ⚠️ CASO ESPECIAL

**Propósito:** Obtener estadísticas complejas de presupuestos.

> **⚠️ NOTA IMPORTANTE:** El caso "estadísticas" es algo **especialmente diseñado para este controller** de presupuesto. **NO es algo habitual** encontrar en otros controllers del proyecto. Este método fue desarrollado específicamente para satisfacer necesidades analíticas del módulo de presupuestos y no debe considerarse parte del patrón estándar de los controllers.

**Flujo:**
1. Llama al método especial `obtenerEstadisticas()` del modelo Presupuesto.
2. Verifica si hay errores en la respuesta.
3. Registra errores si los hay.
4. Devuelve respuesta JSON estructurada.

**Respuesta JSON (éxito):**
```json
{
  "success": true,
  "data": {
    "generales": {
      "total_presupuestos": 125,
      "total_activos": 98,
      "total_inactivos": 27,
      "valor_total": "450250.75"
    },
    "por_estado": [...],
    "mensuales": [...],
    "alertas": [...]
  }
}
```

**Respuesta JSON (error):**
```json
{
  "success": false,
  "mensaje": "Error al obtener estadísticas: Descripción del error"
}
```

**Características destacadas:**
- Es el único método que devuelve estadísticas agregadas.
- Integra información de múltiples dimensiones (general, estados, tiempo, alertas).
- Tiene su propia gestión de errores especializada.
- Se muestra en un modal específico (`estadisticas.php`) en la interfaz.

---

## Características Comunes del Controller

### 1. **Formato de Respuesta JSON**

Todos los casos que devuelven datos usan:
```php
header('Content-Type: application/json');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
```

- `Content-Type: application/json` indica que la respuesta es JSON.
- `JSON_UNESCAPED_UNICODE` preserva caracteres especiales (ñ, acentos, etc.) sin escapar.

### 2. **Registro de Actividades**

La mayoría de operaciones registran su ejecución:
```php
$registro->registrarActividad(
    'admin',
    'presupuesto.php',
    'Descripción de la acción',
    "Detalles de la operación",
    "info" // o "error"
);
```

### 3. **Manejo de Campos Opcionales**

Patrón repetido para campos que pueden ser `null`:
```php
$campo = null;
if (isset($_POST["campo"]) && $_POST["campo"] !== '' && $_POST["campo"] !== 'null') {
    $campo = $_POST["campo"]; // o intval() si es numérico
}
```

### 4. **Control de Errores**

Los casos importantes usan `try-catch`:
```php
try {
    // Operación
} catch (Exception $e) {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error detallado: ' . $e->getMessage()
    ], JSON_UNESCAPED_UNICODE);
}
```

---

## Flujo de Datos Completo

```
┌─────────────────────┐
│   Vista (AJAX)      │
│  presupuesto.js     │
└──────────┬──────────┘
           │ $.ajax({
           │   url: '../controller/presupuesto.php?op=listar',
           │   type: 'POST',
           │   data: formData
           │ })
           ▼
┌─────────────────────────────┐
│  Controller                 │
│  presupuesto.php            │
├─────────────────────────────┤
│ switch($_GET["op"]) {       │
│   case "listar":            │
│     $presupuesto->get...()  │──────┐
│   case "guardaryeditar":    │      │
│     $presupuesto->insert... │──────┤
│   ...                       │      │
│ }                           │      │
└─────────────────────────────┘      │
                                     ▼
                           ┌──────────────────┐
                           │  Modelo          │
                           │  Presupuesto.php │
                           ├──────────────────┤
                           │ PDO Query        │
                           │ Prepared Stmt    │
                           └────────┬─────────┘
                                    │
                                    ▼
                           ┌──────────────────┐
                           │  Base de Datos   │
                           │  MySQL/MariaDB   │
                           └──────────────────┘
```

---

## Comparación con Otros Controllers

| Característica | presupuesto.php | Controllers Típicos |
|----------------|-----------------|---------------------|
| Operaciones CRUD | ✅ Todas (listar, guardaryeditar, mostrar, eliminar) | ✅ Estándar |
| Activar/Desactivar | ✅ Sí | ✅ Común |
| Verificación | ✅ verificar número único | ⚠️ Algunos |
| Listado reducido | ✅ listar_disponibles | ⚠️ Algunos |
| **Estadísticas** | ⚠️ **Caso especial único** | ❌ **NO habitual** |
| Logging detallado | ✅ writeToLog() | ⚠️ Algunos |
| Manejo de opcionales | ✅ Extensivo (6 campos) | ⚠️ Variable |

---

## Convenciones de Código

### Nombres de Métodos del Modelo

Los métodos del modelo siguen el patrón:
- `get_presupuestos()` - Listado completo
- `get_presupuestos_disponibles()` - Listado filtrado
- `get_presupuestoxid($id)` - Obtener por ID
- `insert_presupuesto(...)` - Insertar
- `update_presupuesto(...)` - Actualizar
- `delete_presupuestoxid($id)` - Eliminar
- `activar_presupuestoxid($id)` - Activar
- `desactivar_presupuestoxid($id)` - Desactivar (custom)
- `verificarPresupuesto(...)` - Verificar existencia
- `obtenerEstadisticas()` - **Método especial** (no estándar)

### Estructura de Respuestas JSON

**Listados (DataTables):**
```json
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 100,
  "data": [...]
}
```

**Operaciones (éxito/error):**
```json
{
  "success": true|false,
  "message": "Mensaje descriptivo",
  "id_presupuesto": 42  // opcional en INSERT
}
```

**Estadísticas:**
```json
{
  "success": true|false,
  "data": {...}  // o "mensaje" en caso de error
}
```

---

## Buenas Prácticas Observadas

### ✅ **Separación de responsabilidades**
- El controller **no contiene lógica de negocio**, solo coordina.
- Toda la lógica SQL está en el modelo.

### ✅ **Validación de datos**
- Campos opcionales se convierten correctamente a `null`.
- Se valida existencia con `verificarPresupuesto()`.

### ✅ **Manejo de errores**
- `try-catch` en operaciones críticas.
- Mensajes de error informativos.

### ✅ **Logging y trazabilidad**
- Uso de `RegistroActividad` para auditoría.
- `writeToLog()` para debugging.

### ✅ **Formato de respuesta consistente**
- Siempre JSON con `JSON_UNESCAPED_UNICODE`.
- Headers `Content-Type` correctos.

### ✅ **Seguridad**
- Los datos llegan al modelo donde se usan **prepared statements**.
- No hay concatenación directa de SQL en el controller.

---

## Aspectos de Mejora Potenciales

### 🔸 **Autenticación y Autorización**
El código actual usa `'admin'` hardcodeado en los logs:
```php
$registro->registrarActividad('admin', ...);
```

**Mejora sugerida:** Usar sesiones para identificar al usuario real:
```php
$registro->registrarActividad($_SESSION['id_usuario'] ?? null, ...);
```

### 🔸 **Validación de entrada**
No hay validación explícita de tipos o formatos antes de pasar al modelo.

**Mejora sugerida:** Validar datos críticos antes de llamar al modelo:
```php
if (!is_numeric($_POST["id_cliente"])) {
    echo json_encode(['success' => false, 'message' => 'ID de cliente inválido']);
    exit;
}
```

### 🔸 **Manejo de permisos**
No hay verificación de permisos por operación.

**Mejora sugerida:** Verificar roles/permisos antes de ejecutar operaciones sensibles.

---

## Resumen

El archivo **presupuesto.php** es un controller que:

1. **Sigue la convención de nomenclatura:** nombre igual al modelo pero en minúsculas.
2. **Maneja 10 operaciones diferentes** mediante un switch basado en el parámetro `op`.
3. **Coordina entre vista y modelo** sin contener lógica de negocio.
4. **Incluye un caso especial "estadísticas"** que NO es habitual en otros controllers.
5. **Implementa logging y trazabilidad** para auditoría y debugging.
6. **Maneja correctamente campos opcionales** convirtiéndolos a `null` cuando corresponde.
7. **Devuelve respuestas JSON consistentes** con encoding UTF-8.
8. **Usa try-catch** en operaciones críticas para control de errores.

Este controller es representativo de la capa de control en la arquitectura MVC del proyecto, sirviendo como **puente entre las peticiones AJAX de la interfaz y los métodos del modelo**, con la particularidad de tener un método analítico avanzado (`estadisticas`) que es específico de este módulo y no debe considerarse parte del patrón estándar.

---

## Enlaces Relacionados

- [Documentación de Models](./models.md) - Estructura y métodos del modelo Presupuesto
- [Documentación de Prompt Models](./prompt_models.md) - Cómo generar modelos
- [Documentación de Prompt Controller](./prompt_controller.md) - Cómo generar controllers
