-- ========================================================
-- AGREGAR aplicar_coeficiente_linea_ppto A LA VISTA
-- ========================================================
-- Fecha: 2026-01-22
-- Descripción: Agregar el campo aplicar_coeficiente_linea_ppto
--              a la vista v_linea_presupuesto_calculada
-- ========================================================

-- Ejecutar el script completo de la vista actualizada
SOURCE w:/MDR/BD/presupuesto/v_linea_presupuesto_calculada.sql;

-- Verificar que el campo esté en la vista
DESCRIBE v_linea_presupuesto_calculada;

-- Probar una consulta
SELECT 
    id_linea_ppto,
    descripcion_linea_ppto,
    jornadas_linea_ppto,
    aplicar_coeficiente_linea_ppto,
    valor_coeficiente_linea_ppto
FROM v_linea_presupuesto_calculada
LIMIT 5;

SELECT 'Vista actualizada correctamente con aplicar_coeficiente_linea_ppto' AS resultado;
