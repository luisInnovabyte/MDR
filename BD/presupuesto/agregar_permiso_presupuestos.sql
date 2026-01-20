-- ========================================================
-- AGREGAR PERMISO PARA MÓDULO DE PRESUPUESTOS
-- ========================================================
-- Fecha: 2025-01-23
-- Propósito: Otorgar acceso al módulo 'presupuestos' 
--           para el usuario administrador
-- ========================================================

-- 1️⃣ Verificar permisos actuales del usuario root/admin
SELECT 
    u.id_usuario,
    u.nombre_usuario,
    u.email_usuario,
    p.id_permiso,
    p.nombre_modulo,
    p.puede_ver,
    p.puede_crear,
    p.puede_editar,
    p.puede_eliminar
FROM usuario u
LEFT JOIN permisos p ON u.id_usuario = p.id_usuario
WHERE u.nombre_usuario = 'root' 
   OR u.email_usuario LIKE '%admin%'
ORDER BY u.id_usuario, p.nombre_modulo;

-- 2️⃣ Buscar si ya existe permiso para 'presupuestos'
SELECT * FROM permisos 
WHERE nombre_modulo = 'presupuestos';

-- 3️⃣ Insertar permiso completo para presupuestos (usuario ID 1 = admin)
-- NOTA: Ajusta el id_usuario si tu administrador tiene otro ID
INSERT INTO permisos (
    id_usuario,
    nombre_modulo,
    puede_ver,
    puede_crear,
    puede_editar,
    puede_eliminar
) VALUES (
    1,                    -- ID del usuario administrador
    'presupuestos',      -- Nombre del módulo
    1,                   -- Puede ver
    1,                   -- Puede crear
    1,                   -- Puede editar
    1                    -- Puede eliminar
)
ON DUPLICATE KEY UPDATE
    puede_ver = 1,
    puede_crear = 1,
    puede_editar = 1,
    puede_eliminar = 1;

-- 4️⃣ Verificar que se insertó correctamente
SELECT 
    p.*,
    u.nombre_usuario
FROM permisos p
INNER JOIN usuario u ON p.id_usuario = u.id_usuario
WHERE p.nombre_modulo = 'presupuestos';

-- 5️⃣ Ver todos los módulos del usuario admin
SELECT 
    nombre_modulo,
    puede_ver,
    puede_crear,
    puede_editar,
    puede_eliminar
FROM permisos
WHERE id_usuario = 1
ORDER BY nombre_modulo;

-- ========================================================
-- NOTA: Si el sistema usa un id_usuario diferente para
-- el administrador, ejecuta primero la consulta 1️⃣
-- para identificar el ID correcto y modifica el INSERT
-- ========================================================
