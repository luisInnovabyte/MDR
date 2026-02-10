<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Debug PDF Controller</h2>";
echo "<pre>";

// Simular POST
$_POST['id_presupuesto'] = 12; // Cambia esto por un ID válido
$_GET['op'] = 'cli_esp';

echo "1. Verificando archivos requeridos...\n";

if (!file_exists(__DIR__ . "/../config/conexion.php")) {
    die("✗ No existe conexion.php\n");
}
echo "✓ conexion.php existe\n";

if (!file_exists(__DIR__ . "/../config/funciones.php")) {
    die("✗ No existe funciones.php\n");
}
echo "✓ funciones.php existe\n";

if (!file_exists(__DIR__ . "/../models/ImpresionPresupuesto.php")) {
    die("✗ No existe ImpresionPresupuesto.php\n");
}
echo "✓ ImpresionPresupuesto.php existe\n";

if (!file_exists(__DIR__ . "/../models/Kit.php")) {
    die("✗ No existe Kit.php\n");
}
echo "✓ Kit.php existe\n";

if (!file_exists(__DIR__ . "/../vendor/tcpdf/tcpdf.php")) {
    die("✗ No existe tcpdf.php\n");
}
echo "✓ tcpdf.php existe\n";

echo "\n2. Cargando archivos...\n";

try {
    require_once __DIR__ . "/../config/conexion.php";
    echo "✓ conexion.php cargado\n";
} catch (Exception $e) {
    die("✗ Error cargando conexion.php: " . $e->getMessage() . "\n");
}

try {
    require_once __DIR__ . "/../config/funciones.php";
    echo "✓ funciones.php cargado\n";
} catch (Exception $e) {
    die("✗ Error cargando funciones.php: " . $e->getMessage() . "\n");
}

try {
    require_once __DIR__ . "/../models/ImpresionPresupuesto.php";
    echo "✓ ImpresionPresupuesto.php cargado\n";
} catch (Exception $e) {
    die("✗ Error cargando ImpresionPresupuesto.php: " . $e->getMessage() . "\n");
}

try {
    require_once __DIR__ . "/../models/Kit.php";
    echo "✓ Kit.php cargado\n";
} catch (Exception $e) {
    die("✗ Error cargando Kit.php: " . $e->getMessage() . "\n");
}

try {
    require_once __DIR__ . "/../vendor/tcpdf/tcpdf.php";
    echo "✓ tcpdf.php cargado\n";
} catch (Exception $e) {
    die("✗ Error cargando tcpdf.php: " . $e->getMessage() . "\n");
}

echo "\n3. Instanciando clases...\n";

try {
    $registro = new RegistroActividad();
    echo "✓ RegistroActividad instanciada\n";
} catch (Exception $e) {
    die("✗ Error instanciando RegistroActividad: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
}

try {
    $impresion = new ImpresionPresupuesto();
    echo "✓ ImpresionPresupuesto instanciada\n";
} catch (Exception $e) {
    die("✗ Error instanciando ImpresionPresupuesto: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
}

try {
    $kitModel = new Kit();
    echo "✓ Kit instanciada\n";
} catch (Exception $e) {
    die("✗ Error instanciando Kit: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
}

echo "\n4. Probando obtención de datos...\n";

try {
    $id_presupuesto = intval($_POST['id_presupuesto']);
    echo "ID Presupuesto: $id_presupuesto\n";
    
    $datos_presupuesto = $impresion->get_datos_cabecera($id_presupuesto);
    if ($datos_presupuesto) {
        echo "✓ Datos de presupuesto obtenidos: " . count($datos_presupuesto) . " campos\n";
        echo "  - Número: " . ($datos_presupuesto['numero_presupuesto'] ?? 'N/A') . "\n";
    } else {
        echo "✗ No se obtuvieron datos del presupuesto\n";
    }
} catch (Exception $e) {
    die("✗ Error obteniendo datos: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
}

try {
    $datos_empresa = $impresion->get_empresa_datos();
    if ($datos_empresa) {
        echo "✓ Datos de empresa obtenidos: " . count($datos_empresa) . " campos\n";
        echo "  - Nombre: " . ($datos_empresa['nombre_empresa'] ?? 'N/A') . "\n";
    } else {
        echo "✗ No se obtuvieron datos de la empresa\n";
    }
} catch (Exception $e) {
    die("✗ Error obteniendo datos empresa: " . $e->getMessage() . "\n" . $e->getTraceAsString() . "\n");
}

echo "\n5. Verificando clase MYPDF...\n";

try {
    // Verificar si la clase MYPDF está definida
    if (class_exists('MYPDF')) {
        echo "✗ MYPDF ya está definida (no debería estarlo aquí)\n";
    } else {
        echo "✓ MYPDF no está definida (correcto, se define en el controlador)\n";
    }
} catch (Exception $e) {
    echo "✗ Error verificando MYPDF: " . $e->getMessage() . "\n";
}

echo "\n✓✓✓ Todas las verificaciones pasaron!\n";
echo "\nSi ves este mensaje, el problema está en:\n";
echo "- La definición de la clase MYPDF dentro del controlador\n";
echo "- O en la generación del PDF en sí\n";
echo "\n</pre>";
?>
