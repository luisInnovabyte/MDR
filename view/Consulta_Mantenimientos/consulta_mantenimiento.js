$(document).ready(function () {
  // Agregar estilos CSS para mejorar la visualización
  if (!document.getElementById("elementos-styles")) {
    const style = document.createElement("style");
    style.id = "elementos-styles";
    style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
            .table-hover tbody tr:hover {
                background-color: #f8f9fa;
            }
            .group-row {
                background-color: #e3f2fd !important;
                font-weight: bold;
                font-size: 1.05em;
                cursor: pointer;
            }
            .group-row:hover {
                background-color: #bbdefb !important;
            }
            .group-row td {
                padding: 12px 8px !important;
                border-bottom: 2px solid #2196F3 !important;
            }
        `;
    document.head.appendChild(style);
  }

    /////////////////////////////////////
    // INICIO DE LA TABLA DE ELEMENTOS //
    //         DATATABLES             //
    ///////////////////////////////////
    var datatable_elementosConfig = {
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
            emptyTable: "No hay elementos registrados",
            info: "Mostrando _START_ a _END_ de _TOTAL_ elementos",
            infoEmpty: "Mostrando 0 a 0 de 0 elementos",
            infoFiltered: "(filtrado de _MAX_ elementos totales)",
            lengthMenu: "Mostrar _MENU_ elementos por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron elementos que coincidan con la búsqueda",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }, // Columna 0: Mostrar más
            { name: 'id_elemento', data: 'id_elemento', visible: false, className: "text-center" }, // Columna 1: ID
            { name: 'nombre_articulo', data: 'nombre_articulo', visible:false, className: "text-center align-middle" }, // Columna 2: ARTÍCULO
            { name: 'codigo_elemento', data: 'codigo_elemento', visible:false, className: "text-center align-middle" }, // Columna 3: CODIGO
            { name: 'descripcion_elemento', data: 'descripcion_elemento', className: "text-center align-middle" }, // Columna 4: DESCRIPCION
            { name: 'nombre_marca', data: 'nombre_marca', className: "text-center align-middle" }, // Columna 5: MARCA
            { name: 'modelo_elemento', data: 'modelo_elemento', className: "text-center align-middle" }, // Columna 6: MODELO
            { name: 'numero_serie_elemento', data: 'numero_serie_elemento', className: "text-center align-middle" }, // Columna 7: N° SERIE
    
            { 
                name: 'ubicacion', 
                data: 'nave_elemento',
                className: "text-center align-middle",
                render: function(data, type, row) {
                    if (type === 'display') {
                        let ubicacion = [];
                        if (row.nave_elemento) ubicacion.push(row.nave_elemento);
                        if (row.pasillo_columna_elemento) ubicacion.push(row.pasillo_columna_elemento);
                        if (row.altura_elemento) ubicacion.push(row.altura_elemento);
                        return ubicacion.length > 0 ? ubicacion.join(' | ') : '--';
                    }
                    return data;
                }
            }, // Columna 9: UBICACION

            // Columna 10: ESTADO MANTENIMIENTO
            {
                name: 'estado_mantenimiento_elemento',
                data: 'estado_mantenimiento_elemento',
                className: "text-center align-middle",
                render: function(data, type, row) {
                    if (!data) return '<span class="text-muted fst-italic">Sin programar</span>';

                    let colorClass =
                        data === 'Atrasado' ? 'bg-danger' :
                        data === 'Próximo' ? 'bg-warning text-dark' :
                        data === 'Al día' ? 'bg-success' :
                        'bg-secondary';

                    return `<span class="badge ${colorClass}">${data}</span>`;
                }
            },

             // Columna 11: FECHA MANTENIMIENTO
            { 
                name: 'proximo_mantenimiento_elemento',           // <- name para la fecha de MANTENIMIENTO
                data: 'proximo_mantenimiento_elemento',
                className: "text-center align-middle",
                render: function(data, type, row) {
                    return data ? formatoFechaEuropeo(data) : '<span class="text-muted fst-italic">Sin fecha mantenimiento</span>';
                }
            },
            //columna 12: ACTIVO
            { name: 'activo_elemento', data: 'activo_elemento', className: "text-center align-middle" } // Columna 10: ACTIVO
        ],
        columnDefs: [
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '3%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_elemento 
            { targets: "id_elemento:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: nombre_articulo
            { targets: "nombre_articulo:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: codigo_elemento
            { targets: "codigo_elemento:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: descripcion_elemento
            { targets: "descripcion_elemento:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            // Columna 5: nombre_marca
            { targets: "nombre_marca:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 6: modelo_elemento
            { targets: "modelo_elemento:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 7: numero_serie_elemento
            { targets: "numero_serie_elemento:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 8: descripcion_estado_elemento (con color)
            {
                targets: "descripcion_estado_elemento:name", width: '10%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const color = row.color_estado_elemento || '#6c757d';
                        return `<span class="badge" style="background-color: ${color};">${row.descripcion_estado_elemento || 'Sin estado'}</span>`;
                    }
                    return row.descripcion_estado_elemento;
                }
            },
            // Columna 9: ubicacion (3 campos concatenados)
            { targets: "ubicacion:name", width: '12%', searchable: true, orderable: true, className: "text-center" },

            // Columna 10: estado_mantenimiento_elemento (con color)
            {
                targets: "estado_mantenimiento_elemento:name",
                className: "text-center",
                render: function(data) {

                    if (!data) return '<span class="text-muted">Sin garantía</span>';

                    let color = "bg-secondary";
                    if (data === "Vigente") color = "bg-success";
                    if (data === "Por vencer") color = "bg-warning text-dark";
                    if (data === "Vencida") color = "bg-danger";

                    return `<span class="badge ${color}">${data}</span>`;
                }
            },
            // COLUMNA 11 Fecha de mantenimiento
            {
                targets: "proximo   _mantenimiento_elemento:name",
                className: "text-center",
                render: function(data) {
                    return data ? formatoFechaEuropeo(data) : "--";
                }
            },


            // Columna 12: activo_elemento
            {
                targets: "activo_elemento:name", width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_elemento == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_elemento;
                }
            }
        ],
        ajax: {
            url: '../../controller/elemento.php?op=listar',
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
            error: function(xhr, status, error) {
                console.error("Error al cargar datos:", error);
                console.error("Status:", status);
                console.error("Response:", xhr.responseText);
            }
        },
        deferRender: true,
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[10, 'asc']], // Ordenar por artículo por defecto
        rowGroup: {
            dataSrc: 'nombre_articulo',
            startRender: function (rows, group) {
                var articuloData = rows.data()[0];
                var codigoArticulo = articuloData.codigo_articulo || 'Sin código';
                var nombreArticulo = group || 'Sin artículo';
                var count = rows.count();
                
                return $('<tr/>')
                    .addClass('group-row bg-light')
                    .append('<td colspan="11" class="text-start fw-bold text-primary">' +
                        '<i class="bi bi-box-seam me-2"></i>' +
                        '<span class="badge bg-primary me-2">' + codigoArticulo + '</span>' +
                        nombreArticulo + 
                        ' <span class="badge bg-secondary ms-2">' + count + ' elemento(s)</span>' +
                        '</td>');
            }
        }
    };

    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    var $table = $('#elementos_data');
    var $tableConfig = datatable_elementosConfig;
    var $tableBody = $('#elementos_data tbody');
    var $columnFilterInputs = $('#elementos_data tfoot input, #elementos_data tfoot select');

    var table_e = $table.DataTable($tableConfig);

    // Cargar estados para el filtro
    cargarEstadosElemento();

    function format(d) {
        console.log(d);

        return `
            <div class="card border-info mb-3" style="overflow: visible;">
                <div class="card-header bg-info text-white">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-info-circle fs-3 me-2"></i>
                        <h5 class="card-title mb-0">Detalles del Elemento</h5>
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
                                            <i class="bi bi-box-seam me-2"></i>Artículo
                                        </th>
                                        <td class="pe-4">
                                            <strong>${d.nombre_articulo || '<span class="text-muted fst-italic">Sin artículo</span>'}</strong>
                                            <br><small class="text-muted">Código: ${d.codigo_articulo || '--'}</small>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-upc me-2"></i>Código Elemento
                                        </th>
                                        <td class="pe-4">
                                            <strong>${d.codigo_elemento || '<span class="text-muted fst-italic">Sin código</span>'}</strong>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-barcode me-2"></i>Código de Barras
                                        </th>
                                        <td class="pe-4">
                                            ${d.codigo_barras_elemento || '<span class="text-muted fst-italic">Sin código de barras</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-tag me-2"></i>Marca
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_marca || '<span class="text-muted fst-italic">Sin marca</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-box me-2"></i>Modelo
                                        </th>
                                        <td class="pe-4">
                                            ${d.modelo_elemento || '<span class="text-muted fst-italic">Sin modelo</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-123 me-2"></i>Número de Serie
                                        </th>
                                        <td class="pe-4">
                                            ${d.numero_serie_elemento || '<span class="text-muted fst-italic">Sin número de serie</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-palette me-2"></i>Estado
                                        </th>
                                        <td class="pe-4">
                                            <span class="badge" style="background-color: ${d.color_estado_elemento || '#6c757d'};">
                                                ${d.descripcion_estado_elemento || 'Sin estado'}
                                            </span>
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
                                            <i class="bi bi-building me-2"></i>Nave/Almacén
                                        </th>
                                        <td class="pe-4">
                                            ${d.nave_elemento || '<span class="text-muted fst-italic">--</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-signpost-split me-2"></i>Pasillo/Columna
                                        </th>
                                        <td class="pe-4">
                                            ${d.pasillo_columna_elemento || '<span class="text-muted fst-italic">--</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-arrow-bar-up me-2"></i>Altura/Nivel
                                        </th>
                                        <td class="pe-4">
                                            ${d.altura_elemento || '<span class="text-muted fst-italic">--</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-folder-fill me-2"></i>Familia
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_familia || '<span class="text-muted fst-italic">Sin familia</span>'}
                                            ${d.codigo_familia ? '<br><small class="text-muted">Código: ' + d.codigo_familia + '</small>' : ''}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar-plus me-2"></i>Fecha de Alta
                                        </th>
                                        <td class="pe-4">
                                            ${d.fecha_alta_elemento ? formatoFechaEuropeo(d.fecha_alta_elemento) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar-check me-2"></i>Garantía hasta
                                        </th>
                                        <td class="pe-4">
                                            ${d.fecha_fin_garantia_elemento ? formatoFechaEuropeo(d.fecha_fin_garantia_elemento) : '<span class="text-muted fst-italic">Sin garantía</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-tools me-2"></i>Próximo Mantenimiento
                                        </th>
                                        <td class="pe-4">
                                            ${d.proximo_mantenimiento_elemento ? formatoFechaEuropeo(d.proximo_mantenimiento_elemento) : '<span class="text-muted fst-italic">Sin programar</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-shield-exclamation me-2"></i>Estado Garantía
                                        </th>
                                        <td class="pe-4">
                                            ${d.estado_garantia_elemento ? 
                                                `<span class="badge ${
                                                    d.estado_garantia_elemento === 'Vigente' ? 'bg-success' :
                                                    d.estado_garantia_elemento === 'Por vencer' ? 'bg-warning text-dark' :
                                                    d.estado_garantia_elemento === 'Vencida' ? 'bg-danger' :
                                                    'bg-secondary'
                                                }">${d.estado_garantia_elemento}</span>` 
                                                : '<span class="text-muted fst-italic">Sin garantía</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-wrench-adjustable me-2"></i>Estado Mantenimiento
                                        </th>
                                        <td class="pe-4">
                                            ${d.estado_mantenimiento_elemento ? 
                                                `<span class="badge ${
                                                    d.estado_mantenimiento_elemento === 'Al día' ? 'bg-success' :
                                                    d.estado_mantenimiento_elemento === 'Próximo' ? 'bg-warning text-dark' :
                                                    d.estado_mantenimiento_elemento === 'Atrasado' ? 'bg-danger' :
                                                    'bg-secondary'
                                                }">${d.estado_mantenimiento_elemento}</span>` 
                                                : '<span class="text-muted fst-italic">Sin programar</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-clock-history me-2"></i>Años en Servicio
                                        </th>
                                        <td class="pe-4">
                                            ${d.anios_en_servicio_elemento !== null && d.anios_en_servicio_elemento !== undefined ? 
                                                `<strong>${parseFloat(d.anios_en_servicio_elemento).toFixed(2)} años</strong>` 
                                                : '<span class="text-muted fst-italic">Sin calcular</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    
                    <!-- Observaciones -->
                    ${d.observaciones_elemento ? `
                    <div class="row mt-2">
                        <div class="col-12">
                            <div class="alert alert-info mx-3 mb-3">
                                <strong><i class="bi bi-chat-left-text me-2"></i>Observaciones:</strong>
                                <p class="mb-0 mt-2">${d.observaciones_elemento}</p>
                            </div>
                        </div>
                    </div>
                    ` : ''}
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Vista de consulta - Solo lectura</small>
                </div>
            </div>
        `;
    }

    // NO TOCAR, se configura en la parte superior --> funcion format(d)
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

    ////////////////////////////////////////////
    //   INICIO ZONA FUNCIONES DE APOYO      //
    //////////////////////////////////////////

    // Función para cargar estados en el filtro
    function cargarEstadosElemento() {
        $.ajax({
            url: '../../controller/estado_elemento.php?op=listar',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                console.log('🔄 Cargando estados de elementos:', response);
                
                const $select = $('#elementos_data tfoot select[title="Filtrar por estado"]');
                $select.empty();
                $select.append('<option value="">Todos los estados</option>');
                
                const data = response.data || response.aaData || response;
                
                if (Array.isArray(data)) {
                    data.forEach(function(estado) {
                        if (estado.activo_estado_elemento == 1 || estado.activo_estado_elemento === '1' || estado.activo_estado_elemento === true) {
                            $select.append(`<option value="${estado.descripcion_estado_elemento}">${estado.descripcion_estado_elemento}</option>`);
                        }
                    });
                    console.log('✅ Estados cargados correctamente');
                } else {
                    console.warn('⚠️ Formato de datos inesperado:', data);
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al cargar estados:', error);
            }
        });
    }

    /////////////////////////////////////
    //  INICIO ZONA FILTROS PIES y SEARCH     //
    //    NO ES NECESARIO TOCAR              //
    //     FUNCIONES NO TOCAR               //
    ///////////////////////////////////////////

    // Filtro de cada columna en el pie de la tabla
    $columnFilterInputs.on("keyup change", function () {
        var columnIndex = table_e.column($(this).closest("th")).index();
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
            $("#filter-alert").show();
        } else {
            $("#filter-alert").hide();
        }
    }

    table_e.on("search.dt", function () {
        updateFilterMessage();
    });

    // Botón para limpiar los filtros
    $("#clear-filter").on("click", function () {
        table_e.destroy();

        $columnFilterInputs.each(function () {
            $(this).val("");
        });

        table_e = $table.DataTable($tableConfig);

        // Recargar estados
        cargarEstadosElemento();

        $("#filter-alert").hide();
    });

    // Filtrar por estado de mantenimiento
$('#filtro-estado-mantenimiento').on('change', function() {
    table_e.column(9).search(this.value).draw();
});

    ////////////////////////////////////////////
    //  FIN ZONA FILTROS PIES y SEARCH     //
    ///////////////////////////////////////////

}); // de document.ready

// Función para formatear fechas a formato europeo
function formatoFechaEuropeo(fecha) {
    if (!fecha) return '';
    
    const date = new Date(fecha);
    const dia = String(date.getDate()).padStart(2, '0');
    const mes = String(date.getMonth() + 1).padStart(2, '0');
    const anio = date.getFullYear();
    
    return `${dia}/${mes}/${anio}`;
}
