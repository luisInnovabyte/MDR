<?php

require_once("../config/conexion.php");
require_once("../config/funciones.php");
require_once("../models/Productos.php");

$productos = new Productos();

switch ($_GET["op"]) {
    case "listar":
        $datos = $productos->get_producto();

        // Verifica si hay datos antes de procesarlos
        //if (!$datos) {
        //    die("Error: No se obtuvieron datos de get_producto()");
        //}

        $data = array();
        foreach ($datos as $row) {
            $sub_array = array();
            $sub_array[] = $row["prod_nom"];
            $sub_array[] = '<button type="button" onClick="editar(' . $row["prod_id"] . ');" id="' . $row["prod_id"] . '" class="btn btn-outline-primary btn-icon"><div><i class="fa fa-edit"></i></div></button>';
            $sub_array[] = '<button type="button" onClick="eliminar(' . $row["prod_id"] . ');" id="' . $row["prod_id"] . '" class="btn btn-outline-danger btn-icon"><div><i class="fa fa-trash"></i></div></button>';
            $data[] = $sub_array;
        }

        $results = array(
            "sEcho" => 1,
            "iTotalRecords" => count($data),
            "iTotalDisplayRecords" => count($data),
            "aaData" => $data
        );

        //echo "Llega hasta aquí";
        //exit; 

        // Guardar en un archivo JSON
        // file_put_contents("resultado.json", json_encode($result, JSON_PRETTY_PRINT));

        echo json_encode($results);
        //echo json_encode($result, JSON_PRETTY_PRINT);
        //break;
}
