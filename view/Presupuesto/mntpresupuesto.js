$(document).ready(function () {

    /////////////////////////////////////
    //          FIN DE TIPS           //
    ///////////////////////////////////

    /////////////////////////////////////
    // INICIO DE LA TABLA DE PRESUPUESTOS //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_presupuestosConfig = {
        processing: true,
        responsive: {
            details: {
                type: 'column',
                target: 0,
                renderer: function (api, rowIdx, columns) {
                    // Obtener los datos de la fila
                    var data = api.row(rowIdx).data();
                    
                    // Usar nuestra función format personalizada
                    return format(data);
                }
            }
        },
        layout: {
            bottomEnd: {
                paging: {
                    firstLast: true,
                    numbers: false,
                    previousNext: true
                }
            }
        },
        language: {
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }, // Columna 0: Mostrar más
            { name: 'id_presupuesto', data: 'id_presupuesto', visible: false, className: "text-center" }, // Columna 1: ID
            { name: 'numero_presupuesto', data: 'numero_presupuesto', className: "text-center" }, // Columna 2: NÚMERO
            { name: 'nombre_cliente', data: 'nombre_cliente', className: "text-center" }, // Columna 3: CLIENTE
            { name: 'nombre_evento_presupuesto', data: 'nombre_evento_presupuesto', className: "text-center" }, // Columna 4: EVENTO
            { name: 'fecha_inicio_evento_presupuesto', data: 'fecha_inicio_evento_presupuesto', className: "text-center" }, // Columna 5: FECHA INICIO EVENTO
            { name: 'fecha_fin_evento_presupuesto', data: 'fecha_fin_evento_presupuesto', className: "text-center" }, // Columna 6: FECHA FIN EVENTO
            { name: 'dias_validez_restantes', data: 'dias_validez_restantes', className: "text-center" }, // Columna 7: DÍAS VALIDEZ RESTANTES
            { name: 'duracion_evento_dias', data: 'duracion_evento_dias', className: "text-center" }, // Columna 8: DURACIÓN EVENTO
            { name: 'dias_hasta_inicio_evento', data: 'dias_hasta_inicio_evento', className: "text-center" }, // Columna 9: DÍAS HASTA INICIO
            { name: 'estado_evento_presupuesto', data: 'estado_evento_presupuesto', className: "text-center" }, // Columna 10: ESTADO EVENTO
            { name: 'nombre_estado_ppto', data: 'nombre_estado_ppto', className: "text-center" }, // Columna 11: ESTADO
            { name: 'activo_presupuesto', data: 'activo_presupuesto', className: "text-center" }, // Columna 12: ACTIVO
            { name: 'activar', data: null, className: "text-center" }, // Columna 13: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center" }  // Columna 14: EDITAR
        ],
        columnDefs: [
            // Columna 0: BOTÓN MÁS - Control responsive
            { 
                targets: "control:name", 
                width: '3%', 
                searchable: false, 
                orderable: false, 
                className: "control text-center",
                responsivePriority: 1
            },
            // Columna 1: id_presupuesto 
            { targets: "id_presupuesto:name", width: '3%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: numero_presupuesto
            { targets: "numero_presupuesto:name", width: '8%', searchable: true, orderable: true, className: "text-center", responsivePriority: 2 },
            // Columna 3: nombre_cliente
            { targets: "nombre_cliente:name", width: '12%', searchable: true, orderable: true, className: "text-center", responsivePriority: 3 },
            // Columna 4: nombre_evento_presupuesto
            { targets: "nombre_evento_presupuesto:name", width: '12%', searchable: true, orderable: true, className: "text-center", responsivePriority: 4 },
            // Columna 5: fecha_inicio_evento_presupuesto
            { 
                targets: "fecha_inicio_evento_presupuesto:name", width: '8%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.fecha_inicio_evento_presupuesto ? formatoFechaEuropeo(row.fecha_inicio_evento_presupuesto) : '<span class="text-muted">-</span>';
                    }
                    return row.fecha_inicio_evento_presupuesto;
                }
            },
            // Columna 6: fecha_fin_evento_presupuesto
            { 
                targets: "fecha_fin_evento_presupuesto:name", width: '8%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.fecha_fin_evento_presupuesto ? formatoFechaEuropeo(row.fecha_fin_evento_presupuesto) : '<span class="text-muted">-</span>';
                    }
                    return row.fecha_fin_evento_presupuesto;
                }
            },
            // Columna 7: dias_validez_restantes
            { 
                targets: "dias_validez_restantes:name", width: '6%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (row.dias_validez_restantes === null) return '<span class="text-muted">-</span>';
                        let dias = row.dias_validez_restantes;
                        let clase = dias < 0 ? 'text-danger' : (dias <= 7 ? 'text-warning' : 'text-success');
                        return `<span class="${clase} fw-bold">${dias}</span>`;
                    }
                    return row.dias_validez_restantes;
                }
            },
            // Columna 8: duracion_evento_dias
            { 
                targets: "duracion_evento_dias:name", width: '6%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.duracion_evento_dias ? `<span class="badge bg-info">${row.duracion_evento_dias}</span>` : '<span class="text-muted">-</span>';
                    }
                    return row.duracion_evento_dias;
                }
            },
            // Columna 9: dias_hasta_inicio_evento
            { 
                targets: "dias_hasta_inicio_evento:name", width: '6%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (row.dias_hasta_inicio_evento === null) return '<span class="text-muted">-</span>';
                        let dias = row.dias_hasta_inicio_evento;
                        let clase = dias < 0 ? 'text-muted' : (dias <= 7 ? 'text-danger' : 'text-primary');
                        return `<span class="${clase} fw-bold">${dias}</span>`;
                    }
                    return row.dias_hasta_inicio_evento;
                }
            },
            // Columna 10: estado_evento_presupuesto
            { 
                targets: "estado_evento_presupuesto:name", width: '10%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        let estado = row.estado_evento_presupuesto || '-';
                        let clase = 'bg-secondary';
                        if (estado.includes('finalizado')) clase = 'bg-dark';
                        else if (estado.includes('curso')) clase = 'bg-success';
                        else if (estado.includes('hoy')) clase = 'bg-danger';
                        else if (estado.includes('próximo')) clase = 'bg-warning';
                        else if (estado.includes('futuro')) clase = 'bg-info';
                        return `<span class="badge ${clase}">${estado}</span>`;
                    }
                    return row.estado_evento_presupuesto;
                }
            },
            // Columna 11: nombre_estado_ppto
            { targets: "nombre_estado_ppto:name", width: '8%', searchable: true, orderable: true, className: "text-center" },
            // Columna 12: activo_presupuesto (Estado)
            {
                targets: "activo_presupuesto:name", width: '5%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_presupuesto == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_presupuesto;
                }
            },
            // Columna 13: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '5%', searchable: false, orderable: false, class: "text-center", responsivePriority: 5,
                render: function (data, type, row) {
                    if (row.activo_presupuesto == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacPresupuesto" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_presupuesto="${row.id_presupuesto}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
                    } else {
                        return `<button class="btn btn-success btn-sm activarPresupuesto" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_presupuesto="${row.id_presupuesto}">
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
                    }
                }
            },
            // Columna 14: BOTON PARA EDITAR PRESUPUESTO
            {   
                targets: "editar:name", width: '5%', searchable: false, orderable: false, class: "text-center", responsivePriority: 6,
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarPresupuesto" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_presupuesto="${row.id_presupuesto}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                }
            }
        ],
        ajax: {
            url: '../../controller/presupuesto.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json);
                return json.data || json;
            }
        }
    };
    ////////////////////////////
    // FIN DE LA TABLA DE PRESUPUESTOS //
    ///////////////////////////

    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    var $table = $('#presupuestos_data');
    var $tableConfig = datatable_presupuestosConfig;
    var $tableBody = $('#presupuestos_data tbody');
    var $columnFilterInputs = $('#presupuestos_data tfoot input, #presupuestos_data tfoot select');

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
                        <i class="bi bi-file-earmark-text fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Presupuesto</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <table class="table table-borderless table-striped table-hover mb-0">
                        <tbody>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hash me-2"></i>Número Presupuesto
                                </th>
                                <td class="pe-4">
                                    ${d.numero_presupuesto || '<span class="text-muted fst-italic">Sin número</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-person me-2"></i>Cliente
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_cliente || '<span class="text-muted fst-italic">Sin cliente</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-event me-2"></i>Evento
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_evento_presupuesto || '<span class="text-muted fst-italic">Sin evento</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-geo-alt me-2"></i>Ubicación
                                </th>
                                <td class="pe-4">
                                    ${d.ubicacion_completa_evento_presupuesto || '<span class="text-muted fst-italic">Sin ubicación</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar me-2"></i>Fecha Presupuesto
                                </th>
                                <td class="pe-4">
                                    ${d.fecha_presupuesto ? formatoFechaEuropeo(d.fecha_presupuesto) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-check me-2"></i>Fecha Inicio Evento
                                </th>
                                <td class="pe-4">
                                    ${d.fecha_inicio_evento_presupuesto ? formatoFechaEuropeo(d.fecha_inicio_evento_presupuesto) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-x me-2"></i>Fecha Fin Evento
                                </th>
                                <td class="pe-4">
                                    ${d.fecha_fin_evento_presupuesto ? formatoFechaEuropeo(d.fecha_fin_evento_presupuesto) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-hourglass-split me-2"></i>Duración Evento
                                </th>
                                <td class="pe-4">
                                    ${d.duracion_evento_dias !== null ? '<span class="badge bg-info">' + d.duracion_evento_dias + ' días</span>' : '<span class="text-muted fst-italic">Sin duración</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-clock-history me-2"></i>Días hasta Inicio
                                </th>
                                <td class="pe-4">
                                    ${d.dias_hasta_inicio_evento !== null ? (() => {
                                        let dias = d.dias_hasta_inicio_evento;
                                        let clase = dias < 0 ? 'text-muted' : (dias <= 7 ? 'text-danger' : 'text-primary');
                                        return '<span class="' + clase + ' fw-bold">' + dias + ' días</span>';
                                    })() : '<span class="text-muted fst-italic">Sin cálculo</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-event me-2"></i>Estado Evento
                                </th>
                                <td class="pe-4">
                                    ${d.estado_evento_presupuesto ? (() => {
                                        let estado = d.estado_evento_presupuesto;
                                        let clase = 'bg-secondary';
                                        if (estado.includes('finalizado')) clase = 'bg-dark';
                                        else if (estado.includes('curso')) clase = 'bg-success';
                                        else if (estado.includes('hoy')) clase = 'bg-danger';
                                        else if (estado.includes('próximo')) clase = 'bg-warning';
                                        else if (estado.includes('futuro')) clase = 'bg-info';
                                        return '<span class="badge ' + clase + '">' + estado + '</span>';
                                    })() : '<span class="text-muted fst-italic">Sin estado</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-range me-2"></i>Días Validez Restantes
                                </th>
                                <td class="pe-4">
                                    ${d.dias_validez_restantes !== null ? (() => {
                                        let dias = d.dias_validez_restantes;
                                        let clase = dias < 0 ? 'text-danger' : (dias <= 7 ? 'text-warning' : 'text-success');
                                        return '<span class="' + clase + ' fw-bold">' + dias + ' días</span>';
                                    })() : '<span class="text-muted fst-italic">Sin validez</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-info-circle me-2"></i>Estado
                                </th>
                                <td class="pe-4">
                                    ${d.nombre_estado_ppto || '<span class="text-muted fst-italic">Sin estado</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-chat-left-text me-2"></i>Observaciones Internas
                                </th>
                                <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                    ${d.observaciones_internas_presupuesto || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Creado el:
                                </th>
                                <td class="pe-4">
                                    ${d.created_at_presupuesto ? formatoFechaEuropeo(d.created_at_presupuesto) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                </td>
                            </tr>
                            <tr>
                                <th scope="row" class="ps-4 w-25 align-top">
                                    <i class="bi bi-calendar-plus me-2"></i>Actualizado el:
                                </th>
                                <td class="pe-4">
                                  ${d.updated_at_presupuesto ? formatoFechaEuropeo(d.updated_at_presupuesto) : '<span class="text-muted fst-italic">Sin fecha</span>'}
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
    
    // NOTA: El modo responsive de DataTables maneja automáticamente 
    // el click en la columna de control para mostrar/ocultar detalles.
    // Ya no necesitamos el handler manual del click.
    
    /*
    $tableBody.on('click', 'td.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table_e.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(format(row.data())).show();
            tr.addClass('shown');
        }
    });
    */

    ////////////////////////////////////////////
    //   INICIO ZONA FUNCIONES DE APOYO      //
    //////////////////////////////////////////

    /////////////////////////////////////
    //   INICIO ZONA DELETE PRESUPUESTOS  //
    ///////////////////////////////////
    function desacPresupuesto(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar el presupuesto con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>Esto desactivará este presupuesto en el sistema</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/presupuesto.php?op=eliminar", { id_presupuesto: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Desactivado',
                        'El presupuesto ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }

    $(document).on('click', '.desacPresupuesto', function (event) {
        event.preventDefault();
        let id = $(this).data('id_presupuesto');
        desacPresupuesto(id);
    });
    ////////////////////////////////////
    //   FIN ZONA DELETE PRESUPUESTO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR PRESUPUESTO  //
    /////////////////////////////////////
    function activarPresupuesto(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar el presupuesto con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/presupuesto.php?op=activar", { id_presupuesto: id }, function (data) {

                    $table.DataTable().ajax.reload();

                    Swal.fire(
                        'Activado',
                        'El presupuesto ha sido activado',
                        'success'
                    )
                });
            }
        })
    }

    $(document).on('click', '.activarPresupuesto', function (event) {
        event.preventDefault();
        let id = $(this).data('id_presupuesto');
        console.log("id presupuesto:",id);
        
        activarPresupuesto(id);
    });
    ////////////////////////////////////
    //   FIN ZONA ACTIVAR PRESUPUESTO    //
    //////////////////////////////////

    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    $(document).on('click', '.editarPresupuesto', function (event) {
        event.preventDefault();
        
        let id = $(this).data('id_presupuesto');
        console.log("id presupuesto:", id);
        
        window.location.href = `formularioPresupuesto.php?modo=editar&id=${id}`;
    });
    ///////////////////////////////////////
    //        FIN ZONA EDITAR           //
    /////////////////////////////////////

    ////////////////////////////////////////////////////////
    //        ZONA FILTROS RADIOBUTTON CABECERA           //
    ///////////////////////////////////////////////////////
    $('input[name="filterStatus"]').on('change', function () {
        var value = $(this).val();

        if (value === "all") {
            table_e.column(7).search("").draw();
        } else {
            table_e.column(7).search(value).draw();
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

    ////////////////////////////////////////////
    //  INICIO ZONA FILTROS PIES y SEARCH     //
    //    NO ES NECESARIO TOCAR              //
    //     FUNCIONES NO TOCAR               // 
    ///////////////////////////////////////////

    $columnFilterInputs.on('keyup change', function () {
        var columnIndex = table_e.column($(this).closest('th')).index();
        var searchValue = $(this).val();

        table_e.column(columnIndex).search(searchValue).draw();

        updateFilterMessage();
    });

    function updateFilterMessage() {
        var activeFilters = false;

        $columnFilterInputs.each(function () {
            if ($(this).val() !== "") {
                activeFilters = true;
                return false;
            }
        });

        if (table_e.search() !== "") {
            activeFilters = true;
        }

        if (activeFilters) {
            $('#filter-alert').show();
        } else {
            $('#filter-alert').hide();
        }
    }

    table_e.on('search.dt', function () {
        updateFilterMessage();
    });

    $('#clear-filter').on('click', function () {
        table_e.destroy();

        $columnFilterInputs.each(function () {
            $(this).val('');
        });

        table_e = $table.DataTable($tableConfig);

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
