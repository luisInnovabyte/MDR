-- ========================================================================
-- SCRIPT DE VERIFICACIÓN - CAMPOS EN TABLA EMPRESA
-- ========================================================================
-- Ejecuta este script para verificar si los campos necesarios existen
-- ========================================================================

-- 1. Verificar estructura completa de la tabla empresa
SELECT
    '=== ESTRUCTURA TABLA EMPRESA ===' as verificacion;

DESCRIBE empresa;

-- 2. Buscar específicamente los 3 campos nuevos
SELECT
    '=== VERIFICACIÓN CAMPOS ESPECÍFICOS ===' as verificacion;

SELECT
    COLUMN_NAME as campo,
    DATA_TYPE as tipo,
    IS_NULLABLE as permite_null,
    COLUMN_DEFAULT as valor_defecto,
    COLUMN_COMMENT as comentario
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'empresa'
  AND COLUMN_NAME IN ('configuracion_pdf_presupuesto_empresa', 'obs_esp', 'obs_eng')
ORDER BY ORDINAL_POSITION;

-- 3. Contar cuántos de los 3 campos existen
SELECT
    '=== RESUMEN ===' as verificacion,
    COUNT(*) as campos_encontrados,
    CASE
        WHEN COUNT(*) = 3 THEN 'TODOS LOS CAMPOS EXISTEN ✓'
        WHEN COUNT(*) = 2 THEN 'FALTA 1 CAMPO ✗'
        WHEN COUNT(*) = 1 THEN 'FALTAN 2 CAMPOS ✗'
        ELSE 'FALTAN TODOS LOS CAMPOS ✗✗✗'
    END as estado
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'empresa'
  AND COLUMN_NAME IN ('configuracion_pdf_presupuesto_empresa', 'obs_esp', 'obs_eng');

-- 4. Si existen, mostrar datos actuales de una empresa
SELECT
    '=== DATOS ACTUALES EMPRESAS ===' as verificacion;

SELECT
    id_empresa,
    codigo_empresa,
    LEFT(COALESCE(configuracion_pdf_presupuesto_empresa, '(NULL)'), 30) as config_pdf,
    LEFT(COALESCE(obs_esp, '(NULL)'), 50) as observacion_esp,
    LEFT(COALESCE(obs_eng, '(NULL)'), 50) as observacion_eng
FROM empresa
WHERE activo_empresa = 1
LIMIT 5;

-- ========================================================================
-- INTERPRETACIÓN DE RESULTADOS:
-- ========================================================================
-- Si "campos_encontrados" es 3 → Todo OK, campos existen
-- Si "campos_encontrados" es 0, 1 o 2 → DEBES ejecutar el archivo:
--    BD/migrations/EJECUTAR_PRIMERO_migraciones_empresa.sql
-- ========================================================================
