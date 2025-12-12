# Configuración de Charset y Collation

> Estándar de codificación de caracteres para soporte completo del español

---

## Configuración estándar

| Parámetro | Valor |
|-----------|-------|
| **Charset** | `utf8mb4` |
| **Collation** | `utf8mb4_spanish_ci` |
| **Engine** | `InnoDB` |

---

## ¿Por qué utf8mb4?

`utf8mb4` es el charset recomendado porque:

- Soporta **todos los caracteres Unicode**, incluyendo emojis
- Es el verdadero UTF-8 (4 bytes por carácter máximo)
- El antiguo `utf8` de MySQL solo soporta 3 bytes (BMP)
- Compatible con caracteres especiales españoles (ñ, á, é, í, ó, ú, ü)

---

## ¿Por qué utf8mb4_spanish_ci?

El collation `utf8mb4_spanish_ci` está optimizado para español:

| Característica | Comportamiento |
|----------------|----------------|
| **Case insensitive** (`ci`) | `García` = `garcía` = `GARCÍA` |
| **Ordenación española** | `ch` y `ll` como letras separadas (tradicional) |
| **Acentos equivalentes** | `cafe` = `café` en búsquedas |
| **Ñ correcta** | La ñ se ordena después de la n |

### Comparación de collations

```sql
-- Con utf8mb4_spanish_ci (correcto para español)
SELECT * FROM cliente ORDER BY nombre_cliente;
-- Resultado: Ana, Ángel, Beatriz, Carlos, Ñoño, Óscar

-- Con utf8mb4_general_ci (incorrecto)
-- Resultado: Ana, Beatriz, Carlos, Óscar, Ángel, Ñoño
```

---

## Sintaxis en CREATE TABLE

```sql
CREATE TABLE nombre_tabla (
    -- campos...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Configuración a nivel de base de datos

Al crear la base de datos:

```sql
CREATE DATABASE nombre_bd 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_spanish_ci;
```

---

## Configuración a nivel de servidor (my.cnf)

Para que todas las nuevas bases de datos usen esta configuración por defecto:

```ini
[mysqld]
character-set-server = utf8mb4
collation-server = utf8mb4_spanish_ci

[client]
default-character-set = utf8mb4

[mysql]
default-character-set = utf8mb4
```

---

## Configuración de conexión PHP

### Con PDO

```php
$dsn = "mysql:host=localhost;dbname=nombre_bd;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_spanish_ci"
];
$pdo = new PDO($dsn, $usuario, $password, $options);
```

### Con mysqli

```php
$mysqli = new mysqli($host, $usuario, $password, $nombre_bd);
$mysqli->set_charset("utf8mb4");
$mysqli->query("SET NAMES utf8mb4 COLLATE utf8mb4_spanish_ci");
```

---

## Verificar configuración actual

### De la base de datos

```sql
SELECT DEFAULT_CHARACTER_SET_NAME, DEFAULT_COLLATION_NAME 
FROM information_schema.SCHEMATA 
WHERE SCHEMA_NAME = 'nombre_bd';
```

### De una tabla específica

```sql
SELECT TABLE_NAME, TABLE_COLLATION 
FROM information_schema.TABLES 
WHERE TABLE_SCHEMA = 'nombre_bd';
```

### De columnas específicas

```sql
SELECT COLUMN_NAME, CHARACTER_SET_NAME, COLLATION_NAME 
FROM information_schema.COLUMNS 
WHERE TABLE_SCHEMA = 'nombre_bd' 
AND TABLE_NAME = 'nombre_tabla';
```

---

## Migrar tablas existentes

Si tienes tablas con otra codificación:

```sql
-- Cambiar charset y collation de una tabla
ALTER TABLE nombre_tabla 
    CONVERT TO CHARACTER SET utf8mb4 
    COLLATE utf8mb4_spanish_ci;

-- Cambiar solo una columna
ALTER TABLE nombre_tabla 
    MODIFY nombre_columna VARCHAR(100) 
    CHARACTER SET utf8mb4 
    COLLATE utf8mb4_spanish_ci;
```

**Precaución**: En tablas grandes, esta operación puede tardar y bloquear la tabla.

---

## Collations alternativos

Si necesitas comportamiento diferente:

| Collation | Uso |
|-----------|-----|
| `utf8mb4_spanish_ci` | **Recomendado** - Español tradicional |
| `utf8mb4_spanish2_ci` | Español moderno (ch/ll no son letras separadas) |
| `utf8mb4_unicode_ci` | Multiidioma genérico |
| `utf8mb4_bin` | Comparación binaria exacta (case sensitive) |

---

## Problemas comunes

### Error "Incorrect string value"

**Causa**: Intentar insertar caracteres de 4 bytes (emojis) en columna `utf8` (3 bytes).

**Solución**: Convertir a `utf8mb4`.

### Comparaciones case-sensitive inesperadas

**Causa**: Collation `_bin` o `_cs` en la columna.

**Solución**: Verificar y cambiar a `utf8mb4_spanish_ci`.

### Ordenación incorrecta de caracteres españoles

**Causa**: Usar `utf8mb4_general_ci` en lugar de `utf8mb4_spanish_ci`.

**Solución**: Cambiar collation de la tabla/columna.

---

*Documento: 02-05 | Última actualización: Diciembre 2024*
