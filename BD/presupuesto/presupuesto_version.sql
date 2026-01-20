-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql:3306
-- Tiempo de generación: 15-01-2026 a las 18:48:06
-- Versión del servidor: 9.3.0
-- Versión de PHP: 8.2.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de datos: `toldos_db`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto_version`
--

CREATE TABLE `presupuesto_version` (
  `id_version_presupuesto` int UNSIGNED NOT NULL,
  `id_presupuesto` int UNSIGNED NOT NULL COMMENT 'FK a presupuesto',
  `numero_version_presupuesto` int UNSIGNED NOT NULL COMMENT 'Número secuencial de versión (1, 2, 3...)',
  `version_padre_presupuesto` int UNSIGNED DEFAULT NULL COMMENT 'ID de la versión anterior (NULL si es la versión original)',
  `estado_version_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'borrador' COMMENT 'Estado específico de esta versión',
  `motivo_modificacion_version` text COLLATE utf8mb4_spanish_ci COMMENT 'Razón por la que se creó esta versión',
  `fecha_creacion_version` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación de esta versión',
  `creado_por_version` int UNSIGNED NOT NULL COMMENT 'ID del usuario que creó esta versión',
  `fecha_envio_version` datetime DEFAULT NULL COMMENT 'Fecha de envío al cliente',
  `enviado_por_version` int UNSIGNED DEFAULT NULL COMMENT 'ID del usuario que envió esta versión',
  `fecha_aprobacion_version` datetime DEFAULT NULL COMMENT 'Fecha en que el cliente aprobó esta versión',
  `fecha_rechazo_version` datetime DEFAULT NULL COMMENT 'Fecha en que el cliente rechazó esta versión',
  `motivo_rechazo_version` text COLLATE utf8mb4_spanish_ci COMMENT 'Motivo del rechazo del cliente',
  `ruta_pdf_version` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT 'Ruta del archivo PDF generado para esta versión',
  `activo_version` tinyint(1) DEFAULT '1',
  `created_at_version` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_version` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Tabla de control de versiones de presupuestos. Cada registro representa una versión específica con su historial completo de cambios y estados';

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `presupuesto_version`
--
ALTER TABLE `presupuesto_version`
  ADD PRIMARY KEY (`id_version_presupuesto`),
  ADD KEY `idx_id_presupuesto_version` (`id_presupuesto`),
  ADD KEY `idx_numero_version` (`numero_version_presupuesto`),
  ADD KEY `idx_version_padre` (`version_padre_presupuesto`),
  ADD KEY `idx_estado_version` (`estado_version_presupuesto`),
  ADD KEY `idx_fecha_creacion_version` (`fecha_creacion_version`),
  ADD KEY `idx_fecha_envio_version` (`fecha_envio_version`),
  ADD KEY `idx_presupuesto_numero_version` (`id_presupuesto`,`numero_version_presupuesto`),
  ADD KEY `idx_creado_por` (`creado_por_version`),
  ADD KEY `idx_enviado_por` (`enviado_por_version`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `presupuesto_version`
--
ALTER TABLE `presupuesto_version`
  MODIFY `id_version_presupuesto` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `presupuesto_version`
--
ALTER TABLE `presupuesto_version`
  ADD CONSTRAINT `fk_version_padre` FOREIGN KEY (`version_padre_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_version_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
