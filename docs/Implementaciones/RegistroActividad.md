# Documentación de RegistroActividad

## Introducción

La clase **RegistroActividad** es un sistema de logging y auditoría que registra todas las actividades realizadas por los usuarios en el sistema. Proporciona trazabilidad completa de las operaciones, facilitando la auditoría, debugging y seguimiento de acciones realizadas en la aplicación.

---

## Localización del Archivo

### 📂 Ubicación
```
w:\MDR\config\funciones.php
```

### 📍 Estructura del Proyecto
```
MDR/
├── config/
│   ├── conexion.php
│   ├── conexion.json
│   └── funciones.php ← AQUÍ SE ENCUENTRA RegistroActividad
├── controller/
├── models/
├── view/
└── public/
    └── logs/ ← AQUÍ SE GUARDAN LOS LOGS
        ├── 2025-12-01.json
        ├── 2025-12-02.json
        ├── 2025-12-14.json
        └── log_20251214.txt
```

### 🔗 Ruta Relativa desde Controllers
Los controllers incluyen el archivo así:
```php
require_once '../config/funciones.php';
```

---

## ¿Para Qué Sirve?

La clase **RegistroActividad** proporciona funcionalidades para:

### 1. 📝 **Registro de Actividades**
Guarda un log detallado de cada operación realizada en el sistema:
- ¿Quién? → Usuario que realizó la acción
- ¿Dónde? → Pantalla/módulo donde ocurrió
- ¿Qué? → Tipo de actividad (listar, guardar, eliminar, etc.)
- ¿Cuándo? → Fecha y hora exacta
- ¿Resultado? → Mensaje descriptivo y tipo de evento

### 2. 📊 **Auditoría del Sistema**
Permite rastrear todas las acciones realizadas por los usuarios para:
- Identificar cambios en los datos
- Detectar problemas o errores
- Cumplir con requisitos de auditoría
- Analizar patrones de uso

### 3. 🔍 **Debugging y Troubleshooting**
Facilita la identificación de problemas:
- Ver qué operaciones se ejecutaron antes de un error
- Rastrear el flujo de ejecución
- Identificar acciones que causaron problemas

### 4. 📁 **Organización por Fecha**
Los logs se organizan automáticamente en archivos diarios, facilitando:
- Búsqueda de actividades por fecha
- Gestión del espacio en disco
- Rotación y archivado de logs antiguos

---

## Código Completo

```php
<?php

class RegistroActividad

{
  private $directorio = '../public/logs/'; // Directorio donde se guardarán los archivos JSON

  public function __construct()
  {
    // Verificar si el directorio existe, si no, crearlo
    if (!file_exists($this->directorio)) {
      mkdir($this->directorio, 0777, true);
    }
  }

  /**
   * Guarda una nueva actividad en el archivo JSON correspondiente al día.
   *
   * @param string $usuario Nombre del usuario.
   * @param string $pantalla Pantalla donde ocurrió la actividad.
   * @param string $actividad Acción realizada (listar, guardar, activar, desactivar, etc.).
   * @param string $mensaje Mensaje adicional sobre la actividad.
   * @param string $tipo Tipo de evento (info, error, warning, success).
   */
public function registrarActividad($usuario, $pantalla, $actividad, $mensaje, $tipo)
{
    date_default_timezone_set('Europe/Madrid'); // Ajusta según tu zona horaria
    // Obtener la fecha actual para nombrar el archivo
    $fechaActual = date('Y-m-d');
    $archivo = $this->directorio . $fechaActual . '.json';

    // Si el archivo no existe, crearlo vacío y asignarle permisos completos
    if (!file_exists($archivo)) {
        file_put_contents($archivo, json_encode([], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        chmod($archivo, 0777); // Asignar permisos totales al archivo
    }

    // Cargar el contenido existente del archivo
    $contenido = file_get_contents($archivo);
    $actividades = json_decode($contenido, true) ?? [];

    // Crear el nuevo registro de actividad
    $nuevaActividad = [
        'usuario' => $usuario,
        'pantalla' => $pantalla,
        'actividad' => $actividad,
        'mensaje' => $mensaje,
        'tipo' => $tipo,
        'fecha_hora' => date('Y-m-d H:i:s')
    ];

    // Agregar la nueva actividad al array
    $actividades[] = $nuevaActividad;

    // Guardar el array actualizado en el archivo JSON
    file_put_contents($archivo, json_encode($actividades, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}


  /**
   * Lista todas las actividades registradas en un archivo JSON específico.
   *
   * @param string $fecha La fecha del archivo a listar (formato YYYY-MM-DD).
   * @return array Un array con las actividades registradas o un mensaje de error.
   */
  public function listarActividades($fecha)
  {
    $archivo = $this->directorio . $fecha . '.json';

    if (!file_exists($archivo)) {
      return ['error' => "No existe ningún registro para la fecha $fecha."];
    }

    $contenido = file_get_gets($archivo);
    return json_decode($contenido, true) ?? [];
  }

    public function generarToken($length = 30) 
    {
      // Caracteres permitidos (letras minúsculas y números)
      $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
      $charactersLength = strlen($characters);
      $randomString = '';
      for ($i = 0; $i < $length; $i++) {
          $randomString .= $characters[random_int(0, $charactersLength - 1)];
      }
      return $randomString;
  }

}

?>
```

---

## Métodos de la Clase

### 1. `__construct()`

**Propósito:** Inicializa la clase y asegura que el directorio de logs existe.

**Flujo:**
```php
public function __construct()
{
    if (!file_exists($this->directorio)) {
        mkdir($this->directorio, 0777, true);
    }
}
```

**Características:**
- Se ejecuta automáticamente al crear una instancia de la clase
- Verifica si el directorio `../public/logs/` existe
- Si no existe, lo crea con permisos `0777` (lectura/escritura/ejecución para todos)
- El parámetro `true` en `mkdir()` crea directorios intermedios si son necesarios

---

### 2. `registrarActividad()` ⭐ **MÉTODO PRINCIPAL**

**Propósito:** Registra una actividad en el archivo JSON del día actual.

**Parámetros:**
| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `$usuario` | string | Identificador del usuario | `'admin'`, `'usuario123'` |
| `$pantalla` | string | Módulo o archivo donde ocurrió | `'presupuesto.php'`, `'cliente.php'` |
| `$actividad` | string | Acción realizada | `'Guardar'`, `'Eliminar'`, `'Listar'` |
| `$mensaje` | string | Descripción detallada | `'Presupuesto guardado con ID: 42'` |
| `$tipo` | string | Tipo de evento | `'info'`, `'error'`, `'warning'`, `'success'` |

**Flujo de Ejecución:**

```
1. Establecer zona horaria → Europe/Madrid
2. Obtener fecha actual → 2025-12-14
3. Construir nombre archivo → ../public/logs/2025-12-14.json
4. Si archivo no existe:
   ├─ Crear archivo con array vacío []
   └─ Asignar permisos 0777
5. Leer contenido existente del archivo
6. Decodificar JSON a array PHP
7. Crear nuevo registro con timestamp
8. Agregar registro al array
9. Guardar array completo en archivo JSON
```

**Ejemplo de Uso en un Controller:**

```php
require_once '../config/funciones.php';

$registro = new RegistroActividad();

$registro->registrarActividad(
    'admin',
    'presupuesto.php',
    'Guardar el presupuesto',
    "Presupuesto guardado exitosamente con ID: 42",
    'info'
);
```

**Formato del Registro Guardado:**

```json
{
  "usuario": "admin",
  "pantalla": "presupuesto.php",
  "actividad": "Guardar el presupuesto",
  "mensaje": "Presupuesto guardado exitosamente con ID: 42",
  "tipo": "info",
  "fecha_hora": "2025-12-14 15:30:45"
}
```

**Características Técnicas:**

✅ **Zona Horaria:** Usa `Europe/Madrid` explícitamente para consistencia

✅ **Archivos Diarios:** Un archivo JSON por día (`YYYY-MM-DD.json`)

✅ **Formato JSON:** Usa `JSON_PRETTY_PRINT` para legibilidad humana

✅ **UTF-8:** Usa `JSON_UNESCAPED_UNICODE` para preservar caracteres especiales

✅ **Permisos:** Archivos creados con `chmod 0777` para acceso total

✅ **Timestamp:** Fecha y hora en formato `Y-m-d H:i:s`

---

### 3. `listarActividades()`

**Propósito:** Recupera todas las actividades registradas en una fecha específica.

**Parámetros:**
| Parámetro | Tipo | Descripción | Ejemplo |
|-----------|------|-------------|---------|
| `$fecha` | string | Fecha en formato YYYY-MM-DD | `'2025-12-14'` |

**Retorno:**
- **Array** con todas las actividades del día si el archivo existe
- **Array con error** si no existe el archivo para esa fecha

**Ejemplo de Uso:**

```php
$registro = new RegistroActividad();
$actividades = $registro->listarActividades('2025-12-14');

if (isset($actividades['error'])) {
    echo "No hay registros para esta fecha";
} else {
    foreach ($actividades as $act) {
        echo "{$act['usuario']} - {$act['actividad']} - {$act['fecha_hora']}\n";
    }
}
```

**Respuesta si existe el archivo:**
```php
[
    [
        'usuario' => 'admin',
        'pantalla' => 'presupuesto.php',
        'actividad' => 'Listar presupuestos',
        'mensaje' => 'Listado obtenido correctamente',
        'tipo' => 'info',
        'fecha_hora' => '2025-12-14 10:15:30'
    ],
    [
        'usuario' => 'admin',
        'pantalla' => 'presupuesto.php',
        'actividad' => 'Guardar presupuesto',
        'mensaje' => 'Presupuesto guardado con ID: 42',
        'tipo' => 'info',
        'fecha_hora' => '2025-12-14 11:45:20'
    ]
]
```

**Respuesta si no existe el archivo:**
```php
[
    'error' => 'No existe ningún registro para la fecha 2025-12-14.'
]
```

---

### 4. `generarToken()`

**Propósito:** Genera un token alfanumérico aleatorio.

**Parámetros:**
| Parámetro | Tipo | Descripción | Valor por defecto |
|-----------|------|-------------|-------------------|
| `$length` | int | Longitud del token a generar | `30` |

**Retorno:** String alfanumérico (solo minúsculas y números)

**Ejemplo de Uso:**

```php
$registro = new RegistroActividad();

// Token de 30 caracteres (por defecto)
$token1 = $registro->generarToken();
// Ejemplo: "a3k5m9z2l7p1q4w8e6r0t2y5u8"

// Token de 20 caracteres
$token2 = $registro->generarToken(20);
// Ejemplo: "x2n5b9v7c4m1k8j3f6"

// Token de 10 caracteres
$token3 = $registro->generarToken(10);
// Ejemplo: "k3m9p2l5r8"
```

**Características:**
- Usa `random_int()` para generación segura de números aleatorios
- Solo incluye letras minúsculas (a-z) y dígitos (0-9)
- Útil para generar identificadores únicos, tokens de sesión, etc.

**⚠️ Nota:** Aunque esta función está en la clase `RegistroActividad`, no tiene relación directa con el logging. Probablemente debería estar en una clase de utilidades separada.

---

## Estructura de los Archivos de Log

### Nomenclatura
```
YYYY-MM-DD.json
```

**Ejemplos:**
- `2025-12-14.json` → Logs del 14 de diciembre de 2025
- `2025-01-01.json` → Logs del 1 de enero de 2025
- `2024-12-31.json` → Logs del 31 de diciembre de 2024

### Contenido del Archivo JSON

```json
[
  {
    "usuario": "admin",
    "pantalla": "presupuesto.php",
    "actividad": "Listar presupuestos",
    "mensaje": "Presupuestos listados correctamente",
    "tipo": "info",
    "fecha_hora": "2025-12-14 09:15:30"
  },
  {
    "usuario": "admin",
    "pantalla": "presupuesto.php",
    "actividad": "Guardar el presupuesto",
    "mensaje": "Presupuesto guardado exitosamente con ID: 123",
    "tipo": "info",
    "fecha_hora": "2025-12-14 09:20:45"
  },
  {
    "usuario": "admin",
    "pantalla": "cliente.php",
    "actividad": "Actualizar cliente",
    "mensaje": "Cliente actualizado ID: 45",
    "tipo": "info",
    "fecha_hora": "2025-12-14 10:05:12"
  },
  {
    "usuario": "admin",
    "pantalla": "producto.php",
    "actividad": "Eliminar producto",
    "mensaje": "Error al eliminar: Producto tiene dependencias",
    "tipo": "error",
    "fecha_hora": "2025-12-14 11:30:00"
  }
]
```

---

## Tipos de Eventos

La clase soporta diferentes tipos de eventos para categorizar las actividades:

| Tipo | Uso | Ejemplo |
|------|-----|---------|
| `info` | Operaciones normales exitosas | Listar, Mostrar, Obtener datos |
| `success` | Operaciones de modificación exitosas | Guardar, Actualizar, Activar |
| `warning` | Advertencias o situaciones inusuales | Validación fallida, Dato duplicado |
| `error` | Errores en operaciones | Fallo en INSERT, Excepción capturada |

---

## Uso en Controllers

### Patrón Típico en un Controller

```php
<?php
require_once "../config/conexion.php";
require_once "../models/Presupuesto.php";
require_once '../config/funciones.php';

// Crear instancia de RegistroActividad
$registro = new RegistroActividad();
$presupuesto = new Presupuesto();

switch ($_GET["op"]) {
    
    case "listar":
        $datos = $presupuesto->get_presupuestos();
        
        // ✅ Registrar actividad de listado
        $registro->registrarActividad(
            'admin',
            'presupuesto.php',
            'Listar presupuestos',
            "Listado obtenido correctamente",
            "info"
        );
        
        echo json_encode($datos);
        break;
        
    case "guardaryeditar":
        try {
            if (empty($_POST["id_presupuesto"])) {
                // INSERT
                $resultado = $presupuesto->insert_presupuesto(...);
                
                if ($resultado > 0) {
                    // ✅ Registrar éxito
                    $registro->registrarActividad(
                        'admin',
                        'presupuesto.php',
                        'Guardar el presupuesto',
                        "Presupuesto guardado exitosamente con ID: $resultado",
                        "info"
                    );
                    
                    echo json_encode(['success' => true]);
                }
            } else {
                // UPDATE
                $resultado = $presupuesto->update_presupuesto(...);
                
                if ($resultado) {
                    // ✅ Registrar actualización
                    $registro->registrarActividad(
                        'admin',
                        'presupuesto.php',
                        'Actualizar el presupuesto',
                        "Presupuesto actualizado ID: " . $_POST["id_presupuesto"],
                        "info"
                    );
                }
            }
        } catch (Exception $e) {
            // ❌ Registrar error
            $registro->registrarActividad(
                'admin',
                'presupuesto.php',
                'Error al guardar presupuesto',
                "Error: " . $e->getMessage(),
                "error"
            );
        }
        break;
}
?>
```

### Inicialización Estándar

En la mayoría de los controllers del proyecto, se sigue este patrón:

```php
require_once '../config/funciones.php';
$registro = new RegistroActividad();
```

Esta instancia `$registro` se reutiliza en todos los casos del switch del controller.

---

## Ventajas del Sistema de Logging

### ✅ **1. Trazabilidad Completa**
Cada acción queda registrada con todos sus detalles:
- Quién realizó la acción
- En qué módulo/pantalla
- Qué operación se ejecutó
- Cuándo exactamente
- Resultado de la operación

### ✅ **2. Debugging Facilitado**
Los logs JSON son fáciles de:
- Leer y analizar
- Parsear con herramientas
- Buscar con grep o editores de texto
- Procesar con scripts

### ✅ **3. Organización Automática**
- Archivos separados por día
- Sin necesidad de rotación manual
- Fácil identificar logs antiguos para archivar
- Nomenclatura clara y consistente

### ✅ **4. Formato JSON Legible**
- `JSON_PRETTY_PRINT` hace los archivos legibles
- `JSON_UNESCAPED_UNICODE` preserva acentos y caracteres especiales
- Estructura consistente facilita el parsing

### ✅ **5. Sin Dependencias de BD**
- No requiere conexión a base de datos
- No afecta al rendimiento de queries
- Los logs persisten aunque la BD falle
- Independiente de problemas de conexión

### ✅ **6. Permisos Flexibles**
- Archivos con `chmod 0777` aseguran acceso
- Directorio creado automáticamente
- Sin problemas de permisos en diferentes entornos

---

## Consideraciones y Limitaciones

### ⚠️ **1. Usuario Hardcodeado**

Actualmente, muchos controllers usan `'admin'` hardcodeado:
```php
$registro->registrarActividad('admin', ...);
```

**Mejora sugerida:** Usar sesiones para identificar al usuario real:
```php
$registro->registrarActividad($_SESSION['id_usuario'] ?? 'sistema', ...);
```

### ⚠️ **2. Permisos 0777**

Los permisos `0777` son muy permisivos y pueden ser un riesgo de seguridad.

**Mejora sugerida:** Usar `0755` o `0644`:
```php
chmod($archivo, 0755); // rwxr-xr-x
```

### ⚠️ **3. Crecimiento de Archivos**

Los archivos JSON pueden crecer mucho en días con mucha actividad.

**Mejoras sugeridas:**
- Implementar rotación automática
- Comprimir logs antiguos
- Establecer límite de tamaño
- Archivar logs en almacenamiento secundario

### ⚠️ **4. Concurrencia**

Si múltiples procesos escriben simultáneamente, puede haber pérdida de datos.

**Mejora sugerida:** Implementar file locking:
```php
$fp = fopen($archivo, 'c+');
if (flock($fp, LOCK_EX)) {
    // Escribir de forma segura
    flock($fp, LOCK_UN);
}
fclose($fp);
```

### ⚠️ **5. Validación de Datos**

No se validan los parámetros de entrada.

**Mejora sugerida:** Validar tipos y longitudes:
```php
public function registrarActividad($usuario, $pantalla, $actividad, $mensaje, $tipo)
{
    // Validaciones
    if (empty($usuario) || empty($pantalla)) {
        return false;
    }
    
    $tiposValidos = ['info', 'error', 'warning', 'success'];
    if (!in_array($tipo, $tiposValidos)) {
        $tipo = 'info';
    }
    
    // ... resto del código
}
```

---

## Relación con Otros Archivos del Proyecto

### Archivos que Usan RegistroActividad

Los controllers que registran actividades incluyen:

```
controller/
├── presupuesto.php ✅ Usa RegistroActividad
├── cliente.php ✅ Usa RegistroActividad
├── producto.php ✅ Usa RegistroActividad
├── proveedor.php ✅ Usa RegistroActividad
├── familia.php ✅ Usa RegistroActividad
├── marca.php ✅ Usa RegistroActividad
└── ... (otros controllers)
```

### Archivos de Configuración Relacionados

```
config/
├── funciones.php ← Contiene RegistroActividad
├── conexion.php ← Conexión a BD
└── conexion.json ← Credenciales
```

### Directorio de Salida

```
public/
└── logs/
    ├── 2025-12-01.json ← Logs del 1 de diciembre
    ├── 2025-12-02.json ← Logs del 2 de diciembre
    ├── 2025-12-14.json ← Logs del 14 de diciembre
    └── log_20251214.txt ← Logs de desarrollo (writeToLog)
```

**Nota:** El directorio también contiene archivos `.txt` generados por la función `writeToLog()` que algunos controllers usan para debugging adicional.

---

## Comparación con Otros Sistemas de Logging

| Característica | RegistroActividad | Monolog | Syslog | BD Logs |
|----------------|-------------------|---------|---------|---------|
| **Formato** | JSON | Multiple | Text | Tablas |
| **Configuración** | Ninguna | Media | Media | Alta |
| **Legibilidad** | ⭐⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐ |
| **Rendimiento** | ⭐⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐⭐⭐ | ⭐⭐⭐ |
| **Búsqueda** | ⭐⭐⭐ | ⭐⭐⭐⭐ | ⭐⭐⭐ | ⭐⭐⭐⭐⭐ |
| **Dependencias** | Ninguna | Composer | Sistema | BD |
| **Rotación** | Manual | Automática | Automática | N/A |
| **Ideal para** | Proyectos simples | Proyectos grandes | Servidores | Análisis complejo |

---

## Ejemplo Completo de Flujo

### 1. Usuario hace una petición
```javascript
// Desde la vista: presupuesto.js
$.ajax({
    url: '../controller/presupuesto.php?op=guardaryeditar',
    type: 'POST',
    data: formData,
    success: function(response) {
        console.log('Presupuesto guardado');
    }
});
```

### 2. Controller procesa la petición
```php
// En controller/presupuesto.php
require_once '../config/funciones.php';
$registro = new RegistroActividad();

switch ($_GET["op"]) {
    case "guardaryeditar":
        $resultado = $presupuesto->insert_presupuesto(...);
        
        if ($resultado > 0) {
            $registro->registrarActividad(
                'admin',
                'presupuesto.php',
                'Guardar el presupuesto',
                "Presupuesto guardado con ID: $resultado",
                'info'
            );
        }
        break;
}
```

### 3. RegistroActividad guarda el log
```php
// En config/funciones.php
public function registrarActividad($usuario, $pantalla, $actividad, $mensaje, $tipo)
{
    $archivo = '../public/logs/2025-12-14.json';
    // ... proceso de guardado
}
```

### 4. Se crea/actualiza el archivo JSON
```json
// En public/logs/2025-12-14.json
[
  {
    "usuario": "admin",
    "pantalla": "presupuesto.php",
    "actividad": "Guardar el presupuesto",
    "mensaje": "Presupuesto guardado con ID: 123",
    "tipo": "info",
    "fecha_hora": "2025-12-14 15:45:30"
  }
]
```

---

## Resumen

La clase **RegistroActividad** ubicada en `w:\MDR\config\funciones.php` es el sistema de logging y auditoría del proyecto que:

1. ✅ **Registra todas las actividades** del sistema en archivos JSON diarios
2. ✅ **Organiza automáticamente** los logs por fecha (un archivo por día)
3. ✅ **Proporciona trazabilidad completa** de operaciones (quién, qué, cuándo, dónde)
4. ✅ **Facilita debugging** con formato legible y estructurado
5. ✅ **No depende de la base de datos** para su funcionamiento
6. ✅ **Se integra fácilmente** en todos los controllers del proyecto

**Localización clave:**
- **Archivo de clase:** `w:\MDR\config\funciones.php`
- **Directorio de logs:** `w:\MDR\public\logs/`
- **Inclusión típica:** `require_once '../config/funciones.php';`

---

## Enlaces Relacionados

- [Documentación de Controllers](controller.md) - Uso de RegistroActividad en controllers
- [Documentación de Conexión](conexion.md) - Sistema de conexión a base de datos
- [Estructura de Carpetas](estructura_carpetas.md) - Arquitectura general del proyecto
