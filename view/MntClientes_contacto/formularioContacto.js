$(document).ready(function () {
    // Obtener parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const modo = urlParams.get('modo');
    const idContacto = urlParams.get('id');
    const idCliente = urlParams.get('id_cliente');

    // Configurar la página según el modo
    if (modo === 'editar' && idContacto) {
        configurarModoEdicion(idContacto);
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
    $('#btnSalvarContacto').on('click', function() {
        guardarContacto();
    });

    /////////////////////////////////////
    //   FUNCIONES DE CONFIGURACIÓN    //
    ///////////////////////////////////

    function configurarModoNuevo() {
        $('#page-title').text('Nuevo Contacto del Cliente');
        $('#breadcrumb-title').text('Nuevo Contacto');
        $('#btnSalvarContacto').html('<i class="fas fa-save me-2"></i>Guardar Contacto');
        $('#estado_section').hide();
    }

    function configurarModoEdicion(id) {
        $('#page-title').text('Editar Contacto del Cliente');
        $('#breadcrumb-title').text('Editar Contacto');
        $('#btnSalvarContacto').html('<i class="fas fa-save me-2"></i>Actualizar Contacto');
        $('#estado_section').show();
        
        // Cargar los datos del contacto
        cargarDatosContacto(id);
    }

    function cargarInfoCliente(id_cliente) {
        $.post("../../controller/cliente.php?op=mostrar", { id_cliente: id_cliente }, function (data) {
            if (data) {
                $('#nombre-cliente').text(data.nombre_cliente || 'Sin nombre');
                $('#id-cliente').text(id_cliente);
            }
        }, 'json').fail(function() {
            $('#nombre-cliente').text('Error al cargar cliente');
        });
    }

    function cargarDatosContacto(id) {
        $.post("../../controller/clientes_contacto.php?op=mostrar", { id_contacto_cliente: id }, function (data) {
            if (data) {
                $('#id_contacto_cliente').val(data.id_contacto_cliente);
                $('#id_cliente').val(data.id_cliente);
                $('#nombre_contacto_cliente').val(data.nombre_contacto_cliente);
                $('#apellidos_contacto_cliente').val(data.apellidos_contacto_cliente);
                $('#cargo_contacto_cliente').val(data.cargo_contacto_cliente);
                $('#departamento_contacto_cliente').val(data.departamento_contacto_cliente);
                $('#telefono_contacto_cliente').val(data.telefono_contacto_cliente);
                $('#movil_contacto_cliente').val(data.movil_contacto_cliente);
                $('#email_contacto_cliente').val(data.email_contacto_cliente);
                $('#extension_contacto_cliente').val(data.extension_contacto_cliente);
                $('#principal_contacto_cliente').prop('checked', data.principal_contacto_cliente == 1);
                $('#observaciones_contacto_cliente').val(data.observaciones_contacto_cliente);
                
                // Configurar estado visual
                const esActivo = data.activo_contacto_cliente == 1;
                $('#activo_contacto_cliente_display').prop('checked', esActivo);
                $('#estado_texto').text(esActivo ? 'Contacto Activo' : 'Contacto Inactivo');
                $('#estado_descripcion').text(esActivo ? 'El contacto está activo y disponible.' : 'El contacto está inactivo.');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los datos del contacto', 'error');
        });
    }

    function configurarValidaciones() {
        // Validación en tiempo real para nombre
        $('#nombre_contacto_cliente').on('input blur', function() {
            validarCampo($(this), validarNombre);
        });

        // Validación en tiempo real para email
        $('#email_contacto_cliente').on('input blur', function() {
            validarCampo($(this), validarEmail, false); // false = no obligatorio
        });

        // Validación en tiempo real para teléfonos
        $('#telefono_contacto_cliente, #movil_contacto_cliente').on('input blur', function() {
            validarCampo($(this), validarTelefono, false);
        });

        // Validación de contacto principal
        $('#principal_contacto_cliente').on('change', function() {
            if ($(this).is(':checked')) {
                validarContactoPrincipal();
            }
        });

        // Validación de duplicados en nombre
        $('#nombre_contacto_cliente').on('blur', function() {
            validarDuplicadoContacto();
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
            mostrarError($('#nombre_contacto_cliente'), 'El nombre debe tener entre 2 y 100 caracteres');
            return false;
        }
        return true;
    }

    function validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(email)) {
            mostrarError($('#email_contacto_cliente'), 'Ingrese un email válido');
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

    function validarDuplicadoContacto() {
        const nombre = $('#nombre_contacto_cliente').val().trim();
        const idContacto = $('#id_contacto_cliente').val();
        const idCliente = $('#id_cliente').val();

        if (!nombre || !idCliente) return;

        $.post("../../controller/clientes_contacto.php?op=verificarContactoCliente", {
            nombre_contacto_cliente: nombre,
            id_cliente: idCliente,
            id_contacto_cliente: idContacto
        }, function(data) {
            if (data.existe) {
                mostrarError($('#nombre_contacto_cliente'), 'Ya existe un contacto con este nombre para el cliente');
            } else {
                mostrarExito($('#nombre_contacto_cliente'));
            }
        }, 'json');
    }

    function validarContactoPrincipal() {
        const idContacto = $('#id_contacto_cliente').val();
        const idCliente = $('#id_cliente').val();

        if (!idCliente) return;

        // Nota: Este endpoint tendría que implementarse en el controlador si se desea la misma funcionalidad
        // Por ahora simplemente mostramos un aviso
        if (idContacto) {
            // Si está editando, podría verificar si ya hay un principal
            Swal.fire({
                title: 'Contacto Principal',
                html: 'Este contacto será marcado como principal.<br><small class="text-warning">Si ya existe otro contacto principal, perderá esta condición.</small>',
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
    //   FUNCIÓN GUARDAR CONTACTO      //
    ///////////////////////////////////

    function guardarContacto() {
        // Validar todos los campos
        let esValido = true;
        
        esValido &= validarCampo($('#nombre_contacto_cliente'), validarNombre);
        
        const email = $('#email_contacto_cliente').val().trim();
        if (email) {
            esValido &= validarCampo($('#email_contacto_cliente'), validarEmail, false);
        }

        const telefono = $('#telefono_contacto_cliente').val().trim();
        if (telefono) {
            esValido &= validarCampo($('#telefono_contacto_cliente'), validarTelefono, false);
        }

        const movil = $('#movil_contacto_cliente').val().trim();
        if (movil) {
            esValido &= validarCampo($('#movil_contacto_cliente'), validarTelefono, false);
        }

        if (!esValido) {
            Swal.fire('Error de validación', 'Por favor, corrija los errores en el formulario', 'warning');
            return;
        }

        // Recoger datos del formulario
        const formData = new FormData($('#formContacto')[0]);

        // Convertir el checkbox a valor 1 o 0
        formData.set('principal_contacto_cliente', $('#principal_contacto_cliente').is(':checked') ? 1 : 0);

        // Mostrar loading
        Swal.fire({
            title: 'Guardando...',
            html: 'Procesando la información del contacto',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Enviar datos
        $.ajax({
            url: '../../controller/clientes_contacto.php?op=guardaryeditar',
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
                Swal.fire('Error', 'Hubo un problema al guardar el contacto', 'error');
            }
        });
    }

    /////////////////////////////////////
    //   FORMATEO DE CAMPOS            //
    ///////////////////////////////////

    // Convertir nombre a formato título
    $('#nombre_contacto_cliente, #apellidos_contacto_cliente').on('input', function() {
        let valor = $(this).val();
        valor = valor.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        $(this).val(valor);
    });

    // Formatear teléfonos (quitar caracteres no válidos excepto los permitidos)
    $('#telefono_contacto_cliente, #movil_contacto_cliente, #extension_contacto_cliente').on('input', function() {
        let valor = $(this).val();
        // Permitir solo números, espacios, guiones, paréntesis y símbolo +
        valor = valor.replace(/[^\d\s\-\+\(\)]/g, '');
        $(this).val(valor);
    });

    // Convertir email a minúsculas
    $('#email_contacto_cliente').on('input', function() {
        $(this).val($(this).val().toLowerCase());
    });

});
