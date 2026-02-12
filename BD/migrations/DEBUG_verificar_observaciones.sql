-- ========================================================================
-- SCRIPT DEBUG - Verificar observaciones por defecto
-- ========================================================================

-- 1. Ver qué empresa tiene el flag empresa_ficticia_principal = 1
SELECT
    '=== EMPRESA FICTICIA PRINCIPAL ===' as info,
    id_empresa,
    codigo_empresa,
    nombre_empresa,
    empresa_ficticia_principal,
    activo_empresa
FROM empresa
WHERE empresa_ficticia_principal = 1;

-- 2. Ver los valores de obs_esp y obs_eng de la empresa principal
SELECT
    '=== OBSERVACIONES DE LA EMPRESA PRINCIPAL ===' as info,
    id_empresa,
    codigo_empresa,
    obs_esp,
    obs_eng
FROM empresa
WHERE empresa_ficticia_principal = 1
  AND activo_empresa = 1;

-- 3. Si no hay empresa principal, ver todas las empresas activas
SELECT
    '=== TODAS LAS EMPRESAS ACTIVAS ===' as info,
    id_empresa,
    codigo_empresa,
    nombre_empresa,
    empresa_ficticia_principal,
    LEFT(COALESCE(obs_esp, '(NULL)'), 50) as obs_esp,
    LEFT(COALESCE(obs_eng, '(NULL)'), 50) as obs_eng
FROM empresa
WHERE activo_empresa = 1
ORDER BY empresa_ficticia_principal DESC, id_empresa;
