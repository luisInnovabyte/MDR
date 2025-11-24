CREATE TABLE proveedores (
    -- Clave primaria
    id_prv INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Datos principales del proveedor
    cod_prv VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código del proveedor',
    nombre_prv VARCHAR(255) NOT NULL COMMENT 'Nombre o razón social',
    
    -- Dirección principal
    direc_prv VARCHAR(255) COMMENT 'Dirección completa',
    cp_prv VARCHAR(10) COMMENT 'Código postal',
    poblac_prv VARCHAR(100) COMMENT 'Ciudad o población',
    prov_prv VARCHAR(100) COMMENT 'Provincia',
    
    -- Datos fiscales y de contacto
    nif_prv VARCHAR(20) COMMENT 'NIF/DNI/CIF',
    telf_prv VARCHAR(255) COMMENT 'Números de teléfono',
    fax_prv VARCHAR(50) COMMENT 'Número de fax',
    web_prv VARCHAR(255) COMMENT 'Sitio web',
    email_prv VARCHAR(255) COMMENT 'Correo electrónico',
    pers_cont_prv VARCHAR(255) COMMENT 'Persona de contacto',
    
    -- Dirección de S.A.T. (Servicio de Atención Técnica)
    dir_sat_prv VARCHAR(255) COMMENT 'Dirección S.A.T.',
    cp_sat_prv VARCHAR(10) COMMENT 'CP S.A.T.',
    pob_sat_prv VARCHAR(100) COMMENT 'Población S.A.T.',
    prov_sat_prv VARCHAR(100) COMMENT 'Provincia S.A.T.',
    telf_sat_prv VARCHAR(255) COMMENT 'Teléfonos S.A.T.',
    fax_sat_prv VARCHAR(50) COMMENT 'Fax S.A.T.',
    email_sat_prv VARCHAR(255) COMMENT 'Email S.A.T.',
    
    -- Observaciones
    obs_prv TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_prv TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de alta del proveedor',
    fmod_prv TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_prv BOOLEAN DEFAULT TRUE COMMENT 'Proveedor activo/inactivo',
    
    -- Índices para búsquedas frecuentes
    INDEX idx_cod_prv (cod_prv),
    INDEX idx_nombre_prv (nombre_prv),
    INDEX idx_nif_prv (nif_prv),
    INDEX idx_poblac_prv (poblac_prv),
    INDEX idx_prov_prv (prov_prv)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de proveedores';

-- ========================================================
-- TABLA DE CONTACTOS DE PROVEEDORES (relación 1:N)
-- ========================================================

CREATE TABLE contactos_prv (
    -- Clave primaria
    id_con_prv INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Clave foránea hacia proveedores
    id_prv INT NOT NULL COMMENT 'ID del proveedor',
    
    -- Datos del contacto
    nombre_con_prv VARCHAR(100) NOT NULL COMMENT 'Nombre del contacto',
    apellidos_con_prv VARCHAR(150) COMMENT 'Apellidos del contacto',
    cargo_con_prv VARCHAR(100) COMMENT 'Cargo o puesto',
    dpto_con_prv VARCHAR(100) COMMENT 'Departamento',
    
    -- Datos de contacto
    telf_con_prv VARCHAR(50) COMMENT 'Teléfono directo',
    movil_con_prv VARCHAR(50) COMMENT 'Teléfono móvil',
    email_con_prv VARCHAR(255) COMMENT 'Email del contacto',
    ext_con_prv VARCHAR(10) COMMENT 'Extensión telefónica',
    
    -- Preferencias
    principal_con_prv BOOLEAN DEFAULT FALSE COMMENT 'Contacto principal',
    obs_con_prv TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_con_prv TIMESTAMP DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de alta',
    fmod_con_prv TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_con_prv BOOLEAN DEFAULT TRUE COMMENT 'Contacto activo/inactivo',
    
    -- Clave foránea
    CONSTRAINT fk_contacto_proveedor FOREIGN KEY (id_prv) 
        REFERENCES proveedores(id_prv) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    
    -- Índices
    INDEX idx_id_prv_con (id_prv),
    INDEX idx_nombre_con_prv (nombre_con_prv),
    INDEX idx_email_con_prv (email_con_prv),
    INDEX idx_principal_con_prv (principal_con_prv)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de personas de contacto de proveedores';