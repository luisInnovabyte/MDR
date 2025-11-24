<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Estado_presupuesto.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Log básico para verificar que el archivo se ejecuta
file_put_contents(__DIR__ . "/../public/logs/estado_presupuesto_access_" . date("Ymd") . ".txt", 
                  "[" . date("Y-m-d H:i:s") . "] estado_presupuesto.php ejecutado - IP: " . ($_SERVER['REMOTE_ADDR'] ?? 'CLI') . "\n", 
                  FILE_APPEND | LOCK_EX);


//  id_estado_ppto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_estado_ppto VARCHAR(20) NOT NULL UNIQUE,
//     nombre_estado_ppto VARCHAR(100) NOT NULL,
//     color_estado_ppto VARCHAR(7),
//     orden_estado_ppto INT DEFAULT 0,
//     observaciones_estado_ppto TEXT,
//     activo_estado_ppto BOOLEAN DEFAULT TRUE,
//     created_at_estado_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_estado_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;




$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$estado_presupuesto = new Estado_presupuesto();

switch ($_GET["op"]) {

    case "listar":
        $datos = $estado_presupuesto->get_estado_presupuesto();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_estado_ppto" => $row["id_estado_ppto"],
                "codigo_estado_ppto" => $row["codigo_estado_ppto"],
                "nombre_estado_ppto" => $row["nombre_estado_ppto"],
                "color_estado_ppto" => $row["color_estado_ppto"],
                "orden_estado_ppto" => $row["orden_estado_ppto"],
                "observaciones_estado_ppto" => $row["observaciones_estado_ppto"],
                "activo_estado_ppto" => $row["activo_estado_ppto"],
                "created_at_estado_ppto" => $row["created_at_estado_ppto"],
                "updated_at_estado_ppto" => $row["updated_at_estado_ppto"]
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
            $datos = $estado_presupuesto->get_estado_presupuesto_disponible();
            $data = array();
            foreach ($datos as $row) {
            $data[] = array(
                "id_estado_ppto" => $row["id_estado_ppto"],
                "codigo_estado_ppto" => $row["codigo_estado_ppto"],
                "nombre_estado_ppto" => $row["nombre_estado_ppto"],
                "color_estado_ppto" => $row["color_estado_ppto"],
                "orden_estado_ppto" => $row["orden_estado_ppto"],
                "observaciones_estado_ppto" => $row["observaciones_estado_ppto"],
                "activo_estado_ppto" => $row["activo_estado_ppto"],
                "created_at_estado_ppto" => $row["created_at_estado_ppto"],
                "updated_at_estado_ppto" => $row["updated_at_estado_ppto"]
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
                $codigo_estado_ppto = $_POST["codigo_estado_ppto"] ?? '';
                $nombre_estado_ppto = $_POST["nombre_estado_ppto"] ?? '';
                $color_estado_ppto = $_POST["color_estado_ppto"] ?? '';
                $orden_estado_ppto = isset($_POST["orden_estado_ppto"]) ? (int)$_POST["orden_estado_ppto"] : 0;
                $observaciones_estado_ppto = $_POST["observaciones_estado_ppto"] ?? '';
                
                // Debug: Log de los datos recibidos
                file_put_contents(__DIR__ . "/../public/logs/estado_presupuesto_debug_" . date("Ymd") . ".txt", 
                                  "[" . date("Y-m-d H:i:s") . "] Datos recibidos: " . print_r($_POST, true) . "\n", 
                                  FILE_APPEND | LOCK_EX);
                
                // Validar campos obligatorios
                if (empty($codigo_estado_ppto) || empty($nombre_estado_ppto)) {
                    echo json_encode([
                        "success" => false,
                        "message" => "Los campos código y nombre son obligatorios"
                    ]);
                    break;
                }
                
                if (empty($_POST["id_estado_ppto"])) {
                    // Insertar nuevo estado de presupuesto                                      
                    $resultado = $estado_presupuesto->insert_estado_presupuesto(
                        $codigo_estado_ppto,
                        $nombre_estado_ppto,
                        $color_estado_ppto,
                        $orden_estado_ppto,
                        $observaciones_estado_ppto
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'estado_presupuesto.php',
                            'Guardar el estado de presupuesto',
                            "Estado de presupuesto guardado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Estado de presupuesto insertado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar el estado de presupuesto"
                        ]);
                    }
        
                } else {
                    // Actualizar estado de presupuesto existente
                    $resultado = $estado_presupuesto->update_estado_presupuesto(
                        $_POST["id_estado_ppto"],
                        $codigo_estado_ppto,
                        $nombre_estado_ppto,
                        $color_estado_ppto,
                        $orden_estado_ppto,
                        $observaciones_estado_ppto
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'estado_presupuesto.php',
                            'Actualizar el estado de presupuesto',
                            "Estado de presupuesto actualizado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Estado de presupuesto actualizado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar el estado de presupuesto"
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
            // Encabezado para indicar que se devuelve JSON
            header('Content-Type: application/json; charset=utf-8');
            // Obtenemos el estado de presupuesto por ID
            $datos = $estado_presupuesto->get_estado_presupuestoxid($_POST["id_estado_ppto"]);
            // Si hay datos, los devolvemos; si no, mandamos un JSON de error
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener el estado de presupuesto solicitado"
                ]);
            }
        
             break;
            

    case "eliminar":
        $estado_presupuesto->delete_estado_presupuestoxid($_POST["id_estado_ppto"]);
        break;

    case "activar":
        $estado_presupuesto->activar_estado_presupuestoxid($_POST["id_estado_ppto"]);
        break;

    case "verificarEstadoPresupuesto":
            // Obtener el código y nombre del estado de presupuesto, y el ID (si se está editando)
            $codigoEstado = isset($_POST["codigo_estado_ppto"]) ? trim($_POST["codigo_estado_ppto"]) : '';
            $nombreEstado = isset($_POST["nombre_estado_ppto"]) ? trim($_POST["nombre_estado_ppto"]) : '';
            $idEstado = isset($_POST["id_estado_ppto"]) ? trim($_POST["id_estado_ppto"]) : null;

            // Validar que el código del estado de presupuesto no esté vacío
            if (empty($codigoEstado) && empty($nombreEstado)) {
                $registro->registrarActividad(
                    'admin',
                    'estado_presupuesto.php',
                    'Verificar estado de presupuesto',
                    'Intento de verificación con código y nombre vacíos',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El código o nombre del estado de presupuesto no pueden estar vacíos."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $estado_presupuesto->verificarEstadoPresupuesto($codigoEstado, $nombreEstado, $idEstado);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'estado_presupuesto.php',
                    'Verificar estado de presupuesto',
                    'Error al verificar estado de presupuesto: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar el estado de presupuesto: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Estado de presupuesto duplicado detectado: ' . $nombreEstado : 
                'Estado de presupuesto disponible: ' . $nombreEstado;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'estado_presupuesto.php',
                'Verificar estado de presupuesto',
                $mensajeActividad,
                $tipoActividad
            );
            
            // Devolver el resultado como JSON
            echo json_encode([
                "success" => true,
                "existe" => $resultado['existe']  // Accedemos a la clave 'existe' del array
            ]);
            break;
            
}
?>