-- =============================================================================
-- MIGRACIÓN: Agregar campos exento_iva a vista contacto_cantidad_cliente
-- Fecha: 2026-02-13
-- Descripción: Actualiza la vista para incluir los campos de exención de IVA
--              del Punto 17 (exento_iva_cliente, justificacion_exencion_iva_cliente)
-- =============================================================================

USE toldos_db;

-- Eliminar vista existente
DROP VIEW IF EXISTS `contacto_cantidad_cliente`;

-- Recrear vista con campos de exento_iva incluidos
CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `contacto_cantidad_cliente` AS 
SELECT 
    -- Campos básicos del cliente
    `c`.`id_cliente` AS `id_cliente`,
    `c`.`codigo_cliente` AS `codigo_cliente`,
    `c`.`nombre_cliente` AS `nombre_cliente`,
    `c`.`direccion_cliente` AS `direccion_cliente`,
    `c`.`cp_cliente` AS `cp_cliente`,
    `c`.`poblacion_cliente` AS `poblacion_cliente`,
    `c`.`provincia_cliente` AS `provincia_cliente`,
    `c`.`nif_cliente` AS `nif_cliente`,
    `c`.`telefono_cliente` AS `telefono_cliente`,
    `c`.`fax_cliente` AS `fax_cliente`,
    `c`.`web_cliente` AS `web_cliente`,
    `c`.`email_cliente` AS `email_cliente`,
    
    -- Campos de facturación
    `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`,
    `c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`,
    `c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`,
    `c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`,
    `c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`,
    
    -- Otros campos
    `c`.`observaciones_cliente` AS `observaciones_cliente`,
    `c`.`activo_cliente` AS `activo_cliente`,
    `c`.`created_at_cliente` AS `created_at_cliente`,
    `c`.`updated_at_cliente` AS `updated_at_cliente`,
    `c`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`,
    `c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`,
    
    -- *** PUNTO 17: CAMPOS DE EXENCIÓN DE IVA ***
    `c`.`exento_iva_cliente` AS `exento_iva_cliente`,
    `c`.`justificacion_exencion_iva_cliente` AS `justificacion_exencion_iva_cliente`,
    
    -- Campos de forma de pago
    `fp`.`codigo_pago` AS `codigo_pago`,
    `fp`.`nombre_pago` AS `nombre_pago`,
    `fp`.`descuento_pago` AS `descuento_pago`,
    `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`,
    `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`,
    `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`,
    `fp`.`dias_final_pago` AS `dias_final_pago`,
    `fp`.`observaciones_pago` AS `observaciones_pago`,
    `fp`.`activo_pago` AS `activo_pago`,
    
    -- Campos de método de pago
    `mp`.`id_metodo_pago` AS `id_metodo_pago`,
    `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`,
    `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`,
    `mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`,
    `mp`.`activo_metodo_pago` AS `activo_metodo_pago`,
    
    -- Subconsulta: cantidad de contactos
    (SELECT COUNT(`cc`.`id_contacto_cliente`) 
     FROM `contacto_cliente` `cc` 
     WHERE `cc`.`id_cliente` = `c`.`id_cliente`) AS `cantidad_contactos_cliente`,
    
    -- Campos calculados: tipo de pago
    (CASE 
        WHEN `fp`.`porcentaje_anticipo_pago` = 100.00 THEN 'Pago único'
        WHEN `fp`.`porcentaje_anticipo_pago` < 100.00 THEN 'Pago fraccionado'
        ELSE 'Sin forma de pago'
    END) AS `tipo_pago_cliente`,
    
    -- Campos calculados: descripción forma de pago
    (CASE 
        WHEN `fp`.`id_pago` IS NULL THEN 'Sin forma de pago asignada'
        WHEN `fp`.`porcentaje_anticipo_pago` = 100.00 THEN 
            CONCAT(`mp`.`nombre_metodo_pago`, ' - ', `fp`.`nombre_pago`,
                CASE WHEN `fp`.`descuento_pago` > 0 
                    THEN CONCAT(' (Dto: ', `fp`.`descuento_pago`, '%)')
                    ELSE ''
                END)
        ELSE CONCAT(`mp`.`nombre_metodo_pago`, ' - ', `fp`.`porcentaje_anticipo_pago`, '% + ', `fp`.`porcentaje_final_pago`, '%')
    END) AS `descripcion_forma_pago_cliente`,
    
    -- Campos calculados: dirección completa
    CONCAT_WS(', ', `c`.`direccion_cliente`, 
              CONCAT(`c`.`cp_cliente`, ' ', `c`.`poblacion_cliente`), 
              `c`.`provincia_cliente`) AS `direccion_completa_cliente`,
    
    -- Campos calculados: dirección facturación completa
    (CASE 
        WHEN `c`.`direccion_facturacion_cliente` IS NOT NULL THEN 
            CONCAT_WS(', ', `c`.`direccion_facturacion_cliente`, 
                      CONCAT(`c`.`cp_facturacion_cliente`, ' ', `c`.`poblacion_facturacion_cliente`), 
                      `c`.`provincia_facturacion_cliente`)
        ELSE NULL
    END) AS `direccion_facturacion_completa_cliente`,
    
    -- Campos calculados: tiene dirección facturación diferente
    (CASE 
        WHEN `c`.`direccion_facturacion_cliente` IS NOT NULL THEN TRUE
        ELSE FALSE
    END) AS `tiene_direccion_facturacion_diferente`,
    
    -- Campos calculados: estado forma de pago
    (CASE 
        WHEN `c`.`id_forma_pago_habitual` IS NULL THEN 'Sin configurar'
        WHEN `fp`.`activo_pago` = FALSE THEN 'Forma de pago inactiva'
        WHEN `mp`.`activo_metodo_pago` = FALSE THEN 'Método de pago inactivo'
        ELSE 'Configurado'
    END) AS `estado_forma_pago_cliente`,
    
    -- Campos calculados: categoría descuento
    (CASE 
        WHEN `c`.`porcentaje_descuento_cliente` = 0.00 THEN 'Sin descuento'
        WHEN `c`.`porcentaje_descuento_cliente` > 0.00 AND `c`.`porcentaje_descuento_cliente` <= 5.00 THEN 'Descuento bajo'
        WHEN `c`.`porcentaje_descuento_cliente` > 5.00 AND `c`.`porcentaje_descuento_cliente` <= 15.00 THEN 'Descuento medio'
        WHEN `c`.`porcentaje_descuento_cliente` > 15.00 THEN 'Descuento alto'
        ELSE 'Sin descuento'
    END) AS `categoria_descuento_cliente`,
    
    -- Campos calculados: tiene descuento
    (CASE 
        WHEN `c`.`porcentaje_descuento_cliente` > 0.00 THEN TRUE
        ELSE FALSE
    END) AS `tiene_descuento_cliente`

FROM `cliente` `c`
LEFT JOIN `forma_pago` `fp` ON `c`.`id_forma_pago_habitual` = `fp`.`id_pago`
LEFT JOIN `metodo_pago` `mp` ON `fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`;

-- Verificar que la vista se creó correctamente
SELECT 'Vista contacto_cantidad_cliente actualizada correctamente' AS resultado;

-- Verificar que los nuevos campos están presentes
SHOW FULL COLUMNS FROM contacto_cantidad_cliente LIKE '%exento%';
