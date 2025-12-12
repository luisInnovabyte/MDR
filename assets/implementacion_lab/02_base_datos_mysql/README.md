# 02 - Base de Datos MySQL

> Documentación completa de estándares para diseño de bases de datos

---

## Documentos de esta sección

| # | Documento | Descripción |
|---|-----------|-------------|
| 01 | [Convenciones de Tablas](01_convenciones_tablas.md) | Nomenclatura de tablas y campos |
| 02 | [Campos Obligatorios](02_campos_obligatorios.md) | Campos que siempre deben existir |
| 03 | [Tipos de Campos Estándar](03_tipos_campos_estandar.md) | Guía de tipos de datos según uso |
| 04 | [Índices y Foreign Keys](04_indices_foreign_keys.md) | Estándar de índices y relaciones |
| 05 | [Charset y Collation](05_charset_collation.md) | Configuración de codificación |
| 06 | [Plantilla SQL](06_plantilla_sql.md) | Plantillas listas para usar |

---

## Resumen rápido de convenciones

### Tablas
- Nombre en **singular**: `cliente`, `pedido`, `articulo`
- Formato **snake_case**: `linea_pedido`, `foto_articulo`

### Campos
- Siempre terminan en `_<<nombre_tabla>>`: `nombre_cliente`, `total_pedido`
- FK mantienen el nombre original: `id_cliente` en tabla `pedido`

### Campos obligatorios
```sql
id_<<tabla>> INT NOT NULL AUTO_INCREMENT PRIMARY KEY
activo_<<tabla>> TINYINT(1) NOT NULL DEFAULT 1
created_at_<<tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
updated_at_<<tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

### Configuración de tabla
```sql
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
```

---

## Cómo usar esta documentación

1. **Nueva tabla**: Usa la plantilla de `06_plantilla_sql.md`
2. **Elegir tipo de dato**: Consulta `03_tipos_campos_estandar.md`
3. **Definir relaciones**: Sigue `04_indices_foreign_keys.md`
4. **Verificar nomenclatura**: Revisa `01_convenciones_tablas.md`

---

*Sección: 02 - Base de Datos MySQL | Última actualización: Diciembre 2024*
