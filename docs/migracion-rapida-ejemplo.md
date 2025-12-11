# Migración Rápida - Ejemplo Práctico

## Pantalla: MntFamilias/index.php

### ❌ ANTES (Código Antiguo)

```php
<div class="br-mainpanel">
    <div class="br-pageheader">
        <nav class="breadcrumb pd-0 mg-0 tx-12">
            <a class="breadcrumb-item" href="../Dashboard/index.php">Dashboard</a>
            <span class="breadcrumb-item active">Familias</span>
        </nav>
    </div>
    
    <div class="br-pagetitle">
        <h4>Mantenimiento de Familias</h4>
        <a href="nuevo.php" class="btn btn-primary">
            <i class="fa fa-plus"></i> Nueva Familia
        </a>
    </div>

    <div class="br-pagebody">
        <div class="br-section-wrapper">
            <!-- Tabla -->
            <table id="tabla_familias" class="table">
                ...
            </table>
        </div>
    </div>
</div>
```

---

### ✅ DESPUÉS (Con Componentes)

```php
<div class="br-mainpanel">
    <?php
    // ========== CONFIGURAR PAGE HEADER ==========
    $pageIcon = 'fa-layer-group';
    $pageTitle = 'Familias';
    $pageSubtitle = 'Gestión de familias de productos';
    $breadcrumbs = [
        ['url' => '../Dashboard/index.php', 'icon' => 'fa-home', 'text' => 'Dashboard'],
        ['url' => '#', 'icon' => 'fa-cog', 'text' => 'Mantenimientos'],
        ['text' => 'Familias']
    ];
    $actionButtons = [
        ['url' => 'nuevo.php', 'text' => 'Nueva Familia', 'icon' => 'fa-plus-circle', 'class' => 'btn-primary']
    ];
    $helpModal = ['target' => '#modalAyuda', 'title' => 'Ayuda'];
    
    include_once('../../config/template/pageHeader.php');
    ?>

    <div class="br-pagebody">
        <div class="br-section-wrapper">
            
            <?php include_once('../../config/template/filterAlert.php'); ?>
            
            <?php include_once('../../config/template/filterAccordionStart.php'); ?>
            
            <div class="row">
                <!-- Aquí puedes agregar filtros si necesitas -->
            </div>
            
            <?php include_once('../../config/template/filterAccordionEnd.php'); ?>
            
            <!-- Tabla -->
            <div class="table-wrapper">
                <table id="tabla_familias" class="table display responsive nowrap table-hover">
                    ...
                </table>
            </div>
        </div>
    </div>
</div>
```

---

## 🎯 Cambios Aplicados:

✅ **Breadcrumb mejorado** con iconos  
✅ **Título profesional** con icono y subtítulo  
✅ **Botón de ayuda** circular  
✅ **Botón de acción** con estilo profesional  
✅ **Alerta de filtros** lista para usar  
✅ **Acordeón de filtros** preparado  
✅ **Tabla con hover** para mejor UX  

---

## ⚡ Tiempo de migración: ~5 minutos por pantalla

1. Copiar el bloque de configuración
2. Cambiar los valores específicos (título, icono, URLs)
3. Incluir los componentes
4. Probar

---

## 📊 Pantallas Sugeridas para Migrar (en orden de prioridad):

1. ✅ **MntUnidad** - ✔️ YA MIGRADA (plantilla base)
2. 🔄 **MntFamilias** - Alta prioridad
3. 🔄 **MntProveedores** - Alta prioridad  
4. 🔄 **MntClientes** - Alta prioridad
5. 🔄 **MntProductos** - Alta prioridad
6. 🔄 **MntArticulo** - Media prioridad
7. 🔄 **MntMarca** - Media prioridad
8. 🔄 **MntCategorias** - Media prioridad

---

## 🔧 Script de Ayuda (PowerShell)

Para listar todas las pantallas que tienen `index.php`:

```powershell
Get-ChildItem -Path "W:\MDR\view\" -Recurse -Filter "index.php" | Select-Object FullName
```

Para buscar pantallas con el patrón antiguo:

```powershell
Get-ChildItem -Path "W:\MDR\view\" -Recurse -Filter "*.php" | 
    Select-String -Pattern "br-pagetitle" | 
    Select-Object -Property Path -Unique
```

---

¡Listo para comenzar las migraciones! 🚀
