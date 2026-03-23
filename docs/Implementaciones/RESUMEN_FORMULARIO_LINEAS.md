# 📋 RESUMEN: Formulario de Líneas de Presupuesto - Completo

## ✅ **ARCHIVOS MODIFICADOS:**

### 1. **formularioLinea.php** - Formulario completo reestructurado

**Estructura implementada:**
- ✅ **Sección 1 - Artículo** (azul): Select artículos + descripción readonly
- ✅ **Sección 2 - Fechas** (info): 4 campos (montaje, desmontaje, inicio, fin)
- ✅ **Sección 3 - Precios** (verde): Cantidad, precio, descuento, IVA, total
- ✅ **Sección 4 - Ubicación** (gris): Select ubicaciones + checkbox ocultar kit
- ✅ **Sección 5 - Coeficiente** (amarillo): Checkbox aplicar + cálculo automático
- ✅ **Sección 6 - Observaciones** (negro): Textarea

**JavaScript incluido:**
- ✅ `cargarDatosArticulo()` - Carga precio, descripción, IVA del artículo
- ✅ `calcularJornadas()` - Calcula días entre fechas
- ✅ `cargarCoeficiente()` - Obtiene coeficiente según jornadas
- ✅ `calcularPreview()` - Actualización en tiempo real de totales
- ✅ `formatearMoneda()` - Formato español con €

---

### 2. **lineasPresupuesto.js** - Funciones AJAX añadidas

```javascript
// ✅ Función principal para abrir modal
function abrirModalNuevaLinea()

// ✅ Carga fechas desde cabecera presupuesto
function cargarFechasIniciales()

// ✅ Carga artículos disponibles (incluye KITs deshabilitados)
function cargarArticulosDisponibles()

// ✅ Carga ubicaciones del cliente
function cargarUbicacionesCliente()

// ✅ Auxiliar para cargar ubicaciones por ID cliente
function cargarUbicacionesPorCliente(idCliente)
```

---

## ⏳ **ENDPOINTS PENDIENTES DE AÑADIR:**

### 📄 **1. Controller: presupuesto.php**
**Archivo:** `w:\MDR\controller\presupuesto.php`  
**Instrucciones:** Ver archivo `w:\MDR\docs\AÑADIR_get_fechas_evento.md`

**Nuevo case:**
```php
case "get_fechas_evento":
    // Retorna fecha_inicio_evento y fecha_fin_evento del presupuesto
```

---

### 📄 **2. Controller: articulo.php**
**Archivo:** `w:\MDR\controller\articulo.php`  
**Instrucciones:** Ver archivo `w:\MDR\docs\AÑADIR_listar_para_presupuesto.md`

**Nuevo case:**
```php
case "listar_para_presupuesto":
    // Lista artículos + KITs con campos necesarios
    // Incluye: precio_alquiler_articulo, porcentaje_iva, es_kit
```

---

### 📄 **3. Controller: ubicaciones.php**
**Archivo:** `w:\MDR\controller\ubicaciones.php`  
**Instrucciones:** Ver archivo `w:\MDR\docs\AÑADIR_listar_por_cliente_ubicaciones.md`

**Nuevo case:**
```php
case "listar_por_cliente":
    // Retorna ubicaciones activas de un cliente específico
```

---

### 📄 **4. Controller: coeficiente.php**
**Archivo:** `w:\MDR\controller\coeficiente.php`  
**Instrucciones:** Ver archivo `w:\MDR\docs\AÑADIR_obtener_por_jornadas_coeficiente.md`

**Nuevo case:**
```php
case "obtener_por_jornadas":
    // Busca coeficiente según número de jornadas
    // Retorna el coeficiente más cercano (menor o igual)
```

---

## 🔧 **PRÓXIMOS PASOS:**

1. **Añadir los 4 endpoints** a sus respectivos controllers siguiendo las instrucciones en los archivos `.md`

2. **Probar el formulario:**
   - Abrir modal → Verificar que carguen:
     - ✅ Fechas (4 campos prellenados)
     - ✅ Artículos en select (con KITs deshabilitados)
     - ✅ Ubicaciones del cliente
   
3. **Seleccionar un artículo:**
   - Debe cargar automáticamente:
     - ✅ Descripción (readonly)
     - ✅ Precio (readonly)
     - ✅ IVA (readonly)

4. **Activar coeficiente:**
   - Marcar checkbox → Debe calcular:
     - ✅ Jornadas (diferencia entre fechas)
     - ✅ Coeficiente aplicable
     - ✅ Precio con coeficiente

5. **Calcular preview:**
   - Cambiar cantidad/precio/descuento
   - Debe actualizar en tiempo real:
     - ✅ Subtotal
     - ✅ Descuento
     - ✅ Base imponible
     - ✅ IVA
     - ✅ Total

---

## 📊 **CAMPOS MAPEADOS A BD:**

| Campo Formulario | Campo BD | Tipo | Fuente |
|-----------------|----------|------|--------|
| `id_articulo` | `id_articulo` | INT | Select artículos |
| `descripcion_linea_ppto` | `descripcion_linea_ppto` | TEXT | Desde artículo (readonly) |
| `fecha_montaje_linea_ppto` | `fecha_montaje_linea_ppto` | DATE | Desde presupuesto |
| `fecha_desmontaje_linea_ppto` | `fecha_desmontaje_linea_ppto` | DATE | Desde presupuesto |
| `fecha_inicio_linea_ppto` | `fecha_inicio_linea_ppto` | DATE | Desde presupuesto |
| `fecha_fin_linea_ppto` | `fecha_fin_linea_ppto` | DATE | Desde presupuesto |
| `cantidad_linea_ppto` | `cantidad_linea_ppto` | DECIMAL(10,2) | Input manual |
| `precio_unitario_linea_ppto` | `precio_unitario_linea_ppto` | DECIMAL(10,2) | Desde artículo (readonly) |
| `descuento_linea_ppto` | `descuento_linea_ppto` | DECIMAL(5,2) | Input manual |
| `porcentaje_iva_linea_ppto` | `porcentaje_iva_linea_ppto` | DECIMAL(5,2) | Desde artículo (readonly) |
| `id_ubicacion` | `id_ubicacion` | INT | Select ubicaciones |
| `aplicar_coeficiente_linea_ppto` | `aplicar_coeficiente_linea_ppto` | BOOLEAN | Checkbox |
| `id_coeficiente` | `id_coeficiente` | INT | Auto según jornadas |
| `jornadas_linea_ppto` | `jornadas_linea_ppto` | INT | Calculado de fechas |
| `valor_coeficiente_linea_ppto` | `valor_coeficiente_linea_ppto` | DECIMAL(10,2) | Desde tabla coeficiente |
| `ocultar_detalle_kit_linea_ppto` | `ocultar_detalle_kit_linea_ppto` | BOOLEAN | Checkbox |
| `observaciones_linea_ppto` | `observaciones_linea_ppto` | TEXT | Textarea |

---

## 🎯 **ESTADO ACTUAL:**

- ✅ Formulario completo estructurado en 6 secciones
- ✅ JavaScript con todas las funciones de carga y cálculo
- ✅ Modal responsive al 95% de ancho
- ✅ Preview de totales en tiempo real
- ⏳ **Pendiente:** Añadir 4 endpoints a controllers
- ⏳ **Pendiente:** Implementar función `guardarLinea()` en formulario
- ⏳ **Pendiente:** Implementar `case "guardaryeditar"` en lineapresupuesto.php controller

---

¿Todo listo para añadir los endpoints a los controllers?
