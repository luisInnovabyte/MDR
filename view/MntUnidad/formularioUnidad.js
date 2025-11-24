$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formUnidad', {
        nombre_unidad: {
            required: true
        },
        name_unidad: {
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

    // Función para cargar datos de unidad para edición
    function cargarDatosUnidad(idUnidad) {
        console.log('Cargando datos de unidad ID:', idUnidad);
        
        $.ajax({
            url: "../../controller/unidad.php?op=mostrar",
            type: "POST",
            data: { id_unidad: idUnidad },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_unidad').val(data.id_unidad);
                    $('#nombre_unidad').val(data.nombre_unidad);
                    $('#name_unidad').val(data.name_unidad);
                    $('#simbolo_unidad').val(data.simbolo_unidad);
                    $('#descr_unidad').val(data.descr_unidad);
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_unidad').prop('checked', data.activo_unidad == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_unidad == 1) {
                        $('#estado-text').text('Unidad Activa').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Unidad Inactiva').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar contador de caracteres si existe descripción
                    if (data.descr_unidad) {
                        $('#char-count').text(data.descr_unidad.length);
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#nombre_unidad').focus();
                    
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
                toastr.error('Error al obtener datos de la unidad');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idUnidad = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idUnidad) {
        cargarDatosUnidad(idUnidad);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#nombre_unidad').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR UNIDAD
    //*****************************************************/

    $(document).on('click', '#btnSalvarUnidad', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_unidadR = $('#id_unidad').val().trim();
        var nombre_unidadR = $('#nombre_unidad').val().trim();
        var name_unidadR = $('#name_unidad').val().trim();
        var simbolo_unidadR = $('#simbolo_unidad').val().trim();
        var descr_unidadR = $('#descr_unidad').val().trim();
        
        // El estado siempre será activo para nuevas unidades, o mantener el actual para edición
        var activo_unidadR;
        if (id_unidadR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_unidadR = $('#activo_unidad').is(':checked') ? 1 : 0;
        } else {
            // Nueva unidad: siempre activa
            activo_unidadR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar unidad primero
        verificarUnidadExistente(id_unidadR, nombre_unidadR, name_unidadR, simbolo_unidadR, descr_unidadR, activo_unidadR);
    });

    function verificarUnidadExistente(id_unidad, nombre_unidad, name_unidad, simbolo_unidad, descr_unidad, activo_unidad) {
        $.ajax({
            url: "../../controller/unidad.php?op=verificarUnidad",
            type: "POST",
            data: { 
                nombre_unidad: nombre_unidad,
                name_unidad: name_unidad,
                id_unidad: id_unidad 
            },
            dataType: "json",
            success: function(response) {
                if (!response.success) {
                    toastr.warning(response.message || "Error al verificar la unidad.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorUnidadExistente(nombre_unidad);
                } else {
                    guardarUnidad(id_unidad, nombre_unidad, name_unidad, simbolo_unidad, descr_unidad, activo_unidad);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar la unidad. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorUnidadExistente(nombre_unidad) {
        console.log("Unidad duplicada detectada:", nombre_unidad);
        Swal.fire({
            title: 'Unidad duplicada',
            text: 'La unidad "' + nombre_unidad + '" ya existe. Por favor, elija otro nombre.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarUnidad(id_unidad, nombre_unidad, name_unidad, simbolo_unidad, descr_unidad, activo_unidad) {
        // Mostrar indicador de carga
        $('#btnSalvarUnidad').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear datos para enviar
        var formData = {
            'nombre_unidad': nombre_unidad,
            'name_unidad': name_unidad,
            'simbolo_unidad': simbolo_unidad,
            'descr_unidad': descr_unidad,
            'activo_unidad': activo_unidad
        };
        
        if (id_unidad) {
            formData['id_unidad'] = id_unidad;
        }

        $.ajax({
            url: "../../controller/unidad.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Unidad guardada correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar la unidad");
                    // Restaurar botón
                    $('#btnSalvarUnidad').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Unidad');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar la unidad. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarUnidad').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Unidad');
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
            nombre_unidad: $('#nombre_unidad').val(),
            name_unidad: $('#name_unidad').val(),
            simbolo_unidad: $('#simbolo_unidad').val(),
            descr_unidad: $('#descr_unidad').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#nombre_unidad').val() !== formOriginalValues.nombre_unidad ||
            $('#name_unidad').val() !== formOriginalValues.name_unidad ||
            $('#simbolo_unidad').val() !== formOriginalValues.simbolo_unidad ||
            $('#descr_unidad').val() !== formOriginalValues.descr_unidad
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
function cargarDatosUnidad(idUnidad) {
    console.log('Función global - Cargando datos de unidad ID:', idUnidad);
    // Esta función ya está implementada dentro del document.ready
}