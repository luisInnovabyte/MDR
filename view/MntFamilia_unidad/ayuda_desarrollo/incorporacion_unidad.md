# Incorporación de Unidad de Medida al Módulo Familias

**Fecha:** 5 de noviembre de 2025  
**Módulo:** MntFamilia_unidad  
**Objetivo:** Integrar el campo `id_unidad_familia` en el formulario de gestión de familias

---

## 📋 Resumen de Cambios

Se ha implementado la funcionalidad completa para asociar unidades de medida a las familias de productos, incluyendo:

- ✅ Campo select dinámico con unidades disponibles
- ✅ Descripción contextual de la unidad seleccionada
- ✅ Integración completa en el proceso CRUD
- ✅ Validación y persistencia de datos
- ✅ Datos de beispiel para testing

---

## 🗃️ Cambios en Base de Datos

### Tabla Existente
La tabla `familia` ya contenía el campo `id_unidad_familia` según el esquema en `almacen.sql`:

```sql
CREATE TABLE familia (
    id_familia INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_familia VARCHAR(20) NOT NULL UNIQUE,
    nombre_familia VARCHAR(100) NOT NULL,
    name_familia VARCHAR(100) NOT NULL COMMENT 'Nombre en inglés',
    descr_familia VARCHAR(255),
    activo_familia BOOLEAN DEFAULT TRUE,
    id_unidad_familia INT UNSIGNED,  -- ✅ Campo ya existente
    created_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_familia TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Datos de Ejemplo Creados
**Archivo:** `BD/unidades_medida_ejemplo.sql`

```sql
INSERT INTO unidad_medida (nombre_unidad, name_unidad, descr_unidad, simbolo_unidad, activo_unidad) VALUES
('Metro', 'Meter', 'Unidad básica de longitud del Sistema Internacional', 'm', 1),
('Metro cuadrado', 'Square meter', 'Unidad de superficie derivada del metro', 'm²', 1),
('Metro cúbico', 'Cubic meter', 'Unidad de volumen derivada del metro', 'm³', 1),
('Kilogramo', 'Kilogram', 'Unidad básica de masa del Sistema Internacional', 'kg', 1),
('Litro', 'Liter', 'Unidad de volumen equivalente a un decímetro cúbico', 'L', 1),
-- ... más registros
```

---

## 🎛️ Controlador Nuevo

### Archivo: `controller/unidad_medida.php`

```php
<?php
require_once "../config/conexion.php";
require_once "../models/UnidadMedida.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$unidadMedida = new UnidadMedida();

switch ($_GET["op"]) {
    case "listarDisponibles":
        $datos = $unidadMedida->get_unidades_disponibles();
        header('Content-Type: application/json');
        echo json_encode($datos, JSON_UNESCAPED_UNICODE);
        break;
        
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

**Operaciones disponibles:**
- `listarDisponibles`: Lista unidades activas para poblar el select
- `mostrar`: Obtiene detalles de una unidad específica

---

## 🏛️ Modelo Nuevo

### Archivo: `models/UnidadMedida.php`

```php
<?php
class UnidadMedida
{
    private $conexion;
    private $registro;

    public function __construct()
    {
        $this->conexion = (new Conexion())->getConexion();
        $this->registro = new RegistroActividad();
    }

    public function get_unidades_disponibles()
    {
        $sql = "SELECT id_unidad, nombre_unidad, name_unidad, descr_unidad, simbolo_unidad 
                FROM unidad_medida 
                WHERE activo_unidad = 1 
                ORDER BY nombre_unidad ASC";
        // ... implementación
    }

    public function get_unidadxid($id_unidad)
    {
        $sql = "SELECT * FROM unidad_medida WHERE id_unidad = ?";
        // ... implementación
    }
}
?>
```

**Métodos implementados:**
- `get_unidades_disponibles()`: Obtiene lista de unidades activas
- `get_unidadxid()`: Obtiene una unidad específica por ID

> **📋 NOTA IMPORTANTE - VISTA vs JOINS:**  
> En este proyecto, para **todas las consultas SELECT** en los modelos que requieren datos de familias con sus unidades de medida, **NO se utiliza directamente la tabla `familia`**. En su lugar, se usa la **vista de base de datos `familia_unidad_medida`** que ya contiene la información combinada.
> 
> **Alternativa equivalente:** Se podría lograr el mismo resultado usando un `LEFT JOIN` entre las tablas:
> ```sql
> SELECT f.*, um.nombre_unidad, um.simbolo_unidad, um.descr_unidad 
> FROM familia f 
> LEFT JOIN unidad_medida um ON f.id_unidad_familia = um.id_unidad_medida
> ```
> 
> **Ventaja de usar vista:** Mayor simplicidad en las consultas y mejor rendimiento al evitar repetir JOINs complejos.

---

## 🔧 Modificaciones al Modelo Familia

### Archivo: `models/Familia.php`

#### Método `insert_familia()` Actualizado
```php
// ANTES
public function insert_familia($nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '')

// DESPUÉS
public function insert_familia($nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '', $id_unidad_familia = null)
```

**SQL actualizado:**
```sql
INSERT INTO familia (codigo_familia, nombre_familia, name_familia, descr_familia, activo_familia, imagen_familia, id_unidad_familia, created_at_familia, updated_at_familia) 
VALUES (?, ?, ?, ?, 1, ?, ?, NOW(), NOW())
```

#### Método `update_familia()` Actualizado
```php
// ANTES
public function update_familia($id_familia, $nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '')

// DESPUÉS  
public function update_familia($id_familia, $nombre_familia, $codigo_familia, $name_familia, $descr_familia, $imagen_familia = '', $id_unidad_familia = null)
```

**SQL actualizado:**
```sql
UPDATE familia SET nombre_familia = ?, codigo_familia = ?, name_familia = ?, descr_familia = ?, imagen_familia = ?, id_unidad_familia = ?, updated_at_familia = NOW() WHERE id_familia = ?
```

---

## 🎨 Modificaciones en la Vista

### Archivo: `view/MntFamilia_unidad/formularioFamilia.php`

#### Campo Select Añadido
```html
<div class="col-12 col-md-6">
    <label for="id_unidad_familia" class="form-label">Unidad de medida:</label>
    <select class="form-control" name="id_unidad_familia" id="id_unidad_familia">
        <option value="">Seleccionar unidad de medida...</option>
        <!-- Las opciones se cargarán dinámicamente -->
    </select>
    <div class="invalid-feedback small-invalid-feedback">Seleccione una unidad de medida válida</div>
    <small class="form-text text-muted">
        Unidad de medida asociada a esta familia
        <div id="unidad-descripcion" class="mt-1 p-2 bg-light border rounded" style="display: none;">
            <strong>Descripción:</strong> <span id="unidad-descr-text"></span>
        </div>
    </small>
</div>
```

#### Modal de Ayuda Actualizado
```html
<div class="col-12">
    <h6 class="text-primary"><i class="fas fa-balance-scale me-2"></i>Unidad de Medida</h6>
    <p><strong>Campo opcional.</strong> Unidad de medida asociada a esta familia de productos.</p>
    <ul class="list-unstyled ms-3">
        <li><i class="fas fa-list text-info me-2"></i>Seleccione de la lista de unidades disponibles</li>
        <li><i class="fas fa-info-circle text-info me-2"></i>Se muestra la descripción de la unidad al seleccionarla</li>
        <li><i class="fas fa-tools text-secondary me-2"></i>Útil para categorizar productos por su forma de medición</li>
    </ul>
</div>
```

---

## 💻 Modificaciones en JavaScript

### Archivo: `view/MntFamilia_unidad/formularioFamilia.js`

#### Función para Cargar Unidades
```javascript
function cargarUnidadesMedida() {
    $.ajax({
        url: "../../controller/unidad_medida.php?op=listarDisponibles",
        type: "GET",
        dataType: "json",
        success: function(data) {
            if (Array.isArray(data)) {
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
        },
        error: function(xhr, status, error) {
            console.error('Error al cargar unidades de medida:', error);
            toastr.error('Error al cargar las unidades de medida');
        }
    });
}
```

#### Event Handler para Mostrar Descripción
```javascript
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

#### Actualizaciones en el Guardado
```javascript
// Captura del valor
var id_unidad_familiaR = $('#id_unidad_familia').val() || null;

// Envío en FormData
formData.append('id_unidad_familia', id_unidad_familia || '');

// Actualización en funciones de validación y seguimiento de cambios
$('#id_unidad_familia').val() !== formOriginalValues.id_unidad_familia
```

#### Carga en Modo Edición
```javascript
// Configurar unidad de familia
if (data.id_unidad_familia) {
    $('#id_unidad_familia').val(data.id_unidad_familia);
    // Trigger change para mostrar descripción
    $('#id_unidad_familia').trigger('change');
}
```

---

## 🚀 Flujo de Funcionamiento

### 1. **Carga Inicial del Formulario**
```
formularioFamilia.php → formularioFamilia.js → cargarUnidadesMedida() → controller/unidad_medida.php?op=listarDisponibles → models/UnidadMedida.php
```

### 2. **Selección de Unidad**
```
Usuario selecciona unidad → Event 'change' → Mostrar descripción en div informativo
```

### 3. **Guardado de Familia**
```
Botón Guardar → Validación → verificarFamiliaExistente() → guardarFamilia() → controller/familia_unidad.php?op=guardaryeditar → models/Familia.php
```

### 4. **Carga en Modo Edición**
```
URL con id → cargarDatosFamilia() → controller/familia_unidad.php?op=mostrar → Poblar campo select → Trigger change para descripción
```

---

## 🎯 Características Implementadas

### ✅ **Funcionalidades del Campo**
- **Select Dinámico**: Carga automática de unidades activas
- **Descripción Contextual**: Información adicional al seleccionar
- **Formato Mejorado**: Muestra "Nombre (símbolo)" 
- **Campo Opcional**: No es obligatorio, permite valor vacío
- **Persistencia Completa**: Guarda y carga en modo edición
- **Validación Integrada**: Parte del sistema de seguimiento de cambios

### ✅ **Interfaz de Usuario**
- **Diseño Responsivo**: Adaptable a diferentes tamaños de pantalla
- **Feedback Visual**: Descripción desplegable informativa
- **Integración Seamless**: No interfiere con campos existentes
- **Ayuda Contextual**: Documentación en modal de ayuda

### ✅ **Funcionalidad Backend**
- **API RESTful**: Endpoints claros para unidades de medida
- **Logging Integrado**: Registro de actividades y errores
- **Manejo de Errores**: Respuestas JSON estructuradas
- **Optimización de Queries**: Consultas eficientes ordenadas

---

## 📊 Datos de Ejemplo Incluidos

El archivo `unidades_medida_ejemplo.sql` incluye 10 unidades de medida comunes:

| Nombre | Símbolo | Descripción |
|--------|---------|-------------|
| Metro | m | Unidad básica de longitud |
| Metro cuadrado | m² | Unidad de superficie |
| Metro cúbico | m³ | Unidad de volumen |
| Kilogramo | kg | Unidad básica de masa |
| Litro | L | Unidad de volumen |
| Unidad | ud | Unidad genérica de conteo |
| Pieza | pz | Elementos individuales |
| Rollo | rollo | Materiales enrollados |
| Juego | jgo | Conjunto de elementos |
| Par | par | Conjunto de dos elementos |

---

## 🔍 Testing y Validación

### Casos de Prueba Sugeridos
1. **Crear familia nueva** con unidad de medida
2. **Crear familia nueva** sin unidad de medida (campo opcional)
3. **Editar familia existente** y cambiar unidad
4. **Editar familia existente** y quitar unidad
5. **Verificar persistencia** de datos tras recargar
6. **Validar descripción** se muestra correctamente
7. **Probar con unidades** con y sin símbolo
8. **Verificar logs** de actividad se generan

### Validaciones Implementadas
- ✅ Campo opcional (no interfiere si está vacío)
- ✅ Validación de datos antes del guardado
- ✅ Manejo de errores en carga de unidades
- ✅ Seguimiento de cambios para formulario modificado
- ✅ Restauración correcta en modo edición

---

## 📋 Archivos Modificados

### Archivos Creados
- `controller/unidad_medida.php`
- `models/UnidadMedida.php`
- `BD/unidades_medida_ejemplo.sql`
- `view/MntFamilia_unidad/incorporacion_unidad.md` (este archivo)

### Archivos Modificados
- `models/Familia.php`
- `view/MntFamilia_unidad/formularioFamilia.php`
- `view/MntFamilia_unidad/formularioFamilia.js`

---

*Documentación generada el 5 de noviembre de 2025*
*Módulo: MntFamilia_unidad*
*Desarrollador: GitHub Copilot*