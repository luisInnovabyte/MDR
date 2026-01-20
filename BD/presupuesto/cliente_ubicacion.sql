-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql:3306
-- Tiempo de generación: 15-01-2026 a las 18:51:39
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
-- Estructura de tabla para la tabla `cliente_ubicacion`
--

CREATE TABLE `cliente_ubicacion` (
  `id_ubicacion` int UNSIGNED NOT NULL,
  `id_cliente` int UNSIGNED NOT NULL COMMENT 'Cliente propietario de esta ubicación',
  `nombre_ubicacion` varchar(255) NOT NULL COMMENT 'Nombre identificativo: "Teatro Municipal", "Auditorio Central", etc.',
  `direccion_ubicacion` varchar(255) DEFAULT NULL COMMENT 'Calle, número, piso, etc.',
  `codigo_postal_ubicacion` varchar(10) DEFAULT NULL,
  `poblacion_ubicacion` varchar(100) DEFAULT NULL,
  `provincia_ubicacion` varchar(100) DEFAULT NULL,
  `pais_ubicacion` varchar(100) DEFAULT 'España' COMMENT 'País de la ubicación',
  `persona_contacto_ubicacion` varchar(255) DEFAULT NULL COMMENT 'Persona de contacto en esta ubicación específica',
  `telefono_contacto_ubicacion` varchar(50) DEFAULT NULL COMMENT 'Teléfono de contacto en la ubicación',
  `email_contacto_ubicacion` varchar(255) DEFAULT NULL COMMENT 'Email de contacto en la ubicación',
  `observaciones_ubicacion` text COMMENT 'Notas operativas: "Acceso calle trasera", "Ascensor limitado", "Horario carga 8-10h", etc.',
  `es_principal_ubicacion` tinyint(1) DEFAULT '0' COMMENT 'TRUE: Ubicación por defecto del cliente | FALSE: Ubicación secundaria',
  `activo_ubicacion` tinyint(1) DEFAULT '1' COMMENT 'TRUE: Ubicación activa | FALSE: Ubicación desactivada',
  `created_at_ubicacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_ubicacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Ubicaciones habituales de eventos para cada cliente';

--
-- Volcado de datos para la tabla `cliente_ubicacion`
--

INSERT INTO `cliente_ubicacion` (`id_ubicacion`, `id_cliente`, `nombre_ubicacion`, `direccion_ubicacion`, `codigo_postal_ubicacion`, `poblacion_ubicacion`, `provincia_ubicacion`, `pais_ubicacion`, `persona_contacto_ubicacion`, `telefono_contacto_ubicacion`, `email_contacto_ubicacion`, `observaciones_ubicacion`, `es_principal_ubicacion`, `activo_ubicacion`, `created_at_ubicacion`, `updated_at_ubicacion`) VALUES
(1, 4, 'Oficina Central', 'C/ rio Amadorio ,4', '03013', 'Alicante', 'Alicante', 'España', '', '', '', 'Prueba de ubicación principal', 1, 1, '2025-12-19 17:19:31', '2025-12-19 17:21:19');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `cliente_ubicacion`
--
ALTER TABLE `cliente_ubicacion`
  ADD PRIMARY KEY (`id_ubicacion`),
  ADD KEY `idx_id_cliente_ubicacion` (`id_cliente`),
  ADD KEY `idx_nombre_ubicacion` (`nombre_ubicacion`),
  ADD KEY `idx_poblacion_ubicacion` (`poblacion_ubicacion`),
  ADD KEY `idx_provincia_ubicacion` (`provincia_ubicacion`),
  ADD KEY `idx_es_principal_ubicacion` (`es_principal_ubicacion`),
  ADD KEY `idx_activo_ubicacion` (`activo_ubicacion`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `cliente_ubicacion`
--
ALTER TABLE `cliente_ubicacion`
  MODIFY `id_ubicacion` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `cliente_ubicacion`
--
ALTER TABLE `cliente_ubicacion`
  ADD CONSTRAINT `fk_ubicacion_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
