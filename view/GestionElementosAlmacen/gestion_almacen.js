/**
 * gestion_almacen.js — Lógica de la pantalla de gestión de elementos en almacén
 * Fases: 1-Búsqueda (NFC / manual) → 2-Detalle + edición
 */

'use strict';

const CTR = '../../controller/gestion_almacen.php';

const state = {
    elemento: null,   // datos completos del elemento activo
    nfcController: null    // AbortController para NFC
};

// ────────────────────────────────────────────────────────────────
// HELPERS
// ────────────────────────────────────────────────────────────────
function mostrarFase(n) {
    document.querySelectorAll('.phase').forEach(p => p.classList.remove('active'));
    document.getElementById('phase' + n).classList.add('active');

    if (n === 2) {
        document.getElementById('bottom-bar').classList.add('visible');
    } else {
        document.getElementById('bottom-bar').classList.remove('visible');
    }
}

function feedback(elementId, msg, tipo) {
    const el = document.getElementById(elementId);
    const icons = { ok: 'check-circle', err: 'times-circle', warn: 'exclamation-triangle', info: 'info-circle' };
    el.className = 'fb-banner fb-' + tipo;
    el.innerHTML = '<i class="fa fa-' + (icons[tipo] || 'circle-info') + '"></i>' + msg;
    el.classList.remove('fb-hidden');
}

function hideFeedback(elementId) {
    const el = document.getElementById(elementId);
    el.classList.add('fb-hidden');
    el.className = 'fb-banner fb-hidden';
}

function vibrar(tipo) {
    if (!navigator.vibrate) return;
    if (tipo === 'ok') navigator.vibrate([100]);
    if (tipo === 'err') navigator.vibrate([200, 100, 200]);
}

function escHtml(str) {
    return String(str ?? '')
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// ────────────────────────────────────────────────────────────────
// NFC
// ────────────────────────────────────────────────────────────────
function iniciarNFC() {
    if (!('NDEFReader' in window)) {
        document.getElementById('btn-nfc').style.display = 'none';
        return;
    }

    feedback('nfc-estado', 'NFC activo — acerca la etiqueta…', 'info');

    state.nfcController = new AbortController();
    const reader = new NDEFReader();

    reader.scan({ signal: state.nfcController.signal })
        .then(() => {
            reader.onreading = ({ message }) => {
                for (const record of message.records) {
                    if (record.recordType === 'text') {
                        const decoder = new TextDecoder(record.encoding || 'utf-8');
                        const codigo = decoder.decode(record.data).trim().toUpperCase();
                        detenerNFC();
                        buscarElemento(codigo);
                        break;
                    }
                }
            };
        })
        .catch(() => {
            document.getElementById('btn-nfc').style.display = 'none';
            hideFeedback('nfc-estado');
        });
}

function detenerNFC() {
    if (state.nfcController) {
        state.nfcController.abort();
        state.nfcController = null;
    }
    hideFeedback('nfc-estado');
}

// ────────────────────────────────────────────────────────────────
// FASE 1 — BÚSQUEDA
// ────────────────────────────────────────────────────────────────
function buscarElemento(codigoExterno) {
    const codigo = codigoExterno || document.getElementById('inp-codigo').value.trim().toUpperCase();
    if (!codigo) return;

    feedback('fb-busqueda', 'Buscando ' + escHtml(codigo) + '…', 'info');

    $.post(CTR + '?op=buscar', { codigo_elemento: codigo })
        .done(function (resp) {
            if (!resp.success) {
                vibrar('err');
                feedback('fb-busqueda', escHtml(resp.message || 'Elemento no encontrado'), 'err');
                return;
            }

            vibrar('ok');
            hideFeedback('fb-busqueda');
            state.elemento = resp.elemento;
            renderFase2(resp.elemento, resp.estados, resp.presupuesto_activo);
        })
        .fail(function () {
            vibrar('err');
            feedback('fb-busqueda', 'Error de comunicación con el servidor', 'err');
        });
}

// ────────────────────────────────────────────────────────────────
// FASE 2 — RENDERIZAR DETALLE
// ────────────────────────────────────────────────────────────────
function renderFase2(elem, estados, presupuestoActivo) {
    // Strip
    document.getElementById('strip-codigo').textContent = elem.codigo_elemento || '—';
    document.getElementById('strip-articulo').textContent = elem.nombre_articulo || '—';

    // Badge estado actual
    const color = elem.color_estado_elemento || '#6c757d';
    document.getElementById('strip-estado-badge').innerHTML =
        '<span class="estado-badge" style="background:' + escHtml(color) + '">'
        + escHtml(elem.descripcion_estado_elemento || elem.codigo_estado_elemento)
        + '</span>';

    // Info readonly
    const rowPpto = document.getElementById('row-presupuesto');
    if (presupuestoActivo && presupuestoActivo.numero_presupuesto_salida) {
        document.getElementById('info-presupuesto').textContent = presupuestoActivo.numero_presupuesto_salida;
        rowPpto.style.display = '';
    } else {
        rowPpto.style.display = 'none';
    }

    const propText = elem.es_propio_elemento == 1
        ? '<span style="color:#198754;font-weight:700;">PROPIO</span>'
        : '<span style="color:#dc3545;font-weight:700;">ALQUILADO</span>'
        + (elem.nombre_proveedor_alquiler ? ' · ' + escHtml(elem.nombre_proveedor_alquiler) : '');
    document.getElementById('info-propiedad').innerHTML = propText;

    const ubi = [elem.nave_elemento, elem.pasillo_columna_elemento, elem.altura_elemento]
        .filter(Boolean).join(' · ');
    document.getElementById('info-ubicacion').textContent = ubi || '—';
    document.getElementById('info-peso').textContent = elem.peso_elemento ? elem.peso_elemento + ' kg' : '—';
    document.getElementById('info-serie').textContent = elem.numero_serie_elemento || '—';
    document.getElementById('info-descripcion').textContent = elem.descripcion_elemento || '—';

    // Poblar select de estados (TODOS — sin restricciones para almacén)
    const sel = document.getElementById('sel-estado');
    sel.innerHTML = '';
    (estados || []).filter(e => e.activo_estado_elemento == 1).forEach(function (e) {
        const opt = document.createElement('option');
        opt.value = e.id_estado_elemento;
        opt.textContent = e.descripcion_estado_elemento;
        opt.dataset.color = e.color_estado_elemento || '#6c757d';
        if (e.id_estado_elemento == elem.id_estado_elemento) opt.selected = true;
        sel.appendChild(opt);
    });

    // Fecha de próximo mantenimiento
    const mantVal = elem.proximo_mantenimiento_elemento
        ? elem.proximo_mantenimiento_elemento.split(' ')[0]   // quitar parte hora si existe
        : '';
    document.getElementById('inp-mant').value = mantVal;

    // ID oculto
    document.getElementById('hidden-id-elemento').value = elem.id_elemento;

    // Actualizar appbar sub
    document.getElementById('app-bar-sub').textContent = elem.codigo_elemento + ' — ' + (elem.nombre_articulo || '');

    mostrarFase(2);
}

// ────────────────────────────────────────────────────────────────
// GUARDAR
// ────────────────────────────────────────────────────────────────
// function guardar() {
//     const idElemento = document.getElementById('hidden-id-elemento').value;
//     const idEstado = document.getElementById('sel-estado').value;
//     const proxMant = document.getElementById('inp-mant').value;

//     if (!idElemento || !idEstado) {
//         Swal.fire('Error', 'Faltan datos obligatorios', 'error');
//         return;
//     }

//     document.getElementById('btn-guardar').disabled = true;

//     $.post(CTR + '?op=actualizar', {
//         id_elemento: idElemento,
//         id_estado_elemento: idEstado,
//         proximo_mantenimiento_elemento: proxMant
//     })
//         .done(function (resp) {
//             document.getElementById('btn-guardar').disabled = false;

//             if (resp.success) {
//                 vibrar('ok');
//                 Swal.fire({
//                     icon: 'success',
//                     title: 'Guardado',
//                     text: resp.message,
//                     timer: 1800,
//                     showConfirmButton: false
//                 });

//                 // Actualizar el badge del strip con el color real del estado seleccionado
//                 const sel = document.getElementById('sel-estado');
//                 const selOpt = sel.options[sel.selectedIndex];
//                 const txtEstado = selOpt?.text || '';
//                 const colorEstado = selOpt?.dataset.color || '#6c757d';
//                 document.getElementById('strip-estado-badge').innerHTML =
//                     '<span class="estado-badge" style="background:' + escHtml(colorEstado) + '">' + escHtml(txtEstado) + '</span>';

//             } else {
//                 vibrar('err');
//                 Swal.fire('Error', resp.message || 'No se pudo guardar', 'error');
//             }
//         })
//         .fail(function () {
//             document.getElementById('btn-guardar').disabled = false;
//             vibrar('err');
//             Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
//         });
// }

function guardar() {
    const idElemento = document.getElementById('hidden-id-elemento').value;
    const idEstado = document.getElementById('sel-estado').value;
    const proxMant = document.getElementById('inp-mant').value;

    if (!idElemento || !idEstado) return;

    // Bloquear controles para evitar doble disparo
    document.getElementById('sel-estado').disabled = true;
    document.getElementById('inp-mant').disabled = true;

    feedback('fb-fase2', 'Guardando…', 'info');

    $.post(CTR + '?op=actualizar', {
        id_elemento: idElemento,
        id_estado_elemento: idEstado,
        proximo_mantenimiento_elemento: proxMant
    })
        .done(function (resp) {
            document.getElementById('sel-estado').disabled = false;
            document.getElementById('inp-mant').disabled = false;

            if (resp.success) {
                vibrar('ok');
                feedback('fb-fase2', 'Guardado', 'ok');
                setTimeout(function () { hideFeedback('fb-fase2'); }, 2000);

                // Actualizar badge con el color real del estado guardado
                const sel = document.getElementById('sel-estado');
                const selOpt = sel.options[sel.selectedIndex];
                const txtEstado = selOpt?.text || '';
                const colorEst = selOpt?.dataset.color || '#6c757d';
                document.getElementById('strip-estado-badge').innerHTML =
                    '<span class="estado-badge" style="background:' + escHtml(colorEst) + '">' + escHtml(txtEstado) + '</span>';

                // Sincronizar state con los valores guardados
                state.elemento.id_estado_elemento = idEstado;
                state.elemento.proximo_mantenimiento_elemento = proxMant || null;

            } else {
                vibrar('err');
                feedback('fb-fase2', escHtml(resp.message || 'Error al guardar'), 'err');
            }
        })
        .fail(function () {
            document.getElementById('sel-estado').disabled = false;
            document.getElementById('inp-mant').disabled = false;
            vibrar('err');
            feedback('fb-fase2', 'Error de comunicación', 'err');
        });
}

// ────────────────────────────────────────────────────────────────
// NUEVA BÚSQUEDA
// ────────────────────────────────────────────────────────────────
function nuevaBusqueda() {
    detenerNFC();
    state.elemento = null;
    document.getElementById('inp-codigo').value = '';
    hideFeedback('fb-busqueda');
    document.getElementById('app-bar-sub').textContent = 'MDR · Área Técnica';
    mostrarFase(1);
}

// ────────────────────────────────────────────────────────────────
// INIT — ocultar NFC si no disponible + auto-guardar en cambios
// ────────────────────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', function () {
    if (!('NDEFReader' in window)) {
        document.getElementById('btn-nfc').style.display = 'none';
    }

    // Auto-guardar al cambiar el estado (select)
    document.getElementById('sel-estado').addEventListener('change', function () {
        if (!state.elemento) return;  // solo si hay un elemento cargado
        guardar();
    });

    // Auto-guardar al salir del campo fecha (blur en lugar de change para
    // permitir escribir la fecha completa sin disparar guardados parciales)
    const esTactil = ('ontouchstart' in window) || (navigator.maxTouchPoints > 0);
    document.getElementById('inp-mant').addEventListener(esTactil ? 'change' : 'blur', function () {
        if (!state.elemento) return;
        if (!this.value) return;
        guardar();
    });
});
