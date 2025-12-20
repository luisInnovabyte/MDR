# Sistema de Control de Accesos por Roles

## 📋 Descripción General

Esta aplicación implementa un sistema de control de accesos basado en roles (RBAC - Role-Based Access Control) que restringe el acceso a diferentes módulos y funcionalidades según el rol asignado a cada usuario.

## 🗄️ Estructura de Base de Datos

### Tabla `roles`

```sql
CREATE TABLE `roles` (
  `id_rol` int NOT NULL,
  `nombre_rol` varchar(50) NOT NULL,
  `est` tinyint DEFAULT '1' COMMENT 'est = 0 --> Inactivo, est = 1 --> Activo'
)
```

**Roles definidos:**
- **1**: Empleado
- **2**: Gestor
- **3**: Administrador
- **4**: Comercial
- **5**: Técnico ✨ **(Nuevo)** - Acceso al Área Técnica

### Tabla `usuarios`

```sql
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `email` varchar(60),
  `contrasena` varchar(255),
  `nombre` varchar(60),
  `fecha_crea` datetime DEFAULT CURRENT_TIMESTAMP,
  `est` tinyint DEFAULT NULL COMMENT 'est = 0 --> Inactivo, est = 1 --> activo',
  `id_rol` int NOT NULL,
  `tokenUsu` longtext
)
```

## 🔐 Flujo de Autenticación

### 1. Inicio de Sesión (`controller/login.php`)

Cuando un usuario inicia sesión (caso `iniciarSesion`):

```php
$user = $login->verificarUsuario($email, $password);

if ($user) {
    $_SESSION = [
        'id_usuario' => $user['id_usuario'],
        'email' => $user['email'],
        'nombre' => $user['nombre'],
        'fecha_crea' => $user['fecha_crea'],
        'est' => $user['est'],
        'id_rol' => $user['id_rol'],           // ← ROL DEL USUARIO
        'tokenUsu' => $user['tokenUsu'],
        'nombre_rol' => $user['nombre_rol'],
        'id_comercial' => $user['id_comercial'],
        'sesion_iniciada' => true
    ];
}
```

**Verificación en Base de Datos:**

```php
public function verificarUsuario($email, $contrasena) {
    $sql = "SELECT usuarios.*, roles.nombre_rol, comerciales.id_comercial 
            FROM usuarios 
            INNER JOIN roles ON usuarios.id_rol = roles.id_rol
            LEFT JOIN comerciales ON comerciales.id_usuario = usuarios.id_usuario
            WHERE usuarios.email = ? AND usuarios.contrasena = ?";
}
```

### 2. Validación del Estado de la Cuenta

Antes de crear la sesión, se verifica:

```php
if ($user['est'] == 0) {
    // Cuenta inhabilitada
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Cuenta inhabilitada']);
    exit;
}
```

## 🛡️ Sistema de Control de Permisos

### Nivel 1: Control en el Sidebar (`config/template/mainSidebar.php`)

**Función de Control:**

```php
function puedeVerMenu($idRol, $modulo) {
    $permisos = [
        'usuarios'       => [2, 3],          // Gestor y Admin
        'logs'           => [2, 3],          // Gestor y Admin
        'mantenimientos' => [2, 3, 4],       // Gestor, Admin y Comercial
        'llamadas'       => [2, 3, 4],       // Gestor, Admin y Comercial
        'comerciales'    => [3],             // Solo Admin
        'dashboard'      => [2, 3, 4],       // Gestor, Admin y Comercial
        'area_tecnica'   => [2, 3, 5],       // Gestor, Admin y Técnico ✨
    ];
    return in_array($idRol, $permisos[$modulo] ?? []);
}
```

**Uso en el menú:**

```php
$idRolUsuario = $_SESSION['id_rol'] ?? 0;

<?php if (puedeVerMenu($idRolUsuario, 'dashboard')): ?>
    <li class="br-menu-item">
        <a href="../Dashboard/index.php" class="br-menu-link">
            <i class="menu-item-icon icon ion-ios-home tx-24"></i>
            <span class="menu-item-label">Dashboard</span>
        </a>
    </li>
<?php endif; ?>
```

### Nivel 2: Control a Nivel de Vista (`config/template/verificarPermiso.php`)

Este archivo se incluye al inicio de cada vista protegida para validar acceso:

```php
session_start();

// Verificar si hay sesión iniciada
if (!isset($_SESSION['sesion_iniciada']) || $_SESSION['sesion_iniciada'] !== true) {
    header("Location: ../../view/Home/index.php");
    exit();
}

// Verificar que se haya definido el módulo actual
if (!isset($moduloActual)) {
    die("Error: No se definió el módulo actual para verificar permisos.");
}

// Obtener rol del usuario
$idRol = $_SESSION['id_rol'] ?? null;

// Permisos por rol
$permisosPorRol = [, 'area_tecnica'], // Gestor
    3 => ['usuarios', 'logs', 'mantenimientos', 'comerciales', 'llamadas', 'dashboard', 'area_tecnica'], // Admin
    4 => ['llamadas', 'mantenimientos', 'dashboard'], // Comercial
    5 => ['area_tecnica', 'elementos_consulta', 'documentos_tecnico', 'consultas_tecnico', 'informes_tecnico'], // Técnico ✨amadas', 'dashboard'], // Admin
    4 => ['llamadas', 'mantenimientos', 'dashboard'], // Comercial
];

// Validar permiso
if (!isset($permisosPorRol[$idRol]) || !in_array($moduloActual, $permisosPorRol[$idRol])) {
    header("Location: ../../view/Home/accesoDenegado.php");
    exit();
}
```

**Uso en una vista protegida (`view/Dashboard/index.php`):**

```php
<?php $moduloActual = 'dashboard'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>

<!DOCTYPE html>
<html lang="es">
<!-- Resto del contenido de la página -->
```

## 📊 Matriz de Permisos Actual
 Técnico (5) ✨ |
|------------------|--------------|------------|-----------|---------------|----------------|
| Dashboard        | ❌           | ✅         | ✅        | ✅            | ❌             |
| Usuarios         | ❌           | ✅         | ✅        | ❌            | ❌             |
| Roles            | ❌           | ✅         | ✅        | ❌            | ❌             |
| Logs             | ❌           | ✅         | ✅        | ❌            | ❌             |
| Mantenimientos   | ❌           | ✅         | ✅        | ✅            | ❌             |
| Llamadas         | ❌           | ✅         | ✅        | ✅            | ❌             |
| Comerciales      | ❌           | ❌         | ✅        | ❌            | ❌             |
| Clientes/Proveedores | ✅      | ✅         | ✅        | ✅            | ❌             |
| Informes         | ❌           | ✅         | ✅        | ❌            | ❌             |
| **Área Técnica** ✨ | ❌       | ✅         | ✅        | ❌            | ✅            |
| Informes         | ❌           | ✅         | ✅        | ❌            |

## 🔄 Flujo Completo de Verificación

```
1. Usuario intenta acceder → view/Dashboard/index.php
                              ↓
2. Se define el módulo     → $moduloActual = 'dashboard';
                              ↓
3. Se incluye verificador  → verificarPermiso.php
                              ↓
4. Verifica sesión activa  → $_SESSION['sesion_iniciada']
                              ↓
5. Obtiene rol del usuario → $_SESSION['id_rol']
                              ↓
6. Valida permiso          → in_array($moduloActual, $permisosPorRol[$idRol])
                              ↓
7a. PERMITIDO              → Carga la vista
7b. DENEGADO               → Redirección a accesoDenegado.php
```

## 🚪 Cierre de Sesión

```php
case "cerrarSesion":
    session_start();
    $email = $_SESSION['email'] ?? 'usuario desconocido';
    
    // Destruir sesión
    session_unset();
    session_destroy();
    
    // Registrar actividad
    $registro->registrarActividad($email, 'login.php', 'cerrarSesion', 
                                  'Sesión cerrada correctamente.', 'info');
    
    echo json_encode(['success' => true, 'message' => 'Sesión cerrada correctamente.']);
    exit;
```

## 🛠️ Implementación en Nuevas Vistas

Para proteger una nueva vista:

```php
<?php 
// 1. Definir el módulo (debe coincidir con la configuración de permisos)
$moduloActual = 'nombre_del_modulo'; 

// 2. Incluir el verificador de permisos
require_once('../../config/template/verificarPermiso.php'); 
?>

<!DOCTYPE html>
<html lang="es">
<!-- Tu contenido aquí -->
```

## 📝 Notas Importantes

1. **Sincronización de permisos**: Los permisos están definidos en DOS lugares:
   - `config/template/mainSidebar.php` (control visual del menú)
   - `config/template/verificarPermiso.php` (control de acceso real)
   
   **Ambos deben mantenerse sincronizados.**

2. **Seguridad por capas**: El sistema implementa dos niveles:
   - **Visual** (sidebar): Oculta opciones no permitidas
   - **Backend** (verificarPermiso): Bloquea acceso directo por URL

3. **Sesión iniciada**: Todas las verificaciones dependen de `$_SESSION['sesion_iniciada']` y `$_SESSION['id_rol']`

4. **Redirecciones**:
   - Sin sesión → `view/Home/index.php`
   - Sin permiso → `view/Home/accesoDenegado.php`

## 🔍 Ejemplos Prácticos por Rol

### Rol Comercial (ID 4)

Un usuario con `id_rol = 4` (Comercial):

- ✅ **Puede acceder a**: Dashboard, Llamadas, Mantenimientos
- ❌ **NO puede acceder a**: Usuarios, Logs, Comerciales, Informes, Área Técnica
- 🔒 Si intenta acceder directamente a `view/Usuarios/index.php` → Redirigido a `accesoDenegado.php`

### Rol Técnico (ID 5) ✨ **(Nuevo)**

Un usuario con `id_rol = 5` (Técnico):

- ✅ **Puede acceder a**: 
  - Consulta de Elementos (solo lectura)
  - Estados de Elementos
  - Documentos de Elementos
  - Fotos de Elementos
  - Consulta de Garantías
  - Consulta de Mantenimientos
  - Gestor Documental Técnico
  - Informes técnicos (Calendario Garantías y Mantenimientos)
  
- ❌ **NO puede acceder a**: Dashboard, Usuarios, Logs, Mantenimientos generales, Llamadas, Comerciales, Presupuestos
- 🔒 Acceso restringido únicamente al Área Técnica mediante `$moduloActual = 'area_tecnica'`

## 🛠️ Módulos del Área Técnica

El nuevo módulo **Área Técnica** incluye las siguientes pantallas:

| Pantalla | Ruta | Descripción | Permisos |
|----------|------|-------------|----------|
| **Consulta de Elementos** | `view/MntElementos_consulta/index.php` | Vista de solo lectura de todos los elementos | Técnico, Gestor, Admin |
| **Estados de Elementos** | `view/MntEstados_elemento/index.php` | Gestión de estados de elementos | Técnico, Gestor, Admin |
| **Documentos de Elementos** | `view/MntDocumento_elemento/index.php` | Gestión de documentación técnica | Técnico, Gestor, Admin |
| **Fotos de Elementos** | `view/MntFoto_elemento/index.php` | Gestión de fotografías de elementos | Técnico, Gestor, Admin |
| **Consulta Garantías** | `view/Consulta_Garantias/index.php` | Vista de consulta de garantías vigentes | Técnico, Gestor, Admin |
| **Consulta Mantenimientos** | `view/Consulta_Mantenimientos/index.php` | Vista de consulta de mantenimientos programados | Técnico, Gestor, Admin |
| **Gestor Documental Técnico** | `view/Documento/index_tecnico.php` | Gestor de documentos técnicos (manuales, certificados) | Técnico, Gestor, Admin |
| **Calendario Garantías** | `view/Informe_vigencia/index.php` | Calendario de garantías | Técnico, Gestor, Admin |
| **Calendario Mantenimientos** | `view/Informe_mantenimiento/index.php` | Calendario de mantenimientos | Técnico, Gestor, Admin |

### Configuración de Permisos en Vistas del Área Técnica

Todas las vistas del área técnica deben incluir:

```php
<?php $moduloActual = 'area_tecnica'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>
```

## 📅 Última Actualización

Sistema documentado y actualizado con el **Rol Técnico** el **20 de diciembre de 2025**.

### Cambios recientes:
- ✨ Agregado rol **Técnico (ID 5)** con acceso al Área Técnica
- 🔧 Creada sección **Área Técnica** en el menú principal
- 📋 Actualizados permisos en 9 vistas específicas para técnicos
- 📝 Actualizada matriz de permisos y documentación completa
