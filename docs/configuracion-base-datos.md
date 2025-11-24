# Configuración de Conexión a Base de Datos

## Descripción General

Este documento describe la configuración y funcionamiento del sistema de conexión a la base de datos del proyecto TOLDOS. La conexión está diseñada con un patrón de configuración externa que separa las credenciales del código fuente.

## Arquitectura del Sistema

### Componentes Principales

1. **`config/conexion.json`** - Archivo de configuración con credenciales
2. **`config/conexion.php`** - Clase PHP para manejo de conexiones PDO

### Estructura de Archivos

```text
config/
├── conexion.json    # Configuración de credenciales (NO versionar)
└── conexion.php     # Clase de conexión PHP
```

## Archivo de Configuración: `conexion.json`

### Estructura del JSON

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

### Parámetros de Configuración

| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `host` | string | Dirección IP o hostname del servidor MySQL | `"217.154.117.83"` |
| `port` | string | Puerto de conexión MySQL | `"3308"` |
| `user` | string | Usuario de la base de datos | `"administrator"` |
| `password` | string | Contraseña del usuario | `"27979699"` |
| `database` | string | Nombre de la base de datos | `"toldos_db"` |
| `charset` | string | Codificación de caracteres | `"utf8mb4"` |

### Configuraciones Alternativas

#### Configuración Local de Desarrollo

```json
{
    "host": "localhost",
    "port": "3306",
    "user": "root",
    "password": "",
    "database": "toldos_local",
    "charset": "utf8mb4"
}
```

#### Configuración de Producción

```json
{
    "host": "servidor-produccion.com",
    "port": "3306",
    "user": "usuario_prod",
    "password": "contraseña_segura",
    "database": "toldos_production",
    "charset": "utf8mb4"
}
```

## Clase de Conexión: `conexion.php`

### Funcionalidades Principales

- **Singleton implícito**: Una conexión por instancia
- **Manejo de errores**: Excepciones detalladas
- **Configuración externa**: Lee desde archivo JSON
- **PDO nativo**: Utiliza PHP Data Objects
- **Configuración segura**: Atributos de seguridad predefinidos

### Métodos Disponibles

#### Constructor `__construct()`

```php
public function __construct()
```

**Funcionalidad:**

- Lee el archivo `conexion.json`
- Valida la existencia del archivo
- Parsea la configuración JSON
- Establece la conexión PDO
- Configura atributos de seguridad

**Excepciones:**

- `Exception`: Si el archivo de configuración no existe
- `Exception`: Si el JSON no es válido
- `PDOException`: Si la conexión falla

#### Método `getConexion()`

```php
public function getConexion(): PDO
```

**Funcionalidad:**

- Retorna la instancia PDO activa
- Permite acceso a todas las funciones de PDO

#### Método `cerrar()`

```php
public function cerrar(): void
```

**Funcionalidad:**

- Cierra la conexión asignando `null`
- Libera recursos de memoria

### Configuración PDO

La clase configura automáticamente los siguientes atributos PDO:

```php
[
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,      // Modo de error por excepción
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC, // Fetch asociativo por defecto
    PDO::ATTR_EMULATE_PREPARES => false               // Desactiva emulación de prepared statements
]
```

## Uso del Sistema

### Implementación Básica

```php
<?php
require_once 'config/conexion.php';

try {
    // Crear instancia de conexión
    $conexion = new Conexion();
    
    // Obtener PDO
    $pdo = $conexion->getConexion();
    
    // Realizar consultas
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([1]);
    $usuario = $stmt->fetch();
    
    // Cerrar conexión (opcional)
    $conexion->cerrar();
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
```

### Implementación en Modelos

```php
<?php
class UsuarioModel extends Conexion
{
    private $pdo;
    
    public function __construct()
    {
        parent::__construct();
        $this->pdo = $this->getConexion();
    }
    
    public function obtenerUsuarios()
    {
        $stmt = $this->pdo->query("SELECT * FROM usuarios");
        return $stmt->fetchAll();
    }
}
```

## Automatización de la Configuración

### Script de Generación Automática

```php
<?php
/**
 * Generador automático de configuración de base de datos
 */
function generarConfiguracion($entorno = 'desarrollo')
{
    $configuraciones = [
        'desarrollo' => [
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'root',
            'password' => '',
            'database' => 'toldos_dev',
            'charset' => 'utf8mb4'
        ],
        'testing' => [
            'host' => 'localhost',
            'port' => '3306',
            'user' => 'test_user',
            'password' => 'test_pass',
            'database' => 'toldos_test',
            'charset' => 'utf8mb4'
        ],
        'produccion' => [
            'host' => getenv('DB_HOST') ?: 'localhost',
            'port' => getenv('DB_PORT') ?: '3306',
            'user' => getenv('DB_USER'),
            'password' => getenv('DB_PASSWORD'),
            'database' => getenv('DB_DATABASE'),
            'charset' => 'utf8mb4'
        ]
    ];
    
    $config = $configuraciones[$entorno] ?? $configuraciones['desarrollo'];
    
    file_put_contents(
        'config/conexion.json',
        json_encode($config, JSON_PRETTY_PRINT)
    );
    
    echo "Configuración para '$entorno' generada exitosamente.\n";
}

// Uso: php generar_config.php desarrollo
$entorno = $argv[1] ?? 'desarrollo';
generarConfiguracion($entorno);
```

### Variables de Entorno

Para mayor seguridad en producción, considera usar variables de entorno:

```php
// En producción, leer desde variables de entorno
$config = [
    'host' => $_ENV['DB_HOST'] ?? 'localhost',
    'port' => $_ENV['DB_PORT'] ?? '3306',
    'user' => $_ENV['DB_USER'] ?? 'root',
    'password' => $_ENV['DB_PASSWORD'] ?? '',
    'database' => $_ENV['DB_DATABASE'] ?? 'toldos',
    'charset' => 'utf8mb4'
];
```

## Mejores Prácticas de Seguridad

### 1. Protección del Archivo JSON

```apache
# .htaccess para proteger config/
<Files "conexion.json">
    Order Deny,Allow
    Deny from all
</Files>
```

### 2. Validación de Configuración

```php
private function validarConfiguracion($config)
{
    $requeridos = ['host', 'user', 'password', 'database'];
    
    foreach ($requeridos as $campo) {
        if (empty($config[$campo])) {
            throw new Exception("Campo requerido '$campo' no encontrado en configuración");
        }
    }
    
    return true;
}
```

### 3. Logging de Conexiones

```php
private function logConexion($exitosa = true)
{
    $log = date('Y-m-d H:i:s') . " - ";
    $log .= $exitosa ? "Conexión exitosa" : "Error de conexión";
    $log .= " desde IP: " . $_SERVER['REMOTE_ADDR'] ?? 'CLI';
    $log .= "\n";
    
    file_put_contents('logs/conexiones.log', $log, FILE_APPEND);
}
```

## Troubleshooting

### Errores Comunes

1. **Archivo no encontrado**

   ```text
   Error: El archivo de configuración no existe
   ```

   **Solución**: Verificar que `config/conexion.json` existe

2. **JSON inválido**

   ```text
   Error: No se pudo parsear el archivo de configuración
   ```

   **Solución**: Validar sintaxis JSON

3. **Conexión rechazada**

   ```text
   Error de conexión: SQLSTATE[HY000] [2002]
   ```

   **Solución**: Verificar host, puerto y credenciales

### Herramientas de Diagnóstico

```php
<?php
// test_conexion.php
require_once 'config/conexion.php';

echo "=== DIAGNÓSTICO DE CONEXIÓN ===\n";

try {
    // Verificar archivo de configuración
    $configFile = 'config/conexion.json';
    if (!file_exists($configFile)) {
        throw new Exception("Archivo de configuración no encontrado");
    }
    echo "✓ Archivo de configuración encontrado\n";
    
    // Verificar contenido JSON
    $json = file_get_contents($configFile);
    $config = json_decode($json, true);
    if ($config === null) {
        throw new Exception("JSON inválido");
    }
    echo "✓ JSON válido\n";
    
    // Probar conexión
    $conexion = new Conexion();
    $pdo = $conexion->getConexion();
    echo "✓ Conexión exitosa\n";
    
    // Verificar base de datos
    $stmt = $pdo->query("SELECT DATABASE() as db");
    $result = $stmt->fetch();
    echo "✓ Base de datos activa: " . $result['db'] . "\n";
    
    echo "\n=== CONEXIÓN EXITOSA ===\n";
    
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "\n";
}
```

## Conclusión

Este sistema de configuración proporciona:

- **Flexibilidad**: Fácil cambio entre entornos
- **Seguridad**: Credenciales fuera del código
- **Mantenibilidad**: Configuración centralizada
- **Robustez**: Manejo completo de errores

La separación entre configuración y código permite la automatización del despliegue y facilita el mantenimiento del sistema en diferentes entornos.
