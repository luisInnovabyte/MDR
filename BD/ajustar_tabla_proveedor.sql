-- ================================================================
-- Script para ajustar la tabla proveedor a la estructura esperada
-- ================================================================

-- Opción 1: Si la tabla no existe, crearla
CREATE TABLE IF NOT EXISTS proveedor (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    codigo_proveedor VARCHAR(20) NOT NULL UNIQUE,
    nombre_proveedor VARCHAR(255) NOT NULL,
    direccion_proveedor VARCHAR(255),
    cp_proveedor VARCHAR(10),
    poblacion_proveedor VARCHAR(100),
    provincia_proveedor VARCHAR(100),
    nif_proveedor VARCHAR(20),
    telefono_proveedor VARCHAR(255),
    fax_proveedor VARCHAR(50),
    web_proveedor VARCHAR(255),
    email_proveedor VARCHAR(255),
    persona_contacto_proveedor VARCHAR(255),
    direccion_sat_proveedor VARCHAR(255),
    cp_sat_proveedor VARCHAR(10),
    poblacion_sat_proveedor VARCHAR(100),
    provincia_sat_proveedor VARCHAR(100),
    telefono_sat_proveedor VARCHAR(255),
    fax_sat_proveedor VARCHAR(50),
    email_sat_proveedor VARCHAR(255),
    observaciones_proveedor TEXT,
    activo_proveedor BOOLEAN DEFAULT TRUE,
    created_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Opción 2: Si la tabla existe pero le faltan columnas, añadirlas
-- (Estas sentencias solo se ejecutan si la columna no existe)

-- Verificar y añadir columnas principales si no existen
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'codigo_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN codigo_proveedor VARCHAR(20) NOT NULL UNIQUE AFTER id_proveedor',
    'SELECT "Column codigo_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'nombre_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN nombre_proveedor VARCHAR(255) NOT NULL AFTER codigo_proveedor',
    'SELECT "Column nombre_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'direccion_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN direccion_proveedor VARCHAR(255) AFTER nombre_proveedor',
    'SELECT "Column direccion_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'cp_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN cp_proveedor VARCHAR(10) AFTER direccion_proveedor',
    'SELECT "Column cp_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'poblacion_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN poblacion_proveedor VARCHAR(100) AFTER cp_proveedor',
    'SELECT "Column poblacion_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'provincia_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN provincia_proveedor VARCHAR(100) AFTER poblacion_proveedor',
    'SELECT "Column provincia_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'nif_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN nif_proveedor VARCHAR(20) AFTER provincia_proveedor',
    'SELECT "Column nif_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'telefono_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN telefono_proveedor VARCHAR(255) AFTER nif_proveedor',
    'SELECT "Column telefono_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'fax_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN fax_proveedor VARCHAR(50) AFTER telefono_proveedor',
    'SELECT "Column fax_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'web_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN web_proveedor VARCHAR(255) AFTER fax_proveedor',
    'SELECT "Column web_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'email_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN email_proveedor VARCHAR(255) AFTER web_proveedor',
    'SELECT "Column email_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'persona_contacto_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN persona_contacto_proveedor VARCHAR(255) AFTER email_proveedor',
    'SELECT "Column persona_contacto_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Campos SAT
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'direccion_sat_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN direccion_sat_proveedor VARCHAR(255) AFTER persona_contacto_proveedor',
    'SELECT "Column direccion_sat_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'cp_sat_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN cp_sat_proveedor VARCHAR(10) AFTER direccion_sat_proveedor',
    'SELECT "Column cp_sat_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'poblacion_sat_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN poblacion_sat_proveedor VARCHAR(100) AFTER cp_sat_proveedor',
    'SELECT "Column poblacion_sat_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'provincia_sat_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN provincia_sat_proveedor VARCHAR(100) AFTER poblacion_sat_proveedor',
    'SELECT "Column provincia_sat_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'telefono_sat_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN telefono_sat_proveedor VARCHAR(255) AFTER provincia_sat_proveedor',
    'SELECT "Column telefono_sat_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'fax_sat_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN fax_sat_proveedor VARCHAR(50) AFTER telefono_sat_proveedor',
    'SELECT "Column fax_sat_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'email_sat_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN email_sat_proveedor VARCHAR(255) AFTER fax_sat_proveedor',
    'SELECT "Column email_sat_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Campos finales
SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'observaciones_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN observaciones_proveedor TEXT AFTER email_sat_proveedor',
    'SELECT "Column observaciones_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'activo_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN activo_proveedor BOOLEAN DEFAULT TRUE AFTER observaciones_proveedor',
    'SELECT "Column activo_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'created_at_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN created_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP AFTER activo_proveedor',
    'SELECT "Column created_at_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(
    (SELECT COUNT(*) FROM INFORMATION_SCHEMA.COLUMNS 
     WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = 'proveedor' AND COLUMN_NAME = 'updated_at_proveedor') = 0,
    'ALTER TABLE proveedor ADD COLUMN updated_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER created_at_proveedor',
    'SELECT "Column updated_at_proveedor already exists" as msg'
));
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;