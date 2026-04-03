# SPEC: Campo Imagen con Preview

> Implementación reutilizable de campo de subida de imagen con preview en formularios PHP MVC.  
> Basado en la implementación de `imagen_articulo` en MDR.

---

## ⚙️ VARIABLES DE CONFIGURACIÓN

> **Antes de implementar**, responde estas preguntas y sustituye las variables en todos los archivos.

| Variable | Descripción | Ejemplo MDR |
|----------|-------------|-------------|
| `{{ENTIDAD}}` | Nombre de la clase/modelo | `Articulo` |
| `{{entidad}}` | Nombre en minúsculas (tabla, prefijo) | `articulo` |
| `{{CAMPO_IMAGEN}}` | Nombre del campo en BD | `imagen_articulo` |
| `{{CARPETA_IMGS}}` | Subcarpeta dentro de `public/img/` | `articulo` |
| `{{PREFIJO_ARCHIVO}}` | Prefijo del nombre de archivo generado | `articulo_` |
| `{{RUTA_CONTROLLER}}` | Ruta URL al controller desde la vista | `../../controller/articulo.php` |
| `{{RUTA_IMGS_VISTA}}` | Ruta URL a las imágenes desde la vista | `../../public/img/articulo/` |
| `{{TAMANIO_MAX_MB}}` | Tamaño máximo permitido en MB | `5` |
| `{{FORMATOS_PERMITIDOS}}` | Extensiones aceptadas | `jpg, jpeg, png` |
| `{{PLACEHOLDER_ICON}}` | Icono Font Awesome cuando no hay imagen | `fa-image` |

### Preguntas de personalización

Antes de generar cualquier archivo, hacer estas preguntas:

1. ¿A qué entidad/tabla pertenece el campo imagen? → define `{{ENTIDAD}}` y `{{entidad}}`
2. ¿Cómo se llama el campo en la BD? (convención: `imagen_{{entidad}}`) → `{{CAMPO_IMAGEN}}`
3. ¿En qué subcarpeta de `public/img/` se guardarán las imágenes? → `{{CARPETA_IMGS}}`
4. ¿Cuántos MB de máximo? (recomendado: 5) → `{{TAMANIO_MAX_MB}}`
5. ¿Qué formatos se admiten? (recomendado: jpg, png) → `{{FORMATOS_PERMITIDOS}}`

---

## 🗄️ BASE DE DATOS

### Campo a añadir en la tabla

```sql
`{{CAMPO_IMAGEN}}` VARCHAR(255) DEFAULT NULL COMMENT 'Nombre del archivo de imagen (solo nombre, no ruta)',
```

**Reglas:**
- Guarda **solo el nombre del archivo**, nunca la ruta completa
- `DEFAULT NULL` — imagen opcional, nunca obligatoria
- VARCHAR(255) es suficiente para nombres generados con `uniqid()`

---

## 📁 ESTRUCTURA DE ARCHIVOS

```
public/
└── img/
    └── {{CARPETA_IMGS}}/   ← Se crea automáticamente en el primer upload
```

---

## 🖼️ VISTA: HTML del campo

Insertar dentro del formulario en una fila Bootstrap de 2 columnas (input izquierda, preview derecha):

```html
<!-- COLUMNA IZQUIERDA: INPUT -->
<div class="col-12 col-md-6">
    <label for="{{CAMPO_IMAGEN}}" class="form-label">Imagen:</label>
    <input type="file"
           class="form-control"
           name="{{CAMPO_IMAGEN}}"
           id="{{CAMPO_IMAGEN}}"
           accept="image/jpeg, image/png">

    <!-- Preserva la imagen actual en modo edición -->
    <input type="hidden" name="imagen_actual" id="imagen_actual">

    <div class="invalid-feedback small-invalid-feedback">
        Seleccione una imagen válida (JPG, PNG)
    </div>
    <small class="form-text text-muted">
        Imagen opcional (máximo {{TAMANIO_MAX_MB}}MB, formatos: JPG, PNG)
    </small>
    <div class="alert alert-warning mt-2 py-2" role="alert">
        <i class="fas fa-exclamation-triangle me-2"></i>
        <small>
            <strong>Advertencia:</strong> La imagen tardará unos segundos en procesarse,
            no cierre esta ventana.
        </small>
    </div>
</div>

<!-- COLUMNA DERECHA: PREVIEW -->
<div class="col-12 col-md-6">
    <label class="form-label">Vista previa:</label>
    <div class="image-preview-container"
         style="border: 2px dashed #ddd; border-radius: 8px; padding: 10px;
                text-align: center; min-height: 120px;
                display: flex; align-items: center; justify-content: center;">
        <div id="image-preview">
            <i class="fas fa-{{PLACEHOLDER_ICON}} text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        </div>
    </div>
</div>
```

**Puntos clave:**
- `accept="image/jpeg, image/png"` — filtra en el explorador de archivos del navegador (solo visual, no es validación de seguridad)
- `name="imagen_actual"` (hidden) — transporta el nombre del archivo actual al hacer submit en edición
- El contenedor preview tiene `min-height: 120px` para no colapsar cuando está vacío

---

## 💻 JAVASCRIPT: Preview y envío

En el archivo JS del formulario:

### Funciones de preview

```javascript
// Estado vacío (sin imagen seleccionada ni existente)
function showDefaultImagePreview() {
    const preview = document.getElementById('image-preview');
    if (preview) {
        preview.innerHTML = `
            <i class="fas fa-{{PLACEHOLDER_ICON}} text-muted" style="font-size: 3rem;"></i>
            <p class="text-muted mt-2 mb-0">Sin imagen</p>
        `;
    }
}

// Mostrar imagen existente al abrir en modo edición
function showExistingImage(imagePath) {
    const preview = document.getElementById('image-preview');
    if (!preview) return;
    if (!imagePath) { showDefaultImagePreview(); return; }
    preview.innerHTML = `
        <img src="{{RUTA_IMGS_VISTA}}${imagePath}"
             alt="Imagen actual"
             style="max-width: 100%; max-height: 100px; border-radius: 4px;"
             onerror="this.onerror=null; showDefaultImagePreview();">
        <p class="text-muted mt-1 mb-0 small">${imagePath}</p>
    `;
}

// Preview inmediato al seleccionar un archivo nuevo (antes de guardar)
$('#{{CAMPO_IMAGEN}}').on('change', function () {
    const file = this.files[0];
    if (!file) { showDefaultImagePreview(); return; }
    const reader = new FileReader();
    reader.onload = function (e) {
        document.getElementById('image-preview').innerHTML = `
            <img src="${e.target.result}"
                 alt="Vista previa"
                 style="max-width: 100%; max-height: 100px; border-radius: 4px;">
        `;
    };
    reader.readAsDataURL(file);
});
```

### Carga de datos en modo edición

Llamar dentro del `.done()` de la petición `op=mostrar`:

```javascript
if (data.{{CAMPO_IMAGEN}}) {
    $('#imagen_actual').val(data.{{CAMPO_IMAGEN}});
    showExistingImage(data.{{CAMPO_IMAGEN}});
} else {
    $('#imagen_actual').val('');
    showDefaultImagePreview();
}
```

### Envío AJAX con FormData

> ⚠️ **Obligatorio usar `FormData`** — `$.serialize()` no incluye archivos.

```javascript
function guardaryeditar() {
    const formData = new FormData($('#frm{{ENTIDAD}}')[0]);
    // FormData serializa automáticamente todos los inputs, incluido el file y el hidden

    $.ajax({
        url: '{{RUTA_CONTROLLER}}?op=guardaryeditar',
        method: 'POST',
        data: formData,
        contentType: false,   // ← Obligatorio: deja que el navegador gestione el boundary
        processData: false,   // ← Obligatorio: evita que jQuery serialice el FormData
        dataType: 'json',
        success: function (response) {
            if (response.success) {
                Swal.fire('Éxito', response.message, 'success');
                $('#modalFormulario').modal('hide');
                tabla.ajax.reload();
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function () {
            Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
        }
    });
}
```

---

## 🔧 CONTROLLER: Función de procesamiento

Añadir **fuera del switch**, al inicio del controller:

```php
/**
 * Valida y mueve una imagen subida al directorio destino.
 *
 * @param array       $archivo   Entrada de $_FILES
 * @param string      $prefijo   Prefijo para el nombre del archivo generado
 * @param string      $carpeta   Subcarpeta en public/img/ donde se guarda
 * @param string|null $errorMsg  Mensaje de error (pasado por referencia)
 * @return string|false          Nombre del archivo generado, o false si hay error
 */
function procesarImagen(array $archivo, string $prefijo, string $carpeta, ?string &$errorMsg = null)
{
    // 1. Validar código de error de PHP
    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        $errores = [
            UPLOAD_ERR_INI_SIZE   => 'El archivo excede el tamaño máximo permitido por PHP (php.ini)',
            UPLOAD_ERR_FORM_SIZE  => 'El archivo excede el tamaño máximo del formulario',
            UPLOAD_ERR_PARTIAL    => 'El archivo se subió parcialmente',
            UPLOAD_ERR_NO_FILE    => 'No se recibió ningún archivo',
            UPLOAD_ERR_NO_TMP_DIR => 'Falta la carpeta temporal de PHP',
            UPLOAD_ERR_CANT_WRITE => 'No se pudo escribir el archivo en disco',
            UPLOAD_ERR_EXTENSION  => 'Una extensión PHP detuvo la subida',
        ];
        $errorMsg = $errores[$archivo['error']] ?? 'Error desconocido en la subida';
        return false;
    }

    // 2. Verificar que el archivo viene de un upload real (previene ataques)
    if (!is_uploaded_file($archivo['tmp_name'])) {
        $errorMsg = 'El archivo no fue subido correctamente';
        return false;
    }

    // 3. Crear / verificar directorio destino
    $directorio = __DIR__ . "/../public/img/{$carpeta}/";
    if (!is_dir($directorio)) {
        if (!mkdir($directorio, 0777, true)) {
            $errorMsg = 'No se pudo crear el directorio de destino';
            return false;
        }
    }
    if (!is_writable($directorio)) {
        $errorMsg = 'El directorio no tiene permisos de escritura';
        return false;
    }

    // 4. Validar extensión (whitelist)
    $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
    $extensionesPermitidas = ['jpg', 'jpeg', 'png'];
    if (!in_array($extension, $extensionesPermitidas)) {
        $errorMsg = "Extensión no permitida: .{$extension} (permitidas: " . implode(', ', $extensionesPermitidas) . ')';
        return false;
    }

    // 5. Validar tamaño
    $maxBytes = {{TAMANIO_MAX_MB}} * 1024 * 1024;
    if ($archivo['size'] > $maxBytes) {
        $mb = round($archivo['size'] / (1024 * 1024), 2);
        $errorMsg = "Archivo demasiado grande: {$mb}MB (máximo: {{TAMANIO_MAX_MB}}MB)";
        return false;
    }

    // 6. Validar MIME real (no confiar solo en extensión ni en el Content-Type del navegador)
    $mimePermitidos = ['image/jpeg', 'image/jpg', 'image/pjpeg', 'image/png'];
    $finfo   = finfo_open(FILEINFO_MIME_TYPE);
    $mimeReal = finfo_file($finfo, $archivo['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mimeReal, $mimePermitidos)) {
        $errorMsg = "Tipo de archivo no permitido: {$mimeReal}";
        return false;
    }

    // 7. Generar nombre único y mover
    $nombreArchivo = $prefijo . uniqid() . '.' . $extension;
    if (!move_uploaded_file($archivo['tmp_name'], $directorio . $nombreArchivo)) {
        $errorMsg = 'No se pudo mover el archivo al destino. Verificar permisos.';
        return false;
    }

    return $nombreArchivo;
}
```

### Bloque de imagen en `case "guardaryeditar"`

Añadir al inicio del case, antes de llamar al modelo:

```php
case "guardaryeditar":

    $id_{{entidad}} = $_POST['id_{{entidad}}'] ?? null;
    // ... sanitizar otros campos ...

    // ── GESTIÓN DE IMAGEN ────────────────────────────────────
    $imagen = null;

    if (isset($_FILES['{{CAMPO_IMAGEN}}']) &&
        $_FILES['{{CAMPO_IMAGEN}}']['error'] === UPLOAD_ERR_OK) {

        // Escenario A: nueva imagen subida
        $errorMsgImagen = null;
        $imagen = procesarImagen(
            $_FILES['{{CAMPO_IMAGEN}}'],
            '{{PREFIJO_ARCHIVO}}',
            '{{CARPETA_IMGS}}',
            $errorMsgImagen
        );

        if ($imagen === false) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al procesar la imagen: ' . ($errorMsgImagen ?? 'Error desconocido')
            ], JSON_UNESCAPED_UNICODE);
            break;
        }

        // Borrar imagen anterior si existía
        $imagenAnterior = basename($_POST['imagen_actual'] ?? '');
        if ($imagenAnterior !== '') {
            $rutaAnterior = __DIR__ . "/../public/img/{{CARPETA_IMGS}}/" . $imagenAnterior;
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }

    } elseif (!empty($_POST['imagen_actual'])) {
        // Escenario B: sin nueva imagen → preservar la actual
        $imagen = basename($_POST['imagen_actual']);
    }
    // Escenario C: sin imagen → $imagen queda null
    // ─────────────────────────────────────────────────────────

    // Continuar con INSERT o UPDATE pasando $imagen al modelo...
```

**Nota de seguridad:** `basename()` al leer `imagen_actual` previene path traversal.

---

## 🏗️ MODELO: Cambios necesarios

### `insert_{{entidad}}()`

```php
public function insert_{{entidad}}(
    // ... otros parámetros ...,
    ${{CAMPO_IMAGEN}} = null
) {
    $sql = "INSERT INTO {{entidad}} (
                ...,
                {{CAMPO_IMAGEN}},
                created_at_{{entidad}}
            ) VALUES (
                ..., ?, NOW()
            )";

    $stmt = $this->conexion->prepare($sql);
    // ... otros bindValue ...
    $stmt->bindValue($n, ${{CAMPO_IMAGEN}}, !empty(${{CAMPO_IMAGEN}}) ? PDO::PARAM_STR : PDO::PARAM_NULL);
```

### `update_{{entidad}}()`

```php
public function update_{{entidad}}(
    $id_{{entidad}},
    // ... otros parámetros ...,
    ${{CAMPO_IMAGEN}} = null
) {
    $sql = "UPDATE {{entidad}} SET
                ...,
                {{CAMPO_IMAGEN}} = ?,
                updated_at_{{entidad}} = NOW()
            WHERE id_{{entidad}} = ?";

    $stmt = $this->conexion->prepare($sql);
    // ... otros bindValue ...
    $stmt->bindValue($n, ${{CAMPO_IMAGEN}}, !empty(${{CAMPO_IMAGEN}}) ? PDO::PARAM_STR : PDO::PARAM_NULL);
```

---

## 📊 VISUALIZACIÓN EN DATATABLE (opcional)

Columna de imagen en el JS del listado:

```javascript
{
    data: '{{CAMPO_IMAGEN}}',
    orderable: false,
    render: function (data, type, row) {
        if (data) {
            return `<img src="{{RUTA_IMGS_VISTA}}${data}"
                        alt="Imagen"
                        style="max-width:50px; max-height:50px; border-radius:4px; cursor:pointer;"
                        onclick="verImagenCompleta('{{RUTA_IMGS_VISTA}}${data}', '${row.nombre_{{entidad}}}')">`;
        }
        return '<span class="text-muted small">Sin imagen</span>';
    }
}
```

Función para ver en modal:

```javascript
function verImagenCompleta(url, titulo) {
    Swal.fire({
        title: titulo,
        imageUrl: url,
        imageWidth: 400,
        imageAlt: titulo
    });
}
```

---

## ✅ CHECKLIST DE IMPLEMENTACIÓN

### Base de datos
- [ ] Campo `{{CAMPO_IMAGEN}} VARCHAR(255) DEFAULT NULL` añadido a la tabla
- [ ] (Opcional) Migración documentada en `BD/migrations/`

### Permisos y carpetas
- [ ] Carpeta `public/img/{{CARPETA_IMGS}}/` con permisos de escritura para el servidor web
- [ ] Carpeta añadida al `.gitignore` si contiene imágenes de producción

### Modelo
- [ ] Parámetro `${{CAMPO_IMAGEN}} = null` en `insert_{{entidad}}()`
- [ ] Parámetro `${{CAMPO_IMAGEN}} = null` en `update_{{entidad}}()`
- [ ] `bindValue` correcto: `PDO::PARAM_STR` si tiene valor, `PDO::PARAM_NULL` si está vacío

### Controller
- [ ] Función `procesarImagen()` añadida fuera del switch
- [ ] Bloque de imagen en `case "guardaryeditar"` con los 3 escenarios (nueva / preservar / null)
- [ ] `basename()` al leer `imagen_actual` (seguridad: previene path traversal)
- [ ] `unlink()` de la imagen anterior al actualizar con una imagen nueva

### Vista HTML
- [ ] Input `file` con `accept="image/jpeg, image/png"`
- [ ] Input `hidden` `imagen_actual` dentro del formulario
- [ ] Contenedor `#image-preview` presente
- [ ] Aviso de espera mientras se procesa visible

### JavaScript
- [ ] `showDefaultImagePreview()` implementada
- [ ] `showExistingImage()` llamada al cargar datos en modo edición
- [ ] Evento `change` en el input file para preview inmediato (FileReader)
- [ ] Envío con `FormData` y `contentType: false`, `processData: false`

---

## 📐 Referencia MDR

| Archivo | Rol |
|---------|-----|
| [view/MntArticulos/formularioArticulo.php](../../view/MntArticulos/formularioArticulo.php) | HTML del campo y preview |
| [view/MntArticulos/formularioArticulo.js](../../view/MntArticulos/formularioArticulo.js) | Lógica JS de preview y envío |
| [view/MntArticulos/mntarticulo.js](../../view/MntArticulos/mntarticulo.js) | Renderizado en DataTable |
| [controller/articulo.php](../../controller/articulo.php) | `procesarImagenArticulo()` y case `guardaryeditar` |
| [models/Articulo.php](../../models/Articulo.php) | `insert_articulo()` y `update_articulo()` |
| `public/img/articulo/` | Carpeta física de imágenes |

---

*Spec creado el 23/03/2026 — Stack: PHP 8 MVC sin framework, Bootstrap 5, jQuery, SweetAlert2*
