# Prompt: Añadir un campo nuevo al child-row de DataTables

## ¿Cuándo usar este prompt?
- Cuando el child-row ya existe y funciona, pero necesitas incorporar un campo
  adicional de BD que aún no se muestra en la fila expandible.
- Para añadir un campo en la columna correcta según la naturaleza del dato
  (info general, económico, localización o técnico).

## Spec de referencia
`.claude/specs/childrow_campos.md`

## Archivos que modifica
- `view/lineasPresupuesto/lineasPresupuesto.js` (o el archivo JS del módulo correspondiente)

---

## Prompt listo para copiar y pegar

```
Lee `.claude/specs/childrow_campos.md` y añade el campo `[nombre_campo_bd]`
en la [columna / fila] correspondiente siguiendo los patrones del spec.
```

### Ejemplo relleno
```
Lee `.claude/specs/childrow_campos.md` y añade el campo `observaciones_linea`
en la Fila Adicional (sección técnica) siguiendo los patrones del spec.
```

---

## Instrucciones de uso
1. Sustituye `[nombre_campo_bd]` por el nombre exacto del campo tal como viene en el JSON del controller.
2. Indica en qué columna/fila debe ir:
   - **Col1** → Información general (nombre, descripción, estado, tipo)
   - **Col2** → Detalle económico (precios, cantidades, IVA, totales)
   - **Col3** → Localización y fechas (lugar, fechas montaje/desmontaje)
   - **Fila Adicional** → Datos técnicos, timestamps, observaciones internas
3. Si el campo necesita badge, icono o condición especial, descríbelo en el prompt.

## Notas
- Asegúrate de que el controller (op=listar) devuelva ya el campo en el JSON;
  si no, habrá que añadirlo también en el controller y en el modelo.
