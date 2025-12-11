# Guía de Componentes Reutilizables - Sistema MDR

Esta guía explica cómo usar los componentes de UI mejorados para mantener consistencia visual en todas las pantallas del sistema.

## 📁 Ubicación de Componentes

Todos los componentes están en: `config/template/`

- `pageHeader.php` - Header de página con breadcrumb y botones
- `filterAlert.php` - Alerta de filtros activos
- `filterAccordionStart.php` - Inicio de acordeón de filtros
- `filterAccordionEnd.php` - Cierre de acordeón de filtros
- `filterCard.php` - Tarjeta individual de filtro

---

## 🎨 Componente 1: Page Header

### Descripción
Header completo con breadcrumb mejorado, título con icono, subtítulo, botón de ayuda y botones de acción.

### Variables Requeridas

```php
// Configuración de la página
$pageIcon = 'fa-ruler-combined';           // Icono Font Awesome
$pageTitle = 'Unidades de Medida';         // Título principal
$pageSubtitle = 'Gestión y configuración de unidades de medida del sistema';

// Breadcrumbs
$breadcrumbs = [
    ['url' => '../Dashboard/index.php', 'icon' => 'fa-home', 'text' => 'Dashboard'],
    ['url' => '#', 'icon' => 'fa-cog', 'text' => 'Mantenimientos'],
    ['text' => 'Unidades de Medida']  // El último no lleva URL (se muestra como activo)
];

// Botones de acción (opcional)
$actionButtons = [
    [
        'url' => 'formularioUnidad.php?modo=nuevo',
        'text' => 'Nueva Unidad',
        'icon' => 'fa-plus-circle',
        'class' => 'btn-primary'  // btn-primary, btn-success, btn-info, etc.
    ]
];

// Para botones con eventos JavaScript (modales, funciones, etc.)
$actionButtons = [
    [
        'id' => 'btnnuevo',          // ID del botón
        'text' => 'Nueva Familia',
        'icon' => 'fa-plus-circle',
        'class' => 'btn-primary'
    ]
];

// O con onclick directo
$actionButtons = [
    [
        'onclick' => 'abrirModal()',
        'text' => 'Nueva Familia',
        'icon' => 'fa-plus-circle',
        'class' => 'btn-primary'
    ]
];

// Modal de ayuda (opcional)
$helpModal = [
    'target' => '#modalAyudaUnidades',
    'title' => 'Ayuda sobre este módulo'
];
```

### Uso

```php
<?php
// Definir variables ANTES de incluir el componente
$pageIcon = 'fa-users';
$pageTitle = 'Clientes';
$pageSubtitle = 'Gestión de clientes del sistema';
$breadcrumbs = [
    ['url' => '../Dashboard/index.php', 'icon' => 'fa-home', 'text' => 'Dashboard'],
    ['text' => 'Clientes']
];
$actionButtons = [
    ['url' => 'nuevo.php', 'text' => 'Nuevo Cliente', 'icon' => 'fa-plus-circle', 'class' => 'btn-primary']
];
$helpModal = ['target' => '#modalAyuda', 'title' => 'Ayuda'];

// Incluir el componente
include_once('../../config/template/pageHeader.php');
?>
```

### Resultado Visual
- ✅ Breadcrumb con iconos y separadores automáticos
- ✅ Título grande con icono en color primario
- ✅ Subtítulo descriptivo en gris
- ✅ Botón de ayuda circular (40x40px)
- ✅ Botones de acción profesionales alineados a la derecha

---

## 🔔 Componente 2: Filter Alert

### Descripción
Alerta que aparece cuando hay filtros activos en la tabla.

### Variables Opcionales

```php
$alertId = 'filter-alert';              // ID del contenedor
$filtersTextId = 'active-filters-text'; // ID para texto de filtros
$clearButtonId = 'clear-filter';        // ID del botón limpiar
```

### Uso

```php
<?php include_once('../../config/template/filterAlert.php'); ?>
```

### JavaScript Requerido

El JavaScript de tu página debe controlar la visibilidad y actualizar el texto:

```javascript
// Mostrar alerta
$('#filter-alert').show();
$('#active-filters-text').text('Estado: Activo, Categoría: Herramientas');

// Ocultar alerta
$('#filter-alert').hide();

// Botón limpiar
$('#clear-filter').on('click', function() {
    // Lógica para limpiar filtros
    $('#filter-alert').hide();
});
```

---

## 🎛️ Componente 3: Filter Accordion

### Descripción
Acordeón colapsable para filtros avanzados con diseño profesional.

### Variables Opcionales

```php
$accordionId = 'accordion';           // ID del acordeón
$collapseId = 'collapseOne';          // ID del collapse
$accordionTitle = 'Filtros Avanzados'; // Título del acordeón
```

### Uso

```php
<?php
// Configurar acordeón (opcional)
$accordionTitle = 'Filtros de Búsqueda';

include_once('../../config/template/filterAccordionStart.php');
?>

<!-- AQUÍ VA EL CONTENIDO DE LOS FILTROS -->
<div class="row">
    <div class="col-md-6">
        <!-- Tus filtros aquí -->
    </div>
</div>

<?php include_once('../../config/template/filterAccordionEnd.php'); ?>
```

### Características
- ✅ Header con fondo gris claro
- ✅ Icono de filtro y flecha que rota
- ✅ Body con fondo gris muy claro
- ✅ Sombra sutil para profundidad

---

## 🃏 Componente 4: Filter Card

### Descripción
Tarjeta individual para agrupar controles de filtro relacionados.

### Variables Requeridas

```php
$cardTitle = 'Estado';                // Título de la tarjeta
$cardIcon = 'fa-toggle-on';          // Icono Font Awesome
$cardContent = '<div>...</div>';     // HTML del contenido
$colClass = 'col-md-6';              // Clases de columna Bootstrap
```

### Uso

```php
<?php
$cardTitle = 'Estado';
$cardIcon = 'fa-toggle-on';
$colClass = 'col-md-6';

// Capturar contenido en buffer
ob_start();
?>
<div class="status-selector">
    <input type="radio" name="status" value="all" checked>
    <label>Todos</label>
</div>
<?php
$cardContent = ob_get_clean();

include_once('../../config/template/filterCard.php');
?>
```

---

## 📋 Ejemplo Completo de Implementación

### Archivo: `view/MntProductos/index.php`

```php
<?php $moduloActual = 'mantenimientos'; ?>
<?php require_once('../../config/template/verificarPermiso.php'); ?>

<!DOCTYPE html>
<html lang="es">
<head>
    <?php include_once('../../config/template/mainHead.php') ?>
</head>
<body>
    <?php require_once('../../config/template/mainLogo.php') ?>
    
    <div class="br-sideleft sideleft-scrollbar">
        <?php require_once('../../config/template/mainSidebar.php') ?>
        <?php require_once('../../config/template/mainSidebarDown.php') ?>
    </div>

    <div class="br-header">
        <?php include_once('../../config/template/mainHeader.php') ?>
    </div>

    <div class="br-sideright">
        <?php include_once('../../config/template/mainRightPanel.php') ?>
    </div>

    <!-- MAIN PANEL -->
    <div class="br-mainpanel">
        
        <?php
        // ========== CONFIGURAR PAGE HEADER ==========
        $pageIcon = 'fa-box';
        $pageTitle = 'Productos';
        $pageSubtitle = 'Gestión de productos y artículos del sistema';
        $breadcrumbs = [
            ['url' => '../Dashboard/index.php', 'icon' => 'fa-home', 'text' => 'Dashboard'],
            ['url' => '#', 'icon' => 'fa-cog', 'text' => 'Mantenimientos'],
            ['text' => 'Productos']
        ];
        $actionButtons = [
            ['url' => 'nuevo.php', 'text' => 'Nuevo Producto', 'icon' => 'fa-plus-circle', 'class' => 'btn-primary']
        ];
        $helpModal = ['target' => '#modalAyuda', 'title' => 'Ayuda'];
        
        include_once('../../config/template/pageHeader.php');
        ?>

        <div class="br-pagebody">
            <div class="br-section-wrapper">
                
                <?php include_once('../../config/template/filterAlert.php'); ?>
                
                <?php
                $accordionTitle = 'Filtros de Productos';
                include_once('../../config/template/filterAccordionStart.php');
                ?>
                
                <div class="row">
                    <?php
                    // Tarjeta de filtro de estado
                    $cardTitle = 'Estado';
                    $cardIcon = 'fa-toggle-on';
                    $colClass = 'col-md-6';
                    ob_start();
                    ?>
                    <div class="form-group">
                        <select class="form-control" id="filterStatus">
                            <option value="">Todos</option>
                            <option value="1">Activo</option>
                            <option value="0">Inactivo</option>
                        </select>
                    </div>
                    <?php
                    $cardContent = ob_get_clean();
                    include_once('../../config/template/filterCard.php');
                    ?>
                </div>
                
                <?php include_once('../../config/template/filterAccordionEnd.php'); ?>
                
                <!-- Tabla -->
                <div class="table-wrapper">
                    <table id="tabla_productos" class="table display responsive nowrap table-hover">
                        <!-- Tu tabla aquí -->
                    </table>
                </div>
                
            </div>
        </div>

        <footer class="br-footer">
            <?php include_once('../../config/template/mainFooter.php') ?>
        </footer>
    </div>

    <?php include_once('../../config/template/mainJs.php') ?>
    <script src="productos.js"></script>
</body>
</html>
```

---

## 🎨 Clases CSS Disponibles

### Colores de texto
- `tx-primary` - Color primario
- `tx-gray-800` - Gris oscuro (títulos)
- `tx-gray-600` - Gris medio (subtítulos)
- `tx-gray-700` - Gris para texto regular

### Márgenes y padding
- `mg-r-5`, `mg-r-10` - Margen derecho
- `mg-l-5`, `mg-l-10` - Margen izquierdo
- `mg-b-20` - Margen inferior
- `pd-15`, `pd-20`, `pd-30` - Padding

### Bordes y fondos
- `bd-0` - Sin borde
- `bd-b` - Borde inferior
- `bd-gray-300` - Borde gris claro
- `bg-gray-50` - Fondo gris muy claro
- `bg-gray-100` - Fondo gris claro
- `bg-white` - Fondo blanco

### Botones
- `btn-primary` - Botón primario azul
- `btn-success` - Botón verde
- `btn-info` - Botón celeste
- `btn-outline-info` - Botón celeste con borde
- `btn-oblong` - Botón ovalado
- `btn-icon` - Botón circular para iconos
- `tx-11 tx-uppercase tx-mont tx-medium` - Estilo profesional para botones

### Sombras
- `shadow-base` - Sombra estándar
- `shadow-sm` - Sombra pequeña

---

## 🚀 Pasos para Migrar una Pantalla Existente

1. **Identificar las secciones:**
   - Header con título
   - Breadcrumb
   - Botones de acción
   - Filtros
   - Tabla

2. **Configurar variables antes del header:**
   ```php
   $pageIcon = 'fa-tu-icono';
   $pageTitle = 'Tu Título';
   // ... resto de variables
   ```

3. **Reemplazar el header antiguo:**
   ```php
   // ANTES
   <div class="br-pagetitle">
       <h4>Mi Título</h4>
   </div>
   
   // DESPUÉS
   <?php include_once('../../config/template/pageHeader.php'); ?>
   ```

4. **Migrar la alerta de filtros:**
   ```php
   <?php include_once('../../config/template/filterAlert.php'); ?>
   ```

5. **Envolver los filtros con el acordeón:**
   ```php
   <?php include_once('../../config/template/filterAccordionStart.php'); ?>
   <!-- Tus filtros -->
   <?php include_once('../../config/template/filterAccordionEnd.php'); ?>
   ```

6. **Añadir clase hover a la tabla:**
   ```html
   <table class="table display responsive nowrap table-hover">
   ```

7. **Probar y ajustar:** Verificar que todo funcione correctamente.

---

## ✅ Checklist de Migración

- [ ] Variables de pageHeader configuradas
- [ ] include_once de pageHeader agregado
- [ ] Alerta de filtros incluida
- [ ] Acordeón de filtros implementado
- [ ] Clase `table-hover` añadida a la tabla
- [ ] JavaScript actualizado para IDs correctos
- [ ] Modal de ayuda funcionando
- [ ] Botones de acción operativos
- [ ] Diseño responsive verificado
- [ ] Probado en diferentes navegadores

---

## 🆘 Troubleshooting

### El botón de ayuda no abre el modal
- Verificar que `data-bs-toggle` sea correcto (puede ser `data-toggle` en versiones antiguas de Bootstrap)
- Confirmar que el ID del modal coincida con el `target`

### Los estilos no se aplican
- Verificar que `mainHead.php` cargue todos los CSS necesarios
- Comprobar orden de carga de archivos CSS

### El acordeón no se colapsa
- Verificar que jQuery esté cargado
- Confirmar que Bootstrap JS esté incluido
- Revisar que los IDs coincidan

---

## 📞 Soporte

Para dudas o problemas con los componentes, consultar:
- Código fuente: `config/template/*.php`
- Ejemplo completo: `view/MntUnidad/index.php`
- Documentación Bootstrap: https://getbootstrap.com/

---

**Última actualización:** Diciembre 2025
**Versión:** 1.0
