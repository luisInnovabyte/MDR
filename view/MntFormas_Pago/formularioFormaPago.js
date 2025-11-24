$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formFormaPago', {
        codigo_pago: {
            required: true
        },
        nombre_pago: {
            required: true
        },
        id_metodo_pago: {
            required: true
        },
        porcentaje_anticipo_pago: {
            required: true
        },
        dias_anticipo_pago: {
            required: true
        },
        porcentaje_final_pago: {
            required: true
        },
        dias_final_pago: {
            required: true
        }
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Cargar métodos de pago en el select
    function cargarMetodosPago() {
        $.ajax({
            url: "../../controller/metodospago.php?op=listarDisponibles",
            type: "GET",
            dataType: "json",
            success: function(data) {
                try {
                    console.log('Métodos de pago recibidos:', data);
                    
                    var select = $('#id_metodo_pago');
                    select.empty();
                    select.append('<option value="">Seleccione un método...</option>');
                    
                    // Si data tiene la propiedad .data, usarla; si no, usar directamente data
                    var metodos = data.data || data;
                    
                    if (Array.isArray(metodos)) {
                        metodos.forEach(function(metodo) {
                            select.append(
                                '<option value="' + metodo.id_metodo_pago + '">' + 
                                metodo.nombre_metodo_pago + 
                                '</option>'
                            );
                        });
                    } else {
                        console.error('Formato de datos inesperado:', metodos);
                        toastr.error('Error al cargar métodos de pago');
                    }
                } catch (e) {
                    console.error('Error al procesar métodos de pago:', e);
                    toastr.error('Error al cargar métodos de pago');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar métodos de pago:', error);
                toastr.error('No se pudieron cargar los métodos de pago');
            }
        });
    }

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para cargar datos de forma de pago para edición
    function cargarDatosFormaPago(idPago) {
        console.log('Cargando datos de forma de pago ID:', idPago);
        
        $.ajax({
            url: "../../controller/formaspago.php?op=mostrar",
            type: "POST",
            data: { id_pago: idPago },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_pago').val(data.id_pago);
                    $('#codigo_pago').val(data.codigo_pago);
                    $('#nombre_pago').val(data.nombre_pago);
                    $('#id_metodo_pago').val(data.id_metodo_pago);
                    $('#descuento_pago').val(data.descuento_pago);
                    $('#porcentaje_anticipo_pago').val(data.porcentaje_anticipo_pago);
                    $('#dias_anticipo_pago').val(data.dias_anticipo_pago);
                    $('#porcentaje_final_pago').val(data.porcentaje_final_pago);
                    $('#dias_final_pago').val(data.dias_final_pago);
                    $('#observaciones_pago').val(data.observaciones_pago);
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_pago').prop('checked', data.activo_pago == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_pago == 1) {
                        $('#estado-text').text('Forma de Pago Activa').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Forma de Pago Inactiva').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar contador de caracteres si existen observaciones
                    if (data.observaciones_pago) {
                        $('#char-count').text(data.observaciones_pago.length);
                    }
                    
                    // Validar porcentajes después de cargar
                    validarPorcentajes();
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_pago').focus();
                    
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
                toastr.error('Error al obtener datos de la forma de pago');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Función de validación de porcentajes
    function validarPorcentajes() {
        const anticipo = parseFloat($('#porcentaje_anticipo_pago').val()) || 0;
        const final = parseFloat($('#porcentaje_final_pago').val()) || 0;
        const suma = anticipo + final;
        
        const alertPorcentaje = $('#alert-porcentaje');
        const mensajePorcentaje = $('#mensaje-porcentaje');
        
        if (Math.abs(suma - 100) > 0.01) { // Tolerancia para decimales
            alertPorcentaje.show();
            mensajePorcentaje.text(`La suma de porcentajes es ${suma.toFixed(2)}%. Debe ser exactamente 100%.`);
            return false;
        } else {
            alertPorcentaje.hide();
            return true;
        }
    }

    // Inicializar carga de métodos de pago
    cargarMetodosPago();

    // Verificar si estamos en modo edición
    const idPago = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idPago) {
        // Esperar a que se carguen los métodos de pago antes de cargar los datos
        setTimeout(function() {
            cargarDatosFormaPago(idPago);
        }, 500);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_pago').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    // Event listeners para validación en tiempo real de porcentajes
    $('#porcentaje_anticipo_pago, #porcentaje_final_pago').on('input', function() {
        validarPorcentajes();
    });

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR FORMA DE PAGO
    //*****************************************************/

    $(document).on('click', '#btnSalvarFormaPago', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_pagoR = $('#id_pago').val().trim();
        var codigo_pagoR = $('#codigo_pago').val().trim();
        var nombre_pagoR = $('#nombre_pago').val().trim();
        var id_metodo_pagoR = $('#id_metodo_pago').val();
        var descuento_pagoR = $('#descuento_pago').val().trim() || '0.00';
        var porcentaje_anticipo_pagoR = $('#porcentaje_anticipo_pago').val().trim();
        var dias_anticipo_pagoR = $('#dias_anticipo_pago').val().trim();
        var porcentaje_final_pagoR = $('#porcentaje_final_pago').val().trim();
        var dias_final_pagoR = $('#dias_final_pago').val().trim();
        var observaciones_pagoR = $('#observaciones_pago').val().trim();
        
        // El estado siempre será activo para nuevas formas de pago, o mantener el actual para edición
        var activo_pagoR;
        if (id_pagoR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_pagoR = $('#activo_pago').is(':checked') ? 1 : 0;
        } else {
            // Nueva forma de pago: siempre activa
            activo_pagoR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Validar que los porcentajes sumen 100%
        if (!validarPorcentajes()) {
            toastr.error('Los porcentajes de anticipo y pago final deben sumar exactamente 100%.', 'Error de Validación');
            return;
        }
        
        // Verificar forma de pago primero
        verificarFormaPagoExistente(
            id_pagoR,
            codigo_pagoR,
            nombre_pagoR,
            id_metodo_pagoR,
            descuento_pagoR,
            porcentaje_anticipo_pagoR,
            dias_anticipo_pagoR,
            porcentaje_final_pagoR,
            dias_final_pagoR,
            observaciones_pagoR,
            activo_pagoR
        );
    });

    function verificarFormaPagoExistente(
        id_pago,
        codigo_pago,
        nombre_pago,
        id_metodo_pago,
        descuento_pago,
        porcentaje_anticipo_pago,
        dias_anticipo_pago,
        porcentaje_final_pago,
        dias_final_pago,
        observaciones_pago,
        activo_pago
    ) {
        $.ajax({
            url: "../../controller/formaspago.php?op=verificar",
            type: "POST",
            data: { 
                codigo_pago: codigo_pago,
                nombre_pago: nombre_pago,
                id_pago: id_pago 
            },
            dataType: "json",
            success: function(response) {
                if (response.status === 'error') {
                    toastr.warning(response.message || "Error al verificar la forma de pago.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorFormaPagoExistente(codigo_pago, nombre_pago);
                } else {
                    guardarFormaPago(
                        id_pago,
                        codigo_pago,
                        nombre_pago,
                        id_metodo_pago,
                        descuento_pago,
                        porcentaje_anticipo_pago,
                        dias_anticipo_pago,
                        porcentaje_final_pago,
                        dias_final_pago,
                        observaciones_pago,
                        activo_pago
                    );
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar la forma de pago. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorFormaPagoExistente(codigo_pago, nombre_pago) {
        console.log("Forma de pago duplicada detectada:", codigo_pago, nombre_pago);
        Swal.fire({
            title: 'Forma de pago duplicada',
            text: 'Ya existe una forma de pago con el código "' + codigo_pago + '" o nombre "' + nombre_pago + '". Por favor, elija otro.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarFormaPago(
        id_pago,
        codigo_pago,
        nombre_pago,
        id_metodo_pago,
        descuento_pago,
        porcentaje_anticipo_pago,
        dias_anticipo_pago,
        porcentaje_final_pago,
        dias_final_pago,
        observaciones_pago,
        activo_pago
    ) {
        // Mostrar indicador de carga
        $('#btnSalvarFormaPago').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear datos para enviar
        var formData = {
            'codigo_pago': codigo_pago,
            'nombre_pago': nombre_pago,
            'id_metodo_pago': id_metodo_pago,
            'descuento_pago': descuento_pago,
            'porcentaje_anticipo_pago': porcentaje_anticipo_pago,
            'dias_anticipo_pago': dias_anticipo_pago,
            'porcentaje_final_pago': porcentaje_final_pago,
            'dias_final_pago': dias_final_pago,
            'observaciones_pago': observaciones_pago,
            'activo_pago': activo_pago
        };
        
        if (id_pago) {
            formData['id_pago'] = id_pago;
        }

        $.ajax({
            url: "../../controller/formaspago.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.status === 'success') {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Forma de pago guardada correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar la forma de pago");
                    // Restaurar botón
                    $('#btnSalvarFormaPago').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Forma de Pago');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar la forma de pago. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarFormaPago').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Forma de Pago');
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
            codigo_pago: $('#codigo_pago').val(),
            nombre_pago: $('#nombre_pago').val(),
            id_metodo_pago: $('#id_metodo_pago').val(),
            descuento_pago: $('#descuento_pago').val(),
            porcentaje_anticipo_pago: $('#porcentaje_anticipo_pago').val(),
            dias_anticipo_pago: $('#dias_anticipo_pago').val(),
            porcentaje_final_pago: $('#porcentaje_final_pago').val(),
            dias_final_pago: $('#dias_final_pago').val(),
            observaciones_pago: $('#observaciones_pago').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#codigo_pago').val() !== formOriginalValues.codigo_pago ||
            $('#nombre_pago').val() !== formOriginalValues.nombre_pago ||
            $('#id_metodo_pago').val() !== formOriginalValues.id_metodo_pago ||
            $('#descuento_pago').val() !== formOriginalValues.descuento_pago ||
            $('#porcentaje_anticipo_pago').val() !== formOriginalValues.porcentaje_anticipo_pago ||
            $('#dias_anticipo_pago').val() !== formOriginalValues.dias_anticipo_pago ||
            $('#porcentaje_final_pago').val() !== formOriginalValues.porcentaje_final_pago ||
            $('#dias_final_pago').val() !== formOriginalValues.dias_final_pago ||
            $('#observaciones_pago').val() !== formOriginalValues.observaciones_pago
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
    //   AUTO-FORMATO DE CAMPOS           //
    ///////////////////////////////////////

    // Auto-formato para codigo_pago: mayúsculas, sin espacios, máximo 20 caracteres
    $('#codigo_pago').on('input', function() {
        this.value = this.value.toUpperCase().replace(/\s+/g, '').slice(0, 20);
    });

    /////////////////////////////////////////
    //   FIN AUTO-FORMATO DE CAMPOS       //
    ///////////////////////////////////////

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosFormaPago(idPago) {
    console.log('Función global - Cargando datos de forma de pago ID:', idPago);
    // Esta función ya está implementada dentro del document.ready
}
