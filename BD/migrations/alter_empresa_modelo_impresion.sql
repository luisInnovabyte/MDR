-- =====================================================
-- Script de Migración: Añadir modelo de impresión a empresa
-- =====================================================
-- Fecha: 10-02-2026
-- Descripción: Añade campo para seleccionar modelo de impresión
--              para presupuestos (m1_es o m2_es)
-- =====================================================

-- Añadir campo modelo_impresion_empresa a tabla empresa
ALTER TABLE empresa 
ADD COLUMN modelo_impresion_empresa VARCHAR(50) DEFAULT 'impresionpresupuesto_m1_es.php' 
COMMENT 'Archivo del modelo de impresión de presupuestos que usa la empresa'
AFTER texto_pie_presupuesto_empresa;

-- Añadir índice para búsquedas rápidas
CREATE INDEX idx_modelo_impresion ON empresa(modelo_impresion_empresa);

-- Actualizar empresas existentes con el modelo por defecto
UPDATE empresa 
SET modelo_impresion_empresa = 'impresionpresupuesto_m1_es.php'
WHERE modelo_impresion_empresa IS NULL;

-- =====================================================
-- Verificación
-- =====================================================
-- SELECT id_empresa, nombre_empresa, modelo_impresion_empresa FROM empresa;
