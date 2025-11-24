-- Agregar columna imagen_familia a la tabla familia
-- Ejecutar este script SQL en la base de datos

ALTER TABLE familia 
ADD COLUMN imagen_familia VARCHAR(255) DEFAULT '' COMMENT 'Nombre del archivo de imagen de la familia'
AFTER descr_familia;

-- Actualizar la estructura de la tabla familia
UPDATE familia SET imagen_familia = '' WHERE imagen_familia IS NULL;