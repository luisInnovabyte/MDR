# Supuestos del Sistema de Pagos y Documentos — MDR ERP

> Basado en la implementación actual (marzo 2026).  
> Archivos clave: `controller/pago_presupuesto.php`, `controller/impresion_factura_*.php`,  
> `models/PagoPresupuesto.php`, `models/DocumentoPresupuesto.php`

---

## Prerequisito General

Para registrar **cualquier pago nuevo**, el presupuesto debe tener estado **Aprobado** (`codigo_estado_ppto = 'APROB'`).  
Si el estado es Borrador, En Proceso, Rechazado o Cancelado → el botón queda bloqueado y se muestra aviso.

La validación se realiza en dos capas:
- **Frontend** (`pagos_presupuesto.js` → `initTabPagos()`): llama a `pago_presupuesto.php?op=verificar_estado_pagable` al entrar al tab.
- **Backend** (`pago_presupuesto.php` → `case "guardaryeditar"`): valida estado antes de ejecutar el INSERT.

---

## Tipos de Pago

### 1. Anticipo

Pago parcial a cuenta del total del presupuesto.

| Aspecto | Detalle |
|---|---|
| `tipo_pago_ppto` | `'anticipo'` |
| Campos obligatorios | importe, fecha, método de pago |
| Campos opcionales | referencia, fecha valor, observaciones |
| `porcentaje_pago_ppto` | Calculado automáticamente: `(importe / total_presupuesto) × 100` |
| Efecto en total pagado | ✅ **Suma** |
| Documento generado | **Factura Anticipo** (serie F) **ó Factura Proforma** (serie FP) — el usuario elige en el modal |
| El modal muestra | Sección "Tipo de documento" con radio button + selección de idioma (Español / English) |
| Descuento agencia/hotel | ❌ **No aplica** — las facturas y proformas de anticipo se emiten siempre **sin descuento de agencia/hotel** |

**Reglas:**
- ✅ El sistema valida que el importe no supere el saldo pendiente. Si lo supera, se devuelve error y no se registra el pago.
- ✅ Si el saldo pendiente es **0,00 €**, el pago se bloquea directamente con un mensaje informativo, independientemente del tipo (anticipo, total, resto). Ver **Feature 17c**.
- Si se elige **Factura Anticipo** → queda registrada en la serie de facturas reales (`F2026/XXXX`). Puede abonarse posteriormente.
- Si se elige **Factura Proforma** → puede reemplazarse después ("Repetir Proforma"). No se abona; la anterior queda inactiva y se crea una nueva.
- El idioma del PDF (Español / English) se selecciona en el mismo modal al registrar el anticipo.

---

### 2. Pago Total

Pago íntegro del presupuesto en un solo cobro (sin anticipos previos).

| Aspecto | Detalle |
|---|---|
| `tipo_pago_ppto` | `'total'` |
| Campos obligatorios | importe, fecha, método de pago |
| `porcentaje_pago_ppto` | Calculado automáticamente |
| Efecto en total pagado | ✅ **Suma** |
| Documento generado | **Factura Final** (serie F) |
| El modal muestra | Sin sección de tipo de documento (genera factura final directamente) |

> ✅ **Implementado:** `impresion_factura_final.php` creado con layout dedicado (azul marino, título FACTURA/INVOICE, detalle completo de líneas, bloque de anticipos previos). Ver `controller/impresion_factura_final.php`.

**Reglas:**
- ✅ El importe no puede superar el saldo pendiente. Si el saldo es **0,00 €**, el pago queda bloqueado (mismo comportamiento que anticipo). Ver **Feature 17c**.

---

### 3. Resto / Liquidación

Pago del saldo pendiente tras uno o varios anticipos previos.

| Aspecto | Detalle |
|---|---|
| `tipo_pago_ppto` | `'resto'` |
| Campos obligatorios | importe, fecha, método de pago |
| `porcentaje_pago_ppto` | Calculado automáticamente |
| Efecto en total pagado | ✅ **Suma** |
| Documento generado | **Factura Final** (serie F) — igual que Pago Total |
| El modal muestra | Sin sección de tipo de documento |

> ✅ **Validación implementada:** el sistema valida que exista al menos un anticipo previo activo y no anulado antes de permitir registrar un Resto. Si no hay anticipos → error y no se registra el pago.

**Reglas:**
- ✅ El importe no puede superar el saldo pendiente. Si el saldo es **0,00 €**, el pago queda bloqueado. Ver **Feature 17c**.
- ✅ Requiere al menos un anticipo previo activo (V2).

---

### 4. Devolución — ❌ *Tipo eliminado (Feature 17b, 06/03/2026)*

> El tipo `devolucion` ha sido **eliminado del modal de Registrar Pago** y del controller.  
> Cada reembolso siempre está ligado a un documento:
> - **Proforma** → "Repetir Proforma" (la anterior queda inactiva, se crea una nueva; sin abono fiscal).
> - **Factura real (F)** → "Emitir Abono" desde el tab Documentos → genera `factura_rectificativa` (serie `R`) y anula automáticamente el pago vinculado.
>
> El valor `'devolucion'` se conserva en el `ENUM` de `pago_presupuesto.tipo_pago_ppto` para no invalidar registros históricos, pero ya **no aparece como opción en el modal** ni es aceptado en `$tipos_validos` del controller.

---

## Tipos de Documento

### Cuándo se genera cada uno

```
PRESUPUESTO APROBADO
        │
        ├─ Registro pago ANTICIPO ──┬─ [elige Factura Anticipo] ──► factura_anticipo   (F2026/XXXX)
        │                           └─ [elige Proforma]          ──► factura_proforma   (FP2026/XXXX)
        │
        ├─ Registro pago TOTAL ──────────────────────────────────► factura_final *     (F2026/XXXX)
        │
        ├─ Registro pago RESTO ──────────────────────────────────► factura_final *     (F2026/XXXX)
        │
        ├─ Botón "Generar Proforma" (tab Documentos) ────────────► factura_proforma    (FP2026/XXXX)
        │
        └─ Botón "Emitir Abono" sobre factura F activa ──────────► factura_rectificativa (R2026/XXXX)

* ✅ Implementada: controller dedicado impresion_factura_final.php
```

---

### A. Factura Proforma (`factura_proforma`)

| | |
|---|---|
| Serie / Numeración | `FP` — `FP2026/XXXX` |
| Color PDF | Azul `(102, 126, 234)` |
| Carpeta PDF | `public/documentos/proformas/` |
| ¿Se puede abonar? | ❌ No. Se "repite": la anterior queda inactiva y se crea una nueva |
| ¿Bloquea empresa emisora? | ✅ Sí (fija la empresa para el resto de facturas del presupuesto) |
| Usos | Anticipo sin factura oficial · Total/Resto (provisional) · Generación manual desde tab Documentos |
| Contenido | Línea de anticipo ó detalle completo de líneas según `tipo_contenido` |
| Requiere empresa real | ✅ Sí |
| Idioma PDF | 🌐 Seleccionable: Español / English |
| Descuento agencia/hotel | ❌ **No aplica** — siempre se emite sin descuento de agencia/hotel |

---

### B. Factura de Anticipo (`factura_anticipo`)

| | |
|---|---|
| Serie / Numeración | `F` — `F2026/XXXX` (misma serie que facturas finales) |
| Color PDF | Verde `(39, 174, 96)` |
| Carpeta PDF | `public/documentos/anticipos/` |
| ¿Se puede abonar? | ✅ Sí → genera `factura_rectificativa` |
| ¿Bloquea empresa emisora? | ✅ Sí |
| Usos | Pago anticipo con factura oficial |
| Contenido | Una sola línea: `"A cuenta del presupuesto [Nº]"` + desglose IVA |
| Requiere empresa real | ✅ Sí (ficticia → bloqueado) |
| Idioma PDF | 🌐 Seleccionable: Español / English |
| Descuento agencia/hotel | ❌ **No aplica** — siempre se emite sin descuento de agencia/hotel |
| Datos bancarios en PDF | Solo si: forma de pago incluye "TRANSFERENCIA" **y** empresa tiene IBAN **y** `mostrar_cuenta_bancaria_pdf = true` |

---

### C. Factura Final (`factura_final`)

| | |
|---|---|
| Serie / Numeración | `F` — `F2026/XXXX` (misma serie que anticipos) |
| Color PDF | Azul marino `(41, 128, 185)` |
| Carpeta PDF | `public/documentos/facturas/` |
| ¿Se puede abonar? | ✅ Sí → genera `factura_rectificativa` |
| ¿Bloquea empresa emisora? | ✅ Sí |
| Usos | Pago total o liquidación (resto) |
| Contenido | Detalle completo de líneas + desglose IVA + bloque anticipos previos (si existen) |
| Requiere empresa real | ✅ Sí |

> ✅ **Implementada.** Controller: `impresion_factura_final.php`. Layout dedicado con título FACTURA / INVOICE, líneas completas del presupuesto y sección informativa de anticipos previos cuando `tipo = resto`.

---

### D. Factura de Abono / Nota de Crédito (`factura_rectificativa`)

| | |
|---|---|
| Serie / Numeración | `R` — `R2026/XXXX` |
| Color PDF | Rojo `(192, 57, 43)` |
| Carpeta PDF | `public/documentos/abonos/` |
| ¿Se puede abonar? | ❌ No (ya es un abono) |
| Requiere empresa real | ✅ Sí |
| Importes en BD | **Negativos** (subtotal, iva, total) |
| Referencia a origen | `id_documento_origen` → FK a la factura original |
| Motivo | Campo `motivo_abono_documento_ppto` (obligatorio al emitir) |

**Condiciones para poder emitir abono:**
1. El documento origen debe ser `factura_anticipo` ó `factura_final` (no proforma, no presupuesto).
2. El documento origen debe estar activo (`activo_documento_ppto = 1`).
3. No puede existir ya una `factura_rectificativa` activa para ese mismo origen (una sola por factura).

**Proceso:**
1. Usuario hace clic en "Emitir Abono" en el DataTable de Documentos (botón solo visible en facturas F activas).
2. Se abre modal con campo de motivo.
3. Al confirmar → `impresion_factura_abono.php?op=generar`.
4. Se inserta `factura_rectificativa` con importes negativos y `id_documento_origen`.
5. se genera PDF con todas las líneas en rojo e importes negativos.

> ✅ **El pago vinculado se anula automáticamente.** Al emitir el abono, `impresion_factura_abono.php` llama a `PagoPresupuesto::get_pago_vinculado_documento()` + `anular_pago_por_abono()` para anular el pago asociado. El JSON de respuesta incluye `pago_anulado` e `id_pago_anulado`.

---

### E. PDF de Presupuesto (`presupuesto`)

| | |
|---|---|
| Serie / Numeración | `P` — `P2026/XXXX` (numeración propia) |
| Controller | `impresionpresupuesto_m2_pdf_es.php` |
| ¿Se puede abonar? | ❌ No |
| ¿Bloquea empresa emisora? | ❌ No |
| Usos | Envío al cliente para aprobación |

---

### F. Parte de Trabajo (`parte_trabajo`)

| | |
|---|---|
| Serie / Numeración | Sin numeración correlativa |
| Controller | `impresionpartetrabajo_m2_pdf_es.php` |
| ¿Se puede abonar? | ❌ No |
| Usos | Documento operacional sin precios (para el equipo de montaje) |

---

## Tabla Resumen: Tipo de Pago → Documento

| Tipo de pago | Documento generado | Serie | Cómo se genera |
|---|---|---|---|
| Anticipo | Factura Anticipo | `F` | Al guardar el pago (usuario elige esta opción) |
| Anticipo | Factura Proforma | `FP` | Al guardar el pago (usuario elige esta opción) |
| Total | Factura Final | `F` | Al guardar el pago |
| Resto | Factura Final | `F` | Al guardar el pago |
| — | Factura Proforma | `FP` | Manual desde botón "Generar Proforma" en tab Documentos |
| — | Abono / Nota de crédito | `R` | Manual desde botón "Emitir Abono" sobre factura F activa |

---

## Estados del Pago — Ciclo de Vida

```
[INSERT] → recibido
               │
               ├──► [op=conciliar]  ──► conciliado   (comprobado en extracto bancario)
               │
               └──► [op=anular]     ──► anulado       (queda en histórico, activo = 1)

[op=desactivar] → activo = 0   (soft delete, desaparece del listado)
```

**Botones visibles en el DataTable de Pagos:**

| Botón | Condición |
|---|---|
| ✏️ Editar | `estado != 'anulado'` y `activo = 1` |
| ✅ Conciliar | `estado = 'recibido'` y `activo = 1` |
| 🚫 Anular | `activo = 1` y `estado != 'anulado'` |
| 🗑️ Eliminar (soft) | `activo = 1` |

---

## Empresa Emisora — Bloqueo

Una vez emitida la **primera factura real** (proforma, anticipo ó final), la empresa emisora queda **bloqueada** para ese presupuesto:

- El select de empresa en el modal muestra el nombre con 🔒 y queda deshabilitado.
- Si se intenta cambiar → error `422` con el nombre de la empresa fijada.
- **Solo aplica** a documentos tipo factura. El PDF de presupuesto y parte de trabajo son independientes.

---

## Cambios Pendientes — Factura Anticipo y Proforma de Anticipo

> Recogidos el 05/03/2026. Afectan a `controller/impresion_factura_anticipo.php` y `controller/impresion_factura_proforma.php` cuando `tipo_contenido = anticipo`.

---

### 1. ¿De dónde sale el IVA que se aplica en la factura de anticipo?

**Situación actual:** el porcentaje IVA se obtiene de `$_POST['porcentaje_iva'] ?? 21`. Es decir, el modal puede enviarlo; si no lo envía, se usa el **21 % como valor por defecto hardcodeado**. No se lee del presupuesto ni de sus líneas.

**Contexto:** las líneas del presupuesto tienen `porcentaje_iva_linea_ppto` (FK a `impuesto.tasa_impuesto`). El modelo `ImpresionPresupuesto::get_desglose_iva()` ya calcula el desglose real por tipo de IVA a partir de esas líneas.

**Decisión pendiente:** definir si el IVA de la factura anticipo debe:
- (a) Calcularse a partir del desglose real de líneas del presupuesto (preferido para coherencia fiscal).
- (b) Seguir siendo un valor fijo enviado desde el modal (más sencillo pero menos preciso si hay líneas con distintos tipos de IVA).

---

### 2. Observaciones de cabecera

Mostrar el campo de observaciones de cabecera del presupuesto en el idioma del documento:

| Idioma | Campo | Tabla |
|---|---|---|
| Español | `observaciones_cabecera_presupuesto` | `presupuesto` |
| English | `observaciones_cabecera_ingles_presupuesto` | `presupuesto` |

- Si el campo está vacío → no se muestra nada (no dejar espacio en blanco).
- Se coloca **antes del bloque de líneas / desglose IVA** (zona de cabecera del cuerpo del PDF).

---

### 3. Concepto de la línea de anticipo

**Actual:** `"A cuenta del presupuesto [NUMERO_PRESUPUESTO]"`

**Nuevo:** `"Entrega a cuenta confirmación presupuesto [NUMERO_PRESUPUESTO]"`

- El número de presupuesto se sigue concatenando igual que ahora.
- Aplica tanto a `factura_anticipo` como a `factura_proforma` con `tipo_contenido = anticipo`.
- El resto del contenido (desglose IVA, totales, etc.) permanece igual.

---

### 4. Bloque de datos bancarios (si transferencia)

Ya existe la lógica en `factura_anticipo` (campo `mostrar_cuenta_bancaria_pdf`). Verificar que se cumple en `factura_proforma` de anticipo también.

**Condición de aparición (igual que hasta ahora):**
1. La forma de pago del presupuesto incluye la palabra `"TRANSFERENCIA"` (case-insensitive).
2. La empresa emisora tiene IBAN (`iban_empresa` no vacío).
3. El flag `mostrar_cuenta_bancaria_pdf = true` en la empresa.

**Ubicación:** al final del cuerpo del PDF, antes del pie de página.

---

### 5. Pie de página — texto legal de la empresa (`texto_pie_factura_empresa`)

- Campo: `empresa.texto_pie_factura_empresa`
- Empresa: la emisora de la factura (real o proforma), no la ficticia principal.
- Tipografía: **tamaño muy pequeño** (≈ 6–7 pt), color gris.
- Ubicación: en el **Footer** del PDF (método `Footer()` de TCPDF), centrado o alineado a la izquierda, en la franja inferior de cada página.
- Si el campo está vacío → no se imprime nada.
- Aplica a: `factura_anticipo` y `factura_proforma` de anticipo. Pendiente revisar si también aplica a `factura_final` y `factura_rectificativa` (previsiblemente sí).

> ⚠️ Actualmente el Footer de `MYPDF_ANTICIPO` no imprime este campo. Hay que añadirlo.

---

## Lagunas y Pendientes

| # | Descripción | Estado | Implementado en |
|---|---|---|---|
| 1 | `factura_final` no tiene controller dedicado — usa proforma como soporte provisional | ✅ Resuelto | `controller/impresion_factura_final.php` |
| 2 | Al abonar una factura, el pago vinculado **no se anula automáticamente** | ✅ Resuelto | `controller/impresion_factura_abono.php` |
| 3 | No se valida que el importe del anticipo no supere el saldo pendiente | ✅ Resuelto | `controller/pago_presupuesto.php` case guardaryeditar V1 |
| 4 | No se valida que haya anticipos previos antes de registrar un Resto | ✅ Resuelto | `controller/pago_presupuesto.php` case guardaryeditar V2 |
| 5 | Tipo `devolucion` en modal de pagos — eliminado: cada reembolso es siempre documental (Proforma → Repetir; Factura F → Emitir Abono) | ✅ Eliminado | `modal_registrar_pago.php`, `controller/pago_presupuesto.php`, `pagos_presupuesto.js` |
| 6 | IVA anticipo hardcodeado a 21 % — selector en modal (4/10/21 %) enviado por POST | ✅ Resuelto | `modal_registrar_pago.php` + `pagos_presupuesto.js` + `impresion_factura_anticipo.php` + `impresion_factura_proforma.php` |
| 7 | Observaciones cabecera no se muestran en factura anticipo/proforma | ✅ Resuelto | `impresion_factura_anticipo.php` + `impresion_factura_proforma.php` |
| 8 | Concepto línea anticipo: cambiado a "Entrega a cuenta confirmación presupuesto" (ES) / "On account of confirmation of quotation" (EN) | ✅ Resuelto | `impresion_factura_anticipo.php` + `impresion_factura_proforma.php` |
| 9 | Datos bancarios nunca aparecían — dos causas: (a) campo erróneo `nombre_pago` (forma de pago del presupuesto) en lugar de `nombre_metodo_pago` del pago real; (b) flag `mostrar_cuenta_bancaria_pdf=0` en ambas empresas | ✅ Resuelto | Ver sección "Bug 13" |
| 10 | `texto_pie_factura_empresa` ahora se usa correctamente en Footer del PDF anticipo/proforma | ✅ Resuelto | `impresion_factura_anticipo.php` + `impresion_factura_proforma.php` |
| 11 | Select de empresa vacío — `ficticia_empresa=1` en todas las empresas + bug `isset()` en controller | ✅ Resuelto | Ver sección "Bug 11" |
| 12 | Selector IVA no visible hasta seleccionar tipo=anticipo — código correcto, visible por change event | ✅ Resuelto | `pagos_presupuesto.js` — `.fail()` + trigger |
| 13 | Bloque bancarios: comprobaba forma de pago del presupuesto en vez del método real del pago | ✅ Resuelto | `impresion_factura_anticipo.php` + `impresion_factura_proforma.php` |
| 14 | `ficticia_empresa` en formulario empresa siempre se guardaba como 1 — `isset()` vs valor real | ✅ Resuelto | `controller/empresas.php` |
| 15 | Ajustes visuales PDF: espacio entre caja azul y obs. cabecera; ancho caja bancaria reducido; separación nota legal | ✅ Resuelto | `impresion_factura_anticipo.php` + `impresion_factura_proforma.php` || 16 | No se valida importe vs saldo para tipos Total y Resto; con saldo=0 era posible guardar cualquier cobro | ✅ Resuelto | `controller/pago_presupuesto.php` V1 extendida a todos los tipos (Feature 17c) || 17 | Empresa emisora no se fijaba para devoluciones ni cuando el único documento era un abono | ✅ Resuelto | Ver sección "Feature 17" |

---

## Bug 11 — Select de empresa vacío en el modal de anticipo

> Detectado: 05/03/2026 — Resuelto: 05/03/2026

### Diagnóstico

La migración `20260304_01_alter_empresa_proforma.sql` **ya estaba aplicada** — las columnas existen. La causa real es que **todas las empresas tenían `ficticia_empresa = 1`**:

```sql
-- Resultado real de la BD a 05/03/2026:
-- id=1  MDR Audiovisuales Group          ficticia_empresa=1
-- id=3  MDR EVENTOS Y PRODUCCIONES S.L.  ficticia_empresa=1
```

La query de `get_empresas_reales_activas()` filtra `WHERE ficticia_empresa = 0` → devolvía vacío → select sin opciones.

### Causa raíz del bug de guardado

El formulario de empresa (`formularioEmpresa.js`) siempre envía `ficticia_empresa` con valor `'0'` o `'1'` en el POST. El controlador usaba `isset($_POST["ficticia_empresa"]) ? 1 : 0` — como el campo **siempre existe** (el JS lo envía explícitamente), `isset()` siempre devolvía `true` → `ficticia_empresa` se guardaba siempre como `1`, independientemente de si el checkbox estaba marcado o no.

### Solución aplicada

**`controller/empresas.php`** — `case "guardaryeditar"` (INSERT y UPDATE):

```php
// ANTES (bug):
isset($_POST["ficticia_empresa"]) ? 1 : 0

// AHORA (fix):
intval($_POST["ficticia_empresa"] ?? 0)
```

Mismo fix aplicado a `empresa_ficticia_principal`.

**BD:** `mostrar_cuenta_bancaria_pdf_presupuesto_empresa` activado a `1` en ambas empresas.

---

## Bug 13 — Bloque de datos bancarios nunca aparecía en PDF anticipo/proforma

> Detectado: 05/03/2026 — Resuelto: 05/03/2026

### Diagnóstico

El bloque bancario tiene **3 condiciones `&&`** que deben cumplirse simultáneamente:

1. `$es_transferencia` — la forma/método de pago contiene "TRANSFERENCIA"
2. `$tiene_datos_ban` — empresa tiene IBAN o banco rellenos
3. `$mostrar_cuenta` — flag `mostrar_cuenta_bancaria_pdf_presupuesto_empresa = 1` en empresa

**Causa 1 (código):** el código usaba `$datos_ppto['nombre_pago']`, que contiene la **forma de pago del presupuesto** (términos como `"40% anticipo + 60% al finalizar"`). Ese campo nunca contiene "transferencia". El campo correcto es `nombre_metodo_pago` del **pago registrado** (`v_pagos_presupuesto`), que sí puede ser `"Transferencia bancaria"`.

**Causa 2 (config BD):** `mostrar_cuenta_bancaria_pdf_presupuesto_empresa = 0` en ambas empresas.

### Solución aplicada

**`controller/impresion_factura_anticipo.php`** y **`controller/impresion_factura_proforma.php`**:

- Se carga `$pagoTmp` (datos del pago desde `v_pagos_presupuesto`) siempre, no solo cuando falta el importe.
- Se inyecta `$datos_ppto['nombre_metodo_pago_pago'] = $pagoTmp['nombre_metodo_pago']` tras cargar los datos del presupuesto.
- La comprobación usa `$datos_ppto['nombre_metodo_pago_pago'] ?? $datos_ppto['nombre_pago']` (fallback por compatibilidad).

**BD:** `UPDATE empresa SET mostrar_cuenta_bancaria_pdf_presupuesto_empresa = 1 WHERE id_empresa IN (1, 3);`

### Condición de aparición del bloque (confirmada)

El bloque bancario aparece si y solo si, al registrar el pago anticipo, se selecciona **"Transferencia bancaria"** como método de pago en el modal (no depende de la forma de pago del presupuesto).

---

## Bug 12 — Selector de IVA no visible en el modal

> Detectado: 05/03/2026

### Diagnóstico

El selector `#pago_porcentaje_iva` está dentro de `#seccionTipoDocumento` (oculto por defecto con `d-none`). La sección se muestra automáticamente al seleccionar tipo=anticipo vía el `change` event — el mecanismo **funciona correctamente**.

El síntoma "nunca pregunta el IVA" se reproducía porque el Bug 11 activo hacía que el select de empresa estuviera vacío, lo que impedía completar el flujo antes de llegar a seleccionar el tipo.

### Mejora de código aplicada

En `abrirModalRegistrarPago()` (modo nuevo): reemplazado `actualizarSeccionesPagoModal()` directo por `$('#pago_tipo_pago_ppto').trigger('change')` para que el estado visual siempre se calcule a través del mecanismo de eventos (más robusto ante valores residuales pre-reset).


---

## Mejora 15 — Ajustes visuales: espacio cabecera + caja bancaria reducida

> Implementado: 05/03/2026

### Cambios aplicados (anticipo y proforma)

**1. Espacio entre caja azul (Ref. Presupuesto) y observaciones de cabecera**
- Antes: sin espacio — el texto de observaciones comínzaba inmediatamente bajo la caja.
- Ahora: `$pdf->Ln(5);` tras `AddPage()`, antes del bloque `obs_cab`.

**2. Rectángulo gris de datos bancarios: anchura 194mm → 130mm**
- `Rect(8, $y_banco, 130, $altura_banco, 'DF')` — ocupa solo la mitad derecha del folio libre.
- `Cell` de la línea "Forma de pago": `190` → `125`.
- `Cell` de valores (Banco/IBAN/SWIFT): `165` → `103`.
- Geometría: x=8 + 2(padding) + 25(etiqueta) + 103(valor) = 138mm (dentro del rect 130mm desde x=8).

**3. Separación nota legal proforma respecto al rectángulo gris**
- `$pdf->Ln(3)` → `$pdf->Ln(5)` al cerrar el bloque bancario, para que "Este documento es una factura proforma..." no se monte con el borde del rectángulo.

---

## Bug 16 — Segundo anticipo: "No hay empresas de facturación disponibles"

> Detectado: 05/03/2026

### Descripción

Cuando un presupuesto ya tiene un anticipo con factura emitida, al registrar un segundo anticipo el modal muestra `#alertaSinEmpresasDisponibles` en lugar de pre-seleccionar la empresa del primer anticipo.

### Causa raíz

1. `cargarEmpresasFacturacion()` llama al controller con `id_presupuesto`.
2. Controller devuelve `empresa_bloqueada_id` + `bloqueada: true` para esa empresa.
3. JS renderizaba la opción con `disabled: true` → `algunaDisponible = false` → mostraba el error.
4. JS ignoraba `response.empresa_bloqueada_id` que trae la respuesta a nivel raíz.

### Solución aplicada

Modificado `cargarEmpresasFacturacion()` en `pagos_presupuesto.js`:

- Al inicio: `$select.prop('disabled', false)` para resetear estado de sesiones previas.
- Leer `const idBloqueada = response.empresa_bloqueada_id ? parseInt(response.empresa_bloqueada_id) : null`.
- Opciones se renderizan **sin** `disabled` (solo icono 🔒 en el label).
- Si `idBloqueada`: `$select.val(idBloqueada).prop('disabled', true)` + mostrar `#alertaEmpresaBloqueada`.
- Solo muestra `#alertaSinEmpresasDisponibles` si no hay empresas disponibles Y no hay `idBloqueada`.


---

## Feature 17 - Empresa fijada universalmente (todos los tipos de pago) + desbloqueo por saldo cero

> Implementado: 06/03/2026

### Motivacion

El bloqueo de empresa previo (Bug 16) solo funcionaba si existia un documento_presupuesto activo de tipo actura_proforma, actura_anticipo o actura_final. Dejaba dos huecos:

1. **Devoluciones**: no generan documento automatico, registrar una devolucion no fijaba la empresa para los pagos siguientes.
2. **Post-abono**: al emitir un abono, el pago origen queda nulado pero el documento actura_anticipo origen NO se desactiva, por lo que la empresa permanecia bloqueada aunque el saldo efectivo fuera 0.

### Regla de negocio definitiva

> **Sea cual sea el tipo del primer pago (anticipo, total, resto), la empresa elegida queda fijada para todo el presupuesto.**
> **Cuando 	otal_pagado = 0 (todos los cobros han sido anulados o devueltos), la empresa se desbloquea.**

Nota: devolucion como primer movimiento no es posible (validacion V3 en guardaryeditar exige cobros previos).

### Cambios tecnicos

#### 1. BD - nueva columna id_empresa_pago

Migracion: BD/migrations/20260306_01_alter_pago_presupuesto_empresa.sql

\\\sql
ALTER TABLE pago_presupuesto
    ADD COLUMN id_empresa_pago INT UNSIGNED NULL AFTER id_documento_ppto,
    ADD CONSTRAINT fk_pago_ppto_empresa
        FOREIGN KEY (id_empresa_pago)
        REFERENCES empresa(id_empresa)
        ON DELETE RESTRICT ON UPDATE CASCADE;
CREATE INDEX idx_empresa_pago_ppto ON pago_presupuesto (id_empresa_pago);
\\\

#### 2. models/PagoPresupuesto.php

- insert_pago() - acepta y persiste id_empresa_pago (nullable).
- update_pago() - anadido id_empresa_pago al map de campos actualizables.
- **Nuevo metodo** obtener_empresa_bloqueada_por_pagos(int id_presupuesto):
  - Si 	otal_pagado <= 0 devuelve alse (empresa libre).
  - Si 	otal_pagado > 0 devuelve el registro de empresa del primer pago activo, no-anulado, con id_empresa_pago IS NOT NULL.

#### 3. controller/pago_presupuesto.php

- case "guardaryeditar" - lee $_POST['id_empresa'] (INT nullable) y lo pasa como $datos['id_empresa_pago'].
- case "listar_empresas_facturacion" - sustituye DocumentoPresupuesto::obtener_empresa_bloqueada_presupuesto() por PagoPresupuesto::obtener_empresa_bloqueada_por_pagos().

#### 4. iew/Presupuesto/pagos_presupuesto.js

- guardarPago() - anade id_empresa explicitamente al POST (funciona tambien cuando select esta disabled).
- guardarPago() - para 	ipoPago === 'devolucion' no se llama a generarFacturaPago().

### Flujo post-implementacion

| Escenario | Comportamiento |
|---|---|
| Primer pago empresa A | id_empresa_pago = A; siguiente pago -> A bloqueada |
| Pago anulado via abono -> 	otal_pagado = 0 | empresa libre |
| Dos pagos empresa A -> uno anulado | 	otal_pagado > 0 -> A sigue bloqueada |
| Devolucion empresa A | id_empresa_pago = A; siguiente pago -> A bloqueada |


---

## Feature 17 - Empresa fijada universalmente (todos los tipos de pago) + desbloqueo por saldo cero

> Implementado: 06/03/2026

### Motivacion

El bloqueo de empresa previo (Bug 16) solo funcionaba si existia un documento_presupuesto activo de tipo actura_proforma, actura_anticipo o actura_final. Dejaba dos huecos:

1. **Devoluciones**: no generan documento automatico, registrar una devolucion no fijaba la empresa para los pagos siguientes.
2. **Post-abono**: al emitir un abono, el pago origen queda nulado pero el documento actura_anticipo origen NO se desactiva, por lo que la empresa permanecia bloqueada aunque el saldo efectivo fuera 0.

### Regla de negocio definitiva

> **Sea cual sea el tipo del primer pago (anticipo, total, resto), la empresa elegida queda fijada para todo el presupuesto.**
> **Cuando 	otal_pagado = 0 (todos los cobros han sido anulados o devueltos), la empresa se desbloquea.**

Nota: devolucion como primer movimiento no es posible (validacion V3 en guardaryeditar exige cobros previos).

### Cambios tecnicos

#### 1. BD - nueva columna id_empresa_pago

Migracion: BD/migrations/20260306_01_alter_pago_presupuesto_empresa.sql

\\\sql
ALTER TABLE pago_presupuesto
    ADD COLUMN id_empresa_pago INT UNSIGNED NULL AFTER id_documento_ppto,
    ADD CONSTRAINT fk_pago_ppto_empresa
        FOREIGN KEY (id_empresa_pago)
        REFERENCES empresa(id_empresa)
        ON DELETE RESTRICT ON UPDATE CASCADE;
CREATE INDEX idx_empresa_pago_ppto ON pago_presupuesto (id_empresa_pago);
\\\

#### 2. models/PagoPresupuesto.php

- insert_pago() - acepta y persiste id_empresa_pago (nullable).
- update_pago() - anadido id_empresa_pago al map de campos actualizables.
- **Nuevo metodo** obtener_empresa_bloqueada_por_pagos(int id_presupuesto):
  - Si 	otal_pagado <= 0 devuelve alse (empresa libre).
  - Si 	otal_pagado > 0 devuelve el registro de empresa del primer pago activo, no-anulado, con id_empresa_pago IS NOT NULL.

#### 3. controller/pago_presupuesto.php

- case "guardaryeditar" - lee $_POST['id_empresa'] (INT nullable) y lo pasa como $datos['id_empresa_pago'].
- case "listar_empresas_facturacion" - sustituye DocumentoPresupuesto::obtener_empresa_bloqueada_presupuesto() por PagoPresupuesto::obtener_empresa_bloqueada_por_pagos().

#### 4. iew/Presupuesto/pagos_presupuesto.js

- guardarPago() - anade id_empresa explicitamente al POST (funciona tambien cuando select esta disabled).
- guardarPago() - para 	ipoPago === 'devolucion' no se llama a generarFacturaPago().

### Flujo post-implementacion

| Escenario | Comportamiento |
|---|---|
| Primer pago empresa A | id_empresa_pago = A; siguiente pago -> A bloqueada |
| Pago anulado via abono -> 	otal_pagado = 0 | empresa libre |
| Dos pagos empresa A -> uno anulado | 	otal_pagado > 0 -> A sigue bloqueada |
| Devolucion empresa A | id_empresa_pago = A; siguiente pago -> A bloqueada |


---

## Feature 17c — Bloqueo universal cuando saldo pendiente = 0

> Implementado: 06/03/2026

### Motivación

La validación V1 del controller solo cubría el tipo `anticipo`. Para `total` y `resto` no existía ninguna comprobación de importe vs saldo: con saldo = 0 (presupuesto completamente pagado), se podía guardar cualquier pago de tipo Total o Resto sin error.

### Regla de negocio

> **Si el saldo pendiente es ≤ 0,00 €, ningún tipo de cobro (anticipo, total, resto) puede registrarse.**
> **Si el importe supera el saldo pendiente, también se bloquea (cualquier tipo).**

### Cambios técnicos

#### `controller/pago_presupuesto.php` — `case "guardaryeditar"` (V1 extendida)

**Antes:** solo validaba el tipo `anticipo` contra el saldo pendiente. `total` y `resto` no tenían ninguna comprobación.

**Ahora:** se obtiene `$saldoActual` una sola vez para todos los tipos:
- Si `$saldoActual <= 0` → error "presupuesto completamente pagado", `break`.
- Si `$importe > saldo + 0,01€` → error "importe supera saldo pendiente", `break`.

### Tabla de comportamiento

| Escenario | Antes | Ahora |
|---|---|---|
| saldo=0, tipo Anticipo | ✅ Bloqueado | ✅ Bloqueado |
| saldo=0, tipo Total | ❌ Se guardaba | ✅ Bloqueado |
| saldo=0, tipo Resto | ❌ Se guardaba | ✅ Bloqueado |
| saldo=300€, tipo Total, importe 200€ | ✅ Se guardaba | ✅ Se guarda |
| saldo=300€, tipo Total, importe 400€ | ❌ Se guardaba | ✅ Bloqueado |
| saldo=300€, tipo Resto, importe 400€ | ❌ Se guardaba | ✅ Bloqueado |
