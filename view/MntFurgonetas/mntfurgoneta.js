$(document).ready(function () {
    // ==========================================
    // 0. FUNCIÓN DE UTILIDAD PARA FECHAS
    // ==========================================
    /**
     * Convierte fecha de formato YYYY-MM-DD a DD/MM/YYYY
     * @param {string} fecha - Fecha en formato ISO (YYYY-MM-DD)
     * @returns {string} Fecha en formato europeo (DD/MM/YYYY)
     */
    function formatearFechaEuropea(fecha) {
        if (!fecha || fecha === '0000-00-00') {
            return null;
        }
        const partes = fecha.split('-');
        if (partes.length === 3) {
            return partes[2] + '/' + partes[1] + '/' + partes[0];
        }
        return fecha;
    }

    // ==========================================
    // 1. ESTILOS CSS DINÁMICOS
    // ==========================================
    if (!document.getElementById("furgoneta-styles")) {
        const style = document.createElement("style");
        style.id = "furgoneta-styles";
        style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
        `;
        document.head.appendChild(style);
    }

    // ==========================================
    // 2. CONFIGURACIÓN DE DATATABLES
    // ==========================================
    var datatable_furgonetasConfig = {
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
                    previousNext: true,
                },
            },
        },

        language: {
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>',
            },
            processing: "Procesando...",
            search: "Buscar:",
            lengthMenu: "Mostrar _MENU_ registros",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
            infoEmpty: "Mostrando 0 a 0 de 0 registros",
            infoFiltered: "(filtrado de _MAX_ registros totales)",
            loadingRecords: "Cargando...",
            zeroRecords: "No se encontraron registros coincidentes",
            emptyTable: "No hay datos disponibles en la tabla",
        },

        columns: [
            // Columna 0: Control (Botón +)
            {
                name: "control",
                data: null,
                defaultContent: "",
                className: "details-control sorting_1 text-center",
            },
            // Columna 1: ID (Oculta)
            {
                name: "id_furgoneta",
                data: "id_furgoneta",
                visible: false,
                className: "text-center",
            },
            // Columna 2: Matrícula
            {
                name: "matricula_furgoneta",
                data: "matricula_furgoneta",
                className: "text-center align-middle",
            },
            // Columna 3: Modelo
            {
                name: "modelo_furgoneta",
                data: "modelo_furgoneta",
                className: "text-center align-middle",
            },
            // Columna 4: Año
            {
                name: "anio_furgoneta",
                data: "anio_furgoneta",
                className: "text-center align-middle",
            },
            // Columna 5: Próxima ITV
            {
                name: "fecha_proxima_itv_furgoneta",
                data: "fecha_proxima_itv_furgoneta",
                className: "text-center align-middle",
            },
            // Columna 6: Seguro Vence
            {
                name: "fecha_vencimiento_seguro_furgoneta",
                data: "fecha_vencimiento_seguro_furgoneta",
                className: "text-center align-middle",
            },
            // Columna 7: Estado
            {
                name: "estado_furgoneta",
                data: "estado_furgoneta",
                className: "text-center align-middle",
            },
            // Columna 8: Activo
            {
                name: "activo_furgoneta",
                data: "activo_furgoneta",
                className: "text-center align-middle",
            },
            // Columna 9: Activar/Desactivar
            {
                name: "activar",
                data: null,
                className: "text-center align-middle",
            },
            // Columna 10: Editar
            {
                name: "editar",
                data: null,
                defaultContent: "",
                className: "text-center align-middle",
            },
            // Columna 11: Mantenimiento
            {
                name: "mantenimiento",
                data: null,
                defaultContent: "",
                className: "text-center align-middle",
            },
            // Columna 12: Kilometraje
            {
                name: "kilometraje",
                data: null,
                defaultContent: "",
                className: "text-center align-middle",
            },
        ],

        columnDefs: [
            // Columna 0: Control
            {
                targets: "control:name",
                width: "3%",
                searchable: false,
                orderable: false,
                className: "text-center",
            },
            // Columna 2: Matrícula
            {
                targets: "matricula_furgoneta:name",
                width: "10%",
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return '<span class="badge bg-primary fs-6">' + data + '</span>';
                    }
                    return data;
                },
            },
            // Columna 3: Modelo
            {
                targets: "modelo_furgoneta:name",
                width: "15%",
                searchable: true,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const marca = row.marca_furgoneta || "";
                        const modelo = data || "";
                        if (marca) {
                            return '<strong>' + marca + '</strong> ' + modelo;
                        }
                        return modelo || '<span class="text-muted fst-italic">Sin modelo</span>';
                    }
                    return data;
                },
            },
            // Columna 4: Año
            {
                targets: "anio_furgoneta:name",
                width: "8%",
                orderable: true,
                searchable: true,
                className: "text-center",
            },
            // Columna 5: Próxima ITV
            {
                targets: "fecha_proxima_itv_furgoneta:name",
                width: "12%",
                orderable: true,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (!data) {
                            return '<span class="text-muted fst-italic">Sin fecha</span>';
                        }
                        // Convertir fecha y verificar si está próxima a vencer
                        const fecha = new Date(data);
                        const hoy = new Date();
                        const diasDiferencia = Math.ceil((fecha - hoy) / (1000 * 60 * 60 * 24));
                        
                        let badgeClass = "bg-success";
                        if (diasDiferencia < 0) {
                            badgeClass = "bg-danger";
                        } else if (diasDiferencia <= 30) {
                            badgeClass = "bg-warning";
                        }
                        
                        // Convertir a formato europeo
                        const fechaEuropea = formatearFechaEuropea(data);
                        return '<span class="badge ' + badgeClass + ' fs-6">' + fechaEuropea + '</span>';
                    }
                    return data;
                },
            },
            // Columna 6: Seguro Vence
            {
                targets: "fecha_vencimiento_seguro_furgoneta:name",
                width: "12%",
                orderable: true,
                searchable: false,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (!data) {
                            return '<span class="text-muted fst-italic">Sin fecha</span>';
                        }
                        // Convertir fecha y verificar si está próxima a vencer
                        const fecha = new Date(data);
                        const hoy = new Date();
                        const diasDiferencia = Math.ceil((fecha - hoy) / (1000 * 60 * 60 * 24));
                        
                        let badgeClass = "bg-success";
                        if (diasDiferencia < 0) {
                            badgeClass = "bg-danger";
                        } else if (diasDiferencia <= 30) {
                            badgeClass = "bg-warning";
                        }
                        
                        // Convertir a formato europeo
                        const fechaEuropea = formatearFechaEuropea(data);
                        return '<span class="badge ' + badgeClass + ' fs-6">' + fechaEuropea + '</span>';
                    }
                    return data;
                },
            },
            // Columna 7: Estado
            {
                targets: "estado_furgoneta:name",
                width: "10%",
                orderable: true,
                searchable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        let badgeClass = "bg-secondary";
                        let icon = "bi-question-circle";
                        
                        if (data === "operativa") {
                            badgeClass = "bg-success";
                            icon = "bi-check-circle";
                        } else if (data === "taller") {
                            badgeClass = "bg-warning";
                            icon = "bi-tools";
                        } else if (data === "baja") {
                            badgeClass = "bg-danger";
                            icon = "bi-x-circle";
                        }
                        
                        return '<span class="badge ' + badgeClass + ' fs-6"><i class="bi ' + icon + ' me-1"></i>' + 
                               (data ? data.charAt(0).toUpperCase() + data.slice(1) : 'Desconocido') + '</span>';
                    }
                    return data;
                },
            },
            // Columna 8: Activo
            {
                targets: "activo_furgoneta:name",
                width: "8%",
                orderable: true,
                searchable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_furgoneta == 1
                            ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
                            : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_furgoneta;
                },
            },
            // Columna 9: Activar/Desactivar
            {
                targets: "activar:name",
                width: "8%",
                searchable: false,
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    if (row.activo_furgoneta == 1) {
                        return `<button type="button" 
                                       class="btn btn-danger btn-sm desacFurgoneta" 
                                       data-bs-toggle="tooltip-primary" 
                                       data-placement="top" 
                                       title="Desactivar" 
                                       data-id_furgoneta="${row.id_furgoneta}"> 
                                    <i class="fa-solid fa-trash"></i>
                                </button>`;
                    } else {
                        return `<button class="btn btn-success btn-sm activarFurgoneta" 
                                       data-bs-toggle="tooltip-primary" 
                                       data-placement="top" 
                                       title="Activar" 
                                       data-id_furgoneta="${row.id_furgoneta}">
                                    <i class="bi bi-hand-thumbs-up-fill"></i>
                                </button>`;
                    }
                },
            },
            // Columna 10: Editar
            {
                targets: "editar:name",
                width: "8%",
                searchable: false,
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" 
                                   class="btn btn-info btn-sm editarFurgoneta" 
                                   data-toggle="tooltip-primary" 
                                   data-placement="top" 
                                   title="Editar"  
                                   data-id_furgoneta="${row.id_furgoneta}"> 
                                <i class="fa-solid fa-edit"></i>
                            </button>`;
                },
            },
            // Columna 11: Mantenimiento
            {
                targets: "mantenimiento:name",
                width: "10%",
                searchable: false,
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    return `<a href="../MntFurgonetas_mantenimiento/index.php?id_furgoneta=${row.id_furgoneta}" 
                              class="btn btn-primary btn-sm" 
                              data-bs-toggle="tooltip" 
                              data-placement="top" 
                              title="Ver Historial de Mantenimiento">
                                <i class="bi bi-wrench-adjustable-circle me-1"></i>Historial
                            </a>`;
                },
            },
            // Columna 12: Kilometraje
            {
                targets: "kilometraje:name",
                width: "10%",
                searchable: false,
                orderable: false,
                class: "text-center",
                render: function (data, type, row) {
                    return `<a href="../MntFurgonetas_registro_kilometraje/index.php?id_furgoneta=${row.id_furgoneta}" 
                              class="btn btn-success btn-sm" 
                              data-bs-toggle="tooltip" 
                              data-placement="top" 
                              title="Ver Registro de Kilometraje">
                                <i class="bi bi-speedometer2 me-1"></i>Kilometraje
                            </a>`;
                },
            },
        ],

        ajax: {
            url: "../../controller/furgoneta.php?op=listar",
            type: "POST",
            dataSrc: function (json) {
                console.log("JSON recibido:", json);
                return json.data || json;
            },
        },

        order: [[2, 'asc']], // Ordenar por matrícula
        responsive: true,
    };

    // ==========================================
    // 3. FUNCIÓN PARA CHILD ROWS (2 COLUMNAS)
    // ==========================================
    function format(d) {
        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-truck fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles de la Furgoneta</h5>
                    </div>
                </div>
                <div class="card-body p-3">
                    <div class="row">
                        <!-- COLUMNA IZQUIERDA -->
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-3" style="width: 45%;">
                                            <i class="bi bi-hash me-2"></i>ID Furgoneta
                                        </th>
                                        <td class="pe-3">
                                            ${d.id_furgoneta || '<span class="text-muted fst-italic">No disponible</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-card-text me-2"></i>Matrícula
                                        </th>
                                        <td class="pe-3">
                                            ${d.matricula_furgoneta || '<span class="text-muted fst-italic">No disponible</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-tag me-2"></i>Marca
                                        </th>
                                        <td class="pe-3">
                                            ${d.marca_furgoneta || '<span class="text-muted fst-italic">No especificada</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-truck me-2"></i>Modelo
                                        </th>
                                        <td class="pe-3">
                                            ${d.modelo_furgoneta || '<span class="text-muted fst-italic">No especificado</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-calendar3 me-2"></i>Año
                                        </th>
                                        <td class="pe-3">
                                            ${d.anio_furgoneta || '<span class="text-muted fst-italic">No disponible</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-shield-check me-2"></i>Número de Bastidor
                                        </th>
                                        <td class="pe-3">
                                            ${d.numero_bastidor_furgoneta || '<span class="text-muted fst-italic">No disponible</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-speedometer me-2"></i>Km entre Revisiones
                                        </th>
                                        <td class="pe-3">
                                            ${d.kilometros_entre_revisiones_furgoneta || '10000'} km
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-calendar-check me-2"></i>Próxima ITV
                                        </th>
                                        <td class="pe-3">
                                            ${d.fecha_proxima_itv_furgoneta ? formatearFechaEuropea(d.fecha_proxima_itv_furgoneta) : '<span class="text-muted fst-italic">No especificada</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-shield-fill-check me-2"></i>Vencimiento Seguro
                                        </th>
                                        <td class="pe-3">
                                            ${d.fecha_vencimiento_seguro_furgoneta ? formatearFechaEuropea(d.fecha_vencimiento_seguro_furgoneta) : '<span class="text-muted fst-italic">No especificada</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-info-circle me-2"></i>Estado
                                        </th>
                                        <td class="pe-3">
                                            ${d.estado_furgoneta ? d.estado_furgoneta.charAt(0).toUpperCase() + d.estado_furgoneta.slice(1) : 'Desconocido'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- COLUMNA DERECHA -->
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-3" style="width: 45%;">
                                            <i class="bi bi-building me-2"></i>Compañía Seguro
                                        </th>
                                        <td class="pe-3">
                                            ${d.compania_seguro_furgoneta || '<span class="text-muted fst-italic">No especificada</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-file-text me-2"></i>Número Póliza
                                        </th>
                                        <td class="pe-3">
                                            ${d.numero_poliza_seguro_furgoneta || '<span class="text-muted fst-italic">No especificado</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-box me-2"></i>Capacidad Carga (kg)
                                        </th>
                                        <td class="pe-3">
                                            ${d.capacidad_carga_kg_furgoneta || '<span class="text-muted fst-italic">No especificada</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-box me-2"></i>Capacidad Carga (m³)
                                        </th>
                                        <td class="pe-3">
                                            ${d.capacidad_carga_m3_furgoneta || '<span class="text-muted fst-italic">No especificada</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-fuel-pump me-2"></i>Tipo Combustible
                                        </th>
                                        <td class="pe-3">
                                            ${d.tipo_combustible_furgoneta || '<span class="text-muted fst-italic">No especificado</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-speedometer2 me-2"></i>Consumo Medio (L/100km)
                                        </th>
                                        <td class="pe-3">
                                            ${d.consumo_medio_furgoneta || '<span class="text-muted fst-italic">No especificado</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-tools me-2"></i>Taller Habitual
                                        </th>
                                        <td class="pe-3">
                                            ${d.taller_habitual_furgoneta || '<span class="text-muted fst-italic">No especificado</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-telephone me-2"></i>Teléfono Taller
                                        </th>
                                        <td class="pe-3">
                                            ${d.telefono_taller_furgoneta || '<span class="text-muted fst-italic">No especificado</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-3">
                                            <i class="bi bi-chat-left-text me-2"></i>Observaciones
                                        </th>
                                        <td class="pe-3">
                                            ${d.observaciones_furgoneta || '<span class="text-muted fst-italic">Sin observaciones</span>'}
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

    // ==========================================
    // 4. INICIALIZAR DATATABLE
    // ==========================================
    var $table = $("#furgonetas_data");
    var $tableConfig = datatable_furgonetasConfig;
    var $tableBody = $("#furgonetas_data tbody");
    var $columnFilterInputs = $("#furgonetas_data tfoot input, #furgonetas_data tfoot select");

    var table_e = $table.DataTable($tableConfig);

    // ==========================================
    // 5. EVENTO EXPANDIR CHILD ROWS
    // ==========================================
    $tableBody.on("click", "td.details-control", function () {
        var tr = $(this).closest("tr");
        var row = table_e.row(tr);

        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass("shown");
        } else {
            row.child(format(row.data())).show();
            tr.addClass("shown");
        }
    });

    // ==========================================
    // 6. EVENTOS DE BOTONES
    // ==========================================
    
    // Desactivar furgoneta
    $(document).on("click", ".desacFurgoneta", function (event) {
        event.preventDefault();
        let id = $(this).data("id_furgoneta");
        desacFurgoneta(id);
    });

    // Activar furgoneta
    $(document).on("click", ".activarFurgoneta", function (event) {
        event.preventDefault();
        let id = $(this).data("id_furgoneta");
        activarFurgoneta(id);
    });

    // Editar furgoneta
    $(document).on("click", ".editarFurgoneta", function (event) {
        event.preventDefault();
        let id = $(this).data("id_furgoneta");
        console.log("id furgoneta:", id);
        window.location.href = `formularioFurgoneta.php?modo=editar&id=${id}`;
    });

    // ==========================================
    // 7. SISTEMA DE FILTROS
    // ==========================================
    $columnFilterInputs.on("keyup change", function () {
        var columnIndex = table_e.column($(this).closest("th")).index();
        var searchValue = $(this).val();

        table_e.column(columnIndex).search(searchValue).draw();

        updateFilterMessage();
    });

    // Limpiar filtros
    $("#clear-filter").on("click", function () {
        table_e.destroy();

        $columnFilterInputs.each(function () {
            $(this).val("");
        });

        table_e = $table.DataTable($tableConfig);
        $("#filter-alert").hide();
    });

    // Actualizar mensaje de filtro
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
            $("#filter-alert").show();
        } else {
            $("#filter-alert").hide();
        }
    }

}); // Fin document.ready

// ==========================================
// 8. FUNCIONES GLOBALES
// ==========================================

// Función para recargar estadísticas
function recargarEstadisticas() {
    $.ajax({
        url: "../../controller/furgoneta.php?op=estadisticas",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.success && response.data) {
                $(".card.border-primary h2").text(response.data.total || 0);
                $(".card.border-success h2").text(response.data.operativas || 0);
                $(".card.border-warning h2").text(response.data.taller || 0);
                $(".card.border-danger h2").text(response.data.baja || 0);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al recargar estadísticas:", error);
        }
    });
}

// Función para desactivar furgoneta
function desacFurgoneta(id) {
    Swal.fire({
        title: "Desactivar",
        text: `¿Desea desactivar la furgoneta con ID ${id}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "No",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "../../controller/furgoneta.php?op=eliminar",
                { id_furgoneta: id },
                function (data) {
                    $("#furgonetas_data").DataTable().ajax.reload();
                    recargarEstadisticas();
                    
                    Swal.fire(
                        "Desactivado",
                        "La furgoneta ha sido desactivada",
                        "success"
                    );
                }
            );
        }
    });
}

// Función para activar furgoneta
function activarFurgoneta(id) {
    Swal.fire({
        title: "Activar",
        text: `¿Desea activar la furgoneta con ID ${id}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "No",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "../../controller/furgoneta.php?op=activar",
                { id_furgoneta: id },
                function (data) {
                    $("#furgonetas_data").DataTable().ajax.reload();
                    recargarEstadisticas();
                    
                    Swal.fire("Activado", "La furgoneta ha sido activada", "success");
                }
            );
        }
    });
}
