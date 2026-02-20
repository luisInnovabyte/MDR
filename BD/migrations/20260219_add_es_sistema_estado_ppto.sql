-- ============================================================
-- Migración: Añadir columna es_sistema_estado_ppto
-- Fecha: 2026-02-19
-- Descripción: Marca los estados gestionados automáticamente
--              por el sistema (BORRADOR, APROBADO, CANCELADO,
--              ENVIADO/Esperando respuesta) para impedir que
--              un usuario los desactive, reactive o edite
--              desde el mantenimiento de estados.
-- ============================================================

ALTER TABLE estado_presupuesto
    ADD COLUMN es_sistema_estado_ppto TINYINT(1) NOT NULL DEFAULT 0
        COMMENT 'Gestionado automáticamente por el sistema: 1=sistema, 0=usuario'
    AFTER activo_estado_ppto;

-- Marcar los estados del sistema
-- id=1: BORRADOR  |  id=3: Aprobado  |  id=5: Cancelado  |  id=8: Esperando respuesta (Enviado)
UPDATE estado_presupuesto
SET es_sistema_estado_ppto = 1
WHERE id_estado_ppto IN (1, 3, 5, 8);

-- Verificación
SELECT id_estado_ppto, codigo_estado_ppto, nombre_estado_ppto, es_sistema_estado_ppto
FROM estado_presupuesto
ORDER BY id_estado_ppto;
