<?php
require_once "../config/conexion.php";
require_once "../models/UnidadMedida.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$unidadMedida = new UnidadMedida();

switch ($_GET["op"]) {
    case "listarDisponibles":
        $datos = $unidadMedida->get_unidades_disponibles();
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;
        
    case "listarTodasParaModal":
        $datos = $unidadMedida->get_todas_unidades_para_modal();
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;
        
    case "obtenerUnidadPorId":
        header('Content-Type: application/json; charset=utf-8');
        $datos = $unidadMedida->get_unidadxid($_GET["id_unidad"]);
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener la unidad de medida solicitada"
            ]);
        }
        break;
        
    case "mostrar":
        header('Content-Type: application/json; charset=utf-8');
        $datos = $unidadMedida->get_unidadxid($_POST["id_unidad"]);
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener la unidad de medida solicitada"
            ]);
        }
        break;
}
?>