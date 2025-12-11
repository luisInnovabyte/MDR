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

    // FILTRO FECHA INICIO DECLARACIÓN

    $('#filtroFechaInicio').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaInicio').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

    // FILTRO FECHA FIN DECLARACIÓN

    $('#filtroFechaFin').inputmask('99-99-9999');
    // NO FUNCIONA - Muestra la máscara pero no permite escribir.
    //$('#prod_telefono').inputmask('(+99) 999-999-999');


    // Configura el datepicker en español
    $.datepicker.setDefaults($.datepicker.regional['es']);

    $('#filtroFechaFin').datepicker({
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

       // MODAL FECHA INICIO DECLARACIÓN

       $('#fecha_inicio').datepicker({
        dropdownParent: $("modalMantenimiento"),
        showAnim: "slideDown",
        dateFormat: 'dd-mm-yy',
        showOtherMonths: true,
        selectOtherMonths: true,
        numberOfMonths: 1
    });

       // Configura el datepicker en español
       $.datepicker.setDefaults($.datepicker.regional['es']);
   
       // MODAL FECHA FIN DECLARACIÓN
   
       $('#fecha_fin').inputmask('99-99-9999');
       // NO FUNCIONA - Muestra la máscara pero no permite escribir.
       //$('#prod_telefono').inputmask('(+99) 999-999-999');
   
   
       // Configura el datepicker en español
       $.datepicker.setDefaults($.datepicker.regional['es']);
   
       $('#fecha_fin').datepicker({
           showAnim: "slideDown",
           dateFormat: 'dd-mm-yy',
           showOtherMonths: true,
           selectOtherMonths: true,
           numberOfMonths: 1
       });


    var formValidator = new FormValidator('formEstado', {
        desc_estado: {
            required: true
        },
        peso_estado: {
            required: true
        }
    });

        $('#peso_estado').on('keypress', function (event) {
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
    // INICIO DE LA TABLA DE ESTADOS //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_estadosConfig = {
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
        order: [[3, 'asc']], // Orden inicial por peso en orden descendente
        columns: [

            // Son los botones para más
            // No tocar
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }, // Columna 0: Mostrar más
            { name: 'id_estado', data: 'id_estado', visible: false, className: "text-center" }, // Columna 1: ID_ESTADO
            { name: 'desc_estado', data: 'desc_estado' , className: "text-center align-middle" }, // Columna 2: DESC_ESTADO
            { name: 'peso_estado', data: 'peso_estado', className: "text-center align-middle" }, // Columna 3: PESO_ESTADO
            { name: 'activo_estado', data: 'activo_estado', className: "text-center align-middle"  }, // Columna 4: ACTIVO_ESTADO
            { name: 'activar', data: null, defaultContent: '', className: "text-center align-middle"  },  // Columna 5: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center align-middle"  },  // Columna // 6: EDITAR
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
           
            // Columna 0: BOTÓN MÁS 
            { targets: 'control:name', width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_estado 
            { targets: 'id_estado:name', width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: DESC_ESTADO
            { targets: 'desc_estado:name', width: '20%', searchable: true, orderable: true, className: "text-center" },
             // peso_estado
            { targets: 'peso_estado:name', width: '15%', searchable: true, orderable: true, className: "text-center" },
            // activo_estado
            {
                targets: 'activo_estado:name', width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_estado == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_estado;
                }
            },
            // BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {
                targets: 'activar:name',
                width: '15%',
                searchable: false,
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    console.log(row);
            
                    // Estados que deben inhabilitar el botón
                    const estadosInhabilitados = [1, 2, 3, 4];
                    const disabledAttr = estadosInhabilitados.includes(Number(row.id_estado)) ? 'disabled' : '';
            
                    if (row.activo_estado == 1) {
                        // Botón para desactivar
                        return `<button type="button" class="btn btn-danger btn-sm desacEstado"
                            data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar"
                            data-original-title="Tooltip on top" data-id_estado="${row.id_estado}" ${disabledAttr}>
                            <i class="fa-solid fa-trash"></i>
                        </button>`;
                    } else {
                        // Botón para activar
                        return `<button class="btn btn-success btn-sm activarEstado"
                            data-bs-toggle="tooltip-primary" data-placement="top" title="Activar"
                            data-original-title="Tooltip on top" data-id_estado="${row.id_estado}" ${disabledAttr}>
                            <i class="bi bi-hand-thumbs-up-fill"></i>
                        </button>`;
                    }
                }
            },
            // BOTON PARA EDITAR ESTADO
            {
                targets: 'editar:name',
                width: '15%',
                searchable: false,
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    const estadosInhabilitados = [1, 2, 3, 4];
                    const disabledAttr = estadosInhabilitados.includes(Number(row.id_estado)) ? 'disabled' : '';
            
                    return `<button type="button" class="btn btn-info btn-sm editarEstado"
                            data-toggle="tooltip-primary" data-placement="top" title="Editar"
                            data-id_estado="${row.id_estado}" ${disabledAttr}>
                            <i class="fa-solid fa-edit"></i>
                        </button>`;
                }
            }
            
             // De la columna 9
        ], // de la columnDefs
        ajax: {
            url: '../../controller/estados.php?op=listar',
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
    var $table = $('#estados_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_estadosConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#estados_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#estados_data tfoot input'); /*<--- Es el nombre que le hemos dado a los inputs de los pies de la tabla en HTML */
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
                        <i class="bi bi-flag me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Estado</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top"><i class="bi bi-flag-fill me-2"></i>Id Estado</th>
                                <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">
                                    ${d.id_estado || '<span class="text-muted fst-italic">No tiene id estado</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 align-top"><i class="bi bi-arrow-up-circle-fill me-2"></i>Peso Estado</th>
                                <td class="pe-4" style="white-space: pre-wrap; word-wrap: break-word;">
                                    ${d.peso_estado || '<span class="text-muted fst-italic">No tiene peso estado</span>'}
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

    /////////////////////////////////////
    //   INICIO ZONA DELETE ESTADOS  //
    ///////////////////////////////////
    function desacEstado(id) {
        swal.fire({
            title: 'Desactivar',
            text: `¿Desea desactivar el estado con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/estados.php?op=eliminar", { id_estado: id }, function (data) { // Cambiado a prod_id

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Desactivado',
                        'El estado ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacEstado', function (event) {
        event.preventDefault();
        let id = $(this).data('id_estado'); // Cambiado de data('id') a data('prod_id')
        desacEstado(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE ESTADO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR ESTADO  //
    /////////////////////////////////////
    function activarEstado(id) {
        swal.fire({
            title: 'Activar',
            text: `¿Desea activar el estado con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/estados.php?op=activar", { id_estado: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    swal.fire(
                        'Activado',
                        'El estado ha sido activado',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarEstado', function (event) { // Sin acento
        event.preventDefault();
        let id = $(this).data('id_estado');
        activarEstado(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR ESTADO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE NUEVO
    $(document).on('click', '#btnnuevo', function (event) {
        event.preventDefault();
        $('#mdltitulo').text('Nuevo registro de estado');

        $('#modalEstado').modal('show');

        // Limpiar el formulario
        $("#formEstado")[0].reset();

        // RESETEAR ID SEGURO
        $('#formEstado').find('input[name="id_estado"]').val("");

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        // Mostrar el mantenimiento(modal) con el foco en el primer campo
        $('#modalEstado').on('shown.bs.modal', function () {
            $('#modalEstado .modal-body #desc_estado').focus();
        });

        //console.log('Modal mostrado');
    });
    
// CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR
$(document).on('click', '#btnsalvar', async function (event) {
    event.preventDefault();
    
    // 1. Obtener valores del formulario
    var id_estado = $('#formEstado').find('input[name="id_estado"]').val().trim();
    var desc_estado = $('#formEstado').find('input[name="desc_estado"]').val().trim();
    var peso_estado = $('#formEstado').find('input[name="peso_estado"]').val().trim();
    var isPredeterminado = $('#formEstado').find('input[name="predeterminado"]').is(':checked');
    
    // 2. Validar el formulario
    const isValid = formValidator.validateForm(event);
    if (!isValid) {
        toastr.error(`Por favor, corrija los errores en el formulario.`, 'Error de Validación');
        return;
    }
    
    // 3. Preparar datos para enviar (CAMBIADO: usamos defecto_estado en lugar de predeterminado)
    var datosFormulario = {
        desc_estado: desc_estado,
        peso_estado: peso_estado,
        defecto_estado: isPredeterminado ? 1 : 0  // Cambio clave aquí
    };
    
    // Solo agregar `id_estado` si tiene un valor
    if (id_estado.trim() != "") {
        datosFormulario.id_estado = id_estado;
    }
    
    // 4. Manejo del estado predeterminado (actualizado para usar defecto_estado)
    if (isPredeterminado) {
        $.ajax({
            url: "../../controller/estados.php?op=comprobarPredeterminado",
            type: "GET",
            success: function(response) {
                if (response.hasPredeterminado) {
                    // Guardar el id del estado predeterminado
                    const estadoPredeterminadoId = response.id_estado; // NUEVO CÓDIGO
                    
                    // Preguntar al usuario si desea reasignar
                    swal.fire({
                        title: 'Reasignar Estado Predeterminado',
                        text: "Ya existe un estado predeterminado. ¿Desea reasignarlo?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sí, reevaluar',
                        cancelButtonText: 'No'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Si confirma, quitar el estado predeterminado actual
                            $.ajax({
                                url: "../../controller/estados.php?op=quitarPredeterminado",
                                type: "POST",
                                data: { id_estado_predeterminado: estadoPredeterminadoId },
                                success: function() {
                                    // Guardar el nuevo estado después de quitar el anterior
                                    guardarNuevoEstado(datosFormulario);
                                },
                                error: function() {
                                    swal.fire('Error', 'No se pudo actualizar el estado anterior', 'error');
                                    // Aún así, intenta guardar el nuevo estado, ya que no se pudo remover
                                    guardarNuevoEstado(datosFormulario);
                                }
                            });
                        } else {
                            // No se eliminará el estado predeterminado
                            datosFormulario.defecto_estado = 1;  // Asegúrate de mantenerse como predeterminado
                            guardarNuevoEstado(datosFormulario);
                        }
                    });
                } else {
                    // No hay un estado predeterminado, se puede guardar directamente
                    guardarNuevoEstado(datosFormulario);
                }
            },
            error: function() {
                toastr.error('Error al verificar estados predeterminados', 'Error');
                // Asegúrate de no marcar como predeterminado si hubo un error
                datosFormulario.defecto_estado = 0; 
                guardarNuevoEstado(datosFormulario);
            }
        });
    } else {
        // Si no es predeterminado, guardar directamente
        guardarNuevoEstado(datosFormulario);
    }
});

// Función para guardar el nuevo estado (MODIFICADA para usar FormData correctamente)
function guardarNuevoEstado(datosFormulario) {
    var formData = new FormData();
    
    // Mapeamos los nombres de campos para el backend
    formData.append('desc_estado', datosFormulario.desc_estado);
    formData.append('peso_estado', datosFormulario.peso_estado);
    formData.append('defecto_estado', datosFormulario.defecto_estado);
    
    if (datosFormulario.id_estado) {
        formData.append('id_estado', datosFormulario.id_estado);
    }
    
    // Debug
    console.log('Datos a enviar:');
    for (var pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    
    $.ajax({
        url: "../../controller/estados.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        success: function (data) {
            $('#modalEstado').modal('hide');
            $table.DataTable().ajax.reload();
            $("#formEstado")[0].reset();
            toastr.success("El estado ha sido guardado", "Guardado");
        },
        error: function (xhr, status, error) {
            console.error("Error en la petición:", status, error);
            swal.fire(
                'Error',
                'No se pudo guardar el estado: ' + (xhr.responseText || error),
                'error'
            );
        }
    });
}
    
    
    ///////////////////////////////////////
    //      FIN ZONA NUEVO           //
    /////////////////////////////////////


    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarEstado', function (event) {
        event.preventDefault();
        formValidator.clearValidation();
        
        let id = $(this).data('id_estado');
        
        $.ajax({
            url: "../../controller/estados.php?op=mostrar",
            type: "POST",
            data: { id_estado: id },
            dataType: "json", // Forzamos a que jQuery interprete la respuesta como JSON
            success: function(data) {
                try {
                    // Verificamos si la respuesta es válida
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }
    
                    // Configuramos el modal
                    $('#mdltitulo').text('Edición registro estado');
                    
                    // Llenamos los campos del formulario
                    $('#formEstado input[name="id_estado"]').val(data.id_estado);
                    $('#formEstado input[name="desc_estado"]').val(data.desc_estado);
                    $('#formEstado input[name="peso_estado"]').val(data.peso_estado);
                    
                    // Configuramos el checkbox de defecto_estado
                    const isDefecto = parseInt(data.defecto_estado) === 1;
                    $('#formEstado input[name="predeterminado"]').prop('checked', isDefecto);
                    
                    // Mostramos el modal
                    $('#modalEstado').modal('show');
                    
                } catch (e) {
                    console.error('Error al procesar datos:', e);
                    toastr.error('Error al cargar datos para edición');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al obtener datos del estado');
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
            table_e.column(3).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(3).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

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

    $('#filtroFechaInicio').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado
        console.log(value);
        table_e.column(2).search(value).draw();
    });


    // borrar la fecha
    $('#borrarFechaInicioFiltro').on('click', function () {
        $('#filtroFechaInicio').val('');
        $('#filtroFechaInicio').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaInicioFiltro').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

     ////////////////////////////////////////////////
    //        FECHA DE FIN FILTRO           //
    ///////////////////////////////////////////////

    $('#filtroFechaFin').on('change', function () {
        var value = $(this).val(); // Obtener el valor seleccionado
        console.log(value);
        table_e.column(3).search(value).draw();
    });


    // borrar la fecha
    $('#borrarFechaFinFiltro').on('click', function () {
        $('#filtroFechaFin').val('');
        $('#filtroFechaFin').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaFinFiltro').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

     // borrar la fecha de inicio del modal
     $('#borrarFechaInicioCarrera').on('click', function () {
        $('#fecha_inicio').val('');
        $('#fecha_inicio').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaInicioCarrera').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    // borrar la fecha de fin del modal
    $('#borrarFechaFinCarrera').on('click', function () {
        $('#fecha_fin').val('');
        $('#fecha_fin').trigger('change');
    });

    // cambiar el cursor
    $('#borrarFechaFinCarrera').on('mouseenter', function () {
        $(this).css('cursor', 'pointer');
    }).on('mouseleave', function () {
        $(this).css('cursor', 'default');
    });

    

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