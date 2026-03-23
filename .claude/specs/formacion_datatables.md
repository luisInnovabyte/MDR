# Spec: DataTable CRUD Completo (Módulo Mnt*)

> Referencia prescriptiva para implementar un módulo de mantenimiento completo (Mnt*)
> con DataTables en MDR ERP. Cubre HTML, JS, Controller PHP y Model PHP.
>
> Fuente: `docs/formacion_datatables.md`
> Módulo de referencia real: `view/MntMarca/`

---

## 1. HTML — Estructura de la Tabla

### Clases obligatorias

```html
<table id="tbl_[entidad]" class="table display responsive nowrap">
```

### Estructura completa

```html
<div class="table-wrapper">
    <table id="tbl_[entidad]" class="table display responsive nowrap">
        <thead>
            <tr>
                <th></th>                          <!-- col 0: expansión child-row -->
                <th>ID</th>                        <!-- col 1: ID (se ocultará) -->
                <th>[Campo 1]</th>
                <th>[Campo N]</th>
                <th>Estado</th>
                <th>Act./Desac.</th>
                <th>Edit.</th>
            </tr>
        </thead>
        <tbody></tbody>
        <tfoot>
            <tr>
                <th></th>                          <!-- sin filtro -->
                <th class="d-none">               <!-- ID oculto -->
                    <input type="text" placeholder="Buscar ID" class="form-control form-control-sm" />
                </th>
                <th>
                    <input type="text" placeholder="Buscar [campo1]" class="form-control form-control-sm" />
                </th>
                <th>
                    <select class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </th>
                <th class="d-none">               <!-- sin filtro -->
                    <input type="text" class="form-control form-control-sm" />
                </th>
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
```

### Reglas HTML

- `id` de la tabla debe ser único en la página
- Columna 0 = vacía (expansión child-row)
- Columna 1 = ID (se oculta con `visible: false` en JS)
- Última columna de tfoot para Act./Desac. tiene `class="d-none"` (no tiene filtro propio)
- Siempre incluir `tfoot` aunque no haya filtros

---

## 2. JavaScript

### 2.1 Objeto de configuración DataTable

```javascript
var datatable_[entidad]Config = {
    processing: true,

    layout: {
        bottomEnd: {
            paging: {
                firstLast: true,
                numbers: false,
                previousNext: true
            }
        }
    },

    language: {
        paginate: {
            first: '<i class="bi bi-chevron-double-left"></i>',
            last: '<i class="bi bi-chevron-double-right"></i>',
            previous: '<i class="bi bi-chevron-compact-left"></i>',
            next: '<i class="bi bi-chevron-compact-right"></i>'
        }
    },

    columns: [
        // col 0: control de expansión
        { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' },
        // col 1: ID oculto
        { name: 'id_[entidad]', data: 'id_[entidad]', visible: false, className: 'text-center' },
        // cols de datos
        { name: '[campo1]', data: '[campo1]', className: 'text-center align-middle' },
        // col estado
        { name: 'activo_[entidad]', data: 'activo_[entidad]', className: 'text-center align-middle' },
        // col activar/desactivar
        { name: 'activar', data: null, className: 'text-center align-middle' },
        // col editar
        { name: 'editar', data: null, defaultContent: '', className: 'text-center align-middle' }
    ],

    columnDefs: [
        // control
        { targets: 'control:name', width: '5%', searchable: false, orderable: false },
        // ID
        { targets: 'id_[entidad]:name', width: '5%', searchable: false, orderable: false },
        // campo de datos
        { targets: '[campo1]:name', width: '20%', searchable: true, orderable: true },

        // Estado: icono ✓ / ✗
        {
            targets: 'activo_[entidad]:name', width: '10%', orderable: true, searchable: true,
            render: function(data, type, row) {
                if (type === 'display') {
                    return row.activo_[entidad] == 1
                        ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
                        : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                }
                return row.activo_[entidad];
            }
        },

        // Botón activar/desactivar (condicional)
        {
            targets: 'activar:name', width: '10%', searchable: false, orderable: false,
            render: function(data, type, row) {
                if (row.activo_[entidad] == 1) {
                    return `<button type="button" class="btn btn-danger btn-sm desac[Entidad]"
                                    data-bs-toggle="tooltip" title="Desactivar"
                                    data-id_[entidad]="${row.id_[entidad]}">
                                <i class="fa-solid fa-trash"></i>
                            </button>`;
                } else {
                    return `<button class="btn btn-success btn-sm activar[Entidad]"
                                    data-bs-toggle="tooltip" title="Activar"
                                    data-id_[entidad]="${row.id_[entidad]}">
                                <i class="bi bi-hand-thumbs-up-fill"></i>
                            </button>`;
                }
            }
        },

        // Botón editar
        {
            targets: 'editar:name', width: '10%', searchable: false, orderable: false,
            render: function(data, type, row) {
                return `<button type="button" class="btn btn-info btn-sm editar[Entidad]"
                                data-bs-toggle="tooltip" title="Editar"
                                data-id_[entidad]="${row.id_[entidad]}">
                            <i class="fa-solid fa-edit"></i>
                        </button>`;
            }
        }
    ],

    ajax: {
        url: '../../controller/[entidad].php?op=listar',
        type: 'GET',
        dataSrc: function(json) {
            console.log('JSON recibido:', json);
            return json.data || json;
        }
    }
};
```

### 2.2 Inicialización y variables

```javascript
$(document).ready(function() {
    var $table = $('#tbl_[entidad]');
    var table_e = $table.DataTable(datatable_[entidad]Config);
    var $tableBody = $('#tbl_[entidad] tbody');

    // Filtros footer
    $table.find('tfoot input, tfoot select').on('keyup change', function() {
        table_e.column($(this).parent().index() + ':visible')
               .search(this.value)
               .draw();
    });
});
```

### 2.3 Función format() — child-row expandible

> El `format()` descrito aquí es el **patrón base** (tabla simple dentro de una card).
> Para child-rows avanzados con estructura multi-columna (3 cols + fila adicional,
> helper `val()`, iconos por campo, etc.) consultar el spec dedicado:
> **`.claude/specs/childrow_campos.md`**.
>
> ⚠️ El patrón avanzado de `childrow_campos.md` **no se aplica por defecto**.
> Debe solicitarse expresamente al generar o modificar el módulo.

```javascript
function format(d) {
    return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-gear-fill fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles de [Entidad]</h5>
                </div>
            </div>
            <div class="card-body p-0" style="overflow: visible;">
                <table class="table table-borderless table-striped table-hover mb-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="ps-4 w-25 align-top">
                                <i class="bi bi-hash me-2"></i>ID
                            </th>
                            <td class="pe-4">${d.id_[entidad] || '<span class="text-muted fst-italic">–</span>'}</td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 w-25 align-top">
                                <i class="bi bi-calendar-plus me-2"></i>Creado el
                            </th>
                            <td class="pe-4">
                                ${d.created_at_[entidad] ? formatoFechaEuropeo(d.created_at_[entidad]) : '<span class="text-muted fst-italic">–</span>'}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>`;
}

// Handler de expansión (delegado, dentro de document.ready)
$tableBody.on('click', 'td.details-control', function() {
    var tr  = $(this).closest('tr');
    var row = table_e.row(tr);
    if (row.child.isShown()) {
        row.child.hide();
        tr.removeClass('shown');
    } else {
        row.child(format(row.data())).show();
        tr.addClass('shown');
    }
});
```

### 2.4 Event handlers — TODOS delegados vía `$(document).on`

```javascript
// Desactivar
$(document).on('click', '.desac[Entidad]', function(event) {
    event.preventDefault();
    let id = $(this).data('id_[entidad]');
    Swal.fire({
        title: 'Desactivar',
        html: `¿Desea desactivar el registro con ID ${id}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../../controller/[entidad].php?op=eliminar', { id_[entidad]: id }, function() {
                table_e.ajax.reload();
                Swal.fire('Desactivado', 'Registro desactivado', 'success');
            });
        }
    });
});

// Activar
$(document).on('click', '.activar[Entidad]', function(event) {
    event.preventDefault();
    let id = $(this).data('id_[entidad]');
    Swal.fire({
        title: 'Activar',
        text: `¿Desea activar el registro con ID ${id}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('../../controller/[entidad].php?op=activar', { id_[entidad]: id }, function() {
                table_e.ajax.reload();
                Swal.fire('Activado', 'Registro activado', 'success');
            });
        }
    });
});

// Editar (cargar datos en modal)
$(document).on('click', '.editar[Entidad]', function(event) {
    event.preventDefault();
    let id = $(this).data('id_[entidad]');
    $.get('../../controller/[entidad].php?op=mostrar', { id_[entidad]: id }, function(data) {
        $('#mdltitulo').text('Editar [entidad]');
        $('input[name="id_[entidad]"]').val(data.id_[entidad]);
        $('input[name="[campo1]"]').val(data.[campo1]);
        new bootstrap.Modal(document.getElementById('modal[Entidad]')).show();
    });
});

// Nuevo registro
$(document).on('click', '#btnnuevo', function(event) {
    event.preventDefault();
    $('#mdltitulo').text('Nuevo [entidad]');
    $('#form[Entidad]')[0].reset();
    $('input[name="id_[entidad]"]').val('');
    new bootstrap.Modal(document.getElementById('modal[Entidad]')).show();
});

// Guardar/editar
$(document).on('click', '#btnSalvar[Entidad]', function(event) {
    event.preventDefault();
    let id     = $('input[name="id_[entidad]"]').val().trim();
    let campo1 = $('input[name="[campo1]"]').val().trim();
    $.ajax({
        url: '../../controller/[entidad].php?op=guardaryeditar',
        method: 'POST',
        data: { id_[entidad]: id, [campo1]: campo1 },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                bootstrap.Modal.getInstance(document.getElementById('modal[Entidad]')).hide();
                table_e.ajax.reload();
                toastr.success(response.message, 'Éxito');
            } else {
                toastr.error(response.message, 'Error');
            }
        }
    });
});
```

---

## 3. Controller PHP

```php
<?php
require_once '../config/conexion.php';
require_once '../config/funciones.php';
require_once '../models/[Entidad].php';

$registro = new RegistroActividad();
$entidad  = new [Entidad]();

switch ($_GET['op']) {

    case 'listar':
        $datos = $entidad->get_[entidades]();
        $data  = [];
        foreach ($datos as $row) {
            $data[] = [
                'id_[entidad]'       => $row['id_[entidad]'],
                '[campo1]'           => $row['[campo1]'],
                'activo_[entidad]'   => $row['activo_[entidad]'],
                'created_at_[entidad]' => $row['created_at_[entidad]']
            ];
        }
        header('Content-Type: application/json');
        echo json_encode([
            'draw'            => intval($_POST['draw'] ?? 1),
            'recordsTotal'    => count($data),
            'recordsFiltered' => count($data),
            'data'            => $data
        ], JSON_UNESCAPED_UNICODE);
        break;

    case 'guardaryeditar':
        $id     = $_POST['id_[entidad]'] ?? null;
        $campo1 = htmlspecialchars(trim($_POST['[campo1]']), ENT_QUOTES, 'UTF-8');
        try {
            if (empty($id)) {
                $res = $entidad->insert_[entidad]($campo1);
                echo json_encode(['success' => (bool)$res, 'message' => $res ? 'Registro creado' : 'Error al crear', 'id_[entidad]' => $res], JSON_UNESCAPED_UNICODE);
            } else {
                $res = $entidad->update_[entidad]($id, $campo1);
                echo json_encode(['success' => $res !== false, 'message' => $res !== false ? 'Registro actualizado' : 'Error al actualizar'], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => 'Error al procesar la solicitud'], JSON_UNESCAPED_UNICODE);
        }
        break;

    case 'mostrar':
        $id   = (int)$_POST['id_[entidad]'];
        $dato = $entidad->get_[entidad]xid($id);
        header('Content-Type: application/json');
        echo json_encode($dato ?: ['success' => false, 'message' => 'No encontrado'], JSON_UNESCAPED_UNICODE);
        break;

    case 'eliminar':
        $id  = (int)$_POST['id_[entidad]'];
        $res = $entidad->delete_[entidad]xid($id);
        echo json_encode(['success' => $res, 'message' => $res ? 'Registro desactivado' : 'Error'], JSON_UNESCAPED_UNICODE);
        break;

    case 'activar':
        $id  = (int)$_POST['id_[entidad]'];
        $res = $entidad->activar_[entidad]xid($id);
        echo json_encode(['success' => $res, 'message' => $res ? 'Registro activado' : 'Error'], JSON_UNESCAPED_UNICODE);
        break;

    case 'verificar':
        $campo_unico = $_POST['[campo1]'];
        $id          = $_POST['id_[entidad]'] ?? null;
        $res         = $entidad->verificar[Entidad]($campo_unico, $id);
        header('Content-Type: application/json');
        echo json_encode($res, JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

---

## 4. Model PHP

Ver spec completo en `.claude/specs/controller-models/models.md`.

Métodos mínimos requeridos para un módulo Mnt*:

| Método | Descripción |
|--------|-------------|
| `__construct()` | PDO + RegistroActividad + timezone Europe/Madrid |
| `get_[entidades]()` | Lista todos los activos (o vista SQL si existe) |
| `get_[entidades]_disponibles()` | Solo activos para selects/dropdowns |
| `get_[entidad]xid($id)` | Un registro por PK |
| `insert_[entidad](...)` | Inserta, devuelve `lastInsertId()` |
| `update_[entidad]($id, ...)` | Actualiza, devuelve `rowCount()` |
| `delete_[entidad]xid($id)` | Soft delete (`activo=0`) |
| `activar_[entidad]xid($id)` | Reactiva (`activo=1`) |
| `verificar[Entidad]($campo, $id)` | Unicidad de campo |

---

## 5. Checklist de implementación

### HTML
- [ ] `id` de tabla único
- [ ] Clases `table display responsive nowrap`
- [ ] Columna 0 vacía (expansión)
- [ ] Columna 1 = ID (se ocultará vía JS)
- [ ] `tfoot` con inputs/selects de filtro

### JavaScript
- [ ] Objeto config con `columns[]` y `columnDefs[]` usando `name:`
- [ ] `dataSrc: function(json) { return json.data || json; }`
- [ ] Render condicional en columna `activar` (btn-danger si activo, btn-success si inactivo)
- [ ] `format()` implementada para child-row
- [ ] Handler expansión en `$tableBody.on('click', 'td.details-control', ...)`
- [ ] **Todos los handlers delegados** via `$(document).on(...)`
- [ ] Filtros footer conectados a `table_e.column(...).search(...).draw()`
- [ ] `responsive: { details: { type: 'column', target: 0, renderer: fn } }` configurado
- [ ] `responsivePriority` asignado en columnDefs (1 = control, 2+ = columnas importantes, acciones = 4)

### Controller PHP
- [ ] Cases: `listar`, `guardaryeditar`, `mostrar`, `eliminar`, `activar`, `verificar`
- [ ] `header('Content-Type: application/json')` antes de cada echo
- [ ] `JSON_UNESCAPED_UNICODE` en todos los `json_encode`
- [ ] Sanitización con `htmlspecialchars` + `trim` en inputs de texto
- [ ] IDs casteados a `(int)`

### Model PHP
- [ ] Prepared statements en todas las queries
- [ ] Try-catch en todos los métodos
- [ ] Soft delete (nunca DELETE físico)
- [ ] `RegistroActividad` para errores y acciones

---

## 6. Responsive — Evitar Scroll Horizontal

### 6.1 Librerías requeridas

Las librerías Responsive ya están incluidas en los archivos de plantilla del proyecto.
Verificar que en `config/template/mainHead.php` exista el CSS y en `config/template/mainJs.php` el JS
(después del script principal de DataTables):

```html
<!-- CSS (mainHead.php) -->
<link href="...datatables.min.css" rel="stylesheet">   <!-- ya incluye Responsive -->

<!-- JS (mainJs.php) — en este orden -->
<script src="...dataTables.min.js"></script>
<script src="...dataTables.responsive.min.js"></script>
```

> Si las librerías Responsive **no** están en la plantilla global, añadirlas manualmente
> con las versiones CDN indicadas en `docs/responsive_datatables.md`.

### 6.2 Configuración en el objeto DataTable

```javascript
var datatable_[entidad]Config = {
    processing: true,

    // ← AÑADIR responsive al objeto de configuración
    responsive: {
        details: {
            type: 'column',
            target: 0,          // Columna 0 actúa como control de expansión
            renderer: function(api, rowIdx, columns) {
                var data = api.row(rowIdx).data();
                return format(data);   // Reutiliza la función format() existente
            }
        }
    },

    columns: [ /* ... */ ],
    columnDefs: [ /* ... */ ]
};
```

> ⚠️ Cuando se usa `responsive.details.renderer`, el handler manual de `details-control`
> (`$tableBody.on('click', 'td.details-control', ...)`) **debe eliminarse o comentarse**
> para evitar doble apertura del child-row.

### 6.3 responsivePriority en columnDefs

Determina qué columnas permanecen visibles al reducir el espacio:

| Prioridad | Significado |
|-----------|-------------|
| `1` | Siempre visible — columna de control (col 0) |
| `2` | Campo principal — nombre, código |
| `3` | Campo secundario importante |
| `4` | Botones de acción (mantener visibles) |
| Sin prioridad | Se ocultan primero |

```javascript
columnDefs: [
    // Control — siempre visible
    {
        targets: 'control:name',
        className: 'control text-center',
        orderable: false,
        searchable: false,
        responsivePriority: 1
    },
    // Campo principal
    {
        targets: '[campo1]:name',
        responsivePriority: 2
    },
    // Botones de acción
    {
        targets: 'activar:name',
        orderable: false,
        searchable: false,
        responsivePriority: 4
    },
    {
        targets: 'editar:name',
        orderable: false,
        searchable: false,
        responsivePriority: 4
    }
    // Columnas sin responsivePriority → se ocultarán primero
]
```

### 6.4 CSS — Evitar scroll horizontal

Añadir en el bloque `<style>` del `index.php` del módulo:

```css
/* Evitar scroll horizontal */
.dataTables_wrapper {
    overflow-x: hidden !important;
}
.table-wrapper {
    overflow-x: hidden !important;
}

/* Botón de control: azul colapsado, rojo expandido */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before {
    background-color: #0168fa;
}
table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control:before,
table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control:before {
    background-color: #d33333;
}
```

### 6.5 Checklist Responsive

- [ ] `responsive: { details: { type: 'column', target: 0, renderer: fn } }` en config
- [ ] Col 0 tiene `className: 'control'` (no `details-control`) en `columns[]` Y en `columnDefs`
- [ ] `responsivePriority` asignado: 1 (control), 2–3 (campos clave), 4 (acciones)
- [ ] Handler manual `td.details-control` eliminado/comentado (lo gestiona el renderer)
- [ ] CSS `overflow-x: hidden` en `.dataTables_wrapper` y `.table-wrapper`
- [ ] Librerías Responsive JS cargadas **después** del script principal de DataTables

---

## 7. Troubleshooting

| Síntoma | Causa probable | Solución |
|---------|---------------|----------|
| Tabla no carga datos | URL AJAX incorrecta o JSON malformado | Verificar `url` en `ajax:`, revisar consola + Network tab |
| Botones no responden | Handler no delegado (binding sobre elementos que no existen aún) | Usar `$(document).on(...)` siempre |
| Columna `activo` no filtra | `searchable: false` en columnDefs | Cambiar a `searchable: true` |
| Child-row no abre | Falta clase `details-control` en col 0 | Verificar `className` en col 0 config |
| `json.data` undefined | Controller devuelve array plano sin wrapper | Envolver en `{ data: [...], draw:1, ... }` O usar `dataSrc: json => json.data \|\| json` |
| Modal no se abre | Bootstrap JS no cargado o ID incorrecto | Verificar carga de `bootstrap.bundle.min.js` e ID del modal |
| Botón "+" responsive no aparece | Librerías Responsive JS no cargadas o col 0 sin `className: 'control'` | Verificar orden de scripts en `mainJs.php` y `className` en col 0 |
| Child-row se abre dos veces | Handler manual `details-control` coexiste con `renderer` responsive | Eliminar/comentar el handler `$tableBody.on('click', 'td.details-control', ...)` |
| Columnas no se ocultan en móvil | Falta `responsivePriority` o clase `responsive` en tabla | Añadir prioridades en `columnDefs` y verificar clase `table display responsive nowrap` |
| Scroll horizontal persiste | Sin `overflow-x: hidden` o anchos fijos suman >100% | Añadir CSS en `.dataTables_wrapper` y `.table-wrapper`; evitar `scrollX: true` |
