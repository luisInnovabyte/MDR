$(document).ready(function () {

    /////////////////////////////////////
    //            TIPS                //
    ///////////////////////////////////
    // Ocultar dinámicamente la columna con índice 2 (tercera columna)
    // ----> $('#miTabla').DataTable().column(2).visible(false);

    /////////////////////////////////////
    //          FIN DE TIPS           //
    ///////////////////////////////////


    /////////////////////////////////////
    //     FORMATEO DE CAMPOS          //
    ///////////////////////////////////

    var formValidator = new FormValidator('formUsuario', {
        nombre: {
            required: true,
        },
        emailUsuario: {
            required: true,
            pattern: /^[^\s@]+@[^\s@]+\.[^\s@]+$/  // Regex para email válido
        },
        id_rol: {
            required: true
        }
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

    /////////////////////////////////////
    // INICIO DE LA TABLA DE ROLES //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_usuariosConfig = {
        //serverSide: true, // procesamiento del lado del servidor
        processing: true, // mostrar el procesamiento de la tabla
        layout: {
            bottomEnd: { // que elementos de la paginación queremos que aparezcan
                paging: {
                    firstLast: true,
                    numbers: false,
                    previousNext: true
                }
            }
        }, //
        language: {
            // Se hace para cambiar la paginación por flechas
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>', // Ícono de FontAwesome
                last: '<i class="bi bi-chevron-double-right"></i>', // Ícono de FontAwesome
                previous: '<i class="bi bi-chevron-compact-left"></i>', // Ícono de FontAwesome
                next: '<i class="bi bi-chevron-compact-right"></i>'  // Ícono de FontAwesome
            }
        }, // de la language
        columns: [

            // Son los botones para más
            // No tocar
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }, // Columna 0: Mostrar más
            { name: 'id_usuario', data: 'id_usuario', visible: false, className: "text-center" }, // Columna 1: ID_USUARIO
            { name: 'nombre', data: 'nombre' , className: "text-center" }, // Columna 2: NOMBRE_COMPLETO_USUARIO
            { name: 'email', data: 'email', className: "text-center" }, // Columna 1: ID_USUARIO
            { name: 'nombre_rol', data: 'nombre_rol', className: "text-center"  }, // Columna 3: ROL
            { name: 'est', data: 'est', className: "text-center"  }, // Columna 4: ESTADO
            { name: 'activar', data: null, className: "text-center" }, // Columna 5: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center"  },  // Columna 6: EDITAR
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
           
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_rol 
            { targets: "id_usuario:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: NOMBRE 
            { targets: "nombre:name", width: '20%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: EMAIL
            { targets: "email:name", width: '20%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: nombre_rol
            {
                targets: 'id_rol:name',
                width: '10%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display") {
                        return row.nombre_rol; // Solo para visualización
                    }
                    // Para filtrado, ordenamiento y procesamiento interno usa el ID + nombre
                    return row.id_rol + "|" + row.nombre_rol;
                }
            },
            // Columna 5: est
            {
                targets: "est:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.est == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.est;
                }
            },
            // Columna 6: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '15%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.est == 1) {
                        // permito desactivar el rol
                        return `<button type="button" class="btn btn-danger btn-sm desacUsuario" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_usuario="${row.id_usuario}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        // debo permitir activar de nuevo el rol
                        return `<button class="btn btn-success btn-sm activarUsuario" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_usuario="${row.id_usuario}"> <!-- Cambiado de data-id a data-prod_id -->
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },// 
            // Columna 7: BOTON PARA EDITAR USUARIO
            {   
                targets: "editar:name", width: '15%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar el producto
                    return `<button type="button" class="btn btn-info btn-sm editarUsuario" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_usuario="${row.id_usuario}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            }
             // De la columna 9
        ], // de la columnDefs
        ajax: {
            url: '../../controller/login.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
                return json.data || json; // Ajusta en función de lo recibido
            }
        } // del ajax
    }; // de la variable datatable_companiesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE    //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de empleados
    var $table = $('#usuarios_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_usuariosConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#usuarios_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#usuarios_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    var table_e = $table.DataTable($tableConfig);

    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/

    function format(d) {
        console.log(d);
    
        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-gear-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Usuario</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Rol
                                </th>
                                <td class="pe-4">
                                    ${d.id_usuario || '<span class="text-muted fst-italic">No tiene id usuario</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-person-badge me-2"></i>Nombre Completo usuario
                                </th>
                                <td class="pe-4">
                                    ${d.nombre || '<span class="text-muted fst-italic">No tiene nombre</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-person-badge me-2"></i>Fecha de creación de usuario
                                </th>
                                <td class="pe-4">
                                    ${formatoFechaEuropeo(d.fecha_crea) || '<span class="text-muted fst-italic">No tiene fecha de creación</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-person-badge me-2"></i>ID Rol
                                </th>
                                <td class="pe-4">
                                    ${d.id_rol || '<span class="text-muted fst-italic">No tiene id de rol</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-person-badge me-2"></i>Rol de usuario
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_rol || '<span class="text-muted fst-italic">No tiene rol de usuario</span>'}
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Actualizado: ${new Date().toLocaleDateString()}</small>
                </div>
            </div>
        `;
    }
    
    
        // NO TOCAR, se configura en la parte superior --> funcion format(d)
        $tableBody.on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table_e.row(tr);
    
            if (row.child.isShown()) {
                // Esta fila ya está abierta, la cerramos
                row.child.hide();
                tr.removeClass('shown');
            } else {
                // Abrir esta fila
                row.child(format(row.data())).show();
                tr.addClass('shown');
            }
        });

    ////////////////////////////////////////////
    //   INICIO ZONA FUNCIONES DE APOYO      //
    //////////////////////////////////////////

    
    ////////////////////////////////////////////////////
    //     MÉTODO PARA CARGAR ROLES EN MODAL          //
    ///////////////////////////////////////////////////

    function configurarSelect2Roles(selector = '#id_rol') {
    $(selector).select2({
        width: '100%',
        dropdownParent: $('#modalUsuario .modal-content'),
        dropdownPosition: 'below',
        dropdownAutoWidth: true,
        placeholder: 'Seleccione un rol',
        allowClear: true,
        language: {
            noResults: function () {
                return "No hay roles disponibles";
            }
        }
    });
}

    function cargarRolesEnSelect(selectId, idRolSeleccionado) {
    $.post("../../controller/roles.php?op=listarDisponibles", function (data) {
        const jsondata = data;
        var select = $(selectId);

        // Limpiar las opciones existentes
        select.empty();

        // Agregar la opción por defecto
        select.append($('<option>', { value: '', text: 'Seleccione un rol...' }));

        if (data) {
            if (typeof data === 'string') {
                try {
                    data = JSON.parse(data);
                } catch (e) {
                    console.error('Error al parsear JSON:', e);
                }
            }
        }

            $.each(jsondata.data, function (index, rol) {
                let selected = (idRolSeleccionado != null && rol.id_rol == idRolSeleccionado) ? 'selected' : '';
                var optionHtml = '<option value="' + rol.id_rol + '" ' + selected + '>' + rol.nombre_rol + '</option>';
                select.append(optionHtml);
            });
        }, "json").fail(function (xhr, status, error) {
            console.error("Error al cargar los roles:", error);
        });
    }

    function validarContrasena(contrasena, confirmarContrasena, inputContrasenaSelector, inputConfirmarSelector) {
    // Limpiar estados anteriores
    $(inputContrasenaSelector + ', ' + inputConfirmarSelector).removeClass('is-invalid is-valid');
    $(inputContrasenaSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback').hide();
    $(inputConfirmarSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback').hide();

    let esValido = true;

    // Validar contraseña principal
    if (!contrasena) {
        $(inputContrasenaSelector).addClass('is-invalid');
        $(inputContrasenaSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback')
            .text("La contraseña es obligatoria").show();
        esValido = false;
    } else if (contrasena.length < 10) {
        $(inputContrasenaSelector).addClass('is-invalid');
        $(inputContrasenaSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback')
            .text("Debe tener al menos 10 caracteres").show();
        esValido = false;
    } else if (!/[A-Z]/.test(contrasena)) {
        $(inputContrasenaSelector).addClass('is-invalid');
        $(inputContrasenaSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback')
            .text("Debe contener al menos una mayúscula").show();
        esValido = false;
    } else if (!/\d/.test(contrasena)) {
        $(inputContrasenaSelector).addClass('is-invalid');
        $(inputContrasenaSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback')
            .text("Debe contener al menos un número").show();
        esValido = false;
    } else if (!/[@$!%*?&]/.test(contrasena)) {
        $(inputContrasenaSelector).addClass('is-invalid');
        $(inputContrasenaSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback')
            .text("Debe contener al menos un carácter especial (@$!%*?&)").show();
        esValido = false;
    } else {
        $(inputContrasenaSelector).addClass('is-valid');
    }

    // Validar confirmación
    if (!confirmarContrasena) {
        $(inputConfirmarSelector).addClass('is-invalid');
        $(inputConfirmarSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback')
            .text("Por favor confirme la contraseña").show();
        esValido = false;
    } else if (contrasena !== confirmarContrasena) {
        $(inputConfirmarSelector).addClass('is-invalid');
        $(inputConfirmarSelector).closest('.col-12, .col-lg-6').find('.invalid-feedback')
            .text("Las contraseñas no coinciden").show();
        esValido = false;
    } else {
        $(inputConfirmarSelector).addClass('is-valid');
    }

    return esValido;
}

// Validación en tiempo real
$(document).on('input', '#contrasena, #confirmar_contrasena', function() {
    const contrasenaActual = $('#contrasena').val();
    const confirmacionActual = $('#confirmar_contrasena').val();

    if (contrasenaActual || confirmacionActual) {
        validarContrasena(contrasenaActual, confirmacionActual, '#contrasena', '#confirmar_contrasena');
    } else {
        $('#contrasena, #confirmar_contrasena').removeClass('is-invalid is-valid');
        $('#contrasena').closest('.col-12, .col-lg-6').find('.invalid-feedback').hide();
        $('#confirmar_contrasena').closest('.col-12, .col-lg-6').find('.invalid-feedback').hide();
    }
});

// Mostrar/ocultar contraseña
$('#verContraseña').on('click', function () {
    const input = $('#contrasena');
    const tipo = input.attr('type') === 'password' ? 'text' : 'password';
    input.attr('type', tipo);
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
});

$('#verConfirmarContraseña').on('click', function () {
    const input = $('#confirmar_contrasena');
    const tipo = input.attr('type') === 'password' ? 'text' : 'password';
    input.attr('type', tipo);
    $(this).find('i').toggleClass('fa-eye fa-eye-slash');
});


    /////////////////////////////////////
    //   INICIO ZONA DELETE ESTADOS  //
    ///////////////////////////////////
    function desacUsuario(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar al usuario con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/login.php?op=eliminar", { id_usuario: id }, function (data) { // Cambiado a prod_id

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Desactivado',
                        'El usuario ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacUsuario', function (event) {
        event.preventDefault();
        let id = $(this).data('id_usuario'); // Cambiado de data('id') a data('prod_id')
        desacUsuario(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE ROL    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR ROL  //
    /////////////////////////////////////
    function activarUsuario(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar al usuario con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/login.php?op=activar", { id_usuario: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Activado',
                        'El usuario ha sido activado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarUsuario', function (event) { // Sin acento
        event.preventDefault();
        let id = $(this).data('id_usuario');
        activarUsuario(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR USUARIO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE NUEVO
    $(document).on('click', '#btnnuevo', function (event) {
        event.preventDefault();
        $('#mdltitulo').text('Nuevo registro de usuario');

        $('#modalUsuario').modal('show');

        // Limpiar el formulario
        $("#formUsuario")[0].reset();

        // Limpiar contraseñas y mensajes de error
        $('#contrasena, #confirmar_contrasena')
        .removeClass('is-invalid is-valid')
        .val('')
        .closest('.input-group')
        .next('.invalid-feedback').hide();

        // RESETEAR ID USUARIO
        $('#formUsuario').find('input[name="id_usuario"]').val("");

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        cargarRolesEnSelect('#formUsuario select[name="id_rol"]');
        configurarSelect2Roles();

        // Mostrar el modal con el foco en el primer campo (nombre)
        $('#modalUsuario').on('shown.bs.modal', function () {
            $('#modalUsuario .modal-body #nombre').focus();
        });

        //console.log('Modal mostrado');
    });


// TENER ESTE CÓDIGO EN CUENTA PARA GUIARSE Y HACER IGUAL EL FUNCIONAMIENTO DE LAS CONTRASEÑAS PARA USUARIOS
/* 
// CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
$(document).on('click', '#btnsalvar', function (event) {
    event.preventDefault();

    // OBTENER EL FORMULARIO Y SUS VALORES
    var form = $('#formUsuario');
    var idE = form.find('input[name="id"]').val().trim();
    var esNuevo = idE === "";  // DETERMINAR SI ES UN NUEVO USUARIO SI TIENE O NO UN ID

    var nombreUsuarioE = form.find('input[name="nombre_usuario"]').val().trim();
    var nombreE = form.find('input[name="nombre"]').val().trim();
    var apellidosE = form.find('input[name="apellidos"]').val().trim();
    var emailE = form.find('input[name="email"]').val().trim();
    var telefonoE = form.find('input[name="telefono"]').val().trim();
    var contrasenaE = form.find('input[name="contraseña"]').val().trim();
    var confirmarContrasenaE = form.find('input[name="confirmar_contraseña"]').val().trim();

    // VALIDAR LAS CONTRASEÑAS
    var esContrasenaValida = true;
    if (esNuevo || contrasenaE !== "") {
        esContrasenaValida = validarContrasena(contrasenaE, confirmarContrasenaE, '#contraseña', '#confirmar_contraseña');
    }

    // VALIDAR EL FORMULARIO
    var esFormularioValido = formValidator.validateForm(event);

    // SI HAY ALGÚN ERROR EN LA VALIDACIÓN, SE MUESTRA UN TOAST Y NO SE CONTINÚA
    if (!esFormularioValido || !esContrasenaValida) {
        toastr.error("Corrija los errores en el formulario", "Error de Validación");
        return;
    }

    // AJAX PARA VALIDAR QUE LOS CAMPOS USUARIO, TELÉFONO Y EMAIL SON ÚNICOS
    $.ajax({
        url: base_url + "Admin/validarCamposUnicos",
        type: "POST",
        data: { id: idE, nombre_usuario: nombreUsuarioE, email: emailE, telefono: telefonoE },
        success: function (validacion) {
            if (!validacion.success) {
                let errores = [];
                if (validacion.error_nombre_usuario) {
                    $('#nombre_usuario').addClass('is-invalid');
                    $('#error-nombre_usuario').text(validacion.error_nombre_usuario).show();
                    errores.push(validacion.error_nombre_usuario);
                }
                if (validacion.error_email) {
                    $('#email').addClass('is-invalid');
                    $('#error-email').text(validacion.error_email).show();
                    errores.push(validacion.error_email);
                }
                if (validacion.error_telefono) {
                    $('#telefono').addClass('is-invalid');
                    $('#error-telefono').text(validacion.error_telefono).show();
                    errores.push(validacion.error_telefono);
                }

                if (errores.length) {
                    toastr.error(errores.join('<br>'), 'Errores');
                }
                return;
            }

            // SI TODO SALE CORRECTO, SE PREPARA EL OBJETO PARA CREAR/EDITAR AL USUARIO
            var formData = new FormData();
            formData.append("id", idE);
            formData.append("nombre_usuario", nombreUsuarioE);
            formData.append("nombre", nombreE);
            formData.append("apellidos", apellidosE);
            formData.append("email", emailE);
            formData.append("telefono", telefonoE);

            if (esNuevo || contrasenaE !== "") {
                formData.append("contraseña", contrasenaE);
                formData.append("confirmar_contraseña", confirmarContrasenaE);
            }

            // SE MONTA LA ULR PARA O CREAR/EDITAR AL USUARIO
            var urlAccion = esNuevo ? base_url + "Admin/crearUsuario" : base_url + "Admin/editarUsuario/" + idE;
            // AJAX PARA CREAR/EDITAR AL USUARIO
            $.ajax({
                url: urlAccion,
                type: "POST",
                data: formData,
                processData: false,
                contentType: false,
                success: function (response) {
                    if (response.success) {
                        // SI SE CREA EL USUARIO, AHORA SE PREPARA EL CORREO PARA NOTIFICAR DE SU ALTA/MODIFICACIÓN
                        let asunto = esNuevo ? "Bienvenido a la plataforma" : "Actualización de sus datos";
                        let mensajeTexto = 
                        Hola ${nombreE},

                        ${esNuevo ? "Se ha creado tu usuario en la plataforma con los siguientes datos:" : "Se han actualizado tus datos de usuario con la siguiente información:"}

                        - Nombre de usuario: ${nombreUsuarioE}
                        - Nombre: ${nombreE}
                        - Apellidos: ${apellidosE}
                        - Email: ${emailE}
                        - Teléfono: ${telefonoE}
                        ${esNuevo ? - Contraseña temporal: ${contrasenaE}\n\n⚠️ Por favor cambia tu contraseña lo antes posible desde tu perfil. : ""}
                        ${(!esNuevo && contrasenaE !== "") ? "- Se ha actualizado tu contraseña.\n" : ""}

                        Si no realizaste esta acción, contacta con el administrador.

                        Este es un mensaje automático. No respondas a este correo.
                        ;

                        // EN CASO DE CREACIÓN, EL ADMIN LE ASIGNA LA CONTRASEÑA, Y SE LA ENSEÑA AL USUARIO QUE HA CREADO, Y LE PIDE QUE CAMBIE LA CONTRASEÑA AL INICIAR SESIÓN
                        const emailPayload = {
                            email: emailE,
                            nombre: nombreE,
                            asunto: asunto,
                            mensaje: mensajeTexto
                        };
                        // AJAX PARA NOTIFICAR AL USUARIO DE SU NUEVA CUENTA/EDICIÓN, Y PASAR SUS NUEVAS CREDENCIALES
                        $.ajax({
                            url: base_url + "Contactos/enviar",
                            method: "POST",
                            contentType: "application/json",
                            data: JSON.stringify(emailPayload),
                            dataType: "json",
                            success: function() {
                                $('#modalUsuario').modal('hide');
                                $table.DataTable().ajax.reload();
                                form[0].reset();

                                if (esNuevo) {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Usuario creado y correo enviado',
                                        html: 'El usuario ha sido creado correctamente.<br>Se ha enviado un correo con su contraseña temporal.',
                                    });
                                } else {
                                    Swal.fire({
                                        icon: 'success',
                                        title: 'Usuario actualizado correctamente y correo enviado',
                                        html: 'El usuario ha sido actualizado correctamente.',
                                    });
                                }
                            },
                            error: function() {
                                $('#modalUsuario').modal('hide');
                                $table.DataTable().ajax.reload();
                                form[0].reset();

                                toastr.warning("Usuario guardado pero no se pudo enviar el correo de notificación");
                            }
                        });

                    } else {
                        swal.fire('Error', response.errors.join('<br>'), 'error');
                    }
                },
                error: function () {
                    swal.fire('Error', 'Error al guardar', 'error');
                }
            });
        },
        error: function () {
            swal.fire('Error', 'Error al validar datos', 'error');
        }
    });
});
*/

// CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
$(document).on('click', '#btnGuardarUsuario', function (event) {
    event.preventDefault();

    const id_usuario = $('#id_usuario').val().trim();
    const nombre = $('#nombre').val().trim();
    const contrasena = $('#contrasena').val().trim();
    const confirmarContrasena = $('#confirmar_contrasena').val().trim();
    const email = $('#emailUsuario').val().trim();
    const id_rol = $('#id_rol').val();

    const esNuevo = id_usuario === "";

    // Validar contraseñas si es nuevo o si hay una nueva contraseña
    let esContrasenaValida = true;
    if (esNuevo || contrasena !== "" || confirmarContrasena !== "") {
        esContrasenaValida = validarContrasena(contrasena, confirmarContrasena, '#contrasena', '#confirmar_contrasena');
    }

    // Validar el formulario con tu validador
    if (!formValidator.validateForm(event) || !esContrasenaValida) {
        toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
        return;
    }

    // Verificar si el email ya existe
    $.ajax({
        url: "../../controller/login.php?op=verificarCorreo",
        type: "POST",
        data: { email: email, id_usuario: id_usuario },
        dataType: "json",
        success: function(response) {
            if (!response.success) {
                toastr.warning(response.message || "Error al verificar el correo.");
                return;
            }

            if (response.existe) {
                Swal.fire({
                    title: 'Correo electrónico duplicado',
                    text: 'El correo "' + email + '" ya está registrado. Por favor, utilice otro.',
                    icon: 'warning',
                    confirmButtonText: 'Entendido'
                });
                return;
            }

            // Preparar datos para guardar
            const datos = {
                id_usuario: id_usuario,
                nombre: nombre,
                email: email,
                id_rol: id_rol
            };

            if (esNuevo || contrasena !== "") {
                datos.contrasena = contrasena;
            }

            // Enviar al backend
            $.ajax({
                url: "../../controller/login.php?op=guardaryeditar",
                type: "POST",
                data: datos,
                dataType: "json",
                success: function(res) {
                    if (res.success) {
                        $('#modalUsuario').modal('hide');
                        table_e.ajax.reload(null, false);
                        $("#formUsuario")[0].reset();
                        toastr.success(res.message || "Usuario guardado correctamente");
                    } else {
                        toastr.error(res.message || "Error al guardar el usuario");
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire('Error', 'No se pudo guardar el usuario. Error: ' + error, 'error');
                }
            });
        },
        error: function(xhr, status, error) {
            toastr.error('Error al verificar el usuario. Intente nuevamente.', 'Error');
            console.error('Error en verificación:', error);
        }
    });
});




    
    ///////////////////////////////////////
    //      FIN ZONA NUEVO           //
    /////////////////////////////////////


    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
$(document).on('click', '.editarUsuario', function (event) {
    event.preventDefault();
    formValidator.clearValidation();

    let id_usuario = $(this).data('id_usuario');
    console.log("ID usuario:", id_usuario);

    $.ajax({
        url: "../../controller/login.php?op=mostrar",
        type: "POST",
        data: { id_usuario: id_usuario },
        dataType: "json",
        success: function(data) {
            try {

                console.log(data);

                // Configuramos el modal
                $('#mdltitulo').text('Edición registro usuario');

                // Llenamos los campos del formulario
                $('#formUsuario').find('input[name="id_usuario"]').val(data.id_usuario);
                $('#formUsuario input[name="nombre"]').val(data.nombre);
                // Limpiar contraseñas y mensajes de error
                $('#contrasena, #confirmar_contrasena')
                .removeClass('is-invalid is-valid')
                .val('')
                .closest('.input-group')
                .next('.invalid-feedback').hide();

                $('#formUsuario input[name="emailUsuario"]').val(data.email);
                cargarRolesEnSelect('#formUsuario select[name="id_rol"]', data.id_rol);
                configurarSelect2Roles();

                // Mostramos el modal
                $('#modalUsuario').modal('show');

            } catch (e) {
                console.error('Error al procesar datos:', e);
                toastr.error('Error al cargar datos para edición');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
            toastr.error('Error al obtener datos del usuario');
        }
    });
});

    ///////////////////////////////////////
    //        FIN ZONA EDITAR           //
    /////////////////////////////////////


    ////////////////////////////////////////////////////////
    //        ZONA FILTROS RADIOBUTTON CABECERA           //
    ///////////////////////////////////////////////////////
    // Escuchar cambios en los radio buttons
    // Si es necesario filtrar por texto en lugar de valores numéricos, hay que asegurarse que los valores de los radio buttons coincidan con los valores de la columna.
    $('input[name="filterStatus"]').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado

        if (value === "all") {
            // Si se selecciona "Todos", limpiar el filtro
            table_e.column(5).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(5).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

        }
    });
    ////////////////////////////////////////////////////////////
    //        FIN ZONA FILTROS RADIOBUTTON CABECERA          //
    //////////////////////////////////////////////////////////
    ////////////////////////////////////////////////
    //        ZONA FILTRO DE LA FECHA            //
    ///////////////////////////////////////////////

    ////////////////////////////////////////////////
    //        FECHA DE INICIO FILTRO           //
    ///////////////////////////////////////////////

    ////////////////////////////////////////////////
    //     FIN ZONA FILTRO DE LA FECHA           //
    ///////////////////////////////////////////////


    /*********************************************************** */
    /********************************************************** */
    /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
    /******************************************************** */
    /******************************************************* */

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    //var table_e = $table.DataTable($tableConfig);

    /////////////////////////////////////
    //  INICIO ZONA CLICS COLUMNA     //
    //    NO ES NECESARIO TOCAR      // 
    //////////////////////////////////
    //Código para capturar clics solo en la tercera columna (edad) y filtrar DataTables
    // El resto no responden al clic
    //ejemplo - $('#employees-table tbody').on('click', 'td', function () {

    // En caso de no querer que se filtre por columna se puede comentar o eliminar

    /*  En este caso no deseamos buscar por ninguna columna al hacer clic
        $tableBody.on('click', 'td', function () {
            var cellIndex = table_e.cell(this).index().column; // Índice real de la columna en DataTables
     
            // ejemplo - if (cellIndex === 3) { // Asegúrarse de que es la columna 'edad' 
            if (cellIndex === $columSearch) { // Asegúrarse de que es la columna 'edad' 
                var cellValue = $(this).text().trim();
                table_e.search(cellValue).draw();
                updateFilterMessage(); // Actualizar el mensaje cuando se aplique el filtro
            }
        });
    */
    /////////////////////////////////////
    //  FIN ZONA CLICS COLUMNA     //
    ///////////////////////////////////

    ////////////////////////////////////////////
    //  INICIO ZONA FILTROS PIES y SEARCH     //
    //    NO ES NECESARIO TOCAR              //
    //     FUNCIONES NO TOCAR               // 
    ///////////////////////////////////////////

    /* IMPORTANTE ---- IMPORTANTE ---- IMPORTANTE ---- IMPORTANTE */
    /* Si algún campo no quiere que se habilite en el footer la busqueda, 
    bastará con poner en el columnDefs -- > searchable: false */

    // Filtro de cada columna en el pie de la tabla de empleados (tfoot)
    // ejemplo - $('#employees-table tfoot input').on('keyup', function () {
    $columnFilterInputs.on('keyup', function () {
        var columnIndex = table_e.column($(this).closest('th')).index(); // Obtener el índice de la columna del encabezado correspondiente
        var searchValue = $(this).val(); // Obtener el valor del campo de búsqueda

        // Aplicar el filtro a la columna correspondiente
        table_e.column(columnIndex).search(searchValue).draw();

        // Actualizar el mensaje de filtro
        updateFilterMessage();
    });

    // Función para actualizar el mensaje de filtro activo
    function updateFilterMessage() {
        var activeFilters = false;

        // Revisamos si hay algún filtro activo en cualquier columna
        $columnFilterInputs.each(function () {
            if ($(this).val() !== "") {
                activeFilters = true;
                return false; // Si encontramos un filtro activo, salimos del loop
            }
        });

        // Revisamos si hay un filtro activo en la búsqueda global
        if (table_e.search() !== "") {
            activeFilters = true;
        }

        // Muestra u oculta el mensaje "Hay un filtro activo"
        if (activeFilters) {
            $('#filter-alert').show();
        } else {
            $('#filter-alert').hide();
        }
    }

    // Esto es solo valido para la busqueda superior //
    table_e.on('search.dt', function () {
        updateFilterMessage(); // Actualizar mensaje de filtro
    });
    ////////////////////////////////////////////////////////

    // Botón para limpiar los filtros y ocultar el mensaje ////////////////////////////////////////////
    $('#clear-filter').on('click', function () {
        //console.log('Limpiando filtros...');
        table_e.destroy();  // Destruir la tabla para limpiar los filtros

        // Limpiar los campos de búsqueda del pie de la tabla
        // ejemplo - $('#employees-table tfoot input').each(function () {
        $columnFilterInputs.each(function () {
            //console.log('Campo:', $(this).attr('placeholder'), 'Valor antes:', $(this).val());
            $(this).val('');  // Limpiar cada campo input del pie y disparar el evento input
            //console.log('Valor después:', $(this).val());
        });

        table_e = $table.DataTable($tableConfig);

        // Ocultar el mensaje de "Hay un filtro activo"
        $('#filter-alert').hide();
    });
    ////////////////////////////////////////////
    //  FIN ZONA FILTROS PIES y SEARCH     //
    ///////////////////////////////////////////
}); // de document.ready

// Función global para formatear fecha al formato europeo
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return 'Sin fecha';
    
    try {
        const fecha = new Date(fechaString);
        if (isNaN(fecha.getTime())) return 'Fecha inválida';
        
        const dia = fecha.getDate().toString().padStart(2, '0');
        const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const año = fecha.getFullYear();
        
        return `${dia}/${mes}/${año}`;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Error en fecha';
    }
}