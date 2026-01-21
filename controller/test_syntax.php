<?php
// Script para verificar sintaxis de lineapresupuesto.php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "Verificando sintaxis...\n";

$file = __DIR__ . '/lineapresupuesto.php';

if (!file_exists($file)) {
    die("Archivo no encontrado\n");
}

// Verificar sintaxis
$output = [];
$return_var = 0;
exec("php -l " . escapeshellarg($file), $output, $return_var);

if ($return_var === 0) {
    echo "✅ Sintaxis correcta\n";
    echo implode("\n", $output) . "\n";
} else {
    echo "❌ Error de sintaxis:\n";
    echo implode("\n", $output) . "\n";
}

// Intentar incluir el archivo
echo "\nIntentando incluir el archivo...\n";
try {
    // Definir variables mínimas necesarias
    $_GET['op'] = 'debug';
    $_POST['id_version_presupuesto'] = 2;
    
    ob_start();
    include $file;
    $output_content = ob_get_clean();
    
    echo "✅ Archivo incluido correctamente\n";
    echo "Salida: " . substr($output_content, 0, 200) . "...\n";
} catch (Exception $e) {
    echo "❌ Error al incluir: " . $e->getMessage() . "\n";
    echo "Archivo: " . $e->getFile() . "\n";
    echo "Línea: " . $e->getLine() . "\n";
}
?>
