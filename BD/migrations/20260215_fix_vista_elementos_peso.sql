-- ═══════════════════════════════════════════════════════════
-- FIX: Añadir campo peso_elemento a vista_elementos_completa
-- Fecha: 15 de febrero de 2026
-- Descripción: La vista no incluía peso_elemento, por eso no se
--              cargaba al editar elementos
-- ═══════════════════════════════════════════════════════════

CREATE OR REPLACE VIEW vista_elementos_completa AS
SELECT 
    -- ID y códigos principales
    e.id_elemento,
    e.codigo_elemento,
    e.codigo_barras_elemento,
    e.descripcion_elemento,
    e.numero_serie_elemento,
    e.modelo_elemento,
    
    -- Ubicación
    e.nave_elemento,
    e.pasillo_columna_elemento,
    e.altura_elemento,
    CONCAT_WS(' | ', 
        COALESCE(e.nave_elemento, ''),
        COALESCE(e.pasillo_columna_elemento, ''),
        COALESCE(e.altura_elemento, '')
    ) AS ubicacion_completa_elemento,
    
    -- ✅ PESO (CAMPO NUEVO)
    e.peso_elemento,
    
    -- Fechas y precios de compra
    e.fecha_compra_elemento,
    e.precio_compra_elemento,
    e.fecha_alta_elemento,
    e.fecha_fin_garantia_elemento,
    e.proximo_mantenimiento_elemento,
    e.observaciones_elemento,
    
    -- Gestión de propiedad
    e.es_propio_elemento,
    e.id_proveedor_compra_elemento,
    e.id_proveedor_alquiler_elemento,
    e.precio_dia_alquiler_elemento,
    e.id_forma_pago_alquiler_elemento,
    e.observaciones_alquiler_elemento,
    
    -- Proveedor de compra
    prov_compra.codigo_proveedor AS codigo_proveedor_compra,
    prov_compra.nombre_proveedor AS nombre_proveedor_compra,
    prov_compra.telefono_proveedor AS telefono_proveedor_compra,
    prov_compra.email_proveedor AS email_proveedor_compra,
    prov_compra.nif_proveedor AS nif_proveedor_compra,
    
    -- Proveedor de alquiler
    prov_alquiler.codigo_proveedor AS codigo_proveedor_alquiler,
    prov_alquiler.nombre_proveedor AS nombre_proveedor_alquiler,
    prov_alquiler.telefono_proveedor AS telefono_proveedor_alquiler,
    prov_alquiler.email_proveedor AS email_proveedor_alquiler,
    prov_alquiler.nif_proveedor AS nif_proveedor_alquiler,
    prov_alquiler.persona_contacto_proveedor AS persona_contacto_proveedor_alquiler,
    
    -- Forma de pago de alquiler
    fp_alquiler.codigo_pago AS codigo_forma_pago_alquiler,
    fp_alquiler.nombre_pago AS nombre_forma_pago_alquiler,
    fp_alquiler.porcentaje_anticipo_pago AS porcentaje_anticipo_alquiler,
    fp_alquiler.dias_anticipo_pago AS dias_anticipo_alquiler,
    fp_alquiler.porcentaje_final_pago AS porcentaje_final_alquiler,
    fp_alquiler.dias_final_pago AS dias_final_alquiler,
    fp_alquiler.descuento_pago AS descuento_forma_pago_alquiler,
    
    -- Método de pago de alquiler
    mp_alquiler.codigo_metodo_pago AS codigo_metodo_pago_alquiler,
    mp_alquiler.nombre_metodo_pago AS nombre_metodo_pago_alquiler,
    
    -- Artículo
    a.id_articulo,
    a.codigo_articulo,
    a.nombre_articulo,
    a.name_articulo,
    a.precio_alquiler_articulo,
    
    -- Familia
    f.id_familia,
    f.codigo_familia,
    f.nombre_familia,
    f.name_familia,
    
    -- Grupo
    g.id_grupo,
    g.codigo_grupo,
    g.nombre_grupo,
    
    -- Marca
    m.id_marca,
    m.codigo_marca,
    m.nombre_marca,
    
    -- Estado
    est.id_estado_elemento,
    est.codigo_estado_elemento,
    est.descripcion_estado_elemento,
    est.color_estado_elemento,
    est.permite_alquiler_estado_elemento,
    
    -- Control
    e.activo_elemento,
    e.created_at_elemento,
    e.updated_at_elemento,
    
    -- Campos calculados
    CONCAT_WS(' > ',
        COALESCE(g.nombre_grupo, 'Sin grupo'),
        f.nombre_familia,
        a.nombre_articulo,
        e.descripcion_elemento
    ) AS jerarquia_completa_elemento,
    
    CASE 
        WHEN e.es_propio_elemento = TRUE THEN 'PROPIO'
        ELSE 'ALQUILADO A PROVEEDOR'
    END AS tipo_propiedad_elemento,
    
    CASE 
        WHEN e.es_propio_elemento = TRUE THEN prov_compra.nombre_proveedor
        ELSE prov_alquiler.nombre_proveedor
    END AS proveedor_principal_elemento,
    
    CASE
        WHEN e.es_propio_elemento = TRUE THEN 'N/A - Equipo propio'
        WHEN e.id_proveedor_alquiler_elemento IS NULL THEN 'Sin proveedor asignado'
        WHEN e.precio_dia_alquiler_elemento IS NULL OR e.precio_dia_alquiler_elemento = 0 THEN 'Proveedor asignado - Falta precio'
        WHEN e.id_forma_pago_alquiler_elemento IS NULL THEN 'Proveedor y precio OK - Falta forma de pago'
        ELSE 'Completamente configurado'
    END AS estado_configuracion_alquiler,
    
    CASE 
        WHEN e.es_propio_elemento = FALSE AND fp_alquiler.id_pago IS NOT NULL THEN
            CASE 
                WHEN fp_alquiler.porcentaje_anticipo_pago = 100.00 THEN
                    CONCAT(mp_alquiler.nombre_metodo_pago, ' - ', fp_alquiler.nombre_pago,
                        CASE WHEN fp_alquiler.descuento_pago > 0 
                            THEN CONCAT(' (Dto: ', fp_alquiler.descuento_pago, '%)')
                            ELSE ''
                        END
                    )
                ELSE
                    CONCAT(mp_alquiler.nombre_metodo_pago, ' - ', 
                           fp_alquiler.porcentaje_anticipo_pago, '% + ', 
                           fp_alquiler.porcentaje_final_pago, '%')
            END
        ELSE NULL
    END AS descripcion_forma_pago_alquiler,
    
    CASE 
        WHEN e.es_propio_elemento = FALSE AND e.precio_dia_alquiler_elemento IS NOT NULL THEN
            ROUND(e.precio_dia_alquiler_elemento * 30, 2)
        ELSE NULL
    END AS costo_mensual_estimado_alquiler,
    
    DATEDIFF(CURDATE(), e.fecha_alta_elemento) AS dias_en_servicio_elemento,
    
    ROUND(DATEDIFF(CURDATE(), e.fecha_alta_elemento) / 365.25, 2) AS anios_en_servicio_elemento

FROM elemento e

INNER JOIN articulo a ON e.id_articulo_elemento = a.id_articulo
INNER JOIN familia f ON a.id_familia = f.id_familia
LEFT JOIN grupo_articulo g ON f.id_grupo = g.id_grupo
LEFT JOIN marca m ON e.id_marca_elemento = m.id_marca
INNER JOIN estado_elemento est ON e.id_estado_elemento = est.id_estado_elemento
LEFT JOIN proveedor prov_compra ON e.id_proveedor_compra_elemento = prov_compra.id_proveedor
LEFT JOIN proveedor prov_alquiler ON e.id_proveedor_alquiler_elemento = prov_alquiler.id_proveedor
LEFT JOIN forma_pago fp_alquiler ON e.id_forma_pago_alquiler_elemento = fp_alquiler.id_pago
LEFT JOIN metodo_pago mp_alquiler ON fp_alquiler.id_metodo_pago = mp_alquiler.id_metodo_pago;

-- ═══════════════════════════════════════════════════════════
-- VERIFICACIÓN
-- ═══════════════════════════════════════════════════════════

-- Verificar que el campo peso_elemento existe en la vista
SELECT COLUMN_NAME 
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'vista_elementos_completa'
  AND COLUMN_NAME = 'peso_elemento';

-- Si devuelve 1 fila, el campo está incluido ✅
