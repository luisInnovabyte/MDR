# Spec: Factura Agrupada de Múltiples Presupuestos

> **Fecha:** 2026-03-18
> **Estado:** Implementado por Copilot — pendiente de testing

---

## Descripción del requisito

Agrupar varios presupuestos en una única factura final, contemplando que sobre esos
presupuestos pueden haberse emitido previamente:

- Facturas **proforma** de anticipo (`factura_proforma`)
- Facturas **reales** de anticipo (`factura_anticipo`)
- Combinaciones de ambas

---

## Contexto del sistema

- `documento_presupuesto` → vincula 1 presupuesto → N documentos (proforma, anticipo, final, abono)
- `pago_presupuesto` → pagos vinculados a 1 presupuesto con empresa bloqueada al primer anticipo real
- Series de numeración gestionadas por SP: serie `factura` (F), serie `factura_proforma` (FP), serie `abono` (A)

---

## Tablas afectadas

### Nuevas

```sql
-- Cabecera
CREATE TABLE factura_agrupada (
    id_factura_agrupada         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_factura_agrupada     VARCHAR(50) NOT NULL UNIQUE,
    id_empresa                  INT UNSIGNED NOT NULL,
    id_cliente                  INT UNSIGNED NOT NULL,
    fecha_factura_agrupada      DATE NOT NULL,
    observaciones_agrupada      TEXT,
    total_base_agrupada         DECIMAL(10,2) DEFAULT 0,
    total_iva_agrupada          DECIMAL(10,2) DEFAULT 0,
    total_bruto_agrupada        DECIMAL(10,2) DEFAULT 0,
    total_anticipos_agrupada    DECIMAL(10,2) DEFAULT 0,
    total_a_cobrar_agrupada     DECIMAL(10,2) DEFAULT 0,
    pdf_path_agrupada           VARCHAR(500),
    activo_factura_agrupada     BOOLEAN DEFAULT TRUE,
    created_at_factura_agrupada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_factura_agrupada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_fag_empresa FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa),
    CONSTRAINT fk_fag_cliente FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

-- Líneas
CREATE TABLE factura_agrupada_presupuesto (
    id_fap                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_factura_agrupada         INT UNSIGNED NOT NULL,
    id_presupuesto              INT UNSIGNED NOT NULL,
    total_base_fap              DECIMAL(10,2) DEFAULT 0,
    total_iva_fap               DECIMAL(10,2) DEFAULT 0,
    total_bruto_fap             DECIMAL(10,2) DEFAULT 0,
    total_anticipos_reales_fap  DECIMAL(10,2) DEFAULT 0,
    resto_fap                   DECIMAL(10,2) DEFAULT 0,
    orden_fap                   TINYINT UNSIGNED DEFAULT 0,
    activo_fap                  BOOLEAN DEFAULT TRUE,
    created_at_fap              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_fap_agrupada    FOREIGN KEY (id_factura_agrupada) REFERENCES factura_agrupada(id_factura_agrupada),
    CONSTRAINT fk_fap_presupuesto FOREIGN KEY (id_presupuesto) REFERENCES presupuesto(id_presupuesto),
    UNIQUE KEY uq_fap_ppto (id_factura_agrupada, id_presupuesto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

### Modificada

```sql
-- Campo nuevo en pago_presupuesto
ALTER TABLE pago_presupuesto
    ADD COLUMN id_factura_agrupada INT UNSIGNED NULL DEFAULT NULL
    AFTER id_documento_ppto,
    ADD CONSTRAINT fk_pp_factura_agrupada
        FOREIGN KEY (id_factura_agrupada)
        REFERENCES factura_agrupada(id_factura_agrupada)
        ON DELETE SET NULL ON UPDATE CASCADE;
```

---

## Ficheros implementados

| Fichero | Propósito |
|---|---|
| `models/FacturaAgrupada.php` | Modelo CRUD + validaciones |
| `controller/factura_agrupada.php` | Ops: listar, guardaryeditar, generar, descargar, desactivar |
| `controller/impresion_factura_agrupada.php` | Genera PDF con TCPDF |
| `view/FacturasAgrupadas/index.php` | Vista listado + wizard selección presupuestos |
| `view/FacturasAgrupadas/factura_agrupada.js` | JS / AJAX |

---

## Flujo de generación (transacción completa)

```
a) sp_obtener_siguiente_numero('factura', id_empresa) → número F-YYYY/NNN
b) INSERT factura_agrupada
c) INSERT factura_agrupada_presupuesto (una fila por presupuesto)
d) Para cada presupuesto:
   - Anular proformas de anticipo activas (activo_documento_ppto = 0)
   - INSERT pago_presupuesto con id_factura_agrupada
e) sp_actualizar_contador_empresa(...)
f) Generar PDF → guardar ruta en pdf_path_agrupada
→ DEVOLVER JSON { success, numero_factura, url_pdf }
```

**Ante cualquier fallo en pasos a-f → rollback completo.**

---

## Casuística de anticipos

| Tipo documento previo | Acción |
|---|---|
| Sin anticipos | Factura agrupada por total completo |
| Proforma anticipo (`factura_proforma`) | Se anula (activo=0). **No resta del total** |
| Anticipo real (`factura_anticipo`) | Se mantiene activa. **Su importe SÍ se deduce** |
| Mezcla de ambos | Anulan proformas + deducen solo anticipos reales |

---

## Fórmulas de cálculo (verificar exactitud)

```
Por cada presupuesto incluido (fila en factura_agrupada_presupuesto):
  total_base_fap             = total_base de v_presupuesto_totales
  total_iva_fap              = total_iva de v_presupuesto_totales (conserva tipo IVA original)
  total_bruto_fap            = total_con_iva de v_presupuesto_totales
  total_anticipos_reales_fap = SUM(importe) de factura_anticipo WHERE id_presupuesto = X AND activo = 1
  resto_fap                  = total_bruto_fap - total_anticipos_reales_fap
  resto_fap                  >= 0 siempre (nunca negativo; si anticipos >= total → resto = 0)

En cabecera factura_agrupada:
  total_base_agrupada        = SUM(total_base_fap)
  total_iva_agrupada         = SUM(total_iva_fap)
  total_bruto_agrupada       = SUM(total_bruto_fap)
  total_anticipos_agrupada   = SUM(total_anticipos_reales_fap)
  total_a_cobrar_agrupada    = total_bruto_agrupada - total_anticipos_agrupada
```

---

## Validaciones de negocio (⛔ = bloqueo, ⚠️ = advertencia)

```
⛔ Todos los presupuestos deben tener el mismo id_cliente
⛔ Todos deben tener la misma id_empresa emisora (o todos sin empresa bloqueada)
⛔ Ningún presupuesto puede estar ya incluido en otra factura_agrupada activa
⛔ Ningún presupuesto puede tener una factura_final activa (ya liquidado)
⛔ Ningún presupuesto puede estar en estado CANCELADO
⛔ Mínimo 2 presupuestos — si es 1, usar flujo normal de factura_final
⚠️  Anticipos reales de distintas empresas → conflicto de empresa emisora
⚠️  Mezcla de idiomas (es/en) entre presupuestos → aviso al usuario
```

---

## Plan de testing

### FASE 1 — Verificación de estructura BD

```sql
-- Verificar que existen las tablas nuevas
SHOW TABLES LIKE 'factura_agrupada';
SHOW TABLES LIKE 'factura_agrupada_presupuesto';

-- Verificar columnas y tipos
DESCRIBE factura_agrupada;
DESCRIBE factura_agrupada_presupuesto;

-- Verificar columna nueva en pago_presupuesto
SHOW COLUMNS FROM pago_presupuesto LIKE 'id_factura_agrupada';

-- Verificar FKs
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME, REFERENCED_TABLE_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'toldos_db'
AND TABLE_NAME IN ('factura_agrupada', 'factura_agrupada_presupuesto')
AND REFERENCED_TABLE_NAME IS NOT NULL;
```

---

### FASE 2 — Datos de prueba

Antes de ejecutar los tests, insertar datos controlados. Usar IDs reales obtenidos
de la BD via MCP. El proceso es:

1. Obtener un `id_cliente` real con al menos 3 presupuestos sin factura_final
2. Obtener un `id_cliente` diferente con al menos 1 presupuesto (para test de cliente distinto)
3. Identificar un presupuesto con `factura_anticipo` activa (para test de deducción)
4. Identificar un presupuesto con `factura_proforma` activa (para test de anulación)
5. Crear manualmente una `factura_agrupada` de prueba e incluir un presupuesto en ella
   (para test de presupuesto ya agrupado)

```sql
-- Consultas de apoyo para obtener datos reales
SELECT id_presupuesto, id_cliente, id_empresa, estado_presupuesto
FROM presupuesto
WHERE activo_presupuesto = 1
LIMIT 20;

SELECT id_cliente, nombre_cliente FROM cliente LIMIT 10;

-- Presupuestos con anticipo real activo
SELECT dp.id_presupuesto, dp.tipo_documento_ppto, dp.activo_documento_ppto
FROM documento_presupuesto dp
WHERE dp.tipo_documento_ppto = 'factura_anticipo'
AND dp.activo_documento_ppto = 1
LIMIT 5;
```

**Prefijo para datos de prueba insertados:** `TEST_` en observaciones_agrupada
**Limpieza al finalizar:**
```sql
DELETE fap FROM factura_agrupada_presupuesto fap
JOIN factura_agrupada fa ON fap.id_factura_agrupada = fa.id_factura_agrupada
WHERE fa.observaciones_agrupada LIKE 'TEST_%';

DELETE FROM factura_agrupada WHERE observaciones_agrupada LIKE 'TEST_%';
```

---

### FASE 3 — Casos de prueba

#### TC-01 Happy path: 2 presupuestos sin anticipos
- **Acción:** Generar factura agrupada con P1 y P2 del mismo cliente, sin anticipos
- **Verificar:**
  - `total_a_cobrar_agrupada = total_bruto_agrupada`
  - `total_anticipos_agrupada = 0`
  - `resto_fap = total_bruto_fap` en ambas líneas
  - Se generó registro en `pago_presupuesto` con `id_factura_agrupada` informado
  - `numero_factura_agrupada` tiene formato serie F correcto

#### TC-02 Happy path: presupuesto con anticipo real
- **Acción:** Generar con P1 (sin anticipo) y P3 (con anticipo real de X€)
- **Verificar:**
  - `total_anticipos_reales_fap` de P3 = importe de su `factura_anticipo`
  - `resto_fap` de P3 = `total_bruto_fap` - anticipo
  - `total_a_cobrar_agrupada = total_bruto_agrupada - anticipo`
  - La `factura_anticipo` de P3 sigue activa (`activo_documento_ppto = 1`)

#### TC-03 Happy path: presupuesto con proforma de anticipo
- **Acción:** Generar con P1 y P4 (con proforma activa)
- **Verificar:**
  - La proforma de P4 queda `activo_documento_ppto = 0` tras la generación
  - `total_anticipos_reales_fap` de P4 = 0 (la proforma NO resta)
  - `resto_fap` de P4 = `total_bruto_fap` completo

#### TC-04 Mezcla de IVA distinto entre presupuestos
- **Acción:** Generar con un presupuesto al 21% y otro al 10%
- **Verificar:**
  - `total_iva_agrupada = SUM de cada total_iva_fap` (no se recalcula, se suma)
  - Los tipos de IVA individuales se conservan en cada línea `factura_agrupada_presupuesto`

#### TC-05 Validación: presupuestos de clientes distintos
- **Acción:** Intentar generar con P1 (cliente A) y P7 (cliente B)
- **Resultado esperado:** `{ success: false, mensaje: '...' }` — operación rechazada
- **Verificar:** ningún INSERT en `factura_agrupada`

#### TC-06 Validación: presupuesto ya en otra agrupación activa
- **Acción:** Intentar incluir P5 (ya en `factura_agrupada` activa) junto a P1
- **Resultado esperado:** rechazo con mensaje específico
- **Verificar:** ningún INSERT

#### TC-07 Validación: presupuesto con factura_final activa
- **Acción:** Intentar incluir P6 (tiene `factura_final` activa)
- **Resultado esperado:** rechazo
- **Verificar:** ningún INSERT

#### TC-08 Validación: solo 1 presupuesto
- **Acción:** Intentar generar con un único presupuesto
- **Resultado esperado:** rechazo (mínimo 2)

#### TC-09 Caso límite: anticipo >= total del presupuesto
- **Acción:** Generar con un presupuesto donde `anticipo_real >= total_bruto`
- **Verificar:**
  - `resto_fap = 0` (nunca negativo)
  - `total_a_cobrar_agrupada` no es negativo

#### TC-10 Rollback ante fallo en transacción
- **Acción:** Simular fallo en paso d (provocar error en INSERT pago_presupuesto)
- **Verificar:**
  - No existe registro en `factura_agrupada`
  - No existen registros en `factura_agrupada_presupuesto`
  - Las proformas que deberían haberse anulado siguen activas
  - El contador de serie NO se incrementó

---

### FASE 4 — Verificación de integridad referencial

```sql
-- Verificar que no hay facturas_agrupadas sin líneas
SELECT fa.id_factura_agrupada
FROM factura_agrupada fa
LEFT JOIN factura_agrupada_presupuesto fap ON fa.id_factura_agrupada = fap.id_factura_agrupada
WHERE fap.id_fap IS NULL
AND fa.observaciones_agrupada LIKE 'TEST_%';

-- Verificar que los totales de cabecera cuadran con suma de líneas
SELECT
    fa.id_factura_agrupada,
    fa.total_bruto_agrupada AS cabecera_bruto,
    SUM(fap.total_bruto_fap) AS suma_lineas_bruto,
    fa.total_bruto_agrupada - SUM(fap.total_bruto_fap) AS diferencia
FROM factura_agrupada fa
JOIN factura_agrupada_presupuesto fap ON fa.id_factura_agrupada = fap.id_factura_agrupada
WHERE fa.observaciones_agrupada LIKE 'TEST_%'
GROUP BY fa.id_factura_agrupada
HAVING diferencia <> 0;
-- Si devuelve filas → los totales no cuadran → BUG
```

---

## Puntos a tener en cuenta para las pruebas
- [ ] Serie: Las series de las facturas agrupadas coinciden con las series de las facturas "normales"
- [ ] Solo se permite abonar/rectificar una factura agrupada completa
- [ ] Las facturas agrupadas tendrán todo el detalle de los presupuestos, a excepción de las observaciones
- [ ] El acceso se realizará desde pantalla independiente.
