-- Script para insertar grupos de artículo de ejemplo
-- Para una empresa de alquiler de equipos audiovisuales (MDR)

-- Primero verificar si la tabla existe
SELECT 'Insertando grupos de artículo de ejemplo...' AS mensaje;

-- Insertar grupos de artículo
INSERT INTO grupo_articulo (codigo_grupo, nombre_grupo, descripcion_grupo, observaciones_grupo, activo_grupo) VALUES
('AUD', 'Audio', 'Equipos de sonido, megafonía y amplificación', 'Incluye micrófonos, altavoces, consolas de mezcla, etc.', TRUE),
('ILU', 'Iluminación', 'Equipos de iluminación escénica y arquitectónica', 'Incluye moving heads, PAR LED, focos, etc.', TRUE),
('VID', 'Vídeo', 'Equipos de proyección, pantallas y cámaras', 'Incluye proyectores, pantallas LED, cámaras, etc.', TRUE),
('EST', 'Estructuras', 'Truss, torres, escenarios y rigging', 'Incluye estructura de aluminio, torres de elevación, etc.', TRUE),
('COM', 'Comunicaciones', 'Intercomunicadores y sistemas de coordinación', 'Incluye walkie-talkies, intercom, etc.', TRUE),
('ELE', 'Eléctrico', 'Distribución eléctrica y cableado', 'Incluye racks de distribución, cables de potencia, etc.', TRUE),
('ACC', 'Accesorios', 'Cables, conectores, adaptadores y consumibles', 'Incluye cables de audio, conectores XLR, adaptadores, etc.', TRUE),
('MOB', 'Mobiliario', 'Sillas, mesas, vallas y elementos de evento', 'Incluye mesas, sillas, vallas, alfombras, etc.', TRUE);

-- Verificar los datos insertados
SELECT 'Grupos de artículo insertados correctamente' AS mensaje;

-- Mostrar los grupos insertados
SELECT * FROM grupo_articulo ORDER BY codigo_grupo;
