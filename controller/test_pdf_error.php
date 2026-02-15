<?php
// Script de prueba para capturar errores en impresionpresupuesto_m2_pdf_es.php

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../public/logs/php_error.log');

echo "<h2>Test de inclusión de archivo</h2>";

try {
    // Probar si el archivo tiene errores de sintaxis
    echo "1. Verificando sintaxis...<br>";
    
    // Capturar errores
    ob_start();
    include_once __DIR__ . '/impresionpresupuesto_m2_pdf_es.php';
    $output = ob_get_clean();
    
    echo "2. Archivo incluido correctamente<br>";
    
} catch (Throwable $e) {
    echo "<pre style='color: red; background: #fee;'>";
    echo "ERROR CAPTURADO:\n";
    echo "Mensaje: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
    echo "Trace:\n" . $e->getTraceAsString();
    echo "</pre>";
}

// Verificar si hay errores en el log
$log_file = __DIR__ . '/../public/logs/php_error.log';
if (file_exists($log_file)) {
    echo "<h3>Últimos errores del log:</h3>";
    echo "<pre>";
    echo file_get_contents($log_file);
    echo "</pre>";
}
?>
