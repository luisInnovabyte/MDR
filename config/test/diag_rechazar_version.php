<?php
/**
 * Diagnóstico: rechazar versión
 * Comprueba si la columna motivo_rechazo_version existe y prueba un UPDATE real.
 * Abrir en: http://localhost/MDR/config/test/diag_rechazar_version.php
 */
require_once '../conexion.php';
$pdo = (new Conexion())->getConexion();

echo '<pre>';
echo "=== DIAGNÓSTICO rechazar_version ===\n\n";

// 1. Comprobar columnas de presupuesto_version
echo "--- Columnas de presupuesto_version ---\n";
$cols = $pdo->query("SHOW COLUMNS FROM presupuesto_version")->fetchAll(PDO::FETCH_ASSOC);
foreach ($cols as $c) {
    echo "  " . $c['Field'] . "  (" . $c['Type'] . ")" . ($c['Field'] === 'motivo_rechazo_version' ? "  ← BUSCADA" : "") . "\n";
}

// 2. Comprobar si motivo_rechazo_version existe
$tiene_col = false;
foreach ($cols as $c) { if ($c['Field'] === 'motivo_rechazo_version') { $tiene_col = true; break; } }
echo "\nmotivo_rechazo_version existe: " . ($tiene_col ? "✅ SÍ" : "❌ NO — hay que añadirla") . "\n";

// 3. Si no existe, añadirla
if (!$tiene_col) {
    echo "\nAñadiendo columna motivo_rechazo_version...\n";
    try {
        $pdo->exec("ALTER TABLE presupuesto_version 
                    ADD COLUMN motivo_rechazo_version TEXT 
                    COMMENT 'Razón por la que el cliente rechazó esta versión'
                    AFTER fecha_rechazo_version");
        echo "✅ Columna añadida correctamente\n";
    } catch (PDOException $e) {
        echo "❌ Error al añadir columna: " . $e->getMessage() . "\n";
    }
}

// 4. Listar versiones activas
echo "\n--- Versiones activas ---\n";
$vers = $pdo->query("SELECT id_version_presupuesto, id_presupuesto, numero_version_presupuesto, 
                            estado_version_presupuesto, activo_version 
                     FROM presupuesto_version 
                     WHERE activo_version = 1 
                     ORDER BY id_version_presupuesto DESC 
                     LIMIT 10")->fetchAll(PDO::FETCH_ASSOC);
foreach ($vers as $v) {
    echo "  ID={$v['id_version_presupuesto']}  ppto={$v['id_presupuesto']}  v{$v['numero_version_presupuesto']}  estado={$v['estado_version_presupuesto']}  activo={$v['activo_version']}\n";
}

// 4b. Ver registros de estado_presupuesto
echo "\n--- estado_presupuesto (todos los registros) ---\n";
$estados = $pdo->query("SELECT id_estado_ppto, nombre_estado_ppto, codigo_estado_ppto FROM estado_presupuesto ORDER BY id_estado_ppto")->fetchAll(PDO::FETCH_ASSOC);
foreach ($estados as $e) {
    echo "  id={$e['id_estado_ppto']}  codigo='{$e['codigo_estado_ppto']}'  nombre='{$e['nombre_estado_ppto']}'\n";
}

// 4c. Ver trigger trg_version_sync_estado_cabecera
echo "\n--- Trigger trg_version_sync_estado_cabecera ---\n";
$trg = $pdo->query("SHOW CREATE TRIGGER trg_version_sync_estado_cabecera")->fetch(PDO::FETCH_ASSOC);
echo isset($trg['SQL Original Statement']) ? $trg['SQL Original Statement'] : (isset($trg[2]) ? $trg[2] : print_r($trg, true));
echo "\n";

// 5. Simular el UPDATE de rechazar sobre la última versión borrador/enviado
$v_prueba = null;
foreach ($vers as $v) {
    if (in_array($v['estado_version_presupuesto'], ['borrador', 'enviado'])) {
        $v_prueba = $v;
        break;
    }
}

if ($v_prueba) {
    echo "\n--- Simulando UPDATE rechazar sobre versión ID={$v_prueba['id_version_presupuesto']} ---\n";
    try {
        $stmt = $pdo->prepare("UPDATE presupuesto_version SET 
                                    estado_version_presupuesto = 'rechazado',
                                    enviado_por_version = NULL,
                                    motivo_rechazo_version = ?
                               WHERE id_version_presupuesto = ?
                               AND activo_version = 1");
        $stmt->bindValue(1, 'PRUEBA DIAGNÓSTICO - se revertirá', PDO::PARAM_STR);
        $stmt->bindValue(2, $v_prueba['id_version_presupuesto'], PDO::PARAM_INT);
        $stmt->execute();
        $filas = $stmt->rowCount();
        echo "rowCount() = $filas  " . ($filas > 0 ? "✅ UPDATE OK" : "❌ Sin filas afectadas") . "\n";

        // Revertir
        $pdo->prepare("UPDATE presupuesto_version SET estado_version_presupuesto = ?, motivo_rechazo_version = NULL 
                        WHERE id_version_presupuesto = ?")
            ->execute([$v_prueba['estado_version_presupuesto'], $v_prueba['id_version_presupuesto']]);
        echo "↩️  Estado revertido a '{$v_prueba['estado_version_presupuesto']}'\n";
    } catch (PDOException $e) {
        echo "❌ PDOException: " . $e->getMessage() . "\n";
    }
} else {
    echo "\nNo hay versiones en estado borrador/enviado para probar el UPDATE\n";
}

echo "\n=== FIN DIAGNÓSTICO ===\n";
echo '</pre>';
