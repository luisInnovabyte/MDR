# Acta de Reunión de Seguimiento — MDR ERP Manager

> **Fecha:** 18/03/2026
> **Participantes:** Enrique del Rey (Gerente MDR) · Carmen (Responsable Comercial MDR) · Luis Innovabyte (Desarrollo)
> **Estado:** Pendiente de planificación y estimación económica

---

## 1. Estado actual del proyecto

Recorrido completo por los módulos implementados, validados con el cliente durante la reunión.

### 1.1 Gestión de artículos y elementos

- Alta de artículos con codificación automática, familia, peso medio calculado, imagen, idioma ES/EN.
- Control de características especiales: kit, no facturar, permitir/bloquear descuentos, precio editable en presupuesto.
- Herencia de coeficientes desde familia o configuración específica por artículo.
- Gestión de elementos físicos con estado automático (`disponible → preparación → en cliente → retornado`).
- Visualizador de elementos agrupado por estado (disponibles, en reparación, de baja, etc.).
- Gestión documental de elementos en dos capas diferenciadas:
  - **Administración:** facturas de compra/reparación, seguros, fotos.
  - **Técnicos:** manuales de usuario, fichas de seguridad — accesible desde móvil con descarga offline.

### 1.2 Gestión de presupuestos

- Cabecera con datos del evento, fechas, forma de pago, referencia de pedido, observaciones de pie (destacadas o normales).
- Líneas con coeficientes de jornada aplicados automáticamente (toma el tramo inmediatamente superior si no existe el exacto).
- Versiones operativas (v1 → v2 → v3).
- Documento dual: presupuesto para **cliente final** (sin rappel) y documento para **intermediario/hotel** (con descuento por línea).
- Control de descuentos configurable por artículo y por presupuesto.

### 1.3 Informes y analítica

- Calendario de presupuestos con acceso directo a cada evento.
- Informe de rotación de inventario: artículos más alquilados, top 10, tendencia mensual, análisis por familia.
- Informe de ventas por periodo: total aprobado, pendiente de facturar, facturado, comparativa mensual/anual, top clientes.
- Control de disponibilidad de material por artículo y fecha (confirmado vs. presupuestado).

> ⚠️ **Pendiente:** cuadrar las cifras del desglose de ventas por familia — se detectó que podrían estar cogiendo una fecha incorrecta.

### 1.4 Área técnica

- Kanban operativo en ventana de ±14 días (montaje / en curso / desmontaje).
- Albarán de carga con peso estimado (promedio para artículos genéricos, suma exacta para kits) y configuración de nivel de detalle.
- Gestión de elementos: lectura NFC/código, cambio de estado, ubicación (nave, pasillo, altura).

### 1.5 Gestión de vehículos

- Alta de furgonetas con matrícula, ITV, seguro, capacidad de carga (m³ y kg), consumo medio, taller habitual.
- KPIs: total, operativas, bajas, ITV próximas a vencer, seguros próximos a vencer.
- Registro de kilometraje por vehículo con historial.

### 1.6 Gestión de clientes

- Tabla de clientes con dropdown de acciones: editar, contactos, ubicaciones, activar/desactivar.

---

## 2. Nuevas funcionalidades solicitadas

| # | Descripción | Prioridad | Detalle pendiente |
|---|---|---|---|
| 1 | Escaneo masivo NFC con móviles | Alta | Sí — sesión dedicada |
| 2 | Albaranes de cliente fraccionados por ubicación | Alta | No |
| 3 | Llegada masiva de material al almacén | Alta | Relacionado con #1 |
| 4 | Modificaciones en el albarán de carga | Media | Sí — concretar cambios |
| 5 | Kanban semana (L–D): Aprobados y Pdte. confirmación | Media | No |
| 6 | Kanban dirección: Aprobados y Pdte. aprobar | Media | No |
| 7 | Cambio de artículos desde la orden de carga | Media | No |
| 8 | Técnicos asignados a presupuesto + documentación | Media | No |
| 9 | Responsable de mantenimientos de equipos | Baja | No |
| 10 | Histórico de salidas por elemento | Baja | Ampliado — ver §2.10 y §2.13 |
| 11 | Panel 360° del cliente | Alta | Documentado — ver §2.11 |
| 12 | Factura agrupada de múltiples presupuestos | Alta | Documentado — ver §2.12 |
| 13 | DataTable de presupuestos por elemento (ficha de elemento) | Media | Documentado — ver §2.13 |

---

### 2.1 Escaneo masivo de etiquetas NFC con móviles

Registrar e identificar elementos del almacén de forma ágil usando teléfonos móviles, sin pistolas de escaneo especializadas. Afecta a los procesos de salida, retorno y recepción masiva de material.

**Aspectos clave:**

- Leer la etiqueta exterior del rack/caja debe desplegar automáticamente todos los elementos del kit, sin escanear cada uno individualmente.
- El problema identificado está en el **retorno**: al leer el rack exterior el sistema asumiría que vienen todos los elementos aunque pueda faltar alguno.
- Solución propuesta: **sistema de incidencias vinculado** — si al revisar falta algún elemento, se genera una incidencia desde el móvil (texto, foto o nota de voz) que queda registrada y notifica a gerencia/jefe de almacén.
- Se contempla adjuntar fotos desde el móvil para documentar el estado del material (útil para reclamaciones de seguros).

> ⏳ Pendiente sesión específica para detallar el flujo completo y planificar la implementación.

---

### 2.2 Albaranes de cliente fraccionados por ubicación

Los albaranes de entrega al cliente deben poder dividirse por ubicaciones del evento. Cada ubicación generará su propio albarán (o sección diferenciada), facilitando la logística en campo cuando un evento ocurre en múltiples espacios físicos.

---

### 2.3 Llegada masiva de material al almacén

Proceso de recepción masiva al retorno de un servicio: registrar múltiples elementos de forma ágil. Directamente relacionado con el módulo de escaneo masivo (#1). Comparten la misma sesión de planificación pendiente.

---

### 2.4 Modificaciones en el albarán de carga

Cambios exactos pendientes de concretar. Se mencionó en particular la posibilidad de indicar en el albarán cuando un artículo es un **traslado interno** dentro del mismo presupuesto (reutilización del mismo elemento en distintas jornadas o salas), para evitar que el sistema cuente más unidades de las realmente necesarias.

> ⏳ Pendiente sesión para concretar los cambios exactos.

---

### 2.5 Kanban semanal (lunes a domingo)

El Kanban operativo debe mostrar exclusivamente la **semana en curso (lunes a domingo)**, filtrando únicamente presupuestos en estado **Aprobado** y **Pendiente de confirmación**. Actualmente el sistema muestra una ventana de ±14 días, lo que el cliente considera excesivo para la operativa diaria.

---

### 2.6 Kanban de dirección

Vista Kanban independiente para gerencia con presupuestos en estado **Aprobado** y **Pendiente de aprobar**. Permite a dirección ver qué trabajos están en marcha y cuáles requieren aprobación, sin el detalle operativo del Kanban de técnicos.

---

### 2.7 Edición de albaranes de carga (actualmente de solo lectura)

Los albaranes de carga son generados a partir del presupuesto pero actualmente **no pueden ser modificados por el usuario final**. El cliente solicita que sea posible:

- Sustituir un elemento físico asignado por otro de la misma familia/artículo (ej. cámara-003 por cámara-007 porque la primera está en reparación).
- Añadir o retirar elementos puntualmente sin tocar el presupuesto original.
- Ajustar cantidades si en el último momento varía el material que sale físicamente.

**Impacto sobre trazabilidad:** los cambios en el albarán deben quedar auditados (quién lo modificó, cuándo, qué cambió) para que el historial de `linea_salida_almacen` sea fiable. Esto es requisito previo para que el DataTable de §2.13 muestre datos reales y no solo lo planificado en el presupuesto.

> ⚠️ Actualmente `controller/albaranesCarga.php` solo tiene el case `listar`. Habrá que añadir cases de modificación sobre `linea_salida_almacen` con validación de disponibilidad del elemento alternativo.

---

### 2.8 Técnicos asignados a presupuesto y documentación

En cada presupuesto debe poderse indicar qué técnicos van a realizar el trabajo y qué documentación específica aportan para ese servicio (acreditaciones, fichas de seguridad, permisos). Da trazabilidad completa de quién ejecutó cada evento y con qué habilitaciones.

---

### 2.9 Responsable de mantenimientos de equipos

Al registrar un mantenimiento (preventivo, correctivo o reparación) debe quedar registrado quién lo realizó. Distinguir entre trabajos internos (jefe de almacén / técnico MDR) y externos (SAT, taller externo).

---

### 2.10 Histórico de salidas por elemento

En la ficha de cada elemento del inventario, añadir un histórico completo de sus salidas: qué presupuestos lo han incluido, fechas de salida y retorno, estado en cada momento y quién lo gestionó.

> Detalle técnico completo en **§2.13** — DataTable de presupuestos por elemento.

---

### 2.13 DataTable de presupuestos por elemento (ficha de elemento)

Dentro de la ficha de cada elemento físico del inventario, incluir un **DataTable** que muestre todos los presupuestos en los que ese elemento ha participado o está actualmente asignado, cargado vía AJAX en una nueva pestaña de `view/MntElementos/`.

**Fuente de datos correcta — albarán de carga:**

El presupuesto referencia únicamente el *artículo genérico* (`linea_presupuesto.id_articulo`), nunca el elemento físico concreto. La asignación real unidad-a-presupuesto se registra en el momento de la preparación del material, a través de:

```
elemento
  └─▶ linea_salida_almacen.id_elemento
          └─▶ salida_almacen.id_presupuesto
                  └─▶ presupuesto → cliente, estado, empresa...
```

Esto significa que el DataTable refleja los elementos **realmente salidos** en cada presupuesto, no los artículos planificados. Es trazabilidad exacta por unidad física, sin necesidad de crear nueva infraestructura de datos.

**Columnas propuestas del DataTable:**

| Campo | Origen | Notas |
|---|---|---|
| Nº Presupuesto | `presupuesto.numero_presupuesto` | Enlace directo al presupuesto |
| Cliente | `cliente.nombre_cliente + apellido_cliente` | |
| Nombre del evento | `presupuesto.nombre_evento_presupuesto` | |
| Fecha salida | `salida_almacen.fecha_salida_almacen` | Cuándo salió físicamente |
| Fecha retorno | `salida_almacen.fecha_retorno_almacen` | Cuándo volvió (o — si sigue fuera) |
| Fecha inicio evento | `presupuesto.fecha_inicio_evento_presupuesto` | |
| Fecha fin evento | `presupuesto.fecha_fin_evento_presupuesto` | |
| Estado presupuesto | `estado_presupuesto.nombre_estado_ppto` | Badge con color (`color_estado_ppto`) |
| Estado elemento en esa salida | `linea_salida_almacen` / estado actual | Disponible · En preparación · Alquilado · Retornado |
| Es backup | `linea_salida_almacen.es_backup_linea_salida` | Badge si el elemento fue material de sustitución |
| Empresa emisora | `empresa.nombre_empresa` | |
| Responsable de salida | `usuario` via `salida_almacen` | Quién generó el albarán |

**Dependencia con §2.7 — editabilidad de albaranes:**

Si el albarán de carga se modifica tras la generación inicial (sustitución de un elemento por otro, según §2.7), el DataTable debe reflejar el elemento **efectivamente asignado** en `linea_salida_almacen`, no el planificado. La implementación de §2.7 es por tanto un prerequisito para que este DataTable sea completamente fiable.

**Impacto técnico:**

| Capa | Cambio |
|---|---|
| Model | `Elemento.php` → nuevo método `get_presupuestos_por_elemento(int $id_elemento)` via JOIN `linea_salida_almacen → salida_almacen → presupuesto → cliente → estado_presupuesto → empresa` |
| Controller | `elemento.php` → nuevo `case "listar_presupuestos"` |
| View | `view/MntElementos/` → nueva pestaña **Historial de salidas** con DataTable AJAX |

---

### 2.11 Panel 360° del cliente

Ficha centralizada del cliente accesible desde la tabla de mantenimiento de clientes (nueva opción **"Perfil completo"** en el dropdown de acciones).

**Estructura del panel:**

- Cabecera fija: nombre, código, NIF, teléfono, email, estado (badge).
- Bloque KPIs: total presupuestado · facturas emitidas · total cobrado · saldo pendiente.
- Barra de acciones: nuevo presupuesto prellenado con el cliente; botón factura agrupada (deshabilitado hasta implementar esa feature).
- Seis pestañas: **Resumen · Presupuestos · Facturas · Pagos · Contactos · Ubicaciones**.

**Impacto técnico:**

| Capa | Cambio |
|---|---|
| Models | 3 métodos nuevos (`get_presupuestos_por_cliente`, `get_pagos_por_cliente`, `get_documentos_por_cliente`) |
| Controllers | 3 cases nuevos en controladores existentes + 1 nuevo `controller/cliente_panel.php` |
| Views | 1 vista nueva `view/ClientePanel/index.php` + 1 JS nuevo `cliente_panel.js` |
| Sin tocar | Flujo original: editar cliente, contactos, ubicaciones, presupuestos |

**Decisiones pendientes:**

- [ ] ¿El panel hereda permisos del módulo de clientes o requiere permisos propios?
- [ ] ¿`formularioPresupuesto.php` acepta ya `?id_cliente=X` para prerellenar el selector?
- [ ] ¿Los KPIs incluyen presupuestos cancelados o solo activos?
- [ ] ¿El tab de Ubicaciones permite editar o solo lectura desde el panel?

---

### 2.12 Factura agrupada de múltiples presupuestos

Permite agrupar N presupuestos del mismo cliente en una única factura final, contemplando anticipos ya emitidos (reales o proformas).

**Motivación:** clientes como NH o Villaitana facturan mensualmente todos los servicios del periodo en una única factura — el modelo actual centrado en presupuesto individual no puede gestionarlo.

**Solución — nueva entidad `factura_agrupada`:**

- 2 tablas nuevas: `factura_agrupada` (cabecera) + `factura_agrupada_presupuesto` (líneas con snapshot de importes).
- 1 campo nuevo en `pago_presupuesto` (`id_factura_agrupada`) para trazar el origen del pago.
- Flujo: selección de N presupuestos → validaciones → cálculo → transacción (número serie, inserts, anulación proformas, generación pago, PDF).

**Casuística de anticipos:**

| Tipo documento previo | Acción al generar factura agrupada |
|---|---|
| Sin anticipos | Factura por el total completo |
| Proforma de anticipo | Se anula automáticamente. No resta del total |
| Anticipo real | Se mantiene activa. Su importe se deduce |
| Mezcla de ambos | Se anulan proformas, se deducen solo los anticipos reales |

**Impacto técnico:** 2 tablas nuevas + 1 columna + 1 modelo + 2 controladores + 1 vista con wizard.

**Decisiones pendientes:**

- [ ] ¿La factura agrupada consume la serie `F` o una serie específica `FA`?
- [ ] ¿Se permite abonar la factura agrupada completa o solo presupuesto a presupuesto?
- [ ] ¿El PDF muestra el detalle de líneas de cada presupuesto o solo los totales?
- [ ] ¿La vista de selección de presupuestos parte de la ficha de cliente o es pantalla independiente?

---

## 3. Temas transversales y conclusiones técnicas

### 3.1 Estrategia de kits y NFC en almacén

Acuerdo alcanzado sobre la gestión de racks de inalámbricos (cada rack = kit con elementos fijos):

- El artículo genérico (kit) se instancia como varios elementos físicos independientes (rack-A, rack-B…), cada uno con etiqueta NFC en el exterior de la caja.
- Leer la etiqueta del rack despliega automáticamente el contenido completo del kit.
- El control granular de los elementos individuales del rack se delega al **sistema de incidencias**: si al retorno falta algo, se genera una incidencia con foto desde el móvil.
- Los elementos que siempre salen y retornan juntos no necesitan etiqueta individual — la etiqueta va en el contenedor (caja, rack, maleta).

### 3.2 Disponibilidad de material por jornadas

Validado el sistema de disponibilidad día a día dentro del rango montaje–desmontaje. Detecta con precisión solapamientos en fechas concretas y diferencia disponibilidad **confirmada** (solo aprobados) de disponibilidad **presupuestada** (todos los activos).

Pendiente de resolver: cuando un artículo se reutiliza dentro del mismo presupuesto en distintas jornadas o salas (traslado interno), la contabilización debe evitar duplicar la necesidad. Solución propuesta: flag de **"traslado"** en el albarán de carga.

### 3.3 Facturación a intermediarios (hoteles / clientes recurrentes)

Flujo de presupuesto dual ya implementado y validado:

- **Documento A (cliente final):** precio de tarifa sin rappel.
- **Documento B (intermediario/hotel):** rappel acordado aplicado por línea. Es el documento contractual sobre el que se factura.

### 3.4 Control de kilometraje de vehículos

El módulo está construido. El procedimiento operativo queda pendiente de definición interna por MDR. Se acordó como solución pragmática un **registro mensual** por parte del responsable de almacén, que permite controlar el desgaste diferencial entre vehículos y anticipar revisiones.

### 3.5 Roles y permisos por módulo

Arquitectura de acceso confirmada: gerencia, administración, jefe de almacén y técnicos con permisos diferenciados. El cambio de estado de elementos (mantenimiento, reparación) es exclusivo del jefe de almacén y gerencia. Los técnicos tienen acceso de lectura para localizar material y consultar documentación.

### 3.6 Firma digital de empleados

La firma queda registrada en la ficha de usuario y se usa automáticamente en los documentos que ese empleado genera. Cada empleado puede actualizarla desde su perfil.

> ⚠️ Pendiente validar que el guardado funciona correctamente en el entorno de producción.

### 3.7 Bug detectado en pruebas: sistema de versiones solo reconoce la última versión como válida

**Detectado el:** 18/03/2026 durante prueba en entorno de desarrollo.

**Reproducción del problema:**

1. Se generan dos versiones del mismo presupuesto (V1 y V2 como opciones para el cliente final).
2. El cliente rechaza la V2 y aprueba la V1.
3. Al intentar generar los albaranes para los técnicos, el sistema devuelve el error *"no hay ninguna versión aprobada"*.

**Causa raíz identificada:** la lógica de validación busca la versión aprobada tomando como referencia la **última versión creada** (V2 en este caso), comprueba su estado (rechazada) y concluye que no existe ninguna versión válida, ignorando que V1 está aprobada.

**Comportamiento correcto esperado:** la búsqueda de la versión aprobada debe recorrer **todas las versiones activas del presupuesto** y devolver la que tenga estado `aprobada`, independientemente de cuántas versiones posteriores existan rechazadas.

**Ámbito afectado:** generación de albaranes, albarán de carga y cualquier proceso que requiera validar la existencia de una versión aprobada antes de proceder.

---

## 4. Próximos pasos

### 4.1 Acciones inmediatas

- [ ] **[BUG]** Corregir lógica de versiones: debe buscarse la versión en estado `aprobada` entre todas las versiones del presupuesto, no solo la última (§3.7).
- [ ] Resolver discrepancia de cifras en el informe de ventas por familia (fecha incorrecta).
- [ ] Validar guardado de firma digital de empleados en producción.
- [ ] Confirmar si `formularioPresupuesto.php` acepta `?id_cliente=X` para prellenado.
- [ ] Resolver preguntas abiertas del Panel 360° (§2.11) y Factura Agrupada (§2.12).

### 4.2 Sesiones pendientes

- [ ] Sesión dedicada: **escaneo masivo NFC + llegada masiva de material** (§2.1 y §2.3).
- [ ] Sesión para **concretar modificaciones del albarán de carga** (§2.4).

### 4.3 Backlog priorizado

| Prioridad | Trabajo | Notas |
|---|---|---|
| 0 — Urgente | **[BUG]** Versiones: la lógica ignora versiones aprobadas si existe una versión posterior rechazada | Ver §3.7 |
| 1 — Alta | Panel 360° del cliente | Diseño técnico completo disponible |
| 2 — Alta | Factura agrupada de múltiples presupuestos | Diseño técnico completo disponible |
| 3 — Alta | Escaneo masivo NFC + llegada masiva material | Pendiente sesión de detalle |
| 4 — Alta | Albaranes fraccionados por ubicación | Sin pendientes de detalle |
| 5 — Media | Kanban semanal (L–D) | Sin pendientes de detalle |
| 6 — Media | Kanban de dirección | Sin pendientes de detalle |
| 7 — Media | Cambio de artículos desde orden de carga | Sin pendientes de detalle |
| 8 — Media | Técnicos por presupuesto + documentación | Sin pendientes de detalle |
| 9 — Media | Modificaciones albarán de carga | Pendiente concretar cambios |
| 10 — Baja | Responsable de mantenimientos | Sin pendientes de detalle |
| 11 — Baja | Histórico de salidas por elemento | Sin pendientes de detalle |
| 12 — Media | DataTable de presupuestos por elemento | Decisión pendiente: Enfoque A (rápido) vs. Enfoque B (trazabilidad exacta) — ver §2.13 |

---

*Documento generado por Innovabyte · 18/03/2026*
