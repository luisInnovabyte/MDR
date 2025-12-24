# Prompt para Generar Modelo (Model)
## Plantilla de solicitud para implementar un modelo PHP con acceso a datos

> **Propósito:** Prompt reutilizable para que un asistente de IA genere el modelo PHP  
> **Contexto:** Se generará el modelo con operaciones CRUD estándar usando PDO.  
> **Documentación:** Basado en [models.md](./models.md)

---

## 📋 Checklist Pre-Implementación

Antes de usar el prompt, recopila y ten listos estos elementos:

### ✅ Estructura de Base de Datos

- [ ] **Tabla de base de datos creada** 
  - SQL CREATE TABLE completo para copiar
  - Incluir todos los FOREIGN KEYs con ON DELETE/UPDATE
  - Incluir todos los INDEXes
  - Incluir comentarios de tabla y campos si existen
  - Verificar tipos de datos (VARCHAR, INT, DECIMAL, DATE, etc.)

- [ ] **Vista SQL (si existe)**
  - CREATE VIEW completo con JOINs
  - Lista de campos calculados incluidos
  - Tablas relacionadas en la vista

### ✅ Información de la Entidad

- [ ] **Nombre de la entidad** (singular): `_____________`
  - Ejemplo: `presupuesto`, `cliente`, `articulo`, `proveedor`

- [ ] **Nombre del modelo** (PascalCase): `_____________`
  - Ejemplo: `Presupuesto`, `Cliente`, `Articulo`, `Proveedor`

- [ ] **Prefijo de tabla**: `_____________`
  - Ejemplo: `presupuesto`, `cliente`, `articulo`, `proveedor`
  - Todos los campos de la tabla deben tener este sufijo: `nombre_presupuesto`, `id_cliente`, etc.

- [ ] **Primary Key**: `_____________`
  - Ejemplo: `id_presupuesto`, `id_cliente`, `id_articulo`

- [ ] **Campo único para validación**: `_____________`
  - Ejemplo: `numero_presupuesto`, `codigo_cliente`, `codigo_articulo`
  - Este campo se usará en el método `verificar[Entidad]()`

### ✅ Clasificación de Campos

Agrupa los campos de tu tabla según su tipo:

**Campos Obligatorios (NOT NULL):**
- `_____________` (ejemplo: numero_presupuesto, nombre_cliente)
- `_____________` 
- `_____________` 

**Campos Opcionales (NULL):**
- `_____________` (ejemplo: email_cliente, telefono_proveedor)
- `_____________` 
- `_____________` 

**Foreign Keys (relaciones):**
- `_____________` → Tabla: `_____________` (OBLIGATORIO/OPCIONAL)
- `_____________` → Tabla: `_____________` (OBLIGATORIO/OPCIONAL)
- `_____________` → Tabla: `_____________` (OBLIGATORIO/OPCIONAL)

**Campos tipo DATE o DATETIME:**
- `_____________` (ejemplo: fecha_presupuesto, fecha_nacimiento_cliente)
- `_____________` 

**Campos tipo DECIMAL:**
- `_____________` (ejemplo: precio_articulo, descuento_cliente)
- `_____________` 

**Campos tipo TEXT:**
- `_____________` (ejemplo: observaciones_presupuesto, descripcion_articulo)
- `_____________` 

**Campos tipo BOOLEAN:**
- `_____________` (ejemplo: mostrar_obs_familias_presupuesto, es_principal_contacto)
- `_____________` 

**Campos de control (automáticos):**
- `activo_[entidad]` - Soft delete
- `created_at_[entidad]` - Fecha de creación
- `updated_at_[entidad]` - Fecha de actualización

### ✅ Vista SQL

- [ ] **¿Existe una vista SQL para esta entidad?**
  - [ ] SÍ - La entidad tiene muchas relaciones (>3 tablas relacionadas)
  - [ ] NO - La tabla es simple o tiene pocas relaciones

- [ ] **Nombre de la vista** (si existe): `_____________`
  - Ejemplo: `vista_presupuesto_completa`, `vista_cliente_completa`

- [ ] **Campos calculados en la vista** (si existen):
  - `_____________` (ejemplo: duracion_evento_dias, direccion_completa_cliente)
  - `_____________` 
  - `_____________` 

### ✅ Métodos del Modelo

Marca los métodos que necesitas:

**Métodos Estándar (TODOS recomendados):**
- [ ] `__construct()` - Constructor con PDO y RegistroActividad
- [ ] `get_[entidades]()` - Listar todos los registros
- [ ] `get_[entidades]_disponibles()` - Listar solo activos
- [ ] `get_[entidad]xid($id)` - Obtener un registro por ID
- [ ] `insert_[entidad](...)` - Insertar nuevo registro
- [ ] `update_[entidad]($id, ...)` - Actualizar registro existente
- [ ] `delete_[entidad]xid($id)` - Soft delete (activo=0)
- [ ] `activar_[entidad]xid($id)` - Reactivar registro (activo=1)
- [ ] `verificar[Entidad]($campo, $id)` - Validar unicidad

**Métodos NO Estándar (opcionales):**
- [ ] `obtenerEstadisticas()` - Estadísticas y métricas para dashboard
- [ ] `get_[entidades]_por_[campo]($valor)` - Filtro específico
- [ ] Otros: `_____________`

### ✅ Necesidad de Estadísticas

Si marcaste `obtenerEstadisticas()`, define qué métricas necesitas:

- [ ] **Contadores básicos**:
  - [ ] Total de registros activos
  - [ ] Total de registros inactivos
  - [ ] Total general

- [ ] **Contadores por estado** (si aplica):
  - `_____________` (ejemplo: presupuestos aprobados, ventas completadas)
  - `_____________` 
  - `_____________` 

- [ ] **Alertas basadas en fechas** (si aplica):
  - `_____________` (ejemplo: presupuestos que caducan hoy, mantenimientos vencidos)
  - `_____________` 

- [ ] **Tasas de conversión o porcentajes**:
  - `_____________` (ejemplo: tasa de conversión presupuestos, índice de satisfacción)
  - `_____________` 

- [ ] **Análisis temporal**:
  - [ ] Registros creados este mes
  - [ ] Registros creados esta semana
  - [ ] Otros: `_____________`

---

## 🎯 Prompt Base para Copiar y Pegar

```text
Necesito implementar el MODELO para un módulo siguiendo el patrón MVC del proyecto.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): [entidad]
- Nombre del Modelo (clase): [Entidad]
- Archivo a crear: models/[Entidad].php
- Controller asociado: controller/[entidad].php (para referencia si existe)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
[PEGAR AQUÍ EL CREATE TABLE COMPLETO CON TODOS LOS DETALLES:
- Campos con tipos de datos
- FOREIGN KEYs con ON DELETE/UPDATE
- INDEXes
- Comentarios
- Configuración de ENGINE y CHARSET]
```

📊 VISTA SQL (si existe):

[SI EXISTE UNA VISTA, PEGAR AQUÍ EL CREATE VIEW COMPLETO]
[SI NO EXISTE VISTA, ELIMINAR ESTA SECCIÓN]

```sql
CREATE VIEW vista_[entidad]_completa AS
SELECT 
    -- Campos de la tabla principal
    -- JOINs con tablas relacionadas
    -- Campos calculados (CONCAT, DATEDIFF, CASE, etc.)
FROM [entidad] e
INNER JOIN ...
LEFT JOIN ...
```

🔧 MÉTODOS DEL MODELO A IMPLEMENTAR:

Implementar estos métodos siguiendo las convenciones documentadas:

✅ **__construct()** (Constructor)
   - Inicializar conexión PDO: `$this->conexion = (new Conexion())->getConexion();`
   - Inicializar RegistroActividad: `$this->registro = new RegistroActividad();`
   - Configurar zona horaria Madrid: `SET time_zone = 'Europe/Madrid'`
   - Try-catch para manejo de errores de zona horaria
   - Logging si falla la configuración

✅ **get_[entidades]()** (Listar todos)
   - Consultar: [VISTA SQL si existe] o [TABLA si no existe vista]
   - ORDER BY: [campo lógico de ordenación]
   - Retornar: `fetchAll(PDO::FETCH_ASSOC)`
   - Try-catch con logging de errores
   - Retornar array vacío en caso de error

✅ **get_[entidades]_disponibles()** (Listar solo activos)
   - Similar a get_[entidades]() pero con WHERE activo_[entidad] = 1
   - ORDER BY: [campo lógico de ordenación]
   - Try-catch con logging de errores

✅ **get_[entidad]xid($id_[entidad])** (Obtener por ID)
   - Consultar: [VISTA SQL] o [TABLA]
   - WHERE id_[entidad] = ?
   - bindValue con PDO::PARAM_INT
   - Retornar: `fetch(PDO::FETCH_ASSOC)`
   - Try-catch con logging de errores
   - Retornar false en caso de error

✅ **insert_[entidad](...)** (Insertar nuevo registro)
   - Parámetros: TODOS los campos de la tabla EXCEPTO:
     * id_[entidad] (auto_increment)
     * activo_[entidad] (siempre 1)
     * created_at_[entidad] (NOW())
     * updated_at_[entidad] (NOW())
   - **IMPORTANTE - Manejo de campos opcionales:**
     ```php
     if (!empty($campo_opcional)) {
         $stmt->bindValue(N, $campo_opcional, PDO::PARAM_[TYPE]);
     } else {
         $stmt->bindValue(N, null, PDO::PARAM_NULL);
     }
     ```
   - Tipos PDO según campo:
     * Strings: PDO::PARAM_STR
     * Enteros/IDs: PDO::PARAM_INT
     * Booleanos: PDO::PARAM_BOOL
     * NULL: PDO::PARAM_NULL
   - Configurar zona horaria al inicio: `SET time_zone = 'Europe/Madrid'`
   - INSERT con activo_[entidad] = 1, created_at = NOW(), updated_at = NOW()
   - Retornar: `lastInsertId()` si éxito
   - Logging con RegistroActividad: "Se insertó [entidad] ID: X"
   - Try-catch con throw Exception en error

✅ **update_[entidad]($id_[entidad], ...)** (Actualizar registro)
   - Primer parámetro: $id_[entidad]
   - Resto de parámetros: Todos los campos editables
   - UPDATE con SET de todos los campos + updated_at_[entidad] = NOW()
   - WHERE id_[entidad] = ?
   - **ID va como ÚLTIMO parámetro en bindValue** (después de todos los SET)
   - Manejo de campos opcionales igual que en insert
   - Configurar zona horaria al inicio
   - Retornar: `rowCount()` (número de filas afectadas)
   - Logging: "Se actualizó [entidad] ID: X"
   - Try-catch con throw Exception

✅ **delete_[entidad]xid($id_[entidad])** (Soft delete)
   - UPDATE [entidad] SET activo_[entidad] = 0 WHERE id_[entidad] = ?
   - **NO hacer DELETE físico**
   - bindValue con PDO::PARAM_INT
   - Retornar: `rowCount() > 0` (boolean)
   - Logging: "Se desactivó [entidad] ID: X"
   - Try-catch con logging de errores y retornar false

✅ **activar_[entidad]xid($id_[entidad])** (Reactivar)
   - UPDATE [entidad] SET activo_[entidad] = 1 WHERE id_[entidad] = ?
   - bindValue con PDO::PARAM_INT
   - Retornar: `rowCount() > 0` (boolean)
   - Logging: "Se activó [entidad] ID: X"
   - Try-catch con logging de errores y retornar false

✅ **verificar[Entidad]($campo_unico, $id_[entidad] = null)** (Validar unicidad)
   - SELECT COUNT(*) FROM [entidad] WHERE LOWER(campo_unico) = LOWER(?)
   - Si $id_[entidad] no es null: AND id_[entidad] != ?
   - Parámetros dinámicos en execute: $params = [trim($campo_unico), $id_[entidad]]
   - Retornar: `['existe' => ($resultado['total'] > 0)]`
   - Try-catch con logging y retornar: `['existe' => false, 'error' => $e->getMessage()]`

[SI APLICA - Método adicional:]
✅ **obtenerEstadisticas()** (Métricas y análisis)
   - Array asociativo con todas las estadísticas
   - Consultas COUNT, SUM, AVG según necesidad
   - Cálculos de tasas de conversión, porcentajes
   - Alertas basadas en fechas (CURDATE(), DATEDIFF, etc.)
   - Agrupaciones por estado, categoría, período
   - Try-catch con logging
   - Retornar: Array con todas las métricas o ['error' => true, 'mensaje' => ...]
   - **NOTA:** Solo implementar si realmente se necesitan métricas complejas

🎯 DOCUMENTACIÓN TÉCNICA - SEGUIR EXACTAMENTE:

⚠️ **CRÍTICO:** Antes de generar el código, DEBES LEER Y SEGUIR FIELMENTE este archivo:

📖 **docs/controller-models/models.md**
   - **Contiene:** Estructura completa y convenciones de modelos
   - **Seguir exactamente:**
     * Nombre archivo: PascalCase `[Entidad].php`
     * Nombre clase: `class [Entidad]` (primera letra mayúscula)
     * Requires obligatorios:
       ```php
       require_once '../config/conexion.php';
       require_once '../config/funciones.php';
       ```
     * Propiedades privadas: `$conexion`, `$registro`
     * Zona horaria: Siempre configurar a 'Europe/Madrid'
     * Prepared statements: SIEMPRE con bindValue(), NUNCA concatenación
     * Manejo NULL: Validar `!empty()` antes de bindValue, usar PDO::PARAM_NULL si vacío
     * Logging: `$this->registro->registrarActividad('admin', '[Entidad]', 'metodo', 'mensaje', 'info/error')`
     * Try-catch: En TODOS los métodos críticos
     * Retornos consistentes:
       - get_*(): array o false
       - insert_*(): lastInsertId() o false/exception
       - update_*(): rowCount() o false/exception
       - delete_*/activar_*(): boolean
       - verificar*(): array ['existe' => bool]
     * Convenciones de nombres:
       - Métodos: snake_case con prefijo get_/insert_/update_/delete_/activar_/verificar
       - Parámetros: snake_case con sufijo _[entidad]
       - Campos SQL: siempre con sufijo _[entidad]

✅ **Preferir VISTAS sobre TABLAS cuando:**
- La tabla tiene más de 3 relaciones (FOREIGN KEYs)
- Se necesitan campos calculados frecuentemente
- Las consultas SELECT son muy complejas con múltiples JOINs
- **IMPORTANTE:** get_[entidades]() y get_[entidades]_disponibles() usan VISTA si existe
- **IMPORTANTE:** get_[entidad]xid() usa VISTA si existe
- **IMPORTANTE:** INSERT/UPDATE/DELETE siempre usan la TABLA directamente

✅ **Convenciones del proyecto (.github/copilot-instructions.md):**
- Charset UTF-8 en archivos PHP
- PDO con prepared statements SIEMPRE
- Mensajes en español con acentos correctos
- Logging obligatorio en operaciones que modifican datos (INSERT/UPDATE/DELETE/ACTIVAR)
- Try-catch en todos los métodos con logging de errores
- No exponer detalles técnicos de SQL en excepciones
- Zona horaria Europe/Madrid configurada en constructor y métodos INSERT/UPDATE

⚠️ **NO IMPROVISES - SIGUE EL PATRÓN:**
Los modelos existentes siguen un patrón estricto documentado en models.md. COPIA la estructura exacta, adaptando ÚNICAMENTE:
- Nombres de campos según tu tabla
- Nombre de la entidad [entidad] / [Entidad]
- Cantidad de parámetros según campos de la tabla
- Orden de campos en INSERT/UPDATE según tu tabla
- Vista SQL si existe, tabla si no existe

TODO LO DEMÁS debe ser EXACTAMENTE igual a la documentación.

📚 **DOCUMENTACIÓN COMPLETA DE REFERENCIA:**
1. `docs/controller-models/models.md` - ⭐ Estructura y convenciones de modelos
2. `docs/controller-models/controller.md` - Documentación del controller (para referencia)
3. `docs/controller-models/prompt_controller.md` - Cómo se generará el controller

📝 **CAMPOS DE LA TABLA (para referencia rápida):**

**Obligatorios (NOT NULL):**
[Listar campos obligatorios]

**Opcionales (pueden ser NULL):**
[Listar campos opcionales]

**Foreign Keys:**
[Listar FKs con tabla destino y obligatoriedad]

**Fechas:**
[Listar campos DATE/DATETIME]

**Decimales:**
[Listar campos DECIMAL]

**Texto largo:**
[Listar campos TEXT]

Por favor, PRIMERO lee docs/controller-models/models.md, LUEGO genera el archivo models/[Entidad].php siguiendo EXACTAMENTE los patrones documentados.
```

---

## 📝 Ejemplos de Uso Completos

### Ejemplo 1: Modelo Simple (Proveedores)

```text
Necesito implementar el MODELO para el módulo de Proveedores.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): proveedor
- Nombre del Modelo (clase): Proveedor
- Archivo a crear: models/Proveedor.php

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
CREATE TABLE proveedor (
    -- Identificación
    id_proveedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_proveedor VARCHAR(50) NOT NULL UNIQUE 
        COMMENT 'Código único del proveedor',
    
    -- Datos principales
    nombre_proveedor VARCHAR(255) NOT NULL 
        COMMENT 'Razón social o nombre comercial',
    nif_proveedor VARCHAR(20) 
        COMMENT 'NIF/CIF del proveedor',
    
    -- Contacto
    email_proveedor VARCHAR(100),
    telefono_proveedor VARCHAR(20),
    movil_proveedor VARCHAR(20),
    web_proveedor VARCHAR(255),
    
    -- Dirección
    direccion_proveedor VARCHAR(255),
    poblacion_proveedor VARCHAR(100),
    cp_proveedor VARCHAR(10),
    provincia_proveedor VARCHAR(100),
    pais_proveedor VARCHAR(100) DEFAULT 'España',
    
    -- Datos comerciales
    forma_pago_proveedor VARCHAR(100)
        COMMENT 'Condiciones de pago acordadas',
    dias_pago_proveedor INT UNSIGNED
        COMMENT 'Días para pago acordados',
    descuento_proveedor DECIMAL(5,2)
        COMMENT 'Descuento estándar (%)',
    
    -- Observaciones
    notas_proveedor TEXT,
    
    -- Control
    activo_proveedor BOOLEAN DEFAULT TRUE,
    created_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_codigo_proveedor (codigo_proveedor),
    INDEX idx_nombre_proveedor (nombre_proveedor),
    INDEX idx_nif_proveedor (nif_proveedor),
    INDEX idx_activo_proveedor (activo_proveedor)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Gestión de proveedores del sistema';
```

📊 VISTA SQL: NO existe vista (tabla simple sin relaciones complejas)

🔧 MÉTODOS A IMPLEMENTAR:
✅ Todos los métodos estándar (constructor, get_proveedores, get_proveedorxid, insert_proveedor, update_proveedor, delete_proveedorxid, activar_proveedorxid, verificarProveedor)
✅ obtenerEstadisticas() - Contadores: total_activos, con_pedidos_activos, nuevos_mes

📝 CAMPOS DE LA TABLA:

**Obligatorios:**
- codigo_proveedor (VARCHAR 50)
- nombre_proveedor (VARCHAR 255)

**Opcionales:**
- nif_proveedor, email_proveedor, telefono_proveedor, movil_proveedor, web_proveedor
- direccion_proveedor, poblacion_proveedor, cp_proveedor, provincia_proveedor, pais_proveedor
- forma_pago_proveedor, dias_pago_proveedor, descuento_proveedor, notas_proveedor

**Decimales:**
- descuento_proveedor (DECIMAL 5,2)

**Texto largo:**
- notas_proveedor (TEXT)

🎯 DOCUMENTACIÓN: Seguir docs/controller-models/models.md exactamente.
Campo único para verificación: codigo_proveedor
ORDER BY: nombre_proveedor ASC
```

### Ejemplo 2: Modelo con Foreign Keys (Elementos)

```text
Necesito implementar el MODELO para el módulo de Elementos.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): elemento
- Nombre del Modelo (clase): Elemento
- Archivo a crear: models/Elemento.php

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
CREATE TABLE elemento (
    -- Identificación
    id_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_elemento VARCHAR(50) NOT NULL UNIQUE
        COMMENT 'Código único del elemento (ej: MIC-001)',
    
    -- Relación con artículo (OBLIGATORIO)
    id_articulo INT UNSIGNED NOT NULL
        COMMENT 'Artículo al que pertenece este elemento',
    
    -- Identificación del elemento
    numero_serie_elemento VARCHAR(100)
        COMMENT 'Número de serie del fabricante',
    
    -- Estado y ubicación (OPCIONALES)
    id_estado_elemento INT UNSIGNED
        COMMENT 'Estado actual del elemento',
    id_ubicacion INT UNSIGNED
        COMMENT 'Ubicación física del elemento',
    
    -- Información adicional
    observaciones_elemento TEXT,
    fecha_compra_elemento DATE,
    precio_compra_elemento DECIMAL(10,2),
    
    -- Imagen del elemento
    imagen_elemento VARCHAR(255)
        COMMENT 'Ruta de la imagen del elemento',
    
    -- Control
    activo_elemento BOOLEAN DEFAULT TRUE,
    created_at_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- Índices
    INDEX idx_codigo_elemento (codigo_elemento),
    INDEX idx_numero_serie (numero_serie_elemento),
    INDEX idx_articulo (id_articulo),
    INDEX idx_estado (id_estado_elemento),
    INDEX idx_ubicacion (id_ubicacion),
    INDEX idx_activo_elemento (activo_elemento),
    
    -- Foreign Keys
    CONSTRAINT fk_elemento_articulo 
        FOREIGN KEY (id_articulo) 
        REFERENCES articulo(id_articulo)
        ON DELETE RESTRICT 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_elemento_estado 
        FOREIGN KEY (id_estado_elemento) 
        REFERENCES estado_elemento(id_estado_elemento)
        ON DELETE SET NULL 
        ON UPDATE CASCADE,
    
    CONSTRAINT fk_elemento_ubicacion 
        FOREIGN KEY (id_ubicacion) 
        REFERENCES ubicacion(id_ubicacion)
        ON DELETE SET NULL 
        ON UPDATE CASCADE
        
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Elementos individuales de artículos (unidades físicas)';
```

📊 VISTA SQL:

```sql
CREATE VIEW vista_elemento_completa AS
SELECT 
    -- Datos del elemento
    e.id_elemento,
    e.codigo_elemento,
    e.numero_serie_elemento,
    e.observaciones_elemento,
    e.fecha_compra_elemento,
    e.precio_compra_elemento,
    e.imagen_elemento,
    e.activo_elemento,
    e.created_at_elemento,
    e.updated_at_elemento,
    
    -- Datos del artículo
    a.id_articulo,
    a.codigo_articulo,
    a.nombre_articulo,
    a.descripcion_articulo,
    a.precio_alquiler_articulo,
    
    -- Datos del estado
    est.id_estado_elemento,
    est.codigo_estado_elemento,
    est.nombre_estado_elemento,
    est.color_estado_elemento,
    
    -- Datos de la ubicación
    u.id_ubicacion,
    u.codigo_ubicacion,
    u.nombre_ubicacion,
    
    -- Campo calculado: Disponibilidad
    CASE
        WHEN est.codigo_estado_elemento = 'DISP' THEN 'Disponible'
        WHEN est.codigo_estado_elemento = 'ALQU' THEN 'Alquilado'
        WHEN est.codigo_estado_elemento = 'MANT' THEN 'En mantenimiento'
        WHEN est.codigo_estado_elemento = 'AVER' THEN 'Averiado'
        ELSE 'Desconocido'
    END AS estado_disponibilidad
    
FROM elemento e
INNER JOIN articulo a ON e.id_articulo = a.id_articulo
LEFT JOIN estado_elemento est ON e.id_estado_elemento = est.id_estado_elemento
LEFT JOIN ubicacion u ON e.id_ubicacion = u.id_ubicacion;
```

🔧 MÉTODOS A IMPLEMENTAR:
✅ Todos los métodos estándar
✅ obtenerEstadisticas() - Contadores: total_activos, disponibles, alquilados, en_mantenimiento, averiados

📝 CAMPOS DE LA TABLA:

**Obligatorios:**
- codigo_elemento (VARCHAR 50)
- id_articulo (INT UNSIGNED) - FK OBLIGATORIO

**Opcionales:**
- numero_serie_elemento (VARCHAR 100)
- id_estado_elemento (INT UNSIGNED) - FK OPCIONAL
- id_ubicacion (INT UNSIGNED) - FK OPCIONAL
- observaciones_elemento (TEXT)
- fecha_compra_elemento (DATE)
- precio_compra_elemento (DECIMAL 10,2)
- imagen_elemento (VARCHAR 255)

**Foreign Keys:**
- id_articulo → articulo(id_articulo) [OBLIGATORIO]
- id_estado_elemento → estado_elemento(id_estado_elemento) [OPCIONAL]
- id_ubicacion → ubicacion(id_ubicacion) [OPCIONAL]

**Fechas:**
- fecha_compra_elemento (DATE)

**Decimales:**
- precio_compra_elemento (DECIMAL 10,2)

**Texto largo:**
- observaciones_elemento (TEXT)

🎯 DOCUMENTACIÓN: Seguir docs/controller-models/models.md exactamente.
Campo único para verificación: codigo_elemento
ORDER BY: codigo_elemento ASC
Vista: vista_elemento_completa (usar en get_elementos, get_elementos_disponibles, get_elementoxid)
```

### Ejemplo 3: Modelo Complejo con Estadísticas (Presupuestos)

```text
Necesito implementar el MODELO para el módulo de Presupuestos.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): presupuesto
- Nombre del Modelo (clase): Presupuesto
- Archivo a crear: models/Presupuesto.php

📊 DEFINICIÓN DE LA TABLA (SQL):

[AQUÍ IRÍA EL CREATE TABLE COMPLETO DEL EJEMPLO DE docs/controller-models/models.md - OMITIDO POR BREVEDAD]

📊 VISTA SQL:

[AQUÍ IRÍA EL CREATE VIEW COMPLETO DEL EJEMPLO DE docs/controller-models/models.md - OMITIDO POR BREVEDAD]

🔧 MÉTODOS A IMPLEMENTAR:
✅ Todos los métodos estándar
✅ obtenerEstadisticas() con las siguientes métricas:

**Contadores básicos:**
- total_activos: Presupuestos activos
- total_inactivos: Presupuestos inactivos

**Contadores por estado:**
- pendientes: Estado 'PEND'
- aprobados: Estado 'APROB'
- rechazados: Estado 'RECH'
- facturados: Estado 'FACT'

**Tasas de conversión:**
- tasa_conversion: (aprobados / (aprobados + rechazados)) * 100

**Alertas de validez:**
- caduca_hoy: WHERE fecha_validez_presupuesto = CURDATE()
- caducados: WHERE fecha_validez_presupuesto < CURDATE()
- proximos_caducar: WHERE fecha_validez_presupuesto BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)

**Alertas de eventos:**
- evento_hoy: WHERE fecha_inicio_evento_presupuesto = CURDATE()
- evento_proximos: WHERE fecha_inicio_evento_presupuesto BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)

**Análisis temporal:**
- nuevos_mes: WHERE MONTH(created_at_presupuesto) = MONTH(CURDATE())
- nuevos_semana: WHERE created_at_presupuesto >= DATE_SUB(CURDATE(), INTERVAL 7 DAY)

📝 CAMPOS DE LA TABLA:

**Obligatorios:**
- numero_presupuesto (VARCHAR 50) - UNIQUE
- id_cliente (INT UNSIGNED) - FK OBLIGATORIO
- id_estado_ppto (INT UNSIGNED) - FK OBLIGATORIO
- fecha_presupuesto (DATE)
- nombre_evento_presupuesto (VARCHAR 255)

**Opcionales:**
- id_contacto_cliente, id_forma_pago, id_metodo_pago, id_metodo (FKs opcionales)
- fecha_validez_presupuesto, fecha_inicio_evento_presupuesto, fecha_fin_evento_presupuesto (fechas)
- Todos los campos de observaciones (TEXT)
- Campos de ubicación del evento
- Booleanos: mostrar_obs_familias_presupuesto, mostrar_obs_articulos_presupuesto

**Foreign Keys:**
- id_cliente → cliente(id_cliente) [OBLIGATORIO - RESTRICT]
- id_contacto_cliente → contacto_cliente(id_contacto_cliente) [OPCIONAL - SET NULL]
- id_estado_ppto → estado_presupuesto(id_estado_ppto) [OBLIGATORIO - RESTRICT]
- id_forma_pago → forma_pago(id_pago) [OPCIONAL - SET NULL]
- id_metodo → metodos_contacto(id_metodo) [OPCIONAL - SET NULL]

🎯 DOCUMENTACIÓN: Seguir docs/controller-models/models.md exactamente.
Campo único para verificación: numero_presupuesto
ORDER BY: fecha_presupuesto DESC
Vista: vista_presupuesto_completa (usar en todos los SELECT)

**NOTA ESPECIAL:** Este modelo tiene MUCHOS campos opcionales. Asegurar manejo correcto de NULL en insert/update.
```

---

## 📌 Notas Importantes

### ⚠️ Antes de ejecutar el prompt:

1. **Crea la tabla en la base de datos**:
   ```sql
   -- Ejecutar el CREATE TABLE
   -- Verificar que se creó correctamente
   SHOW CREATE TABLE [entidad];
   ```

2. **Crea la vista SQL si aplica**:
   ```sql
   -- Si la tabla tiene múltiples relaciones
   CREATE VIEW vista_[entidad]_completa AS ...
   ```

3. **Identifica campos obligatorios vs opcionales**:
   - Lee el CREATE TABLE cuidadosamente
   - Marca qué campos tienen `NOT NULL`
   - Marca qué campos son `FOREIGN KEY`

4. **Define si necesitas estadísticas**:
   - ¿Hay un dashboard para este módulo?
   - ¿Se necesitan métricas en tiempo real?
   - ¿Hay estados o categorías para agrupar?

### ✅ Después de generar el modelo:

1. **Verificar sintaxis PHP**:
   ```bash
   php -l models/[Entidad].php
   # Debe retornar: No syntax errors detected
   ```

2. **Probar la conexión**:
   ```php
   require_once 'models/[Entidad].php';
   $entidad = new [Entidad]();
   var_dump($entidad); // No debe dar error
   ```

3. **Probar método get_[entidades]()**:
   ```php
   $entidad = new [Entidad]();
   $resultado = $entidad->get_[entidades]();
   var_dump($resultado); // Debe retornar array
   ```

4. **Probar inserción**:
   ```php
   $id = $entidad->insert_[entidad](...parámetros de prueba);
   echo "ID insertado: $id"; // Debe retornar número
   ```

5. **Verificar logs**:
   ```bash
   # Revisar que se están generando logs
   cat public/logs/YYYY-MM-DD.json
   ```

### 🔧 Ajustes comunes necesarios:

1. **Orden de campos en INSERT/UPDATE**: Verificar que coincide con el orden de bindValue
2. **Tipos de datos en bindValue**: INT → PDO::PARAM_INT, VARCHAR → PDO::PARAM_STR
3. **Manejo de NULL**: Asegurar que campos opcionales usan PDO::PARAM_NULL cuando están vacíos
4. **Vista SQL**: Si hay errores, verificar que todos los campos tienen alias sin conflictos
5. **Estadísticas**: Ajustar consultas según la lógica de negocio real

### 🎯 Checklist de Validación:

- [ ] Archivo creado en: `models/[Entidad].php`
- [ ] Nombre de clase en PascalCase: `class [Entidad]`
- [ ] Requires correctos (conexion, funciones)
- [ ] Propiedades privadas ($conexion, $registro)
- [ ] Constructor con zona horaria Madrid
- [ ] Todos los métodos estándar implementados
- [ ] Prepared statements con bindValue en TODOS los métodos
- [ ] Manejo correcto de NULL en campos opcionales
- [ ] Try-catch en todos los métodos críticos
- [ ] Logging en operaciones que modifican datos
- [ ] Retornos consistentes según tipo de método
- [ ] Convenciones de nombres respetadas
- [ ] Vista SQL usada en SELECT (si existe)
- [ ] Tabla usada en INSERT/UPDATE/DELETE
- [ ] Sin errores de sintaxis PHP

---

## 🎯 Resultado Esperado

Al usar este prompt, el asistente generará el archivo `models/[Entidad].php` con:

### ✅ Estructura Completa:

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class [Entidad]
{
    private $conexion;
    private $registro;

    public function __construct() { ... }
    
    public function get_[entidades]() { ... }
    
    public function get_[entidades]_disponibles() { ... }
    
    public function get_[entidad]xid($id_[entidad]) { ... }
    
    public function insert_[entidad](...) { ... }
    
    public function update_[entidad]($id_[entidad], ...) { ... }
    
    public function delete_[entidad]xid($id_[entidad]) { ... }
    
    public function activar_[entidad]xid($id_[entidad]) { ... }
    
    public function verificar[Entidad]($campo, $id = null) { ... }
    
    public function obtenerEstadisticas() { ... } // Si aplica
}
?>
```

### 🎨 Características Implementadas:

- ✅ PDO con prepared statements en todas las consultas
- ✅ Zona horaria Europe/Madrid configurada
- ✅ RegistroActividad para logging
- ✅ Try-catch en todos los métodos críticos
- ✅ Manejo correcto de campos opcionales (NULL)
- ✅ Uso de vista SQL para SELECT (si existe)
- ✅ Uso de tabla directa para INSERT/UPDATE/DELETE
- ✅ Soft delete (activo=0) en lugar de DELETE físico
- ✅ Validación de unicidad con verificar[Entidad]
- ✅ Estadísticas complejas (si se solicitaron)
- ✅ Convenciones de nombres respetadas
- ✅ Mensajes en español
- ✅ Retornos consistentes

---

## 📚 Referencias

- **Documentación completa**: [docs/controller-models/models.md](./models.md) ⭐
- **Documentación del controller**: [docs/controller-models/controller.md](./controller.md)
- **Prompt para controllers**: [docs/controller-models/prompt_controller.md](./prompt_controller.md)
- **Convenciones del proyecto**: `.github/copilot-instructions.md`

---

**Última actualización:** 23 de diciembre de 2024  
**Versión:** 1.0  
**Proyecto:** MDR ERP Manager  
**Autor:** Luis - Innovabyte
