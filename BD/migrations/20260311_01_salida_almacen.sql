-- ============================================================
-- Migración: Salida de Almacén para Técnicos (Picking Móvil)
-- Fecha: 2026-03-11
-- ============================================================

-- 1. Nuevo estado de elemento: En preparación
INSERT INTO estado_elemento (codigo_estado_elemento, descripcion_estado_elemento, color_estado_elemento, permite_alquiler_estado_elemento, activo_estado_elemento)
VALUES ('PREP', 'En preparación', '#795548', 0, 1);

-- 2. Tabla salida_almacen (cabecera de cada operación de picking)
CREATE TABLE `salida_almacen` (
  `id_salida_almacen` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_presupuesto` INT UNSIGNED NOT NULL,
  `id_version_presupuesto` INT UNSIGNED NOT NULL,
  `id_usuario_salida` INT NOT NULL,
  `numero_presupuesto_salida` VARCHAR(50) NOT NULL COMMENT 'Desnormalizado para histórico',
  `estado_salida` ENUM('en_proceso','completada','cancelada') NOT NULL DEFAULT 'en_proceso',
  `fecha_inicio_salida` DATETIME DEFAULT NULL,
  `fecha_fin_salida` DATETIME DEFAULT NULL,
  `observaciones_salida` TEXT DEFAULT NULL,
  `activo_salida_almacen` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at_salida_almacen` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_salida_almacen` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_presupuesto_salida` (`id_presupuesto`),
  INDEX `idx_usuario_salida` (`id_usuario_salida`),
  INDEX `idx_estado_salida` (`estado_salida`),
  INDEX `idx_activo_salida_almacen` (`activo_salida_almacen`),
  CONSTRAINT `fk_salida_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_salida_version` FOREIGN KEY (`id_version_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Cabecera de cada operación de picking de almacén';

-- 3. Tabla linea_salida_almacen (cada elemento físico escaneado)
CREATE TABLE `linea_salida_almacen` (
  `id_linea_salida` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_salida_almacen` INT UNSIGNED NOT NULL,
  `id_elemento` INT UNSIGNED NOT NULL,
  `id_articulo` INT UNSIGNED NOT NULL,
  `id_linea_ppto` INT UNSIGNED DEFAULT NULL,
  `es_backup_linea_salida` TINYINT(1) NOT NULL DEFAULT 0 COMMENT '1 = material de backup',
  `orden_escaneo` INT UNSIGNED DEFAULT NULL,
  `fecha_escaneo_linea_salida` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones_linea_salida` TEXT DEFAULT NULL,
  `activo_linea_salida` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at_linea_salida` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_linea_salida` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_salida_linea` (`id_salida_almacen`),
  INDEX `idx_elemento_linea` (`id_elemento`),
  INDEX `idx_articulo_linea` (`id_articulo`),
  INDEX `idx_activo_linea_salida` (`activo_linea_salida`),
  CONSTRAINT `fk_linea_salida_cabecera` FOREIGN KEY (`id_salida_almacen`) REFERENCES `salida_almacen` (`id_salida_almacen`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_linea_salida_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_linea_salida_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_linea_salida_linea_ppto` FOREIGN KEY (`id_linea_ppto`) REFERENCES `linea_presupuesto` (`id_linea_ppto`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Cada elemento físico escaneado en una salida de almacén';

-- 4. Tabla movimiento_elemento_salida (historial de desplazamientos físicos)
CREATE TABLE `movimiento_elemento_salida` (
  `id_movimiento` INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `id_linea_salida` INT UNSIGNED NOT NULL,
  `id_ubicacion_origen` INT UNSIGNED DEFAULT NULL COMMENT 'NULL = primera colocación desde almacén',
  `id_ubicacion_destino` INT UNSIGNED NOT NULL,
  `id_usuario_movimiento` INT NOT NULL,
  `fecha_movimiento` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones_movimiento` TEXT DEFAULT NULL,
  `activo_movimiento` BOOLEAN NOT NULL DEFAULT TRUE,
  `created_at_movimiento` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_movimiento` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_linea_movimiento` (`id_linea_salida`),
  INDEX `idx_destino_movimiento` (`id_ubicacion_destino`),
  INDEX `idx_fecha_movimiento` (`fecha_movimiento`),
  INDEX `idx_activo_movimiento` (`activo_movimiento`),
  CONSTRAINT `fk_mov_linea_salida` FOREIGN KEY (`id_linea_salida`) REFERENCES `linea_salida_almacen` (`id_linea_salida`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_mov_ubicacion_origen` FOREIGN KEY (`id_ubicacion_origen`) REFERENCES `cliente_ubicacion` (`id_ubicacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_mov_ubicacion_destino` FOREIGN KEY (`id_ubicacion_destino`) REFERENCES `cliente_ubicacion` (`id_ubicacion`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Historial de desplazamientos físicos de elementos en un evento';

-- 5. Vista: progreso de picking por articulo
CREATE OR REPLACE VIEW `vista_progreso_salida` AS
SELECT
  sa.id_salida_almacen,
  lp.id_articulo,
  a.nombre_articulo,
  a.codigo_articulo,
  SUM(lp.cantidad_linea_ppto) AS cantidad_requerida,
  COALESCE(SUM(CASE WHEN lsa.id_linea_salida IS NOT NULL AND lsa.es_backup_linea_salida = 0 AND lsa.activo_linea_salida = 1 THEN 1 ELSE 0 END), 0) AS cantidad_escaneada,
  COALESCE(SUM(CASE WHEN lsa.id_linea_salida IS NOT NULL AND lsa.es_backup_linea_salida = 1 AND lsa.activo_linea_salida = 1 THEN 1 ELSE 0 END), 0) AS cantidad_backup,
  CASE WHEN COALESCE(SUM(CASE WHEN lsa.id_linea_salida IS NOT NULL AND lsa.es_backup_linea_salida = 0 AND lsa.activo_linea_salida = 1 THEN 1 ELSE 0 END), 0) >= SUM(lp.cantidad_linea_ppto) THEN 1 ELSE 0 END AS esta_completo,
  cu.id_ubicacion AS id_ubicacion_linea,
  cu.nombre_ubicacion AS nombre_ubicacion_linea
FROM salida_almacen sa
INNER JOIN presupuesto_version pv ON sa.id_version_presupuesto = pv.id_version_presupuesto
INNER JOIN linea_presupuesto lp ON lp.id_version_presupuesto = pv.id_version_presupuesto
  AND lp.tipo_linea_ppto IN ('articulo','kit')
  AND lp.activo_linea_ppto = 1
INNER JOIN articulo a ON lp.id_articulo = a.id_articulo
LEFT JOIN linea_salida_almacen lsa ON lsa.id_salida_almacen = sa.id_salida_almacen
  AND lsa.id_articulo = lp.id_articulo
LEFT JOIN cliente_ubicacion cu ON lp.id_ubicacion = cu.id_ubicacion
WHERE sa.activo_salida_almacen = 1
GROUP BY sa.id_salida_almacen, lp.id_articulo, a.nombre_articulo, a.codigo_articulo, cu.id_ubicacion, cu.nombre_ubicacion;

-- 6. Vista: ubicación actual de cada elemento en una salida
CREATE OR REPLACE VIEW `vista_ubicacion_actual_elemento` AS
SELECT
  lsa.id_salida_almacen,
  lsa.id_linea_salida,
  lsa.id_elemento,
  e.codigo_elemento,
  a.nombre_articulo,
  a.codigo_articulo,
  lsa.es_backup_linea_salida,
  ult.id_ubicacion_destino AS id_ubicacion_actual,
  cu_dest.nombre_ubicacion AS nombre_ubicacion_actual,
  ult.fecha_movimiento AS fecha_ultimo_movimiento,
  ult.id_usuario_movimiento
FROM linea_salida_almacen lsa
INNER JOIN elemento e ON lsa.id_elemento = e.id_elemento
INNER JOIN articulo a ON lsa.id_articulo = a.id_articulo
LEFT JOIN (
  SELECT m1.*
  FROM movimiento_elemento_salida m1
  INNER JOIN (
    SELECT id_linea_salida, MAX(fecha_movimiento) AS max_fecha
    FROM movimiento_elemento_salida
    WHERE activo_movimiento = 1
    GROUP BY id_linea_salida
  ) m2 ON m1.id_linea_salida = m2.id_linea_salida AND m1.fecha_movimiento = m2.max_fecha
  WHERE m1.activo_movimiento = 1
) ult ON lsa.id_linea_salida = ult.id_linea_salida
LEFT JOIN cliente_ubicacion cu_dest ON ult.id_ubicacion_destino = cu_dest.id_ubicacion
WHERE lsa.activo_linea_salida = 1;
