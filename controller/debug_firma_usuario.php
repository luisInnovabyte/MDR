<?php
/**
 * Script de debug para verificar la firma del usuario actual
 */

session_start();

header('Content-Type: text/html; charset=utf-8');

require_once '../config/conexion.php';
require_once '../models/Comerciales.php';

echo "<h1>Debug de Firma Digital</h1>";

// 1. Verificar sesión
echo "<h2>1. Información de Sesión</h2>";
echo "<pre>";
echo "Sesión iniciada: " . (isset($_SESSION['sesion_iniciada']) && $_SESSION['sesion_iniciada'] ? 'SÍ' : 'NO') . "\n";
echo "ID Usuario: " . ($_SESSION['id_usuario'] ?? 'NO DISPONIBLE') . "\n";
echo "Email: " . ($_SESSION['email'] ?? 'NO DISPONIBLE') . "\n";
echo "</pre>";

if (!isset($_SESSION['id_usuario'])) {
    echo "<p style='color: red;'><strong>ERROR: No hay id_usuario en sesión</strong></p>";
    exit;
}

$id_usuario = $_SESSION['id_usuario'];

// 2. Verificar comercial
echo "<h2>2. Información del Comercial</h2>";
try {
    $comercialesModel = new Comerciales();
    $comercial = $comercialesModel->get_comercial_by_usuario($id_usuario);
    
    if ($comercial) {
        echo "<pre>";
        echo "✓ Comercial encontrado:\n";
        echo "  - ID Comercial: " . $comercial['id_comercial'] . "\n";
        echo "  - Nombre: " . $comercial['nombre'] . " " . $comercial['apellidos'] . "\n";
        echo "</pre>";
    } else {
        echo "<p style='color: red;'><strong>ERROR: Usuario no tiene comercial asociado</strong></p>";
        exit;
    }
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>ERROR: " . $e->getMessage() . "</strong></p>";
    exit;
}

// 3. Obtener firma
echo "<h2>3. Firma Digital en Base de Datos</h2>";
try {
    $firma_comercial = $comercialesModel->get_firma_by_usuario($id_usuario);
    
    if (!empty($firma_comercial)) {
        echo "<pre>";
        echo "✓ Firma encontrada:\n";
        echo "  - Longitud total: " . strlen($firma_comercial) . " caracteres\n";
        echo "  - Primeros 100 caracteres: " . substr($firma_comercial, 0, 100) . "...\n\n";
        
        // Verificar formato
        if (preg_match('/^data:image\/(png|jpg|jpeg);base64,/', $firma_comercial, $matches)) {
            echo "  ✓ Formato válido detectado: {$matches[0]}\n";
            
            // Verificar si es base64 válido después del prefijo
            $solo_base64 = preg_replace('/^data:image\/(png|jpg|jpeg);base64,/', '', $firma_comercial);
            $decoded = base64_decode($solo_base64, true);
            
            if ($decoded !== false) {
                echo "  ✓ Base64 válido (decodifica correctamente)\n";
                echo "  - Tamaño decodificado: " . strlen($decoded) . " bytes\n";
            } else {
                echo "  ✗ ERROR: Base64 inválido (no se puede decodificar)\n";
            }
        } else {
            echo "  ✗ ERROR: Formato inválido - NO comienza con 'data:image/png;base64,'\n";
            echo "  - La firma debe comenzar con el prefijo correcto\n";
        }
        echo "</pre>";
        
        // 4. Mostrar vista previa de la firma
        echo "<h2>4. Vista Previa</h2>";
        echo "<p>Esta es la firma tal como se guardó en la base de datos:</p>";
        echo "<img src='" . htmlspecialchars($firma_comercial) . "' style='border: 1px solid #ccc; max-width: 400px; background: white;' alt='Firma'>";
        
    } else {
        echo "<p style='color: orange;'><strong>ADVERTENCIA: No hay firma guardada para este usuario</strong></p>";
        echo "<p>Ve a tu perfil y dibuja/guarda una firma primero.</p>";
    }
    
} catch (Exception $e) {
    echo "<p style='color: red;'><strong>ERROR al obtener firma: " . $e->getMessage() . "</strong></p>";
}

// 5. Verificar logs recientes
echo "<h2>5. Logs Recientes</h2>";
$log_file = __DIR__ . '/../public/logs/firma_debug_' . date('Y-m-d') . '.log';
if (file_exists($log_file)) {
    echo "<p><a href='../view/Home/ver_logs_firma.php' target='_blank'>Ver logs completos</a></p>";
    echo "<pre style='max-height: 300px; overflow: auto; background: #f5f5f5; padding: 10px;'>";
    $logs = file_get_contents($log_file);
    $lineas = explode("\n", $logs);
    // Mostrar últimas 20 líneas
    $ultimas = array_slice($lineas, -20);
    echo htmlspecialchars(implode("\n", $ultimas));
    echo "</pre>";
} else {
    echo "<p>No hay logs del día de hoy.</p>";
}

echo "<hr>";
echo "<p><strong>Instrucciones:</strong></p>";
echo "<ol>";
echo "<li>Si no hay firma guardada, ve a tu <a href='../view/Home/perfil.php'>perfil</a> y dibuja/guarda una</li>";
echo "<li>Si hay firma pero el formato es inválido, vuelve a dibujar y guardar</li>";
echo "<li>Si el formato es válido pero no aparece en PDF, revisa los logs de generación del PDF</li>";
echo "</ol>";
?>
