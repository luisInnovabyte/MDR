-- =====================================================================
-- Migración: Agregar campo para controlar subtotales por fecha en PDF
-- Fecha: 2026-02-13
-- Descripción: Campo booleano para mostrar/ocultar "Subtotal Fecha XX/XX/XXXX" 
--              en impresión de presupuestos
-- =====================================================================

ALTER TABLE empresa
ADD COLUMN mostrar_subtotales_fecha_presupuesto_empresa BOOLEAN DEFAULT TRUE 
COMMENT 'Controla si se muestran subtotales por fecha en PDF de presupuestos. TRUE=mostrar, FALSE=ocultar';

-- Verificación
SELECT 'Campo agregado correctamente' AS resultado;
