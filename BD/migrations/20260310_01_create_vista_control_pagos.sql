-- =============================================================
-- Migration: 20260310_01_create_vista_control_pagos.sql
-- Descripción: Vista global de control de pagos por presupuesto
-- Proyecto: MDR ERP Manager
-- Fecha: 2026-03-10
-- =============================================================

CREATE OR REPLACE VIEW vista_control_pagos AS
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

    -- Total acordado: suma de TODOS los pagos del plan (incl. anulados)
    -- Representa el importe total real facturado/acordado con el cliente
    COALESCE(plan.total_acordado, 0) AS total_presupuesto,

    -- Totales de pagos agregados
    COALESCE(ag.total_pagado, 0)      AS total_pagado,
    COALESCE(ag.total_conciliado, 0)  AS total_conciliado,
    COALESCE(plan.total_acordado, 0) - COALESCE(ag.total_pagado, 0) AS saldo_pendiente,
    ROUND(
        CASE
            WHEN COALESCE(ag.total_pagado, 0) > 0
            THEN COALESCE(ag.total_conciliado, 0) / ag.total_pagado * 100
            ELSE 0
        END, 2
    ) AS porcentaje_pagado,

    ag.fecha_ultimo_pago,
    ag.metodos_pago_usados,
    COALESCE(ag.num_pagos, 0) AS num_pagos,

    -- Tipos de documentos emitidos para este presupuesto
    docs.tipos_documentos,
    docs.fecha_ultima_factura,

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

-- Total acordado: suma de TODOS los pagos activos (incluido estado='anulado')
-- porque representan el plan de pago total del presupuesto
LEFT JOIN (
    SELECT
        id_presupuesto,
        SUM(importe_pago_ppto) AS total_acordado
    FROM pago_presupuesto
    WHERE activo_pago_ppto = 1
    GROUP BY id_presupuesto
) plan ON plan.id_presupuesto = p.id_presupuesto

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
        SUM(
            CASE
                WHEN pp.estado_pago_ppto = 'conciliado' AND pp.tipo_pago_ppto = 'devolucion' THEN -pp.importe_pago_ppto
                WHEN pp.estado_pago_ppto = 'conciliado' THEN pp.importe_pago_ppto
                ELSE 0
            END
        ) AS total_conciliado,
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
        ) AS tipos_documentos,
        MAX(CASE
            WHEN dp.tipo_documento_ppto IN ('factura_proforma','factura_anticipo','factura_final','factura_rectificativa')
            THEN dp.fecha_generacion_documento_ppto
        END) AS fecha_ultima_factura
    FROM documento_presupuesto dp
    WHERE dp.activo_documento_ppto = 1
    GROUP BY dp.id_presupuesto
) docs ON docs.id_presupuesto = p.id_presupuesto

WHERE p.activo_presupuesto = 1
ORDER BY p.fecha_inicio_evento_presupuesto ASC, p.id_presupuesto ASC;
