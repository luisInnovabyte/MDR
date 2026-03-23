# Prompt: Añadir modal de ayuda contextual («?») a una vista

## ¿Cuándo usar este prompt?
- Cuando una vista del proyecto necesita un botón «?» junto al título que,
  al pulsarlo, abra un modal explicativo para el usuario.
- Para añadir el componente de ayuda contextual a un módulo nuevo o existente
  respetando el patrón estándar del proyecto (basado en `Informe_rotacion`).

## Spec de referencia
`.claude/specs/ayuda.md`

## Archivos que modifica / crea
- `view/[Modulo]/index.php` — añade el botón `?` en el `<h4>` del título e incluye el PHP del modal
- `view/[Modulo]/ayuda[Vista].php` — **archivo nuevo** con el HTML completo del modal

---

## Prompt listo para copiar y pegar

```
Lee `.claude/specs/ayuda.md` y añade el modal de ayuda contextual a la vista
`view/[Modulo]/index.php`.

Datos de la vista:
- Nombre de la vista (para IDs y nombre de fichero): [NombreVista]   <!-- ej: Rotacion, Presupuestos, Articulos -->
- Título visible del modal: [Título descriptivo]
- Icono Font Awesome del módulo: fas fa-[icono]                       <!-- el que ya usa el h4 -->

Secciones de contenido del modal (incluir las que apliquen):
1. ¿Qué es esta pantalla?:  [breve descripción de para qué sirve]
2. Estados / Semáforo:      [sí/no — si sí, lista los estados y su significado]
3. KPIs / indicadores:      [sí/no — si sí, lista los KPIs disponibles]
4. Gráficos:                [sí/no — si sí, lista los gráficos y qué muestran]
5. Columnas de tabla:       [sí/no — si sí, lista las columnas y qué significan]
6. Filtros:                 [sí/no — si sí, lista los filtros disponibles]
7. FAQ:                     [sí/no — si sí, lista las preguntas y respuestas]
```

### Ejemplo relleno
```
Lee `.claude/specs/ayuda.md` y añade el modal de ayuda contextual a la vista
`view/Informe_presupuestos/index.php`.

Datos de la vista:
- Nombre de la vista (para IDs y nombre de fichero): Presupuestos
- Título visible del modal: Informe de Presupuestos
- Icono Font Awesome del módulo: fas fa-file-invoice

Secciones de contenido del modal:
1. ¿Qué es esta pantalla?: Muestra el resumen de todos los presupuestos emitidos,
   con sus importes, estados y evolución temporal.
2. Estados / Semáforo: sí
   - BORRADOR → en preparación, no enviado
   - ENVIADO → cliente notificado, pendiente de respuesta
   - ACEPTADO → cliente confirmó, puede pasar a factura
   - RECHAZADO → cliente declinó el presupuesto
   - CANCELADO → anulado internamente
3. KPIs / indicadores: sí — Total presupuestado, Total aceptado, % aceptación, Importe medio
4. Gráficos: no
5. Columnas de tabla: sí — Número, Fecha, Cliente, Evento, Importe, Estado, Comercial
6. Filtros: sí — Rango de fechas, Estado, Comercial
7. FAQ: no
```

---

## Instrucciones de uso
1. Sustituye `[Modulo]` por el nombre de la carpeta de la vista (ej: `MntClientes`).
2. `[NombreVista]` se usa para generar los IDs del modal (`modalAyuda[NombreVista]`)
   y el nombre del fichero PHP (`ayuda[NombreVista].php`). Usa PascalCase sin espacios.
3. Indica solo las secciones que aportan valor real; el agente omite las marcadas como «no».
4. Para la sección de estados, proporciona los valores del `ENUM` o códigos reales de la BD.

## Notas
- No se necesita JavaScript adicional: el modal funciona solo con `data-bs-toggle/target` de Bootstrap 5.
- Bootstrap 5 y Bootstrap Icons ya están cargados en `mainHead.php` y `mainJs.php` en todas las vistas.
- Vista de referencia completa: `view/Informe_rotacion/index.php` + `view/Informe_rotacion/ayudaRotacion.php`.
