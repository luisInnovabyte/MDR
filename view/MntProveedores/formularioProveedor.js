$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formProveedor', {
        codigo_proveedor: {
            required: true
        },
        nombre_proveedor: {
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

    // Función para cargar datos de proveedor para edición
    function cargarDatosProveedor(idProveedor) {
        console.log('Cargando datos de proveedor ID:', idProveedor);
        
        $.ajax({
            url: "../../controller/proveedor.php?op=mostrar",
            type: "POST",
            data: { id_proveedor: idProveedor },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_proveedor').val(data.id_proveedor);
                    $('#codigo_proveedor').val(data.codigo_proveedor);
                    $('#nombre_proveedor').val(data.nombre_proveedor);
                    $('#direccion_proveedor').val(data.direccion_proveedor);
                    $('#cp_proveedor').val(data.cp_proveedor);
                    $('#poblacion_proveedor').val(data.poblacion_proveedor);
                    $('#provincia_proveedor').val(data.provincia_proveedor);
                    $('#nif_proveedor').val(data.nif_proveedor);
                    $('#telefono_proveedor').val(data.telefono_proveedor);
                    $('#fax_proveedor').val(data.fax_proveedor);
                    $('#web_proveedor').val(data.web_proveedor);
                    $('#email_proveedor').val(data.email_proveedor);
                    $('#persona_contacto_proveedor').val(data.persona_contacto_proveedor);
                    $('#direccion_sat_proveedor').val(data.direccion_sat_proveedor);
                    $('#cp_sat_proveedor').val(data.cp_sat_proveedor);
                    $('#poblacion_sat_proveedor').val(data.poblacion_sat_proveedor);
                    $('#provincia_sat_proveedor').val(data.provincia_sat_proveedor);
                    $('#telefono_sat_proveedor').val(data.telefono_sat_proveedor);
                    $('#fax_sat_proveedor').val(data.fax_sat_proveedor);
                    $('#email_sat_proveedor').val(data.email_sat_proveedor);
                    $('#observaciones_proveedor').val(data.observaciones_proveedor);
                    
                    // Configurar el campo activo_proveedor (solo lectura en formulario)
                    // Campo oculto con el valor real para envío
                    $('#activo_proveedor_hidden').val(data.activo_proveedor);
                    
                    // Indicador visual (checkbox disabled)
                    $('#activo_proveedor_display').prop('checked', data.activo_proveedor == 1);
                    
                    // Actualizar texto y descripción según el estado
                    if (data.activo_proveedor == 1) {
                        $('#estado_texto').text('Proveedor Activo').removeClass('text-danger').addClass('text-success');
                        $('#estado_descripcion').text('Este proveedor está activo y aparecerá en las selecciones.');
                    } else {
                        $('#estado_texto').text('Proveedor Inactivo').removeClass('text-success').addClass('text-danger');
                        $('#estado_descripcion').text('Este proveedor está inactivo. Para activarlo use la opción desde la lista.');
                    }
                    
                    // Actualizar contador de caracteres si existe observaciones
                    if (data.observaciones_proveedor) {
                        $('#char-count').text(data.observaciones_proveedor.length);
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_proveedor').focus();
                    
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
                toastr.error('Error al obtener datos del proveedor');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idProveedor = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idProveedor) {
        cargarDatosProveedor(idProveedor);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_proveedor').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR PROVEEDOR
    //*****************************************************/

    $(document).on('click', '#btnSalvarProveedor', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_proveedorR = $('#id_proveedor').val().trim();
        var codigo_proveedorR = $('#codigo_proveedor').val().trim().toUpperCase();
        var nombre_proveedorR = $('#nombre_proveedor').val().trim();
        var direccion_proveedorR = $('#direccion_proveedor').val().trim();
        var cp_proveedorR = $('#cp_proveedor').val().trim();
        var poblacion_proveedorR = $('#poblacion_proveedor').val().trim();
        var provincia_proveedorR = $('#provincia_proveedor').val().trim();
        var nif_proveedorR = $('#nif_proveedor').val().trim().toUpperCase();
        var telefono_proveedorR = $('#telefono_proveedor').val().trim();
        var fax_proveedorR = $('#fax_proveedor').val().trim();
        var web_proveedorR = $('#web_proveedor').val().trim();
        var email_proveedorR = $('#email_proveedor').val().trim();
        var persona_contacto_proveedorR = $('#persona_contacto_proveedor').val().trim();
        var direccion_sat_proveedorR = $('#direccion_sat_proveedor').val().trim();
        var cp_sat_proveedorR = $('#cp_sat_proveedor').val().trim();
        var poblacion_sat_proveedorR = $('#poblacion_sat_proveedor').val().trim();
        var provincia_sat_proveedorR = $('#provincia_sat_proveedor').val().trim();
        var telefono_sat_proveedorR = $('#telefono_sat_proveedor').val().trim();
        var fax_sat_proveedorR = $('#fax_sat_proveedor').val().trim();
        var email_sat_proveedorR = $('#email_sat_proveedor').val().trim();
        var observaciones_proveedorR = $('#observaciones_proveedor').val().trim();
        
        // Obtener el estado del proveedor
        var activo_proveedorR;
        if (id_proveedorR) {
            // En edición: usar el valor del campo oculto (cargado desde BD)
            activo_proveedorR = $('#activo_proveedor_hidden').val();
        } else {
            // Nuevo proveedor: siempre activo (valor fijo)
            activo_proveedorR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar proveedor primero
        verificarProveedorExistente(id_proveedorR, codigo_proveedorR, nombre_proveedorR, direccion_proveedorR, cp_proveedorR, poblacion_proveedorR, provincia_proveedorR, nif_proveedorR, telefono_proveedorR, fax_proveedorR, web_proveedorR, email_proveedorR, persona_contacto_proveedorR, direccion_sat_proveedorR, cp_sat_proveedorR, poblacion_sat_proveedorR, provincia_sat_proveedorR, telefono_sat_proveedorR, fax_sat_proveedorR, email_sat_proveedorR, observaciones_proveedorR, activo_proveedorR);
    });

    function verificarProveedorExistente(id_proveedor, codigo_proveedor, nombre_proveedor, direccion_proveedor, cp_proveedor, poblacion_proveedor, provincia_proveedor, nif_proveedor, telefono_proveedor, fax_proveedor, web_proveedor, email_proveedor, persona_contacto_proveedor, direccion_sat_proveedor, cp_sat_proveedor, poblacion_sat_proveedor, provincia_sat_proveedor, telefono_sat_proveedor, fax_sat_proveedor, email_sat_proveedor, observaciones_proveedor, activo_proveedor) {
        console.log('🔍 Verificando proveedor:', { codigo: codigo_proveedor, nombre: nombre_proveedor, id: id_proveedor });
        
        $.ajax({
            url: "../../controller/proveedor.php?op=verificar",
            type: "POST",
            data: { 
                codigo_proveedor: codigo_proveedor,
                nombre_proveedor: nombre_proveedor,  // Añadido nombre_proveedor
                id_proveedor: id_proveedor || ''     // Asegurar que no sea null
            },
            dataType: "json",
            success: function(response) {
                console.log('📋 Respuesta verificación:', response);
                
                // La respuesta del modelo es { existe: true/false }
                if (response.existe === false) {
                    // No existe, podemos guardar
                    console.log('✅ Proveedor no existe, procediendo a guardar');
                    guardarProveedor(id_proveedor, codigo_proveedor, nombre_proveedor, direccion_proveedor, cp_proveedor, poblacion_proveedor, provincia_proveedor, nif_proveedor, telefono_proveedor, fax_proveedor, web_proveedor, email_proveedor, persona_contacto_proveedor, direccion_sat_proveedor, cp_sat_proveedor, poblacion_sat_proveedor, provincia_sat_proveedor, telefono_sat_proveedor, fax_sat_proveedor, email_sat_proveedor, observaciones_proveedor, activo_proveedor);
                } else {
                    // Ya existe
                    console.log('❌ Proveedor ya existe');
                    mostrarErrorProveedorExistente("Ya existe un proveedor con el código '" + codigo_proveedor + "' o nombre '" + nombre_proveedor + "'");
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al verificar el proveedor. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorProveedorExistente(mensaje) {
        console.log("Proveedor duplicado detectado:", mensaje);
        Swal.fire({
            title: 'Código de proveedor duplicado',
            text: mensaje,
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarProveedor(id_proveedor, codigo_proveedor, nombre_proveedor, direccion_proveedor, cp_proveedor, poblacion_proveedor, provincia_proveedor, nif_proveedor, telefono_proveedor, fax_proveedor, web_proveedor, email_proveedor, persona_contacto_proveedor, direccion_sat_proveedor, cp_sat_proveedor, poblacion_sat_proveedor, provincia_sat_proveedor, telefono_sat_proveedor, fax_sat_proveedor, email_sat_proveedor, observaciones_proveedor, activo_proveedor) {
        // Mostrar indicador de carga
        $('#btnSalvarProveedor').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Preparar los datos para enviar
        const datosEnvio = {
            id_proveedor: id_proveedor,
            codigo_proveedor: codigo_proveedor,
            nombre_proveedor: nombre_proveedor,
            direccion_proveedor: direccion_proveedor,
            cp_proveedor: cp_proveedor,
            poblacion_proveedor: poblacion_proveedor,
            provincia_proveedor: provincia_proveedor,
            nif_proveedor: nif_proveedor,
            telefono_proveedor: telefono_proveedor,
            fax_proveedor: fax_proveedor,
            web_proveedor: web_proveedor,
            email_proveedor: email_proveedor,
            persona_contacto_proveedor: persona_contacto_proveedor,
            direccion_sat_proveedor: direccion_sat_proveedor,
            cp_sat_proveedor: cp_sat_proveedor,
            poblacion_sat_proveedor: poblacion_sat_proveedor,
            provincia_sat_proveedor: provincia_sat_proveedor,
            telefono_sat_proveedor: telefono_sat_proveedor,
            fax_sat_proveedor: fax_sat_proveedor,
            email_sat_proveedor: email_sat_proveedor,
            observaciones_proveedor: observaciones_proveedor,
            activo_proveedor: activo_proveedor
        };
        
        console.log('💾 Datos a guardar:', datosEnvio);
        
        $.ajax({
            url: "../../controller/proveedor.php?op=guardaryeditar",
            type: "POST",
            data: datosEnvio,
            dataType: "json",
            success: function(res) {
                console.log('📋 Respuesta del guardado:', res);
                
                if (res.status === 'success') {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Proveedor guardado correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el proveedor");
                    // Restaurar botón
                    $('#btnSalvarProveedor').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Proveedor');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                console.error("Estado:", status);
                console.error("Respuesta del servidor:", xhr.responseText);
                
                let errorMsg = 'No se pudo guardar el proveedor.';
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
                $('#btnSalvarProveedor').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Proveedor');
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
            codigo_proveedor: $('#codigo_proveedor').val(),
            nombre_proveedor: $('#nombre_proveedor').val(),
            direccion_proveedor: $('#direccion_proveedor').val(),
            cp_proveedor: $('#cp_proveedor').val(),
            poblacion_proveedor: $('#poblacion_proveedor').val(),
            provincia_proveedor: $('#provincia_proveedor').val(),
            nif_proveedor: $('#nif_proveedor').val(),
            telefono_proveedor: $('#telefono_proveedor').val(),
            fax_proveedor: $('#fax_proveedor').val(),
            web_proveedor: $('#web_proveedor').val(),
            email_proveedor: $('#email_proveedor').val(),
            persona_contacto_proveedor: $('#persona_contacto_proveedor').val(),
            direccion_sat_proveedor: $('#direccion_sat_proveedor').val(),
            cp_sat_proveedor: $('#cp_sat_proveedor').val(),
            poblacion_sat_proveedor: $('#poblacion_sat_proveedor').val(),
            provincia_sat_proveedor: $('#provincia_sat_proveedor').val(),
            telefono_sat_proveedor: $('#telefono_sat_proveedor').val(),
            fax_sat_proveedor: $('#fax_sat_proveedor').val(),
            email_sat_proveedor: $('#email_sat_proveedor').val(),
            observaciones_proveedor: $('#observaciones_proveedor').val(),
            activo_proveedor: $('#activo_proveedor_hidden').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#codigo_proveedor').val() !== formOriginalValues.codigo_proveedor ||
            $('#nombre_proveedor').val() !== formOriginalValues.nombre_proveedor ||
            $('#direccion_proveedor').val() !== formOriginalValues.direccion_proveedor ||
            $('#cp_proveedor').val() !== formOriginalValues.cp_proveedor ||
            $('#poblacion_proveedor').val() !== formOriginalValues.poblacion_proveedor ||
            $('#provincia_proveedor').val() !== formOriginalValues.provincia_proveedor ||
            $('#nif_proveedor').val() !== formOriginalValues.nif_proveedor ||
            $('#telefono_proveedor').val() !== formOriginalValues.telefono_proveedor ||
            $('#fax_proveedor').val() !== formOriginalValues.fax_proveedor ||
            $('#web_proveedor').val() !== formOriginalValues.web_proveedor ||
            $('#email_proveedor').val() !== formOriginalValues.email_proveedor ||
            $('#persona_contacto_proveedor').val() !== formOriginalValues.persona_contacto_proveedor ||
            $('#direccion_sat_proveedor').val() !== formOriginalValues.direccion_sat_proveedor ||
            $('#cp_sat_proveedor').val() !== formOriginalValues.cp_sat_proveedor ||
            $('#poblacion_sat_proveedor').val() !== formOriginalValues.poblacion_sat_proveedor ||
            $('#provincia_sat_proveedor').val() !== formOriginalValues.provincia_sat_proveedor ||
            $('#telefono_sat_proveedor').val() !== formOriginalValues.telefono_sat_proveedor ||
            $('#fax_sat_proveedor').val() !== formOriginalValues.fax_sat_proveedor ||
            $('#email_sat_proveedor').val() !== formOriginalValues.email_sat_proveedor ||
            $('#observaciones_proveedor').val() !== formOriginalValues.observaciones_proveedor ||
            $('#activo_proveedor_hidden').val() !== formOriginalValues.activo_proveedor
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
    $('#codigo_proveedor').on('input', function() {
        this.value = this.value.toUpperCase().replace(/\s+/g, '').slice(0, 20);
    });

    $('#nif_proveedor').on('input', function() {
        this.value = this.value.toUpperCase().replace(/\s+/g, '').slice(0, 20);
    });

    $('#cp_proveedor, #cp_sat_proveedor').on('input', function() {
        this.value = this.value.replace(/[^0-9]/g, '').slice(0, 10);
    });

    // Contador de caracteres para observaciones
    $('#observaciones_proveedor').on('input', function() {
        const maxLength = 65535; // TEXT field max length
        const currentLength = $(this).val().length;
        
        $('#char-count').text(currentLength);
        
        if (currentLength > maxLength - 100) {
            $('#char-count').parent().removeClass('text-muted').addClass('text-warning');
        } else {
            $('#char-count').parent().removeClass('text-warning').addClass('text-muted');
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
    const idProveedorInit = urlParamsInit.get('id');
    const modoInit = urlParamsInit.get('modo') || 'nuevo';
    
    if (modoInit === 'editar' && idProveedorInit) {
        // Cargar datos 
        setTimeout(function() {
            cargarDatosProveedor(idProveedorInit);
        }, 500);
    } else {
        // Capturar valores originales para formulario nuevo
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosProveedor(idProveedor) {
    console.log('Función global - Cargando datos de proveedor ID:', idProveedor);
    // Esta función ya está implementada dentro del document.ready
}