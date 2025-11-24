# Implementación del Campo coeficiente_familia

## Resumen de Cambios

Se ha agregado el campo `coeficiente_familia` como un checkbox al formulario de mantenimiento de familias. Este campo controla si a una familia se le pueden aplicar coeficientes de descuento, funcionalidad que se arrastrará a los artículos y podrá modificarse en los presupuestos.

## Archivos Modificados

### 1. Base de Datos
- **BD/alter_coeficiente_familia.sql** - Script para agregar el campo a la tabla familia
- **BD/alter_vista_familia_unidad_media.sql** - Script para actualizar la vista que incluye el nuevo campo

### 2. Frontend - Formulario (Nuevo/Editar)
- **view/MntFamilia_unidad/formularioFamilia.php**:
  - Agregado checkbox para coeficiente_familia
  - Actualizada documentación de ayuda
  - Agregado tooltip explicativo
  - Reorganizada distribución de campos en 3 columnas (antes eran 2)

- **view/MntFamilia_unidad/formularioFamilia.js**:
  - Agregado manejo del checkbox coeficiente_familia
  - Incluido en captura de valores originales para detectar cambios
  - Agregado al FormData para envío al servidor
  - Actualizado texto dinámico del estado del checkbox

### 3. Frontend - Listado
- **view/MntFamilia_unidad/index.php**:
  - Agregada columna "Coeficientes" a la tabla
  - Agregado filtro select para coeficientes en el footer

- **view/MntFamilia_unidad/mntfamilia.js**:
  - Nueva columna coeficiente_familia en configuración DataTables
  - Renderizado con iconos (✓ para permite, ✗ para no permite)
  - Agregado en función format() para detalles expandibles
  - Actualizado índice de columna estado (de 4 a 6)

### 4. Backend - Controlador
- **controller/familia_unidad.php**:
  - Agregada captura del campo coeficiente_familia en operación guardaryeditar
  - Incluido en respuestas JSON de listar y listarDisponibles
  - Pasado a métodos insert y update del modelo

### 5. Backend - Modelo
- **models/Familia_unidad.php**:
  - Método insert_familia: agregado parámetro coeficiente_familia con valor por defecto 1
  - Método update_familia: agregado parámetro coeficiente_familia con valor por defecto 1
  - Actualizada consulta SQL para incluir el campo en INSERT y UPDATE

## Estructura del Campo

```sql
coeficiente_familia BOOLEAN DEFAULT TRUE COMMENT 'Control de coeficientes de descuento'
```

- **Tipo**: BOOLEAN
- **Por defecto**: TRUE (permite coeficientes)
- **Ubicación**: Después del campo activo_familia

## Comportamiento

### Nuevas Familias
- El checkbox aparece marcado por defecto (permite coeficientes)
- Se guarda como valor 1 en la base de datos

### Edición de Familias
- El checkbox muestra el estado actual desde la base de datos
- Permite modificar el valor
- Se actualiza el texto dinámicamente ("Permite coeficientes" / "No permite coeficientes")

### Listado de Familias
- Nueva columna "Coeficientes" con iconos visuales
- Icono de porcentaje (%) verde: permite coeficientes
- Icono de círculo tachado rojo: no permite coeficientes
- Filtro select en footer para buscar por estado de coeficientes
- Información detallada en vista expandible

## Validaciones y Características

1. **Formulario**:
   - Campo no requerido (opcional)
   - Tooltip explicativo sobre la funcionalidad
   - Detección de cambios para alerta de salida sin guardar

2. **Base de Datos**:
   - Campo con valor por defecto TRUE
   - Comentario descriptivo en la estructura

3. **Interfaz de Usuario**:
   - Iconos intuitivos en listado
   - Texto dinámico en formulario
   - Filtros funcionales en tabla
   - Documentación en modal de ayuda

## Instrucciones de Instalación

1. Ejecutar el script `BD/alter_coeficiente_familia.sql` para agregar el campo a la tabla
2. Ejecutar el script `BD/alter_vista_familia_unidad_media.sql` para actualizar la vista
3. Los archivos modificados ya incluyen toda la lógica necesaria

## Impacto en el Sistema

- **Compatibilidad**: Los registros existentes tendrán valor TRUE por defecto
- **Performance**: Mínimo impacto, campo con índice implícito
- **Funcionalidad**: Base para futuras implementaciones de coeficientes de descuento
- **Mantenimiento**: Integración completa con CRUD existente

## Próximos Pasos Sugeridos

1. Implementar la funcionalidad en el módulo de artículos
2. Agregar lógica de coeficientes en presupuestos
3. Considerar auditoría de cambios en este campo específico
4. Documentar reglas de negocio para coeficientes de descuento