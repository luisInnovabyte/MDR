-- =====================================================
-- SCRIPT DE VERIFICACIÓN - PUNTO 17
-- Verificar campos exento_iva_cliente y justificacion_exencion_iva_cliente
-- =====================================================

USE toldos_db;

-- Verificar estructura de tabla cliente
DESCRIBE cliente;

-- Verificar específicamente los campos de exención IVA
SELECT 
    COLUMN_NAME,
    COLUMN_TYPE,
    IS_NULLABLE,
    COLUMN_DEFAULT,
    COLUMN_COMMENT
FROM 
    INFORMATION_SCHEMA.COLUMNS
WHERE 
    TABLE_SCHEMA = 'toldos_db'
    AND TABLE_NAME = 'cliente'
    AND COLUMN_NAME IN ('exento_iva_cliente', 'justificacion_exencion_iva_cliente');

-- Si retorna 0 filas, los campos NO EXISTEN y hay que ejecutar la migración:
-- source w:/MDR/BD/migrations/20260213_alter_cliente_exento_iva.sql

-- Verificar datos de prueba (si hay registros con exento_iva = 1)
SELECT 
    id_cliente,
    codigo_cliente,
    nombre_cliente,
    exento_iva_cliente,
    LEFT(justificacion_exencion_iva_cliente, 50) AS justificacion_preview
FROM 
    cliente
WHERE 
    exento_iva_cliente = 1
LIMIT 5;
