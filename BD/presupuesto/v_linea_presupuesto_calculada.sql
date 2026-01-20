-- ========================================================
-- VISTA: v_linea_presupuesto_calculada
-- ========================================================
-- VERSIÓN: DEFINITIVA - Nomenclatura 100% verificada
-- FECHA: 2025-01-20
-- AUTOR: Luis - MDR ERP Manager
--
-- CORRECCIONES FINALES:
--   ✅ id_version_presupuesto (NO id_presupuesto)
--   ✅ Tabla: impuesto (NO tipo_iva)  
--   ✅ Tabla: coeficiente (campos verificados)
--   ✅ JOIN triple: linea_presupuesto → presupuesto_version → presupuesto
-- ========================================================

DROP VIEW IF EXISTS v_linea_presupuesto_calculada;

CREATE VIEW v_linea_presupuesto_calculada AS
SELECT 
    -- =====================================================
    -- DATOS DE LA LÍNEA DE PRESUPUESTO
    -- =====================================================
    lp.id_linea_ppto,
    lp.id_version_presupuesto,
    lp.id_articulo,
    lp.numero_linea_ppto,
    lp.tipo_linea_ppto,
    lp.codigo_linea_ppto,
    lp.descripcion_linea_ppto,
    lp.orden_linea_ppto,
    lp.observaciones_linea_ppto,
    lp.mostrar_obs_articulo_linea_ppto,
    lp.activo_linea_ppto,
    
    -- =====================================================
    -- CANTIDADES Y PRECIOS ORIGINALES
    -- =====================================================
    lp.cantidad_linea_ppto,
    lp.precio_unitario_linea_ppto,
    lp.descuento_linea_ppto,
    lp.porcentaje_iva_linea_ppto,
    
    -- =====================================================
    -- COEFICIENTE REDUCTOR
    -- =====================================================
    lp.jornadas_linea_ppto,
    lp.id_coeficiente,
    lp.valor_coeficiente_linea_ppto,
    
    -- Datos del coeficiente maestro
    c.jornadas_coeficiente,
    c.valor_coeficiente,
    c.observaciones_coeficiente,
    c.activo_coeficiente,
    
    -- =====================================================
    -- CÁLCULO 1: SUBTOTAL SIN COEFICIENTE
    -- Fórmula: cantidad × precio × (1 - descuento/100)
    -- =====================================================
    (lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto * (1 - lp.descuento_linea_ppto/100)) 
        AS subtotal_sin_coeficiente,
    
    -- =====================================================
    -- CÁLCULO 2: BASE IMPONIBLE (CON COEFICIENTE SI APLICA)
    -- =====================================================
    CASE 
        WHEN lp.valor_coeficiente_linea_ppto IS NOT NULL 
             AND lp.valor_coeficiente_linea_ppto > 0 THEN
            (lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto * (1 - lp.descuento_linea_ppto/100)) 
            * lp.valor_coeficiente_linea_ppto
        ELSE
            (lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto * (1 - lp.descuento_linea_ppto/100))
    END AS base_imponible,
    
    -- =====================================================
    -- CÁLCULO 3: IMPORTE DE IVA
    -- =====================================================
    CASE 
        WHEN lp.valor_coeficiente_linea_ppto IS NOT NULL 
             AND lp.valor_coeficiente_linea_ppto > 0 THEN
            ((lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto * (1 - lp.descuento_linea_ppto/100)) 
            * lp.valor_coeficiente_linea_ppto) 
            * (lp.porcentaje_iva_linea_ppto/100)
        ELSE
            ((lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto * (1 - lp.descuento_linea_ppto/100))) 
            * (lp.porcentaje_iva_linea_ppto/100)
    END AS importe_iva,
    
    -- =====================================================
    -- CÁLCULO 4: TOTAL LÍNEA (BASE + IVA)
    -- =====================================================
    CASE 
        WHEN lp.valor_coeficiente_linea_ppto IS NOT NULL 
             AND lp.valor_coeficiente_linea_ppto > 0 THEN
            ((lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto * (1 - lp.descuento_linea_ppto/100)) 
            * lp.valor_coeficiente_linea_ppto) 
            * (1 + lp.porcentaje_iva_linea_ppto/100)
        ELSE
            ((lp.cantidad_linea_ppto * lp.precio_unitario_linea_ppto * (1 - lp.descuento_linea_ppto/100))) 
            * (1 + lp.porcentaje_iva_linea_ppto/100)
    END AS total_linea,
    
    -- =====================================================
    -- DATOS DEL ARTÍCULO
    -- =====================================================
    a.codigo_articulo,
    a.nombre_articulo,
    a.name_articulo,
    a.imagen_articulo,
    a.precio_alquiler_articulo,
    a.es_kit_articulo,
    a.control_total_articulo,
    a.activo_articulo,
    
    -- =====================================================
    -- DATOS DEL IMPUESTO
    -- =====================================================
    lp.id_impuesto,
    imp.tipo_impuesto,
    imp.tasa_impuesto,
    imp.descr_impuesto,
    imp.activo_impuesto,
    
    -- =====================================================
    -- DATOS DE LA VERSIÓN DEL PRESUPUESTO
    -- =====================================================
    pv.id_presupuesto,
    pv.numero_version_presupuesto,
    pv.estado_version_presupuesto,
    pv.fecha_creacion_version,
    pv.fecha_envio_version,
    pv.fecha_aprobacion_version,
    
    -- =====================================================
    -- DATOS DE LA CABECERA DEL PRESUPUESTO
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
    
    -- =====================================================
    -- DATOS DEL CLIENTE
    -- =====================================================
    cl.nombre_cliente,
    cl.nif_cliente,
    cl.email_cliente,
    cl.telefono_cliente,
    cl.direccion_cliente,
    cl.cp_cliente,
    cl.poblacion_cliente,
    cl.provincia_cliente,
    
    -- =====================================================
    -- CÁLCULO: DURACIÓN DEL EVENTO
    -- =====================================================
    CASE 
        WHEN p.fecha_inicio_evento_presupuesto IS NOT NULL 
             AND p.fecha_fin_evento_presupuesto IS NOT NULL THEN
            DATEDIFF(p.fecha_fin_evento_presupuesto, p.fecha_inicio_evento_presupuesto) + 1
        ELSE NULL
    END AS duracion_evento_dias,
    
    -- =====================================================
    -- TIMESTAMPS
    -- =====================================================
    lp.created_at_linea_ppto,
    lp.updated_at_linea_ppto

FROM linea_presupuesto lp

-- JOIN 1: linea_presupuesto → presupuesto_version
INNER JOIN presupuesto_version pv 
    ON lp.id_version_presupuesto = pv.id_version_presupuesto

-- JOIN 2: presupuesto_version → presupuesto
INNER JOIN presupuesto p 
    ON pv.id_presupuesto = p.id_presupuesto

-- JOIN 3: presupuesto → cliente
INNER JOIN cliente cl 
    ON p.id_cliente = cl.id_cliente

-- JOINs opcionales (LEFT JOIN)
LEFT JOIN articulo a 
    ON lp.id_articulo = a.id_articulo

LEFT JOIN coeficiente c 
    ON lp.id_coeficiente = c.id_coeficiente

LEFT JOIN impuesto imp 
    ON lp.id_impuesto = imp.id_impuesto

WHERE lp.activo_linea_ppto = TRUE
  AND p.activo_presupuesto = TRUE;


-- ========================================================
-- CONSULTAS DE EJEMPLO
-- ========================================================

/*
-- EJEMPLO 1: Líneas de una versión específica
SELECT 
    id_version_presupuesto,
    numero_version_presupuesto,
    estado_version_presupuesto,
    numero_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    base_imponible,
    importe_iva,
    total_linea
FROM v_linea_presupuesto_calculada
WHERE id_version_presupuesto = 1
ORDER BY orden_linea_ppto;

-- EJEMPLO 2: Totales de una versión
SELECT 
    id_version_presupuesto,
    numero_version_presupuesto,
    numero_presupuesto,
    SUM(base_imponible) AS total_base,
    SUM(importe_iva) AS total_iva,
    SUM(total_linea) AS total_final
FROM v_linea_presupuesto_calculada
WHERE id_version_presupuesto = 1;
*/