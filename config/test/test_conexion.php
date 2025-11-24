<?php
require_once('../conexion.php');

try {
    $conexion = (new Conexion())->getConexion();
    echo "Conexión exitosa a la base de datos";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
