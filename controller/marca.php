<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Marca.php";
require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión


// CREATE TABLE marca (
//     id_marca INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
//     codigo_marca VARCHAR(20) NOT NULL UNIQUE,
//     nombre_marca VARCHAR(100) NOT NULL,
//     name_marca VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
//     descr_marca VARCHAR(255),
//     activo_marca BOOLEAN DEFAULT TRUE,
//     created_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
//     updated_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$marca = new Marca();

switch ($_GET["op"]) {

    case "listar":
        $datos = $marca->get_marca();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "id_marca" => $row["id_marca"],
                "codigo_marca" => $row["codigo_marca"],
                "nombre_marca" => $row["nombre_marca"],
                "name_marca" => $row["name_marca"],
                "descr_marca" => $row["descr_marca"],
                "activo_marca" => $row["activo_marca"],
                "created_at_marca" => $row["created_at_marca"],
                "updated_at_marca" => $row["updated_at_marca"]
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
            $datos = $marca->get_marca_disponible();
            $data = array();
            foreach ($datos as $row) {
            $data[] = array(
                "id_marca" => $row["id_marca"],
                "codigo_marca" => $row["codigo_marca"],
                "nombre_marca" => $row["nombre_marca"],
                "name_marca" => $row["name_marca"],
                "descr_marca" => $row["descr_marca"],
                "activo_marca" => $row["activo_marca"],
                "created_at_marca" => $row["created_at_marca"],
                "updated_at_marca" => $row["updated_at_marca"]
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
                if (empty($_POST["id_marca"])) {
                    $resultado = $marca->insert_marca(
                        $_POST["codigo_marca"],
                        $_POST["nombre_marca"],
                        $_POST["name_marca"],
                        $_POST["descr_marca"]
                    );
    
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'marca.php',
                            'Guardar la marca',
                            "Marca guardada exitosamente",
                            "info"
                        );
    
                        echo json_encode([
                            "success" => true,
                            "message" => "Marca insertada correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al insertar la marca"
                        ]);
                    }
    
                } else {
                    $resultado = $marca->update_marca(
                        $_POST["id_marca"],
                        $_POST["codigo_marca"],
                        $_POST["nombre_marca"],
                        $_POST["name_marca"],
                        $_POST["descr_marca"]
                    );
    
                    if ($resultado !== false) {
                        $registro->registrarActividad(
                            'admin',
                            'marca.php',
                            'Actualizar la marca',
                            "Marca actualizada exitosamente",
                            "info"
                        );
    
                        echo json_encode([
                            "success" => true,
                            "message" => "Marca actualizada correctamente"
                        ]);
                    } else {
                        echo json_encode([
                            "success" => false,
                            "message" => "Error al actualizar la marca"
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
            $datos = $marca->get_marcaxid($_POST["id_marca"]);
            if ($datos) {
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "No se pudo obtener la marca solicitada"
                ]);
            }
            break;
            

    case "eliminar":
        $marca->delete_marcaxid($_POST["id_marca"]);
        break;

    case "activar":
        $marca->activar_marcaxid($_POST["id_marca"]);
        break;

    case "verificarMarca":
            $nombreMarca = isset($_GET["nombre_marca"]) ? trim($_GET["nombre_marca"]) : '';
            $nameMarca = isset($_GET["name_marca"]) ? trim($_GET["name_marca"]) : '';
            $codigoMarca = isset($_GET["codigo_marca"]) ? trim($_GET["codigo_marca"]) : '';
            $idMarca = isset($_GET["id_marca"]) ? trim($_GET["id_marca"]) : null;

            if (empty($nombreMarca) && empty($codigoMarca)) {
                $registro->registrarActividad(
                    'admin',
                    'marca.php',
                    'Verificar marca',
                    'Intento de verificación con nombre y código vacíos',
                    'warning'
                );
            
                echo json_encode([
                    "success" => false,
                    "message" => "El nombre o código de la marca no pueden estar vacíos."
                ]);
                break;
            }
            
            $resultado = $marca->verificarMarca($nombreMarca, $codigoMarca, $idMarca, $nameMarca);
            
            if (isset($resultado['error'])) {
                $registro->registrarActividad(
                    'admin',
                    'marca.php',
                    'Verificar marca',
                    'Error al verificar marca: ' . $resultado['error'],
                    'error'
                );
                
                echo json_encode([
                    "success" => false,
                    "message" => "Error al verificar la marca: " . $resultado['error']
                ]);
                break;
            }
            
            $mensajeActividad = $resultado['existe'] ? 
                'Marca duplicada detectada: ' . $nombreMarca : 
                'Marca disponible: ' . $nombreMarca;
            
            $tipoActividad = $resultado['existe'] ? 'warning' : 'info';
            
            // Registro de actividad
            $registro->registrarActividad(
                'admin',
                'marca.php',
                'Verificar marca',
                $mensajeActividad,
                $tipoActividad
            );
            
            // Devolver el resultado como JSON
            echo json_encode([
                "success" => true,
                "existe" => $resultado['existe']
            ]);
            break;
            
}
