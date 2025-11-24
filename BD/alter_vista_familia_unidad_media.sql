-- Script para actualizar la vista familia_unidad_media
-- Incluye el nuevo campo coeficiente_familia, id_grupo y datos del grupo_articulo

-- Eliminar la vista existente si existe
DROP VIEW IF EXISTS familia_unidad_media;

-- Crear la vista actualizada con los campos coeficiente_familia, id_grupo y datos de grupo_articulo
CREATE VIEW familia_unidad_media AS
SELECT 
    f.id_familia,
    f.id_grupo,
    f.codigo_familia,
    f.nombre_familia,
    f.name_familia,
    f.descr_familia,
    f.imagen_familia,
    f.activo_familia,
    f.coeficiente_familia,
    f.created_at_familia,
    f.updated_at_familia,
    f.id_unidad_familia,
    u.nombre_unidad,
    u.descr_unidad,
    u.simbolo_unidad,
    u.activo_unidad,
    g.codigo_grupo,
    g.nombre_grupo,
    g.descripcion_grupo
FROM familia f
LEFT JOIN unidad_medida u ON f.id_unidad_familia = u.id_unidad
LEFT JOIN grupo_articulo g ON f.id_grupo = g.id_grupo;

-- Verificar que la vista se creó correctamente
SELECT 'Vista familia_unidad_media actualizada correctamente' AS mensaje;

-- Mostrar estructura de la vista
DESCRIBE familia_unidad_media;