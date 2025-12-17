# 📦 Documentación de Modelos (Models)

## 🎯 Introducción

Los **modelos** son clases PHP que encapsulan toda la lógica de acceso a datos. Cada modelo representa una entidad del sistema (tabla de base de datos) y proporciona métodos para realizar operaciones CRUD y consultas especializadas.

---

## 🏗️ Arquitectura de Modelos

### Principios Fundamentales

1. **Un modelo por entidad principal**: Cada tabla importante tiene su propio modelo
2. **Uso de PDO**: Todas las consultas usan PHP Data Objects con prepared statements
3. **Separación de responsabilidades**: El modelo solo gestiona datos, no lógica de negocio
4. **Registro de actividad**: Todas las operaciones importantes se registran en logs
5. **Manejo de errores**: Try-catch en todos los métodos críticos

---

## 📋 Modelo de Ejemplo: `Presupuesto.php`

El modelo `Presupuesto.php` es el **más completo** del sistema y sirve como referencia para otros modelos.

---

## 🗄️ Estructura de la Tabla `presupuesto`

```sql
CREATE TABLE presupuesto (
    -- =====================================================
    -- IDENTIFICACIÓN
    -- =====================================================
    id_presupuesto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_presupuesto VARCHAR(50) NOT NULL UNIQUE,
    
    -- =====================================================
    -- RELACIONES (CLAVES FORÁNEAS)
    -- =====================================================
    id_cliente INT UNSIGNED NOT NULL,
    id_contacto_cliente INT UNSIGNED,
    id_estado_ppto INT UNSIGNED NOT NULL,
    id_forma_pago INT UNSIGNED,
    id_metodo INT,
    
    -- =====================================================
    -- FECHAS
    -- =====================================================
    fecha_presupuesto DATE NOT NULL 
        COMMENT 'Fecha de emisión del presupuesto',
    
    fecha_validez_presupuesto DATE 
        COMMENT 'Fecha hasta la que es válido el presupuesto',
    
    fecha_inicio_evento_presupuesto DATE 
        COMMENT 'Fecha de inicio del evento/servicio',
    
    fecha_fin_evento_presupuesto DATE 
        COMMENT 'Fecha de finalización del evento/servicio',
    
    -- =====================================================
    -- DATOS DEL EVENTO/PROYECTO
    -- =====================================================
    numero_pedido_cliente_presupuesto VARCHAR(80) 
        COMMENT 'Número de pedido del cliente',
    
    nombre_evento_presupuesto VARCHAR(255) 
        COMMENT 'Nombre del evento o proyecto',
    
    direccion_evento_presupuesto VARCHAR(100) 
        COMMENT 'Dirección del evento',
    
    poblacion_evento_presupuesto VARCHAR(80) 
        COMMENT 'Población/Ciudad del evento',
    
    cp_evento_presupuesto VARCHAR(10) 
        COMMENT 'Código postal del evento',
    
    provincia_evento_presupuesto VARCHAR(80) 
        COMMENT 'Provincia del evento',
    
    -- =====================================================
    -- OBSERVACIONES
    -- =====================================================
    observaciones_cabecera_presupuesto TEXT 
        COMMENT 'Observaciones iniciales (español)',
    
    observaciones_cabecera_ingles_presupuesto TEXT 
        COMMENT 'Observaciones iniciales (inglés)',
    
    observaciones_pie_presupuesto TEXT 
        COMMENT 'Observaciones al pie (español)',
    
    observaciones_pie_ingles_presupuesto TEXT 
        COMMENT 'Observaciones al pie (inglés)',
    
    mostrar_obs_familias_presupuesto BOOLEAN DEFAULT TRUE 
        COMMENT 'Mostrar observaciones de familias',
    
    mostrar_obs_articulos_presupuesto BOOLEAN DEFAULT TRUE 
        COMMENT 'Mostrar observaciones de artículos',
    
    observaciones_internas_presupuesto TEXT 
        COMMENT 'Notas internas (no imprimen en PDF)',
    
    -- =====================================================
    -- CONTROL
    -- =====================================================
    activo_presupuesto BOOLEAN DEFAULT TRUE,
    created_at_presupuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_presupuesto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- =====================================================
    -- CLAVES FORÁNEAS
    -- =====================================================
    CONSTRAINT fk_presupuesto_cliente 
        FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_contacto 
        FOREIGN KEY (id_contacto_cliente) REFERENCES contacto_cliente(id_contacto_cliente) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_estado 
        FOREIGN KEY (id_estado_ppto) REFERENCES estado_presupuesto(id_estado_ppto) 
        ON DELETE RESTRICT ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_forma_pago 
        FOREIGN KEY (id_forma_pago) REFERENCES forma_pago(id_pago) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    
    CONSTRAINT fk_presupuesto_metodo_contacto 
        FOREIGN KEY (id_metodo) REFERENCES metodos_contacto(id_metodo) 
        ON DELETE SET NULL ON UPDATE CASCADE,
    
    -- =====================================================
    -- ÍNDICES DE OPTIMIZACIÓN
    -- =====================================================
    INDEX idx_numero_presupuesto (numero_presupuesto),
    INDEX idx_id_cliente_presupuesto (id_cliente),
    INDEX idx_id_estado_presupuesto (id_estado_ppto),
    INDEX idx_fecha_presupuesto (fecha_presupuesto),
    INDEX idx_fecha_inicio_evento (fecha_inicio_evento_presupuesto),
    INDEX idx_fecha_fin_evento (fecha_fin_evento_presupuesto),
    INDEX idx_numero_pedido_cliente (numero_pedido_cliente_presupuesto),
    INDEX idx_poblacion_evento (poblacion_evento_presupuesto),
    INDEX idx_provincia_evento (provincia_evento_presupuesto)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
```

### Relaciones de la Tabla

```
presupuesto
  ├─→ cliente (id_cliente) [OBLIGATORIO - RESTRICT]
  ├─→ contacto_cliente (id_contacto_cliente) [OPCIONAL - SET NULL]
  ├─→ estado_presupuesto (id_estado_ppto) [OBLIGATORIO - RESTRICT]
  ├─→ forma_pago (id_forma_pago) [OPCIONAL - SET NULL]
  └─→ metodos_contacto (id_metodo) [OPCIONAL - SET NULL]
```

---

## 👁️ Vista SQL: `vista_presupuesto_completa`

### ¿Qué es una Vista SQL?

Una **vista** es una tabla virtual que combina datos de múltiples tablas relacionadas. En el proyecto MDR, cuando una entidad tiene **muchas relaciones** con otras tablas, se crea una vista SQL para:

1. **Simplificar las consultas**: No repetir JOINs complejos en cada método
2. **Mejorar el rendimiento**: MySQL optimiza las vistas
3. **Centralizar la lógica**: Los cambios en la vista afectan automáticamente a todas las consultas
4. **Incluir campos calculados**: Días de validez, duración de eventos, etc.

### ¿Cuándo usar Vistas en lugar de la Tabla directamente?

✅ **USA VISTA cuando**:
- La tabla tiene más de 3-4 relaciones con otras tablas
- Necesitas campos calculados frecuentemente (fechas, totales, estados)
- Las consultas SELECT se vuelven repetitivas y complejas
- Quieres ocultar la complejidad a los controladores

❌ **USA TABLA directamente cuando**:
- Son operaciones INSERT/UPDATE/DELETE (las vistas no soportan estos)
- La tabla es simple y no tiene relaciones complejas
- Solo necesitas campos específicos de la tabla principal

### Estructura de `vista_presupuesto_completa`

La vista combina **6 tablas relacionadas**:

```sql
CREATE VIEW vista_presupuesto_completa AS
SELECT 
    -- =====================================================
    -- DATOS DEL PRESUPUESTO (tabla principal)
    -- =====================================================
    p.id_presupuesto,
    p.numero_presupuesto,
    p.fecha_presupuesto,
    p.fecha_validez_presupuesto,
    p.fecha_inicio_evento_presupuesto,
    p.fecha_fin_evento_presupuesto,
    p.numero_pedido_cliente_presupuesto,
    p.nombre_evento_presupuesto,
    p.direccion_evento_presupuesto,
    p.poblacion_evento_presupuesto,
    p.cp_evento_presupuesto,
    p.provincia_evento_presupuesto,
    p.observaciones_cabecera_presupuesto,
    p.observaciones_pie_presupuesto,
    p.observaciones_cabecera_ingles_presupuesto,
    p.observaciones_pie_ingles_presupuesto,
    p.mostrar_obs_familias_presupuesto,
    p.mostrar_obs_articulos_presupuesto,
    p.observaciones_internas_presupuesto,
    p.activo_presupuesto,
    p.created_at_presupuesto,
    p.updated_at_presupuesto,
    
    -- Campo calculado: Ubicación completa
    CONCAT_WS(', ',
        p.direccion_evento_presupuesto,
        CONCAT(p.cp_evento_presupuesto, ' ', p.poblacion_evento_presupuesto),
        p.provincia_evento_presupuesto
    ) AS ubicacion_completa_evento_presupuesto,
    
    -- =====================================================
    -- DATOS DEL CLIENTE (tabla: cliente)
    -- =====================================================
    c.id_cliente,
    c.codigo_cliente,
    c.nombre_cliente,
    c.nif_cliente,
    c.direccion_cliente,
    c.cp_cliente,
    c.poblacion_cliente,
    c.provincia_cliente,
    c.telefono_cliente,
    c.email_cliente,
    c.web_cliente,
    c.direccion_facturacion_cliente,
    
    -- Campo calculado: Dirección completa del cliente
    CONCAT_WS(', ',
        c.direccion_cliente,
        CONCAT(c.cp_cliente, ' ', c.poblacion_cliente),
        c.provincia_cliente
    ) AS direccion_completa_cliente,
    
    -- =====================================================
    -- CONTACTO DEL CLIENTE (tabla: contacto_cliente)
    -- =====================================================
    cc.id_contacto_cliente,
    cc.nombre_contacto_cliente,
    cc.apellidos_contacto_cliente,
    cc.cargo_contacto_cliente,
    cc.telefono_contacto_cliente,
    cc.movil_contacto_cliente,
    cc.email_contacto_cliente,
    
    -- Campo calculado: Nombre completo del contacto
    CONCAT_WS(' ', cc.nombre_contacto_cliente, cc.apellidos_contacto_cliente) 
        AS nombre_completo_contacto_cliente,
    
    -- =====================================================
    -- ESTADO DEL PRESUPUESTO (tabla: estado_presupuesto)
    -- =====================================================
    ep.id_estado_ppto,
    ep.codigo_estado_ppto,
    ep.nombre_estado_ppto,
    ep.color_estado_ppto,
    ep.orden_estado_ppto,
    
    -- =====================================================
    -- FORMA DE PAGO (tabla: forma_pago)
    -- =====================================================
    fp.id_pago,
    fp.codigo_pago,
    fp.nombre_pago,
    fp.porcentaje_anticipo_pago,
    fp.dias_anticipo_pago,
    fp.porcentaje_final_pago,
    fp.dias_final_pago,
    fp.descuento_pago,
    
    -- =====================================================
    -- MÉTODO DE CONTACTO (tabla: metodos_contacto)
    -- =====================================================
    mc.id_metodo AS id_metodo_contacto,
    mc.nombre AS nombre_metodo_contacto,
    
    -- =====================================================
    -- CAMPOS CALCULADOS - VALIDEZ DEL PRESUPUESTO
    -- =====================================================
    (TO_DAYS(p.fecha_validez_presupuesto) - TO_DAYS(CURDATE())) 
        AS dias_validez_restantes,
    
    CASE
        WHEN p.fecha_validez_presupuesto IS NULL THEN 'Sin fecha de validez'
        WHEN p.fecha_validez_presupuesto < CURDATE() THEN 'Caducado'
        WHEN p.fecha_validez_presupuesto = CURDATE() THEN 'Caduca hoy'
        WHEN (TO_DAYS(p.fecha_validez_presupuesto) - TO_DAYS(CURDATE())) <= 7 
            THEN 'Próximo a caducar'
        ELSE 'Vigente'
    END AS estado_validez_presupuesto,
    
    -- =====================================================
    -- CAMPOS CALCULADOS - EVENTO
    -- =====================================================
    ((TO_DAYS(p.fecha_fin_evento_presupuesto) - 
      TO_DAYS(p.fecha_inicio_evento_presupuesto)) + 1) 
        AS duracion_evento_dias,
    
    (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) 
        AS dias_hasta_inicio_evento,
    
    (TO_DAYS(p.fecha_fin_evento_presupuesto) - TO_DAYS(CURDATE())) 
        AS dias_hasta_fin_evento,
    
    CASE
        WHEN p.fecha_inicio_evento_presupuesto IS NULL THEN 'Sin fecha de evento'
        WHEN p.fecha_fin_evento_presupuesto < CURDATE() THEN 'Evento finalizado'
        WHEN p.fecha_inicio_evento_presupuesto <= CURDATE() 
             AND p.fecha_fin_evento_presupuesto >= CURDATE() THEN 'Evento en curso'
        WHEN p.fecha_inicio_evento_presupuesto = CURDATE() THEN 'Evento inicia hoy'
        WHEN (TO_DAYS(p.fecha_inicio_evento_presupuesto) - TO_DAYS(CURDATE())) <= 7 
            THEN 'Evento próximo'
        ELSE 'Evento futuro'
    END AS estado_evento_presupuesto,
    
    -- =====================================================
    -- CAMPOS CALCULADOS - ANTIGÜEDAD
    -- =====================================================
    (TO_DAYS(CURDATE()) - TO_DAYS(p.fecha_presupuesto)) 
        AS dias_antiguedad_presupuesto

FROM presupuesto p
INNER JOIN cliente c ON p.id_cliente = c.id_cliente
LEFT JOIN contacto_cliente cc ON p.id_contacto_cliente = cc.id_contacto_cliente
INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
LEFT JOIN forma_pago fp ON p.id_forma_pago = fp.id_pago
LEFT JOIN metodos_contacto mc ON p.id_metodo = mc.id_metodo;
```

### Ventajas de esta Vista

✅ **Simplificación**: Un solo SELECT a la vista en lugar de 6 JOINs  
✅ **Campos calculados**: Días de validez, duración, estados automáticos  
✅ **Concatenaciones**: Direcciones completas, nombres completos  
✅ **Mantenibilidad**: Cambios en la vista afectan automáticamente a todas las consultas  
✅ **Rendimiento**: MySQL cachea y optimiza las vistas  

### Uso en el Modelo

```php
// ❌ MAL: Sin vista (consulta compleja repetida)
$sql = "SELECT p.*, c.nombre_cliente, c.direccion_cliente, ... 
        FROM presupuesto p
        INNER JOIN cliente c ON p.id_cliente = c.id_cliente
        LEFT JOIN contacto_cliente cc ON ...
        INNER JOIN estado_presupuesto ep ON ...
        WHERE p.activo_presupuesto = 1";

// ✅ BIEN: Con vista (consulta simple)
$sql = "SELECT * FROM vista_presupuesto_completa 
        WHERE activo_presupuesto = 1";
```

---

## 🔧 Métodos Estándar de un Modelo

### 1️⃣ Constructor `__construct()`

```php
public function __construct()
{
    $this->conexion = (new Conexion())->getConexion();
    $this->registro = new RegistroActividad();
    
    // Configurar zona horaria Madrid
    try {
        $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'system',
            'Presupuesto',
            '__construct',
            "No se pudo establecer zona horaria: " . $e->getMessage(),
            'warning'
        );
    }
}
```

**Función**: Inicializar la conexión PDO y el sistema de registro de actividad.

---

### 2️⃣ Listar Todos `get_[entidad]s()`

```php
public function get_presupuestos()
{
    try {
        $sql = "SELECT * FROM vista_presupuesto_completa 
                ORDER BY fecha_presupuesto DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'get_presupuestos',
            "Error al listar presupuestos: " . $e->getMessage(),
            "error"
        );
    }
}
```

**Función**: Obtener **todos** los registros de la vista/tabla.

**Convención de nombre**: `get_[entidad_plural]()`
- Ejemplos: `get_clientes()`, `get_articulos()`, `get_proveedores()`

---

### 3️⃣ Listar Activos `get_[entidad]s_disponibles()`

```php
public function get_presupuestos_disponibles()
{
    try {
        $sql = "SELECT * FROM vista_presupuesto_completa 
                WHERE activo_presupuesto = 1 
                ORDER BY fecha_presupuesto DESC";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'get_presupuestos_disponibles',
            "Error: " . $e->getMessage(),
            "error"
        );
    }
}
```

**Función**: Obtener solo registros **activos** (campo `activo_[entidad] = 1`).

**Convención de nombre**: `get_[entidad_plural]_disponibles()`
- Ejemplos: `get_clientes_disponibles()`, `get_articulos_disponibles()`

---

### 4️⃣ Obtener por ID `get_[entidad]xid($id)`

```php
public function get_presupuestoxid($id_presupuesto)
{
    try {
        $sql = "SELECT * FROM vista_presupuesto_completa 
                WHERE id_presupuesto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'get_presupuestoxid',
            "Error al obtener presupuesto {$id_presupuesto}: " . $e->getMessage(),
            "error"
        );
        return false;
    }
}
```

**Función**: Obtener **un único registro** por su ID primario.

**Convención de nombre**: `get_[entidad]xid($id_[entidad])`
- Ejemplos: `get_clientexid($id_cliente)`, `get_articuloxid($id_articulo)`

---

### 5️⃣ Insertar `insert_[entidad](...)`

```php
public function insert_presupuesto(
    $numero_presupuesto, 
    $id_cliente, 
    $id_contacto_cliente, 
    $id_estado_ppto, 
    $id_forma_pago, 
    // ... más parámetros ...
)
{
    try {
        $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        
        $sql = "INSERT INTO presupuesto 
                (numero_presupuesto, id_cliente, id_contacto_cliente, 
                 id_estado_ppto, id_forma_pago, ..., 
                 activo_presupuesto, created_at_presupuesto, updated_at_presupuesto) 
                VALUES (?, ?, ?, ?, ?, ..., 1, NOW(), NOW())";
        
        $stmt = $this->conexion->prepare($sql);
        
        // Binding de parámetros
        $stmt->bindValue(1, $numero_presupuesto, PDO::PARAM_STR);
        $stmt->bindValue(2, $id_cliente, PDO::PARAM_INT);
        
        // Campos opcionales con validación
        if (!empty($id_contacto_cliente)) {
            $stmt->bindValue(3, $id_contacto_cliente, PDO::PARAM_INT);
        } else {
            $stmt->bindValue(3, null, PDO::PARAM_NULL);
        }
        
        // ... más bindings ...
        
        $stmt->execute();
        $idInsert = $this->conexion->lastInsertId();
        
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'Insertar',
            "Se insertó presupuesto ID: $idInsert",
            'info'
        );
        
        return $idInsert;
        
    } catch (PDOException $e) {
        throw new Exception("Error SQL: " . $e->getMessage());
    }
}
```

**Función**: Insertar un **nuevo registro** en la tabla.

**Convención de nombre**: `insert_[entidad](...parámetros)`
- Ejemplos: `insert_cliente(...)`, `insert_articulo(...)`

**Importante**:
- ✅ Usar prepared statements con bindValue()
- ✅ Validar campos opcionales (NULL vs valor)
- ✅ Retornar el ID insertado con `lastInsertId()`
- ✅ Registrar la actividad en logs

---

### 6️⃣ Actualizar `update_[entidad]($id, ...)`

```php
public function update_presupuesto(
    $id_presupuesto, 
    $numero_presupuesto, 
    $id_cliente, 
    // ... más parámetros ...
)
{
    try {
        $this->conexion->exec("SET time_zone = 'Europe/Madrid'");
        
        $sql = "UPDATE presupuesto 
                SET numero_presupuesto = ?, 
                    id_cliente = ?, 
                    id_contacto_cliente = ?, 
                    ..., 
                    updated_at_presupuesto = NOW() 
                WHERE id_presupuesto = ?";
        
        $stmt = $this->conexion->prepare($sql);
        
        $stmt->bindValue(1, $numero_presupuesto, PDO::PARAM_STR);
        $stmt->bindValue(2, $id_cliente, PDO::PARAM_INT);
        // ... más bindings ...
        $stmt->bindValue(24, $id_presupuesto, PDO::PARAM_INT); // ID al final
        
        $stmt->execute();
        
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'Actualizar',
            "Se actualizó presupuesto ID: $id_presupuesto",
            'info'
        );
        
        return $stmt->rowCount();
        
    } catch (PDOException $e) {
        throw new Exception("Error SQL: " . $e->getMessage());
    }
}
```

**Función**: Actualizar un registro existente.

**Convención de nombre**: `update_[entidad]($id_[entidad], ...parámetros)`
- Ejemplos: `update_cliente($id_cliente, ...)`, `update_articulo($id_articulo, ...)`

**Importante**:
- ✅ El ID siempre es el **primer parámetro** y el **último en el bindValue**
- ✅ Actualizar automáticamente `updated_at_[entidad] = NOW()`
- ✅ Retornar `rowCount()` (número de filas afectadas)

---

### 7️⃣ Desactivar `delete_[entidad]xid($id)` o `desactivar_[entidad]xid($id)`

```php
public function delete_presupuestoxid($id_presupuesto)
{
    try {
        $sql = "UPDATE presupuesto 
                SET activo_presupuesto = 0 
                WHERE id_presupuesto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
        $stmt->execute();
        
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'Desactivar',
            "Se desactivó presupuesto ID: $id_presupuesto",
            'info'
        );
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'delete_presupuestoxid',
            "Error: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}
```

**Función**: **Desactivar** un registro (no se elimina físicamente).

**Convención de nombre**: `delete_[entidad]xid($id)` o `desactivar_[entidad]xid($id)`
- Ejemplos: `delete_clientexid($id)`, `desactivar_articuloxid($id)`

**Importante**:
- ⚠️ **NO se hace DELETE físico**, solo se pone `activo_[entidad] = 0`
- ✅ Esto preserva los datos y las relaciones con otras tablas
- ✅ Se pueden "recuperar" activándolos de nuevo

---

### 8️⃣ Activar `activar_[entidad]xid($id)`

```php
public function activar_presupuestoxid($id_presupuesto)
{
    try {
        $sql = "UPDATE presupuesto 
                SET activo_presupuesto = 1 
                WHERE id_presupuesto = ?";
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
        $stmt->execute();
        
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'Activar',
            "Se activó presupuesto ID: $id_presupuesto",
            'info'
        );
        
        return $stmt->rowCount() > 0;
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'activar_presupuestoxid',
            "Error: " . $e->getMessage(),
            "error"
        );
        return false;
    }
}
```

**Función**: **Reactivar** un registro previamente desactivado.

**Convención de nombre**: `activar_[entidad]xid($id_[entidad])`
- Ejemplos: `activar_clientexid($id)`, `activar_articuloxid($id)`

---

### 9️⃣ Verificar Existencia `verificar[Entidad]($campo, $id = null)`

```php
public function verificarPresupuesto($numero_presupuesto, $id_presupuesto = null)
{
    try {
        $sql = "SELECT COUNT(*) AS total 
                FROM presupuesto 
                WHERE LOWER(numero_presupuesto) = LOWER(?)";
        $params = [trim($numero_presupuesto)];

        // Si se proporciona ID, excluirlo (útil para edición)
        if (!empty($id_presupuesto)) {
            $sql .= " AND id_presupuesto != ?";
            $params[] = $id_presupuesto;
        }

        $stmt = $this->conexion->prepare($sql);
        $stmt->execute($params);
        $resultado = $stmt->fetch(PDO::FETCH_ASSOC);

        return [
            'existe' => ($resultado['total'] > 0)
        ];

    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            null,
            'Presupuesto',
            'verificarPresupuesto',
            "Error: " . $e->getMessage(),
            'error'
        );
        return [
            'existe' => false,
            'error' => $e->getMessage()
        ];
    }
}
```

**Función**: Verificar si un campo único ya existe (para validaciones).

**Convención de nombre**: `verificar[Entidad]($campo_unico, $id_opcional)`
- Ejemplos: `verificarCliente($codigo_cliente, $id_cliente)`, `verificarArticulo($codigo_articulo, $id_articulo)`

**Uso común**:
- ✅ Validar campos UNIQUE antes de INSERT
- ✅ Validar en UPDATE excluyendo el propio registro
- ✅ Retornar array con `['existe' => true/false]`

---

## 🎨 Métodos NO Estándar (Especializados)

### Método: `obtenerEstadisticas()`

Este método **NO es estándar** y solo se implementa cuando se necesitan **métricas y análisis** complejos.

```php
public function obtenerEstadisticas()
{
    try {
        $estadisticas = [];
        
        // ESTADÍSTICAS GENERALES
        $sql = "SELECT COUNT(*) as total 
                FROM presupuesto 
                WHERE activo_presupuesto = 1";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['total_activos'] = (int)$result['total'];
        
        // CONTEO POR ESTADO
        $sql = "SELECT COUNT(*) as total 
                FROM presupuesto p
                INNER JOIN estado_presupuesto ep ON p.id_estado_ppto = ep.id_estado_ppto
                WHERE p.activo_presupuesto = 1 
                AND ep.codigo_estado_ppto = 'APROB'";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['aprobados'] = (int)$result['total'];
        
        // TASA DE CONVERSIÓN
        $total_evaluables = $estadisticas['aprobados'] + $estadisticas['rechazados'];
        if ($total_evaluables > 0) {
            $estadisticas['tasa_conversion'] = 
                round(($estadisticas['aprobados'] / $total_evaluables) * 100, 2);
        } else {
            $estadisticas['tasa_conversion'] = 0;
        }
        
        // ALERTAS DE VALIDEZ
        $sql = "SELECT COUNT(*) as total 
                FROM presupuesto 
                WHERE activo_presupuesto = 1 
                AND fecha_validez_presupuesto = CURDATE()";
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        $estadisticas['caduca_hoy'] = (int)$result['total'];
        
        // ... más estadísticas ...
        
        return $estadisticas;
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            null,
            'Presupuesto',
            'obtenerEstadisticas',
            "Error: " . $e->getMessage(),
            'error'
        );
        return [
            'error' => true,
            'mensaje' => $e->getMessage()
        ];
    }
}
```

### ¿Cuándo implementar `obtenerEstadisticas()`?

✅ **Implementa este método cuando**:
- Necesitas **dashboards** con métricas en tiempo real
- Requieres análisis de **estados, tasas de conversión, totales**
- Hay **alertas** basadas en fechas o condiciones complejas
- Se necesitan **KPIs** (Key Performance Indicators) del módulo
- El cliente solicita **reportes estadísticos** específicos

❌ **NO lo implementes si**:
- El módulo es simple (solo CRUD básico)
- No hay necesidad de análisis o métricas
- Los datos son demasiado simples (ej: una tabla de países)

### Ejemplos de módulos que NECESITAN estadísticas:
- ✅ **Presupuestos**: Estados, tasas de conversión, alertas de caducidad
- ✅ **Ventas**: Totales, ingresos, productos más vendidos
- ✅ **Mantenimientos**: Pendientes, completados, tiempos promedio
- ✅ **Garantías**: Activas, vencidas, próximas a vencer

### Ejemplos de módulos que NO necesitan estadísticas:
- ❌ **Países**: Solo listado y CRUD básico
- ❌ **Unidades de medida**: Catálogo simple
- ❌ **Categorías**: Clasificación básica

---

## 📊 Resumen de Convenciones de Nombres

| Método | Convención | Ejemplo | Descripción |
|--------|-----------|---------|-------------|
| **Listar todos** | `get_[entidades]()` | `get_presupuestos()` | Todos los registros |
| **Listar activos** | `get_[entidades]_disponibles()` | `get_presupuestos_disponibles()` | Solo activos |
| **Obtener por ID** | `get_[entidad]xid($id)` | `get_presupuestoxid($id)` | Un registro por ID |
| **Insertar** | `insert_[entidad](...)` | `insert_presupuesto(...)` | Nuevo registro |
| **Actualizar** | `update_[entidad]($id, ...)` | `update_presupuesto($id, ...)` | Modificar registro |
| **Desactivar** | `delete_[entidad]xid($id)` | `delete_presupuestoxid($id)` | Desactivar (no eliminar) |
| **Activar** | `activar_[entidad]xid($id)` | `activar_presupuestoxid($id)` | Reactivar registro |
| **Verificar** | `verificar[Entidad]($campo, $id)` | `verificarPresupuesto($num, $id)` | Validar unicidad |
| **Estadísticas** | `obtenerEstadisticas()` | `obtenerEstadisticas()` | Métricas y análisis |

---

## 🔐 Buenas Prácticas en Modelos

### 1. Seguridad

```php
// ✅ BIEN: Prepared statements
$sql = "SELECT * FROM presupuesto WHERE id_presupuesto = ?";
$stmt = $this->conexion->prepare($sql);
$stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);

// ❌ MAL: Concatenación directa (SQL Injection)
$sql = "SELECT * FROM presupuesto WHERE id_presupuesto = $id_presupuesto";
```

### 2. Manejo de Errores

```php
// ✅ BIEN: Try-catch con registro de errores
try {
    // Código
} catch (PDOException $e) {
    $this->registro->registrarActividad(
        'admin',
        'Presupuesto',
        'metodo',
        "Error: " . $e->getMessage(),
        'error'
    );
    return false;
}
```

### 3. Validación de Campos Opcionales

```php
// ✅ BIEN: Validar si el campo tiene valor
if (!empty($id_contacto_cliente)) {
    $stmt->bindValue(3, $id_contacto_cliente, PDO::PARAM_INT);
} else {
    $stmt->bindValue(3, null, PDO::PARAM_NULL);
}

// ❌ MAL: Insertar valor vacío sin validar
$stmt->bindValue(3, $id_contacto_cliente, PDO::PARAM_INT);
```

### 4. Tipos de Datos en bindValue()

```php
// Tipos de datos PDO
PDO::PARAM_INT    // Enteros
PDO::PARAM_STR    // Cadenas de texto
PDO::PARAM_BOOL   // Booleanos
PDO::PARAM_NULL   // NULL
```

### 5. Retorno Consistente

```php
// ✅ BIEN: Retornos consistentes
public function insert_presupuesto(...) {
    return $this->conexion->lastInsertId(); // ID del registro insertado
}

public function update_presupuesto(...) {
    return $stmt->rowCount(); // Número de filas afectadas
}

public function delete_presupuestoxid($id) {
    return $stmt->rowCount() > 0; // true/false
}
```

---

## 📝 Plantilla de Modelo Estándar

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class NombreEntidad
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
                'NombreEntidad',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // Listar todos
    public function get_entidades()
    {
        try {
            $sql = "SELECT * FROM vista_entidad_completa ORDER BY campo ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidades',
                "Error: " . $e->getMessage(),
                "error"
            );
        }
    }

    // Listar activos
    public function get_entidades_disponibles()
    {
        try {
            $sql = "SELECT * FROM vista_entidad_completa 
                    WHERE activo_entidad = 1 ORDER BY campo ASC";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidades_disponibles',
                "Error: " . $e->getMessage(),
                "error"
            );
        }
    }

    // Obtener por ID
    public function get_entidadxid($id_entidad)
    {
        try {
            $sql = "SELECT * FROM vista_entidad_completa WHERE id_entidad = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'get_entidadxid',
                "Error: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    // Insertar
    public function insert_entidad($param1, $param2, ...)
    {
        try {
            $sql = "INSERT INTO entidad (campo1, campo2, ..., activo_entidad) 
                    VALUES (?, ?, ..., 1)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $param1, PDO::PARAM_STR);
            $stmt->bindValue(2, $param2, PDO::PARAM_INT);
            // ... más bindings
            $stmt->execute();
            $idInsert = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'Insertar',
                "Se insertó entidad ID: $idInsert",
                'info'
            );
            
            return $idInsert;
        } catch (PDOException $e) {
            throw new Exception("Error SQL: " . $e->getMessage());
        }
    }

    // Actualizar
    public function update_entidad($id_entidad, $param1, $param2, ...)
    {
        try {
            $sql = "UPDATE entidad SET campo1 = ?, campo2 = ?, ... 
                    WHERE id_entidad = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $param1, PDO::PARAM_STR);
            $stmt->bindValue(2, $param2, PDO::PARAM_INT);
            // ... más bindings
            $stmt->bindValue(N, $id_entidad, PDO::PARAM_INT); // ID al final
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'Actualizar',
                "Se actualizó entidad ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount();
        } catch (PDOException $e) {
            throw new Exception("Error SQL: " . $e->getMessage());
        }
    }

    // Desactivar
    public function delete_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE entidad SET activo_entidad = 0 WHERE id_entidad = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'Desactivar',
                "Se desactivó entidad ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'delete_entidadxid',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // Activar
    public function activar_entidadxid($id_entidad)
    {
        try {
            $sql = "UPDATE entidad SET activo_entidad = 1 WHERE id_entidad = ?";
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_entidad, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'Activar',
                "Se activó entidad ID: $id_entidad",
                'info'
            );
            
            return $stmt->rowCount() > 0;
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'NombreEntidad',
                'activar_entidadxid',
                "Error: " . $e->getMessage(),
                "error"
            );
            return false;
        }
    }

    // Verificar existencia
    public function verificarEntidad($campo_unico, $id_entidad = null)
    {
        try {
            $sql = "SELECT COUNT(*) AS total FROM entidad 
                    WHERE LOWER(campo_unico) = LOWER(?)";
            $params = [trim($campo_unico)];
    
            if (!empty($id_entidad)) {
                $sql .= " AND id_entidad != ?";
                $params[] = $id_entidad;
            }
    
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            $resultado = $stmt->fetch(PDO::FETCH_ASSOC);
    
            return [
                'existe' => ($resultado['total'] > 0)
            ];
        } catch (PDOException $e) {
            return [
                'existe' => false,
                'error' => $e->getMessage()
            ];
        }
    }
}
?>
```

---

## 📚 Resumen Ejecutivo

### ¿Qué es un Modelo?
Clase PHP que gestiona el acceso a datos de una entidad (tabla) mediante PDO.

### ¿Cuándo usar Vista SQL vs Tabla directamente?
- **Vista**: Cuando hay múltiples relaciones y campos calculados frecuentes
- **Tabla**: Para INSERT/UPDATE/DELETE y tablas simples

### Métodos Estándar (todos los modelos)
1. `__construct()` - Inicialización
2. `get_entidades()` - Listar todos
3. `get_entidades_disponibles()` - Listar activos
4. `get_entidadxid($id)` - Obtener por ID
5. `insert_entidad(...)` - Insertar
6. `update_entidad($id, ...)` - Actualizar
7. `delete_entidadxid($id)` - Desactivar
8. `activar_entidadxid($id)` - Reactivar
9. `verificarEntidad($campo, $id)` - Validar unicidad

### Métodos NO Estándar (según necesidad)
- `obtenerEstadisticas()` - Solo cuando se necesitan dashboards/métricas

### Buenas Prácticas Clave
✅ Prepared statements siempre  
✅ Try-catch en todos los métodos  
✅ Registro de actividad (logs)  
✅ Validación de campos opcionales (NULL)  
✅ Retornos consistentes  
✅ No eliminación física (soft delete)  

---

**Última actualización**: 14 de diciembre de 2025  
**Versión del documento**: 1.0  
**Autor**: InnovaByte
