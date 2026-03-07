-- ============================================================
-- Migration: 20260304_02_create_documento_presupuesto.sql
-- Descripción: Tabla que registra todos los documentos generados
--              a partir de presupuestos (partes, proformas, facturas, abonos)
-- Fecha: 04 de marzo de 2026
-- ============================================================

CREATE TABLE documento_presupuesto (
    -- Identificación
    id_documento_ppto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_presupuesto INT UNSIGNED NOT NULL
        COMMENT 'FK a presupuesto',
    id_version_presupuesto INT UNSIGNED
        COMMENT 'Versión del presupuesto usada al generar el documento',
    id_empresa INT UNSIGNED NOT NULL
        COMMENT 'Empresa emisora - DEBE SER REAL (no ficticia) para facturas',
    seleccion_manual_empresa_documento_ppto BOOLEAN DEFAULT FALSE
        COMMENT 'TRUE si empresa fue seleccionada manualmente (facturas), FALSE si heredada (presupuesto, parte_trabajo)',

    -- Tipo de documento
    tipo_documento_ppto ENUM(
        'presupuesto',           -- PDF presupuesto original
        'parte_trabajo',         -- PDF para técnicos (sin precios)
        'factura_proforma',      -- Factura proforma - REQUIERE EMPRESA REAL
        'factura_anticipo',      -- Factura de anticipo (parcial) - REQUIERE EMPRESA REAL
        'factura_final',         -- Factura final - REQUIERE EMPRESA REAL
        'factura_rectificativa'  -- Abono/rectificativa - USA MISMA EMPRESA QUE FACTURA ORIGEN
    ) NOT NULL
        COMMENT 'Tipo de documento generado',

    -- Numeración
    numero_documento_ppto VARCHAR(50) NOT NULL
        COMMENT 'Número generado (P2024-001, FP2024/001, F2024/001, R2024/001)',
    serie_documento_ppto VARCHAR(10)
        COMMENT 'Serie usada (P, FP, F, R)',

    -- Relación con documento original (para abonos/rectificativas)
    id_documento_origen INT UNSIGNED
        COMMENT 'FK al documento_presupuesto que se rectifica (solo para factura_rectificativa)',
    motivo_abono_documento_ppto VARCHAR(255)
        COMMENT 'Motivo del abono/rectificativa (obligatorio si tipo=factura_rectificativa)',

    -- Importes (para facturas)
    subtotal_documento_ppto DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Base imponible (negativo si abono)',
    total_iva_documento_ppto DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Total IVA (negativo si abono)',
    total_documento_ppto DECIMAL(10,2) DEFAULT NULL
        COMMENT 'Total con IVA (negativo si abono)',

    -- Archivo PDF
    ruta_pdf_documento_ppto VARCHAR(255)
        COMMENT 'Ruta relativa: public/documentos/presupuestos/[id_ppto]/[tipo]_[numero].pdf',
    tamano_pdf_documento_ppto INT UNSIGNED
        COMMENT 'Tamaño del PDF en bytes',

    -- Fechas
    fecha_emision_documento_ppto DATE NOT NULL
        COMMENT 'Fecha de emisión del documento',
    fecha_generacion_documento_ppto DATETIME DEFAULT CURRENT_TIMESTAMP
        COMMENT 'Timestamp de generación del PDF',

    -- Observaciones internas
    observaciones_documento_ppto TEXT,

    -- Auditoría (campos obligatorios MDR)
    activo_documento_ppto BOOLEAN DEFAULT TRUE
        COMMENT 'Soft delete: TRUE=activo, FALSE=anulado/reemplazado',
    created_at_documento_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_documento_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign Keys
    CONSTRAINT fk_doc_ppto_presupuesto
        FOREIGN KEY (id_presupuesto)
        REFERENCES presupuesto(id_presupuesto)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_doc_ppto_version
        FOREIGN KEY (id_version_presupuesto)
        REFERENCES presupuesto_version(id_version_presupuesto)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_doc_ppto_empresa
        FOREIGN KEY (id_empresa)
        REFERENCES empresa(id_empresa)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_doc_ppto_origen
        FOREIGN KEY (id_documento_origen)
        REFERENCES documento_presupuesto(id_documento_ppto)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    -- Índices
    INDEX idx_presupuesto_doc_ppto (id_presupuesto),
    INDEX idx_tipo_doc_ppto (tipo_documento_ppto),
    INDEX idx_numero_doc_ppto (numero_documento_ppto),
    INDEX idx_fecha_emision_doc_ppto (fecha_emision_documento_ppto),
    INDEX idx_activo_doc_ppto (activo_documento_ppto),
    INDEX idx_doc_ppto_origen (id_documento_origen),
    INDEX idx_empresa_doc_ppto (id_empresa),

    UNIQUE KEY uk_numero_tipo_doc_ppto (numero_documento_ppto, tipo_documento_ppto)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Documentos generados a partir de presupuestos: partes de trabajo, facturas proforma, anticipos, abonos';
