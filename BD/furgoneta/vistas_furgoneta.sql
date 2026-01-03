-- ========================================================
-- VISTAS SQL PARA MÓDULO DE FURGONETAS
-- DESCRIPCIÓN: Vistas para facilitar consultas complejas
-- FECHA: 2024-12-23
-- ========================================================

-- ========================================================
-- VISTA 1: Vista completa de furgonetas con estadísticas
-- ========================================================
CREATE OR REPLACE VIEW vista_furgoneta_completa AS
SELECT 
    -- Datos básicos de la furgoneta
    f.id_furgoneta,
    f.matricula_furgoneta,
    f.marca_furgoneta,
    f.modelo_furgoneta,
    f.anio_furgoneta,
    f.numero_bastidor_furgoneta,
    f.kilometros_entre_revisiones_furgoneta,
    
    -- ITV y seguros
    f.fecha_proxima_itv_furgoneta,
    f.fecha_vencimiento_seguro_furgoneta,
    f.compania_seguro_furgoneta,
    f.numero_poliza_seguro_furgoneta,
    
    -- Capacidad
    f.capacidad_carga_kg_furgoneta,
    f.capacidad_carga_m3_furgoneta,
    
    -- Combustible
    f.tipo_combustible_furgoneta,
    f.consumo_medio_furgoneta,
    
    -- Taller
    f.taller_habitual_furgoneta,
    f.telefono_taller_furgoneta,
    
    -- Estado
    f.estado_furgoneta,
    f.observaciones_furgoneta,
    f.activo_furgoneta,
    
    -- Timestamps
    f.created_at_furgoneta,
    f.updated_at_furgoneta,
    
    -- Estadísticas de kilometraje
    (SELECT MAX(kilometraje_registrado_km) 
     FROM furgoneta_registro_kilometraje 
     WHERE id_furgoneta = f.id_furgoneta) AS kilometraje_actual,
    
    (SELECT fecha_registro_km 
     FROM furgoneta_registro_kilometraje 
     WHERE id_furgoneta = f.id_furgoneta 
     ORDER BY fecha_registro_km DESC 
     LIMIT 1) AS fecha_ultimo_registro_km,
    
    -- Estadísticas de mantenimiento
    (SELECT COUNT(*) 
     FROM furgoneta_mantenimiento 
     WHERE id_furgoneta = f.id_furgoneta 
     AND activo_mantenimiento = 1) AS total_mantenimientos,
    
    (SELECT SUM(costo_mantenimiento) 
     FROM furgoneta_mantenimiento 
     WHERE id_furgoneta = f.id_furgoneta 
     AND activo_mantenimiento = 1) AS costo_total_mantenimientos,
    
    (SELECT fecha_mantenimiento 
     FROM furgoneta_mantenimiento 
     WHERE id_furgoneta = f.id_furgoneta 
     AND activo_mantenimiento = 1
     ORDER BY fecha_mantenimiento DESC 
     LIMIT 1) AS fecha_ultimo_mantenimiento,
    
    -- Alertas
    CASE 
        WHEN DATEDIFF(f.fecha_proxima_itv_furgoneta, CURDATE()) < 0 THEN 'ITV_VENCIDA'
        WHEN DATEDIFF(f.fecha_proxima_itv_furgoneta, CURDATE()) <= 30 THEN 'ITV_PROXIMA'
        ELSE 'ITV_OK'
    END AS estado_itv,
    
    CASE 
        WHEN DATEDIFF(f.fecha_vencimiento_seguro_furgoneta, CURDATE()) < 0 THEN 'SEGURO_VENCIDO'
        WHEN DATEDIFF(f.fecha_vencimiento_seguro_furgoneta, CURDATE()) <= 30 THEN 'SEGURO_PROXIMO'
        ELSE 'SEGURO_OK'
    END AS estado_seguro,
    
    -- Kilómetros desde última revisión
    CASE 
        WHEN (SELECT MAX(kilometraje_registrado_km) FROM furgoneta_registro_kilometraje WHERE id_furgoneta = f.id_furgoneta) IS NOT NULL
        THEN (
            (SELECT MAX(kilometraje_registrado_km) FROM furgoneta_registro_kilometraje WHERE id_furgoneta = f.id_furgoneta) -
            COALESCE((SELECT kilometraje_mantenimiento 
                     FROM furgoneta_mantenimiento 
                     WHERE id_furgoneta = f.id_furgoneta 
                     AND tipo_mantenimiento = 'revision'
                     AND activo_mantenimiento = 1
                     ORDER BY fecha_mantenimiento DESC 
                     LIMIT 1), 0)
        )
        ELSE NULL
    END AS km_desde_ultima_revision,
    
    -- Necesita revisión
    CASE 
        WHEN (SELECT MAX(kilometraje_registrado_km) FROM furgoneta_registro_kilometraje WHERE id_furgoneta = f.id_furgoneta) IS NOT NULL
        AND (
            (SELECT MAX(kilometraje_registrado_km) FROM furgoneta_registro_kilometraje WHERE id_furgoneta = f.id_furgoneta) -
            COALESCE((SELECT kilometraje_mantenimiento 
                     FROM furgoneta_mantenimiento 
                     WHERE id_furgoneta = f.id_furgoneta 
                     AND tipo_mantenimiento = 'revision'
                     AND activo_mantenimiento = 1
                     ORDER BY fecha_mantenimiento DESC 
                     LIMIT 1), 0)
        ) >= f.kilometros_entre_revisiones_furgoneta
        THEN TRUE
        ELSE FALSE
    END AS necesita_revision

FROM furgoneta f;

-- ========================================================
-- VISTA 2: Registro de kilometraje con información de furgoneta
-- ========================================================
CREATE OR REPLACE VIEW vista_registro_kilometraje AS
SELECT 
    rk.id_registro_km,
    rk.id_furgoneta,
    rk.fecha_registro_km,
    rk.kilometraje_registrado_km,
    rk.tipo_registro_km,
    rk.observaciones_registro_km,
    rk.created_at_registro_km,
    
    -- Datos de la furgoneta
    f.matricula_furgoneta,
    f.marca_furgoneta,
    f.modelo_furgoneta,
    f.estado_furgoneta,
    
    -- Cálculo de kilómetros recorridos desde el registro anterior
    (rk.kilometraje_registrado_km - 
     COALESCE((SELECT kilometraje_registrado_km 
               FROM furgoneta_registro_kilometraje 
               WHERE id_furgoneta = rk.id_furgoneta 
               AND fecha_registro_km < rk.fecha_registro_km 
               ORDER BY fecha_registro_km DESC 
               LIMIT 1), 0)) AS km_recorridos,
    
    -- Días transcurridos desde el registro anterior
    DATEDIFF(rk.fecha_registro_km,
             COALESCE((SELECT fecha_registro_km 
                       FROM furgoneta_registro_kilometraje 
                       WHERE id_furgoneta = rk.id_furgoneta 
                       AND fecha_registro_km < rk.fecha_registro_km 
                       ORDER BY fecha_registro_km DESC 
                       LIMIT 1), rk.fecha_registro_km)) AS dias_transcurridos,
    
    -- Promedio diario de kilómetros
    CASE 
        WHEN DATEDIFF(rk.fecha_registro_km,
                     COALESCE((SELECT fecha_registro_km 
                               FROM furgoneta_registro_kilometraje 
                               WHERE id_furgoneta = rk.id_furgoneta 
                               AND fecha_registro_km < rk.fecha_registro_km 
                               ORDER BY fecha_registro_km DESC 
                               LIMIT 1), rk.fecha_registro_km)) > 0
        THEN (rk.kilometraje_registrado_km - 
              COALESCE((SELECT kilometraje_registrado_km 
                        FROM furgoneta_registro_kilometraje 
                        WHERE id_furgoneta = rk.id_furgoneta 
                        AND fecha_registro_km < rk.fecha_registro_km 
                        ORDER BY fecha_registro_km DESC 
                        LIMIT 1), 0)) / 
             DATEDIFF(rk.fecha_registro_km,
                     COALESCE((SELECT fecha_registro_km 
                               FROM furgoneta_registro_kilometraje 
                               WHERE id_furgoneta = rk.id_furgoneta 
                               AND fecha_registro_km < rk.fecha_registro_km 
                               ORDER BY fecha_registro_km DESC 
                               LIMIT 1), rk.fecha_registro_km))
        ELSE 0
    END AS km_promedio_diario

FROM furgoneta_registro_kilometraje rk
INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
ORDER BY rk.fecha_registro_km DESC;

-- ========================================================
-- VISTA 3: Mantenimientos con información completa
-- ========================================================
CREATE OR REPLACE VIEW vista_mantenimiento_completo AS
SELECT 
    m.id_mantenimiento,
    m.id_furgoneta,
    m.fecha_mantenimiento,
    m.tipo_mantenimiento,
    m.descripcion_mantenimiento,
    m.kilometraje_mantenimiento,
    m.costo_mantenimiento,
    m.numero_factura_mantenimiento,
    m.taller_mantenimiento,
    m.telefono_taller_mantenimiento,
    m.direccion_taller_mantenimiento,
    m.resultado_itv,
    m.fecha_proxima_itv,
    m.garantia_hasta_mantenimiento,
    m.observaciones_mantenimiento,
    m.activo_mantenimiento,
    m.created_at_mantenimiento,
    m.updated_at_mantenimiento,
    
    -- Datos de la furgoneta
    f.matricula_furgoneta,
    f.marca_furgoneta,
    f.modelo_furgoneta,
    f.anio_furgoneta,
    f.estado_furgoneta,
    
    -- Estado de la garantía
    CASE 
        WHEN m.garantia_hasta_mantenimiento IS NULL THEN 'SIN_GARANTIA'
        WHEN m.garantia_hasta_mantenimiento < CURDATE() THEN 'GARANTIA_VENCIDA'
        WHEN DATEDIFF(m.garantia_hasta_mantenimiento, CURDATE()) <= 30 THEN 'GARANTIA_PROXIMA'
        ELSE 'GARANTIA_VIGENTE'
    END AS estado_garantia,
    
    -- Días desde el mantenimiento
    DATEDIFF(CURDATE(), m.fecha_mantenimiento) AS dias_desde_mantenimiento,
    
    -- Kilómetros recorridos desde este mantenimiento
    CASE 
        WHEN (SELECT MAX(kilometraje_registrado_km) 
              FROM furgoneta_registro_kilometraje 
              WHERE id_furgoneta = m.id_furgoneta) IS NOT NULL
        AND m.kilometraje_mantenimiento IS NOT NULL
        THEN (SELECT MAX(kilometraje_registrado_km) 
              FROM furgoneta_registro_kilometraje 
              WHERE id_furgoneta = m.id_furgoneta) - m.kilometraje_mantenimiento
        ELSE NULL
    END AS km_desde_mantenimiento

FROM furgoneta_mantenimiento m
INNER JOIN furgoneta f ON m.id_furgoneta = f.id_furgoneta
WHERE m.activo_mantenimiento = 1
ORDER BY m.fecha_mantenimiento DESC;

-- ========================================================
-- VISTA 4: Resumen de costos por furgoneta
-- ========================================================
CREATE OR REPLACE VIEW vista_costos_furgoneta AS
SELECT 
    f.id_furgoneta,
    f.matricula_furgoneta,
    f.marca_furgoneta,
    f.modelo_furgoneta,
    f.anio_furgoneta,
    
    -- Costos totales
    COALESCE(SUM(m.costo_mantenimiento), 0) AS costo_total,
    
    -- Costos por año actual
    COALESCE(SUM(CASE 
        WHEN YEAR(m.fecha_mantenimiento) = YEAR(CURDATE()) 
        THEN m.costo_mantenimiento 
        ELSE 0 
    END), 0) AS costo_anio_actual,
    
    -- Costos por tipo
    COALESCE(SUM(CASE 
        WHEN m.tipo_mantenimiento = 'revision' 
        THEN m.costo_mantenimiento 
        ELSE 0 
    END), 0) AS costo_revisiones,
    
    COALESCE(SUM(CASE 
        WHEN m.tipo_mantenimiento = 'reparacion' 
        THEN m.costo_mantenimiento 
        ELSE 0 
    END), 0) AS costo_reparaciones,
    
    COALESCE(SUM(CASE 
        WHEN m.tipo_mantenimiento = 'itv' 
        THEN m.costo_mantenimiento 
        ELSE 0 
    END), 0) AS costo_itv,
    
    COALESCE(SUM(CASE 
        WHEN m.tipo_mantenimiento = 'neumaticos' 
        THEN m.costo_mantenimiento 
        ELSE 0 
    END), 0) AS costo_neumaticos,
    
    -- Conteo de mantenimientos
    COUNT(m.id_mantenimiento) AS total_mantenimientos,
    
    -- Promedio de costo
    COALESCE(AVG(m.costo_mantenimiento), 0) AS costo_promedio,
    
    -- Último mantenimiento
    MAX(m.fecha_mantenimiento) AS fecha_ultimo_mantenimiento,
    
    -- Kilometraje actual
    (SELECT MAX(kilometraje_registrado_km) 
     FROM furgoneta_registro_kilometraje 
     WHERE id_furgoneta = f.id_furgoneta) AS kilometraje_actual,
    
    -- Costo por kilómetro
    CASE 
        WHEN (SELECT MAX(kilometraje_registrado_km) 
              FROM furgoneta_registro_kilometraje 
              WHERE id_furgoneta = f.id_furgoneta) > 0
        THEN COALESCE(SUM(m.costo_mantenimiento), 0) / 
             (SELECT MAX(kilometraje_registrado_km) 
              FROM furgoneta_registro_kilometraje 
              WHERE id_furgoneta = f.id_furgoneta)
        ELSE NULL
    END AS costo_por_km

FROM furgoneta f
LEFT JOIN furgoneta_mantenimiento m ON f.id_furgoneta = m.id_furgoneta 
    AND m.activo_mantenimiento = 1
GROUP BY f.id_furgoneta, f.matricula_furgoneta, f.marca_furgoneta, 
         f.modelo_furgoneta, f.anio_furgoneta;

-- ========================================================
-- COMENTARIOS SOBRE LAS VISTAS
-- ========================================================

/*
VISTA 1 - vista_furgoneta_completa:
    Proporciona toda la información de la furgoneta junto con estadísticas
    calculadas de kilometraje y mantenimiento. Incluye alertas de ITV y seguro.

VISTA 2 - vista_registro_kilometraje:
    Muestra el historial de kilometraje con cálculos de distancia recorrida
    entre registros y promedios diarios.

VISTA 3 - vista_mantenimiento_completo:
    Presenta el historial de mantenimientos con información completa de la
    furgoneta y cálculos de garantías y kilómetros desde el mantenimiento.

VISTA 4 - vista_costos_furgoneta:
    Resumen financiero por furgoneta con desglose de costos por tipo y período.
    Útil para análisis de rentabilidad y toma de decisiones.
*/
