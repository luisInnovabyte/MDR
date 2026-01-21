<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Coeficiente.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$coeficiente = new Coeficiente();


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}


switch ($_GET["op"]) {

    case "listar":
        $datos = $coeficiente->get_coeficiente();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_coeficiente" => $row["id_coeficiente"],
                "jornadas_coeficiente" => $row["jornadas_coeficiente"],
                "valor_coeficiente" => $row["valor_coeficiente"],
                "observaciones_coeficiente" => $row["observaciones_coeficiente"],
                "activo_coeficiente" => $row["activo_coeficiente"],
                "created_at_coeficiente" => $row["created_at_coeficiente"],
                "updated_at_coeficiente" => $row["updated_at_coeficiente"]
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
                // Obtener datos del formulario
                $jornadas_coeficiente = $_POST["jornadas_coeficiente"] ?? '';
                $valor_coeficiente = $_POST["valor_coeficiente"] ?? '';
                $observaciones_coeficiente = $_POST["observaciones_coeficiente"] ?? '';
                
                // Debug: Log de los datos recibidos
                file_put_contents(__DIR__ . "/../public/logs/coeficiente_debug_" . date("Ymd") . ".txt", 
                                  "[" . date("Y-m-d H:i:s") . "] Datos recibidos: " . print_r($_POST, true) . "\n", 
                                  FILE_APPEND | LOCK_EX);
                
                // Validar campos obligatorios
                if (empty($jornadas_coeficiente) || empty($valor_coeficiente)) {
                    echo json_encode([
                        "success" => false,
                        "message" => "Los campos jornadas y valor son obligatorios"
                    ]);
                    break;
                }
                
                if (empty($_POST["id_coeficiente"])) {
                    // Verificar si ya existe un coeficiente con las mismas jornadas
                    $verificacion = $coeficiente->verificarJornadasExistentes($jornadas_coeficiente);
                    
                    if ($verificacion["existe"]) {
                        $registro->registrarActividad(
                            'admin',
                            'coeficiente.php',
                            'Error al guardar coeficiente',
                            "Ya existe un coeficiente para " . $jornadas_coeficiente . " jornadas",
                            "warning"
                        );
                        echo json_encode([
                            "success" => false,
                            "message" => "Ya existe un coeficiente para estas jornadas"
                        ]);
                        break;
                    }
                    
                    // Insertar nuevo coeficiente                                      
                    $resultado = $coeficiente->insert_coeficiente(
                        $jornadas_coeficiente,
                        $valor_coeficiente,
                        $observaciones_coeficiente
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'coeficiente.php',
                            'Guardar coeficiente',
                            "Coeficiente guardado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Coeficiente insertado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar el coeficiente"
                        ]);
                    }
        
                } else {
                    // Verificar si ya existe un coeficiente con las mismas jornadas (excluyendo el actual)
                    $verificacion = $coeficiente->verificarJornadasExistentes($jornadas_coeficiente, $_POST["id_coeficiente"]);
                    
                    if ($verificacion["existe"]) {
                        $registro->registrarActividad(
                            'admin',
                            'coeficiente.php',
                            'Error al actualizar coeficiente',
                            "Ya existe un coeficiente para " . $jornadas_coeficiente . " jornadas",
                            "warning"
                        );
                        echo json_encode([
                            "success" => false,
                            "message" => "Ya existe un coeficiente para estas jornadas"
                        ]);
                        break;
                    }
                    
                    // Actualizar coeficiente existente
                    $resultado = $coeficiente->update_coeficiente(
                        $_POST["id_coeficiente"],
                        $jornadas_coeficiente,
                        $valor_coeficiente,
                        $observaciones_coeficiente
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'coeficiente.php',
                            'Actualizar coeficiente',
                            "Coeficiente actualizado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Coeficiente actualizado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar el coeficiente"
                        ]);
                    }
                }
            } catch (Exception $e) {
                echo json_encode([
                    "success" => false,
                    "message" => "Excepción: " . $e->getMessage()
                ]);
            }
            break;

    case "mostrar":
        $datos = $coeficiente->get_coeficientexid($_POST["id_coeficiente"]);

        $registro->registrarActividad(
            'admin',
            'coeficiente.php',
            'Obtener coeficiente seleccionado',
            "Coeficiente obtenido exitosamente",
            "info"
        );

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $coeficiente->delete_coeficientexid($_POST["id_coeficiente"]);

        $registro->registrarActividad(
            'admin',
            'coeficiente.php',
            'Desactivar coeficiente seleccionado',
            "Coeficiente desactivado exitosamente",
            "info"
        );

        break;

    case "activar":
        $coeficiente->activar_coeficientexid($_POST["id_coeficiente"]);

        $registro->registrarActividad(
            'admin',
            'coeficiente.php',
            'Activar coeficiente seleccionado',
            "Coeficiente activado exitosamente",
            "info"
        );

        break;

    case "verificarJornadas":
        // Verificar si ya existen las jornadas especificadas
        $id_coeficiente = $_POST["id_coeficiente"] ?? null;
        $jornadas = $_POST["jornadas_coeficiente"];
        
        $resultado = $coeficiente->verificarJornadasExistentes($jornadas, $id_coeficiente);
        
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;

    // =========================================================
    // CASE: obtener_por_jornadas
    // Obtiene el coeficiente correspondiente según número de jornadas
    // =========================================================
    case "obtener_por_jornadas":
        $jornadas = $_POST["jornadas"] ?? null;
        
        if (!$jornadas || $jornadas < 1) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Número de jornadas no válido'
            ], JSON_UNESCAPED_UNICODE);
            break;
        }
        
        // Buscar coeficiente más cercano (por defecto buscar el mayor que no supere las jornadas)
        $sql = "SELECT 
                    id_coeficiente,
                    jornadas_coeficiente,
                    valor_coeficiente
                FROM coeficiente
                WHERE jornadas_coeficiente <= ?
                AND activo_coeficiente = 1
                ORDER BY jornadas_coeficiente DESC
                LIMIT 1";
        
        try {
            $conexion = (new Conexion())->getConexion();
            $stmt = $conexion->prepare($sql);
            $stmt->bindValue(1, $jornadas, PDO::PARAM_INT);
            $stmt->execute();
            $datos = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($datos) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => true,
                    'data' => [
                        'id_coeficiente' => $datos['id_coeficiente'],
                        'jornadas_desde_coeficiente' => $datos['jornadas_coeficiente'],
                        'jornadas_hasta_coeficiente' => $datos['jornadas_coeficiente'],
                        'factor_coeficiente' => $datos['valor_coeficiente']
                    ]
                ], JSON_UNESCAPED_UNICODE);
            } else {
                // Si no hay coeficiente, devolver error
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => 'No se encontró coeficiente para ' . $jornadas . ' jornadas'
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } catch (PDOException $e) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener coeficiente: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

}