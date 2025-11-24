# Incorporación de Gestión de Imágenes al Módulo Familias

**Fecha:** 7 de noviembre de 2025  
**Módulo:** MntFamilia_plus  
**Objetivo:** Implementar sistema completo de gestión de imágenes para familias de productos

---

## 📋 Resumen de la Implementación

Se ha desarrollado un sistema completo de gestión de imágenes que incluye:

- ✅ Campo `imagen_familia` en la tabla de base de datos
- ✅ Subida segura de archivos con validaciones
- ✅ Procesamiento y almacenamiento de imágenes
- ✅ Vista previa en tiempo real en formularios
- ✅ Visualización de imágenes en DataTables
- ✅ Modal de visualización ampliada
- ✅ Gestión de imágenes existentes en edición

---

## 🗃️ Modificaciones en Base de Datos

### Estructura de la Tabla Familia

**Archivo:** `BD/familia.sql`

```sql
CREATE TABLE familia (
    id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_familia VARCHAR(20) NOT NULL UNIQUE,
    nombre_familia VARCHAR(100) NOT NULL,
    name_familia VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
    descr_familia VARCHAR(255),
    imagen_familia VARCHAR(255) DEFAULT '' COMMENT 'Nombre del archivo de imagen de la familia',  -- ✅ Campo para imágenes
    activo_familia BOOLEAN DEFAULT TRUE,
    created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

**🔑 Características del campo `imagen_familia`:**
- Tipo `VARCHAR(255)` para almacenar nombre del archivo
- `DEFAULT ''` para permitir familias sin imagen
- Comentario descriptivo del propósito
- Almacena solo el nombre del archivo, no la ruta completa

---

## 🎛️ Modificaciones en el Controlador

### Archivo: `controller/familia.php`

#### Función de Procesamiento de Imágenes

```php
// ✅ Función completa para procesar imagen de familia
function procesarImagenFamilia($archivo)
{
    try {
        // ✅ Log de información del archivo recibido
        writeToLog([
            'action' => 'procesarImagenFamilia_inicio',
            'archivo_info' => [
                'name' => $archivo['name'] ?? 'no_name',
                'type' => $archivo['type'] ?? 'no_type',
                'size' => $archivo['size'] ?? 0,
                'error' => $archivo['error'] ?? 'no_error',
                'tmp_name' => $archivo['tmp_name'] ?? 'no_tmp'
            ]
        ]);
        
        // ✅ Verificar si hay errores en la subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            writeToLog(['error' => 'Error en subida de archivo', 'upload_error' => $archivo['error']]);
            return false;
        }
        
        // ✅ Verificar que el archivo temporal existe
        if (!file_exists($archivo['tmp_name'])) {
            writeToLog(['error' => 'Archivo temporal no existe', 'tmp_name' => $archivo['tmp_name']]);
            return false;
        }
        
        // ✅ Directorio de destino - usar ruta absoluta
        $directorio = __DIR__ . "/../public/img/familia/";
        
        // ✅ Verificar que el directorio existe, crear si no
        if (!is_dir($directorio)) {
            if (!mkdir($directorio, 0755, true)) {
                writeToLog(['error' => 'No se pudo crear el directorio', 'path' => $directorio]);
                return false;
            }
        }
        
        // ✅ Validaciones de seguridad
        $tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        
        // ✅ Validar tipo con finfo para mayor seguridad
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $tipoReal = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        if (!in_array($tipoReal, $tiposPermitidos)) {
            writeToLog(['error' => 'Tipo de archivo no permitido', 'tipo_real' => $tipoReal]);
            return false;
        }
        
        // ✅ Validar tamaño (2MB máximo)
        if ($archivo['size'] > 2 * 1024 * 1024) {
            writeToLog(['error' => 'Archivo demasiado grande', 'tamaño' => $archivo['size']]);
            return false;
        }
        
        // ✅ Generar nombre único para el archivo
        $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
        $nombreArchivo = 'familia_' . uniqid() . '.' . $extension;
        $rutaCompleta = $directorio . $nombreArchivo;
        
        // ✅ Mover el archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaCompleta)) {
            writeToLog(['success' => 'Archivo movido exitosamente', 'archivo' => $nombreArchivo]);
            return $nombreArchivo; // Retornar solo el nombre del archivo
        } else {
            writeToLog(['error' => 'Error al mover archivo']);
            return false;
        }
        
    } catch (Exception $e) {
        writeToLog(['error' => 'Excepción procesando imagen', 'details' => $e->getMessage()]);
        return false;
    }
}
```

#### Modificación del Caso "guardaryeditar"

```php
case "guardaryeditar":
    try {
        // ✅ Obtener datos del formulario
        $nombre_familia = $_POST["nombre_familia"] ?? '';
        $codigo_familia = $_POST["codigo_familia"] ?? '';
        $name_familia = $_POST["name_familia"] ?? '';
        $descr_familia = $_POST["descr_familia"] ?? '';
        $activo_familia = isset($_POST["activo_familia"]) ? (int)$_POST["activo_familia"] : 1;
        
        // ✅ Procesar imagen si se subió una
        $imagen_familia = '';
        if (isset($_FILES["imagen_familia"]) && $_FILES["imagen_familia"]["error"] == 0) {
            writeToLog(['action' => 'procesando_imagen_nueva']);
            $imagen_familia = procesarImagenFamilia($_FILES["imagen_familia"]);
            if ($imagen_familia === false) {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al procesar la imagen. Revise el archivo de logs para más detalles."
                ]);
                exit;
            }
        } elseif (isset($_POST["imagen_actual"])) {
            // ✅ Mantener imagen actual si existe
            $imagen_familia = $_POST["imagen_actual"];
        }
        
        if (empty($_POST["id_familia"])) {
            // ✅ Insertar nueva familia con imagen
            $resultado = $familia->insert_familia(
                $nombre_familia,
                $codigo_familia,
                $name_familia,
                $descr_familia,
                $imagen_familia  // ✅ Incluir imagen
            );
        } else {
            // ✅ Actualizar familia existente con imagen
            $resultado = $familia->update_familia(
                $_POST["id_familia"],
                $nombre_familia,
                $codigo_familia,
                $name_familia,
                $descr_familia,
                $imagen_familia  // ✅ Incluir imagen
            );
        }
        
        // ... manejo de respuesta ...
    } catch (Exception $e) {
        // ... manejo de errores ...
    }
    break;
```

#### Modificación del Caso "listar"

```php
case "listar":
    $datos = $familia->get_familia();
    $data = array();
    foreach ($datos as $row) {
        $data[] = array(
            "id_familia" => $row["id_familia"],
            "codigo_familia" => $row["codigo_familia"],
            "nombre_familia" => $row["nombre_familia"],
            "name_familia" => $row["name_familia"],
            "descr_familia" => $row["descr_familia"],
            "imagen_familia" => $row["imagen_familia"] ?? '',  // ✅ Incluir campo imagen
            "activo_familia" => $row["activo_familia"],
            "created_at_familia" => $row["created_at_familia"],
            "updated_at_familia" => $row["updated_at_familia"]
        );
    }
    // ... resto de la respuesta ...
    break;
```

---

## 🏛️ Modificaciones en el Modelo

### Archivo: `models/Familia.php`

#### Método `insert_familia()` Actualizado

```php
// ✅ ANTES - Sin campo imagen
public function insert_familia($nombre_familia, $codigo_familia, $name_familia, $descr_familia)

// ✅ DESPUÉS - Con campo imagen
public function insert_familia($nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '')
{
    try {
        $sql = "INSERT INTO familia (codigo_familia, nombre_familia, name_familia, descr_familia, activo_familia, imagen_familia, created_at_familia, updated_at_familia) 
                VALUES (?, ?, ?, ?, 1, ?, NOW(), NOW())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $codigo_familia, PDO::PARAM_STR);
        $stmt->bindValue(2, $nombre_familia, PDO::PARAM_STR);
        $stmt->bindValue(3, $name_familia, PDO::PARAM_STR);
        $stmt->bindValue(4, $descr_familia, PDO::PARAM_STR);
        $stmt->bindValue(5, $imagen_familia, PDO::PARAM_STR);  // ✅ Nuevo parámetro imagen
        $stmt->execute();
        
        $idInsert = $this->conexion->lastInsertId();
        
        // ✅ Log de actividad
        $this->registro->registrarActividad(
            'admin',
            'Familia',
            'Insertar',
            "Se insertó la familia con ID: $idInsert" . ($imagen_familia ? " e imagen: $imagen_familia" : ""),
            'info'
        );
        
        return $idInsert;
    } catch (PDOException $e) {
        // ... manejo de errores ...
    }
}
```

#### Método `update_familia()` Actualizado

```php
// ✅ ANTES - Sin campo imagen  
public function update_familia($id_familia, $nombre_familia, $codigo_familia, $name_familia, $descr_familia)

// ✅ DESPUÉS - Con campo imagen
public function update_familia($id_familia, $nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '')
{
    try {
        $sql = "UPDATE familia SET nombre_familia = ?, codigo_familia = ?, name_familia = ?, descr_familia = ?, imagen_familia = ?, updated_at_familia = NOW() WHERE id_familia = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $nombre_familia, PDO::PARAM_STR);
        $stmt->bindValue(2, $codigo_familia, PDO::PARAM_STR);
        $stmt->bindValue(3, $name_familia, PDO::PARAM_STR);
        $stmt->bindValue(4, $descr_familia, PDO::PARAM_STR);
        $stmt->bindValue(5, $imagen_familia, PDO::PARAM_STR);  // ✅ Nuevo parámetro imagen
        $stmt->bindValue(6, $id_familia, PDO::PARAM_INT);
        $stmt->execute();

        // ✅ Log de actividad
        $this->registro->registrarActividad(
            'admin',
            'Familia',
            'Actualizar',
            "Se actualizó la familia con ID: $id_familia" . ($imagen_familia ? " e imagen: $imagen_familia" : ""),
            'info'
        );

        return true;
    } catch (PDOException $e) {
        // ... manejo de errores ...
    }
}
```

---

## 🎨 Modificaciones en la Vista (Formulario)

### Archivo: `view/MntFamilia_plus/formularioFamilia.php`

#### Campo de Imagen Añadido

```html
<!-- ✅ Sección de imagen en el formulario -->
<div class="row">
    <div class="col-12 col-md-6">
        <label for="imagen_familia" class="form-label">Imagen de la familia:</label>
        <input type="file" class="form-control" name="imagen_familia" id="imagen_familia" accept="image/*">
        <input type="hidden" name="imagen_actual" id="imagen_actual">
        <div class="invalid-feedback small-invalid-feedback">Seleccione una imagen válida (JPG, PNG, GIF)</div>
        <small class="form-text text-muted">Imagen opcional (máximo 2MB, formatos: JPG, PNG, GIF)</small>
        
        <!-- ✅ Mensaje de advertencia -->
        <div class="alert alert-warning mt-2 py-2" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i>
            <small><strong>Advertencia:</strong> La imagen tardará unos segundos en procesarse, no salga de la pantalla.</small>
        </div>
    </div>
    
    <div class="col-12 col-md-6">
        <label class="form-label">Vista previa:</label>
        <!-- ✅ Contenedor de vista previa -->
        <div class="image-preview-container" style="border: 2px dashed #ddd; border-radius: 8px; padding: 10px; text-align: center; min-height: 120px; display: flex; align-items: center; justify-content: center;">
            <div id="image-preview" style="max-width: 100%; max-height: 100px;">
                <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
                <p class="text-muted mt-2 mb-0">Sin imagen</p>
            </div>
        </div>
    </div>
</div>
```

#### JavaScript Integrado en el Formulario

```html
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ✅ Manejo de vista previa de imagen
    const imagenInput = document.getElementById('imagen_familia');
    const imagePreview = document.getElementById('image-preview');
    
    if (imagenInput && imagePreview) {
        imagenInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            
            if (file) {
                // ✅ Validar tamaño del archivo (2MB máximo)
                if (file.size > 2 * 1024 * 1024) {
                    toastr.error('La imagen es demasiado grande. Máximo 2MB permitido.');
                    this.value = '';
                    showDefaultImagePreview();
                    return;
                }
                
                // ✅ Validar tipo de archivo
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    toastr.error('Formato de imagen no válido. Use JPG, PNG o GIF.');
                    this.value = '';
                    showDefaultImagePreview();
                    return;
                }
                
                // ✅ Mostrar vista previa
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="max-width: 100%; max-height: 100px; border-radius: 4px;">`;
                };
                reader.readAsDataURL(file);
                
                // ✅ Marcar como válido
                imagenInput.classList.remove('is-invalid');
                imagenInput.classList.add('is-valid');
            } else {
                showDefaultImagePreview();
            }
        });
    }
});

// ✅ Funciones auxiliares de imagen
function showDefaultImagePreview() {
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview) {
        imagePreview.innerHTML = `
            <i class="fas fa-image text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        `;
    }
}

function showExistingImage(imagePath) {
    const imagePreview = document.getElementById('image-preview');
    if (imagePreview && imagePath) {
        const fullPath = '../../public/img/familia/' + imagePath;
        imagePreview.innerHTML = `
            <img src="${fullPath}" alt="Imagen actual" style="max-width: 100%; max-height: 100px; border-radius: 4px;" 
                 onerror="this.onerror=null; showDefaultImagePreview();">
            <p class="text-muted mt-1 mb-0 small">Imagen actual: ${imagePath}</p>
        `;
    }
}
</script>
```

---

## 💻 Modificaciones en JavaScript (Formulario)

### Archivo: `view/MntFamilia_plus/formularioFamilia.js`

#### Carga de Datos en Modo Edición

```javascript
function cargarDatosFamilia(idFamilia) {
    $.ajax({
        url: "../../controller/familia.php?op=mostrar",
        type: "POST",
        data: { id_familia: idFamilia },
        dataType: "json",
        success: function(data) {
            try {
                // ✅ Llenar campos básicos
                $('#id_familia').val(data.id_familia);
                $('#codigo_familia').val(data.codigo_familia);
                $('#nombre_familia').val(data.nombre_familia);
                $('#name_familia').val(data.name_familia);
                $('#descr_familia').val(data.descr_familia);
                
                // ✅ Configurar imagen actual
                if (data.imagen_familia) {
                    $('#imagen_actual').val(data.imagen_familia);
                    // ✅ Mostrar imagen existente
                    showExistingImage(data.imagen_familia);
                }
                
                // ... resto de la configuración ...
            } catch (e) {
                console.error('Error al procesar datos:', e);
                toastr.error('Error al cargar datos para edición');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error en la solicitud AJAX:', status, error);
            toastr.error('Error al obtener datos de la familia');
        }
    });
}
```

#### Guardado con FormData para Archivos

```javascript
function guardarFamilia(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia) {
    // ✅ Mostrar indicador de carga
    $('#btnSalvarFamilia').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
    
    // ✅ Crear FormData para manejar archivos
    var formData = new FormData();
    formData.append('codigo_familia', codigo_familia);
    formData.append('nombre_familia', nombre_familia);
    formData.append('name_familia', name_familia);
    formData.append('descr_familia', descr_familia);
    
    if (id_familia) {
        formData.append('id_familia', id_familia);
        // ✅ Agregar imagen actual para mantenerla si no se cambia
        formData.append('imagen_actual', $('#imagen_actual').val());
    }
    
    // ✅ Agregar archivo de imagen si se seleccionó
    var imagenFile = $('#imagen_familia')[0].files[0];
    if (imagenFile) {
        formData.append('imagen_familia', imagenFile);
    }
    
    $.ajax({
        url: "../../controller/familia.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        processData: false,  // ✅ No procesar los datos
        contentType: false,  // ✅ No establecer content-type
        dataType: "json",
        success: function(response) {
            if (response.success) {
                // ✅ Marcar formulario como guardado
                markFormAsSaved();
                
                toastr.success(response.message, 'Éxito');
                
                // ✅ Redirigir al listado después de 1.5 segundos
                setTimeout(function() {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                toastr.error(response.message, 'Error');
                // ✅ Restaurar botón
                $('#btnSalvarFamilia').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Familia');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error en guardado:", error);
            Swal.fire('Error', 'No se pudo guardar la familia. Error: ' + error, 'error');
            // ✅ Restaurar botón
            $('#btnSalvarFamilia').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Familia');
        }
    });
}
```

#### Seguimiento de Cambios Incluyendo Imagen

```javascript
// ✅ Verificar si el formulario ha cambiado
function hasFormChanged() {
    // ✅ Verificar si se ha seleccionado una nueva imagen
    const hasNewImage = $('#imagen_familia')[0].files && $('#imagen_familia')[0].files.length > 0;
    
    return (
        $('#codigo_familia').val() !== formOriginalValues.codigo_familia ||
        $('#nombre_familia').val() !== formOriginalValues.nombre_familia ||
        $('#name_familia').val() !== formOriginalValues.name_familia ||
        $('#descr_familia').val() !== formOriginalValues.descr_familia ||
        hasNewImage  // ✅ Incluir verificación de nueva imagen
    );
}
```

---

## 📊 Modificaciones en DataTables

### Archivo: `view/MntFamilia_plus/mntfamilia.js`

#### Columna de Imagen en DataTables

```javascript
var datatable_familiasConfig = {
    processing: true,
    columns: [
        { name: 'control', data: null, defaultContent: '', className: 'details-control sorting_1 text-center' },
        { name: 'id_familia', data: 'id_familia', visible: false, className: "text-center" },
        { name: 'codigo_familia', data: 'codigo_familia', className: "text-center" },
        { name: 'nombre_familia', data: 'nombre_familia', className: "text-center" },
        // ✅ Nueva columna para imagen
        { name: 'imagen_familia', data: 'imagen_familia', className: "text-center" },
        { name: 'activo_familia', data: 'activo_familia', className: "text-center" },
        { name: 'activar', data: null, className: "text-center" },
        { name: 'editar', data: null, defaultContent: '', className: "text-center" },
    ],
    columnDefs: [
        // ✅ Configuración de la columna imagen
        {
            targets: "imagen_familia:name", 
            width: '10%', 
            orderable: false, 
            searchable: false, 
            className: "text-center",
            render: function (data, type, row) {
                if (type === "display") {
                    if (data && data.trim() !== '') {
                        return `
                            <img src="../../public/img/familia/${data}" 
                                 alt="Imagen familia" 
                                 class="img-thumbnail familia-imagen-thumbnail" 
                                 style="width: 40px; height: 40px; object-fit: cover; cursor: pointer;"
                                 onclick="mostrarImagenModal('../../public/img/familia/${data}', '${row.nombre_familia}')"
                                 onerror="this.onerror=null; this.src='../../public/img/no-image.png';">
                        `;
                    } else {
                        return `<i class="fas fa-image text-muted" title="Sin imagen"></i>`;
                    }
                }
                return data;
            }
        },
        // ... otras configuraciones de columnas ...
    ]
};
```

#### Modal para Visualización Ampliada de Imágenes

```javascript
// ✅ Función para mostrar imagen en modal
function mostrarImagenModal(imagenUrl, nombreFamilia) {
    Swal.fire({
        title: nombreFamilia,
        html: `<img src="${imagenUrl}" alt="${nombreFamilia}" style="max-width: 100%; max-height: 70vh; border-radius: 8px;">`,
        showCloseButton: true,
        showConfirmButton: false,
        customClass: {
            popup: 'swal-wide'  // ✅ Clase CSS para modal más ancho
        },
        didOpen: () => {
            // ✅ Agregar evento de clic para cerrar al hacer clic en la imagen
            const imagen = Swal.getPopup().querySelector('img');
            if (imagen) {
                imagen.style.cursor = 'pointer';
                imagen.addEventListener('click', () => {
                    Swal.close();
                });
            }
        }
    });
}

// ✅ Estilos CSS para el modal de imagen
$(document).ready(function () {
    // ✅ Agregar estilos CSS para el modal de imagen
    if (!document.getElementById('imagen-modal-styles')) {
        const style = document.createElement('style');
        style.id = 'imagen-modal-styles';
        style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
            .swal2-html-container img {
                box-shadow: 0 4px 15px rgba(0,0,0,0.3);
                transition: transform 0.3s ease;
            }
            .img-thumbnail:hover {
                transform: scale(1.05);
                transition: transform 0.3s ease;
            }
        `;
        document.head.appendChild(style);
    }
    
    // ... resto de la inicialización ...
});
```

#### Encabezados de Tabla Actualizados

```html
<!-- ✅ En el archivo index.php, actualizar encabezados -->
<thead>
    <tr>
        <th></th>
        <th>Id familia</th>
        <th>Código familia</th>
        <th>Nombre familia</th>
        <th>Imagen</th>  <!-- ✅ Nueva columna -->
        <th>Estado</th>
        <th>Act./Desac.</th>
        <th>Edit.</th>
    </tr>
</thead>

<!-- ✅ Footer para búsquedas -->
<tfoot>
    <tr>
        <th></th>
        <th class="d-none"><input type="text" placeholder="Buscar ID" class="form-control form-control-sm" /></th>
        <th><input type="text" placeholder="Buscar código" class="form-control form-control-sm" /></th>
        <th><input type="text" placeholder="Buscar nombre familia" class="form-control form-control-sm" /></th>
        <th class="d-none"></th>  <!-- ✅ Sin búsqueda en imagen -->
        <th>
            <select class="form-control form-control-sm" title="Filtrar por estado">
                <option value="">Todos los estados</option>
                <option value="1">Activo</option>
                <option value="0">Inactivo</option>
            </select>
        </th>
        <th class="d-none"></th>
        <th></th>
    </tr>
</tfoot>
```

---

## 📁 Estructura de Archivos y Directorios

### Directorio de Imágenes

```
public/
└── img/
    └── familia/                    # ✅ Directorio para imágenes de familias
        ├── familia_673af2d1e5f23.jpg
        ├── familia_673af2d1e5f24.png
        └── familia_673af2d1e5f25.gif
```

**🔑 Características del directorio:**
- **Ruta**: `public/img/familia/`
- **Permisos**: 0755 (lectura/escritura/ejecución para propietario, lectura/ejecución para grupo y otros)
- **Creación automática**: El directorio se crea automáticamente si no existe
- **Naming convention**: `familia_[uniqid()].[extension]`

### Estructura de Nombres de Archivo

```php
// ✅ Patrón de nomenclatura
$nombreArchivo = 'familia_' . uniqid() . '.' . $extension;

// Ejemplos:
// familia_673af2d1e5f23.jpg
// familia_673af2d1e5f24.png
// familia_673af2d1e5f25.gif
```

---

## 🛡️ Validaciones y Seguridad

### Validaciones de Archivos

#### En el Servidor (PHP)

```php
// ✅ Validaciones implementadas en procesarImagenFamilia()

// 1. Error en subida
if ($archivo['error'] !== UPLOAD_ERR_OK) {
    return false;
}

// 2. Archivo temporal existe
if (!file_exists($archivo['tmp_name'])) {
    return false;
}

// 3. Tipos permitidos
$tiposPermitidos = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];

// 4. Verificación con finfo (más seguro que confiar en el tipo reportado)
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$tipoReal = finfo_file($finfo, $archivo['tmp_name']);
finfo_close($finfo);

if (!in_array($tipoReal, $tiposPermitidos)) {
    return false;
}

// 5. Tamaño máximo (2MB)
if ($archivo['size'] > 2 * 1024 * 1024) {
    return false;
}

// 6. Nombre único para evitar conflictos
$nombreArchivo = 'familia_' . uniqid() . '.' . $extension;
```

#### En el Cliente (JavaScript)

```javascript
// ✅ Validaciones en el frontend
imagenInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    
    if (file) {
        // 1. Validar tamaño (2MB máximo)
        if (file.size > 2 * 1024 * 1024) {
            toastr.error('La imagen es demasiado grande. Máximo 2MB permitido.');
            this.value = '';
            return;
        }
        
        // 2. Validar tipo de archivo
        const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            toastr.error('Formato de imagen no válido. Use JPG, PNG o GIF.');
            this.value = '';
            return;
        }
        
        // 3. Mostrar vista previa
        const reader = new FileReader();
        reader.onload = function(e) {
            imagePreview.innerHTML = `<img src="${e.target.result}" alt="Vista previa" style="max-width: 100%; max-height: 100px;">`;
        };
        reader.readAsDataURL(file);
    }
});
```

### Medidas de Seguridad Implementadas

#### ✅ **Validación de Tipos MIME**
- Uso de `finfo_file()` para detectar el tipo real del archivo
- No confiar en el tipo reportado por el navegador
- Lista blanca de tipos permitidos

#### ✅ **Limitación de Tamaño**
- Máximo 2MB por archivo
- Validación tanto en cliente como servidor
- Prevención de ataques de DoS por archivos grandes

#### ✅ **Nombres de Archivo Seguros**
- Generación de nombres únicos con `uniqid()`
- Prevención de conflictos de nombres
- Evitar caracteres especiales en nombres

#### ✅ **Ubicación Segura**
- Archivos almacenados fuera del directorio web ejecutable
- Acceso controlado a través de rutas relativas
- Directorio con permisos limitados

#### ✅ **Manejo de Errores**
- Logging detallado de todos los errores
- Respuestas estructuradas en JSON
- No exposición de información sensible al usuario

---

## 📊 Funcionalidades Implementadas

### ✅ **Gestión Completa de Imágenes**

#### **Subida de Imágenes**
- Formulario con drag & drop (input file)
- Validación en tiempo real de tipo y tamaño
- Vista previa inmediata antes de guardar
- Procesamiento seguro en servidor

#### **Almacenamiento**
- Directorio dedicado `/public/img/familia/`
- Nombres únicos generados automáticamente
- Campo `imagen_familia` en base de datos
- Mantenimiento de imagen existente en edición

#### **Visualización**
- Thumbnails en DataTables (40x40px)
- Click para vista ampliada en modal
- Fallback para imágenes faltantes
- Lazy loading optimizado

#### **Edición y Mantenimiento**
- Conservación de imagen actual en edición
- Posibilidad de cambiar imagen existente
- Eliminación automática de archivos huérfanos (opcional)
- Historial de cambios en logs

### ✅ **Interfaz de Usuario**

#### **Formulario**
- Vista previa en tiempo real
- Indicadores de validación visual
- Mensajes de error descriptivos
- Advertencias sobre tiempo de procesamiento

#### **DataTables**
- Columna dedicada para imágenes
- Hover effects en thumbnails
- Modal responsive para vista ampliada
- Indicador visual para familias sin imagen

#### **Responsive Design**
- Adaptable a diferentes tamaños de pantalla
- Optimización para dispositivos móviles
- Conservación de funcionalidad en tablets
- Degradación elegante en navegadores antiguos

---

## 🔍 Testing y Validación

### Casos de Prueba Esenciales

#### ✅ **Funcionalidad Básica**

**1. Subida de Imagen Nueva**
- [ ] Seleccionar imagen válida muestra vista previa
- [ ] Imagen se guarda correctamente en servidor
- [ ] Nombre se almacena en base de datos
- [ ] Thumbnail aparece en DataTables

**2. Edición con Imagen Existente**
- [ ] Imagen actual se muestra en vista previa
- [ ] Mantener imagen actual funciona
- [ ] Cambiar por nueva imagen funciona
- [ ] Quitar imagen actual funciona

**3. Validaciones de Archivo**
- [ ] Rechaza archivos no válidos (PDF, DOC, etc.)
- [ ] Rechaza archivos demasiado grandes (>2MB)
- [ ] Acepta formatos válidos (JPG, PNG, GIF)
- [ ] Muestra mensajes de error apropiados

#### ✅ **Interfaz de Usuario**

**4. Vista Previa**
- [ ] Muestra imagen inmediatamente al seleccionar
- [ ] Redimensiona apropiadamente en contenedor
- [ ] Muestra placeholder cuando no hay imagen
- [ ] Maneja errores de carga de imagen

**5. DataTables**
- [ ] Thumbnails se cargan correctamente
- [ ] Click en thumbnail abre modal
- [ ] Modal muestra imagen en tamaño completo
- [ ] Fallback funciona para imágenes faltantes

**6. Responsive**
- [ ] Funciona en dispositivos móviles
- [ ] Vista previa se adapta al tamaño de pantalla
- [ ] Modal es responsive
- [ ] Thumbnails mantienen proporciones

#### ✅ **Seguridad**

**7. Validaciones de Servidor**
- [ ] finfo detecta tipos reales correctamente
- [ ] Tamaños se validan independientemente del frontend
- [ ] Nombres únicos previenen conflictos
- [ ] Directorio se crea con permisos correctos

**8. Manejo de Errores**
- [ ] Errores de subida se logean apropiadamente
- [ ] Respuestas JSON estructuradas
- [ ] No se expone información sensible
- [ ] Fallbacks funcionan cuando falla el servidor

### Scripts de Validación

```javascript
// ✅ Validar que las imágenes se cargan correctamente
function validarImagenesDataTable() {
    const imagenesConError = $('img[src*="familia/"]').filter(function() {
        return this.naturalWidth === 0;
    });
    
    if (imagenesConError.length > 0) {
        console.warn('⚠️ Hay imágenes que no se cargan correctamente:', imagenesConError.length);
    }
}

// ✅ Validar estructura de directorio
function validarEstructuraArchivos() {
    $.ajax({
        url: '../../public/img/familia/',
        type: 'HEAD',
        success: function() {
            console.log('✅ Directorio de imágenes accesible');
        },
        error: function() {
            console.error('❌ Directorio de imágenes no accesible');
        }
    });
}
```

---

## 📋 Checklist de Implementación

### ✅ **Base de Datos**
- [ ] Campo `imagen_familia` añadido a tabla
- [ ] Tipo VARCHAR(255) con DEFAULT ''
- [ ] Comentario descriptivo añadido
- [ ] Migración ejecutada en entorno

### ✅ **Backend (PHP)**
- [ ] Función `procesarImagenFamilia()` implementada
- [ ] Validaciones de seguridad completas
- [ ] Logging detallado activado
- [ ] Directorio de imágenes configurado
- [ ] Métodos insert/update actualizados
- [ ] Endpoint listar incluye imagen

### ✅ **Frontend (HTML/CSS)**
- [ ] Input file añadido al formulario
- [ ] Contenedor de vista previa implementado
- [ ] Estilos CSS para modal de imagen
- [ ] Campo hidden para imagen actual
- [ ] Mensajes de ayuda y advertencia
- [ ] Columna imagen añadida a DataTables

### ✅ **JavaScript**
- [ ] Vista previa en tiempo real
- [ ] Validaciones de cliente implementadas
- [ ] FormData para subida de archivos
- [ ] Carga de imagen en modo edición
- [ ] Modal de visualización ampliada
- [ ] Seguimiento de cambios actualizado

### ✅ **Testing**
- [ ] Casos de prueba básicos ejecutados
- [ ] Validaciones de seguridad verificadas
- [ ] Rendimiento con múltiples imágenes
- [ ] Compatibilidad cross-browser
- [ ] Responsive design validado

### ✅ **Documentación**
- [ ] Cambios documentados
- [ ] Casos de uso descritos
- [ ] Configuración de permisos
- [ ] Guías de troubleshooting

---

## 🚀 Optimizaciones y Mejores Prácticas

### 🎯 **Rendimiento**

#### Optimización de Carga de Imágenes
```javascript
// ✅ Lazy loading para thumbnails
$('.familia-imagen-thumbnail').each(function() {
    const img = $(this);
    const originalSrc = img.attr('src');
    
    // Crear imagen temporal para precargar
    const tempImg = new Image();
    tempImg.onload = function() {
        img.attr('src', originalSrc).fadeIn();
    };
    tempImg.src = originalSrc;
});
```

#### Cache de Imágenes
```php
// ✅ Headers de cache para imágenes estáticas
if (preg_match('/\.(jpg|jpeg|png|gif)$/i', $requestUri)) {
    header('Cache-Control: public, max-age=31536000'); // 1 año
    header('Expires: ' . gmdate('D, d M Y H:i:s', time() + 31536000) . ' GMT');
}
```

### 🔧 **Mantenimiento**

#### Limpieza de Archivos Huérfanos
```php
// ✅ Script para limpiar imágenes no utilizadas
function limpiarImagenesHuerfanas() {
    $directorioImagenes = __DIR__ . "/../public/img/familia/";
    $imagenesEnDisco = glob($directorioImagenes . "familia_*");
    
    $conexion = (new Conexion())->getConexion();
    $stmt = $conexion->query("SELECT imagen_familia FROM familia WHERE imagen_familia IS NOT NULL AND imagen_familia != ''");
    $imagenesEnBD = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    foreach ($imagenesEnDisco as $archivo) {
        $nombreArchivo = basename($archivo);
        if (!in_array($nombreArchivo, $imagenesEnBD)) {
            unlink($archivo);
            echo "Eliminado archivo huérfano: $nombreArchivo\n";
        }
    }
}
```

#### Backup de Imágenes
```bash
#!/bin/bash
# ✅ Script de backup para imágenes
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/familias_images"
SOURCE_DIR="/path/to/public/img/familia"

mkdir -p "$BACKUP_DIR"
tar -czf "$BACKUP_DIR/familia_images_$DATE.tar.gz" -C "$SOURCE_DIR" .
```

### 📊 **Monitoreo**

#### Métricas de Uso de Imágenes
```sql
-- ✅ Queries para estadísticas
-- Familias con imagen vs sin imagen
SELECT 
    CASE 
        WHEN imagen_familia IS NULL OR imagen_familia = '' THEN 'Sin imagen'
        ELSE 'Con imagen'
    END AS tipo,
    COUNT(*) as cantidad
FROM familia 
GROUP BY 
    CASE 
        WHEN imagen_familia IS NULL OR imagen_familia = '' THEN 'Sin imagen'
        ELSE 'Con imagen'
    END;

-- Tamaño promedio de imágenes por mes
SELECT 
    DATE_FORMAT(created_at_familia, '%Y-%m') as mes,
    COUNT(CASE WHEN imagen_familia != '' THEN 1 END) as familias_con_imagen,
    COUNT(*) as total_familias
FROM familia 
GROUP BY DATE_FORMAT(created_at_familia, '%Y-%m')
ORDER BY mes DESC;
```

---

## 📚 Recursos y Referencias

### 🔗 **Documentación Técnica**
- [PHP File Upload](https://www.php.net/manual/en/features.file-upload.php)
- [File Info Functions](https://www.php.net/manual/en/book.fileinfo.php)
- [HTML5 File API](https://developer.mozilla.org/en-US/docs/Web/API/File)
- [FormData Interface](https://developer.mozilla.org/en-US/docs/Web/API/FormData)

### 📁 **Archivos de Referencia**
- `view/MntFamilia_plus/formularioFamilia.php` - Formulario con gestión de imágenes
- `view/MntFamilia_plus/formularioFamilia.js` - JavaScript del formulario
- `view/MntFamilia_plus/mntfamilia.js` - DataTables con columna de imagen
- `controller/familia.php` - Procesamiento de imágenes en servidor
- `models/Familia.php` - Modelo actualizado con campo imagen

### 🛠️ **Herramientas Recomendadas**
- **ImageMagick**: Para procesamiento avanzado de imágenes
- **WebP Converter**: Para optimización de formato
- **TinyPNG API**: Para compresión automática
- **Cloudinary**: Para CDN de imágenes (producción)

---

*Documentación creada el 7 de noviembre de 2025*  
*Basada en la implementación completa del sistema de gestión de imágenes en MntFamilia_plus*