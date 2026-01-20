# 🧪 Guía de Pruebas - Módulo de Líneas de Presupuesto

## 📋 Requisitos Previos

Antes de probar el módulo, asegúrate de que:

1. ✅ La tabla `presupuesto_version` existe en la base de datos
2. ✅ La tabla `linea_presupuesto` existe y tiene la FK a `presupuesto_version`
3. ✅ Las vistas `v_linea_presupuesto_calculada` y `v_presupuesto_totales` están creadas
4. ✅ Los triggers de `triggers_sistema_versiones.sql` están ejecutados (opcional para pruebas)
5. ✅ Existe al menos un presupuesto en la tabla `presupuesto`
6. ✅ Existe al menos un cliente en la tabla `cliente`

---

## 🔧 Paso 1: Verificar Estructura de Base de Datos

### Verificar que existen las tablas necesarias:

```sql
-- Verificar tabla presupuesto
SELECT COUNT(*) as total_presupuestos FROM presupuesto WHERE activo_presupuesto = 1;

-- Verificar tabla presupuesto_version
SHOW TABLES LIKE 'presupuesto_version';

-- Verificar tabla linea_presupuesto
SHOW TABLES LIKE 'linea_presupuesto';

-- Verificar vistas
SHOW TABLES LIKE 'v_linea_presupuesto_calculada';
SHOW TABLES LIKE 'v_presupuesto_totales';
```

---

## 🎯 Paso 2: Crear Datos de Prueba

### Opción A: Si ya tienes presupuestos

```sql
-- 1. Verificar presupuestos existentes
SELECT id_presupuesto, numero_presupuesto, nombre_evento_presupuesto 
FROM presupuesto 
WHERE activo_presupuesto = 1 
LIMIT 5;

-- 2. Crear una versión de prueba para un presupuesto existente
-- IMPORTANTE: Reemplaza {ID_PRESUPUESTO} con un ID real de la consulta anterior

INSERT INTO presupuesto_version (
    id_presupuesto,
    numero_version_presupuesto,
    version_padre_presupuesto,
    estado_version_presupuesto,
    motivo_modificacion_version,
    creado_por_version,
    activo_version
) VALUES (
    {ID_PRESUPUESTO},  -- ⚠️ REEMPLAZAR con ID real
    1,
    NULL,
    'borrador',
    'Versión inicial para pruebas',
    1,  -- ID de usuario (ajustar si es necesario)
    1
);

-- 3. Obtener el ID de la versión creada
SELECT LAST_INSERT_ID() AS id_version_creada;
```

### Opción B: Crear presupuesto completo desde cero

```sql
-- 1. Verificar que existe al menos un cliente
SELECT id_cliente, nombre_cliente, apellido_cliente 
FROM cliente 
WHERE activo_cliente = 1 
LIMIT 5;

-- 2. Crear un presupuesto de prueba
INSERT INTO presupuesto (
    numero_presupuesto,
    id_cliente,
    id_estado_ppto,
    fecha_presupuesto,
    fecha_validez_presupuesto,
    nombre_evento_presupuesto,
    activo_presupuesto
) VALUES (
    CONCAT('P-TEST-', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')),
    1,  -- ⚠️ REEMPLAZAR con ID de cliente real
    1,  -- ID de estado "borrador" (ajustar si es necesario)
    CURDATE(),
    DATE_ADD(CURDATE(), INTERVAL 30 DAY),
    'Evento de Prueba - Líneas',
    1
);

-- 3. Obtener el ID del presupuesto creado
SET @id_presupuesto_test = LAST_INSERT_ID();

-- 4. Crear la primera versión automáticamente
INSERT INTO presupuesto_version (
    id_presupuesto,
    numero_version_presupuesto,
    version_padre_presupuesto,
    estado_version_presupuesto,
    motivo_modificacion_version,
    creado_por_version,
    activo_version
) VALUES (
    @id_presupuesto_test,
    1,
    NULL,
    'borrador',
    'Versión inicial de prueba',
    1,
    1
);

-- 5. Obtener el ID de la versión creada
SET @id_version_test = LAST_INSERT_ID();

SELECT @id_version_test AS 'ID de Versión para Pruebas';
```

---

## 🌐 Paso 3: Acceder al Módulo

Una vez tengas el **ID de versión** (por ejemplo: `id_version_presupuesto = 5`), accede a:

```
http://localhost/MDR/view/lineasPresupuesto/index.php?id_version_presupuesto=5
```

**⚠️ IMPORTANTE:** Reemplaza `5` con el ID real que obtuviste en el paso anterior.

---

## ✅ Paso 4: Verificaciones Visuales

### 4.1 Card de Información de Versión

Debe mostrar:
- ✅ Número de presupuesto (ej: P-2024-001)
- ✅ Nombre del cliente
- ✅ Nombre del evento
- ✅ Número de versión (v1)
- ✅ Badge de estado con color (BORRADOR en amarillo)

### 4.2 Tarjetas de Totales

Debe mostrar:
- ✅ Base Imponible: 0,00 € (sin líneas aún)
- ✅ IVA Total: 0,00 €
- ✅ TOTAL con IVA: 0,00 €
- ✅ Nº Líneas: 0

### 4.3 Botones Habilitados

Si el estado es **"borrador"**:
- ✅ Botón "Nueva Línea" debe estar **habilitado**
- ✅ NO debe aparecer la alerta de "Versión bloqueada"

Si el estado es **"enviado", "aceptado", "rechazado" o "caducado"**:
- ✅ Botón "Nueva Línea" debe estar **deshabilitado**
- ✅ Debe aparecer la alerta de "Versión bloqueada"

---

## 🧪 Paso 5: Probar Creación de Líneas

### 5.1 Abrir Modal de Nueva Línea

1. Clic en botón **"Nueva Línea"**
2. Debe abrirse el modal `formularioLinea.php`

### 5.2 Llenar Formulario de Prueba

**Datos de prueba:**

```
Tipo de Línea:     Artículo
Artículo:          (Seleccionar cualquiera del catálogo)
Descripción:       Toldo de prueba 3x3 metros
Cantidad:          2
Precio Unitario:   100.00
Descuento:         10
IVA:               21%

☑️ Aplicar Coeficiente de Jornadas
Nº Jornadas:       3
```

### 5.3 Verificar Preview de Cálculos

Debe calcular automáticamente:
- **Subtotal sin Dto.:** 200,00 € (2 × 100)
- **Base Imponible:** Varía según coeficiente
- **IVA:** 21% de la base
- **TOTAL:** Base + IVA

### 5.4 Guardar Línea

1. Clic en **"Guardar Línea"**
2. Debe aparecer mensaje de éxito
3. El modal se cierra
4. La tabla se recarga automáticamente
5. Las tarjetas de totales se actualizan

---

## 🔍 Paso 6: Verificar en Base de Datos

### Verificar que se insertó la línea

```sql
-- Reemplaza {ID_VERSION} con tu ID de versión
SELECT 
    id_linea_ppto,
    numero_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    descuento_linea_ppto,
    porcentaje_iva_linea_ppto,
    activo_linea_ppto
FROM linea_presupuesto
WHERE id_version_presupuesto = {ID_VERSION}
AND activo_linea_ppto = 1;
```

### Verificar cálculos en la vista

```sql
-- Reemplaza {ID_VERSION} con tu ID de versión
SELECT 
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    subtotal_sin_coeficiente,
    base_imponible,
    importe_iva,
    total_linea
FROM v_linea_presupuesto_calculada
WHERE id_version_presupuesto = {ID_VERSION}
AND activo_linea_ppto = 1;
```

### Verificar totales consolidados

```sql
-- Reemplaza {ID_VERSION} con tu ID de versión
SELECT 
    total_base_imponible,
    total_iva,
    iva_21,
    iva_10,
    iva_4,
    iva_0,
    total_con_iva,
    cantidad_lineas
FROM v_presupuesto_totales
WHERE id_version_presupuesto = {ID_VERSION};
```

---

## 🧪 Paso 7: Pruebas de Funcionalidades

### ✅ Test 1: Crear múltiples líneas

1. Crear 3-5 líneas con diferentes tipos (artículo, kit, texto, sección)
2. Verificar que aparecen en la tabla
3. Verificar que los totales suman correctamente

### ✅ Test 2: Editar una línea

1. Clic en botón de editar (lápiz amarillo)
2. Modificar cantidad o precio
3. Guardar cambios
4. Verificar que se actualizó en la tabla
5. Verificar que los totales se recalcularon

### ✅ Test 3: Eliminar una línea

1. Clic en botón de eliminar (papelera roja)
2. Confirmar eliminación
3. Verificar que desaparece de la tabla
4. Verificar que los totales se actualizaron

### ✅ Test 4: Filtros de tabla

1. Usar filtros en el footer de la tabla:
   - Buscar por descripción
   - Filtrar por tipo de línea
   - Filtrar por estado (activo/inactivo)
2. Verificar que aparece la alerta de "Filtros aplicados"
3. Clic en "Limpiar filtros"
4. Verificar que se quitan todos los filtros

### ✅ Test 5: Versión bloqueada

1. Cambiar el estado de la versión a "enviado":
   ```sql
   UPDATE presupuesto_version 
   SET estado_version_presupuesto = 'enviado' 
   WHERE id_version_presupuesto = {ID_VERSION};
   ```
2. Recargar la página
3. Verificar que:
   - Aparece alerta de "Versión bloqueada"
   - Botón "Nueva Línea" está deshabilitado
   - Botones de editar/eliminar muestran candado
4. Intentar editar o eliminar → debe mostrar mensaje de error
5. Volver a estado "borrador" para continuar pruebas:
   ```sql
   UPDATE presupuesto_version 
   SET estado_version_presupuesto = 'borrador' 
   WHERE id_version_presupuesto = {ID_VERSION};
   ```

---

## 🐛 Solución de Problemas

### Problema 1: No carga la información de la versión

**Síntomas:** Card de información muestra "Cargando..."

**Solución:**
```sql
-- Verificar que la versión existe y está activa
SELECT * FROM presupuesto_version WHERE id_version_presupuesto = {ID};

-- Verificar que el presupuesto asociado existe
SELECT p.*, pv.* 
FROM presupuesto_version pv
INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
WHERE pv.id_version_presupuesto = {ID};
```

**Revisar en consola del navegador:**
- Abrir DevTools (F12)
- Ir a pestaña "Console"
- Buscar errores AJAX

### Problema 2: Error "ID de versión no proporcionado"

**Causa:** No se pasó el parámetro `id_version_presupuesto` en la URL

**Solución:** Asegurarse de acceder con:
```
?id_version_presupuesto={ID_REAL}
```

### Problema 3: Tabla vacía pero hay líneas en BD

**Verificar:**
```sql
-- ¿Las líneas están activas?
SELECT COUNT(*) FROM linea_presupuesto 
WHERE id_version_presupuesto = {ID} 
AND activo_linea_ppto = 1;

-- Revisar la respuesta del controller en Network
```

**En navegador:**
- F12 → Network → XHR
- Buscar la petición a `lineapresupuesto.php?op=listar`
- Ver la respuesta JSON

### Problema 4: Los totales no se calculan

**Verificar que existe la vista:**
```sql
SELECT * FROM v_presupuesto_totales 
WHERE id_version_presupuesto = {ID};
```

Si no existe, ejecutar:
```bash
SOURCE w:/MDR/BD/presupuesto/v_presupuesto_totales.sql;
```

---

## 📊 Checklist de Pruebas Completas

### Funcionalidad
- [ ] Card de información se carga correctamente
- [ ] Totales del pie se muestran y calculan bien
- [ ] DataTable carga las líneas
- [ ] Botón "Nueva Línea" funciona
- [ ] Modal de formulario se abre y cierra
- [ ] Se pueden crear líneas nuevas
- [ ] Se pueden editar líneas existentes
- [ ] Se pueden eliminar líneas (soft delete)
- [ ] Los totales se actualizan automáticamente
- [ ] Filtros de tabla funcionan correctamente

### Sistema de Versiones
- [ ] Estado "borrador" permite editar
- [ ] Estados bloqueados ("enviado", etc.) desactivan botones
- [ ] Aparece alerta cuando versión está bloqueada
- [ ] No se puede editar/eliminar en versiones bloqueadas

### Interfaz
- [ ] Responsive design funciona en móvil
- [ ] Breadcrumb correcto
- [ ] Iconos y colores apropiados
- [ ] Badges de estado con colores correctos
- [ ] SweetAlert2 muestra mensajes correctamente
- [ ] Modal de ayuda se abre correctamente

### Base de Datos
- [ ] INSERT de líneas funciona
- [ ] UPDATE de líneas funciona
- [ ] Soft delete (activo=0) funciona
- [ ] Vistas calculan correctamente
- [ ] Totales consolidados son correctos

---

## 🚀 Prueba Rápida (Script Completo)

```sql
-- ===================================================
-- SCRIPT DE PRUEBA RÁPIDA
-- Copiar y ejecutar todo junto
-- ===================================================

-- 1. Crear presupuesto de prueba
INSERT INTO presupuesto (
    numero_presupuesto,
    id_cliente,
    id_estado_ppto,
    fecha_presupuesto,
    fecha_validez_presupuesto,
    nombre_evento_presupuesto,
    activo_presupuesto
) VALUES (
    CONCAT('P-PRUEBA-', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')),
    (SELECT id_cliente FROM cliente WHERE activo_cliente = 1 LIMIT 1),
    (SELECT id_estado_ppto FROM estado_presupuesto WHERE codigo_estado_ppto = 'BORRADOR' LIMIT 1),
    CURDATE(),
    DATE_ADD(CURDATE(), INTERVAL 30 DAY),
    'PRUEBA - Módulo Líneas',
    1
);

SET @id_ppto = LAST_INSERT_ID();

-- 2. Crear versión 1
INSERT INTO presupuesto_version (
    id_presupuesto,
    numero_version_presupuesto,
    estado_version_presupuesto,
    motivo_modificacion_version,
    creado_por_version,
    activo_version
) VALUES (
    @id_ppto,
    1,
    'borrador',
    'Versión inicial para pruebas del módulo',
    1,
    1
);

SET @id_version = LAST_INSERT_ID();

-- 3. Mostrar URL para acceder
SELECT CONCAT(
    'http://localhost/MDR/view/lineasPresupuesto/index.php?id_version_presupuesto=',
    @id_version
) AS 'URL_PARA_PROBAR';

-- 4. Guardar IDs para consultas posteriores
SELECT 
    @id_ppto AS 'ID_Presupuesto',
    @id_version AS 'ID_Version',
    'Estado: BORRADOR - Se puede editar' AS 'Nota';
```

---

## 📝 Notas Finales

1. **Permisos:** Asegúrate de estar logueado en el sistema
2. **Artículos:** Debe haber artículos en el catálogo para poder añadirlos a las líneas
3. **Estados:** Prueba todos los estados de versión para verificar el sistema de bloqueo
4. **Triggers:** Si ejecutaste los triggers, verifica que funcionan correctamente
5. **Logs:** Revisa `public/logs/` para ver el registro de actividad

¡El módulo está listo para usar! 🎉
