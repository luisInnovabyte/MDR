# 🔧 Implementación del Área Técnica - MDR ERP Manager

## ✅ Implementación Completada

**Fecha:** 20 de diciembre de 2025  
**Branch:** `interface`  
**Autor:** Luis - Innovabyte

---

## 📋 Resumen de Cambios

Se ha implementado un nuevo **módulo de Área Técnica** con su propio rol de usuario y sección en el menú principal. Esta implementación permite una mejor organización de las funcionalidades técnicas del sistema.

### 🎯 Objetivo

Centralizar todas las pantallas y funcionalidades relacionadas con técnicos en una sección específica del menú, con permisos diferenciados por rol.

---

## 📂 ARCHIVOS QUE DEBES MODIFICAR PARA CREAR UNA NUEVA SECCIÓN

### 🔴 Archivos OBLIGATORIOS (4 archivos)

#### 1️⃣ **Base de Datos** - Crear rol (si es necesario)
📁 **Ubicación:** `BD/crear_rol_[nombre].sql`

**¿Cuándo crearlo?** Solo si necesitas un nuevo rol específico (ej: Técnico, Comercial, etc.)

```sql
INSERT INTO roles (id_rol, nombre_rol, est)
SELECT [ID], '[Nombre Rol]', 1
WHERE NOT EXISTS (
    SELECT 1 FROM roles WHERE id_rol = [ID]
);
```

**Ejemplo:**
```sql
-- Para crear rol Técnico (ID 5)
INSERT INTO roles (id_rol, nombre_rol, est)
SELECT 5, 'Técnico', 1
WHERE NOT EXISTS (
    SELECT 1 FROM roles WHERE id_rol = 5
);
```

---

#### 2️⃣ **Sistema de Menú** - `config/template/mainSidebar.php`
📁 **Ubicación:** `config/template/mainSidebar.php`

**¿Qué modificar?**

**A) Agregar permiso en función `puedeVerMenu()`**
```php
// Buscar la función puedeVerMenu() y agregar tu módulo
function puedeVerMenu($idRol, $modulo) {
    $permisos = [
        'dashboard' => [2, 3, 4],
        'area_tecnica' => [2, 3, 5],
        'tu_nuevo_modulo' => [2, 3, 4], // ✨ AGREGAR AQUÍ
        // ... otros módulos
    ];
    return isset($permisos[$modulo]) && in_array($idRol, $permisos[$modulo]);
}
```

**B) Agregar sección HTML del menú**
```php
// Buscar las secciones <li class="br-menu-item"> y agregar la tuya
<?php if (puedeVerMenu($idRolUsuario, 'tu_nuevo_modulo')): ?>
<li class="br-menu-item">
    <a href="#" class="br-menu-link with-sub">
        <i class="menu-item-icon icon ion-[icono] tx-24"></i>
        <span class="menu-item-label">Tu Nueva Sección</span>
    </a>
    <ul class="br-menu-sub">
        <!-- Agregar subsecciones (opcional) -->
        <li class="sub-item" style="pointer-events: none; color: #333; font-weight: bold; font-size: 12px; text-transform: uppercase; padding: 8px 15px; background-color: #f8f9fa; margin: 2px 0;">
            📊 CATEGORÍA 1
        </li>
        <li class="sub-item"><a href="../MntPantalla1/index.php" class="sub-link">Pantalla 1</a></li>
        <li class="sub-item"><a href="../MntPantalla2/index.php" class="sub-link">Pantalla 2</a></li>
    </ul>
</li>
<?php endif; ?>
```

**Iconos disponibles (Ionicons):**
- `ion-briefcase` (maletín)
- `ion-calendar` (calendario)
- `ion-settings` (engranaje)
- `ion-wrench` (llave inglesa)
- `ion-folder` (carpeta)
- `ion-people` (personas)
- `ion-phone` (teléfono)
- Ver más en: https://ionic.io/ionicons/v4

---

#### 3️⃣ **Control de Permisos** - `config/template/verificarPermiso.php`
📁 **Ubicación:** `config/template/verificarPermiso.php`

**¿Qué modificar?** Agregar tu módulo al array `$permisosPorRol`

```php
// Buscar el array $permisosPorRol y agregar permisos
$permisosPorRol = [
    1 => [], // Empleado
    2 => [
        'dashboard', 
        'usuarios', 
        'mantenimientos',
        'area_tecnica',
        'tu_nuevo_modulo' // ✨ AGREGAR AQUÍ
    ], // Gestor
    3 => [
        'dashboard', 
        'usuarios', 
        'mantenimientos',
        'area_tecnica',
        'tu_nuevo_modulo' // ✨ AGREGAR AQUÍ
    ], // Admin
    4 => ['dashboard', 'tu_nuevo_modulo'], // Comercial (si aplica)
    5 => ['area_tecnica'] // Técnico
];
```

**Submódulos (opcional):** Si tienes subcategorías dentro de tu módulo:
```php
$permisosPorRol = [
    2 => [
        'tu_nuevo_modulo',
        'submodulo_1', // Para pantallas específicas
        'submodulo_2'
    ]
];
```

---

#### 4️⃣ **Documentación** - `control-accesos-roles.md`
📁 **Ubicación:** `control-accesos-roles.md`

**¿Qué modificar?**

**A) Actualizar función `puedeVerMenu()` con tu módulo**
```php
function puedeVerMenu($idRol, $modulo) {
    $permisos = [
        'dashboard' => [2, 3, 4],
        'usuarios' => [2, 3],
        'area_tecnica' => [2, 3, 5],
        'tu_nuevo_modulo' => [2, 3, 4], // ✨ AGREGAR
        // ...
    ];
    // ...
}
```

**B) Actualizar matriz de permisos**
```markdown
| Módulo | Empleado | Gestor | Admin | Comercial | Técnico |
|--------|----------|--------|-------|-----------|---------||
| Dashboard | ❌ | ✅ | ✅ | ✅ | ❌ |
| Área Técnica | ❌ | ✅ | ✅ | ❌ | ✅ |
| **Tu Nuevo Módulo** | ❌ | ✅ | ✅ | ✅ | ❌ |
```

**C) Agregar tabla de pantallas del módulo**
```markdown
### Pantallas del Módulo "Tu Nuevo Módulo"

| Pantalla | Archivo | Descripción |
|----------|---------|-------------|
| Pantalla 1 | `view/MntPantalla1/index.php` | Descripción 1 |
| Pantalla 2 | `view/MntPantalla2/index.php` | Descripción 2 |
```

**D) Actualizar fecha de modificación**
```markdown
**Última actualización:** [Fecha actual]
```

---

### 🟡 Archivos OPCIONALES (según necesidad)

#### 5️⃣ **Vistas/Pantallas** - Actualizar `$moduloActual`
📁 **Ubicación:** `view/[TuPantalla]/index.php`

**Solo si creas nuevas vistas** que pertenezcan a tu módulo:

```php
<?php 
// Al inicio del archivo, ANTES de require verificarPermiso.php
$moduloActual = 'tu_nuevo_modulo'; // ✨ Cambiar según tu módulo
require_once '../../config/template/verificarPermiso.php'; 
?>
```

**Ejemplo completo:**
```php
<?php
// Definir módulo actual para control de permisos
$moduloActual = 'tu_nuevo_modulo';
require_once '../../config/template/verificarPermiso.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Tu Nueva Pantalla - MDR</title>
    <!-- ... CSS ... -->
</head>
<body>
    <?php require_once('../../config/template/mainSidebar.php'); ?>
    <!-- ... Contenido ... -->
</body>
</html>
```

---

#### 6️⃣ **Dashboard Específico** - Crear pantalla de acceso rápido
📁 **Ubicación:** `view/Dashboard/dash_[nombre].php`

**Solo si quieres** un dashboard específico para tu módulo:

```php
<?php 
$moduloActual = 'dashboard'; // Dashboard usa permisos de dashboard
require_once '../../config/template/verificarPermiso.php'; 
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <title>Dashboard [Nombre] - MDR</title>
    <!-- Bootstrap, CSS, etc. -->
</head>
<body>
    <?php require_once('../../config/template/mainSidebar.php'); ?>
    
    <div class="br-mainpanel">
        <div class="br-pageheader">
            <h4 class="tx-gray-800 mg-b-5">
                <i class="icon ion-[icono]"></i> Dashboard [Nombre]
            </h4>
            <p class="mg-b-0">Descripción del módulo</p>
        </div>

        <div class="br-pagebody">
            <div class="row row-sm">
                <!-- Tarjetas de acceso rápido -->
                <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mg-b-20">
                    <a href="../MntPantalla1/index.php" class="card-dashboard">
                        <div class="card h-100" style="min-height: 180px; border-radius: 8px;">
                            <div class="card-body d-flex flex-column justify-content-center text-center">
                                <i class="fa fa-[icono] fa-3x mb-3" style="color: #007bff;"></i>
                                <h5 class="card-title">Pantalla 1</h5>
                                <p class="card-text text-muted">Descripción breve</p>
                            </div>
                        </div>
                    </a>
                </div>
                <!-- Más tarjetas... -->
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../public/lib/jquery-3.7.1/jquery.min.js"></script>
    <script src="../../public/lib/bootstrap-5.0.2/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```

**Enlazar desde dashboard principal:**
```php
// En view/Dashboard/index.php
<?php if (puedeVerMenu($idRolUsuario, 'tu_nuevo_modulo')): ?>
<div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 mg-b-20">
    <a href="dash_[nombre].php" class="card-dashboard">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="icon ion-[icono] tx-60 tx-[color]"></i>
                <h5>Tu Módulo</h5>
                <p>Descripción</p>
            </div>
        </div>
    </a>
</div>
<?php endif; ?>
```

---

#### 7️⃣ **Documentación Adicional** (opcional)
📁 **Ubicación:** `docs/IMPLEMENTACION_[NOMBRE].md`

Crear archivo similar a este para documentar la implementación de tu módulo.

---

## 📋 CHECKLIST DE IMPLEMENTACIÓN

Usa esta lista para verificar que has tocado todos los archivos necesarios:

### Archivos Obligatorios (4)
- [ ] **1. Script SQL** - `BD/crear_rol_[nombre].sql` (solo si necesitas nuevo rol)
- [ ] **2. Menú** - `config/template/mainSidebar.php`
  - [ ] Agregar módulo a función `puedeVerMenu()`
  - [ ] Agregar sección HTML con submenús
- [ ] **3. Permisos** - `config/template/verificarPermiso.php`
  - [ ] Agregar módulo a array `$permisosPorRol`
  - [ ] Agregar submódulos si es necesario
- [ ] **4. Documentación** - `control-accesos-roles.md`
  - [ ] Actualizar función `puedeVerMenu()`
  - [ ] Actualizar matriz de permisos
  - [ ] Agregar tabla de pantallas
  - [ ] Actualizar fecha

### Archivos Opcionales
- [ ] **5. Vistas** - `view/[TuPantalla]/index.php` (actualizar `$moduloActual`)
- [ ] **6. Dashboard** - `view/Dashboard/dash_[nombre].php` (crear si necesitas)
- [ ] **7. Docs** - `docs/IMPLEMENTACION_[NOMBRE].md` (documentar proceso)

### Testing
- [ ] Ejecutar script SQL en base de datos
- [ ] Verificar que el rol existe
- [ ] Login con cada rol y verificar accesos
- [ ] Probar acceso por menú
- [ ] Probar acceso directo por URL
- [ ] Verificar redirecciones de acceso denegado

### Git
- [ ] `git add .`
- [ ] `git commit -m "feat: Implementar [Nombre Módulo]"`
- [ ] `git push origin [branch]`

---

## 🆕 Cambios Realizados en Área Técnica

### 1. **Script SQL - Crear Rol Técnico**

📁 **Archivo:** `BD/crear_rol_tecnico.sql`

```sql
INSERT INTO roles (id_rol, nombre_rol, est)
SELECT 5, 'Técnico', 1
WHERE NOT EXISTS (
    SELECT 1 FROM roles WHERE id_rol = 5
);
```

**⚠️ ACCIÓN REQUERIDA:** Ejecutar este script en la base de datos:

```bash
# Opción 1: Desde PowerShell
Get-Content BD\crear_rol_tecnico.sql | mysql -h 217.154.117.83 -P 3308 -u administrator -p toldos_db

# Opción 2: Desde Git Bash
mysql -h 217.154.117.83 -P 3308 -u administrator -p toldos_db < BD/crear_rol_tecnico.sql

# Opción 3: Manual en HeidiSQL/phpMyAdmin
# Copiar y ejecutar el contenido del archivo
```

---

### 2. **Actualización del Sistema de Permisos**

#### 📁 `config/template/mainSidebar.php` ✨ **UBICACIÓN CORREGIDA**

**⚠️ IMPORTANTE:** El archivo estaba inicialmente en `docs/mainSidebar.php` pero fue **movido a su ubicación correcta** en `config/template/mainSidebar.php` durante la implementación.

**Cambios:**
- ✅ Agregado permiso `'area_tecnica' => [2, 3, 5]` en función `puedeVerMenu()`
- ✅ Creada nueva sección de menú "🔧 Área Técnica" con 9 submenús organizados
- ✅ Icono cambiado a `ion-wrench` (llave inglesa)

**Estructura del nuevo menú:**

```
🔧 ÁREA TÉCNICA (Visible para: Gestor, Admin, Técnico)
├── 📊 CONSULTAS
│   ├── Consulta de Elementos
│   ├── Consulta Garantías
│   └── Consulta Mantenimientos
│
├── 📁 DOCUMENTACIÓN
│   └── Gestor Documental Técnico
│
└── 📋 INFORMES
    ├── Calendario Garantías
    └── Calendario Mantenimientos
```

#### 📁 `config/template/verificarPermiso.php`

**Cambios:**
- ✅ Agregado rol Técnico (5) con permisos específicos
- ✅ Añadidos módulos: `'area_tecnica'`, `'elementos_consulta'`, `'documentos_tecnico'`, `'consultas_tecnico'`, `'informes_tecnico'`
- ✅ Extendidos permisos para roles Gestor (2) y Admin (3)

```php
$permisosPorRol = [
    2 => [..., 'area_tecnica', ...],  // Gestor
    3 => [..., 'area_tecnica', ...],  // Admin
    5 => ['area_tecnica', 'elementos_consulta', ...], // Técnico ✨
];
```

---

### 3. **Corrección de Permisos en Vistas**

Se actualizó `$moduloActual` de `'usuarios'` a `'area_tecnica'` en las siguientes vistas:

| Vista | Archivo | Estado |
|-------|---------|--------|
| **Consulta de Elementos** | `view/MntElementos_consulta/index.php` | ✅ Actualizada |
| **Estados de Elementos** | `view/MntEstados_elemento/index.php` | ✅ Actualizada |
| **Documentos de Elementos** | `view/MntDocumento_elemento/index.php` | ✅ Actualizada |
| **Fotos de Elementos** | `view/MntFoto_elemento/index.php` | ✅ Actualizada |
| **Consulta Garantías** | `view/Consulta_Garantias/index.php` | ✅ Actualizada |
| **Consulta Mantenimientos** | `view/Consulta_Mantenimientos/index.php` | ✅ Actualizada |
| **Gestor Documental Técnico** | `view/Documento/index_tecnico.php` | ✅ Actualizada |
| **Calendario Garantías** | `view/Informe_vigencia/index.php` | ✅ Actualizada |
| **Calendario Mantenimientos** | `view/Informe_mantenimiento/index.php` | ✅ Actualizada |

**Antes:**
```php
<?php $moduloActual = 'usuarios'; ?>
```

**Después:**
```php
<?php $moduloActual = 'area_tecnica'; ?>
```

---

### 4. **Documentación Actualizada**

📁 **Archivo:** `control-accesos-roles.md`

**Cambios:**
- ✅ Agregado rol **Técnico (ID 5)** en la lista de roles
- ✅ Actualizada función `puedeVerMenu()` con ejemplo del nuevo permiso
- ✅ Actualizada matriz de permisos con columna "Técnico (5)"
- ✅ Agregada tabla completa de módulos del Área Técnica
- ✅ Incluido ejemplo práctico del rol Técnico
- ✅ Actualizada fecha de última modificación

---

## 🎭 Matriz de Permisos Actualizada

| Módulo | Empleado | Gestor | Admin | Comercial | **Técnico** ✨ |
|--------|----------|--------|-------|-----------|----------------|
| Dashboard | ❌ | ✅ | ✅ | ✅ | ❌ |
| Usuarios | ❌ | ✅ | ✅ | ❌ | ❌ |
| Mantenimientos | ❌ | ✅ | ✅ | ✅ | ❌ |
| Llamadas | ❌ | ✅ | ✅ | ✅ | ❌ |
| Informes | ❌ | ✅ | ✅ | ❌ | ❌ |
| **Área Técnica** | ❌ | ✅ | ✅ | ❌ | **✅** |

---

## 🚀 Pasos para Activar la Implementación

### Paso 1: Ejecutar Script SQL ⚠️ **OBLIGATORIO**

```bash
# Conectar a la base de datos y ejecutar
mysql -h 217.154.117.83 -P 3308 -u administrator -p toldos_db < BD/crear_rol_tecnico.sql
```

O desde HeidiSQL/phpMyAdmin:
1. Abrir `BD/crear_rol_tecnico.sql`
2. Copiar contenido
3. Ejecutar en la base de datos `toldos_db`
4. Verificar que se creó el rol con: `SELECT * FROM roles WHERE id_rol = 5;`

### Paso 2: Commit y Push de los Cambios

```bash
git add .
git commit -m "feat: Implementar Área Técnica y rol Técnico (ID 5)"
git push origin ubicaciones
```

### Paso 3: Crear Usuario Técnico de Prueba

Después de ejecutar el script SQL, crear un usuario de prueba:

```sql
-- Ejemplo: Crear usuario técnico
INSERT INTO usuarios (email, contrasena, nombre, est, id_rol)
VALUES ('tecnico@mdr.com', 'hash_password_aqui', 'Juan Técnico', 1, 5);
```

### Paso 4: Testing

1. **Login como Técnico:**
   - Email: `tecnico@mdr.com`
   - Verificar que solo aparece el menú "Área Técnica"

2. **Verificar Accesos:**
   - ✅ Debe ver: Área Técnica completa
   - ❌ NO debe ver: Dashboard, Usuarios, Mantenimientos, Llamadas

3. **Probar Acceso Directo por URL:**
   - Intentar acceder a `view/Dashboard/index.php` → Debe redirigir a `accesoDenegado.php`

4. **Login como Gestor/Admin:**
   - Verificar que pueden ver TANTO Área Técnica COMO los demás módulos

---

## 📊 Estadísticas de la Implementación

- **Archivos creados:** 2
- **Archivos modificados:** 13 (incluye el movimiento de mainSidebar.php)
- **Vistas actualizadas:** 9
- **Roles agregados:** 1 (Técnico - ID 5)
- **Nuevos permisos:** 5 módulos
- **Líneas de código:** ~200

---

## 🔍 Archivos Modificados

### Creados
1. ✨ `BD/crear_rol_tecnico.sql`
2. ✨ `BD/IMPLEMENTACION_AREA_TECNICA.md` (este archivo)

### Modificados
1. 📝 `config/template/mainSidebar.php` ⚠️ **(Movido desde docs/)**
2. 📝 `config/template/verificarPermiso.php`
3. 📝 `control-accesos-roles.md`
4. 📝 `view/MntElementos_consulta/index.php`
5. 📝 `view/MntEstados_elemento/index.php`
6. 📝 `view/MntDocumento_elemento/index.php`
7. 📝 `view/MntFoto_elemento/index.php`
8. 📝 `view/Consulta_Garantias/index.php`
9. 📝 `view/Consulta_Mantenimientos/index.php`
10. 📝 `view/Documento/index_tecnico.php`
11. 📝 `view/Informe_vigencia/index.php`
12. 📝 `view/Informe_mantenimiento/index.php`
13. 📝 `directorio.html` (agregada documentación impresion_pdf.md)

---

## ⚠️ Consideraciones Importantes

### 1. Ubicación del mainSidebar.php
- **Ubicación correcta:** `config/template/mainSidebar.php`
- El archivo fue movido desde `docs/mainSidebar.php` durante la implementación
- Las vistas lo referencian correctamente con `../../config/template/mainSidebar.php`

### 2. Base de Datos
- El script SQL es **IDEMPOTENTE** (puede ejecutarse múltiples veces sin errores)
- Verifica antes de insertar si ya existe el rol
- **OBLIGATORIO** ejecutar el script antes de hacer login con usuarios técnicos

### 3. Sincronización de Permisos
- Los permisos están en DOS archivos: `mainSidebar.php` y `verificarPermiso.php`
- Ambos **DEBEN** mantenerse sincronizados
- Cualquier cambio futuro debe replicarse en ambos lugares

### 4. Testing Obligatorio
- Probar con cada rol (Empleado, Gestor, Admin, Comercial, Técnico)
- Verificar acceso por menú Y por URL directa
- Confirmar que las redirecciones funcionan correctamente

### 5. Migración de Usuarios Existentes
- Los usuarios existentes NO se ven afectados
- Si hay técnicos con rol "Empleado", deben actualizarse manualmente:

```sql
UPDATE usuarios 
SET id_rol = 5 
WHERE id_usuario IN (1, 2, 3); -- IDs de los técnicos
```

---

## 🎯 Próximos Pasos Sugeridos

1. **Crear usuarios técnicos** en el sistema
2. **Configurar permisos adicionales** si es necesario para submódulos
3. **Revisar vistas de solo lectura** (MntElementos_consulta) para confirmar que no tienen botones de edición
4. **Documentar procedimientos** específicos para técnicos
5. **Capacitar a usuarios técnicos** en el uso de su nueva sección

---

## 📞 Soporte

**Proyecto:** MDR ERP Manager  
**Fecha implementación:** 20 de diciembre de 2025  
**Branch:** `interface`  
**Versión:** 1.0  
**Estado:** ✅ Implementación completa - Pendiente ejecución SQL

---

## ✅ Checklist de Activación

- [x] Mover mainSidebar.php a config/template/
- [ ] Ejecutar script SQL `crear_rol_tecnico.sql`
- [ ] Verificar que el rol Técnico existe en BD
- [ ] Crear al menos un usuario técnico de prueba
- [ ] Hacer login y verificar menú Área Técnica
- [ ] Probar todas las pantallas del área técnica (6 vistas)
- [ ] Verificar redirecciones de acceso denegado
- [ ] Probar con roles Gestor y Admin
- [ ] Hacer commit y push de los cambios
- [ ] Actualizar usuarios técnicos existentes (si aplica)
- [ ] Documentar el cambio en bitácora del proyecto

---

**¡Implementación exitosa! El sistema está listo para gestionar técnicos con su propia área de trabajo.**
