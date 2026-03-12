<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Calendario de Servicios — MDR</title>

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
            overflow: hidden; /* Pantalla fija — el scroll está en las columnas */
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

        /* ===== KANBAN WRAPPER ===== */
        #kanban-wrapper {
            display: flex;
            gap: 14px;
            padding: 14px 20px;
            height: calc(100vh - 62px);
            overflow: hidden;
        }

        /* ===== COLUMNAS ===== */
        .kanban-col {
            flex: 1;
            display: flex;
            flex-direction: column;
            background: #161b22;
            border-radius: 10px;
            border: 1px solid #30363d;
            overflow: hidden;
            min-width: 0;
        }

        .kanban-col-header {
            padding: 12px 16px;
            font-weight: 700;
            font-size: .95rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-bottom: 1px solid #30363d;
            flex-shrink: 0;
        }
        .kanban-col-header .count-badge {
            font-size: .78rem;
            font-weight: 600;
            padding: 2px 9px;
            border-radius: 20px;
            min-width: 28px;
            text-align: center;
        }

        /* Color cabeceras */
        .col-montaje    .kanban-col-header { background: #2d2106; color: #e3b341; border-bottom-color: #6e4f0f; }
        .col-en_curso   .kanban-col-header { background: #0d2a16; color: #56d364; border-bottom-color: #1a5c2e; }
        .col-desmontaje .kanban-col-header { background: #0d1f3c; color: #58a6ff; border-bottom-color: #1a3a6e; }

        .col-montaje    .kanban-col-header .count-badge { background: #6e4f0f; color: #e3b341; }
        .col-en_curso   .kanban-col-header .count-badge { background: #1a5c2e; color: #56d364; }
        .col-desmontaje .kanban-col-header .count-badge { background: #1a3a6e; color: #58a6ff; }

        /* Scrollable body */
        .kanban-col-body {
            overflow-y: auto;
            overflow-x: hidden;
            padding: 10px;
            gap: 10px;
            display: flex;
            flex-direction: column;
            flex: 1;
        }
        .kanban-col-body::-webkit-scrollbar { width: 5px; }
        .kanban-col-body::-webkit-scrollbar-thumb { background: #30363d; border-radius: 4px; }

        /* ===== TARJETAS ===== */
        .evento-card {
            background: #21262d;
            border-radius: 8px;
            border: 1px solid #30363d;
            padding: 14px 16px;
            transition: border-color .2s;
            cursor: default;
        }
        .evento-card:hover { border-color: #58a6ff; }

        .evento-card .card-numero {
            font-size: 1.05rem;
            font-weight: 800;
            letter-spacing: .04em;
            margin-bottom: 4px;
        }
        .col-montaje    .evento-card .card-numero { color: #e3b341; }
        .col-en_curso   .evento-card .card-numero { color: #56d364; }
        .col-desmontaje .evento-card .card-numero { color: #58a6ff; }

        .evento-card .card-nombre {
            font-size: .92rem;
            font-weight: 600;
            color: #e6edf3;
            margin-bottom: 6px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }
        .evento-card .card-cliente {
            font-size: .82rem;
            color: #8b949e;
            margin-bottom: 5px;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .evento-card .card-ubicacion {
            font-size: .8rem;
            color: #8b949e;
            margin-bottom: 6px;
            display: flex;
            align-items: flex-start;
            gap: 6px;
        }
        .evento-card .card-ubicacion span {
            line-height: 1.4;
        }
        .card-fechas {
            margin-top: 8px;
            display: flex;
            flex-direction: column;
            gap: 3px;
        }
        .card-fecha-row {
            font-size: .77rem;
            display: flex;
            align-items: center;
            gap: 6px;
            color: #8b949e;
        }
        .card-fecha-row .label {
            min-width: 80px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-size: .7rem;
            color: #6e7681;
        }
        .card-fecha-row .val {
            color: #e6edf3;
            font-weight: 600;
        }

        /* Estado vacío */
        .kanban-empty {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            gap: 8px;
            color: #30363d;
            font-size: .9rem;
        }
        .kanban-empty i { font-size: 2rem; }

        /* ===== Spinner carga inicial ===== */
        .spinner-kanban {
            display: flex;
            align-items: center;
            justify-content: center;
            flex: 1;
        }

        /* ===== Footer info ===== */
        #info-periodo {
            font-size: .73rem;
            color: #484f58;
            text-align: center;
            padding: 4px 0 2px;
            flex-shrink: 0;
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
        #modalAyuda .modal-body { padding: 24px 28px; }
        #modalAyuda .modal-footer {
            border-top: 1px solid #30363d;
            background: #1c2128;
        }

        /* Bloques de cada columna en la ayuda */
        .help-col-block {
            border-radius: 8px;
            padding: 16px 18px;
            margin-bottom: 14px;
        }
        .help-col-block.montaje    { background: #2d2106; border: 1px solid #6e4f0f; }
        .help-col-block.en-curso   { background: #0d2a16; border: 1px solid #1a5c2e; }
        .help-col-block.desmontaje { background: #0d1f3c; border: 1px solid #1a3a6e; }
        .help-col-block .help-col-title {
            font-weight: 800;
            font-size: 1rem;
            letter-spacing: .05em;
            text-transform: uppercase;
            margin-bottom: 6px;
            display: flex;
            align-items: center;
            gap: 9px;
        }
        .help-col-block.montaje    .help-col-title { color: #e3b341; }
        .help-col-block.en-curso   .help-col-title { color: #56d364; }
        .help-col-block.desmontaje .help-col-title { color: #58a6ff; }
        .help-col-block p {
            font-size: .88rem;
            color: #b1bac4;
            margin: 0;
            line-height: 1.6;
        }
        .help-section-title {
            font-size: .7rem;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #484f58;
            font-weight: 700;
            margin: 18px 0 10px;
        }
        .help-note {
            background: #21262d;
            border: 1px solid #30363d;
            border-radius: 6px;
            padding: 11px 15px;
            font-size: .83rem;
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
            <h1>Calendario de Servicios</h1>
        </div>
        <span class="badge-live ms-2">● LIVE</span>
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

<!-- ===== KANBAN ===== -->
<div id="kanban-wrapper">

    <!-- Columna: MONTAJE -->
    <div class="kanban-col col-montaje">
        <div class="kanban-col-header">
            <span><i class="fas fa-tools me-2"></i>Montaje</span>
            <span class="count-badge" id="cnt-montaje">0</span>
        </div>
        <div class="kanban-col-body" id="col-montaje">
            <div class="spinner-kanban">
                <div class="spinner-border text-warning" role="status"></div>
            </div>
        </div>
    </div>

    <!-- Columna: EN CURSO -->
    <div class="kanban-col col-en_curso">
        <div class="kanban-col-header">
            <span><i class="fas fa-play-circle me-2"></i>En curso</span>
            <span class="count-badge" id="cnt-en_curso">0</span>
        </div>
        <div class="kanban-col-body" id="col-en_curso">
            <div class="spinner-kanban">
                <div class="spinner-border text-success" role="status"></div>
            </div>
        </div>
    </div>

    <!-- Columna: DESMONTAJE -->
    <div class="kanban-col col-desmontaje">
        <div class="kanban-col-header">
            <span><i class="fas fa-truck-loading me-2"></i>Desmontaje</span>
            <span class="count-badge" id="cnt-desmontaje">0</span>
        </div>
        <div class="kanban-col-body" id="col-desmontaje">
            <div class="spinner-kanban">
                <div class="spinner-border text-info" role="status"></div>
            </div>
        </div>
    </div>

</div>

<div id="info-periodo">
    Mostrando eventos aprobados en un rango de ±14 días &mdash; Se actualiza automáticamente cada 5 minutos
</div>

<!-- ===== MODAL AYUDA ===== -->
<div class="modal fade" id="modalAyuda" tabindex="-1" aria-labelledby="modalAyudaLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAyudaLabel">
                    <i class="fas fa-question-circle me-2 text-secondary"></i>¿Cómo funciona el Calendario de Servicios?
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
            </div>
            <div class="modal-body">

                <p style="font-size:.9rem;color:#b1bac4;margin-bottom:20px;">
                    Esta pantalla muestra en tiempo real todos los servicios <strong style="color:#e6edf3">aprobados</strong>
                    que están próximos, en marcha o terminando. Las tarjetas se distribuyen automáticamente en tres columnas
                    según el estado de trabajo en cada momento.
                </p>

                <div class="help-section-title">Las tres columnas</div>

                <div class="help-col-block montaje">
                    <div class="help-col-title"><i class="fas fa-tools"></i> Montaje</div>
                    <p>
                        Servicios cuyo montaje <strong style="color:#e3b341">todavía no ha comenzado</strong>.
                        Aquí también aparecen servicios que aún no tienen fechas asignadas pero ya están aprobados.
                    </p>
                </div>

                <div class="help-col-block en-curso">
                    <div class="help-col-title"><i class="fas fa-play-circle"></i> En curso</div>
                    <p>
                        Servicios que <strong style="color:#56d364">ya han empezado a montarse</strong> y cuyo
                        desmontaje aún no ha concluido. El material está en el lugar del evento.
                    </p>
                </div>

                <div class="help-col-block desmontaje">
                    <div class="help-col-title"><i class="fas fa-truck-loading"></i> Desmontaje</div>
                    <p>
                        Servicios <strong style="color:#58a6ff">recientemente finalizados</strong> y pendientes de
                        confirmación de recogida. Desaparecen de la pantalla pasados 3 días desde que terminaron.
                    </p>
                </div>

                <div class="help-section-title">¿Qué servicios aparecen?</div>

                <div class="help-note">
                    Solo se muestran los servicios en estado <strong>Aprobado</strong> que tengan actividad
                    en una ventana de <strong>±14 días</strong> a partir de hoy: los que empiezan
                    en los próximos 14 días, los que están activos ahora mismo, y los que terminaron
                    hace un máximo de 3 días.
                </div>

                <div class="help-section-title">¿Cada cuánto se actualiza?</div>

                <div class="help-note">
                    La pantalla se refresca <strong>automáticamente cada 5 minutos</strong>. El contador
                    de la barra superior indica cuánto tiempo falta para la próxima actualización.
                    También puedes actualizarla manualmente con el botón <i class="fas fa-sync-alt fa-xs"></i>.
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
<script src="../../public/lib/sweetalert2-11.7.32/sweetalert2.all.min.js"></script>
<script src="calendario.js"></script>

</body>
</html>
