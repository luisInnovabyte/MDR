$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formFamilia', {
        codigo_familia: {
            required: true
        },
        nombre_familia: {
            required: true
        },
        name_familia: {
            required: true
        }
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Cargar grupos de artículo disponibles
    function cargarGruposArticulo() {
        $.ajax({
            url: "../../controller/grupo_articulo.php?op=listarDisponibles",
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (Array.isArray(data)) {
                    var select = $('#id_grupo');
                    select.empty();
                    select.append('<option value="">Seleccionar grupo de artículo...</option>');
                    
                    data.forEach(function(grupo) {
                        var displayText = grupo.codigo_grupo + ' - ' + grupo.nombre_grupo;
                        select.append('<option value="' + grupo.id_grupo + '" data-descripcion="' + (grupo.descripcion_grupo || '') + '">' + displayText + '</option>');
                    });
                } else {
                    console.error('Error: Respuesta no válida del servidor para grupos de artículo');
                    toastr.warning('No se pudieron cargar los grupos de artículo');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar grupos de artículo:', error);
                toastr.error('Error al cargar los grupos de artículo');
            }
        });
    }

    // Cargar unidades de medida disponibles
    function cargarUnidadesMedida() {
        $.ajax({
            url: "../../controller/unidad_medida.php?op=listarDisponibles",
            type: "GET",
            dataType: "json",
            success: function(data) {
                if (Array.isArray(data)) {
                    var select = $('#id_unidad_familia');
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
                    console.error('Error: Respuesta no válida del servidor para unidades de medida');
                    toastr.warning('No se pudieron cargar las unidades de medida');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar unidades de medida:', error);
                toastr.error('Error al cargar las unidades de medida');
            }
        });
    }

    // Manejar cambio en el select de grupo de artículo
    $('#id_grupo').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var descripcion = selectedOption.data('descripcion');
        
        if (descripcion && descripcion.trim() !== '') {
            $('#grupo-descr-text').text(descripcion);
            $('#grupo-descripcion').show();
        } else {
            $('#grupo-descripcion').hide();
        }
    });

    // Manejar cambio en el select de unidad de medida
    $('#id_unidad_familia').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var descripcion = selectedOption.data('descripcion');
        
        if (descripcion && descripcion.trim() !== '') {
            $('#unidad-descr-text').text(descripcion);
            $('#unidad-descripcion').show();
        } else {
            $('#unidad-descripcion').hide();
        }
    });

    // Manejar cambio en el switch de permite descuento
    $('#permite_descuento_familia').on('change', function() {
        if ($(this).is(':checked')) {
            $('#descuento-text').text('Permite descuentos').removeClass('text-danger').addClass('text-success');
        } else {
            $('#descuento-text').text('No permite descuentos').removeClass('text-success').addClass('text-danger');
        }
    });

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para cargar datos de familia para edición
    function cargarDatosFamilia(idFamilia) {
        console.log('Cargando datos de familia ID:', idFamilia);
        
        $.ajax({
            url: "../../controller/familia_unidad.php?op=mostrar",
            type: "POST",
            data: { id_familia: idFamilia },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_familia').val(data.id_familia);
                    $('#codigo_familia').val(data.codigo_familia);
                    $('#nombre_familia').val(data.nombre_familia);
                    $('#name_familia').val(data.name_familia);
                    $('#descr_familia').val(data.descr_familia);
                    $('#observaciones_presupuesto_familia').val(data.observaciones_presupuesto_familia || '');
                    $('#observations_budget_familia').val(data.observations_budget_familia || '');
                    $('#orden_obs_familia').val(data.orden_obs_familia || 100);
                    
                    // Configurar grupo de artículo
                    if (data.id_grupo) {
                        $('#id_grupo').val(data.id_grupo);
                        // Trigger change para mostrar descripción
                        $('#id_grupo').trigger('change');
                    }
                    
                    // Configurar unidad de familia
                    if (data.id_unidad_familia) {
                        $('#id_unidad_familia').val(data.id_unidad_familia);
                        // Trigger change para mostrar descripción
                        $('#id_unidad_familia').trigger('change');
                    }
                    
                    // Configurar imagen actual
                    if (data.imagen_familia) {
                        $('#imagen_actual').val(data.imagen_familia);
                        // Mostrar imagen existente
                        showExistingImage(data.imagen_familia);
                    }
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_familia').prop('checked', data.activo_familia == 1);
                    
                    // Configurar el checkbox de coeficiente
                    $('#coeficiente_familia').prop('checked', data.coeficiente_familia == 1);
                    
                    // Configurar el checkbox de permite descuento
                    $('#permite_descuento_familia').prop('checked', data.permite_descuento_familia !== 0);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_familia == 1) {
                        $('#estado-text').text('Familia Activa').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Familia Inactiva').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar texto del coeficiente según el valor actual
                    if (data.coeficiente_familia == 1) {
                        $('#coeficiente-text').text('Permite coeficientes').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#coeficiente-text').text('No permite coeficientes').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar texto del permite descuento según el valor actual
                    if (data.permite_descuento_familia !== 0) {
                        $('#descuento-text').text('Permite descuentos').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#descuento-text').text('No permite descuentos').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar contador de caracteres si existe descripción
                    if (data.descr_familia) {
                        $('#char-count').text(data.descr_familia.length);
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_familia').focus();
                    
                } catch (e) {
                    console.error('Error al procesar datos:', e);
                    toastr.error('Error al cargar datos para edición');
                    // Redirigir al listado si hay error
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al obtener datos de la familia');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idFamilia = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idFamilia) {
        cargarDatosFamilia(idFamilia);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_familia').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR FAMILIA
    //*****************************************************/

    $(document).on('click', '#btnSalvarFamilia', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_familiaR = $('#id_familia').val().trim();
        var codigo_familiaR = $('#codigo_familia').val().trim();
        var nombre_familiaR = $('#nombre_familia').val().trim();
        var name_familiaR = $('#name_familia').val().trim();
        var descr_familiaR = $('#descr_familia').val().trim();
        var id_grupo_R = $('#id_grupo').val() || null;
        var id_unidad_familiaR = $('#id_unidad_familia').val() || null;
        var observaciones_presupuesto_familiaR = $('#observaciones_presupuesto_familia').val().trim();
        var observations_budget_familiaR = $('#observations_budget_familia').val().trim();
        var orden_obs_familiaR = $('#orden_obs_familia').val() || 100;
        
        // El estado siempre será activo para nuevas familias, o mantener el actual para edición
        var activo_familiaR;
        if (id_familiaR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_familiaR = $('#activo_familia').is(':checked') ? 1 : 0;
        } else {
            // Nueva familia: siempre activo
            activo_familiaR = 1;
        }
        
        // El coeficiente se toma del checkbox
        var coeficiente_familiaR = $('#coeficiente_familia').is(':checked') ? 1 : 0;
        
        // El permite descuento se toma del checkbox
        var permite_descuento_familiaR = $('#permite_descuento_familia').is(':checked') ? 1 : 0;

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar familia primero
        verificarFamiliaExistente(id_familiaR, codigo_familiaR, name_familiaR, nombre_familiaR, descr_familiaR, activo_familiaR, id_grupo_R, id_unidad_familiaR, coeficiente_familiaR, observaciones_presupuesto_familiaR, observations_budget_familiaR, orden_obs_familiaR, permite_descuento_familiaR);
    });

    function verificarFamiliaExistente(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia, activo_familia, id_grupo, id_unidad_familia, coeficiente_familia, observaciones_presupuesto_familia, observations_budget_familia, orden_obs_familia, permite_descuento_familia) {
        $.ajax({
            url: "../../controller/familia_unidad.php?op=verificarFamilia",
            type: "GET",
            data: { 
                nombre_familia: nombre_familia,
                name_familia: name_familia, 
                codigo_familia: codigo_familia,
                id_familia: id_familia 
            },
            dataType: "json",
            success: function(response) {
                if (!response.success) {
                    toastr.warning(response.message || "Error al verificar la familia.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorFamiliaExistente(nombre_familia);
                } else {
                    guardarFamilia(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia, activo_familia, id_grupo, id_unidad_familia, coeficiente_familia, observaciones_presupuesto_familia, observations_budget_familia, orden_obs_familia, permite_descuento_familia);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar la familia. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorFamiliaExistente(nombre_familia) {
        console.log("Familia duplicada detectada:", nombre_familia);
        Swal.fire({
            title: 'Nombre de familia duplicado',
            text: 'La familia "' + nombre_familia + '" ya existe. Por favor, elija otro nombre.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarFamilia(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia, activo_familia, id_grupo, id_unidad_familia, coeficiente_familia, observaciones_presupuesto_familia, observations_budget_familia, orden_obs_familia, permite_descuento_familia) {
        // Mostrar indicador de carga
        $('#btnSalvarFamilia').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear FormData para manejar archivos
        var formData = new FormData();
        formData.append('codigo_familia', codigo_familia);
        formData.append('nombre_familia', nombre_familia);
        formData.append('name_familia', name_familia);
        formData.append('descr_familia', descr_familia);
        formData.append('activo_familia', activo_familia);
        formData.append('coeficiente_familia', coeficiente_familia);
        formData.append('permite_descuento_familia', permite_descuento_familia);
        formData.append('id_grupo', id_grupo || '');
        formData.append('id_unidad_familia', id_unidad_familia || '');
        formData.append('observaciones_presupuesto_familia', observaciones_presupuesto_familia);
        formData.append('observations_budget_familia', observations_budget_familia);
        formData.append('orden_obs_familia', orden_obs_familia);
        
        // Log para depuración
        console.log('Datos a enviar:');
        console.log('observaciones_presupuesto_familia:', observaciones_presupuesto_familia);
        console.log('observations_budget_familia:', observations_budget_familia);
        
        if (id_familia) {
            formData.append('id_familia', id_familia);
            // Agregar imagen actual para mantenerla si no se cambia
            formData.append('imagen_actual', $('#imagen_actual').val());
        }
        
        // Agregar archivo de imagen si se seleccionó uno
        const imagenFile = $('#imagen_familia')[0].files[0];
        if (imagenFile) {
            formData.append('imagen_familia', imagenFile);
        }

        $.ajax({
            url: "../../controller/familia_unidad.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false, // No procesar los datos
            contentType: false, // No establecer contentType
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Familia guardada correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar la familia");
                    // Restaurar botón
                    $('#btnSalvarFamilia').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Familia');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar la familia. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarFamilia').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Familia');
            }
        });
    }

    /////////////////////////////////////////
    //   FUNCIONES DE UTILIDAD            //
    ///////////////////////////////////////

    // Variables para controlar si el formulario ha sido modificado
    var formOriginalValues = {};
    var formSaved = false;
    
    // Capturar valores originales después de cargar datos
    function captureOriginalValues() {
        formOriginalValues = {
            codigo_familia: $('#codigo_familia').val(),
            nombre_familia: $('#nombre_familia').val(),
            name_familia: $('#name_familia').val(),
            descr_familia: $('#descr_familia').val(),
            imagen_actual: $('#imagen_actual').val(),
            id_grupo: $('#id_grupo').val(),
            id_unidad_familia: $('#id_unidad_familia').val(),
            coeficiente_familia: $('#coeficiente_familia').is(':checked'),
            permite_descuento_familia: $('#permite_descuento_familia').is(':checked'),
            observaciones_presupuesto_familia: $('#observaciones_presupuesto_familia').val(),
            observations_budget_familia: $('#observations_budget_familia').val(),
            orden_obs_familia: $('#orden_obs_familia').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        // Verificar si se ha seleccionado una nueva imagen
        const hasNewImage = $('#imagen_familia')[0].files && $('#imagen_familia')[0].files.length > 0;
        
        return (
            $('#codigo_familia').val() !== formOriginalValues.codigo_familia ||
            $('#nombre_familia').val() !== formOriginalValues.nombre_familia ||
            $('#name_familia').val() !== formOriginalValues.name_familia ||
            $('#descr_familia').val() !== formOriginalValues.descr_familia ||
            $('#id_grupo').val() !== formOriginalValues.id_grupo ||
            $('#id_unidad_familia').val() !== formOriginalValues.id_unidad_familia ||
            $('#coeficiente_familia').is(':checked') !== formOriginalValues.coeficiente_familia ||
            $('#permite_descuento_familia').is(':checked') !== formOriginalValues.permite_descuento_familia ||
            $('#observaciones_presupuesto_familia').val() !== formOriginalValues.observaciones_presupuesto_familia ||
            $('#observations_budget_familia').val() !== formOriginalValues.observations_budget_familia ||
            $('#orden_obs_familia').val() !== formOriginalValues.orden_obs_familia ||
            hasNewImage
        );
    }
    
    // Función para mostrar mensajes de confirmación antes de salir
    window.addEventListener('beforeunload', function (e) {
        // Solo mostrar si hay cambios reales en el formulario y no se ha guardado
        if (!formSaved && hasFormChanged()) {
            e.preventDefault();
            e.returnValue = '';
        }
    });

    // Función para marcar el formulario como guardado
    function markFormAsSaved() {
        formSaved = true;
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE UTILIDAD        //
    ///////////////////////////////////////

    /////////////////////////////////////////
    //   INICIALIZACIÓN DEL FORMULARIO    //
    ///////////////////////////////////////
    
    // Cargar grupos de artículo y unidades de medida al cargar la página
    cargarGruposArticulo();
    cargarUnidadesMedida();

    // Inicialización según el modo (nuevo/editar)
    const urlParamsInit = new URLSearchParams(window.location.search);
    const idFamiliaInit = urlParamsInit.get('id');
    const modoInit = urlParamsInit.get('modo') || 'nuevo';
    
    if (modoInit === 'editar' && idFamiliaInit) {
        // Cargar datos después de cargar las unidades de medida
        setTimeout(function() {
            cargarDatosFamilia(idFamiliaInit);
        }, 500);
    } else {
        // Capturar valores originales para formulario nuevo
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosFamilia(idFamilia) {
    console.log('Función global - Cargando datos de familia ID:', idFamilia);
    // Esta función ya está implementada dentro del document.ready
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
        const fullPath = '../../public/img/familia/' + imagePath;
        imagePreview.innerHTML = `
            <img src="${fullPath}" alt="Imagen actual" style="max-width: 100%; max-height: 100px; border-radius: 4px;" 
                 onerror="this.onerror=null; showDefaultImagePreview();">
            <p class="text-muted mt-1 mb-0 small">Imagen actual: ${imagePath}</p>
        `;
    }
}