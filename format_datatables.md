# Guía de Implementación: Child-Row en DataTables

> Basado en la implementación real de `/view/MntClientes/` del proyecto MDR ERP Manager.  
> Este documento es suficiente para implementar la funcionalidad completa en cualquier proyecto que use el mismo stack.

---

## Índice

1. [¿Qué es el child-row?](#1-qué-es-el-child-row)
2. [Requisitos previos](#2-requisitos-previos)
3. [Assets necesarios (imágenes PNG)](#3-assets-necesarios-imágenes-png)
4. [CSS obligatorio](#4-css-obligatorio)
5. [HTML — Estructura de la tabla](#5-html--estructura-de-la-tabla)
6. [JavaScript — Configuración completa DataTables](#6-javascript--configuración-completa-datatables)
7. [Función `format(d)` — El generador del child-row](#7-función-formatd--el-generador-del-child-row)
8. [Event Listener — Click para expandir/colapsar](#8-event-listener--click-para-expandircolapsar)
9. [Helper: `formatoFechaEuropeo()`](#9-helper-formatofechaeuropeo)
10. [PHP — Controller: `case "listar"`](#10-php--controller-case-listar)
11. [PHP — Modelo: vista SQL como fuente de datos](#11-php--modelo-vista-sql-como-fuente-de-datos)
12. [Alternativa: Bootstrap Icons en lugar de PNG](#12-alternativa-bootstrap-icons-en-lugar-de-png)
13. [Checklist de implementación](#13-checklist-de-implementación)
14. [Ejemplo mínimo funcional completo](#14-ejemplo-mínimo-funcional-completo)

---

## 1. ¿Qué es el child-row?

El **child-row** (fila hija) es una funcionalidad nativa de DataTables que permite expandir una fila para mostrar **información adicional** del registro, sin abandonar la vista de lista. Al hacer clic en el icono de la primera columna:

- La fila se expande mostrando un panel de detalles HTML personalizado.
- El icono cambia de "abrir" a "cerrar".
- Al volver a hacer clic, el panel se oculta.

**¿Cuándo usarlo?** Cuando una entidad tiene muchos campos pero solo unos pocos necesitan visibilidad directa en la tabla; el resto se expone en el child-row sin necesidad de modal ni navegación.

**Versión de DataTables requerida:** DataTables 1.10+ o 2.x. La API `row.child()` está disponible a partir de la versión 1.10.

---

## 2. Requisitos previos

El proyecto MDR usa las siguientes librerías, que deben estar disponibles:

```html
<!-- jQuery (obligatorio para DataTables) -->
<script src="../../public/lib/jquery-3.7.1/jquery.min.js"></script>

<!-- Bootstrap 5 (para el card y badges del child-row) -->
<link href="../../public/lib/bootstrap-5.0.2/css/bootstrap.min.css" rel="stylesheet">
<script src="../../public/lib/bootstrap-5.0.2/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap Icons (para los iconos dentro del child-row) -->
<link rel="stylesheet" href="../../public/lib/bootstrap-icons/bootstrap-icons.css">

<!-- DataTables -->
<link href="../../public/lib/DataTables/datatables.min.css" rel="stylesheet">
<script src="../../public/lib/DataTables/datatables.min.js"></script>
```

> ⚠️ El orden importa: jQuery debe cargarse **antes** que DataTables y Bootstrap JS.

---

## 3. Assets necesarios (imágenes PNG)

El icono de expandir/colapsar se implementa mediante **dos imágenes PNG** que se muestran como `background` CSS en la celda activadora.

| Imagen | Propósito | Ruta en el proyecto |
|--------|-----------|---------------------|
| `details_open.png` | Icono mostrado cuando la fila está **cerrada** (invita a expandir) | `public/assets/images/details_open.png` |
| `details_close.png` | Icono mostrado cuando la fila está **abierta** (invita a colapsar) | `public/assets/images/details_close.png` |

Estas imágenes son PNG estándar de DataTables (habitualmente un triángulo o flecha). Se pueden obtener de la descarga oficial de DataTables o sustituirlas por iconos con CSS puro (ver [sección 12](#12-alternativa-bootstrap-icons-en-lugar-de-png)).

---

## 4. CSS obligatorio

Este bloque CSS está definido **globalmente** en `config/template/mainHead.php` dentro de una etiqueta `<style>`, por lo que aplica a **todas las vistas** del proyecto.

```css
/* =============================================
   CHILD-ROW DATATABLES — ICONOS DE CONTROL
   Añadir en el <head> de forma global
   ============================================= */

/* Celda activadora cuando la fila está CERRADA → icono de abrir */
td.details-control {
    background: url('../../public/assets/images/details_open.png') no-repeat center center;
    cursor: pointer;
}

/* Celda activadora cuando la fila está ABIERTA → icono de cerrar */
/* Se activa añadiendo la clase 'shown' al <tr> padre en JavaScript */
tr.shown td.details-control {
    background: url('../../public/assets/images/details_close.png') no-repeat center center;
}
```

**Puntos clave:**
- La clase `details-control` se aplica a la **celda `<td>`** (no al `<tr>` ni a un botón).
- La clase `shown` se añade/quita dinámicamente al `<tr>` desde JavaScript.
- El `cursor: pointer` es esencial para indicar al usuario que la celda es clicable.
- La ruta a las imágenes debe ajustarse según la profundidad de directorio de cada vista.

---

## 5. HTML — Estructura de la tabla

Extracto real de `view/MntClientes/index.php`:

```html
<!-- Tabla de clientes -->
<div class="table-wrapper">
    <table id="clientes_data" class="table display responsive nowrap">
        <thead>
            <tr>
                <!-- ▼ COLUMNA 0: ACTIVADOR DEL CHILD-ROW — siempre vacío en el thead -->
                <th></th>
                <!-- Columna 1: ID (se marca como visible:false en DataTables) -->
                <th>Id cliente</th>
                <!-- Columnas visibles -->
                <th>Código cliente</th>
                <th>Nombre cliente</th>
                <th>NIF</th>
                <th>Teléfono</th>
                <th><i class="bi bi-percent"></i> Descuento</th>
                <th><i class="bi bi-people-fill"></i> Contactos</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <!-- Los datos se cargan vía AJAX — no poner nada aquí -->
        </tbody>
        <tfoot>
            <tr>
                <!-- Col 0: sin filtro (es el activador del child-row) -->
                <th></th>
                <!-- Col 1: ID — clase d-none para ocultarla visualmente también en el tfoot -->
                <th class="d-none">
                    <input type="text" placeholder="Buscar ID" class="form-control form-control-sm" />
                </th>
                <!-- Cols con filtro de texto -->
                <th><input type="text" placeholder="Buscar código" class="form-control form-control-sm" /></th>
                <th><input type="text" placeholder="Buscar nombre cliente" class="form-control form-control-sm" /></th>
                <th><input type="text" placeholder="Buscar NIF" class="form-control form-control-sm" /></th>
                <th><input type="text" placeholder="Buscar teléfono" class="form-control form-control-sm" /></th>
                <!-- Cols sin filtro -->
                <th></th>
                <th></th>
                <!-- Col Estado: filtro con select -->
                <th>
                    <select class="form-control form-control-sm" title="Filtrar por estado">
                        <option value="">Todos los estados</option>
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                </th>
                <!-- Col Acciones: sin filtro -->
                <th></th>
            </tr>
        </tfoot>
    </table>
</div>
```

**Puntos críticos del HTML:**

| Regla | Motivo |
|-------|--------|
| El `<th></th>` vacío en posición 0 del `<thead>` es **obligatorio** | DataTables necesita que el número de `<th>` coincida con el número de columnas definidas en JS |
| Las clases `display responsive nowrap` en el `<table>` | `display` activa DataTables, `responsive` adapta a pantallas pequeñas, `nowrap` evita que el texto se parta |
| El `id` de la tabla (`clientes_data`) debe ser **único** en la página | Es el selector con el que JavaScript inicializa DataTables |
| El `<tfoot>` también necesita el `<th></th>` vacío en posición 0 | Para que los filtros de columna queden alineados correctamente |

---

## 6. JavaScript — Configuración completa DataTables

Código real de `view/MntClientes/mntclientes.js`:

```javascript
$(document).ready(function () {

    // ─────────────────────────────────────────────────────────────
    // 1. CONFIGURACIÓN DE LA TABLA (objeto de configuración)
    // ─────────────────────────────────────────────────────────────
    var datatable_clientesConfig = {
        processing: true, // Muestra el indicador de carga mientras procesa
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
            emptyTable:    "No hay clientes registrados",
            info:          "Mostrando _START_ a _END_ de _TOTAL_ clientes",
            infoEmpty:     "Mostrando 0 a 0 de 0 clientes",
            infoFiltered:  "(filtrado de _MAX_ clientes totales)",
            lengthMenu:    "Mostrar _MENU_ clientes por página",
            loadingRecords:"Cargando...",
            processing:    "Procesando...",
            search:        "Buscar:",
            zeroRecords:   "No se encontraron clientes que coincidan con la búsqueda",
            paginate: {
                first:    '<i class="bi bi-chevron-double-left"></i>',
                last:     '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next:     '<i class="bi bi-chevron-compact-right"></i>'
            }
        },

        // ─── DEFINICIÓN DE COLUMNAS ───────────────────────────────
        columns: [
            // ▼▼▼ COLUMNA 0: ACTIVADOR DEL CHILD-ROW ▼▼▼
            // data: null        → No viene del JSON, es solo un control visual
            // defaultContent:'' → Sin contenido HTML por defecto
            // className         → 'details-control' activa el CSS del icono PNG
            //                     'sorting_1' es cosmético (primera columna ordenada)
            //                     'text-center' centra el icono
            {
                name: 'control',
                data: null,
                defaultContent: '',
                className: 'details-control sorting_1 text-center'
            },
            // ▲▲▲ FIN COLUMNA ACTIVADOR ▲▲▲

            // Columna 1: ID (oculta en la tabla, pero disponible en row.data())
            { name: 'id_cliente',   data: 'id_cliente',   visible: false, className: "text-center" },

            // Columnas visibles
            { name: 'codigo_cliente',   data: 'codigo_cliente',   className: "text-center align-middle" },
            { name: 'nombre_cliente',   data: 'nombre_cliente',   className: "text-center align-middle" },
            { name: 'nif_cliente',      data: 'nif_cliente',      className: "text-center align-middle" },
            { name: 'telefono_cliente', data: 'telefono_cliente', className: "text-center align-middle" },
            { name: 'porcentaje_descuento_cliente', data: 'porcentaje_descuento_cliente', className: "text-center align-middle" },
            { name: 'cantidad_contactos', data: 'cantidad_contactos', className: "text-center align-middle" },
            { name: 'activo_cliente',   data: 'activo_cliente',   className: "text-center align-middle" },

            // Columna de acciones: también data:null porque se construye con render()
            { name: 'acciones', data: null, defaultContent: '', className: "text-center align-middle" },
        ],

        // ─── DEFINICIÓN DE COMPORTAMIENTO POR COLUMNA ─────────────
        columnDefs: [
            // Columna 0: el activador — sin búsqueda, sin ordenación
            { targets: "control:name",    width: '5%',  searchable: false, orderable: false, className: "text-center" },
            // Columna 1: ID — sin búsqueda, sin ordenación
            { targets: "id_cliente:name", width: '5%',  searchable: false, orderable: false, className: "text-center" },
            // Columnas con búsqueda activada
            { targets: "codigo_cliente:name",   width: '12%', searchable: true,  orderable: true,  className: "text-center" },
            { targets: "nombre_cliente:name",   width: '20%', searchable: true,  orderable: true,  className: "text-center" },
            { targets: "nif_cliente:name",      width: '10%', searchable: true,  orderable: true,  className: "text-center" },
            { targets: "telefono_cliente:name", width: '12%', searchable: true,  orderable: true,  className: "text-center" },

            // Columna con render personalizado: Descuento (badge de color por rangos)
            {
                targets: "porcentaje_descuento_cliente:name",
                width: '8%', orderable: true, searchable: false, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        var descuento = parseFloat(data) || 0;
                        var badgeClass = 'bg-secondary';
                        var icon = 'bi-dash-circle';
                        if (descuento > 0) {
                            if      (descuento <= 5)  { badgeClass = 'bg-info';    icon = 'bi-percent'; }
                            else if (descuento <= 15) { badgeClass = 'bg-warning'; icon = 'bi-percent'; }
                            else                      { badgeClass = 'bg-success'; icon = 'bi-percent'; }
                        }
                        return `<span class="badge ${badgeClass} fs-6">
                                    <i class="bi ${icon} me-1"></i>${descuento.toFixed(2)}%
                                </span>`;
                    }
                    return parseFloat(data) || 0;
                }
            },

            // Columna con render personalizado: Contactos (badge verde/gris)
            {
                targets: "cantidad_contactos:name",
                width: '8%', orderable: true, searchable: false, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        var cantidad = parseInt(data) || 0;
                        var badgeClass = cantidad > 0 ? 'bg-success' : 'bg-secondary';
                        return `<span class="badge ${badgeClass} fs-6">
                                    <i class="bi bi-people-fill me-1"></i>${cantidad}
                                </span>`;
                    }
                    return parseInt(data) || 0;
                }
            },

            // Columna con render personalizado: Estado (icono ✓/✗)
            {
                targets: "activo_cliente:name",
                width: '8%', orderable: true, searchable: true, className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_cliente == 1
                            ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
                            : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_cliente;
                }
            },

            // Columna con render personalizado: Acciones (dropdown Bootstrap)
            {
                targets: "acciones:name",
                width: '8%', searchable: false, orderable: false, className: "text-center",
                render: function (data, type, row) {
                    // Label dinámico Activar / Desactivar según estado
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
                                <li><hr class="dropdown-divider"></li>
                                ${itemActivarDesactivar}
                            </ul>
                        </div>`;
                }
            }
        ],

        // ─── AJAX: datos del servidor ──────────────────────────────
        ajax: {
            url: '../../controller/cliente.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                // Verificación defensiva: acepta tanto {data:[...]} como [...]
                if (!json || (!json.data && !Array.isArray(json))) {
                    console.warn("No se recibieron datos válidos del servidor");
                    return [];
                }
                return json.data || json;
            },
            error: function(xhr, status, error) {
                console.error("Error al cargar datos:", error);
            }
        },

        // ─── Otras opciones ────────────────────────────────────────
        deferRender: true,   // Renderiza solo las filas visibles (mejor rendimiento)
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        order: [[2, 'asc']]  // Ordenar por código_cliente (columna índice 2) por defecto
    };

    // ─────────────────────────────────────────────────────────────
    // 2. REFERENCIAS / VARIABLES DE TRABAJO
    // ─────────────────────────────────────────────────────────────
    var $table              = $('#clientes_data');
    var $tableConfig        = datatable_clientesConfig;
    var $tableBody          = $('#clientes_data tbody');
    var $columnFilterInputs = $('#clientes_data tfoot input, #clientes_data tfoot select');

    // Instancia principal de DataTables — se usa en toda la lógica posterior
    var table_e = $table.DataTable($tableConfig);

    // ... [función format(d) — ver sección 7]
    // ... [event listener — ver sección 8]

}); // fin document.ready
```

---

## 7. Función `format(d)` — El generador del child-row

Esta función recibe como argumento `d`, que es el **objeto completo de datos de la fila** (`row.data()`). Devuelve un **string HTML** que DataTables inserta como child-row.

En MntClientes genera una **card Bootstrap de dos columnas**, con todos los datos extendidos del cliente que no se muestran en la tabla principal.

```javascript
function format(d) {
    // 'd' contiene TODOS los campos devueltos por el JSON del controller,
    // incluidos los que no se muestran en ninguna columna visible de la tabla.
    // Por eso el controller debe devolver todos los campos que se quieren mostrar aquí.

    return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-person-fill fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles del Cliente</h5>
                </div>
            </div>

            <div class="card-body p-0" style="overflow: visible;">
                <div class="row">

                    <!-- ═══════════════════════════════════════ -->
                    <!-- COLUMNA IZQUIERDA: Datos principales   -->
                    <!-- ═══════════════════════════════════════ -->
                    <div class="col-md-6">
                        <table class="table table-borderless table-striped table-hover mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-hash me-2"></i>ID Cliente
                                    </th>
                                    <td class="pe-4">
                                        ${d.id_cliente || '<span class="text-muted fst-italic">Sin ID</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-upc me-2"></i>Código
                                    </th>
                                    <td class="pe-4">
                                        ${d.codigo_cliente || '<span class="text-muted fst-italic">Sin código</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-person me-2"></i>Nombre
                                    </th>
                                    <td class="pe-4">
                                        ${d.nombre_cliente || '<span class="text-muted fst-italic">Sin nombre</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-card-text me-2"></i>NIF/CIF
                                    </th>
                                    <td class="pe-4">
                                        ${d.nif_cliente || '<span class="text-muted fst-italic">Sin NIF/CIF</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-geo-alt me-2"></i>Dirección
                                    </th>
                                    <td class="pe-4">
                                        ${d.direccion_cliente || '<span class="text-muted fst-italic">Sin dirección</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-mailbox me-2"></i>CP/Población
                                    </th>
                                    <td class="pe-4">
                                        ${(d.cp_cliente && d.poblacion_cliente)
                                            ? `${d.cp_cliente} - ${d.poblacion_cliente}`
                                            : '<span class="text-muted fst-italic">Sin CP/Población</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-map me-2"></i>Provincia
                                    </th>
                                    <td class="pe-4">
                                        ${d.provincia_cliente || '<span class="text-muted fst-italic">Sin provincia</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-telephone me-2"></i>Teléfono
                                    </th>
                                    <td class="pe-4">
                                        ${d.telefono_cliente || '<span class="text-muted fst-italic">Sin teléfono</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-envelope me-2"></i>Email
                                    </th>
                                    <td class="pe-4">
                                        ${d.email_cliente
                                            ? `<a href="mailto:${d.email_cliente}" target="_blank">${d.email_cliente}</a>`
                                            : '<span class="text-muted fst-italic">Sin email</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-globe me-2"></i>Web
                                    </th>
                                    <td class="pe-4">
                                        ${d.web_cliente
                                            ? `<a href="${d.web_cliente}" target="_blank" rel="noopener">${d.web_cliente}</a>`
                                            : '<span class="text-muted fst-italic">Sin web</span>'}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- ═══════════════════════════════════════════════ -->
                    <!-- COLUMNA DERECHA: Facturación, pagos y más      -->
                    <!-- ═══════════════════════════════════════════════ -->
                    <div class="col-md-6">
                        <table class="table table-borderless table-striped table-hover mb-0">
                            <tbody>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-receipt me-2"></i>Facturación
                                    </th>
                                    <td class="pe-4">
                                        ${d.nombre_facturacion_cliente || '<span class="text-muted fst-italic">Sin nombre facturación</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-geo-alt-fill me-2"></i>Dir. Facturación
                                    </th>
                                    <td class="pe-4">
                                        ${d.direccion_facturacion_cliente || '<span class="text-muted fst-italic">Sin dirección facturación</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-mailbox2 me-2"></i>CP/Pobl. Facturación
                                    </th>
                                    <td class="pe-4">
                                        ${(d.cp_facturacion_cliente && d.poblacion_facturacion_cliente)
                                            ? `${d.cp_facturacion_cliente} - ${d.poblacion_facturacion_cliente}`
                                            : '<span class="text-muted fst-italic">Sin CP/Población facturación</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-map-fill me-2"></i>Prov. Facturación
                                    </th>
                                    <td class="pe-4">
                                        ${d.provincia_facturacion_cliente || '<span class="text-muted fst-italic">Sin provincia facturación</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-printer me-2"></i>Fax
                                    </th>
                                    <td class="pe-4">
                                        ${d.fax_cliente || '<span class="text-muted fst-italic">Sin fax</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-chat-text me-2"></i>Observaciones
                                    </th>
                                    <!-- max-width + word-wrap: para que textos largos no rompan el layout -->
                                    <td class="pe-4" style="max-width: 300px; word-wrap: break-word; white-space: pre-wrap; overflow-wrap: break-word;">
                                        ${d.observaciones_cliente || '<span class="text-muted fst-italic">Sin observaciones</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-calendar-plus me-2"></i>Creado el:
                                    </th>
                                    <td class="pe-4">
                                        ${d.created_at_cliente
                                            ? formatoFechaEuropeo(d.created_at_cliente)
                                            : '<span class="text-muted fst-italic">Sin fecha</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-credit-card me-2"></i>Forma de Pago
                                    </th>
                                    <td class="pe-4">
                                        ${d.descripcion_forma_pago_cliente || '<span class="text-muted fst-italic">Sin forma de pago</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-tag me-2"></i>Tipo de Pago
                                    </th>
                                    <td class="pe-4">
                                        ${d.tipo_pago_cliente
                                            ? `<span class="badge ${
                                                d.tipo_pago_cliente === 'Pago único'
                                                    ? 'bg-info'
                                                    : d.tipo_pago_cliente === 'Pago fraccionado'
                                                        ? 'bg-warning'
                                                        : 'bg-secondary'
                                                }">${d.tipo_pago_cliente}</span>`
                                            : '<span class="text-muted fst-italic">Sin información</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-percent me-2"></i>Descuento
                                    </th>
                                    <td class="pe-4">
                                        ${d.descuento_pago
                                            ? `${d.descuento_pago}%`
                                            : '<span class="text-muted fst-italic">Sin descuento</span>'}
                                    </td>
                                </tr>
                                <tr>
                                    <th scope="row" class="ps-4 w-40 align-top">
                                        <i class="bi bi-check-circle me-2"></i>Estado Forma Pago
                                    </th>
                                    <td class="pe-4">
                                        ${d.estado_forma_pago_cliente
                                            ? `<span class="badge ${
                                                d.estado_forma_pago_cliente === 'Configurado'
                                                    ? 'bg-success'
                                                    : d.estado_forma_pago_cliente === 'Sin configurar'
                                                        ? 'bg-secondary'
                                                        : 'bg-danger'
                                                }">${d.estado_forma_pago_cliente}</span>`
                                            : '<span class="text-muted fst-italic">Sin estado</span>'}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                </div><!-- .row -->
            </div><!-- .card-body -->

            <div class="card-footer bg-transparent border-top-0 text-end">
                <small class="text-muted">
                    Actualizado: ${d.updated_at_cliente
                        ? formatoFechaEuropeo(d.updated_at_cliente)
                        : 'Sin fecha de actualización'}
                </small>
            </div>

        </div><!-- .card -->
    `;
}
```

**Puntos clave de la función `format(d)`:**

| Decisión | Motivo |
|----------|--------|
| Recibe `row.data()` completo, no solo el ID | Evita una segunda llamada AJAX por registro |
| Patrón `${d.campo \|\| '<span class="text-muted fst-italic">Sin X</span>'}` | Muestra texto "Sin X" en gris si el campo es `null`, `undefined` o vacío |
| `white-space: pre-wrap` en el campo de observaciones | Respeta los saltos de línea tal como se guardaron en la BD |
| La función se define **dentro** del `$(document).ready()` | Tiene acceso al scope de `table_e` aunque no lo usa directamente |
| El HTML del child-row puede ser **cualquier HTML válido** | Se pueden incluir tablas, listas, imágenes, botones, etc. |

---

## 8. Event Listener — Click para expandir/colapsar

Este bloque es el **núcleo de la funcionalidad** del child-row. Se coloca dentro del `$(document).ready()`, después de declarar `table_e` y la función `format`.

```javascript
// ─────────────────────────────────────────────────────────────────────
// EVENT LISTENER DEL CHILD-ROW
// NO MODIFICAR la lógica. Solo cambiar:
//   - '#clientes_data tbody'  →  selector del tbody de la tabla destino
//   - table_e                 →  instancia de DataTables de la tabla destino
// ─────────────────────────────────────────────────────────────────────

// IMPORTANTE: el listener se registra en $tableBody (el tbody),
// NO en la tabla ni en el documento completo.
// Así evitamos que otros elementos con 'details-control' en otras tablas
// disparen esta lógica.
$tableBody.on('click', 'td.details-control', function () {
    var tr  = $(this).closest('tr');     // El <tr> de la fila clicada
    var row = table_e.row(tr);           // El objeto row de DataTables para esa fila

    if (row.child.isShown()) {
        // La fila ya está abierta → CERRAR
        row.child.hide();                // Oculta el child-row
        tr.removeClass('shown');         // Quita el CSS de "abierto" (cambia el icono PNG)
    } else {
        // La fila está cerrada → ABRIR
        row.child(format(row.data())).show(); // Genera el HTML y lo muestra
        tr.addClass('shown');                 // Añade el CSS de "abierto" (cambia el icono PNG)
    }
});
```

**Puntos críticos del event listener:**

| Punto | Explicación |
|-------|-------------|
| `$tableBody.on('click', 'td.details-control', ...)` | Usar **event delegation** en el `tbody`, no en `td` directamente, porque DataTables puede repintar los `<td>` y los listeners directos se perderían |
| `$(this).closest('tr')` | Sube al `<tr>` padre desde la celda clicada |
| `table_e.row(tr)` | Obtiene el objeto Row de DataTables —  necesario para acceder a `row.child` y `row.data()` |
| `row.child.isShown()` | Comprueba si ya hay un child-row abierto para esa fila |
| `row.child(format(row.data())).show()` | Pasa el HTML generado por `format()` a DataTables y lo muestra |
| `tr.addClass('shown')` / `tr.removeClass('shown')` | Actualiza el estado visual del icono PNG mediante el CSS definido en la sección 4 |

---

## 9. Helper: `formatoFechaEuropeo()`

Esta función se usa dentro de `format(d)` para convertir los timestamps de MySQL al formato `DD/MM/YYYY HH:MM`. Se define **fuera** del `$(document).ready()` para ser accesible globalmente desde cualquier vista que incluya el script.

```javascript
// FUERA del $(document).ready() — función global
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return 'Sin fecha';

    try {
        const fecha = new Date(fechaString);
        if (isNaN(fecha.getTime())) return 'Fecha inválida';

        const dia     = fecha.getDate().toString().padStart(2, '0');
        const mes     = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const año     = fecha.getFullYear();
        const horas   = fecha.getHours().toString().padStart(2, '0');
        const minutos = fecha.getMinutes().toString().padStart(2, '0');

        return `${dia}/${mes}/${año} ${horas}:${minutos}`;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Error en fecha';
    }
}
```

> Esta función transforma `"2024-06-15 09:30:00"` → `"15/06/2024 09:30"`.

---

## 10. PHP — Controller: `case "listar"`

Código real de `controller/cliente.php`. El punto crítico es que **todos los campos** que se van a mostrar en el child-row deben incluirse en el array de respuesta, aunque no tengan columna visible en la tabla.

```php
case "listar":
    $datos = $cliente->get_clientes();
    $data  = array();

    foreach ($datos as $row) {
        $data[] = array(
            // ── Datos básicos (columnas visibles en la tabla) ──────────
            "id_cliente"       => $row["id_cliente"],
            "codigo_cliente"   => $row["codigo_cliente"],
            "nombre_cliente"   => $row["nombre_cliente"],
            "nif_cliente"      => $row["nif_cliente"],
            "telefono_cliente" => $row["telefono_cliente"],
            "activo_cliente"   => $row["activo_cliente"],

            // ── Campos SOLO para el child-row (no tienen columna visible) ─
            "direccion_cliente"   => $row["direccion_cliente"],
            "cp_cliente"          => $row["cp_cliente"],
            "poblacion_cliente"   => $row["poblacion_cliente"],
            "provincia_cliente"   => $row["provincia_cliente"],
            "fax_cliente"         => $row["fax_cliente"],
            "web_cliente"         => $row["web_cliente"],
            "email_cliente"       => $row["email_cliente"],

            // ── Dirección de facturación ────────────────────────────────
            "nombre_facturacion_cliente"    => $row["nombre_facturacion_cliente"],
            "direccion_facturacion_cliente" => $row["direccion_facturacion_cliente"],
            "cp_facturacion_cliente"        => $row["cp_facturacion_cliente"],
            "poblacion_facturacion_cliente" => $row["poblacion_facturacion_cliente"],
            "provincia_facturacion_cliente" => $row["provincia_facturacion_cliente"],

            // ── Timestamps ──────────────────────────────────────────────
            "created_at_cliente" => $row["created_at_cliente"],
            "updated_at_cliente" => $row["updated_at_cliente"],

            // ── Observaciones ───────────────────────────────────────────
            "observaciones_cliente" => $row["observaciones_cliente"],

            // ── Sistema de descuentos ───────────────────────────────────
            "porcentaje_descuento_cliente" => $row["porcentaje_descuento_cliente"] ?? 0.00,
            "categoria_descuento_cliente"  => $row["categoria_descuento_cliente"]  ?? 'Sin descuento',
            "tiene_descuento_cliente"      => isset($row["tiene_descuento_cliente"]) ? (bool)$row["tiene_descuento_cliente"] : false,

            // ── Forma de pago habitual ──────────────────────────────────
            "id_forma_pago_habitual"   => $row["id_forma_pago_habitual"],
            "codigo_pago"              => $row["codigo_pago"]              ?? null,
            "nombre_pago"              => $row["nombre_pago"]              ?? null,
            "descuento_pago"           => $row["descuento_pago"]           ?? null,
            "porcentaje_anticipo_pago" => $row["porcentaje_anticipo_pago"] ?? null,
            "dias_anticipo_pago"       => $row["dias_anticipo_pago"]       ?? null,
            "porcentaje_final_pago"    => $row["porcentaje_final_pago"]    ?? null,
            "dias_final_pago"          => $row["dias_final_pago"]          ?? null,
            "observaciones_pago"       => $row["observaciones_pago"]       ?? null,
            "activo_pago"              => $row["activo_pago"]              ?? null,

            // ── Método de pago ──────────────────────────────────────────
            "id_metodo_pago"          => $row["id_metodo_pago"]          ?? null,
            "codigo_metodo_pago"      => $row["codigo_metodo_pago"]      ?? null,
            "nombre_metodo_pago"      => $row["nombre_metodo_pago"]      ?? null,
            "observaciones_metodo_pago" => $row["observaciones_metodo_pago"] ?? null,
            "activo_metodo_pago"      => $row["activo_metodo_pago"]      ?? null,

            // ── Campos calculados (vienen de la vista SQL) ──────────────
            // "cantidad_contactos" viene como "cantidad_contactos_cliente" en la vista
            "cantidad_contactos"       => isset($row["cantidad_contactos_cliente"]) ? intval($row["cantidad_contactos_cliente"]) : 0,
            "tipo_pago_cliente"        => $row["tipo_pago_cliente"]        ?? null,
            "descripcion_forma_pago_cliente"         => $row["descripcion_forma_pago_cliente"]         ?? null,
            "direccion_completa_cliente"              => $row["direccion_completa_cliente"]              ?? null,
            "direccion_facturacion_completa_cliente"  => $row["direccion_facturacion_completa_cliente"]  ?? null,
            "tiene_direccion_facturacion_diferente"   => isset($row["tiene_direccion_facturacion_diferente"]) ? (bool)$row["tiene_direccion_facturacion_diferente"] : false,
            "estado_forma_pago_cliente" => $row["estado_forma_pago_cliente"] ?? null
        );
    }

    $results = array(
        "draw"            => 1,
        "recordsTotal"    => count($data),
        "recordsFiltered" => count($data),
        "data"            => $data
    );

    header('Content-Type: application/json');
    echo json_encode($results, JSON_UNESCAPED_UNICODE);
    break;
```

**Regla fundamental:** Si un campo aparece en `format(d)` como `d.campo`, **debe estar** en el array del controller. Si no está, aparecerá como `undefined` en JavaScript y el child-row mostrará texto vacío o el fallback de "Sin X".

---

## 11. PHP — Modelo: vista SQL como fuente de datos

El modelo **no consulta la tabla `cliente` directamente**, sino una **vista SQL** llamada `contacto_cantidad_cliente`. Esta vista consolida los JOINs con otras tablas y calcula campos derivados. Esto evita que el controller tenga que hacer joins manuales.

```php
// models/Clientes.php
public function get_clientes()
{
    try {
        // La vista SQL 'contacto_cantidad_cliente' ya incluye:
        //   - JOINs con forma_pago, metodo_pago
        //   - COUNT de contactos → campo 'cantidad_contactos_cliente'
        //   - Campos calculados: tipo_pago_cliente, descripcion_forma_pago_cliente,
        //     estado_forma_pago_cliente, direccion_completa_cliente, etc.
        $sql = "SELECT * FROM contacto_cantidad_cliente ORDER BY nombre_cliente ASC";

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Clientes',
            'get_clientes',
            "Error al listar los clientes: " . $e->getMessage(),
            "error"
        );
        // En producción devolver array vacío o false, nunca hacer die()
    }
}
```

**¿Por qué usar una vista SQL?**

| Sin vista SQL | Con vista SQL |
|---------------|---------------|
| El controller necesita múltiples JOINs | El controller hace `SELECT * FROM vista` |
| El modelo devuelve datos crudos | La vista devuelve campos calculados listos |
| Difícil de mantener y reutilizar | Centralizado y reutilizable desde cualquier módulo |

**Campos calculados que genera la vista** (exemplos):
- `cantidad_contactos_cliente` → `COUNT` de registros en tabla `contacto_cliente`
- `descripcion_forma_pago_cliente` → texto descriptivo de los plazos de pago
- `tipo_pago_cliente` → `'Pago único'` o `'Pago fraccionado'` según estructura
- `estado_forma_pago_cliente` → `'Configurado'` / `'Sin configurar'` / `'Incompleto'`
- `direccion_completa_cliente` → concatenación de `direccion + cp + poblacion + provincia`

---

## 12. Alternativa: Bootstrap Icons en lugar de PNG

Si el proyecto destino **no tiene** las imágenes `details_open.png` y `details_close.png`, se puede sustituir el CSS de imágenes por iconos de Bootstrap Icons:

```css
/* Alternativa sin imágenes PNG — usando Bootstrap Icons (pseudoelementos) */

td.details-control {
    cursor: pointer;
    text-align: center;
    vertical-align: middle;
    font-size: 1.3rem;
    color: #0d6efd;   /* Bootstrap primary */
}

/* Icono de flecha derecha (expandir) */
td.details-control::before {
    font-family: "bootstrap-icons";
    content: "\f285";  /* bi-chevron-right */
}

/* Icono de flecha abajo (colapsar) — cuando el tr tiene clase 'shown' */
tr.shown td.details-control::before {
    content: "\f282";  /* bi-chevron-down */
    color: #198754;    /* Bootstrap success */
}
```

O bien, con caracteres Unicode simples (sin dependencias):

```css
td.details-control {
    cursor: pointer;
    text-align: center;
    vertical-align: middle;
    font-size: 1.2rem;
    color: #0d6efd;
    user-select: none;
}

td.details-control::before {
    content: "▶";
}

tr.shown td.details-control::before {
    content: "▼";
    color: #198754;
}
```

> ⚠️ Si se usa la alternativa CSS, la columna control **no debe tener** `defaultContent: ''`  sino que el icono lo pone el pseudoelemento `::before`. No es necesario cambiar nada en la lógica JavaScript.

---

## 13. Checklist de implementación

Pasos exactos para implementar el child-row en una vista nueva:

### CSS
- [ ] Añadir el bloque CSS de `td.details-control` / `tr.shown td.details-control` en el `<head>` global (o en el `<head>` de la vista específica)
- [ ] Verificar que la ruta a `details_open.png` y `details_close.png` es correcta para la profundidad de directorio de la nueva vista (o usar la alternativa CSS)

### HTML
- [ ] En el `<thead>`, añadir `<th></th>` vacío como **primera columna**
- [ ] En el `<tfoot>`, añadir `<th></th>` vacío como **primera columna** también
- [ ] El `id` del `<table>` debe ser único y coincidir con el selector usado en JS
- [ ] Clases mínimas en el `<table>`: `table display responsive nowrap`

### JavaScript (dentro de `$(document).ready()`)
- [ ] En el array `columns[]`, agregar en **posición 0** la entrada del control:
  ```javascript
  { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' }
  ```
- [ ] En `columnDefs[]`, agregar la definición del control en posición 0:
  ```javascript
  { targets: "control:name", width: '5%', searchable: false, orderable: false, className: "text-center" }
  ```
- [ ] Definir las variables `$table`, `$tableBody`, `$columnFilterInputs` y la instancia `table_e`
- [ ] Copiar/adaptar la función `format(d)` con los campos del JSON de la entidad nueva
- [ ] Copiar el event listener `$tableBody.on('click', 'td.details-control', ...)` **sin modificar su lógica**
- [ ] Asegurarse de que `$tableBody` apunta al tbody de **esta tabla** y que `table_e` es la instancia correcta

### JavaScript (fuera de `$(document).ready()`)
- [ ] Definir `formatoFechaEuropeo()` si se van a mostrar fechas en el child-row

### PHP — Controller
- [ ] En el `case "listar"`, incluir en el array de cada fila **todos los campos** que se vayan a usar en `format(d)`, aunque no tengan columna visible
- [ ] Usar el operador `?? null` para los campos opcionales, evitando errores de clave inexistente

### PHP — Modelo
- [ ] Si la entidad requiere datos de tablas relacionadas, considerar crear (o usar) una **vista SQL** que consolide los JOINs y los campos calculados en lugar de hacerlos en el controller

---

## 14. Ejemplo mínimo funcional completo

Ejemplo para una entidad genérica `producto` con child-row. Es copy-paste ready.

### CSS (en el `<head>` global)

```css
td.details-control {
    background: url('../../public/assets/images/details_open.png') no-repeat center center;
    cursor: pointer;
}
tr.shown td.details-control {
    background: url('../../public/assets/images/details_close.png') no-repeat center center;
}
```

### HTML (`view/MntProductos/index.php`)

```html
<table id="productos_data" class="table display responsive nowrap">
    <thead>
        <tr>
            <th></th>               <!-- Col 0: activador child-row -->
            <th>ID</th>             <!-- Col 1: oculta -->
            <th>Referencia</th>
            <th>Nombre</th>
            <th>Precio</th>
            <th>Estado</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th></th>
            <th class="d-none"><input type="text" class="form-control form-control-sm" /></th>
            <th><input type="text" placeholder="Buscar referencia" class="form-control form-control-sm" /></th>
            <th><input type="text" placeholder="Buscar nombre" class="form-control form-control-sm" /></th>
            <th></th>
            <th>
                <select class="form-control form-control-sm">
                    <option value="">Todos</option>
                    <option value="1">Activo</option>
                    <option value="0">Inactivo</option>
                </select>
            </th>
            <th></th>
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
            info:        "Mostrando _START_ a _END_ de _TOTAL_ productos",
            zeroRecords: "No se encontraron productos",
        },
        columns: [
            // ▼ Columna 0: activador child-row — obligatoria y en primera posición
            { name: 'control',      data: null, defaultContent: '', className: 'details-control sorting_1 text-center' },
            // Columna 1: ID oculta
            { name: 'id_producto',  data: 'id_producto',  visible: false, className: "text-center" },
            // Columnas visibles
            { name: 'referencia_producto', data: 'referencia_producto', className: "text-center align-middle" },
            { name: 'nombre_producto',     data: 'nombre_producto',     className: "text-center align-middle" },
            { name: 'precio_producto',     data: 'precio_producto',     className: "text-center align-middle" },
            { name: 'activo_producto',     data: 'activo_producto',     className: "text-center align-middle" },
            { name: 'acciones',            data: null, defaultContent: '', className: "text-center align-middle" },
        ],
        columnDefs: [
            { targets: "control:name",           width: '5%',  searchable: false, orderable: false },
            { targets: "id_producto:name",        width: '5%',  searchable: false, orderable: false },
            { targets: "referencia_producto:name", width: '15%', searchable: true,  orderable: true  },
            { targets: "nombre_producto:name",    width: '30%', searchable: true,  orderable: true  },
            { targets: "precio_producto:name",    width: '12%', searchable: false, orderable: true  },
            {
                targets: "activo_producto:name", width: '8%', orderable: true, searchable: true,
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_producto == 1
                            ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
                            : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_producto;
                }
            },
            {
                targets: "acciones:name", width: '10%', searchable: false, orderable: false,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm btn-warning editarProducto"
                                data-id_producto="${row.id_producto}">
                            <i class="bi bi-pencil"></i>
                        </button>`;
                }
            }
        ],
        ajax: {
            url: '../../controller/producto.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                return json.data || json;
            }
        },
        deferRender: true,
        pageLength: 25,
        order: [[2, 'asc']]
    };

    // ── Variables de trabajo ────────────────────────────────────
    var $table              = $('#productos_data');
    var $tableBody          = $('#productos_data tbody');
    var $columnFilterInputs = $('#productos_data tfoot input, #productos_data tfoot select');
    var table_e             = $table.DataTable(datatable_productosConfig);

    // ── Función que genera el HTML del child-row ────────────────
    // Adaptar los campos según los que devuelva el controller
    function format(d) {
        return `
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-box-seam fs-4 me-2"></i>
                    <strong>Detalles del Producto</strong>
                </div>
                <div class="card-body p-0">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped mb-0">
                                <tbody>
                                    <tr>
                                        <th class="ps-4"><i class="bi bi-hash me-2"></i>ID</th>
                                        <td>${d.id_producto || '<span class="text-muted fst-italic">Sin ID</span>'}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-4"><i class="bi bi-upc me-2"></i>Referencia</th>
                                        <td>${d.referencia_producto || '<span class="text-muted fst-italic">Sin referencia</span>'}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-4"><i class="bi bi-tag me-2"></i>Nombre</th>
                                        <td>${d.nombre_producto || '<span class="text-muted fst-italic">Sin nombre</span>'}</td>
                                    </tr>
                                    <tr>
                                        <th class="ps-4"><i class="bi bi-currency-euro me-2"></i>Precio</th>
                                        <td>${d.precio_producto ? d.precio_producto + ' €' : '<span class="text-muted fst-italic">Sin precio</span>'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless table-striped mb-0">
                                <tbody>
                                    <tr>
                                        <th class="ps-4"><i class="bi bi-card-text me-2"></i>Descripción</th>
                                        <td style="max-width:300px; word-wrap:break-word; white-space:pre-wrap;">
                                            ${d.descripcion_producto || '<span class="text-muted fst-italic">Sin descripción</span>'}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="ps-4"><i class="bi bi-calendar-plus me-2"></i>Creado el</th>
                                        <td>${d.created_at_producto ? formatoFechaEuropeo(d.created_at_producto) : '<span class="text-muted fst-italic">Sin fecha</span>'}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                <div class="card-footer bg-transparent border-top-0 text-end">
                    <small class="text-muted">Actualizado: ${d.updated_at_producto ? formatoFechaEuropeo(d.updated_at_producto) : 'Sin fecha'}</small>
                </div>
            </div>
        `;
    }

    // ── Event listener — NO MODIFICAR la lógica interna ────────
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

    // ── Filtros de pie de tabla ─────────────────────────────────
    $columnFilterInputs.on('keyup change', function () {
        var columnIndex  = table_e.column($(this).closest('th')).index();
        table_e.column(columnIndex).search($(this).val()).draw();
    });

}); // fin $(document).ready()

// FUERA del document.ready — función global de formateo de fechas
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return 'Sin fecha';
    try {
        const fecha   = new Date(fechaString);
        if (isNaN(fecha.getTime())) return 'Fecha inválida';
        const dia     = fecha.getDate().toString().padStart(2, '0');
        const mes     = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const año     = fecha.getFullYear();
        const horas   = fecha.getHours().toString().padStart(2, '0');
        const minutos = fecha.getMinutes().toString().padStart(2, '0');
        return `${dia}/${mes}/${año} ${horas}:${minutos}`;
    } catch (e) {
        return 'Error en fecha';
    }
}
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
        $data  = array();

        foreach ($datos as $row) {
            $data[] = array(
                // ── Columnas visibles en la tabla ──────────────────────
                "id_producto"         => $row["id_producto"],
                "referencia_producto" => $row["referencia_producto"],
                "nombre_producto"     => $row["nombre_producto"],
                "precio_producto"     => $row["precio_producto"],
                "activo_producto"     => $row["activo_producto"],

                // ── Campos para el child-row (no tienen columna visible) ─
                "descripcion_producto"  => $row["descripcion_producto"],
                "created_at_producto"   => $row["created_at_producto"],
                "updated_at_producto"   => $row["updated_at_producto"],
            );
        }

        $results = array(
            "draw"            => 1,
            "recordsTotal"    => count($data),
            "recordsFiltered" => count($data),
            "data"            => $data
        );

        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

### PHP — Modelo (`models/Producto.php`)

```php
<?php
require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Producto
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        try {
            $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        } catch (PDOException $e) {
            $this->registro->registrarActividad('system', 'Producto', '__construct',
                "Error zona horaria: " . $e->getMessage(), 'warning');
        }
    }

    public function get_productos()
    {
        try {
            // Usar vista SQL si hay JOINs complejos; tabla directa si es sencillo
            $sql = "SELECT * FROM producto WHERE activo_producto = 1 ORDER BY nombre_producto ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad('admin', 'Producto', 'get_productos',
                "Error: " . $e->getMessage(), 'error');
            return [];
        }
    }
}
?>
```

---

## Resumen del flujo completo

```
1. Vista carga → <table id="X_data"> con thead/tfoot — primera <th> siempre vacía
2. <head> incluye CSS global:
       td.details-control { background: url(details_open.png) }
       tr.shown td.details-control { background: url(details_close.png) }
3. DataTables inicializa con ajax GET → controller/entidad.php?op=listar
4. Controller llama al Modelo → Modelo consulta tabla o VISTA SQL
5. JSON devuelto → DataTables renderiza filas
6. Columna 0 (className: 'details-control') muestra el icono de expandir
7. Usuario hace clic en td.details-control:
   ├─ row.child.isShown() == true  → hide() + tr.removeClass('shown')  [cierra]
   └─ row.child.isShown() == false → row.child(format(row.data())).show()
                                     + tr.addClass('shown')            [abre]
8. format(d) genera HTML (card Bootstrap de 2 columnas) con d.campo
9. CSS tr.shown cambia automáticamente el icono PNG mediante el selector CSS
```

---

*Documentado el 25/02/2026 — Proyecto MDR ERP Manager — Basado en `/view/MntClientes/`*
