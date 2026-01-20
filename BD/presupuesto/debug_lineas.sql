-- ========================================================
-- DEBUG: Verificar líneas de presupuesto
-- ========================================================
-- Fecha: 2025-01-23
-- Propósito: Diagnosticar por qué no carga líneas
-- ========================================================

-- 1️⃣ Verificar que existe la vista
SHOW TABLES LIKE 'v_linea_presupuesto_calculada';

-- 2️⃣ Ver estructura de la vista
DESCRIBE v_linea_presupuesto_calculada;

-- 3️⃣ Verificar datos de tu presupuesto
SELECT 
    p.id_presupuesto,
    p.numero_presupuesto,
    pv.id_version_presupuesto,
    pv.numero_version_presupuesto
FROM presupuesto p
INNER JOIN presupuesto_version pv ON p.id_presupuesto = pv.id_presupuesto
WHERE p.id_presupuesto = 10;

-- 4️⃣ Verificar si existen líneas en la tabla
SELECT COUNT(*) AS total_lineas
FROM linea_presupuesto
WHERE id_version_presupuesto = 2;

-- 5️⃣ Ver líneas directamente de la tabla
SELECT 
    id_linea_ppto,
    id_version_presupuesto,
    tipo_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    activo_linea_ppto
FROM linea_presupuesto
WHERE id_version_presupuesto = 2;

-- 6️⃣ Probar la vista directamente
SELECT * 
FROM v_linea_presupuesto_calculada 
WHERE id_version_presupuesto = 2;

-- 7️⃣ Si no existe la vista, crearla
-- (Busca en BD/presupuesto/ el script de creación de vistas)

-- ========================================================
-- NOTA: Ejecuta estos queries paso a paso para identificar
-- dónde está el problema:
-- - Si el presupuesto/versión existe ✓
-- - Si existen líneas en la tabla ✗ (probablemente NO hay líneas todavía)
-- - Si la vista existe y funciona ✓
-- ========================================================
