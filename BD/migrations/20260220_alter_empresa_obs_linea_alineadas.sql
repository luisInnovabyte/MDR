-- ====================================================================
-- MIGRACIÓN: Alineación de observaciones de línea bajo columna Descripción en PDF
-- Fecha: 20 de febrero de 2026
-- Autor: Luis - Innovabyte
-- Descripción: Añade campo switch en la tabla empresa para controlar si las
--              observaciones de línea de artículo en el PDF del presupuesto
--              se imprimen desde el margen izquierdo (0) o alineadas bajo la
--              columna "Descripción" (1).
-- ====================================================================

USE toldos_db;

ALTER TABLE empresa
ADD COLUMN obs_linea_alineadas_descripcion_empresa TINYINT(1) NOT NULL DEFAULT 0
COMMENT 'Alinear obs. de línea bajo columna Descripción en PDF: 1=Sí, 0=No (margen izq.)'
AFTER mostrar_obs_pie_albaran_empresa;

-- ====================================================================
-- VERIFICACIÓN
-- ====================================================================
SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, IS_NULLABLE, COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'toldos_db'
AND TABLE_NAME = 'empresa'
AND COLUMN_NAME = 'obs_linea_alineadas_descripcion_empresa';

-- ====================================================================
-- ROLLBACK
-- ====================================================================
-- ALTER TABLE empresa DROP COLUMN obs_linea_alineadas_descripcion_empresa;
-- ====================================================================
