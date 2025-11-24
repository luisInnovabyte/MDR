$(document).ready(function () {

    /////////////////////////////////////
    //            TIPS                //
    ///////////////////////////////////
    // Ocultar dinámicamente la columna con índice 2 (tercera columna)
    // ----> $('#miTabla').DataTable().column(2).visible(false);



    // Agregue un controlador de eventos para el evento "keypress" en el campo de entrada
    //    capaAula.on('keypress', function (event) {
    //        // Obtener el código ASCII de la tecla presionada
    //        var charCode = (event.which) ? event.which : event.keyCode;
    //
    //        // Permitir solo caracteres numéricos (códigos ASCII 48-57)
    //        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
    //            event.preventDefault(); // Impide la entrada de otros caracteres
    //        }
    //    });


    /////////////////////////////////////
    //          FIN DE TIPS           //
    ///////////////////////////////////

    var formValidator = new FormValidator('formUsuario', {
        nombre: {
            required: true
        },
        email: {
            pattern: '^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$',
            required: true
        },
        id_rol: {
            required: true
        }
    });

      // 1. Al abrir: Cambia a azul claro (bg-info)
      $('#collapseOne').on('show.bs.collapse', function() {
        $('#accordion-toggle')
            .removeClass('bg-primary')  // Quita el azul original
            .addClass('bg-info')       // Añade azul claro
            .css('color', 'white');    // Asegura texto blanco
    });

    // 2. Al cerrar: Restaura el azul original (bg-primary)
    $('#collapseOne').on('hide.bs.collapse', function() {
        $('#accordion-toggle')
            .removeClass('bg-info')    // Quita azul claro
            .addClass('bg-primary')    // Restaura azul original
            .css('color', '#e6f0fa');  // Color texto original
    });

    // 3. Efecto hover (opcional)
    $('#accordion-toggle').hover(
        function() { // Mouse entra
            $(this).css('opacity', '0.9');
        },
        function() { // Mouse sale
            $(this).css('opacity', '1');
        }
    );

    /////////////////////////////////////
    // INICIO DE LA TABLA DE PRODUCTOS //
    ///////////////////////////////////
    var datatable_usuarioConfig = {
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
            { name: 'control', data: null, defaultContent: '', className: 'details-control text-center' },
            { name: 'id_usuario', data: 'id_usuario', className: "text-center", visible: false },
            { name: 'nombre', data: 'nombre', className: "text-center" },
            { name: 'email', data: 'email', className: "text-center" },
            { name: 'fecha_crea', data: 'fecha_crea', className: "text-center", visible: false },
            { name: 'id_rol', data: 'id_rol', className: "text-center" },
            { name: 'est', data: 'est', className: "text-center" },
            { name: 'activar', data: null, className: "text-center"},
            { name: 'editar', data: null, className: "text-center"},
            { name: 'nombre_rol', data: 'nombre_rol', visible: false },
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
            // Esta no tocar es el + para mostrar más
            { targets: "control:name", width: '5%', searchable: false, orderable: false },
            // ID USUARIO
            { targets: "id_usuario:name", searchable: false, orderable: false, visible: false },
            // NOMBRE
            { targets: "nombre:name", width: '35%', searchable: true, orderable: true },
            // EMAIL
            { targets: "email:name", width: '30%', searchable: true, orderable: true },
            
            // FECHA_CREA
            {
                targets: "fecha_crea:name", orderable: true, className: "text-center", visible: false,
                render: function (data, type, row) {
                    if (type === "display" || type === "filter") {
                        return formatoFechaEuropeo(data);
                    } // de la function
                    return data; // para la ordenación y el procesamiento utiliza la original  
                } // de la function
            },
             // - id_rol MOSTRAR NOMBRE ROL
             {
                targets: "id_rol:name",
                width: '15%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display" || type === "filter") {
                        return row.nombre_rol;
                    }
                    return data; 
                }
            },
            // EST
            {
                targets: "est:name", width: '5%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return data;
                }
            },
            {// ACT/DESACT USUARIO
                // para añadir un botón borrar
                targets: "activar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.est == 1) {
                        // permito desactivar el estado
                        return `<button type="button" class="btn btn-danger btn-sm desacUsuario" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_usuario="${row.id_usuario}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else if(row.est == 0){
                        // debo permitir activar de nuevo el estado
                        return `<button class="btn btn-success btn-sm activarUsuario" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_usuario="${row.id_usuario}"> 
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },// de la columDef 8
            {   // para añadir un botón editar EDITAR USUARIOS
                targets: "editar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos

                    // botón editar el producto
                    return `<button type="button" class="btn btn-info btn-sm editarUsuario" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_usuario="${row.id_usuario}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`

                } // de la function
            }, // De la columna 9
            { targets: "nombre_rol:name" , searchable: false, orderable: false, visible: false },
        ], // de la columnDefs
        ajax: {
            url: '../../controller/login.php?op=listar',
            type: 'GET',
            dataSrc: 'data' // Antes era "aaData"
        }, // del ajax
    }; // de la variable datatable_companiesConfig
    /////////////////////////////////
    // FIN DE LA TABLA DE FORUM   //
    ///////////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de paises
    var $table = $('#usuarios_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_usuarioConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
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

    ////////////////////////////////////////////
    //   INICIO ZONA FUNCIONES DE APOYO      //
    //////////////////////////////////////////
    //Funcion para dar mostrar mas -- es en boton de +
    function format(d) {
        console.log(d);
    
        return `
            <div class="card border-info mb-3" style="overflow: visible;">
                <div class="card-header bg-info text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-person-lines-fill fs-4 me-2"></i>
                        <h5 class="mb-0">Detalles del Contacto</h5>
                    </div>
                </div>
                <div class="card-body p-0">
                    <table class="table table-bordered table-striped mb-0">
                        <tbody>
                            <tr>
                                <th class="w-25">Id Contacto</th>
                                <td>${d.id_usuario || '<span class="text-muted fst-italic">No tiene id usuario</span>'}</td>
                            </tr>
                           <tr>
                            <th>Fecha de Creación</th>
                            <td>
                                ${d.fecha_crea 
                                    ? (() => {
                                        const f = new Date(d.fecha_crea);
                                        const pad = n => n.toString().padStart(2, '0');
                                        return `${pad(f.getDate())}-${pad(f.getMonth()+1)}-${f.getFullYear()} ${pad(f.getHours())}-${pad(f.getMinutes())}-${pad(f.getSeconds())}`;
                                    })()
                                    : '<span class="text-muted fst-italic">No tiene fecha de creación</span>'
                                }
                            </td>
                            </tr>

                        </tbody>
                    </table>
                </div>
                <div class="card-footer text-end bg-transparent border-top-0">
                    <small class="text-muted">Actualizado: ${new Date().toLocaleDateString()}</small>
                </div>
            </div>
        `;
    }
    

    // Vamos a definir cómo funcionará el botón de mostrar más
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

    function configurarSelect2() {
        $('#id_rol').select2({
            width: '100%',
            dropdownParent: $('#modalMantenimientoUsuario .modal-content'),
            dropdownPosition: 'below',
            dropdownAutoWidth: true,
            placeholder: 'Seleccione un rol',
            allowClear: true
        });
    }

    function cargarRolesEnSelect(selectId, idRolSeleccionado) {
        $.post("../../controller/roles.php?op=listar", function (data) {
            const jsondata = data;
            console.log(jsondata);
            
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
                let selected = (idRolSeleccionado !== undefined && idRolSeleccionado !== null && idRolSeleccionado !== '' && rol.id_rol == idRolSeleccionado) ? 'selected' : '';
                var optionHtml = '<option value="' + rol.id_rol + '" ' + selected + '>' + rol.nombre_rol + '</option>';

                select.append(optionHtml);
            });
        }, "json").fail(function (xhr, status, error) {
            console.error("Error al cargar los roles:", error);
        });
    }

    /////////////////////////////////////
    //   INICIO ZONA DELETE PRODUCTO  //
    ///////////////////////////////////
    function desacUsuario(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar el usuario con ID ${id}?`,
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
    //   FIN ZONA DELETE PRODUCTO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR PRODUCTO  //
    /////////////////////////////////////
    function activarUsuario(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar el usuario con ID ${id}?`,
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
    $(document).on('click', '.activarUsuario', function (event) {
        event.preventDefault();
        let id = $(this).data('id_usuario'); // Cambiado de data('id') a data('prod_id')
        activarUsuario(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR PRODUCTO    //
    //////////////////////////////////



    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE NUEVO
    $(document).on('click', '#btnnuevoUsuario', function (event) {
        event.preventDefault();
        $('#tituloUsuario').text('Nuevo registro de usuario');

        // $('#modalMantenimiento .modal-body #prod_id').focus();

        // Limpiar cualquier clase de validación de contraseñas (is-invalid o is-valid)
        $('#formUsuario').find("input[name='contrasena']").removeClass('is-invalid is-valid');
        $('#formUsuario').find("input[name='confirmar_contrasena']").removeClass('is-invalid is-valid');
        
        formValidator.clearValidation();

        $("#formUsuario")[0].reset();  // Limpiamos el formulario
        $('#modalMantenimientoUsuario').modal('show');

        configurarSelect2();
        cargarRolesEnSelect("#id_rol");

        $('#modalMantenimientoUsuario').on('shown.bs.modal', function () {
            $('#modalMantenimientoUsuario .modal-body #nombre').focus();
        });

        console.log('Modal mostrado');
    });

    $(document).on('click', '#btnSalvarUsuario', function (evento) {
        evento.preventDefault();
    
        // Recogemos los valores de los inputs
        var idUsuario = $('#formUsuario').find('input[name="id_usuario"]').val().trim();
        var nombre = $('#formUsuario').find('input[name="nombre"]').val().trim();
        var correo = $('#formUsuario').find('input[name="email"]').val();
        var contrasena1 = $('#formUsuario').find("input[name='contrasena']").val();  
        var contrasena2 = $('#formUsuario').find("input[name='confirmar_contrasena']").val();
        var id_rolS = $('#formUsuario').find("select[name='id_rol']").val();
    
        // Validación de los campos generales con FormValidator
        var formularioValido = formValidator.validateForm(evento);
    
        // Variables para los errores
        var contraseñasValidas = true;
        var hayErrorGeneral = false;
        var hayErrorContraseña = false;
    
        // Validamos las contraseñas si estamos creando un nuevo usuario
        if (idUsuario === "") {
            var resultadoContraseña = validarContraseñas(contrasena1, contrasena2);
            contraseñasValidas = resultadoContraseña.contraseñasValidas;
            hayErrorContraseña = resultadoContraseña.hayErrorContraseña;
        } else {
            // Si estamos editando, las contraseñas son opcionales
            if (contrasena1 !== "" || contrasena2 !== "") {
                var resultadoContraseña = validarContraseñas(contrasena1, contrasena2);
                contraseñasValidas = resultadoContraseña.contraseñasValidas;
                hayErrorContraseña = resultadoContraseña.hayErrorContraseña;
            }
        }
    
        // Si el formulario tiene errores generales
        if (!formularioValido) {
            hayErrorGeneral = true;
        }
    
        // Si solo hay errores de contraseñas, mostramos el error específico para contraseñas
        if (hayErrorContraseña && !hayErrorGeneral) {
            toastr["error"]("Las contraseñas no cumplen con los requisitos. Asegúrate de que coincidan y sigan el formato correcto.", "Error en contraseñas");
        }
    
        // Si hay errores generales, mostramos un mensaje de error general
        if (hayErrorGeneral) {
            toastr["error"]("Por favor, corrija los errores en el formulario.", "Error");
        }
    
        // Si hay errores, no enviamos el formulario
        if (!formularioValido || !contraseñasValidas) {
            return;  // Detenemos la ejecución si hay errores
        }
    
        // Si todo está bien, recogemos los datos del formulario
        var datosFormulario = {
            id_usuario: idUsuario,  // Incluimos el id de usuario para editar
            nombre: nombre,
            email: correo,
            id_rol: id_rolS
        };
    
        // Solo agregamos la contraseña al FormData si es nueva (cuando es válida y si no estamos editando)
        if (contrasena1 !== "" && contrasena1 === contrasena2) {
            datosFormulario.contrasena = contrasena1;
        }
    
        // Creamos un FormData para enviar los datos
        var formData = new FormData();
        for (var clave in datosFormulario) {
            formData.append(clave, datosFormulario[clave]);
        }
    
        // Enviamos los datos al backend
        $.ajax({
            url: "../../controller/login.php?op=guardaryeditar",
            type: "POST",
            data: formData,
            processData: false,  // No procesamos los datos
            contentType: false,  // No definimos el tipo de contenido
            success: function (data) {
                // Si la operación es exitosa
                $('#modalMantenimientoUsuario').modal('hide');
                $table.DataTable().ajax.reload();
                $("#formUsuario")[0].reset();  // Limpiamos el formulario
                toastr["success"]("El usuario ha sido guardado correctamente.", "Éxito");
            },
            error: function (xhr, status, error) {
                // Aquí manejamos el caso cuando el código de estado es 409
                if (xhr.status === 409) {
                    // Mostramos el mensaje de error usando Swal
                    Swal.fire({
                        title: 'Error',
                        text: 'El correo ya está registrado. Por favor, utiliza otro.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                } else {
                    // Otros errores
                    Swal.fire({
                        title: 'Error',
                        text: 'Hubo un problema al guardar el usuario. Intenta de nuevo.',
                        icon: 'error',
                        confirmButtonText: 'Aceptar'
                    });
                }
            }
        });
    });
    
    $(document).on('change', '#id_rol', function () {
        var valorSeleccionado = $(this).val();
    
        if (!valorSeleccionado) {
            // Si no hay valor, mostramos un error (puedes personalizar esto)
            $(this).addClass('is-invalid'); // clase Bootstrap para resaltar error
        } else {
            // Si hay valor válido, quitamos el error
            $(this).removeClass('is-invalid');
        }
    });
    

// Función para validar las contraseñas
function validarContraseñas(contrasena1, contrasena2) {
    let contraseñasValidas = true;
    let hayErrorContraseña = false;

    const input1 = $("#formUsuario input[name='contrasena']");
    const input2 = $("#formUsuario input[name='confirmar_contrasena']");
    const feedback1 = input1.closest('.form-group').find('.invalid-feedback');
    const feedback2 = input2.closest('.form-group').find('.invalid-feedback');

    input1.removeClass('is-valid is-invalid');
    input2.removeClass('is-valid is-invalid');
    feedback1.hide();
    feedback2.hide();

    // Vacíos
    if (contrasena1 === "" || contrasena2 === "") {
        hayErrorContraseña = true;
        contraseñasValidas = false;

        if (contrasena1 === "") {
            input1.addClass('is-invalid');
            feedback1.text("La contraseña es obligatoria").show();
        }
        if (contrasena2 === "") {
            input2.addClass('is-invalid');
            feedback2.text("Por favor confirma la contraseña").show();
        }

        return { contraseñasValidas, hayErrorContraseña };
    }

    // Validación progresiva
    if (contrasena1.length < 10) {
        input1.addClass('is-invalid');
        feedback1.text("Debe tener al menos 10 caracteres").show();
        hayErrorContraseña = true;
        contraseñasValidas = false;
    } else if (!/[A-Z]/.test(contrasena1)) {
        input1.addClass('is-invalid');
        feedback1.text("Debe contener al menos una letra mayúscula").show();
        hayErrorContraseña = true;
        contraseñasValidas = false;
    } else if (!/[a-z]/.test(contrasena1)) {
        input1.addClass('is-invalid');
        feedback1.text("Debe contener al menos una letra minúscula").show();
        hayErrorContraseña = true;
        contraseñasValidas = false;
    } else if (!/\d/.test(contrasena1)) {
        input1.addClass('is-invalid');
        feedback1.text("Debe contener al menos un número").show();
        hayErrorContraseña = true;
        contraseñasValidas = false;
    } else if (!/[@$!%*?&]/.test(contrasena1)) {
        input1.addClass('is-invalid');
        feedback1.text("Debe contener al menos un carácter especial (@$!%*?&)").show();
        hayErrorContraseña = true;
        contraseñasValidas = false;
    }

    // Si pasó todos los tests anteriores
    if (!hayErrorContraseña) {
        if (contrasena1 !== contrasena2) {
            input2.addClass('is-invalid');
            feedback2.text("Las contraseñas no coinciden").show();
            contraseñasValidas = false;
            hayErrorContraseña = true;
        } else {
            input1.addClass('is-valid');
            input2.addClass('is-valid');
        }
    }

    return { contraseñasValidas, hayErrorContraseña };
}


// Función para resaltar las contraseñas con clases CSS y mensajes de error
function resaltarCamposContraseñas(contrasena1, contrasena2) {
    const campo1 = $('#formUsuario').find("input[name='contrasena']");
    const campo2 = $('#formUsuario').find("input[name='confirmar_contrasena']");
    const feedback1 = campo1.closest('.form-group').find('.invalid-feedback');
    const feedback2 = campo2.closest('.form-group').find('.invalid-feedback');

    campo1.removeClass('is-valid is-invalid');
    campo2.removeClass('is-valid is-invalid');
    feedback1.hide();
    feedback2.hide();

    // Validaciones progresivas en orden
    if (contrasena1.length < 10) {
        campo1.addClass('is-invalid');
        feedback1.text("Debe tener al menos 10 caracteres").show();
    } else if (!/[A-Z]/.test(contrasena1)) {
        campo1.addClass('is-invalid');
        feedback1.text("Debe contener al menos una letra mayúscula").show();
    } else if (!/[a-z]/.test(contrasena1)) {
        campo1.addClass('is-invalid');
        feedback1.text("Debe contener al menos una letra minúscula").show();
    } else if (!/\d/.test(contrasena1)) {
        campo1.addClass('is-invalid');
        feedback1.text("Debe contener al menos un número").show();
    } else if (!/[@$!%*?&]/.test(contrasena1)) {
        campo1.addClass('is-invalid');
        feedback1.text("Debe contener al menos un carácter especial (@$!%*?&)").show();
    } else {
        campo1.addClass('is-valid');
    }

    // Confirmación
    if (contrasena2.length === 0) {
        campo2.addClass('is-invalid');
        feedback2.text("Este campo no puede estar vacío").show();
    } else if (contrasena1 !== contrasena2) {
        campo2.addClass('is-invalid');
        feedback2.text("Las contraseñas no coinciden").show();
    } else if (campo1.hasClass('is-valid')) {
        campo2.addClass('is-valid');
    }
}


// Validación en tiempo real para las contraseñas
$('#formUsuario').find("input[name='contrasena'], input[name='confirmar_contrasena']").on('input', function () {
    const contrasena1 = $('#formUsuario').find("input[name='contrasena']").val();
    const contrasena2 = $('#formUsuario').find("input[name='confirmar_contrasena']").val();

    // Pasamos las contraseñas directamente para analizar progresivamente
    resaltarCamposContraseñas(contrasena1, contrasena2);
});


    
    ///////////////////////////////////////
    //      FIN ZONA NUEVO           //
    /////////////////////////////////////
    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarUsuario', function (event) {
        event.preventDefault();
        let id = $(this).data('id_usuario');
        //console.log('Antes del click', id);
        
        $.post("../../controller/login.php?op=mostrar", { id_usuario: id }, function (data) {
            //console.log('Dentro del click', id);
            //console.log('data:', data);

            if (data) {
                console.log(data);
                
                // Podría ser que los datos estén llegando como una cadena JSON
                // Intentemos parsear si es necesario
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                    }
                }

                // Limpiar cualquier clase de validación de contraseñas (is-invalid o is-valid)
                $('#formUsuario').find("input[name='contrasena']").removeClass('is-invalid is-valid');
                $('#formUsuario').find("input[name='confirmar_contrasena']").removeClass('is-invalid is-valid');
                       
                formValidator.clearValidation();

                $("#formUsuario")[0].reset();  // Limpiamos el formulario

                configurarSelect2();
                cargarRolesEnSelect("#id_rol", data.id_rol);

                $('#formUsuario').find('input[name="id_usuario"]').val(data.id_usuario);
                $('#formUsuario').find('input[name="nombre"]').val(data.nombre);
                $('#formUsuario').find('input[name="email"]').val(data.email);

                $('#tituloUsuario').text('Edición de usuario');
                $('#modalMantenimientoUsuario').modal('show');

            } else {
                console.error('Error: Datos no encontrados');
            }
        }).fail(function (xhr, status, error) {
            console.error('Error en la solicitud AJAX:', status, error);
            console.error('Respuesta del servidor:', xhr.responseText);
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
            table_e.column(6).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(6).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

        }
    });



    ////////////////////////////////////////////////////////////
    //        FIN ZONA FILTROS RADIOBUTTON CABECERA          //
    //////////////////////////////////////////////////////////


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