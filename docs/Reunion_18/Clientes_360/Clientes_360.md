# Panel 360° del Cliente

> **Fecha:** 2026-03-18  
> **Solicitado por:** Cliente  
> **Estado:** Plan aprobado · Pendiente de implementación

---

## Descripción del requisito

Desde la pantalla de mantenimiento de clientes, el usuario quiere acceder a una **ficha completa del cliente** que centralice toda la información relacionada con él: presupuestos, facturas, pagos, contactos y ubicaciones, con capacidad de actuar directamente desde ahí sin navegar a otras pantallas.

---

## Punto de entrada

Nuevo item **"Perfil completo"** en el dropdown de acciones de cada fila de la tabla de clientes (`view/MntClientes/mntclientes.js`).

El dropdown actual ya tiene: Editar · Contactos · Ubicaciones · Activar/Desactivar.  
Se añade entre "Editar" y "Contactos":

```javascript
<li>
    <a class="dropdown-item" href="../ClientePanel/index.php?id_cliente=${row.id_cliente}">
        <i class="bi bi-person-lines-fill me-2"></i>Perfil completo
    </a>
</li>
```

---

## Estructura visual del panel

```
┌──── Panel 360° — [Nombre Cliente] / [Código] ──────────────────────────┐
│  ← Volver a clientes    [NIF]  [Tel]  [Email]              Activo ✓    │
│                                                                         │
│  💰 Total presupuestado   📄 Facturas emitidas   ⏳ Pendiente cobro     │
│     42.300 €                  18.500 €                8.200 €          │
│                                                                         │
│  [ + Nuevo Presupuesto ]    [ Factura Agrupada (próximamente) ]        │
├─────────────────────────────────────────────────────────────────────────┤
│ Resumen │ Presupuestos │ Facturas │ Pagos │ Contactos │ Ubicaciones     │
├─────────────────────────────────────────────────────────────────────────┤
│ [contenido de la pestaña activa — DataTable o ficha]                    │
└─────────────────────────────────────────────────────────────────────────┘
```

### Cabecera fija (siempre visible, encima de los tabs)

- Nombre del cliente + código
- Enlace "← Volver a clientes"
- Datos rápidos: NIF, teléfono, email, estado (badge Activo/Inactivo)
- **Bloque KPIs** (4 cards): Total presupuestado · Facturas emitidas · Total cobrado · Pendiente cobro
- **Barra de acciones agénticas** (ver sección dedicada)

---

## Tabs

### Tab 1 — Resumen
Ficha completa del cliente con todos sus datos: datos fiscales, dirección completa, forma de pago habitual, descuento, observaciones, fechas de alta/actualización.

### Tab 2 — Presupuestos
DataTable con:

| Columna | Detalle |
|---|---|
| Número | Enlace → abre el presupuesto |
| Nombre evento | Texto |
| Fecha inicio / fin evento | Fechas del evento |
| Estado | Badge con color del estado (usando `color_estado_ppto`) |
| Total | Importe con IVA |
| Acciones | Ver PDF · Abrir presupuesto |

Fuente: `controller/presupuesto.php?op=listar_por_cliente` (nuevo)

### Tab 3 — Facturas
DataTable con todos los documentos factura del cliente (anticipos, finales, proformas, abonos):

| Columna | Detalle |
|---|---|
| Tipo | Badge diferenciado por color: Anticipo (verde) · Final (azul) · Proforma (gris) · Abono (rojo) |
| Número | Número de factura |
| Presupuesto origen | Número del presupuesto relacionado |
| Fecha | Fecha de generación |
| Importe | Importe del documento |
| Acciones | Descargar PDF |

Fuente: `controller/documento_presupuesto.php?op=listar_por_cliente` (nuevo)

### Tab 4 — Pagos
DataTable con todos los pagos registrados para los presupuestos del cliente:

| Columna | Detalle |
|---|---|
| Presupuesto | Número del presupuesto al que pertenece |
| Tipo | anticipo · total · resto · devolución |
| Importe | Importe del pago |
| Fecha pago | Fecha |
| Método de pago | Nombre del método |
| Estado | pendiente · recibido · conciliado · anulado |

Fuente: `controller/pago_presupuesto.php?op=listar_por_cliente` (nuevo)

### Tab 5 — Contactos
Reutiliza directamente el controlador existente: `controller/clientes_contacto.php?op=listar&id_cliente=X`  
El endpoint ya acepta `id_cliente` por GET → **no requiere cambios en el backend**.

### Tab 6 — Ubicaciones
Reutiliza el controlador de ubicaciones de cliente con filtro `id_cliente`.  
El endpoint ya acepta `id_cliente` → **no requiere cambios en el backend**.

---

## Acciones agénticas (barra superior)

| Botón | Comportamiento |
|---|---|
| **+ Nuevo Presupuesto** | Enlace a `formularioPresupuesto.php?modo=nuevo&id_cliente=X` — el formulario queda prellenado con el cliente |
| **Factura Agrupada** | Botón deshabilitado con tooltip *"Próximamente — agrupar varios presupuestos en una factura"*. Se activa al implementar la feature de agrupación ([ver docs/Agrupacion/agrupacion.md](../Agrupacion/agrupacion.md)) |

> ⚠️ **Pendiente de confirmar**: verificar si `formularioPresupuesto.php` ya acepta `?id_cliente=X` para prerellenar el selector de cliente, o si hay que añadir esa lógica en el JS del formulario.

---

## Plan de implementación

### Fase 1 — Backend (métodos nuevos, sin romper nada existente)

Los controladores actuales operan por `id_presupuesto`, no por `id_cliente`. Hay que añadir ese filtro.

| # | Archivo | Cambio |
|---|---|---|
| 1 | `models/Presupuesto.php` | Añadir `get_presupuestos_por_cliente(int $id_cliente)` — filtra `vista_presupuesto_completa` por `id_cliente` |
| 2 | `models/PagoPresupuesto.php` | Añadir `get_pagos_por_cliente(int $id_cliente)` — JOIN con `presupuesto` WHERE `id_cliente=?` |
| 3 | `models/DocumentoPresupuesto.php` | Añadir `get_documentos_por_cliente(int $id_cliente)` — JOIN con `presupuesto` WHERE `id_cliente=?`, solo tipos factura |
| 4 | `controller/presupuesto.php` | Añadir `case "listar_por_cliente"` (POST: `id_cliente`) |
| 5 | `controller/pago_presupuesto.php` | Añadir `case "listar_por_cliente"` (POST: `id_cliente`) |
| 6 | `controller/documento_presupuesto.php` | Añadir `case "listar_por_cliente"` (POST: `id_cliente`) |
| 7 | **NUEVO** `controller/cliente_panel.php` | `case "kpis"` devuelve en un solo AJAX los totales consolidados del cliente |

#### KPIs devueltos por `cliente_panel.php?op=kpis`

```json
{
  "total_presupuestado": 42300.00,
  "total_facturas_emitidas": 18500.00,
  "total_cobrado": 10300.00,
  "saldo_pendiente": 8200.00,
  "num_presupuestos": 7,
  "num_presupuestos_activos": 4
}
```

### Fase 2 — Vista

| # | Archivo | Descripción |
|---|---|---|
| 8 | **NUEVO** `view/ClientePanel/index.php` | Página completa con estructura de tabs Bootstrap, cabecera con KPIs y barra de acciones |
| 9 | **NUEVO** `view/ClientePanel/cliente_panel.js` | Inicializa los 6 DataTables, carga KPIs vía AJAX, gestiona cambio de tabs |

### Fase 3 — Punto de entrada

| # | Archivo | Cambio |
|---|---|---|
| 10 | `view/MntClientes/mntclientes.js` | Añadir opción "Perfil completo" en el dropdown de acciones de cada fila |

---

## Resumen de impacto

| Capa | Cambios |
|---|---|
| **Models** | 3 métodos nuevos (uno en cada modelo) |
| **Controllers** | 3 cases nuevos en controladores existentes + 1 controlador nuevo |
| **Views** | 1 página nueva + 1 JS nuevo |
| **Sin tocar** | Flujo original: editar cliente, contactos, ubicaciones, presupuestos — todo intacto |

---

## Dependencias cruzadas

- **Factura Agrupada**: el botón de acción agéntica "Factura Agrupada" depende de la feature documentada en [docs/Agrupacion/agrupacion.md](../Agrupacion/agrupacion.md). El botón existe desde el principio pero deshabilitado.
- **Formulario presupuesto prellenado**: si `formularioPresupuesto.php` no acepta aún `?id_cliente=X`, hay que añadir lógica en `formularioPresupuesto.js` para leer el parámetro de la URL y disparar la selección del cliente al cargar.

---

## Pendientes de decidir

- [ ] ¿El panel 360° requiere permisos de acceso propios, o hereda los del módulo de clientes?
- [ ] ¿Se confirma que `formularioPresupuesto.php` ya acepta `?id_cliente=X`?
- [ ] ¿Los KPIs deben incluir presupuestos inactivos (cancelados) o solo activos?
- [ ] ¿El tab de Ubicaciones debe permitir crear/editar desde el panel, o solo lectura?
