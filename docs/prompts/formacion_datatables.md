# Prompt: Generar módulo Mnt* completo con DataTables

> **Cuándo usar:** Cuando necesitas crear desde cero un módulo de mantenimiento CRUD
> completo (vista + JS + controller + model) siguiendo el patrón estándar del proyecto.
>
> **Spec de referencia:** `.claude/specs/formacion_datatables.md`
> **Módulo de referencia real:** `view/MntMarca/`
>
> **Archivos que genera:**
> - `view/Mnt[Entidad]/index.php` — Vista HTML con tabla y modal
> - `view/Mnt[Entidad]/mnt[Entidad].js` — Lógica DataTables y eventos
> - `controller/[entidad].php` — Endpoint AJAX (switch de operaciones)
> - `models/[Entidad].php` — Acceso a datos con PDO

---

## Prompt principal

```
Lee `.claude/specs/formacion_datatables.md` y `.claude/specs/controller-models/models.md`
y genera el módulo Mnt* completo para la entidad `[entidad]`.

Datos de la entidad:
- Nombre de clase PHP (PascalCase):        [Entidad]
- Nombre de tabla en BD:                   [entidad]
- PK:                                      id_[entidad]
- Campo único para verificar:              [campo_unico_entidad]
- ¿Existe vista SQL?:                      [SÍ: vista_[entidad]_completa / NO]

Campos a mostrar en la tabla (columnas visibles):
- [campo1]: tipo=[varchar/int/decimal/date], label="[Etiqueta visible]", filtro=[sí/no]
- [campo2]: tipo=[varchar/int/decimal/date], label="[Etiqueta visible]", filtro=[sí/no]
...

Campos del formulario modal (todos los editables):
- [campo1]: tipo=[text/number/select/date], requerido=[sí/no], label="[Etiqueta]"
- [campo2]: tipo=[text/number/select/date], requerido=[sí/no], label="[Etiqueta]"
...

Campos para el child-row expandible (info detallada):
- [campo1], [campo2], created_at_[entidad], updated_at_[entidad]

¿Child-row avanzado?:  NO (patrón base: tabla simple dentro de card)
                       SÍ → leer también `.claude/specs/childrow_campos.md`
                            (3 columnas + fila adicional, helper val(), iconos por campo)

FK obligatorias (NOT NULL):
- id_[tabla_fk] → tabla [tabla_fk], mostrar campo [nombre_campo_fk] en la tabla principal

FK opcionales (NULL):
- (ninguna / lista)

[Pega aquí el CREATE TABLE completo de la entidad]
```

---

## Ejemplo relleno (entidad `marca`)

```
Lee `.claude/specs/formacion_datatables.md` y `.claude/specs/controller-models/models.md`
y genera el módulo Mnt* completo para la entidad `marca`.

Datos de la entidad:
- Nombre de clase PHP (PascalCase):        Marca
- Nombre de tabla en BD:                   marca
- PK:                                      id_marca
- Campo único para verificar:              nombre_marca
- ¿Existe vista SQL?:                      NO

Campos a mostrar en la tabla (columnas visibles):
- nombre_marca: tipo=varchar, label="Nombre", filtro=sí
- descripcion_marca: tipo=varchar, label="Descripción", filtro=sí

Campos del formulario modal:
- nombre_marca: tipo=text, requerido=sí, label="Nombre"
- descripcion_marca: tipo=text, requerido=no, label="Descripción"

Campos para el child-row expandible:
- descripcion_marca, created_at_marca, updated_at_marca

¿Child-row avanzado?: NO

FK obligatorias: (ninguna)
FK opcionales: (ninguna)

CREATE TABLE marca (
    id_marca INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_marca VARCHAR(100) NOT NULL,
    descripcion_marca TEXT,
    activo_marca BOOLEAN DEFAULT TRUE,
    created_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_marca TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

---

## Instrucciones de uso

1. Copia el **prompt principal** arriba.
2. Sustituye todos los placeholders `[...]` con los datos reales de tu entidad.
3. Pega el `CREATE TABLE` completo al final.
4. Envía el prompt: el agente generará los 4 archivos completos y listos para usar.

---

## Notas importantes

- Los event handlers en el JS **siempre** se generan con `$(document).on(...)` (delegados).
  Nunca binding directo sobre elementos del DataTable.
- El controller usa **soft delete** (`activo_[entidad] = 0`). Nunca `DELETE` físico.
- El model incluye `RegistroActividad` y prepared statements en todos los métodos.
- Si la entidad tiene FK, el agente añadirá el JOIN correspondiente en el model y en la
  columna de la tabla (label del campo relacionado, no el ID numérico).
- El módulo generado sigue la convención de rutas `../../controller/` y `../../public/lib/`.
