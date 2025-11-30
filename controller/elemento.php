<?php
require_once "../config/conexion.php";
require_once "../models/Elemento.php";
require_once '../config/funciones.php';

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/elemento_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] elemento.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    
    $logDirs = [
        __DIR__ . "/../public/logs/",
        "W:/TOLDOS_AMPLIADO/public/logs/",
        sys_get_temp_dir() . "/toldos_logs/"
    ];
    
    foreach ($logDirs as $logDir) {
        try {
            if (!is_dir($logDir)) {
                mkdir($logDir, 0777, true);
            }
            
            $logFile = $logDir . "elemento_debug_" . date("Ymd") . ".txt";
            
            $result = file_put_contents($logFile, $logMessage, FILE_APPEND | LOCK_EX);
            
            if ($result !== false) {
                break;
            }
        } catch (Exception $e) {
            continue;
        }
    }
}

$registro = new RegistroActividad();
$elemento = new Elemento();

// Aceptar 'op' desde GET o POST (lección aprendida de estado_elemento)
$op = $_GET["op"] ?? $_POST["op"] ?? null;

if (!$op) {
    echo json_encode(["status" => "error", "message" => "Parámetro 'op' no proporcionado"]);
    exit;
}

switch ($op) {

    case "listar":
        // Verificar si se proporciona un filtro específico
        if (isset($_GET["id_articulo"]) && !empty($_GET["id_articulo"])) {
            $datos = $elemento->get_elementos_by_articulo($_GET["id_articulo"]);
        } elseif (isset($_GET["id_estado_elemento"]) && !empty($_GET["id_estado_elemento"])) {
            $datos = $elemento->get_elementos_by_estado($_GET["id_estado_elemento"]);
        } else {
            $datos = $elemento->get_elementos();
        }
        
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_elemento" => $row["id_elemento"],
                "codigo_elemento" => $row["codigo_elemento"],
                "codigo_barras_elemento" => $row["codigo_barras_elemento"] ?? '',
                "descripcion_elemento" => $row["descripcion_elemento"],
                "numero_serie_elemento" => $row["numero_serie_elemento"] ?? '',
                "modelo_elemento" => $row["modelo_elemento"] ?? '',
                "nave_elemento" => $row["nave_elemento"] ?? '',
                "pasillo_columna_elemento" => $row["pasillo_columna_elemento"] ?? '',
                "altura_elemento" => $row["altura_elemento"] ?? '',
                
                // Datos del artículo
                "id_articulo" => $row["id_articulo"],
                "codigo_articulo" => $row["codigo_articulo"],
                "nombre_articulo" => $row["nombre_articulo"],
                "precio_alquiler_articulo" => $row["precio_alquiler_articulo"],
                
                // Datos de la familia
                "id_familia" => $row["id_familia"],
                "codigo_familia" => $row["codigo_familia"],
                "nombre_familia" => $row["nombre_familia"],
                
                // Datos del grupo
                "id_grupo" => $row["id_grupo"] ?? null,
                "codigo_grupo" => $row["codigo_grupo"] ?? '',
                "nombre_grupo" => $row["nombre_grupo"] ?? '',
                
                // Datos de la marca
                "id_marca" => $row["id_marca"] ?? null,
                "codigo_marca" => $row["codigo_marca"] ?? '',
                "nombre_marca" => $row["nombre_marca"] ?? '',
                
                // Datos del estado
                "id_estado_elemento" => $row["id_estado_elemento"],
                "codigo_estado_elemento" => $row["codigo_estado_elemento"],
                "descripcion_estado_elemento" => $row["descripcion_estado_elemento"],
                "color_estado_elemento" => $row["color_estado_elemento"],
                "permite_alquiler_estado_elemento" => $row["permite_alquiler_estado_elemento"],
                
                // Datos económicos
                "fecha_compra_elemento" => $row["fecha_compra_elemento"] ?? '',
                "precio_compra_elemento" => $row["precio_compra_elemento"],
                "proveedor_compra_elemento" => $row["proveedor_compra_elemento"] ?? '',
                "fecha_alta_elemento" => $row["fecha_alta_elemento"] ?? '',
                
                // Garantía y mantenimiento
                "fecha_fin_garantia_elemento" => $row["fecha_fin_garantia_elemento"] ?? '',
                "proximo_mantenimiento_elemento" => $row["proximo_mantenimiento_elemento"] ?? '',
                "estado_garantia_elemento" => $row["estado_garantia_elemento"] ?? 'Sin garantía',
                "estado_mantenimiento_elemento" => $row["estado_mantenimiento_elemento"] ?? 'Sin programar',
                
                // Años en servicio
                "anios_en_servicio_elemento" => $row["anios_en_servicio_elemento"] ?? null,
                
                // Observaciones
                "observaciones_elemento" => $row["observaciones_elemento"] ?? '',
                
                // Estado activo
                "activo_elemento" => $row["activo_elemento"],
                
                // Timestamps
                "created_at_elemento" => $row["created_at_elemento"],
                "updated_at_elemento" => $row["updated_at_elemento"]
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

    case "listarDisponibles":
        // Listar solo elementos disponibles para alquiler
        $datos = $elemento->get_elementos_disponibles();
        
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_elemento" => $row["id_elemento"],
                "codigo_elemento" => $row["codigo_elemento"],
                "descripcion_elemento" => $row["descripcion_elemento"],
                "nombre_articulo" => $row["nombre_articulo"],
                "nombre_familia" => $row["nombre_familia"],
                "descripcion_estado_elemento" => $row["descripcion_estado_elemento"],
                "color_estado_elemento" => $row["color_estado_elemento"],
                "nave_elemento" => $row["nave_elemento"] ?? '',
                "pasillo_columna_elemento" => $row["pasillo_columna_elemento"] ?? '',
                "altura_elemento" => $row["altura_elemento"] ?? ''
            );
        }

        header('Content-Type: application/json');
        echo json_encode($data, JSON_UNESCAPED_UNICODE);
        break;

    case "guardaryeditar":
        writeToLog(['action' => 'guardaryeditar_iniciado', 'timestamp' => date('Y-m-d H:i:s')]);
        
        try {
            $id_articulo_elemento = $_POST["id_articulo_elemento"] ?? '';
            $id_marca_elemento = $_POST["id_marca_elemento"] ?? '';
            $modelo_elemento = $_POST["modelo_elemento"] ?? '';
            $codigo_barras_elemento = $_POST["codigo_barras_elemento"] ?? '';
            $descripcion_elemento = $_POST["descripcion_elemento"] ?? '';
            $numero_serie_elemento = $_POST["numero_serie_elemento"] ?? '';
            $id_estado_elemento = $_POST["id_estado_elemento"] ?? 1;
            $nave_elemento = $_POST["nave_elemento"] ?? '';
            $pasillo_columna_elemento = $_POST["pasillo_columna_elemento"] ?? '';
            $altura_elemento = $_POST["altura_elemento"] ?? '';
            $fecha_compra_elemento = $_POST["fecha_compra_elemento"] ?? '';
            $precio_compra_elemento = $_POST["precio_compra_elemento"] ?? 0.00;
            $proveedor_compra_elemento = $_POST["proveedor_compra_elemento"] ?? '';
            $fecha_alta_elemento = $_POST["fecha_alta_elemento"] ?? '';
            $fecha_fin_garantia_elemento = $_POST["fecha_fin_garantia_elemento"] ?? '';
            $proximo_mantenimiento_elemento = $_POST["proximo_mantenimiento_elemento"] ?? '';
            $observaciones_elemento = $_POST["observaciones_elemento"] ?? '';
            
            writeToLog([
                'action' => 'guardaryeditar_inicio',
                'post_info' => $_POST
            ]);
            
            if (empty($_POST["id_elemento"])) {
                // Insertar nuevo elemento
                $resultado = $elemento->insert_elemento(
                    $id_articulo_elemento,
                    $id_marca_elemento,
                    $modelo_elemento,
                    $codigo_barras_elemento,
                    $descripcion_elemento,
                    $numero_serie_elemento,
                    $id_estado_elemento,
                    $nave_elemento,
                    $pasillo_columna_elemento,
                    $altura_elemento,
                    $fecha_compra_elemento,
                    $precio_compra_elemento,
                    $proveedor_compra_elemento,
                    $fecha_alta_elemento,
                    $fecha_fin_garantia_elemento,
                    $proximo_mantenimiento_elemento,
                    $observaciones_elemento
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'elemento.php',
                        'Guardar elemento',
                        "Elemento guardado exitosamente",
                        "info"
                    );

                    echo json_encode([
                        "status" => "success",
                        "message" => "Elemento insertado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error al insertar el elemento"
                    ]);
                }

            } else {
                // Actualizar elemento existente
                $resultado = $elemento->update_elemento(
                    $_POST["id_elemento"],
                    $id_articulo_elemento,
                    $id_marca_elemento,
                    $modelo_elemento,
                    $codigo_barras_elemento,
                    $descripcion_elemento,
                    $numero_serie_elemento,
                    $id_estado_elemento,
                    $nave_elemento,
                    $pasillo_columna_elemento,
                    $altura_elemento,
                    $fecha_compra_elemento,
                    $precio_compra_elemento,
                    $proveedor_compra_elemento,
                    $fecha_alta_elemento,
                    $fecha_fin_garantia_elemento,
                    $proximo_mantenimiento_elemento,
                    $observaciones_elemento
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'elemento.php',
                        'Actualizar elemento',
                        "Elemento actualizado exitosamente",
                        "info"
                    );

                    echo json_encode([
                        "status" => "success",
                        "message" => "Elemento actualizado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "status" => "error",
                        "message" => "Error al actualizar el elemento"
                    ]);
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                "status" => "error",
                "message" => "Excepción: " . $e->getMessage()
            ]);
        }
        break;

    case "mostrar": 
        header('Content-Type: application/json; charset=utf-8');
        $datos = $elemento->get_elementoxid($_POST["id_elemento"]);
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "status" => "error",
                "message" => "No se pudo obtener el elemento solicitado"
            ]);
        }
        break;

    case "eliminar":
        $resultado = $elemento->desactivar_elementoxid($_POST["id_elemento"]);
        echo json_encode([
            "status" => $resultado ? "success" : "error",
            "message" => $resultado ? "Elemento desactivado correctamente" : "Error al desactivar el elemento"
        ]);
        break;

    case "activar":
        $resultado = $elemento->activar_elementoxid($_POST["id_elemento"]);
        echo json_encode([
            "status" => $resultado ? "success" : "error",
            "message" => $resultado ? "Elemento activado correctamente" : "Error al activar el elemento"
        ]);
        break;

    case "cambiarEstado":
        $id_elemento = $_POST["id_elemento"] ?? '';
        $id_estado_nuevo = $_POST["id_estado_elemento"] ?? '';
        
        if (empty($id_elemento) || empty($id_estado_nuevo)) {
            echo json_encode([
                "status" => "error",
                "message" => "ID del elemento y nuevo estado son obligatorios"
            ]);
            break;
        }
        
        $resultado = $elemento->cambiar_estado_elemento($id_elemento, $id_estado_nuevo);
        echo json_encode([
            "status" => $resultado ? "success" : "error",
            "message" => $resultado ? "Estado del elemento cambiado correctamente" : "Error al cambiar el estado del elemento"
        ]);
        break;

    case "verificarCodigoBarras":
        $codigoBarras = isset($_POST["codigo_barras_elemento"]) ? trim($_POST["codigo_barras_elemento"]) : 
                       (isset($_GET["codigo_barras_elemento"]) ? trim($_GET["codigo_barras_elemento"]) : '');

        $idElemento = isset($_POST["id_elemento"]) ? trim($_POST["id_elemento"]) : 
                     (isset($_GET["id_elemento"]) ? trim($_GET["id_elemento"]) : null);

        writeToLog([
            'action' => 'verificarCodigoBarras',
            'codigo_barras_elemento' => $codigoBarras,
            'id_elemento' => $idElemento,
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST,
            'get_data' => $_GET
        ]);

        if (empty($codigoBarras)) {
            echo json_encode([
                "status" => "error",
                "message" => "El código de barras es obligatorio."
            ]);
            break;
        }
        
        $resultado = $elemento->verificarCodigoBarras($codigoBarras, $idElemento);
        
        if (isset($resultado['error'])) {
            $registro->registrarActividad(
                'admin',
                'elemento.php',
                'Verificar código de barras',
                'Error al verificar código de barras: ' . $resultado['error'],
                'error'
            );
            
            echo json_encode([
                "status" => "error",
                "message" => "Error al verificar el código de barras: " . $resultado['error']
            ]);
            break;
        }
        
        $mensajeActividad = $resultado['existe'] ? 
            'Código de barras duplicado detectado: ' . $codigoBarras : 
            'Código de barras disponible: ' . $codigoBarras;
        
        $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
        
        $registro->registrarActividad(
            'admin',
            'elemento.php',
            'Verificar código de barras',
            $mensajeActividad,
            $tipoActividad
        );
        
        echo json_encode([
            "status" => "success",
            "existe" => $resultado['existe']
        ]);
        break;

    case "verificarNumeroSerie":
        $numeroSerie = isset($_POST["numero_serie_elemento"]) ? trim($_POST["numero_serie_elemento"]) : 
                      (isset($_GET["numero_serie_elemento"]) ? trim($_GET["numero_serie_elemento"]) : '');

        $idElemento = isset($_POST["id_elemento"]) ? trim($_POST["id_elemento"]) : 
                     (isset($_GET["id_elemento"]) ? trim($_GET["id_elemento"]) : null);

        writeToLog([
            'action' => 'verificarNumeroSerie',
            'numero_serie_elemento' => $numeroSerie,
            'id_elemento' => $idElemento,
            'method' => $_SERVER['REQUEST_METHOD'],
            'post_data' => $_POST,
            'get_data' => $_GET
        ]);

        if (empty($numeroSerie)) {
            echo json_encode([
                "status" => "error",
                "message" => "El número de serie es obligatorio."
            ]);
            break;
        }
        
        $resultado = $elemento->verificarNumeroSerie($numeroSerie, $idElemento);
        
        if (isset($resultado['error'])) {
            $registro->registrarActividad(
                'admin',
                'elemento.php',
                'Verificar número de serie',
                'Error al verificar número de serie: ' . $resultado['error'],
                'error'
            );
            
            echo json_encode([
                "status" => "error",
                "message" => "Error al verificar el número de serie: " . $resultado['error']
            ]);
            break;
        }
        
        $mensajeActividad = $resultado['existe'] ? 
            'Número de serie duplicado detectado: ' . $numeroSerie : 
            'Número de serie disponible: ' . $numeroSerie;
        
        $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
        
        $registro->registrarActividad(
            'admin',
            'elemento.php',
            'Verificar número de serie',
            $mensajeActividad,
            $tipoActividad
        );
        
        echo json_encode([
            "status" => "success",
            "existe" => $resultado['existe']
        ]);
        break;

    case 'getWarrantyEvents':
        // Obtener mes y año del calendario
        $month = isset($_POST["month"]) ? intval($_POST["month"]) : date('n');
        $year = isset($_POST["year"]) ? intval($_POST["year"]) : date('Y');
        
        writeToLog([
            'action' => 'getWarrantyEvents',
            'month' => $month,
            'year' => $year
        ]);
        
        try {
            $eventos = $elemento->getWarrantyEvents($month, $year);
            
            writeToLog([
                'action' => 'getWarrantyEvents',
                'result' => 'success',
                'count' => count($eventos)
            ]);
            
            echo json_encode([
                "status" => "success",
                "data" => $eventos
            ]);
            
        } catch (Exception $e) {
            writeToLog([
                'action' => 'getWarrantyEvents',
                'error' => $e->getMessage()
            ]);
            
            echo json_encode([
                "status" => "error",
                "message" => "Error al obtener eventos: " . $e->getMessage()
            ]);
        }
        break;

    case 'getMaintenanceEvents':
        // Obtener mes y año del calendario
        $month = isset($_POST["month"]) ? intval($_POST["month"]) : date('n');
        $year = isset($_POST["year"]) ? intval($_POST["year"]) : date('Y');
        
        writeToLog([
            'action' => 'getMaintenanceEvents',
            'month' => $month,
            'year' => $year
        ]);
        
        try {
            $eventos = $elemento->getMaintenanceEvents($month, $year);
            
            writeToLog([
                'action' => 'getMaintenanceEvents',
                'result' => 'success',
                'count' => count($eventos)
            ]);
            
            echo json_encode([
                "status" => "success",
                "data" => $eventos
            ]);
            
        } catch (Exception $e) {
            writeToLog([
                'action' => 'getMaintenanceEvents',
                'error' => $e->getMessage()
            ]);
            
            echo json_encode([
                "status" => "error",
                "message" => "Error al obtener eventos de mantenimiento: " . $e->getMessage()
            ]);
        }
        break;

    default:
        echo json_encode([
            "status" => "error",
            "message" => "Operación no válida"
        ]);
        break;
}
