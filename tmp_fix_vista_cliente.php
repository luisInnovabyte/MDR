<?php
$config = json_decode(file_get_contents(__DIR__ . '/config/conexion.json'), true);
$dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset=utf8mb4";
$pdo = new PDO($dsn, $config['user'], $config['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$sql = "CREATE OR REPLACE VIEW vista_control_pagos AS
SELECT
    p.id_presupuesto,
    p.numero_presupuesto,
    p.fecha_presupuesto,
    p.fecha_inicio_evento_presupuesto,
    p.fecha_fin_evento_presupuesto,
    p.nombre_evento_presupuesto,
    p.numero_pedido_cliente_presupuesto,

    -- Cliente
    c.id_cliente,
    COALESCE(NULLIF(TRIM(c.nombre_facturacion_cliente), ''), c.nombre_cliente) AS nombre_completo_cliente,

    -- Estado presupuesto
    ep.id_estado_ppto,
    ep.nombre_estado_ppto,
    ep.codigo_estado_ppto,
    ep.color_estado_ppto,

    -- Forma de pago
    fp.nombre_pago AS nombre_forma_pago,

    -- Total del presupuesto (versión actual desde v_presupuesto_totales)
    COALESCE(vt.total_con_iva, 0) AS total_presupuesto,

    -- Totales de pagos agregados
    COALESCE(ag.total_pagado, 0) AS total_pagado,
    COALESCE(vt.total_con_iva, 0) - COALESCE(ag.total_pagado, 0) AS saldo_pendiente,
    ROUND(
        CASE
            WHEN COALESCE(vt.total_con_iva, 0) > 0
            THEN COALESCE(ag.total_pagado, 0) / vt.total_con_iva * 100
            ELSE 0
        END, 2
    ) AS porcentaje_pagado,

    ag.fecha_ultimo_pago,
    ag.metodos_pago_usados,
    COALESCE(ag.num_pagos, 0) AS num_pagos,

    -- Tipos de documentos emitidos para este presupuesto
    docs.tipos_documentos,

    p.created_at_presupuesto,
    p.updated_at_presupuesto

FROM presupuesto p

INNER JOIN estado_presupuesto ep
    ON p.id_estado_ppto = ep.id_estado_ppto
    AND ep.codigo_estado_ppto = 'APROB'

INNER JOIN cliente c
    ON p.id_cliente = c.id_cliente

LEFT JOIN forma_pago fp
    ON p.id_forma_pago = fp.id_pago

LEFT JOIN v_presupuesto_totales vt
    ON vt.id_presupuesto = p.id_presupuesto
    AND vt.numero_version_presupuesto = p.version_actual_presupuesto

-- Subquery: agregados de pagos activos no anulados
LEFT JOIN (
    SELECT
        pp.id_presupuesto,
        SUM(
            CASE
                WHEN pp.tipo_pago_ppto = 'devolucion' THEN -pp.importe_pago_ppto
                ELSE pp.importe_pago_ppto
            END
        ) AS total_pagado,
        MAX(pp.fecha_pago_ppto) AS fecha_ultimo_pago,
        GROUP_CONCAT(
            DISTINCT mp.nombre_metodo_pago
            ORDER BY mp.nombre_metodo_pago
            SEPARATOR ', '
        ) AS metodos_pago_usados,
        COUNT(pp.id_pago_ppto) AS num_pagos
    FROM pago_presupuesto pp
    LEFT JOIN metodo_pago mp ON pp.id_metodo_pago = mp.id_metodo_pago
    WHERE pp.activo_pago_ppto = 1
        AND pp.estado_pago_ppto != 'anulado'
    GROUP BY pp.id_presupuesto
) ag ON ag.id_presupuesto = p.id_presupuesto

-- Subquery: tipos de documentos emitidos
LEFT JOIN (
    SELECT
        dp.id_presupuesto,
        GROUP_CONCAT(
            DISTINCT dp.tipo_documento_ppto
            ORDER BY dp.tipo_documento_ppto
            SEPARATOR ','
        ) AS tipos_documentos
    FROM documento_presupuesto dp
    WHERE dp.activo_documento_ppto = 1
    GROUP BY dp.id_presupuesto
) docs ON docs.id_presupuesto = p.id_presupuesto

WHERE p.activo_presupuesto = 1
ORDER BY p.fecha_inicio_evento_presupuesto ASC, p.id_presupuesto ASC";

try {
    $pdo->exec($sql);
    $row = $pdo->query("SELECT numero_presupuesto, nombre_completo_cliente, nombre_estado_ppto, nombre_forma_pago, total_presupuesto, total_pagado FROM vista_control_pagos LIMIT 1")->fetch(PDO::FETCH_ASSOC);
    echo "OK: Vista completa actualizada.\n";
    echo "Verificacion: " . json_encode($row, JSON_UNESCAPED_UNICODE) . "\n";
} catch (PDOException $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}
