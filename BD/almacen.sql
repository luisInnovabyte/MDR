CREATE TABLE familia (
    id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_familia VARCHAR(20) NOT NULL UNIQUE,
    nombre_familia VARCHAR(100) NOT NULL,
    name_familia VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
    descr_familia VARCHAR(255),
    activo_familia BOOLEAN DEFAULT TRUE,
    id_unidad_familia INT UNSIGNED,
    created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


CREATE TABLE marca (
    id_marca INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_marca VARCHAR(20) NOT NULL UNIQUE,
    nombre_marca VARCHAR(100) NOT NULL,
    name_marca VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
    descr_marca VARCHAR(255),
    activo_marca BOOLEAN DEFAULT TRUE,
    created_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE impuesto (
  id_impuesto INT AUTO_INCREMENT PRIMARY KEY,
  tipo_impuesto VARCHAR(20) NOT NULL COMMENT 'Tipo de impuesto (e.g., IVA, GST)',
  tasa_impuesto DECIMAL(5,2) NOT NULL comment 'Tasa del impuesto en porcentaje',
  descr_impuesto VARCHAR(255),
  activo_impuesto boolean default true, 
  created_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at_impuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

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

CREATE TABLE proveedor (
    id_proveedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_proveedor VARCHAR(100) NOT NULL,
    razon_social_proveedor VARCHAR(150),
    nif_cif_proveedor VARCHAR(20) NOT NULL,
    tipo_proveedor VARCHAR(50),
    categoria_proveedor VARCHAR(50),
    condiciones_pago_proveedor VARCHAR(100),
    limite_credito_proveedor DECIMAL(12,2),
    estado_proveedor VARCHAR(50),
    observaciones_proveedor VARCHAR(255),
    sitio_web_proveedor VARCHAR(100),
    direccion_proveedor VARCHAR(255),
    ciudad_proveedor VARCHAR(100),
    provincia_proveedor VARCHAR(100),
    codigo_postal_proveedor VARCHAR(20),
    pais_proveedor VARCHAR(100),
    auditoria_proveedor TEXT,
    created_at_proveedor DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE proveedor_contacto (
    id_proveedor_contacto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT UNSIGNED NOT NULL,
    telefono_fijo_proveedor_contacto VARCHAR(20),
    telefono_movil_proveedor_contacto VARCHAR(20),
    email_principal_proveedor_contacto VARCHAR(100),
    email_secundario_proveedor_contacto VARCHAR(100),
    created_at_proveedor_contacto DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor_contacto DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
);

CREATE TABLE proveedor_banco (
    id_proveedor_banco INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT UNSIGNED NOT NULL,
    banco_proveedor_banco VARCHAR(100),
    iban_proveedor_banco VARCHAR(34),
    swift_bic_proveedor_banco VARCHAR(20),
    created_at_proveedor_banco DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor_banco DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
);

CREATE TABLE persona_contacto_proveedor (
    id_persona_contacto_proveedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT UNSIGNED NOT NULL,
    nombre_persona_contacto_proveedor VARCHAR(100),
    cargo_persona_contacto_proveedor VARCHAR(50),
    telefono_persona_contacto_proveedor VARCHAR(20),
    email_persona_contacto_proveedor VARCHAR(100),
    created_at_persona_contacto_proveedor DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_persona_contacto_proveedor DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
);

CREATE TABLE proveedor_evaluacion (
    id_proveedor_evaluacion INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_proveedor INT UNSIGNED NOT NULL,
    calificacion_proveedor_evaluacion INT,
    certificaciones_proveedor_evaluacion VARCHAR(255),
    tiempo_entrega_proveedor_evaluacion INT, -- en días
    created_at_proveedor_evaluacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor_evaluacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
);

CREATE TABLE articulo (
    id_articulo INT AUTO_INCREMENT PRIMARY KEY,
    codigo_articulo VARCHAR(40) NOT NULL,
    descripcion_articulo VARCHAR(255) NOT NULL,
    id_familia_articulo INT NOT NULL,
    id_marca_articulo INT NOT NULL,    
    id_modelo_articulo VARCHAR(40) NOT NULL,
    id_unidad_medida_articulo INT NOT NULL,
    id_impuesto_articulo INT NOT NULL,
    precio_compra_articulo DECIMAL(10,2) DEFAULT 0,
    precio_venta_articulo DECIMAL(10,2) DEFAULT 0,
    stock_minimo_articulo INT DEFAULT 0,
    stock_maximo_articulo INT DEFAULT 0,
    activo_articulo BOOLEAN DEFAULT TRUE,
    escompuesto_articulo TINYINT(1) NOT NULL DEFAULT 0 COMMENT 'Indica si el artículo está compuesto por otros artículos: 1=Sí, 0=No',
    solo_compra_venta_articulo INT DEFAULT 3 COMMENT '1: solo permite compra, 2: solo permite venta, 3: permite ambos (por defecto)',
    ubicacion_articulo VARCHAR(100) COMMENT 'Localización física del artículo, como almacén o estantería',
    observaciones_logistica_articulo TEXT COMMENT 'Observaciones relacionadas con logística, embalaje o transporte del artículo',
    fecha_caducidad_articulo DATE COMMENT 'Fecha de caducidad del artículo (especialmente útil para productos perecederos)',
    observaciones_articulo TEXT,
    created_at_proveedor_evaluacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor_evaluacion DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_marca_articulo) REFERENCES marca(id_marca),
    FOREIGN KEY (id_familia_articulo) REFERENCES familia(id_familia),
    FOREIGN KEY (id_unidad_medida_articulo) REFERENCES unidad_medida(id_unidad_medida),
    FOREIGN KEY (id_impuesto_articulo) REFERENCES impuesto(id_impuesto)
);

CREATE TABLE articulo_proveedor (
    id_articulo_proveedor INT AUTO_INCREMENT PRIMARY KEY,
    id_articulo INT NOT NULL,
    id_proveedor INT NOT NULL,
    codigo_proveedor_articulo VARCHAR(40) COMMENT 'Código que le da el proveedor al artículo',
    precio_compra_articulo_proveedor DECIMAL(10,2),
    condiciones_pago_articulo_proveedor VARCHAR(100),
    observaciones_articulo_proveedor TEXT,
    created_at_articulo_proveedor DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_articulo_proveedor DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_articulo) REFERENCES articulo(id_articulo),
    FOREIGN KEY (id_proveedor) REFERENCES proveedor(id_proveedor)
) COMMENT='Asociar cada artículo a uno o varios proveedores (relación muchos a muchos). Gestionar información adicional como precios de compra por proveedor, códigos de proveedor, condiciones de pago, y preferencias de compra.';


CREATE TABLE articulo_foto (
    id_articulo_foto INT AUTO_INCREMENT PRIMARY KEY,
    id_articulo INT NOT NULL,
    url_articulo_foto VARCHAR(255) NOT NULL COMMENT 'Ruta o URL de la imagen asociada al artículo',
    descripcion_articulo_foto VARCHAR(255) COMMENT 'Descripción breve de la foto del artículo',
    activo_articulo_foto BOOLEAN DEFAULT TRUE,
    created_at_articulo_foto DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_articulo_foto DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_articulo) REFERENCES articulo(id_articulo)
) COMMENT='Almacenar múltiples fotos por artículo, cada una identificada y relacionada al artículo correspondiente. Permite gestión y descripción de archivos multimedia para los artículos.';


CREATE TABLE articulocomponente (
    idarticuloprincipal_articulocomponente INT NOT NULL,
    idarticulocomponente_articulocomponente INT NOT NULL,
    cantidad_articulocomponente DECIMAL(10,2) NOT NULL DEFAULT 1,
    PRIMARY KEY (idarticuloprincipal_articulocomponente, idarticulocomponente_articulocomponente),
    FOREIGN KEY (idarticuloprincipal_articulocomponente) REFERENCES articulo(id_articulo),
    FOREIGN KEY (idarticulocomponente_articulocomponente) REFERENCES articulo(id_articulo)
) COMMENT = 'Tabla que almacena los componentes de los artículos que tienen escompuesto_articulo=1 en la tabla articulo.';
