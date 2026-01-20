-- Insertar registro con FECHA DIFERENTE (10 de enero)
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
    1,                              
    '2026-01-10',                   -- ⚠️ IMPORTANTE: Esta fecha es DIFERENTE a los otros registros
    500,                            
    'manual',                       
    'Prueba con fecha diferente - 10 de enero'
);

-- Verificar después de insertar
SELECT 
    id_registro_km,
    fecha_registro_km,
    kilometraje_registrado_km,
    km_recorridos,
    dias_transcurridos,
    km_promedio_diario
FROM vista_kilometraje_completo
WHERE id_furgoneta = 1
ORDER BY fecha_registro_km DESC, kilometraje_registrado_km DESC;
