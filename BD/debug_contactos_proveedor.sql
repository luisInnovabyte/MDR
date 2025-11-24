-- Verificación directa de la relación entre proveedor y contactos

-- 1. Ver contactos del proveedor ID 1
SELECT 'CONTACTOS DEL PROVEEDOR ID 1:' AS Info;
SELECT * FROM contacto_proveedor WHERE id_proveedor = 1;

-- 2. Ver lo que devuelve la vista para ese proveedor
SELECT 'VISTA PARA PROVEEDOR ID 1:' AS Info;
SELECT 
    id_proveedor,
    codigo_proveedor,
    nombre_proveedor,
    cantidad_contactos
FROM contacto_cantidad_proveedor 
WHERE id_proveedor = 1;

-- 3. Hacer el cálculo manualmente
SELECT 'CALCULO MANUAL:' AS Info;
SELECT 
    p.id_proveedor,
    p.codigo_proveedor,
    p.nombre_proveedor,
    COUNT(cp.id_contacto_proveedor) AS cantidad_contactos_manual
FROM proveedor p
LEFT JOIN contacto_proveedor cp ON p.id_proveedor = cp.id_proveedor
WHERE p.id_proveedor = 1
GROUP BY p.id_proveedor, p.codigo_proveedor, p.nombre_proveedor;

-- 4. Ver TODOS los contactos con sus proveedores
SELECT 'TODOS LOS CONTACTOS:' AS Info;
SELECT 
    cp.id_contacto_proveedor,
    cp.id_proveedor,
    p.nombre_proveedor,
    cp.nombre_contacto,
    cp.email_contacto
FROM contacto_proveedor cp
LEFT JOIN proveedor p ON cp.id_proveedor = p.id_proveedor;
