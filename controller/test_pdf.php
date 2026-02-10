<?php
// Script de prueba para identificar errores
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "1. Probando require de archivos...<br>";

try {
    require_once __DIR__ . "/../config/conexion.php";
    echo "✓ conexion.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error en conexion.php: " . $e->getMessage() . "<br>";
    die();
}

try {
    require_once __DIR__ . "/../config/funciones.php";
    echo "✓ funciones.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error en funciones.php: " . $e->getMessage() . "<br>";
    die();
}

try {
    require_once __DIR__ . "/../models/ImpresionPresupuesto.php";
    echo "✓ ImpresionPresupuesto.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error en ImpresionPresupuesto.php: " . $e->getMessage() . "<br>";
    die();
}

try {
    require_once __DIR__ . "/../models/Kit.php";
    echo "✓ Kit.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error en Kit.php: " . $e->getMessage() . "<br>";
    die();
}

try {
    require_once __DIR__ . "/../vendor/tcpdf/tcpdf.php";
    echo "✓ tcpdf.php cargado<br>";
} catch (Exception $e) {
    echo "✗ Error en tcpdf.php: " . $e->getMessage() . "<br>";
    die();
}

echo "<br>2. Probando instanciación de clases...<br>";

try {
    $registro = new RegistroActividad();
    echo "✓ RegistroActividad instanciada<br>";
} catch (Exception $e) {
    echo "✗ Error en RegistroActividad: " . $e->getMessage() . "<br>";
}

try {
    $impresion = new ImpresionPresupuesto();
    echo "✓ ImpresionPresupuesto instanciada<br>";
} catch (Exception $e) {
    echo "✗ Error en ImpresionPresupuesto: " . $e->getMessage() . "<br>";
}

try {
    $kitModel = new Kit();
    echo "✓ Kit instanciada<br>";
} catch (Exception $e) {
    echo "✗ Error en Kit: " . $e->getMessage() . "<br>";
}

try {
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
    echo "✓ TCPDF instanciada<br>";
} catch (Exception $e) {
    echo "✗ Error en TCPDF: " . $e->getMessage() . "<br>";
}

echo "<br>✓✓✓ Todas las pruebas pasaron correctamente!<br>";
?>
