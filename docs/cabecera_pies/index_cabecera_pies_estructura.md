# Estructura del Index.php
## Sistema Cabecera-Pies - Análisis Detallado

> **Archivo:** `view/MntArticulos/index.php`  
> **Propósito:** Listado principal con DataTables y sistema de cabecera-pies

[← Volver al índice](./index_cabecera_pies.md)

---

## 📋 Tabla de Contenidos

1. [Estructura General](#estructura-general)
2. [Verificación de Permisos](#verificación-de-permisos)
3. [Carga de Estadísticas](#carga-de-estadísticas)
4. [Estructura HTML](#estructura-html)
5. [Panel de Estadísticas](#panel-de-estadísticas)
6. [Sistema de Filtros](#sistema-de-filtros)
7. [Tabla DataTables](#tabla-datatables)
8. [Scripts y Dependencias](#scripts-y-dependencias)

---

## 1. Estructura General

### Diagrama de Bloques

```
index.php
│
├── Verificación de permisos
│   └── verificarPermiso.php
│
├── Carga de estadísticas (PHP)
│   ├── require Articulo.php
│   ├── total_articulo()
│   ├── total_articulo_activo()
│   ├── total_articulo_activo_kit()
│   └── total_articulo_activo_coeficiente()
│
├── <!DOCTYPE html>
│   └── <head>
│       └── mainHead.php (CSS, meta tags)
│
└── <body>
    ├── LEFT PANEL (mainLogo + mainSidebar)
    ├── HEAD PANEL (mainHeader)
    ├── RIGHT PANEL (mainRightPanel)
    │
    └── MAIN PANEL
        ├── br-pageheader (Breadcrumb)
        ├── br-pagetitle (Título + Ayuda)
        ├── br-pagebody
        │   ├── Panel de estadísticas (4 cards)
        │   └── br-section-wrapper
        │       ├── Alerta de filtros activos
        │       ├── Botón "Nuevo Artículo"
        │       └── Tabla DataTables
        │           ├── <thead> (Encabezados)
        │           ├── <tbody> (Datos vía AJAX)
        │           └── <tfoot> (Filtros)
        │
        ├── Modal de Ayuda (ayudaArticulos.php)
        └── Scripts
            ├── mainJs.php
            ├── tooltip-colored.js
            ├── popover-colored.js
            └── mntarticulo.js
```

---

## 2. Verificación de Permisos

### Código

```php
<?php 
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'usuarios';
require_once('../../config/template/verificarPermiso.php');
```

### Explicación

- **Propósito**: Verificar que el usuario tiene permisos para acceder al módulo
- **Variable `$moduloActual`**: Define el módulo actual (en este caso 'usuarios')
- **Archivo incluido**: `verificarPermiso.php` valida permisos y redirige si no tiene acceso

### Para Replicar

```php
<?php 
$moduloActual = 'tu_modulo'; // Cambiar por el nombre de tu módulo
require_once('../../config/template/verificarPermiso.php');
```

---

## 3. Carga de Estadísticas

### Código Completo

```php
// Inicializar variables por defecto
$totalArticulos = 0;
$totalArticulosActivos = 0;
$totalArticulosKits = 0;
$totalArticulosCoeficientes = 0;

// Cargar estadísticas de artículos
try {
    require_once('../../models/Articulo.php');
    $articuloModel = new Articulo();
    
    // Total general
    $totalArticulos = $articuloModel->total_articulo();
    if ($totalArticulos === false || $totalArticulos === null) {
        $totalArticulos = 0;
    }
    
    // Total activos
    $totalArticulosActivos = $articuloModel->total_articulo_activo();
    if ($totalArticulosActivos === false || $totalArticulosActivos === null) {
        $totalArticulosActivos = 0;
    }

    // Total kits
    $totalArticulosKits = $articuloModel->total_articulo_activo_kit();
    if ($totalArticulosKits === false || $totalArticulosKits === null) {
        $totalArticulosKits = 0;
    }

    // Total con coeficientes
    $totalArticulosCoeficientes = $articuloModel->total_articulo_activo_coeficiente();
    if ($totalArticulosCoeficientes === false || $totalArticulosCoeficientes === null) {
        $totalArticulosCoeficientes = 0;
    }

} catch (Throwable $e) {
    // Captura cualquier error (Exception o Error)
    $totalArticulos = 0;
    $totalArticulosActivos = 0;
    $totalArticulosKits = 0;
    $totalArticulosCoeficientes = 0;
    error_log("Error al cargar estadísticas: " . $e->getMessage());
}
```

### Características Importantes

1. **Inicialización defensiva**: Variables con valor 0 por defecto
2. **Try-catch robusto**: Captura `Throwable` para errores y excepciones
3. **Validación de nulls**: Verifica `false` y `null` antes de usar valores
4. **Logging de errores**: Usa `error_log()` para depuración
5. **Degradación elegante**: Si falla, muestra 0 en vez de error

### Para Replicar

```php
// Inicializar tus contadores
$totalTuEntidad = 0;
$totalTuEntidadActivos = 0;
// ... más contadores según necesites

try {
    require_once('../../models/TuEntidad.php');
    $modelo = new TuEntidad();
    
    $totalTuEntidad = $modelo->total_tuEntidad();
    if ($totalTuEntidad === false || $totalTuEntidad === null) {
        $totalTuEntidad = 0;
    }
    
    // Repetir para cada contador
    
} catch (Throwable $e) {
    // Valores por defecto en caso de error
    $totalTuEntidad = 0;
    error_log("Error al cargar estadísticas de TuEntidad: " . $e->getMessage());
}
```

---

## 4. Estructura HTML

### Head Section

```php
<!DOCTYPE html>
<html lang="es">

<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>
```

**Contenido de `mainHead.php`:**
- Meta tags (charset, viewport, etc.)
- Bootstrap 5 CSS
- DataTables CSS
- Bootstrap Icons
- SweetAlert2 CSS
- CSS personalizados del proyecto

### Body Structure

```php
<body>
    <!-- LEFT PANEL -->
    <?php require_once('../../config/template/mainLogo.php') ?>
    
    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
    </div>
    
    <!-- HEAD PANEL -->
    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div>
    
    <!-- RIGHT PANEL -->
    <div class="br-sideright">
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div>
    
    <!-- MAIN PANEL -->
    <div class="br-mainpanel">
        <!-- Contenido principal aquí -->
    </div>
</body>
```

### Componentes de Plantillas

| Archivo | Propósito |
|---------|-----------|
| `mainLogo.php` | Logo de la aplicación |
| `mainSidebar.php` | Menú lateral de navegación |
| `mainSidebarDown.php` | Parte inferior del sidebar |
| `mainHeader.php` | Header superior con usuario y notificaciones |
| `mainRightPanel.php` | Panel derecho (configuraciones, notificaciones) |
| `mainFooter.php` | Footer con copyright e info |
| `mainJs.php` | Scripts JavaScript comunes |

---

## 5. Panel de Estadísticas

### Código HTML

```php
<div class="br-pagebody">
    <!-- Panel de Estadísticas -->
    <div class="row row-sm mb-4">
        <!-- Card 1: Total Artículos -->
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card shadow-sm border-primary">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-box-seam text-primary me-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0 text-muted">Total Artículos</h6>
                    </div>
                    <h2 class="mb-0 text-primary fw-bold">
                        <?php echo $totalArticulos; ?>
                    </h2>
                </div>
            </div>
        </div>
        
        <!-- Card 2: Activos -->
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card shadow-sm border-success">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-check-circle text-success me-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0 text-muted">Activos</h6>
                    </div>
                    <h2 class="mb-0 text-success fw-bold">
                        <?php echo $totalArticulosActivos; ?>
                    </h2>
                </div>
            </div>
        </div>
        
        <!-- Card 3: Kits -->
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card shadow-sm border-info">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-box-seam-fill text-info me-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0 text-muted">Kits</h6>
                    </div>
                    <h2 class="mb-0 text-success fw-bold">
                        <?php echo $totalArticulosKits; ?>
                    </h2>
                </div>
            </div>
        </div>
        
        <!-- Card 4: Con Coeficientes -->
        <div class="col-lg-3 col-md-3 col-sm-12">
            <div class="card shadow-sm border-warning">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-2">
                        <i class="bi bi-percent text-warning me-2" style="font-size: 2rem;"></i>
                        <h6 class="mb-0 text-muted">Con Coeficientes</h6>
                    </div>
                    <h2 class="mb-0 text-success fw-bold">
                        <?php echo $totalArticulosCoeficientes; ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>
    <!-- Fin Panel de Estadísticas -->
```

### Características

1. **Grid Responsive**: `col-lg-3 col-md-3 col-sm-12`
   - Desktop: 4 columnas (25% cada una)
   - Tablet: 4 columnas
   - Móvil: 1 columna (100%)

2. **Cards con Bordes de Color**: `border-primary`, `border-success`, etc.

3. **Iconos Bootstrap Icons**: `bi bi-box-seam`, `bi bi-check-circle`, etc.

4. **Flexbox para Alineación**: `d-flex align-items-center justify-content-center`

### Para Replicar

```php
<div class="row row-sm mb-4">
    <div class="col-lg-3 col-md-3 col-sm-12">
        <div class="card shadow-sm border-primary">
            <div class="card-body text-center">
                <div class="d-flex align-items-center justify-content-center mb-2">
                    <i class="bi bi-TU-ICONO text-primary me-2" style="font-size: 2rem;"></i>
                    <h6 class="mb-0 text-muted">Tu Título</h6>
                </div>
                <h2 class="mb-0 text-primary fw-bold">
                    <?php echo $tuVariable; ?>
                </h2>
            </div>
        </div>
    </div>
    <!-- Repetir para más cards -->
</div>
```

---

## 6. Sistema de Filtros

### Código HTML

```php
<div class="br-section-wrapper">
    <!-- Fila contenedora -->
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <!-- Contenedor de alerta expandible -->
        <div class="flex-grow-1 me-3" style="min-width: 300px;">
            <!-- Alerta de filtro activo -->
            <div class="alert alert-warning alert-dismissible fade show mb-0 w-100" 
                 role="alert" 
                 id="filter-alert" 
                 style="display: none;">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="truncate">
                        <i class="fas fa-filter me-2"></i>
                        <span>Filtros aplicados: </span>
                        <span id="active-filters-text" class="text-truncate"></span>
                    </div>
                    <button type="button" 
                            class="btn btn-sm btn-outline-warning ms-2 flex-shrink-0" 
                            id="clear-filter">
                        Limpiar filtros
                    </button>
                </div>
            </div>
        </div>

        <!-- Botón Nuevo Artículo -->
        <a href="formularioArticulo.php?modo=nuevo" 
           class="btn btn-oblong btn-outline-primary flex-shrink-0 mt-2 mt-sm-0">
            <i class="fas fa-plus-circle me-2"></i>Nuevo Artículo 
        </a>
    </div>
```

### Características

1. **Flexbox Layout**: Distribuye espacio entre alerta y botón
2. **Responsive**: Se adapta en móviles con `flex-wrap`
3. **Alerta Oculta**: `display: none` por defecto, se muestra con JavaScript
4. **Botón de Limpieza**: Dentro de la misma alerta
5. **Redirección a Formulario**: Con parámetro GET `?modo=nuevo`

### Funcionamiento JavaScript

```javascript
// Actualizar mensaje de filtro activo
function updateFilterMessage() {
    var activeFilters = false;
    
    $columnFilterInputs.each(function () {
        if ($(this).val() !== "") {
            activeFilters = true;
            return false;
        }
    });
    
    if (table_e.search() !== "") {
        activeFilters = true;
    }
    
    if (activeFilters) {
        $("#filter-alert").show();
    } else {
        $("#filter-alert").hide();
    }
}

// Limpiar filtros
$("#clear-filter").on("click", function () {
    table_e.destroy();
    
    $columnFilterInputs.each(function () {
        $(this).val("");
    });
    
    table_e = $table.DataTable($tableConfig);
    $("#filter-alert").hide();
});
```

---

## 7. Tabla DataTables

### Estructura HTML

```php
<!-- Tabla de artículos -->
<div class="table-wrapper">
    <table id="articulos_data" class="table display responsive nowrap">
        <thead>
            <tr>
                <th></th>
                <th>Id artículo</th>
                <th>Código artículo</th>
                <th>Nombre artículo</th>
                <th>Familia</th>
                <th>Precio alquiler</th>
                <th>Es kit</th>
                <th>Coeficientes</th>
                <th>Estado</th>
                <th>Act./Desac.</th>
                <th>Edit.</th>
                <th>Elementos</th>
            </tr>
        </thead>
        <tbody>
            <!-- Datos se cargarán aquí -->
        </tbody>
        <tfoot>
            <tr>
                <th></th>
                <th class="d-none">
                    <input type="text" placeholder="Buscar ID" 
                           class="form-control form-control-sm" />
                </th>
                <th>
                    <input type="text" placeholder="Buscar código" 
                           class="form-control form-control-sm" />
                </th>
                <th>
                    <input type="text" placeholder="Buscar nombre artículo" 
                           class="form-control form-control-sm" />
                </th>
                <th>
                    <input type="text" placeholder="Buscar familia" 
                           class="form-control form-control-sm" />
                </th>
                <th>
                    <input type="text" placeholder="Buscar precio" 
                           class="form-control form-control-sm" />
                </th>
                <th>
                    <select class="form-control form-control-sm" 
                            title="Filtrar por kit">
                        <option value="">Todos</option>
                        <option value="1">Es kit</option>
                        <option value="0">No es kit</option>
                    </select>
                </th>
                <th>
                    <select class="form-control form-control-sm" 
                            title="Filtrar por coeficientes">
                        <option value="">Todos</option>
                        <option value="1">Permite coeficientes</option>
                        <option value="0">No permite</option>
                    </select>
                </th>
                <th>
                    <select class="form-control form-control-sm" 
                            title="Filtrar por estado">
                        <option value="">Todos los estados</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </th>
                <th class="d-none">
                    <input type="text" placeholder="NO Buscar" 
                           class="form-control form-control-sm" />
                </th>
                <th></th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div><!-- table-wrapper -->
```

### Características

1. **Clases DataTables**: `display responsive nowrap`
2. **Thead**: Encabezados de columna
3. **Tbody**: Vacío, se llena vía AJAX
4. **Tfoot**: Inputs y selects para filtrar cada columna
5. **Columnas ocultas**: `class="d-none"` para ID y columnas sin filtro

### Tipos de Filtros

| Tipo de Campo | Uso | Ejemplo |
|---------------|-----|---------|
| `<input type="text">` | Búsqueda de texto libre | Código, Nombre, Familia |
| `<select>` | Filtrado por valores específicos | Estado (Activo/Inactivo), Es Kit (Sí/No) |
| Vacío (`<th></th>`) | Sin filtro | Columna de acciones |
| `d-none` | Oculto pero funcional | ID (oculto pero filtrable) |

---

## 8. Scripts y Dependencias

### Orden de Carga

```php
<!-- Scripts de plantilla -->
<?php include_once('../../config/template/mainJs.php') ?>

<!-- Scripts de componentes -->
<script src="../../public/js/tooltip-colored.js"></script>
<script src="../../public/js/popover-colored.js"></script>

<!-- Script específico del módulo -->
<script type="text/javascript" src="mntarticulo.js"></script>

<!-- Script adicional: Colapsar sidebar -->
<script>
    $(document).ready(function() {
        $('body').addClass('collapsed-menu');
        $('.br-sideleft').addClass('collapsed');
    });
</script>
```

### Contenido de `mainJs.php`

- jQuery 3.7.1
- Bootstrap 5 JS Bundle
- DataTables JS
- SweetAlert2 JS
- Toastr JS
- jQuery UI (si aplica)
- Scripts globales del proyecto

### Orden Importantísimo

```
1. jQuery (base)
   ↓
2. Bootstrap (requiere jQuery)
   ↓
3. DataTables (requiere jQuery)
   ↓
4. SweetAlert2, Toastr, etc.
   ↓
5. Scripts específicos de componentes
   ↓
6. Script del módulo (mntarticulo.js)
   ↓
7. Scripts inline personalizados
```

---

## 🎯 Puntos Clave para Replicar

### ✅ Checklist de Estructura

- [ ] Verificación de permisos al inicio
- [ ] Carga de estadísticas con try-catch
- [ ] Inicialización de variables por defecto
- [ ] Validación de nulls y false
- [ ] Panel de estadísticas responsive
- [ ] Sistema de alerta de filtros activos
- [ ] Botón de nuevo registro con URL paramétrica
- [ ] Tabla DataTables con thead, tbody, tfoot
- [ ] Filtros en tfoot (inputs y selects)
- [ ] Inclusión de scripts en orden correcto
- [ ] Modal de ayuda incluido
- [ ] Footer con copyright

---

## 📝 Ejemplo Adaptado

```php
<?php 
// Permisos
$moduloActual = 'tu_modulo';
require_once('../../config/template/verificarPermiso.php');

// Estadísticas
$totalTuEntidad = 0;
try {
    require_once('../../models/TuEntidad.php');
    $modelo = new TuEntidad();
    $totalTuEntidad = $modelo->total_tuEntidad() ?: 0;
} catch (Throwable $e) {
    $totalTuEntidad = 0;
    error_log("Error: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>
<body>
    <!-- Plantillas -->
    <?php require_once('../../config/template/mainLogo.php') ?>
    <!-- ... otros includes ... -->
    
    <div class="br-mainpanel">
        <div class="br-pagebody">
            <!-- Panel de estadísticas -->
            <div class="row row-sm mb-4">
                <!-- Tus cards -->
            </div>
            
            <!-- Filtros y botón nuevo -->
            <div class="d-flex justify-content-between mb-3">
                <div class="flex-grow-1 me-3">
                    <div class="alert alert-warning" id="filter-alert" style="display:none;">
                        <span>Filtros aplicados</span>
                        <button id="clear-filter">Limpiar</button>
                    </div>
                </div>
                <a href="formularioTuEntidad.php?modo=nuevo" class="btn btn-primary">
                    Nuevo
                </a>
            </div>
            
            <!-- Tabla -->
            <table id="tu_data" class="table display responsive nowrap">
                <thead>...</thead>
                <tbody></tbody>
                <tfoot>...</tfoot>
            </table>
        </div>
    </div>
    
    <!-- Scripts -->
    <?php include_once('../../config/template/mainJs.php') ?>
    <script src="mnttuentidad.js"></script>
</body>
</html>
```

---

[← Volver al índice](./index_cabecera_pies.md) | [Siguiente: DataTables →](./index_cabecera_pies_datatables.md)
