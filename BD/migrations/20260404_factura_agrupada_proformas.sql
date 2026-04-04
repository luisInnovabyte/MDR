-- =============================================================================
-- Migración: Soporte de proformas en Factura Agrupada
-- Fecha: 2026-04-04
-- Descripción:
--   1. Añade total_proformas_agrupada a factura_agrupada
--   2. Añade total_proformas_fap a factura_agrupada_presupuesto
--   3. Añade anulada_por_fa_id a documento_presupuesto (trazabilidad de proformas anuladas)
--   4. Actualiza vista_factura_agrupada_completa para exponer el nuevo campo
-- =============================================================================

-- 1. Campo en cabecera: importe total de proformas anuladas al crear la FA
ALTER TABLE factura_agrupada
    ADD COLUMN total_proformas_agrupada DECIMAL(10,2) NOT NULL DEFAULT 0.00
        COMMENT 'Suma de importes de facturas_proforma anuladas al crear la FA, para nota de observaciones'
    AFTER total_anticipos_agrupada;

-- 2. Campo en líneas: importe de proformas del presupuesto
ALTER TABLE factura_agrupada_presupuesto
    ADD COLUMN total_proformas_fap DECIMAL(10,2) NOT NULL DEFAULT 0.00
        COMMENT 'Suma de facturas_proforma activas del presupuesto en el momento de crear la FA'
    AFTER total_anticipos_reales_fap;

-- 3. Campo de trazabilidad en documento_presupuesto
--    Permite saber qué FA anuló cada proforma, para reactivarla al abonar la FA
ALTER TABLE documento_presupuesto
    ADD COLUMN anulada_por_fa_id INT UNSIGNED NULL DEFAULT NULL
        COMMENT 'ID de la factura_agrupada que anuló este documento proforma (NULL si no aplica)'
    AFTER observaciones_documento_ppto,
    ADD CONSTRAINT fk_doc_ppto_fa_anuladora
        FOREIGN KEY (anulada_por_fa_id)
        REFERENCES factura_agrupada(id_factura_agrupada)
        ON DELETE SET NULL
        ON UPDATE CASCADE;

-- 4. Recrear vista incluyendo el nuevo campo
DROP VIEW IF EXISTS vista_factura_agrupada_completa;

CREATE VIEW vista_factura_agrupada_completa AS
SELECT
    fa.id_factura_agrupada,
    fa.numero_factura_agrupada,
    fa.serie_factura_agrupada,
    fa.fecha_factura_agrupada,
    fa.observaciones_agrupada,
    fa.total_base_agrupada,
    fa.total_iva_agrupada,
    fa.total_bruto_agrupada,
    fa.total_anticipos_agrupada,
    fa.total_proformas_agrupada,
    fa.total_a_cobrar_agrupada,
    fa.is_abono_agrupada,
    fa.id_factura_agrupada_ref,
    fa.motivo_abono_agrupada,
    fa.pdf_path_agrupada,
    fa.activo_factura_agrupada,
    fa.created_at_factura_agrupada,
    fa.updated_at_factura_agrupada,
    fa.id_empresa,
    e.nombre_empresa,
    e.nombre_comercial_empresa,
    e.nif_empresa,
    e.ficticia_empresa,
    fa.id_cliente,
    c.nombre_cliente,
    c.nif_cliente,
    c.email_cliente,
    c.telefono_cliente,
    (SELECT COUNT(*)
     FROM factura_agrupada_presupuesto fap2
     WHERE fap2.id_factura_agrupada = fa.id_factura_agrupada
       AND fap2.activo_fap = 1) AS num_presupuestos_agrupada,
    fa_ref.numero_factura_agrupada AS numero_factura_original
FROM factura_agrupada fa
JOIN empresa e ON fa.id_empresa = e.id_empresa
JOIN cliente c ON fa.id_cliente = c.id_cliente
LEFT JOIN factura_agrupada fa_ref ON fa.id_factura_agrupada_ref = fa_ref.id_factura_agrupada
ORDER BY fa.fecha_factura_agrupada DESC, fa.id_factura_agrupada DESC;
