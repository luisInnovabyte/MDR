<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Kanban Operaciones — MDR</title>

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css" rel="stylesheet">

    <style>
        /* ===== MODO KIOSK ===== */
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            padding: 0;
            background: #0d1117;
            color: #e6edf3;
            font-family: 'Segoe UI', system-ui, sans-serif;
            overflow: hidden;
        }

        /* ===== TOPBAR ===== */
        #topbar {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 20px;
            background: #161b22;
            border-bottom: 2px solid #30363d;
            height: 62px;
            flex-shrink: 0;
        }

        #topbar .brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        #topbar .brand img {
            height: 36px;
            object-fit: contain;
        }
        #topbar .brand h1 {
            font-size: 1.2rem;
            font-weight: 700;
            margin: 0;
            color: #e6edf3;
            letter-spacing: .04em;
        }
        #topbar .brand span.badge-live {
            background: #238636;
            color: #fff;
            font-size: .65rem;
            padding: 2px 7px;
            border-radius: 20px;
            font-weight: 600;
            letter-spacing: .06em;
            text-transform: uppercase;
            animation: pulse-live 2s infinite;
        }
        @keyframes pulse-live {
            0%, 100% { opacity: 1; }
            50%       { opacity: .55; }
        }
        #topbar .semana-badge {
            background: #1c2128;
            border: 1px solid #30363d;
            border-radius: 6px;
            padding: 3px 12px;
            font-size: .75rem;
            color: #8b949e;
            font-weight: 600;
        }
        #topbar .semana-badge span {
            color: #e6edf3;
        }

        #topbar .controls {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        #reloj {
            font-size: 1.5rem;
            font-weight: 700;
            font-variant-numeric: tabular-nums;
            color: #58a6ff;
            letter-spacing: .05em;
            min-width: 90px;
            text-align: center;
        }
        #fecha-hoy {
            font-size: .78rem;
            color: #8b949e;
            text-align: center;
        }
        #countdown-bar {
            font-size: .72rem;
            color: #8b949e;
        }
        #countdown-bar span {
            font-weight: 700;
            color: #d29922;
        }

        /* ===== KANBAN WRAPPER — 7 días ===== */
        #kanban-wrapper {
            display: flex;
            gap: 8px;
            padding: 8px 10px;
            height: calc(100vh - 62px - 22px);
            overflow-x: auto;
            overflow-y: hidden;
        }

        /* ===== COLUMNAS ===== */
        .kanban-col {
            flex: 1;
            min-width: 180px;
            display: flex;
            flex-direction: column;
            background: #161b22;
            border-radius: 8px;
            border: 1px solid #30363d;
            overflow: hidden;
        }

        /* Columna de HOY */
        .kanban-col.hoy {
            border-color: #388bfd;
            box-shadow: 0 0 0 1px #388bfd44;
        }

        .kanban-col-header {
            padding: 9px 12px;
            background: #1c2128;
            border-bottom: 1px solid #30363d;
            flex-shrink: 0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .kanban-col.hoy .kanban-col-header {
            background: #0d1f3c;
            border-bottom-color: #1a3a6e;
        }

        .day-name {
            font-size: .82rem;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #8b949e;
        }
        .kanban-col.hoy .day-name { color: #79c0ff; }

        .day-date {
            font-size: .7rem;
            color: #484f58;
        }
        .kanban-col.hoy .day-date { color: #388bfd; }

        .count-badge {
            font-size: .68rem;
            font-weight: 700;
            padding: 1px 7px;
            border-radius: 20px;
            background: #30363d;
            color: #8b949e;
            min-width: 22px;
            text-align: center;
            flex-shrink: 0;
        }
        .kanban-col.hoy .count-badge { background: #1a3a6e; color: #79c0ff; }

        /* ===== CUERPO COLUMNA ===== */
        .kanban-col-body {
            overflow-y: auto;
            overflow-x: hidden;
            padding: 7px;
            display: flex;
            flex-direction: column;
            gap: 6px;
            flex: 1;
        }
        .kanban-col-body::-webkit-scrollbar { width: 4px; }
        .kanban-col-body::-webkit-scrollbar-thumb { background: #30363d; border-radius: 3px; }

        /* ===== TARJETAS COMPACTAS ===== */
        .evento-card {
            background: #21262d;
            border-radius: 6px;
            border: 1px solid #30363d;
            border-left-width: 3px;
            padding: 9px 11px;
            transition: background .15s;
            cursor: default;
        }
        .evento-card.tipo-montaje    { border-left-color: #e3b341; }
        .evento-card.tipo-en_curso   { border-left-color: #56d364; }
        .evento-card.tipo-desmontaje { border-left-color: #58a6ff; }
        .evento-card:hover { background: #2d333b; }

        .card-top {
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 5px;
            flex-wrap: wrap;
        }

        .tipo-badge {
            font-size: .6rem;
            font-weight: 700;
            padding: 2px 7px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .05em;
            white-space: nowrap;
        }
        .tipo-badge.montaje    { background: #2d2106; color: #e3b341; }
        .tipo-badge.en_curso   { background: #0d2a16; color: #56d364; }
        .tipo-badge.desmontaje { background: #0d1f3c; color: #58a6ff; }

        .estado-badge {
            font-size: .58rem;
            font-weight: 700;
            padding: 2px 6px;
            border-radius: 20px;
            text-transform: uppercase;
            letter-spacing: .04em;
            white-space: nowrap;
        }
        .badge-aprobado  { background: #1a3a6e; color: #58a6ff; }
        .badge-pendiente { background: #2d2106; color: #e3b341; }

        .card-numero {
            font-size: .82rem;
            font-weight: 800;
            letter-spacing: .03em;
            margin-bottom: 3px;
        }
        .card-numero a { color: #e6edf3; text-decoration: none; }
        .card-numero a:hover { color: #79c0ff; text-decoration: underline; }

        .card-nombre {
            font-size: .78rem;
            font-weight: 500;
            color: #c9d1d9;
            margin-bottom: 4px;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            line-height: 1.35;
        }
        .card-cliente {
            font-size: .72rem;
            color: #6e7681;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .card-ubicacion {
            font-size: .68rem;
            color: #484f58;
            margin-top: 3px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        /* Estado vacío */
        .kanban-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 6px;
            font-size: .75rem;
            padding: 28px 0;
            text-align: center;
        }
        .kanban-empty i { font-size: 1.3rem; color: #30363d; }
        .kanban-empty span { color: #30363d; }

        .spinner-kanban {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 28px 0;
        }

        /* ===== Footer info ===== */
        #info-periodo {
            font-size: .68rem;
            color: #484f58;
            text-align: center;
            padding: 3px 0 1px;
            flex-shrink: 0;
            height: 22px;
            line-height: 22px;
        }

        /* ===== Modal ayuda — tema oscuro ===== */
        #modalAyuda .modal-content {
            background: #161b22;
            border: 1px solid #30363d;
            color: #e6edf3;
        }
        #modalAyuda .modal-header {
            background: #1c2128;
            border-bottom: 1px solid #30363d;
        }
        #modalAyuda .modal-header .modal-title {
            font-weight: 700;
            color: #e6edf3;
            font-size: 1.05rem;
        }
        #modalAyuda .modal-header .btn-close {
            filter: invert(1) grayscale(1) brightness(1.5);
        }
        #modalAyuda .modal-body { padding: 22px 26px; }
        #modalAyuda .modal-footer {
            border-top: 1px solid #30363d;
            background: #1c2128;
        }

        .help-tipo-block {
            border-radius: 7px;
            padding: 13px 16px;
            margin-bottom: 10px;
        }
        .help-tipo-block.montaje    { background: #2d2106; border: 1px solid #6e4f0f; }
        .help-tipo-block.en-curso   { background: #0d2a16; border: 1px solid #1a5c2e; }
        .help-tipo-block.desmontaje { background: #0d1f3c; border: 1px solid #1a3a6e; }
        .help-tipo-block .help-tipo-title {
            font-weight: 800;
            font-size: .9rem;
            letter-spacing: .04em;
            text-transform: uppercase;
            margin-bottom: 4px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .help-tipo-block.montaje    .help-tipo-title { color: #e3b341; }
        .help-tipo-block.en-curso   .help-tipo-title { color: #56d364; }
        .help-tipo-block.desmontaje .help-tipo-title { color: #58a6ff; }
        .help-tipo-block p { font-size: .84rem; color: #b1bac4; margin: 0; line-height: 1.5; }

        .help-section-title {
            font-size: .68rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #484f58;
            font-weight: 700;
            margin: 16px 0 8px;
        }
        .help-note {
            background: #21262d;
            border: 1px solid #30363d;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: .82rem;
            color: #8b949e;
            line-height: 1.6;
        }
        .help-note strong { color: #e6edf3; }
    </style>
</head>
<body>

<!-- ===== TOPBAR ===== -->
<div id="topbar">
    <div class="brand">
        <img src="../../public/img/logo.png"
             onerror="this.style.display='none'"
             alt="MDR">
        <div>
            <h1>Kanban Operaciones</h1>
        </div>
        <span class="badge-live ms-2">● LIVE</span>
        <div class="semana-badge ms-3">
            Semana <span id="rango-semana">—</span>
        </div>
    </div>

    <div class="controls">
        <div>
            <div id="reloj">--:--:--</div>
            <div id="fecha-hoy"></div>
        </div>
        <div class="text-end">
            <div id="countdown-bar">
                Próxima actualización en <span id="countdown">05:00</span>
            </div>
            <div class="mt-1 d-flex gap-2 justify-content-end">
                <button class="btn btn-sm btn-outline-secondary" onclick="cargarEventos()" title="Actualizar ahora">
                    <i class="fas fa-sync-alt"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" onclick="toggleFullscreen()" title="Pantalla completa">
                    <i class="fas fa-expand" id="iconFullscreen"></i>
                </button>
                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#modalAyuda" title="Ayuda">
                    <i class="fas fa-question-circle"></i>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- ===== KANBAN 7 DÍAS (ventana deslizante: hoy + 6 días) ===== -->
<div id="kanban-wrapper">

    <!-- Día 0: HOY -->
    <div class="kanban-col" id="kc-0">
        <div class="kanban-col-header">
            <div>
                <div class="day-name"></div>
                <div class="day-date" id="hdr-0"></div>
            </div>
            <span class="count-badge" id="cnt-0">0</span>
        </div>
        <div class="kanban-col-body" id="col-0">
            <div class="spinner-kanban"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>
        </div>
    </div>

    <!-- Día 1 -->
    <div class="kanban-col" id="kc-1">
        <div class="kanban-col-header">
            <div>
                <div class="day-name"></div>
                <div class="day-date" id="hdr-1"></div>
            </div>
            <span class="count-badge" id="cnt-1">0</span>
        </div>
        <div class="kanban-col-body" id="col-1">
            <div class="spinner-kanban"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>
        </div>
    </div>

    <!-- Día 2 -->
    <div class="kanban-col" id="kc-2">
        <div class="kanban-col-header">
            <div>
                <div class="day-name"></div>
                <div class="day-date" id="hdr-2"></div>
            </div>
            <span class="count-badge" id="cnt-2">0</span>
        </div>
        <div class="kanban-col-body" id="col-2">
            <div class="spinner-kanban"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>
        </div>
    </div>

    <!-- Día 3 -->
    <div class="kanban-col" id="kc-3">
        <div class="kanban-col-header">
            <div>
                <div class="day-name"></div>
                <div class="day-date" id="hdr-3"></div>
            </div>
            <span class="count-badge" id="cnt-3">0</span>
        </div>
        <div class="kanban-col-body" id="col-3">
            <div class="spinner-kanban"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>
        </div>
    </div>

    <!-- Día 4 -->
    <div class="kanban-col" id="kc-4">
        <div class="kanban-col-header">
            <div>
                <div class="day-name"></div>
                <div class="day-date" id="hdr-4"></div>
            </div>
            <span class="count-badge" id="cnt-4">0</span>
        </div>
        <div class="kanban-col-body" id="col-4">
            <div class="spinner-kanban"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>
        </div>
    </div>

    <!-- Día 5 -->
    <div class="kanban-col" id="kc-5">
        <div class="kanban-col-header">
            <div>
                <div class="day-name"></div>
                <div class="day-date" id="hdr-5"></div>
            </div>
            <span class="count-badge" id="cnt-5">0</span>
        </div>
        <div class="kanban-col-body" id="col-5">
            <div class="spinner-kanban"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>
        </div>
    </div>

    <!-- Día 6 -->
    <div class="kanban-col" id="kc-6">
        <div class="kanban-col-header">
            <div>
                <div class="day-name"></div>
                <div class="day-date" id="hdr-6"></div>
            </div>
            <span class="count-badge" id="cnt-6">0</span>
        </div>
        <div class="kanban-col-body" id="col-6">
            <div class="spinner-kanban"><div class="spinner-border spinner-border-sm text-secondary" role="status"></div></div>
        </div>
    </div>

</div>

<div id="info-periodo">
    Presupuestos aprobados y pendientes con actividad los próximos 7 días (hoy + 6 días) — Actualización automática cada 5 minutos
</div>

<!-- ===== MODAL AYUDA ===== -->
<div class="modal fade" id="modalAyuda" tabindex="-1" aria-labelledby="modalAyudaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAyudaLabel">
                    <i class="fas fa-question-circle me-2 text-secondary"></i>¿Cómo funciona el Kanban de Operaciones?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">

                <p style="font-size:.88rem;color:#b1bac4;margin-bottom:18px;">
                    Esta pantalla muestra los presupuestos <strong style="color:#56d364">aprobados</strong> y
                    <strong style="color:#e3b341">pendientes de aprobar</strong> con actividad
                    <strong style="color:#e6edf3">esta semana</strong>.
                    Cada columna representa un día (Lunes a Domingo).
                    Un mismo evento puede aparecer en <strong style="color:#e6edf3">varios días</strong> si su actividad se prolonga.
                </p>

                <div class="help-section-title">Tipos de acción por día</div>

                <div class="help-tipo-block montaje">
                    <div class="help-tipo-title"><i class="fas fa-wrench"></i> Montaje</div>
                    <p>Ese día es la fecha de inicio del montaje del evento.</p>
                </div>
                <div class="help-tipo-block en-curso">
                    <div class="help-tipo-title"><i class="fas fa-play-circle"></i> En Curso</div>
                    <p>El evento está activo ese día: el montaje ya comenzó y el desmontaje aún no ha terminado.</p>
                </div>
                <div class="help-tipo-block desmontaje">
                    <div class="help-tipo-title"><i class="fas fa-truck-loading"></i> Desmontaje</div>
                    <p>Ese día es la fecha de recogida y retorno del material al almacén.</p>
                </div>

                <div class="help-section-title">Columna de hoy</div>
                <div class="help-note">
                    La columna del día actual se resalta con un borde <strong style="color:#58a6ff">azul</strong> para facilitar la orientación visual.
                </div>

                <div class="help-section-title">Filtro semanal</div>
                <div class="help-note">
                    Solo aparecen presupuestos cuya actividad (fechas de montaje/desmontaje, o fechas del evento
                    si no hay fechas técnicas) <strong>solapa con la semana actual</strong>.
                    Presupuestos sin ninguna fecha asignada no se muestran.
                </div>

                <div class="help-section-title">Actualización</div>
                <div class="help-note">
                    La pantalla se refresca <strong>automáticamente cada 5 minutos</strong>.
                    Usa el botón <i class="fas fa-sync-alt fa-xs"></i> para actualizar manualmente.
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="kanbanOperaciones.js"></script>
</body>
</html>
