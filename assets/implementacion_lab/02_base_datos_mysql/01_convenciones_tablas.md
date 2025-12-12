# Convenciones de Tablas MySQL

> Estándar de nomenclatura y estructura para todas las tablas del sistema

---

## Nomenclatura de Tablas

### Reglas

| Regla | Ejemplo correcto | Ejemplo incorrecto |
|-------|------------------|-------------------|
| Siempre en **singular** | `cliente` | `clientes` |
| Formato **snake_case** | `pie_presupuesto` | `piePresupuesto`, `PiePresupuesto` |
| Nombres descriptivos | `foto_articulo` | `fa`, `fotos` |
| Sin prefijos de proyecto | `usuario` | `app_usuario`, `tbl_usuario` |

### Ejemplos válidos

```
cliente
comercial
presupuesto
pie_presupuesto
foto_articulo
documento_elemento
configuracion_sistema
permiso_usuario
```

---

## Nomenclatura de Campos

### Regla principal

**Todos los campos deben terminar con `_<<nombre_tabla>>`**

### Formato

```
<<nombre_descriptivo>>_<<nombre_tabla>>
```

### Ejemplos para tabla `cliente`

| Campo | Descripción |
|-------|-------------|
| `id_cliente` | Identificador único |
| `nombre_cliente` | Nombre del cliente |
| `apellido_cliente` | Apellido del cliente |
| `email_cliente` | Correo electrónico |
| `telefono_cliente` | Teléfono de contacto |
| `foto_cliente` | Ruta o nombre de imagen |
| `activo_cliente` | Estado activo/inactivo |
| `created_at_cliente` | Fecha de creación |
| `updated_at_cliente` | Fecha de última modificación |

### Ejemplos para tabla `pie_presupuesto`

| Campo | Descripción |
|-------|-------------|
| `id_pie_presupuesto` | Identificador único |
| `descripcion_pie_presupuesto` | Texto del pie |
| `orden_pie_presupuesto` | Posición en listado |
| `id_presupuesto` | FK a tabla presupuesto |
| `activo_pie_presupuesto` | Estado activo/inactivo |
| `created_at_pie_presupuesto` | Fecha de creación |
| `updated_at_pie_presupuesto` | Fecha de última modificación |

---

## Ventajas de esta convención

1. **Consultas JOIN sin ambigüedad**: No necesitas alias, cada campo es único
2. **Autocompletado eficiente**: Escribes el sufijo y filtras por tabla
3. **Trazabilidad**: Sabes de qué tabla viene cada campo en cualquier consulta
4. **Consistencia**: Regla única aplicable a todo el sistema

---

*Documento: 02-01 | Última actualización: Diciembre 2024*
