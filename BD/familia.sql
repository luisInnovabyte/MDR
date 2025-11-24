CREATE TABLE familia (
    id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_familia VARCHAR(20) NOT NULL UNIQUE,
    nombre_familia VARCHAR(100) NOT NULL,
    name_familia VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
    descr_familia VARCHAR(255),
    imagen_familia VARCHAR(255) DEFAULT '' COMMENT 'Nombre del archivo de imagen de la familia',
    activo_familia BOOLEAN DEFAULT TRUE,
    coeficiente_familia BOOLEAN,
    created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;