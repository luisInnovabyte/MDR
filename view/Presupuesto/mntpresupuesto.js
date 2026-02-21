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
            /* Dropdown acciones dentro de DataTables / fixedColumns */
            .dtfc-fixed-left .dropdown-menu,
            .dtfc-fixed-right .dropdown-menu,
            table.dataTable td .dropdown-menu {
                z-index: 9999 !important;
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
            { name: 'numero_presupuesto', data: 'numero_presupuesto', className: "text-start" }, // Columna 1: NÚMERO
            { name: 'nombre_cliente', data: 'nombre_cliente', className: "text-center align-middle" }, // Columna 2: CLIENTE
            { name: 'nombre_evento_presupuesto', data: 'nombre_evento_presupuesto', className: "text-center align-middle" }, // Columna 3: EVENTO
            { name: 'fecha_inicio_evento_presupuesto', data: 'fecha_inicio_evento_presupuesto', className: "text-center align-middle" }, // Columna 4: FECHA INICIO
            { name: 'fecha_fin_evento_presupuesto', data: 'fecha_fin_evento_presupuesto', className: "text-center align-middle" }, // Columna 5: FECHA FIN
            { name: 'duracion_evento_dias', data: 'duracion_evento_dias', className: "text-center align-middle" }, // Columna 6: DURACIÓN
            { name: 'dias_hasta_inicio_evento', data: 'dias_hasta_inicio_evento', className: "text-center align-middle" }, // Columna 7: DÍAS INICIO
            { name: 'estado_evento_presupuesto', data: 'estado_evento_presupuesto', className: "text-center align-middle" }, // Columna 8: ESTADO EVENTO
            { name: 'dias_validez_restantes', data: 'dias_validez_restantes', className: "text-center align-middle" }, // Columna 9: DÍAS VALIDEZ
            { name: 'nombre_estado_ppto', data: 'nombre_estado_ppto', className: "text-center align-middle" }, // Columna 10: ESTADO
            { name: 'activo_presupuesto', data: 'activo_presupuesto', className: "text-center align-middle" }, // Columna 11: ACTIVO
            { name: 'acciones', data: null, defaultContent: '', className: "text-center align-middle" }  // Columna 13: ACCIONES (dropdown)
        ],
        columnDefs: [
            // Columna 0: id_presupuesto
            { targets: "id_presupuesto:name", width: '3%', searchable: false, orderable: false, className: "text-center align-middle" },
            // Columna 1: numero_presupuesto CON BOTÓN
            { 
                targets: "numero_presupuesto:name", 
                width: '10%', 
                searchable: true, 
                orderable: true, 
                className: "text-start align-middle ",
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
            // Columna 6: duracion_evento_dias
            {
                targets: "duracion_evento_dias:name", width: '6%', searchable: true, orderable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.duracion_evento_dias !== null ? `<span class="badge bg-info">${row.duracion_evento_dias}</span>` : '<span class="text-muted">-</span>';
                    }
                    return row.duracion_evento_dias;
                }
            },
            // Columna 7: dias_hasta_inicio_evento
           // ...existing code...
           // Columna 7: dias_hasta_inicio_evento
           {
               targets: "dias_hasta_inicio_evento:name", width: '6%', searchable: true, orderable: true, className: "text-center",
               render: function (data, type, row) {
                   if (type === "display") {
                       if (row.dias_hasta_inicio_evento === null) return '<span class="text-muted">-</span>';
                       let dias = row.dias_hasta_inicio_evento;
                       if (dias < 0) return '<span class="text-muted">-</span>';
                       let clase = dias <= 7 ? 'text-danger' : 'text-primary';
                       return `<span class="${clase} fw-bold">${dias}</span>`;
                   }
                   return row.dias_hasta_inicio_evento;
               }
           },
            // Columna 8: estado_evento_presupuesto
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
            // Columna 9: dias_validez_restantes
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
            // Columna 10: nombre_estado_ppto
            { targets: "nombre_estado_ppto:name", width: '8%', searchable: true, orderable: true, className: "text-center" },
            // Columna 11: activo_presupuesto
            {
                targets: "activo_presupuesto:name", width: '5%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_presupuesto == 1 ? '<i class="bi bi-check-circle text-success fa-2x"></i>' : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_presupuesto;
                }
            },
            // Columna 13: ACCIONES — Dropdown Bootstrap con todas las acciones
            {
                targets: "acciones:name", width: '5%', searchable: false, orderable: false, className: "text-center align-middle",
                render: function (data, type, row) {
                    var tieneVersion = row.id_version_actual ? '' : ' disabled';
                    var labelActivar = row.activo_presupuesto == 1
                        ? '<i class="fa-solid fa-trash me-1"></i>Desactivar'
                        : '<i class="bi bi-hand-thumbs-up-fill me-1"></i>Activar';
                    var claseActivar = row.activo_presupuesto == 1 ? 'desacPresupuesto' : 'activarPresupuesto';
                    var colorActivar = row.activo_presupuesto == 1 ? 'text-danger' : 'text-success';
                    return `<div class="dropdown">
                        <button type="button" class="btn btn-sm btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="fas fa-bolt"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow">
                            <li><a class="dropdown-item editarPresupuesto" href="#" data-id_presupuesto="${row.id_presupuesto}"><i class="fa-solid fa-edit me-2 text-info"></i>Editar</a></li>
                            <li><a class="dropdown-item gestionarLineas${tieneVersion}" href="#" data-id_version_presupuesto="${row.id_version_actual}" data-numero_version="${row.numero_version_actual || 1}"><i class="fas fa-list me-2 text-info"></i>Gestionar Líneas</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item verVersiones" href="#" data-id_presupuesto="${row.id_presupuesto}" data-numero_presupuesto="${row.numero_presupuesto}" data-nombre_cliente="${row.nombre_cliente || ''}" data-nombre_evento="${row.nombre_evento_presupuesto || ''}"><i class="fas fa-history me-2 text-secondary"></i>Historial versiones</a></li>
                            <li><a class="dropdown-item copiarPresupuesto" href="#" data-id_presupuesto="${row.id_presupuesto}" data-numero_presupuesto="${row.numero_presupuesto}"><i class="fas fa-copy me-2 text-warning"></i>Copiar presupuesto</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item cambiarEstadoPpto" href="#" data-id_presupuesto="${row.id_presupuesto}" data-id_estado_actual="${row.id_estado_ppto}"><i class="fas fa-exchange-alt me-2 text-primary"></i>Cambiar estado</a></li>
                            <li><a class="dropdown-item imprimirPresupuesto" href="#" data-id_presupuesto="${row.id_presupuesto}" data-id_empresa="${row.id_empresa}" data-codigo_estado="${row.codigo_estado_ppto}" data-numero_presupuesto="${row.numero_presupuesto}" data-nombre_cliente="${row.nombre_cliente}"><i class="fas fa-print me-2 text-success"></i>Imprimir (opciones)</a></li>
                            <li><a class="dropdown-item pdfRapido" href="#" data-id_presupuesto="${row.id_presupuesto}"><i class="fas fa-bolt me-2 text-success"></i>PDF rápido</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item ${claseActivar}" href="#" data-id_presupuesto="${row.id_presupuesto}"><span class="${colorActivar}">${labelActivar}</span></a></li>
                        </ul>
                    </div>`;
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
        fixedColumns: {
            left: 3  // Fija las 3 primeras columnas (ID, Número, Fecha)
        },
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

    // Función para aplicar colores a los encabezados
    function aplicarColoresEncabezados() {
        // Primera fila - encabezados agrupados
        $('#presupuestos_data thead tr:first-child th[colspan="8"]').css({
            'background-color': '#d1ecf1',
            'color': '#0c5460',
            'border-color': '#bee5eb'
        });
        
        $('#presupuestos_data thead tr:first-child th[colspan="2"]').css({
            'background-color': '#d4edda',
            'color': '#155724',
            'border-color': '#c3e6cb'
        });
        
        // Segunda fila - columnas individuales del grupo EVENTO (columnas 1-8)
        $('#presupuestos_data thead tr:nth-child(2) th:nth-child(1), ' +
          '#presupuestos_data thead tr:nth-child(2) th:nth-child(2), ' +
          '#presupuestos_data thead tr:nth-child(2) th:nth-child(3), ' +
          '#presupuestos_data thead tr:nth-child(2) th:nth-child(4), ' +
          '#presupuestos_data thead tr:nth-child(2) th:nth-child(5), ' +
          '#presupuestos_data thead tr:nth-child(2) th:nth-child(6), ' +
          '#presupuestos_data thead tr:nth-child(2) th:nth-child(7), ' +
          '#presupuestos_data thead tr:nth-child(2) th:nth-child(8)').css({
            'background-color': '#d1ecf1',
            'color': '#0c5460',
            'border-color': '#bee5eb'
        });
        
        // Segunda fila - columnas individuales del grupo PRESUPUESTO (columnas 9-10)
        $('#presupuestos_data thead tr:nth-child(2) th:nth-child(9), ' +
          '#presupuestos_data thead tr:nth-child(2) th:nth-child(10)').css({
            'background-color': '#d4edda',
            'color': '#155724',
            'border-color': '#c3e6cb'
        });
    }

    // Aplicar colores después de inicializar la tabla
    aplicarColoresEncabezados();
    
    // Reaplicar colores cuando se redibuje la tabla
    table_e.on('draw', function() {
        aplicarColoresEncabezados();
    });

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
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-text-fill fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Presupuesto</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        
                        <!-- ========== COLUMNA 1 ========== -->
                        <div class="col-md-4" style="overflow-x: auto; overflow-y: visible;">
                            
                            <!-- DATOS DEL PRESUPUESTO -->
                            <h6 class="text-primary border-bottom pb-2 mb-3">
                                <i class="bi bi-file-text me-2"></i>Datos del Presupuesto
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-hash me-1"></i>ID:</strong> ${val(d.id_presupuesto)}</p>
                                <p class="mb-1"><strong><i class="bi bi-file-earmark-code me-1"></i>Número:</strong> ${val(d.numero_presupuesto)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar-event me-1"></i>F. Presupuesto:</strong> ${d.fecha_presupuesto ? formatoFechaEuropeo(d.fecha_presupuesto) : val(null)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar-check me-1"></i>F. Validez:</strong> ${d.fecha_validez_presupuesto ? formatoFechaEuropeo(d.fecha_validez_presupuesto) : val(null)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calculator me-1"></i>Aplicar Coeficientes:</strong> ${d.aplicar_coeficientes_presupuesto == 1 ? '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Activado</span>' : '<span class="badge bg-warning text-dark"><i class="bi bi-x-circle me-1"></i>Desactivado</span>'}</p>
                                <p class="mb-1"><strong><i class="bi bi-percent me-1"></i>Descuento (%):</strong> 
                                    <span class="badge bg-success">${parseFloat(d.descuento_presupuesto || 0).toFixed(2)}%</span>
                                    ${d.porcentaje_descuento_cliente && parseFloat(d.descuento_presupuesto || 0) !== parseFloat(d.porcentaje_descuento_cliente || 0) ? 
                                        '<small class="text-muted ms-1">(Cliente: ' + parseFloat(d.porcentaje_descuento_cliente || 0).toFixed(2) + '%)</small>' : 
                                        ''}
                                </p>
                              
                            </div>

                            <!-- DATOS DEL EVENTO -->
                            <h6 class="text-success border-bottom pb-2 mb-3">
                                <i class="bi bi-geo-alt me-2"></i>Datos del Evento
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-pin-map me-1"></i>Ubicación:</strong></p>
                                <p class="ms-3 text-muted small" style="word-break: break-word; overflow-wrap: break-word; max-width: 100%;">${val(d.ubicacion_completa_evento_presupuesto)}</p>
                                  <p class="mb-1"><strong><i class="bi bi-calendar3 me-1"></i>F. Inicio Evento:</strong> ${d.fecha_inicio_evento_presupuesto ? formatoFechaEuropeo(d.fecha_inicio_evento_presupuesto) : val(null)}</p>
                                <p class="mb-1"><strong><i class="bi bi-calendar3 me-1"></i>F. Fin Evento:</strong> ${d.fecha_fin_evento_presupuesto ? formatoFechaEuropeo(d.fecha_fin_evento_presupuesto) : val(null)}</p>
                            </div>

                            <!-- DATOS DEL CLIENTE -->
                            <h6 class="text-info border-bottom pb-2 mb-3">
                                <i class="bi bi-person me-2"></i>Datos del Cliente
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-building me-1"></i>Dirección:</strong></p>
                                <p class="ms-3 text-muted small" style="word-break: break-word; overflow-wrap: break-word; max-width: 100%;">${val(d.direccion_completa_cliente)}</p>
                                <p class="mb-1"><strong><i class="bi bi-receipt me-1"></i>Dir. Facturación:</strong></p>
                                <p class="ms-3 text-muted small" style="word-break: break-word; overflow-wrap: break-word; max-width: 100%;">${val(d.direccion_facturacion_completa_cliente)}</p>
                            </div>
                        </div>

                        <!-- ========== COLUMNA 2 ========== -->
                        <div class="col-md-4" style="overflow-x: auto; overflow-y: visible;">
                            
                            <!-- OBSERVACIONES -->
                            <h6 class="text-warning border-bottom pb-2 mb-3">
                                <i class="bi bi-chat-square-text me-2"></i>Observaciones
                            </h6>
                            <div class="mb-3">
                                <p class="mb-1"><strong><i class="bi bi-file-text me-1"></i>Obs. Cabecera:</strong></p>
                                <p class="ms-3 text-muted small" style="word-break: break-word; overflow-wrap: break-word; max-width: 100%;">${val(d.observaciones_cabecera_presupuesto)}</p>
                                
                                <p class="mb-1"><strong><i class="bi bi-translate me-1"></i>Obs. Cabecera (Inglés):</strong></p>
                                <p class="ms-3 text-muted small" style="word-break: break-word; overflow-wrap: break-word; max-width: 100%;">${val(d.observaciones_cabecera_ingles_presupuesto)}</p>
                                
                                <p class="mb-1"><strong><i class="bi bi-file-text me-1"></i>Obs. Pie:</strong></p>
                                <p class="ms-3 text-muted small" style="word-break: break-word; overflow-wrap: break-word; max-width: 100%;">${val(d.observaciones_pie_presupuesto)}</p>
                                
                                <p class="mb-1"><strong><i class="bi bi-translate me-1"></i>Obs. Pie (Inglés):</strong></p>
                                <p class="ms-3 text-muted small" style="word-break: break-word; overflow-wrap: break-word; max-width: 100%;">${val(d.observaciones_pie_ingles_presupuesto)}</p>
                                
                                <p class="mb-1"><strong><i class="bi bi-lock me-1"></i>Obs. Internas:</strong></p>
                                <p class="ms-3 text-muted small" style="word-break: break-word; overflow-wrap: break-word; max-width: 100%;">${val(d.observaciones_internas_presupuesto)}</p>

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
                        <div class="col-md-4" style="overflow-x: auto; overflow-y: visible;">
                            
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

                            <!-- DATOS DE PESO -->
                            <h6 class="text-secondary border-bottom pb-2 mb-3">
                                <i class="bi bi-box-seam me-2"></i>Peso del Presupuesto
                            </h6>
                            <div class="mb-3">
                                ${d.peso_total_kg !== null && d.peso_total_kg !== undefined ? `
                                    <p class="mb-1">
                                        <strong><i class="bi bi-box me-1"></i>Peso Total:</strong> 
                                        <span class="badge bg-info text-dark fs-6">${parseFloat(d.peso_total_kg).toFixed(3)} kg</span>
                                    </p>
                                    <p class="mb-1">
                                        <strong><i class="bi bi-list-ol me-1"></i>Líneas con peso:</strong> 
                                        ${d.lineas_con_peso || 0} / ${d.total_lineas || 0}
                                        ${d.porcentaje_completitud_peso !== null ? `
                                            <span class="badge ${parseFloat(d.porcentaje_completitud_peso) >= 80 ? 'bg-success' : parseFloat(d.porcentaje_completitud_peso) >= 50 ? 'bg-warning text-dark' : 'bg-danger'} ms-1">
                                                ${parseFloat(d.porcentaje_completitud_peso).toFixed(1)}%
                                            </span>
                                        ` : ''}
                                    </p>
                                    ${d.peso_articulos_normales_kg > 0 || d.peso_kits_kg > 0 ? `
                                        <p class="mb-1 ms-3 small text-muted">
                                            <i class="bi bi-arrow-return-right me-1"></i>Artículos: ${parseFloat(d.peso_articulos_normales_kg || 0).toFixed(3)} kg<br>
                                            <i class="bi bi-arrow-return-right me-1"></i>KITs: ${parseFloat(d.peso_kits_kg || 0).toFixed(3)} kg
                                        </p>
                                    ` : ''}
                                ` : `
                                    <p class="mb-1 text-muted">
                                        <i class="bi bi-exclamation-triangle me-1"></i>Sin datos de peso disponibles
                                    </p>
                                `}
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

    // Gestionar líneas del presupuesto (versión actual)
    $(document).on('click', '.gestionarLineas', function () {
        var id_version_presupuesto = $(this).data('id_version_presupuesto');
        var numero_version = $(this).data('numero_version') || 1;
        
        console.log('Redirigiendo a líneas de presupuesto:', {
            id_version: id_version_presupuesto,
            numero_version: numero_version
        });
        
        window.location.href = '../lineasPresupuesto/index.php?id_version_presupuesto=' + id_version_presupuesto;
    });

    // ============================================
    // IMPRIMIR PRESUPUESTO
    // ============================================
    
    // Abrir modal de impresión
    $(document).on('click', '.imprimirPresupuesto', function () {
        var id_presupuesto    = $(this).data('id_presupuesto');
        var id_empresa        = $(this).data('id_empresa');
        var codigo_estado     = $(this).data('codigo_estado');
        var numero_presupuesto = $(this).data('numero_presupuesto');
        var nombre_cliente    = $(this).data('nombre_cliente');

        console.log('Abriendo modal de impresión — presupuesto:', id_presupuesto, '| empresa:', id_empresa, '| estado:', codigo_estado);

        // Guardar datos en campos ocultos del formulario
        $('#impresion_id_presupuesto').val(id_presupuesto);
        $('#impresion_id_empresa').val(id_empresa);
        $('#impresion_codigo_estado').val(codigo_estado);

        // Actualizar banda informativa del modal
        $('#impresion_info_presupuesto').text(numero_presupuesto + '  —  ' + nombre_cliente);

        // Resetear opciones del modal a valores por defecto
        $('#tipo_cliente').prop('checked', true);
        $('#idioma_espanol').prop('checked', true);
        
        // Ocultar selector de versión y mostrar el modal de inmediato
        $('#divSelectorVersion').hide();
        $('#alertaVersionActual').show();
        $('#impresion_numero_version').empty();
        
        // Mostrar el modal
        $('#modalImpresionPresupuesto').modal('show');
        
        // Cargar versiones disponibles (sólo si hay más de una se mostrará el selector)
        $.post('../../controller/presupuesto.php?op=get_versiones_impresion', { id_presupuesto: id_presupuesto })
            .done(function(response) {
                if (response.success && response.versiones && response.versiones.length > 1) {
                    var select = $('#impresion_numero_version');
                    $.each(response.versiones, function(i, v) {
                        var etiqueta = 'Versión ' + v.numero_version + ' — ' + v.estado;
                        if (v.es_actual) { etiqueta += ' (actual)'; }
                        select.append($('<option>', { value: v.numero_version, text: etiqueta, selected: v.es_actual }));
                    });
                    $('#divSelectorVersion').show();
                    $('#alertaVersionActual').hide();
                }
            });
    });
    
    // Procesar impresión cuando se hace clic en el botón "Imprimir" del modal
    $(document).on('click', '#btnImprimirPresupuesto', function () {
        var id_presupuesto = $('#impresion_id_presupuesto').val();
        var id_empresa = $('#impresion_id_empresa').val();
        var tipo = $('input[name="tipo_presupuesto"]:checked').val();
        var idioma = $('input[name="idioma"]:checked').val();

        console.log('Procesando impresión:', {
            id_presupuesto: id_presupuesto,
            id_empresa: id_empresa,
            tipo: tipo,
            idioma: idioma
        });
        
        // Validar que se haya seleccionado un presupuesto
        if (!id_presupuesto) {
            Swal.fire('Error', 'No se ha seleccionado ningún presupuesto', 'error');
            return;
        }
        
        // Determinar la operación según las selecciones
        // Por ahora solo tenemos cli_esp (cliente en español)
        var operacion = 'cli_esp';
        
        if (tipo === 'intermediario') {
            generarImpresion('impresionpresupuestohotel_m2_pdf_es.php');
            return;
        }
        
        if (idioma === 'ingles') {
            Swal.fire('Próximamente', 'El presupuesto en inglés estará disponible pronto', 'info');
            return;
        }
        
        // Función para generar la impresión
        function generarImpresion(controller) {
            // Crear formulario temporal para enviar por POST y abrir en nueva ventana
            var form = $('<form>', {
                'method': 'POST',
                'action': '../../controller/' + controller + '?op=' + operacion,
                'target': '_blank'
            });
            
            // Añadir campo oculto con el ID del presupuesto
            form.append($('<input>', {
                'type': 'hidden',
                'name': 'id_presupuesto',
                'value': id_presupuesto
            }));
            
            // Añadir versión seleccionada si el selector está visible
            if ($('#divSelectorVersion').is(':visible')) {
                form.append($('<input>', {
                    'type': 'hidden',
                    'name': 'numero_version',
                    'value': $('#impresion_numero_version').val()
                }));
            }
            
            // Añadir el formulario al body, enviarlo y eliminarlo
            $('body').append(form);
            form.submit();
            form.remove();
            
            // Cerrar el modal
            $('#modalImpresionPresupuesto').modal('hide');
            
            // Notificar al usuario
            Swal.fire({
                icon: 'success',
                title: 'Generando impresión',
                text: 'Se abrirá el presupuesto en una nueva ventana',
                timer: 2000,
                showConfirmButton: false
            });
        }
        
        // Siempre generar en PDF
        generarImpresion('impresionpresupuesto_m2_pdf_es.php');
    });

    // Event handler para el botón "Albarán de Carga"
    $(document).on('click', '#btnAlbaranCarga', function() {
        console.log('Generando Albarán de Carga');

        // Obtener datos del presupuesto (mismos campos que impresión normal)
        var id_presupuesto = $('#impresion_id_presupuesto').val();
        var codigo_estado  = $('#impresion_codigo_estado').val();

        console.log('ID presupuesto:', id_presupuesto, '| estado:', codigo_estado);

        // Validar que el presupuesto tenga una versión aprobada
        if (codigo_estado !== 'APROB') {
            Swal.fire({
                icon: 'warning',
                title: 'Versión no aprobada',
                html: 'El <strong>albarán de carga</strong> solo puede generarse para presupuestos con una versión <strong>aprobada</strong>.<br><br>Aprueba una versión antes de generar este documento.',
                confirmButtonText: 'Entendido',
                confirmButtonColor: '#1a73e8'
            });
            return;
        }
        
        if (!id_presupuesto) {
            Swal.fire('Error', 'No se ha seleccionado ningún presupuesto', 'error');
            return;
        }
        
        // Crear formulario dinámico para enviar POST
        var form = $('<form>', {
            'method': 'POST',
            'action': '../../controller/impresionpartetrabajo_m2_pdf_es.php?op=albaran_carga',
            'target': '_blank'
        });
        
        // Añadir campo hidden con el ID del presupuesto
        form.append($('<input>', {
            'type': 'hidden',
            'name': 'id_presupuesto',
            'value': id_presupuesto
        }));
        
        // Añadir el formulario al body, enviarlo y eliminarlo
        $('body').append(form);
        form.submit();
        form.remove();
        
        // Cerrar el modal
        $('#modalImpresionPresupuesto').modal('hide');
        
        // Notificar al usuario
        Swal.fire({
            icon: 'success',
            title: 'Generando Albarán de Carga',
            text: 'Se abrirá el documento en una nueva ventana',
            timer: 2000,
            showConfirmButton: false
        });
    });

    // ============================================
    // COPIAR PRESUPUESTO
    // ============================================
    $(document).on('click', '.copiarPresupuesto', function (e) {
        e.preventDefault();
        var id_presupuesto    = $(this).data('id_presupuesto');
        var numero_presupuesto = $(this).data('numero_presupuesto');

        Swal.fire({
            title: '¿Copiar presupuesto?',
            html: 'Se creará un nuevo presupuesto duplicado a partir de <strong>' + numero_presupuesto + '</strong> con todas sus líneas.',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#f0ad4e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<i class="fas fa-copy me-1"></i> Sí, copiar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '../../controller/presupuesto.php?op=copiar',
                    type: 'POST',
                    data: { id_presupuesto: id_presupuesto },
                    dataType: 'json',
                    success: function (response) {
                        if (response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Presupuesto copiado',
                                html: 'Se ha creado el presupuesto <strong>' + (response.numero_nuevo || '') + '</strong>',
                                confirmButtonText: 'Aceptar'
                            }).then(function () { table_e.ajax.reload(); });
                        } else {
                            Swal.fire('Error', response.message || 'No se pudo copiar el presupuesto.', 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
                    }
                });
            }
        });
    });

    // ============================================
    // CAMBIAR ESTADO DEL PRESUPUESTO
    // ============================================
    $(document).on('click', '.cambiarEstadoPpto', function (e) {
        e.preventDefault();
        var id_presupuesto  = $(this).data('id_presupuesto');
        var id_estado_actual = $(this).data('id_estado_actual');

        // Cargar estados disponibles
        $.ajax({
            url: '../../controller/presupuesto.php?op=get_estados',
            type: 'GET',
            dataType: 'json',
            success: function (response) {
                if (!response.success || !response.data || !response.data.length) {
                    Swal.fire('Error', 'No se pudieron cargar los estados.', 'error');
                    return;
                }

                var optionsHtml = '<select id="swal_select_estado" class="swal2-input">';
                $.each(response.data, function (i, estado) {
                    var selected = (estado.id_estado_ppto == id_estado_actual) ? ' selected' : '';
                    optionsHtml += '<option value="' + estado.id_estado_ppto + '"' + selected + '>' + estado.nombre_estado_ppto + '</option>';
                });
                optionsHtml += '</select>';

                Swal.fire({
                    title: 'Cambiar estado del presupuesto',
                    html: optionsHtml,
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fas fa-exchange-alt me-1"></i> Cambiar estado',
                    cancelButtonText: 'Cancelar',
                    preConfirm: function () {
                        return document.getElementById('swal_select_estado').value;
                    }
                }).then(function (result) {
                    if (result.isConfirmed && result.value) {
                        $.ajax({
                            url: '../../controller/presupuesto.php?op=cambiar_estado',
                            type: 'POST',
                            data: { id_presupuesto: id_presupuesto, id_estado_ppto: result.value },
                            dataType: 'json',
                            success: function (resp) {
                                if (resp.success) {
                                    Swal.fire({ icon: 'success', title: 'Estado actualizado', timer: 1500, showConfirmButton: false });
                                    table_e.ajax.reload();
                                } else {
                                    Swal.fire('Error', resp.message || 'No se pudo cambiar el estado.', 'error');
                                }
                            },
                            error: function () {
                                Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
                            }
                        });
                    }
                });
            },
            error: function () {
                Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
            }
        });
    });

    // ============================================
    // PDF RÁPIDO (sin modal de opciones)
    // ============================================
    $(document).on('click', '.pdfRapido', function (e) {
        e.preventDefault();
        var id_presupuesto = $(this).data('id_presupuesto');

        var form = $('<form>', {
            'method': 'POST',
            'action': '../../controller/impresionpresupuesto_m2_pdf_es.php?op=cli_esp',
            'target': '_blank'
        });
        form.append($('<input>', { 'type': 'hidden', 'name': 'id_presupuesto', 'value': id_presupuesto }));
        $('body').append(form);
        form.submit();
        form.remove();

        Swal.fire({
            icon: 'success',
            title: 'Generando PDF',
            text: 'Se abrirá el presupuesto en una nueva ventana',
            timer: 1500,
            showConfirmButton: false
        });
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
                    dataType: 'json',
                    success: function (response) {
                        console.log('Respuesta desactivar:', response);
                        if (response.success) {
                            Swal.fire('Desactivado!', response.message || 'El presupuesto ha sido desactivado.', 'success');
                            table_e.ajax.reload();
                        } else {
                            Swal.fire('Error!', response.message || 'No se pudo desactivar el presupuesto.', 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error en desactivar:', error);
                        console.error('Response:', xhr.responseText);
                        Swal.fire('Error!', 'No se pudo desactivar el presupuesto. Error: ' + error, 'error');
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
                    dataType: 'json',
                    success: function (response) {
                        console.log('Respuesta activar:', response);
                        if (response.success) {
                            Swal.fire('Activado!', response.message || 'El presupuesto ha sido activado.', 'success');
                            table_e.ajax.reload();
                        } else {
                            Swal.fire('Error!', response.message || 'No se pudo activar el presupuesto.', 'error');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error en activar:', error);
                        console.error('Response:', xhr.responseText);
                        Swal.fire('Error!', 'No se pudo activar el presupuesto. Error: ' + error, 'error');
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

/* ================================================================
   SISTEMA DE VERSIONES - Gestión de versiones de presupuesto
   ================================================================ */

// Variables globales del módulo de versiones
let _tablaVersiones = null;
let _idPresupuestoVersiones = null;
let _versionesCache = [];

// Colores de badge por estado de versión
const VERSION_BADGES = {
    borrador:  'bg-secondary',
    enviado:   'bg-primary',
    aprobado:  'bg-success',
    rechazado: 'bg-danger',
    cancelado: 'bg-dark'
};

const VERSION_LABELS = {
    borrador:  'Borrador',
    enviado:   'Enviado',
    aprobado:  'Aprobado',
    rechazado: 'Rechazado',
    cancelado: 'Cancelado'
};

/**
 * Abre el modal de historial de versiones de un presupuesto.
 */
function abrirHistorialVersiones(id_presupuesto, numero_presupuesto, nombre_cliente, nombre_evento) {
    _idPresupuestoVersiones = id_presupuesto;

    // Rellenar cabecera informativa
    $('#hv_numeroPresupuesto').text(numero_presupuesto || '-');
    $('#hv_nombreCliente').text(nombre_cliente || '-');
    $('#hv_nombreEvento').text(nombre_evento || '-');

    // Mostrar modal y cargar versiones
    $('#modalHistorialVersiones').modal('show');
    cargarVersiones(id_presupuesto);
}

/**
 * Carga y renderiza las versiones en el modal.
 */
function cargarVersiones(id_presupuesto) {
    $('#tblHistorialVersiones tbody').html(
        '<tr><td colspan="7" class="text-center"><i class="fas fa-spinner fa-spin"></i> Cargando...</td></tr>'
    );

    $.post('../../controller/presupuesto.php?op=listar_versiones',
        { id_presupuesto: id_presupuesto },
        function (resp) {
            if (!resp || !resp.success) {
                $('#tblHistorialVersiones tbody').html(
                    '<tr><td colspan="7" class="text-danger text-center">' + (resp ? resp.message : 'Error de comunicación') + '</td></tr>'
                );
                return;
            }

            _versionesCache = resp.data || [];
            renderizarTablaVersiones(_versionesCache);
            _rellenarSelectoresComparacion(_versionesCache);
        }, 'json'
    ).fail(function () {
        $('#tblHistorialVersiones tbody').html(
            '<tr><td colspan="7" class="text-danger text-center">Error de comunicación con el servidor.</td></tr>'
        );
    });
}

/**
 * Renderiza las filas del historial de versiones.
 */
function renderizarTablaVersiones(versiones) {
    // Destruir DataTable anterior si existe
    if (_tablaVersiones) {
        _tablaVersiones.destroy();
        _tablaVersiones = null;
    }

    var $tbody = $('#tblHistorialVersiones tbody');
    $tbody.empty();

    if (!versiones.length) {
        $tbody.html('<tr><td colspan="7" class="text-center text-muted">Sin versiones registradas.</td></tr>');
        return;
    }

    versiones.forEach(function (v) {
        var badge = VERSION_BADGES[v.estado_version_presupuesto] || 'bg-secondary';
        var label = VERSION_LABELS[v.estado_version_presupuesto] || v.estado_version_presupuesto;
        var estado = v.estado_version_presupuesto;

        // Botones según estado
        var btns = `<button class="btn btn-xs btn-info btn-sm me-1" onclick="verVersion(${v.id_version_presupuesto})" title="Ver líneas">
                        <i class="fas fa-list"></i>
                    </button>`;
        if (estado === 'borrador') {
            btns += `<button class="btn btn-xs btn-primary btn-sm me-1" onclick="enviarVersion(${v.id_version_presupuesto})" title="Marcar como enviada">
                         <i class="fas fa-paper-plane"></i>
                     </button>`;
        }
        if (estado === 'enviado') {
            btns += `<button class="btn btn-xs btn-success btn-sm me-1" onclick="aprobarVersion(${v.id_version_presupuesto})" title="Aprobar">
                         <i class="fas fa-check"></i>
                     </button>
                     <button class="btn btn-xs btn-danger btn-sm me-1" onclick="rechazarVersion(${v.id_version_presupuesto})" title="Rechazar">
                         <i class="fas fa-times"></i>
                     </button>`;
        }

        var motivo = v.motivo_modificacion_version
            ? `<span title="${v.motivo_modificacion_version}" style="cursor:help">${v.motivo_modificacion_version.substring(0, 40)}${v.motivo_modificacion_version.length > 40 ? '…' : ''}</span>`
            : '<span class="text-muted">-</span>';

        var fCreacion = v.fecha_creacion_version ? formatoFechaEuropeo(v.fecha_creacion_version) : '-';
        var fEnvio   = v.fecha_envio_version      ? formatoFechaEuropeo(v.fecha_envio_version)     : '-';

        $tbody.append(`
            <tr>
                <td class="text-center"><strong>v${v.numero_version_presupuesto}</strong></td>
                <td class="text-center"><span class="badge ${badge}">${label}</span></td>
                <td class="text-center">${fCreacion}</td>
                <td class="text-center">${fEnvio}</td>
                <td>${motivo}</td>
                <td class="text-center">${v.total_lineas || 0}</td>
                <td class="text-center text-nowrap">${btns}</td>
            </tr>
        `);
    });

    // Inicializar como DataTable ligero
    _tablaVersiones = $('#tblHistorialVersiones').DataTable({
        paging: false,
        searching: false,
        info: false,
        ordering: false,
        scrollX: true,
        destroy: true
    });
}

/**
 * Rellena los selectores del comparador de versiones.
 */
function _rellenarSelectoresComparacion(versiones) {
    var opts = '<option value="">-- Selecciona --</option>';
    versiones.forEach(function (v) {
        var label = VERSION_LABELS[v.estado_version_presupuesto] || v.estado_version_presupuesto;
        opts += `<option value="${v.id_version_presupuesto}">v${v.numero_version_presupuesto} – ${label} (${formatoFechaEuropeo(v.fecha_creacion_version)})</option>`;
    });
    $('#cmp_selectVersionA, #cmp_selectVersionB').html(opts);
}

// ── Botón "Nueva versión" dentro del modal historial ──────────────
$(document).on('click', '#btnNuevaVersion', function () {
    if (!_idPresupuestoVersiones) return;
    $('#nv_idPresupuesto').val(_idPresupuestoVersiones);
    $('#nv_motivo').val('');
    $('#modalNuevaVersion').modal('show');
});

// ── Botón "Crear versión" dentro del modal nueva versión ──────────
$(document).on('click', '#btnConfirmarNuevaVersion', function () {
    var id_presupuesto = $('#nv_idPresupuesto').val();
    var motivo = $.trim($('#nv_motivo').val());

    if (!motivo) {
        Swal.fire('Campo requerido', 'Por favor introduce el motivo de la nueva versión.', 'warning');
        return;
    }

    Swal.fire({
        title: 'Creando versión…',
        text: 'Por favor espera',
        allowOutsideClick: false,
        didOpen: function () { Swal.showLoading(); }
    });

    $.post('../../controller/presupuesto.php?op=crear_version', {
        id_presupuesto: id_presupuesto,
        motivo: motivo,
        id_usuario: 1
    }, function (resp) {
        Swal.close();
        if (resp && resp.success) {
            $('#modalNuevaVersion').modal('hide');
            Swal.fire({
                icon: 'success',
                title: 'Versión creada',
                html: `Se creó la <strong>v${resp.numero_version}</strong> con ${resp.lineas_duplicadas} líneas copiadas.`,
                timer: 2500,
                showConfirmButton: false
            });
            // Recargar tabla de versiones y presupuestos
            cargarVersiones(id_presupuesto);
            $('#presupuestos_data').DataTable().ajax.reload(null, false);
        } else {
            Swal.fire('Error', (resp && resp.message) ? resp.message : 'No se pudo crear la versión.', 'error');
        }
    }, 'json').fail(function () {
        Swal.close();
        Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
    });
});

// ── Botón "Abrir comparador" dentro del modal historial ──────────
$(document).on('click', '#btnAbrirComparador', function () {
    $('#cmp_resultado').hide();
    $('#modalCompararVersiones').modal('show');
});

// ── Botón "Comparar" en el modal comparador ──────────────────────
$(document).on('click', '#btnEjecutarComparacion', function () {
    var idA = $('#cmp_selectVersionA').val();
    var idB = $('#cmp_selectVersionB').val();

    if (!idA || !idB) {
        Swal.fire('Selección incompleta', 'Selecciona ambas versiones para comparar.', 'warning');
        return;
    }
    if (idA === idB) {
        Swal.fire('Misma versión', 'Selecciona versiones distintas.', 'warning');
        return;
    }

    $.post('../../controller/presupuesto.php?op=comparar_versiones', {
        id_version_a: idA,
        id_version_b: idB
    }, function (resp) {
        if (!resp || !resp.success) {
            Swal.fire('Error', (resp && resp.message) ? resp.message : 'Error al comparar.', 'error');
            return;
        }

        var r = resp.data;

        // Resumen
        $('#cmp_resumen').html(
            `Comparación: <strong>${r.resumen.total_anadidas} añadidas</strong>, ` +
            `<strong>${r.resumen.total_eliminadas} eliminadas</strong>, ` +
            `<strong>${r.resumen.total_modificadas} modificadas</strong>.`
        );

        // Añadidas
        var $ta = $('#cmp_tbodyAnadidas').empty();
        if (r.anadidas && r.anadidas.length) {
            r.anadidas.forEach(function (l) {
                $ta.append(`<tr><td>${l.codigo_articulo || '-'}</td><td>${l.descripcion_linea_ppto || '-'}</td><td>${l.cantidad_linea_ppto}</td><td>${parseFloat(l.precio_unitario_linea_ppto || 0).toFixed(2)}</td><td>${parseFloat(l.total_linea_ppto || 0).toFixed(2)}</td></tr>`);
            });
            $('#cmp_seccionAnadidas').show();
        } else {
            $('#cmp_seccionAnadidas').hide();
        }

        // Eliminadas
        var $te = $('#cmp_tbodyEliminadas').empty();
        if (r.eliminadas && r.eliminadas.length) {
            r.eliminadas.forEach(function (l) {
                $te.append(`<tr><td>${l.codigo_articulo || '-'}</td><td>${l.descripcion_linea_ppto || '-'}</td><td>${l.cantidad_linea_ppto}</td><td>${parseFloat(l.precio_unitario_linea_ppto || 0).toFixed(2)}</td><td>${parseFloat(l.total_linea_ppto || 0).toFixed(2)}</td></tr>`);
            });
            $('#cmp_seccionEliminadas').show();
        } else {
            $('#cmp_seccionEliminadas').hide();
        }

        // Modificadas
        var $tm = $('#cmp_tbodyModificadas').empty();
        if (r.modificadas && r.modificadas.length) {
            r.modificadas.forEach(function (l) {
                $tm.append(`<tr>
                    <td>${l.descripcion_linea_ppto || '-'}</td>
                    <td>${l.cantidad_antigua}</td><td>${l.cantidad_linea_ppto}</td>
                    <td>${parseFloat(l.precio_antiguo || 0).toFixed(2)}</td><td>${parseFloat(l.precio_unitario_linea_ppto || 0).toFixed(2)}</td>
                    <td>${parseFloat(l.total_antiguo || 0).toFixed(2)}</td><td>${parseFloat(l.total_linea_ppto || 0).toFixed(2)}</td>
                </tr>`);
            });
            $('#cmp_seccionModificadas').show();
        } else {
            $('#cmp_seccionModificadas').hide();
        }

        $('#cmp_resultado').show();
    }, 'json').fail(function () {
        Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
    });
});

// ── Delegación del botón ".verVersiones" en el DataTable ─────────
$(document).on('click', '.verVersiones', function () {
    var id  = $(this).data('id_presupuesto');
    var num = $(this).data('numero_presupuesto');
    var cli = $(this).data('nombre_cliente');
    var evt = $(this).data('nombre_evento');
    abrirHistorialVersiones(id, num, cli, evt);
});

// ── Funciones de acción sobre versiones individuales ─────────────

function verVersion(id_version) {
    window.open('../lineasPresupuesto/index.php?id_version_presupuesto=' + id_version, '_blank');
}

function enviarVersion(id_version) {
    Swal.fire({
        title: '¿Marcar como enviada?',
        text: 'Se registrará la fecha de envío al cliente.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.post('../../controller/presupuesto.php?op=cambiar_estado_version', {
            id_version: id_version,
            nuevo_estado: 'enviado'
        }, function (resp) {
            if (resp && resp.success) {
                Swal.fire({ icon: 'success', title: 'Versión enviada', timer: 1800, showConfirmButton: false });
                cargarVersiones(_idPresupuestoVersiones);
                $('#presupuestos_data').DataTable().ajax.reload(null, false);
            } else {
                Swal.fire('Error', (resp && resp.message) ? resp.message : 'No se pudo cambiar el estado.', 'error');
            }
        }, 'json').fail(function () {
            Swal.fire('Error', 'Error de comunicación.', 'error');
        });
    });
}

function aprobarVersion(id_version) {
    Swal.fire({
        title: '¿Aprobar esta versión?',
        text: 'La versión quedará marcada como aprobada. No podrá modificarse.',
        icon: 'success',
        showCancelButton: true,
        confirmButtonText: 'Sí, aprobar',
        confirmButtonColor: '#28a745',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.post('../../controller/presupuesto.php?op=aprobar_version', {
            id_version: id_version
        }, function (resp) {
            if (resp && resp.success) {
                Swal.fire({ icon: 'success', title: 'Versión aprobada', timer: 1800, showConfirmButton: false });
                cargarVersiones(_idPresupuestoVersiones);
                $('#presupuestos_data').DataTable().ajax.reload(null, false);
            } else {
                Swal.fire('Error', (resp && resp.message) ? resp.message : 'No se pudo aprobar.', 'error');
            }
        }, 'json').fail(function () {
            Swal.fire('Error', 'Error de comunicación.', 'error');
        });
    });
}

function rechazarVersion(id_version) {
    Swal.fire({
        title: 'Rechazar versión',
        input: 'textarea',
        inputLabel: 'Motivo del rechazo',
        inputPlaceholder: 'Indica por qué se rechaza esta versión…',
        inputAttributes: { 'aria-label': 'Motivo del rechazo' },
        showCancelButton: true,
        confirmButtonText: 'Rechazar',
        confirmButtonColor: '#dc3545',
        cancelButtonText: 'Cancelar',
        didOpen: function () {
            // Bootstrap 5 registra su focus trap con addEventListener nativo,
            // no con jQuery, por lo tanto $.off('focusin.bs.modal') NO lo elimina.
            // Solución: listener en fase de captura (se ejecuta ANTES que Bootstrap)
            // que bloquea la propagación cuando el foco está dentro del Swal.
            var swalContainer = document.querySelector('.swal2-container');
            window._swalFocusTrapBlocker = function (e) {
                if (swalContainer && swalContainer.contains(e.target)) {
                    e.stopImmediatePropagation();
                }
            };
            document.addEventListener('focusin', window._swalFocusTrapBlocker, true);

            var textarea = document.querySelector('.swal2-textarea');
            if (textarea) {
                textarea.removeAttribute('readonly');
                setTimeout(function () { textarea.focus(); }, 80);
            }
        },
        didClose: function () {
            // Limpiar el bloqueador al cerrar el Swal
            if (window._swalFocusTrapBlocker) {
                document.removeEventListener('focusin', window._swalFocusTrapBlocker, true);
                delete window._swalFocusTrapBlocker;
            }
        },
        inputValidator: function (value) {
            if (!$.trim(value)) return 'El motivo es obligatorio.';
        }
    }).then(function (result) {
        if (!result.isConfirmed) return;
        $.post('../../controller/presupuesto.php?op=rechazar_version', {
            id_version: id_version,
            motivo_rechazo: result.value
        }, function (resp) {
            if (resp && resp.success) {
                $('#presupuestos_data').DataTable().ajax.reload(null, false);
                Swal.fire({
                    icon: 'info',
                    title: 'Versión rechazada',
                    text: '¿Deseas crear una nueva versión basada en ésta?',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, nueva versión',
                    cancelButtonText: 'No por ahora'
                }).then(function (res2) {
                    if (res2.isConfirmed) {
                        $('#nv_idPresupuesto').val(_idPresupuestoVersiones);
                        $('#nv_motivo').val('Revisión tras rechazo: ' + result.value);
                        cargarVersiones(_idPresupuestoVersiones);
                        $('#modalNuevaVersion').modal('show');
                    } else {
                        cargarVersiones(_idPresupuestoVersiones);
                    }
                });
            } else {
                Swal.fire('Error', (resp && resp.message) ? resp.message : 'No se pudo rechazar.', 'error');
            }
        }, 'json').fail(function () {
            Swal.fire('Error', 'Error de comunicación.', 'error');
        });
    });
}
/* ================================================================
   FIN SISTEMA DE VERSIONES
   ================================================================ */