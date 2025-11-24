$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formMetodoPago', {
        codigo_metodo_pago: {
            required: true
        },
        nombre_metodo_pago: {
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

    // Función para cargar datos de método de pago para edición
    function cargarDatosMetodoPago(idMetodoPago) {
        console.log('Cargando datos de método de pago ID:', idMetodoPago);
        
        $.ajax({
            url: "../../controller/metodospago.php?op=mostrar",
            type: "POST",
            data: { id_metodo_pago: idMetodoPago },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_metodo_pago').val(data.id_metodo_pago);
                    $('#codigo_metodo_pago').val(data.codigo_metodo_pago);
                    $('#nombre_metodo_pago').val(data.nombre_metodo_pago);
                    $('#observaciones_metodo_pago').val(data.observaciones_metodo_pago);
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_metodo_pago').prop('checked', data.activo_metodo_pago == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_metodo_pago == 1) {
                        $('#estado-text').text('Método Activo').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Método Inactivo').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_metodo_pago').focus();
                    
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
                toastr.error('Error al obtener datos del método de pago');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idMetodoPago = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idMetodoPago) {
        cargarDatosMetodoPago(idMetodoPago);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_metodo_pago').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR MÉTODO PAGO
    //*****************************************************/

    $(document).on('click', '#btnSalvarMetodoPago', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_metodo_pagoR = $('#id_metodo_pago').val().trim();
        var codigo_metodo_pagoR = $('#codigo_metodo_pago').val().trim();
        var nombre_metodo_pagoR = $('#nombre_metodo_pago').val().trim();
        var observaciones_metodo_pagoR = $('#observaciones_metodo_pago').val().trim();
        
        // El estado siempre será activo para nuevos métodos, o mantener el actual para edición
        var activo_metodo_pagoR;
        if (id_metodo_pagoR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_metodo_pagoR = $('#activo_metodo_pago').is(':checked') ? 1 : 0;
        } else {
            // Nuevo método de pago: siempre activo
            activo_metodo_pagoR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar método de pago primero
        verificarMetodoPagoExistente(id_metodo_pagoR, codigo_metodo_pagoR, nombre_metodo_pagoR, observaciones_metodo_pagoR, activo_metodo_pagoR);
    });

    function verificarMetodoPagoExistente(id_metodo_pago, codigo_metodo_pago, nombre_metodo_pago, observaciones_metodo_pago, activo_metodo_pago) {
        $.ajax({
            url: "../../controller/metodospago.php?op=verificarMetodoPago",
            type: "GET",
            data: { 
                codigo_metodo_pago: codigo_metodo_pago,
                nombre_metodo_pago: nombre_metodo_pago,
                id_metodo_pago: id_metodo_pago 
            },
            dataType: "json",
            success: function(response) {
                if (!response.success) {
                    toastr.warning(response.message || "Error al verificar el método de pago.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorMetodoPagoExistente(codigo_metodo_pago, nombre_metodo_pago);
                } else {
                    guardarMetodoPago(id_metodo_pago, codigo_metodo_pago, nombre_metodo_pago, observaciones_metodo_pago, activo_metodo_pago);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar el método de pago. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorMetodoPagoExistente(codigo_metodo_pago, nombre_metodo_pago) {
        console.log("Método de pago duplicado detectado:", codigo_metodo_pago, nombre_metodo_pago);
        Swal.fire({
            title: 'Método de pago duplicado',
            text: 'El método de pago con código "' + codigo_metodo_pago + '" o nombre "' + nombre_metodo_pago + '" ya existe. Por favor, elija otro.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarMetodoPago(id_metodo_pago, codigo_metodo_pago, nombre_metodo_pago, observaciones_metodo_pago, activo_metodo_pago) {
        // Mostrar indicador de carga
        $('#btnSalvarMetodoPago').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear datos para enviar
        var formData = {
            'codigo_metodo_pago': codigo_metodo_pago,
            'nombre_metodo_pago': nombre_metodo_pago,
            'observaciones_metodo_pago': observaciones_metodo_pago,
            'activo_metodo_pago': activo_metodo_pago
        };
        
        if (id_metodo_pago) {
            formData['id_metodo_pago'] = id_metodo_pago;
        }

        $.ajax({
            url: "../../controller/metodospago.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Método de pago guardado correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el método de pago");
                    // Restaurar botón
                    $('#btnSalvarMetodoPago').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Método de Pago');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar el método de pago. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarMetodoPago').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Método de Pago');
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
            codigo_metodo_pago: $('#codigo_metodo_pago').val(),
            nombre_metodo_pago: $('#nombre_metodo_pago').val(),
            observaciones_metodo_pago: $('#observaciones_metodo_pago').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#codigo_metodo_pago').val() !== formOriginalValues.codigo_metodo_pago ||
            $('#nombre_metodo_pago').val() !== formOriginalValues.nombre_metodo_pago ||
            $('#observaciones_metodo_pago').val() !== formOriginalValues.observaciones_metodo_pago
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
function cargarDatosMetodoPago(idMetodoPago) {
    console.log('Función global - Cargando datos de método de pago ID:', idMetodoPago);
    // Esta función ya está implementada dentro del document.ready
}
