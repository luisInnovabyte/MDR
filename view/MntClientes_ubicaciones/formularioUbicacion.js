$(document).ready(function () {
    // Obtener parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const modo = urlParams.get('modo');
    const idUbicacion = urlParams.get('id');
    const idCliente = urlParams.get('id_cliente');

    // Configurar la página según el modo
    if (modo === 'editar' && idUbicacion) {
        configurarModoEdicion(idUbicacion);
    } else {
        configurarModoNuevo();
    }

    // Cargar información del cliente
    if (idCliente) {
        cargarInfoCliente(idCliente);
    }

    // Configurar validaciones del formulario
    configurarValidaciones();

    // Configurar el botón de guardar
    $('#btnSalvarUbicacion').on('click', function() {
        guardarUbicacion();
    });

    /////////////////////////////////////
    //   FUNCIONES DE CONFIGURACIÓN    //
    ///////////////////////////////////

    function configurarModoNuevo() {
        $('#page-title').text('Nueva Ubicación del Cliente');
        $('#breadcrumb-title').text('Nueva Ubicación');
        $('#btnSalvarUbicacion').html('<i class="fas fa-save me-2"></i>Guardar Ubicación');
        $('#estado_section').hide();
        
        // Establecer el id_cliente en el campo oculto
        if (idCliente) {
            $('#id_cliente').val(idCliente);
        }
    }

    function configurarModoEdicion(id) {
        $('#page-title').text('Editar Ubicación del Cliente');
        $('#breadcrumb-title').text('Editar Ubicación');
        $('#btnSalvarUbicacion').html('<i class="fas fa-save me-2"></i>Actualizar Ubicación');
        $('#estado_section').show();
        
        // Cargar los datos de la ubicación
        cargarDatosUbicacion(id);
    }

    function cargarInfoCliente(id_cliente) {
        $.post("../../controller/cliente.php?op=mostrar", { id_cliente: id_cliente }, function (data) {
            if (data) {
                // Nombre completo del cliente
                const nombreCompleto = (data.nombre_cliente || '') + ' ' + (data.apellido_cliente || '');
                $('#nombre-cliente').text(nombreCompleto.trim() || 'Sin nombre');
                
                // ID del cliente
                $('#id-cliente').text(id_cliente);
                
                // Email del cliente
                $('#email-cliente').text(data.email_cliente || '--');
                
                // Teléfono del cliente (priorizar móvil si existe, sino telefono fijo)
                const telefono = data.movil_cliente || data.telefono_cliente || '--';
                $('#telefono-cliente').text(telefono);
            }
        }, 'json').fail(function() {
            $('#nombre-cliente').text('Error al cargar cliente');
            $('#id-cliente').text('--');
            $('#email-cliente').text('--');
            $('#telefono-cliente').text('--');
        });
    }

    function cargarDatosUbicacion(id) {
        $.post("../../controller/ubicaciones.php?op=mostrar", { id_ubicacion: id }, function (data) {
            if (data) {
                $('#id_ubicacion').val(data.id_ubicacion);
                $('#id_cliente').val(data.id_cliente);
                $('#nombre_ubicacion').val(data.nombre_ubicacion);
                $('#direccion_ubicacion').val(data.direccion_ubicacion);
                $('#codigo_postal_ubicacion').val(data.codigo_postal_ubicacion);
                $('#poblacion_ubicacion').val(data.poblacion_ubicacion);
                $('#provincia_ubicacion').val(data.provincia_ubicacion);
                $('#pais_ubicacion').val(data.pais_ubicacion || 'España');
                $('#persona_contacto_ubicacion').val(data.persona_contacto_ubicacion);
                $('#telefono_contacto_ubicacion').val(data.telefono_contacto_ubicacion);
                $('#email_contacto_ubicacion').val(data.email_contacto_ubicacion);
                $('#es_principal_ubicacion').prop('checked', data.es_principal_ubicacion == 1);
                $('#observaciones_ubicacion').val(data.observaciones_ubicacion);
                
                // Configurar estado visual
                const esActivo = data.activo_ubicacion == 1;
                $('#activo_ubicacion_display').prop('checked', esActivo);
                $('#estado_texto').text(esActivo ? 'Ubicación Activa' : 'Ubicación Inactiva');
                $('#estado_descripcion').text(esActivo ? 'La ubicación está activa y disponible.' : 'La ubicación está inactiva.');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los datos de la ubicación', 'error');
        });
    }

    function configurarValidaciones() {
        // Validación en tiempo real para nombre
        $('#nombre_ubicacion').on('input blur', function() {
            validarCampo($(this), validarNombre);
        });

        // Validación en tiempo real para email
        $('#email_contacto_ubicacion').on('input blur', function() {
            validarCampo($(this), validarEmail, false); // false = no obligatorio
        });

        // Validación en tiempo real para teléfono
        $('#telefono_contacto_ubicacion').on('input blur', function() {
            validarCampo($(this), validarTelefono, false);
        });

        // Validación de ubicación principal
        $('#es_principal_ubicacion').on('change', function() {
            if ($(this).is(':checked')) {
                validarUbicacionPrincipal();
            }
        });

        // Validación de duplicados en nombre
        $('#nombre_ubicacion').on('blur', function() {
            validarDuplicadoUbicacion();
        });
    }

    /////////////////////////////////////
    //   FUNCIONES DE VALIDACIÓN       //
    ///////////////////////////////////

    function validarCampo($campo, funcionValidacion, obligatorio = true) {
        const valor = $campo.val().trim();
        
        if (obligatorio && !valor) {
            mostrarError($campo, 'Este campo es obligatorio');
            return false;
        }
        
        if (valor && !funcionValidacion(valor)) {
            return false;
        }
        
        mostrarExito($campo);
        return true;
    }

    function validarNombre(nombre) {
        if (nombre.length < 2 || nombre.length > 100) {
            mostrarError($('#nombre_ubicacion'), 'El nombre debe tener entre 2 y 100 caracteres');
            return false;
        }
        return true;
    }

    function validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(email)) {
            mostrarError($('#email_contacto_ubicacion'), 'Ingrese un email válido');
            return false;
        }
        return true;
    }

    function validarTelefono(telefono) {
        const regex = /^[\d\s\-\+\(\)]+$/;
        if (!regex.test(telefono)) {
            mostrarError($(event.target), 'Ingrese un teléfono válido (solo números, espacios, guiones y símbolos +, -, (, ))');
            return false;
        }
        return true;
    }

    function validarDuplicadoUbicacion() {
        const nombre = $('#nombre_ubicacion').val().trim();
        const idUbicacion = $('#id_ubicacion').val();
        const idCliente = $('#id_cliente').val();

        if (!nombre || !idCliente) return;

        $.post("../../controller/ubicaciones.php?op=verificarUbicacion", {
            nombre_ubicacion: nombre,
            id_cliente: idCliente,
            id_ubicacion: idUbicacion
        }, function(data) {
            if (data.existe) {
                mostrarError($('#nombre_ubicacion'), 'Ya existe una ubicación con este nombre para el cliente');
            } else {
                mostrarExito($('#nombre_ubicacion'));
            }
        }, 'json');
    }

    function validarUbicacionPrincipal() {
        const idUbicacion = $('#id_ubicacion').val();
        const idCliente = $('#id_cliente').val();

        if (!idCliente) return;

        // Mostrar aviso sobre ubicación principal
        if (idUbicacion) {
            Swal.fire({
                title: 'Ubicación Principal',
                html: 'Esta ubicación será marcada como principal.<br><small class="text-warning">Si ya existe otra ubicación principal, perderá esta condición.</small>',
                icon: 'info',
                confirmButtonText: 'Entendido'
            });
        }
    }

    function mostrarError($campo, mensaje) {
        $campo.addClass('is-invalid').removeClass('is-valid');
        $campo.siblings('.invalid-feedback').text(mensaje);
    }

    function mostrarExito($campo) {
        $campo.addClass('is-valid').removeClass('is-invalid');
    }

    /////////////////////////////////////
    //   FUNCIÓN GUARDAR UBICACIÓN     //
    ///////////////////////////////////

    function guardarUbicacion() {
        // Validar todos los campos
        let esValido = true;
        
        esValido &= validarCampo($('#nombre_ubicacion'), validarNombre);
        
        const email = $('#email_contacto_ubicacion').val().trim();
        if (email) {
            esValido &= validarCampo($('#email_contacto_ubicacion'), validarEmail, false);
        }

        const telefono = $('#telefono_contacto_ubicacion').val().trim();
        if (telefono) {
            esValido &= validarCampo($('#telefono_contacto_ubicacion'), validarTelefono, false);
        }

        if (!esValido) {
            Swal.fire('Error de validación', 'Por favor, corrija los errores en el formulario', 'warning');
            return;
        }

        // Recoger datos del formulario
        const formData = new FormData($('#frmUbicacion')[0]);

        // Convertir el checkbox a valor 1 o 0
        formData.set('es_principal_ubicacion', $('#es_principal_ubicacion').is(':checked') ? 1 : 0);

        // Mostrar loading
        Swal.fire({
            title: 'Guardando...',
            html: 'Procesando la información de la ubicación',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar datos
        $.ajax({
            url: '../../controller/ubicaciones.php?op=guardaryeditar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.success) {
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirigir al listado
                        window.location.href = `index.php?id_cliente=${idCliente}`;
                    });
                } else {
                    Swal.fire('Error', response.message, 'error');
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                console.error('Error:', xhr.responseText);
                Swal.fire('Error', 'Hubo un problema al guardar la ubicación', 'error');
            }
        });
    }

    /////////////////////////////////////
    //   FORMATEO DE CAMPOS            //
    ///////////////////////////////////

    // Convertir nombre a formato título
    $('#nombre_ubicacion, #persona_contacto_ubicacion').on('input', function() {
        let valor = $(this).val();
        valor = valor.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        $(this).val(valor);
    });

    // Formatear teléfono (quitar caracteres no válidos excepto los permitidos)
    $('#telefono_contacto_ubicacion').on('input', function() {
        let valor = $(this).val();
        // Permitir solo números, espacios, guiones, paréntesis y símbolo +
        valor = valor.replace(/[^\d\s\-\+\(\)]/g, '');
        $(this).val(valor);
    });

    // Convertir email a minúsculas
    $('#email_contacto_ubicacion').on('input', function() {
        $(this).val($(this).val().toLowerCase());
    });

});
