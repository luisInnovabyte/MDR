-- =====================================================================
-- Script de Verificación: Campo firma_comercial en tabla comerciales
-- Fecha: 2026-02-14
-- Descripción: Verifica que el campo firma_comercial existe y está correctamente configurado
-- =====================================================================

USE toldos_db;

-- 1. Verificar estructura del campo
SELECT 
    COLUMN_NAME AS 'Campo',
    DATA_TYPE AS 'Tipo',
    COLUMN_TYPE AS 'Tipo Completo',
    IS_NULLABLE AS 'Nulo',
    COLUMN_DEFAULT AS 'Valor por Defecto',
    COLUMN_COMMENT AS 'Comentario'
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'toldos_db' 
AND TABLE_NAME = 'comerciales'
AND COLUMN_NAME = 'firma_comercial';

-- 2. Contar comerciales con firma
SELECT 
    COUNT(*) AS 'Total Comerciales',
    SUM(CASE WHEN firma_comercial IS NOT NULL AND firma_comercial != '' THEN 1 ELSE 0 END) AS 'Con Firma',
    SUM(CASE WHEN firma_comercial IS NULL OR firma_comercial = '' THEN 1 ELSE 0 END) AS 'Sin Firma'
FROM comerciales
WHERE activo = 1;

-- 3. Ver comerciales con sus usuarios
SELECT 
    c.id_comercial,
    c.nombre,
    c.apellidos,
    c.id_usuario,
    u.email,
    CASE 
        WHEN c.firma_comercial IS NOT NULL AND c.firma_comercial != '' THEN 'SÍ'
        ELSE 'NO'
    END AS 'Tiene Firma',
    CASE 
        WHEN c.firma_comercial IS NOT NULL AND c.firma_comercial != '' 
        THEN CONCAT(LEFT(c.firma_comercial, 50), '...')
        ELSE NULL
    END AS 'Preview Firma',
    c.activo
FROM comerciales c
LEFT JOIN usuarios u ON c.id_usuario = u.id_usuario
WHERE c.activo = 1
ORDER BY c.id_comercial;

-- 4. Si el campo NO existe, ejecutar este ALTER TABLE:
-- ALTER TABLE comerciales 
-- ADD COLUMN firma_comercial TEXT 
-- COMMENT 'Firma digital del comercial en formato base64 PNG';
