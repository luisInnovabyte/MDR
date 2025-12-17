-- ============================================
-- Tabla: tipo_documento
-- Descripción: Catálogo de tipos de documentos para el gestor documental de técnicos
-- Fecha creación: 2024-12-17
-- Autor: MDR ERP Manager
-- ============================================

CREATE TABLE IF NOT EXISTS tipo_documento (
    -- ----------------------------------------
    -- Identificador único
    -- ----------------------------------------
    id_tipo_documento INT NOT NULL AUTO_INCREMENT,
    
    -- ----------------------------------------
    -- Campos específicos
    -- ----------------------------------------
    codigo_tipo_documento VARCHAR(20) NOT NULL
        COMMENT 'Código alfanumérico único del tipo (ej: SEG, MAN, PROC)',
    
    nombre_tipo_documento VARCHAR(100) NOT NULL
        COMMENT 'Nombre descriptivo del tipo de documento',
    
    descripcion_tipo_documento TEXT NULL
        COMMENT 'Descripción detallada del tipo de documento',
    
    -- ----------------------------------------
    -- Campos de control (obligatorios)
    -- ----------------------------------------
    activo_tipo_documento TINYINT(1) NOT NULL DEFAULT 1
        COMMENT 'Estado del registro: 1=activo, 0=inactivo',
    
    created_at_tipo_documento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Fecha y hora de creación del registro',
    
    updated_at_tipo_documento TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        COMMENT 'Fecha y hora de última modificación',
    
    -- ----------------------------------------
    -- Índices
    -- ----------------------------------------
    PRIMARY KEY (id_tipo_documento),
    UNIQUE KEY uk_codigo_tipo_documento (codigo_tipo_documento),
    KEY idx_nombre_tipo_documento (nombre_tipo_documento)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Catálogo de tipos de documentos para el gestor documental de técnicos';

-- ============================================
-- Insertar datos de ejemplo (opcional)
-- ============================================

INSERT INTO tipo_documento (codigo_tipo_documento, nombre_tipo_documento, descripcion_tipo_documento) VALUES
('SEG', 'Seguro', 'Documentos de seguros y pólizas'),
('MAN', 'Manual', 'Manuales de usuario y técnicos'),
('PROC', 'Procedimiento', 'Procedimientos de operación'),
('CERT', 'Certificado', 'Certificados y acreditaciones'),
('LIC', 'Licencia', 'Licencias de software y permisos'),
('CONT', 'Contrato', 'Contratos y acuerdos'),
('FACT', 'Factura', 'Facturas y documentos contables'),
('PLANO', 'Plano Técnico', 'Planos y esquemas técnicos')
ON DUPLICATE KEY UPDATE 
    nombre_tipo_documento = VALUES(nombre_tipo_documento),
    descripcion_tipo_documento = VALUES(descripcion_tipo_documento);
