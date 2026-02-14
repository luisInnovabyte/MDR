# Solución: IVA 0% en Líneas para Clientes Exentos

## 🐛 Problema Reportado

Al añadir líneas de presupuesto a un cliente **marcado como exento de IVA**, el campo de IVA mostraba el **21% del artículo** en lugar de **forzarse a 0%**.

## 🔍 Diagnóstico

El problema estaba en el archivo `view/lineasPresupuesto/formularioLinea.php` (líneas 444-447):

```javascript
// ❌ CÓDIGO PROBLEMÁTICO (ORIGINAL)
const tasaIva = data.tasa_impuesto || 21;
$('#porcentaje_iva_linea_ppto').val(tasaIva);
```

Cuando se seleccionaba un artículo, **siempre** se establecía el IVA del artículo sin verificar si el cliente estaba exento, sobrescribiendo el 0% que se había establecido al abrir el modal.

---

## ✅ Solución Implementada

### 1. **Archivo:** `view/lineasPresupuesto/index.php`

**Ubicación:** Después de cargar `mainJs.php` (línea ~437)

**Cambio:** Declaración de variable global

```html
<!-- *** PUNTO 17: Variables globales para exención de IVA *** -->
<script>
    // Variable global para indicar si el cliente está exento de IVA
    // Se inicializa aquí y se actualiza cuando se carga la info de la versión
    let clienteExentoIVA = false;
</script>
```

**Motivo:** La variable debe estar disponible **antes** de cargar cualquier script que la use.

---

### 2. **Archivo:** `view/lineasPresupuesto/lineasPresupuesto.js`

**Ubicación:** Línea 15

**Cambio:** Eliminar declaración local de la variable

```javascript
// ❌ ANTES (declaración local duplicada)
let clienteExentoIVA = false;

// ✅ AHORA (usa la variable global)
// clienteExentoIVA ya está declarada globalmente en index.php
```

**Motivo:** Evitar conflicto con la variable global. El script ahora **actualiza** la variable global en lugar de crear una local.

---

### 3. **Archivo:** `view/lineasPresupuesto/formularioLinea.php`

**Ubicación:** Líneas 444-455 (función `cargarDatosArticulo`)

**Cambio:** Verificar exención de IVA antes de cargar IVA del artículo

```javascript
// ✅ CÓDIGO CORREGIDO
// *** PUNTO 17: Cargar IVA según si cliente está exento ***
// Si cliente exento IVA: forzar 0% y deshabilitar campo
// Si NO exento: usar IVA del artículo
if (typeof clienteExentoIVA !== 'undefined' && clienteExentoIVA === true) {
    $('#porcentaje_iva_linea_ppto').val(0).prop('disabled', true).prop('readonly', true);
    console.log('✓ IVA forzado a 0% para artículo (Cliente exento de IVA)');
} else {
    const tasaIva = data.tasa_impuesto || 21;
    $('#porcentaje_iva_linea_ppto').val(tasaIva).prop('disabled', false).prop('readonly', false);
}
```

**Motivo:** 
- **Si cliente exento:** IVA = 0%, campo deshabilitado y de solo lectura
- **Si NO exento:** IVA = valor del artículo (21% por defecto), campo editable

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
