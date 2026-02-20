<?php
/**
 * Migración: 20260219_fix_vista_presupuesto_peso_join
 * ====================================================
 * Corrige el JOIN de vista_presupuesto_completa con vista_presupuesto_peso.
 *
 * PROBLEMA: El LEFT JOIN usaba p.id_presupuesto = vpp.id_presupuesto.
 *   vista_presupuesto_peso devuelve UNA FILA POR VERSIÓN, así que un
 *   presupuesto con v1+v2 generaba 2 filas en la vista → duplicado en DataTable.
 *
 * SOLUCIÓN: Unir por pv.id_version_presupuesto = vpp.id_version_presupuesto,
 *   ya que pv está filtrado a version_actual, garantizando 1 fila por presupuesto.
 *
 * ¡ELIMINAR este archivo tras ejecutarlo!
 */
require_once '../../config/conexion.php';

header('Content-Type: text/html; charset=utf-8');
echo '<!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><title>Fix vista_presupuesto_completa</title>
<style>
  body { font-family:monospace; padding:20px; background:#1e1e1e; color:#d4d4d4; }
  h2   { color:#569cd6; }
  h3   { color:#9cdcfe; }
  .ok  { color:#4ec9b0; }
  .err { color:#f44747; font-weight:bold; }
  .warn{ color:#ce9178; }
  table{ border-collapse:collapse; margin-top:10px; }
  th   { background:#2d2d2d; color:#9cdcfe; padding:5px 10px; border:1px solid #444; }
  td   { padding:4px 10px; border:1px solid #333; }
</style></head><body>';
echo '<h2>🔧 Fix: vista_presupuesto_completa — JOIN a vista_presupuesto_peso</h2><pre>';

$sql_view = "CREATE OR REPLACE VIEW `vista_presupuesto_completa` AS
SELECT
  `p`.`id_presupuesto`, `p`.`numero_presupuesto`, `p`.`version_actual_presupuesto`,
  `p`.`fecha_presupuesto`, `p`.`fecha_validez_presupuesto`,
  `p`.`fecha_inicio_evento_presupuesto`, `p`.`fecha_fin_evento_presupuesto`,
  `p`.`numero_pedido_cliente_presupuesto`, `p`.`aplicar_coeficientes_presupuesto`,
  `p`.`descuento_presupuesto`, `p`.`nombre_evento_presupuesto`,
  `p`.`direccion_evento_presupuesto`, `p`.`poblacion_evento_presupuesto`,
  `p`.`cp_evento_presupuesto`, `p`.`provincia_evento_presupuesto`,
  `p`.`observaciones_cabecera_presupuesto`, `p`.`observaciones_pie_presupuesto`,
  `p`.`observaciones_cabecera_ingles_presupuesto`, `p`.`observaciones_pie_ingles_presupuesto`,
  `p`.`mostrar_obs_familias_presupuesto`, `p`.`mostrar_obs_articulos_presupuesto`,
  `p`.`observaciones_internas_presupuesto`, `p`.`activo_presupuesto`,
  `p`.`created_at_presupuesto`, `p`.`updated_at_presupuesto`,
  `c`.`id_cliente`, `c`.`codigo_cliente`, `c`.`nombre_cliente`, `c`.`nif_cliente`,
  `c`.`direccion_cliente`, `c`.`cp_cliente`, `c`.`poblacion_cliente`, `c`.`provincia_cliente`,
  `c`.`telefono_cliente`, `c`.`email_cliente`, `c`.`porcentaje_descuento_cliente`,
  `c`.`nombre_facturacion_cliente`, `c`.`direccion_facturacion_cliente`,
  `c`.`cp_facturacion_cliente`, `c`.`poblacion_facturacion_cliente`,
  `c`.`provincia_facturacion_cliente`, `c`.`exento_iva_cliente`,
  `c`.`justificacion_exencion_iva_cliente`,
  `cc`.`id_contacto_cliente`, `cc`.`nombre_contacto_cliente`,
  `cc`.`apellidos_contacto_cliente`, `cc`.`telefono_contacto_cliente`,
  `cc`.`email_contacto_cliente`,
  `ep`.`id_estado_ppto`, `ep`.`codigo_estado_ppto`, `ep`.`nombre_estado_ppto`,
  `ep`.`color_estado_ppto`, `ep`.`orden_estado_ppto`,
  `fp`.`id_pago` AS `id_forma_pago`, `fp`.`codigo_pago`, `fp`.`nombre_pago`,
  `fp`.`porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago`,
  `fp`.`porcentaje_final_pago`, `fp`.`dias_final_pago`, `fp`.`descuento_pago`,
  `mp`.`id_metodo_pago`, `mp`.`codigo_metodo_pago`, `mp`.`nombre_metodo_pago`,
  `mc`.`id_metodo` AS `id_metodo_contacto`, `mc`.`nombre` AS `nombre_metodo_contacto`,
  `c`.`id_forma_pago_habitual`, `fph`.`nombre_pago` AS `nombre_forma_pago_habitual_cliente`,
  CONCAT_WS(', ', `p`.`direccion_evento_presupuesto`,
    CONCAT(`p`.`cp_evento_presupuesto`, ' ', `p`.`poblacion_evento_presupuesto`),
    `p`.`provincia_evento_presupuesto`) AS `direccion_completa_evento_presupuesto`,
  CONCAT_WS(', ', `c`.`direccion_cliente`,
    CONCAT(`c`.`cp_cliente`, ' ', `c`.`poblacion_cliente`),
    `c`.`provincia_cliente`) AS `direccion_completa_cliente`,
  CONCAT_WS(', ', `c`.`direccion_facturacion_cliente`,
    CONCAT(`c`.`cp_facturacion_cliente`, ' ', `c`.`poblacion_facturacion_cliente`),
    `c`.`provincia_facturacion_cliente`) AS `direccion_facturacion_completa_cliente`,
  CONCAT_WS(' ', `cc`.`nombre_contacto_cliente`, `cc`.`apellidos_contacto_cliente`)
    AS `nombre_completo_contacto`,
  (TO_DAYS(`p`.`fecha_validez_presupuesto`) - TO_DAYS(CURDATE())) AS `dias_validez_restantes`,
  (CASE
    WHEN `p`.`fecha_validez_presupuesto` IS NULL THEN 'Sin fecha de validez'
    WHEN `p`.`fecha_validez_presupuesto` < CURDATE() THEN 'Caducado'
    WHEN `p`.`fecha_validez_presupuesto` = CURDATE() THEN 'Caduca hoy'
    WHEN (TO_DAYS(`p`.`fecha_validez_presupuesto`) - TO_DAYS(CURDATE())) <= 7 THEN 'Próximo a caducar'
    ELSE 'Vigente'
  END) AS `estado_validez_presupuesto`,
  ((TO_DAYS(`p`.`fecha_fin_evento_presupuesto`) - TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`)) + 1)
    AS `duracion_evento_dias`,
  (TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`) - TO_DAYS(CURDATE())) AS `dias_hasta_inicio_evento`,
  (TO_DAYS(`p`.`fecha_fin_evento_presupuesto`) - TO_DAYS(CURDATE())) AS `dias_hasta_fin_evento`,
  (CASE
    WHEN `p`.`fecha_inicio_evento_presupuesto` IS NULL THEN 'Sin fecha de evento'
    WHEN `p`.`fecha_inicio_evento_presupuesto` < CURDATE() AND `p`.`fecha_fin_evento_presupuesto` < CURDATE() THEN 'Evento finalizado'
    WHEN `p`.`fecha_inicio_evento_presupuesto` <= CURDATE() AND `p`.`fecha_fin_evento_presupuesto` >= CURDATE() THEN 'Evento en curso'
    WHEN `p`.`fecha_inicio_evento_presupuesto` = CURDATE() THEN 'Evento HOY'
    WHEN (TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`) - TO_DAYS(CURDATE())) = 1 THEN 'Evento MAÑANA'
    WHEN (TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`) - TO_DAYS(CURDATE())) <= 7 THEN 'Evento esta semana'
    WHEN (TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`) - TO_DAYS(CURDATE())) <= 30 THEN 'Evento este mes'
    ELSE 'Evento futuro'
  END) AS `estado_evento_presupuesto`,
  (CASE
    WHEN `p`.`fecha_inicio_evento_presupuesto` IS NULL THEN 'Sin prioridad'
    WHEN `p`.`fecha_inicio_evento_presupuesto` = CURDATE() THEN 'HOY'
    WHEN (TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`) - TO_DAYS(CURDATE())) = 1 THEN 'MAÑANA'
    WHEN (TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`) - TO_DAYS(CURDATE())) <= 7 THEN 'Esta semana'
    WHEN (TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`) - TO_DAYS(CURDATE())) <= 15 THEN 'Próximo'
    WHEN (TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`) - TO_DAYS(CURDATE())) <= 30 THEN 'Este mes'
    ELSE 'Futuro'
  END) AS `prioridad_presupuesto`,
  (CASE
    WHEN `fp`.`id_pago` IS NULL THEN 'Sin forma de pago'
    WHEN `fp`.`porcentaje_anticipo_pago` = 100.00 THEN 'Pago único'
    WHEN `fp`.`porcentaje_anticipo_pago` < 100.00 THEN 'Pago fraccionado'
    ELSE 'Sin forma de pago'
  END) AS `tipo_pago_presupuesto`,
  (CASE
    WHEN `fp`.`id_pago` IS NULL THEN 'Sin forma de pago asignada'
    WHEN `fp`.`porcentaje_anticipo_pago` = 100.00 THEN
      CONCAT(`mp`.`nombre_metodo_pago`, ' - ', `fp`.`nombre_pago`,
        CASE WHEN `fp`.`descuento_pago` > 0 THEN CONCAT(' (Dto: ', `fp`.`descuento_pago`, '%)') ELSE '' END)
    ELSE CONCAT(`mp`.`nombre_metodo_pago`, ' - ', `fp`.`porcentaje_anticipo_pago`, '% + ', `fp`.`porcentaje_final_pago`, '%')
  END) AS `descripcion_completa_forma_pago`,
  (CASE
    WHEN `fp`.`dias_anticipo_pago` = 0 THEN `p`.`fecha_presupuesto`
    ELSE (`p`.`fecha_presupuesto` + INTERVAL `fp`.`dias_anticipo_pago` DAY)
  END) AS `fecha_vencimiento_anticipo`,
  (CASE
    WHEN `fp`.`dias_final_pago` = 0 AND `p`.`fecha_fin_evento_presupuesto` IS NOT NULL THEN `p`.`fecha_fin_evento_presupuesto`
    WHEN `fp`.`dias_final_pago` > 0 THEN (`p`.`fecha_presupuesto` + INTERVAL `fp`.`dias_final_pago` DAY)
    WHEN `fp`.`dias_final_pago` < 0 AND `p`.`fecha_inicio_evento_presupuesto` IS NOT NULL
      THEN (`p`.`fecha_inicio_evento_presupuesto` + INTERVAL `fp`.`dias_final_pago` DAY)
    ELSE NULL
  END) AS `fecha_vencimiento_final`,
  (CASE
    WHEN `p`.`descuento_presupuesto` = `c`.`porcentaje_descuento_cliente` THEN 'Igual al habitual'
    WHEN `p`.`descuento_presupuesto` > `c`.`porcentaje_descuento_cliente` THEN 'Mayor al habitual'
    WHEN `p`.`descuento_presupuesto` < `c`.`porcentaje_descuento_cliente` THEN 'Menor al habitual'
    ELSE 'Sin comparar'
  END) AS `comparacion_descuento`,
  (CASE
    WHEN `p`.`descuento_presupuesto` = 0.00 THEN 'Sin descuento'
    WHEN `p`.`descuento_presupuesto` > 0.00 AND `p`.`descuento_presupuesto` <= 5.00
      THEN CONCAT('Descuento bajo: ', `p`.`descuento_presupuesto`, '%')
    WHEN `p`.`descuento_presupuesto` > 5.00 AND `p`.`descuento_presupuesto` <= 15.00
      THEN CONCAT('Descuento medio: ', `p`.`descuento_presupuesto`, '%')
    WHEN `p`.`descuento_presupuesto` > 15.00
      THEN CONCAT('Descuento alto: ', `p`.`descuento_presupuesto`, '%')
    ELSE 'Sin descuento'
  END) AS `estado_descuento_presupuesto`,
  (CASE WHEN `p`.`descuento_presupuesto` > 0.00 THEN TRUE ELSE FALSE END) AS `aplica_descuento_presupuesto`,
  (`p`.`descuento_presupuesto` - `c`.`porcentaje_descuento_cliente`) AS `diferencia_descuento`,
  (CASE WHEN `c`.`direccion_facturacion_cliente` IS NOT NULL THEN TRUE ELSE FALSE END)
    AS `tiene_direccion_facturacion_diferente`,
  (TO_DAYS(CURDATE()) - TO_DAYS(`p`.`fecha_presupuesto`)) AS `dias_desde_emision`,
  `pv`.`id_version_presupuesto`     AS `id_version_actual`,
  `pv`.`numero_version_presupuesto` AS `numero_version_actual`,
  `pv`.`estado_version_presupuesto` AS `estado_version_actual`,
  `pv`.`fecha_creacion_version`     AS `fecha_creacion_version_actual`,
  (CASE
    WHEN `ep`.`codigo_estado_ppto` = 'CANC' THEN 'Cancelado'
    WHEN `ep`.`codigo_estado_ppto` = 'FACT' THEN 'Facturado'
    WHEN `p`.`fecha_validez_presupuesto` < CURDATE()
         AND `ep`.`codigo_estado_ppto` NOT IN ('ACEP','RECH','CANC','FACT') THEN 'Caducado'
    WHEN `p`.`fecha_inicio_evento_presupuesto` < CURDATE()
         AND `p`.`fecha_fin_evento_presupuesto` < CURDATE() THEN 'Evento finalizado'
    WHEN `p`.`fecha_inicio_evento_presupuesto` <= CURDATE()
         AND `p`.`fecha_fin_evento_presupuesto` >= CURDATE() THEN 'Evento en curso'
    WHEN `ep`.`codigo_estado_ppto` = 'ACEP' THEN 'Aceptado - Pendiente evento'
    ELSE `ep`.`nombre_estado_ppto`
  END) AS `estado_general_presupuesto`,
  `vpp`.`peso_total_kg`, `vpp`.`peso_articulos_normales_kg`, `vpp`.`peso_kits_kg`,
  `vpp`.`total_lineas`, `vpp`.`lineas_con_peso`, `vpp`.`lineas_sin_peso`,
  `vpp`.`porcentaje_completitud` AS `porcentaje_completitud_peso`

FROM `presupuesto` `p`
JOIN  `cliente`               `c`   ON `p`.`id_cliente`             = `c`.`id_cliente`
LEFT  JOIN `contacto_cliente` `cc`  ON `p`.`id_contacto_cliente`    = `cc`.`id_contacto_cliente`
JOIN  `estado_presupuesto`    `ep`  ON `p`.`id_estado_ppto`         = `ep`.`id_estado_ppto`
LEFT  JOIN `forma_pago`       `fp`  ON `p`.`id_forma_pago`          = `fp`.`id_pago`
LEFT  JOIN `metodo_pago`      `mp`  ON `fp`.`id_metodo_pago`        = `mp`.`id_metodo_pago`
LEFT  JOIN `metodos_contacto` `mc`  ON `p`.`id_metodo`              = `mc`.`id_metodo`
LEFT  JOIN `forma_pago`       `fph` ON `c`.`id_forma_pago_habitual` = `fph`.`id_pago`
LEFT  JOIN `presupuesto_version` `pv`
        ON `p`.`id_presupuesto` = `pv`.`id_presupuesto`
       AND `pv`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`
LEFT  JOIN `vista_presupuesto_peso` `vpp`
        ON `pv`.`id_version_presupuesto` = `vpp`.`id_version_presupuesto`";

try {
    $pdo = (new Conexion())->getConexion();

    // ── Verificación previa: cuántas filas devuelve la vista actualmente
    $antes = $pdo->query("SELECT COUNT(*) AS total FROM vista_presupuesto_completa WHERE activo_presupuesto = 1")->fetch(PDO::FETCH_ASSOC);
    $ptosAntes = $pdo->query("SELECT COUNT(DISTINCT id_presupuesto) AS total FROM presupuesto WHERE activo_presupuesto = 1")->fetch(PDO::FETCH_ASSOC);
    echo "<span class='warn'>Antes — filas en vista: {$antes['total']} | presupuestos únicos activos: {$ptosAntes['total']}</span>\n";
    if ($antes['total'] > $ptosAntes['total']) {
        echo "<span class='err'>⚠️  Hay duplicados: " . ($antes['total'] - $ptosAntes['total']) . " filas extra</span>\n";
    }

    // ── Aplicar el fix
    $pdo->exec($sql_view);
    echo "<span class='ok'>✅ Vista recreada correctamente con el JOIN corregido</span>\n\n";

    // ── Verificación posterior
    $despues = $pdo->query("SELECT COUNT(*) AS total FROM vista_presupuesto_completa WHERE activo_presupuesto = 1")->fetch(PDO::FETCH_ASSOC);
    echo "<span class='ok'>Después — filas en vista: {$despues['total']} | presupuestos únicos: {$ptosAntes['total']}</span>\n";
    if ($despues['total'] == $ptosAntes['total']) {
        echo "<span class='ok'><b>✅ Sin duplicados. Filas = presupuestos activos. Fix aplicado correctamente.</b></span>\n";
    } else {
        echo "<span class='err'>⚠️  Todavía hay diferencias. Revisar si hay otro JOIN problemático.</span>\n";
    }

    // ── Top de presupuestos por número de filas (para detectar duplicados residuales)
    echo "\n<b>Verificación por presupuesto (ordenado por filas DESC):</b>\n";
    $rows = $pdo->query("
        SELECT id_presupuesto, numero_presupuesto, COUNT(*) AS n_filas
        FROM vista_presupuesto_completa
        WHERE activo_presupuesto = 1
        GROUP BY id_presupuesto, numero_presupuesto
        HAVING n_filas > 1
        LIMIT 10
    ")->fetchAll(PDO::FETCH_ASSOC);

    if (empty($rows)) {
        echo "<span class='ok'>✅ Ningún presupuesto con filas duplicadas.</span>\n";
    } else {
        echo "<span class='err'>Presupuestos con duplicados residuales:</span>\n";
        foreach ($rows as $r) {
            echo "  ID {$r['id_presupuesto']} | {$r['numero_presupuesto']} → {$r['n_filas']} filas\n";
        }
    }

} catch (Exception $e) {
    echo '<span class="err">❌ ERROR: ' . htmlspecialchars($e->getMessage()) . '</span>';
}

echo '</pre>';
echo '<br><span class="err"><b>⚠️ ELIMINA este archivo tras ejecutarlo: config/test/run_migration_fix_vista_peso.php</b></span>';
echo '</body></html>';
