-- =============================================================
-- Feature 17: empresa fijada universalmente en pago_presupuesto
-- Fecha: 06/03/2026
-- =============================================================

ALTER TABLE pago_presupuesto
    ADD COLUMN id_empresa_pago INT UNSIGNED NULL
        COMMENT 'Empresa emisora fijada en el momento del pago'
        AFTER id_documento_ppto,
    ADD CONSTRAINT fk_pago_ppto_empresa
        FOREIGN KEY (id_empresa_pago)
        REFERENCES empresa(id_empresa)
        ON DELETE RESTRICT
        ON UPDATE CASCADE;

CREATE INDEX idx_empresa_pago_ppto ON pago_presupuesto (id_empresa_pago);
