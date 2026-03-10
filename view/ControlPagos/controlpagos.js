/**
 * controlpagos.js
 * MDR ERP Manager — Control Global de Pagos
 * Vista: view/ControlPagos/index.php
 */

'use strict';

// ─── Estado global ────────────────────────────────────────────────────────────
let tablaControlPagos  = null;
let tablaDetalleActiva = null;
let filtrosActivos     = {};
let _idPresupuestoDetalle = null;

// ─── Utils ────────────────────────────────────────────────────────────────────
function formatEuro(valor) {
    if (valor === null || valor === undefined || valor === '') return '—';
    return Number(valor).toLocaleString('es-ES', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2,
    }) + ' €';
}

// ─── Document ready ───────────────────────────────────────────────────────────
$(document).ready(function () {
    inicializarTabla();
    cargarKPIs();
});

// ─── Inicializar DataTable principal ─────────────────────────────────────────
function inicializarTabla(filtros) {
    if (tablaControlPagos) {
        tablaControlPagos.destroy();
        $('#tblControlPagos tbody').empty();
    }

    const opBase          = filtros ? 'listar_filtrado' : 'listar';
    const dataPost        = filtros ? filtros : {};
    const urlController   = '../../controller/controlpagos.php?op=' + opBase;

    tablaControlPagos = $('#tblControlPagos').DataTable({
        ajax: {
            url  : urlController,
            type : 'POST',
            data : dataPost,
            dataSrc: 'data',
            error: function (xhr, error, code) {
                console.error('Error AJAX Control Pagos:', error, code);
                Swal.fire('Error', 'No se pudo cargar la tabla de pagos.', 'error');
            },
        },
        columns: [
            { data: 'numero_presupuesto' },
            { data: 'nombre_completo_cliente' },
            { data: 'evento' },
            { data: 'total_presupuesto',   className: 'text-end' },
            { data: 'total_pagado',        className: 'text-end' },
            { data: 'total_conciliado',    className: 'text-end' },
            { data: 'saldo_pendiente',     className: 'text-end' },
            { data: 'tipos_documentos',    orderable: false },
            { data: 'ultima_factura' },
            { data: 'opciones',            orderable: false, className: 'text-center' },
        ],
        scrollX       : true,
        scrollCollapse: true,
        layout: {
            bottomEnd: {
                paging: {
                    firstLast    : true,
                    numbers      : false,
                    previousNext : true,
                },
            },
        },
        language: {
            paginate: {
                first:    '<i class="bi bi-chevron-double-left"></i>',
                last:     '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next:     '<i class="bi bi-chevron-compact-right"></i>',
            },
        },
        responsive  : true,
        order       : [[2, 'asc']],
        pageLength  : 25,
        drawCallback: function () {
            // Reinicializar tooltips Bootstrap tras redibujado
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
    });
}

// ─── Cargar KPIs ─────────────────────────────────────────────────────────────
function cargarKPIs() {
    $.post('../../controller/controlpagos.php?op=resumen_global')
        .done(function (res) {
            if (!res.success || !res.data) return;
            const d = res.data;

            $('#kpi-total').text(formatEuro(d.suma_total_global));
            $('#kpi-total-sub').text((d.total_presupuestos || 0) + ' presupuestos aprobados');

            $('#kpi-cobrado').text(formatEuro(d.suma_total_pagado));
            $('#kpi-cobrado-sub').text((d.presupuestos_pagados_completo || 0) + ' pagados por completo');
            $('#kpi-conciliado').text(formatEuro(d.suma_total_conciliado));

            $('#kpi-pendiente').text(formatEuro(d.suma_total_pendiente));
            $('#kpi-pendiente-sub').text(
                (d.presupuestos_pago_parcial || 0) + ' pago parcial · ' +
                (d.presupuestos_sin_pagos || 0) + ' sin pago'
            );

            $('#kpi-porcentaje').text((d.porcentaje_global_pagado || 0) + ' %');
        })
        .fail(function () {
            console.warn('No se pudieron cargar los KPIs.');
        });
}

// ─── Ver detalle de pagos de un presupuesto ───────────────────────────────────
function verDetallePagos(idPresupuesto, numeroPppto) {
    _idPresupuestoDetalle = idPresupuesto;

    // Título del modal
    $('#modalDetalleTitulo').text(numeroPppto);

    // Link a presupuesto
    $('#detalleLinkPresupuesto').attr(
        'href',
        '../Presupuesto/formularioPresupuesto.php?id=' + idPresupuesto + '&tab=pagos'
    );

    // Resetear estado visual
    $('#detalleCargando').show();
    $('#detalleContenido').hide();
    $('#detalleSinPagos').hide();
    $('#detalleTotalPptoVal').text('—');
    $('#detalleTotalPagadoVal').text('—');
    $('#detalleSaldoPendienteVal').text('—');

    // Destruir DataTable anterior si existe
    if (tablaDetalleActiva) {
        tablaDetalleActiva.destroy();
        tablaDetalleActiva = null;
        $('#tblDetallePagosTbody').empty();
    }

    // Buscar la fila correspondiente en la tabla principal para datos financieros
    tablaControlPagos.rows().every(function () {
        const rowData = this.data();
        if (rowData.id_presupuesto == idPresupuesto) {
            $('#detalleTotalPptoVal').text(rowData._total_presupuesto_num
                ? formatEuro(rowData._total_presupuesto_num) : '—');
            $('#detalleTotalPagadoVal').text(rowData._total_pagado_num !== undefined
                ? formatEuro(rowData._total_pagado_num) : '—');
            $('#detalleSaldoPendienteVal').text(rowData._saldo_pendiente_num !== undefined
                ? formatEuro(rowData._saldo_pendiente_num) : '—');
        }
    });

    // Abrir modal
    const modal = new bootstrap.Modal(document.getElementById('modalDetallePagos'));
    modal.show();

    // Pedir datos al controller
    $.post('../../controller/controlpagos.php?op=detalle_pagos', {
        id_presupuesto: idPresupuesto,
    })
    .done(function (res) {
        $('#detalleCargando').hide();

        if (!res.success) {
            Swal.fire('Error', res.message || 'No se pudo cargar el detalle.', 'error');
            return;
        }

        if (!res.data || res.data.length === 0) {
            $('#detalleSinPagos').show();
            return;
        }

        // Construir tabla
        const tbody = $('#tblDetallePagosTbody').empty();
        res.data.forEach(function (p) {
            tbody.append(
                '<tr>' +
                '<td>' + p.fecha_pago + '</td>' +
                '<td>' + p.tipo + '</td>' +
                '<td class="text-end">' + p.importe + '</td>' +
                '<td>' + p.metodo + '</td>' +
                '<td>' + p.documento + '</td>' +
                '<td>' + p.estado + '</td>' +
                '<td class="small text-muted">' + p.referencia + '</td>' +
                '</tr>'
            );
        });

        tablaDetalleActiva = $('#tblDetallePagos').DataTable({
            paging   : false,
            searching: false,
            info     : false,
            ordering : true,
            order    : [[0, 'asc']],
        });

        $('#detalleContenido').show();
    })
    .fail(function () {
        $('#detalleCargando').hide();
        Swal.fire('Error', 'Error de comunicación al cargar el detalle.', 'error');
    });
}

// ─── Aplicar filtros ──────────────────────────────────────────────────────────
function aplicarFiltros() {
    filtrosActivos = {
        solo_pendientes    : $('#chkSoloPendientes').is(':checked') ? '1' : '0',
        fecha_evento_desde : $('#filtroFechaDesde').val() || '',
        fecha_evento_hasta : $('#filtroFechaHasta').val() || '',
    };
    inicializarTabla(filtrosActivos);
    cargarKPIs();
}

// ─── Limpiar filtros ──────────────────────────────────────────────────────────
function limpiarFiltros() {
    $('#chkSoloPendientes').prop('checked', false);
    $('#filtroFechaDesde').val('');
    $('#filtroFechaHasta').val('');
    filtrosActivos = {};
    inicializarTabla();
    cargarKPIs();
}

// ─── Recargar tabla (botón actualizar) ───────────────────────────────────────
function recargarTabla() {
    if (Object.keys(filtrosActivos).length > 0) {
        inicializarTabla(filtrosActivos);
    } else {
        if (tablaControlPagos) {
            tablaControlPagos.ajax.reload(null, false);
        }
    }
    cargarKPIs();
}
