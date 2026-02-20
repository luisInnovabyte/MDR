<?php
/**
 * Diagnóstico: comparar_versiones
 * Abrir en: http://localhost/MDR/config/test/diag_comparar_versiones.php
 */
require_once '../conexion.php';
require_once '../funciones.php';
require_once '../../models/PresupuestoVersion.php';

$pdo = (new Conexion())->getConexion();

echo '<pre>';
echo "=== DIAGNÓSTICO comparar_versiones ===\n\n";

// 1. Versiones disponibles para comparar
echo "--- Versiones disponibles ---\n";
$vers = $pdo->query("SELECT id_version_presupuesto, id_presupuesto, numero_version_presupuesto, estado_version_presupuesto FROM presupuesto_version WHERE activo_version = 1 ORDER BY id_presupuesto, numero_version_presupuesto")->fetchAll(PDO::FETCH_ASSOC);
foreach ($vers as $v) {
    echo "  ID={$v['id_version_presupuesto']}  ppto={$v['id_presupuesto']}  v{$v['numero_version_presupuesto']}  estado={$v['estado_version_presupuesto']}\n";
}

// 2. Buscar un presupuesto con al menos 2 versiones
$pair = null;
$ppto_ids = array_unique(array_column($vers, 'id_presupuesto'));
foreach ($ppto_ids as $pid) {
    $del_ppto = array_filter($vers, fn($v) => $v['id_presupuesto'] == $pid);
    if (count($del_ppto) >= 2) {
        $pair = array_values($del_ppto);
        break;
    }
}

if (!$pair) {
    echo "\n❌ No hay presupuesto con 2 versiones para comparar\n";
    exit;
}

$idA = $pair[0]['id_version_presupuesto'];
$idB = $pair[1]['id_version_presupuesto'];
echo "\n--- Comparando versión A=$idA vs B=$idB (ppto={$pair[0]['id_presupuesto']}) ---\n";

// 3. Probar las 3 queries directamente
echo "\n--- Query AÑADIDAS ---\n";
try {
    $sql = "SELECT lb.id_linea_ppto, lb.id_articulo, lb.descripcion_linea_ppto
            FROM linea_presupuesto lb
            LEFT JOIN linea_presupuesto la
                ON la.id_articulo = lb.id_articulo
                AND la.id_version_presupuesto = ?
                AND la.activo_linea_ppto = 1
            WHERE lb.id_version_presupuesto = ?
            AND lb.activo_linea_ppto = 1
            AND la.id_linea_ppto IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idA, $idB]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "  OK — " . count($rows) . " filas\n";
} catch (PDOException $e) {
    echo "  ❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- Query ELIMINADAS ---\n";
try {
    $sql = "SELECT la.id_linea_ppto, la.id_articulo, la.descripcion_linea_ppto
            FROM linea_presupuesto la
            LEFT JOIN linea_presupuesto lb
                ON lb.id_articulo = la.id_articulo
                AND lb.id_version_presupuesto = ?
                AND lb.activo_linea_ppto = 1
            WHERE la.id_version_presupuesto = ?
            AND la.activo_linea_ppto = 1
            AND lb.id_linea_ppto IS NULL";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idB, $idA]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "  OK — " . count($rows) . " filas\n";
} catch (PDOException $e) {
    echo "  ❌ ERROR: " . $e->getMessage() . "\n";
}

echo "\n--- Query MODIFICADAS ---\n";
try {
    $sql = "SELECT lb.id_linea_ppto, lb.descripcion_linea_ppto,
                   la.cantidad_linea_ppto as cantidad_antigua, lb.cantidad_linea_ppto,
                   la.precio_unitario_linea_ppto as precio_antiguo, lb.precio_unitario_linea_ppto
            FROM linea_presupuesto la
            INNER JOIN linea_presupuesto lb
                ON lb.id_articulo = la.id_articulo
                AND lb.id_version_presupuesto = ?
                AND lb.activo_linea_ppto = 1
            WHERE la.id_version_presupuesto = ?
            AND la.activo_linea_ppto = 1
            AND (
                la.cantidad_linea_ppto != lb.cantidad_linea_ppto OR
                la.precio_unitario_linea_ppto != lb.precio_unitario_linea_ppto OR
                la.descuento_linea_ppto != lb.descuento_linea_ppto
            )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$idB, $idA]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo "  OK — " . count($rows) . " filas\n";
} catch (PDOException $e) {
    echo "  ❌ ERROR: " . $e->getMessage() . "\n";
}

// 4. Llamar al modelo directamente
echo "\n--- Llamada al modelo comparar_versiones($idA, $idB) ---\n";
$modelo = new PresupuestoVersion();
$resultado = $modelo->comparar_versiones($idA, $idB);
echo "Claves devueltas: " . implode(', ', array_keys($resultado)) . "\n";
echo "resumen: " . json_encode($resultado['resumen']) . "\n";

// 5. Simular respuesta JSON exacta del controller
echo "\n--- JSON que recibiría el frontend ---\n";
$json = json_encode(['success' => true, 'data' => $resultado], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
echo htmlspecialchars(substr($json, 0, 800)) . "\n";

echo "\n=== FIN DIAGNÓSTICO ===\n";
echo '</pre>';
