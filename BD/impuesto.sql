CREATE TABLE impuesto (
  id_impuesto INT AUTO_INCREMENT PRIMARY KEY,
  tipo_impuesto VARCHAR(20) NOT NULL COMMENT 'Tipo de impuesto (e.g., IVA, GST)',
  tasa_impuesto DECIMAL(5,2) NOT NULL comment 'Tasa del impuesto en porcentaje',
  descr_impuesto VARCHAR(255),
  activo_impuesto boolean default true, 
  created_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);