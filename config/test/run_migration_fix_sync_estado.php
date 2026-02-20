<?php
/**
 * MIGRACIÓN: Fix trg_version_sync_estado_cabecera + desactivar PROC
 * =================================================================
 * Fecha: 2026-02-19
 * Descripción:
 *   - Extiende el trigger para que también sincronice id_estado_ppto
 *     (FK al catálogo estado_presupuesto) además del campo ENUM existente.
 *   - Desactiva el estado PROC (activo=0).
 *
 * INSTRUCCIONES:
 *   1. Abrir en el navegador: http://localhost/MDR/config/test/run_migration_fix_sync_estado.php
 *   2. Verificar que todos los pasos muestren ✅
 *   3. ELIMINAR o mover este archivo después de ejecutarlo.
 */

require_once __DIR__ . '/../conexion.php';

$conn = null;
$resultados = [];
$errores = [];

function ok($msg) {
    global $resultados;
    $resultados[] = ['ok', $msg];
}

function fail($msg) {
    global $errores;
    $errores[] = $msg;
}

try {
    $conn = (new Conexion())->getConexion();

    // ====================================================================
    // PRE-CHECK: Estado actual del trigger y de PROC
    // ====================================================================

    // Ver trigger actual
    $stmt = $conn->query("SHOW TRIGGERS LIKE 'presupuesto_version'");
    $triggers = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $trigger_actual = '';
    foreach ($triggers as $t) {
        if ($t['Trigger'] === 'trg_version_sync_estado_cabecera') {
            $trigger_actual = $t['Statement'];
        }
    }

    // Ver si PROC está activo
    $stmt = $conn->prepare("SELECT id_estado_ppto, activo_estado_ppto FROM estado_presupuesto WHERE codigo_estado_ppto = 'PROC'");
    $stmt->execute();
    $proc_antes = $stmt->fetch(PDO::FETCH_ASSOC);

    // Ver algunos presupuestos antes de la migración
    $stmt = $conn->query(
        "SELECT p.id_presupuesto, p.numero_presupuesto,
                p.estado_general_presupuesto, p.id_estado_ppto,
                ep.nombre_estado_ppto
         FROM presupuesto p
         LEFT JOIN estado_presupuesto ep ON ep.id_estado_ppto = p.id_estado_ppto
         WHERE p.activo_presupuesto = 1
         LIMIT 5"
    );
    $presupuestos_antes = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ====================================================================
    // PASO 1: Eliminar trigger antiguo
    // ====================================================================
    try {
        $conn->exec("DROP TRIGGER IF EXISTS `trg_version_sync_estado_cabecera`");
        ok("PASO 1 ✅ Trigger antiguo eliminado");
    } catch (PDOException $e) {
        fail("PASO 1 ❌ Error al eliminar trigger: " . $e->getMessage());
    }

    // ====================================================================
    // PASO 2: Crear nuevo trigger con sincronización de id_estado_ppto
    // ====================================================================
    if (empty($errores)) {
        $sql_trigger = "CREATE TRIGGER `trg_version_sync_estado_cabecera`
AFTER UPDATE ON `presupuesto_version`
FOR EACH ROW
BEGIN
    DECLARE version_actual INT;
    DECLARE nuevo_id_estado INT;

    SELECT version_actual_presupuesto
    INTO version_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto;

    IF NEW.numero_version_presupuesto = version_actual THEN

        SELECT id_estado_ppto
        INTO nuevo_id_estado
        FROM estado_presupuesto
        WHERE codigo_estado_ppto = CASE NEW.estado_version_presupuesto
            WHEN 'borrador'  THEN 'BORRADOR'
            WHEN 'enviado'   THEN 'ESPE-RESP'
            WHEN 'aprobado'  THEN 'APROB'
            WHEN 'rechazado' THEN 'RECH'
            WHEN 'cancelado' THEN 'CANC'
            ELSE 'BORRADOR'
        END
        LIMIT 1;

        UPDATE presupuesto
        SET
            estado_general_presupuesto = NEW.estado_version_presupuesto,
            id_estado_ppto             = nuevo_id_estado
        WHERE id_presupuesto = NEW.id_presupuesto;

    END IF;
END";

        try {
            $conn->exec($sql_trigger);
            ok("PASO 2 ✅ Nuevo trigger creado con sincronización de id_estado_ppto");
        } catch (PDOException $e) {
            fail("PASO 2 ❌ Error al crear trigger: " . $e->getMessage());
        }
    }

    // ====================================================================
    // PASO 3: Desactivar estado PROC
    // ====================================================================
    if (empty($errores)) {
        try {
            $stmt = $conn->prepare("UPDATE estado_presupuesto SET activo_estado_ppto = 0 WHERE codigo_estado_ppto = 'PROC'");
            $stmt->execute();
            $filas = $stmt->rowCount();
            ok("PASO 3 ✅ Estado PROC desactivado ($filas fila/s afectada/s)");
        } catch (PDOException $e) {
            fail("PASO 3 ❌ Error al desactivar PROC: " . $e->getMessage());
        }
    }

    // ====================================================================
    // PASO 4 (OPCIONAL): Re-sincronizar presupuestos existentes
    //   Actualiza id_estado_ppto en presupuestos cuya versión actual
    //   es "enviado"/"aprobado"/"rechazado"/"cancelado" pero aún tienen
    //   el id_estado_ppto desfasado.
    // ====================================================================
    if (empty($errores)) {
        $sql_resync = "UPDATE presupuesto p
            INNER JOIN presupuesto_version pv
                ON  pv.id_presupuesto = p.id_presupuesto
                AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                AND pv.activo_version = 1
            INNER JOIN estado_presupuesto ep
                ON  ep.codigo_estado_ppto = CASE pv.estado_version_presupuesto
                        WHEN 'borrador'  THEN 'BORRADOR'
                        WHEN 'enviado'   THEN 'ESPE-RESP'
                        WHEN 'aprobado'  THEN 'APROB'
                        WHEN 'rechazado' THEN 'RECH'
                        WHEN 'cancelado' THEN 'CANC'
                        ELSE 'BORRADOR'
                    END
            SET p.id_estado_ppto = ep.id_estado_ppto,
                p.estado_general_presupuesto = pv.estado_version_presupuesto
            WHERE p.activo_presupuesto = 1";

        try {
            $stmt = $conn->prepare($sql_resync);
            $stmt->execute();
            $filas = $stmt->rowCount();
            ok("PASO 4 ✅ Re-sincronización de presupuestos existentes: $filas fila/s actualizadas");
        } catch (PDOException $e) {
            fail("PASO 4 ❌ Error en re-sincronización: " . $e->getMessage());
        }
    }

    // ====================================================================
    // POST-CHECK: Estado después de la migración
    // ====================================================================
    $stmt = $conn->query("SHOW TRIGGERS LIKE 'presupuesto_version'");
    $triggers_post = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $trigger_nuevo_ok = false;
    foreach ($triggers_post as $t) {
        if ($t['Trigger'] === 'trg_version_sync_estado_cabecera') {
            $trigger_nuevo_ok = true;
        }
    }

    $stmt = $conn->prepare("SELECT id_estado_ppto, activo_estado_ppto FROM estado_presupuesto WHERE codigo_estado_ppto = 'PROC'");
    $stmt->execute();
    $proc_despues = $stmt->fetch(PDO::FETCH_ASSOC);

    $stmt = $conn->query(
        "SELECT p.id_presupuesto, p.numero_presupuesto,
                p.estado_general_presupuesto, p.id_estado_ppto,
                ep.nombre_estado_ppto
         FROM presupuesto p
         LEFT JOIN estado_presupuesto ep ON ep.id_estado_ppto = p.id_estado_ppto
         WHERE p.activo_presupuesto = 1
         LIMIT 5"
    );
    $presupuestos_despues = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $stmt = $conn->query("SELECT id_estado_ppto, codigo_estado_ppto, nombre_estado_ppto, activo_estado_ppto FROM estado_presupuesto ORDER BY orden_estado_ppto");
    $todos_estados = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (Exception $e) {
    fail("Error de conexión: " . $e->getMessage());
}

?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Migración: Fix Sync id_estado_ppto</title>
    <style>
        body { font-family: monospace; background: #1e1e1e; color: #d4d4d4; padding: 24px; }
        h1 { color: #4ec9b0; }
        h2 { color: #9cdcfe; margin-top: 24px; }
        .ok   { color: #4ec9b0; }
        .fail { color: #f44747; font-weight: bold; }
        table { border-collapse: collapse; margin: 8px 0; width: 100%; }
        th    { background: #264f78; color: #fff; padding: 6px 10px; text-align: left; }
        td    { padding: 4px 10px; border-bottom: 1px solid #333; }
        .badge-ok  { background: #1b5e20; color: #a5d6a7; padding: 2px 8px; border-radius: 4px; }
        .badge-fail{ background: #b71c1c; color: #ffcdd2; padding: 2px 8px; border-radius: 4px; }
        .box { background: #252526; border: 1px solid #444; border-radius: 6px; padding: 16px; margin-top: 16px; }
        pre { background: #1a1a2e; padding: 12px; border-radius: 4px; overflow-x: auto; font-size: 12px; color: #abb2bf; }
    </style>
</head>
<body>
<h1>Migración: Fix trg_version_sync_estado_cabecera + PROC</h1>

<?php if (!empty($errores)): ?>
    <div class="box">
        <h2>❌ ERRORES</h2>
        <?php foreach ($errores as $e): ?>
            <p class="fail"><?= htmlspecialchars($e) ?></p>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<div class="box">
    <h2>Resultados de la migración</h2>
    <?php foreach ($resultados as [$tipo, $msg]): ?>
        <p class="<?= $tipo ?>"><?= htmlspecialchars($msg) ?></p>
    <?php endforeach; ?>
</div>

<div class="box">
    <h2>Trigger después de la migración</h2>
    <?php if ($trigger_nuevo_ok ?? false): ?>
        <span class="badge-ok">✅ trg_version_sync_estado_cabecera EXISTE</span>
    <?php else: ?>
        <span class="badge-fail">❌ Trigger NO encontrado</span>
    <?php endif; ?>
</div>

<div class="box">
    <h2>Estado PROC</h2>
    <table>
        <tr><th></th><th>id</th><th>activo_antes</th><th>activo_después</th></tr>
        <tr>
            <td>PROC</td>
            <td><?= $proc_antes['id_estado_ppto'] ?? '—' ?></td>
            <td><?= ($proc_antes['activo_estado_ppto'] ?? '—') == 1 ? '<span class="badge-fail">1 (activo)</span>' : '<span class="badge-ok">0 (inactivo)</span>' ?></td>
            <td><?= ($proc_despues['activo_estado_ppto'] ?? '—') == 1 ? '<span class="badge-fail">1 (activo)</span>' : '<span class="badge-ok">0 (inactivo)</span>' ?></td>
        </tr>
    </table>
</div>

<div class="box">
    <h2>Todos los estados del catálogo (post-migración)</h2>
    <table>
        <tr><th>id</th><th>código</th><th>nombre</th><th>activo</th></tr>
        <?php foreach ($todos_estados ?? [] as $e): ?>
        <tr>
            <td><?= $e['id_estado_ppto'] ?></td>
            <td><?= $e['codigo_estado_ppto'] ?></td>
            <td><?= htmlspecialchars($e['nombre_estado_ppto']) ?></td>
            <td><?= $e['activo_estado_ppto'] == 1 ? '<span class="badge-ok">activo</span>' : '<span class="badge-fail">inactivo</span>' ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="box">
    <h2>Comparación presupuestos: antes → después</h2>
    <table>
        <tr><th>id</th><th>número</th><th>estado_general (ENUM)</th><th>id_estado_ppto (antes)</th><th>nombre estado (después)</th></tr>
        <?php 
        $ids_antes = [];
        foreach ($presupuestos_antes as $p) {
            $ids_antes[$p['id_presupuesto']] = $p;
        }
        foreach ($presupuestos_despues ?? [] as $p):
            $antes = $ids_antes[$p['id_presupuesto']] ?? ['id_estado_ppto' => '?', 'nombre_estado_ppto' => '?'];
            $cambio = $antes['id_estado_ppto'] != $p['id_estado_ppto'] ? ' style="background:#1b3a1b"' : '';
        ?>
        <tr<?= $cambio ?>>
            <td><?= $p['id_presupuesto'] ?></td>
            <td><?= htmlspecialchars($p['numero_presupuesto']) ?></td>
            <td><?= $p['estado_general_presupuesto'] ?></td>
            <td><?= $antes['id_estado_ppto'] ?></td>
            <td><?= htmlspecialchars($p['nombre_estado_ppto'] ?? '—') ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</div>

<div class="box" style="border-color:#b71c1c;">
    <h2 style="color:#f44747;">⚠️ Recordatorio</h2>
    <p>Elimina o mueve este archivo después de ejecutarlo:</p>
    <pre>w:\MDR\config\test\run_migration_fix_sync_estado.php</pre>
</div>
</body>
</html>
