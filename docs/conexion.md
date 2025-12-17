# 🔌 Documentación de Conexión a Base de Datos

## 🎯 Introducción

Los proyectos utilizan un sistema de conexión a base de datos basado en **PDO (PHP Data Objects)** con configuración externa mediante archivo JSON. Esta arquitectura proporciona seguridad, flexibilidad y facilidad de mantenimiento.


## 📁 Archivos del Sistema de Conexión

### 1. `config/conexion.json` - Archivo de Configuración
### 2. `config/conexion.php` - Clase de Conexión PDO


## 🗂️ Archivo: `conexion.json`

### Ubicación
```
/config/conexion.json
```

### Propósito
Almacenar las **credenciales de conexión** a la base de datos de forma externa y segura, separadas del código PHP.

### Contenido Completo

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

### Descripción de Campos

| Campo | Tipo | Descripción | Ejemplo |
|-------|------|-------------|---------|
| `host` | string | Dirección IP o dominio del servidor MySQL/MariaDB | `217.154.117.83` o `localhost` |
| `port` | string | Puerto de conexión (por defecto MySQL usa 3306) | `3308` |
| `user` | string | Nombre de usuario de la base de datos | `administrator` |
| `password` | string | Contraseña del usuario | `27979699` |
| `database` | string | Nombre de la base de datos | `toldos_db` |
| `charset` | string | Codificación de caracteres (recomendado: utf8mb4) | `utf8mb4` |

### Ventajas de usar JSON

✅ **Separación de código y configuración**: Las credenciales no están en el código PHP  
✅ **Fácil modificación**: Cambiar credenciales sin tocar código  
✅ **Portabilidad**: Diferentes entornos (desarrollo, producción) con distintos JSON  
✅ **Seguridad**: El archivo puede estar fuera del DocumentRoot  
✅ **Versionado**: `.gitignore` puede excluir este archivo de Git  

### ⚠️ Seguridad Importante

**Este archivo NUNCA debe estar en repositorios públicos.** 

Añadir en `.gitignore`:
```
config/conexion.json
```

Para compartir la estructura sin exponer credenciales, crear `conexion.json.example`:
```json
{
    "host": "localhost",
    "port": "3306",
    "user": "tu_usuario",
    "password": "tu_password",
    "database": "nombre_base_datos",
    "charset": "utf8mb4"
}
```

---

## 🔧 Archivo: `conexion.php`

### Ubicación
```
/config/conexion.php
```

### Propósito
Clase PHP que gestiona la conexión a MySQL/MariaDB mediante **PDO**, leyendo la configuración desde `conexion.json`.

### Contenido Completo

```php
<?php
class Conexion
{
    protected $conect;

    public function __construct()
    {
        $Json_conf = __DIR__ . '/conexion.json';

        if (!file_exists($Json_conf)) {
            throw new Exception("Error: El archivo de configuración no existe");
        }

        $json = file_get_contents($Json_conf);
        $config = json_decode($json, true);

        if ($config === null) {
            throw new Exception("Error: No se pudo parsear el archivo de configuración");
        }

        try {
            $port = isset($config['port']) ? $config['port'] : '3306';
            $dsn = "mysql:host={$config['host']};port=$port;dbname={$config['database']};charset={$config['charset']}";
            $this->conect = new PDO($dsn, $config['user'], $config['password'], [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]);
        } catch (PDOException $e) {
            die("Error de conexión: " . $e->getMessage());
        }
    }

    public function getConexion()
    {
        return $this->conect; // ✅ Método para obtener la conexión
    }


    // Método para cerrar la conexión (opcional en PDO)
    public function cerrar()
    {
        $this->conect = null; // Cerrar la conexión asignando null
    }
}
```

---

## 📚 Explicación Detallada del Código

### 1️⃣ Propiedad de la Clase

```php
protected $conect;
```

**Propósito**: Almacenar el objeto PDO de conexión a la base de datos.  
**Visibilidad**: `protected` permite acceso desde la clase y sus herederas.

---

### 2️⃣ Constructor `__construct()`

#### **Paso 1: Localizar el archivo JSON**

```php
$Json_conf = __DIR__ . '/conexion.json';
```

- `__DIR__`: Constante mágica de PHP que devuelve el directorio del archivo actual
- Busca `conexion.json` en el mismo directorio que `conexion.php` (`/config/`)

#### **Paso 2: Validar existencia del archivo**

```php
if (!file_exists($Json_conf)) {
    throw new Exception("Error: El archivo de configuración no existe");
}
```

**Prevención**: Evita errores si el archivo no existe.

#### **Paso 3: Leer y parsear el JSON**

```php
$json = file_get_contents($Json_conf);
$config = json_decode($json, true);

if ($config === null) {
    throw new Exception("Error: No se pudo parsear el archivo de configuración");
}
```

- `file_get_contents()`: Lee el contenido del archivo como string
- `json_decode($json, true)`: Convierte JSON a array asociativo PHP
- **Validación**: Si el JSON es inválido, `json_decode()` retorna `null`

#### **Paso 4: Construir el DSN (Data Source Name)**

```php
$port = isset($config['port']) ? $config['port'] : '3306';
$dsn = "mysql:host={$config['host']};port=$port;dbname={$config['database']};charset={$config['charset']}";
```

**DSN**: String de conexión con formato específico de MySQL/MariaDB.

**Ejemplo generado**:
```
mysql:host=217.154.117.83;port=3308;dbname=toldos_db;charset=utf8mb4
```

**Puerto por defecto**: Si no se especifica `port` en el JSON, usa `3306` (puerto estándar MySQL).

#### **Paso 5: Crear la conexión PDO**

```php
try {
    $this->conect = new PDO($dsn, $config['user'], $config['password'], [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
```

##### **Parámetros del Constructor PDO**:

1. **DSN**: String de conexión
2. **Usuario**: `$config['user']`
3. **Contraseña**: `$config['password']`
4. **Opciones** (array):

| Opción | Valor | Descripción |
|--------|-------|-------------|
| `PDO::ATTR_ERRMODE` | `PDO::ERRMODE_EXCEPTION` | Lanzar excepciones en errores SQL (en lugar de warnings) |
| `PDO::ATTR_DEFAULT_FETCH_MODE` | `PDO::FETCH_ASSOC` | Retornar resultados como arrays asociativos por defecto |
| `PDO::ATTR_EMULATE_PREPARES` | `false` | Usar prepared statements reales (más seguro contra SQL Injection) |

##### **Manejo de Errores**:

```php
catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}
```

- Si falla la conexión, detiene la ejecución y muestra el mensaje de error
- En producción, esto debería registrarse en logs en lugar de mostrarse al usuario

---

### 3️⃣ Método `getConexion()`

```php
public function getConexion()
{
    return $this->conect;
}
```

**Propósito**: Devolver el objeto PDO para ser usado en modelos y controladores.

**Uso típico**:
```php
$conexion = (new Conexion())->getConexion();
$stmt = $conexion->prepare("SELECT * FROM presupuesto");
```

---

### 4️⃣ Método `cerrar()`

```php
public function cerrar()
{
    $this->conect = null;
}
```

**Propósito**: Cerrar explícitamente la conexión a la base de datos.

**Nota**: En PDO, las conexiones se cierran automáticamente al finalizar el script, por lo que este método es **opcional** y rara vez necesario.

---

## 🔄 Flujo de Conexión Completo

```
┌─────────────────────────────────────────────────────────────┐
│  1. Crear instancia: $conn = new Conexion();               │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  2. Constructor __construct() se ejecuta automáticamente    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  3. Buscar archivo: __DIR__ . '/conexion.json'             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  4. ¿Existe el archivo?                                     │
│     └─ NO → throw Exception("archivo no existe")           │
│     └─ SÍ → Continuar                                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  5. Leer JSON: file_get_contents()                         │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  6. Parsear JSON: json_decode($json, true)                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  7. ¿JSON válido?                                           │
│     └─ NO → throw Exception("no se pudo parsear")          │
│     └─ SÍ → Continuar                                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  8. Construir DSN con credenciales del JSON                 │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  9. Crear objeto PDO con opciones de seguridad              │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  10. ¿Conexión exitosa?                                     │
│      └─ NO → die("Error de conexión: ...")                 │
│      └─ SÍ → $this->conect = PDO object                    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  11. Obtener conexión: $pdo = $conn->getConexion();        │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│  12. Usar PDO en modelos para ejecutar consultas           │
└─────────────────────────────────────────────────────────────┘
```

---

## 💻 Ejemplo de Uso en un Modelo

### Ejemplo completo en `models/Presupuesto.php`:

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Presupuesto
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        // 1. Crear instancia de Conexion
        $this->conexion = (new Conexion())->getConexion();
        
        // 2. Inicializar registro de actividad
        $this->registro = new RegistroActividad();
        
        // 3. Configurar zona horaria (opcional)
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'system',
                'Presupuesto',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    public function get_presupuestos()
    {
        try {
            // 4. Preparar consulta SQL
            $sql = "SELECT * FROM vista_presupuesto_completa 
                    ORDER BY fecha_presupuesto DESC";
            
            // 5. Preparar statement
            $stmt = $this->conexion->prepare($sql);
            
            // 6. Ejecutar consulta
            $stmt->execute();
            
            // 7. Retornar resultados
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Presupuesto',
                'get_presupuestos',
                "Error: " . $e->getMessage(),
                "error"
            );
        }
    }
}
```

---

## 🔐 Configuración de Seguridad PDO

### Opciones de Seguridad Implementadas

#### 1. `PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION`

**Propósito**: Lanzar excepciones en lugar de warnings silenciosos.

```php
// ❌ SIN ESTA OPCIÓN: Error silencioso
$stmt = $pdo->prepare("SELECT * FROM tabla_inexistente");
// Continúa ejecutando, difícil de debuggear

// ✅ CON ESTA OPCIÓN: Excepción clara
try {
    $stmt = $pdo->prepare("SELECT * FROM tabla_inexistente");
} catch (PDOException $e) {
    echo "Error SQL: " . $e->getMessage();
}
```

#### 2. `PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC`

**Propósito**: Retornar arrays asociativos por defecto.

```php
// ✅ CON FETCH_ASSOC
$resultado = $stmt->fetch();
// Array ( [id_presupuesto] => 1, [numero_presupuesto] => "2025-001" )

// ❌ SIN FETCH_ASSOC (retorna índices numéricos también)
// Array ( [0] => 1, [id_presupuesto] => 1, [1] => "2025-001", [numero_presupuesto] => "2025-001" )
```

#### 3. `PDO::ATTR_EMULATE_PREPARES => false`

**Propósito**: Usar prepared statements reales del servidor MySQL.

**Seguridad contra SQL Injection**:

```php
// ✅ SEGURO: Prepared statement real
$stmt = $pdo->prepare("SELECT * FROM presupuesto WHERE id_presupuesto = ?");
$stmt->bindValue(1, $id, PDO::PARAM_INT);
$stmt->execute();

// El servidor MySQL valida y escapa los parámetros
// Inmune a: $id = "1 OR 1=1; DROP TABLE presupuesto"
```

**Ventajas**:
- ✅ Mayor seguridad contra SQL Injection
- ✅ Validación de tipos por parte del servidor
- ✅ Mejor rendimiento en consultas repetidas

---

## 🌍 Configuración de Zona Horaria

En los modelos se configura la zona horaria de Madrid:

```php
$this->conexion->exec("SET time_zone = 'Europe/Madrid'");
```

### ¿Por qué es importante?

1. **Timestamps consistentes**: Todas las fechas se guardan en hora de Madrid
2. **Funciones de fecha**: `NOW()`, `CURDATE()`, `CURRENT_TIMESTAMP` usan Madrid
3. **Cálculos de diferencia**: Los cálculos de días/horas son precisos

### Ejemplo práctico:

```sql
-- Sin zona horaria configurada (usa UTC del servidor)
INSERT INTO presupuesto (..., created_at_presupuesto) VALUES (..., NOW());
-- Guarda: 2025-12-14 13:00:00 (puede estar en UTC)

-- Con zona horaria Madrid
SET time_zone = 'Europe/Madrid';
INSERT INTO presupuesto (..., created_at_presupuesto) VALUES (..., NOW());
-- Guarda: 2025-12-14 14:00:00 (hora de Madrid, UTC+1)
```

---

## 🛠️ Configuración de Diferentes Entornos

### Desarrollo Local

**`conexion.json` (desarrollo)**:
```json
{
    "host": "localhost",
    "port": "3306",
    "user": "root",
    "password": "",
    "database": "toldos_db_dev",
    "charset": "utf8mb4"
}
```

### Producción

**`conexion.json` (producción)**:
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

### Testing

**`conexion.json` (testing)**:
```json
{
    "host": "localhost",
    "port": "3306",
    "user": "test_user",
    "password": "test_pass",
    "database": "toldos_db_test",
    "charset": "utf8mb4"
}
```

---

## 🐛 Solución de Problemas Comunes

### Problema 1: "El archivo de configuración no existe"

**Error**:
```
Fatal error: Uncaught Exception: Error: El archivo de configuración no existe
```

**Solución**:
1. Verificar que `conexion.json` existe en `/config/`
2. Verificar permisos de lectura del archivo
3. Verificar la ruta con `__DIR__`

```bash
# Verificar existencia (Linux/Mac)
ls -la config/conexion.json

# Verificar existencia (Windows)
dir config\conexion.json
```

---

### Problema 2: "No se pudo parsear el archivo de configuración"

**Error**:
```
Fatal error: Uncaught Exception: Error: No se pudo parsear el archivo de configuración
```

**Causas**:
- JSON mal formado (falta coma, llave, comillas)
- Archivo corrupto
- Archivo vacío

**Solución**:
1. Validar JSON en [jsonlint.com](https://jsonlint.com)
2. Verificar comillas dobles (no simples)
3. Verificar comas entre elementos

**Ejemplo de JSON inválido**:
```json
{
    "host": "localhost"
    "port": "3306"  ← Falta coma
    'user': 'root'  ← Comillas simples no válidas
}
```

---

### Problema 3: "Error de conexión: Access denied"

**Error**:
```
Error de conexión: SQLSTATE[HY000] [1045] Access denied for user 'usuario'@'host'
```

**Causas**:
- Usuario o contraseña incorrectos
- Usuario no tiene permisos desde ese host
- Base de datos no existe

**Solución**:
1. Verificar credenciales en `conexion.json`
2. Verificar permisos del usuario en MySQL:

```sql
-- Verificar usuarios
SELECT user, host FROM mysql.user;

-- Dar permisos si es necesario
GRANT ALL PRIVILEGES ON toldos_db.* TO 'administrator'@'%' IDENTIFIED BY 'password';
FLUSH PRIVILEGES;
```

---

### Problema 4: "Error de conexión: Unknown database"

**Error**:
```
Error de conexión: SQLSTATE[HY000] [1049] Unknown database 'toldos_db'
```

**Solución**:
1. Verificar que la base de datos existe:

```sql
SHOW DATABASES;
```

2. Crear la base de datos si no existe:

```sql
CREATE DATABASE toldos_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

---

### Problema 5: "Can't connect to MySQL server on 'host'"

**Error**:
```
Error de conexión: SQLSTATE[HY000] [2002] Can't connect to MySQL server
```

**Causas**:
- Servidor MySQL no está ejecutándose
- Host o puerto incorrectos
- Firewall bloqueando conexión

**Solución**:
1. Verificar que MySQL está activo:

```bash
# Linux/Mac
sudo systemctl status mysql

# Windows
net start | find "MySQL"
```

2. Verificar puerto:

```bash
# Verificar puerto en uso
netstat -an | grep 3308
```

3. Probar conexión manual:

```bash
mysql -h 217.154.117.83 -P 3308 -u administrator -p
```

---

## 📊 Ventajas del Sistema de Conexión Actual

| Ventaja | Descripción |
|---------|-------------|
| ✅ **Seguridad** | Credenciales separadas del código |
| ✅ **PDO nativo** | Prepared statements reales, protección SQL Injection |
| ✅ **Manejo de errores** | Excepciones claras y capturables |
| ✅ **Portabilidad** | Fácil migración entre entornos |
| ✅ **Mantenibilidad** | Cambios de credenciales sin tocar código |
| ✅ **Configuración externa** | JSON fácil de editar |
| ✅ **Validaciones** | Verifica existencia y validez del JSON |
| ✅ **Charset UTF-8** | Soporte completo de caracteres especiales |
| ✅ **Zona horaria** | Timestamps consistentes en Madrid |
| ✅ **Reutilizable** | Se usa en todos los modelos del proyecto |

---

## 🔄 Comparación: Antes vs Después

### ❌ Conexión Antigua (mysqli sin configuración externa)

```php
// Credenciales hardcodeadas en el código
$host = "217.154.117.83";
$user = "administrator";
$pass = "27979699";
$db = "toldos_db";

$conexion = mysqli_connect($host, $user, $pass, $db);

if (!$conexion) {
    die("Error: " . mysqli_connect_error());
}

// Sin prepared statements, vulnerable a SQL Injection
$sql = "SELECT * FROM presupuesto WHERE id = " . $_GET['id'];
$resultado = mysqli_query($conexion, $sql);
```

**Problemas**:
- ❌ Credenciales en el código fuente
- ❌ Difícil de mantener
- ❌ Sin preparación de statements
- ❌ Vulnerable a SQL Injection
- ❌ Sin manejo de excepciones

### ✅ Conexión Actual (PDO con JSON)

```php
// Credenciales en archivo externo
$conexion = (new Conexion())->getConexion();

// Prepared statement seguro
$stmt = $conexion->prepare("SELECT * FROM presupuesto WHERE id_presupuesto = ?");
$stmt->bindValue(1, $_GET['id'], PDO::PARAM_INT);
$stmt->execute();
$resultado = $stmt->fetch(PDO::FETCH_ASSOC);
```

**Ventajas**:
- ✅ Credenciales externas y seguras
- ✅ Fácil de mantener y migrar
- ✅ Prepared statements automáticos
- ✅ Protección contra SQL Injection
- ✅ Manejo de excepciones

---

## 📝 Checklist de Configuración Inicial

### Para un nuevo entorno:

- [ ] 1. Crear archivo `conexion.json` en `/config/`
- [ ] 2. Configurar credenciales correctas en el JSON
- [ ] 3. Verificar que el servidor MySQL está activo
- [ ] 4. Verificar que la base de datos existe
- [ ] 5. Verificar permisos del usuario MySQL
- [ ] 6. Verificar puerto (3306 o 3308)
- [ ] 7. Probar conexión con script de prueba:

```php
<?php
require_once 'config/conexion.php';

try {
    $conn = new Conexion();
    $pdo = $conn->getConexion();
    echo "✅ Conexión exitosa a la base de datos";
    
    // Probar consulta simple
    $stmt = $pdo->query("SELECT DATABASE() as db");
    $result = $stmt->fetch();
    echo "\n📁 Base de datos actual: " . $result['db'];
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage();
}
```

- [ ] 8. Añadir `conexion.json` a `.gitignore`
- [ ] 9. Crear `conexion.json.example` para el equipo
- [ ] 10. Documentar en README del proyecto

---

## 🔗 Archivos Relacionados

### Estructura del proyecto:

```
MDR/
├── config/
│   ├── conexion.php          ← Clase de conexión PDO
│   ├── conexion.json         ← Credenciales (NO en Git)
│   ├── conexion.json.example ← Plantilla para el equipo
│   └── funciones.php         ← Funciones globales
│
├── models/
│   ├── Presupuesto.php       ← Usa conexion.php
│   ├── Clientes.php          ← Usa conexion.php
│   └── ...                   ← Todos los modelos
│
├── docs/
│   ├── conexion.md           ← Este documento
│   ├── models.md             ← Documentación de modelos
│   └── estructura_carpetas.md
│
└── .gitignore                ← Debe incluir conexion.json
```

---

## 📚 Referencias Adicionales

### Documentación Oficial:
- [PHP PDO Documentation](https://www.php.net/manual/es/book.pdo.php)
- [MySQL Charset UTF-8](https://dev.mysql.com/doc/refman/8.0/en/charset-unicode-utf8mb4.html)
- [Prepared Statements](https://www.php.net/manual/es/pdo.prepared-statements.php)

### Documentación del Proyecto:
- [`docs/models.md`](models.md) - Documentación de modelos
- [`docs/estructura_carpetas.md`](estructura_carpetas.md) - Estructura del proyecto

---

## 📌 Resumen Ejecutivo

### Sistema de Conexión MDR

**Componentes**:
1. `conexion.json` - Credenciales externas
2. `conexion.php` - Clase PDO con validaciones

**Características**:
- ✅ PDO con prepared statements
- ✅ Configuración externa JSON
- ✅ Validaciones de existencia y parseo
- ✅ Opciones de seguridad configuradas
- ✅ Zona horaria Madrid
- ✅ Manejo de excepciones

**Uso en Modelos**:
```php
$this->conexion = (new Conexion())->getConexion();
$stmt = $this->conexion->prepare("SELECT ...");
```

**Seguridad**:
- Prepared statements reales
- Credenciales fuera del código
- Protección SQL Injection
- Manejo de errores robusto

---

**Última actualización**: 14 de diciembre de 2025  
**Versión del documento**: 1.0  
**Autor**: Equipo Innovabyte
