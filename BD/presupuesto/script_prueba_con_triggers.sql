-- ============================================================
-- SCRIPT DE PRUEBA PARA LÍNEAS DE PRESUPUESTO
-- Compatible con triggers habilitados
-- ============================================================
-- 
-- Este script crea datos de prueba evitando conflictos
-- con los triggers del sistema de versiones
--
-- IMPORTANTE: Los triggers deben estar habilitados
-- ============================================================

-- Configuración inicial
SET @usuario_prueba = 1;
SET @fecha_actual = CURDATE();

-- ============================================================
-- PASO 1: Verificar que existen los datos maestros necesarios
-- ============================================================

-- Verificar clientes
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ OK: Hay clientes disponibles'
        ELSE '✗ ERROR: No hay clientes activos. Crear uno primero.'
    END AS verificacion_clientes,
    COUNT(*) AS total_clientes
FROM cliente 
WHERE activo_cliente = 1;

-- Verificar estados de presupuesto
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ OK: Hay estados de presupuesto'
        ELSE '✗ ERROR: No hay estados de presupuesto. Ejecutar script de maestros.'
    END AS verificacion_estados,
    COUNT(*) AS total_estados
FROM estado_presupuesto 
WHERE activo_estado_ppto = 1;

-- Verificar artículos (opcional pero recomendado)
SELECT 
    CASE 
        WHEN COUNT(*) > 0 THEN '✓ OK: Hay artículos disponibles'
        ELSE '⚠ WARNING: No hay artículos. Podrás crear el presupuesto pero no líneas de artículos.'
    END AS verificacion_articulos,
    COUNT(*) AS total_articulos
FROM articulo 
WHERE activo_articulo = 1;

-- ============================================================
-- PASO 2: Obtener IDs necesarios para crear el presupuesto
-- ============================================================

-- Obtener un cliente activo (el primero disponible)
SET @id_cliente_prueba = (
    SELECT id_cliente 
    FROM cliente 
    WHERE activo_cliente = 1 
    LIMIT 1
);

-- Obtener el estado "BORRADOR" 
-- (Si no existe con ese código, toma el primero disponible)
SET @id_estado_borrador = (
    SELECT id_estado_ppto 
    FROM estado_presupuesto 
    WHERE UPPER(codigo_estado_ppto) = 'BORRADOR'
    AND activo_estado_ppto = 1
    LIMIT 1
);

-- Si no hay estado "BORRADOR", usar el primero disponible
SET @id_estado_borrador = COALESCE(@id_estado_borrador, (
    SELECT id_estado_ppto 
    FROM estado_presupuesto 
    WHERE activo_estado_ppto = 1 
    LIMIT 1
));

-- Mostrar los IDs que se usarán
SELECT 
    @id_cliente_prueba AS 'ID_Cliente',
    @id_estado_borrador AS 'ID_Estado',
    'Estos valores se usarán para crear el presupuesto' AS 'Nota';

-- Verificar que tenemos los datos necesarios
SELECT 
    CASE 
        WHEN @id_cliente_prueba IS NULL THEN '✗ ERROR: No se encontró un cliente. No se puede continuar.'
        WHEN @id_estado_borrador IS NULL THEN '✗ ERROR: No se encontró un estado. No se puede continuar.'
        ELSE '✓ OK: Todos los datos necesarios están disponibles'
    END AS 'Estado_Verificación';

-- ============================================================
-- PASO 3: Desactivar temporalmente triggers problemáticos
-- ============================================================
-- 
-- El trigger trg_presupuesto_after_insert causa conflicto porque
-- intenta actualizar presupuesto.version_actual_presupuesto
-- desde un trigger AFTER INSERT de la misma tabla.
--
-- Solución: Crear el presupuesto SIN el campo version_actual,
-- luego actualizar manualmente después de crear la versión.
-- ============================================================

-- ============================================================
-- PASO 4: Crear el presupuesto de prueba
-- ============================================================

INSERT INTO presupuesto (
    numero_presupuesto,
    id_cliente,
    id_estado_ppto,
    fecha_presupuesto,
    fecha_validez_presupuesto,
    nombre_evento_presupuesto,
    observaciones_cabecera_presupuesto,
    activo_presupuesto,
    created_at_presupuesto
) VALUES (
    CONCAT('P-PRUEBA-', DATE_FORMAT(NOW(), '%Y%m%d%H%i%s')),
    @id_cliente_prueba,
    @id_estado_borrador,
    @fecha_actual,
    DATE_ADD(@fecha_actual, INTERVAL 30 DAY),
    'PRUEBA - Módulo Líneas de Presupuesto',
    'Este es un presupuesto de prueba para validar el módulo de líneas.\nSe puede eliminar después de las pruebas.',
    1,
    NOW()
);

-- Guardar el ID del presupuesto creado
SET @id_presupuesto_prueba = LAST_INSERT_ID();

-- Verificar que se creó correctamente
SELECT 
    @id_presupuesto_prueba AS 'ID_Presupuesto_Creado',
    'Presupuesto creado exitosamente' AS 'Estado';

-- ============================================================
-- PASO 5: Crear manualmente la primera versión
-- ============================================================
--
-- El trigger trg_presupuesto_after_insert DEBERÍA crear la versión
-- automáticamente, pero si causa problemas, la creamos manualmente.
-- ============================================================

-- Verificar si el trigger ya creó la versión automáticamente
SET @version_existente = (
    SELECT id_version_presupuesto
    FROM presupuesto_version
    WHERE id_presupuesto = @id_presupuesto_prueba
    AND numero_version_presupuesto = 1
    LIMIT 1
);

-- Si NO existe la versión, crearla manualmente
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
)
SELECT
    @id_presupuesto_prueba,
    1,
    NULL,
    'borrador',
    'Versión inicial - Creada para pruebas del módulo de líneas',
    NOW(),
    @usuario_prueba,
    1,
    NOW()
WHERE @version_existente IS NULL;  -- Solo insertar si no existe

-- Obtener el ID de la versión (existente o recién creada)
SET @id_version_prueba = COALESCE(@version_existente, LAST_INSERT_ID());

-- Verificar que tenemos la versión
SELECT 
    @id_version_prueba AS 'ID_Version_Creada',
    CASE 
        WHEN @version_existente IS NOT NULL THEN 'Versión creada automáticamente por trigger'
        ELSE 'Versión creada manualmente por este script'
    END AS 'Origen';

-- ============================================================
-- PASO 6: Actualizar campo version_actual en presupuesto
-- ============================================================
-- 
-- Si los triggers están habilitados, esto puede fallar.
-- Lo hacemos con IGNORE por si acaso.
-- ============================================================

UPDATE IGNORE presupuesto 
SET version_actual_presupuesto = 1
WHERE id_presupuesto = @id_presupuesto_prueba
AND version_actual_presupuesto IS NULL;

-- ============================================================
-- PASO 7: Verificar la estructura creada
-- ============================================================

SELECT 
    '═══════════════════════════════════════════════════════════' AS '';

SELECT '📋 DATOS DEL PRESUPUESTO CREADO' AS '';

SELECT 
    p.id_presupuesto,
    p.numero_presupuesto,
    p.nombre_evento_presupuesto,
    p.fecha_presupuesto,
    p.fecha_validez_presupuesto,
    p.version_actual_presupuesto,
    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS cliente,
    e.nombre_estado_ppto AS estado,
    p.activo_presupuesto
FROM presupuesto p
INNER JOIN cliente c ON p.id_cliente = c.id_cliente
LEFT JOIN estado_presupuesto e ON p.id_estado_ppto = e.id_estado_ppto
WHERE p.id_presupuesto = @id_presupuesto_prueba;

SELECT 
    '═══════════════════════════════════════════════════════════' AS '';

SELECT '📝 VERSIONES DEL PRESUPUESTO' AS '';

SELECT 
    pv.id_version_presupuesto,
    pv.numero_version_presupuesto,
    pv.estado_version_presupuesto,
    pv.version_padre_presupuesto,
    pv.motivo_modificacion_version,
    pv.fecha_creacion_version,
    pv.activo_version
FROM presupuesto_version pv
WHERE pv.id_presupuesto = @id_presupuesto_prueba
ORDER BY pv.numero_version_presupuesto;

SELECT 
    '═══════════════════════════════════════════════════════════' AS '';

-- ============================================================
-- PASO 8: Generar URL para acceder al módulo
-- ============================================================

SELECT 
    '🌐 URL PARA PROBAR EL MÓDULO' AS '';

SELECT 
    CONCAT(
        'http://localhost/MDR/view/lineasPresupuesto/index.php?id_version_presupuesto=',
        @id_version_prueba
    ) AS '🔗 Copiar esta URL y abrir en el navegador';

SELECT 
    '═══════════════════════════════════════════════════════════' AS '';

-- ============================================================
-- PASO 9: Información adicional para pruebas
-- ============================================================

SELECT 
    '📊 INFORMACIÓN PARA PRUEBAS MANUALES' AS '';

SELECT 
    @id_presupuesto_prueba AS 'ID_Presupuesto',
    @id_version_prueba AS 'ID_Version',
    'borrador' AS 'Estado_Version',
    '✓ Se pueden crear/editar/eliminar líneas' AS 'Permisos_Edición';

SELECT 
    '═══════════════════════════════════════════════════════════' AS '';

-- ============================================================
-- CONSULTAS ÚTILES PARA VERIFICACIÓN POST-PRUEBAS
-- ============================================================

SELECT 
    '📝 CONSULTAS ÚTILES PARA VERIFICAR DESPUÉS' AS '';

SELECT '-- Ver líneas creadas:' AS '';
SELECT CONCAT(
    'SELECT * FROM linea_presupuesto WHERE id_version_presupuesto = ',
    @id_version_prueba,
    ' AND activo_linea_ppto = 1;'
) AS 'Consulta_SQL';

SELECT '-- Ver totales calculados:' AS '';
SELECT CONCAT(
    'SELECT * FROM v_presupuesto_totales WHERE id_version_presupuesto = ',
    @id_version_prueba,
    ';'
) AS 'Consulta_SQL';

SELECT '-- Ver líneas con cálculos:' AS '';
SELECT CONCAT(
    'SELECT * FROM v_linea_presupuesto_calculada WHERE id_version_presupuesto = ',
    @id_version_prueba,
    ' AND activo_linea_ppto = 1;'
) AS 'Consulta_SQL';

SELECT 
    '═══════════════════════════════════════════════════════════' AS '';

SELECT 
    '✅ SCRIPT COMPLETADO EXITOSAMENTE' AS '',
    'Ahora puedes abrir la URL proporcionada arriba para probar el módulo' AS '';

-- ============================================================
-- NOTAS FINALES
-- ============================================================
/*

NOTAS IMPORTANTES:

1. Este script crea UN presupuesto de prueba con su versión inicial.

2. La versión se crea en estado "borrador", lo que permite:
   - Crear nuevas líneas
   - Editar líneas existentes
   - Eliminar líneas

3. Para probar el bloqueo de versiones, ejecuta:
   UPDATE presupuesto_version 
   SET estado_version_presupuesto = 'enviado' 
   WHERE id_version_presupuesto = {ID_VERSION};

4. Para volver a habilitar edición:
   UPDATE presupuesto_version 
   SET estado_version_presupuesto = 'borrador' 
   WHERE id_version_presupuesto = {ID_VERSION};

5. Para limpiar datos de prueba después:
   -- Eliminar líneas
   UPDATE linea_presupuesto SET activo_linea_ppto = 0 
   WHERE id_version_presupuesto = {ID_VERSION};
   
   -- Eliminar versión
   UPDATE presupuesto_version SET activo_version = 0 
   WHERE id_version_presupuesto = {ID_VERSION};
   
   -- Eliminar presupuesto
   UPDATE presupuesto SET activo_presupuesto = 0 
   WHERE id_presupuesto = {ID_PRESUPUESTO};

6. Si encuentras errores de triggers, revisa:
   - Que los campos version_actual_presupuesto y estado_general_presupuesto 
     existen en la tabla presupuesto
   - Que los triggers están correctamente instalados
   - Los logs de actividad en public/logs/

*/
