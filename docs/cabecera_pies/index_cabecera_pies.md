# Sistema Cabecera-Pies con DataTables
## Documentación del módulo de Artículos

> **Sistema de referencia:** MDR ERP Manager  
> **Módulo:** Mantenimiento de Artículos (MntArticulos)  
> **Fecha:** 23 de diciembre de 2025  
> **Versión:** 1.0

---

## 📋 Índice General

Este sistema implementa un patrón de **cabecera-pies** donde:
- **Cabecera**: Tabla DataTables con listado de registros
- **Pies**: Formulario independiente para crear/editar registros

### Archivos de Documentación

La documentación está dividida en los siguientes archivos para facilitar su lectura:

1. **[Estructura del Index](./index_cabecera_pies_estructura.md)**
   - Estructura HTML completa
   - Integración de plantillas
   - Panel de estadísticas
   - Sistema de filtros
   - Tabla DataTables

2. **[Configuración DataTables](./index_cabecera_pies_datatables.md)**
   - Configuración completa de DataTables
   - Definición de columnas
   - Renderizado personalizado
   - Agrupación por familia (rowGroup)
   - Detalles expandibles (child rows)

3. **[Funciones JavaScript](./index_cabecera_pies_js_funciones.md)**
   - Funciones CRUD completas
   - Sistema de filtros
   - Alertas y confirmaciones
   - Funciones auxiliares
   - Manejo de eventos

4. **[Controller y Backend](./index_cabecera_pies_controller.md)**
   - Estructura del controller
   - Operaciones estándar (listar, guardar, editar, eliminar)
   - Respuestas JSON
   - Manejo de errores

5. **[Formulario y Ayuda](./index_cabecera_pies_formulario.md)**
   - Estructura del formulario independiente
   - JavaScript del formulario
   - Modal de ayuda
   - Validaciones

6. **[Guía de Replicación](./index_cabecera_pies_replicacion.md)**
   - Pasos para replicar el sistema
   - Checklist completo
   - Adaptaciones necesarias
   - Mejores prácticas

---

## 🎯 Visión General del Sistema

### Arquitectura MVC

```
┌─────────────────────────────────────────────────────────────┐
│                        VIEW (Vista)                          │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ index.php (Listado con DataTables)                   │   │
│  │  - Panel de estadísticas                              │   │
│  │  - Tabla con filtros en pies                         │   │
│  │  - Botones de acción (editar, eliminar, activar)     │   │
│  │  - Detalles expandibles                              │   │
│  └──────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ mntarticulo.js (Lógica cliente)                      │   │
│  │  - Configuración DataTables                          │   │
│  │  - Manejo de eventos                                 │   │
│  │  - Funciones CRUD                                    │   │
│  │  - Sistema de filtros                                │   │
│  └──────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ formularioArticulo.php (Crear/Editar)                │   │
│  │  - Formulario independiente                          │   │
│  │  - Validaciones                                      │   │
│  │  - Subida de archivos                                │   │
│  └──────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ ayudaArticulos.php (Modal de ayuda)                  │   │
│  │  - Documentación del módulo                          │   │
│  │  - Ejemplos de uso                                   │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                   CONTROLLER (Controlador)                   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ articulo.php (Lógica de negocio)                     │   │
│  │  - Switch por operación (?op=...)                    │   │
│  │  - listar: Listado para DataTables                   │   │
│  │  - guardaryeditar: INSERT/UPDATE                     │   │
│  │  - mostrar: Obtener registro por ID                  │   │
│  │  - eliminar: Soft delete                             │   │
│  │  - activar: Reactivar registro                       │   │
│  │  - estadisticas: Contadores del panel                │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                      MODEL (Modelo)                          │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Articulo.php (Acceso a datos)                        │   │
│  │  - Conexión PDO                                      │   │
│  │  - get_articulos(): Listar todos                     │   │
│  │  - get_articuloxid($id): Obtener por ID             │   │
│  │  - insert_articulo(): Insertar nuevo                 │   │
│  │  - update_articulo(): Actualizar                     │   │
│  │  - delete_articuloxid(): Soft delete                 │   │
│  │  - activar_articuloxid(): Reactivar                  │   │
│  │  - total_articulo(): Estadísticas                    │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
                              │
                              ▼
┌─────────────────────────────────────────────────────────────┐
│                    BASE DE DATOS (MySQL)                     │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Tabla: articulo                                      │   │
│  │  - id_articulo (PK)                                  │   │
│  │  - codigo_articulo (UNIQUE)                          │   │
│  │  - nombre_articulo                                   │   │
│  │  - id_familia (FK)                                   │   │
│  │  - precio_alquiler_articulo                          │   │
│  │  - es_kit_articulo                                   │   │
│  │  - activo_articulo (soft delete)                     │   │
│  │  - created_at_articulo                               │   │
│  │  - updated_at_articulo                               │   │
│  └──────────────────────────────────────────────────────┘   │
│  ┌──────────────────────────────────────────────────────┐   │
│  │ Vista: vista_articulo_completa                       │   │
│  │  - JOIN con familia, grupo, unidad                   │   │
│  │  - Campos calculados y heredados                     │   │
│  └──────────────────────────────────────────────────────┘   │
└─────────────────────────────────────────────────────────────┘
```

---

## 🔑 Características Principales

### 1. **Panel de Estadísticas**
- Total de artículos
- Artículos activos
- Artículos que son kits
- Artículos con coeficientes
- Actualización automática vía AJAX

### 2. **DataTables Avanzado**
- **Agrupación por familia** (rowGroup)
- **Detalles expandibles** (child rows)
- **Filtros en pies de columna**
- **Búsqueda global**
- **Ordenación personalizada**
- **Renderizado condicional** de columnas
- **Responsive** y adaptable

### 3. **Operaciones CRUD**
- **Crear**: Formulario independiente
- **Leer**: Vista con DataTables
- **Actualizar**: Formulario independiente
- **Eliminar**: Soft delete con confirmación
- **Activar**: Reactivación de registros

### 4. **Filtros Inteligentes**
- Filtro por código
- Filtro por nombre
- Filtro por familia
- Filtro por precio
- Filtro por tipo (kit/no kit)
- Filtro por coeficientes
- Filtro por estado (activo/inactivo)
- **Alerta visual** de filtros activos
- **Botón de limpieza** rápida

### 5. **Sistema de Ayuda**
- Modal con documentación completa
- Acordeón de campos
- Ejemplos prácticos
- Buenas prácticas

---

## 📁 Estructura de Archivos

```
view/MntArticulos/
├── index.php                    # Listado principal con DataTables
├── mntarticulo.js              # JavaScript del listado
├── formularioArticulo.php      # Formulario crear/editar
├── formularioArticulo.js       # JavaScript del formulario
└── ayudaArticulos.php          # Modal de ayuda

controller/
└── articulo.php                # Controller con operaciones CRUD

models/
└── Articulo.php                # Modelo de acceso a datos

BD/
└── claude_MDR                  # Script completo de base de datos
```

---

## 🚀 Tecnologías Utilizadas

| Tecnología | Versión | Uso |
|------------|---------|-----|
| **PHP** | 8.x | Backend y lógica de negocio |
| **MySQL/MariaDB** | 8.x | Base de datos |
| **jQuery** | 3.7.1 | Manipulación DOM y AJAX |
| **DataTables** | 2.x | Tablas interactivas |
| **Bootstrap** | 5.x | Framework CSS |
| **SweetAlert2** | 11.x | Alertas y confirmaciones |
| **Bootstrap Icons** | 1.x | Iconografía |
| **Toastr** | 2.x | Notificaciones toast |

---

## 📊 Flujo de Datos

### Carga Inicial
```
1. Usuario accede a index.php
   ↓
2. PHP carga estadísticas del modelo
   ↓
3. Se renderiza HTML con panel de estadísticas
   ↓
4. JavaScript inicializa DataTables
   ↓
5. AJAX solicita datos a articulo.php?op=listar
   ↓
6. Controller consulta modelo
   ↓
7. Modelo ejecuta consulta SQL con JOIN
   ↓
8. Controller formatea respuesta JSON
   ↓
9. DataTables renderiza la tabla con datos
```

### Operación de Edición
```
1. Usuario hace clic en botón "Editar"
   ↓
2. JavaScript captura evento y obtiene ID
   ↓
3. Redirección a formularioArticulo.php?modo=editar&id=XX
   ↓
4. PHP carga datos del artículo
   ↓
5. Se renderiza formulario con datos prellenados
   ↓
6. Usuario modifica y envía formulario
   ↓
7. JavaScript valida datos
   ↓
8. AJAX envía a articulo.php?op=guardaryeditar
   ↓
9. Controller valida y actualiza en BD
   ↓
10. Respuesta JSON con resultado
   ↓
11. Redirección a index.php con mensaje
```

### Operación de Eliminación (Soft Delete)
```
1. Usuario hace clic en botón "Eliminar"
   ↓
2. JavaScript muestra confirmación SweetAlert2
   ↓
3. Usuario confirma
   ↓
4. AJAX POST a articulo.php?op=eliminar
   ↓
5. Controller ejecuta soft delete (activo=0)
   ↓
6. Modelo actualiza registro
   ↓
7. Respuesta JSON con resultado
   ↓
8. JavaScript recarga DataTables
   ↓
9. JavaScript actualiza estadísticas
   ↓
10. Notificación de éxito
```

---

## 🎨 Componentes Visuales

### Panel de Estadísticas
```html
<div class="row row-sm mb-4">
    <div class="col-lg-3">
        <div class="card shadow-sm border-primary">
            <div class="card-body text-center">
                <i class="bi bi-box-seam text-primary"></i>
                <h6>Total Artículos</h6>
                <h2>150</h2>
            </div>
        </div>
    </div>
    <!-- Más tarjetas... -->
</div>
```

### Tabla con Filtros en Pies
```html
<table id="articulos_data" class="table">
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <!-- Más columnas... -->
        </tr>
    </thead>
    <tfoot>
        <tr>
            <th><input type="text" placeholder="Buscar código" /></th>
            <th><input type="text" placeholder="Buscar nombre" /></th>
            <!-- Más filtros... -->
        </tr>
    </tfoot>
</table>
```

### Alerta de Filtros Activos
```html
<div class="alert alert-warning" id="filter-alert" style="display: none;">
    <i class="fas fa-filter"></i>
    <span>Filtros aplicados</span>
    <button id="clear-filter">Limpiar filtros</button>
</div>
```

---

## 📖 Convenciones del Proyecto

### Nomenclatura de Archivos
- `index.php` - Listado principal
- `mnt[entidad].js` - JavaScript del listado
- `formulario[Entidad].php` - Formulario crear/editar
- `formulario[Entidad].js` - JavaScript del formulario
- `ayuda[Entidad].php` - Modal de ayuda

### Nomenclatura de Funciones JavaScript
- `desac[Entidad]()` - Desactivar registro
- `activar[Entidad]()` - Activar registro
- `mostrar[Entidad]()` - Cargar datos para editar
- `guardar[Entidad]()` - Guardar/actualizar

### Operaciones del Controller
- `?op=listar` - Listado para DataTables
- `?op=guardaryeditar` - Crear o actualizar
- `?op=mostrar` - Obtener por ID
- `?op=eliminar` - Soft delete
- `?op=activar` - Reactivar
- `?op=estadisticas` - Contadores

---

## ✅ Ventajas de este Sistema

1. **Separación clara**: Listado independiente del formulario
2. **Navegación fluida**: URLs amigables con parámetros GET
3. **Escalable**: Fácil agregar nuevas funcionalidades
4. **Mantenible**: Código organizado y documentado
5. **Reutilizable**: Patrón replicable en otros módulos
6. **Performance**: Carga bajo demanda con AJAX
7. **UX mejorada**: Filtros, agrupación y detalles
8. **Responsive**: Adaptable a dispositivos móviles

---

## 📚 Siguientes Pasos

Para implementar este sistema en tu módulo:

1. Lee la **[Estructura del Index](./index_cabecera_pies_estructura.md)**
2. Estudia la **[Configuración DataTables](./index_cabecera_pies_datatables.md)**
3. Revisa las **[Funciones JavaScript](./index_cabecera_pies_js_funciones.md)**
4. Comprende el **[Controller](./index_cabecera_pies_controller.md)**
5. Analiza el **[Formulario](./index_cabecera_pies_formulario.md)**
6. Sigue la **[Guía de Replicación](./index_cabecera_pies_replicacion.md)**

---

## 💡 Soporte

Para dudas o mejoras:
- Revisa el código fuente en `view/MntArticulos/`
- Consulta el modal de ayuda en `ayudaArticulos.php`
- Revisa la documentación del proyecto en `.github/copilot-instructions.md`

---

**Documentación generada por:** Claude Sonnet 4.5  
**Fecha:** 23 de diciembre de 2025  
**Proyecto:** MDR ERP Manager
