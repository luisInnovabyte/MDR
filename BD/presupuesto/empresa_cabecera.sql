ALTER TABLE presupuesto
ADD COLUMN id_empresa INT UNSIGNED 
    COMMENT 'Empresa que emite el presupuesto (ficticia o real)'
    AFTER id_presupuesto,
ADD CONSTRAINT fk_presupuesto_empresa 
    FOREIGN KEY (id_empresa) 
    REFERENCES empresa(id_empresa) 
    ON DELETE RESTRICT 
    ON UPDATE CASCADE,
ADD INDEX idx_id_empresa_presupuesto (id_empresa);