<?php
/**
 * SCRIPT DE RESET PARA PRUEBAS DE VERSIONES
 * ==========================================
 * Vuelve TODOS los presupuestos a su estado original:
 *   - Solo queda la versión 1 (en estado 'borrador')
 *   - Se eliminan todas las versiones 2, 3, 4... y sus líneas
 *   - El presupuesto queda en version_actual=1, estado=borrador, id_estado_ppto=1
 *
 * ⚠️  SOLO PARA ENTORNO DE PRUEBAS
 * ⚠️  NO ejecutar en producción con datos reales
 */
require_once '../../config/conexion.php';

header('Content-Type: text/html; charset=utf-8');

$css = '
<style>
  body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
  h1   { color: #569cd6; border-bottom: 1px solid #444; padding-bottom: 10px; }
  h2   { color: #9cdcfe; margin-top: 30px; }
  .ok  { color: #4ec9b0; }
  .warn{ color: #ce9178; }
  .err { color: #f44747; font-weight: bold; }
  .dim { color: #808080; }
  table{ border-collapse: collapse; margin-top:10px; min-width:700px; }
  th   { background:#2d2d2d; color:#9cdcfe; padding:6px 12px; border:1px solid #444; text-align:left; }
  td   { padding:5px 12px; border:1px solid #333; }
  tr:hover td { background:#2a2a2a; }
  .badge-v1 { background:#0e639c; color:#fff; padding:2px 6px; border-radius:3px; font-size:11px; }
  .badge-ok { background:#1a7c5c; color:#fff; padding:2px 6px; border-radius:3px; font-size:11px; }
  .sep  { border-top:2px solid #444; margin:20px 0; }
  .box  { background:#252526; border:1px solid #444; padding:15px; border-radius:4px; margin:15px 0; }
  .btn  { display:inline-block; margin-top:20px; padding:10px 24px; background:#0e639c; color:#fff;
          text-decoration:none; border-radius:4px; font-family:sans-serif; font-size:14px; }
  .btn:hover { background:#1177bb; }
  .confirm-box { background:#3a1010; border:1px solid #f44747; padding:15px; border-radius:4px; margin:15px 0; }
</style>';

echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Reset Pruebas Versiones</title>{$css}</head><body>";
echo "<h1>⚙️  Reset de Versiones para Pruebas</h1>";

$ejecutar = isset($_GET['confirmar']) && $_GET['confirmar'] === 'si';

// ─── MODO VISTA PREVIA ──────────────────────────────────────────────────────
if (!$ejecutar) {
    try {
        $pdo = (new Conexion())->getConexion();
        $pdo->exec("SET time_zone = 'Europe/Madrid'");

        // Estado actual
        $rows = $pdo->query("
            SELECT
                p.id_presupuesto,
                p.numero_presupuesto,
                p.version_actual_presupuesto,
                p.estado_general_presupuesto,
                ep.nombre_estado_ppto,
                COUNT(pv.id_version_presupuesto)          AS total_versiones,
                SUM(pv.numero_version_presupuesto > 1)    AS versiones_extra,
                (SELECT COUNT(*) FROM linea_presupuesto lp
                 INNER JOIN presupuesto_version pv2
                         ON pv2.id_version_presupuesto = lp.id_version_presupuesto
                        AND pv2.id_presupuesto = p.id_presupuesto
                        AND pv2.numero_version_presupuesto > 1) AS lineas_extra
            FROM presupuesto p
            LEFT JOIN presupuesto_version pv ON pv.id_presupuesto = p.id_presupuesto AND pv.activo_version = 1
            LEFT JOIN estado_presupuesto ep  ON ep.id_estado_ppto = p.id_estado_ppto
            WHERE p.activo_presupuesto = 1
            GROUP BY p.id_presupuesto
            ORDER BY p.id_presupuesto
        ")->fetchAll(PDO::FETCH_ASSOC);

        echo "<div class='box'><b class='warn'>⚠️  Esto eliminará versiones extra y restablecerá cada presupuesto a su versión 1 en estado BORRADOR.</b>";
        echo "<br><span class='dim'>Las líneas de la versión 1 NO se modifican.</span></div>";

        echo "<h2>Estado actual de los presupuestos</h2>";
        echo "<table><tr><th>ID</th><th>Número</th><th>Versión actual</th><th>Estado general</th><th>Estado ppto</th><th>Versiones totales</th><th>Versiones a eliminar</th><th>Líneas a eliminar</th></tr>";
        $hayExtra = false;
        foreach ($rows as $r) {
            $extra = intval($r['versiones_extra']);
            $lineas = intval($r['lineas_extra']);
            if ($extra > 0) $hayExtra = true;
            $badge = $extra > 0 ? "<span style='color:#f44747'>🗑 {$extra}</span>" : "<span class='dim'>ninguna</span>";
            $lbadge = $lineas > 0 ? "<span style='color:#ce9178'>{$lineas}</span>" : "<span class='dim'>0</span>";
            echo "<tr>
                <td>{$r['id_presupuesto']}</td>
                <td>{$r['numero_presupuesto']}</td>
                <td><span class='badge-v1'>v{$r['version_actual_presupuesto']}</span></td>
                <td>{$r['estado_general_presupuesto']}</td>
                <td>{$r['nombre_estado_ppto']}</td>
                <td>{$r['total_versiones']}</td>
                <td>{$badge}</td>
                <td>{$lbadge}</td>
            </tr>";
        }
        echo "</table>";

        if (!$hayExtra) {
            echo "<div class='box'><span class='ok'>✅ Todos los presupuestos ya están en versión 1. No hay nada que resetear.</span></div>";
        } else {
            echo "<div class='confirm-box'>
                <b class='err'>⚠️  ATENCIÓN:</b> Esta operación es destructiva e irreversible en la BD de pruebas.<br>
                Solo se eliminarán versiones con <code>numero_version_presupuesto &gt; 1</code> y sus líneas.<br>
                La versión 1 y sus líneas permanecen intactas.<br><br>
                <a class='btn' href='?confirmar=si'>▶ Ejecutar Reset</a>
            </div>";
        }

    } catch (Exception $e) {
        echo "<p class='err'>ERROR: " . htmlspecialchars($e->getMessage()) . "</p>";
    }

    echo "</body></html>";
    exit;
}

// ─── MODO EJECUCIÓN ─────────────────────────────────────────────────────────
echo "<h2>▶ Ejecutando reset...</h2><div class='box'><pre>";

// DDL de triggers a recrear tras el reset
$ddl_trg_before_delete = "CREATE TRIGGER `trg_presupuesto_version_before_delete` BEFORE DELETE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE num_lineas INT;
    DECLARE tiene_hijos INT;
    SELECT COUNT(*) INTO num_lineas FROM linea_presupuesto WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    IF num_lineas > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene líneas asociadas. Elimine primero las líneas.';
    END IF;
    SELECT COUNT(*) INTO tiene_hijos FROM presupuesto_version WHERE version_padre_presupuesto = OLD.id_version_presupuesto;
    IF tiene_hijos > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene versiones hijas. Esto rompería la cadena genealógica.';
    END IF;
    IF OLD.estado_version_presupuesto != 'borrador' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar versiones que no están en borrador. El histórico debe ser inmutable.';
    END IF;
END";

$ddl_trg_linea_before_delete = "CREATE TRIGGER `trg_linea_presupuesto_before_delete` BEFORE DELETE ON `linea_presupuesto` FOR EACH ROW BEGIN
    DECLARE estado_version VARCHAR(20);
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar líneas de versiones que no están en borrador. El histórico debe permanecer inmutable.';
    END IF;
END";

$ddl_trg_linea_before_update = "CREATE TRIGGER `trg_linea_presupuesto_before_update` BEFORE UPDATE ON `linea_presupuesto` FOR EACH ROW BEGIN
    DECLARE estado_version VARCHAR(20);
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = NEW.id_version_presupuesto;
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden modificar líneas de versiones que no están en borrador. Para hacer cambios, cree una nueva versión.';
    END IF;
END";

$ddl_trg_validar_transicion = "CREATE TRIGGER `trg_version_validar_transicion_estado` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    IF OLD.estado_version_presupuesto != NEW.estado_version_presupuesto THEN
        IF OLD.estado_version_presupuesto = 'borrador'
           AND NEW.estado_version_presupuesto NOT IN ('enviado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: Desde borrador solo se puede pasar a enviado o cancelado. Workflow inválido.';
        END IF;
        IF OLD.estado_version_presupuesto = 'enviado'
           AND NEW.estado_version_presupuesto NOT IN ('aprobado', 'rechazado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: Desde enviado solo se puede pasar a aprobado, rechazado o cancelado. Workflow inválido.';
        END IF;
        IF OLD.estado_version_presupuesto IN ('aprobado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: No se puede cambiar el estado de versiones aprobadas o canceladas. Son estados finales e inmutables.';
        END IF;
        IF OLD.estado_version_presupuesto = 'rechazado'
           AND NEW.estado_version_presupuesto != 'cancelado' THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: Una versión rechazada solo puede cancelarse. Para nuevos intentos, cree una nueva versión.';
        END IF;
    END IF;
END";

try {
    $pdo = (new Conexion())->getConexion();
    $pdo->exec("SET time_zone = 'Europe/Madrid'");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // ── PASO 0: Desactivar triggers bloqueantes (DROP temporal)
    //   El trigger de validación de transición impide volver a 'borrador'
    //   El trigger de delete exige estado='borrador' antes de eliminar
    //   Para un reset de pruebas necesitamos saltarnos ambas restricciones
    $pdo->exec("DROP TRIGGER IF EXISTS `trg_linea_presupuesto_before_delete`");
    echo "<span class='warn'>⚡ Trigger trg_linea_presupuesto_before_delete desactivado temporalmente</span>\n";
    $pdo->exec("DROP TRIGGER IF EXISTS `trg_linea_presupuesto_before_update`");
    echo "<span class='warn'>⚡ Trigger trg_linea_presupuesto_before_update desactivado temporalmente</span>\n";
    $pdo->exec("DROP TRIGGER IF EXISTS `trg_version_validar_transicion_estado`");
    echo "<span class='warn'>⚡ Trigger trg_version_validar_transicion_estado desactivado temporalmente</span>\n";
    $pdo->exec("DROP TRIGGER IF EXISTS `trg_presupuesto_version_before_delete`");
    echo "<span class='warn'>⚡ Trigger trg_presupuesto_version_before_delete desactivado temporalmente</span>\n\n";

    // ── PASO 1: Obtener IDs de versiones extra (numero > 1), ordenadas de mayor a menor
    //   (para respetar jerarquía padre-hijo: eliminamos hijos antes que padres)
    $stmt = $pdo->query("
        SELECT id_version_presupuesto, id_presupuesto, numero_version_presupuesto, estado_version_presupuesto
        FROM presupuesto_version
        WHERE numero_version_presupuesto > 1
        ORDER BY numero_version_presupuesto DESC
    ");
    $versionesExtra = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (empty($versionesExtra)) {
        echo "<span class='ok'>✅ No hay versiones extra. Nada que limpiar.</span>\n";
    } else {
        echo "<span class='dim'>Versiones a eliminar: " . count($versionesExtra) . "</span>\n\n";

        foreach ($versionesExtra as $v) {
            $id  = $v['id_version_presupuesto'];
            $num = $v['numero_version_presupuesto'];
            $pid = $v['id_presupuesto'];

            // Desligar referencias hijas antes de eliminar
            $pdo->prepare("UPDATE presupuesto_version SET version_padre_presupuesto = NULL WHERE version_padre_presupuesto = ?")->execute([$id]);

            // Eliminar líneas de esta versión
            $stmtLineas = $pdo->prepare("DELETE FROM linea_presupuesto WHERE id_version_presupuesto = ?");
            $stmtLineas->execute([$id]);
            $nLineas = $stmtLineas->rowCount();
            if ($nLineas > 0) {
                echo "  <span class='dim'>→ Versión v{$num} (id={$id}): {$nLineas} líneas eliminadas</span>\n";
            }

            // Eliminar la versión (sin restricciones, trigger desactivado)
            $pdo->prepare("DELETE FROM presupuesto_version WHERE id_version_presupuesto = ?")->execute([$id]);
            echo "<span class='ok'>  ✅ Versión v{$num} (id={$id}, ppto={$pid}) [estado: {$v['estado_version_presupuesto']}] eliminada</span>\n";
        }
    }

    echo "\n";

    // ── PASO 2: Resetear versión 1 de cada presupuesto a estado 'borrador'
    $n = $pdo->exec("
        UPDATE presupuesto_version
        SET
            estado_version_presupuesto  = 'borrador',
            fecha_envio_version         = NULL,
            enviado_por_version         = NULL,
            fecha_aprobacion_version    = NULL,
            fecha_rechazo_version       = NULL,
            motivo_rechazo_version      = NULL,
            updated_at_version          = NOW()
        WHERE numero_version_presupuesto = 1
          AND activo_version = 1
    ");
    echo "<span class='ok'>✅ {$n} versiones 1 reseteadas a estado 'borrador'</span>\n";

    // ── PASO 3: Resetear cabecera de presupuesto
    $n2 = $pdo->exec("
        UPDATE presupuesto
        SET
            version_actual_presupuesto   = 1,
            estado_general_presupuesto   = 'borrador',
            id_estado_ppto               = (SELECT id_estado_ppto FROM estado_presupuesto WHERE codigo_estado_ppto = 'BORRADOR' LIMIT 1),
            updated_at_presupuesto       = NOW()
        WHERE activo_presupuesto = 1
    ");
    echo "<span class='ok'>✅ {$n2} presupuestos reseteados a versión 1 / BORRADOR</span>\n";

    // ── PASO 4: Mostrar estado final
    echo "\n<b>Estado final:</b>\n";
    $final = $pdo->query("
        SELECT
            p.id_presupuesto,
            p.numero_presupuesto,
            p.version_actual_presupuesto  AS v_actual,
            p.estado_general_presupuesto  AS estado_gen,
            ep.nombre_estado_ppto         AS estado_tab,
            COUNT(pv.id_version_presupuesto) AS num_versiones,
            (SELECT COUNT(*) FROM linea_presupuesto lp
             INNER JOIN presupuesto_version pv2 ON pv2.id_version_presupuesto = lp.id_version_presupuesto
             WHERE pv2.id_presupuesto = p.id_presupuesto) AS total_lineas
        FROM presupuesto p
        LEFT JOIN presupuesto_version pv ON pv.id_presupuesto = p.id_presupuesto
        LEFT JOIN estado_presupuesto ep  ON ep.id_estado_ppto = p.id_estado_ppto
        WHERE p.activo_presupuesto = 1
        GROUP BY p.id_presupuesto
        ORDER BY p.id_presupuesto
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo str_pad("ID", 6) . str_pad("Número", 20) . str_pad("V.actual", 10) . str_pad("Estado gen.", 15) . str_pad("Estado tab.", 25) . str_pad("Versiones", 12) . "Líneas\n";
    echo str_repeat("─", 90) . "\n";
    foreach ($final as $f) {
        echo str_pad($f['id_presupuesto'], 6)
           . str_pad($f['numero_presupuesto'], 20)
           . str_pad("v" . $f['v_actual'], 10)
           . str_pad($f['estado_gen'], 15)
           . str_pad($f['estado_tab'], 25)
           . str_pad($f['num_versiones'], 12)
           . $f['total_lineas'] . "\n";
    }

    // ── PASO 4: Recrear los triggers bloqueantes
    echo "\n";
    $pdo->exec($ddl_trg_linea_before_delete);
    echo "<span class='ok'>🔒 Trigger trg_linea_presupuesto_before_delete recreado</span>\n";
    $pdo->exec($ddl_trg_linea_before_update);
    echo "<span class='ok'>🔒 Trigger trg_linea_presupuesto_before_update recreado</span>\n";
    $pdo->exec($ddl_trg_before_delete);
    echo "<span class='ok'>🔒 Trigger trg_presupuesto_version_before_delete recreado</span>\n";
    $pdo->exec($ddl_trg_validar_transicion);
    echo "<span class='ok'>🔒 Trigger trg_version_validar_transicion_estado recreado</span>\n";

    echo "\n<span class='ok'><b>✅ Reset completado. Puedes comenzar las pruebas desde cero.</b></span>\n";

} catch (Exception $e) {
    // Intentar recrear triggers aunque haya habido un error
    try {
        $pdo->exec("DROP TRIGGER IF EXISTS `trg_linea_presupuesto_before_delete`");
        $pdo->exec("DROP TRIGGER IF EXISTS `trg_linea_presupuesto_before_update`");
        $pdo->exec("DROP TRIGGER IF EXISTS `trg_version_validar_transicion_estado`");
        $pdo->exec("DROP TRIGGER IF EXISTS `trg_presupuesto_version_before_delete`");
        $pdo->exec($ddl_trg_linea_before_delete);
        $pdo->exec($ddl_trg_linea_before_update);
        $pdo->exec($ddl_trg_before_delete);
        $pdo->exec($ddl_trg_validar_transicion);
        echo "<span class='warn'>⚠️  Triggers recreados tras el error.</span>\n";
    } catch (Exception $ignored) {}

    echo "<span class='err'>❌ ERROR: " . htmlspecialchars($e->getMessage()) . "</span>\n";
}

echo "</pre></div>";
echo "<a class='btn' href='?'>← Volver a la vista previa</a>";
echo "</body></html>";
