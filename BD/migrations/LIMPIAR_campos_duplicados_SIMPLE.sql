-- ========================================================================
-- LIMPIEZA SIMPLE - Eliminar campos duplicados
-- Fecha: 2026-02-12
-- ========================================================================
-- Ejecuta este script COMPLETO (todos los statements juntos)
-- ========================================================================

-- 1. Copiar datos de obs_esp a observaciones_cabecera_presupuesto_empresa (por seguridad)
UPDATE empresa
SET observaciones_cabecera_presupuesto_empresa = obs_esp
WHERE obs_esp IS NOT NULL
  AND (observaciones_cabecera_presupuesto_empresa IS NULL
       OR observaciones_cabecera_presupuesto_empresa = '');

-- 2. Copiar datos de obs_eng a observaciones_cabecera_ingles_presupuesto_empresa (por seguridad)
UPDATE empresa
SET observaciones_cabecera_ingles_presupuesto_empresa = obs_eng
WHERE obs_eng IS NOT NULL
  AND (observaciones_cabecera_ingles_presupuesto_empresa IS NULL
       OR observaciones_cabecera_ingles_presupuesto_empresa = '');

-- 3. Eliminar campo duplicado obs_esp
ALTER TABLE empresa DROP COLUMN obs_esp;

-- 4. Eliminar campo duplicado obs_eng
ALTER TABLE empresa DROP COLUMN obs_eng;

-- 5. Verificación - Ver los datos finales
SELECT
    id_empresa,
    codigo_empresa,
    nombre_empresa,
    LEFT(COALESCE(observaciones_cabecera_presupuesto_empresa, '(vacío)'), 50) as obs_español,
    LEFT(COALESCE(observaciones_cabecera_ingles_presupuesto_empresa, '(vacío)'), 50) as obs_inglés
FROM empresa
WHERE activo_empresa = 1;

-- ========================================================================
-- FIN - Si ves tus empresas con los datos correctos, todo está bien
-- ========================================================================
