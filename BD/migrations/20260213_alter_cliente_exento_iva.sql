-- ====================================================================
-- MIGRACIÓN: Punto 17 - Clientes Exentos de IVA - Operaciones Intracomunitarias
-- Fecha: 13 de febrero de 2026
-- Autor: Luis - Innovabyte
-- Descripción: Añade campos para gestionar exención de IVA en clientes intracomunitarios
-- ====================================================================

USE toldos_db;

-- Paso 1: Añadir campos a tabla cliente
ALTER TABLE cliente
ADD COLUMN exento_iva_cliente BOOLEAN DEFAULT FALSE COMMENT 'TRUE si el cliente está exento de IVA (operaciones intracomunitarias)' 
    AFTER observaciones_cliente,
ADD COLUMN justificacion_exencion_iva_cliente TEXT DEFAULT NULL COMMENT 'Justificación legal de la exención de IVA (ARt. 25 Ley 37/1992, etc.)'
    AFTER exento_iva_cliente;

-- Paso 2: Crear índice para mejorar consultas de clientes exentos
CREATE INDEX idx_exento_iva_cliente ON cliente(exento_iva_cliente);

-- Paso 3: Insertar comentario en historial de cambios (opcional)
-- Este registro queda en los logs del sistema

-- ====================================================================
-- VERIFICACIÓN DE LA MIGRACIÓN
-- ====================================================================
-- Para verificar que los cambios se aplicaron correctamente:
-- 
-- SELECT COLUMN_NAME, COLUMN_TYPE, COLUMN_DEFAULT, IS_NULLABLE, COLUMN_COMMENT
-- FROM INFORMATION_SCHEMA.COLUMNS
-- WHERE TABLE_SCHEMA = 'toldos_db' 
-- AND TABLE_NAME = 'cliente'
-- AND COLUMN_NAME IN ('exento_iva_cliente', 'justificacion_exencion_iva_cliente');
-- 
-- SHOW INDEX FROM cliente WHERE Key_name = 'idx_exento_iva_cliente';
-- ====================================================================

-- ====================================================================
-- ROLLBACK (usar solo en caso de necesitar revertir los cambios)
-- ====================================================================
-- ALTER TABLE cliente 
-- DROP COLUMN exento_iva_cliente,
-- DROP COLUMN justificacion_exencion_iva_cliente;
-- 
-- DROP INDEX idx_exento_iva_cliente ON cliente;
-- ====================================================================
