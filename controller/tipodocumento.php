<?php
require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/Tipodocumento.php";

$registro = new RegistroActividad();
$tipoDocumento = new TipoDocumento();

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt";
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

switch ($_GET["op"]) {

    case "listar":
        $datos = $tipoDocumento->get_tipos_documento();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_tipo_documento" => $row["id_tipo_documento"],
                "codigo_tipo_documento" => $row["codigo_tipo_documento"],
                "nombre_tipo_documento" => $row["nombre_tipo_documento"],
                "descripcion_tipo_documento" => $row["descripcion_tipo_documento"] ?? '',
                "activo_tipo_documento" => (bool)$row["activo_tipo_documento"],
                "created_at_tipo_documento" => $row["created_at_tipo_documento"],
                "updated_at_tipo_documento" => $row["updated_at_tipo_documento"]
            );
        }

        $registro->registrarActividad(
            'admin',
            'tipodocumento.php',
            'Listar tipos de documento',
            "Tipos de documento listados correctamente",
            "info"
        );

        $resultados = array(
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        header('Content-Type: application/json');
        echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
        break;

    case "listar_disponibles":
        $datos = $tipoDocumento->get_tipos_documento_disponibles();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_tipo_documento" => $row["id_tipo_documento"],
                "codigo_tipo_documento" => $row["codigo_tipo_documento"],
                "nombre_tipo_documento" => $row["nombre_tipo_documento"],
                "descripcion_tipo_documento" => $row["descripcion_tipo_documento"] ?? '',
                "activo_tipo_documento" => (bool)$row["activo_tipo_documento"]
            );
        }

        $registro->registrarActividad(
            'admin',
            'tipodocumento.php',
            'Listar tipos de documento disponibles',
            "Tipos de documento disponibles listados correctamente",
            "info"
        );

        $resultados = array(
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        header('Content-Type: application/json');
        echo json_encode($resultados, JSON_UNESCAPED_UNICODE);
        break;

    case "guardaryeditar":
        try {
            // Validar campos obligatorios
            if (empty($_POST["codigo_tipo_documento"]) || empty($_POST["nombre_tipo_documento"])) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'El código y nombre son obligatorios'
                ], JSON_UNESCAPED_UNICODE);
                break;
            }

            // Preparar campo opcional
            $descripcion = null;
            if (isset($_POST["descripcion_tipo_documento"]) && 
                $_POST["descripcion_tipo_documento"] !== '' && 
                $_POST["descripcion_tipo_documento"] !== 'null') {
                $descripcion = $_POST["descripcion_tipo_documento"];
            }

            // Verificar si es INSERT o UPDATE
            if (empty($_POST["id_tipo_documento"])) {
                // INSERT - Verificar que no exista el código
                $verificacion = $tipoDocumento->verificarTipoDocumento($_POST["codigo_tipo_documento"]);
                
                if ($verificacion['existe']) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Ya existe un tipo de documento con ese código'
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                }

                $resultado = $tipoDocumento->insert_tipo_documento(
                    $_POST["codigo_tipo_documento"],
                    $_POST["nombre_tipo_documento"],
                    $descripcion
                );

                if ($resultado) {
                    $registro->registrarActividad(
                        'admin',
                        'tipodocumento.php',
                        'Guardar tipo de documento',
                        "Tipo de documento guardado exitosamente con ID: $resultado",
                        "success"
                    );

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tipo de documento registrado correctamente',
                        'id_tipo_documento' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al guardar el tipo de documento'
                    ], JSON_UNESCAPED_UNICODE);
                }

            } else {
                // UPDATE - Verificar que el código no esté duplicado (excluyendo el actual)
                $verificacion = $tipoDocumento->verificarTipoDocumento(
                    $_POST["codigo_tipo_documento"],
                    $_POST["id_tipo_documento"]
                );
                
                if ($verificacion['existe']) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Ya existe otro tipo de documento con ese código'
                    ], JSON_UNESCAPED_UNICODE);
                    break;
                }

                $resultado = $tipoDocumento->update_tipo_documento(
                    $_POST["id_tipo_documento"],
                    $_POST["codigo_tipo_documento"],
                    $_POST["nombre_tipo_documento"],
                    $descripcion
                );

                if ($resultado !== false) {
                    $registro->registrarActividad(
                        'admin',
                        'tipodocumento.php',
                        'Actualizar tipo de documento',
                        "Tipo de documento actualizado ID: {$_POST["id_tipo_documento"]}",
                        "success"
                    );

                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => true,
                        'message' => 'Tipo de documento actualizado correctamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar el tipo de documento'
                    ], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error en la operación: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "mostrar":
        $datos = $tipoDocumento->get_tipo_documentoxid($_POST["id_tipo_documento"]);

        $registro->registrarActividad(
            'admin',
            'tipodocumento.php',
            'Obtener tipo de documento seleccionado',
            "Tipo de documento obtenido exitosamente",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        try {
            $resultado = $tipoDocumento->delete_tipo_documentoxid($_POST["id_tipo_documento"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'tipodocumento.php',
                    'Eliminar tipo de documento seleccionado',
                    "Tipo de documento desactivado exitosamente ID: {$_POST["id_tipo_documento"]}",
                    "success"
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Tipo de documento desactivado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo desactivar el tipo de documento'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "activar":
        try {
            $resultado = $tipoDocumento->activar_tipo_documentoxid($_POST["id_tipo_documento"]);

            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'tipodocumento.php',
                    'Activar tipo de documento seleccionado',
                    "Tipo de documento activado exitosamente ID: {$_POST["id_tipo_documento"]}",
                    "success"
                );

                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'message' => 'Tipo de documento activado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se pudo activar el tipo de documento'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "verificarTipodocumento":
        $codigo = $_GET["codigo_tipo_documento"] ?? '';
        $id_tipo_documento = $_GET["id_tipo_documento"] ?? null;
        
        if (empty($codigo)) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Código no proporcionado'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }
        
        $resultado = $tipoDocumento->verificarTipoDocumento($codigo, $id_tipo_documento);

        // Asegurar que siempre haya un campo 'success'
        if (!isset($resultado['success'])) {
            $resultado['success'] = !isset($resultado['error']);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    case "verificar":
        $resultado = $tipoDocumento->verificarTipoDocumento(
            $_POST["codigo_tipo_documento"],
            $_POST["id_tipo_documento"] ?? null
        );

        // Asegurar que siempre haya un campo 'success'
        if (!isset($resultado['success'])) {
            $resultado['success'] = !isset($resultado['error']);
        }

        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
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