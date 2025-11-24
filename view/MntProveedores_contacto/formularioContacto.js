$(document).ready(function () {
    // Obtener parámetros de la URL
    const urlParams = new URLSearchParams(window.location.search);
    const modo = urlParams.get('modo');
    const idContacto = urlParams.get('id');
    const idProveedor = urlParams.get('id_proveedor');

    // Configurar la página según el modo
    if (modo === 'editar' && idContacto) {
        configurarModoEdicion(idContacto);
    } else {
        configurarModoNuevo();
    }

    // Cargar información del proveedor
    if (idProveedor) {
        cargarInfoProveedor(idProveedor);
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
        $('#page-title').text('Nuevo Contacto del Proveedor');
        $('#breadcrumb-title').text('Nuevo Contacto');
        $('#btnSalvarContacto').html('<i class="fas fa-save me-2"></i>Guardar Contacto');
        $('#estado_section').hide();
    }

    function configurarModoEdicion(id) {
        $('#page-title').text('Editar Contacto del Proveedor');
        $('#breadcrumb-title').text('Editar Contacto');
        $('#btnSalvarContacto').html('<i class="fas fa-save me-2"></i>Actualizar Contacto');
        $('#estado_section').show();
        
        // Cargar los datos del contacto
        cargarDatosContacto(id);
    }

    function cargarInfoProveedor(id_proveedor) {
        $.post("../../controller/proveedor.php?op=mostrar", { id_proveedor: id_proveedor }, function (data) {
            if (data) {
                $('#nombre-proveedor').text(data.nombre_proveedor || 'Sin nombre');
                $('#id-proveedor').text(id_proveedor);
            }
        }, 'json').fail(function() {
            $('#nombre-proveedor').text('Error al cargar proveedor');
        });
    }

    function cargarDatosContacto(id) {
        $.post("../../controller/proveedores_contacto.php?op=mostrar", { id_contacto_proveedor: id }, function (data) {
            if (data) {
                $('#id_contacto_proveedor').val(data.id_contacto_proveedor);
                $('#id_proveedor').val(data.id_proveedor);
                $('#nombre_contacto_proveedor').val(data.nombre_contacto_proveedor);
                $('#apellidos_contacto_proveedor').val(data.apellidos_contacto_proveedor);
                $('#cargo_contacto_proveedor').val(data.cargo_contacto_proveedor);
                $('#departamento_contacto_proveedor').val(data.departamento_contacto_proveedor);
                $('#telefono_contacto_proveedor').val(data.telefono_contacto_proveedor);
                $('#movil_contacto_proveedor').val(data.movil_contacto_proveedor);
                $('#email_contacto_proveedor').val(data.email_contacto_proveedor);
                $('#extension_contacto_proveedor').val(data.extension_contacto_proveedor);
                $('#principal_contacto_proveedor').prop('checked', data.principal_contacto_proveedor == 1);
                $('#observaciones_contacto_proveedor').val(data.observaciones_contacto_proveedor);
                
                // Configurar estado visual
                const esActivo = data.activo_contacto_proveedor == 1;
                $('#activo_contacto_proveedor_display').prop('checked', esActivo);
                $('#estado_texto').text(esActivo ? 'Contacto Activo' : 'Contacto Inactivo');
                $('#estado_descripcion').text(esActivo ? 'El contacto está activo y disponible.' : 'El contacto está inactivo.');
            }
        }, 'json').fail(function() {
            Swal.fire('Error', 'No se pudieron cargar los datos del contacto', 'error');
        });
    }

    function configurarValidaciones() {
        // Validación en tiempo real para nombre
        $('#nombre_contacto_proveedor').on('input blur', function() {
            validarCampo($(this), validarNombre);
        });

        // Validación en tiempo real para email
        $('#email_contacto_proveedor').on('input blur', function() {
            validarCampo($(this), validarEmail, false); // false = no obligatorio
        });

        // Validación en tiempo real para teléfonos
        $('#telefono_contacto_proveedor, #movil_contacto_proveedor').on('input blur', function() {
            validarCampo($(this), validarTelefono, false);
        });

        // Validación de contacto principal
        $('#principal_contacto_proveedor').on('change', function() {
            if ($(this).is(':checked')) {
                validarContactoPrincipal();
            }
        });

        // Validación de duplicados en nombre
        $('#nombre_contacto_proveedor').on('blur', function() {
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
            mostrarError($('#nombre_contacto_proveedor'), 'El nombre debe tener entre 2 y 100 caracteres');
            return false;
        }
        return true;
    }

    function validarEmail(email) {
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(email)) {
            mostrarError($('#email_contacto_proveedor'), 'Ingrese un email válido');
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
        const nombre = $('#nombre_contacto_proveedor').val().trim();
        const idContacto = $('#id_contacto_proveedor').val();
        const idProveedor = $('#id_proveedor').val();

        if (!nombre || !idProveedor) return;

        $.post("../../controller/proveedores_contacto.php?op=verificar", {
            nombre_contacto_proveedor: nombre,
            id_proveedor: idProveedor,
            id_contacto_proveedor: idContacto
        }, function(data) {
            if (data.existe) {
                mostrarError($('#nombre_contacto_proveedor'), 'Ya existe un contacto con este nombre para el proveedor');
            } else {
                mostrarExito($('#nombre_contacto_proveedor'));
            }
        }, 'json');
    }

    function validarContactoPrincipal() {
        const idContacto = $('#id_contacto_proveedor').val();
        const idProveedor = $('#id_proveedor').val();

        if (!idProveedor) return;

        $.post("../../controller/proveedores_contacto.php?op=verificar_principal", {
            id_proveedor: idProveedor,
            id_contacto_proveedor: idContacto
        }, function(data) {
            if (data.existe_principal) {
                Swal.fire({
                    title: 'Contacto Principal Existente',
                    html: 'Ya existe un contacto principal para este proveedor.<br><br>¿Desea que este contacto sea el nuevo principal?<br><small class="text-warning">El contacto principal anterior perderá esta condición.</small>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, hacer principal',
                    cancelButtonText: 'No, mantener como secundario',
                    reverseButtons: true
                }).then((result) => {
                    if (!result.isConfirmed) {
                        $('#principal_contacto_proveedor').prop('checked', false);
                    }
                });
            }
        }, 'json');
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
        
        esValido &= validarCampo($('#nombre_contacto_proveedor'), validarNombre);
        
        const email = $('#email_contacto_proveedor').val().trim();
        if (email) {
            esValido &= validarCampo($('#email_contacto_proveedor'), validarEmail, false);
        }

        const telefono = $('#telefono_contacto_proveedor').val().trim();
        if (telefono) {
            esValido &= validarCampo($('#telefono_contacto_proveedor'), validarTelefono, false);
        }

        const movil = $('#movil_contacto_proveedor').val().trim();
        if (movil) {
            esValido &= validarCampo($('#movil_contacto_proveedor'), validarTelefono, false);
        }

        if (!esValido) {
            Swal.fire('Error de validación', 'Por favor, corrija los errores en el formulario', 'warning');
            return;
        }

        // Recoger datos del formulario
        const formData = new FormData($('#formContacto')[0]);

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
            url: '../../controller/proveedores_contacto.php?op=guardaryeditar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json',
            success: function(response) {
                Swal.close();
                
                if (response.status === 'success') {
                    Swal.fire({
                        title: 'Éxito',
                        text: response.message,
                        icon: 'success',
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => {
                        // Redirigir al listado
                        window.location.href = `index.php?id_proveedor=${idProveedor}`;
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
    $('#nombre_contacto_proveedor, #apellidos_contacto_proveedor').on('input', function() {
        let valor = $(this).val();
        valor = valor.replace(/\b\w/g, function(l) { return l.toUpperCase(); });
        $(this).val(valor);
    });

    // Formatear teléfonos (quitar caracteres no válidos excepto los permitidos)
    $('#telefono_contacto_proveedor, #movil_contacto_proveedor, #extension_contacto_proveedor').on('input', function() {
        let valor = $(this).val();
        // Permitir solo números, espacios, guiones, paréntesis y símbolo +
        valor = valor.replace(/[^\d\s\-\+\(\)]/g, '');
        $(this).val(valor);
    });

    // Convertir email a minúsculas
    $('#email_contacto_proveedor').on('input', function() {
        $(this).val($(this).val().toLowerCase());
    });

});