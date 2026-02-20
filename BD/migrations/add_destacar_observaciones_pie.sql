-- Migración: Agregar campo destacar_observaciones_pie_presupuesto
-- Fecha: 2026-02-20
-- Descripción: Añade switch para controlar si las observaciones de pie se muestran destacadas (TRUE)
--              o integradas con las observaciones regulares (FALSE) en el PDF del presupuesto

ALTER TABLE presupuesto 
ADD COLUMN destacar_observaciones_pie_presupuesto BOOLEAN DEFAULT TRUE 
COMMENT 'Controla visualización de observaciones de pie: TRUE=destacadas con líneas y centrado, FALSE=integradas sin decoración y alineadas a izquierda'
AFTER observaciones_pie_ingles_presupuesto;

-- Verificar cambio
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, COLUMN_COMMENT 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'toldos_db' 
AND TABLE_NAME = 'presupuesto' 
AND COLUMN_NAME = 'destacar_observaciones_pie_presupuesto';
