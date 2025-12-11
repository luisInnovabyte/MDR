$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formCliente', {
        codigo_cliente: {
            required: true
        },
        nombre_cliente: {
            required: true
        },
        nif_cliente: {
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

    // Función para cargar datos de cliente para edición
    function cargarDatosCliente(idCliente) {
        console.log('Cargando datos de cliente ID:', idCliente);
        
        $.ajax({
            url: "../../controller/cliente.php?op=mostrar",
            type: "POST",
            data: { id_cliente: idCliente },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_cliente').val(data.id_cliente);
                    $('#codigo_cliente').val(data.codigo_cliente);
                    $('#nombre_cliente').val(data.nombre_cliente);
                    $('#nif_cliente').val(data.nif_cliente);
                    $('#direccion_cliente').val(data.direccion_cliente);
                    $('#cp_cliente').val(data.cp_cliente);
                    $('#poblacion_cliente').val(data.poblacion_cliente);
                    $('#provincia_cliente').val(data.provincia_cliente);
                    $('#telefono_cliente').val(data.telefono_cliente);
                    $('#email_cliente').val(data.email_cliente);
                    $('#web_cliente').val(data.web_cliente);
                    $('#fax_cliente').val(data.fax_cliente);
                    
                    // Campos de facturación
                    $('#nombre_facturacion_cliente').val(data.nombre_facturacion_cliente);
                    $('#direccion_facturacion_cliente').val(data.direccion_facturacion_cliente);
                    $('#cp_facturacion_cliente').val(data.cp_facturacion_cliente);
                    $('#poblacion_facturacion_cliente').val(data.poblacion_facturacion_cliente);
                    $('#provincia_facturacion_cliente').val(data.provincia_facturacion_cliente);
                    
                    $('#observaciones_cliente').val(data.observaciones_cliente);
                    
                    // Forma de pago habitual
                    $('#id_forma_pago_habitual').val(data.id_forma_pago_habitual || '');
                    
                    // Si hay forma de pago seleccionada, mostrar su información
                    if (data.id_forma_pago_habitual) {
                        $('#id_forma_pago_habitual').trigger('change');
                    }
                    
                    // Configurar el campo activo_cliente (solo lectura en formulario)
                    // Campo oculto con el valor real para envío
                    $('#activo_cliente_hidden').val(data.activo_cliente);
                    
                    // Indicador visual (checkbox disabled)
                    $('#activo_cliente_display').prop('checked', data.activo_cliente == 1);
                    
                    // Actualizar texto y descripción según el estado
                    if (data.activo_cliente == 1) {
                        $('#estado_texto').text('Cliente Activo').removeClass('text-danger').addClass('text-success');
                        $('#estado_descripcion').text('Este cliente está activo y aparecerá en las selecciones.');
                    } else {
                        $('#estado_texto').text('Cliente Inactivo').removeClass('text-success').addClass('text-danger');
                        $('#estado_descripcion').text('Este cliente está inactivo. Para activarlo use la opción desde la lista.');
                    }
                    
                    // Actualizar contador de caracteres si existe observaciones
                    if (data.observaciones_cliente) {
                        $('#obs-char-count').text(data.observaciones_cliente.length);
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_cliente').focus();
                    
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
                toastr.error('Error al obtener datos del cliente');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idCliente = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idCliente) {
        cargarDatosCliente(idCliente);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_cliente').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR CLIENTE
    //*****************************************************/

    $(document).on('click', '#btnSalvarCliente', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_clienteR = $('#id_cliente').val().trim();
        var codigo_clienteR = $('#codigo_cliente').val().trim().toUpperCase();
        var nombre_clienteR = $('#nombre_cliente').val().trim();
        var nif_clienteR = $('#nif_cliente').val().trim().toUpperCase();
        var direccion_clienteR = $('#direccion_cliente').val().trim();
        var cp_clienteR = $('#cp_cliente').val().trim();
        var poblacion_clienteR = $('#poblacion_cliente').val().trim();
        var provincia_clienteR = $('#provincia_cliente').val().trim();
        var telefono_clienteR = $('#telefono_cliente').val().trim();
        var email_clienteR = $('#email_cliente').val().trim();
        var web_clienteR = $('#web_cliente').val().trim();
        var fax_clienteR = $('#fax_cliente').val().trim();
        
        // Campos de facturación
        var nombre_facturacion_clienteR = $('#nombre_facturacion_cliente').val().trim();
        var direccion_facturacion_clienteR = $('#direccion_facturacion_cliente').val().trim();
        var cp_facturacion_clienteR = $('#cp_facturacion_cliente').val().trim();
        var poblacion_facturacion_clienteR = $('#poblacion_facturacion_cliente').val().trim();
        var provincia_facturacion_clienteR = $('#provincia_facturacion_cliente').val().trim();
        
        var observaciones_clienteR = $('#observaciones_cliente').val().trim();
        
        // Forma de pago habitual
        var id_forma_pago_habitualR = $('#id_forma_pago_habitual').val();
        
        // Obtener el estado del cliente
        var activo_clienteR;
        if (id_clienteR) {
            // En edición: usar el valor del campo oculto (cargado desde BD)
            activo_clienteR = $('#activo_cliente_hidden').val();
        } else {
            // Nuevo cliente: siempre activo (valor fijo)
            activo_clienteR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar cliente primero
        verificarClienteExistente(id_clienteR, codigo_clienteR, nombre_clienteR, nif_clienteR, direccion_clienteR, cp_clienteR, poblacion_clienteR, provincia_clienteR, telefono_clienteR, email_clienteR, web_clienteR, fax_clienteR, nombre_facturacion_clienteR, direccion_facturacion_clienteR, cp_facturacion_clienteR, poblacion_facturacion_clienteR, provincia_facturacion_clienteR, id_forma_pago_habitualR, observaciones_clienteR, activo_clienteR);
    });

    function verificarClienteExistente(id_cliente, codigo_cliente, nombre_cliente, nif_cliente, direccion_cliente, cp_cliente, poblacion_cliente, provincia_cliente, telefono_cliente, email_cliente, web_cliente, fax_cliente, nombre_facturacion_cliente, direccion_facturacion_cliente, cp_facturacion_cliente, poblacion_facturacion_cliente, provincia_facturacion_cliente, id_forma_pago_habitual, observaciones_cliente, activo_cliente) {
        console.log('🔍 Verificando cliente:', { codigo: codigo_cliente, nombre: nombre_cliente, nif: nif_cliente, id: id_cliente });
        
        $.ajax({
            url: "../../controller/cliente.php?op=verificar",
            type: "POST",
            data: { 
                codigo_cliente: codigo_cliente,
                nombre_cliente: nombre_cliente,  // Añadido nombre_cliente
                nif_cliente: nif_cliente,        // Añadido nif_cliente
                id_cliente: id_cliente || ''     // Asegurar que no sea null
            },
            dataType: "json",
            success: function(response) {
                console.log('📋 Respuesta verificación:', response);
                
                // La respuesta del modelo es { existe: true/false }
                if (response.existe === false) {
                    // No existe, podemos guardar
                    console.log('✅ Cliente no existe, procediendo a guardar');
                    guardarCliente(id_cliente, codigo_cliente, nombre_cliente, nif_cliente, direccion_cliente, cp_cliente, poblacion_cliente, provincia_cliente, telefono_cliente, email_cliente, web_cliente, fax_cliente, nombre_facturacion_cliente, direccion_facturacion_cliente, cp_facturacion_cliente, poblacion_facturacion_cliente, provincia_facturacion_cliente, id_forma_pago_habitual, observaciones_cliente, activo_cliente);
                } else {
                    // Ya existe
                    console.log('❌ Cliente ya existe');
                    mostrarErrorClienteExistente("Ya existe un cliente con el código '" + codigo_cliente + "', nombre '" + nombre_cliente + "' o NIF/CIF '" + nif_cliente + "'");
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al verificar el cliente. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorClienteExistente(mensaje) {
        console.log("Cliente duplicado detectado:", mensaje);
        Swal.fire({
            title: 'Cliente duplicado',
            text: mensaje,
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarCliente(id_cliente, codigo_cliente, nombre_cliente, nif_cliente, direccion_cliente, cp_cliente, poblacion_cliente, provincia_cliente, telefono_cliente, email_cliente, web_cliente, fax_cliente, nombre_facturacion_cliente, direccion_facturacion_cliente, cp_facturacion_cliente, poblacion_facturacion_cliente, provincia_facturacion_cliente, id_forma_pago_habitual, observaciones_cliente, activo_cliente) {
        // Mostrar indicador de carga
        $('#btnSalvarCliente').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Preparar los datos para enviar - IMPORTANTE: Usar FormData para enviar todos los campos
        const formData = new FormData();
        formData.append('id_cliente', id_cliente);
        formData.append('codigo_cliente', codigo_cliente);
        formData.append('nombre_cliente', nombre_cliente);
        formData.append('nif_cliente', nif_cliente);
        formData.append('direccion_cliente', direccion_cliente);
        formData.append('cp_cliente', cp_cliente);
        formData.append('poblacion_cliente', poblacion_cliente);
        formData.append('provincia_cliente', provincia_cliente);
        formData.append('telefono_cliente', telefono_cliente);
        formData.append('email_cliente', email_cliente);
        formData.append('web_cliente', web_cliente);
        formData.append('fax_cliente', fax_cliente);
        formData.append('nombre_facturacion_cliente', nombre_facturacion_cliente);
        formData.append('direccion_facturacion_cliente', direccion_facturacion_cliente);
        formData.append('cp_facturacion_cliente', cp_facturacion_cliente);
        formData.append('poblacion_facturacion_cliente', poblacion_facturacion_cliente);
        formData.append('provincia_facturacion_cliente', provincia_facturacion_cliente);
        formData.append('id_forma_pago_habitual', id_forma_pago_habitual || '');
        formData.append('observaciones_cliente', observaciones_cliente);
        formData.append('activo_cliente', activo_cliente);
        
        console.log('💾 Enviando con FormData');
        console.log('🔍 id_forma_pago_habitual:', id_forma_pago_habitual, 'Tipo:', typeof id_forma_pago_habitual);
        
        $.ajax({
            url: "../../controller/cliente.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(res) {
                console.log('📋 Respuesta del guardado:', res);
                
                if (res.status === 'success') {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Cliente guardado correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el cliente");
                    // Restaurar botón
                    $('#btnSalvarCliente').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Cliente');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                console.error("Estado:", status);
                console.error("Respuesta del servidor:", xhr.responseText);
                
                let errorMsg = 'No se pudo guardar el cliente.';
                try {
                    // Intentar parsear la respuesta como JSON
                    const response = JSON.parse(xhr.responseText);
                    errorMsg = response.message || errorMsg;
                } catch (e) {
                    // Si no es JSON válido, usar el error original
                    errorMsg += ' Error: ' + error;
                    if (xhr.responseText) {
                        errorMsg += ' Respuesta del servidor: ' + xhr.responseText.substring(0, 200);
                    }
                }
                
                Swal.fire('Error', errorMsg, 'error');
                // Restaurar botón
                $('#btnSalvarCliente').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Cliente');
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
            codigo_cliente: $('#codigo_cliente').val(),
            nombre_cliente: $('#nombre_cliente').val(),
            nif_cliente: $('#nif_cliente').val(),
            direccion_cliente: $('#direccion_cliente').val(),
            cp_cliente: $('#cp_cliente').val(),
            poblacion_cliente: $('#poblacion_cliente').val(),
            provincia_cliente: $('#provincia_cliente').val(),
            telefono_cliente: $('#telefono_cliente').val(),
            email_cliente: $('#email_cliente').val(),
            web_cliente: $('#web_cliente').val(),
            fax_cliente: $('#fax_cliente').val(),
            nombre_facturacion_cliente: $('#nombre_facturacion_cliente').val(),
            direccion_facturacion_cliente: $('#direccion_facturacion_cliente').val(),
            cp_facturacion_cliente: $('#cp_facturacion_cliente').val(),
            poblacion_facturacion_cliente: $('#poblacion_facturacion_cliente').val(),
            provincia_facturacion_cliente: $('#provincia_facturacion_cliente').val(),
            id_forma_pago_habitual: $('#id_forma_pago_habitual').val(),
            observaciones_cliente: $('#observaciones_cliente').val(),
            activo_cliente: $('#activo_cliente_hidden').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#codigo_cliente').val() !== formOriginalValues.codigo_cliente ||
            $('#nombre_cliente').val() !== formOriginalValues.nombre_cliente ||
            $('#nif_cliente').val() !== formOriginalValues.nif_cliente ||
            $('#direccion_cliente').val() !== formOriginalValues.direccion_cliente ||
            $('#cp_cliente').val() !== formOriginalValues.cp_cliente ||
            $('#poblacion_cliente').val() !== formOriginalValues.poblacion_cliente ||
            $('#provincia_cliente').val() !== formOriginalValues.provincia_cliente ||
            $('#telefono_cliente').val() !== formOriginalValues.telefono_cliente ||
            $('#email_cliente').val() !== formOriginalValues.email_cliente ||
            $('#web_cliente').val() !== formOriginalValues.web_cliente ||
            $('#fax_cliente').val() !== formOriginalValues.fax_cliente ||
            $('#nombre_facturacion_cliente').val() !== formOriginalValues.nombre_facturacion_cliente ||
            $('#direccion_facturacion_cliente').val() !== formOriginalValues.direccion_facturacion_cliente ||
            $('#cp_facturacion_cliente').val() !== formOriginalValues.cp_facturacion_cliente ||
            $('#poblacion_facturacion_cliente').val() !== formOriginalValues.poblacion_facturacion_cliente ||
            $('#provincia_facturacion_cliente').val() !== formOriginalValues.provincia_facturacion_cliente ||
            $('#id_forma_pago_habitual').val() !== formOriginalValues.id_forma_pago_habitual ||
            $('#observaciones_cliente').val() !== formOriginalValues.observaciones_cliente ||
            $('#activo_cliente_hidden').val() !== formOriginalValues.activo_cliente
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

    // Auto-formato para algunos campos
    $('#codigo_cliente').on('input', function() {
        this.value = this.value.toUpperCase().replace(/\s+/g, '').slice(0, 20);
    });

    $('#nif_cliente').on('input', function() {
        this.value = this.value.toUpperCase().replace(/\s+/g, '').slice(0, 20);
    });

    $('#cp_cliente, #cp_facturacion_cliente').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });

    // Contador de caracteres para observaciones
    $('#observaciones_cliente').on('input', function() {
        const maxLength = 65535; // TEXT field max length
        const currentLength = $(this).val().length;
        
        $('#obs-char-count').text(currentLength);
        
        if (currentLength > maxLength - 100) {
            $('#obs-char-count').parent().removeClass('text-muted').addClass('text-warning');
        } else {
            $('#obs-char-count').parent().removeClass('text-warning').addClass('text-muted');
        }
    });

    // Mostrar información de la forma de pago seleccionada
    $('#id_forma_pago_habitual').on('change', function() {
        const selectedOption = $(this).find('option:selected');
        
        if ($(this).val() === '') {
            // No hay forma de pago seleccionada
            $('#info-forma-pago').hide();
        } else {
            // Mostrar información de la forma de pago
            const metodo = selectedOption.data('metodo') || 'Sin información';
            const tipo = selectedOption.data('tipo') || 'Sin información';
            const anticipo = selectedOption.data('anticipo') || '0';
            const descuento = selectedOption.data('descuento') || '0';
            
            $('#info-metodo').text(metodo);
            $('#info-tipo').text(tipo);
            $('#info-anticipo').text(anticipo);
            $('#info-descuento').text(descuento);
            
            $('#info-forma-pago').slideDown();
        }
    });

    /////////////////////////////////////////
    //   FIN FUNCIONES DE UTILIDAD        //
    ///////////////////////////////////////

    /////////////////////////////////////////
    //   INICIALIZACIÓN DEL FORMULARIO    //
    ///////////////////////////////////////

    // Inicialización según el modo (nuevo/editar)
    const urlParamsInit = new URLSearchParams(window.location.search);
    const idClienteInit = urlParamsInit.get('id');
    const modoInit = urlParamsInit.get('modo') || 'nuevo';
    
    if (modoInit === 'editar' && idClienteInit) {
        // Actualizar títulos para modo edición
        document.getElementById('page-title').textContent = 'Editar Cliente';
        document.getElementById('breadcrumb-title').textContent = 'Editar Cliente';
        document.getElementById('btnSalvarCliente').innerHTML = '<i class="fas fa-save me-2"></i>Actualizar Cliente';
        document.getElementById('id_cliente').value = idClienteInit;
        
        // Cargar datos 
        setTimeout(function() {
            cargarDatosCliente(idClienteInit);
        }, 500);
    } else {
        // Capturar valores originales para formulario nuevo
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosCliente(idCliente) {
    console.log('Función global - Cargando datos de cliente ID:', idCliente);
    // Esta función ya está implementada dentro del document.ready
}