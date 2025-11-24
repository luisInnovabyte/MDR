-- Script para agregar el campo coeficiente_familia a la tabla familia
-- Este script agrega el campo solo si no existe

-- Verificar si el campo no existe y agregarlo
SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
    AND TABLE_NAME = 'familia'
    AND COLUMN_NAME = 'coeficiente_familia'
);

SET @sql = IF(@column_exists = 0, 
    'ALTER TABLE familia ADD COLUMN coeficiente_familia BOOLEAN DEFAULT TRUE COMMENT ''Control de coeficientes de descuento - TRUE permite aplicar coeficientes'' AFTER activo_familia', 
    'SELECT ''El campo coeficiente_familia ya existe en la tabla familia'' AS mensaje'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar el resultado
SELECT 
    COLUMN_NAME,
    DATA_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
AND TABLE_NAME = 'familia'
AND COLUMN_NAME = 'coeficiente_familia';

-- Mostrar estructura actualizada de la tabla
DESCRIBE familia;