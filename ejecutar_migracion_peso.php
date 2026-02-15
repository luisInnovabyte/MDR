<?php
/**
 * Script para ejecutar migración 20260215_add_peso_sistema.sql
 * Ejecutar desde línea de comandos: php ejecutar_migracion_peso.php
 */

require_once __DIR__ . '/config/conexion.php';
require_once __DIR__ . '/config/funciones.php';

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo " EJECUCIÓN DE MIGRACIÓN: Sistema de Peso en Presupuestos\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "\n";

try {
    // 1. Obtener conexión
    echo "[1/4] Conectando a base de datos...\n";
    $conn_obj = new Conexion();
    $pdo = $conn_obj->getConexion();
    echo "✓ Conexión establecida\n\n";
    
    // 2. Leer archivo SQL
    echo "[2/4] Leyendo archivo de migración...\n";
    $archivo_sql = __DIR__ . '/BD/migrations/20260215_add_peso_sistema.sql';
    
    if (!file_exists($archivo_sql)) {
        throw new Exception("No se encuentra el archivo: $archivo_sql");
    }
    
    $sql_completo = file_get_contents($archivo_sql);
    echo "✓ Archivo leído: " . strlen($sql_completo) . " bytes\n\n";
    
    // 3. Dividir en statements individuales
    echo "[3/4] Ejecutando migración...\n";
    
    // Eliminar comentarios de bloque
    $sql_completo = preg_replace('/--.*$/m', '', $sql_completo);
    
    // Dividir por punto y coma (statements)
    $statements = array_filter(
        array_map('trim', explode(';', $sql_completo)),
        function($stmt) {
            return !empty($stmt) && 
                   !preg_match('/^(--|\/\*)/', $stmt) &&
                   strlen($stmt) > 10;
        }
    );
    
    $ejecutados = 0;
    $errores = 0;
    
    foreach ($statements as $index => $statement) {
        try {
            // Saltar si es solo comentario
            if (preg_match('/^\s*(--|#|\/\*)/i', $statement)) {
                continue;
            }
            
            // Ejecutar statement
            $pdo->exec($statement);
            $ejecutados++;
            
            // Mostrar progreso cada 5 statements
            if (($ejecutados % 5) == 0) {
                echo "  · Ejecutados: $ejecutados statements...\n";
            }
            
        } catch (PDOException $e) {
            // Ignorar errores de "ya existe" (índices, vistas)
            if (strpos($e->getMessage(), 'Duplicate key') !== false ||
                strpos($e->getMessage(), 'already exists') !== false ||
                strpos($e->getMessage(), 'Ya existe') !== false) {
                echo "  ⚠ Advertencia (ignorada): " . substr($e->getMessage(), 0, 80) . "...\n";
            } else {
                $errores++;
                echo "  ✗ Error en statement " . ($index + 1) . ": " . $e->getMessage() . "\n";
            }
        }
    }
    
    echo "\n✓ Migración completada\n";
    echo "  - Statements ejecutados: $ejecutados\n";
    echo "  - Errores: $errores\n\n";
    
    // 4. Verificación
    echo "[4/4] Verificando cambios...\n\n";
    
    // Verificar campo peso_elemento
    $stmt = $pdo->query("
        SELECT COLUMN_NAME, DATA_TYPE, NUMERIC_PRECISION, NUMERIC_SCALE
        FROM INFORMATION_SCHEMA.COLUMNS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'elemento'
          AND COLUMN_NAME = 'peso_elemento'
    ");
    $campo = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($campo) {
        echo "✓ Campo 'peso_elemento' añadido correctamente\n";
        echo "  - Tipo: {$campo['DATA_TYPE']}\n";
        echo "  - Precisión: {$campo['NUMERIC_PRECISION']},{$campo['NUMERIC_SCALE']}\n\n";
    } else {
        echo "✗ ERROR: Campo 'peso_elemento' NO fue creado\n\n";
    }
    
    // Verificar vistas
    $stmt = $pdo->query("
        SELECT TABLE_NAME
        FROM INFORMATION_SCHEMA.TABLES
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_TYPE = 'VIEW'
          AND TABLE_NAME LIKE 'vista_%peso%'
        ORDER BY TABLE_NAME
    ");
    $vistas = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Vistas creadas (" . count($vistas) . "):\n";
    foreach ($vistas as $vista) {
        echo "  ✓ $vista\n";
    }
    echo "\n";
    
    // Verificar índices
    $stmt = $pdo->query("
        SELECT INDEX_NAME
        FROM INFORMATION_SCHEMA.STATISTICS
        WHERE TABLE_SCHEMA = DATABASE()
          AND TABLE_NAME = 'elemento'
          AND INDEX_NAME LIKE '%peso%'
        GROUP BY INDEX_NAME
    ");
    $indices = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Índices creados en 'elemento' (" . count($indices) . "):\n";
    foreach ($indices as $indice) {
        echo "  ✓ $indice\n";
    }
    echo "\n";
    
    // Registrar en log
    $registro = new RegistroActividad();
    $registro->registrarActividad(
        'system',
        'Migración',
        '20260215_add_peso_sistema',
        "Migración ejecutada correctamente. Statements: $ejecutados, Errores: $errores",
        'info'
    );
    
    echo "═══════════════════════════════════════════════════════════\n";
    echo " ✅ MIGRACIÓN COMPLETADA CON ÉXITO\n";
    echo "═══════════════════════════════════════════════════════════\n";
    echo "\n";
    echo "Siguientes pasos:\n";
    echo "1. Modificar modelos PHP (Elemento.php, ImpresionPresupuesto.php)\n";
    echo "2. Añadir campo peso en interfaz de Elementos\n";
    echo "3. Integrar en PDF de presupuesto\n";
    echo "4. Probar con datos reales\n";
    echo "\n";
    
} catch (Exception $e) {
    echo "\n✗ ERROR CRÍTICO:\n";
    echo $e->getMessage() . "\n\n";
    echo $e->getTraceAsString() . "\n\n";
    
    // Registrar error
    if (isset($registro)) {
        $registro->registrarActividad(
            'system',
            'Migración',
            '20260215_add_peso_sistema',
            "ERROR: " . $e->getMessage(),
            'error'
        );
    }
    
    exit(1);
}
