-- =====================================================
-- SCRIPT: Actualizar vista_kit_completa
-- FECHA: 2026-01-03
-- PROPÓSITO: Eliminar filtro WHERE activo_kit = 1 
--           para mostrar todos los registros
-- =====================================================

-- 1. Eliminar la vista existente
DROP VIEW IF EXISTS vista_kit_completa;

-- 2. Crear la vista SIN filtro de activo_kit
CREATE VIEW vista_kit_completa AS
SELECT 
    -- DATOS DE LA TABLA KIT
    k.id_kit,
    k.cantidad_kit,
    k.activo_kit,
    k.created_at_kit,
    k.updated_at_kit,
    
    -- ARTÍCULO MAESTRO (EL KIT)
    k.id_articulo_maestro,
    am.codigo_articulo AS codigo_articulo_maestro,
    am.nombre_articulo AS nombre_articulo_maestro,
    am.name_articulo AS name_articulo_maestro,
    am.precio_alquiler_articulo AS precio_articulo_maestro,
    am.es_kit_articulo AS es_kit_articulo_maestro,
    am.activo_articulo AS activo_articulo_maestro,
    
    -- ARTÍCULO COMPONENTE
    k.id_articulo_componente,
    ac.codigo_articulo AS codigo_articulo_componente,
    ac.nombre_articulo AS nombre_articulo_componente,
    ac.name_articulo AS name_articulo_componente,
    ac.precio_alquiler_articulo AS precio_articulo_componente,
    ac.es_kit_articulo AS es_kit_articulo_componente,
    ac.activo_articulo AS activo_articulo_componente,
    
    -- CAMPOS CALCULADOS
    (k.cantidad_kit * ac.precio_alquiler_articulo) AS subtotal_componente,
    
    -- SUBCONSULTA: Total componentes del kit maestro
    (SELECT COUNT(*) 
     FROM kit k2 
     WHERE k2.id_articulo_maestro = k.id_articulo_maestro 
       AND k2.activo_kit = 1) AS total_componentes_kit,
    
    -- SUBCONSULTA: Precio total del kit (suma de componentes)
    (SELECT SUM(k2.cantidad_kit * a2.precio_alquiler_articulo)
     FROM kit k2
     INNER JOIN articulo a2 ON k2.id_articulo_componente = a2.id_articulo
     WHERE k2.id_articulo_maestro = k.id_articulo_maestro
       AND k2.activo_kit = 1
       AND a2.activo_articulo = 1) AS precio_total_kit

FROM kit k
INNER JOIN articulo am ON k.id_articulo_maestro = am.id_articulo
INNER JOIN articulo ac ON k.id_articulo_componente = ac.id_articulo;

-- 3. Verificar que la vista se creó correctamente
SELECT 'Vista creada correctamente' AS status;

-- 4. Probar la vista - mostrar todos los registros
SELECT 
    id_kit,
    codigo_articulo_componente,
    nombre_articulo_componente,
    cantidad_kit,
    activo_kit,
    CASE 
        WHEN activo_kit = 1 THEN 'ACTIVO'
        ELSE 'INACTIVO'
    END AS estado
FROM vista_kit_completa
ORDER BY codigo_articulo_maestro, codigo_articulo_componente;
