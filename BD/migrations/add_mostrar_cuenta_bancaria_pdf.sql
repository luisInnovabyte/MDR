-- =====================================================
-- Migración: Añadir switch para mostrar cuenta bancaria en PDF
-- Fecha: 20/02/2026
-- Autor: Sistema
-- Tabla: empresa
-- Descripción: Permite controlar si se muestra el bloque de datos
--              bancarios en el PDF cuando la forma de pago es transferencia
-- =====================================================

USE toldos_db;

-- Verificar si el campo ya existe antes de crearlo
SELECT COUNT(*) INTO @column_exists 
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = 'toldos_db' 
AND TABLE_NAME = 'empresa' 
AND COLUMN_NAME = 'mostrar_cuenta_bancaria_pdf_presupuesto_empresa';

-- Crear el campo solo si no existe
SET @sql = IF(@column_exists = 0,
    'ALTER TABLE empresa 
     ADD COLUMN mostrar_cuenta_bancaria_pdf_presupuesto_empresa TINYINT(1) DEFAULT 1 
     COMMENT ''Mostrar cuenta bancaria en PDF si forma pago es transferencia: 1=Sí, 0=No'' 
     AFTER cabecera_firma_presupuesto_empresa',
    'SELECT ''El campo mostrar_cuenta_bancaria_pdf_presupuesto_empresa ya existe'' AS message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar el resultado
SELECT 
    COLUMN_NAME AS 'Campo',
    COLUMN_TYPE AS 'Tipo',
    COLUMN_DEFAULT AS 'Valor por Defecto',
    IS_NULLABLE AS 'Permite NULL',
    COLUMN_COMMENT AS 'Comentario'
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'toldos_db' 
AND TABLE_NAME = 'empresa'
AND COLUMN_NAME = 'mostrar_cuenta_bancaria_pdf_presupuesto_empresa';

-- Mensaje final
SELECT 'Migración completada exitosamente' AS 'Estado';
