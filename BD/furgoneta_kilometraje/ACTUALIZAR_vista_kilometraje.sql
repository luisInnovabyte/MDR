-- ========================================================
-- SCRIPT DE ACTUALIZACIÓN: Vista kilometraje_completo
-- DESCRIPCIÓN: Actualiza la vista para usar LAG() en lugar de subconsultas
--              Esto mejora el rendimiento y corrige el cálculo de días y km/día
-- FECHA: 2026-01-03
-- ========================================================

-- Eliminar vista anterior si existe
DROP VIEW IF EXISTS vista_kilometraje_completo;

-- Crear vista actualizada con LAG()
CREATE OR REPLACE VIEW vista_kilometraje_completo AS
SELECT 
    -- =====================================================
    -- DATOS DEL REGISTRO DE KILOMETRAJE
    -- =====================================================
    rk.id_registro_km,
    rk.id_furgoneta,
    rk.fecha_registro_km,
    rk.kilometraje_registrado_km,
    rk.tipo_registro_km,
    rk.observaciones_registro_km,
    rk.created_at_registro_km,
    rk.updated_at_registro_km,
    
    -- =====================================================
    -- DATOS DE LA FURGONETA
    -- =====================================================
    f.matricula_furgoneta,
    f.marca_furgoneta,
    f.modelo_furgoneta,
    f.estado_furgoneta,
    
    -- =====================================================
    -- CÁLCULO: Kilómetros recorridos desde registro anterior
    -- Usando LAG() para obtener el kilometraje anterior
    -- El ORDER BY usa fecha_registro_km DESC y luego kilometraje ASC
    -- para que los registros del mismo día se ordenen por km menor a mayor
    -- =====================================================
    COALESCE(
        rk.kilometraje_registrado_km - LAG(rk.kilometraje_registrado_km) 
        OVER (PARTITION BY rk.id_furgoneta ORDER BY rk.fecha_registro_km ASC, rk.kilometraje_registrado_km ASC),
        0
    ) AS km_recorridos,
    
    -- =====================================================
    -- CÁLCULO: Días transcurridos desde registro anterior
    -- Usando LAG() para obtener la fecha anterior
    -- =====================================================
    COALESCE(
        DATEDIFF(
            rk.fecha_registro_km,
            LAG(rk.fecha_registro_km) OVER (PARTITION BY rk.id_furgoneta ORDER BY rk.fecha_registro_km ASC, rk.kilometraje_registrado_km ASC)
        ),
        0
    ) AS dias_transcurridos,
    
    -- =====================================================
    -- CÁLCULO: Promedio diario de kilómetros (km/día)
    -- Solo calcula si hay días transcurridos > 0
    -- =====================================================
    CASE 
        WHEN COALESCE(
            DATEDIFF(
                rk.fecha_registro_km,
                LAG(rk.fecha_registro_km) OVER (PARTITION BY rk.id_furgoneta ORDER BY rk.fecha_registro_km ASC, rk.kilometraje_registrado_km ASC)
            ),
            0
        ) > 0
        THEN ROUND(
            COALESCE(
                rk.kilometraje_registrado_km - LAG(rk.kilometraje_registrado_km) 
                OVER (PARTITION BY rk.id_furgoneta ORDER BY rk.fecha_registro_km ASC, rk.kilometraje_registrado_km ASC),
                0
            ) / 
            COALESCE(
                DATEDIFF(
                    rk.fecha_registro_km,
                    LAG(rk.fecha_registro_km) OVER (PARTITION BY rk.id_furgoneta ORDER BY rk.fecha_registro_km ASC, rk.kilometraje_registrado_km ASC)
                ),
                1
            ),
            2
        )
        ELSE 0
    END AS km_promedio_diario

FROM furgoneta_registro_kilometraje rk
INNER JOIN furgoneta f ON rk.id_furgoneta = f.id_furgoneta
ORDER BY rk.id_furgoneta ASC, rk.fecha_registro_km DESC, rk.kilometraje_registrado_km DESC;

-- ========================================================
-- VERIFICACIÓN: Consultar datos de prueba
-- ========================================================
-- Descomenta las siguientes líneas para verificar:
-- SELECT * FROM vista_kilometraje_completo LIMIT 10;
-- 
-- SELECT 
--     fecha_registro_km,
--     kilometraje_registrado_km,
--     km_recorridos,
--     dias_transcurridos,
--     km_promedio_diario
-- FROM vista_kilometraje_completo
-- WHERE id_furgoneta = 1
-- ORDER BY fecha_registro_km DESC;
