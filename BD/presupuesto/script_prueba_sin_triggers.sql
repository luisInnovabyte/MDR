-- ============================================================
-- SCRIPT ALTERNATIVO: CREAR PRESUPUESTO SIN TRIGGERS
-- ============================================================
-- 
-- Si los triggers causan problemas, usa este script que
-- desactiva temporalmente los triggers problemáticos
--
-- ⚠️ IMPORTANTE: Restaura los triggers después de las pruebas
-- ============================================================

-- ============================================================
-- OPCIÓN 1: Desactivar triggers temporalmente
-- ============================================================

-- Listar todos los triggers de presupuesto
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING
FROM information_schema.TRIGGERS
WHERE EVENT_OBJECT_SCHEMA = 'toldos_db'
AND EVENT_OBJECT_TABLE IN ('presupuesto', 'presupuesto_version', 'linea_presupuesto')
ORDER BY EVENT_OBJECT_TABLE, ACTION_TIMING, EVENT_MANIPULATION;

-- Guardar triggers para restaurar después
DROP TABLE IF EXISTS temp_triggers_backup;
CREATE TEMPORARY TABLE temp_triggers_backup AS
SELECT 
    TRIGGER_NAME,
    EVENT_MANIPULATION,
    EVENT_OBJECT_TABLE,
    ACTION_TIMING,
    ACTION_STATEMENT
FROM information_schema.TRIGGERS
WHERE EVENT_OBJECT_SCHEMA = 'toldos_db'
AND EVENT_OBJECT_TABLE = 'presupuesto';

-- Desactivar trigger problemático
DROP TRIGGER IF EXISTS trg_presupuesto_after_insert;

SELECT '✓ Trigger problemático desactivado temporalmente' AS 'Estado';

-- ============================================================
-- OPCIÓN 2: Crear presupuesto y versión manualmente
-- ============================================================

SET @usuario_prueba = 1;
SET @fecha_actual = CURDATE();

-- Obtener IDs necesarios
SET @id_cliente_prueba = (SELECT id_cliente FROM cliente WHERE activo_cliente = 1 LIMIT 1);
SET @id_estado_borrador = (
    SELECT id_estado_ppto 
    FROM estado_presupuesto 
    WHERE UPPER(codigo_estado_ppto) = 'BORRADOR' 
    OR UPPER(nombre_estado_ppto) LIKE '%BORRADOR%'
    LIMIT 1
);

-- Si no hay estado borrador, usar el primero disponible
SET @id_estado_borrador = COALESCE(@id_estado_borrador, (
    SELECT id_estado_ppto FROM estado_presupuesto WHERE activo_estado_ppto = 1 LIMIT 1
));

-- Verificar datos
SELECT 
    CASE 
        WHEN @id_cliente_prueba IS NULL THEN '✗ ERROR: No hay clientes'
        WHEN @id_estado_borrador IS NULL THEN '✗ ERROR: No hay estados'
        ELSE '✓ Datos disponibles para crear presupuesto'
    END AS 'Verificación';

-- Crear presupuesto
INSERT INTO presupuesto (
    numero_presupuesto,
    id_cliente,
    id_estado_ppto,
    fecha_presupuesto,
    fecha_validez_presupuesto,
    nombre_evento_presupuesto,
    observaciones_cabecera_presupuesto,
    version_actual_presupuesto,
    activo_presupuesto,
    created_at_presupuesto
) VALUES (
    CONCAT('P-PRUEBA-', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')),
    @id_cliente_prueba,
    @id_estado_borrador,
    @fecha_actual,
    DATE_ADD(@fecha_actual, INTERVAL 30 DAY),
    'PRUEBA - Sin Triggers',
    'Presupuesto creado sin triggers para pruebas',
    1,  -- Versión actual = 1
    1,
    NOW()
);

SET @id_presupuesto_prueba = LAST_INSERT_ID();

SELECT 
    @id_presupuesto_prueba AS 'ID_Presupuesto',
    '✓ Presupuesto creado' AS 'Estado';

-- Crear versión 1 manualmente
INSERT INTO presupuesto_version (
    id_presupuesto,
    numero_version_presupuesto,
    version_padre_presupuesto,
    estado_version_presupuesto,
    motivo_modificacion_version,
    fecha_creacion_version,
    creado_por_version,
    activo_version,
    created_at_version
) VALUES (
    @id_presupuesto_prueba,
    1,
    NULL,
    'borrador',
    'Versión inicial - Creada sin triggers',
    NOW(),
    @usuario_prueba,
    1,
    NOW()
);

SET @id_version_prueba = LAST_INSERT_ID();

SELECT 
    @id_version_prueba AS 'ID_Version',
    '✓ Versión creada' AS 'Estado';

-- ============================================================
-- Verificar la creación
-- ============================================================

SELECT '═══════════════════════════════════════' AS '';
SELECT '📋 PRESUPUESTO CREADO' AS '';

SELECT 
    p.id_presupuesto,
    p.numero_presupuesto,
    p.nombre_evento_presupuesto,
    p.version_actual_presupuesto,
    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
    p.activo_presupuesto
FROM presupuesto p
INNER JOIN cliente c ON p.id_cliente = c.id_cliente
WHERE p.id_presupuesto = @id_presupuesto_prueba;

SELECT '═══════════════════════════════════════' AS '';
SELECT '📝 VERSIÓN CREADA' AS '';

SELECT 
    id_version_presupuesto,
    numero_version_presupuesto,
    estado_version_presupuesto,
    motivo_modificacion_version,
    fecha_creacion_version,
    activo_version
FROM presupuesto_version
WHERE id_presupuesto = @id_presupuesto_prueba;

-- ============================================================
-- URL para acceder
-- ============================================================

SELECT '═══════════════════════════════════════' AS '';
SELECT '🌐 URL PARA PROBAR' AS '';

SELECT CONCAT(
    'http://localhost/MDR/view/lineasPresupuesto/index.php?id_version_presupuesto=',
    @id_version_prueba
) AS '🔗 Abrir esta URL en el navegador';

-- ============================================================
-- IMPORTANTE: Restaurar triggers después de las pruebas
-- ============================================================

SELECT '═══════════════════════════════════════' AS '';
SELECT '⚠️ IMPORTANTE' AS '';
SELECT 'Después de las pruebas, ejecuta:' AS '';
SELECT 'SOURCE w:/MDR/docs/triggers_sistema_versiones.sql;' AS 'Comando';
SELECT 'Para restaurar los triggers desactivados' AS 'Nota';

-- ============================================================
-- SCRIPT PARA RESTAURAR TRIGGERS
-- ============================================================
/*

Para restaurar el trigger desactivado, ejecuta esto:

SOURCE w:/MDR/docs/triggers_sistema_versiones.sql;

O manualmente:

DELIMITER $$

CREATE TRIGGER trg_presupuesto_after_insert
AFTER INSERT ON presupuesto
FOR EACH ROW
BEGIN
    -- Crear automáticamente la versión 1
    INSERT INTO presupuesto_version (
        id_presupuesto,
        numero_version_presupuesto,
        version_padre_presupuesto,
        estado_version_presupuesto,
        motivo_modificacion_version,
        fecha_creacion_version,
        creado_por_version
    ) VALUES (
        NEW.id_presupuesto,
        1,
        NULL,
        'borrador',
        'Versión inicial creada automáticamente',
        NOW(),
        1
    );
END$$

DELIMITER ;

*/
