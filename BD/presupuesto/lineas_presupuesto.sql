-- ========================================================
-- TABLA DE LÍNEAS DE PRESUPUESTO
-- ========================================================

CREATE TABLE linea_presupuesto (
    id_linea_ppto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- Relación con la versión del presupuesto
    id_version_presupuesto INT UNSIGNED NOT NULL COMMENT 'Cada versión tiene sus propias líneas',
    
    -- Referencia al artículo original
    id_articulo INT UNSIGNED,
    
    -- Estructura de la línea
    numero_linea_ppto INT NOT NULL COMMENT 'Orden visual de la línea',
    tipo_linea_ppto ENUM('articulo', 'kit', 'componente_kit', 'seccion', 'texto', 'subtotal') DEFAULT 'articulo',
    
    -- Para estructura jerárquica (KITs y sus componentes)
    id_linea_padre INT UNSIGNED COMMENT 'Para componentes de KIT, referencia a la línea KIT padre',
    nivel_jerarquia TINYINT DEFAULT 0 COMMENT '0=principal, 1=componente KIT, 2=sub-componente',
    
    -- Datos del artículo/ítem
    codigo_linea_ppto VARCHAR(50),
    descripcion_linea_ppto TEXT NOT NULL,
    
    -- FECHAS DE PLANIFICACIÓN Y COBRO
    fecha_montaje_linea_ppto DATE COMMENT 'Fecha orientativa de montaje (solo informativa para planning)',
    fecha_desmontaje_linea_ppto DATE COMMENT 'Fecha orientativa de desmontaje (solo informativa para planning)',
    fecha_inicio_linea_ppto DATE COMMENT 'Fecha REAL de inicio para el cobro (heredada de cabecera pero modificable)',
    fecha_fin_linea_ppto DATE COMMENT 'Fecha REAL de fin para el cobro (heredada de cabecera pero modificable)',
    
    -- UBICACIÓN
    id_ubicacion INT UNSIGNED COMMENT 'Lugar específico de montaje de esta línea',
    
    -- Cantidades y precios BASE (sin calcular)
    cantidad_linea_ppto DECIMAL(10,2) DEFAULT 1.00,
    precio_unitario_linea_ppto DECIMAL(10,2) DEFAULT 0.00 COMMENT 'Precio base del artículo (heredado pero modificable)',
    descuento_linea_ppto DECIMAL(5,2) DEFAULT 0.00 COMMENT 'Descuento específico sobre este artículo (%)',
    
    -- COEFICIENTE REDUCTOR
    aplicar_coeficiente_linea_ppto BOOLEAN DEFAULT FALSE COMMENT 'Si se aplica coeficiente reductor (Sí/No)',
    id_coeficiente INT UNSIGNED COMMENT 'Referencia al coeficiente aplicado',
    valor_coeficiente_linea_ppto DECIMAL(10,2) COMMENT 'Valor aplicado del coeficiente',
    jornadas_linea_ppto INT COMMENT 'Número de jornadas para el cálculo del coeficiente',
    
    -- IVA
    id_iva INT UNSIGNED,
    porcentaje_iva_linea_ppto DECIMAL(5,2) DEFAULT 21.00,
    
    -- OBSERVACIONES
    observaciones_linea_ppto TEXT COMMENT 'Observaciones específicas de esta línea',
    mostrar_obs_articulo_linea_ppto BOOLEAN DEFAULT TRUE COMMENT 'Si mostrar las observaciones del artículo original',
    
    -- VISIBILIDAD KIT
    ocultar_detalle_kit_linea_ppto BOOLEAN DEFAULT FALSE COMMENT 'Si TRUE, no mostrar desglose del KIT; si FALSE, mostrar componentes',
    
    -- Visibilidad general
    mostrar_en_presupuesto BOOLEAN DEFAULT TRUE COMMENT 'Si se muestra al cliente en el presupuesto',
    es_opcional BOOLEAN DEFAULT FALSE COMMENT 'Si es una línea opcional',
    
    -- Control de orden y estado
    orden_linea_ppto INT DEFAULT 0,
    activo_linea_ppto BOOLEAN DEFAULT TRUE,
    
    created_at_linea_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_linea_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Claves foráneas
    CONSTRAINT fk_linea_ppto_version FOREIGN KEY (id_version_presupuesto) 
        REFERENCES presupuesto_version(id_version_presupuesto) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_linea_ppto_articulo FOREIGN KEY (id_articulo) 
        REFERENCES articulo(id_articulo) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_linea_ppto_linea_padre FOREIGN KEY (id_linea_padre) 
        REFERENCES linea_presupuesto(id_linea_ppto) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_linea_ppto_ubicacion FOREIGN KEY (id_ubicacion) 
        REFERENCES cliente_ubicacion(id_ubicacion) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_linea_ppto_coeficiente FOREIGN KEY (id_coeficiente) 
        REFERENCES coeficiente(id_coeficiente) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
        
    CONSTRAINT fk_linea_ppto_iva FOREIGN KEY (id_iva) 
        REFERENCES tipo_iva(id_iva) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    -- Índices
    INDEX idx_id_version_presupuesto_linea (id_version_presupuesto),
    INDEX idx_id_articulo_linea (id_articulo),
    INDEX idx_orden_linea_ppto (orden_linea_ppto),
    INDEX idx_tipo_linea (tipo_linea_ppto),
    INDEX idx_linea_padre (id_linea_padre),
    INDEX idx_fecha_montaje (fecha_montaje_linea_ppto),
    INDEX idx_fecha_inicio (fecha_inicio_linea_ppto),
    INDEX idx_ubicacion (id_ubicacion)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci COMMENT='Líneas de detalle de cada versión de presupuesto con soporte para KITs jerárquicos';