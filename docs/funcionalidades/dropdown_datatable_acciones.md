# Dropdown de Acciones en DataTable

> **Referencia**: `view/Presupuesto/index.php` · `view/Presupuesto/mntpresupuesto.js`  
> **Fecha**: 21/02/2026  
> **Rama**: `modelo_presupuesto`

---

## ¿Por qué usar un dropdown?

Cuando una DataTable tiene `scrollX: true` + `fixedColumns`, las columnas de acción quedan fuera del área visible y el usuario tiene que hacer scroll horizontal para acceder a ellas. Consolidar todas las acciones en un único botón dropdown elimina ese problema y reduce el número de columnas.

**Antes (presupuestos):** 4 columnas de acción (Act./Desac., Editar, Líneas, Imprimir) → scroll horizontal obligatorio  
**Después:** 1 columna "Acciones" con dropdown Bootstrap 5 → todo en un clic

---

## 1. HTML — `index.php`

### `<thead>`

Sustituir las `<th>` de acciones por **una sola**:

```html
<!-- ANTES (una por acción) -->
<th rowspan="2">Act./Desac.</th>
<th rowspan="2">Editar</th>
<th rowspan="2">Líneas</th>
<th rowspan="2">Imprimir</th>

<!-- DESPUÉS (una para todo) -->
<th rowspan="2">Acciones</th>
```

### `<tfoot>`

Lo mismo: reducir los `<th></th>` vacíos a uno solo:

```html
<!-- ANTES -->
<th></th><th></th><th></th><th></th>

<!-- DESPUÉS -->
<th></th>
```

---

## 2. JavaScript — `mntXXX.js`

### 2.1 Array `columns[]`

Eliminar las entradas que apuntaban a las columnas de acción individuales y añadir **una entrada** al final:

```javascript
// Columna de acciones (ÚNICA)
{
    name: 'acciones',
    data: null,
    defaultContent: '',
    className: "text-center align-middle"
}
```

### 2.2 `columnDefs` — render del dropdown

Añadir (o sustituir) la definición para la columna `acciones`:

```javascript
{
    targets: 'acciones:name',
    orderable: false,
    searchable: false,
    render: function (data, type, row) {

        // ─── construir items condicionales ───────────────────────────
        let itemGestionarLineas = '';
        if (row.id_estado_codigo !== 'FACTURADO') {
            itemGestionarLineas = `
                <li>
                    <a class="dropdown-item gestionarLineas"
                       href="#"
                       data-id="${row.id_presupuesto}"
                       data-num="${row.numero_presupuesto}">
                        <i class="fa-solid fa-list-check me-2"></i>Gestionar Líneas
                    </a>
                </li>`;
        } else {
            itemGestionarLineas = `
                <li>
                    <a class="dropdown-item disabled" href="#" tabindex="-1"
                       aria-disabled="true"
                       title="No se pueden editar líneas de un presupuesto facturado">
                        <i class="fa-solid fa-list-check me-2"></i>Gestionar Líneas
                    </a>
                </li>`;
        }

        // ─── label dinámico Activar / Desactivar ────────────────────
        let itemActivarDesactivar = '';
        if (row.activo_presupuesto == 1) {
            itemActivarDesactivar = `
                <li>
                    <a class="dropdown-item text-danger desacPresupuesto"
                       href="#"
                       data-id="${row.id_presupuesto}"
                       data-num="${row.numero_presupuesto}">
                        <i class="fa-solid fa-ban me-2"></i>Desactivar
                    </a>
                </li>`;
        } else {
            itemActivarDesactivar = `
                <li>
                    <a class="dropdown-item text-success activarPresupuesto"
                       href="#"
                       data-id="${row.id_presupuesto}"
                       data-num="${row.numero_presupuesto}">
                        <i class="fa-solid fa-circle-check me-2"></i>Activar
                    </a>
                </li>`;
        }

        // ─── HTML completo del dropdown ──────────────────────────────
        return `
            <div class="dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">

                    <!-- Editar -->
                    <li>
                        <a class="dropdown-item editarPresupuesto"
                           href="#"
                           data-id="${row.id_presupuesto}">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Editar
                        </a>
                    </li>

                    <!-- Gestionar Líneas (condicional) -->
                    ${itemGestionarLineas}

                    <li><hr class="dropdown-divider"></li>

                    <!-- Historial de versiones -->
                    <li>
                        <a class="dropdown-item verVersiones"
                           href="#"
                           data-id="${row.id_presupuesto}"
                           data-num="${row.numero_presupuesto}">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i>Historial versiones
                        </a>
                    </li>

                    <!-- Copiar presupuesto -->
                    <li>
                        <a class="dropdown-item copiarPresupuesto"
                           href="#"
                           data-id="${row.id_presupuesto}"
                           data-num="${row.numero_presupuesto}">
                            <i class="fa-solid fa-copy me-2"></i>Copiar presupuesto
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <!-- Cambiar estado -->
                    <li>
                        <a class="dropdown-item cambiarEstadoPpto"
                           href="#"
                           data-id="${row.id_presupuesto}"
                           data-estado-actual="${row.id_estado_ppto}">
                            <i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Cambiar estado
                        </a>
                    </li>

                    <!-- Imprimir (modal de opciones) -->
                    <li>
                        <a class="dropdown-item imprimirPresupuesto"
                           href="#"
                           data-id="${row.id_presupuesto}"
                           data-num="${row.numero_presupuesto}"
                           data-cliente="${row.nombre_completo_cliente}"
                           data-estado="${row.id_estado_ppto}">
                            <i class="fa-solid fa-print me-2"></i>Imprimir
                        </a>
                    </li>

                    <!-- PDF rápido -->
                    <li>
                        <a class="dropdown-item pdfRapido"
                           href="#"
                           data-id="${row.id_presupuesto}"
                           data-num="${row.numero_presupuesto}">
                            <i class="fa-solid fa-file-pdf me-2 text-danger"></i>PDF rápido
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <!-- Activar / Desactivar (dinámico) -->
                    ${itemActivarDesactivar}

                </ul>
            </div>`;
    }
}
```

### 2.3 Fix z-index para `fixedColumns`

Añadir **antes del bloque `$(document).ready`** para que los dropdowns no queden ocultos bajo las columnas fijas:

```javascript
// Fix: z-index dropdown dentro de fixedColumns
$('<style>')
    .text(`
        .dtfc-fixed-left .dropdown-menu,
        .dtfc-fixed-right .dropdown-menu,
        table.dataTable td .dropdown-menu {
            z-index: 9999 !important;
        }
    `)
    .appendTo('head');
```

### 2.4 Handlers de eventos (delegación)

Todos los handlers se enganchan al documento con delegación. Ejemplo de estructura:

```javascript
// ── Editar ────────────────────────────────────────────────────────────
$(document).on('click', '.editarPresupuesto', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    // ... lógica editar
});

// ── Copiar presupuesto ────────────────────────────────────────────────
$(document).on('click', '.copiarPresupuesto', function (e) {
    e.preventDefault();
    const id  = $(this).data('id');
    const num = $(this).data('num');

    Swal.fire({
        title: '¿Copiar presupuesto?',
        text: `Se creará una copia del presupuesto ${num}`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, copiar',
        cancelButtonText:  'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;

        $.post('../../controller/presupuesto.php?op=copiar',
               { id_presupuesto: id })
            .done(function (resp) {
                if (resp.success) {
                    Swal.fire('Copiado', `Nuevo presupuesto: ${resp.numero_nuevo}`, 'success');
                    tabla.ajax.reload(null, false);
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            })
            .fail(() => Swal.fire('Error', 'Error de comunicación', 'error'));
    });
});

// ── Cambiar estado ────────────────────────────────────────────────────
$(document).on('click', '.cambiarEstadoPpto', function (e) {
    e.preventDefault();
    const id          = $(this).data('id');
    const estadoActual = $(this).data('estado-actual');

    $.get('../../controller/presupuesto.php?op=get_estados')
        .done(function (estados) {
            let opciones = estados.map(est =>
                `<option value="${est.id_estado_ppto}"
                         ${est.id_estado_ppto == estadoActual ? 'selected' : ''}>
                     ${est.nombre_estado_ppto}
                 </option>`
            ).join('');

            Swal.fire({
                title: 'Cambiar estado',
                html: `<select id="swal-estado" class="form-select">${opciones}</select>`,
                showCancelButton: true,
                confirmButtonText: 'Cambiar',
                cancelButtonText:  'Cancelar',
                preConfirm: () => document.getElementById('swal-estado').value
            }).then(result => {
                if (!result.isConfirmed) return;

                $.post('../../controller/presupuesto.php?op=cambiar_estado', {
                    id_presupuesto: id,
                    id_estado_ppto: result.value
                })
                .done(resp => {
                    if (resp.success) {
                        tabla.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', resp.message, 'error');
                    }
                });
            });
        });
});

// ── Desactivar ────────────────────────────────────────────────────────
$(document).on('click', '.desacPresupuesto', function (e) {
    e.preventDefault();
    const id  = $(this).data('id');
    const num = $(this).data('num');

    Swal.fire({
        title: '¿Desactivar?',
        text: `Presupuesto ${num}`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, desactivar',
        cancelButtonText:  'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;

        $.post('../../controller/presupuesto.php?op=desactivar',
               { id_presupuesto: id })
            .done(resp => {
                if (resp.success) {
                    Swal.fire('Desactivado', resp.message, 'success');
                    tabla.ajax.reload(null, false);
                } else {
                    Swal.fire('Error', resp.message, 'error');
                }
            });
    });
});

// ── Activar ───────────────────────────────────────────────────────────
$(document).on('click', '.activarPresupuesto', function (e) {
    e.preventDefault();
    const id = $(this).data('id');

    $.post('../../controller/presupuesto.php?op=activar',
           { id_presupuesto: id })
        .done(resp => {
            if (resp.success) {
                Swal.fire('Activado', resp.message, 'success');
                tabla.ajax.reload(null, false);
            } else {
                Swal.fire('Error', resp.message, 'error');
            }
        });
});
```

---

## 3. Backend — Controller y Model

Para cada acción nueva hay que añadir:

1. Un **`case`** en el `switch ($_GET["op"])` del controller
2. Un **método** en el Model

### Ejemplo: `copiar`

**`controller/xxx.php`**
```php
case "copiar":
    $id = $_POST["id_presupuesto"] ?? null;
    if (!$id) {
        echo json_encode(['success' => false, 'message' => 'ID no proporcionado'], JSON_UNESCAPED_UNICODE);
        break;
    }
    $resultado = $presupuesto->copiar_presupuesto($id);
    if ($resultado) {
        echo json_encode([
            'success'     => true,
            'message'     => 'Presupuesto copiado correctamente',
            'id_nuevo'    => $resultado['id_nuevo'],
            'numero_nuevo' => $resultado['numero_nuevo']
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo copiar el presupuesto'], JSON_UNESCAPED_UNICODE);
    }
    break;
```

**`models/Xxx.php`** — esquema del método:
```php
public function copiar_presupuesto($id_presupuesto)
{
    try {
        // 1. Leer presupuesto original
        // 2. Generar nuevo número (p.ej. 'COPIA-XXX-YYYYMMDD')
        // 3. beginTransaction()
        // 4. INSERT en tabla presupuesto SIN columnas calculadas (subtotales, etc.)
        // 5. Recuperar ID nuevo con lastInsertId()
        // 6. Copiar líneas si las hay
        // 7. commit() → return ['id_nuevo' => ..., 'numero_nuevo' => ...]
    } catch (PDOException $e) {
        $this->conexion->rollBack();
        // logging
        return false;
    }
}
```

> ⚠️ **Importante (presupuestos):** la tabla `presupuesto` tiene el trigger
> `trg_presupuesto_after_insert` que crea automáticamente la fila en
> `presupuesto_version` (versión 1). No insertar manualmente esa fila o se
> producirá una colisión de clave duplicada.

---

## 4. Checklist de implementación

- [ ] Reducir `<th>` en `<thead>` a una sola celda `Acciones`
- [ ] Reducir `<th>` vacíos en `<tfoot>` al mismo número
- [ ] Actualizar `columns[]` en JS (eliminar cols de acción, añadir la unificada)
- [ ] Añadir `columnDefs` con el render del dropdown
- [ ] Añadir el fix z-index antes del `$(document).ready`
- [ ] Migrar / añadir todos los handlers con delegación `$(document).on(...)`
- [ ] Añadir `case` en el controller por cada nueva acción
- [ ] Añadir método en el Model por cada nueva acción
- [ ] Verificar que el número total de columnas `columns[]` coincide con las `<th>` del `<thead>`

---

## 5. Consideraciones

### Items deshabilitados
Para mostrar una acción pero impedir su uso, usar la clase `disabled` de Bootstrap:
```html
<a class="dropdown-item disabled" href="#" tabindex="-1" aria-disabled="true"
   title="Texto explicativo del motivo">
    <i class="fa-solid fa-lock me-2"></i>Acción bloqueada
</a>
```
En el handler añadir siempre el guard `:not(.disabled)`:
```javascript
$(document).on('click', '.miAccion:not(.disabled)', function(e) { ... });
```

### Overflow en tablas con `fixedColumns`
Las columnas fijas tienen `overflow: hidden` por defecto. El fix de z-index del
apartado 2.3 resuelve el problema para dropdowns que se abren dentro de esas columnas.
Si la columna `Acciones` **no está fijada**, el fix no es necesario.

### Cuántas acciones tiene sentido meter

| Nº de acciones | Recomendación |
|:--------------:|---------------|
| 2–3 | Mantener botones individuales (más visible) |
| 4–6 | Dropdown recomendado |
| 7+ | Dropdown con separadores y agrupación por categoría |

---

## 6. Ejemplo real: Presupuestos

| Aspecto | Antes | Después |
|---------|-------|---------|
| Columnas de acción | 4 (Act./Desac., Editar, Líneas, Imprimir) | 1 (Acciones) |
| Total columnas tabla | 17 | 14 |
| Acciones disponibles | 4 | 10 (editar, gestionar líneas, historial, copiar, cambiar estado, imprimir, PDF rápido, activar/desactivar) |

**Archivos modificados:**

| Archivo | Cambio |
|---------|--------|
| `view/Presupuesto/index.php` | thead/tfoot: 4 `<th>` → 1 `<th>` |
| `view/Presupuesto/mntpresupuesto.js` | columns, columnDefs, z-index fix, handlers nuevos |
| `models/Presupuesto.php` | `copiar_presupuesto()`, `get_estados_presupuesto()`, `cambiar_estado_presupuesto()` |
| `controller/presupuesto.php` | cases `copiar`, `get_estados`, `cambiar_estado` |
