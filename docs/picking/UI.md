# Picking — Diseño visual de fases

## Layout general

- App standalone mobile-first (sin sidebar ni header MDR)
- AppBar fijo arriba: logo + número presupuesto activo + botón ← atrás
- Safe-area insets (notch iOS / Android)
- Una sola `.phase.active` visible en cada momento
- Fondo general: `#eef0f5`

---

## Fase 1 — Escáner QR

```
┌──────────────────────────────┐
│  🏷 PICKING MDR              │  ← AppBar (#1a237e)
├──────────────────────────────┤
│                              │
│   ┌──────────────────────┐   │
│   │                      │   │
│   │   [  CÁMARA QR  ]    │   │  280×280 px centrado
│   │                      │   │
│   └──────────────────────┘   │
│                              │
│   ─────────── o ───────────  │
│                              │
│   Número de albarán:         │
│   ┌──────────────────────┐   │
│   │ P-00001/2026         │   │  .inp-scan
│   └──────────────────────┘   │
│   [       Buscar       ]     │  .btn-app-primary
│                              │
│   ┌──────────────────────┐   │
│   │ ⚠ Mensaje feedback   │   │  .fb-banner (oculto por defecto)
│   └──────────────────────┘   │
└──────────────────────────────┘
```

---

## Fase 2 — Necesidades del albarán (sólo lectura)

```
┌──────────────────────────────┐
│  ← │ 🏷 PICKING  │ P-00001  │
├──────────────────────────────┤
│  Cliente: Juan García        │
│  Evento:  Boda civil         │
├──────────────────────────────┤
│  ARTÍCULOS REQUERIDOS        │
│  ┌──────────────────────┐    │
│  │ Silla Tiffany  ×20   │    │  .art-row (solo lectura, sin barra de progreso)
│  │ 📍 Carpa principal   │    │
│  └──────────────────────┘    │
│  ┌──────────────────────┐    │
│  │ Mesa redonda   ×6    │    │
│  └──────────────────────┘    │
│            ...               │
├──────────────────────────────┤
│  [   Empezar escaneo  ▶  ]   │  .btn-app-primary full-width
└──────────────────────────────┘
```

---

## Fase 3 — Pool de escaneo (NUEVO comportamiento)

```
┌──────────────────────────────┐
│  ← │ 🏷 PICKING  │ P-00001  │
├──────────────────────────────┤
│  Escanea los elementos       │
│  ┌──────────────────────┐    │
│  │  📡 Activar NFC      │    │  .btn-nfc-full (animación pulso cuando activo)
│  └──────────────────────┘    │
│  ─── o introduce código ───  │
│  ┌──────────┐ [ Añadir ]     │
│  │ SILLA-001│                │  .inp-scan
│  └──────────┘                │
│                              │
│  EN EL POOL (12)             │  contador dinámico
│  ┌──────────────────────┐    │
│  │ ● SILLA-001      ✕  │    │  .pool-row  fondo verde si DISP
│  │ ● SILLA-002      ✕  │    │             fondo naranja si estado inválido
│  │ ● MESA-001       ✕  │    │
│  │ ...                  │    │
│  └──────────────────────┘    │
│                              │
├──────────────────────────────┤
│  [ Comparar (12 elementos) ] │  .btn-app-primary full-width, siempre habilitado
└──────────────────────────────┘
```

**Notas Fase 3:**
- Elemento con estado inválido (no DISP/TERC): fila en color `.text-danger` + icono ⚠, NO impide añadirlo
- Duplicado detectado: toast efímero "Ya está en el pool" y no se añade de nuevo
- El botón Comparar muestra siempre el recuento actual

---

## Fase 4 — Comparación (NUEVA FASE)

```
┌──────────────────────────────┐
│  ← │ 🏷 PICKING  │ P-00001  │
├──────────────────────────────┤
│                              │
│  ▼ ✅ CORRECTOS (10)         │  encabezado colapsable, fondo #d1e7dd
│  ┌──────────────────────┐    │
│  │ SILLA-001 · Silla Tif│    │  .cmp-row-correcto
│  │ SILLA-002 · Silla Tif│    │
│  │ ...                  │    │
│  └──────────────────────┘    │
│                              │
│  ▼ ❌ FALTAN (2)             │  siempre expandido; fondo #f8d7da
│  ┌──────────────────────────┐ │
│  │ Silla Tiffany  ×2        │ │  .cmp-row-falta
│  │  — sin elemento          │ │  texto gris itálico
│  │ [Asignar sustituto ▼]   │ │  .btn-asignar-sust (azul outline)
│  │                          │ │
│  │  ← panel inline (oculto por defecto) →          │
│  │  ┌──────────────────────┐│ │  .sust-panel (se abre al pulsar el botón)
│  │  │[📦 Misma fam.][🔓 Todas]││ │  chips toggle, client-side
│  │  │ SILLA-021 · Silla Tif ││ │  candidatos (Sobran ∪ No_relacionados)
│  │  │ [Asignar este]        ││ │  filtrado por familia por defecto
│  │  └──────────────────────┘│ │
│  └──────────────────────────┘ │
│                              │
│  ▼ ➕ SOBRAN / BACKUP (1)    │  encabezado colapsable, fondo #e2e3e5
│  ┌──────────────────────┐    │
│  │ SILLA-021 · Silla Tif│    │  .cmp-row-sobra
│  │ « cubre a Silla ×2 » │    │  badge cuando está asignado
│  └──────────────────────┘    │
│                              │
│  ▼ ❓ NO RELACIONADOS (1)    │  encabezado colapsable, fondo #fff3cd
│  ┌────────────────────────┐  │
│  │ FOCO-001 · Foco LED    │  │  .cmp-row-norel
│  │ (sin asignación)       │  │  texto gris — será backup
│  └────────────────────────┘  │
│                              │
│  ┌──────────────────────┐    │
│  │ ⚠ Faltan 2 sin cubrir│    │  #cmp-alerta-faltan .fb-banner .fb-err
│  └──────────────────────┘    │
│                              │
├──────────────────────────────┤
│ [← Volver] [Confirmar ▶]    │  Confirmar: disabled hasta 0 faltantes sin cubrir
└──────────────────────────────┘
```

**Lógica del panel "Asignar sustituto":**
- Se abre pulsando `[Asignar sustituto ▼]` en la fila del **faltante** (no en el candidato)
- El panel muestra candidatos = **Sobran ∪ No_relacionados** que NO están ya asignados
- **Por defecto**: filtrado a candidatos con `id_familia == id_familia del artículo faltante`
- El toggle `[📦 Misma familia]` / `[🔓 Todas las familias]` es puramente visual (client-side con `id_familia`)
- Al pulsar `[Asignar este]` en un candidato:
  - La fila del faltante muestra `« cubre: CODIGO-XXX »` + botón `✕ Quitar`
  - El candidato queda bloqueado (no aparece en otros paneles de faltante)
  - El candidato muestra badge `« cubre a NombreArt »` en su sección (Sobran / No rel.)
- Al pulsar `✕ Quitar`: deshace la asignación, ambos vuelven a su estado libre
- `#btn-confirmar` se habilita cuando todos los faltantes tienen sustituto o están cubiertos

---

## Fase 5 — Completado (era Fase 4)

```
┌──────────────────────────────┐
│       ✔                      │  .icon-done (verde grande, animación entrada)
│  ¡Picking guardado!          │
│  12 elementos en estado PREP │  texto dinámico con recuento
│                              │
│  [     Nueva salida     ]    │  .btn-app-primary, resetea toda la app
└──────────────────────────────┘
```

---

## Paleta de colores

| Token CSS | Valor | Uso |
|-----------|-------|-----|
| `--color-brand` | `#1a237e` | AppBar, botones primarios |
| `--color-ok` | `#198754` | Correctos, confirmaciones |
| `--color-err` | `#dc3545` | Faltan, errores |
| `--color-warn` | `#fd7e14` | Warnings, estado inválido |
| `--color-prep` | `#795548` | Estado PREP |
| `#6c757d` | gris Bootstrap | Sobran / backups |
| `#d1e7dd` | verde suave | Fondo sección Correctos |
| `#f8d7da` | rojo suave | Fondo sección Faltan |
| `#fff3cd` | amarillo suave | Fondo sección No relacionados |
| `#e2e3e5` | gris suave | Fondo sección Sobran |

## IDs HTML clave (referencia para JS)

| ID | Fase | Descripción |
|----|------|-------------|
| `#phase1` … `#phase5` | — | Contenedores de fase |
| `#pool-lista` | 3 | Lista de elementos en el pool |
| `#btn-comparar` | 3 | Botón Comparar |
| `#cmp-correctos` | 4 | Sección correctos |
| `#cmp-faltan` | 4 | Sección faltantes |
| `#cmp-sobran` | 4 | Sección sobran/backup |
| `#cmp-no-relacionados` | 4 | Sección no relacionados (sin selects) |
| `#cmp-alerta-faltan` | 4 | Banner de alerta faltantes |
| `#btn-volver-escaneo` | 4 | Volver a fase 3 |
| `#btn-confirmar` | 4 | Confirmar (disabled por defecto) |
| `.btn-asignar-sust` | 4 | Botón "Asignar sustituto ▼" en cada fila faltante |
| `.sust-panel` | 4 | Panel inline de candidatos (Sobran ∪ No_relacionados) |
| `.sust-chip-familia` | 4 | Toggle chips Misma familia / Todas |
| `#p5-resumen` | 5 | Texto resumen elementos PREP |
| `#btn-nueva-salida` | 5 | Reset y nueva salida |
