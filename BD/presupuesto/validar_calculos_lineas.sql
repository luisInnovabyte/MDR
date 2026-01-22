-- ========================================================
-- VALIDACIÓN DE CÁLCULOS: Líneas de Presupuesto
-- ========================================================
-- Compara los cálculos manuales con los de la vista
-- para verificar que coincidan correctamente
-- ========================================================

USE toldos_db;

-- Mostrar datos de ejemplo del presupuesto versión 3
SELECT 
    '=== ANÁLISIS DE LÍNEAS - PRESUPUESTO VERSIÓN 3 ===' AS titulo;

-- Mostrar fechas del evento
SELECT 
    CONCAT('Evento: ', 
           DATE_FORMAT(fecha_inicio_presupuesto, '%d/%m/%Y'), 
           ' - ', 
           DATE_FORMAT(fecha_fin_presupuesto, '%d/%m/%Y'),
           ' (', 
           DATEDIFF(fecha_fin_presupuesto, fecha_inicio_presupuesto) + 1, 
           ' días)') AS datos_evento
FROM presupuesto_version pv
INNER JOIN presupuesto p ON p.id_presupuesto = pv.id_presupuesto
WHERE pv.id_version_presupuesto = 3;

SELECT '' AS separador;

-- Análisis detallado de cada línea
SELECT 
    id_linea_ppto AS ID,
    descripcion_linea_ppto AS Artículo,
    
    -- Fechas y días
    DATE_FORMAT(fecha_inicio_linea_ppto, '%d/%m/%Y') AS Fecha_Inicio,
    DATE_FORMAT(fecha_fin_linea_ppto, '%d/%m/%Y') AS Fecha_Fin,
    dias_evento AS Días,
    
    -- Datos base
    cantidad_linea_ppto AS Cant,
    CONCAT(FORMAT(precio_unitario_linea_ppto, 2), ' €') AS Precio_Unit,
    CONCAT(descuento_linea_ppto, '%') AS Desc,
    
    -- Coeficiente
    CASE 
        WHEN aplicar_coeficiente_linea_ppto = 1 THEN 'SÍ'
        ELSE 'NO'
    END AS Aplica_Coef,
    FORMAT(valor_coeficiente_linea_ppto, 2) AS Valor_Coef,
    
    -- Cálculos
    CONCAT(FORMAT(subtotal_sin_coeficiente, 2), ' €') AS Subtotal_Sin_Coef,
    CONCAT(FORMAT(base_imponible, 2), ' €') AS Base_Imponible,
    CONCAT(FORMAT(importe_iva, 2), ' €') AS IVA,
    CONCAT(FORMAT(total_linea, 2), ' €') AS Total
    
FROM v_linea_presupuesto_calculada
WHERE id_version_presupuesto = 3
ORDER BY orden_linea_ppto;

SELECT '' AS separador;

-- Verificación manual de cálculos
SELECT 
    '=== VERIFICACIÓN MANUAL DE CÁLCULOS ===' AS titulo;

SELECT 
    id_linea_ppto AS ID,
    descripcion_linea_ppto AS Artículo,
    
    -- Fórmula manual paso a paso
    CONCAT(dias_evento, ' × ', cantidad_linea_ppto, ' × ', precio_unitario_linea_ppto, ' × (1 - ', descuento_linea_ppto, '/100)') AS Formula_Base,
    
    -- Cálculo manual del subtotal
    ROUND(
        dias_evento * cantidad_linea_ppto * precio_unitario_linea_ppto * (1 - descuento_linea_ppto / 100),
        2
    ) AS Subtotal_Manual,
    
    -- Cálculo manual con coeficiente (si aplica)
    CASE 
        WHEN aplicar_coeficiente_linea_ppto = 1 THEN
            ROUND(
                dias_evento * cantidad_linea_ppto * precio_unitario_linea_ppto * (1 - descuento_linea_ppto / 100) * valor_coeficiente_linea_ppto,
                2
            )
        ELSE
            ROUND(
                dias_evento * cantidad_linea_ppto * precio_unitario_linea_ppto * (1 - descuento_linea_ppto / 100),
                2
            )
    END AS Base_Manual,
    
    -- Comparar con vista
    ROUND(base_imponible, 2) AS Base_Vista,
    
    -- ¿Coinciden?
    CASE 
        WHEN aplicar_coeficiente_linea_ppto = 1 THEN
            IF(
                ROUND(dias_evento * cantidad_linea_ppto * precio_unitario_linea_ppto * (1 - descuento_linea_ppto / 100) * valor_coeficiente_linea_ppto, 2) = ROUND(base_imponible, 2),
                '✓ OK',
                '✗ ERROR'
            )
        ELSE
            IF(
                ROUND(dias_evento * cantidad_linea_ppto * precio_unitario_linea_ppto * (1 - descuento_linea_ppto / 100), 2) = ROUND(base_imponible, 2),
                '✓ OK',
                '✗ ERROR'
            )
    END AS Validacion
    
FROM v_linea_presupuesto_calculada
WHERE id_version_presupuesto = 3
ORDER BY orden_linea_ppto;

SELECT '' AS separador;

-- Ejemplo específico: Línea 11 (con coeficiente)
SELECT 
    '=== LÍNEA 11: CON COEFICIENTE ===' AS titulo;

SELECT 
    CONCAT('Cálculo esperado (coeficiente YA considera jornadas):') AS paso,
    CONCAT('5 cant × 25 € × (1 - 10%/100) × 13.25 coef = ',
           FORMAT(5 * 25 * 0.9 * 13.25, 2), ' €') AS resultado
UNION ALL
SELECT 
    'Cálculo en vista:',
    CONCAT(FORMAT(base_imponible, 2), ' €')
FROM v_linea_presupuesto_calculada
WHERE id_linea_ppto = 11;

SELECT '' AS separador;

-- Ejemplo específico: Línea 12 (sin coeficiente)
SELECT 
    '=== LÍNEA 12: SIN COEFICIENTE ===' AS titulo;

SELECT 
    CONCAT('Cálculo esperado (sin coeficiente SÍ multiplica por días):') AS paso,
    CONCAT('14 días × 10 cant × 120 € × (1 - 10%/100) = ',
           FORMAT(14 * 10 * 120 * 0.9, 2), ' €') AS resultado
UNION ALL
SELECT 
    'Cálculo en vista:',
    CONCAT(FORMAT(base_imponible, 2), ' €')
FROM v_linea_presupuesto_calculada
WHERE id_linea_ppto = 12;
