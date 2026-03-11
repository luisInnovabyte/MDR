# Formación DataTables - MDR ERP Manager

> Documentación completa y genérica sobre la implementación de DataTables en el proyecto MDR ERP Manager siguiendo el patrón arquitectónico MVC.

---

## 🏗️ Arquitectura General

Los DataTables en MDR ERP siguen un patrón estructurado que integra:

- **HTML**: Estructura básica de la tabla
- **JavaScript**: Configuración e interactividad del DataTables
- **Controller PHP**: Endpoint AJAX que devuelve datos en formato JSON
- **Model PHP**: Consultas a base de datos utilizando prepared statements
- **Bootstrap 5**: Estilos y componentes UI
- **SweetAlert2**: Confirmaciones y alertas
- **jQuery**: Manipulación DOM y eventos

---

## 📋 Estructura HTML Base

### Estructura Mínima Requerida

```html
<!-- Contenedor principal -->
<div class="table-wrapper">
    <table id="nombre_tabla" class="table display responsive nowrap">
        <!-- Cabecera -->
        <thead>
            <tr>
                <th></th>                    <!-- Columna de expansión (detalles) -->
                <th>ID</th>                  <!-- Campo ID (normalmente oculto) -->
                <th>Campo 1</th>
                <th>Campo 2</th>
                <th>Campo N</th>
                <th>Estado</th>
                <th>Act./Desac.</th>          <!-- Botón activar/desactivar -->
                <th>Edit.</th>               <!-- Botón editar -->
            </tr>
        </thead>
        
        <!-- Cuerpo (se llena vía AJAX) -->
        <tbody>
            <!-- Los datos se cargarán aquí dinámicamente -->
        </tbody>
        
        <!-- Pie con filtros de búsqueda -->
        <tfoot>
            <tr>
                <th></th>                    <!-- Sin filtro -->
                <th class="d-none">         <!-- ID oculto -->
                    <input type="text" placeholder="Buscar ID" class="form-control form-control-sm" />
                </th>
                <th>
                    <input type="text" placeholder="Buscar campo 1" class="form-control form-control-sm" />
                </th>
                <th>
                    <input type="text" placeholder="Buscar campo 2" class="form-control form-control-sm" />
                </th>
                <th>
                    <input type="text" placeholder="Buscar campo N" class="form-control form-control-sm" />
                </th>
                <th>
                    <select class="form-control form-control-sm">
                        <option value="">Todos los estados</option>
                        <option value="1">Activos</option>
                        <option value="0">Inactivos</option>
                    </select>
                </th>
                <th class="d-none">         <!-- Sin filtro -->
                    <input type="text" placeholder="NO Buscar" class="form-control form-control-sm" />
                </th>
                <th></th>                    <!-- Sin filtro -->
            </tr>
        </tfoot>
    </table>
</div>
```

### Consideraciones Importantes

- **ID único**: Cada tabla debe tener un ID único (`id="nombre_tabla"`)
- **Clases CSS**: Usar siempre `table display responsive nowrap`
- **Columna de expansión**: Primera columna vacía para detalles expandibles
- **Filtros en footer**: Inputs y selects para búsqueda por columna
- **Responsive**: Las clases permiten adaptación móvil

---

## ⚙️ Configuración JavaScript

### Patrón Base de Configuración

```javascript
$(document).ready(function () {
    
    // 1. CONFIGURACIÓN DE DATATABLES
    var datatable_entidadConfig = {
        processing: true,
        //serverSide: true, // Opcional: procesamiento del servidor
        
        // Layout de paginación (Bootstrap Icons)
        layout: {
            bottomEnd: {
                paging: {
                    firstLast: true,
                    numbers: false,
                    previousNext: true
                }
            }
        },
        
        // Idioma e iconos personalizados
        language: {
            paginate: {
                first: '<i class="bi bi-chevron-double-left"></i>',
                last: '<i class="bi bi-chevron-double-right"></i>',
                previous: '<i class="bi bi-chevron-compact-left"></i>',
                next: '<i class="bi bi-chevron-compact-right"></i>'
            }
        },
        
        // Definición de columnas
        columns: [
            // Columna 0: Control de expansión
            { 
                name: 'control', 
                data: null, 
                defaultContent: '', 
                className: 'details-control sorting_1 text-center' 
            },
            
            // Columna 1: ID (oculta)
            { 
                name: 'id_entidad', 
                data: 'id_entidad', 
                visible: false, 
                className: "text-center" 
            },
            
            // Columnas de datos
            { 
                name: 'campo1', 
                data: 'campo1', 
                className: "text-center align-middle" 
            },
            { 
                name: 'campo2', 
                data: 'campo2', 
                className: "text-center align-middle" 
            },
            
            // Columna de estado 
            { 
                name: 'activo_entidad', 
                data: 'activo_entidad', 
                className: "text-center align-middle" 
            },
            
            // Columna de botón activar/desactivar
            { 
                name: 'activar', 
                data: null, 
                className: "text-center align-middle" 
            },
            
            // Columna de botón editar
            { 
                name: 'editar', 
                data: null, 
                defaultContent: '', 
                className: "text-center align-middle" 
            }
        ],
        
        // Definición avanzada de columnas
        columnDefs: [
            // Columna de controles
            { 
                targets: "control:name", 
                width: '5%', 
                searchable: false, 
                orderable: false, 
                className: "text-center"
            },
            
            // ID oculto
            { 
                targets: "id_entidad:name", 
                width: '5%', 
                searchable: false, 
                orderable: false, 
                className: "text-center" 
            },
            
            // Campos normales
            { 
                targets: "campo1:name", 
                width: '15%', 
                searchable: true, 
                orderable: true, 
                className: "text-center" 
            },
            { 
                targets: "campo2:name", 
                width: '20%', 
                searchable: true, 
                orderable: true, 
                className: "text-center" 
            },
            
            // Estado con iconos
            {
                targets: "activo_entidad:name", 
                width: '10%', 
                orderable: true, 
                searchable: true, 
                className: "text-center",
                render: function (data, type, row) {
                    if (type === "display") {
                        return row.activo_entidad == 1 
                            ? '<i class="bi bi-check-circle text-success fa-2x"></i>' 
                            : '<i class="bi bi-x-circle text-danger fa-2x"></i>';
                    }
                    return row.activo_entidad;
                }
            },
            
            // Botón activar/desactivar
            {   
                targets: "activar:name", 
                width: '10%', 
                searchable: false, 
                orderable: false, 
                class: "text-center",
                render: function (data, type, row) {
                    if (row.activo_entidad == 1) {
                        return `<button type="button" class="btn btn-danger btn-sm desacEntidad" 
                                       data-bs-toggle="tooltip" title="Desactivar" 
                                       data-id_entidad="${row.id_entidad}">
                                    <i class="fa-solid fa-trash"></i>
                                </button>`;
                    } else {
                        return `<button class="btn btn-success btn-sm activarEntidad" 
                                       data-bs-toggle="tooltip" title="Activar" 
                                       data-id_entidad="${row.id_entidad}">
                                    <i class="bi bi-hand-thumbs-up-fill"></i>
                                </button>`;
                    }
                }
            },
            
            // Botón editar
            {   
                targets: "editar:name", 
                width: '10%', 
                searchable: false, 
                orderable: false, 
                class: "text-center",
                render: function (data, type, row) {
                    return `<button type="button" class="btn btn-info btn-sm editarEntidad" 
                                   data-bs-toggle="tooltip" title="Editar" 
                                   data-id_entidad="${row.id_entidad}">
                                <i class="fa-solid fa-edit"></i>
                            </button>`;
                }
            }
        ],
        
        // Configuración AJAX
        ajax: {
            url: '../../controller/entidad.php?op=listar',
            type: 'GET',
            dataSrc: function (json) {
                console.log("JSON recibido:", json);
                return json.data || json;
            }
        }
    };
    
    // 2. DEFINICIÓN DE VARIABLES
    var $table = $('#nombre_tabla');
    var $tableConfig = datatable_entidadConfig;
    var $tableBody = $('#nombre_tabla tbody');
    
    // 3. INICIALIZACIÓN DEL DATATABLE  
    var table_e = $table.DataTable($tableConfig);
});
```

---

## 🎯 Función de Detalles Expandibles

### Patrón format()

```javascript
// Función que define el contenido expandible
function format(d) {
    return `
        <div class="card border-primary mb-3" style="overflow: visible;">
            <!-- Cabecera del card -->
            <div class="card-header bg-primary text-white">
                <div class="d-flex align-items-center">
                    <i class="bi bi-gear-fill fs-3 me-2"></i>
                    <h5 class="card-title mb-0">Detalles del Registro</h5>
                </div>
            </div>
            
            <!-- Cuerpo con tabla de detalles -->
            <div class="card-body p-0" style="overflow: visible;">
                <table class="table table-borderless table-striped table-hover mb-0">
                    <tbody>
                        <tr>
                            <th scope="row" class="ps-4 w-25 align-top">
                                <i class="bi bi-hash me-2"></i>ID
                            </th>
                            <td class="pe-4">
                                ${d.id_entidad || '<span class="text-muted fst-italic">Sin ID</span>'}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 w-25 align-top">
                                <i class="bi bi-tags me-2"></i>Nombre
                            </th>
                            <td class="pe-4">
                                ${d.nombre_entidad || '<span class="text-muted fst-italic">Sin nombre</span>'}
                            </td>
                        </tr>
                        <tr>
                            <th scope="row" class="ps-4 w-25 align-top">
                                <i class="bi bi-calendar-plus me-2"></i>Creado el:
                            </th>
                            <td class="pe-4">
                                ${d.created_at_entidad ? formatoFechaEuropeo(d.created_at_entidad) : '<span class="text-muted fst-italic">Sin fecha</span>'}
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <!-- Pie del card -->
            <div class="card-footer bg-transparent border-top-0 text-end">
                <small class="text-muted">Actualizado: ${new Date().toLocaleDateString()}</small>
            </div>
        </div>
    `;
}

// Event handler para expansión/colapso
$tableBody.on('click', 'td.details-control', function () {
    var tr = $(this).closest('tr');
    var row = table_e.row(tr);

    if (row.child.isShown()) {
        // Cerrar detalles
        row.child.hide();
        tr.removeClass('shown');
    } else {
        // Abrir detalles
        row.child(format(row.data())).show();
        tr.addClass('shown');
    }
});
```

---

## 🔘 Gestión de Botones y Eventos

### Patrón de Botones de Acción

```javascript
// Función de desactivación
function desacEntidad(id) {
    Swal.fire({
        title: 'Desactivar',
        html: `¿Desea desactivar el registro con ID ${id}?<br><br>
               <small class="text-warning">
                   <i class="bi bi-exclamation-triangle me-1"></i>
                   Esta acción desactivará el registro
               </small>`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí',
        cancelButtonText: 'No',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            $.post("../../controller/entidad.php?op=eliminar", { id_entidad: id }, function (data) {
                // Recargar tabla
                $table.DataTable().ajax.reload();
                
                // Notificación de éxito
                Swal.fire('Desactivado', 'El registro ha sido desactivado', 'success');
            });
        }
    });
}

// Event handler
$(document).on('click', '.desacEntidad', function (event) {
    event.preventDefault();
    let id = $(this).data('id_entidad');
    desacEntidad(id);
});

// Función de activación
function activarEntidad(id) {
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
            $.post("../../controller/entidad.php?op=activar", { id_entidad: id }, function (data) {
                $table.DataTable().ajax.reload();
                Swal.fire('Activado', 'El registro ha sido activado', 'success');
            });
        }
    });
}

// Event handler activar
$(document).on('click', '.activarEntidad', function (event) {
    event.preventDefault();
    let id = $(this).data('id_entidad');
    activarEntidad(id);
});

// Botón nuevo registro
$(document).on('click', '#btnnuevo', function (event) {
    event.preventDefault();
    
    $('#mdltitulo').text('Nuevo registro');
    
    // Mostrar modal (Bootstrap 5)
    var modalEntidad = new bootstrap.Modal(document.getElementById('modalEntidad'));
    modalEntidad.show();

    // Limpiar formulario
    $("#formEntidad")[0].reset();
    $('#formEntidad').find('input[name="id_entidad"]').val("");

    // Limpiar validaciones
    if (typeof formValidator !== 'undefined') {
        formValidator.clearValidation();
    }
});

// Botón guardar/editar
$(document).on('click', '#btnSalvarEntidad', function (event) {
    event.preventDefault();

    // Obtener valores del formulario
    var id_entidad = $('#formEntidad').find('input[name="id_entidad"]').val().trim();
    var campo1 = $('input[name="campo1"]').val().trim();
    var campo2 = $('input[name="campo2"]').val().trim();

    // Validar formulario
    if (typeof formValidator !== 'undefined' && !formValidator.validateForm(event)) {
        toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
        return;
    }
    
    // Guardar datos
    guardarEntidad(id_entidad, campo1, campo2);
});

function guardarEntidad(id, campo1, campo2) {
    $.ajax({
        url: "../../controller/entidad.php?op=guardaryeditar",
        method: "POST",
        data: {
            id_entidad: id,
            campo1: campo1,
            campo2: campo2
        },
        dataType: "json",
        success: function(response) {
            if (response.success) {
                // Cerrar modal
                bootstrap.Modal.getInstance(document.getElementById('modalEntidad')).hide();
                
                // Recargar tabla
                $table.DataTable().ajax.reload();
                
                // Notificación
                toastr.success(response.message, 'Éxito');
            } else {
                toastr.error(response.message, 'Error');
            }
        }
    });
}
```

---

## 📡 Integración con Backend PHP

### Controller PHP - Estructura Estándar

```php
<?php
require_once "../config/conexion.php";
require_once "../config/funciones.php"; 
require_once "../models/Entidad.php";

// Inicializar clases
$registro = new RegistroActividad();
$entidad = new Entidad();

switch ($_GET["op"]) {
    
    case "listar":
        $datos = $entidad->get_entidades();
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_entidad" => $row["id_entidad"],
                "campo1" => $row["campo1"],
                "campo2" => $row["campo2"],
                "activo_entidad" => $row["activo_entidad"],
                "created_at_entidad" => $row["created_at_entidad"]
            );
        }
        
        $results = array(
            "draw" => intval($_POST['draw'] ?? 1),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        header('Content-Type: application/json');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        break;
        
    case "guardaryeditar":
        // Sanitizar inputs
        $id_entidad = $_POST["id_entidad"] ?? null;
        $campo1 = htmlspecialchars(trim($_POST["campo1"]), ENT_QUOTES, 'UTF-8');
        $campo2 = htmlspecialchars(trim($_POST["campo2"]), ENT_QUOTES, 'UTF-8');
        
        try {
            if (empty($id_entidad)) {
                // INSERT
                $resultado = $entidad->insert_entidad($campo1, $campo2);
                
                echo json_encode([
                    'success' => $resultado ? true : false,
                    'message' => $resultado ? 'Registro creado correctamente' : 'Error al crear',
                    'id_entidad' => $resultado
                ], JSON_UNESCAPED_UNICODE);
            } else {
                // UPDATE
                $resultado = $entidad->update_entidad($id_entidad, $campo1, $campo2);
                
                echo json_encode([
                    'success' => $resultado !== false,
                    'message' => $resultado !== false ? 'Registro actualizado' : 'Error al actualizar'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;
        
    case "eliminar":
        // Soft delete
        $id_entidad = $_POST["id_entidad"];
        $resultado = $entidad->delete_entidadxid($id_entidad);
        
        echo json_encode([
            'success' => $resultado,
            'message' => $resultado ? 'Registro desactivado' : 'Error al desactivar'
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case "activar":
        // Reactivar
        $id_entidad = $_POST["id_entidad"];
        $resultado = $entidad->activar_entidadxid($id_entidad);
        
        echo json_encode([
            'success' => $resultado,
            'message' => $resultado ? 'Registro activado' : 'Error al activar'
        ], JSON_UNESCAPED_UNICODE);
        break;
        
    case "verificar":
        // Verificar existencia
        $campo_unico = $_POST["campo1"];
        $id_entidad = $_POST["id_entidad"] ?? null;
        
        $resultado = $entidad->verificarEntidad($campo_unico, $id_entidad);
        echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

### Modelo PHP - Métodos Estándar

```php
<?php
require_once '../config/conexion.php';
require_once '../config/funciones.php';

class Entidad
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
            $this->registro->registrarActividad(
                'system', 'Entidad', '__construct',
                "Error zona horaria: " . $e->getMessage(), 'warning'
            );
        }
    }

    // Listar todos los registros activos
    public function get_entidades()
    {
        try {
            $sql = "SELECT * FROM entidad 
                    WHERE activo_entidad = 1 
                    ORDER BY campo1 ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'Entidad', 'get_entidades',
                "Error: " . $e->getMessage(), 'error'
            );
            return [];
        }
    }

    // Insertar nuevo registro
    public function insert_entidad($campo1, $campo2)
    {
        try {
            $sql = "INSERT INTO entidad (
                        campo1, campo2, created_at_entidad
                    ) VALUES (?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $campo1, PDO::PARAM_STR);
            $stmt->bindValue(2, $campo2, PDO::PARAM_STR);
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin', 'Entidad', 'insert_entidad',
                "Entidad creada con ID: $id", 'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin', 'Entidad', 'insert_entidad',
                "Error: " . $e->getMessage(), 'error'
            );
            return false;
        }
    }

    // Actualizar registro
    public function update_entidad($id_entidad, $campo1, $campo2)
    {
        try {
            $sql = "UPDATE entidad SET 
                        campo1 = ?,
                        campo2 = ?,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $campo1, PDO::PARAM_STR);
            $stmt->bindValue(2, $campo2, PDO::PARAM_STR);
            $stmt->bindValue(3, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            return false;
        }
    }

    // Soft delete
    public function delete_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE entidad SET 
                        activo_entidad = 0,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }

    // Activar registro
    public function activar_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE entidad SET 
                        activo_entidad = 1,
                        updated_at_entidad = NOW()
                    WHERE id_entidad = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            return false;
        }
    }

    // Verificar existencia
    public function verificarEntidad($campo_unico, $id_entidad = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM entidad 
                    WHERE LOWER(campo1) = LOWER(?)";
            $params = [trim($campo_unico)];
            
            if (!empty($id_entidad)) {
                $sql .= " AND id_entidad != ?";
                $params[] = $id_entidad;
            }
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return ['existe' => ($resultado['total'] > 0)];
            
        } catch (PDOException $e) {
            return ['existe' => false, 'error' => $e->getMessage()];
        }
    }
}
?>
```

---

## 📱 Responsividad y UX

### Clases CSS Importantes

```css
/* Tabla responsive */
.table.display.responsive.nowrap

/* Controles de alineación */
.text-center.align-middle
.text-start.align-middle
.text-end.align-middle

/* Controles de ancho */
width: '5%'   /* Columnas de control */
width: '10%'  /* Botones de acción */
width: '15%'  /* Campos cortos */
width: '20%'  /* Campos medios */

/* Estados condicionales */
.d-none       /* Ocultar elementos */
.visible      /* Controlar visibilidad de columnas */
```

### Adaptación Móvil

- **Columnas ocultas**: IDs y campos secundarios se ocultan en móvil
- **Botones compactos**: `.btn-sm` para espacios reducidos
- **Detalles expandibles**: Información adicional en card expandible
- **Iconos descriptivos**: Bootstrap Icons para identificación visual

---

## ⚡ Optimización y Rendimiento

### Mejores Prácticas

#### 1. **Lazy Loading**
```javascript
// Solo cargar datos cuando es necesario
"serverSide": true,  // Para grandes volúmenes
"processing": true   // Indicador visual
```

#### 2. **Paginación Eficiente**
```javascript
"pageLength": 25,    // Registros por página
"lengthMenu": [ [10, 25, 50, 100], [10, 25, 50, 100] ]
```

#### 3. **Búsqueda por Columnas**
```javascript
// Filtros específicos en footer
$columnFilterInputs.on('keyup change', function () {
    table.column($(this).parent().index() + ':visible')
         .search(this.value)
         .draw();
});
```

---

## 🛠️ Debugging y Troubleshooting

### Console Logs Útiles

```javascript
// Debug de datos recibidos
dataSrc: function (json) {
    console.log("JSON recibido:", json);
    return json.data || json;
}

// Debug de eventos de botones
$(document).on('click', '.editarEntidad', function (event) {
    console.log("Botón editar clickeado, ID:", $(this).data('id_entidad'));
});
```

### Problemas Comunes

#### 1. **Tabla no se carga**
- Verificar la URL del AJAX
- Comprobar la respuesta JSON del controller
- Revisar errores en consola del navegador

#### 2. **Botones no funcionan**
- Verificar que los `data-*` attributes estén correctos
- Comprobar que los Event Handlers estén definidos
- Asegurar que jQuery esté cargado

#### 3. **Modal no aparece**
- Verificar que Bootstrap esté cargado
- Comprobar la existencia del modal en DOM
- Revisar la sintaxis de Bootstrap 5

---

## 📋 Checklist de Implementación

### HTML ✅
- [ ] Tabla con ID único definido
- [ ] Clases CSS correctas (`table display responsive nowrap`)
- [ ] Estructura thead, tbody, tfoot
- [ ] Columna de control de expansión
- [ ] Filtros en footer (inputs y selects)

### JavaScript ✅  
- [ ] Configuración de DataTables completa
- [ ] Definición de columnas y columnDefs
- [ ] Función format() para detalles expandibles
- [ ] Event handlers para todos los botones
- [ ] Validación de formularios
- [ ] Integración con SweetAlert2 y Toastr

### Controller PHP ✅
- [ ] Switch con todas las operaciones necesarias
- [ ] Sanitización de inputs
- [ ] Manejo de errores con try-catch
- [ ] Respuestas JSON consistentes
- [ ] Headers correcto (Content-Type: application/json)

### Modelo PHP ✅
- [ ] Métodos estándar implementados
- [ ] Prepared statements en todas las consultas
- [ ] Logging de actividades
- [ ] Manejo de soft delete
- [ ] Validación de unicidad

### UX/UI ✅
- [ ] Diseño responsive con Bootstrap 5
- [ ] Iconos descriptivos (Bootstrap Icons)
- [ ] Confirmaciones con SweetAlert2
- [ ] Notificaciones con Toastr
- [ ] Focus automático en campos

---

## 💡 Tips y Consideraciones Finales

### Convenciones del Proyecto MDR

1. **Nomenclatura**: Seguir patrón `nombre_entidad` para todos los campos
2. **Soft Delete**: NUNCA usar DELETE físico, usar `activo_entidad = 0`
3. **Timestamps**: Siempre incluir `created_at_entidad` y `updated_at_entidad`
4. **Logging**: Registrar todas las actividades importantes
5. **Prepared Statements**: OBLIGATORIO para todas las consultas SQL

### Extensibilidad

- **Nuevas columnas**: Agregar en `columns[]` y `columnDefs[]`
- **Filtros avanzados**: Usar acordeones y filtros personalizados
- **Exportación**: Agregar botones de Excel, PDF, CSV
- **Bulk operations**: Implementar checkboxes para acciones múltiples

### Performance

- Para tablas con +1000 registros, considerar `serverSide: true`
- Implementar índices en base de datos para campos de búsqueda
- Usar caché PHP para consultas repetitivas
- Optimizar consultas con EXPLAIN en MySQL

---

**Autor**: MDR ERP Manager Team  
**Última actualización**: Marzo 2026  
**Versión**: 1.0

---

> Esta documentación cubre todos los aspectos necesarios para implementar DataTables siguiendo los estándares y patrones del proyecto MDR ERP Manager. Para casos específicos o preguntas adicionales, consultar el código fuente en `/view/MntMarca/` como referencia práctica.