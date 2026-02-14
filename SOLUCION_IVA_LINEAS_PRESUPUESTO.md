# Solución: IVA 0% en Líneas para Clientes Exentos

**ACTUALIZACIÓN 14-Feb-2026:** Corrección completa de problemas identificados

## 🐛 Problemas Reportados

### Problema 1: IVA no se fuerza a 0%
Al añadir líneas de presupuesto a un cliente **marcado como exento de IVA**, el campo de IVA mostraba el **21% del artículo** en lugar de **forzarse a 0%**.

### Problema 2: Campos precio e IVA editables
Los campos **precio unitario** e **IVA** estaban habilitados para edición, cuando deben ser **readonly** (solo lectura) y **tomar valores del artículo**.

---

## 🔍 Diagnóstico (Actualizado)

Se identificaron **3 problemas críticos**:

### 1. Modelo NO devolvía campo `exento_iva_cliente`
**Archivo:** `models/Presupuesto.php` (método `get_info_version`)

El SELECT NO incluía el campo `exento_iva_cliente` de la tabla cliente:

```php
// ❌ PROBLEMA: Faltaba campo en SELECT
SELECT 
    c.id_cliente,
    c.nombre_cliente,
    c.email_cliente,
    c.telefono_cliente
    -- FALTABA: c.exento_iva_cliente
FROM ...
```

**Resultado:** La variable `clienteExentoIVA` en JavaScript siempre era `undefined` o `false`.

### 2. Campos precio e IVA eran editables
**Archivo:** `view/lineasPresupuesto/formularioLinea.php`

Los campos se habilitaban con `.prop('disabled', false).prop('readonly', false)`:

```javascript
// ❌ PROBLEMA: Campos editables
$('#porcentaje_iva_linea_ppto').val(tasaIva).prop('disabled', false).prop('readonly', false);
$('#precio_unitario_linea_ppto').val(precio); // Sin readonly
```

**Resultado:** Usuario podía modificar precio e IVA manualmente.

### 3. Validación de variable poco robusta
**Archivo:** `view/lineasPresupuesto/lineasPresupuesto.js`

La validación `if (typeof clienteExentoIVA !== 'undefined' && clienteExentoIVA === true)` era muy estricta y la variable no estaba en `window` global.

---

## ✅ Solución Implementada (Completa)

### 1. **Archivo:** `models/Presupuesto.php`

**Ubicación:** Método `get_info_version()` línea ~243

**Cambio:** Agregar campo `exento_iva_cliente` al SELECT

```php
// ✅ SOLUCIÓN
SELECT 
    -- Datos del cliente
    c.id_cliente,
    c.nombre_cliente,
    c.email_cliente,
    c.telefono_cliente,
    c.exento_iva_cliente  -- ✅ AGREGADO
FROM presupuesto_version pv
INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
INNER JOIN cliente c ON p.id_cliente = c.id_cliente
WHERE pv.id_version_presupuesto = ?
```

**Resultado:** Ahora el backend SÍ devuelve el campo `exento_iva_cliente`.

---

### 2. **Archivo:** `view/lineasPresupuesto/lineasPresupuesto.js`

#### 2.1. Actualizar variable global (línea ~98)

```javascript
// ✅ SOLUCIÓN: Variable en window global con logs de debug
window.clienteExentoIVA = (data.exento_iva_cliente == 1 || data.exento_iva_cliente === true);
console.log('DEBUG - exento_iva_cliente recibido:', data.exento_iva_cliente);
console.log('DEBUG - clienteExentoIVA asignado:', window.clienteExentoIVA);
if (window.clienteExentoIVA) {
    console.log('⚠ Cliente EXENTO de IVA detectado - IVA será forzado a 0%');
} else {
    console.log('ℹ Cliente normal (NO exento) - IVA según artículo');
}
```

#### 2.2. Modal nueva línea (línea ~808)

```javascript
// ✅ SOLUCIÓN: Campos SIEMPRE readonly
$('#precio_unitario_linea_ppto').prop('readonly', true);
$('#porcentaje_iva_linea_ppto').prop('readonly', true);

if (window.clienteExentoIVA === true) {
    $('#porcentaje_iva_linea_ppto').val(0);
    console.log('✓ Modal nueva línea: IVA forzado a 0% (Cliente exento)');
} else {
    $('#porcentaje_iva_linea_ppto').val(21);
    console.log('✓ Modal nueva línea: IVA por defecto 21% (Cliente normal)');
}
```

#### 2.3. Modal edición (línea ~927)

```javascript
// ✅ SOLUCIÓN: Campos SIEMPRE readonly
$('#precio_unitario_linea_ppto').prop('readonly', true);
$('#porcentaje_iva_linea_ppto').prop('readonly', true);

if (window.clienteExentoIVA === true) {
    $('#porcentaje_iva_linea_ppto').val(0);
    console.log('✓ Modal edición: IVA forzado a 0% (Cliente exento)');
} else {
    $('#porcentaje_iva_linea_ppto').val(data.porcentaje_iva_linea_ppto || 21);
    console.log('✓ Modal edición: IVA de la línea cargado:', data.porcentaje_iva_linea_ppto || 21);
}
```

---

### 3. **Archivo:** `view/lineasPresupuesto/formularioLinea.php`

**Ubicación:** Función `cargarDatosArticulo()` línea ~442

**Cambio:** Campos precio e IVA SIEMPRE readonly + validación simplificada

```javascript
// ✅ SOLUCIÓN COMPLETA
// Cargar precio de alquiler - SOLO en creación
if (!esEdicion) {
    const precioArticulo = parseFloat(data.precio_alquiler_articulo || 0).toFixed(2);
    $('#precio_unitario_linea_ppto').val(precioArticulo);
}
// Hacer campo precio readonly SIEMPRE
$('#precio_unitario_linea_ppto').prop('readonly', true);

// *** PUNTO 17: Cargar IVA según si cliente está exento ***
if (window.clienteExentoIVA === true) {
    $('#porcentaje_iva_linea_ppto').val(0);
    console.log('✓ IVA forzado a 0% (Cliente exento de IVA)');
} else {
    const tasaIva = data.tasa_impuesto || 21;
    $('#porcentaje_iva_linea_ppto').val(tasaIva);
    console.log('✓ IVA del artículo aplicado:', tasaIva + '%');
}
// Hacer campo IVA readonly SIEMPRE
$('#porcentaje_iva_linea_ppto').prop('readonly', true);
```

**Mejoras:**
- ✅ Validación simplificada: `window.clienteExentoIVA === true`
- ✅ Campos **precio e IVA siempre readonly**
- ✅ Logs específicos para debugging
- ✅ Sin uso de `disabled` (que previene envío del valor)

---

## 🔄 Flujo Completo Corregido

### 1. Al Cargar Página de Líneas
```javascript
// index.php - Variable global inicializada
clienteExentoIVA = false;
```

### 2. Al Cargar Información de la Versión
```javascript
// lineasPresupuesto.js - función cargarInfoVersion() línea 97
clienteExentoIVA = (data.exento_iva_cliente == 1 || data.exento_iva_cliente === true);
if (clienteExentoIVA) {
    console.log('⚠ Cliente exento de IVA detectado - IVA será forzado a 0%');
}
```

### 3. Al Abrir Modal de Nueva Línea
```javascript
// lineasPresupuesto.js - función nuevaLinea() línea 808
if (clienteExentoIVA) {
    $('#porcentaje_iva_linea_ppto').val(0).prop('disabled', true).prop('readonly', true);
    console.log('✓ IVA forzado a 0% (Cliente exento)');
}
```

### 4. Al Seleccionar un Artículo
```javascript
// formularioLinea.php - función cargarDatosArticulo() línea 446 (CORREGIDO)
if (clienteExentoIVA === true) {
    // Cliente exento: IVA 0%, campo bloqueado
    $('#porcentaje_iva_linea_ppto').val(0).prop('disabled', true);
} else {
    // Cliente normal: IVA del artículo, campo editable
    $('#porcentaje_iva_linea_ppto').val(data.tasa_impuesto || 21);
}
```

### 5. Al Editar Línea Existente
```javascript
// lineasPresupuesto.js - función editarLinea() línea 927
if (clienteExentoIVA) {
    $('#porcentaje_iva_linea_ppto').val(0).prop('disabled', true).prop('readonly', true);
} else {
    $('#porcentaje_iva_linea_ppto').val(data.porcentaje_iva_linea_ppto || 21);
}
```

---

## 📝 Cambios No Realizados (No Necesarios)

### Vista `contacto_cantidad_cliente`
- **PENDIENTE:** Actualizar vista SQL para incluir campos `exento_iva_cliente` y `justificacion_exencion_iva_cliente`
- **Motivo:** Esta vista se usa en el **formulario de clientes** para cargar datos en edición
- **Estado:** Script SQL creado en `BD/migrations/020260213_agregar_exento_iva_a_vista.sql`
- **Acción requerida:** Ejecutar el script manualmente en phpMyAdmin o HeidiSQL

**IMPORTANTE:** La vista NO afecta a las líneas de presupuesto, por lo que la funcionalidad de IVA 0% funciona correctamente sin actualizar la vista. La vista solo es necesaria para que el formulario de clientes muestre correctamente los campos de exención después de guardar.

---

## ✅ Pruebas a Realizar

### Caso 1: Cliente Exento de IVA - Nueva Línea
1. Ir a un presupuesto con un **cliente marcado como exento de IVA**
2. Verificar que aparece el **banner amarillo de alerta** en la cabecera
3. Hacer clic en **"Nueva Línea"**
4. Verificar que el campo **"IVA (%)"** muestra **0.00** y está **deshabilitado**
5. **Seleccionar un artículo** (ej: con IVA 21%)
6. Verificar que el campo **IVA sigue en 0.00** (no cambia al 21%)
7. Verificar en **consola** (F12) el mensaje: `✓ IVA forzado a 0% para artículo (Cliente exento de IVA)`
8. Guardar la línea
9. Verificar que la línea guardada tiene **IVA 0%** en la tabla

### Caso 2: Cliente Exento de IVA - Editar Línea
1. Editar una línea existente del presupuesto
2. Verificar que el campo **IVA está en 0.00** y **deshabilitado**
3. Cambiar el artículo por otro
4. Verificar que el IVA **permanece en 0.00**
5. Guardar
6. Verificar que se mantiene el **IVA 0%**

### Caso 3: Cliente NO Exento - Nueva Línea
1. Ir a un presupuesto con un **cliente normal** (NO exento)
2. Verificar que **NO aparece** el banner amarillo de alerta
3. Hacer clic en **"Nueva Línea"**
4. Verificar que el campo **IVA muestra 21%** y está **habilitado**
5. Seleccionar un artículo
6. Verificar que el IVA se **actualiza con el valor del artículo** (generalmente 21%)
7. El campo debe ser **editable** (se puede cambiar manualmente)

### Caso 4: Verificar Consola del Navegador
1. Abrir **Consola** (F12 → Consola)
2. Al cargar página de líneas con cliente exento, debe mostrar:
   ```
   ⚠ Cliente exento de IVA detectado - IVA será forzado a 0%
   ```
3. Al abrir modal de nueva línea:
   ```
   ✓ IVA forzado a 0% (Cliente exento)
   ```
4. Al seleccionar artículo:
   ```
   ✓ IVA forzado a 0% para artículo (Cliente exento de IVA)
   ```

---

## 🎯 Resultado Esperado

### Comportamiento Correcto

| Situación | Cliente Exento | Cliente Normal |
|-----------|----------------|----------------|
| **Al abrir modal de nueva línea** | IVA = 0%, deshabilitado | IVA = 21%, habilitado |
| **Al seleccionar artículo** | IVA permanece en 0% | IVA = valor del artículo |
| **Campo editable** | ❌ NO (readonly) | ✅ SÍ |
| **Al guardar línea** | Se guarda con IVA 0% | Se guarda con IVA del artículo |
| **Alerta en cabecera presupuesto** | ✅ Sí (banner amarillo) | ❌ No |
| **Cálculo de totales** | Base imponible, IVA=0, Total=Base | Base imponible, IVA calculado, Total con IVA |

---

## 📁 Archivos Modificados

1. ✅ `view/lineasPresupuesto/index.php` - Declaración de variable global
2. ✅ `view/lineasPresupuesto/lineasPresupuesto.js` - Eliminada declaración local duplicada
3. ✅ `view/lineasPresupuesto/formularioLinea.php` - Verificación de exención al cargar artículo

---

## 📅 Fecha de Implementación

**14 de febrero de 2026**

---

## 🔗 Relacionado con

- **Punto 17:** Clientes Exentos de IVA
- **Archivo de referencia:** `.github/copilot-instructions.md`
- **Migración SQL pendiente:** `BD/migrations/020260213_agregar_exento_iva_a_vista.sql`

---

## ✨ Mejoras Aplicadas

- ✅ IVA forzado a 0% para clientes exentos en líneas de presupuesto
- ✅ Campo de IVA deshabilitado cuando cliente exento (no editable)
- ✅ Validación al seleccionar artículo (no sobrescribe el 0%)
- ✅ Variable global compartida entre módulos
- ✅ Mensajes de consola para debugging
- ✅ Funcionamiento consistente en creación y edición de líneas

---

**NOTA:** Recuerda ejecutar el script SQL `020260213_agregar_exento_iva_a_vista.sql` para actualizar la vista `contacto_cantidad_cliente` y que el formulario de clientes muestre correctamente los campos de exención después de guardar.
