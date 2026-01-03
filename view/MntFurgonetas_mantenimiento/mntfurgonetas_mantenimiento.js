$(document).ready(function () {
  // Agregar estilos CSS para mejorar la visualización
  if (!document.getElementById("mantenimiento-styles")) {
    const style = document.createElement("style");
    style.id = "mantenimiento-styles";
    style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }
            .badge-tipo {
                padding: 0.5em 0.8em;
                font-size: 0.85em;
            }
        `;
    document.head.appendChild(style);
  }

    // Obtener el ID de la furgoneta desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const idFurgoneta = urlParams.get('id_furgoneta');

    // Cargar información de la furgoneta
    if (idFurgoneta) {
        cargarInfoFurgoneta(idFurgoneta);
    }

    /////////////////////////////////////
    // INICIO DE LA TABLA DE MANTENIMIENTOS //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_mantenimientosConfig = {
        processing: true,
        scrollX: true, // Habilita el desplazamiento horizontal
        scrollCollapse: true, // Permite que la tabla se ajuste al contenedor y que el scroll se oculte cuando no es necesario
        fixedColumns: {
        left: 2  // Fija columnas: control (0), id_articulo oculto (1) 
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
            emptyTable: "No hay mantenimientos registrados para esta furgoneta",
            info: "Mostrando _START_ a _END_ de _TOTAL_ mantenimientos",
            infoEmpty: "Mostrando 0 a 0 de 0 mantenimientos",
            infoFiltered: "(filtrado de _MAX_ mantenimientos totales)",
            lengthMenu: "Mostrar _MENU_ mantenimientos por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron mantenimientos que coincidan con la búsqueda",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' },
            { name: 'id_mantenimiento', data: 'id_mantenimiento', visible: false, className: "text-center" },
            { name: 'fecha_mantenimiento', data: 'fecha_mantenimiento', className: "text-center align-middle" },
            { name: 'tipo_mantenimiento', data: 'tipo_mantenimiento', className: "text-center align-middle" },
            { name: 'descripcion_mantenimiento', data: 'descripcion_mantenimiento', className: "text-left align-middle" },
            { name: 'kilometraje_mantenimiento', data: 'kilometraje_mantenimiento', className: "text-center align-middle" },
            { name: 'costo_mantenimiento', data: 'costo_mantenimiento', className: "text-right align-middle" },
            { name: 'taller_mantenimiento', data: 'taller_mantenimiento', className: "text-center align-middle" },
            { name: 'activo_mantenimiento', data: 'activo_mantenimiento', className: "text-center align-middle" },
            { name: 'activar', data: null, className: "text-center align-middle" },
            { name: 'editar', data: null, defaultContent: '', className: "text-center align-middle" }
        ],
        columnDefs: [
            { targets: "control:name", width: '3%', searchable: false, orderable: false, className: "text-center"},
            { targets: "id_mantenimiento:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            { 
                targets: "fecha_mantenimiento:name", 
                width: '10%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display" && data) {
                        return formatoFechaSolo(data);
                    }
                    return data;
                }
            },
            { 
                targets: "tipo_mantenimiento:name", 
                width: '10%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const badges = {
                            'revision': '<span class="badge badge-tipo bg-primary">Revisión</span>',
                            'reparacion': '<span class="badge badge-tipo bg-warning text-dark">Reparación</span>',
                            'itv': '<span class="badge badge-tipo bg-success">ITV</span>',
                            'neumaticos': '<span class="badge badge-tipo bg-info">Neumáticos</span>',
                            'otros': '<span class="badge badge-tipo bg-secondary">Otros</span>'
                        };
                        return badges[data] || data;
                    }
                    return data;
                }
            },
            { 
                targets: "descripcion_mantenimiento:name", 
                width: '25%', 
                searchable: true, 
                orderable: false, 
                className: "text-left",
                render: function (data, type, row) {
                    if (type === "display" && data) {
                        return data.length > 50 ? data.substring(0, 50) + '...' : data;
                    }
                    return data;
                }
            },
            { 
                targets: "kilometraje_mantenimiento:name", 
                width: '10%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data ? new Intl.NumberFormat('es-ES').format(data) + ' km' : '-';
                    }
                    return data;
                }
            },
            { 
                targets: "costo_mantenimiento:name", 
                width: '10%', 
                searchable: true, 
                orderable: true, 
                className: "text-right",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(data) : '-';
                    }
                    return data;
                }
            },
            { 
                targets: "taller_mantenimiento:name", 
                width: '12%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    return data || '-';
                }
            },
            {
                targets: "activo_mantenimiento:name", 
                width: '7%', 
                orderable: true, 
                searchable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_mantenimiento == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_mantenimiento;
                }
            },
            {   
                targets: "activar:name", 
                width: '7%', 
                searchable: false, 
                orderable: false, 
                class: "text-center",
                render: function (data, type, row) {
                    if (row.activo_mantenimiento == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacMantenimiento" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" 
                             data-id_mantenimiento="${row.id_mantenimiento}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        return `<button class="btn btn-success btn-sm activarMantenimiento" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" 
                             data-id_mantenimiento="${row.id_mantenimiento}"> 
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                }
            },
            {   
                targets: "editar:name", 
                width: '7%', 
                searchable: false, 
                orderable: false, 
                class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarMantenimiento" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_mantenimiento="${row.id_mantenimiento}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                }
            }
        ],
        ajax: {
            url: '../../controller/furgoneta_mantenimiento.php?op=listar_por_furgoneta',
            type: 'POST',
            data: function() {
                return {
                    id_furgoneta: idFurgoneta
                };
            },
            dataSrc: function (json) {
                console.log("JSON recibido desde servidor:", json);
                console.log("Número de registros:", json.data ? json.data.length : json.length);
                
                if (!json || (!json.data && !Array.isArray(json))) {
                    console.warn("No se recibieron datos válidos del servidor");
                    return [];
                }
                
                return json.data || json;
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar datos:", error);
                console.error("Status:", status);
                console.error("Response:", xhr.responseText);
            }
        },
        deferRender: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[2, 'desc']] // Ordenar por fecha descendente
    };

    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    var $table = $('#mantenimientos_data');
    var $tableConfig = datatable_mantenimientosConfig;
    var $tableBody = $('#mantenimientos_data tbody');
    var $columnFilterInputs = $('#mantenimientos_data tfoot input, #mantenimientos_data tfoot select');

    var table_e = $table.DataTable($tableConfig);

    function format(d) {
        console.log(d);

        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-wrench fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Mantenimiento</h5>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <div class="row">
                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-hash me-2"></i>ID Mantenimiento
                                        </th>
                                        <td class="pe-4">
                                            ${d.id_mantenimiento || '<span class="text-muted fst-italic">Sin ID</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar me-2"></i>Fecha
                                        </th>
                                        <td class="pe-4">
                                            ${d.fecha_mantenimiento ? formatoFechaSolo(d.fecha_mantenimiento) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-tag me-2"></i>Tipo
                                        </th>
                                        <td class="pe-4">
                                            ${d.tipo_mantenimiento || '<span class="text-muted fst-italic">Sin tipo</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-speedometer2 me-2"></i>Kilometraje
                                        </th>
                                        <td class="pe-4">
                                            ${d.kilometraje_mantenimiento ? new Intl.NumberFormat('es-ES').format(d.kilometraje_mantenimiento) + ' km' : '<span class="text-muted fst-italic">Sin registrar</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-cash me-2"></i>Costo
                                        </th>
                                        <td class="pe-4">
                                            ${d.costo_mantenimiento ? new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(d.costo_mantenimiento) : '<span class="text-muted fst-italic">Sin costo</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-receipt me-2"></i>Nº Factura
                                        </th>
                                        <td class="pe-4">
                                            ${d.numero_factura_mantenimiento || '<span class="text-muted fst-italic">Sin factura</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-building me-2"></i>Taller
                                        </th>
                                        <td class="pe-4">
                                            ${d.taller_mantenimiento || '<span class="text-muted fst-italic">Sin taller</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-telephone me-2"></i>Teléfono Taller
                                        </th>
                                        <td class="pe-4">
                                            ${d.telefono_taller_mantenimiento || '<span class="text-muted fst-italic">Sin teléfono</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-geo-alt me-2"></i>Dirección Taller
                                        </th>
                                        <td class="pe-4">
                                            ${d.direccion_taller_mantenimiento || '<span class="text-muted fst-italic">Sin dirección</span>'}
                                        </td>
                                    </tr>
                                    ${d.resultado_itv ? `
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-clipboard-check me-2"></i>Resultado ITV
                                        </th>
                                        <td class="pe-4">
                                            ${d.resultado_itv}
                                        </td>
                                    </tr>
                                    ` : ''}
                                    ${d.fecha_proxima_itv ? `
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar-event me-2"></i>Próxima ITV
                                        </th>
                                        <td class="pe-4">
                                            ${formatoFechaSolo(d.fecha_proxima_itv)}
                                        </td>
                                    </tr>
                                    ` : ''}
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-shield-check me-2"></i>Garantía Hasta
                                        </th>
                                        <td class="pe-4">
                                            ${d.garantia_hasta_mantenimiento ? formatoFechaSolo(d.garantia_hasta_mantenimiento) : '<span class="text-muted fst-italic">Sin garantía</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-chat-text me-2"></i>Observaciones
                                        </th>
                                        <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            ${d.observaciones_mantenimiento || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12">
                            <table class="table table-borderless table-striped mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-4 w-20 align-top">
                                            <i class="bi bi-card-text me-2"></i>Descripción Completa
                                        </th>
                                        <td class="pe-4" style="word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            ${d.descripcion_mantenimiento || '<span class="text-muted fst-italic">Sin descripción</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-20 align-top">
                                            <i class="bi bi-calendar-plus me-2"></i>Creado el
                                        </th>
                                        <td class="pe-4">
                                            ${d.created_at_mantenimiento ? formatoFechaEuropeo(d.created_at_mantenimiento) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-20 align-top">
                                            <i class="bi bi-calendar-check me-2"></i>Actualizado el
                                        </th>
                                        <td class="pe-4">
                                            ${d.updated_at_mantenimiento ? formatoFechaEuropeo(d.updated_at_mantenimiento) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }
    
    // Control de expansión de filas
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

    ////////////////////////////////////////////
    //   INICIO ZONA FUNCIONES DE APOYO      //
    //////////////////////////////////////////

    // Función para cargar información de la furgoneta
    function cargarInfoFurgoneta(id_furgoneta) {
        $.post("../../controller/furgoneta.php?op=mostrar", { id_furgoneta: id_furgoneta }, function (data) {
            if (data) {
                $('#matricula-furgoneta').text(data.matricula_furgoneta || 'Sin matrícula');
                $('#marca-furgoneta').text(data.marca_furgoneta || '');
                $('#modelo-furgoneta').text(data.modelo_furgoneta || '');
                $('#id-furgoneta').text(id_furgoneta);
            }
        }, 'json').fail(function() {
            $('#matricula-furgoneta').text('Error al cargar');
        });
    }

    /////////////////////////////////////
    //   INICIO ZONA DELETE MANTENIMIENTO   //
    ///////////////////////////////////
    function desacMantenimiento(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar el mantenimiento con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>El registro quedará marcado como inactivo</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/furgoneta_mantenimiento.php?op=eliminar", { id_mantenimiento: id }, function (data) {
                    $table.DataTable().ajax.reload();
                    Swal.fire(
                        'Desactivado',
                        'El mantenimiento ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacMantenimiento', function (event) {
        event.preventDefault();
        let id = $(this).data('id_mantenimiento');
        desacMantenimiento(id);
    });

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR MANTENIMIENTO    //
    /////////////////////////////////////
    function activarMantenimiento(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar el mantenimiento con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/furgoneta_mantenimiento.php?op=activar", { id_mantenimiento: id }, function (data) {
                    $table.DataTable().ajax.reload();
                    Swal.fire(
                        'Activado',
                        'El mantenimiento ha sido activado',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarMantenimiento', function (event) {
        event.preventDefault();
        let id = $(this).data('id_mantenimiento');
        console.log("id mantenimiento:",id);
        activarMantenimiento(id);
    });

    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarMantenimiento', function (event) {
        event.preventDefault();

        let id = $(this).data('id_mantenimiento');
        console.log("id mantenimiento:", id);

        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioMantenimiento.php?modo=editar&id=${id}&id_furgoneta=${idFurgoneta}`;
    });

    /*********************************************************** */
    /********************************************************** */
    /* A PARTIR DE AQUI NO TOCAR  SE ACTUALIZA AUTOMATICAMENTE */
    /******************************************************** */
    /******************************************************* */

    /////////////////////////////////////
    //  INICIO ZONA FILTROS PIES y SEARCH     //
    //    NO ES NECESARIO TOCAR              //
    //     FUNCIONES NO TOCAR               // 
    ///////////////////////////////////////////

    // Filtro de cada columna en el pie de la tabla (tfoot)
    $columnFilterInputs.on('keyup change', function () {
        var columnIndex = table_e.column($(this).closest('th')).index();
        var searchValue = $(this).val();

        table_e.column(columnIndex).search(searchValue).draw();
        updateFilterMessage();
    });

    // Función para actualizar el mensaje de filtro activo
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

    // Botón para limpiar los filtros
    $('#clear-filter').on('click', function () {
        table_e.destroy();

        $columnFilterInputs.each(function () {
            $(this).val('');
        });

        table_e = $table.DataTable($tableConfig);
        $('#filter-alert').hide();
    });

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
        const horas = fecha.getHours().toString().padStart(2, '0');
        const minutos = fecha.getMinutes().toString().padStart(2, '0');
        
        return `${dia}/${mes}/${año} ${horas}:${minutos}`;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Error en fecha';
    }
}

// Función global para formatear solo fecha (sin hora)
function formatoFechaSolo(fechaString) {
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
