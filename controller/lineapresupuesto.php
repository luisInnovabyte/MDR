<?php
// Activar visualización de errores para debug
error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../public/logs/php_errors_' . date('Ymd') . '.log');

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

// Manejo global de errores
set_exception_handler(function($exception) {
    writeToLog([
        'type' => 'exception',
        'message' => $exception->getMessage(),
        'file' => $exception->getFile(),
        'line' => $exception->getLine(),
        'trace' => $exception->getTraceAsString()
    ]);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'message' => 'Error del servidor: ' . $exception->getMessage(),
        'file' => basename($exception->getFile()),
        'line' => $exception->getLine()
    ], JSON_UNESCAPED_UNICODE);
    exit;
});

switch ($_GET["op"]) {

    // =========================================================
    // CASE: debug - Verifica que todo funciona
    // =========================================================
    case "debug":
        try {
            $id_version = $_POST["id_version_presupuesto"] ?? $_GET["id_version_presupuesto"] ?? 2;
            
            // Test 1: Conexión
            $testConexion = $lineaPresupuesto->getConexion();
            
            // Test 2: Consulta directa a tabla (todas las líneas)
            $sql = "SELECT id_linea_ppto, descripcion_linea_ppto, activo_linea_ppto 
                    FROM linea_presupuesto 
                    WHERE id_version_presupuesto = ?
                    ORDER BY orden_linea_ppto";
            $stmt = $testConexion->prepare($sql);
            $stmt->execute([$id_version]);
            $lineasTabla = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Test 3: Consulta a vista (todas las líneas)
            $sqlVista = "SELECT id_linea_ppto, descripcion_linea_ppto, activo_linea_ppto 
                        FROM v_linea_presupuesto_calculada 
                        WHERE id_version_presupuesto = ?
                        ORDER BY orden_linea_ppto";
            $stmtVista = $testConexion->prepare($sqlVista);
            $stmtVista->execute([$id_version]);
            $lineasVista = $stmtVista->fetchAll(PDO::FETCH_ASSOC);
            
            // Test 4: Método del modelo
            $lineasModelo = $lineaPresupuesto->get_lineas_version($id_version);
            
            // Contar activos e inactivos
            $activosTabla = array_filter($lineasTabla, fn($l) => $l['activo_linea_ppto'] == 1);
            $inactivosTabla = array_filter($lineasTabla, fn($l) => $l['activo_linea_ppto'] == 0);
            
            $activosVista = array_filter($lineasVista, fn($l) => $l['activo_linea_ppto'] == 1);
            $inactivosVista = array_filter($lineasVista, fn($l) => $l['activo_linea_ppto'] == 0);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'tests' => [
                    'conexion' => $testConexion ? 'OK' : 'FAIL',
                    'tabla' => [
                        'total' => count($lineasTabla),
                        'activos' => count($activosTabla),
                        'inactivos' => count($inactivosTabla),
                        'lineas' => $lineasTabla
                    ],
                    'vista' => [
                        'total' => count($lineasVista),
                        'activos' => count($activosVista),
                        'inactivos' => count($inactivosVista),
                        'lineas' => $lineasVista
                    ],
                    'modelo' => [
                        'total' => count($lineasModelo)
                    ]
                ],
                'id_version_test' => $id_version
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
                'file' => basename($e->getFile()),
                'line' => $e->getLine(),
                'trace' => explode("\n", $e->getTraceAsString())
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
        exit;

    // =========================================================
    // CASE: listar
    // Obtiene líneas de una versión de presupuesto
    // =========================================================
    case "listar":
        try {
            writeToLog(['operation' => 'listar', 'start' => true]);
            
            $id_version_presupuesto = $_POST["id_version_presupuesto"] ?? null;
            
            writeToLog(['id_version_presupuesto' => $id_version_presupuesto]);
        
            if (!$id_version_presupuesto) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de versión no proporcionado'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            writeToLog(['calling' => 'get_lineas_version']);
            
            $datos = $lineaPresupuesto->get_lineas_version($id_version_presupuesto);
            
            writeToLog(['rows_fetched' => count($datos)]);
            
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
                    "aplicar_coeficiente_linea_ppto" => $row["aplicar_coeficiente_linea_ppto"] ?? 0,
                    "jornadas_linea_ppto" => $row["jornadas_linea_ppto"] ?? null,
                    "valor_coeficiente_linea_ppto" => $row["valor_coeficiente_linea_ppto"] ?? null,
                    
                    // Fechas del evento
                    "fecha_inicio_linea_ppto" => $row["fecha_inicio_linea_ppto"] ?? null,
                    "fecha_fin_linea_ppto" => $row["fecha_fin_linea_ppto"] ?? null,
                    "dias_evento_linea_ppto" => $row["dias_evento_linea_ppto"] ?? null,
                    
                    // Fechas de planificación
                    "fecha_montaje_linea_ppto" => $row["fecha_montaje_linea_ppto"] ?? null,
                    "fecha_desmontaje_linea_ppto" => $row["fecha_desmontaje_linea_ppto"] ?? null,
                    "dias_planificacion_linea_ppto" => $row["dias_planificacion_linea_ppto"] ?? null,
                    
                    // Cálculos (desde la vista)
                    "subtotal_sin_coeficiente" => $row["subtotal_sin_coeficiente"],
                    "base_imponible" => $row["base_imponible"],
                    "importe_iva" => $row["importe_iva"],
                    "total_linea" => $row["total_linea"],
                    
                    // Notas
                    "notas_tecnicas_linea_ppto" => $row["notas_tecnicas_linea_ppto"] ?? null,
                    "notas_comerciales_linea_ppto" => $row["notas_comerciales_linea_ppto"] ?? null,
                    "observaciones_linea_ppto" => $row["observaciones_linea_ppto"] ?? null,
                    
                    // Localización
                    "localizacion_linea_ppto" => $row["localizacion_linea_ppto"] ?? null,
                    
                    // Kit
                    "id_kit" => $row["id_kit"] ?? null,
                    "es_kit_articulo" => $row["es_kit_articulo"] ?? 0,
                    "ocultar_detalle_kit_linea_ppto" => $row["ocultar_detalle_kit_linea_ppto"] ?? null,
                    
                    // Impuesto
                    "tipo_impuesto" => $row["tipo_impuesto"] ?? null,
                    "tasa_impuesto" => $row["tasa_impuesto"] ?? null,
                    
                    // Auditoría
                    "created_at_linea_ppto" => $row["created_at_linea_ppto"] ?? null,
                    "updated_at_linea_ppto" => $row["updated_at_linea_ppto"] ?? null,
                    
                    // Estado
                    "activo_linea_ppto" => $row["activo_linea_ppto"]
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

        writeToLog(['operation' => 'listar', 'success' => true, 'records' => count($data)]);

        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        
        } catch (Exception $e) {
            writeToLog([
                'operation' => 'listar',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al listar líneas: ' . $e->getMessage(),
                'error_detail' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // =========================================================
    // OBTENER TOTALES (PIE) DE UNA VERSIÓN
    // =========================================================
    case "totales":
        try {
            writeToLog(['operation' => 'totales', 'start' => true]);
            
            $id_version_presupuesto = $_POST["id_version_presupuesto"] ?? null;
            
            if (!$id_version_presupuesto) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de versión no proporcionado'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            writeToLog(['calling' => 'get_totales_version', 'id' => $id_version_presupuesto]);

            $totales = $lineaPresupuesto->get_totales_version($id_version_presupuesto);
            
            writeToLog(['totales_result' => $totales]);
            
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
        } catch (Exception $e) {
            writeToLog([
                'operation' => 'totales',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener totales: ' . $e->getMessage(),
                'error_detail' => [
                    'file' => basename($e->getFile()),
                    'line' => $e->getLine()
                ]
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // =========================================================
    // MOSTRAR UNA LÍNEA POR ID
    // =========================================================
    case "mostrar":
        try {
            $id_linea_ppto = $_POST["id_linea_ppto"] ?? null;
            
            if (!$id_linea_ppto) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de línea no proporcionado'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            $linea = $lineaPresupuesto->get_lineaxid($id_linea_ppto);
            
            if ($linea) {
                $registro->registrarActividad(
                    $_SESSION['usuario'] ?? 'admin',
                    'lineapresupuesto.php',
                    'Mostrar línea',
                    "Línea ID: {$id_linea_ppto}",
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => $linea
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Línea no encontrada'
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener línea: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    // =========================================================
    // DUPLICAR UNA LÍNEA
    // =========================================================
    case "duplicar":
        try {
            $id_linea_ppto = $_POST["id_linea_ppto"] ?? null;
            
            if (!$id_linea_ppto) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'ID de línea no proporcionado'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Obtener línea original
            $linea_original = $lineaPresupuesto->get_lineaxid($id_linea_ppto);
            
            if (!$linea_original) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Línea original no encontrada'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }

            // Obtener el siguiente número de línea y orden
            $id_version = $linea_original['id_version_presupuesto'];
            
            // Obtener max numero_linea_ppto
            $sql_numero = "SELECT COALESCE(MAX(numero_linea_ppto), 0) + 1 as siguiente_numero 
                          FROM linea_presupuesto 
                          WHERE id_version_presupuesto = ?";
            $stmt_numero = $lineaPresupuesto->getConexion()->prepare($sql_numero);
            $stmt_numero->execute([$id_version]);
            $siguiente_numero = $stmt_numero->fetch(PDO::FETCH_ASSOC)['siguiente_numero'];
            
            // Obtener max orden_linea_ppto
            $sql_orden = "SELECT COALESCE(MAX(orden_linea_ppto), 0) + 1 as siguiente_orden 
                         FROM linea_presupuesto 
                         WHERE id_version_presupuesto = ?";
            $stmt_orden = $lineaPresupuesto->getConexion()->prepare($sql_orden);
            $stmt_orden->execute([$id_version]);
            $siguiente_orden = $stmt_orden->fetch(PDO::FETCH_ASSOC)['siguiente_orden'];

            // Preparar datos para nueva línea (clonar todos los datos excepto ID)
            $datos = [
                'id_version_presupuesto' => $linea_original['id_version_presupuesto'],
                'id_articulo' => $linea_original['id_articulo'],
                'id_linea_padre' => $linea_original['id_linea_padre'],
                'id_ubicacion' => $linea_original['id_ubicacion'],
                'id_coeficiente' => $linea_original['id_coeficiente'],
                'id_impuesto' => $linea_original['id_impuesto'],
                'numero_linea_ppto' => $siguiente_numero,
                'tipo_linea_ppto' => $linea_original['tipo_linea_ppto'],
                'nivel_jerarquia' => $linea_original['nivel_jerarquia'],
                'orden_linea_ppto' => $siguiente_orden,
                'codigo_linea_ppto' => $linea_original['codigo_linea_ppto'],
                'descripcion_linea_ppto' => $linea_original['descripcion_linea_ppto'],
                'cantidad_linea_ppto' => $linea_original['cantidad_linea_ppto'],
                'precio_unitario_linea_ppto' => $linea_original['precio_unitario_linea_ppto'],
                'descuento_linea_ppto' => $linea_original['descuento_linea_ppto'],
                'jornadas_linea_ppto' => $linea_original['jornadas_linea_ppto'],
                'valor_coeficiente_linea_ppto' => $linea_original['valor_coeficiente_linea_ppto'],
                'porcentaje_iva_linea_ppto' => $linea_original['porcentaje_iva_linea_ppto'],
                'fecha_montaje_linea_ppto' => $linea_original['fecha_montaje_linea_ppto'],
                'fecha_desmontaje_linea_ppto' => $linea_original['fecha_desmontaje_linea_ppto'],
                'fecha_inicio_linea_ppto' => $linea_original['fecha_inicio_linea_ppto'],
                'fecha_fin_linea_ppto' => $linea_original['fecha_fin_linea_ppto'],
                'observaciones_linea_ppto' => $linea_original['observaciones_linea_ppto'],
                'mostrar_obs_articulo_linea_ppto' => $linea_original['mostrar_obs_articulo_linea_ppto'],
                'ocultar_detalle_kit_linea_ppto' => $linea_original['ocultar_detalle_kit_linea_ppto'],
                'mostrar_en_presupuesto' => $linea_original['mostrar_en_presupuesto'],
                'es_opcional' => $linea_original['es_opcional']
            ];

            // Insertar nueva línea
            $id_nueva_linea = $lineaPresupuesto->insert_linea($datos);

            if ($id_nueva_linea) {
                $registro->registrarActividad(
                    $_SESSION['usuario'] ?? 'admin',
                    'lineapresupuesto.php',
                    'Duplicar línea',
                    "Línea original ID: {$id_linea_ppto}, Nueva línea ID: {$id_nueva_linea}",
                    'info'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Línea duplicada correctamente',
                    'id_nueva_linea' => $id_nueva_linea
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al duplicar la línea'
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (Exception $e) {
            writeToLog([
                'operation' => 'duplicar',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine()
            ]);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al duplicar línea: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
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
                'aplicar_coeficiente_linea_ppto' => isset($_POST['aplicar_coeficiente_linea_ppto']) ? intval($_POST['aplicar_coeficiente_linea_ppto']) : 0,
                'jornadas_linea_ppto' => null,
                'valor_coeficiente_linea_ppto' => null,
                'porcentaje_iva_linea_ppto' => $_POST['porcentaje_iva_linea_ppto'] ?? 21.00,
                'fecha_montaje_linea_ppto' => null,
                'fecha_desmontaje_linea_ppto' => null,
                'fecha_inicio_linea_ppto' => null,
                'fecha_fin_linea_ppto' => null,
                'observaciones_linea_ppto' => null,
                'mostrar_obs_articulo_linea_ppto' => isset($_POST['mostrar_obs_articulo_linea_ppto']) ? intval($_POST['mostrar_obs_articulo_linea_ppto']) : 1,
                'ocultar_detalle_kit_linea_ppto' => isset($_POST['ocultar_detalle_kit_linea_ppto']) ? intval($_POST['ocultar_detalle_kit_linea_ppto']) : 0,
                'mostrar_en_presupuesto' => isset($_POST['mostrar_en_presupuesto']) ? intval($_POST['mostrar_en_presupuesto']) : 1,
                'es_opcional' => isset($_POST['es_opcional']) ? intval($_POST['es_opcional']) : 0,
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

            if (isset($_POST["id_impuesto"]) && $_POST["id_impuesto"] !== '' && $_POST["id_impuesto"] !== 'null') {
                $datos['id_impuesto'] = intval($_POST["id_impuesto"]);
            }

            if (isset($_POST["codigo_linea_ppto"]) && $_POST["codigo_linea_ppto"] !== '' && $_POST["codigo_linea_ppto"] !== 'null') {
                $datos['codigo_linea_ppto'] = $_POST["codigo_linea_ppto"];
            }

            // Solo guardar id_coeficiente, jornadas y valor_coeficiente si aplicar_coeficiente_linea_ppto = 1
            if ($datos['aplicar_coeficiente_linea_ppto'] == 1) {
                // id_coeficiente
                if (isset($_POST["id_coeficiente"]) && $_POST["id_coeficiente"] !== '' && $_POST["id_coeficiente"] !== 'null') {
                    $datos['id_coeficiente'] = intval($_POST["id_coeficiente"]);
                }
                
                // jornadas
                if (isset($_POST["jornadas_linea_ppto"]) && $_POST["jornadas_linea_ppto"] !== '' && $_POST["jornadas_linea_ppto"] !== 'null') {
                    $datos['jornadas_linea_ppto'] = intval($_POST["jornadas_linea_ppto"]);
                }
                
                // valor_coeficiente
                if (isset($_POST["valor_coeficiente_linea_ppto"]) && $_POST["valor_coeficiente_linea_ppto"] !== '' && $_POST["valor_coeficiente_linea_ppto"] !== 'null') {
                    $datos['valor_coeficiente_linea_ppto'] = floatval($_POST["valor_coeficiente_linea_ppto"]);
                }
            } else {
                // Si NO se aplica coeficiente, forzar todo a NULL
                $datos['id_coeficiente'] = null;
                $datos['jornadas_linea_ppto'] = null;
                $datos['valor_coeficiente_linea_ppto'] = null;
            }

            // Campos de fecha
            if (isset($_POST["fecha_montaje_linea_ppto"]) && $_POST["fecha_montaje_linea_ppto"] !== '' && $_POST["fecha_montaje_linea_ppto"] !== 'null') {
                $datos['fecha_montaje_linea_ppto'] = $_POST["fecha_montaje_linea_ppto"];
            }

            if (isset($_POST["fecha_desmontaje_linea_ppto"]) && $_POST["fecha_desmontaje_linea_ppto"] !== '' && $_POST["fecha_desmontaje_linea_ppto"] !== 'null') {
                $datos['fecha_desmontaje_linea_ppto'] = $_POST["fecha_desmontaje_linea_ppto"];
            }

            if (isset($_POST["fecha_inicio_linea_ppto"]) && $_POST["fecha_inicio_linea_ppto"] !== '' && $_POST["fecha_inicio_linea_ppto"] !== 'null') {
                $datos['fecha_inicio_linea_ppto'] = $_POST["fecha_inicio_linea_ppto"];
            }

            if (isset($_POST["fecha_fin_linea_ppto"]) && $_POST["fecha_fin_linea_ppto"] !== '' && $_POST["fecha_fin_linea_ppto"] !== 'null') {
                $datos['fecha_fin_linea_ppto'] = $_POST["fecha_fin_linea_ppto"];
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
    // DESACTIVAR LÍNEA (SOFT DELETE)
    // =========================================================
    case "desactivar":
        try {
            $id_linea_ppto = intval($_POST["id_linea_ppto"]);
            
            $resultado = $lineaPresupuesto->delete_lineaxid($id_linea_ppto);
            
            if ($resultado) {
                $registro->registrarActividad(
                    $_SESSION['usuario'] ?? 'admin',
                    'lineapresupuesto.php',
                    'Desactivar línea',
                    "Línea desactivada ID: {$id_linea_ppto}",
                    'warning'
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Línea desactivada correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al desactivar la línea'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar línea: ' . $e->getMessage()
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
