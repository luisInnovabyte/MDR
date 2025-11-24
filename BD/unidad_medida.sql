CREATE TABLE unidad_medida (
    id_unidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_unidad VARCHAR(50) NOT NULL,
    name_unidad VARCHAR(50) NOT NULL COMMENT 'Nombre en inglés',
    descr_unidad VARCHAR(255),
    simbolo_unidad VARCHAR(10),
    activo_unidad boolean default true, 
    created_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);