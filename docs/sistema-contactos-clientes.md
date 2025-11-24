# Sistema de Gestión de Contactos de Clientes

## Descripción
Sistema completo de gestión de contactos para clientes implementado en el framework MDR. Basado en la estructura del sistema de contactos de proveedores y adaptado para la gestión de contactos de clientes.

## Archivos Creados

### Modelo (Model)
- **`models/Clientes_contacto.php`** - Clase para manejo de datos de contactos de clientes
  - Operaciones CRUD completas
  - Verificación de contactos duplicados
  - Configuración de zona horaria Europe/Madrid
  - Logging de actividades

### Controlador (Controller)
- **`controller/clientes_contacto.php`** - API REST para operaciones de contactos
  - `listar` - Lista contactos con filtro opcional por cliente
  - `guardaryeditar` - Crear/actualizar contactos
  - `mostrar` - Obtener detalles de un contacto específico
  - `verificarContactoCliente` - Verificar contactos duplicados
  - `eliminar` - Desactivar contactos
  - `activar` - Reactivar contactos

### Vistas (Views)
- **`view/MntClientes_contacto/index.php`** - Listado principal con DataTables
  - Tabla responsiva con filtros
  - Filtro por cliente
  - Botones de acción (editar, activar/desactivar)
  - Detalles expandibles

- **`view/MntClientes_contacto/formularioContactoCliente.php`** - Formulario de creación/edición
  - Formulario responsivo con Bootstrap 5
  - Validación cliente y servidor
  - Selector de cliente
  - Campos completos de información de contacto
  - Sistema de ayuda contextual

### JavaScript
- **`view/MntClientes_contacto/mntclientes_contacto.js`** - Gestión de DataTables
  - Configuración avanzada de DataTables
  - Filtros por columna
  - Expansión de detalles
  - AJAX para operaciones CRUD

- **`view/MntClientes_contacto/formularioContactoCliente.js`** - Gestión del formulario
  - Validación en tiempo real
  - Carga de datos para edición
  - Verificación de duplicados
  - Control de cambios no guardados

## Integración con Sistema de Clientes

El sistema se integra con el módulo existente de clientes modificando:

### `view/MntClientes/mntclientes.js`
- **Línea ~270**: Modificado el botón "Formulario" para dirigir a contactos
- **Icono**: Cambiado de `fa-file-alt` a `fas fa-users` 
- **Tooltip**: Cambiado de "Formulario" a "Contactos"
- **Función**: Redirige a `../MntClientes_contacto/index.php?id_cliente=${id}`

## Estructura de Base de Datos

### Tabla `contact_cliente`
```sql
- id_contacto_cliente (INT, PK, AUTO_INCREMENT)
- id_cliente (INT, FK a cliente.id_cliente)
- nombre_contacto_cliente (VARCHAR)
- apellidos_contacto_cliente (VARCHAR)
- cargo_contacto_cliente (VARCHAR)
- departamento_contacto_cliente (VARCHAR)
- telefono_contacto_cliente (VARCHAR)
- movil_contacto_cliente (VARCHAR)
- email_contacto_cliente (VARCHAR)
- extension_contacto_cliente (VARCHAR)
- principal_contacto_cliente (TINYINT)
- observaciones_contacto_cliente (TEXT)
- activo_contacto_cliente (TINYINT)
- created_at_contacto_cliente (TIMESTAMP)
- updated_at_contacto_cliente (TIMESTAMP)
```

## Funcionalidades

### ✅ Operaciones CRUD Completas
- Crear nuevos contactos
- Leer/listar contactos existentes
- Actualizar información de contactos
- Eliminar (desactivar) contactos

### ✅ Validaciones
- Nombres de contacto únicos por cliente
- Validación de email
- Campos obligatorios
- Verificación de duplicados

### ✅ Filtros y Búsquedas
- Filtro por cliente específico
- Búsqueda por cualquier campo
- Filtros por columna en DataTables

### ✅ Interfaz Responsiva
- Compatible con dispositivos móviles
- Bootstrap 5
- DataTables responsivo
- Formularios adaptativos

### ✅ Sistema de Ayuda
- Tooltips informativos
- Modal de ayuda contextual
- Validación visual en tiempo real

## Navegación

### Desde Listado de Clientes
1. Clic en botón de "Contactos" (icono de usuarios)
2. Acceso directo a contactos del cliente seleccionado

### URLs de Acceso
- **Listado general**: `/view/MntClientes_contacto/index.php`
- **Filtrado por cliente**: `/view/MntClientes_contacto/index.php?id_cliente={ID}`
- **Nuevo contacto**: `/view/MntClientes_contacto/formularioContactoCliente.php`
- **Editar contacto**: `/view/MntClientes_contacto/formularioContactoCliente.php?modo=editar&id={ID}`

## Patrones Implementados

### MVC (Modelo-Vista-Controlador)
- Separación clara de responsabilidades
- Modelo para acceso a datos
- Controlador para lógica de negocio  
- Vista para presentación

### RESTful API
- Operaciones estándar via parámetro `op`
- Respuestas JSON estructuradas
- Códigos de estado apropiados

### DataTables Pattern
- Configuración centralizada
- Filtros dinámicos
- Paginación automática
- Responsive design

## Consideraciones de Seguridad

### Validación Doble
- Cliente (JavaScript)
- Servidor (PHP)

### Escape de Datos
- Prevención de XSS
- Sanitización de inputs
- Prepared statements

### Control de Acceso
- Verificación de sesión
- Validación de permisos
- Logging de actividades

## Mantenimiento

### Logging
- Todas las operaciones se registran
- Timestamps con zona horaria Europa/Madrid
- Trazabilidad completa

### Escalabilidad
- Diseño modular
- Fácil extensión
- Patrones estándar del framework

## Próximos Pasos

1. **Testing**: Pruebas de integración con módulo de clientes
2. **Validación**: Verificar estructura de tabla `contact_cliente`
3. **Refinamiento**: Ajustes basados en feedback de usuario
4. **Documentación**: Actualizar documentación de usuario final

---

**Estado**: ✅ **Completado y listo para testing**  
**Versión**: 1.0  
**Fecha**: 2025-01-16  
**Autor**: Sistema MDR