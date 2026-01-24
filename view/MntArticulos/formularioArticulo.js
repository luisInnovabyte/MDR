$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

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

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Cargar familias disponibles
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
                            select.append('<option value="' + familia.id_familia + '" data-descripcion="' + (familia.descr_familia || '') + '">' + displayText + '</option>');
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

    // Cargar unidades de medida disponibles
    function cargarUnidadesMedida() {
        $.ajax({
            url: "../../controller/unidad_medida.php?op=listarDisponibles",
            type: "GET",
            dataType: "json",
            success: function(response) {
                try {
                    console.log('Respuesta de unidades de medida:', response);
                    
                    // La respuesta viene como {data: [...], draw: 1, recordsTotal: N, recordsFiltered: N}
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
                            select.append('<option value="' + unidad.id_unidad + '" data-descripcion="' + (unidad.descr_unidad || '') + '">' + displayText + '</option>');
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

    // Cargar impuestos disponibles
    function cargarImpuestos() {
        $.ajax({
            url: "../../controller/impuesto.php?op=listarDisponibles",
            type: "GET",
            dataType: "json",
            success: function(response) {
                try {
                    console.log('Respuesta impuestos:', response);
                    
                    if (response && response.data && Array.isArray(response.data)) {
                        var select = $('#id_impuesto');
                        select.empty();
                        select.append('<option value="">-- Sin impuesto --</option>');
                        
                        response.data.forEach(function(impuesto) {
                            var option = $('<option></option>')
                                .attr('value', impuesto.id_impuesto)
                                .text(impuesto.tipo_impuesto + ' (' + impuesto.tasa_impuesto + '%)');
                            select.append(option);
                        });
                        
                        console.log('Impuestos cargados correctamente');
                    } else {
                        console.error('Formato de respuesta inesperado:', response);
                        toastr.error('Error al cargar los impuestos');
                    }
                } catch (e) {
                    console.error('Error al procesar impuestos:', e);
                    toastr.error('Error al cargar los impuestos');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar impuestos:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al cargar los impuestos');
            }
        });
    }

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

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para cargar datos de artículo para edición
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
                        $('#id_familia').trigger('change');
                    }
                    
                    // Configurar unidad
                    if (data.id_unidad) {
                        $('#id_unidad').val(data.id_unidad);
                        $('#id_unidad').trigger('change');
                    }
                    
                    // Configurar impuesto
                    if (data.id_impuesto) {
                        $('#id_impuesto').val(data.id_impuesto);
                    }
                    
                    // Configurar permitir descuentos
                    $('#permitir_descuentos_articulo').prop('checked', data.permitir_descuentos_articulo != 0);
                    
                    // Configurar coeficiente
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
                    
                    // Capturar valores originales
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

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR ARTÍCULO
    //*****************************************************/

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
        var permitir_descuentos_articuloR = $('#permitir_descuentos_articulo').is(':checked') ? 1 : 0;
        
        // Obtener impuesto
        var id_impuestoR = $('#id_impuesto').val() || null;
        
        // El estado
        var activo_articuloR;
        if (id_articuloR) {
            activo_articuloR = $('#activo_articulo').is(':checked') ? 1 : 0;
        } else {
            activo_articuloR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar artículo primero
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
            activo_articuloR,
            permitir_descuentos_articuloR,
            id_impuestoR
        );
    });

    function verificarArticuloExistente(id_articulo, codigo_articulo, nombre_articulo, name_articulo, id_familia, id_unidad, precio_alquiler_articulo, coeficiente_articulo, es_kit_articulo, control_total_articulo, no_facturar_articulo, notas_presupuesto_articulo, notes_budget_articulo, orden_obs_articulo, observaciones_articulo, activo_articulo, permitir_descuentos_articulo, id_impuesto) {
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
                    guardarArticulo(id_articulo, codigo_articulo, nombre_articulo, name_articulo, id_familia, id_unidad, precio_alquiler_articulo, coeficiente_articulo, es_kit_articulo, control_total_articulo, no_facturar_articulo, notas_presupuesto_articulo, notes_budget_articulo, orden_obs_articulo, observaciones_articulo, activo_articulo, permitir_descuentos_articulo, id_impuesto);
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

    function guardarArticulo(id_articulo, codigo_articulo, nombre_articulo, name_articulo, id_familia, id_unidad, precio_alquiler_articulo, coeficiente_articulo, es_kit_articulo, control_total_articulo, no_facturar_articulo, notas_presupuesto_articulo, notes_budget_articulo, orden_obs_articulo, observaciones_articulo, activo_articulo, permitir_descuentos_articulo, id_impuesto) {
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
        formData.append('permitir_descuentos_articulo', permitir_descuentos_articulo);
        formData.append('id_impuesto', id_impuesto || '');
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
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    formSaved = true;
                    
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

    /////////////////////////////////////////
    //   FUNCIONES DE UTILIDAD            //
    ///////////////////////////////////////

    // Variables para controlar si el formulario ha sido modificado
    var formOriginalValues = {};
    var formSaved = false;
    
    // Capturar valores originales
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
    
    // Verificar si el formulario ha cambiado
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
    
    // Advertencia antes de salir
    window.addEventListener('beforeunload', function (e) {
        if (!formSaved && hasFormChanged()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    /////////////////////////////////////////
    //   FIN FUNCIONES DE UTILIDAD        //
    ///////////////////////////////////////

    /////////////////////////////////////////
    //   INICIALIZACIÓN DEL FORMULARIO    //
    ///////////////////////////////////////
    
    // Cargar familias, unidades de medida e impuestos
    cargarFamilias();
    cargarUnidadesMedida();
    cargarImpuestos();

    // Inicialización según el modo
    const urlParamsInit = new URLSearchParams(window.location.search);
    const idArticuloInit = urlParamsInit.get('id');
    const modoInit = urlParamsInit.get('modo') || 'nuevo';
    
    if (modoInit === 'editar' && idArticuloInit) {
        setTimeout(function() {
            cargarDatosArticulo(idArticuloInit);
        }, 500);
    } else {
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

}); // de document.ready

// Función global para cargar datos
function cargarDatosArticulo(idArticulo) {
    console.log('Función global - Cargando datos de artículo ID:', idArticulo);
}

// Función para mostrar vista previa por defecto
function showDefaultImagePreview() {
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview) {
        imagePreview.innerHTML = `
            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        `;
    }
}

// Función para mostrar imagen existente
function showExistingImage(imagePath) {
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview && imagePath) {
        const fullPath = '../../public/img/articulo/' + imagePath;
        imagePreview.innerHTML = `
            <img src="${fullPath}" alt="Imagen actual" style="max-width: 100%; max-height: 100px; border-radius: 4px;" 
                 onerror="this.onerror=null; showDefaultImagePreview();">
            <p class="text-muted mt-1 mb-0 small">Imagen actual: ${imagePath}</p>
        `;
    }
}
