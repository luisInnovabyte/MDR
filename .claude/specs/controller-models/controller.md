# Spec — Generación de Controllers PHP (MDR)

> Referencia: `docs/controller-models/controller.md` + `docs/controller-models/prompt_controller.md`  
> Patrón: MVC estricto — PHP puro sin frameworks

---

## 1. Qué es un Controller en MDR

El controller recibe peticiones AJAX desde las vistas, delega en el modelo y devuelve JSON.  
**No contiene lógica de negocio** — eso va en el modelo.

### Nomenclatura

| Modelo | Controller |
|--------|------------|
| `Presupuesto.php` | `presupuesto.php` |
| `Cliente.php` | `cliente.php` |
| `Articulo.php` | `articulo.php` |

Ruta: `controller/[entidad].php`

---

## 2. Estructura obligatoria

```php
<?php
require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/[Entidad].php";

$registro = new RegistroActividad();
$[entidad] = new [Entidad]();

switch ($_GET["op"]) {
    case "listar":          // Listado completo para DataTables
    case "guardaryeditar":  // INSERT o UPDATE según id
    case "mostrar":         // Obtener registro por ID
    case "eliminar":        // Soft delete (activo=0)
    case "activar":         // Reactivar registro
    case "verificar":       // Validar unicidad de campo
    case "listar_disponibles": // Solo activos
}
?>
```

---

## 3. Operaciones estándar (switch cases)

### `listar` — DataTables

```php
case "listar":
    $datos = $[entidad]->get_[entidades]();
    $data  = [];

    foreach ($datos as $row) {
        $data[] = [
            "id_[entidad]"     => $row["id_[entidad]"],
            "nombre_[entidad]" => $row["nombre_[entidad]"],
            "activo_[entidad]" => $row["activo_[entidad]"],
            "opciones" => '
                <button class="btn btn-warning btn-sm" onclick="mostrar('.$row["id_[entidad]"].')">
                    <i class="fa fa-edit"></i>
                </button>
                <button class="btn btn-danger btn-sm" onclick="eliminar('.$row["id_[entidad]"].')">
                    <i class="fa fa-trash"></i>
                </button>'
        ];
    }

    header('Content-Type: application/json');
    echo json_encode([
        "draw"            => intval($_POST['draw'] ?? 1),
        "recordsTotal"    => count($data),
        "recordsFiltered" => count($data),
        "data"            => $data
    ], JSON_UNESCAPED_UNICODE);
    break;
```

### `guardaryeditar` — INSERT o UPDATE

```php
case "guardaryeditar":
    try {
        $id_[entidad] = $_POST["id_[entidad]"] ?? null;

        // Campos obligatorios — sanitizar siempre
        $nombre = htmlspecialchars(trim($_POST["nombre_[entidad]"]), ENT_QUOTES, 'UTF-8');

        // Campos opcionales — convertir vacíos a null
        $campo_opt = !empty($_POST["campo_opt"]) ? $_POST["campo_opt"] : null;

        // FK opcionales — validar no vacío y no 'null' (string)
        $id_fk = null;
        if (isset($_POST["id_fk"]) && $_POST["id_fk"] !== '' && $_POST["id_fk"] !== 'null') {
            $id_fk = intval($_POST["id_fk"]);
        }

        if (empty($id_[entidad])) {
            $resultado = $[entidad]->insert_[entidad]($nombre, $campo_opt, $id_fk);
            if ($resultado) {
                echo json_encode(['success' => true, 'message' => 'Registro creado correctamente',
                                  'id_[entidad]' => $resultado], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al crear el registro'],
                                  JSON_UNESCAPED_UNICODE);
            }
        } else {
            $resultado = $[entidad]->update_[entidad]($id_[entidad], $nombre, $campo_opt, $id_fk);
            if ($resultado !== false) {
                echo json_encode(['success' => true, 'message' => 'Registro actualizado correctamente'],
                                  JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al actualizar el registro'],
                                  JSON_UNESCAPED_UNICODE);
            }
        }
    } catch (Exception $e) {
        $registro->registrarActividad('admin', '[entidad].php', 'guardaryeditar',
                                       "Error: " . $e->getMessage(), 'error');
        echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud'],
                          JSON_UNESCAPED_UNICODE);
    }
    break;
```

### `mostrar` — Obtener registro para edición

```php
case "mostrar":
    $datos = $[entidad]->get_[entidad]xid($_POST["id_[entidad]"]);
    header('Content-Type: application/json');
    echo json_encode($datos ?: ['success' => false, 'message' => 'Registro no encontrado'],
                     JSON_UNESCAPED_UNICODE);
    break;
```

### `eliminar` — Soft delete

```php
case "eliminar":
    $resultado = $[entidad]->delete_[entidad]xid($_POST["id_[entidad]"]);
    header('Content-Type: application/json');
    echo json_encode(
        $resultado
            ? ['success' => true,  'message' => 'Registro desactivado correctamente']
            : ['success' => false, 'message' => 'Error al desactivar el registro'],
        JSON_UNESCAPED_UNICODE
    );
    break;
```

### `activar` — Reactivar

```php
case "activar":
    try {
        $resultado = $[entidad]->activar_[entidad]xid($_POST["id_[entidad]"]);
        header('Content-Type: application/json');
        echo json_encode(
            $resultado
                ? ['success' => true,  'message' => 'Registro activado correctamente']
                : ['success' => false, 'message' => 'No se pudo activar el registro'],
            JSON_UNESCAPED_UNICODE
        );
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()],
                          JSON_UNESCAPED_UNICODE);
    }
    break;
```

### `verificar` — Validar unicidad

```php
case "verificar":
    $campo_unico = $_POST["campo_unico"];
    $id_[entidad] = $_POST["id_[entidad]"] ?? null;
    $resultado = $[entidad]->verificar[Entidad]($campo_unico, $id_[entidad]);
    if (!isset($resultado['success'])) {
        $resultado['success'] = !isset($resultado['error']);
    }
    header('Content-Type: application/json');
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    break;
```

### `listar_disponibles` — Solo activos

```php
case "listar_disponibles":
    $datos = $[entidad]->get_[entidades]_disponibles();
    $data  = [];
    foreach ($datos as $row) {
        $data[] = [
            "id_[entidad]"   => $row["id_[entidad]"],
            "nombre_[entidad]" => $row["nombre_[entidad]"]
        ];
    }
    header('Content-Type: application/json');
    echo json_encode([
        "draw" => 1, "recordsTotal" => count($data),
        "recordsFiltered" => count($data), "data" => $data
    ], JSON_UNESCAPED_UNICODE);
    break;
```

---

## 4. Reglas de seguridad (obligatorias)

| Regla | Cómo |
|-------|------|
| Sanitizar inputs de texto | `htmlspecialchars(trim(...), ENT_QUOTES, 'UTF-8')` |
| Sanitizar emails | `filter_var(..., FILTER_SANITIZE_EMAIL)` |
| Sanitizar teléfonos | `preg_replace('/[^0-9+]/', '', ...)` |
| FK opcionales vacías | Verificar `!== ''` y `!== 'null'` antes de `intval()` |
| Fechas opcionales | Verificar `!== ''` y `!== 'null'` antes de asignar |
| No exponer errores SQL | Mensaje genérico al cliente, detalle solo en log |
| Content-Type | `header('Content-Type: application/json')` siempre antes de `echo` |
| Encoding | `JSON_UNESCAPED_UNICODE` en todos los `json_encode` |

### Conversión de campos opcionales — patrón estándar

```php
// FK entero opcional
$id_fk = null;
if (isset($_POST["id_fk"]) && $_POST["id_fk"] !== '' && $_POST["id_fk"] !== 'null') {
    $id_fk = intval($_POST["id_fk"]);
}

// Fecha opcional
$fecha = null;
if (isset($_POST["fecha"]) && $_POST["fecha"] !== '' && $_POST["fecha"] !== 'null') {
    $fecha = $_POST["fecha"];
}

// String opcional
$campo_opt = !empty($_POST["campo_opt"]) ? trim($_POST["campo_opt"]) : null;
```

---

## 5. Patrones de respuesta JSON

```jsonc
// Éxito en operación
{ "success": true, "message": "...", "id_[entidad]": 42 }

// Error en operación
{ "success": false, "message": "Error al procesar la solicitud" }

// DataTables
{ "draw": 1, "recordsTotal": 100, "recordsFiltered": 100, "data": [...] }

// Verificar unicidad
{ "existe": true }   // o false
```

---

## 6. Checklist antes de entregar el controller

- [ ] `require_once` de conexion, funciones y el modelo
- [ ] `$registro = new RegistroActividad()` instanciado
- [ ] `switch ($_GET["op"])` como estructura principal
- [ ] Los 7 cases estándar implementados
- [ ] Todos los campos opcionales convierten vacío → null
- [ ] `header('Content-Type: application/json')` antes de cada `echo`
- [ ] `JSON_UNESCAPED_UNICODE` en todos los `json_encode`
- [ ] `try-catch` en `guardaryeditar`, `activar`, y cualquier case complejo
- [ ] Mensajes de error genéricos (sin detalles SQL al cliente)
- [ ] `RegistroActividad` en INSERT, UPDATE, DELETE y errores

---

## 7. Prompt de activación

Para pedir a la IA que genere un controller nuevo, usa este prompt:

```
Lee `.claude/specs/controller-models/controller.md` y genera el fichero
`controller/[entidad].php` para la entidad `[entidad]`.

Datos de la entidad:
- Nombre de clase del modelo: [Entidad]
- Prefijo de tabla: [entidad]
- PK: id_[entidad]
- Campo único para verificar: [campo_unico_entidad]
- Campos obligatorios: [lista]
- Campos opcionales (null): [lista]
- FK opcionales: [lista de id_xxx]
- Operaciones extra (además de las 7 estándar): [lista o "ninguna"]

[Pega aquí el CREATE TABLE completo]
```
