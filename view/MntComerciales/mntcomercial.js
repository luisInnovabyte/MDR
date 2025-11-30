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

    /* DATEPICKER DE FILTRO DATATABLES */
    // Aplicar la máscara para fecha dd/mm/yyyy
    //9 : numeric
    //a: alphabetical
    // * : alphanumeric

    $('#dateCreateFilter').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#dateCreateFilter').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });


    var formValidator = new FormValidator('formComercial', {
        nombre: {
            // Letras, números con espacios, acentos y ñÑ con un mínimo de 3 pos.
            // pattern: '^[a-zA-ZáéíóúÁÉÍÓÚñÑ]{3,}$', Sin posibilida de números
            pattern: '^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9"/ºª ]{3,}$',
            required: true
        },
        apellidos: {
            // Letras, números con espacios, acentos y ñÑ con un mínimo de 3 pos.
            pattern: '^[a-zA-ZáéíóúÁÉÍÓÚñÑ0-9"/ºª ]{3,}$',
            required: true
        },
        movil: {
            // Letras, números con espacios, acentos y ñÑ con un mínimo de 3 pos.
            // pattern: '^[a-zA-ZáéíóúÁÉÍÓÚñÑ]{3,}$', Sin posibilida de números
            pattern: '^(\\+?\\d{1,3}[ ]?)?\\d{9,10}$',
            required: true
        },
        telefono: {
            pattern: '^(\\+?\\d{1,3}[ ]?)?\\d{9,10}$',
            required: true
        },
        id_usuario: {
            required: true
        }
    });


    // Solo permito pulsar los numeros
    // Si mas de una campo que debemos limitar el ingreso de caracteres
    // $('#prod_telefono, #otro_campo_id, #otro_campo_id2').on('keypress', function (event) {

    // EVITO QUE SE INSERTEN COSAS INNECESARIAS EN EL CAMPO CON ID #telefono

    $('#telefono').on('keypress', function (event) {
        //        // Obtener el código ASCII de la tecla presionada
        var charCode = (event.which) ? event.which : event.keyCode;
        //        // Permitir solo caracteres numéricos (códigos ASCII 48-57)
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault(); // Impide la entrada de otros caracteres
        }
    });

    // EVITO QUE SE INSERTEN COSAS INNECESARIAS EN EL CAMPO CON ID #movil

    $('#movil').on('keypress', function (event) {
        //        // Obtener el código ASCII de la tecla presionada
        var charCode = (event.which) ? event.which : event.keyCode;
        //        // Permitir solo caracteres numéricos (códigos ASCII 48-57)
        if (charCode > 31 && (charCode < 48 || charCode > 57)) {
            event.preventDefault(); // Impide la entrada de otros caracteres
        }
    });

    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////

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
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_comercialConfig = {
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
            { name: 'id_comercial', data: 'id_comercial', visible: false },
            { name: 'nombre', data: 'nombre' },
            { name: 'apellidos', data: 'apellidos' , visible: false},
            { name: 'movil', data: 'movil', visible: false},
            { name: 'telefono', data: 'telefono', visible: false },
            { name: 'email', data: 'email', visible: false },
            { name: 'activo', data: 'activo'},
            { name: 'activar', data: null, defaultContent: '', className: "text-center" },   // Columna 7: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center" },   // Columna 8: EDITAR

        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
                // Columna 0: BOTÓN MÁS 
            { 
              targets: 'control:name', width: '5%', searchable: false, orderable: false, className: "text-center" 
            },    
            // idcomercial
            { targets: 'id_comercial:name', width: '5%', searchable: false, orderable: false, className: "text-center"},
            // nombre
            {
                targets: 'nombre:name',
                width: '10%',
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function(data, type, row) {
                    if (type === "display") {
                        return row.nombre + " " + row.apellidos; // Solo para visualización
                    }
                    // Para filtrado, ordenamiento y procesamiento interno usa el ID + nombre
                    return row.nombre;
                }
            },
            // apellidos
            { targets: 'apellidos:name', width: '10%', searchable: true, orderable: true, className: "text-center" },
            // movil
            { targets: 'movil:name', width: '10%', searchable: true, orderable: false, className: "text-center" },
            // telefono
            { targets: 'telefono:name', width: '10%', searchable: true, orderable: false, className: "text-center" },
            // email
            { targets: 'email:name', width: '10%', searchable: true, orderable: false, className: "text-center" },
            //activo
            {
                targets: 'activo:name', width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function(data, type, row) {
                    if (type === "display") {  // Solo para visualización
                        return data == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return data;  // Para filtrado, ordenamiento y datos crudos
                }
            },
            {   // BOTON PARA ACTIVAR/DESACTIVAR COMERCIAL
                targets: 'activar:name', width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.activo == 1) {
                        // permito desactivar el estado
                        return `<button type="button" class="btn btn-danger btn-sm desacComercial" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_comercial="${row.id_comercial}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        // debo permitir activar de nuevo el estado
                        return `<button class="btn btn-success btn-sm activarComercial" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_comercial="${row.id_comercial}"> <!-- Cambiado de data-id a data-prod_id -->
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },// de la columDef 8
            {   // BOTON PARA EDITAR COMERCIAL
                targets: 'editar:name', width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar el producto
                    return `<button type="button" class="btn btn-info btn-sm editarComercial" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_comercial="${row.id_comercial}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            } // De la columna 9
        ], // de la columnDefs
        ajax: {
            url: '../../controller/comerciales.php?op=listar',
            type: 'GET',
                function (json) {
                   console.log("JSON recibido:", json); // 📌 Esto mostrará la respuesta en la consola
                    return json.data; // Asegúrate de que tu JSON tiene la clave "data"
               }
            /*********************************************************************************************/
            /************  FIN ESTO LO UTILIZAREMOS PARA SABER LO QUE NOS TRAE el AJAX ******************/
            /*******************************************************************************************/
        }, // del ajax
    }; // de la variable datatable_companiesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE    //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de empleados
    var $table = $('#comerciales_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_comercialConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#comerciales_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#comerciales_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#employees-table').DataTable(datatable_employeeConfig);
    var table_e = $table.DataTable($tableConfig);

    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/
    
    function format(d) {
        return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-circle fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles del Contacto</h5>
                </div>
            </div>
            <div class="card-body p-0" style="overflow: visible;">
                <table class="table table-borderless table-striped table-hover mb-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="ps-4 w-25 align-top"><i class="bi bi-person-fill me-2"></i>Nombre Completo</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.nombre || '<span class="text-muted fst-italic">No tiene un nombre</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-person-lines-fill me-2"></i>Apellidos</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.apellidos || '<span class="text-muted fst-italic">No tiene apellidos</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-phone-fill me-2"></i>Movil</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.movil || '<span class="text-muted fst-italic">No tiene movil</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-phone me-2"></i>Teléfono</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.telefono || '<span class="text-muted fst-italic">No tiene teléfono</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 align-top"><i class="bi bi-envelope-fill me-2"></i>Email</th>
                            <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">${d.email || '<span class="text-muted fst-italic">No tiene email</span>'}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div class="card-footer bg-transparent border-top-0 text-end">
                <small class="text-muted">Actualizado: ${new Date().toLocaleDateString()}</small>
            </div>
        </div>`;
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

function configurarSelect2Usuarios(selector = '#id_usuario') {
    $(selector).select2({
        width: '100%',
        dropdownParent: $('#modalMantenimiento .modal-content'),
        dropdownPosition: 'below',
        dropdownAutoWidth: true,
        placeholder: 'Seleccione un usuario',
        allowClear: true,
        language: {
            noResults: function () {
                return "No hay usuarios disponibles";
            }
        }
    });
}



function cargarUsuariosEnSelect(selectId, idUsuarioSeleccionado) {
    var url = idUsuarioSeleccionado == null || idUsuarioSeleccionado === '' 
        ? "../../controller/login.php?op=listarComercialesDisponibles" 
        : "../../controller/login.php?op=listarUsuariosConSeleccionado&id_usuario=" + idUsuarioSeleccionado;

    $.post(url, function (data) {
        const jsondata = data;
        console.log(jsondata);
        
        var select = $(selectId);

        // Limpiar las opciones existentes
        select.empty();

        // Agregar la opción por defecto
        select.append($('<option>', { value: '', text: 'Seleccione un usuario...' }));

        if (jsondata && jsondata.data) {
            $.each(jsondata.data, function (index, usuario) {
                select.append($('<option>', {
                    value: usuario.id_usuario,
                    text: usuario.nombre
                }));
            });

            if (idUsuarioSeleccionado != null && idUsuarioSeleccionado !== '') {
                select.val(idUsuarioSeleccionado).trigger('change'); // para Select2 si se usa
            }
        }
    }, "json").fail(function (xhr, status, error) {
        console.error("Error al cargar los usuarios:", error);
    });
}



 
    /////////////////////////////////////
    //   INICIO ZONA DELETE COMERCIAL  //
    ///////////////////////////////////
    function desacComercial(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar el comercial con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/comerciales.php?op=eliminar", { id_comercial: id }, function (data) { // Cambiado a prod_id

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Desactivado',
                        'El comercial ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacComercial', function (event) {
        event.preventDefault();
        let id = $(this).data('id_comercial'); // Cambiado de data('id') a data('prod_id')
        desacComercial(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE COMERCIAL    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR COMERCIAL  //
    /////////////////////////////////////
    function activarComercial(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar el comercial con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/comerciales.php?op=activar", { id_comercial: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Activado',
                        'El comercial ha sido activado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarComercial', function (event) {
        event.preventDefault();
        let id = $(this).data('id_comercial'); // Cambiado de data('id') a data('prod_id')
        activarComercial(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR COMERCIAL    //
    //////////////////////////////////


    /// ME HE QUEDADO POR AQUIIIIIIIIIIIIIIIIIII
    // DLKJFWDOPIGHRGHJFDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDDD

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE NUEVO
    $(document).on('click', '#btnnuevo', function (event) {
        event.preventDefault();
        $('#mdltitulo').text('Nuevo registro de comercial');

        $('#modalMantenimiento').modal('show');

        // Limpiar el formulario
        $("#formComercial")[0].reset();

        cargarUsuariosEnSelect("#id_usuario");
        configurarSelect2Usuarios();

         // Habilitar el select para nuevo registro
        $('#id_usuario').prop('disabled', false);

        // RESETEAR ID SEGURO
        $('#formComercial').find('input[name="id_comercial"]').val("");

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        // Mostrar el mantenimiento(modal) con el foco en el primer campo
        $('#modalMantenimiento').on('shown.bs.modal', function () {
            $('#modalMantenimiento .modal-body #nombre').focus();
        });

        //console.log('Modal mostrado');
    });

    // CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
$(document).on('click', '#btnsalvar', async function (event) {
    event.preventDefault();

    // Recoger el valor de cada input del formulario
    // ID COMERCIAL
    var idC = $('#formComercial').find('input[name="id_comercial"]').val();
    // NOMBRE COMERCIAL
    var nombreC = $('#formComercial').find('input[name="nombre"]').val().trim();
    // APELLIDOS COMERCIAL
    var apellidosC = $('#formComercial').find('input[name="apellidos"]').val().trim();
    // MOVIL COMERCIAL
    var movilC = $('#formComercial').find('input[name="movil"]').val().trim();
    // TELEFONO COMERCIAL
    var telefonoC = $('#formComercial').find('input[name="telefono"]').val().trim();
    // ID USUARIO (SELECT)
    var idUsuario = $('#formComercial').find('select[name="id_usuario"]').val();

    //        var paisesId = $('#formProducto').find('input[name="paisesId"]').val();

    //        console.log('Valor de prod_nom:', prodNom);
    //        console.log('Valor de prod_id:', prodId);
    //console.log('EstadoProducto:', Estado);
    //console.log('Oferta (checkbox checked):', $("#oferta").is(":checked"));--> error por el selector
    //console.log('Oferta (valor final):', Oferta);

    // No hacer falta el sistema de formValidator ya lo hace.
    // if (prodNom.length === 0) {
    //     toastr.error(`El campo nombre está vacío.`, 'Error de Validación');
    //     return; // Salir de la función si hay un campo vacío
    // }

    // Validar el formulario usando FormValidator
    const isValid = formValidator.validateForm(event);

    // Si la validación falla, no enviar el formulario
    if (!isValid) {
        toastr.error(`Por favor, corrija los errores en el formulario.`, 'Error de Validación');
        return; // Salir de la función si la validación falla
    }
    // Serializar los datos del formulario lo utilizaremos cuando no tengamos que
    // cambiar nada de los datos que se envían al servidor
    // var formData = $('#formProducto').serialize();
    // console.log('Datos del formulario serializados:', formData);

    // primero <<nombre del campo de la BD>>:<<nombre de la variable>
    var datosFormulario = {
        id_comercial: idC,
        nombre: nombreC,
        apellidos: apellidosC,
        movil: movilC,
        telefono: telefonoC,
        id_usuario: idUsuario  // <-- añadido el id_usuario
    };

    //console.log(datosFormulario);

    var formData = new FormData();

    // Agregar los datos al objeto FormData
    for (var key in datosFormulario) {
        formData.append(key, datosFormulario[key]);
    }

    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }

    $.ajax({
        url: "../../controller/comerciales.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        processData: false, // Evitar que jQuery procese los datos
        contentType: false, // Evitar que jQuery establezca el tipo de contenido
        success: function (data) {
            $('#modalMantenimiento').modal('hide');
            $table.DataTable().ajax.reload();
            $("#formComercial")[0].reset();
            // Alternativa 1 de información
            //swal.fire(
            //    'Guardado',
            //    'El producto ha sido guardado',
            //    'success'
            //);
            // Alternativa 2 de información con toastr
            //https://codeseven.github.io/toastr/demo.html
            toastr["success"]("El comercial ha sido guardado", "Guardado");
        },
        error: function (xhr, status, error) {
            swal.fire(
                'Error',
                'No se pudo guardar el comercial',
                'error'
            );
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
    $(document).on('click', '.editarComercial', function (event) {
        event.preventDefault();

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        let id = $(this).data('id_comercial');
        //        console.log('Antes del click', id);
        $.post("../../controller/comerciales.php?op=mostrar", { id_comercial: id }, function (data) {
            //console.log('Datos recibidos del servidor:', data);

            if (data) {
                // Podría ser que los datos estén llegando como una cadena JSON
                // Intentemos parsear si es necesario
                if (typeof data === 'string') {
                    try {
                        data = JSON.parse(data);
                    } catch (e) {
                        console.error('Error al parsear JSON:', e);
                    }
                }

                //console.log('Datos parseados:', data);

                $('#mdltitulo').text('Edición registro comercial');
                $('#modalMantenimiento').modal('show');

                $('#modalMantenimiento .modal-body #id_comercial').val(data.id_comercial);
                $('#modalMantenimiento .modal-body #nombre').val(data.nombre);
                $('#modalMantenimiento .modal-body #apellidos').val(data.apellidos);
                $('#modalMantenimiento .modal-body #movil').val(data.movil);
                $('#modalMantenimiento .modal-body #telefono').val(data.telefono);

                cargarUsuariosEnSelect("#id_usuario", data.id_usuario);
                configurarSelect2Usuarios();

                 console.log("id usuario:", data.id_usuario);

                // Aquí deshabilitamos el select para edición
                $('#id_usuario').prop('disabled', true);
                
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
            table_e.column(7).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(7).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

        }
    });
    ////////////////////////////////////////////////////////////
    //        FIN ZONA FILTROS RADIOBUTTON CABECERA          //
    //////////////////////////////////////////////////////////
   
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