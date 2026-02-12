-- ========================================================
-- Migración: Añadir campos de observaciones por defecto
-- Fecha: 2026-02-12
-- Descripción: Campos para observaciones de cabecera
--              por defecto al crear nuevos presupuestos
-- ========================================================

-- 1. Añadir campos a tabla empresa
ALTER TABLE empresa
ADD COLUMN obs_esp TEXT NULL DEFAULT NULL
COMMENT 'Texto por defecto para observaciones de cabecera (español) en nuevos presupuestos'
AFTER configuracion_pdf_presupuesto_empresa;

ALTER TABLE empresa
ADD COLUMN obs_eng TEXT NULL DEFAULT NULL
COMMENT 'Texto por defecto para observaciones de cabecera (inglés) en nuevos presupuestos'
AFTER obs_esp;

-- 2. Establecer valor por defecto para empresas existentes
UPDATE empresa
SET obs_esp = 'Montaje de material audiovisual en regimen de alquiler'
WHERE obs_esp IS NULL
  AND activo_empresa = 1;

-- 3. Verificación
SELECT
    id_empresa,
    codigo_empresa,
    nombre_empresa,
    LEFT(COALESCE(obs_esp, '(sin valor)'), 50) as obs_esp,
    LEFT(COALESCE(obs_eng, '(sin valor)'), 50) as obs_eng
FROM empresa
WHERE activo_empresa = 1;
