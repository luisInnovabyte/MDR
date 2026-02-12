-- ========================================================
-- Migración: Añadir campo de configuración PDF de presupuestos
-- Fecha: 2026-02-12
-- Descripción: Campo JSON para almacenar configuraciones
--              personalizables del PDF de presupuestos
-- ========================================================

-- 1. Añadir campo a tabla empresa
ALTER TABLE empresa
ADD COLUMN configuracion_pdf_presupuesto_empresa TEXT NULL DEFAULT NULL
COMMENT 'Configuración JSON para personalizar PDFs de presupuesto'
AFTER modelo_impresion_empresa;

-- 2. Valores por defecto para todas las empresas existentes
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

-- 3. Verificación
SELECT
    id_empresa,
    codigo_empresa,
    CASE
        WHEN configuracion_pdf_presupuesto_empresa IS NULL THEN 'SIN CONFIGURACIÓN'
        ELSE 'CONFIGURADO'
    END as estado_config
FROM empresa;
