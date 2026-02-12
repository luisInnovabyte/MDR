-- ========================================================================
-- SCRIPT RÁPIDO - VER VALORES obs_esp y obs_eng
-- ========================================================================
-- Copia y pega este script COMPLETO en tu gestor de base de datos
-- ========================================================================

-- Ver los valores actuales de todas las empresas activas
SELECT
    id_empresa,
    codigo_empresa,
    nombre_empresa,
    obs_esp,
    obs_eng
FROM empresa
WHERE activo_empresa = 1
ORDER BY id_empresa;

-- ========================================================================
-- Después de editar una empresa y guardar, ejecuta este script de nuevo
-- para ver si los valores cambiaron
-- ========================================================================
