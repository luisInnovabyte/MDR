-- =====================================================
-- SCRIPT: Crear Rol Técnico para MDR ERP Manager
-- Fecha: 20 de diciembre de 2025
-- Descripción: Añade el rol "Técnico" (ID 5) al sistema
-- =====================================================

-- Verificar si el rol ya existe antes de insertar
INSERT INTO roles (id_rol, nombre_rol, est)
SELECT 5, 'Técnico', 1
WHERE NOT EXISTS (
    SELECT 1 FROM roles WHERE id_rol = 5
);

-- Verificar el resultado
SELECT * FROM roles WHERE id_rol = 5;

-- =====================================================
-- DESCRIPCIÓN DEL ROL TÉCNICO
-- =====================================================
-- El rol Técnico (ID 5) tendrá acceso a:
-- - Consulta de Elementos (solo lectura)
-- - Gestión de Documentos de Elementos
-- - Gestión de Fotos de Elementos
-- - Estados de Elementos
-- - Consulta de Garantías (solo lectura)
-- - Consulta de Mantenimientos (solo lectura)
-- - Informes técnicos (garantías, mantenimientos)
-- - Gestor Documental de Técnicos
-- =====================================================
