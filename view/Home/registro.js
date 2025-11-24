$(document).ready(function () {

    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    /////////////////////////////////////

    var formValidator = new FormValidator('formRegistro', {
        nombre: {
            required: true
        },
        email: {
            pattern: '^[\\w.-]+@[a-zA-Z\\d.-]+\\.[a-zA-Z]{2,}$',
            required: true
        },
        contrasena: {
            pattern: '^(?=.*[A-Za-z])(?=.*\\d).{6,}$', // al menos una letra, un número y 6 caracteres
            required: true
        },
        confirmar_contrasena: {
            required: true
        }
    });

    $(document).on('click', '#enviar', async function (event) {
        const isValid = formValidator.validateForm(event);

        if (!isValid) {
            toastr.error('Formulario de registro NO válido.', 'Error de Validación');
            return;
        }

        // Validar coincidencia de contraseñas
        const contrasena = $('#contrasena').val();
        const confirmar = $('#confirmar_contrasena').val();
        if (contrasena !== confirmar) {
            toastr.error('Las contraseñas no coinciden.', 'Error');
            return;
        }

        const datosFormulario = {
            nombre: $('#nombre').val(),
            email: $('#email').val(),
            contrasena: contrasena
        };

        const formData = new FormData();
        for (let key in datosFormulario) {
            formData.append(key, datosFormulario[key]);
        }

        $.ajax({
            url: '../../controller/login.php?op=guardaryeditar',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            dataType: 'json', // 👈 esto
            success: function (response) {
                if (response.success === true) {
                    toastr.success('Usuario registrado correctamente.', 'Éxito');
                    setTimeout(() => {
                        window.location.href = 'index.php';
                    }, 1500);
                } else {
                    toastr.error(response.message || 'Error al registrar el usuario.', 'Error');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.log('Respuesta completa:', xhr.responseText);
            
                let mensajeError = 'Ocurrió un error al registrar el usuario.';
            
                try {
                    const respuesta = JSON.parse(xhr.responseText);
                    if (respuesta.message) {
                        mensajeError = respuesta.message;
                    } else if (respuesta.error) {
                        mensajeError = respuesta.error;
                    }
                } catch (e) {
                    console.warn('No se pudo parsear JSON de respuesta:', e);
                    // dejamos mensaje genérico si no es JSON válido
                }
            
                toastr.error(mensajeError, 'Error');
            }
        });              
    });

    //---------------------------------------
    //      VER CONTRASEÑA
    //--------------------------------------

    $('#verContrasena').on('click', function () {
        var passwordField = $('#contrasena');
        var passwordFieldType = passwordField.attr('type');
        if (passwordFieldType == 'password') {
            passwordField.attr('type', 'text');
            $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
        } else {
            passwordField.attr('type', 'password');
            $(this).html('<i class="fa fa-eye" aria-hidden="true"></i>');
        }
    });

    $('#verConfirmarContrasena').on('click', function () {
        var passwordField = $('#confirmar_contrasena');
        var passwordFieldType = passwordField.attr('type');
        if (passwordFieldType == 'password') {
            passwordField.attr('type', 'text');
            $(this).html('<i class="fa fa-eye-slash" aria-hidden="true"></i>');
        } else {
            passwordField.attr('type', 'password');
            $(this).html('<i class="fa fa-eye" aria-hidden="true"></i>');
        }
    });

});
