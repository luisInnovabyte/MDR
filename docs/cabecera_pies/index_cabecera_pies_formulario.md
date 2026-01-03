# Formulario y Ayuda
## Sistema Cabecera-Pies - Formulario Independiente

> **Archivos:** `formularioArticulo.php` + `ayudaArticulos.php`  
> **Propósito:** Formulario completo de creación/edición con ayuda contextual

[← Volver al índice](./index_cabecera_pies.md)

---

## 📋 Tabla de Contenidos

1. [Estructura del Formulario](#estructura-del-formulario)
2. [Parámetros GET](#parámetros-get)
3. [Secciones del Formulario](#secciones-del-formulario)
4. [Campos del Formulario](#campos-del-formulario)
5. [Validaciones Client-Side](#validaciones-client-side)
6. [Gestión de Imágenes](#gestión-de-imágenes)
7. [Modal de Ayuda](#modal-de-ayuda)
8. [JavaScript del Formulario](#javascript-del-formulario)
9. [Integración con Controller](#integración-con-controller)

---

## 1. Estructura del Formulario

### Esquema General

```
┌─────────────────────────────────────────────────┐
│ HEADER (template)                                │
├─────────────────────────────────────────────────┤
│ BREADCRUMB                                       │
│ ┌───────────────┐                                │
│ │ Inicio > ...  │                                │
│ └───────────────┘                                │
├─────────────────────────────────────────────────┤
│ TÍTULO + BOTÓN AYUDA                             │
│ ┌─────────────────────────────────────────────┐ │
│ │ 📝 Nuevo Artículo    [?] Ayuda   [←] Volver│ │
│ └─────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────┤
│ FORMULARIO                                       │
│ ┌─────────────────────────────────────────────┐ │
│ │ 📄 Información Básica                       │ │
│ │ [Código] [Nombre ES] [Nombre EN]            │ │
│ │ [Familia] [Unidad] [Precio] [Imagen]        │ │
│ ├─────────────────────────────────────────────┤ │
│ │ ⚙️ Configuración Avanzada                   │ │
│ │ [Coeficientes] [Es Kit] [Control Total]     │ │
│ │ [No Facturar] [Orden Observaciones]         │ │
│ ├─────────────────────────────────────────────┤ │
│ │ 📝 Observaciones y Notas                    │ │
│ │ [Notas ES] [Notas EN]                       │ │
│ │ [Observaciones]                              │ │
│ └─────────────────────────────────────────────┘ │
├─────────────────────────────────────────────────┤
│ BOTONES ACCIÓN                                   │
│ [Cancelar]              [💾 Guardar Artículo]   │
├─────────────────────────────────────────────────┤
│ FOOTER (template)                                │
└─────────────────────────────────────────────────┘
```

---

## 2. Parámetros GET

### Estructura de URLs

```php
// MODO NUEVO
formularioArticulo.php?modo=nuevo

// MODO EDITAR
formularioArticulo.php?modo=editar&id=42
```

### Validación en PHP

```php
<?php
// 1. Validar parámetro modo
$modo = isset($_GET['modo']) ? $_GET['modo'] : '';

if (!in_array($modo, ['nuevo', 'editar'])) {
    header("Location: index.php");
    exit();
}

// 2. Validar ID en modo edición
$id_articulo = null;
if ($modo === 'editar') {
    if (!isset($_GET['id']) || empty($_GET['id'])) {
        header("Location: index.php");
        exit();
    }
    $id_articulo = intval($_GET['id']);
}

// 3. Definir título dinámico
$titulo_pagina = ($modo === 'nuevo') ? 'Nuevo Artículo' : 'Editar Artículo';
$icono_titulo = ($modo === 'nuevo') ? 'fa-plus-circle' : 'fa-edit';
?>
```

### Uso en JavaScript

```javascript
// Obtener parámetros de URL
const urlParams = new URLSearchParams(window.location.search);
const modo = urlParams.get('modo');
const idArticulo = urlParams.get('id');

// Determinar acción
if (modo === 'editar' && idArticulo) {
    cargarDatosArticulo(idArticulo);
} else if (modo === 'nuevo') {
    prepararFormularioNuevo();
}
```

---

## 3. Secciones del Formulario

### Sección 1: Información Básica

```html
<div class="card mb-4 border-primary">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0 tx-bold">
            <i class="fas fa-info-circle me-2"></i>Información Básica del Artículo
        </h5>
    </div>
    <div class="card-body">
        <!-- Campos: Código, Nombre ES, Nombre EN, Familia, Unidad, Precio, Imagen -->
    </div>
</div>
```

**Campos incluidos:**
- ✅ Código del artículo (único, obligatorio)
- ✅ Estado (activo/inactivo, checkbox)
- ✅ Precio de alquiler (decimal)
- ✅ Nombre en español (obligatorio)
- ✅ Nombre en inglés (obligatorio)
- ✅ Familia (select, obligatorio)
- ✅ Unidad de medida (select, opcional)
- ✅ Imagen del artículo (file upload, opcional)
- ✅ Vista previa de imagen

### Sección 2: Configuración Avanzada

```html
<div class="card mb-4 border-info">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0 tx-bold">
            <i class="fas fa-cog me-2"></i>Configuración Avanzada
        </h5>
    </div>
    <div class="card-body">
        <!-- Campos: Coeficientes, Es Kit, Control Total, No Facturar, Orden -->
    </div>
</div>
```

**Campos incluidos:**
- ✅ Coeficientes (radio buttons: heredar, sí, no)
- ✅ Es Kit (checkbox)
- ✅ Control Total (checkbox)
- ✅ No Facturar (checkbox)
- ✅ Orden de observaciones (número)

### Sección 3: Observaciones y Notas

```html
<div class="card mb-4 border-secondary">
    <div class="card-header bg-secondary text-white">
        <h5 class="mb-0 tx-bold">
            <i class="fas fa-file-alt me-2"></i>Observaciones y Notas
        </h5>
    </div>
    <div class="card-body">
        <!-- Campos: Notas ES, Notas EN, Observaciones -->
    </div>
</div>
```

**Campos incluidos:**
- ✅ Notas para presupuesto (español, textarea)
- ✅ Notas para presupuesto (inglés, textarea)
- ✅ Observaciones internas (textarea)

---

## 4. Campos del Formulario

### 4.1 Campo Código

```html
<div class="col-12 col-md-4">
    <label for="codigo_articulo" class="form-label">
        Código artículo: <span class="tx-danger">*</span>
    </label>
    <input 
        type="text" 
        class="form-control" 
        name="codigo_articulo" 
        id="codigo_articulo" 
        maxlength="50" 
        placeholder="Ej: ART001, MIC-SM58, etc..." 
        required>
    <div class="invalid-feedback small-invalid-feedback">
        Ingrese un código válido (máximo 50 caracteres, único)
    </div>
    <small class="form-text text-muted">
        Código único identificativo (máximo 50 caracteres)
    </small>
</div>
```

**Características:**
- Campo obligatorio (`required`)
- Máximo 50 caracteres (`maxlength="50"`)
- Debe ser único en la base de datos
- Validación en blur para verificar existencia

### 4.2 Campo Familia (Select)

```html
<div class="col-12 col-md-6">
    <label for="id_familia" class="form-label">
        Familia: <span class="tx-danger">*</span>
    </label>
    <select class="form-control" name="id_familia" id="id_familia" required>
        <option value="">Seleccionar familia...</option>
        <!-- Las opciones se cargarán dinámicamente vía AJAX -->
    </select>
    <div class="invalid-feedback small-invalid-feedback">
        Seleccione una familia válida
    </div>
    <small class="form-text text-muted">
        Familia a la que pertenece el artículo
        <div id="familia-descripcion" class="mt-1 p-2 bg-light border rounded" style="display: none;">
            <strong>Descripción:</strong> <span id="familia-descr-text"></span>
        </div>
    </small>
</div>
```

**Carga dinámica:**
```javascript
function cargarFamilias() {
    $.ajax({
        url: '../../controller/familia.php?op=listarDisponibles',
        type: 'GET',
        success: function(response) {
            if (response.success && response.data) {
                let options = '<option value="">Seleccionar familia...</option>';
                response.data.forEach(familia => {
                    options += `<option value="${familia.id_familia}" 
                                    data-descripcion="${familia.descripcion_familia || ''}"
                                    data-codigo="${familia.codigo_familia}"
                                    data-coeficiente="${familia.coeficiente_familia}">
                                    ${familia.codigo_familia} - ${familia.nombre_familia}
                                </option>`;
                });
                $('#id_familia').html(options);
            }
        }
    });
}
```

### 4.3 Campo Precio

```html
<div class="col-12 col-md-4">
    <label for="precio_alquiler_articulo" class="form-label">
        Precio de alquiler (€):
    </label>
    <input 
        type="number" 
        class="form-control" 
        name="precio_alquiler_articulo" 
        id="precio_alquiler_articulo" 
        step="0.01" 
        min="0" 
        placeholder="0.00">
    <small class="form-text text-muted">
        Precio base de alquiler del artículo
    </small>
</div>
```

**Características:**
- Tipo `number` con `step="0.01"` (2 decimales)
- Mínimo 0 (`min="0"`)
- Opcional (puede ser 0.00)

### 4.4 Campo Coeficientes (Radio Buttons)

```html
<div class="col-12 col-md-4">
    <label class="form-label">Coeficientes de descuento:</label>
    <div class="mt-2">
        <div class="form-check">
            <input class="form-check-input" type="radio" 
                   name="coeficiente_articulo" 
                   id="coeficiente_heredado" 
                   value="" 
                   checked>
            <label class="form-check-label" for="coeficiente_heredado">
                Heredar de familia
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" 
                   name="coeficiente_articulo" 
                   id="coeficiente_si" 
                   value="1">
            <label class="form-check-label" for="coeficiente_si">
                Sí, permitir coeficientes
            </label>
        </div>
        <div class="form-check">
            <input class="form-check-input" type="radio" 
                   name="coeficiente_articulo" 
                   id="coeficiente_no" 
                   value="0">
            <label class="form-check-label" for="coeficiente_no">
                No, sin coeficientes
            </label>
        </div>
    </div>
    <small class="form-text text-muted">
        Si permite aplicar coeficientes de descuento
        <i class="fas fa-question-circle text-info ms-1" 
           data-bs-toggle="tooltip" 
           title="Controla si este artículo puede tener coeficientes..."></i>
    </small>
</div>
```

**Valores:**
- `""` (vacío): Hereda de familia (NULL en BD)
- `"1"`: Permite coeficientes (TRUE en BD)
- `"0"`: No permite coeficientes (FALSE en BD)

### 4.5 Campo Imagen con Vista Previa

```html
<div class="col-12 col-md-6">
    <label for="imagen_articulo" class="form-label">Imagen del artículo:</label>
    <input type="file" 
           class="form-control" 
           name="imagen_articulo" 
           id="imagen_articulo" 
           accept="image/*">
    <input type="hidden" name="imagen_actual" id="imagen_actual">
    <div class="invalid-feedback small-invalid-feedback">
        Seleccione una imagen válida (JPG, PNG, GIF)
    </div>
    <small class="form-text text-muted">
        Imagen opcional (máximo 2MB, formatos: JPG, PNG, GIF)
    </small>
    <!-- Advertencia -->
    <div class="alert alert-warning mt-2 py-2" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <small><strong>Advertencia:</strong> La imagen tardará unos segundos en procesarse, no salga de la pantalla.</small>
    </div>
</div>

<div class="col-12 col-md-6">
    <label class="form-label">Vista previa:</label>
    <div class="image-preview-container" 
         style="border: 2px dashed #ddd; border-radius: 8px; padding: 10px; 
                text-align: center; min-height: 120px; display: flex; 
                align-items: center; justify-content: center;">
        <div id="image-preview" style="max-width: 100%; max-height: 100px;">
            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        </div>
    </div>
</div>
```

**JavaScript para vista previa:**
```javascript
$('#imagen_articulo').on('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        // Validar tipo
        const tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!tiposPermitidos.includes(file.type)) {
            Swal.fire('Error', 'Solo se permiten imágenes JPG, PNG o GIF', 'error');
            $(this).val('');
            return;
        }
        
        // Validar tamaño (2MB = 2097152 bytes)
        if (file.size > 2097152) {
            Swal.fire('Error', 'La imagen no debe superar 2MB', 'error');
            $(this).val('');
            return;
        }
        
        // Mostrar vista previa
        const reader = new FileReader();
        reader.onload = function(e) {
            $('#image-preview').html(
                `<img src="${e.target.result}" 
                      alt="Vista previa" 
                      style="max-width: 100%; max-height: 100px; object-fit: contain;">`
            );
        };
        reader.readAsDataURL(file);
    } else {
        // Restaurar estado sin imagen
        $('#image-preview').html(`
            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        `);
    }
});
```

---

## 5. Validaciones Client-Side

### Bootstrap Validation

```javascript
// Inicializar validación de Bootstrap 5
(function() {
    'use strict';
    
    const forms = document.querySelectorAll('.needs-validation');
    
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    });
})();
```

### Validación de Código Único

```javascript
$('#codigo_articulo').on('blur', function() {
    const codigo = $(this).val().trim();
    const idArticulo = $('#id_articulo').val(); // Para modo edición
    
    if (codigo.length > 0) {
        $.ajax({
            url: '../../controller/articulo.php?op=verificarCodigo',
            type: 'POST',
            data: { 
                codigo_articulo: codigo,
                id_articulo: idArticulo || null
            },
            success: function(response) {
                if (response.existe) {
                    $('#codigo_articulo')
                        .addClass('is-invalid')
                        .siblings('.invalid-feedback')
                        .text('Este código ya existe');
                } else {
                    $('#codigo_articulo').removeClass('is-invalid');
                }
            }
        });
    }
});
```

### Validación de Nombres (Mínimo 3 caracteres)

```javascript
$('#nombre_articulo, #name_articulo').on('blur', function() {
    const valor = $(this).val().trim();
    
    if (valor.length > 0 && valor.length < 3) {
        $(this)
            .addClass('is-invalid')
            .siblings('.invalid-feedback')
            .text('El nombre debe tener al menos 3 caracteres');
    } else {
        $(this).removeClass('is-invalid');
    }
});
```

---

## 6. Gestión de Imágenes

### Upload con FormData

```javascript
function guardarArticulo() {
    // Validar formulario
    if (!$('#formArticulo')[0].checkValidity()) {
        $('#formArticulo').addClass('was-validated');
        Toastr.warning('Complete los campos obligatorios');
        return;
    }
    
    // Crear FormData (necesario para archivos)
    const formData = new FormData($('#formArticulo')[0]);
    
    // Mostrar loading
    Swal.fire({
        title: 'Procesando...',
        text: 'Guardando artículo, por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../../controller/articulo.php?op=guardaryeditar',
        type: 'POST',
        data: formData,
        processData: false,  // ¡IMPORTANTE!
        contentType: false,  // ¡IMPORTANTE!
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    timer: 1500
                }).then(() => {
                    window.location.href = 'index.php';
                });
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
        }
    });
}
```

### Borrar Imagen Existente

```javascript
function borrarImagen() {
    Swal.fire({
        title: '¿Eliminar imagen?',
        text: "Esta acción no se puede deshacer",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            // Limpiar campos
            $('#imagen_articulo').val('');
            $('#imagen_actual').val('');
            
            // Restaurar vista previa
            $('#image-preview').html(`
                <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2 mb-0">Sin imagen</p>
            `);
            
            Toastr.success('Imagen marcada para eliminación');
        }
    });
}
```

---

## 7. Modal de Ayuda

### Estructura HTML

```html
<!-- Modal de Ayuda -->
<div class="modal fade" id="modalAyuda" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="bi bi-question-circle-fill me-2"></i>
                    Ayuda - Gestión de Artículos
                </h5>
                <button type="button" class="btn-close btn-close-white" 
                        data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Contenido: incluir ayudaArticulos.php -->
                <?php include 'ayudaArticulos.php'; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" 
                        data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
```

### Contenido del Modal (ayudaArticulos.php)

```html
<!-- Accordion de ayuda -->
<div class="accordion" id="accordionCampos">
    
    <!-- Campo Código -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingCodigo">
            <button class="accordion-button" type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseCodigo">
                <i class="bi bi-upc-scan me-2"></i>
                Código del Artículo
            </button>
        </h2>
        <div id="collapseCodigo" class="accordion-collapse collapse show">
            <div class="accordion-body">
                <strong>Campo obligatorio.</strong> Código único identificativo.
                <br><strong>Formato:</strong> Alfanumérico, máximo 50 caracteres
                <br><strong>Ejemplos:</strong> ART001, MIC-SM58, LUZ-PAR64
                <br><strong>Restricción:</strong> No puede repetirse
            </div>
        </div>
    </div>
    
    <!-- Campo Familia -->
    <div class="accordion-item">
        <h2 class="accordion-header" id="headingFamilia">
            <button class="accordion-button collapsed" type="button" 
                    data-bs-toggle="collapse" 
                    data-bs-target="#collapseFamilia">
                <i class="bi bi-diagram-3 me-2"></i>
                Familia
            </button>
        </h2>
        <div id="collapseFamilia" class="accordion-collapse collapse">
            <div class="accordion-body">
                <strong>Campo obligatorio.</strong> Familia a la que pertenece.
                <br><strong>Jerarquía:</strong> GRUPO → FAMILIA → ARTÍCULO → ELEMENTO
                <br><strong>Herencia:</strong> El artículo hereda:
                <ul class="mt-2 mb-0">
                    <li>Unidad de medida (si no tiene propia)</li>
                    <li>Configuración de coeficientes (si no tiene propia)</li>
                    <li>Imagen (si no tiene propia)</li>
                </ul>
            </div>
        </div>
    </div>
    
    <!-- Más campos... -->
    
</div>

<!-- Sección de Jerarquía del Sistema -->
<div class="mt-4 p-3 bg-light border rounded">
    <h5 class="text-primary mb-3">
        <i class="bi bi-diagram-3-fill me-2"></i>
        Jerarquía del Sistema
    </h5>
    
    <div class="hierarchy-diagram">
        <div class="level">
            <div class="badge bg-danger">GRUPO</div>
            <small>Categoría más amplia</small>
        </div>
        <div class="arrow">↓</div>
        <div class="level">
            <div class="badge bg-warning text-dark">FAMILIA</div>
            <small>Subcategoría del grupo</small>
        </div>
        <div class="arrow">↓</div>
        <div class="level">
            <div class="badge bg-info">ARTÍCULO</div>
            <small>Producto específico (este formulario)</small>
        </div>
        <div class="arrow">↓</div>
        <div class="level">
            <div class="badge bg-success">ELEMENTO</div>
            <small>Unidad física individual</small>
        </div>
    </div>
    
    <div class="mt-3">
        <strong>Ejemplo práctico:</strong>
        <ol class="mt-2 mb-0">
            <li><strong>GRUPO:</strong> Sonido</li>
            <li><strong>FAMILIA:</strong> Microfonía</li>
            <li><strong>ARTÍCULO:</strong> Micrófono inalámbrico Shure SM58</li>
            <li><strong>ELEMENTOS:</strong> 
                MIC-SM58-001, MIC-SM58-002, MIC-SM58-003...</li>
        </ol>
    </div>
</div>
```

### Botón para Abrir Ayuda

```html
<button type="button" class="btn btn-outline-info" 
        data-bs-toggle="modal" 
        data-bs-target="#modalAyuda">
    <i class="fas fa-question-circle"></i> Ayuda
</button>
```

---

## 8. JavaScript del Formulario

### Inicialización

```javascript
$(document).ready(function() {
    // Cargar datos para selects
    cargarFamilias();
    cargarUnidades();
    
    // Verificar modo
    const urlParams = new URLSearchParams(window.location.search);
    const modo = urlParams.get('modo');
    const idArticulo = urlParams.get('id');
    
    if (modo === 'editar' && idArticulo) {
        cargarDatosArticulo(idArticulo);
    }
    
    // Inicializar tooltips
    const tooltipTriggerList = [].slice.call(
        document.querySelectorAll('[data-bs-toggle="tooltip"]')
    );
    tooltipTriggerList.map(function(tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Event handlers
    $('#formArticulo').on('submit', function(e) {
        e.preventDefault();
        guardarArticulo();
    });
});
```

### Cargar Datos en Modo Edición

```javascript
function cargarDatosArticulo(id) {
    Swal.fire({
        title: 'Cargando...',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../../controller/articulo.php?op=mostrar',
        type: 'POST',
        data: { id_articulo: id },
        success: function(data) {
            Swal.close();
            
            if (data) {
                // Campos de texto
                $('#id_articulo').val(data.id_articulo);
                $('#codigo_articulo').val(data.codigo_articulo);
                $('#nombre_articulo').val(data.nombre_articulo);
                $('#name_articulo').val(data.name_articulo);
                $('#precio_alquiler_articulo').val(data.precio_alquiler_articulo);
                
                // Selects
                $('#id_familia').val(data.id_familia);
                $('#id_unidad').val(data.id_unidad || '');
                
                // Coeficientes (radio buttons)
                if (data.coeficiente_articulo === null) {
                    $('#coeficiente_heredado').prop('checked', true);
                } else if (data.coeficiente_articulo == 1) {
                    $('#coeficiente_si').prop('checked', true);
                } else {
                    $('#coeficiente_no').prop('checked', true);
                }
                
                // Checkboxes
                $('#es_kit_articulo').prop('checked', data.es_kit_articulo == 1);
                $('#control_total_articulo').prop('checked', data.control_total_articulo == 1);
                $('#no_facturar_articulo').prop('checked', data.no_facturar_articulo == 1);
                $('#activo_articulo').prop('checked', data.activo_articulo == 1);
                
                // Textareas
                $('#notas_presupuesto_articulo').val(data.notas_presupuesto_articulo || '');
                $('#notes_budget_articulo').val(data.notes_budget_articulo || '');
                $('#observaciones_articulo').val(data.observaciones_articulo || '');
                $('#orden_obs_articulo').val(data.orden_obs_articulo || 200);
                
                // Imagen
                if (data.imagen_articulo) {
                    $('#imagen_actual').val(data.imagen_articulo);
                    $('#image-preview').html(
                        `<img src="../../public/img/articulo/${data.imagen_articulo}" 
                              alt="${data.nombre_articulo}" 
                              style="max-width: 100%; max-height: 100px; object-fit: contain;">`
                    );
                }
            } else {
                Swal.fire('Error', 'No se pudo cargar el artículo', 'error')
                    .then(() => {
                        window.location.href = 'index.php';
                    });
            }
        },
        error: function() {
            Swal.close();
            Swal.fire('Error', 'Error al cargar datos', 'error')
                .then(() => {
                    window.location.href = 'index.php';
                });
        }
    });
}
```

---

## 9. Integración con Controller

### Submit del Formulario

```javascript
function guardarArticulo() {
    // Validar
    if (!$('#formArticulo')[0].checkValidity()) {
        $('#formArticulo').addClass('was-validated');
        Toastr.warning('Complete todos los campos obligatorios');
        return;
    }
    
    // FormData para archivos
    const formData = new FormData($('#formArticulo')[0]);
    
    // Loading
    Swal.fire({
        title: 'Guardando...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    // AJAX
    $.ajax({
        url: '../../controller/articulo.php?op=guardaryeditar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        success: function(response) {
            Swal.close();
            
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: response.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'index.php';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: response.message
                });
            }
        },
        error: function(xhr, status, error) {
            Swal.close();
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error de comunicación: ' + error
            });
        }
    });
}
```

---

## ✅ Checklist de Formulario

### HTML
- [ ] Parámetros GET validados
- [ ] Campos obligatorios con asterisco (*)
- [ ] Placeholders descriptivos
- [ ] Textos de ayuda (small.form-text)
- [ ] Mensajes de validación (invalid-feedback)
- [ ] Estructura responsive (col-12 col-md-X)
- [ ] Botones de acción visibles

### JavaScript
- [ ] Cargar selects dinámicamente
- [ ] Validar formulario antes de submit
- [ ] Vista previa de imágenes
- [ ] FormData para archivos
- [ ] processData: false, contentType: false
- [ ] Loading durante guardado
- [ ] Manejo de errores
- [ ] Redirección después de éxito

### Ayuda
- [ ] Modal con accordion
- [ ] Explicación de cada campo
- [ ] Ejemplos prácticos
- [ ] Diagrama de jerarquía
- [ ] Botón visible y accesible

---

[← Anterior: Controller](./index_cabecera_pies_controller.md) | [Siguiente: Replicación →](./index_cabecera_pies_replicacion.md)
