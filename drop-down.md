# Guía de Implementación: Dropdown de Acciones en DataTables

> Basado en la implementación real de `/view/MntClientes/` del proyecto MDR ERP Manager.  
> Este documento es suficiente para implementar el dropdown de acciones completo en cualquier proyecto que use el mismo stack.

---

## Índice

1. [¿Qué es el dropdown de acciones?](#1-qué-es-el-dropdown-de-acciones)
2. [Requisitos previos](#2-requisitos-previos)
3. [Cómo se genera el HTML del dropdown](#3-cómo-se-genera-el-html-del-dropdown)
4. [El ítem dinámico: Activar / Desactivar](#4-el-ítem-dinámico-activar--desactivar)
5. [Patrón `data-*` para pasar el ID de fila](#5-patrón-data--para-pasar-el-id-de-fila)
6. [Columna `acciones` en la tabla HTML](#6-columna-acciones-en-la-tabla-html)
7. [Columna `acciones` en la configuración DataTables](#7-columna-acciones-en-la-configuración-datatables)
8. [Event Listeners — captura de clics en los ítems del dropdown](#8-event-listeners--captura-de-clics-en-los-ítems-del-dropdown)
9. [SweetAlert2 — confirmaciones antes de acciones destructivas](#9-sweetalert2--confirmaciones-antes-de-acciones-destructivas)
10. [AJAX — llamadas al controller](#10-ajax--llamadas-al-controller)
11. [Navegación a página externa desde un ítem del dropdown](#11-navegación-a-página-externa-desde-un-ítem-del-dropdown)
12. [PHP — Controller: `case "eliminar"` y `case "activar"`](#12-php--controller-case-eliminar-y-case-activar)
13. [PHP — Modelo: soft-delete y reactivación](#13-php--modelo-soft-delete-y-reactivación)
14. [Checklist de implementación](#14-checklist-de-implementación)
15. [Ejemplo mínimo funcional completo](#15-ejemplo-mínimo-funcional-completo)

---

## 1. ¿Qué es el dropdown de acciones?

El **dropdown de acciones** es un botón `<button class="dropdown-toggle">` de Bootstrap 5 que se genera dinámicamente en la última columna de cada fila de DataTables. Al hacer clic abre un menú desplegable con todas las acciones disponibles para ese registro:

- **Editar** → navega a un formulario independiente
- **Contactos** → navega al módulo de contactos del cliente
- **Ubicaciones** → navega al módulo de ubicaciones del cliente
- **Activar / Desactivar** → ítem dinámico según el estado actual del registro (soft-delete)

**¿Cuándo usarlo?** Cuando una fila tiene más de 2-3 acciones posibles. El dropdown evita colapsar la columna con muchos botones y mantiene la interfaz limpia.

**¿Cómo funciona?** El HTML del botón y el menú se construyen dentro de la función `render()` de `columnDefs` en la configuración de DataTables. Los clics se capturan con event delegation en el `document` (no en la celda directamente, porque DataTables repinta las filas al paginar o filtrar).

---

## 2. Requisitos previos

```html
<!-- Bootstrap 5 — obligatorio para el componente Dropdown -->
<link href="../../public/lib/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
<script src="../../public/lib/bootstrap-5.0.2/js/bootstrap.bundle.min.js"></script>

<!-- FontAwesome 6 — para los iconos del menú -->
<link href="../../public/lib/fontawesome-6.4.2/css/all.min.css" rel="stylesheet">

<!-- Bootstrap Icons — para otros iconos del menú -->
<link rel="stylesheet" href="../../public/lib/bootstrap-icons/bootstrap-icons.css">

<!-- SweetAlert2 — para las confirmaciones antes de acciones destructivas -->
<link href="../../public/lib/sweetalert2-11.7.32/sweetalert2.min.css" rel="stylesheet">
<script src="../../public/lib/sweetalert2-11.7.32/sweetalert2.all.min.js"></script>

<!-- jQuery + DataTables -->
<script src="../../public/lib/jquery-3.7.1/jquery.min.js"></script>
<script src="../../public/lib/DataTables/datatables.min.js"></script>
```

> ⚠️ `bootstrap.bundle.min.js` incluye Popper.js, que es necesario para que el dropdown se posicione correctamente. No usar `bootstrap.min.js` sin bundle.

---

## 3. Cómo se genera el HTML del dropdown

El dropdown **no existe en el HTML estático** del fichero PHP. Se genera en tiempo de ejecución dentro de la función `render()` del `columnDef` de la columna `acciones`. DataTables llama a esta función una vez por cada fila al renderizar la tabla.

Código real de `view/MntClientes/mntclientes.js`:

```javascript
// columnDef para la columna 'acciones'
{
    targets: "acciones:name",
    width: '8%',
    searchable: false,
    orderable: false,
    className: "text-center",
    render: function (data, type, row) {
        // 'row' contiene el objeto completo de datos de la fila (igual que en format(d))
        // 'data' es null porque la columna tiene data:null y defaultContent:''
        // Construimos el ítem Activar/Desactivar de forma condicional:
        let itemActivarDesactivar = '';

        if (row.activo_cliente == 1) {
            // Si el cliente está ACTIVO → mostrar opción de Desactivar
            itemActivarDesactivar = `
                <li>
                    <a class="dropdown-item text-danger desacCliente" href="#"
                       data-id_cliente="${row.id_cliente}">
                        <i class="fa-solid fa-ban me-2"></i>Desactivar
                    </a>
                </li>`;
        } else {
            // Si el cliente está INACTIVO → mostrar opción de Activar
            itemActivarDesactivar = `
                <li>
                    <a class="dropdown-item text-success activarCliente" href="#"
                       data-id_cliente="${row.id_cliente}">
                        <i class="bi bi-hand-thumbs-up-fill me-2"></i>Activar
                    </a>
                </li>`;
        }

        // HTML completo del dropdown
        return `
            <div class="dropdown">
                <button class="btn btn-sm btn-secondary dropdown-toggle"
                        type="button"
                        data-bs-toggle="dropdown"
                        aria-expanded="false">
                    <i class="fa-solid fa-ellipsis-vertical"></i>
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow">

                    <!-- Ítem: Editar (navega a formulario independiente) -->
                    <li>
                        <a class="dropdown-item editarCliente" href="#"
                           data-id_cliente="${row.id_cliente}">
                            <i class="fa-solid fa-pen-to-square me-2"></i>Editar
                        </a>
                    </li>

                    <!-- Ítem: Contactos (navega a módulo de contactos) -->
                    <li>
                        <a class="dropdown-item formularioCliente" href="#"
                           data-id_cliente="${row.id_cliente}">
                            <i class="fas fa-users me-2"></i>Contactos
                        </a>
                    </li>

                    <!-- Ítem: Ubicaciones (enlace directo, sin listener JS) -->
                    <li>
                        <a class="dropdown-item"
                           href="../MntClientes_ubicaciones/index.php?id_cliente=${row.id_cliente}">
                            <i class="bi bi-geo-alt-fill me-2"></i>Ubicaciones
                        </a>
                    </li>

                    <!-- Separador visual -->
                    <li><hr class="dropdown-divider"></li>

                    <!-- Ítem dinámico: Activar o Desactivar según estado -->
                    ${itemActivarDesactivar}

                </ul>
            </div>`;
    }
}
```

---

## 4. El ítem dinámico: Activar / Desactivar

El toque clave del dropdown es que **uno de sus ítems cambia según el estado del registro**. Esto se consigue mediante una variable `let itemActivarDesactivar` que se construye condicionalmente antes del `return` del HTML.

```javascript
// Patrón para cualquier ítem dinámico según campo booleano:
let itemDinamico = '';

if (row.campo_estado == 1) {
    // Estado activo → ofrecer la acción de desactivar
    itemDinamico = `
        <li>
            <a class="dropdown-item text-danger claseDesactivar" href="#"
               data-id_entidad="${row.id_entidad}">
                <i class="fa-solid fa-ban me-2"></i>Desactivar
            </a>
        </li>`;
} else {
    // Estado inactivo → ofrecer la acción de activar
    itemDinamico = `
        <li>
            <a class="dropdown-item text-success claseActivar" href="#"
               data-id_entidad="${row.id_entidad}">
                <i class="bi bi-hand-thumbs-up-fill me-2"></i>Activar
            </a>
        </li>`;
}
```

**Colores de Bootstrap usados:**
- `text-danger` (rojo) para acciones destructivas o que deshabilitan
- `text-success` (verde) para acciones que restauran o habilitan

---

## 5. Patrón `data-*` para pasar el ID de fila

Todos los ítems del dropdown que necesitan saber qué registro afectar usan atributos `data-*` HTML5:

```html
<!-- En el render() del columnDef: -->
<a class="dropdown-item editarCliente" href="#"
   data-id_cliente="${row.id_cliente}">
    Editar
</a>
```

Y se recuperan en el event listener con `.data()` de jQuery:

```javascript
$(document).on('click', '.editarCliente', function (event) {
    event.preventDefault();
    let id = $(this).data('id_cliente');  // Lee el atributo data-id_cliente
    // ... usar 'id'
});
```

**Puntos críticos:**
- El nombre del atributo `data-id_cliente` puede ser cualquier cosa, pero debe ser **coherente** entre el `render()` y el listener.
- jQuery convierte automáticamente `data-id_cliente` → `.data('id_cliente')` (guiones bajos se mantienen).
- `href="#"` se usa para que el `<a>` sea clicable sin navegar; el `event.preventDefault()` en el listener evita el scroll a `#`.

---

## 6. Columna `acciones` en la tabla HTML

En el HTML estático de la vista (`index.php`) solo se necesita el `<th>` header y el `<th>` del tfoot. El contenido de las celdas `<td>` lo genera DataTables mediante el `render()`.

```html
<table id="clientes_data" class="table display responsive nowrap">
    <thead>
        <tr>
            <th></th>               <!-- Col 0: child-row -->
            <th>Id cliente</th>     <!-- Col 1: ID (oculta) -->
            <th>Código cliente</th>
            <th>Nombre cliente</th>
            <th>NIF</th>
            <th>Teléfono</th>
            <th>Descuento</th>
            <th>Contactos</th>
            <th>Estado</th>
            <!-- ▼ Columna de acciones: solo el encabezado -->
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th></th>
            <th class="d-none"><input ... /></th>
            <th><input placeholder="Buscar código" ... /></th>
            <th><input placeholder="Buscar nombre" ... /></th>
            <th><input placeholder="Buscar NIF" ... /></th>
            <th><input placeholder="Buscar teléfono" ... /></th>
            <th></th>
            <th></th>
            <th><select>...</select></th>
            <!-- ▼ Tfoot de acciones: sin input de filtro -->
            <th></th>
        </tr>
    </tfoot>
</table>
```

> La columna `acciones` **nunca tiene input de filtro** en el tfoot (`<th></th>` vacío), y en la configuración DataTables lleva `searchable: false, orderable: false`.

---

## 7. Columna `acciones` en la configuración DataTables

```javascript
var datatable_clientesConfig = {
    // ...
    columns: [
        { name: 'control',    data: null, defaultContent: '', className: 'details-control sorting_1 text-center' },
        { name: 'id_cliente', data: 'id_cliente', visible: false, className: "text-center" },
        { name: 'codigo_cliente',   data: 'codigo_cliente',   className: "text-center align-middle" },
        { name: 'nombre_cliente',   data: 'nombre_cliente',   className: "text-center align-middle" },
        { name: 'nif_cliente',      data: 'nif_cliente',      className: "text-center align-middle" },
        { name: 'telefono_cliente', data: 'telefono_cliente', className: "text-center align-middle" },
        { name: 'porcentaje_descuento_cliente', data: 'porcentaje_descuento_cliente', className: "text-center align-middle" },
        { name: 'cantidad_contactos', data: 'cantidad_contactos', className: "text-center align-middle" },
        { name: 'activo_cliente', data: 'activo_cliente', className: "text-center align-middle" },

        // ▼▼▼ COLUMNA ACCIONES ▼▼▼
        // data: null              → no viene del JSON; el contenido lo genera render()
        // defaultContent: ''      → sin contenido por defecto antes de renderizar
        // className: text-center  → centra el botón dropdown
        { name: 'acciones', data: null, defaultContent: '', className: "text-center align-middle" },
        // ▲▲▲ FIN COLUMNA ACCIONES ▲▲▲
    ],
    columnDefs: [
        // ... otras columnas ...

        // ▼▼▼ columnDef de ACCIONES ▼▼▼
        {
            targets: "acciones:name",   // apunta a la columna por su 'name', no por índice numérico
            width: '8%',
            searchable: false,          // no aparece en búsqueda global
            orderable: false,           // no se puede ordenar por esta columna
            className: "text-center",
            render: function (data, type, row) {
                // data → null (porque data:null en columns[])
                // type → 'display' | 'filter' | 'sort' | 'type'
                // row  → objeto completo del registro (todos los campos del JSON)

                let itemActivarDesactivar = '';
                if (row.activo_cliente == 1) {
                    itemActivarDesactivar = `
                        <li>
                            <a class="dropdown-item text-danger desacCliente" href="#"
                               data-id_cliente="${row.id_cliente}">
                                <i class="fa-solid fa-ban me-2"></i>Desactivar
                            </a>
                        </li>`;
                } else {
                    itemActivarDesactivar = `
                        <li>
                            <a class="dropdown-item text-success activarCliente" href="#"
                               data-id_cliente="${row.id_cliente}">
                                <i class="bi bi-hand-thumbs-up-fill me-2"></i>Activar
                            </a>
                        </li>`;
                }

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
                                <a class="dropdown-item editarCliente" href="#"
                                   data-id_cliente="${row.id_cliente}">
                                    <i class="fa-solid fa-pen-to-square me-2"></i>Editar
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item formularioCliente" href="#"
                                   data-id_cliente="${row.id_cliente}">
                                    <i class="fas fa-users me-2"></i>Contactos
                                </a>
                            </li>
                            <li>
                                <a class="dropdown-item"
                                   href="../MntClientes_ubicaciones/index.php?id_cliente=${row.id_cliente}">
                                    <i class="bi bi-geo-alt-fill me-2"></i>Ubicaciones
                                </a>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            ${itemActivarDesactivar}
                        </ul>
                    </div>`;
            }
        }
        // ▲▲▲ FIN columnDef de ACCIONES ▲▲▲
    ],
    // ...
};
```

**Por qué `targets: "acciones:name"` en lugar de un número:**

Usar el nombre (`name: 'acciones'` en `columns[]` + `targets: "acciones:name"` en `columnDefs[]`) hace el código **resistente a cambios de orden de columnas**. Si se reordena la tabla añadiendo o quitando columnas, el `columnDef` sigue apuntando a la columna correcta sin tener que actualizar índices numéricos.

---

## 8. Event Listeners — captura de clics en los ítems del dropdown

Los listeners usan **event delegation en `document`** (no en `tbody` ni en la celda), porque:
1. El menú desplegable de Bootstrap 5 se renderiza fuera del DOM normal cuando se abre.
2. DataTables repinta las filas al paginar, filtrar o recargar; los listeners directos se perderían.

Código real de `view/MntClientes/mntclientes.js`:

```javascript
// ── Listener para DESACTIVAR ─────────────────────────────────────────
$(document).on('click', '.desacCliente', function (event) {
    event.preventDefault();                      // Evita el salto a href="#"
    let id = $(this).data('id_cliente');         // Lee el atributo data-id_cliente del <a>
    desacCliente(id);                            // Llama a la función de confirmación
});

// ── Listener para ACTIVAR ────────────────────────────────────────────
$(document).on('click', '.activarCliente', function (event) {
    event.preventDefault();
    let id = $(this).data('id_cliente');
    activarCliente(id);
});

// ── Listener para EDITAR ─────────────────────────────────────────────
$(document).on('click', '.editarCliente', function (event) {
    event.preventDefault();
    let id = $(this).data('id_cliente');
    // Navega al formulario independiente en modo edición
    window.location.href = `formularioCliente.php?modo=editar&id=${id}`;
});

// ── Listener para CONTACTOS ──────────────────────────────────────────
$(document).on('click', '.formularioCliente', function (event) {
    event.preventDefault();
    let id = $(this).data('id_cliente');
    // Navega al módulo de contactos del cliente
    window.location.href = `../MntClientes_contacto/index.php?id_cliente=${id}`;
});
```

**Tabla de clases CSS usadas como selectores:**

| Clase del `<a>` | Acción que dispara |
|-----------------|-------------------|
| `.desacCliente` | Confirmar y desactivar (soft-delete) |
| `.activarCliente` | Confirmar y activar (revertir soft-delete) |
| `.editarCliente` | Navegar a formulario de edición |
| `.formularioCliente` | Navegar al módulo de contactos |

> ⚠️ Estas clases son **selectores funcionales**, no clases de estilo. No añadir estas clases a otros elementos de la página para evitar disparar los listeners por error.

---

## 9. SweetAlert2 — confirmaciones antes de acciones destructivas

Las acciones Desactivar y Activar piden confirmación antes de proceder. Esto evita errores accidentales.

### Función `desacCliente(id)` — con confirmación

```javascript
function desacCliente(id) {
    Swal.fire({
        title: 'Desactivar',
        html: `¿Desea desactivar el cliente con ID ${id}?<br><br>
               <small class="text-warning">
                   <i class="bi bi-exclamation-triangle me-1"></i>
                   El cliente quedará marcado como inactivo
               </small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        reverseButtons: true    // Pone el botón cancelar a la izquierda
    }).then((result) => {
        if (result.isConfirmed) {
            // Solo ejecuta la llamada AJAX si el usuario confirmó
            $.post("../../controller/cliente.php?op=eliminar",
                { id_cliente: id },
                function (data) {
                    // Recarga los datos de la tabla sin recargar la página
                    $table.DataTable().ajax.reload();

                    Swal.fire('Desactivado', 'El cliente ha sido desactivado', 'success');
                }
            );
        }
        // Si el usuario pulsa 'No' o cierra el modal, no ocurre nada
    });
}
```

### Función `activarCliente(id)` — con confirmación

```javascript
function activarCliente(id) {
    Swal.fire({
        title: 'Activar',
        text: `¿Desea activar el cliente con ID ${id}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Si',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../controller/cliente.php?op=activar",
                { id_cliente: id },
                function (data) {
                    $table.DataTable().ajax.reload();

                    Swal.fire('Activado', 'El cliente ha sido activado', 'success');
                }
            );
        }
    });
}
```

**Diferencias entre las dos confirmaciones:**

| Propiedad | Desactivar | Activar |
|-----------|-----------|---------|
| `title` | `'Desactivar'` | `'Activar'` |
| `html` vs `text` | `html` (para incluir el aviso en rojo) | `text` (simple) |
| Mensaje de advertencia | Sí (`text-warning`) | No |
| `icon` | `'question'` | `'question'` |
| `op` del controller | `eliminar` | `activar` |
| Respuesta SweetAlert | `'Desactivado'` / `'success'` | `'Activado'` / `'success'` |

**¿Por qué `op=eliminar` para desactivar?** Por convención histórica del proyecto. En el modelo hace un soft-delete (`activo_cliente=0`), no un DELETE físico.

---

## 10. AJAX — llamadas al controller

Ambas acciones usan `$.post()` de jQuery, que envía los datos por POST:

```javascript
// Desactivar (soft-delete)
$.post(
    "../../controller/cliente.php?op=eliminar",  // URL con operación en query string
    { id_cliente: id },                           // Datos enviados en el body POST
    function (data) {                             // Callback al recibir respuesta
        $table.DataTable().ajax.reload();         // Recarga los datos de la tabla
        Swal.fire('Desactivado', '...', 'success');
    }
);

// Activar (revertir soft-delete)
$.post(
    "../../controller/cliente.php?op=activar",
    { id_cliente: id },
    function (data) {
        $table.DataTable().ajax.reload();
        Swal.fire('Activado', '...', 'success');
    }
);
```

**Puntos importantes:**
- `$table.DataTable().ajax.reload()` recarga los datos desde el servidor y vuelve a renderizar la tabla, incluido el **ítem dinámico** del dropdown (Activar/Desactivar) que ahora mostrará la opción contraria.
- No se usa `table_e.ajax.reload()` directamente por legibilidad, pero es equivalente.
- El controller para estas operaciones **no devuelve JSON**: simplemente ejecuta la operación y no hace `echo` de nada. DataTables ignora la respuesta vacía del callback.

---

## 11. Navegación a página externa desde un ítem del dropdown

El ítem **Ubicaciones** usa un enlace HTML directo en lugar de un listener JavaScript. Es el patrón correcto cuando la acción es simplemente navegar a otra URL:

```javascript
// En el render() del columnDef — enlace directo (sin class funcional, sin listener)
`<li>
    <a class="dropdown-item"
       href="../MntClientes_ubicaciones/index.php?id_cliente=${row.id_cliente}">
        <i class="bi bi-geo-alt-fill me-2"></i>Ubicaciones
    </a>
</li>`
```

El ID se pasa directamente en la query string (`?id_cliente=${row.id_cliente}`), usando template literals de JavaScript. No necesita `data-*` porque no hay listener que lo lea; el navegador simplemente sigue el `href`.

**Comparativa de los dos patrones de navegación:**

| Patrón | Cuándo usarlo | Ejemplo en MntClientes |
|--------|--------------|----------------------|
| `href` directo con query string | Navegación simple a otra página | Ubicaciones |
| `href="#"` + `data-*` + listener JS | Cuando se necesita lógica antes de navegar (log, modal, etc.) | Editar, Contactos |

---

## 12. PHP — Controller: `case "eliminar"` y `case "activar"`

Código real de `controller/cliente.php`:

```php
case "eliminar":
    // Llama al modelo para hacer el soft-delete (activo_cliente = 0)
    $cliente->delete_clientexid($_POST["id_cliente"]);

    $registro->registrarActividad(
        'admin',
        'cliente.php',
        'Eliminar cliente seleccionado',
        "Cliente eliminado exitosamente",
        "info"
    );
    // No hace echo — el AJAX callback recibe respuesta vacía (ignorada por JS)
    break;

case "activar":
    // Llama al modelo para revertir el soft-delete (activo_cliente = 1)
    $cliente->activar_clientexid($_POST["id_cliente"]);

    $registro->registrarActividad(
        'admin',
        'cliente.php',
        'Activar cliente seleccionado',
        "Cliente activado exitosamente",
        "info"
    );
    // No hace echo — el AJAX callback recibe respuesta vacía (ignorada por JS)
    break;
```

> En estos dos `case` el controller **no devuelve JSON**. La operación es tan sencilla que el feedback al usuario se gestiona directamente desde el SweetAlert2 en el callback JS, sin depender de la respuesta del servidor.
>
> Si se quiere un manejo de errores más robusto, se puede añadir `echo json_encode(['success' => true])` y validar en el callback, pero en la implementación real de MntClientes no está presente.

---

## 13. PHP — Modelo: soft-delete y reactivación

Código real de `models/Clientes.php`:

```php
// Desactivar (soft-delete: activo_cliente = 0)
public function delete_clientexid($id_cliente)
{
    try {
        // NUNCA usar DELETE físico — solo marcar como inactivo
        $sql = "UPDATE cliente SET activo_cliente = 0 WHERE id_cliente = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
        $stmt->execute();

        $this->registro->registrarActividad(
            'admin',
            'Clientes',
            'Desactivar',
            "Se desactivó el cliente con ID: $id_cliente",
            'info'
        );

        // Retorna true si se afectó al menos 1 fila, false si el ID no existía
        return $stmt->rowCount() > 0;

    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Clientes',
            'delete_clientexid',
            "Error al desactivar el cliente {$id_cliente}: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}

// Reactivar (revertir soft-delete: activo_cliente = 1)
public function activar_clientexid($id_cliente)
{
    try {
        $sql = "UPDATE cliente SET activo_cliente = 1 WHERE id_cliente = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_cliente, PDO::PARAM_INT);
        $stmt->execute();

        $this->registro->registrarActividad(
            'admin',
            'Clientes',
            'Activar',
            "Se activó el cliente con ID: $id_cliente",
            'info'
        );

        return $stmt->rowCount() > 0;

    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Clientes',
            'activar_clientexid',
            "Error al activar el cliente {$id_cliente}: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}
```

**Reglas del soft-delete en este proyecto:**

| Regla | Motivo |
|-------|--------|
| `UPDATE ... SET activo = 0` en lugar de `DELETE` | Permite restaurar registros y mantener el historial |
| `prepared statement` con `bindValue` | Prevenir SQL injection |
| `PDO::PARAM_INT` para el ID | El ID siempre es entero, nunca string |
| `rowCount() > 0` como retorno | Indica si el ID existía y fue procesado |
| Logging de éxito **y** de error | Trazabilidad para auditoría y depuración |

---

## 14. Checklist de implementación

### HTML (`view/MntNueva/index.php`)
- [ ] Añadir en el `<thead>` el `<th>Acciones</th>` como **última columna**
- [ ] Añadir en el `<tfoot>` el `<th></th>` vacío en la posición correspondiente
- [ ] No poner nada en `<tbody>` — DataTables lo rellena vía AJAX

### JavaScript — Configuración DataTables
- [ ] En `columns[]`, añadir al final: `{ name: 'acciones', data: null, defaultContent: '', className: "text-center align-middle" }`
- [ ] En `columnDefs[]`, añadir el bloque con `targets: "acciones:name"`, `searchable: false`, `orderable: false` y la función `render()`
- [ ] Dentro del `render()`:
  - [ ] Construir la variable condicional `itemActivarDesactivar` según `row.campo_activo`
  - [ ] Poner en cada `<a>` el atributo `data-id_entidad="${row.id_entidad}"`
  - [ ] Usar clases CSS funcionales únicas (ej. `desacEntidad`, `activarEntidad`, `editarEntidad`)
  - [ ] Para ítems de navegación directa, usar `href="ruta?id=${row.id_entidad}"` en lugar de `href="#"`

### JavaScript — Event Listeners
- [ ] Usar `$(document).on('click', '.claseUnica', function(event) { ... })` para cada acción
- [ ] Siempre incluir `event.preventDefault()` cuando el `<a>` tenga `href="#"`
- [ ] Leer el ID con `$(this).data('id_entidad')`
- [ ] Encapsular la lógica en funciones nombradas (`desacEntidad(id)`, `activarEntidad(id)`)

### JavaScript — SweetAlert2
- [ ] Para acciones destructivas (desactivar), usar `html:` con aviso visual
- [ ] Para acciones de restauración (activar), `text:` simple es suficiente
- [ ] Incluir `reverseButtons: true` para que el botón de confirmación quede a la derecha
- [ ] Dentro del `.then()`, llamar a `$table.DataTable().ajax.reload()` para refrescar el dropdown
- [ ] Mostrar SweetAlert de confirmación tras la operación exitosa

### PHP — Controller
- [ ] Añadir `case "eliminar":` que llama a `$modelo->delete_entidadxid($_POST["id_entidad"])`
- [ ] Añadir `case "activar":` que llama a `$modelo->activar_entidadxid($_POST["id_entidad"])`
- [ ] Registrar actividad con `$registro->registrarActividad(...)` en ambos cases
- [ ] Verificar que el campo `activo_entidad` se incluye en el `case "listar"` (lo necesita el `render()` para el ítem dinámico)

### PHP — Modelo
- [ ] Implementar `delete_entidadxid($id)` con `UPDATE ... SET activo_entidad = 0`
- [ ] Implementar `activar_entidadxid($id)` con `UPDATE ... SET activo_entidad = 1`
- [ ] Ambos métodos deben usar `prepared statements` con `bindValue`
- [ ] Retornar `$stmt->rowCount() > 0`
- [ ] Envolver en `try-catch` con logging de errores

---

## 15. Ejemplo mínimo funcional completo

Implementación para una entidad genérica `producto`. Copy-paste ready, adaptando los nombres de campos.

### HTML (`view/MntProductos/index.php`)

```html
<table id="productos_data" class="table display responsive nowrap">
    <thead>
        <tr>
            <th></th>              <!-- child-row -->
            <th>ID</th>            <!-- oculta -->
            <th>Referencia</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acciones</th>      <!-- dropdown -->
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th></th>
            <th class="d-none"></th>
            <th><input type="text" placeholder="Buscar referencia" class="form-control form-control-sm"></th>
            <th><input type="text" placeholder="Buscar nombre" class="form-control form-control-sm"></th>
            <th></th>
            <th>
                <select class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </th>
            <th></th>   <!-- sin filtro en acciones -->
        </tr>
    </tfoot>
</table>
```

### JavaScript (`view/MntProductos/mntproductos.js`)

```javascript
$(document).ready(function () {

    var datatable_productosConfig = {
        processing: true,
        language: {
            emptyTable:  "No hay productos registrados",
            zeroRecords: "No se encontraron productos",
        },
        columns: [
            { name: 'control',           data: null,               defaultContent: '', className: 'details-control sorting_1 text-center' },
            { name: 'id_producto',       data: 'id_producto',      visible: false, className: "text-center" },
            { name: 'referencia_producto', data: 'referencia_producto', className: "text-center align-middle" },
            { name: 'nombre_producto',   data: 'nombre_producto',  className: "text-center align-middle" },
            { name: 'precio_producto',   data: 'precio_producto',  className: "text-center align-middle" },
            { name: 'activo_producto',   data: 'activo_producto',  className: "text-center align-middle" },
            // ▼ Columna acciones
            { name: 'acciones',          data: null,               defaultContent: '', className: "text-center align-middle" },
        ],
        columnDefs: [
            { targets: "control:name",            width: '5%',  searchable: false, orderable: false },
            { targets: "id_producto:name",         width: '5%',  searchable: false, orderable: false },
            { targets: "referencia_producto:name", width: '15%', searchable: true,  orderable: true  },
            { targets: "nombre_producto:name",     width: '30%', searchable: true,  orderable: true  },
            { targets: "precio_producto:name",     width: '12%', searchable: false, orderable: true  },
            {
                targets: "activo_producto:name", width: '8%', searchable: true, orderable: true,
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_producto == 1
                            ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
                            : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_producto;
                }
            },

            // ▼▼▼ columnDef del DROPDOWN DE ACCIONES ▼▼▼
            {
                targets: "acciones:name",
                width: '10%',
                searchable: false,
                orderable: false,
                className: "text-center",
                render: function (data, type, row) {

                    // ── Ítem dinámico: cambia según estado del registro ──
                    let itemActivarDesactivar = '';
                    if (row.activo_producto == 1) {
                        itemActivarDesactivar = `
                            <li>
                                <a class="dropdown-item text-danger desacProducto" href="#"
                                   data-id_producto="${row.id_producto}">
                                    <i class="fa-solid fa-ban me-2"></i>Desactivar
                                </a>
                            </li>`;
                    } else {
                        itemActivarDesactivar = `
                            <li>
                                <a class="dropdown-item text-success activarProducto" href="#"
                                   data-id_producto="${row.id_producto}">
                                    <i class="bi bi-hand-thumbs-up-fill me-2"></i>Activar
                                </a>
                            </li>`;
                    }

                    // ── HTML completo del dropdown ──
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
                                    <a class="dropdown-item editarProducto" href="#"
                                       data-id_producto="${row.id_producto}">
                                        <i class="fa-solid fa-pen-to-square me-2"></i>Editar
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                ${itemActivarDesactivar}
                            </ul>
                        </div>`;
                }
            }
            // ▲▲▲ FIN columnDef del DROPDOWN DE ACCIONES ▲▲▲
        ],
        ajax: {
            url: '../../controller/producto.php?op=listar',
            type: 'GET',
            dataSrc: function (json) { return json.data || json; }
        },
        deferRender: true,
        pageLength: 25,
        order: [[2, 'asc']]
    };

    var $table              = $('#productos_data');
    var $tableBody          = $('#productos_data tbody');
    var $columnFilterInputs = $('#productos_data tfoot input, #productos_data tfoot select');
    var table_e             = $table.DataTable(datatable_productosConfig);

    // ── format() para el child-row (si lo hay) ──────────────────
    function format(d) {
        return `<div class="card border-primary mb-3">
                    <div class="card-body">
                        <strong>Descripción:</strong> ${d.descripcion_producto || 'Sin descripción'}
                    </div>
                </div>`;
    }

    // ── Event listener child-row ─────────────────────────────────
    $tableBody.on('click', 'td.details-control', function () {
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

    // ── Función: Desactivar producto ─────────────────────────────
    function desacProducto(id) {
        Swal.fire({
            title: 'Desactivar',
            html: `¿Desea desactivar el producto con ID ${id}?<br><br>
                   <small class="text-warning">
                       <i class="bi bi-exclamation-triangle me-1"></i>
                       El producto quedará marcado como inactivo
                   </small>`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/producto.php?op=eliminar",
                    { id_producto: id },
                    function () {
                        $table.DataTable().ajax.reload();
                        Swal.fire('Desactivado', 'El producto ha sido desactivado', 'success');
                    }
                );
            }
        });
    }

    // ── Función: Activar producto ────────────────────────────────
    function activarProducto(id) {
        Swal.fire({
            title: 'Activar',
            text: `¿Desea activar el producto con ID ${id}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Si',
            cancelButtonText: 'No',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                $.post("../../controller/producto.php?op=activar",
                    { id_producto: id },
                    function () {
                        $table.DataTable().ajax.reload();
                        Swal.fire('Activado', 'El producto ha sido activado', 'success');
                    }
                );
            }
        });
    }

    // ── Event Listeners del dropdown (event delegation en document) ──
    $(document).on('click', '.desacProducto', function (event) {
        event.preventDefault();
        desacProducto($(this).data('id_producto'));
    });

    $(document).on('click', '.activarProducto', function (event) {
        event.preventDefault();
        activarProducto($(this).data('id_producto'));
    });

    $(document).on('click', '.editarProducto', function (event) {
        event.preventDefault();
        let id = $(this).data('id_producto');
        window.location.href = `formularioProducto.php?modo=editar&id=${id}`;
    });

    // ── Filtros de pie de tabla ──────────────────────────────────
    $columnFilterInputs.on('keyup change', function () {
        var idx = table_e.column($(this).closest('th')).index();
        table_e.column(idx).search($(this).val()).draw();
    });

}); // fin document.ready
```

### PHP — Controller (`controller/producto.php`)

```php
<?php
require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/Producto.php";

$registro = new RegistroActividad();
$producto = new Producto();

switch ($_GET["op"]) {

    case "listar":
        $datos = $producto->get_productos();
        $data  = [];
        foreach ($datos as $row) {
            $data[] = [
                // ── Campos necesarios por el dropdown ─────────────────
                // activo_producto es OBLIGATORIO: lo usa render() para el ítem dinámico
                "id_producto"          => $row["id_producto"],
                "activo_producto"      => $row["activo_producto"],
                // ── Columnas visibles ──────────────────────────────────
                "referencia_producto"  => $row["referencia_producto"],
                "nombre_producto"      => $row["nombre_producto"],
                "precio_producto"      => $row["precio_producto"],
                // ── Solo para el child-row ─────────────────────────────
                "descripcion_producto" => $row["descripcion_producto"],
                "created_at_producto"  => $row["created_at_producto"],
                "updated_at_producto"  => $row["updated_at_producto"],
            ];
        }
        $results = ["draw" => 1, "recordsTotal" => count($data), "recordsFiltered" => count($data), "data" => $data];
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;

    case "eliminar":
        $producto->delete_productoxid($_POST["id_producto"]);
        $registro->registrarActividad('admin', 'producto.php', 'Desactivar producto',
            "Producto desactivado ID: " . $_POST["id_producto"], 'info');
        break;

    case "activar":
        $producto->activar_productoxid($_POST["id_producto"]);
        $registro->registrarActividad('admin', 'producto.php', 'Activar producto',
            "Producto activado ID: " . $_POST["id_producto"], 'info');
        break;
}
?>
```

### PHP — Modelo (`models/Producto.php`)

```php
public function delete_productoxid($id_producto)
{
    try {
        $sql = "UPDATE producto SET activo_producto = 0 WHERE id_producto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_producto, PDO::PARAM_INT);
        $stmt->execute();
        $this->registro->registrarActividad('admin', 'Producto', 'delete_productoxid',
            "Producto desactivado ID: $id_producto", 'info');
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', 'Producto', 'delete_productoxid',
            "Error: " . $e->getMessage(), 'error');
        return false;
    }
}

public function activar_productoxid($id_producto)
{
    try {
        $sql = "UPDATE producto SET activo_producto = 1 WHERE id_producto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_producto, PDO::PARAM_INT);
        $stmt->execute();
        $this->registro->registrarActividad('admin', 'Producto', 'activar_productoxid',
            "Producto activado ID: $id_producto", 'info');
        return $stmt->rowCount() > 0;
    } catch (PDOException $e) {
        $this->registro->registrarActividad('admin', 'Producto', 'activar_productoxid',
            "Error: " . $e->getMessage(), 'error');
        return false;
    }
}
```

---

## Resumen del flujo completo

```
1. DataTables inicializa → llama a controller?op=listar → JSON con campo activo_X
2. render() del columnDef "acciones" se ejecuta por cada fila:
   ├─ activo_X == 1  → ítem "Desactivar" (clase .desacEntidad, text-danger)
   └─ activo_X == 0  → ítem "Activar"   (clase .activarEntidad, text-success)
3. Cada <a> lleva data-id_entidad="${row.id_entidad}"
4. Usuario abre el dropdown → Bootstrap 5 gestiona la apertura (data-bs-toggle="dropdown")
5. Usuario hace clic en un ítem:
   ├─ Editar/Contactos → $(document).on('click','.clase') → window.location.href
   ├─ Enlace directo   → el href navega directamente (sin listener)
   └─ Desactivar/Activar → $(document).on('click','.clase') → SweetAlert2 confirmación
6. Si confirma → $.post() al controller con { id_entidad: id }
7. Controller llama al modelo → UPDATE activo = 0/1
8. Callback JS → $table.DataTable().ajax.reload() → tabla se recarga
9. render() vuelve a ejecutarse → el ítem dinámico muestra ahora la opción contraria
```

---

*Documentado el 25/02/2026 — Proyecto MDR ERP Manager — Basado en `/view/MntClientes/`*
