# Correcciones y Mejoras al Prompt de Automatización
## Sistema Cabecera-Pies para Módulos de Mantenimiento

> **Fecha**: 23 de diciembre de 2025  
> **Módulo de Prueba**: MntFurgonetas  
> **Propósito**: Documentar problemas encontrados y correcciones aplicadas para mejorar el prompt de automatización

---

## 📋 Resumen Ejecutivo

Durante la implementación del módulo **MntFurgonetas** usando el prompt de automatización original, se identificaron **7 problemas críticos** que deben ser incorporados al prompt para evitar su repetición en futuras implementaciones:

1. **Falta de etiqueta PHP** (`<?php`) en archivos PHP
2. **Módulo de permisos incorrecto** (nombre específico vs 'mantenimientos')
3. **CSS personalizado innecesario** para botón details-control
4. **Formato de fechas americano** (YYYY-MM-DD vs DD/MM/YYYY)
5. **Ausencia de sistema de ayuda contextual** (modales informativos)
6. **Tamaño de fuente insuficiente** (falta clase fs-6 en badges)
7. **Botón Guardar no funcional** (event binding, flujo de validación y posicionamiento incorrectos)

---

## ❌ PROBLEMA 1: Falta de Etiqueta PHP de Apertura

### Descripción
El archivo `index.php` generado **no incluía la etiqueta de apertura `<?php`** en la primera línea, causando que:
- El código PHP no se ejecutara
- Las estadísticas no se cargaran
- Las variables quedaran sin inicializar

### Código Incorrecto Generado
```php

// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'furgonetas';
require_once('../../config/template/verificarPermiso.php');
```

### Código Correcto
```php
<?php
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'mantenimientos';
require_once('../../config/template/verificarPermiso.php');
```

### ✅ Corrección a Aplicar en el Prompt
**AGREGAR AL INICIO DEL PROMPT:**

```markdown
## ⚠️ CRÍTICO: Etiqueta PHP de Apertura

**TODOS los archivos PHP DEBEN comenzar con `<?php` en la primera línea.**

Ejemplo correcto para index.php:
```php
<?php
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'mantenimientos';
```

**NUNCA generar un archivo PHP sin la etiqueta de apertura.**
```

---

## ❌ PROBLEMA 2: Módulo de Permisos Incorrecto

### Descripción
El archivo `index.php` y `formularioFurgoneta.php` usaban `$moduloActual = 'furgonetas'`, un módulo que **no existe** en el sistema de permisos, causando:
- Error "Acceso Denegado"
- Imposibilidad de acceder al módulo incluso con permisos de administrador

### Sistema de Permisos en `verificarPermiso.php`
```php
$permisosPorRol = [
    2 => ['usuarios', 'logs', 'mantenimientos', 'llamadas', 'dashboard', ...], // Gestor
    3 => ['usuarios', 'logs', 'mantenimientos', 'comerciales', 'llamadas', ...], // Administrador
    4 => ['llamadas', 'mantenimientos', 'dashboard'], // Comercial
    5 => ['area_tecnica', 'elementos_consulta', ...], // Técnico
];
```

### Código Incorrecto Generado
```php
<?php
$moduloActual = 'furgonetas';  // ❌ Este módulo NO existe
require_once('../../config/template/verificarPermiso.php');
```

### Código Correcto
```php
<?php
$moduloActual = 'mantenimientos';  // ✅ Módulo existente
require_once('../../config/template/verificarPermiso.php');
```

### Módulos de Referencia
Todos los módulos Mnt* usan `'mantenimientos'`:
- ✅ MntArticulos → `'mantenimientos'`
- ✅ MntClientes → `'usuarios'`
- ✅ MntEmpresas → `'mantenimientos'`
- ✅ MntImpuesto → `'mantenimientos'`
- ✅ MntUnidad → `'mantenimientos'`
- ✅ MntFormas_Pago → `'mantenimientos'`
- ✅ MntContactos → `'mantenimientos'`

### ✅ Corrección a Aplicar en el Prompt

**AGREGAR SECCIÓN DE PERMISOS:**

```markdown
## 🔐 CONFIGURACIÓN DE PERMISOS

**Para TODOS los módulos de tipo Mnt* (Mantenimientos):**

```php
<?php
// ----------------------
//   Comprobar permisos
// ----------------------
$moduloActual = 'mantenimientos';  // ← SIEMPRE usar 'mantenimientos' para módulos Mnt*
require_once('../../config/template/verificarPermiso.php');
```

**Módulos disponibles en el sistema:**
- `'usuarios'` - Gestión de usuarios y algunos mantenimientos básicos
- `'mantenimientos'` - Todos los módulos Mnt* (Artículos, Clientes, Empresas, etc.)
- `'comerciales'` - Área comercial
- `'llamadas'` - Gestión de llamadas
- `'dashboard'` - Panel principal
- `'area_tecnica'` - Área técnica
- `'elementos_consulta'` - Consulta de elementos
- `'logs'` - Registros del sistema

**Esta configuración debe aplicarse a:**
1. ✅ `index.php` - En las primeras líneas
2. ✅ `formularioNombreEntidad.php` - Antes de validar parámetros GET
```

---

## ❌ PROBLEMA 3: Estilos CSS del Botón Details-Control

### Descripción
El prompt generaba estilos CSS personalizados para el botón de expandir/contraer (`details-control`) que:
- Sobreescribían los estilos nativos de DataTables
- Causaban inconsistencias visuales
- No eran necesarios

### Código Incorrecto Generado (en JS)
```javascript
$(document).ready(function () {
    if (!document.getElementById("furgoneta-styles")) {
        const style = document.createElement("style");
        style.id = "furgoneta-styles";
        style.textContent = `
            .details-control {
                cursor: pointer;
            }
            .details-control:before {
                content: '+';
                display: inline-block;
                width: 20px;
                height: 20px;
                // ... más CSS innecesario
            }
            tr.shown .details-control:before {
                content: '-';
                background-color: #dc3545;
            }
        `;
        document.head.appendChild(style);
    }
    // ...
});
```

### Código Correcto
**DataTables ya incluye sus propios estilos para `details-control`**, por lo tanto:

```javascript
$(document).ready(function () {
    // ==========================================
    // 1. ESTILOS CSS DINÁMICOS (solo para modales)
    // ==========================================
    if (!document.getElementById("entidad-styles")) {
        const style = document.createElement("style");
        style.id = "entidad-styles";
        style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    // NO incluir estilos para .details-control
    // DataTables ya los proporciona
```

### ✅ Corrección a Aplicar en el Prompt

**MODIFICAR LA SECCIÓN DE ESTILOS EN EL JS:**

```markdown
## 🎨 Estilos CSS en el JavaScript

**SOLO incluir estilos para modales personalizados:**

```javascript
$(document).ready(function () {
    // Estilos CSS dinámicos (SOLO para modales)
    if (!document.getElementById("<<entidad>>-styles")) {
        const style = document.createElement("style");
        style.id = "<<entidad>>-styles";
        style.textContent = `
            .swal-wide {
                max-width: 90% !important;
                width: auto !important;
            }
        `;
        document.head.appendChild(style);
    }
    
    // ... resto del código
});
```

**⚠️ NO INCLUIR:**
- ❌ Estilos para `.details-control`
- ❌ Estilos para botones de expandir/contraer
- ❌ Estilos que ya proporciona DataTables o Bootstrap

**DataTables ya incluye:**
- ✅ Iconos de expandir/contraer
- ✅ Estilos hover y active
- ✅ Animaciones de transición
```

---

## ❌ PROBLEMA 4: Formato de Fechas Americano

### Descripción
Las fechas se mostraban en formato **americano (YYYY-MM-DD)** en lugar del formato **europeo (DD/MM/YYYY)**, causando:
- Confusión para usuarios españoles
- Inconsistencia con el resto del sistema
- Dificultad para interpretar fechas rápidamente

### Ubicaciones Afectadas
- **DataTables**: Columnas de ITV y vencimiento de seguro
- **Child Rows**: Detalles expandibles con fechas
- **Formulario**: Campos de fecha (mantiene input type="date" nativo)

### Código Incorrecto Generado
```javascript
// En el render de DataTables
render: function (data, type, row) {
    if (type === "display") {
        // ...
        return '<span class="badge ' + badgeClass + '">' + data + '</span>';
        // ❌ Muestra: 2025-12-31
    }
    return data;
}

// En child rows
<td class="pe-4">
    ${d.fecha_proxima_itv_furgoneta || '<span class="text-muted">...</span>'}
    // ❌ Muestra: 2025-12-31
</td>
```

### Código Correcto

**1. Función de utilidad (inicio del documento ready):**
```javascript
$(document).ready(function () {
    // ==========================================
    // 0. FUNCIÓN DE UTILIDAD PARA FECHAS
    // ==========================================
    /**
     * Convierte fecha de formato YYYY-MM-DD a DD/MM/YYYY
     * @param {string} fecha - Fecha en formato ISO (YYYY-MM-DD)
     * @returns {string} Fecha en formato europeo (DD/MM/YYYY)
     */
    function formatearFechaEuropea(fecha) {
        if (!fecha || fecha === '0000-00-00') {
            return null;
        }
        const partes = fecha.split('-');
        if (partes.length === 3) {
            return partes[2] + '/' + partes[1] + '/' + partes[0];
        }
        return fecha;
    }
    
    // ... resto del código
```

**2. Uso en renders de DataTables:**
```javascript
render: function (data, type, row) {
    if (type === "display") {
        if (!data) {
            return '<span class="text-muted fst-italic">Sin fecha</span>';
        }
        
        // Lógica de colores según vencimiento
        const fecha = new Date(data);
        const hoy = new Date();
        const diasDiferencia = Math.ceil((fecha - hoy) / (1000 * 60 * 60 * 24));
        
        let badgeClass = "bg-success";
        if (diasDiferencia < 0) {
            badgeClass = "bg-danger";
        } else if (diasDiferencia <= 30) {
            badgeClass = "bg-warning";
        }
        
        // ✅ Convertir a formato europeo
        const fechaEuropea = formatearFechaEuropea(data);
        return '<span class="badge ' + badgeClass + '">' + fechaEuropea + '</span>';
        // ✅ Muestra: 31/12/2025
    }
    return data;
}
```

**3. Uso en child rows:**
```javascript
<tr>
    <th scope="row" class="ps-4 w-25">
        <i class="bi bi-calendar-check me-2"></i>Próxima ITV
    </th>
    <td class="pe-4">
        ${d.fecha_proxima_itv_furgoneta ? formatearFechaEuropea(d.fecha_proxima_itv_furgoneta) : '<span class="text-muted fst-italic">No especificada</span>'}
        <!-- ✅ Muestra: 31/12/2025 -->
    </td>
</tr>
```

### ✅ Corrección a Aplicar en el Prompt

**AGREGAR SECCIÓN DE FORMATO DE FECHAS:**

```markdown
## 📅 FORMATO DE FECHAS EUROPEO

**Todas las fechas deben mostrarse en formato DD/MM/YYYY para usuarios españoles.**

### 1. Función de Utilidad Obligatoria

Agregar al INICIO del `$(document).ready()` en `mntentidad.js`:

\`\`\`javascript
/**
 * Convierte fecha de formato YYYY-MM-DD a DD/MM/YYYY
 * @param {string} fecha - Fecha en formato ISO (YYYY-MM-DD)
 * @returns {string} Fecha en formato europeo (DD/MM/YYYY)
 */
function formatearFechaEuropea(fecha) {
    if (!fecha || fecha === '0000-00-00') {
        return null;
    }
    const partes = fecha.split('-');
    if (partes.length === 3) {
        return partes[2] + '/' + partes[1] + '/' + partes[0];
    }
    return fecha;
}
\`\`\`

### 2. Uso en DataTables

Para TODAS las columnas de tipo fecha:

\`\`\`javascript
render: function (data, type, row) {
    if (type === "display") {
        if (!data) {
            return '<span class="text-muted fst-italic">Sin fecha</span>';
        }
        
        // Lógica de negocio (colores, cálculos, etc.)
        // ...
        
        // SIEMPRE convertir antes de mostrar
        const fechaEuropea = formatearFechaEuropea(data);
        return '<span class="badge ' + badgeClass + '">' + fechaEuropea + '</span>';
    }
    return data; // Para sorting y filtering usar formato ISO
}
\`\`\`

### 3. Uso en Child Rows

Para TODOS los campos de fecha en child rows:

\`\`\`javascript
<td class="pe-4">
    \${d.campo_fecha ? formatearFechaEuropea(d.campo_fecha) : '<span class="text-muted fst-italic">No especificada</span>'}
</td>
\`\`\`

### 4. Inputs de Fecha (Formularios)

Los inputs `type="date"` mantienen formato ISO internamente pero muestran según configuración del navegador:

\`\`\`html
<!-- Correcto: type="date" para inputs nativos -->
<input type="date" 
       class="form-control" 
       name="fecha_campo" 
       id="fecha_campo">
\`\`\`

⚠️ **NO intentar formatear los valores de inputs type="date"**, el navegador lo maneja automáticamente.

### 5. Checklist de Fechas

- [ ] Función `formatearFechaEuropea()` al inicio del JS
- [ ] Todos los renders de columnas fecha usan la función
- [ ] Todos los child rows con fechas usan la función
- [ ] Inputs del formulario usan `type="date"` sin modificar
- [ ] NO se formatea el valor en `type="sort"` o `type="filter"`
\`\`\`

---

## 📝 CHECKLIST PARA VALIDACIÓN POST-GENERACIÓN

Después de generar los archivos con el prompt, verificar:

### ✅ Archivo: `index.php`
- [ ] Primera línea es `<?php`
- [ ] Variable `$moduloActual = 'mantenimientos';`
- [ ] Se incluye `verificarPermiso.php`
- [ ] NO hay estilos CSS para `.details-control`
- [ ] Estadísticas se cargan correctamente en el try-catch

### ✅ Archivo: `formularioNombreEntidad.php`
- [ ] Primera línea es `<?php`
- [ ] Variable `$moduloActual = 'mantenimientos';`
- [ ] Se incluye `verificarPermiso.php` ANTES de validar GET
- [ ] Validación de parámetros GET correcta

### ✅ Archivo: `mntentidad.js`
- [ ] Solo estilos para modales (`.swal-wide`)
- [ ] NO incluye estilos para `.details-control`
- [ ] Configuración DataTables correcta
- [ ] Evento click en `td.details-control` funciona

### ✅ Archivo: `formularioEntidad.js`
- [ ] FormValidator configurado
- [ ] Detección de modo (nuevo/editar) correcta
- [ ] Advertencia de cambios sin guardar funciona
- [ ] Redirección a index.php tras guardar

---

## ❌ PROBLEMA 5: Ausencia de Sistema de Ayuda Contextual

### Descripción
Los módulos generados **no incluían sistema de ayuda para los usuarios**, causando:
- Falta de documentación contextual sobre funcionalidades
- Usuarios sin guía sobre campos obligatorios
- Ausencia de explicaciones sobre estados y alertas
- No hay referencia sobre el uso de filtros y búsquedas

### Archivos Necesarios
```
view/MntEntidad/
  ├── ayudaEntidad.php          ← Modal de ayuda del módulo (NUEVO)
  ├── index.php                 ← Incluye botón y referencia
  └── formularioEntidad.php     ← Incluye botón y modal embebido
```

### ✅ Corrección Aplicada

**1. Archivo `ayudaEntidad.php` creado con:**
- Modal completo con ID único: `#modalAyudaEntidad`
- Header con gradiente y título descriptivo
- Body organizado en 2 columnas (funciones + información específica)
- Secciones: Funciones principales, Filtros, Estados, Datos, Alertas, Consejos
- Footer con botón "Entendido"
- Estilos CSS personalizados para hover effects

**2. Botón de ayuda en `index.php`:**
```php
<div class="br-pagetitle d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <i class="bi bi-[icono] tx-50 lh-0"></i>
        <div class="d-inline-block align-middle">
            <h4 class="mg-b-0">Gestión de [Entidad]</h4>
            <p class="mg-b-0 tx-gray-600">Descripción</p>
        </div>
        <!-- ✅ BOTÓN DE AYUDA -->
        <button type="button" class="btn btn-link p-0 ms-2" 
                data-bs-toggle="modal" 
                data-bs-target="#modalAyudaEntidad" 
                title="Ayuda sobre el módulo">
            <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
        </button>
    </div>
</div>
```

**3. Include al final de `index.php` (antes de scripts):**
```php
<!-- Modal de Ayuda -->
<?php include_once('ayudaEntidad.php') ?>
```

**4. Botón de ayuda en `formularioEntidad.php`:**
```php
<div class="br-pagetitle d-flex justify-content-between align-items-center">
    <div class="d-flex align-items-center">
        <i class="fas <?php echo $icono_titulo; ?> tx-50 lh-0"></i>
        <div class="d-inline-block align-middle">
            <h4 class="mg-b-0"><?php echo $titulo_pagina; ?></h4>
            <p class="mg-b-0 tx-gray-600">Complete los datos</p>
        </div>
        <!-- ✅ BOTÓN DE AYUDA EN FORMULARIO -->
        <button type="button" class="btn btn-link p-0 ms-2" 
                data-bs-toggle="modal" 
                data-bs-target="#modalAyudaFormulario" 
                title="Ayuda sobre el formulario">
            <i class="bi bi-question-circle text-primary" style="font-size: 1.3rem;"></i>
        </button>
    </div>
    <div>
        <a href="index.php" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Volver
        </a>
    </div>
</div>
```

**5. Modal de ayuda embebido al final de `formularioEntidad.php`:**
```php
<!-- Modal de Ayuda del Formulario -->
<div class="modal fade" id="modalAyudaFormulario" tabindex="-1" aria-labelledby="modalAyudaFormularioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalAyudaFormularioLabel">
                    <i class="fas fa-question-circle me-2"></i>Ayuda - Formulario de [Entidad]
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <!-- Secciones por cada campo importante -->
                <div class="col-12">
                    <h6 class="text-primary"><i class="[icono] me-2"></i>Campo 1</h6>
                    <p><strong>Descripción.</strong></p>
                    <ul class="list-unstyled ms-3">
                        <li><i class="fas fa-check text-success me-2"></i>Validación 1</li>
                    </ul>
                    <hr>
                </div>
                <!-- Más campos... -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">
                    <i class="bi bi-check-lg me-2"></i>Entendido
                </button>
            </div>
        </div>
    </div>
</div>
```

### ✅ Corrección a Aplicar en el Prompt

**AGREGAR NUEVA SECCIÓN: SISTEMA DE AYUDA CONTEXTUAL**

```markdown
## 📚 SISTEMA DE AYUDA CONTEXTUAL (OBLIGATORIO)

### Archivos a Generar

**ARCHIVO 1: `ayudaEntidad.php`**
- Modal Bootstrap con ID: `#modalAyudaEntidad`
- Estructura de 2 columnas en el body
- Secciones obligatorias:
  * Funciones principales (botones CRUD)
  * Filtros y búsqueda (global + por columnas)
  * Estados (con badges de colores)
  * Datos de la entidad (campos obligatorios)
  * Alertas específicas (si hay fechas, etc.)
  * Consejos útiles
- Estilos CSS incluidos en el mismo archivo

**ARCHIVO 2: Modificar `index.php`**
- Botón de ayuda en el título (junto al h4)
- Include del archivo al final: `<?php include_once('ayudaEntidad.php') ?>`

**ARCHIVO 3: Modificar `formularioEntidad.php`**
- Botón de ayuda en el título del formulario
- Modal embebido al final (antes de cierre `</body>`)
- Contenido específico para cada campo del formulario

### Plantilla de Contenido

**Para `ayudaEntidad.php`:**
1. **Funciones**: Describir cada botón con icono, nombre y propósito
2. **Filtros**: Explicar búsqueda global y filtros por columna
3. **Estados**: Mostrar badges con colores y significados
4. **Campos obligatorios**: Lista con iconos de check
5. **Alertas**: Si hay fechas de vencimiento, explicar sistema de colores
6. **Consejos**: 4-6 tips útiles en 2 columnas

**Para modal en `formularioEntidad.php`:**
1. Cada campo importante debe tener su sección
2. Formato estándar:
   - Título con icono
   - Descripción breve
   - Lista de validaciones/características
   - Separador `<hr>`
3. Alertas específicas para campos críticos

### Checklist de Implementación

- [ ] Archivo `ayudaEntidad.php` creado
- [ ] Modal con ID único correcto
- [ ] Botón en `index.php` con `data-bs-target` correcto
- [ ] Include en `index.php` antes de cierre `</body>`
- [ ] Botón en `formularioEntidad.php`
- [ ] Modal embebido en `formularioEntidad.php`
- [ ] Contenido personalizado según entidad
- [ ] Campos obligatorios documentados
- [ ] Estados y alertas explicados
- [ ] Consejos útiles incluidos
```

---

## ❌ PROBLEMA 6: Tamaño de Fuente Insuficiente en DataTables

### Descripción
El tamaño de fuente por defecto de DataTables es demasiado pequeño para lectura cómoda. Al intentar aumentarlo con CSS inline con `!important`, se descubrió que **MntClientes usa la clase `fs-6` de Bootstrap** en los badges, que es el enfoque correcto.

### Código Incorrecto Generado
```css
/* CSS inline en index.php - NO HACER ESTO */
<style>
    #tblFurgonetas {
        font-size: 15px !important;
    }
    #tblFurgonetas tbody td {
        font-size: 15px !important;
        line-height: 1.6 !important;
    }
</style>
```

### Código Correcto
```javascript
// En mntentidad.js - Agregar fs-6 a todos los badges
render: function (data, type, row) {
    if (type === "display") {
        // ✅ CORRECTO: Usar clase fs-6 de Bootstrap
        return '<span class="badge bg-success fs-6">' + data + '</span>';
    }
    return data;
}
```

### ✅ Corrección a Aplicar en el Prompt

**Agregar al archivo `mntentidad.js`:**

```javascript
// SIEMPRE que se genere un badge, incluir la clase fs-6:
// Badges de estado
'<span class="badge bg-success fs-6">Activo</span>'

// Badges con iconos
'<span class="badge bg-warning fs-6"><i class="bi bi-exclamation-triangle me-1"></i>Alerta</span>'

// Badges de cantidad
'<span class="badge bg-info fs-6"><i class="bi bi-people-fill me-1"></i>' + cantidad + '</span>'
```

**NO HACER:**
- ❌ NO agregar estilos CSS inline con `!important` en `index.php`
- ❌ NO crear archivos CSS personalizados para cambiar font-size
- ❌ NO usar estilos globales que afecten todas las tablas

**SÍ HACER:**
- ✅ Agregar clase `fs-6` a TODOS los badges generados en renders
- ✅ Mantener las clases `align-middle` en las columnas
- ✅ Usar clases de Bootstrap en lugar de CSS custom

### Ubicaciones Típicas de Badges

1. **Columnas de estado/categoría**: Badges con colores
2. **Columnas de fechas**: Badges con colores según vencimiento
3. **Columnas de cantidad**: Badges con iconos y números
4. **Columnas de porcentaje/descuento**: Badges con símbolos

### Ejemplo Completo: Fecha con Badge

```javascript
{
    targets: "fecha_vencimiento:name",
    width: "12%",
    orderable: true,
    searchable: false,
    className: "text-center",
    render: function (data, type, row) {
        if (type === "display") {
            if (!data) {
                return '<span class="text-muted fst-italic">Sin fecha</span>';
            }
            
            const fecha = new Date(data);
            const hoy = new Date();
            const diasDiferencia = Math.ceil((fecha - hoy) / (1000 * 60 * 60 * 24));
            
            let badgeClass = "bg-success";
            if (diasDiferencia < 0) {
                badgeClass = "bg-danger";
            } else if (diasDiferencia <= 30) {
                badgeClass = "bg-warning";
            }
            
            const fechaEuropea = formatearFechaEuropea(data);
            // ✅ NOTA: fs-6 está aquí
            return '<span class="badge ' + badgeClass + ' fs-6">' + fechaEuropea + '</span>';
        }
        return data;
    },
}
```

---

## ❌ PROBLEMA 7: Botón Guardar No Funcional en Formularios

### Descripción
El botón "Guardar" en `formularioEntidad.js` **no funcionaba correctamente** debido a:
- Event binding directo en lugar de delegación
- Validaciones personalizadas no presentes en el patrón de referencia (MntClientes)
- Falta de verificación de existencia antes de guardar
- Flujo de guardado diferente al patrón establecido

### Síntomas
- Click en botón "Guardar" no ejecutaba ninguna acción
- No se mostraban errores en consola
- Formulario no enviaba datos al servidor
- Validaciones personalizadas fallaban silenciosamente

### Código Incorrecto Generado

```javascript
// ❌ Event binding directo (no delegado)
$('#btnSalvarFurgoneta').on('click', function (e) {
    e.preventDefault();
    
    // ❌ Validación personalizada no en MntClientes
    if (validator && !validator.validate()) {
        toastr.error('Por favor, complete los campos obligatorios');
        return;
    }

    // ❌ Validación Bootstrap nativa no en MntClientes
    const form = document.getElementById('formFurgoneta');
    if (!form.checkValidity()) {
        form.classList.add('was-validated');
        return;
    }
    
    // ❌ Verificación hasFormChanged() no en MntClientes
    if (modo === 'editar' && !hasFormChanged()) {
        toastr.info('No se detectaron cambios');
        return;
    }
    
    // ❌ Confirmación Swal antes de guardar (no en MntClientes)
    Swal.fire({
        title: '¿Guardar furgoneta?',
        showCancelButton: true
    }).then((result) => {
        if (result.isConfirmed) {
            guardarFurgoneta(); // Falta verificación previa
        }
    });
});
```

### Código Correcto (Patrón MntClientes)

```javascript
// ✅ Event delegation con $(document).on()
$(document).on('click', '#btnSalvarFurgoneta', function (event) {
    event.preventDefault();

    // ✅ Recoger valores del formulario
    var id_furgoneta = $('#id_furgoneta').val().trim();
    var matricula_furgoneta = $('#matricula_furgoneta').val().trim().toUpperCase();
    var marca_furgoneta = $('#marca_furgoneta').val().trim();
    // ... resto de campos

    // ✅ Solo FormValidator (si existe)
    if (formValidator && !formValidator.validateForm(event)) {
        toastr.error('Por favor, corrija los errores en el formulario.', 'Error de Validación');
        return;
    }

    // ✅ Validación básica de campo obligatorio
    if (!matricula_furgoneta) {
        toastr.error('La matrícula es obligatoria', 'Error de Validación');
        $('#matricula_furgoneta').focus();
        return;
    }

    // ✅ PRIMERO verificar existencia, LUEGO guardar
    verificarFurgonetaExistente(
        id_furgoneta,
        matricula_furgoneta,
        marca_furgoneta,
        // ... todos los parámetros
    );
});

// ✅ Función de verificación antes de guardar
function verificarFurgonetaExistente(
    id_furgoneta,
    matricula_furgoneta,
    // ... resto de parámetros
) {
    console.log('🔍 Verificando furgoneta:', { matricula: matricula_furgoneta, id: id_furgoneta });

    $.ajax({
        url: "../../controller/furgoneta.php?op=verificar",
        type: "POST",
        data: {
            matricula_furgoneta: matricula_furgoneta,
            id_furgoneta: id_furgoneta || ''
        },
        dataType: "json",
        success: function (response) {
            console.log('📋 Respuesta verificación:', response);

            if (response.existe === false) {
                // No existe, guardar
                console.log('✅ Furgoneta no existe, procediendo a guardar');
                guardarFurgoneta(
                    id_furgoneta,
                    matricula_furgoneta,
                    // ... todos los parámetros
                );
            } else {
                // Ya existe, mostrar advertencia
                console.log('❌ Furgoneta ya existe');
                mostrarErrorFurgonetaExistente("Ya existe una furgoneta con la matrícula '" + matricula_furgoneta + "'");
            }
        },
        error: function (xhr, status, error) {
            console.error('Error en verificación:', error);
            toastr.error('Error al verificar la furgoneta. Intente nuevamente.', 'Error');
        }
    });
}

// ✅ Función de guardado con manejo de estados del botón
function guardarFurgoneta(
    id_furgoneta,
    matricula_furgoneta,
    // ... resto de parámetros
) {
    // ✅ Deshabilitar botón con spinner
    $('#btnSalvarFurgoneta').prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Guardando...');

    // ✅ FormData con un append por campo
    const formData = new FormData();
    formData.append('id_furgoneta', id_furgoneta);
    formData.append('matricula_furgoneta', matricula_furgoneta);
    formData.append('marca_furgoneta', marca_furgoneta);
    // ... resto de campos

    console.log('💾 Enviando con FormData');

    $.ajax({
        url: "../../controller/furgoneta.php?op=guardaryeditar",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        dataType: "json",
        success: function (res) {
            console.log('📋 Respuesta del guardado:', res);

            if (res.success) {
                // ✅ Marcar como guardado para evitar alerta beforeunload
                formSaved = true;

                toastr.success(res.message || "Furgoneta guardada correctamente");

                // ✅ Redirigir después de 1.5s
                setTimeout(() => {
                    window.location.href = 'index.php';
                }, 1500);
            } else {
                toastr.error(res.message || "Error al guardar la furgoneta");
                // ✅ Restaurar botón original
                $('#btnSalvarFurgoneta').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Furgoneta');
            }
        },
        error: function (xhr, status, error) {
            console.error("Error en guardado:", error);

            let errorMsg = 'No se pudo guardar la furgoneta.';
            try {
                const response = JSON.parse(xhr.responseText);
                errorMsg = response.message || errorMsg;
            } catch (e) {
                errorMsg += ' Error: ' + error;
            }

            Swal.fire('Error', errorMsg, 'error');
            // ✅ Restaurar botón original
            $('#btnSalvarFurgoneta').prop('disabled', false).html('<i class="fas fa-save me-2"></i>Guardar Furgoneta');
        }
    });
}

// ✅ Función auxiliar para mostrar error de duplicado
function mostrarErrorFurgonetaExistente(mensaje) {
    console.log("Furgoneta duplicada detectada:", mensaje);
    Swal.fire({
        title: 'Furgoneta duplicada',
        text: mensaje,
        icon: 'warning',
        confirmButtonText: 'Entendido'
    });
}
```

### Diferencias Clave con MntClientes

#### ❌ Lo que NO debe estar (incorrecto)
1. **Event binding directo**: `$('#btnSalvar').on('click', ...)`
2. **Validación custom**: `validator.validate()`
3. **Validación Bootstrap nativa**: `form.checkValidity()`
4. **Check de cambios en click**: `hasFormChanged()` dentro del handler
5. **Confirmación Swal**: Mostrar confirmación antes de guardar
6. **Guardado directo**: Llamar `guardarEntidad()` sin verificar existencia

#### ✅ Lo que SÍ debe estar (correcto)
1. **Event delegation**: `$(document).on('click', '#btnSalvar', ...)`
2. **FormValidator simple**: `formValidator.validateForm(event)` (solo si existe)
3. **Validación básica**: Check manual de campos obligatorios
4. **Sin confirmación**: Guardar directamente después de validar
5. **Verificación previa**: Llamar `verificarEntidadExistente()` primero
6. **Manejo de estado del botón**: Deshabilitar con spinner → restaurar en error
7. **FormData explícito**: Un `formData.append()` por cada campo
8. **formSaved flag**: Marcar como guardado antes de redirect
9. **Console.logs**: Para debugging (🔍, 📋, ✅, ❌, 💾)

### Flujo Correcto (MntClientes)

```
CLICK en #btnSalvarEntidad
    ↓
event.preventDefault()
    ↓
Recoger todos los valores del formulario
(con .trim(), .toUpperCase() donde aplique)
    ↓
formValidator.validateForm(event) ← Solo si existe FormValidator
    ↓ (si pasa)
Validación básica de campos obligatorios
    ↓ (si pasa)
verificarEntidadExistente(todos_los_parametros)
    ↓
    ├─── AJAX POST a ?op=verificar
    ↓
    ├─── response.existe === false
    │    ↓
    │    guardarEntidad(todos_los_parametros)
    │        ↓
    │        Deshabilitar botón con spinner
    │        ↓
    │        FormData con todos los campos
    │        ↓
    │        AJAX POST a ?op=guardaryeditar
    │        ↓
    │        ├─── Success
    │        │    ↓
    │        │    formSaved = true
    │        │    toastr.success()
    │        │    setTimeout redirect 1.5s
    │        │
    │        └─── Error
    │             ↓
    │             Parse mensaje
    │             Swal.fire('Error', ...)
    │             Restaurar botón
    │
    └─── response.existe === true
         ↓
         mostrarErrorEntidadExistente("Ya existe...")
         SweetAlert warning
```

### Requisitos del Backend

Para que esto funcione, el controller debe tener:

```php
// controller/entidad.php

case "verificar":
    $resultado = $entidad->verificarCampoUnico(
        $_POST["campo_unico_entidad"],
        $_POST["id_entidad"] ?? null
    );

    if (!isset($resultado['success'])) {
        $resultado['success'] = !isset($resultado['error']);
    }

    header('Content-Type: application/json');
    echo json_encode($resultado, JSON_UNESCAPED_UNICODE);
    break;
```

Y el modelo debe tener:

```php
// models/Entidad.php

public function verificarCampoUnico($campo_valor, $id_entidad = null)
{
    try {
        $sql = "SELECT COUNT(*) AS total FROM entidad 
                WHERE LOWER(campo_unico_entidad) = LOWER(?)";
        $params = [trim($campo_valor)];
        
        // Excluir el propio registro en edición
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
```

### Anti-Patrones a Evitar

**NO HACER:**
- ❌ Event binding directo: `$('#btn').on('click', ...)`
- ❌ Validaciones custom no presentes en MntClientes
- ❌ Confirmación Swal antes de guardar
- ❌ Guardar sin verificar existencia primero
- ❌ No deshabilitar el botón durante guardado
- ❌ No usar FormData explícito
- ❌ No marcar formSaved antes de redirect
- ❌ Swal.showLoading() innecesarios

**SÍ HACER:**
- ✅ Event delegation: `$(document).on('click', '#btn', ...)`
- ✅ Solo formValidator.validateForm() si existe
- ✅ Verificar existencia ANTES de guardar
- ✅ Deshabilitar botón con spinner durante guardado
- ✅ FormData con append por cada campo
- ✅ Marcar formSaved = true en success
- ✅ Restaurar botón en error
- ✅ Console.logs para debugging
- ✅ Redirect con setTimeout(1500)

### Inicialización de FormValidator

```javascript
// ✅ CORRECTO: Estructura simple como MntClientes
let formValidator = null;
if (typeof FormValidator !== 'undefined') {
    formValidator = new FormValidator('formFurgoneta', {
        matricula_furgoneta: {
            required: true
        },
        marca_furgoneta: {
            required: true
        }
        // Solo campos realmente obligatorios
    });
}

// ❌ INCORRECTO: Estructura compleja
let validator = null;
if (typeof FormValidator !== 'undefined') {
    validator = new FormValidator('formFurgoneta', {
        rules: {
            matricula_furgoneta: {
                required: true,
                maxLength: 20
            }
        },
        messages: {
            matricula_furgoneta: {
                required: 'La matrícula es obligatoria',
                maxLength: 'La matrícula no puede exceder 20 caracteres'
            }
        }
    });
}
```

### Posicionamiento de Botones Guardar y Cancelar

**IMPORTANTE**: Los botones deben replicar EXACTAMENTE el layout de MntClientes:

#### ❌ Estructura INCORRECTA

```html
<!-- NO USAR: d-flex justify-content-between -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <a href="index.php" class="btn btn-secondary">
        <i class="fas fa-times me-2"></i>Cancelar
    </a>
    <button type="button" 
            class="btn btn-primary btn-lg" 
            id="btnSalvarFurgoneta">
        <i class="fas fa-save me-2"></i>Guardar Furgoneta
    </button>
</div>
```

**Problemas:**
- ❌ Botones justificados (uno a cada lado)
- ❌ Botón Cancelar no tiene `btn-lg`
- ❌ Orden incorrecto (Cancelar primero)
- ❌ No usa card con text-center
- ❌ Falta atributo `name="action"` en botón Guardar
- ❌ Falta espaciado `me-3` entre botones

#### ✅ Estructura CORRECTA (MntClientes)

```html
<!-- USAR: card con card-body text-center -->
<div class="card">
    <div class="card-body text-center">
        <button type="button" 
                name="action" 
                id="btnSalvarEntidad" 
                class="btn btn-primary btn-lg me-3">
            <i class="fas fa-save me-2"></i>Guardar Entidad
        </button>
        <a href="index.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
    </div>
</div>
```

**Características obligatorias:**
- ✅ Envuelto en `card > card-body text-center`
- ✅ Botones centrados (no justificados)
- ✅ **Botón Guardar PRIMERO**, Cancelar segundo
- ✅ Ambos botones con `btn-lg`
- ✅ Atributo `name="action"` en botón Guardar
- ✅ Espaciado `me-3` en botón Guardar (margen derecho)
- ✅ Iconos con `me-2` antes del texto

#### Tabla Comparativa

| Característica | ❌ Incorrecto | ✅ Correcto (MntClientes) |
|----------------|---------------|---------------------------|
| **Contenedor** | `d-flex justify-content-between` | `card > card-body text-center` |
| **Alineación** | Justificados (separados) | Centrados |
| **Orden** | Cancelar - Guardar | Guardar - Cancelar |
| **Tamaño Guardar** | `btn-lg` | `btn-lg` |
| **Tamaño Cancelar** | `btn` (normal) | `btn-lg` |
| **Espaciado** | Sin margen | `me-3` en Guardar |
| **Atributo name** | No | `name="action"` |
| **Consistencia visual** | Diferente entre módulos | Igual en todos los Mnt* |

#### Código PHP del Formulario

```php
<!-- Botones de acción -->
<div class="card">
    <div class="card-body text-center">
        <button type="button" 
                name="action" 
                id="btnSalvar<?php echo $nombreEntidad; ?>" 
                class="btn btn-primary btn-lg me-3">
            <i class="fas fa-save me-2"></i>Guardar <?php echo $nombreEntidad; ?>
        </button>
        <a href="index.php" class="btn btn-secondary btn-lg">
            <i class="fas fa-times me-2"></i>Cancelar
        </a>
    </div>
</div>
```

**Ubicación:** Después del último campo del formulario y antes del cierre del `</form>`

**Reglas de generación:**
1. Siempre dentro de un card para mejor presentación visual
2. Centrado con `text-center` para consistencia
3. Guardar SIEMPRE primero (acción principal)
4. Ambos botones grandes (`btn-lg`) para facilitar el click
5. Margen derecho (`me-3`) en Guardar para separación
6. ID dinámico: `btnSalvar<?php echo $nombreEntidad; ?>`
7. Texto dinámico: `Guardar <?php echo $nombreEntidad; ?>`

---

## 🔧 PROMPT MEJORADO - SECCIÓN A AÑADIR

```markdown
## ⚠️ VALIDACIONES CRÍTICAS PRE-GENERACIÓN

Antes de generar los archivos, verificar:

### 1. Etiqueta PHP de Apertura
- TODOS los archivos PHP deben comenzar con `<?php` en la línea 1
- No dejar líneas en blanco antes de la etiqueta

### 2. Módulo de Permisos
- Para módulos Mnt* usar: `$moduloActual = 'mantenimientos';`
- Incluir en index.php y formularioEntidad.php
- Colocar ANTES del código de validación

### 4. Formato de Fechas
- TODAS las fechas deben mostrarse en formato DD/MM/YYYY
- Incluir función `formatearFechaEuropea()` al inicio del JS
- Aplicar en renders de columnas Y en child rows

### 5. Sistema de Ayuda
- OBLIGATORIO crear archivo `ayudaEntidad.php`
- Botón de ayuda en título de index.php
- Modal de ayuda en formularioEntidad.php
- Contenido personalizado según la entidad

### 6. Tamaño de Fuente en Badges
- TODOS los badges deben incluir clase `fs-6` de Bootstrap
- NO usar CSS inline con `!important`
- Aplicar en todos los renders que generen badges

### 7. Botón Guardar en Formularios
- Event delegation: `$(document).on('click', '#btnSalvar', ...)`
- Solo `formValidator.validateForm(event)` si existe
- Verificar existencia ANTES de guardar
- Deshabilitar botón con spinner durante guardado
- FormData explícito (un `append()` por campo)
- Marcar `formSaved = true` antes de redirect
- Restaurar botón en errores
- NO usar confirmación Swal ni `form.checkValidity()`

### 8. Estructura de Archivos
Los ARCHIVOS a generar son:
1. **index.php** (con estadísticas PHP + HTML)
2. **mntentidad.js** (DataTables + CRUD)
3. **formularioEntidad.php** (formulario HTML)
4. **formularioEntidad.js** (validación + guardado)
5. **ayudaEntidad.php** (modal de ayuda del módulo) ✨ NUEVO
```

---

## 📊 COMPARATIVA: ANTES vs DESPUÉS

| Aspecto | Antes (Prompt Original) | Después (Prompt Mejorado) |
|---------|------------------------|---------------------------|
| **Etiqueta PHP** | ❌ Faltaba en algunos casos | ✅ Siempre presente |
| **Permisos** | ❌ Módulo específico (error) | ✅ 'mantenimientos' correcto |
| **CSS Details** | ❌ Estilos personalizados | ✅ Usa nativos de DataTables |
| **Formato Fechas** | ❌ Americano (YYYY-MM-DD) | ✅ Europeo (DD/MM/YYYY) |
| **Sistema Ayuda** | ❌ No existía | ✅ Modal completo + documentación |
| **Tamaño Fuente** | ❌ Badges sin fs-6 | ✅ Clase fs-6 en todos los badges |
| **Botón Guardar** | ❌ Event binding directo | ✅ Event delegation correcto |
| **Validación** | ❌ Múltiples validaciones custom | ✅ Solo FormValidator simple |
| **Verificación** | ❌ Sin verificar duplicados | ✅ Verificar ANTES de guardar |
| **Estado Botón** | ❌ Sin feedback visual | ✅ Spinner + disabled en guardado |
| **Posición Botones** | ❌ Justificados, tamaños dispares | ✅ Centrados, ambos btn-lg |
| **Archivos Generados** | 4 archivos | 5 archivos (+ ayudaEntidad.php) |
| **Estadísticas** | ⚠️ A veces no cargaban | ✅ Carga garantizada |
| **Acceso** | ❌ Denegado por permisos | ✅ Acceso correcto |
| **Botón Expandir** | ⚠️ Inconsistente | ✅ Nativo DataTables |
| **UX Fechas** | ❌ Confuso para usuarios ES | ✅ Formato localizado |
| **UX General** | ⚠️ Sin guía para usuarios | ✅ Ayuda contextual integrada |
| **Legibilidad** | ❌ Fuente pequeña | ✅ Fuente legible con fs-6 |

---

## 🎯 IMPLEMENTACIÓN EN FUTURAS ENTIDADES

Al usar el prompt mejorado para crear módulos como:
- `MntProveedores`
- `MntCategorias`
- `MntUbicaciones`
- `MntEstados`
- etc.

**Los 5 problemas documentados NO se repetirán** si se siguen las correcciones indicadas.

---

## 📚 REFERENCIAS

- Archivo original: `docs/cabecera_pies/prompt_cabecera_pies.md`
- Módulo de prueba: `view/MntFurgonetas/`
- Sistema de permisos: `config/template/verificarPermiso.php`
- DataTables docs: https://datatables.net/

---

## 📝 CHECKLIST PARA VALIDACIÓN POST-GENERACIÓN

Revisar SIEMPRE estos 7 puntos críticos antes de considerar completa la generación:

### ✅ 1. Apertura PHP en Archivos
- [ ] `index.php` comienza con `<?php` en línea 1
- [ ] `formularioEntidad.php` comienza con `<?php` en línea 1
- [ ] `ayudaEntidad.php` comienza con `<?php` en línea 1
- [ ] Código PHP se ejecuta correctamente (estadísticas, verificaciones)

### ✅ 2. Módulo de Permisos
- [ ] `$moduloActual` en `index.php` es correcto según tabla permisosPorRol
- [ ] `$moduloActual` en `formularioEntidad.php` coincide con index.php
- [ ] Verificar en array: `['almacen', 'mantenimientos', 'configuracion', 'administracion']`
- [ ] Probar acceso con rol limitado (no admin)

### ✅ 3. Botón Details-Control
- [ ] NO incluir CSS personalizado para `.details-control`
- [ ] Dejar que DataTables use estilos nativos
- [ ] Botón debe mostrarse consistente (+ cuando cerrado, - cuando abierto)
- [ ] Verificar que child rows se expanden correctamente

### ✅ 4. Formato de Fechas Europeo
- [ ] Función `formatearFechaEuropea()` presente al inicio del JS
- [ ] Todas las columnas de fecha usan `formatearFechaEuropea(data)` en render
- [ ] Child rows con fechas usan operador ternario + función
- [ ] Inputs del formulario mantienen `type="date"` sin modificar
- [ ] Fechas se muestran como DD/MM/YYYY (no YYYY-MM-DD)

### ✅ 5. Sistema de Ayuda Contextual
- [ ] Archivo `ayudaEntidad.php` existe con modal completo
- [ ] Botón de ayuda en título de `index.php` (icono `bi-question-circle`)
- [ ] Include de `ayudaEntidad.php` al final de `index.php`
- [ ] Botón de ayuda en título de `formularioEntidad.php`
- [ ] Modal de ayuda embebido en `formularioEntidad.php`
- [ ] Contenido personalizado según la entidad específica
- [ ] Secciones obligatorias presentes (funciones, filtros, estados, datos, alertas, consejos)
- [ ] Campos obligatorios identificados en la ayuda
- [ ] Estados y alertas documentados con badges de colores

### ✅ 6. Tamaño de Fuente en Badges
- [ ] Todos los badges incluyen clase `fs-6` de Bootstrap
- [ ] NO hay CSS inline con `!important` para font-size
- [ ] Badges de estado tienen `fs-6`
- [ ] Badges de fechas tienen `fs-6`
- [ ] Badges de cantidad/iconos tienen `fs-6`
- [ ] NO se crearon archivos CSS personalizados para fuente
- [ ] Se usa Bootstrap en lugar de CSS custom

### ✅ 7. Botón Guardar en Formularios
- [ ] Event delegation: `$(document).on('click', '#btnSalvar', ...)`
- [ ] Solo `formValidator.validateForm(event)` si FormValidator existe
- [ ] NO usa `form.checkValidity()` ni `validator.validate()`
- [ ] NO usa `hasFormChanged()` en el click handler
- [ ] NO muestra confirmación Swal antes de guardar
- [ ] Llama a `verificarEntidadExistente()` ANTES de guardar
- [ ] Backend tiene `case "verificar"` en controller
- [ ] Modelo tiene método `verificarCampoUnico()`
- [ ] `guardarEntidad()` deshabilita botón con spinner
- [ ] FormData usa un `append()` por cada campo
- [ ] Success marca `formSaved = true` antes de redirect
- [ ] Error restaura botón con texto original
- [ ] Console.logs para debugging (🔍, 📋, ✅, ❌, 💾)
- [ ] **Botones dentro de `<div class="card"><div class="card-body text-center">`**
- [ ] **Botón Guardar PRIMERO, Cancelar segundo**
- [ ] **Ambos botones con clase `btn-lg`**
- [ ] **Botón Guardar tiene `me-3` para separación**
- [ ] **Botón Guardar tiene atributo `name="action"`**
- [ ] **Iconos con `me-2` antes del texto**
- [ ] **NO usar `d-flex justify-content-between`**

### 🎯 Validación Completa
Si todos los checkboxes están marcados ✅, la generación está lista para producción.

---

## 🔄 PROCESO DE ACTUALIZACIÓN DEL PROMPT PRINCIPAL

1. **Leer** el archivo `prompt_cabecera_pies.md`
2. **Agregar** las 7 secciones de validación crítica
3. **Actualizar** los ejemplos de código (especialmente formularioEntidad.js)
4. **Añadir** el checklist de validación ampliado
5. **Incluir** generación de sistema de ayuda
6. **Añadir** patrón MntClientes para botón guardar
7. **Probar** con una nueva entidad para verificar

---

**Documento creado**: 23/12/2025  
**Última actualización**: 23/12/2025 (Problema 7: Botón Guardar - Posicionamiento añadido)  
**Estado**: ✅ Validado con MntFurgonetas  
**Problemas documentados**: 7 (PHP tag, Permisos, CSS Details, Fechas, Ayuda, Font-size, Botón Guardar + Layout)  
**Archivos por módulo**: 5 (index.php, mntentidad.js, formularioEntidad.php, formularioEntidad.js, ayudaEntidad.php)  
**Próxima revisión**: Al implementar siguiente módulo Mnt*
