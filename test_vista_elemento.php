<?php
require_once "config/conexion.php";

$conexion = (new Conexion())->getConexion();

// Ver definición de la vista
$sql = "SHOW CREATE VIEW vista_elemento_completa";
$stmt = $conexion->prepare($sql);
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

echo "=== DEFINICIÓN DE vista_elemento_completa ===\n\n";
echo $result['Create View'] ?? 'No encontrada';
echo "\n\n";
?>
