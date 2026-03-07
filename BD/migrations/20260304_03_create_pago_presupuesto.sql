-- ============================================================
-- Migration: 20260304_03_create_pago_presupuesto.sql
-- Descripción: Tabla para registrar pagos a cuenta recibidos
--              sobre presupuestos aprobados (anticipos, totales, resto, devoluciones)
-- Fecha: 04 de marzo de 2026
-- ============================================================

CREATE TABLE pago_presupuesto (
    -- Identificación
    id_pago_ppto INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_presupuesto INT UNSIGNED NOT NULL
        COMMENT 'FK a presupuesto',
    id_documento_ppto INT UNSIGNED
        COMMENT 'FK a documento_presupuesto (factura vinculada al pago, si se generó)',

    -- Tipo de pago
    tipo_pago_ppto ENUM(
        'anticipo',    -- Pago a cuenta parcial
        'total',       -- Pago del 100% del presupuesto
        'resto',       -- Pago del saldo pendiente
        'devolucion'   -- Devolución al cliente (por abono de factura)
    ) NOT NULL
        COMMENT 'Tipo de pago registrado',

    -- Importes
    importe_pago_ppto DECIMAL(10,2) NOT NULL
        COMMENT 'Importe recibido (negativo si devolución)',
    porcentaje_pago_ppto DECIMAL(5,2)
        COMMENT 'Porcentaje sobre el total del presupuesto (calculado)',

    -- Método de pago
    id_metodo_pago INT UNSIGNED
        COMMENT 'FK a metodo_pago (efectivo, transferencia, tarjeta, etc.)',
    referencia_pago_ppto VARCHAR(100)
        COMMENT 'Número de transferencia, recibo, cheque, etc.',

    -- Fechas
    fecha_pago_ppto DATE NOT NULL
        COMMENT 'Fecha en que se recibió el pago',
    fecha_valor_pago_ppto DATE
        COMMENT 'Fecha valor bancaria (puede diferir de fecha_pago)',

    -- Estado
    estado_pago_ppto ENUM(
        'pendiente',   -- Registrado pero no confirmado
        'recibido',    -- Pago recibido y confirmado
        'conciliado',  -- Conciliado en contabilidad
        'anulado'      -- Anulado (por generación de abono)
    ) DEFAULT 'recibido'
        COMMENT 'Estado actual del pago',

    -- Observaciones
    observaciones_pago_ppto TEXT,

    -- Auditoría (campos obligatorios MDR)
    activo_pago_ppto BOOLEAN DEFAULT TRUE
        COMMENT 'Soft delete: TRUE=activo, FALSE=eliminado',
    created_at_pago_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at_pago_ppto TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

    -- Foreign Keys
    CONSTRAINT fk_pago_ppto_presupuesto
        FOREIGN KEY (id_presupuesto)
        REFERENCES presupuesto(id_presupuesto)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    CONSTRAINT fk_pago_ppto_documento
        FOREIGN KEY (id_documento_ppto)
        REFERENCES documento_presupuesto(id_documento_ppto)
        ON DELETE SET NULL ON UPDATE CASCADE,

    CONSTRAINT fk_pago_ppto_metodo
        FOREIGN KEY (id_metodo_pago)
        REFERENCES metodo_pago(id_metodo_pago)
        ON DELETE RESTRICT ON UPDATE CASCADE,

    -- Índices
    INDEX idx_presupuesto_pago_ppto (id_presupuesto),
    INDEX idx_tipo_pago_ppto (tipo_pago_ppto),
    INDEX idx_fecha_pago_ppto (fecha_pago_ppto),
    INDEX idx_estado_pago_ppto (estado_pago_ppto),
    INDEX idx_activo_pago_ppto (activo_pago_ppto),
    INDEX idx_documento_pago_ppto (id_documento_ppto)

) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci
COMMENT='Registro de pagos a cuenta sobre presupuestos aprobados (anticipos, totales, resto, devoluciones)';
