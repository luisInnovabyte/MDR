# Picking — Diagrama de flujo

## Fases de la aplicación

```
┌─────────────────────────────────────────────────────────────────┐
│  FASE 1 · Escanear QR del albarán de carga                      │
│                                                                  │
│   [ Cámara QR ]  ──o──  [ Input manual número presupuesto ]     │
│                                                                  │
│   ► Llama: salida_almacen.php?op=buscar_presupuesto             │
│   ► Llama: salida_almacen.php?op=iniciar_salida                 │
│   ► Guarda en state: presupuesto, salida, necesidades           │
└──────────────────────────┬──────────────────────────────────────┘
                           │ presupuesto encontrado + salida creada
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│  FASE 2 · Resumen de necesidades                                │
│                                                                  │
│   Listado de artículos requeridos con cantidad total             │
│   Sólo lectura — sin progreso en tiempo real                     │
│   Botón ► "Empezar escaneo"                                     │
└──────────────────────────┬──────────────────────────────────────┘
                           │ usuario pulsa Empezar escaneo
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│  FASE 3 · Escaneo de elementos → POOL LOCAL                     │
│                                                                  │
│   NFC / Input manual  ──►  state.pool.push(codigo)              │
│   SIN escritura en BD mientras se escanea                       │
│                                                                  │
│   Lista en pantalla: elementos en el pool con botón ✕           │
│   Botón ► "Comparar"                                            │
└──────────────────────────┬──────────────────────────────────────┘
                           │ usuario pulsa Comparar
                           ▼
                ┌──────────────────────┐
                │ POST comparar_pool() │  ← sin writes en BD
                │ devuelve 4 cubos     │
                └──────────┬───────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│  FASE 4 · Resultado de comparación                              │
│                                                                  │
│  ✅ Correctos        — elementos que cubren línea del albarán   │
│  ❌ Faltan           — líneas del albarán sin elemento físico   │
│       └─► [Asignar sustituto ▼] en cada fila de faltante       │
│           Abre panel con candidatos (Sobran ∪ No_relacionados)  │
│           Filtro por defecto: misma familia del artículo faltante│
│           Toggle [📦 Misma familia / 🔓 Todas] (client-side)   │
│  ➕ Sobran (backup)  — elementos cuyo artículo ya está cubierto │
│  ❓ No relacionados  — artículo no está en el albarán           │
│                                                                  │
│  REGLA: Confirmar queda BLOQUEADO si hay faltantes sin          │
│         cubrir (sin escaneado y sin sustituto asignado)          │
│                                                                  │
│  [← Volver a escanear]        [Confirmar ▶]                    │
└──────────────────────────┬──────────────────────────────────────┘
                           │ todos los faltantes cubiertos + pulsa Confirmar
                           ▼
                ┌──────────────────────┐
                │ POST confirmar_pool()│  ← transacción BD
                │ INSERT lineas        │
                │ estado → PREP        │
                └──────────┬───────────┘
                           │
                           ▼
┌─────────────────────────────────────────────────────────────────┐
│  FASE 5 · Completado                                            │
│                                                                  │
│   ✔ Salida guardada — elementos en estado PREP                  │
│   Botón ► "Nueva salida"  (resetea la app)                      │
│                                                                  │
│   (El paso PREP → ALQU ocurre más tarde desde gestion_almacen) │
└─────────────────────────────────────────────────────────────────┘
```

## Clasificación de elementos en la comparación

| Cubo | Condición | Rol en sustitución | Acción en BD al confirmar |
|------|-----------|-------------------|---------------------------|
| **Correcto** | `id_articulo` en albarán + cantidad no cubierta aún | Nunca candidato | INSERT con `id_linea_ppto` asignado, `es_backup=0` |
| **Sobra (backup)** | `id_articulo` en albarán pero cantidad ya cubierta | Candidato disponible | Si asignado: INSERT con `id_linea_ppto` del faltante, `es_backup=0`; si no: INSERT con `es_backup=1` |
| **No relacionado** | `id_articulo` NO está en el albarán | Candidato disponible | Si asignado: INSERT con `id_linea_ppto` del faltante, `es_backup=0`; si no: INSERT con `id_linea_ppto=NULL`, `es_backup=1` |
| **Faltante** | Línea del albarán sin elemento físico ni sustituto | Receptor de sustitución (tiene botón "Asignar sustituto ▼") | ❌ bloquea Confirmar si sigue sin cubrir |

## Lógica de candidatos de sustitución

- **Candidatos** = Sobran ∪ No_relacionados (ambos grupos pueden sustituir a un faltante)
- **Filtro por defecto**: candidatos con `id_familia == id_familia del artículo faltante`
- **Toggle**: chip `[📦 Misma familia]` / `[🔓 Todas las familias]` — sólo filtra visualmente en cliente
- Un candidato sólo puede cubrir **un** faltante a la vez (queda bloqueado al asignarse)
- La asignación es reversible: botón `✕ Quitar` en la fila del faltante
- `comparar_pool` debe devolver `id_familia` + `nombre_familia` para todos los elementos del pool

## Reglas de negocio

- Un código escaneado dos veces en el pool → se ignora el duplicado (warning visual, no bloquea)
- El estado del elemento debe ser `DISP` o `TERC` para entrar en el pool; si no → warning visual en la fila, pero NO impide añadirlo ni bloquea el flujo (el usuario decide si lo quita)
- `confirmar_pool` es atómico: si falla algún INSERT → rollback completo
- La sesión de escaneo (pool) es 100% client-side hasta pulsar Confirmar
- `confirmar_pool` no cambia a `ALQU`; eso sigue siendo responsabilidad del flujo de `completar_salida` desde gestion_almacen
