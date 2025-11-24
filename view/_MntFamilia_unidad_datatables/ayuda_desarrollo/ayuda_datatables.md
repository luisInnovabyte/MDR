# Guía para Implementar DataTable de Búsqueda en Formularios

Esta guía detalla cómo implementar un modal con DataTable para la búsqueda y selección de registros relacionados en formularios, reemplazando los campos `<select>` tradicionales.

## 📋 Tabla de Contenidos

1. [Introducción](#introducción)
2. [Estructura de Archivos](#estructura-de-archivos)
3. [Implementación Paso a Paso](#implementación-paso-a-paso)
4. [Código de Ejemplo](#código-de-ejemplo)
5. [Lista de Tareas para Replicar](#lista-de-tareas-para-replicar)

---

## 🎯 Introducción

### Ventajas del Modal con DataTable:
- ✅ **Escalabilidad**: Funciona con miles de registros
- ✅ **Búsqueda potente**: Filtros en todas las columnas
- ✅ **Información completa**: Muestra todos los campos relevantes
- ✅ **Mejor UX**: Interfaz moderna y funcional
- ✅ **Rendimiento**: Carga datos bajo demanda
- ✅ **Responsive**: Funciona en móviles y tablets

### Cuándo usar:
- Tablas con más de 50-100 registros
- Necesidad de búsqueda avanzada
- Mostrar información adicional en la selección
- Mejorar la experiencia de usuario

---

## 📂 Estructura de Archivos

```
view/MiModulo/
├── formulario.php          # Archivo PHP principal
├── formulario.js          # JavaScript del formulario
├── index.php             # Listado principal (opcional)
└── ayuda_datatables.md   # Esta guía

controller/
└── entidad_relacionada.php   # Controlador con nuevos endpoints

models/
└── EntidadRelacionada.php     # Modelo con nuevos métodos
```

---

## 🛠️ Implementación Paso a Paso

### 1. Modificar el Campo en el HTML (formulario.php)

#### Antes (Select tradicional):
```html
<select class="form-control" name="id_entidad" id="id_entidad">
    <option value="">Seleccionar...</option>
    <!-- Opciones cargadas dinámicamente -->
</select>
```

#### Después (Campo con modal):
```html
<div class="input-group">
    <input type="text" 
           class="form-control" 
           id="entidad_display" 
           placeholder="Seleccionar entidad..."
           readonly>
    <input type="hidden" name="id_entidad" id="id_entidad">
    <button type="button" 
            class="btn btn-outline-secondary" 
            data-bs-toggle="modal" 
            data-bs-target="#modalBuscarEntidad">
        <i class="fas fa-search"></i>
    </button>
    <button type="button" 
            class="btn btn-outline-danger" 
            id="limpiarEntidad"
            title="Limpiar selección">
        <i class="fas fa-times"></i>
    </button>
</div>
```

### 2. Agregar el Modal al HTML

```html
<!-- Modal de búsqueda -->
<div class="modal fade" id="modalBuscarEntidad" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-search me-2"></i>Seleccionar Entidad
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table id="tablaEntidadesBusqueda" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
```

### 3. Implementar JavaScript (formulario.js)

```javascript
// Variable para DataTable
var tablaEntidades;

// Inicializar modal
function inicializarModalEntidades() {
    $('#modalBuscarEntidad').on('shown.bs.modal', function () {
        if (!$.fn.DataTable.isDataTable('#tablaEntidadesBusqueda')) {
            tablaEntidades = $('#tablaEntidadesBusqueda').DataTable({
                "ajax": {
                    "url": "../../controller/entidad.php?op=listarTodasParaModal",
                    "type": "GET",
                    "dataSrc": ""
                },
                "columns": [
                    { "data": "id_entidad" },
                    { "data": "nombre_entidad" },
                    { "data": "descripcion_entidad" },
                    { 
                        "data": "activo_entidad",
                        "render": function(data) {
                            return data == 1 ? 
                                '<span class="badge bg-success">Activo</span>' : 
                                '<span class="badge bg-danger">Inactivo</span>';
                        }
                    },
                    {
                        "data": null,
                        "orderable": false,
                        "render": function(data, type, row) {
                            return '<button class="btn btn-sm btn-primary seleccionar-entidad" ' +
                                   'data-id="' + row.id_entidad + '" ' +
                                   'data-nombre="' + row.nombre_entidad + '">' +
                                   '<i class="fas fa-check me-1"></i>Seleccionar</button>';
                        }
                    }
                ],
                "language": {
                    "lengthMenu": "Mostrar _MENU_ registros por página",
                    "zeroRecords": "No se encontraron registros",
                    "info": "Página _PAGE_ de _PAGES_",
                    "search": "Buscar:",
                    "paginate": {
                        "next": "Siguiente",
                        "previous": "Anterior"
                    }
                }
            });
        }
    });

    // Limpiar al cerrar
    $('#modalBuscarEntidad').on('hidden.bs.modal', function () {
        if (tablaEntidades) {
            tablaEntidades.destroy();
            tablaEntidades = null;
        }
    });
}

// Manejar selección
$(document).on('click', '.seleccionar-entidad', function() {
    var id = $(this).data('id');
    var nombre = $(this).data('nombre');
    
    $('#id_entidad').val(id);
    $('#entidad_display').val(nombre);
    
    $('#modalBuscarEntidad').modal('hide');
    toastr.success('Entidad seleccionada: ' + nombre);
});

// Manejar limpiar
$('#limpiarEntidad').on('click', function() {
    $('#id_entidad').val('');
    $('#entidad_display').val('');
    toastr.info('Selección eliminada');
});
```

### 4. Agregar Endpoints al Controlador

```php
// En controller/entidad.php
case "listarTodasParaModal":
    $datos = $entidad->get_todas_entidades_para_modal();
    header('Content-Type: application/json');
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    break;
    
case "obtenerEntidadPorId":
    header('Content-Type: application/json; charset=utf-8');
    $datos = $entidad->get_entidadxid($_GET["id_entidad"]);
    echo json_encode($datos, JSON_UNESCAPED_UNICODE);
    break;
```

### 5. Agregar Métodos al Modelo

```php
// En models/Entidad.php
public function get_todas_entidades_para_modal()
{
    try {
        $sql = "SELECT id_entidad, nombre_entidad, descripcion_entidad, activo_entidad 
                FROM entidad 
                ORDER BY nombre_entidad ASC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return false;
    }
}
```

### 6. Agregar Estilos CSS

```css
/* Modal personalizado */
#modalBuscarEntidad .modal-lg {
    max-width: 900px;
}

#tablaEntidadesBusqueda {
    font-size: 0.9rem;
}

#tablaEntidadesBusqueda th {
    background-color: #f8f9fa;
    font-weight: 600;
}

.seleccionar-entidad {
    font-size: 0.8rem;
    padding: 0.25rem 0.5rem;
}

@media (max-width: 768px) {
    #modalBuscarEntidad .modal-lg {
        max-width: 95%;
    }
}
```

---

## 🔧 Código de Ejemplo Completo

### Ejemplo basado en Unidades de Medida:

#### HTML del Campo:
```html
<div class="col-12 col-md-6">
    <label for="id_unidad_familia" class="form-label">Unidad de medida:</label>
    <div class="input-group">
        <input type="text" 
               class="form-control" 
               id="unidad_medida_display" 
               placeholder="Seleccionar unidad de medida..."
               readonly>
        <input type="hidden" name="id_unidad_familia" id="id_unidad_familia">
        <button type="button" 
                class="btn btn-outline-secondary" 
                data-bs-toggle="modal" 
                data-bs-target="#modalBuscarUnidad">
            <i class="fas fa-search"></i>
        </button>
        <button type="button" 
                class="btn btn-outline-danger" 
                id="limpiarUnidad"
                title="Limpiar selección">
            <i class="fas fa-times"></i>
        </button>
    </div>
</div>
```

#### Modal HTML:
```html
<div class="modal fade" id="modalBuscarUnidad" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-search me-2"></i>Seleccionar Unidad de Medida
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <table id="tablaUnidadesBusqueda" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Símbolo</th>
                            <th>Descripción</th>
                            <th>Estado</th>
                            <th>Acción</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>
```

---

## ✅ Lista de Tareas para Replicar en Otros Formularios

### 📝 **Checklist de Implementación:**

#### **1. Preparación Inicial:**
- [ ] Identificar el campo `<select>` a reemplazar
- [ ] Definir qué columnas mostrar en la tabla
- [ ] Verificar que existe el modelo correspondiente
- [ ] Revisar el controlador existente

#### **2. Modificaciones en el HTML (formulario.php):**
- [ ] Reemplazar `<select>` por `<input>` con botones
- [ ] Agregar campo hidden para el ID
- [ ] Crear el modal con la tabla DataTable
- [ ] Agregar estilos CSS específicos
- [ ] Actualizar la ayuda/documentación del formulario

#### **3. Modificaciones en JavaScript (formulario.js):**
- [ ] Agregar variable para almacenar la instancia DataTable
- [ ] Implementar función `inicializarModalEntidades()`
- [ ] Configurar DataTable con columnas apropiadas
- [ ] Implementar manejador de selección (`.seleccionar-entidad`)
- [ ] Implementar manejador de limpiar (`#limpiarEntidad`)
- [ ] Actualizar función de carga de datos en modo edición
- [ ] Llamar a `inicializarModalEntidades()` en document.ready

#### **4. Nuevos Endpoints en Controlador:**
- [ ] Agregar case `listarTodasParaModal`
- [ ] Agregar case `obtenerEntidadPorId` (si no existe)
- [ ] Configurar headers JSON apropiados
- [ ] Manejar errores correctamente

#### **5. Nuevos Métodos en Modelo:**
- [ ] Implementar `get_todas_entidades_para_modal()`
- [ ] Incluir todas las columnas necesarias para la tabla
- [ ] Ordenar resultados apropiadamente
- [ ] Manejar excepciones PDO

#### **6. Validaciones y Testing:**
- [ ] Probar apertura y cierre del modal
- [ ] Verificar que la tabla carga correctamente
- [ ] Probar selección de registros
- [ ] Probar botón de limpiar
- [ ] Verificar modo edición (carga de datos existentes)
- [ ] Probar en dispositivos móviles
- [ ] Verificar mensajes de toastr
- [ ] Probar con registros inactivos/activos

#### **7. Optimizaciones Opcionales:**
- [ ] Implementar paginación server-side para tablas muy grandes
- [ ] Agregar filtros adicionales en el modal
- [ ] Implementar caché para mejorar rendimiento
- [ ] Agregar tooltips informativos
- [ ] Implementar búsqueda avanzada

### 🔄 **Plantilla de Nombres para Replicar:**

Para mantener consistencia, use estos patrones de nombres:

```javascript
// Variables JavaScript
var tabla[Entidades];                    // tablaUnidades, tablaClientes
var #modalBuscar[Entidad]               // #modalBuscarUnidad, #modalBuscarCliente
var #tabla[Entidades]Busqueda           // #tablaUnidadesBusqueda
var .seleccionar-[entidad]              // .seleccionar-unidad
var #limpiar[Entidad]                   // #limpiarUnidad
var [entidad]_display                   // unidad_medida_display
var inicializarModal[Entidades]()       // inicializarModalUnidades()
```

```php
// Métodos PHP
get_todas_[entidades]_para_modal()      // get_todas_unidades_para_modal()
obtener[Entidad]PorId                   // obtenerUnidadPorId
listarTodasParaModal                    // endpoint genérico
```

### 🎯 **Campos Típicos a Convertir:**

Campos que se benefician de esta implementación:
- `id_unidad_familia` → Unidades de Medida
- `id_marca` → Marcas
- `id_categoria` → Categorías
- `id_proveedor` → Proveedores
- `id_cliente` → Clientes
- `id_comercial` → Comerciales
- `id_pais` → Países
- `id_estado` → Estados

---

## 📚 **Recursos Adicionales:**

- **DataTables Documentation**: https://datatables.net/
- **Bootstrap Modals**: https://getbootstrap.com/docs/5.1/components/modal/
- **Toastr Notifications**: https://github.com/CodeSeven/toastr

---

**Autor**: Sistema de Desarrollo  
**Fecha**: 8 de noviembre de 2025  
**Versión**: 1.0  
**Proyecto**: TOLDOS_AMPLIADO