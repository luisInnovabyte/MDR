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
    // FormValidator removido - ahora se maneja en formularioFormaPago.js
    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


    /////////////////////////////////////
    // INICIO DE LA TABLA DE FORMAS PAGO//
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_formaspagConfig = {
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
            { name: 'id_pago', data: 'id_pago', visible: false, className: "text-center" }, // Columna 1: ID_PAGO
            { name: 'codigo_pago', data: 'codigo_pago', className: "text-center align-middle" }, // Columna 2: CODIGO_PAGO
            { name: 'nombre_pago', data: 'nombre_pago', className: "text-center align-middle" }, // Columna 3: NOMBRE_PAGO
            { name: 'nombre_metodo_pago', data: 'nombre_metodo_pago', className: "text-center align-middle" }, // Columna 4: METODO_PAGO
            { name: 'tipo_pago', data: 'tipo_pago', className: "text-center align-middle" }, // Columna 5: TIPO_PAGO
            { name: 'activo_pago', data: 'activo_pago', className: "text-center align-middle" }, // Columna 6: ESTADO
            { name: 'activar', data: null, className: "text-center align-middle" }, // Columna 7: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center align-middle" },  // Columna 8: EDITAR
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
           
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_pago 
            { targets: "id_pago:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: codigo_pago
            { targets: "codigo_pago:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: nombre_pago
            { targets: "nombre_pago:name", width: '20%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: nombre_metodo_pago
            { targets: "nombre_metodo_pago:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            // Columna 5: tipo_pago
            { targets: "tipo_pago:name", width: '15%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (row.tipo_pago === 'Pago único') {
                            return '<span class="badge bg-success"><i class="bi bi-cash-coin me-1"></i>Pago único</span>';
                        } else {
                            return '<span class="badge bg-info"><i class="bi bi-credit-card-2-front me-1"></i>Pago fraccionado</span>';
                        }
                    }
                    return row.tipo_pago;
                }
            },
            // Columna 6: activo_pago (Estado)
            {
                targets: "activo_pago:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_pago == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_pago;
                }
            },
            // Columna 7: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.activo_pago == 1) {
                        // permito desactivar la forma de pago
                        return `<button type="button" class="btn btn-danger btn-sm desacFormaPago" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_pago="${row.id_pago}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
                    } else {
                        // debo permitir activar de nuevo la forma de pago
                        return `<button class="btn btn-success btn-sm activarFormaPago" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_pago="${row.id_pago}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
                    }
                } // de la function
            },// 
            // Columna 8: BOTON PARA EDITAR FORMA DE PAGO
            {   
                targets: "editar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar la forma de pago
                    return `<button type="button" class="btn btn-info btn-sm editarFormaPago" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_pago="${row.id_pago}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            }
             // De la columna 8
        ], // de la columnDefs
        ajax: {
            url: '../../controller/formaspago.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
                return json.data || json; // Ajusta en función de lo recibido
            }
        } // del ajax
    }; // de la variable datatable_formaspagConfig
    ////////////////////////////
    // FIN DE LA TABLA DE FORMAS DE PAGO //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de formas de pago
    var $table = $('#formaspago_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_formaspagConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    var $tableBody = $('#formaspago_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#formaspago_data tfoot input, #formaspago_data tfoot select'); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#formaspago-table').DataTable(datatable_formaspagConfig);
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
                        <i class="bi bi-cash-stack fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles de la Forma de Pago</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Forma Pago
                                </th>
                                <td class="pe-4">
                                    ${d.id_pago || '<span class="text-muted fst-italic">No tiene id</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tags me-2"></i>Código
                                </th>
                                <td class="pe-4">
                                    ${d.codigo_pago || '<span class="text-muted fst-italic">No tiene código</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-credit-card me-2"></i>Nombre
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_pago || '<span class="text-muted fst-italic">No tiene nombre</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-wallet2 me-2"></i>Método de Pago
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_metodo_pago || '<span class="text-muted fst-italic">No tiene método</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-percent me-2"></i>Descuento (%)
                                </th>
                                <td class="pe-4">
                                    ${d.descuento_pago ? d.descuento_pago + '%' : '<span class="text-muted fst-italic">Sin descuento</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-coin me-2"></i>Anticipo (%)
                                </th>
                                <td class="pe-4">
                                    ${d.porcentaje_anticipo_pago ? d.porcentaje_anticipo_pago + '% en ' + d.dias_anticipo_pago + ' días' : '<span class="text-muted fst-italic">No especificado</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-cash-coin me-2"></i>Pago Final (%)
                                </th>
                                <td class="pe-4">
                                    ${d.porcentaje_final_pago ? d.porcentaje_final_pago + '% en ' + (d.dias_final_pago >= 0 ? d.dias_final_pago + ' días' : Math.abs(d.dias_final_pago) + ' días antes del evento') : '<span class="text-muted fst-italic">No aplica</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.observaciones_pago || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Creado el:
                                </th>
                                <td class="pe-4">
                                    ${d.created_at_pago ? formatoFechaEuropeo(d.created_at_pago) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-event me-2"></i>Actualizado el:
                                </th>
                                <td class="pe-4">
                                  ${d.updated_at_pago ? formatoFechaEuropeo(d.updated_at_pago) : '<span class="text-muted fst-italic">Sin fecha</span>'}
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
    //   INICIO ZONA DELETE FORMA PAGO  //
    ///////////////////////////////////
    function desacFormaPago(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar la forma de pago con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Esto desactivará esta forma de pago en el sistema</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/formaspago.php?op=eliminar", { id_pago: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Desactivado',
                        'La forma de pago ha sido desactivada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacFormaPago', function (event) {
        event.preventDefault();
        let id = $(this).data('id_pago');
        desacFormaPago(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE FORMA PAGO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR FORMA PAGO  //
    /////////////////////////////////////
    function activarFormaPago(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar la forma de pago con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/formaspago.php?op=activar", { id_pago: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Activado',
                        'La forma de pago ha sido activada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarFormaPago', function (event) {
        event.preventDefault();
        let id = $(this).data('id_pago');
        console.log("id forma pago:",id);
        
        activarFormaPago(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR FORMA PAGO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA NUEVO           //
    //        BOTON DE NUEVO           // 
    /////////////////////////////////////
    // BOTÓN NUEVO AHORA ES UN ENLACE DIRECTO AL FORMULARIO INDEPENDIENTE
    // La funcionalidad del modal ha sido removida

    ///////////////////////////////////////
    //      FIN ZONA NUEVO               //
    // (Ahora manejado por formulario    //
    //       independiente)              //
    /////////////////////////////////////


    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarFormaPago', function (event) {
        event.preventDefault();
        
        let id = $(this).data('id_pago');
        console.log("id forma pago:", id);
        
        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioFormaPago.php?modo=editar&id=${id}`;
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