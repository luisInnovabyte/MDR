-- ========================================================
-- VISTA: v_presupuesto_totales
-- ========================================================
-- VERSIÓN: DEFINITIVA - Nomenclatura 100% verificada
-- FECHA: 2025-01-20
-- AUTOR: Luis - MDR ERP Manager
--
-- PROPÓSITO:
--   Vista agregada para obtener totales del PIE del presupuesto
--   por VERSIÓN, con desglose completo de IVA
-- ========================================================

DROP VIEW IF EXISTS v_presupuesto_totales;

CREATE VIEW v_presupuesto_totales AS
SELECT 
    -- =====================================================
    -- IDENTIFICACIÓN DE LA VERSIÓN
    -- =====================================================
    vlpc.id_version_presupuesto,
    vlpc.numero_version_presupuesto,
    vlpc.estado_version_presupuesto,
    vlpc.fecha_creacion_version,
    vlpc.fecha_envio_version,
    vlpc.fecha_aprobacion_version,
    
    -- =====================================================
    -- IDENTIFICACIÓN DEL PRESUPUESTO
    -- =====================================================
    vlpc.id_presupuesto,
    vlpc.numero_presupuesto,
    vlpc.fecha_presupuesto,
    vlpc.fecha_validez_presupuesto,
    vlpc.nombre_evento_presupuesto,
    vlpc.fecha_inicio_evento_presupuesto,
    vlpc.fecha_fin_evento_presupuesto,
    
    -- =====================================================
    -- DATOS DEL CLIENTE
    -- =====================================================
    vlpc.id_cliente,
    vlpc.nombre_cliente,
    vlpc.nif_cliente,
    vlpc.email_cliente,
    vlpc.telefono_cliente,
    
    -- =====================================================
    -- DURACIÓN DEL EVENTO
    -- =====================================================
    MAX(vlpc.duracion_evento_dias) AS duracion_evento_dias,
    
    -- =====================================================
    -- TOTALES GENERALES
    -- =====================================================
    SUM(vlpc.base_imponible) AS total_base_imponible,
    SUM(vlpc.importe_iva) AS total_iva,
    SUM(vlpc.total_linea) AS total_con_iva,
    
    -- =====================================================
    -- CANTIDAD DE LÍNEAS
    -- =====================================================
    COUNT(*) AS cantidad_lineas_total,
    COUNT(CASE WHEN vlpc.valor_coeficiente_linea_ppto IS NOT NULL THEN 1 END) AS cantidad_lineas_con_coeficiente,
    
    -- =====================================================
    -- DESGLOSE DE IVA 21%
    -- =====================================================
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 21.00 THEN vlpc.base_imponible 
        ELSE 0 
    END) AS base_iva_21,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 21.00 THEN vlpc.importe_iva 
        ELSE 0 
    END) AS importe_iva_21,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 21.00 THEN vlpc.total_linea 
        ELSE 0 
    END) AS total_iva_21,
    
    -- =====================================================
    -- DESGLOSE DE IVA 10%
    -- =====================================================
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 10.00 THEN vlpc.base_imponible 
        ELSE 0 
    END) AS base_iva_10,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 10.00 THEN vlpc.importe_iva 
        ELSE 0 
    END) AS importe_iva_10,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 10.00 THEN vlpc.total_linea 
        ELSE 0 
    END) AS total_iva_10,
    
    -- =====================================================
    -- DESGLOSE DE IVA 4%
    -- =====================================================
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 4.00 THEN vlpc.base_imponible 
        ELSE 0 
    END) AS base_iva_4,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 4.00 THEN vlpc.importe_iva 
        ELSE 0 
    END) AS importe_iva_4,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 4.00 THEN vlpc.total_linea 
        ELSE 0 
    END) AS total_iva_4,
    
    -- =====================================================
    -- IVA 0% (SIN_IVA intracomunitarias)
    -- =====================================================
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 0.00 THEN vlpc.base_imponible 
        ELSE 0 
    END) AS base_iva_0,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 0.00 THEN vlpc.importe_iva 
        ELSE 0 
    END) AS importe_iva_0,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto = 0.00 THEN vlpc.total_linea 
        ELSE 0 
    END) AS total_iva_0,
    
    -- =====================================================
    -- OTROS TIPOS DE IVA
    -- =====================================================
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto NOT IN (21.00, 10.00, 4.00, 0.00) 
        THEN vlpc.base_imponible 
        ELSE 0 
    END) AS base_iva_otros,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto NOT IN (21.00, 10.00, 4.00, 0.00) 
        THEN vlpc.importe_iva 
        ELSE 0 
    END) AS importe_iva_otros,
    
    SUM(CASE 
        WHEN vlpc.porcentaje_iva_linea_ppto NOT IN (21.00, 10.00, 4.00, 0.00) 
        THEN vlpc.total_linea 
        ELSE 0 
    END) AS total_iva_otros,
    
    -- =====================================================
    -- AHORRO POR COEFICIENTES
    -- =====================================================
    SUM(vlpc.subtotal_sin_coeficiente - vlpc.base_imponible) AS ahorro_total_coeficientes,
    
    -- =====================================================
    -- FECHAS DE AUDITORÍA
    -- =====================================================
    MIN(vlpc.created_at_linea_ppto) AS fecha_primera_linea_creada,
    MAX(vlpc.updated_at_linea_ppto) AS fecha_ultima_modificacion_linea

FROM v_linea_presupuesto_calculada vlpc

GROUP BY 
    vlpc.id_version_presupuesto,
    vlpc.numero_version_presupuesto,
    vlpc.estado_version_presupuesto,
    vlpc.fecha_creacion_version,
    vlpc.fecha_envio_version,
    vlpc.fecha_aprobacion_version,
    vlpc.id_presupuesto,
    vlpc.numero_presupuesto,
    vlpc.fecha_presupuesto,
    vlpc.fecha_validez_presupuesto,
    vlpc.nombre_evento_presupuesto,
    vlpc.fecha_inicio_evento_presupuesto,
    vlpc.fecha_fin_evento_presupuesto,
    vlpc.id_cliente,
    vlpc.nombre_cliente,
    vlpc.nif_cliente,
    vlpc.email_cliente,
    vlpc.telefono_cliente;


-- ========================================================
-- CONSULTAS DE EJEMPLO
-- ========================================================

/*
-- PIE de una versión específica
SELECT * FROM v_presupuesto_totales WHERE id_version_presupuesto = 1;

-- Comparar versiones
SELECT 
    numero_presupuesto,
    numero_version_presupuesto,
    estado_version_presupuesto,
    total_con_iva
FROM v_presupuesto_totales
WHERE id_presupuesto = 1
ORDER BY numero_version_presupuesto;
*/