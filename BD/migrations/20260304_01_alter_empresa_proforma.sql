-- ============================================================
-- Migration: 20260304_01_alter_empresa_proforma.sql
-- Descripción: Añadir campos para numeración de facturas proforma
-- Fecha: 04 de marzo de 2026
-- ============================================================

ALTER TABLE empresa
    ADD COLUMN serie_factura_proforma_empresa VARCHAR(10) DEFAULT 'FP'
        COMMENT 'Serie para facturas proforma (ej: FP, PRO, FPR)'
        AFTER numero_actual_presupuesto_empresa,
    ADD COLUMN numero_actual_factura_proforma_empresa INT UNSIGNED DEFAULT 0
        COMMENT 'Último número de factura proforma emitida'
        AFTER serie_factura_proforma_empresa;

CREATE INDEX idx_serie_fp_empresa ON empresa(serie_factura_proforma_empresa);

-- Verificar resultado
-- SELECT id_empresa, nombre_empresa, serie_factura_proforma_empresa, numero_actual_factura_proforma_empresa
-- FROM empresa;
