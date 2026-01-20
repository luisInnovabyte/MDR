-- ========================================================
-- TABLA PRESUPUESTO - VERSION EN MINUSCULAS
-- Para compatibilidad con base de datos existente
-- ========================================================

-- ========================================================
-- TABLA DE PRESUPUESTOS (ACTUALIZADA Y CORREGIDA)
-- ========================================================

DROP TABLE IF EXISTS presupuesto;

CREATE TABLE presupuesto (
    id_presupuesto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_presupuesto VARCHAR(50) NOT NULL UNIQUE,
    id_cliente INT UNSIGNED NOT NULL,
    id_contacto_cliente INT UNSIGNED,
    id_estado_ppto INT UNSIGNED NOT NULL,
    id_forma_pago INT UNSIGNED,
    id_metodo INT,
    
    -- =====================================================
    -- FECHAS
    -- =====================================================
    fecha_presupuesto DATE NOT NULL 
        COMMENT 'Fecha de emisión del presupuesto',
    
    fecha_validez_presupuesto DATE 
        COMMENT 'Fecha hasta la que es válido el presupuesto',
    
    fecha_inicio_evento_presupuesto DATE 
        COMMENT 'Fecha de inicio del evento/servicio',
    
    fecha_fin_evento_presupuesto DATE 
        COMMENT 'Fecha de finalización del evento/servicio',
    
    -- =====================================================
    -- DATOS DEL EVENTO/PROYECTO
    -- =====================================================
    numero_pedido_cliente_presupuesto VARCHAR(80) 
        COMMENT 'Número de pedido del cliente (si lo proporciona)',
    
    nombre_evento_presupuesto VARCHAR(255) 
        COMMENT 'Nombre del evento o proyecto',
    
    -- Ubicación del evento (4 campos separados)
    direccion_evento_presupuesto VARCHAR(100) 
        COMMENT 'Dirección del evento',
    
    poblacion_evento_presupuesto VARCHAR(80) 
        COMMENT 'Población/Ciudad del evento',
    
    cp_evento_presupuesto VARCHAR(10) 
        COMMENT 'Código postal del evento',
    
    provincia_evento_presupuesto VARCHAR(80) 
        COMMENT 'Provincia del evento',
    
    -- =====================================================
    -- OBSERVACIONES ESPECÍFICAS DEL PRESUPUESTO
    -- =====================================================
    observaciones_cabecera_presupuesto TEXT 
        COMMENT 'Observaciones iniciales del presupuesto',
    
    observaciones_pie_presupuesto TEXT 
        COMMENT 'Observaciones específicas adicionales al pie',
    
    mostrar_obs_familias_presupuesto BOOLEAN DEFAULT TRUE 
        COMMENT 'Si TRUE, muestra observaciones de las familias usadas',
    
    mostrar_obs_articulos_presupuesto BOOLEAN DEFAULT TRUE 
        COMMENT 'Si TRUE, muestra observaciones de los artículos usados',
    
    observaciones_internas_presupuesto TEXT 
        COMMENT 'Notas internas, no se imprimen en el PDF',
    
    -- =====================================================
    -- CONTROL
    -- =====================================================
    activo_presupuesto BOOLEAN DEFAULT TRUE,
    created_at_presupuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_presupuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- =====================================================
    -- CLAVES FORÁNEAS
    -- =====================================================
    CONSTRAINT fk_presupuesto_cliente FOREIGN KEY (id_cliente) 
        REFERENCES cliente(id_cliente) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_contacto FOREIGN KEY (id_contacto_cliente) 
        REFERENCES contacto_cliente(id_contacto_cliente) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_estado FOREIGN KEY (id_estado_ppto) 
        REFERENCES estado_presupuesto(id_estado_ppto) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_forma_pago FOREIGN KEY (id_forma_pago) 
        REFERENCES forma_pago(id_pago) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_metodo_contacto FOREIGN KEY (id_metodo) 
        REFERENCES metodos_contacto(id_metodo) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    -- =====================================================
    -- ÍNDICES DE OPTIMIZACIÓN
    -- =====================================================
    INDEX idx_numero_presupuesto (numero_presupuesto),
    INDEX idx_id_cliente_presupuesto (id_cliente),
    INDEX idx_id_estado_presupuesto (id_estado_ppto),
    INDEX idx_fecha_presupuesto (fecha_presupuesto),
    INDEX idx_fecha_inicio_evento (fecha_inicio_evento_presupuesto),
    INDEX idx_fecha_fin_evento (fecha_fin_evento_presupuesto),
    INDEX idx_numero_pedido_cliente (numero_pedido_cliente_presupuesto),
    INDEX idx_poblacion_evento (poblacion_evento_presupuesto),
    INDEX idx_provincia_evento (provincia_evento_presupuesto)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 
COMMENT='Cabecera de presupuestos para alquiler de equipos';


-- ============================================
-- ALTER TABLE: presupuesto
-- Añadir campos de observaciones en inglés
-- Fecha: 2025-12-13
-- ============================================

ALTER TABLE presupuesto

ADD COLUMN observaciones_cabecera_ingles_presupuesto TEXT 
    COMMENT 'Observaciones iniciales del presupuesto en inglés'
    AFTER observaciones_cabecera_presupuesto,

ADD COLUMN observaciones_pie_ingles_presupuesto TEXT 
    COMMENT 'Observaciones específicas adicionales al pie en inglés'
    AFTER observaciones_pie_presupuesto;



-- ============================================
-- ALTER TABLE: presupuesto
-- Añadir control de aplicación de coeficientes
-- Fecha: 2024-12-18
-- Autor: Luis - MDR
-- ============================================
ALTER TABLE presupuesto
ADD COLUMN aplicar_coeficientes_presupuesto BOOLEAN DEFAULT TRUE 
    COMMENT 'TRUE: aplicar coeficientes reductores por días. FALSE: usar precio base sin reducción'
    AFTER numero_pedido_cliente_presupuesto;

-- Crear índice para consultas rápidas
CREATE INDEX idx_aplicar_coeficientes_presupuesto 
    ON presupuesto(aplicar_coeficientes_presupuesto);



-- ========================================================
-- MODIFICACIÓN TABLA PRESUPUESTO - Sistema de Descuentos (CORREGIDO)
-- DESCRIPCIÓN: Añadir porcentaje de descuento específico del presupuesto
-- FECHA: 2024-12-19
-- ========================================================

ALTER TABLE presupuesto

-- =====================================================
-- DESCUENTO ESPECÍFICO DEL PRESUPUESTO
-- =====================================================
ADD COLUMN descuento_presupuesto DECIMAL(5,2) NOT NULL DEFAULT 0.00
    COMMENT 'Porcentaje de descuento aplicado en este presupuesto (0.00 a 100.00). Se hereda de porcentaje_descuento_cliente pero puede modificarse'
    AFTER aplicar_coeficientes_presupuesto,

-- =====================================================
-- CONSTRAINT DE VALIDACIÓN
-- =====================================================
ADD CONSTRAINT chk_descuento_presupuesto 
    CHECK (descuento_presupuesto >= 0.00 AND descuento_presupuesto <= 100.00),

-- =====================================================
-- ÍNDICE
-- =====================================================
ADD INDEX idx_descuento_presupuesto (descuento_presupuesto);



### Flujo correcto del sistema de descuentos:

1. CLIENTE tiene un descuento habitual:
   cliente.porcentaje_descuento_cliente (ej: 10.00%)

2. Al crear PRESUPUESTO:
   presupuesto.descuento_presupuesto = cliente.porcentaje_descuento_cliente
   (Se hereda pero puede modificarse)

3. El usuario puede MODIFICAR el descuento del presupuesto:
   - Dejarlo igual que el cliente
   - Aumentarlo (promoción especial)
   - Reducirlo (menos descuento)
   - Ponerlo a 0 (sin descuento)

4. Al calcular LÍNEAS de presupuesto:
   SI familia.permite_descuento_familia = TRUE
   ENTONCES aplicar presupuesto.descuento_presupuesto
   SINO descuento = 0



-- ============================================
-- ALTER TABLE: presupuesto_cabecera
-- Descripción: Añade campos para sistema de versiones
-- Fecha: 2025-01-12
-- ============================================

-- Renombrar la tabla si aún se llama 'presupuesto'
-- RENAME TABLE presupuesto TO presupuesto_cabecera;

-- Añadir campos de control de versiones
ALTER TABLE presupuesto_cabecera
    -- Versión actual activa del presupuesto
    ADD COLUMN version_actual_presupuesto INT UNSIGNED NOT NULL DEFAULT 1 
        COMMENT 'Número de versión activa actual' 
        AFTER id_estado_ppto,
    
    -- Estado general del presupuesto (puede diferir del estado de cada versión)
    ADD COLUMN estado_general_presupuesto ENUM(
        'borrador', 
        'enviado', 
        'aprobado', 
        'rechazado', 
        'cancelado'
    ) NOT NULL DEFAULT 'borrador' 
        COMMENT 'Estado general del presupuesto (sincronizado con version_actual)' 
        AFTER version_actual_presupuesto,
    
    -- Índice para búsquedas por versión actual
    ADD INDEX idx_version_actual_presupuesto (version_actual_presupuesto),
    
    -- Índice para búsquedas por estado general
    ADD INDEX idx_estado_general_presupuesto (estado_general_presupuesto);