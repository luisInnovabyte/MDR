<?php
// Script de prueba para verificar el funcionamiento de estado_elemento
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<!DOCTYPE html><html><head><title>Test Estado Elemento</title></head><body>";
echo "<h1>Test de Estado Elemento</h1>";

require_once "../config/conexion.php";
require_once "../models/Estado_elemento.php";
require_once '../config/funciones.php';

echo "<h2>1. Test de Conexión</h2>";
try {
    $conexion = (new Conexion())->getConexion();
    echo "✅ Conexión establecida correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error de conexión: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>2. Test de Tabla Estado Elemento</h2>";
try {
    $sql = "SHOW TABLES LIKE 'estado_elemento'";
    $stmt = $conexion->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetch();
    
    if ($result) {
        echo "✅ La tabla 'estado_elemento' existe<br>";
    } else {
        echo "❌ La tabla 'estado_elemento' NO existe<br>";
        echo "<p style='color: red;'>Debes ejecutar el archivo: w:\\MDR\\BD\\crear_tabla_estado_elemento.sql</p>";
        exit;
    }
} catch (Exception $e) {
    echo "❌ Error al verificar tabla: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>3. Test de Modelo</h2>";
try {
    $estado_elemento = new Estado_elemento();
    echo "✅ Modelo Estado_elemento instanciado correctamente<br>";
} catch (Exception $e) {
    echo "❌ Error al instanciar modelo: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>4. Test de Listar Estados</h2>";
try {
    $estados = $estado_elemento->get_estado_elemento();
    echo "✅ Query ejecutada correctamente<br>";
    echo "📊 Total de registros: " . count($estados) . "<br>";
    
    if (count($estados) > 0) {
        echo "<table border='1' cellpadding='5' style='margin-top: 10px;'>";
        echo "<tr><th>ID</th><th>Código</th><th>Descripción</th><th>Color</th><th>Permite Alquiler</th><th>Activo</th></tr>";
        foreach ($estados as $estado) {
            echo "<tr>";
            echo "<td>" . $estado['id_estado_elemento'] . "</td>";
            echo "<td>" . $estado['codigo_estado_elemento'] . "</td>";
            echo "<td>" . $estado['descripcion_estado_elemento'] . "</td>";
            echo "<td style='background-color: " . $estado['color_estado_elemento'] . ";'>" . $estado['color_estado_elemento'] . "</td>";
            echo "<td>" . ($estado['permite_alquiler_estado_elemento'] ? 'SÍ' : 'NO') . "</td>";
            echo "<td>" . ($estado['activo_estado_elemento'] ? 'Activo' : 'Inactivo') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "⚠️ No hay registros en la tabla<br>";
    }
} catch (Exception $e) {
    echo "❌ Error al listar estados: " . $e->getMessage() . "<br>";
    exit;
}

echo "<h2>5. Test de Get Estado por ID</h2>";
if (count($estados) > 0) {
    $primer_id = $estados[0]['id_estado_elemento'];
    echo "Probando con ID: " . $primer_id . "<br>";
    
    try {
        $estado = $estado_elemento->get_estado_elementoxid($primer_id);
        
        if ($estado && is_array($estado)) {
            echo "✅ Estado obtenido correctamente<br>";
            echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
            echo json_encode($estado, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            echo "</pre>";
        } else {
            echo "❌ No se obtuvo el estado (retornó: " . var_export($estado, true) . ")<br>";
        }
    } catch (Exception $e) {
        echo "❌ Error al obtener estado por ID: " . $e->getMessage() . "<br>";
    }
}

echo "<h2>6. Test de Controller (simulado)</h2>";
echo "Simulando POST a controller con op=mostrar e id=" . $primer_id . "<br>";
$_POST['id_estado_elemento'] = $primer_id;
$_GET['op'] = 'mostrar';

ob_start();
try {
    include('../controller/estado_elemento.php');
    $output = ob_get_clean();
    
    echo "✅ Controller ejecutado<br>";
    echo "📤 Respuesta:<br>";
    echo "<pre style='background: #f5f5f5; padding: 10px; border-radius: 5px;'>";
    
    $json = json_decode($output, true);
    if ($json) {
        echo json_encode($json, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    } else {
        echo htmlspecialchars($output);
    }
    echo "</pre>";
} catch (Exception $e) {
    ob_end_clean();
    echo "❌ Error en controller: " . $e->getMessage() . "<br>";
}

echo "<hr>";
echo "<h2>Resumen</h2>";
echo "<p>Si todos los tests pasaron con ✅, el sistema está funcionando correctamente.</p>";
echo "<p>Si hay errores ❌, revisa los mensajes para identificar el problema.</p>";

echo "<hr>";
echo "<p><a href='../view/MntEstados_elemento/index.php'>Ir a Mantenimiento de Estados de Elemento</a></p>";

echo "</body></html>";
?>
