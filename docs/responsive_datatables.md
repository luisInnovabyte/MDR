# Guía de Implementación: DataTables Responsive

Esta guía documenta cómo implementar el modo responsive en DataTables para evitar el scroll horizontal y adaptar las tablas a diferentes tamaños de pantalla.

## 📋 Tabla de Contenidos

1. [Librerías Requeridas](#librerías-requeridas)
2. [Configuración Básica](#configuración-básica)
3. [Configuración con Función Personalizada](#configuración-con-función-personalizada)
4. [Prioridades Responsive](#prioridades-responsive)
5. [Estilos CSS](#estilos-css)
6. [Ejemplo Completo](#ejemplo-completo)

---

## 🔧 Librerías Requeridas

### 1. CSS (en `mainHead.php`)

Añade la librería CSS de DataTables Responsive:

```html
<!-- DataTables Responsive CSS -->
<link href="https://cdn.datatables.net/responsive/3.0.4/css/responsive.dataTables.min.css" rel="stylesheet">
```

O la versión combinada:

```html
<!-- DataTables con Responsive CSS (versión combinada) -->
<link href="https://cdn.datatables.net/v/dt/dt-2.2.2/r-3.0.4/datatables.min.css" rel="stylesheet">
```

### 2. JavaScript (en `mainJs.php`)

Añade los scripts de DataTables Responsive DESPUÉS del script principal de DataTables:

```html
<!-- DataTables principal -->
<script src="https://cdn.datatables.net/2.2.2/js/dataTables.min.js"></script>

<!-- DataTables Responsive JS -->
<script src="https://cdn.datatables.net/responsive/3.0.4/js/dataTables.responsive.min.js"></script>
<script src="https://cdn.datatables.net/responsive/3.0.4/js/responsive.dataTables.min.js"></script>
```

---

## ⚙️ Configuración Básica

### Configuración Mínima

```javascript
var datatableConfig = {
    processing: true,
    responsive: {
        details: {
            type: 'column',  // Tipo de expansión por columna
            target: 0         // Columna objetivo (normalmente la primera)
        }
    },
    columns: [
        { data: null, defaultContent: '', className: 'control' }, // Columna de control
        { data: 'campo1' },
        { data: 'campo2' },
        // ... más columnas
    ],
    columnDefs: [
        {
            targets: 0,
            className: 'control',
            orderable: false,
            searchable: false,
            responsivePriority: 1
        }
        // ... más definiciones
    ]
};
```

### HTML de la Tabla

```html
<table id="mi_tabla" class="table display responsive nowrap">
    <thead>
        <tr>
            <th></th>  <!-- Columna de control -->
            <th>Campo 1</th>
            <th>Campo 2</th>
            <!-- ... más columnas -->
        </tr>
    </thead>
    <tbody></tbody>
    <tfoot>
        <tr>
            <th></th>  <!-- Sin filtro en columna de control -->
            <th><input type="text" placeholder="Buscar..." /></th>
            <th><input type="text" placeholder="Buscar..." /></th>
            <!-- ... más filtros -->
        </tr>
    </tfoot>
</table>
```

---

## 🎨 Configuración con Función Personalizada

Si ya tienes una función `format(d)` personalizada para mostrar detalles, puedes integrarla:

```javascript
var datatableConfig = {
    processing: true,
    responsive: {
        details: {
            type: 'column',
            target: 0,
            renderer: function (api, rowIdx, columns) {
                // Obtener los datos de la fila
                var data = api.row(rowIdx).data();
                
                // Usar tu función format personalizada
                return format(data);
            }
        }
    },
    // ... resto de configuración
};

// Tu función personalizada para mostrar detalles
function format(d) {
    return `
        <div class="card border-primary mb-3">
            <div class="card-header bg-primary text-white">
                <h5 class="card-title mb-0">Detalles del Registro</h5>
            </div>
            <div class="card-body">
                <table class="table table-borderless">
                    <tbody>
                        <tr>
                            <th>Campo 1:</th>
                            <td>${d.campo1 || 'N/A'}</td>
                        </tr>
                        <tr>
                            <th>Campo 2:</th>
                            <td>${d.campo2 || 'N/A'}</td>
                        </tr>
                        <!-- Más campos -->
                    </tbody>
                </table>
            </div>
        </div>
    `;
}
```

---

## 📊 Prioridades Responsive

Las prioridades determinan qué columnas permanecen visibles cuando se reduce el espacio:

- **Prioridad 1**: Siempre visible (columnas más importantes)
- **Prioridad 2-6**: Visible según espacio disponible
- **Sin prioridad**: Se oculta primero

### Ejemplo de Prioridades

```javascript
columnDefs: [
    // Control responsive - siempre visible
    { 
        targets: 0,
        className: 'control',
        orderable: false,
        responsivePriority: 1
    },
    // Campo principal - alta prioridad
    { 
        targets: 1,
        responsivePriority: 2
    },
    // Campo importante
    { 
        targets: 2,
        responsivePriority: 3
    },
    // Botones de acción - deben permanecer visibles
    { 
        targets: -1,  // Última columna
        orderable: false,
        responsivePriority: 4
    },
    // Columnas secundarias sin prioridad
    { 
        targets: [3, 4, 5],
        // Se ocultarán primero cuando no haya espacio
    }
]
```

---

## 🎨 Estilos CSS

Añade estos estilos en tu archivo `index.php` o en un CSS global:

```css
/* Evitar scroll horizontal */
.dataTables_wrapper {
    overflow-x: hidden !important;
}

.table-wrapper {
    overflow-x: hidden !important;
}

/* Estilos para el botón de control responsive */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before {
    background-color: #0168fa;  /* Azul cuando está colapsado */
}

table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control:before,
table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control:before {
    background-color: #d33333;  /* Rojo cuando está expandido */
}

/* Opcional: Personalizar el icono */
table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before {
    content: '+';
    font-size: 18px;
    line-height: 18px;
}

table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control:before {
    content: '-';
}
```

---

## 📝 Ejemplo Completo

### Archivo: `mntEjemplo.js`

```javascript
$(document).ready(function () {

    var datatable_config = {
        processing: true,
        responsive: {
            details: {
                type: 'column',
                target: 0,
                renderer: function (api, rowIdx, columns) {
                    var data = api.row(rowIdx).data();
                    return format(data);
                }
            }
        },
        columns: [
            { name: 'control', data: null, defaultContent: '' },
            { name: 'id', data: 'id', visible: false },
            { name: 'codigo', data: 'codigo' },
            { name: 'nombre', data: 'nombre' },
            { name: 'descripcion', data: 'descripcion' },
            { name: 'fecha', data: 'fecha' },
            { name: 'estado', data: 'estado' },
            { name: 'activo', data: 'activo' },
            { name: 'acciones', data: null }
        ],
        columnDefs: [
            // Columna 0: Control responsive
            { 
                targets: "control:name",
                className: "control text-center",
                orderable: false,
                searchable: false,
                responsivePriority: 1
            },
            // Columna 2: Código
            { 
                targets: "codigo:name",
                responsivePriority: 2
            },
            // Columna 3: Nombre
            { 
                targets: "nombre:name",
                responsivePriority: 3
            },
            // Columna 6: Fecha con formato
            { 
                targets: "fecha:name",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.fecha ? formatoFechaEuropeo(row.fecha) : '-';
                    }
                    return row.fecha;
                }
            },
            // Columna 7: Estado con icono
            {
                targets: "activo:name",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo == 1 
                            ? '<i class="bi bi-check-circle text-success fa-2x"></i>' 
                            : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo;
                }
            },
            // Columna 8: Botones de acción
            {   
                targets: "acciones:name",
                orderable: false,
                searchable: false,
                responsivePriority: 4,
                render: function (data, type, row) {
                    return `
                        <button class="btn btn-info btn-sm editarBtn" data-id="${row.id}">
                            <i class="fa-solid fa-edit"></i>
                        </button>
                        <button class="btn btn-danger btn-sm eliminarBtn" data-id="${row.id}">
                            <i class="fa-solid fa-trash"></i>
                        </button>
                    `;
                }
            }
        ],
        ajax: {
            url: '../../controller/ejemplo.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                return json.data || json;
            }
        }
    };

    var table = $('#ejemplo_data').DataTable(datatable_config);

    // Función para mostrar detalles expandidos
    function format(d) {
        return `
            <div class="card border-primary mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">Detalles del Registro</h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-striped">
                        <tbody>
                            <tr>
                                <th>ID:</th>
                                <td>${d.id}</td>
                            </tr>
                            <tr>
                                <th>Código:</th>
                                <td>${d.codigo || 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Descripción:</th>
                                <td>${d.descripcion || 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Observaciones:</th>
                                <td>${d.observaciones || 'N/A'}</td>
                            </tr>
                            <tr>
                                <th>Creado:</th>
                                <td>${d.created_at ? formatoFechaEuropeo(d.created_at) : 'N/A'}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        `;
    }

});

// Función auxiliar para formatear fechas
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return 'N/A';
    try {
        const fecha = new Date(fechaString);
        const dia = fecha.getDate().toString().padStart(2, '0');
        const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const año = fecha.getFullYear();
        return `${dia}/${mes}/${año}`;
    } catch (error) {
        return 'Fecha inválida';
    }
}
```

### Archivo: `index.php`

```php
<!DOCTYPE html>
<html lang="es">
<head>
    <?php include_once('../../config/template/mainHead.php') ?>
    <style>
        /* Evitar scroll horizontal */
        .dataTables_wrapper {
            overflow-x: hidden !important;
        }
        
        .table-wrapper {
            overflow-x: hidden !important;
        }
        
        /* Estilos para el modo responsive */
        table.dataTable.dtr-inline.collapsed > tbody > tr > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr > th.dtr-control:before {
            background-color: #0168fa;
        }
        
        table.dataTable.dtr-inline.collapsed > tbody > tr.parent > td.dtr-control:before,
        table.dataTable.dtr-inline.collapsed > tbody > tr.parent > th.dtr-control:before {
            background-color: #d33333;
        }
    </style>
</head>
<body>
    <div class="br-mainpanel">
        <div class="br-pagebody">
            <div class="br-section-wrapper">
                <div class="table-wrapper">
                    <table id="ejemplo_data" class="table display responsive nowrap">
                        <thead>
                            <tr>
                                <th></th>  <!-- Control -->
                                <th>ID</th>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Fecha</th>
                                <th>Estado</th>
                                <th>Activo</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                        <tfoot>
                            <tr>
                                <th></th>
                                <th class="d-none"><input type="text" /></th>
                                <th><input type="text" placeholder="Buscar..." /></th>
                                <th><input type="text" placeholder="Buscar..." /></th>
                                <th><input type="text" placeholder="Buscar..." /></th>
                                <th><input type="text" placeholder="Buscar..." /></th>
                                <th><input type="text" placeholder="Buscar..." /></th>
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
                </div>
            </div>
        </div>
    </div>

    <?php include_once('../../config/template/mainJs.php') ?>
    <script type="text/javascript" src="mntEjemplo.js"></script>
</body>
</html>
```

---

## ✅ Checklist de Implementación

- [ ] Verificar que las librerías CSS de Responsive están en `mainHead.php`
- [ ] Verificar que las librerías JS de Responsive están en `mainJs.php` (DESPUÉS de DataTables principal)
- [ ] Añadir `responsive: { details: { type: 'column', target: 0 } }` en la configuración
- [ ] Crear columna de control en posición 0
- [ ] Configurar `className: 'control'` en columnDefs para la columna 0
- [ ] Establecer `responsivePriority` para columnas importantes
- [ ] Si tienes función `format(d)`, añadir `renderer` en la configuración responsive
- [ ] Añadir estilos CSS para evitar scroll horizontal
- [ ] Añadir columna vacía `<th></th>` en thead y tfoot del HTML
- [ ] Comentar o eliminar event handlers manuales de `details-control`

---

## 🐛 Troubleshooting

### El botón "+" no aparece
- Verifica que las librerías JS de Responsive están cargadas
- Asegúrate de que la columna 0 tiene `className: 'control'`
- Revisa que `target: 0` esté configurado correctamente

### Las columnas no se ocultan
- Verifica que el HTML tiene `class="table display responsive nowrap"`
- Asegúrate de que no hay `width: 100%` forzado en CSS
- Revisa que `overflow-x: hidden` esté aplicado

### La función format(d) no se ejecuta
- Verifica que el `renderer` esté configurado en `responsive.details`
- Asegúrate de que la función `format` está definida antes de usar DataTables
- Revisa la consola del navegador para errores JavaScript

### Scroll horizontal sigue apareciendo
- Añade `overflow-x: hidden !important` en `.dataTables_wrapper` y `.table-wrapper`
- Verifica que no hay anchos fijos en las columnas que sumen más del 100%
- Elimina la propiedad `scrollX: true` si existe en la configuración

---

## 📚 Referencias

- [DataTables Responsive Documentation](https://datatables.net/extensions/responsive/)
- [DataTables Responsive Examples](https://datatables.net/extensions/responsive/examples/)
- [Column Priorities](https://datatables.net/extensions/responsive/priority)

---

**Fecha de última actualización:** 11 de diciembre de 2025
**Versión de DataTables:** 2.2.2
**Versión de Responsive:** 3.0.4
