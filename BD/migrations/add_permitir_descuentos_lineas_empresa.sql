-- Migración: Añadir campo permitir_descuentos_lineas_empresa a la tabla empresa
-- Fecha: 2026-02-23
-- Descripción: Controla si se permiten descuentos en líneas de presupuesto.
--              Si es 0: el campo %Descuento queda bloqueado a 0 en la vista de líneas
--              y la columna/fila de descuento no se muestra en el PDF.
--              Si es 1 (por defecto): comportamiento actual sin cambios.

ALTER TABLE empresa 
ADD COLUMN permitir_descuentos_lineas_empresa TINYINT(1) NOT NULL DEFAULT 1 
COMMENT 'Si 0: %Descuento bloqueado a 0 en líneas y oculto en PDF. Si 1 (default): comportamiento estándar.'
AFTER obs_linea_alineadas_descripcion_empresa;
