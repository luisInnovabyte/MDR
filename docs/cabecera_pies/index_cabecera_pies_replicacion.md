# Guía de Replicación
## Sistema Cabecera-Pies - Implementar en Otro Módulo

> **Propósito:** Instrucciones paso a paso para replicar este sistema  
> **Nivel de Dificultad:** Intermedio-Avanzado

[← Volver al índice](./index_cabecera_pies.md)

---

## 📋 Tabla de Contenidos

1. [Prerequisitos](#prerequisitos)
2. [Checklist de Replicación](#checklist-de-replicación)
3. [Paso 1: Base de Datos](#paso-1-base-de-datos)
4. [Paso 2: Modelo (Model)](#paso-2-modelo-model)
5. [Paso 3: Controller](#paso-3-controller)
6. [Paso 4: Vista Principal](#paso-4-vista-principal)
7. [Paso 5: JavaScript DataTables](#paso-5-javascript-datatables)
8. [Paso 6: Formulario](#paso-6-formulario)
9. [Paso 7: Ayuda](#paso-7-ayuda)
10. [Paso 8: Pruebas](#paso-8-pruebas)
11. [Errores Comunes](#errores-comunes)
12. [Optimizaciones](#optimizaciones)

---

## 1. Prerequisitos

### Conocimientos Necesarios

- ✅ **PHP 8.x**: Orientación a objetos, PDO, prepared statements
- ✅ **MySQL/MariaDB**: Creación de tablas, vistas, triggers
- ✅ **jQuery 3.7.1**: Selectors, AJAX, eventos
- ✅ **DataTables 2.x**: Configuración, columnas, render functions
- ✅ **Bootstrap 5**: Grid system, cards, modals, forms
- ✅ **Patrón MVC**: Separación de capas

### Herramientas

- Editor de código (VS Code recomendado)
- Servidor local (XAMPP, WAMP, Laragon)
- Navegador con DevTools
- Cliente MySQL (phpMyAdmin, HeidiSQL, DBeaver)

### Archivos del Sistema Original

```
view/MntArticulos/
  ├── index.php                    ← Vista principal con tabla
  ├── mntarticulo.js               ← JavaScript DataTables + CRUD
  ├── formularioArticulo.php       ← Formulario independiente
  └── ayudaArticulos.php           ← Modal de ayuda

controller/
  └── articulo.php                 ← Controller con switch

models/
  └── Articulo.php                 ← Modelo con métodos estándar

BD/
  └── vista_articulo_completa.sql  ← Vista SQL (opcional)
```

---

## 2. Checklist de Replicación

### ☑️ Fase 1: Planificación

- [ ] Identificar entidad a replicar (ej: Proveedores, Clientes)
- [ ] Definir campos obligatorios y opcionales
- [ ] Identificar relaciones con otras tablas
- [ ] Definir campos de agrupación (equivalente a familia)
- [ ] Decidir si necesita child rows expandibles
- [ ] Listar estadísticas necesarias

### ☑️ Fase 2: Base de Datos

- [ ] Crear tabla principal con campos estándar
- [ ] Crear vista SQL con JOINs (opcional)
- [ ] Crear índices en campos de búsqueda
- [ ] Crear triggers si es necesario
- [ ] Insertar datos de prueba

### ☑️ Fase 3: Backend

- [ ] Crear modelo con métodos estándar
- [ ] Crear controller con operaciones CRUD
- [ ] Implementar validaciones
- [ ] Agregar logging
- [ ] Probar endpoints con Postman

### ☑️ Fase 4: Frontend

- [ ] Crear vista principal (index.php)
- [ ] Implementar panel de estadísticas
- [ ] Configurar DataTables
- [ ] Crear formulario independiente
- [ ] Crear modal de ayuda

### ☑️ Fase 5: Testing

- [ ] Probar creación de registros
- [ ] Probar edición
- [ ] Probar eliminación (soft delete)
- [ ] Probar reactivación
- [ ] Probar filtros
- [ ] Probar responsividad

---

## 3. Paso 1: Base de Datos

### 3.1 Crear Tabla Principal

**Template SQL:**

```sql
CREATE TABLE [nombre_entidad] (
    -- Identificación
    id_[entidad] INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_[entidad] VARCHAR(50) NOT NULL UNIQUE,
    
    -- Campos específicos (EJEMPLO)
    nombre_[entidad] VARCHAR(255) NOT NULL,
    descripcion_[entidad] TEXT,
    
    -- Foreign Keys (si aplica)
    id_categoria INT UNSIGNED,
    
    -- Campos obligatorios estándar
    activo_[entidad] BOOLEAN DEFAULT TRUE,
    created_at_[entidad] TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_[entidad] TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_codigo_[entidad] (codigo_[entidad]),
    INDEX idx_nombre_[entidad] (nombre_[entidad]),
    INDEX idx_activo_[entidad] (activo_[entidad]),
    
    -- Foreign Keys
    CONSTRAINT fk_[entidad]_categoria 
        FOREIGN KEY (id_categoria) 
        REFERENCES categoria(id_categoria)
        ON DELETE RESTRICT ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

**Ejemplo real (Proveedores):**

```sql
CREATE TABLE proveedor (
    id_proveedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_proveedor VARCHAR(50) NOT NULL UNIQUE,
    nombre_proveedor VARCHAR(255) NOT NULL,
    nif_proveedor VARCHAR(20),
    email_proveedor VARCHAR(100),
    telefono_proveedor VARCHAR(20),
    direccion_proveedor VARCHAR(255),
    notas_proveedor TEXT,
    activo_proveedor BOOLEAN DEFAULT TRUE,
    created_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_codigo_proveedor (codigo_proveedor),
    INDEX idx_nombre_proveedor (nombre_proveedor),
    INDEX idx_activo_proveedor (activo_proveedor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

### 3.2 Crear Vista SQL (Opcional)

**Template:**

```sql
CREATE OR REPLACE VIEW vista_[entidad]_completa AS
SELECT 
    e.*,
    c.nombre_categoria,
    c.codigo_categoria,
    -- Campos calculados
    CONCAT(c.nombre_categoria, ' > ', e.nombre_[entidad]) as jerarquia_completa
FROM [entidad] e
LEFT JOIN categoria c ON e.id_categoria = c.id_categoria
WHERE e.activo_[entidad] = 1
ORDER BY c.nombre_categoria, e.nombre_[entidad];
```

**Ventajas de usar vista:**
- ✅ Simplifica consultas complejas
- ✅ Centraliza lógica de JOINs
- ✅ Facilita mantenimiento
- ✅ Mejora legibilidad del código

---

## 4. Paso 2: Modelo (Model)

### Template: Entidad.php

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
                'system',
                'Entidad',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // 1. Listar todos
    public function get_entidades()
    {
        try {
            $sql = "SELECT * FROM [tabla] 
                    WHERE activo_[entidad] = 1 
                    ORDER BY nombre_[entidad] ASC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'get_entidades',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    // 2. Obtener por ID
    public function get_entidadxid($id_entidad)
    {
        try {
            $sql = "SELECT * FROM [tabla] 
                    WHERE id_[entidad] = ? AND activo_[entidad] = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'get_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // 3. Insertar
    public function insert_entidad($campo1, $campo2, $campo3 = null)
    {
        try {
            $sql = "INSERT INTO [tabla] (
                        campo1_[entidad], 
                        campo2_[entidad], 
                        campo3_[entidad],
                        created_at_[entidad]
                    ) VALUES (?, ?, ?, NOW())";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $campo1, PDO::PARAM_STR);
            $stmt->bindValue(2, $campo2, PDO::PARAM_STR);
            
            // Campos opcionales
            if (!empty($campo3)) {
                $stmt->bindValue(3, $campo3, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->execute();
            
            $id = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'insert_entidad',
                "Entidad creada con ID: $id",
                'info'
            );
            
            return $id;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'insert_entidad',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // 4. Actualizar
    public function update_entidad($id_entidad, $campo1, $campo2, $campo3 = null)
    {
        try {
            $sql = "UPDATE [tabla] SET 
                        campo1_[entidad] = ?,
                        campo2_[entidad] = ?,
                        campo3_[entidad] = ?,
                        updated_at_[entidad] = NOW()
                    WHERE id_[entidad] = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $campo1, PDO::PARAM_STR);
            $stmt->bindValue(2, $campo2, PDO::PARAM_STR);
            
            if (!empty($campo3)) {
                $stmt->bindValue(3, $campo3, PDO::PARAM_STR);
            } else {
                $stmt->bindValue(3, null, PDO::PARAM_NULL);
            }
            
            $stmt->bindValue(4, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'update_entidad',
                "Entidad actualizada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount();
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'update_entidad',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // 5. Eliminar (SOFT DELETE)
    public function delete_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE [tabla] SET 
                        activo_[entidad] = 0,
                        updated_at_[entidad] = NOW()
                    WHERE id_[entidad] = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'delete_entidadxid',
                "Entidad desactivada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'delete_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // 6. Activar
    public function activar_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE [tabla] SET 
                        activo_[entidad] = 1,
                        updated_at_[entidad] = NOW()
                    WHERE id_[entidad] = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'activar_entidadxid',
                "Entidad activada ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'Entidad',
                'activar_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // 7. Estadísticas
    public function total_entidad()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM [tabla]";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }

    public function total_entidad_activo()
    {
        try {
            $sql = "SELECT COUNT(*) as total FROM [tabla] WHERE activo_[entidad] = 1";
            $stmt = $this->conexion->query($sql);
            return $stmt->fetch(PDO::FETCH_ASSOC)['total'];
        } catch (PDOException $e) {
            return 0;
        }
    }
}
?>
```

---

## 5. Paso 3: Controller

### Template: entidad.php

```php
<?php
require_once "../config/conexion.php";
require_once "../models/Entidad.php";
require_once '../config/funciones.php';

$registro = new RegistroActividad();
$entidad = new Entidad();

switch ($_GET["op"]) {

    case "estadisticas":
        try {
            $total = $entidad->total_entidad() ?: 0;
            $activos = $entidad->total_entidad_activo() ?: 0;
            
            echo json_encode([
                "success" => true,
                "data" => [
                    "total" => $total,
                    "activos" => $activos
                ]
            ], JSON_UNESCAPED_UNICODE);
        } catch (Exception $e) {
            echo json_encode([
                "success" => false,
                "message" => "Error al obtener estadísticas"
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "listar":
        try {
            $datos = $entidad->get_entidades();
            $data = array();
            
            foreach ($datos as $row) {
                $data[] = array(
                    "id_entidad" => $row["id_[entidad]"],
                    "codigo" => $row["codigo_[entidad]"],
                    "nombre" => $row["nombre_[entidad]"],
                    "activo" => $row["activo_[entidad]"]
                    // ... más campos
                );
            }
            
            $results = array(
                "draw" => 1,
                "recordsTotal" => count($data),
                "recordsFiltered" => count($data),
                "data" => $data
            );
            
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($results, JSON_UNESCAPED_UNICODE);
            
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener datos',
                'data' => []
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "guardaryeditar":
        try {
            $id_entidad = !empty($_POST["id_entidad"]) ? $_POST["id_entidad"] : null;
            
            // Sanitizar datos
            $campo1 = htmlspecialchars(trim($_POST["campo1"]), ENT_QUOTES, 'UTF-8');
            $campo2 = htmlspecialchars(trim($_POST["campo2"]), ENT_QUOTES, 'UTF-8');
            
            // Campos opcionales
            $campo3 = !empty($_POST["campo3"]) ? $_POST["campo3"] : null;
            
            if (empty($id_entidad)) {
                // INSERT
                $resultado = $entidad->insert_entidad($campo1, $campo2, $campo3);
                
                if ($resultado) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro creado correctamente',
                        'id_entidad' => $resultado
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al crear el registro'
                    ], JSON_UNESCAPED_UNICODE);
                }
            } else {
                // UPDATE
                $resultado = $entidad->update_entidad($id_entidad, $campo1, $campo2, $campo3);
                
                if ($resultado !== false) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Registro actualizado correctamente'
                    ], JSON_UNESCAPED_UNICODE);
                } else {
                    echo json_encode([
                        'success' => false,
                        'message' => 'Error al actualizar el registro'
                    ], JSON_UNESCAPED_UNICODE);
                }
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "mostrar":
        try {
            $id_entidad = $_POST["id_entidad"];
            $datos = $entidad->get_entidadxid($id_entidad);
            
            if ($datos) {
                header('Content-Type: application/json; charset=utf-8');
                echo json_encode($datos, JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Registro no encontrado'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error al obtener el registro'
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "eliminar":
        try {
            $id_entidad = $_POST["id_entidad"];
            $resultado = $entidad->delete_entidadxid($id_entidad);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registro desactivado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al desactivar el registro'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    case "activar":
        try {
            $id_entidad = $_POST["id_entidad"];
            $resultado = $entidad->activar_entidadxid($id_entidad);
            
            if ($resultado) {
                echo json_encode([
                    'success' => true,
                    'message' => 'Registro activado correctamente'
                ], JSON_UNESCAPED_UNICODE);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Error al activar el registro'
                ], JSON_UNESCAPED_UNICODE);
            }
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], JSON_UNESCAPED_UNICODE);
        }
        break;

    default:
        echo json_encode([
            'success' => false,
            'message' => 'Operación no válida'
        ], JSON_UNESCAPED_UNICODE);
        break;
}
?>
```

---

## 6. Paso 4: Vista Principal

Ver [documentación de estructura](./index_cabecera_pies_estructura.md) para detalles completos.

**Elementos clave:**
1. Panel de estadísticas (4 tarjetas)
2. Alerta de filtros activos
3. Tabla DataTables con thead/tbody/tfoot
4. Inclusión de templates

---

## 7. Paso 5: JavaScript DataTables

Ver [documentación de DataTables](./index_cabecera_pies_datatables.md) y [funciones JS](./index_cabecera_pies_js_funciones.md).

**Configuración mínima:**

```javascript
const tabla = $('#tblEntidades').DataTable({
    ajax: {
        url: '../../controller/entidad.php?op=listar',
        type: 'GET',
        dataSrc: 'data'
    },
    columns: [
        { data: 'id_entidad' },
        { data: 'codigo' },
        { data: 'nombre' },
        { data: 'activo' }
    ],
    language: {
        url: '../../public/lib/DataTables/es-ES.json'
    }
});
```

---

## 8. Paso 6: Formulario

Ver [documentación de formulario](./index_cabecera_pies_formulario.md) para template completo.

**Mínimo viable:**
- Parámetros GET (modo, id)
- Campos con validación
- Submit con FormData
- Redirección después de guardar

---

## 9. Paso 7: Ayuda

**Template de accordion:**

```html
<div class="accordion" id="accordionAyuda">
    <div class="accordion-item">
        <h2 class="accordion-header">
            <button class="accordion-button" data-bs-toggle="collapse" 
                    data-bs-target="#collapse1">
                Campo 1
            </button>
        </h2>
        <div id="collapse1" class="accordion-collapse collapse show">
            <div class="accordion-body">
                Descripción detallada del campo 1
            </div>
        </div>
    </div>
</div>
```

---

## 10. Paso 8: Pruebas

### Checklist de Testing

- [ ] **Crear registro**: Formulario nuevo funciona
- [ ] **Editar registro**: Carga datos correctamente
- [ ] **Eliminar registro**: Soft delete funciona
- [ ] **Reactivar registro**: Cambia activo=1
- [ ] **Estadísticas**: Contadores actualizan
- [ ] **Filtros**: DataTables filtra correctamente
- [ ] **Responsividad**: Funciona en móvil
- [ ] **Validaciones**: Campos obligatorios funcionan
- [ ] **Errores**: Mensajes claros al usuario

---

## 11. Errores Comunes

### Error 1: DataTables no carga datos

**Síntoma:** Tabla vacía o error en consola

**Solución:**
```javascript
// Verificar que dataSrc coincida con estructura JSON
ajax: {
    dataSrc: 'data'  // ← Debe coincidir con response.data
}

// Verificar en controller:
$results = array(
    "data" => $data  // ← Debe coincidir
);
```

### Error 2: Soft delete no funciona

**Síntoma:** No encuentra el registro

**Solución:**
```php
// Incluir WHERE activo = 1 en todas las consultas
$sql = "SELECT * FROM tabla WHERE activo_entidad = 1";
```

### Error 3: FormData no envía archivos

**Síntoma:** Imagen no se sube

**Solución:**
```javascript
$.ajax({
    processData: false,  // ← OBLIGATORIO
    contentType: false,  // ← OBLIGATORIO
    data: formData
});
```

### Error 4: JSON con caracteres raros

**Síntoma:** Acentos se ven como �

**Solución:**
```php
header('Content-Type: application/json; charset=utf-8');
echo json_encode($data, JSON_UNESCAPED_UNICODE);
```

---

## 12. Optimizaciones

### Performance

1. **Usar vistas SQL** para consultas complejas
2. **Índices** en campos de búsqueda frecuente
3. **Pagination server-side** para > 1000 registros
4. **Cache de estadísticas** si no cambian frecuentemente

### UX

1. **Loading spinners** durante operaciones
2. **Toasts** para feedback rápido
3. **Confirmaciones** antes de eliminar
4. **Breadcrumbs** para navegación
5. **Ayuda contextual** visible

### Mantenimiento

1. **Comentarios en código** explicativos
2. **Logging** de operaciones críticas
3. **Naming conventions** consistentes
4. **Documentación** actualizada

---

## ✅ Checklist Final

- [ ] Base de datos creada con campos estándar
- [ ] Modelo implementado con métodos estándar
- [ ] Controller con switch y operaciones CRUD
- [ ] Vista principal con estadísticas y tabla
- [ ] JavaScript DataTables configurado
- [ ] Formulario independiente funcional
- [ ] Modal de ayuda completo
- [ ] Validaciones client-side y server-side
- [ ] Pruebas de todas las funcionalidades
- [ ] Documentación actualizada

---

## 📚 Recursos Adicionales

- [Estructura del Sistema](./index_cabecera_pies_estructura.md)
- [Configuración DataTables](./index_cabecera_pies_datatables.md)
- [Funciones JavaScript](./index_cabecera_pies_js_funciones.md)
- [Controller y Backend](./index_cabecera_pies_controller.md)
- [Formulario y Ayuda](./index_cabecera_pies_formulario.md)

---

## 💡 Soporte

Si encuentras problemas durante la replicación:

1. Revisa los logs en `public/logs/`
2. Verifica la consola del navegador (F12)
3. Compara con el módulo Artículos original
4. Consulta las instrucciones de desarrollo en `.github/copilot-instructions.md`

---

[← Anterior: Formulario](./index_cabecera_pies_formulario.md) | [Volver al índice](./index_cabecera_pies.md)
