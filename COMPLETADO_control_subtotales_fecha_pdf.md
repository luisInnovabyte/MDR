# ✅ COMPLETADO: Control de Subtotales por Fecha en PDF

**Fecha:** 13 de febrero de 2026  
**Descripción:** Implementación de campo booleano para controlar la visualización de subtotales por fecha en PDF de presupuestos

---

## 📋 Cambios Implementados

### 1. **Base de Datos** ✅
**Archivo:** `BD/migrations/add_mostrar_subtotales_fecha_presupuesto.sql`

```sql
ALTER TABLE empresa
ADD COLUMN mostrar_subtotales_fecha_presupuesto_empresa BOOLEAN DEFAULT TRUE 
COMMENT 'Controla si se muestran subtotales por fecha en PDF de presupuestos. TRUE=mostrar, FALSE=ocultar';
```

**Por defecto:** `TRUE` (mostrar subtotales) - mantiene comportamiento existente

---

### 2. **Modelo: Empresas.php** ✅

#### Método `insert_empresa()`
- ✅ Agregado parámetro `$mostrar_subtotales_fecha_presupuesto_empresa` (parámetro 45)
- ✅ Incluido campo en SQL INSERT
- ✅ Agregado `bindValue()` con `PDO::PARAM_BOOL`

#### Método `update_empresa()`
- ✅ Agregado parámetro `$mostrar_subtotales_fecha_presupuesto_empresa` (parámetro 45)
- ✅ Incluido campo en SQL UPDATE
- ✅ Agregado `bindValue()` con `PDO::PARAM_BOOL` (posición 45, id_empresa pasa a posición 46)

---

### 3. **Controller: empresas.php** ✅

#### Case `"guardaryeditar"` - INSERT
```php
isset($_POST["mostrar_subtotales_fecha_presupuesto_empresa"]) ? 1 : 0
```

#### Case `"guardaryeditar"` - UPDATE
```php
isset($_POST["mostrar_subtotales_fecha_presupuesto_empresa"]) ? 1 : 0
```

---

### 4. **Vista: formularioEmpresa.php** ✅

**Nueva sección añadida entre "Observaciones por Defecto" y "Estado de la Empresa":**

```html
<!-- SECCIÓN: Configuración de PDF -->
<div class="card shadow-sm mb-4">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0">
            <i class="bi bi-file-pdf me-2"></i>Configuración de PDF de Presupuestos
        </h5>
    </div>
    <div class="card-body">
        <div class="form-check">
            <input type="checkbox" 
                   class="form-check-input" 
                   id="mostrar_subtotales_fecha_presupuesto_empresa"
                   name="mostrar_subtotales_fecha_presupuesto_empresa"
                   value="1"
                   checked>
            <label class="form-check-label">
                <strong>Mostrar subtotales por fecha en PDF</strong>
            </label>
        </div>
        <small class="text-muted d-block mt-2">
            Al desmarcar esta opción, se ocultarán las líneas de "Subtotal Fecha XX/XX/XXXX"
        </small>
    </div>
</div>
```

---

### 5. **JavaScript: formularioEmpresa.js** ✅

**Carga de datos al editar:**
```javascript
// Configuración PDF - Subtotales por fecha
$('#mostrar_subtotales_fecha_presupuesto_empresa').prop('checked', 
    data.mostrar_subtotales_fecha_presupuesto_empresa == 1
);
```

---

### 6. **PDF: impresionpresupuesto_m2_pdf_es.php** ✅

#### Inicialización de variable (línea ~512)
```php
// Configuración de subtotales por fecha (por defecto TRUE si no existe el campo)
$mostrar_subtotales_fecha = isset($datos_empresa['mostrar_subtotales_fecha_presupuesto_empresa']) 
    ? (bool)$datos_empresa['mostrar_subtotales_fecha_presupuesto_empresa'] 
    : true;
```

#### Renderizado condicional (línea ~865)
```php
// Subtotal por fecha - SOLO SI ESTÁ HABILITADO
if ($mostrar_subtotales_fecha) {
    $pdf->SetFont('helvetica', 'B', 8);
    $pdf->SetFillColor(220, 220, 220);
    $pdf->Cell(170, 6, 'Subtotal Fecha ' . $fecha_formateada, 1, 0, 'R', 1);
    $pdf->Cell(24, 6, number_format($grupo_fecha['subtotal_fecha'], 2, ',', '.'), 1, 1, 'R', 1);
    $pdf->Ln(3);
} else {
    // Sin subtotal, solo un pequeño espacio visual
    $pdf->Ln(2);
}
```

---

## 🧪 Testing

### Pasos para verificar:

1. **Migración SQL**
   ```sql
   -- Ejecutar en base de datos
   source W:/MDR/BD/migrations/add_mostrar_subtotales_fecha_presupuesto.sql
   
   -- Verificar
   DESCRIBE empresa;
   ```

2. **Interfaz de Empresa**
   - Ir a gestión de empresas
   - Crear nueva empresa → Checkbox debe estar **marcado** por defecto
   - Editar empresa existente → Checkbox debe reflejar valor de BD
   - Desmarcar y guardar → Verificar que se guarda como 0

3. **Generación PDF**
   - **Con checkbox MARCADO:**
     - Generar PDF presupuesto
     - Verificar que aparece línea "Subtotal Fecha XX/XX/XXXX"
   
   - **Con checkbox DESMARCADO:**
     - Generar PDF presupuesto
     - Verificar que NO aparece línea de subtotal
     - Espaciado debe ser correcto

4. **Backward compatibility**
   - Empresas existentes sin valor en el campo → Deben mostrar subtotales (default TRUE)

---

## 📦 Archivos Modificados

1. ✅ `BD/migrations/add_mostrar_subtotales_fecha_presupuesto.sql` (NUEVO)
2. ✅ `models/Empresas.php`
3. ✅ `controller/empresas.php`
4. ✅ `view/MntEmpresas/formularioEmpresa.php`
5. ✅ `view/MntEmpresas/formularioEmpresa.js`
6. ✅ `controller/impresionpresupuesto_m2_pdf_es.php`

---

## 🎯 Resultado Esperado

### Checkbox MARCADO (valor=1):
```
────────────────────────────────────────
Ubicación 1: Sala Principal
  MESA-001    Mesa redonda 150cm    50€
  SILLA-002   Silla apilable       10€
────────────────────────────────────────
Subtotal Ubicación 1              60,00€
────────────────────────────────────────
Subtotal Fecha 27/01/2026      1.605,00€  ← SE MUESTRA
────────────────────────────────────────
```

### Checkbox DESMARCADO (valor=0):
```
────────────────────────────────────────
Ubicación 1: Sala Principal
  MESA-001    Mesa redonda 150cm    50€
  SILLA-002   Silla apilable       10€
────────────────────────────────────────
Subtotal Ubicación 1              60,00€
────────────────────────────────────────
                                         ← NO SE MUESTRA (solo espacio)
────────────────────────────────────────
```

---

## 🔄 Patrón Seguido

Implementación basada en el patrón de `observaciones_cabecera_presupuesto_empresa` según documentado en `COMPLETADO_seccion7_observaciones_cabecera.md`:

- ✅ Campo booleano con valor por defecto
- ✅ Modificación de modelo (insert/update)
- ✅ Sanitización en controller
- ✅ Checkbox en interfaz con Bootstrap 5
- ✅ JavaScript para carga de valores
- ✅ Uso del campo en generación de PDF

---

**Estado:** 🟢 COMPLETADO  
**Requiere ejecución de migración SQL:** ✅ SÍ  
**Branch sugerida:** `cam_presupuesto`
