# Procedimiento de pruebas — Feature 17
## Empresa fijada universalmente + desbloqueo por saldo cero

> Fecha: 06/03/2026  
> URL base: `http://192.168.31.19/MDR/`  
> Presupuesto de prueba: **P-00007/2026** (`?modo=editar&id=16`) — estado APROBADO  
> Versión activa: v2 aprobada — **total 22.793,53 €** (base 18.837,63 € + IVA 21% 3.955,90 €)  
> Empresa real disponible: **MDR EVENTOS Y PRODUCCIONES S.L.** (id=3, código MDR02)  
> Contadores empresa id=3 al inicio: `numero_actual_factura_empresa = 2`, `numero_actual_abono_empresa = 0`

---

## Estado inicial — Confirmar estado limpio

```sql
-- Debe devolver 0 filas en ambas
SELECT COUNT(*) AS pagos   FROM pago_presupuesto    WHERE id_presupuesto = 16;
SELECT COUNT(*) AS doctos  FROM documento_presupuesto WHERE id_presupuesto = 16;
```

Resultado esperado: **0 filas** en ambas tablas (reset ejecutado previamente).  
`total_pagado = 0` → empresa **libre** → el select de empresa estará habilitado en el primer pago.

---

## T01 — Badge del tab Pagos muestra el contador correcto

**Objetivo:** Verificar que `#badge-pagos` se actualiza al cargar la página en modo edición.

| Paso | Acción | Resultado esperado |
|------|--------|--------------------|
| 1 | Abrir `formularioPresupuesto.php?modo=editar&id=16` | Página carga sin errores |
| 2 | Observar el tab "Pagos" en el encabezado **antes** de hacer clic | Badge muestra **0** (sin pagos aún) |
| 3 | Hacer clic en el tab "Pagos" | DataTable se carga con 0 filas (tabla vacía) |
| 4 | El badge sigue mostrando **0** tras cargar el DataTable | ✅ |

**Verificación de fallo previo:** Antes de Feature 17 el badge no se inicializaba correctamente al cargar la página.

---

## T02 — Primer pago fija empresa (tipo: anticipo, desde estado limpio)

**Objetivo:** Sin pagos previos (`total_pagado = 0`), el select de empresa debe estar **libre**. El usuario elige empresa, guarda y a partir de entonces queda fijada.

| Paso | Acción | Resultado esperado |
|------|--------|--------------------|
| 1 | Tab Pagos → botón **Registrar Pago** | Modal se abre |
| 2 | Observar el select **Empresa emisora** | **Habilitado**, sin preselección forzada — el usuario puede elegir |
| 3 | **No** aparece aviso azul "Empresa bloqueada" | ✅ Correcto — `total_pagado = 0` |
| 4 | Seleccionar empresa: **MDR EVENTOS Y PRODUCCIONES S.L.** (id=3) | |
| 5 | Tipo de pago: **Anticipo** | |
| 6 | Importe: **1000** | |
| 7 | Fecha: **06/03/2026**, Método: cualquiera | |
| 8 | Tipo documento: **Factura Anticipo** | |
| 9 | Clic **Guardar Pago** | SweetAlert de éxito; PDF de factura anticipo abierto |
| 10 | Cerrar modal → DataTable recarga | 1 fila activa; badge pasa a **1** |

**Verificación BD:**
```sql
SELECT id_pago_ppto, tipo_pago_ppto, importe_pago_ppto, id_empresa_pago
FROM   pago_presupuesto
WHERE  id_presupuesto = 16
ORDER  BY id_pago_ppto DESC;
```
Resultado esperado: 1 fila, `id_empresa_pago = 3`.

```sql
SELECT id_documento_ppto, tipo_documento_ppto, numero_documento_ppto, id_empresa
FROM   documento_presupuesto
WHERE  id_presupuesto = 16;
```
Resultado esperado: 1 factura anticipo, `id_empresa = 3`, número **F-0003/2026** (contador 2 → 3).

**Estado tras T02:** `total_pagado = 1.000 €`, saldo pendiente = **21.793,53 €**.

---

## T03 — Segundo pago respeta la empresa ya fijada (tipo: anticipo)

**Objetivo:** Tras T02 (`total_pagado = 1.000 €`), el modal debe mostrar la empresa **bloqueada** en MDR02. El usuario no puede cambiarla.

| Paso | Acción | Resultado esperado |
|------|--------|--------------------||
| 1 | Tab Pagos → botón **Registrar Pago** | Modal se abre |
| 2 | Observar el select **Empresa emisora** | Muestra **MDR EVENTOS Y PRODUCCIONES S.L.** seleccionada y **deshabilitada** (`disabled`) |
| 3 | Aparece aviso azul "Empresa bloqueada" | El texto explica que ya hay pagos con esa empresa |
| 4 | No es posible cambiar la empresa | El select no responde a clics |
| 5 | Tipo de pago: **Anticipo**, Importe: **500**, Fecha: **06/03/2026** | |
| 6 | Tipo documento: **Sin documento** (o Factura Anticipo) | |
| 7 | Guardar | Éxito — DataTable con 2 filas; badge **2** |

> **Nota técnica:** `listar_empresas_facturacion` devuelve `empresa_bloqueada_id = 3`; `cargarEmpresasFacturacion()` hace `$select.val(3).prop('disabled', true)`.

**Estado tras T03:** `total_pagado = 1.500 €`, saldo pendiente = **21.293,53 €**.

---

## T04 — Pago tipo Resto auto-rellena importe y fija empresa

**Objetivo:** Al seleccionar tipo Resto el campo importe se auto-rellena con el saldo pendiente; el pago se guarda y `id_empresa_pago` queda persistido.

| Paso | Acción | Resultado esperado |
|------|--------|--------------------|
| 1 | Modal Registrar Pago → Se abre | |
| 2 | Tipo de pago: **Resto** | Campo Importe se rellena automáticamente con el saldo pendiente (**21.293,53 €**); empresa bloqueada en MDR02 |
| 3 | Verificar que el importe ya tiene el saldo pendiente | ✅ Correcto — `actualizarSeccionesPagoModal()` llama a `verificar_pago_completo` |
| 4 | Fecha y método de pago | |
| 5 | Guardar | SweetAlert de generación de Factura Final → PDF abierto |
| 6 | DataTable recarga con la nueva fila | |

> **Nota técnica:** El mismo auto-relleno aplica también a tipo **Pago Total** (modo creación). En modo edición no se auto-rellena.

**Verificación BD:**
```sql
SELECT id_pago_ppto, tipo_pago_ppto, importe_pago_ppto, id_empresa_pago
FROM   pago_presupuesto
WHERE  id_presupuesto = 16 AND tipo_pago_ppto = 'resto'
ORDER  BY id_pago_ppto DESC LIMIT 1;
```
Resultado esperado: `id_empresa_pago = 3`.

---

## T05 — Saldo pendiente = 0 bloquea todos los tipos de cobro (Feature 17c)

**Objetivo:** Tras T04 (presupuesto completamente pagado, saldo = 0,00 €), verificar que el modal rechaza cualquier intento de cobro independientemente del tipo.

**Estado inicial de T05:** `total_pagado = 22.793,53 €`, saldo pendiente = **0,00 €**

| Paso | Acción | Resultado esperado |
|------|--------|--------------------| 
| 1 | Tab Pagos → botón **Registrar Pago** | Modal se abre |
| 2 | Tipo: **Anticipo**, Importe: **100**, Guardar | SweetAlert error: "El presupuesto ya está completamente pagado (saldo pendiente: 0,00 €)..." |
| 3 | Tipo: **Pago Total**, Importe: **200**, Guardar | SweetAlert error: mismo mensaje |
| 4 | Tipo: **Resto**, Importe: **200**, Guardar | SweetAlert error: mismo mensaje |
| 5 | DataTable de pagos | Ningún pago nuevo añadido — mismo número de filas que tras T04 |

**Verificación BD:**
```sql
SELECT COUNT(*) AS total_pagos_activos
FROM   pago_presupuesto
WHERE  id_presupuesto   = 16
  AND  activo_pago_ppto  = 1
  AND  estado_pago_ppto != 'anulado';
-- Debe ser igual al número de pagos de T01-T04 (sin filas nuevas)
```

---

## T06 — Tipo Devolución no existe en el modal (Feature 17b)

**Objetivo:** Verificar regresión: la opción "Devolución" fue eliminada del select de tipo de pago.

| Paso | Acción | Resultado esperado |
|------|--------|--------------------|
| 1 | Tab Pagos → botón **Registrar Pago** | Modal se abre |
| 2 | Examinar el `<select id="pago_tipo_pago_ppto">` | Opciones visibles: — Seleccionar —, Anticipo, Pago total, Resto / Liquidación |
| 3 | Verificar que **no existe** la opción "Devolución" | ✅ Correcto — opción eliminada |
| 4 | Abrir DevTools → Elements → buscar `value="devolucion"` | No debe existir ningún `<option>` con ese valor |

**Base técnica:** `modal_registrar_pago.php` — opción eliminada; `controller/pago_presupuesto.php` — `'devolucion'` eliminado de `$tipos_validos`; el flujo de generación de factura ya no tiene el guard `tipoPago !== 'devolucion'`. Todo reembolso se gestiona vía "Emitir Abono" en el tab Documentos.



---

## T07 — Empresa se desbloquea cuando total_pagado = 0

**Objetivo:** Anular todos los cobros activos → `total_pagado` baja a 0 → empresa libre en el siguiente modal.

### Paso previo — Calcular total_pagado actual

```sql
SELECT SUM(importe_pago_ppto) AS total_pagado
FROM   pago_presupuesto
WHERE  id_presupuesto  = 16
  AND  activo_pago_ppto = 1
  AND  estado_pago_ppto != 'anulado';
```

### Anular / devolver hasta total_pagado = 0

Opción A (directamente en BD para el test):
```sql
-- Anular todos los pagos activos de cobro del presupuesto 16
UPDATE pago_presupuesto
SET    estado_pago_ppto   = 'anulado',
       activo_pago_ppto   = 0,
       updated_at_pago_ppto = NOW()
WHERE  id_presupuesto   = 16
  AND  tipo_pago_ppto   IN ('anticipo', 'total', 'resto')
  AND  activo_pago_ppto  = 1;
```

Opción B (desde UI): usar el botón **Anular** en cada fila del DataTable de pagos hasta que el resumen financiero muestre **Total pagado: 0,00 €**.

### Verificar desbloqueo

| Paso | Acción | Resultado esperado |
|------|--------|--------------------|
| 1 | Con total_pagado = 0, abrir Modal Registrar Pago | Se abre |
| 2 | Tipo de pago: **Anticipo** | |
| 3 | Observar el select **Empresa emisora** | **Habilitado** y sin preselección forzada |
| 4 | Se puede elegir cualquier empresa | ✅ Empresa libre |

**Verificación BD (confirmar total_pagado = 0):**
```sql
SELECT SUM(importe_pago_ppto) AS total_pagado
FROM   pago_presupuesto
WHERE  id_presupuesto  = 16
  AND  activo_pago_ppto = 1
  AND  estado_pago_ppto != 'anulado';
-- Debe retornar NULL o 0
```

---

## T08 — Verificación global de id_empresa_pago en BD

Tras todos los tests anteriores, confirmar que todos los pagos con empresa tienen la columna rellena:

```sql
SELECT id_pago_ppto,
       tipo_pago_ppto,
       importe_pago_ppto,
       estado_pago_ppto,
       activo_pago_ppto,
       id_empresa_pago,
       e.nombre_empresa
FROM   pago_presupuesto pp
LEFT   JOIN empresa e ON pp.id_empresa_pago = e.id_empresa
WHERE  pp.id_presupuesto = 16
ORDER  BY pp.id_pago_ppto;
```

Resultado esperado: todos los pagos tienen `id_empresa_pago = 3` con `nombre_empresa = 'MDR EVENTOS Y PRODUCCIONES S.L.'`. Los pagos anulados (de T06) tienen `activo_pago_ppto = 0` y `estado_pago_ppto = 'anulado'`.

---

## T09 — Verificar que no hay regresiones en el badge tras guardar

| Paso | Acción | Resultado esperado |
|------|--------|--------------------|
| 1 | Guardar un nuevo pago desde el modal | Modal se cierra |
| 2 | Observar el badge del tab Pagos inmediatamente | Se incrementa en 1 (actualizado por `drawCallback`) |
| 3 | Recargar la página (`F5`) | Badge sigue mostrando el número correcto (actualizado por `cargarContadoresTabs`) |

---

## Resumen de checks de BD de una sola pasada

Ejecutar al finalizar todos los tests:

```sql
-- Check 1: Columna existe con FK correcta
SHOW COLUMNS FROM pago_presupuesto LIKE 'id_empresa_pago';

-- Check 2: FK registrada
SELECT CONSTRAINT_NAME, REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM   INFORMATION_SCHEMA.KEY_COLUMN_USAGE
WHERE  TABLE_NAME = 'pago_presupuesto'
  AND  COLUMN_NAME = 'id_empresa_pago'
  AND  TABLE_SCHEMA = DATABASE();

-- Check 3: Índice creado
SHOW INDEX FROM pago_presupuesto WHERE Key_name = 'idx_empresa_pago_ppto';

-- Check 4: Pagos del presupuesto 16 con empresa
SELECT pp.id_pago_ppto, pp.tipo_pago_ppto, pp.importe_pago_ppto,
       pp.estado_pago_ppto, pp.id_empresa_pago, e.nombre_empresa
FROM   pago_presupuesto pp
LEFT   JOIN empresa e ON pp.id_empresa_pago = e.id_empresa
WHERE  pp.id_presupuesto = 16
ORDER  BY pp.id_pago_ppto;
```
