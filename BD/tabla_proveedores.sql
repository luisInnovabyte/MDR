-- ================================================================
-- Tabla para gestión de proveedores
-- Creada: 27 de octubre de 2025
-- Descripción: Almacena información completa de proveedores
-- ================================================================

-- Crear tabla de proveedores
CREATE TABLE proveedores (
    id_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    
    -- Información básica
    nombre_proveedor VARCHAR(150) NOT NULL,
    razon_social VARCHAR(200),
    nif_cif VARCHAR(20) UNIQUE,
    
    -- Información de contacto
    telefono_principal VARCHAR(20),
    telefono_secundario VARCHAR(20),
    email_principal VARCHAR(100),
    email_secundario VARCHAR(100),
    sitio_web VARCHAR(150),
    
    -- Dirección
    direccion TEXT,
    ciudad VARCHAR(100),
    provincia VARCHAR(100),
    codigo_postal VARCHAR(10),
    pais VARCHAR(100) DEFAULT 'España',
    
    -- Información comercial
    tipo_proveedor ENUM('Producto', 'Servicio', 'Mixto') DEFAULT 'Producto',
    categoria_principal VARCHAR(100),
    condiciones_pago ENUM('Contado', '30 días', '60 días', '90 días', 'Personalizado') DEFAULT '30 días',
    descuento_comercial DECIMAL(5,2) DEFAULT 0.00,
    limite_credito DECIMAL(12,2) DEFAULT 0.00,
    
    -- Información bancaria
    banco VARCHAR(100),
    iban VARCHAR(34),
    swift_bic VARCHAR(11),
    
    -- Persona de contacto
    contacto_nombre VARCHAR(100),
    contacto_cargo VARCHAR(100),
    contacto_telefono VARCHAR(20),
    contacto_email VARCHAR(100),
    
    -- Control de calidad y evaluación
    calificacion ENUM('Excelente', 'Bueno', 'Regular', 'Malo') DEFAULT 'Bueno',
    certificaciones TEXT, -- ISO, CE, etc.
    tiempo_entrega_promedio INT COMMENT 'Días promedio de entrega',
    
    -- Estado y control administrativo
    estado ENUM('Activo', 'Inactivo', 'Bloqueado', 'En evaluación') DEFAULT 'Activo',
    observaciones TEXT,
    
    -- Campos de auditoría
    fecha_alta DATETIME DEFAULT CURRENT_TIMESTAMP,
    fecha_modificacion DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    usuario_alta VARCHAR(50),
    usuario_modificacion VARCHAR(50),
    
    -- Índices para optimizar consultas
    INDEX idx_nombre (nombre_proveedor),
    INDEX idx_nif (nif_cif),
    INDEX idx_estado (estado),
    INDEX idx_tipo (tipo_proveedor),
    INDEX idx_fecha_alta (fecha_alta)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci 
COMMENT='Tabla para gestión completa de proveedores';

-- ================================================================
-- Tabla para categorías de proveedores (opcional)
-- ================================================================

CREATE TABLE categorias_proveedores (
    id_categoria INT AUTO_INCREMENT PRIMARY KEY,
    nombre_categoria VARCHAR(100) NOT NULL UNIQUE,
    descripcion TEXT,
    estado ENUM('Activo', 'Inactivo') DEFAULT 'Activo',
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    INDEX idx_nombre (nombre_categoria)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Categorías para clasificar proveedores';

-- ================================================================
-- Tabla para historial de evaluaciones de proveedores
-- ================================================================

CREATE TABLE evaluaciones_proveedores (
    id_evaluacion INT AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT NOT NULL,
    fecha_evaluacion DATE NOT NULL,
    calificacion_calidad TINYINT CHECK (calificacion_calidad BETWEEN 1 AND 10),
    calificacion_precio TINYINT CHECK (calificacion_precio BETWEEN 1 AND 10),
    calificacion_entrega TINYINT CHECK (calificacion_entrega BETWEEN 1 AND 10),
    calificacion_servicio TINYINT CHECK (calificacion_servicio BETWEEN 1 AND 10),
    calificacion_general DECIMAL(3,1) GENERATED ALWAYS AS (
        (calificacion_calidad + calificacion_precio + calificacion_entrega + calificacion_servicio) / 4
    ) STORED,
    comentarios TEXT,
    evaluador VARCHAR(50),
    fecha_creacion DATETIME DEFAULT CURRENT_TIMESTAMP,
    
    FOREIGN KEY (id_proveedor) REFERENCES proveedores(id_proveedor) ON DELETE CASCADE,
    INDEX idx_proveedor (id_proveedor),
    INDEX idx_fecha (fecha_evaluacion)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
COMMENT='Historial de evaluaciones de proveedores';

-- ================================================================
-- Insertar datos de ejemplo
-- ================================================================

-- Categorías de ejemplo
INSERT INTO categorias_proveedores (nombre_categoria, descripcion) VALUES
('Toldos y Parasoles', 'Proveedores especializados en toldos, parasoles y estructuras'),
('Textiles', 'Proveedores de lonas, telas y materiales textiles'),
('Estructuras Metálicas', 'Proveedores de perfiles, tubos y estructuras metálicas'),
('Herrajes y Accesorios', 'Proveedores de tornillería, herrajes y accesorios'),
('Motores y Automatización', 'Proveedores de motores y sistemas de automatización'),
('Servicios', 'Proveedores de servicios especializados');

-- Proveedores de ejemplo
INSERT INTO proveedores (
    nombre_proveedor, razon_social, nif_cif, telefono_principal, email_principal,
    direccion, ciudad, provincia, codigo_postal, tipo_proveedor, categoria_principal,
    condiciones_pago, contacto_nombre, estado, usuario_alta
) VALUES
('Toldos Mediterráneo S.L.', 'Toldos Mediterráneo Sociedad Limitada', 'B12345678', 
 '965123456', 'info@toldosmediterraneo.com', 'Calle Industrial 15', 'Alicante', 
 'Alicante', '03008', 'Producto', 'Toldos y Parasoles', '30 días', 
 'Juan García López', 'Activo', 'admin'),

('Textiles Levante S.A.', 'Textiles Levante Sociedad Anónima', 'A87654321',
 '963987654', 'comercial@textileslevante.com', 'Polígono Industrial Norte, Nave 8',
 'Valencia', 'Valencia', '46015', 'Producto', 'Textiles', '60 días',
 'María Fernández', 'Activo', 'admin'),

('Herrajes del Sur', 'Herrajes del Sur S.L.', 'B45678912',
 '954321987', 'pedidos@herrajesdelsur.es', 'Avenida de la Industria 42',
 'Sevilla', 'Sevilla', '41015', 'Producto', 'Herrajes y Accesorios', '30 días',
 'Carlos Ruiz', 'Activo', 'admin');

-- ================================================================
-- Vistas útiles para consultas frecuentes
-- ================================================================

-- Vista de proveedores activos con información resumida
CREATE VIEW vista_proveedores_activos AS
SELECT 
    id_proveedor,
    nombre_proveedor,
    nif_cif,
    telefono_principal,
    email_principal,
    ciudad,
    provincia,
    tipo_proveedor,
    categoria_principal,
    calificacion,
    fecha_alta
FROM proveedores 
WHERE estado = 'Activo'
ORDER BY nombre_proveedor;

-- Vista de proveedores con evaluación promedio
CREATE VIEW vista_proveedores_evaluados AS
SELECT 
    p.id_proveedor,
    p.nombre_proveedor,
    p.calificacion,
    ROUND(AVG(e.calificacion_general), 2) as evaluacion_promedio,
    COUNT(e.id_evaluacion) as total_evaluaciones,
    MAX(e.fecha_evaluacion) as ultima_evaluacion
FROM proveedores p
LEFT JOIN evaluaciones_proveedores e ON p.id_proveedor = e.id_proveedor
WHERE p.estado = 'Activo'
GROUP BY p.id_proveedor, p.nombre_proveedor, p.calificacion
ORDER BY evaluacion_promedio DESC;

-- ================================================================
-- Procedimientos almacenados útiles
-- ================================================================

DELIMITER //

-- Procedimiento para buscar proveedores
CREATE PROCEDURE BuscarProveedores(
    IN p_termino_busqueda VARCHAR(100),
    IN p_tipo_proveedor VARCHAR(20),
    IN p_estado VARCHAR(20)
)
BEGIN
    SELECT 
        id_proveedor,
        nombre_proveedor,
        razon_social,
        telefono_principal,
        email_principal,
        ciudad,
        tipo_proveedor,
        calificacion,
        estado
    FROM proveedores
    WHERE 
        (p_termino_busqueda IS NULL OR 
         nombre_proveedor LIKE CONCAT('%', p_termino_busqueda, '%') OR
         razon_social LIKE CONCAT('%', p_termino_busqueda, '%') OR
         nif_cif LIKE CONCAT('%', p_termino_busqueda, '%'))
    AND (p_tipo_proveedor IS NULL OR tipo_proveedor = p_tipo_proveedor)
    AND (p_estado IS NULL OR estado = p_estado)
    ORDER BY nombre_proveedor;
END //

-- Procedimiento para obtener estadísticas de proveedores
CREATE PROCEDURE EstadisticasProveedores()
BEGIN
    SELECT 
        COUNT(*) as total_proveedores,
        SUM(CASE WHEN estado = 'Activo' THEN 1 ELSE 0 END) as activos,
        SUM(CASE WHEN estado = 'Inactivo' THEN 1 ELSE 0 END) as inactivos,
        SUM(CASE WHEN tipo_proveedor = 'Producto' THEN 1 ELSE 0 END) as productos,
        SUM(CASE WHEN tipo_proveedor = 'Servicio' THEN 1 ELSE 0 END) as servicios,
        SUM(CASE WHEN calificacion = 'Excelente' THEN 1 ELSE 0 END) as excelentes,
        SUM(CASE WHEN calificacion = 'Bueno' THEN 1 ELSE 0 END) as buenos
    FROM proveedores;
END //

DELIMITER ;

-- ================================================================
-- Comentarios y documentación
-- ================================================================

/*
CAMPOS PRINCIPALES:
- id_proveedor: Identificador único autoincremental
- nombre_proveedor: Nombre comercial del proveedor
- razon_social: Razón social oficial de la empresa
- nif_cif: Número de identificación fiscal (único)
- Campos de contacto: teléfonos, emails, web, dirección completa
- tipo_proveedor: Clasificación por tipo de productos/servicios
- condiciones_pago: Términos de pago acordados
- calificacion: Evaluación general del proveedor
- estado: Control del estado del proveedor en el sistema

CARACTERÍSTICAS:
- Soporte para múltiples contactos (principal/secundario)
- Información bancaria para pagos
- Persona de contacto específica
- Sistema de calificación y evaluación
- Campos de auditoría (fechas, usuarios)
- Índices optimizados para consultas frecuentes
- Soporte UTF-8 completo
- Constraints para integridad de datos

USO:
Esta estructura permite gestionar proveedores de forma completa,
incluyendo evaluaciones, categorización y seguimiento histórico.
Está diseñada para integrarse con el sistema MVC existente.
*/