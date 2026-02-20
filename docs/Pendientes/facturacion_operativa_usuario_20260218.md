# Sistema de Facturación de Presupuestos — Operativa

**Fecha**: febrero 2026  
**Destinatario**: Responsable de administración

---

## Introducción

A partir de ahora, desde cualquier presupuesto aprobado podrás generar y registrar todos los documentos de facturación directamente en la aplicación: facturas proforma, facturas de anticipo, factura final y abonos. Todos quedan guardados y vinculados al presupuesto, y en todo momento podrás consultar cuánto se ha cobrado y cuánto queda pendiente.

---

## 📋 Cómo operar — Paso a paso

### 1️⃣ Emitir una Factura Proforma

La factura proforma se usa cuando el cliente necesita un documento formal para realizar el pago completo antes del evento, sin que sea aún una factura fiscal.

**Pasos:**

1. Abre el presupuesto aprobado.
2. Ve a la pestaña **Documentos**.
3. Pulsa **Nueva Factura Proforma**.
4. Selecciona la **empresa real** con la que se emitirá *(ver restricciones al final del documento)*.
5. Confirma y genera el PDF.

> ⚠️ Solo se puede emitir **una factura proforma por presupuesto**.  
> Una vez emitida, la empresa queda fijada para todos los documentos posteriores de ese presupuesto.

---

### 2️⃣ Registrar un pago con Factura de Anticipo

Cuando el cliente realiza un pago parcial (señal, reserva, anticipo), se genera una factura fiscal por el importe concreto recibido.

**Pasos:**

1. Abre el presupuesto.
2. Ve a la pestaña **Pagos**.
3. Pulsa **Registrar Pago**.
4. Indica:
   - **Importe** recibido
   - **Fecha** del pago
   - **Método de pago** (transferencia, tarjeta, efectivo…)
   - **Referencia** opcional (nº transferencia, etc.)
5. La aplicación genera automáticamente la **Factura de Anticipo** con su número de serie y la vincula al pago.
6. Descarga o imprime el PDF desde el registro.

> 💡 Puedes registrar **varios anticipos** sobre el mismo presupuesto. Cada uno generará su propia factura de anticipo numerada.

---

### 3️⃣ Emitir la Factura Final

Cuando se va a cerrar el servicio y facturar el total o el resto pendiente.

**Pasos:**

1. Abre el presupuesto.
2. Ve a la pestaña **Documentos**.
3. Pulsa **Nueva Factura Final**.
4. Revisa los importes: la aplicación muestra el total del presupuesto, lo ya facturado en anticipos y el **saldo pendiente**.
5. Confirma y genera el PDF.

> ✅ La factura final consolida toda la operación. Tras emitirla, el presupuesto queda completamente facturado.

---

### 4️⃣ Abonar (anular) una factura

Si necesitas anular una factura ya emitida (error en datos, cambio de condiciones, devolución…), se genera una **Factura de Abono** que la cancela fiscalmente.

**Pasos:**

1. Abre el presupuesto.
2. Ve a la pestaña **Documentos** y localiza la factura a anular.
3. Pulsa el botón **Abonar** (aparece junto a cada factura de anticipo o factura final).
4. Escribe el **motivo del abono** (campo obligatorio, mínimo 10 caracteres).
5. Confirma la operación en el aviso de seguridad que aparecerá.
6. La aplicación genera la **Factura de Abono** con importes en negativo y la vincula automáticamente a la factura original.
7. Si había un pago registrado asociado a esa factura, queda anulado automáticamente.

> ⚠️ **Las facturas proforma NO se pueden abonar.** El abono solo aplica a facturas de anticipo y facturas finales.

---

## 📊 Panel de seguimiento de pagos

En la pestaña **Pagos** de cada presupuesto encontrarás un resumen siempre actualizado:

| Concepto | Descripción |
|---|---|
| **Total presupuesto** | Importe total acordado con el cliente |
| **Total facturado/cobrado** | Suma de todos los anticipos recibidos |
| **Pendiente de cobro** | Diferencia entre total y lo ya cobrado |

Cada pago registrado muestra: fecha, importe, método, referencia y la factura asociada. Desde la misma tabla puedes descargar el PDF de cualquier documento emitido.

---

## 🚫 Restricciones importantes

Estas limitaciones están en la aplicación para garantizar la coherencia fiscal de los documentos:

- **No se puede abonar una Factura Proforma.**  
  La proforma no es una factura fiscal; si no se va a usar, simplemente no se registra pago contra ella.

- **Una vez emitida cualquier factura, no se puede cambiar de empresa.**  
  La primera factura (proforma, anticipo o final) que se emite sobre un presupuesto fija la empresa emisora para todos los documentos siguientes de ese presupuesto.

- **Solo se puede emitir un abono por factura.**  
  Si necesitas corregir reiteradamente, contacta con administración para gestionar el proceso manualmente.

- **Las facturas siempre requieren empresa real.**  
  Los presupuestos pueden ir con la empresa del grupo, pero cualquier documento fiscal debe emitirse siempre con una empresa con datos fiscales reales.

- **El motivo del abono es obligatorio.**  
  Es un requisito legal para la validez del documento rectificativo.

---

*MDR ERP Manager — febrero 2026*
