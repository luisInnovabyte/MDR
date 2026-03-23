# Factura Agrupada de Múltiples Presupuestos

> **Fecha:** 2026-03-18  
> **Solicitado por:** Cliente  
> **Estado:** Propuesta pendiente de implementación

---

## Descripción del requisito

El cliente necesita poder agrupar varios presupuestos en una única factura final, contemplando que sobre esos presupuestos pueden haberse emitido previamente:

- Facturas **proforma** de anticipo (`factura_proforma`)
- Facturas **reales** de anticipo (`factura_anticipo`)
- Combinaciones de ambas

---

## Contexto del sistema actual

El sistema tiene:

- `documento_presupuesto` → vincula **1 presupuesto → N documentos** (proforma, anticipo, final, abono)
- `pago_presupuesto` → pagos vinculados a **1 presupuesto** con empresa bloqueada al primer anticipo real
- Series de numeración gestionadas por SP: serie `factura` (F), serie `factura_proforma` (FP), serie `abono` (A)

### Enfoques descartados

- **Reutilizar `documento_presupuesto`**: su FK `id_presupuesto` es 1→N. No puede representar N presupuestos.
- **"Presupuesto contenedor"**: artificio sucio que rompe la semántica del modelo.

---

## Solución propuesta: Nueva entidad `factura_agrupada`

### 1. Nuevas tablas BD

```sql
-- Cabecera de la factura agrupada
CREATE TABLE factura_agrupada (
    id_factura_agrupada         INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    numero_factura_agrupada     VARCHAR(50) NOT NULL UNIQUE  COMMENT 'Número serie F (mismo SP que factura_anticipo/final)',
    id_empresa                  INT UNSIGNED NOT NULL        COMMENT 'Empresa emisora — igual para todos los presupuestos incluidos',
    id_cliente                  INT UNSIGNED NOT NULL        COMMENT 'Cliente — igual para todos',
    fecha_factura_agrupada      DATE         NOT NULL,
    observaciones_agrupada      TEXT,
    -- Totales snapshot (calculados al generar)
    total_base_agrupada         DECIMAL(10,2) DEFAULT 0,
    total_iva_agrupada          DECIMAL(10,2) DEFAULT 0,
    total_bruto_agrupada        DECIMAL(10,2) DEFAULT 0      COMMENT 'Suma de totales de todos los presupuestos',
    total_anticipos_agrupada    DECIMAL(10,2) DEFAULT 0      COMMENT 'Suma de anticipos reales ya facturados',
    total_a_cobrar_agrupada     DECIMAL(10,2) DEFAULT 0      COMMENT 'total_bruto - total_anticipos',
    -- Control
    pdf_path_agrupada           VARCHAR(500),
    activo_factura_agrupada     BOOLEAN DEFAULT TRUE,
    created_at_factura_agrupada TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_factura_agrupada TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_fag_empresa  FOREIGN KEY (id_empresa) REFERENCES empresa(id_empresa)  ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_fag_cliente  FOREIGN KEY (id_cliente) REFERENCES cliente(id_cliente)  ON DELETE RESTRICT ON UPDATE CASCADE,
    INDEX idx_activo_factura_agrupada (activo_factura_agrupada),
    INDEX idx_cliente_factura_agrupada (id_cliente)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;


-- Líneas: qué presupuestos incluye y con qué importes
CREATE TABLE factura_agrupada_presupuesto (
    id_fap                      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_factura_agrupada         INT UNSIGNED NOT NULL,
    id_presupuesto              INT UNSIGNED NOT NULL,
    -- Snapshot importes del presupuesto
    total_base_fap              DECIMAL(10,2) DEFAULT 0,
    total_iva_fap               DECIMAL(10,2) DEFAULT 0,
    total_bruto_fap             DECIMAL(10,2) DEFAULT 0,
    -- Anticipos reales YA facturados de este presupuesto (factura_anticipo activas)
    total_anticipos_reales_fap  DECIMAL(10,2) DEFAULT 0,
    -- Resto que corresponde cobrar en esta factura agrupada
    resto_fap                   DECIMAL(10,2) DEFAULT 0,
    -- Orden de aparición en el PDF
    orden_fap                   TINYINT UNSIGNED DEFAULT 0,
    activo_fap                  BOOLEAN DEFAULT TRUE,
    created_at_fap              TIMESTAMP DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_fap_agrupada     FOREIGN KEY (id_factura_agrupada) REFERENCES factura_agrupada(id_factura_agrupada) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_fap_presupuesto  FOREIGN KEY (id_presupuesto)      REFERENCES presupuesto(id_presupuesto)          ON DELETE RESTRICT ON UPDATE CASCADE,
    UNIQUE KEY uq_fap_ppto (id_factura_agrupada, id_presupuesto)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
```

### 2. Campo adicional en `pago_presupuesto`

Para trazar qué pago proviene de una factura agrupada:

```sql
ALTER TABLE pago_presupuesto
    ADD COLUMN id_factura_agrupada INT UNSIGNED NULL DEFAULT NULL
        COMMENT 'Si el pago se originó en una factura agrupada'
        AFTER id_documento_ppto,
    ADD CONSTRAINT fk_pp_factura_agrupada
        FOREIGN KEY (id_factura_agrupada)
        REFERENCES factura_agrupada(id_factura_agrupada)
        ON DELETE SET NULL ON UPDATE CASCADE;
```

---

### 3. Ficheros nuevos

| Fichero | Propósito |
|---|---|
| `models/FacturaAgrupada.php` | Modelo CRUD + validaciones |
| `controller/factura_agrupada.php` | Switch ops: `listar`, `guardaryeditar`, `generar`, `descargar`, `desactivar` |
| `controller/impresion_factura_agrupada.php` | Genera el PDF con TCPDF |
| `view/FacturasAgrupadas/index.php` | Vista listado + wizard de selección de presupuestos |
| `view/FacturasAgrupadas/factura_agrupada.js` | JS / AJAX |

---

### 4. Flujo completo de generación

```
1. Usuario selecciona N presupuestos del mismo cliente
        ↓
2. VALIDACIONES (antes de mostrar formulario)
   ✅ Todos del mismo cliente
   ✅ Todos con la misma empresa emisora (o empresa libre si no hay anticipos reales)
   ✅ Ninguno tiene ya una factura_final activa
   ✅ Ninguno está incluido en otra factura_agrupada activa
   ✅ No mezclar idiomas (es / en) → aviso al usuario
        ↓
3. CÁLCULO POR PRESUPUESTO
   Para cada presupuesto:
   - total_bruto          = total_con_iva de v_presupuesto_totales
   - anticipos_reales     = SUM(importe) de factura_anticipo activas
   - proformas_anticipo   = detectadas pero NO restan (solo se anulan)
   - resto                = total_bruto - anticipos_reales
        ↓
4. GENERACIÓN (transacción)
   a) sp_obtener_siguiente_numero('factura', id_empresa) → número FA-YYYY/NNN
   b) INSERT factura_agrupada
   c) INSERT factura_agrupada_presupuesto (una fila por presupuesto)
   d) Para cada presupuesto:
      - Anular proformas de anticipo activas (activo_documento_ppto = 0)
      - INSERT pago_presupuesto tipo='total' o 'resto' vinculado a la factura agrupada
        (campo nuevo: id_factura_agrupada en pago_presupuesto)
   e) sp_actualizar_contador_empresa(...)
   f) Generar PDF → guardar ruta en pdf_path_agrupada
        ↓
5. DEVOLVER JSON { success, numero_factura, url_pdf }
```

---

### 5. Casuística de anticipos

| Tipo documento previo | Acción al generar factura agrupada |
|---|---|
| **Sin anticipos** | Factura agrupada por el total completo de todos |
| **Proforma de anticipo** (`factura_proforma`) | Se anula automáticamente (activo=0). No resta del total. Solo nota informativa opcional en PDF |
| **Anticipo real** (`factura_anticipo`) | Se mantiene activa. Su importe **se deduce** en la factura agrupada. Se referencia en el PDF con el número de factura |
| **Mezcla de ambos** | Se anulan las proformas y se deducen solo los anticipos reales |

---

### 6. Validaciones de negocio a implementar

```
⛔ Los N presupuestos deben ser del mismo id_cliente
⛔ La empresa emisora debe ser la misma en todos (o libre en todos)
⛔ Un presupuesto ya incluido en otra factura_agrupada activa → bloqueado
⛔ Un presupuesto con factura_final activa → bloqueado (ya está liquidado)
⛔ Un presupuesto en estado CANCELADO → advertencia / bloqueo
⚠️  Si hay anticipos reales de distintas empresas → conflicto de empresa emisora
⚠️  Mínimo 2 presupuestos (si es 1, usar el flujo normal de factura_final)
```

---

### 7. Diseño del PDF

```
┌─────────────────────────────────────────────────────────┐
│  FACTURA                               FA-2026/001       │
│  [Logo empresa]      Empresa emisora (real)              │
│  ─────────────────────────────────────────────────────── │
│  Cliente: Nombre Apellido / NIF                         │
│  Fecha: 18/03/2026                                       │
├─────────────────────────────────────────────────────────┤
│  PRESUPUESTO 1: MDR-2026-0045 / Evento Boda García      │
│  Fechas evento: 12/04/2026 – 13/04/2026                 │
│  ─── Líneas del presupuesto ────                        │
│   • Sonido escenario       1 ud  ×  2.500,00 = 2.500,00 │
│   • Iluminación decorativa 2 ud  ×    800,00 = 1.600,00 │
│   Base: 4.100,00 │ IVA 21%: 861,00 │ Total: 4.961,00   │
│   Menos: Anticipo Factura F-2026/003 (15/01/2026)       │
│                                         – 1.000,00      │
│  SUBTOTAL PRESUPUESTO 1:                3.961,00         │
├─────────────────────────────────────────────────────────┤
│  PRESUPUESTO 2: MDR-2026-0051 / Congreso Tech Madrid    │
│  ...                                                    │
│  SUBTOTAL PRESUPUESTO 2:                5.200,00         │
├─────────────────────────────────────────────────────────┤
│                                                         │
│  TOTAL BRUTO:                          10.961,00         │
│  Total anticipos facturados:           – 1.000,00       │
│  TOTAL A COBRAR:                        9.961,00 €       │
└─────────────────────────────────────────────────────────┘
```

---

### 8. Resumen de impacto

| Capa | Cambios |
|---|---|
| **BD** | 2 tablas nuevas + 1 columna nueva en `pago_presupuesto` |
| **Models** | 1 modelo nuevo `FacturaAgrupada.php` |
| **Controllers** | 2 controladores nuevos (gestión + impresión PDF) |
| **Views** | 1 vista nueva con wizard de selección |
| **Sin tocar** | Todo el flujo actual de presupuesto individual |

El flujo individual de presupuesto (anticipo → final) queda **intacto**. La factura agrupada es un camino alternativo que no interfiere con él.

---

## Pendiente de decidir

- [ ] ¿La factura agrupada consume la misma serie `F` que las facturas normales, o tiene su propia serie (ej. `FA`)?
- [ ] ¿Se permite abonar (rectificativa) una factura agrupada completa, o solo presupuesto a presupuesto?
- [ ] ¿Mostrar el detalle completo de líneas de cada presupuesto en el PDF, o solo los totales por presupuesto?
- [ ] ¿La vista de selección de presupuestos parte de la ficha del cliente, o es una pantalla independiente?
