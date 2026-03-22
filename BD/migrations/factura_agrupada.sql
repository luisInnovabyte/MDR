-- ============================================================
-- MIGRACIÓN: Factura Agrupada de Múltiples Presupuestos
-- Fecha:     2026-03-20
-- ============================================================

-- ─────────────────────────────────────────────────────────────
-- 1. TABLA CABECERA: factura_agrupada
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS factura_agrupada (
    id_factura_agrupada         INT UNSIGNED       AUTO_INCREMENT PRIMARY KEY,
    numero_factura_agrupada     VARCHAR(50)        NOT NULL UNIQUE  COMMENT 'Número generado por SP (serie F o A según tipo)',
    serie_factura_agrupada      VARCHAR(10)        NULL             COMMENT 'Serie almacenada (ej. F, A)',
    id_empresa                  INT UNSIGNED       NOT NULL         COMMENT 'Empresa emisora — igual para todos los presupuestos incluidos',
    id_cliente                  INT UNSIGNED       NOT NULL         COMMENT 'Cliente — igual para todos los presupuestos incluidos',
    fecha_factura_agrupada      DATE               NOT NULL,
    observaciones_agrupada      TEXT               NULL,

    -- Snapshot de totales calculados al generar
    total_base_agrupada         DECIMAL(10,2)      NOT NULL DEFAULT 0,
    total_iva_agrupada          DECIMAL(10,2)      NOT NULL DEFAULT 0,
    total_bruto_agrupada        DECIMAL(10,2)      NOT NULL DEFAULT 0  COMMENT 'Suma de totales con IVA de todos los presupuestos',
    total_anticipos_agrupada    DECIMAL(10,2)      NOT NULL DEFAULT 0  COMMENT 'Suma de anticipos reales ya facturados',
    total_a_cobrar_agrupada     DECIMAL(10,2)      NOT NULL DEFAULT 0  COMMENT 'total_bruto - total_anticipos',

    -- Soporte para abono/rectificativa de la propia factura agrupada
    is_abono_agrupada           TINYINT(1)         NOT NULL DEFAULT 0  COMMENT '0=factura normal, 1=abono/rectificativa',
    id_factura_agrupada_ref     INT UNSIGNED       NULL                COMMENT 'FK a la factura agrupada que se abona (solo si is_abono=1)',
    motivo_abono_agrupada       VARCHAR(255)       NULL,

    -- PDF
    pdf_path_agrupada           VARCHAR(500)       NULL,

    -- Control
    activo_factura_agrupada     TINYINT(1)         NOT NULL DEFAULT 1,
    created_at_factura_agrupada TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at_factura_agrupada TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT fk_fag_empresa    FOREIGN KEY (id_empresa)               REFERENCES empresa(id_empresa)               ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_fag_cliente    FOREIGN KEY (id_cliente)               REFERENCES cliente(id_cliente)               ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_fag_ref_abono  FOREIGN KEY (id_factura_agrupada_ref)  REFERENCES factura_agrupada(id_factura_agrupada) ON DELETE RESTRICT ON UPDATE CASCADE,

    INDEX idx_activo_factura_agrupada   (activo_factura_agrupada),
    INDEX idx_cliente_factura_agrupada  (id_cliente),
    INDEX idx_empresa_factura_agrupada  (id_empresa),
    INDEX idx_fecha_factura_agrupada    (fecha_factura_agrupada),
    INDEX idx_numero_factura_agrupada   (numero_factura_agrupada)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
  COMMENT='Cabecera de facturas agrupadas que consolidan N presupuestos de un mismo cliente';


-- ─────────────────────────────────────────────────────────────
-- 2. TABLA LÍNEAS: factura_agrupada_presupuesto
-- ─────────────────────────────────────────────────────────────
CREATE TABLE IF NOT EXISTS factura_agrupada_presupuesto (
    id_fap                      INT UNSIGNED       AUTO_INCREMENT PRIMARY KEY,
    id_factura_agrupada         INT UNSIGNED       NOT NULL,
    id_presupuesto              INT UNSIGNED       NOT NULL,

    -- Snapshot de importes del presupuesto en el momento de generar la factura
    total_base_fap              DECIMAL(10,2)      NOT NULL DEFAULT 0,
    total_iva_fap               DECIMAL(10,2)      NOT NULL DEFAULT 0,
    total_bruto_fap             DECIMAL(10,2)      NOT NULL DEFAULT 0,

    -- Anticipos reales (facturas_anticipo activas) del presupuesto al generar
    total_anticipos_reales_fap  DECIMAL(10,2)      NOT NULL DEFAULT 0,

    -- Resto que corresponde cobrar de este presupuesto en la factura agrupada
    resto_fap                   DECIMAL(10,2)      NOT NULL DEFAULT 0  COMMENT 'total_bruto_fap - total_anticipos_reales_fap',

    -- Orden de aparición en el PDF
    orden_fap                   TINYINT UNSIGNED   NOT NULL DEFAULT 0,

    activo_fap                  TINYINT(1)         NOT NULL DEFAULT 1,
    created_at_fap              TIMESTAMP          NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT fk_fap_agrupada    FOREIGN KEY (id_factura_agrupada) REFERENCES factura_agrupada(id_factura_agrupada) ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_fap_presupuesto FOREIGN KEY (id_presupuesto)      REFERENCES presupuesto(id_presupuesto)           ON DELETE RESTRICT ON UPDATE CASCADE,

    UNIQUE KEY uq_fap_ppto (id_factura_agrupada, id_presupuesto),
    INDEX idx_fap_ppto      (id_presupuesto),
    INDEX idx_fap_agrupada  (id_factura_agrupada)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
  COMMENT='Presupuestos que componen una factura agrupada, con snapshot de importes';


-- ─────────────────────────────────────────────────────────────
-- 3. ALTER pago_presupuesto: trazabilidad con factura agrupada
-- ─────────────────────────────────────────────────────────────
ALTER TABLE pago_presupuesto
    ADD COLUMN id_factura_agrupada INT UNSIGNED NULL DEFAULT NULL
        COMMENT 'Si el pago se originó en una factura agrupada'
        AFTER id_documento_ppto,
    ADD CONSTRAINT fk_pp_factura_agrupada
        FOREIGN KEY (id_factura_agrupada)
        REFERENCES factura_agrupada(id_factura_agrupada)
        ON DELETE SET NULL ON UPDATE CASCADE;


-- ─────────────────────────────────────────────────────────────
-- 4. VISTA: vista_factura_agrupada_completa
-- ─────────────────────────────────────────────────────────────
CREATE OR REPLACE VIEW vista_factura_agrupada_completa AS
SELECT
    fa.id_factura_agrupada,
    fa.numero_factura_agrupada,
    fa.serie_factura_agrupada,
    fa.fecha_factura_agrupada,
    fa.observaciones_agrupada,
    fa.total_base_agrupada,
    fa.total_iva_agrupada,
    fa.total_bruto_agrupada,
    fa.total_anticipos_agrupada,
    fa.total_a_cobrar_agrupada,
    fa.is_abono_agrupada,
    fa.id_factura_agrupada_ref,
    fa.motivo_abono_agrupada,
    fa.pdf_path_agrupada,
    fa.activo_factura_agrupada,
    fa.created_at_factura_agrupada,
    fa.updated_at_factura_agrupada,

    -- Empresa emisora
    fa.id_empresa,
    e.nombre_empresa,
    e.nombre_comercial_empresa,
    e.nif_empresa,
    e.ficticia_empresa,

    -- Cliente
    fa.id_cliente,
    c.nombre_cliente,
    c.apellido_cliente,
    CONCAT(c.nombre_cliente, ' ', COALESCE(c.apellido_cliente, '')) AS nombre_completo_cliente,
    c.email_cliente,
    c.telefono_cliente,
    c.nif_cliente,

    -- Nº de presupuestos incluidos
    (SELECT COUNT(*) FROM factura_agrupada_presupuesto fap2
        WHERE fap2.id_factura_agrupada = fa.id_factura_agrupada
          AND fap2.activo_fap = 1)  AS num_presupuestos_agrupada,

    -- Número de la factura original (si es abono)
    fa_ref.numero_factura_agrupada  AS numero_factura_original

FROM factura_agrupada fa
INNER JOIN empresa  e  ON fa.id_empresa = e.id_empresa
INNER JOIN cliente  c  ON fa.id_cliente = c.id_cliente
LEFT  JOIN factura_agrupada fa_ref ON fa.id_factura_agrupada_ref = fa_ref.id_factura_agrupada

ORDER BY fa.fecha_factura_agrupada DESC, fa.id_factura_agrupada DESC;

-- ─── Corrección aplicada manualmente: cliente no tiene apellido_cliente ───
-- Campos reales de cliente: id_cliente, codigo_cliente, nombre_cliente,
-- nif_cliente, email_cliente, telefono_cliente
