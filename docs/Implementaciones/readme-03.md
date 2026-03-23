# 03 - Backend PHP

> Documentación completa de la arquitectura backend MVC con PHP puro y MySQL

---

## Documentos de esta sección

| # | Documento | Descripción |
|---|-----------|-------------|
| 01 | [Estructura de Carpetas](estructura_carpetas.md) | Arquitectura MVC, organización de directorios, convenciones de nombres |
| 02 | [Conexión a Base de Datos](conexion.md) | Clase PDO con configuración JSON externa |
| 03 | [Models](models.md) | Patrón de modelos, métodos CRUD estándar, uso de vistas SQL |
| 04 | [Controllers](controller.md) | Estructura de controladores, switch de operaciones, respuestas JSON |
| 05 | [RegistroActividad](RegistroActividad.md) | Sistema de logging y auditoría en archivos JSON |

---

## Resumen de Arquitectura

### Stack Tecnológico

| Capa | Tecnología |
|------|------------|
| Backend | PHP 7.4+ (sin framework) |
| Base de Datos | MySQL/MariaDB con PDO |
| Patrón | MVC (Model-View-Controller) |
| Comunicación | AJAX + JSON |
| Frontend | HTML5, CSS3, JavaScript ES6+, Bootstrap 5, jQuery, DataTables |

### Flujo de una Petición

```
┌──────────┐     AJAX      ┌────────────┐            ┌─────────┐            ┌────────┐
│   Vista  │ ────────────> │ Controller │ ────────── │  Model  │ ────────── │   BD   │
│   (JS)   │               │   (PHP)    │            │  (PHP)  │            │ MySQL  │
└──────────┘               └────────────┘            └─────────┘            └────────┘
     ↑                           │                        │                      │
     │                           │                        │                      │
     └─────────── JSON ──────────┴────────────────────────┴──────────────────────┘
```

---

## Convenciones Rápidas

### Nomenclatura de Archivos

| Tipo | Convención | Ejemplo |
|------|------------|---------|
| Model | PascalCase | `Presupuesto.php`, `Cliente.php` |
| Controller | minúsculas (igual que model) | `presupuesto.php`, `cliente.php` |
| Vista (carpeta) | PascalCase o prefijo Mnt/Consulta/Informe | `MntClientes/`, `Presupuesto/` |

### Métodos Estándar del Model

| Método | Propósito |
|--------|-----------|
| `get_[entidades]()` | Listar todos los registros |
| `get_[entidades]_disponibles()` | Listar solo activos |
| `get_[entidad]xid($id)` | Obtener por ID |
| `insert_[entidad](...)` | Insertar nuevo registro |
| `update_[entidad]($id, ...)` | Actualizar registro |
| `delete_[entidad]xid($id)` | Desactivar (soft delete) |
| `activar_[entidad]xid($id)` | Reactivar registro |
| `verificar[Entidad]($campo, $id)` | Validar unicidad |

### Operaciones Estándar del Controller

| Operación (`op`) | Acción |
|------------------|--------|
| `listar` | Obtener todos (para DataTables) |
| `listar_disponibles` | Obtener solo activos |
| `mostrar` | Obtener uno por ID |
| `guardaryeditar` | INSERT o UPDATE según si hay ID |
| `eliminar` | Desactivar registro |
| `activar` | Reactivar registro |
| `verificar` | Validar campo único |

### Estructura de Respuestas JSON

**Listados (DataTables):**
```json
{
  "draw": 1,
  "recordsTotal": 100,
  "recordsFiltered": 100,
  "data": [...]
}
```

**Operaciones:**
```json
{
  "success": true,
  "message": "Operación exitosa",
  "id_entidad": 42
}
```

---

## Inicialización Estándar

### En un Model

```php
<?php
require_once '../config/conexion.php';
require_once '../config/funciones.php';

class NombreEntidad
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
    }
    
    // Métodos CRUD...
}
```

### En un Controller

```php
<?php
require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/NombreEntidad.php";

$registro = new RegistroActividad();
$entidad = new NombreEntidad();

switch ($_GET["op"]) {
    case "listar":
        // ...
        break;
    case "guardaryeditar":
        // ...
        break;
    // ... más operaciones
}
```

---

## Seguridad Implementada

| Medida | Implementación |
|--------|----------------|
| SQL Injection | Prepared statements con PDO |
| Credenciales | Archivo JSON externo (no en código) |
| Soft Delete | Campo `activo_` en lugar de DELETE físico |
| Logging | RegistroActividad para auditoría |
| Errores | Try-catch con mensajes controlados |

---

## Archivos de Configuración

```
config/
├── conexion.php          ← Clase PDO de conexión
├── conexion.json         ← Credenciales (excluir de Git)
├── conexion.json.example ← Plantilla para el equipo
└── funciones.php         ← RegistroActividad + helpers
```

---

## Cómo Añadir Nuevas Funcionalidades

1. **Crear el archivo de documentación** correspondiente (ej: `validaciones.md`)
2. **Añadirlo a este índice** en la tabla de documentos
3. **Actualizar las secciones** de resumen si es necesario

---

## Enlaces Útiles

- [Documentación PDO](https://www.php.net/manual/es/book.pdo.php)
- [Prepared Statements](https://www.php.net/manual/es/pdo.prepared-statements.php)
- [JSON en PHP](https://www.php.net/manual/es/book.json.php)

---

*Sección: 03 - Backend PHP | Última actualización: Diciembre 2024*
