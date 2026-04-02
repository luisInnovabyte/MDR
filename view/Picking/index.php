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
            --color-brand: #1a237e;
            --color-prep: #795548;
            --color-ok: #198754;
            --color-err: #dc3545;
            --color-warn: #fd7e14;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
            --font-base: 1rem;
            /* 16px */
            --touch-min: 52px;
            /* altura mínima táctil */
        }

        /* ── Reset / base ────────────────────────────── */
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html,
        body {
            height: 100%;
            margin: 0;
            background: #eef0f5;
            font-size: var(--font-base);
            -webkit-tap-highlight-color: transparent;
            overscroll-behavior: none;
        }

        body {
            padding-top: var(--safe-top);
            padding-bottom: calc(var(--safe-bottom) + 76px);
            /* espacio barra inferior */
            padding-left: var(--safe-left);
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
            box-shadow: 0 2px 6px rgba(0, 0, 0, .35);
        }

        #app-bar .brand {
            font-size: 1.1rem;
            font-weight: 700;
            letter-spacing: .02em;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        #app-bar .sub {
            font-size: .78rem;
            opacity: .7;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            max-width: 160px;
        }

        #app-bar .btn-back {
            color: #fff;
            background: transparent;
            border: none;
            padding: 8px;
            font-size: 1.25rem;
            line-height: 1;
            display: none;
        }

        /* ── Phases ──────────────────────────────────── */
        .phase {
            display: none;
        }

        .phase.active {
            display: block;
        }

        /* ── Contenedor general ──────────────────────── */
        .page-wrap {
            padding: 16px 14px 0;
        }

        #phase3 .page-wrap {
            padding-bottom: 140px;
        }

        /* ── Tarjeta base ────────────────────────────── */
        .app-card {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .1);
            margin-bottom: 14px;
            overflow: hidden;
        }

        .app-card-body {
            padding: 14px 16px;
        }

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

        .btn-app:active {
            transform: scale(.97);
            filter: brightness(.9);
        }

        .btn-app-primary {
            background: var(--color-brand);
            color: #fff;
        }

        /* ── Barra sticky acción ─────────────────────── */
        .sticky-action-bar {
            position: sticky;
            top: 0;
            z-index: 20;
            background: #eef0f5;
            margin: 6px -14px 0;
            padding: 10px 14px 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .10);
        }

        .btn-app-success {
            background: var(--color-ok);
            color: #fff;
        }

        .btn-app-danger {
            background: var(--color-err);
            color: #fff;
        }

        .btn-app-secondary {
            background: #e9ecef;
            color: #333;
        }

        .btn-app-outline {
            background: transparent;
            border: 2px solid #c0c4cc;
            color: #444;
        }

        .btn-app:disabled {
            opacity: .45;
        }

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

        .inp-scan:focus {
            border-color: var(--color-brand);
        }

        .inp-group {
            display: flex;
            margin-bottom: 8px;
        }

        .inp-group .btn-app {
            border-radius: 0 10px 10px 0;
            min-width: 56px;
            width: auto;
        }

        .btn-nfc-full {
            border-radius: 10px !important;
            width: 100%;
            margin-bottom: 12px;
        }

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

        .fb-banner i {
            font-size: 1.5rem;
            flex-shrink: 0;
        }

        .fb-ok {
            background: #d1f2e1;
            color: #0a3622;
        }

        .fb-err {
            background: #fde8e9;
            color: #58151c;
        }

        .fb-warn {
            background: #fff3cd;
            color: #5a3e00;
        }

        .fb-info {
            background: #e0f0ff;
            color: #073b6f;
        }

        .fb-hidden {
            display: none;
        }

        /* ── Lector QR ───────────────────────────────── */
        #qr-reader {
            width: 100%;
            border-radius: 14px;
            overflow: hidden;
            background: #000;
            margin-bottom: 14px;
        }

        #qr-reader video {
            width: 100% !important;
            display: block;
        }

        /* Ocultar la UI de html5-qrcode que no necesitamos */
        #qr-reader__dashboard_section_csr span,
        #qr-reader__dashboard_section_swaplink {
            display: none !important;
        }

        /* ── Separador "o" ───────────────────────────── */
        .sep-or {
            display: flex;
            align-items: center;
            gap: 10px;
            color: #999;
            font-size: .85rem;
            margin: 12px 0;
        }

        .sep-or::before,
        .sep-or::after {
            content: '';
            flex: 1;
            height: 1px;
            background: #ddd;
        }

        /* ── Tarjeta-cabecera presupuesto ────────────── */
        .ppto-strip {
            display: flex;
            justify-content: space-between;
            align-items: center;
            background: #fff;
            border-left: 5px solid var(--color-brand);
            border-radius: 12px;
            padding: 12px 14px;
            box-shadow: 0 1px 4px rgba(0, 0, 0, .1);
            margin-bottom: 14px;
        }

        .ppto-strip .num {
            font-size: 1.1rem;
            font-weight: 700;
            color: var(--color-brand);
        }

        .ppto-strip .meta {
            font-size: .8rem;
            color: #666;
        }

        .ppto-strip .btn-icon {
            background: none;
            border: none;
            color: #666;
            font-size: 1.2rem;
            padding: 6px;
        }

        /* ── Progress grande ─────────────────────────── */
        .prog-wrap {
            margin-bottom: 14px;
        }

        .prog-label {
            display: flex;
            justify-content: space-between;
            font-size: .85rem;
            color: #666;
            margin-bottom: 6px;
        }

        .prog-bar-track {
            height: 20px;
            border-radius: 10px;
            background: #e9ecef;
            overflow: hidden;
        }

        .prog-bar-fill {
            height: 100%;
            border-radius: 10px;
            background: var(--color-ok);
            transition: width .4s ease;
        }

        /* ── Tarjetas de artículo ────────────────────── */
        .art-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
        }

        .art-row:last-child {
            border-bottom: none;
        }

        .art-nom {
            font-size: .95rem;
            font-weight: 600;
        }

        .art-loc {
            font-size: .78rem;
            color: #888;
        }

        .art-cnt {
            font-size: 1.2rem;
            font-weight: 700;
            min-width: 56px;
            text-align: center;
            padding: 4px 8px;
            border-radius: 8px;
            background: #eee;
            color: #555;
        }

        .art-cnt.done {
            background: #d1f2e1;
            color: #0a3622;
        }

        .art-miniprog {
            height: 5px;
            border-radius: 3px;
            background: #e9ecef;
            margin-top: 6px;
            overflow: hidden;
        }

        .art-miniprog-fill {
            height: 100%;
            border-radius: 3px;
            background: var(--color-ok);
            transition: width .4s;
        }

        /* ── Tarjetas de elemento escaneado ──────────── */
        .elem-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid #f0f0f0;
            background: #fff;
        }

        .elem-row:last-child {
            border-bottom: none;
        }

        .elem-cod {
            font-size: .9rem;
            font-weight: 700;
            font-family: monospace;
        }

        .elem-art {
            font-size: .78rem;
            color: #555;
        }

        .elem-loc {
            font-size: .75rem;
            color: #e65100;
        }

        .elem-move {
            background: none;
            border: 1.5px solid #bbb;
            border-radius: 8px;
            padding: 6px 10px;
            color: #555;
            font-size: .9rem;
            min-height: 40px;
            min-width: 44px;
        }

        /* ── Badge backup ────────────────────────────── */
        .badge-bkp {
            font-size: .65rem;
            background: var(--color-prep);
            color: #fff;
            padding: 2px 6px;
            border-radius: 6px;
            vertical-align: middle;
        }

        /* ── Barra inferior fija ─────────────────────── */
        #bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 14px;
            padding-bottom: calc(10px + var(--safe-bottom));
            background: rgba(255, 255, 255, .97);
            backdrop-filter: blur(10px);
            box-shadow: 0 -1px 8px rgba(0, 0, 0, .13);
            z-index: 100;
            display: none;
        }

        #bottom-bar.visible {
            display: block;
        }

        /* ── Modal de reubicación ────────────────────── */
        .modal-content {
            border-radius: 18px 18px 14px 14px;
            overflow: hidden;
        }

        .modal-header {
            background: var(--color-prep);
            color: #fff;
            border-radius: 0;
        }

        .modal-header .btn-close {
            filter: invert(1);
        }

        .modal-select {
            height: 52px;
            font-size: 1rem;
            border-radius: 10px;
        }

        /* ── Barra inferior comparación (fase 5) ──────── */
        #bottom-bar-cmp {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 14px;
            padding-bottom: calc(10px + var(--safe-bottom));
            background: rgba(255, 255, 255, .97);
            backdrop-filter: blur(10px);
            box-shadow: 0 -1px 8px rgba(0, 0, 0, .13);
            z-index: 100;
            display: none;
        }

        #bottom-bar-cmp.visible {
            display: block;
        }

        #phase4 .page-wrap,
        #phase5 .page-wrap {
            padding-bottom: 90px;
        }

        /* ── Comparación ─────────────────────────────── */
        .cmp-chevron {
            font-size: .75rem;
            color: #888;
            transition: transform .2s;
        }

        .cmp-header:not(.collapsed) .cmp-chevron {
            transform: rotate(180deg);
        }

        .cmp-row {
            padding: 10px 16px;
            border-bottom: 1px solid rgba(0,0,0,.06);
            font-size: .88rem;
            line-height: 1.4;
        }

        .cmp-row:last-child { border-bottom: none; }

        .sust-panel {
            background: #eef2ff;
            border-top: 1px dashed #c5cae9;
            padding: 10px 14px;
            display: none;
        }

        .sust-panel.open { display: block; }

        .sust-chip {
            display: inline-flex;
            align-items: center;
            padding: 4px 12px;
            border-radius: 20px;
            border: 1px solid #1a237e;
            font-size: .78rem;
            cursor: pointer;
            margin-right: 6px;
            margin-bottom: 6px;
            background: #fff;
            color: #1a237e;
            user-select: none;
        }

        .sust-chip.active {
            background: #1a237e;
            color: #fff;
        }

        /* ── Filas del pool (fase 4) ─────────────────── */
        .pool-item-wrap {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 1px 4px rgba(0,0,0,.1);
            margin-bottom: 10px;
            overflow: hidden;
        }

        .pool-item-wrap:last-child { margin-bottom: 0; }

        .pool-row {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 12px;
            cursor: pointer;
            user-select: none;
        }

        .pool-row.pool-row-warn {
            background: #fff8e1;
        }

        .pool-expand-btn {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            border: none;
            background: #f0f2f5;
            color: #666;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            padding: 0;
            flex-shrink: 0;
            transition: background .15s;
        }

        .pool-expand-btn:hover { background: #e2e5ea; }

        .pool-expand-btn .fa {
            transition: transform .2s;
        }

        .pool-expand-btn.open .fa {
            transform: rotate(180deg);
        }

        .pool-detail {
            border-top: 1px solid #f0f0f0;
        }

        .pool-quitar-btn {
            width: 100%;
            padding: 10px;
            border: none;
            border-radius: 8px;
            background: #fff0f0;
            color: #dc3545;
            font-size: .88rem;
            font-weight: 600;
            cursor: pointer;
            transition: background .15s;
        }

        .pool-quitar-btn:hover { background: #fde0e0; }

        .pool-thumb {
            width: 54px;
            height: 54px;
            object-fit: cover;
            border-radius: 6px;
            flex-shrink: 0;
            border: 1px solid rgba(0,0,0,.08);
        }

        .pool-thumb-empty {
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f0f0f0;
            color: #999;
            font-size: 1.3rem;
        }

        .pool-row-info {
            flex: 1;
            min-width: 0;
        }

        .pool-row-info > div {
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* ── Nueva fase 4: pool + buscador ────────────── */
        #pool-search-wrap {
            position: relative;
            display: flex;
            align-items: stretch;
            margin-bottom: 10px;
        }

        #pool-search:focus { border-color: var(--color-brand); }

        .pool-filter-btn {
            display: inline-flex;
            align-items: center;
            padding: 6px 14px;
            border-radius: 20px;
            border: 1.5px solid #c0c4cc;
            font-size: .8rem;
            font-weight: 600;
            background: #fff;
            color: #555;
            cursor: pointer;
            white-space: nowrap;
            transition: background .15s, color .15s;
        }

        .pool-filter-btn.active {
            background: var(--color-brand);
            color: #fff;
            border-color: var(--color-brand);
        }

        #bottom-bar-pool {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            padding: 10px 14px;
            padding-bottom: calc(10px + var(--safe-bottom));
            background: rgba(255, 255, 255, .97);
            backdrop-filter: blur(10px);
            box-shadow: 0 -1px 8px rgba(0, 0, 0, .13);
            z-index: 100;
            display: none;
        }

        #bottom-bar-pool.visible { display: block; }

        /* ── Fase 6 (completada) ─────────────────────────── */
        #phase6 {
            min-height: 70vh;
            display: none;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 32px 24px;
            text-align: center;
        }

        #phase6.active {
            display: flex;
        }

        .icon-done {
            font-size: 6rem;
            color: var(--color-ok);
        }

        /* ── NFC icon activo ─────────────────────────── */
        .nfc-active {
            color: var(--color-brand) !important;
            animation: pulse 1.4s infinite;
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1
            }

            50% {
                opacity: .4
            }
        }

        /* ── DEBUG-PHASE-LABEL ─── quitar bloque completo al terminar pruebas ── */
        .dbg-phase-label {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: rgba(0,0,0,.72);
            color: #ffe066;
            font-size: .72rem;
            font-weight: 700;
            font-family: monospace;
            letter-spacing: .06em;
            padding: 4px 12px;
            border-radius: 20px;
            margin: 10px 0 6px;
            pointer-events: none;
            user-select: none;
        }
        /* ── /DEBUG-PHASE-LABEL ─── */
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
            <i class="fa fa-boxes"></i>
            <span>Picking MDR</span>
        </div>
        <span id="nav-numero" class="sub"></span>
        <button id="btn-ayuda" onclick="document.getElementById('modalAyuda').classList.add('modal-ayuda--open')" style="background:transparent;border:none;color:#fff;opacity:.75;font-size:1.2rem;padding:8px;line-height:1;" title="Ayuda">
            <i class="fa fa-question-circle"></i>
        </button>
    </div>

    <!-- ============================================================ -->
    <!-- FASE 1 — Escaneo QR del presupuesto                          -->
    <!-- ============================================================ -->
    <div id="phase1" class="phase active">
        <div class="page-wrap">
            <!-- DEBUG-PHASE-LABEL --><div class="dbg-phase-label"><i class="fa fa-bug"></i> FASE 1 — Escaneo QR del presupuesto</div><!-- /DEBUG-PHASE-LABEL -->

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
                (function() {
                    var el = document.getElementById('diag-info');
                    if (!el) return;
                    var sc = window.isSecureContext;
                    var md = !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia);
                    var jq = (typeof jQuery !== 'undefined') ? jQuery.fn.jquery : 'NO CARGÓ';
                    var h5 = (typeof Html5Qrcode !== 'undefined') ? 'OK' : 'NO CARGÓ';
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
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div id="fb-busqueda" class="fb-banner fb-hidden">
                        <i class="fa fa-info-circle"></i><span id="fb-busqueda-text"></span>
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
            <!-- DEBUG-PHASE-LABEL --><div class="dbg-phase-label"><i class="fa fa-bug"></i> FASE 2 — Lista de necesidades + progreso</div><!-- /DEBUG-PHASE-LABEL -->

            <!-- Cabecera presupuesto -->
            <div class="ppto-strip">
                <div>
                    <div class="num" id="p2-numero"></div>
                    <div class="meta" id="p2-cliente"></div>
                    <div class="meta fw-semibold" id="p2-evento"></div>
                </div>
                <button class="btn-icon" id="btn-cambiar-ppto" title="Cambiar presupuesto">
                    <i class="fa fa-undo text-secondary"></i>
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
                <div class="prog-bar-track">
                    <div id="p2-barra" class="prog-bar-fill" style="width:0%"></div>
                </div>
            </div>

            <!-- Lista artículos -->
            <div class="app-card" id="card-articulos">
                <div id="lista-articulos"></div>
            </div>

        </div>
    </div>

    <!-- ============================================================ -->
    <!-- FASE 3 — Escaneo NFC de elementos                           -->
    <!-- ============================================================ -->
    <div id="phase3" class="phase">
        <div class="page-wrap">
            <!-- DEBUG-PHASE-LABEL --><div class="dbg-phase-label"><i class="fa fa-bug"></i> FASE 3 — Escaneo NFC de elementos</div><!-- /DEBUG-PHASE-LABEL -->

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
                        <i class="fa fa-info-circle"></i>
                        <span>Acerca el tag NFC o introduce el código</span>
                    </div>
                </div>
            </div>

            <!-- Botón Elementos escaneados -->
            <button class="btn-app btn-app-outline w-100 mt-3" onclick="mostrarFase4Pool()">
                <i class="fa fa-list me-2"></i> Elementos escaneados
                <span id="pool-count-bar" class="badge bg-primary ms-2">0</span>
            </button>

            <!-- Último elemento escaneado -->
            <div id="last-scan-card" style="display:none;" class="app-card mt-3">

                <!-- Fila superior: imagen | código + artículo + familia -->
                <div class="d-flex gap-3 align-items-center" style="padding:10px 10px 8px;">
                    <!-- Imagen -->
                    <div id="lsc-img" style="flex-shrink:0;margin-right:12px;"></div>
                    <!-- Info principal -->
                    <div style="flex:1;min-width:0;display:flex;flex-direction:column;gap:5px;justify-content:center;">
                        <!-- Estado -->
                        <div>
                            <span id="lsc-estado" class="badge" style="font-size:.78rem;"></span>
                        </div>
                        <!-- Código -->
                        <div>
                            <span id="lsc-codigo" class="fw-bold text-uppercase" style="font-size:1rem;"></span>
                        </div>
                        <!-- Artículo -->
                        <div>
                            <span id="lsc-articulo" class="fw-semibold" style="font-size:.92rem;"></span>
                        </div>
                        <!-- Familia -->
                        <div>
                            <span id="lsc-familia" class="text-muted" style="font-size:.82rem;"></span>
                        </div>
                    </div>
                </div>

                <!-- Separador -->
                <div style="border-top:1px solid #f0f0f0;margin:0 10px;"></div>

                <!-- Fila inferior: modelo + serie + ubicación (ancho completo) -->
                <div style="padding:8px 12px 10px;display:flex;flex-direction:column;gap:5px;">
                    <!-- Modelo + Serie en la misma fila -->
                    <div class="d-flex flex-wrap align-items-baseline" style="gap:0 16px;row-gap:5px;">
                        <div id="lsc-modelo" class="text-muted" style="font-size:.82rem;"></div>
                        <span id="lsc-serie" class="text-muted" style="font-size:.82rem;word-break:break-word;"></span>
                    </div>
                    <!-- Ubicacion -->
                    <div id="lsc-ubicacion" class="text-muted" style="font-size:.82rem;word-break:break-word;"></div>
                </div>

            </div>

        </div>

        <!-- Botón oculto usado por picking.js para volver a fase 2 -->
        <button id="btn-volver-p2" style="display:none;"></button>

        <!-- Barra inferior fija: Comparar / Cancelar -->
        <div id="bottom-bar" class="visible">
            <button id="btn-comparar-fase3" class="btn-app btn-app-primary mb-2" disabled onclick="compararPool()">
                <i class="fa fa-balance-scale me-2"></i> Comparar <span id="pool-count-bar2" class="badge bg-white text-primary ms-1">0</span>
            </button>
            <button id="btn-cancelar-salida" class="btn-app btn-app-outline" style="color:var(--color-err); border-color:var(--color-err);">
                Cancelar y devolver elementos
            </button>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- FASE 4 — Pool completo con buscador                          -->
    <!-- ============================================================ -->
    <div id="phase4" class="phase">

        <!-- Buscador — ancho completo con márgenes iguales a las tarjetas -->
        <div style="padding:16px 14px 0;">
            <!-- DEBUG-PHASE-LABEL --><div class="dbg-phase-label"><i class="fa fa-bug"></i> FASE 4 — Pool con buscador</div><!-- /DEBUG-PHASE-LABEL -->
            <div id="pool-search-wrap">
                <button id="pool-campo-btn" type="button" data-campo="nombre_articulo"
                    style="flex-shrink:0;height:46px;border:2px solid #c0c4cc;border-right:none;border-radius:10px 0 0 10px;background:#f8f9fa;color:#444;font-size:.9rem;font-weight:600;padding:0 14px;white-space:nowrap;cursor:pointer;">
                    <span id="pool-campo-label">Artículo</span>
                    <i class="fa fa-caret-down" style="margin-left:5px;"></i>
                </button>
                <div id="pool-campo-menu" style="display:none;position:absolute;top:48px;left:0;background:#fff;border:1.5px solid #c0c4cc;border-radius:0 0 8px 8px;z-index:400;min-width:160px;box-shadow:0 4px 12px rgba(0,0,0,.12);">
                    <a class="dropdown-item pool-campo-opt" href="#" data-campo="nombre_articulo">Artículo</a>
                    <a class="dropdown-item pool-campo-opt" href="#" data-campo="nombre_familia">Familia</a>
                    <a class="dropdown-item pool-campo-opt" href="#" data-campo="codigo">Código</a>
                </div>
                <input type="search" id="pool-search" placeholder="Buscar..." autocomplete="off"
                    style="flex:1;min-width:0;height:46px;border:2px solid #c0c4cc;border-left:none;border-radius:0 10px 10px 0;font-size:1rem;padding:0 14px;outline:none;">
                <!-- Panel de sugerencias -->
                <div id="pool-suggestions" style="display:none;position:absolute;left:0;right:0;top:48px;background:#fff;border:1.5px solid #c0c4cc;border-radius:0 0 12px 12px;box-shadow:0 4px 16px rgba(0,0,0,.12);z-index:300;overflow:hidden;"></div>
            </div>
        </div>

        <div class="page-wrap" style="padding-top:12px;">
            <!-- Lista del pool -->
            <div id="phase4-pool-lista"></div>
        </div>

        <!-- Barra inferior: Volver a escaneo + Comparar -->
        <div id="bottom-bar-pool" class="visible">
            <div class="d-flex gap-2">
                <button class="btn-app btn-app-outline flex-fill" style="height:52px;" onclick="mostrarFase(3)">
                    <i class="fa fa-arrow-left me-1"></i> Volver
                </button>
                <button id="btn-comparar" class="btn-app btn-app-primary flex-fill" style="height:52px;" disabled>
                    <i class="fa fa-balance-scale me-1"></i> Comparar
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- FASE 5 — Comparación de pool                                 -->
    <!-- ============================================================ -->
    <div id="phase5" class="phase">
        <div class="page-wrap">
            <!-- DEBUG-PHASE-LABEL --><div class="dbg-phase-label"><i class="fa fa-bug"></i> FASE 5 — Comparación de pool</div><!-- /DEBUG-PHASE-LABEL -->

            <!-- Banner: alerta faltantes sin cubrir -->
            <div id="cmp-alerta-faltan" class="alert alert-warning mb-3" role="alert" style="display:none;">
                <i class="fa fa-exclamation-triangle me-2"></i>
                <span id="cmp-alerta-texto">Hay elementos sin cubrir</span>
            </div>

            <!-- CORRECTOS (colapsable) -->
            <div class="card border-success shadow-sm mb-3 overflow-hidden">
                <button class="cmp-header card-header d-flex align-items-center justify-content-between w-100 border-0 collapsed"
                    type="button" data-toggle="collapse" data-target="#cmp-correctos"
                    style="background:rgba(25,135,84,.08);font-size:.95rem;font-weight:600;cursor:pointer;">
                    <span>
                        <i class="fa fa-check-circle text-success me-1"></i> Correctos
                        <span class="badge bg-success ms-1" id="badge-correctos">0</span>
                    </span>
                    <i class="fa fa-chevron-down cmp-chevron"></i>
                </button>
                <div class="collapse" id="cmp-correctos">
                    <div id="cmp-correctos-list"></div>
                </div>
            </div>

            <!-- FALTAN (siempre visible) -->
            <div class="card border-danger shadow-sm mb-3 overflow-hidden">
                <div class="card-header d-flex align-items-center justify-content-between"
                    style="background:rgba(220,53,69,.08);font-size:.95rem;font-weight:600;">
                    <span>
                        <i class="fa fa-times-circle text-danger me-1"></i> Faltan
                        <span class="badge bg-danger ms-1" id="badge-faltan">0</span>
                    </span>
                </div>
                <div id="cmp-faltan-list"></div>
            </div>

            <!-- SOBRAN / BACKUP (colapsable) -->
            <div class="card border-secondary shadow-sm mb-3 overflow-hidden">
                <button class="cmp-header card-header d-flex align-items-center justify-content-between w-100 border-0 collapsed"
                    type="button" data-toggle="collapse" data-target="#cmp-sobran"
                    style="background:rgba(108,117,125,.08);font-size:.95rem;font-weight:600;cursor:pointer;">
                    <span>
                        <i class="fa fa-plus-circle text-secondary me-1"></i> Sobran / Backup
                        <span class="badge bg-secondary ms-1" id="badge-sobran">0</span>
                    </span>
                    <i class="fa fa-chevron-down cmp-chevron"></i>
                </button>
                <div class="collapse" id="cmp-sobran">
                    <div id="cmp-sobran-list"></div>
                </div>
            </div>

            <!-- NO RELACIONADOS (colapsable) -->
            <div class="card border-warning shadow-sm mb-3 overflow-hidden">
                <button class="cmp-header card-header d-flex align-items-center justify-content-between w-100 border-0 collapsed"
                    type="button" data-toggle="collapse" data-target="#cmp-no-relacionados"
                    style="background:rgba(255,193,7,.1);font-size:.95rem;font-weight:600;cursor:pointer;">
                    <span>
                        <i class="fa fa-question-circle text-warning me-1"></i> No relacionados
                        <span class="badge bg-warning text-dark ms-1" id="badge-norel">0</span>
                    </span>
                    <i class="fa fa-chevron-down cmp-chevron"></i>
                </button>
                <div class="collapse" id="cmp-no-relacionados">
                    <div id="cmp-norel-list"></div>
                </div>
            </div>

        </div>

        <!-- Barra inferior: Volver + Confirmar -->
        <div id="bottom-bar-cmp">
            <div class="d-flex gap-2">
                <button id="btn-volver-escaneo" class="btn btn-outline-secondary flex-fill" style="height:52px;">
                    <i class="fa fa-arrow-left me-1"></i> Volver
                </button>
                <button id="btn-confirmar" class="btn btn-success flex-fill" style="height:52px;">
                    <i class="fa fa-check-double me-1"></i> Confirmar
                </button>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- FASE 6 — Confirmación final                                  -->
    <!-- ============================================================ -->
    <div id="phase6" class="phase">
        <!-- DEBUG-PHASE-LABEL --><div class="dbg-phase-label"><i class="fa fa-bug"></i> FASE 6 — Confirmación final</div><!-- /DEBUG-PHASE-LABEL -->
        <i class="fa fa-check-circle icon-done mb-3"></i>
        <h3 class="fw-bold mb-2">¡Salida Completada!</h3>
        <p class="text-muted mb-4" id="p6-resumen"></p>
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
                        <i class="fa fa-map-marker-alt me-2"></i>Reubicar Elemento
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
                        <i class="fa fa-map-marker-alt me-1"></i> Confirmar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ============================================================ -->
    <!-- MODAL AYUDA                                                   -->
    <!-- ============================================================ -->
    <div id="modalAyuda" class="modal-ayuda" role="dialog" aria-modal="true" aria-labelledby="ayuda-titulo">
        <div class="modal-ayuda__backdrop" onclick="document.getElementById('modalAyuda').classList.remove('modal-ayuda--open')"></div>
        <div class="modal-ayuda__sheet">
            <div class="modal-ayuda__header">
                <span id="ayuda-titulo"><i class="fa fa-info-circle me-2"></i>¿Cómo funciona esta pantalla?</span>
                <button onclick="document.getElementById('modalAyuda').classList.remove('modal-ayuda--open')" class="modal-ayuda__close" aria-label="Cerrar">&times;</button>
            </div>
            <div class="modal-ayuda__body">

                <div class="ayuda-item">
                    <div class="ayuda-item__icon" style="background:#e3f2fd;color:#1a237e;"><i class="fa fa-list"></i></div>
                    <div>
                        <div class="ayuda-item__title">Lista de artículos agrupada</div>
                        <div class="ayuda-item__desc">Cada artículo aparece <strong>una sola vez</strong>, aunque figure en varias líneas del presupuesto (por ejemplo, alquilado el día 1 y de nuevo el día 3). La cantidad mostrada es el <strong>total acumulado</strong> de todas las líneas.</div>
                    </div>
                </div>

                <div class="ayuda-item">
                    <div class="ayuda-item__icon" style="background:#e8f5e9;color:#198754;"><i class="fa fa-tag"></i></div>
                    <div>
                        <div class="ayuda-item__title">Contador escaneados / requeridos</div>
                        <div class="ayuda-item__desc">El indicador <strong>X / Y</strong> muestra cuántos elementos físicos se han escaneado (X) frente a los que pide el presupuesto (Y). Es posible ver valores como <strong>2/1</strong> si se preparan más unidades de las previstas.</div>
                    </div>
                </div>

                <div class="ayuda-item">
                    <div class="ayuda-item__icon" style="background:#fff3e0;color:#fd7e14;"><i class="fa fa-wifi"></i></div>
                    <div>
                        <div class="ayuda-item__title">Botón «Escanear Elementos»</div>
                        <div class="ayuda-item__desc">Pasa a la pantalla de <strong>escaneo NFC</strong> para registrar los elementos físicos uno a uno mediante etiquetas NFC o introduciendo el código manualmente.</div>
                    </div>
                </div>

                <div class="ayuda-item">
                    <div class="ayuda-item__icon" style="background:#fce4ec;color:#dc3545;"><i class="fa fa-check-double"></i></div>
                    <div>
                        <div class="ayuda-item__title">Completar Salida</div>
                        <div class="ayuda-item__desc">El botón se activa únicamente cuando <strong>todos los artículos</strong> han alcanzado la cantidad requerida. Al confirmar, los elementos quedan registrados como <strong>alquilados</strong>.</div>
                    </div>
                </div>

                <div class="ayuda-item">
                    <div class="ayuda-item__icon" style="background:#fff8e1;color:#795548;"><i class="fa fa-undo"></i></div>
                    <div>
                        <div class="ayuda-item__title">Cancelar y devolver elementos</div>
                        <div class="ayuda-item__desc">Al cancelar, <strong>todos los elementos escaneados vuelven al estado Disponible</strong> como si nunca hubieran sido preparados. La salida queda registrada como cancelada y se puede iniciar de nuevo el picking desde cero.</div>
                    </div>
                </div>

                <div class="ayuda-item">
                    <div class="ayuda-item__icon" style="background:#fff3e0;color:#e65100;"><i class="fa fa-exclamation-triangle"></i></div>
                    <div>
                        <div class="ayuda-item__title">Escaneado excede las unidades previstas</div>
                        <div class="ayuda-item__desc">Si ya se ha cubierto la cantidad requerida de un artículo y se escanea otra unidad adicional, el sistema pregunta si deseas <strong>añadirla igualmente como material extra</strong>. Puede ser útil para llevar unidades de recambio al evento. Se registra fuera del conteo obligatorio.</div>
                    </div>
                </div>

                <div class="ayuda-item">
                    <div class="ayuda-item__icon" style="background:#efebe9;color:#4e342e;"><i class="fa fa-toolbox"></i></div>
                    <div>
                        <div class="ayuda-item__title">Artículo no está en el presupuesto</div>
                        <div class="ayuda-item__desc">Si se escanea un elemento cuyo artículo <strong>no figura en el presupuesto</strong>, el sistema pregunta si lo quieres añadir como <strong>material de repuesto</strong>. Si confirmas, queda registrado como repuesto (backup) en la salida, sin afectar a los contadores del presupuesto.</div>
                    </div>
                </div>

                <div class="ayuda-item">
                    <div class="ayuda-item__icon" style="background:#e8eaf6;color:#3949ab;"><i class="fa fa-exchange-alt"></i></div>
                    <div>
                        <div class="ayuda-item__title">Traslado de elemento alquilado</div>
                        <div class="ayuda-item__desc">Si se escanea un elemento que ya está en estado <strong>Alquilado</strong> (asignado a otra salida), el sistema avisa y ofrece la opción de <strong>reubicar</strong>. Al confirmar, el elemento se transfiere a esta salida, quedando desvinculado de la salida anterior.</div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <style>
        .modal-ayuda {
            position: fixed; inset: 0; z-index: 9999;
            display: flex; align-items: flex-end;
            pointer-events: none;
        }
        .modal-ayuda--open { pointer-events: auto; }
        .modal-ayuda__backdrop {
            position: absolute; inset: 0;
            background: rgba(0,0,0,.45);
            opacity: 0; transition: opacity .25s;
        }
        .modal-ayuda--open .modal-ayuda__backdrop { opacity: 1; }
        .modal-ayuda__sheet {
            position: relative; width: 100%;
            background: #fff;
            border-radius: 20px 20px 0 0;
            max-height: 88vh; overflow-y: auto;
            transform: translateY(100%); transition: transform .3s cubic-bezier(.4,0,.2,1);
            padding-bottom: env(safe-area-inset-bottom, 0px);
        }
        .modal-ayuda--open .modal-ayuda__sheet { transform: translateY(0); }
        .modal-ayuda__header {
            display: flex; justify-content: space-between; align-items: center;
            padding: 16px 20px 12px;
            font-size: 1rem; font-weight: 700; color: #1a237e;
            border-bottom: 1px solid #eee;
            position: sticky; top: 0; background: #fff; z-index: 1;
        }
        .modal-ayuda__close {
            background: none; border: none; font-size: 1.6rem; line-height: 1;
            color: #999; padding: 0 4px; cursor: pointer;
        }
        .modal-ayuda__body { padding: 18px 20px 24px; }
        .ayuda-item {
            display: flex; gap: 14px; align-items: flex-start;
            margin-bottom: 20px;
        }
        .ayuda-item:last-child { margin-bottom: 0; }
        .ayuda-item__icon {
            flex-shrink: 0; width: 42px; height: 42px;
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.1rem;
        }
        .ayuda-item__title {
            font-size: .9rem; font-weight: 700; color: #222;
            margin-bottom: 4px;
        }
        .ayuda-item__desc {
            font-size: .82rem; color: #555; line-height: 1.5;
        }
    </style>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../public/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../public/lib/html5-qrcode/html5-qrcode.min.js"></script>
    <script>
        const ID_USUARIO = <?= $idUsuario; ?>;
        const PICKING_VER = '<?= date('His'); ?>';
    </script>
    <script src="picking.js?v=<?= time(); ?>"></script>

    <script>
        // Botón atrás del app bar según fase
        function appBarBack() {
            if (document.getElementById('phase5').classList.contains('active')) {
                // fase5 → vuelve a fase4
                mostrarFase(4);
            } else if (document.getElementById('phase4').classList.contains('active')) {
                // fase4 → vuelve a fase3
                mostrarFase(3);
            } else if (document.getElementById('phase3').classList.contains('active')) {
                // fase3 → vuelve a fase2
                document.getElementById('btn-volver-p2')?.click();
            } else if (document.getElementById('phase2').classList.contains('active')) {
                // fase2 → vuelve a fase1 y limpia estado
                resetState();
                mostrarFase(1);
            }
        }
        // Control visibilidad btn-back y barras inferiores
        document.addEventListener('phaseChange', function(e) {
            const btnBack = document.getElementById('btn-appbar-back');
            btnBack.style.display = (e.detail.phase > 1 && e.detail.phase < 6) ? 'block' : 'none';
            // Barra inferior fase 3
            const bb = document.getElementById('bottom-bar');
            if (bb) bb.classList.toggle('visible', e.detail.phase === 3);
            // Barra inferior fase 4 (pool buscador)
            const bbp = document.getElementById('bottom-bar-pool');
            if (bbp) bbp.classList.toggle('visible', e.detail.phase === 4);
            // Barra inferior fase 5 (comparación)
            const bbc = document.getElementById('bottom-bar-cmp');
            if (bbc) bbc.classList.toggle('visible', e.detail.phase === 5);
        });
    </script>
</body>

</html>