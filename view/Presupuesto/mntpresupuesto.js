$(document).ready(function () {
    // Agregar estilos CSS para mejorar la visualización
    if (!document.getElementById("presupuesto-styles")) {
        const style = document.createElement("style");
        style.id = "presupuesto-styles";
        style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }
        `;
        document.head.appendChild(style);
    }

    ///////////////////////////////////
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_presupuestosConfig = {
        processing: true,
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
            emptyTable: "No hay presupuestos registrados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ presupuestos",
            infoEmpty: "Mostrando 0 a 0 de 0 presupuestos",
            infoFiltered: "(filtrado de _MAX_ presupuestos totales)",
            lengthMenu: "Mostrar _MENU_ presupuestos por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron presupuestos que coincidan con la búsqueda",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            { name: 'id_presupuesto', data: 'id_presupuesto', visible: false, className: "text-center" }, // Columna 0: ID
            { name: 'numero_presupuesto', data: 'numero_presupuesto', className: "text-center" }, // Columna 1: NÚMERO
            { name: 'nombre_cliente', data: 'nombre_cliente', className: "text-center" }, // Columna 2: CLIENTE
            { name: 'nombre_evento_presupuesto', data: 'nombre_evento_presupuesto', className: "text-center" }, // Columna 3: EVENTO
            { name: 'fecha_inicio_evento_presupuesto', data: 'fecha_inicio_evento_presupuesto', className: "text-center" }, // Columna 4: FECHA INICIO
            { name: 'fecha_fin_evento_presupuesto', data: 'fecha_fin_evento_presupuesto', className: "text-center" }, // Columna 5: FECHA FIN
            { name: 'dias_validez_restantes', data: 'dias_validez_restantes', className: "text-center" }, // Columna 6: DÍAS VALIDEZ
            { name: 'duracion_evento_dias', data: 'duracion_evento_dias', className: "text-center" }, // Columna 7: DURACIÓN
            { name: 'dias_hasta_inicio_evento', data: 'dias_hasta_inicio_evento', className: "text-center" }, // Columna 8: DÍAS INICIO
            { name: 'estado_evento_presupuesto', data: 'estado_evento_presupuesto', className: "text-center" }, // Columna 9: ESTADO EVENTO
            { name: 'nombre_estado_ppto', data: 'nombre_estado_ppto', className: "text-center" }, // Columna 10: ESTADO
            { name: 'total_presupuesto', data: 'total_presupuesto', className: "text-center" }, // Columna 11: IMPORTE
            { name: 'activo_presupuesto', data: 'activo_presupuesto', className: "text-center" }, // Columna 12: ACTIVO
            { name: 'activar', data: null, className: "text-center" }, // Columna 13: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center" }, // Columna 14: EDITAR
            { name: 'lineas', data: null, defaultContent: '', className: "text-center" }  // Columna 15: LÍNEAS
        ],
        columnDefs: [
            // Columna 0: id_presupuesto
            { targets: "id_presupuesto:name", width: '3%', searchable: false, orderable: false, className: "text-center" },
            // Columna 1: numero_presupuesto CON BOTÓN
            { 
                targets: "numero_presupuesto:name", 
                width: '10%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function(data, type, row) {
                    if (type === 'display') {
                        return '<button class="btn btn-sm btn-primary me-2 details-control"><i class="bi bi-plus-circle"></i></button>' + data;
                    }
                    return data;
                }
            },
            // Columna 2: nombre_cliente
            { targets: "nombre_cliente:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: nombre_evento_presupuesto
            { targets: "nombre_evento_presupuesto:name", width: '12%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: fecha_inicio_evento_presupuesto
            {
                targets: "fecha_inicio_evento_presupuesto:name", width: '8%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.fecha_inicio_evento_presupuesto ? formatoFechaEuropeo(row.fecha_inicio_evento_presupuesto) : '<span class="text-muted">-</span>';
                    }
                    return row.fecha_inicio_evento_presupuesto;
                }
            },
            // Columna 5: fecha_fin_evento_presupuesto
            {
                targets: "fecha_fin_evento_presupuesto:name", width: '8%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.fecha_fin_evento_presupuesto ? formatoFechaEuropeo(row.fecha_fin_evento_presupuesto) : '<span class="text-muted">-</span>';
                    }
                    return row.fecha_fin_evento_presupuesto;
                }
            },
            // Columna 6: dias_validez_restantes
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
            // Columna 7: duracion_evento_dias
            {
                targets: "duracion_evento_dias:name", width: '6%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.duracion_evento_dias !== null ? `<span class="badge bg-info">${row.duracion_evento_dias}</span>` : '<span class="text-muted">-</span>';
                    }
                    return row.duracion_evento_dias;
                }
            },
            // Columna 8: dias_hasta_inicio_evento
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
            // Columna 9: estado_evento_presupuesto
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
            // Columna 10: nombre_estado_ppto
            { targets: "nombre_estado_ppto:name", width: '8%', searchable: true, orderable: true, className: "text-center" },
            // Columna 11: total_presupuesto
            {
                targets: "total_presupuesto:name", width: '8%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        let importe = parseFloat(row.total_presupuesto) || 0;
                        return '<span class="fw-bold text-success">' + importe.toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €</span>';
                    }
                    return row.total_presupuesto;
                }
            },
            // Columna 12: activo_presupuesto
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
                targets: "activar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    if (row.activo_presupuesto == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacPresupuesto" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" 
                             data-id_presupuesto="${row.id_presupuesto}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`;
                    } else {
                        return `<button class="btn btn-success btn-sm activarPresupuesto" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" 
                             data-id_presupuesto="${row.id_presupuesto}"> 
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
                    }
                }
            },
            // Columna 14: BOTON PARA EDITAR PRESUPUESTO
            {
                targets: "editar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarPresupuesto" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_presupuesto="${row.id_presupuesto}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`;
                }
            },
            // Columna 15: BOTON PARA GESTIONAR LÍNEAS DEL PRESUPUESTO
            {
                targets: "lineas:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-primary btn-sm gestionarLineas" data-toggle="tooltip-primary" data-placement="top" title="Gestionar líneas"  
                             data-id_presupuesto="${row.id_presupuesto}"> 
                             <i class="bi bi-list-ul"></i>
                             </button>`;
                }
            }
        ],
        ajax: {
            url: '../../controller/presupuesto.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido desde servidor:", json);
                console.log("Número de registros:", json.data ? json.data.length : json.length);

                if (!json || (!json.data && !Array.isArray(json))) {
                    console.warn("No se recibieron datos válidos del servidor");
                    return [];
                }

                return json.data || json;
            },
            error: function (xhr, status, error) {
                console.error("Error al cargar datos:", error);
                console.error("Status:", status);
                console.error("Response:", xhr.responseText);
            }
        },
        deferRender: true,
        scrollX: true,
        scrollCollapse: true,
        order: [[1, 'desc']],
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "Todos"]]
    };

    /************************************/
    //   FIN ZONA DE DEFINICIONES      //
    /**********************************/

    // Inicializar DataTable
    var $table = $('#presupuestos_data');
    var $tableBody = $table.find('tbody');
    var $tableConfig = datatable_presupuestosConfig;
    var table_e = $table.DataTable($tableConfig);

    // Click event para mostrar/ocultar detalles
    $tableBody.on('click', 'button.details-control', function () {
        var tr = $(this).closest('tr');
        var row = table_e.row(tr);
        var btn = $(this);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
            btn.html('<i class="bi bi-plus-circle"></i>');
        } else {
            var detalles = format(row.data());
            row.child(detalles).show();
            tr.addClass('shown');
            btn.html('<i class="bi bi-dash-circle"></i>');
        }
    });

    function format(d) {
        // Función auxiliar para formatear valores nulos
        const val = (value) => value !== null && value !== undefined && value !== '' ? value : '<span class="text-muted">-</span>';
        
        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Presupuesto</h5>
                    </div>
                </div>
                <div class="card-body p-3" style="overflow: visible;">
                    <div class="row">
                        
                        <!-- ========== COLUMNA 1 ========== -->
                        <div class="col-md-4">
                            
                            <!-- DATOS DEL PRESUPUESTO -->
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-file-text me-2"></i>Datos del Presupuesto
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-hash me-1"></i>ID:</strong> ${val(d.id_presupuesto)}</p>
                                <p class="mb-1"><strong><i class="bi bi-file-earmark-code me-1"></i>Número:</strong> ${val(d.numero_presupuesto)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar-event me-1"></i>F. Presupuesto:</strong> ${d.fecha_presupuesto ? formatoFechaEuropeo(d.fecha_presupuesto) : val(null)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar-check me-1"></i>F. Validez:</strong> ${d.fecha_validez_presupuesto ? formatoFechaEuropeo(d.fecha_validez_presupuesto) : val(null)}</p>
                              
                            </div>

                            <!-- DATOS DEL EVENTO -->
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="bi bi-geo-alt me-2"></i>Datos del Evento
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-pin-map me-1"></i>Ubicación:</strong></p>
                                <p class="ms-3 text-muted small">${val(d.ubicacion_completa_evento_presupuesto)}</p>
                                  <p class="mb-1"><strong><i class="bi bi-calendar3 me-1"></i>F. Inicio Evento:</strong> ${d.fecha_inicio_evento_presupuesto ? formatoFechaEuropeo(d.fecha_inicio_evento_presupuesto) : val(null)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar3 me-1"></i>F. Fin Evento:</strong> ${d.fecha_fin_evento_presupuesto ? formatoFechaEuropeo(d.fecha_fin_evento_presupuesto) : val(null)}</p>
                            </div>

                            <!-- DATOS DEL CLIENTE -->
                            <h6 class="text-info border-bottom pb-2 mb-3">
                                <i class="bi bi-person me-2"></i>Datos del Cliente
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-building me-1"></i>Dirección:</strong></p>
                                <p class="ms-3 text-muted small">${val(d.direccion_completa_cliente)}</p>
                                <p class="mb-1"><strong><i class="bi bi-receipt me-1"></i>Dir. Facturación:</strong></p>
                                <p class="ms-3 text-muted small">${val(d.direccion_facturacion_completa_cliente)}</p>
                            </div>
                        </div>

                        <!-- ========== COLUMNA 2 ========== -->
                        <div class="col-md-4">
                            
                            <!-- OBSERVACIONES -->
                            <h6 class="text-warning border-bottom pb-2 mb-3">
                                <i class="bi bi-chat-square-text me-2"></i>Observaciones
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-file-text me-1"></i>Obs. Cabecera:</strong></p>
                                <p class="ms-3 text-muted small">${val(d.observaciones_cabecera_presupuesto)}</p>
                                
                                <p class="mb-1"><strong><i class="bi bi-file-text me-1"></i>Obs. Pie:</strong></p>
                                <p class="ms-3 text-muted small">${val(d.observaciones_pie_presupuesto)}</p>
                                <p class="mb-1"><strong><i class="bi bi-lock me-1"></i>Obs. Internas:</strong></p>
                                <p class="ms-3 text-muted small">${val(d.observaciones_internas_presupuesto)}</p>

                                <p class="mb-1"><strong><i class="bi bi-eye me-1"></i>Mostrar Obs. Familias:</strong> ${d.mostrar_obs_familias_presupuesto == 1 ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>'}</p>
                                <p class="mb-1"><strong><i class="bi bi-eye me-1"></i>Mostrar Obs. Artículos:</strong> ${d.mostrar_obs_articulos_presupuesto == 1 ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-secondary">No</span>'}</p>
                            </div>

                            <!-- DATOS DEL CONTACTO -->
                            <h6 class="text-secondary border-bottom pb-2 mb-3">
                                <i class="bi bi-person-lines-fill me-2"></i>Contacto del Cliente
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-person-badge me-1"></i>Nombre:</strong> ${val(d.nombre_completo_contacto_cliente)}</p>
                            </div>

                            <!-- DATOS DEL MÉTODO DE CONTACTO -->
                            <h6 class="text-dark border-bottom pb-2 mb-3">
                                <i class="bi bi-telephone me-2"></i>Método de Contacto
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-hash me-1"></i>ID Método:</strong> ${val(d.id_metodo)}</p>
                                <p class="mb-1"><strong><i class="bi bi-envelope me-1"></i>Método:</strong> ${val(d.nombre_metodo_contacto)}</p>
                            </div>
                        </div>

                        <!-- ========== COLUMNA 3 ========== -->
                        <div class="col-md-4">
                            
                            <!-- DATOS DEL ESTADO -->
                            <h6 class="text-danger border-bottom pb-2 mb-3">
                                <i class="bi bi-flag me-2"></i>Estado del Presupuesto
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-bookmark me-1"></i>Estado:</strong> 
                                    <span class="badge" style="background-color: ${d.color_estado_ppto || '#6c757d'};">
                                        ${val(d.nombre_estado_ppto)}
                                    </span>
                                </p>
                            </div>

                            <!-- DATOS DE LA FORMA DE PAGO -->
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="bi bi-credit-card me-2"></i>Forma de Pago
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-upc me-1"></i>Código:</strong> ${val(d.codigo_pago)}</p>
                                <p class="mb-1"><strong><i class="bi bi-tag me-1"></i>Nombre:</strong> ${val(d.nombre_pago)}</p>
                                <p class="mb-1"><strong><i class="bi bi-percent me-1"></i>% Anticipo:</strong> ${val(d.porcentaje_anticipo_pago)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar-range me-1"></i>Días Anticipo:</strong> ${val(d.dias_anticipo_pago)}</p>
                                <p class="mb-1"><strong><i class="bi bi-percent me-1"></i>% Final:</strong> ${val(d.porcentaje_final_pago)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar-range me-1"></i>Días Final:</strong> ${val(d.dias_final_pago)}</p>
                                <p class="mb-1"><strong><i class="bi bi-piggy-bank me-1"></i>Descuento:</strong> ${val(d.descuento_pago)}</p>
                                <p class="mb-1"><strong><i class="bi bi-cash me-1"></i>Tipo Pago:</strong> ${val(d.tipo_pago_presupuesto)}</p>
                            </div>

                            <!-- CONTROL -->
                            <h6 class="text-muted border-bottom pb-2 mb-3">
                                <i class="bi bi-info-circle me-2"></i>Control
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-toggle-on me-1"></i>Activo:</strong> ${d.activo_presupuesto == 1 ? '<span class="badge bg-success">Sí</span>' : '<span class="badge bg-danger">No</span>'}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar-plus me-1"></i>Creado:</strong> ${d.created_at_presupuesto ? formatoFechaEuropeo(d.created_at_presupuesto) : val(null)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar-event me-1"></i>Actualizado:</strong> ${d.updated_at_presupuesto ? formatoFechaEuropeo(d.updated_at_presupuesto) : val(null)}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
    }

    /************************************/
    //     FILTROS DE BÚSQUEDA         //
    /************************************/
    $('#presupuestos_data tfoot th').each(function (index) {
        var input = $(this).find('input, select');
        
        if (input.length) {
            input.on('keyup change', function () {
                var column = table_e.column(index);
                if (column.search() !== this.value) {
                    column.search(this.value).draw();
                }
                updateFilterMessage();
            });
        }
    });

    // Limpiar filtros
    $('#clear-filter').on('click', function () {
        table_e.search('').columns().search('').draw();
        $('tfoot input, tfoot select').val('');
        updateFilterMessage();
    });

    function updateFilterMessage() {
        var activeFiltersCount = 0;
        $('tfoot input, tfoot select').each(function () {
            if ($(this).val()) {
                activeFiltersCount++;
            }
        });

        if (activeFiltersCount > 0) {
            $('#active-filters-text').text('');
            $('#filter-alert').show();
        } else {
            $('#filter-alert').hide();
        }
    }

    /************************************/
    //     EVENTOS DE BOTONES          //
    /************************************/

    // Desactivar presupuesto
    $(document).on('click', '.desacPresupuesto', function () {
        var id_presupuesto = $(this).data('id_presupuesto');
        desacPresupuesto(id_presupuesto);
    });

    // Activar presupuesto
    $(document).on('click', '.activarPresupuesto', function () {
        var id_presupuesto = $(this).data('id_presupuesto');
        activarPresupuesto(id_presupuesto);
    });

    // Editar presupuesto
    $(document).on('click', '.editarPresupuesto', function () {
        var id_presupuesto = $(this).data('id_presupuesto');
        window.location.href = 'formularioPresupuesto.php?modo=editar&id=' + id_presupuesto;
    });

    // Gestionar líneas del presupuesto
    $(document).on('click', '.gestionarLineas', function () {
        var id_presupuesto = $(this).data('id_presupuesto');
        window.location.href = './Presupuesto_pies/index.php?id_presupuesto=' + id_presupuesto;
    });

    /************************************/
    //     FUNCIONES                   //
    /************************************/

    function desacPresupuesto(id_presupuesto) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se desactivará el presupuesto",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, desactivar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../controller/presupuesto.php?op=desactivar',
                    type: 'POST',
                    data: { id_presupuesto: id_presupuesto },
                    success: function (response) {
                        Swal.fire('Desactivado!', 'El presupuesto ha sido desactivado.', 'success');
                        table_e.ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Error!', 'No se pudo desactivar el presupuesto.', 'error');
                    }
                });
            }
        });
    }

    function activarPresupuesto(id_presupuesto) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: "Se activará el presupuesto",
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, activar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../controller/presupuesto.php?op=activar',
                    type: 'POST',
                    data: { id_presupuesto: id_presupuesto },
                    success: function (response) {
                        Swal.fire('Activado!', 'El presupuesto ha sido activado.', 'success');
                        table_e.ajax.reload();
                    },
                    error: function () {
                        Swal.fire('Error!', 'No se pudo activar el presupuesto.', 'error');
                    }
                });
            }
        });
    }

}); // Fin document.ready

// Función global para formatear fechas
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return '-';
    var fecha = new Date(fechaString);
    var dia = ("0" + fecha.getDate()).slice(-2);
    var mes = ("0" + (fecha.getMonth() + 1)).slice(-2);
    var anio = fecha.getFullYear();
    return dia + "/" + mes + "/" + anio;
}
