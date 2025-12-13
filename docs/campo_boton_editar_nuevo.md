# Documentación Técnica: Campo Select con Botones de Edición/Creación

## Propósito General

Este documento describe la implementación de un componente de interfaz de usuario que combina un campo `<select>` con botones de acción (Nuevo/Editar) integrados, diseñado específicamente para permitir la gestión CRUD de entidades relacionadas sin abandonar el formulario principal. 

El caso de uso implementado es el campo `id_contacto_cliente` en el formulario de presupuestos, que permite:
- Seleccionar un contacto existente de un cliente
- Crear nuevos contactos mediante un modal
- Editar contactos existentes mediante el mismo modal
- Mostrar información contextual del contacto seleccionado
- Evitar la pérdida de datos del formulario principal al gestionar contactos

**Problema que resuelve**: En formularios complejos (como presupuestos), cuando un usuario está introduciendo datos y necesita crear una entidad relacionada (contacto), tradicionalmente tendría que:
1. Guardar como borrador o perder los datos introducidos
2. Navegar a otra página para crear el contacto
3. Regresar y continuar

Esta solución permite gestionar contactos directamente desde el formulario mediante un modal, preservando todos los datos introducidos.

---

## Estructura de Archivos Involucrados

### 1. Vista Principal (HTML)
**Archivo**: `view/Presupuesto/formularioPresupuesto.php`

### 2. Modal Independiente (HTML)
**Archivo**: `view/Presupuesto/contacto_presupuesto.php`

### 3. JavaScript Principal
**Archivo**: `view/Presupuesto/formularioPresupuesto.js`

### 4. Controlador Backend
**Archivo**: `controller/clientes_contacto.php`

### 5. Modelo
**Archivo**: `models/Clientes_contacto.php`

---

## Implementación Paso a Paso

## PARTE 1: HTML - Vista Principal

### 1.1. Estructura del Input Group con Botones

```html
<div class="col-12 col-md-6">
    <label for="id_contacto_cliente" class="form-label">
        Contacto del cliente:
        <i class="bi bi-info-circle text-info" data-bs-toggle="tooltip" 
           title="Solo se muestran los contactos marcados como activos en la tabla de contactos."></i>
    </label>
    <div class="input-group">
        <!-- Select principal -->
        <select class="form-control" name="id_contacto_cliente" id="id_contacto_cliente">
            <option value="">Seleccione primero un cliente</option>
        </select>
        
        <!-- Botón Nuevo -->
        <button type="button" class="btn btn-outline-primary btn-disabled" 
                id="btnNuevoContacto" data-enabled="false" 
                title="Agregar nuevo contacto">
            <i class="bi bi-person-plus-fill"></i>
        </button>
        
        <!-- Botón Editar -->
        <button type="button" class="btn btn-outline-secondary btn-disabled" 
                id="btnEditarContacto" data-enabled="false" 
                title="Editar contacto">
            <i class="bi bi-pencil-fill"></i>
        </button>
    </div>
    <small class="form-text text-muted">Contacto específico del cliente (opcional)</small>
    
    <!-- Sección de información contextual (opcional pero recomendado) -->
    <div id="info-contacto-cliente" style="display: none;" class="mt-2">
        <div class="alert alert-light border-start border-info border-4 mb-0 py-2" role="alert">
            <h6 class="alert-heading tx-11 tx-semibold mb-1">
                <i class="bi bi-person-fill me-1"></i>Datos del Contacto
            </h6>
            <div class="tx-10">
                <div><strong>Cargo:</strong> <span id="cargo_contacto_info">-</span></div>
                <div><strong>Departamento:</strong> <span id="departamento_contacto_info">-</span></div>
                <div><strong>Teléfono:</strong> <span id="telefono_contacto_info">-</span> | 
                     <strong>Móvil:</strong> <span id="movil_contacto_info">-</span></div>
                <div><strong>Email:</strong> <span id="email_contacto_info">-</span></div>
            </div>
        </div>
    </div>
</div>
```

**Elementos clave**:
- **`input-group`**: Contenedor de Bootstrap 5 que agrupa el select y los botones
- **`btn-disabled`**: Clase CSS personalizada (no usar `disabled` nativo)
- **`data-enabled="false"`**: Atributo custom para controlar el estado sin bloquear eventos
- **`type="button"`**: CRÍTICO - Previene que los botones actúen como submit del formulario
- **Iconos Bootstrap Icons**: `bi-person-plus-fill` y `bi-pencil-fill`

### 1.2. CSS para Botones Deshabilitados

```html
<style>
    .btn-disabled {
        opacity: 0.5;
        cursor: not-allowed;
        /* NO usar pointer-events: none - bloquearía la delegación de eventos */
    }
</style>
```

**Importante**: NO usar `pointer-events: none` porque bloquea completamente los eventos del mouse, impidiendo la delegación de eventos.

### 1.3. Inclusión del Modal

```php
<!-- Modal de Contacto Rápido -->
<?php include_once('contacto_presupuesto.php'); ?>
```

---

## PARTE 2: HTML - Modal Independiente

### 2.1. Archivo Modal Separado
**Archivo**: `view/Presupuesto/contacto_presupuesto.php`

```html
<!-- Modal Nuevo/Editar Contacto Rápido desde Presupuesto -->
<div class="modal fade" id="modalContactoRapido" tabindex="-1" 
     aria-labelledby="modalContactoRapidoLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalContactoRapidoLabel">
                    <i class="bi bi-person-plus-fill me-2"></i>
                    <span id="tituloModalContacto">Nuevo Contacto</span>
                </h5>
                <button type="button" class="btn-close btn-close-white" 
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            
            <div class="modal-body">
                <form id="formContactoRapido">
                    <!-- Campos ocultos -->
                    <input type="hidden" id="id_contacto_cliente_modal" name="id_contacto_cliente">
                    <input type="hidden" id="id_cliente_modal" name="id_cliente">
                    
                    <!-- Fila 1: Nombre y Apellidos -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="nombre_contacto_cliente_modal" class="form-label">
                                Nombre: <span class="tx-danger">*</span>
                            </label>
                            <input type="text" class="form-control" 
                                   id="nombre_contacto_cliente_modal" 
                                   name="nombre_contacto_cliente" 
                                   required maxlength="100" 
                                   placeholder="Ej: Juan">
                        </div>
                        <div class="col-md-6">
                            <label for="apellidos_contacto_cliente_modal" class="form-label">
                                Apellidos:
                            </label>
                            <input type="text" class="form-control" 
                                   id="apellidos_contacto_cliente_modal" 
                                   name="apellidos_contacto_cliente" 
                                   maxlength="150" 
                                   placeholder="Ej: García López">
                        </div>
                    </div>
                    
                    <!-- Fila 2: Cargo y Departamento -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="cargo_contacto_cliente_modal" class="form-label">
                                Cargo:
                            </label>
                            <input type="text" class="form-control" 
                                   id="cargo_contacto_cliente_modal" 
                                   name="cargo_contacto_cliente" 
                                   maxlength="100" 
                                   placeholder="Ej: Director de Compras">
                        </div>
                        <div class="col-md-6">
                            <label for="departamento_contacto_cliente_modal" class="form-label">
                                Departamento:
                            </label>
                            <input type="text" class="form-control" 
                                   id="departamento_contacto_cliente_modal" 
                                   name="departamento_contacto_cliente" 
                                   maxlength="100" 
                                   placeholder="Ej: Compras">
                        </div>
                    </div>
                    
                    <!-- Fila 3: Teléfono y Móvil -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label for="telefono_contacto_cliente_modal" class="form-label">
                                Teléfono:
                            </label>
                            <input type="tel" class="form-control" 
                                   id="telefono_contacto_cliente_modal" 
                                   name="telefono_contacto_cliente" 
                                   maxlength="20" 
                                   placeholder="Ej: 924123456">
                        </div>
                        <div class="col-md-6">
                            <label for="movil_contacto_cliente_modal" class="form-label">
                                Móvil:
                            </label>
                            <input type="tel" class="form-control" 
                                   id="movil_contacto_cliente_modal" 
                                   name="movil_contacto_cliente" 
                                   maxlength="20" 
                                   placeholder="Ej: 666123456">
                        </div>
                    </div>
                    
                    <!-- Fila 4: Email -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <label for="email_contacto_cliente_modal" class="form-label">
                                Email:
                            </label>
                            <input type="email" class="form-control" 
                                   id="email_contacto_cliente_modal" 
                                   name="email_contacto_cliente" 
                                   maxlength="100" 
                                   placeholder="Ej: contacto@empresa.com">
                        </div>
                    </div>
                    
                    <!-- Checkbox Contacto Principal -->
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="principal_contacto_cliente_modal" 
                                       name="principal_contacto_cliente" 
                                       value="1">
                                <label class="form-check-label" 
                                       for="principal_contacto_cliente_modal">
                                    Marcar como contacto principal del cliente
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                El contacto principal se selecciona automáticamente
                            </small>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle me-2"></i>Cancelar
                </button>
                <button type="button" class="btn btn-primary" id="btnGuardarContactoRapido">
                    <i class="bi bi-check-circle me-2"></i>Guardar Contacto
                </button>
            </div>
        </div>
    </div>
</div>
```

**Convenciones de nomenclatura**:
- IDs del modal tienen sufijo `_modal`: `id_contacto_cliente_modal`
- Nombres de campos coinciden con la BD: `nombre_contacto_cliente`
- El modal debe tener ID único: `modalContactoRapido`
- Título dinámico en span: `tituloModalContacto`

---

## PARTE 3: JavaScript - Lógica de Cliente

### 3.1. Inicialización y Event Listeners

```javascript
$(document).ready(function () {
    console.log('formularioPresupuesto.js cargado');
    console.log('Botón Nuevo existe:', $('#btnNuevoContacto').length);
    console.log('Botón Editar existe:', $('#btnEditarContacto').length);

    // ... resto del código ...

    /////////////////////////////////////////
    //   GESTIÓN DE CONTACTOS RÁPIDOS     //
    ///////////////////////////////////////

    console.log('Registrando event handlers de botones...');
    console.log('btnNuevoContacto encontrado:', $('#btnNuevoContacto').length);
    console.log('btnEditarContacto encontrado:', $('#btnEditarContacto').length);
    console.log('btnNuevoContacto disabled:', $('#btnNuevoContacto').prop('disabled'));
    console.log('btnEditarContacto disabled:', $('#btnEditarContacto').prop('disabled'));
```

### 3.2. Función para Cargar Contactos del Cliente

```javascript
function cargarContactosCliente(idCliente, idContactoSeleccionado = null) {
    var select = $('#id_contacto_cliente');
    select.empty();
    
    if (!idCliente) {
        select.append('<option value="">Seleccione primero un cliente</option>');
        select.prop('disabled', true);
        return;
    }
    
    select.prop('disabled', true);
    select.append('<option value="">Cargando contactos...</option>');
    
    $.ajax({
        url: "../../controller/clientes_contacto.php?op=selectByCliente",
        type: "POST",
        data: { id_cliente: idCliente },
        dataType: "json",
        success: function(data) {
            select.empty();
            select.append('<option value="">Sin contacto específico</option>');
            
            if (data.data && Array.isArray(data.data) && data.data.length > 0) {
                data.data.forEach(function(contacto) {
                    var nombreCompleto = contacto.nombre_contacto_cliente;
                    if (contacto.apellidos_contacto_cliente) {
                        nombreCompleto += ' ' + contacto.apellidos_contacto_cliente;
                    }
                    if (contacto.cargo_contacto_cliente) {
                        nombreCompleto += ' (' + contacto.cargo_contacto_cliente + ')';
                    }
                    
                    var option = $('<option></option>')
                        .val(contacto.id_contacto_cliente)
                        .text(nombreCompleto);
                    
                    // Marcar como seleccionado si es el contacto especificado o el principal
                    if (idContactoSeleccionado && contacto.id_contacto_cliente == idContactoSeleccionado) {
                        option.prop('selected', true);
                    } else if (!idContactoSeleccionado && contacto.principal_contacto_cliente == 1) {
                        option.prop('selected', true);
                    }
                    
                    select.append(option);
                });
                select.prop('disabled', false);
                
                // CRÍTICO: Disparar evento change para actualizar la información del contacto
                if (idContactoSeleccionado) {
                    select.trigger('change');
                }
            } else {
                select.append('<option value="">No hay contactos disponibles</option>');
                select.prop('disabled', true);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar contactos:', error);
            select.empty();
            select.append('<option value="">Error al cargar contactos</option>');
            select.prop('disabled', true);
            toastr.warning('No se pudieron cargar los contactos del cliente');
        }
    });
}
```

### 3.3. Función para Cargar Información del Contacto

```javascript
function cargarInfoContacto(idContacto) {
    if (!idContacto) {
        $('#info-contacto-cliente').hide();
        return;
    }
    
    $.ajax({
        url: '../../controller/clientes_contacto.php?op=mostrar',
        type: 'POST',
        data: { id_contacto_cliente: idContacto },
        dataType: 'json',
        success: function(data) {
            if (data) {
                // Actualizar campos informativos
                $('#cargo_contacto_info').text(data.cargo_contacto_cliente || '-');
                $('#departamento_contacto_info').text(data.departamento_contacto_cliente || '-');
                $('#telefono_contacto_info').text(data.telefono_contacto_cliente || '-');
                $('#movil_contacto_info').text(data.movil_contacto_cliente || '-');
                $('#email_contacto_info').text(data.email_contacto_cliente || '-');
                
                // Mostrar sección informativa
                $('#info-contacto-cliente').fadeIn();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar información del contacto:', error);
            $('#info-contacto-cliente').hide();
        }
    });
}
```

### 3.4. Event Handlers del Campo Dependiente (Cliente)

```javascript
// Event listener para cambio de cliente
$('#id_cliente').on('change', function() {
    var idCliente = $(this).val();
    cargarContactosCliente(idCliente);
    cargarInfoCliente(idCliente); // Si existe
    
    // Limpiar información del contacto al cambiar de cliente
    $('#info-contacto-cliente').hide();
    
    // Habilitar/deshabilitar botón de nuevo contacto
    if (idCliente) {
        $('#btnNuevoContacto').removeClass('btn-disabled').attr('data-enabled', 'true');
    } else {
        $('#btnNuevoContacto, #btnEditarContacto').addClass('btn-disabled').attr('data-enabled', 'false');
    }
});

// Event listener para cambio de contacto
$('#id_contacto_cliente').on('change', function() {
    var idContacto = $(this).val();
    cargarInfoContacto(idContacto);
    
    // Habilitar/deshabilitar botón de editar contacto
    if (idContacto) {
        $('#btnEditarContacto').removeClass('btn-disabled').attr('data-enabled', 'true');
    } else {
        $('#btnEditarContacto').addClass('btn-disabled').attr('data-enabled', 'false');
    }
});
```

**Patrón importante**: Usar `removeClass/addClass` con `btn-disabled` en lugar de `.prop('disabled')` para permitir que los eventos click se disparen.

### 3.5. Event Handler - Botón Nuevo Contacto (Delegación de Eventos)

```javascript
// Abrir modal para nuevo contacto
$(document).on('click', '#btnNuevoContacto', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('Click en btnNuevoContacto (delegación)');
    
    // Verificar si el botón está habilitado
    if ($(this).attr('data-enabled') !== 'true') {
        console.log('Botón deshabilitado, ignorando click');
        return false;
    }
    
    var idCliente = $('#id_cliente').val();
    console.log('ID Cliente (delegación):', idCliente);
    
    if (!idCliente) {
        toastr.warning('Debe seleccionar un cliente primero');
        return false;
    }
    
    // Configurar modal para NUEVO contacto
    $('#tituloModalContacto').text('Nuevo Contacto');
    $('#formContactoRapido')[0].reset();
    $('#id_contacto_cliente_modal').val('');
    $('#id_cliente_modal').val(idCliente);
    $('#principal_contacto_cliente_modal').prop('checked', false);
    $('#modalContactoRapido').modal('show');
    
    return false;
});
```

**Uso de delegación de eventos**: `$(document).on('click', '#btnNuevoContacto', ...)` permite que el evento funcione incluso si el elemento se modifica dinámicamente o tiene estilos que simulan `disabled`.

### 3.6. Event Handler - Botón Editar Contacto

```javascript
// Abrir modal para editar contacto
$(document).on('click', '#btnEditarContacto', function(e) {
    e.preventDefault();
    e.stopPropagation();
    console.log('Click en btnEditarContacto (delegación)');
    
    // Verificar si el botón está habilitado
    if ($(this).attr('data-enabled') !== 'true') {
        console.log('Botón deshabilitado, ignorando click');
        return false;
    }
    
    var idContacto = $('#id_contacto_cliente').val();
    console.log('ID Contacto (delegación):', idContacto);
    
    if (!idContacto) {
        toastr.warning('Debe seleccionar un contacto primero');
        return false;
    }
    
    // Configurar modal para EDITAR contacto
    $('#tituloModalContacto').text('Editar Contacto');
    
    // Cargar datos del contacto
    $.ajax({
        url: '../../controller/clientes_contacto.php?op=mostrar',
        type: 'POST',
        data: { id_contacto_cliente: idContacto },
        dataType: 'json',
        success: function(data) {
            if (data) {
                $('#id_contacto_cliente_modal').val(data.id_contacto_cliente);
                $('#id_cliente_modal').val(data.id_cliente);
                $('#nombre_contacto_cliente_modal').val(data.nombre_contacto_cliente || '');
                $('#apellidos_contacto_cliente_modal').val(data.apellidos_contacto_cliente || '');
                $('#cargo_contacto_cliente_modal').val(data.cargo_contacto_cliente || '');
                $('#departamento_contacto_cliente_modal').val(data.departamento_contacto_cliente || '');
                $('#telefono_contacto_cliente_modal').val(data.telefono_contacto_cliente || '');
                $('#movil_contacto_cliente_modal').val(data.movil_contacto_cliente || '');
                $('#email_contacto_cliente_modal').val(data.email_contacto_cliente || '');
                $('#principal_contacto_cliente_modal').prop('checked', data.principal_contacto_cliente == 1);
                $('#modalContactoRapido').modal('show');
            } else {
                toastr.error('No se pudo cargar la información del contacto');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar contacto:', error);
            toastr.error('Error de comunicación con el servidor');
        }
    });
    
    return false;
});
```

### 3.7. Event Handler - Guardar Contacto

```javascript
// Guardar contacto desde el modal
$(document).on('click', '#btnGuardarContactoRapido', function(e) {
    e.preventDefault();
    console.log('Click en btnGuardarContactoRapido');
    
    var form = $('#formContactoRapido')[0];
    
    // Validar formulario HTML5
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }
    
    var idContacto = $('#id_contacto_cliente_modal').val();
    console.log('ID Contacto a guardar:', idContacto);
    
    // Preparar datos del formulario
    var formData = new FormData(form);
    formData.append('activo_contacto_cliente', '1');
    
    // Si no está marcado como principal, enviar 0
    if (!$('#principal_contacto_cliente_modal').prop('checked')) {
        formData.set('principal_contacto_cliente', '0');
    }
    
    // Deshabilitar botón mientras se guarda
    var btnGuardar = $(this);
    var textoOriginal = btnGuardar.html();
    btnGuardar.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
    
    console.log('Enviando petición AJAX a guardaryeditar');
    
    $.ajax({
        url: '../../controller/clientes_contacto.php?op=guardaryeditar',
        type: 'POST',
        data: formData,
        processData: false,
        contentType: false,
        dataType: 'json',
        success: function(response) {
            btnGuardar.prop('disabled', false).html(textoOriginal);
            
            if (response.success || response.status === 'success') {
                var mensaje = idContacto ? 'Contacto actualizado correctamente' : 'Contacto creado correctamente';
                toastr.success(mensaje);
                $('#modalContactoRapido').modal('hide');
                
                // Obtener el ID del contacto guardado
                var idContactoGuardado = response.id_contacto_cliente || response.id || idContacto;
                
                console.log('ID del contacto guardado:', idContactoGuardado);
                
                // Recargar lista de contactos del cliente (el trigger change cargará la info)
                var idCliente = $('#id_cliente').val();
                cargarContactosCliente(idCliente, idContactoGuardado);
            } else {
                toastr.error(response.message || 'Error al guardar el contacto');
            }
        },
        error: function(xhr, status, error) {
            btnGuardar.prop('disabled', false).html(textoOriginal);
            console.error('Error al guardar contacto:', error);
            console.error('Status:', status);
            console.error('XHR Status:', xhr.status);
            console.error('Response Text:', xhr.responseText);
            console.error('FormData entries:');
            for (var pair of formData.entries()) {
                console.log(pair[0] + ': ' + pair[1]);
            }
            toastr.error('Error de comunicación con el servidor. Ver consola para detalles.');
        }
    });
});
```

**Detalles críticos**:
- Usar `FormData` para enviar archivos si es necesario
- `processData: false` y `contentType: false` para FormData
- Siempre incluir feedback visual (spinner, deshabilitar botón)
- El `trigger('change')` en `cargarContactosCliente` actualiza automáticamente la información

---

## PARTE 4: Backend - Controlador

### 4.1. Operación `selectByCliente`
**Archivo**: `controller/clientes_contacto.php`

```php
case "selectByCliente":
    // Listar contactos activos de un cliente específico
    $id_cliente = isset($_POST["id_cliente"]) ? limpiarcadena($_POST["id_cliente"]) : "";
    
    if (empty($id_cliente)) {
        echo json_encode([
            "success" => false,
            "message" => "ID de cliente no proporcionado",
            "data" => []
        ]);
        break;
    }
    
    $rspta = $clientes_contacto->listar_por_cliente($id_cliente);
    $data = array();
    
    while ($reg = $rspta->fetch_object()) {
        // Filtrar solo contactos activos
        if ($reg->activo_contacto_cliente == 1) {
            $data[] = array(
                "id_contacto_cliente" => $reg->id_contacto_cliente,
                "id_cliente" => $reg->id_cliente,
                "nombre_contacto_cliente" => $reg->nombre_contacto_cliente,
                "apellidos_contacto_cliente" => $reg->apellidos_contacto_cliente,
                "cargo_contacto_cliente" => $reg->cargo_contacto_cliente,
                "departamento_contacto_cliente" => $reg->departamento_contacto_cliente,
                "telefono_contacto_cliente" => $reg->telefono_contacto_cliente,
                "movil_contacto_cliente" => $reg->movil_contacto_cliente,
                "email_contacto_cliente" => $reg->email_contacto_cliente,
                "principal_contacto_cliente" => $reg->principal_contacto_cliente,
                "activo_contacto_cliente" => $reg->activo_contacto_cliente
            );
        }
    }
    
    echo json_encode([
        "success" => true,
        "data" => $data
    ]);
    break;
```

**Importante**: Filtrar por `activo_contacto_cliente == 1` para mostrar solo contactos activos.

### 4.2. Operación `mostrar`

```php
case "mostrar":
    // Obtener datos de un contacto específico
    $id_contacto_cliente = isset($_POST["id_contacto_cliente"]) ? limpiarcadena($_POST["id_contacto_cliente"]) : "";
    
    if (empty($id_contacto_cliente)) {
        echo json_encode([
            "success" => false,
            "message" => "ID de contacto no proporcionado"
        ]);
        break;
    }
    
    $rspta = $clientes_contacto->mostrar($id_contacto_cliente);
    echo json_encode($rspta);
    break;
```

### 4.3. Operación `guardaryeditar` (CRÍTICO)

```php
case "guardaryeditar":
    writeToLog(['action' => 'guardaryeditar_iniciado', 'timestamp' => date('Y-m-d H:i:s')]);
    
    try {
        $id_cliente = $_POST["id_cliente"] ?? '';
        $nombre_contacto_cliente = $_POST["nombre_contacto_cliente"] ?? '';
        $apellidos_contacto_cliente = $_POST["apellidos_contacto_cliente"] ?? '';
        $cargo_contacto_cliente = $_POST["cargo_contacto_cliente"] ?? '';
        $departamento_contacto_cliente = $_POST["departamento_contacto_cliente"] ?? '';
        $telefono_contacto_cliente = $_POST["telefono_contacto_cliente"] ?? '';
        $movil_contacto_cliente = $_POST["movil_contacto_cliente"] ?? '';
        $email_contacto_cliente = $_POST["email_contacto_cliente"] ?? '';
        $extension_contacto_cliente = $_POST["extension_contacto_cliente"] ?? '';
        $principal_contacto_cliente = isset($_POST["principal_contacto_cliente"]) ? (int)$_POST["principal_contacto_cliente"] : 0;
        $observaciones_contacto_cliente = $_POST["observaciones_contacto_cliente"] ?? '';
        
        writeToLog([
            'action' => 'guardaryeditar_inicio',
            'post_info' => $_POST
        ]);
        
        if (empty($_POST["id_contacto_cliente"])) {
            // INSERTAR NUEVO CONTACTO
            $resultado = $clientes_contacto->insert_contacto_cliente(
                $id_cliente,
                $nombre_contacto_cliente,
                $apellidos_contacto_cliente,
                $cargo_contacto_cliente,
                $departamento_contacto_cliente,
                $telefono_contacto_cliente,
                $movil_contacto_cliente,
                $email_contacto_cliente,
                $extension_contacto_cliente,
                $principal_contacto_cliente,
                $observaciones_contacto_cliente
            );

            if ($resultado !== false) {
                $registro->registrarActividad(
                    'admin',
                    'clientes_contacto.php',
                    'Guardar contacto cliente',
                    "Contacto cliente guardado exitosamente",
                    "info"
                );

                // CRÍTICO: Devolver el ID del contacto insertado
                echo json_encode([
                    "success" => true,
                    "message" => "Contacto cliente insertado correctamente",
                    "id_contacto_cliente" => $resultado  // <-- IMPORTANTE
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al insertar el contacto cliente"
                ]);
            }

        } else {
            // ACTUALIZAR CONTACTO EXISTENTE
            $resultado = $clientes_contacto->update_contacto_cliente(
                $_POST["id_contacto_cliente"],
                $id_cliente,
                $nombre_contacto_cliente,
                $apellidos_contacto_cliente,
                $cargo_contacto_cliente,
                $departamento_contacto_cliente,
                $telefono_contacto_cliente,
                $movil_contacto_cliente,
                $email_contacto_cliente,
                $extension_contacto_cliente,
                $principal_contacto_cliente,
                $observaciones_contacto_cliente
            );

            if ($resultado !== false) {
                $registro->registrarActividad(
                    'admin',
                    'clientes_contacto.php',
                    'Actualizar contacto cliente',
                    "Contacto cliente actualizado exitosamente",
                    "info"
                );

                echo json_encode([
                    "success" => true,
                    "message" => "Contacto cliente actualizado correctamente"
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al actualizar el contacto cliente"
                ]);
            }
        }
        
    } catch (Exception $e) {
        writeToLog(['action' => 'guardaryeditar_error', 'error' => $e->getMessage()]);
        echo json_encode([
            "success" => false,
            "message" => "Error: " . $e->getMessage()
        ]);
    }
    break;
```

**CRÍTICO**: Al insertar, devolver `"id_contacto_cliente" => $resultado` para que el frontend pueda seleccionar automáticamente el contacto creado.

---

## PARTE 5: Backend - Modelo

### 5.1. Método `insert_contacto_cliente`
**Archivo**: `models/Clientes_contacto.php`

```php
public function insert_contacto_cliente(
    $id_cliente, 
    $nombre_contacto_cliente, 
    $apellidos_contacto_cliente, 
    $cargo_contacto_cliente, 
    $departamento_contacto_cliente, 
    $telefono_contacto_cliente, 
    $movil_contacto_cliente, 
    $email_contacto_cliente, 
    $extension_contacto_cliente, 
    $principal_contacto_cliente, 
    $observaciones_contacto_cliente
) {
    try {
        // Configurar zona horaria
        $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        
        $sql = "INSERT INTO contacto_cliente (
            id_cliente, 
            nombre_contacto_cliente, 
            apellidos_contacto_cliente, 
            cargo_contacto_cliente, 
            departamento_contacto_cliente, 
            telefono_contacto_cliente, 
            movil_contacto_cliente, 
            email_contacto_cliente, 
            extension_contacto_cliente, 
            principal_contacto_cliente, 
            observaciones_contacto_cliente, 
            activo_contacto_cliente, 
            created_at_contacto_cliente, 
            updated_at_contacto_cliente
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, NOW(), NOW())";
        
        $stmt = $this->conexion->prepare($sql);
        
        if (!$stmt) {
            throw new Exception("Error al preparar la consulta SQL");
        }
                      
        $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT); 
        $stmt->bindValue(2, $nombre_contacto_cliente, PDO::PARAM_STR); 
        $stmt->bindValue(3, $apellidos_contacto_cliente, PDO::PARAM_STR); 
        $stmt->bindValue(4, $cargo_contacto_cliente, PDO::PARAM_STR); 
        $stmt->bindValue(5, $departamento_contacto_cliente, PDO::PARAM_STR); 
        $stmt->bindValue(6, $telefono_contacto_cliente, PDO::PARAM_STR); 
        $stmt->bindValue(7, $movil_contacto_cliente, PDO::PARAM_STR); 
        $stmt->bindValue(8, $email_contacto_cliente, PDO::PARAM_STR); 
        $stmt->bindValue(9, $extension_contacto_cliente, PDO::PARAM_STR); 
        $stmt->bindValue(10, $principal_contacto_cliente, PDO::PARAM_BOOL); 
        $stmt->bindValue(11, $observaciones_contacto_cliente, PDO::PARAM_STR); 
                      
        $resultado = $stmt->execute();
        
        if (!$resultado) {
            $errorInfo = $stmt->errorInfo();
            throw new Exception("Error al ejecutar la consulta: " . $errorInfo[2]);
        }
                      
        // CRÍTICO: Devolver el ID insertado
        $idInsert = $this->conexion->lastInsertId();

        $this->registro->registrarActividad(
            'admin',
            'Clientes_contacto',
            'Insertar',
            "Se insertó el contacto cliente con ID: $idInsert",
            'info'
        );

        return $idInsert;  // <-- IMPORTANTE: Devolver el ID
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Clientes_contacto',
            'Insertar',
            "Error al insertar contacto cliente: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}
```

**CRÍTICO**: `return $idInsert;` permite al controlador devolver el ID al frontend.

### 5.2. Método `listar_por_cliente`

```php
public function listar_por_cliente($id_cliente)
{
    try {
        $sql = "SELECT * FROM contacto_cliente 
                WHERE id_cliente = ? 
                ORDER BY principal_contacto_cliente DESC, nombre_contacto_cliente ASC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt;
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Clientes_contacto',
            'Listar por cliente',
            "Error al listar contactos del cliente: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}
```

**Orden**: `principal_contacto_cliente DESC` asegura que el contacto principal aparezca primero.

---

## PARTE 6: Flujos de Trabajo

### 6.1. Flujo: Crear Nuevo Contacto

1. **Usuario selecciona cliente** → `#id_cliente.change()`
2. **Se cargan contactos** → `cargarContactosCliente(idCliente)`
3. **Botón Nuevo se habilita** → `removeClass('btn-disabled')`
4. **Usuario hace click en Nuevo** → `$(document).on('click', '#btnNuevoContacto')`
5. **Verificación de estado** → `if (data-enabled !== 'true') return`
6. **Configurar modal** → Reset form, establecer `id_cliente_modal`
7. **Usuario completa formulario** → Validación HTML5
8. **Usuario hace click en Guardar** → `$('#btnGuardarContactoRapido').click()`
9. **AJAX a backend** → `clientes_contacto.php?op=guardaryeditar`
10. **Backend inserta y devuelve ID** → `{ success: true, id_contacto_cliente: 123 }`
11. **Frontend recarga select** → `cargarContactosCliente(idCliente, idContactoGuardado)`
12. **Select dispara change** → `select.trigger('change')`
13. **Se carga información** → `cargarInfoContacto(idContactoGuardado)`
14. **Modal se cierra** → `$('#modalContactoRapido').modal('hide')`

### 6.2. Flujo: Editar Contacto Existente

1. **Usuario selecciona contacto** → `#id_contacto_cliente.change()`
2. **Se carga información** → `cargarInfoContacto(idContacto)`
3. **Botón Editar se habilita** → `removeClass('btn-disabled')`
4. **Usuario hace click en Editar** → `$(document).on('click', '#btnEditarContacto')`
5. **Verificación de estado** → `if (data-enabled !== 'true') return`
6. **AJAX obtiene datos** → `clientes_contacto.php?op=mostrar`
7. **Llenar formulario modal** → `$('#campo_modal').val(data.campo)`
8. **Usuario modifica y guarda** → `$('#btnGuardarContactoRapido').click()`
9. **AJAX actualiza backend** → `clientes_contacto.php?op=guardaryeditar`
10. **Backend actualiza** → `{ success: true }`
11. **Frontend recarga select** → `cargarContactosCliente(idCliente, idContacto)`
12. **Se actualiza información** → `cargarInfoContacto(idContacto)`

---

## PARTE 7: Convenciones y Buenas Prácticas

### 7.1. Nomenclatura

| Tipo | Convención | Ejemplo |
|------|------------|---------|
| ID campo principal | `id_[entidad]` | `id_contacto_cliente` |
| ID campos modal | `[campo]_modal` | `nombre_contacto_cliente_modal` |
| Botón Nuevo | `btnNuevo[Entidad]` | `btnNuevoContacto` |
| Botón Editar | `btnEditar[Entidad]` | `btnEditarContacto` |
| Botón Guardar | `btnGuardar[Accion]` | `btnGuardarContactoRapido` |
| Modal ID | `modal[Nombre]` | `modalContactoRapido` |
| Función cargar lista | `cargar[Entidades][Padre]` | `cargarContactosCliente` |
| Función cargar info | `cargarInfo[Entidad]` | `cargarInfoContacto` |
| Sección información | `info-[entidad]` | `info-contacto-cliente` |
| Spans informativos | `[campo]_info` | `cargo_contacto_info` |

### 7.2. CSS y Estilos

```css
/* NO hacer esto - bloquea eventos */
.btn-disabled {
    pointer-events: none; /* ❌ MAL */
}

/* SÍ hacer esto - simula disabled sin bloquear */
.btn-disabled {
    opacity: 0.5;
    cursor: not-allowed;
    /* ✅ BIEN - sin pointer-events */
}
```

### 7.3. Atributos HTML

```html
<!-- ❌ MAL - No usar disabled nativo -->
<button type="button" id="btnNuevo" disabled>

<!-- ✅ BIEN - Usar clase y data-attribute -->
<button type="button" id="btnNuevo" class="btn-disabled" data-enabled="false">
```

### 7.4. Event Delegation

```javascript
// ❌ MAL - No funciona con elementos modificados dinámicamente
$('#btnNuevo').on('click', function() { ... });

// ✅ BIEN - Funciona siempre con delegación
$(document).on('click', '#btnNuevo', function(e) {
    e.preventDefault();
    e.stopPropagation();
    
    // Verificar estado manualmente
    if ($(this).attr('data-enabled') !== 'true') {
        return false;
    }
    
    // ... lógica ...
});
```

### 7.5. AJAX con FormData

```javascript
// Preparar FormData
var formData = new FormData($('#formModal')[0]);

// Agregar campos adicionales
formData.append('activo_campo', '1');

// Configurar AJAX
$.ajax({
    url: 'controller.php?op=guardaryeditar',
    type: 'POST',
    data: formData,
    processData: false,      // CRÍTICO
    contentType: false,      // CRÍTICO
    dataType: 'json',
    success: function(response) { ... }
});
```

### 7.6. Validación HTML5

```javascript
var form = $('#formModal')[0];

if (!form.checkValidity()) {
    form.reportValidity();  // Muestra mensajes nativos
    return;
}

// Continuar con guardado
```

### 7.7. Feedback Visual

```javascript
var btnGuardar = $('#btnGuardar');
var textoOriginal = btnGuardar.html();

// Antes de AJAX
btnGuardar.prop('disabled', true)
    .html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');

// Después de AJAX (siempre, en success y error)
btnGuardar.prop('disabled', false).html(textoOriginal);
```

---

## PARTE 8: Checklist de Implementación

### Frontend (HTML/JS)

- [ ] Crear estructura `input-group` con select y botones
- [ ] Agregar clase `btn-disabled` y atributo `data-enabled="false"` a botones
- [ ] Incluir tooltips informativos con `bi-info-circle`
- [ ] Crear archivo modal separado con formulario completo
- [ ] Incluir modal con `<?php include_once('archivo_modal.php'); ?>`
- [ ] Agregar CSS personalizado para `.btn-disabled`
- [ ] Implementar función `cargarEntidades[Padre](idPadre, idSeleccionado)`
- [ ] Implementar función `cargarInfoEntidad(idEntidad)` (opcional)
- [ ] Configurar event listeners para campo padre con habilitación de botones
- [ ] Configurar event listeners para campo hijo con actualización de info
- [ ] Implementar handlers con delegación `$(document).on('click', ...)`
- [ ] Agregar verificación de `data-enabled` en handlers
- [ ] Implementar lógica de modal para NUEVO (reset + config)
- [ ] Implementar lógica de modal para EDITAR (AJAX + llenar campos)
- [ ] Implementar guardado con FormData y validación HTML5
- [ ] Agregar `trigger('change')` después de recargar select
- [ ] Incluir feedback visual (spinner, disabled) en todos los botones
- [ ] Agregar console.log para debugging

### Backend (Controller)

- [ ] Crear/verificar operación `selectBy[Padre]` que filtre activos
- [ ] Crear/verificar operación `mostrar` para obtener detalles
- [ ] Crear/verificar operación `guardaryeditar` que:
  - [ ] Detecte INSERT vs UPDATE basándose en `id_entidad`
  - [ ] Devuelva `id_entidad` en respuesta JSON al insertar
  - [ ] Incluya campos de auditoría (`created_at`, `updated_at`)
  - [ ] Registre actividad con `$registro->registrarActividad()`
  - [ ] Maneje errores con try-catch
- [ ] Validar todos los campos obligatorios
- [ ] Usar `limpiarcadena()` para sanitizar inputs

### Backend (Model)

- [ ] Implementar `insert_[entidad]()` que:
  - [ ] Use prepared statements con `bindValue()`
  - [ ] Devuelva `$this->conexion->lastInsertId()`
  - [ ] Maneje errores con try-catch
  - [ ] Registre actividad
- [ ] Implementar `update_[entidad]()` similar
- [ ] Implementar `listar_por_[padre]()` con ORDER BY apropiado
- [ ] Implementar `mostrar($id)` para obtener detalles

### Base de Datos

- [ ] Verificar existencia de campo `activo_[entidad]`
- [ ] Verificar existencia de `created_at_[entidad]`
- [ ] Verificar existencia de `updated_at_[entidad]`
- [ ] Verificar foreign key hacia entidad padre
- [ ] Verificar índices en campos de búsqueda

---

## PARTE 9: Troubleshooting

### Problema: Botones no responden

**Causa**: Uso de `disabled` nativo o `pointer-events: none`

**Solución**:
```javascript
// Cambiar de:
$('#btn').prop('disabled', true);

// A:
$('#btn').addClass('btn-disabled').attr('data-enabled', 'false');
```

### Problema: Select no se actualiza después de crear

**Causa**: No se devuelve el ID del backend o no se dispara `change`

**Solución**:
```php
// Backend debe devolver:
echo json_encode([
    "success" => true,
    "id_contacto_cliente" => $resultado
]);
```

```javascript
// Frontend debe disparar:
if (idContactoSeleccionado) {
    select.trigger('change');
}
```

### Problema: Información no se actualiza al editar

**Causa**: No se recarga la información después de actualizar

**Solución**: Asegurar que `cargarContactosCliente` dispare `trigger('change')`:
```javascript
cargarContactosCliente(idCliente, idContactoGuardado);
// El trigger('change') interno llamará a cargarInfoContacto()
```

### Problema: Modal muestra datos del contacto anterior

**Causa**: No se resetea el formulario al abrir para nuevo

**Solución**:
```javascript
$('#formContactoRapido')[0].reset();
$('#id_contacto_cliente_modal').val('');
```

### Problema: Error "FormData entries is not a function"

**Causa**: Intentar iterar FormData en navegadores antiguos

**Solución**: Usar console.log condicional:
```javascript
if (formData.entries) {
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
}
```

---

## PARTE 10: Ejemplos de Adaptación

### Ejemplo 1: Campo Proveedor con Contactos

```html
<!-- HTML -->
<div class="input-group">
    <select class="form-control" name="id_contacto_proveedor" id="id_contacto_proveedor">
        <option value="">Seleccione primero un proveedor</option>
    </select>
    <button type="button" class="btn btn-outline-primary btn-disabled" 
            id="btnNuevoContactoProveedor" data-enabled="false">
        <i class="bi bi-person-plus-fill"></i>
    </button>
    <button type="button" class="btn btn-outline-secondary btn-disabled" 
            id="btnEditarContactoProveedor" data-enabled="false">
        <i class="bi bi-pencil-fill"></i>
    </button>
</div>
```

```javascript
// JavaScript
function cargarContactosProveedor(idProveedor, idContactoSeleccionado = null) {
    // Adaptar lógica cambiando endpoints y campos
}

$(document).on('click', '#btnNuevoContactoProveedor', function(e) {
    // Adaptar lógica
});
```

### Ejemplo 2: Campo Artículo con Categorías

```html
<!-- HTML -->
<div class="input-group">
    <select class="form-control" name="id_categoria" id="id_categoria">
        <option value="">Seleccione una categoría</option>
    </select>
    <button type="button" class="btn btn-outline-success btn-disabled" 
            id="btnNuevaCategoria" data-enabled="false">
        <i class="bi bi-folder-plus"></i>
    </button>
    <button type="button" class="btn btn-outline-warning btn-disabled" 
            id="btnEditarCategoria" data-enabled="false">
        <i class="bi bi-pencil-square"></i>
    </button>
</div>
```

---

## PARTE 11: Resumen de Archivos a Modificar

### Nuevos Archivos
1. `view/[Modulo]/[entidad]_modal.php` - Modal independiente

### Archivos a Modificar
1. `view/[Modulo]/formulario[Principal].php` - Agregar input-group y botones
2. `view/[Modulo]/formulario[Principal].js` - Toda la lógica JavaScript
3. `controller/[entidad].php` - Operaciones `selectBy[Padre]`, `mostrar`, `guardaryeditar`
4. `models/[Entidad].php` - Métodos `insert`, `update`, `listar_por_[padre]`, `mostrar`

---

## Conclusión

Este patrón permite crear campos select con funcionalidad CRUD completa sin abandonar el formulario principal, mejorando significativamente la experiencia de usuario y evitando la pérdida de datos. Es especialmente útil en:

- Formularios de presupuestos/facturas
- Formularios de pedidos
- Formularios de tickets/incidencias
- Cualquier formulario maestro-detalle con relaciones 1:N

La clave está en:
1. Usar delegación de eventos para que funcione con elementos dinámicos
2. NO usar `disabled` nativo, sino clases CSS con `data-enabled`
3. Devolver el ID insertado desde el backend
4. Disparar `trigger('change')` después de recargar el select
5. Mantener el modal en archivo separado para reutilización
