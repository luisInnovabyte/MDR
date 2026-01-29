-- ============================================
-- ACTUALIZACIÓN: VISTA v_linea_presupuesto_calculada
-- Descripción: Añadir todos los campos de vista_presupuesto_completa
-- Fecha: 2026-01-29
-- Autor: Luis MDR
-- BASADO EN: toldos_db.sql - Estructura real de la base de datos
-- ============================================

-- =====================================================
-- Eliminar vista existente
-- =====================================================
DROP VIEW IF EXISTS `v_linea_presupuesto_calculada`;

-- =====================================================
-- Crear vista actualizada con campos REALES
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
    
    -- Total línea (base + IVA)
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
    
    -- Campos de impuesto del artículo
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
    -- CAMPOS DE IMPUESTO DE LÍNEA (LEFT JOIN imp)
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
    -- *** TODOS LOS CAMPOS REALES DE LA VISTA ***
    -- =====================================================
    
    -- Datos básicos del presupuesto
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
    
    -- Método de pago
    `p`.`id_metodo_pago`,
    `p`.`codigo_metodo_pago`,
    `p`.`nombre_metodo_pago`,
    
    -- Método de contacto
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
    -- CAMPOS CALCULADOS PARA AGRUPACIÓN EN DATATABLES
    -- =====================================================
    -- Ubicación para agrupación: prioriza ubicación específica de la línea,
    -- luego el nombre del evento, y finalmente 'Sin ubicación'
    COALESCE(
        `cu`.`nombre_ubicacion`, 
        `p`.`nombre_evento_presupuesto`, 
        'Sin ubicación'
    ) AS `ubicacion_agrupacion`,
    
    -- Dirección completa para agrupación (opcional, para mostrar más detalles)
    COALESCE(
        CONCAT_WS(', ',
            `cu`.`nombre_ubicacion`,
            `cu`.`direccion_ubicacion`,
            CONCAT(`cu`.`codigo_postal_ubicacion`, ' ', `cu`.`poblacion_ubicacion`),
            `cu`.`provincia_ubicacion`
        ),
        `p`.`direccion_completa_evento_presupuesto`,
        'Sin ubicación'
    ) AS `ubicacion_completa_agrupacion`,
    
    -- =====================================================
    -- CAMPOS DE AUDITORÍA
    -- =====================================================
    `lp`.`created_at_linea_ppto`,
    `lp`.`updated_at_linea_ppto`
    
-- =====================================================
-- FROM Y JOINS
-- =====================================================
FROM `linea_presupuesto` `lp`

-- JOIN con presupuesto_version
INNER JOIN `presupuesto_version` `pv` 
    ON `lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`

-- JOIN con vista_presupuesto_completa
INNER JOIN `vista_presupuesto_completa` `p` 
    ON `pv`.`id_presupuesto` = `p`.`id_presupuesto`

-- LEFT JOIN con vista_articulo_completa
LEFT JOIN `vista_articulo_completa` `a` 
    ON `lp`.`id_articulo` = `a`.`id_articulo`
    
-- LEFT JOIN con coeficiente
LEFT JOIN `coeficiente` `c` 
    ON `lp`.`id_coeficiente` = `c`.`id_coeficiente`
    
-- LEFT JOIN con impuesto de línea
LEFT JOIN `impuesto` `imp` 
    ON `lp`.`id_impuesto` = `imp`.`id_impuesto`
    
-- LEFT JOIN con cliente_ubicacion
LEFT JOIN `cliente_ubicacion` `cu` 
    ON `lp`.`id_ubicacion` = `cu`.`id_ubicacion`

-- =====================================================
-- WHERE
-- =====================================================
WHERE `p`.`activo_presupuesto` = TRUE;

-- =====================================================
-- VERIFICACIÓN
-- =====================================================

-- Verificar que la vista se creó correctamente
SELECT COUNT(*) AS total_registros 
FROM `v_linea_presupuesto_calculada`;

-- Verificar algunos campos clave
SELECT 
    `id_linea_ppto`,
    `codigo_linea_ppto`,
    `descripcion_linea_ppto`,
    `fecha_inicio_linea_ppto`,
    `ubicacion_agrupacion`,
    `ubicacion_completa_agrupacion`,
    `numero_presupuesto`,
    `nombre_evento_presupuesto`,
    `duracion_evento_dias`,
    `nombre_cliente`,
    `email_cliente`,
    `nombre_estado_ppto`,
    `direccion_completa_evento_presupuesto`,
    `estado_validez_presupuesto`,
    `estado_evento_presupuesto`,
    `prioridad_presupuesto`
FROM `v_linea_presupuesto_calculada`
ORDER BY `fecha_inicio_linea_ppto`, `ubicacion_agrupacion`
LIMIT 5;

-- =====================================================
-- RESUMEN DE CAMBIOS
-- =====================================================

/*
CAMPOS AÑADIDOS DE VISTA_PRESUPUESTO_COMPLETA (80+ campos nuevos):

1. DATOS BÁSICOS DEL PRESUPUESTO:
   - version_actual_presupuesto
   - numero_pedido_cliente_presupuesto
   - aplicar_coeficientes_presupuesto
   - descuento_presupuesto
   - Ubicación evento: direccion, poblacion, cp, provincia
   - Observaciones: cabecera, pie (español e inglés)
   - Flags: mostrar_obs_familias, mostrar_obs_articulos
   - observaciones_internas_presupuesto

2. DATOS EXTENDIDOS DEL CLIENTE:
   - codigo_cliente
   - porcentaje_descuento_cliente
   - Datos facturación: nombre, direccion, cp, poblacion, provincia

3. CONTACTO DEL CLIENTE:
   - apellidos_contacto_cliente

4. FORMA DE PAGO COMPLETA:
   - Todos los campos de forma_pago
   - Método de pago: id, codigo, nombre
   - Forma de pago habitual del cliente

5. MÉTODO DE CONTACTO:
   - id_metodo_contacto, nombre_metodo_contacto

6. CAMPOS CALCULADOS (YA EN VISTA):
   - direccion_completa_evento_presupuesto
   - direccion_completa_cliente
   - direccion_facturacion_completa_cliente
   - nombre_completo_contacto
   - dias_validez_restantes
   - estado_validez_presupuesto
   - duracion_evento_dias
   - dias_hasta_inicio_evento
   - dias_hasta_fin_evento
   - estado_evento_presupuesto
   - prioridad_presupuesto
   - tipo_pago_presupuesto
   - descripcion_completa_forma_pago
   - fecha_vencimiento_anticipo
   - fecha_vencimiento_final
   - comparacion_descuento
   - estado_descuento_presupuesto
   - aplica_descuento_presupuesto
   - diferencia_descuento
   - tiene_direccion_facturacion_diferente
   - dias_desde_emision
   - id_version_actual
   - numero_version_actual
   - estado_version_actual
   - fecha_creacion_version_actual
   - estado_general_presupuesto

TOTAL: Aproximadamente 122 campos incluyendo los originales

CAMPOS ESPECIALES PARA AGRUPACIÓN EN DATATABLES:
✅ ubicacion_agrupacion: 
   - Prioridad 1: nombre_ubicacion (ubicación específica de la línea)
   - Prioridad 2: nombre_evento_presupuesto (ubicación general del evento)
   - Prioridad 3: 'Sin ubicación' (fallback)
   
✅ ubicacion_completa_agrupacion:
   - Versión extendida con dirección completa para mostrar detalles
   
✅ fecha_inicio_linea_ppto:
   - Ya disponible para agrupación de primer nivel

USO EN DATATABLES:
Para agrupar correctamente en DataTables usar:
1. Nivel 1: ORDER BY fecha_inicio_linea_ppto
2. Nivel 2: ORDER BY ubicacion_agrupacion

EJEMPLO DE CONFIGURACIÓN DATATABLES:
```javascript
$('#tabla').DataTable({
    order: [[col_fecha_inicio, 'asc'], [col_ubicacion_agrupacion, 'asc']],
    rowGroup: {
        dataSrc: ['fecha_inicio_linea_ppto', 'ubicacion_agrupacion'],
        startRender: function (rows, group, level) {
            if (level === 0) {
                // Nivel 1: Fecha
                return 'Fecha: ' + moment(group).format('DD/MM/YYYY');
            } else {
                // Nivel 2: Ubicación
                return 'Ubicación: ' + group;
            }
        }
    }
});
```

BENEFICIOS:
✅ Basado en estructura REAL de la base de datos
✅ No inventa campos inexistentes
✅ Incluye TODOS los campos calculados útiles
✅ 100% compatible con código existente
✅ Coherencia con vista_articulo_completa
✅ Campos optimizados para agrupación en DataTables
*/
