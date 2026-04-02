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
    ubicacionesDisponibles: [],
    pool: [],               // códigos escaneados pendientes de comparar
    comparacion: null,      // resultado del último comparar_pool
    sustituciones: {}       // {codigo_candidato: id_linea_ppto_faltante}
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
    codigo = codigo.trim().toUpperCase();
    feedback('fb-escaneo', '<i class="fa fa-spinner fa-spin me-2"></i>Consultando <strong>' + escHtml(codigo) + '</strong>...', 'info');
    post('buscar_elemento', { codigo_elemento: codigo })
        .done(function (data) {
            if (!data.success) {
                vibrar('err');
                feedback('fb-escaneo', '<strong>' + escHtml(codigo) + '</strong> — error de comunicación', 'err');
                return;
            }
            if (!data.encontrado) {
                vibrar('err');
                feedback('fb-escaneo', '<strong>' + escHtml(codigo) + '</strong> — elemento no encontrado en el sistema', 'err');
                return;
            }
            agregarAlPool(data.elemento, data.foto_url);
        })
        .fail(function () {
            vibrar('err');
            feedback('fb-escaneo', 'Error de conexión al consultar el elemento', 'err');
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
    if (!cont) return;
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
                    mostrarFase6();
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
// FASE 4 — POOL BUSCADOR + FASE 5 — COMPARACIÓN (nuevo flujo)
// ============================================================

// D3 — Añadir elemento enriquecido al pool
function agregarAlPool(elemento, foto_url) {
    var codigo = elemento.codigo_elemento;
    if (!codigo) return;
    if (state.pool.some(function (item) { return item.codigo === codigo; })) {
        Swal.fire({ toast: true, position: 'top', icon: 'info', timer: 1800, showConfirmButton: false, title: 'Ya está en el pool' });
        return;
    }
    state.pool.push({ codigo: codigo, elemento: elemento, foto_url: foto_url || null });
    vibrar('ok');
    feedback('fb-escaneo', '<strong>' + escHtml(codigo) + '</strong> — ' + escHtml(elemento.nombre_articulo || '') + ' añadido', 'ok');
    mostrarUltimoEscaneado(elemento, foto_url);
    actualizarContadoresPool();
}

// D3b — Mostrar tarjeta del último elemento escaneado en fase 3
function mostrarUltimoEscaneado(elemento, foto_url) {
    var card = document.getElementById('last-scan-card');
    if (!card) return;
    var e = elemento || {};
    var imgEl = document.getElementById('lsc-img');
    if (imgEl) {
        imgEl.innerHTML = foto_url
            ? '<img src="' + escHtml(foto_url) + '" style="width:33vw;height:33vw;object-fit:cover;border-radius:10px;border:1px solid rgba(0,0,0,.1);" alt="" onerror="this.style.display=\'none\'" />'
            : '<div style="width:33vw;height:33vw;border-radius:10px;background:#f0f0f0;display:inline-flex;align-items:center;justify-content:center;color:#bbb;font-size:2.2rem;"><i class="fa fa-image"></i></div>';
    }
    var set = function(id, html) { var el = document.getElementById(id); if (el) el.innerHTML = html; };
    var txt = function(id, val, icon, label) {
        var el = document.getElementById(id);
        if (el) {
            if (val) {
                var prefix = (icon  ? '<i class="fa ' + icon + '" style="margin-right:6px;"></i>' : '')
                           + (label ? '<span style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:#aaa;margin-right:6px;">' + label + '</span>' : '');
                el.innerHTML = prefix + escHtml(val);
                el.style.display = '';
            } else {
                el.style.display = 'none';
            }
        }
    };
    txt('lsc-codigo',  e.codigo_elemento || '',     'fa-hashtag', null);
    var estadoEl = document.getElementById('lsc-estado');
    if (estadoEl) {
        estadoEl.textContent = e.descripcion_estado_elemento || '';
        estadoEl.style.background = e.color_estado_elemento || '#6c757d';
        estadoEl.style.color = '#fff';
    }
    txt('lsc-articulo', e.nombre_articulo || '',    'fa-box',           null);
    txt('lsc-familia',  e.nombre_familia  || '',    'fa-tag',           null);
    txt('lsc-modelo',   e.modelo_elemento || '',    'fa-cube',          'Modelo');
    txt('lsc-serie',    e.numero_serie_elemento ? e.numero_serie_elemento : '', 'fa-barcode', 'Núm. Serie');
    var ubicacion = [e.nave_elemento, e.pasillo_columna_elemento, e.altura_elemento].filter(Boolean).join(' · ');
    txt('lsc-ubicacion', ubicacion,                 'fa-map-marker-alt','Ubicación');
    card.style.display = '';
}

// D5 — Quitar código del pool
function quitarDelPool(codigo) {
    state.pool = state.pool.filter(function (item) { return item.codigo !== codigo; });
    // Si fase4 está activa, re-renderizar la lista
    var ph4 = document.getElementById('phase4');
    if (ph4 && ph4.classList.contains('active')) {
        renderFase4Pool(document.getElementById('pool-search') ? document.getElementById('pool-search').value : '', estadoCampoActivo());
    }
    actualizarContadoresPool();
}

// D5b — Actualizar todos los contadores/botones del pool en fase 3
function actualizarContadoresPool() {
    var n = state.pool.length;
    // Badge del botón 'Elementos escaneados'
    var badge1 = document.getElementById('pool-count-bar');
    if (badge1) badge1.textContent = n;
    // Badge del botón Comparar en bottom-bar
    var badge2 = document.getElementById('pool-count-bar2');
    if (badge2) badge2.textContent = n;
    // Habilitar/deshabilitar btn-comparar-fase3
    var btnCmp3 = document.getElementById('btn-comparar-fase3');
    if (btnCmp3) btnCmp3.disabled = (n === 0);
}

// D5b — Obtener campo de filtro activo
function estadoCampoActivo() {
    var btn = document.getElementById('pool-campo-btn');
    return btn ? (btn.dataset.campo || 'nombre_articulo') : 'nombre_articulo';
}

// D5c — Sugerencias de búsqueda
function renderSugerencias(q) {
    var panel = document.getElementById('pool-suggestions');
    if (!panel) return;
    if (!q || q.length < 1) { cerrarSugerencias(); return; }
    var campo = estadoCampoActivo();
    var ql = q.toLowerCase();
    // Obtener valores únicos del pool que coincidan
    var vistos = {};
    var sugerencias = [];
    state.pool.forEach(function (item) {
        var val = '';
        if (campo === 'nombre_articulo') val = item.elemento.nombre_articulo || '';
        else if (campo === 'nombre_familia') val = item.elemento.nombre_familia || '';
        else val = item.codigo || '';
        if (val && val.toLowerCase().indexOf(ql) !== -1 && !vistos[val]) {
            vistos[val] = true;
            sugerencias.push(val);
        }
    });
    if (!sugerencias.length) { cerrarSugerencias(); return; }
    sugerencias = sugerencias.slice(0, 6); // máximo 6
    var html = sugerencias.map(function (s) {
        // Resaltar la parte que coincide
        var idx = s.toLowerCase().indexOf(ql);
        var highlighted = escHtml(s.substring(0, idx))
            + '<strong>' + escHtml(s.substring(idx, idx + q.length)) + '</strong>'
            + escHtml(s.substring(idx + q.length));
        return '<div class="pool-sug-item" data-val="' + escHtml(s) + '" '
            + 'style="display:flex;align-items:center;gap:10px;padding:12px 16px;cursor:pointer;border-bottom:1px solid #f0f0f0;font-size:.95rem;">'
            + '<i class="fa fa-search" style="color:#bbb;font-size:.85rem;flex-shrink:0;"></i>'
            + '<span>' + highlighted + '</span>'
            + '</div>';
    }).join('');
    panel.innerHTML = html;
    panel.style.display = 'block';
}

function cerrarSugerencias() {
    var panel = document.getElementById('pool-suggestions');
    if (panel) { panel.style.display = 'none'; panel.innerHTML = ''; }
}


// D6 — Enviar pool al servidor y pasar a fase 5
function compararPool() {
    if (!state.pool.length) return;
    feedback('fb-escaneo', 'Comparando...', 'info');
    post('comparar', {
        id_salida_almacen: state.salida.id_salida_almacen,
        codigos: state.pool.map(function (item) { return item.codigo; })
    }).done(function (data) {
        if (!data.success) {
            feedback('fb-escaneo', data.message || 'Error al comparar', 'err');
            return;
        }
        state.comparacion = data;
        state.sustituciones = {};
        renderComparacion(data);
        mostrarFase(5);
    }).fail(function () {
        feedback('fb-escaneo', 'Error de conexión', 'err');
    });
}

// D7 — Renderizar las 4 secciones de comparación en fase 4
function renderComparacion(data) {
    // Correctos
    var correctos = data.correctos || [];
    document.getElementById('badge-correctos').textContent = correctos.length;
    document.getElementById('cmp-correctos-list').innerHTML = correctos.length
        ? correctos.map(function (c) {
            return '<div class="cmp-row">'
                + '<span class="fw-semibold small text-uppercase">' + escHtml(c.codigo_elemento) + '</span>'
                + '<span class="text-muted small ms-2">' + escHtml(c.nombre_articulo || '') + '</span>'
                + (c.estado_invalido ? '<span class="badge bg-warning text-dark ms-1 small"><i class="fa fa-exclamation-triangle"></i> estado</span>' : '')
                + '</div>';
        }).join('')
        : '<p class="text-muted small text-center py-2">Ninguno</p>';

    // Faltan
    var faltan = data.faltan || [];
    document.getElementById('badge-faltan').textContent = faltan.length;
    document.getElementById('cmp-faltan-list').innerHTML = faltan.length
        ? faltan.map(function (f) {
            return '<div class="cmp-row" id="falta-row-' + f.id_linea_ppto + '">'
                + '<div class="fw-semibold small">' + escHtml(f.nombre_articulo || '') + '</div>'
                + '<div class="text-muted small fst-italic">— sin elemento asignado</div>'
                + '<div id="sust-asignado-' + f.id_linea_ppto + '" style="display:none;" class="mt-1">'
                + '<span class="badge bg-info text-dark" id="sust-badge-' + f.id_linea_ppto + '"></span>'
                + '<button class="btn btn-sm btn-link text-danger p-0 ms-2" onclick="quitarSustitucion(' + f.id_linea_ppto + ')">'
                + '<i class="fa fa-times"></i> Quitar</button>'
                + '</div>'
                + '<button class="btn btn-outline-primary btn-sm mt-2" id="btn-sust-' + f.id_linea_ppto + '"'
                + ' onclick="abrirPanelSustitucion(' + f.id_linea_ppto + ', ' + (f.id_familia || 0) + ', \'' + escJs(f.nombre_familia || '') + '\')">'
                + '<i class="fa fa-box me-1"></i> Asignar sustituto \u25bc</button>'
                + '<div class="sust-panel" id="sust-panel-' + f.id_linea_ppto + '"></div>'
                + '</div>';
        }).join('')
        : '<p class="text-muted small text-center py-2">Ninguno</p>';

    // Sobran
    var sobran = data.sobran || [];
    document.getElementById('badge-sobran').textContent = sobran.length;
    document.getElementById('cmp-sobran-list').innerHTML = sobran.length
        ? sobran.map(function (s) {
            return '<div class="cmp-row" id="cand-row-' + escHtml(s.codigo_elemento) + '">'
                + '<span class="fw-semibold small text-uppercase">' + escHtml(s.codigo_elemento) + '</span>'
                + '<span class="text-muted small ms-2">' + escHtml(s.nombre_articulo || '') + '</span>'
                + (s.estado_invalido ? '<span class="badge bg-warning text-dark ms-1 small"><i class="fa fa-exclamation-triangle"></i></span>' : '')
                + '<span class="badge bg-info text-dark ms-1 small" id="badge-cand-' + escHtml(s.codigo_elemento) + '" style="display:none;"></span>'
                + '</div>';
        }).join('')
        : '<p class="text-muted small text-center py-2">Ninguno</p>';

    // No relacionados
    var norel = data.no_relacionados || [];
    document.getElementById('badge-norel').textContent = norel.length;
    document.getElementById('cmp-norel-list').innerHTML = norel.length
        ? norel.map(function (n) {
            return '<div class="cmp-row" id="cand-row-' + escHtml(n.codigo_elemento) + '">'
                + '<span class="fw-semibold small text-uppercase">' + escHtml(n.codigo_elemento) + '</span>'
                + (n.no_encontrado
                    ? '<span class="badge bg-danger ms-1 small">no encontrado</span>'
                    : '<span class="text-muted small ms-2">' + escHtml(n.nombre_articulo || '') + '</span>')
                + (n.estado_invalido ? '<span class="badge bg-warning text-dark ms-1 small"><i class="fa fa-exclamation-triangle"></i></span>' : '')
                + '<span class="badge bg-info text-dark ms-1 small" id="badge-cand-' + escHtml(n.codigo_elemento) + '" style="display:none;"></span>'
                + '</div>';
        }).join('')
        : '<p class="text-muted small text-center py-2">Ninguno</p>';

    actualizarEstadoConfirmar();
}

// D8a — Abrir panel de sustitución para un faltante
function abrirPanelSustitucion(id_linea_ppto, id_familia, nombre_familia) {
    document.querySelectorAll('.sust-panel.open').forEach(function (p) { p.classList.remove('open'); });
    var panel = document.getElementById('sust-panel-' + id_linea_ppto);
    if (!panel) return;
    var asignados = Object.keys(state.sustituciones);
    var candidatos = [].concat(state.comparacion.sobran || [], state.comparacion.no_relacionados || [])
        .filter(function (c) { return !asignados.includes(c.codigo_elemento); });
    renderPanelSustitucion(panel, id_linea_ppto, id_familia, nombre_familia, candidatos, true);
    panel.classList.add('open');
}

function renderPanelSustitucion(panel, id_linea_ppto, id_familia, nombre_familia, candidatos, filtrarFamilia) {
    var filtrados = filtrarFamilia
        ? candidatos.filter(function (c) { return String(c.id_familia) === String(id_familia); })
        : candidatos;
    panel.innerHTML = '<div class="mb-2">'
        + '<span class="sust-chip' + (filtrarFamilia ? ' active' : '') + '"'
        + ' onclick="toggleFiltroFamilia(' + id_linea_ppto + ', ' + id_familia + ', \'' + escJs(nombre_familia) + '\', true)">'
        + '<i class="fa fa-box me-1"></i> Misma familia</span>'
        + '<span class="sust-chip' + (!filtrarFamilia ? ' active' : '') + '"'
        + ' onclick="toggleFiltroFamilia(' + id_linea_ppto + ', ' + id_familia + ', \'' + escJs(nombre_familia) + '\', false)">'
        + '<i class="fa fa-unlock me-1"></i> Todas</span>'
        + '</div>'
        + (filtrados.length === 0
            ? '<p class="text-muted small fst-italic mb-0">Sin candidatos disponibles</p>'
            : filtrados.map(function (c) {
                return '<div class="d-flex justify-content-between align-items-center py-1">'
                    + '<div><span class="fw-semibold small text-uppercase">' + escHtml(c.codigo_elemento) + '</span>'
                    + (c.nombre_articulo ? '<span class="text-muted small ms-1">' + escHtml(c.nombre_articulo) + '</span>' : '')
                    + '</div>'
                    + '<button class="btn btn-sm btn-success ms-2" style="font-size:.78rem;"'
                    + ' onclick="asignarSustitucion(\'' + escJs(c.codigo_elemento) + '\', ' + id_linea_ppto + ')">'
                    + 'Asignar</button>'
                    + '</div>';
            }).join(''));
}

// D8b — Toggle filtro familia / todas
function toggleFiltroFamilia(id_linea_ppto, id_familia, nombre_familia, filtrarFamilia) {
    var panel = document.getElementById('sust-panel-' + id_linea_ppto);
    if (!panel) return;
    var asignados = Object.keys(state.sustituciones);
    var candidatos = [].concat(state.comparacion.sobran || [], state.comparacion.no_relacionados || [])
        .filter(function (c) { return !asignados.includes(c.codigo_elemento); });
    renderPanelSustitucion(panel, id_linea_ppto, id_familia, nombre_familia, candidatos, filtrarFamilia);
}

// D8c — Asignar sustituto a un faltante
function asignarSustitucion(codigo_candidato, id_linea_ppto) {
    state.sustituciones[codigo_candidato] = id_linea_ppto;
    var faltante = (state.comparacion.faltan || []).find(function (f) { return f.id_linea_ppto == id_linea_ppto; });
    var btnSust = document.getElementById('btn-sust-' + id_linea_ppto);
    var asignadoDiv = document.getElementById('sust-asignado-' + id_linea_ppto);
    var badge = document.getElementById('sust-badge-' + id_linea_ppto);
    if (btnSust) btnSust.style.display = 'none';
    if (asignadoDiv) asignadoDiv.style.display = 'block';
    if (badge) badge.textContent = '« cubre: ' + codigo_candidato + ' »';
    var panel = document.getElementById('sust-panel-' + id_linea_ppto);
    if (panel) panel.classList.remove('open');
    var badgeCand = document.getElementById('badge-cand-' + codigo_candidato);
    if (badgeCand) {
        badgeCand.textContent = '« cubre a ' + (faltante ? faltante.nombre_articulo : '?') + ' »';
        badgeCand.style.display = 'inline';
    }
    actualizarEstadoConfirmar();
}

// D8d — Quitar sustituto asignado
function quitarSustitucion(id_linea_ppto) {
    var codigo = Object.keys(state.sustituciones).find(function (k) { return state.sustituciones[k] == id_linea_ppto; });
    if (!codigo) return;
    delete state.sustituciones[codigo];
    var btnSust = document.getElementById('btn-sust-' + id_linea_ppto);
    var asignadoDiv = document.getElementById('sust-asignado-' + id_linea_ppto);
    if (btnSust) btnSust.style.display = 'block';
    if (asignadoDiv) asignadoDiv.style.display = 'none';
    var badgeCand = document.getElementById('badge-cand-' + codigo);
    if (badgeCand) { badgeCand.textContent = ''; badgeCand.style.display = 'none'; }
    actualizarEstadoConfirmar();
}

// D9 — Habilitar/deshabilitar btn-confirmar según faltantes sin cubrir
function contarFaltanSinCubrir() {
    var faltan = state.comparacion ? (state.comparacion.faltan || []) : [];
    return faltan.filter(function (f) {
        return !Object.values(state.sustituciones).some(function (v) { return v == f.id_linea_ppto; });
    }).length;
}

function actualizarEstadoConfirmar() {
    var faltanSinCubrir = contarFaltanSinCubrir();
    var btnConfirmar = document.getElementById('btn-confirmar');
    var alerta = document.getElementById('cmp-alerta-faltan');
    var alertaTexto = document.getElementById('cmp-alerta-texto');
    if (btnConfirmar) btnConfirmar.disabled = false;
    if (faltanSinCubrir === 0) {
        if (alerta) alerta.style.display = 'none';
    } else {
        if (alerta) alerta.style.display = 'flex';
        if (alertaTexto) alertaTexto.textContent = 'Faltan ' + faltanSinCubrir + ' elemento(s) sin cubrir';
    }
}

// D10 — Confirmar pool: construye payload y hace POST
function confirmarPool() {
    if (!state.comparacion) return;
    var pool = [];
    (state.comparacion.correctos || []).forEach(function (c) {
        pool.push({ codigo_elemento: c.codigo_elemento, modo: 'correcto', id_linea_ppto: c.id_linea_ppto });
    });
    (state.comparacion.sobran || []).forEach(function (s) {
        if (state.sustituciones[s.codigo_elemento]) {
            pool.push({ codigo_elemento: s.codigo_elemento, modo: 'sustituye', id_linea_ppto: state.sustituciones[s.codigo_elemento] });
        } else {
            pool.push({ codigo_elemento: s.codigo_elemento, modo: 'backup', id_linea_ppto: s.id_linea_ppto });
        }
    });
    (state.comparacion.no_relacionados || []).forEach(function (n) {
        if (state.sustituciones[n.codigo_elemento]) {
            pool.push({ codigo_elemento: n.codigo_elemento, modo: 'sustituye', id_linea_ppto: state.sustituciones[n.codigo_elemento] });
        } else {
            pool.push({ codigo_elemento: n.codigo_elemento, modo: 'backup', id_linea_ppto: null });
        }
    });
    post('confirmar', {
        id_salida_almacen: state.salida.id_salida_almacen,
        pool: JSON.stringify(pool)
    }).done(function (data) {
        if (data.success) {
            detenerNFC();
            mostrarFase6();
        } else {
            Swal.fire('Error', data.message || 'Error al confirmar', 'error');
        }
    }).fail(function () {
        Swal.fire('Error', 'Error de conexión', 'error');
    });
}

// ============================================================
// FASE 6 — CONFIRMACIÓN FINAL
// ============================================================
function mostrarFase6() {
    const p = state.progreso;
    const resumen = p ? `${p.total_escaneado} elementos preparados` + (p.total_backup > 0 ? ` (${p.total_backup} backup)` : '') : '';
    document.getElementById('p6-resumen').textContent = resumen;
    mostrarFase(6);
}

// ============================================================
// FASE 4 — POOL CON BUSCADOR
// ============================================================
function mostrarFase4Pool() {
    var searchEl = document.getElementById('pool-search');
    if (searchEl) searchEl.value = '';
    // Resetear dropdown a Artículo
    var btn = document.getElementById('pool-campo-btn');
    var lbl = document.getElementById('pool-campo-label');
    if (btn) btn.dataset.campo = 'nombre_articulo';
    if (lbl) lbl.textContent = 'Artículo';
    cerrarSugerencias();
    renderFase4Pool('', 'nombre_articulo');
    mostrarFase(4);
}

function renderFase4Pool(query, campo) {
    var lista = document.getElementById('phase4-pool-lista');
    var btnComparar = document.getElementById('btn-comparar');
    if (!lista) return;
    var q = (query || '').toLowerCase().trim();
    var filtrados = state.pool.filter(function (item) {
        if (!q) return true;
        var e = item.elemento || {};
        if (campo === 'nombre_familia') return (e.nombre_familia || '').toLowerCase().includes(q);
        if (campo === 'codigo') return (item.codigo || '').toLowerCase().includes(q) || (e.modelo_elemento || '').toLowerCase().includes(q);
        // default: nombre_articulo
        return (e.nombre_articulo || '').toLowerCase().includes(q);
    });
    if (!filtrados.length) {
        lista.innerHTML = '<p class="text-muted small text-center py-3">' + (q ? 'Sin resultados para «' + escHtml(q) + '»' : 'No hay ningún elemento escaneado') + '</p>';
    } else {
        lista.innerHTML = filtrados.map(function (item) {
            var e = item.elemento || {};
            var estadoColor = e.color_estado_elemento || '#6c757d';
            var noAlquiler  = e.permite_alquiler_estado_elemento == 0;
            var ubicacion = [e.nave_elemento, e.pasillo_columna_elemento, e.altura_elemento].filter(Boolean).join(' · ');

            // — Imagen para el detalle expandido (28vw × 28vw)
            var imgDetalle = item.foto_url
                ? '<img src="' + escHtml(item.foto_url) + '" style="width:28vw;height:28vw;object-fit:cover;border-radius:10px;border:1px solid rgba(0,0,0,.1);flex-shrink:0;" alt="" onerror="this.style.display=\'none\'" />'
                : '<div style="width:28vw;height:28vw;border-radius:10px;background:#f0f0f0;display:inline-flex;align-items:center;justify-content:center;color:#bbb;font-size:2rem;flex-shrink:0;"><i class="fa fa-image"></i></div>';

            // — Helper para campo con ícono + label en el detalle
            function campo(val, icon, label) {
                if (!val) return '';
                var prefix = '<i class="fa ' + icon + '" style="margin-right:6px;"></i>'
                           + (label ? '<span style="font-size:.72rem;text-transform:uppercase;letter-spacing:.06em;color:#aaa;margin-right:6px;">' + label + '</span>' : '');
                return prefix + escHtml(val);
            }

            // — Fila compacta (siempre visible)
            var filaCompacta = '<div class="pool-row' + (noAlquiler ? ' pool-row-warn' : '') + '">'
                + '<div class="pool-row-info">'
                + '<div style="font-size:.95rem;font-weight:700;text-transform:uppercase;letter-spacing:.03em;">' + escHtml(item.codigo) + '</div>'
                + '<div class="text-muted" style="font-size:.78rem;">' + escHtml(e.nombre_articulo || '') + '</div>'
                + '</div>'
                + '<div style="display:flex;align-items:center;gap:6px;flex-shrink:0;">'
                + (noAlquiler ? '<span class="badge bg-warning text-dark" style="font-size:.68rem;"><i class="fa fa-exclamation-triangle"></i></span>' : '')
                + '<span class="badge" style="background:' + estadoColor + ';color:#fff;font-size:.7rem;">' + escHtml(e.descripcion_estado_elemento || '') + '</span>'
                + '<span class="pool-expand-btn"><i class="fa fa-chevron-down"></i></span>'
                + '</div>'
                + '</div>';

            // — Panel de detalle (oculto por defecto, similar a last-scan-card)
            var panelDetalle = '<div class="pool-detail" style="display:none;">'
                // Fila imagen + info principal
                + '<div style="display:flex;align-items:center;padding:10px 12px 8px;gap:0;">'
                + '<div style="flex-shrink:0;margin-right:12px;">' + imgDetalle + '</div>'
                + '<div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:5px;justify-content:center;">'
                + '<div><span class="badge" style="background:' + estadoColor + ';color:#fff;font-size:.78rem;">' + escHtml(e.descripcion_estado_elemento || '') + '</span></div>'
                + '<div style="font-size:1rem;font-weight:700;text-transform:uppercase;">' + escHtml(item.codigo) + '</div>'
                + '<div style="font-size:.92rem;font-weight:600;">' + escHtml(e.nombre_articulo || '') + '</div>'
                + (e.nombre_familia ? '<div class="text-muted" style="font-size:.82rem;"><i class="fa fa-tag" style="margin-right:6px;"></i>' + escHtml(e.nombre_familia) + '</div>' : '')
                + '</div>'
                + '</div>'
                // Separador
                + '<div style="border-top:1px solid #f0f0f0;margin:0 12px;"></div>'
                // Fila modelo + serie + ubicación
                + '<div style="padding:8px 12px 10px;display:flex;flex-direction:column;gap:5px;">'
                + '<div style="display:flex;flex-wrap:wrap;align-items:baseline;gap:0 16px;row-gap:5px;">'
                + (e.modelo_elemento      ? '<div class="text-muted" style="font-size:.82rem;">' + campo(e.modelo_elemento, 'fa-cube', 'Modelo') + '</div>' : '')
                + (e.numero_serie_elemento ? '<div class="text-muted" style="font-size:.82rem;word-break:break-word;">' + campo(e.numero_serie_elemento, 'fa-barcode', 'Núm. Serie') + '</div>' : '')
                + '</div>'
                + (ubicacion ? '<div class="text-muted" style="font-size:.82rem;word-break:break-word;">' + campo(ubicacion, 'fa-map-marker-alt', 'Ubicación') + '</div>' : '')
                + '</div>'
                // Botón descartar
                + '<div style="padding:0 12px 12px;">'
                + '<button class="pool-quitar-btn" type="button" data-codigo="' + escHtml(item.codigo) + '"><i class="fa fa-trash" style="margin-right:6px;"></i>Descartar elemento</button>'
                + '</div>'
                + '</div>';

            return '<div class="pool-item-wrap">' + filaCompacta + panelDetalle + '</div>';
        }).join('');
    }
    if (btnComparar) {
        btnComparar.disabled = state.pool.length === 0;
        btnComparar.innerHTML = '<i class="fa fa-balance-scale me-1"></i> Comparar (' + state.pool.length + ')';
    }
    actualizarContadoresPool();
}

// ============================================================
// RESET
// ============================================================
function resetState() {
    state.presupuesto = null;
    state.salida = null;
    state.progreso = null;
    state.elementoReubicacion = null;
    state.pool = [];
    state.comparacion = null;
    state.sustituciones = {};
    actualizarContadoresPool();
    // Ocultar tarjeta último escaneado
    var lsc = document.getElementById('last-scan-card');
    if (lsc) lsc.style.display = 'none';
    // Limpiar búsqueda en fase4 si existe
    var searchEl = document.getElementById('pool-search');
    if (searchEl) searchEl.value = '';
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
                    iniciarFase3();
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            });
        } else {
            iniciarFase3();
        }
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

    // --- Fase 4 — Pool buscador ---
    // El botón btn-comparar vive en #phase4, se registra por delegación por si el DOM tarda
    $(document).on('click', '#btn-comparar', compararPool);
    $('#btn-volver-escaneo').on('click', function () { mostrarFase(3); });
    $('#btn-confirmar').on('click', function () {
        var n = contarFaltanSinCubrir();
        if (n === 0) { confirmarPool(); return; }
        Swal.fire({
            title: '¿Confirmar con faltantes?',
            text: 'Hay ' + n + ' elemento(s) sin cubrir. ¿Continuar igualmente?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Confirmar de todas formas',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (result.isConfirmed) confirmarPool();
        });
    });

    // Buscador en fase4
    $(document).on('input', '#pool-search', function () {
        var q = this.value;
        renderFase4Pool(q, estadoCampoActivo());
        renderSugerencias(q);
    });
    // Expandir/colapsar detalle al clicar la fila compacta
    $(document).on('click', '.pool-item-wrap .pool-row', function () {
        var wrap = $(this).closest('.pool-item-wrap');
        var detail = wrap.find('.pool-detail')[0];
        if (!detail) return;
        var isOpen = detail.style.display !== 'none';
        detail.style.display = isOpen ? 'none' : '';
        wrap.find('.pool-expand-btn').toggleClass('open', !isOpen);
    });
    // Descartar elemento del pool con confirmación
    $(document).on('click', '.pool-quitar-btn', function () {
        var codigo = this.dataset.codigo;
        Swal.fire({
            title: '¿Descartar elemento?',
            text: codigo,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, descartar',
            cancelButtonText: 'Cancelar'
        }).then(function (result) {
            if (result.isConfirmed) quitarDelPool(codigo);
        });
    });
    // Toggle menú campo
    $(document).on('click', '#pool-campo-btn', function (e) {
        e.stopPropagation();
        var menu = document.getElementById('pool-campo-menu');
        if (menu) menu.style.display = menu.style.display === 'none' ? 'block' : 'none';
    });
    // Cerrar sugerencias y menú de campo al hacer clic fuera
    $(document).on('click', function (e) {
        if (!$(e.target).closest('#pool-search-wrap').length) {
            cerrarSugerencias();
            var menu = document.getElementById('pool-campo-menu');
            if (menu) menu.style.display = 'none';
        }
    });
    // Seleccionar sugerencia
    $(document).on('click', '.pool-sug-item', function () {
        var val = this.dataset.val;
        var searchEl = document.getElementById('pool-search');
        if (searchEl) { searchEl.value = val; }
        cerrarSugerencias();
        renderFase4Pool(val, estadoCampoActivo());
    });
    // Opciones de campo en dropdown fase4
    $(document).on('click', '.pool-campo-opt', function (e) {
        e.preventDefault();
        var campo = this.dataset.campo;
        var label = this.textContent.trim();
        var btn = document.getElementById('pool-campo-btn');
        var lbl = document.getElementById('pool-campo-label');
        if (btn) btn.dataset.campo = campo;
        if (lbl) lbl.textContent = label;
        var menu = document.getElementById('pool-campo-menu');
        if (menu) menu.style.display = 'none';
        var searchEl = document.getElementById('pool-search');
        renderFase4Pool(searchEl ? searchEl.value : '', campo);
        cerrarSugerencias();
    });

    // --- Fase 6 ---
    $('#btn-nueva-salida').on('click', function () {
        detenerNFC();
        resetState();
        mostrarFase(1);
    });
});