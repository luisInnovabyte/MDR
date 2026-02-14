-- =====================================================================
-- Migración: Agregar campo firma_comercial a tabla comerciales
-- Fecha: 2025-01-20
-- Descripción: Añade campo TEXT para almacenar firma digital en base64
-- Punto 14: Nueva Funcionalidad - Firma de Empleado
-- =====================================================================

USE toldos_db;

-- Agregar columna firma_comercial
ALTER TABLE comerciales 
ADD COLUMN firma_comercial TEXT 
COMMENT 'Firma digital del comercial en formato base64 PNG';

-- Verificar que se agregó correctamente
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'toldos_db' 
AND TABLE_NAME = 'comerciales'
AND COLUMN_NAME = 'firma_comercial';
