-- ============================================================
-- Migración: Vista para Informe de Rotación de Inventario
-- Fecha: 2026-03-17
-- Informe: Informe_rotacion
-- ============================================================
--
-- Propósito: Muestra cada artículo activo con el número de
--            veces que ha aparecido en presupuestos aprobados
--            y la última fecha de uso, para identificar
--            artículos de alta/baja rotación.
--
-- Tablas involucradas:
--   articulo            → catálogo de artículos
--   familia             → familia del artículo
--   linea_presupuesto   → apariciones en presupuestos (LEFT JOIN)
--   presupuesto         → para filtrar estados ganados y fecha
--   estado_presupuesto  → solo ACEP y FACT
--
-- NOTA: Se usa LEFT JOIN desde articulo → linea_presupuesto
--       para que los artículos NUNCA USADOS también aparezcan
--       con total_usos = 0 y ultimo_uso = NULL.
-- ============================================================

CREATE OR REPLACE VIEW vista_rotacion_inventario AS
SELECT
    -- Artículo
    a.id_articulo,
    a.codigo_articulo,
    a.nombre_articulo,
    a.precio_alquiler_articulo,
    a.activo_articulo,

    -- Familia
    COALESCE(f.id_familia, 0)                     AS id_familia,
    COALESCE(f.nombre_familia, 'Sin familia')     AS nombre_familia,
    COALESCE(f.codigo_familia, '--')              AS codigo_familia,

    -- Métricas de rotación (agrupadas por artículo)
    COUNT(DISTINCT p.id_presupuesto)              AS total_usos,
    MAX(p.fecha_presupuesto)                      AS ultimo_uso,
    DATEDIFF(CURDATE(), MAX(p.fecha_presupuesto)) AS dias_desde_ultimo_uso,

    -- Cantidad total de unidades alquiladas (suma de todas las líneas)
    COALESCE(SUM(lp.cantidad_linea_ppto), 0)      AS total_unidades_alquiladas

FROM articulo a

LEFT JOIN familia f
    ON a.id_familia = f.id_familia

LEFT JOIN linea_presupuesto lp
    ON a.id_articulo = lp.id_articulo
    AND lp.activo_linea_ppto = 1
    AND lp.tipo_linea_ppto = 'articulo'

LEFT JOIN presupuesto_version pv
    ON lp.id_version_presupuesto = pv.id_version_presupuesto

LEFT JOIN presupuesto p
    ON pv.id_presupuesto = p.id_presupuesto
    AND p.activo_presupuesto = 1
    AND pv.numero_version_presupuesto = p.version_actual_presupuesto

LEFT JOIN estado_presupuesto ep
    ON p.id_estado_ppto = ep.id_estado_ppto
    AND ep.codigo_estado_ppto = 'APROB'

WHERE
    a.activo_articulo = 1

GROUP BY
    a.id_articulo,
    a.codigo_articulo,
    a.nombre_articulo,
    a.precio_alquiler_articulo,
    a.activo_articulo,
    f.id_familia,
    f.nombre_familia,
    f.codigo_familia

ORDER BY total_usos DESC, a.nombre_articulo ASC;

-- ============================================================
-- Verificación rápida tras aplicar:
--
-- TOP 10 más usados:
--   SELECT nombre_articulo, total_usos, ultimo_uso
--   FROM vista_rotacion_inventario
--   ORDER BY total_usos DESC LIMIT 10;
--
-- Artículos nunca usados:
--   SELECT nombre_articulo, nombre_familia
--   FROM vista_rotacion_inventario
--   WHERE total_usos = 0
--   ORDER BY nombre_familia, nombre_articulo;
-- ============================================================
