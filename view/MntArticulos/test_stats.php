<?php
// Archivo de prueba para diagnosticar el error
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "1. Iniciando pruebas...<br>";

try {
    echo "2. Cargando verificarPermiso.php...<br>";
    $moduloActual = 'usuarios';
    require_once('../../config/template/verificarPermiso.php');
    echo "3. verificarPermiso.php cargado correctamente<br>";
} catch (Exception $e) {
    die("ERROR en verificarPermiso: " . $e->getMessage() . "<br>");
}

try {
    echo "4. Cargando modelo Articulo.php...<br>";
    require_once('../../models/Articulo.php');
    echo "5. Articulo.php cargado correctamente<br>";
} catch (Exception $e) {
    die("ERROR al cargar Articulo.php: " . $e->getMessage() . "<br>");
}

try {
    echo "6. Creando instancia de Articulo...<br>";
    $articuloModel = new Articulo();
    echo "7. Instancia creada correctamente<br>";
} catch (Exception $e) {
    die("ERROR al crear instancia: " . $e->getMessage() . "<br>");
}

try {
    echo "8. Llamando a total_articulo()...<br>";
    $total = $articuloModel->total_articulo();
    echo "9. Total de artículos: " . $total . "<br>";
} catch (Exception $e) {
    die("ERROR en total_articulo(): " . $e->getMessage() . "<br>");
}

try {
    echo "10. Llamando a total_articulo_activo()...<br>";
    $totalActivos = $articuloModel->total_articulo_activo();
    echo "11. Total de artículos activos: " . $totalActivos . "<br>";
} catch (Exception $e) {
    die("ERROR en total_articulo_activo(): " . $e->getMessage() . "<br>");
}

echo "<br><strong>¡Todas las pruebas pasaron correctamente!</strong>";
?>
