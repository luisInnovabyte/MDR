-- ========================================================
-- TABLA DE TIPOS DE IVA
-- ========================================================

CREATE TABLE tipos_iva (
    -- Clave primaria
    id_iva INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Datos del IVA
    cod_iva VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código del IVA',
    nombre_iva VARCHAR(100) NOT NULL COMMENT 'Nombre descriptivo',
    porc_iva DECIMAL(5,2) NOT NULL COMMENT 'Porcentaje de IVA',
    porc_req_iva DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Porcentaje de recargo de equivalencia',
    
    -- Observaciones
    obs_iva TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_iva TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fmod_iva TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_iva BOOLEAN DEFAULT TRUE COMMENT 'IVA activo/inactivo',
    
    -- Índices
    INDEX idx_cod_iva (cod_iva),
    INDEX idx_activo_iva (activo_iva)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de tipos de IVA';

-- ========================================================
-- TABLA DE FORMAS DE ENVÍO
-- ========================================================

CREATE TABLE formas_envio (
    -- Clave primaria
    id_fenv INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Datos del método de envío
    cod_fenv VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código forma de envío',
    nombre_fenv VARCHAR(100) NOT NULL COMMENT 'Nombre del método',
    desc_fenv VARCHAR(255) COMMENT 'Descripción',
    
    -- Observaciones
    obs_fenv TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_fenv TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fmod_fenv TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_fenv BOOLEAN DEFAULT TRUE COMMENT 'Forma de envío activa/inactiva',
    
    -- Índices
    INDEX idx_cod_fenv (cod_fenv),
    INDEX idx_activo_fenv (activo_fenv)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de formas de envío (email, WhatsApp, correo postal, etc.)';

-- ========================================================
-- TABLA DE FORMAS DE PAGO
-- ========================================================

CREATE TABLE formas_pago (
    -- Clave primaria
    id_fpag INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Datos de la forma de pago
    cod_fpag VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código forma de pago',
    nombre_fpag VARCHAR(100) NOT NULL COMMENT 'Nombre de la forma de pago',
    dias_fpag INT DEFAULT 0 COMMENT 'Días de plazo de pago',
    desc_fpag DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Descuento por pronto pago (%)',
    
    -- Observaciones
    obs_fpag TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_fpag TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fmod_fpag TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_fpag BOOLEAN DEFAULT TRUE COMMENT 'Forma de pago activa/inactiva',
    
    -- Índices
    INDEX idx_cod_fpag (cod_fpag),
    INDEX idx_activo_fpag (activo_fpag)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de formas de pago (contado, transferencia, tarjeta, etc.)';

-- ========================================================
-- TABLA DE ESTADOS DE PRESUPUESTO
-- ========================================================

CREATE TABLE estados_ppto (
    -- Clave primaria
    id_est_ppto INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Datos del estado
    cod_est_ppto VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código del estado',
    nombre_est_ppto VARCHAR(100) NOT NULL COMMENT 'Nombre del estado',
    color_est_ppto VARCHAR(7) COMMENT 'Color hexadecimal para UI (#FFFFFF)',
    orden_est_ppto INT DEFAULT 0 COMMENT 'Orden de visualización',
    
    -- Observaciones
    obs_est_ppto TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_est_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fmod_est_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_est_ppto BOOLEAN DEFAULT TRUE COMMENT 'Estado activo/inactivo',
    
    -- Índices
    INDEX idx_cod_est_ppto (cod_est_ppto),
    INDEX idx_orden_est_ppto (orden_est_ppto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de estados de presupuesto (pendiente, enviado, aceptado, rechazado, etc.)';

-- ========================================================
-- TABLA DE UNIDADES DE MEDIDA
-- ========================================================

CREATE TABLE unidades_medida (
    -- Clave primaria
    id_um INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Datos de la unidad
    cod_um VARCHAR(20) NOT NULL UNIQUE COMMENT 'Código unidad de medida',
    nombre_um VARCHAR(100) NOT NULL COMMENT 'Nombre de la unidad',
    abrv_um VARCHAR(10) COMMENT 'Abreviatura',
    
    -- Observaciones
    obs_um TEXT COMMENT 'Observaciones',
    
    -- Metadatos
    falta_um TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fmod_um TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    activo_um BOOLEAN DEFAULT TRUE COMMENT 'Unidad activa/inactiva',
    
    -- Índices
    INDEX idx_cod_um (cod_um),
    INDEX idx_activo_um (activo_um)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Tabla de unidades de medida (unidad, metro, kg, hora, etc.)';