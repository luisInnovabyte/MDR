Necesito implementar las VISTAS del sistema cabecera-pies (basado en MntArticulos) para un módulo existente.

⚠️ IMPORTANTE: El backend (Controller y Modelo) YA ESTÁ IMPLEMENTADO.
Solo necesito las 4 vistas del frontend siguiendo EXACTAMENTE los patrones documentados.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): furgoneta
- Nombre entidad (plural): furgonetas
- Módulo: MntFurgonetas
- Controller: controller/furgoneta.php (YA EXISTE ✅)
- Modelo: models/furgoneta.php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
-- ========================================================
-- TABLA: furgoneta (CORREGIDA - SIN DATOS REDUNDANTES)
-- DESCRIPCIÓN: Gestión de vehículos de la empresa (furgonetas)
-- FECHA: 2024-12-20
-- ========================================================

CREATE TABLE furgoneta (
    id_furgoneta INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    
    -- =====================================================
    -- IDENTIFICACIÓN DEL VEHÍCULO
    -- =====================================================
    matricula_furgoneta VARCHAR(20) NOT NULL UNIQUE
        COMMENT 'Matrícula del vehículo',
    
    marca_furgoneta VARCHAR(100)
        COMMENT 'Marca del vehículo (Renault, Mercedes, Ford, etc.)',
    
    modelo_furgoneta VARCHAR(100)
        COMMENT 'Modelo del vehículo (Master, Sprinter, Transit, etc.)',
    
    anio_furgoneta INT
        COMMENT 'Año de fabricación',
    
    numero_bastidor_furgoneta VARCHAR(50)
        COMMENT 'Número de bastidor/chasis (VIN)',
    
    -- =====================================================
    -- CONFIGURACIÓN DE MANTENIMIENTO
    -- =====================================================
    kilometros_entre_revisiones_furgoneta INT UNSIGNED DEFAULT 10000
        COMMENT 'Kilómetros entre revisiones preventivas (ej: 10000 km)',
    
    -- =====================================================
    -- ITV Y SEGUROS
    -- =====================================================
    fecha_proxima_itv_furgoneta DATE
        COMMENT 'Fecha de vencimiento de la ITV',
    
    fecha_vencimiento_seguro_furgoneta DATE
        COMMENT 'Fecha de vencimiento del seguro',
    
    compania_seguro_furgoneta VARCHAR(255)
        COMMENT 'Compañía aseguradora',
    
    numero_poliza_seguro_furgoneta VARCHAR(100)
        COMMENT 'Número de póliza del seguro',
    
    -- =====================================================
    -- CAPACIDAD Y CARACTERÍSTICAS
    -- =====================================================
    capacidad_carga_kg_furgoneta DECIMAL(10,2)
        COMMENT 'Capacidad de carga en kilogramos',
    
    capacidad_carga_m3_furgoneta DECIMAL(10,2)
        COMMENT 'Capacidad de carga en metros cúbicos',
    
    -- =====================================================
    -- CONSUMO Y COMBUSTIBLE
    -- =====================================================
    tipo_combustible_furgoneta VARCHAR(50)
        COMMENT 'Tipo de combustible (Diesel, Gasolina, Eléctrico, Híbrido)',
    
    consumo_medio_furgoneta DECIMAL(5,2)
        COMMENT 'Consumo medio en L/100km',
    
    -- =====================================================
    -- MANTENIMIENTO
    -- =====================================================
    taller_habitual_furgoneta VARCHAR(255)
        COMMENT 'Taller donde se realizan los mantenimientos habitualmente',
    
    telefono_taller_furgoneta VARCHAR(50)
        COMMENT 'Teléfono del taller habitual',
    
    -- =====================================================
    -- ESTADO Y OBSERVACIONES
    -- =====================================================
    estado_furgoneta ENUM('operativa', 'taller', 'baja') DEFAULT 'operativa'
        COMMENT 'Estado actual del vehículo',
    
    observaciones_furgoneta TEXT
        COMMENT 'Observaciones generales sobre el vehículo',
    
    -- =====================================================
    -- CONTROL
    -- =====================================================
    activo_furgoneta BOOLEAN DEFAULT TRUE
        COMMENT 'TRUE: Vehículo activo | FALSE: Vehículo dado de baja',
    
    created_at_furgoneta TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    
    updated_at_furgoneta TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    -- =====================================================
    -- ÍNDICES
    -- =====================================================
    INDEX idx_matricula_furgoneta (matricula_furgoneta),
    INDEX idx_estado_furgoneta (estado_furgoneta),
    INDEX idx_activo_furgoneta (activo_furgoneta),
    INDEX idx_fecha_proxima_itv (fecha_proxima_itv_furgoneta)
    
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 
COMMENT='Vehículos de la empresa (furgonetas de transporte)';

``` {data-source-line="97"}

📡 OPERACIONES DEL CONTROLLER DISPONIBLES:

El controller ya tiene implementadas estas operaciones:

1. **estadisticas** (GET)
   - Endpoint: `controller/furgoneta.php?op=estadisticas`
   - Respuesta: `{ success: true, data: { total: X, activos: Y, ... } }`

2. **listar** (GET/POST)
   - Endpoint: `controller/furgoneta.php?op=listar`
   - Respuesta: Formato DataTables `{ draw, recordsTotal, recordsFiltered, data: [...] }`

3. **guardaryeditar** (POST)
   - Endpoint: `controller/furgoneta.php?op=guardaryeditar`
   - Parámetros: FormData con todos los campos + id_[entidad] (vacío=nuevo, con valor=editar)
   - Respuesta: `{ success: true/false, message: "..." }`

4. **mostrar** (POST)
   - Endpoint: `controller/furgoneta.php?op=mostrar`
   - Parámetros: `{ id_[entidad]: X }`
   - Respuesta: Objeto con todos los campos del registro

5. **eliminar** (POST - soft delete)
   - Endpoint: `controller/furgoneta.php?op=eliminar`
   - Parámetros: `{ id_[entidad]: X }`
   - Respuesta: `{ success: true/false, message: "..." }`

6. **activar** (POST)
   - Endpoint: `controller/furgoneta.php?op=activar`
   - Parámetros: `{ id_[entidad]: X }`
   - Respuesta: `{ success: true/false, message: "..." }`

📈 ESTADÍSTICAS PARA EL PANEL:

El panel superior debe mostrar estas 4 tarjetas (llamar a ?op=estadisticas):

1. [Tarjeta 1] - Color: border-primary - Icono: [icono] - Valor: data.activas
2. [Tarjeta 2] - Color: border-success - Icono: [icono] - Valor: data.operativas
3. [Tarjeta 3] - Color: border-success - Icono: [icono] - Valor: data.taller
4. [Tarjeta 4] - Color: border-success - Icono: [icono] - Valor: data.baja

🎨 CONFIGURACIÓN DATATABLES:

**Agrupación:**
[OPCIÓN B: Sin agrupación (tabla simple)]

**Child Rows:**
[OPCIÓN A: Sí - Mostrar en child row todos los campos de la tabla


**Columnas visibles:**
1. [Nombre columna 1] - data: "[matricula_furgoneta]"
2. [Nombre columna 2] - data: "[modelo_furgoneta]"
3. [Nombre columna 3] - data: "[anio_furgoneta]"
4. [Nombre columna 4] - data: "[fecha_proxima_itv_furgoneta]"
5. [Nombre columna 5] - data: "[fecha_vencimiento_seguro_furgoneta]"
5. [Nombre columna 5] - data: "[estado_furgoneta]"


**Filtros en footer:**
- Columna [1]: Input text para filtrar por [matricula_furgoneta]
- Columna [2]: Input text para filtrar por [modelo_furgoneta]
- Columna [3]: Input text para filtrar por [anio_furgoneta]
- Columna [5]: Select para filtrar por [estado_furgoneta con valores: operativa, taller y baja]
- Columna [6]: Select de estado (Activo/Inactivo)

📝 CAMPOS DEL FORMULARIO:

El formulario (formulario[Entidad].php) debe tener estos campos:

**Sección 1: Información Básica**
Todos los campos de la tabla

**Sección 2: [Nombre sección si aplica]**
- [matricula_furgoneta] (vartext, obligatorio, validaciones logicas, es decir no vacio)

No tiene imagenes.


🎯 DOCUMENTACIÓN TÉCNICA - SEGUIR EXACTAMENTE:

⚠️ **CRÍTICO:** Antes de generar el código, DEBES LEER Y SEGUIR FIELMENTE estos archivos de documentación:

📖 **ARCHIVO 1: docs/index_cabecera_pies_estructura.md**
   - **Para:** Generar `view/Mntfurgonetas/index.php`
   - **Contiene:** Estructura completa línea por línea del archivo index.php
   - **Seguir exactamente:**
     
     * Bloque carga estadísticas: try-catch con null coalescing `??`
     * HTML panel 4 tarjetas: clases `card border-primary/success/info/warning`
     * HTML alerta filtros: `id="filter-alert"` con botón `id="clear-filter"`
     * HTML tabla: `<thead>` columnas + `<tfoot>` filtros (inputs y selects)
     * Orden carga scripts: jQuery → Bootstrap → DataTables → SweetAlert2 → mnt[entidad].js

📖 **ARCHIVO 2: docs/index_cabecera_pies_datatables.md**
   - **Para:** Configuración DataTables en `view/MntFurgonetas/mntfurgoneta.js`
   - **Contiene:** Configuración completa de DataTables con todas las opciones
   - **Seguir exactamente:**
     * Config básica: `language: { url: 'es-ES.json' }`, responsive, dom, ordering
     * Array `columns[]`: especificación data/title/className por columna
     * Array `columnDefs[]`: targets y render functions (badges, formateo, iconos, botones)
     * Config AJAX: `{ url, type: 'POST', dataSrc: 'data' }`
     * RowGroup (si aplica): `{ dataSrc, startRender: function() con HTML personalizado }`
     * Función `format(d)` child rows: retorna HTML tabla con datos detallados
     * Evento click expand: `$('#tabla').on('click', 'td.details-control', ...)`

📖 **ARCHIVO 3: docs/index_cabecera_pies_js_funciones.md**
   - **Para:** Funciones JavaScript en `view/MntFurgonetas/mntfurgoneta.js`
   - **Contiene:** Todas las funciones JavaScript estándar del sistema
   - **Seguir exactamente:**
     * `recargarEstadisticas()`: 4 $.ajax().done() actualizando #stat-total, #stat-activos...
     * `desacFurgoneta(id)`: SweetAlert2 confirmación → $.post() eliminar → toastr → reload
     * `activarFurgoneta(id)`: $.post() activar → toastr.success() → reload
     * `editarFurgoneta(id)`: window.location.href a formulario con ?modo=editar&id=X
     * `updateFilterMessage()`: construir texto filtros activos + mostrar/ocultar alerta
     * Evento `$('#clear-filter').on('click')`: limpiar filtros + reload
     * Event delegation: `$(document).on('click', '.btn-editar', function() { ... })`

📖 **ARCHIVO 4: docs/index_cabecera_pies_formulario.md**
   - **Para:** Generar `formularioFurgoneta.php` y `ayudaFurgoneta.php`
   - **Contiene:** Sistema completo de formulario independiente y modal de ayuda
   - **Seguir exactamente para formulario:**
     * Validación GET: `if (!isset($_GET['modo']) || ($_GET['modo'] === 'editar' && !isset($_GET['id'])))`
     * Breadcrumb: `<nav><ol class="breadcrumb">` con niveles
     * Header: título dinámico + botón ayuda con `data-bs-toggle="modal" data-bs-target="#modalAyuda"`
     * Cards secciones: `<div class="card mb-3"><div class="card-header"><h5>`
     * Campos: clases `form-control`, atributos `required maxlength pattern`
     * Preview imagen: `<img id="preview_imagen_[entidad]" style="display:none">`
     * ⚠️ **NO incluir scripts inline**: El JavaScript va en archivo separado (ver Archivo 5)
   - **Seguir exactamente para ayuda:**
     * Modal: `<div class="modal fade" id="modalAyuda" tabindex="-1">`
     * Accordion: `<div class="accordion accordion-flush" id="accordionAyuda">`
     * Items: `<div class="accordion-item">` por cada campo con header + body
     * Iconos: `<i class="bi bi-XXX">` Bootstrap Icons
     * Estructura: Explicación + Ejemplo + Notas (si aplica)

📖 **ARCHIVO 5: docs/index_cabecera_pies_formulario_js.md** 🆕
   - **Para:** Generar `formulario[Entidad].js` (JavaScript separado del formulario)
   - **Contiene:** Toda la lógica JavaScript del formulario en archivo independiente
   - **Seguir exactamente:**
     * Estructura `$(document).ready(function () { ... });`
     * Instancia FormValidator: `var formValidator = new FormValidator('form[Entidad]', {...})`
     * Funciones carga selects: `cargar[SelectX]()` con $.ajax() a controllers específicos
     * Listeners change: `$('#id_select').on('change', ...)` para mostrar descripciones
     * Función `getUrlParameter(name)` para detectar modo (nuevo/editar)
     * Función `cargarDatos[Entidad](id)`: AJAX a `?op=mostrar`, llenar campos, trigger('change')
     * Click botón guardar: `$(document).on('click', '#btnSalvar[Entidad]', ...)`
     * Función `verificar[Entidad]Existente(...)`: AJAX a `?op=verificar`, validar unicidad
     * Función `guardar[Entidad](...)`: FormData con todos los campos, manejo de NULL para opcionales
     * Variables control cambios: `formOriginalValues`, `formSaved`
     * Función `captureOriginalValues()`: snapshot de valores iniciales del formulario
     * Función `hasFormChanged()`: comparar valores actuales vs originales + archivos nuevos
     * Event `beforeunload`: advertencia navegador si `!formSaved && hasFormChanged()`
     * Funciones globales (fuera de ready): `showDefaultImagePreview()`, `showExistingImage(path)`
     * FormData config: `processData: false, contentType: false` para archivos
     * Botón spinner: `prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>Guardando...')`
     * Redirección: `setTimeout(() => { window.location.href = 'index.php'; }, 1500);`

✅ **Librerías (rutas exactas a usar):**
- jQuery 3.7.1: `../../public/lib/jquery-3.7.1/jquery.min.js`
- DataTables 2.x: `../../public/lib/DataTables/datatables.min.js` + `.css`
- Bootstrap 5.0.2: `../../public/lib/bootstrap-5.0.2/js/bootstrap.bundle.min.js` + `.css`
- SweetAlert2 11.7.32: `../../public/lib/sweetalert2-11.7.32/sweetalert2.all.min.js` + `.css`
- Toastr 2.x: `../../public/lib/toastr/toastr.min.js` + `.css`
- Bootstrap Icons CDN: `https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css`
- FontAwesome 6.4.2: `../../public/lib/fontawesome-6.4.2/css/all.min.css`

✅ **Convenciones del proyecto (docs/.github/copilot-instructions.md):**
- Charset UTF-8: `<meta charset="UTF-8">`
- Responsive Bootstrap 5: `class="col-12 col-md-6 col-lg-3"`
- Iconos: Preferir Bootstrap Icons `<i class="bi bi-check-circle"></i>`
- Mensajes en español con acentos
- Validación HTML5 antes de AJAX: `$('#formulario')[0].checkValidity()`
- Loading Swal.fire: `showConfirmButton: false, allowOutsideClick: false`
- Toastr posición: `positionClass: 'toast-top-right'`
- DataTables idioma: `language: { url: '../../public/lib/DataTables/es-ES.json' }`

⚠️ **NO IMPROVISES - COPIA LOS PATRONES:**
Los archivos de documentación contienen código completo y funcional. COPIA los bloques de código tal cual están documentados, adaptando ÚNICAMENTE:
- Nombres de campos según tu tabla
- Nombre de la entidad [entidad] / [Entidad] / [entidades] / [Entidades]
- Endpoints del controller
- Cantidad y nombres de columnas DataTables
- Selects que cargan desde otros controllers

TODO LO DEMÁS debe ser EXACTAMENTE igual a la documentación.

🆕 **IMPORTANTE: SEPARACIÓN DE ARCHIVOS JS:**
- `formulario[Entidad].php` contiene SOLO HTML (no scripts inline)
- `formulario[Entidad].js` contiene TODA la lógica del formulario (archivo separado)
- Al final del .php, incluir: `<script src="formulario[Entidad].js"></script>`

📚 **DOCUMENTACIÓN COMPLETA DE REFERENCIA:**
1. `docs/index_cabecera_pies.md` - Visión general y arquitectura
2. `docs/index_cabecera_pies_estructura.md` - ⭐ Estructura index.php
3. `docs/index_cabecera_pies_datatables.md` - ⭐ Config DataTables
4. `docs/index_cabecera_pies_js_funciones.md` - ⭐ Funciones JavaScript
5. `docs/index_cabecera_pies_formulario.md` - ⭐ Formulario y ayuda (HTML)
6. `docs/index_cabecera_pies_formulario_js.md` - ⭐ Formulario (JavaScript) 🆕
7. `docs/index_cabecera_pies_replicacion.md` - Guía paso a paso

Por favor, PRIMERO lee los 5 archivos marcados con ⭐, LUEGO genera los 5 archivos de frontend siguiendo EXACTAMENTE los patrones documentados.