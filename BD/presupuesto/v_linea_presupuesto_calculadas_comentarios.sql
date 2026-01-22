CREATE 
    ALGORITHM = UNDEFINED 
    DEFINER = `root`@`%` 
    SQL SECURITY DEFINER
VIEW `v_linea_presupuesto_calculada` AS
SELECT 
    -- ============================================
    -- CAMPOS PRINCIPALES DE LÍNEA DE PRESUPUESTO
    -- ============================================
    `lp`.`id_linea_ppto` AS `id_linea_ppto`,                                   -- ID único de la línea de presupuesto
    `lp`.`id_version_presupuesto` AS `id_version_presupuesto`,                 -- FK a la versión del presupuesto a la que pertenece
    `lp`.`id_articulo` AS `id_articulo`,                                       -- FK al artículo genérico asociado (puede ser NULL para líneas de texto)
    `lp`.`id_linea_padre` AS `id_linea_padre`,                                 -- FK recursiva para jerarquía KIT (NULL si es línea raíz)
    `lp`.`id_ubicacion` AS `id_ubicacion`,                                     -- FK a ubicación para montaje/desmontaje específico
    
    -- ============================================
    -- CAMPOS DE IDENTIFICACIÓN Y TIPO
    -- ============================================
    `lp`.`numero_linea_ppto` AS `numero_linea_ppto`,                           -- Número secuencial de línea dentro de la versión
    `lp`.`tipo_linea_ppto` AS `tipo_linea_ppto`,                               -- Tipo: 'articulo', 'kit', 'texto', 'subtotal', 'seccion'
    `lp`.`nivel_jerarquia` AS `nivel_jerarquia`,                               -- Nivel en árbol de KITs (0=raíz, 1=hijo directo, etc.)
    `lp`.`codigo_linea_ppto` AS `codigo_linea_ppto`,                           -- Código único generado automáticamente (ej: LP-2025-00001)
    `lp`.`descripcion_linea_ppto` AS `descripcion_linea_ppto`,                 -- Descripción personalizada de la línea (puede sobreescribir nombre del artículo)
    `lp`.`orden_linea_ppto` AS `orden_linea_ppto`,                             -- Orden de visualización en el presupuesto
    
    -- ============================================
    -- CAMPOS DE OBSERVACIONES Y VISUALIZACIÓN
    -- ============================================
    `lp`.`observaciones_linea_ppto` AS `observaciones_linea_ppto`,             -- Observaciones específicas de esta línea
    `lp`.`mostrar_obs_articulo_linea_ppto` AS `mostrar_obs_articulo_linea_ppto`, -- Si TRUE, muestra observaciones del artículo base
    `lp`.`ocultar_detalle_kit_linea_ppto` AS `ocultar_detalle_kit_linea_ppto`, -- Si TRUE, oculta líneas hijas del KIT (solo muestra total)
    `lp`.`mostrar_en_presupuesto` AS `mostrar_en_presupuesto`,                 -- Si FALSE, línea no visible en PDF/impresión (cálculos internos)
    `lp`.`es_opcional` AS `es_opcional`,                                       -- Marca línea como opcional (no incluida por defecto)
    `lp`.`activo_linea_ppto` AS `activo_linea_ppto`,                           -- Soft delete: FALSE = línea eliminada lógicamente
    
    -- ============================================
    -- CAMPOS DE FECHAS Y PLANIFICACIÓN
    -- ============================================
    `lp`.`fecha_montaje_linea_ppto` AS `fecha_montaje_linea_ppto`,             -- Fecha planificada para montaje del equipo
    `lp`.`fecha_desmontaje_linea_ppto` AS `fecha_desmontaje_linea_ppto`,       -- Fecha planificada para desmontaje del equipo
    `lp`.`fecha_inicio_linea_ppto` AS `fecha_inicio_linea_ppto`,               -- Fecha inicio de alquiler/facturación
    `lp`.`fecha_fin_linea_ppto` AS `fecha_fin_linea_ppto`,                     -- Fecha fin de alquiler/facturación
    
    -- ============================================
    -- CAMPOS DE CANTIDADES Y PRECIOS BASE
    -- ============================================
    `lp`.`cantidad_linea_ppto` AS `cantidad_linea_ppto`,                       -- Cantidad de unidades (puede ser decimal)
    `lp`.`precio_unitario_linea_ppto` AS `precio_unitario_linea_ppto`,         -- Precio unitario por jornada/unidad antes de descuentos
    `lp`.`descuento_linea_ppto` AS `descuento_linea_ppto`,                     -- Porcentaje de descuento (0-100)
    `lp`.`porcentaje_iva_linea_ppto` AS `porcentaje_iva_linea_ppto`,           -- Porcentaje IVA aplicable (normalmente heredado de impuesto)
    `lp`.`jornadas_linea_ppto` AS `jornadas_linea_ppto`,                       -- Número de jornadas de alquiler
    
    -- ============================================
    -- CAMPOS DE COEFICIENTE REDUCTOR
    -- ============================================
    `lp`.`id_coeficiente` AS `id_coeficiente`,                                 -- FK al coeficiente aplicable (puede ser NULL)
    `lp`.`aplicar_coeficiente_linea_ppto` AS `aplicar_coeficiente_linea_ppto`, -- Si TRUE, aplica el coeficiente reductor
    `lp`.`valor_coeficiente_linea_ppto` AS `valor_coeficiente_linea_ppto`,     -- Valor concreto del coeficiente para esta línea (puede diferir del maestro)
    
    -- ============================================
    -- CAMPOS DEL COEFICIENTE (JOIN)
    -- ============================================
    `c`.`jornadas_coeficiente` AS `jornadas_coeficiente`,                      -- Jornadas mínimas para aplicar este coeficiente
    `c`.`valor_coeficiente` AS `valor_coeficiente`,                            -- Valor del coeficiente en tabla maestra
    `c`.`observaciones_coeficiente` AS `observaciones_coeficiente`,            -- Descripción del coeficiente (ej: "7-13 días: 0.90")
    `c`.`activo_coeficiente` AS `activo_coeficiente`,                          -- Estado del coeficiente en maestro
    
    -- ============================================
    -- CÁLCULOS FINANCIEROS
    -- ============================================
    -- Subtotal sin aplicar coeficiente reductor
    ((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) 
        AS `subtotal_sin_coeficiente`,
    
    -- Base imponible (subtotal con coeficiente aplicado si corresponde)
    (CASE
        WHEN ((`lp`.`valor_coeficiente_linea_ppto` IS NOT NULL) AND (`lp`.`valor_coeficiente_linea_ppto` > 0))
        THEN (((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`)
        ELSE ((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100)))
    END) AS `base_imponible`,
    
    -- Importe del IVA calculado sobre la base imponible
    (CASE
        WHEN ((`lp`.`valor_coeficiente_linea_ppto` IS NOT NULL) AND (`lp`.`valor_coeficiente_linea_ppto` > 0))
        THEN ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (`lp`.`porcentaje_iva_linea_ppto` / 100))
        ELSE (((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (`lp`.`porcentaje_iva_linea_ppto` / 100))
    END) AS `importe_iva`,
    
    -- Total de la línea (base imponible + IVA)
    (CASE
        WHEN ((`lp`.`valor_coeficiente_linea_ppto` IS NOT NULL) AND (`lp`.`valor_coeficiente_linea_ppto` > 0))
        THEN ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
        ELSE (((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100)))
    END) AS `total_linea`,
    
    -- ============================================
    -- CAMPOS DEL ARTÍCULO (JOIN)
    -- ============================================
    `a`.`codigo_articulo` AS `codigo_articulo`,                                -- Código del artículo genérico
    `a`.`nombre_articulo` AS `nombre_articulo`,                                -- Nombre en español del artículo
    `a`.`name_articulo` AS `name_articulo`,                                    -- Nombre en inglés del artículo (internacionalización)
    `a`.`imagen_articulo` AS `imagen_articulo`,                                -- URL/path de la imagen del artículo
    `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`,              -- Precio estándar de alquiler del artículo
    `a`.`es_kit_articulo` AS `es_kit_articulo`,                                -- TRUE si el artículo es un KIT compuesto
    `a`.`control_total_articulo` AS `control_total_articulo`,                  -- TRUE si requiere control serializado en campo
    `a`.`activo_articulo` AS `activo_articulo`,                                -- Estado del artículo en maestro
    
    -- ============================================
    -- CAMPOS DEL IMPUESTO (JOIN)
    -- ============================================
    `lp`.`id_impuesto` AS `id_impuesto`,                                       -- FK al impuesto aplicable
    `imp`.`tipo_impuesto` AS `tipo_impuesto`,                                  -- Tipo de impuesto (ej: 'IVA', 'IGIC')
    `imp`.`tasa_impuesto` AS `tasa_impuesto`,                                  -- Tasa del impuesto en tabla maestra
    `imp`.`descr_impuesto` AS `descr_impuesto`,                                -- Descripción del impuesto
    `imp`.`activo_impuesto` AS `activo_impuesto`,                              -- Estado del impuesto en maestro
    
    -- ============================================
    -- CAMPOS DE VERSIÓN DEL PRESUPUESTO (JOIN)
    -- ============================================
    `pv`.`id_presupuesto` AS `id_presupuesto`,                                 -- FK al presupuesto padre
    `pv`.`numero_version_presupuesto` AS `numero_version_presupuesto`,         -- Número secuencial de versión (1, 2, 3...)
    `pv`.`estado_version_presupuesto` AS `estado_version_presupuesto`,         -- Estado: 'borrador', 'enviado', 'aprobado', 'rechazado'
    `pv`.`fecha_creacion_version` AS `fecha_creacion_version`,                 -- Timestamp de creación de esta versión
    `pv`.`fecha_envio_version` AS `fecha_envio_version`,                       -- Fecha de envío al cliente (NULL si no enviado)
    `pv`.`fecha_aprobacion_version` AS `fecha_aprobacion_version`,             -- Fecha de aprobación (NULL si no aprobado)
    
    -- ============================================
    -- CAMPOS DEL PRESUPUESTO CABECERA (JOIN)
    -- ============================================
    `p`.`numero_presupuesto` AS `numero_presupuesto`,                          -- Número único del presupuesto (formato: PPTO-2025-00001)
    `p`.`fecha_presupuesto` AS `fecha_presupuesto`,                            -- Fecha de creación del presupuesto
    `p`.`fecha_validez_presupuesto` AS `fecha_validez_presupuesto`,            -- Fecha hasta la cual es válido el presupuesto
    `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`,            -- Nombre del evento/proyecto
    `p`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`, -- Fecha de inicio del evento
    `p`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`,      -- Fecha de fin del evento
    `p`.`id_cliente` AS `id_cliente`,                                          -- FK al cliente
    `p`.`id_estado_ppto` AS `id_estado_ppto`,                                  -- FK al estado del presupuesto
    `p`.`activo_presupuesto` AS `activo_presupuesto`,                          -- Soft delete del presupuesto
    
    -- ============================================
    -- CAMPOS DEL CLIENTE (JOIN)
    -- ============================================
    `cl`.`nombre_cliente` AS `nombre_cliente`,                                 -- Nombre/razón social del cliente
    `cl`.`nif_cliente` AS `nif_cliente`,                                       -- NIF/CIF del cliente
    `cl`.`email_cliente` AS `email_cliente`,                                   -- Email principal del cliente
    `cl`.`telefono_cliente` AS `telefono_cliente`,                             -- Teléfono de contacto
    `cl`.`direccion_cliente` AS `direccion_cliente`,                           -- Dirección postal
    `cl`.`cp_cliente` AS `cp_cliente`,                                         -- Código postal
    `cl`.`poblacion_cliente` AS `poblacion_cliente`,                           -- Población/ciudad
    `cl`.`provincia_cliente` AS `provincia_cliente`,                           -- Provincia
    
    -- ============================================
    -- CAMPO CALCULADO: DURACIÓN DEL EVENTO
    -- ============================================
    -- Calcula días totales del evento (incluye día inicio y fin)
    (CASE
        WHEN ((`p`.`fecha_inicio_evento_presupuesto` IS NOT NULL) AND (`p`.`fecha_fin_evento_presupuesto` IS NOT NULL))
        THEN ((TO_DAYS(`p`.`fecha_fin_evento_presupuesto`) - TO_DAYS(`p`.`fecha_inicio_evento_presupuesto`)) + 1)
        ELSE NULL
    END) AS `duracion_evento_dias`,
    
    -- ============================================
    -- CAMPOS DE AUDITORÍA
    -- ============================================
    `lp`.`created_at_linea_ppto` AS `created_at_linea_ppto`,                   -- Timestamp de creación del registro
    `lp`.`updated_at_linea_ppto` AS `updated_at_linea_ppto`                    -- Timestamp de última modificación

FROM
    -- Tabla principal: líneas de presupuesto
    `linea_presupuesto` `lp`
    
    -- JOIN obligatorio: versión del presupuesto
    INNER JOIN `presupuesto_version` `pv` 
        ON (`lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`)
    
    -- JOIN obligatorio: cabecera del presupuesto
    INNER JOIN `presupuesto` `p` 
        ON (`pv`.`id_presupuesto` = `p`.`id_presupuesto`)
    
    -- JOIN obligatorio: cliente
    INNER JOIN `cliente` `cl` 
        ON (`p`.`id_cliente` = `cl`.`id_cliente`)
    
    -- LEFT JOIN: artículo (puede ser NULL para líneas de texto/sección)
    LEFT JOIN `articulo` `a` 
        ON (`lp`.`id_articulo` = `a`.`id_articulo`)
    
    -- LEFT JOIN: coeficiente reductor (puede no tener coeficiente)
    LEFT JOIN `coeficiente` `c` 
        ON (`lp`.`id_coeficiente` = `c`.`id_coeficiente`)
    
    -- LEFT JOIN: impuesto (puede tener impuesto específico)
    LEFT JOIN `impuesto` `imp` 
        ON (`lp`.`id_impuesto` = `imp`.`id_impuesto`)

WHERE
    -- Solo presupuestos activos (soft delete)
    `p`.`activo_presupuesto` = TRUE;