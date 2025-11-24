-- ================================================================
-- Tabla proveedor (singular) para el módulo MVC de Proveedores
-- Estructura compatible con el modelo Proveedores.php
-- ================================================================

CREATE TABLE IF NOT EXISTS proveedor (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Información básica
    codigo_proveedor VARCHAR(20) NOT NULL UNIQUE,
    nombre_proveedor VARCHAR(255) NOT NULL,
    
    -- Dirección principal
    direccion_proveedor VARCHAR(255),
    cp_proveedor VARCHAR(10),
    poblacion_proveedor VARCHAR(100),
    provincia_proveedor VARCHAR(100),
    
    -- Datos fiscales y contacto
    nif_proveedor VARCHAR(20),
    telefono_proveedor VARCHAR(255),
    fax_proveedor VARCHAR(50),
    web_proveedor VARCHAR(255),
    email_proveedor VARCHAR(255),
    persona_contacto_proveedor VARCHAR(255),
    
    -- Dirección S.A.T. (Servicio de Atención Técnica)
    direccion_sat_proveedor VARCHAR(255),
    cp_sat_proveedor VARCHAR(10),
    poblacion_sat_proveedor VARCHAR(100),
    provincia_sat_proveedor VARCHAR(100),
    telefono_sat_proveedor VARCHAR(255),
    fax_sat_proveedor VARCHAR(50),
    email_sat_proveedor VARCHAR(255),
    
    -- Observaciones
    observaciones_proveedor TEXT,
    
    -- Control de estado
    activo_proveedor BOOLEAN DEFAULT TRUE,
    
    -- Campos de auditoría
    created_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices para optimizar consultas
    INDEX idx_codigo_proveedor (codigo_proveedor),
    INDEX idx_nombre_proveedor (nombre_proveedor),
    INDEX idx_nif_proveedor (nif_proveedor),
    INDEX idx_activo_proveedor (activo_proveedor)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tabla de proveedores para el módulo MVC';

-- Insertar algunos proveedores de ejemplo para probar
INSERT INTO proveedor (
    codigo_proveedor, 
    nombre_proveedor, 
    direccion_proveedor, 
    cp_proveedor, 
    poblacion_proveedor, 
    provincia_proveedor, 
    nif_proveedor, 
    telefono_proveedor, 
    email_proveedor, 
    persona_contacto_proveedor
) VALUES 
('PROV001', 'Toldos Mediterráneo S.L.', 'Calle Industrial 15', '03008', 'Alicante', 'Alicante', 'B12345678', '965123456', 'info@toldosmediterraneo.com', 'Juan García'),
('PROV002', 'Textiles Levante S.A.', 'Polígono Industrial Norte 8', '46015', 'Valencia', 'Valencia', 'A87654321', '963987654', 'comercial@textileslevante.com', 'María Fernández'),
('PROV003', 'Herrajes del Sur S.L.', 'Avenida de la Industria 42', '41015', 'Sevilla', 'Sevilla', 'B45678912', '954321987', 'pedidos@herrajesdelsur.es', 'Carlos Ruiz');