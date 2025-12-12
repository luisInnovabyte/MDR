# Índices y Claves Foráneas

> Estándar para índices, claves primarias y relaciones entre tablas

---

## Índices obligatorios

### Clave primaria (PRIMARY KEY)

Toda tabla DEBE tener una clave primaria en el campo `id_<<tabla>>`.

```sql
PRIMARY KEY (id_cliente)
```

**Características**:
- Siempre sobre el campo `id_<<tabla>>`
- Automáticamente crea un índice único
- No permite valores NULL ni duplicados

---

## Índices recomendados

### Campos que DEBEN indexarse

| Tipo de campo | Motivo | Ejemplo |
|---------------|--------|---------|
| Claves foráneas (FK) | Optimiza JOINs | `KEY idx_id_categoria (id_categoria)` |
| Campos de búsqueda frecuente | Optimiza WHERE | `KEY idx_email (email_cliente)` |
| Campos de ordenación frecuente | Optimiza ORDER BY | `KEY idx_fecha (fecha_pedido)` |
| Campos únicos (email, referencia) | Evita duplicados | `UNIQUE KEY uk_email (email_cliente)` |

### Campos que NO necesitan índice

- Campos de texto largo (`TEXT`, `LONGTEXT`)
- Campos booleanos con poca selectividad (`activo_<<tabla>>`)
- Campos que raramente se usan en WHERE o JOIN

---

## Nomenclatura de índices

| Tipo | Prefijo | Formato | Ejemplo |
|------|---------|---------|---------|
| Índice simple | `idx_` | `idx_<<campo>>` | `idx_email_cliente` |
| Índice único | `uk_` | `uk_<<campo>>` | `uk_referencia_articulo` |
| Índice compuesto | `idx_` | `idx_<<campo1>>_<<campo2>>` | `idx_fecha_estado` |
| Clave foránea | `fk_` | `fk_<<tabla_origen>>_<<tabla_destino>>` | `fk_pedido_cliente` |

---

## Claves foráneas (Foreign Keys)

### Regla general

**Siempre** crear FK cuando una tabla referencia a otra.

### Sintaxis estándar

```sql
CONSTRAINT fk_<<tabla_origen>>_<<tabla_destino>> 
    FOREIGN KEY (id_<<tabla_destino>>) 
    REFERENCES <<tabla_destino>>(id_<<tabla_destino>>) 
    ON DELETE <<accion>> 
    ON UPDATE CASCADE
```

### Acciones ON DELETE

| Acción | Uso | Ejemplo |
|--------|-----|---------|
| `RESTRICT` | Impide borrar si hay registros relacionados | Cliente con pedidos |
| `CASCADE` | Borra en cascada los registros relacionados | Líneas de un pedido al borrar pedido |
| `SET NULL` | Pone NULL en la FK (campo debe permitir NULL) | Artículo sin proveedor si se borra proveedor |

### Acciones ON UPDATE

| Acción | Uso |
|--------|-----|
| `CASCADE` | **Siempre usar**. Si cambia el ID origen, actualiza las referencias |

---

## Ejemplos prácticos

### Relación 1:N (Uno a muchos)

**Escenario**: Un cliente tiene muchos pedidos

```sql
-- Tabla cliente (lado 1)
CREATE TABLE cliente (
    id_cliente INT NOT NULL AUTO_INCREMENT,
    nombre_cliente VARCHAR(100) NOT NULL,
    -- ... otros campos ...
    PRIMARY KEY (id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Tabla pedido (lado N)
CREATE TABLE pedido (
    id_pedido INT NOT NULL AUTO_INCREMENT,
    fecha_pedido DATETIME NOT NULL,
    id_cliente INT NOT NULL,
    -- ... otros campos ...
    PRIMARY KEY (id_pedido),
    KEY idx_id_cliente (id_cliente),
    CONSTRAINT fk_pedido_cliente 
        FOREIGN KEY (id_cliente) 
        REFERENCES cliente(id_cliente) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

### Relación 1:N con borrado en cascada

**Escenario**: Un pedido tiene muchas líneas. Si se borra el pedido, se borran sus líneas.

```sql
-- Tabla linea_pedido
CREATE TABLE linea_pedido (
    id_linea_pedido INT NOT NULL AUTO_INCREMENT,
    id_pedido INT NOT NULL,
    id_articulo INT NOT NULL,
    cantidad_linea_pedido INT NOT NULL,
    precio_linea_pedido DECIMAL(10,2) NOT NULL,
    -- ... campos obligatorios ...
    PRIMARY KEY (id_linea_pedido),
    KEY idx_id_pedido (id_pedido),
    KEY idx_id_articulo (id_articulo),
    CONSTRAINT fk_linea_pedido_pedido 
        FOREIGN KEY (id_pedido) 
        REFERENCES pedido(id_pedido) 
        ON DELETE CASCADE 
        ON UPDATE CASCADE,
    CONSTRAINT fk_linea_pedido_articulo 
        FOREIGN KEY (id_articulo) 
        REFERENCES articulo(id_articulo) 
        ON DELETE RESTRICT 
        ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

### Relación N:M (Muchos a muchos)

**Escenario**: Un artículo puede tener muchas etiquetas. Una etiqueta puede estar en muchos artículos.

```sql
-- Tabla pivote articulo_etiqueta
CREATE TABLE articulo_etiqueta (
    id_articulo_etiqueta INT NOT NULL AUTO_INCREMENT,
    id_articulo INT NOT NULL,
    id_etiqueta INT NOT NULL,
    -- Campos obligatorios
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

**Nota**: El índice `UNIQUE` en la combinación evita duplicados en la relación.

---

### FK opcional (permite NULL)

**Escenario**: Un artículo puede tener proveedor o no.

```sql
id_proveedor INT NULL,
-- ...
CONSTRAINT fk_articulo_proveedor 
    FOREIGN KEY (id_proveedor) 
    REFERENCES proveedor(id_proveedor) 
    ON DELETE SET NULL 
    ON UPDATE CASCADE
```

---

## Guía de decisión ON DELETE

```
¿La relación es obligatoria? (FK es NOT NULL)
├── SÍ → ¿Se puede borrar el padre si tiene hijos?
│       ├── NO → ON DELETE RESTRICT (cliente con pedidos)
│       └── SÍ → ON DELETE CASCADE (líneas de pedido)
└── NO → ON DELETE SET NULL (artículo sin proveedor)
```

---

## Verificar integridad

### Ver todas las FK de una tabla

```sql
SELECT 
    CONSTRAINT_NAME,
    COLUMN_NAME,
    REFERENCED_TABLE_NAME,
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'nombre_bd' 
AND TABLE_NAME = 'nombre_tabla'
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

### Ver todos los índices de una tabla

```sql
SHOW INDEX FROM nombre_tabla;
```

---

*Documento: 02-04 | Última actualización: Diciembre 2024*
