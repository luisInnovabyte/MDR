# Campos Obligatorios Estándar

> Campos que SIEMPRE deben incluirse en todas las tablas del sistema

---

## Resumen de campos obligatorios

| Campo | Tipo | Atributos | Valor por defecto |
|-------|------|-----------|-------------------|
| `id_<<tabla>>` | `INT` | `AUTO_INCREMENT`, `NOT NULL`, `PRIMARY KEY` | - |
| `activo_<<tabla>>` | `TINYINT(1)` | `NOT NULL` | `1` |
| `created_at_<<tabla>>` | `TIMESTAMP` | `NOT NULL` | `CURRENT_TIMESTAMP` |
| `updated_at_<<tabla>>` | `TIMESTAMP` | `NOT NULL` | `CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP` |

---

## Detalle de cada campo

### 1. Campo ID (Identificador único)

```sql
id_<<tabla>> INT NOT NULL AUTO_INCREMENT PRIMARY KEY
```

**Propósito**: Identificador único de cada registro.

**Características**:
- Tipo `INT` (o `BIGINT` para tablas con millones de registros previstos)
- `AUTO_INCREMENT` para generación automática
- `NOT NULL` obligatorio
- `PRIMARY KEY` como índice principal

**Ejemplo para tabla `cliente`**:
```sql
id_cliente INT NOT NULL AUTO_INCREMENT PRIMARY KEY
```

---

### 2. Campo Activo (Soft Delete)

```sql
activo_<<tabla>> TINYINT(1) NOT NULL DEFAULT 1
```

**Propósito**: Permite "eliminar" registros sin borrarlos físicamente (soft delete).

**Valores**:
- `1` = Registro activo (visible en el sistema)
- `0` = Registro inactivo (oculto pero conservado)

**Características**:
- Tipo `TINYINT(1)` para optimizar espacio
- Valor por defecto `1` (activo al crear)
- Nunca se hace `DELETE`, se hace `UPDATE activo_<<tabla>> = 0`

**Ejemplo para tabla `cliente`**:
```sql
activo_cliente TINYINT(1) NOT NULL DEFAULT 1
```

**Uso en consultas**:
```sql
-- Listar solo registros activos
SELECT * FROM cliente WHERE activo_cliente = 1;

-- "Eliminar" un registro
UPDATE cliente SET activo_cliente = 0 WHERE id_cliente = 5;

-- Recuperar un registro eliminado
UPDATE cliente SET activo_cliente = 1 WHERE id_cliente = 5;
```

---

### 3. Campo Created At (Fecha de creación)

```sql
created_at_<<tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
```

**Propósito**: Registra automáticamente cuándo se creó el registro.

**Características**:
- Tipo `TIMESTAMP` para almacenar fecha y hora
- `DEFAULT CURRENT_TIMESTAMP` asigna la fecha/hora actual automáticamente al insertar
- No se modifica nunca después de la creación

**Ejemplo para tabla `cliente`**:
```sql
created_at_cliente TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
```

---

### 4. Campo Updated At (Fecha de modificación)

```sql
updated_at_<<tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

**Propósito**: Registra automáticamente cuándo se modificó el registro por última vez.

**Características**:
- Tipo `TIMESTAMP`
- `DEFAULT CURRENT_TIMESTAMP` para valor inicial
- `ON UPDATE CURRENT_TIMESTAMP` actualiza automáticamente en cada modificación
- No requiere intervención manual en el código PHP

**Ejemplo para tabla `cliente`**:
```sql
updated_at_cliente TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

---

## Plantilla SQL completa

Para crear cualquier tabla nueva, usar esta estructura base:

```sql
CREATE TABLE <<nombre_tabla>> (
    -- Identificador único
    id_<<nombre_tabla>> INT NOT NULL AUTO_INCREMENT,
    
    -- ========================================
    -- CAMPOS ESPECÍFICOS DE LA TABLA AQUÍ
    -- ========================================
    
    -- Campos de control (siempre al final)
    activo_<<nombre_tabla>> TINYINT(1) NOT NULL DEFAULT 1,
    created_at_<<nombre_tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_<<nombre_tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_<<nombre_tabla>>)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Ejemplo completo: Tabla cliente

```sql
CREATE TABLE cliente (
    -- Identificador único
    id_cliente INT NOT NULL AUTO_INCREMENT,
    
    -- Campos específicos
    nombre_cliente VARCHAR(100) NOT NULL,
    apellido_cliente VARCHAR(100) NULL,
    email_cliente VARCHAR(150) NULL,
    telefono_cliente VARCHAR(20) NULL,
    direccion_cliente VARCHAR(255) NULL,
    foto_cliente VARCHAR(255) NULL,
    
    -- Campos de control
    activo_cliente TINYINT(1) NOT NULL DEFAULT 1,
    created_at_cliente TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    PRIMARY KEY (id_cliente)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

*Documento: 02-02 | Última actualización: Diciembre 2024*
