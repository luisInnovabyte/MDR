# 📄 Documentación: Exportación a PDF/Impresión

> Guía completa para implementar la funcionalidad de exportación a PDF en informes y listados del sistema MDR ERP Manager

---

## 📋 Tabla de Contenidos

1. [Arquitectura de la Solución](#arquitectura)
2. [Archivos Necesarios](#archivos-necesarios)
3. [Implementación Paso a Paso](#implementación-paso-a-paso)
4. [Parámetros Configurables](#parámetros-configurables)
5. [Cómo Pasar Datos](#cómo-pasar-datos)
6. [Ejemplo Completo](#ejemplo-completo)
7. [Troubleshooting](#troubleshooting)

---

## 🏗️ Arquitectura de la Solución {#arquitectura}

La solución de exportación a PDF utiliza una arquitectura simple pero efectiva:

```
┌─────────────────┐
│  Vista (HTML)   │ ← Botón "Exportar PDF"
│   + JavaScript  │
└────────┬────────┘
         │ POST (parámetros)
         ↓
┌─────────────────┐
│  Controller PHP │ ← Procesa y obtiene datos
└────────┬────────┘
         │ Consulta
         ↓
┌─────────────────┐
│   Model PHP     │ ← Obtiene datos de BD
│   (Opcional)    │
└────────┬────────┘
         │ Retorna datos
         ↓
┌─────────────────┐
│  Controller PHP │ ← Genera HTML con estilos
└────────┬────────┘
         │ HTML Response
         ↓
┌─────────────────┐
│ Nueva Ventana   │ ← Muestra HTML imprimible
│  con Botón      │    Usuario usa Ctrl+P
│  "Imprimir"     │    o "Guardar como PDF"
└─────────────────┘
```

**Ventajas de esta solución:**
- ✅ No requiere librerías externas (TCPDF, FPDF, etc.)
- ✅ Usa la funcionalidad nativa del navegador
- ✅ Fácil de mantener y personalizar
- ✅ Compatible con todos los navegadores modernos
- ✅ Permite vista previa antes de imprimir

---

## 📁 Archivos Necesarios {#archivos-necesarios}

### 1️⃣ **Controller** (`controller/nombre_informe.php`)

**Propósito:** Recibe parámetros, obtiene datos y genera HTML imprimible

**Ubicación:** `w:\MDR\controller\informe_[nombre].php`

**Ejemplo:** `w:\MDR\controller\informe_garantias.php`

### 2️⃣ **Vista** (`view/[Modulo]/index.php`)

**Propósito:** Contiene el botón de exportación

**Ubicación:** `w:\MDR\view\[NombreModulo]\index.php`

**Ejemplo:** `w:\MDR\view\Informe_vigencia\index.php`

### 3️⃣ **JavaScript** (`view/[Modulo]/js/archivo.js`)

**Propósito:** Maneja el clic del botón y envía datos al controller

**Ubicación:** `w:\MDR\view\[NombreModulo]\js\[archivo].js`

**Ejemplo:** `w:\MDR\view\Informe_vigencia\js\calendario.js`

### 4️⃣ **Model** (Opcional - `models/NombreEntidad.php`)

**Propósito:** Métodos para obtener datos de la BD

**Ubicación:** `w:\MDR\models\[NombreEntidad].php`

**Ejemplo:** `w:\MDR\models\Elemento.php`

---

## 🔧 Implementación Paso a Paso {#implementación-paso-a-paso}

### **PASO 1: Crear el Controller**

```php
<?php
// Archivo: controller/informe_[nombre].php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/[NombreEntidad].php"; // Opcional

$registro = new RegistroActividad();
$entidad = new [NombreEntidad](); // Opcional si se usa modelo

switch ($_GET["op"]) {
    
    case "generar_pdf":
        try {
            // ========================================
            // PASO 1A: Recibir parámetros
            // ========================================
            $param1 = isset($_POST['param1']) ? $_POST['param1'] : 'default';
            $param2 = isset($_POST['param2']) ? intval($_POST['param2']) : 0;
            
            // ========================================
            // PASO 1B: Obtener datos
            // ========================================
            // OPCIÓN A: Desde modelo
            $datos = $entidad->metodoObtenerDatos($param1, $param2);
            
            // OPCIÓN B: Consulta directa (sin modelo)
            /*
            $conexion = (new Conexion())->getConexion();
            $sql = "SELECT * FROM tabla WHERE campo = ?";
            $stmt = $conexion->prepare($sql);
            $stmt->execute([$param1]);
            $datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
            */
            
            // ========================================
            // PASO 1C: Generar HTML
            // ========================================
            $html = generarHTMLImprimible($datos, $param1, $param2);
            
            echo $html;
            
            // Logging
            $registro->registrarActividad(
                'admin',
                'informe_[nombre].php',
                'generar_pdf',
                "PDF generado - Total registros: " . count($datos),
                'info'
            );
            
        } catch (Exception $e) {
            $registro->registrarActividad(
                'admin',
                'informe_[nombre].php',
                'generar_pdf',
                "Error: " . $e->getMessage(),
                'error'
            );
            
            echo '<h1>Error al generar el informe</h1>';
        }
        break;
}

// ========================================
// FUNCIÓN: Generar HTML Imprimible
// ========================================
function generarHTMLImprimible($datos, $param1, $param2) {
    $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe - [NOMBRE]</title>
    <style>
        /* Estilos para impresión - Ver sección "Estilos CSS" */
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">
        Imprimir / Guardar como PDF
    </button>
    
    <div class="header">
        <h1>Título del Informe</h1>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Columna 1</th>
                <th>Columna 2</th>
            </tr>
        </thead>
        <tbody>';
    
    foreach ($datos as $row) {
        $html .= '
            <tr>
                <td>' . htmlspecialchars($row['campo1']) . '</td>
                <td>' . htmlspecialchars($row['campo2']) . '</td>
            </tr>';
    }
    
    $html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p>Total registros: ' . count($datos) . '</p>
    </div>
</body>
</html>';
    
    return $html;
}
?>
```

---

### **PASO 2: Agregar Botón en la Vista**

```html
<!-- Archivo: view/[Modulo]/index.php -->

<div class="row">
    <div class="col-auto">
        <button type="button" class="btn btn-danger btn-sm" id="btnExportPDF">
            <i class="fas fa-file-pdf me-1"></i>
            Exportar PDF
        </button>
    </div>
</div>
```

**Personalización del botón:**
- `btn-danger` → Color rojo (puedes usar `btn-primary`, `btn-success`, etc.)
- `btn-sm` → Tamaño pequeño (puedes usar `btn-lg` para grande)
- `id="btnExportPDF"` → **IMPORTANTE:** Este ID debe coincidir con el JavaScript

---

### **PASO 3: Agregar JavaScript**

```javascript
// Archivo: view/[Modulo]/js/[archivo].js

// Opción 1: Si tienes una clase/objeto
class MiClase {
    constructor() {
        this.init();
    }
    
    init() {
        this.attachEventListeners();
    }
    
    attachEventListeners() {
        // Agregar event listener al botón
        document.getElementById('btnExportPDF').addEventListener('click', () => {
            this.exportToPDF();
        });
    }
    
    exportToPDF() {
        // Preparar parámetros
        const param1 = 'valor1';
        const param2 = document.getElementById('campo2').value;
        
        // Crear formulario dinámico
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '../../controller/informe_[nombre].php?op=generar_pdf';
        form.target = '_blank'; // Abrir en nueva ventana
        
        // Agregar campos de datos
        const input1 = document.createElement('input');
        input1.type = 'hidden';
        input1.name = 'param1';
        input1.value = param1;
        form.appendChild(input1);
        
        const input2 = document.createElement('input');
        input2.type = 'hidden';
        input2.name = 'param2';
        input2.value = param2;
        form.appendChild(input2);
        
        // Enviar formulario
        document.body.appendChild(form);
        form.submit();
        document.body.removeChild(form);
    }
}

// Opción 2: Función simple sin clase
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('btnExportPDF').addEventListener('click', function() {
        // Código de exportación aquí
    });
});
```

---

## ⚙️ Parámetros Configurables {#parámetros-configurables}

### **1. Parámetros desde JavaScript → Controller**

Los parámetros se pasan mediante **inputs hidden** en un formulario POST:

```javascript
// Ejemplo: Pasar mes, año y filtro
const param1 = 12;                    // Mes
const param2 = 2024;                  // Año
const param3 = 'vigente';             // Filtro de estado

const form = document.createElement('form');
form.method = 'POST';
form.action = '../../controller/informe.php?op=generar_pdf';
form.target = '_blank';

// Agregar cada parámetro
['param1', 'param2', 'param3'].forEach((name, index) => {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = [param1, param2, param3][index];
    form.appendChild(input);
});
```

### **2. Recibir en Controller**

```php
// En controller PHP
$mes = isset($_POST['param1']) ? intval($_POST['param1']) : date('n');
$anio = isset($_POST['param2']) ? intval($_POST['param2']) : date('Y');
$filtro = isset($_POST['param3']) ? $_POST['param3'] : '';
```

### **3. Parámetros Comunes por Tipo de Informe**

| Tipo de Informe | Parámetros Típicos |
|-----------------|-------------------|
| **Por Fecha** | `fecha_inicio`, `fecha_fin`, `mes`, `año` |
| **Por Cliente** | `id_cliente`, `nombre_cliente` |
| **Por Estado** | `id_estado`, `tipo_estado` |
| **Por Ubicación** | `id_ubicacion`, `nave`, `pasillo` |
| **Por Artículo** | `id_articulo`, `id_familia`, `id_categoria` |
| **Sin Filtros** | Solo necesitas `op=generar_pdf` |

---

## 📊 Cómo Pasar Datos {#cómo-pasar-datos}

### **Método 1: Usando Modelo (RECOMENDADO)**

**Ventaja:** Código más limpio, reutilizable y siguiendo MVC

```php
// En models/Elemento.php
public function getReporteGarantias($mes, $anio) {
    try {
        $sql = "SELECT 
                    campo1, campo2, campo3
                FROM tabla
                WHERE MONTH(fecha) = ? AND YEAR(fecha) = ?
                ORDER BY fecha";
        
        $stmt = $this->conexion->prepare($sql);
        $stmt->execute([$mes, $anio]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        return [];
    }
}

// En controller/informe.php
$elemento = new Elemento();
$datos = $elemento->getReporteGarantias($mes, $anio);
```

### **Método 2: Usando Vista SQL**

**Ventaja:** Datos ya consolidados con JOINs

```php
// Crear vista en BD
CREATE OR REPLACE VIEW vista_reporte_garantias AS
SELECT 
    e.id_elemento,
    e.codigo_elemento,
    e.fecha_fin_garantia_elemento,
    a.nombre_articulo,
    f.nombre_familia
FROM elemento e
INNER JOIN articulo a ON e.id_articulo = a.id_articulo
INNER JOIN familia f ON a.id_familia = f.id_familia
WHERE e.activo_elemento = 1;

// En controller
$sql = "SELECT * FROM vista_reporte_garantias 
        WHERE MONTH(fecha_fin_garantia_elemento) = ? 
        AND YEAR(fecha_fin_garantia_elemento) = ?";
$stmt = $conexion->prepare($sql);
$stmt->execute([$mes, $anio]);
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

### **Método 3: Consulta Directa en Controller**

**Ventaja:** Rápido para informes simples y específicos

```php
// En controller/informe.php
$conexion = (new Conexion())->getConexion();

$sql = "SELECT 
            t1.campo1,
            t2.campo2
        FROM tabla1 t1
        INNER JOIN tabla2 t2 ON t1.id = t2.id_tabla1
        WHERE t1.activo = 1
        ORDER BY t1.fecha DESC";

$stmt = $conexion->prepare($sql);
$stmt->execute();
$datos = $stmt->fetchAll(PDO::FETCH_ASSOC);
```

### **Comparación de Métodos**

| Método | Cuándo Usar | Ventajas | Desventajas |
|--------|-------------|----------|-------------|
| **Modelo** | Datos complejos reutilizables | MVC puro, testeable | Más archivos |
| **Vista SQL** | JOINs complejos repetitivos | Performance, claridad | Requiere crear vista |
| **Directa** | Informes únicos y simples | Rápido, todo en un archivo | No reutilizable |

---

## 💡 Ejemplo Completo: Informe de Clientes {#ejemplo-completo}

### **Caso de Uso:** Listar clientes activos por provincia

#### **1. Crear Modelo (Opcional)**

```php
// models/Clientes.php
public function getClientesPorProvincia($provincia = null) {
    try {
        $sql = "SELECT 
                    nombre_cliente,
                    apellido_cliente,
                    email_cliente,
                    telefono_cliente,
                    provincia_cliente
                FROM cliente
                WHERE activo_cliente = 1";
        
        if (!empty($provincia)) {
            $sql .= " AND provincia_cliente = ?";
        }
        
        $sql .= " ORDER BY nombre_cliente ASC";
        
        $stmt = $this->conexion->prepare($sql);
        
        if (!empty($provincia)) {
            $stmt->execute([$provincia]);
        } else {
            $stmt->execute();
        }
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
        
    } catch (PDOException $e) {
        $this->registro->registrarActividad(
            'admin',
            'Clientes',
            'getClientesPorProvincia',
            "Error: " . $e->getMessage(),
            'error'
        );
        return [];
    }
}
```

#### **2. Crear Controller**

```php
<?php
// controller/informe_clientes.php

require_once "../config/conexion.php";
require_once "../config/funciones.php";
require_once "../models/Clientes.php";

$registro = new RegistroActividad();
$clientes = new Clientes();

switch ($_GET["op"]) {
    
    case "generar_pdf":
        try {
            // Recibir parámetros
            $provincia = isset($_POST['provincia']) ? $_POST['provincia'] : null;
            
            // Obtener datos
            $datos = $clientes->getClientesPorProvincia($provincia);
            
            // Generar HTML
            $titulo = empty($provincia) ? "Todos los Clientes" : "Clientes de $provincia";
            
            $html = '<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Informe de Clientes</title>
    <style>
        @page { size: A4; margin: 15mm; }
        @media print { .no-print { display: none !important; } }
        body { font-family: Arial, sans-serif; margin: 20px; }
        .header { text-align: center; border-bottom: 3px solid #0066cc; padding-bottom: 15px; }
        .header h1 { color: #0066cc; margin: 0; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th { background-color: #0066cc; color: white; padding: 10px; text-align: left; }
        td { padding: 8px; border-bottom: 1px solid #ddd; }
        tr:nth-child(even) { background-color: #f9f9f9; }
        .footer { margin-top: 30px; text-align: center; font-size: 11px; border-top: 2px solid #ddd; padding-top: 15px; }
        .print-button { position: fixed; top: 20px; right: 20px; padding: 12px 24px; background-color: #dc3545; color: white; border: none; border-radius: 5px; cursor: pointer; }
    </style>
</head>
<body>
    <button class="print-button no-print" onclick="window.print()">Imprimir / Guardar como PDF</button>
    
    <div class="header">
        <h1>Informe de Clientes</h1>
        <p>' . $titulo . '</p>
        <p>Generado el ' . date('d/m/Y H:i') . '</p>
    </div>
    
    <table>
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Email</th>
                <th>Teléfono</th>
                <th>Provincia</th>
            </tr>
        </thead>
        <tbody>';
            
            foreach ($datos as $row) {
                $html .= '
            <tr>
                <td>' . htmlspecialchars($row['nombre_cliente'] . ' ' . $row['apellido_cliente']) . '</td>
                <td>' . htmlspecialchars($row['email_cliente'] ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($row['telefono_cliente'] ?? 'N/A') . '</td>
                <td>' . htmlspecialchars($row['provincia_cliente'] ?? 'N/A') . '</td>
            </tr>';
            }
            
            $html .= '
        </tbody>
    </table>
    
    <div class="footer">
        <p><strong>MDR ERP Manager</strong></p>
        <p>Total de registros: ' . count($datos) . '</p>
    </div>
</body>
</html>';
            
            echo $html;
            
            $registro->registrarActividad(
                'admin',
                'informe_clientes.php',
                'generar_pdf',
                "PDF generado - Total: " . count($datos),
                'info'
            );
            
        } catch (Exception $e) {
            echo '<h1>Error al generar el informe</h1>';
        }
        break;
}
?>
```

#### **3. Agregar Botón en Vista**

```html
<!-- view/MntClientes/index.php -->

<div class="row mb-3">
    <div class="col-12">
        <select class="form-select" id="filtroProvincia">
            <option value="">Todas las provincias</option>
            <option value="Madrid">Madrid</option>
            <option value="Barcelona">Barcelona</option>
            <option value="Valencia">Valencia</option>
        </select>
    </div>
</div>

<button type="button" class="btn btn-danger btn-sm" id="btnExportPDF">
    <i class="fas fa-file-pdf me-1"></i>
    Exportar PDF
</button>
```

#### **4. Agregar JavaScript**

```javascript
// view/MntClientes/js/clientes.js

document.getElementById('btnExportPDF').addEventListener('click', function() {
    const provincia = document.getElementById('filtroProvincia').value;
    
    const btnExport = this;
    const originalHTML = btnExport.innerHTML;
    btnExport.disabled = true;
    btnExport.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Generando...';
    
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = '../../controller/informe_clientes.php?op=generar_pdf';
    form.target = '_blank';
    
    const provinciaInput = document.createElement('input');
    provinciaInput.type = 'hidden';
    provinciaInput.name = 'provincia';
    provinciaInput.value = provincia;
    form.appendChild(provinciaInput);
    
    document.body.appendChild(form);
    form.submit();
    document.body.removeChild(form);
    
    setTimeout(() => {
        btnExport.disabled = false;
        btnExport.innerHTML = originalHTML;
    }, 1000);
});
```

---

## 🎨 Estilos CSS Estándar para Impresión

```css
/* Estilos base para PDF/Impresión */

/* Configuración de página */
@page {
    size: A4;           /* Tamaño de papel */
    margin: 15mm;       /* Márgenes */
}

/* Ocultar elementos en impresión */
@media print {
    .no-print {
        display: none !important;
    }
    body {
        margin: 0;
        padding: 0;
    }
}

/* Estilos generales */
body {
    font-family: Arial, sans-serif;
    margin: 20px;
    font-size: 12px;
}

/* Cabecera */
.header {
    text-align: center;
    margin-bottom: 30px;
    border-bottom: 3px solid #0066cc;
    padding-bottom: 15px;
}

.header h1 {
    color: #0066cc;
    margin: 0 0 10px 0;
    font-size: 28px;
}

/* Botón de impresión (oculto en impresión) */
.print-button {
    position: fixed;
    top: 20px;
    right: 20px;
    padding: 12px 24px;
    background-color: #dc3545;
    color: white;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-size: 14px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    z-index: 1000;
}

.print-button:hover {
    background-color: #c82333;
}

/* Tablas */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

th {
    background-color: #0066cc;
    color: white;
    padding: 12px 8px;
    text-align: left;
    font-weight: bold;
    font-size: 12px;
}

td {
    padding: 10px 8px;
    border-bottom: 1px solid #ddd;
    font-size: 11px;
}

tr:nth-child(even) {
    background-color: #f9f9f9;
}

tr:hover {
    background-color: #f0f0f0;
}

/* Pie de página */
.footer {
    margin-top: 30px;
    text-align: center;
    font-size: 11px;
    color: #666;
    border-top: 2px solid #ddd;
    padding-top: 15px;
}

/* Sin datos */
.no-data {
    text-align: center;
    padding: 60px 20px;
    color: #666;
    font-style: italic;
    font-size: 16px;
}
```

---

## 🔍 Troubleshooting {#troubleshooting}

### **Problema 1: La ventana no se abre**

**Síntomas:** Al hacer clic en "Exportar PDF" no pasa nada

**Soluciones:**
```javascript
// 1. Verificar que el ID del botón coincida
document.getElementById('btnExportPDF') // ¿Existe este ID en HTML?

// 2. Verificar bloqueo de pop-ups
form.target = '_blank'; // El navegador puede bloquearlo

// 3. Alternativa sin pop-up
form.target = '_self'; // Abre en la misma ventana (no recomendado)
```

### **Problema 2: No se reciben los parámetros en PHP**

**Síntomas:** `$_POST['parametro']` está vacío

**Soluciones:**
```php
// 1. Verificar que el método sea POST
var_dump($_POST); // Debugging

// 2. Verificar nombres de campos
// JavaScript: input.name = 'mes';
// PHP: $_POST['mes'] (debe coincidir exactamente)

// 3. Sanitizar con isset
$mes = isset($_POST['mes']) ? intval($_POST['mes']) : 1;
```

### **Problema 3: Los estilos no se aplican al imprimir**

**Síntomas:** En pantalla se ve bien, pero al imprimir pierde formato

**Soluciones:**
```css
/* 1. Usar @media print */
@media print {
    /* Estilos específicos para impresión */
}

/* 2. Evitar position: fixed (excepto para .no-print) */
/* 3. Usar colores oscuros (#000, #333) para textos */
/* 4. No usar background-image (se pierden en impresión) */
```

### **Problema 4: El PDF tiene muchas páginas en blanco**

**Síntomas:** Se generan páginas vacías al final

**Soluciones:**
```css
/* 1. Configurar página correctamente */
@page {
    size: A4;
    margin: 15mm;
}

/* 2. Evitar elementos muy altos */
table {
    page-break-inside: auto;
}

tr {
    page-break-inside: avoid;
    page-break-after: auto;
}
```

### **Problema 5: Los datos no aparecen**

**Síntomas:** La página se genera pero la tabla está vacía

**Soluciones:**
```php
// 1. Verificar consulta SQL
var_dump($datos); // Antes de generar HTML

// 2. Verificar que hay datos
if (count($datos) > 0) {
    // Generar tabla
} else {
    echo '<p>No hay datos</p>';
}

// 3. Revisar errores PDO
try {
    // consulta
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
```

---

## 📌 Checklist de Implementación

Usa esta lista para verificar que todo está correcto:

### **Controller**
- [ ] Archivo creado en `controller/informe_[nombre].php`
- [ ] Switch con case `"generar_pdf"`
- [ ] Parámetros recibidos con `$_POST`
- [ ] Datos obtenidos (modelo, vista o consulta)
- [ ] HTML generado con estilos
- [ ] Try-catch implementado
- [ ] Logging de actividad agregado

### **Vista**
- [ ] Botón agregado con ID único
- [ ] Clase Bootstrap aplicada
- [ ] Icono Font Awesome agregado
- [ ] JavaScript referenciado

### **JavaScript**
- [ ] Event listener agregado al botón
- [ ] Formulario dinámico creado
- [ ] Parámetros agregados como inputs hidden
- [ ] Form.target = '_blank'
- [ ] Form.submit() ejecutado
- [ ] Feedback visual (spinner) implementado

### **Modelo (Opcional)**
- [ ] Método público creado
- [ ] Prepared statements usados
- [ ] Try-catch implementado
- [ ] Retorno consistente

---

## 🎯 Casos de Uso Comunes

### **1. Informe sin filtros (todo)**

```javascript
// JavaScript - sin parámetros
form.action = '../../controller/informe.php?op=generar_pdf';
// No agregar inputs, solo submit

// PHP - obtener todo
$datos = $modelo->getAll();
```

### **2. Informe por rango de fechas**

```javascript
// JavaScript
const fechaInicio = document.getElementById('fechaInicio').value;
const fechaFin = document.getElementById('fechaFin').value;

const input1 = document.createElement('input');
input1.name = 'fecha_inicio';
input1.value = fechaInicio;

const input2 = document.createElement('input');
input2.name = 'fecha_fin';
input2.value = fechaFin;

// PHP
$fechaInicio = $_POST['fecha_inicio'];
$fechaFin = $_POST['fecha_fin'];
$datos = $modelo->getPorRangoFechas($fechaInicio, $fechaFin);
```

### **3. Informe con múltiples filtros**

```javascript
// JavaScript
const filtros = {
    provincia: document.getElementById('provincia').value,
    estado: document.getElementById('estado').value,
    tipo: document.getElementById('tipo').value
};

Object.keys(filtros).forEach(key => {
    const input = document.createElement('input');
    input.name = key;
    input.value = filtros[key];
    form.appendChild(input);
});

// PHP
$provincia = $_POST['provincia'] ?? '';
$estado = $_POST['estado'] ?? '';
$tipo = $_POST['tipo'] ?? '';
$datos = $modelo->getFiltrado($provincia, $estado, $tipo);
```

---

## 📚 Referencias Adicionales

- **CSS Print:** https://developer.mozilla.org/es/docs/Web/CSS/@page
- **Window.print():** https://developer.mozilla.org/es/docs/Web/API/Window/print
- **PHP PDO:** https://www.php.net/manual/es/book.pdo.php
- **Bootstrap Buttons:** https://getbootstrap.com/docs/5.0/components/buttons/

---

## ✨ Mejoras Opcionales

### **1. Agregar logo de empresa**

```html
<div class="header">
    <img src="../../public/img/logo.png" alt="Logo" style="max-width: 150px;">
    <h1>Informe</h1>
</div>
```

### **2. Orientación horizontal (landscape)**

```css
@page {
    size: A4 landscape;
    margin: 15mm;
}
```

### **3. Numeración de páginas**

```css
@page {
    @bottom-center {
        content: "Página " counter(page) " de " counter(pages);
    }
}
```

### **4. Exportar en Excel (alternativa)**

```php
// Cambiar header a CSV
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="informe.csv"');

// Generar CSV en lugar de HTML
echo "Campo1,Campo2,Campo3\n";
foreach ($datos as $row) {
    echo '"' . $row['campo1'] . '","' . $row['campo2'] . '","' . $row['campo3'] . "\"\n";
}
```

---

**Última actualización:** 20 de diciembre de 2025  
**Versión:** 1.0  
**Autor:** Luis - Innovabyte  
**Proyecto:** MDR ERP Manager
