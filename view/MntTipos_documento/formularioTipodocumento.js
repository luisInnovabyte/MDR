$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formTipodocumento', {
        codigo_tipo_documento: {
            required: true
        },
        nombre_tipo_documento: {
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

    // Función para cargar datos de tipo de documento para edición
    function cargarDatosTipodocumento(idTipodocumento) {
        console.log('Cargando datos de tipo de documento ID:', idTipodocumento);
        
        $.ajax({
            url: "../../controller/tipodocumento.php?op=mostrar",
            type: "POST",
            data: { id_tipo_documento: idTipodocumento },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_tipo_documento').val(data.id_tipo_documento);
                    $('#codigo_tipo_documento').val(data.codigo_tipo_documento);
                    $('#nombre_tipo_documento').val(data.nombre_tipo_documento);
                    $('#descripcion_tipo_documento').val(data.descripcion_tipo_documento);
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_tipo_documento').prop('checked', data.activo_tipo_documento == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_tipo_documento == 1) {
                        $('#estado-text').text('Tipo de Documento Activo').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Tipo de Documento Inactivo').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar contador de caracteres si existe descripción
                    if (data.descripcion_tipo_documento) {
                        $('#char-count').text(data.descripcion_tipo_documento.length);
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_tipo_documento').focus();
                    
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
                toastr.error('Error al obtener datos del tipo de documento');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idTipodocumento = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idTipodocumento) {
        cargarDatosTipodocumento(idTipodocumento);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_tipo_documento').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR TIPO DE DOCUMENTO
    //*****************************************************/

    $(document).on('click', '#btnSalvarTipodocumento', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_tipo_documentoR = $('#id_tipo_documento').val().trim();
        var codigo_tipo_documentoR = $('#codigo_tipo_documento').val().trim();
        var nombre_tipo_documentoR = $('#nombre_tipo_documento').val().trim();
        var descripcion_tipo_documentoR = $('#descripcion_tipo_documento').val().trim();
        
        // El estado siempre será activo para nuevos tipos, o mantener el actual para edición
        var activo_tipo_documentoR;
        if (id_tipo_documentoR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_tipo_documentoR = $('#activo_tipo_documento').is(':checked') ? 1 : 0;
        } else {
            // Nuevo tipo de documento: siempre activo
            activo_tipo_documentoR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar tipo de documento primero
        verificarTipodocumentoExistente(id_tipo_documentoR, codigo_tipo_documentoR, nombre_tipo_documentoR, descripcion_tipo_documentoR, activo_tipo_documentoR);
    });

    function verificarTipodocumentoExistente(id_tipo_documento, codigo_tipo_documento, nombre_tipo_documento, descripcion_tipo_documento, activo_tipo_documento) {
        $.ajax({
            url: "../../controller/tipodocumento.php?op=verificarTipodocumento",
            type: "GET",
            data: { 
                codigo_tipo_documento: codigo_tipo_documento,
                id_tipo_documento: id_tipo_documento 
            },
            dataType: "json",
            success: function(response) {
                if (!response.success) {
                    toastr.warning(response.message || "Error al verificar el tipo de documento.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorTipodocumentoExistente(codigo_tipo_documento);
                } else {
                    guardarTipodocumento(id_tipo_documento, codigo_tipo_documento, nombre_tipo_documento, descripcion_tipo_documento, activo_tipo_documento);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar el tipo de documento. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorTipodocumentoExistente(codigo_tipo_documento) {
        console.log("Tipo de documento duplicado detectado:", codigo_tipo_documento);
        Swal.fire({
            title: 'Código de tipo de documento duplicado',
            text: 'El código "' + codigo_tipo_documento + '" ya existe. Por favor, elija otro código.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarTipodocumento(id_tipo_documento, codigo_tipo_documento, nombre_tipo_documento, descripcion_tipo_documento, activo_tipo_documento) {
        // Mostrar indicador de carga
        $('#btnSalvarTipodocumento').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear datos para enviar
        var formData = {
            'codigo_tipo_documento': codigo_tipo_documento,
            'nombre_tipo_documento': nombre_tipo_documento,
            'descripcion_tipo_documento': descripcion_tipo_documento,
            'activo_tipo_documento': activo_tipo_documento
        };
        
        if (id_tipo_documento) {
            formData['id_tipo_documento'] = id_tipo_documento;
        }

        $.ajax({
            url: "../../controller/tipodocumento.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Tipo de documento guardado correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el tipo de documento");
                    // Restaurar botón
                    $('#btnSalvarTipodocumento').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Tipo de Documento');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar el tipo de documento. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarTipodocumento').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Tipo de Documento');
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
            codigo_tipo_documento: $('#codigo_tipo_documento').val(),
            nombre_tipo_documento: $('#nombre_tipo_documento').val(),
            descripcion_tipo_documento: $('#descripcion_tipo_documento').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#codigo_tipo_documento').val() !== formOriginalValues.codigo_tipo_documento ||
            $('#nombre_tipo_documento').val() !== formOriginalValues.nombre_tipo_documento ||
            $('#descripcion_tipo_documento').val() !== formOriginalValues.descripcion_tipo_documento
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
function cargarDatosTipodocumento(idTipodocumento) {
    console.log('Función global - Cargando datos de tipo de documento ID:', idTipodocumento);
    // Esta función ya está implementada dentro del document.ready
}
