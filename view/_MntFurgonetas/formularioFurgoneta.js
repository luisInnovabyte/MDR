$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////////
    //   FUNCIONES DE INICIALIZACIÓN      //
    ///////////////////////////////////////

    // Formatear matrícula en mayúsculas automáticamente
    $('#matricula_furgoneta').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Formatear número de bastidor en mayúsculas
    $('#numero_bastidor_furgoneta').on('input', function() {
        $(this).val($(this).val().toUpperCase());
    });

    // Función para obtener parámetros de la URL
    function getUrlParameter(name) {
        name = name.replace(/[\[]/, '\\[').replace(/[\]]/, '\\]');
        var regex = new RegExp('[\\?&]' + name + '=([^&#]*)');
        var results = regex.exec(location.search);
        return results === null ? '' : decodeURIComponent(results[1].replace(/\+/g, ' '));
    }

    // Función para cargar datos de furgoneta para edición
    function cargarDatosFurgoneta(idFurgoneta) {
        console.log('Cargando datos de furgoneta ID:', idFurgoneta);
        
        $.ajax({
            url: "../../controller/furgoneta.php?op=mostrar",
            type: "POST",
            data: { id_furgoneta: idFurgoneta },
            dataType: "json",
            success: function(data) {
                try {
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log('Datos recibidos:', data);

                    // Llenar los campos del formulario
                    $('#id_furgoneta').val(data.id_furgoneta);
                    $('#matricula_furgoneta').val(data.matricula_furgoneta);
                    $('#marca_furgoneta').val(data.marca_furgoneta);
                    $('#modelo_furgoneta').val(data.modelo_furgoneta);
                    $('#anio_furgoneta').val(data.anio_furgoneta || '');
                    $('#numero_bastidor_furgoneta').val(data.numero_bastidor_furgoneta || '');
                    
                    // ITV y Seguros
                    $('#fecha_proxima_itv_furgoneta').val(data.fecha_proxima_itv_furgoneta || '');
                    $('#fecha_vencimiento_seguro_furgoneta').val(data.fecha_vencimiento_seguro_furgoneta || '');
                    $('#compania_seguro_furgoneta').val(data.compania_seguro_furgoneta || '');
                    $('#numero_poliza_seguro_furgoneta').val(data.numero_poliza_seguro_furgoneta || '');
                    
                    // Capacidad y Combustible
                    $('#capacidad_carga_kg_furgoneta').val(data.capacidad_carga_kg_furgoneta || '');
                    $('#capacidad_carga_m3_furgoneta').val(data.capacidad_carga_m3_furgoneta || '');
                    $('#tipo_combustible_furgoneta').val(data.tipo_combustible_furgoneta || '');
                    $('#consumo_medio_furgoneta').val(data.consumo_medio_furgoneta || '');
                    
                    // Mantenimiento
                    $('#taller_habitual_furgoneta').val(data.taller_habitual_furgoneta || '');
                    $('#telefono_taller_furgoneta').val(data.telefono_taller_furgoneta || '');
                    $('#kilometros_entre_revisiones_furgoneta').val(data.kilometros_entre_revisiones_furgoneta || '');
                    
                    // Estado y Observaciones
                    $('#estado_furgoneta').val(data.estado_furgoneta || 'operativa');
                    $('#observaciones_furgoneta').val(data.observaciones_furgoneta || '');
                    
                    // Actualizar título de la página
                    $('#page-title').text('Editar Furgoneta: ' + data.matricula_furgoneta);
                    $('#breadcrumb-title').text('Editar Furgoneta: ' + data.matricula_furgoneta);
                    $('#btnSalvarFurgoneta').html('<i class="fas fa-save me-2"></i>Actualizar Furgoneta');

                } catch (error) {
                    console.error('Error al cargar los datos:', error);
                    toastr.error('Error al cargar los datos de la furgoneta');
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 2000);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al cargar la furgoneta:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al cargar la furgoneta desde el servidor');
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 2000);
            }
        });
    }

    // Variable para controlar cambios en el formulario
    var formularioModificado = false;

    // Marcar formulario como modificado cuando cambie algún campo
    $('#formFurgoneta').on('change', 'input, select, textarea', function() {
        formularioModificado = true;
    });

    // Advertir al usuario si intenta salir con cambios sin guardar
    $(window).on('beforeunload', function() {
        if (formularioModificado) {
            return '¿Estás seguro de que deseas salir? Los cambios no guardados se perderán.';
        }
    });

    /////////////////////////////////////////
    //   CAPTURAR CLICK EN BOTÓN GUARDAR  //
    /////////////////////////////////////////

    $(document).on('click', '#btnSalvarFurgoneta', function(event) {
        event.preventDefault();

        // Validar campos obligatorios
        var matricula = $('#matricula_furgoneta').val().trim();
        var marca = $('#marca_furgoneta').val().trim();
        var modelo = $('#modelo_furgoneta').val().trim();
        var estado = $('#estado_furgoneta').val();

        if (!matricula || !marca || !modelo || !estado) {
            toastr.warning('Por favor, complete todos los campos obligatorios');
            return false;
        }

        // Verificar unicidad de la matrícula
        var idFurgoneta = $('#id_furgoneta').val();

        $.ajax({
            url: "../../controller/furgoneta.php?op=verificarFurgoneta",
            type: "POST",
            data: { 
                matricula_furgoneta: matricula,
                id_furgoneta: idFurgoneta
            },
            dataType: "json",
            success: function(response) {
                if (response.existe) {
                    toastr.error('La matrícula ya existe en el sistema. Por favor, ingrese una matrícula diferente.');
                    $('#matricula_furgoneta').addClass('is-invalid');
                    return false;
                } else {
                    $('#matricula_furgoneta').removeClass('is-invalid');
                    // Si no existe, proceder a guardar
                    guardarFurgoneta();
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al verificar la matrícula:', error);
                toastr.error('Error al verificar la matrícula en el servidor');
            }
        });
    });

    // Función para guardar la furgoneta
    function guardarFurgoneta() {
        // Mostrar indicador de carga
        $('#btnSalvarFurgoneta').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');

        // Recopilar datos del formulario
        var formData = new FormData($('#formFurgoneta')[0]);

        // Realizar la petición AJAX
        $.ajax({
            url: "../../controller/furgoneta.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false,
            contentType: false,
            dataType: "json",
            success: function(response) {
                console.log('Respuesta del servidor:', response);

                if (response.success) {
                    toastr.success(response.message || 'Furgoneta guardada correctamente');
                    formularioModificado = false;

                    // Redirigir al listado después de 1.5 segundos
                    setTimeout(function() {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Error al guardar la furgoneta');
                    $('#btnSalvarFurgoneta').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Furgoneta');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error al guardar:', error);
                console.error('Respuesta del servidor:', xhr.responseText);
                
                var errorMsg = 'Error al comunicarse con el servidor';
                try {
                    var errorResponse = JSON.parse(xhr.responseText);
                    if (errorResponse.message) {
                        errorMsg = errorResponse.message;
                    }
                } catch (e) {
                    console.error('Error al parsear respuesta de error:', e);
                }
                
                toastr.error(errorMsg);
                $('#btnSalvarFurgoneta').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Furgoneta');
            }
        });
    }

    /////////////////////////////////////////
    //   INICIALIZACIÓN SEGÚN EL MODO     //
    /////////////////////////////////////////

    // Detectar modo (nuevo o editar) desde la URL
    var modo = getUrlParameter('modo');
    var idFurgoneta = getUrlParameter('id');

    if (modo === 'editar' && idFurgoneta) {
        // Modo edición: cargar datos de la furgoneta
        cargarDatosFurgoneta(idFurgoneta);
    } else {
        // Modo nuevo: mantener valores por defecto
        $('#page-title').text('Nueva Furgoneta');
        $('#breadcrumb-title').text('Nueva Furgoneta');
    }

    /////////////////////////////////////////
    //   MANEJO DE ALERTAS DE FECHAS      //
    /////////////////////////////////////////

    // Validar fecha ITV al cambiar
    $('#fecha_proxima_itv_furgoneta').on('change', function() {
        var fechaITV = new Date($(this).val());
        var hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        var dias30 = new Date();
        dias30.setDate(hoy.getDate() + 30);
        
        if (fechaITV < hoy) {
            toastr.warning('La ITV está vencida. Por favor, actualice la fecha.');
        } else if (fechaITV < dias30) {
            toastr.info('La ITV vence en menos de 30 días.');
        }
    });

    // Validar fecha seguro al cambiar
    $('#fecha_vencimiento_seguro_furgoneta').on('change', function() {
        var fechaSeguro = new Date($(this).val());
        var hoy = new Date();
        hoy.setHours(0, 0, 0, 0);
        
        var dias30 = new Date();
        dias30.setDate(hoy.getDate() + 30);
        
        if (fechaSeguro < hoy) {
            toastr.warning('El seguro está vencido. Por favor, actualice la fecha.');
        } else if (fechaSeguro < dias30) {
            toastr.info('El seguro vence en menos de 30 días.');
        }
    });

}); // de document.ready
