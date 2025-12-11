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

    var formValidator = new FormValidator('formFamilia', {
        codigo_familia: {
            required: true
        },
        nombre_familia: {
            required: true
        }
    });
    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


    /////////////////////////////////////
    // INICIO DE LA TABLA DE FAMILIAS //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_familiasConfig = {
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
            { name: 'id_familia', data: 'id_familia', visible: false, className: "text-center " }, // Columna 1: ID_FAMILIA
            { name: 'codigo_familia', data: 'codigo_familia', className: "text-center align-middle" }, // Columna 2: CODIGO_FAMILIA
            { name: 'nombre_familia', data: 'nombre_familia', className: "text-center" }, // Columna 3: NOMBRE_FAMILIA
            
            { name: 'activo_familia', data: 'activo_familia', className: "text-center" }, // Columna 5: ESTADO
            { name: 'activar', data: null, className: "text-center" }, // Columna 6: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center" },  // Columna 7: EDITAR
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
           
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_familia 
            { targets: "id_familia:name", width: '5%', searchable: false, orderable: false, className: "text-center, align-middle" },
            // Columna 2: codigo_familia
            { targets: "codigo_familia:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: nombre_familia
            { targets: "nombre_familia:name", width: '20%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: descr_familia
            { targets: 4, className: "text-start" },
            // Columna 5: activo_familia (Estado)
            {
                targets: "activo_familia:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_familia == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_familia;
                }
            },
            // Columna 6: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.activo_familia == 1) {
                        // permito desactivar la familia
                        return `<button type="button" class="btn btn-danger btn-sm desacFamilia" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_familia="${row.id_familia}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        // debo permitir activar de nuevo la familia
                        return `<button class="btn btn-success btn-sm activarFamilia" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_familia="${row.id_familia}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                } // de la function
            },// 
            // Columna 7: BOTON PARA EDITAR FAMILIA
            {   
                targets: "editar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar la familia
                    return `<button type="button" class="btn btn-info btn-sm editarFamilia" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_familia="${row.id_familia}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            }
             // De la columna 7
        ], // de la columnDefs
        ajax: {
            url: '../../controller/familia.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
                return json.data || json; // Ajusta en función de lo recibido
            }
        } // del ajax
    }; // de la variable datatable_familiasConfig
    ////////////////////////////
    // FIN DE LA TABLA DE FAMILIAS //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de familias
    var $table = $('#familias_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_familiasConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    //var $columSearch = 3; /* <-- Es la columna en la cual al hacer click el valor se colocará en la zona de search y se buscará */
    var $tableBody = $('#familias_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#familias_data tfoot input, #familias_data tfoot select'); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#familias-table').DataTable(datatable_familiasConfig);
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
                        <h5 class="card-title mb-0">Detalles de la Familia</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Familia
                                </th>
                                <td class="pe-4">
                                    ${d.id_familia || '<span class="text-muted fst-italic">No tiene id familia</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tags me-2"></i>Nombre Familia
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_familia || '<span class="text-muted fst-italic">No tiene nombre familia</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tags me-2"></i>Nombre Familia (en)
                                </th>
                                <td class="pe-4">
                                    ${d.name_familia || '<span class="text-muted fst-italic">No tiene traducción (en)</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-collection me-2"></i>Grupo de Artículo
                                </th>
                                <td class="pe-4">
                                    ${d.codigo_grupo ? 
                                        `<span class="badge bg-info me-2">${d.codigo_grupo}</span>
                                        <strong>${d.nombre_grupo}</strong><br>
                                        <small class="text-muted">${d.descripcion_grupo}</small>`
                                    : '<span class="text-muted fst-italic">Sin grupo asignado</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones Familia
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.descr_familia || '<span class="text-muted fst-italic">No tiene observaciones</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Familia creada el:
                                </th>
                                <td class="pe-4">
                                    ${d.created_at_familia ? formatoFechaEuropeo(d.created_at_familia) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Familia actualizada el:
                                </th>
                                <td class="pe-4">
                                  ${d.updated_at_familia ? formatoFechaEuropeo(d.updated_at_familia) : '<span class="text-muted fst-italic">Sin fecha</span>'}
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
    //   INICIO ZONA DELETE FAMILIAS  //
    ///////////////////////////////////
    function desacFamilia(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar la familia con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Esto desactivará todos los artículos que tengan esta familia</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/familia.php?op=eliminar", { id_familia: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Desactivado',
                        'La familia ha sido desactivada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacFamilia', function (event) {
        event.preventDefault();
        let id = $(this).data('id_familia');
        desacFamilia(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE FAMILIA    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR FAMILIA  //
    /////////////////////////////////////
    function activarFamilia(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar la familia con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/familia.php?op=activar", { id_familia: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Activado',
                        'La familia ha sido activada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarFamilia', function (event) {
        event.preventDefault();
        let id = $(this).data('id_familia');
        console.log("id familia:",id);
        
        activarFamilia(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR FAMILIA    //
    //////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE NUEVO
    $(document).on('click', '#btnnuevo', function (event) {
        event.preventDefault();
        console.log('Botón Nuevo clickeado');
        
        $('#mdltitulo').text('Nuevo registro de familia');
        console.log('Título del modal cambiado');

        // Verificar si el modal existe antes de mostrarlo
        if ($('#modalFamilia').length === 0) {
            console.error('Error: El modal #modalFamilia no se encontró en el DOM');
            return;
        }

        // Bootstrap 5 syntax
        var modalFamilia = new bootstrap.Modal(document.getElementById('modalFamilia'));
        modalFamilia.show();
        console.log('Modal mostrado');

        // Limpiar el formulario
        $("#formFamilia")[0].reset();

        // RESETEAR ID SEGURO
        $('#formFamilia').find('input[name="id_familia"]').val("");

        // Limpiar las validaciones
        formValidator.clearValidation(); // Llama al método clearValidation

        // Mostrar el mantenimiento(modal) con el foco en el primer campo (Bootstrap 5)
        document.getElementById('modalFamilia').addEventListener('shown.bs.modal', function () {
            document.getElementById('codigo_familia').focus();
        });
    });
    


//*****************************************************/
//   CAPTURAR EL CLICK EN EL BOTÓN DE SALVAR FAMILIA
//*****************************************************/

$(document).on('click', '#btnSalvarFamilia', function (event) {
    event.preventDefault();

    // Obtener valores del formulario de forma más directa
    var id_familiaR = $('#formFamilia').find('input[name="id_familia"]').val().trim();
    var codigo_familiaR = $('input[name="codigo_familia"]').val().trim();
    var nombre_familiaR = $('input[name="nombre_familia"]').val().trim();
    var name_familiaR = $('input[name="name_familia"]').val().trim();
    var descr_familiaR = $('textarea[name="descr_familia"]').val().trim();

    // Validar el formulario
    if (!formValidator.validateForm(event)) {
        toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
        return;
    }
    
    // Verificar familia primero
    verificarFamiliaExistente(id_familiaR, codigo_familiaR, name_familiaR, nombre_familiaR, descr_familiaR);
});

function verificarFamiliaExistente(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia) {
    $.ajax({
        url: "../../controller/familia.php?op=verificarFamilia",
        type: "GET",
        data: { 
            nombre_familia: nombre_familia,
            name_familia: name_familia, 
            codigo_familia: codigo_familia,
            id_familia: id_familia 
        },
        dataType: "json",
        success: function(response) {
            if (!response.success) {
                toastr.warning(response.message || "Error al verificar la familia.");
                return;
            }

            if (response.existe) {
                mostrarErrorFamiliaExistente(nombre_familia);
            } else {
                guardarFamilia(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en verificación:', error);
            toastr.error('Error al verificar la familia. Intente nuevamente.', 'Error');
        }
    });
}

function mostrarErrorFamiliaExistente(nombre_familia) {
    console.log("Familia duplicada detectada:", nombre_familia);
    Swal.fire({
        title: 'Nombre de familia duplicado',
        text: 'La familia "' + nombre_familia + '" ya existe. Por favor, elija otro nombre.',
        icon: 'warning',
        confirmButtonText: 'Entendido'
    });
}

function guardarFamilia(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia) {
    var datos = { 
        codigo_familia: codigo_familia,
        nombre_familia: nombre_familia,
        name_familia: name_familia,
        descr_familia: descr_familia
    };
    if (id_familia) datos.id_familia = id_familia;

    $.ajax({
        url: "../../controller/familia.php?op=guardaryeditar",
        type: "POST",
        data: datos,
        dataType: "json",
        success: function(res) {
            if (res.success) {
                // Bootstrap 5 syntax para cerrar modal
                var modalFamilia = bootstrap.Modal.getInstance(document.getElementById('modalFamilia'));
                modalFamilia.hide();
                table_e.ajax.reload(null, false);
                $("#formFamilia")[0].reset();
                toastr.success(res.message || "Familia guardada correctamente");
            } else {
                toastr.error(res.message || "Error al guardar la familia");
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en guardado:", error);
            Swal.fire('Error', 'No se pudo guardar la familia. Error: ' + error, 'error');
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
    $(document).on('click', '.editarFamilia', function (event) {
        event.preventDefault();
        formValidator.clearValidation();
        
        let id = $(this).data('id_familia');
        console.log("id familia:", id);
        
        
        $.ajax({
            url: "../../controller/familia.php?op=mostrar",
            type: "POST",
            data: { id_familia: id },
            dataType: "json", // Forzamos a que jQuery interprete la respuesta como JSON
            success: function(data) {
                try {
                    // Verificamos si la respuesta es válida
                    if (!data || typeof data !== 'object') {
                        throw new Error('Respuesta del servidor no válida');
                    }

                    console.log(data);
    
                    // Configuramos el modal
                    $('#mdltitulo').text('Edición registro familia');
                    
                    // Llenamos los campos del formulario
                    $('#formFamilia').find('input[name="id_familia"]').val(data.id_familia);
                    $('#formFamilia input[name="codigo_familia"]').val(data.codigo_familia);
                    $('#formFamilia input[name="nombre_familia"]').val(data.nombre_familia);
                    $('#formFamilia input[name="name_familia"]').val(data.name_familia);
                    $('#formFamilia textarea[name="descr_familia"]').val(data.descr_familia);
                    
                    // Configurar el switch de estado
                    $('#formFamilia input[name="activo_familia"]').prop('checked', data.activo_familia == 1);
                   
                    // Mostramos el modal (Bootstrap 5)
                    var modalFamilia = new bootstrap.Modal(document.getElementById('modalFamilia'));
                    modalFamilia.show();
                    
                } catch (e) {
                    console.error('Error al procesar datos:', e);
                    toastr.error('Error al cargar datos para edición');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error en la solicitud AJAX:', status, error);
                console.error('Respuesta del servidor:', xhr.responseText);
                toastr.error('Error al obtener datos de la familia');
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
            table_e.column(4).search("").draw(); // Cambiar numero por el índice de la columna a filtrar
        } else {
            // Filtrar la columna por el valor seleccionado
            table_e.column(4).search(value).draw(); // Cambia numero por el índice de la columna a filtrar

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
    // Manejo para elementos input (keyup) y select (change)
    $columnFilterInputs.on('keyup change', function () {
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