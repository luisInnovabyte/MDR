-- ========================================================================
-- MIGRACIÓN URGENTE - Crear campos obs_esp y obs_eng
-- Fecha: 2026-02-12
-- ========================================================================
-- INSTRUCCIONES:
-- 1. Abre tu gestor de base de datos (phpMyAdmin, MySQL Workbench, etc.)
-- 2. Selecciona tu base de datos
-- 3. Copia y pega este script COMPLETO
-- 4. Ejecuta el script
-- ========================================================================

-- Paso 1: Crear campo obs_esp
ALTER TABLE empresa
ADD COLUMN obs_esp TEXT NULL DEFAULT NULL
COMMENT 'Texto por defecto para observaciones de cabecera (español) en nuevos presupuestos';

-- Paso 2: Crear campo obs_eng
ALTER TABLE empresa
ADD COLUMN obs_eng TEXT NULL DEFAULT NULL
COMMENT 'Texto por defecto para observaciones de cabecera (inglés) en nuevos presupuestos';

-- Paso 3: Establecer valor por defecto para empresas activas
UPDATE empresa
SET obs_esp = 'Montaje de material audiovisual en regimen de alquiler'
WHERE obs_esp IS NULL
  AND activo_empresa = 1;

-- Paso 4: Verificación - Ver los valores actuales
SELECT
    '=== VERIFICACIÓN EXITOSA ===' as resultado,
    COUNT(*) as total_empresas_activas,
    SUM(CASE WHEN obs_esp IS NOT NULL THEN 1 ELSE 0 END) as con_obs_esp,
    SUM(CASE WHEN obs_eng IS NOT NULL THEN 1 ELSE 0 END) as con_obs_eng
FROM empresa
WHERE activo_empresa = 1;

-- Paso 5: Ver datos de las empresas
SELECT
    id_empresa,
    codigo_empresa,
    nombre_empresa,
    LEFT(COALESCE(obs_esp, '(vacío)'), 50) as observacion_español,
    LEFT(COALESCE(obs_eng, '(vacío)'), 50) as observacion_ingles
FROM empresa
WHERE activo_empresa = 1
ORDER BY id_empresa;

-- ========================================================================
-- FIN DE LA MIGRACIÓN
-- ========================================================================
-- Si ves el mensaje "=== VERIFICACIÓN EXITOSA ===" y no hay errores,
-- la migración se completó correctamente.
-- ========================================================================
