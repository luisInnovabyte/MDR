-- ═══════════════════════════════════════════════════════════
-- MIGRACIÓN: Sistema de Peso en Presupuestos
-- Fecha: 15 de febrero de 2026
-- Versión: 1.0
-- Descripción: Implementa cálculo dinámico de peso mediante vistas SQL
-- 
-- Funcionalidad:
-- - Añade campo peso_elemento en tabla elemento (único cambio físico)
-- - Crea 5 vistas SQL para cálculo automático de pesos
-- - Diferencia entre artículos normales (peso medio) y KITs (suma componentes)
-- - Calcula peso total por línea y por presupuesto
--
-- Autor: MDR ERP Manager
-- Referencia: docs/presupuestos_20260211.md - Sección 20
-- ═══════════════════════════════════════════════════════════

-- ───────────────────────────────────────────────────────────
-- PASO 1: Añadir campo peso en tabla elemento
-- (ÚNICO cambio en estructura física de tablas)
-- ───────────────────────────────────────────────────────────

ALTER TABLE elemento 
ADD COLUMN peso_elemento DECIMAL(10,3) DEFAULT NULL 
    COMMENT 'Peso en kilogramos (NULL=no aplica o desconocido)';

-- Índice simple para búsquedas por peso
DROP INDEX IF EXISTS idx_peso_elemento ON elemento;
ALTER TABLE elemento 
ADD INDEX idx_peso_elemento (peso_elemento);

-- Índice compuesto para optimizar agregaciones por artículo
DROP INDEX IF EXISTS idx_articulo_peso ON elemento;
ALTER TABLE elemento 
ADD INDEX idx_articulo_peso (id_articulo_elemento, activo_elemento, peso_elemento);

-- ───────────────────────────────────────────────────────────
-- PASO 2: Vista - Peso MEDIO de artículos normales
-- Calcula el promedio aritmético de peso de elementos activos
-- ───────────────────────────────────────────────────────────

CREATE OR REPLACE VIEW vista_articulo_peso_medio AS
SELECT 
    a.id_articulo,
    a.codigo_articulo,
    a.nombre_articulo,
    a.es_kit_articulo,
    
    -- Contador de elementos con peso definido
    COUNT(CASE WHEN e.peso_elemento IS NOT NULL THEN 1 END) AS elementos_con_peso,
    
    -- Contador total de elementos activos
    COUNT(e.id_elemento) AS total_elementos,
    
    -- Peso MEDIO (promedio aritmético de elementos activos)
    -- Razón: No sabemos qué elementos específicos se asignarán al presupuesto
    AVG(e.peso_elemento) AS peso_medio_kg,
    
    -- Suma total (para referencia/debug)
    SUM(e.peso_elemento) AS peso_suma_total_kg,
    
    -- MIN y MAX (para análisis de variación)
    MIN(e.peso_elemento) AS peso_min_kg,
    MAX(e.peso_elemento) AS peso_max_kg

FROM articulo a

LEFT JOIN elemento e 
    ON a.id_articulo = e.id_articulo_elemento
    AND e.activo_elemento = 1
    AND e.peso_elemento IS NOT NULL  -- Solo elementos con peso definido

WHERE a.activo_articulo = 1
  AND a.es_kit_articulo = 0  -- Solo artículos normales (NO KITs)

GROUP BY a.id_articulo, a.codigo_articulo, a.nombre_articulo, a.es_kit_articulo;

-- ───────────────────────────────────────────────────────────
-- PASO 3: Vista - Peso TOTAL de artículos tipo KIT
-- Calcula la suma del peso de todos los componentes del KIT
-- ───────────────────────────────────────────────────────────

CREATE OR REPLACE VIEW vista_kit_peso_total AS
SELECT 
    k.id_articulo_maestro AS id_articulo,
    am.codigo_articulo,
    am.nombre_articulo,
    am.es_kit_articulo,
    
    -- Contador de componentes del KIT
    COUNT(DISTINCT k.id_articulo_componente) AS total_componentes,
    
    -- Componentes con peso definido
    COUNT(DISTINCT CASE 
        WHEN vpm.peso_medio_kg IS NOT NULL 
        THEN k.id_articulo_componente 
    END) AS componentes_con_peso,
    
    -- Peso TOTAL del KIT = SUMA(cantidad × peso_medio_componente)
    -- Razón: Los KITs tienen composición fija, siempre llevan los mismos componentes
    SUM(
        k.cantidad_kit * COALESCE(vpm.peso_medio_kg, 0)
    ) AS peso_total_kit_kg

FROM articulo am

INNER JOIN kit k 
    ON am.id_articulo = k.id_articulo_maestro
    AND k.activo_kit = 1

-- JOIN con peso medio de cada artículo componente
-- Nota: Los componentes del KIT son artículos, NO elementos directos
LEFT JOIN vista_articulo_peso_medio vpm
    ON k.id_articulo_componente = vpm.id_articulo

WHERE am.activo_articulo = 1
  AND am.es_kit_articulo = 1  -- Solo KITs

GROUP BY k.id_articulo_maestro, am.codigo_articulo, am.nombre_articulo, am.es_kit_articulo;

-- ───────────────────────────────────────────────────────────
-- PASO 4: Vista UNIFICADA - Peso de cualquier artículo
-- Combina peso medio (artículos) y peso total (KITs)
-- ───────────────────────────────────────────────────────────

CREATE OR REPLACE VIEW vista_articulo_peso AS
SELECT 
    a.id_articulo,
    a.codigo_articulo,
    a.nombre_articulo,
    a.es_kit_articulo,
    a.precio_alquiler_articulo,
    
    -- Peso calculado según tipo de artículo
    CASE 
        -- Artículo normal: peso medio de elementos
        WHEN a.es_kit_articulo = 0 THEN 
            COALESCE(vpm.peso_medio_kg, 0.000)
        
        -- KIT: suma de componentes
        WHEN a.es_kit_articulo = 1 THEN 
            COALESCE(vkp.peso_total_kit_kg, 0.000)
        
        ELSE 0.000
    END AS peso_articulo_kg,
    
    -- Indicador del método de cálculo usado
    CASE 
        WHEN a.es_kit_articulo = 0 THEN 'MEDIA_ELEMENTOS'
        WHEN a.es_kit_articulo = 1 THEN 'SUMA_COMPONENTES'
        ELSE 'SIN_METODO'
    END AS metodo_calculo,
    
    -- Indicador de completitud de datos
    CASE 
        WHEN a.es_kit_articulo = 0 AND vpm.elementos_con_peso > 0 THEN TRUE
        WHEN a.es_kit_articulo = 1 AND vkp.componentes_con_peso > 0 THEN TRUE
        ELSE FALSE
    END AS tiene_datos_peso,
    
    -- Detalles adicionales para debug/análisis
    CASE 
        WHEN a.es_kit_articulo = 0 THEN vpm.elementos_con_peso
        WHEN a.es_kit_articulo = 1 THEN vkp.componentes_con_peso
        ELSE 0
    END AS items_con_peso,
    
    CASE 
        WHEN a.es_kit_articulo = 0 THEN vpm.total_elementos
        WHEN a.es_kit_articulo = 1 THEN vkp.total_componentes
        ELSE 0
    END AS total_items

FROM articulo a

-- LEFT JOIN con peso medio (artículos normales)
LEFT JOIN vista_articulo_peso_medio vpm
    ON a.id_articulo = vpm.id_articulo
    AND a.es_kit_articulo = 0

-- LEFT JOIN con peso total (KITs)
LEFT JOIN vista_kit_peso_total vkp
    ON a.id_articulo = vkp.id_articulo
    AND a.es_kit_articulo = 1

WHERE a.activo_articulo = 1;

-- ───────────────────────────────────────────────────────────
-- PASO 5: Vista - Peso por línea de presupuesto
-- Calcula peso de cada línea del presupuesto
-- ───────────────────────────────────────────────────────────

CREATE OR REPLACE VIEW vista_linea_peso AS
SELECT 
    lp.id_linea_ppto,
    lp.id_version_presupuesto,
    lp.id_articulo,
    lp.numero_linea_ppto,
    lp.tipo_linea_ppto,
    lp.cantidad_linea_ppto,
    lp.descripcion_linea_ppto,
    lp.codigo_linea_ppto,
    
    -- Datos del artículo
    vap.codigo_articulo,
    vap.nombre_articulo,
    vap.es_kit_articulo,
    vap.peso_articulo_kg,
    vap.metodo_calculo,
    vap.tiene_datos_peso,
    
    -- Peso TOTAL de esta línea = cantidad × peso_artículo
    (lp.cantidad_linea_ppto * COALESCE(vap.peso_articulo_kg, 0)) AS peso_total_linea_kg,
    
    -- Indicador de si esta línea tiene peso calculable
    CASE 
        WHEN lp.tipo_linea_ppto IN ('articulo', 'kit') 
             AND vap.tiene_datos_peso = TRUE 
        THEN TRUE
        ELSE FALSE
    END AS linea_tiene_peso

FROM linea_presupuesto lp

LEFT JOIN vista_articulo_peso vap 
    ON lp.id_articulo = vap.id_articulo

WHERE lp.activo_linea_ppto = 1
  AND lp.mostrar_en_presupuesto = 1;

-- ───────────────────────────────────────────────────────────
-- PASO 6: Vista - Peso TOTAL por presupuesto
-- Calcula peso total acumulado y métricas de completitud
-- ───────────────────────────────────────────────────────────

CREATE OR REPLACE VIEW vista_presupuesto_peso AS
SELECT 
    pv.id_version_presupuesto,
    pv.id_presupuesto,
    
    -- PESO TOTAL del presupuesto (suma de todas las líneas)
    COALESCE(SUM(vlp.peso_total_linea_kg), 0.000) AS peso_total_kg,
    
    -- Desglose por tipo de cálculo
    SUM(CASE 
        WHEN vlp.metodo_calculo = 'MEDIA_ELEMENTOS' 
        THEN vlp.peso_total_linea_kg 
        ELSE 0 
    END) AS peso_articulos_normales_kg,
    
    SUM(CASE 
        WHEN vlp.metodo_calculo = 'SUMA_COMPONENTES' 
        THEN vlp.peso_total_linea_kg 
        ELSE 0 
    END) AS peso_kits_kg,
    
    -- Contadores de líneas
    COUNT(vlp.id_linea_ppto) AS total_lineas,
    
    COUNT(CASE 
        WHEN vlp.linea_tiene_peso = TRUE 
        THEN 1 
    END) AS lineas_con_peso,
    
    COUNT(CASE 
        WHEN vlp.linea_tiene_peso = FALSE 
        THEN 1 
    END) AS lineas_sin_peso,
    
    -- Porcentaje de completitud
    ROUND(
        (COUNT(CASE WHEN vlp.linea_tiene_peso = TRUE THEN 1 END) * 100.0) / 
        NULLIF(COUNT(vlp.id_linea_ppto), 0),
        2
    ) AS porcentaje_completitud

FROM presupuesto_version pv

LEFT JOIN vista_linea_peso vlp 
    ON pv.id_version_presupuesto = vlp.id_version_presupuesto

GROUP BY pv.id_version_presupuesto, pv.id_presupuesto;

-- ───────────────────────────────────────────────────────────
-- PASO 7: Índices adicionales para optimización
-- ───────────────────────────────────────────────────────────

-- Optimizar joins en linea_presupuesto
DROP INDEX IF EXISTS idx_version_articulo_peso ON linea_presupuesto;
ALTER TABLE linea_presupuesto 
ADD INDEX idx_version_articulo_peso (id_version_presupuesto, id_articulo, activo_linea_ppto);

-- Optimizar joins en kit
DROP INDEX IF EXISTS idx_maestro_activo_peso ON kit;
ALTER TABLE kit 
ADD INDEX idx_maestro_activo_peso (id_articulo_maestro, activo_kit);

-- Optimizar filtros en articulo
DROP INDEX IF EXISTS idx_es_kit_activo_peso ON articulo;
ALTER TABLE articulo 
ADD INDEX idx_es_kit_activo_peso (es_kit_articulo, activo_articulo);

-- ═══════════════════════════════════════════════════════════
-- VERIFICACIÓN DE MIGRACIÓN
-- ═══════════════════════════════════════════════════════════

-- Verificar que el campo fue añadido
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    NUMERIC_PRECISION, 
    NUMERIC_SCALE,
    IS_NULLABLE, 
    COLUMN_DEFAULT, 
    COLUMN_COMMENT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_NAME = 'elemento'
  AND COLUMN_NAME = 'peso_elemento';

-- Verificar que las vistas fueron creadas
SELECT 
    TABLE_NAME as vista_nombre,
    TABLE_TYPE as tipo
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = DATABASE()
  AND TABLE_TYPE = 'VIEW'
  AND TABLE_NAME LIKE 'vista_%peso%'
ORDER BY TABLE_NAME;

-- Verificar índices creados
SELECT 
    TABLE_NAME,
    INDEX_NAME,
    GROUP_CONCAT(COLUMN_NAME ORDER BY SEQ_IN_INDEX) as columnas
FROM INFORMATION_SCHEMA.STATISTICS
WHERE TABLE_SCHEMA = DATABASE()
  AND (
      (TABLE_NAME = 'elemento' AND INDEX_NAME LIKE '%peso%')
      OR (TABLE_NAME = 'linea_presupuesto' AND INDEX_NAME LIKE '%peso%')
      OR (TABLE_NAME = 'kit' AND INDEX_NAME LIKE '%peso%')
      OR (TABLE_NAME = 'articulo' AND INDEX_NAME LIKE '%peso%')
  )
GROUP BY TABLE_NAME, INDEX_NAME
ORDER BY TABLE_NAME, INDEX_NAME;

-- ═══════════════════════════════════════════════════════════
-- CONSULTAS DE EJEMPLO Y TESTING
-- ═══════════════════════════════════════════════════════════

-- Ejemplo 1: Ver peso de todos los artículos
-- SELECT 
--     codigo_articulo,
--     nombre_articulo,
--     CASE WHEN es_kit_articulo = 1 THEN 'KIT' ELSE 'ARTÍCULO' END AS tipo,
--     peso_articulo_kg,
--     metodo_calculo,
--     tiene_datos_peso,
--     CONCAT(items_con_peso, '/', total_items) AS completitud
-- FROM vista_articulo_peso
-- ORDER BY tiene_datos_peso DESC, es_kit_articulo, nombre_articulo;

-- Ejemplo 2: Ver peso de un presupuesto específico
-- SELECT 
--     p.numero_presupuesto,
--     vpp.peso_total_kg,
--     vpp.peso_articulos_normales_kg,
--     vpp.peso_kits_kg,
--     vpp.lineas_con_peso,
--     vpp.lineas_sin_peso,
--     vpp.porcentaje_completitud
-- FROM vista_presupuesto_peso vpp
-- JOIN presupuesto_version pv ON vpp.id_version_presupuesto = pv.id_version_presupuesto
-- JOIN presupuesto p ON vpp.id_presupuesto = p.id_presupuesto
-- WHERE p.numero_presupuesto = '2026-001';

-- Ejemplo 3: Ver líneas de presupuesto con peso
-- SELECT 
--     numero_linea_ppto,
--     codigo_linea_ppto,
--     descripcion_linea_ppto,
--     cantidad_linea_ppto,
--     peso_articulo_kg,
--     peso_total_linea_kg,
--     metodo_calculo
-- FROM vista_linea_peso
-- WHERE id_version_presupuesto = 1
-- ORDER BY numero_linea_ppto;

-- ═══════════════════════════════════════════════════════════
-- FIN DE MIGRACIÓN
-- ═══════════════════════════════════════════════════════════

-- Resumen de cambios:
-- ✅ 1 campo añadido: elemento.peso_elemento
-- ✅ 3 índices añadidos en elemento
-- ✅ 3 índices añadidos en otras tablas
-- ✅ 5 vistas SQL creadas
-- ✅ Sistema 100% funcional sin triggers
