$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formObservacion', {
        codigo_obs_general: {
            required: true
        },
        titulo_obs_general: {
            required: true
        },
        texto_obs_general: {
            required: true
        }
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para cargar datos de observación para edición
    function cargarDatosObservacion(idObsGeneral) {
        console.log('Cargando datos de observación ID:', idObsGeneral);
        
        $.ajax({
            url: "../../controller/observaciones.php?op=mostrar",
            type: "POST",
            data: { id_obs_general: idObsGeneral },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_obs_general').val(data.id_obs_general);
                    $('#codigo_obs_general').val(data.codigo_obs_general);
                    $('#titulo_obs_general').val(data.titulo_obs_general);
                    $('#title_obs_general').val(data.title_obs_general || '');
                    $('#texto_obs_general').val(data.texto_obs_general);
                    $('#text_obs_general').val(data.text_obs_general || '');
                    $('#orden_obs_general').val(data.orden_obs_general);
                    $('#tipo_obs_general').val(data.tipo_obs_general);
                    
                    // Configurar el switch de obligatoria
                    $('#obligatoria_obs_general').prop('checked', data.obligatoria_obs_general == 1);
                    $('#obligatoria-text').text(data.obligatoria_obs_general == 1 ? 'Sí' : 'No');
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_obs_general').prop('checked', data.activo_obs_general == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_obs_general == 1) {
                        $('#estado-text').text('Observación Activa').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Observación Inactiva').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_obs_general').focus();
                    
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
                toastr.error('Error al obtener datos de la observación');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idObsGeneral = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idObsGeneral) {
        cargarDatosObservacion(idObsGeneral);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_obs_general').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR OBSERVACIÓN
    //*****************************************************/

    $(document).on('click', '#btnSalvarObservacion', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_obs_generalR = $('#id_obs_general').val().trim();
        var codigo_obs_generalR = $('#codigo_obs_general').val().trim();
        var titulo_obs_generalR = $('#titulo_obs_general').val().trim();
        var title_obs_generalR = $('#title_obs_general').val().trim();
        var texto_obs_generalR = $('#texto_obs_general').val().trim();
        var text_obs_generalR = $('#text_obs_general').val().trim();
        var orden_obs_generalR = $('#orden_obs_general').val().trim() || 0;
        var tipo_obs_generalR = $('#tipo_obs_general').val();
        var obligatoria_obs_generalR = $('#obligatoria_obs_general').is(':checked') ? 1 : 0;
        
        // El estado siempre será activo para nuevas observaciones, o mantener el actual para edición
        var activo_obs_generalR;
        if (id_obs_generalR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_obs_generalR = $('#activo_obs_general').is(':checked') ? 1 : 0;
        } else {
            // Nueva observación: siempre activa
            activo_obs_generalR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar observación primero
        verificarObservacionExistente(id_obs_generalR, codigo_obs_generalR, titulo_obs_generalR, title_obs_generalR, texto_obs_generalR, text_obs_generalR, orden_obs_generalR, tipo_obs_generalR, obligatoria_obs_generalR, activo_obs_generalR);
    });

    function verificarObservacionExistente(id_obs_general, codigo_obs_general, titulo_obs_general, title_obs_general, texto_obs_general, text_obs_general, orden_obs_general, tipo_obs_general, obligatoria_obs_general, activo_obs_general) {
        $.ajax({
            url: "../../controller/observaciones.php?op=verificarObservaciones",
            type: "GET",
            data: { 
                codigo_obs_general: codigo_obs_general,
                id_obs_general: id_obs_general 
            },
            dataType: "json",
            success: function(response) {
                if (!response.success) {
                    toastr.warning(response.message || "Error al verificar la observación.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorObservacionExistente(codigo_obs_general);
                } else {
                    guardarObservacion(id_obs_general, codigo_obs_general, titulo_obs_general, title_obs_general, texto_obs_general, text_obs_general, orden_obs_general, tipo_obs_general, obligatoria_obs_general, activo_obs_general);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar la observación. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorObservacionExistente(codigo_obs_general) {
        console.log("Observación duplicada detectada:", codigo_obs_general);
        Swal.fire({
            title: 'Código de observación duplicado',
            text: 'La observación "' + codigo_obs_general + '" ya existe. Por favor, elija otro código.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarObservacion(id_obs_general, codigo_obs_general, titulo_obs_general, title_obs_general, texto_obs_general, text_obs_general, orden_obs_general, tipo_obs_general, obligatoria_obs_general, activo_obs_general) {
        // Mostrar indicador de carga
        $('#btnSalvarObservacion').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear datos para enviar
        var formData = {
            'codigo_obs_general': codigo_obs_general,
            'titulo_obs_general': titulo_obs_general,
            'title_obs_general': title_obs_general,
            'texto_obs_general': texto_obs_general,
            'text_obs_general': text_obs_general,
            'orden_obs_general': orden_obs_general,
            'tipo_obs_general': tipo_obs_general,
            'obligatoria_obs_general': obligatoria_obs_general,
            'activo_obs_general': activo_obs_general
        };
        
        if (id_obs_general) {
            formData['id_obs_general'] = id_obs_general;
        }

        $.ajax({
            url: "../../controller/observaciones.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Observación guardada correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar la observación");
                    // Restaurar botón
                    $('#btnSalvarObservacion').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Observación');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar la observación. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarObservacion').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Observación');
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
            codigo_obs_general: $('#codigo_obs_general').val(),
            titulo_obs_general: $('#titulo_obs_general').val(),
            title_obs_general: $('#title_obs_general').val(),
            texto_obs_general: $('#texto_obs_general').val(),
            text_obs_general: $('#text_obs_general').val(),
            orden_obs_general: $('#orden_obs_general').val(),
            tipo_obs_general: $('#tipo_obs_general').val(),
            obligatoria_obs_general: $('#obligatoria_obs_general').is(':checked')
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#codigo_obs_general').val() !== formOriginalValues.codigo_obs_general ||
            $('#titulo_obs_general').val() !== formOriginalValues.titulo_obs_general ||
            $('#title_obs_general').val() !== formOriginalValues.title_obs_general ||
            $('#texto_obs_general').val() !== formOriginalValues.texto_obs_general ||
            $('#text_obs_general').val() !== formOriginalValues.text_obs_general ||
            $('#orden_obs_general').val() !== formOriginalValues.orden_obs_general ||
            $('#tipo_obs_general').val() !== formOriginalValues.tipo_obs_general ||
            $('#obligatoria_obs_general').is(':checked') !== formOriginalValues.obligatoria_obs_general
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

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosObservacion(idObsGeneral) {
    console.log('Función global - Cargando datos de observación ID:', idObsGeneral);
    // Esta función ya está implementada dentro del document.ready
}
