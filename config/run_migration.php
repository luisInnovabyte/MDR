<?php
$config = json_decode(file_get_contents(__DIR__ . '/conexion.json'), true);
$pdo = new PDO(
    'mysql:host=' . $config['host'] . ';port=' . $config['port'] . ';dbname=' . $config['database'] . ';charset=utf8mb4',
    $config['user'],
    $config['password'],
    [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_spanish_ci",
    ]
);

$file = $argv[1] ?? null;
if (!$file || !file_exists($file)) {
    echo "ERROR: fichero no encontrado: $file\n";
    exit(1);
}

$sql = file_get_contents($file);
$pdo->exec($sql);
echo "OK: migration aplicada correctamente.\n";
