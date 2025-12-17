# 📁 Estructura de Carpetas del Proyecto MDR

## 🏗️ Arquitectura: MVC (Model-View-Controller)

El proyecto MDR sigue el patrón arquitectónico MVC con una clara separación de responsabilidades, facilitando el mantenimiento y la escalabilidad.

---

## 📂 Carpetas Principales

### 1️⃣ `/config/` - Configuración del Sistema

**Utilidad**: Archivos de configuración y plantillas compartidas

**Contenido**:
- `conexion.json` - Credenciales de base de datos
- `conexion.php` - Clase de conexión a MySQL usando PDO
- `funciones.php` - Funciones globales y utilidades del sistema
- `template/` - Plantillas HTML compartidas (header, sidebar, footer, navegación)
- `test/` - Archivos de pruebas de configuración

**Responsabilidad**: Centralizar la configuración del sistema y proporcionar componentes reutilizables para todas las vistas.

---

### 2️⃣ `/controller/` - Controladores (Lógica de Negocio)

**Utilidad**: Gestionan las peticiones HTTP y coordinan la interacción entre Modelos y Vistas

**Contenido** (más de 30 controladores):
- `presupuesto.php` - Controlador de presupuestos
- `cliente.php` - Controlador de clientes
- `articulo.php` - Controlador de artículos
- `proveedor.php` - Controlador de proveedores
- `login.php` - Controlador de autenticación
- `estado_presupuesto.php` - Controlador de estados de presupuesto
- `familia.php` - Controlador de familias de productos
- Y más...

**Función**: 
- Reciben peticiones AJAX desde las vistas
- Llaman a los métodos de los modelos
- Procesan y validan datos
- Devuelven respuestas en formato JSON
- Gestionan la lógica de negocio

---

### 3️⃣ `/models/` - Modelos (Acceso a Datos)

**Utilidad**: Clases que encapsulan la lógica de acceso a la base de datos

**Contenido** (más de 30 modelos):
- `Presupuesto.php` - Modelo de presupuestos
- `Clientes.php` - Modelo de clientes
- `Articulo.php` - Modelo de artículos
- `Proveedores.php` - Modelo de proveedores
- `Estado_presupuesto.php` - Modelo de estados de presupuesto
- `Familia.php` - Modelo de familias de productos
- Y más...

**Función**:
- Contienen métodos para operaciones CRUD (Create, Read, Update, Delete)
- Ejecutan consultas SQL preparadas (prepared statements)
- Validan datos antes de insertar/actualizar
- Retornan objetos o arrays de datos
- Abstraen la complejidad de las consultas SQL

---

### 4️⃣ `/view/` - Vistas (Interfaz de Usuario)

**Utilidad**: Páginas HTML/PHP que conforman la interfaz visual del usuario

**Contenido** (más de 40 módulos):

#### Módulos Principales:
- `Presupuesto/` - Gestión completa de presupuestos
  - `index.php` - Listado con DataTables
  - `formularioPresupuesto.php` - Crear/editar presupuestos
  - `ayudaPresupuestos.php` - Modal de ayuda
  - `estadisticas.php` - Panel de estadísticas
- `Dashboard/` - Panel principal del sistema
- `MntClientes/` - Mantenimiento de clientes
- `MntArticulos/` - Mantenimiento de artículos
- `MntProveedores/` - Mantenimiento de proveedores
- `MntLogin/` - Vista de inicio de sesión
- `MntElementos/` - Gestión de elementos
- `MntFamilia/` - Gestión de familias

#### Módulos de Consulta:
- `Consulta_Garantias/` - Consultas de garantías
- `Consulta_Mantenimientos/` - Consultas de mantenimientos

#### Módulos de Informes:
- `Informe_mantenimiento/` - Reportes de mantenimiento
- `Informe_vigencia/` - Reportes de vigencia

**Función**:
- Presentan información al usuario
- Capturan inputs de formularios
- Realizan llamadas AJAX a controladores
- Muestran mensajes de éxito/error

---

### 5️⃣ `/public/` - Recursos Públicos

**Utilidad**: Archivos estáticos accesibles desde el navegador

#### Subcarpetas:

**`/css/`** - Hojas de estilo personalizadas
- Estilos propios del sistema
- Extensiones de Bootstrap
- Estilos de componentes específicos

**`/js/`** - Scripts JavaScript personalizados
- Lógica de interacción del cliente
- Validaciones de formularios
- Funciones AJAX
- Scripts específicos por módulo

**`/img/`** - Imágenes del sistema
- Logos
- Iconos
- Imágenes de productos
- Recursos gráficos

**`/lib/`** - Librerías de terceros
- jQuery
- Bootstrap 5
- DataTables
- SweetAlert2
- Chart.js
- Font Awesome
- Y más...

**`/documentos/`** - Archivos generados
- PDFs de presupuestos
- Documentos exportados
- Archivos adjuntos

**`/logs/`** - Archivos de registro
- Logs de errores
- Logs de auditoría
- Logs de acceso

**`/mailTemplate/`** - Plantillas de correo
- Templates HTML para emails
- Plantillas de notificaciones

**`/Services/`** - Servicios auxiliares
- Servicios de terceros
- APIs integradas

---

### 6️⃣ `/BD/` - Base de Datos

**Utilidad**: Scripts SQL y documentación de la base de datos

**Contenido**:
- **`claude_MDR`** - 📘 Estructura completa y documentada de la base de datos
- `almacen.sql` - Gestión de almacén
- `familia.sql` - Familias de productos
- `marca.sql` - Marcas
- `impuesto.sql` - Impuestos
- `estado_presupuesto.sql` - Estados de presupuestos
- `crear_tabla_*.sql` - Scripts de creación de tablas
- `alter_*.sql` - Scripts de alteración de estructura
- `*_ejemplo.sql` - Datos de ejemplo
- `MDR/` - Respaldos de base de datos
- `Importacion_BD_VerI/` - Importaciones de versiones anteriores

**Función**:
- Mantener versionado de la estructura de BD
- Documentar cambios en el esquema
- Proporcionar datos de prueba
- Facilitar migraciones entre entornos

**Archivo Destacado**:
El archivo `claude_MDR` contiene:
- ✅ Definición completa de todas las tablas
- ✅ Relaciones (Foreign Keys)
- ✅ Índices optimizados
- ✅ Vistas SQL
- ✅ Triggers (disparadores)
- ✅ Comentarios y documentación detallada

---

### 7️⃣ `/assets/` - Recursos de Documentación

**Utilidad**: Documentación del proyecto, capturas de pantalla y recursos de diseño

**Contenido**:
- `BD_DEFINICION/` - Definición detallada de base de datos
- `Documentacion/` - Manuales y guías de usuario
- `Pantallas MDR/` - Capturas de pantalla del sistema
- `Reunion/` - Actas de reuniones y decisiones
- `implementacion_lab/` - Documentación de implementación en laboratorio
- `Familias_marcas.sql` - Datos iniciales de familias y marcas
- `prompmt` - Prompts y especificaciones

**Función**:
- Documentar el proceso de desarrollo
- Mantener historial de decisiones
- Proporcionar recursos para capacitación
- Almacenar diseños y mockups

---

### 8️⃣ `/docs/` - Documentación Técnica

**Utilidad**: Documentación técnica en formato Markdown

**Contenido**:
- `campo_boton_editar_nuevo.md` - Guía de botones de edición
- `configuracion-base-datos.md` - Configuración de base de datos
- `fecha_validez_presupuesto.md` - Lógica de validez de presupuestos
- `responsive_datatables.md` - Implementación de tablas responsivas
- `control-accesos-roles.md` - Sistema de permisos y roles
- `estructura_carpetas.md` - Este documento

**Función**:
- Documentar funcionalidades específicas
- Guías de implementación
- Referencias técnicas para desarrolladores
- Documentación de arquitectura

---

### 9️⃣ `/HTML/` - Ejemplos y Prototipos

**Utilidad**: Archivos HTML de prueba, ejemplos y documentación visual

**Contenido**:
- `datatables.html` - Ejemplos de implementación de DataTables
- `ajax-explicacion.html` - Tutoriales de AJAX
- `estructura-mvc-roles.html` - Documentación visual de MVC

**Función**:
- Prototipos rápidos de interfaces
- Ejemplos de código
- Documentación visual
- Pruebas de concepto

---

## 📄 Archivos en la Raíz

| Archivo | Utilidad |
|---------|----------|
| `index.php` | Página de inicio del sistema / redirección principal |
| `.htaccess` | Configuración Apache (reescritura URLs, seguridad) |
| `.gitignore` | Archivos y carpetas ignorados por Git |
| `README.md` | Documentación principal del proyecto |
| `control-accesos-roles.md` | Documentación de roles y permisos |
| `test_php_config.php` | Prueba de configuración PHP |
| `test_trigger.php` | Prueba de triggers de BD |
| `test_vista_elemento.php` | Prueba de vistas SQL |
| `setup_contactos.php` | Script de configuración inicial de contactos |

---

## 🔄 Flujo de Trabajo MVC

```
┌─────────┐      ┌────────────┐      ┌─────────┐      ┌──────────────┐
│ Usuario │ ───> │    VIEW    │ ───> │ CONTROL │ ───> │    MODEL     │
│         │      │ (interfaz) │      │  (PHP)  │      │   (datos)    │
└─────────┘      └────────────┘      └─────────┘      └──────────────┘
     ↑                                      │                  │
     │                                      │                  ↓
     │                                      │           ┌──────────────┐
     │                                      │           │  Base Datos  │
     │                                      │           │   (MySQL)    │
     │                                      ↓           └──────────────┘
     └────────────────────── JSON Response ─────────────────┘
```

### Ejemplo: Crear un Presupuesto

1. **Usuario** rellena formulario en `/view/Presupuesto/formularioPresupuesto.php`
2. **JavaScript** (`formularioPresupuesto.js`) envía petición AJAX a `/controller/presupuesto.php`
3. **Controller** recibe datos, valida y llama a `/models/Presupuesto.php`
4. **Model** ejecuta INSERT en la base de datos usando prepared statements
5. **Model** retorna el resultado al Controller
6. **Controller** devuelve JSON con resultado (`success: true/false`)
7. **View** procesa el JSON y muestra mensaje de éxito/error con SweetAlert2

---

## 🎯 Convenciones de Nombres

### Carpetas de Vistas

- **`Mnt*`** = Mantenimiento (CRUD completo)
  - Ejemplo: `MntClientes/`, `MntArticulos/`, `MntProveedores/`
  - Incluyen: listado, formulario, edición, eliminación

- **`Consulta_*`** = Solo lectura (consultas)
  - Ejemplo: `Consulta_Garantias/`, `Consulta_Mantenimientos/`
  - Solo visualización de datos, sin edición

- **`Informe_*`** = Reportes y estadísticas
  - Ejemplo: `Informe_mantenimiento/`, `Informe_vigencia/`
  - Generación de reportes PDF/Excel

### Archivos

- **Controllers**: minúsculas con guiones bajos
  - Ejemplo: `presupuesto.php`, `estado_presupuesto.php`

- **Models**: PascalCase (primera letra mayúscula)
  - Ejemplo: `Presupuesto.php`, `Estado_presupuesto.php`

- **Views**: según funcionalidad
  - `index.php` - Listado principal
  - `formulario*.php` - Formularios de entrada
  - `ayuda*.php` - Modales de ayuda
  - `estadisticas.php` - Paneles de métricas

---

## 🗄️ Estructura de Base de Datos

### Tablas Principales

- **`presupuesto`** - Presupuestos con toda su información
- **`cliente`** - Clientes del sistema
- **`articulo`** - Artículos/productos
- **`familia`** - Familias de productos
- **`marca`** - Marcas de productos
- **`proveedor`** - Proveedores
- **`estado_presupuesto`** - Estados del ciclo de vida de presupuestos
- **`forma_pago`** - Formas de pago configuradas
- **`impuesto`** - Tipos de impuestos (IVA, etc.)
- **`usuario`** - Usuarios del sistema
- **`rol`** - Roles y permisos

### Vistas SQL

- **`vista_presupuesto_completo`** - Vista con toda la información de presupuestos
- **`vista_familia_unidad_media`** - Relación familias-unidades de medida

### Triggers (Disparadores)

- **`trg_presupuesto_before_desactivar`** - Sincroniza desactivación con estado cancelado
- **`trg_presupuesto_before_reactivar`** - Sincroniza reactivación con estado en proceso
- **`trg_presupuesto_estado_cancelado`** - Desactiva automáticamente al cancelar
- **`trg_presupuesto_estado_no_cancelado`** - Reactiva automáticamente al cambiar de cancelado

---

## 🚀 Tecnologías Utilizadas

### Backend
- **PHP 7.4+** con PDO (PHP Data Objects)
- **MySQL/MariaDB** como base de datos
- **Arquitectura MVC** pura sin frameworks

### Frontend
- **HTML5** semántico
- **CSS3** con Flexbox y Grid
- **JavaScript ES6+** moderno
- **Bootstrap 5** para diseño responsivo
- **jQuery 3.x** para manipulación DOM y AJAX

### Librerías JavaScript
- **DataTables** - Tablas interactivas con paginación y búsqueda
- **SweetAlert2** - Alertas y confirmaciones elegantes
- **Chart.js** - Gráficos y estadísticas
- **Font Awesome / Bootstrap Icons** - Iconografía
- **Select2** - Selectores mejorados

### Herramientas de Desarrollo
- **Git** - Control de versiones
- **Apache** - Servidor web
- **Composer** (opcional) - Gestión de dependencias PHP

---

## 📊 Resumen Visual de la Estructura

```
MDR/
├── 📁 config/          → Configuración y plantillas compartidas
│   ├── conexion.json
│   ├── conexion.php
│   ├── funciones.php
│   └── template/
│
├── 📁 controller/      → Lógica de negocio (30+ archivos)
│   ├── presupuesto.php
│   ├── cliente.php
│   ├── articulo.php
│   └── ...
│
├── 📁 models/          → Acceso a datos (30+ clases)
│   ├── Presupuesto.php
│   ├── Clientes.php
│   ├── Articulo.php
│   └── ...
│
├── 📁 view/            → Interfaces de usuario (40+ módulos)
│   ├── Presupuesto/
│   ├── Dashboard/
│   ├── MntClientes/
│   └── ...
│
├── 📁 public/          → Recursos públicos
│   ├── css/
│   ├── js/
│   ├── img/
│   ├── lib/
│   └── documentos/
│
├── 📁 BD/              → Scripts SQL y estructura
│   ├── claude_MDR      ← ⭐ ARCHIVO PRINCIPAL DE BD
│   └── *.sql
│
├── 📁 assets/          → Documentación y diseño
│   ├── BD_DEFINICION/
│   ├── Documentacion/
│   └── Pantallas MDR/
│
├── 📁 docs/            → Documentación técnica markdown
│   ├── estructura_carpetas.md
│   └── *.md
│
├── 📁 HTML/            → Prototipos y ejemplos
│
└── 📄 index.php        → Punto de entrada principal
```

---

## 🔒 Seguridad Implementada

- ✅ **Prepared Statements** - Prevención de SQL Injection
- ✅ **Validación de sesiones** - Control de acceso
- ✅ **Sistema de roles** - Permisos granulares
- ✅ **CSRF Protection** - Tokens en formularios
- ✅ **Encriptación de contraseñas** - Hashing seguro
- ✅ **Validación de inputs** - Cliente y servidor
- ✅ **Logs de auditoría** - Registro de acciones

---

## 📈 Escalabilidad

La estructura MVC permite:
- ✅ Añadir nuevos módulos sin afectar los existentes
- ✅ Modificar la lógica de negocio sin tocar las vistas
- ✅ Cambiar el diseño sin alterar la funcionalidad
- ✅ Migrar a frameworks PHP en el futuro si es necesario
- ✅ Implementar APIs REST reutilizando los modelos
- ✅ Trabajo en equipo con separación clara de responsabilidades

---

## 📝 Notas Adicionales

### Buenas Prácticas Implementadas

1. **Separación de responsabilidades** (MVC)
2. **Código reutilizable** (funciones compartidas en `/config/`)
3. **Nomenclatura consistente** (convenciones claras)
4. **Documentación inline** (comentarios en código)
5. **Versionado de BD** (scripts SQL organizados)
6. **Manejo de errores** (try-catch, logs)
7. **Responsive design** (Bootstrap 5)
8. **Accesibilidad** (WAI-ARIA labels)

### Próximas Mejoras Sugeridas

- 🔄 Implementar caching (Redis/Memcached)
- 🔄 Añadir tests automatizados (PHPUnit)
- 🔄 Migrar a Composer para autoloading
- 🔄 Implementar API REST para integraciones
- 🔄 Dockerizar el proyecto
- 🔄 CI/CD con GitHub Actions

---

**Última actualización**: 14 de diciembre de 2025  
**Versión del documento**: 1.0  
**Autor**: Equipo MDR
