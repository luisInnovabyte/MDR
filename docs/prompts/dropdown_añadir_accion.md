# Prompt: Añadir una acción nueva a un dropdown existente en DataTables

## ¿Cuándo usar este prompt?
- Cuando el módulo ya tiene el dropdown implementado y quieres incorporar
  una nueva acción (nuevo item en el menú + su lógica completa en JS, controller y model).
- Para mantener la coherencia con el patrón del proyecto sin reescribir lo que ya funciona.

## Spec de referencia
`.claude/specs/dropdown_datatable_acciones.md`

## Archivos que modifica
- `view/[Modulo]/mnt[Modulo].js` — nuevo item en el render del dropdown + event handler
- `controller/[modulo].php` — nuevo `case` en el switch
- `models/[Entidad].php` — nuevo método que ejecuta la operación en BD

---

## Prompt listo para copiar y pegar

```
Lee `.claude/specs/dropdown_datatable_acciones.md` y añade la acción `[nombre_visible]`
al dropdown en `view/[Modulo]/mnt[Modulo].js`, con su `case` en
`controller/[modulo].php` y método en `models/[Entidad].php`.

Descripción de la nueva acción:
- Nombre visible en el menú: [nombre]
- Icono (Bootstrap Icons o Font Awesome): [bi-xxx / fa-xxx]
- Función JS: [nombreFuncion(id)]
- Operación en controller: op=[nombre_operacion]
- Lógica en BD: [descripción breve de lo que hace el método en el modelo]
- ¿Condición de deshabilitado?: [sí: campo == valor / no]
- ¿Separador antes de este item?: [sí / no]
```

### Ejemplo relleno
```
Lee `.claude/specs/dropdown_datatable_acciones.md` y añade la acción `Duplicar`
al dropdown en `view/MntArticulos/mntArticulos.js`, con su `case` en
`controller/articulo.php` y método `duplicar_articuloxid` en `models/Articulo.php`.

Descripción de la nueva acción:
- Nombre visible en el menú: Duplicar
- Icono: bi-copy
- Función JS: duplicar(id)
- Operación en controller: op=duplicar
- Lógica en BD: copiar todos los campos del artículo excepto el código (que se genera nuevo) y devolver el id del nuevo registro
- ¿Condición de deshabilitado?: sí: activo_articulo == 0
- ¿Separador antes de este item?: sí
```

---

## Instrucciones de uso
1. Sustituye `[Modulo]` y `[Entidad]` por los nombres reales del módulo y clase modelo.
2. El nombre de la operación (`op=`) debe ser en minúsculas sin espacios.
3. Si la acción necesita confirmación SweetAlert2, indícalo en la descripción.
4. Si es una acción destructiva, el agente añadirá automáticamente confirmación de borrado.

## Notas
- Solo se tocan los 3 archivos indicados; el HTML de `index.php` no cambia
  porque el render del dropdown es dinámico (vía `columnDefs render()`).
- Si el método en el modelo ya existe con otro nombre, indícalo para evitar duplicados.
