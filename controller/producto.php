<?php
require_once "../config/conexion.php";
require_once "../models/Productos.php";
require_once "../config/funciones.php";


$producto = new Productos();
$usuario = "usuarioEjemplo";  // AQUI TIENE QUE IR EL USUARIO QUE HAY EN SESION, AHORA HAY UNO ESTATICO
$pantalla = "Productos";  // LA PANTALLA SIEMPRE VA A SER PRODUCTOS EN ESTE CASO


switch ($_GET["op"]) {

    case "listar":
        $datos = $producto->get_producto();
        $data = array();
        foreach ($datos as $row) {
            $data[] = array(
                "prod_id" => $row["prod_id"],
                "prod_nom" => $row["prod_nom"],
                "fech_crea" => $row["fech_crea"],
                "fech_modi" => $row["fech_modi"],
                "fech_elim" => $row["fech_elim"],
                "est" => $row["est"],
                "oferta" => $row["oferta"],
                "estadoProducto" => $row["estadoProducto"],
                "paisesId" => $row["paisesId"],
                "descrPais" => $row["descrPais"]
            );
        }

        $results = array(
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );

        // ACCION LISTAR PARA EL ARCHIVO DE LOGS
        // AQUI SE REGISTRA LA ACCION LISTAR EN EL FICHERO INTERACCIONES.
        $accion = "listar";
        $mensaje = "Se listaron los productos.";
        $registro = new RegistroInteracciones($usuario, $pantalla, $accion, $mensaje);  // Registrar acción

        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;

    case "guardaryeditar":
        if (empty($_POST["prod_id"])) {
            $producto->insert_producto($_POST["prod_nom"], $_POST["oferta"], $_POST["estadoProducto"], $_POST["paisesId"]);

            // ACCION GUARDAR PARA EL ARCHIVO DE LOGS
            //AQUI HAGO EL REGISTRO DEL GUARDAR EN EL FICHERO INTERACCIONES
            $accion = "guardar";
            $mensaje = "Se creó un nuevo producto: " . $_POST["prod_nom"]; // MENSAJE + NOMBRE DE PRODUCTO DEL POST
            $registro = new RegistroInteracciones($usuario, $pantalla, $accion, $mensaje);  // INSTANCIO LA CLASE PARA QUE SE HAGA LA ACCION

        } else {
            $producto->update_producto($_POST["prod_id"], $_POST["prod_nom"], $_POST["oferta"], $_POST["estadoProducto"]);

            // ACCION EDITAR/UPDATE PARA EL ARCHIVO DE LOGS
            $accion = "editar";
            $mensaje = "Se actualizó el producto con ID: " . $_POST["prod_id"]; // MENSAJE + NOMBRE DE PRODUCTO DEL POST
            $registro = new RegistroInteracciones($usuario, $pantalla, $accion, $mensaje);  // INSTANCIO LA CLASE PARA QUE SE HAGA LA ACCION

        }
        break;

    case "mostrar":
        $datos = $producto->get_productoxid($_POST["prod_id"]);
        // if (is_array($datos) == true and count($datos) > 0) {
        //     foreach ($datos as $row) {
        //         $output["prod_id"] = $row["prod_id"];
        //         $output["prod_nom"] = $row["prod_nom"];
        //     }
        // }
        //echo json_encode($output);

        // ACCION MOSTRAR PARA EL ARCHIVO DE LOGS
        $accion = "mostrar";
        $mensaje = "Se mostró el producto con ID: " . $_POST["prod_id"]; // MENSAJE + ID DE PRODUCTO DEL POST
        $registro = new RegistroInteracciones($usuario, $pantalla, $accion, $mensaje);  // INSTANCIO LA CLASE PARA QUE SE HAGA LA ACCION

        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;


    case "eliminar":
        $producto->delete_productoxid($_POST["prod_id"]);

        // ACCION ELIMINAR PARA EL ARCHIVO DE LOGS
        $accion = "eliminar";
        $mensaje = "Se eliminó el producto con ID: " . $_POST["prod_id"];  // MENSAJE + ID DE PRODUCTO DEL POST
        $registro = new RegistroInteracciones($usuario, $pantalla, $accion, $mensaje);  // INSTANCIO LA CLASE PARA QUE SE HAGA LA ACCION

        break;

    case "activar":
        $producto->activar_productoxid($_POST["prod_id"]);

        // ACCION ACTIVAR PARA EL ARCHIVO DE LOGS
        $accion = "activar";
        $mensaje = "Se activó el producto con ID: " . $_POST["prod_id"]; // MENSAJE + ID DE PRODUCTO DEL POST
        $registro = new RegistroInteracciones($usuario, $pantalla, $accion, $mensaje);  // INSTANCIO LA CLASE PARA QUE SE HAGA LA ACCION

        break;
}
