# Prompt para Replicar Sistema Cabecera-Pies
## Plantilla de solicitud para implementar las vistas del patrón (Backend ya implementado)

> **Propósito:** Prompt reutilizable para que un asistente de IA genere las vistas del sistema cabecera-pies  
> **Contexto:** Controller y Modelo ya están programados. Solo falta el frontend.  
> **Documentación:** Basado en los archivos `index_cabecera_pies_*.md`

---

## 📋 Checklist Pre-Implementación

Antes de usar el prompt, recopila y ten listos estos archivos:

### ✅ Archivos Backend (YA IMPLEMENTADOS)

- [ ] **Tabla de base de datos creada** 
  - SQL CREATE TABLE listo para copiar
  - Vista SQL (si existe)

- [ ] **Controller implementado** 
  - Ruta: `controller/[entidad].php`
  - Operaciones disponibles: estadisticas, listar, guardaryeditar, mostrar, eliminar, activar

- [ ] **Modelo implementado**
  - Ruta: `models/[Entidad].php`
  - Métodos implementados verificados

### ✅ Información de la Entidad

- [ ] **Nombre de la entidad** (singular): `_____________`
  - Ejemplo: `proveedor`, `cliente`, `furgoneta`

- [ ] **Nombre en plural**: `_____________`
  - Ejemplo: `proveedores`, `clientes`, `furgonetas`

- [ ] **Prefijo de tabla**: `_____________`
  - Ejemplo: `proveedor`, `cliente`, `furgoneta`

- [ ] **Módulo**: `_____________`
  - Ejemplo: `MntProveedores`, `MntClientes`, `MntFurgonetas`

### ✅ Visualización DataTables

- [ ] **¿Tiene agrupación (RowGroup)?**
  - [ ] Sí → Campo para agrupar: `_____________`
  - [ ] No → Tabla simple

- [ ] **¿Necesita child rows expandibles?**
  - [ ] Sí → Campos a mostrar: `_____________`
  - [ ] No

- [ ] **Columnas visibles en la tabla**: `_____________`
  - Ejemplo: Código, Nombre, Email, Teléfono, Estado, Acciones

- [ ] **Campos con filtro en footer**: `_____________`
  - Ejemplo: Código, Nombre, Email, Estado

### ✅ Estadísticas del Panel

¿Qué contadores mostrar? (máximo 4 tarjetas)

1. `_____________` (Ejemplo: Total de proveedores)
2. `_____________` (Ejemplo: Proveedores activos)
3. `_____________` (Ejemplo: Proveedores con pedidos)
4. `_____________` (Ejemplo: Nuevos este mes)

### ✅ Campos del Formulario

- [ ] **Campos obligatorios**: `_____________`
- [ ] **Campos opcionales**: `_____________`
- [ ] **Campos tipo select (FK)**: `_____________`
- [ ] **Campos tipo checkbox**: `_____________`
- [ ] **Campos tipo textarea**: `_____________`
- [ ] **¿Tiene upload de imágenes?**: Sí / No

---

## 🎯 Prompt Base para Copiar y Pegar

```text
Necesito implementar las VISTAS del sistema cabecera-pies (basado en MntArticulos) para un módulo existente.

⚠️ IMPORTANTE: El backend (Controller y Modelo) YA ESTÁ IMPLEMENTADO.
Solo necesito las 4 vistas del frontend siguiendo EXACTAMENTE los patrones documentados.

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): [entidad]
- Nombre entidad (plural): [entidades]
- Módulo: Mnt[Entidades]
- Controller: controller/[entidad].php (YA EXISTE ✅)
- Modelo: models/[Entidad].php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
[PEGAR AQUÍ EL CREATE TABLE COMPLETO]
```

📡 OPERACIONES DEL CONTROLLER DISPONIBLES:

El controller ya tiene implementadas estas operaciones:

1. **estadisticas** (GET)
   - Endpoint: `controller/[entidad].php?op=estadisticas`
   - Respuesta: `{ success: true, data: { total: X, activos: Y, ... } }`

2. **listar** (GET/POST)
   - Endpoint: `controller/[entidad].php?op=listar`
   - Respuesta: Formato DataTables `{ draw, recordsTotal, recordsFiltered, data: [...] }`

3. **guardaryeditar** (POST)
   - Endpoint: `controller/[entidad].php?op=guardaryeditar`
   - Parámetros: FormData con todos los campos + id_[entidad] (vacío=nuevo, con valor=editar)
   - Respuesta: `{ success: true/false, message: "..." }`

4. **mostrar** (POST)
   - Endpoint: `controller/[entidad].php?op=mostrar`
   - Parámetros: `{ id_[entidad]: X }`
   - Respuesta: Objeto con todos los campos del registro

5. **eliminar** (POST - soft delete)
   - Endpoint: `controller/[entidad].php?op=eliminar`
   - Parámetros: `{ id_[entidad]: X }`
   - Respuesta: `{ success: true/false, message: "..." }`

6. **activar** (POST)
   - Endpoint: `controller/[entidad].php?op=activar`
   - Parámetros: `{ id_[entidad]: X }`
   - Respuesta: `{ success: true/false, message: "..." }`

📈 ESTADÍSTICAS PARA EL PANEL:

El panel superior debe mostrar estas 4 tarjetas (llamar a ?op=estadisticas):

1. [Tarjeta 1] - Color: border-primary - Icono: [icono] - Valor: data.total
2. [Tarjeta 2] - Color: border-success - Icono: [icono] - Valor: data.activos
3. [Tarjeta 3] - Color: border-info - Icono: [icono] - Valor: data.[campo3]
4. [Tarjeta 4] - Color: border-warning - Icono: [icono] - Valor: data.[campo4]

🎨 CONFIGURACIÓN DATATABLES:

**Agrupación:**
[OPCIÓN A: RowGroup por campo "nombre_[categoria]"]
[OPCIÓN B: Sin agrupación (tabla simple)]

**Child Rows:**
[OPCIÓN A: Sí - Mostrar en child row: campo1, campo2, campo3, ...]
[OPCIÓN B: No - Sin child rows]

**Columnas visibles:**
1. [Nombre columna 1] - data: "[campo1]"
2. [Nombre columna 2] - data: "[campo2]"
3. [Nombre columna 3] - data: "[campo3]"
[... seguir listando todas las columnas]

**Filtros en footer:**
- Columna [X]: Input text para filtrar por [campo]
- Columna [Y]: Select para filtrar por [campo_select]
- Columna [Z]: Select de estado (Activo/Inactivo)

📝 CAMPOS DEL FORMULARIO:

El formulario (formulario[Entidad].php) debe tener estos campos:

**Sección 1: Información Básica**
- [campo1] (tipo, obligatorio/opcional, validaciones)
- [campo2] (tipo, obligatorio/opcional, validaciones)
- [...]

**Sección 2: [Nombre sección si aplica]**
- [campo_adicional] (tipo, obligatorio/opcional, validaciones)
- [...]

[SI APLICA: 
**Upload de imagen:**
- Campo: imagen_[entidad]
- Validación: JPG/PNG/GIF, máx 5MB
- Vista previa en formulario
]

🎯 DOCUMENTACIÓN TÉCNICA - SEGUIR EXACTAMENTE:

⚠️ **CRÍTICO:** Antes de generar el código, DEBES LEER Y SEGUIR FIELMENTE estos archivos de documentación:

📖 **ARCHIVO 1: docs/index_cabecera_pies_estructura.md**
   - **Para:** Generar `view/Mnt[Entidades]/index.php`
   - **Contiene:** Estructura completa línea por línea del archivo index.php
   - **Seguir exactamente:**
     * Bloque verificación permisos: `$moduloActual = 'Mnt[Entidades]'`
     * Bloque carga estadísticas: try-catch con null coalescing `??`
     * HTML panel 4 tarjetas: clases `card border-primary/success/info/warning`
     * HTML alerta filtros: `id="filter-alert"` con botón `id="clear-filter"`
     * HTML tabla: `<thead>` columnas + `<tfoot>` filtros (inputs y selects)
     * Orden carga scripts: jQuery → Bootstrap → DataTables → SweetAlert2 → mnt[entidad].js

📖 **ARCHIVO 2: docs/index_cabecera_pies_datatables.md**
   - **Para:** Configuración DataTables en `view/Mnt[Entidades]/mnt[entidad].js`
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
   - **Para:** Funciones JavaScript en `view/Mnt[Entidades]/mnt[entidad].js`
   - **Contiene:** Todas las funciones JavaScript estándar del sistema
   - **Seguir exactamente:**
     * `recargarEstadisticas()`: 4 $.ajax().done() actualizando #stat-total, #stat-activos...
     * `desac[Entidad](id)`: SweetAlert2 confirmación → $.post() eliminar → toastr → reload
     * `activar[Entidad](id)`: $.post() activar → toastr.success() → reload
     * `editar[Entidad](id)`: window.location.href a formulario con ?modo=editar&id=X
     * `updateFilterMessage()`: construir texto filtros activos + mostrar/ocultar alerta
     * Evento `$('#clear-filter').on('click')`: limpiar filtros + reload
     * Event delegation: `$(document).on('click', '.btn-editar', function() { ... })`

📖 **ARCHIVO 4: docs/index_cabecera_pies_formulario.md**
   - **Para:** Generar `formulario[Entidad].php` y `ayuda[Entidades].php`
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
```

---

## 📝 Ejemplos de Uso Completos

### Ejemplo 1: Módulo de Proveedores (Tabla Simple)

```text
Necesito implementar las VISTAS del sistema cabecera-pies para el módulo de Proveedores.

⚠️ Backend ya implementado: controller/proveedor.php y models/Proveedor.php

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): proveedor
- Nombre entidad (plural): proveedores
- Módulo: MntProveedores
- Controller: controller/proveedor.php (YA EXISTE ✅)
- Modelo: models/Proveedor.php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
CREATE TABLE proveedor (
    id_proveedor INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_proveedor VARCHAR(50) NOT NULL UNIQUE,
    nombre_proveedor VARCHAR(255) NOT NULL,
    nif_proveedor VARCHAR(20),
    email_proveedor VARCHAR(100),
    telefono_proveedor VARCHAR(20),
    direccion_proveedor VARCHAR(255),
    notas_proveedor TEXT,
    activo_proveedor BOOLEAN DEFAULT TRUE,
    created_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_proveedor TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_codigo_proveedor (codigo_proveedor),
    INDEX idx_nombre_proveedor (nombre_proveedor)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

📡 OPERACIONES DEL CONTROLLER DISPONIBLES:
✅ Todas las operaciones estándar implementadas (estadisticas, listar, guardaryeditar, mostrar, eliminar, activar)

📈 ESTADÍSTICAS PARA EL PANEL:
1. Total proveedores - Color: border-primary - Icono: bi-people - Valor: data.total
2. Proveedores activos - Color: border-success - Icono: bi-check-circle - Valor: data.activos
3. Con pedidos activos - Color: border-info - Icono: bi-cart - Valor: data.con_pedidos
4. Nuevos este mes - Color: border-warning - Icono: bi-star - Valor: data.nuevos

🎨 CONFIGURACIÓN DATATABLES:
- Sin agrupación (tabla simple)
- Sin child rows
- Columnas visibles: Código, Nombre, NIF, Email, Teléfono, Estado, Acciones
- Filtros en footer por: Código (input), Nombre (input), NIF (input), Email (input), Estado (select)

📝 CAMPOS DEL FORMULARIO:
**Sección 1: Información Básica**
- codigo_proveedor (text, obligatorio, único, maxlength 50)
- nombre_proveedor (text, obligatorio, maxlength 255)
- nif_proveedor (text, opcional, maxlength 20)
- email_proveedor (email, opcional, maxlength 100)
- telefono_proveedor (tel, opcional, maxlength 20)

**Sección 2: Datos Adicionales**
- direccion_proveedor (textarea, opcional)
- notas_proveedor (textarea, opcional)

🎯 DOCUMENTACIÓN: Leer docs/index_cabecera_pies_*.md antes de generar.
Por favor, genera los 4 archivos de frontend.
```

### Ejemplo 2: Módulo de Clientes (Con Agrupación)

```text
Necesito implementar las VISTAS del sistema cabecera-pies para el módulo de Clientes.

⚠️ Backend ya implementado: controller/cliente.php y models/Cliente.php

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): cliente
- Nombre entidad (plural): clientes
- Módulo: MntClientes
- Controller: controller/cliente.php (YA EXISTE ✅)
- Modelo: models/Cliente.php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
CREATE TABLE cliente (
    id_cliente INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_cliente VARCHAR(50) NOT NULL UNIQUE,
    nombre_cliente VARCHAR(255) NOT NULL,
    id_tipo_cliente INT UNSIGNED NOT NULL,
    email_cliente VARCHAR(100),
    telefono_cliente VARCHAR(20),
    direccion_cliente VARCHAR(255),
    activo_cliente BOOLEAN DEFAULT TRUE,
    created_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_cliente TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    INDEX idx_codigo_cliente (codigo_cliente),
    CONSTRAINT fk_cliente_tipo FOREIGN KEY (id_tipo_cliente) 
        REFERENCES tipo_cliente(id_tipo_cliente) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

📡 OPERACIONES DEL CONTROLLER DISPONIBLES:
✅ Todas las operaciones estándar implementadas
⚠️ El controller usa vista SQL que incluye el campo "nombre_tipo_cliente" para agrupación

📈 ESTADÍSTICAS PARA EL PANEL:
1. Total clientes - Color: border-primary - Icono: bi-people - Valor: data.total
2. Clientes activos - Color: border-success - Icono: bi-check-circle - Valor: data.activos
3. Tipo particular - Color: border-info - Icono: bi-person - Valor: data.particulares
4. Tipo empresa - Color: border-warning - Icono: bi-building - Valor: data.empresas

🎨 CONFIGURACIÓN DATATABLES:
- RowGroup por campo "nombre_tipo_cliente"
- Sin child rows
- Columnas visibles: Tipo, Código, Nombre, Email, Teléfono, Estado, Acciones
- Filtros en footer por: Tipo (select desde BD), Código (input), Nombre (input), Email (input), Estado (select)

📝 CAMPOS DEL FORMULARIO:
**Sección 1: Información Básica**
- codigo_cliente (text, obligatorio, único, maxlength 50)
- nombre_cliente (text, obligatorio, maxlength 255)
- id_tipo_cliente (select, obligatorio, carga desde controller/tipo_cliente.php?op=listar)
- email_cliente (email, opcional, maxlength 100)
- telefono_cliente (tel, opcional, maxlength 20)

**Sección 2: Dirección**
- direccion_cliente (textarea, opcional)

🎯 DOCUMENTACIÓN: Leer docs/index_cabecera_pies_*.md antes de generar.
Por favor, genera los 4 archivos de frontend.
```

### Ejemplo 3: Módulo de Elementos (Con Child Rows e Imágenes)

```text
Necesito implementar las VISTAS del sistema cabecera-pies para el módulo de Elementos.

⚠️ Backend ya implementado: controller/elemento.php y models/Elemento.php

📌 INFORMACIÓN DEL MÓDULO:
- Nombre entidad (singular): elemento
- Nombre entidad (plural): elementos
- Módulo: MntElementos
- Controller: controller/elemento.php (YA EXISTE ✅)
- Modelo: models/Elemento.php (YA EXISTE ✅)

📊 DEFINICIÓN DE LA TABLA (SQL):

```sql
CREATE TABLE elemento (
    id_elemento INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    codigo_elemento VARCHAR(50) NOT NULL UNIQUE,
    id_articulo INT UNSIGNED NOT NULL,
    numero_serie_elemento VARCHAR(100),
    id_estado_elemento INT UNSIGNED,
    id_ubicacion INT UNSIGNED,
    observaciones_elemento TEXT,
    fecha_compra_elemento DATE,
    precio_compra_elemento DECIMAL(10,2),
    imagen_elemento VARCHAR(255),
    activo_elemento BOOLEAN DEFAULT TRUE,
    created_at_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_elemento TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    
    CONSTRAINT fk_elemento_articulo FOREIGN KEY (id_articulo) 
        REFERENCES articulo(id_articulo) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

📡 OPERACIONES DEL CONTROLLER DISPONIBLES:
✅ Todas las operaciones estándar implementadas
✅ Operación adicional para upload de imágenes
⚠️ El controller usa vista SQL que incluye: nombre_articulo, nombre_estado_elemento, nombre_ubicacion

📈 ESTADÍSTICAS PARA EL PANEL:
1. Total elementos - Color: border-primary - Icono: bi-box - Valor: data.total
2. Elementos activos - Color: border-success - Icono: bi-check-circle - Valor: data.activos
3. Disponibles - Color: border-info - Icono: bi-inbox - Valor: data.disponibles
4. En uso - Color: border-warning - Icono: bi-truck - Valor: data.en_uso

🎨 CONFIGURACIÓN DATATABLES:
- RowGroup por campo "nombre_articulo"
- Child rows expandibles mostrando: 
  * Número de serie: numero_serie_elemento
  * Estado: nombre_estado_elemento (con badge de color)
  * Ubicación: nombre_ubicacion
  * Observaciones: observaciones_elemento
  * Fecha compra: fecha_compra_elemento (formato DD/MM/YYYY)
  * Precio compra: precio_compra_elemento (formato moneda)
  * Imagen: imagen_elemento (thumbnail clickable)
- Columnas visibles: Artículo, Código, N° Serie, Estado, Ubicación, Acciones (+ columna expand)
- Filtros en footer por: Artículo (select), Código (input), N° Serie (input), Estado (select), Ubicación (select)

📝 CAMPOS DEL FORMULARIO:
**Sección 1: Información Básica**
- codigo_elemento (text, obligatorio, único, maxlength 50)
- id_articulo (select con búsqueda, obligatorio, carga desde controller/articulo.php?op=listarDisponibles)
- numero_serie_elemento (text, opcional, maxlength 100)
- id_estado_elemento (select, obligatorio, carga desde controller/estado_elemento.php?op=listar)
- id_ubicacion (select, opcional, carga desde controller/ubicaciones.php?op=listar)

**Sección 2: Datos de Compra**
- fecha_compra_elemento (date, opcional)
- precio_compra_elemento (number, step 0.01, opcional)

**Sección 3: Imagen y Observaciones**
- imagen_elemento (file upload, opcional, JPG/PNG/GIF, máx 5MB, con vista previa)
- observaciones_elemento (textarea, opcional)

🎯 DOCUMENTACIÓN: Leer docs/index_cabecera_pies_*.md antes de generar.
Por favor, genera los 4 archivos de frontend.
```

---

## 📌 Notas Importantes

### ⚠️ Antes de ejecutar el prompt:

1. **Verifica backend**: Asegúrate de que el controller y modelo funcionan correctamente
   ```bash
   # Probar endpoints con curl o navegador
   http://localhost/MDR/controller/[entidad].php?op=estadisticas
   http://localhost/MDR/controller/[entidad].php?op=listar
   ```

2. **Verifica permisos**: Asegúrate de tener configurado el módulo en `config/permisos.php`
   ```php
   'Mnt[Entidades]' => [
       'nombre' => 'Gestión de [Entidades]',
       'roles' => ['admin', 'gerente']
   ]
   ```

3. **Crea el directorio**: 
   ```bash
   mkdir view/Mnt[Entidades]
   ```

4. **Prepara la información**: Completa el checklist con todos los datos necesarios

### ✅ Después de ejecutar el prompt:

1. **Revisar código generado**: 
   - Verificar nombres de campos coinciden con la tabla
   - Verificar endpoints del controller están correctos
   - Verificar nombres de métodos del modelo

2. **Probar la interfaz**:
   - [ ] Abrir index.php → Verificar que carga la tabla
   - [ ] Verificar que estadísticas cargan correctamente
   - [ ] Probar filtros en footer
   - [ ] Crear nuevo registro desde formulario
   - [ ] Editar registro existente
   - [ ] Desactivar y reactivar registros
   - [ ] Verificar child rows si aplica

3. **Ajustes comunes necesarios**:
   - Adaptar textos de mensajes
   - Ajustar colores de badges según estados
   - Personalizar iconos si es necesario
   - Añadir validaciones específicas del negocio

4. **Validar permisos**: 
   - Probar con diferentes roles de usuario
   - Verificar restricciones de acceso

---

## 🎯 Resultado Esperado

Al usar este prompt, el asistente generará ÚNICAMENTE los 5 archivos de frontend:

### ✅ Archivos Generados:

1. **view/Mnt[Entidades]/index.php** - Página principal con tabla DataTables
2. **view/Mnt[Entidades]/mnt[entidad].js** - JavaScript con lógica del listado
3. **view/Mnt[Entidades]/formulario[Entidad].php** - Formulario independiente (solo HTML)
4. **view/Mnt[Entidades]/formulario[Entidad].js** - JavaScript del formulario (separado) 🆕
5. **view/Mnt[Entidades]/ayuda[Entidades].php** - Modal de ayuda

### 🎨 Características Implementadas:

- ✅ Responsive design (mobile-first Bootstrap 5)
- ✅ Validaciones HTML5 y JavaScript
- ✅ Loading spinners durante operaciones AJAX
- ✅ Confirmaciones con SweetAlert2
- ✅ Notificaciones con Toastr
- ✅ Filtros en tiempo real con actualización automática
- ✅ Estadísticas que se actualizan automáticamente
- ✅ Manejo de errores con mensajes claros en español
- ✅ Integración completa con backend existente
- ✅ Compatibilidad con todos los navegadores modernos

---

## 📚 Referencias

- **Documentación completa**: [docs/index_cabecera_pies.md](./index_cabecera_pies.md)
- **Módulo original**: `view/MntArticulos/`
- **Convenciones del proyecto**: `.github/copilot-instructions.md`

---

**Última actualización:** 23 de diciembre de 2025  
**Versión:** 2.0 - Con referencias explícitas a documentación  
**Proyecto:** MDR ERP Manager  
**Autor:** Luis - Innovabyte
