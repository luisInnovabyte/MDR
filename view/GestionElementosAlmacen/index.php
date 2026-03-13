<?php
// view/GestionElementosAlmacen/index.php — Gestión de elementos (mobile-first)
session_start();
if (!isset($_SESSION['sesion_iniciada']) || $_SESSION['sesion_iniciada'] !== true) {
    header('Location: ../../index.php');
    exit;
}
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
    <title>Gestión Almacén — MDR</title>

    <link href="../../public/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../public/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* ── Variables ────────────────────────────────── */
        :root {
            --color-brand: #1a237e;
            --color-ok: #198754;
            --color-err: #dc3545;
            --color-warn: #fd7e14;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
            --font-base: 1rem;
            --touch-min: 52px;
        }

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
            padding-left: var(--safe-left);
            padding-right: var(--safe-right);
        }

        /* ── AppBar ───────────────────────────────────── */
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

        /* ── Fases ────────────────────────────────────── */
        .phase {
            display: none;
        }

        .phase.active {
            display: block;
        }

        .page-wrap {
            padding: 16px 14px 0;
        }

        #phase2 .page-wrap {
            padding-bottom: 100px;
        }

        /* ── Tarjeta base ─────────────────────────────── */
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

        /* ── Botones táctiles ─────────────────────────── */
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
            user-select: none;
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

        .btn-app:disabled {
            opacity: .45;
            cursor: not-allowed;
        }

        /* ── Input grande ─────────────────────────────── */
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

        /* ── Select grande ────────────────────────────── */
        .sel-app {
            height: var(--touch-min);
            font-size: 1rem;
            border-radius: 10px;
            border: 2px solid #c0c4cc;
            width: 100%;
            padding: 0 14px;
            background: #fff;
            outline: none;
            cursor: pointer;
        }

        .sel-app:focus {
            border-color: var(--color-brand);
        }

        /* ── Input fecha ──────────────────────────────── */
        .inp-date {
            height: var(--touch-min);
            font-size: 1rem;
            border-radius: 10px;
            border: 2px solid #c0c4cc;
            width: 100%;
            padding: 0 14px;
            outline: none;
        }

        .inp-date:focus {
            border-color: var(--color-brand);
        }

        /* ── Feedback banner ──────────────────────────── */
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

        /* ── Strip del elemento ───────────────────────── */
        .elem-strip {
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

        .elem-strip .codigo {
            font-size: 1rem;
            font-weight: 700;
        }

        .elem-strip .articulo {
            font-size: .82rem;
            color: #555;
        }

        /* ── Info row ─────────────────────────────────── */
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 7px 0;
            border-bottom: 1px solid #f0f0f0;
            font-size: .93rem;
            gap: 10px;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-row .lbl {
            color: #888;
            flex-shrink: 0;
        }

        .info-row .val {
            font-weight: 600;
            text-align: right;
            word-break: break-word;
        }

        /* ── Badge estado ─────────────────────────────── */
        .estado-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 20px;
            font-size: .78rem;
            font-weight: 700;
            color: #fff;
        }

        /* ── Barra inferior fija ──────────────────────── */
        #bottom-bar {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            z-index: 300;
            background: #fff;
            border-top: 1px solid #dee2e6;
            padding: 10px 14px calc(10px + var(--safe-bottom));
            display: none;
            gap: 10px;
        }

        #bottom-bar.visible {
            display: flex;
        }

        /* ── Label de formulario ──────────────────────── */
        .form-lbl {
            font-size: .78rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .06em;
            color: #555;
            margin-bottom: 6px;
            display: block;
        }

        /* ── NFC full button ──────────────────────────── */
        .btn-nfc-full {
            border-radius: 10px !important;
            margin-bottom: 12px;
        }

        /* ── Separador ────────────────────────────────── */
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
    </style>
</head>

<body>

    <!-- ═══════════════════════ APP BAR ═══════════════════════ -->
    <div id="app-bar">
        <div class="brand">
            <i class="fa fa-warehouse"></i>
            <div>
                <div>Gestión Almacén</div>
                <div class="sub" id="app-bar-sub">MDR · Área Técnica</div>
            </div>
        </div>
    </div>

    <!-- ═══════════════════════ FASE 1 — BÚSQUEDA ═══════════════════════ -->
    <div id="phase1" class="phase active">
        <div class="page-wrap">

            <!-- Feedback -->
            <div id="fb-busqueda" class="fb-banner fb-hidden"></div>

            <!-- Búsqueda NFC -->
            <div class="app-card">
                <div class="app-card-body">
                    <div class="app-card-title"><i class="fa fa-rss me-1"></i> Escanear NFC</div>
                    <button id="btn-nfc" class="btn-app btn-app-primary btn-nfc-full" onclick="iniciarNFC()">
                        <i class="fa fa-wifi"></i> Activar lectura NFC
                    </button>
                    <div id="nfc-estado" class="fb-banner fb-hidden"></div>
                </div>
            </div>

            <div class="sep-or">o buscar manualmente</div>

            <!-- Búsqueda manual por código -->
            <div class="app-card">
                <div class="app-card-body">
                    <div class="app-card-title"><i class="fa fa-barcode me-1"></i> Código de elemento</div>
                    <div class="inp-group">
                        <input type="text" id="inp-codigo" class="inp-scan" placeholder="Ej.: CAMARA-001"
                            autocomplete="off" autocorrect="off" spellcheck="false"
                            onkeydown="if(event.key==='Enter') buscarElemento()">
                        <button class="btn-app btn-app-primary" style="min-width:64px;" onclick="buscarElemento()">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════ FASE 2 — DETALLE + EDICIÓN ═══════════════════════ -->
    <div id="phase2" class="phase">
        <div class="page-wrap">

            <!-- Feedback auto-guardado -->
            <div id="fb-fase2" class="fb-banner fb-hidden"></div>

            <!-- Strip identificación -->
            <div class="elem-strip" id="elem-strip">
                <div>
                    <div class="codigo" id="strip-codigo">—</div>
                    <div class="articulo" id="strip-articulo">—</div>
                </div>
                <div id="strip-estado-badge"></div>
            </div>

            <!-- Información (solo lectura) -->
            <div class="app-card">
                <div class="app-card-body">
                    <div class="app-card-title"><i class="fa fa-info-circle me-1"></i> Información</div>

                    <div class="info-row" id="row-presupuesto" style="display:none;">
                        <span class="lbl">Presupuesto</span>
                        <span class="val" id="info-presupuesto">—</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Propiedad</span>
                        <span class="val" id="info-propiedad">—</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Ubicación</span>
                        <span class="val" id="info-ubicacion">—</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Peso</span>
                        <span class="val" id="info-peso">—</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Nº serie</span>
                        <span class="val" id="info-serie">—</span>
                    </div>
                    <div class="info-row">
                        <span class="lbl">Descripción</span>
                        <span class="val" id="info-descripcion">—</span>
                    </div>
                </div>
            </div>

            <!-- Edición: estado + mantenimiento -->
            <div class="app-card">
                <div class="app-card-body">
                    <div class="app-card-title"><i class="fa fa-edit me-1"></i> Actualizar</div>

                    <!-- Estado -->
                    <div class="mb-3">
                        <label class="form-lbl" for="sel-estado"><i class="fa fa-tag me-1"></i> Estado</label>
                        <select id="sel-estado" class="sel-app"></select>
                    </div>

                    <!-- Próximo mantenimiento -->
                    <div>
                        <label class="form-lbl" for="inp-mant"><i class="fa fa-wrench me-1"></i> Próximo mantenimiento</label>
                        <input type="date" id="inp-mant" class="inp-date">
                        <small class="text-muted">Dejar en blanco para no modificar</small>
                    </div>

                    <!-- Input oculto ID -->
                    <input type="hidden" id="hidden-id-elemento">
                </div>
            </div>

        </div>
    </div>

    <!-- ═══════════════════════ BARRA INFERIOR ═══════════════════════ -->
    <div id="bottom-bar">
        <button class="btn-app btn-app-secondary" style="width:100%;" onclick="nuevaBusqueda()">
            <i class="fa fa-arrow-left"></i> Nueva búsqueda
        </button>
        <!-- Botón Guardar eliminado: el guardado es automático al cambiar estado o fecha -->
        <!-- <button class="btn-app btn-app-success" id="btn-guardar" style="flex:1;" onclick="guardar()">
            <i class="fa fa-floppy-disk"></i> Guardar
        </button> -->
    </div>

    <!-- ═══════════════════════ SCRIPTS ═══════════════════════ -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    <script src="gestion_almacen.js"></script>

</body>

</html>