# 📦 Picking / Salida de Almacén — MDR ERP

> Sistema mobile-first para que los técnicos preparen el material de un presupuesto
> escaneando etiquetas NFC (o código manual / QR) antes de cada evento.

**Rama activa:** `tecnicos`  
**Fecha de inicio:** 2026-03-11  
**Migración BD:** `BD/migrations/20260311_01_salida_almacen.sql`

---

## 📂 Archivos del módulo

| Tipo | Ruta |
|------|------|
| Vista (PWA mobile) | `view/Picking/index.php` |
| Lógica JS | `view/Picking/picking.js` |
| Controller | `controller/salida_almacen.php` |
| Modelo | `models/SalidaAlmacen.php` |
| Migración SQL | `BD/migrations/20260311_01_salida_almacen.sql` |

---

## 🗄️ Estructuras de BD creadas

### Nuevas tablas

| Tabla | Descripción |
|-------|-------------|
| `salida_almacen` | Cabecera de cada sesión de picking (1 por presupuesto activo) |
| `linea_salida_almacen` | Cada elemento físico escaneado (incluye backup) |
| `movimiento_elemento_salida` | Historial de desplazamientos físicos durante el evento |

### Nuevo estado de elemento

| Código | Descripción | Color | Permite alquiler |
|--------|-------------|-------|-----------------|
| `PREP` | En preparación | `#795548` (marrón) | NO |

### Vistas SQL

| Vista | Uso |
|-------|-----|
| `vista_progreso_salida` | Progreso del picking agrupado por artículo (requerido vs escaneado) |
| `vista_ubicacion_actual_elemento` | Ubicación actual de cada elemento (último movimiento) |

### Transiciones de estado del elemento

```
DISP ──► PREP   (al escanear un elemento en picking)
PREP ──► ALQU   (al completar la salida)
PREP ──► DISP   (al cancelar la salida / soft-delete línea)
```

---

## 🔄 Flujo en 4 fases (UI)

```
┌──────────────────────────────────────────────────────────┐
│  FASE 1 — Identificar presupuesto                        │
│  • Escaneo QR (html5-qrcode)                             │
│  • O entrada manual del número de presupuesto            │
│  • Verifica estado: solo APROBADO | CONFIRMADO | EN_CURSO│
│  • Recupera sesión en_proceso si ya existe               │
└──────────────────────────────────────────────────────────┘
           │
           ▼
┌──────────────────────────────────────────────────────────┐
│  FASE 2 — Necesidades del presupuesto                    │
│  • Lista artículos con cantidades requeridas             │
│  • Ubicación planificada de cada artículo                │
│  • Barra de progreso global                              │
│  • Botón "Iniciar Escaneo" → crea salida_almacen          │
└──────────────────────────────────────────────────────────┘
           │
           ▼
┌──────────────────────────────────────────────────────────┐
│  FASE 3 — Escaneo de elementos                           │
│  • NFC (Web NFC API, Chrome Android)                     │
│  • Entrada manual / lector Bluetooth (input text)        │
│  • Feedback inmediato por tipo de respuesta              │
│  • Lista de elementos ya escaneados con botón reubicar   │
│  • Botón "Completar Salida" (habilitado al 100%)         │
└──────────────────────────────────────────────────────────┘
           │
           ▼
┌──────────────────────────────────────────────────────────┐
│  FASE 4 — Confirmación final                             │
│  • Resumen: N elementos preparados (M backup)            │
│  • Botón "Nueva Salida" → resetea a Fase 1               │
└──────────────────────────────────────────────────────────┘
```

---

## 🔌 API del controller (`controller/salida_almacen.php`)

Todas las operaciones son peticiones `POST` con `?op=<operacion>`.

| `op` | Parámetros POST | Descripción |
|------|-----------------|-------------|
| `buscar_presupuesto` | `numero_presupuesto` | Busca presupuesto y devuelve cabecera + necesidades + salida activa |
| `iniciar_salida` | `id_presupuesto`, `id_version_presupuesto`, `id_usuario`, `numero_presupuesto` | Crea nueva salida (evita duplicados) |
| `escanear` | `id_salida_almacen`, `codigo_elemento`, `es_backup` (0\|1) | Procesa un escaneo NFC/manual |
| `progreso` | `id_salida_almacen` | Devuelve progreso por artículo + totales |
| `elementos_escaneados` | `id_salida_almacen` | Lista elementos con su ubicación actual |
| `completar` | `id_salida_almacen` | Cierra la salida y mueve todos a estado ALQU |
| `cancelar` | `id_salida_almacen` | Cancela, revierte elementos a DISP |
| `listar` | `id_presupuesto` (opcional) | Historial de salidas de un presupuesto |
| `registrar_movimiento` | `id_salida_almacen`, `id_elemento`, `id_ubicacion_destino`, `id_usuario`, `observaciones` | Mueve un elemento a otra ubicación |
| `get_ubicacion_actual` | `id_salida_almacen`, `id_elemento` | Ubicación actual de un elemento |
| `get_historial_movimientos` | `id_salida_almacen`, `id_elemento` | Historial completo de movimientos |
| `mapa_ubicaciones` | `id_salida_almacen` | Mapa de elementos + ubicaciones disponibles del cliente |

---

## 🎯 Tipos de respuesta en `escanear`

| `tipo` | Descripción | Vibración | Color UI |
|--------|-------------|-----------|----------|
| `correcto` | Elemento escaneado OK | 1 pulso corto | Verde |
| `backup` | Material adicional fuera de cantidad | 1 pulso corto | Amarillo |
| `ya_asignado` | Ya estaba en esta salida → abre modal reubicación | 2 pulsos | Naranja |
| `cantidad_completada` | Artículo ya completo → pregunta si añadir como backup | 2 pulsos | Naranja |
| `articulo_no_pertenece` | El artículo no está en el presupuesto | 2 pulsos dobles | Rojo |
| `no_disponible` | Estado del elemento no permite alquiler | 2 pulsos dobles | Rojo |
| `elemento_no_encontrado` | Código no existe en la BD | 2 pulsos dobles | Rojo |

---

## 🔧 Dependencias externas (frontend)

| Biblioteca | Versión | Uso |
|-----------|---------|-----|
| `html5-qrcode` | incluida en `/public/lib/html5-qrcode/` | Lectura de QR con la cámara |
| Web NFC API (`NDEFReader`) | Nativa Chrome Android | Lectura de tags NFC |
| Bootstrap 5 | 5.0.2 | Layout y modal de reubicación |
| SweetAlert2 | 11.7.32 | Confirmaciones y toasts |
| jQuery | 3.7.1 | AJAX y eventos |

> ⚠️ La Web NFC API **solo funciona en Chrome para Android** y requiere **HTTPS**.
> La vista muestra un aviso si se accede por HTTP y deshabilita la cámara.
> En entornos sin NFC, el técnico puede introducir el código manualmente o usar
> un lector Bluetooth (que actúa como teclado).

---

## ✅ Estado de implementación

### Completado
- [x] Migración SQL: tablas `salida_almacen`, `linea_salida_almacen`, `movimiento_elemento_salida`
- [x] Estado `PREP` (En preparación) en `estado_elemento`
- [x] Vistas SQL `vista_progreso_salida` y `vista_ubicacion_actual_elemento`
- [x] Modelo `SalidaAlmacen.php` con todos los métodos
- [x] Controller `salida_almacen.php` con todas las operaciones
- [x] Vista mobile-first `view/Picking/index.php` (standalone, sin sidebar)
- [x] Lógica JS `picking.js` — las 4 fases completas
- [x] Escaneo NFC con fallback a entrada manual
- [x] Escaneo QR del presupuesto (html5-qrcode)
- [x] Manejo de backup (cantidad ya cubierta)
- [x] Modal de reubicación de elementos
- [x] Completar / Cancelar salida con reversión de estados

### Pendiente
- [ ] **Ejecutar migracion SQL** en la base de datos de producción
  ```
  BD/migrations/20260311_01_salida_almacen.sql
  ```
- [ ] **Autenticación real**: sustituir `const ID_USUARIO = 1;` en `picking.js` por la sesión del usuario (integrar con el sistema de login del ERP)
- [ ] **Acceso desde el menú**: añadir enlace en el sidebar del área técnica
  - URL: `view/Picking/index.php`
  - Rol con acceso: Técnico (id_rol = 5)
- [ ] **Verificar códigos de estado** del presupuesto: el controller acepta `APROBADO | CONFIRMADO | EN_CURSO`. Verificar que estos códigos coinciden exactamente con los valores de `estado_general_presupuesto` en la base de datos.
- [ ] **QR del presupuesto**: decidir si el QR contiene solo el número o una URL con `?n=P-00001/2026`. El código JS ya soporta ambos formatos.
- [ ] **Librería html5-qrcode**: verificar que está copiada en `public/lib/html5-qrcode/html5-qrcode.min.js`
- [ ] **Pruebas NFC en dispositivo real** (Android + Chrome)
- [ ] **Vista de historial de salidas** para el área de administración (listado de todas las sesiones de picking con sus elementos)

---

## 🗺️ Integración con el resto del ERP

```
Presupuesto (APROBADO)
  │
  └─► Picking (salida_almacen)
        │
        ├─► Elementos: DISP → PREP → ALQU
        │
        ├─► Líneas escaneadas (linea_salida_almacen)
        │     └─► Movimientos entre ubicaciones (movimiento_elemento_salida)
        │
        └─► Al completar: elementos en estado ALQU
              │
              └─► [FUTURO] Devolución al almacén: ALQU → DISP
```

---

## 💡 Notas técnicas

- **Sin sidebar**: la vista de Picking es standalone (`view/Picking/index.php`) con su propio App Bar, optimizada para móvil en pantalla completa.
- **Sesión recuperable**: si el técnico cierra el navegador, al volver a escanear el mismo presupuesto recupera la sesión `en_proceso` automáticamente.
- **Soft delete**: cancelar una salida hace soft delete de líneas y movimientos, y revierte el estado de los elementos a `DISP`.
- **Transacción en completar_salida / cancelar_salida**: ambas operaciones usan `beginTransaction()` + `rollback()` para garantizar consistencia.
- **Backup**: si un artículo ya tiene sus unidades cubiertas, el sistema pregunta si se quiere añadir como backup. Los backups se marcan con `es_backup_linea_salida = 1` y no cuentan para la completitud del picking.
