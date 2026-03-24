'use strict';

// ─── Constantes de controladores ─────────────────────────────────────────────
const CTRL      = '../../controller/cliente_panel.php';
const CTRL_PPTO = '../../controller/presupuesto.php';
const CTRL_DOC  = '../../controller/documento_presupuesto.php';
const CTRL_PAGO = '../../controller/pago_presupuesto.php';
const CTRL_CLI  = '../../controller/cliente.php';
const CTRL_CONT = '../../controller/clientes_contacto.php';
const CTRL_UBIC = '../../controller/ubicaciones.php';

// ─── Idioma compartido para todos los DataTables ──────────────────────────────
const DT_LANG = {
    emptyTable:     "No hay datos disponibles",
    info:           "Mostrando _START_ a _END_ de _TOTAL_ registros",
    infoEmpty:      "Mostrando 0 a 0 de 0 registros",
    infoFiltered:   "(filtrado de _MAX_ registros totales)",
    lengthMenu:     "Mostrar _MENU_ registros",
    loadingRecords: "Cargando...",
    processing:     "Procesando...",
    search:         "Buscar:",
    zeroRecords:    "No se encontraron registros coincidentes",
    paginate: {
        first:    '<i class="bi bi-chevron-double-left"></i>',
        last:     '<i class="bi bi-chevron-double-right"></i>',
        previous: '<i class="bi bi-chevron-compact-left"></i>',
        next:     '<i class="bi bi-chevron-compact-right"></i>'
    }
};

// ─── Instancias de DataTables ─────────────────────────────────────────────────
let tablaPresupuestos = null;
let tablaFacturas     = null;
let tablaPagos        = null;
let tablaContactos    = null;
let tablaUbicaciones  = null;

// ─── Document ready ───────────────────────────────────────────────────────────
$(document).ready(function () {

    if (typeof ID_CLIENTE === 'undefined' || !ID_CLIENTE) {
        console.error('[ClientePanel] ID_CLIENTE no definido.');
        return;
    }

    // Cargar cabecera, KPIs y contadores de badges al entrar
    cargarResumen();
    cargarKPIs();
    cargarContadores();

    // Lazy init: inicializar DataTables al activar cada pestaña
    $('#tab-presupuestos-btn').on('shown.bs.tab', function () {
        if (!tablaPresupuestos) inicializarTablaPresupuestos();
    });

    $('#tab-facturas-btn').on('shown.bs.tab', function () {
        if (!tablaFacturas) inicializarTablaFacturas();
    });

    $('#tab-pagos-btn').on('shown.bs.tab', function () {
        if (!tablaPagos) inicializarTablaPagos();
    });

    $('#tab-contactos-btn').on('shown.bs.tab', function () {
        if (!tablaContactos) inicializarTablaContactos();
    });

    $('#tab-ubicaciones-btn').on('shown.bs.tab', function () {
        if (!tablaUbicaciones) inicializarTablaUbicaciones();
    });

});

// ─── Contadores de tabs (badges) ────────────────────────────────────────────
function cargarContadores() {
    $.post(CTRL + '?op=contadores', { id_cliente: ID_CLIENTE })
        .done(function (res) {
            if (!res || !res.success) return;
            const d = res.data;
            $('#badge-presupuestos').text(d.presupuestos);
            $('#badge-facturas').text(d.facturas);
            $('#badge-pagos').text(d.pagos);
            $('#badge-contactos').text(d.contactos);
            $('#badge-ubicaciones').text(d.ubicaciones);
        })
        .fail(function () {
            console.error('[ClientePanel] Error al cargar contadores de tabs');
        });
}

// ─── KPIs ─────────────────────────────────────────────────────────────────────
function cargarKPIs() {
    $.post(CTRL + '?op=kpis', { id_cliente: ID_CLIENTE })
        .done(function (res) {
            if (!res || !res.success) return;
            const d = res.data;
            $('#kpiTotalPresupuestado').text(formatEuro(d.total_presupuestado));
            $('#kpiFacturasEmitidas').text(formatEuro(d.total_facturas_emitidas));
            $('#kpiTotalCobrado').text(formatEuro(d.total_cobrado));
            $('#kpiPendienteCobro').text(formatEuro(d.saldo_pendiente));
        })
        .fail(function () {
            console.error('[ClientePanel] Error al cargar KPIs');
        });
}

// ─── Resumen / cabecera del cliente ──────────────────────────────────────────
function cargarResumen() {
    $.post(CTRL_CLI + '?op=mostrar', { id_cliente: ID_CLIENTE })
        .done(function (data) {
            if (!data) return;
            const nombre = (data.nombre_cliente || '') +
                           (data.apellido_cliente ? ' ' + data.apellido_cliente : '');

            // Cabecera visible
            $('#lblNombreCliente').text(nombre);
            $('#lblCodigoCliente').text(data.codigo_cliente || '');
            $('#lblNifCliente').text(data.nif_cliente || '—');
            $('#lblTelefonoCliente').text(data.telefono_cliente || data.movil_cliente || '—');
            $('#lblEmailCliente').text(data.email_cliente || '—');
            if (parseInt(data.activo_cliente) === 1) {
                $('#badgeEstadoCliente').removeClass('bg-danger').addClass('bg-success').text('Activo');
            } else {
                $('#badgeEstadoCliente').removeClass('bg-success').addClass('bg-danger').text('Inactivo');
            }

            // Ficha de detalle (Tab Resumen)
            $('#resumen_codigo').text(data.codigo_cliente || '—');
            $('#resumen_nombre').text(nombre || '—');
            $('#resumen_nif').text(data.nif_cliente || '—');
            $('#resumen_tipo').text(data.tipo_cliente || '—');
            $('#resumen_telefono').text(data.telefono_cliente || '—');
            $('#resumen_movil').text(data.movil_cliente || '—');
            $('#resumen_email').text(data.email_cliente || '—');
            $('#resumen_direccion').text(data.direccion_cliente || '—');
            $('#resumen_poblacion').text(data.poblacion_cliente || '—');
            $('#resumen_cp').text(data.cp_cliente || '—');
            $('#resumen_provincia').text(data.provincia_cliente || '—');
            $('#resumen_pais').text(data.pais_cliente || 'España');
            $('#resumen_descuento').text(
                data.porcentaje_descuento_cliente != null
                    ? data.porcentaje_descuento_cliente + ' %'
                    : '—'
            );
            $('#resumen_observaciones').text(data.observaciones_cliente || '—');
            $('#resumen_created_at').text(data.created_at_cliente || '—');
            $('#resumen_updated_at').text(data.updated_at_cliente || '—');
            $('#resumen_num_contactos').text(
                data.contacto_cantidad_cliente != null ? data.contacto_cantidad_cliente : '—'
            );
            $('#resumen_forma_pago').text(data.nombre_pago || data.forma_pago_cliente || '—');
        })
        .fail(function () {
            console.error('[ClientePanel] Error al cargar resumen');
        });
}

// ─── DataTable: Presupuestos ──────────────────────────────────────────────────
function formatPresupuestoDetalle(d) {
    const val = (v) => (v !== null && v !== undefined && v !== '') ? v : '<span class="text-muted">-</span>';
    const fec = (v) => v ? formatoFechaEuropeo(v) : val(null);

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
                    <div class="col-md-4">

                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-file-text me-2"></i>Datos del Presupuesto
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-hash me-1"></i>ID:</strong> ${val(d.id_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-file-earmark-code me-1"></i>Número:</strong> ${val(d.numero_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-calendar-event me-1"></i>F. Presupuesto:</strong> ${fec(d.fecha_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-calendar-check me-1"></i>F. Validez:</strong> ${fec(d.fecha_validez_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-percent me-1"></i>Descuento (%):</strong>
                                <span class="badge bg-success">${parseFloat(d.descuento_presupuesto || 0).toFixed(2)}%</span>
                            </p>
                        </div>

                        <h6 class="text-success border-bottom pb-2 mb-3">
                            <i class="bi bi-geo-alt me-2"></i>Datos del Evento
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-pin-map me-1"></i>Ubicación:</strong></p>
                            <p class="ms-3 text-muted small" style="word-break:break-word">${val(d.direccion_completa_evento_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-calendar3 me-1"></i>F. Inicio:</strong> ${fec(d.fecha_inicio_evento_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-calendar3 me-1"></i>F. Fin:</strong> ${fec(d.fecha_fin_evento_presupuesto)}</p>
                        </div>

                        <h6 class="text-info border-bottom pb-2 mb-3">
                            <i class="bi bi-person me-2"></i>Datos del Cliente
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-building me-1"></i>Dirección:</strong></p>
                            <p class="ms-3 text-muted small" style="word-break:break-word">${val(d.direccion_completa_cliente)}</p>
                            <p class="mb-1"><strong><i class="bi bi-receipt me-1"></i>Dir. Facturación:</strong></p>
                            <p class="ms-3 text-muted small" style="word-break:break-word">${val(d.direccion_facturacion_completa_cliente)}</p>
                        </div>
                    </div>

                    <!-- ========== COLUMNA 2 ========== -->
                    <div class="col-md-4">

                        <h6 class="text-warning border-bottom pb-2 mb-3">
                            <i class="bi bi-chat-square-text me-2"></i>Observaciones
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-file-text me-1"></i>Obs. Cabecera:</strong></p>
                            <p class="ms-3 text-muted small" style="word-break:break-word">${val(d.observaciones_cabecera_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-file-text me-1"></i>Obs. Pie:</strong></p>
                            <p class="ms-3 text-muted small" style="word-break:break-word">${val(d.observaciones_pie_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-lock me-1"></i>Obs. Internas:</strong></p>
                            <p class="ms-3 text-muted small" style="word-break:break-word">${val(d.observaciones_internas_presupuesto)}</p>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3">
                            <i class="bi bi-person-lines-fill me-2"></i>Contacto del Cliente
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-person-badge me-1"></i>Nombre:</strong> ${val(d.nombre_completo_contacto)}</p>
                        </div>

                        <h6 class="text-dark border-bottom pb-2 mb-3">
                            <i class="bi bi-telephone me-2"></i>Método de Contacto
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-envelope me-1"></i>Método:</strong> ${val(d.nombre_metodo_contacto)}</p>
                        </div>
                    </div>

                    <!-- ========== COLUMNA 3 ========== -->
                    <div class="col-md-4">

                        <h6 class="text-danger border-bottom pb-2 mb-3">
                            <i class="bi bi-flag me-2"></i>Estado del Presupuesto
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-bookmark me-1"></i>Estado:</strong>
                                <span class="badge" style="background-color:${d.color_estado_ppto || '#6c757d'}">
                                    ${val(d.nombre_estado_ppto)}
                                </span>
                            </p>
                        </div>

                        <h6 class="text-success border-bottom pb-2 mb-3">
                            <i class="bi bi-credit-card me-2"></i>Forma de Pago
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-tag me-1"></i>Nombre:</strong> ${val(d.nombre_pago)}</p>
                            <p class="mb-1"><strong><i class="bi bi-percent me-1"></i>% Anticipo:</strong> ${val(d.porcentaje_anticipo_pago)}</p>
                            <p class="mb-1"><strong><i class="bi bi-percent me-1"></i>% Final:</strong> ${val(d.porcentaje_final_pago)}</p>
                            <p class="mb-1"><strong><i class="bi bi-cash me-1"></i>Tipo Pago:</strong> ${val(d.tipo_pago_presupuesto)}</p>
                        </div>

                        <h6 class="text-muted border-bottom pb-2 mb-3">
                            <i class="bi bi-info-circle me-2"></i>Control
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-calendar-plus me-1"></i>Creado:</strong> ${fec(d.created_at_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-calendar-event me-1"></i>Actualizado:</strong> ${fec(d.updated_at_presupuesto)}</p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    `;
}

function inicializarTablaPresupuestos() {
    tablaPresupuestos = $('#tblPresupuestosCliente').DataTable({
        ajax: {
            url: CTRL_PPTO + '?op=listar_por_cliente',
            type: 'POST',
            data: { id_cliente: ID_CLIENTE },
            dataSrc: 'data'
        },
        columns: [
            {
                data: null,
                defaultContent: '',
                className: 'details-control text-center',
                orderable: false,
                searchable: false
            },
            { data: 'numero_presupuesto' },
            { data: 'nombre_evento_presupuesto' },
            { data: 'fecha_inicio_evento_presupuesto' },
            { data: 'fecha_fin_evento_presupuesto' },
            {
                data: null,
                render: function (data, type, row) {
                    const color = row.color_estado_ppto || '#6c757d';
                    return '<span class="badge" style="background-color:' + color + '">' +
                           htmlEsc(row.nombre_estado_ppto || '—') + '</span>';
                }
            },
            {
                data: 'total_presupuesto',
                render: function (d) { return formatEuro(d); }
            },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    return '<a href="../Presupuesto/formularioPresupuesto.php?modo=editar&id=' +
                           row.id_presupuesto +
                           '" class="btn btn-sm btn-outline-primary" title="Abrir presupuesto">' +
                           '<i class="bi bi-box-arrow-up-right"></i></a>';
                }
            }
        ],
        columnDefs: [
            { targets: 0, width: '3%' }
        ],
        language: DT_LANG,
        responsive: true,
        order: [[1, 'desc']],
        drawCallback: function () {
            $('#badge-presupuestos').text(this.api().page.info().recordsTotal);
        }
    });

    // Child-row: expandir/contraer al hacer click en la celda details-control
    $('#tblPresupuestosCliente tbody').on('click', 'td.details-control', function () {
        var tr  = $(this).closest('tr');
        var row = tablaPresupuestos.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(formatPresupuestoDetalle(row.data())).show();
            tr.addClass('shown');
        }
    });
}

// ─── DataTable: Facturas ──────────────────────────────────────────────────────
const TIPO_BADGE = {
    'factura_anticipo':      { label: 'Anticipo',  cls: 'bg-success' },
    'factura_final':         { label: 'Final',     cls: 'bg-primary' },
    'factura_proforma':      { label: 'Proforma',  cls: 'bg-warning text-dark' },
    'factura_rectificativa': { label: 'Abono',     cls: 'bg-danger' },
};

function formatFacturaDetalle(d) {
    const val = (v) => (v !== null && v !== undefined && v !== '') ? v : '<span class="text-muted">-</span>';
    const fec = (v) => v ? formatoFechaEuropeo(v) : val(null);
    const eur = (v) => formatEuro(parseFloat(v) || 0);
    const esProforma  = d.tipo_documento_ppto === 'factura_proforma';
    const headerCls   = esProforma ? 'bg-warning text-dark' : 'bg-primary text-white';
    const titulo      = esProforma ? 'Detalles del Documento (Proforma)' : 'Detalles de la Factura';
    const iconoCls    = esProforma ? 'bi-file-earmark-text' : 'bi-receipt-cutoff';

    const bannerProforma = esProforma ? `
        <div class="alert alert-warning py-2 px-3 mb-3">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Documento sin validez fiscal.</strong> La proforma no genera obligación de pago ni puede utilizarse como justificante contable.
        </div>` : '';

    const bloqueOrigen = d.numero_documento_origen ? `
        <h6 class="text-danger border-bottom pb-2 mb-3">
            <i class="bi bi-arrow-return-left me-2"></i>Documento de origen
        </h6>
        <div class="mb-3">
            <p class="mb-1"><strong><i class="bi bi-hash me-1"></i>Nº origen:</strong>
                ${val(d.numero_documento_origen)}
                <span class="badge bg-secondary ms-1">${val(d.tipo_documento_origen)}</span>
            </p>
            <p class="mb-1"><strong><i class="bi bi-currency-euro me-1"></i>Importe origen:</strong> ${eur(d.total_documento_origen)}</p>
            ${d.motivo_abono_documento_ppto ? `<p class="mb-1"><strong><i class="bi bi-chat-text me-1"></i>Motivo:</strong> ${val(d.motivo_abono_documento_ppto)}</p>` : ''}
        </div>` : '';

    return `
        <div class="card border-primary mb-3">
            <div class="card-header ${headerCls}">
                <div class="d-flex align-items-center">
                    <i class="bi ${iconoCls} fs-3 me-2"></i>
                    <h5 class="card-title mb-0">${titulo}</h5>
                </div>
            </div>
            <div class="card-body p-3">
                ${bannerProforma}
                <div class="row">

                    <!-- ========== COLUMNA 1: Identificación ========== -->
                    <div class="col-md-4">

                        <h6 class="text-primary border-bottom pb-2 mb-3">
                            <i class="bi bi-file-earmark-code me-2"></i>Identificación
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-bookmark me-1"></i>Serie:</strong> ${val(d.serie_documento_ppto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-hash me-1"></i>Número:</strong> ${val(d.numero_documento_ppto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-calendar-event me-1"></i>F. emisión:</strong> ${fec(d.fecha_emision_documento_ppto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-calendar-check me-1"></i>F. generación:</strong> ${fec(d.fecha_generacion_documento_ppto)}</p>
                        </div>

                        <h6 class="text-success border-bottom pb-2 mb-3">
                            <i class="bi bi-file-text me-2"></i>Presupuesto vinculado
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-file-earmark-code me-1"></i>Número:</strong> ${val(d.numero_presupuesto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-calendar-event me-1"></i>Evento:</strong></p>
                            <p class="ms-3 text-muted small" style="word-break:break-word">${val(d.nombre_evento_presupuesto)}</p>
                        </div>

                        ${bloqueOrigen}

                    </div>

                    <!-- ========== COLUMNA 2: Desglose económico ========== -->
                    <div class="col-md-4">

                        <h6 class="text-warning border-bottom pb-2 mb-3">
                            <i class="bi bi-calculator me-2"></i>Desglose económico
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-currency-euro me-1"></i>Base imponible:</strong> ${eur(d.subtotal_documento_ppto)}</p>
                            <p class="mb-1"><strong><i class="bi bi-percent me-1"></i>IVA:</strong> ${eur(d.total_iva_documento_ppto)}</p>
                            <hr class="my-2">
                            <p class="mb-1 fw-bold fs-6"><strong><i class="bi bi-cash me-1"></i>Total:</strong> ${eur(d.total_documento_ppto)}</p>
                        </div>

                    </div>

                    <!-- ========== COLUMNA 3: Empresa + Observaciones ========== -->
                    <div class="col-md-4">

                        <h6 class="text-info border-bottom pb-2 mb-3">
                            <i class="bi bi-building me-2"></i>Empresa emisora
                        </h6>
                        <div class="mb-3">
                            <p class="mb-1"><strong><i class="bi bi-building me-1"></i>Nombre:</strong> ${val(d.nombre_comercial_empresa || d.nombre_empresa)}</p>
                            <p class="mb-1"><strong><i class="bi bi-card-text me-1"></i>NIF:</strong> ${val(d.nif_empresa)}</p>
                        </div>

                        <h6 class="text-secondary border-bottom pb-2 mb-3">
                            <i class="bi bi-chat-square-text me-2"></i>Observaciones
                        </h6>
                        <div class="mb-3">
                            <p class="ms-0 text-muted small" style="word-break:break-word">${val(d.observaciones_documento_ppto)}</p>
                        </div>

                    </div>

                </div>
            </div>
        </div>
    `;
}

function inicializarTablaFacturas() {
    tablaFacturas = $('#tblFacturasCliente').DataTable({
        ajax: {
            url: CTRL_DOC + '?op=listar_por_cliente',
            type: 'POST',
            data: { id_cliente: ID_CLIENTE },
            dataSrc: 'data'
        },
        columns: [
            {
                data: null,
                defaultContent: '',
                className: 'details-control text-center',
                orderable: false,
                searchable: false
            },
            {
                data: 'tipo_documento_ppto',
                render: function (d) {
                    const t = TIPO_BADGE[d] || { label: d, cls: 'bg-secondary' };
                    return '<span class="badge ' + t.cls + '">' + t.label + '</span>';
                }
            },
            { data: 'numero_documento_ppto' },
            { data: 'numero_presupuesto' },
            { data: 'fecha_emision_documento_ppto' },
            { data: 'total_documento_ppto', render: function (d) { return formatEuro(d); } },
            {
                data: null,
                orderable: false,
                render: function (data, type, row) {
                    if (!row.ruta_pdf_documento_ppto) return '—';
                    return '<a href="../../' + row.ruta_pdf_documento_ppto + '" target="_blank" ' +
                           'class="btn btn-sm btn-outline-secondary" title="Ver PDF">' +
                           '<i class="bi bi-file-earmark-pdf"></i></a>';
                }
            }
        ],
        columnDefs: [
            { targets: 0, width: '3%' }
        ],
        createdRow: function (row, data) {
            if (data.tipo_documento_ppto === 'factura_proforma') {
                $(row).addClass('fila-proforma');
            }
        },
        language: DT_LANG,
        responsive: true,
        order: [[4, 'desc']],
        drawCallback: function () {
            $('#badge-facturas').text(this.api().page.info().recordsTotal);
        }
    });

    // Child-row: expandir/contraer al hacer click en la celda details-control
    $('#tblFacturasCliente tbody').on('click', 'td.details-control', function () {
        var tr  = $(this).closest('tr');
        var row = tablaFacturas.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(formatFacturaDetalle(row.data())).show();
            tr.addClass('shown');
        }
    });
}

// ─── DataTable: Pagos ─────────────────────────────────────────────────────────
function inicializarTablaPagos() {
    tablaPagos = $('#tblPagosCliente').DataTable({
        ajax: {
            url: CTRL_PAGO + '?op=listar_por_cliente',
            type: 'POST',
            data: { id_cliente: ID_CLIENTE },
            dataSrc: 'data'
        },
        columns: [
            { data: 'numero_presupuesto' },
            { data: 'tipo_pago_ppto' },
            { data: 'importe_pago_ppto', render: function (d) { return formatEuro(d); } },
            { data: 'fecha_pago_ppto' },
            { data: 'nombre_metodo_pago' },
            { data: 'estado_pago_ppto' }
        ],
        language: DT_LANG,
        responsive: true,
        order: [[3, 'desc']],
        drawCallback: function () {
            $('#badge-pagos').text(this.api().page.info().recordsTotal);
        }
    });
}

// ─── DataTable: Contactos ─────────────────────────────────────────────────────
function inicializarTablaContactos() {
    tablaContactos = $('#tblContactosCliente').DataTable({
        ajax: {
            url: CTRL_CONT + '?op=listar&id_cliente=' + ID_CLIENTE,
            type: 'POST',
            dataSrc: 'data'
        },
        columns: [
            { data: null, defaultContent: '', className: 'details-control sorting_1 text-center', orderable: false, searchable: false },
            { data: 'nombre_contacto_cliente' },
            { data: 'apellidos_contacto_cliente' },
            { data: 'cargo_contacto_cliente' },
            { data: 'departamento_contacto_cliente' },
            { data: 'telefono_contacto_cliente' },
            { data: 'movil_contacto_cliente' },
            { data: 'email_contacto_cliente' },
            {
                data: 'principal_contacto_cliente',
                render: function (d) {
                    return d == 1
                        ? '<span class="badge bg-success">S\u00ed</span>'
                        : '<span class="badge bg-secondary">No</span>';
                }
            }
        ],
        columnDefs: [
            { targets: 0, width: '5%' }
        ],
        language: DT_LANG,
        responsive: true,
        order: [[1, 'asc']],
        drawCallback: function () {
            $('#badge-contactos').text(this.api().page.info().recordsTotal);
        }
    });

    // Click handler para expand/collapse del child-row
    $('#tblContactosCliente tbody').on('click', 'td.details-control', function () {
        var tr  = $(this).closest('tr');
        var row = tablaContactos.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(formatContactoDetalle(row.data())).show();
            tr.addClass('shown');
        }
    });
}

// ─── DataTable: Ubicaciones ───────────────────────────────────────────────────
function inicializarTablaUbicaciones() {
    tablaUbicaciones = $('#tblUbicacionesCliente').DataTable({
        ajax: {
            url: CTRL_UBIC + '?op=listar&id_cliente=' + ID_CLIENTE,
            type: 'POST',
            dataSrc: 'data'
        },
        columns: [
            { data: null, defaultContent: '', className: 'details-control sorting_1 text-center', orderable: false, searchable: false },
            { data: 'nombre_ubicacion' },
            { data: 'direccion_ubicacion' },
            { data: 'codigo_postal_ubicacion' },
            { data: 'poblacion_ubicacion' },
            { data: 'provincia_ubicacion' },
            { data: 'persona_contacto_ubicacion' },
            { data: 'telefono_contacto_ubicacion' },
            {
                data: 'es_principal_ubicacion',
                render: function (d) {
                    return d == 1
                        ? '<i class="bi bi-star-fill text-warning" title="Ubicaci\u00f3n Principal"></i>'
                        : '<i class="bi bi-star text-muted" title="Ubicaci\u00f3n Secundaria"></i>';
                }
            }
        ],
        columnDefs: [
            { targets: 0, width: '5%' }
        ],
        language: DT_LANG,
        responsive: true,
        order: [[1, 'asc']],
        drawCallback: function () {
            $('#badge-ubicaciones').text(this.api().page.info().recordsTotal);
        }
    });

    // Click handler para expand/collapse del child-row
    $('#tblUbicacionesCliente tbody').on('click', 'td.details-control', function () {
        var tr  = $(this).closest('tr');
        var row = tablaUbicaciones.row(tr);
        if (row.child.isShown()) {
            row.child.hide();
            tr.removeClass('shown');
        } else {
            row.child(formatUbicacionDetalle(row.data())).show();
            tr.addClass('shown');
        }
    });
}

// ─── Child-row: detalle completo de una ubicación ─────────────────────────────
function formatUbicacionDetalle(d) {
    var na = function (texto) { return '<span class="text-muted fst-italic">' + texto + '</span>'; };

    return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-geo-alt-fill fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles de la Ubicación</h5>
                </div>
            </div>
            <div class="card-body p-0 small" style="overflow: visible;">
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <table class="table table-borderless table-striped table-hover mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-pin-map me-2"></i>Nombre
                                    </th>
                                    <td class="pe-4">
                                        ${d.nombre_ubicacion || na('Sin nombre')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-house me-2"></i>Dirección
                                    </th>
                                    <td class="pe-4">
                                        ${d.direccion_ubicacion || na('Sin dirección')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-mailbox me-2"></i>Código Postal
                                    </th>
                                    <td class="pe-4">
                                        ${d.codigo_postal_ubicacion || na('Sin C.P.')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-building me-2"></i>Población
                                    </th>
                                    <td class="pe-4">
                                        ${d.poblacion_ubicacion || na('Sin población')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-map me-2"></i>Provincia
                                    </th>
                                    <td class="pe-4">
                                        ${d.provincia_ubicacion || na('Sin provincia')}
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
                                        <i class="bi bi-globe me-2"></i>País
                                    </th>
                                    <td class="pe-4">
                                        ${d.pais_ubicacion || na('Sin país')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-person me-2"></i>Persona Contacto
                                    </th>
                                    <td class="pe-4">
                                        ${d.persona_contacto_ubicacion || na('Sin contacto')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-telephone me-2"></i>Teléfono
                                    </th>
                                    <td class="pe-4">
                                        ${d.telefono_contacto_ubicacion || na('Sin teléfono')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-envelope me-2"></i>Email
                                    </th>
                                    <td class="pe-4">
                                        ${d.email_contacto_ubicacion ? `<a href="mailto:${htmlEsc(d.email_contacto_ubicacion)}" target="_blank">${htmlEsc(d.email_contacto_ubicacion)}</a>` : na('Sin email')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-star me-2"></i>Principal
                                    </th>
                                    <td class="pe-4">
                                        ${d.es_principal_ubicacion == 1 ? '<span class="badge bg-warning text-dark">Ubicación Principal</span>' : '<span class="badge bg-secondary">Ubicación Secundaria</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-chat-text me-2"></i>Observaciones
                                    </th>
                                    <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                        ${d.observaciones_ubicacion || na('Sin observaciones')}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 text-end">
                <small class="text-muted">Actualizado: ${d.updated_at_ubicacion ? formatoFechaEuropeo(d.updated_at_ubicacion) : 'Sin fecha de actualización'}</small>
            </div>
        </div>
    `;
}

// ─── Helpers ─────────────────────────────────────────────────────────────────
function formatEuro(valor) {
    if (valor === null || valor === undefined || valor === '') return '—';
    return Number(valor).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }) + '\u00a0€';
}

function htmlEsc(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}

function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return '\u2014';
    var partes = fechaString.split(/[- :]/);
    if (partes.length < 3) return fechaString;
    var anio = partes[0], mes = partes[1], dia = partes[2];
    var hora = partes[3] || '00', min = partes[4] || '00';
    return dia + '/' + mes + '/' + anio + ' ' + hora + ':' + min;
}

// ─── Child-row: detalle completo de un contacto ───────────────────────────────
function formatContactoDetalle(d) {
    var na = function (texto) { return '<span class="text-muted fst-italic">' + texto + '</span>'; };

    return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-fill fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles del Contacto</h5>
                </div>
            </div>
            <div class="card-body p-0 small" style="overflow: visible;">
                <div class="row">
                    <!-- Columna izquierda -->
                    <div class="col-md-6">
                        <table class="table table-borderless table-striped table-hover mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-person me-2"></i>Nombre completo
                                    </th>
                                    <td class="pe-4">
                                        ${(((d.nombre_contacto_cliente || '') + ' ' + (d.apellidos_contacto_cliente || '')).trim()) || na('Sin nombre')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-briefcase me-2"></i>Cargo
                                    </th>
                                    <td class="pe-4">
                                        ${d.cargo_contacto_cliente || na('Sin cargo')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-building me-2"></i>Departamento
                                    </th>
                                    <td class="pe-4">
                                        ${d.departamento_contacto_cliente || na('Sin departamento')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-telephone me-2"></i>Teléfono
                                    </th>
                                    <td class="pe-4">
                                        ${d.telefono_contacto_cliente || na('Sin teléfono')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-phone me-2"></i>Móvil
                                    </th>
                                    <td class="pe-4">
                                        ${d.movil_contacto_cliente || na('Sin móvil')}
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
                                        <i class="bi bi-envelope me-2"></i>Email
                                    </th>
                                    <td class="pe-4">
                                        ${d.email_contacto_cliente ? `<a href="mailto:${htmlEsc(d.email_contacto_cliente)}" target="_blank">${htmlEsc(d.email_contacto_cliente)}</a>` : na('Sin email')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-telephone-plus me-2"></i>Extensión
                                    </th>
                                    <td class="pe-4">
                                        ${d.extension_contacto_cliente || na('Sin extensión')}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-star me-2"></i>Principal
                                    </th>
                                    <td class="pe-4">
                                        ${d.principal_contacto_cliente == 1 ? '<span class="badge bg-warning text-dark">Contacto Principal</span>' : '<span class="badge bg-secondary">Contacto Secundario</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-chat-text me-2"></i>Observaciones
                                    </th>
                                    <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                        ${d.observaciones_contacto_cliente || na('Sin observaciones')}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 text-end">
                <small class="text-muted">Actualizado: ${d.updated_at_contacto_cliente ? formatoFechaEuropeo(d.updated_at_contacto_cliente) : 'Sin fecha de actualización'}</small>
            </div>
        </div>
    `;
}
