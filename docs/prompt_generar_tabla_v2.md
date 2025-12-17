# Prompt: Generar Tabla MySQL

> Prompt optimizado para solicitar la creación de tablas siguiendo los estándares del proyecto Implementation Lab

---

## Instrucciones de Uso

**Disparador**: Cuando escribas `NUEVA TABLA` seguido de la especificación, Claude generará automáticamente el SQL completo aplicando todos los estándares documentados.

**Requisito**: Este prompt está diseñado para usarse dentro de un proyecto Claude que tenga cargada la documentación de estándares de base de datos.

---

## Formato de Solicitud

```
NUEVA TABLA
===========
Nombre: <<nombre_en_singular_snake_case>>
Descripción: <<propósito breve de la tabla>>

CAMPOS:
- <<nombre_campo>>: <<descripción>> [obligatorio|opcional]
- <<nombre_campo>>: <<descripción>> [obligatorio|opcional]
- ...

RELACIONES:
- FK a <<tabla>>: [obligatoria|opcional] - <<descripción>> [ON DELETE: RESTRICT|CASCADE|SET NULL]
- ...
(o "Ninguna" si no tiene)

ÚNICOS:
- <<campo>>
- <<campo1>> + <<campo2>> (combinación)
(o "Ninguno" si no aplica)

ÍNDICES ADICIONALES:
- <<campo>> (búsqueda frecuente)
(o "Ninguno" si no aplica)

ENUM (si aplica):
- <<campo>>: valor1, valor2, valor3

NOTAS (opcional):
- <<cualquier consideración especial>>
```

---

## Tipos de Tablas

### Tipo 1: Tabla Simple (Catálogo)

Tablas independientes sin relaciones o con relaciones mínimas.

```
NUEVA TABLA
===========
Nombre: categoria
Descripción: Categorías para clasificar artículos del catálogo

CAMPOS:
- nombre: Nombre de la categoría [obligatorio]
- descripcion: Descripción detallada [opcional]
- color: Color hexadecimal para UI [opcional]
- orden: Posición en listados [obligatorio, default 0]

RELACIONES:
- Ninguna

ÚNICOS:
- nombre

ÍNDICES ADICIONALES:
- orden (ordenación frecuente)
```

---

### Tipo 2: Tabla con FK Obligatoria

Tablas que dependen de otra tabla padre.

```
NUEVA TABLA
===========
Nombre: articulo
Descripción: Catálogo de artículos/productos disponibles

CAMPOS:
- codigo: Código único interno [obligatorio]
- nombre: Nombre del artículo [obligatorio]
- descripcion: Descripción detallada [opcional]
- precio: Precio unitario [obligatorio]
- stock: Cantidad disponible [obligatorio, default 0]
- foto: Ruta de imagen [opcional]
- destacado: Marcar como destacado [opcional, default 0]

RELACIONES:
- FK a categoria: obligatoria - Categoría del artículo [ON DELETE: RESTRICT]
- FK a proveedor: opcional - Proveedor principal [ON DELETE: SET NULL]

ÚNICOS:
- codigo

ÍNDICES ADICIONALES:
- nombre (búsqueda frecuente)
- precio (filtros y ordenación)
```

---

### Tipo 3: Tabla Cabecera-Detalle (1:N con CASCADE)

Para estructuras de documento con líneas de detalle.

**Tabla Cabecera:**
```
NUEVA TABLA
===========
Nombre: pedido
Descripción: Cabecera de pedidos de clientes

CAMPOS:
- numero: Número único de pedido [obligatorio]
- fecha: Fecha de emisión [obligatorio]
- fecha_entrega: Fecha prevista de entrega [opcional]
- observaciones: Notas del pedido [opcional]
- total: Importe total calculado [obligatorio, default 0.00]
- estado: Estado del pedido [obligatorio]

RELACIONES:
- FK a cliente: obligatoria - Cliente que realiza el pedido [ON DELETE: RESTRICT]
- FK a forma_pago: opcional - Forma de pago seleccionada [ON DELETE: SET NULL]

ÚNICOS:
- numero

ENUM:
- estado: borrador, confirmado, en_proceso, enviado, entregado, cancelado

NOTAS:
- El campo total se actualiza mediante trigger desde las líneas
```

**Tabla Detalle (líneas):**
```
NUEVA TABLA
===========
Nombre: linea_pedido
Descripción: Líneas de detalle de los pedidos

CAMPOS:
- cantidad: Cantidad de unidades [obligatorio, default 1]
- precio_unitario: Precio por unidad [obligatorio]
- descuento: Porcentaje de descuento [opcional, default 0.00]
- total_linea: Importe total de la línea [obligatorio]
- observaciones: Notas de la línea [opcional]

RELACIONES:
- FK a pedido: obligatoria - Pedido al que pertenece [ON DELETE: CASCADE]
- FK a articulo: obligatoria - Artículo de la línea [ON DELETE: RESTRICT]

ÚNICOS:
- Ninguno

NOTAS:
- ON DELETE CASCADE: Al eliminar el pedido, se eliminan sus líneas
- total_linea se calcula: (cantidad * precio_unitario) * (1 - descuento/100)
```

---

### Tipo 4: Tabla Pivote (N:M)

Para relaciones muchos a muchos.

```
NUEVA TABLA
===========
Nombre: articulo_etiqueta
Descripción: Relación muchos a muchos entre artículos y etiquetas

CAMPOS:
- orden: Posición de la etiqueta en el artículo [opcional, default 0]

RELACIONES:
- FK a articulo: obligatoria - Artículo relacionado [ON DELETE: CASCADE]
- FK a etiqueta: obligatoria - Etiqueta asignada [ON DELETE: CASCADE]

ÚNICOS:
- articulo + etiqueta (combinación única)

NOTAS:
- Tabla pivote pura con campos adicionales mínimos
- CASCADE en ambas FK para limpieza automática
```

---

### Tipo 5: Tabla con Auto-referencia (Jerárquica)

Para estructuras de árbol o jerarquías.

```
NUEVA TABLA
===========
Nombre: categoria_producto
Descripción: Categorías jerárquicas de productos (árbol multinivel)

CAMPOS:
- codigo: Código único de categoría [obligatorio]
- nombre: Nombre de la categoría [obligatorio]
- descripcion: Descripción [opcional]
- nivel: Nivel en la jerarquía [obligatorio, default 1]
- orden: Posición entre hermanos [obligatorio, default 0]

RELACIONES:
- FK a categoria_producto (id_padre): opcional - Categoría padre [ON DELETE: SET NULL]

ÚNICOS:
- codigo

ÍNDICES ADICIONALES:
- nivel (filtrar por profundidad)
- orden (ordenación)

NOTAS:
- id_padre = NULL indica categoría raíz
- nivel se calcula automáticamente según profundidad
```

---

## Lo que Claude Generará Automáticamente

Al recibir `NUEVA TABLA`, aplicaré:

### 1. Nomenclatura de Campos
- Sufijo `_<<nombre_tabla>>` a todos los campos propios
- FK mantienen nombre original: `id_<<tabla_referenciada>>`

### 2. Campos Obligatorios del Sistema
```sql
id_<<tabla>> INT NOT NULL AUTO_INCREMENT PRIMARY KEY
activo_<<tabla>> TINYINT(1) NOT NULL DEFAULT 1
created_at_<<tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
updated_at_<<tabla>> TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
```

### 3. Tipos de Datos Según Estándar
| Uso | Tipo |
|-----|------|
| Textos cortos (nombre, código) | `VARCHAR(50-150)` |
| Email | `VARCHAR(150)` |
| Teléfono | `VARCHAR(20)` |
| Dirección | `VARCHAR(255)` |
| Descripciones | `TEXT` |
| Precios/Importes | `DECIMAL(10,2)` o `DECIMAL(15,2)` |
| Cantidades | `INT` |
| Porcentajes | `DECIMAL(5,2)` |
| Booleanos | `TINYINT(1)` |
| Fechas | `DATE` o `DATETIME` |

### 4. Índices
- `PRIMARY KEY` en `id_<<tabla>>`
- `KEY idx_<<campo>>` en todas las FK
- `UNIQUE KEY uk_<<campo>>` en campos únicos
- `KEY idx_<<campo>>` en campos de búsqueda/ordenación frecuente

### 5. Foreign Keys
```sql
CONSTRAINT fk_<<tabla_origen>>_<<tabla_destino>> 
    FOREIGN KEY (id_<<tabla_destino>>) 
    REFERENCES <<tabla_destino>>(id_<<tabla_destino>>) 
    ON DELETE <<acción>> 
    ON UPDATE CASCADE
```

**Acciones ON DELETE:**
- `RESTRICT` → FK obligatoria, no permite borrar padre con hijos
- `CASCADE` → Borrar padre elimina hijos (líneas de detalle)
- `SET NULL` → FK opcional, pone NULL si se borra padre

### 6. Configuración de Tabla
```sql
ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
```

---

## Respuesta Esperada

Al procesar tu solicitud, recibirás:

### 1. SQL Completo
```sql
-- ============================================
-- Tabla: <<nombre>>
-- Descripción: <<descripción>>
-- Fecha: <<fecha_actual>>
-- ============================================

CREATE TABLE <<nombre>> (
    -- Campos...
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

### 2. Checklist de Verificación
- [ ] Nombre en singular y snake_case
- [ ] Todos los campos con sufijo `_<<tabla>>`
- [ ] Campos obligatorios incluidos (id, activo, created_at, updated_at)
- [ ] Índices en FK y campos de búsqueda
- [ ] UNIQUE en campos que no deben repetirse
- [ ] FK con ON DELETE/UPDATE apropiados
- [ ] ENGINE=InnoDB y CHARSET=utf8mb4

### 3. Notas Adicionales (si aplica)
- Triggers recomendados
- Vistas SQL sugeridas
- Consideraciones de rendimiento

---

## Ejemplos Rápidos de Uso

### Crear tabla de clientes:
```
NUEVA TABLA
===========
Nombre: cliente
Descripción: Clientes del sistema

CAMPOS:
- codigo: Código único de cliente [obligatorio]
- nombre: Nombre o razón social [obligatorio]
- nif: NIF/CIF [opcional]
- email: Email principal [opcional]
- telefono: Teléfono contacto [opcional]
- direccion: Dirección postal [opcional]
- poblacion: Ciudad [opcional]
- cp: Código postal [opcional]
- provincia: Provincia [opcional]

RELACIONES:
- FK a comercial: opcional - Comercial asignado [ON DELETE: SET NULL]

ÚNICOS:
- codigo
- nif
- email
```

### Crear tabla de configuración:
```
NUEVA TABLA
===========
Nombre: configuracion
Descripción: Parámetros de configuración del sistema

CAMPOS:
- clave: Identificador del parámetro [obligatorio]
- valor: Valor del parámetro [obligatorio]
- tipo: Tipo de dato [obligatorio]
- descripcion: Descripción del parámetro [opcional]
- editable: Si el usuario puede modificarlo [obligatorio, default 1]

RELACIONES:
- Ninguna

ÚNICOS:
- clave

ENUM:
- tipo: texto, numero, booleano, fecha, json
```

---

## Referencia Rápida de Documentación

| Documento | Contenido |
|-----------|-----------|
| `01_convenciones_tablas.md` | Nomenclatura de tablas y campos |
| `02_campos_obligatorios.md` | Campos id, activo, created_at, updated_at |
| `03_tipos_campos_estandar.md` | Guía de tipos de datos |
| `04_indices_foreign_keys.md` | Índices y relaciones |
| `05_charset_collation.md` | Configuración UTF-8 español |
| `06_plantilla_sql.md` | Plantillas SQL completas |

---

*Documento: 06-01 | Versión: 2.0 | Última actualización: Diciembre 2024*
