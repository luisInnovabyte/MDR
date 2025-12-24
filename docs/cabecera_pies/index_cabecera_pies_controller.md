# Controller y Backend
## Sistema Cabecera-Pies - Lógica de Negocio

> **Archivo:** `controller/articulo.php`  
> **Propósito:** Gestión de operaciones CRUD y lógica de negocio

[← Volver al índice](./index_cabecera_pies.md)

---

## 📋 Tabla de Contenidos

1. [Estructura del Controller](#estructura-del-controller)
2. [Operación: listar](#operación-listar)
3. [Operación: guardaryeditar](#operación-guardaryeditar)
4. [Operación: mostrar](#operación-mostrar)
5. [Operación: eliminar](#operación-eliminar)
6. [Operación: activar](#operación-activar)
7. [Operación: estadisticas](#operación-estadisticas)
8. [Manejo de Archivos](#manejo-de-archivos)
9. [Respuestas JSON](#respuestas-json)
10. [Modelo (Articulo.php)](#modelo-articulophp)

---

## 1. Estructura del Controller

### Esquema General

```php
<?php
require_once "../config/conexion.php";
require_once "../models/Articulo.php";
require_once '../config/funciones.php';

// Funciones auxiliares
function procesarImagenArticulo($archivo, &$errorMsg = null) {
    // Procesamiento de imágenes
}

// Inicializar clases
$registro = new RegistroActividad();
$articulo = new Articulo();

// Switch principal basado en operación
switch ($_GET["op"]) {
    case "listar":
        // Código para listar
        break;
        
    case "guardaryeditar":
        // Código para INSERT/UPDATE
        break;
        
    case "mostrar":
        // Código para obtener por ID
        break;
        
    case "eliminar":
        // Código para soft delete
        break;
        
    case "activar":
        // Código para reactivar
        break;
        
    case "estadisticas":
        // Código para contadores
        break;
        
    default:
        echo json_encode([
            'success' => false,
            'message' => 'Operación no válida'
        ]);
        break;
}
?>
```

### Componentes Principales

1. **Requires**: Conexión, modelo y funciones
2. **Funciones auxiliares**: Procesamiento de archivos
3. **Instancias**: RegistroActividad y Modelo
4. **Switch**: Enrutamiento por parámetro GET

---

## 2. Operación: listar

### Código

```php
case "listar":
    try {
        // Obtener datos del modelo
        $datos = $articulo->get_articulos();
        
        // Preparar array de respuesta
        $data = array();
        
        foreach ($datos as $row) {
            $data[] = array(
                "id_articulo" => $row["id_articulo"],
                "codigo_articulo" => $row["codigo_articulo"],
                "nombre_articulo" => $row["nombre_articulo"],
                "name_articulo" => $row["name_articulo"],
                "nombre_familia" => $row["nombre_familia"],
                "codigo_familia" => $row["codigo_familia"],
                "nombre_grupo" => $row["nombre_grupo"],
                "nombre_unidad" => $row["nombre_unidad"],
                "simbolo_unidad" => $row["simbolo_unidad"],
                "precio_alquiler_articulo" => $row["precio_alquiler_articulo"],
                "es_kit_articulo" => $row["es_kit_articulo"],
                "coeficiente_efectivo" => $row["coeficiente_efectivo"],
                "coeficiente_articulo" => $row["coeficiente_articulo"],
                "coeficiente_familia" => $row["coeficiente_familia"],
                "control_total_articulo" => $row["control_total_articulo"],
                "no_facturar_articulo" => $row["no_facturar_articulo"],
                "activo_articulo" => $row["activo_articulo"],
                "imagen_articulo" => $row["imagen_articulo"],
                "imagen_familia" => $row["imagen_familia"],
                "imagen_efectiva" => $row["imagen_efectiva"],
                "notas_presupuesto_articulo" => $row["notas_presupuesto_articulo"],
                "notes_budget_articulo" => $row["notes_budget_articulo"],
                "orden_obs_articulo" => $row["orden_obs_articulo"],
                "observaciones_articulo" => $row["observaciones_articulo"],
                "jerarquia_completa" => $row["jerarquia_completa"],
                "total_elementos" => $row["total_elementos"] ?? 0,
                "created_at_articulo" => $row["created_at_articulo"],
                "updated_at_articulo" => $row["updated_at_articulo"]
            );
        }
        
        // Estructura para DataTables
        $results = array(
            "draw" => intval($_GET['draw'] ?? 1),
            "recordsTotal" => count($data),
            "recordsFiltered" => count($data),
            "data" => $data
        );
        
        // Respuesta JSON
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        $registro->registrarActividad(
            'admin',
            'articulo.php',
            'listar',
            "Error: " . $e->getMessage(),
            'error'
        );
        
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener datos',
            'data' => []
        ], JSON_UNESCAPED_UNICODE);
    }
    break;
```

### Características

1. **Try-catch**: Captura errores
2. **Modelo**: Llama a `get_articulos()`
3. **Transformación**: Convierte datos a estructura DataTables
4. **Headers**: `Content-Type: application/json; charset=utf-8`
5. **JSON_UNESCAPED_UNICODE**: Mantiene caracteres especiales
6. **Logging**: Registra errores en caso de fallo

### Respuesta JSON

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
            "precio_alquiler_articulo": "25.00",
            "activo_articulo": 1,
            // ... más campos
        }
    ]
}
```

---

## 3. Operación: guardaryeditar

### Código

```php
case "guardaryeditar":
    try {
        // Determinar si es INSERT o UPDATE
        $id_articulo = !empty($_POST["id_articulo"]) ? $_POST["id_articulo"] : null;
        
        // Sanitizar datos obligatorios
        $codigo = htmlspecialchars(trim($_POST["codigo_articulo"]), ENT_QUOTES, 'UTF-8');
        $nombre = htmlspecialchars(trim($_POST["nombre_articulo"]), ENT_QUOTES, 'UTF-8');
        $name = htmlspecialchars(trim($_POST["name_articulo"]), ENT_QUOTES, 'UTF-8');
        
        // Campos opcionales: convertir vacío a null
        $id_familia = !empty($_POST["id_familia"]) ? $_POST["id_familia"] : null;
        $id_unidad = !empty($_POST["id_unidad"]) ? $_POST["id_unidad"] : null;
        $precio = !empty($_POST["precio_alquiler_articulo"]) ? 
                  $_POST["precio_alquiler_articulo"] : 0;
        
        // Campos booleanos
        $es_kit = isset($_POST["es_kit_articulo"]) ? 1 : 0;
        $control_total = isset($_POST["control_total_articulo"]) ? 1 : 0;
        $no_facturar = isset($_POST["no_facturar_articulo"]) ? 1 : 0;
        
        // Coeficiente (puede ser null para heredar de familia)
        $coeficiente = null;
        if (isset($_POST["coeficiente_articulo"])) {
            $coeficiente = $_POST["coeficiente_articulo"] === "1" ? 1 : 0;
        }
        
        // Procesar imagen si existe
        $nombreImagen = null;
        if (isset($_FILES['imagen_articulo']) && $_FILES['imagen_articulo']['error'] === UPLOAD_ERR_OK) {
            $errorMsg = null;
            $nombreImagen = procesarImagenArticulo($_FILES['imagen_articulo'], $errorMsg);
            
            if (!$nombreImagen) {
                echo json_encode([
                    'success' => false,
                    'message' => $errorMsg ?? 'Error al procesar la imagen'
                ], JSON_UNESCAPED_UNICODE);
                exit;
            }
        }
        
        if (empty($id_articulo)) {
            // INSERT
            $resultado = $articulo->insert_articulo(
                $codigo,
                $nombre,
                $name,
                $id_familia,
                $id_unidad,
                $precio,
                $es_kit,
                $coeficiente,
                $control_total,
                $no_facturar,
                $nombreImagen,
                $_POST["notas_presupuesto_articulo"] ?? null,
                $_POST["notes_budget_articulo"] ?? null,
                $_POST["orden_obs_articulo"] ?? 200,
                $_POST["observaciones_articulo"] ?? null
            );
            
            if ($resultado) {
                $registro->registrarActividad(
                    'admin',
                    'articulo.php',
                    'insert',
                    "Artículo creado: $codigo - $nombre",
                    'info'
                );
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Artículo creado correctamente',
                    'id_articulo' => $resultado
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al crear el artículo'
                ], JSON_UNESCAPED_UNICODE);
            }
            
        } else {
            // UPDATE
            $resultado = $articulo->update_articulo(
                $id_articulo,
                $codigo,
                $nombre,
                $name,
                $id_familia,
                $id_unidad,
                $precio,
                $es_kit,
                $coeficiente,
                $control_total,
                $no_facturar,
                $nombreImagen,
                $_POST["notas_presupuesto_articulo"] ?? null,
                $_POST["notes_budget_articulo"] ?? null,
                $_POST["orden_obs_articulo"] ?? 200,
                $_POST["observaciones_articulo"] ?? null
            );
            
            if ($resultado !== false) {
                $registro->registrarActividad(
                    'admin',
                    'articulo.php',
                    'update',
                    "Artículo actualizado ID: $id_articulo",
                    'info'
                );
                
                echo json_encode([
                    'success' => true,
                    'message' => 'Artículo actualizado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al actualizar el artículo'
                ], JSON_UNESCAPED_UNICODE);
            }
        }
        
    } catch (Exception $e) {
        $registro->registrarActividad(
            'admin',
            'articulo.php',
            'guardaryeditar',
            "Error: " . $e->getMessage(),
            'error'
        );
        
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    break;
```

### Puntos Clave

1. **Validación de ID**: Determina INSERT vs UPDATE
2. **Sanitización**: `htmlspecialchars()` para campos de texto
3. **Campos opcionales**: Conversión de vacío a `null`
4. **Booleanos**: `isset()` para checkboxes
5. **Archivos**: Procesamiento con función auxiliar
6. **Logging**: Registro de actividades
7. **Respuestas JSON**: Con `success` y `message`

---

## 4. Operación: mostrar

### Código

```php
case "mostrar":
    try {
        $id_articulo = $_POST["id_articulo"];
        
        $datos = $articulo->get_articuloxid($id_articulo);
        
        if ($datos) {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Artículo no encontrado'
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener el artículo'
        ], JSON_UNESCAPED_UNICODE);
    }
    break;
```

### Uso

Carga datos del artículo para el formulario de edición:

```javascript
$.post('articulo.php?op=mostrar', { id_articulo: 42 })
    .done(function(data) {
        $('#id_articulo').val(data.id_articulo);
        $('#codigo_articulo').val(data.codigo_articulo);
        // ... rellenar más campos
    });
```

---

## 5. Operación: eliminar

### Código (Soft Delete)

```php
case "eliminar":
    try {
        $id_articulo = $_POST["id_articulo"];
        
        $resultado = $articulo->delete_articuloxid($id_articulo);
        
        if ($resultado) {
            $registro->registrarActividad(
                'admin',
                'articulo.php',
                'eliminar',
                "Artículo desactivado ID: $id_articulo",
                'info'
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Artículo desactivado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al desactivar el artículo'
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    break;
```

### En el Modelo

```php
public function delete_articuloxid($id_articulo)
{
    try {
        $sql = "UPDATE articulo SET 
                    activo_articulo = 0,
                    updated_at_articulo = NOW()
                WHERE id_articulo = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        return false;
    }
}
```

**NO hace DELETE físico**, solo cambia `activo_articulo = 0`

---

## 6. Operación: activar

### Código

```php
case "activar":
    try {
        $id_articulo = $_POST["id_articulo"];
        
        $resultado = $articulo->activar_articuloxid($id_articulo);
        
        if ($resultado) {
            $registro->registrarActividad(
                'admin',
                'articulo.php',
                'activar',
                "Artículo activado ID: $id_articulo",
                'info'
            );
            
            echo json_encode([
                'success' => true,
                'message' => 'Artículo activado correctamente'
            ], JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                'success' => false,
                'message' => 'Error al activar el artículo'
            ], JSON_UNESCAPED_UNICODE);
        }
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error: ' . $e->getMessage()
        ], JSON_UNESCAPED_UNICODE);
    }
    break;
```

### En el Modelo

```php
public function activar_articuloxid($id_articulo)
{
    try {
        $sql = "UPDATE articulo SET 
                    activo_articulo = 1,
                    updated_at_articulo = NOW()
                WHERE id_articulo = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        return false;
    }
}
```

---

## 7. Operación: estadisticas

### Código

```php
case "estadisticas":
    try {
        $total = $articulo->total_articulo() ?: 0;
        $activos = $articulo->total_articulo_activo() ?: 0;
        $kits = $articulo->total_articulo_activo_kit() ?: 0;
        $coeficientes = $articulo->total_articulo_activo_coeficiente() ?: 0;
        
        echo json_encode([
            'success' => true,
            'data' => [
                'total' => $total,
                'activos' => $activos,
                'kits' => $kits,
                'coeficientes' => $coeficientes
            ]
        ], JSON_UNESCAPED_UNICODE);
        
    } catch (Exception $e) {
        echo json_encode([
            'success' => false,
            'message' => 'Error al obtener estadísticas'
        ], JSON_UNESCAPED_UNICODE);
    }
    break;
```

### Métodos del Modelo

```php
public function total_articulo()
{
    $sql = "SELECT COUNT(*) as total FROM articulo";
    $stmt = $this->conexion->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}

public function total_articulo_activo()
{
    $sql = "SELECT COUNT(*) as total FROM articulo WHERE activo_articulo = 1";
    $stmt = $this->conexion->query($sql);
    return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
}
```

---

## 8. Manejo de Archivos

### Función procesarImagenArticulo()

```php
function procesarImagenArticulo($archivo, &$errorMsg = null)
{
    try {
        // 1. Verificar errores en la subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            $errores = [
                UPLOAD_ERR_INI_SIZE => 'El archivo excede el tamaño máximo',
                UPLOAD_ERR_FORM_SIZE => 'El archivo excede el tamaño del formulario',
                UPLOAD_ERR_PARTIAL => 'El archivo se subió parcialmente',
                UPLOAD_ERR_NO_FILE => 'No se subió ningún archivo',
            ];
            $errorMsg = $errores[$archivo['error']] ?? 'Error desconocido';
            return false;
        }
        
        // 2. Verificar archivo temporal
        if (!file_exists($archivo['tmp_name'])) {
            $errorMsg = "El archivo temporal no existe";
            return false;
        }
        
        // 3. Directorio de destino
        $directorio = __DIR__ . "/../public/img/articulo/";
        
        if (!is_dir($directorio)) {
            mkdir($directorio, 0777, true);
        }
        
        // 4. Validar extensión
        $extension = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
        $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($extension, $extensionesPermitidas)) {
            $errorMsg = "Extensión no permitida. Solo: " . 
                       implode(', ', $extensionesPermitidas);
            return false;
        }
        
        // 5. Validar tipo MIME
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $archivo['tmp_name']);
        finfo_close($finfo);
        
        $tiposPermitidos = [
            'image/jpeg',
            'image/jpg',
            'image/png',
            'image/gif',
            'image/webp'
        ];
        
        if (!in_array($mimeType, $tiposPermitidos)) {
            $errorMsg = "Tipo de archivo no válido";
            return false;
        }
        
        // 6. Validar tamaño (máx. 5MB)
        $maxSize = 5 * 1024 * 1024; // 5MB
        if ($archivo['size'] > $maxSize) {
            $errorMsg = "El archivo excede el tamaño máximo de 5MB";
            return false;
        }
        
        // 7. Generar nombre único
        $nombreArchivo = time() . '_' . uniqid() . '.' . $extension;
        $rutaDestino = $directorio . $nombreArchivo;
        
        // 8. Mover archivo
        if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return $nombreArchivo;
        } else {
            $errorMsg = "Error al mover el archivo";
            return false;
        }
        
    } catch (Exception $e) {
        $errorMsg = "Error inesperado: " . $e->getMessage();
        return false;
    }
}
```

### Validaciones

1. ✅ **Error de subida**: Verifica `error !== UPLOAD_ERR_OK`
2. ✅ **Archivo temporal**: Verifica que existe
3. ✅ **Directorio**: Crea si no existe
4. ✅ **Extensión**: Solo jpg, jpeg, png, gif, webp
5. ✅ **MIME type**: Validación real del tipo
6. ✅ **Tamaño**: Máximo 5MB
7. ✅ **Nombre único**: `time()_uniqid().ext`
8. ✅ **Mover archivo**: `move_uploaded_file()`

---

## 9. Respuestas JSON

### Formato Estándar

```php
// ✅ Éxito
echo json_encode([
    'success' => true,
    'message' => 'Operación exitosa',
    'data' => $datos // Opcional
], JSON_UNESCAPED_UNICODE);

// ❌ Error
echo json_encode([
    'success' => false,
    'message' => 'Descripción del error'
], JSON_UNESCAPED_UNICODE);
```

### Headers Importantes

```php
header('Content-Type: application/json; charset=utf-8');
```

### JSON_UNESCAPED_UNICODE

```php
// ❌ Sin flag
echo json_encode(['nombre' => 'José']);
// Output: {"nombre":"Jos\u00e9"}

// ✅ Con flag
echo json_encode(['nombre' => 'José'], JSON_UNESCAPED_UNICODE);
// Output: {"nombre":"José"}
```

---

## 10. Modelo (Articulo.php)

### Métodos Estándar

```php
class Articulo
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
        $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
    }

    // Listar todos con JOIN
    public function get_articulos()
    {
        $sql = "SELECT * FROM vista_articulo_completa 
                WHERE activo_articulo = 1 
                ORDER BY nombre_familia ASC, nombre_articulo ASC";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener por ID
    public function get_articuloxid($id_articulo)
    {
        $sql = "SELECT * FROM articulo WHERE id_articulo = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Insertar
    public function insert_articulo(/* parámetros */)
    {
        $sql = "INSERT INTO articulo (campos...) VALUES (?, ?, ...)";
        
        $stmt = $this->conexion->prepare($sql);
        // bindValue para cada parámetro
        $stmt->execute();
        
        return $this->conexion->lastInsertId();
    }

    // Actualizar
    public function update_articulo($id, /* parámetros */)
    {
        $sql = "UPDATE articulo SET campos = ? WHERE id_articulo = ?";
        
        $stmt = $this->conexion->prepare($sql);
        // bindValue para cada parámetro
        $stmt->execute();
        
        return $stmt->rowCount();
    }

    // Soft Delete
    public function delete_articuloxid($id_articulo)
    {
        $sql = "UPDATE articulo SET activo_articulo = 0 WHERE id_articulo = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }

    // Activar
    public function activar_articuloxid($id_articulo)
    {
        $sql = "UPDATE articulo SET activo_articulo = 1 WHERE id_articulo = ?";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_articulo, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->rowCount() > 0;
    }
}
```

### Vista SQL (vista_articulo_completa)

```sql
CREATE OR REPLACE VIEW vista_articulo_completa AS
SELECT 
    a.*,
    f.nombre_familia,
    f.codigo_familia,
    f.imagen_familia,
    f.coeficiente_familia,
    g.nombre_grupo,
    u.nombre_unidad,
    u.simbolo_unidad,
    COALESCE(a.imagen_articulo, f.imagen_familia) as imagen_efectiva,
    COALESCE(a.coeficiente_articulo, f.coeficiente_familia) as coeficiente_efectivo,
    CONCAT(g.nombre_grupo, ' > ', f.nombre_familia, ' > ', a.nombre_articulo) as jerarquia_completa,
    (SELECT COUNT(*) FROM elemento e WHERE e.id_articulo = a.id_articulo AND e.activo_elemento = 1) as total_elementos
FROM articulo a
LEFT JOIN familia f ON a.id_familia = f.id_familia
LEFT JOIN grupo_articulo g ON f.id_grupo = g.id_grupo
LEFT JOIN unidad u ON a.id_unidad = u.id_unidad
WHERE a.activo_articulo = 1;
```

---

## ✅ Checklist de Controller

- [ ] Switch por operación GET
- [ ] Try-catch en cada case
- [ ] Sanitización de inputs
- [ ] Validación de campos obligatorios
- [ ] Conversión de vacíos a null
- [ ] Prepared statements en modelo
- [ ] Respuestas JSON con success/message
- [ ] Headers Content-Type correcto
- [ ] JSON_UNESCAPED_UNICODE para caracteres especiales
- [ ] Logging de actividades
- [ ] Manejo de archivos con validaciones
- [ ] Soft delete en vez de DELETE físico

---

[← Anterior: Funciones JS](./index_cabecera_pies_js_funciones.md) | [Siguiente: Formulario →](./index_cabecera_pies_formulario.md)
