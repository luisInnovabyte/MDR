# Picking — ToDo de implementación

## Phase A — Backend: nuevos métodos en SalidaAlmacen.php

- [ ] **A1** `SalidaAlmacen.php` — método `comparar_pool(int $id_salida, array $codigos): array`
  - Sin ningún write en BD
  - Q1: recupera `id_version_presupuesto` de `salida_almacen`
  - Q2: `linea_presupuesto` agrupada por `id_articulo` → `cantidad_requerida` + lista de `id_linea_ppto` disponibles
  - Q3: para cada código → resuelve `{id_elemento, id_articulo, nombre_articulo, estado_elemento, id_familia, nombre_familia}`
    - JOIN: `elemento → articulo → familia` (para traer datos de familia usados en el filtro de sustitución client-side)
  - Clasifica en 4 cubos y devuelve:
    ```json
    {
      "correctos":       [{ "codigo_elemento", "id_elemento", "id_articulo", "nombre_articulo", "id_familia", "nombre_familia", "id_linea_ppto" }],
      "sobran":          [{ "codigo_elemento", "id_elemento", "id_articulo", "nombre_articulo", "id_familia", "nombre_familia", "id_linea_ppto" }],
      "no_relacionados": [{ "codigo_elemento", "id_elemento", "id_articulo", "nombre_articulo", "id_familia", "nombre_familia" }],
      "faltan":          [{ "id_articulo", "nombre_articulo", "cantidad_faltante", "id_linea_ppto", "id_familia", "nombre_familia" }]
    }
    ```
  - Tener en cuenta elementos con estado != DISP/TERC: incluirlos pero marcar `estado_invalido: true`

- [ ] **A2** `SalidaAlmacen.php` — método `confirmar_pool(int $id_salida, array $pool): bool`
  - Cada ítem del pool: `{codigo_elemento, modo: correcto|backup|sustituye, id_linea_ppto?: int}`
  - Dentro de **transacción** (BEGIN / COMMIT / ROLLBACK):
    - `correcto` → INSERT `linea_salida_almacen` con `id_linea_ppto` asignado, `es_backup=0`
    - `sustituye` → INSERT con `id_linea_ppto` del faltante que cubre, `es_backup=0`
    - `backup` → INSERT con `id_linea_ppto=NULL`, `es_backup=1`
    - `cambiar_estado_elemento(id_elemento, 'PREP')` para cada elemento
    - Si la `linea_presupuesto` referenciada tiene `id_ubicacion` → llamar `registrar_movimiento` automático
  - Retorna `true` en éxito, `false` (o lanza excepción) en fallo

## Phase B — Controller: nuevos cases en salida_almacen.php

- [ ] **B1** `case 'comparar'`
  - POST esperado: `id_salida_almacen` (int), `codigos[]` (array de strings)
  - Validar: `id_salida_almacen` requerido, `codigos` no vacío
  - Llama `$model->comparar_pool($id_salida, $codigos)`
  - Devuelve: `{ success: true, correctos[], sobran[], no_relacionados[], faltan[] }`

- [ ] **B2** `case 'confirmar'`
  - POST esperado: `id_salida_almacen` (int), `pool` (string JSON)
  - Validar: ambos requeridos; `pool` debe ser JSON válido
  - Decodificar `$pool = json_decode($_POST['pool'], true)`
  - Llamar `$model->confirmar_pool($id_salida, $pool)`
  - Devuelve: `{ success: true|false, message: string }`

## Phase C — HTML: index.php

- [ ] **C1** Renombrar `id="phase4"` → `id="phase5"` (pantalla "Completado") y actualizar todos los atributos/referencias internas del HTML
- [ ] **C2** Actualizar `id="p4-resumen"` → `id="p5-resumen"` y `id="btn-nueva-salida"` mantiene su id
- [ ] **C3** Añadir nuevo `#phase4` — pantalla de comparación — entre `#phase3` y `#phase5`:
  - Sección `#cmp-correctos` (colapsable)
  - Sección `#cmp-faltan` (siempre visible)
  - Sección `#cmp-sobran` (colapsable)
  - Sección `#cmp-no-relacionados` (colapsable, sin selects — los candidatos aparecen en paneles de faltantes)
  - Banner `#cmp-alerta-faltan` (.fb-banner .fb-err, visible cuando hay faltantes)
  - Botón `#btn-volver-escaneo` + botón `#btn-confirmar` (disabled por defecto)
- [ ] **C4** Añadir zona pool en `#phase3`:
  - Contador `#pool-count`
  - Lista `#pool-lista`
  - Botón `#btn-comparar` full-width (siempre habilitado mientras haya elementos en el pool)

## Phase D — JavaScript: picking.js

- [ ] **D1** `state`: añadir campos `pool: []`, `comparacion: null`, `sustituciones: {}`
- [ ] **D2** `procesarEscaneo(codigo)`: eliminar llamada a `post('escanear', ...)` y reemplazar por `agregarAlPool(codigo)`
- [ ] **D3** Nueva función `agregarAlPool(codigo)`:
  - Comprobar duplicado en `state.pool` → si existe, mostrar toast "Ya está en el pool" y salir
  - Push `{codigo}` a `state.pool`
  - Llamar `renderPool()`
- [ ] **D4** Nueva función `renderPool()`:
  - Renderiza `#pool-lista` con una fila por elemento: `codigo` + botón ✕ que llama `quitarDelPool(codigo)`
  - Actualiza `#pool-count`
  - Habilita/deshabilita `#btn-comparar` según `state.pool.length > 0`
- [ ] **D5** Nueva función `quitarDelPool(codigo)`:
  - Elimina el código de `state.pool`
  - Llama `renderPool()`
- [ ] **D6** Nueva función `compararPool()`:
  - Construye array de códigos desde `state.pool`
  - POST a `salida_almacen.php?op=comparar` con `{id_salida_almacen, codigos[]}`
  - En éxito: guarda en `state.comparacion`, llama `renderComparacion(data)`, `mostrarFase(4)`
  - En error: feedback en fase 3
- [ ] **D7** Nueva función `renderComparacion(data)`:
  - Renderiza las 4 secciones usando `state.comparacion`
  - Cada fila de `faltan` incluye botón `[Asignar sustituto ▼]` que abre el panel `.sust-panel`
  - Las secciones `sobran` y `no_relacionados` renderizan sus filas (sin selects)
  - Al renderizar, llama `actualizarEstadoConfirmar()`
- [ ] **D8a** Nueva función `abrirPanelSustitucion(id_linea_ppto_faltante, id_familia_faltante)`:
  - Abre el `.sust-panel` de la fila correspondiente al faltante
  - Carga la lista de candidatos = Sobran ∪ No_relacionados que no estén en `state.sustituciones`
  - Aplica filtro inicial: candidatos con `id_familia == id_familia_faltante`
  - Renderiza los chips toggle `.sust-chip-familia` ("Misma familia" activo por defecto)
- [ ] **D8b** Handler toggle chips `[Misma familia] / [Todas]`:
  - Filtra/desfiltra la lista de candidatos por `id_familia` (puramente client-side)
  - No hace ninguna llamada al servidor
- [ ] **D8c** Nueva función `asignarSustitucion(codigo_candidato, id_linea_ppto_faltante)`:
  - Actualiza `state.sustituciones[codigo_candidato] = id_linea_ppto_faltante`
  - Marca el candidato como bloqueado (no aparece en otros paneles de faltante)
  - La fila del faltante muestra `« cubre: codigo_candidato »` + botón `✕ Quitar`
  - El candidato muestra badge `« cubre a NombreArt »` en su sección
  - Cierra el panel, llama `actualizarEstadoConfirmar()`
- [ ] **D8d** Nueva función `quitarSustitucion(codigo_candidato)`:
  - Elimina `state.sustituciones[codigo_candidato]`
  - Reactiva el candidato (vuelve a aparecer en paneles de faltante)
  - La fila del faltante vuelve a mostrar el botón `[Asignar sustituto ▼]`
  - Llama `actualizarEstadoConfirmar()`
- [ ] **D9** Nueva función `actualizarEstadoConfirmar()`:
  - Cuenta faltantes sin cubrir (= faltan que no tienen sustituto asignado en `state.sustituciones`)
  - Si 0 → habilitar `#btn-confirmar`, ocultar `#cmp-alerta-faltan`
  - Si > 0 → deshabilitar `#btn-confirmar`, mostrar `#cmp-alerta-faltan` con recuento
- [ ] **D10** Nueva función `confirmarPool()`:
  - Construye array `pool[]` combinando `state.comparacion` + `state.sustituciones`:
    - correctos → `{codigo, modo: 'correcto', id_linea_ppto}`
    - sobran con `state.sustituciones[codigo]` definido → `{codigo, modo: 'sustituye', id_linea_ppto}` (id_linea_ppto del faltante cubierto)
    - sobran sin sustitución → `{codigo, modo: 'backup', id_linea_ppto}` (id_linea_ppto propio del artículo)
    - no_relacionados con `state.sustituciones[codigo]` definido → `{codigo, modo: 'sustituye', id_linea_ppto}` (id_linea_ppto del faltante cubierto)
    - no_relacionados sin sustitución → `{codigo, modo: 'backup', id_linea_ppto: null}`
  - POST a `salida_almacen.php?op=confirmar` con `{id_salida_almacen, pool: JSON.stringify(pool)}`
  - En éxito: `mostrarFase(5)`
  - En error: Swal de error
- [ ] **D11** `resetState()`: incluir reset de `pool: []`, `comparacion: null`, `sustituciones: {}`
- [ ] **D12** Actualizar todas las llamadas `mostrarFase(4)` existentes → `mostrarFase(5)` (la pantalla de completado se mueve a fase 5)
- [ ] **D13** Vincular eventos en `$(document).ready`:
  - `#btn-comparar` → `compararPool()`
  - `#btn-volver-escaneo` → `mostrarFase(3)`
  - `#btn-confirmar` → `confirmarPool()`

## Limpieza posterior (no bloqueante)

- [ ] `escanear_elemento` en `SalidaAlmacen.php` queda sin uso desde Picking — añadir `@deprecated` en el docblock cuando se confirme que nada más lo consume
- [ ] Retirar `case 'debug_buscar'` del controller (era temporal)
- [ ] Retirar `case 'escanear'` del controller si se confirma que no lo usa ningún otro módulo

## Orden de implementación recomendado

```
A1 → A2 → B1 → B2     (backend, en serie)
C1 → C2 → C3 → C4     (HTML, en serie)
D1 → D3 → D4 → D5     (state + pool básico)
D6 → D7 → D8a → D8b → D8c → D8d → D9     (comparación + sustitución)
D10 → D11 → D12 → D13 (confirmar + wiring)
```

A y C son independientes entre sí y pueden hacerse en paralelo.
D depende de C (para los IDs HTML) y de B (para las ops del controller).
