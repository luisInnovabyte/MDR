-- =====================================================================
-- Migración: Agregar campo para cabecera de firma en presupuestos
-- Fecha: 2026-02-13
-- Descripción: Campo de texto para personalizar la cabecera de la firma
--              en la impresión de presupuestos PDF
-- =====================================================================

USE toldos_db;

ALTER TABLE empresa
ADD COLUMN cabecera_firma_presupuesto_empresa VARCHAR(255) DEFAULT 'Departamento comercial' 
COMMENT 'Texto de cabecera para la firma en PDF de presupuestos';

-- Verificar que se agregó correctamente
SELECT 'Campo agregado correctamente' AS resultado;

-- Mostrar las empresas con el nuevo campo
SELECT id_empresa, nombre_empresa, cabecera_firma_presupuesto_empresa 
FROM empresa 
LIMIT 5;
