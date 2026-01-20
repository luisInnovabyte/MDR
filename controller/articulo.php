<?php
require_once "../config/conexion.php";
require_once "../models/Articulo.php";
require_once '../config/funciones.php';

// Función para procesar imagen de artículo
function procesarImagenArticulo($archivo, &$errorMsg = null)
{
    try {
        // Verificar si hay errores en la subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $errores = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo permitido por PHP',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño máximo del formulario',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
                UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal',
                UPLOAD_ERR_CANT_WRITE => 'Error al escribir el archivo en disco',
                UPLOAD_ERR_EXTENSION => 'Una extensión de PHP detuvo la subida'
            ];
            $errorMsg = $errores[$archivo['error']] ?? 'Error desconocido en la subida';
            return false;
        }
        
        // Verificar que el archivo temporal existe
        if (!file_exists($archivo['tmp_name'])) {
            $errorMsg = "El archivo temporal no existe";
            return false;
        }
        
        // Directorio de destino - usar ruta absoluta
        $directorio = __DIR__ . "/../public/img/articulo/";
        
        // Verificar que el directorio existe
        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0777, true)) {
                $errorMsg = "No se pudo crear el directorio de destino: " . $directorio;
                return false;
            }
        }
        
        // Verificar permisos de escritura
        if (!is_writable($directorio)) {
            $errorMsg = "El directorio no tiene permisos de escritura: " . $directorio;
            return false;
        }
        
        // Validar extensión del archivo
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $extensionesPermitidas)) {
            $errorMsg = "Extensión no permitida: .$extension (permitidas: " . implode(', ', $extensionesPermitidas) . ")";
            return false;
        }
        
        // Validar tamaño (5MB máximo)
        $tamañoArchivo = $archivo['size'];
        if ($tamañoArchivo > 5 * 1024 * 1024) {
            $tamañoMB = round($tamañoArchivo / (1024 * 1024), 2);
            $errorMsg = "El archivo es muy grande: {$tamañoMB}MB (máximo: 5MB)";
            return false;
        }
        
        // Validar tipo MIME (más flexible)
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png', 'image/gif', 'image/webp'];
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoReal = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($tipoReal, $tiposPermitidos)) {
            // Si la extensión es válida, continuar de todas formas (algunos servidores reportan MIME incorrectos)
            if (!in_array($extension, $extensionesPermitidas)) {
                $errorMsg = "Tipo de archivo no permitido: $tipoReal";
                return false;
            }
        }
        
        // Generar nombre único para el archivo
        $nombreArchivo = 'articulo_' . uniqid() . '.' . $extension;
        $rutaCompleta = $directorio . $nombreArchivo;
        
        // Mover el archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            return $nombreArchivo; // Retornar solo el nombre del archivo
        } else {
            $errorMsg = "No se pudo mover el archivo al directorio de destino. Verifique permisos.";
            return false;
        }
        
    } catch (Exception $e) {
        $errorMsg = "Excepción: " . $e->getMessage();
        return false;
    }
}

$registro = new RegistroActividad();
$articulo = new Articulo();

switch ($_GET["op"]) {

    case "estadisticas":
        try {
            $totalArticulos = $articulo->total_articulo();
            $totalActivos = $articulo->total_articulo_activo();
            $totalKits = $articulo->total_articulo_activo_kit();
            $totalCoeficientes = $articulo->total_articulo_activo_coeficiente();

            echo json_encode([
                "success" => true,
                "data" => [
                    "total" => $totalArticulos ?? 0,
                    "activos" => $totalActivos ?? 0,
                    "kits" => $totalKits ?? 0,
                    "coeficientes" => $totalCoeficientes ?? 0
                ]
            ]);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error al obtener estadísticas: " . $e->getMessage()
            ]);
        }
        break;

    case "listar":
        $datos = $articulo->get_articulo_con_elementos();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_articulo" => $row["id_articulo"],
                "id_familia" => $row["id_familia"],
                "codigo_familia" => $row["codigo_familia"] ?? '',
                "nombre_familia" => $row["nombre_familia"] ?? '',
                "name_familia" => $row["name_familia"] ?? '',
                "id_grupo" => $row["id_grupo"] ?? null,
                "codigo_grupo" => $row["codigo_grupo"] ?? '',
                "nombre_grupo" => $row["nombre_grupo"] ?? '',
                "id_unidad" => $row["id_unidad"] ?? null,
                "nombre_unidad" => $row["nombre_unidad"] ?? '',
                "simbolo_unidad" => $row["simbolo_unidad"] ?? '',
                "codigo_articulo" => $row["codigo_articulo"],
                "nombre_articulo" => $row["nombre_articulo"],
                "name_articulo" => $row["name_articulo"],
                "imagen_articulo" => $row["imagen_articulo"] ?? '',
                "imagen_efectiva" => $row["imagen_efectiva"] ?? '',
                "tipo_imagen" => $row["tipo_imagen"] ?? '',
                "precio_alquiler_articulo" => $row["precio_alquiler_articulo"],
                "coeficiente_articulo" => $row["coeficiente_articulo"] ?? null,
                "coeficiente_efectivo" => $row["coeficiente_efectivo"] ?? 0,
                "coeficiente_familia" => $row["coeficiente_familia"] ?? 0,
                "es_kit_articulo" => $row["es_kit_articulo"],
                "control_total_articulo" => $row["control_total_articulo"],
                "no_facturar_articulo" => $row["no_facturar_articulo"],
                "notas_presupuesto_articulo" => $row["notas_presupuesto_articulo"] ?? '',
                "notes_budget_articulo" => $row["notes_budget_articulo"] ?? '',
                "orden_obs_articulo" => $row["orden_obs_articulo"] ?? 200,
                "observaciones_articulo" => $row["observaciones_articulo"] ?? '',
                "jerarquia_completa" => $row["jerarquia_completa"] ?? '',
                "configuracion_completa" => $row["configuracion_completa"] ?? 0,
                "activo_articulo" => $row["activo_articulo"],
                "created_at_articulo" => $row["created_at_articulo"],
                "updated_at_articulo" => $row["updated_at_articulo"],
                "total_elementos" => $row["total_elementos"] ?? 0
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
        $datos = $articulo->get_articulo_disponible();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_articulo" => $row["id_articulo"],
                "id_familia" => $row["id_familia"],
                "codigo_familia" => $row["codigo_familia"] ?? '',
                "nombre_familia" => $row["nombre_familia"] ?? '',
                "name_familia" => $row["name_familia"] ?? '',
                "id_grupo" => $row["id_grupo"] ?? null,
                "codigo_grupo" => $row["codigo_grupo"] ?? '',
                "nombre_grupo" => $row["nombre_grupo"] ?? '',
                "id_unidad" => $row["id_unidad"] ?? null,
                "nombre_unidad" => $row["nombre_unidad"] ?? '',
                "simbolo_unidad" => $row["simbolo_unidad"] ?? '',
                "codigo_articulo" => $row["codigo_articulo"],
                "nombre_articulo" => $row["nombre_articulo"],
                "name_articulo" => $row["name_articulo"],
                "imagen_articulo" => $row["imagen_articulo"] ?? '',
                "imagen_efectiva" => $row["imagen_efectiva"] ?? '',
                "tipo_imagen" => $row["tipo_imagen"] ?? '',
                "precio_alquiler_articulo" => $row["precio_alquiler_articulo"],
                "coeficiente_articulo" => $row["coeficiente_articulo"] ?? null,
                "coeficiente_efectivo" => $row["coeficiente_efectivo"] ?? 0,
                "es_kit_articulo" => $row["es_kit_articulo"],
                "control_total_articulo" => $row["control_total_articulo"],
                "no_facturar_articulo" => $row["no_facturar_articulo"],
                "notas_presupuesto_articulo" => $row["notas_presupuesto_articulo"] ?? '',
                "notes_budget_articulo" => $row["notes_budget_articulo"] ?? '',
                "orden_obs_articulo" => $row["orden_obs_articulo"] ?? 200,
                "observaciones_articulo" => $row["observaciones_articulo"] ?? '',
                "jerarquia_completa" => $row["jerarquia_completa"] ?? '',
                "configuracion_completa" => $row["configuracion_completa"] ?? 0,
                "activo_articulo" => $row["activo_articulo"],
                "created_at_articulo" => $row["created_at_articulo"],
                "updated_at_articulo" => $row["updated_at_articulo"]
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
            // Validación de datos requeridos
            if (empty($_POST["id_familia"]) || empty($_POST["codigo_articulo"]) || 
                empty($_POST["nombre_articulo"]) || empty($_POST["name_articulo"])) {
                echo json_encode([
                    "success" => false,
                    "message" => "Faltan datos obligatorios"
                ]);
                break;
            }

            // Procesar campos opcionales con valores por defecto
            $id_unidad = !empty($_POST["id_unidad"]) ? (int)$_POST["id_unidad"] : null;
            $precio_alquiler = !empty($_POST["precio_alquiler_articulo"]) ? 
                (float)$_POST["precio_alquiler_articulo"] : 0.00;
            
            // Coeficiente: puede ser null, 0 o 1
            $coeficiente = null;
            if (isset($_POST["coeficiente_articulo"]) && $_POST["coeficiente_articulo"] !== '') {
                $coeficiente = (int)$_POST["coeficiente_articulo"];
            }
            
            $es_kit = !empty($_POST["es_kit_articulo"]) ? 1 : 0;
            $control_total = !empty($_POST["control_total_articulo"]) ? 1 : 0;
            $no_facturar = !empty($_POST["no_facturar_articulo"]) ? 1 : 0;
            $orden_obs = !empty($_POST["orden_obs_articulo"]) ? 
                (int)$_POST["orden_obs_articulo"] : 200;

            // Procesar imagen usando la función procesarImagenArticulo
            $imagen = '';
            
            // Si hay un archivo nuevo subido
            if (isset($_FILES["imagen_articulo"]) && $_FILES["imagen_articulo"]["error"] == UPLOAD_ERR_OK) {
                // Usar la función procesarImagenArticulo que ya está definida arriba
                $errorMsgImagen = null;
                $resultadoImagen = procesarImagenArticulo($_FILES["imagen_articulo"], $errorMsgImagen);
                
                if ($resultadoImagen === false) {
                    echo json_encode([
                        "success" => false,
                        "message" => "Error al procesar la imagen: " . ($errorMsgImagen ?? "Error desconocido")
                    ]);
                    break;
                }
                
                $imagen = $resultadoImagen;
                
                // Si estamos editando y había una imagen anterior, eliminarla
                if (!empty($_POST["id_articulo"]) && !empty($_POST["imagen_actual"])) {
                    $ruta_imagen_anterior = __DIR__ . "/../public/img/articulo/" . $_POST["imagen_actual"];
                    if (file_exists($ruta_imagen_anterior)) {
                        unlink($ruta_imagen_anterior);
                    }
                }
            } else if (!empty($_POST["imagen_actual"])) {
                // Si no hay archivo nuevo pero existe una imagen actual (modo edición), conservarla
                $imagen = $_POST["imagen_actual"];
            }

            // Determinar si es inserción o actualización
            if (empty($_POST["id_articulo"])) {
                // INSERCIÓN
                $resultado = $articulo->insert_articulo(
                    (int)$_POST["id_familia"],
                    $id_unidad,
                    trim($_POST["codigo_articulo"]),
                    trim($_POST["nombre_articulo"]),
                    trim($_POST["name_articulo"]),
                    $imagen,
                    $precio_alquiler,
                    $coeficiente,
                    $es_kit,
                    $control_total,
                    $no_facturar,
                    trim($_POST["notas_presupuesto_articulo"] ?? ''),
                    trim($_POST["notes_budget_articulo"] ?? ''),
                    $orden_obs,
                    trim($_POST["observaciones_articulo"] ?? '')
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'articulo.php',
                        'Insertar artículo',
                        "Artículo creado: " . $_POST["nombre_articulo"],
                        "success"
                    );
                    
                    echo json_encode([
                        "success" => true,
                        "message" => "Artículo registrado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "No se pudo registrar el artículo"
                    ]);
                }
            } else {
                // ACTUALIZACIÓN
                $resultado = $articulo->update_articulo(
                    (int)$_POST["id_articulo"],
                    (int)$_POST["id_familia"],
                    $id_unidad,
                    trim($_POST["codigo_articulo"]),
                    trim($_POST["nombre_articulo"]),
                    trim($_POST["name_articulo"]),
                    $imagen,
                    $precio_alquiler,
                    $coeficiente,
                    $es_kit,
                    $control_total,
                    $no_facturar,
                    trim($_POST["notas_presupuesto_articulo"] ?? ''),
                    trim($_POST["notes_budget_articulo"] ?? ''),
                    $orden_obs,
                    trim($_POST["observaciones_articulo"] ?? '')
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'articulo.php',
                        'Actualizar artículo',
                        "Artículo actualizado: " . $_POST["nombre_articulo"] . " (ID: " . $_POST["id_articulo"] . ")",
                        "info"
                    );
                    
                    echo json_encode([
                        "success" => true,
                        "message" => "Artículo actualizado correctamente"
                    ]);
                } else {
                    echo json_encode([
                        "success" => false,
                        "message" => "No se pudo actualizar el artículo"
                    ]);
                }
            }

        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error al procesar: " . $e->getMessage()
            ]);
        }
        break;

    case "mostrar": 
        // Encabezado para indicar que se devuelve JSON
        header('Content-Type: application/json; charset=utf-8');
        // Obtenemos el artículo por ID
        $datos = $articulo->get_articuloxid($_POST["id_articulo"]);
        // Si hay datos, los devolvemos; si no, mandamos un JSON de error
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener el artículo solicitado"
            ]);
        }
        break;

    case "eliminar":
        $articulo->delete_articuloxid($_POST["id_articulo"]);
        
        $registro->registrarActividad(
            'admin',
            'articulo.php',
            'Eliminar artículo',
            "Artículo desactivado con ID: " . $_POST["id_articulo"],
            "warning"
        );
        
        echo json_encode([
            "success" => true,
            "message" => "Artículo desactivado correctamente"
        ]);
        break;

    case "activar":
        $articulo->activar_articuloxid($_POST["id_articulo"]);
        
        $registro->registrarActividad(
            'admin',
            'articulo.php',
            'Activar artículo',
            "Artículo activado con ID: " . $_POST["id_articulo"],
            "info"
        );
        
        echo json_encode([
            "success" => true,
            "message" => "Artículo activado correctamente"
        ]);
        break;

    case "verificarArticulo":
        // Obtener los datos del artículo
        $nombreArticulo = isset($_GET["nombre_articulo"]) ? trim($_GET["nombre_articulo"]) : '';
        $nameArticulo = isset($_GET["name_articulo"]) ? trim($_GET["name_articulo"]) : '';
        $codigoArticulo = isset($_GET["codigo_articulo"]) ? trim($_GET["codigo_articulo"]) : '';
        $idArticulo = isset($_GET["id_articulo"]) ? trim($_GET["id_articulo"]) : null;

        // Validar que al menos uno de los campos tenga valor
        if (empty($nombreArticulo) && empty($codigoArticulo) && empty($nameArticulo)) {
            $registro->registrarActividad(
                'admin',
                'articulo.php',
                'Verificar artículo',
                'Intento de verificación sin datos',
                'warning'
            );
        
            echo json_encode([
                "success" => false,
                "message" => "Debe proporcionar al menos un campo para verificar."
            ]);
            break;
        }
        
        // Llamar al método del modelo
        $resultado = $articulo->verificarArticulo($nombreArticulo, $codigoArticulo, $idArticulo, $nameArticulo);
        
        // Verificar si hubo error en la consulta
        if (isset($resultado['error'])) {
            $registro->registrarActividad(
                'admin',
                'articulo.php',
                'Verificar artículo',
                'Error al verificar artículo: ' . $resultado['error'],
                'error'
            );
            
            echo json_encode([
                "success" => false,
                "message" => "Error al verificar el artículo: " . $resultado['error']
            ]);
            break;
        }
        
        // Determinar mensaje para el registro de actividad
        $mensajeActividad = $resultado['existe'] ? 
            'Artículo duplicado detectado: ' . $nombreArticulo : 
            'Artículo disponible: ' . $nombreArticulo;
        
        $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
        
        // Registro de actividad
        $registro->registrarActividad(
            'admin',
            'articulo.php',
            'Verificar artículo',
            $mensajeActividad,
            $tipoActividad
        );
        
        // Devolver el resultado como JSON
        echo json_encode([
            "success" => true,
            "existe" => $resultado['existe']
        ]);
        break;

    // =========================================================
    // CASE: listar_para_presupuesto
    // Lista artículos disponibles para añadir a presupuestos
    // Incluye artículos y KITs (para mostrar) pero solo artículos son seleccionables
    // =========================================================
    case "listar_para_presupuesto":
        $datos = $articulo->get_articulo_disponible();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_articulo" => $row["id_articulo"],
                "codigo_articulo" => $row["codigo_articulo"],
                "nombre_articulo" => $row["nombre_articulo"],
                "descripcion_articulo" => $row["descripcion_articulo"] ?? '',
                "precio_alquiler_articulo" => $row["precio_alquiler_articulo"] ?? 0.00,
                "porcentaje_iva" => $row["porcentaje_iva"] ?? 21.00,
                "es_kit" => $row["es_kit"] ?? 0,
                "activo_articulo" => $row["activo_articulo"]
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

    // =========================================================
    // CASE: obtener_componentes_kit
    // Obtiene los componentes de un KIT
    // =========================================================
    case "obtener_componentes_kit":
        $id_articulo = $_POST["id_articulo"] ?? null;
        
        if (!$id_articulo) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'ID de artículo no proporcionado',
                'data' => []
            ], JSON_UNESCAPED_UNICODE);
            break;
        }
        
        // Obtener componentes del KIT
        $sql = "SELECT 
                    k.id_kit,
                    k.cantidad_kit,
                    a.id_articulo,
                    a.codigo_articulo,
                    a.nombre_articulo,
                    a.precio_alquiler_articulo
                FROM kit k
                INNER JOIN articulo a ON k.id_articulo_componente = a.id_articulo
                WHERE k.id_articulo_maestro = ?
                AND k.activo_kit = 1
                AND a.activo_articulo = 1
                ORDER BY a.nombre_articulo ASC";
        
        try {
            $conexion = (new Conexion())->getConexion();
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
            $stmt->execute();
            $componentes = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            header('Content-Type: application/json');
            echo json_encode([
                'success' => true,
                'data' => $componentes
            ], JSON_UNESCAPED_UNICODE);
            
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener componentes: ' . $e->getMessage(),
                'data' => []
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

}
