-- ============================================
-- Tabla: estado_elemento
-- Descripción: Gestiona los estados de los elementos físicos del inventario
-- Ejemplos: Disponible, Alquilado, En reparación, Dado de baja, etc.
-- Fecha creación: 24-11-2025
-- ============================================

-- Verificar si la tabla existe y eliminarla (solo para desarrollo)
-- DROP TABLE IF EXISTS estado_elemento;

CREATE TABLE IF NOT EXISTS estado_elemento (
    id_estado_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_estado_elemento VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código único del estado (ej: DISP, ALQU, REPA)',
    descripcion_estado_elemento VARCHAR(50) NOT NULL COMMENT 'Descripción del estado',
    color_estado_elemento VARCHAR(7) DEFAULT '#4CAF50' COMMENT 'Color hexadecimal para visualización',
    permite_alquiler_estado_elemento BOOLEAN DEFAULT TRUE COMMENT 'Si TRUE, el elemento puede ser alquilado en este estado',
    observaciones_estado_elemento TEXT COMMENT 'Observaciones adicionales del estado',
    activo_estado_elemento BOOLEAN DEFAULT TRUE COMMENT 'Estado activo/inactivo',
    created_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at_estado_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
    
    INDEX idx_codigo (codigo_estado_elemento),
    INDEX idx_activo (activo_estado_elemento),
    INDEX idx_permite_alquiler (permite_alquiler_estado_elemento)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Estados de elementos físicos del inventario';

-- ============================================
-- Insertar estados de elemento por defecto
-- ============================================

INSERT INTO estado_elemento 
    (codigo_estado_elemento, descripcion_estado_elemento, color_estado_elemento, permite_alquiler_estado_elemento, observaciones_estado_elemento, activo_estado_elemento) 
VALUES
    ('DISP', 'Disponible', '#4CAF50', TRUE, 'Elemento disponible para alquiler', TRUE),
    ('ALQU', 'Alquilado', '#2196F3', FALSE, 'Elemento actualmente alquilado a un cliente', TRUE),
    ('REPA', 'En reparación', '#FF9800', FALSE, 'Elemento en proceso de reparación', TRUE),
    ('BAJA', 'Dado de baja', '#F44336', FALSE, 'Elemento retirado del inventario', TRUE),
    ('TERC', 'De terceros', '#9C27B0', TRUE, 'Elemento propiedad de terceros disponible para alquiler', TRUE),
    ('DEPO', 'En depósito', '#607D8B', FALSE, 'Elemento guardado en depósito, no disponible', TRUE),
    ('MANT', 'Mantenimiento', '#FFC107', FALSE, 'Elemento en proceso de mantenimiento preventivo', TRUE),
    ('TRAN', 'En tránsito', '#00BCD4', FALSE, 'Elemento en proceso de envío o traslado', TRUE);

-- ============================================
-- Consultas útiles para verificación
-- ============================================

-- Ver todos los estados
-- SELECT * FROM estado_elemento ORDER BY descripcion_estado_elemento;

-- Ver solo estados activos que permiten alquiler
-- SELECT * FROM estado_elemento 
-- WHERE activo_estado_elemento = TRUE 
--   AND permite_alquiler_estado_elemento = TRUE
-- ORDER BY descripcion_estado_elemento;

-- Ver estadísticas
-- SELECT 
--     COUNT(*) as total_estados,
--     SUM(CASE WHEN activo_estado_elemento = 1 THEN 1 ELSE 0 END) as estados_activos,
--     SUM(CASE WHEN permite_alquiler_estado_elemento = 1 THEN 1 ELSE 0 END) as permiten_alquiler
-- FROM estado_elemento;
