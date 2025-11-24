-- ====================================================
-- CREAR TABLA contacto_cliente
-- Para el sistema de gestión de contactos de clientes
-- Tabla principal: cliente (ya existe)
-- Tabla dependiente: contacto_cliente (a crear)
-- ====================================================

CREATE TABLE contacto_cliente (
    id_contacto_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_cliente INT UNSIGNED NOT NULL COMMENT 'FK hacia tabla cliente',
    nombre_contacto_cliente VARCHAR(100) NOT NULL COMMENT 'Nombre del contacto',
    apellidos_contacto_cliente VARCHAR(150) COMMENT 'Apellidos del contacto',
    cargo_contacto_cliente VARCHAR(100) COMMENT 'Cargo o puesto del contacto',
    departamento_contacto_cliente VARCHAR(100) COMMENT 'Departamento donde trabaja',
    telefono_contacto_cliente VARCHAR(20) COMMENT 'Teléfono fijo',
    movil_contacto_cliente VARCHAR(20) COMMENT 'Teléfono móvil',
    email_contacto_cliente VARCHAR(100) COMMENT 'Email del contacto',
    extension_contacto_cliente VARCHAR(10) COMMENT 'Extensión telefónica',
    principal_contacto_cliente TINYINT DEFAULT 0 COMMENT 'Es contacto principal (1=sí, 0=no)',
    observaciones_contacto_cliente TEXT COMMENT 'Observaciones adicionales',
    activo_contacto_cliente TINYINT DEFAULT 1 COMMENT 'Contacto activo (1=activo, 0=inactivo)',
    created_at_contacto_cliente DATETIME DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
    updated_at_contacto_cliente DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de actualización',
    
    -- Clave foránea
    CONSTRAINT fk_contacto_cliente_id_cliente 
        FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) 
        ON DELETE CASCADE ON UPDATE CASCADE,
    
    -- Índices para optimización
    INDEX idx_contacto_cliente_id_cliente (id_cliente),
    INDEX idx_contacto_cliente_nombre (nombre_contacto_cliente),
    INDEX idx_contacto_cliente_email (email_contacto_cliente),
    INDEX idx_contacto_cliente_activo (activo_contacto_cliente)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Contactos de clientes - Sistema MDR';

-- ====================================================
-- INSERTAR DATOS DE PRUEBA (OPCIONAL)
-- ====================================================

-- Descomenta estas líneas para insertar datos de ejemplo
-- (ajusta los id_cliente según los clientes existentes en tu BD)

/*
INSERT INTO contacto_cliente 
(id_cliente, nombre_contacto_cliente, apellidos_contacto_cliente, cargo_contacto_cliente, telefono_contacto_cliente, email_contacto_cliente, principal_contacto_cliente) 
VALUES 
(1, 'María', 'García López', 'Gerente General', '961234567', 'maria.garcia@cliente1.com', 1),
(1, 'Juan', 'Martínez Ruiz', 'Responsable Compras', '961234568', 'juan.martinez@cliente1.com', 0),
(2, 'Ana', 'Fernández Soto', 'Directora', '963456789', 'ana.fernandez@cliente2.es', 1);
*/