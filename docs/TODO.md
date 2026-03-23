# TODO — Backlog de desarrollo MDR

> Última actualización: 18/03/2026  
> Leyenda: 🔴 Alta prioridad · 🟡 Media · 🟢 Baja · 📋 Pendiente de detalle · 🔗 Ver documentación

---

## NUEVAS FUNCIONALIDADES (reunión 18-03-2026)

| Estado | Tarea | Prio | Notas |
|--------|-------|------|-------|
| ⬜ | Escaneo masivo de elementos con teléfonos móviles | 🔴 | 📋 Pendiente sesión de detalle |
| ⬜ | Fraccionar albaranes de clientes por ubicaciones | 🔴 | |
| ⬜ | Llegada masiva de material al almacén | 🔴 | Relacionado con escaneo móvil |
| ⬜ | Modificación albarán de carga | 🟡 | 📋 Pendiente concretar cambios |
| ⬜ | Kanban operaciones: solo semana actual (L-D), estados Aprobado + Pdte. confirmación | 🟡 | Ver `docs/Kanban/logicaKanban.md` |
| ✅ | Kanban dirección: estados Aprobado + Pdte. aprobar | 🟢 | Implementado 19/03/2026 — abre en nueva pestaña, grid 2 col por columna |
| ⬜ | Orden de carga: permitir cambiar artículos | 🟡 | |
| ⬜ | Técnicos asignados a presupuesto + documentación por técnico | 🟡 | Requiere nuevo modelo de datos |
| ⬜ | Mantenimientos de equipos: registrar responsable (quién lo hace) | 🟢 | |
| ⬜ | Histórico de salidas por elemento (presupuestos, fechas, estado, gestor) | 🟢 | Desarrollar después |

---

## FUNCIONALIDADES PLANIFICADAS

| Estado | Tarea | Prio | Notas |
|--------|-------|------|-------|
| ⬜ | Factura agrupada (agrupar varios presupuestos en una sola factura) | 🔴 | 🔗 `docs/Agrupacion/agrupacion.md` |
| ⬜ | Panel Cliente 360° (acceso a presupuestos, facturas, pagos, contactos desde ficha cliente) | 🟡 | 🔗 `docs/Clientes_360/Clientes_360.md` |

---

## PENDIENTES DE VERSIONES / FORMATO

| Estado | Tarea | Doc. referencia |
|--------|-------|-----------------|
| ⬜ | Facturación operativa (flujo completo facturas anticipo, final, abono) | `docs/Pendientes/facturacion_operativa_usuario_20260218.md` |
| ⬜ | Mejoras en gestión de pagos | `docs/Pendientes/pagos_20260211.md` |
| ⬜ | Mejoras en presupuestos | `docs/Pendientes/presupuestos_20260211.md` |
| ⬜ | Road map versiones de presupuesto | `docs/Pendientes/RoadMap_versiones_20260211.md` |
| ⬜ | Modificación formato presupuestos (presentación) | `docs/Pendientes/modi_formatoPresupuestos_20260220.md` |
| ⬜ | Destacar observaciones en pie de presupuesto | `docs/Pendientes/implementacion_destacar_observaciones_pie_20260220.md` |

---

## NOTAS

- El **escaneo masivo con móviles** (punto 1 reunión 18/03) necesita sesión específica antes de planificar.  
- El **histórico de salidas por elemento** (punto 10 reunión 18/03) se deja explícitamente para "desarrollar después" — no bloquea otros entregables.  
- Los dos Kanban (puntos 5 y 6) afectan `docs/Kanban/logicaKanban.md` — revisar antes de implementar para no romper lógica existente.
