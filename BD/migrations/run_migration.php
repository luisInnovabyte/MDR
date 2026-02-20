<?php
$config = json_decode(file_get_contents(__DIR__ . '/../../config/conexion.json'), true);
$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
$pdo = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// Verificar si la columna ya existe
$cols = $pdo->query('SHOW COLUMNS FROM estado_presupuesto LIKE "es_sistema_estado_ppto"')->fetchAll();
if (count($cols) === 0) {
    $pdo->exec("ALTER TABLE estado_presupuesto 
        ADD COLUMN es_sistema_estado_ppto TINYINT(1) NOT NULL DEFAULT 0 
        COMMENT 'Gestionado automaticamente por el sistema: 1=sistema, 0=usuario' 
        AFTER activo_estado_ppto");
    echo "OK: Columna es_sistema_estado_ppto creada." . PHP_EOL;
} else {
    echo "INFO: Columna ya existe, se omite ALTER." . PHP_EOL;
}

$n = $pdo->exec('UPDATE estado_presupuesto SET es_sistema_estado_ppto = 1 WHERE id_estado_ppto IN (1, 3, 5, 8)');
echo "OK: {$n} estados marcados como sistema." . PHP_EOL;

echo PHP_EOL . "Estado final:" . PHP_EOL;
$rows = $pdo->query('SELECT id_estado_ppto, codigo_estado_ppto, nombre_estado_ppto, es_sistema_estado_ppto FROM estado_presupuesto ORDER BY id_estado_ppto')->fetchAll(PDO::FETCH_ASSOC);
printf("%-5s %-15s %-30s %s\n", "ID", "CODIGO", "NOMBRE", "ES_SISTEMA");
printf("%-5s %-15s %-30s %s\n", "---", "------", "------", "----------");
foreach ($rows as $r) {
    printf("%-5s %-15s %-30s %s\n",
        $r['id_estado_ppto'],
        $r['codigo_estado_ppto'],
        $r['nombre_estado_ppto'],
        $r['es_sistema_estado_ppto'] ? 'SI (bloqueado)' : 'no'
    );
}
