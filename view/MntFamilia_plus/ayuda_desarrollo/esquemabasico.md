# Esquema Básico - Módulo de Gestión de Familias

## 📋 Características del Modelo

Este modelo incluye:

- ✅ **CRUD de Familias**: Crear, leer, actualizar y eliminar registros de familias
- 📸 **Gestión de Fotografía**: Sistema completo de subida y gestión de imágenes integrado en la tabla de familias
- 🔍 **Búsquedas Ampliadas**: Filtros avanzados en los pies de DataTables para búsqueda granular por cada columna
- 📋 **Formulario Independiente**: Formulario de inserción y modificación en página separada (no modal)

---

## 🏗️ Estructura de Archivos Fundamentales

### 📁 Base de Datos
```
BD/
└── familia.sql                    # Estructura de la tabla familia con campo imagen_familia
```

### 🎛️ Controlador (Lógica de Negocio)
```
controller/
└── familia.php                    # Controlador principal con operaciones CRUD
                                   # Incluye procesamiento de imágenes
                                   # Casos: listar, guardaryeditar, mostrar, eliminar, etc.
```

### 🏛️ Modelo (Acceso a Datos)
```
models/
└── Familia.php                    # Clase modelo con métodos de base de datos
                                   # get_familia(), insert_familia(), update_familia()
                                   # delete_familiaxid(), verificarFamilia()
```

### 🎨 Vista (Interfaz de Usuario)
```
view/MntFamilia_plus/
├── index.php                      # Página principal con DataTable y listado
├── formularioFamilia.php           # Formulario independiente de creación/edición (NO modal)
├── mntfamilia.js                  # JavaScript principal con DataTables configurado
├── formularioFamilia.js           # JavaScript del formulario independiente con validaciones
└── ayudaFamilias.php              # Modal de ayuda del módulo
```

### ⚙️ Configuración
```
config/
├── conexion.php                   # Clase de conexión a base de datos
├── conexion.json                  # Configuración de conexión DB
├── funciones.php                  # Funciones auxiliares y RegistroActividad
└── template/                      # Plantillas de interfaz
    ├── mainHead.php
    ├── mainHeader.php
    ├── mainSidebar.php
    └── verificarPermiso.php
```

### 📁 Recursos Públicos
```
public/
├── img/familia/                   # Directorio de imágenes de familias
├── logs/                          # Archivos de registro y debug
└── assets/                        # CSS, JS y recursos estáticos
```

---

## 🔄 Flujo de Funcionamiento

### 1. **Visualización de Datos**
```
index.php → mntfamilia.js → controller/familia.php?op=listar → models/Familia.php
```

### 2. **Crear Nueva Familia**
```
index.php → [Botón "Nueva Familia"] → formularioFamilia.php?modo=nuevo → formularioFamilia.js → controller/familia.php?op=guardaryeditar → models/Familia.php
```

### 3. **Editar Familia Existente**
```
index.php → [Botón "Editar"] → formularioFamilia.php?modo=edicion&id=[ID] → formularioFamilia.js → controller/familia.php?op=guardaryeditar → models/Familia.php
```

### 4. **Gestión de Imágenes**
```
Formulario con input[type="file"] → procesarImagenFamilia() → public/img/familia/
```

### 5. **Búsquedas Avanzadas**
```
DataTables tfoot inputs → Filtros por columna → Ajax requests dinámicos
```

---

## 🗃️ Estructura de Base de Datos

### Tabla: `familia`
```sql
- id_familia (INT, AUTO_INCREMENT, PRIMARY KEY)
- codigo_familia (VARCHAR(20), UNIQUE)
- nombre_familia (VARCHAR(100))
- name_familia (VARCHAR(100)) -- Nombre en inglés
- descr_familia (VARCHAR(255))
- imagen_familia (VARCHAR(255)) -- 🎯 Campo para gestión de fotografías
- activo_familia (BOOLEAN)
- created_at_familia (TIMESTAMP)
- updated_at_familia (TIMESTAMP)
```

---

## 🎯 Funcionalidades Destacadas

### 📸 Gestión de Fotografías
- **Validación**: Tipos permitidos (JPEG, PNG, GIF)
- **Seguridad**: Verificación con `finfo` del tipo real de archivo
- **Tamaño**: Límite de 2MB por imagen
- **Almacenamiento**: Nombres únicos con `uniqid()`
- **Ubicación**: `public/img/familia/`

### 🔍 Búsquedas Ampliadas en DataTables
- **Footer Inputs**: Campo de búsqueda en cada columna
- **Filtros por Estado**: Select específico para activo/inactivo
- **Búsqueda en Tiempo Real**: Ajax dinámico conforme se escribe
- **Filtros Persistentes**: Mantiene estado durante la sesión

### � Formulario Independiente de Gestión
- **Página Dedicada**: `formularioFamilia.php` como página completa separada del index
- **Navegación Fluida**: Botones de "Nueva Familia" y "Editar" redirigen al formulario
- **Modo Dual**: Detección automática de modo (nuevo/edición) mediante parámetros URL
- **Validación Completa**: JavaScript dedicado (`formularioFamilia.js`) con validaciones específicas
- **Gestión de Estado**: Manejo independiente del estado del formulario
- **Interfaz Mejorada**: Diseño enfocado exclusivamente en la edición/creación

### �📝 Sistema de Logs
- **Registro de Actividades**: Todas las operaciones CRUD se registran
- **Debug Avanzado**: Logs detallados para subida de imágenes
- **Múltiples Ubicaciones**: Directorios fallback para logs

---

## 🚀 Operaciones CRUD Disponibles

| Operación | Endpoint | Método | Descripción |
|-----------|----------|---------|-------------|
| **Create** | `?op=guardaryeditar` | POST | Crear nueva familia con imagen |
| **Read** | `?op=listar` | GET | Listar todas las familias |
| **Read** | `?op=mostrar` | POST | Obtener familia específica |
| **Update** | `?op=guardaryeditar` | POST | Actualizar familia existente |
| **Delete** | `?op=eliminar` | POST | Desactivar familia (soft delete) |
| **Activate** | `?op=activar` | POST | Reactivar familia |
| **Validate** | `?op=verificarFamilia` | GET | Verificar duplicados |

---

*Generado el 5 de noviembre de 2025*