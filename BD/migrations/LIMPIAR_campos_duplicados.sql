-- ========================================================================
-- LIMPIEZA - Eliminar campos duplicados obs_esp y obs_eng
-- Fecha: 2026-02-12
-- ========================================================================
-- Estos campos son duplicados, estamos usando los nombres largos:
-- - observaciones_cabecera_presupuesto_empresa
-- - observaciones_cabecera_ingles_presupuesto_empresa
-- ========================================================================

-- 1. Verificar que existan los campos correctos antes de eliminar los duplicados
SELECT
    '=== VERIFICANDO CAMPOS CORRECTOS ===' as paso,
    COLUMN_NAME as campo_correcto
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'empresa'
  AND COLUMN_NAME IN ('observaciones_cabecera_presupuesto_empresa', 'observaciones_cabecera_ingles_presupuesto_empresa');

-- 2. Copiar datos de obs_esp a observaciones_cabecera_presupuesto_empresa (por si acaso)
UPDATE empresa
SET observaciones_cabecera_presupuesto_empresa = COALESCE(observaciones_cabecera_presupuesto_empresa, obs_esp)
WHERE obs_esp IS NOT NULL
  AND observaciones_cabecera_presupuesto_empresa IS NULL;

-- 3. Copiar datos de obs_eng a observaciones_cabecera_ingles_presupuesto_empresa (por si acaso)
UPDATE empresa
SET observaciones_cabecera_ingles_presupuesto_empresa = COALESCE(observaciones_cabecera_ingles_presupuesto_empresa, obs_eng)
WHERE obs_eng IS NOT NULL
  AND observaciones_cabecera_ingles_presupuesto_empresa IS NULL;

-- 4. Eliminar campo duplicado obs_esp
ALTER TABLE empresa DROP COLUMN obs_esp;

-- 5. Eliminar campo duplicado obs_eng
ALTER TABLE empresa DROP COLUMN obs_eng;

-- 6. Verificación final
SELECT
    '=== LIMPIEZA COMPLETADA ===' as resultado,
    id_empresa,
    codigo_empresa,
    LEFT(COALESCE(observaciones_cabecera_presupuesto_empresa, '(NULL)'), 50) as obs_esp,
    LEFT(COALESCE(observaciones_cabecera_ingles_presupuesto_empresa, '(NULL)'), 50) as obs_eng
FROM empresa
WHERE activo_empresa = 1;

-- ========================================================================
-- FIN DE LA LIMPIEZA
-- ========================================================================
