/**
 * factura_agrupada.js
 * MDR ERP Manager — Facturas Agrupadas
 * Vista: view/FacturasAgrupadas/index.php
 */

'use strict';

// ─── Estado global ────────────────────────────────────────────────────────────
let tablaFA        = null;
let wizardPaso     = 1;
let clienteSelId   = null;
let empresaSelId   = null;  // id_empresa real seleccionada en paso 2
let empresaBloqueada = null;  // {id_empresa, nombre_empresa, ...} o null
let pptosDisponibles = [];  // [{id, numero, evento, total_presupuesto, ...}]
let pptosValidados = [];    // IDs validados en paso 3

// ─── Helpers ─────────────────────────────────────────────────────────────────
const formatEuro = v =>
    (v === null || v === undefined || v === '')
        ? '—'
        : Number(v).toLocaleString('es-ES', { minimumFractionDigits: 2, maximumFractionDigits: 2 }) + ' €';

const ctrl   = path => '../../controller/' + path;
const ctrlFA = op   => ctrl('factura_agrupada.php?op=' + op);
const ctrlImp = op  => ctrl('impresion_factura_agrupada.php?op=' + op);

// ─── Document ready ───────────────────────────────────────────────────────────
$(document).ready(function () {
    inicializarTabla();
    cargarClientes();
    cargarKPIs();
});

// ═══════════════════════════════════════════════════════════════════════════════
// TABLA PRINCIPAL
// ═══════════════════════════════════════════════════════════════════════════════
function inicializarTabla() {
    if (tablaFA) { tablaFA.destroy(); $('#tblFacturasAgrupadas tbody').empty(); }

    tablaFA = $('#tblFacturasAgrupadas').DataTable({
        ajax: {
            url    : ctrlFA('listar'),
            type   : 'POST',
            dataSrc: 'data',
            error  : () => Swal.fire('Error', 'No se pudo cargar la tabla.', 'error'),
        },
        columns: [
            { data: 'numero_factura_agrupada' },
            {
                data: 'fecha_factura_agrupada',
                render: d => d ? new Date(d).toLocaleDateString('es-ES') : '—',
            },
            { data: 'nombre_cliente' },
            { data: 'nombre_empresa' },
            {
                data: 'total_presupuestos',
                className: 'text-center',
                render: d => {
                    if (!d) return '<span class="text-muted">—</span>';
                    return d.split(', ').map(n => `<span class="badge bg-secondary me-1">${n}</span>`).join('');
                },
            },
            {
                data: 'total_bruto_agrupada',
                className: 'text-end',
                render: d => formatEuro(d),
            },
            {
                data: 'total_a_cobrar_agrupada',
                className: 'text-end fw-bold',
                render: d => `<span style="color: var(--fa-naranja);">${formatEuro(d)}</span>`,
            },
            {
                data: 'is_abono_agrupada',
                className: 'text-center',
                render: d => d == 1
                    ? '<span class="badge badge-abono">Rectificativa</span>'
                    : '<span class="badge badge-agrupada">Factura</span>',
            },
            {
                data: 'id_factura_agrupada',
                className: 'text-center',
                orderable: false,
                render: (id, type, row) => {
                    const esAbono     = row.is_abono_agrupada == 1;
                    const estaAbonada = row.esta_abonada == 1;
                    let btns = `<button class="btn btn-sm btn-outline-info me-1" title="Ver detalle"
                                        onclick="verDetalle(${id})">
                                    <i class="fas fa-eye"></i>
                                </button>`;
                    // Si está ABONADA: botón para descargar el PDF de la rectificativa asociada
                    if (estaAbonada && row.id_abono_asociado) {
                        btns += `<button class="btn btn-sm btn-outline-warning me-1"
                                         title="PDF Rectificativa: ${row.numero_abono_asociado || ''}"
                                         onclick="generarPDF(${row.id_abono_asociado})">
                                     <i class="fas fa-file-pdf"></i> <small>RECT.</small>
                                 </button>`;
                    }
                    // Botón generar rectificativa: solo facturas normales activas (ni abono ni abonada)
                    if (!esAbono && !estaAbonada) {
                        btns += `<button class="btn btn-sm btn-outline-warning me-1" title="Generar rectificativa"
                                         onclick="abrirModalAbono(${id})">
                                     <i class="fas fa-undo"></i>
                                 </button>`;
                    }
                    return btns;
                },
            },
        ],
        language: {
            sProcessing   : 'Procesando...',
            sLengthMenu   : 'Mostrar _MENU_ registros',
            sZeroRecords  : 'No se encontraron resultados',
            sEmptyTable   : 'Ningún dato disponible en esta tabla',
            sInfo         : 'Mostrando _START_ a _END_ de _TOTAL_ registros',
            sInfoEmpty    : 'Mostrando 0 a 0 de 0 registros',
            sInfoFiltered : '(filtrado de _MAX_ registros totales)',
            sSearch       : 'Buscar:',
            sLoadingRecords: 'Cargando...',
            oPaginate: {
                sFirst   : '<i class="bi bi-chevron-double-left"></i>',
                sLast    : '<i class="bi bi-chevron-double-right"></i>',
                sPrevious: '<i class="bi bi-chevron-compact-left"></i>',
                sNext    : '<i class="bi bi-chevron-compact-right"></i>',
            },
        },
        responsive: true,
        order    : [[0, 'desc']],
        pageLength: 25,
    });
}

// ═══════════════════════════════════════════════════════════════════════════════
// KPIs
// ═══════════════════════════════════════════════════════════════════════════════
function cargarKPIs() {
    // Derivamos KPIs de los datos de la tabla una vez cargada
    tablaFA.on('draw.dt', function () {
        const rows = tablaFA.rows().data().toArray();
        let total     = rows.length;
        let importe   = 0;
        let nbPptos   = 0;
        let nbAbonos  = 0;

        rows.forEach(r => {
            importe  += parseFloat(r.total_bruto_agrupada || 0);
            nbPptos  += parseInt(r.total_presupuestos || 0);
            if (r.is_abono_agrupada == 1) nbAbonos++;
        });

        $('#kpi-total').text(total);
        $('#kpi-importe').text(formatEuro(importe));
        $('#kpi-presupuestos').text(nbPptos);
        $('#kpi-abonos').text(nbAbonos);
    });
}

// ═══════════════════════════════════════════════════════════════════════════════
// WIZARD — NUEVA FACTURA AGRUPADA
// ═══════════════════════════════════════════════════════════════════════════════
function abrirWizard() {
    wizardPaso       = 1;
    clienteSelId     = null;
    empresaSelId     = null;
    empresaBloqueada = null;
    pptosDisponibles = [];
    pptosValidados   = [];

    $('#sel-cliente').val('').trigger('change');
    $('#info-ppto-cliente').hide();
    $('#lista-presupuestos').html('');
    $('#alertaValidacion').hide();
    $('#listaErroresValidacion').html('');
    $('#alertaEmpresaBloqueada').addClass('d-none');
    $('#alertaSinEmpresas').addClass('d-none');
    $('#sel-empresa').html('<option value="">— Selecciona una empresa —</option>');
    $('#input-obs-fa').val('');
    $('#input-fecha-fa').val(hoy());

    _wizardMostrarPaso(1);
    $('#modalWizard').modal('show');
}

function hoy() {
    return new Date().toISOString().split('T')[0];
}

function _wizardMostrarPaso(paso) {
    wizardPaso = paso;

    // Ocultar todos
    $('#step-1, #step-2, #step-3, #step-4').hide();
    $(`#step-${paso}`).show();

    // Indicadores
    for (let i = 1; i <= 4; i++) {
        const $el = $(`#step-ind-${i}`);
        $el.removeClass('active done');
        if (i < paso) $el.addClass('done');
        if (i === paso) $el.addClass('active');
    }

    // Botones footer
    $('#btn-wiz-anterior').toggle(paso > 1);
    $('#btn-wiz-siguiente').toggle(paso < 4);
    $('#btn-wiz-guardar').toggle(paso === 4);
}

function wizardSiguiente() {
    if (wizardPaso === 1) return _wizardPaso1a2();
    if (wizardPaso === 2) return _wizardPaso2a3();
    if (wizardPaso === 3) return _wizardPaso3a4();
}

function wizardAnterior() {
    _wizardMostrarPaso(wizardPaso - 1);
}

// ── Paso 1 → 2 (carga presupuestos del cliente) ────────────────────────────────────────────
function _wizardPaso1a2() {
    clienteSelId = $('#sel-cliente').val();
    if (!clienteSelId) {
        Swal.fire('Aviso', 'Selecciona un cliente para continuar.', 'warning');
        return;
    }

    const $btn = $('#btn-wiz-siguiente').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span>Cargando...'
    );
    $('#lista-presupuestos').html(
        '<div class="text-center py-3"><div class="spinner-border text-warning"></div></div>'
    );

    $.post(ctrlFA('presupuestos_disponibles'), { id_cliente: clienteSelId })
        .done(resp => {
            $btn.prop('disabled', false).html('Siguiente <i class="fas fa-arrow-right ms-1"></i>');

            if (!resp.success || !resp.data?.length) {
                $('#lista-presupuestos').html(
                    '<div class="alert alert-warning">Este cliente no tiene presupuestos pendientes de facturar.</div>'
                );
                pptosDisponibles = [];
            } else {
                pptosDisponibles = resp.data;
                _renderListaPptos();
            }
            _wizardMostrarPaso(2);
        })
        .fail(() => {
            $btn.prop('disabled', false).html('Siguiente <i class="fas fa-arrow-right ms-1"></i>');
            Swal.fire('Error', 'No se pudieron cargar los presupuestos.', 'error');
        });
}

/**
 * Carga las empresas de facturación del cliente.
 * Si hay empresa bloqueada, la selecciona automáticamente y la deshabilita.
 * Si no, muestra el selector para que el usuario elija.
 */
function cargarEmpresasWizard(id_cliente) {
    const $btn = $('#btn-wiz-siguiente').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span>Cargando...'
    );

    $.post(ctrlFA('listar_empresas_facturacion'), { id_cliente })
        .done(resp => {
            $btn.prop('disabled', false).html('Siguiente <i class="fas fa-arrow-right ms-1"></i>');

            if (!resp.success) {
                Swal.fire('Error', resp.message || 'No se pudieron cargar las empresas.', 'error');
                return;
            }

            const empresas = resp.empresas_reales || [];
            empresaBloqueada = resp.empresa_bloqueada || null;

            // Resetear alertas
            $('#alertaEmpresaBloqueada').addClass('d-none');
            $('#alertaSinEmpresas').addClass('d-none');

            if (!empresas.length) {
                $('#alertaSinEmpresas').removeClass('d-none');
                $('#divSelEmpresa').hide();
                empresaSelId = null;
                _wizardMostrarPaso(2);
                return;
            }

            // Rellenar selector
            let opts = '<option value="">— Selecciona una empresa —</option>';
            empresas.forEach(e => {
                opts += `<option value="${e.id_empresa}">${e.nombre_empresa} (${e.nif_empresa || e.codigo_empresa})</option>`;
            });
            $('#sel-empresa').html(opts);

            if (empresaBloqueada) {
                // Auto-seleccionar y deshabilitar
                $('#sel-empresa').val(empresaBloqueada.id_empresa).prop('disabled', true);
                $('#txtEmpresaBloqueadaNombre').text(empresaBloqueada.nombre_empresa);
                $('#alertaEmpresaBloqueada').removeClass('d-none');
                $('#divSelEmpresa').show();
                empresaSelId = String(empresaBloqueada.id_empresa);
            } else {
                $('#sel-empresa').prop('disabled', false);
                $('#divSelEmpresa').show();
                empresaSelId = null;
            }

            _wizardMostrarPaso(2);
        })
        .fail(() => {
            $btn.prop('disabled', false).html('Siguiente <i class="fas fa-arrow-right ms-1"></i>');
            Swal.fire('Error', 'No se pudieron cargar las empresas.', 'error');
        });
}

// ── Paso 2 → 3 (valida empresa, carga presupuestos) ────────────────────────────
function _wizardPaso2a3() {
    const ids = _getIdsSeleccionados();
    if (!ids.length) {
        Swal.fire('Aviso', 'Selecciona al menos un presupuesto para continuar.', 'warning');
        return;
    }

    const $btn = $('#btn-wiz-siguiente').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span>Cargando...'
    );

    $.post(ctrlFA('detectar_empresa_para_seleccion'), { ids_presupuesto: ids })
        .done(resp => {
            $btn.prop('disabled', false).html('Siguiente <i class="fas fa-arrow-right ms-1"></i>');

            if (!resp.success) {
                Swal.fire('Error', resp.message || 'No se pudieron cargar las empresas.', 'error');
                return;
            }

            const empresas = resp.empresas_reales || [];
            empresaBloqueada = resp.empresa_bloqueada || null;

            // Resetear alertas
            $('#alertaEmpresaBloqueada').addClass('d-none');
            $('#alertaSinEmpresas').addClass('d-none');

            if (!empresas.length) {
                $('#alertaSinEmpresas').removeClass('d-none');
                $('#divSelEmpresa').hide();
                empresaSelId = null;
                _wizardMostrarPaso(3);
                return;
            }

            // Rellenar selector
            let opts = '<option value="">— Selecciona una empresa —</option>';
            empresas.forEach(e => {
                opts += `<option value="${e.id_empresa}">${e.nombre_empresa} (${e.nif_empresa || e.codigo_empresa})</option>`;
            });
            $('#sel-empresa').html(opts);

            if (empresaBloqueada) {
                $('#sel-empresa').val(empresaBloqueada.id_empresa).prop('disabled', true);
                $('#txtEmpresaBloqueadaNombre').text(empresaBloqueada.nombre_empresa);
                $('#alertaEmpresaBloqueada').removeClass('d-none');
                $('#divSelEmpresa').show();
                empresaSelId = String(empresaBloqueada.id_empresa);
            } else {
                $('#sel-empresa').prop('disabled', false);
                $('#divSelEmpresa').show();
                empresaSelId = null;
            }

            _wizardMostrarPaso(3);
        })
        .fail(() => {
            $btn.prop('disabled', false).html('Siguiente <i class="fas fa-arrow-right ms-1"></i>');
            Swal.fire('Error', 'No se pudieron cargar las empresas.', 'error');
        });
}

function _renderListaPptos() {
    let html = '';
    pptosDisponibles.forEach(p => {
        const total = formatEuro(p.total_con_iva ?? 0);
        html += `
        <div class="ppto-item" data-id="${p.id_presupuesto}" onclick="togglePpto(this)">
            <input type="checkbox" id="chk-ppto-${p.id_presupuesto}"
                   value="${p.id_presupuesto}" onclick="event.stopPropagation();"
                   onchange="togglePptoCheck(this)">
            <div class="ppto-num"># ${p.numero_presupuesto}</div>
            <div class="ppto-evento">${p.nombre_evento_presupuesto || '(Sin evento)'}</div>
            <div class="text-muted small me-2">${p.fecha_presupuesto ? new Date(p.fecha_presupuesto).toLocaleDateString('es-ES') : ''}</div>
            <div class="ppto-total">${total}</div>
        </div>`;
    });
    $('#lista-presupuestos').html(html || '<p class="text-muted text-center">Sin presupuestos disponibles.</p>');
}

function togglePpto(el) {
    const chk = $(el).find('input[type=checkbox]')[0];
    chk.checked = !chk.checked;
    $(el).toggleClass('selected', chk.checked);
    _actualizarResumenEstimado();
}

function togglePptoCheck(chk) {
    $(chk).closest('.ppto-item').toggleClass('selected', chk.checked);
    _actualizarResumenEstimado();
}

function seleccionarTodos() {
    $('#lista-presupuestos .ppto-item').addClass('selected').find('input[type=checkbox]').prop('checked', true);
    _actualizarResumenEstimado();
}

function deseleccionarTodos() {
    $('#lista-presupuestos .ppto-item').removeClass('selected').find('input[type=checkbox]').prop('checked', false);
    _actualizarResumenEstimado();
}

function _getIdsSeleccionados() {
    return $('#lista-presupuestos input[type=checkbox]:checked').map((i, el) => el.value).toArray();
}

function _actualizarResumenEstimado() {
    const ids = _getIdsSeleccionados();
    // Ocultar alerta previa
    $('#alertaValidacion').hide();
}

// ── Paso 3 → 4 (valida selección con el server) ─────────────────────────
function _wizardPaso3a4() {
    // Leer empresa del selector si no está bloqueada
    if (!empresaBloqueada) {
        empresaSelId = $('#sel-empresa').val();
    }
    if (!empresaSelId) {
        Swal.fire('Aviso', 'Selecciona una empresa emisora para continuar.', 'warning');
        return;
    }

    const ids = _getIdsSeleccionados();
    if (!ids.length) {
        Swal.fire('Aviso', 'No hay presupuestos seleccionados.', 'warning');
        return;
    }

    const $btn = $('#btn-wiz-siguiente').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span>Validando...'
    );

    $.post(ctrlFA('validar_seleccion'), { ids_presupuesto: ids, id_empresa_real: empresaSelId })
        .done(resp => {
            $btn.prop('disabled', false).html('Siguiente <i class="fas fa-arrow-right ms-1"></i>');

            if (!resp.success) {
                const errores = resp.errores || [resp.message || 'Error de validación'];
                Swal.fire({
                    title: 'Error de validación',
                    html: '<ul style="text-align:left">' + errores.map(e => `<li>${e}</li>`).join('') + '</ul>',
                    icon: 'error'
                });
                return;
            }

            pptosValidados = ids;
            _rellenarResumenPaso4(resp);
            _wizardMostrarPaso(4);
        })
        .fail(() => {
            $btn.prop('disabled', false).html('Siguiente <i class="fas fa-arrow-right ms-1"></i>');
            Swal.fire('Error', 'No se pudo validar la selección.', 'error');
        });
}

function _rellenarResumenPaso4(validacion) {
    // Lista de pptos seleccionados
    const seleccionados = pptosDisponibles.filter(p => pptosValidados.includes(String(p.id_presupuesto)));
    let html = '<h6 class="text-muted mb-2">Presupuestos incluidos:</h6><ul class="list-group list-group-flush mb-3">';
    let baseTotal = 0, ivaTotal = 0, anticiposTotal = 0;

    seleccionados.forEach(p => {
        const tot = parseFloat(p.total_con_iva ?? 0);
        baseTotal      += parseFloat(p.total_base_imponible   ?? 0);
        ivaTotal       += parseFloat(p.total_iva              ?? 0);
        anticiposTotal += parseFloat(p.total_anticipos_reales ?? 0);
        html += `<li class="list-group-item d-flex justify-content-between py-1">
                    <span><strong>#${p.numero_presupuesto}</strong> — ${p.nombre_evento_presupuesto || '(Sin evento)'}</span>
                    <span class="fw-bold">${formatEuro(tot)}</span>
                 </li>`;
    });
    html += '</ul>';
    $('#resumen-pptos').html(html);

    // Los totales reales vienen del server en resp.totales (si los devuelve)
    const totales = validacion.totales || {};
    const totalACobrar = baseTotal + ivaTotal - anticiposTotal;
    $('#res-base').text(formatEuro(totales.base           ?? baseTotal));
    $('#res-iva').text(formatEuro(totales.iva              ?? ivaTotal));
    $('#res-anticipos').text(formatEuro(totales.anticipos  ?? anticiposTotal));
    $('#res-total').text(formatEuro(totales.total_a_cobrar ?? totalACobrar));
}

// ── GUARDAR ───────────────────────────────────────────────────────────────────
function guardarFacturaAgrupada() {
    const fecha = $('#input-fecha-fa').val();
    const obs   = $('#input-obs-fa').val().trim();

    if (!fecha) {
        Swal.fire('Aviso', 'La fecha es obligatoria.', 'warning');
        return;
    }
    if (!pptosValidados.length) {
        Swal.fire('Aviso', 'No hay presupuestos seleccionados.', 'warning');
        return;
    }

    const $btn = $('#btn-wiz-guardar').prop('disabled', true).html(
        '<span class="spinner-border spinner-border-sm me-1"></span>Creando...'
    );

    $.post(ctrlFA('guardar'), {
        ids_presupuesto           : pptosValidados,
        fecha_factura_agrupada    : fecha,
        observaciones             : obs,
        id_empresa_real           : empresaSelId,
        id_cliente                : clienteSelId,
    })
    .done(resp => {
        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Crear Factura');
        if (resp.success) {
            $('#modalWizard').modal('hide');
            tablaFA.ajax.reload();

            Swal.fire({
                title            : '¡Factura creada!',
                html             : `Número: <strong>${resp.numero}</strong>`,
                icon             : 'success',
                confirmButtonText: 'Cerrar',
                confirmButtonColor: '#6c757d',
            });
        } else {
            Swal.fire('Error', resp.message || 'No se pudo crear la factura agrupada.', 'error');
        }
    })
    .fail(() => {
        $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Crear Factura');
        Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
    });
}

// ═══════════════════════════════════════════════════════════════════════════════
// PDF — GENERAR / REGENERAR / DESCARGAR
// ═══════════════════════════════════════════════════════════════════════════════
function generarPDF(id) {
    _ejecutarGenerarPDF(id, 'generar');
}

function regenerarPDF(id) {
    _ejecutarGenerarPDF(id, 'regenerar');
}

function _ejecutarGenerarPDF(id, op) {
    const toast = Swal.fire({
        title  : 'Generando PDF...',
        text   : 'Por favor espera.',
        allowOutsideClick: false,
        didOpen: () => Swal.showLoading(),
    });

    $.post(ctrlImp(op), { id_factura_agrupada: id })
        .done(resp => {
            Swal.close();
            if (resp.success) {
                Swal.fire({
                    title            : 'PDF generado',
                    text             : 'Factura ' + resp.numero,
                    icon             : 'success',
                    showCancelButton : true,
                    confirmButtonText: '<i class="fas fa-eye me-1"></i>Ver PDF',
                    confirmButtonColor: '#d35400',
                    cancelButtonText : 'Cerrar',
                }).then(r => {
                    if (r.isConfirmed) descargarPDF(id);
                });
                tablaFA.ajax.reload(null, false);
            } else {
                Swal.fire('Error', resp.message || 'No se pudo generar el PDF.', 'error');
            }
        })
        .fail(() => {
            Swal.close();
            Swal.fire('Error', 'Error de comunicación al generar el PDF.', 'error');
        });
}

function descargarPDF(id) {
    window.open(ctrlImp('descargar') + '&id=' + id + '&ts=' + Date.now(), '_blank');
}

// ═══════════════════════════════════════════════════════════════════════════════
// VER DETALLE
// ═══════════════════════════════════════════════════════════════════════════════
function verDetalle(id) {
    $('#cuerpo-detalle').html(
        '<div class="text-center py-4"><div class="spinner-border text-warning"></div></div>'
    );
    $('#modalDetalle').modal('show');

    $.post(ctrlFA('mostrar'), { id_factura_agrupada: id })
        .done(resp => {
            if (!resp || !resp.success || !resp.cabecera) {
                $('#cuerpo-detalle').html('<div class="alert alert-danger">No se encontró la factura.</div>');
                return;
            }
            const fa    = resp.cabecera;
            const pptos = resp.presupuestos || [];

            let html = `
                <div class="mb-3">
                    <h6 class="text-muted">Nº: <strong style="color:var(--fa-naranja)">${fa.numero_factura_agrupada}</strong>
                        &rarr; ${fa.nombre_cliente}</h6>
                    <p class="mb-1 small text-muted">Fecha: ${fa.fecha_factura_agrupada || '—'}
                        | Total: <strong>${formatEuro(fa.total_bruto_agrupada)}</strong>
                        | A cobrar: <strong>${formatEuro(fa.total_a_cobrar_agrupada)}</strong>
                    </p>
                </div>
                <h6 class="mb-2">Presupuestos incluidos (${pptos.length}):</h6>
                <ul class="list-group">`;
            pptos.forEach(p => {
                html += `<li class="list-group-item d-flex justify-content-between">
                            <span>#${p.numero_presupuesto} — ${p.nombre_evento_presupuesto || '(Sin evento)'}</span>
                            <span>${p.fecha_presupuesto ? new Date(p.fecha_presupuesto).toLocaleDateString('es-ES') : ''}</span>
                         </li>`;
            });
            html += '</ul>';
            $('#cuerpo-detalle').html(html);

            // Botón PDF en el footer del modal
            let pdfBtn = '';
            if (fa.pdf_path_agrupada) {
                pdfBtn = `<button class="btn btn-sm" style="background:var(--fa-naranja);color:#fff;"
                                  onclick="descargarPDF(${id})">
                              <i class="fas fa-file-pdf me-1"></i>Descargar PDF
                          </button>
                          <button class="btn btn-sm btn-outline-secondary ms-1"
                                  onclick="regenerarPDF(${id})">
                              <i class="fas fa-sync-alt me-1"></i>Regenerar
                          </button>`;
            } else {
                pdfBtn = `<button class="btn btn-sm" style="background:var(--fa-naranja);color:#fff;"
                                  onclick="generarPDF(${id})">
                              <i class="fas fa-file-pdf me-1"></i>Generar PDF
                          </button>`;
            }
            $('#footer-pdf-detalle').html(pdfBtn);
        })
        .fail(() => $('#cuerpo-detalle').html('<div class="alert alert-danger">Error al cargar los datos.</div>'));
}

// ═══════════════════════════════════════════════════════════════════════════════
// ABONO / RECTIFICATIVA
// ═══════════════════════════════════════════════════════════════════════════════
function abrirModalAbono(id) {
    $('#abono-id-fa').val(id);
    $('#abono-motivo').val('');
    $('#modalAbono').modal('show');
}

function confirmarAbono() {
    const id     = $('#abono-id-fa').val();
    const motivo = $('#abono-motivo').val().trim();
    if (!motivo) {
        Swal.fire('Aviso', 'El motivo de rectificación es obligatorio.', 'warning');
        return;
    }

    $.post(ctrlFA('generar_abono'), { id_factura_agrupada: id, motivo: motivo })
        .done(resp => {
            if (resp.success) {
                $('#modalAbono').modal('hide');
                tablaFA.ajax.reload();
                Swal.fire({
                    title            : 'Rectificativa creada',
                    text             : 'Número: ' + resp.numero,
                    icon             : 'success',
                    showCancelButton : true,
                    confirmButtonText: 'Generar PDF',
                    confirmButtonColor: '#d35400',
                }).then(r => { if (r.isConfirmed) generarPDF(resp.id); });
            } else {
                Swal.fire('Error', resp.message || 'No se pudo crear la rectificativa.', 'error');
            }
        })
        .fail(() => Swal.fire('Error', 'Error de comunicación.', 'error'));
}

// ═══════════════════════════════════════════════════════════════════════════════
// DESACTIVAR
// ═══════════════════════════════════════════════════════════════════════════════
function desactivarFA(id) {
    Swal.fire({
        title             : '¿Desactivar factura agrupada?',
        text              : 'Esta acción no borra los presupuestos asociados.',
        icon              : 'warning',
        showCancelButton  : true,
        confirmButtonColor: '#d33',
        cancelButtonColor : '#3085d6',
        confirmButtonText : 'Sí, desactivar',
        cancelButtonText  : 'Cancelar',
    }).then(result => {
        if (!result.isConfirmed) return;

        $.post(ctrlFA('desactivar'), { id_factura_agrupada: id })
            .done(resp => {
                if (resp.success) {
                    Swal.fire('Desactivada', resp.message, 'success');
                    tablaFA.ajax.reload();
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            })
            .fail(() => Swal.fire('Error', 'Error de comunicación.', 'error'));
    });
}

// ═══════════════════════════════════════════════════════════════════════════════
// CARGA CLIENTES (SELECT en wizard paso 1)
// ═══════════════════════════════════════════════════════════════════════════════
function cargarClientes() {
    $.post('../../controller/cliente.php?op=listar_disponibles')
        .done(resp => {
            const $sel = $('#sel-cliente');
            if (resp.data && resp.data.length) {
                resp.data.forEach(c => {
                    $sel.append(
                        $('<option>').val(c.id_cliente).text(c.nombre_cliente)
                    );
                });
            }

            // Inicializar Select2 si está disponible
            if (typeof $.fn.select2 === 'function') {
                $sel.select2({
                    dropdownParent : $('#modalWizard'),
                    placeholder    : '— Selecciona un cliente —',
                    allowClear     : true,
                    width          : '100%',
                });
            }
        })
        .fail(() => console.warn('No se pudieron cargar los clientes.'));
}
