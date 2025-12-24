# Configuración de DataTables
## Sistema Cabecera-Pies - DataTables Avanzado

> **Archivo:** `view/MntArticulos/mntarticulo.js`  
> **Sección:** Configuración de DataTables

[← Volver al índice](./index_cabecera_pies.md)

---

## 📋 Tabla de Contenidos

1. [Configuración Básica](#configuración-básica)
2. [Definición de Columnas](#definición-de-columnas)
3. [ColumnDefs Detallado](#columndefs-detallado)
4. [AJAX y Origen de Datos](#ajax-y-origen-de-datos)
5. [RowGroup (Agrupación)](#rowgroup-agrupación)
6. [Child Rows (Detalles Expandibles)](#child-rows-detalles-expandibles)
7. [Inicialización](#inicialización)

---

## 1. Configuración Básica

### Objeto de Configuración

```javascript
var datatable_articulosConfig = {
    processing: true,
    
    // Layout de controles (paginación, búsqueda, etc.)
    layout: {
        bottomEnd: {
            paging: {
                firstLast: true,
                numbers: false,
                previousNext: true,
            },
        },
    },
    
    // Traducción de textos
    language: {
        paginate: {
            first: '<i class="bi bi-chevron-double-left"></i>',
            last: '<i class="bi bi-chevron-double-right"></i>',
            previous: '<i class="bi bi-chevron-compact-left"></i>',
            next: '<i class="bi bi-chevron-compact-right"></i>',
        },
    },
    
    // ... más configuración
};
```

### Propiedades Importantes

| Propiedad | Valor | Descripción |
|-----------|-------|-------------|
| `processing` | `true` | Muestra "Procesando..." durante operaciones |
| `layout.bottomEnd.paging` | objeto | Configuración de la paginación |
| `firstLast` | `true` | Muestra botones Primera/Última página |
| `numbers` | `false` | Oculta números de página |
| `previousNext` | `true` | Muestra botones Anterior/Siguiente |
| `language.paginate` | objeto | Iconos personalizados para paginación |

---

## 2. Definición de Columnas

### Array de Columnas

```javascript
columns: [
    // Columna 0: Control (Botón +)
    {
        name: "control",
        data: null,
        defaultContent: "",
        className: "details-control sorting_1 text-center",
    },
    
    // Columna 1: ID (Oculta)
    {
        name: "id_articulo",
        data: "id_articulo",
        visible: false,
        className: "text-center",
    },
    
    // Columna 2: Código
    {
        name: "codigo_articulo",
        data: "codigo_articulo",
        className: "text-center align-middle",
    },
    
    // Columna 3: Nombre
    {
        name: "nombre_articulo",
        data: "nombre_articulo",
        className: "text-center align-middle",
    },
    
    // Columna 4: Familia (Calculada)
    {
        name: "familia",
        data: null,
        className: "text-center align-middle",
    },
    
    // Columna 5: Precio
    {
        name: "precio_alquiler_articulo",
        data: "precio_alquiler_articulo",
        className: "text-center align-middle",
    },
    
    // Columna 6: Es Kit
    {
        name: "es_kit_articulo",
        data: "es_kit_articulo",
        className: "text-center align-middle",
    },
    
    // Columna 7: Coeficientes
    {
        name: "coeficiente_efectivo",
        data: "coeficiente_efectivo",
        className: "text-center align-middle",
    },
    
    // Columna 8: Estado
    {
        name: "activo_articulo",
        data: "activo_articulo",
        className: "text-center align-middle",
    },
    
    // Columna 9: Activar/Desactivar (Botones)
    {
        name: "activar",
        data: null,
        className: "text-center align-middle",
    },
    
    // Columna 10: Editar (Botón)
    {
        name: "editar",
        data: null,
        defaultContent: "",
        className: "text-center align-middle",
    },
    
    // Columna 11: Elementos (Conteo + Botón)
    {
        name: "elementos",
        data: null,
        defaultContent: "",
        className: "text-center align-middle",
    },
],
```

### Propiedades de Columnas

| Propiedad | Tipo | Descripción |
|-----------|------|-------------|
| `name` | string | Identificador único de la columna |
| `data` | string/null | Campo del JSON que mapea (`null` para calculadas) |
| `defaultContent` | string | Contenido si `data` es null o undefined |
| `className` | string | Clases CSS para celdas |
| `visible` | boolean | Mostrar/ocultar columna |

### Clases CSS Útiles

```
text-center       → Centrado horizontal
align-middle      → Centrado vertical
details-control   → Cursor pointer para expandir
sorting_1         → Estilo de ordenación activa
```

---

## 3. ColumnDefs Detallado

### Columna 0: Botón Control (+)

```javascript
{
    targets: "control:name",
    width: "3%",
    searchable: false,
    orderable: false,
    className: "text-center",
}
```

**Propósito**: Botón para expandir detalles (child row)

### Columna 2: Código Artículo

```javascript
{
    targets: "codigo_articulo:name",
    width: "10%",
    searchable: true,
    orderable: true,
    className: "text-center",
}
```

**Propósito**: Campo de texto simple, filtrable y ordenable

### Columna 4: Familia (Renderizado Complejo)

```javascript
{
    targets: "familia:name",
    width: "15%",
    searchable: true,
    orderable: true,
    className: "text-center",
    render: function (data, type, row) {
        if (type === "display" || type === "type") {
            const nombreFamilia = row.nombre_familia || "";
            const codigoFamilia = row.codigo_familia || "";
            
            if (nombreFamilia) {
                return `<span class="badge bg-info">${codigoFamilia}</span> 
                        <span class="text-muted">${nombreFamilia}</span>`;
            } else {
                return '<span class="text-muted fst-italic">Sin familia</span>';
            }
        }
        return row.nombre_familia || "";
    },
}
```

**Explicación**:
1. Verifica si es para visualización (`display` o `type`)
2. Obtiene campos relacionados (`nombre_familia`, `codigo_familia`)
3. Renderiza badge con código + nombre
4. Si no hay familia, muestra texto en itálica
5. Para ordenación/filtrado, retorna solo el nombre

### Columna 5: Precio (Formato Moneda)

```javascript
{
    targets: "precio_alquiler_articulo:name",
    width: "10%",
    orderable: true,
    searchable: true,
    className: "text-center",
    render: function (data, type, row) {
        if (type === "display") {
            return row.precio_alquiler_articulo > 0
                ? '<span class="badge bg-success">' + 
                  parseFloat(row.precio_alquiler_articulo).toFixed(2) + 
                  ' €</span>'
                : '<span class="badge bg-secondary">0.00 €</span>';
        }
        return row.precio_alquiler_articulo;
    },
}
```

**Características**:
- Badge verde si precio > 0
- Badge gris si precio = 0
- Formato con 2 decimales
- Símbolo de euro

### Columna 6: Es Kit (Icono)

```javascript
{
    targets: "es_kit_articulo:name",
    width: "8%",
    orderable: true,
    searchable: true,
    className: "text-center",
    render: function (data, type, row) {
        if (type === "display") {
            return row.es_kit_articulo == 1
                ? '<i class="bi bi-box-seam text-primary fa-2x" title="Es un kit"></i>'
                : '<i class="bi bi-box text-muted fa-2x" title="Artículo individual"></i>';
        }
        return row.es_kit_articulo;
    },
}
```

**Lógica**:
- Si `es_kit_articulo == 1`: Icono azul de caja sellada
- Si no: Icono gris de caja simple
- Tooltip descriptivo con `title`

### Columna 7: Coeficientes (Icono)

```javascript
{
    targets: "coeficiente_efectivo:name",
    width: "8%",
    orderable: true,
    searchable: true,
    className: "text-center",
    render: function (data, type, row) {
        if (type === "display") {
            return row.coeficiente_efectivo == 1
                ? '<i class="bi bi-percent text-success fa-2x" title="Permite coeficientes"></i>'
                : '<i class="bi bi-slash-circle text-danger fa-2x" title="No permite coeficientes"></i>';
        }
        return row.coeficiente_efectivo;
    },
}
```

**Lógica**:
- Si permite: Icono % verde
- Si no permite: Icono círculo tachado rojo

### Columna 8: Estado (Icono)

```javascript
{
    targets: "activo_articulo:name",
    width: "8%",
    orderable: true,
    searchable: true,
    className: "text-center",
    render: function (data, type, row) {
        if (type === "display") {
            return row.activo_articulo == 1
                ? '<i class="bi bi-check-circle text-success fa-2x"></i>'
                : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
        }
        return row.activo_articulo;
    },
}
```

**Lógica**:
- Activo (1): Check verde
- Inactivo (0): X roja

### Columna 9: Botones Activar/Desactivar

```javascript
{
    targets: "activar:name",
    width: "8%",
    searchable: false,
    orderable: false,
    class: "text-center",
    render: function (data, type, row) {
        if (row.activo_articulo == 1) {
            return `<button type="button" 
                           class="btn btn-danger btn-sm desacArticulo" 
                           data-bs-toggle="tooltip-primary" 
                           data-placement="top" 
                           title="Desactivar" 
                           data-id_articulo="${row.id_articulo}"> 
                        <i class="fa-solid fa-trash"></i>
                    </button>`;
        } else {
            return `<button class="btn btn-success btn-sm activarArticulo" 
                           data-bs-toggle="tooltip-primary" 
                           data-placement="top" 
                           title="Activar" 
                           data-id_articulo="${row.id_articulo}">
                        <i class="bi bi-hand-thumbs-up-fill"></i>
                    </button>`;
        }
    },
}
```

**Características**:
- **Botón condicional**: Rojo (desactivar) o Verde (activar)
- **Data attribute**: `data-id_articulo` para capturar el ID
- **Tooltip**: Descripción al pasar el mouse
- **Clases específicas**: `desacArticulo` o `activarArticulo` para eventos

### Columna 10: Botón Editar

```javascript
{
    targets: "editar:name",
    width: "8%",
    searchable: false,
    orderable: false,
    class: "text-center",
    render: function (data, type, row) {
        return `<button type="button" 
                       class="btn btn-info btn-sm editarArticulo" 
                       data-toggle="tooltip-primary" 
                       data-placement="top" 
                       title="Editar"  
                       data-id_articulo="${row.id_articulo}"> 
                    <i class="fa-solid fa-edit"></i>
                </button>`;
    },
}
```

**Siempre visible**, botón info (azul) con icono de editar

### Columna 11: Elementos (Badge + Botón)

```javascript
{
    targets: "elementos:name",
    width: "8%",
    searchable: false,
    orderable: true,
    class: "text-center",
    render: function (data, type, row) {
        if (type === "display") {
            const totalElementos = row.total_elementos || 0;
            const badgeClass = totalElementos > 0 ? 'bg-success' : 'bg-secondary';
            
            return `<div class="d-flex align-items-center justify-content-center gap-2">
                      <span class="badge ${badgeClass} fs-6">${totalElementos}</span>
                      <button type="button" 
                             class="btn btn-warning btn-sm verElementos" 
                             data-toggle="tooltip-primary" 
                             data-placement="top" 
                             title="Ver Elementos"  
                             data-id_articulo="${row.id_articulo}"> 
                          <i class="bi bi-list-ul"></i>
                      </button>
                    </div>`;
        }
        return row.total_elementos || 0;
    },
}
```

**Componentes**:
1. **Badge**: Muestra conteo (verde si > 0, gris si = 0)
2. **Botón**: Para ver listado de elementos
3. **Flexbox**: Para alinear badge y botón

---

## 4. AJAX y Origen de Datos

### Configuración AJAX

```javascript
ajax: {
    url: "../../controller/articulo.php?op=listar",
    type: "GET",
    dataSrc: function (json) {
        console.log("JSON recibido:", json);
        return json.data || json;
    },
}
```

### Explicación

1. **URL**: Endpoint del controller con parámetro `?op=listar`
2. **Type**: Método HTTP GET
3. **dataSrc**: Función para extraer los datos del JSON
   - Si el JSON tiene estructura `{data: [...]}`, usa `json.data`
   - Si el JSON es directamente un array `[...]`, usa `json`

### Formato de Respuesta Esperado

```json
{
    "draw": 1,
    "recordsTotal": 150,
    "recordsFiltered": 150,
    "data": [
        {
            "id_articulo": 1,
            "codigo_articulo": "MIC-SM58",
            "nombre_articulo": "Micrófono Shure SM58",
            "nombre_familia": "Microfonía",
            "codigo_familia": "MIC",
            "precio_alquiler_articulo": "25.00",
            "es_kit_articulo": 0,
            "coeficiente_efectivo": 1,
            "activo_articulo": 1,
            "total_elementos": 5
        },
        // ... más registros
    ]
}
```

---

## 5. RowGroup (Agrupación)

### Configuración

```javascript
order: [[4, 'asc']], // Ordenar por familia (columna 4)

rowGroup: {
    dataSrc: 'nombre_familia',
    startRender: function (rows, group) {
        // Obtener información del grupo
        var familiaData = rows.data()[0];
        var codigoFamilia = familiaData.codigo_familia || 'Sin código';
        var nombreFamilia = group || 'Sin familia';
        var count = rows.count();
        
        return $('<tr/>')
            .addClass('group-row bg-light')
            .append('<td colspan="12" class="text-start fw-bold text-primary">' +
                '<i class="bi bi-folder-fill me-2"></i>' +
                '<span class="badge bg-primary me-2">' + codigoFamilia + '</span>' +
                nombreFamilia + 
                ' <span class="badge bg-secondary ms-2">' + count + ' artículo(s)</span>' +
                '</td>');
    }
}
```

### Explicación

1. **dataSrc**: Campo por el que se agrupa (`nombre_familia`)
2. **order**: Ordenación inicial por familia para que la agrupación funcione
3. **startRender**: Función que genera la fila de grupo
   - Accede a datos del primer registro del grupo
   - Extrae código y nombre de familia
   - Cuenta registros del grupo
   - Retorna fila HTML personalizada

### Resultado Visual

```
┌────────────────────────────────────────────────────┐
│ 📁 [MIC] Microfonía (12 artículo(s))              │ ← Fila de grupo
├────────────────────────────────────────────────────┤
│ MIC-SM58    Micrófono Shure SM58      25.00€      │
│ MIC-BETA87  Micrófono Shure Beta 87   30.00€      │
│ ...                                                 │
├────────────────────────────────────────────────────┤
│ 📁 [SPK] Altavoces (8 artículo(s))                │ ← Fila de grupo
├────────────────────────────────────────────────────┤
│ SPK-K12     Altavoz QSC K12            50.00€      │
│ ...                                                 │
└────────────────────────────────────────────────────┘
```

### CSS para Grupos

```css
.group-row {
    background-color: #f8f9fa !important;
    font-weight: bold;
    font-size: 1.1em;
    cursor: pointer;
}

.group-row:hover {
    background-color: #e9ecef !important;
}

.group-row td {
    padding: 12px 8px !important;
    border-bottom: 2px solid #0d6efd !important;
}
```

---

## 6. Child Rows (Detalles Expandibles)

### Función format()

```javascript
function format(d) {
    return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-box-seam fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles del Artículo</h5>
                </div>
            </div>
            <div class="card-body p-0">
                <table class="table table-borderless table-striped table-hover mb-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="ps-4 w-25">
                                <i class="bi bi-hash me-2"></i>Id Artículo
                            </th>
                            <td class="pe-4">
                                ${d.id_articulo || '<span class="text-muted fst-italic">No disponible</span>'}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 w-25">
                                <i class="bi bi-tags me-2"></i>Nombre Artículo
                            </th>
                            <td class="pe-4">
                                ${d.nombre_articulo || '<span class="text-muted fst-italic">No disponible</span>'}
                            </td>
                        </tr>
                        <!-- Más filas... -->
                    </tbody>
                </table>
            </div>
        </div>
    `;
}
```

### Manejo del Click

```javascript
$tableBody.on("click", "td.details-control", function () {
    var tr = $(this).closest("tr");
    var row = table_e.row(tr);

    if (row.child.isShown()) {
        // Cerrar si ya está abierto
        row.child.hide();
        tr.removeClass("shown");
    } else {
        // Abrir y mostrar detalles
        row.child(format(row.data())).show();
        tr.addClass("shown");
    }
});
```

### Explicación

1. **Event delegation**: Click en `td.details-control`
2. **Toggle de visibilidad**: Abre/cierra con `isShown()`
3. **Generación de HTML**: Llama a `format()` con datos de la fila
4. **Clase "shown"**: Para cambiar el icono del botón + a -

---

## 7. Inicialización

### Variables y Definiciones

```javascript
var $table = $("#articulos_data");
var $tableConfig = datatable_articulosConfig;
var $tableBody = $("#articulos_data tbody");
var $columnFilterInputs = $("#articulos_data tfoot input, #articulos_data tfoot select");

// Inicializar DataTables
var table_e = $table.DataTable($tableConfig);
```

### Explicación

1. **$table**: Selector jQuery de la tabla
2. **$tableConfig**: Objeto de configuración
3. **$tableBody**: Body para delegación de eventos
4. **$columnFilterInputs**: Inputs del footer para filtros
5. **table_e**: Instancia de DataTables para manipulación

---

## 🎯 Resumen de Características

### ✅ Funcionalidades Implementadas

- [x] **Agrupación por familia** con rowGroup
- [x] **Detalles expandibles** con child rows
- [x] **Renderizado condicional** de columnas
- [x] **Iconos de estado** para visualización rápida
- [x] **Badges de color** para precios y contadores
- [x] **Botones de acción** contextuales
- [x] **Filtros en pies** con inputs y selects
- [x] **AJAX** para carga de datos
- [x] **Responsive** y adaptable
- [x] **Tooltips** en botones

---

[← Anterior: Estructura](./index_cabecera_pies_estructura.md) | [Siguiente: Funciones JS →](./index_cabecera_pies_js_funciones.md)
