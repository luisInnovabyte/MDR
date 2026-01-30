-- ============================================
-- Vista: v_linea_presupuesto_calculada
-- Descripción: Líneas de presupuesto con cálculos automáticos
-- Fecha modificación: 2025-01-30
-- Cambio 1: descuento_linea_ppto SIEMPRE se aplica en campos _hotel
-- Cambio 2: campos _hotel ahora calculan días cuando NO hay coeficiente
-- Cambio 3: Incluidos TODOS los campos de vista_articulo_completa
-- Cambio 4: Añadidos mostrar_obs_familias_presupuesto y mostrar_obs_articulos_presupuesto
-- ============================================

DROP VIEW IF EXISTS `v_linea_presupuesto_calculada`;

CREATE ALGORITHM=UNDEFINED 
DEFINER=`root`@`%` 
SQL SECURITY DEFINER 
VIEW `v_linea_presupuesto_calculada` AS

SELECT 
    -- =====================================================
    -- CAMPOS DIRECTOS DE LÍNEA PRESUPUESTO
    -- =====================================================
    lp.id_linea_ppto,
    lp.id_version_presupuesto,
    lp.id_articulo,
    lp.id_linea_padre,
    lp.id_ubicacion,
    lp.numero_linea_ppto,
    lp.tipo_linea_ppto,
    lp.nivel_jerarquia,
    lp.codigo_linea_ppto,
    lp.descripcion_linea_ppto,
    lp.orden_linea_ppto,
    lp.observaciones_linea_ppto,
    lp.mostrar_obs_articulo_linea_ppto,
    lp.ocultar_detalle_kit_linea_ppto,
    lp.mostrar_en_presupuesto,
    lp.es_opcional,
    lp.activo_linea_ppto,
    lp.fecha_montaje_linea_ppto,
    lp.fecha_desmontaje_linea_ppto,
    lp.fecha_inicio_linea_ppto,
    lp.fecha_fin_linea_ppto,
    lp.cantidad_linea_ppto,
    lp.precio_unitario_linea_ppto,
    lp.descuento_linea_ppto,
    lp.porcentaje_iva_linea_ppto,
    lp.jornadas_linea_ppto,
    lp.id_coeficiente,
    lp.aplicar_coeficiente_linea_ppto,
    lp.valor_coeficiente_linea_ppto,
    
    -- =====================================================
    -- CAMPOS DE COEFICIENTE
    -- =====================================================
    c.jornadas_coeficiente,
    c.valor_coeficiente,
    c.observaciones_coeficiente,
    c.activo_coeficiente,
    
    -- =====================================================
    -- CÁLCULOS ESTÁNDAR (sin descuento de cliente)
    -- =====================================================
    
    -- Días del evento (desde linea, no desde presupuesto)
    CASE 
        WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
         AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
            (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
        ELSE 1
    END AS dias_linea,
    
    -- Subtotal sin coeficiente
    (
        (
            CASE 
                WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                 AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                    (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                ELSE 1
            END
            * lp.cantidad_linea_ppto
        ) 
        * lp.precio_unitario_linea_ppto
    ) * (1 - (lp.descuento_linea_ppto / 100)) AS subtotal_sin_coeficiente,
    
    -- Base imponible
    CASE 
        WHEN lp.aplicar_coeficiente_linea_ppto = 1 
         AND lp.valor_coeficiente_linea_ppto IS NOT NULL 
         AND lp.valor_coeficiente_linea_ppto > 0 THEN
            (
                (lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto) 
                * (1 - (lp.descuento_linea_ppto / 100))
            ) * lp.valor_coeficiente_linea_ppto
        ELSE
            (
                (
                    CASE 
                        WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                         AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                            (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                        ELSE 1
                    END
                    * lp.cantidad_linea_ppto
                ) 
                * lp.precio_unitario_linea_ppto
            ) * (1 - (lp.descuento_linea_ppto / 100))
    END AS base_imponible,
    
    -- Importe IVA
    CASE 
        WHEN lp.aplicar_coeficiente_linea_ppto = 1 
         AND lp.valor_coeficiente_linea_ppto IS NOT NULL 
         AND lp.valor_coeficiente_linea_ppto > 0 THEN
            (
                (
                    (lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto) 
                    * (1 - (lp.descuento_linea_ppto / 100))
                ) * lp.valor_coeficiente_linea_ppto
            ) * (lp.porcentaje_iva_linea_ppto / 100)
        ELSE
            (
                (
                    (
                        CASE 
                            WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                             AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                            ELSE 1
                        END
                        * lp.cantidad_linea_ppto
                    ) 
                    * lp.precio_unitario_linea_ppto
                ) * (1 - (lp.descuento_linea_ppto / 100))
            ) * (lp.porcentaje_iva_linea_ppto / 100)
    END AS importe_iva,
    
    -- Total línea
    CASE 
        WHEN lp.aplicar_coeficiente_linea_ppto = 1 
         AND lp.valor_coeficiente_linea_ppto IS NOT NULL 
         AND lp.valor_coeficiente_linea_ppto > 0 THEN
            (
                (
                    (lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto) 
                    * (1 - (lp.descuento_linea_ppto / 100))
                ) * lp.valor_coeficiente_linea_ppto
            ) * (1 + (lp.porcentaje_iva_linea_ppto / 100))
        ELSE
            (
                (
                    (
                        CASE 
                            WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                             AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                            ELSE 1
                        END
                        * lp.cantidad_linea_ppto
                    ) 
                    * lp.precio_unitario_linea_ppto
                ) * (1 - (lp.descuento_linea_ppto / 100))
            ) * (1 + (lp.porcentaje_iva_linea_ppto / 100))
    END AS total_linea,
    
    -- =====================================================
    -- CÁLCULOS "HOTEL" (CON descuento de cliente)
    -- MODIFICADO: Aplica días cuando NO hay coeficiente
    -- MODIFICADO: descuento_linea_ppto SIEMPRE se aplica
    -- =====================================================
    
    -- 1. Precio unitario hotel (con descuento de cliente)
    CASE 
        WHEN a.permitir_descuentos_articulo = 1 THEN
            lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100)
        ELSE
            lp.precio_unitario_linea_ppto
    END AS precio_unitario_linea_ppto_hotel,
    
    -- 2. Base imponible hotel (ANTES de descuento_linea_ppto)
    CASE 
        -- SI HAY COEFICIENTE: usa coeficiente
        WHEN lp.aplicar_coeficiente_linea_ppto = 1 
         AND lp.valor_coeficiente_linea_ppto IS NOT NULL 
         AND lp.valor_coeficiente_linea_ppto > 0 THEN
            CASE 
                WHEN a.permitir_descuentos_articulo = 1 THEN
                    (
                        (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                        * lp.valor_coeficiente_linea_ppto
                    ) * lp.cantidad_linea_ppto
                ELSE
                    (lp.precio_unitario_linea_ppto * lp.valor_coeficiente_linea_ppto) * lp.cantidad_linea_ppto
            END
        -- SI NO HAY COEFICIENTE: usa días
        ELSE
            CASE 
                WHEN a.permitir_descuentos_articulo = 1 THEN
                    (
                        (
                            CASE 
                                WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                 AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                    (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                ELSE 1
                            END
                            * lp.cantidad_linea_ppto
                        ) 
                        * (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                    )
                ELSE
                    (
                        CASE 
                            WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                             AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                            ELSE 1
                        END
                        * lp.cantidad_linea_ppto
                    ) * lp.precio_unitario_linea_ppto
            END
    END AS base_imponible_hotel,
    
    -- 3. Importe descuento línea hotel (SIEMPRE SE APLICA)
    (
        CASE 
            -- SI HAY COEFICIENTE
            WHEN lp.aplicar_coeficiente_linea_ppto = 1 
             AND lp.valor_coeficiente_linea_ppto IS NOT NULL 
             AND lp.valor_coeficiente_linea_ppto > 0 THEN
                CASE 
                    WHEN a.permitir_descuentos_articulo = 1 THEN
                        (
                            (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                            * lp.valor_coeficiente_linea_ppto
                        ) * lp.cantidad_linea_ppto
                    ELSE
                        (lp.precio_unitario_linea_ppto * lp.valor_coeficiente_linea_ppto) * lp.cantidad_linea_ppto
                END
            -- SI NO HAY COEFICIENTE
            ELSE
                CASE 
                    WHEN a.permitir_descuentos_articulo = 1 THEN
                        (
                            (
                                CASE 
                                    WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                     AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                        (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                    ELSE 1
                                END
                                * lp.cantidad_linea_ppto
                            ) 
                            * (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                        )
                    ELSE
                        (
                            CASE 
                                WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                 AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                    (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                ELSE 1
                            END
                            * lp.cantidad_linea_ppto
                        ) * lp.precio_unitario_linea_ppto
                END
        END
        * lp.descuento_linea_ppto
    ) / 100 AS importe_descuento_linea_ppto_hotel,
    
    -- 4. Total después del descuento de línea hotel (SIEMPRE SE APLICA)
    CASE 
        -- SI HAY COEFICIENTE
        WHEN lp.aplicar_coeficiente_linea_ppto = 1 
         AND lp.valor_coeficiente_linea_ppto IS NOT NULL 
         AND lp.valor_coeficiente_linea_ppto > 0 THEN
            CASE 
                WHEN a.permitir_descuentos_articulo = 1 THEN
                    (
                        (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                        * lp.valor_coeficiente_linea_ppto
                    ) * lp.cantidad_linea_ppto
                    * (1 - (lp.descuento_linea_ppto / 100))
                ELSE
                    (lp.precio_unitario_linea_ppto * lp.valor_coeficiente_linea_ppto) 
                    * lp.cantidad_linea_ppto
                    * (1 - (lp.descuento_linea_ppto / 100))
            END
        -- SI NO HAY COEFICIENTE
        ELSE
            CASE 
                WHEN a.permitir_descuentos_articulo = 1 THEN
                    (
                        (
                            CASE 
                                WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                 AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                    (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                ELSE 1
                            END
                            * lp.cantidad_linea_ppto
                        ) 
                        * (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                    )
                    * (1 - (lp.descuento_linea_ppto / 100))
                ELSE
                    (
                        (
                            CASE 
                                WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                 AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                    (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                ELSE 1
                            END
                            * lp.cantidad_linea_ppto
                        ) 
                        * lp.precio_unitario_linea_ppto
                    )
                    * (1 - (lp.descuento_linea_ppto / 100))
            END
    END AS TotalImporte_descuento_linea_ppto_hotel,
    
    -- 5. Importe IVA hotel
    (
        CASE 
            -- SI HAY COEFICIENTE
            WHEN lp.aplicar_coeficiente_linea_ppto = 1 
             AND lp.valor_coeficiente_linea_ppto IS NOT NULL 
             AND lp.valor_coeficiente_linea_ppto > 0 THEN
                CASE 
                    WHEN a.permitir_descuentos_articulo = 1 THEN
                        (
                            (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                            * lp.valor_coeficiente_linea_ppto
                        ) * lp.cantidad_linea_ppto
                        * (1 - (lp.descuento_linea_ppto / 100))
                    ELSE
                        (lp.precio_unitario_linea_ppto * lp.valor_coeficiente_linea_ppto) 
                        * lp.cantidad_linea_ppto
                        * (1 - (lp.descuento_linea_ppto / 100))
                END
            -- SI NO HAY COEFICIENTE
            ELSE
                CASE 
                    WHEN a.permitir_descuentos_articulo = 1 THEN
                        (
                            (
                                CASE 
                                    WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                     AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                        (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                    ELSE 1
                                END
                                * lp.cantidad_linea_ppto
                            ) 
                            * (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                        )
                        * (1 - (lp.descuento_linea_ppto / 100))
                    ELSE
                        (
                            (
                                CASE 
                                    WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                     AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                        (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                    ELSE 1
                                END
                                * lp.cantidad_linea_ppto
                            ) 
                            * lp.precio_unitario_linea_ppto
                        )
                        * (1 - (lp.descuento_linea_ppto / 100))
                END
        END
        * lp.porcentaje_iva_linea_ppto
    ) / 100 AS importe_iva_linea_ppto_hotel,
    
    -- 6. Total con IVA hotel
    CASE 
        -- SI HAY COEFICIENTE
        WHEN lp.aplicar_coeficiente_linea_ppto = 1 
         AND lp.valor_coeficiente_linea_ppto IS NOT NULL 
         AND lp.valor_coeficiente_linea_ppto > 0 THEN
            CASE 
                WHEN a.permitir_descuentos_articulo = 1 THEN
                    (
                        (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                        * lp.valor_coeficiente_linea_ppto
                    ) * lp.cantidad_linea_ppto
                    * (1 - (lp.descuento_linea_ppto / 100))
                    * (1 + (lp.porcentaje_iva_linea_ppto / 100))
                ELSE
                    (lp.precio_unitario_linea_ppto * lp.valor_coeficiente_linea_ppto) 
                    * lp.cantidad_linea_ppto
                    * (1 - (lp.descuento_linea_ppto / 100))
                    * (1 + (lp.porcentaje_iva_linea_ppto / 100))
            END
        -- SI NO HAY COEFICIENTE
        ELSE
            CASE 
                WHEN a.permitir_descuentos_articulo = 1 THEN
                    (
                        (
                            CASE 
                                WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                 AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                    (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                ELSE 1
                            END
                            * lp.cantidad_linea_ppto
                        ) 
                        * (lp.precio_unitario_linea_ppto - ((lp.precio_unitario_linea_ppto * p.porcentaje_descuento_cliente) / 100))
                    )
                    * (1 - (lp.descuento_linea_ppto / 100))
                    * (1 + (lp.porcentaje_iva_linea_ppto / 100))
                ELSE
                    (
                        (
                            CASE 
                                WHEN lp.fecha_inicio_linea_ppto IS NOT NULL 
                                 AND lp.fecha_fin_linea_ppto IS NOT NULL THEN
                                    (TO_DAYS(lp.fecha_fin_linea_ppto) - TO_DAYS(lp.fecha_inicio_linea_ppto)) + 1
                                ELSE 1
                            END
                            * lp.cantidad_linea_ppto
                        ) 
                        * lp.precio_unitario_linea_ppto
                    )
                    * (1 - (lp.descuento_linea_ppto / 100))
                    * (1 + (lp.porcentaje_iva_linea_ppto / 100))
            END
    END AS TotalImporte_iva_linea_ppto_hotel,
    
    -- =====================================================
    -- CAMPOS DE ARTÍCULO (vista_articulo_completa)
    -- TODOS LOS CAMPOS INCLUIDOS
    -- =====================================================
    
    -- Campos base del artículo
    a.codigo_articulo,
    a.nombre_articulo,
    a.name_articulo,
    a.imagen_articulo,
    a.precio_alquiler_articulo,
    a.coeficiente_articulo,
    a.es_kit_articulo,
    a.control_total_articulo,
    a.no_facturar_articulo,
    a.notas_presupuesto_articulo,
    a.notes_budget_articulo,
    a.orden_obs_articulo,
    a.observaciones_articulo,
    a.activo_articulo,
    a.permitir_descuentos_articulo,
    
    -- Campos adicionales del artículo
    a.id_familia,
    a.created_at_articulo,
    a.updated_at_articulo,
    
    -- Impuesto del artículo
    a.id_impuesto AS id_impuesto_articulo,
    a.tipo_impuesto AS tipo_impuesto_articulo,
    a.tasa_impuesto AS tasa_impuesto_articulo,
    a.descr_impuesto AS descr_impuesto_articulo,
    a.activo_impuesto_relacionado AS activo_impuesto_articulo,
    
    -- Unidad de medida del artículo
    a.id_unidad,
    a.nombre_unidad,
    a.name_unidad,
    a.descr_unidad,
    a.simbolo_unidad,
    a.activo_unidad_relacionada AS activo_unidad,
    
    -- Campos de FAMILIA del artículo
    a.id_grupo,
    a.codigo_familia,
    a.nombre_familia,
    a.name_familia,
    a.descr_familia,
    a.imagen_familia,
    a.coeficiente_familia,
    a.observaciones_presupuesto_familia,
    a.observations_budget_familia,
    a.orden_obs_familia,
    a.permite_descuento_familia,
    a.activo_familia_relacionada,
    
    -- =====================================================
    -- CAMPOS DE IMPUESTO DE LÍNEA (tabla impuesto)
    -- =====================================================
    lp.id_impuesto,
    imp.tipo_impuesto,
    imp.tasa_impuesto,
    imp.descr_impuesto,
    imp.activo_impuesto,
    
    -- =====================================================
    -- CAMPOS DE VERSIÓN PRESUPUESTO
    -- =====================================================
    pv.id_presupuesto,
    pv.numero_version_presupuesto,
    pv.estado_version_presupuesto,
    pv.fecha_creacion_version,
    pv.fecha_envio_version,
    pv.fecha_aprobacion_version,
    
    -- =====================================================
    -- CAMPOS DE VISTA_PRESUPUESTO_COMPLETA
    -- =====================================================
    p.numero_presupuesto,
    p.fecha_presupuesto,
    p.fecha_validez_presupuesto,
    p.nombre_evento_presupuesto,
    p.fecha_inicio_evento_presupuesto,
    p.fecha_fin_evento_presupuesto,
    p.id_cliente,
    p.id_estado_ppto,
    p.activo_presupuesto,
    p.nombre_cliente,
    p.nif_cliente,
    p.email_cliente,
    p.telefono_cliente,
    p.direccion_cliente,
    p.cp_cliente,
    p.poblacion_cliente,
    p.provincia_cliente,
    p.porcentaje_descuento_cliente,
    p.duracion_evento_dias,
    p.dias_hasta_inicio_evento,
    p.dias_hasta_fin_evento,
    p.estado_evento_presupuesto,
    p.prioridad_presupuesto,
    p.tipo_pago_presupuesto,
    p.descripcion_completa_forma_pago,
    p.fecha_vencimiento_anticipo,
    p.fecha_vencimiento_final,
    p.comparacion_descuento,
    p.estado_descuento_presupuesto,
    p.aplica_descuento_presupuesto,
    p.diferencia_descuento,
    p.tiene_direccion_facturacion_diferente,
    p.dias_desde_emision,
    p.id_version_actual,
    p.numero_version_actual,
    p.estado_version_actual,
    p.fecha_creacion_version_actual,
    p.estado_general_presupuesto,
    
    -- NUEVOS: Campos de control de observaciones del presupuesto
    p.mostrar_obs_familias_presupuesto,
    p.mostrar_obs_articulos_presupuesto,
    
    -- =====================================================
    -- CAMPOS DE CLIENTE_UBICACION
    -- =====================================================
    cu.nombre_ubicacion,
    cu.direccion_ubicacion,
    cu.codigo_postal_ubicacion,
    cu.poblacion_ubicacion,
    cu.provincia_ubicacion,
    cu.pais_ubicacion,
    cu.persona_contacto_ubicacion,
    cu.telefono_contacto_ubicacion,
    cu.email_contacto_ubicacion,
    cu.observaciones_ubicacion,
    cu.es_principal_ubicacion,
    cu.activo_ubicacion,
    
    -- =====================================================
    -- CAMPOS CALCULADOS PARA AGRUPACIÓN
    -- =====================================================
    COALESCE(cu.nombre_ubicacion, p.nombre_evento_presupuesto, 'Sin ubicación') AS ubicacion_agrupacion,
    COALESCE(
        CONCAT_WS(', ',
            cu.nombre_ubicacion,
            cu.direccion_ubicacion,
            CONCAT(cu.codigo_postal_ubicacion, ' ', cu.poblacion_ubicacion),
            cu.provincia_ubicacion
        ),
        p.direccion_completa_evento_presupuesto,
        'Sin ubicación'
    ) AS ubicacion_completa_agrupacion,
    
    -- =====================================================
    -- CAMPOS DE AUDITORÍA
    -- =====================================================
    lp.created_at_linea_ppto,
    lp.updated_at_linea_ppto

FROM linea_presupuesto lp

JOIN presupuesto_version pv 
    ON lp.id_version_presupuesto = pv.id_version_presupuesto

JOIN vista_presupuesto_completa p 
    ON pv.id_presupuesto = p.id_presupuesto

LEFT JOIN vista_articulo_completa a 
    ON lp.id_articulo = a.id_articulo

LEFT JOIN coeficiente c 
    ON lp.id_coeficiente = c.id_coeficiente

LEFT JOIN impuesto imp 
    ON lp.id_impuesto = imp.id_impuesto

LEFT JOIN cliente_ubicacion cu 
    ON lp.id_ubicacion = cu.id_ubicacion

WHERE p.activo_presupuesto = TRUE;