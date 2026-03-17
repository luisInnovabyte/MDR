/**
 * picking.js — Lógica del sistema de picking MDR
 * Fases: 1-QR ppto → 2-necesidades → 3-escaneo NFC → 4-confirmación
 */

'use strict';

// ============================================================
// ESTADO GLOBAL
// ============================================================
const state = {
    presupuesto: null,      // datos del presupuesto activo
    salida: null,           // id_salida_almacen + meta
    progreso: null,         // último get_progreso_salida
    nfcController: null,    // AbortController NFC
    qrScanner: null,        // instancia Html5QrcodeScanner
    elementoReubicacion: null,  // {id_elemento, id_linea_salida, nombre}
    ubicacionesDisponibles: []
};

const CTR = '../../controller/salida_almacen.php';
// ID_USUARIO se inyecta desde PHP en index.php

// ============================================================
// HELPERS
// ============================================================
function mostrarFase(n) {
    document.querySelectorAll('.phase').forEach(p => p.classList.remove('active'));
    document.getElementById('phase' + n).classList.add('active');
    // Notificar al app bar y barra inferior
    document.dispatchEvent(new CustomEvent('phaseChange', { detail: { phase: n } }));
}

function feedback(elementId, msg, tipo) {
    // tipo: ok | err | warn | info
    const el = document.getElementById(elementId);
    el.className = 'fb-banner fb-' + tipo;
    el.innerHTML = '<i class="fa fa-' + { ok: 'check-circle', err: 'times-circle', warn: 'exclamation-triangle', info: 'info-circle' }[tipo] + ' me-2"></i>' + msg;
    el.classList.remove('fb-hidden');
}

function vibrar(tipo) {
    if (!navigator.vibrate) return;
    if (tipo === 'ok') navigator.vibrate([100]);
    if (tipo === 'err') navigator.vibrate([200, 100, 200]);
    if (tipo === 'warn') navigator.vibrate([150, 50, 150]);
}

function post(op, data) {
    return $.post(CTR + '?op=' + op, data);
}

// ============================================================
// FASE 1 — ESCANEO QR PRESUPUESTO
// ============================================================
function iniciarQR() {
    const placeholder = document.getElementById('qr-placeholder');
    const readerEl = document.getElementById('qr-reader');

    // Feedback inmediato para confirmar que la función se ejecuta
    if (placeholder) {
        placeholder.innerHTML =
            '<div style="text-align:center;padding:20px 0;">' +
            '<i class="fa fa-spinner fa-spin fa-2x mb-2 d-block" style="color:#1a237e;"></i>' +
            '<span style="font-size:.9rem;color:#555;">Iniciando cámara…</span>' +
            '</div>';
    }

    if (state.qrScanner) return;

    function mostrarErrorCamara(err) {
        console.error('[Picking] iniciarQR error:', err);
        state.qrScanner = null;
        if (readerEl) { readerEl.style.display = 'none'; readerEl.innerHTML = ''; }
        if (!placeholder) return;

        let msg = 'No se pudo activar la cámara.';
        const errStr = (err && (err.name || err.message || String(err))).toLowerCase();

        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            msg = 'La cámara requiere <strong>HTTPS</strong>. Abre la página con <code>https://</code>';
        } else if (errStr.includes('notallowed') || errStr.includes('permission')) {
            msg = 'Permiso de cámara <strong>denegado</strong>. Toca el icono de cámara en la barra del navegador y concede el permiso.';
        } else if (errStr.includes('notfound') || errStr.includes('devicenotfound')) {
            msg = 'No se encontró ninguna cámara en este dispositivo.';
        } else if (errStr.includes('notreadable') || errStr.includes('trackstart')) {
            msg = 'La cámara está siendo usada por otra aplicación. Ciérrala e inténtalo de nuevo.';
        } else if (err) {
            msg = 'Error cámara: <code>' + String(err.name || err) + '</code>';
        }

        placeholder.innerHTML =
            '<div class="fb-banner fb-warn mb-3" style="flex-direction:column;align-items:flex-start;">'
            + '<div><i class="fa fa-exclamation-triangle me-2"></i><strong>Cámara no disponible</strong></div>'
            + '<div style="font-size:.85rem;margin-top:6px;">' + msg + '</div>'
            + '</div>'
            + '<button id="btn-activar-qr" class="btn-app btn-app-primary">'
            + '<i class="fa fa-redo me-2"></i>Reintentar</button>';
        placeholder.style.display = 'block';
        bindBtnActivarQR();
    }

    // Comprobar soporte antes de intentar
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        mostrarErrorCamara(null);
        return;
    }

    // Mostrar spinner mientras se pide permiso de cámara
    if (readerEl) {
        readerEl.innerHTML =
            '<div style="text-align:center;padding:32px 0;color:#555;">'
            + '<i class="fa fa-spinner fa-spin fa-2x mb-3 d-block"></i>'
            + '<span style="font-size:.9rem;">Solicitando permiso de cámara…</span>'
            + '</div>';
        readerEl.style.display = 'block';
    }
    if (placeholder) placeholder.style.display = 'none';

    const dim = Math.min(280, Math.max(200, window.innerWidth - 60));

    try {
        state.qrScanner = new Html5Qrcode('qr-reader');
    } catch (e) {
        mostrarErrorCamara(e);
        return;
    }

    state.qrScanner
        .start(
            { facingMode: 'environment' },
            { fps: 12, qrbox: { width: dim, height: dim } },
            onQRSuccess,
            function () { /* errores de frame, silencioso */ }
        )
        .then(function () {
            if (readerEl) readerEl.style.display = 'block';
        })
        .catch(mostrarErrorCamara);
}

function detenerQR() {
    if (!state.qrScanner) return;
    const scanner = state.qrScanner;
    state.qrScanner = null;           // null síncrono, evita que iniciarQR() lo reutilice
    scanner.stop()
        .then(function () { try { scanner.clear(); } catch (e) { } })
        .catch(function () { });
}

function onQRSuccess(decodedText) {
    detenerQR();
    // Ocultar vídeo y mostrar placeholder de nuevo
    const placeholder = document.getElementById('qr-placeholder');
    const readerEl = document.getElementById('qr-reader');
    if (readerEl) readerEl.style.display = 'none';
    if (placeholder) {
        placeholder.innerHTML = '<div class="d-flex align-items-center gap-2 text-success fw-semibold">'
            + '<i class="fa fa-check-circle fa-lg"></i><span>QR leído ✓</span></div>';
        placeholder.style.display = 'block';
    }
    // El QR puede contener el número directamente o una URL con el número
    const numero = extraerNumeroPpto(decodedText);
    buscarPresupuesto(numero);
}

function extraerNumeroPpto(raw) {
    // Si viene URL con ?n=P-00001/2026 → extraer
    try {
        const url = new URL(raw);
        if (url.searchParams.has('n')) return url.searchParams.get('n');
    } catch (e) { }
    // Si es código directo tipo P-00001/2026
    return raw.trim();
}

function buscarPresupuesto(numero) {
    if (!numero) return;
    feedback('fb-busqueda', 'Buscando...', 'info');

    post('buscar_presupuesto', { numero_presupuesto: numero })
        .done(function (data) {
            if (!data.success) {
                vibrar('err');
                feedback('fb-busqueda', data.message, 'err');
                // Reactivar cámara tras error para reintentar
                setTimeout(function () {
                    const ph = document.getElementById('qr-placeholder');
                    if (ph) { ph.innerHTML = '<button id="btn-activar-qr" class="btn-app btn-app-primary"><i class="fa fa-camera me-2"></i>Activar Cámara</button>'; ph.style.display = 'block'; bindBtnActivarQR(); }
                    document.getElementById('qr-reader').style.display = 'none';
                }, 300);
                return;
            }
            vibrar('ok');
            detenerQR();
            state.presupuesto = data.presupuesto;

            if (data.salida_activa) {
                // Recuperar sesión existente
                state.salida = data.salida_activa;
                cargarFase2(data);
                Swal.fire({
                    toast: true, position: 'top', icon: 'info', showConfirmButton: false, timer: 2500,
                    title: 'Sesión de picking recuperada'
                });
            } else {
                cargarFase2(data);
            }
        })
        .fail(function (xhr) {
            feedback('fb-busqueda', 'Error de conexión — HTTP ' + xhr.status + ' · ' + (xhr.responseText || '').substring(0, 120), 'err');
        });
}

// ============================================================
// FASE 2 — NECESIDADES Y PROGRESO
// ============================================================
function cargarFase2(data) {
    const p = data.presupuesto;
    document.getElementById('nav-numero').textContent = p.numero_presupuesto;
    document.getElementById('p2-numero').textContent = p.numero_presupuesto;
    document.getElementById('p2-cliente').textContent = p.nombre_cliente || '';
    document.getElementById('p2-evento').textContent = p.nombre_evento_presupuesto || '';

    renderNecesidades(data.necesidades || []);
    mostrarFase(2);

    // Si hay salida activa, refrescar progreso
    if (state.salida) {
        refrescarProgreso();
        document.getElementById('btn-ver-mapa').style.display = 'block';
    }
}

function renderNecesidades(necesidades) {
    const cont = document.getElementById('lista-articulos');
    if (!necesidades.length) {
        cont.innerHTML = '<div class="alert alert-warning small">Este presupuesto no tiene líneas de material.</div>';
        return;
    }
    cont.innerHTML = necesidades.map(n => `
        <div class="card art-card shadow-sm" id="art-${n.id_articulo}">
            <div class="card-body py-2 px-3 d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">${escHtml(n.nombre_articulo)}</div>
                    <div class="text-muted small">${n.nombre_ubicacion ? '📍 ' + escHtml(n.nombre_ubicacion) : 'Sin ubicación asignada'}</div>
                </div>
                <div class="text-end">
                    <span class="badge bg-secondary badge-count" id="cnt-${n.id_articulo}">0/${parseInt(n.cantidad_linea_ppto) || 0}</span>
                </div>
            </div>
            <div class="px-3 pb-2">
                <div class="progress" style="height:8px;border-radius:4px;">
                    <div class="progress-bar bg-success" id="pb-${n.id_articulo}" style="width:0%;transition:width .4s;"></div>
                </div>
            </div>
        </div>
    `).join('');
}

function refrescarProgreso() {
    if (!state.salida) return;
    post('progreso', { id_salida_almacen: state.salida.id_salida_almacen })
        .done(function (data) {
            if (!data.success) return;
            state.progreso = data.progreso;
            actualizarBarras(data.progreso);
        });
}

function actualizarBarras(progreso) {
    const total = progreso.total_requerido;
    const escaneado = progreso.total_escaneado;
    const pct = total ? Math.min(100, Math.round(escaneado / total * 100)) : 0;

    document.getElementById('p2-contadores').textContent = `${escaneado} / ${total}`;
    document.getElementById('p2-barra').style.width = pct + '%';
    document.getElementById('p3-contadores').textContent = `${escaneado}/${total}`;

    (progreso.por_articulo || []).forEach(art => {
        const cnt = document.getElementById('cnt-' + art.id_articulo);
        const pb = document.getElementById('pb-' + art.id_articulo);
        if (!cnt || !pb) return;
        const req = parseInt(art.cantidad_requerida) || 0;
        const esc = parseInt(art.cantidad_escaneada) || 0;
        const bkp = parseInt(art.cantidad_backup) || 0;
        const pctArt = req ? Math.min(100, Math.round(esc / req * 100)) : 0;
        cnt.textContent = esc + (bkp ? '+' + bkp + '🔵' : '') + '/' + req;
        cnt.className = 'badge badge-count ' + (esc >= req ? 'bg-success' : 'bg-secondary');
        pb.style.width = pctArt + '%';
    });

    // Habilitar btn completar
    const btnCompletar = document.getElementById('btn-completar');
    if (btnCompletar) {
        btnCompletar.disabled = !progreso.completo;
        btnCompletar.className = 'btn-app ' + (progreso.completo ? 'btn-app-success mb-2' : 'btn-app-secondary mb-2');
    }
}

// ============================================================
// FASE 3 — ESCANEO NFC / MANUAL DE ELEMENTOS
// ============================================================
function iniciarFase3() {
    document.getElementById('p3-numero').textContent = state.presupuesto.numero_presupuesto;
    feedback('fb-escaneo', 'Acerca el tag NFC o introduce el c\u00f3digo', 'info');
    mostrarFase(3);
    iniciarNFC();
    cargarElementosEscaneados();
}

// --- NFC ---
function iniciarNFC() {
    if (!('NDEFReader' in window)) {
        document.getElementById('btn-nfc').style.display = 'none';
        return;
    }
    state.nfcController = new AbortController();
    const reader = new NDEFReader();
    reader.scan({ signal: state.nfcController.signal })
        .then(() => {
            reader.onreading = ({ message }) => {
                for (const record of message.records) {
                    if (record.recordType === 'text') {
                        const decoder = new TextDecoder(record.encoding || 'utf-8');
                        const codigo = decoder.decode(record.data).trim().toUpperCase();
                        procesarEscaneo(codigo);
                        break;
                    }
                }
            };
        })
        .catch(() => { document.getElementById('btn-nfc').style.display = 'none'; });
}

function detenerNFC() {
    if (state.nfcController) {
        state.nfcController.abort();
        state.nfcController = null;
    }
}

// --- Procesar escaneo central ---
function procesarEscaneo(codigo, esBackup = false) {
    if (!codigo) return;
    document.getElementById('inp-codigo-elem').value = '';
    feedback('fb-escaneo', 'Verificando ' + escHtml(codigo) + '...', 'info');

    post('escanear', {
        id_salida_almacen: state.salida.id_salida_almacen,
        codigo_elemento: codigo,
        es_backup: esBackup ? 1 : 0
    }).done(function (data) {
        switch (data.tipo) {
            case 'correcto':
                vibrar('ok');
                feedback('fb-escaneo', '<strong>' + escHtml(codigo) + '</strong> — ' + escHtml(data.elemento.nombre_articulo), 'ok');
                break;
            case 'backup':
                vibrar('ok');
                feedback('fb-escaneo', '<strong>' + escHtml(codigo) + '</strong> añadido como backup', 'warn');
                break;
            case 'ya_asignado':
                vibrar('warn');
                feedback('fb-escaneo', '<strong>' + escHtml(codigo) + '</strong> ya está en la lista. ¿Reubicar?', 'warn');
                if (data.progreso) { state.progreso = data.progreso; actualizarBarras(data.progreso); }
                Swal.fire({
                    title: 'Ya está preparado',
                    html: '<strong>' + escHtml(codigo) + '</strong> ya se encuentra en la lista de esta salida.<br><small class="text-muted">' + escHtml(data.elemento.nombre_articulo) + '</small>',
                    icon: 'info',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-exchange-alt me-1"></i> Reubicar',
                    cancelButtonText: 'Cerrar',
                    confirmButtonColor: '#6c757d'
                }).then(r => { if (r.isConfirmed) abrirModalReubicacion(data); });
                return;
            case 'ya_alquilado':
                vibrar('warn');
                feedback('fb-escaneo', '<strong>' + escHtml(codigo) + '</strong> está alquilado. ¿Reubicar?', 'warn');
                if (data.linea_salida) {
                    Swal.fire({
                        title: 'Elemento alquilado',
                        html: '<strong>' + escHtml(codigo) + '</strong> ya está en estado alquilado.<br><small class="text-muted">' + escHtml(data.elemento.nombre_articulo) + '</small>',
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: '<i class="fa fa-exchange-alt me-1"></i> Reubicar',
                        cancelButtonText: 'Cerrar',
                        confirmButtonColor: '#6c757d'
                    }).then(r => { if (r.isConfirmed) abrirModalReubicacion(data); });
                }
                return;
            case 'cantidad_completada':
                vibrar('warn');
                Swal.fire({
                    title: 'Cantidad cubierta',
                    text: data.mensaje,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, añadir como backup',
                    cancelButtonText: 'No'
                }).then(r => {
                    if (r.isConfirmed) procesarEscaneo(codigo, true);
                });
                return;
            case 'articulo_no_pertenece_preguntar':
                vibrar('warn');
                feedback('fb-escaneo', '<strong>' + escHtml(codigo) + '</strong> no está en el presupuesto', 'warn');
                Swal.fire({
                    title: 'No está en el presupuesto',
                    html: '<strong>' + escHtml(codigo) + '</strong><br><span class="text-muted">' + escHtml(data.elemento.nombre_articulo) + '</span><br><br>¿Lo añades como <strong>material de repuesto</strong>?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '<i class="fa fa-toolbox me-1"></i> Sí, es repuesto',
                    cancelButtonText: 'No, descartar',
                    confirmButtonColor: '#795548'
                }).then(r => {
                    if (r.isConfirmed) procesarEscaneo(codigo, true);
                    else feedback('fb-escaneo', 'Elemento descartado', 'info');
                });
                return;
            case 'articulo_no_pertenece':
                vibrar('err');
                feedback('fb-escaneo', escHtml(data.mensaje), 'err');
                break;
            case 'no_disponible':
                vibrar('err');
                feedback('fb-escaneo', escHtml(data.mensaje), 'err');
                break;
            case 'elemento_no_encontrado':
                vibrar('err');
                feedback('fb-escaneo', escHtml(data.mensaje), 'err');
                break;
            default:
                vibrar('err');
                feedback('fb-escaneo', data.mensaje || 'Error desconocido', 'err');
        }

        if (data.progreso) {
            state.progreso = data.progreso;
            actualizarBarras(data.progreso);
        }
        cargarElementosEscaneados();
    }).fail(function () {
        feedback('fb-escaneo', 'Error de conexión', 'err');
    });
}

// --- Lista de elementos escaneados ---
function cargarElementosEscaneados() {
    if (!state.salida) return;
    post('elementos_escaneados', { id_salida_almacen: state.salida.id_salida_almacen })
        .done(function (data) {
            if (!data.success) return;
            renderElementosEscaneados(data.elementos || []);
        });
}

function renderElementosEscaneados(elementos) {
    const cont = document.getElementById('lista-elementos-escaneados');
    if (!elementos.length) {
        cont.innerHTML = '<p class="text-muted small text-center py-3">Aún no se ha escaneado ningún elemento</p>';
        return;
    }
    cont.innerHTML = elementos.map(e => `
        <div class="elem-row">
            <div>
                <span class="fw-semibold small text-uppercase">${escHtml(e.codigo_elemento)}</span>
                ${e.es_backup_linea_salida == 1 ? '<span class="badge badge-bkp ms-1 small">backup</span>' : ''}
                <div class="text-muted" style="font-size:0.78rem;">${escHtml(e.nombre_articulo || '')}</div>
                ${e.numero_serie_elemento ? `<div class="text-muted" style="font-size:0.75rem;"><i class="fa fa-barcode me-1"></i>S/N: ${escHtml(e.numero_serie_elemento)}</div>` : ''}
                <div class="text-muted" style="font-size:0.75rem;">
                    <i class="fa fa-map-marker-alt me-1 text-danger"></i>${escHtml(e.nombre_ubicacion_actual || 'Sin ubicación')}
                </div>
            </div>
            <button class="elem-move"
                    data-id-elemento="${e.id_elemento}"
                    data-id-linea="${e.id_linea_salida}"
                    data-codigo="${escHtml(e.codigo_elemento)}"
                    data-nombre="${escHtml(e.nombre_articulo || '')}"
                    data-ubicacion="${escHtml(e.nombre_ubicacion_actual || '')}">
                <i class="fa fa-exchange-alt"></i>
            </button>
        </div>
    `).join('');
}

// ============================================================
// MODAL REUBICACIÓN
// ============================================================
function abrirModalReubicacion(data) {
    const elem = data.elemento;
    const ubic = data.ubicacion_actual;
    state.elementoReubicacion = {
        id_elemento: elem.id_elemento,
        id_linea_salida: data.linea_salida.id_linea_salida,
        nombre: elem.nombre_articulo
    };
    document.getElementById('reub-elem-nombre').textContent = elem.codigo_elemento + ' — ' + elem.nombre_articulo;
    document.getElementById('reub-ubicacion-actual').textContent = ubic ? ubic.nombre_ubicacion : 'Sin ubicación';
    cargarSelectUbicaciones();
    document.getElementById('inp-obs-movimiento').value = '';
    abrirModalReubicacionUI();
}

function abrirReubicacionDirecta(id_elemento, id_linea_salida, codigo, nombre, ubicacionActual) {
    state.elementoReubicacion = { id_elemento, id_linea_salida, nombre };
    document.getElementById('reub-elem-nombre').textContent = codigo + ' — ' + nombre;
    document.getElementById('reub-ubicacion-actual').textContent = ubicacionActual || 'Sin ubicación';
    cargarSelectUbicaciones();
    document.getElementById('inp-obs-movimiento').value = '';
    abrirModalReubicacionUI();
}

function abrirModalReubicacionUI() {
    // Reset Bootstrap 4 modal state para que funcione en aperturas sucesivas
    var $m = $('#modalReubicacion');
    $m.data('bs.modal', null);
    $m.modal({ backdrop: true, keyboard: true, show: true });
}

function cargarSelectUbicaciones() {
    post('mapa_ubicaciones', { id_salida_almacen: state.salida.id_salida_almacen })
        .done(function (data) {
            const sel = document.getElementById('sel-ubicacion-destino');
            const ubicaciones = data.ubicaciones_disponibles || [];
            sel.innerHTML = '<option value="">-- Selecciona ubicación --</option>' +
                ubicaciones.map(u => `<option value="${u.id_ubicacion}">${escHtml(u.nombre_ubicacion)}</option>`).join('');
        });
}

// ============================================================
// COMPLETAR / CANCELAR
// ============================================================
function completarSalida() {
    Swal.fire({
        title: '¿Confirmar salida?',
        html: `Se marcarán <strong>${state.progreso ? state.progreso.total_escaneado : '?'}</strong> elementos como alquilados.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, completar',
        cancelButtonText: 'Volver',
        confirmButtonColor: '#198754'
    }).then(r => {
        if (!r.isConfirmed) return;
        post('completar', { id_salida_almacen: state.salida.id_salida_almacen })
            .done(function (data) {
                if (data.success) {
                    detenerNFC();
                    mostrarFase4();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
    });
}

function cancelarSalida() {
    Swal.fire({
        title: 'Cancelar salida',
        text: 'Se revertirán todos los elementos al estado DISPONIBLE.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, cancelar',
        cancelButtonText: 'Volver',
        confirmButtonColor: '#dc3545'
    }).then(r => {
        if (!r.isConfirmed) return;
        post('cancelar', { id_salida_almacen: state.salida.id_salida_almacen })
            .done(function (data) {
                if (data.success) {
                    detenerNFC();
                    resetState();
                    mostrarFase(1);
                    Swal.fire({ toast: true, position: 'top', icon: 'info', timer: 2500, showConfirmButton: false, title: 'Salida cancelada' });
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
    });
}

// ============================================================
// FASE 4 — CONFIRMACIÓN FINAL
// ============================================================
function mostrarFase4() {
    const p = state.progreso;
    const resumen = p ? `${p.total_escaneado} elementos preparados` + (p.total_backup > 0 ? ` (${p.total_backup} backup)` : '') : '';
    document.getElementById('p4-resumen').textContent = resumen;
    mostrarFase(4);
}

// ============================================================
// RESET
// ============================================================
function resetState() {
    state.presupuesto = null;
    state.salida = null;
    state.progreso = null;
    state.elementoReubicacion = null;
    document.getElementById('inp-numero-ppto').value = '';
    document.getElementById('fb-busqueda').classList.add('fb-hidden');
    document.getElementById('nav-numero').textContent = '';
    // Detener cámara si estaba activa y restaurar el botón de activación
    detenerQR();
    const readerEl = document.getElementById('qr-reader');
    const ph = document.getElementById('qr-placeholder');
    if (readerEl) { readerEl.style.display = 'none'; readerEl.innerHTML = ''; }
    if (ph) {
        ph.innerHTML = '<button id="btn-activar-qr" class="btn-app btn-app-primary"><i class="fa fa-camera me-2"></i>Activar Cámara</button>';
        ph.style.display = 'block';
        bindBtnActivarQR();
    }
}

// ============================================================
// UTILIDADES
// ============================================================
function escHtml(str) {
    if (!str) return '';
    return String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;');
}

function escJs(str) {
    if (!str) return '';
    return String(str).replace(/\\/g, '\\\\').replace(/'/g, "\\'");
}

// ============================================================
// EVENT LISTENERS
// ============================================================
function bindBtnActivarQR() {
    // Usar delegación porque el botón puede regenerarse
    $(document).off('click', '#btn-activar-qr').on('click', '#btn-activar-qr', function () {
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Activando...');
        iniciarQR();
    });
}

$(document).ready(function () {

    // --- Diagnóstico visible (pequeño texto gris bajo el lector QR) ---
    const diagEl = document.getElementById('diag-info');
    if (diagEl) {
        const sc = window.isSecureContext;
        const md = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
        diagEl.textContent = 'v' + (typeof PICKING_VER !== 'undefined' ? PICKING_VER : '?') + ' · secure:' + sc + ' · cam:' + md + ' · ' + location.protocol + '//' + location.hostname;
    }

    // --- Aviso HTTPS ---
    // isSecureContext es true en HTTPS, localhost Y cuando chrome://flags lo marca como seguro
    const esSeguro = window.isSecureContext === true;
    if (!esSeguro) {
        // Ocultar tarjeta de cámara: en HTTP no puede funcionar en Android Chrome
        $('#card-qr').hide();

        // Mostrar aviso con instrucciones concretas para Android
        const $aviso = $('#aviso-https');
        $aviso.css({ 'display': 'flex', 'align-items': 'flex-start', 'gap': '12px' });
        $aviso.html(
            '<i class="fa fa-exclamation-triangle" style="font-size:1.8rem;flex-shrink:0;margin-top:2px;"></i>' +
            '<div>' +
            '<strong>Cámara y NFC no disponibles (HTTP)</strong><br>' +
            '<span style="font-size:.87rem;">Android Chrome bloquea la cámara en redes locales sin HTTPS. Opciones:</span>' +
            '<ol style="font-size:.83rem;margin:8px 0 0;padding-left:1.3em;line-height:1.7;">' +
            '<li>Usa la <strong>entrada manual</strong> de abajo ↓</li>' +
            '<li>En Chrome Android abre <code style="background:#fffde7;padding:1px 4px;border-radius:4px;">chrome://flags</code>' +
            ' → busca <em>Insecure origins treated as secure</em>' +
            ' → añade <code style="background:#fffde7;padding:1px 4px;border-radius:4px;word-break:break-all;">' + location.origin + '</code>' +
            ' → reinicia Chrome</li>' +
            '<li>O configura HTTPS en el servidor para uso permanente</li>' +
            '</ol>' +
            '</div>'
        );

        // Auto-foco en la entrada manual para que el usuario pueda escribir directamente
        setTimeout(function () { document.getElementById('inp-numero-ppto').focus(); }, 350);

    } else {
        // Solo enlazar el botón de cámara si la página es segura
        bindBtnActivarQR();
    }

    $('#btn-buscar-ppto').on('click', function () {
        buscarPresupuesto($('#inp-numero-ppto').val().trim());
    });

    $('#inp-numero-ppto').on('keypress', function (e) {
        if (e.key === 'Enter') buscarPresupuesto($(this).val().trim());
    });

    // --- Fase 2 ---
    $('#btn-cambiar-ppto').on('click', function () {
        resetState();
        mostrarFase(1);
    });

    $('#btn-iniciar-picking').on('click', function () {
        if (!state.salida) {
            // Crear salida nueva
            post('iniciar_salida', {
                id_presupuesto: state.presupuesto.id_presupuesto,
                id_version_presupuesto: state.presupuesto.id_version_presupuesto,
                id_usuario: ID_USUARIO,
                numero_presupuesto: state.presupuesto.numero_presupuesto
            }).done(function (data) {
                if (data.success) {
                    state.salida = { id_salida_almacen: data.id_salida_almacen };
                    document.getElementById('btn-ver-mapa').style.display = 'block';
                    iniciarFase3();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        } else {
            iniciarFase3();
        }
    });

    $('#btn-ver-mapa').on('click', function () {
        iniciarFase3();
    });

    // --- Fase 3 ---
    $('#btn-volver-p2').on('click', function () {
        detenerNFC();
        refrescarProgreso();
        mostrarFase(2);
    });

    $('#btn-escanear-manual').on('click', function () {
        const codigo = $('#inp-codigo-elem').val().trim().toUpperCase();
        procesarEscaneo(codigo);
    });

    $('#inp-codigo-elem').on('keypress', function (e) {
        if (e.key === 'Enter') {
            procesarEscaneo($(this).val().trim().toUpperCase());
        }
    });

    $('#btn-nfc').on('click', function () {
        feedback('fb-escaneo', 'NFC activo — acerca el tag', 'info');
    });

    $('#btn-refrescar-lista').on('click', function () {
        cargarElementosEscaneados();
        refrescarProgreso();
    });

    $('#btn-completar').on('click', completarSalida);
    $('#btn-cancelar-salida').on('click', cancelarSalida);

    // --- Botones reubicar en lista (event delegation) ---
    $(document).on('click', '.elem-move', function () {
        var $btn = $(this);
        var id_elemento = parseInt($btn.data('id-elemento'));
        var id_linea_salida = parseInt($btn.data('id-linea'));
        var codigo = $btn.data('codigo');
        var nombre = $btn.data('nombre');
        var ubicacion = $btn.data('ubicacion');
        abrirReubicacionDirecta(id_elemento, id_linea_salida, codigo, nombre, ubicacion);
    });

    // Limpiar backdrop al cerrar modal (fix Bootstrap 4)
    $('#modalReubicacion').on('hidden.bs.modal', function () {
        $('.modal-backdrop').remove();
        $('body').removeClass('modal-open').css('padding-right', '');
    });

    // --- Modal Reubicación ---
    $('#btn-confirmar-reubicacion').on('click', function () {
        const id_ubicacion_destino = parseInt($('#sel-ubicacion-destino').val());
        const observaciones = $('#inp-obs-movimiento').val().trim();

        if (!id_ubicacion_destino) {
            Swal.fire({ toast: true, position: 'top', icon: 'warning', timer: 2000, showConfirmButton: false, title: 'Selecciona una ubicación' });
            return;
        }

        post('registrar_movimiento', {
            id_salida_almacen: state.salida.id_salida_almacen,
            id_elemento: state.elementoReubicacion.id_elemento,
            id_ubicacion_destino,
            id_usuario: ID_USUARIO,
            observaciones: observaciones || null
        }).done(function (data) {
            $('#modalReubicacion').modal('hide');
            if (data.success) {
                vibrar('ok');
                Swal.fire({ toast: true, position: 'top', icon: 'success', timer: 2000, showConfirmButton: false, title: 'Movimiento registrado' });
                cargarElementosEscaneados();
            } else {
                Swal.fire('Error', data.message, 'error');
            }
        });
    });

    // --- Fase 4 ---
    $('#btn-nueva-salida').on('click', function () {
        detenerNFC();
        resetState();
        mostrarFase(1);
    });
});