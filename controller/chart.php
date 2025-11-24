<?php
require_once "../config/conexion.php";
// require_once "../config/funciones.php";
require_once "../models/Chart.php";

require_once '../config/funciones.php'; // ✅ Se incluye correctamente el archivo de conexión

// Función para escribir en el log - Desarrollo
function writeToLog($logData)
{
    $logFile = "../public/logs/log_" . date("Ymd") . ".txt"; // Nombre del archivo de log
    $logMessage = "[" . date("Y-m-d H:i:s") . "] " . json_encode($logData, JSON_UNESCAPED_UNICODE) . "\n";
    file_put_contents($logFile, $logMessage, FILE_APPEND);
}

$registro = new RegistroActividad(); // ✅ Se crea una instancia de la clase RegistroActividad
$chart = new ChartModel();


switch ($_GET["op"]) {

    case "circuloxllamadasxcomercial":
        $datos = $chart->getLlamadasxcomercial();

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);

        break;


    case "circuloxllamadasxcomercialxmes":
    session_start();

    $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
    $año = isset($_GET['año']) ? (int)$_GET['año'] : date('Y');

    $idComercial = $_SESSION['id_comercial'] ?? null;  // id_comercial guardado en sesión
    $idRol = $_SESSION['id_rol'] ?? null;              // rol del usuario

    if ($idRol === 4 && $idComercial !== null) {
        // Si es comercial, filtrar por su id_comercial
        $datos = $chart->getLlamadasxmesxcomercial($mes, $año, $idComercial);
    } else {
        // Si no es comercial, obtener todas las llamadas sin filtrar por comercial
        $datos = $chart->getLlamadasxmesxcomercial($mes, $año);
    }

    header('Content-Type: application/json');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);

    break;


       case "circuloxllamadasxcomercialxmesxestado":
    session_start();

    $mes = isset($_GET['mes']) ? (int)$_GET['mes'] : date('n');
    $año = isset($_GET['año']) ? (int)$_GET['año'] : date('Y');
    $estado = isset($_GET['estado']) ? $_GET['estado'] : '';

    // Mapa de conversión de estados (de URL a BD)
    $estadoMap = [
        "RecibidasSinAtencion" => "Recibida sin Atención",
        "ConContacto" => "Con contacto",
        "CitasCerradas" => "Cita Cerrada",
        "Perdidas" => "Perdida"
    ];

    // Convertir el estado si está en el mapa
    if (isset($estadoMap[$estado])) {
        $estado = $estadoMap[$estado]; // Transformar el valor al formato de la base de datos
    } else {
        http_response_code(400); // Bad Request
        echo json_encode([
            'error' => 'Estado no válido',
            'estados_permitidos' => array_keys($estadoMap)
        ]);
        exit;
    }

    // Filtrar si es comercial
    $idComercial = null;
    if (isset($_SESSION['id_rol']) && $_SESSION['id_rol'] == 4 && isset($_SESSION['id_comercial'])) {
        $idComercial = $_SESSION['id_comercial'];
    }

    // Llamar al método con el posible filtro
    $datos = $chart->getLlamadasxmesxcomercialxestado($mes, $año, $estado, $idComercial);

    header('Content-Type: application/json');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    break;


}
