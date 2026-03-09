-- ============================================================
-- Migration: 20260304_05_create_vistas_pagos.sql
-- Descripción: Vistas para consultar documentos y pagos con
--              información completa (joins a presupuesto, cliente, empresa)
-- Fecha: 04 de marzo de 2026
-- NOTA: Ejecutar DESPUÉS de crear las tablas documento_presupuesto
--       y pago_presupuesto
-- ============================================================

-- ─────────────────────────────────────────────────
-- Vista: v_documentos_presupuesto
-- ─────────────────────────────────────────────────
CREATE OR REPLACE VIEW v_documentos_presupuesto AS
SELECT
    -- Documento actual
    dp.id_documento_ppto,
    dp.id_presupuesto,
    dp.id_version_presupuesto,
    dp.id_empresa,
    dp.seleccion_manual_empresa_documento_ppto,
    dp.tipo_documento_ppto,
    dp.numero_documento_ppto,
    dp.serie_documento_ppto,
    dp.id_documento_origen,
    dp.motivo_abono_documento_ppto,
    dp.subtotal_documento_ppto,
    dp.total_iva_documento_ppto,
    dp.total_documento_ppto,
    dp.ruta_pdf_documento_ppto,
    dp.tamano_pdf_documento_ppto,
    dp.fecha_emision_documento_ppto,
    dp.fecha_generacion_documento_ppto,
    dp.observaciones_documento_ppto,
    dp.activo_documento_ppto,
    dp.created_at_documento_ppto,
    dp.updated_at_documento_ppto,

    -- Presupuesto
    p.numero_presupuesto,
    p.nombre_evento_presupuesto,
    vt.total_con_iva AS total_presupuesto,

    -- Cliente
    c.id_cliente,
    c.nombre_cliente,
    c.nombre_facturacion_cliente,
    IFNULL(c.nombre_facturacion_cliente, c.nombre_cliente) AS nombre_completo_cliente,

    -- Empresa emisora
    e.nombre_empresa,
    e.nombre_comercial_empresa,
    e.nif_empresa,
    e.ficticia_empresa,

    -- Documento de origen (para rectificativas/abonos)
    dorg.numero_documento_ppto  AS numero_documento_origen,
    dorg.tipo_documento_ppto    AS tipo_documento_origen,
    dorg.total_documento_ppto   AS total_documento_origen,
    dorg.fecha_emision_documento_ppto AS fecha_emision_origen

FROM documento_presupuesto dp

INNER JOIN presupuesto p
    ON dp.id_presupuesto = p.id_presupuesto

INNER JOIN cliente c
    ON p.id_cliente = c.id_cliente

INNER JOIN empresa e
    ON dp.id_empresa = e.id_empresa

LEFT JOIN v_presupuesto_totales vt
    ON dp.id_presupuesto = vt.id_presupuesto
    AND vt.numero_version_presupuesto = p.version_actual_presupuesto

LEFT JOIN documento_presupuesto dorg
    ON dp.id_documento_origen = dorg.id_documento_ppto

WHERE dp.activo_documento_ppto = 1

ORDER BY dp.fecha_emision_documento_ppto DESC, dp.id_documento_ppto DESC;


-- ─────────────────────────────────────────────────
-- Vista: v_pagos_presupuesto
-- ─────────────────────────────────────────────────
CREATE OR REPLACE VIEW v_pagos_presupuesto AS
SELECT
    -- Pago
    pp.id_pago_ppto,
    pp.id_presupuesto,
    pp.id_documento_ppto,
    pp.tipo_pago_ppto,
    pp.importe_pago_ppto,
    pp.porcentaje_pago_ppto,
    pp.id_metodo_pago,
    pp.referencia_pago_ppto,
    pp.fecha_pago_ppto,
    pp.fecha_valor_pago_ppto,
    pp.estado_pago_ppto,
    pp.observaciones_pago_ppto,
    pp.activo_pago_ppto,
    pp.created_at_pago_ppto,
    pp.updated_at_pago_ppto,

    -- Presupuesto
    p.numero_presupuesto,
    p.nombre_evento_presupuesto,
    p.fecha_presupuesto,
    vt.total_con_iva AS total_presupuesto,

    -- Cliente
    c.id_cliente,
    c.nombre_cliente,
    c.nombre_facturacion_cliente,
    IFNULL(c.nombre_facturacion_cliente, c.nombre_cliente) AS nombre_completo_cliente,

    -- Documento vinculado (factura asociada al pago)
    dp.tipo_documento_ppto        AS tipo_documento_vinculado,
    dp.numero_documento_ppto      AS numero_documento_vinculado,
    dp.subtotal_documento_ppto    AS subtotal_documento_vinculado,
    dp.total_iva_documento_ppto   AS iva_cuota_documento_vinculado,
    dp.total_documento_ppto       AS total_documento_vinculado,
    dp.ruta_pdf_documento_ppto    AS ruta_pdf_vinculado,

    -- Método de pago
    mp.codigo_metodo_pago,
    mp.nombre_metodo_pago

FROM pago_presupuesto pp

INNER JOIN presupuesto p
    ON pp.id_presupuesto = p.id_presupuesto

INNER JOIN cliente c
    ON p.id_cliente = c.id_cliente

LEFT JOIN v_presupuesto_totales vt
    ON pp.id_presupuesto = vt.id_presupuesto
    AND vt.numero_version_presupuesto = p.version_actual_presupuesto

LEFT JOIN documento_presupuesto dp
    ON pp.id_documento_ppto = dp.id_documento_ppto

LEFT JOIN metodo_pago mp
    ON pp.id_metodo_pago = mp.id_metodo_pago

WHERE pp.activo_pago_ppto = 1

ORDER BY pp.fecha_pago_ppto DESC, pp.id_pago_ppto DESC;
