-- Tabla para estados de presupuesto
-- Creada: 14 de noviembre de 2025

CREATE TABLE IF NOT EXISTS `estado_presupuesto` (
    `id_estado_ppto` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `codigo_estado_ppto` VARCHAR(20) NOT NULL UNIQUE,
    `nombre_estado_ppto` VARCHAR(100) NOT NULL,
    `color_estado_ppto` VARCHAR(7) DEFAULT '#007bff',
    `orden_estado_ppto` INT DEFAULT 0,
    `observaciones_estado_ppto` TEXT,
    `activo_estado_ppto` BOOLEAN DEFAULT TRUE,
    `created_at_estado_ppto` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    `updated_at_estado_ppto` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX `idx_estado_presupuesto_activo` (`activo_estado_ppto`),
    INDEX `idx_estado_presupuesto_codigo` (`codigo_estado_ppto`),
    INDEX `idx_estado_presupuesto_orden` (`orden_estado_ppto`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar algunos estados de presupuesto por defecto
INSERT INTO `estado_presupuesto` (`codigo_estado_ppto`, `nombre_estado_ppto`, `color_estado_ppto`, `orden_estado_ppto`, `observaciones_estado_ppto`) VALUES
('PEND', 'Pendiente', '#ffc107', 1, 'Presupuesto pendiente de revisión'),
('PROC', 'En Proceso', '#17a2b8', 2, 'Presupuesto en proceso de elaboración'),
('APROB', 'Aprobado', '#28a745', 3, 'Presupuesto aprobado por el cliente'),
('RECH', 'Rechazado', '#dc3545', 4, 'Presupuesto rechazado por el cliente'),
('CANC', 'Cancelado', '#6c757d', 5, 'Presupuesto cancelado');