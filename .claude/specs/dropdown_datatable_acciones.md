# Spec — Dropdown de Acciones en DataTable

> **Referencia:** `view/Presupuesto/index.php` · `view/Presupuesto/mntpresupuesto.js`  
> **Descripción:** Consolidar múltiples columnas de acción en un único botón dropdown Bootstrap 5, resolviendo el problema de scroll horizontal en tablas con `fixedColumns`.

---

## 1. Cuándo aplicar este patrón

| Nº de acciones | Recomendación |
|:--------------:|---------------|
| 2–3 | Mantener botones individuales |
| 4–6 | **Dropdown recomendado** |
| 7+ | Dropdown con separadores y agrupación por categoría |

**Problema que resuelve:** Con `scrollX: true` + `fixedColumns`, las columnas de acción quedan fuera del área visible y el usuario necesita scroll horizontal para acceder a ellas.

---

## 2. HTML — `index.php`

### `<thead>` y `<tfoot>`

Sustituir todas las `<th>` de acciones por **una sola**:

```html
<!-- ANTES -->
<th rowspan="2">Act./Desac.</th>
<th rowspan="2">Editar</th>
<th rowspan="2">Líneas</th>
<th rowspan="2">Imprimir</th>

<!-- DESPUÉS -->
<th rowspan="2">Acciones</th>
```

Hacer lo mismo en `<tfoot>`: reducir los `<th></th>` vacíos al número correcto.

> ⚠️ El total de `<th>` en `<thead>` debe coincidir exactamente con el array `columns[]` en JS.

---

## 3. JavaScript — `mntXXX.js`

### 3.1 Array `columns[]`

Eliminar entradas de acción individuales y añadir **una sola** al final:

```javascript
{
    name: 'acciones',
    data: null,
    defaultContent: '',
    className: "text-center align-middle"
}
```

### 3.2 `columnDefs` — render del dropdown

```javascript
{
    targets: 'acciones:name',
    orderable: false,
    searchable: false,
    render: function (data, type, row) {
        // data → null (porque data: null en columns[])
        // type → 'display' | 'filter' | 'sort' | 'type'
        // row  → objeto completo del registro (todos los campos del JSON)

        // ─── Items condicionales ─────────────────────────────────────

        // Ejemplo: item habilitado/deshabilitado según estado
        let itemGestionarLineas = '';
        if (row.id_estado_codigo !== 'FACTURADO') {
            itemGestionarLineas = `
                <li>
                    <a class="dropdown-item gestionarLineas" href="#"
                       data-id="${row.id_presupuesto}"
                       data-num="${row.numero_presupuesto}">
                        <i class="fa-solid fa-list-check me-2"></i>Gestionar Líneas
                    </a>
                </li>`;
        } else {
            itemGestionarLineas = `
                <li>
                    <a class="dropdown-item disabled" href="#"
                       tabindex="-1" aria-disabled="true"
                       title="No se pueden editar líneas de un presupuesto facturado">
                        <i class="fa-solid fa-list-check me-2"></i>Gestionar Líneas
                    </a>
                </li>`;
        }

        // Ejemplo: label dinámico Activar / Desactivar
        let itemActivarDesactivar = '';
        if (row.activo_presupuesto == 1) {
            itemActivarDesactivar = `
                <li>
                    <a class="dropdown-item text-danger desacPresupuesto" href="#"
                       data-id="${row.id_presupuesto}"
                       data-num="${row.numero_presupuesto}">
                        <i class="fa-solid fa-ban me-2"></i>Desactivar
                    </a>
                </li>`;
        } else {
            itemActivarDesactivar = `
                <li>
                    <a class="dropdown-item text-success activarPresupuesto" href="#"
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

                    <li>
                        <a class="dropdown-item editarPresupuesto" href="#"
                           data-id="${row.id_presupuesto}">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Editar
                        </a>
                    </li>

                    ${itemGestionarLineas}

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <a class="dropdown-item verVersiones" href="#"
                           data-id="${row.id_presupuesto}"
                           data-num="${row.numero_presupuesto}">
                            <i class="fa-solid fa-clock-rotate-left me-2"></i>Historial versiones
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item copiarPresupuesto" href="#"
                           data-id="${row.id_presupuesto}"
                           data-num="${row.numero_presupuesto}">
                            <i class="fa-solid fa-copy me-2"></i>Copiar presupuesto
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    <li>
                        <a class="dropdown-item cambiarEstadoPpto" href="#"
                           data-id="${row.id_presupuesto}"
                           data-estado-actual="${row.id_estado_ppto}">
                            <i class="fa-solid fa-arrow-right-arrow-left me-2"></i>Cambiar estado
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item imprimirPresupuesto" href="#"
                           data-id="${row.id_presupuesto}"
                           data-num="${row.numero_presupuesto}"
                           data-cliente="${row.nombre_completo_cliente}"
                           data-estado="${row.id_estado_ppto}">
                            <i class="fa-solid fa-print me-2"></i>Imprimir
                        </a>
                    </li>

                    <li>
                        <a class="dropdown-item pdfRapido" href="#"
                           data-id="${row.id_presupuesto}"
                           data-num="${row.numero_presupuesto}">
                            <i class="fa-solid fa-file-pdf me-2 text-danger"></i>PDF rápido
                        </a>
                    </li>

                    <li><hr class="dropdown-divider"></li>

                    ${itemActivarDesactivar}

                </ul>
            </div>`;
    }
}
```

### 3.3 Fix z-index para `fixedColumns`

Añadir **antes** del bloque `$(document).ready` cuando la tabla use `fixedColumns`:

```javascript
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

> Si la columna Acciones **no está fijada**, este fix no es necesario.

### 3.4 Handlers de eventos (delegación)

**Siempre** usar delegación `$(document).on(...)`. El guard `:not(.disabled)` es obligatorio para items que pueden quedar deshabilitados:

```javascript
// Editar
$(document).on('click', '.editarPresupuesto', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    // lógica...
});

// Copiar (con SweetAlert de confirmación)
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
        cancelButtonText: 'Cancelar'
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('../../controller/presupuesto.php?op=copiar', { id_presupuesto: id })
            .done(resp => {
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

// Cambiar estado (select dinámico en SweetAlert)
$(document).on('click', '.cambiarEstadoPpto', function (e) {
    e.preventDefault();
    const id           = $(this).data('id');
    const estadoActual = $(this).data('estado-actual');

    $.get('../../controller/presupuesto.php?op=get_estados')
        .done(function (estados) {
            const opciones = estados.map(est =>
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
                cancelButtonText: 'Cancelar',
                preConfirm: () => document.getElementById('swal-estado').value
            }).then(result => {
                if (!result.isConfirmed) return;
                $.post('../../controller/presupuesto.php?op=cambiar_estado', {
                    id_presupuesto: id,
                    id_estado_ppto: result.value
                }).done(resp => {
                    if (resp.success) tabla.ajax.reload(null, false);
                    else Swal.fire('Error', resp.message, 'error');
                });
            });
        });
});

// Desactivar
$(document).on('click', '.desacPresupuesto', function (e) {
    e.preventDefault();
    const id  = $(this).data('id');
    const num = $(this).data('num');

    Swal.fire({
        title: '¿Desactivar?',
        html: `¿Desea desactivar el presupuesto ${num}?<br><br>
               <small class="text-warning">
                   <i class="fa-solid fa-triangle-exclamation me-1"></i>
                   El registro quedará marcado como inactivo
               </small>`,
        icon: 'warning', showCancelButton: true,
        confirmButtonColor: '#d33',
        confirmButtonText: 'Sí, desactivar', cancelButtonText: 'Cancelar',
        reverseButtons: true
    }).then(result => {
        if (!result.isConfirmed) return;
        $.post('../../controller/presupuesto.php?op=desactivar', { id_presupuesto: id })
            .done(resp => {
                if (resp.success) {
                    Swal.fire('Desactivado', resp.message, 'success');
                    tabla.ajax.reload(null, false);
                } else Swal.fire('Error', resp.message, 'error');
            });
    });
});

// Activar
$(document).on('click', '.activarPresupuesto', function (e) {
    e.preventDefault();
    const id = $(this).data('id');
    $.post('../../controller/presupuesto.php?op=activar', { id_presupuesto: id })
        .done(resp => {
            if (resp.success) {
                Swal.fire('Activado', resp.message, 'success');
                tabla.ajax.reload(null, false);
            } else Swal.fire('Error', resp.message, 'error');
        });
});
```

### 3.5 Patrones de navegación en items del dropdown

| Patrón | Cuándo usar | Ejemplo |
|--------|-------------|--------|
| `href` directo con query string | Navegación a otro módulo sin lógica JS previa | `href="../../view/MntFacturas/index.php?id=${row.id}"` |
| `href="#"` + `data-*` + listener | Cuando se necesita lógica (AJAX, modal, confirmación) | Editar, Activar, Desactivar, Copiar |

> Los items de **navegación directa** no necesitan event listener — el `href` con query string lleva al módulo destino.
> Los items de **acción JS** siempre usan `href="#"` + `e.preventDefault()` + delegación `$(document).on(...)`.

---

## 4. Backend — Controller y Model

Por cada acción nueva añadir un `case` en el controller y un método en el model.

### Patrón controller (`controller/xxx.php`)

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
            'success'      => true,
            'message'      => 'Presupuesto copiado correctamente',
            'id_nuevo'     => $resultado['id_nuevo'],
            'numero_nuevo' => $resultado['numero_nuevo']
        ], JSON_UNESCAPED_UNICODE);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo copiar'], JSON_UNESCAPED_UNICODE);
    }
    break;
```

### Patrón model (`models/Xxx.php`)

```php
public function copiar_presupuesto($id_presupuesto)
{
    try {
        // 1. Leer presupuesto original
        // 2. Generar nuevo número
        // 3. beginTransaction()
        // 4. INSERT presupuesto (sin columnas calculadas: subtotales, etc.)
        // 5. lastInsertId() → $id_nuevo
        // 6. Copiar líneas si las hay
        // 7. commit() → return ['id_nuevo' => ..., 'numero_nuevo' => ...]
    } catch (PDOException $e) {
        $this->conexion->rollBack();
        $this->registro->registrarActividad('admin', 'Xxx', 'copiar_presupuesto',
                                             "Error: " . $e->getMessage(), 'error');
        return false;
    }
}
```

> ⚠️ **Triggers:** La tabla `presupuesto` tiene `trg_presupuesto_after_insert` que crea automáticamente la fila en `presupuesto_version`. No insertar esa fila manualmente o se produce colisión de clave duplicada.

---

## 5. Items deshabilitados

Para mostrar una acción bloqueada sin eliminarla del menú:

```html
<a class="dropdown-item disabled" href="#" tabindex="-1" aria-disabled="true"
   title="Texto explicativo del motivo">
    <i class="fa-solid fa-lock me-2"></i>Acción bloqueada
</a>
```

Handler con guard para evitar ejecución si está deshabilitada:

```javascript
$(document).on('click', '.miAccion:not(.disabled)', function(e) { ... });
```

---

## 6. Checklist de implementación

- [ ] Reducir `<th>` de acciones en `<thead>` a una sola celda `Acciones`
- [ ] Reducir `<th>` vacíos en `<tfoot>` al mismo número
- [ ] Actualizar `columns[]` en JS (eliminar cols de acción individuales, añadir `acciones`)
- [ ] Total `columns[]` == total `<th>` en `<thead>`
- [ ] Añadir `columnDefs` con el render del dropdown
- [ ] Añadir fix z-index antes de `$(document).ready` (si usa `fixedColumns`)
- [ ] Migrar todos los handlers con delegación `$(document).on(...)`
- [ ] Añadir guard `:not(.disabled)` en handlers de items condicionales
- [ ] Añadir `case` en el controller por cada acción nueva
- [ ] Añadir método en el Model por cada acción nueva

---

## 7. Prompt de activación

Para aplicar este patrón a una tabla nueva:

```
Lee `.claude/specs/dropdown_datatable_acciones.md` e implementa el dropdown de acciones
en `view/[Modulo]/index.php` y `view/[Modulo]/mnt[Modulo].js`.

Acciones a incluir:
- [lista de acciones]

Condiciones especiales:
- [items que deben deshabilitarse según estado / campo]

Datos del row disponibles: [lista de campos que devuelve el controller `listar`]
```

Para añadir una acción nueva a un dropdown existente:

```
Lee `.claude/specs/dropdown_datatable_acciones.md` y añade la acción `[nombre]`
al dropdown en `view/[Modulo]/mnt[Modulo].js`, con su `case` en
`controller/[modulo].php` y método en `models/[Entidad].php`.
```
