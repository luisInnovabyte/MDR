$(document).ready(function () {


    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formLogin', {
        email: {
            pattern: '^[\\w.-]+@[a-zA-Z\\d.-]+\\.[a-zA-Z]{2,}$',
            required: true
        },
        contrasena: {
            // 1 letra -- A, a
            pattern: '^(?=.*[A-Za-z])(?=.*\\d).{6,}$',
            required: true
        }
    });

    // CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
    $(document).on('click', '#enviar', async function (event) {
        // Validar el formulario usando FormValidator
        const isValid = formValidator.validateForm(event);

        // Si la validación falla, no enviar el formulario
        if (!isValid) {
            toastr.error(`Error en la validación....`, 'Error de Validación');
            //console.log('contraseña,', $('#contrasena').val());
            //console.log('email0,', $('#email').val());
            return; // Salir de la función si la validación falla
        }

        //alert("Entramos .....")
        // Obtener los valores del formulario
        var email = $('#email').val();
        var contrasena = $('#contrasena').val();

        // primero <<nombre del campo de la BD>>:<<nombre de la variable>
        var datosFormulario = {
            email: email,
            contrasena: contrasena
        };

        var formData = new FormData();
        // Agregar los datos al objeto FormData
        for (var key in datosFormulario) {
            formData.append(key, datosFormulario[key]);
        }

        // Enviar los datos al controlador para validar el usuario
        $.ajax({
            url: '../../controller/login.php?op=iniciarSesion',
            type: 'POST',
            data: formData,
            processData: false, // Evitar que jQuery procese los datos
            contentType: false,
            dataType: 'json', // Asegurar que se espera JSON
            success: function (response) {
                if (response.success) {
                    toastr.success('Login exitoso.', 'Éxito');
        
                    // Esperar 2 segundos antes de redirigir
                    setTimeout(() => {
                        //window.location.href = 'perfil.php'; // Cambia esto según el destino real
                        // ANTES REDIRIGIA A PERFIL, AHORA SE VA A REDIRIGIR A LOS GRÁFICOS
                        window.location.href = '../Dashboard/index.php';
                    }, 2000); // 2000 milisegundos = 2 segundos
                } else {
                    const mensaje = response.message || 'Usuario o contraseña incorrectos.';
                    toastr.error(mensaje, 'Error');
                }
            },
            error: function (xhr, status, error) {
                console.error('AJAX Error:', error);
                console.log('Respuesta completa:', xhr.responseText);
        
                let mensajeError = 'Ocurrió un error al validar el usuario.';
        
                try {
                    const respuesta = JSON.parse(xhr.responseText);
                    if (respuesta.message) {
                        mensajeError = respuesta.message;
                    } else if (respuesta.error) {
                        mensajeError = respuesta.error;
                    }
                } catch (e) {
                    console.warn('No se pudo parsear JSON de respuesta:', e);
                }
        
                toastr.error(mensajeError, 'Error');
            }
        });
        
              
    }); // del click
    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


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



}) //end of document ready