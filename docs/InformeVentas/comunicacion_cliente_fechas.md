# Informe de Ventas — Separación de fechas presupuesto / factura

**Para**: [Cliente]  
**Fecha**: 23 de marzo de 2026  
**Asunto**: Análisis del cambio solicitado y opciones de implementación

---

## Contexto: cómo funciona el informe actualmente

El informe de ventas muestra, mes a mes, dos magnitudes comparadas en el mismo gráfico:

- **Aprobado**: valor total de los presupuestos aprobados ese mes
- **Facturado/Cobrado**: pagos recibidos asociados a esos mismos presupuestos

Ambas series usan la **fecha del presupuesto** como referencia temporal. Esto garantiza una regla fundamental: lo cobrado de un mes nunca puede superar lo aprobado en ese mismo mes, porque se está mirando el mismo conjunto de presupuestos desde dos ángulos.

---

## El problema con las fechas desacopladas

El cambio solicitado es que los cobros y facturas aparezcan en el mes en que **realmente ocurrieron**, no en el mes del presupuesto. El programa ya almacena este dato (`fecha_pago`, `fecha_factura`), por lo que técnicamente el dato existe.

Sin embargo, desacoplar las fechas rompe la consistencia del gráfico comparativo:

**Ejemplo:**
```
Presupuesto aprobado en enero → 10.000 €
  - Anticipo cobrado en diciembre del año anterior → 3.000 €
  - Pago final cobrado en septiembre → 7.000 €
```

Con fechas desacopladas, **enero mostraría 10.000 € aprobado y 0 € cobrado**, mientras que **septiembre mostraría 7.000 € cobrado y 0 € aprobado**. El gráfico parecería indicar que en septiembre se cobró dinero que nunca fue aprobado ese mes, lo que da una lectura errónea de la actividad comercial.

En resumen: **cuando las dos series no comparten la misma base temporal, su comparación visual pierde significado y puede inducir a conclusiones incorrectas.**

---

## Opciones propuestas

### Opción A — Doble sección (recomendada)

Mantener el gráfico actual intacto (aprobado vs. cobrado por fecha de presupuesto) y añadir una **sección separada** debajo con un gráfico propio de "Cobros reales por mes", que agrupa únicamente por `fecha_pago` con una sola barra. Cada sección tiene su propio título y contexto, evitando cualquier mezcla.

- ✅ El gráfico actual no cambia
- ✅ El cliente tiene la visión de cobros reales por mes
- ✅ No hay riesgo de lecturas erróneas
- ⚠️ Añade volumen visual a la página

### Opción B — Sistema de pestañas en el gráfico

El área del gráfico principal tiene dos pestañas:
- **Pestaña 1**: vista actual (fecha presupuesto, aprobado vs. cobrado)
- **Pestaña 2**: vista de cobros reales (fecha pago, una sola barra)

El usuario elige qué quiere ver pero nunca mezcla bases temporales en la misma visualización.

- ✅ UX limpia, mismo espacio en pantalla
- ✅ Separa claramente los dos conceptos
- ⚠️ Requiere que el usuario sepa qué pestaña está mirando en cada momento

### Opción C — Mantener el diseño actual

Explicar al cliente que el diseño actual es técnicamente la representación más precisa de la actividad comercial mes a mes. Los cobros de anticipos previos a la aprobación son casos excepcionales que no justifican un rediseño del informe principal.

- ✅ Sin coste de desarrollo
- ❌ No satisface la necesidad del cliente

---

## Restricción técnica común a todas las opciones

En cualquier vista que agrupe por **fecha de cobro o fecha de factura**, el desglose por familia de artículo **no estará disponible**. Los pagos en el sistema están asociados al presupuesto completo, no a líneas individuales, por lo que no es posible calcular qué proporción del cobro corresponde a cada familia de producto.

---

## Siguiente paso

Pendiente confirmación de la opción elegida para proceder con la implementación.
