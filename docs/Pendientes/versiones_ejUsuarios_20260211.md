# 📋 Sistema de Versiones de Presupuestos - Plan de Implementación

**Fecha de análisis**: 16 de febrero de 2026  
**Estado actual**: Base de datos 100% implementada, Backend/Frontend 10%  
**Rama**: cliente0_presupuesto  
**Documentación base**: [versionesPresupuesto_corregido.md](versionesPresupuesto_corregido.md)

---

## 📊 RESUMEN EJECUTIVO

El sistema de versiones de presupuestos tiene la **infraestructura de base de datos completamente funcional** (tablas, triggers, vistas), pero carece de la **capa de aplicación PHP** y la **interfaz de usuario** necesarias para operarlo en producción.

**Progreso actual:**
```
Base de Datos:     ██████████ 100% ✅ PRODUCCIÓN LISTA
Modelos PHP:       █░░░░░░░░░  10% ⚠️ CRÍTICO
Controllers:       █░░░░░░░░░  10% ⚠️ CRÍTICO
Vista/JavaScript:  █░░░░░░░░░  10% ⚠️ CRÍTICO
─────────────────────────────────────────
Estado Global:     ███░░░░░░░  30% ❌ INCOMPLETO
```

---

## 🔍 ANÁLISIS DEL ESTADO ACTUAL

### ✅ 1. BASE DE DATOS (100% IMPLEMENTADO)

#### 1.1. Tabla `presupuesto_version` ✅

**Ubicación**: [BD/toldos_db(2).sql](BD/toldos_db(2).sql#L2188-L2237)

**Estructura completa (17 campos):**

| Campo | Tipo | Descripción | Estado |
|-------|------|-------------|---------|
| `id_version_presupuesto` | INT UNSIGNED AUTO_INCREMENT | ID único de la versión (PK) | ✅ |
| `id_presupuesto` | INT UNSIGNED NOT NULL | FK al presupuesto padre | ✅ |
| `numero_version_presupuesto` | INT UNSIGNED NOT NULL | Número lógico de versión (1,2,3...) | ✅ |
| `version_padre_presupuesto` | INT UNSIGNED NULL | ID de la versión desde la que se creó (genealogía) | ✅ |
| `estado_version_presupuesto` | ENUM | borrador/enviado/aprobado/rechazado/cancelado | ✅ |
| `motivo_modificacion_version` | TEXT | Razón de creación de esta versión | ✅ |
| `fecha_creacion_version` | TIMESTAMP | Fecha de creación automática | ✅ |
| `creado_por_version` | INT UNSIGNED | ID del usuario creador | ✅ |
| `fecha_envio_version` | DATETIME NULL | Fecha de envío al cliente | ✅ |
| `enviado_por_version` | INT UNSIGNED NULL | ID del usuario que envió | ✅ |
| `fecha_aprobacion_version` | DATETIME NULL | Fecha de aprobación | ✅ |
| `fecha_rechazo_version` | DATETIME NULL | Fecha de rechazo | ✅ |
| `motivo_rechazo_version` | TEXT NULL | Razón del rechazo | ✅ |
| `ruta_pdf_version` | VARCHAR(255) NULL | Ruta del PDF generado | ✅ |
| `activo_version` | TINYINT(1) DEFAULT 1 | Soft delete | ✅ |
| `created_at_version` | TIMESTAMP | Timestamp de creación | ✅ |
| `updated_at_version` | TIMESTAMP | Timestamp de actualización | ✅ |

**Índices implementados:**
```sql
PRIMARY KEY (`id_version_presupuesto`)
KEY `fk_version_presupuesto` (`id_presupuesto`)
KEY `idx_numero_version_presupuesto` (`numero_version_presupuesto`)
KEY `idx_estado_version_presupuesto` (`estado_version_presupuesto`)
KEY `idx_activo_version` (`activo_version`)
```

**Foreign Keys:**
```sql
CONSTRAINT `fk_version_presupuesto` 
  FOREIGN KEY (`id_presupuesto`) 
  REFERENCES `presupuesto` (`id_presupuesto`) 
  ON DELETE RESTRICT ON UPDATE CASCADE
```

---

#### 1.2. Tabla `presupuesto` - Campos de Versiones ✅

**Campos añadidos:**
- `version_actual_presupuesto` INT UNSIGNED DEFAULT 1 ✅
- `estado_general_presupuesto` ENUM('borrador','enviado','aprobado','rechazado','cancelado') DEFAULT 'borrador' ✅

**Índices específicos:**
```sql
KEY `idx_version_actual_presupuesto` (`version_actual_presupuesto`)
KEY `idx_estado_general_presupuesto` (`estado_general_presupuesto`)
```

---

#### 1.3. Tabla `linea_presupuesto` ✅

**Campo FK a versión:**
```sql
`id_version_presupuesto` INT UNSIGNED NOT NULL 
COMMENT 'FK: Versión del presupuesto a la que pertenece esta línea'
```

**Foreign Key:**
```sql
CONSTRAINT `fk_linea_version` 
  FOREIGN KEY (`id_version_presupuesto`) 
  REFERENCES `presupuesto_version` (`id_version_presupuesto`) 
  ON DELETE RESTRICT ON UPDATE CASCADE
```

**✅ Estado**: Las líneas YA apuntan a `id_version_presupuesto` (no a `id_presupuesto`).

---

#### 1.4. Triggers Implementados (8 triggers) ✅

##### **Triggers en `presupuesto` (1 trigger)**

**1. `trg_presupuesto_after_insert`** [Línea 2027]
- **Función**: Crea automáticamente versión 1 al crear presupuesto
- **Comportamiento**: 
  - Inserta en `presupuesto_version` con `numero_version = 1`
  - Estado inicial: `'borrador'`
  - `version_padre = NULL`
  - Motivo: `'Versión inicial'`

```sql
CREATE TRIGGER `trg_presupuesto_after_insert` AFTER INSERT ON `presupuesto`
FOR EACH ROW BEGIN
    INSERT INTO presupuesto_version (
        id_presupuesto, numero_version_presupuesto, version_padre_presupuesto,
        estado_version_presupuesto, creado_por_version, motivo_modificacion_version
    ) VALUES (
        NEW.id_presupuesto, 1, NULL, 'borrador', 1, 'Versión inicial'
    );
END
```

---

##### **Triggers en `presupuesto_version` (6 triggers)**

**2. `trg_presupuesto_version_before_delete`** [Línea 2280]
- **Función**: Previene eliminación de versiones
- **Validaciones**:
  - No eliminar si tiene líneas asociadas
  - No eliminar si tiene versiones hijas (rompe genealogía)
  - No eliminar si NO está en estado `'borrador'` (inmutabilidad)

**3. `trg_presupuesto_version_before_insert_numero`** [Línea 2310]
- **Función**: Auto-calcula `numero_version_presupuesto` secuencial
- **Lógica**: `MAX(numero_version) + 1` por `id_presupuesto`

**4. `trg_presupuesto_version_before_insert_validar`** [Línea 2329]
- **Función**: Valida reglas de negocio al crear versión
- **Validaciones**:
  - Solo permitir 1 versión en estado `'borrador'` por presupuesto
  - Nueva versión debe referenciar la actual como `version_padre`

**5. `trg_version_auto_fechas`** [Línea 2358]
- **Función**: Establece fechas automáticamente según transiciones de estado
- **Comportamiento**:
  - `estado → 'enviado'` → `fecha_envio_version = NOW()`
  - `estado → 'aprobado'` → `fecha_aprobacion_version = NOW()`
  - `estado → 'rechazado'` → `fecha_rechazo_version = NOW()`

**6. `trg_version_auto_ruta_pdf`** [Línea 2404]
- **Función**: Genera automáticamente `ruta_pdf_version` al enviar
- **Formato**: `/documentos/presupuestos/{numero_presupuesto}_v{numero_version}.pdf`
- **Ejemplo**: `/documentos/presupuestos/P-00005-2026_v2.pdf`

**7. `trg_version_sync_estado_cabecera`** [Línea 2442]
- **Función**: Sincroniza estado versión actual con `presupuesto.estado_general_presupuesto`
- **Lógica**: Si la versión modificada es la actual → actualizar cabecera

---

##### **Triggers en `linea_presupuesto` (2 triggers)**

**8. `trg_linea_presupuesto_before_update`** [Línea 1715]
- **Función**: BLOQUEA modificaciones si versión NO es `'borrador'`
- **Error**: `SQLSTATE '45000'` - No modificar líneas de versiones cerradas

**9. `trg_linea_presupuesto_before_delete`** [Línea 1698]
- **Función**: BLOQUEA eliminaciones si versión NO es `'borrador'`
- **Error**: `SQLSTATE '45000'` - No eliminar líneas de versiones cerradas

---

#### 1.5. Vista SQL `vista_presupuesto_completa` ✅

**Incluye JOIN con versión actual:**
```sql
LEFT JOIN presupuesto_version pv 
  ON p.id_presupuesto = pv.id_presupuesto
  AND pv.numero_version_presupuesto = p.version_actual_presupuesto
```

**Campos de versión expuestos:**
- `id_version_actual` (ID de registro de versión)
- `numero_version_actual` (número lógico 1,2,3...)
- `estado_version_actual` (borrador/enviado/...)
- `fecha_creacion_version_actual`

---

### ❌ 2. CAPA DE APLICACIÓN PHP (10% IMPLEMENTADO)

#### 2.1. Modelo `Presupuesto.php` ⚠️

**EXISTENTE:**
- ✅ `get_info_version($id_version_presupuesto)` [Líneas 221-290]
  - Obtiene información completa de una versión específica
  - Incluye datos de presupuesto, cliente, estado
  - Usado en pantalla de líneas de presupuesto

**FALTANTES (7 métodos críticos):**
- ❌ `crear_nueva_version($id_presupuesto, $motivo, $id_usuario)`
  - Crear versión vacía desde presupuesto
  - Debe llamar a `duplicar_lineas()` automáticamente
  
- ❌ `duplicar_lineas($id_version_origen, $id_version_destino)`
  - Copiar todas las líneas de una versión a otra
  - Mantener estructura completa (líneas padre/hijas de KITs)
  
- ❌ `get_versiones_presupuesto($id_presupuesto)`
  - Listar todas las versiones con metadatos
  - Para modal de historial
  
- ❌ `get_version_actual($id_presupuesto)`
  - Obtener versión activa actualmente
  - Para verificaciones de estado
  
- ❌ `cambiar_version_activa($id_presupuesto, $numero_version)`
  - Cambiar a otra versión existente
  - Solo si está en borrador
  
- ❌ `cambiar_estado_version($id_version, $nuevo_estado, $datos_extra)`
  - Workflow de transiciones de estado
  - Validar transiciones permitidas
  
- ❌ `comparar_versiones($id_version_a, $id_version_b)`
  - Generar diff de líneas entre versiones
  - Retornar añadidos/eliminados/modificados

---

#### 2.2. Modelo `PresupuestoVersion.php` ❌

**NO EXISTE** un modelo separado para gestión de versiones.

**DEBE CREARSE** con métodos específicos:
- Gestión CRUD de versiones
- Validaciones de reglas de negocio
- Métodos de comparación y estadísticas

---

#### 2.3. Controller `presupuesto.php` ⚠️

**EXISTENTE:**
- ✅ `case "get_info_version"` [Líneas 658-695]
  - Endpoint que llama a `$presupuesto->get_info_version()`
  - Retorna JSON con datos de versión específica

**FALTANTES (8 endpoints):**
- ❌ `case "crear_version"` - Crear nueva versión con duplicación de líneas
- ❌ `case "listar_versiones"` - Obtener historial completo para modal
- ❌ `case "activar_version"` - Cambiar versión activa
- ❌ `case "cambiar_estado_version"` - Transiciones de workflow
- ❌ `case "aprobar_version"` - Shortcut para aprobar
- ❌ `case "rechazar_version"` - Shortcut para rechazar con motivo
- ❌ `case "comparar_versiones"` - Diff entre dos versiones
- ❌ `case "generar_pdf_version"` - PDF de versión específica

---

### ❌ 3. INTERFAZ DE USUARIO (10% IMPLEMENTADO)

#### 3.1. Funcionalidad Existente ⚠️

**En `mntpresupuesto.js`:**
- ✅ Renderiza datos de versión en listado (líneas 220-227)
  - `data-id_version_presupuesto` y `data-numero_version`
- ✅ Navegación a líneas con parámetro versión (líneas 598-606)
  - `window.location.href = '../lineasPresupuesto/index.php?id_version_presupuesto=' + id`

**En `lineasPresupuesto.js`:**
- ✅ Carga info de versión desde URL (líneas 20-23)
- ✅ Función `cargarInfoVersion()` (líneas 82-109)
  - Obtiene datos de versión vía AJAX
  - Almacena estado para validaciones
- ✅ Alerta de versión bloqueada (líneas 203-212)
  - Muestra SweetAlert2 cuando versión NO es borrador

---

#### 3.2. Componentes Faltantes ❌

**NO EXISTE interfaz para:**

**A. Gestión de Versiones:**
- ❌ Modal "Historial de Versiones"
  - Tabla DataTables con todas las versiones
  - Columnas: Número, Estado, Fecha, Usuario, Acciones
  - Botón "Nueva Versión" con campo motivo
  
- ❌ Botón "Nueva versión" en detalle de presupuesto
  - Visible solo si versión está en borrador
  - Modal para ingresar motivo de creación
  
- ❌ Selector desplegable de versiones en cabecera
  - Cambiar entre versiones existentes
  - Solo borradores editables

**B. Workflow de Estados:**
- ❌ Botón "Enviar al cliente" (borrador → enviado)
  - Confirmación SweetAlert2
  - Genera PDF automáticamente
  
- ❌ Botón "Aprobar" (enviado → aprobado)
  - Solo si versión fue enviada
  - Bloqueo permanente de edición
  
- ❌ Botón "Rechazar" (enviado → rechazado)
  - Modal con textarea obligatorio para motivo
  - Opción de crear nueva versión inmediatamente

**C. Visualización:**
- ❌ Indicador visual de número de versión en listado
  - Badge "v{número}" con color según estado
  - Tooltip con estado completo
  
- ❌ Badge de estado de versión
  - Verde = borrador
  - Azul = enviado
  - Verde oscuro = aprobado
  - Rojo = rechazado
  - Gris = cancelado

**D. Comparación:**
- ❌ Comparador visual de versiones
  - Modal con 2 selects (Versión A vs Versión B)
  - Tabla de diferencias con 3 secciones:
    - Líneas añadidas (fondo verde)
    - Líneas eliminadas (fondo rojo)
    - Líneas modificadas (fondo amarillo)
  - Resumen de totales con diferencia absoluta y %

**E. Información Contextual:**
- ❌ Banner de advertencia en edición
  - "Esta es versión {número} en estado {estado}"
  - "No se pueden realizar cambios. [Crear nueva versión]"
  
- ❌ Timeline de histórico de cambios
  - Visualización cronológica de versiones
  - Estados, fechas, usuarios

---

### 🔒 4. FLUJO ACTUAL DE CREACIÓN Y EDICIÓN

#### 4.1. Creación de Presupuesto ✅

**Flujo automático implementado:**

1. **PHP**: `Presupuesto->insert_presupuesto()` inserta en tabla `presupuesto`
2. **Trigger**: `trg_presupuesto_after_insert` crea versión 1 automáticamente
3. **Resultado**: Presupuesto tiene `version_actual = 1` y versión en estado `'borrador'`

**Estado**: ✅ **FUNCIONA CORRECTAMENTE**

---

#### 4.2. Inserción de Líneas ✅

**Flujo actual:**

1. Frontend envía `id_version_presupuesto` en formulario
2. `LineaPresupuesto->insert_linea()` inserta con FK a versión
3. Sin validación PHP de estado (delega a triggers)

**Estado**: ✅ **FUNCIONA** pero sin validación en capa aplicación

---

#### 4.3. Edición/Eliminación de Líneas ✅

**Protección implementada:**

- ✅ Trigger `trg_linea_presupuesto_before_update` bloquea UPDATE si NO es borrador
- ✅ Trigger `trg_linea_presupuesto_before_delete` bloquea DELETE si NO es borrador
- ⚠️ **FALTA**: Trigger para bloquear INSERT en versiones cerradas

**Estado**: ⚠️ **PARCIALMENTE PROTEGIDO**

---

#### 4.4. Generación de PDF ✅

**Información de versión incluida:**

```php
// En header del PDF (línea 147)
'N°: P-00002/2026 | F: 21/01/2026 | Val: 20/02/2026 | Ver: 1'
```

**Uso de versión en cálculos:**
- ✅ Sistema de peso usa `id_version_presupuesto` para totales
- ✅ Ruta PDF incluye número de versión (trigger automático)

**Estado**: ✅ **FUNCIONA CORRECTAMENTE**

---

## 📋 PLAN DE IMPLEMENTACIÓN DETALLADO

### **Fase 1: Modelo y Backend (Backend Foundation)**

---

#### **TASK 1.1: Crear modelo `models/PresupuestoVersion.php`**

**Objetivo**: Modelo dedicado para operaciones específicas de versiones.

**Métodos a implementar:**

```php
<?php

require_once '../config/conexion.php';
require_once '../config/funciones.php';

class PresupuestoVersion
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
                'PresupuestoVersion',
                '__construct',
                "Error zona horaria: " . $e->getMessage(),
                'warning'
            );
        }
    }

    // ============================================
    // MÉTODOS DE LECTURA
    // ============================================
    
    /**
     * Obtener todas las versiones de un presupuesto
     * @param int $id_presupuesto
     * @return array Lista de versiones con metadatos
     */
    public function get_versiones($id_presupuesto)
    {
        try {
            $sql = "SELECT 
                        pv.id_version_presupuesto,
                        pv.numero_version_presupuesto,
                        pv.estado_version_presupuesto,
                        pv.motivo_modificacion_version,
                        pv.fecha_creacion_version,
                        pv.fecha_envio_version,
                        pv.fecha_aprobacion_version,
                        pv.fecha_rechazo_version,
                        pv.motivo_rechazo_version,
                        pv.ruta_pdf_version,
                        pv.creado_por_version,
                        pv.enviado_por_version,
                        (SELECT COUNT(*) FROM linea_presupuesto 
                         WHERE id_version_presupuesto = pv.id_version_presupuesto
                         AND activo_linea_ppto = 1) as total_lineas
                    FROM presupuesto_version pv
                    WHERE pv.id_presupuesto = ?
                    AND pv.activo_version = 1
                    ORDER BY pv.numero_version_presupuesto DESC";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'get_versiones',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [];
        }
    }

    /**
     * Obtener detalle completo de una versión
     * @param int $id_version
     * @return array|false
     */
    public function get_version_detalle($id_version)
    {
        try {
            $sql = "SELECT 
                        pv.*,
                        p.numero_presupuesto,
                        p.nombre_evento_presupuesto,
                        c.nombre_cliente,
                        c.email_cliente
                    FROM presupuesto_version pv
                    INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                    INNER JOIN cliente c ON p.id_cliente = c.id_cliente
                    WHERE pv.id_version_presupuesto = ?
                    AND pv.activo_version = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_version, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'get_version_detalle',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Obtener versión activa de un presupuesto
     * @param int $id_presupuesto
     * @return array|false
     */
    public function get_version_activa($id_presupuesto)
    {
        try {
            $sql = "SELECT pv.*
                    FROM presupuesto_version pv
                    INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
                    WHERE p.id_presupuesto = ?
                    AND pv.numero_version_presupuesto = p.version_actual_presupuesto
                    AND pv.activo_version = 1";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'get_version_activa',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // ============================================
    // MÉTODOS DE ESCRITURA
    // ============================================
    
    /**
     * Crear nueva versión vacía
     * @param int $id_presupuesto
     * @param string $motivo
     * @param int $id_usuario
     * @return int|false ID de nueva versión o false
     */
    public function crear_version($id_presupuesto, $motivo, $id_usuario)
    {
        try {
            // El trigger auto-calcula numero_version y valida reglas
            $sql = "INSERT INTO presupuesto_version (
                        id_presupuesto,
                        version_padre_presupuesto,
                        estado_version_presupuesto,
                        motivo_modificacion_version,
                        creado_por_version
                    ) VALUES (?, 
                        (SELECT id_version_presupuesto 
                         FROM presupuesto_version 
                         WHERE id_presupuesto = ? 
                         AND numero_version_presupuesto = 
                             (SELECT version_actual_presupuesto 
                              FROM presupuesto WHERE id_presupuesto = ?)),
                        'borrador',
                        ?,
                        ?)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(3, $id_presupuesto, PDO::PARAM_INT);
            $stmt->bindValue(4, $motivo, PDO::PARAM_STR);
            $stmt->bindValue(5, $id_usuario, PDO::PARAM_INT);
            $stmt->execute();
            
            $id_version_nueva = $this->conexion->lastInsertId();
            
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'crear_version',
                "Versión creada: ID=$id_version_nueva, Presupuesto=$id_presupuesto",
                'info'
            );
            
            return $id_version_nueva;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'crear_version',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    /**
     * Duplicar líneas de una versión a otra
     * @param int $id_version_origen
     * @param int $id_version_destino
     * @return int Cantidad de líneas duplicadas
     */
    public function duplicar_lineas($id_version_origen, $id_version_destino)
    {
        try {
            $sql = "INSERT INTO linea_presupuesto (
                        id_version_presupuesto,
                        id_articulo,
                        id_linea_padre,
                        numero_linea_ppto,
                        tipo_linea_ppto,
                        codigo_linea_ppto,
                        descripcion_linea_ppto,
                        cantidad_linea_ppto,
                        precio_unitario_linea_ppto,
                        descuento_linea_ppto,
                        subtotal_linea_ppto,
                        id_impuesto,
                        importe_iva_linea_ppto,
                        total_linea_ppto,
                        es_componente_kit,
                        fecha_inicio_linea_ppto,
                        fecha_fin_linea_ppto,
                        fecha_montaje_linea_ppto,
                        fecha_desmontaje_linea_ppto,
                        observaciones_linea_ppto,
                        peso_total_linea_kg
                    )
                    SELECT 
                        ? as id_version_presupuesto,
                        id_articulo,
                        id_linea_padre,
                        numero_linea_ppto,
                        tipo_linea_ppto,
                        codigo_linea_ppto,
                        descripcion_linea_ppto,
                        cantidad_linea_ppto,
                        precio_unitario_linea_ppto,
                        descuento_linea_ppto,
                        subtotal_linea_ppto,
                        id_impuesto,
                        importe_iva_linea_ppto,
                        total_linea_ppto,
                        es_componente_kit,
                        fecha_inicio_linea_ppto,
                        fecha_fin_linea_ppto,
                        fecha_montaje_linea_ppto,
                        fecha_desmontaje_linea_ppto,
                        observaciones_linea_ppto,
                        peso_total_linea_kg
                    FROM linea_presupuesto
                    WHERE id_version_presupuesto = ?
                    AND activo_linea_ppto = 1
                    ORDER BY numero_linea_ppto";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $id_version_destino, PDO::PARAM_INT);
            $stmt->bindValue(2, $id_version_origen, PDO::PARAM_INT);
            $stmt->execute();
            
            $lineas_duplicadas = $stmt->rowCount();
            
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'duplicar_lineas',
                "Duplicadas $lineas_duplicadas líneas: $id_version_origen → $id_version_destino",
                'info'
            );
            
            return $lineas_duplicadas;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'duplicar_lineas',
                "Error: " . $e->getMessage(),
                'error'
            );
            return 0;
        }
    }

    /**
     * Cambiar estado de una versión
     * @param int $id_version
     * @param string $nuevo_estado
     * @param array $datos_extra ['motivo_rechazo', 'enviado_por']
     * @return bool
     */
    public function cambiar_estado($id_version, $nuevo_estado, $datos_extra = [])
    {
        try {
            // Los triggers auto-asignan fechas según el nuevo estado
            $sql = "UPDATE presupuesto_version SET 
                        estado_version_presupuesto = ?,
                        enviado_por_version = ?,
                        motivo_rechazo_version = ?
                    WHERE id_version_presupuesto = ?";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindValue(1, $nuevo_estado, PDO::PARAM_STR);
            $stmt->bindValue(2, $datos_extra['enviado_por'] ?? null, PDO::PARAM_INT);
            $stmt->bindValue(3, $datos_extra['motivo_rechazo'] ?? null, PDO::PARAM_STR);
            $stmt->bindValue(4, $id_version, PDO::PARAM_INT);
            $stmt->execute();
            
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'cambiar_estado',
                "Versión $id_version → $nuevo_estado",
                'info'
            );
            
            return true;
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'cambiar_estado',
                "Error: " . $e->getMessage(),
                'error'
            );
            return false;
        }
    }

    // ============================================
    // MÉTODOS DE COMPARACIÓN
    // ============================================
    
    /**
     * Comparar dos versiones
     * @param int $id_version_a
     * @param int $id_version_b
     * @return array ['anadidas', 'eliminadas', 'modificadas', 'resumen']
     */
    public function comparar_versiones($id_version_a, $id_version_b)
    {
        try {
            // Líneas añadidas en B
            $sql_anadidas = "SELECT la.*, 'AÑADIDO' as accion
                            FROM linea_presupuesto la
                            LEFT JOIN linea_presupuesto lb 
                                ON lb.id_articulo = la.id_articulo 
                                AND lb.id_version_presupuesto = ?
                            WHERE la.id_version_presupuesto = ?
                            AND lb.id_linea_ppto IS NULL
                            AND la.activo_linea_ppto = 1";
            
            $stmt = $this->conexion->prepare($sql_anadidas);
            $stmt->execute([$id_version_a, $id_version_b]);
            $anadidas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Líneas eliminadas en B
            $sql_eliminadas = "SELECT la.*, 'ELIMINADO' as accion
                              FROM linea_presupuesto la
                              LEFT JOIN linea_presupuesto lb 
                                  ON lb.id_articulo = la.id_articulo 
                                  AND lb.id_version_presupuesto = ?
                              WHERE la.id_version_presupuesto = ?
                              AND lb.id_linea_ppto IS NULL
                              AND la.activo_linea_ppto = 1";
            
            $stmt = $this->conexion->prepare($sql_eliminadas);
            $stmt->execute([$id_version_b, $id_version_a]);
            $eliminadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Líneas modificadas
            $sql_modificadas = "SELECT lb.*, 'MODIFICADO' as accion,
                                       la.cantidad_linea_ppto as cantidad_antigua,
                                       la.precio_unitario_linea_ppto as precio_antiguo,
                                       la.descuento_linea_ppto as descuento_antiguo,
                                       la.total_linea_ppto as total_antiguo
                               FROM linea_presupuesto la
                               INNER JOIN linea_presupuesto lb 
                                   ON lb.id_articulo = la.id_articulo
                               WHERE la.id_version_presupuesto = ?
                               AND lb.id_version_presupuesto = ?
                               AND la.activo_linea_ppto = 1
                               AND lb.activo_linea_ppto = 1
                               AND (
                                   la.cantidad_linea_ppto != lb.cantidad_linea_ppto OR
                                   la.precio_unitario_linea_ppto != lb.precio_unitario_linea_ppto OR
                                   la.descuento_linea_ppto != lb.descuento_linea_ppto
                               )";
            
            $stmt = $this->conexion->prepare($sql_modificadas);
            $stmt->execute([$id_version_a, $id_version_b]);
            $modificadas = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return [
                'anadidas' => $anadidas,
                'eliminadas' => $eliminadas,
                'modificadas' => $modificadas,
                'resumen' => [
                    'total_anadidas' => count($anadidas),
                    'total_eliminadas' => count($eliminadas),
                    'total_modificadas' => count($modificadas)
                ]
            ];
            
        } catch (PDOException $e) {
            $this->registro->registrarActividad(
                'admin',
                'PresupuestoVersion',
                'comparar_versiones',
                "Error: " . $e->getMessage(),
                'error'
            );
            return [
                'anadidas' => [],
                'eliminadas' => [],
                'modificadas' => [],
                'resumen' => ['error' => $e->getMessage()]
            ];
        }
    }
}
?>
```

**Convenciones seguidas:**
- ✅ Constructor con PDO y RegistroActividad
- ✅ Zona horaria Europe/Madrid
- ✅ Prepared statements con bindValue
- ✅ Try-catch en todos los métodos
- ✅ Logging de operaciones críticas
- ✅ Retornos consistentes (ID, bool, array)

---

#### **TASK 1.2: Extender modelo `models/Presupuesto.php`**

**Objetivo**: Añadir métodos de orquestación para versiones.

**Métodos a añadir:**

```php
/**
 * Crear nueva versión y duplicar líneas automáticamente
 * @param int $id_presupuesto
 * @param string $motivo
 * @param int $id_usuario
 * @return array ['success' => bool, 'id_version' => int, 'numero_version' => int]
 */
public function crear_nueva_version($id_presupuesto, $motivo = null, $id_usuario = 1)
{
    try {
        $this->conexion->beginTransaction();
        
        // Obtener versión actual
        $sql_actual = "SELECT id_version_presupuesto, numero_version_presupuesto, estado_version_presupuesto
                       FROM presupuesto_version
                       WHERE id_presupuesto = ?
                       AND numero_version_presupuesto = 
                           (SELECT version_actual_presupuesto FROM presupuesto WHERE id_presupuesto = ?)";
        
        $stmt = $this->conexion->prepare($sql_actual);
        $stmt->execute([$id_presupuesto, $id_presupuesto]);
        $version_actual = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$version_actual) {
            throw new Exception("No se encontró versión actual del presupuesto");
        }
        
        // Validar que se puede crear nueva versión
        if ($version_actual['estado_version_presupuesto'] === 'aprobado') {
            throw new Exception("No se puede crear nueva versión de un presupuesto aprobado");
        }
        
        if ($version_actual['estado_version_presupuesto'] === 'cancelado') {
            throw new Exception("No se puede crear nueva versión de un presupuesto cancelado");
        }
        
        // Crear nueva versión (trigger auto-calcula número)
        require_once 'PresupuestoVersion.php';
        $modeloVersion = new PresupuestoVersion();
        
        $id_version_nueva = $modeloVersion->crear_version(
            $id_presupuesto,
            $motivo ?? 'Nueva versión solicitada',
            $id_usuario
        );
        
        if (!$id_version_nueva) {
            throw new Exception("Error al crear versión");
        }
        
        // Duplicar líneas de versión actual
        $lineas_duplicadas = $modeloVersion->duplicar_lineas(
            $version_actual['id_version_presupuesto'],
            $id_version_nueva
        );
        
        // Actualizar versión actual en cabecera
        $sql_update = "UPDATE presupuesto SET 
                          version_actual_presupuesto = 
                              (SELECT numero_version_presupuesto 
                               FROM presupuesto_version 
                               WHERE id_version_presupuesto = ?)
                       WHERE id_presupuesto = ?";
        
        $stmt = $this->conexion->prepare($sql_update);
        $stmt->execute([$id_version_nueva, $id_presupuesto]);
        
        $this->conexion->commit();
        
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'crear_nueva_version',
            "Presupuesto $id_presupuesto: creada versión $id_version_nueva con $lineas_duplicadas líneas",
            'info'
        );
        
        return [
            'success' => true,
            'id_version' => $id_version_nueva,
            'numero_version' => $version_actual['numero_version_presupuesto'] + 1,
            'lineas_duplicadas' => $lineas_duplicadas
        ];
        
    } catch (Exception $e) {
        $this->conexion->rollBack();
        
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'crear_nueva_version',
            "Error: " . $e->getMessage(),
            'error'
        );
        
        return [
            'success' => false,
            'error' => $e->getMessage()
        ];
    }
}

/**
 * Activar una versión específica (solo si es borrador)
 * @param int $id_presupuesto
 * @param int $numero_version
 * @return bool
 */
public function activar_version($id_presupuesto, $numero_version)
{
    try {
        // Verificar que la versión existe y está en borrador
        $sql_verificar = "SELECT id_version_presupuesto, estado_version_presupuesto
                         FROM presupuesto_version
                         WHERE id_presupuesto = ?
                         AND numero_version_presupuesto = ?";
        
        $stmt = $this->conexion->prepare($sql_verificar);
        $stmt->execute([$id_presupuesto, $numero_version]);
        $version = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$version) {
            throw new Exception("Versión no encontrada");
        }
        
        if ($version['estado_version_presupuesto'] !== 'borrador') {
            throw new Exception("Solo se pueden activar versiones en borrador");
        }
        
        // Actualizar versión actual
        $sql_update = "UPDATE presupuesto SET 
                          version_actual_presupuesto = ?
                       WHERE id_presupuesto = ?";
        
        $stmt = $this->conexion->prepare($sql_update);
        $stmt->execute([$numero_version, $id_presupuesto]);
        
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'activar_version',
            "Presupuesto $id_presupuesto: version $numero_version activada",
            'info'
        );
        
        return true;
        
    } catch (Exception $e) {
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'activar_version',
            "Error: " . $e->getMessage(),
            'error'
        );
        return false;
    }
}

/**
 * Obtener estadísticas de versiones de un presupuesto
 * @param int $id_presupuesto
 * @return array
 */
public function get_estadisticas_versiones($id_presupuesto)
{
    try {
        $sql = "SELECT 
                    COUNT(*) as total_versiones,
                    MAX(numero_version_presupuesto) as ultima_version,
                    SUM(CASE WHEN estado_version_presupuesto = 'borrador' THEN 1 ELSE 0 END) as borradores,
                    SUM(CASE WHEN estado_version_presupuesto = 'enviado' THEN 1 ELSE 0 END) as enviadas,
                    SUM(CASE WHEN estado_version_presupuesto = 'aprobado' THEN 1 ELSE 0 END) as aprobadas,
                    SUM(CASE WHEN estado_version_presupuesto = 'rechazado' THEN 1 ELSE 0 END) as rechazadas,
                    MAX(updated_at_version) as ultima_modificacion
                FROM presupuesto_version
                WHERE id_presupuesto = ?
                AND activo_version = 1";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->bindValue(1, $id_presupuesto, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetch(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Presupuesto',
            'get_estadisticas_versiones',
            "Error: " . $e->getMessage(),
            'error'
        );
        return [];
    }
}
```

---

#### **TASK 1.3: Añadir endpoints en `controller/presupuesto.php`**

**Objetivo**: Implementar API REST para gestión de versiones.

**Endpoints a añadir:**

```php
// ============================================
// CREAR NUEVA VERSIÓN
// ============================================
case "crear_version":
    $id_presupuesto = $_POST["id_presupuesto"];
    $motivo = htmlspecialchars(trim($_POST["motivo"] ?? ''), ENT_QUOTES, 'UTF-8');
    $id_usuario = $_SESSION['id_usuario'] ?? 1; // TODO: Obtener de sesión
    
    $resultado = $presupuesto->crear_nueva_version($id_presupuesto, $motivo, $id_usuario);
    
    header('Content-Type: application/json');
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    break;

// ============================================
// LISTAR VERSIONES DE UN PRESUPUESTO
// ============================================
case "listar_versiones":
    require_once "../models/PresupuestoVersion.php";
    $modeloVersion = new PresupuestoVersion();
    
    $id_presupuesto = $_POST["id_presupuesto"];
    $versiones = $modeloVersion->get_versiones($id_presupuesto);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $versiones
    ], JSON_UNESCAPED_UNICODE);
    break;

// ============================================
// ACTIVAR VERSIÓN
// ============================================
case "activar_version":
    $id_presupuesto = $_POST["id_presupuesto"];
    $numero_version = $_POST["numero_version"];
    
    $resultado = $presupuesto->activar_version($id_presupuesto, $numero_version);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $resultado,
        'message' => $resultado ? 'Versión activada correctamente' : 'Error al activar versión'
    ], JSON_UNESCAPED_UNICODE);
    break;

// ============================================
// CAMBIAR ESTADO DE VERSIÓN
// ============================================
case "cambiar_estado_version":
    require_once "../models/PresupuestoVersion.php";
    $modeloVersion = new PresupuestoVersion();
    
    $id_version = $_POST["id_version"];
    $nuevo_estado = $_POST["nuevo_estado"];
    $id_usuario = $_SESSION['id_usuario'] ?? 1;
    
    $datos_extra = [];
    
    if ($nuevo_estado === 'enviado') {
        $datos_extra['enviado_por'] = $id_usuario;
    }
    
    if ($nuevo_estado === 'rechazado') {
        $datos_extra['motivo_rechazo'] = htmlspecialchars(
            trim($_POST["motivo_rechazo"] ?? ''),
            ENT_QUOTES,
            'UTF-8'
        );
    }
    
    $resultado = $modeloVersion->cambiar_estado($id_version, $nuevo_estado, $datos_extra);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $resultado,
        'message' => $resultado ? "Estado cambiado a $nuevo_estado" : 'Error al cambiar estado'
    ], JSON_UNESCAPED_UNICODE);
    break;

// ============================================
// APROBAR VERSIÓN (shortcut)
// ============================================
case "aprobar_version":
    require_once "../models/PresupuestoVersion.php";
    $modeloVersion = new PresupuestoVersion();
    
    $id_version = $_POST["id_version"];
    $resultado = $modeloVersion->cambiar_estado($id_version, 'aprobado');
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $resultado,
        'message' => $resultado ? 'Versión aprobada correctamente' : 'Error al aprobar versión'
    ], JSON_UNESCAPED_UNICODE);
    break;

// ============================================
// RECHAZAR VERSIÓN (shortcut)
// ============================================
case "rechazar_version":
    require_once "../models/PresupuestoVersion.php";
    $modeloVersion = new PresupuestoVersion();
    
    $id_version = $_POST["id_version"];
    $motivo_rechazo = htmlspecialchars(
        trim($_POST["motivo_rechazo"]),
        ENT_QUOTES,
        'UTF-8'
    );
    
    $resultado = $modeloVersion->cambiar_estado(
        $id_version,
        'rechazado',
        ['motivo_rechazo' => $motivo_rechazo]
    );
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $resultado,
        'message' => $resultado ? 'Versión rechazada' : 'Error al rechazar versión'
    ], JSON_UNESCAPED_UNICODE);
    break;

// ============================================
// COMPARAR VERSIONES
// ============================================
case "comparar_versiones":
    require_once "../models/PresupuestoVersion.php";
    $modeloVersion = new PresupuestoVersion();
    
    $id_version_a = $_POST["id_version_a"];
    $id_version_b = $_POST["id_version_b"];
    
    $diferencias = $modeloVersion->comparar_versiones($id_version_a, $id_version_b);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $diferencias
    ], JSON_UNESCAPED_UNICODE);
    break;

// ============================================
// OBTENER ESTADÍSTICAS DE VERSIONES
// ============================================
case "estadisticas_versiones":
    $id_presupuesto = $_POST["id_presupuesto"];
    $estadisticas = $presupuesto->get_estadisticas_versiones($id_presupuesto);
    
    header('Content-Type: application/json');
    echo json_encode([
        'success' => true,
        'data' => $estadisticas
    ], JSON_UNESCAPED_UNICODE);
    break;
```

---

### **Fase 2: Interfaz de Usuario (Frontend)**

---

#### **TASK 2.1: Modal "Historial de Versiones" en `view/Presupuesto/mntpresupuesto.php`**

**HTML del modal:**

```html
<!-- Modal Historial de Versiones -->
<div class="modal fade" id="modalHistorialVersiones" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-history"></i> Historial de Versiones
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Info del presupuesto -->
                <div class="alert alert-info" id="infoPresupuesto">
                    <strong>Presupuesto:</strong> <span id="numeroPresupuesto"></span> |
                    <strong>Cliente:</strong> <span id="nombreCliente"></span> |
                    <strong>Evento:</strong> <span id="nombreEvento"></span>
                </div>
                
                <!-- Tabla de versiones -->
                <table id="tblVersiones" class="table table-striped table-bordered nowrap" style="width:100%">
                    <thead>
                        <tr>
                            <th>Ver.</th>
                            <th>Estado</th>
                            <th>Creación</th>
                            <th>Envío</th>
                            <th>Motivo</th>
                            <th>Líneas</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                <button type="button" class="btn btn-success" id="btnNuevaVersion">
                    <i class="fas fa-plus"></i> Nueva Versión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Nueva Versión -->
<div class="modal fade" id="modalNuevaVersion" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Nueva Versión</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="idPresupuestoNuevaVersion">
                <div class="mb-3">
                    <label for="motivoNuevaVersion" class="form-label">
                        Motivo de la nueva versión *
                    </label>
                    <textarea 
                        class="form-control" 
                        id="motivoNuevaVersion" 
                        rows="3"
                        placeholder="Ej: Cliente solicita 2 focos adicionales"
                        required
                    ></textarea>
                    <div class="form-text">
                        Explica brevemente por qué se crea esta versión
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnCrearVersion">
                    <i class="fas fa-save"></i> Crear Versión
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modal Comparar Versiones -->
<div class="modal fade" id="modalCompararVersiones" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-code-branch"></i> Comparar Versiones
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label>Versión A:</label>
                        <select class="form-select" id="selectVersionA"></select>
                    </div>
                    <div class="col-md-2 text-center pt-4">
                        <i class="fas fa-exchange-alt fa-2x"></i>
                    </div>
                    <div class="col-md-5">
                        <label>Versión B:</label>
                        <select class="form-select" id="selectVersionB"></select>
                    </div>
                </div>
                
                <button class="btn btn-primary mb-3" id="btnComparar">
                    <i class="fas fa-search"></i> Comparar
                </button>
                
                <div id="resultadoComparacion" style="display:none;">
                    <!-- Resumen -->
                    <div class="alert alert-info">
                        <strong>Cambios encontrados:</strong>
                        <span id="resumenCambios"></span>
                    </div>
                    
                    <!-- Líneas añadidas -->
                    <div id="seccionAnadidas" style="display:none;">
                        <h6 class="text-success">➕ Líneas Añadidas</h6>
                        <table class="table table-sm">
                            <tbody id="tbodyAnadidas"></tbody>
                        </table>
                    </div>
                    
                    <!-- Líneas eliminadas -->
                    <div id="seccionEliminadas" style="display:none;">
                        <h6 class="text-danger">➖ Líneas Eliminadas</h6>
                        <table class="table table-sm">
                            <tbody id="tbodyEliminadas"></tbody>
                        </table>
                    </div>
                    
                    <!-- Líneas modificadas -->
                    <div id="seccionModificadas" style="display:none;">
                        <h6 class="text-warning">✏️ Líneas Modificadas</h6>
                        <table class="table table-sm">
                            <tbody id="tbodyModificadas"></tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
            </div>
        </div>
    </div>
</div>
```

---

#### **TASK 2.2: JavaScript en `view/Presupuesto/mntpresupuesto.js`**

**Añadir funciones de gestión de versiones:**

```javascript
// ============================================
// VARIABLES GLOBALES
// ============================================
let tablaVersiones;
let idPresupuestoActual;
let versionesDelPresupuesto = [];

// ============================================
// ABRIR MODAL HISTORIAL
// ============================================
function abrirHistorialVersiones(id_presupuesto, numero_presupuesto, nombre_cliente, nombre_evento) {
    idPresupuestoActual = id_presupuesto;
    
    // Actualizar info del presupuesto
    $('#numeroPresupuesto').text(numero_presupuesto);
    $('#nombreCliente').text(nombre_cliente);
    $('#nombreEvento').text(nombre_evento || 'Sin especificar');
    
    // Cargar versiones
    cargarVersiones(id_presupuesto);
    
    // Mostrar modal
    $('#modalHistorialVersiones').modal('show');
}

// ============================================
// CARGAR VERSIONES
// ============================================
function cargarVersiones(id_presupuesto) {
    $.ajax({
        url: '../../controller/presupuesto.php?op=listar_versiones',
        type: 'POST',
        data: { id_presupuesto: id_presupuesto },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                versionesDelPresupuesto = response.data;
                renderizarTablaVersiones(response.data);
            } else {
                Swal.fire('Error', 'No se pudieron cargar las versiones', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
        }
    });
}

// ============================================
// RENDERIZAR TABLA VERSIONES
// ============================================
function renderizarTablaVersiones(versiones) {
    if (tablaVersiones) {
        tablaVersiones.destroy();
    }
    
    let tbody = '';
    
    versiones.forEach(function(version) {
        // Badge de estado con colores
        let badgeEstado = '';
        switch(version.estado_version_presupuesto) {
            case 'borrador':
                badgeEstado = '<span class="badge bg-success">Borrador</span>';
                break;
            case 'enviado':
                badgeEstado = '<span class="badge bg-primary">Enviado</span>';
                break;
            case 'aprobado':
                badgeEstado = '<span class="badge bg-dark">Aprobado</span>';
                break;
            case 'rechazado':
                badgeEstado = '<span class="badge bg-danger">Rechazado</span>';
                break;
            case 'cancelado':
                badgeEstado = '<span class="badge bg-secondary">Cancelado</span>';
                break;
        }
        
        // Fecha de envío
        let fechaEnvio = version.fecha_envio_version 
            ? new Date(version.fecha_envio_version).toLocaleDateString('es-ES')
            : '-';
        
        // Motivo
        let motivo = version.motivo_modificacion_version || '-';
        if (motivo.length > 50) {
            motivo = motivo.substring(0, 50) + '...';
        }
        
        // Botones de acciones
        let acciones = `
            <button class="btn btn-sm btn-info" onclick="verVersion(${version.id_version_presupuesto})" title="Ver líneas">
                <i class="fas fa-eye"></i>
            </button>
        `;
        
        if (version.estado_version_presupuesto === 'borrador') {
            acciones += `
                <button class="btn btn-sm btn-primary" onclick="enviarVersion(${version.id_version_presupuesto})" title="Enviar al cliente">
                    <i class="fas fa-paper-plane"></i>
                </button>
            `;
        }
        
        if (version.estado_version_presupuesto === 'enviado') {
            acciones += `
                <button class="btn btn-sm btn-success" onclick="aprobarVersion(${version.id_version_presupuesto})" title="Aprobar">
                    <i class="fas fa-check"></i>
                </button>
                <button class="btn btn-sm btn-danger" onclick="rechazarVersion(${version.id_version_presupuesto})" title="Rechazar">
                    <i class="fas fa-times"></i>
                </button>
            `;
        }
        
        if (version.ruta_pdf_version) {
            acciones += `
                <a href="${version.ruta_pdf_version}" target="_blank" class="btn btn-sm btn-secondary" title="Ver PDF">
                    <i class="fas fa-file-pdf"></i>
                </a>
            `;
        }
        
        tbody += `
            <tr>
                <td class="text-center"><strong>v${version.numero_version_presupuesto}</strong></td>
                <td>${badgeEstado}</td>
                <td>${new Date(version.fecha_creacion_version).toLocaleDateString('es-ES')}</td>
                <td>${fechaEnvio}</td>
                <td>${motivo}</td>
                <td class="text-center">${version.total_lineas}</td>
                <td class="text-nowrap">${acciones}</td>
            </tr>
        `;
    });
    
    $('#tblVersiones tbody').html(tbody);
    
    tablaVersiones = $('#tblVersiones').DataTable({
        language: {
            url: '../../public/lib/DataTables/es-ES.json'
        },
        order: [[0, 'desc']],
        pageLength: 10,
        responsive: true
    });
}

// ============================================
// CREAR NUEVA VERSIÓN
// ============================================
$('#btnNuevaVersion').on('click', function() {
    $('#idPresupuestoNuevaVersion').val(idPresupuestoActual);
    $('#motivoNuevaVersion').val('');
    $('#modalNuevaVersion').modal('show');
});

$('#btnCrearVersion').on('click', function() {
    let id_presupuesto = $('#idPresupuestoNuevaVersion').val();
    let motivo = $('#motivoNuevaVersion').val().trim();
    
    if (!motivo) {
        Swal.fire('Atención', 'Debe indicar el motivo de la nueva versión', 'warning');
        return;
    }
    
    Swal.fire({
        title: 'Creando versión...',
        text: 'Por favor espere',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });
    
    $.ajax({
        url: '../../controller/presupuesto.php?op=crear_version',
        type: 'POST',
        data: {
            id_presupuesto: id_presupuesto,
            motivo: motivo
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                Swal.fire({
                    icon: 'success',
                    title: '¡Versión creada!',
                    html: `Se ha creado la versión <strong>${response.numero_version}</strong> con <strong>${response.lineas_duplicadas}</strong> líneas`,
                    confirmButtonText: 'Ver nueva versión'
                }).then(() => {
                    $('#modalNuevaVersion').modal('hide');
                    cargarVersiones(id_presupuesto);
                    tabla.ajax.reload();
                });
            } else {
                Swal.fire('Error', response.error || 'No se pudo crear la versión', 'error');
            }
        },
        error: function() {
            Swal.fire('Error', 'Error de comunicación con el servidor', 'error');
        }
    });
});

// ============================================
// ENVIAR VERSIÓN AL CLIENTE
// ============================================
function enviarVersion(id_version) {
    Swal.fire({
        title: '¿Enviar al cliente?',
        text: 'Esta acción bloqueará la edición de la versión',
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, enviar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/presupuesto.php?op=cambiar_estado_version',
                type: 'POST',
                data: {
                    id_version: id_version,
                    nuevo_estado: 'enviado'
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Enviado', 'La versión ha sido enviada al cliente', 'success');
                        cargarVersiones(idPresupuestoActual);
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}

// ============================================
// APROBAR VERSIÓN
// ============================================
function aprobarVersion(id_version) {
    Swal.fire({
        title: '¿Aprobar versión?',
        text: 'Esta acción es definitiva y cerrará el presupuesto',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sí, aprobar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/presupuesto.php?op=aprobar_version',
                type: 'POST',
                data: { id_version: id_version },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire('Aprobado', 'La versión ha sido aprobada', 'success');
                        cargarVersiones(idPresupuestoActual);
                        tabla.ajax.reload();
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}

// ============================================
// RECHAZAR VERSIÓN
// ============================================
function rechazarVersion(id_version) {
    Swal.fire({
        title: 'Rechazar versión',
        text: 'Indique el motivo del rechazo:',
        input: 'textarea',
        inputAttributes: {
            rows: 3
        },
        showCancelButton: true,
        confirmButtonText: 'Rechazar',
        cancelButtonText: 'Cancelar',
        inputValidator: (value) => {
            if (!value) {
                return 'Debe indicar el motivo del rechazo';
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '../../controller/presupuesto.php?op=rechazar_version',
                type: 'POST',
                data: {
                    id_version: id_version,
                    motivo_rechazo: result.value
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        Swal.fire({
                            icon: 'info',
                            title: 'Versión rechazada',
                            text: '¿Desea crear una nueva versión?',
                            showCancelButton: true,
                            confirmButtonText: 'Sí, crear nueva',
                            cancelButtonText: 'No'
                        }).then((result2) => {
                            if (result2.isConfirmed) {
                                $('#motivoNuevaVersion').val('Modificaciones según rechazo: ' + result.value);
                                $('#btnNuevaVersion').click();
                            }
                            cargarVersiones(idPresupuestoActual);
                            tabla.ajax.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                }
            });
        }
    });
}

// ============================================
// VER LÍNEAS DE UNA VERSIÓN
// ============================================
function verVersion(id_version) {
    window.open('../lineasPresupuesto/index.php?id_version_presupuesto=' + id_version, '_blank');
}

// ============================================
// INTEGRACIÓN CON LISTADO PRINCIPAL
// ============================================
// Añadir botón de historial en cada fila del listado
function agregarBotonHistorial(row) {
    return `
        <button class="btn btn-sm btn-secondary" 
                onclick="abrirHistorialVersiones(
                    ${row.id_presupuesto}, 
                    '${row.numero_presupuesto}', 
                    '${row.nombre_cliente}', 
                    '${row.nombre_evento_presupuesto || ''}'
                )" 
                title="Ver historial de versiones">
            <i class="fas fa-history"></i>
        </button>
    `;
}

// Añadir badge de versión en número de presupuesto
function formatearNumeroConVersion(numero_presupuesto, numero_version, estado_version) {
    let colorBadge = 'secondary';
    switch(estado_version) {
        case 'borrador': colorBadge = 'success'; break;
        case 'enviado': colorBadge = 'primary'; break;
        case 'aprobado': colorBadge = 'dark'; break;
        case 'rechazado': colorBadge = 'danger'; break;
    }
    
    return `
        ${numero_presupuesto}
        <span class="badge bg-${colorBadge} ms-2" title="Versión ${numero_version} (${estado_version})">
            v${numero_version}
        </span>
    `;
}
```

---

### **Fase 3: Mejoras y Validaciones**

---

#### **TASK 3.1: Trigger de validación INSERT en líneas**

**Archivo**: `BD/migrations/20260216_add_trigger_linea_insert.sql`

```sql
-- ============================================
-- Trigger: trg_linea_presupuesto_before_insert
-- Descripción: Bloquea inserción de líneas en versiones cerradas
-- Fecha: 16 de febrero de 2026
-- ============================================

DELIMITER //

CREATE TRIGGER trg_linea_presupuesto_before_insert
BEFORE INSERT ON linea_presupuesto
FOR EACH ROW
BEGIN
    DECLARE estado_version VARCHAR(20);
    
    -- Obtener estado de la versión
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = NEW.id_version_presupuesto;
    
    -- Bloquear si NO es borrador
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden añadir líneas a versiones cerradas. Debe crear una nueva versión.';
    END IF;
END//

DELIMITER ;
```

---

#### **TASK 3.2: Validación en controller `lineapresupuesto.php`**

**Añadir al inicio del caso `"guardaryeditar"` (antes de INSERT):**

```php
// Validar que la versión esté en borrador
$sql_verificar = "SELECT estado_version_presupuesto 
                  FROM presupuesto_version 
                  WHERE id_version_presupuesto = ?";

$stmt_verificar = $conexion->prepare($sql_verificar);
$stmt_verificar->execute([$datos['id_version_presupuesto']]);
$version = $stmt_verificar->fetch(PDO::FETCH_ASSOC);

if (!$version) {
    echo json_encode([
        'success' => false,
        'message' => 'Versión de presupuesto no encontrada'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

if ($version['estado_version_presupuesto'] !== 'borrador') {
    echo json_encode([
        'success' => false,
        'message' => 'No se pueden añadir líneas a versiones cerradas. Debe crear una nueva versión desde el menú de versiones.',
        'sugerencia' => 'crear_nueva_version'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

// Continuar con INSERT normal...
```

---

#### **TASK 3.3: Indicadores visuales en `lineasPresupuesto.js`**

**Añadir banner de advertencia si versión NO es borrador:**

```javascript
function mostrarInfoVersion(data) {
    // Actualizar info de cabecera
    $('#numeroPresupuesto').text(data.numero_presupuesto);
    $('#nombreCliente').text(data.nombre_cliente);
    
    // Mostrar badge de versión
    let badgeColor, badgeTexto;
    switch(data.estado_version_presupuesto) {
        case 'borrador':
            badgeColor = 'success';
            badgeTexto = 'BORRADOR - Editable';
            break;
        case 'enviado':
            badgeColor = 'primary';
            badgeTexto = 'ENVIADO - Bloqueado';
            break;
        case 'aprobado':
            badgeColor = 'dark';
            badgeTexto = 'APROBADO - Cerrado';
            break;
        case 'rechazado':
            badgeColor = 'danger';
            badgeTexto = 'RECHAZADO - Bloqueado';
            break;
        case 'cancelado':
            badgeColor = 'secondary';
            badgeTexto = 'CANCELADO';
            break;
    }
    
    $('#badgeEstadoVersion').html(`
        <span class="badge bg-${badgeColor} fs-6 me-3">
            Versión ${data.numero_version_presupuesto}: ${badgeTexto}
        </span>
    `);
    
    // Si NO es borrador, mostrar banner de advertencia
    if (data.estado_version_presupuesto !== 'borrador') {
        $('#bannerBloqueado').html(`
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <i class="fas fa-lock me-2"></i>
                <strong>Versión bloqueada:</strong> 
                Esta es la versión ${data.numero_version_presupuesto} en estado <strong>${data.estado_version_presupuesto}</strong>. 
                No se pueden realizar cambios.
                <button type="button" class="btn btn-sm btn-primary ms-3" onclick="window.open('../Presupuesto/index.php', '_self')">
                    <i class="fas fa-plus"></i> Crear nueva versión
                </button>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `).show();
        
        // Deshabilitar todos los botones de edición
        $('.btn-editar-linea').prop('disabled', true).addClass('disabled');
        $('.btn-eliminar-linea').prop('disabled', true).addClass('disabled');
        $('#btnNuevaLinea').prop('disabled', true).addClass('disabled');
    } else {
        $('#bannerBloqueado').hide();
    }
}
```

---

## 🧪 PLAN DE PRUEBAS

### **Pruebas Unitarias (Base de Datos)**

```sql
-- PRUEBA 1: Creación automática de versión 1
INSERT INTO presupuesto (numero_presupuesto, id_cliente, ...) VALUES ('TEST-001', 1, ...);
SELECT * FROM presupuesto_version WHERE id_presupuesto = LAST_INSERT_ID();
-- Esperado: 1 versión con numero_version = 1, estado = 'borrador'

-- PRUEBA 2: Bloqueo de edición de líneas (UPDATE)
UPDATE presupuesto_version SET estado_version_presupuesto = 'enviado' WHERE id_version = 10;
UPDATE linea_presupuesto SET cantidad_linea_ppto = 5 WHERE id_version_presupuesto = 10;
-- Esperado: ERROR 45000 - No se pueden modificar líneas

-- PRUEBA 3: Bloqueo de eliminación de líneas
DELETE FROM linea_presupuesto WHERE id_version_presupuesto = 10;
-- Esperado: ERROR 45000 - No se pueden eliminar líneas

-- PRUEBA 4: Auto-numeración secuencial
INSERT INTO presupuesto_version (id_presupuesto, ...) VALUES (1, ...);
-- Esperado: numero_version auto-calculado = MAX + 1

-- PRUEBA 5: Sincronización de estados
UPDATE presupuesto_version SET estado_version_presupuesto = 'aprobado' WHERE id_version = 10;
SELECT estado_general_presupuesto FROM presupuesto WHERE id_presupuesto = 1;
-- Esperado: estado_general = 'aprobado'
```

---

### **Pruebas de Integración (PHP)**

```php
// PRUEBA 1: Crear nueva versión con duplicación
$resultado = $presupuesto->crear_nueva_version(1, 'Prueba de duplicación');
// Esperado: ['success' => true, 'id_version' => X, 'lineas_duplicadas' => N]

// PRUEBA 2: Listar versiones
$versiones = $modeloVersion->get_versiones(1);
// Esperado: Array con todas las versiones del presupuesto

// PRUEBA 3: Cambiar estado con datos extra
$resultado = $modeloVersion->cambiar_estado(10, 'rechazado', [
    'motivo_rechazo' => 'Precio muy elevado'
]);
// Esperado: true, y campo motivo_rechazo guardado

// PRUEBA 4: Comparar versiones
$diff = $modeloVersion->comparar_versiones(10, 11);
// Esperado: Array con 'anadidas', 'eliminadas', 'modificadas', 'resumen'
```

---

### **Pruebas de UI (Frontend)**

1. **Modal Historial:**
   - Abrir historial desde listado → verificar tabla DataTables con todas las versiones
   - Verificar badges de estado con colores correctos
   - Click en "Ver" → abre pantalla de líneas en nueva pestaña
   - Click en "PDF" → descarga o muestra PDF de esa versión

2. **Crear Nueva Versión:**
   - Click "Nueva Versión" → modal con textarea motivo
   - Sin motivo + click "Crear" → validación "Debe indicar motivo"
   - Con motivo + click "Crear" → SweetAlert loading → success con número de versión
   - Verificar recarga de tabla de versiones

3. **Workflow de Estados:**
   - Versión borrador + click "Enviar" → confirmación SweetAlert → badge cambia a "Enviado"
   - Versión enviada + click "Aprobar" → confirmación → badge cambia a "Aprobado"
   - Versión enviada + click "Rechazar" → textarea motivo obligatorio → success → opción crear nueva versión

4. **Comparador:**
   - Elegir 2 versiones en selects → click "Comparar"
   - Verificar secciones: añadidas (verde), eliminadas (rojo), modificadas (amarillo)
   - Verificar resumen de cambios: "3 añadidas, 1 eliminada, 2 modificadas"

5. **Bloqueo de Edición:**
   - Abrir versión NO borrador → banner de advertencia visible
   - Botones "Editar" y "Eliminar" deshabilitados
   - Intentar editar línea → error en backend + SweetAlert con mensaje claro

---

## 📐 DECISIONES TÉCNICAS

### **1. Modelo Separado vs Extender Presupuesto**

**Decisión:** Crear modelo separado `PresupuestoVersion.php`

**Razones:**
- `Presupuesto.php` actualmente tiene 1000+ líneas
- Separación de responsabilidades (SRP)
- Facilita testing unitario
- Permite reutilización en otros contextos
- Mantiene cohesión: `Presupuesto` = cabecera, `PresupuestoVersion` = versionado

---

### **2. Duplicación de Líneas en PHP vs SQL**

**Decisión:** Duplicación en modelo PHP (`duplicar_lineas()`)

**Razones:**
- ✅ Logging granular en `RegistroActividad`
- ✅ Manejo de errores en capa aplicación
- ✅ Facilita futuras extensiones (ej: duplicar adjuntos, observaciones)
- ✅ Permite auditoría: quién, cuándo, cuántas líneas
- ⚠️ Desventaja: ligeramente más lento que SQL puro (negligible < 100 líneas)

---

### **3. Triggers para Inmutabilidad**

**Decisión:** Mantener lógica de bloqueo en triggers (no solo PHP)

**Razones:**
- ✅ Última línea de defensa (seguridad en profundidad)
- ✅ Evita bypass por llamadas directas a BD
- ✅ Evita bypass por scripts externos o SQL manual
- ✅ Consistente con arquitectura actual del proyecto
- ✅ Garantiza integridad de datos sin importar origen de request

---

### **4. Modal vs Página Separada para Historial**

**Decisión:** Modal integrado en listado principal

**Razones:**
- ✅ Mantiene contexto visual del presupuesto
- ✅ Evita navegación extra (mejor UX)
- ✅ Permite acceso rápido sin cambiar de página
- ✅ Historial es consulta secundaria, no flujo principal
- ✅ Reduce carga de servidor (no renderiza página completa)

**Contras considerados:**
- ⚠️ Modal grande puede ser incómodo en pantallas pequeñas
- **Solución:** Modal `modal-xl` (Bootstrap 5) con scroll interno

---

### **5. Workflow Explícito con Botones**

**Decisión:** Implementar botones "Enviar/Aprobar/Rechazar" en lugar de cambio directo de estado

**Razones:**
- ✅ Mejora trazabilidad (se registra quién y cuándo)
- ✅ Previene cambios accidentales
- ✅ Permite validaciones específicas por transición
- ✅ Facilita reglas de negocio futuras (ej: notificaciones por email)
- ✅ UX más clara: botones con íconos intuitivos

**Estados y transiciones permitidas:**
```
borrador   → enviado   (botón "Enviar")
enviado    → aprobado  (botón "Aprobar")
enviado    → rechazado (botón "Rechazar" + motivo obligatorio)
rechazado  → borrador  (crear nueva versión)
```

---

### **6. Comparador de Versiones: Modal vs Vista Independiente**

**Decisión:** Modal dentro del historial

**Razones:**
- ✅ Mantiene flujo contextual
- ✅ Seleccionar versiones sin salir del historial
- ✅ Comparar y volver rápido
- ✅ Evita crear nueva ruta y componente

**Alternativa descartada:** Vista `/comparadorVersiones.php`
- ❌ Requiere pasar IDs por URL
- ❌ Pierde contexto de historial
- ❌ Más código para mantener

---

### **7. Validación Doble: PHP + Trigger**

**Decisión:** Validar estado de versión tanto en PHP como en trigger

**Razones:**
- ✅ **PHP**: Mensaje de error amigable al usuario
- ✅ **PHP**: Evita query innecesario a BD
- ✅ **Trigger**: Seguridad (último bastión)
- ✅ **Trigger**: Protege contra bypass

**Flujo completo:**
```
1. Frontend intenta editar línea
2. JavaScript verifica estado_version_actual (prevención)
3. PHP verifica estado antes de INSERT (validación)
4. Trigger verifica estado al ejecutar INSERT (seguridad)
```

---

## 📝 NOTAS ADICIONALES

### **Campos Futuros (Opcionales)**

**En tabla `presupuesto_version`:**
- `usuario_aprobacion_version` INT UNSIGNED - Quién aprobó
- `usuario_rechazo_version` INT UNSIGNED - Quién rechazó
- `etiquetas_version` JSON - Tags para organizar versiones
- `notas_internas_version` TEXT - Comentarios del equipo

**En tabla `presupuesto`:**
- `version_aprobada_presupuesto` INT UNSIGNED - Link a versión aprobada (si existe)
- `bloqueado_presupuesto` BOOLEAN - Bloqueo manual adicional

---

### **Mejoras de UX Futuras**

1. **Timeline Visual:**
   - Línea de tiempo vertical con todas las versiones
   - Íconos por estado (📝borrador, 📧enviado, ✅aprobado, ❌rechazado)
   - Links entre versiones padre-hija

2. **Notificaciones:**
   - Email al cliente cuando se envía nueva versión
   - Email al comercial cuando cliente aprueba/rechaza
   - Alertas en dashboard de versiones pendientes de revisar

3. **Comentarios por Versión:**
   - Sistema de chat interno por versión
   - Permite discusión del equipo sin salir de la app
   - Historial de conversaciones

4. **Exportación de Comparación:**
   - Generar PDF con diff de versiones
   - Útil para justificar cambios al cliente
   - Incluye resumen ejecutivo de modificaciones

---

### **Rendimiento y Optimización**

**Consultas pesadas identificadas:**
- `comparar_versiones()` con muchas líneas (100+)
- **Solución**: Añadir índice compuesto en `linea_presupuesto(id_version_presupuesto, id_articulo)`

**Caché potencial:**
- Vista `vista_presupuesto_completa` ya incluye datos de versión actual
- NO cachear versiones (datos críticos, deben ser tiempo real)

**Límites recomendados:**
- Máximo 20 versiones por presupuesto (después sugerir crear nuevo presupuesto)
- Máximo 500 líneas por versión (después advertir de rendimiento)

---

## 📊 MÉTRICAS DE IMPLEMENTACIÓN

### **Esfuerzo Estimado (Horas de desarrollo)**

| Fase | Tarea | Horas | Complejidad |
|------|-------|-------|-------------|
| 1.1 | Modelo PresupuestoVersion.php | 6h | Alta |
| 1.2 | Extender Presupuesto.php | 4h | Media |
| 1.3 | Endpoints en controller | 4h | Media |
| 2.1 | Modal historial HTML | 3h | Media |
| 2.2 | JavaScript versiones | 8h | Alta |
| 3.1 | Trigger INSERT validación | 1h | Baja |
| 3.2 | Validación PHP controller | 2h | Baja |
| 3.3 | Indicadores visuales | 3h | Media |
| **Testing** | Pruebas completas | 6h | Alta |
| **Documentación** | Actualizar docs | 2h | Baja |
| **TOTAL** | | **39h** | **~5 días** |

---

### **Archivos a Crear/Modificar**

**Nuevos (5 archivos):**
1. `models/PresupuestoVersion.php`
2. `BD/migrations/20260216_add_trigger_linea_insert.sql`
3. `view/Presupuesto/comparadorVersiones.js` (opcional)
4. `docs/versiones_manual_usuario.md` (opcional)
5. `tests/PresupuestoVersionTest.php` (opcional)

**Modificados (5 archivos):**
1. `models/Presupuesto.php` (+150 líneas)
2. `controller/presupuesto.php` (+200 líneas)
3. `controller/lineapresupuesto.php` (+30 líneas)
4. `view/Presupuesto/mntpresupuesto.php` (+250 líneas HTML)
5. `view/Presupuesto/mntpresupuesto.js` (+400 líneas)

**Total líneas de código a añadir:** ~2000 líneas

---

## 🎯 ROADMAP DE IMPLEMENTACIÓN

### **Sprint 1: Backend Foundation (Días 1-2)**
- ✅ Crear `PresupuestoVersion.php` completo
- ✅ Extender `Presupuesto.php` con métodos orquestación
- ✅ Implementar endpoints en `presupuesto.php`
- ✅ Testing unitario de modelos

### **Sprint 2: UI Core (Días 3-4)**
- ✅ HTML modal historial + nueva versión
- ✅ JavaScript básico (cargar, crear versión)
- ✅ Integración con listado principal
- ✅ Testing funcional básico

### **Sprint 3: Workflow (Día 5)**
- ✅ Botones enviar/aprobar/rechazar
- ✅ Validaciones y confirmaciones
- ✅ JavaScript workflow estados
- ✅ Testing de flujo completo

### **Sprint 4: Mejoras (Días 6-7)**
- ✅ Trigger INSERT validación
- ✅ Comparador de versiones (modal)
- ✅ Indicadores visuales mejorados
- ✅ Refinamiento UX
- ✅ Testing integral
- ✅ Documentación final

---

## 📚 REFERENCIAS

- **Documentación base**: [versionesPresupuesto_corregido.md](versionesPresupuesto_corregido.md)
- **Base de datos**: [BD/toldos_db(2).sql](BD/toldos_db(2).sql)
- **Triggers existentes**: Líneas 2027-2770
- **Vistas SQL**: Línea 4393+
- **Modelo Presupuesto**: [models/Presupuesto.php](models/Presupuesto.php)
- **Controller**: [controller/presupuesto.php](controller/presupuesto.php)
- **Vista principal**: [view/Presupuesto/mntpresupuesto.php](view/Presupuesto/mntpresupuesto.php)

---

## 🔗 ENLACES ÚTILES

- Bootstrap 5 Modals: https://getbootstrap.com/docs/5.0/components/modal/
- DataTables API: https://datatables.net/reference/api/
- SweetAlert2: https://sweetalert2.github.io/
- Font Awesome Icons: https://fontawesome.com/icons

---

**Documento creado**: 16 de febrero de 2026  
**Autor**: Sistema MDR - Análisis automático  
**Versión**: 1.0  
**Estado**: PENDIENTE DE IMPLEMENTACIÓN  
**Prioridad**: ALTA (después de actualización de otro proyecto)

---

*Este documento será el blueprint completo para la implementación del sistema de versiones. Mantener actualizado conforme se implementen las fases.*
