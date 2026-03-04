/**
 * pagos_presupuesto.js
 * Lógica frontend para las pestañas Documentos y Pagos del formulario de presupuesto.
 * Dependencias: jQuery, Bootstrap 5, DataTables, SweetAlert2
 */

'use strict';

/* ============================================================
   VARIABLES GLOBALES
   ============================================================ */
let tblDocumentos        = null;
let tblPagos             = null;
let tabDocumentosInited  = false;
let tabPagosInited       = false;

/* ============================================================
   INICIALIZACIÓN
   ============================================================ */
$(document).ready(function () {

    const idPresupuesto = obtenerIdPresupuesto();

    // Habilitar tabs si ya tenemos ID (modo edición: el parámetro ?id= está en la URL
    // aunque el AJAX de cargarDatosPresupuesto aún no haya terminado)
    if (idPresupuesto) {
        habilitarTabsSecundarios();
    }

    // Lazy-init de DataTables al mostrar cada tab
    $('#tab-documentos-btn').on('shown.bs.tab', function () {
        if (!tabDocumentosInited) {
            initTabDocumentos();
            tabDocumentosInited = true;
        }
    });

    $('#tab-pagos-btn').on('shown.bs.tab', function () {
        if (!tabPagosInited) {
            initTabPagos();
            actualizarResumenFinanciero();
            tabPagosInited = true;
        }
    });

    // Formulario: Registrar Pago — submit
    $('#frmRegistrarPago').on('submit', function (e) {
        e.preventDefault();
        if (!validarFormularioPago()) return;
        guardarPago();
    });

    // Formulario: Abonar Factura — submit
    $('#frmAbonarFactura').on('submit', function (e) {
        e.preventDefault();
        if (!validarFormularioAbono()) return;
        procesarAbono();
    });

    // Cambio de tipo de pago → mostrar/ocultar sección tipo documento
    $('#pago_tipo_pago_ppto').on('change', function () {
        actualizarSeccionesPagoModal();
    });

    // Cambio de checkbox "generar factura"
    $('#chkGenerarFactura').on('change', function () {
        actualizarSeccionesFacturaModal();
    });

    // Cambio de empresa → verificar si está bloqueada
    $('#pago_id_empresa_factura').on('change', function () {
        verificarEmpresaSeleccionada();
    });

    // Cambio de tipo de documento → mostrar alerta proforma
    $('input[name="tipo_documento_generar"]').on('change', function () {
        const esProforma = $(this).val() === 'factura_proforma';
        $('#alertaProformaInfo').toggleClass('d-none', !esProforma);
    });

    // Cálculo de porcentaje al cambiar importe
    $('#pago_importe_pago_ppto').on('input', calcularPorcentajePago);

    // Selector empresa en modal proforma
    $('#selectEmpresaProforma').on('change', function () {
        const selOpt = $(this).find(':selected');
        const bloqueada = selOpt.data('bloqueada') == 1;
        $('#alertaEmpresaBloqueadaProforma').toggleClass('d-none', !bloqueada);
        $('#btnConfirmarProforma').prop('disabled', bloqueada || !$(this).val());
    });

    // Exponer funciones públicas (necesarias para los onclick del HTML generado por PHP)
    window.mostrarDocumento    = mostrarDocumento;
    window.abonarFactura       = abonarFactura;
    window.abonarDocumento     = abonarFactura;             // ← alias: controller genera abonarDocumento(id)
    window.repetirProforma     = repetirProforma;
    window.desactivarDocumento = desactivarDocumento;
    window.editarPago          = editarPago;
    window.mostrarPago         = function(id) { abrirModalRegistrarPago(id); }; // ← alias: controller genera mostrarPago(id)
    window.anularPago          = anularPago;
    window.conciliarPago       = conciliarPago;

});

/* ============================================================
   UTILIDADES GENERALES
   ============================================================ */

function obtenerIdPresupuesto() {
    // Primero el campo oculto (rellenado por el AJAX de cargarDatosPresupuesto)
    const fromField = $('#id_presupuesto').val();
    if (fromField) return fromField;
    // Fallback: ?id= de la URL — disponible en el primer ciclo, antes de que
    // el AJAX de edición termine y rellene el campo oculto
    return new URLSearchParams(window.location.search).get('id') || null;
}

function habilitarTabsSecundarios() {
    $('#tab-documentos-btn, #tab-pagos-btn')
        .prop('disabled', false)
        .removeClass('disabled')
        .attr('aria-disabled', 'false');
}

/**
 * Habilita los tabs secundarios después de guardar el presupuesto.
 * Llamado por formularioPresupuesto.js cuando se obtiene un ID.
 */
function onPresupuestoGuardado(idPresupuesto) {
    if (idPresupuesto) {
        habilitarTabsSecundarios();
    }
}
window.onPresupuestoGuardado = onPresupuestoGuardado;

function formatMoneda(valor) {
    if (valor === null || valor === undefined || valor === '') return '—';
    const num = parseFloat(valor);
    return isNaN(num) ? '—' : num.toLocaleString('es-ES', { style: 'currency', currency: 'EUR' });
}

/* ============================================================
   TAB 2: DOCUMENTOS
   ============================================================ */

function initTabDocumentos() {
    const idPresupuesto = obtenerIdPresupuesto();
    if (!idPresupuesto) return;

    tblDocumentos = $('#tblDocumentos').DataTable({
        destroy: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: '../../controller/documento_presupuesto.php?op=listar',
            type: 'POST',
            data: { id_presupuesto: idPresupuesto },
            dataSrc: 'data'
        },
        columns: [
            { data: 'numero_documento_ppto',   title: 'Nº Documento' },
            {
                data: 'tipo_documento_ppto',
                title: 'Tipo',
                render: function (data) {
                    const map = {
                        'factura_proforma':  '<span class="badge bg-primary">Proforma</span>',
                        'factura_anticipo':  '<span class="badge bg-success">Anticipo</span>',
                        'factura_final':     '<span class="badge bg-dark">Final</span>',
                        'factura_abono':     '<span class="badge bg-warning text-dark">Abono</span>',
                        'parte_trabajo':     '<span class="badge bg-info text-dark">Parte Trabajo</span>'
                    };
                    return map[data] || '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            { data: 'nombre_empresa',          title: 'Empresa' },
            {
                data: 'total_documento_ppto',
                title: 'Total',
                render: function (data) { return formatMoneda(data); },
                className: 'text-end'
            },
            { data: 'fecha_emision_documento_ppto', title: 'Fecha emisión' },
            {
                data: 'activo_documento_ppto',
                title: 'Estado',
                render: function (data) {
                    return data == 1
                        ? '<span class="badge bg-success">Activo</span>'
                        : '<span class="badge bg-danger">Anulado</span>';
                }
            },
            { data: 'opciones', title: 'Opciones', orderable: false, searchable: false }
        ],
        language: {
            emptyTable:    "No hay documentos para este presupuesto",
            info:          "Mostrando _START_ a _END_ de _TOTAL_ documentos",
            infoEmpty:     "Sin documentos",
            infoFiltered:  "(filtrado de _MAX_ documentos totales)",
            lengthMenu:    "Mostrar _MENU_ documentos",
            loadingRecords:"Cargando...",
            processing:    "Procesando...",
            search:        "Buscar:",
            zeroRecords:   "No se encontraron documentos",
            paginate: {
                first:    '<i class="fas fa-angle-double-left"></i>',
                last:     '<i class="fas fa-angle-double-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>',
                next:     '<i class="fas fa-angle-right"></i>'
            }
        },
        order: [[4, 'desc']],
        responsive: true,
        pageLength: 10,
        drawCallback: function () {
            // Activar tooltips en botones de opciones
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

function recargarDocumentos() {
    if (tblDocumentos) {
        tblDocumentos.ajax.reload(null, false);
    }
}

/* ---- Acciones sobre documentos (llamadas desde HTML del controller) ---- */

function mostrarDocumento(rutaPdf) {
    if (!rutaPdf) {
        Swal.fire('Sin PDF', 'Este documento no tiene PDF generado.', 'info');
        return;
    }
    window.open('../../' + rutaPdf, '_blank');
}

function abonarFactura(idDocumento) {
    const idPresupuesto = obtenerIdPresupuesto();

    $.post('../../controller/documento_presupuesto.php?op=mostrar', { id_documento_ppto: idDocumento })
        .done(function (data) {
            if (!data || data.success === false) {
                Swal.fire('Error', 'No se pudo cargar el documento.', 'error');
                return;
            }

            // Verificar si puede abonarse
            $.post('../../controller/documento_presupuesto.php?op=verificar_puede_abonar', { id_documento_ppto: idDocumento })
                .done(function (res) {
                    if (!res.puede_abonar) {
                        Swal.fire('No se puede abonar', res.mensaje || 'Este documento no admite abono.', 'warning');
                        return;
                    }

                    // Rellenar modal
                    $('#abono_id_documento_origen').val(data.id_documento_ppto);
                    $('#abono_id_presupuesto').val(idPresupuesto);
                    $('#abono_id_empresa').val(data.id_empresa);
                    $('#abono_numero_documento').text(data.numero_documento_ppto || '—');
                    $('#abono_nombre_empresa').text(data.nombre_empresa || '—');
                    $('#abono_total_documento').text(formatMoneda(data.total_documento_ppto));

                    const tipoBadge = {
                        'factura_anticipo': '<span class="badge bg-success">Anticipo</span>',
                        'factura_final':    '<span class="badge bg-dark">Final</span>'
                    };
                    $('#abono_badge_tipo').html(tipoBadge[data.tipo_documento_ppto] || data.tipo_documento_ppto);

                    // Limpiar y mostrar modal
                    $('#abono_motivo_abono').val('').removeClass('is-invalid');
                    $('#chkConfirmarAbono').prop('checked', false).removeClass('is-invalid');
                    $('#frmAbonarFactura').removeClass('was-validated');

                    const modal = new bootstrap.Modal(document.getElementById('modalAbonarFactura'));
                    modal.show();
                })
                .fail(function () {
                    Swal.fire('Error', 'Error de comunicación al verificar el documento.', 'error');
                });
        })
        .fail(function () {
            Swal.fire('Error', 'Error de comunicación al cargar el documento.', 'error');
        });
}

function repetirProforma(idDocumento) {
    const idPresupuesto = obtenerIdPresupuesto();

    Swal.fire({
        title: '¿Repetir proforma?',
        text: 'Se generará una nueva copia de la proforma.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, repetir',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.post('../../controller/documento_presupuesto.php?op=repetir_proforma', {
            id_documento_ppto: idDocumento,
            id_presupuesto: idPresupuesto
        }).done(function (response) {
            if (response.success) {
                Swal.fire({
                    title: 'Proforma generada',
                    text: 'La proforma se ha generado correctamente.',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Ver PDF',
                    cancelButtonText: 'Cerrar'
                }).then(function (r) {
                    if (r.isConfirmed && response.ruta_pdf) {
                        window.open('../../' + response.ruta_pdf, '_blank');
                    }
                    recargarDocumentos();
                });
            } else {
                Swal.fire('Error', response.message || 'No se pudo repetir la proforma.', 'error');
            }
        }).fail(function () {
            Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
        });
    });
}

function desactivarDocumento(idDocumento) {
    Swal.fire({
        title: '¿Anular documento?',
        html: 'El documento quedará marcado como <strong>inactivo/anulado</strong>. Esta acción no se puede deshacer.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.post('../../controller/documento_presupuesto.php?op=desactivar', { id_documento_ppto: idDocumento })
            .done(function (response) {
                if (response.success) {
                    Swal.fire('Anulado', response.message, 'success');
                    recargarDocumentos();
                } else {
                    Swal.fire('Error', response.message || 'No se pudo anular el documento.', 'error');
                }
            })
            .fail(function () {
                Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
            });
    });
}

/* ---- Generar Proforma ---- */

function abrirModalGenerarProforma() {
    const idPresupuesto = obtenerIdPresupuesto();
    if (!idPresupuesto) {
        Swal.fire('Sin guardar', 'Debes guardar el presupuesto antes de generar una proforma.', 'warning');
        return;
    }

    // Cargar empresas disponibles via verificar_empresa_disponible
    $.post('../../controller/documento_presupuesto.php?op=verificar_empresa_disponible', { id_presupuesto: idPresupuesto })
        .done(function (response) {
            const $select = $('#selectEmpresaProforma').empty().append('<option value="">— Seleccionar empresa —</option>');
            $('#alertaEmpresaBloqueadaProforma').addClass('d-none');
            $('#btnConfirmarProforma').prop('disabled', true);

            if (response && response.empresas && response.empresas.length > 0) {
                response.empresas.forEach(function (emp) {
                    const label = emp.nombre_empresa + (emp.bloqueada == 1 ? ' 🔒 (ya facturada)' : '');
                    $select.append(
                        $('<option>', {
                            value: emp.id_empresa,
                            text: label,
                            'data-bloqueada': emp.bloqueada ? 1 : 0,
                            disabled: emp.bloqueada == 1
                        })
                    );
                });
            } else if (response && response.id_empresa) {
                // Respuesta simple (una sola empresa)
                const label = response.nombre_empresa + (response.bloqueada == 1 ? ' 🔒 (ya facturada)' : '');
                $select.append(
                    $('<option>', {
                        value: response.id_empresa,
                        text: label,
                        'data-bloqueada': response.bloqueada ? 1 : 0,
                        selected: response.bloqueada != 1,
                        disabled: response.bloqueada == 1
                    })
                );
                if (response.bloqueada != 1) {
                    $('#btnConfirmarProforma').prop('disabled', false);
                } else {
                    $('#alertaEmpresaBloqueadaProforma').removeClass('d-none');
                }
            }

            const modal = new bootstrap.Modal(document.getElementById('modalGenerarProforma'));
            modal.show();
        })
        .fail(function () {
            Swal.fire('Error', 'No se pudieron cargar las empresas de facturación.', 'error');
        });
}
window.abrirModalGenerarProforma = abrirModalGenerarProforma;

function confirmarGenerarProforma() {
    const idPresupuesto = obtenerIdPresupuesto();
    const idEmpresa     = $('#selectEmpresaProforma').val();

    if (!idEmpresa) {
        Swal.fire('Selección requerida', 'Debes seleccionar una empresa.', 'warning');
        return;
    }

    const bloqueada = $('#selectEmpresaProforma').find(':selected').data('bloqueada');
    if (bloqueada == 1) {
        Swal.fire('No disponible', 'Esta empresa ya tiene una factura activa para este presupuesto.', 'warning');
        return;
    }

    $('#btnConfirmarProforma').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Generando...');

    $.post('../../controller/impresion_factura_proforma.php?op=generar', {
        id_presupuesto: idPresupuesto,
        id_empresa: idEmpresa
    }).done(function (response) {
        $('#btnConfirmarProforma').prop('disabled', false).html('<i class="fas fa-file-invoice-dollar me-1"></i>Generar Proforma');
        bootstrap.Modal.getInstance(document.getElementById('modalGenerarProforma')).hide();

        if (response.success) {
            Swal.fire({
                title: 'Proforma generada',
                text: 'La factura proforma se ha generado correctamente.',
                icon: 'success',
                showCancelButton: true,
                confirmButtonText: 'Ver PDF',
                cancelButtonText: 'Cerrar'
            }).then(function (r) {
                if (r.isConfirmed && response.ruta_pdf) {
                    window.open('../../' + response.ruta_pdf, '_blank');
                }
                recargarDocumentos();
            });
        } else {
            Swal.fire('Error', response.message || 'No se pudo generar la proforma.', 'error');
        }
    }).fail(function () {
        $('#btnConfirmarProforma').prop('disabled', false).html('<i class="fas fa-file-invoice-dollar me-1"></i>Generar Proforma');
        Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
    });
}
window.confirmarGenerarProforma = confirmarGenerarProforma;

/* ---- Parte de trabajo ---- */

function imprimirParteTrabajo() {
    const idPresupuesto = obtenerIdPresupuesto();
    if (!idPresupuesto) {
        Swal.fire('Sin guardar', 'Debes guardar el presupuesto primero.', 'warning');
        return;
    }

    const form = $('<form>', { method: 'POST', action: '../../controller/impresionpartetrabajo_m2_pdf_es.php?op=generar', target: '_blank' });
    form.append($('<input>', { type: 'hidden', name: 'id_presupuesto', value: idPresupuesto }));
    $('body').append(form);
    form.submit();
    form.remove();
}
window.imprimirParteTrabajo = imprimirParteTrabajo;

/* ============================================================
   TAB 3: PAGOS
   ============================================================ */

function initTabPagos() {
    const idPresupuesto = obtenerIdPresupuesto();
    if (!idPresupuesto) return;

    // Verificar si el presupuesto permite pagos (debe estar Aprobado)
    $.post('../../controller/pago_presupuesto.php?op=verificar_estado_pagable',
           { id_presupuesto: idPresupuesto })
        .done(function (res) {
            if (!res.pagable) {
                const estado = res.estado || 'no aprobado';
                $('#pane-pagos button[onclick*="abrirModalRegistrarPago"]')
                    .prop('disabled', true)
                    .attr('title', 'El presupuesto debe estar Aprobado para registrar pagos');
                if (!$('#aviso-pagos-bloqueados').length) {
                    $('#pane-pagos .d-flex.flex-wrap').before(
                        '<div id="aviso-pagos-bloqueados" class="alert alert-warning d-flex align-items-center gap-2 mb-3">' +
                        '<i class="fas fa-lock me-1"></i>' +
                        '<span>No se pueden registrar pagos. El presupuesto está en estado <strong>' + estado + '</strong>. Debe estar <strong>Aprobado</strong>.</span>' +
                        '</div>'
                    );
                }
            }
        });

    tblPagos = $('#tblPagos').DataTable({
        destroy: true,
        processing: true,
        serverSide: false,
        ajax: {
            url: '../../controller/pago_presupuesto.php?op=listar',
            type: 'POST',
            data: { id_presupuesto: idPresupuesto },
            dataSrc: 'data'
        },
        columns: [
            {
                data: 'tipo_pago_ppto',
                title: 'Tipo',
                render: function (data) {
                    const map = {
                        'anticipo':   '<span class="badge bg-info text-dark">Anticipo</span>',
                        'total':      '<span class="badge bg-success">Pago total</span>',
                        'resto':      '<span class="badge bg-primary">Resto</span>',
                        'devolucion': '<span class="badge bg-danger">Devolución</span>'
                    };
                    return map[data] || '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            {
                data: 'importe_pago_ppto',
                title: 'Importe',
                render: function (data) { return formatMoneda(data); },
                className: 'text-end'
            },
            {
                data: 'porcentaje_pago_ppto',
                title: '%',
                render: function (data) {
                    if (data === null || data === '') return '—';
                    return parseFloat(data).toFixed(1) + '%';
                },
                className: 'text-end'
            },
            { data: 'nombre_metodo_pago', title: 'Método' },
            {
                data: 'referencia_pago_ppto',
                title: 'Referencia',
                render: function (data) { return data || '<span class="text-muted">—</span>'; }
            },
            { data: 'fecha_pago_ppto', title: 'Fecha' },
            {
                data: 'estado_pago_ppto',
                title: 'Estado',
                render: function (data) {
                    const map = {
                        'pendiente':   '<span class="badge bg-warning text-dark">Pendiente</span>',
                        'conciliado':  '<span class="badge bg-success">Conciliado</span>',
                        'anulado':     '<span class="badge bg-danger">Anulado</span>'
                    };
                    return map[data] || '<span class="badge bg-secondary">' + data + '</span>';
                }
            },
            {
                data: 'numero_documento_ppto',
                title: 'Documento',
                render: function (data) { return data || '<span class="text-muted">—</span>'; }
            },
            { data: 'opciones', title: 'Opciones', orderable: false, searchable: false }
        ],
        language: {
            emptyTable:    "No hay pagos registrados para este presupuesto",
            info:          "Mostrando _START_ a _END_ de _TOTAL_ pagos",
            infoEmpty:     "Sin pagos",
            infoFiltered:  "(filtrado de _MAX_ pagos totales)",
            lengthMenu:    "Mostrar _MENU_ pagos",
            loadingRecords:"Cargando...",
            processing:    "Procesando...",
            search:        "Buscar:",
            zeroRecords:   "No se encontraron pagos",
            paginate: {
                first:    '<i class="fas fa-angle-double-left"></i>',
                last:     '<i class="fas fa-angle-double-right"></i>',
                previous: '<i class="fas fa-angle-left"></i>',
                next:     '<i class="fas fa-angle-right"></i>'
            }
        },
        order: [[5, 'desc']],
        responsive: true,
        pageLength: 10,
        drawCallback: function () {
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
}

function recargarPagos() {
    if (tblPagos) {
        tblPagos.ajax.reload(null, false);
    }
    actualizarResumenFinanciero();
}

/* ---- Resumen financiero ---- */

function actualizarResumenFinanciero() {
    const idPresupuesto = obtenerIdPresupuesto();
    if (!idPresupuesto) return;

    $.post('../../controller/pago_presupuesto.php?op=resumen_financiero', { id_presupuesto: idPresupuesto })
        .done(function (data) {
            if (!data) return;
            $('#rf-total').text(formatMoneda(data.total_presupuesto));
            $('#rf-pagado').text(formatMoneda(data.total_pagado));
            $('#rf-pendiente').text(formatMoneda(data.saldo_pendiente));

            const pct = parseFloat(data.porcentaje_pagado) || 0;
            $('#rf-barra').css('width', Math.min(pct, 100) + '%').attr('aria-valuenow', pct);
            $('#rf-porcentaje').text(pct.toFixed(1) + '% pagado');
        });
}

/* ---- Modal: Registrar Pago ---- */

function abrirModalRegistrarPago(idPago) {
    idPago = idPago || null;
    const idPresupuesto = obtenerIdPresupuesto();

    if (!idPresupuesto) {
        Swal.fire('Sin guardar', 'Debes guardar el presupuesto antes de registrar pagos.', 'warning');
        return;
    }

    // Reset formulario
    const form = document.getElementById('frmRegistrarPago');
    form.reset();
    $(form).removeClass('was-validated');
    $('#pago_info_porcentaje').text('');
    $('#alertaEmpresaBloqueada').addClass('d-none');
    $('#alertaSinEmpresasDisponibles').addClass('d-none');
    $('#alertaProformaInfo').addClass('d-none');
    $('#seccionTipoDocumento').addClass('d-none');

    $('#pago_id_presupuesto').val(idPresupuesto);
    $('#pago_id_pago_ppto').val('');

    // Fecha de hoy por defecto
    $('#pago_fecha_pago_ppto').val(new Date().toISOString().split('T')[0]);

    if (idPago) {
        // Modo edición
        $('#tituloModalPago').text('Editar Pago');
        $('#seccionGenerarFactura').addClass('d-none');
        $('#alertaModoEdicion').removeClass('d-none');

        $.post('../../controller/pago_presupuesto.php?op=mostrar', { id_pago_ppto: idPago })
            .done(function (data) {
                if (!data || data.success === false) {
                    Swal.fire('Error', 'No se pudo cargar el pago.', 'error');
                    return;
                }
                $('#pago_id_pago_ppto').val(data.id_pago_ppto);
                $('#pago_tipo_pago_ppto').val(data.tipo_pago_ppto);
                $('#pago_importe_pago_ppto').val(parseFloat(data.importe_pago_ppto).toFixed(2));
                $('#pago_fecha_pago_ppto').val(data.fecha_pago_ppto);
                $('#pago_referencia_pago_ppto').val(data.referencia_pago_ppto || '');
                $('#pago_observaciones_pago_ppto').val(data.observaciones_pago_ppto || '');

                cargarMetodosPago(data.id_metodo_pago);
                calcularPorcentajePago();

                const modal = new bootstrap.Modal(document.getElementById('modalRegistrarPago'));
                modal.show();
            })
            .fail(function () {
                Swal.fire('Error', 'Error de comunicación al cargar el pago.', 'error');
            });
    } else {
        // Modo nuevo
        $('#tituloModalPago').text('Registrar Pago');
        $('#seccionGenerarFactura').removeClass('d-none');
        $('#alertaModoEdicion').addClass('d-none');
        $('#chkGenerarFactura').prop('checked', true);

        cargarMetodosPago(null);
        cargarEmpresasFacturacion();
        actualizarSeccionesPagoModal();
        actualizarSeccionesFacturaModal();

        const modal = new bootstrap.Modal(document.getElementById('modalRegistrarPago'));
        modal.show();
    }
}
window.abrirModalRegistrarPago = abrirModalRegistrarPago;

function editarPago(idPago) {
    abrirModalRegistrarPago(idPago);
}

/* ---- Carga de datos del modal Pago ---- */

function cargarMetodosPago(idSeleccionado) {
    const $select = $('#pago_id_metodo_pago').empty().append('<option value="">— Seleccionar —</option>');

    $.post('../../controller/pago_presupuesto.php?op=listar_metodos_pago')
        .done(function (response) {
            const lista = Array.isArray(response) ? response : (response.data || []);
            lista.forEach(function (m) {
                const idVal   = m.id_metodo_pago   || m.id_metodo;
                const txtVal  = m.nombre_metodo_pago || m.nombre_metodo;
                $select.append($('<option>', {
                    value:    idVal,
                    text:     txtVal,
                    selected: (idVal == idSeleccionado)
                }));
            });
        });
}

function cargarEmpresasFacturacion() {
    const $select = $('#pago_id_empresa_factura').empty().append('<option value="">— Seleccionar empresa —</option>');
    $('#alertaEmpresaBloqueada').addClass('d-none');
    $('#alertaSinEmpresasDisponibles').addClass('d-none');

    const idPresupuesto = obtenerIdPresupuesto();

    $.post('../../controller/pago_presupuesto.php?op=listar_empresas_facturacion',
           { id_presupuesto: idPresupuesto })
        .done(function (response) {
            const lista = response.data || response;
            if (!Array.isArray(lista) || lista.length === 0) {
                $('#alertaSinEmpresasDisponibles').removeClass('d-none');
                return;
            }

            let algunaDisponible = false;
            lista.forEach(function (emp) {
                const bloqueada = emp.bloqueada == 1;
                const label = emp.nombre_empresa + (bloqueada ? ' 🔒' : '');
                $select.append($('<option>', {
                    value: emp.id_empresa,
                    text: label,
                    'data-bloqueada': bloqueada ? 1 : 0,
                    disabled: bloqueada
                }));
                if (!bloqueada) algunaDisponible = true;
            });

            if (!algunaDisponible) {
                $('#alertaSinEmpresasDisponibles').removeClass('d-none');
            }
        });
}

function verificarEmpresaSeleccionada() {
    const selOpt = $('#pago_id_empresa_factura').find(':selected');
    const bloqueada = selOpt.data('bloqueada') == 1;
    $('#alertaEmpresaBloqueada').toggleClass('d-none', !bloqueada);
}

/* ---- Lógica de visibilidad en el modal Pago ---- */

function actualizarSeccionesPagoModal() {
    const tipo = $('#pago_tipo_pago_ppto').val();
    const generaFactura = $('#chkGenerarFactura').is(':checked');
    const esAnticipo = tipo === 'anticipo';

    // Sección tipo documento: solo si es anticipo Y genera factura
    $('#seccionTipoDocumento').toggleClass('d-none', !(esAnticipo && generaFactura));

    // Si no es anticipo, resetear radio a factura_anticipo
    if (!esAnticipo) {
        $('#rdoFacturaAnticipo').prop('checked', true);
        $('#alertaProformaInfo').addClass('d-none');
    }
}

function actualizarSeccionesFacturaModal() {
    const generaFactura = $('#chkGenerarFactura').is(':checked');
    $('#seccionEmpresaFactura').toggleClass('d-none', !generaFactura);
    if (!generaFactura) {
        $('#seccionTipoDocumento').addClass('d-none');
    } else {
        actualizarSeccionesPagoModal();
    }
}

/* ---- Cálculo de porcentaje ---- */

function calcularPorcentajePago() {
    const importe = parseFloat($('#pago_importe_pago_ppto').val()) || 0;

    $.post('../../controller/pago_presupuesto.php?op=verificar_pago_completo', {
        id_presupuesto: obtenerIdPresupuesto()
    }).done(function (data) {
        const total = parseFloat(data.total_presupuesto) || 0;
        if (total > 0 && importe > 0) {
            const pct = (importe / total * 100).toFixed(1);
            $('#pago_info_porcentaje').text('(' + pct + '% sobre el total del presupuesto: ' + formatMoneda(total) + ')');
        } else {
            $('#pago_info_porcentaje').text('');
        }
    }).fail(function () {
        $('#pago_info_porcentaje').text('');
    });
}

/* ---- Guardar pago ---- */

function guardarPago() {
    const idPresupuesto   = obtenerIdPresupuesto();
    const idPago          = $('#pago_id_pago_ppto').val();
    const generaFactura   = $('#chkGenerarFactura').is(':checked') && !idPago;
    const idEmpresa       = $('#pago_id_empresa_factura').val();
    const tipoDocumento   = $('input[name="tipo_documento_generar"]:checked').val() || 'factura_anticipo';
    const tipoPago        = $('#pago_tipo_pago_ppto').val();

    // Validar empresa si va a generar factura
    if (generaFactura && !idPago) {
        const bloqueada = $('#pago_id_empresa_factura').find(':selected').data('bloqueada');
        if (!idEmpresa) {
            Swal.fire('Empresa requerida', 'Debes seleccionar la empresa emisora de la factura.', 'warning');
            return;
        }
        if (bloqueada == 1) {
            Swal.fire('Empresa bloqueada', 'La empresa seleccionada ya tiene una factura activa para este presupuesto.', 'warning');
            return;
        }
    }

    const $btn = $('#btnGuardarPago').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

    const formData = $('#frmRegistrarPago').serialize();

    $.post('../../controller/pago_presupuesto.php?op=guardaryeditar', formData)
        .done(function (response) {
            if (!response.success) {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Pago');
                Swal.fire('Error', response.message || 'No se pudo guardar el pago.', 'error');
                return;
            }

            const idPagoNuevo = response.id_pago_ppto || idPago;

            // Si hay que generar factura
            if (generaFactura && idEmpresa) {
                generarFacturaPago(idPagoNuevo, idPresupuesto, idEmpresa, tipoPago, tipoDocumento, function () {
                    $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Pago');
                    bootstrap.Modal.getInstance(document.getElementById('modalRegistrarPago')).hide();
                    recargarDocumentos();
                    recargarPagos();
                });
            } else {
                $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Pago');
                bootstrap.Modal.getInstance(document.getElementById('modalRegistrarPago')).hide();
                Swal.fire('Guardado', response.message || 'Pago guardado correctamente.', 'success');
                recargarPagos();
                recargarDocumentos();
            }
        })
        .fail(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-save me-1"></i>Guardar Pago');
            Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
        });
}

/**
 * Genera la factura correspondiente según el tipo de pago.
 */
function generarFacturaPago(idPago, idPresupuesto, idEmpresa, tipoPago, tipoDocumento, callback) {

    let urlController = '';
    const postData = { id_presupuesto: idPresupuesto, id_empresa: idEmpresa, id_pago_ppto: idPago };

    if (tipoPago === 'anticipo' && tipoDocumento === 'factura_proforma') {
        urlController = '../../controller/impresion_factura_proforma.php?op=generar';
    } else if (tipoPago === 'anticipo') {
        urlController = '../../controller/impresion_factura_anticipo.php?op=generar';
    } else {
        // total, resto → factura final (proforma por ahora)
        urlController = '../../controller/impresion_factura_proforma.php?op=generar';
        postData.tipo_documento = 'factura_final';
    }

    $.post(urlController, postData)
        .done(function (response) {
            if (response.success) {
                Swal.fire({
                    title: 'Pago y factura guardados',
                    text: 'El pago y la factura se han generado correctamente.',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Ver PDF',
                    cancelButtonText: 'Cerrar'
                }).then(function (r) {
                    if (r.isConfirmed && response.ruta_pdf) {
                        window.open('../../' + response.ruta_pdf, '_blank');
                    }
                    if (typeof callback === 'function') callback();
                });
            } else {
                Swal.fire('Pago guardado', 'El pago se guardó pero la factura no pudo generarse: ' + (response.message || ''), 'warning');
                if (typeof callback === 'function') callback();
            }
        })
        .fail(function () {
            Swal.fire('Pago guardado', 'El pago se guardó pero hubo un error al generar la factura.', 'warning');
            if (typeof callback === 'function') callback();
        });
}

/* ---- Acciones sobre pagos ---- */

function anularPago(idPago) {
    Swal.fire({
        title: '¿Anular pago?',
        html: 'El pago quedará marcado como <strong>anulado</strong>.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, anular',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.post('../../controller/pago_presupuesto.php?op=anular', { id_pago_ppto: idPago })
            .done(function (response) {
                if (response.success) {
                    Swal.fire('Anulado', response.message || 'Pago anulado correctamente.', 'success');
                    recargarPagos();
                    recargarDocumentos();
                } else {
                    Swal.fire('Error', response.message || 'No se pudo anular el pago.', 'error');
                }
            })
            .fail(function () {
                Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
            });
    });
}

function conciliarPago(idPago) {
    Swal.fire({
        title: '¿Conciliar pago?',
        html: 'El pago quedará marcado como <strong>conciliado</strong>.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#27ae60',
        confirmButtonText: 'Sí, conciliar',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        $.post('../../controller/pago_presupuesto.php?op=conciliar', { id_pago_ppto: idPago })
            .done(function (response) {
                if (response.success) {
                    Swal.fire('Conciliado', response.message || 'Pago conciliado correctamente.', 'success');
                    recargarPagos();
                } else {
                    Swal.fire('Error', response.message || 'No se pudo conciliar el pago.', 'error');
                }
            })
            .fail(function () {
                Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
            });
    });
}

/* ============================================================
   MODAL: ABONAR FACTURA — VALIDACIÓN Y PROCESO
   ============================================================ */

function validarFormularioAbono() {
    const form = document.getElementById('frmAbonarFactura');
    const motivo = $('#abono_motivo_abono').val().trim();
    const confirmado = $('#chkConfirmarAbono').is(':checked');

    let valido = true;

    if (motivo.length < 10) {
        $('#abono_motivo_abono').addClass('is-invalid');
        valido = false;
    } else {
        $('#abono_motivo_abono').removeClass('is-invalid');
    }

    if (!confirmado) {
        $('#chkConfirmarAbono').addClass('is-invalid');
        valido = false;
    } else {
        $('#chkConfirmarAbono').removeClass('is-invalid');
    }

    if (!valido) {
        $(form).addClass('was-validated');
    }

    return valido;
}

function procesarAbono() {
    const idDocumentoOrigen = $('#abono_id_documento_origen').val();
    const idPresupuesto     = $('#abono_id_presupuesto').val();
    const idEmpresa         = $('#abono_id_empresa').val();
    const motivo            = $('#abono_motivo_abono').val().trim();

    Swal.fire({
        title: '¿Confirmar abono?',
        html: 'Se generará la factura de abono y se <strong>anulará el pago vinculado</strong>. Esta acción es irreversible.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, generar abono',
        cancelButtonText: 'Cancelar'
    }).then(function (result) {
        if (!result.isConfirmed) return;

        const $btn = $('#btnConfirmarAbono').prop('disabled', true).html('<span class="spinner-border spinner-border-sm me-2"></span>Procesando...');

        $.post('../../controller/impresion_factura_abono.php?op=generar', {
            id_presupuesto:      idPresupuesto,
            id_empresa:          idEmpresa,
            id_documento_origen: idDocumentoOrigen,
            motivo_abono:        motivo
        }).done(function (response) {
            $btn.prop('disabled', false).html('<i class="fas fa-rotate-left me-1"></i>Generar Abono');
            bootstrap.Modal.getInstance(document.getElementById('modalAbonarFactura')).hide();

            if (response.success) {
                Swal.fire({
                    title: 'Abono generado',
                    text: 'La factura de abono se ha generado y el pago ha sido anulado.',
                    icon: 'success',
                    showCancelButton: true,
                    confirmButtonText: 'Ver PDF',
                    cancelButtonText: 'Cerrar'
                }).then(function (r) {
                    if (r.isConfirmed && response.ruta_pdf) {
                        window.open('../../' + response.ruta_pdf, '_blank');
                    }
                    recargarDocumentos();
                    recargarPagos();
                });
            } else {
                Swal.fire('Error', response.message || 'No se pudo generar el abono.', 'error');
            }
        }).fail(function () {
            $btn.prop('disabled', false).html('<i class="fas fa-rotate-left me-1"></i>Generar Abono');
            Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
        });
    });
}

/* ============================================================
   VALIDACIÓN FORMULARIO PAGO
   ============================================================ */

function validarFormularioPago() {
    const form = document.getElementById('frmRegistrarPago');
    if (!form.checkValidity()) {
        $(form).addClass('was-validated');
        return false;
    }

    const importe = parseFloat($('#pago_importe_pago_ppto').val());
    if (isNaN(importe) || importe <= 0) {
        $('#pago_importe_pago_ppto').addClass('is-invalid');
        $(form).addClass('was-validated');
        return false;
    }

    return true;
}
