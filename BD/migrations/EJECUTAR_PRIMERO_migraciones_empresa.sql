-- ========================================================================
-- ARCHIVO DE MIGRACIONES COMBINADAS - EJECUTAR COMPLETO EN ORDEN
-- Fecha: 2026-02-12
-- Descripción: Migraciones necesarias para campos de configuración empresa
-- ========================================================================
--
-- IMPORTANTE: Este archivo debe ejecutarse COMPLETO en tu gestor de BD
--
-- ========================================================================

-- ========================================================
-- MIGRACIÓN 1: Campo configuracion_pdf_presupuesto_empresa
-- ========================================================

-- 1.1 Añadir campo configuracion_pdf a tabla empresa
ALTER TABLE empresa
ADD COLUMN configuracion_pdf_presupuesto_empresa TEXT NULL DEFAULT NULL
COMMENT 'Configuración JSON para personalizar PDFs de presupuesto'
AFTER modelo_impresion_empresa;

-- 1.2 Valores por defecto para todas las empresas existentes
UPDATE empresa
SET configuracion_pdf_presupuesto_empresa = '{
  "ocultar_cif_si_termina_en_0000": true,
  "texto_observacion_montaje": "Montaje ______ alquiler",
  "mostrar_subtotales_por_fecha": false,
  "formato_observaciones": "texto_completo",
  "texto_firma_departamento": "Departamento Comercial",
  "mostrar_descuento_detallado": true,
  "primera_linea_articulo_en_negrita": false
}'
WHERE configuracion_pdf_presupuesto_empresa IS NULL;

-- 1.3 Verificación migración 1
SELECT
    '*** VERIFICACIÓN MIGRACIÓN 1 - configuracion_pdf ***' as comprobacion,
    COUNT(*) as total_empresas,
    SUM(CASE WHEN configuracion_pdf_presupuesto_empresa IS NOT NULL THEN 1 ELSE 0 END) as empresas_configuradas
FROM empresa;

-- ========================================================
-- MIGRACIÓN 2: Campos obs_esp y obs_eng
-- ========================================================

-- 2.1 Añadir campo obs_esp
ALTER TABLE empresa
ADD COLUMN obs_esp TEXT NULL DEFAULT NULL
COMMENT 'Texto por defecto para observaciones de cabecera (español) en nuevos presupuestos'
AFTER configuracion_pdf_presupuesto_empresa;

-- 2.2 Añadir campo obs_eng
ALTER TABLE empresa
ADD COLUMN obs_eng TEXT NULL DEFAULT NULL
COMMENT 'Texto por defecto para observaciones de cabecera (inglés) en nuevos presupuestos'
AFTER obs_esp;

-- 2.3 Establecer valor por defecto para empresas existentes
UPDATE empresa
SET obs_esp = 'Montaje de material audiovisual en regimen de alquiler'
WHERE obs_esp IS NULL
  AND activo_empresa = 1;

-- 2.4 Verificación migración 2
SELECT
    '*** VERIFICACIÓN MIGRACIÓN 2 - obs_esp/obs_eng ***' as comprobacion,
    id_empresa,
    codigo_empresa,
    nombre_empresa,
    LEFT(COALESCE(obs_esp, '(sin valor)'), 50) as obs_esp_muestra,
    LEFT(COALESCE(obs_eng, '(sin valor)'), 50) as obs_eng_muestra
FROM empresa
WHERE activo_empresa = 1;

-- ========================================================
-- VERIFICACIÓN FINAL DE ESTRUCTURA
-- ========================================================

SELECT '*** VERIFICACIÓN FINAL - ESTRUCTURA TABLA ***' as comprobacion;
DESCRIBE empresa;

-- ========================================================
-- FIN DE MIGRACIONES
-- ========================================================
-- Si todo se ejecutó correctamente, deberías ver:
-- - configuracion_pdf_presupuesto_empresa (TEXT)
-- - obs_esp (TEXT)
-- - obs_eng (TEXT)
-- ========================================================
