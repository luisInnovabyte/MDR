-- Verificar registros en la tabla
SELECT 
    id_registro_km,
    fecha_registro_km,
    kilometraje_registrado_km,
    tipo_registro_km
FROM furgoneta_registro_kilometraje
WHERE id_furgoneta = 1
ORDER BY fecha_registro_km ASC, kilometraje_registrado_km ASC;

-- Verificar cálculos en la vista
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
