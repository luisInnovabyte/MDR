# Esquema Básico - Módulo de Gestión de Familias

## 📋 Características del Modelo

Este modelo incluye:

- ✅ **CRUD de Familias**: Crear, leer, actualizar y eliminar registros de familias
- 🔍 **Búsquedas Ampliadas**: Filtros avanzados en los pies de DataTables para búsqueda granular por cada columna

---

## 🏗️ Estructura de Archivos Fundamentales

### 📁 Base de Datos
```
BD/
└── familia.sql                    # Estructura de la tabla familia
```

### 🎛️ Controlador (Lógica de Negocio)
```
controller/
└── familia.php                    # Controlador principal con operaciones CRUD
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
view/MntFamilia/
├── index.php                      # Página principal con DataTable
├── mantenimientoFamilias.php       # Modal de creación/edición de familias
├── mntfamilia.js                  # JavaScript principal con DataTables configurado
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
├── logs/                          # Archivos de registro y debug
└── assets/                        # CSS, JS y recursos estáticos
```

---

## 🔄 Flujo de Funcionamiento

### 1. **Visualización de Datos**
```
index.php → mntfamilia.js → controller/familia.php?op=listar → models/Familia.php
```

### 2. **Crear/Editar Familia**
```
index.php → Modal (mantenimientoFamilias.php) → mntfamilia.js → controller/familia.php?op=guardaryeditar → models/Familia.php
```

### 3. **Búsquedas Avanzadas**
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
- activo_familia (BOOLEAN)
- created_at_familia (TIMESTAMP)
- updated_at_familia (TIMESTAMP)
```

---

## 🎯 Funcionalidades Destacadas

### 🔍 Búsquedas Ampliadas en DataTables
- **Footer Inputs**: Campo de búsqueda en cada columna
- **Filtros por Estado**: Select específico para activo/inactivo
- **Búsqueda en Tiempo Real**: Ajax dinámico conforme se escribe
- **Indicador de Filtros**: Alerta visual cuando hay filtros activos
- **Limpieza de Filtros**: Botón para resetear todos los filtros

### 📝 Sistema de Logs
- **Registro de Actividades**: Todas las operaciones CRUD se registran
- **Control de Acceso**: Verificación de permisos por módulo

### 🖥️ Interfaz de Usuario
- **Modal Responsivo**: Formulario de edición en modal Bootstrap
- **Validación de Campos**: FormValidator integrado
- **Notificaciones**: Sistema de alertas con Toastr y SweetAlert
- **Responsive Design**: Adaptable a diferentes tamaños de pantalla

---

## 🚀 Operaciones CRUD Disponibles

| Operación | Endpoint | Método | Descripción |
|-----------|----------|---------|-------------|
| **Create** | `?op=guardaryeditar` | POST | Crear nueva familia |
| **Read** | `?op=listar` | GET | Listar todas las familias |
| **Read** | `?op=mostrar` | POST | Obtener familia específica |
| **Update** | `?op=guardaryeditar` | POST | Actualizar familia existente |
| **Delete** | `?op=eliminar` | POST | Desactivar familia (soft delete) |
| **Activate** | `?op=activar` | POST | Reactivar familia |
| **Validate** | `?op=verificarFamilia` | GET | Verificar duplicados |

---

## 🔧 Características Técnicas

### DataTables Configuración
- **Processing**: Indicador de carga durante operaciones
- **Responsive**: Adaptable a dispositivos móviles
- **Custom Pagination**: Navegación con iconos Bootstrap
- **Column Search**: Búsqueda individual por columna
- **State Management**: Persistencia de filtros durante la sesión

### Validaciones
- **Campos Obligatorios**: Código y nombre de familia
- **Duplicados**: Verificación automática antes de guardar
- **FormValidator**: Validación del lado cliente
- **Sanitización**: Limpieza de datos en el servidor

---

*Generado el 5 de noviembre de 2025*