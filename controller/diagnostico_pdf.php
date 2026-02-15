<?php
// Diagnóstico simple del error 500
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h2>Diagnóstico de impresionpresupuesto_m2_pdf_es.php</h2>";

// 1. Verificar session
echo "<h3>1. Verificando sesión:</h3>";
if (session_status() === PHP_SESSION_NONE) {
    session_start();
    echo "✓ Sesión iniciada<br>";
} else {
    echo "✓ Sesión ya estaba activa<br>";
}

echo "Estado sesión: " . session_status() . "<br>";
echo "ID Usuario: " . ($_SESSION['id_usuario'] ?? 'NO DISPONIBLE') . "<br>";
echo "Email: " . ($_SESSION['email'] ?? 'NO DISPONIBLE') . "<br>";

// 2. Verificar requires
echo "<h3>2. Verificando archivos requeridos:</h3>";

$files_to_check = [
    '../config/conexion.php',
    '../config/funciones.php',
    '../models/ImpresionPresupuesto.php',
    '../models/Kit.php',
    '../models/Comerciales.php',
    '../vendor/tcpdf/tcpdf.php'
];

foreach ($files_to_check as $file) {
    $full_path = __DIR__ . '/' . $file;
    if (file_exists($full_path)) {
        echo "✓ $file existe<br>";
    } else {
        echo "✗ $file NO existe - RUTA: $full_path<br>";
    }
}

// 3. Verificar directorio de logs
echo "<h3>3. Verificando directorio de logs:</h3>";
$log_dir = __DIR__ . '/../public/logs/';
if (file_exists($log_dir)) {
    echo "✓ Directorio existe<br>";
    echo "Permisos: " . substr(sprintf('%o', fileperms($log_dir)), -4) . "<br>";
    if (is_writable($log_dir)) {
        echo "✓ Directorio es escribible<br>";
    } else {
        echo "✗ Directorio NO es escribible<br>";
    }
} else {
    echo "✗ Directorio NO existe<br>";
    if (@mkdir($log_dir, 0777, true)) {
        echo "✓ Directorio creado<br>";
    } else {
        echo "✗ No se pudo crear directorio<br>";
    }
}

// 4. Probar función debug
echo "<h3>4. Probando función debug_firma_log:</h3>";
function debug_firma_log($mensaje, $tipo = 'INFO') {
    try {
        $log_dir = __DIR__ . '/../public/logs/';
        if (!file_exists($log_dir)) {
            mkdir($log_dir, 0777, true);
        }
        $log_file = $log_dir . 'firma_debug_' . date('Y-m-d') . '.log';
        $timestamp = date('Y-m-d H:i:s');
        $log_msg = "[$timestamp] [PDF] [$tipo] $mensaje" . PHP_EOL;
        file_put_contents($log_file, $log_msg, FILE_APPEND);
        return true;
    } catch (Exception $e) {
        return "ERROR: " . $e->getMessage();
    }
}

$result = debug_firma_log("Test de diagnóstico", 'TEST');
if ($result === true) {
    echo "✓ Función debug funciona correctamente<br>";
} else {
    echo "✗ Error en función debug: $result<br>";
}

// 5. Probar modelo Comerciales
echo "<h3>5. Probando modelo Comerciales:</h3>";
try {
    require_once '../config/conexion.php';
    require_once '../models/Comerciales.php';
    
    $comercialesModel = new Comerciales();
    echo "✓ Modelo Comerciales instanciado correctamente<br>";
    
    // Si hay usuario en sesión, probar obtener firma
    if (isset($_SESSION['id_usuario'])) {
        $firma = $comercialesModel->get_firma_by_usuario($_SESSION['id_usuario']);
        if ($firma) {
            echo "✓ Firma obtenida: " . strlen($firma) . " bytes<br>";
        } else {
            echo "ℹ No se encontró firma para este usuario<br>";
        }
    } else {
        echo "ℹ No hay usuario en sesión para probar<br>";
    }
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
}

echo "<h3>6. Acceso GET parameters:</h3>";
echo "op: " . ($_GET['op'] ?? 'NO DEFINIDO') . "<br>";
echo "id: " . ($_GET['id_presupuesto'] ?? 'NO DEFINIDO') . "<br>";

echo "<h3>Conclusión:</h3>";
echo "<p>Si todos los puntos anteriores pasaron, el error 500 puede estar en otra parte del código o en la lógica específica del PDF.</p>";
?>
