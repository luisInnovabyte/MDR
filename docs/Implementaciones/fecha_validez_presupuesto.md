# Implementación de Fecha de Validez de Presupuesto

## Fecha de Implementación
12 de diciembre de 2025

## Descripción General

Se ha implementado una funcionalidad para calcular automáticamente la fecha de validez de los presupuestos basándose en un parámetro configurable por empresa (`dias_validez_presupuesto_empresa`). Esta fecha se calcula automáticamente al crear un nuevo presupuesto, pero permite modificación manual por parte del usuario.

---

## 1. Modificaciones en Base de Datos

### Tabla `empresa`

Se añadió el campo `dias_validez_presupuesto_empresa` para almacenar el número de días de validez por defecto que tendrá cada presupuesto emitido por esa empresa.

```sql
ALTER TABLE empresa
ADD COLUMN dias_validez_presupuesto_empresa INT UNSIGNED NOT NULL DEFAULT 30 
    COMMENT 'Días de validez por defecto para los presupuestos emitidos por esta empresa'
    AFTER numero_actual_presupuesto_empresa;
```

**Ubicación:** Después del campo `numero_actual_presupuesto_empresa`  
**Tipo:** `INT UNSIGNED`  
**Valor por defecto:** `30` días  
**NOT NULL:** Sí (siempre debe tener un valor)

---

## 2. Modificaciones en el Modelo

### Archivo: `w:\MDR\models\Empresas.php`

#### Método `get_empresaActiva()`

Se modificó la consulta SQL para incluir el nuevo campo en el SELECT:

```php
public function get_empresaActiva() {
    $sql = "SELECT 
        id_empresa, 
        codigo_empresa, 
        nombre_empresa, 
        nombre_comercial_empresa,
        nif_empresa,
        direccion_fiscal_empresa,
        cp_fiscal_empresa,
        poblacion_fiscal_empresa,
        provincia_fiscal_empresa,
        pais_fiscal_empresa,
        telefono_empresa,
        movil_empresa,
        email_empresa,
        email_facturacion_empresa,
        web_empresa,
        iban_empresa,
        swift_empresa,
        banco_empresa,
        serie_presupuesto_empresa,
        numero_actual_presupuesto_empresa,
        dias_validez_presupuesto_empresa,  /* ← CAMPO AÑADIDO */
        logotipo_empresa,
        texto_pie_presupuesto_empresa,
        ficticia_empresa,
        empresa_ficticia_principal,
        activo_empresa
    FROM empresa 
    WHERE empresa_ficticia_principal = TRUE 
    AND activo_empresa = TRUE
    LIMIT 1";
    
    return ejecutarConsultaSimpleFila($sql);
}
```

**Justificación:** Este campo es necesario para que el frontend pueda obtener el número de días configurado y calcular automáticamente la fecha de validez.

---

## 3. Modificaciones en el Controlador

### Archivo: `w:\MDR\controller\empresas.php`

No fue necesario modificar este controlador ya que el caso `"obtenerEmpresaActiva"` simplemente llama al método del modelo y devuelve los datos en JSON. Al añadir el campo al SELECT del modelo, automáticamente queda disponible en la respuesta JSON.

```php
case "obtenerEmpresaActiva":
    $rspta = $empresa->get_empresaActiva();
    echo json_encode($rspta);
    break;
```

---

## 4. Modificaciones en el Frontend

### Archivo: `w:\MDR\view\Presupuesto\formularioPresupuesto.js`

Esta fue la modificación más compleja debido a la **naturaleza asíncrona de las peticiones AJAX**.

#### 4.1. Variable Global

```javascript
var diasValidezPresupuesto = 30; // Valor por defecto
```

#### 4.2. Función para Cargar Días de Validez

Se creó una función que retorna una **Promise** para garantizar la carga asíncrona de los datos:

```javascript
function cargarDiasValidezEmpresa() {
    console.log("⏳ Cargando días de validez desde empresa...");
    
    return $.ajax({
        url: "../controller/empresas.php?op=obtenerEmpresaActiva",
        type: "GET",
        dataType: "json"
    }).then(function(data) {
        if (data && data.dias_validez_presupuesto_empresa) {
            diasValidezPresupuesto = parseInt(data.dias_validez_presupuesto_empresa);
            console.log("✓ Días de validez cargados desde empresa: " + diasValidezPresupuesto);
            return diasValidezPresupuesto;
        } else {
            console.warn("⚠ No se obtuvieron días de validez. Usando valor por defecto: 30");
            diasValidezPresupuesto = 30;
            return diasValidezPresupuesto;
        }
    }).fail(function(jqXHR, textStatus, errorThrown) {
        console.error("✗ Error al cargar días de validez:", textStatus, errorThrown);
        diasValidezPresupuesto = 30;
        return diasValidezPresupuesto;
    });
}
```

**Características importantes:**
- Retorna una **Promise** usando `.then()` de jQuery
- Registra en consola cada paso del proceso con iconos (⏳, ✓, ⚠, ✗)
- Maneja errores con valor por defecto de 30 días

#### 4.3. Función para Calcular Fecha de Validez

```javascript
function calcularFechaValidez(fechaPresupuesto) {
    if (!fechaPresupuesto) {
        console.warn("⚠ No se proporcionó fecha de presupuesto");
        return '';
    }
    
    var fecha = new Date(fechaPresupuesto + 'T00:00:00');
    fecha.setDate(fecha.getDate() + diasValidezPresupuesto);
    
    var anio = fecha.getFullYear();
    var mes = String(fecha.getMonth() + 1).padStart(2, '0');
    var dia = String(fecha.getDate()).padStart(2, '0');
    
    var fechaValidezCalculada = anio + '-' + mes + '-' + dia;
    console.log("✓ Fecha de validez calculada automáticamente: " + 
                fechaValidezCalculada + " (+" + diasValidezPresupuesto + " días)");
    
    return fechaValidezCalculada;
}
```

#### 4.4. Inicialización en Formulario Nuevo

```javascript
if ($("#id_presupuesto").length && !$("#id_presupuesto").val()) {
    console.log("📝 Nuevo presupuesto: Inicializando fechas automáticas...");
    
    var hoy = obtenerFechaHoy();
    $('#fecha_presupuesto').val(hoy);
    
    // Cargar días de validez ANTES de calcular fecha de validez
    cargarDiasValidezEmpresa().then(function() {
        var fechaValidez = calcularFechaValidez(hoy);
        if (fechaValidez) {
            $('#fecha_validez_presupuesto').val(fechaValidez);
        }
    });
}
```

**Importante:** Se usa `.then()` para esperar a que se carguen los días de validez **antes** de calcular la fecha.

#### 4.5. Listener para Cambio de Fecha de Presupuesto

```javascript
$('#fecha_presupuesto').on('change', function() {
    console.log("📅 Fecha de presupuesto modificada: " + $(this).val());
    
    var fechaPresupuesto = $(this).val();
    if (fechaPresupuesto) {
        cargarDiasValidezEmpresa().then(function() {
            var fechaValidez = calcularFechaValidez(fechaPresupuesto);
            if (fechaValidez) {
                $('#fecha_validez_presupuesto').val(fechaValidez);
            }
        });
    }
});
```

**Importante:** También usa `.then()` para garantizar que los días están cargados antes de calcular.

### Archivo: `w:\MDR\view\Presupuesto\formularioPresupuesto.php`

Se añadió un tooltip informativo al campo `fecha_validez_presupuesto`:

```html
<div class="col-md-3">
    <div class="form-group">
        <label for="fecha_validez_presupuesto">
            Fecha Validez 
            <i class="bi bi-info-circle" 
               data-bs-toggle="tooltip" 
               title="Se calcula automáticamente según los días de validez configurados en la empresa. Puede modificarse."></i>
        </label>
        <input type="date" 
               class="form-control" 
               name="fecha_validez_presupuesto" 
               id="fecha_validez_presupuesto" 
               placeholder="Fecha de Validez">
    </div>
</div>
```

---

## 5. Actualización de Formulario de Empresa

### Archivo: `w:\MDR\view\MntEmpresas\formularioEmpresa.php`

Se añadió un campo HTML para editar los días de validez:

```html
<div class="col-md-6">
    <div class="form-group">
        <label for="dias_validez_presupuesto_empresa">
            Días de Validez de Presupuesto 
            <i class="bi bi-info-circle" 
               data-bs-toggle="tooltip" 
               title="Días de validez por defecto para los presupuestos emitidos por esta empresa"></i>
        </label>
        <input type="number" 
               class="form-control" 
               name="dias_validez_presupuesto_empresa" 
               id="dias_validez_presupuesto_empresa" 
               value="30" 
               min="1" 
               max="365" 
               required>
    </div>
</div>
```

### Archivo: `w:\MDR\view\MntEmpresas\formularioEmpresa.js`

Se actualizó la función `guardaryeditar()` para incluir el nuevo campo en el array de parámetros (posición 22).

### Archivo: `w:\MDR\controller\empresas.php`

Se añadió el campo en las operaciones INSERT y UPDATE con valor por defecto de 30 días:

```php
case "guardaryeditar":
    // ... otros campos ...
    isset($_POST["dias_validez_presupuesto_empresa"]) ? $_POST["dias_validez_presupuesto_empresa"] : 30,
    // ... más campos ...
```

### Archivo: `w:\MDR\models\Empresas.php`

Se actualizaron los métodos `insert_empresa()` y `update_empresa()` para incluir el nuevo parámetro en la posición 22:

```php
public function insert_empresa(/* ... parámetros ... */, 
                                $dias_validez_presupuesto_empresa, /* posición 22 */
                                /* ... más parámetros ... */) {
    $sql = "INSERT INTO empresa (
        /* ... campos ... */
        dias_validez_presupuesto_empresa,
        /* ... más campos ... */
    ) VALUES (
        /* ... bindValue(1) a bindValue(21) ... */
        :dias_validez_presupuesto_empresa,  /* bindValue(22) */
        /* ... más valores ... */
    )";
    
    // ...
    $sql->bindValue(22, $dias_validez_presupuesto_empresa);
    // ...
}
```

---

## 6. Problema Encontrado y Solución Aplicada

### 6.1. Problema Inicial

**Síntoma:** Al crear un nuevo presupuesto, el campo `fecha_validez_presupuesto` mostraba una fecha incorrecta (11/01/2026 en lugar de 14/12/2025), usando el valor por defecto de 30 días en lugar de los 2 días configurados en la empresa ficticia.

**Causa raíz:** **Problema de sincronización asíncrona**

El código inicial usaba `setTimeout()` para intentar esperar a que se cargaran los datos:

```javascript
// ❌ CÓDIGO INCORRECTO (versión inicial)
cargarDiasValidezEmpresa();
setTimeout(function() {
    var fechaValidez = calcularFechaValidez(hoy);
    $('#fecha_validez_presupuesto').val(fechaValidez);
}, 300);
```

**Por qué fallaba:**
1. La petición AJAX a `empresas.php?op=obtenerEmpresaActiva` se iniciaba
2. El `setTimeout(300ms)` esperaba 300 milisegundos
3. **Si la petición AJAX tardaba más de 300ms**, el cálculo se ejecutaba con el valor por defecto (30)
4. La petición AJAX completaba **después**, pero ya era tarde

**Registro de consola que evidenció el problema:**
```
⏳ Cargando días de validez desde empresa...
✓ Fecha de validez calculada automáticamente: 2025-01-11 (+30 días)  ← Usó valor por defecto
✓ Días de validez cargados desde empresa: 2  ← Llegó tarde
```

### 6.2. Solución Implementada: Patrón Promise

Se refactorizó `cargarDiasValidezEmpresa()` para que **retorne una Promise**, garantizando que el cálculo se ejecute **solo después** de obtener los datos:

```javascript
// ✓ CÓDIGO CORRECTO (versión final)
cargarDiasValidezEmpresa().then(function() {
    var fechaValidez = calcularFechaValidez(hoy);
    $('#fecha_validez_presupuesto').val(fechaValidez);
});
```

**Por qué funciona:**
1. `cargarDiasValidezEmpresa()` retorna una Promise de jQuery (`.ajax()`)
2. `.then()` se ejecuta **solo cuando** la promesa se resuelve exitosamente
3. En ese momento, `diasValidezPresupuesto` ya tiene el valor correcto (2 días)
4. `calcularFechaValidez()` usa el valor correcto
5. El campo se rellena con la fecha correcta (14/12/2025)

**Registro de consola con la solución:**
```
⏳ Cargando días de validez desde empresa...
✓ Días de validez cargados desde empresa: 2
✓ Fecha de validez calculada automáticamente: 2025-12-14 (+2 días)  ← Correcto
```

### 6.3. Por Qué No se Usó async/await

Aunque async/await es más moderno y legible, se optó por **Promises con .then()** por las siguientes razones:

1. **Compatibilidad:** El proyecto usa jQuery 3.x y su patrón de Promises es nativo
2. **Consistencia:** El resto del código del proyecto usa callbacks y promises de jQuery
3. **Simplicidad:** No requiere refactorizar funciones existentes a `async`
4. **Sin transpilación:** No es necesario Babel/TypeScript para navegadores antiguos

### 6.4. Patrón Aplicado en Múltiples Lugares

Esta solución se aplicó en **dos lugares** del código:

1. **Inicialización de nuevo presupuesto:**
```javascript
cargarDiasValidezEmpresa().then(function() {
    var fechaValidez = calcularFechaValidez(hoy);
    $('#fecha_validez_presupuesto').val(fechaValidez);
});
```

2. **Listener de cambio de fecha:**
```javascript
$('#fecha_presupuesto').on('change', function() {
    var fechaPresupuesto = $(this).val();
    if (fechaPresupuesto) {
        cargarDiasValidezEmpresa().then(function() {
            var fechaValidez = calcularFechaValidez(fechaPresupuesto);
            $('#fecha_validez_presupuesto').val(fechaValidez);
        });
    }
});
```

---

## 7. Flujo de Datos Completo

```
┌─────────────────────────────────────────────────────────────┐
│ 1. Usuario abre formulario de nuevo presupuesto            │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 2. JavaScript detecta formulario nuevo (sin id_presupuesto)│
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 3. Establece fecha_presupuesto = HOY                        │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 4. Llama a cargarDiasValidezEmpresa()                       │
│    └─> AJAX GET a empresas.php?op=obtenerEmpresaActiva     │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 5. Controller llama a $empresa->get_empresaActiva()         │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 6. Model ejecuta SELECT con dias_validez_presupuesto_empresa│
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 7. Retorna JSON: { "dias_validez_presupuesto_empresa": 2 } │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 8. Promise se resuelve, establece diasValidezPresupuesto=2 │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 9. .then() ejecuta calcularFechaValidez(hoy)                │
│    └─> hoy = "2025-12-12"                                   │
│    └─> hoy + 2 días = "2025-12-14"                          │
└────────────────────────────┬────────────────────────────────┘
                             │
                             ▼
┌─────────────────────────────────────────────────────────────┐
│ 10. Campo fecha_validez_presupuesto = "2025-12-14"          │
└─────────────────────────────────────────────────────────────┘
```

---

## 8. Archivos Modificados

### Base de Datos
- **w:\MDR\BD\claude_MDR** (línea ~3207): ALTER TABLE empresa

### Backend (PHP)
- **w:\MDR\models\Empresas.php**:
  - `get_empresaActiva()`: Añadido campo en SELECT
  - `insert_empresa()`: Añadido parámetro en posición 22
  - `update_empresa()`: Añadido parámetro en posición 22

- **w:\MDR\controller\empresas.php**:
  - Case "guardaryeditar": Añadido campo con default 30

### Frontend (HTML/JS)
- **w:\MDR\view\MntEmpresas\formularioEmpresa.php**: Campo HTML input number
- **w:\MDR\view\MntEmpresas\formularioEmpresa.js**: Actualizado array de parámetros
- **w:\MDR\view\Presupuesto\formularioPresupuesto.php**: Tooltip informativo
- **w:\MDR\view\Presupuesto\formularioPresupuesto.js**: Lógica completa con Promises

---

## 9. Testing y Verificación

### Caso de Prueba 1: Nuevo Presupuesto
**Datos iniciales:**
- Empresa ficticia con `dias_validez_presupuesto_empresa = 2`
- Fecha actual: 12/12/2025

**Pasos:**
1. Navegar a crear nuevo presupuesto
2. Observar que `fecha_presupuesto` = 12/12/2025 (hoy)
3. Observar que `fecha_validez_presupuesto` = 14/12/2025 (hoy + 2 días)

**Resultado esperado:** ✓ **Correcto** (14/12/2025)  
**Resultado anterior:** ✗ **Incorrecto** (11/01/2026 - usaba 30 días)

### Caso de Prueba 2: Cambio de Fecha Manual
**Pasos:**
1. Cambiar `fecha_presupuesto` a 20/12/2025
2. Observar que `fecha_validez_presupuesto` se recalcula automáticamente

**Resultado esperado:** 22/12/2025 (20/12 + 2 días)

### Caso de Prueba 3: Logs de Consola
**Verificar en DevTools:**
```
⏳ Cargando días de validez desde empresa...
✓ Días de validez cargados desde empresa: 2
✓ Fecha de validez calculada automáticamente: 2025-12-14 (+2 días)
```

---

## 10. Consideraciones Futuras

### Optimización Posible
Si el usuario cambia múltiples veces la fecha de presupuesto, cada cambio dispara una nueva petición AJAX. Para optimizar, se podría:

1. **Cachear el valor** en `sessionStorage` o `localStorage`
2. **Cargar una sola vez** al inicio y reutilizar
3. **Implementar debouncing** en el listener

Ejemplo:
```javascript
var diasValidezPresupuesto = sessionStorage.getItem('diasValidezPresupuesto');

if (diasValidezPresupuesto) {
    // Usar valor cacheado
    calcularFechaValidez(hoy);
} else {
    // Cargar desde servidor
    cargarDiasValidezEmpresa().then(...);
}
```

### Validaciones Adicionales
- Validar que `dias_validez_presupuesto_empresa` esté entre 1 y 365
- Mostrar advertencia si el presupuesto tiene validez menor a 7 días
- Permitir configurar diferentes valideces según tipo de cliente

---

## 11. Conclusión

La implementación de esta funcionalidad requirió:

1. **Cambio en BD:** Añadir campo configurable por empresa
2. **Backend PHP:** Incluir campo en queries y métodos
3. **Frontend JS:** Implementar carga asíncrona con **patrón Promise**
4. **Bug fix crítico:** Resolver problema de sincronización cambiando de `setTimeout()` a `.then()`

La solución final garantiza que la fecha de validez se calcula **siempre** con los datos correctos de la empresa, manteniendo flexibilidad para edición manual por el usuario.

**Lección aprendida:** En operaciones asíncronas, usar Promises o async/await en lugar de timeouts arbitrarios garantiza la correcta secuencia de ejecución.

---

## Referencias

- jQuery AJAX Promises: https://api.jquery.com/jquery.ajax/
- JavaScript Promises: https://developer.mozilla.org/en-US/docs/Web/JavaScript/Reference/Global_Objects/Promise
- Patrón Promise vs setTimeout: https://javascript.info/promise-basics
