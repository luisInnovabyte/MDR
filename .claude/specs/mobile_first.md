# Spec: Diseño Mobile-First con Cámara y NFC

> Patrón de referencia extraído de `view/Picking/index.php` + `view/Picking/picking.js`  
> Replica este patrón en cualquier pantalla standalone orientada a uso en móvil.

---

## 1. Filosofía Mobile-First

Esta pantalla es **standalone**: sin sidebar ni header global del ERP.  
Diseñada para usarse en dispositivos Android/iOS en red local.

Principios:
- Todo botón táctil mínimo **52 px** de altura (`--touch-min`)
- **Sin scroll horizontal** — fluido al 100% del viewport
- **Safe Areas** para notch e indicador home de iOS
- **Feedback inmediato** en cada acción (visual + vibratorio)
- **Degrada graciosamente**: si no hay HTTPS → ocultar cámara, mostrar entrada manual

---

## 2. Meta Tags PWA (obligatorios)

```html
<meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="theme-color" content="#1a237e">
```

El `viewport-fit=cover` es **crítico**: permite que `env(safe-area-inset-*)` funcione en iOS.

---

## 3. Variables CSS Raíz

```css
:root {
    --color-brand:  #1a237e;   /* azul primario */
    --color-prep:   #795548;   /* marrón (backup/reubicación) */
    --color-ok:     #198754;   /* verde éxito */
    --color-err:    #dc3545;   /* rojo error */
    --color-warn:   #fd7e14;   /* naranja aviso */
    --safe-top:     env(safe-area-inset-top,    0px);
    --safe-bottom:  env(safe-area-inset-bottom, 0px);
    --safe-left:    env(safe-area-inset-left,   0px);
    --safe-right:   env(safe-area-inset-right,  0px);
    --font-base:    1rem;       /* 16px */
    --touch-min:    52px;       /* altura mínima táctil */
}
```

---

## 4. Reset Base

```css
*, *::before, *::after { box-sizing: border-box; }

html, body {
    height: 100%;
    margin: 0;
    background: #eef0f5;
    font-size: var(--font-base);
    -webkit-tap-highlight-color: transparent;  /* sin flash azul al tocar */
    overscroll-behavior: none;                 /* sin rebote de scroll */
}

body {
    padding-top:    var(--safe-top);
    padding-bottom: calc(var(--safe-bottom) + 76px);  /* espacio barra inferior */
    padding-left:   var(--safe-left);
    padding-right:  var(--safe-right);
}
```

---

## 5. Componentes CSS

### 5.1 AppBar (`#app-bar`)

Barra superior sticky, color brand, con botón atrás (oculto en fase inicial), título, subtítulo y botón de ayuda.

```css
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
.brand    { font-size: 1.1rem; font-weight: 700; letter-spacing: .02em; display: flex; align-items: center; gap: 10px; }
.sub      { font-size: .78rem; opacity: .7; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 160px; }
.btn-back { color: #fff; background: transparent; border: none; padding: 8px; font-size: 1.25rem; line-height: 1; display: none; }
```

HTML:
```html
<div id="app-bar">
    <button id="btn-appbar-back" class="btn-back" onclick="appBarBack()">
        <i class="fa fa-chevron-left"></i>
    </button>
    <div class="brand">
        <i class="fa fa-boxes"></i>
        <span>Título Pantalla</span>
    </div>
    <span id="nav-numero" class="sub"></span>
    <button id="btn-ayuda"
            onclick="document.getElementById('modalAyuda').classList.add('modal-ayuda--open')"
            style="background:transparent;border:none;color:#fff;opacity:.75;font-size:1.2rem;padding:8px;line-height:1;">
        <i class="fa fa-question-circle"></i>
    </button>
</div>
```

---

### 5.2 Sistema de Fases

Cada "pantalla/tab" es un `.phase`. Solo la activa tiene `display: block`.

```css
.phase        { display: none; }
.phase.active { display: block; }

/* Excepción: fase final usa flex centrado */
#phaseN        { display: none; flex-direction: column; align-items: center; justify-content: center; min-height: 70vh; padding: 32px 24px; text-align: center; }
#phaseN.active { display: flex; }
```

Función JS de cambio de fase:
```javascript
function mostrarFase(n) {
    document.querySelectorAll('.phase').forEach(p => p.classList.remove('active'));
    document.getElementById('phase' + n).classList.add('active');
    // Notificar al AppBar y BottomBar
    document.dispatchEvent(new CustomEvent('phaseChange', { detail: { phase: n } }));
}
```

El `CustomEvent('phaseChange')` permite que el AppBar y la BottomBar reaccionen:
```javascript
document.addEventListener('phaseChange', function(e) {
    // Mostrar btn-back solo en fases intermedias (no en la primera ni en la final)
    document.getElementById('btn-appbar-back').style.display =
        (e.detail.phase > 1 && e.detail.phase < TOTAL_FASES) ? 'block' : 'none';
    // BottomBar visible solo en la fase que corresponda (ej: fase de escaneo)
    const bb = document.getElementById('bottom-bar');
    if (bb) bb.classList.toggle('visible', e.detail.phase === FASE_CON_BOTTOM_BAR);
});
```

Botón atrás del AppBar (navegar hacia la fase anterior):
```javascript
function appBarBack() {
    // Fase 3 → Fase 2
    if (document.getElementById('phase3').classList.contains('active')) {
        document.getElementById('btn-volver-p2')?.click();
    // Fase 2 → Fase 1
    } else if (document.getElementById('phase2').classList.contains('active')) {
        resetState();
        mostrarFase(1);
    }
}
```

---

### 5.3 Contenedor de Página (`.page-wrap`)

```css
.page-wrap { padding: 16px 14px 0; }

/* Cuando hay barra inferior fija, añadir padding-bottom para que el contenido no quede tapado */
#phaseCONBARRA .page-wrap { padding-bottom: 140px; }
```

---

### 5.4 Tarjeta Base (`.app-card`)

```css
.app-card {
    background: #fff;
    border-radius: 14px;
    box-shadow: 0 1px 4px rgba(0,0,0,.1);
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
```

---

### 5.5 Botones Táctiles (`.btn-app`)

```css
.btn-app {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    min-height: var(--touch-min);    /* 52px mínimo táctil */
    width: 100%;
    border-radius: 12px;
    font-size: 1.05rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    transition: filter .15s, transform .1s;
    -webkit-user-select: none;
}
.btn-app:active   { transform: scale(.97); filter: brightness(.9); }
.btn-app:disabled { opacity: .45; }

.btn-app-primary   { background: var(--color-brand); color: #fff; }
.btn-app-success   { background: var(--color-ok);    color: #fff; }
.btn-app-danger    { background: var(--color-err);   color: #fff; }
.btn-app-secondary { background: #e9ecef;            color: #333; }
.btn-app-outline   { background: transparent; border: 2px solid #c0c4cc; color: #444; }
```

---

### 5.6 Input de Escaneo (`.inp-scan` + `.inp-group`)

```css
.inp-group { display: flex; margin-bottom: 8px; }

.inp-scan {
    height: var(--touch-min);       /* 52px */
    font-size: 1.1rem;
    border-radius: 10px 0 0 10px;
    border: 2px solid #c0c4cc;
    flex: 1;
    padding: 0 14px;
    outline: none;
}
.inp-scan:focus { border-color: var(--color-brand); }
.inp-group .btn-app { border-radius: 0 10px 10px 0; min-width: 56px; width: auto; }
```

HTML:
```html
<div class="inp-group">
    <input type="text" id="inp-codigo-elem" class="inp-scan text-uppercase"
           placeholder="Código NFC / manual"
           autocomplete="off"
           autocapitalize="characters"
           inputmode="text">
    <button class="btn-app btn-app-success" id="btn-escanear-manual"
            style="border-radius:0 10px 10px 0; width:56px; min-width:56px;">
        <i class="fa fa-check"></i>
    </button>
</div>
```

Notas sobre atributos del input:
- `autocapitalize="characters"` → teclado Android en mayúsculas automáticas
- `inputmode="text"` → teclado alfabético (no numérico)
- `autocomplete="off"` → evita sugerencias que interfieren con el flujo de escaneo

---

### 5.7 Banners de Feedback (`.fb-banner`)

```css
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
.fb-banner i { font-size: 1.5rem; flex-shrink: 0; }
.fb-ok     { background: #d1f2e1; color: #0a3622; }
.fb-err    { background: #fde8e9; color: #58151c; }
.fb-warn   { background: #fff3cd; color: #5a3e00; }
.fb-info   { background: #e0f0ff; color: #073b6f; }
.fb-hidden { display: none; }
```

Función JS reutilizable:
```javascript
function feedback(elementId, msg, tipo) {
    // tipo: 'ok' | 'err' | 'warn' | 'info'
    const iconos = {
        ok:   'check-circle',
        err:  'times-circle',
        warn: 'exclamation-triangle',
        info: 'info-circle'
    };
    const el = document.getElementById(elementId);
    el.className = 'fb-banner fb-' + tipo;
    el.innerHTML = '<i class="fa fa-' + iconos[tipo] + ' me-2"></i>' + msg;
    el.classList.remove('fb-hidden');
}
```

HTML inicial (estado info visible):
```html
<div id="fb-escaneo" class="fb-banner fb-info">
    <i class="fa fa-info-circle"></i>
    <span>Texto inicial de instrucción</span>
</div>
```

HTML inicial (estado oculto):
```html
<div id="fb-busqueda" class="fb-banner fb-hidden">
    <i class="fa fa-info-circle"></i><span></span>
</div>
```

---

### 5.8 Barra de Progreso Grande (`.prog-wrap`)

```css
.prog-wrap      { margin-bottom: 14px; }
.prog-label     { display: flex; justify-content: space-between; font-size: .85rem; color: #666; margin-bottom: 6px; }
.prog-bar-track { height: 20px; border-radius: 10px; background: #e9ecef; overflow: hidden; }
.prog-bar-fill  { height: 100%; border-radius: 10px; background: var(--color-ok); transition: width .4s ease; }
```

HTML:
```html
<div class="prog-wrap">
    <div class="prog-label">
        <span>Material preparado</span>
        <strong id="p2-contadores">0 / 0</strong>
    </div>
    <div class="prog-bar-track">
        <div id="p2-barra" class="prog-bar-fill" style="width:0%"></div>
    </div>
</div>
```

---

### 5.9 Tarjeta-Cabecera de Entidad (`.ppto-strip`)

```css
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
.ppto-strip .num     { font-size: 1.1rem; font-weight: 700; color: var(--color-brand); }
.ppto-strip .meta    { font-size: .8rem; color: #666; }
.ppto-strip .btn-icon { background: none; border: none; color: #666; font-size: 1.2rem; padding: 6px; }
```

---

### 5.10 Barra Sticky de Acción (`.sticky-action-bar`)

Se pega al top del viewport al hacer scroll, útil para el botón de acción principal en fases con lista larga.

```css
.sticky-action-bar {
    position: sticky;
    top: 0;
    z-index: 20;
    background: #eef0f5;
    margin: 6px -14px 0;
    padding: 10px 14px 12px;
    box-shadow: 0 2px 8px rgba(0,0,0,.10);
}
```

---

### 5.11 Barra Inferior Fija (`#bottom-bar`)

```css
#bottom-bar {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    padding: 10px 14px;
    padding-bottom: calc(10px + var(--safe-bottom));  /* respeta home indicator iOS */
    background: rgba(255,255,255,.97);
    backdrop-filter: blur(10px);
    box-shadow: 0 -1px 8px rgba(0,0,0,.13);
    z-index: 100;
    display: none;
}
#bottom-bar.visible { display: block; }
```

HTML (dos botones apilados: acción principal + destructiva):
```html
<div id="bottom-bar">
    <button id="btn-completar" class="btn-app btn-app-success mb-2" disabled>
        <i class="fa fa-check-double"></i> Acción Principal
    </button>
    <button id="btn-cancelar" class="btn-app btn-app-outline"
            style="color:var(--color-err); border-color:var(--color-err);">
        Acción Destructiva
    </button>
</div>
```

La visibilidad se controla desde el listener `phaseChange` (ver §5.2):
```javascript
const bb = document.getElementById('bottom-bar');
if (bb) bb.classList.toggle('visible', e.detail.phase === FASE_CON_BOTTOM_BAR);
```

---

### 5.12 Filas de Elementos Escaneados (`.elem-row`)

```css
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
.badge-bkp {
    font-size: .65rem;
    background: var(--color-prep);
    color: #fff;
    padding: 2px 6px;
    border-radius: 6px;
    vertical-align: middle;
}
```

Template JS (con escHtml):
```javascript
elementos.map(e => `
    <div class="elem-row">
        <div>
            <span class="elem-cod">${escHtml(e.codigo_elemento)}</span>
            ${e.es_backup == 1 ? '<span class="badge badge-bkp ms-1">backup</span>' : ''}
            <div class="elem-art">${escHtml(e.nombre_articulo || '')}</div>
            <div class="elem-loc"><i class="fa fa-map-marker-alt me-1"></i>${escHtml(e.nombre_ubicacion_actual || 'Sin ubicación')}</div>
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
`).join('')
```

---

### 5.13 Icono de Fase Final

```css
.icon-done { font-size: 6rem; color: var(--color-ok); }
```

HTML:
```html
<div id="phaseN" class="phase">
    <i class="fa fa-check-circle icon-done mb-3"></i>
    <h3 class="fw-bold mb-2">¡Operación Completada!</h3>
    <p class="text-muted mb-4" id="pN-resumen">Resumen de la operación</p>
    <button id="btn-nueva" class="btn-app btn-app-primary" style="max-width:280px;">
        <i class="fa fa-plus"></i> Nueva Operación
    </button>
</div>
```

---

## 6. Activación de Cámara QR (Html5Qrcode)

### 6.1 Librería

Ruta local en MDR:
```html
<script src="../../public/lib/html5-qrcode/html5-qrcode.min.js"></script>
```

### 6.2 HTML de soporte

```html
<!-- Aviso HTTP — JS lo muestra si no hay HTTPS -->
<div id="aviso-https" class="fb-banner fb-warn mb-3" style="display:none;"></div>

<!-- Card con botón de activación -->
<div class="app-card" id="card-qr">
    <div class="app-card-body">
        <div class="app-card-title"><i class="fa fa-qrcode me-1"></i> Escanea el QR</div>
        <!-- Placeholder: botón de activación o mensaje de error/reintento -->
        <div id="qr-placeholder" class="mb-2">
            <button id="btn-activar-qr" class="btn-app btn-app-primary" onclick="iniciarQR()">
                <i class="fa fa-camera me-2"></i> Activar Cámara
            </button>
        </div>
        <!-- Div gestionado por html5-qrcode — oculto hasta que se activa -->
        <div id="qr-reader" style="display:none;"></div>
    </div>
</div>
```

El `onclick="iniciarQR()"` es **inline directo** en el botón inicial.  
Los **reintentos** posteriores se enlazan via event delegation con `bindBtnActivarQR()`.

### 6.3 CSS para el lector QR

```css
#qr-reader {
    width: 100%;
    border-radius: 14px;
    overflow: hidden;
    background: #000;
    margin-bottom: 14px;
}
#qr-reader video { width: 100% !important; display: block; }

/* Ocultar controles internos de html5-qrcode que no se necesitan */
#qr-reader__dashboard_section_csr span,
#qr-reader__dashboard_section_swaplink { display: none !important; }
```

### 6.4 Función `iniciarQR()`

```javascript
function iniciarQR() {
    const placeholder = document.getElementById('qr-placeholder');
    const readerEl    = document.getElementById('qr-reader');

    // 1. Spinner inmediato en el placeholder antes de pedir permisos
    if (placeholder) {
        placeholder.innerHTML =
            '<div style="text-align:center;padding:20px 0;">' +
            '<i class="fa fa-spinner fa-spin fa-2x mb-2 d-block" style="color:#1a237e;"></i>' +
            '<span style="font-size:.9rem;color:#555;">Iniciando cámara…</span>' +
            '</div>';
    }

    if (state.qrScanner) return; // ya activa, evitar doble instancia

    // 2. Función de error unificada
    function mostrarErrorCamara(err) {
        console.error('[App] iniciarQR error:', err);
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
        bindBtnActivarQR(); // re-enlazar después de regenerar el botón
    }

    // 3. Verificar soporte antes de instanciar
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
        mostrarErrorCamara(null);
        return;
    }

    // 4. Spinner en el div del lector mientras se solicita permiso
    if (readerEl) {
        readerEl.innerHTML =
            '<div style="text-align:center;padding:32px 0;color:#555;">'
            + '<i class="fa fa-spinner fa-spin fa-2x mb-3 d-block"></i>'
            + '<span style="font-size:.9rem;">Solicitando permiso de cámara…</span>'
            + '</div>';
        readerEl.style.display = 'block';
    }
    if (placeholder) placeholder.style.display = 'none';

    // 5. Calcular tamaño óptimo del recuadro QR según ancho de pantalla
    const dim = Math.min(280, Math.max(200, window.innerWidth - 60));

    // 6. Instanciar y arrancar
    try {
        state.qrScanner = new Html5Qrcode('qr-reader');
    } catch (e) {
        mostrarErrorCamara(e);
        return;
    }

    state.qrScanner
        .start(
            { facingMode: 'environment' },               // cámara trasera
            { fps: 12, qrbox: { width: dim, height: dim } },
            onQRSuccess,                                  // callback éxito
            function() { /* errores de frame, silencioso */ }
        )
        .then(function() {
            if (readerEl) readerEl.style.display = 'block';
        })
        .catch(mostrarErrorCamara);
}
```

### 6.5 Función `detenerQR()`

```javascript
function detenerQR() {
    if (!state.qrScanner) return;
    const scanner = state.qrScanner;
    state.qrScanner = null;      // null síncrono para evitar reutilización antes del stop()
    scanner.stop()
        .then(function() { try { scanner.clear(); } catch(e) {} })
        .catch(function() {});
}
```

### 6.6 Callback `onQRSuccess(decodedText)`

```javascript
function onQRSuccess(decodedText) {
    detenerQR();
    // Ocultar vídeo y confirmar lectura en el placeholder
    const placeholder = document.getElementById('qr-placeholder');
    const readerEl    = document.getElementById('qr-reader');
    if (readerEl) readerEl.style.display = 'none';
    if (placeholder) {
        placeholder.innerHTML = '<div class="d-flex align-items-center gap-2 text-success fw-semibold">'
            + '<i class="fa fa-check-circle fa-lg"></i><span>QR leído ✓</span></div>';
        placeholder.style.display = 'block';
    }
    // El QR puede contener el código directamente o una URL con parámetro
    const valor = extraerValorQR(decodedText);
    procesarQR(valor);  // función de negocio de la pantalla
}

function extraerValorQR(raw) {
    // Si el QR es una URL con ?n=VALOR → extraer parámetro
    try {
        const url = new URL(raw);
        if (url.searchParams.has('n')) return url.searchParams.get('n');
    } catch(e) {}
    // Si es un código directo → devolver tal cual
    return raw.trim();
}
```

### 6.7 Event Delegation para el botón de activación/reintento

```javascript
function bindBtnActivarQR() {
    // off() primero para evitar listeners duplicados al regenerar el botón
    $(document).off('click', '#btn-activar-qr').on('click', '#btn-activar-qr', function() {
        $(this).prop('disabled', true).html('<i class="fa fa-spinner fa-spin me-2"></i>Activando...');
        iniciarQR();
    });
}
```

`bindBtnActivarQR()` se llama en tres momentos:
1. `$(document).ready()` — enlace inicial (solo si `window.isSecureContext === true`)
2. `mostrarErrorCamara()` — tras regenerar el botón de reintento
3. `resetState()` — al restaurar el botón después de completar o cancelar

### 6.8 Detección HTTPS y fallback manual

```javascript
// En $(document).ready():
const esSeguro = window.isSecureContext === true;
if (!esSeguro) {
    // Ocultar tarjeta de cámara: en HTTP no funciona en Android Chrome
    $('#card-qr').hide();

    // Mostrar aviso con instrucciones específicas para Android
    $('#aviso-https').css({ 'display': 'flex', 'align-items': 'flex-start', 'gap': '12px' }).html(
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
    // Auto-foco en la entrada manual
    setTimeout(function() { document.getElementById('inp-manual').focus(); }, 350);
} else {
    bindBtnActivarQR();
}
```

### 6.9 Bloque de diagnóstico inline (debug en producción)

Colocar justo debajo del div de la cámara. No depende de jQuery (se ejecuta síncrono):

```html
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
        'isSecureContext: <b>' + sc + '</b><br>' +
        'mediaDevices: <b>' + md + '</b><br>' +
        'jQuery: <b>' + jq + '</b><br>' +
        'Html5Qrcode: <b>' + h5 + '</b><br>' +
        'URL: ' + location.href;
})();
</script>
```

Actualizar en `$(document).ready()` con texto más compacto para producción:
```javascript
const diagEl = document.getElementById('diag-info');
if (diagEl) {
    diagEl.textContent = 'secure:' + window.isSecureContext
        + ' · cam:' + !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia)
        + ' · ' + location.protocol + '//' + location.hostname;
}
```

---

## 7. Activación de NFC (NDEFReader API)

### 7.1 Soporte del navegador

La API `NDEFReader` está disponible solo en:
- Android Chrome 89+
- Con HTTPS o localhost
- **No disponible en iOS Safari** (WebKit no soporta Web NFC)

### 7.2 Activar lectura continua

```javascript
function iniciarNFC() {
    // Verificar soporte antes de cualquier cosa
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
                        procesarEscaneo(codigo);   // ← función de negocio de la pantalla
                        break;
                    }
                }
            };
        })
        .catch(() => {
            // Error al iniciar scan (permisos denegados o hardware no disponible)
            document.getElementById('btn-nfc').style.display = 'none';
        });
}
```

### 7.3 Detener lectura NFC

```javascript
function detenerNFC() {
    if (state.nfcController) {
        state.nfcController.abort();
        state.nfcController = null;
    }
}
```

Llamar a `detenerNFC()` siempre en:
- Cambio de fase que abandona la pantalla de escaneo
- Completar la operación exitosamente
- Cancelar la operación
- `resetState()` al iniciar nueva sesión

### 7.4 Botón NFC en HTML

```html
<button class="btn-app btn-app-secondary btn-nfc-full" id="btn-nfc">
    <i class="fa fa-wifi me-2"></i> Activar lectura NFC
</button>
```

```css
.btn-nfc-full { border-radius: 10px !important; width: 100%; margin-bottom: 12px; }
```

Animación visual "NFC activo":
```css
.nfc-active {
    color: var(--color-brand) !important;
    animation: pulse 1.4s infinite;
}
@keyframes pulse {
    0%, 100% { opacity: 1  }
    50%       { opacity: .4 }
}
```

---

## 8. Retroalimentación Táctil (Vibración)

```javascript
function vibrar(tipo) {
    if (!navigator.vibrate) return;  // iOS 17 no soporta, no rompe nada
    if (tipo === 'ok')   navigator.vibrate([100]);
    if (tipo === 'err')  navigator.vibrate([200, 100, 200]);
    if (tipo === 'warn') navigator.vibrate([150, 50, 150]);
}
```

Convención de uso:
| Tipo   | Cuándo usar |
|--------|-------------|
| `ok`   | Elemento/acción aceptado correctamente |
| `err`  | Error — elemento rechazado, no encontrado |
| `warn` | Advertencia — ya procesado, conflicto, pregunta |

---

## 9. Modal de Ayuda (Bottom Sheet CSS Puro)

No usa Bootstrap Modal. Es un bottom sheet nativo que se anima con CSS + toggle de clase.

### 9.1 CSS completo

```css
.modal-ayuda {
    position: fixed;
    inset: 0;
    z-index: 9999;
    display: flex;
    align-items: flex-end;
    pointer-events: none;          /* invisible e inaccesible por defecto */
}
.modal-ayuda--open              { pointer-events: auto; }

.modal-ayuda__backdrop {
    position: absolute;
    inset: 0;
    background: rgba(0,0,0,.45);
    opacity: 0;
    transition: opacity .25s;
}
.modal-ayuda--open .modal-ayuda__backdrop { opacity: 1; }

.modal-ayuda__sheet {
    position: relative;
    width: 100%;
    background: #fff;
    border-radius: 20px 20px 0 0;
    max-height: 88vh;
    overflow-y: auto;
    transform: translateY(100%);   /* oculto bajo la pantalla */
    transition: transform .3s cubic-bezier(.4,0,.2,1);
    padding-bottom: env(safe-area-inset-bottom, 0px);
}
.modal-ayuda--open .modal-ayuda__sheet { transform: translateY(0); }

.modal-ayuda__header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 16px 20px 12px;
    font-size: 1rem;
    font-weight: 700;
    color: #1a237e;
    border-bottom: 1px solid #eee;
    position: sticky;
    top: 0;
    background: #fff;
    z-index: 1;
}
.modal-ayuda__close {
    background: none;
    border: none;
    font-size: 1.6rem;
    line-height: 1;
    color: #999;
    padding: 0 4px;
    cursor: pointer;
}
.modal-ayuda__body  { padding: 18px 20px 24px; }

/* Items de ayuda */
.ayuda-item            { display: flex; gap: 14px; align-items: flex-start; margin-bottom: 20px; }
.ayuda-item:last-child { margin-bottom: 0; }
.ayuda-item__icon      { flex-shrink: 0; width: 42px; height: 42px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.1rem; }
.ayuda-item__title     { font-size: .9rem; font-weight: 700; color: #222; margin-bottom: 4px; }
.ayuda-item__desc      { font-size: .82rem; color: #555; line-height: 1.5; }
```

### 9.2 HTML estructura

```html
<div id="modalAyuda" class="modal-ayuda" role="dialog" aria-modal="true" aria-labelledby="ayuda-titulo">
    <!-- Backdrop: click fuera cierra -->
    <div class="modal-ayuda__backdrop"
         onclick="document.getElementById('modalAyuda').classList.remove('modal-ayuda--open')"></div>
    <div class="modal-ayuda__sheet">
        <div class="modal-ayuda__header">
            <span id="ayuda-titulo"><i class="fa fa-info-circle me-2"></i>¿Cómo funciona?</span>
            <button onclick="document.getElementById('modalAyuda').classList.remove('modal-ayuda--open')"
                    class="modal-ayuda__close" aria-label="Cerrar">&times;</button>
        </div>
        <div class="modal-ayuda__body">
            <div class="ayuda-item">
                <div class="ayuda-item__icon" style="background:#e3f2fd;color:#1a237e;">
                    <i class="fa fa-list"></i>
                </div>
                <div>
                    <div class="ayuda-item__title">Título del concepto</div>
                    <div class="ayuda-item__desc">
                        Descripción detallada del comportamiento esperado.
                        Usar <strong>negritas</strong> para destacar lo importante.
                    </div>
                </div>
            </div>
            <!-- ... más items ... -->
        </div>
    </div>
</div>
```

### 9.3 Abrir y cerrar

```javascript
// Abrir desde el botón del AppBar
document.getElementById('btn-ayuda').onclick = function() {
    document.getElementById('modalAyuda').classList.add('modal-ayuda--open');
};

// Cerrar desde backdrop o botón ×
document.getElementById('modalAyuda').classList.remove('modal-ayuda--open');
```

---

## 10. Modal de Bootstrap 4 (formularios mobile-friendly)

Para modales con selects o formularios de confirmación.

### 10.1 CSS adaptaciones

```css
.modal-content {
    border-radius: 18px 18px 14px 14px;
    overflow: hidden;
}
.modal-header {
    background: var(--color-prep);   /* personalizar por contexto */
    color: #fff;
    border-radius: 0;
}
.modal-header .btn-close { filter: invert(1); }

/* Inputs/selects de tamaño táctil dentro del modal */
.modal-select, .modal-body .form-control {
    height: 52px;
    font-size: 1rem;
    border-radius: 10px;
}
```

### 10.2 Fix Bootstrap 4 — apertura múltiple

Bootstrap 4 falla al abrir el mismo modal dos veces seguidas. Solución obligatoria:

```javascript
function abrirModal() {
    var $m = $('#miModal');
    $m.data('bs.modal', null);   // reset estado interno de Bootstrap
    $m.modal({ backdrop: true, keyboard: true, show: true });
}
```

Limpieza en cierre (evita que el body quede bloqueado):
```javascript
$('#miModal').on('hidden.bs.modal', function() {
    $('.modal-backdrop').remove();
    $('body').removeClass('modal-open').css('padding-right', '');
});
```

### 10.3 Botón de cierre Bootstrap 4

```html
<button type="button" class="close" data-dismiss="modal" aria-label="Cerrar">
    <span aria-hidden="true">&times;</span>
</button>
```

---

## 11. Estado Global (patrón `state`)

Centraliza todos los datos de sesión activa en un objeto único por pantalla:

```javascript
const state = {
    // Entidad principal cargada (datos del registro activo)
    entidad: null,
    // Sesión de operación activa (ID + metadata)
    sesion: null,
    // Último progreso recibido del servidor
    progreso: null,
    // AbortController para NFC (permite cancelarlo limpiamente)
    nfcController: null,
    // Instancia de Html5Qrcode activa
    qrScanner: null,
    // Contexto para operaciones de modales (elemento en foco)
    elementoActivo: null,
    // Listas auxiliares cargadas del servidor
    opcionesAuxiliares: []
};
```

`resetState()` limpia todo y restaura la UI inicial:

```javascript
function resetState() {
    state.entidad      = null;
    state.sesion       = null;
    state.progreso     = null;
    state.elementoActivo = null;

    detenerNFC();
    detenerQR();

    // Restaurar inputs
    document.getElementById('inp-manual').value = '';

    // Restaurar botón de cámara
    const ph = document.getElementById('qr-placeholder');
    if (ph) {
        ph.innerHTML = '<button id="btn-activar-qr" class="btn-app btn-app-primary"><i class="fa fa-camera me-2"></i>Activar Cámara</button>';
        ph.style.display = 'block';
        bindBtnActivarQR();
    }
    const readerEl = document.getElementById('qr-reader');
    if (readerEl) { readerEl.style.display = 'none'; readerEl.innerHTML = ''; }

    // Limpiar identificadores del AppBar
    document.getElementById('nav-numero').textContent = '';
}
```

---

## 12. Patrón AJAX

```javascript
const CTR = '../../controller/[entidad].php';

function post(op, data) {
    return $.post(CTR + '?op=' + op, data);
}
```

Uso estándar con feedback:
```javascript
post('operacion', { campo: valor })
    .done(function(data) {
        if (data.success) {
            vibrar('ok');
            feedback('fb-elemento', data.message || 'Operación correcta', 'ok');
        } else {
            vibrar('err');
            feedback('fb-elemento', data.message || 'Error', 'err');
        }
    })
    .fail(function() {
        vibrar('err');
        feedback('fb-elemento', 'Error de conexión', 'err');
    });
```

---

## 13. Seguridad en Renderizado HTML Dinámico

**SIEMPRE** sanitizar datos del servidor antes de insertarlos en el DOM con `innerHTML`:

```javascript
function escHtml(str) {
    if (!str) return '';
    return String(str)
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
```

Uso en template literals:
```javascript
cont.innerHTML = datos.map(d => `
    <div class="elem-row">
        <span class="fw-semibold">${escHtml(d.nombre)}</span>
        <div class="text-muted small">${escHtml(d.descripcion || '')}</div>
    </div>
`).join('');
```

⚠️ **NUNCA** hacer `innerHTML = dato_sin_sanitizar`.

---

## 14. Scaffolding Completo (plantilla mínima de copia-pega)

```php
<?php
session_start();
if (!isset($_SESSION['sesion_iniciada']) || $_SESSION['sesion_iniciada'] !== true) {
    header('Location: ../../index.php'); exit;
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
    <title>Título — MDR</title>

    <link href="../../public/lib/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="../../public/lib/@fortawesome/fontawesome-free/css/all.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">

    <style>
        /* 1. Variables */
        :root {
            --color-brand: #1a237e; --color-prep: #795548;
            --color-ok: #198754;   --color-err: #dc3545; --color-warn: #fd7e14;
            --safe-top: env(safe-area-inset-top, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
            --font-base: 1rem; --touch-min: 52px;
        }
        /* 2. Reset */
        *, *::before, *::after { box-sizing: border-box; }
        html, body { height: 100%; margin: 0; background: #eef0f5; font-size: var(--font-base);
            -webkit-tap-highlight-color: transparent; overscroll-behavior: none; }
        body { padding-top: var(--safe-top); padding-bottom: calc(var(--safe-bottom) + 76px);
            padding-left: var(--safe-left); padding-right: var(--safe-right); }
        /* 3. Pegar aquí los bloques CSS de los componentes necesarios */
    </style>
</head>
<body>

    <!-- AppBar -->
    <div id="app-bar">
        <button id="btn-appbar-back" class="btn-back" onclick="appBarBack()"><i class="fa fa-chevron-left"></i></button>
        <div class="brand"><i class="fa fa-boxes"></i><span>Título</span></div>
        <span id="nav-numero" class="sub"></span>
        <button id="btn-ayuda" onclick="document.getElementById('modalAyuda').classList.add('modal-ayuda--open')"
                style="background:transparent;border:none;color:#fff;opacity:.75;font-size:1.2rem;padding:8px;"><i class="fa fa-question-circle"></i></button>
    </div>

    <!-- Fase 1 -->
    <div id="phase1" class="phase active">
        <div class="page-wrap">
            <!-- Aviso HTTPS (oculto por defecto) -->
            <div id="aviso-https" class="fb-banner fb-warn mb-3" style="display:none;"></div>
            <!-- Card cámara QR -->
            <div class="app-card" id="card-qr">
                <div class="app-card-body">
                    <div class="app-card-title"><i class="fa fa-qrcode me-1"></i> Escanea el QR</div>
                    <div id="qr-placeholder" class="mb-2">
                        <button id="btn-activar-qr" class="btn-app btn-app-primary" onclick="iniciarQR()">
                            <i class="fa fa-camera me-2"></i> Activar Cámara
                        </button>
                    </div>
                    <div id="qr-reader" style="display:none;"></div>
                </div>
            </div>
            <!-- Diagnóstico inline -->
            <div id="diag-info" style="background:#1a237e;color:#fff;font-size:.75rem;border-radius:10px;padding:10px 14px;margin-bottom:12px;line-height:1.8;"></div>
            <script>
            (function() {
                var el = document.getElementById('diag-info');
                if (!el) return;
                el.innerHTML = 'secure:<b>' + window.isSecureContext + '</b> · cam:<b>'
                    + !!(navigator.mediaDevices && navigator.mediaDevices.getUserMedia) + '</b> · ' + location.href;
            })();
            </script>
            <!-- Entrada manual como fallback -->
            <div class="app-card">
                <div class="app-card-body">
                    <div class="app-card-title"><i class="fa fa-keyboard me-1"></i> O introduce el número</div>
                    <div class="inp-group">
                        <input type="text" id="inp-manual" class="inp-scan"
                               placeholder="Código" autocomplete="off" inputmode="text">
                        <button class="btn-app btn-app-primary" id="btn-buscar"
                                style="border-radius:0 10px 10px 0;width:56px;min-width:56px;">
                            <i class="fa fa-search"></i>
                        </button>
                    </div>
                    <div id="fb-busqueda" class="fb-banner fb-hidden"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Fase 2 (ejemplo con lista) -->
    <div id="phase2" class="phase">
        <div class="page-wrap">
            <!-- Cabecera entidad -->
            <div class="ppto-strip">
                <div>
                    <div class="num" id="p2-titulo"></div>
                    <div class="meta" id="p2-subtitulo"></div>
                </div>
                <button class="btn-icon" id="btn-volver-p1"><i class="fa fa-undo text-secondary"></i></button>
            </div>
            <!-- Acción principal sticky -->
            <div class="sticky-action-bar">
                <button id="btn-accion-principal" class="btn-app btn-app-primary">
                    <i class="fa fa-arrow-right"></i> Siguiente
                </button>
            </div>
            <!-- Lista dinámica -->
            <div class="app-card" id="card-lista">
                <div id="lista-items"></div>
            </div>
        </div>
    </div>

    <!-- Fase 3 (ejemplo con escaneo) -->
    <div id="phase3" class="phase">
        <div class="page-wrap">
            <div class="ppto-strip">
                <div class="num small" id="p3-titulo"></div>
                <span id="p3-contadores" class="badge bg-primary fs-6 px-3 py-2"></span>
            </div>
            <div class="app-card">
                <div class="app-card-body">
                    <div class="app-card-title"><i class="fa fa-tag me-1"></i> Escanea la etiqueta</div>
                    <div class="inp-group">
                        <input type="text" id="inp-codigo-elem" class="inp-scan text-uppercase"
                               placeholder="Código NFC / manual" autocomplete="off"
                               autocapitalize="characters" inputmode="text">
                        <button class="btn-app btn-app-success" id="btn-escanear-manual"
                                style="border-radius:0 10px 10px 0;width:56px;min-width:56px;">
                            <i class="fa fa-check"></i>
                        </button>
                    </div>
                    <button class="btn-app btn-app-secondary btn-nfc-full" id="btn-nfc">
                        <i class="fa fa-wifi me-2"></i> Activar lectura NFC
                    </button>
                    <div id="fb-escaneo" class="fb-banner fb-info">
                        <i class="fa fa-info-circle"></i>
                        <span>Acerca el tag NFC o introduce el código</span>
                    </div>
                </div>
            </div>
            <div class="app-card"><div id="lista-escaneados"></div></div>
        </div>
        <!-- Barra inferior fija -->
        <div id="bottom-bar" class="visible">
            <button id="btn-completar" class="btn-app btn-app-success mb-2" disabled>
                <i class="fa fa-check-double"></i> Completar
            </button>
            <button id="btn-cancelar" class="btn-app btn-app-outline"
                    style="color:var(--color-err);border-color:var(--color-err);">
                Cancelar
            </button>
        </div>
    </div>

    <!-- Fase 4 (completado) -->
    <div id="phase4" class="phase">
        <i class="fa fa-check-circle icon-done mb-3"></i>
        <h3 class="fw-bold mb-2">¡Completado!</h3>
        <p class="text-muted mb-4" id="p4-resumen"></p>
        <button id="btn-nueva" class="btn-app btn-app-primary" style="max-width:280px;">
            <i class="fa fa-plus"></i> Nueva Operación
        </button>
    </div>

    <!-- Modal Ayuda (bottom sheet CSS) -->
    <div id="modalAyuda" class="modal-ayuda" role="dialog" aria-modal="true">
        <div class="modal-ayuda__backdrop"
             onclick="document.getElementById('modalAyuda').classList.remove('modal-ayuda--open')"></div>
        <div class="modal-ayuda__sheet">
            <div class="modal-ayuda__header">
                <span><i class="fa fa-info-circle me-2"></i>¿Cómo funciona?</span>
                <button onclick="document.getElementById('modalAyuda').classList.remove('modal-ayuda--open')"
                        class="modal-ayuda__close">&times;</button>
            </div>
            <div class="modal-ayuda__body">
                <!-- Añadir .ayuda-item según necesidades -->
            </div>
        </div>
    </div>

    <!-- Scripts (orden obligatorio) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="../../public/lib/bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../../public/lib/html5-qrcode/html5-qrcode.min.js"></script>
    <script>
        const ID_USUARIO = <?= $idUsuario; ?>;
    </script>
    <script src="mipantalla.js?v=<?= time(); ?>"></script>
    <script>
        function appBarBack() {
            if (document.getElementById('phase3').classList.contains('active')) {
                detenerNFC(); mostrarFase(2);
            } else if (document.getElementById('phase2').classList.contains('active')) {
                resetState(); mostrarFase(1);
            }
        }
        document.addEventListener('phaseChange', function(e) {
            document.getElementById('btn-appbar-back').style.display =
                (e.detail.phase > 1 && e.detail.phase < 4) ? 'block' : 'none';
            const bb = document.getElementById('bottom-bar');
            if (bb) bb.classList.toggle('visible', e.detail.phase === 3);
        });
    </script>
</body>
</html>
```

---

## 15. Checklist Mobile-First

Verificar antes de dar por terminada cualquier pantalla mobile:

- [ ] `viewport-fit=cover` en meta viewport
- [ ] Meta tags PWA (`mobile-web-app-capable`, `apple-mobile-web-app-capable`)
- [ ] Safe areas en `body` con `env(safe-area-inset-*)`
- [ ] `--touch-min: 52px` aplicado a todos los botones interactivos
- [ ] `overscroll-behavior: none` en body (sin rebote de scroll)
- [ ] `-webkit-tap-highlight-color: transparent` en body
- [ ] Barra inferior con `padding-bottom: calc(10px + var(--safe-bottom))`
- [ ] `backdrop-filter: blur()` en `#bottom-bar`
- [ ] Detección HTTPS con `window.isSecureContext` y fallback manual
- [ ] Diagnóstico inline de soporte (`isSecureContext`, `mediaDevices`)
- [ ] Vibración táctil con `navigator.vibrate()` en acciones
- [ ] `escHtml()` en TODOS los datos renderizados con `innerHTML`
- [ ] `detenerNFC()` en todos los puntos de salida de la fase de escaneo
- [ ] `detenerQR()` tras lectura exitosa y en `resetState()`
- [ ] `bindBtnActivarQR()` llamado en `ready`, `mostrarErrorCamara` y `resetState`
- [ ] `autocapitalize="characters"` en inputs de código
- [ ] `autocomplete="off"` en inputs de escaneo
- [ ] Fix Bootstrap 4 modal (`data('bs.modal', null)` antes de abrir)
- [ ] Cleanup backdrop Bootstrap 4 en `hidden.bs.modal`
- [ ] `CustomEvent('phaseChange')` disparado en `mostrarFase()`
- [ ] AppBar muestra `btn-back` solo en fases intermedias

---

*Referencia: `view/Picking/index.php` + `view/Picking/picking.js`*  
*Fecha: 24/03/2026*
