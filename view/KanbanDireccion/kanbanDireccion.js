'use strict';

// ============================================================
// CONFIGURACIÓN
// ============================================================
const REFRESH_INTERVAL_MS = 5 * 60 * 1000; // 5 minutos
const COLUMNAS = ['pendiente', 'proximos', 'en_curso'];

let countdownTotal = REFRESH_INTERVAL_MS / 1000;
let countdownActual = countdownTotal;
let countdownTimer = null;

// ============================================================
// INIT
// ============================================================
$(document).ready(function () {
    iniciarReloj();
    cargarEventos();
    iniciarAutoRefresh();
});

// ============================================================
// RELOJ EN TIEMPO REAL
// ============================================================
function iniciarReloj() {
    const DIAS  = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
    const MESES = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];

    function tick() {
        const now = new Date();
        const hh  = String(now.getHours()).padStart(2, '0');
        const mm  = String(now.getMinutes()).padStart(2, '0');
        const ss  = String(now.getSeconds()).padStart(2, '0');
        $('#reloj').text(`${hh}:${mm}:${ss}`);

        const dia  = DIAS[now.getDay()];
        const fdia = String(now.getDate()).padStart(2, '0');
        const mes  = MESES[now.getMonth()];
        const anio = now.getFullYear();
        $('#fecha-hoy').text(`${dia}, ${fdia} de ${mes} de ${anio}`);
    }
    tick();
    setInterval(tick, 1000);
}

// ============================================================
// CARGA DE EVENTOS
// ============================================================
function cargarEventos() {
    // Mostrar spinners mientras carga
    COLUMNAS.forEach(col => {
        $(`#col-${col}`).html(`
            <div class="spinner-kanban">
                <div class="spinner-border text-${colorPorColumna(col)}" role="status"></div>
            </div>`);
        $(`#cnt-${col}`).text('0');
    });

    $.ajax({
        url    : '../../controller/kanbanDireccion.php?op=listar',
        type   : 'POST',
        dataType: 'json',
        success: function (resp) {
            if (!resp.success) {
                mostrarError('No se pudieron cargar los datos. Reintentando en el próximo ciclo.');
                renderVacios();
                return;
            }
            renderKanban(resp.data);
            resetCountdown();
        },
        error: function () {
            mostrarError('Error de comunicación con el servidor.');
            renderVacios();
        }
    });
}

// ============================================================
// RENDER KANBAN
// ============================================================
function renderKanban(data) {
    const grupos = { pendiente: [], proximos: [], en_curso: [] };
    (data || []).forEach(e => {
        if (grupos[e.columna] !== undefined) grupos[e.columna].push(e);
    });

    COLUMNAS.forEach(col => {
        const eventos = grupos[col];
        const $body   = $(`#col-${col}`);
        $body.empty();
        $(`#cnt-${col}`).text(eventos.length);

        if (eventos.length === 0) {
            $body.html(`
                <div class="kanban-empty">
                    <i class="fas fa-inbox"></i>
                    <span>Sin presupuestos</span>
                </div>`);
            return;
        }

        eventos.forEach(e => $body.append(crearTarjeta(e)));
    });
}

function renderVacios() {
    COLUMNAS.forEach(col => {
        $(`#col-${col}`).html(`
            <div class="kanban-empty">
                <i class="fas fa-exclamation-triangle text-danger"></i>
                <span>Error al cargar</span>
            </div>`);
    });
}

// ============================================================
// CREAR TARJETA
// ============================================================
function crearTarjeta(e) {
    // Badge de estado
    let badgeClass, badgeLabel;
    if (e.estado_codigo === 'ESPE-RESP') {
        badgeClass = 'badge-pendiente';
        badgeLabel = 'Pendiente de aprobar';
    } else if (e.finalizado) {
        badgeClass = 'badge-finalizado';
        badgeLabel = 'Finalizado';
    } else {
        badgeClass = 'badge-aprobado';
        badgeLabel = 'Aprobado';
    }

    // Importe total
    const filaImporte = e.total_presupuesto
        ? `<div class="card-importe">
               <i class="fas fa-euro-sign flex-shrink-0"></i>
               <span>${escHtml(e.total_presupuesto)} €</span>
           </div>`
        : '';

    // Ubicación
    const ubicacion = e.ubicacion
        ? `<div class="card-ubicacion">
               <i class="fas fa-map-marker-alt mt-1 flex-shrink-0"></i>
               <span>${e.ubicacion}</span>
           </div>`
        : '';

    // Fila fecha evento
    let filaEvento = '';
    if (e.fecha_inicio) {
        const rango = e.fecha_fin && e.fecha_fin !== e.fecha_inicio
            ? `${e.fecha_inicio} → ${e.fecha_fin}`
            : e.fecha_inicio;
        filaEvento = `
            <div class="card-fecha-row">
                <span class="label"><i class="fas fa-calendar-alt me-1"></i>Evento</span>
                <span class="val">${rango}</span>
            </div>`;
    }

    // Fila montaje
    const filaMontaje = e.fecha_montaje
        ? `<div class="card-fecha-row">
               <span class="label"><i class="fas fa-tools me-1"></i>Montaje</span>
               <span class="val">${e.fecha_montaje}</span>
           </div>`
        : '';

    // Fila desmontaje
    const filaDesmontaje = e.fecha_desmontaje
        ? `<div class="card-fecha-row">
               <span class="label"><i class="fas fa-truck-loading me-1"></i>Desmontaje</span>
               <span class="val">${e.fecha_desmontaje}</span>
           </div>`
        : '';

    return $(`
        <div class="evento-card">
            <div class="card-numero">${escHtml(e.numero_presupuesto)}</div>
            <span class="card-estado-badge ${badgeClass}">${badgeLabel}</span>
            <div class="card-nombre" title="${escHtml(e.nombre_evento)}">${escHtml(e.nombre_evento)}</div>
            <div class="card-cliente">
                <i class="fas fa-user flex-shrink-0"></i>
                <span>${escHtml(e.nombre_cliente)}</span>
            </div>
            ${filaImporte}
            ${ubicacion}
            <div class="card-fechas">
                ${filaEvento}
                ${filaMontaje}
                ${filaDesmontaje}
            </div>
        </div>`);
}

// ============================================================
// AUTO-REFRESH con COUNTDOWN
// ============================================================
function iniciarAutoRefresh() {
    resetCountdown();
}

function resetCountdown() {
    clearInterval(countdownTimer);
    countdownActual = countdownTotal;
    actualizarCountdownUI();

    countdownTimer = setInterval(function () {
        countdownActual--;
        actualizarCountdownUI();

        if (countdownActual <= 0) {
            clearInterval(countdownTimer);
            cargarEventos();
        }
    }, 1000);
}

function actualizarCountdownUI() {
    const mm = String(Math.floor(countdownActual / 60)).padStart(2, '0');
    const ss = String(countdownActual % 60).padStart(2, '0');
    $('#countdown').text(`${mm}:${ss}`);
}

// ============================================================
// FULLSCREEN
// ============================================================
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen().catch(() => {});
        $('#iconFullscreen').removeClass('fa-expand').addClass('fa-compress');
    } else {
        document.exitFullscreen();
        $('#iconFullscreen').removeClass('fa-compress').addClass('fa-expand');
    }
}

document.addEventListener('fullscreenchange', function () {
    if (!document.fullscreenElement) {
        $('#iconFullscreen').removeClass('fa-compress').addClass('fa-expand');
    }
});

// ============================================================
// HELPERS
// ============================================================
function colorPorColumna(col) {
    const map = { pendiente: 'warning', proximos: 'info', en_curso: 'success' };
    return map[col] || 'secondary';
}

function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#039;');
}

function mostrarError(msg) {
    const toast = $(`
        <div style="position:fixed;bottom:20px;right:20px;z-index:9999;
                    background:#da3633;color:#fff;padding:10px 16px;
                    border-radius:8px;font-size:.85rem;max-width:320px;">
            <i class="fas fa-exclamation-circle me-2"></i>${escHtml(msg)}
        </div>`);
    $('body').append(toast);
    setTimeout(() => toast.fadeOut(400, () => toast.remove()), 6000);
}
