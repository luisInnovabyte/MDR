# Vista de Ubicaciones de Cliente - MDR ERP

## Resumen de Archivos Creados

### Carpeta: `view/MntClientes_ubicaciones/`

Se han creado todos los archivos necesarios para la gestión de ubicaciones de clientes, adaptados desde el módulo de contactos de clientes (`MntClientes_contacto`).

---

## 📄 Archivos Creados

### 1. **index.php** (256 líneas)
- **Propósito**: Página principal del listado de ubicaciones
- **Características**:
  - Breadcrumb: Dashboard → Clientes → Ubicaciones del Cliente
  - Card informativa del cliente (carga dinámica)
  - Tabla DataTables con 13 columnas:
    - Control (expandir/contraer)
    - ID, Nombre, Dirección, Población, Provincia, País
    - Persona contacto, Teléfono
    - Principal (badge), Estado, Acciones (Activar/Desactivar, Editar)
  - Sistema de filtros por columna (footer)
  - Alerta de filtro activo con botón de limpiar
  - Botones: "Nueva Ubicación" y "Volver a Clientes"
- **Dependencias**:
  - Template: mainHead, mainSidebar, mainHeader, mainFooter
  - ayudaUbicaciones.php
  - mntclientes_ubicaciones.js

---

### 2. **ayudaUbicaciones.php** (250+ líneas)
- **Propósito**: Modal de ayuda con documentación del módulo
- **Características**:
  - Modal Bootstrap 5 responsive
  - Documentación de campos:
    - Obligatorios: Nombre de ubicación
    - Opcionales: Dirección, CP, Población, Provincia, País, Persona contacto, Teléfono, Email, Observaciones
  - Sección de buenas prácticas
  - Atajos de teclado
  - Información sobre ubicación principal
  - Formato: tabla de campos con badges (Obligatorio/Opcional)

---

### 3. **mntclientes_ubicaciones.js** (500+ líneas)
- **Propósito**: Configuración DataTables y handlers AJAX
- **Características principales**:
  - Configuración DataTables con idioma español
  - 13 columnas con renderizado personalizado:
    - Badges de estado (Activo/Inactivo)
    - Badge de ubicación principal
    - Botones de acción con iconos FontAwesome
  - Función `format()` para filas expandibles con detalles completos
  - Handlers AJAX:
    - `desacUbicacion()` - Desactivar con confirmación SweetAlert2
    - `activarUbicacion()` - Reactivar ubicación
    - `editarUbicacion()` - Redirigir a formulario de edición
  - Sistema de filtros por columna (inputs en footer)
  - Función `updateFilterMessage()` - Gestión de alertas de filtro
  - Filtro por cliente (parámetro GET `id_cliente`)
  - Paginación configurada (10, 25, 50, 100 registros)
  - Responsive con detalles expandibles
- **Endpoints utilizados**:
  - `../../controller/ubicaciones.php?op=listar`
  - `../../controller/ubicaciones.php?op=eliminar`
  - `../../controller/ubicaciones.php?op=activar`

---

### 4. **formularioUbicacion.php** (339 líneas)
- **Propósito**: Formulario de creación/edición de ubicaciones
- **Características**:
  - Modos: Nuevo / Editar (según parámetro GET `modo`)
  - Card informativa del cliente
  - Formulario organizado en 4 secciones:
    
    **a) Información de la Ubicación**
    - Nombre de ubicación (requerido, 2-100 caracteres)
    - País (default: "España")
    
    **b) Dirección Completa**
    - Dirección (255 caracteres)
    - Código postal (10 caracteres)
    - Población (100 caracteres)
    - Provincia (100 caracteres)
    
    **c) Información de Contacto**
    - Persona de contacto (100 caracteres)
    - Teléfono de contacto (20 caracteres, validado)
    - Email de contacto (100 caracteres, validado)
    
    **d) Configuración**
    - Checkbox "Ubicación Principal"
    - Observaciones (textarea)
    
  - Sección Estado (solo visible en modo edición)
  - Botones: Volver / Guardar (con iconos)
  - Inputs con placeholders y textos de ayuda
  - Validación en tiempo real (clases Bootstrap is-valid/is-invalid)
- **Dependencias**:
  - formularioUbicacion.js
  - ayudaUbicaciones.php (modal de ayuda)

---

### 5. **formularioUbicacion.js** (315 líneas)
- **Propósito**: Validación y manejo del formulario
- **Características principales**:
  
  **Funciones de Configuración**:
  - `configurarModoNuevo()` - Oculta sección de estado
  - `configurarModoEdicion(id)` - Muestra estado, carga datos
  - `cargarInfoCliente(id_cliente)` - Carga nombre del cliente
  - `cargarDatosUbicacion(id)` - Carga datos para edición
  - `configurarValidaciones()` - Establece validaciones en tiempo real
  
  **Funciones de Validación**:
  - `validarCampo($campo, funcionValidacion, obligatorio)` - Validador genérico
  - `validarNombre(nombre)` - Longitud 2-100 caracteres
  - `validarEmail(email)` - Regex de formato de email
  - `validarTelefono(telefono)` - Solo números, espacios, guiones, +, (, )
  - `validarDuplicadoUbicacion()` - Verifica nombre único por cliente
  - `validarUbicacionPrincipal()` - Aviso sobre ubicación principal
  - `mostrarError($campo, mensaje)` - Añade clase is-invalid
  - `mostrarExito($campo)` - Añade clase is-valid
  
  **Función Principal**:
  - `guardarUbicacion()` - Valida formulario, envía FormData vía AJAX
    - Convierte checkbox a 1/0
    - Muestra loading SweetAlert2
    - Redirige al listado tras éxito
  
  **Formateo Automático**:
  - Nombres a formato título (primera letra mayúscula)
  - Teléfonos: solo caracteres permitidos
  - Email: convertir a minúsculas

- **Endpoints utilizados**:
  - `../../controller/cliente.php?op=mostrar` (info cliente)
  - `../../controller/ubicaciones.php?op=mostrar` (cargar ubicación)
  - `../../controller/ubicaciones.php?op=verificarUbicacion` (duplicados)
  - `../../controller/ubicaciones.php?op=guardaryeditar` (guardar)

---

## 🔄 Adaptaciones Realizadas

### Cambios de Nomenclatura (Contacto → Ubicación)

| Contactos | Ubicaciones |
|-----------|-------------|
| `contacto_cliente` | `ubicacion` |
| `id_contacto_cliente` | `id_ubicacion` |
| `nombre_contacto_cliente` | `nombre_ubicacion` |
| `apellidos_contacto_cliente` | - (eliminado) |
| `cargo_contacto_cliente` | - (eliminado) |
| `departamento_contacto_cliente` | - (eliminado) |
| - | `direccion_ubicacion` |
| - | `codigo_postal_ubicacion` |
| - | `poblacion_ubicacion` |
| - | `provincia_ubicacion` |
| - | `pais_ubicacion` |
| `telefono_contacto_cliente` | `telefono_contacto_ubicacion` |
| `movil_contacto_cliente` | - (eliminado) |
| `email_contacto_cliente` | `email_contacto_ubicacion` |
| `extension_contacto_cliente` | - (eliminado) |
| `principal_contacto_cliente` | `es_principal_ubicacion` |
| - | `persona_contacto_ubicacion` |
| `observaciones_contacto_cliente` | `observaciones_ubicacion` |
| `activo_contacto_cliente` | `activo_ubicacion` |

### Campos Nuevos en Ubicaciones
- `direccion_ubicacion` - Dirección completa
- `codigo_postal_ubicacion` - CP
- `poblacion_ubicacion` - Ciudad
- `provincia_ubicacion` - Provincia
- `pais_ubicacion` - País (default: "España")
- `persona_contacto_ubicacion` - Responsable en ubicación

### Campos Eliminados (vs Contactos)
- `apellidos_contacto_cliente`
- `cargo_contacto_cliente`
- `departamento_contacto_cliente`
- `movil_contacto_cliente`
- `extension_contacto_cliente`

---

## 📊 Estructura de DataTables

### Columnas del Listado
1. **Control** - Botón expandir/contraer (+/-)
2. **ID** - id_ubicacion (ordenable)
3. **Nombre** - nombre_ubicacion (filtrable)
4. **Dirección** - direccion_ubicacion (filtrable)
5. **Población** - poblacion_ubicacion (filtrable)
6. **Provincia** - provincia_ubicacion (filtrable)
7. **País** - pais_ubicacion (filtrable)
8. **Persona Contacto** - persona_contacto_ubicacion (filtrable)
9. **Teléfono** - telefono_contacto_ubicacion (filtrable)
10. **Principal** - es_principal_ubicacion (badge Sí/No)
11. **Estado** - activo_ubicacion (badge Activo/Inactivo)
12. **Activar/Desactivar** - Botón acción
13. **Editar** - Botón acción

### Fila Expandida (Detalles)
- Dirección completa
- Código postal
- Email de contacto
- Observaciones
- Fechas (creación/actualización)

---

## 🔗 Flujo de Navegación

```
MntClientes (listado de clientes)
    ↓
MntClientes_ubicaciones/index.php (listado de ubicaciones del cliente)
    ↓
    ├─→ formularioUbicacion.php?modo=nuevo&id_cliente=X (nueva ubicación)
    │       ↓ (guardar)
    │       └─→ Redirige a index.php?id_cliente=X
    │
    └─→ formularioUbicacion.php?modo=editar&id=Y&id_cliente=X (editar ubicación)
            ↓ (actualizar)
            └─→ Redirige a index.php?id_cliente=X
```

---

## ✅ Validaciones Implementadas

### Client-Side (JavaScript)
- ✅ Nombre obligatorio (2-100 caracteres)
- ✅ Email formato válido (si se proporciona)
- ✅ Teléfono formato válido (solo números, espacios, guiones, +, (, ))
- ✅ Verificación de duplicados (nombre único por cliente)
- ✅ Formateo automático de campos

### Server-Side (Controller)
- ✅ Sanitización con htmlspecialchars()
- ✅ Conversión de vacíos a NULL
- ✅ Validación de existencia de ubicación
- ✅ Manejo de ubicación principal (solo una por cliente)
- ✅ Soft delete (activo_ubicacion = 0)

---

## 📝 Endpoints del Controller

### Implementados y Utilizados
1. **listar** - Listado de ubicaciones (con filtro opcional por id_cliente)
2. **guardaryeditar** - INSERT o UPDATE según id_ubicacion
3. **mostrar** - Obtener ubicación por id para edición
4. **eliminar** - Soft delete (desactivar)
5. **activar** - Reactivar ubicación
6. **verificarUbicacion** - Validar nombre único por cliente
7. **selectByCliente** - Ubicaciones activas de un cliente (para dropdowns)

---

## 🎨 Librerías y Recursos

- **Bootstrap 5.0.2** - Framework CSS
- **jQuery 3.7.1** - Manipulación DOM y AJAX
- **DataTables** - Tablas interactivas
- **SweetAlert2 11.7.32** - Modales y alertas
- **Font Awesome 6.4.2** - Iconos
- **AdminLTE** - Template

---

## 🚀 Funcionalidades Clave

1. ✅ **Listado paginado** con búsqueda y filtros por columna
2. ✅ **CRUD completo** (Crear, Leer, Actualizar, Desactivar)
3. ✅ **Soft delete** (mantiene histórico)
4. ✅ **Ubicación principal** (solo una por cliente)
5. ✅ **Validación en tiempo real** con feedback visual
6. ✅ **Responsive** con detalles expandibles en móviles
7. ✅ **Filtrado por cliente** (parámetro GET)
8. ✅ **Ayuda contextual** (modal de ayuda)
9. ✅ **Confirmaciones** para acciones destructivas (SweetAlert2)
10. ✅ **Formateo automático** de campos (mayúsculas, teléfonos, email)

---

## 📦 Integración con el Sistema

### Modelos Relacionados
- `models/Ubicaciones.php` - Acceso a datos
- `models/Clientes.php` - Información del cliente

### Controllers Relacionados
- `controller/ubicaciones.php` - Operaciones de ubicaciones
- `controller/cliente.php` - Información del cliente

### Vistas Relacionadas
- `view/MntClientes/` - Listado de clientes (enlace a ubicaciones)

---

## 🔒 Seguridad

- ✅ Prepared statements en todas las consultas SQL
- ✅ Sanitización de inputs con htmlspecialchars()
- ✅ Validación de tipos de datos en bindValue()
- ✅ Validación server-side + client-side
- ✅ Logging de operaciones (RegistroActividad)
- ✅ Zona horaria configurada (Europe/Madrid)

---

## 📌 Notas de Implementación

1. **Sin invenciones**: Todos los archivos fueron adaptados fielmente desde MntClientes_contacto
2. **Convenciones respetadas**: Nomenclatura con sufijo `_ubicacion`
3. **Compatibilidad**: Compatible con estructura de BD existente (tabla `cliente_ubicacion`)
4. **Vista SQL**: Utiliza `vista_cliente_ubicaciones` para listados
5. **Patrón MVC**: Estricta separación de responsabilidades

---

## ✨ Características Destacadas

### 🎯 Experiencia de Usuario
- Breadcrumbs para navegación
- Información contextual del cliente siempre visible
- Iconos FontAwesome para claridad visual
- Badges de color para estados (principal, activo/inactivo)
- Filtros persistentes con mensaje de alerta
- Loading states en guardado

### 💻 Experiencia de Desarrollador
- Código comentado en español
- Funciones con nombres descriptivos
- Console.log para debugging
- Manejo de errores con try-catch
- Respuestas JSON consistentes
- Código reutilizable y mantenible

---

## 🧪 Testing Recomendado

1. ✅ Crear nueva ubicación con todos los campos
2. ✅ Crear ubicación con solo campos obligatorios
3. ✅ Editar ubicación existente
4. ✅ Verificar validación de duplicados
5. ✅ Marcar/desmarcar ubicación principal
6. ✅ Desactivar y reactivar ubicación
7. ✅ Filtrar por columnas
8. ✅ Probar responsive (móvil/tablet)
9. ✅ Verificar filas expandibles
10. ✅ Validar formato de email y teléfono

---

**Fecha de creación**: 18 de diciembre de 2024  
**Versión**: 1.0  
**Proyecto**: MDR ERP Manager  
**Módulo**: Gestión de Ubicaciones de Clientes  
**Autor**: Sistema de adaptación automatizada
