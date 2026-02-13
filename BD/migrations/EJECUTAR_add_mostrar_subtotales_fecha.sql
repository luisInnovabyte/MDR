-- =====================================================================
-- MIGRACIÓN: Agregar campo para controlar subtotales por fecha en PDF
-- Ejecutar MANUALMENTE en phpMyAdmin o cliente MySQL
-- =====================================================================

USE toldos_db;

-- Agregar el campo
ALTER TABLE empresa
ADD COLUMN mostrar_subtotales_fecha_presupuesto_empresa BOOLEAN DEFAULT TRUE 
COMMENT 'Controla si se muestran subtotales por fecha en PDF de presupuestos. TRUE=mostrar, FALSE=ocultar';

-- Verificar que se agregó correctamente
DESCRIBE empresa;

-- Mostrar las empresas con el nuevo campo
SELECT id_empresa, nombre_empresa, mostrar_subtotales_fecha_presupuesto_empresa 
FROM empresa 
LIMIT 5;
