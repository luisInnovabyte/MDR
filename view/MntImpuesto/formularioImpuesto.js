$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formImpuesto', {
        tipo_impuesto: {
            required: true
        },
        tasa_impuesto: {
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

    // Función para cargar datos de impuesto para edición
    function cargarDatosImpuesto(idImpuesto) {
        console.log('Cargando datos de impuesto ID:', idImpuesto);
        
        $.ajax({
            url: "../../controller/impuesto.php?op=mostrar",
            type: "POST",
            data: { id_impuesto: idImpuesto },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_impuesto').val(data.id_impuesto);
                    $('#tipo_impuesto').val(data.tipo_impuesto);
                    $('#tasa_impuesto').val(data.tasa_impuesto);
                    $('#descr_impuesto').val(data.descr_impuesto);
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_impuesto').prop('checked', data.activo_impuesto == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_impuesto == 1) {
                        $('#estado-text').text('Impuesto Activo').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Impuesto Inactivo').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar contador de caracteres si existe descripción
                    if (data.descr_impuesto) {
                        $('#char-count').text(data.descr_impuesto.length);
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#tipo_impuesto').focus();
                    
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
                toastr.error('Error al obtener datos del impuesto');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idImpuesto = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idImpuesto) {
        cargarDatosImpuesto(idImpuesto);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#tipo_impuesto').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR IMPUESTO
    //*****************************************************/

    $(document).on('click', '#btnSalvarImpuesto', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_impuestoR = $('#id_impuesto').val().trim();
        var tipo_impuestoR = $('#tipo_impuesto').val().trim();
        var tasa_impuestoR = $('#tasa_impuesto').val().trim();
        var descr_impuestoR = $('#descr_impuesto').val().trim();
        
        // El estado siempre será activo para nuevos impuestos, o mantener el actual para edición
        var activo_impuestoR;
        if (id_impuestoR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_impuestoR = $('#activo_impuesto').is(':checked') ? 1 : 0;
        } else {
            // Nuevo impuesto: siempre activo
            activo_impuestoR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar impuesto primero
        verificarImpuestoExistente(id_impuestoR, tipo_impuestoR, tasa_impuestoR, descr_impuestoR, activo_impuestoR);
    });

    function verificarImpuestoExistente(id_impuesto, tipo_impuesto, tasa_impuesto, descr_impuesto, activo_impuesto) {
        $.ajax({
            url: "../../controller/impuesto.php?op=verificarImpuesto",
            type: "GET",
            data: { 
                tipo_impuesto: tipo_impuesto,
                tasa_impuesto: tasa_impuesto,
                id_impuesto: id_impuesto 
            },
            dataType: "json",
            success: function(response) {
                if (!response.success) {
                    toastr.warning(response.message || "Error al verificar el impuesto.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorImpuestoExistente(tipo_impuesto);
                } else {
                    guardarImpuesto(id_impuesto, tipo_impuesto, tasa_impuesto, descr_impuesto, activo_impuesto);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar el impuesto. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorImpuestoExistente(tipo_impuesto) {
        console.log("Impuesto duplicado detectado:", tipo_impuesto);
        Swal.fire({
            title: 'Tipo de impuesto duplicado',
            text: 'El impuesto "' + tipo_impuesto + '" ya existe. Por favor, elija otro tipo.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarImpuesto(id_impuesto, tipo_impuesto, tasa_impuesto, descr_impuesto, activo_impuesto) {
        // Mostrar indicador de carga
        $('#btnSalvarImpuesto').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear datos para enviar
        var formData = {
            'tipo_impuesto': tipo_impuesto,
            'tasa_impuesto': tasa_impuesto,
            'descr_impuesto': descr_impuesto,
            'activo_impuesto': activo_impuesto
        };
        
        if (id_impuesto) {
            formData['id_impuesto'] = id_impuesto;
        }

        $.ajax({
            url: "../../controller/impuesto.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Impuesto guardado correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el impuesto");
                    // Restaurar botón
                    $('#btnSalvarImpuesto').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Impuesto');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar el impuesto. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarImpuesto').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Impuesto');
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
            tipo_impuesto: $('#tipo_impuesto').val(),
            tasa_impuesto: $('#tasa_impuesto').val(),
            descr_impuesto: $('#descr_impuesto').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#tipo_impuesto').val() !== formOriginalValues.tipo_impuesto ||
            $('#tasa_impuesto').val() !== formOriginalValues.tasa_impuesto ||
            $('#descr_impuesto').val() !== formOriginalValues.descr_impuesto
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
function cargarDatosImpuesto(idImpuesto) {
    console.log('Función global - Cargando datos de impuesto ID:', idImpuesto);
    // Esta función ya está implementada dentro del document.ready
}