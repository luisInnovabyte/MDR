# Guía para Implementar Campos Select de Tablas Relacionadas

**Fecha:** 7 de noviembre de 2025  
**Basado en:** Implementación del campo `id_unidad_familia` en MntFamilia_unidad  
**Objetivo:** Documentar el proceso completo para añadir campos select dinámicos desde tablas relacionadas

---

## 📋 Resumen del Proceso

Cuando necesites añadir un campo que viene de otra tabla (relación FK), debes seguir estos pasos:

1. ✅ **Verificar estructura de BD** - Confirmar que existe la relación FK
2. ✅ **Crear modelo para tabla relacionada** - Clase PHP con métodos de acceso
3. ✅ **Crear controlador para tabla relacionada** - Endpoints API REST
4. ✅ **Actualizar modelo principal** - Modificar métodos CRUD
5. ✅ **Actualizar controlador principal** - Incluir nuevo campo en operaciones
6. ✅ **Modificar vista (HTML)** - Añadir campo select al formulario
7. ✅ **Actualizar JavaScript** - Funciones de carga y manejo del select
8. ✅ **Crear datos de ejemplo** - Poblar tabla relacionada para testing
9. ✅ **Documentar cambios** - Registrar implementación

---

## 🏗️ Paso 1: Verificar Estructura de Base de Datos

### 1.1 Confirmar Relación en Tabla Principal

```sql
-- Ejemplo: tabla familia con FK a unidad_medida
CREATE TABLE familia (
    id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_familia VARCHAR(20) NOT NULL UNIQUE,
    nombre_familia VARCHAR(100) NOT NULL,
    -- ... otros campos ...
    id_unidad_familia INT UNSIGNED,  -- ✅ Campo FK
    created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (id_unidad_familia) REFERENCES unidad_medida(id_unidad)  -- ✅ Relación FK
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### 1.2 Verificar Tabla Relacionada

```sql
-- Ejemplo: tabla unidad_medida
CREATE TABLE unidad_medida (
    id_unidad INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_unidad VARCHAR(50) NOT NULL,
    name_unidad VARCHAR(50) NOT NULL,
    descr_unidad VARCHAR(255),
    simbolo_unidad VARCHAR(10),
    activo_unidad BOOLEAN DEFAULT TRUE,  -- ✅ Campo para filtrar registros activos
    created_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_unidad TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);
```

**🔑 Elementos clave:**
- Campo `id` como PK
- Campo `activo_*` para filtrar registros disponibles
- Campos descriptivos (`nombre_*`, `descr_*`)
- Campo opcional con símbolo o código (`simbolo_*`)

---

## 🏛️ Paso 2: Crear Modelo para Tabla Relacionada

### 2.1 Estructura del Archivo Modelo

**Archivo:** `models/[NombreTabla].php`

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class UnidadMedida  // ✅ Nombre descriptivo de la clase
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
    }

    // ✅ Método principal: obtener registros activos para select
    public function get_unidades_disponibles()
    {
        try {
            $sql = "SELECT id_unidad, nombre_unidad, name_unidad, descr_unidad, simbolo_unidad 
                    FROM unidad_medida 
                    WHERE activo_unidad = 1 
                    ORDER BY nombre_unidad ASC";  // ✅ Ordenar alfabéticamente
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'UnidadMedida',
                'get_unidades_disponibles',
                "Error al listar las unidades de medida disponibles: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    // ✅ Método opcional: obtener registro específico por ID
    public function get_unidadxid($id_unidad)
    {
        try {
            $sql = "SELECT * FROM unidad_medida WHERE id_unidad = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_unidad, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'UnidadMedida',
                'get_unidadxid',
                "Error al mostrar la unidad de medida {$id_unidad}: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }
}
?>
```

**🎯 Métodos esenciales:**
- `get_[tabla]_disponibles()`: Lista registros activos para el select
- `get_[tabla]xid()`: Obtiene un registro específico (opcional)

---

## 🎛️ Paso 3: Crear Controlador para Tabla Relacionada

### 3.1 Estructura del Controlador

**Archivo:** `controller/[nombre_tabla].php`

```php
<?php
require_once "../config/conexion.php";
require_once "../models/UnidadMedida.php";  // ✅ Incluir modelo correspondiente
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$unidadMedida = new UnidadMedida();  // ✅ Instanciar clase del modelo

switch ($_GET["op"]) {
    // ✅ Endpoint principal: listar registros disponibles para select
    case "listarDisponibles":
        $datos = $unidadMedida->get_unidades_disponibles();
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;
        
    // ✅ Endpoint opcional: obtener registro específico
    case "mostrar":
        header('Content-Type: application/json; charset=utf-8');
        $datos = $unidadMedida->get_unidadxid($_POST["id_unidad"]);
        if ($datos) {
            echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "No se pudo obtener la unidad de medida solicitada"
            ]);
        }
        break;
}
?>
```

**🔑 Endpoints necesarios:**
- `listarDisponibles`: Para poblar el select
- `mostrar`: Para obtener detalles (opcional)

---

## 🔧 Paso 4: Actualizar Modelo Principal

### 4.1 Modificar Método Insert

**Archivo:** `models/Familia.php`

```php
// ✅ ANTES - Sin campo FK
public function insert_familia($nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '')

// ✅ DESPUÉS - Con campo FK
public function insert_familia($nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '', $id_unidad_familia = null)
{
    try {
        // ✅ Actualizar SQL para incluir nuevo campo
        $sql = "INSERT INTO familia (codigo_familia, nombre_familia, name_familia, descr_familia, activo_familia, imagen_familia, id_unidad_familia, created_at_familia, updated_at_familia) 
                VALUES (?, ?, ?, ?, 1, ?, ?, NOW(), NOW())";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $codigo_familia, PDO::PARAM_STR);
        $stmt->bindValue(2, $nombre_familia, PDO::PARAM_STR);
        $stmt->bindValue(3, $name_familia, PDO::PARAM_STR);
        $stmt->bindValue(4, $descr_familia, PDO::PARAM_STR);
        $stmt->bindValue(5, $imagen_familia, PDO::PARAM_STR);
        $stmt->bindValue(6, $id_unidad_familia, PDO::PARAM_INT);  // ✅ Nuevo parámetro
        $stmt->execute();
        
        // ... resto del método
    } catch (PDOException $e) {
        // ... manejo de errores
    }
}
```

### 4.2 Modificar Método Update

```php
// ✅ ANTES - Sin campo FK
public function update_familia($id_familia, $nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '')

// ✅ DESPUÉS - Con campo FK  
public function update_familia($id_familia, $nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '', $id_unidad_familia = null)
{
    try {
        // ✅ Actualizar SQL para incluir nuevo campo
        $sql = "UPDATE familia SET nombre_familia = ?, codigo_familia = ?, name_familia = ?, descr_familia = ?, imagen_familia = ?, id_unidad_familia = ?, updated_at_familia = NOW() WHERE id_familia = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $nombre_familia, PDO::PARAM_STR);
        $stmt->bindValue(2, $codigo_familia, PDO::PARAM_STR);
        $stmt->bindValue(3, $name_familia, PDO::PARAM_STR);
        $stmt->bindValue(4, $descr_familia, PDO::PARAM_STR);
        $stmt->bindValue(5, $imagen_familia, PDO::PARAM_STR);
        $stmt->bindValue(6, $id_unidad_familia, PDO::PARAM_INT);  // ✅ Nuevo parámetro
        $stmt->bindValue(7, $id_familia, PDO::PARAM_INT);
        $stmt->execute();
        
        // ... resto del método
    } catch (PDOException $e) {
        // ... manejo de errores
    }
}
```

---

## 🎛️ Paso 5: Actualizar Controlador Principal

### 5.1 Modificar Caso "guardaryeditar"

**Archivo:** `controller/familia_unidad.php`

```php
case "guardaryeditar":
    try {
        // ✅ Capturar nuevo campo del formulario
        $nombre_familia = $_POST["nombre_familia"] ?? '';
        $codigo_familia = $_POST["codigo_familia"] ?? '';
        $name_familia = $_POST["name_familia"] ?? '';
        $descr_familia = $_POST["descr_familia"] ?? '';
        $activo_familia = isset($_POST["activo_familia"]) ? (int)$_POST["activo_familia"] : 1;
        $id_unidad_familia = $_POST["id_unidad_familia"] ?? null;  // ✅ Nuevo campo
        
        // ... procesamiento de imagen ...
        
        if (empty($_POST["id_familia"])) {
            // ✅ Insertar - incluir nuevo parámetro
            $resultado = $familia->insert_familia(
                $nombre_familia,
                $codigo_familia,
                $name_familia,
                $descr_familia,
                $imagen_familia,
                $id_unidad_familia  // ✅ Nuevo parámetro
            );
        } else {
            // ✅ Actualizar - incluir nuevo parámetro
            $resultado = $familia->update_familia(
                $_POST["id_familia"],
                $nombre_familia,
                $codigo_familia,
                $name_familia,
                $descr_familia,
                $imagen_familia,
                $id_unidad_familia  // ✅ Nuevo parámetro
            );
        }
        
        // ... manejo de respuesta ...
    } catch (Exception $e) {
        // ... manejo de errores ...
    }
    break;
```

### 5.2 Modificar Caso "listar"

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
            "imagen_familia" => $row["imagen_familia"] ?? '',
            "activo_familia" => $row["activo_familia"],
            "id_unidad_familia" => $row["id_unidad_familia"],  // ✅ Incluir nuevo campo
            "created_at_familia" => $row["created_at_familia"],
            "updated_at_familia" => $row["updated_at_familia"]
        );
    }
    // ... resto del método ...
    break;
```

---

## 🎨 Paso 6: Modificar Vista (HTML)

### 6.1 Añadir Campo Select al Formulario

**Archivo:** `view/[Modulo]/formulario[Entidad].php`

```html
<!-- ✅ Añadir dentro de una row existente o crear nueva -->
<div class="col-12 col-md-6">
    <label for="id_unidad_familia" class="form-label">Unidad de medida:</label>
    <select class="form-control" name="id_unidad_familia" id="id_unidad_familia">
        <option value="">Seleccionar unidad de medida...</option>
        <!-- ✅ Las opciones se cargarán dinámicamente vía JavaScript -->
    </select>
    <div class="invalid-feedback small-invalid-feedback">Seleccione una unidad de medida válida</div>
    <small class="form-text text-muted">
        Unidad de medida asociada a esta familia
        <!-- ✅ Div opcional para mostrar descripción adicional -->
        <div id="unidad-descripcion" class="mt-1 p-2 bg-light border rounded" style="display: none;">
            <strong>Descripción:</strong> <span id="unidad-descr-text"></span>
        </div>
    </small>
</div>
```

### 6.2 Actualizar Modal de Ayuda (Opcional)

```html
<!-- ✅ Añadir sección en el modal de ayuda -->
<div class="col-12">
    <h6 class="text-primary"><i class="fas fa-balance-scale me-2"></i>Unidad de Medida</h6>
    <p><strong>Campo opcional.</strong> Unidad de medida asociada a esta familia de productos.</p>
    <ul class="list-unstyled ms-3">
        <li><i class="fas fa-list text-info me-2"></i>Seleccione de la lista de unidades disponibles</li>
        <li><i class="fas fa-info-circle text-info me-2"></i>Se muestra la descripción de la unidad al seleccionarla</li>
        <li><i class="fas fa-tools text-secondary me-2"></i>Útil para categorizar productos por su forma de medición</li>
    </ul>
    <hr>
</div>
```

---

## 💻 Paso 7: Actualizar JavaScript

### 7.1 Función para Cargar Datos del Select

**Archivo:** `view/[Modulo]/formulario[Entidad].js`

```javascript
// ✅ Función para cargar opciones del select dinámicamente
function cargarUnidadesMedida() {
    $.ajax({
        url: "../../controller/unidad_medida.php?op=listarDisponibles",  // ✅ Endpoint del controlador
        type: "GET",
        dataType: "json",
        success: function(data) {
            if (Array.isArray(data)) {
                var select = $('#id_unidad_familia');
                select.empty();
                select.append('<option value="">Seleccionar unidad de medida...</option>');
                
                data.forEach(function(unidad) {
                    // ✅ Formato: "Nombre (símbolo)" para mejor UX
                    var displayText = unidad.nombre_unidad;
                    if (unidad.simbolo_unidad) {
                        displayText += ' (' + unidad.simbolo_unidad + ')';
                    }
                    // ✅ Incluir descripción como data attribute para mostrar info adicional
                    select.append('<option value="' + unidad.id_unidad + '" data-descripcion="' + (unidad.descr_unidad || '') + '">' + displayText + '</option>');
                });
            } else {
                console.error('Error: Respuesta no válida del servidor para unidades de medida');
                toastr.warning('No se pudieron cargar las unidades de medida');
            }
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar unidades de medida:', error);
            toastr.error('Error al cargar las unidades de medida');
        }
    });
}
```

### 7.2 Event Handler para Mostrar Información Adicional

```javascript
// ✅ Manejar cambio en el select para mostrar descripción
$('#id_unidad_familia').on('change', function() {
    var selectedOption = $(this).find('option:selected');
    var descripcion = selectedOption.data('descripcion');
    
    if (descripcion && descripcion.trim() !== '') {
        $('#unidad-descr-text').text(descripcion);
        $('#unidad-descripcion').show();
    } else {
        $('#unidad-descripcion').hide();
    }
});
```

### 7.3 Actualizar Captura de Datos en el Guardado

```javascript
// ✅ En la función del botón guardar, añadir captura del nuevo campo
$(document).on('click', '#btnSalvarFamilia', function (event) {
    event.preventDefault();

    // Obtener valores del formulario
    var id_familiaR = $('#id_familia').val().trim();
    var codigo_familiaR = $('#codigo_familia').val().trim();
    var nombre_familiaR = $('#nombre_familia').val().trim();
    var name_familiaR = $('#name_familia').val().trim();
    var descr_familiaR = $('#descr_familia').val().trim();
    var id_unidad_familiaR = $('#id_unidad_familia').val() || null;  // ✅ Nuevo campo
    
    // ... resto de validaciones ...
    
    // ✅ Pasar nuevo parámetro a función de guardado
    verificarFamiliaExistente(id_familiaR, codigo_familiaR, name_familiaR, nombre_familiaR, descr_familiaR, activo_familiaR, id_unidad_familiaR);
});
```

### 7.4 Actualizar Función de Guardado

```javascript
// ✅ Incluir nuevo parámetro en la función de guardado
function guardarFamilia(id_familia, codigo_familia, name_familia, nombre_familia, descr_familia, activo_familia, id_unidad_familia) {
    $('#btnSalvarFamilia').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');
    
    var formData = new FormData();
    formData.append('codigo_familia', codigo_familia);
    formData.append('nombre_familia', nombre_familia);
    formData.append('name_familia', name_familia);
    formData.append('descr_familia', descr_familia);
    formData.append('activo_familia', activo_familia);
    formData.append('id_unidad_familia', id_unidad_familia || '');  // ✅ Nuevo campo
    
    // ... resto de la implementación ...
}
```

### 7.5 Actualizar Carga de Datos en Modo Edición

```javascript
// ✅ En la función cargarDatosFamilia, incluir el nuevo campo
function cargarDatosFamilia(idFamilia) {
    $.ajax({
        url: "../../controller/familia.php?op=mostrar",
        type: "POST",
        data: { id_familia: idFamilia },
        dataType: "json",
        success: function(data) {
            // Llenar los campos del formulario
            $('#id_familia').val(data.id_familia);
            $('#codigo_familia').val(data.codigo_familia);
            $('#nombre_familia').val(data.nombre_familia);
            $('#name_familia').val(data.name_familia);
            $('#descr_familia').val(data.descr_familia);
            
            // ✅ Configurar unidad de familia
            if (data.id_unidad_familia) {
                $('#id_unidad_familia').val(data.id_unidad_familia);
                // ✅ Trigger change para mostrar descripción
                $('#id_unidad_familia').trigger('change');
            }
            
            // ... resto de campos ...
        }
    });
}
```

### 7.6 Actualizar Seguimiento de Cambios

```javascript
// ✅ Incluir nuevo campo en captura de valores originales
function captureOriginalValues() {
    formOriginalValues = {
        codigo_familia: $('#codigo_familia').val(),
        nombre_familia: $('#nombre_familia').val(),
        name_familia: $('#name_familia').val(),
        descr_familia: $('#descr_familia').val(),
        imagen_actual: $('#imagen_actual').val(),
        id_unidad_familia: $('#id_unidad_familia').val()  // ✅ Nuevo campo
    };
}

// ✅ Incluir en verificación de cambios
function hasFormChanged() {
    const hasNewImage = $('#imagen_familia')[0].files && $('#imagen_familia')[0].files.length > 0;
    
    return (
        $('#codigo_familia').val() !== formOriginalValues.codigo_familia ||
        $('#nombre_familia').val() !== formOriginalValues.nombre_familia ||
        $('#name_familia').val() !== formOriginalValues.name_familia ||
        $('#descr_familia').val() !== formOriginalValues.descr_familia ||
        $('#id_unidad_familia').val() !== formOriginalValues.id_unidad_familia ||  // ✅ Nuevo campo
        hasNewImage
    );
}
```

### 7.7 Inicialización

```javascript
$(document).ready(function () {
    // ... otras inicializaciones ...
    
    // ✅ Cargar datos del select al cargar la página
    cargarUnidadesMedida();
    
    // ✅ Inicialización según modo
    const urlParams = new URLSearchParams(window.location.search);
    const idFamilia = urlParams.get('id');
    const modo = urlParams.get('modo') || 'nuevo';
    
    if (modo === 'editar' && idFamilia) {
        // ✅ Cargar datos después de cargar las unidades
        setTimeout(function() {
            cargarDatosFamilia(idFamilia);
        }, 500);  // Delay para asegurar que se carguen las opciones primero
    }
});
```

---

## 📊 Paso 8: Crear Datos de Ejemplo

### 8.1 Archivo SQL con Registros de Prueba

**Archivo:** `BD/[nombre_tabla]_ejemplo.sql`

```sql
-- ✅ Datos de ejemplo para testing
INSERT INTO unidad_medida (nombre_unidad, name_unidad, descr_unidad, simbolo_unidad, activo_unidad) VALUES
('Metro', 'Meter', 'Unidad básica de longitud del Sistema Internacional', 'm', 1),
('Metro cuadrado', 'Square meter', 'Unidad de superficie derivada del metro', 'm²', 1),
('Metro cúbico', 'Cubic meter', 'Unidad de volumen derivada del metro', 'm³', 1),
('Kilogramo', 'Kilogram', 'Unidad básica de masa del Sistema Internacional', 'kg', 1),
('Litro', 'Liter', 'Unidad de volumen equivalente a un decímetro cúbico', 'L', 1),
('Unidad', 'Unit', 'Unidad de medida genérica para conteo de elementos', 'ud', 1),
('Pieza', 'Piece', 'Unidad de medida para elementos individuales', 'pz', 1),
('Rollo', 'Roll', 'Unidad de medida para materiales enrollados', 'rollo', 1),
('Juego', 'Set', 'Conjunto de elementos que se venden juntos', 'jgo', 1),
('Par', 'Pair', 'Conjunto de dos elementos iguales', 'par', 1);
```

**🎯 Criterios para datos de ejemplo:**
- Incluir al menos 5-10 registros
- Variar entre con y sin símbolo
- Incluir descripciones descriptivas
- Todos con `activo = 1`

---

## 🔍 Paso 9: Testing y Validación

### 9.1 Casos de Prueba Esenciales

#### ✅ **Funcionalidad Básica**
1. **Carga del Select**
   - [ ] El select se llena automáticamente al cargar la página
   - [ ] Muestra formato "Nombre (símbolo)" correctamente
   - [ ] Incluye opción vacía por defecto

2. **Selección de Opciones**
   - [ ] Al seleccionar, muestra descripción si existe
   - [ ] Al cambiar selección, actualiza descripción
   - [ ] Al seleccionar opción vacía, oculta descripción

#### ✅ **Proceso CRUD**
3. **Crear Registro**
   - [ ] Guarda correctamente con unidad seleccionada
   - [ ] Guarda correctamente sin unidad (campo opcional)
   - [ ] Valida y muestra errores apropiadamente

4. **Editar Registro**
   - [ ] Carga valor correcto en modo edición
   - [ ] Muestra descripción correspondiente al cargar
   - [ ] Permite cambiar a otra unidad
   - [ ] Permite quitar unidad (campo opcional)

5. **Persistencia**
   - [ ] Los datos se guardan correctamente en BD
   - [ ] Los datos se mantienen tras recargar página
   - [ ] Las relaciones FK funcionan correctamente

#### ✅ **Manejo de Errores**
6. **Escenarios de Error**
   - [ ] Maneja error si no se puede cargar lista de unidades
   - [ ] Muestra mensaje apropiado si falla la conexión
   - [ ] Funciona correctamente si no hay datos en tabla relacionada

### 9.2 Validaciones de Código

```javascript
// ✅ Validar que el select se haya cargado
function validarSelectCargado() {
    const opciones = $('#id_unidad_familia option').length;
    if (opciones <= 1) {  // Solo opción por defecto
        console.warn('⚠️ Select no se cargó correctamente');
        toastr.warning('No se pudieron cargar las unidades de medida');
    }
}

// ✅ Validar en modo edición que se seleccione el valor correcto
function validarSeleccionEdicion(valorEsperado) {
    const valorActual = $('#id_unidad_familia').val();
    if (valorEsperado && valorActual !== valorEsperado.toString()) {
        console.error('❌ Error: No se seleccionó el valor correcto en edición');
    }
}
```

---

## 📋 Checklist de Implementación

### ✅ **Backend**
- [ ] Verificar estructura de BD y relación FK
- [ ] Crear modelo para tabla relacionada
- [ ] Crear controlador para tabla relacionada
- [ ] Actualizar modelo principal (insert/update)
- [ ] Actualizar controlador principal (guardaryeditar/listar)
- [ ] Crear datos de ejemplo

### ✅ **Frontend**
- [ ] Añadir campo select al formulario HTML
- [ ] Actualizar modal de ayuda (opcional)
- [ ] Crear función de carga de datos del select
- [ ] Crear event handler para mostrar información adicional
- [ ] Actualizar captura de datos en guardado
- [ ] Actualizar función de guardado principal
- [ ] Actualizar carga de datos en modo edición
- [ ] Actualizar seguimiento de cambios del formulario
- [ ] Configurar inicialización correcta

### ✅ **Testing**
- [ ] Probar carga inicial del select
- [ ] Probar creación con y sin valor
- [ ] Probar edición y cambio de valores
- [ ] Probar persistencia de datos
- [ ] Probar manejo de errores
- [ ] Validar rendimiento con muchos registros

### ✅ **Documentación**
- [ ] Documentar cambios realizados
- [ ] Actualizar esquema de BD si necesario
- [ ] Crear/actualizar manual de usuario
- [ ] Documentar endpoints API

---

## 🚀 Patrones y Mejores Prácticas

### 🎯 **Nomenclatura Consistente**

| Elemento | Patrón | Ejemplo |
|----------|--------|---------|
| Modelo | `[NombreTabla].php` | `UnidadMedida.php` |
| Controlador | `[nombre_tabla].php` | `unidad_medida.php` |
| Método Get Lista | `get_[tabla]_disponibles()` | `get_unidades_disponibles()` |
| Método Get Por ID | `get_[tabla]xid()` | `get_unidadxid()` |
| Campo HTML | `id_[tabla]_[entidad]` | `id_unidad_familia` |
| Función JS | `cargar[TablasPlural]()` | `cargarUnidadesMedida()` |

### 🔧 **Optimizaciones Recomendadas**

```javascript
// ✅ Cache de datos para evitar llamadas repetidas
let unidadesMedidaCache = null;

function cargarUnidadesMedida() {
    if (unidadesMedidaCache) {
        poblarSelect(unidadesMedidaCache);
        return;
    }
    
    $.ajax({
        // ... llamada AJAX ...
        success: function(data) {
            unidadesMedidaCache = data;  // ✅ Guardar en cache
            poblarSelect(data);
        }
    });
}

// ✅ Separar lógica de poblado del select
function poblarSelect(data) {
    var select = $('#id_unidad_familia');
    select.empty();
    select.append('<option value="">Seleccionar unidad de medida...</option>');
    
    data.forEach(function(unidad) {
        var displayText = unidad.nombre_unidad;
        if (unidad.simbolo_unidad) {
            displayText += ' (' + unidad.simbolo_unidad + ')';
        }
        select.append('<option value="' + unidad.id_unidad + '" data-descripcion="' + (unidad.descr_unidad || '') + '">' + displayText + '</option>');
    });
}
```

### 🛡️ **Validaciones de Seguridad**

```php
// ✅ En el controlador, validar que el FK existe
case "guardaryeditar":
    $id_unidad_familia = $_POST["id_unidad_familia"] ?? null;
    
    // ✅ Si se proporciona, validar que existe
    if (!empty($id_unidad_familia)) {
        $unidadMedida = new UnidadMedida();
        $unidadExiste = $unidadMedida->get_unidadxid($id_unidad_familia);
        
        if (!$unidadExiste) {
            echo json_encode([
                "success" => false,
                "message" => "La unidad de medida seleccionada no es válida"
            ]);
            exit;
        }
    }
    
    // ... continuar con el guardado ...
```

---

## 📚 Recursos Adicionales

### 🔗 **Enlaces Útiles**
- [Documentación PDO PHP](https://www.php.net/manual/es/book.pdo.php)
- [Guía de Ajax con jQuery](https://api.jquery.com/jquery.ajax/)
- [Bootstrap Select Components](https://getbootstrap.com/docs/5.3/forms/select/)

### 📁 **Archivos de Referencia**
- `view/MntFamilia_unidad/formularioFamilia.php` - Ejemplo completo de formulario
- `view/MntFamilia_unidad/formularioFamilia.js` - JavaScript completo
- `models/Familia.php` - Modelo principal actualizado
- `controller/familia_unidad.php` - Controlador principal actualizado
- `models/UnidadMedida.php` - Modelo de tabla relacionada
- `controller/unidad_medida.php` - Controlador de tabla relacionada

---

*Guía creada el 7 de noviembre de 2025*  
*Basada en la implementación real del campo `id_unidad_familia` en MntFamilia_unidad*