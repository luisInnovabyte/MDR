-- ============================================
-- ACTUALIZACIÃƒ"N: VISTA v_linea_presupuesto_calculada
-- DescripciÃƒÂ³n: AÃƒÂ±adir campos calculados con descuento de hotel
-- Fecha: 2026-01-29
-- Autor: Luis MDR
-- Ticket: Campos para presupuestos con/sin descuento de hotel
-- ============================================

-- =====================================================
-- Eliminar vista existente
-- =====================================================
DROP VIEW IF EXISTS `v_linea_presupuesto_calculada`;

-- =====================================================
-- Crear vista actualizada con campos de hotel
-- =====================================================
CREATE OR REPLACE VIEW `v_linea_presupuesto_calculada` AS 
SELECT 
    -- =====================================================
    -- CAMPOS DE LINEA_PRESUPUESTO
    -- =====================================================
    `lp`.`id_linea_ppto`,
    `lp`.`id_version_presupuesto`,
    `lp`.`id_articulo`,
    `lp`.`id_linea_padre`,
    `lp`.`id_ubicacion`,
    `lp`.`numero_linea_ppto`,
    `lp`.`tipo_linea_ppto`,
    `lp`.`nivel_jerarquia`,
    `lp`.`codigo_linea_ppto`,
    `lp`.`descripcion_linea_ppto`,
    `lp`.`orden_linea_ppto`,
    `lp`.`observaciones_linea_ppto`,
    `lp`.`mostrar_obs_articulo_linea_ppto`,
    `lp`.`ocultar_detalle_kit_linea_ppto`,
    `lp`.`mostrar_en_presupuesto`,
    `lp`.`es_opcional`,
    `lp`.`activo_linea_ppto`,
    `lp`.`fecha_montaje_linea_ppto`,
    `lp`.`fecha_desmontaje_linea_ppto`,
    `lp`.`fecha_inicio_linea_ppto`,
    `lp`.`fecha_fin_linea_ppto`,
    `lp`.`cantidad_linea_ppto`,
    `lp`.`precio_unitario_linea_ppto`,
    `lp`.`descuento_linea_ppto`,
    `lp`.`porcentaje_iva_linea_ppto`,
    `lp`.`jornadas_linea_ppto`,
    `lp`.`id_coeficiente`,
    `lp`.`aplicar_coeficiente_linea_ppto`,
    `lp`.`valor_coeficiente_linea_ppto`,
    
    -- =====================================================
    -- CAMPOS DE COEFICIENTE (LEFT JOIN)
    -- =====================================================
    `c`.`jornadas_coeficiente`,
    `c`.`valor_coeficiente`,
    `c`.`observaciones_coeficiente`,
    `c`.`activo_coeficiente`,
    
    -- =====================================================
    -- CAMPOS CALCULADOS DE FECHAS Y CANTIDADES
    -- =====================================================
    CASE 
        WHEN `lp`.`fecha_inicio_linea_ppto` IS NOT NULL 
         AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL 
        THEN ((TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`)) + 1)
        ELSE 1
    END AS `dias_linea`,
    
    -- Subtotal sin aplicar coeficiente
    (
        (
            (
                CASE 
                    WHEN `lp`.`fecha_inicio_linea_ppto` IS NOT NULL 
                     AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL 
                    THEN ((TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`)) + 1)
                    ELSE 1
                END 
                * `lp`.`cantidad_linea_ppto`
            ) 
            * `lp`.`precio_unitario_linea_ppto`
        ) * (1 - (`lp`.`descuento_linea_ppto` / 100))
    ) AS `subtotal_sin_coeficiente`,
    
    -- Base imponible (con o sin coeficiente)
    CASE 
        WHEN `lp`.`aplicar_coeficiente_linea_ppto` = 1 
         AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL 
         AND `lp`.`valor_coeficiente_linea_ppto` > 0 
        THEN (
            (
                (`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) 
                * (1 - (`lp`.`descuento_linea_ppto` / 100))
            ) * `lp`.`valor_coeficiente_linea_ppto`
        )
        ELSE (
            (
                (
                    CASE 
                        WHEN `lp`.`fecha_inicio_linea_ppto` IS NOT NULL 
                         AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL 
                        THEN ((TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`)) + 1)
                        ELSE 1
                    END 
                    * `lp`.`cantidad_linea_ppto`
                ) 
                * `lp`.`precio_unitario_linea_ppto`
            ) * (1 - (`lp`.`descuento_linea_ppto` / 100))
        )
    END AS `base_imponible`,
    
    -- Importe IVA
    CASE 
        WHEN `lp`.`aplicar_coeficiente_linea_ppto` = 1 
         AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL 
         AND `lp`.`valor_coeficiente_linea_ppto` > 0 
        THEN (
            (
                (`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) 
                * (1 - (`lp`.`descuento_linea_ppto` / 100))
            ) * `lp`.`valor_coeficiente_linea_ppto`
        ) * (`lp`.`porcentaje_iva_linea_ppto` / 100)
        ELSE (
            (
                (
                    CASE 
                        WHEN `lp`.`fecha_inicio_linea_ppto` IS NOT NULL 
                         AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL 
                        THEN ((TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`)) + 1)
                        ELSE 1
                    END 
                    * `lp`.`cantidad_linea_ppto`
                ) 
                * `lp`.`precio_unitario_linea_ppto`
            ) * (1 - (`lp`.`descuento_linea_ppto` / 100))
        ) * (`lp`.`porcentaje_iva_linea_ppto` / 100)
    END AS `importe_iva`,
    
    -- Total lÃƒÂ­nea (base + IVA)
    CASE 
        WHEN `lp`.`aplicar_coeficiente_linea_ppto` = 1 
         AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL 
         AND `lp`.`valor_coeficiente_linea_ppto` > 0 
        THEN (
            (
                (`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) 
                * (1 - (`lp`.`descuento_linea_ppto` / 100))
            ) * `lp`.`valor_coeficiente_linea_ppto`
        ) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))
        ELSE (
            (
                (
                    CASE 
                        WHEN `lp`.`fecha_inicio_linea_ppto` IS NOT NULL 
                         AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL 
                        THEN ((TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`)) + 1)
                        ELSE 1
                    END 
                    * `lp`.`cantidad_linea_ppto`
                ) 
                * `lp`.`precio_unitario_linea_ppto`
            ) * (1 - (`lp`.`descuento_linea_ppto` / 100))
        ) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))
    END AS `total_linea`,
    
    -- =====================================================
    -- CAMPOS CALCULADOS CON DESCUENTO DE HOTEL
    -- Aplican descuento de cliente (hotel) si permitir_descuentos_articulo = 1
    -- =====================================================
    
    -- Precio unitario con descuento de hotel
    CASE 
        WHEN `a`.`permitir_descuentos_articulo` = 1 
        THEN `lp`.`precio_unitario_linea_ppto` - (
            (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
        )
        ELSE `lp`.`precio_unitario_linea_ppto`
    END AS `precio_unitario_linea_ppto_hotel`,
    
    -- Base imponible con descuento de hotel
    CASE 
        WHEN `a`.`permitir_descuentos_articulo` = 1 
        THEN (
            -- precio_unitario_linea_ppto_hotel
            (`lp`.`precio_unitario_linea_ppto` - (
                (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
            ))
            * `lp`.`valor_coeficiente_linea_ppto`
            * `lp`.`cantidad_linea_ppto`
        )
        ELSE (
            `lp`.`precio_unitario_linea_ppto`
            * `lp`.`valor_coeficiente_linea_ppto`
            * `lp`.`cantidad_linea_ppto`
        )
    END AS `base_imponible_hotel`,
    
    -- Importe descuento de lÃƒÂ­nea sobre base hotel
    CASE 
        WHEN `a`.`permitir_descuentos_articulo` = 1 
        THEN (
            -- base_imponible_hotel
            (
                (`lp`.`precio_unitario_linea_ppto` - (
                    (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                ))
                * `lp`.`valor_coeficiente_linea_ppto`
                * `lp`.`cantidad_linea_ppto`
            )
            * `lp`.`descuento_linea_ppto`
        ) / 100
        ELSE 0
    END AS `importe_descuento_linea_ppto_hotel`,
    
    -- Total con descuento de lÃƒÂ­nea aplicado
    CASE 
        WHEN `a`.`permitir_descuentos_articulo` = 1 
        THEN (
            -- base_imponible_hotel
            (
                (`lp`.`precio_unitario_linea_ppto` - (
                    (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                ))
                * `lp`.`valor_coeficiente_linea_ppto`
                * `lp`.`cantidad_linea_ppto`
            )
            - (
                -- importe_descuento_linea_ppto_hotel
                (
                    (
                        (`lp`.`precio_unitario_linea_ppto` - (
                            (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                        ))
                        * `lp`.`valor_coeficiente_linea_ppto`
                        * `lp`.`cantidad_linea_ppto`
                    )
                    * `lp`.`descuento_linea_ppto`
                ) / 100
            )
        )
        ELSE (
            `lp`.`precio_unitario_linea_ppto`
            * `lp`.`valor_coeficiente_linea_ppto`
            * `lp`.`cantidad_linea_ppto`
        )
    END AS `TotalImporte_descuento_linea_ppto_hotel`,
    
    -- Importe IVA sobre total con descuentos
    CASE 
        WHEN `a`.`permitir_descuentos_articulo` = 1 
        THEN (
            -- TotalImporte_descuento_linea_ppto_hotel
            (
                (
                    (`lp`.`precio_unitario_linea_ppto` - (
                        (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                    ))
                    * `lp`.`valor_coeficiente_linea_ppto`
                    * `lp`.`cantidad_linea_ppto`
                )
                - (
                    (
                        (
                            (`lp`.`precio_unitario_linea_ppto` - (
                                (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                            ))
                            * `lp`.`valor_coeficiente_linea_ppto`
                            * `lp`.`cantidad_linea_ppto`
                        )
                        * `lp`.`descuento_linea_ppto`
                    ) / 100
                )
            )
            * `lp`.`porcentaje_iva_linea_ppto`
        ) / 100
        ELSE (
            (
                `lp`.`precio_unitario_linea_ppto`
                * `lp`.`valor_coeficiente_linea_ppto`
                * `lp`.`cantidad_linea_ppto`
            )
            * `lp`.`porcentaje_iva_linea_ppto`
        ) / 100
    END AS `importe_iva_linea_ppto_hotel`,
    
    -- Total con IVA incluido (base con descuentos + IVA)
    CASE 
        WHEN `a`.`permitir_descuentos_articulo` = 1 
        THEN (
            -- TotalImporte_descuento_linea_ppto_hotel
            (
                (
                    (`lp`.`precio_unitario_linea_ppto` - (
                        (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                    ))
                    * `lp`.`valor_coeficiente_linea_ppto`
                    * `lp`.`cantidad_linea_ppto`
                )
                - (
                    (
                        (
                            (`lp`.`precio_unitario_linea_ppto` - (
                                (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                            ))
                            * `lp`.`valor_coeficiente_linea_ppto`
                            * `lp`.`cantidad_linea_ppto`
                        )
                        * `lp`.`descuento_linea_ppto`
                    ) / 100
                )
            )
            + (
                -- importe_iva_linea_ppto_hotel
                (
                    (
                        (
                            (`lp`.`precio_unitario_linea_ppto` - (
                                (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                            ))
                            * `lp`.`valor_coeficiente_linea_ppto`
                            * `lp`.`cantidad_linea_ppto`
                        )
                        - (
                            (
                                (
                                    (`lp`.`precio_unitario_linea_ppto` - (
                                        (`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100
                                    ))
                                    * `lp`.`valor_coeficiente_linea_ppto`
                                    * `lp`.`cantidad_linea_ppto`
                                )
                                * `lp`.`descuento_linea_ppto`
                            ) / 100
                        )
                    )
                    * `lp`.`porcentaje_iva_linea_ppto`
                ) / 100
            )
        )
        ELSE (
            (
                `lp`.`precio_unitario_linea_ppto`
                * `lp`.`valor_coeficiente_linea_ppto`
                * `lp`.`cantidad_linea_ppto`
            )
            + (
                (
                    `lp`.`precio_unitario_linea_ppto`
                    * `lp`.`valor_coeficiente_linea_ppto`
                    * `lp`.`cantidad_linea_ppto`
                )
                * `lp`.`porcentaje_iva_linea_ppto`
            ) / 100
        )
    END AS `TotalImporte_iva_linea_ppto_hotel`,
    
    -- =====================================================
    -- CAMPOS DE ARTICULO (desde vista_articulo_completa)
    -- =====================================================
    `a`.`codigo_articulo`,
    `a`.`nombre_articulo`,
    `a`.`name_articulo`,
    `a`.`imagen_articulo`,
    `a`.`precio_alquiler_articulo`,
    `a`.`coeficiente_articulo`,
    `a`.`es_kit_articulo`,
    `a`.`control_total_articulo`,
    `a`.`no_facturar_articulo`,
    `a`.`notas_presupuesto_articulo`,
    `a`.`notes_budget_articulo`,
    `a`.`orden_obs_articulo`,
    `a`.`observaciones_articulo`,
    `a`.`activo_articulo`,
    `a`.`permitir_descuentos_articulo`,
    
    -- Campos de impuesto del artÃƒÂ­culo
    `a`.`id_impuesto` AS `id_impuesto_articulo`,
    `a`.`tipo_impuesto` AS `tipo_impuesto_articulo`,
    `a`.`tasa_impuesto` AS `tasa_impuesto_articulo`,
    `a`.`descr_impuesto` AS `descr_impuesto_articulo`,
    `a`.`activo_impuesto_relacionado` AS `activo_impuesto_articulo`,
    
    -- Campos de unidad de medida
    `a`.`id_unidad`,
    `a`.`nombre_unidad`,
    `a`.`name_unidad`,
    `a`.`descr_unidad`,
    `a`.`simbolo_unidad`,
    `a`.`activo_unidad_relacionada` AS `activo_unidad`,
    
    -- =====================================================
    -- CAMPOS DE IMPUESTO DE LÃƒNEA (LEFT JOIN imp)
    -- =====================================================
    `lp`.`id_impuesto`,
    `imp`.`tipo_impuesto`,
    `imp`.`tasa_impuesto`,
    `imp`.`descr_impuesto`,
    `imp`.`activo_impuesto`,
    
    -- =====================================================
    -- CAMPOS DE PRESUPUESTO_VERSION
    -- =====================================================
    `pv`.`id_presupuesto`,
    `pv`.`numero_version_presupuesto`,
    `pv`.`estado_version_presupuesto`,
    `pv`.`fecha_creacion_version`,
    `pv`.`fecha_envio_version`,
    `pv`.`fecha_aprobacion_version`,
    
    -- =====================================================
    -- CAMPOS DE PRESUPUESTO (desde vista_presupuesto_completa)
    -- =====================================================
    
    -- Datos bÃƒÂ¡sicos del presupuesto
    `p`.`numero_presupuesto`,
    `p`.`version_actual_presupuesto`,
    `p`.`fecha_presupuesto`,
    `p`.`fecha_validez_presupuesto`,
    `p`.`fecha_inicio_evento_presupuesto`,
    `p`.`fecha_fin_evento_presupuesto`,
    `p`.`numero_pedido_cliente_presupuesto`,
    `p`.`aplicar_coeficientes_presupuesto`,
    `p`.`descuento_presupuesto`,
    `p`.`nombre_evento_presupuesto`,
    `p`.`direccion_evento_presupuesto`,
    `p`.`poblacion_evento_presupuesto`,
    `p`.`cp_evento_presupuesto`,
    `p`.`provincia_evento_presupuesto`,
    `p`.`observaciones_cabecera_presupuesto`,
    `p`.`observaciones_pie_presupuesto`,
    `p`.`observaciones_cabecera_ingles_presupuesto`,
    `p`.`observaciones_pie_ingles_presupuesto`,
    `p`.`mostrar_obs_familias_presupuesto`,
    `p`.`mostrar_obs_articulos_presupuesto`,
    `p`.`observaciones_internas_presupuesto`,
    `p`.`activo_presupuesto`,
    `p`.`created_at_presupuesto`,
    `p`.`updated_at_presupuesto`,
    
    -- Datos del cliente
    `p`.`id_cliente`,
    `p`.`codigo_cliente`,
    `p`.`nombre_cliente`,
    `p`.`nif_cliente`,
    `p`.`direccion_cliente`,
    `p`.`cp_cliente`,
    `p`.`poblacion_cliente`,
    `p`.`provincia_cliente`,
    `p`.`telefono_cliente`,
    `p`.`email_cliente`,
    `p`.`porcentaje_descuento_cliente`,
    `p`.`nombre_facturacion_cliente`,
    `p`.`direccion_facturacion_cliente`,
    `p`.`cp_facturacion_cliente`,
    `p`.`poblacion_facturacion_cliente`,
    `p`.`provincia_facturacion_cliente`,
    
    -- Datos del contacto del cliente
    `p`.`id_contacto_cliente`,
    `p`.`nombre_contacto_cliente`,
    `p`.`apellidos_contacto_cliente`,
    `p`.`telefono_contacto_cliente`,
    `p`.`email_contacto_cliente`,
    
    -- Estado del presupuesto
    `p`.`id_estado_ppto`,
    `p`.`codigo_estado_ppto`,
    `p`.`nombre_estado_ppto`,
    `p`.`color_estado_ppto`,
    `p`.`orden_estado_ppto`,
    
    -- Forma de pago
    `p`.`id_forma_pago`,
    `p`.`codigo_pago`,
    `p`.`nombre_pago`,
    `p`.`porcentaje_anticipo_pago`,
    `p`.`dias_anticipo_pago`,
    `p`.`porcentaje_final_pago`,
    `p`.`dias_final_pago`,
    `p`.`descuento_pago`,
    
    -- MÃƒÂ©todo de pago
    `p`.`id_metodo_pago`,
    `p`.`codigo_metodo_pago`,
    `p`.`nombre_metodo_pago`,
    
    -- MÃƒÂ©todo de contacto
    `p`.`id_metodo_contacto`,
    `p`.`nombre_metodo_contacto`,
    
    -- Forma de pago habitual del cliente
    `p`.`id_forma_pago_habitual`,
    `p`.`nombre_forma_pago_habitual_cliente`,
    
    -- =====================================================
    -- CAMPOS CALCULADOS DE VISTA_PRESUPUESTO_COMPLETA
    -- =====================================================
    `p`.`direccion_completa_evento_presupuesto`,
    `p`.`direccion_completa_cliente`,
    `p`.`direccion_facturacion_completa_cliente`,
    `p`.`nombre_completo_contacto`,
    `p`.`dias_validez_restantes`,
    `p`.`estado_validez_presupuesto`,
    `p`.`duracion_evento_dias`,
    `p`.`dias_hasta_inicio_evento`,
    `p`.`dias_hasta_fin_evento`,
    `p`.`estado_evento_presupuesto`,
    `p`.`prioridad_presupuesto`,
    `p`.`tipo_pago_presupuesto`,
    `p`.`descripcion_completa_forma_pago`,
    `p`.`fecha_vencimiento_anticipo`,
    `p`.`fecha_vencimiento_final`,
    `p`.`comparacion_descuento`,
    `p`.`estado_descuento_presupuesto`,
    `p`.`aplica_descuento_presupuesto`,
    `p`.`diferencia_descuento`,
    `p`.`tiene_direccion_facturacion_diferente`,
    `p`.`dias_desde_emision`,
    `p`.`id_version_actual`,
    `p`.`numero_version_actual`,
    `p`.`estado_version_actual`,
    `p`.`fecha_creacion_version_actual`,
    `p`.`estado_general_presupuesto`,
    
    -- =====================================================
    -- CAMPOS DE CLIENTE_UBICACION
    -- =====================================================
    `cu`.`nombre_ubicacion`,
    `cu`.`direccion_ubicacion`,
    `cu`.`codigo_postal_ubicacion`,
    `cu`.`poblacion_ubicacion`,
    `cu`.`provincia_ubicacion`,
    `cu`.`pais_ubicacion`,
    `cu`.`persona_contacto_ubicacion`,
    `cu`.`telefono_contacto_ubicacion`,
    `cu`.`email_contacto_ubicacion`,
    `cu`.`observaciones_ubicacion`,
    `cu`.`es_principal_ubicacion`,
    `cu`.`activo_ubicacion`,
    
    -- =====================================================
    -- CAMPOS CALCULADOS PARA AGRUPACIÃƒ"N EN DATATABLES
    -- =====================================================
    COALESCE(
        `cu`.`nombre_ubicacion`, 
        `p`.`nombre_evento_presupuesto`, 
        'Sin ubicaciÃƒÂ³n'
    ) AS `ubicacion_agrupacion`,
    
    COALESCE(
        CONCAT_WS(', ',
            `cu`.`nombre_ubicacion`,
            `cu`.`direccion_ubicacion`,
            CONCAT(`cu`.`codigo_postal_ubicacion`, ' ', `cu`.`poblacion_ubicacion`),
            `cu`.`provincia_ubicacion`
        ),
        `p`.`direccion_completa_evento_presupuesto`,
        'Sin ubicaciÃƒÂ³n'
    ) AS `ubicacion_completa_agrupacion`,
    
    -- =====================================================
    -- CAMPOS DE AUDITORÃƒA
    -- =====================================================
    `lp`.`created_at_linea_ppto`,
    `lp`.`updated_at_linea_ppto`
    
-- =====================================================
-- FROM Y JOINS
-- =====================================================
FROM `linea_presupuesto` `lp`

INNER JOIN `presupuesto_version` `pv` 
    ON `lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`

INNER JOIN `vista_presupuesto_completa` `p` 
    ON `pv`.`id_presupuesto` = `p`.`id_presupuesto`

LEFT JOIN `vista_articulo_completa` `a` 
    ON `lp`.`id_articulo` = `a`.`id_articulo`
    
LEFT JOIN `coeficiente` `c` 
    ON `lp`.`id_coeficiente` = `c`.`id_coeficiente`
    
LEFT JOIN `impuesto` `imp` 
    ON `lp`.`id_impuesto` = `imp`.`id_impuesto`
    
LEFT JOIN `cliente_ubicacion` `cu` 
    ON `lp`.`id_ubicacion` = `cu`.`id_ubicacion`

WHERE `p`.`activo_presupuesto` = TRUE;

-- =====================================================
-- VERIFICACIÃƒ"N
-- =====================================================

SELECT 'Vista v_linea_presupuesto_calculada actualizada correctamente con campos hotel' AS resultado;

-- Verificar campos hotel en un registro
SELECT 
    `id_linea_ppto`,
    `codigo_linea_ppto`,
    `descripcion_linea_ppto`,
    `permitir_descuentos_articulo`,
    `porcentaje_descuento_cliente`,
    `precio_unitario_linea_ppto`,
    `precio_unitario_linea_ppto_hotel`,
    `base_imponible`,
    `base_imponible_hotel`,
    `importe_descuento_linea_ppto_hotel`,
    `TotalImporte_descuento_linea_ppto_hotel`,
    `importe_iva_linea_ppto_hotel`,
    `TotalImporte_iva_linea_ppto_hotel`
FROM `v_linea_presupuesto_calculada`
LIMIT 5;