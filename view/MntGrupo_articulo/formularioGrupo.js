$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formGrupo', {
        codigo_grupo: {
            required: true
        },
        nombre_grupo: {
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

    // Función para cargar datos de grupo para edición
    function cargarDatosGrupo(idGrupo) {
        console.log('Cargando datos de grupo ID:', idGrupo);
        
        $.ajax({
            url: "../../controller/grupo_articulo.php?op=mostrar",
            type: "POST",
            data: { id_grupo: idGrupo },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_grupo').val(data.id_grupo);
                    $('#codigo_grupo').val(data.codigo_grupo);
                    $('#nombre_grupo').val(data.nombre_grupo);
                    $('#descripcion_grupo').val(data.descripcion_grupo || '');
                    $('#observaciones_grupo').val(data.observaciones_grupo || '');
                    
                    // Configurar el switch de estado (mantener estado actual en edición)
                    $('#activo_grupo').prop('checked', data.activo_grupo == 1);
                    
                    // Actualizar texto del estado según el valor actual
                    if (data.activo_grupo == 1) {
                        $('#estado-text').text('Grupo Activo').removeClass('text-danger').addClass('text-success');
                    } else {
                        $('#estado-text').text('Grupo Inactivo').removeClass('text-success').addClass('text-danger');
                    }
                    
                    // Actualizar contadores de caracteres
                    if (data.descripcion_grupo) {
                        $('#char-count-desc').text(data.descripcion_grupo.length);
                    }
                    if (data.observaciones_grupo) {
                        $('#char-count-obs').text(data.observaciones_grupo.length);
                    }
                    
                    // Capturar valores originales después de cargar datos
                    setTimeout(function() {
                        captureOriginalValues();
                    }, 100);
                    
                    // Enfocar el primer campo
                    $('#codigo_grupo').focus();
                    
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
                toastr.error('Error al obtener datos del grupo');
                // Redirigir al listado si hay error
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Verificar si estamos en modo edición
    const idGrupo = getUrlParameter('id');
    const modo = getUrlParameter('modo') || 'nuevo';
    
    if (modo === 'editar' && idGrupo) {
        cargarDatosGrupo(idGrupo);
    } else {
        // En modo nuevo, enfocar el primer campo y capturar valores iniciales
        $('#codigo_grupo').focus();
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

    /////////////////////////////////////////
    //   FIN FUNCIONES DE INICIALIZACIÓN  //
    ///////////////////////////////////////

    //*****************************************************/
    //   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR GRUPO
    //*****************************************************/

    $(document).on('click', '#btnSalvarGrupo', function (event) {
        event.preventDefault();

        // Obtener valores del formulario
        var id_grupoR = $('#id_grupo').val().trim();
        var codigo_grupoR = $('#codigo_grupo').val().trim();
        var nombre_grupoR = $('#nombre_grupo').val().trim();
        var descripcion_grupoR = $('#descripcion_grupo').val().trim();
        var observaciones_grupoR = $('#observaciones_grupo').val().trim();
        
        // El estado siempre será activo para nuevos grupos, o mantener el actual para edición
        var activo_grupoR;
        if (id_grupoR) {
            // En edición: mantener el estado actual (el que está en el checkbox)
            activo_grupoR = $('#activo_grupo').is(':checked') ? 1 : 0;
        } else {
            // Nuevo grupo: siempre activo
            activo_grupoR = 1;
        }

        // Validar el formulario
        if (!formValidator.validateForm(event)) {
            toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
            return;
        }
        
        // Verificar grupo primero
        verificarGrupoExistente(id_grupoR, codigo_grupoR, nombre_grupoR, descripcion_grupoR, observaciones_grupoR, activo_grupoR);
    });

    function verificarGrupoExistente(id_grupo, codigo_grupo, nombre_grupo, descripcion_grupo, observaciones_grupo, activo_grupo) {
        $.ajax({
            url: "../../controller/grupo_articulo.php?op=verificarGrupoArticulo",
            type: "GET",
            data: { 
                codigo_grupo: codigo_grupo,
                nombre_grupo: nombre_grupo,
                id_grupo: id_grupo 
            },
            dataType: "json",
            success: function(response) {
                if (!response.success) {
                    toastr.warning(response.message || "Error al verificar el grupo.");
                    return;
                }

                if (response.existe) {
                    mostrarErrorGrupoExistente(nombre_grupo);
                } else {
                    guardarGrupo(id_grupo, codigo_grupo, nombre_grupo, descripcion_grupo, observaciones_grupo, activo_grupo);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en verificación:', error);
                toastr.error('Error al verificar el grupo. Intente nuevamente.', 'Error');
            }
        });
    }

    function mostrarErrorGrupoExistente(nombre_grupo) {
        console.log("Grupo duplicado detectado:", nombre_grupo);
        Swal.fire({
            title: 'Nombre de grupo duplicado',
            text: 'El grupo "' + nombre_grupo + '" ya existe. Por favor, elija otro nombre.',
            icon: 'warning',
            confirmButtonText: 'Entendido'
        });
    }

    function guardarGrupo(id_grupo, codigo_grupo, nombre_grupo, descripcion_grupo, observaciones_grupo, activo_grupo) {
        // Mostrar indicador de carga
        $('#btnSalvarGrupo').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
        
        // Crear datos para enviar
        var formData = {
            codigo_grupo: codigo_grupo,
            nombre_grupo: nombre_grupo,
            descripcion_grupo: descripcion_grupo,
            observaciones_grupo: observaciones_grupo,
            activo_grupo: activo_grupo
        };
        
        if (id_grupo) {
            formData.id_grupo = id_grupo;
        }

        $.ajax({
            url: "../../controller/grupo_articulo.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            dataType: "json",
            success: function(res) {
                if (res.success) {
                    // Marcar como guardado para evitar la alerta de salida
                    formSaved = true;
                    
                    toastr.success(res.message || "Grupo guardado correctamente");
                    
                    // Redirigir al listado después de un breve delay
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(res.message || "Error al guardar el grupo");
                    // Restaurar botón
                    $('#btnSalvarGrupo').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Grupo');
                }
            },
            error: function(xhr, status, error) {
                console.error("Error en guardado:", error);
                Swal.fire('Error', 'No se pudo guardar el grupo. Error: ' + error, 'error');
                // Restaurar botón
                $('#btnSalvarGrupo').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Grupo');
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
            codigo_grupo: $('#codigo_grupo').val(),
            nombre_grupo: $('#nombre_grupo').val(),
            descripcion_grupo: $('#descripcion_grupo').val(),
            observaciones_grupo: $('#observaciones_grupo').val()
        };
    }
    
    // Verificar si el formulario ha cambiado
    function hasFormChanged() {
        return (
            $('#codigo_grupo').val() !== formOriginalValues.codigo_grupo ||
            $('#nombre_grupo').val() !== formOriginalValues.nombre_grupo ||
            $('#descripcion_grupo').val() !== formOriginalValues.descripcion_grupo ||
            $('#observaciones_grupo').val() !== formOriginalValues.observaciones_grupo
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

    /////////////////////////////////////////
    //   FIN FUNCIONES DE UTILIDAD        //
    ///////////////////////////////////////

    /////////////////////////////////////////
    //   INICIALIZACIÓN DEL FORMULARIO    //
    ///////////////////////////////////////
    
    // Inicialización según el modo (nuevo/editar)
    const urlParamsInit = new URLSearchParams(window.location.search);
    const idGrupoInit = urlParamsInit.get('id');
    const modoInit = urlParamsInit.get('modo') || 'nuevo';
    
    if (modoInit === 'editar' && idGrupoInit) {
        // Cargar datos
        setTimeout(function() {
            cargarDatosGrupo(idGrupoInit);
        }, 100);
    } else {
        // Capturar valores originales para formulario nuevo
        setTimeout(function() {
            captureOriginalValues();
        }, 100);
    }

}); // de document.ready

// Función global para cargar datos (llamada desde el HTML)
function cargarDatosGrupo(idGrupo) {
    console.log('Función global - Cargando datos de grupo ID:', idGrupo);
    // Esta función ya está implementada dentro del document.ready
}
