# Spec: Factura Agrupada de Múltiples Presupuestos

> \*\*Fecha:\*\* 2026-03-18
> \*\*Estado:\*\* Implementado por Copilot — pendiente de testing

\---

## Descripción del requisito

Agrupar varios presupuestos en una única factura final, contemplando que sobre esos
presupuestos pueden haberse emitido previamente:

* Facturas **proforma** de anticipo (`factura\_proforma`)
* Facturas **reales** de anticipo (`factura\_anticipo`)
* Combinaciones de ambas

\---

## Contexto del sistema

* `documento\_presupuesto` → vincula 1 presupuesto → N documentos (proforma, anticipo, final, abono)
* `pago\_presupuesto` → pagos vinculados a 1 presupuesto con empresa bloqueada al primer anticipo real
* Series de numeración gestionadas por SP: serie `factura` (F), serie `factura\_proforma` (FP), serie `abono` (A)

\---

## Tablas afectadas

### Nuevas

```sql
-- Cabecera
CREATE TABLE factura\_agrupada (
    id\_factura\_agrupada         INT UNSIGNED AUTO\_INCREMENT PRIMARY KEY,
    numero\_factura\_agrupada     VARCHAR(50) NOT NULL UNIQUE,
    serie\_factura\_agrupada      VARCHAR(5) NULL,
    id\_empresa                  INT UNSIGNED NOT NULL,
    id\_cliente                  INT UNSIGNED NOT NULL,
    fecha\_factura\_agrupada      DATE NOT NULL,
    observaciones\_agrupada      TEXT,
    total\_base\_agrupada         DECIMAL(10,2) DEFAULT 0,
    total\_iva\_agrupada          DECIMAL(10,2) DEFAULT 0,
    total\_bruto\_agrupada        DECIMAL(10,2) DEFAULT 0,
    total\_anticipos\_agrupada    DECIMAL(10,2) DEFAULT 0,
    total\_a\_cobrar\_agrupada     DECIMAL(10,2) DEFAULT 0,
    is\_abono\_agrupada           BOOLEAN DEFAULT FALSE,
    id\_factura\_agrupada\_ref     INT UNSIGNED NULL DEFAULT NULL,
    motivo\_abono\_agrupada       TEXT,
    pdf\_path\_agrupada           VARCHAR(500),
    activo\_factura\_agrupada     BOOLEAN DEFAULT TRUE,
    created\_at\_factura\_agrupada TIMESTAMP DEFAULT CURRENT\_TIMESTAMP,
    updated\_at\_factura\_agrupada TIMESTAMP DEFAULT CURRENT\_TIMESTAMP ON UPDATE CURRENT\_TIMESTAMP,
    CONSTRAINT fk\_fag\_empresa FOREIGN KEY (id\_empresa) REFERENCES empresa(id\_empresa),
    CONSTRAINT fk\_fag\_cliente FOREIGN KEY (id\_cliente) REFERENCES cliente(id\_cliente),
    CONSTRAINT fk\_fag\_ref FOREIGN KEY (id\_factura\_agrupada\_ref) REFERENCES factura\_agrupada(id\_factura\_agrupada)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4\_spanish\_ci;

-- Líneas
CREATE TABLE factura\_agrupada\_presupuesto (
    id\_fap                      INT UNSIGNED AUTO\_INCREMENT PRIMARY KEY,
    id\_factura\_agrupada         INT UNSIGNED NOT NULL,
    id\_presupuesto              INT UNSIGNED NOT NULL,
    total\_base\_fap              DECIMAL(10,2) DEFAULT 0,
    total\_iva\_fap               DECIMAL(10,2) DEFAULT 0,
    total\_bruto\_fap             DECIMAL(10,2) DEFAULT 0,
    total\_anticipos\_reales\_fap  DECIMAL(10,2) DEFAULT 0,
    resto\_fap                   DECIMAL(10,2) DEFAULT 0,
    orden\_fap                   TINYINT UNSIGNED DEFAULT 0,
    activo\_fap                  BOOLEAN DEFAULT TRUE,
    created\_at\_fap              TIMESTAMP DEFAULT CURRENT\_TIMESTAMP,
    CONSTRAINT fk\_fap\_agrupada    FOREIGN KEY (id\_factura\_agrupada) REFERENCES factura\_agrupada(id\_factura\_agrupada),
    CONSTRAINT fk\_fap\_presupuesto FOREIGN KEY (id\_presupuesto) REFERENCES presupuesto(id\_presupuesto),
    UNIQUE KEY uq\_fap\_ppto (id\_factura\_agrupada, id\_presupuesto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4\_spanish\_ci;
```

### Modificada

```sql
-- Campo nuevo en pago\_presupuesto
ALTER TABLE pago\_presupuesto
    ADD COLUMN id\_factura\_agrupada INT UNSIGNED NULL DEFAULT NULL
    AFTER id\_documento\_ppto,
    ADD CONSTRAINT fk\_pp\_factura\_agrupada
        FOREIGN KEY (id\_factura\_agrupada)
        REFERENCES factura\_agrupada(id\_factura\_agrupada)
        ON DELETE SET NULL ON UPDATE CASCADE;
```

\---

## Ficheros implementados

|Fichero|Propósito|
|-|-|
|`models/FacturaAgrupada.php`|Modelo CRUD + validaciones|
|`controller/factura\_agrupada.php`|Ops: listar, guardaryeditar, generar, descargar, desactivar|
|`controller/impresion\_factura\_agrupada.php`|Genera PDF con TCPDF|
|`view/FacturasAgrupadas/index.php`|Vista listado + wizard **4 pasos**: Cliente → Empresa → Presupuestos → Confirmar|
|`public/js/factura_agrupada.js`|JS / AJAX del wizard y listado|

### Nuevos endpoints añadidos (2026-03-21)

| Operación | Método | Parámetros POST | Respuesta |
|-----------|--------|-----------------|-----------| 
| `listar_empresas_facturacion` | POST | `id_cliente` | `{ success, empresa_bloqueada: {id_empresa, nombre_empresa, nif_empresa, codigo_empresa}\|null, empresas_reales: [...] }` |

- **`empresa_bloqueada`**: se rellena si el cliente ya tiene un `documento_presupuesto` activo de tipo `factura_proforma`/`factura_anticipo`/`factura_final` emitido desde una empresa real (`ficticia_empresa = 0`). En ese caso el selector del paso 2 queda deshabilitado y se muestra una alerta azul.
- **`empresas_reales`**: lista completa de empresas con `ficticia_empresa = 0 AND activo_empresa = 1`, ordenadas por nombre.
- El endpoint `validar_seleccion` acepta ahora además `id_empresa_real` (INT) para comprobar conflictos.
- El endpoint `guardar` recibe `id_empresa_real` en lugar de `id_empresa`.

\---

## Flujo de generación (transacción completa)

```
a) sp\_obtener\_siguiente\_numero('factura', id\_empresa) → número F-YYYY/NNN
b) INSERT factura\_agrupada
c) INSERT factura\_agrupada\_presupuesto (una fila por presupuesto)
d) Para cada presupuesto:
   - Anular proformas de anticipo activas (activo\_documento\_ppto = 0)
   - INSERT pago\_presupuesto con id\_factura\_agrupada
e) sp\_actualizar\_contador\_empresa(...)
f) Generar PDF → guardar ruta en pdf\_path\_agrupada
→ DEVOLVER JSON { success, numero\_factura, url\_pdf }
```

**Ante cualquier fallo en pasos a-f → rollback completo.**

\---

## Casuística de anticipos

|Tipo documento previo|Acción|
|-|-|
|Sin anticipos|Factura agrupada por total completo|
|Proforma anticipo (`factura\_proforma`)|Se anula (activo=0). **No resta del total**|
|Anticipo real (`factura\_anticipo`)|Se mantiene activa. **Su importe SÍ se deduce**|
|Mezcla de ambos|Anulan proformas + deducen solo anticipos reales|

\---

## Fórmulas de cálculo (verificar exactitud)

```
Por cada presupuesto incluido (fila en factura\_agrupada\_presupuesto):
  total\_base\_fap             = total\_base de v\_presupuesto\_totales
  total\_iva\_fap              = total\_iva de v\_presupuesto\_totales (conserva tipo IVA original)
  total\_bruto\_fap            = total\_con\_iva de v\_presupuesto\_totales
  total\_anticipos\_reales\_fap = SUM(importe) de factura\_anticipo WHERE id\_presupuesto = X AND activo = 1
  resto\_fap                  = total\_bruto\_fap - total\_anticipos\_reales\_fap
  resto\_fap                  >= 0 siempre (nunca negativo; si anticipos >= total → resto = 0)

En cabecera factura\_agrupada:
  total\_base\_agrupada        = SUM(total\_base\_fap)
  total\_iva\_agrupada         = SUM(total\_iva\_fap)
  total\_bruto\_agrupada       = SUM(total\_bruto\_fap)
  total\_anticipos\_agrupada   = SUM(total\_anticipos\_reales\_fap)
  total\_a\_cobrar\_agrupada    = total\_bruto\_agrupada - total\_anticipos\_agrupada
```

\---

## Validaciones de negocio (⛔ = bloqueo, ⚠️ = advertencia)

```
⛔ Todos los presupuestos deben tener el mismo id\_cliente
⛔ Todos deben tener la misma id\_empresa emisora (o todos sin empresa bloqueada)
⛔ Ningún presupuesto puede estar ya incluido en otra factura\_agrupada activa
⛔ Ningún presupuesto puede tener una factura\_final activa (ya liquidado)
⛔ Ningún presupuesto puede estar en estado CANCELADO
⛔ Mínimo 2 presupuestos — si es 1, usar flujo normal de factura\_final
⚠️  Anticipos reales de distintas empresas → conflicto de empresa emisora
⛔ Uno o más presupuestos seleccionados tienen facturas previas (proforma/anticipo/final) emitidas desde una empresa real **distinta** a la seleccionada en el wizard → error bloqueante
⚠️  Mezcla de idiomas (es/en) entre presupuestos → aviso al usuario
```

\---

## Plan de testing

> Fichero de pruebas para **Claude CODE** — ejecutar queries directamente via MCP MySQL (`toldos_db`).
> Prefijo para datos de prueba insertados manualmente: `TEST_` en `observaciones_agrupada`.

---

### ⚠️ BUGS / DISCREPANCIAS detectados al revisar la implementación

Resolver **antes** de ejecutar las fases de testing o marcar como excepción conocida.

#### BUG-03 (RIESGO): Interpolación de string en llamadas a Stored Procedures

En `insert_factura_agrupada_transaccion` y `insert_abono_agrupada`:

```php
$this->conexion->exec("CALL sp_obtener_siguiente_numero('$codigo_empresa', 'factura', @numero_completo)");
```

`$codigo_empresa` proviene de la BD pero se interpola directamente. Aunque el vector de ataque
es remoto, debería usarse un prepared statement o validar explícitamente que el valor es
alfanumérico (`preg_match('/^[A-Z0-9_]+$/', $codigo_empresa)`).

---

### FASE 1 — Verificación de estructura BD

Ejecutar via MCP MySQL antes de cualquier test funcional.

```sql
-- 1.1 Existencia de tablas
SHOW TABLES LIKE 'factura_agrupada';
SHOW TABLES LIKE 'factura_agrupada_presupuesto';

-- 1.2 Estructura completa de factura_agrupada
DESCRIBE factura_agrupada;
-- Campos esperados: id_factura_agrupada, numero_factura_agrupada, serie_factura_agrupada,
--   id_empresa, id_cliente, fecha_factura_agrupada, observaciones_agrupada,
--   total_base_agrupada, total_iva_agrupada, total_bruto_agrupada,
--   total_anticipos_agrupada, total_a_cobrar_agrupada,
--   is_abono_agrupada, id_factura_agrupada_ref, motivo_abono_agrupada,
--   pdf_path_agrupada, activo_factura_agrupada,
--   created_at_factura_agrupada, updated_at_factura_agrupada

-- 1.3 Estructura de factura_agrupada_presupuesto
DESCRIBE factura_agrupada_presupuesto;
-- Campos esperados: id_fap, id_factura_agrupada, id_presupuesto,
--   total_base_fap, total_iva_fap, total_bruto_fap,
--   total_anticipos_reales_fap, resto_fap, orden_fap,
--   activo_fap, created_at_fap

-- 1.4 Columna nueva en pago_presupuesto
SHOW COLUMNS FROM pago_presupuesto LIKE 'id_factura_agrupada';
-- Debe devolver 1 fila con Type=INT UNSIGNED y Null=YES

-- 1.5 Foreign Keys de las dos tablas nuevas
SELECT CONSTRAINT_NAME, TABLE_NAME, COLUMN_NAME,
       REFERENCED_TABLE_NAME, REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_SCHEMA = 'toldos_db'
  AND TABLE_NAME IN ('factura_agrupada','factura_agrupada_presupuesto','pago_presupuesto')
  AND REFERENCED_TABLE_NAME IS NOT NULL
ORDER BY TABLE_NAME;

-- 1.6 Vista usada por el modelo
SHOW FULL TABLES WHERE Table_type = 'VIEW' AND Tables_in_toldos_db LIKE 'vista_factura_agrupada%';
-- Debe aparecer vista_factura_agrupada_completa

-- 1.7 Campos de la vista
DESCRIBE vista_factura_agrupada_completa;
-- Debe exponer al menos: id_factura_agrupada, numero_factura_agrupada, is_abono_agrupada,
--   n_presupuestos, nombre_empresa, nombre_cliente, total_bruto_agrupada, total_a_cobrar_agrupada
```

---

### FASE 2 — Reconocimiento de datos de prueba

Ejecutar antes de FASE 3 para obtener IDs reales con los que trabajar.
Anotar los valores obtenidos para sustituirlos en los TCs.

```sql
-- 2.1 Clientes con al menos 3 presupuestos activos sin factura_final
SELECT p.id_cliente, c.nombre_cliente, COUNT(*) AS n_pptos
FROM presupuesto p
INNER JOIN cliente c ON c.id_cliente = p.id_cliente
WHERE p.activo_presupuesto = 1
  AND NOT EXISTS (
      SELECT 1 FROM documento_presupuesto dp
      WHERE dp.id_presupuesto = p.id_presupuesto
        AND dp.tipo_documento_ppto = 'factura_final'
        AND dp.activo_documento_ppto = 1
  )
  AND NOT EXISTS (
      SELECT 1 FROM factura_agrupada_presupuesto fap
      INNER JOIN factura_agrupada fa ON fa.id_factura_agrupada = fap.id_factura_agrupada
      WHERE fap.id_presupuesto = p.id_presupuesto
        AND fap.activo_fap = 1 AND fa.activo_factura_agrupada = 1
  )
GROUP BY p.id_cliente
HAVING n_pptos >= 3
ORDER BY n_pptos DESC
LIMIT 5;

-- 2.2 Para el cliente_A elegido: listar sus presupuestos disponibles con totales
-- (sustituir :id_cliente_A por el ID obtenido en 2.1)
SELECT p.id_presupuesto, p.numero_presupuesto, p.id_empresa, e.nombre_empresa,
       vt.total_base_imponible, vt.total_iva, vt.total_con_iva,
       COALESCE(ant.total_anticipos, 0) AS anticipos_reales
FROM presupuesto p
INNER JOIN empresa e ON e.id_empresa = p.id_empresa
LEFT JOIN v_presupuesto_totales vt ON vt.id_presupuesto = p.id_presupuesto
LEFT JOIN (
    SELECT id_presupuesto, SUM(total_documento_ppto) AS total_anticipos
    FROM documento_presupuesto
    WHERE tipo_documento_ppto = 'factura_anticipo' AND activo_documento_ppto = 1
    GROUP BY id_presupuesto
) ant ON ant.id_presupuesto = p.id_presupuesto
WHERE p.id_cliente = :id_cliente_A AND p.activo_presupuesto = 1
ORDER BY p.fecha_presupuesto DESC;

-- 2.3 Presupuesto con factura_anticipo activa (para TC-02)
SELECT dp.id_presupuesto, p.numero_presupuesto, dp.total_documento_ppto AS importe_anticipo,
       p.id_cliente, p.id_empresa
FROM documento_presupuesto dp
INNER JOIN presupuesto p ON p.id_presupuesto = dp.id_presupuesto
WHERE dp.tipo_documento_ppto = 'factura_anticipo'
  AND dp.activo_documento_ppto = 1
  AND p.activo_presupuesto = 1
  AND NOT EXISTS (
      SELECT 1 FROM documento_presupuesto dp2
      WHERE dp2.id_presupuesto = dp.id_presupuesto
        AND dp2.tipo_documento_ppto = 'factura_final'
        AND dp2.activo_documento_ppto = 1
  )
LIMIT 5;

-- 2.4 Presupuesto con factura_proforma activa (para TC-03 / BUG-01)
SELECT dp.id_presupuesto, p.numero_presupuesto, p.id_cliente, p.id_empresa,
       dp.id_documento_ppto, dp.total_documento_ppto AS importe_proforma
FROM documento_presupuesto dp
INNER JOIN presupuesto p ON p.id_presupuesto = dp.id_presupuesto
WHERE dp.tipo_documento_ppto = 'factura_proforma'
  AND dp.activo_documento_ppto = 1
  AND p.activo_presupuesto = 1
  AND NOT EXISTS (
      SELECT 1 FROM documento_presupuesto dp2
      WHERE dp2.id_presupuesto = dp.id_presupuesto
        AND dp2.tipo_documento_ppto = 'factura_final'
        AND dp2.activo_documento_ppto = 1
  )
LIMIT 5;

-- 2.5 Presupuesto con factura_final activa (para TC-07)
SELECT dp.id_presupuesto, p.numero_presupuesto, p.id_cliente
FROM documento_presupuesto dp
INNER JOIN presupuesto p ON p.id_presupuesto = dp.id_presupuesto
WHERE dp.tipo_documento_ppto = 'factura_final'
  AND dp.activo_documento_ppto = 1
  AND p.activo_presupuesto = 1
LIMIT 3;

-- 2.6 Cliente diferente al cliente_A (para TC-05)
SELECT id_cliente, nombre_cliente FROM cliente
WHERE id_cliente != :id_cliente_A AND activo_cliente = 1
LIMIT 3;

-- 2.7 Comprobar series disponibles en tabla de numeración
SELECT * FROM serie_empresa WHERE tipo_serie IN ('factura','abono') LIMIT 10;
-- (ajustar nombre tabla/campo según estructura real del SP de numeración)

-- 2.8 Empresas reales activas (para los TCs de empresa)
SELECT id_empresa, codigo_empresa, nombre_empresa, nombre_comercial_empresa, nif_empresa
FROM empresa
WHERE ficticia_empresa = 0 AND activo_empresa = 1
ORDER BY nombre_empresa;
-- Anotar: empresa_real_A e empresa_real_B para los TCs de conflicto

-- 2.9 Cliente con empresa bloqueada
-- (tiene documento_presupuesto activo de empresa real — FK dp.id_empresa → empresa)
SELECT dp.id_presupuesto, p.id_cliente, c.nombre_cliente,
       e.id_empresa, e.nombre_empresa,
       dp.tipo_documento_ppto, dp.created_at_documento_ppto
FROM documento_presupuesto dp
INNER JOIN empresa e         ON e.id_empresa     = dp.id_empresa
INNER JOIN presupuesto p     ON p.id_presupuesto = dp.id_presupuesto
INNER JOIN cliente c         ON c.id_cliente     = p.id_cliente
WHERE dp.tipo_documento_ppto IN ('factura_anticipo', 'factura_proforma', 'factura_final')
  AND dp.activo_documento_ppto = 1
  AND e.ficticia_empresa = 0
  AND e.activo_empresa = 1
  AND p.activo_presupuesto = 1
ORDER BY dp.created_at_documento_ppto DESC
LIMIT 10;
-- Anotar: id_cliente → cliente_BLOQUEADO, id_empresa → empresa_bloqueada_A

-- 2.10 Cliente SIN empresa bloqueada (sin docs activos de empresa real)
SELECT p.id_cliente, c.nombre_cliente, COUNT(p.id_presupuesto) AS n_pptos
FROM presupuesto p
INNER JOIN cliente c ON c.id_cliente = p.id_cliente
WHERE p.activo_presupuesto = 1
  AND p.id_cliente NOT IN (
      SELECT DISTINCT p2.id_cliente
      FROM documento_presupuesto dp
      INNER JOIN empresa e         ON e.id_empresa     = dp.id_empresa
      INNER JOIN presupuesto p2    ON p2.id_presupuesto = dp.id_presupuesto
      WHERE e.ficticia_empresa = 0
        AND dp.tipo_documento_ppto IN ('factura_anticipo', 'factura_proforma', 'factura_final')
        AND dp.activo_documento_ppto = 1
  )
GROUP BY p.id_cliente
HAVING n_pptos >= 2
LIMIT 5;
-- Anotar: id_cliente → cliente_LIBRE

-- 2.11 Presupuesto de cliente_BLOQUEADO con docs de empresa_real_A
--      (para TC-21: intentar facturarlo con empresa_real_B → debe fallar)
SELECT p.id_presupuesto, p.numero_presupuesto,
       dp.id_empresa AS empresa_en_doc, e.nombre_empresa,
       dp.tipo_documento_ppto
FROM presupuesto p
INNER JOIN documento_presupuesto dp ON dp.id_presupuesto = p.id_presupuesto
INNER JOIN empresa e ON e.id_empresa = dp.id_empresa
WHERE p.id_cliente = :id_cliente_BLOQUEADO
  AND dp.tipo_documento_ppto IN ('factura_anticipo', 'factura_proforma', 'factura_final')
  AND dp.activo_documento_ppto = 1
  AND p.activo_presupuesto = 1
ORDER BY p.id_presupuesto;
-- Anotar: id_presupuesto → ppto_con_emp_A
```

---

### FASE 3 — Casos de prueba: creación de factura agrupada

> Antes de cada TC de creación, usar los IDs obtenidos en FASE 2.
> Tras cada TC de creación, ejecutar las queries de verificación indicadas.

#### TC-01 — Happy path: 2 presupuestos sin anticipos

- **Prerequisito:** P1 y P2 del mismo cliente_A, misma empresa, sin anticipos reales ni proformas.
- **Acción:** llamada a `controller/factura_agrupada.php?op=guardar` con `ids_presupuesto=[P1,P2]`.
- **Resultado esperado:** `{ "success": true, "id": X, "numero": "F-..." }`

```sql
-- V1: cabecera creada correctamente
SELECT id_factura_agrupada, numero_factura_agrupada, serie_factura_agrupada,
       is_abono_agrupada, total_base_agrupada, total_iva_agrupada,
       total_bruto_agrupada, total_anticipos_agrupada, total_a_cobrar_agrupada,
       activo_factura_agrupada
FROM factura_agrupada
WHERE id_factura_agrupada = :id_generado;
-- Esperado: total_anticipos_agrupada = 0.00, total_a_cobrar_agrupada = total_bruto_agrupada
--           is_abono_agrupada = 0, activo_factura_agrupada = 1

-- V2: 2 líneas en factura_agrupada_presupuesto
SELECT id_fap, id_presupuesto, total_bruto_fap, total_anticipos_reales_fap,
       resto_fap, orden_fap, activo_fap
FROM factura_agrupada_presupuesto
WHERE id_factura_agrupada = :id_generado AND activo_fap = 1;
-- Esperado: 2 filas, resto_fap = total_bruto_fap en ambas

-- V3: pago_presupuesto creado para cada presupuesto
SELECT pp.id_presupuesto, pp.id_factura_agrupada, pp.tipo_pago_ppto,
       pp.importe_pago_ppto, pp.estado_pago_ppto
FROM pago_presupuesto pp
WHERE pp.id_factura_agrupada = :id_generado;
-- Esperado: 2 filas con tipo_pago_ppto = 'total', estado_pago_ppto = 'pendiente'

-- V4: los totales de cabecera cuadran con la suma de líneas
SELECT fa.total_bruto_agrupada AS cab_bruto,
       SUM(fap.total_bruto_fap) AS sum_bruto,
       fa.total_bruto_agrupada - SUM(fap.total_bruto_fap) AS diferencia
FROM factura_agrupada fa
JOIN factura_agrupada_presupuesto fap ON fap.id_factura_agrupada = fa.id_factura_agrupada
WHERE fa.id_factura_agrupada = :id_generado
GROUP BY fa.id_factura_agrupada;
-- Esperado: diferencia = 0.00
```

---

#### TC-02 — Happy path: presupuesto con anticipo real

- **Prerequisito:** P1 (sin anticipo) + P3 (con `factura_anticipo` activa de X€), mismo cliente/empresa.
- **Acción:** `op=guardar` con `ids_presupuesto=[P1,P3]`.

```sql
-- V1: total_anticipos_reales_fap de P3 = importe de su factura_anticipo
SELECT fap.id_presupuesto, fap.total_bruto_fap,
       fap.total_anticipos_reales_fap, fap.resto_fap
FROM factura_agrupada_presupuesto fap
WHERE fap.id_factura_agrupada = :id_generado AND fap.id_presupuesto = :P3;
-- Esperado: total_anticipos_reales_fap > 0, resto_fap = total_bruto_fap - total_anticipos_reales_fap

-- V2: total_a_cobrar_agrupada = total_bruto - anticipos
SELECT total_bruto_agrupada, total_anticipos_agrupada, total_a_cobrar_agrupada,
       total_bruto_agrupada - total_anticipos_agrupada AS calculado
FROM factura_agrupada WHERE id_factura_agrupada = :id_generado;
-- Esperado: total_a_cobrar_agrupada = calculado

-- V3: la factura_anticipo de P3 sigue activa (NO se toca)
SELECT activo_documento_ppto
FROM documento_presupuesto
WHERE id_presupuesto = :P3
  AND tipo_documento_ppto = 'factura_anticipo'
  AND activo_documento_ppto = 1;
-- Esperado: 1 fila (sigue activa)

-- V4: el pago de P3 tiene tipo_pago = 'resto'
SELECT tipo_pago_ppto, importe_pago_ppto
FROM pago_presupuesto
WHERE id_presupuesto = :P3 AND id_factura_agrupada = :id_generado;
-- Esperado: tipo_pago_ppto = 'resto', importe_pago_ppto = resto_fap de P3
```

---

#### TC-03 — Happy path: presupuesto con proforma de anticipo

> ⚠️ Este TC fallará hasta que se corrija BUG-01.

- **Prerequisito:** P1 + P4 (con `factura_proforma` activa), mismo cliente/empresa.
  Anotar `id_documento_ppto` de la proforma antes de crear.
- **Acción:** `op=guardar` con `ids_presupuesto=[P1,P4]`.

```sql
-- V1 (validar BUG-01): la proforma de P4 debe quedar inactiva
SELECT activo_documento_ppto
FROM documento_presupuesto
WHERE id_documento_ppto = :id_proforma_P4;
-- Esperado: 0 (fue anulada)
-- FALLA si BUG-01 no está corregido → activo sigue siendo 1

-- V2: total_anticipos_reales_fap de P4 = 0 (proforma NO resta del total)
SELECT fap.total_anticipos_reales_fap, fap.resto_fap, fap.total_bruto_fap
FROM factura_agrupada_presupuesto fap
WHERE fap.id_factura_agrupada = :id_generado AND fap.id_presupuesto = :P4;
-- Esperado: total_anticipos_reales_fap = 0.00, resto_fap = total_bruto_fap

-- V3: los totales de cabecera no descuentan la proforma
SELECT total_anticipos_agrupada, total_a_cobrar_agrupada, total_bruto_agrupada
FROM factura_agrupada WHERE id_factura_agrupada = :id_generado;
-- Esperado: total_anticipos_agrupada = 0.00 (o solo anticipos de P1 si los tiene)
```

---

#### TC-04 — Mezcla de IVA distinto entre presupuestos

- **Prerequisito:** P1 (21% IVA) y P2 (10% IVA), mismo cliente/empresa.
- **Acción:** `op=guardar` con `ids_presupuesto=[P1,P2]`.

```sql
-- V1: total_iva_agrupada = suma de total_iva_fap individuales (no recalculado)
SELECT fa.total_iva_agrupada        AS cab_iva,
       SUM(fap.total_iva_fap)       AS sum_iva,
       fa.total_iva_agrupada - SUM(fap.total_iva_fap) AS diferencia
FROM factura_agrupada fa
JOIN factura_agrupada_presupuesto fap ON fap.id_factura_agrupada = fa.id_factura_agrupada
WHERE fa.id_factura_agrupada = :id_generado
GROUP BY fa.id_factura_agrupada;
-- Esperado: diferencia = 0.00

-- V2: cada línea conserva su IVA individual
SELECT fap.id_presupuesto, fap.total_base_fap, fap.total_iva_fap, fap.total_bruto_fap,
       ROUND(fap.total_iva_fap / NULLIF(fap.total_base_fap,0) * 100, 0) AS pct_iva
FROM factura_agrupada_presupuesto fap
WHERE fap.id_factura_agrupada = :id_generado;
-- Esperado: pct_iva = 21 para P1 y 10 para P2 (aprox)
```

---

#### TC-05 — Validación: presupuestos de clientes distintos

- **Prerequisito:** P1 (cliente_A) y P7 (cliente_B).
- **Acción:** `op=validar_seleccion` con `ids_presupuesto=[P1,P7]`.
- **Resultado esperado:** `{ "valido": false, "errores": ["Todos los presupuestos deben pertenecer al mismo cliente."] }`

```sql
-- Verificar que ningún INSERT se produjo
SELECT COUNT(*) AS total
FROM factura_agrupada
WHERE created_at_factura_agrupada > NOW() - INTERVAL 1 MINUTE
  AND id_cliente IN (:id_cliente_A, :id_cliente_B);
-- Esperado: 0
```

---

#### TC-06 — Validación: presupuesto ya en otra agrupación activa

- **Prerequisito:** Crear previamente una factura agrupada con P5 (TC-01 o similar).
  P5 está activo en `factura_agrupada_presupuesto`.
- **Acción:** `op=validar_seleccion` con `ids_presupuesto=[P1,P5]`.
- **Resultado esperado:** `{ "valido": false, "errores": ["...ya pertenece a otra factura agrupada activa."] }`

```sql
-- Confirmar que P5 sigue en la agrupación activa (no se tocó)
SELECT fap.id_factura_agrupada, fap.activo_fap, fa.activo_factura_agrupada
FROM factura_agrupada_presupuesto fap
JOIN factura_agrupada fa ON fa.id_factura_agrupada = fap.id_factura_agrupada
WHERE fap.id_presupuesto = :P5 AND fap.activo_fap = 1 AND fa.activo_factura_agrupada = 1;
-- Esperado: 1 fila
```

---

#### TC-07 — Validación: presupuesto con factura_final activa

- **Prerequisito:** P6 tiene `factura_final` activa en `documento_presupuesto`.
- **Acción:** `op=validar_seleccion` con `ids_presupuesto=[P1,P6]`.
- **Resultado esperado:** `{ "valido": false, "errores": ["...ya tiene una factura final activa."] }`

```sql
-- Verificar que no se insertó nada
SELECT COUNT(*) FROM factura_agrupada
WHERE created_at_factura_agrupada > NOW() - INTERVAL 1 MINUTE;
-- Esperado: 0
```

---

#### TC-08 — Validación: solo 1 presupuesto

- **Acción:** `op=validar_seleccion` con `ids_presupuesto=[P1]`.
- **Resultado esperado:** `{ "valido": false, "errores": ["Debe seleccionar al menos 2 presupuestos."] }`

No requiere verificación SQL adicional.

---

#### TC-09 — Caso límite: anticipo >= total del presupuesto

- **Prerequisito:** Presupuesto P_SAT donde `factura_anticipo.total_documento_ppto >= vt.total_con_iva`.
  Si no existe en BD, crear ad-hoc un documento_presupuesto de tipo `factura_anticipo`
  cuyo importe sea >= al total del presupuesto y marcarlo activo.
- **Acción:** `op=guardar` con `ids_presupuesto=[P1, P_SAT]`.

```sql
-- V1: resto_fap de P_SAT = 0 (nunca negativo)
SELECT fap.total_bruto_fap, fap.total_anticipos_reales_fap, fap.resto_fap
FROM factura_agrupada_presupuesto fap
WHERE fap.id_factura_agrupada = :id_generado AND fap.id_presupuesto = :P_SAT;
-- Esperado: resto_fap = 0.00

-- V2: total_a_cobrar_agrupada no es negativo
SELECT total_a_cobrar_agrupada
FROM factura_agrupada WHERE id_factura_agrupada = :id_generado;
-- Esperado: >= 0.00
```

---

#### TC-10 — Validación: empresa emisora distinta entre presupuestos

- **Prerequisito:** P1 (empresa_A) y P8 (empresa_B), mismo cliente.
- **Acción:** `op=validar_seleccion` con `ids_presupuesto=[P1,P8]`.
- **Resultado esperado:** `{ "valido": false, "errores": ["Todos los presupuestos deben pertenecer a la misma empresa emisora."] }`

```sql
SELECT COUNT(*) FROM factura_agrupada
WHERE created_at_factura_agrupada > NOW() - INTERVAL 1 MINUTE;
-- Esperado: 0
```

---

### FASE 4 — Casos de prueba: abono/rectificativa

#### TC-11 — Happy path: generar abono de una factura agrupada

- **Prerequisito:** Factura agrupada FA_X creada en TC-01 (activa, `is_abono_agrupada = 0`).
  Anotar `id_factura_agrupada`, `numero_factura_agrupada`, `total_bruto_agrupada`.
- **Acción:** `op=generar_abono` con `id_factura_agrupada=FA_X` y `motivo="TEST_abono"`.
- **Resultado esperado:** `{ "success": true, "id": Y, "numero": "A-..." }`

```sql
-- V1: el abono se creó con importes negativos
SELECT id_factura_agrupada, numero_factura_agrupada, serie_factura_agrupada,
       is_abono_agrupada, id_factura_agrupada_ref, motivo_abono_agrupada,
       total_bruto_agrupada, total_a_cobrar_agrupada
FROM factura_agrupada WHERE id_factura_agrupada = :id_abono;
-- Esperado: is_abono_agrupada = 1, id_factura_agrupada_ref = FA_X
--           total_bruto_agrupada = -(total_bruto de FA_X)
--           serie_factura_agrupada empieza con 'A'

-- V2: la factura original quedó inactiva
SELECT activo_factura_agrupada
FROM factura_agrupada WHERE id_factura_agrupada = :FA_X;
-- Esperado: 0

-- V3: las líneas del abono tienen importes negativos
SELECT fap.id_presupuesto, fap.total_bruto_fap, fap.total_anticipos_reales_fap, fap.resto_fap
FROM factura_agrupada_presupuesto fap
WHERE fap.id_factura_agrupada = :id_abono AND fap.activo_fap = 1;
-- Esperado: todos los campos monetarios negativos (o 0)

-- V4: el número del abono tiene formato de serie abono (A o similar)
SELECT numero_factura_agrupada LIKE 'A%' AS formato_correcto
FROM factura_agrupada WHERE id_factura_agrupada = :id_abono;
-- Esperado: 1
```

---

#### TC-12 — Validación: intentar abonar un abono

- **Prerequisito:** Abono Y creado en TC-11 (`is_abono_agrupada = 1`).
- **Acción:** `op=generar_abono` con `id_factura_agrupada=Y`.
- **Resultado esperado:** `{ "success": false, "message": "No se puede abonar una factura que ya es un abono." }`

```sql
-- Verificar que no se creó un segundo abono
SELECT COUNT(*) FROM factura_agrupada
WHERE id_factura_agrupada_ref = :id_abono_Y;
-- Esperado: 0
```

---

#### TC-13 — Validación: intentar abonar una factura agrupada que no existe

- **Acción:** `op=generar_abono` con `id_factura_agrupada=99999999` (ID imposible).
- **Resultado esperado:** `{ "success": false, "message": "Factura agrupada no encontrada." }`

---

### FASE 5 — Casos de prueba: endpoints del controller

#### TC-14 — `op=listar` — estructura JSON para DataTables

- **Acción:** POST a `controller/factura_agrupada.php?op=listar`.
- **Resultado esperado:** JSON con `draw`, `recordsTotal`, `recordsFiltered`, `data[]`.

Cada elemento de `data` debe tener los campos:
`id_factura_agrupada`, `numero_factura_agrupada`, `nombre_cliente`, `nombre_empresa`,
`fecha_factura_agrupada`, `total_bruto`, `total_a_cobrar`, `n_presupuestos`, `opciones`.

---

#### TC-15 — `op=presupuestos_disponibles` — devuelve solo presupuestos elegibles

- **Acción:** POST a `op=presupuestos_disponibles` con `id_cliente=:id_cliente_A`.
- **Resultado esperado:** `{ "success": true, "data": [...] }`
- **Verificar que NO aparecen en `data`:**
  - P6 (tiene `factura_final` activa)
  - P5 (ya en una `factura_agrupada` activa)

```sql
-- Contrastar resultado con la query directa del modelo
SELECT p.id_presupuesto, p.numero_presupuesto, vt.total_con_iva,
       COALESCE(ant.total_anticipos,0) AS anticipos
FROM presupuesto p
LEFT JOIN v_presupuesto_totales vt ON vt.id_presupuesto = p.id_presupuesto
LEFT JOIN (
    SELECT id_presupuesto, SUM(total_documento_ppto) AS total_anticipos
    FROM documento_presupuesto
    WHERE tipo_documento_ppto = 'factura_anticipo' AND activo_documento_ppto = 1
    GROUP BY id_presupuesto
) ant ON ant.id_presupuesto = p.id_presupuesto
WHERE p.id_cliente = :id_cliente_A AND p.activo_presupuesto = 1
  AND NOT EXISTS (
      SELECT 1 FROM documento_presupuesto dp
      WHERE dp.id_presupuesto = p.id_presupuesto
        AND dp.tipo_documento_ppto = 'factura_final' AND dp.activo_documento_ppto = 1
  )
  AND NOT EXISTS (
      SELECT 1 FROM factura_agrupada_presupuesto fap
      INNER JOIN factura_agrupada fa ON fa.id_factura_agrupada = fap.id_factura_agrupada
      WHERE fap.id_presupuesto = p.id_presupuesto
        AND fap.activo_fap = 1 AND fa.activo_factura_agrupada = 1 AND fa.is_abono_agrupada = 0
  );
```

---

#### TC-16 — `op=mostrar` — devuelve cabecera + líneas

- **Prerequisito:** FA_X creada en TC-01.
- **Acción:** POST a `op=mostrar` con `id_factura_agrupada=FA_X`.
- **Resultado esperado:**
  ```json
  {
    "success": true,
    "cabecera": { "id_factura_agrupada": ..., "numero_factura_agrupada": ..., ... },
    "presupuestos": [ { "id_fap": ..., "id_presupuesto": ..., "total_bruto_fap": ... }, ... ]
  }
  ```
- **Verificar:** `presupuestos` contiene exactamente 2 elementos (P1 y P2).

---

#### TC-17 — `op=desactivar` — soft delete

- **Prerequisito:** FA_Z (factura agrupada creada para este test, no usar FA_X).
- **Acción:** POST a `op=desactivar` con `id_factura_agrupada=FA_Z`.
- **Resultado esperado:** `{ "success": true, "message": "Factura agrupada desactivada correctamente." }`

```sql
-- Verificar soft delete
SELECT activo_factura_agrupada, updated_at_factura_agrupada
FROM factura_agrupada WHERE id_factura_agrupada = :FA_Z;
-- Esperado: activo_factura_agrupada = 0

-- Verificar que ya no aparece en el listado normal
SELECT COUNT(*) FROM vista_factura_agrupada_completa
WHERE id_factura_agrupada = :FA_Z AND activo_factura_agrupada = 1;
-- Esperado: 0
```

---

#### TC-18 — `op=listar_empresas_facturacion` — empresa bloqueada detectada

- **Prerequisito:** `cliente_BLOQUEADO` (de 2.9) con al menos un `documento_presupuesto` activo de empresa real.
- **Acción:** POST a `controller/factura_agrupada.php?op=listar_empresas_facturacion` con `id_cliente=:id_cliente_BLOQUEADO`.
- **Resultado esperado:**
  ```json
  {
    "success": true,
    "empresa_bloqueada": { "id_empresa": <empresa_bloqueada_A>, "nombre_empresa": "...", "nif_empresa": "...", "codigo_empresa": "..." },
    "empresas_reales": [ { "id_empresa": ..., "nombre_empresa": "..." }, ... ]
  }
  ```
- **Verificar:** `empresa_bloqueada` no es null; `empresa_bloqueada.id_empresa` = empresa_bloqueada_A; `empresas_reales` contiene ≥1 empresa; empresa_bloqueada_A aparece en `empresas_reales`.

```sql
-- Contrastar con la query del modelo
SELECT e.id_empresa, e.nombre_empresa, e.nif_empresa, e.codigo_empresa
FROM documento_presupuesto dp
INNER JOIN empresa e     ON e.id_empresa     = dp.id_empresa
INNER JOIN presupuesto p ON p.id_presupuesto = dp.id_presupuesto
WHERE p.id_cliente = :id_cliente_BLOQUEADO
  AND dp.tipo_documento_ppto IN ('factura_proforma', 'factura_anticipo', 'factura_final')
  AND dp.activo_documento_ppto = 1
  AND e.ficticia_empresa = 0
  AND e.activo_empresa = 1
ORDER BY dp.created_at_documento_ppto DESC
LIMIT 1;
-- Esperado: 1 fila; id_empresa debe coincidir con empresa_bloqueada del JSON
```

---

#### TC-19 — `op=listar_empresas_facturacion` — sin empresa bloqueada

- **Prerequisito:** `cliente_LIBRE` (de 2.10) sin documentos de empresa real activos.
- **Acción:** POST a `op=listar_empresas_facturacion` con `id_cliente=:id_cliente_LIBRE`.
- **Resultado esperado:** `{ "success": true, "empresa_bloqueada": null, "empresas_reales": [...] }`
- **Verificar:** `empresa_bloqueada` es literalmente `null`; `empresas_reales` tiene todas las empresas reales activas.

```sql
-- Confirmar que el cliente no tiene docs de empresa real
SELECT COUNT(*) AS total
FROM documento_presupuesto dp
INNER JOIN empresa e ON e.id_empresa = dp.id_empresa
INNER JOIN presupuesto p ON p.id_presupuesto = dp.id_presupuesto
WHERE p.id_cliente = :id_cliente_LIBRE
  AND dp.tipo_documento_ppto IN ('factura_proforma', 'factura_anticipo', 'factura_final')
  AND dp.activo_documento_ppto = 1
  AND e.ficticia_empresa = 0;
-- Esperado: 0
```

---

#### TC-20 — `op=listar_empresas_facturacion` — id_cliente inválido

- **Acción:** POST a `op=listar_empresas_facturacion` con `id_cliente=0` (o sin el campo).
- **Resultado esperado:** `{ "success": false, "message": "ID de cliente no proporcionado." }`

No requiere verificación SQL.

---

#### TC-21 — `op=validar_seleccion` — conflicto: empresa distinta a docs previos

- **Prerequisito:** `ppto_con_emp_A` (de 2.11) con facturas emitidas desde `empresa_real_A`. Segundo presupuesto del mismo cliente sin conflicto (`ppto_SIN_conflicto`, mismo cliente).
- **Acción:** POST a `op=validar_seleccion` con:
  - `ids_presupuesto=[ppto_SIN_conflicto, ppto_con_emp_A]`
  - `id_empresa_real=<empresa_real_B>` (empresa **distinta** a empresa_real_A)
- **Resultado esperado:** `{ "success": false, "errores": ["...ya tiene facturas emitidas con una empresa distinta a la seleccionada."] }`

```sql
-- Verificar que los docs previos de ppto_con_emp_A son de empresa_real_A
SELECT dp.id_empresa, e.nombre_empresa, dp.tipo_documento_ppto
FROM documento_presupuesto dp
INNER JOIN empresa e ON e.id_empresa = dp.id_empresa
WHERE dp.id_presupuesto = :ppto_con_emp_A
  AND dp.tipo_documento_ppto IN ('factura_proforma', 'factura_anticipo', 'factura_final')
  AND dp.activo_documento_ppto = 1;
-- Esperado: id_empresa = empresa_real_A (distinto de empresa_real_B usada en el POST)

-- Confirmar que no se creó ninguna factura agrupada
SELECT COUNT(*) FROM factura_agrupada
WHERE created_at_factura_agrupada > NOW() - INTERVAL 1 MINUTE;
-- Esperado: 0
```

---

#### TC-22 — `op=validar_seleccion` — empresa coincide con docs previos (OK)

- **Prerequisito:** Mismo `ppto_con_emp_A` con facturas de `empresa_real_A`.
- **Acción:** POST a `op=validar_seleccion` con:
  - `ids_presupuesto=[ppto_SIN_conflicto, ppto_con_emp_A]`
  - `id_empresa_real=<empresa_real_A>` (empresa **igual** a la de los docs previos)
- **Resultado esperado:** `{ "success": true, "valido": true }` — sin errores de empresa.

```sql
-- Confirmar que la empresa del POST coincide con los docs previos
SELECT dp.id_empresa
FROM documento_presupuesto dp
WHERE dp.id_presupuesto = :ppto_con_emp_A
  AND dp.tipo_documento_ppto IN ('factura_proforma', 'factura_anticipo', 'factura_final')
  AND dp.activo_documento_ppto = 1
LIMIT 1;
-- Esperado: id_empresa = empresa_real_A (igual a la enviada en id_empresa_real)
```

---

#### TC-23 — Wizard 4 pasos — prueba funcional manual (UI)

> ⚠️ Este TC se ejecuta **manualmente** en `http://192.168.31.19/MDR/view/FacturasAgrupadas/`

**Flujo con cliente_BLOQUEADO (empresa auto-detectada):**

| Paso | Acción | Verificar |
|------|--------|-----------|
| 1. Abrir wizard | Click "Nueva Factura Agrupada" | Modal visible, `step-1` visible, pasos `step-2/3/4` ocultos, indicadores activos |
| 2. Seleccionar cliente_BLOQUEADO | Elegir del select; click "Siguiente" | Spinner en botón durante AJAX |
| 3. Paso Empresa (bloqueada) | `step-2` visible | Selector `#sel-empresa` deshabilitado con empresa_bloqueada_A; alerta azul con nombre; NO aparece `#alertaSinEmpresas` |
| 4. Siguiente desde paso empresa | Click "Siguiente" | `step-3` visible; spinner mientras carga presupuestos; `empresaSelId` tiene el id correcto |
| 5. Paso Presupuestos | Aparecen presupuestos del cliente | Seleccionar ≥ 2 presupuestos con checkboxes |
| 6. Siguiente desde paso presupuestos | Click "Siguiente" | AJAX de validación; pasa a `step-4` si OK; errores en alerta roja si falla |
| 7. Paso Confirmar | Resumen HTML con presupuestos + fecha + observaciones | `#btn-guardar-fa` visible ("Guardar Factura Agrupada") |
| 8. Guardar | Click "Guardar Factura Agrupada" | `success=true`; tabla recarga via DataTables; modal cierra |

**Flujo alternativo con cliente_LIBRE (empresa a elegir):**

| Paso | Verificar |
|------|-----------|
| Seleccionar cliente_LIBRE → Siguiente | `step-2` muestra selector **habilitado**, sin alerta azul |
| Intentar Siguiente sin seleccionar | SweetAlert "Selecciona una empresa emisora para continuar." |
| Seleccionar empresa manualmente | Dropdown con todas las empresas reales activas |
| Siguiente → `step-3` | Presupuestos del cliente_LIBRE en la lista |

**Flujo negativo — sin empresas reales configuradas:**

| Paso | Verificar |
|------|-----------|
| Si `empresas_reales = []` en la respuesta | `step-2` muestra `#alertaSinEmpresas`; selector `#divSelEmpresa` oculto; botón "Siguiente" no avanza |

---

### FASE 6 — Verificación de integridad referencial

```sql
-- 6.1 No debe haber facturas_agrupadas activas sin líneas activas
SELECT fa.id_factura_agrupada, fa.numero_factura_agrupada
FROM factura_agrupada fa
LEFT JOIN factura_agrupada_presupuesto fap
    ON fap.id_factura_agrupada = fa.id_factura_agrupada AND fap.activo_fap = 1
WHERE fa.activo_factura_agrupada = 1
  AND fa.is_abono_agrupada = 0
  AND fap.id_fap IS NULL;
-- Esperado: 0 filas

-- 6.2 Los totales de cabecera deben cuadrar con la suma de líneas (toda la tabla)
SELECT fa.id_factura_agrupada, fa.numero_factura_agrupada,
       fa.total_bruto_agrupada                         AS cab_bruto,
       SUM(fap.total_bruto_fap)                        AS sum_bruto,
       ABS(fa.total_bruto_agrupada - SUM(fap.total_bruto_fap)) AS diferencia
FROM factura_agrupada fa
JOIN factura_agrupada_presupuesto fap ON fap.id_factura_agrupada = fa.id_factura_agrupada
WHERE fap.activo_fap = 1
GROUP BY fa.id_factura_agrupada
HAVING diferencia > 0.01;
-- Esperado: 0 filas

-- 6.3 Cada pago_presupuesto con id_factura_agrupada debe referenciar a una fa activa
SELECT pp.id_pago_ppto, pp.id_presupuesto, pp.id_factura_agrupada
FROM pago_presupuesto pp
LEFT JOIN factura_agrupada fa ON fa.id_factura_agrupada = pp.id_factura_agrupada
WHERE pp.id_factura_agrupada IS NOT NULL
  AND (fa.id_factura_agrupada IS NULL OR fa.activo_factura_agrupada = 0);
-- Esperado: 0 filas (registros huérfanos o con fa inactiva)

-- 6.4 Un presupuesto no puede estar en dos facturas agrupadas activas al mismo tiempo
SELECT fap.id_presupuesto, COUNT(*) AS n_agrupaciones
FROM factura_agrupada_presupuesto fap
INNER JOIN factura_agrupada fa ON fa.id_factura_agrupada = fap.id_factura_agrupada
WHERE fap.activo_fap = 1
  AND fa.activo_factura_agrupada = 1
  AND fa.is_abono_agrupada = 0
GROUP BY fap.id_presupuesto
HAVING n_agrupaciones > 1;
-- Esperado: 0 filas

-- 6.5 Todo abono debe tener una referencia válida a la factura original
SELECT fa.id_factura_agrupada, fa.numero_factura_agrupada, fa.id_factura_agrupada_ref
FROM factura_agrupada fa
LEFT JOIN factura_agrupada fa_orig ON fa_orig.id_factura_agrupada = fa.id_factura_agrupada_ref
WHERE fa.is_abono_agrupada = 1
  AND fa_orig.id_factura_agrupada IS NULL;
-- Esperado: 0 filas (ningun abono con referencia rota)
```

---

### FASE 7 — Limpieza de datos de prueba

```sql
-- Limpiar pagos asociados a facturas TEST
DELETE pp FROM pago_presupuesto pp
INNER JOIN factura_agrupada fa ON fa.id_factura_agrupada = pp.id_factura_agrupada
WHERE fa.observaciones_agrupada LIKE 'TEST_%';

-- Limpiar líneas TEST
DELETE fap FROM factura_agrupada_presupuesto fap
INNER JOIN factura_agrupada fa ON fa.id_factura_agrupada = fap.id_factura_agrupada
WHERE fa.observaciones_agrupada LIKE 'TEST_%';

-- Limpiar cabeceras TEST
DELETE FROM factura_agrupada WHERE observaciones_agrupada LIKE 'TEST_%';

-- Reactivar manualmente cualquier proforma anulada durante TC-03 si BUG-01 no se resolvió
-- UPDATE documento_presupuesto SET activo_documento_ppto = 1 WHERE id_documento_ppto = :id_proforma_P4;
```

---

## Puntos a tener en cuenta para las pruebas

- **Serie:** Las series de las facturas agrupadas comparten numeración con las facturas normales (serie `F`). Los abonos usan serie `A`.
- **Abono completo:** Solo se permite abonar/rectificar una factura agrupada completa, no líneas indivuales.
- **Detalle de presupuestos:** Las facturas agrupadas incluyen todo el detalle de líneas de cada presupuesto, excepto las observaciones de cada presupuesto.
- **Acceso independiente:** Vista en `view/FacturasAgrupadas/` — pantalla propia, no embebida en presupuesto.
- **Presupuesto disponible tras abono:** Cuando se genera un abono (factura original → `activo = 0`), el presupuesto vuelve a ser elegible para una nueva factura agrupada porque la query de `get_presupuestos_disponibles` busca `fa.activo_factura_agrupada = 1 AND fa.is_abono_agrupada = 0`. Verificar que esto es correcto con el negocio.
- **Campo `serie_factura_agrupada`:** Se extrae del primer carácter del número (`substr($numero_factura, 0, 1)`). Validar que el SP devuelve siempre un número con prefijo de letra.

