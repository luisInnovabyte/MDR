<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de Estadísticas de Furgonetas</h1>";
echo "<hr>";

try {
    echo "<p>1. Intentando cargar el modelo...</p>";
    require_once('../../models/Furgoneta.php');
    echo "<p style='color:green;'>✓ Modelo cargado exitosamente</p>";
    
    echo "<p>2. Intentando instanciar la clase...</p>";
    $furgonetaModel = new Furgoneta();
    echo "<p style='color:green;'>✓ Clase instanciada exitosamente</p>";
    
    echo "<p>3. Llamando a total_furgonetas()...</p>";
    $totalFurgonetas = $furgonetaModel->total_furgonetas();
    echo "<p style='color:green;'>✓ Total Furgonetas: <strong>$totalFurgonetas</strong></p>";
    
    echo "<p>4. Llamando a total_furgonetas_activas()...</p>";
    $totalFurgonetasActivas = $furgonetaModel->total_furgonetas_activas();
    echo "<p style='color:green;'>✓ Furgonetas Activas: <strong>$totalFurgonetasActivas</strong></p>";
    
    echo "<p>5. Llamando a total_furgonetas_operativas()...</p>";
    $totalFurgonetasOperativas = $furgonetaModel->total_furgonetas_operativas();
    echo "<p style='color:green;'>✓ Furgonetas Operativas: <strong>$totalFurgonetasOperativas</strong></p>";
    
    echo "<p>6. Llamando a total_furgonetas_taller()...</p>";
    $totalFurgonetasTaller = $furgonetaModel->total_furgonetas_taller();
    echo "<p style='color:green;'>✓ Furgonetas en Taller: <strong>$totalFurgonetasTaller</strong></p>";
    
    echo "<hr>";
    echo "<h2 style='color:green;'>¡Todas las pruebas pasaron exitosamente!</h2>";
    
} catch (Throwable $e) {
    echo "<p style='color:red;'><strong>✗ ERROR:</strong></p>";
    echo "<pre style='background:#ffeeee;padding:10px;border:1px solid red;'>";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n\n";
    echo "Stack Trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}
?>
