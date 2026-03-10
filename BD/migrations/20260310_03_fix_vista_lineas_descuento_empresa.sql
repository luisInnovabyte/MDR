-- ============================================================
-- Migration: 20260310_03_fix_vista_lineas_descuento_empresa
-- Date: 2026-03-10
-- Description:
--   Recrea v_linea_presupuesto_calculada para respetar el flag
--   empresa.permitir_descuentos_lineas_empresa.
--   Cuando = 0, el descuento de línea (descuento_linea_ppto)
--   NO se aplica en los cálculos de base_imponible / IVA / total.
--
-- Campos afectados:
--   subtotal_sin_coeficiente, base_imponible, importe_iva, total_linea
--   TotalImporte_descuento_linea_ppto_hotel, importe_iva_linea_ppto_hotel
--   TotalImporte_iva_linea_ppto_hotel, importe_descuento_linea_ppto_hotel
--
-- Estrategia:
--   Reemplazar (1 - (descuento_linea_ppto / 100))
--   por        (1 - (IF(COALESCE(e.permitir_descuentos_lineas_empresa,1)=1,
--                        descuento_linea_ppto, 0) / 100))
--
--   JOIN añadido:
--     LEFT JOIN `empresa` `e` ON (e.empresa_ficticia_principal=1 AND e.activo_empresa=1)
--   (igual que los PDFs: siempre leen la empresa ficticia principal, ignoran presupuesto.id_empresa)
-- ============================================================

CREATE OR REPLACE ALGORITHM=UNDEFINED DEFINER=`root`@`%`
SQL SECURITY DEFINER
VIEW `v_linea_presupuesto_calculada` AS
SELECT
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
  `c`.`jornadas_coeficiente`,
  `c`.`valor_coeficiente`,
  `c`.`observaciones_coeficiente`,
  `c`.`activo_coeficiente`,

  -- días efectivos de la línea
  (CASE
    WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
    THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
    ELSE 1
  END) AS `dias_linea`,

  -- ── subtotal_sin_coeficiente ─────────────────────────────────────────
  ((CASE
    WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
    THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
    ELSE 1
  END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
   * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
               `lp`.`descuento_linea_ppto`, 0) / 100))
  ) AS `subtotal_sin_coeficiente`,

  -- ── base_imponible ───────────────────────────────────────────────────
  (CASE
    WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1
          AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL
          AND `lp`.`valor_coeficiente_linea_ppto` > 0)
    THEN (
      `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
      * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                 `lp`.`descuento_linea_ppto`, 0) / 100))
      * `lp`.`valor_coeficiente_linea_ppto`
    )
    ELSE (
      (CASE
        WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
        THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
        ELSE 1
      END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
      * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                 `lp`.`descuento_linea_ppto`, 0) / 100))
    )
  END) AS `base_imponible`,

  -- ── importe_iva ──────────────────────────────────────────────────────
  (CASE
    WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1
          AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL
          AND `lp`.`valor_coeficiente_linea_ppto` > 0)
    THEN (
      `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
      * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                 `lp`.`descuento_linea_ppto`, 0) / 100))
      * `lp`.`valor_coeficiente_linea_ppto`
      * (`lp`.`porcentaje_iva_linea_ppto` / 100)
    )
    ELSE (
      (CASE
        WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
        THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
        ELSE 1
      END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
      * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                 `lp`.`descuento_linea_ppto`, 0) / 100))
      * (`lp`.`porcentaje_iva_linea_ppto` / 100)
    )
  END) AS `importe_iva`,

  -- ── total_linea ──────────────────────────────────────────────────────
  (CASE
    WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1
          AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL
          AND `lp`.`valor_coeficiente_linea_ppto` > 0)
    THEN (
      `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
      * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                 `lp`.`descuento_linea_ppto`, 0) / 100))
      * `lp`.`valor_coeficiente_linea_ppto`
      * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))
    )
    ELSE (
      (CASE
        WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
        THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
        ELSE 1
      END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
      * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                 `lp`.`descuento_linea_ppto`, 0) / 100))
      * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))
    )
  END) AS `total_linea`,

  -- ════════════════════════════════════════════════════════════════════
  -- HOTEL fields  (usan porcentaje_descuento_cliente en precio unitario
  --               + descuento de línea condicionado por empresa)
  -- ════════════════════════════════════════════════════════════════════

  -- precio_unitario_linea_ppto_hotel (sin cambio: no usa descuento_linea)
  (CASE
    WHEN (`a`.`permitir_descuentos_articulo` = 1)
    THEN (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
    ELSE `lp`.`precio_unitario_linea_ppto`
  END) AS `precio_unitario_linea_ppto_hotel`,

  -- base_imponible_hotel (sin descuento_linea; descuento_cliente sí aplica siempre)
  (CASE
    WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1
          AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL
          AND `lp`.`valor_coeficiente_linea_ppto` > 0)
    THEN (
      CASE
        WHEN (`a`.`permitir_descuentos_articulo` = 1)
        THEN ((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
              * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
        ELSE (`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
      END
    )
    ELSE (
      CASE
        WHEN (`a`.`permitir_descuentos_articulo` = 1)
        THEN (
          (CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
          END) * `lp`.`cantidad_linea_ppto`
          * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
        )
        ELSE (
          (CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
          END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
        )
      END
    )
  END) AS `base_imponible_hotel`,

  -- importe_descuento_linea_ppto_hotel — descuento de línea condicionado
  (((CASE
      WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1
            AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL
            AND `lp`.`valor_coeficiente_linea_ppto` > 0)
      THEN (
        CASE
          WHEN (`a`.`permitir_descuentos_articulo` = 1)
          THEN ((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
                * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
          ELSE (`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
        END
      )
      ELSE (
        CASE
          WHEN (`a`.`permitir_descuentos_articulo` = 1)
          THEN (
            (CASE
              WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
              THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
              ELSE 1
            END) * `lp`.`cantidad_linea_ppto`
            * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
          )
          ELSE (
            (CASE
              WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
              THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
              ELSE 1
            END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`
          )
        END
      )
    END)
    * IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
         `lp`.`descuento_linea_ppto`, 0)
  ) / 100) AS `importe_descuento_linea_ppto_hotel`,

  -- TotalImporte_descuento_linea_ppto_hotel
  (CASE
    WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1
          AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL
          AND `lp`.`valor_coeficiente_linea_ppto` > 0)
    THEN (
      CASE
        WHEN (`a`.`permitir_descuentos_articulo` = 1)
        THEN (((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
               * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
              * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                         `lp`.`descuento_linea_ppto`, 0) / 100)))
        ELSE ((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
              * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                         `lp`.`descuento_linea_ppto`, 0) / 100)))
      END
    )
    ELSE (
      CASE
        WHEN (`a`.`permitir_descuentos_articulo` = 1)
        THEN ((
          (CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
          END) * `lp`.`cantidad_linea_ppto`
          * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)))
          * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                     `lp`.`descuento_linea_ppto`, 0) / 100)))
        ELSE ((
          (CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
          END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`)
          * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                     `lp`.`descuento_linea_ppto`, 0) / 100)))
      END
    )
  END) AS `TotalImporte_descuento_linea_ppto_hotel`,

  -- importe_iva_linea_ppto_hotel
  (((CASE
      WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1
            AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL
            AND `lp`.`valor_coeficiente_linea_ppto` > 0)
      THEN (
        CASE
          WHEN (`a`.`permitir_descuentos_articulo` = 1)
          THEN (((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
                 * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
                * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                           `lp`.`descuento_linea_ppto`, 0) / 100)))
          ELSE ((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
                * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                           `lp`.`descuento_linea_ppto`, 0) / 100)))
        END
      )
      ELSE (
        CASE
          WHEN (`a`.`permitir_descuentos_articulo` = 1)
          THEN ((
            (CASE
              WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
              THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
              ELSE 1
            END) * `lp`.`cantidad_linea_ppto`
            * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)))
            * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                       `lp`.`descuento_linea_ppto`, 0) / 100)))
          ELSE ((
            (CASE
              WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
              THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
              ELSE 1
            END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`)
            * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                       `lp`.`descuento_linea_ppto`, 0) / 100)))
        END
      )
    END) * `lp`.`porcentaje_iva_linea_ppto`) / 100
  ) AS `importe_iva_linea_ppto_hotel`,

  -- TotalImporte_iva_linea_ppto_hotel
  (CASE
    WHEN (`lp`.`aplicar_coeficiente_linea_ppto` = 1
          AND `lp`.`valor_coeficiente_linea_ppto` IS NOT NULL
          AND `lp`.`valor_coeficiente_linea_ppto` > 0)
    THEN (
      CASE
        WHEN (`a`.`permitir_descuentos_articulo` = 1)
        THEN ((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))
                * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
               * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                          `lp`.`descuento_linea_ppto`, 0) / 100)))
              * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
        ELSE (((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto` * `lp`.`cantidad_linea_ppto`)
               * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                          `lp`.`descuento_linea_ppto`, 0) / 100)))
              * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
      END
    )
    ELSE (
      CASE
        WHEN (`a`.`permitir_descuentos_articulo` = 1)
        THEN (((
          (CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
          END) * `lp`.`cantidad_linea_ppto`
          * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)))
          * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                     `lp`.`descuento_linea_ppto`, 0) / 100)))
          * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
        ELSE (((
          (CASE
            WHEN (`lp`.`fecha_inicio_linea_ppto` IS NOT NULL AND `lp`.`fecha_fin_linea_ppto` IS NOT NULL)
            THEN (TO_DAYS(`lp`.`fecha_fin_linea_ppto`) - TO_DAYS(`lp`.`fecha_inicio_linea_ppto`) + 1)
            ELSE 1
          END) * `lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`)
          * (1 - (IF(COALESCE(`e`.`permitir_descuentos_lineas_empresa`, 1) = 1,
                     `lp`.`descuento_linea_ppto`, 0) / 100)))
          * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
      END
    )
  END) AS `TotalImporte_iva_linea_ppto_hotel`,

  -- ── campos de artículo ───────────────────────────────────────────────
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
  `a`.`precio_editable_articulo`,
  `a`.`id_familia`,
  `a`.`created_at_articulo`,
  `a`.`updated_at_articulo`,
  `a`.`id_impuesto`                  AS `id_impuesto_articulo`,
  `a`.`tipo_impuesto`                AS `tipo_impuesto_articulo`,
  `a`.`tasa_impuesto`                AS `tasa_impuesto_articulo`,
  `a`.`descr_impuesto`               AS `descr_impuesto_articulo`,
  `a`.`activo_impuesto_relacionado`  AS `activo_impuesto_articulo`,
  `a`.`id_unidad`,
  `a`.`nombre_unidad`,
  `a`.`name_unidad`,
  `a`.`descr_unidad`,
  `a`.`simbolo_unidad`,
  `a`.`activo_unidad_relacionada`    AS `activo_unidad`,
  `a`.`id_grupo`,
  `a`.`codigo_familia`,
  `a`.`nombre_familia`,
  `a`.`name_familia`,
  `a`.`descr_familia`,
  `a`.`imagen_familia`,
  `a`.`coeficiente_familia`,
  `a`.`observaciones_presupuesto_familia`,
  `a`.`observations_budget_familia`,
  `a`.`orden_obs_familia`,
  `a`.`permite_descuento_familia`,
  `a`.`activo_familia_relacionada`,

  -- ── impuesto de la línea ─────────────────────────────────────────────
  `lp`.`id_impuesto`,
  `imp`.`tipo_impuesto`,
  `imp`.`tasa_impuesto`,
  `imp`.`descr_impuesto`,
  `imp`.`activo_impuesto`,

  -- ── presupuesto / versión ────────────────────────────────────────────
  `pv`.`id_presupuesto`,
  `pv`.`numero_version_presupuesto`,
  `pv`.`estado_version_presupuesto`,
  `pv`.`fecha_creacion_version`,
  `pv`.`fecha_envio_version`,
  `pv`.`fecha_aprobacion_version`,
  `p`.`numero_presupuesto`,
  `p`.`fecha_presupuesto`,
  `p`.`fecha_validez_presupuesto`,
  `p`.`nombre_evento_presupuesto`,
  `p`.`fecha_inicio_evento_presupuesto`,
  `p`.`fecha_fin_evento_presupuesto`,
  `p`.`id_cliente`,
  `p`.`id_estado_ppto`,
  `p`.`activo_presupuesto`,
  `p`.`nombre_cliente`,
  `p`.`nif_cliente`,
  `p`.`email_cliente`,
  `p`.`telefono_cliente`,
  `p`.`direccion_cliente`,
  `p`.`cp_cliente`,
  `p`.`poblacion_cliente`,
  `p`.`provincia_cliente`,
  `p`.`porcentaje_descuento_cliente`,
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
  `p`.`mostrar_obs_familias_presupuesto`,
  `p`.`mostrar_obs_articulos_presupuesto`,

  -- ── ubicación ────────────────────────────────────────────────────────
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
  ) AS `ubicacion_completa_agrupacion`,

  `lp`.`created_at_linea_ppto`,
  `lp`.`updated_at_linea_ppto`

FROM `linea_presupuesto` `lp`
JOIN  `presupuesto_version` `pv`       ON `lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`
JOIN  `vista_presupuesto_completa` `p` ON `pv`.`id_presupuesto` = `p`.`id_presupuesto`
-- NEW: join a empresa ficticia principal (igual que los PDFs: WHERE empresa_ficticia_principal=1)
-- presupuesto.id_empresa puede ser NULL; el flag lo maneja siempre la empresa principal
LEFT JOIN `empresa` `e`                ON (`e`.`empresa_ficticia_principal` = 1 AND `e`.`activo_empresa` = 1)
LEFT JOIN `vista_articulo_completa` `a` ON `lp`.`id_articulo` = `a`.`id_articulo`
LEFT JOIN `coeficiente` `c`            ON `lp`.`id_coeficiente` = `c`.`id_coeficiente`
LEFT JOIN `impuesto` `imp`             ON `lp`.`id_impuesto` = `imp`.`id_impuesto`
LEFT JOIN `cliente_ubicacion` `cu`     ON `lp`.`id_ubicacion` = `cu`.`id_ubicacion`
WHERE `p`.`activo_presupuesto` = TRUE;
