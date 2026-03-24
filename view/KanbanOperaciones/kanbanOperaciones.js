/**
 * kanbanOperaciones.js
 * Kanban semanal de operaciones — MDR ERP
 *
 * 7 columnas (hoy + 6 días siguientes), un evento puede aparecer en varios días
 * Datos: controller/kanbanOperaciones.php?op=listar
 */

'use strict';

/* =====================================================
   CONFIGURACIÓN
   ===================================================== */
const URL_LISTAR    = '../../controller/kanbanOperaciones.php?op=listar';
const INTERVALO_MS  = 5 * 60 * 1000;   // 5 minutos

// Índices de columna 0–6 (0 = hoy, 6 = hoy+6 días)
const DIAS_SEMANA = [0, 1, 2, 3, 4, 5, 6];

/* =====================================================
   RELOJ Y COUNTDOWN
   ===================================================== */
let timerCountdown;
let segundosRestantes = INTERVALO_MS / 1000;

function actualizarReloj() {
    const ahora = new Date();

    const h  = String(ahora.getHours()).padStart(2, '0');
    const m  = String(ahora.getMinutes()).padStart(2, '0');
    const s  = String(ahora.getSeconds()).padStart(2, '0');
    document.getElementById('reloj').textContent = `${h}:${m}:${s}`;

    const dias  = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    const meses = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    document.getElementById('fecha-hoy').textContent =
        `${dias[ahora.getDay()]} ${ahora.getDate()} ${meses[ahora.getMonth()]} ${ahora.getFullYear()}`;
}

function iniciarCountdown() {
    clearInterval(timerCountdown);
    segundosRestantes = INTERVALO_MS / 1000;
    timerCountdown = setInterval(() => {
        segundosRestantes--;
        const mm = String(Math.floor(segundosRestantes / 60)).padStart(2, '0');
        const ss = String(segundosRestantes % 60).padStart(2, '0');
        const el = document.getElementById('countdown');
        if (el) el.textContent = `${mm}:${ss}`;
        if (segundosRestantes <= 0) {
            cargarEventos();
        }
    }, 1000);
}

/**
 * Devuelve array[7] de objetos Date normalizados a 00:00:00
 * índice 0 = hoy, índice 6 = hoy + 6 días
 */
function getDiasSemana() {
    const hoy = new Date();
    hoy.setHours(0, 0, 0, 0);
    const dias = [];
    for (let i = 0; i < 7; i++) {
        const d = new Date(hoy);
        d.setDate(hoy.getDate() + i);
        dias.push(d);
    }
    return dias;
}

/**
 * Convierte string 'YYYY-MM-DD' a Date 00:00:00 local.
 * Devuelve null si el string es falsy o inválido.
 */
function parseDate(str) {
    if (!str) return null;
    const p = str.split('-');
    if (p.length !== 3) return null;
    const d = new Date(+p[0], +p[1] - 1, +p[2]);
    return isNaN(d.getTime()) ? null : d;
}

/**
 * Rellena las cabeceras de fecha/nombre y marca la columna de hoy con .hoy
 */
function inicializarFechasColumnas() {
    const meses   = ['ene','feb','mar','abr','may','jun','jul','ago','sep','oct','nov','dic'];
    const nombres = ['Domingo','Lunes','Martes','Miércoles','Jueves','Viernes','Sábado'];
    const semana  = getDiasSemana();
    const hoy     = new Date(); hoy.setHours(0, 0, 0, 0);

    semana.forEach((d, idx) => {
        const nameEl = document.querySelector(`#kc-${idx} .day-name`);
        if (nameEl) nameEl.textContent = nombres[d.getDay()];
        const hdrEl  = document.getElementById(`hdr-${idx}`);
        if (hdrEl) hdrEl.textContent = `${d.getDate()} ${meses[d.getMonth()]}`;
        const colEl  = document.getElementById(`kc-${idx}`);
        if (colEl) colEl.classList.toggle('hoy', d.getTime() === hoy.getTime());
    });

    // Rango en topbar
    const fmt = d => `${d.getDate()} ${meses[d.getMonth()]}`;
    const el  = document.getElementById('rango-semana');
    if (el) el.textContent = `${fmt(semana[0])} – ${fmt(semana[6])}`;
}

/* =====================================================
   DISTRIBUCIÓN POR DÍAS
   ===================================================== */

/**
 * Para cada evento, determina en qué días de la semana tiene actividad
 * y qué tipo de acción ocurre ese día (montaje / en_curso / desmontaje).
 */
function distribuirEventos(eventos) {
    const semana = getDiasSemana();
    const grupos = {};
    DIAS_SEMANA.forEach(d => grupos[d] = []);

    eventos.forEach(ev => {
        const fmRef = parseDate(ev.fecha_montaje);
        const fdRef = parseDate(ev.fecha_desmontaje);
        const fiRef = parseDate(ev.fecha_inicio);
        const ffRef = parseDate(ev.fecha_fin);

        const inicio = fmRef || fiRef;
        const fin    = fdRef || ffRef || fmRef || fiRef;

        if (!inicio) return; // sin fechas → no se muestra

        semana.forEach((diaDate, idx) => {
            if (diaDate < inicio || diaDate > fin) return;

            let tipo;
            if (fmRef && diaDate.getTime() === fmRef.getTime()) {
                tipo = 'montaje';
            } else if (fdRef && diaDate.getTime() === fdRef.getTime()) {
                tipo = 'desmontaje';
            } else {
                tipo = 'en_curso';
            }

            grupos[DIAS_SEMANA[idx]].push({ ...ev, tipo_dia: tipo });
        });
    });

    return grupos;
}

/* =====================================================
   RENDER DE TARJETAS
   ===================================================== */
const TIPO_LABEL = { montaje: 'Montaje', en_curso: 'En Curso', desmontaje: 'Desmontaje' };
const TIPO_ICON  = { montaje: 'fa-wrench', en_curso: 'fa-play-circle', desmontaje: 'fa-truck-loading' };

function badgeEstado(codigo) {
    if (!codigo) return '';
    const c = codigo.toUpperCase();
    if (c === 'APROB')     return '<span class="estado-badge badge-aprobado">Aprobado</span>';
    if (c === 'ESPE-RESP') return '<span class="estado-badge badge-pendiente">Pdte. resp.</span>';
    return `<span class="estado-badge badge-pendiente">${codigo}</span>`;
}

function crearTarjeta(ev) {
    const tipo      = ev.tipo_dia || 'en_curso';
    const tipoLabel = TIPO_LABEL[tipo] || tipo;
    const tipoIcon  = TIPO_ICON[tipo]  || 'fa-circle';

    const ubicacion = ev.ubicacion
        ? `<div class="card-ubicacion">
               <i class="fas fa-map-marker-alt" style="font-size:.64rem"></i>
               ${ev.ubicacion}
           </div>`
        : '';

    return `
    <div class="evento-card tipo-${tipo}">
        <div class="card-top">
            <span class="tipo-badge ${tipo}">
                <i class="fas ${tipoIcon} me-1"></i>${tipoLabel}
            </span>
            ${badgeEstado(ev.estado_codigo)}
        </div>
        <div class="card-numero">
            <a href="../../view/Presupuesto/index.php?id=${ev.id_presupuesto}"
               target="_blank" rel="noopener"
               title="Abrir presupuesto">${ev.numero}</a>
        </div>
        <div class="card-nombre" title="${ev.nombre_evento || ''}">${ev.nombre_evento || '(Sin nombre)'}</div>
        <div class="card-cliente">
            <i class="fas fa-user" style="font-size:.64rem"></i>
            ${ev.nombre_cliente || '—'}
        </div>
        ${ubicacion}
    </div>`;
}

/* =====================================================
   CARGA DE DATOS
   ===================================================== */
function mostrarSpinners() {
    DIAS_SEMANA.forEach(dia => {
        const el = document.getElementById(`col-${dia}`);
        if (!el) return;
        el.innerHTML = `<div class="spinner-kanban">
            <div class="spinner-border spinner-border-sm text-secondary" role="status"></div>
        </div>`;
        const cnt = document.getElementById(`cnt-${dia}`);
        if (cnt) cnt.textContent = '0';
    });
}

function cargarEventos() {
    iniciarCountdown();
    mostrarSpinners();

    $.getJSON(URL_LISTAR)
        .done(function (resp) {
            if (!resp || !resp.data) {
                mostrarError('Respuesta inesperada del servidor.');
                return;
            }

            inicializarFechasColumnas();
            const grupos = distribuirEventos(resp.data);

            DIAS_SEMANA.forEach(dia => {
                const contenedor = document.getElementById(`col-${dia}`);
                const contador   = document.getElementById(`cnt-${dia}`);
                if (!contenedor) return;

                const lista = grupos[dia];
                if (!lista || lista.length === 0) {
                    contenedor.innerHTML = `
                        <div class="kanban-empty">
                            <i class="fas fa-inbox"></i>
                            <span>Sin actividad</span>
                        </div>`;
                    if (contador) contador.textContent = '0';
                } else {
                    contenedor.innerHTML = lista.map(crearTarjeta).join('');
                    if (contador) contador.textContent = lista.length;
                }
            });
        })
        .fail(function (jqXHR, textStatus) {
            mostrarError(`Error de comunicación: ${textStatus}`);
            console.error('kanbanOperaciones error:', jqXHR.responseText);
        });
}

function mostrarError(msg) {
    DIAS_SEMANA.forEach(dia => {
        const el = document.getElementById(`col-${dia}`);
        if (!el) return;
        el.innerHTML = `<div class="kanban-empty">
            <i class="fas fa-exclamation-triangle" style="color:#f85149"></i>
            <span style="color:#f85149">${msg}</span>
        </div>`;
    });
}

/* =====================================================
   PANTALLA COMPLETA
   ===================================================== */
function toggleFullscreen() {
    const icon = document.getElementById('iconFullscreen');
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
        if (icon) { icon.classList.remove('fa-expand'); icon.classList.add('fa-compress'); }
    } else {
        document.exitFullscreen();
        if (icon) { icon.classList.remove('fa-compress'); icon.classList.add('fa-expand'); }
    }
}

/* =====================================================
   INIT
   ===================================================== */
$(document).ready(function () {
    actualizarReloj();
    setInterval(actualizarReloj, 1000);
    inicializarFechasColumnas();
    cargarEventos();
});
