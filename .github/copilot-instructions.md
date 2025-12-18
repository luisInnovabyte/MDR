# Instrucciones de Desarrollo - MDR ERP Manager

> Sistema ERP para gestión de alquiler de equipos audiovisuales  
> Arquitectura MVC con PHP 8+ y MySQL/MariaDB

---

## ��� Stack Tecnológico

- **Backend**: PHP 8.x con PDO (sin frameworks)
- **Base de Datos**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript ES6+, Bootstrap 5, jQuery
- **Patrón**: MVC estricto (Model-View-Controller)
- **Comunicación**: AJAX + JSON
- **Charset**: UTF8MB4 (utf8mb4_spanish_ci)
- **Zona Horaria**: Europe/Madrid

---

## ���️ CONVENCIONES DE BASE DE DATOS

### Nomenclatura de Tablas

**REGLA FUNDAMENTAL**: Tablas en **SINGULAR**, campos con sufijo **_<<nombre_tabla>>**

```sql
-- ✅ CORRECTO
CREATE TABLE cliente (
    id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100),
    telefono_cliente VARCHAR(20),
    activo_cliente BOOLEAN DEFAULT TRUE,
    created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ❌ INCORRECTO
CREATE TABLE clientes (  -- No plural
    id INT,  -- Falta sufijo _cliente
    nombre VARCHAR(100),  -- Falta sufijo _cliente
    activo BOOLEAN  -- Falta sufijo _cliente
);
```

### Campos Obligatorios en TODA Tabla

```sql
-- Estos 4 campos son OBLIGATORIOS en cada tabla:
id_<<tabla>> INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
activo_<<tabla>> BOOLEAN DEFAULT TRUE COMMENT 'Soft delete: TRUE=activo, FALSE=eliminado',
created_at_<<tabla>> TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at_<<tabla>> TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

### Foreign Keys

```sql
-- Siempre con sufijos claros y acciones definidas
CONSTRAINT fk_presupuesto_cliente 
    FOREIGN KEY (id_cliente) 
    REFERENCES cliente(id_cliente)
    ON DELETE RESTRICT 
    ON UPDATE CASCADE
```

### Índices Estándar

```sql
-- Siempre indexar:
INDEX idx_activo_<<tabla>> (activo_<<tabla>>),
INDEX idx_created_<<tabla>> (created_at_<<tabla>>),
-- FK automáticamente indexadas
-- Campos de búsqueda frecuente
```

### Tipos de Datos Estándar

| Uso | Tipo SQL | Ejemplo |
|-----|----------|---------|
| **Dinero** | `DECIMAL(10,2)` | `precio_articulo DECIMAL(10,2)` |
| **Texto corto** | `VARCHAR(100)` | `nombre_cliente VARCHAR(100)` |
| **Texto medio** | `VARCHAR(255)` | `direccion_cliente VARCHAR(255)` |
| **Texto largo** | `TEXT` | `descripcion_articulo TEXT` |
| **Email** | `VARCHAR(100)` | `email_cliente VARCHAR(100)` |
| **Teléfono** | `VARCHAR(20)` | `telefono_cliente VARCHAR(20)` |
| **CIF/NIF** | `VARCHAR(20)` | `nif_empresa VARCHAR(20)` |
| **Código postal** | `VARCHAR(10)` | `cp_cliente VARCHAR(10)` |
| **Boolean** | `BOOLEAN` o `TINYINT(1)` | `activo_cliente BOOLEAN` |
| **Fecha** | `DATE` | `fecha_presupuesto DATE` |
| **Fecha+Hora** | `DATETIME` | `fecha_evento_presupuesto DATETIME` |
| **Timestamp** | `TIMESTAMP` | `created_at_cliente TIMESTAMP` |
| **Enum** | `ENUM('valor1','valor2')` | `tipo_empresa ENUM('real','ficticia')` |
| **Porcentaje** | `DECIMAL(5,2)` | `iva_impuesto DECIMAL(5,2)` |
| **Cantidad** | `INT UNSIGNED` | `cantidad_linea INT UNSIGNED` |
| **ID** | `INT UNSIGNED` | `id_cliente INT UNSIGNED` |

### Configuración de Tabla

```sql
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## ���️ ARQUITECTURA MVC

### Estructura de Directorios

```
MDR/
├── config/
│   ├── conexion.php          ← Clase PDO de conexión
│   ├── conexion.json         ← Credenciales (NO en Git)
│   ├── funciones.php         ← RegistroActividad + helpers
│   └── template/             ← Plantillas compartidas
│
├── models/                   ← Clases de acceso a datos
│   ├── Presupuesto.php
│   ├── Clientes.php
│   ├── Articulo.php
│   └── ...
│
├── controller/               ← Lógica de negocio
│   ├── presupuesto.php
│   ├── cliente.php
│   ├── articulo.php
│   └── ...
│
├── view/                     ← Interfaces de usuario
│   ├── Presupuesto/
│   ├── MntClientes/
│   ├── MntArticulos/
│   └── ...
│
├── public/                   ← Recursos públicos
│   ├── css/
│   ├── js/
│   ├── img/
│   ├── lib/
│   ├── logs/
│   └── documentos/
│
└── BD/                       ← Scripts SQL
    └── claude_MDR            ← Estructura completa BD
```

---

## ��� MODELOS (Models)

### Estructura Estándar de un Modelo

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
        // 1. Inicializar conexión PDO
        $this->conexion = (new Conexion())->getConexion();
        
        // 2. Inicializar registro de actividad
        $this->registro = new RegistroActividad();
        
        // 3. Configurar zona horaria
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'NombreEntidad',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // MÉTODO 1: Listar todos (usando vista SQL si existe)
    public function get_entidades()
    {
        try {
            // Preferir vistas SQL para consultas complejas
            $sql = "SELECT * FROM vista_entidad_completa 
                    WHERE activo_entidad = 1 
                    ORDER BY nombre_entidad ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidades',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // MÉTODO 2: Listar solo activos
    public function get_entidades_disponibles()
    {
        try {
            $sql = "SELECT * FROM entidad 
                    WHERE activo_entidad = 1 
                    ORDER BY nombre_entidad ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidades_disponibles',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // MÉTODO 3: Obtener por ID
    public function get_entidadxid($id_entidad)
    {
        try {
            $sql = "SELECT * FROM entidad 
                    WHERE id_entidad = ? 
                    AND activo_entidad = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 4: Insertar
    public function insert_entidad($nombre, $descripcion, $campo_opcional = null)
    {
        try {
            $sql = "INSERT INTO entidad (
                        nombre_entidad, 
                        descripcion_entidad, 
                        campo_opcional_entidad,
                        created_at_entidad
                    ) VALUES (?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion, PDO::PARAM_STR);
            
            // IMPORTANTE: Manejo de campos opcionales
            if (!empty($campo_opcional)) {
                $stmt->bindValue(3, $campo_opcional, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'insert_entidad',
                "Entidad creada con ID: $id",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'insert_entidad',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 5: Actualizar
    public function update_entidad($id_entidad, $nombre, $descripcion, $campo_opcional = null)
    {
        try {
            $sql = "UPDATE entidad SET 
                        nombre_entidad = ?,
                        descripcion_entidad = ?,
                        campo_opcional_entidad = ?,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion, PDO::PARAM_STR);
            
            if (!empty($campo_opcional)) {
                $stmt->bindValue(3, $campo_opcional, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(4, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'update_entidad',
                "Entidad actualizada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'update_entidad',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 6: Eliminar (SOFT DELETE)
    public function delete_entidadxid($id_entidad)
    {
        try {
            // NO usar DELETE físico, usar soft delete
            $sql = "UPDATE entidad SET 
                        activo_entidad = 0,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'delete_entidadxid',
                "Entidad desactivada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'delete_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 7: Activar (restaurar soft delete)
    public function activar_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE entidad SET 
                        activo_entidad = 1,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'activar_entidadxid',
                "Entidad activada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'activar_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 8: Verificar existencia (validación unicidad)
    public function verificarEntidad($campo_unico, $id_entidad = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM entidad 
                    WHERE LOWER(campo_unico_entidad) = LOWER(?)";
            $params = [trim($campo_unico)];
            
            // Excluir el propio registro en edición
            if (!empty($id_entidad)) {
                $sql .= " AND id_entidad != ?";
                $params[] = $id_entidad;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'existe' => ($resultado['total'] > 0)
            ];
            
        } catch (PDOException $e) {
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>
```

### Métodos Estándar (todos los modelos)

1. ✅ `__construct()` - Inicialización con PDO y RegistroActividad
2. ✅ `get_entidades()` - Listar todos
3. ✅ `get_entidades_disponibles()` - Listar solo activos
4. ✅ `get_entidadxid($id)` - Obtener por ID
5. ✅ `insert_entidad(...)` - Insertar nuevo registro
6. ✅ `update_entidad($id, ...)` - Actualizar registro
7. ✅ `delete_entidadxid($id)` - Soft delete (activo=0)
8. ✅ `activar_entidadxid($id)` - Reactivar (activo=1)
9. ✅ `verificarEntidad($campo, $id)` - Validar unicidad

### Métodos NO Estándar (según necesidad)

- `obtenerEstadisticas()` - Solo cuando se necesitan dashboards/métricas
- `get_entidades_por_categoria($id_categoria)` - Filtros específicos
- Métodos personalizados según lógica de negocio

---

## ��� CONTROLADORES (Controllers)

### Estructura Estándar de un Controller

```php
<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/NombreEntidad.php";

// Inicializar clases
$registro = new RegistroActividad();
$entidad = new NombreEntidad();

// Switch principal basado en operación
switch ($_GET["op"]) {
    
    case "listar":
        // Para DataTables
        $datos = $entidad->get_entidades();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_entidad" => $row["id_entidad"],
                "nombre_entidad" => $row["nombre_entidad"],
                "descripcion_entidad" => $row["descripcion_entidad"],
                "activo_entidad" => $row["activo_entidad"],
                "opciones" => '<button class="btn btn-warning btn-sm" onclick="mostrar('.$row["id_entidad"].')">
                                  <i class="fa fa-edit"></i>
                               </button>
                               <button class="btn btn-danger btn-sm" onclick="desactivar('.$row["id_entidad"].')">
                                  <i class="fa fa-trash"></i>
                               </button>'
            );
        }
        
        $results = array(
            "draw" => intval($_POST['draw'] ?? 1),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
    
    case "guardaryeditar":
        // Validar si es INSERT o UPDATE
        $id_entidad = $_POST["id_entidad"] ?? null;
        
        // Sanitizar datos
        $nombre = htmlspecialchars(trim($_POST["nombre_entidad"]), ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars(trim($_POST["descripcion_entidad"]), ENT_QUOTES, 'UTF-8');
        
        // Campos opcionales: convertir vacío a null
        $campo_opcional = !empty($_POST["campo_opcional"]) ? $_POST["campo_opcional"] : null;
        
        try {
            if (empty($id_entidad)) {
                // INSERT
                $resultado = $entidad->insert_entidad($nombre, $descripcion, $campo_opcional);
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro creado correctamente',
                        'id_entidad' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al crear el registro'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // UPDATE
                $resultado = $entidad->update_entidad($id_entidad, $nombre, $descripcion, $campo_opcional);
                
                if ($resultado !== false) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro actualizado correctamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar el registro'
                    ], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin',
                'entidad.php',
                'guardaryeditar',
                "Error: " . $e->getMessage(),
                'error'
            );
            
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "mostrar":
        // Obtener registro por ID para edición
        $id_entidad = $_POST["id_entidad"];
        $datos = $entidad->get_entidadxid($id_entidad);
        
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "desactivar":
        // Soft delete
        $id_entidad = $_POST["id_entidad"];
        $resultado = $entidad->delete_entidadxid($id_entidad);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Registro desactivado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar el registro'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "activar":
        // Reactivar
        $id_entidad = $_POST["id_entidad"];
        $resultado = $entidad->activar_entidadxid($id_entidad);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Registro activado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar el registro'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "verificar":
        // Validar unicidad de campo
        $campo_unico = $_POST["campo_unico"];
        $id_entidad = $_POST["id_entidad"] ?? null;
        
        $resultado = $entidad->verificarEntidad($campo_unico, $id_entidad);
        
        if (!isset($resultado['success'])) {
            $resultado['success'] = !isset($resultado['error']);
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
    
    case "listar_disponibles":
        // Solo registros activos
        $datos = $entidad->get_entidades_disponibles();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_entidad" => $row["id_entidad"],
                "nombre_entidad" => $row["nombre_entidad"]
            );
        }
        
        $results = array(
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

### Operaciones Estándar (switch cases)

1. ✅ `listar` - Listado completo para DataTables
2. ✅ `guardaryeditar` - INSERT o UPDATE según id
3. ✅ `mostrar` - Obtener registro para edición
4. ✅ `desactivar` - Soft delete
5. ✅ `activar` - Reactivar registro
6. ✅ `verificar` - Validar unicidad
7. ✅ `listar_disponibles` - Solo activos

---

## ��� SEGURIDAD

### Prepared Statements (SIEMPRE)

```php
// ✅ CORRECTO: Prepared statement con bindValue
$sql = "SELECT * FROM cliente WHERE email_cliente = ?";
$stmt = $this->conexion->prepare($sql);
$stmt->bindValue(1, $email, PDO::PARAM_STR);
$stmt->execute();

// ✅ CORRECTO: Prepared statement con array de parámetros
$sql = "INSERT INTO cliente (nombre_cliente, email_cliente) VALUES (?, ?)";
$stmt = $this->conexion->prepare($sql);
$stmt->execute([$nombre, $email]);

// ❌ PROHIBIDO: Concatenación directa (SQL Injection)
$sql = "SELECT * FROM cliente WHERE email_cliente = '$email'";
$resultado = $this->conexion->query($sql);
```

### Sanitización de Inputs

```php
// ✅ Sanitizar SIEMPRE en controllers
$nombre = htmlspecialchars(trim($_POST["nombre"]), ENT_QUOTES, 'UTF-8');
$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
$telefono = preg_replace('/[^0-9+]/', '', $_POST["telefono"]);

// Validar formato
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Email inválido']);
    exit;
}
```

### Manejo de Campos Opcionales

```php
// ✅ CORRECTO: Convertir vacío a NULL
$id_contacto = !empty($_POST["id_contacto"]) ? $_POST["id_contacto"] : null;

if (!empty($id_contacto)) {
    $stmt->bindValue(3, $id_contacto, PDO::PARAM_INT);
} else {
    $stmt->bindValue(3, null, PDO::PARAM_NULL);
}

// ❌ INCORRECTO: No validar antes de insertar
$stmt->bindValue(3, $_POST["id_contacto"], PDO::PARAM_INT);
```

### Tipos de Datos en bindValue()

```php
// Tipos PDO estándar
PDO::PARAM_INT    // Enteros: IDs, cantidades
PDO::PARAM_STR    // Cadenas: nombres, descripciones
PDO::PARAM_BOOL   // Booleanos: activo, visible
PDO::PARAM_NULL   // NULL explícito
```

### Manejo de Errores

```php
// ✅ CORRECTO: Try-catch con logging
try {
    // Operación de BD
    $resultado = $modelo->insert_entidad(...);
    
} catch (PDOException $e) {
    $this->registro->registrarActividad(
        'admin',
        'Controller',
        'operacion',
        "Error: " . $e->getMessage(),
        'error'
    );
    
    // Mensaje genérico al usuario (NO exponer detalles del error)
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud'
    ]);
}
```

---

## ��� CONEXIÓN A BASE DE DATOS

### Clase Conexion.php

```php
<?php

class Conexion
{
    private $pdo;

    public function __construct()
    {
        // Leer credenciales desde JSON externo
        $config_file = __DIR__ . '/conexion.json';
        
        if (!file_exists($config_file)) {
            throw new Exception("Error: El archivo de configuración no existe");
        }

        $config_json = file_get_contents($config_file);
        $config = json_decode($config_json, true);

        if ($config === null) {
            throw new Exception("Error: No se pudo parsear el archivo de configuración");
        }

        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion()
    {
        return $this->pdo;
    }
}
?>
```

### Archivo conexion.json (NO versionar en Git)

```json
{
    "host": "217.154.117.83",
    "port": "3308",
    "user": "administrator",
    "password": "27979699",
    "database": "toldos_db",
    "charset": "utf8mb4"
}
```

### Uso en Modelos

```php
// En constructor del modelo
$this->conexion = (new Conexion())->getConexion();

// Usar PDO normalmente
$stmt = $this->conexion->prepare("SELECT ...");
```

---

## ��� SISTEMA DE LOGGING

### Clase RegistroActividad

```php
// Ubicación: config/funciones.php

class RegistroActividad
{
    private $directorio = '../public/logs/';

    public function registrarActividad($usuario, $pantalla, $actividad, $mensaje, $tipo = 'info')
    {
        // Crear archivo JSON diario: YYYY-MM-DD.json
        $fecha = date('Y-m-d');
        $archivo = $this->directorio . $fecha . '.json';
        
        // Crear entrada de log
        $registro = [
            'usuario' => $usuario,
            'pantalla' => $pantalla,
            'actividad' => $actividad,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'fecha_hora' => date('Y-m-d H:i:s')
        ];
        
        // Leer logs existentes
        if (file_exists($archivo)) {
            $logs = json_decode(file_get_contents($archivo), true);
        } else {
            $logs = [];
        }
        
        // Añadir nuevo registro
        $logs[] = $registro;
        
        // Guardar
        file_put_contents($archivo, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
```

### Uso en Modelos y Controllers

```php
// Éxito
$this->registro->registrarActividad(
    'admin',
    'Presupuesto',
    'insert_presupuesto',
    "Presupuesto creado con ID: $id",
    'info'
);

// Error
$this->registro->registrarActividad(
    'admin',
    'Presupuesto',
    'insert_presupuesto',
    "Error: " . $e->getMessage(),
    'error'
);

// Advertencia
$this->registro->registrarActividad(
    'system',
    'Conexion',
    '__construct',
    "Zona horaria no configurada",
    'warning'
);
```

---

## ��� VISTAS (Views)

### Estructura HTML con Bootstrap 5

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Entidades - MDR</title>
    
    <!-- Bootstrap 5 -->
    <link href="../../public/lib/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="../../public/lib/DataTables/datatables.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="../../public/lib/fontawesome-6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="../../public/lib/sweetalert2-11.7.32/sweetalert2.min.css" rel="stylesheet">
    
    <!-- CSS personalizado -->
    <link href="../../public/css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Header y navegación -->
    <?php include '../template/header.php'; ?>
    <?php include '../template/sidebar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Entidades</h1>
                
                <!-- Botón crear -->
                <button class="btn btn-primary" onclick="mostrarFormulario()">
                    <i class="fa fa-plus"></i> Nueva Entidad
                </button>
                
                <!-- Tabla DataTables -->
                <table id="tblEntidades" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se llena vía AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal formulario -->
    <div class="modal fade" id="modalFormulario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="frmEntidad">
                    <div class="modal-header">
                        <h5 class="modal-title">Formulario de Entidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_entidad" id="id_entidad">
                        
                        <div class="mb-3">
                            <label for="nombre_entidad" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre_entidad" name="nombre_entidad" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion_entidad" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_entidad" name="descripcion_entidad" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="../../public/lib/jquery-3.7.1/jquery.min.js"></script>
    <script src="../../public/lib/bootstrap-5.0.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/lib/DataTables/datatables.min.js"></script>
    <script src="../../public/lib/sweetalert2-11.7.32/sweetalert2.all.min.js"></script>
    
    <!-- Script específico de la vista -->
    <script src="entidad.js"></script>
</body>
</html>
```

### JavaScript (entidad.js)

```javascript
let tabla;

$(document).ready(function() {
    // Inicializar DataTable
    tabla = $('#tblEntidades').DataTable({
        ajax: {
            url: '../../controller/entidad.php?op=listar',
            type: 'POST',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_entidad' },
            { data: 'nombre_entidad' },
            { data: 'descripcion_entidad' },
            { 
                data: 'activo_entidad',
                render: function(data) {
                    return data == 1 
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                }
            },
            { data: 'opciones', orderable: false }
        ],
        language: {
            url: '../../public/lib/DataTables/es-ES.json'
        },
        responsive: true,
        order: [[0, 'desc']]
    });
    
    // Submit formulario
    $('#frmEntidad').on('submit', function(e) {
        e.preventDefault();
        guardaryeditar();
    });
});

function mostrarFormulario() {
    $('#frmEntidad')[0].reset();
    $('#id_entidad').val('');
    $('#modalFormulario').modal('show');
}

function mostrar(id) {
    $.post('../../controller/entidad.php?op=mostrar', { id_entidad: id })
        .done(function(data) {
            $('#id_entidad').val(data.id_entidad);
            $('#nombre_entidad').val(data.nombre_entidad);
            $('#descripcion_entidad').val(data.descripcion_entidad);
            $('#modalFormulario').modal('show');
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo cargar el registro', 'error');
        });
}

function guardaryeditar() {
    let formData = $('#frmEntidad').serialize();
    
    $.post('../../controller/entidad.php?op=guardaryeditar', formData)
        .done(function(response) {
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success');
                $('#modalFormulario').modal('hide');
                tabla.ajax.reload();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
        });
}

function desactivar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción desactivará el registro",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../../controller/entidad.php?op=desactivar', { id_entidad: id })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Desactivado', response.message, 'success');
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                });
        }
    });
}

function activar(id) {
    $.post('../../controller/entidad.php?op=activar', { id_entidad: id })
        .done(function(response) {
            if (response.success) {
                Swal.fire('Activado', response.message, 'success');
                tabla.ajax.reload();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        });
}
```

---

## ��� PATRONES DE RESPUESTA JSON

### Respuestas de Controllers

```javascript
// Éxito en operación
{
    "success": true,
    "message": "Operación realizada correctamente",
    "id_entidad": 42  // Opcional: ID del registro creado
}

// Error en operación
{
    "success": false,
    "message": "Descripción del error"
}

// Listado para DataTables
{
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 100,
    "data": [
        { "id_entidad": 1, "nombre_entidad": "...", ... },
        { "id_entidad": 2, "nombre_entidad": "...", ... }
    ]
}

// Verificación de existencia
{
    "existe": true  // o false
}

// Obtener registro por ID
{
    "id_entidad": 1,
    "nombre_entidad": "...",
    "descripcion_entidad": "...",
    "activo_entidad": 1
}
```

---

## ��� TRIGGERS (Disparadores)

### Patrón de Generación de Códigos Correlativos

```sql
-- Trigger para generar código automático
DELIMITER $$

CREATE TRIGGER trg_elemento_before_insert
BEFORE INSERT ON elemento
FOR EACH ROW
BEGIN
    DECLARE siguiente_numero INT;
    
    -- Obtener el siguiente número para este artículo
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(codigo_elemento, '-', -1) AS UNSIGNED)), 0) + 1
    INTO siguiente_numero
    FROM elemento
    WHERE id_articulo = NEW.id_articulo;
    
    -- Asignar el código formato: ARTICULO-001
    SET NEW.codigo_elemento = CONCAT(
        (SELECT UPPER(nombre_articulo) FROM articulo WHERE id_articulo = NEW.id_articulo),
        '-',
        LPAD(siguiente_numero, 3, '0')
    );
END$$

DELIMITER ;
```

### Patrón de Sincronización de Estados

```sql
-- Sincronizar campo activo con estado
DELIMITER $$

CREATE TRIGGER trg_presupuesto_before_desactivar
BEFORE UPDATE ON presupuesto
FOR EACH ROW
BEGIN
    -- Si se desactiva, marcar como CANCELADO
    IF NEW.activo_presupuesto = 0 AND OLD.activo_presupuesto = 1 THEN
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'CANCELADO'
        );
    END IF;
END$$

DELIMITER ;
```

### Patrón de Validación con Error

```sql
-- Validar regla de negocio
DELIMITER $$

CREATE TRIGGER trg_empresa_validar_ficticia_principal
BEFORE INSERT ON empresa
FOR EACH ROW
BEGIN
    DECLARE existe_principal INT;
    
    -- Si intenta crear empresa ficticia principal
    IF NEW.ficticia_empresa = 1 AND NEW.empresa_ficticia_principal = 1 THEN
        
        -- Verificar si ya existe una
        SELECT COUNT(*) INTO existe_principal
        FROM empresa
        WHERE ficticia_empresa = 1 
        AND empresa_ficticia_principal = 1
        AND activo_empresa = 1;
        
        -- Si existe, lanzar error
        IF existe_principal > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Ya existe una empresa ficticia principal activa';
        END IF;
    END IF;
END$$

DELIMITER ;
```

---

## ��� VISTAS SQL

### Patrón de Vista Completa con JOINs

```sql
-- Vista que consolida información de múltiples tablas
CREATE OR REPLACE VIEW vista_presupuesto_completa AS
SELECT 
    -- Campos de presupuesto
    p.id_presupuesto,
    p.numero_presupuesto,
    p.fecha_presupuesto,
    p.fecha_validez_presupuesto,
    p.nombre_evento_presupuesto,
    p.activo_presupuesto,
    p.created_at_presupuesto,
    p.updated_at_presupuesto,
    
    -- Datos del cliente
    c.id_cliente,
    c.nombre_cliente,
    c.apellido_cliente,
    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS nombre_completo_cliente,
    c.email_cliente,
    c.telefono_cliente,
    
    -- Datos del contacto (puede ser NULL)
    cc.id_contacto_cliente,
    cc.nombre_contacto_cliente,
    cc.telefono_contacto_cliente,
    
    -- Estado del presupuesto
    ep.id_estado_ppto,
    ep.nombre_estado_ppto,
    ep.codigo_estado_ppto,
    ep.color_estado_ppto,
    
    -- Forma de pago
    fp.id_forma_pago,
    fp.nombre_forma_pago,
    
    -- Método de pago
    m.id_metodo,
    m.nombre_metodo,
    
    -- Totales calculados (si los tienes en la tabla)
    p.subtotal_presupuesto,
    p.total_iva_presupuesto,
    p.total_presupuesto

FROM presupuesto p

INNER JOIN cliente c 
    ON p.id_cliente = c.id_cliente

LEFT JOIN contacto_cliente cc 
    ON p.id_contacto_cliente = cc.id_contacto_cliente

INNER JOIN estado_presupuesto ep 
    ON p.id_estado_ppto = ep.id_estado_ppto

LEFT JOIN forma_pago fp 
    ON p.id_forma_pago = fp.id_forma_pago

LEFT JOIN metodo m 
    ON p.id_metodo = m.id_metodo

WHERE p.activo_presupuesto = 1
ORDER BY p.fecha_presupuesto DESC;
```

### Uso en Modelos

```php
// Preferir vistas para consultas complejas
public function get_presupuestos()
{
    $sql = "SELECT * FROM vista_presupuesto_completa 
            ORDER BY fecha_presupuesto DESC";
    
    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Usar tablas directamente solo para INSERT/UPDATE/DELETE
public function insert_presupuesto(...)
{
    $sql = "INSERT INTO presupuesto (...) VALUES (...)";
    // ...
}
```

---

## ✅ CHECKLIST DE BUENAS PRÁCTICAS

### Base de Datos
- [ ] Tablas en SINGULAR
- [ ] Todos los campos con sufijo _<<tabla>>
- [ ] Campos obligatorios: id, activo, created_at, updated_at
- [ ] Soft delete (activo=0) en lugar de DELETE físico
- [ ] Foreign keys con ON DELETE/UPDATE definidos
- [ ] Índices en campos de búsqueda frecuente
- [ ] Charset utf8mb4_spanish_ci

### Modelos
- [ ] Constructor con PDO y RegistroActividad
- [ ] Zona horaria configurada a Europe/Madrid
- [ ] Prepared statements en TODAS las consultas
- [ ] Try-catch en todos los métodos
- [ ] Logging de errores y acciones importantes
- [ ] Validación de campos opcionales (null)
- [ ] Retornos consistentes (ID, rowCount, boolean, array)
- [ ] Métodos estándar implementados

### Controllers
- [ ] Switch por operación ($_GET["op"])
- [ ] Sanitización de inputs
- [ ] Conversión de vacíos a null
- [ ] Respuestas JSON con JSON_UNESCAPED_UNICODE
- [ ] Headers Content-Type correctos
- [ ] Try-catch en operaciones críticas
- [ ] Logging con RegistroActividad

### Vistas
- [ ] HTML5 semántico
- [ ] Bootstrap 5 para diseño
- [ ] DataTables para listados
- [ ] SweetAlert2 para confirmaciones
- [ ] Sin lógica de negocio
- [ ] Validación client-side (complementaria)
- [ ] AJAX para comunicación con controllers

### Seguridad
- [ ] Prepared statements siempre
- [ ] Sanitización de inputs
- [ ] Validación de tipos de datos
- [ ] No exponer detalles de errores SQL
- [ ] credenciales en JSON externo
- [ ] Logging de errores
- [ ] CSRF tokens (si aplica)

---

## ��� EJEMPLOS COMPLETOS

### Ejemplo 1: Tabla Cliente

```sql
CREATE TABLE cliente (
    -- Identificación
    id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_cliente VARCHAR(20) NOT NULL UNIQUE 
        COMMENT 'Código único del cliente',
    
    -- Datos personales
    nombre_cliente VARCHAR(100) NOT NULL,
    apellido_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100),
    telefono_cliente VARCHAR(20),
    movil_cliente VARCHAR(20),
    
    -- Datos fiscales
    nif_cliente VARCHAR(20),
    tipo_cliente ENUM('particular', 'empresa') DEFAULT 'particular',
    
    -- Dirección
    direccion_cliente VARCHAR(255),
    poblacion_cliente VARCHAR(100),
    provincia_cliente VARCHAR(100),
    cp_cliente VARCHAR(10),
    pais_cliente VARCHAR(100) DEFAULT 'España',
    
    -- Campos obligatorios
    activo_cliente BOOLEAN DEFAULT TRUE,
    created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_codigo_cliente (codigo_cliente),
    INDEX idx_nombre_cliente (nombre_cliente),
    INDEX idx_email_cliente (email_cliente),
    INDEX idx_activo_cliente (activo_cliente)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Gestión de clientes del sistema';
```

### Ejemplo 2: Modelo Completo Clientes.php

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Clientes
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'Clientes',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_clientes()
    {
        try {
            $sql = "SELECT * FROM cliente 
                    WHERE activo_cliente = 1 
                    ORDER BY nombre_cliente ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'get_clientes',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    public function insert_cliente(
        $codigo, $nombre, $apellido, $email = null, 
        $telefono = null, $nif = null, $tipo = 'particular',
        $direccion = null, $poblacion = null, $provincia = null, $cp = null
    ) {
        try {
            $sql = "INSERT INTO cliente (
                        codigo_cliente, nombre_cliente, apellido_cliente,
                        email_cliente, telefono_cliente, nif_cliente, tipo_cliente,
                        direccion_cliente, poblacion_cliente, provincia_cliente, cp_cliente,
                        created_at_cliente
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(3, $apellido, PDO::PARAM_STR);
            
            // Campos opcionales
            $stmt->bindValue(4, $email, !empty($email) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(5, $telefono, !empty($telefono) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(6, $nif, !empty($nif) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(7, $tipo, PDO::PARAM_STR);
            $stmt->bindValue(8, $direccion, !empty($direccion) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9, $poblacion, !empty($poblacion) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(10, $provincia, !empty($provincia) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, $cp, !empty($cp) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'insert_cliente',
                "Cliente creado con ID: $id - $nombre $apellido",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'insert_cliente',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    public function verificarCliente($codigo, $id_cliente = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM cliente 
                    WHERE LOWER(codigo_cliente) = LOWER(?)";
            $params = [trim($codigo)];
            
            if (!empty($id_cliente)) {
                $sql .= " AND id_cliente != ?";
                $params[] = $id_cliente;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ['existe' => ($resultado['total'] > 0)];
            
        } catch (PDOException $e) {
            return ['existe' => false, 'error' => $e->getMessage()];
        }
    }
}
?>
```

---

## ��� COMANDOS GIT

```bash
# Clonar repositorio
git clone https://github.com/luisInnovabyte/MDR.git

# Actualizar desde remoto
git pull origin main

# Ver estado
git status

# Añadir cambios
git add .

# Commit
git commit -m "feat: descripción del cambio"

# Push
git push origin main

# Crear rama
git checkout -b feature/nueva-funcionalidad

# IMPORTANTE: Nunca versionar
# - config/conexion.json
# - public/logs/
# - public/documentos/
```

---

## ��� CONVENCIONES DE COMMITS

```bash
feat: Nueva funcionalidad
fix: Corrección de bug
docs: Documentación
style: Formato, punto y coma, etc.
refactor: Refactorización de código
test: Añadir tests
chore: Actualizar dependencias
```

---

## ��� RECURSOS ADICIONALES

- **Documentación PHP PDO**: https://www.php.net/manual/es/book.pdo.php
- **Bootstrap 5**: https://getbootstrap.com/docs/5.0/
- **DataTables**: https://datatables.net/
- **SweetAlert2**: https://sweetalert2.github.io/
- **jQuery**: https://api.jquery.com/

---

## ��� NOTAS FINALES

- **NO usar frameworks PHP**: El proyecto usa PHP puro con MVC
- **NO usar ORMs**: Todas las consultas son SQL directo con PDO
- **Preferir vistas SQL** para consultas complejas con múltiples JOINs
- **SIEMPRE** usar prepared statements
- **NUNCA** hacer DELETE físico, usar soft delete (activo=0)
- **Logging obligatorio** en operaciones críticas
- **Zona horaria Europe/Madrid** configurada en todos los modelos

---

**Última actualización**: 18 de diciembre de 2024  
**Versión**: 1.0  
**Proyecto**: MDR ERP Manager  
**Autor**: Luis - Innovabyte
# Instrucciones de Desarrollo - MDR ERP Manager

> Sistema ERP para gestión de alquiler de equipos audiovisuales  
> Arquitectura MVC con PHP 8+ y MySQL/MariaDB

---

## ��� Stack Tecnológico

- **Backend**: PHP 8.x con PDO (sin frameworks)
- **Base de Datos**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript ES6+, Bootstrap 5, jQuery
- **Patrón**: MVC estricto (Model-View-Controller)
- **Comunicación**: AJAX + JSON
- **Charset**: UTF8MB4 (utf8mb4_spanish_ci)
- **Zona Horaria**: Europe/Madrid

---

## ���️ CONVENCIONES DE BASE DE DATOS

### Nomenclatura de Tablas

**REGLA FUNDAMENTAL**: Tablas en **SINGULAR**, campos con sufijo **_<<nombre_tabla>>**

```sql
-- ✅ CORRECTO
CREATE TABLE cliente (
    id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100),
    telefono_cliente VARCHAR(20),
    activo_cliente BOOLEAN DEFAULT TRUE,
    created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ❌ INCORRECTO
CREATE TABLE clientes (  -- No plural
    id INT,  -- Falta sufijo _cliente
    nombre VARCHAR(100),  -- Falta sufijo _cliente
    activo BOOLEAN  -- Falta sufijo _cliente
);
```

### Campos Obligatorios en TODA Tabla

```sql
-- Estos 4 campos son OBLIGATORIOS en cada tabla:
id_<<tabla>> INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
activo_<<tabla>> BOOLEAN DEFAULT TRUE COMMENT 'Soft delete: TRUE=activo, FALSE=eliminado',
created_at_<<tabla>> TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at_<<tabla>> TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

### Foreign Keys

```sql
-- Siempre con sufijos claros y acciones definidas
CONSTRAINT fk_presupuesto_cliente 
    FOREIGN KEY (id_cliente) 
    REFERENCES cliente(id_cliente)
    ON DELETE RESTRICT 
    ON UPDATE CASCADE
```

### Índices Estándar

```sql
-- Siempre indexar:
INDEX idx_activo_<<tabla>> (activo_<<tabla>>),
INDEX idx_created_<<tabla>> (created_at_<<tabla>>),
-- FK automáticamente indexadas
-- Campos de búsqueda frecuente
```

### Tipos de Datos Estándar

| Uso | Tipo SQL | Ejemplo |
|-----|----------|---------|
| **Dinero** | `DECIMAL(10,2)` | `precio_articulo DECIMAL(10,2)` |
| **Texto corto** | `VARCHAR(100)` | `nombre_cliente VARCHAR(100)` |
| **Texto medio** | `VARCHAR(255)` | `direccion_cliente VARCHAR(255)` |
| **Texto largo** | `TEXT` | `descripcion_articulo TEXT` |
| **Email** | `VARCHAR(100)` | `email_cliente VARCHAR(100)` |
| **Teléfono** | `VARCHAR(20)` | `telefono_cliente VARCHAR(20)` |
| **CIF/NIF** | `VARCHAR(20)` | `nif_empresa VARCHAR(20)` |
| **Código postal** | `VARCHAR(10)` | `cp_cliente VARCHAR(10)` |
| **Boolean** | `BOOLEAN` o `TINYINT(1)` | `activo_cliente BOOLEAN` |
| **Fecha** | `DATE` | `fecha_presupuesto DATE` |
| **Fecha+Hora** | `DATETIME` | `fecha_evento_presupuesto DATETIME` |
| **Timestamp** | `TIMESTAMP` | `created_at_cliente TIMESTAMP` |
| **Enum** | `ENUM('valor1','valor2')` | `tipo_empresa ENUM('real','ficticia')` |
| **Porcentaje** | `DECIMAL(5,2)` | `iva_impuesto DECIMAL(5,2)` |
| **Cantidad** | `INT UNSIGNED` | `cantidad_linea INT UNSIGNED` |
| **ID** | `INT UNSIGNED` | `id_cliente INT UNSIGNED` |

### Configuración de Tabla

```sql
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## ���️ ARQUITECTURA MVC

### Estructura de Directorios

```
MDR/
├── config/
│   ├── conexion.php          ← Clase PDO de conexión
│   ├── conexion.json         ← Credenciales (NO en Git)
│   ├── funciones.php         ← RegistroActividad + helpers
│   └── template/             ← Plantillas compartidas
│
├── models/                   ← Clases de acceso a datos
│   ├── Presupuesto.php
│   ├── Clientes.php
│   ├── Articulo.php
│   └── ...
│
├── controller/               ← Lógica de negocio
│   ├── presupuesto.php
│   ├── cliente.php
│   ├── articulo.php
│   └── ...
│
├── view/                     ← Interfaces de usuario
│   ├── Presupuesto/
│   ├── MntClientes/
│   ├── MntArticulos/
│   └── ...
│
├── public/                   ← Recursos públicos
│   ├── css/
│   ├── js/
│   ├── img/
│   ├── lib/
│   ├── logs/
│   └── documentos/
│
└── BD/                       ← Scripts SQL
    └── claude_MDR            ← Estructura completa BD
```

---

## ��� MODELOS (Models)

### Estructura Estándar de un Modelo

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
        // 1. Inicializar conexión PDO
        $this->conexion = (new Conexion())->getConexion();
        
        // 2. Inicializar registro de actividad
        $this->registro = new RegistroActividad();
        
        // 3. Configurar zona horaria
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'NombreEntidad',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // MÉTODO 1: Listar todos (usando vista SQL si existe)
    public function get_entidades()
    {
        try {
            // Preferir vistas SQL para consultas complejas
            $sql = "SELECT * FROM vista_entidad_completa 
                    WHERE activo_entidad = 1 
                    ORDER BY nombre_entidad ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidades',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // MÉTODO 2: Listar solo activos
    public function get_entidades_disponibles()
    {
        try {
            $sql = "SELECT * FROM entidad 
                    WHERE activo_entidad = 1 
                    ORDER BY nombre_entidad ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidades_disponibles',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // MÉTODO 3: Obtener por ID
    public function get_entidadxid($id_entidad)
    {
        try {
            $sql = "SELECT * FROM entidad 
                    WHERE id_entidad = ? 
                    AND activo_entidad = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 4: Insertar
    public function insert_entidad($nombre, $descripcion, $campo_opcional = null)
    {
        try {
            $sql = "INSERT INTO entidad (
                        nombre_entidad, 
                        descripcion_entidad, 
                        campo_opcional_entidad,
                        created_at_entidad
                    ) VALUES (?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion, PDO::PARAM_STR);
            
            // IMPORTANTE: Manejo de campos opcionales
            if (!empty($campo_opcional)) {
                $stmt->bindValue(3, $campo_opcional, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'insert_entidad',
                "Entidad creada con ID: $id",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'insert_entidad',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 5: Actualizar
    public function update_entidad($id_entidad, $nombre, $descripcion, $campo_opcional = null)
    {
        try {
            $sql = "UPDATE entidad SET 
                        nombre_entidad = ?,
                        descripcion_entidad = ?,
                        campo_opcional_entidad = ?,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion, PDO::PARAM_STR);
            
            if (!empty($campo_opcional)) {
                $stmt->bindValue(3, $campo_opcional, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(4, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'update_entidad',
                "Entidad actualizada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'update_entidad',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 6: Eliminar (SOFT DELETE)
    public function delete_entidadxid($id_entidad)
    {
        try {
            // NO usar DELETE físico, usar soft delete
            $sql = "UPDATE entidad SET 
                        activo_entidad = 0,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'delete_entidadxid',
                "Entidad desactivada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'delete_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 7: Activar (restaurar soft delete)
    public function activar_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE entidad SET 
                        activo_entidad = 1,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'activar_entidadxid',
                "Entidad activada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'activar_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 8: Verificar existencia (validación unicidad)
    public function verificarEntidad($campo_unico, $id_entidad = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM entidad 
                    WHERE LOWER(campo_unico_entidad) = LOWER(?)";
            $params = [trim($campo_unico)];
            
            // Excluir el propio registro en edición
            if (!empty($id_entidad)) {
                $sql .= " AND id_entidad != ?";
                $params[] = $id_entidad;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'existe' => ($resultado['total'] > 0)
            ];
            
        } catch (PDOException $e) {
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>
```

### Métodos Estándar (todos los modelos)

1. ✅ `__construct()` - Inicialización con PDO y RegistroActividad
2. ✅ `get_entidades()` - Listar todos
3. ✅ `get_entidades_disponibles()` - Listar solo activos
4. ✅ `get_entidadxid($id)` - Obtener por ID
5. ✅ `insert_entidad(...)` - Insertar nuevo registro
6. ✅ `update_entidad($id, ...)` - Actualizar registro
7. ✅ `delete_entidadxid($id)` - Soft delete (activo=0)
8. ✅ `activar_entidadxid($id)` - Reactivar (activo=1)
9. ✅ `verificarEntidad($campo, $id)` - Validar unicidad

### Métodos NO Estándar (según necesidad)

- `obtenerEstadisticas()` - Solo cuando se necesitan dashboards/métricas
- `get_entidades_por_categoria($id_categoria)` - Filtros específicos
- Métodos personalizados según lógica de negocio

---

## ��� CONTROLADORES (Controllers)

### Estructura Estándar de un Controller

```php
<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/NombreEntidad.php";

// Inicializar clases
$registro = new RegistroActividad();
$entidad = new NombreEntidad();

// Switch principal basado en operación
switch ($_GET["op"]) {
    
    case "listar":
        // Para DataTables
        $datos = $entidad->get_entidades();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_entidad" => $row["id_entidad"],
                "nombre_entidad" => $row["nombre_entidad"],
                "descripcion_entidad" => $row["descripcion_entidad"],
                "activo_entidad" => $row["activo_entidad"],
                "opciones" => '<button class="btn btn-warning btn-sm" onclick="mostrar('.$row["id_entidad"].')">
                                  <i class="fa fa-edit"></i>
                               </button>
                               <button class="btn btn-danger btn-sm" onclick="desactivar('.$row["id_entidad"].')">
                                  <i class="fa fa-trash"></i>
                               </button>'
            );
        }
        
        $results = array(
            "draw" => intval($_POST['draw'] ?? 1),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
    
    case "guardaryeditar":
        // Validar si es INSERT o UPDATE
        $id_entidad = $_POST["id_entidad"] ?? null;
        
        // Sanitizar datos
        $nombre = htmlspecialchars(trim($_POST["nombre_entidad"]), ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars(trim($_POST["descripcion_entidad"]), ENT_QUOTES, 'UTF-8');
        
        // Campos opcionales: convertir vacío a null
        $campo_opcional = !empty($_POST["campo_opcional"]) ? $_POST["campo_opcional"] : null;
        
        try {
            if (empty($id_entidad)) {
                // INSERT
                $resultado = $entidad->insert_entidad($nombre, $descripcion, $campo_opcional);
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro creado correctamente',
                        'id_entidad' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al crear el registro'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // UPDATE
                $resultado = $entidad->update_entidad($id_entidad, $nombre, $descripcion, $campo_opcional);
                
                if ($resultado !== false) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro actualizado correctamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar el registro'
                    ], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin',
                'entidad.php',
                'guardaryeditar',
                "Error: " . $e->getMessage(),
                'error'
            );
            
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "mostrar":
        // Obtener registro por ID para edición
        $id_entidad = $_POST["id_entidad"];
        $datos = $entidad->get_entidadxid($id_entidad);
        
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "desactivar":
        // Soft delete
        $id_entidad = $_POST["id_entidad"];
        $resultado = $entidad->delete_entidadxid($id_entidad);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Registro desactivado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar el registro'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "activar":
        // Reactivar
        $id_entidad = $_POST["id_entidad"];
        $resultado = $entidad->activar_entidadxid($id_entidad);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Registro activado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar el registro'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "verificar":
        // Validar unicidad de campo
        $campo_unico = $_POST["campo_unico"];
        $id_entidad = $_POST["id_entidad"] ?? null;
        
        $resultado = $entidad->verificarEntidad($campo_unico, $id_entidad);
        
        if (!isset($resultado['success'])) {
            $resultado['success'] = !isset($resultado['error']);
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
    
    case "listar_disponibles":
        // Solo registros activos
        $datos = $entidad->get_entidades_disponibles();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_entidad" => $row["id_entidad"],
                "nombre_entidad" => $row["nombre_entidad"]
            );
        }
        
        $results = array(
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

### Operaciones Estándar (switch cases)

1. ✅ `listar` - Listado completo para DataTables
2. ✅ `guardaryeditar` - INSERT o UPDATE según id
3. ✅ `mostrar` - Obtener registro para edición
4. ✅ `desactivar` - Soft delete
5. ✅ `activar` - Reactivar registro
6. ✅ `verificar` - Validar unicidad
7. ✅ `listar_disponibles` - Solo activos

---

## ��� SEGURIDAD

### Prepared Statements (SIEMPRE)

```php
// ✅ CORRECTO: Prepared statement con bindValue
$sql = "SELECT * FROM cliente WHERE email_cliente = ?";
$stmt = $this->conexion->prepare($sql);
$stmt->bindValue(1, $email, PDO::PARAM_STR);
$stmt->execute();

// ✅ CORRECTO: Prepared statement con array de parámetros
$sql = "INSERT INTO cliente (nombre_cliente, email_cliente) VALUES (?, ?)";
$stmt = $this->conexion->prepare($sql);
$stmt->execute([$nombre, $email]);

// ❌ PROHIBIDO: Concatenación directa (SQL Injection)
$sql = "SELECT * FROM cliente WHERE email_cliente = '$email'";
$resultado = $this->conexion->query($sql);
```

### Sanitización de Inputs

```php
// ✅ Sanitizar SIEMPRE en controllers
$nombre = htmlspecialchars(trim($_POST["nombre"]), ENT_QUOTES, 'UTF-8');
$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
$telefono = preg_replace('/[^0-9+]/', '', $_POST["telefono"]);

// Validar formato
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Email inválido']);
    exit;
}
```

### Manejo de Campos Opcionales

```php
// ✅ CORRECTO: Convertir vacío a NULL
$id_contacto = !empty($_POST["id_contacto"]) ? $_POST["id_contacto"] : null;

if (!empty($id_contacto)) {
    $stmt->bindValue(3, $id_contacto, PDO::PARAM_INT);
} else {
    $stmt->bindValue(3, null, PDO::PARAM_NULL);
}

// ❌ INCORRECTO: No validar antes de insertar
$stmt->bindValue(3, $_POST["id_contacto"], PDO::PARAM_INT);
```

### Tipos de Datos en bindValue()

```php
// Tipos PDO estándar
PDO::PARAM_INT    // Enteros: IDs, cantidades
PDO::PARAM_STR    // Cadenas: nombres, descripciones
PDO::PARAM_BOOL   // Booleanos: activo, visible
PDO::PARAM_NULL   // NULL explícito
```

### Manejo de Errores

```php
// ✅ CORRECTO: Try-catch con logging
try {
    // Operación de BD
    $resultado = $modelo->insert_entidad(...);
    
} catch (PDOException $e) {
    $this->registro->registrarActividad(
        'admin',
        'Controller',
        'operacion',
        "Error: " . $e->getMessage(),
        'error'
    );
    
    // Mensaje genérico al usuario (NO exponer detalles del error)
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud'
    ]);
}
```

---

## ��� CONEXIÓN A BASE DE DATOS

### Clase Conexion.php

```php
<?php

class Conexion
{
    private $pdo;

    public function __construct()
    {
        // Leer credenciales desde JSON externo
        $config_file = __DIR__ . '/conexion.json';
        
        if (!file_exists($config_file)) {
            throw new Exception("Error: El archivo de configuración no existe");
        }

        $config_json = file_get_contents($config_file);
        $config = json_decode($config_json, true);

        if ($config === null) {
            throw new Exception("Error: No se pudo parsear el archivo de configuración");
        }

        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion()
    {
        return $this->pdo;
    }
}
?>
```

### Archivo conexion.json (NO versionar en Git)

```json
{
    "host": "217.154.117.83",
    "port": "3308",
    "user": "administrator",
    "password": "27979699",
    "database": "toldos_db",
    "charset": "utf8mb4"
}
```

### Uso en Modelos

```php
// En constructor del modelo
$this->conexion = (new Conexion())->getConexion();

// Usar PDO normalmente
$stmt = $this->conexion->prepare("SELECT ...");
```

---

## ��� SISTEMA DE LOGGING

### Clase RegistroActividad

```php
// Ubicación: config/funciones.php

class RegistroActividad
{
    private $directorio = '../public/logs/';

    public function registrarActividad($usuario, $pantalla, $actividad, $mensaje, $tipo = 'info')
    {
        // Crear archivo JSON diario: YYYY-MM-DD.json
        $fecha = date('Y-m-d');
        $archivo = $this->directorio . $fecha . '.json';
        
        // Crear entrada de log
        $registro = [
            'usuario' => $usuario,
            'pantalla' => $pantalla,
            'actividad' => $actividad,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'fecha_hora' => date('Y-m-d H:i:s')
        ];
        
        // Leer logs existentes
        if (file_exists($archivo)) {
            $logs = json_decode(file_get_contents($archivo), true);
        } else {
            $logs = [];
        }
        
        // Añadir nuevo registro
        $logs[] = $registro;
        
        // Guardar
        file_put_contents($archivo, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
```

### Uso en Modelos y Controllers

```php
// Éxito
$this->registro->registrarActividad(
    'admin',
    'Presupuesto',
    'insert_presupuesto',
    "Presupuesto creado con ID: $id",
    'info'
);

// Error
$this->registro->registrarActividad(
    'admin',
    'Presupuesto',
    'insert_presupuesto',
    "Error: " . $e->getMessage(),
    'error'
);

// Advertencia
$this->registro->registrarActividad(
    'system',
    'Conexion',
    '__construct',
    "Zona horaria no configurada",
    'warning'
);
```

---

## ��� VISTAS (Views)

### Estructura HTML con Bootstrap 5

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Entidades - MDR</title>
    
    <!-- Bootstrap 5 -->
    <link href="../../public/lib/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="../../public/lib/DataTables/datatables.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="../../public/lib/fontawesome-6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="../../public/lib/sweetalert2-11.7.32/sweetalert2.min.css" rel="stylesheet">
    
    <!-- CSS personalizado -->
    <link href="../../public/css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Header y navegación -->
    <?php include '../template/header.php'; ?>
    <?php include '../template/sidebar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Entidades</h1>
                
                <!-- Botón crear -->
                <button class="btn btn-primary" onclick="mostrarFormulario()">
                    <i class="fa fa-plus"></i> Nueva Entidad
                </button>
                
                <!-- Tabla DataTables -->
                <table id="tblEntidades" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se llena vía AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal formulario -->
    <div class="modal fade" id="modalFormulario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="frmEntidad">
                    <div class="modal-header">
                        <h5 class="modal-title">Formulario de Entidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_entidad" id="id_entidad">
                        
                        <div class="mb-3">
                            <label for="nombre_entidad" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre_entidad" name="nombre_entidad" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion_entidad" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_entidad" name="descripcion_entidad" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="../../public/lib/jquery-3.7.1/jquery.min.js"></script>
    <script src="../../public/lib/bootstrap-5.0.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/lib/DataTables/datatables.min.js"></script>
    <script src="../../public/lib/sweetalert2-11.7.32/sweetalert2.all.min.js"></script>
    
    <!-- Script específico de la vista -->
    <script src="entidad.js"></script>
</body>
</html>
```

### JavaScript (entidad.js)

```javascript
let tabla;

$(document).ready(function() {
    // Inicializar DataTable
    tabla = $('#tblEntidades').DataTable({
        ajax: {
            url: '../../controller/entidad.php?op=listar',
            type: 'POST',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_entidad' },
            { data: 'nombre_entidad' },
            { data: 'descripcion_entidad' },
            { 
                data: 'activo_entidad',
                render: function(data) {
                    return data == 1 
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                }
            },
            { data: 'opciones', orderable: false }
        ],
        language: {
            url: '../../public/lib/DataTables/es-ES.json'
        },
        responsive: true,
        order: [[0, 'desc']]
    });
    
    // Submit formulario
    $('#frmEntidad').on('submit', function(e) {
        e.preventDefault();
        guardaryeditar();
    });
});

function mostrarFormulario() {
    $('#frmEntidad')[0].reset();
    $('#id_entidad').val('');
    $('#modalFormulario').modal('show');
}

function mostrar(id) {
    $.post('../../controller/entidad.php?op=mostrar', { id_entidad: id })
        .done(function(data) {
            $('#id_entidad').val(data.id_entidad);
            $('#nombre_entidad').val(data.nombre_entidad);
            $('#descripcion_entidad').val(data.descripcion_entidad);
            $('#modalFormulario').modal('show');
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo cargar el registro', 'error');
        });
}

function guardaryeditar() {
    let formData = $('#frmEntidad').serialize();
    
    $.post('../../controller/entidad.php?op=guardaryeditar', formData)
        .done(function(response) {
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success');
                $('#modalFormulario').modal('hide');
                tabla.ajax.reload();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
        });
}

function desactivar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción desactivará el registro",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../../controller/entidad.php?op=desactivar', { id_entidad: id })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Desactivado', response.message, 'success');
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                });
        }
    });
}

function activar(id) {
    $.post('../../controller/entidad.php?op=activar', { id_entidad: id })
        .done(function(response) {
            if (response.success) {
                Swal.fire('Activado', response.message, 'success');
                tabla.ajax.reload();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        });
}
```

---

## ��� PATRONES DE RESPUESTA JSON

### Respuestas de Controllers

```javascript
// Éxito en operación
{
    "success": true,
    "message": "Operación realizada correctamente",
    "id_entidad": 42  // Opcional: ID del registro creado
}

// Error en operación
{
    "success": false,
    "message": "Descripción del error"
}

// Listado para DataTables
{
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 100,
    "data": [
        { "id_entidad": 1, "nombre_entidad": "...", ... },
        { "id_entidad": 2, "nombre_entidad": "...", ... }
    ]
}

// Verificación de existencia
{
    "existe": true  // o false
}

// Obtener registro por ID
{
    "id_entidad": 1,
    "nombre_entidad": "...",
    "descripcion_entidad": "...",
    "activo_entidad": 1
}
```

---

## ��� TRIGGERS (Disparadores)

### Patrón de Generación de Códigos Correlativos

```sql
-- Trigger para generar código automático
DELIMITER $$

CREATE TRIGGER trg_elemento_before_insert
BEFORE INSERT ON elemento
FOR EACH ROW
BEGIN
    DECLARE siguiente_numero INT;
    
    -- Obtener el siguiente número para este artículo
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(codigo_elemento, '-', -1) AS UNSIGNED)), 0) + 1
    INTO siguiente_numero
    FROM elemento
    WHERE id_articulo = NEW.id_articulo;
    
    -- Asignar el código formato: ARTICULO-001
    SET NEW.codigo_elemento = CONCAT(
        (SELECT UPPER(nombre_articulo) FROM articulo WHERE id_articulo = NEW.id_articulo),
        '-',
        LPAD(siguiente_numero, 3, '0')
    );
END$$

DELIMITER ;
```

### Patrón de Sincronización de Estados

```sql
-- Sincronizar campo activo con estado
DELIMITER $$

CREATE TRIGGER trg_presupuesto_before_desactivar
BEFORE UPDATE ON presupuesto
FOR EACH ROW
BEGIN
    -- Si se desactiva, marcar como CANCELADO
    IF NEW.activo_presupuesto = 0 AND OLD.activo_presupuesto = 1 THEN
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'CANCELADO'
        );
    END IF;
END$$

DELIMITER ;
```

### Patrón de Validación con Error

```sql
-- Validar regla de negocio
DELIMITER $$

CREATE TRIGGER trg_empresa_validar_ficticia_principal
BEFORE INSERT ON empresa
FOR EACH ROW
BEGIN
    DECLARE existe_principal INT;
    
    -- Si intenta crear empresa ficticia principal
    IF NEW.ficticia_empresa = 1 AND NEW.empresa_ficticia_principal = 1 THEN
        
        -- Verificar si ya existe una
        SELECT COUNT(*) INTO existe_principal
        FROM empresa
        WHERE ficticia_empresa = 1 
        AND empresa_ficticia_principal = 1
        AND activo_empresa = 1;
        
        -- Si existe, lanzar error
        IF existe_principal > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Ya existe una empresa ficticia principal activa';
        END IF;
    END IF;
END$$

DELIMITER ;
```

---

## ��� VISTAS SQL

### Patrón de Vista Completa con JOINs

```sql
-- Vista que consolida información de múltiples tablas
CREATE OR REPLACE VIEW vista_presupuesto_completa AS
SELECT 
    -- Campos de presupuesto
    p.id_presupuesto,
    p.numero_presupuesto,
    p.fecha_presupuesto,
    p.fecha_validez_presupuesto,
    p.nombre_evento_presupuesto,
    p.activo_presupuesto,
    p.created_at_presupuesto,
    p.updated_at_presupuesto,
    
    -- Datos del cliente
    c.id_cliente,
    c.nombre_cliente,
    c.apellido_cliente,
    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS nombre_completo_cliente,
    c.email_cliente,
    c.telefono_cliente,
    
    -- Datos del contacto (puede ser NULL)
    cc.id_contacto_cliente,
    cc.nombre_contacto_cliente,
    cc.telefono_contacto_cliente,
    
    -- Estado del presupuesto
    ep.id_estado_ppto,
    ep.nombre_estado_ppto,
    ep.codigo_estado_ppto,
    ep.color_estado_ppto,
    
    -- Forma de pago
    fp.id_forma_pago,
    fp.nombre_forma_pago,
    
    -- Método de pago
    m.id_metodo,
    m.nombre_metodo,
    
    -- Totales calculados (si los tienes en la tabla)
    p.subtotal_presupuesto,
    p.total_iva_presupuesto,
    p.total_presupuesto

FROM presupuesto p

INNER JOIN cliente c 
    ON p.id_cliente = c.id_cliente

LEFT JOIN contacto_cliente cc 
    ON p.id_contacto_cliente = cc.id_contacto_cliente

INNER JOIN estado_presupuesto ep 
    ON p.id_estado_ppto = ep.id_estado_ppto

LEFT JOIN forma_pago fp 
    ON p.id_forma_pago = fp.id_forma_pago

LEFT JOIN metodo m 
    ON p.id_metodo = m.id_metodo

WHERE p.activo_presupuesto = 1
ORDER BY p.fecha_presupuesto DESC;
```

### Uso en Modelos

```php
// Preferir vistas para consultas complejas
public function get_presupuestos()
{
    $sql = "SELECT * FROM vista_presupuesto_completa 
            ORDER BY fecha_presupuesto DESC";
    
    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Usar tablas directamente solo para INSERT/UPDATE/DELETE
public function insert_presupuesto(...)
{
    $sql = "INSERT INTO presupuesto (...) VALUES (...)";
    // ...
}
```

---

## ✅ CHECKLIST DE BUENAS PRÁCTICAS

### Base de Datos
- [ ] Tablas en SINGULAR
- [ ] Todos los campos con sufijo _<<tabla>>
- [ ] Campos obligatorios: id, activo, created_at, updated_at
- [ ] Soft delete (activo=0) en lugar de DELETE físico
- [ ] Foreign keys con ON DELETE/UPDATE definidos
- [ ] Índices en campos de búsqueda frecuente
- [ ] Charset utf8mb4_spanish_ci

### Modelos
- [ ] Constructor con PDO y RegistroActividad
- [ ] Zona horaria configurada a Europe/Madrid
- [ ] Prepared statements en TODAS las consultas
- [ ] Try-catch en todos los métodos
- [ ] Logging de errores y acciones importantes
- [ ] Validación de campos opcionales (null)
- [ ] Retornos consistentes (ID, rowCount, boolean, array)
- [ ] Métodos estándar implementados

### Controllers
- [ ] Switch por operación ($_GET["op"])
- [ ] Sanitización de inputs
- [ ] Conversión de vacíos a null
- [ ] Respuestas JSON con JSON_UNESCAPED_UNICODE
- [ ] Headers Content-Type correctos
- [ ] Try-catch en operaciones críticas
- [ ] Logging con RegistroActividad

### Vistas
- [ ] HTML5 semántico
- [ ] Bootstrap 5 para diseño
- [ ] DataTables para listados
- [ ] SweetAlert2 para confirmaciones
- [ ] Sin lógica de negocio
- [ ] Validación client-side (complementaria)
- [ ] AJAX para comunicación con controllers

### Seguridad
- [ ] Prepared statements siempre
- [ ] Sanitización de inputs
- [ ] Validación de tipos de datos
- [ ] No exponer detalles de errores SQL
- [ ] credenciales en JSON externo
- [ ] Logging de errores
- [ ] CSRF tokens (si aplica)

---

## ��� EJEMPLOS COMPLETOS

### Ejemplo 1: Tabla Cliente

```sql
CREATE TABLE cliente (
    -- Identificación
    id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_cliente VARCHAR(20) NOT NULL UNIQUE 
        COMMENT 'Código único del cliente',
    
    -- Datos personales
    nombre_cliente VARCHAR(100) NOT NULL,
    apellido_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100),
    telefono_cliente VARCHAR(20),
    movil_cliente VARCHAR(20),
    
    -- Datos fiscales
    nif_cliente VARCHAR(20),
    tipo_cliente ENUM('particular', 'empresa') DEFAULT 'particular',
    
    -- Dirección
    direccion_cliente VARCHAR(255),
    poblacion_cliente VARCHAR(100),
    provincia_cliente VARCHAR(100),
    cp_cliente VARCHAR(10),
    pais_cliente VARCHAR(100) DEFAULT 'España',
    
    -- Campos obligatorios
    activo_cliente BOOLEAN DEFAULT TRUE,
    created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_codigo_cliente (codigo_cliente),
    INDEX idx_nombre_cliente (nombre_cliente),
    INDEX idx_email_cliente (email_cliente),
    INDEX idx_activo_cliente (activo_cliente)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Gestión de clientes del sistema';
```

### Ejemplo 2: Modelo Completo Clientes.php

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Clientes
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'Clientes',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_clientes()
    {
        try {
            $sql = "SELECT * FROM cliente 
                    WHERE activo_cliente = 1 
                    ORDER BY nombre_cliente ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'get_clientes',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    public function insert_cliente(
        $codigo, $nombre, $apellido, $email = null, 
        $telefono = null, $nif = null, $tipo = 'particular',
        $direccion = null, $poblacion = null, $provincia = null, $cp = null
    ) {
        try {
            $sql = "INSERT INTO cliente (
                        codigo_cliente, nombre_cliente, apellido_cliente,
                        email_cliente, telefono_cliente, nif_cliente, tipo_cliente,
                        direccion_cliente, poblacion_cliente, provincia_cliente, cp_cliente,
                        created_at_cliente
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(3, $apellido, PDO::PARAM_STR);
            
            // Campos opcionales
            $stmt->bindValue(4, $email, !empty($email) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(5, $telefono, !empty($telefono) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(6, $nif, !empty($nif) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(7, $tipo, PDO::PARAM_STR);
            $stmt->bindValue(8, $direccion, !empty($direccion) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9, $poblacion, !empty($poblacion) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(10, $provincia, !empty($provincia) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, $cp, !empty($cp) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'insert_cliente',
                "Cliente creado con ID: $id - $nombre $apellido",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'insert_cliente',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    public function verificarCliente($codigo, $id_cliente = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM cliente 
                    WHERE LOWER(codigo_cliente) = LOWER(?)";
            $params = [trim($codigo)];
            
            if (!empty($id_cliente)) {
                $sql .= " AND id_cliente != ?";
                $params[] = $id_cliente;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ['existe' => ($resultado['total'] > 0)];
            
        } catch (PDOException $e) {
            return ['existe' => false, 'error' => $e->getMessage()];
        }
    }
}
?>
```

---

## ��� COMANDOS GIT

```bash
# Clonar repositorio
git clone https://github.com/luisInnovabyte/MDR.git

# Actualizar desde remoto
git pull origin main

# Ver estado
git status

# Añadir cambios
git add .

# Commit
git commit -m "feat: descripción del cambio"

# Push
git push origin main

# Crear rama
git checkout -b feature/nueva-funcionalidad

# IMPORTANTE: Nunca versionar
# - config/conexion.json
# - public/logs/
# - public/documentos/
```

---

## ��� CONVENCIONES DE COMMITS

```bash
feat: Nueva funcionalidad
fix: Corrección de bug
docs: Documentación
style: Formato, punto y coma, etc.
refactor: Refactorización de código
test: Añadir tests
chore: Actualizar dependencias
```

---

## ��� RECURSOS ADICIONALES

- **Documentación PHP PDO**: https://www.php.net/manual/es/book.pdo.php
- **Bootstrap 5**: https://getbootstrap.com/docs/5.0/
- **DataTables**: https://datatables.net/
- **SweetAlert2**: https://sweetalert2.github.io/
- **jQuery**: https://api.jquery.com/

---

## ��� NOTAS FINALES

- **NO usar frameworks PHP**: El proyecto usa PHP puro con MVC
- **NO usar ORMs**: Todas las consultas son SQL directo con PDO
- **Preferir vistas SQL** para consultas complejas con múltiples JOINs
- **SIEMPRE** usar prepared statements
- **NUNCA** hacer DELETE físico, usar soft delete (activo=0)
- **Logging obligatorio** en operaciones críticas
- **Zona horaria Europe/Madrid** configurada en todos los modelos

---

**Última actualización**: 18 de diciembre de 2024  
**Versión**: 1.0  
**Proyecto**: MDR ERP Manager  
**Autor**: Luis - Innovabyte
# Instrucciones de Desarrollo - MDR ERP Manager

> Sistema ERP para gestión de alquiler de equipos audiovisuales  
> Arquitectura MVC con PHP 8+ y MySQL/MariaDB

---

## ��� Stack Tecnológico

- **Backend**: PHP 8.x con PDO (sin frameworks)
- **Base de Datos**: MySQL/MariaDB
- **Frontend**: HTML5, CSS3, JavaScript ES6+, Bootstrap 5, jQuery
- **Patrón**: MVC estricto (Model-View-Controller)
- **Comunicación**: AJAX + JSON
- **Charset**: UTF8MB4 (utf8mb4_spanish_ci)
- **Zona Horaria**: Europe/Madrid

---

## ���️ CONVENCIONES DE BASE DE DATOS

### Nomenclatura de Tablas

**REGLA FUNDAMENTAL**: Tablas en **SINGULAR**, campos con sufijo **_<<nombre_tabla>>**

```sql
-- ✅ CORRECTO
CREATE TABLE cliente (
    id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100),
    telefono_cliente VARCHAR(20),
    activo_cliente BOOLEAN DEFAULT TRUE,
    created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- ❌ INCORRECTO
CREATE TABLE clientes (  -- No plural
    id INT,  -- Falta sufijo _cliente
    nombre VARCHAR(100),  -- Falta sufijo _cliente
    activo BOOLEAN  -- Falta sufijo _cliente
);
```

### Campos Obligatorios en TODA Tabla

```sql
-- Estos 4 campos son OBLIGATORIOS en cada tabla:
id_<<tabla>> INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
activo_<<tabla>> BOOLEAN DEFAULT TRUE COMMENT 'Soft delete: TRUE=activo, FALSE=eliminado',
created_at_<<tabla>> TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at_<<tabla>> TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

### Foreign Keys

```sql
-- Siempre con sufijos claros y acciones definidas
CONSTRAINT fk_presupuesto_cliente 
    FOREIGN KEY (id_cliente) 
    REFERENCES cliente(id_cliente)
    ON DELETE RESTRICT 
    ON UPDATE CASCADE
```

### Índices Estándar

```sql
-- Siempre indexar:
INDEX idx_activo_<<tabla>> (activo_<<tabla>>),
INDEX idx_created_<<tabla>> (created_at_<<tabla>>),
-- FK automáticamente indexadas
-- Campos de búsqueda frecuente
```

### Tipos de Datos Estándar

| Uso | Tipo SQL | Ejemplo |
|-----|----------|---------|
| **Dinero** | `DECIMAL(10,2)` | `precio_articulo DECIMAL(10,2)` |
| **Texto corto** | `VARCHAR(100)` | `nombre_cliente VARCHAR(100)` |
| **Texto medio** | `VARCHAR(255)` | `direccion_cliente VARCHAR(255)` |
| **Texto largo** | `TEXT` | `descripcion_articulo TEXT` |
| **Email** | `VARCHAR(100)` | `email_cliente VARCHAR(100)` |
| **Teléfono** | `VARCHAR(20)` | `telefono_cliente VARCHAR(20)` |
| **CIF/NIF** | `VARCHAR(20)` | `nif_empresa VARCHAR(20)` |
| **Código postal** | `VARCHAR(10)` | `cp_cliente VARCHAR(10)` |
| **Boolean** | `BOOLEAN` o `TINYINT(1)` | `activo_cliente BOOLEAN` |
| **Fecha** | `DATE` | `fecha_presupuesto DATE` |
| **Fecha+Hora** | `DATETIME` | `fecha_evento_presupuesto DATETIME` |
| **Timestamp** | `TIMESTAMP` | `created_at_cliente TIMESTAMP` |
| **Enum** | `ENUM('valor1','valor2')` | `tipo_empresa ENUM('real','ficticia')` |
| **Porcentaje** | `DECIMAL(5,2)` | `iva_impuesto DECIMAL(5,2)` |
| **Cantidad** | `INT UNSIGNED` | `cantidad_linea INT UNSIGNED` |
| **ID** | `INT UNSIGNED` | `id_cliente INT UNSIGNED` |

### Configuración de Tabla

```sql
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## ���️ ARQUITECTURA MVC

### Estructura de Directorios

```
MDR/
├── config/
│   ├── conexion.php          ← Clase PDO de conexión
│   ├── conexion.json         ← Credenciales (NO en Git)
│   ├── funciones.php         ← RegistroActividad + helpers
│   └── template/             ← Plantillas compartidas
│
├── models/                   ← Clases de acceso a datos
│   ├── Presupuesto.php
│   ├── Clientes.php
│   ├── Articulo.php
│   └── ...
│
├── controller/               ← Lógica de negocio
│   ├── presupuesto.php
│   ├── cliente.php
│   ├── articulo.php
│   └── ...
│
├── view/                     ← Interfaces de usuario
│   ├── Presupuesto/
│   ├── MntClientes/
│   ├── MntArticulos/
│   └── ...
│
├── public/                   ← Recursos públicos
│   ├── css/
│   ├── js/
│   ├── img/
│   ├── lib/
│   ├── logs/
│   └── documentos/
│
└── BD/                       ← Scripts SQL
    └── claude_MDR            ← Estructura completa BD
```

---

## ��� MODELOS (Models)

### Estructura Estándar de un Modelo

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
        // 1. Inicializar conexión PDO
        $this->conexion = (new Conexion())->getConexion();
        
        // 2. Inicializar registro de actividad
        $this->registro = new RegistroActividad();
        
        // 3. Configurar zona horaria
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'NombreEntidad',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // MÉTODO 1: Listar todos (usando vista SQL si existe)
    public function get_entidades()
    {
        try {
            // Preferir vistas SQL para consultas complejas
            $sql = "SELECT * FROM vista_entidad_completa 
                    WHERE activo_entidad = 1 
                    ORDER BY nombre_entidad ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidades',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // MÉTODO 2: Listar solo activos
    public function get_entidades_disponibles()
    {
        try {
            $sql = "SELECT * FROM entidad 
                    WHERE activo_entidad = 1 
                    ORDER BY nombre_entidad ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidades_disponibles',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // MÉTODO 3: Obtener por ID
    public function get_entidadxid($id_entidad)
    {
        try {
            $sql = "SELECT * FROM entidad 
                    WHERE id_entidad = ? 
                    AND activo_entidad = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 4: Insertar
    public function insert_entidad($nombre, $descripcion, $campo_opcional = null)
    {
        try {
            $sql = "INSERT INTO entidad (
                        nombre_entidad, 
                        descripcion_entidad, 
                        campo_opcional_entidad,
                        created_at_entidad
                    ) VALUES (?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion, PDO::PARAM_STR);
            
            // IMPORTANTE: Manejo de campos opcionales
            if (!empty($campo_opcional)) {
                $stmt->bindValue(3, $campo_opcional, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'insert_entidad',
                "Entidad creada con ID: $id",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'insert_entidad',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 5: Actualizar
    public function update_entidad($id_entidad, $nombre, $descripcion, $campo_opcional = null)
    {
        try {
            $sql = "UPDATE entidad SET 
                        nombre_entidad = ?,
                        descripcion_entidad = ?,
                        campo_opcional_entidad = ?,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(2, $descripcion, PDO::PARAM_STR);
            
            if (!empty($campo_opcional)) {
                $stmt->bindValue(3, $campo_opcional, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(4, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'update_entidad',
                "Entidad actualizada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'update_entidad',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 6: Eliminar (SOFT DELETE)
    public function delete_entidadxid($id_entidad)
    {
        try {
            // NO usar DELETE físico, usar soft delete
            $sql = "UPDATE entidad SET 
                        activo_entidad = 0,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'delete_entidadxid',
                "Entidad desactivada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'delete_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 7: Activar (restaurar soft delete)
    public function activar_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE entidad SET 
                        activo_entidad = 1,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'activar_entidadxid',
                "Entidad activada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'activar_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // MÉTODO 8: Verificar existencia (validación unicidad)
    public function verificarEntidad($campo_unico, $id_entidad = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM entidad 
                    WHERE LOWER(campo_unico_entidad) = LOWER(?)";
            $params = [trim($campo_unico)];
            
            // Excluir el propio registro en edición
            if (!empty($id_entidad)) {
                $sql .= " AND id_entidad != ?";
                $params[] = $id_entidad;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return [
                'existe' => ($resultado['total'] > 0)
            ];
            
        } catch (PDOException $e) {
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>
```

### Métodos Estándar (todos los modelos)

1. ✅ `__construct()` - Inicialización con PDO y RegistroActividad
2. ✅ `get_entidades()` - Listar todos
3. ✅ `get_entidades_disponibles()` - Listar solo activos
4. ✅ `get_entidadxid($id)` - Obtener por ID
5. ✅ `insert_entidad(...)` - Insertar nuevo registro
6. ✅ `update_entidad($id, ...)` - Actualizar registro
7. ✅ `delete_entidadxid($id)` - Soft delete (activo=0)
8. ✅ `activar_entidadxid($id)` - Reactivar (activo=1)
9. ✅ `verificarEntidad($campo, $id)` - Validar unicidad

### Métodos NO Estándar (según necesidad)

- `obtenerEstadisticas()` - Solo cuando se necesitan dashboards/métricas
- `get_entidades_por_categoria($id_categoria)` - Filtros específicos
- Métodos personalizados según lógica de negocio

---

## ��� CONTROLADORES (Controllers)

### Estructura Estándar de un Controller

```php
<?php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/NombreEntidad.php";

// Inicializar clases
$registro = new RegistroActividad();
$entidad = new NombreEntidad();

// Switch principal basado en operación
switch ($_GET["op"]) {
    
    case "listar":
        // Para DataTables
        $datos = $entidad->get_entidades();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_entidad" => $row["id_entidad"],
                "nombre_entidad" => $row["nombre_entidad"],
                "descripcion_entidad" => $row["descripcion_entidad"],
                "activo_entidad" => $row["activo_entidad"],
                "opciones" => '<button class="btn btn-warning btn-sm" onclick="mostrar('.$row["id_entidad"].')">
                                  <i class="fa fa-edit"></i>
                               </button>
                               <button class="btn btn-danger btn-sm" onclick="desactivar('.$row["id_entidad"].')">
                                  <i class="fa fa-trash"></i>
                               </button>'
            );
        }
        
        $results = array(
            "draw" => intval($_POST['draw'] ?? 1),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
    
    case "guardaryeditar":
        // Validar si es INSERT o UPDATE
        $id_entidad = $_POST["id_entidad"] ?? null;
        
        // Sanitizar datos
        $nombre = htmlspecialchars(trim($_POST["nombre_entidad"]), ENT_QUOTES, 'UTF-8');
        $descripcion = htmlspecialchars(trim($_POST["descripcion_entidad"]), ENT_QUOTES, 'UTF-8');
        
        // Campos opcionales: convertir vacío a null
        $campo_opcional = !empty($_POST["campo_opcional"]) ? $_POST["campo_opcional"] : null;
        
        try {
            if (empty($id_entidad)) {
                // INSERT
                $resultado = $entidad->insert_entidad($nombre, $descripcion, $campo_opcional);
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro creado correctamente',
                        'id_entidad' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al crear el registro'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // UPDATE
                $resultado = $entidad->update_entidad($id_entidad, $nombre, $descripcion, $campo_opcional);
                
                if ($resultado !== false) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro actualizado correctamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar el registro'
                    ], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin',
                'entidad.php',
                'guardaryeditar',
                "Error: " . $e->getMessage(),
                'error'
            );
            
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "mostrar":
        // Obtener registro por ID para edición
        $id_entidad = $_POST["id_entidad"];
        $datos = $entidad->get_entidadxid($id_entidad);
        
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "desactivar":
        // Soft delete
        $id_entidad = $_POST["id_entidad"];
        $resultado = $entidad->delete_entidadxid($id_entidad);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Registro desactivado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar el registro'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "activar":
        // Reactivar
        $id_entidad = $_POST["id_entidad"];
        $resultado = $entidad->activar_entidadxid($id_entidad);
        
        if ($resultado) {
            echo json_encode([
                'success' => true,
                'message' => 'Registro activado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar el registro'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
    
    case "verificar":
        // Validar unicidad de campo
        $campo_unico = $_POST["campo_unico"];
        $id_entidad = $_POST["id_entidad"] ?? null;
        
        $resultado = $entidad->verificarEntidad($campo_unico, $id_entidad);
        
        if (!isset($resultado['success'])) {
            $resultado['success'] = !isset($resultado['error']);
        }
        
        header('Content-Type: application/json');
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
    
    case "listar_disponibles":
        // Solo registros activos
        $datos = $entidad->get_entidades_disponibles();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_entidad" => $row["id_entidad"],
                "nombre_entidad" => $row["nombre_entidad"]
            );
        }
        
        $results = array(
            "draw" => 1,
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

### Operaciones Estándar (switch cases)

1. ✅ `listar` - Listado completo para DataTables
2. ✅ `guardaryeditar` - INSERT o UPDATE según id
3. ✅ `mostrar` - Obtener registro para edición
4. ✅ `desactivar` - Soft delete
5. ✅ `activar` - Reactivar registro
6. ✅ `verificar` - Validar unicidad
7. ✅ `listar_disponibles` - Solo activos

---

## ��� SEGURIDAD

### Prepared Statements (SIEMPRE)

```php
// ✅ CORRECTO: Prepared statement con bindValue
$sql = "SELECT * FROM cliente WHERE email_cliente = ?";
$stmt = $this->conexion->prepare($sql);
$stmt->bindValue(1, $email, PDO::PARAM_STR);
$stmt->execute();

// ✅ CORRECTO: Prepared statement con array de parámetros
$sql = "INSERT INTO cliente (nombre_cliente, email_cliente) VALUES (?, ?)";
$stmt = $this->conexion->prepare($sql);
$stmt->execute([$nombre, $email]);

// ❌ PROHIBIDO: Concatenación directa (SQL Injection)
$sql = "SELECT * FROM cliente WHERE email_cliente = '$email'";
$resultado = $this->conexion->query($sql);
```

### Sanitización de Inputs

```php
// ✅ Sanitizar SIEMPRE en controllers
$nombre = htmlspecialchars(trim($_POST["nombre"]), ENT_QUOTES, 'UTF-8');
$email = filter_var($_POST["email"], FILTER_SANITIZE_EMAIL);
$telefono = preg_replace('/[^0-9+]/', '', $_POST["telefono"]);

// Validar formato
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['error' => 'Email inválido']);
    exit;
}
```

### Manejo de Campos Opcionales

```php
// ✅ CORRECTO: Convertir vacío a NULL
$id_contacto = !empty($_POST["id_contacto"]) ? $_POST["id_contacto"] : null;

if (!empty($id_contacto)) {
    $stmt->bindValue(3, $id_contacto, PDO::PARAM_INT);
} else {
    $stmt->bindValue(3, null, PDO::PARAM_NULL);
}

// ❌ INCORRECTO: No validar antes de insertar
$stmt->bindValue(3, $_POST["id_contacto"], PDO::PARAM_INT);
```

### Tipos de Datos en bindValue()

```php
// Tipos PDO estándar
PDO::PARAM_INT    // Enteros: IDs, cantidades
PDO::PARAM_STR    // Cadenas: nombres, descripciones
PDO::PARAM_BOOL   // Booleanos: activo, visible
PDO::PARAM_NULL   // NULL explícito
```

### Manejo de Errores

```php
// ✅ CORRECTO: Try-catch con logging
try {
    // Operación de BD
    $resultado = $modelo->insert_entidad(...);
    
} catch (PDOException $e) {
    $this->registro->registrarActividad(
        'admin',
        'Controller',
        'operacion',
        "Error: " . $e->getMessage(),
        'error'
    );
    
    // Mensaje genérico al usuario (NO exponer detalles del error)
    echo json_encode([
        'success' => false,
        'message' => 'Error al procesar la solicitud'
    ]);
}
```

---

## ��� CONEXIÓN A BASE DE DATOS

### Clase Conexion.php

```php
<?php

class Conexion
{
    private $pdo;

    public function __construct()
    {
        // Leer credenciales desde JSON externo
        $config_file = __DIR__ . '/conexion.json';
        
        if (!file_exists($config_file)) {
            throw new Exception("Error: El archivo de configuración no existe");
        }

        $config_json = file_get_contents($config_file);
        $config = json_decode($config_json, true);

        if ($config === null) {
            throw new Exception("Error: No se pudo parsear el archivo de configuración");
        }

        try {
            $dsn = "mysql:host={$config['host']};port={$config['port']};dbname={$config['database']};charset={$config['charset']}";
            
            $this->pdo = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
            
        } catch (PDOException $e) {
            throw new Exception("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion()
    {
        return $this->pdo;
    }
}
?>
```

### Archivo conexion.json (NO versionar en Git)

```json
{
    "host": "217.154.117.83",
    "port": "3308",
    "user": "administrator",
    "password": "27979699",
    "database": "toldos_db",
    "charset": "utf8mb4"
}
```

### Uso en Modelos

```php
// En constructor del modelo
$this->conexion = (new Conexion())->getConexion();

// Usar PDO normalmente
$stmt = $this->conexion->prepare("SELECT ...");
```

---

## ��� SISTEMA DE LOGGING

### Clase RegistroActividad

```php
// Ubicación: config/funciones.php

class RegistroActividad
{
    private $directorio = '../public/logs/';

    public function registrarActividad($usuario, $pantalla, $actividad, $mensaje, $tipo = 'info')
    {
        // Crear archivo JSON diario: YYYY-MM-DD.json
        $fecha = date('Y-m-d');
        $archivo = $this->directorio . $fecha . '.json';
        
        // Crear entrada de log
        $registro = [
            'usuario' => $usuario,
            'pantalla' => $pantalla,
            'actividad' => $actividad,
            'mensaje' => $mensaje,
            'tipo' => $tipo,
            'fecha_hora' => date('Y-m-d H:i:s')
        ];
        
        // Leer logs existentes
        if (file_exists($archivo)) {
            $logs = json_decode(file_get_contents($archivo), true);
        } else {
            $logs = [];
        }
        
        // Añadir nuevo registro
        $logs[] = $registro;
        
        // Guardar
        file_put_contents($archivo, json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    }
}
```

### Uso en Modelos y Controllers

```php
// Éxito
$this->registro->registrarActividad(
    'admin',
    'Presupuesto',
    'insert_presupuesto',
    "Presupuesto creado con ID: $id",
    'info'
);

// Error
$this->registro->registrarActividad(
    'admin',
    'Presupuesto',
    'insert_presupuesto',
    "Error: " . $e->getMessage(),
    'error'
);

// Advertencia
$this->registro->registrarActividad(
    'system',
    'Conexion',
    '__construct',
    "Zona horaria no configurada",
    'warning'
);
```

---

## ��� VISTAS (Views)

### Estructura HTML con Bootstrap 5

```html
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Entidades - MDR</title>
    
    <!-- Bootstrap 5 -->
    <link href="../../public/lib/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- DataTables -->
    <link href="../../public/lib/DataTables/datatables.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="../../public/lib/fontawesome-6.4.2/css/all.min.css" rel="stylesheet">
    
    <!-- SweetAlert2 -->
    <link href="../../public/lib/sweetalert2-11.7.32/sweetalert2.min.css" rel="stylesheet">
    
    <!-- CSS personalizado -->
    <link href="../../public/css/custom.css" rel="stylesheet">
</head>
<body>
    <!-- Header y navegación -->
    <?php include '../template/header.php'; ?>
    <?php include '../template/sidebar.php'; ?>
    
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <h1>Gestión de Entidades</h1>
                
                <!-- Botón crear -->
                <button class="btn btn-primary" onclick="mostrarFormulario()">
                    <i class="fa fa-plus"></i> Nueva Entidad
                </button>
                
                <!-- Tabla DataTables -->
                <table id="tblEntidades" class="table table-striped table-bordered dt-responsive nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Opciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Se llena vía AJAX -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- Modal formulario -->
    <div class="modal fade" id="modalFormulario" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="frmEntidad">
                    <div class="modal-header">
                        <h5 class="modal-title">Formulario de Entidad</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" name="id_entidad" id="id_entidad">
                        
                        <div class="mb-3">
                            <label for="nombre_entidad" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre_entidad" name="nombre_entidad" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="descripcion_entidad" class="form-label">Descripción</label>
                            <textarea class="form-control" id="descripcion_entidad" name="descripcion_entidad" rows="3"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="../../public/lib/jquery-3.7.1/jquery.min.js"></script>
    <script src="../../public/lib/bootstrap-5.0.2/js/bootstrap.bundle.min.js"></script>
    <script src="../../public/lib/DataTables/datatables.min.js"></script>
    <script src="../../public/lib/sweetalert2-11.7.32/sweetalert2.all.min.js"></script>
    
    <!-- Script específico de la vista -->
    <script src="entidad.js"></script>
</body>
</html>
```

### JavaScript (entidad.js)

```javascript
let tabla;

$(document).ready(function() {
    // Inicializar DataTable
    tabla = $('#tblEntidades').DataTable({
        ajax: {
            url: '../../controller/entidad.php?op=listar',
            type: 'POST',
            dataSrc: 'data'
        },
        columns: [
            { data: 'id_entidad' },
            { data: 'nombre_entidad' },
            { data: 'descripcion_entidad' },
            { 
                data: 'activo_entidad',
                render: function(data) {
                    return data == 1 
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Inactivo</span>';
                }
            },
            { data: 'opciones', orderable: false }
        ],
        language: {
            url: '../../public/lib/DataTables/es-ES.json'
        },
        responsive: true,
        order: [[0, 'desc']]
    });
    
    // Submit formulario
    $('#frmEntidad').on('submit', function(e) {
        e.preventDefault();
        guardaryeditar();
    });
});

function mostrarFormulario() {
    $('#frmEntidad')[0].reset();
    $('#id_entidad').val('');
    $('#modalFormulario').modal('show');
}

function mostrar(id) {
    $.post('../../controller/entidad.php?op=mostrar', { id_entidad: id })
        .done(function(data) {
            $('#id_entidad').val(data.id_entidad);
            $('#nombre_entidad').val(data.nombre_entidad);
            $('#descripcion_entidad').val(data.descripcion_entidad);
            $('#modalFormulario').modal('show');
        })
        .fail(function() {
            Swal.fire('Error', 'No se pudo cargar el registro', 'error');
        });
}

function guardaryeditar() {
    let formData = $('#frmEntidad').serialize();
    
    $.post('../../controller/entidad.php?op=guardaryeditar', formData)
        .done(function(response) {
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success');
                $('#modalFormulario').modal('hide');
                tabla.ajax.reload();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        })
        .fail(function() {
            Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
        });
}

function desactivar(id) {
    Swal.fire({
        title: '¿Estás seguro?',
        text: "Esta acción desactivará el registro",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../../controller/entidad.php?op=desactivar', { id_entidad: id })
                .done(function(response) {
                    if (response.success) {
                        Swal.fire('Desactivado', response.message, 'success');
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                });
        }
    });
}

function activar(id) {
    $.post('../../controller/entidad.php?op=activar', { id_entidad: id })
        .done(function(response) {
            if (response.success) {
                Swal.fire('Activado', response.message, 'success');
                tabla.ajax.reload();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        });
}
```

---

## ��� PATRONES DE RESPUESTA JSON

### Respuestas de Controllers

```javascript
// Éxito en operación
{
    "success": true,
    "message": "Operación realizada correctamente",
    "id_entidad": 42  // Opcional: ID del registro creado
}

// Error en operación
{
    "success": false,
    "message": "Descripción del error"
}

// Listado para DataTables
{
    "draw": 1,
    "recordsTotal": 100,
    "recordsFiltered": 100,
    "data": [
        { "id_entidad": 1, "nombre_entidad": "...", ... },
        { "id_entidad": 2, "nombre_entidad": "...", ... }
    ]
}

// Verificación de existencia
{
    "existe": true  // o false
}

// Obtener registro por ID
{
    "id_entidad": 1,
    "nombre_entidad": "...",
    "descripcion_entidad": "...",
    "activo_entidad": 1
}
```

---

## ��� TRIGGERS (Disparadores)

### Patrón de Generación de Códigos Correlativos

```sql
-- Trigger para generar código automático
DELIMITER $$

CREATE TRIGGER trg_elemento_before_insert
BEFORE INSERT ON elemento
FOR EACH ROW
BEGIN
    DECLARE siguiente_numero INT;
    
    -- Obtener el siguiente número para este artículo
    SELECT COALESCE(MAX(CAST(SUBSTRING_INDEX(codigo_elemento, '-', -1) AS UNSIGNED)), 0) + 1
    INTO siguiente_numero
    FROM elemento
    WHERE id_articulo = NEW.id_articulo;
    
    -- Asignar el código formato: ARTICULO-001
    SET NEW.codigo_elemento = CONCAT(
        (SELECT UPPER(nombre_articulo) FROM articulo WHERE id_articulo = NEW.id_articulo),
        '-',
        LPAD(siguiente_numero, 3, '0')
    );
END$$

DELIMITER ;
```

### Patrón de Sincronización de Estados

```sql
-- Sincronizar campo activo con estado
DELIMITER $$

CREATE TRIGGER trg_presupuesto_before_desactivar
BEFORE UPDATE ON presupuesto
FOR EACH ROW
BEGIN
    -- Si se desactiva, marcar como CANCELADO
    IF NEW.activo_presupuesto = 0 AND OLD.activo_presupuesto = 1 THEN
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'CANCELADO'
        );
    END IF;
END$$

DELIMITER ;
```

### Patrón de Validación con Error

```sql
-- Validar regla de negocio
DELIMITER $$

CREATE TRIGGER trg_empresa_validar_ficticia_principal
BEFORE INSERT ON empresa
FOR EACH ROW
BEGIN
    DECLARE existe_principal INT;
    
    -- Si intenta crear empresa ficticia principal
    IF NEW.ficticia_empresa = 1 AND NEW.empresa_ficticia_principal = 1 THEN
        
        -- Verificar si ya existe una
        SELECT COUNT(*) INTO existe_principal
        FROM empresa
        WHERE ficticia_empresa = 1 
        AND empresa_ficticia_principal = 1
        AND activo_empresa = 1;
        
        -- Si existe, lanzar error
        IF existe_principal > 0 THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'Ya existe una empresa ficticia principal activa';
        END IF;
    END IF;
END$$

DELIMITER ;
```

---

## ��� VISTAS SQL

### Patrón de Vista Completa con JOINs

```sql
-- Vista que consolida información de múltiples tablas
CREATE OR REPLACE VIEW vista_presupuesto_completa AS
SELECT 
    -- Campos de presupuesto
    p.id_presupuesto,
    p.numero_presupuesto,
    p.fecha_presupuesto,
    p.fecha_validez_presupuesto,
    p.nombre_evento_presupuesto,
    p.activo_presupuesto,
    p.created_at_presupuesto,
    p.updated_at_presupuesto,
    
    -- Datos del cliente
    c.id_cliente,
    c.nombre_cliente,
    c.apellido_cliente,
    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS nombre_completo_cliente,
    c.email_cliente,
    c.telefono_cliente,
    
    -- Datos del contacto (puede ser NULL)
    cc.id_contacto_cliente,
    cc.nombre_contacto_cliente,
    cc.telefono_contacto_cliente,
    
    -- Estado del presupuesto
    ep.id_estado_ppto,
    ep.nombre_estado_ppto,
    ep.codigo_estado_ppto,
    ep.color_estado_ppto,
    
    -- Forma de pago
    fp.id_forma_pago,
    fp.nombre_forma_pago,
    
    -- Método de pago
    m.id_metodo,
    m.nombre_metodo,
    
    -- Totales calculados (si los tienes en la tabla)
    p.subtotal_presupuesto,
    p.total_iva_presupuesto,
    p.total_presupuesto

FROM presupuesto p

INNER JOIN cliente c 
    ON p.id_cliente = c.id_cliente

LEFT JOIN contacto_cliente cc 
    ON p.id_contacto_cliente = cc.id_contacto_cliente

INNER JOIN estado_presupuesto ep 
    ON p.id_estado_ppto = ep.id_estado_ppto

LEFT JOIN forma_pago fp 
    ON p.id_forma_pago = fp.id_forma_pago

LEFT JOIN metodo m 
    ON p.id_metodo = m.id_metodo

WHERE p.activo_presupuesto = 1
ORDER BY p.fecha_presupuesto DESC;
```

### Uso en Modelos

```php
// Preferir vistas para consultas complejas
public function get_presupuestos()
{
    $sql = "SELECT * FROM vista_presupuesto_completa 
            ORDER BY fecha_presupuesto DESC";
    
    $stmt = $this->conexion->prepare($sql);
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

// Usar tablas directamente solo para INSERT/UPDATE/DELETE
public function insert_presupuesto(...)
{
    $sql = "INSERT INTO presupuesto (...) VALUES (...)";
    // ...
}
```

---

## ✅ CHECKLIST DE BUENAS PRÁCTICAS

### Base de Datos
- [ ] Tablas en SINGULAR
- [ ] Todos los campos con sufijo _<<tabla>>
- [ ] Campos obligatorios: id, activo, created_at, updated_at
- [ ] Soft delete (activo=0) en lugar de DELETE físico
- [ ] Foreign keys con ON DELETE/UPDATE definidos
- [ ] Índices en campos de búsqueda frecuente
- [ ] Charset utf8mb4_spanish_ci

### Modelos
- [ ] Constructor con PDO y RegistroActividad
- [ ] Zona horaria configurada a Europe/Madrid
- [ ] Prepared statements en TODAS las consultas
- [ ] Try-catch en todos los métodos
- [ ] Logging de errores y acciones importantes
- [ ] Validación de campos opcionales (null)
- [ ] Retornos consistentes (ID, rowCount, boolean, array)
- [ ] Métodos estándar implementados

### Controllers
- [ ] Switch por operación ($_GET["op"])
- [ ] Sanitización de inputs
- [ ] Conversión de vacíos a null
- [ ] Respuestas JSON con JSON_UNESCAPED_UNICODE
- [ ] Headers Content-Type correctos
- [ ] Try-catch en operaciones críticas
- [ ] Logging con RegistroActividad

### Vistas
- [ ] HTML5 semántico
- [ ] Bootstrap 5 para diseño
- [ ] DataTables para listados
- [ ] SweetAlert2 para confirmaciones
- [ ] Sin lógica de negocio
- [ ] Validación client-side (complementaria)
- [ ] AJAX para comunicación con controllers

### Seguridad
- [ ] Prepared statements siempre
- [ ] Sanitización de inputs
- [ ] Validación de tipos de datos
- [ ] No exponer detalles de errores SQL
- [ ] credenciales en JSON externo
- [ ] Logging de errores
- [ ] CSRF tokens (si aplica)

---

## ��� EJEMPLOS COMPLETOS

### Ejemplo 1: Tabla Cliente

```sql
CREATE TABLE cliente (
    -- Identificación
    id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_cliente VARCHAR(20) NOT NULL UNIQUE 
        COMMENT 'Código único del cliente',
    
    -- Datos personales
    nombre_cliente VARCHAR(100) NOT NULL,
    apellido_cliente VARCHAR(100) NOT NULL,
    email_cliente VARCHAR(100),
    telefono_cliente VARCHAR(20),
    movil_cliente VARCHAR(20),
    
    -- Datos fiscales
    nif_cliente VARCHAR(20),
    tipo_cliente ENUM('particular', 'empresa') DEFAULT 'particular',
    
    -- Dirección
    direccion_cliente VARCHAR(255),
    poblacion_cliente VARCHAR(100),
    provincia_cliente VARCHAR(100),
    cp_cliente VARCHAR(10),
    pais_cliente VARCHAR(100) DEFAULT 'España',
    
    -- Campos obligatorios
    activo_cliente BOOLEAN DEFAULT TRUE,
    created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_codigo_cliente (codigo_cliente),
    INDEX idx_nombre_cliente (nombre_cliente),
    INDEX idx_email_cliente (email_cliente),
    INDEX idx_activo_cliente (activo_cliente)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Gestión de clientes del sistema';
```

### Ejemplo 2: Modelo Completo Clientes.php

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Clientes
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'Clientes',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_clientes()
    {
        try {
            $sql = "SELECT * FROM cliente 
                    WHERE activo_cliente = 1 
                    ORDER BY nombre_cliente ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'get_clientes',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    public function insert_cliente(
        $codigo, $nombre, $apellido, $email = null, 
        $telefono = null, $nif = null, $tipo = 'particular',
        $direccion = null, $poblacion = null, $provincia = null, $cp = null
    ) {
        try {
            $sql = "INSERT INTO cliente (
                        codigo_cliente, nombre_cliente, apellido_cliente,
                        email_cliente, telefono_cliente, nif_cliente, tipo_cliente,
                        direccion_cliente, poblacion_cliente, provincia_cliente, cp_cliente,
                        created_at_cliente
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $codigo, PDO::PARAM_STR);
            $stmt->bindValue(2, $nombre, PDO::PARAM_STR);
            $stmt->bindValue(3, $apellido, PDO::PARAM_STR);
            
            // Campos opcionales
            $stmt->bindValue(4, $email, !empty($email) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(5, $telefono, !empty($telefono) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(6, $nif, !empty($nif) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(7, $tipo, PDO::PARAM_STR);
            $stmt->bindValue(8, $direccion, !empty($direccion) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(9, $poblacion, !empty($poblacion) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(10, $provincia, !empty($provincia) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            $stmt->bindValue(11, $cp, !empty($cp) ? PDO::PARAM_STR : PDO::PARAM_NULL);
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'insert_cliente',
                "Cliente creado con ID: $id - $nombre $apellido",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Clientes',
                'insert_cliente',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    public function verificarCliente($codigo, $id_cliente = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM cliente 
                    WHERE LOWER(codigo_cliente) = LOWER(?)";
            $params = [trim($codigo)];
            
            if (!empty($id_cliente)) {
                $sql .= " AND id_cliente != ?";
                $params[] = $id_cliente;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ['existe' => ($resultado['total'] > 0)];
            
        } catch (PDOException $e) {
            return ['existe' => false, 'error' => $e->getMessage()];
        }
    }
}
?>
```

---

## ��� COMANDOS GIT

```bash
# Clonar repositorio
git clone https://github.com/luisInnovabyte/MDR.git

# Actualizar desde remoto
git pull origin main

# Ver estado
git status

# Añadir cambios
git add .

# Commit
git commit -m "feat: descripción del cambio"

# Push
git push origin main

# Crear rama
git checkout -b feature/nueva-funcionalidad

# IMPORTANTE: Nunca versionar
# - config/conexion.json
# - public/logs/
# - public/documentos/
```

---

## ��� CONVENCIONES DE COMMITS

```bash
feat: Nueva funcionalidad
fix: Corrección de bug
docs: Documentación
style: Formato, punto y coma, etc.
refactor: Refactorización de código
test: Añadir tests
chore: Actualizar dependencias
```

---

## ��� RECURSOS ADICIONALES

- **Documentación PHP PDO**: https://www.php.net/manual/es/book.pdo.php
- **Bootstrap 5**: https://getbootstrap.com/docs/5.0/
- **DataTables**: https://datatables.net/
- **SweetAlert2**: https://sweetalert2.github.io/
- **jQuery**: https://api.jquery.com/

---

## ��� NOTAS FINALES

- **NO usar frameworks PHP**: El proyecto usa PHP puro con MVC
- **NO usar ORMs**: Todas las consultas son SQL directo con PDO
- **Preferir vistas SQL** para consultas complejas con múltiples JOINs
- **SIEMPRE** usar prepared statements
- **NUNCA** hacer DELETE físico, usar soft delete (activo=0)
- **Logging obligatorio** en operaciones críticas
- **Zona horaria Europe/Madrid** configurada en todos los modelos

---

**Última actualización**: 18 de diciembre de 2024  
**Versión**: 1.0  
**Proyecto**: MDR ERP Manager  
**Autor**: Luis - Innovabyte
