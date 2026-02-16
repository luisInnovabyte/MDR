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
        `;
    document.head.appendChild(style);
  }

    // Obtener el ID del artículo desde la URL
    const urlParams = new URLSearchParams(window.location.search);
    const idArticulo = urlParams.get('id_articulo');

    // Cargar información del artículo
    if (idArticulo) {
        cargarInfoArticulo(idArticulo);
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
            emptyTable: "No hay elementos registrados para este artículo",
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
            { name: 'codigo_elemento', data: 'codigo_elemento', className: "text-center" }, // Columna 2: CODIGO
            { name: 'descripcion_elemento', data: 'descripcion_elemento', className: "text-center" }, // Columna 3: DESCRIPCION
            { name: 'nombre_marca', data: 'nombre_marca', className: "text-center" }, // Columna 4: MARCA
            { name: 'modelo_elemento', data: 'modelo_elemento', className: "text-center" }, // Columna 5: MODELO
            { name: 'numero_serie_elemento', data: 'numero_serie_elemento', className: "text-center" }, // Columna 6: N° SERIE
            { name: 'descripcion_estado_elemento', data: 'descripcion_estado_elemento', className: "text-center" }, // Columna 7: ESTADO
            { 
                name: 'ubicacion', 
                data: 'nave_elemento',
                className: "text-center",
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
            }, // Columna 8: UBICACION
            { name: 'precio_compra_elemento', data: 'precio_compra_elemento', className: "text-center" }, // Columna 9: PRECIO COMPRA
            { name: 'activo_elemento', data: 'activo_elemento', className: "text-center" }, // Columna 10: ACTIVO
            { name: 'documentos', data: null, className: "text-center" }, // Columna 11: DOCUMENTOS
            { name: 'fotos', data: null, className: "text-center" }, // Columna 12: FOTOS
            { name: 'activar', data: null, className: "text-center" }, // Columna 13: ACTIVAR/DESACTIVAR
            { name: 'editar', data: null, defaultContent: '', className: "text-center" }  // Columna 14: EDITAR
        ],
        columnDefs: [
            // Columna 0: BOTÓN MÁS 
            { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center"},
            // Columna 1: id_elemento 
            { targets: "id_elemento:name", width: '5%', searchable: false, orderable: false, className: "text-center" },
            // Columna 2: codigo_elemento
            { targets: "codigo_elemento:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 3: descripcion_elemento
            { targets: "descripcion_elemento:name", width: '15%', searchable: true, orderable: true, className: "text-center" },
            // Columna 4: nombre_marca
            { targets: "nombre_marca:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 5: modelo_elemento
            { targets: "modelo_elemento:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 6: numero_serie_elemento
            { targets: "numero_serie_elemento:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 7: descripcion_estado_elemento (con color)
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
            // Columna 8: ubicacion (3 campos concatenados)
            { targets: "ubicacion:name", width: '10%', searchable: true, orderable: true, className: "text-center" },
            // Columna 9: precio_compra_elemento (condicional según tipo de propiedad)
            {
                targets: "precio_compra_elemento:name", width: '8%', orderable: true, searchable: false, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const esPropio = row.es_propio_elemento == 1;
                        if (esPropio) {
                            const precio = row.precio_compra_elemento ? parseFloat(row.precio_compra_elemento).toFixed(2) : '0.00';
                            return `${precio} €<br><small class="text-muted">Compra</small>`;
                        } else {
                            const precio = row.precio_dia_alquiler_elemento ? parseFloat(row.precio_dia_alquiler_elemento).toFixed(2) : '0.00';
                            return `${precio} €<br><small class="text-muted">Alquiler/día</small>`;
                        }
                    }
                    return row.precio_compra_elemento;
                }
            },
            // Columna 10: activo_elemento
            {
                targets: "activo_elemento:name", width: '5%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_elemento == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_elemento;
                }
            },
            // Columna 11: BOTON PARA VER DOCUMENTOS
            {
                targets: "documentos:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-warning btn-sm verDocumentos" data-bs-toggle="tooltip" data-placement="top" title="Ver documentos" 
                             data-id_elemento="${row.id_elemento}" data-codigo_elemento="${row.codigo_elemento}">
                             <i class="bi bi-file-earmark-text"></i>
                             </button>`;
                }
            },
            // Columna 12: BOTON PARA VER FOTOS
            {
                targets: "fotos:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-primary btn-sm verFotos" data-bs-toggle="tooltip" data-placement="top" title="Ver fotos" 
                             data-id_elemento="${row.id_elemento}" data-codigo_elemento="${row.codigo_elemento}">
                             <i class="bi bi-camera"></i>
                             </button>`;
                }
            },
            // Columna 13: BOTON PARA ACTIVAR/DESACTIVAR ESTADO
            {   
                targets: "activar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    if (row.activo_elemento == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacElemento" data-bs-toggle="tooltip-primary" data-placement="top" title="Desactivar" data-original-title="Tooltip on top" 
                             data-id_elemento="${row.id_elemento}"> 
                             <i class="fa-solid fa-trash"></i>
                             </button>`}
                    else {
                        return `<button class="btn btn-success btn-sm activarElemento" data-bs-toggle="tooltip-primary" data-placement="top" title="Activar" data-original-title="Tooltip on top" 
                             data-id_elemento="${row.id_elemento}"> 
                             <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`}
                }
            },
            // Columna 14: BOTON PARA EDITAR ELEMENTO
            {   
                targets: "editar:name", width: '5%', searchable: false, orderable: false, class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarElemento" data-toggle="tooltip-primary" data-placement="top" title="Editar"  
                             data-id_elemento="${row.id_elemento}"> 
                             <i class="fa-solid fa-edit"></i>
                             </button>`
                }
            }
        ],
        ajax: {
            url: '../../controller/elemento.php?op=listar',
            type: 'GET',
            data: function() {
                return {
                    id_articulo: idArticulo
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
        order: [[2, 'asc']] // Ordenar por código por defecto
    };

    /************************************/
    //     ZONA DE DEFINICIONES        //
    /**********************************/
    var $table = $('#elementos_data');
    var $tableConfig = datatable_elementosConfig;
    var $tableBody = $('#elementos_data tbody');
    var $columnFilterInputs = $('#elementos_data tfoot input, #elementos_data tfoot select');

    var table_e = $table.DataTable($tableConfig);

    // Cargar estados en el filtro personalizado después de que DataTables se inicialice
    table_e.on('init.dt', function() {
        console.log('🔄 DataTable inicializado, cargando estados en filtro personalizado...');
        // Cargar inmediatamente ya que el select está fuera de DataTables
        cargarEstadosElemento();
    });

    function format(d) {
        console.log(d);
        const esPropio = d.es_propio_elemento == 1;
        const tipoPropiedad = d.tipo_propiedad_elemento || (esPropio ? 'PROPIO' : 'ALQUILADO A PROVEEDOR');
        const badgeColor = esPropio ? 'bg-success' : 'bg-info';

        return `
            <div class="card border-primary mb-3" style="overflow: visible;">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-box-seam fs-3 me-2"></i>
                            <h5 class="card-title mb-0">Detalles del Elemento</h5>
                        </div>
                        <span class="badge ${badgeColor} fs-6">
                            <i class="bi ${esPropio ? 'bi-building' : 'bi-box-arrow-in-down'} me-1"></i>
                            ${tipoPropiedad}
                        </span>
                    </div>
                </div>
                <div class="card-body p-0" style="overflow: visible;">
                    <!-- Alerta con información de precio -->
                    <div class="alert ${esPropio ? 'alert-success' : 'alert-info'} mb-0 rounded-0 border-0" role="alert">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <strong><i class="bi bi-info-circle me-2"></i>${esPropio ? 'Equipo Propio' : 'Equipo Alquilado'}</strong>
                            </div>
                            <div class="text-end">
                                ${esPropio ? 
                                    `<strong>Precio de Compra:</strong> ${d.precio_compra_elemento ? parseFloat(d.precio_compra_elemento).toFixed(2) + ' €' : '0.00 €'}` :
                                    `<strong>Precio Alquiler/día:</strong> ${d.precio_dia_alquiler_elemento ? parseFloat(d.precio_dia_alquiler_elemento).toFixed(2) + ' €/día' : '0.00 €/día'}`
                                }
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <!-- Columna izquierda -->
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped table-hover mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-hash me-2"></i>ID Elemento
                                        </th>
                                        <td class="pe-4">
                                            ${d.id_elemento || '<span class="text-muted fst-italic">Sin ID</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-upc me-2"></i>Código
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
                                            <i class="bi bi-box-seam me-2"></i>Peso
                                        </th>
                                        <td class="pe-4">
                                            ${d.peso_elemento ? parseFloat(d.peso_elemento).toFixed(3) + ' kg' : '<span class="text-muted fst-italic">--</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        
                        <!-- Columna derecha -->
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped table-hover mb-0">
                                <tbody>
                                    ${esPropio ? `
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar-event me-2"></i>Fecha Compra
                                        </th>
                                        <td class="pe-4">
                                            ${d.fecha_compra_elemento ? formatoFechaEuropeo(d.fecha_compra_elemento) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-currency-euro me-2"></i>Precio Compra
                                        </th>
                                        <td class="pe-4">
                                            ${d.precio_compra_elemento ? parseFloat(d.precio_compra_elemento).toFixed(2) + ' €' : '0.00 €'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-shop me-2"></i>Proveedor Compra
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_proveedor_compra || '<span class="text-muted fst-italic">Sin proveedor</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-calendar-check me-2"></i>Fecha Alta
                                        </th>
                                        <td class="pe-4">
                                            ${d.fecha_alta_elemento ? formatoFechaEuropeo(d.fecha_alta_elemento) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    ` : `
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-currency-euro me-2"></i>Precio Alquiler/día
                                        </th>
                                        <td class="pe-4">
                                            ${d.precio_dia_alquiler_elemento ? parseFloat(d.precio_dia_alquiler_elemento).toFixed(2) + ' €/día' : '0.00 €/día'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-shop me-2"></i>Proveedor Alquiler
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_proveedor_alquiler || '<span class="text-muted fst-italic">Sin proveedor</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-credit-card me-2"></i>Forma de Pago
                                        </th>
                                        <td class="pe-4">
                                            ${d.nombre_forma_pago_alquiler || '<span class="text-muted fst-italic">Sin especificar</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-file-text me-2"></i>Condiciones de Alquiler
                                        </th>
                                        <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            ${d.observaciones_alquiler_elemento || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                        </td>
                                    </tr>
                                    `}
                                    ${esPropio ? `
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-shield-check me-2"></i>Fin Garantía
                                        </th>
                                        <td class="pe-4">
                                            ${d.fecha_fin_garantia_elemento ? formatoFechaEuropeo(d.fecha_fin_garantia_elemento) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-tools me-2"></i>Próximo Mantenimiento
                                        </th>
                                        <td class="pe-4">
                                            ${d.proximo_mantenimiento_elemento ? formatoFechaEuropeo(d.proximo_mantenimiento_elemento) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-shield-exclamation me-2"></i>Estado Garantía
                                        </th>
                                        <td class="pe-4">
                                            ${d.estado_garantia_elemento && d.estado_garantia_elemento !== 'Sin garantía' ? 
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
                                            ${d.estado_mantenimiento_elemento && d.estado_mantenimiento_elemento !== 'Sin programar' ? 
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
                                    ` : ''}
                                    <tr>
                                        <th scope="row" class="ps-4 w-40 align-top">
                                            <i class="bi bi-chat-text me-2"></i>Observaciones
                                        </th>
                                        <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                            ${d.observaciones_elemento || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Creado: ${d.created_at_elemento ? formatoFechaEuropeo(d.created_at_elemento) : 'Sin fecha'} | Actualizado: ${d.updated_at_elemento ? formatoFechaEuropeo(d.updated_at_elemento) : 'Sin fecha'}</small>
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

    // Función para cargar información del artículo
    function cargarInfoArticulo(id_articulo) {
        $.post("../../controller/articulo.php?op=mostrar", { id_articulo: id_articulo }, function (data) {
            if (data) {
                $('#nombre-articulo').text(data.nombre_articulo || 'Sin nombre');
                $('#codigo-articulo').text(data.codigo_articulo || '--');
                $('#id-articulo').text(id_articulo);
            }
        }, 'json').fail(function() {
            $('#nombre-articulo').text('Error al cargar');
        });
    }

    // Función para cargar estados en el filtro
    function cargarEstadosElemento() {
        console.log('🔄 Cargando estados de elemento en filtro personalizado...');
        
        $.ajax({
            url: "../../controller/estado_elemento.php?op=listar",
            type: "GET",
            dataType: "json",
            success: function (response) {
                console.log('✅ Respuesta recibida:', response);
                
                if (response && response.data) {
                    // Buscar el select FUERA de DataTables (el que agregamos arriba de la tabla)
                    const $select = $('#filtro-estado-elemento');
                    console.log('📍 Select personalizado encontrado:', $select.length);
                    
                    if ($select.length === 0) {
                        console.error('❌ No se encontró el select #filtro-estado-elemento');
                        return;
                    }
                    
                    // Limpiar y cargar opciones
                    $select.empty().append('<option value="">Todos los estados</option>');
                    
                    let contador = 0;
                    response.data.forEach(function(estado) {
                        if (estado.activo_estado_elemento == 1) {
                            const desc = estado.descripcion_estado_elemento;
                            $select.append($('<option></option>').val(desc).text(desc));
                            contador++;
                        }
                    });
                    
                    console.log(`✅ ${contador} estados cargados en el select personalizado`);
                    console.log(`📍 Total de opciones: ${$select.find('option').length}`);
                    
                    // Conectar el evento change para filtrar la tabla
                    $select.off('change').on('change', function() {
                        const val = $(this).val();
                        console.log('🔍 Filtrando tabla por estado:', val || 'Todos');
                        
                        // Filtrar la tabla
                        table_e.column(7).search(val).draw();
                        
                        // Actualizar el mensaje informativo
                        const $filtroInfo = $('#filtro-info');
                        if (val) {
                            $filtroInfo.html(`<strong>Filtrando por estado:</strong> <span class="badge bg-primary">${val}</span>`);
                        } else {
                            $filtroInfo.html('Mostrando todos los elementos');
                        }
                    });
                    
                    console.log('✅ Filtro de estados configurado correctamente');
                } else {
                    console.warn('⚠️ No se encontraron datos en la respuesta');
                }
            },
            error: function(xhr, status, error) {
                console.error('❌ Error al cargar estados:', error);
                console.error('Status:', status);
                console.error('Respuesta:', xhr.responseText);
            }
        });
    }


    /////////////////////////////////////
    //   INICIO ZONA DELETE ELEMENTO   //
    ///////////////////////////////////
    function desacElemento(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar el elemento con ID ${id}?<br><br><small class="text-warning"><i class="bi bi-exclamation-triangle me-1"></i>El elemento quedará marcado como inactivo</small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/elemento.php?op=eliminar", { id_elemento: id }, function (data) {
                    $table.DataTable().ajax.reload();
                    Swal.fire(
                        'Desactivado',
                        'El elemento ha sido desactivado',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
    $(document).on('click', '.desacElemento', function (event) {
        event.preventDefault();
        let id = $(this).data('id_elemento');
        desacElemento(id);
    });

    ///////////////////////////////////////
    //   INICIO ZONA ACTIVAR ELEMENTO    //
    /////////////////////////////////////
    function activarElemento(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar el elemento con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/elemento.php?op=activar", { id_elemento: id }, function (data) {
                    $table.DataTable().ajax.reload();
                    Swal.fire(
                        'Activado',
                        'El elemento ha sido activado',
                        'success'
                    )
                });
            }
        })
    }

    // CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
    $(document).on('click', '.activarElemento', function (event) {
        event.preventDefault();
        let id = $(this).data('id_elemento');
        console.log("id elemento:",id);
        activarElemento(id);
    });

    ///////////////////////////////////////
    //      INICIO ZONA EDITAR           //
    //        BOTON DE EDITAR           //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
    $(document).on('click', '.editarElemento', function (event) {
        event.preventDefault();

        let id = $(this).data('id_elemento');
        console.log("id elemento:", id);

        // Redirigir al formulario independiente en modo edición
        window.location.href = `formularioElemento.php?modo=editar&id=${id}&id_articulo=${idArticulo}`;
    });

    ///////////////////////////////////////
    //   INICIO ZONA VER DOCUMENTOS      //
    //    BOTON DE VER DOCUMENTOS       //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE VER DOCUMENTOS
    $(document).on('click', '.verDocumentos', function (event) {
        event.preventDefault();

        let id_elemento = $(this).data('id_elemento');

        // Leer id_articulo **directamente desde la URL en este momento**
        const urlParams = new URLSearchParams(window.location.search);
        const id_articulo = urlParams.get('id_articulo');

        // Generar URL completa
        let url = `../MntDocumento_elemento/index.php?id_elemento=${id_elemento}`;
        if (id_articulo) {
            url += `&id_articulo=${id_articulo}`;
        }
        url += '&origen=elementos';
        
        window.location.href = url;
});
    ///////////////////////////////////////
    //     FIN ZONA VER DOCUMENTOS      //
    /////////////////////////////////////

    ///////////////////////////////////////
    //   INICIO ZONA VER FOTOS          //
    //    BOTON DE VER FOTOS            //
    /////////////////////////////////////
    // CAPTURAR EL CLICK EN EL BOTÓN DE VER FOTOS
    $(document).on('click', '.verFotos', function (event) {
        event.preventDefault();

        let id_elemento = $(this).data('id_elemento');
        let codigo_elemento = $(this).data('codigo_elemento');
        console.log("Ver fotos del elemento ID:", id_elemento, "Código:", codigo_elemento);

        // Leer id_articulo directamente desde la URL en este momento
        const urlParams = new URLSearchParams(window.location.search);
        const id_articulo = urlParams.get('id_articulo');

        // Generar URL completa con id_articulo si existe
        let url = `../MntFoto_elemento/index.php?id_elemento=${id_elemento}`;
        if (id_articulo) {
            url += `&id_articulo=${id_articulo}`;
        }
        
        window.location.href = url;
    });
    ///////////////////////////////////////
    //     FIN ZONA VER FOTOS           //
    /////////////////////////////////////

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
        
        return `${dia}/${mes}/${año}`;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Error en fecha';
    }
}