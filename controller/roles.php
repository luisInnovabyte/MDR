<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Roles.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$rol = new Roles();

switch ($_GET["op"]) {

    case "listar":
        $datos = $rol->get_rol();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_rol" => $row["id_rol"],
                "nombre_rol" => $row["nombre_rol"],
                "est" => $row["est"]
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
            $datos = $rol->get_rol_disponible();
            $data = array();
            foreach ($datos as $row) {
                $data[] = array(
                    "id_rol" => $row["id_rol"],
                    "nombre_rol" => $row["nombre_rol"],
                    "est" => $row["est"]
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
                if (empty($_POST["id_rol"])) {
                    $resultado = $rol->insert_rol($_POST["nombre_rol"]);
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'roles.php',
                            'Guardar el rol',
                            "Rol guardado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Rol insertado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar el rol"
                        ]);
                    }
        
                } else {
                    $resultado = $rol->update_rol(
                        $_POST["id_rol"],
                        $_POST["nombre_rol"]
                    );
        
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'roles.php',
                            'Actualizar el rol',
                            "Rol actualizado exitosamente",
                            "info"
                        );
        
                        echo json_encode([
                            "success" => true,
                            "message" => "Rol actualizado correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar el rol"
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
            // Obtenemos el rol por ID
            $datos = $rol->get_rolxid($_POST["id_rol"]);
            // Si hay datos, los devolvemos; si no, mandamos un JSON de error
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener el rol solicitado"
                ]);
            }
        
             break;
            

    case "eliminar":
        $rol->delete_rolxid($_POST["id_rol"]);
        break;

    case "activar":
        $rol->activar_rolxid($_POST["id_rol"]);
        break;

        case "verificarRol":
            // Obtener el nombre del rol y el ID (si se está editando)
            $nombreRol = isset($_GET["nombre_rol"]) ? trim($_GET["nombre_rol"]) : '';
            $idRol = isset($_GET["id_rol"]) ? trim($_GET["id_rol"]) : null;
            
            // Validar que el nombre del rol no esté vacío
            if (empty($nombreRol)) {
                $registro->registrarActividad(
                    'admin',
                    'roles.php',
                    'Verificar rol',
                    'Intento de verificación con nombre vacío',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El nombre del rol no puede estar vacío."
                ]);
                break;
            }
            
            // Llamar al método del modelo
            $resultado = $rol->verificarRol($nombreRol, $idRol);
            
            // Verificar si hubo error en la consulta
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'roles.php',
                    'Verificar rol',
                    'Error al verificar rol: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar el rol: " . $resultado['error']
                ]);
                break;
            }
            
            // Determinar mensaje para el registro de actividad
            $mensajeActividad = $resultado['existe'] ? 
                'Rol duplicado detectado: ' . $nombreRol : 
                'Rol disponible: ' . $nombreRol;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'roles.php',
                'Verificar rol',
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
