# Plantilla SQL para Nuevas Tablas

> Plantilla base para crear cualquier tabla siguiendo los estándares definidos

---

## Plantilla básica

```sql
-- ============================================
-- Tabla: <<nombre_tabla>>
-- Descripción: <<descripción breve>>
-- Fecha: <<fecha_creación>>
-- ============================================

CREATE TABLE <<nombre_tabla>> (
    -- ----------------------------------------
    -- Identificador único
    -- ----------------------------------------
    id_<<nombre_tabla>> INT NOT NULL AUTO_INCREMENT,
    
    -- ----------------------------------------
    -- Campos específicos de la tabla
    -- ----------------------------------------
    -- <<campo1>>_<<nombre_tabla>> TIPO [NOT NULL|NULL] [DEFAULT valor],
    -- <<campo2>>_<<nombre_tabla>> TIPO [NOT NULL|NULL] [DEFAULT valor],
    
    -- ----------------------------------------
    -- Claves foráneas (campos)
    -- ----------------------------------------
    -- id_<<tabla_relacionada>> INT [NOT NULL|NULL],
    
    -- ----------------------------------------
    -- Campos de control (obligatorios)
    -- ----------------------------------------
    activo_<<nombre_tabla>> TINYINT(1) NOT NULL DEFAULT 1,
    created_at_<<nombre_tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_<<nombre_tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- ----------------------------------------
    -- Índices
    -- ----------------------------------------
    PRIMARY KEY (id_<<nombre_tabla>>)
    -- ,KEY idx_<<campo>> (<<campo>>_<<nombre_tabla>>)
    -- ,UNIQUE KEY uk_<<campo>> (<<campo>>_<<nombre_tabla>>)
    -- ,KEY idx_fk_<<tabla_relacionada>> (id_<<tabla_relacionada>>)
    
    -- ----------------------------------------
    -- Foreign Keys
    -- ----------------------------------------
    -- ,CONSTRAINT fk_<<nombre_tabla>>_<<tabla_relacionada>> 
    --     FOREIGN KEY (id_<<tabla_relacionada>>) 
    --     REFERENCES <<tabla_relacionada>>(id_<<tabla_relacionada>>) 
    --     ON DELETE RESTRICT 
    --     ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Ejemplo 1: Tabla simple (catálogo)

```sql
-- ============================================
-- Tabla: categoria
-- Descripción: Categorías de artículos
-- Fecha: 2024-12-01
-- ============================================

CREATE TABLE categoria (
    -- Identificador único
    id_categoria INT NOT NULL AUTO_INCREMENT,
    
    -- Campos específicos
    nombre_categoria VARCHAR(100) NOT NULL,
    descripcion_categoria VARCHAR(500) NULL,
    orden_categoria INT NOT NULL DEFAULT 0,
    
    -- Campos de control
    activo_categoria TINYINT(1) NOT NULL DEFAULT 1,
    created_at_categoria TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_categoria TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_categoria)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Ejemplo 2: Tabla con FK obligatoria

```sql
-- ============================================
-- Tabla: articulo
-- Descripción: Catálogo de artículos
-- Fecha: 2024-12-01
-- ============================================

CREATE TABLE articulo (
    -- Identificador único
    id_articulo INT NOT NULL AUTO_INCREMENT,
    
    -- Campos específicos
    nombre_articulo VARCHAR(150) NOT NULL,
    descripcion_articulo TEXT NULL,
    referencia_articulo VARCHAR(50) NOT NULL,
    precio_articulo DECIMAL(10,2) NOT NULL,
    stock_articulo INT NOT NULL DEFAULT 0,
    foto_articulo VARCHAR(255) NULL,
    
    -- Claves foráneas
    id_categoria INT NOT NULL,
    
    -- Campos de control
    activo_articulo TINYINT(1) NOT NULL DEFAULT 1,
    created_at_articulo TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_articulo TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_articulo),
    UNIQUE KEY uk_referencia (referencia_articulo),
    KEY idx_id_categoria (id_categoria),
    
    -- Foreign Keys
    CONSTRAINT fk_articulo_categoria 
        FOREIGN KEY (id_categoria) 
        REFERENCES categoria(id_categoria) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Ejemplo 3: Tabla con FK opcional

```sql
-- ============================================
-- Tabla: cliente
-- Descripción: Clientes del sistema
-- Fecha: 2024-12-01
-- ============================================

CREATE TABLE cliente (
    -- Identificador único
    id_cliente INT NOT NULL AUTO_INCREMENT,
    
    -- Campos específicos
    nombre_cliente VARCHAR(100) NOT NULL,
    apellido_cliente VARCHAR(100) NULL,
    email_cliente VARCHAR(150) NULL,
    telefono_cliente VARCHAR(20) NULL,
    direccion_cliente VARCHAR(255) NULL,
    nif_cliente VARCHAR(15) NULL,
    
    -- Claves foráneas (opcional)
    id_comercial INT NULL,
    
    -- Campos de control
    activo_cliente TINYINT(1) NOT NULL DEFAULT 1,
    created_at_cliente TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_cliente),
    UNIQUE KEY uk_email (email_cliente),
    KEY idx_id_comercial (id_comercial),
    
    -- Foreign Keys
    CONSTRAINT fk_cliente_comercial 
        FOREIGN KEY (id_comercial) 
        REFERENCES comercial(id_comercial) 
        ON DELETE SET NULL 
        ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Ejemplo 4: Tabla cabecera-líneas (1:N con CASCADE)

```sql
-- ============================================
-- Tabla: pedido (cabecera)
-- Descripción: Cabecera de pedidos
-- Fecha: 2024-12-01
-- ============================================

CREATE TABLE pedido (
    -- Identificador único
    id_pedido INT NOT NULL AUTO_INCREMENT,
    
    -- Campos específicos
    numero_pedido VARCHAR(20) NOT NULL,
    fecha_pedido DATETIME NOT NULL,
    observaciones_pedido TEXT NULL,
    total_pedido DECIMAL(15,2) NOT NULL DEFAULT 0.00,
    estado_pedido ENUM('borrador','confirmado','enviado','entregado','cancelado') NOT NULL DEFAULT 'borrador',
    
    -- Claves foráneas
    id_cliente INT NOT NULL,
    
    -- Campos de control
    activo_pedido TINYINT(1) NOT NULL DEFAULT 1,
    created_at_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_pedido),
    UNIQUE KEY uk_numero (numero_pedido),
    KEY idx_fecha (fecha_pedido),
    KEY idx_id_cliente (id_cliente),
    
    -- Foreign Keys
    CONSTRAINT fk_pedido_cliente 
        FOREIGN KEY (id_cliente) 
        REFERENCES cliente(id_cliente) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- ============================================
-- Tabla: linea_pedido (detalle)
-- Descripción: Líneas de detalle de pedidos
-- Fecha: 2024-12-01
-- ============================================

CREATE TABLE linea_pedido (
    -- Identificador único
    id_linea_pedido INT NOT NULL AUTO_INCREMENT,
    
    -- Campos específicos
    cantidad_linea_pedido INT NOT NULL DEFAULT 1,
    precio_unitario_linea_pedido DECIMAL(10,2) NOT NULL,
    descuento_linea_pedido DECIMAL(5,2) NOT NULL DEFAULT 0.00,
    total_linea_pedido DECIMAL(10,2) NOT NULL,
    
    -- Claves foráneas
    id_pedido INT NOT NULL,
    id_articulo INT NOT NULL,
    
    -- Campos de control
    activo_linea_pedido TINYINT(1) NOT NULL DEFAULT 1,
    created_at_linea_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_linea_pedido TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_linea_pedido),
    KEY idx_id_pedido (id_pedido),
    KEY idx_id_articulo (id_articulo),
    
    -- Foreign Keys
    CONSTRAINT fk_lineapedido_pedido 
        FOREIGN KEY (id_pedido) 
        REFERENCES pedido(id_pedido) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_lineapedido_articulo 
        FOREIGN KEY (id_articulo) 
        REFERENCES articulo(id_articulo) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Ejemplo 5: Tabla pivote N:M

```sql
-- ============================================
-- Tabla: articulo_etiqueta (pivote N:M)
-- Descripción: Relación muchos a muchos entre artículos y etiquetas
-- Fecha: 2024-12-01
-- ============================================

CREATE TABLE articulo_etiqueta (
    -- Identificador único
    id_articulo_etiqueta INT NOT NULL AUTO_INCREMENT,
    
    -- Claves foráneas (componen la relación)
    id_articulo INT NOT NULL,
    id_etiqueta INT NOT NULL,
    
    -- Campos adicionales de la relación (opcional)
    orden_articulo_etiqueta INT NOT NULL DEFAULT 0,
    
    -- Campos de control
    activo_articulo_etiqueta TINYINT(1) NOT NULL DEFAULT 1,
    created_at_articulo_etiqueta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_articulo_etiqueta TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_articulo_etiqueta),
    UNIQUE KEY uk_articulo_etiqueta (id_articulo, id_etiqueta),
    KEY idx_id_articulo (id_articulo),
    KEY idx_id_etiqueta (id_etiqueta),
    
    -- Foreign Keys
    CONSTRAINT fk_articuloetiqueta_articulo 
        FOREIGN KEY (id_articulo) 
        REFERENCES articulo(id_articulo) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_articuloetiqueta_etiqueta 
        FOREIGN KEY (id_etiqueta) 
        REFERENCES etiqueta(id_etiqueta) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Checklist al crear una tabla

- [ ] Nombre en singular y snake_case
- [ ] Todos los campos terminan en `_<<nombre_tabla>>`
- [ ] Campo `id_<<tabla>>` como PRIMARY KEY AUTO_INCREMENT
- [ ] Campo `activo_<<tabla>>` con DEFAULT 1
- [ ] Campo `created_at_<<tabla>>` con DEFAULT CURRENT_TIMESTAMP
- [ ] Campo `updated_at_<<tabla>>` con ON UPDATE CURRENT_TIMESTAMP
- [ ] Índices en campos FK
- [ ] Índices en campos de búsqueda frecuente
- [ ] UNIQUE en campos que no deben repetirse
- [ ] FK definidas con ON DELETE y ON UPDATE apropiados
- [ ] ENGINE=InnoDB
- [ ] CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci

---

*Documento: 02-06 | Última actualización: Diciembre 2024*
