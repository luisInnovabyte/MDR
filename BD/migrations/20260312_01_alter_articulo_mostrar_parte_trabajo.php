<?php
/**
 * MIGRACIÓN: mostrar_parte_trabajo_articulo
 * Fecha: 2026-03-12
 * Descripción:
 *   1. Añade columna mostrar_parte_trabajo_articulo TINYINT(1) NOT NULL DEFAULT 1
 *      a la tabla articulo.
 *   2. Actualiza la vista vista_articulo_completa para incluir el nuevo campo.
 *
 * Uso: ejecutar desde CLI o navegando a BD/migrations/ via el servidor web.
 *   php 20260312_01_alter_articulo_mostrar_parte_trabajo.php
 */

$config = json_decode(file_get_contents(__DIR__ . '/../../config/conexion.json'), true);
$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
$pdo = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

// =====================================================================
// PASO 1: Añadir columna a tabla articulo (idempotente)
// =====================================================================
$cols = $pdo->query("SHOW COLUMNS FROM articulo LIKE 'mostrar_parte_trabajo_articulo'")->fetchAll();
if (count($cols) === 0) {
    $pdo->exec("ALTER TABLE articulo
        ADD COLUMN mostrar_parte_trabajo_articulo TINYINT(1) NOT NULL DEFAULT 1
        COMMENT 'Mostrar en parte de trabajo tecnico y picking (0=ocultar, 1=mostrar)'
        AFTER precio_editable_articulo");
    echo "OK: Columna mostrar_parte_trabajo_articulo creada en tabla articulo." . PHP_EOL;
} else {
    echo "INFO: Columna ya existe en articulo, se omite ALTER TABLE." . PHP_EOL;
}

// =====================================================================
// PASO 2: Actualizar vista_articulo_completa
// Añadido: mostrar_parte_trabajo_articulo después de precio_editable_articulo
// =====================================================================
$pdo->exec("CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER
VIEW `vista_articulo_completa` AS
SELECT
    `a`.`id_articulo`                          AS `id_articulo`,
    `a`.`id_familia`                           AS `id_familia`,
    `a`.`id_unidad`                            AS `id_unidad`,
    `a`.`id_impuesto`                          AS `id_impuesto`,
    `a`.`codigo_articulo`                      AS `codigo_articulo`,
    `a`.`nombre_articulo`                      AS `nombre_articulo`,
    `a`.`name_articulo`                        AS `name_articulo`,
    `a`.`imagen_articulo`                      AS `imagen_articulo`,
    `a`.`precio_alquiler_articulo`             AS `precio_alquiler_articulo`,
    `a`.`coeficiente_articulo`                 AS `coeficiente_articulo`,
    `a`.`es_kit_articulo`                      AS `es_kit_articulo`,
    `a`.`control_total_articulo`               AS `control_total_articulo`,
    `a`.`no_facturar_articulo`                 AS `no_facturar_articulo`,
    `a`.`notas_presupuesto_articulo`           AS `notas_presupuesto_articulo`,
    `a`.`notes_budget_articulo`                AS `notes_budget_articulo`,
    `a`.`orden_obs_articulo`                   AS `orden_obs_articulo`,
    `a`.`observaciones_articulo`               AS `observaciones_articulo`,
    `a`.`permitir_descuentos_articulo`         AS `permitir_descuentos_articulo`,
    `a`.`precio_editable_articulo`             AS `precio_editable_articulo`,
    `a`.`mostrar_parte_trabajo_articulo`       AS `mostrar_parte_trabajo_articulo`,
    `a`.`activo_articulo`                      AS `activo_articulo`,
    `a`.`created_at_articulo`                  AS `created_at_articulo`,
    `a`.`updated_at_articulo`                  AS `updated_at_articulo`,
    `f`.`id_grupo`                             AS `id_grupo`,
    `f`.`codigo_familia`                       AS `codigo_familia`,
    `f`.`nombre_familia`                       AS `nombre_familia`,
    `f`.`name_familia`                         AS `name_familia`,
    `f`.`descr_familia`                        AS `descr_familia`,
    `f`.`imagen_familia`                       AS `imagen_familia`,
    `f`.`coeficiente_familia`                  AS `coeficiente_familia`,
    `f`.`observaciones_presupuesto_familia`    AS `observaciones_presupuesto_familia`,
    `f`.`observations_budget_familia`          AS `observations_budget_familia`,
    `f`.`orden_obs_familia`                    AS `orden_obs_familia`,
    `f`.`permite_descuento_familia`            AS `permite_descuento_familia`,
    `f`.`activo_familia`                       AS `activo_familia_relacionada`,
    `imp`.`tipo_impuesto`                      AS `tipo_impuesto`,
    `imp`.`tasa_impuesto`                      AS `tasa_impuesto`,
    `imp`.`descr_impuesto`                     AS `descr_impuesto`,
    `imp`.`activo_impuesto`                    AS `activo_impuesto_relacionado`,
    `u`.`nombre_unidad`                        AS `nombre_unidad`,
    `u`.`name_unidad`                          AS `name_unidad`,
    `u`.`descr_unidad`                         AS `descr_unidad`,
    `u`.`simbolo_unidad`                       AS `simbolo_unidad`,
    `u`.`activo_unidad`                        AS `activo_unidad_relacionada`
FROM (((
    `articulo` `a`
    JOIN `familia` `f`          ON (`a`.`id_familia`  = `f`.`id_familia`)
)
LEFT JOIN `impuesto` `imp`      ON (`a`.`id_impuesto` = `imp`.`id_impuesto`)
)
LEFT JOIN `unidad_medida` `u`   ON (`a`.`id_unidad`   = `u`.`id_unidad`)
)");
echo "OK: Vista vista_articulo_completa actualizada con mostrar_parte_trabajo_articulo." . PHP_EOL;

// =====================================================================
// VERIFICACIÓN FINAL
// =====================================================================
echo PHP_EOL . "Estado final de articulo (últimos campos):" . PHP_EOL;
$campos = $pdo->query("SHOW COLUMNS FROM articulo")->fetchAll(PDO::FETCH_ASSOC);
foreach ($campos as $c) {
    if (in_array($c['Field'], ['permitir_descuentos_articulo', 'precio_editable_articulo', 'mostrar_parte_trabajo_articulo', 'id_impuesto', 'activo_articulo'])) {
        printf("  %-45s %-15s DEFAULT=%s\n", $c['Field'], $c['Type'], $c['Default'] ?? 'NULL');
    }
}

echo PHP_EOL . "Migración completada correctamente." . PHP_EOL;
