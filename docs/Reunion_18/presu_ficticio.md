# Presupuestos Ficticios — Datos de BD (TEST_FA2_)

> **Generados el 22/03/2026** para testing de la funcionalidad de Factura Agrupada.
> Todos los presupuestos tienen `observaciones_internas_presupuesto LIKE 'TEST_FA2_%'`.
> IDs de presupuesto: **89 → 102** (alias PA → PN).

---

## 1. Tabla resumen de presupuestos (`presupuesto`)

| Alias | id | Número | Evento | Inicio | Fin | Base | IVA | **Total** | Estado |
|-------|----|--------|--------|--------|-----|-----:|----:|----------:|--------|
| PA | 89 | P-00080/2026 | Gala Anual de Primavera | 10/04/2026 | 11/04/2026 | 320,00 € | 67,20 € | **387,20 €** | aprobado |
| PB | 90 | P-00081/2026 | Congreso Internacional de Tecnología | 15/04/2026 | 17/04/2026 | 261,00 € | 26,10 € | **287,10 €** | aprobado ⚠️ IVA 10% |
| PC | 91 | P-00082/2026 | Evento Corporativo MDR | 02/05/2026 | 03/05/2026 | 1.170,00 € | 245,70 € | **1.415,70 €** | aprobado |
| PD | 92 | P-00083/2026 | Presentación de Nuevos Productos | 08/05/2026 | 09/05/2026 | 278,00 € | 58,38 € | **336,38 €** | aprobado |
| PE | 93 | P-00084/2026 | Celebración de Aniversario | 20/05/2026 | 21/05/2026 | 720,00 € | 151,20 € | **871,20 €** | aprobado |
| PF | 94 | P-00085/2026 | Jornada de Formación Interna | 25/05/2026 | 26/05/2026 | 278,00 € | 58,38 € | **336,38 €** | aprobado |
| PG | 95 | P-00086/2026 | Acto de Protocolo Institucional | 05/06/2026 | 06/06/2026 | 188,00 € | 39,48 € | **227,48 €** | aprobado |
| PH | 96 | P-00087/2026 | Cena de Gala Verano | 05/06/2026 | 06/06/2026 | 175,00 € | 36,75 € | **211,75 €** | aprobado |
| PI | 97 | P-00088/2026 | Acto de Clausura del Congreso | 15/06/2026 | 16/06/2026 | 240,00 € | 50,40 € | **290,40 €** | aprobado |
| PJ | 98 | P-00089/2026 | Seminario de Innovación | 20/06/2026 | 21/06/2026 | 98,00 € | 20,58 € | **118,58 €** | aprobado |
| PK | 99 | P-00090/2026 | Exposición de Productos Premium | 01/07/2026 | 02/07/2026 | 98,00 € | 20,58 € | **118,58 €** | aprobado |
| PL | 100 | P-00091/2026 | Workshop de Transformación Digital | 08/07/2026 | 09/07/2026 | 98,00 € | 20,58 € | **118,58 €** | aprobado |
| PM | 101 | P-00092/2026 | Reunión Anual de Accionistas | 15/07/2026 | 16/07/2026 | 98,00 € | 20,58 € | **118,58 €** | aprobado |
| PN | 102 | P-00093/2026 | Convención de Ventas Melia | 05/08/2026 | 07/08/2026 | 200,00 € | 42,00 € | **242,00 €** | aprobado |
| | | | | | **TOTAL** | **4.124,00 €** | **857,95 €** | **5.081,95 €** | |

> Todos los eventos: ubicación **Sidi San Juan, Benidorm (Alicante)**. IVA 21% salvo PB que lleva 10%.

---

## 2. Versiones (`presupuesto_version`)

| id_version | Alias | id_presupuesto | Versión | Estado | Creada | Aprobada |
|-----------|-------|---------------|---------|--------|--------|----------|
| 94 | PA | 89 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 95 | PB | 90 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 96 | PC | 91 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 97 | PD | 92 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 98 | PE | 93 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 99 | PF | 94 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 100 | PG | 95 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 101 | PH | 96 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 102 | PI | 97 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 103 | PJ | 98 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 104 | PK | 99 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 105 | PL | 100 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 106 | PM | 101 | 1 | aprobado | 22/03/2026 | 22/03/2026 |
| 107 | PN | 102 | 1 | aprobado | 22/03/2026 | 22/03/2026 |

---

## 3. Líneas de presupuesto (`linea_presupuesto`)

IDs de líneas: 461 → 497 (37 líneas en total, de 2 a 3 por presupuesto).

| Alias | id_ppto | id_version | Artículo | Precio unit. | Cantidad | Base línea | % IVA |
|-------|---------|-----------|----------|-------------|---------|-----------|-------|
| PA | 89 | 94 | Micrófono | 25,00 € | 2 | 50,00 € | 21% |
| PA | 89 | 94 | Foco LED | 30,00 € | 3 | 90,00 € | 21% |
| PA | 89 | 94 | Robotizado Beam | 90,00 € | 2 | 180,00 € | 21% |
| **PA TOTAL** | | | | | | **320,00 €** | → 387,20 € con IVA |
| PB | 90 | 95 | Micrófono | 25,00 € | 2 | 50,00 € | **10%** |
| PB | 90 | 95 | Foco PC90 | 19,00 € | 3 | 57,00 € | **10%** |
| PB | 90 | 95 | Robotizado Wash | 77,00 € | 2 | 154,00 € | **10%** |
| **PB TOTAL** | | | | | | **261,00 €** | → 287,10 € con IVA (10%) |
| PC | 91 | 96 | Pantalla P3 | 450,00 € | 2 | 900,00 € | 21% |
| PC | 91 | 96 | Consola X32 | 180,00 € | 1 | 180,00 € | 21% |
| PC | 91 | 96 | Foco LED | 30,00 € | 3 | 90,00 € | 21% |
| **PC TOTAL** | | | | | | **1.170,00 €** | → 1.415,70 € con IVA |
| PD | 92 | 97 | Consola X32 | 180,00 € | 1 | 180,00 € | 21% |
| PD | 92 | 97 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PD | 92 | 97 | Foco PC90 | 19,00 € | 2 | 38,00 € | 21% |
| **PD TOTAL** | | | | | | **278,00 €** | → 336,38 € con IVA |
| PE | 93 | 98 | Consola X32 | 180,00 € | 1 | 180,00 € | 21% |
| PE | 93 | 98 | Pantalla P3 | 450,00 € | 1 | 450,00 € | 21% |
| PE | 93 | 98 | Foco LED | 30,00 € | 3 | 90,00 € | 21% |
| **PE TOTAL** | | | | | | **720,00 €** | → 871,20 € con IVA |
| PF | 94 | 99 | Consola X32 | 180,00 € | 1 | 180,00 € | 21% |
| PF | 94 | 99 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PF | 94 | 99 | Foco PC90 | 19,00 € | 2 | 38,00 € | 21% |
| **PF TOTAL** | | | | | | **278,00 €** | → 336,38 € con IVA |
| PG | 95 | 100 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PG | 95 | 100 | Foco PC90 | 19,00 € | 2 | 38,00 € | 21% |
| PG | 95 | 100 | Robotizado Beam | 90,00 € | 1 | 90,00 € | 21% |
| **PG TOTAL** | | | | | | **188,00 €** | → 227,48 € con IVA |
| PH | 96 | 101 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PH | 96 | 101 | Foco PC90 | 19,00 € | 2 | 38,00 € | 21% |
| PH | 96 | 101 | Robotizado Wash | 77,00 € | 1 | 77,00 € | 21% |
| **PH TOTAL** | | | | | | **175,00 €** | → 211,75 € con IVA |
| PI | 97 | 102 | Consola X32 | 180,00 € | 1 | 180,00 € | 21% |
| PI | 97 | 102 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| **PI TOTAL** | | | | | | **240,00 €** | → 290,40 € con IVA |
| PJ | 98 | 103 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PJ | 98 | 103 | Foco PC90 | 19,00 € | 2 | 38,00 € | 21% |
| **PJ TOTAL** | | | | | | **98,00 €** | → 118,58 € con IVA |
| PK | 99 | 104 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PK | 99 | 104 | Foco PC90 | 19,00 € | 2 | 38,00 € | 21% |
| **PK TOTAL** | | | | | | **98,00 €** | → 118,58 € con IVA |
| PL | 100 | 105 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PL | 100 | 105 | Foco PC90 | 19,00 € | 2 | 38,00 € | 21% |
| **PL TOTAL** | | | | | | **98,00 €** | → 118,58 € con IVA |
| PM | 101 | 106 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PM | 101 | 106 | Foco PC90 | 19,00 € | 2 | 38,00 € | 21% |
| **PM TOTAL** | | | | | | **98,00 €** | → 118,58 € con IVA |
| PN | 102 | 107 | Micrófono | 25,00 € | 2 | 50,00 € | 21% |
| PN | 102 | 107 | Foco LED | 30,00 € | 2 | 60,00 € | 21% |
| PN | 102 | 107 | Robotizado Beam | 90,00 € | 1 | 90,00 € | 21% |
| **PN TOTAL** | | | | | | **200,00 €** | → 242,00 € con IVA |

---

## 4. Documentos (`documento_presupuesto`)

Solo tienen documento los presupuestos **PC, PE, PI y PK**. Los demás (PA, PB, PD, PF, PG, PH, PJ, PL, PM, PN) están limpios sin documentos.

| id_doc | Alias | id_ppto | Tipo documento | ¿Real o proforma? | Número | Fecha emisión | Base | IVA | Total |
|--------|-------|---------|---------------|-------------------|--------|--------------|-----:|----:|------:|
| 125 | PC | 91 | `factura_anticipo` | ✅ **REAL** | TEST-FA2-ANT-PC | 22/03/2026 | 826,45 € | 173,55 € | **1.000,00 €** |
| 126 | PE | 93 | `factura_proforma` | ⚠️ **PROFORMA** | TEST-FA2-PROF-PE | 22/03/2026 | 413,22 € | 86,78 € | **500,00 €** |
| 127 | PI | 97 | `factura_final` | ✅ **REAL** | TEST-FA2-FF-PI | 22/03/2026 | 240,00 € | 50,40 € | **290,40 €** |
| 128 | PK | 99 | `factura_anticipo` | ✅ **REAL** | TEST-FA2-ANT-PK | 22/03/2026 | 413,22 € | 86,78 € | **500,00 €** |

> **Nota importante sobre PE:** El documento de PE es una **factura proforma**, no una factura real. Esto significa que aunque parece que hay un anticipo comprometido, **no genera obligación fiscal ni bloquea la selección en FA** de la misma manera que una factura real. El pago asociado sí existe (ver sección de pagos).

---

## 5. Pagos (`pago_presupuesto`)

### 5.1. Pagos existentes

| id_pago | Alias | id_ppto | Tipo | Importe | Estado | id_doc | id_fa | Observaciones |
|---------|-------|---------|------|--------:|--------|--------|-------|---------------|
| 123 | PC | 91 | anticipo | 1.000,00 € | recibido | 125 | — | TEST_FA2_pago_anticipo_PC |
| 124 | PI | 97 | total | 290,40 € | recibido | 127 | — | TEST_FA2_pago_total_PI |
| 125 | PK | 99 | anticipo | 500,00 € | recibido | 128 | — | TEST_FA2_pago_anticipo_PK |
| **140** | **PA** | **89** | **total** | **387,20 €** | **pendiente** | **—** | **40** | ⚠️ **HUÉRFANO** |
| **141** | **PB** | **90** | **total** | **287,10 €** | **pendiente** | **—** | **40** | ⚠️ **HUÉRFANO** |

### 5.2. ⚠️ PROBLEMA DETECTADO — Pagos huérfanos en PA y PB

Los pagos **140 y 141** apuntan a `id_factura_agrupada = 40`, pero esa FA **ya no existe** en la tabla `factura_agrupada` (fue borrada durante las pruebas). Esto genera una **inconsistencia de integridad referencial** y explica el problema sospechado con los anticipos.

**Consecuencias:**
- PA (P-00080/2026) aparece como si tuviese un pago total de 387,20 € pendiente vinculado a una FA inexistente.
- PB (P-00081/2026) ídem con 287,10 €.
- Al intentar incluir PA o PB en una nueva FA, el sistema puede detectar que ya tienen pagos asignados y **rechazar la selección** o calcular mal el importe restante.
- Ambos presupuestos deberían estar **limpios sin ningún pago** para los TC-01 y TC-04.

**Solución — borrar los pagos huérfanos:**
```sql
-- Verificar antes de borrar
SELECT id_pago_ppto, id_presupuesto, tipo_pago_ppto, importe_pago_ppto, id_factura_agrupada
FROM pago_presupuesto
WHERE id_pago_ppto IN (140, 141);

-- Borrar pagos huérfanos
DELETE FROM pago_presupuesto WHERE id_pago_ppto IN (140, 141);
```

### 5.3. Resumen de estado de pagos por presupuesto

| Alias | id_ppto | Tiene documento | Tiene pago real | Situación para testing FA |
|-------|---------|----------------|----------------|--------------------------|
| PA | 89 | No | ⚠️ Sí (huérfano) | ❌ Limpiar antes de TC-01 |
| PB | 90 | No | ⚠️ Sí (huérfano) | ❌ Limpiar antes de TC-04 |
| PC | 91 | Sí — anticipo real (1.000 €) | Sí — anticipo recibido | ✅ Listo (tiene anticipo real) |
| PD | 92 | No | No | ✅ Limpio |
| PE | 93 | Sí — **proforma** 500 € | No pago asociado al proforma | ⚠️ Proforma no genera pago |
| PF | 94 | No | No | ✅ Limpio |
| PG | 95 | No | No | ✅ Limpio |
| PH | 96 | No | No | ✅ Limpio |
| PI | 97 | Sí — factura final (290,40 €) | Sí — total recibido | ✅ Totalmente pagado |
| PJ | 98 | No | No | ✅ Limpio |
| PK | 99 | Sí — anticipo real (500 €) | Sí — anticipo recibido | ✅ Listo (tiene anticipo real) |
| PL | 100 | No | No | ✅ Limpio |
| PM | 101 | No | No | ✅ Limpio |
| PN | 102 | No | No | ✅ Limpio |

---

## 6. Relación anticipos: ¿real o proforma?

Esta tabla responde específicamente a la pregunta sobre el tipo de anticipo por presupuesto:

| Alias | ¿Tiene anticipo? | Tipo | Número documento | Importe | ¿Genera pago real? |
|-------|-----------------|------|-----------------|--------:|-------------------|
| PA | No | — | — | — | — |
| PB | No | — | — | — | — |
| PC | ✅ Sí | **Factura anticipo REAL** | TEST-FA2-ANT-PC | 1.000,00 € | ✅ Sí (pago 123, recibido) |
| PD | No | — | — | — | — |
| PE | ⚠️ Sí (proforma) | **Factura proforma** | TEST-FA2-PROF-PE | 500,00 € | ❌ No (sin pago registrado) |
| PF | No | — | — | — | — |
| PG | No | — | — | — | — |
| PH | No | — | — | — | — |
| PI | ✅ Sí (final) | **Factura final REAL** | TEST-FA2-FF-PI | 290,40 € | ✅ Sí (pago 124, recibido) |
| PJ | No | — | — | — | — |
| PK | ✅ Sí | **Factura anticipo REAL** | TEST-FA2-ANT-PK | 500,00 € | ✅ Sí (pago 125, recibido) |
| PL | No | — | — | — | — |
| PM | No | — | — | — | — |
| PN | No | — | — | — | — |

> **Conclusión:** PE tiene una factura proforma de 500 €, pero **no tiene pago asociado**. La factura proforma no genera obligación de cobro ni registra el pago en `pago_presupuesto`. Esto puede ser relevante para comprobar cómo se comporta la FA al calcular anticipos descontados cuando el único documento es una proforma.

---

*Documento generado el 22/03/2026. Datos extraídos directamente de `toldos_db` vía MCP MySQL.*
