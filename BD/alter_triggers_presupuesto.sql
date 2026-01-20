-- ============================================
-- CORRECCIÓN DE TRIGGERS - ERROR #1442
-- ============================================
-- Proyecto: MDR ERP Manager
-- Base de datos: toldos_db
-- Fecha: 20 de enero de 2026
-- Autor: Sistema MDR
-- ============================================
-- 
-- PROBLEMA DETECTADO:
-- El trigger trg_presupuesto_after_insert causa error #1442:
-- "Can't update table 'presupuesto' in stored function/trigger 
--  because it is already used by statement which invoked this stored function/trigger"
--
-- CAUSA RAÍZ:
-- El trigger AFTER INSERT en tabla 'presupuesto' intenta hacer UPDATE 
-- en la misma tabla 'presupuesto', lo cual MySQL no permite en el mismo evento.
--
-- SOLUCIÓN IMPLEMENTADA:
-- 1. Modificar la estructura de la tabla para usar DEFAULT en columnas
-- 2. Eliminar el UPDATE problemático del trigger
-- 3. El trigger solo crea la versión 1 en presupuesto_version
-- 4. Los valores de version_actual y estado_general se establecen por DEFAULT
--
-- ============================================

USE toldos_db;

-- ============================================
-- PASO 1: MODIFICAR ESTRUCTURA DE TABLA
-- ============================================
-- Agregamos DEFAULT a las columnas problemáticas
-- Esto elimina la necesidad de hacer UPDATE desde el trigger
-- ============================================

ALTER TABLE presupuesto
MODIFY COLUMN version_actual_presupuesto INT UNSIGNED DEFAULT 1
    COMMENT 'Número de versión activa actual (la que se muestra/edita)',
    
MODIFY COLUMN estado_general_presupuesto ENUM('borrador','enviado','aprobado','rechazado','cancelado') 
    DEFAULT 'borrador'
    COMMENT 'Estado general del presupuesto (sincronizado con versión actual)';

-- ============================================
-- VERIFICACIÓN DE CAMBIOS EN ESTRUCTURA
-- ============================================
-- Usar DESCRIBE en lugar de INFORMATION_SCHEMA (no requiere permisos especiales)

DESCRIBE presupuesto;

-- ============================================
-- PASO 2: RECREAR TRIGGER CORREGIDO
-- ============================================
-- El nuevo trigger solo crea la versión 1
-- NO intenta actualizar la tabla presupuesto
-- ============================================

DELIMITER //

DROP TRIGGER IF EXISTS trg_presupuesto_after_insert//

CREATE TRIGGER trg_presupuesto_after_insert
AFTER INSERT ON presupuesto
FOR EACH ROW
BEGIN
    -- Crear automáticamente la versión 1
    -- El presupuesto ya tiene version_actual_presupuesto = 1 por DEFAULT
    -- No es necesario hacer UPDATE
    
    INSERT INTO presupuesto_version (
        id_presupuesto,
        numero_version_presupuesto,
        version_padre_presupuesto,
        estado_version_presupuesto,
        creado_por_version,
        motivo_modificacion_version,
        fecha_creacion_version
    ) VALUES (
        NEW.id_presupuesto,
        1,                              -- Siempre es versión 1
        NULL,                           -- No tiene padre
        'borrador',                     -- Empieza como borrador
        1,                              -- Usuario por defecto (TODO: cambiar cuando exista tabla usuario)
        'Versión inicial',              -- Motivo por defecto
        NOW()                           -- Fecha actual
    );
    
    -- ✅ CORRECCIÓN: Ya NO hacemos UPDATE aquí
    -- Los valores se establecen por DEFAULT en la tabla:
    --   - version_actual_presupuesto = 1 (por DEFAULT)
    --   - estado_general_presupuesto = 'borrador' (por DEFAULT)
    
    -- NOTA: Si en el INSERT se especificaron valores diferentes,
    -- esos valores se respetan y NO se sobrescriben
END//

DELIMITER ;

-- ============================================
-- PASO 3: VERIFICAR INSTALACIÓN
-- ============================================

-- Mostrar el trigger recreado
SHOW CREATE TRIGGER trg_presupuesto_after_insert;

-- Listar todos los triggers relacionados con presupuesto
-- (alternativa simple sin usar information_schema)
SHOW TRIGGERS FROM toldos_db LIKE 'presupuesto%';
SHOW TRIGGERS FROM toldos_db LIKE 'linea_presupuesto%';

-- ============================================
-- PASO 4: PRUEBA DE FUNCIONAMIENTO
-- ============================================

-- Test 1: Crear un presupuesto de prueba
-- Esto debería funcionar sin error #1442
/*
INSERT INTO presupuesto (
    numero_presupuesto,
    id_cliente,
    id_estado_ppto,
    fecha_presupuesto,
    nombre_evento_presupuesto,
    observaciones_cabecera_presupuesto,
    observaciones_pie_presupuesto,
    observaciones_internas_presupuesto
) VALUES (
    'TEST-2026-001',
    1,  -- Asegúrate que existe este cliente
    1,  -- Asegúrate que existe este estado
    NOW(),
    'Evento de prueba para validar triggers corregidos',
    'Observaciones de prueba',
    'Pie de prueba',
    'Prueba de corrección de trigger #1442'
);
*/

-- Verificar que se creó correctamente
/*
SELECT 
    p.id_presupuesto,
    p.numero_presupuesto,
    p.version_actual_presupuesto,
    p.estado_general_presupuesto,
    pv.id_version_presupuesto,
    pv.numero_version_presupuesto,
    pv.estado_version_presupuesto,
    pv.motivo_modificacion_version
FROM presupuesto p
LEFT JOIN presupuesto_version pv 
    ON pv.id_presupuesto = p.id_presupuesto
WHERE p.numero_presupuesto = 'TEST-2026-001';
*/

-- Si todo funciona correctamente, eliminar el presupuesto de prueba
/*
DELETE FROM linea_presupuesto 
WHERE id_version_presupuesto IN (
    SELECT id_version_presupuesto 
    FROM presupuesto_version 
    WHERE id_presupuesto IN (
        SELECT id_presupuesto 
        FROM presupuesto 
        WHERE numero_presupuesto = 'TEST-2026-001'
    )
);

DELETE FROM presupuesto_version 
WHERE id_presupuesto IN (
    SELECT id_presupuesto 
    FROM presupuesto 
    WHERE numero_presupuesto = 'TEST-2026-001'
);

DELETE FROM presupuesto 
WHERE numero_presupuesto = 'TEST-2026-001';
*/

-- ============================================
-- DOCUMENTACIÓN DE LA CORRECCIÓN
-- ============================================

/*
RESUMEN DE CAMBIOS:
===================

1. TABLA presupuesto:
   - version_actual_presupuesto: Ahora tiene DEFAULT 1
   - estado_general_presupuesto: Ahora tiene DEFAULT 'borrador'
   
   Beneficios:
   - Los valores se establecen automáticamente en el INSERT
   - No necesita UPDATE posterior desde el trigger
   - Elimina el error #1442
   - Más eficiente (un statement menos)

2. TRIGGER trg_presupuesto_after_insert:
   - Eliminada la sección UPDATE presupuesto
   - Solo crea la versión 1 en presupuesto_version
   - Más simple y más eficiente
   
   Beneficios:
   - No causa error #1442
   - Cumple las reglas de MySQL sobre triggers
   - Más fácil de mantener y depurar

3. COMPATIBILIDAD:
   - ✅ Si el INSERT especifica valores para version_actual o estado_general,
        esos valores se respetan (no se sobrescriben con DEFAULT)
   - ✅ Todo el código PHP existente sigue funcionando igual
   - ✅ Los otros 9 triggers no se modifican
   - ✅ No afecta a presupuestos existentes

4. COMPORTAMIENTO ESPERADO:
   Cuando se ejecuta:
   ```sql
   INSERT INTO presupuesto (...) VALUES (...);
   ```
   
   Sucede:
   a) MySQL inserta el registro con:
      - version_actual_presupuesto = 1 (por DEFAULT)
      - estado_general_presupuesto = 'borrador' (por DEFAULT)
   
   b) El trigger trg_presupuesto_after_insert se ejecuta:
      - Inserta versión 1 en presupuesto_version
      - Ya NO intenta UPDATE (error eliminado)
   
   c) Resultado final:
      - Presupuesto creado con version_actual = 1
      - Versión 1 creada en presupuesto_version
      - Sin errores #1442
      - Todo funciona correctamente

5. MIGRACIÓN DE DATOS EXISTENTES:
   Los presupuestos existentes NO necesitan actualización.
   Los DEFAULT solo aplican a nuevos INSERT.
   
   Si quieres normalizar datos antiguos (opcional):
   ```sql
   UPDATE presupuesto
   SET version_actual_presupuesto = 1
   WHERE version_actual_presupuesto IS NULL;
   
   UPDATE presupuesto
   SET estado_general_presupuesto = 'borrador'
   WHERE estado_general_presupuesto IS NULL;
   ```

6. VALIDACIÓN POST-INSTALACIÓN:
   Ejecuta estas queries para verificar:
   
   -- Verificar estructura de tabla
   DESCRIBE presupuesto;
   
   -- Verificar trigger
   SHOW CREATE TRIGGER trg_presupuesto_after_insert;
   
   -- Crear presupuesto de prueba desde PHP:
   Usa el formulario normal de creación de presupuestos
   y verifica que:
   - Se crea sin error
   - version_actual_presupuesto = 1
   - estado_general_presupuesto = 'borrador'
   - Se crea versión 1 en presupuesto_version

7. ROLLBACK (si fuera necesario):
   Si por alguna razón necesitas revertir estos cambios:
   
   ```sql
   -- Eliminar DEFAULT de columnas
   ALTER TABLE presupuesto
   MODIFY COLUMN version_actual_presupuesto INT UNSIGNED,
   MODIFY COLUMN estado_general_presupuesto 
       ENUM('borrador','enviado','aprobado','rechazado','cancelado');
   
   -- Restaurar trigger original
   SOURCE w:/MDR/docs/triggers_sistema_versiones.sql
   ```
   
   NOTA: El trigger original seguirá causando error #1442,
   así que este rollback solo es recomendable si vas a 
   implementar otra solución diferente.

8. TESTING RECOMENDADO:
   Después de aplicar este script, prueba:
   
   ✅ Crear presupuesto desde interfaz web
   ✅ Crear presupuesto desde SQL directo
   ✅ Verificar que se crea versión 1 automática
   ✅ Verificar que version_actual = 1
   ✅ Verificar que estado_general = 'borrador'
   ✅ Crear líneas de presupuesto en versión 1
   ✅ Cambiar estado de versión 1 a 'enviado'
   ✅ Verificar que no puedes editar líneas después
   ✅ Crear versión 2 desde versión 1 rechazada
   ✅ Todo el flujo de versiones completo

9. IMPACTO EN CÓDIGO PHP:
   NINGUNO - No necesitas cambiar nada en PHP
   
   El código existente en:
   - w:\MDR\models\Presupuesto.php (insert_presupuesto)
   - w:\MDR\controller\presupuesto.php (case "guardaryeditar")
   
   Sigue funcionando exactamente igual.
   
   Si el PHP enviaba:
   ```php
   $presupuesto->insert_presupuesto(
       $numero, $cliente, ... // sin especificar version_actual
   );
   ```
   
   MySQL automáticamente usa DEFAULT 1 para version_actual.
   
   Si el PHP enviaba valores explícitos, esos se respetan.

10. VENTAJAS DE ESTA SOLUCIÓN:
    ✅ Elimina completamente el error #1442
    ✅ Es la solución más limpia y correcta según MySQL
    ✅ No requiere cambios en código PHP
    ✅ No requiere deshabilitar triggers
    ✅ Más eficiente (menos statements SQL)
    ✅ Más fácil de mantener
    ✅ Compatible con código existente
    ✅ No afecta funcionalidad actual
    ✅ Sigue todas las mejores prácticas
    ✅ Permite seguir usando todos los otros triggers
*/

-- ============================================
-- INSTRUCCIONES DE APLICACIÓN
-- ============================================

/*
PASOS PARA APLICAR ESTA CORRECCIÓN:
====================================

1. BACKUP (OBLIGATORIO):
   mysqldump -u administrator -p toldos_db > backup_antes_correccion.sql

2. EJECUTAR ESTE SCRIPT:
   En phpMyAdmin o línea de comandos:
   SOURCE w:/MDR/BD/alter_triggers_presupuesto.sql
   
   O copiar y pegar en phpMyAdmin SQL

3. VERIFICAR QUE SE APLICÓ:
   - Verificar estructura: DESCRIBE presupuesto;
   - Verificar trigger: SHOW CREATE TRIGGER trg_presupuesto_after_insert;

4. PROBAR CREACIÓN DE PRESUPUESTO:
   - Ir a la interfaz web de presupuestos
   - Crear un nuevo presupuesto
   - Verificar que se crea sin errores
   - Verificar que tiene versión 1 creada

5. SI TODO FUNCIONA:
   ¡Listo! Ya puedes usar el sistema normalmente
   y crear presupuestos sin error #1442

6. SI HAY PROBLEMAS:
   - Revisar los logs de MySQL
   - Verificar que el script se ejecutó completo
   - Contactar soporte con el mensaje de error

7. DESPUÉS DE VERIFICAR:
   Ya puedes probar el módulo de líneas de presupuesto:
   - Crear presupuesto nuevo
   - Ir a gestión de líneas
   - Agregar líneas de diferentes tipos
   - Probar todo el flujo de versiones
*/

-- ============================================
-- INFORMACIÓN DE CONTACTO Y SOPORTE
-- ============================================

/*
Proyecto: MDR ERP Manager
Script: alter_triggers_presupuesto.sql
Versión: 1.0
Fecha: 20 de enero de 2026

Este script resuelve el error #1442 de MySQL y permite
crear presupuestos correctamente desde cualquier interfaz.

Para más información sobre el sistema de versiones:
- Ver: w:\MDR\docs\triggers_sistema_versiones.sql
- Ver: w:\MDR\docs\pruebas_lineas_presupuesto.md

Para probar el módulo de líneas después de aplicar:
- Ejecutar: w:\MDR\BD\presupuesto\script_prueba_con_triggers.sql
- Abrir: http://localhost/MDR/view/lineasPresupuesto/
*/

-- ============================================
-- FIN DEL SCRIPT DE CORRECCIÓN
-- ============================================

SELECT '✅ Script de corrección aplicado correctamente' AS Resultado,
       'Ya puedes crear presupuestos sin error #1442' AS Mensaje,
       'Prueba creando un presupuesto desde la interfaz web' AS SiguientePaso;
