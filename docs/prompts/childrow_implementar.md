# Prompt: Implementar (o actualizar) child-row completo en DataTables

## ¿Cuándo usar este prompt?
- Cuando creas una tabla DataTable nueva y necesitas añadirle la fila expandible de detalle (child-row).
- Cuando quieres reescribir desde cero la función `formatLineaDetalle(d)` de una tabla existente.
- Cuando el child-row actual no respeta la estructura de 3 columnas + fila técnica definida en el spec.

## Spec de referencia
`.claude/specs/childrow_campos.md`

## Archivos que modifica
- `view/lineasPresupuesto/lineasPresupuesto.js` (o el archivo JS del módulo correspondiente)

---

## Prompt listo para copiar y pegar

```
Lee `.claude/specs/childrow_campos.md` e implementa (o actualiza) la función
`formatLineaDetalle(d)` en `view/lineasPresupuesto/lineasPresupuesto.js`
siguiendo exactamente la estructura de columnas, badges, iconos,
condiciones y helpers descritos en el spec.
```

---

## Notas
- El spec cubre: helper `val()`, helper `formatearMoneda()`, Col1 (Info General),
  Col2 (Detalle Económico), Col3 (Localización/Fechas), Fila Adicional (Técnica/Timestamps)
  y la interacción del botón expandir/colapsar.
- Si el módulo es diferente a `lineasPresupuesto`, ajusta la ruta del archivo JS en el prompt.
