-- ========================================================
-- SCRIPT DE PRUEBA: Insertar registro con fecha diferente
-- DESCRIPCIÓN: Inserta un registro de kilometraje con fecha posterior
--              para verificar que los cálculos de días y km/día funcionan
-- FECHA: 2026-01-03
-- ========================================================

-- Insertar registro de prueba con fecha 10/01/2026 (7 días después)
INSERT INTO furgoneta_registro_kilometraje 
(
    id_furgoneta, 
    fecha_registro_km, 
    kilometraje_registrado_km, 
    tipo_registro_km, 
    observaciones_registro_km
)
VALUES
(
    1,                              -- Furgoneta ID 1 (la misma que los otros registros)
    '2026-01-10',                   -- 7 días después del último registro (03/01/2026)
    500,                            -- 500 km (350 km más que el último de 150 km)
    'manual',                       -- Tipo: manual
    'Registro de prueba para verificar cálculos de días y km/día'
);

-- ========================================================
-- VERIFICACIÓN: Consultar los datos después de la inserción
-- ========================================================
SELECT 
    id_registro_km,
    fecha_registro_km AS 'Fecha',
    kilometraje_registrado_km AS 'Kilometraje',
    km_recorridos AS 'KM Recorridos',
    dias_transcurridos AS 'Días',
    km_promedio_diario AS 'KM/Día'
FROM vista_kilometraje_completo
WHERE id_furgoneta = 1
ORDER BY fecha_registro_km DESC, kilometraje_registrado_km DESC;

-- ========================================================
-- RESULTADO ESPERADO:
-- ========================================================
-- | Fecha      | Kilometraje | KM Recorridos | Días | KM/Día |
-- |------------|-------------|---------------|------|---------|
-- | 2026-01-10 | 500 km      | 350 km        | 7    | 50.0    |
-- | 2026-01-03 | 150 km      | 50 km         | 0    | 0.00    |
-- | 2026-01-03 | 100 km      | 0 km          | 0    | 0.00    |
-- ========================================================
