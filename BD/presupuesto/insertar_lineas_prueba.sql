-- ============================================================
-- INSERTAR LÍNEAS DE PRUEBA EN PRESUPUESTO EXISTENTE
-- ============================================================
-- Presupuesto ID: 10
-- Versión ID: 2 (Versión 1 del presupuesto)
-- ============================================================

USE toldos_db;

-- 1️⃣ Verificar el presupuesto y versión
SELECT 
    p.id_presupuesto,
    p.numero_presupuesto,
    pv.id_version_presupuesto,
    pv.numero_version_presupuesto,
    pv.estado_version_presupuesto,
    c.nombre_cliente
FROM presupuesto p
INNER JOIN presupuesto_version pv ON p.id_presupuesto = pv.id_presupuesto
INNER JOIN cliente c ON p.id_cliente = c.id_cliente
WHERE p.id_presupuesto = 10;

-- 2️⃣ Verificar si existe algún artículo activo
SELECT 
    id_articulo,
    codigo_articulo,
    nombre_articulo,
    precio_articulo
FROM articulo 
WHERE activo_articulo = 1
LIMIT 5;

-- 3️⃣ Verificar si existe algún impuesto (para IVA)
SELECT * FROM impuesto WHERE activo_impuesto = 1;

-- ============================================================
-- INSERTAR LÍNEAS DE PRUEBA
-- ============================================================

-- LÍNEA 1: Tipo texto (sección/encabezado)
INSERT INTO linea_presupuesto (
    id_version_presupuesto,
    numero_linea_ppto,
    orden_linea_ppto,
    tipo_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    descuento_linea_ppto,
    porcentaje_iva_linea_ppto,
    activo_linea_ppto,
    created_at_linea_ppto
) VALUES (
    2,                                  -- id_version_presupuesto
    1,                                  -- numero_linea_ppto
    1,                                  -- orden_linea_ppto
    'texto',                            -- tipo_linea_ppto (texto/sección)
    '=== EQUIPOS DE SONIDO ===',       -- descripcion_linea_ppto
    0,                                  -- cantidad_linea_ppto (0 para textos)
    0.00,                               -- precio_unitario_linea_ppto
    0.00,                               -- descuento_linea_ppto
    0.00,                               -- porcentaje_iva_linea_ppto (sin IVA para textos)
    1,                                  -- activo_linea_ppto
    NOW()                               -- created_at_linea_ppto
);

-- LÍNEA 2: Artículo con precio
INSERT INTO linea_presupuesto (
    id_version_presupuesto,
    numero_linea_ppto,
    orden_linea_ppto,
    tipo_linea_ppto,
    codigo_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    descuento_linea_ppto,
    porcentaje_iva_linea_ppto,
    activo_linea_ppto,
    created_at_linea_ppto
) VALUES (
    2,                                  -- id_version_presupuesto
    2,                                  -- numero_linea_ppto
    2,                                  -- orden_linea_ppto
    'articulo',                         -- tipo_linea_ppto
    'ART-001',                          -- codigo_linea_ppto
    'Mesa de mezclas digital 16 canales', -- descripcion_linea_ppto
    2,                                  -- cantidad_linea_ppto
    450.00,                             -- precio_unitario_linea_ppto
    10.00,                              -- descuento_linea_ppto (10%)
    21.00,                              -- porcentaje_iva_linea_ppto (21%)
    1,                                  -- activo_linea_ppto
    NOW()                               -- created_at_linea_ppto
);

-- LÍNEA 3: Artículo sin descuento
INSERT INTO linea_presupuesto (
    id_version_presupuesto,
    numero_linea_ppto,
    orden_linea_ppto,
    tipo_linea_ppto,
    codigo_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    descuento_linea_ppto,
    porcentaje_iva_linea_ppto,
    activo_linea_ppto,
    created_at_linea_ppto
) VALUES (
    2,                                  -- id_version_presupuesto
    3,                                  -- numero_linea_ppto
    3,                                  -- orden_linea_ppto
    'articulo',                         -- tipo_linea_ppto
    'ART-002',                          -- codigo_linea_ppto
    'Altavoces activos 1000W',          -- descripcion_linea_ppto
    4,                                  -- cantidad_linea_ppto
    350.00,                             -- precio_unitario_linea_ppto
    0.00,                               -- descuento_linea_ppto
    21.00,                              -- porcentaje_iva_linea_ppto
    1,                                  -- activo_linea_ppto
    NOW()                               -- created_at_linea_ppto
);

-- LÍNEA 4: Mano de obra
INSERT INTO linea_presupuesto (
    id_version_presupuesto,
    numero_linea_ppto,
    orden_linea_ppto,
    tipo_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    descuento_linea_ppto,
    porcentaje_iva_linea_ppto,
    jornadas_linea_ppto,
    valor_coeficiente_linea_ppto,
    activo_linea_ppto,
    created_at_linea_ppto
) VALUES (
    2,                                  -- id_version_presupuesto
    4,                                  -- numero_linea_ppto
    4,                                  -- orden_linea_ppto
    'articulo',                         -- tipo_linea_ppto
    'Técnico de sonido (por jornada)',  -- descripcion_linea_ppto
    1,                                  -- cantidad_linea_ppto
    250.00,                             -- precio_unitario_linea_ppto
    0.00,                               -- descuento_linea_ppto
    21.00,                              -- porcentaje_iva_linea_ppto
    3,                                  -- jornadas_linea_ppto (3 días)
    3.00,                               -- valor_coeficiente_linea_ppto (multiplica por 3)
    1,                                  -- activo_linea_ppto
    NOW()                               -- created_at_linea_ppto
);

-- ============================================================
-- VERIFICAR LÍNEAS INSERTADAS
-- ============================================================

-- Ver líneas insertadas
SELECT 
    id_linea_ppto,
    numero_linea_ppto,
    orden_linea_ppto,
    tipo_linea_ppto,
    codigo_linea_ppto,
    descripcion_linea_ppto,
    cantidad_linea_ppto,
    precio_unitario_linea_ppto,
    descuento_linea_ppto,
    porcentaje_iva_linea_ppto,
    jornadas_linea_ppto,
    valor_coeficiente_linea_ppto
FROM linea_presupuesto
WHERE id_version_presupuesto = 2
ORDER BY orden_linea_ppto;

-- Ver con cálculos (usando la vista)
SELECT * 
FROM v_linea_presupuesto_calculada
WHERE id_version_presupuesto = 2
ORDER BY orden_linea_ppto;

-- Ver totales
SELECT * 
FROM v_presupuesto_totales
WHERE id_version_presupuesto = 2;

-- ============================================================
-- NOTAS IMPORTANTES
-- ============================================================
-- 
-- Cálculos automáticos de la vista:
-- 
-- LÍNEA 1 (texto): No suma en totales
--   Base: 0.00
--   IVA: 0.00
--   Total: 0.00
--
-- LÍNEA 2 (mesa mezclas):
--   Subtotal: 2 × 450.00 = 900.00
--   Descuento 10%: 900.00 × 0.90 = 810.00
--   IVA 21%: 810.00 × 0.21 = 170.10
--   Total: 810.00 + 170.10 = 980.10
--
-- LÍNEA 3 (altavoces):
--   Subtotal: 4 × 350.00 = 1,400.00
--   Descuento: 0%
--   IVA 21%: 1,400.00 × 0.21 = 294.00
--   Total: 1,400.00 + 294.00 = 1,694.00
--
-- LÍNEA 4 (técnico con coeficiente):
--   Subtotal: 1 × 250.00 = 250.00
--   Coeficiente ×3: 250.00 × 3 = 750.00
--   IVA 21%: 750.00 × 0.21 = 157.50
--   Total: 750.00 + 157.50 = 907.50
--
-- TOTAL PRESUPUESTO:
--   Base: 810 + 1,400 + 750 = 2,960.00 €
--   IVA: 170.10 + 294 + 157.50 = 621.60 €
--   TOTAL: 2,960 + 621.60 = 3,581.60 €
--
-- ============================================================
