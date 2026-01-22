-- ========================================================
-- SCRIPT DE ACTUALIZACIÓN: Vista v_linea_presupuesto_calculada
-- ========================================================
-- CORRECCIÓN COMPLETA:
--   1. Usar aplicar_coeficiente_linea_ppto en lugar de solo verificar NULL
--   2. Multiplicar por DÍAS solo cuando NO se aplica coeficiente
--      - SIN coeficiente: días × cantidad × precio × (1 - descuento/100)
--      - CON coeficiente: cantidad × precio × (1 - descuento/100) × coeficiente
--   NOTA IMPORTANTE: El valor_coeficiente YA incorpora el ajuste por jornadas
-- FECHA: 2026-01-22
-- PROBLEMA RESUELTO: Cálculos correctos tanto con como sin coeficiente
-- ========================================================

USE toldos_db;

SOURCE w:/MDR/BD/presupuesto/v_linea_presupuesto_calculada.sql;

-- Verificar que la vista se actualizó correctamente
SELECT 'Vista actualizada correctamente con multiplicación por días' AS resultado;

-- Probar la vista con líneas con y sin coeficiente
SELECT 
    id_linea_ppto,
    descripcion_linea_ppto,
    fecha_inicio_linea_ppto,
    fecha_fin_linea_ppto,
    dias_evento,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    descuento_linea_ppto,
    aplicar_coeficiente_linea_ppto,
    valor_coeficiente_linea_ppto,
    ROUND(subtotal_sin_coeficiente, 2) AS subtotal_sin_coef,
    ROUND(base_imponible, 2) AS base_imponible,
    ROUND(importe_iva, 2) AS importe_iva,
    ROUND(total_linea, 2) AS total_linea
FROM v_linea_presupuesto_calculada
WHERE id_version_presupuesto = 3
ORDER BY orden_linea_ppto;
