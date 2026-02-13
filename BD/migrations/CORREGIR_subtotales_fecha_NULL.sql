-- =====================================================================
-- CORRECCIÓN: Actualizar valores NULL a TRUE en campo de subtotales
-- Ejecutar en phpMyAdmin o cliente MySQL
-- =====================================================================

USE toldos_db;

-- Actualizar todos los registros que tengan NULL a 1 (TRUE)
UPDATE empresa 
SET mostrar_subtotales_fecha_presupuesto_empresa = 1 
WHERE mostrar_subtotales_fecha_presupuesto_empresa IS NULL;

-- Verificar el resultado
SELECT id_empresa, 
       nombre_empresa, 
       mostrar_subtotales_fecha_presupuesto_empresa 
FROM empresa;

-- Asegurar que el campo no permita NULL en el futuro
ALTER TABLE empresa 
MODIFY COLUMN mostrar_subtotales_fecha_presupuesto_empresa TINYINT(1) NOT NULL DEFAULT 1 
COMMENT 'Controla si se muestran subtotales por fecha en PDF de presupuestos. TRUE=mostrar, FALSE=ocultar';
