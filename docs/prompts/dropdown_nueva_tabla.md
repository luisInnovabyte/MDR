# Prompt: Implementar dropdown de acciones en una tabla DataTables nueva

## ¿Cuándo usar este prompt?
- Cuando una tabla DataTable tiene 4 o más botones de acción en la columna "Opciones"
  y quieres consolidarlos en un único dropdown Bootstrap 5.
- Para aplicar el patrón estándar del proyecto en un módulo nuevo o existente.

## Spec de referencia
`.claude/specs/dropdown_datatable_acciones.md`

## Archivos que modifica
- `view/[Modulo]/index.php` — columna thead/tfoot "Opciones"
- `view/[Modulo]/mnt[Modulo].js` — columns[], columnDefs, z-index fix, event handlers

---

## Prompt listo para copiar y pegar

```
Lee `.claude/specs/dropdown_datatable_acciones.md` e implementa el dropdown de acciones
en `view/[Modulo]/index.php` y `view/[Modulo]/mnt[Modulo].js`.

Acciones a incluir:
- [acción 1: nombre + función JS que llama]
- [acción 2: nombre + función JS que llama]
- [acción 3: nombre + función JS que llama]
- ...

Condiciones especiales (items que deben deshabilitarse):
- [campo_estado == 'VALOR'] → deshabilitar acción [nombre]
- [sin condiciones]

Datos del row disponibles (campos que devuelve el controller op=listar):
- [campo1], [campo2], [campo3], ...
```

### Ejemplo relleno
```
Lee `.claude/specs/dropdown_datatable_acciones.md` e implementa el dropdown de acciones
en `view/MntArticulos/index.php` y `view/MntArticulos/mntArticulos.js`.

Acciones a incluir:
- Ver detalle → verDetalle(id)
- Editar → mostrar(id)
- Duplicar → duplicar(id)
- Desactivar → desactivar(id)
- Ver documentos → verDocumentos(id)

Condiciones especiales:
- activo_articulo == 0 → deshabilitar "Editar" y "Duplicar"

Datos del row disponibles:
- id_articulo, nombre_articulo, activo_articulo, tipo_articulo
```

---

## Instrucciones de uso
1. Sustituye `[Modulo]` por el nombre de la carpeta del módulo (ej: `MntPresupuestos`).
2. Lista todas las acciones con el nombre visible en el menú y la función JS que invoca.
3. Define las condiciones de deshabilitado (qué campo del row determina que un item esté en gris).
4. Lista los campos disponibles del row para que el agente sepa qué datos puede usar en las condiciones.

## Notas
- El spec incluye la solución para el bug de z-index con `fixedColumns` — se aplica automáticamente.
- Los event handlers siempre van con delegación (`$(document).on('click', '[data-accion]', ...)`)
  para que funcionen tras recargar DataTables.
- Si la tabla usa `scrollX: true`, el fix de z-index es obligatorio.
