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
    // FormValidator removido - ahora se maneja en formularioObservaciones.js
    /////////////////////////////////////////
    //     FIN FORMATEO DE CAMPOS          //
    ////////////////////////////////////////


    /////////////////////////////////////
    // INICIO DE LA TABLA DE OBSERVACIONES //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_observacionesConfig = {
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
            { name: 'id_obs_general', data: 'id_obs_general', visible: false, className: "text-center" }, // Columna 1: ID_OBS_GENERAL
            { name: 'codigo_obs_general', data: 'codigo_obs_general', className: "text-center" }, // Columna 2: CODIGO_OBS_GENERAL
            { name: 'titulo_obs_general', data: 'titulo_obs_general', className: "text-center" }, // Columna 3: TITULO_OBS_GENERAL
            { name: 'tipo_obs_general', data: 'tipo_obs_general', className: "text-center" }, // Columna 4: TIPO_OBS_GENERAL
            { name: 'orden_obs_general', data: 'orden_obs_general', className: "text-center" }, // Columna 5: ORDEN_OBS_GENERAL
            { name: 'obligatoria_obs_general', data: 'obligatoria_obs_general', className: "text-center" }, // Columna 6: OBLIGATORIA_OBS_GENERAL
            { name: 'activo_obs_general', data: 'activo_obs_general', className: "text-center" }, // Columna 7: ESTADO
            { name: 'activar', data: null, className: "text-center" }, // Columna 8: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center" },  // Columna 9: EDITAR
            
        ], // de las columnas
        columnDefs: [
            // Cuidado que el ordrData puede interferir con el ordenamiento de la tabla    
           
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_obs_general 
            { targets: "id_obs_general:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: codigo_obs_general
            { targets: "codigo_obs_general:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: titulo_obs_general
            { targets: "titulo_obs_general:name", width: '20%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: tipo_obs_general
            { targets: "tipo_obs_general:name", width: '10%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const tipos = {
                            'condiciones': '<span class="badge bg-info">Condiciones</span>',
                            'tecnicas': '<span class="badge bg-warning">Técnicas</span>',
                            'legales': '<span class="badge bg-danger">Legales</span>',
                            'comerciales': '<span class="badge bg-success">Comerciales</span>',
                            'otras': '<span class="badge bg-secondary">Otras</span>'
                        };
                        return tipos[row.tipo_obs_general] || row.tipo_obs_general;
                    }
                    return row.tipo_obs_general;
                }
            },
            // Columna 5: orden_obs_general
            { targets: "orden_obs_general:name", width: '8%', searchable: true, orderable: true, className: "text-center" },
            // Columna 6: obligatoria_obs_general
            {
                targets: "obligatoria_obs_general:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.obligatoria_obs_general == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.obligatoria_obs_general;
                }
            },
            // Columna 7: activo_obs_general (Estado)
            {
                targets: "activo_obs_general:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_obs_general == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_obs_general;
                }
            },
            // Columna 8: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    if (row.activo_obs_general == 1) {
                        // permito desactivar la observación
                        return `<button type="button" class="btn btn-danger btn-sm desacObservacion" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_obs_general="${row.id_obs_general}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
                    } else {
                        // debo permitir activar de nuevo la observación
                        return `<button class="btn btn-success btn-sm activarObservacion" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_obs_general="${row.id_obs_general}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
                    }
                } // de la function
            },// 
            // Columna 9: BOTON PARA EDITAR OBSERVACION
            {   
                targets: "editar:name", width: '10%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    // El nombre que de la variable que se pasa por data-xxx debe ser el mismo que el nombre de la columna en la base de datos
                    // botón editar la observación
                    return `<button type="button" class="btn btn-info btn-sm editarObservacion" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_obs_general="${row.id_obs_general}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                } // de la function
            }
             // De la columna 9
        ], // de la columnDefs
        ajax: {
            url: '../../controller/observaciones.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json); // 📌 Ver qué estructura tiene
                return json.data || json; // Ajusta en función de lo recibido
            }
        } // del ajax
    }; // de la variable datatable_observacionesConfig
    ////////////////////////////
    // FIN DE LA TABLA DE OBSERVACIONES //
    ///////////////////////////


    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    // Definición inicial de la tabla de observaciones
    var $table = $('#observaciones_data');  /*<--- Es el nombre que le hemos dado a la tabla en HTML */
    var $tableConfig = datatable_observacionesConfig; /*<--- Es el nombre que le hemos dado a la declaración de la definicion de la tabla */
    var $tableBody = $('#observaciones_data tbody'); /*<--- Es el nombre que le hemos dado al cuerpo de la tabla en HTML */
    /* en el tableBody solo cambiar el nombre de la tabla que encontraremos en HTML*/
    var $columnFilterInputs = $('#observaciones_data tfoot input, #observaciones_data tfoot select'); /*<--- Selecciona tanto inputs como selects de los pies de la tabla en HTML */
    /* en el $columnFilterInputs solo cambiar el nombre de la tabla que encontraremos en HTML*/

    //ejemplo -- var table_e = $('#observaciones-table').DataTable(datatable_observacionesConfig);
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
                        <i class="bi bi-chat-left-text fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles de la Observación</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Id Observación
                                </th>
                                <td class="pe-4">
                                    ${d.id_obs_general || '<span class="text-muted fst-italic">No tiene id</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-upc-scan me-2"></i>Código
                                </th>
                                <td class="pe-4">
                                    ${d.codigo_obs_general || '<span class="text-muted fst-italic">No tiene código</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-card-heading me-2"></i>Título (Español)
                                </th>
                                <td class="pe-4">
                                    ${d.titulo_obs_general || '<span class="text-muted fst-italic">No tiene título</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-translate me-2"></i>Title (English)
                                </th>
                                <td class="pe-4">
                                    ${d.title_obs_general || '<span class="text-muted fst-italic">Sin título en inglés</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-file-text me-2"></i>Texto (Español)
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.texto_obs_general || '<span class="text-muted fst-italic">No tiene texto</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-file-earmark-text me-2"></i>Text (English)
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.text_obs_general || '<span class="text-muted fst-italic">Sin texto en inglés</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-sort-numeric-down me-2"></i>Orden
                                </th>
                                <td class="pe-4">
                                    ${d.orden_obs_general !== null ? d.orden_obs_general : '<span class="text-muted fst-italic">Sin orden</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-tag me-2"></i>Tipo
                                </th>
                                <td class="pe-4">
                                    ${d.tipo_obs_general || '<span class="text-muted fst-italic">Sin tipo</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-exclamation-circle me-2"></i>Obligatoria
                                </th>
                                <td class="pe-4">
                                    ${d.obligatoria_obs_general == 1 ? 
                                        '<span class="badge bg-success">Sí</span>' : 
                                        '<span class="badge bg-secondary">No</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Observación creada el:
                                </th>
                                <td class="pe-4">
                                    ${d.created_at_obs_general ? formatoFechaEuropeo(d.created_at_obs_general) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Observación actualizada el:
                                </th>
                                <td class="pe-4">
                                  ${d.updated_at_obs_general ? formatoFechaEuropeo(d.updated_at_obs_general) : '<span class="text-muted fst-italic">Sin fecha</span>'}
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
    //   INICIO ZONA DELETE OBSERVACIONES  //
    ///////////////////////////////////
    function desacObservacion(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar la observación con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Esto desactivará esta observación en el sistema</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/observaciones.php?op=eliminar", { id_obs_general: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Desactivado',
                        'La observación ha sido desactivada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacObservacion', function (event) {
        event.preventDefault();
        let id = $(this).data('id_obs_general');
        desacObservacion(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE OBSERVACION    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR OBSERVACION  //
    /////////////////////////////////////
    function activarObservacion(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar la observación con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/observaciones.php?op=activar", { id_obs_general: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Activado',
                        'La observación ha sido activada',
                        'success'
                    )
                });
            }
        })
    }


    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarObservacion', function (event) {
        event.preventDefault();
        let id = $(this).data('id_obs_general');
        console.log("id observación:",id);
        
        activarObservacion(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR OBSERVACION    //
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
    $(document).on('click', '.editarObservacion', function (event) {
        event.preventDefault();
        
        let id = $(this).data('id_obs_general');
        console.log("id observación:", id);
        
        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioObservaciones.php?modo=editar&id=${id}`;
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