/**
 * =======================================================
 * MÓDULO DE LÍNEAS DE PRESUPUESTO
 * =======================================================
 * Gestión de líneas de presupuesto por versión
 * Respeta sistema de inmutabilidad de versiones
 * =======================================================
 */

let tabla;
let id_version_presupuesto = null;
let estado_version_actual = null; // Controla si se puede editar

$(document).ready(function () {
    // Obtener ID de versión desde URL
    const urlParams = new URLSearchParams(window.location.search);
    id_version_presupuesto = urlParams.get('id_version_presupuesto');

    if (!id_version_presupuesto) {
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: 'No se ha especificado una versión de presupuesto',
            confirmButtonText: 'Volver a presupuestos'
        }).then(() => {
            window.location.href = '../Presupuesto/mntpresupuesto.php';
        });
        return;
    }

    // Cargar información de la versión
    cargarInfoVersion();

    // Inicializar DataTable
    inicializarDataTable();

    // Cargar totales del pie
    cargarTotales();

    // Event listeners
    $('#btn-nueva-linea').on('click', function () {
        if (!puedeEditar()) {
            mostrarAlertaVersionBloqueada();
            return;
        }
        abrirModalNuevaLinea();
    });

    $('#clear-filter').on('click', function () {
        limpiarFiltros();
    });
    
    // Event listeners para cálculo de días de diferencia en fechas
    $(document).on('change', '#fecha_montaje_linea_ppto, #fecha_desmontaje_linea_ppto', function() {
        actualizarDiasPlanificacion();
    });
    
    $(document).on('change', '#fecha_inicio_linea_ppto, #fecha_fin_linea_ppto', function() {
        actualizarDiasEvento();
    });
});

/**
 * Carga información de la versión del presupuesto
 */
function cargarInfoVersion() {
    $.ajax({
        url: '../../controller/presupuesto.php?op=get_info_version',
        type: 'POST',
        data: { id_version_presupuesto: id_version_presupuesto },
        dataType: 'json',
        success: function (response) {
            console.log("Respuesta de get_info_version:", response);
            
            if (response.success && response.data) {
                const data = response.data;
                console.log("Datos de versión recibidos:", data);
                
                // Guardar estado para controles
                estado_version_actual = data.estado_version_presupuesto;
                
                // Actualizar info en card
                $('#numero-presupuesto').html(
                    `<i class="bi bi-file-earmark-text me-2"></i>${data.numero_presupuesto || 'Sin número'}`
                );
                $('#nombre-cliente').text(data.nombre_cliente || '--');
                $('#nombre-evento').text(data.nombre_evento_presupuesto || 'Sin evento');
                $('#numero-version').text(`v${data.numero_version_presupuesto || '0'}`);
                
                // Badge de estado con colores
                let estadoBadgeClass = 'bg-secondary';
                let estadoIcono = 'bi-circle';
                
                switch (data.estado_version_presupuesto) {
                    case 'borrador':
                        estadoBadgeClass = 'bg-warning text-dark';
                        estadoIcono = 'bi-pencil';
                        break;
                    case 'enviado':
                        estadoBadgeClass = 'bg-info';
                        estadoIcono = 'bi-send';
                        break;
                    case 'aceptado':
                        estadoBadgeClass = 'bg-success';
                        estadoIcono = 'bi-check-circle';
                        break;
                    case 'rechazado':
                        estadoBadgeClass = 'bg-danger';
                        estadoIcono = 'bi-x-circle';
                        break;
                    case 'caducado':
                        estadoBadgeClass = 'bg-secondary';
                        estadoIcono = 'bi-clock-history';
                        break;
                }
                
                $('#estado-version-badge').html(`
                    <span class="badge ${estadoBadgeClass} badge-estado-version">
                        <i class="${estadoIcono} me-1"></i>${data.estado_version_presupuesto.toUpperCase()}
                    </span>
                `);
                
                // Mostrar/ocultar alerta de versión bloqueada
                if (!puedeEditar()) {
                    $('#alert-version-bloqueada').show();
                    $('#btn-nueva-linea').prop('disabled', true);
                } else {
                    $('#alert-version-bloqueada').hide();
                    $('#btn-nueva-linea').prop('disabled', false);
                }
                
                // Configurar enlaces de navegación para volver al presupuesto específico
                if (data.id_presupuesto) {
                    const urlPresupuesto = `../Presupuesto/index.php?id_presupuesto=${data.id_presupuesto}`;
                    $('#btn-volver-header, #btn-volver-footer').attr('href', urlPresupuesto);
                    
                    // Actualizar breadcrumb con número de presupuesto
                    $('#breadcrumb-presupuesto')
                        .attr('href', urlPresupuesto)
                        .html(`<i class="bi bi-file-earmark-text"></i> ${data.numero_presupuesto || 'Presupuesto'}`);
                }
            } else {
                console.error("Error en respuesta:", response);
            }
        },
        error: function (xhr, status, error) {
            console.error("Error AJAX:", xhr.responseText);
            console.error("Status:", status);
            console.error("Error:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la información de la versión'
            });
        }
    });
}

/**
 * Verifica si la versión actual puede ser editada
 */
function puedeEditar() {
    return estado_version_actual === 'borrador';
}

/**
 * Muestra alerta cuando se intenta editar una versión bloqueada
 */
function mostrarAlertaVersionBloqueada() {
    Swal.fire({
        icon: 'warning',
        title: 'Versión bloqueada',
        html: `
            Esta versión está en estado <strong>"${estado_version_actual}"</strong> y no puede ser modificada.<br>
            Las versiones solo pueden editarse cuando están en estado <strong>"borrador"</strong>.<br><br>
            Para hacer cambios, debe crear una nueva versión desde el listado de presupuestos.
        `,
        confirmButtonText: 'Entendido'
    });
}

/**
 * Inicializa DataTable con configuración completa
 */
function inicializarDataTable() {
    var datatable_lineasConfig = {
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
            emptyTable: "No hay líneas registradas para esta versión",
            info: "Mostrando _START_ a _END_ de _TOTAL_ líneas",
            infoEmpty: "Mostrando 0 a 0 de 0 líneas",
            infoFiltered: "(filtrado de _MAX_ líneas totales)",
            lengthMenu: "Mostrar _MENU_ líneas por página",
            loadingRecords: "Cargando...",
            processing: "Procesando...",
            search: "Buscar:",
            zeroRecords: "No se encontraron líneas que coincidan con la búsqueda",
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        columns: [
            { name: 'orden_linea_ppto', data: 'orden_linea_ppto', className: "text-center align-middle" },
            { name: 'codigo_linea_ppto', data: 'codigo_linea_ppto', className: "text-center align-middle" },
            { name: 'descripcion_linea_ppto', data: 'descripcion_linea_ppto', className: "text-left align-middle" },
            { name: 'tipo_linea_ppto', data: 'tipo_linea_ppto', className: "text-center align-middle" },
            { name: 'cantidad_linea_ppto', data: 'cantidad_linea_ppto', className: "text-center align-middle" },
            { name: 'precio_unitario_linea_ppto', data: 'precio_unitario_linea_ppto', className: "text-end align-middle" },
            { name: 'descuento_linea_ppto', data: 'descuento_linea_ppto', className: "text-center align-middle" },
            { name: 'valor_coeficiente_linea_ppto', data: 'valor_coeficiente_linea_ppto', className: "text-center align-middle" },
            { name: 'base_imponible', data: 'base_imponible', className: "text-end align-middle" },
            { name: 'porcentaje_iva_linea_ppto', data: 'porcentaje_iva_linea_ppto', className: "text-center align-middle" },
            { name: 'importe_iva', data: 'importe_iva', className: "text-end align-middle" },
            { name: 'total_linea', data: 'total_linea', className: "text-end align-middle" },
            { name: 'activo_linea_ppto', data: 'activo_linea_ppto', className: "text-center align-middle" },
            { name: 'acciones', data: null, className: "text-center align-middle" }
        ],
        columnDefs: [
            // Columna 0: Orden
            { targets: "orden_linea_ppto:name", width: '5%', searchable: false, orderable: true, className: "text-center" },
            // Columna 1: Código
            { 
                targets: "codigo_linea_ppto:name", 
                width: '8%', 
                searchable: true, 
                orderable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    return data || row.codigo_articulo || '--';
                }
            },
            // Columna 2: Descripción
            { 
                targets: "descripcion_linea_ppto:name", 
                width: '20%', 
                searchable: true, 
                orderable: true, 
                className: "text-left",
                render: function (data, type, row) {
                    if (type === "display") {
                        let html = `<div class="text-truncate" style="max-width: 300px;" title="${data}">${data}</div>`;
                        if (row.nombre_articulo && row.nombre_articulo !== data) {
                            html += `<small class="text-muted">${row.nombre_articulo}</small>`;
                        }
                        return html;
                    }
                    return data;
                }
            },
            // Columna 3: Tipo
            {
                targets: "tipo_linea_ppto:name",
                width: '8%',
                orderable: true,
                searchable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        const tipos = {
                            'articulo': '<span class="badge bg-primary">Artículo</span>',
                            'kit': '<span class="badge bg-info">Kit</span>',
                            'seccion': '<span class="badge bg-secondary">Sección</span>',
                            'texto': '<span class="badge bg-light text-dark">Texto</span>'
                        };
                        return tipos[data] || data;
                    }
                    return data;
                }
            },
            // Columna 4: Cantidad
            { targets: "cantidad_linea_ppto:name", width: '5%', searchable: false, orderable: true, className: "text-center" },
            // Columna 5: Precio Unitario
            {
                targets: "precio_unitario_linea_ppto:name",
                width: '8%',
                searchable: false,
                orderable: true,
                className: "text-end",
                render: function (data, type, row) {
                    if (type === "display") {
                        return formatearMoneda(data);
                    }
                    return data;
                }
            },
            // Columna 6: Descuento
            {
                targets: "descuento_linea_ppto:name",
                width: '6%',
                searchable: false,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data > 0 ? `${data}%` : '--';
                    }
                    return data;
                }
            },
            // Columna 7: Coeficiente
            {
                targets: "valor_coeficiente_linea_ppto:name",
                width: '6%',
                searchable: false,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        if (data && parseFloat(data) > 1) {
                            return `<span class="badge bg-warning text-dark" title="${row.jornadas_linea_ppto} jornadas">x${data}</span>`;
                        }
                        return '--';
                    }
                    return data;
                }
            },
            // Columna 8: Base Imponible
            {
                targets: "base_imponible:name",
                width: '8%',
                searchable: false,
                orderable: true,
                className: "text-end",
                render: function (data, type, row) {
                    if (type === "display") {
                        return `<strong>${formatearMoneda(data)}</strong>`;
                    }
                    return data;
                }
            },
            // Columna 9: Porcentaje IVA
            {
                targets: "porcentaje_iva_linea_ppto:name",
                width: '5%',
                searchable: false,
                orderable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return `${data}%`;
                    }
                    return data;
                }
            },
            // Columna 10: Importe IVA
            {
                targets: "importe_iva:name",
                width: '8%',
                searchable: false,
                orderable: true,
                className: "text-end",
                render: function (data, type, row) {
                    if (type === "display") {
                        return formatearMoneda(data);
                    }
                    return data;
                }
            },
            // Columna 11: Total
            {
                targets: "total_linea:name",
                width: '8%',
                searchable: false,
                orderable: true,
                className: "text-end",
                render: function (data, type, row) {
                    if (type === "display") {
                        return `<strong class="text-success">${formatearMoneda(data)}</strong>`;
                    }
                    return data;
                }
            },
            // Columna 12: Estado
            {
                targets: "activo_linea_ppto:name",
                width: '6%',
                orderable: true,
                searchable: true,
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return data == 1 ? 
                            '<i class="bi bi-check-circle text-success fa-2x"></i>' : 
                            '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return data;
                }
            },
            // Columna 13: Acciones
            {
                targets: "acciones:name",
                width: '10%',
                searchable: false,
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {
                    const puedeEditarLinea = puedeEditar();
                    
                    if (puedeEditarLinea) {
                        return `
                            <button type="button" class="btn btn-info btn-sm editarLinea" data-bs-toggle="tooltip-primary" data-placement="top" title="Editar"  
                                 data-id_linea_ppto="${row.id_linea_ppto}"> 
                                 <i class="fa-solid fa-edit"></i>
                            </button>
                            <button type="button" class="btn btn-danger btn-sm eliminarLinea" data-bs-toggle="tooltip-primary" data-placement="top" title="Eliminar" 
                                 data-id_linea_ppto="${row.id_linea_ppto}"> 
                                 <i class="fa-solid fa-trash"></i>
                            </button>
                        `;
                    } else {
                        return `
                            <button class="btn btn-sm btn-secondary" disabled title="Versión bloqueada">
                                <i class="bi bi-lock"></i>
                            </button>
                        `;
                    }
                }
            }
        ],
        ajax: {
            url: '../../controller/lineapresupuesto.php?op=listar',
            type: 'POST',
            data: function() {
                return {
                    id_version_presupuesto: id_version_presupuesto
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
        order: [[0, 'asc']] // Ordenar por orden por defecto
    };

    var $table = $('#lineas_data');
    var $tableConfig = datatable_lineasConfig;
    var $tableBody = $('#lineas_data tbody');
    var $columnFilterInputs = $('#lineas_data tfoot input, #lineas_data tfoot select');

    tabla = $table.DataTable($tableConfig);

    // Aplicar búsqueda en columnas del footer
    $columnFilterInputs.on('keyup change clear', function () {
        var column_index = $(this).parent().index();
        var search_value = this.value;
        
        if (tabla.column(column_index).search() !== search_value) {
            tabla.column(column_index).search(search_value).draw();
        }
        actualizarAlertaFiltros();
    });

    // Recargar totales después de cada actualización de tabla
    tabla.on('draw', function () {
        cargarTotales();
    });
    
    // Event listeners para botones
    $tableBody.on('click', '.editarLinea', function() {
        var id_linea_ppto = $(this).data('id_linea_ppto');
        editarLinea(id_linea_ppto);
    });
    
    $tableBody.on('click', '.eliminarLinea', function() {
        var id_linea_ppto = $(this).data('id_linea_ppto');
        eliminarLinea(id_linea_ppto);
    });
}

/**
 * Carga los totales del pie (base, IVA, total)
 */
function cargarTotales() {
    $.ajax({
        url: '../../controller/lineapresupuesto.php?op=totales',
        type: 'POST',
        data: { id_version_presupuesto: id_version_presupuesto },
        dataType: 'json',
        success: function (response) {
            if (response.success && response.data) {
                const data = response.data;
                
                // Actualizar valores
                $('#total-base').text(formatearMoneda(data.total_base_imponible || 0));
                $('#total-iva').text(formatearMoneda(data.total_iva || 0));
                $('#total-con-iva').text(formatearMoneda(data.total_con_iva || 0));
                $('#cantidad-lineas').text(data.cantidad_lineas || 0);
                
                // Desglose de IVA
                let desgloseHTML = '';
                if (data.iva_21 && parseFloat(data.iva_21) > 0) {
                    desgloseHTML += `21%: ${formatearMoneda(data.iva_21)} `;
                }
                if (data.iva_10 && parseFloat(data.iva_10) > 0) {
                    desgloseHTML += `10%: ${formatearMoneda(data.iva_10)} `;
                }
                if (data.iva_4 && parseFloat(data.iva_4) > 0) {
                    desgloseHTML += `4%: ${formatearMoneda(data.iva_4)} `;
                }
                if (data.iva_0 && parseFloat(data.iva_0) > 0) {
                    desgloseHTML += `0%: ${formatearMoneda(data.iva_0)} `;
                }
                
                $('#desglose-iva').html(desgloseHTML || 'Sin IVA aplicado');
            }
        },
        error: function () {
            console.error('No se pudieron cargar los totales');
        }
    });
}

/**
 * Formatea un número como moneda (euros)
 */
function formatearMoneda(valor) {
    const numero = parseFloat(valor) || 0;
    return new Intl.NumberFormat('es-ES', {
        style: 'currency',
        currency: 'EUR',
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(numero);
}

/**
 * Abre modal para crear nueva línea
 */
function abrirModalNuevaLinea() {
    // Limpiar formulario
    $('#formLinea')[0].reset();
    $('#id_linea_ppto').val('');
    $('#id_version_presupuesto_hidden').val(id_version_presupuesto);
    
    // Cambiar título
    $('#modalFormularioLineaLabel').text('Nueva Línea de Presupuesto');
    
    // Mostrar modal
    $('#modalFormularioLinea').modal('show');
}

/**
 * Edita una línea existente
 */
function editarLinea(id_linea_ppto) {
    if (!puedeEditar()) {
        mostrarAlertaVersionBloqueada();
        return;
    }

    $.ajax({
        url: '../../controller/lineapresupuesto.php?op=mostrar',
        type: 'POST',
        data: { id_linea_ppto: id_linea_ppto },
        dataType: 'json',
        success: function (response) {
            if (response.success && response.data) {
                const data = response.data;
                
                // Rellenar formulario
                $('#id_linea_ppto').val(data.id_linea_ppto);
                $('#id_version_presupuesto_hidden').val(data.id_version_presupuesto);
                $('#id_articulo').val(data.id_articulo).trigger('change');
                $('#tipo_linea_ppto').val(data.tipo_linea_ppto);
                $('#descripcion_linea_ppto').val(data.descripcion_linea_ppto);
                $('#cantidad_linea_ppto').val(data.cantidad_linea_ppto);
                $('#precio_unitario_linea_ppto').val(data.precio_unitario_linea_ppto);
                $('#descuento_linea_ppto').val(data.descuento_linea_ppto);
                $('#porcentaje_iva_linea_ppto').val(data.porcentaje_iva_linea_ppto);
                $('#jornadas_linea_ppto').val(data.jornadas_linea_ppto);
                
                // Cambiar título
                $('#modalFormularioLineaLabel').text('Editar Línea de Presupuesto');
                
                // Mostrar modal
                $('#modalFormularioLinea').modal('show');
            }
        },
        error: function () {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudo cargar la línea'
            });
        }
    });
}

/**
 * Elimina una línea (soft delete)
 */
function eliminarLinea(id_linea_ppto) {
    if (!puedeEditar()) {
        mostrarAlertaVersionBloqueada();
        return;
    }

    Swal.fire({
        title: '¿Eliminar línea?',
        text: 'Esta acción desactivará la línea del presupuesto',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sí, eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/lineapresupuesto.php?op=desactivar',
                type: 'POST',
                data: { id_linea_ppto: id_linea_ppto },
                dataType: 'json',
                success: function (response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Eliminado',
                            text: response.message,
                            timer: 2000,
                            showConfirmButton: false
                        });
                        tabla.ajax.reload();
                        cargarTotales();
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: response.message
                        });
                    }
                },
                error: function () {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'No se pudo eliminar la línea'
                    });
                }
            });
        }
    });
}

/**
 * Limpia todos los filtros aplicados
 */
function limpiarFiltros() {
    tabla.columns().every(function () {
        const column = this;
        const footer = $(column.footer());
        const input = footer.find('input, select');
        
        if (input.length) {
            input.val('');
            column.search('').draw();
        }
    });
    
    $('#filter-alert').hide();
}

/**
 * Actualiza alerta de filtros activos
 */
function actualizarAlertaFiltros() {
    let filtrosActivos = [];
    
    tabla.columns().every(function () {
        const column = this;
        const searchValue = column.search();
        
        if (searchValue) {
            const header = $(column.header()).text();
            filtrosActivos.push(`${header}: "${searchValue}"`);
        }
    });
    
    if (filtrosActivos.length > 0) {
        $('#active-filters-text').text(filtrosActivos.join(', '));
        $('#filter-alert').show();
    } else {
        $('#filter-alert').hide();
    }
}
/**
 * Abre modal para crear nueva línea
 * Carga datos iniciales: fechas, artículos, ubicaciones
 */
function abrirModalNuevaLinea() {
    // Resetear formulario
    $('#formLinea')[0].reset();
    $('#id_linea_ppto').val('');
    $('#id_version_presupuesto_hidden').val(id_version_presupuesto);
    
    // Cambiar título del modal
    $('#modalFormularioLineaLabel').html('<i class="bi bi-plus-circle me-2"></i>Nueva Línea de Presupuesto');
    
    // Cargar datos iniciales
    cargarFechasIniciales();
    cargarArticulosDisponibles();
    cargarUbicacionesCliente();
    
    // Mostrar modal
    $('#modalFormularioLinea').modal('show');
}

/**
 * Carga las fechas iniciales desde la cabecera del presupuesto
 */
function cargarFechasIniciales() {
    $.ajax({
        url: '../../controller/presupuesto.php?op=get_fechas_evento',
        type: 'POST',
        data: { id_version_presupuesto: id_version_presupuesto },
        dataType: 'json',
        success: function(response) {
            if (response.success && response.data) {
                const fechas = response.data;
                
                // Cargar fechas de planificación (montaje/desmontaje)
                if (fechas.fecha_inicio_evento) {
                    $('#fecha_montaje_linea_ppto').val(fechas.fecha_inicio_evento);
                }
                if (fechas.fecha_fin_evento) {
                    $('#fecha_desmontaje_linea_ppto').val(fechas.fecha_fin_evento);
                }
                
                // Cargar fechas del evento (inicio/fin para cobro)
                if (fechas.fecha_inicio_evento) {
                    $('#fecha_inicio_linea_ppto').val(fechas.fecha_inicio_evento);
                }
                if (fechas.fecha_fin_evento) {
                    $('#fecha_fin_linea_ppto').val(fechas.fecha_fin_evento);
                }
                
                // Calcular días de diferencia después de cargar las fechas
                actualizarDiasPlanificacion();
                actualizarDiasEvento();
            }
        },
        error: function() {
            console.error('No se pudieron cargar las fechas del evento');
        }
    });
}

/**
 * Carga artículos disponibles en el select
 * Incluye artículos y KITs pero solo permite seleccionar artículos
 */
function cargarArticulosDisponibles() {
    $.ajax({
        url: '../../controller/articulo.php?op=listar_para_presupuesto',
        type: 'POST',
        dataType: 'json',
        success: function(response) {
            const select = $('#id_articulo');
            select.empty();
            select.append('<option value="">Buscar artículo...</option>');
            
            if (response.data && response.data.length > 0) {
                response.data.forEach(function(articulo) {
                    // Mostrar KITs pero deshabilitarlos (no se pueden añadir directamente)
                    const esKit = articulo.es_kit == 1 || articulo.es_kit == '1';
                    const disabled = esKit ? 'disabled' : '';
                    const prefijo = esKit ? '[KIT] ' : '';
                    const precio = parseFloat(articulo.precio_alquiler_articulo || 0).toFixed(2);
                    
                    select.append(
                        `<option value="${articulo.id_articulo}" ${disabled} 
                                 data-precio="${articulo.precio_alquiler_articulo}"
                                 data-iva="${articulo.porcentaje_iva || 21}"
                                 data-nombre="${articulo.nombre_articulo}">
                            ${prefijo}${articulo.codigo_articulo} - ${articulo.nombre_articulo} (${precio} €)
                        </option>`
                    );
                });
                
                // Reinicializar Select2 si está disponible
                if ($.fn.select2 && select.hasClass('select2-hidden-accessible')) {
                    select.select2('destroy');
                }
                
                if ($.fn.select2) {
                    select.select2({
                        theme: 'bootstrap-5',
                        dropdownParent: $('#modalFormularioLinea'),
                        placeholder: 'Buscar artículo...',
                        allowClear: true,
                        width: '100%'
                    });
                    
                    // Event listener para cuando cambia el artículo
                    select.on('change', function() {
                        const idArticulo = $(this).val();
                        if (idArticulo) {
                            verificarEstadoCoeficienteArticulo(idArticulo);
                        } else {
                            ocultarInfoEstadoCoeficiente();
                        }
                    });
                }
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'No se pudieron cargar los artículos disponibles'
            });
        }
    });
}

/**
 * Carga ubicaciones del cliente actual
 */
function cargarUbicacionesCliente() {
    console.log('Cargando ubicaciones del cliente para versión:', id_version_presupuesto);
    
    // Primero necesitamos el ID del cliente desde la info de versión
    $.ajax({
        url: '../../controller/presupuesto.php?op=get_info_version',
        type: 'POST',
        data: { id_version_presupuesto: id_version_presupuesto },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta get_info_version:', response);
            
            if (response.success && response.data && response.data.id_cliente) {
                const idCliente = response.data.id_cliente;
                console.log('ID Cliente encontrado:', idCliente);
                cargarUbicacionesPorCliente(idCliente);
            } else {
                console.error('No se pudo obtener el ID del cliente:', response);
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al obtener info versión:', error);
            console.error('Respuesta:', xhr.responseText);
        }
    });
}

/**
 * Carga ubicaciones de un cliente específico
 */
function cargarUbicacionesPorCliente(idCliente) {
    console.log('Cargando ubicaciones para cliente:', idCliente);
    
    $.ajax({
        url: '../../controller/ubicaciones.php?op=listar_por_cliente',
        type: 'POST',
        data: { id_cliente: idCliente },
        dataType: 'json',
        success: function(response) {
            console.log('Respuesta ubicaciones:', response);
            
            const select = $('#id_ubicacion');
            
            if (!select.length) {
                console.error('No se encontró el select #id_ubicacion');
                return;
            }
            
            select.empty();
            select.append('<option value="">Sin ubicación específica</option>');
            
            if (response.success && response.data && response.data.length > 0) {
                console.log('Ubicaciones encontradas:', response.data.length);
                response.data.forEach(function(ubicacion) {
                    const direccion = ubicacion.direccion_ubicacion || '';
                    const poblacion = ubicacion.poblacion_ubicacion || '';
                    const detalle = direccion ? `${direccion}, ${poblacion}` : poblacion;
                    
                    select.append(
                        `<option value="${ubicacion.id_ubicacion}">
                            ${ubicacion.nombre_ubicacion}${detalle ? ' - ' + detalle : ''}
                        </option>`
                    );
                });
            } else {
                console.log('No hay ubicaciones para este cliente');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar ubicaciones:', error);
            console.error('Respuesta:', xhr.responseText);
        }
    });
}

/**
 * Calcula la diferencia de días entre dos fechas
 * @param {string} fechaInicio - Fecha en formato YYYY-MM-DD
 * @param {string} fechaFin - Fecha en formato YYYY-MM-DD
 * @returns {number} - Número de días de diferencia
 */
function calcularDiasDiferencia(fechaInicio, fechaFin) {
    if (!fechaInicio || !fechaFin) {
        return 0;
    }
    
    const inicio = new Date(fechaInicio);
    const fin = new Date(fechaFin);
    
    // Calcular diferencia en milisegundos y convertir a días
    const diferenciaMilisegundos = fin - inicio;
    const diferenciaDias = Math.floor(diferenciaMilisegundos / (1000 * 60 * 60 * 24));
    
    return diferenciaDias;
}

/**
 * Actualiza el indicador de días de diferencia para fechas de planificación (montaje/desmontaje)
 */
function actualizarDiasPlanificacion() {
    const fechaMontaje = $('#fecha_montaje_linea_ppto').val();
    const fechaDesmontaje = $('#fecha_desmontaje_linea_ppto').val();
    
    if (fechaMontaje && fechaDesmontaje) {
        const dias = calcularDiasDiferencia(fechaMontaje, fechaDesmontaje);
        
        if (dias >= 0) {
            $('#dias_planificacion_texto').text(`${dias} día${dias !== 1 ? 's' : ''}`);
            $('#dias_planificacion').show();
        } else {
            $('#dias_planificacion').hide();
            console.warn('La fecha de desmontaje es anterior a la fecha de montaje');
        }
    } else {
        $('#dias_planificacion').hide();
    }
}

/**
 * Actualiza el indicador de días de diferencia para fechas del evento
 */
function actualizarDiasEvento() {
    const fechaInicio = $('#fecha_inicio_linea_ppto').val();
    const fechaFin = $('#fecha_fin_linea_ppto').val();
    
    if (fechaInicio && fechaFin) {
        const dias = calcularDiasDiferencia(fechaInicio, fechaFin);
        
        if (dias >= 0) {
            $('#dias_evento_texto').text(`${dias} día${dias !== 1 ? 's' : ''}`);
            $('#dias_evento').show();
        } else {
            $('#dias_evento').hide();
            console.warn('La fecha fin del evento es anterior a la fecha inicio');
        }
    } else {
        $('#dias_evento').hide();
    }
}

/**
 * Verifica el estado del coeficiente para un artículo
 * @param {number} idArticulo - ID del artículo seleccionado
 */
function verificarEstadoCoeficienteArticulo(idArticulo) {
    $.ajax({
        url: '../../controller/articulo.php?op=get_estado_coeficiente',
        type: 'POST',
        data: { id_articulo: idArticulo },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                mostrarInfoEstadoCoeficiente(response.estado_coeficiente, response.mensaje);
            } else {
                console.error('Error al obtener estado de coeficiente:', response.message);
                ocultarInfoEstadoCoeficiente();
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la petición AJAX:', error);
            ocultarInfoEstadoCoeficiente();
        }
    });
}

/**
 * Muestra el mensaje de estado del coeficiente según el código recibido
 * @param {number} estadoCodigo - Código del estado (1, 2, 3, 4)
 * @param {string} mensaje - Mensaje descriptivo
 */
function mostrarInfoEstadoCoeficiente(estadoCodigo, mensaje) {
    const infoDiv = $('#info_estado_coeficiente');
    const textoSpan = $('#texto_estado_coeficiente');
    const checkbox = $('#aplicar_coeficiente_linea_ppto');
    
    // Remover clases de color previas
    infoDiv.removeClass('alert-success alert-danger alert-warning alert-info');
    
    // Configurar según el código
    switch(estadoCodigo) {
        case 1:
            // Artículo con coeficiente propio activo
            infoDiv.addClass('alert-success');
            textoSpan.html(`<strong>Estado: 1</strong> - ${mensaje}`);
            checkbox.prop('checked', true); // Activar checkbox
            break;
            
        case 2:
            // Artículo que NO permite coeficientes
            infoDiv.addClass('alert-danger');
            textoSpan.html(`<strong>Estado: 2</strong> - ${mensaje}`);
            checkbox.prop('checked', false); // Desactivar checkbox
            break;
            
        case 3:
            // Usa coeficiente de la familia
            infoDiv.addClass('alert-info');
            textoSpan.html(`<strong>Estado: 3</strong> - ${mensaje}`);
            checkbox.prop('checked', true); // Activar checkbox
            break;
            
        case 4:
            // Familia sin coeficientes configurados
            infoDiv.addClass('alert-warning');
            textoSpan.html(`<strong>Estado: 4</strong> - ${mensaje}`);
            checkbox.prop('checked', false); // Desactivar checkbox
            break;
            
        default:
            infoDiv.addClass('alert-secondary');
            textoSpan.html(`<strong>Estado desconocido</strong> - ${mensaje}`);
            checkbox.prop('checked', false); // Por defecto desactivar
    }
    
    infoDiv.show();
    
    // Disparar el evento change del checkbox para actualizar la UI del formulario
    checkbox.trigger('change');
}

/**
 * Oculta el mensaje de estado del coeficiente
 */
function ocultarInfoEstadoCoeficiente() {
    $('#info_estado_coeficiente').hide();
}