-- =============================================================
-- MIGRACIÓN: precio_editable_articulo — tabla y vistas SQL
-- Fecha: 2026-02-24
-- Descripción: Añade el campo precio_editable_articulo a la
--   tabla articulo y lo expone en las vistas vista_articulo_completa
--   y v_linea_presupuesto_calculada para que el frontend lo reciba
--   al cargar artículos y líneas de presupuesto.
-- =============================================================

-- PASO 0: Añadir columna a la tabla articulo
-- =============================================================

-- ✅ La columna precio_editable_articulo ya existe en la BD (verificado 2026-02-24).
-- El ALTER TABLE no es necesario. Si en alguna instalación faltara, ejecutar:
--
--   ALTER TABLE `articulo`
--   ADD COLUMN `precio_editable_articulo` TINYINT(1) NOT NULL DEFAULT 0
--       COMMENT 'Si 1, permite editar el precio unitario directamente en la línea de presupuesto'
--   AFTER `permitir_descuentos_articulo`;

-- Crear el artículo "Descuento" (familia 74 = Varios, verificado en BD real 2026-02-24)
-- Si ya existe un artículo con codigo_articulo='DESCUENTO', omitir este INSERT.
INSERT INTO `articulo` (
    `id_familia`,
    `codigo_articulo`,
    `nombre_articulo`,
    `name_articulo`,
    `precio_alquiler_articulo`,
    `precio_editable_articulo`,
    `coeficiente_articulo`,
    `es_kit_articulo`,
    `no_facturar_articulo`,
    `permitir_descuentos_articulo`,
    `activo_articulo`
) VALUES (
    74,
    'DESCUENTO',
    'Descuento',
    'Discount',
    0.00,
    1,
    0,
    0,
    0,
    0,
    1
);

-- Asegurar coeficiente_articulo = 0 en el artículo DESCUENTO (por si ya existía sin ese campo)
-- 0 = "No, sin coeficientes" → get_estado_coeficiente devuelve estado 2
UPDATE `articulo`
   SET `coeficiente_articulo` = 0
 WHERE `codigo_articulo` = 'DESCUENTO';

-- =============================================================
-- PASO 1: Actualizar vista_articulo_completa
-- Añadido: `a`.`precio_editable_articulo` después de `permitir_descuentos_articulo`
-- =============================================================

CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER
VIEW `vista_articulo_completa` AS
SELECT
    `a`.`id_articulo`                          AS `id_articulo`,
    `a`.`id_familia`                           AS `id_familia`,
    `a`.`id_unidad`                            AS `id_unidad`,
    `a`.`id_impuesto`                          AS `id_impuesto`,
    `a`.`codigo_articulo`                      AS `codigo_articulo`,
    `a`.`nombre_articulo`                      AS `nombre_articulo`,
    `a`.`name_articulo`                        AS `name_articulo`,
    `a`.`imagen_articulo`                      AS `imagen_articulo`,
    `a`.`precio_alquiler_articulo`             AS `precio_alquiler_articulo`,
    `a`.`coeficiente_articulo`                 AS `coeficiente_articulo`,
    `a`.`es_kit_articulo`                      AS `es_kit_articulo`,
    `a`.`control_total_articulo`               AS `control_total_articulo`,
    `a`.`no_facturar_articulo`                 AS `no_facturar_articulo`,
    `a`.`notas_presupuesto_articulo`           AS `notas_presupuesto_articulo`,
    `a`.`notes_budget_articulo`                AS `notes_budget_articulo`,
    `a`.`orden_obs_articulo`                   AS `orden_obs_articulo`,
    `a`.`observaciones_articulo`               AS `observaciones_articulo`,
    `a`.`permitir_descuentos_articulo`         AS `permitir_descuentos_articulo`,
    `a`.`precio_editable_articulo`             AS `precio_editable_articulo`,
    `a`.`activo_articulo`                      AS `activo_articulo`,
    `a`.`created_at_articulo`                  AS `created_at_articulo`,
    `a`.`updated_at_articulo`                  AS `updated_at_articulo`,
    `f`.`id_grupo`                             AS `id_grupo`,
    `f`.`codigo_familia`                       AS `codigo_familia`,
    `f`.`nombre_familia`                       AS `nombre_familia`,
    `f`.`name_familia`                         AS `name_familia`,
    `f`.`descr_familia`                        AS `descr_familia`,
    `f`.`imagen_familia`                       AS `imagen_familia`,
    `f`.`coeficiente_familia`                  AS `coeficiente_familia`,
    `f`.`observaciones_presupuesto_familia`    AS `observaciones_presupuesto_familia`,
    `f`.`observations_budget_familia`          AS `observations_budget_familia`,
    `f`.`orden_obs_familia`                    AS `orden_obs_familia`,
    `f`.`permite_descuento_familia`            AS `permite_descuento_familia`,
    `f`.`activo_familia`                       AS `activo_familia_relacionada`,
    `imp`.`tipo_impuesto`                      AS `tipo_impuesto`,
    `imp`.`tasa_impuesto`                      AS `tasa_impuesto`,
    `imp`.`descr_impuesto`                     AS `descr_impuesto`,
    `imp`.`activo_impuesto`                    AS `activo_impuesto_relacionado`,
    `u`.`nombre_unidad`                        AS `nombre_unidad`,
    `u`.`name_unidad`                          AS `name_unidad`,
    `u`.`descr_unidad`                         AS `descr_unidad`,
    `u`.`simbolo_unidad`                       AS `simbolo_unidad`,
    `u`.`activo_unidad`                        AS `activo_unidad_relacionada`
FROM (((
    `articulo` `a`
    JOIN `familia` `f`          ON (`a`.`id_familia`  = `f`.`id_familia`)
)
LEFT JOIN `impuesto` `imp`      ON (`a`.`id_impuesto` = `imp`.`id_impuesto`)
)
LEFT JOIN `unidad_medida` `u`   ON (`a`.`id_unidad`   = `u`.`id_unidad`)
);


-- =============================================================
-- PASO 2: Actualizar v_linea_presupuesto_calculada
-- Añadido: `a`.`precio_editable_articulo` después de
--   `a`.`permitir_descuentos_articulo`
-- NOTA: Esta vista es muy extensa. El único cambio respecto a
--   la definición existente es la adición de esa columna.
-- =============================================================

CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER
VIEW `v_linea_presupuesto_calculada` AS
SELECT
    `lp`.`id_linea_ppto`                                AS `id_linea_ppto`,
    `lp`.`id_version_presupuesto`                       AS `id_version_presupuesto`,
    `lp`.`id_articulo`                                  AS `id_articulo`,
    `lp`.`id_linea_padre`                               AS `id_linea_padre`,
    `lp`.`id_ubicacion`                                 AS `id_ubicacion`,
    `lp`.`numero_linea_ppto`                            AS `numero_linea_ppto`,
    `lp`.`tipo_linea_ppto`                              AS `tipo_linea_ppto`,
    `lp`.`nivel_jerarquia`                              AS `nivel_jerarquia`,
    `lp`.`codigo_linea_ppto`                            AS `codigo_linea_ppto`,
    `lp`.`descripcion_linea_ppto`                       AS `descripcion_linea_ppto`,
    `lp`.`orden_linea_ppto`                             AS `orden_linea_ppto`,
    `lp`.`observaciones_linea_ppto`                     AS `observaciones_linea_ppto`,
    `lp`.`mostrar_obs_articulo_linea_ppto`              AS `mostrar_obs_articulo_linea_ppto`,
    `lp`.`ocultar_detalle_kit_linea_ppto`               AS `ocultar_detalle_kit_linea_ppto`,
    `lp`.`mostrar_en_presupuesto`                       AS `mostrar_en_presupuesto`,
    `lp`.`es_opcional`                                  AS `es_opcional`,
    `lp`.`activo_linea_ppto`                            AS `activo_linea_ppto`,
    `lp`.`fecha_montaje_linea_ppto`                     AS `fecha_montaje_linea_ppto`,
    `lp`.`fecha_desmontaje_linea_ppto`                  AS `fecha_desmontaje_linea_ppto`,
    `lp`.`fecha_inicio_linea_ppto`                      AS `fecha_inicio_linea_ppto`,
    `lp`.`fecha_fin_linea_ppto`                         AS `fecha_fin_linea_ppto`,
    `lp`.`cantidad_linea_ppto`                          AS `cantidad_linea_ppto`,
    `lp`.`precio_unitario_linea_ppto`                   AS `precio_unitario_linea_ppto`,
    `lp`.`descuento_linea_ppto`                         AS `descuento_linea_ppto`,
    `lp`.`porcentaje_iva_linea_ppto`                    AS `porcentaje_iva_linea_ppto`,
    `lp`.`jornadas_linea_ppto`                          AS `jornadas_linea_ppto`,
    `lp`.`id_coeficiente`                               AS `id_coeficiente`,
    `lp`.`aplicar_coeficiente_linea_ppto`               AS `aplicar_coeficiente_linea_ppto`,
    `lp`.`valor_coeficiente_linea_ppto`                 AS `valor_coeficiente_linea_ppto`,
    `c`.`jornadas_coeficiente`                          AS `jornadas_coeficiente`,
    `c`.`valor_coeficiente`                             AS `valor_coeficiente`,
    `c`.`observaciones_coeficiente`                     AS `observaciones_coeficiente`,
    `c`.`activo_coeficiente`                            AS `activo_coeficiente`,
    -- Cálculo de días del evento
    (CASE
        WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
        THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
        ELSE 1
    END)                                                AS `dias_linea`,
    -- Subtotal sin coeficiente (siempre con días)
    ((((CASE
        WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
        THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
        ELSE 1
    END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
                                                        AS `subtotal_sin_coeficiente`,
    -- Base imponible (con o sin coeficiente)
    (CASE
        WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1 AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL AND `lp`.`valor_coeficiente_linea_ppto` > 0)
        THEN (((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`)
        ELSE ((((CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
        END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
    END)                                                AS `base_imponible`,
    -- Importe IVA
    (CASE
        WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1 AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL AND `lp`.`valor_coeficiente_linea_ppto` > 0)
        THEN ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (`lp`.`porcentaje_iva_linea_ppto` / 100))
        ELSE (((((CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
        END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (`lp`.`porcentaje_iva_linea_ppto` / 100))
    END)                                                AS `importe_iva`,
    -- Total línea con IVA
    (CASE
        WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1 AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL AND `lp`.`valor_coeficiente_linea_ppto` > 0)
        THEN ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
        ELSE (((((CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
        END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
    END)                                                AS `total_linea`,
    -- ---- Cálculos modo HOTEL (con descuento de cliente) ----
    (CASE
        WHEN (`a`.`permitir_descuentos_articulo` = 1)
        THEN (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
        ELSE `lp`.`precio_unitario_linea_ppto`
    END)                                                AS `precio_unitario_linea_ppto_hotel`,
    (CASE
        WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1 AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL AND `lp`.`valor_coeficiente_linea_ppto` > 0)
        THEN (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN (((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`)
            ELSE ((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`)
        END)
        ELSE (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN (((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)))
            ELSE (((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`)
        END)
    END)                                                AS `base_imponible_hotel`,
    (((CASE
        WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1 AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL AND `lp`.`valor_coeficiente_linea_ppto` > 0)
        THEN (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN (((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`)
            ELSE ((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`)
        END)
        ELSE (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN (((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)))
            ELSE (((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`)
        END)
    END) * `lp`.`descuento_linea_ppto`) / 100)          AS `importe_descuento_linea_ppto_hotel`,
    (CASE
        WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1 AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL AND `lp`.`valor_coeficiente_linea_ppto` > 0)
        THEN (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN (((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))))
            ELSE (((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
        END)
        ELSE (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN ((((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
            ELSE ((((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
        END)
    END)                                                AS `TotalImporte_descuento_linea_ppto_hotel`,
    (((CASE
        WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1 AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL AND `lp`.`valor_coeficiente_linea_ppto` > 0)
        THEN (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN (((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))))
            ELSE (((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
        END)
        ELSE (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN ((((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
            ELSE ((((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
        END)
    END) * `lp`.`porcentaje_iva_linea_ppto`) / 100)     AS `importe_iva_linea_ppto_hotel`,
    (CASE
        WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1 AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL AND `lp`.`valor_coeficiente_linea_ppto` > 0)
        THEN (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN ((((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))))
            ELSE (((((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))))
        END)
        ELSE (CASE WHEN (`a`.`permitir_descuentos_articulo` = 1)
            THEN (((((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
            ELSE (((((CASE WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL) THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1) ELSE 1 END) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
        END)
    END)                                                AS `TotalImporte_iva_linea_ppto_hotel`,
    -- ---- Campos de artículo (de vista_articulo_completa) ----
    `a`.`codigo_articulo`                       AS `codigo_articulo`,
    `a`.`nombre_articulo`                       AS `nombre_articulo`,
    `a`.`name_articulo`                         AS `name_articulo`,
    `a`.`imagen_articulo`                       AS `imagen_articulo`,
    `a`.`precio_alquiler_articulo`              AS `precio_alquiler_articulo`,
    `a`.`coeficiente_articulo`                  AS `coeficiente_articulo`,
    `a`.`es_kit_articulo`                       AS `es_kit_articulo`,
    `a`.`control_total_articulo`                AS `control_total_articulo`,
    `a`.`no_facturar_articulo`                  AS `no_facturar_articulo`,
    `a`.`notas_presupuesto_articulo`            AS `notas_presupuesto_articulo`,
    `a`.`notes_budget_articulo`                 AS `notes_budget_articulo`,
    `a`.`orden_obs_articulo`                    AS `orden_obs_articulo`,
    `a`.`observaciones_articulo`                AS `observaciones_articulo`,
    `a`.`activo_articulo`                       AS `activo_articulo`,
    `a`.`permitir_descuentos_articulo`          AS `permitir_descuentos_articulo`,
    `a`.`precio_editable_articulo`              AS `precio_editable_articulo`,
    `a`.`id_familia`                            AS `id_familia`,
    `a`.`created_at_articulo`                   AS `created_at_articulo`,
    `a`.`updated_at_articulo`                   AS `updated_at_articulo`,
    `a`.`id_impuesto`                           AS `id_impuesto_articulo`,
    `a`.`tipo_impuesto`                         AS `tipo_impuesto_articulo`,
    `a`.`tasa_impuesto`                         AS `tasa_impuesto_articulo`,
    `a`.`descr_impuesto`                        AS `descr_impuesto_articulo`,
    `a`.`activo_impuesto_relacionado`           AS `activo_impuesto_articulo`,
    `a`.`id_unidad`                             AS `id_unidad`,
    `a`.`nombre_unidad`                         AS `nombre_unidad`,
    `a`.`name_unidad`                           AS `name_unidad`,
    `a`.`descr_unidad`                          AS `descr_unidad`,
    `a`.`simbolo_unidad`                        AS `simbolo_unidad`,
    `a`.`activo_unidad_relacionada`             AS `activo_unidad`,
    `a`.`id_grupo`                              AS `id_grupo`,
    `a`.`codigo_familia`                        AS `codigo_familia`,
    `a`.`nombre_familia`                        AS `nombre_familia`,
    `a`.`name_familia`                          AS `name_familia`,
    `a`.`descr_familia`                         AS `descr_familia`,
    `a`.`imagen_familia`                        AS `imagen_familia`,
    `a`.`coeficiente_familia`                   AS `coeficiente_familia`,
    `a`.`observaciones_presupuesto_familia`     AS `observaciones_presupuesto_familia`,
    `a`.`observations_budget_familia`           AS `observations_budget_familia`,
    `a`.`orden_obs_familia`                     AS `orden_obs_familia`,
    `a`.`permite_descuento_familia`             AS `permite_descuento_familia`,
    `a`.`activo_familia_relacionada`            AS `activo_familia_relacionada`,
    -- ---- Impuesto de la línea (override sobre el del artículo) ----
    `lp`.`id_impuesto`                          AS `id_impuesto`,
    `imp`.`tipo_impuesto`                       AS `tipo_impuesto`,
    `imp`.`tasa_impuesto`                       AS `tasa_impuesto`,
    `imp`.`descr_impuesto`                      AS `descr_impuesto`,
    `imp`.`activo_impuesto`                     AS `activo_impuesto`,
    -- ---- Presupuesto y versión ----
    `pv`.`id_presupuesto`                       AS `id_presupuesto`,
    `pv`.`numero_version_presupuesto`           AS `numero_version_presupuesto`,
    `pv`.`estado_version_presupuesto`           AS `estado_version_presupuesto`,
    `pv`.`fecha_creacion_version`               AS `fecha_creacion_version`,
    `pv`.`fecha_envio_version`                  AS `fecha_envio_version`,
    `pv`.`fecha_aprobacion_version`             AS `fecha_aprobacion_version`,
    `p`.`numero_presupuesto`                    AS `numero_presupuesto`,
    `p`.`fecha_presupuesto`                     AS `fecha_presupuesto`,
    `p`.`fecha_validez_presupuesto`             AS `fecha_validez_presupuesto`,
    `p`.`nombre_evento_presupuesto`             AS `nombre_evento_presupuesto`,
    `p`.`fecha_inicio_evento_presupuesto`       AS `fecha_inicio_evento_presupuesto`,
    `p`.`fecha_fin_evento_presupuesto`          AS `fecha_fin_evento_presupuesto`,
    `p`.`id_cliente`                            AS `id_cliente`,
    `p`.`id_estado_ppto`                        AS `id_estado_ppto`,
    `p`.`activo_presupuesto`                    AS `activo_presupuesto`,
    `p`.`nombre_cliente`                        AS `nombre_cliente`,
    `p`.`nif_cliente`                           AS `nif_cliente`,
    `p`.`email_cliente`                         AS `email_cliente`,
    `p`.`telefono_cliente`                      AS `telefono_cliente`,
    `p`.`direccion_cliente`                     AS `direccion_cliente`,
    `p`.`cp_cliente`                            AS `cp_cliente`,
    `p`.`poblacion_cliente`                     AS `poblacion_cliente`,
    `p`.`provincia_cliente`                     AS `provincia_cliente`,
    `p`.`porcentaje_descuento_cliente`          AS `porcentaje_descuento_cliente`,
    `p`.`duracion_evento_dias`                  AS `duracion_evento_dias`,
    `p`.`dias_hasta_inicio_evento`              AS `dias_hasta_inicio_evento`,
    `p`.`dias_hasta_fin_evento`                 AS `dias_hasta_fin_evento`,
    `p`.`estado_evento_presupuesto`             AS `estado_evento_presupuesto`,
    `p`.`prioridad_presupuesto`                 AS `prioridad_presupuesto`,
    `p`.`tipo_pago_presupuesto`                 AS `tipo_pago_presupuesto`,
    `p`.`descripcion_completa_forma_pago`       AS `descripcion_completa_forma_pago`,
    `p`.`fecha_vencimiento_anticipo`            AS `fecha_vencimiento_anticipo`,
    `p`.`fecha_vencimiento_final`               AS `fecha_vencimiento_final`,
    `p`.`comparacion_descuento`                 AS `comparacion_descuento`,
    `p`.`estado_descuento_presupuesto`          AS `estado_descuento_presupuesto`,
    `p`.`aplica_descuento_presupuesto`          AS `aplica_descuento_presupuesto`,
    `p`.`diferencia_descuento`                  AS `diferencia_descuento`,
    `p`.`tiene_direccion_facturacion_diferente` AS `tiene_direccion_facturacion_diferente`,
    `p`.`dias_desde_emision`                    AS `dias_desde_emision`,
    `p`.`id_version_actual`                     AS `id_version_actual`,
    `p`.`numero_version_actual`                 AS `numero_version_actual`,
    `p`.`estado_version_actual`                 AS `estado_version_actual`,
    `p`.`fecha_creacion_version_actual`         AS `fecha_creacion_version_actual`,
    `p`.`estado_general_presupuesto`            AS `estado_general_presupuesto`,
    `p`.`mostrar_obs_familias_presupuesto`      AS `mostrar_obs_familias_presupuesto`,
    `p`.`mostrar_obs_articulos_presupuesto`     AS `mostrar_obs_articulos_presupuesto`,
    -- ---- Ubicación ----
    `cu`.`nombre_ubicacion`                     AS `nombre_ubicacion`,
    `cu`.`direccion_ubicacion`                  AS `direccion_ubicacion`,
    `cu`.`codigo_postal_ubicacion`              AS `codigo_postal_ubicacion`,
    `cu`.`poblacion_ubicacion`                  AS `poblacion_ubicacion`,
    `cu`.`provincia_ubicacion`                  AS `provincia_ubicacion`,
    `cu`.`pais_ubicacion`                       AS `pais_ubicacion`,
    `cu`.`persona_contacto_ubicacion`           AS `persona_contacto_ubicacion`,
    `cu`.`telefono_contacto_ubicacion`          AS `telefono_contacto_ubicacion`,
    `cu`.`email_contacto_ubicacion`             AS `email_contacto_ubicacion`,
    `cu`.`observaciones_ubicacion`              AS `observaciones_ubicacion`,
    `cu`.`es_principal_ubicacion`               AS `es_principal_ubicacion`,
    `cu`.`activo_ubicacion`                     AS `activo_ubicacion`,
    COALESCE(`cu`.`nombre_ubicacion`, `p`.`nombre_evento_presupuesto`, 'Sin ubicación')
                                                AS `ubicacion_agrupacion`,
    COALESCE(
        CONCAT_WS(', ',
            `cu`.`nombre_ubicacion`,
            `cu`.`direccion_ubicacion`,
            CONCAT(`cu`.`codigo_postal_ubicacion`, ' ', `cu`.`poblacion_ubicacion`),
            `cu`.`provincia_ubicacion`
        ),
        `p`.`direccion_completa_evento_presupuesto`,
        'Sin ubicación'
    )                                           AS `ubicacion_completa_agrupacion`,
    `lp`.`created_at_linea_ppto`               AS `created_at_linea_ppto`,
    `lp`.`updated_at_linea_ppto`               AS `updated_at_linea_ppto`
FROM ((((((
    `linea_presupuesto` `lp`
    JOIN  `presupuesto_version` `pv`        ON (`lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`)
    JOIN  `vista_presupuesto_completa` `p`  ON (`pv`.`id_presupuesto`         = `p`.`id_presupuesto`)
)
LEFT JOIN `vista_articulo_completa` `a`     ON (`lp`.`id_articulo`            = `a`.`id_articulo`)
)
LEFT JOIN `coeficiente` `c`                 ON (`lp`.`id_coeficiente`         = `c`.`id_coeficiente`)
)
LEFT JOIN `impuesto` `imp`                  ON (`lp`.`id_impuesto`            = `imp`.`id_impuesto`)
)
LEFT JOIN `cliente_ubicacion` `cu`          ON (`lp`.`id_ubicacion`           = `cu`.`id_ubicacion`)
))
WHERE (`p`.`activo_presupuesto` = TRUE);
