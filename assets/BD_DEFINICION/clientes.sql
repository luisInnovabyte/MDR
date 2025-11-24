CREATE TABLE clientes (
    -- Clave primaria
    id_cli INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Datos principales del cliente
    cod_cli VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código del cliente',
    nombre_cli VARCHAR(255) NOT NULL COMMENT 'Nombre o razón social',
    
    -- Dirección principal
    direc_cli VARCHAR(255) COMMENT 'Dirección completa',
    cp_cli VARCHAR(10) COMMENT 'Código postal',
    poblac_cli VARCHAR(100) COMMENT 'Ciudad o población',
    prov_cli VARCHAR(100) COMMENT 'Provincia',
    
    -- Datos fiscales y de contacto
    nif_cli VARCHAR(20) COMMENT 'NIF/DNI/CIF',
    telf_cli VARCHAR(255) COMMENT 'Números de teléfono',
    fax_cli VARCHAR(50) COMMENT 'Número de fax',
    web_cli VARCHAR(255) COMMENT 'Sitio web',
    email_cli VARCHAR(255) COMMENT 'Correo electrónico',
    
    -- Dirección de facturación
    nom_fac_cli VARCHAR(255) COMMENT 'Nombre para facturación',
    dir_fac_cli VARCHAR(255) COMMENT 'Dirección de facturación',
    cp_fac_cli VARCHAR(10) COMMENT 'CP de facturación',
    pob_fac_cli VARCHAR(100) COMMENT 'Población de facturación',
    prov_fac_cli VARCHAR(100) COMMENT 'Provincia de facturación',
    
    -- Observaciones
    obs_cli TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_cli TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de alta del cliente',
    fmod_cli TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_cli BOOLEAN DEFAULT TRUE COMMENT 'Cliente activo/inactivo',
    
    -- Índices para búsquedas frecuentes
    INDEX idx_cod_cli (cod_cli),
    INDEX idx_nombre_cli (nombre_cli),
    INDEX idx_nif_cli (nif_cli),
    INDEX idx_poblac_cli (poblac_cli),
    INDEX idx_prov_cli (prov_cli)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de clientes y empresas';

-- ========================================================
-- TABLA DE CONTACTOS (relación 1:N con clientes)
-- ========================================================

CREATE TABLE contactos (
    -- Clave primaria
    id_con INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Clave foránea hacia clientes
    id_cli INT NOT NULL COMMENT 'ID del cliente',
    
    -- Datos del contacto
    nombre_con VARCHAR(100) NOT NULL COMMENT 'Nombre del contacto',
    apellidos_con VARCHAR(150) COMMENT 'Apellidos del contacto',
    cargo_con VARCHAR(100) COMMENT 'Cargo o puesto',
    dpto_con VARCHAR(100) COMMENT 'Departamento',
    
    -- Datos de contacto
    telf_con VARCHAR(50) COMMENT 'Teléfono directo',
    movil_con VARCHAR(50) COMMENT 'Teléfono móvil',
    email_con VARCHAR(255) COMMENT 'Email del contacto',
    ext_con VARCHAR(10) COMMENT 'Extensión telefónica',
    
    -- Preferencias
    principal_con BOOLEAN DEFAULT FALSE COMMENT 'Contacto principal',
    obs_con TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_con TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de alta',
    fmod_con TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_con BOOLEAN DEFAULT TRUE COMMENT 'Contacto activo/inactivo',
    
    -- Clave foránea
    CONSTRAINT fk_contacto_cliente FOREIGN KEY (id_cli) 
        REFERENCES clientes(id_cli) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    -- Índices
    INDEX idx_id_cli_con (id_cli),
    INDEX idx_nombre_con (nombre_con),
    INDEX idx_email_con (email_con),
    INDEX idx_principal_con (principal_con)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de personas de contacto de clientes';