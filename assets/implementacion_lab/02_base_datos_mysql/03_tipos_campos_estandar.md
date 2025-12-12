# Tipos de Campos Estándar

> Guía de tipos de datos MySQL recomendados según el uso

---

## Tipos de datos por categoría

### Identificadores

| Uso | Tipo recomendado | Ejemplo |
|-----|------------------|---------|
| ID principal | `INT AUTO_INCREMENT` | `id_cliente INT NOT NULL AUTO_INCREMENT` |
| ID para tablas masivas (+10M registros) | `BIGINT AUTO_INCREMENT` | `id_log BIGINT NOT NULL AUTO_INCREMENT` |
| FK a otra tabla | `INT` | `id_cliente INT NOT NULL` |

---

### Textos

| Uso | Tipo recomendado | Ejemplo |
|-----|------------------|---------|
| Nombre, apellido | `VARCHAR(100)` | `nombre_cliente VARCHAR(100) NOT NULL` |
| Email | `VARCHAR(150)` | `email_cliente VARCHAR(150) NULL` |
| Teléfono | `VARCHAR(20)` | `telefono_cliente VARCHAR(20) NULL` |
| Dirección corta | `VARCHAR(255)` | `direccion_cliente VARCHAR(255) NULL` |
| URL, ruta de archivo | `VARCHAR(255)` | `foto_cliente VARCHAR(255) NULL` |
| Código corto (SKU, ref) | `VARCHAR(50)` | `referencia_articulo VARCHAR(50) NOT NULL` |
| Descripción corta | `VARCHAR(500)` | `descripcion_producto VARCHAR(500) NULL` |
| Texto largo (observaciones) | `TEXT` | `observaciones_cliente TEXT NULL` |
| Contenido extenso (HTML, documentos) | `LONGTEXT` | `contenido_pagina LONGTEXT NULL` |

**Nota**: Usar `VARCHAR` siempre que se conozca un límite razonable. `TEXT` solo para contenido variable sin límite claro.

---

### Números

| Uso | Tipo recomendado | Ejemplo |
|-----|------------------|---------|
| Cantidades enteras | `INT` | `cantidad_articulo INT NOT NULL DEFAULT 0` |
| Cantidades pequeñas (0-255) | `TINYINT UNSIGNED` | `prioridad_tarea TINYINT UNSIGNED DEFAULT 0` |
| Porcentajes enteros | `TINYINT UNSIGNED` | `descuento_cliente TINYINT UNSIGNED DEFAULT 0` |
| Precios, importes | `DECIMAL(10,2)` | `precio_articulo DECIMAL(10,2) NOT NULL` |
| Importes grandes | `DECIMAL(15,2)` | `total_factura DECIMAL(15,2) NOT NULL` |
| Porcentajes decimales | `DECIMAL(5,2)` | `iva_factura DECIMAL(5,2) NOT NULL` |
| Coordenadas GPS | `DECIMAL(10,8)` | `latitud_cliente DECIMAL(10,8) NULL` |

**Importante**: NUNCA usar `FLOAT` o `DOUBLE` para dinero. Siempre `DECIMAL`.

---

### Fechas y tiempo

| Uso | Tipo recomendado | Ejemplo |
|-----|------------------|---------|
| Fecha sin hora | `DATE` | `fecha_nacimiento_cliente DATE NULL` |
| Fecha y hora | `DATETIME` | `fecha_cita_cliente DATETIME NOT NULL` |
| Timestamp automático | `TIMESTAMP` | `created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP` |
| Solo hora | `TIME` | `hora_apertura_tienda TIME NULL` |
| Año | `YEAR` | `anio_fabricacion_vehiculo YEAR NULL` |

**Diferencia DATETIME vs TIMESTAMP**:
- `TIMESTAMP`: Se ajusta a zona horaria del servidor, rango 1970-2038
- `DATETIME`: No se ajusta, rango 1000-9999

Usar `TIMESTAMP` para campos automáticos (created_at, updated_at).
Usar `DATETIME` para fechas ingresadas por usuario.

---

### Estados y flags

| Uso | Tipo recomendado | Ejemplo |
|-----|------------------|---------|
| Activo/Inactivo (booleano) | `TINYINT(1)` | `activo_cliente TINYINT(1) NOT NULL DEFAULT 1` |
| Estado con opciones limitadas | `ENUM` | `estado_pedido ENUM('pendiente','procesando','enviado','entregado','cancelado')` |
| Tipo con opciones fijas | `ENUM` | `tipo_documento ENUM('dni','nie','pasaporte','cif')` |

**Nota sobre ENUM**: Usar solo cuando las opciones son estables y no cambiarán. Si pueden cambiar, mejor crear tabla auxiliar con FK.

---

### Archivos e imágenes

| Uso | Tipo recomendado | Ejemplo |
|-----|------------------|---------|
| Ruta de archivo | `VARCHAR(255)` | `documento_cliente VARCHAR(255) NULL` |
| Nombre de archivo | `VARCHAR(100)` | `nombre_archivo_adjunto VARCHAR(100) NULL` |
| Tipo MIME | `VARCHAR(50)` | `tipo_archivo_adjunto VARCHAR(50) NULL` |
| Tamaño en bytes | `INT UNSIGNED` | `tamano_archivo_adjunto INT UNSIGNED NULL` |

**Recomendación**: No almacenar archivos binarios (BLOB) en la base de datos. Guardar en disco y almacenar solo la ruta.

---

### JSON y datos estructurados

| Uso | Tipo recomendado | Ejemplo |
|-----|------------------|---------|
| Configuración flexible | `JSON` | `configuracion_usuario JSON NULL` |
| Datos variables | `JSON` | `metadata_articulo JSON NULL` |

**Nota**: `JSON` disponible desde MySQL 5.7. Permite consultas sobre el contenido con operadores específicos.

---

## Atributos comunes

| Atributo | Uso |
|----------|-----|
| `NOT NULL` | Campo obligatorio |
| `NULL` | Campo opcional (explícito para claridad) |
| `DEFAULT valor` | Valor por defecto si no se especifica |
| `UNSIGNED` | Solo valores positivos (duplica el rango máximo) |
| `AUTO_INCREMENT` | Incremento automático (solo para PKs) |

---

## Ejemplo completo: Tabla artículo

```sql
CREATE TABLE articulo (
    -- Identificador
    id_articulo INT NOT NULL AUTO_INCREMENT,
    
    -- Textos
    nombre_articulo VARCHAR(150) NOT NULL,
    descripcion_articulo TEXT NULL,
    referencia_articulo VARCHAR(50) NOT NULL,
    
    -- Números
    precio_articulo DECIMAL(10,2) NOT NULL,
    stock_articulo INT NOT NULL DEFAULT 0,
    
    -- Relaciones
    id_categoria INT NOT NULL,
    id_proveedor INT NULL,
    
    -- Archivos
    foto_articulo VARCHAR(255) NULL,
    
    -- Estados
    destacado_articulo TINYINT(1) NOT NULL DEFAULT 0,
    
    -- Campos obligatorios
    activo_articulo TINYINT(1) NOT NULL DEFAULT 1,
    created_at_articulo TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_articulo TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_articulo),
    KEY idx_categoria (id_categoria),
    KEY idx_referencia (referencia_articulo),
    
    -- Foreign Keys
    CONSTRAINT fk_articulo_categoria FOREIGN KEY (id_categoria) 
        REFERENCES categoria(id_categoria) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_articulo_proveedor FOREIGN KEY (id_proveedor) 
        REFERENCES proveedor(id_proveedor) ON DELETE SET NULL ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

*Documento: 02-03 | Última actualización: Diciembre 2024*
