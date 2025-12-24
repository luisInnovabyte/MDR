# Prompt para Generar Controller
## Plantilla de solicitud para implementar un controller PHP con patrón MVC

> **Propósito:** Prompt reutilizable para que un asistente de IA genere el controller PHP  
> **Contexto:** El Modelo ya está implementado. Se generará el controller con operaciones CRUD estándar.  
> **Documentación:** Basado en [controller.md](./controller.md)

---

## 📋 Checklist Pre-Implementación

Antes de usar el prompt, recopila y ten listos estos elementos:

### ✅ Archivos Backend (YA IMPLEMENTADOS)

- [ ] **Tabla de base de datos creada** 
  - SQL CREATE TABLE completo para copiar
  - Incluir todos los FOREIGN KEYs
  - Incluir todos los INDEXes
  - Incluir comentarios de tabla si existen

- [ ] **Modelo implementado**
  - Ruta: `models/[Entidad].php`
  - Clase: `[Entidad]` (primera letra mayúscula)
  - Métodos disponibles verificados:
    * `get_[entidades]()`
    * `get_[entidades]_disponibles()`
    * `get_[entidad]xid($id)`
    * `insert_[entidad](...)`
    * `update_[entidad]($id, ...)`
    * `delete_[entidad]xid($id)`
    * `activar_[entidad]xid($id)`
    * `verificar[Entidad]($campo, $id)`

### ✅ Información de la Entidad

- [ ] **Nombre de la entidad** (singular): `_____________`
  - Ejemplo: `proveedor`, `cliente`, `articulo`

- [ ] **Nombre del modelo** (PascalCase): `_____________`
  - Ejemplo: `Proveedor`, `Cliente`, `Articulo`

- [ ] **Prefijo de tabla**: `_____________`
  - Ejemplo: `proveedor`, `cliente`, `articulo`

- [ ] **Primary Key**: `_____________`
  - Ejemplo: `id_proveedor`, `id_cliente`, `id_articulo`

### ✅ Operaciones del Controller

Marca las operaciones que necesitas:

**Operaciones Estándar (todas recomendadas):**
- [ ] `listar` - Listado completo para DataTables
- [ ] `guardaryeditar` - INSERT o UPDATE según ID
- [ ] `mostrar` - Obtener registro por ID para edición
- [ ] `eliminar` - Soft delete (activo=0)
- [ ] `activar` - Reactivar registro (activo=1)
- [ ] `verificar` - Validar unicidad de campos

**Operaciones Adicionales (opcionales):**
- [ ] `estadisticas` - Contadores para dashboard
- [ ] `listarDisponibles` - Solo registros activos
- [ ] `buscar` - Búsqueda avanzada con filtros
- [ ] Otras: `_____________`

### ✅ Campos de la Tabla

Lista los campos por categorías:

**Campos Obligatorios:**
- `_____________` (ejemplo: codigo_proveedor, nombre_proveedor)

**Campos Opcionales (pueden ser NULL):**
- `_____________` (ejemplo: email_proveedor, telefono_proveedor)

**Campos tipo Foreign Key:**
- `_____________` → Tabla referenciada: `_____________`
- `_____________` → Tabla referenciada: `_____________`

**Campos tipo Date/DateTime:**
- `_____________` (ejemplo: fecha_compra, fecha_inicio_evento)

**Campos tipo Boolean/TINYINT:**
- `_____________` (ejemplo: es_principal, activo_proveedor)

**Campos tipo DECIMAL:**
- `_____________` (ejemplo: precio_alquiler, descuento_pago)

### ✅ Estadísticas (si aplica)

Si incluyes la operación `estadisticas`, define qué contadores necesitas:

1. **Total de registros**: Nombre del contador: `_____________`
2. **Registros activos**: Nombre del contador: `_____________`
3. **Contador adicional 1**: `_____________`
4. **Contador adicional 2**: `_____________`

---

## 🎯 Prompt Base para Copiar y Pegar

```text
Necesito implementar el CONTROLLER para un módulo existente siguiendo el patrón MVC del proyecto.

⚠️ IMPORTANTE: El Modelo (models/[Entidad].php) YA ESTÁ IMPLEMENTADO.
Solo necesito el controller que lo utilice.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): [entidad]
- Nombre del Modelo (clase): [Entidad]
- Controller a crear: controller/[entidad].php
- Modelo existente: models/[Entidad].php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
[PEGAR AQUÍ EL CREATE TABLE COMPLETO CON FOREIGN KEYS E INDEXES]
```

🔧 MÉTODOS DEL MODELO DISPONIBLES:

El modelo [Entidad] tiene implementados estos métodos:

1. **get_[entidades]()** - Retorna array con todos los registros (incluye JOINs si hay vista SQL)
2. **get_[entidades]_disponibles()** - Retorna solo registros activos
3. **get_[entidad]xid($id_[entidad])** - Retorna un registro por ID
4. **insert_[entidad](...)** - Inserta nuevo registro, retorna ID insertado o false
5. **update_[entidad]($id_[entidad], ...)** - Actualiza registro, retorna rowCount o false
6. **delete_[entidad]xid($id_[entidad])** - Soft delete (activo=0), retorna boolean
7. **activar_[entidad]xid($id_[entidad])** - Reactivar (activo=1), retorna boolean
8. **verificar[Entidad]($campo_unico, $id_[entidad])** - Valida unicidad, retorna ['existe' => boolean]

📋 OPERACIONES DEL CONTROLLER A IMPLEMENTAR:

Implementar estas operaciones en el switch($_GET["op"]):

✅ **listar** (GET/POST)
   - Llamar a: `$modelo->get_[entidades]()`
   - Formato de salida: DataTables JSON
   - Estructura: `{ draw, recordsTotal, recordsFiltered, data: [...] }`
   - Incluir todos los campos de la tabla/vista
   - Header: `Content-Type: application/json`

✅ **guardaryeditar** (POST)
   - Si `$_POST["id_[entidad]"]` está vacío → INSERT
   - Si `$_POST["id_[entidad]"]` tiene valor → UPDATE
   - **IMPORTANTE:** Convertir campos opcionales vacíos a NULL:
     ```php
     $id_campo_fk = null;
     if (isset($_POST["id_campo_fk"]) && $_POST["id_campo_fk"] !== '' && $_POST["id_campo_fk"] !== 'null') {
         $id_campo_fk = intval($_POST["id_campo_fk"]);
     }
     ```
   - Llamar a: `$modelo->insert_[entidad](...)` o `$modelo->update_[entidad](...)`
   - Respuesta JSON: `{ success: true/false, message: "...", id_[entidad]: X }`
   - Logging con RegistroActividad en ambos casos
   - Try-catch para manejo de errores

✅ **mostrar** (POST)
   - Recibe: `$_POST["id_[entidad]"]`
   - Llamar a: `$modelo->get_[entidad]xid($id)`
   - Retornar: JSON con todos los campos del registro
   - Logging de la operación

✅ **eliminar** (POST)
   - Recibe: `$_POST["id_[entidad]"]`
   - Llamar a: `$modelo->delete_[entidad]xid($id)`
   - Respuesta JSON: `{ success: true/false, message: "..." }`
   - Logging de la operación

✅ **activar** (POST)
   - Recibe: `$_POST["id_[entidad]"]`
   - Llamar a: `$modelo->activar_[entidad]xid($id)`
   - Respuesta JSON: `{ success: true/false, message: "..." }`
   - Logging de la operación

✅ **verificar** (GET)
   - Recibe: `$_GET["campo_unico"]`, `$_GET["id_[entidad]"]` (opcional)
   - Llamar a: `$modelo->verificar[Entidad]($campo, $id)`
   - Respuesta JSON: `{ success: true, existe: true/false }`
   - Sin logging (es validación rápida)

[SI APLICA - Operación adicional:]
✅ **estadisticas** (GET)
   - Llamar a: `$modelo->obtenerEstadisticas()`
   - Respuesta JSON: `{ success: true, data: { total: X, activos: Y, ... } }`
   - Sin logging

[SI APLICA - Operación adicional:]
✅ **listarDisponibles** (GET)
   - Llamar a: `$modelo->get_[entidades]_disponibles()`
   - Formato de salida: DataTables JSON (similar a listar)
   - Solo registros con activo_[entidad] = 1

🎯 DOCUMENTACIÓN TÉCNICA - SEGUIR EXACTAMENTE:

⚠️ **CRÍTICO:** Antes de generar el código, DEBES LEER Y SEGUIR FIELMENTE este archivo:

📖 **docs/controller.md**
   - **Contiene:** Estructura completa y convenciones de controllers
   - **Seguir exactamente:**
     * Nombre archivo: minúsculas `[entidad].php` (modelo es `[Entidad].php`)
     * Includes obligatorios:
       ```php
       require_once "../config/conexion.php";
       require_once "../config/funciones.php";
       require_once "../models/[Entidad].php";
       ```
     * Instancias obligatorias:
       ```php
       $registro = new RegistroActividad();
       $[entidad] = new [Entidad]();
       ```
     * Estructura switch: `switch ($_GET["op"]) { case "operacion": ... break; }`
     * Respuestas JSON: SIEMPRE con `JSON_UNESCAPED_UNICODE`
     * Headers: `header('Content-Type: application/json');` antes de echo json_encode()
     * Try-catch: En guardaryeditar y operaciones críticas
     * Logging: `$registro->registrarActividad('admin', '[entidad].php', 'operacion', "mensaje", 'info/error')`
     * Conversión NULL: Campos opcionales vacíos → null explícito
     * Validación POST: Verificar existencia antes de acceder
     * Operadores ternarios: `$_POST["campo"] ?? ''` para valores por defecto

✅ **Convenciones del proyecto (docs/.github/copilot-instructions.md):**
- Charset UTF-8 en archivos PHP
- Prepared statements en modelos (controller solo llama métodos)
- Mensajes en español con acentos correctos
- JSON siempre con `JSON_UNESCAPED_UNICODE`
- Logging obligatorio en operaciones que modifican datos
- No exponer detalles técnicos en mensajes de error al cliente

⚠️ **NO IMPROVISES - SIGUE EL PATRÓN:**
Los controllers existentes siguen un patrón estricto documentado en controller.md. COPIA la estructura exacta, adaptando ÚNICAMENTE:
- Nombres de campos según tu tabla
- Nombre de la entidad [entidad] / [Entidad]
- Llamadas a métodos del modelo específico
- Cantidad de parámetros según campos de la tabla

TODO LO DEMÁS debe ser EXACTAMENTE igual a la documentación.

📚 **DOCUMENTACIÓN COMPLETA DE REFERENCIA:**
1. `docs/controller.md` - ⭐ Estructura y convenciones de controllers
2. `docs/models.md` - Documentación del modelo (para referencia)
3. `docs/prompt_models.md` - Cómo se generó el modelo

Por favor, PRIMERO lee controller.md, LUEGO genera el archivo controller/[entidad].php siguiendo EXACTAMENTE los patrones documentados.
```

---

## 📝 Ejemplos de Uso Completos

### Ejemplo 1: Controller Simple (Proveedores)

```text
Necesito implementar el CONTROLLER para el módulo de Proveedores.

⚠️ El Modelo (models/Proveedor.php) YA ESTÁ IMPLEMENTADO.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): proveedor
- Nombre del Modelo (clase): Proveedor
- Controller a crear: controller/proveedor.php
- Modelo existente: models/Proveedor.php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

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

🔧 MÉTODOS DEL MODELO DISPONIBLES:
✅ Todos los métodos estándar implementados (get_proveedores, get_proveedorxid, insert_proveedor, update_proveedor, delete_proveedorxid, activar_proveedorxid, verificarProveedor)

📋 OPERACIONES DEL CONTROLLER A IMPLEMENTAR:
✅ listar - Listado completo para DataTables
✅ guardaryeditar - INSERT o UPDATE
✅ mostrar - Obtener por ID
✅ eliminar - Soft delete
✅ activar - Reactivar
✅ verificar - Validar unicidad de codigo_proveedor
✅ estadisticas - Contadores: total, activos, con_pedidos, nuevos_mes

🎯 DOCUMENTACIÓN: Seguir docs/controller.md exactamente.
Campos opcionales (pueden ser NULL): nif_proveedor, email_proveedor, telefono_proveedor, direccion_proveedor, notas_proveedor
```

### Ejemplo 2: Controller con Foreign Keys (Elementos)

```text
Necesito implementar el CONTROLLER para el módulo de Elementos.

⚠️ El Modelo (models/Elemento.php) YA ESTÁ IMPLEMENTADO.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): elemento
- Nombre del Modelo (clase): Elemento
- Controller a crear: controller/elemento.php
- Modelo existente: models/Elemento.php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
CREATE TABLE elemento (
    id_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_elemento VARCHAR(50) NOT NULL UNIQUE,
    id_articulo INT UNSIGNED NOT NULL,
    numero_serie_elemento VARCHAR(100),
    id_estado_elemento INT UNSIGNED,
    id_ubicacion INT UNSIGNED,
    observaciones_elemento TEXT,
    fecha_compra_elemento DATE,
    precio_compra_elemento DECIMAL(10,2),
    imagen_elemento VARCHAR(255),
    activo_elemento BOOLEAN DEFAULT TRUE,
    created_at_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_codigo_elemento (codigo_elemento),
    INDEX idx_numero_serie (numero_serie_elemento),
    INDEX idx_activo_elemento (activo_elemento),
    
    CONSTRAINT fk_elemento_articulo 
        FOREIGN KEY (id_articulo) 
        REFERENCES articulo(id_articulo)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_elemento_estado 
        FOREIGN KEY (id_estado_elemento) 
        REFERENCES estado_elemento(id_estado_elemento)
        ON DELETE SET NULL ON UPDATE CASCADE,
    
    CONSTRAINT fk_elemento_ubicacion 
        FOREIGN KEY (id_ubicacion) 
        REFERENCES ubicacion(id_ubicacion)
        ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

🔧 MÉTODOS DEL MODELO DISPONIBLES:
✅ Todos los métodos estándar implementados

📋 OPERACIONES DEL CONTROLLER A IMPLEMENTAR:
✅ listar - Listado completo (con JOINs desde vista SQL)
✅ guardaryeditar - INSERT o UPDATE
   **IMPORTANTE**: Estos campos FK son opcionales (NULL si vacío):
   - id_estado_elemento
   - id_ubicacion
✅ mostrar - Obtener por ID
✅ eliminar - Soft delete
✅ activar - Reactivar
✅ verificar - Validar unicidad de codigo_elemento y numero_serie_elemento
✅ estadisticas - Contadores: total, activos, disponibles, en_uso

🎯 DOCUMENTACIÓN: Seguir docs/controller.md exactamente.
Campos opcionales (NULL si vacío):
- numero_serie_elemento
- id_estado_elemento (FK)
- id_ubicacion (FK)
- observaciones_elemento
- fecha_compra_elemento
- precio_compra_elemento
- imagen_elemento
```

### Ejemplo 3: Controller con Operaciones Personalizadas (Presupuestos)

```text
Necesito implementar el CONTROLLER para el módulo de Presupuestos.

⚠️ El Modelo (models/Presupuesto.php) YA ESTÁ IMPLEMENTADO.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): presupuesto
- Nombre del Modelo (clase): Presupuesto
- Controller a crear: controller/presupuesto.php
- Modelo existente: models/Presupuesto.php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
CREATE TABLE presupuesto (
    id_presupuesto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_presupuesto VARCHAR(50) NOT NULL UNIQUE,
    id_cliente INT UNSIGNED NOT NULL,
    id_contacto_cliente INT UNSIGNED,
    id_estado_ppto INT UNSIGNED NOT NULL,
    id_forma_pago INT UNSIGNED,
    id_metodo_pago INT UNSIGNED,
    id_metodo INT UNSIGNED,
    fecha_presupuesto DATE NOT NULL,
    fecha_validez_presupuesto DATE,
    fecha_inicio_evento_presupuesto DATETIME,
    fecha_fin_evento_presupuesto DATETIME,
    numero_pedido_cliente_presupuesto VARCHAR(100),
    nombre_evento_presupuesto VARCHAR(255) NOT NULL,
    direccion_evento_presupuesto VARCHAR(255),
    poblacion_evento_presupuesto VARCHAR(100),
    cp_evento_presupuesto VARCHAR(10),
    provincia_evento_presupuesto VARCHAR(100),
    observaciones_cabecera_presupuesto TEXT,
    observaciones_cabecera_ingles_presupuesto TEXT,
    observaciones_pie_presupuesto TEXT,
    observaciones_pie_ingles_presupuesto TEXT,
    mostrar_obs_familias_presupuesto BOOLEAN DEFAULT TRUE,
    mostrar_obs_articulos_presupuesto BOOLEAN DEFAULT TRUE,
    observaciones_internas_presupuesto TEXT,
    activo_presupuesto BOOLEAN DEFAULT TRUE,
    created_at_presupuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_presupuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_numero (numero_presupuesto),
    INDEX idx_cliente (id_cliente),
    INDEX idx_estado (id_estado_ppto),
    INDEX idx_fecha (fecha_presupuesto),
    INDEX idx_activo (activo_presupuesto),
    
    CONSTRAINT fk_presupuesto_cliente 
        FOREIGN KEY (id_cliente) 
        REFERENCES cliente(id_cliente)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_contacto 
        FOREIGN KEY (id_contacto_cliente) 
        REFERENCES contacto_cliente(id_contacto_cliente)
        ON DELETE SET NULL ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_estado 
        FOREIGN KEY (id_estado_ppto) 
        REFERENCES estado_presupuesto(id_estado_ppto)
        ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

🔧 MÉTODOS DEL MODELO DISPONIBLES:
✅ Todos los métodos estándar implementados
✅ Método adicional: obtenerEstadisticas()

📋 OPERACIONES DEL CONTROLLER A IMPLEMENTAR:
✅ listar - Listado completo (desde vista SQL compleja con múltiples JOINs)
✅ guardaryeditar - INSERT o UPDATE
   **IMPORTANTE**: Muchos campos FK opcionales (NULL si vacío):
   - id_contacto_cliente
   - id_forma_pago
   - id_metodo_pago
   - id_metodo
   - fecha_validez_presupuesto
   - fecha_inicio_evento_presupuesto
   - fecha_fin_evento_presupuesto
✅ mostrar - Obtener por ID
✅ eliminar - Soft delete
✅ activar - Reactivar
✅ desactivar - Desactivar explícito (adicional a eliminar)
✅ verificar - Validar unicidad de numero_presupuesto
✅ estadisticas - Contadores: total, activos, pendientes, aprobados, facturados

🎯 DOCUMENTACIÓN: Seguir docs/controller.md exactamente.
**Nota especial**: Este controller tiene muchos campos opcionales. Usar el patrón:
```php
$campo_opcional = null;
if (isset($_POST["campo_opcional"]) && $_POST["campo_opcional"] !== '' && $_POST["campo_opcional"] !== 'null') {
    $campo_opcional = intval($_POST["campo_opcional"]); // o tipo correspondiente
}
```

Campos TEXT opcionales: Si vienen vacíos, enviar cadena vacía '' en lugar de NULL.
Campos DATE/DATETIME opcionales: Si vienen vacíos, enviar NULL.
Campos FK opcionales: Si vienen vacíos, enviar NULL.
```

---

## 📌 Notas Importantes

### ⚠️ Antes de ejecutar el prompt:

1. **Verifica el Modelo**: Asegúrate de que el modelo está funcionando correctamente
   ```php
   // Prueba rápida en models/Entidad.php
   $entidad = new Entidad();
   $resultado = $entidad->get_entidades();
   var_dump($resultado); // Debe retornar array
   ```

2. **Identifica campos opcionales**: Revisa el CREATE TABLE para saber qué campos permiten NULL
   ```sql
   -- Obligatorio (NOT NULL)
   nombre_proveedor VARCHAR(255) NOT NULL
   
   -- Opcional (permite NULL implícito o explícito)
   email_proveedor VARCHAR(100)
   telefono_proveedor VARCHAR(20) DEFAULT NULL
   ```

3. **Conoce los Foreign Keys**: Lista qué campos son FK y a qué tablas apuntan

4. **Define operaciones adicionales**: Si necesitas más allá de las estándar, documéntalas

### ✅ Después de generar el controller:

1. **Probar endpoints básicos**:
   ```bash
   # Probar listar
   curl http://localhost/MDR/controller/[entidad].php?op=listar
   
   # Probar estadísticas (si aplica)
   curl http://localhost/MDR/controller/[entidad].php?op=estadisticas
   ```

2. **Verificar respuestas JSON**:
   - Todas deben tener `Content-Type: application/json`
   - Todas deben usar `JSON_UNESCAPED_UNICODE`
   - Estructura consistente: `{success, message, data/id}`

3. **Revisar logging**:
   ```bash
   # Ver logs del día
   cat public/logs/YYYY-MM-DD.json
   ```

4. **Probar operaciones CRUD**:
   - [ ] Crear registro nuevo (guardaryeditar sin ID)
   - [ ] Editar registro (guardaryeditar con ID)
   - [ ] Obtener registro (mostrar)
   - [ ] Desactivar registro (eliminar)
   - [ ] Reactivar registro (activar)
   - [ ] Validar unicidad (verificar)

5. **Validar manejo de NULL**:
   - Campos opcionales vacíos deben convertirse a NULL
   - Verificar que el modelo los acepta sin error

### 🔧 Ajustes comunes necesarios:

1. **Aumentar campos en listar()**: Si la vista SQL tiene campos calculados adicionales
2. **Personalizar estadísticas**: Adaptar los contadores según la lógica de negocio
3. **Añadir validaciones**: Validaciones de negocio específicas antes de guardar
4. **Operaciones personalizadas**: Añadir cases adicionales al switch según necesidad

### 🎯 Checklist de Validación:

- [ ] Archivo creado en: `controller/[entidad].php`
- [ ] Nombre del archivo en minúsculas
- [ ] Includes correctos (conexion, funciones, modelo)
- [ ] Instancias de RegistroActividad y Modelo
- [ ] Switch con todas las operaciones requeridas
- [ ] Conversión NULL en campos opcionales
- [ ] Try-catch en operaciones críticas
- [ ] Logging en operaciones que modifican datos
- [ ] Headers JSON correctos
- [ ] JSON_UNESCAPED_UNICODE en todos los json_encode
- [ ] Respuestas estandarizadas
- [ ] Sin errores de sintaxis PHP

---

## 🎯 Resultado Esperado

Al usar este prompt, el asistente generará el archivo `controller/[entidad].php` con:

### ✅ Estructura Completa:

```php
<?php
require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/[Entidad].php";

$registro = new RegistroActividad();
$[entidad] = new [Entidad]();

switch ($_GET["op"]) {
    case "listar":
        // Código listar
        break;
    
    case "guardaryeditar":
        // Código INSERT/UPDATE con manejo NULL
        break;
    
    case "mostrar":
        // Código obtener por ID
        break;
    
    case "eliminar":
        // Código soft delete
        break;
    
    case "activar":
        // Código reactivar
        break;
    
    case "verificar":
        // Código validar unicidad
        break;
    
    case "estadisticas": // Si aplica
        // Código estadísticas
        break;
}
?>
```

### 🎨 Características Implementadas:

- ✅ Estructura switch con operaciones estándar
- ✅ Manejo correcto de campos opcionales (NULL)
- ✅ Logging en operaciones críticas
- ✅ Try-catch para errores
- ✅ Respuestas JSON estandarizadas
- ✅ Headers correctos
- ✅ Conversión de tipos adecuada
- ✅ Validaciones antes de operaciones
- ✅ Mensajes en español
- ✅ Integración con modelo existente

---

## 📚 Referencias

- **Documentación completa**: [docs/controller.md](./controller.md) ⭐
- **Documentación del modelo**: [docs/models.md](./models.md)
- **Prompt para modelos**: [docs/prompt_models.md](./prompt_models.md)
- **Convenciones del proyecto**: `.github/copilot-instructions.md`

---

**Última actualización:** 23 de diciembre de 2024  
**Versión:** 1.0  
**Proyecto:** MDR ERP Manager  
**Autor:** Luis - Innovabyte
