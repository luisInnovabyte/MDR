# Funciones JavaScript
## Sistema Cabecera-Pies - Lógica Cliente

> **Archivo:** `view/MntArticulos/mntarticulo.js`  
> **Sección:** Funciones CRUD y manejo de eventos

[← Volver al índice](./index_cabecera_pies.md)

---

## 📋 Tabla de Contenidos

1. [Estructura General](#estructura-general)
2. [Función Recargar Estadísticas](#función-recargar-estadísticas)
3. [Función Desactivar Artículo](#función-desactivar-artículo)
4. [Función Activar Artículo](#función-activar-artículo)
5. [Función Editar Artículo](#función-editar-artículo)
6. [Función Ver Elementos](#función-ver-elementos)
7. [Sistema de Filtros](#sistema-de-filtros)
8. [Funciones Auxiliares](#funciones-auxiliares)

---

## 1. Estructura General

### Document Ready

```javascript
$(document).ready(function () {
    // 1. Agregar estilos CSS dinámicos
    if (!document.getElementById("imagen-modal-styles")) {
        const style = document.createElement("style");
        style.id = "imagen-modal-styles";
        style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
            .group-row {
                background-color: #f8f9fa !important;
                font-weight: bold;
                cursor: pointer;
            }
            // ... más estilos
        `;
        document.head.appendChild(style);
    }
    
    // 2. Definir configuración de DataTables
    var datatable_articulosConfig = { /* ... */ };
    
    // 3. Definir variables
    var $table = $("#articulos_data");
    var table_e = $table.DataTable(datatable_articulosConfig);
    
    // 4. Configurar event handlers
    $(document).on("click", ".desacArticulo", function () { /* ... */ });
    $(document).on("click", ".activarArticulo", function () { /* ... */ });
    $(document).on("click", ".editarArticulo", function () { /* ... */ });
    
    // 5. Configurar filtros
    $columnFilterInputs.on("keyup change", function () { /* ... */ });
    
}); // Fin document.ready
```

### Orden de Inicialización

```
1. Estilos CSS dinámicos
   ↓
2. Configuración DataTables
   ↓
3. Variables y selectores
   ↓
4. Inicialización de DataTables
   ↓
5. Event handlers (delegación)
   ↓
6. Configuración de filtros
```

---

## 2. Función Recargar Estadísticas

### Código

```javascript
// Función para recargar estadísticas
function recargarEstadisticas() {
    $.ajax({
        url: "../../controller/articulo.php?op=estadisticas",
        type: "GET",
        dataType: "json",
        success: function(response) {
            if (response.success) {
                // Actualizar los valores en las tarjetas
                $(".card.border-primary h2").text(response.data.total);
                $(".card.border-success h2").text(response.data.activos);
                $(".card.border-info h2").text(response.data.kits);
                $(".card.border-warning h2").text(response.data.coeficientes);
            }
        },
        error: function(xhr, status, error) {
            console.error("Error al recargar estadísticas:", error);
        }
    });
}
```

### Características

1. **AJAX GET** al endpoint `articulo.php?op=estadisticas`
2. **Selectores específicos**: Usa clases de Bootstrap para identificar cards
3. **Actualización selectiva**: Solo actualiza los `<h2>` dentro de cada card
4. **Manejo de errores**: Log en consola si falla

### Formato de Respuesta Esperado

```json
{
    "success": true,
    "data": {
        "total": 150,
        "activos": 142,
        "kits": 25,
        "coeficientes": 118
    }
}
```

### Cuándo se Llama

```javascript
// Después de desactivar un artículo
$.post("...?op=eliminar", { id_articulo: id }, function () {
    $table.DataTable().ajax.reload();
    recargarEstadisticas(); // ← Aquí
    Swal.fire("Desactivado", "...", "success");
});

// Después de activar un artículo
$.post("...?op=activar", { id_articulo: id }, function () {
    $table.DataTable().ajax.reload();
    recargarEstadisticas(); // ← Aquí
    Swal.fire("Activado", "...", "success");
});
```

---

## 3. Función Desactivar Artículo

### Código Completo

```javascript
function desacArticulo(id) {
    Swal.fire({
        title: "Desactivar",
        text: `¿Desea desactivar el artículo con ID ${id}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Si",
        cancelButtonText: "No",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "../../controller/articulo.php?op=eliminar",
                { id_articulo: id },
                function (data) {
                    $table.DataTable().ajax.reload();
                    recargarEstadisticas();
                    
                    Swal.fire(
                        "Desactivado",
                        "El artículo ha sido desactivado",
                        "success"
                    );
                }
            );
        }
    });
}

// CAPTURAR EL CLICK EN EL BOTÓN DE BORRAR
$(document).on("click", ".desacArticulo", function (event) {
    event.preventDefault();
    let id = $(this).data("id_articulo");
    desacArticulo(id);
});
```

### Flujo

```
1. Usuario hace clic en botón con clase "desacArticulo"
   ↓
2. Event handler captura el evento
   ↓
3. Extrae el ID del data-attribute
   ↓
4. Llama a desacArticulo(id)
   ↓
5. SweetAlert2 muestra confirmación
   ↓
6. Si confirma:
   ├─ AJAX POST a articulo.php?op=eliminar
   ├─ Recarga DataTables
   ├─ Recarga estadísticas
   └─ Muestra mensaje de éxito
```

### Características SweetAlert2

```javascript
Swal.fire({
    title: "Desactivar",              // Título del modal
    text: "¿Desea desactivar...?",    // Mensaje
    icon: "question",                 // Icono: question, warning, error, success, info
    showCancelButton: true,           // Muestra botón cancelar
    confirmButtonText: "Si",          // Texto botón confirmar
    cancelButtonText: "No",           // Texto botón cancelar
    reverseButtons: true,             // Invierte orden de botones
})
```

### Event Delegation

```javascript
// ✅ CORRECTO: Event delegation
$(document).on("click", ".desacArticulo", function () {
    // Funciona incluso para elementos agregados dinámicamente
});

// ❌ INCORRECTO: Binding directo
$(".desacArticulo").on("click", function () {
    // No funciona para elementos cargados por AJAX
});
```

---

## 4. Función Activar Artículo

### Código

```javascript
function activarArticulo(id) {
    Swal.fire({
        title: "Activar",
        text: `¿Desea activar el artículo con ID ${id}?`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Si",
        cancelButtonText: "No",
        reverseButtons: true,
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "../../controller/articulo.php?op=activar",
                { id_articulo: id },
                function (data) {
                    $table.DataTable().ajax.reload();
                    recargarEstadisticas();
                    
                    Swal.fire("Activado", "El artículo ha sido activado", "success");
                }
            );
        }
    });
}

// CAPTURAR EL CLICK EN EL BOTÓN DE ACTIVAR
$(document).on("click", ".activarArticulo", function (event) {
    event.preventDefault();
    let id = $(this).data("id_articulo");
    activarArticulo(id);
});
```

### Diferencia con Desactivar

| Aspecto | Desactivar | Activar |
|---------|------------|---------|
| **Endpoint** | `?op=eliminar` | `?op=activar` |
| **Clase CSS** | `.desacArticulo` | `.activarArticulo` |
| **Icono botón** | `fa-trash` (rojo) | `bi-hand-thumbs-up-fill` (verde) |
| **Texto** | "Desactivar" | "Activar" |

---

## 5. Función Editar Artículo

### Código

```javascript
// CAPTURAR EL CLICK EN EL BOTÓN DE EDITAR
$(document).on("click", ".editarArticulo", function (event) {
    event.preventDefault();

    let id = $(this).data("id_articulo");
    console.log("id articulo:", id);

    // Redirigir al formulario independiente en modo edición
    window.location.href = `formularioArticulo.php?modo=editar&id=${id}`;
});
```

### Explicación

1. **No usa confirmación**: Redirige directamente
2. **window.location.href**: Navegación completa (no AJAX)
3. **Parámetros GET**: `?modo=editar&id=123`
4. **Formulario independiente**: No es modal, es página completa

### URL Generada

```
formularioArticulo.php?modo=editar&id=42
                       ↑             ↑
                       |             └─ ID del artículo
                       └─ Modo de operación
```

### En formularioArticulo.php

```php
<?php
$modo = $_GET['modo'] ?? 'nuevo';
$id = $_GET['id'] ?? null;

if ($modo === 'editar' && $id) {
    // Cargar datos del artículo
    $articulo = $articuloModel->get_articuloxid($id);
    
    // Prellenar formulario con datos
}
?>
```

---

## 6. Función Ver Elementos

### Código

```javascript
// CAPTURAR EL CLICK EN EL BOTÓN DE VER ELEMENTOS
$(document).on("click", ".verElementos", function (event) {
    event.preventDefault();

    let id_articulo = $(this).data("id_articulo");
    console.log("Ver elementos del artículo:", id_articulo);

    // Redirigir a la tabla de elementos filtrada por artículo
    window.location.href = `../MntElementos/index.php?id_articulo=${id_articulo}`;
});
```

### Explicación

1. **Navegación a otro módulo**: `../MntElementos/`
2. **Filtro automático**: Parámetro `?id_articulo=42`
3. **Tabla precargada**: MntElementos carga con filtro aplicado

### En MntElementos/index.php

```javascript
$(document).ready(function() {
    // Detectar parámetro GET
    const urlParams = new URLSearchParams(window.location.search);
    const idArticulo = urlParams.get('id_articulo');
    
    if (idArticulo) {
        // Aplicar filtro automáticamente
        table.column('id_articulo:name').search(idArticulo).draw();
        
        // Mostrar alerta de filtro activo
        $('#filter-alert').show();
        $('#active-filters-text').text(`Artículo: ${idArticulo}`);
    }
});
```

---

## 7. Sistema de Filtros

### Filtro por Columna

```javascript
// Filtro de cada columna en el pie de la tabla
$columnFilterInputs.on("keyup change", function () {
    var columnIndex = table_e.column($(this).closest("th")).index();
    var searchValue = $(this).val();

    table_e.column(columnIndex).search(searchValue).draw();

    updateFilterMessage();
});
```

### Explicación

1. **Selector**: Todos los inputs y selects del tfoot
2. **Eventos**: `keyup` (tecleo) y `change` (cambio de select)
3. **Índice de columna**: `.closest("th")` para obtener la columna
4. **Búsqueda**: `.search()` aplica el filtro
5. **Redibujo**: `.draw()` actualiza la tabla
6. **Actualización**: Llama a `updateFilterMessage()`

### Función Actualizar Mensaje de Filtro

```javascript
function updateFilterMessage() {
    var activeFilters = false;

    // Verificar inputs y selects con valor
    $columnFilterInputs.each(function () {
        if ($(this).val() !== "") {
            activeFilters = true;
            return false; // Break del loop
        }
    });

    // Verificar búsqueda global
    if (table_e.search() !== "") {
        activeFilters = true;
    }

    // Mostrar/ocultar alerta
    if (activeFilters) {
        $("#filter-alert").show();
    } else {
        $("#filter-alert").hide();
    }
}
```

### Listener de Búsqueda Global

```javascript
// Detectar cambios en búsqueda global
table_e.on("search.dt", function () {
    updateFilterMessage();
});
```

### Botón Limpiar Filtros

```javascript
$("#clear-filter").on("click", function () {
    // Destruir instancia actual
    table_e.destroy();

    // Limpiar valores de inputs y selects
    $columnFilterInputs.each(function () {
        $(this).val("");
    });

    // Reinicializar DataTables
    table_e = $table.DataTable($tableConfig);

    // Ocultar alerta
    $("#filter-alert").hide();
});
```

### Explicación del Proceso

```
1. Click en "Limpiar filtros"
   ↓
2. Destruir DataTables (.destroy())
   ↓
3. Vaciar todos los inputs/selects
   ↓
4. Reinicializar DataTables con config original
   ↓
5. Ocultar alerta de filtros activos
```

### ¿Por qué Destruir y Reinicializar?

**Problema**: `.search("").draw()` no limpia completamente los filtros internos

**Solución**: Destruir y recrear garantiza estado limpio

```javascript
// ❌ NO funciona completamente
table_e.search("").columns().search("").draw();

// ✅ FUNCIONA perfectamente
table_e.destroy();
table_e = $table.DataTable($tableConfig);
```

---

## 8. Funciones Auxiliares

### Mostrar Imagen Completa

```javascript
function mostrarImagenCompleta(rutaImagen, nombreArticulo) {
    Swal.fire({
        title: `Imagen de: ${nombreArticulo}`,
        html: `<img src="${rutaImagen}" 
                    alt="${nombreArticulo}" 
                    style="max-width: 100%; max-height: 80vh; border-radius: 8px;">`,
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            popup: "swal-wide",
        },
        background: "#fff",
        backdrop: "rgba(0,0,0,0.8)",
    });
}
```

**Uso**: Se llama desde el child row al hacer clic en una imagen

### Descargar Imagen

```javascript
function descargarImagen(rutaImagen, nombreArchivo) {
    const link = document.createElement("a");
    link.href = rutaImagen;
    link.download = nombreArchivo;
    link.target = "_blank";

    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);

    toastr.success("Descarga iniciada", "Imagen descargada", {
        timeOut: 2000,
        positionClass: "toast-bottom-right",
    });
}
```

**Características**:
1. Crea elemento `<a>` dinámicamente
2. Asigna atributo `download`
3. Simula click
4. Limpia el DOM
5. Muestra notificación Toastr

### Formato Fecha Europeo

```javascript
function formatoFechaEuropeo(fechaString) {
    if (!fechaString) return 'Sin fecha';
    
    try {
        const fecha = new Date(fechaString);
        if (isNaN(fecha.getTime())) return 'Fecha inválida';
        
        const dia = fecha.getDate().toString().padStart(2, '0');
        const mes = (fecha.getMonth() + 1).toString().padStart(2, '0');
        const año = fecha.getFullYear();
        
        return `${dia}/${mes}/${año}`;
    } catch (error) {
        console.error('Error al formatear fecha:', error);
        return 'Error en fecha';
    }
}
```

**Conversión**:
```
Input:  "2024-12-23 15:30:00"
Output: "23/12/2024"
```

**Manejo de errores**:
- Fecha null → "Sin fecha"
- Fecha inválida → "Fecha inválida"
- Error de parsing → "Error en fecha"

---

## 🎯 Patrón de Funciones CRUD

### Estructura Estándar

```javascript
// 1. Función de acción
function accionEntidad(id) {
    Swal.fire({
        title: "Título",
        text: `Mensaje con ${id}`,
        icon: "question",
        showCancelButton: true,
        confirmButtonText: "Sí",
        cancelButtonText: "No",
    }).then((result) => {
        if (result.isConfirmed) {
            $.post(
                "../../controller/entidad.php?op=operacion",
                { id_entidad: id },
                function (data) {
                    $table.DataTable().ajax.reload();
                    recargarEstadisticas();
                    Swal.fire("Éxito", "Mensaje de éxito", "success");
                }
            ).fail(function() {
                Swal.fire("Error", "No se pudo completar", "error");
            });
        }
    });
}

// 2. Event handler
$(document).on("click", ".claseBoton", function (event) {
    event.preventDefault();
    let id = $(this).data("id_entidad");
    accionEntidad(id);
});
```

---

## ✅ Checklist de Funciones

- [ ] Recargar estadísticas después de CRUD
- [ ] Confirmación antes de eliminar
- [ ] Event delegation para botones dinámicos
- [ ] Manejo de errores en AJAX
- [ ] Actualizar DataTables después de cambios
- [ ] Mostrar mensajes de éxito/error
- [ ] Validar datos antes de enviar
- [ ] Limpiar filtros correctamente (destroy + reinit)
- [ ] Funciones auxiliares para formateo
- [ ] Console.log para depuración

---

[← Anterior: DataTables](./index_cabecera_pies_datatables.md) | [Siguiente: Controller →](./index_cabecera_pies_controller.md)
