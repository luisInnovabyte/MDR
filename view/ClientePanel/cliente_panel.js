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

    // Cargar cabecera y KPIs al entrar
    cargarResumen();
    cargarKPIs();

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
function inicializarTablaPresupuestos() {
    tablaPresupuestos = $('#tblPresupuestosCliente').DataTable({
        ajax: {
            url: CTRL_PPTO + '?op=listar_por_cliente',
            type: 'POST',
            data: { id_cliente: ID_CLIENTE },
            dataSrc: 'data'
        },
        columns: [
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
        language: DT_LANG,
        responsive: true,
        order: [[0, 'desc']]
    });
}

// ─── DataTable: Facturas ──────────────────────────────────────────────────────
const TIPO_BADGE = {
    'factura_anticipo':      { label: 'Anticipo',  cls: 'bg-success' },
    'factura_final':         { label: 'Final',     cls: 'bg-primary' },
    'factura_proforma':      { label: 'Proforma',  cls: 'bg-secondary' },
    'factura_rectificativa': { label: 'Abono',     cls: 'bg-danger' },
};

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
        language: DT_LANG,
        responsive: true,
        order: [[3, 'desc']]
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
        order: [[3, 'desc']]
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
        order: [[1, 'asc']]
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
        order: [[1, 'asc']]
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
