-- ========================================================
-- DEBUG: Verificar datos del presupuesto y cliente
-- ========================================================

-- 1️⃣ Ver qué datos tiene tu presupuesto
SELECT 
    p.id_presupuesto,
    p.numero_presupuesto,
    p.nombre_evento_presupuesto,
    p.id_cliente,
    pv.id_version_presupuesto,
    pv.numero_version_presupuesto,
    pv.estado_version_presupuesto
FROM presupuesto p
INNER JOIN presupuesto_version pv ON p.id_presupuesto = pv.id_presupuesto
WHERE p.id_presupuesto = 10;

-- 2️⃣ Ver datos del cliente
SELECT * FROM cliente WHERE id_cliente = (
    SELECT id_cliente FROM presupuesto WHERE id_presupuesto = 10
);

-- 3️⃣ Probar la consulta exacta del modelo
SELECT 
    pv.id_version_presupuesto,
    pv.numero_version_presupuesto,
    pv.estado_version_presupuesto,
    p.numero_presupuesto,
    p.nombre_evento_presupuesto,
    c.id_cliente,
    c.nombre_cliente,
    c.apellido_cliente,
    CONCAT(c.nombre_cliente, ' ', c.apellido_cliente) AS nombre_completo_cliente
FROM presupuesto_version pv
INNER JOIN presupuesto p ON pv.id_presupuesto = p.id_presupuesto
INNER JOIN cliente c ON p.id_cliente = c.id_cliente
WHERE pv.id_version_presupuesto = 2
AND pv.activo_version = 1;

-- 4️⃣ Verificar estructura de cliente
DESCRIBE cliente;
