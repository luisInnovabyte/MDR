<?php
require_once __DIR__ . '/../../config/conexion.php';

$conn = (new Conexion())->getConexion();

echo "=== ESTADO P-00005 EN BD ===\n";
$stmt = $conn->query("SELECT p.id_presupuesto, p.numero_presupuesto, 
    p.id_estado_ppto, p.estado_general_presupuesto, p.version_actual_presupuesto,
    ep.nombre_estado_ppto, ep.codigo_estado_ppto 
    FROM presupuesto p 
    LEFT JOIN estado_presupuesto ep ON ep.id_estado_ppto = p.id_estado_ppto 
    WHERE p.numero_presupuesto LIKE 'P-00005%' LIMIT 3");
foreach ($stmt->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\n=== VERSIONES DE P-00005 ===\n";
$stmt2 = $conn->query("SELECT pv.id_version_presupuesto, pv.numero_version_presupuesto, 
    pv.estado_version_presupuesto 
    FROM presupuesto_version pv 
    INNER JOIN presupuesto p ON p.id_presupuesto = pv.id_presupuesto 
    WHERE p.numero_presupuesto LIKE 'P-00005%' 
    ORDER BY pv.numero_version_presupuesto");
foreach ($stmt2->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
}

echo "\n=== TRIGGERS ACTIVOS EN presupuesto_version ===\n";
$stmt3 = $conn->query("SHOW TRIGGERS LIKE 'presupuesto_version'");
foreach ($stmt3->fetchAll(PDO::FETCH_ASSOC) as $t) {
    echo $t['Trigger'] . " | " . substr($t['Statement'], 0, 120) . "\n";
}

echo "\n=== CATÁLOGO estado_presupuesto ===\n";
$stmt4 = $conn->query("SELECT id_estado_ppto, codigo_estado_ppto, activo_estado_ppto FROM estado_presupuesto ORDER BY orden_estado_ppto");
foreach ($stmt4->fetchAll(PDO::FETCH_ASSOC) as $r) {
    echo json_encode($r) . "\n";
}
