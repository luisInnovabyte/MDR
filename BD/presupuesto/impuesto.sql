-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql:3306
-- Tiempo de generación: 20-01-2026 a las 06:53:08
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
-- Estructura de tabla para la tabla `impuesto`
--

CREATE TABLE `impuesto` (
  `id_impuesto` int NOT NULL,
  `tipo_impuesto` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tasa_impuesto` decimal(5,2) NOT NULL,
  `descr_impuesto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_impuesto` tinyint(1) DEFAULT '1',
  `coeficiente_familia` tinyint DEFAULT NULL COMMENT '1 = SI APLICA, 0 = NO APLICA COEFICIENTE DE DESCUANTO',
  `created_at_impuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_impuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `impuesto`
--

INSERT INTO `impuesto` (`id_impuesto`, `tipo_impuesto`, `tasa_impuesto`, `descr_impuesto`, `activo_impuesto`, `coeficiente_familia`, `created_at_impuesto`, `updated_at_impuesto`) VALUES
(1, 'IVA', 21.00, 'Es la tasa del IVA normal', 1, NULL, '2025-11-10 07:11:38', '2025-11-21 06:14:54'),
(2, 'IVA_REDUCIDO', 10.00, 'Iva reducido aplicable en España', 1, NULL, '2025-11-11 17:07:29', '2025-11-21 06:15:49'),
(3, 'SIN_IVA', 0.00, 'Aplicable a empresas intracomunitarias', 1, NULL, '2025-11-21 06:16:24', '2025-11-21 06:16:24');

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  ADD PRIMARY KEY (`id_impuesto`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  MODIFY `id_impuesto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
