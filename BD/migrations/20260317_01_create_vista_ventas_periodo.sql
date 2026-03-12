-- ============================================================
-- Migración: Vista para Informe de Ventas por Período
-- Fecha: 2026-03-17
-- Informe: Informe_ventas
-- ============================================================
--
-- Propósito: Consolida presupuestos aprobados (ACEP o FACT)
--            con sus líneas de detalle, artículo, familia
--            y cliente, para poder agrupar ventas por
--            período, cliente, familia, etc.
--
-- Tablas involucradas:
--   presupuesto         → cabecera del presupuesto
--   linea_presupuesto   → detalle de líneas
--   articulo            → artículo de cada línea
--   familia             → familia del artículo
--   cliente             → datos del cliente
--   estado_presupuesto  → solo estados "ganados" (ACEP, FACT)
-- ============================================================

CREATE OR REPLACE VIEW vista_ventas_periodo AS
SELECT
    -- Presupuesto
    p.id_presupuesto,
    p.numero_presupuesto,
    p.fecha_presupuesto,
    YEAR(p.fecha_presupuesto)       AS anyo_presupuesto,
    MONTH(p.fecha_presupuesto)      AS mes_presupuesto,
    p.nombre_evento_presupuesto,

    -- Cliente
    p.id_cliente,
    c.nombre_cliente                AS nombre_completo_cliente,

    -- Estado
    ep.codigo_estado_ppto,
    ep.nombre_estado_ppto,

    -- Versión del presupuesto
    pv.id_version_presupuesto,

    -- Línea de detalle
    lp.id_linea_ppto,
    lp.descripcion_linea_ppto,
    lp.cantidad_linea_ppto,
    lp.precio_unitario_linea_ppto,
    lp.descuento_linea_ppto,
    lp.porcentaje_iva_linea_ppto,

    -- Total calculado por línea
    (lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto
        * (1 - COALESCE(lp.descuento_linea_ppto, 0) / 100)
        * COALESCE(lp.jornadas_linea_ppto, 1)
    ) AS total_linea_ppto,

    -- Artículo
    lp.id_articulo,
    a.codigo_articulo,
    a.nombre_articulo,

    -- Familia del artículo
    a.id_familia,
    f.nombre_familia,
    f.codigo_familia

FROM presupuesto p

INNER JOIN cliente c
    ON p.id_cliente = c.id_cliente

INNER JOIN estado_presupuesto ep
    ON p.id_estado_ppto = ep.id_estado_ppto

INNER JOIN presupuesto_version pv
    ON p.id_presupuesto = pv.id_presupuesto
    AND pv.numero_version_presupuesto = p.version_actual_presupuesto

INNER JOIN linea_presupuesto lp
    ON pv.id_version_presupuesto = lp.id_version_presupuesto
    AND lp.activo_linea_ppto = 1
    AND lp.tipo_linea_ppto = 'articulo'

LEFT JOIN articulo a
    ON lp.id_articulo = a.id_articulo

LEFT JOIN familia f
    ON a.id_familia = f.id_familia

WHERE
    p.activo_presupuesto = 1
    AND ep.codigo_estado_ppto = 'APROB';

-- ============================================================
-- Verificación rápida tras aplicar:
--   SELECT anyo_presupuesto, mes_presupuesto,
--          SUM(total_linea_ppto) AS total_mes
--   FROM vista_ventas_periodo
--   GROUP BY anyo_presupuesto, mes_presupuesto
--   ORDER BY anyo_presupuesto DESC, mes_presupuesto ASC;
-- ============================================================
