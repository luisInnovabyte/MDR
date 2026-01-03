# JavaScript del Formulario - Sistema Cabecera-Pies DataTables

> Documentación del archivo `formularioArticulo.js` - Lógica JavaScript del formulario independiente  
> Proyecto: MDR ERP Manager | Versión: 1.0 | Fecha: 23 diciembre 2024

---

## 📋 ÍNDICE

1. [Propósito y Contexto](#propósito-y-contexto)
2. [Estructura General](#estructura-general)
3. [Inicialización y Validaciones](#inicialización-y-validaciones)
4. [Carga de Datos desde Backend](#carga-de-datos-desde-backend)
5. [Gestión del Formulario](#gestión-del-formulario)
6. [Sistema de Guardado](#sistema-de-guardado)
7. [Control de Cambios](#control-de-cambios)
8. [Ejemplos Prácticos](#ejemplos-prácticos)

---

## 🎯 PROPÓSITO Y CONTEXTO

### ¿Qué es este archivo?

`formularioArticulo.js` es el **archivo JavaScript separado** que contiene toda la lógica del formulario independiente (`formularioArticulo.php`).

### ¿Por qué está separado?

- **Separación de responsabilidades**: HTML/PHP en `.php`, lógica en `.js`
- **Reutilización**: El JS puede ser reutilizado en otros contextos
- **Mantenimiento**: Más fácil de mantener y depurar
- **Organización**: Sigue el patrón del proyecto (index.php + mntarticulo.js)

### ¿Cuándo se ejecuta?

Se carga cuando el usuario accede a `formularioArticulo.php`:
- **Modo nuevo**: `formularioArticulo.php?modo=nuevo`
- **Modo editar**: `formularioArticulo.php?modo=editar&id=123`

---

## 🏗️ ESTRUCTURA GENERAL

### Esquema del archivo

```javascript
$(document).ready(function () {
    
    // 1. FORMATEO Y VALIDACIONES
    var formValidator = new FormValidator(...)
    
    // 2. FUNCIONES DE INICIALIZACIÓN
    function cargarFamilias() {...}
    function cargarUnidadesMedida() {...}
    
    // 3. EVENTOS Y LISTENERS
    $('#id_familia').on('change', ...)
    $('#id_unidad').on('change', ...)
    
    // 4. DETECCIÓN DE MODO (nuevo/editar)
    const modo = getUrlParameter('modo')
    if (modo === 'editar') { cargarDatosArticulo(id) }
    
    // 5. BOTÓN GUARDAR
    $(document).on('click', '#btnSalvarArticulo', ...)
    
    // 6. FUNCIONES DE GUARDADO
    function verificarArticuloExistente(...)
    function guardarArticulo(...)
    
    // 7. CONTROL DE CAMBIOS
    function captureOriginalValues()
    function hasFormChanged()
    window.addEventListener('beforeunload', ...)
    
    // 8. INICIALIZACIÓN FINAL
    cargarFamilias()
    cargarUnidadesMedida()
    
}); // fin document.ready

// 9. FUNCIONES GLOBALES (fuera de document.ready)
function showDefaultImagePreview()
function showExistingImage(imagePath)
```

### Longitud total: **567 líneas**

---

## ✅ INICIALIZACIÓN Y VALIDACIONES

### FormValidator (Validaciones)

```javascript
var formValidator = new FormValidator('formArticulo', {
    codigo_articulo: {
        required: true
    },
    nombre_articulo: {
        required: true
    },
    name_articulo: {
        required: true
    },
    id_familia: {
        required: true
    }
});
```

**Campos obligatorios validados:**
- ✅ `codigo_articulo` - Código del artículo
- ✅ `nombre_articulo` - Nombre en español
- ✅ `name_articulo` - Nombre en inglés
- ✅ `id_familia` - Familia (select)

**Uso en guardado:**
```javascript
if (!formValidator.validateForm(event)) {
    toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
    return;
}
```

---

## 📡 CARGA DE DATOS DESDE BACKEND

### 1. Cargar Familias (Select)

```javascript
function cargarFamilias() {
    $.ajax({
        url: "../../controller/familia_unidad.php?op=listarDisponibles",
        type: "GET",
        dataType: "json",
        success: function(response) {
            try {
                console.log('Respuesta de familias:', response);
                
                // La respuesta viene como {data: [...], draw: 1, recordsTotal: N, recordsFiltered: N}
                var data = response.data || response;
                
                if (Array.isArray(data)) {
                    var select = $('#id_familia');
                    select.empty();
                    select.append('<option value="">Seleccionar familia...</option>');
                    
                    data.forEach(function(familia) {
                        var displayText = familia.codigo_familia + ' - ' + familia.nombre_familia;
                        select.append('<option value="' + familia.id_familia + 
                                      '" data-descripcion="' + (familia.descr_familia || '') + '">' + 
                                      displayText + '</option>');
                    });
                } else {
                    console.error('Error: Respuesta no válida del servidor para familias', data);
                    toastr.warning('No se pudieron cargar las familias');
                }
            } catch (e) {
                console.error('Error al procesar familias:', e);
                toastr.error('Error al cargar las familias');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar familias:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            toastr.error('Error al cargar las familias');
        }
    });
}
```

**Características importantes:**
- ✅ **Manejo de respuesta flexible**: Acepta `response.data` o `response` directo
- ✅ **Formato del texto**: `"COD - Nombre Familia"`
- ✅ **Data-attribute**: Guarda `data-descripcion` para mostrar info adicional
- ✅ **Try-catch**: Manejo robusto de errores
- ✅ **Toastr**: Notificaciones al usuario

### 2. Cargar Unidades de Medida (Select)

```javascript
function cargarUnidadesMedida() {
    $.ajax({
        url: "../../controller/unidad_medida.php?op=listarDisponibles",
        type: "GET",
        dataType: "json",
        success: function(response) {
            try {
                console.log('Respuesta de unidades de medida:', response);
                
                var data = response.data || response;
                
                if (Array.isArray(data)) {
                    var select = $('#id_unidad');
                    select.empty();
                    select.append('<option value="">Seleccionar unidad de medida...</option>');
                    
                    data.forEach(function(unidad) {
                        var displayText = unidad.nombre_unidad;
                        if (unidad.simbolo_unidad) {
                            displayText += ' (' + unidad.simbolo_unidad + ')';
                        }
                        select.append('<option value="' + unidad.id_unidad + 
                                      '" data-descripcion="' + (unidad.descr_unidad || '') + '">' + 
                                      displayText + '</option>');
                    });
                } else {
                    console.error('Error: Respuesta no válida del servidor para unidades de medida', data);
                    toastr.warning('No se pudieron cargar las unidades de medida');
                }
            } catch (e) {
                console.error('Error al procesar unidades de medida:', e);
                toastr.error('Error al cargar las unidades de medida');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar unidades de medida:', error);
            console.error('Respuesta del servidor:', xhr.responseText);
            toastr.error('Error al cargar las unidades de medida');
        }
    });
}
```

**Formato del texto**: `"Metro (m)"` o `"Unidad"` si no tiene símbolo

### 3. Listener para Mostrar Descripción

```javascript
// Manejar cambio en el select de familia
$('#id_familia').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    var descripcion = selectedOption.data('descripcion');
    
    if (descripcion && descripcion.trim() !== '') {
        $('#familia-descr-text').text(descripcion);
        $('#familia-descripcion').show();
    } else {
        $('#familia-descripcion').hide();
    }
});

// Manejar cambio en el select de unidad de medida
$('#id_unidad').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    var descripcion = selectedOption.data('descripcion');
    
    if (descripcion && descripcion.trim() !== '') {
        $('#unidad-descr-text').text(descripcion);
        $('#unidad-descripcion').show();
    } else {
        $('#unidad-descripcion').hide();
    }
});
```

**Flujo:**
1. Usuario selecciona una opción
2. Se obtiene el `data-descripcion` del option
3. Si existe, se muestra en un `<div>` debajo del select
4. Si no existe, se oculta el `<div>`

**HTML esperado en formularioArticulo.php:**
```html
<select id="id_familia">...</select>
<div id="familia-descripcion" style="display:none;">
    <small class="text-muted">
        <i class="fas fa-info-circle"></i>
        <span id="familia-descr-text"></span>
    </small>
</div>
```

---

## 📋 GESTIÓN DEL FORMULARIO

### 1. Detección del Modo (nuevo/editar)

```javascript
// Función para obtener parámetros de la URL
function getUrlParameter(name) {
    name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
    var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
    var results = regex.exec(location.search);
    return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
}

// Verificar si estamos en modo edición
const idArticulo = getUrlParameter('id');
const modo = getUrlParameter('modo') || 'nuevo';

if (modo === 'editar' && idArticulo) {
    cargarDatosArticulo(idArticulo);
} else {
    $('#codigo_articulo').focus();
    setTimeout(function() {
        captureOriginalValues();
    }, 100);
}
```

**Comportamiento:**
- **Modo nuevo**: Focus en `codigo_articulo`, capturar valores vacíos
- **Modo editar**: Cargar datos del artículo por ID

### 2. Cargar Datos para Edición

```javascript
function cargarDatosArticulo(idArticulo) {
    console.log('Cargando datos de artículo ID:', idArticulo);
    
    $.ajax({
        url: "../../controller/articulo.php?op=mostrar",
        type: "POST",
        data: { id_articulo: idArticulo },
        dataType: "json",
        success: function(data) {
            try {
                if (!data || typeof data !== 'object') {
                    throw new Error('Respuesta del servidor no válida');
                }

                console.log('Datos recibidos:', data);

                // Llenar los campos del formulario
                $('#id_articulo').val(data.id_articulo);
                $('#codigo_articulo').val(data.codigo_articulo);
                $('#nombre_articulo').val(data.nombre_articulo);
                $('#name_articulo').val(data.name_articulo);
                $('#precio_alquiler_articulo').val(data.precio_alquiler_articulo || '');
                $('#notas_presupuesto_articulo').val(data.notas_presupuesto_articulo || '');
                $('#notes_budget_articulo').val(data.notes_budget_articulo || '');
                $('#orden_obs_articulo').val(data.orden_obs_articulo || 200);
                $('#observaciones_articulo').val(data.observaciones_articulo || '');
                
                // Configurar familia
                if (data.id_familia) {
                    $('#id_familia').val(data.id_familia);
                    $('#id_familia').trigger('change'); // Mostrar descripción
                }
                
                // Configurar unidad
                if (data.id_unidad) {
                    $('#id_unidad').val(data.id_unidad);
                    $('#id_unidad').trigger('change'); // Mostrar descripción
                }
                
                // Configurar coeficiente (NULL, 0 o 1)
                if (data.coeficiente_articulo === null || data.coeficiente_articulo === '') {
                    $('#coeficiente_heredado').prop('checked', true);
                } else if (data.coeficiente_articulo == 1) {
                    $('#coeficiente_si').prop('checked', true);
                } else {
                    $('#coeficiente_no').prop('checked', true);
                }
                
                // Configurar checkboxes
                $('#es_kit_articulo').prop('checked', data.es_kit_articulo == 1);
                $('#control_total_articulo').prop('checked', data.control_total_articulo == 1);
                $('#no_facturar_articulo').prop('checked', data.no_facturar_articulo == 1);
                
                // Configurar imagen actual
                if (data.imagen_articulo) {
                    $('#imagen_actual').val(data.imagen_articulo);
                    showExistingImage(data.imagen_articulo);
                }
                
                // Configurar el switch de estado
                $('#activo_articulo').prop('checked', data.activo_articulo == 1);
                
                // Actualizar texto del estado
                if (data.activo_articulo == 1) {
                    $('#estado-text').text('Artículo Activo').removeClass('text-danger').addClass('text-success');
                } else {
                    $('#estado-text').text('Artículo Inactivo').removeClass('text-success').addClass('text-danger');
                }
                
                // Actualizar contadores de caracteres
                if (data.notas_presupuesto_articulo) {
                    $('#char-count-notas').text(data.notas_presupuesto_articulo.length);
                }
                if (data.notes_budget_articulo) {
                    $('#char-count-notes').text(data.notes_budget_articulo.length);
                }
                if (data.observaciones_articulo) {
                    $('#char-count-obs').text(data.observaciones_articulo.length);
                }
                
                // Capturar valores originales (para detectar cambios)
                setTimeout(function() {
                    captureOriginalValues();
                }, 100);
                
                $('#codigo_articulo').focus();
                
            } catch (e) {
                console.error('Error al procesar datos:', e);
                toastr.error('Error al cargar datos para edición');
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
            toastr.error('Error al obtener datos del artículo');
            setTimeout(() => {
                window.location.href = 'index.php';
            }, 2000);
        }
    });
}
```

**Aspectos importantes:**

1. **Manejo de valores NULL**:
   ```javascript
   $('#precio_alquiler_articulo').val(data.precio_alquiler_articulo || '');
   ```

2. **Trigger de eventos**:
   ```javascript
   $('#id_familia').trigger('change'); // Dispara el evento para mostrar descripción
   ```

3. **Radio buttons (coeficiente)**:
   ```javascript
   if (data.coeficiente_articulo === null) {
       $('#coeficiente_heredado').prop('checked', true);
   } else if (data.coeficiente_articulo == 1) {
       $('#coeficiente_si').prop('checked', true);
   } else {
       $('#coeficiente_no').prop('checked', true);
   }
   ```

4. **Checkboxes**:
   ```javascript
   $('#es_kit_articulo').prop('checked', data.es_kit_articulo == 1);
   ```

5. **Contadores de caracteres**:
   ```javascript
   $('#char-count-notas').text(data.notas_presupuesto_articulo.length);
   ```

6. **Imagen existente**:
   ```javascript
   showExistingImage(data.imagen_articulo); // Función global
   ```

7. **Redirección en error**:
   ```javascript
   setTimeout(() => { window.location.href = 'index.php'; }, 2000);
   ```

---

## 💾 SISTEMA DE GUARDADO

### 1. Click en Botón Guardar

```javascript
$(document).on('click', '#btnSalvarArticulo', function (event) {
    event.preventDefault();

    // Obtener valores del formulario
    var id_articuloR = $('#id_articulo').val().trim();
    var codigo_articuloR = $('#codigo_articulo').val().trim();
    var nombre_articuloR = $('#nombre_articulo').val().trim();
    var name_articuloR = $('#name_articulo').val().trim();
    var id_familiaR = $('#id_familia').val() || null;
    var id_unidadR = $('#id_unidad').val() || null;
    var precio_alquiler_articuloR = $('#precio_alquiler_articulo').val() || 0;
    var notas_presupuesto_articuloR = $('#notas_presupuesto_articulo').val().trim();
    var notes_budget_articuloR = $('#notes_budget_articulo').val().trim();
    var orden_obs_articuloR = $('#orden_obs_articulo').val() || 200;
    var observaciones_articuloR = $('#observaciones_articulo').val().trim();
    
    // Obtener valor del coeficiente (NULL, 0 o 1)
    var coeficiente_articuloR;
    if ($('#coeficiente_heredado').is(':checked')) {
        coeficiente_articuloR = null;
    } else if ($('#coeficiente_si').is(':checked')) {
        coeficiente_articuloR = 1;
    } else {
        coeficiente_articuloR = 0;
    }
    
    // Obtener valores de checkboxes
    var es_kit_articuloR = $('#es_kit_articulo').is(':checked') ? 1 : 0;
    var control_total_articuloR = $('#control_total_articulo').is(':checked') ? 1 : 0;
    var no_facturar_articuloR = $('#no_facturar_articulo').is(':checked') ? 1 : 0;
    
    // El estado
    var activo_articuloR;
    if (id_articuloR) {
        activo_articuloR = $('#activo_articulo').is(':checked') ? 1 : 0;
    } else {
        activo_articuloR = 1; // Siempre 1 en modo nuevo
    }

    // Validar el formulario
    if (!formValidator.validateForm(event)) {
        toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
        return;
    }
    
    // Verificar artículo primero (unicidad)
    verificarArticuloExistente(
        id_articuloR, 
        codigo_articuloR, 
        nombre_articuloR, 
        name_articuloR, 
        id_familiaR, 
        id_unidadR, 
        precio_alquiler_articuloR,
        coeficiente_articuloR,
        es_kit_articuloR,
        control_total_articuloR,
        no_facturar_articuloR,
        notas_presupuesto_articuloR,
        notes_budget_articuloR,
        orden_obs_articuloR,
        observaciones_articuloR,
        activo_articuloR
    );
});
```

**Aspectos clave:**

1. **Conversión de vacíos a null**:
   ```javascript
   var id_familiaR = $('#id_familia').val() || null;
   ```

2. **Radio buttons con 3 estados (NULL, 0, 1)**:
   ```javascript
   var coeficiente_articuloR;
   if ($('#coeficiente_heredado').is(':checked')) {
       coeficiente_articuloR = null;
   } else if ($('#coeficiente_si').is(':checked')) {
       coeficiente_articuloR = 1;
   } else {
       coeficiente_articuloR = 0;
   }
   ```

3. **Checkboxes a 0/1**:
   ```javascript
   var es_kit_articuloR = $('#es_kit_articulo').is(':checked') ? 1 : 0;
   ```

4. **Estado en modo nuevo siempre 1**:
   ```javascript
   if (id_articuloR) {
       activo_articuloR = $('#activo_articulo').is(':checked') ? 1 : 0;
   } else {
       activo_articuloR = 1; // Nuevo siempre activo
   }
   ```

### 2. Verificar Existencia (Unicidad)

```javascript
function verificarArticuloExistente(id_articulo, codigo_articulo, nombre_articulo, name_articulo, id_familia, id_unidad, precio_alquiler_articulo, coeficiente_articulo, es_kit_articulo, control_total_articulo, no_facturar_articulo, notas_presupuesto_articulo, notes_budget_articulo, orden_obs_articulo, observaciones_articulo, activo_articulo) {
    $.ajax({
        url: "../../controller/articulo.php?op=verificarArticulo",
        type: "GET",
        data: { 
            nombre_articulo: nombre_articulo,
            codigo_articulo: codigo_articulo,
            name_articulo: name_articulo, 
            id_articulo: id_articulo 
        },
        dataType: "json",
        success: function(response) {
            if (!response.success) {
                toastr.warning(response.message || "Error al verificar el artículo.");
                return;
            }

            if (response.existe) {
                mostrarErrorArticuloExistente(nombre_articulo);
            } else {
                guardarArticulo(id_articulo, codigo_articulo, nombre_articulo, name_articulo, id_familia, id_unidad, precio_alquiler_articulo, coeficiente_articulo, es_kit_articulo, control_total_articulo, no_facturar_articulo, notas_presupuesto_articulo, notes_budget_articulo, orden_obs_articulo, observaciones_articulo, activo_articulo);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en verificación:', error);
            toastr.error('Error al verificar el artículo. Intente nuevamente.', 'Error');
        }
    });
}

function mostrarErrorArticuloExistente(nombre_articulo) {
    console.log("Artículo duplicado detectado:", nombre_articulo);
    Swal.fire({
        title: 'Nombre de artículo duplicado',
        text: 'El artículo "' + nombre_articulo + '" ya existe. Por favor, elija otro nombre.',
        icon: 'warning',
        confirmButtonText: 'Entendido'
    });
}
```

**Flujo:**
1. Se verifica si existe un artículo con el mismo nombre/código
2. Si existe → SweetAlert de error
3. Si NO existe → Llamar a `guardarArticulo()`

### 3. Guardar Artículo (INSERT/UPDATE)

```javascript
function guardarArticulo(id_articulo, codigo_articulo, nombre_articulo, name_articulo, id_familia, id_unidad, precio_alquiler_articulo, coeficiente_articulo, es_kit_articulo, control_total_articulo, no_facturar_articulo, notas_presupuesto_articulo, notes_budget_articulo, orden_obs_articulo, observaciones_articulo, activo_articulo) {
    // Mostrar indicador de carga
    $('#btnSalvarArticulo').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
    
    // Crear FormData para manejar archivos
    var formData = new FormData();
    formData.append('codigo_articulo', codigo_articulo);
    formData.append('nombre_articulo', nombre_articulo);
    formData.append('name_articulo', name_articulo);
    formData.append('id_familia', id_familia || '');
    formData.append('id_unidad', id_unidad || '');
    formData.append('precio_alquiler_articulo', precio_alquiler_articulo);
    
    // Solo agregar coeficiente_articulo si NO es null
    if (coeficiente_articulo !== null) {
        formData.append('coeficiente_articulo', coeficiente_articulo);
    }
    formData.append('es_kit_articulo', es_kit_articulo);
    formData.append('control_total_articulo', control_total_articulo);
    formData.append('no_facturar_articulo', no_facturar_articulo);
    formData.append('notas_presupuesto_articulo', notas_presupuesto_articulo);
    formData.append('notes_budget_articulo', notes_budget_articulo);
    formData.append('orden_obs_articulo', orden_obs_articulo);
    formData.append('observaciones_articulo', observaciones_articulo);
    formData.append('activo_articulo', activo_articulo);
    
    // Log para depuración
    console.log('Datos a enviar:');
    console.log('coeficiente_articulo:', coeficiente_articulo);
    console.log('es_kit_articulo:', es_kit_articulo);
    
    if (id_articulo) {
        formData.append('id_articulo', id_articulo);
        formData.append('imagen_actual', $('#imagen_actual').val());
    }
    
    // Agregar archivo de imagen si se seleccionó uno
    const imagenFile = $('#imagen_articulo')[0].files[0];
    if (imagenFile) {
        formData.append('imagen_articulo', imagenFile);
    }

    $.ajax({
        url: "../../controller/articulo.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        processData: false,  // NO procesar los datos
        contentType: false,  // NO establecer contentType (FormData lo hace automático)
        dataType: "json",
        success: function(res) {
            if (res.success) {
                formSaved = true; // Marcar como guardado (para beforeunload)
                
                toastr.success(res.message || "Artículo guardado correctamente");
                
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                toastr.error(res.message || "Error al guardar el artículo");
                $('#btnSalvarArticulo').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Artículo');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en guardado:", error);
            Swal.fire('Error', 'No se pudo guardar el artículo. Error: ' + error, 'error');
            $('#btnSalvarArticulo').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Artículo');
        }
    });
}
```

**Aspectos críticos:**

1. **FormData para archivos**:
   ```javascript
   var formData = new FormData();
   formData.append('codigo_articulo', codigo_articulo);
   ```

2. **NO agregar coeficiente si es NULL**:
   ```javascript
   if (coeficiente_articulo !== null) {
       formData.append('coeficiente_articulo', coeficiente_articulo);
   }
   ```

3. **Archivo de imagen**:
   ```javascript
   const imagenFile = $('#imagen_articulo')[0].files[0];
   if (imagenFile) {
       formData.append('imagen_articulo', imagenFile);
   }
   ```

4. **Configuración AJAX para archivos**:
   ```javascript
   processData: false,  // NO procesar
   contentType: false,  // Dejar que FormData configure el Content-Type
   ```

5. **Botón con spinner**:
   ```javascript
   $('#btnSalvarArticulo').prop('disabled', true)
       .html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
   ```

6. **Redirección tras éxito**:
   ```javascript
   setTimeout(() => { window.location.href = 'index.php'; }, 1500);
   ```

---

## 🔄 CONTROL DE CAMBIOS

### 1. Capturar Valores Originales

```javascript
var formOriginalValues = {};
var formSaved = false;

function captureOriginalValues() {
    formOriginalValues = {
        codigo_articulo: $('#codigo_articulo').val(),
        nombre_articulo: $('#nombre_articulo').val(),
        name_articulo: $('#name_articulo').val(),
        id_familia: $('#id_familia').val(),
        id_unidad: $('#id_unidad').val(),
        precio_alquiler_articulo: $('#precio_alquiler_articulo').val(),
        imagen_actual: $('#imagen_actual').val(),
        coeficiente_articulo: $('input[name="coeficiente_articulo"]:checked').val(),
        es_kit_articulo: $('#es_kit_articulo').is(':checked'),
        control_total_articulo: $('#control_total_articulo').is(':checked'),
        no_facturar_articulo: $('#no_facturar_articulo').is(':checked'),
        notas_presupuesto_articulo: $('#notas_presupuesto_articulo').val(),
        notes_budget_articulo: $('#notes_budget_articulo').val(),
        orden_obs_articulo: $('#orden_obs_articulo').val(),
        observaciones_articulo: $('#observaciones_articulo').val()
    };
}
```

**Cuándo se llama:**
- **Modo nuevo**: Tras cargar la página (valores vacíos)
- **Modo editar**: Tras cargar los datos del backend

### 2. Detectar Cambios

```javascript
function hasFormChanged() {
    const hasNewImage = $('#imagen_articulo')[0].files && $('#imagen_articulo')[0].files.length > 0;
    
    return (
        $('#codigo_articulo').val() !== formOriginalValues.codigo_articulo ||
        $('#nombre_articulo').val() !== formOriginalValues.nombre_articulo ||
        $('#name_articulo').val() !== formOriginalValues.name_articulo ||
        $('#id_familia').val() !== formOriginalValues.id_familia ||
        $('#id_unidad').val() !== formOriginalValues.id_unidad ||
        $('#precio_alquiler_articulo').val() !== formOriginalValues.precio_alquiler_articulo ||
        $('input[name="coeficiente_articulo"]:checked').val() !== formOriginalValues.coeficiente_articulo ||
        $('#es_kit_articulo').is(':checked') !== formOriginalValues.es_kit_articulo ||
        $('#control_total_articulo').is(':checked') !== formOriginalValues.control_total_articulo ||
        $('#no_facturar_articulo').is(':checked') !== formOriginalValues.no_facturar_articulo ||
        $('#notas_presupuesto_articulo').val() !== formOriginalValues.notas_presupuesto_articulo ||
        $('#notes_budget_articulo').val() !== formOriginalValues.notes_budget_articulo ||
        $('#orden_obs_articulo').val() !== formOriginalValues.orden_obs_articulo ||
        $('#observaciones_articulo').val() !== formOriginalValues.observaciones_articulo ||
        hasNewImage
    );
}
```

**Compara:**
- ✅ Todos los campos de texto
- ✅ Todos los selects
- ✅ Todos los radio buttons
- ✅ Todos los checkboxes
- ✅ Si se seleccionó una nueva imagen

### 3. Advertencia antes de Salir

```javascript
window.addEventListener('beforeunload', function (e) {
    if (!formSaved && hasFormChanged()) {
        e.preventDefault();
        e.returnValue = ''; // Chrome requiere returnValue
    }
});
```

**Comportamiento:**
- Si el formulario NO se ha guardado (`formSaved = false`)
- Y el formulario ha cambiado (`hasFormChanged() = true`)
- → Mostrar advertencia del navegador: *"¿Estás seguro que deseas salir?"*

**Desactivar advertencia tras guardar:**
```javascript
if (res.success) {
    formSaved = true; // Ya no mostrar advertencia
    // ...redirección
}
```

---

## 🖼️ FUNCIONES GLOBALES (fuera de document.ready)

### 1. Mostrar Imagen por Defecto

```javascript
function showDefaultImagePreview() {
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview) {
        imagePreview.innerHTML = `
            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        `;
    }
}
```

**Cuándo se usa:**
- Cuando `imagen_articulo` es NULL o vacío
- Cuando falla la carga de la imagen (evento `onerror`)

### 2. Mostrar Imagen Existente

```javascript
function showExistingImage(imagePath) {
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview && imagePath) {
        const fullPath = '../../public/img/articulo/' + imagePath;
        imagePreview.innerHTML = `
            <img src="${fullPath}" alt="Imagen actual" 
                 style="max-width: 100%; max-height: 100px; border-radius: 4px;" 
                 onerror="this.onerror=null; showDefaultImagePreview();">
            <p class="text-muted mt-1 mb-0 small">Imagen actual: ${imagePath}</p>
        `;
    }
}
```

**Características:**
- ✅ Construye la ruta completa: `../../public/img/articulo/nombre.jpg`
- ✅ Maneja error de carga con `onerror` → llama a `showDefaultImagePreview()`
- ✅ Muestra el nombre del archivo debajo

**HTML esperado en formularioArticulo.php:**
```html
<div id="image-preview" class="text-center p-3 border rounded">
    <!-- Se llena dinámicamente -->
</div>
```

---

## 📚 EJEMPLOS PRÁCTICOS

### Ejemplo 1: Adaptar para Clientes

```javascript
$(document).ready(function () {
    
    // 1. VALIDACIONES
    var formValidator = new FormValidator('formCliente', {
        codigo_cliente: { required: true },
        nombre_cliente: { required: true },
        apellido_cliente: { required: true },
        email_cliente: { required: true, email: true }
    });
    
    // 2. CARGAR SELECT DE EMPRESAS
    function cargarEmpresas() {
        $.ajax({
            url: "../../controller/empresas.php?op=listarDisponibles",
            type: "GET",
            dataType: "json",
            success: function(response) {
                var data = response.data || response;
                if (Array.isArray(data)) {
                    var select = $('#id_empresa');
                    select.empty();
                    select.append('<option value="">Seleccionar empresa...</option>');
                    data.forEach(function(empresa) {
                        select.append('<option value="' + empresa.id_empresa + '">' + 
                                      empresa.nombre_empresa + '</option>');
                    });
                }
            }
        });
    }
    
    // 3. CARGAR DATOS PARA EDICIÓN
    function cargarDatosCliente(idCliente) {
        $.ajax({
            url: "../../controller/cliente.php?op=mostrar",
            type: "POST",
            data: { id_cliente: idCliente },
            dataType: "json",
            success: function(data) {
                $('#id_cliente').val(data.id_cliente);
                $('#codigo_cliente').val(data.codigo_cliente);
                $('#nombre_cliente').val(data.nombre_cliente);
                $('#apellido_cliente').val(data.apellido_cliente);
                $('#email_cliente').val(data.email_cliente);
                
                if (data.id_empresa) {
                    $('#id_empresa').val(data.id_empresa);
                }
                
                $('#activo_cliente').prop('checked', data.activo_cliente == 1);
                
                setTimeout(function() {
                    captureOriginalValues();
                }, 100);
            }
        });
    }
    
    // 4. DETECTAR MODO
    const idCliente = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idCliente) {
        cargarDatosCliente(idCliente);
    }
    
    // 5. BOTÓN GUARDAR
    $(document).on('click', '#btnSalvarCliente', function (event) {
        event.preventDefault();
        
        var id_clienteR = $('#id_cliente').val().trim();
        var codigo_clienteR = $('#codigo_cliente').val().trim();
        var nombre_clienteR = $('#nombre_cliente').val().trim();
        var apellido_clienteR = $('#apellido_cliente').val().trim();
        var email_clienteR = $('#email_cliente').val().trim();
        var id_empresaR = $('#id_empresa').val() || null;
        var activo_clienteR = id_clienteR ? ($('#activo_cliente').is(':checked') ? 1 : 0) : 1;
        
        if (!formValidator.validateForm(event)) {
            toastr.error('Corrija los errores del formulario');
            return;
        }
        
        guardarCliente(id_clienteR, codigo_clienteR, nombre_clienteR, 
                       apellido_clienteR, email_clienteR, id_empresaR, activo_clienteR);
    });
    
    // 6. FUNCIÓN GUARDAR
    function guardarCliente(id_cliente, codigo_cliente, nombre_cliente, 
                            apellido_cliente, email_cliente, id_empresa, activo_cliente) {
        $('#btnSalvarCliente').prop('disabled', true)
            .html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        $.ajax({
            url: "../../controller/cliente.php?op=guardaryeditar",
            type: "POST",
            data: {
                id_cliente: id_cliente,
                codigo_cliente: codigo_cliente,
                nombre_cliente: nombre_cliente,
                apellido_cliente: apellido_cliente,
                email_cliente: email_cliente,
                id_empresa: id_empresa,
                activo_cliente: activo_cliente
            },
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    formSaved = true;
                    toastr.success(res.message);
                    setTimeout(() => { window.location.href = 'index.php'; }, 1500);
                } else {
                    toastr.error(res.message);
                    $('#btnSalvarCliente').prop('disabled', false)
                        .html('<i class="fas fa-save me-2"></i>Guardar Cliente');
                }
            }
        });
    }
    
    // 7. CONTROL DE CAMBIOS
    var formOriginalValues = {};
    var formSaved = false;
    
    function captureOriginalValues() {
        formOriginalValues = {
            codigo_cliente: $('#codigo_cliente').val(),
            nombre_cliente: $('#nombre_cliente').val(),
            apellido_cliente: $('#apellido_cliente').val(),
            email_cliente: $('#email_cliente').val(),
            id_empresa: $('#id_empresa').val()
        };
    }
    
    function hasFormChanged() {
        return (
            $('#codigo_cliente').val() !== formOriginalValues.codigo_cliente ||
            $('#nombre_cliente').val() !== formOriginalValues.nombre_cliente ||
            $('#apellido_cliente').val() !== formOriginalValues.apellido_cliente ||
            $('#email_cliente').val() !== formOriginalValues.email_cliente ||
            $('#id_empresa').val() !== formOriginalValues.id_empresa
        );
    }
    
    window.addEventListener('beforeunload', function (e) {
        if (!formSaved && hasFormChanged()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });
    
    // 8. INICIALIZACIÓN
    cargarEmpresas();
    
}); // fin document.ready
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Estructura Básica
- [ ] `$(document).ready(function () { ... });`
- [ ] Variables globales: `formOriginalValues`, `formSaved`
- [ ] Instancia de `FormValidator`

### Funciones de Carga
- [ ] `cargarSelectX()` para cada select dependiente
- [ ] Listener `.on('change')` para mostrar descripciones
- [ ] `getUrlParameter(name)` para detectar modo
- [ ] `cargarDatosX(id)` para modo editar

### Botón Guardar
- [ ] `$(document).on('click', '#btnSalvarX', ...)`
- [ ] Recoger todos los valores del formulario
- [ ] Conversión de vacíos a `null`
- [ ] Manejo de radio buttons (NULL, 0, 1)
- [ ] Manejo de checkboxes (0, 1)
- [ ] Validación con `formValidator.validateForm()`
- [ ] Llamada a función de guardado

### Guardado
- [ ] `FormData` si hay archivos, sino objeto JSON
- [ ] `processData: false, contentType: false` para archivos
- [ ] Botón con spinner mientras guarda
- [ ] `formSaved = true` tras éxito
- [ ] Redirección con `setTimeout(() => { window.location.href = 'index.php'; }, 1500);`

### Control de Cambios
- [ ] `captureOriginalValues()` tras cargar datos
- [ ] `hasFormChanged()` comparando valores actuales vs originales
- [ ] `window.addEventListener('beforeunload', ...)` para advertencia

### Funciones Globales (si aplica)
- [ ] `showDefaultImagePreview()`
- [ ] `showExistingImage(imagePath)`

---

## 📝 NOTAS IMPORTANTES

1. **Separación de archivos**: Este JS está separado del PHP para mantener la separación de responsabilidades

2. **FormData vs JSON**: 
   - **FormData**: Cuando hay archivos (imágenes, PDFs, etc.)
   - **JSON**: Cuando solo hay texto/números

3. **NULL vs vacío**:
   - Convertir vacíos a `null` con: `$('#campo').val() || null`
   - Para PHP backend, es mejor recibir `null` que `""`

4. **Radio buttons con 3 estados**:
   - HTML: `<input type="radio" name="coeficiente" value="null">` NO funciona
   - HTML: `<input type="radio" name="coeficiente" id="coeficiente_heredado">` (sin value)
   - JS: Si `#coeficiente_heredado` está checked → `coeficiente = null`

5. **Archivo NO se agrega si es NULL**:
   ```javascript
   if (coeficiente_articulo !== null) {
       formData.append('coeficiente_articulo', coeficiente_articulo);
   }
   // Si es null, simplemente no se envía ese campo
   ```

6. **beforeunload es estándar HTML5**: No requiere librerías, funciona en todos los navegadores modernos

---

**Última actualización**: 23 de diciembre de 2024  
**Versión**: 1.0  
**Proyecto**: MDR ERP Manager  
**Autor**: Luis - Innovabyte
