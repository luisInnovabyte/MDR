<?php
// view/Picking/index.php  — App de picking, standalone mobile-first
session_start();
if (!isset($_SESSION['sesion_iniciada']) || $_SESSION['sesion_iniciada'] !== true) {
    header('Location: ../../index.php');
    exit;
}
$idUsuario = (int)($_SESSION['id_usuario'] ?? 0);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#1a237e">
    <title>Picking — MDR</title>

    <link href="../../public/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../public/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* ── Variables ───────────────────────────────── */
        :root {
            --color-brand:   #1a237e;
            --color-prep:    #795548;
            --color-ok:      #198754;
            --color-err:     #dc3545;
            --color-warn:    #fd7e14;
            --safe-top:      env(safe-area-inset-top,    0px);
            --safe-bottom:   env(safe-area-inset-bottom, 0px);
            --safe-left:     env(safe-area-inset-left,   0px);
            --safe-right:    env(safe-area-inset-right,  0px);
            --font-base:     1rem;       /* 16px */
            --touch-min:     52px;       /* altura mínima táctil */
        }

        /* ── Reset / base ────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        html, body {
            height: 100%;
            margin: 0;
            background: #eef0f5;
            font-size: var(--font-base);
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior: none;
        }

        body {
            padding-top: var(--safe-top);
            padding-bottom: calc(var(--safe-bottom) + 76px); /* espacio barra inferior */
            padding-left:  var(--safe-left);
            padding-right: var(--safe-right);
        }

        /* ── AppBar ──────────────────────────────────── */
        #app-bar {
            position: sticky;
            top: 0;
            z-index: 200;
            background: var(--color-brand);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 16px;
            height: 56px;
            box-shadow: 0 2px 6px rgba(0,0,0,.35);
        }
        #app-bar .brand  { font-size: 1.1rem; font-weight: 700; letter-spacing: .02em; display: flex; align-items: center; gap: 10px; }
        #app-bar .sub    { font-size: .78rem; opacity: .7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
        #app-bar .btn-back { color: #fff; background: transparent; border: none; padding: 8px; font-size: 1.25rem; line-height: 1; display: none; }

        /* ── Phases ──────────────────────────────────── */
        .phase { display: none; }
        .phase.active { display: block; }

        /* ── Contenedor general ──────────────────────── */
        .page-wrap { padding: 16px 14px 0; }
        #phase3 .page-wrap { padding-bottom: 140px; }

        /* ── Tarjeta base ────────────────────────────── */
        .app-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.1);
            margin-bottom: 14px;
            overflow: hidden;
        }
        .app-card-body { padding: 14px 16px; }
        .app-card-title {
            font-size: .7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .08em;
            color: #888;
            margin-bottom: 10px;
        }

        /* ── Botones táctiles grandes ────────────────── */
        .btn-app {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: var(--touch-min);
            width: 100%;
            border-radius: 12px;
            font-size: 1.05rem;
            font-weight: 600;
            border: none;
            cursor: pointer;
            transition: filter .15s, transform .1s;
            -webkit-user-select: none;
        }
        .btn-app:active { transform: scale(.97); filter: brightness(.9); }
        .btn-app-primary  { background: var(--color-brand); color: #fff; }

        /* ── Barra sticky acción ─────────────────────── */
        .sticky-action-bar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #eef0f5;
            margin: 6px -14px 0;
            padding: 10px 14px 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,.10);
        }
        .btn-app-success  { background: var(--color-ok);    color: #fff; }
        .btn-app-danger   { background: var(--color-err);   color: #fff; }
        .btn-app-secondary{ background: #e9ecef;            color: #333; }
        .btn-app-outline  { background: transparent; border: 2px solid #c0c4cc; color: #444; }
        .btn-app:disabled { opacity: .45; }

        /* ── Input grande ────────────────────────────── */
        .inp-scan {
            height: var(--touch-min);
            font-size: 1.1rem;
            border-radius: 10px 0 0 10px;
            border: 2px solid #c0c4cc;
            flex: 1;
            padding: 0 14px;
            outline: none;
        }
        .inp-scan:focus { border-color: var(--color-brand); }
        .inp-group { display: flex; margin-bottom: 8px; }
        .inp-group .btn-app { border-radius: 0 10px 10px 0; min-width: 56px; width: auto; }
        .btn-nfc-full { border-radius: 10px !important; width: 100%; margin-bottom: 12px; }

        /* ── Feedback banner ─────────────────────────── */
        .fb-banner {
            display: flex;
            align-items: center;
            gap: 12px;
            min-height: 60px;
            border-radius: 12px;
            padding: 12px 16px;
            font-size: 1rem;
            font-weight: 500;
            margin-bottom: 14px;
        }
        .fb-banner i   { font-size: 1.5rem; flex-shrink: 0; }
        .fb-ok    { background: #d1f2e1; color: #0a3622; }
        .fb-err   { background: #fde8e9; color: #58151c; }
        .fb-warn  { background: #fff3cd; color: #5a3e00; }
        .fb-info  { background: #e0f0ff; color: #073b6f; }
        .fb-hidden { display: none; }

        /* ── Lector QR ───────────────────────────────── */
        #qr-reader {
            width: 100%;
            border-radius: 14px;
            overflow: hidden;
            background: #000;
            margin-bottom: 14px;
        }
        #qr-reader video { width: 100% !important; display: block; }
        /* Ocultar la UI de html5-qrcode que no necesitamos */
        #qr-reader__dashboard_section_csr span,
        #qr-reader__dashboard_section_swaplink { display: none !important; }

        /* ── Separador "o" ───────────────────────────── */
        .sep-or { display: flex; align-items: center; gap: 10px; color: #999; font-size: .85rem; margin: 12px 0; }
        .sep-or::before, .sep-or::after { content: ''; flex: 1; height: 1px; background: #ddd; }

        /* ── Tarjeta-cabecera presupuesto ────────────── */
        .ppto-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            border-left: 5px solid var(--color-brand);
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.1);
            margin-bottom: 14px;
        }
        .ppto-strip .num  { font-size: 1.1rem; font-weight: 700; color: var(--color-brand); }
        .ppto-strip .meta { font-size: .8rem;  color: #666; }
        .ppto-strip .btn-icon { background: none; border: none; color: #666; font-size: 1.2rem; padding: 6px; }

        /* ── Progress grande ─────────────────────────── */
        .prog-wrap { margin-bottom: 14px; }
        .prog-label { display: flex; justify-content: space-between; font-size: .85rem; color: #666; margin-bottom: 6px; }
        .prog-bar-track { height: 20px; border-radius: 10px; background: #e9ecef; overflow: hidden; }
        .prog-bar-fill  { height: 100%; border-radius: 10px; background: var(--color-ok); transition: width .4s ease; }

        /* ── Tarjetas de artículo ────────────────────── */
        .art-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
        }
        .art-row:last-child { border-bottom: none; }
        .art-nom  { font-size: .95rem; font-weight: 600; }
        .art-loc  { font-size: .78rem; color: #888; }
        .art-cnt  {
            font-size: 1.2rem; font-weight: 700;
            min-width: 56px; text-align: center;
            padding: 4px 8px; border-radius: 8px;
            background: #eee; color: #555;
        }
        .art-cnt.done { background: #d1f2e1; color: #0a3622; }
        .art-miniprog { height: 5px; border-radius: 3px; background: #e9ecef; margin-top: 6px; overflow: hidden; }
        .art-miniprog-fill { height: 100%; border-radius: 3px; background: var(--color-ok); transition: width .4s; }

        /* ── Tarjetas de elemento escaneado ──────────── */
        .elem-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            background: #fff;
        }
        .elem-row:last-child { border-bottom: none; }
        .elem-cod  { font-size: .9rem; font-weight: 700; font-family: monospace; }
        .elem-art  { font-size: .78rem; color: #555; }
        .elem-loc  { font-size: .75rem; color: #e65100; }
        .elem-move { background: none; border: 1.5px solid #bbb; border-radius: 8px; padding: 6px 10px; color: #555; font-size: .9rem; min-height: 40px; min-width: 44px; }

        /* ── Badge backup ────────────────────────────── */
        .badge-bkp { font-size: .65rem; background: var(--color-prep); color: #fff; padding: 2px 6px; border-radius: 6px; vertical-align: middle; }

        /* ── Barra inferior fija ─────────────────────── */
        #bottom-bar {
            position: fixed;
            bottom: 0; left: 0; right: 0;
            padding: 10px 14px;
            padding-bottom: calc(10px + var(--safe-bottom));
            background: rgba(255,255,255,.97);
            backdrop-filter: blur(10px);
            box-shadow: 0 -1px 8px rgba(0,0,0,.13);
            z-index: 100;
            display: none;
        }
        #bottom-bar.visible { display: block; }

        /* ── Modal de reubicación ────────────────────── */
        .modal-content { border-radius: 18px 18px 14px 14px; overflow: hidden; }
        .modal-header  { background: var(--color-prep); color: #fff; border-radius: 0; }
        .modal-header .btn-close { filter: invert(1); }
        .modal-select  { height: 52px; font-size: 1rem; border-radius: 10px; }

        /* ── Fase 4 (completada) ─────────────────────── */
        #phase4 { min-height: 70vh; display: none; flex-direction: column; align-items: center; justify-content: center; padding: 32px 24px; text-align: center; }
        #phase4.active { display: flex; }
        .icon-done { font-size: 6rem; color: var(--color-ok); }

        /* ── NFC icon activo ─────────────────────────── */
        .nfc-active { color: var(--color-brand) !important; animation: pulse 1.4s infinite; }
        @keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
    </style>
</head>
<body>

<!-- ============================================================ -->
<!-- APP BAR                                                       -->
<!-- ============================================================ -->
<div id="app-bar">
    <button id="btn-appbar-back" class="btn-back" onclick="appBarBack()">
        <i class="fa fa-chevron-left"></i>
    </button>
    <div class="brand">
        <i class="fa fa-boxes-stacked"></i>
        <span>Picking MDR</span>
    </div>
    <span id="nav-numero" class="sub"></span>
</div>

<!-- ============================================================ -->
<!-- FASE 1 — Escaneo QR del presupuesto                          -->
<!-- ============================================================ -->
<div id="phase1" class="phase active">
    <div class="page-wrap">

        <!-- Aviso HTTP (picking.js lo rellena y muestra si no es HTTPS) -->
        <div id="aviso-https" class="fb-banner fb-warn mb-3" style="display:none;"></div>

        <!-- Lector QR (oculto por picking.js cuando no hay HTTPS) -->
        <div class="app-card" id="card-qr">
            <div class="app-card-body">
                <div class="app-card-title"><i class="fa fa-qrcode me-1"></i> Escanea el QR del presupuesto</div>
                <!-- Botón de activación con onclick directo -->
                <div id="qr-placeholder" class="mb-2">
                    <button id="btn-activar-qr" class="btn-app btn-app-primary" onclick="iniciarQR()">
                        <i class="fa fa-camera me-2"></i> Activar Cámara
                    </button>
                </div>
                <!-- El div de html5-qrcode — oculto hasta que se activa -->
                <div id="qr-reader" style="display:none;"></div>
            </div>
        </div>

        <!-- Diagnóstico inline — no depende de jQuery ni de picking.js -->
        <div id="diag-info" style="background:#1a237e;color:#fff;font-size:.75rem;border-radius:10px;padding:10px 14px;margin-bottom:12px;line-height:1.8;"></div>
        <script>
        (function(){
            var el = document.getElementById('diag-info');
            if(!el) return;
            var sc  = window.isSecureContext;
            var md  = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
            var jq  = (typeof jQuery !== 'undefined') ? jQuery.fn.jquery : 'NO CARGÓ';
            var h5  = (typeof Html5Qrcode !== 'undefined') ? 'OK' : 'NO CARGÓ';
            el.innerHTML =
                '<b>PHP v<?= date("His") ?></b><br>' +
                'isSecureContext: <b>' + sc + '</b><br>' +
                'mediaDevices: <b>' + md + '</b><br>' +
                'jQuery: <b>' + jq + '</b><br>' +
                'Html5Qrcode: <b>' + h5 + '</b><br>' +
                'URL: ' + location.href;
        })();
        </script>

        <!-- Entrada manual -->
        <div class="app-card">
            <div class="app-card-body">
                <div class="app-card-title"><i class="fa fa-keyboard me-1"></i> O introduce el número</div>
                <div class="inp-group">
                    <input type="text" id="inp-numero-ppto" class="inp-scan"
                           placeholder="P-00001/2026" autocomplete="off" inputmode="text">
                    <button class="btn-app btn-app-primary" id="btn-buscar-ppto" style="border-radius:0 10px 10px 0;width:56px;min-width:56px;">
                        <i class="fa fa-magnifying-glass"></i>
                    </button>
                </div>
                <div id="fb-busqueda" class="fb-banner fb-hidden">
                    <i class="fa fa-circle-info"></i><span id="fb-busqueda-text"></span>
                </div>
            </div>
        </div>

    </div>
</div>

<!-- ============================================================ -->
<!-- FASE 2 — Lista de necesidades + progreso                     -->
<!-- ============================================================ -->
<div id="phase2" class="phase">
    <div class="page-wrap">

        <!-- Cabecera presupuesto -->
        <div class="ppto-strip">
            <div>
                <div class="num" id="p2-numero"></div>
                <div class="meta" id="p2-cliente"></div>
                <div class="meta fw-semibold" id="p2-evento"></div>
            </div>
            <button class="btn-icon" id="btn-cambiar-ppto" title="Cambiar presupuesto">
                <i class="fa fa-rotate-left text-secondary"></i>
            </button>
        </div>

        <!-- Barra sticky: botón de acción principal -->
        <div class="sticky-action-bar">
            <button id="btn-iniciar-picking" class="btn-app btn-app-primary">
                <i class="fa fa-tag"></i> Iniciar Escaneo de Elementos
            </button>
        </div>

        <!-- Progreso global -->
        <div class="prog-wrap">
            <div class="prog-label"><span>Material preparado</span><strong id="p2-contadores">0 / 0</strong></div>
            <div class="prog-bar-track"><div id="p2-barra" class="prog-bar-fill" style="width:0%"></div></div>
        </div>

        <!-- Lista artículos -->
        <div class="app-card" id="card-articulos">
            <div id="lista-articulos"></div>
        </div>

        <!-- Botones de acción -->
        <button id="btn-ver-mapa" class="btn-app btn-app-outline mb-3" style="display:none;">
            <i class="fa fa-map-location-dot"></i> Mapa de Ubicaciones
        </button>

    </div>
</div>

<!-- ============================================================ -->
<!-- FASE 3 — Escaneo NFC de elementos                           -->
<!-- ============================================================ -->
<div id="phase3" class="phase">
    <div class="page-wrap">

        <!-- Cabecera compacta -->
        <div class="ppto-strip">
            <div>
                <div class="num small" id="p3-numero"></div>
            </div>
            <span id="p3-contadores" class="badge bg-primary fs-6 px-3 py-2"></span>
        </div>

        <!-- Input escaneo -->
        <div class="app-card">
            <div class="app-card-body">
                <div class="app-card-title"><i class="fa fa-tag me-1"></i> Escanea la etiqueta del elemento</div>
                <div class="inp-group">
                    <input type="text" id="inp-codigo-elem" class="inp-scan text-uppercase"
                           placeholder="Código NFC / manual" autocomplete="off" autocapitalize="characters" inputmode="text">
                    <button class="btn-app btn-app-success" id="btn-escanear-manual" style="border-radius:0 10px 10px 0;width:56px;min-width:56px;">
                        <i class="fa fa-check"></i>
                    </button>
                </div>
                <button class="btn-app btn-app-secondary btn-nfc-full" id="btn-nfc" title="NFC">
                    <i class="fa fa-wifi me-2"></i> Activar lectura NFC
                </button>
                <!-- Feedback escaneo -->
                <div id="fb-escaneo" class="fb-banner fb-info">
                    <i class="fa fa-circle-info"></i>
                    <span>Acerca el tag NFC o introduce el código</span>
                </div>
            </div>
        </div>

        <!-- Lista elementos escaneados -->
        <div class="d-flex justify-content-between align-items-center px-1 mb-2">
            <span style="font-size:.85rem;font-weight:600;color:#555;">Elementos preparados</span>
            <button class="btn-icon" id="btn-refrescar-lista" style="background:none;border:none;font-size:1rem;color:#888;">
                <i class="fa fa-rotate"></i>
            </button>
        </div>
        <div class="app-card">
            <div id="lista-elementos-escaneados"></div>
        </div>

    </div>

    <!-- Botón oculto usado por picking.js para volver a fase 2 -->
    <button id="btn-volver-p2" style="display:none;"></button>

    <!-- Barra inferior fija: Completar / Cancelar -->
    <div id="bottom-bar" class="visible">
        <button id="btn-completar" class="btn-app btn-app-success mb-2" disabled>
            <i class="fa fa-check-double"></i> Completar Salida
        </button>
        <button id="btn-cancelar-salida" class="btn-app btn-app-outline" style="color:var(--color-err); border-color:var(--color-err);">
            Cancelar y devolver elementos
        </button>
    </div>
</div>

<!-- ============================================================ -->
<!-- FASE 4 — Confirmación final                                  -->
<!-- ============================================================ -->
<div id="phase4" class="phase">
    <i class="fa fa-circle-check icon-done mb-3"></i>
    <h3 class="fw-bold mb-2">¡Salida Completada!</h3>
    <p class="text-muted mb-4" id="p4-resumen"></p>
    <button id="btn-nueva-salida" class="btn-app btn-app-primary" style="max-width:280px;">
        <i class="fa fa-plus"></i> Nueva Salida
    </button>
</div>

<!-- ============================================================ -->
<!-- MODAL — Reubicación de elemento                              -->
<!-- ============================================================ -->
<div class="modal fade" id="modalReubicacion" tabindex="-1" aria-labelledby="lblModalReubicacion" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="lblModalReubicacion">
                    <i class="fa fa-location-dot me-2"></i>Reubicar Elemento
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Cerrar"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-1 fw-semibold fs-6" id="reub-elem-nombre"></p>
                <p class="text-muted small mb-3">
                    Ubicación actual: <span id="reub-ubicacion-actual" class="fw-semibold text-dark"></span>
                </p>
                <label class="form-label fw-semibold">Mover a:</label>
                <select id="sel-ubicacion-destino" class="form-select modal-select mb-3"></select>
                <label class="form-label fw-semibold">
                    Observaciones <span class="text-muted fw-normal small">(opcional)</span>
                </label>
                <input type="text" id="inp-obs-movimiento" class="form-control" style="height:48px;font-size:1rem;"
                       placeholder="Ej: zona lateral izquierda">
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn-app btn-app-secondary flex-fill" data-dismiss="modal" style="height:48px;border-radius:10px;">
                    Cancelar
                </button>
                <button type="button" class="btn-app flex-fill" id="btn-confirmar-reubicacion"
                        style="height:48px;border-radius:10px;background:var(--color-prep);color:#fff;">
                    <i class="fa fa-location-dot me-1"></i> Confirmar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="../../public/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="../../public/lib/html5-qrcode/html5-qrcode.min.js"></script>
<script>const ID_USUARIO = <?= $idUsuario; ?>; const PICKING_VER = '<?= date('His'); ?>';</script>
<script src="picking.js?v=<?= time(); ?>"></script>

<script>
// Botón atrás del app bar según fase
function appBarBack() {
    const btnBack = document.getElementById('btn-appbar-back');
    // en fase3 vuelve a fase2
    if (document.getElementById('phase3').classList.contains('active')) {
        document.getElementById('btn-volver-p2')?.click();
    }
}
// Control visibilidad btn-back
document.addEventListener('phaseChange', function(e) {
    const btnBack = document.getElementById('btn-appbar-back');
    btnBack.style.display = (e.detail.phase > 1 && e.detail.phase < 4) ? 'block' : 'none';
    // Ocultar/mostrar barra inferior sólo en fase 3
    const bb = document.getElementById('bottom-bar');
    if (bb) bb.classList.toggle('visible', e.detail.phase === 3);
});
</script>
</body>
</html>