# Spec — Generación de Models PHP (MDR)

> Referencia: `docs/controller-models/models.md` + `docs/controller-models/prompt_models.md`  
> Patrón: MVC estricto — PHP puro, PDO, sin ORMs

---

## 1. Qué es un Model en MDR

Los models son clases PHP que encapsulan **todo el acceso a datos**.  
El modelo **solo gestiona datos**, nunca lógica de negocio ni presentación.

### Nomenclatura

| Tabla BD | Modelo |
|---------|--------|
| `presupuesto` | `models/Presupuesto.php` |
| `cliente` | `models/Cliente.php` |
| `articulo` | `models/Articulo.php` |

Clase en **PascalCase**, fichero en `models/[Entidad].php`.

---

## 2. Estructura de fichero obligatoria

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class [Entidad]
{
    private $conexion;
    private $registro;

    public function __construct() { ... }

    // Métodos estándar (obligatorios)
    public function get_[entidades]() { ... }
    public function get_[entidades]_disponibles() { ... }
    public function get_[entidad]xid($id_[entidad]) { ... }
    public function insert_[entidad](...) { ... }
    public function update_[entidad]($id_[entidad], ...) { ... }
    public function delete_[entidad]xid($id_[entidad]) { ... }
    public function activar_[entidad]xid($id_[entidad]) { ... }
    public function verificar[Entidad]($campo_unico, $id_[entidad] = null) { ... }
}
?>
```

---

## 3. Métodos estándar — implementación completa

### `__construct()`

```php
public function __construct()
{
    $this->conexion = (new Conexion())->getConexion();
    $this->registro = new RegistroActividad();

    try {
        $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'system', '[Entidad]', '__construct',
            "Error zona horaria: " . $e->getMessage(), 'warning'
        );
    }
}
```

### `get_[entidades]()` — Listar todos

```php
public function get_[entidades]()
{
    try {
        // Usar vista SQL si la entidad tiene >3 relaciones
        $sql = "SELECT * FROM vista_[entidad]_completa ORDER BY nombre_[entidad] ASC";
        // Si no hay vista:
        // $sql = "SELECT * FROM [entidad] ORDER BY nombre_[entidad] ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', '[Entidad]', 'get_[entidades]',
                                             "Error: " . $e->getMessage(), 'error');
        return [];
    }
}
```

### `get_[entidades]_disponibles()` — Solo activos

```php
public function get_[entidades]_disponibles()
{
    try {
        $sql = "SELECT * FROM [entidad] WHERE activo_[entidad] = 1 ORDER BY nombre_[entidad] ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', '[Entidad]', 'get_[entidades]_disponibles',
                                             "Error: " . $e->getMessage(), 'error');
        return [];
    }
}
```

### `get_[entidad]xid($id)` — Por ID

```php
public function get_[entidad]xid($id_[entidad])
{
    try {
        $sql = "SELECT * FROM vista_[entidad]_completa WHERE id_[entidad] = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_[entidad], PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', '[Entidad]', 'get_[entidad]xid',
                                             "Error: " . $e->getMessage(), 'error');
        return false;
    }
}
```

### `insert_[entidad](...)` — Insertar

```php
public function insert_[entidad]($nombre_[entidad], $campo_opt = null, $id_fk = null)
{
    try {
        $sql = "INSERT INTO [entidad] (nombre_[entidad], campo_opt_[entidad], id_fk,
                                       activo_[entidad], created_at_[entidad])
                VALUES (?, ?, ?, 1, NOW())";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $nombre_[entidad], PDO::PARAM_STR);
        $stmt->bindValue(2, $campo_opt,  !empty($campo_opt) ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(3, $id_fk,      !empty($id_fk)     ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->execute();

        $id = $this->conexion->lastInsertId();
        $this->registro->registrarActividad('admin', '[Entidad]', 'insert_[entidad]',
                                             "[Entidad] creada con ID: $id", 'info');
        return $id;
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', '[Entidad]', 'insert_[entidad]',
                                             "Error: " . $e->getMessage(), 'error');
        return false;
    }
}
```

### `update_[entidad]($id, ...)` — Actualizar

```php
public function update_[entidad]($id_[entidad], $nombre_[entidad], $campo_opt = null, $id_fk = null)
{
    try {
        $sql = "UPDATE [entidad]
                SET nombre_[entidad] = ?, campo_opt_[entidad] = ?, id_fk = ?,
                    updated_at_[entidad] = NOW()
                WHERE id_[entidad] = ?";

        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $nombre_[entidad], PDO::PARAM_STR);
        $stmt->bindValue(2, $campo_opt,  !empty($campo_opt) ? PDO::PARAM_STR : PDO::PARAM_NULL);
        $stmt->bindValue(3, $id_fk,      !empty($id_fk)     ? PDO::PARAM_INT : PDO::PARAM_NULL);
        $stmt->bindValue(4, $id_[entidad], PDO::PARAM_INT);
        $stmt->execute();

        $this->registro->registrarActividad('admin', '[Entidad]', 'update_[entidad]',
                                             "[Entidad] actualizada ID: $id_[entidad]", 'info');
        return $stmt->rowCount();
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', '[Entidad]', 'update_[entidad]',
                                             "Error: " . $e->getMessage(), 'error');
        return false;
    }
}
```

### `delete_[entidad]xid($id)` — Soft delete

```php
public function delete_[entidad]xid($id_[entidad])
{
    try {
        $sql = "UPDATE [entidad]
                SET activo_[entidad] = 0, updated_at_[entidad] = NOW()
                WHERE id_[entidad] = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_[entidad], PDO::PARAM_INT);
        $stmt->execute();

        $this->registro->registrarActividad('admin', '[Entidad]', 'delete_[entidad]xid',
                                             "[Entidad] desactivada ID: $id_[entidad]", 'info');
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', '[Entidad]', 'delete_[entidad]xid',
                                             "Error: " . $e->getMessage(), 'error');
        return false;
    }
}
```

### `activar_[entidad]xid($id)` — Reactivar

```php
public function activar_[entidad]xid($id_[entidad])
{
    try {
        $sql = "UPDATE [entidad]
                SET activo_[entidad] = 1, updated_at_[entidad] = NOW()
                WHERE id_[entidad] = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_[entidad], PDO::PARAM_INT);
        $stmt->execute();

        $this->registro->registrarActividad('admin', '[Entidad]', 'activar_[entidad]xid',
                                             "[Entidad] activada ID: $id_[entidad]", 'info');
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', '[Entidad]', 'activar_[entidad]xid',
                                             "Error: " . $e->getMessage(), 'error');
        return false;
    }
}
```

### `verificar[Entidad]($campo, $id)` — Validar unicidad

```php
public function verificar[Entidad]($campo_unico, $id_[entidad] = null)
{
    try {
        $sql    = "SELECT COUNT(*) AS total FROM [entidad]
                   WHERE LOWER(campo_unico_[entidad]) = LOWER(?)";
        $params = [trim($campo_unico)];

        if (!empty($id_[entidad])) {
            $sql   .= " AND id_[entidad] != ?";
            $params[] = $id_[entidad];
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        $row  = $stmt->fetch(PDO::FETCH_ASSOC);

        return ['existe' => ($row['total'] > 0)];
    } catch (PDOException $e) {
        return ['existe' => false, 'error' => $e->getMessage()];
    }
}
```

---

## 4. Cuándo usar Vista SQL vs Tabla directa

| Situación | Usar |
|-----------|------|
| SELECT con >3 JOINs o campos calculados | `vista_[entidad]_completa` |
| Entidad simple, pocas relaciones | Tabla directa |
| INSERT / UPDATE / DELETE | **Siempre tabla directa** — las vistas no soportan escritura |

### Crear la vista

Criterios para crearla:
- La entidad tiene más de 3 tablas relacionadas (JOINs)
- Se necesitan campos calculados (diferencias de fechas, concatenaciones, CASE)
- Las mismas columnas compuestas se repiten en múltiples métodos

Patrón:
```sql
CREATE OR REPLACE VIEW vista_[entidad]_completa AS
SELECT
    e.*,
    t1.nombre_t1,
    -- campos calculados...
FROM [entidad] e
INNER JOIN tabla1 t1 ON t1.id = e.id_t1
LEFT JOIN  tabla2 t2 ON t2.id = e.id_t2
WHERE e.activo_[entidad] = 1;
```

---

## 5. Reglas de binding (PDO)

| Tipo de campo | Tipo PDO |
|---------------|----------|
| IDs, enteros | `PDO::PARAM_INT` |
| Strings, texto, email, teléfono | `PDO::PARAM_STR` |
| DECIMAL (dinero, porcentaje) | `PDO::PARAM_STR` — MySQL acepta string para DECIMAL |
| Boolean / TINYINT(1) | `PDO::PARAM_BOOL` |
| NULL explícito | `PDO::PARAM_NULL` |

### Campos opcionales — patrón obligatorio

```php
// Opción compacta (inline):
$stmt->bindValue(N, $valor, !empty($valor) ? PDO::PARAM_STR : PDO::PARAM_NULL);

// Opción explícita (para mayor claridad):
if (!empty($campo_opcional)) {
    $stmt->bindValue(N, $campo_opcional, PDO::PARAM_STR);
} else {
    $stmt->bindValue(N, null, PDO::PARAM_NULL);
}
```

**NUNCA** pasar un string vacío `''` donde la BD espera NULL o INT.

---

## 6. Convenciones de BD que afectan al modelo

```sql
-- Campos obligatorios en TODA tabla
id_[tabla]         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY
activo_[tabla]     BOOLEAN DEFAULT TRUE   -- soft delete
created_at_[tabla] TIMESTAMP DEFAULT CURRENT_TIMESTAMP
updated_at_[tabla] TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP

-- Todos los campos con sufijo _[tabla]
nombre_[tabla]     VARCHAR(100) NOT NULL
email_[tabla]      VARCHAR(100)           -- NULL si es opcional

-- Dinero
precio_[tabla]     DECIMAL(10,2)
-- Porcentaje
pct_[tabla]        DECIMAL(5,2)
```

---

## 7. Checklist antes de entregar el modelo

- [ ] `require_once` de `conexion.php` y `funciones.php`
- [ ] `private $conexion` y `private $registro`
- [ ] `__construct()` con zona horaria Europe/Madrid
- [ ] Los 8 métodos estándar implementados
- [ ] `try-catch` en cada método
- [ ] Prepared statements con `bindValue()` en todas las queries
- [ ] Campos opcionales usan `PDO::PARAM_NULL` cuando vacíos
- [ ] `lastInsertId()` devuelto en `insert_`
- [ ] `rowCount()` o booleano devuelto en `update_`, `delete_`, `activar_`
- [ ] `RegistroActividad` en INSERT, UPDATE, DELETE y errores
- [ ] **Nunca DELETE físico** — usar soft delete (`activo = 0`)

---

## 8. Prompt de activación

Para pedir a la IA que genere un modelo nuevo, usa este prompt:

```
Lee `.claude/specs/controller-models/models.md` y genera el fichero
`models/[Entidad].php` para la entidad `[entidad]`.

Datos de la entidad:
- Nombre de clase: [Entidad]
- Prefijo de tabla: [entidad]
- PK: id_[entidad]
- Campo único para verificar: [campo_unico_entidad]
- ¿Existe vista SQL?: [SÍ: vista_[entidad]_completa / NO]
- Campos obligatorios (NOT NULL): [lista]
- Campos opcionales (NULL): [lista]
- FK obligatorias: [lista de id_xxx → tabla_referenciada]
- FK opcionales: [lista de id_xxx → tabla_referenciada]
- Campos DATE/DATETIME opcionales: [lista]
- Campos DECIMAL: [lista]
- Campos TEXT: [lista]
- Campos BOOLEAN extra (además de activo_): [lista]

[Pega aquí el CREATE TABLE completo]
[Pega aquí el CREATE VIEW completo si existe]
```
