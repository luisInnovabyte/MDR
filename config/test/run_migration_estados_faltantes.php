<?php
/**
 * Migración: insertar estados de presupuesto faltantes (PROC y RECH)
 * Abrir en: http://localhost/MDR/config/test/run_migration_estados_faltantes.php
 */
require_once '../conexion.php';
$pdo = (new Conexion())->getConexion();

echo '<pre>';
echo "=== MIGRACIÓN: estados_presupuesto faltantes ===\n\n";

$estados_a_insertar = [
    [
        'id_estado_ppto'          => 2,
        'codigo_estado_ppto'      => 'PROC',
        'nombre_estado_ppto'      => 'En Proceso',
        'color_estado_ppto'       => '#17a2b8',
        'orden_estado_ppto'       => 10,
        'observaciones_estado_ppto' => 'Presupuesto en proceso de elaboración',
    ],
    [
        'id_estado_ppto'          => 4,
        'codigo_estado_ppto'      => 'RECH',
        'nombre_estado_ppto'      => 'Rechazado',
        'color_estado_ppto'       => '#dc3545',
        'orden_estado_ppto'       => 50,
        'observaciones_estado_ppto' => 'Presupuesto rechazado por el cliente',
    ],
];

foreach ($estados_a_insertar as $e) {
    // Comprobar si ya existe por código (aunque tenga otro id)
    $check = $pdo->prepare("SELECT id_estado_ppto FROM estado_presupuesto WHERE codigo_estado_ppto = ?");
    $check->execute([$e['codigo_estado_ppto']]);
    $existente = $check->fetch(PDO::FETCH_ASSOC);

    if ($existente) {
        echo "⏭️  '{$e['codigo_estado_ppto']}' ya existe (id={$existente['id_estado_ppto']}), se omite\n";
        continue;
    }

    try {
        $stmt = $pdo->prepare(
            "INSERT INTO estado_presupuesto 
                (id_estado_ppto, codigo_estado_ppto, nombre_estado_ppto, color_estado_ppto, 
                 orden_estado_ppto, observaciones_estado_ppto, activo_estado_ppto)
             VALUES (?, ?, ?, ?, ?, ?, 1)"
        );
        $stmt->execute([
            $e['id_estado_ppto'],
            $e['codigo_estado_ppto'],
            $e['nombre_estado_ppto'],
            $e['color_estado_ppto'],
            $e['orden_estado_ppto'],
            $e['observaciones_estado_ppto'],
        ]);
        echo "✅ Insertado: id={$e['id_estado_ppto']}  codigo='{$e['codigo_estado_ppto']}'  nombre='{$e['nombre_estado_ppto']}'\n";
    } catch (PDOException $ex) {
        // Si falla por ID duplicado, insertar sin forzar el id
        if ($ex->getCode() == 23000) {
            try {
                $stmt2 = $pdo->prepare(
                    "INSERT INTO estado_presupuesto 
                        (codigo_estado_ppto, nombre_estado_ppto, color_estado_ppto, 
                         orden_estado_ppto, observaciones_estado_ppto, activo_estado_ppto)
                     VALUES (?, ?, ?, ?, ?, 1)"
                );
                $stmt2->execute([
                    $e['codigo_estado_ppto'],
                    $e['nombre_estado_ppto'],
                    $e['color_estado_ppto'],
                    $e['orden_estado_ppto'],
                    $e['observaciones_estado_ppto'],
                ]);
                $nuevo_id = $pdo->lastInsertId();
                echo "✅ Insertado (auto-id=$nuevo_id): codigo='{$e['codigo_estado_ppto']}'  nombre='{$e['nombre_estado_ppto']}'\n";
            } catch (PDOException $ex2) {
                echo "❌ Error al insertar '{$e['codigo_estado_ppto']}': " . $ex2->getMessage() . "\n";
            }
        } else {
            echo "❌ Error al insertar '{$e['codigo_estado_ppto']}': " . $ex->getMessage() . "\n";
        }
    }
}

echo "\n--- Estado final de estado_presupuesto ---\n";
$rows = $pdo->query("SELECT id_estado_ppto, codigo_estado_ppto, nombre_estado_ppto, color_estado_ppto FROM estado_presupuesto ORDER BY orden_estado_ppto")->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    echo "  id={$r['id_estado_ppto']}  codigo='{$r['codigo_estado_ppto']}'  nombre='{$r['nombre_estado_ppto']}'  color={$r['color_estado_ppto']}\n";
}

echo "\n=== MIGRACIÓN COMPLETADA ===\n";
echo '</pre>';
