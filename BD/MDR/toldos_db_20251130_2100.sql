-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql:3306
-- Tiempo de generación: 30-11-2025 a las 19:58:24
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

DELIMITER $$
--
-- Procedimientos
--
CREATE DEFINER=`administrator`@`%` PROCEDURE `desactivar_vacaciones_pasadas` ()   BEGIN
    UPDATE com_vacaciones
    SET activo_vacacion = 0
    WHERE fecha_fin < CURDATE()
    AND activo_vacacion = 1
    AND id_vacacion IS NOT NULL; 
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `adjunto_llamada`
--

CREATE TABLE `adjunto_llamada` (
  `id_adjunto` int NOT NULL,
  `id_llamada` int NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint DEFAULT '1' COMMENT '0=Inactivo, 1=Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `adjunto_llamada`
--

INSERT INTO `adjunto_llamada` (`id_adjunto`, `id_llamada`, `nombre_archivo`, `tipo`, `fecha_subida`, `estado`) VALUES
(57, 16, 'TOLDOS-A-MEDIDA-EN-VALENCIA.jpg', 'image/jpeg', '2025-07-14 09:26:29', 1),
(58, 10, 'media.jpg', 'image/jpeg', '2025-07-14 09:50:39', 1),
(61, 1, 'slidebg01F.jpg', 'image/jpeg', '2025-07-14 12:47:31', 1),
(62, 4, 'toldos-con-cofre-splenbox-400.jpg', 'image/jpeg', '2025-07-14 13:07:00', 1),
(63, 20, 'toldo-plano-07.jpg', 'image/jpeg', '2025-07-14 13:11:38', 1),
(64, 21, 'zagle-oddychajace-trojkat-niebieski5b55cf931e846_725x725.jpg', 'image/jpeg', '2025-07-14 13:16:58', 1),
(65, 22, 'toldos_1.jpg', 'image/jpeg', '2025-07-14 13:21:25', 1),
(66, 23, 'Toldos_Veranda-big.jpg', 'image/jpeg', '2025-07-14 13:28:08', 1),
(67, 19, 'toldos-1.jpg', 'image/jpeg', '2025-07-14 13:41:09', 1),
(68, 24, 'toldo-balcon.jpg', 'image/jpeg', '2025-07-14 13:41:17', 1),
(69, 6, 'toldos-con-cofre-splenbox-400_1.jpg', 'image/jpeg', '2025-07-14 13:43:28', 1),
(77, 4, 'toldo-balcon_1.jpg', 'image/jpeg', '2025-07-15 09:47:52', 1),
(78, 4, 'Toldos_Veranda-big_2.jpg', 'image/jpeg', '2025-07-15 09:47:52', 1),
(79, 4, 'toldos_1_1.jpg', 'image/jpeg', '2025-07-15 09:47:52', 1),
(85, 1, 'Imagen de WhatsApp 2024-12-12 a las 20.28.37_e103bb03.jpg', 'image/jpeg', '2025-10-13 12:03:40', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `articulo`
--

CREATE TABLE `articulo` (
  `id_articulo` int UNSIGNED NOT NULL,
  `id_familia` int UNSIGNED NOT NULL,
  `id_unidad` int UNSIGNED DEFAULT NULL,
  `codigo_articulo` varchar(50) NOT NULL,
  `nombre_articulo` varchar(255) NOT NULL,
  `name_articulo` varchar(255) NOT NULL,
  `imagen_articulo` varchar(255) DEFAULT NULL,
  `precio_alquiler_articulo` decimal(10,2) DEFAULT '0.00',
  `coeficiente_articulo` tinyint(1) DEFAULT NULL,
  `es_kit_articulo` tinyint(1) DEFAULT '0',
  `control_total_articulo` tinyint(1) DEFAULT '0',
  `no_facturar_articulo` tinyint(1) DEFAULT '0',
  `notas_presupuesto_articulo` text,
  `notes_budget_articulo` text,
  `orden_obs_articulo` int DEFAULT '200',
  `observaciones_articulo` text,
  `activo_articulo` tinyint(1) DEFAULT '1',
  `created_at_articulo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_articulo` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `articulo`
--

INSERT INTO `articulo` (`id_articulo`, `id_familia`, `id_unidad`, `codigo_articulo`, `nombre_articulo`, `name_articulo`, `imagen_articulo`, `precio_alquiler_articulo`, `coeficiente_articulo`, `es_kit_articulo`, `control_total_articulo`, `no_facturar_articulo`, `notas_presupuesto_articulo`, `notes_budget_articulo`, `orden_obs_articulo`, `observaciones_articulo`, `activo_articulo`, `created_at_articulo`, `updated_at_articulo`) VALUES
(21, 19, 5, 'MIC-INAL-001', 'Micrófono inalámbrico', 'Wireless Microphone', 'articulo_6924135645edd.png', 25.00, 1, 1, 1, 0, 'Incluye petaca transmisora, micrófono de mano y receptor. Requiere 2 pilas AA (no incluidas). Alcance hasta 100 metros en línea directa.', 'Includes bodypack transmitter, handheld microphone and receiver. Requires 2 AA batteries (not included). Range up to 100 meters in direct line.', 200, 'Verificar estado de baterías antes de cada alquiler. Comprobar frecuencias disponibles.', 1, '2025-11-20 20:33:47', '2025-11-25 07:01:44'),
(22, 20, 5, 'KIT-ILU-BASIC', 'Kit iluminación básica (4 PAR LED + trípodes)', 'Basic lighting kit (4 LED PAR + tripods)', 'articulos/kit_iluminacion_basica.jpg', 120.00, 0, 1, 0, 0, 'Kit completo listo para usar. Incluye 4 focos PAR LED RGBW de 54W, 4 trípodes telescópicos hasta 3m, cables DMX, controlador DMX y bolsa de transporte. Consumo total: 220W.', 'Complete plug-and-play kit. Includes 4x 54W RGBW LED PAR fixtures, 4x telescopic tripods up to 3m, DMX cables, DMX controller and transport bag. Total consumption: 220W.', 100, 'Revisar estado de LEDs y conexiones DMX antes de entregar.', 1, '2025-11-20 20:33:47', '2025-11-22 08:04:54'),
(23, 19, NULL, 'MIX-DIG-X32', 'Consola digital Behringer X32', 'Behringer X32 Digital Console', 'articulos/consola_x32.jpg', 180.00, 0, 0, 1, 0, 'Consola digital de 32 canales con 16 buses auxiliares, 8 efectos integrados y grabación multipista USB. Incluye flight case y cable de alimentación. Requiere corriente trifásica.', '32-channel digital console with 16 aux buses, 8 integrated effects and USB multitrack recording. Includes flight case and power cable. Requires three-phase power.', 200, 'Verificar configuración de escenas. Resetear a valores de fábrica después de cada uso.', 1, '2025-11-20 20:33:47', '2025-11-20 20:33:47'),
(24, 21, 5, 'CABLE-XLR-10M', 'Cable XLR 10 metros', '10m XLR Cable', 'articulos/cable_xlr.jpg', 3.80, 1, 0, 0, 0, 'Cable balanceado XLR macho-hembra de 10 metros. Conductor OFC de baja impedancia.', '10m balanced XLR male-female cable. Low impedance OFC conductor.', 300, 'Verificar conectores y continuidad antes de alquilar.', 1, '2025-11-20 20:33:47', '2025-11-26 09:39:41'),
(25, 22, 5, 'LED-PANEL-P3', 'Pantalla LED modular P3 interior (por m²)', 'P3 Indoor LED Panel (per sqm)', 'articulo_6924b18b230b6.png', 450.00, 0, 0, 1, 0, 'Pantalla LED modular de pixel pitch 3mm para interior. Resolución 111.111 píxeles/m². Brillo 1200 nits. Incluye estructura de soporte, procesador de video y cableado. Requiere técnico especializado para montaje.', 'P3 indoor modular LED screen. Resolution 111,111 pixels/sqm. Brightness 1200 nits. Includes support structure, video processor and cabling. Requires specialized technician for assembly.', 50, 'Revisar píxeles muertos. Calibrar color antes de cada evento. Requiere montaje 24h antes.', 1, '2025-11-20 20:33:47', '2025-11-24 19:27:07');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `categorias`
--

CREATE TABLE `categorias` (
  `id` int NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `fecha` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `categorias`
--

INSERT INTO `categorias` (`id`, `nombre`, `fecha`) VALUES
(2, 'comida', '2025-05-01'),
(3, 'f1', '2025-05-29'),
(4, 'trabajadores', '2025-05-30');

--
-- Disparadores `categorias`
--
DELIMITER $$
CREATE TRIGGER `set_default_fecha` BEFORE INSERT ON `categorias` FOR EACH ROW BEGIN
  IF NEW.fecha IS NULL THEN
    SET NEW.fecha = CURDATE();
  END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `cliente`
--

CREATE TABLE `cliente` (
  `id_cliente` int UNSIGNED NOT NULL,
  `codigo_cliente` varchar(20) NOT NULL,
  `nombre_cliente` varchar(255) NOT NULL,
  `direccion_cliente` varchar(255) DEFAULT NULL,
  `cp_cliente` varchar(10) DEFAULT NULL,
  `poblacion_cliente` varchar(100) DEFAULT NULL,
  `provincia_cliente` varchar(100) DEFAULT NULL,
  `nif_cliente` varchar(20) DEFAULT NULL,
  `telefono_cliente` varchar(255) DEFAULT NULL,
  `fax_cliente` varchar(50) DEFAULT NULL,
  `web_cliente` varchar(255) DEFAULT NULL,
  `email_cliente` varchar(255) DEFAULT NULL,
  `nombre_facturacion_cliente` varchar(255) DEFAULT NULL,
  `direccion_facturacion_cliente` varchar(255) DEFAULT NULL,
  `cp_facturacion_cliente` varchar(10) DEFAULT NULL,
  `poblacion_facturacion_cliente` varchar(100) DEFAULT NULL,
  `provincia_facturacion_cliente` varchar(100) DEFAULT NULL,
  `observaciones_cliente` text,
  `activo_cliente` tinyint(1) DEFAULT '1',
  `created_at_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `observaciones_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(1, 'MELIA002', 'Melia Dan Jaime', 'C/ Mayor, 24', '28001', 'Madrid', 'Madrid', 'B214515744444', '965262384', '', '', '', '', '', '', '', '', '', 1, '2025-11-16 09:46:02', '2025-11-18 19:10:27'),
(2, 'PROV00', 'Fontaneria Klek', '', '232244', 'Madrid', 'Madrid', '1213414B', '629995058', '', '', 'cliente@gmail.com', '', 'Calle Comandante Martí 6', '', '', '', '', 0, '2025-11-18 17:20:22', '2025-11-18 19:54:45');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `coeficiente`
--

CREATE TABLE `coeficiente` (
  `id_coeficiente` int UNSIGNED NOT NULL,
  `jornadas_coeficiente` int NOT NULL,
  `valor_coeficiente` decimal(10,2) NOT NULL,
  `observaciones_coeficiente` text,
  `activo_coeficiente` tinyint(1) DEFAULT '1',
  `created_at_coeficiente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_coeficiente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `coeficiente`
--

INSERT INTO `coeficiente` (`id_coeficiente`, `jornadas_coeficiente`, `valor_coeficiente`, `observaciones_coeficiente`, `activo_coeficiente`, `created_at_coeficiente`, `updated_at_coeficiente`) VALUES
(1, 10, 8.20, 'Es una especie de descuento valorado en 3 Euros', 1, '2025-11-14 13:18:26', '2025-11-23 10:12:40'),
(2, 15, 12.00, '', 1, '2025-11-14 13:23:09', '2025-11-14 13:23:09'),
(3, 20, 10.00, '', 1, '2025-11-15 08:40:27', '2025-11-15 08:40:27');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `comerciales`
--

CREATE TABLE `comerciales` (
  `id_comercial` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `movil` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `activo` tinyint DEFAULT '1',
  `id_usuario` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comerciales`
--

INSERT INTO `comerciales` (`id_comercial`, `nombre`, `apellidos`, `movil`, `telefono`, `activo`, `id_usuario`) VALUES
(1, 'Alejandro', 'Rodríguez Martínez', '698689685', '698689685', 1, 5),
(2, 'Carlos', 'López', '655656841', '655656841', 0, 6),
(3, 'Marta', 'Rodríguez González', '645567674', '645567674', 0, 19),
(4, 'Luis', 'Fernández López', '645575741', '645575741', 1, 7),
(9, 'Lucía', 'Pérez Sánchez', '698898874', '698898874', 1, 8),
(11, 'Ana', 'Hernández Torres', '635999999', '635999999', 0, 20),
(12, 'Miguel', 'Díaz Jiménez', '643455441', '643455441', 0, 21),
(13, 'Raúl', 'Romero Álvarez', '695548744', '695548744', 1, 9),
(14, 'Eva', 'Moreno Fernández', '698654645', '698654645', 0, 22),
(16, 'Teresa', 'Vázquez Suárez', '689789454', '689789454', 0, 23),
(17, 'Margarita', 'García Castro', '616515614', '616515614', 1, 10),
(18, 'Carmen', 'Martínez González', '623615641', '623615641', 1, 11),
(19, 'Sergio', 'López Hernández', '634535442', '634535442', 0, 24),
(21, 'Alberto', 'Hernández García', '644334567', '644334567', 1, 12),
(22, 'Natalia', 'Sánchez García', '621849484', '621849484', 1, 13),
(23, 'Laura', 'Ramírez Hernández', '632554779', '632554779', 1, 14),
(24, 'Francisco', 'Moreno Moya', '660300923', '660300923', 1, 15),
(27, 'Beatriz', 'Muñoz Vázquez', '477777777777', '777777774', 1, 16),
(28, 'Pablo', 'Moreno Sánchez', '698544745', '698544745', 1, 17),
(30, 'María', 'Torres García', '645644211', '645644211', 1, 18),
(31, 'Marta', 'Rodríguez González', '635224447', '633221448', 1, 19),
(32, 'Miguel', 'Díaz Jiménez', '646544684', '645487775', 1, 21);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `com_vacaciones`
--

CREATE TABLE `com_vacaciones` (
  `id_vacacion` int NOT NULL,
  `id_comercial` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `descripcion` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_vacacion` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `com_vacaciones`
--

INSERT INTO `com_vacaciones` (`id_vacacion`, `id_comercial`, `fecha_inicio`, `fecha_fin`, `descripcion`, `activo_vacacion`) VALUES
(1, 1, '2025-03-01', '2025-02-14', 'Vacaciones por emergencia', 0),
(2, 2, '2025-04-10', '2025-04-15', 'Viaje familiar', 0),
(3, 3, '2025-05-05', '2025-05-10', 'Descanso médico', 0),
(6, 1, '2025-02-01', '2025-02-22', 'Embarazo', 0),
(9, 2, '2025-04-09', '2025-04-18', 'Vacaciones Provisionales', 0),
(10, 2, '2025-04-02', '2025-04-12', 'Vacaciones por emergencia', 0),
(11, 2, '2025-04-12', '2025-04-19', 'Vacaciones por emergencia', 0),
(12, 2, '2025-04-09', '2025-04-17', 'Descanso médico', 0),
(13, 11, '2025-04-10', '2025-04-18', 'Descanso médico', 0),
(14, 2, '2025-04-19', '2025-04-30', 'Viaje familiar', 0),
(15, 11, '2025-04-09', '2025-04-09', 'Viaje familiar', 0),
(16, 24, '2025-04-03', '2025-04-17', 'Vacaciones de semana santa', 0),
(17, 16, '2025-04-10', '2025-04-24', 'Por semana santa', 0),
(18, 14, '2025-04-02', '2025-04-19', 'Vacaciones de semana santa', 0),
(19, 2, '2025-04-02', '2025-04-17', 'Vacaciones de semana santa', 0),
(20, 2, '2025-05-24', '2025-05-30', 'prueba de ejercicio', 0),
(21, 2, '2025-07-03', '2025-07-17', 'Vacaciones de verano', 0),
(22, 9, '2025-07-12', '2025-07-30', 'Vacaciones de verano', 0),
(23, 9, '2025-08-01', '2025-08-09', 'Segundas vacaciones', 0);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contactos`
--

CREATE TABLE `contactos` (
  `id_contacto` int NOT NULL,
  `id_llamada` int NOT NULL,
  `fecha_hora_contacto` datetime DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `estado` tinyint NOT NULL DEFAULT '1' COMMENT '0=Inactivo, 1=Activo',
  `id_metodo` int NOT NULL,
  `id_visita_cerrada` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `contactos`
--

INSERT INTO `contactos` (`id_contacto`, `id_llamada`, `fecha_hora_contacto`, `observaciones`, `estado`, `id_metodo`, `id_visita_cerrada`) VALUES
(1, 14, '2025-07-13 14:32:00', 'En este contacto, Tomás mostró interés en conocer más detalles sobre los tipos de toldos motorizados disponibles. Se le explicó el funcionamiento del sistema con mando a distancia y la opción de incluir sensores de viento para mayor seguridad. También expresó dudas sobre la instalación y el mantenimiento, que fueron aclaradas. Se acordó enviarle información técnica y un presupuesto preliminar para que pueda valorar las opciones con calma antes de la visita técnica.', 1, 1, NULL),
(2, 1, '2025-07-10 14:23:00', 'Solicitó una visita técnica para tomar medidas en su local comercial. Posible proyecto para instalación de toldos tipo cofre en fachada', 1, 3, 5),
(3, 1, '2025-07-10 14:24:00', '<p data-start=\"259\" data-end=\"384\">María mostró interés en los modelos básicos de toldos, pidió información sobre precios y tiempos de entrega.</p>\n<p data-start=\"386\" data-end=\"535\"></p>', 1, 1, NULL),
(4, 1, '2025-07-10 14:24:00', 'María consultó sobre la posibilidad de instalación rápida y pidió asesoramiento sobre el tipo de toldo más adecuado para su terraza.', 0, 3, NULL),
(5, 1, '2025-07-10 14:24:00', '<p data-start=\"537\" data-end=\"664\">María preguntó sobre opciones de toldos con motor eléctrico y si hay garantía para los mecanismos automáticos.</p>\n<p data-start=\"666\" data-end=\"815\"></p>', 1, 2, NULL),
(6, 1, '2025-07-10 14:24:00', '<p data-start=\"817\" data-end=\"947\">María manifestó dudas sobre el cuidado y limpieza del toldo, y pidió recomendaciones para prolongar su vida útil.</p>', 0, 1, NULL),
(7, 10, '2025-07-13 14:33:00', 'Comentó que su vecina le recomendó nuestra empresa. Valoró positivamente la reputación y experiencia del equipo técnico.', 1, 1, 6),
(8, 2, '2025-04-21 06:52:00', 'El cliente se comunicó por vía telefónica para solicitar información sobre la instalación de un toldo. Mostró interés en conocer precios, tipos de materiales y opciones disponibles. Se tomó nota de sus requerimientos básicos y se ofreció agendar una visita técnica para evaluar el espacio. Se obtuvieron sus datos de contacto para seguimiento.', 1, 1, 10),
(9, 4, '2025-07-14 14:30:00', '<p>Durante este contacto, Luis consultó específicamente sobre la resistencia al viento del modelo propuesto, mostrando preocupación por la durabilidad del sistema. Se le explicó la clasificación de resistencia según normativa UNE-EN 13561 y se le sugirió la opción de incorporar un sensor de viento para mayor seguridad. Mostró interés y solicitó que dicha opción se incluyera en el presupuesto final.</p>', 1, 3, 4),
(14, 20, '2025-07-12 14:30:00', '<p>Luis expresa su intención de renovar varios toldos de la vivienda, priorizando tanto la protección solar como la integración estética con la fachada. Comenta que algunos toldos actuales están deteriorados. Se acuerda una visita para la próxima semana con el fin de tomar medidas y estudiar in situ las opciones más adecuadas. También solicita que en esa visita se le muestren catálogos con diferentes tipos de lona, haciendo especial énfasis en tejidos técnicos y colores neutros.</p>', 1, 2, 7),
(17, 22, '2025-07-12 14:31:00', '<p>Teresa se mostró interesada en renovar el toldo de su terraza debido al desgaste por el sol. Durante la llamada, consultó sobre diferentes tipos de lonas resistentes a la decoloración y preguntó por opciones con sistemas de apertura manual y motorizados. Se le explicó el funcionamiento y ventajas de cada sistema, y se acordó enviarle muestras de tejidos para que pudiera elegir. También solicitó información sobre plazos de instalación y garantías. Quedó pendiente concretar una visita técnica para tomar medidas y avanzar con el presupuesto.</p>', 1, 2, 8),
(18, 24, '2025-05-22 13:06:00', '<p data-start=\"99\" data-end=\"633\">Durante la llamada con Jorge, se confirmó la recepción de la documentación solicitada y se revisaron los detalles técnicos del equipo a instalar. Jorge expresó algunas dudas sobre la configuración del servicio y se le explicó paso a paso el proceso. Además, se acordó que el técnico se pondrá en contacto con él para coordinar la visita en el domicilio. Jorge mostró interés en opciones adicionales, como la instalación de dispositivos complementarios, lo cual será evaluado en próximas comunicaciones.</p>', 1, 1, 9),
(19, 6, '2025-07-12 14:32:00', '<p>Durante el contacto, el cliente solicitó información sobre toldos para un área exterior de aproximadamente 5 metros. Indicó interés en un modelo retráctil, preferiblemente motorizado. Se le brindó una explicación general de los tipos de toldos disponibles, materiales y tiempos estimados de instalación. Se tomaron sus datos para agendar una visita técnica y se le envió catálogo digital por WhatsApp. Cliente receptivo y con intención de avanzar en el proceso.</p>', 1, 1, NULL),
(20, 21, '2025-07-14 13:15:00', '<p>En este contacto, Tomás mostró interés en un toldo vertical para su galería, haciendo hincapié en la necesidad de controlar la entrada de luz y el calor sin oscurecer demasiado el espacio. Preguntó sobre las diferencias entre los tejidos técnicos disponibles y mostró especial atención a las opciones que incluyen guías laterales para mayor estabilidad. También consultó sobre la automatización con sensores solares, buscando comodidad y eficiencia. Se le explicó brevemente cada opción y se acordó realizar una visita técnica para evaluar medidas y condiciones, así como para tomar una decisión informada.</p>', 1, 2, NULL),
(21, 23, '2025-07-13 14:32:00', '<p>Alejandro consultó sobre las diferentes opciones de tejidos técnicos para toldos, mostrando especial interés en aquellos que ofrecen mayor resistencia al sol y a la lluvia. Se aclararon dudas sobre los colores disponibles y se le explicó el funcionamiento del sistema motorizado con mando a distancia. Alejandro pidió que se le enviara información adicional por correo electrónico para analizarla con su familia. Se confirmó la visita técnica para la próxima semana para realizar las mediciones necesarias.</p>', 1, 3, NULL),
(22, 19, '2025-07-12 14:33:00', '<p>En este contacto, Javier mostró interés en las opciones motorizadas, preguntando específicamente por la duración de la batería y la compatibilidad con sistemas domóticos. Se le explicó el funcionamiento del mando a distancia y la posibilidad de integrar sensores de viento para una mayor seguridad. Javier solicitó además información sobre los colores disponibles y el mantenimiento recomendado para prolongar la vida útil del toldo. Se acordó enviarle material informativo y concretar la visita para toma de medidas la próxima semana.</p>', 1, 3, NULL),
(23, 16, '2025-07-12 14:31:00', '<p>Lorena mostró un gran interés en las diferentes opciones de toldos para su vivienda, especialmente en modelos que ofrezcan protección solar eficaz y resistencia a las condiciones climáticas de Valencia. Preguntó por los materiales disponibles, colores y tiempos de instalación. Además, destacó la importancia de un diseño que combine funcionalidad y estética para su terraza. Se acordó enviarle un catálogo con opciones personalizadas y coordinar una visita técnica para evaluar las medidas exactas y ofrecer un presupuesto detallado.</p>', 1, 2, NULL),
(24, 2, '2025-07-17 09:28:00', '<p><br></p>', 1, 1, NULL);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `contactos_con_nombre_comunicante`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `contactos_con_nombre_comunicante` (
`id_contacto` int
,`id_llamada` int
,`id_metodo` int
,`fecha_hora_contacto` datetime
,`observaciones` text
,`id_visita_cerrada` int
,`fecha_visita_cerrada` datetime
,`estado` tinyint
,`nombre_comunicante` varchar(100)
,`domicilio_instalacion` varchar(200)
,`telefono_fijo` varchar(15)
,`telefono_movil` varchar(15)
,`email_contacto` varchar(50)
,`fecha_hora_preferida` datetime
,`fecha_recepcion` datetime
,`id_comercial_asignado` bigint
,`estado_llamada` bigint
,`activo_llamada` int
,`nombre_metodo` varchar(50)
,`imagen_metodo` varchar(255)
,`descripcion_estado_llamada` varchar(100)
,`nombre_comercial` varchar(50)
,`archivos_adjuntos` text
,`tiene_contactos` int
,`estado_es_3` int
,`tiene_adjuntos` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `contacto_cantidad_cliente`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `contacto_cantidad_cliente` (
`id_cliente` int unsigned
,`codigo_cliente` varchar(20)
,`nombre_cliente` varchar(255)
,`direccion_cliente` varchar(255)
,`cp_cliente` varchar(10)
,`poblacion_cliente` varchar(100)
,`provincia_cliente` varchar(100)
,`nif_cliente` varchar(20)
,`telefono_cliente` varchar(255)
,`fax_cliente` varchar(50)
,`web_cliente` varchar(255)
,`email_cliente` varchar(255)
,`nombre_facturacion_cliente` varchar(255)
,`direccion_facturacion_cliente` varchar(255)
,`cp_facturacion_cliente` varchar(10)
,`poblacion_facturacion_cliente` varchar(100)
,`provincia_facturacion_cliente` varchar(100)
,`observaciones_cliente` text
,`activo_cliente` tinyint(1)
,`created_at_cliente` timestamp
,`updated_at_cliente` timestamp
,`cantidad_contactos_cliente` bigint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `contacto_cantidad_proveedor`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `contacto_cantidad_proveedor` (
`id_proveedor` int unsigned
,`codigo_proveedor` varchar(20)
,`nombre_proveedor` varchar(255)
,`direccion_proveedor` varchar(255)
,`cp_proveedor` varchar(10)
,`poblacion_proveedor` varchar(100)
,`provincia_proveedor` varchar(100)
,`nif_proveedor` varchar(20)
,`telefono_proveedor` varchar(255)
,`fax_proveedor` varchar(50)
,`web_proveedor` varchar(255)
,`email_proveedor` varchar(255)
,`persona_contacto_proveedor` varchar(255)
,`direccion_sat_proveedor` varchar(255)
,`cp_sat_proveedor` varchar(10)
,`poblacion_sat_proveedor` varchar(100)
,`provincia_sat_proveedor` varchar(100)
,`telefono_sat_proveedor` varchar(255)
,`fax_sat_proveedor` varchar(50)
,`email_sat_proveedor` varchar(255)
,`observaciones_proveedor` text
,`activo_proveedor` tinyint(1)
,`created_at_proveedor` timestamp
,`updated_at_proveedor` timestamp
,`cantidad_contacto_proveedor` bigint
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_cliente`
--

CREATE TABLE `contacto_cliente` (
  `id_contacto_cliente` int UNSIGNED NOT NULL,
  `id_cliente` int UNSIGNED NOT NULL,
  `nombre_contacto_cliente` varchar(100) NOT NULL,
  `apellidos_contacto_cliente` varchar(150) DEFAULT NULL,
  `cargo_contacto_cliente` varchar(100) DEFAULT NULL,
  `departamento_contacto_cliente` varchar(100) DEFAULT NULL,
  `telefono_contacto_cliente` varchar(50) DEFAULT NULL,
  `movil_contacto_cliente` varchar(50) DEFAULT NULL,
  `email_contacto_cliente` varchar(255) DEFAULT NULL,
  `extension_contacto_cliente` varchar(10) DEFAULT NULL,
  `principal_contacto_cliente` tinyint(1) DEFAULT '0',
  `observaciones_contacto_cliente` text,
  `activo_contacto_cliente` tinyint(1) DEFAULT '1',
  `created_at_contacto_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_contacto_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `contacto_cliente`
--

INSERT INTO `contacto_cliente` (`id_contacto_cliente`, `id_cliente`, `nombre_contacto_cliente`, `apellidos_contacto_cliente`, `cargo_contacto_cliente`, `departamento_contacto_cliente`, `telefono_contacto_cliente`, `movil_contacto_cliente`, `email_contacto_cliente`, `extension_contacto_cliente`, `principal_contacto_cliente`, `observaciones_contacto_cliente`, `activo_contacto_cliente`, `created_at_contacto_cliente`, `updated_at_contacto_cliente`) VALUES
(1, 1, 'Luis', 'Carlos PéRez', '', '', '', '', '', '', 1, 'Prueba de contacto', 1, '2025-11-17 05:59:28', '2025-11-17 06:00:09'),
(2, 1, 'Joseppp', 'Pastor Segura', '', 'Administración', '', '+34622505058', 'joseppastor22@gmail.com', '', 0, '', 1, '2025-11-18 18:13:17', '2025-11-18 18:53:56'),
(3, 2, 'Josep', 'Pastor Segura', '', '', '+34622505058', '', '', '', 1, '', 1, '2025-11-18 19:11:09', '2025-11-18 19:11:24'),
(4, 2, 'Pepe', 'Diaz', '', '', '', '', '', '', 1, '', 1, '2025-11-18 19:11:39', '2025-11-18 19:12:31'),
(5, 2, 'Aaron', 'Sanchez', '', '', '', '', '', '', 0, '', 0, '2025-11-18 19:12:06', '2025-11-18 19:42:09');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `contacto_proveedor`
--

CREATE TABLE `contacto_proveedor` (
  `id_contacto_proveedor` int UNSIGNED NOT NULL,
  `id_proveedor` int UNSIGNED NOT NULL,
  `nombre_contacto_proveedor` varchar(100) NOT NULL,
  `apellidos_contacto_proveedor` varchar(150) DEFAULT NULL,
  `cargo_contacto_proveedor` varchar(100) DEFAULT NULL,
  `departamento_contacto_proveedor` varchar(100) DEFAULT NULL,
  `telefono_contacto_proveedor` varchar(50) DEFAULT NULL,
  `movil_contacto_proveedor` varchar(50) DEFAULT NULL,
  `email_contacto_proveedor` varchar(255) DEFAULT NULL,
  `extension_contacto_proveedor` varchar(10) DEFAULT NULL,
  `principal_contacto_proveedor` tinyint(1) DEFAULT '0',
  `observaciones_contacto_proveedor` text,
  `activo_contacto_proveedor` tinyint(1) DEFAULT '1',
  `created_at_contacto_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_contacto_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `contacto_proveedor`
--

INSERT INTO `contacto_proveedor` (`id_contacto_proveedor`, `id_proveedor`, `nombre_contacto_proveedor`, `apellidos_contacto_proveedor`, `cargo_contacto_proveedor`, `departamento_contacto_proveedor`, `telefono_contacto_proveedor`, `movil_contacto_proveedor`, `email_contacto_proveedor`, `extension_contacto_proveedor`, `principal_contacto_proveedor`, `observaciones_contacto_proveedor`, `activo_contacto_proveedor`, `created_at_contacto_proveedor`, `updated_at_contacto_proveedor`) VALUES
(1, 1, 'Luis Carlos', 'PéRez Mataix', 'Director de Ventas', 'Administración', '660300923', '660345258', 'luiscarlospm@gmail.com', '123', 1, 'Es bastante simpático', 1, '2025-11-16 06:26:58', '2025-11-16 06:27:36'),
(2, 1, 'Alicia', 'Botella', 'Directora general', 'Ventas', '649163478', '', 'alicia@gmail.com', '123', 0, 'Es el contacto principal de ventas.', 1, '2025-11-16 09:23:27', '2025-11-26 09:32:44');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `documento_elemento`
--

CREATE TABLE `documento_elemento` (
  `id_documento_elemento` int UNSIGNED NOT NULL,
  `id_elemento` int UNSIGNED NOT NULL,
  `descripcion_documento_elemento` text,
  `tipo_documento_elemento` varchar(100) DEFAULT NULL COMMENT 'Tipo: Manual, Garantía, Factura, Certificado, etc.',
  `archivo_documento` varchar(500) NOT NULL COMMENT 'Ruta completa con nombre del archivo',
  `privado_documento` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, solo visible para administración',
  `observaciones_documento` text,
  `activo_documento` tinyint(1) DEFAULT '1',
  `created_at_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `documento_elemento`
--

INSERT INTO `documento_elemento` (`id_documento_elemento`, `id_elemento`, `descripcion_documento_elemento`, `tipo_documento_elemento`, `archivo_documento`, `privado_documento`, `observaciones_documento`, `activo_documento`, `created_at_documento`, `updated_at_documento`) VALUES
(1, 1, 'Prueba de documentosssssss', 'Certificado', 'documento_elemento_692c8b7dac770.pdf', 1, 'Prueba de documentossss', 1, '2025-11-30 18:22:53', '2025-11-30 18:59:41'),
(2, 1, 'Prueba de segundo documento', 'Certificado', 'documento_elemento_692c945939d91.pdf', 0, NULL, 1, '2025-11-30 19:00:41', '2025-11-30 19:00:41');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `elemento`
--

CREATE TABLE `elemento` (
  `id_elemento` int UNSIGNED NOT NULL,
  `id_articulo_elemento` int UNSIGNED NOT NULL,
  `id_marca_elemento` int UNSIGNED DEFAULT NULL,
  `modelo_elemento` varchar(100) DEFAULT NULL,
  `codigo_elemento` varchar(50) NOT NULL COMMENT 'Formato: codigo_articulo-correlativo',
  `codigo_barras_elemento` varchar(100) DEFAULT NULL,
  `descripcion_elemento` varchar(255) NOT NULL,
  `numero_serie_elemento` varchar(100) DEFAULT NULL,
  `id_estado_elemento` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Estado actual del elemento',
  `nave_elemento` varchar(50) DEFAULT NULL COMMENT 'Nave o almacén donde se encuentra (ej: "Nave 1", "Nave Principal")',
  `pasillo_columna_elemento` varchar(50) DEFAULT NULL COMMENT 'Pasillo y columna (ej: "A-5", "B-12", "C-3")',
  `altura_elemento` varchar(50) DEFAULT NULL COMMENT 'Altura o nivel (ej: "Planta baja", "Nivel 2", "Altura 3m")',
  `fecha_compra_elemento` date DEFAULT NULL COMMENT 'Fecha de compra del elemento',
  `precio_compra_elemento` decimal(10,2) DEFAULT '0.00' COMMENT 'Precio de compra',
  `proveedor_compra_elemento` varchar(255) DEFAULT NULL COMMENT 'Proveedor que lo vendió',
  `fecha_alta_elemento` date DEFAULT NULL COMMENT 'Fecha de puesta en servicio',
  `fecha_fin_garantia_elemento` date DEFAULT NULL,
  `proximo_mantenimiento_elemento` date DEFAULT NULL,
  `observaciones_elemento` text,
  `activo_elemento` tinyint(1) DEFAULT '1',
  `created_at_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `elemento`
--

INSERT INTO `elemento` (`id_elemento`, `id_articulo_elemento`, `id_marca_elemento`, `modelo_elemento`, `codigo_elemento`, `codigo_barras_elemento`, `descripcion_elemento`, `numero_serie_elemento`, `id_estado_elemento`, `nave_elemento`, `pasillo_columna_elemento`, `altura_elemento`, `fecha_compra_elemento`, `precio_compra_elemento`, `proveedor_compra_elemento`, `fecha_alta_elemento`, `fecha_fin_garantia_elemento`, `proximo_mantenimiento_elemento`, `observaciones_elemento`, `activo_elemento`, `created_at_elemento`, `updated_at_elemento`) VALUES
(1, 21, 1, 'SH-78', 'MIC-INAL-001-001', '123456789', 'Microfono Senheiser SH-78', '123456789', 1, '1', 'a-5', '1', '2025-11-25', 1000.00, 'Prueba', '2025-11-25', '2026-11-25', '2025-12-11', 'Prueba de observaciones', 1, '2025-11-25 07:18:30', '2025-11-25 18:08:56');

--
-- Disparadores `elemento`
--
DELIMITER $$
CREATE TRIGGER `trg_elemento_before_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    DECLARE codigo_art VARCHAR(50);
    DECLARE max_correlativo INT;
    
    -- Obtener código del artículo
    SELECT codigo_articulo 
    INTO codigo_art
    FROM articulo
    WHERE id_articulo = NEW.id_articulo_elemento;
    
    -- Si no se especifica estado, asignar "Disponible"
    IF NEW.id_estado_elemento IS NULL THEN
        SET NEW.id_estado_elemento = 1;
    END IF;
    
    -- Calcular siguiente correlativo extrayéndolo de códigos existentes
    SELECT COALESCE(MAX(
        CAST(SUBSTRING_INDEX(codigo_elemento, '-', -1) AS UNSIGNED)
    ), 0) + 1 INTO max_correlativo
    FROM elemento
    WHERE id_articulo_elemento = NEW.id_articulo_elemento;
    
    -- Generar código completo
    SET NEW.codigo_elemento = CONCAT(
        codigo_art,
        '-',
        LPAD(max_correlativo, 3, '0')
    );
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int NOT NULL,
  `criterio_comercial` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estados_llamada`
--

CREATE TABLE `estados_llamada` (
  `id_estado` int NOT NULL,
  `desc_estado` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `defecto_estado` tinyint DEFAULT NULL,
  `activo_estado` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `peso_estado` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `estados_llamada`
--

INSERT INTO `estados_llamada` (`id_estado`, `desc_estado`, `defecto_estado`, `activo_estado`, `peso_estado`) VALUES
(1, 'Recibida sin atención', 1, '1', 10),
(2, 'Con contacto', 0, '1', 20),
(3, 'Cita Cerrada', 0, '1', 30),
(4, 'Perdida', 0, '1', 40),
(12, 'En espera', 0, '1', 60);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_elemento`
--

CREATE TABLE `estado_elemento` (
  `id_estado_elemento` int UNSIGNED NOT NULL,
  `codigo_estado_elemento` varchar(20) NOT NULL,
  `descripcion_estado_elemento` varchar(50) NOT NULL,
  `color_estado_elemento` varchar(7) DEFAULT NULL COMMENT 'Color hexadecimal para visualización',
  `permite_alquiler_estado_elemento` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, el elemento puede ser alquilado en este estado',
  `observaciones_estado_elemento` text,
  `activo_estado_elemento` tinyint(1) DEFAULT '1',
  `created_at_estado_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_estado_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `estado_elemento`
--

INSERT INTO `estado_elemento` (`id_estado_elemento`, `codigo_estado_elemento`, `descripcion_estado_elemento`, `color_estado_elemento`, `permite_alquiler_estado_elemento`, `observaciones_estado_elemento`, `activo_estado_elemento`, `created_at_estado_elemento`, `updated_at_estado_elemento`) VALUES
(1, 'DISP', 'Disponible', '#4CAF50', 1, NULL, 1, '2025-11-24 08:50:18', '2025-11-24 08:50:18'),
(2, 'ALQU', 'Alquilado', '#2196f3', 0, 'Esta es una prueba de observaciones de estado de elementos', 1, '2025-11-24 08:50:18', '2025-11-24 09:43:34'),
(3, 'REPA', 'En reparación', '#FF9800', 0, NULL, 1, '2025-11-24 08:50:18', '2025-11-24 08:50:18'),
(4, 'BAJA', 'Dado de baja', '#f44336', 0, '', 1, '2025-11-24 08:50:18', '2025-11-24 09:44:15'),
(5, 'TERC', 'De terceros', '#9C27B0', 1, NULL, 1, '2025-11-24 08:50:18', '2025-11-24 08:50:18'),
(6, 'DEPO', 'En depósito', '#607D8B', 0, NULL, 1, '2025-11-24 08:50:18', '2025-11-24 08:50:18'),
(7, 'MANT', 'Mantenimiento', '#FFC107', 0, NULL, 1, '2025-11-24 08:50:18', '2025-11-24 08:50:18'),
(8, 'TRAN', 'En tránsito', '#00BCD4', 0, NULL, 1, '2025-11-24 08:50:18', '2025-11-24 08:50:18'),
(17, 'PR', 'Prueba de estado de elemento', '#4caf50', 0, 'Esta es una prueba de estado de elementos.', 1, '2025-11-24 11:16:31', '2025-11-24 11:16:49');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `estado_presupuesto`
--

CREATE TABLE `estado_presupuesto` (
  `id_estado_ppto` int UNSIGNED NOT NULL,
  `codigo_estado_ppto` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_estado_ppto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_estado_ppto` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#007bff',
  `orden_estado_ppto` int DEFAULT '0',
  `observaciones_estado_ppto` text COLLATE utf8mb4_unicode_ci,
  `activo_estado_ppto` tinyint(1) DEFAULT '1',
  `created_at_estado_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_estado_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estado_presupuesto`
--

INSERT INTO `estado_presupuesto` (`id_estado_ppto`, `codigo_estado_ppto`, `nombre_estado_ppto`, `color_estado_ppto`, `orden_estado_ppto`, `observaciones_estado_ppto`, `activo_estado_ppto`, `created_at_estado_ppto`, `updated_at_estado_ppto`) VALUES
(1, 'PEND', 'Pendiente', '#0000ff', 1, 'Presupuesto pendiente de revisión', 1, '2025-11-14 12:35:03', '2025-11-24 11:30:57'),
(2, 'PROC', 'En Proceso', '#17a2b8', 2, 'Presupuesto en proceso de elaboración', 1, '2025-11-14 12:35:03', '2025-11-14 12:35:03'),
(3, 'APROB', 'Aprobado', '#28a745', 3, 'Presupuesto aprobado por el cliente', 1, '2025-11-14 12:35:03', '2025-11-14 12:35:03'),
(4, 'RECH', 'Rechazado', '#dc3545', 4, 'Presupuesto rechazado por el cliente', 1, '2025-11-14 12:35:03', '2025-11-14 12:35:03'),
(5, 'CANC', 'Cancelado', '#6c757d', 5, 'Presupuesto cancelado', 1, '2025-11-14 12:35:03', '2025-11-14 12:35:03'),
(6, 'DEN', 'Denegado por el cliente', '#ff0000', 100, 'Es una prueba e funcionamiento', 1, '2025-11-14 12:38:40', '2025-11-14 12:38:56'),
(7, 'EST_PRU', 'ESTADO_PRUEBA', '#000000', 500, '', 1, '2025-11-20 19:32:54', '2025-11-20 19:32:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `familia`
--

CREATE TABLE `familia` (
  `id_familia` int UNSIGNED NOT NULL,
  `id_grupo` int UNSIGNED DEFAULT NULL,
  `codigo_familia` varchar(20) NOT NULL,
  `nombre_familia` varchar(100) NOT NULL,
  `name_familia` varchar(100) NOT NULL COMMENT 'Nombre en inglés',
  `descr_familia` varchar(255) DEFAULT NULL,
  `activo_familia` tinyint(1) DEFAULT '1',
  `coeficiente_familia` tinyint DEFAULT NULL,
  `id_unidad_familia` int DEFAULT NULL COMMENT 'el Id (id_unidad) de la tabla unidad_medida',
  `imagen_familia` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Es la foto representativa de la familia',
  `observaciones_presupuesto_familia` text,
  `observations_budget_familia` text,
  `orden_obs_familia` int DEFAULT '100',
  `created_at_familia` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_familia` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `familia`
--

INSERT INTO `familia` (`id_familia`, `id_grupo`, `codigo_familia`, `nombre_familia`, `name_familia`, `descr_familia`, `activo_familia`, `coeficiente_familia`, `id_unidad_familia`, `imagen_familia`, `observaciones_presupuesto_familia`, `observations_budget_familia`, `orden_obs_familia`, `created_at_familia`, `updated_at_familia`) VALUES
(19, 2, 'AUD-MIC', 'Microfonía y Sonido', 'Microphones and Sound', 'Equipos de captación y procesamiento de audio profesional', 1, 1, 1, 'familias/audio_microfonia.jpg', 'Todos los equipos de audio incluyen cables de conexión básicos. El técnico de sonido se cotiza por separado. Se requiere prueba de sonido 2 horas antes del evento.', '', 100, '2025-11-20 20:28:05', '2025-11-28 09:17:47'),
(20, 2, 'ILU-GEN', 'Iluminación Profesional', 'Professional Lighting', 'Equipos de iluminación escénica, arquitectónica y de efectos', 1, 1, 1, 'familias/iluminacion_profesional.jpg', 'La iluminación requiere acceso a cuadro eléctrico con tomas trifásicas. Incluye programación básica de escenas. El operador de iluminación se cotiza por separado.', NULL, 110, '2025-11-20 20:28:05', '2025-11-20 20:28:05'),
(21, 3, 'ACC-CABLE', 'Cableado y Conectores', 'Cables and Connectors', 'Cables de audio, video, datos y alimentación, conectores y adaptadores', 1, 1, 2, 'familias/cables_conectores.jpg', 'Los cables se alquilan en tramos estándar. Disponemos de cables especiales bajo pedido. Se recomienda solicitar 20% adicional como backup.', NULL, 300, '2025-11-20 20:28:05', '2025-11-20 20:28:05'),
(22, 2, 'VID-PROY', 'Video y Proyección', 'Video and Projection', 'Proyectores, pantallas LED, procesadores de video y sistemas de visualización', 1, 0, 1, 'familias/video_proyeccion.jpg', 'Los equipos de video requieren visita técnica previa para verificar condiciones de instalación. Montaje de pantallas LED requiere mínimo 24h de antelación. Incluye técnico durante el evento.', NULL, 50, '2025-11-20 20:28:05', '2025-11-20 20:28:05'),
(23, 3, 'EST-TRUSS', 'Estructuras y Rigging', 'Structures and Rigging', 'Truss, torres de elevación, motores y sistemas de suspensión certificados', 1, 0, 2, 'familias/estructuras_rigging.jpg', 'IMPORTANTE: Todas las estructuras requieren certificación de carga y punto de anclaje certificado. Instalación únicamente por personal cualificado. Se requiere seguro de responsabilidad civil. Cálculo de cargas obligatorio.', NULL, 20, '2025-11-20 20:28:05', '2025-11-20 20:28:05');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `familia_unidad_media`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `familia_unidad_media` (
`id_familia` int unsigned
,`id_grupo` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`descr_familia` varchar(255)
,`imagen_familia` varchar(150)
,`activo_familia` tinyint(1)
,`coeficiente_familia` tinyint
,`created_at_familia` timestamp
,`updated_at_familia` timestamp
,`id_unidad_familia` int
,`observaciones_presupuesto_familia` text
,`orden_obs_familia` int
,`nombre_unidad` varchar(50)
,`descr_unidad` varchar(255)
,`simbolo_unidad` varchar(10)
,`activo_unidad` tinyint(1)
,`codigo_grupo` varchar(20)
,`nombre_grupo` varchar(100)
,`descripcion_grupo` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `forma_pago`
--

CREATE TABLE `forma_pago` (
  `id_pago` int UNSIGNED NOT NULL,
  `codigo_pago` varchar(20) NOT NULL COMMENT 'Código único identificador (ej: CONT_TRANS, FRAC40_60)',
  `nombre_pago` varchar(100) NOT NULL COMMENT 'Nombre descriptivo de la forma de pago',
  `id_metodo_pago` int UNSIGNED NOT NULL COMMENT 'Método de pago a utilizar (transferencia, tarjeta, efectivo...)',
  `descuento_pago` decimal(5,2) DEFAULT '0.00' COMMENT 'Descuento por pronto pago en porcentaje (ej: 2.00 = 2%). Solo aplica si porcentaje_anticipo_pago = 100',
  `porcentaje_anticipo_pago` decimal(5,2) DEFAULT '100.00' COMMENT 'Porcentaje del total a pagar como anticipo (ej: 40.00 = 40%). Si es 100.00 = pago único',
  `dias_anticipo_pago` int DEFAULT '0' COMMENT 'Días para pagar el anticipo desde la firma del presupuesto. 0=al firmar, 7=a los 7 días, 30=a los 30 días',
  `porcentaje_final_pago` decimal(5,2) DEFAULT '0.00' COMMENT 'Porcentaje restante del total (ej: 60.00 = 60%). Debe sumar 100% con el anticipo. Si es 0 = pago único',
  `dias_final_pago` int DEFAULT '0' COMMENT 'Días para el pago final. Positivo=días desde firma (30=a 30 días), Negativo=días antes del evento (-7=7 días antes), 0=al finalizar evento',
  `observaciones_pago` text COMMENT 'Observaciones internas sobre esta forma de pago',
  `activo_pago` tinyint(1) DEFAULT '1' COMMENT 'Si FALSE, la forma de pago no estará disponible para nuevos presupuestos',
  `created_at_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `forma_pago`
--

INSERT INTO `forma_pago` (`id_pago`, `codigo_pago`, `nombre_pago`, `id_metodo_pago`, `descuento_pago`, `porcentaje_anticipo_pago`, `dias_anticipo_pago`, `porcentaje_final_pago`, `dias_final_pago`, `observaciones_pago`, `activo_pago`, `created_at_pago`, `updated_at_pago`) VALUES
(1, 'CONT_TRANS', 'Contado transferencia', 1, 2.00, 100.00, 0, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(2, 'CONT_EFEC', 'Contado efectivo', 3, 2.00, 100.00, 0, 0.00, 0, '', 1, '2025-11-20 07:08:35', '2025-11-21 06:16:47'),
(3, 'CONT_TARJ', 'Contado tarjeta', 2, 0.00, 100.00, 0, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(4, 'TRANS7', 'Transferencia 7 días', 1, 1.00, 100.00, 7, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(5, 'TRANS30', 'Transferencia 30 días', 1, 0.00, 100.00, 30, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(6, 'TRANS60', 'Transferencia 60 días', 1, 0.00, 100.00, 60, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(7, 'TRANS90', 'Transferencia 90 días', 1, 0.00, 100.00, 90, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(8, 'FRAC40_60', '40% anticipo + 60% al finalizar', 1, 0.00, 40.00, 0, 60.00, 0, 'Anticipo al firmar presupuesto, resto al finalizar evento', 1, '2025-11-20 07:10:11', '2025-11-20 07:10:11'),
(9, 'FRAC50_50', '50% anticipo + 50% al finalizar', 1, 0.00, 50.00, 0, 50.00, 0, 'Pago dividido en dos partes iguales', 1, '2025-11-20 07:10:11', '2025-11-20 07:10:11'),
(10, 'FRAC50_30', '50% anticipo + 50% a 30 días', 1, 0.00, 50.00, 0, 50.00, 30, 'Anticipo al firmar, resto a 30 días desde firma', 1, '2025-11-20 07:10:11', '2025-11-20 07:10:11'),
(11, 'FRAC30_60', '30% anticipo + 70% a 60 días', 1, 0.00, 30.00, 0, 70.00, 60, 'Anticipo al firmar, resto a 60 días desde firma', 1, '2025-11-20 07:10:11', '2025-11-20 07:10:11'),
(12, 'FRAC30_7', '30% anticipo + 70% (7 días antes)', 1, 0.00, 30.00, 0, 70.00, -7, 'Anticipo al firmar, resto 7 días antes del evento', 1, '2025-11-20 07:10:11', '2025-11-20 07:10:11'),
(13, 'FRAC40_15', '40% anticipo + 60% (15 días antes)', 1, 0.00, 40.00, 0, 60.00, -15, 'Anticipo al firmar, resto 15 días antes del evento', 1, '2025-11-20 07:10:11', '2025-11-20 07:10:11'),
(14, 'FRAC50_7_30', '50% a 7 días + 50% a 30 días', 1, 0.00, 50.00, 7, 50.00, 30, 'Primer pago a los 7 días de firmar, segundo a los 30 días de firmar', 1, '2025-11-20 07:10:11', '2025-11-20 07:10:11');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `foto_elemento`
--

CREATE TABLE `foto_elemento` (
  `id_foto_elemento` int UNSIGNED NOT NULL,
  `id_elemento` int UNSIGNED NOT NULL,
  `descripcion_foto_elemento` text,
  `archivo_foto` varchar(500) NOT NULL COMMENT 'Ruta completa con nombre del archivo de imagen',
  `privado_foto` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, solo visible para administración',
  `observaciones_foto` text,
  `activo_foto` tinyint(1) DEFAULT '1',
  `created_at_foto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_foto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `foto_elemento`
--

INSERT INTO `foto_elemento` (`id_foto_elemento`, `id_elemento`, `descripcion_foto_elemento`, `archivo_foto`, `privado_foto`, `observaciones_foto`, `activo_foto`, `created_at_foto`, `updated_at_foto`) VALUES
(1, 1, 'Microfonosssss', 'foto_elemento_692ca10c2a911.png', 0, 'Prueba de observacionesssssss', 1, '2025-11-30 19:54:52', '2025-11-30 19:55:26');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `grupo_articulo`
--

CREATE TABLE `grupo_articulo` (
  `id_grupo` int UNSIGNED NOT NULL,
  `codigo_grupo` varchar(20) NOT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `descripcion_grupo` varchar(255) DEFAULT NULL,
  `observaciones_grupo` text,
  `activo_grupo` tinyint(1) DEFAULT '1',
  `created_at_grupo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_grupo` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `grupo_articulo`
--

INSERT INTO `grupo_articulo` (`id_grupo`, `codigo_grupo`, `nombre_grupo`, `descripcion_grupo`, `observaciones_grupo`, `activo_grupo`, `created_at_grupo`, `updated_at_grupo`) VALUES
(2, 'AUD', 'Audio', 'Equipos de sonido, megafonía y amplificación', 'Incluye micrófonos, consolas, altavoces, procesadores de audio, amplificadores y todo el equipamiento relacionado con la captación, procesamiento y reproducción de sonido profesional.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48'),
(3, 'VID', 'Video', 'Equipos de proyección, pantallas y visualización', 'Incluye proyectores, pantallas LED, monitores, procesadores de video, cámaras, sistemas de videoconferencia y todo el equipamiento relacionado con imagen y video.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48'),
(4, 'ILU', 'Iluminación', 'Equipos de iluminación escénica y arquitectónica', 'Incluye focos PAR, moving heads, scanners, strobes, controladores DMX, dimmers y todo tipo de iluminación profesional para eventos y espectáculos.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48'),
(5, 'EST', 'Estructuras', 'Truss, torres, escenarios y rigging', 'Incluye estructuras de aluminio (truss), torres de elevación, motores chain hoist, sistemas de rigging, escenarios modulares y todos los elementos estructurales certificados.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48'),
(6, 'ACC', 'Accesorios', 'Cables, conectores, adaptadores y consumibles', 'Incluye todo tipo de cableado (audio, video, datos, alimentación), conectores, adaptadores, regletas, cajas de señal, soportes y material auxiliar.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48'),
(7, 'COM', 'Comunicaciones', 'Intercomunicadores y sistemas de coordinación', 'Incluye sistemas de intercom, walkie-talkies, sistemas de IFB, auriculares de comunicación y todo el equipamiento para coordinación técnica durante eventos.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48'),
(8, 'ELE', 'Eléctrico', 'Distribución eléctrica y cableado de potencia', 'Incluye cuadros de distribución, cables de potencia, regletas industriales, grupos electrógenos, transformadores y todo el material eléctrico certificado.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48'),
(9, 'MOB', 'Mobiliario', 'Sillas, mesas, vallas y elementos de evento', 'Incluye mobiliario para eventos, vallas de seguridad, moquetas, tarimas, atriles, stands y elementos decorativos o funcionales para eventos.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48');

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

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `llamadas`
--

CREATE TABLE `llamadas` (
  `id_llamada` int NOT NULL,
  `id_metodo` int NOT NULL,
  `nombre_comunicante` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `domicilio_instalacion` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono_fijo` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `telefono_movil` varchar(15) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_contacto` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `fecha_hora_preferida` datetime DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `id_comercial_asignado` int NOT NULL,
  `estado` int NOT NULL,
  `fecha_recepcion` datetime DEFAULT CURRENT_TIMESTAMP,
  `activo_llamada` tinyint DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `llamadas`
--

INSERT INTO `llamadas` (`id_llamada`, `id_metodo`, `nombre_comunicante`, `domicilio_instalacion`, `telefono_fijo`, `telefono_movil`, `email_contacto`, `fecha_hora_preferida`, `observaciones`, `id_comercial_asignado`, `estado`, `fecha_recepcion`, `activo_llamada`) VALUES
(1, 1, 'María García', 'Calle Sant Vicent, 45, Alzira, Valencia', '932345678', '622345678', 'mariagarcia@email.com', '2023-11-16 16:30:00', 'Reclamación sobre factura pendiente.', 12, 3, '2025-06-10 12:48:00', 0),
(2, 1, 'Manolo Gómez Colomer', 'Calle 45, Valencia, Manises', '645121242', '645121268', 'manolo@email.com', '2025-05-21 14:21:00', '<blockquote data-start=\"128\" data-end=\"722\"><p data-start=\"130\" data-end=\"722\">Se realizó contacto con el Sr. Gómez desde su primera llamada el 20/06, en la cual solicitó información para la instalación de un toldo retráctil en su patio. Se agendó una visita técnica el 15/06, donde se tomaron medidas y se le ofrecieron varias opciones de lona y mecanismos. El 20/06 se le envió la cotización formal por correo electrónico. El cliente solicitó ajustes en el presupuesto (cambio de lona a una de mayor resistencia), lo cual se actualizó y se reenviaron los documentos el 26/06. El 28/06 confirmó aceptación del presupuesto y se agendó la instalación para el 02/07.</p>\r\n</blockquote>', 27, 3, '2025-06-15 12:08:00', 1),
(4, 1, 'Luis Rodríguez', 'Calle José María Haro, 12, Quart de Poblet, Valencia', '645787845', '645787874', 'luisrod@gmail.com', NULL, '<p>Desde el primer contacto, Luis mostró interés en la instalación de un toldo para su terraza. Se le presentaron distintas opciones, destacando el modelo de brazos extensibles con lona acrílica. A lo largo de varias conversaciones, planteó dudas sobre el color de la lona, la resistencia al viento, y finalmente se interesó por incorporar un motor con mando a distancia. Se envió presupuesto actualizado incluyendo instalación y automatización. Tras evaluar todo, Luis confirmó su decisión y se agendó visita técnica para toma de medidas. Actualmente, el proceso está pendiente de la confirmación definitiva del presupuesto o programación de la instalación.</p>', 1, 3, '2025-06-09 00:00:00', 0),
(6, 1, 'Juan Pérez', 'Calle Mayor, 12, Xirivella, Valencia', '645215487', '645215454', 'juanperez@gmail.com', '2025-04-10 00:00:00', 'El cliente realizó el primer contacto el 17/04 solicitando información sobre un toldo tipo brazo invisible para su balcón. Se coordinó una visita técnica para el 18/04, en la cual se tomaron medidas y se recomendaron opciones según el espacio disponible. Posteriormente, se envió la cotización vía WhatsApp el 18/04, incluyendo dos alternativas de lona y sistema de apertura motorizado. El cliente solicitó unos días para evaluar la propuesta con su familia. El 20/04 confirmó la aceptación del presupuesto y se programó la instalación para el 23/04.yyyyyy', 4, 2, '2025-06-17 13:08:00', 1),
(10, 1, 'Alejandro Blasco', 'Calle de la Paz, 12 Valencia, Valencia', '686734132', '686734186', 'alejandroblasco@gmail.com', NULL, '<p data-start=\"87\" data-end=\"627\">Durante la llamada con Alejandro, se revisaron las necesidades actuales del cliente y se aclararon varios puntos sobre los servicios ofrecidos. Alejandro manifestó interés en actualizar su plan y solicitó información adicional sobre promociones vigentes. Se le proporcionaron detalles sobre costos y tiempos de instalación. Quedó pendiente agendar una visita técnica para evaluación in situ y resolver dudas específicas. La comunicación fue cordial y Alejandro mostró disposición para continuar con el proceso.</p>', 9, 3, '2025-06-18 13:08:00', 1),
(14, 2, 'Tomás Jiménez', 'Carrer del Baró, 10 València, Patraix', '614854514', '614854532', 'tomasjim@gmail.com', '2025-05-20 16:37:00', '<p data-start=\"86\" data-end=\"724\">Se inicia una nueva llamada con Tomás, interesado en la instalación de toldos para su vivienda. Durante el primer contacto, se recogen sus principales necesidades, destacando la búsqueda de soluciones resistentes y fáciles de manejar. Tomás solicita información sobre distintos tipos de toldos, especialmente aquellos con motorización y sensores de viento. Se acuerda realizar una visita técnica para evaluar las medidas y el tipo de instalación más adecuada. El seguimiento incluirá la presentación de presupuestos personalizados y la resolución de dudas sobre los materiales y opciones disponibles.</p>', 19, 2, '2025-07-17 00:00:00', 0),
(16, 3, 'Lorena López', 'Calle 123, Valencia, Burjassot', '623215451', '623215474', 'lorenalopez@gmail.com', '2025-05-24 14:21:00', 'El cliente se comunicó para solicitar información sobre un toldo para su terraza. Mencionó que busca un modelo retráctil, resistente al sol y la lluvia, de aproximadamente 4 metros de largo. Se le explicó el tipo de materiales disponibles (lona acrílica y PVC), opciones de estructura, colores y sistemas de apertura (manual y motorizado). Se le ofreció una visita técnica sin costo para tomar medidas y evaluar el espacio. El cliente mostró interés y quedó en confirmar la fecha para la visita en las próximas 48 horas. Se registraron sus datos de contacto y ubicación.', 16, 2, '2025-07-11 18:56:00', 1),
(19, 3, 'Javier Díaz', '12 Calle de la Paz, Valencia, Burjassot', '662651515', '662651535', 'javierdiaz@gmail.com', '2025-05-23 13:41:00', 'Javier contactó interesado en la instalación de toldos para su jardín, buscando opciones que combinen funcionalidad y diseño. Durante el primer contacto, explicó que prioriza la resistencia al viento y la facilidad de mantenimiento. Se le informó sobre distintos tipos de tejidos y mecanismos, incluyendo toldos motorizados y manuales. Javier solicitó un presupuesto detallado con opciones de colores y materiales para evaluar. Se coordinó una visita para tomar medidas y ofrecer una propuesta personalizada. Queda pendiente el envío del presupuesto y resolver dudas sobre garantías y tiempos de instalación.', 12, 2, '2025-07-20 10:55:00', 1),
(20, 3, 'Luis Rodríguez', 'Calle de Colón 45, Manises, Valencia', '645787511', '645787574', 'luisrod@gmail.com', '2025-05-16 14:11:00', '<p>Se inicia una nueva llamada con Luis a raíz del interés en renovar varios toldos en su vivienda. En el primer contacto se toma nota de sus necesidades generales, destacando la preocupación por la protección solar y la estética. Se coordina una primera visita para la semana siguiente, con el objetivo de tomar medidas y valorar las mejores opciones en función de la orientación de la fachada. Luis solicita que se le presenten varias alternativas de lona, incluyendo tejidos técnicos. El seguimiento de esta llamada incluirá la presentación del presupuesto y resolución de dudas sobre motorización y sensores de viento.</p>', 19, 3, '2025-07-09 09:21:00', 1),
(21, 2, 'Tomás Hernández', 'Calle Mayor, 45, Quart de Poblet, Valencia', '678778756', '678778773', 'tomasher@email.com', '2025-05-15 17:27:00', '<p>Tomás contacta por primera vez interesado en instalar un toldo vertical para cerrar parcialmente una galería exterior. Explica que busca una solución que reduzca el calor directo pero sin perder completamente la entrada de luz. Durante la conversación, plantea la posibilidad de combinar el toldo con guías laterales o sistema tipo screen enrollable. Se agenda una visita técnica para evaluar la viabilidad según dimensiones y orientación. Tomás solicita también opciones de automatización con sensor solar y de viento. El seguimiento de esta llamada incluirá el envío de propuestas con distintos modelos y precios, así como una comparativa entre sistemas manuales y motorizados.</p>', 2, 2, '2025-07-14 17:27:00', 1),
(22, 3, 'Teresa Alarcón', 'Gran Vía Marqués del Turia, 42, Valencia', '645485781', '645485724', 'teresalar@gmail.com', '2025-05-21 13:16:00', '<p>Se inicia una nueva llamada con Teresa, interesada en la instalación de toldos para su vivienda. Durante el primer contacto, Teresa explicó sus necesidades principales, haciendo énfasis en la durabilidad y diseño de los toldos. Se acordó realizar una visita técnica para evaluar las dimensiones exactas y discutir opciones de motorización y tejidos. Se comprometió a revisar las propuestas que se le enviarán y a resolver cualquier duda en próximas comunicaciones. El seguimiento se centrará en la presentación de presupuestos personalizados y en facilitar asesoramiento técnico para asegurar la satisfacción total del cliente.</p>', 2, 3, '2025-07-15 13:18:00', 1),
(23, 3, 'Alejandro Montero', 'Carrer Major, 45, Valencia, Paiporta', '635356844', '635356814', 'alejandromont@gmail.com', '2025-05-17 16:33:00', '<p>Alejandro mostró interés en instalar toldos automáticos en su vivienda para mejorar la comodidad y protección solar. Durante el primer contacto, se identificaron sus necesidades específicas respecto al tipo de tejido y al sistema de apertura. Se acordó una visita técnica para evaluar las dimensiones y condiciones de instalación. Alejandro solicitó información detallada sobre opciones de motorización y automatización, así como sobre la garantía y mantenimiento. Se le informó sobre las promociones vigentes y se comprometió a valorar el presupuesto en los próximos días. El seguimiento se centrará en resolver dudas técnicas y confirmar fechas para la instalación.</p>', 2, 2, '2025-07-15 13:22:00', 1),
(24, 2, 'Jorge García', 'Calle Valencia, 10, Valencia Alaquàs', '645864861', '645864887', 'jorgegarcia@gmail.com', NULL, '<p data-start=\"151\" data-end=\"949\">Se ha mantenido contacto frecuente con Jorge para seguimiento del proceso de instalación. Durante las llamadas, se han aclarado dudas sobre el equipo y los servicios incluidos, así como sobre los plazos estimados. Jorge ha manifestado interés en recibir una propuesta personalizada que incluya opciones de financiación. Se ha coordinado la visita técnica inicial y se ha informado al cliente sobre la documentación necesaria. También se ha registrado la preferencia de Jorge por la instalación en horario de tarde. Actualmente, se está a la espera de la confirmación final por parte del cliente para avanzar con la contratación y la instalación. Se recomienda realizar una llamada de seguimiento en los próximos días para confirmar fecha y resolver cualquier inquietud adicional.</p>', 2, 3, '2025-07-22 13:06:00', 1),
(26, 2, 'María García', 'Avenida Secundaria 45, Barcelona', NULL, NULL, 'alej@gmail.com', NULL, '<p><br></p>', 2, 1, '2025-07-17 09:12:00', 1);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `llamadas_con_comerciales_y_metodos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `llamadas_con_comerciales_y_metodos` (
`id_llamada` int
,`id_metodo` int
,`nombre_comunicante` varchar(100)
,`domicilio_instalacion` varchar(200)
,`telefono_fijo` varchar(15)
,`telefono_movil` varchar(15)
,`email_contacto` varchar(50)
,`fecha_hora_preferida` datetime
,`observaciones` text
,`id_comercial_asignado` int
,`estado` int
,`fecha_recepcion` datetime
,`activo_llamada` tinyint
,`nombre_comercial` varchar(50)
,`nombre_metodo` varchar(50)
,`imagen_metodo` varchar(255)
,`descripcion_estado` varchar(100)
,`archivos_adjuntos` text
,`tiene_contactos` int
,`estado_es_3` int
,`tiene_adjuntos` int
,`fecha_primer_contacto` datetime
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `marca`
--

CREATE TABLE `marca` (
  `id_marca` int UNSIGNED NOT NULL,
  `codigo_marca` varchar(20) NOT NULL,
  `nombre_marca` varchar(100) NOT NULL,
  `name_marca` varchar(100) NOT NULL COMMENT 'nombre en inglés',
  `descr_marca` varchar(255) DEFAULT NULL,
  `activo_marca` tinyint(1) DEFAULT '1',
  `created_at_marca` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_marca` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `marca`
--

INSERT INTO `marca` (`id_marca`, `codigo_marca`, `nombre_marca`, `name_marca`, `descr_marca`, `activo_marca`, `created_at_marca`, `updated_at_marca`) VALUES
(1, 'SAMS', 'Samsung', 'Samsung (en)', 'Tecnología innovadora y diseño de vanguardia. Suficiente para el desarrollo.', 1, '2025-10-30 16:42:51', '2025-11-05 18:22:50'),
(4, 'ZYYCA44', 'Dell', 'Dell (en)', 'Computadoras y soluciones tecnológicas innovadoras. XXXXXX', 1, '2025-10-30 17:47:32', '2025-10-31 16:18:10'),
(7, 'HP0011', 'Hewlett Packard', 'HP (en) - English', 'Prueba de detalles en marcas (es).', 1, '2025-10-31 16:41:12', '2025-10-31 16:41:41'),
(8, 'NINT', 'Nintendo', 'Nintendo', 'Nintendo es una empresa multinacional japonesa de entretenimiento, originaria de 1889 como fabricante de naipes y ahora dedicada al desarrollo y distribución de consolas y videojuegos.', 1, '2025-11-05 18:22:06', '2025-11-05 18:22:06');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodos_contacto`
--

CREATE TABLE `metodos_contacto` (
  `id_metodo` int NOT NULL,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `permite_adjuntos` tinyint DEFAULT '0',
  `estado` tinyint DEFAULT '1',
  `imagen_metodo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Ruta o nombre de la imagen del método de contacto'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `metodos_contacto`
--

INSERT INTO `metodos_contacto` (`id_metodo`, `nombre`, `permite_adjuntos`, `estado`, `imagen_metodo`) VALUES
(1, 'Correo Electrónico', 1, 1, '34-Mail Success.png'),
(2, 'Llamada Telefónica', 1, 1, '22-Ringing Phone.png'),
(3, 'WhatsApp Business', 0, 1, '41-Chat App.png'),
(39, 'Presencia en tienda', 0, 1, '35-Support.png');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `metodo_forma_pago`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `metodo_forma_pago` (
`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`nombre_metodo_pago` varchar(100)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`descuento_pago` decimal(5,2)
,`tipo_pago` varchar(16)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `metodo_pago`
--

CREATE TABLE `metodo_pago` (
  `id_metodo_pago` int UNSIGNED NOT NULL,
  `codigo_metodo_pago` varchar(20) NOT NULL,
  `nombre_metodo_pago` varchar(100) NOT NULL,
  `observaciones_metodo_pago` text,
  `activo_metodo_pago` tinyint(1) DEFAULT '1',
  `created_at_metodo_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_metodo_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `metodo_pago`
--

INSERT INTO `metodo_pago` (`id_metodo_pago`, `codigo_metodo_pago`, `nombre_metodo_pago`, `observaciones_metodo_pago`, `activo_metodo_pago`, `created_at_metodo_pago`, `updated_at_metodo_pago`) VALUES
(1, 'TRANS', 'Transferencia bancaria', 'Es una observación de transferencia bancaria.', 1, '2025-11-20 07:07:42', '2025-11-21 06:05:11'),
(2, 'TARJ', 'Tarjeta de crédito/débito', NULL, 1, '2025-11-20 07:07:42', '2025-11-20 07:07:42'),
(3, 'EFEC', 'Efectivo', NULL, 1, '2025-11-20 07:07:42', '2025-11-20 07:07:42'),
(4, 'CHEQ', 'Cheque', NULL, 1, '2025-11-20 07:07:42', '2025-11-20 07:07:42'),
(5, 'BIZUM', 'Bizum', NULL, 1, '2025-11-20 07:07:42', '2025-11-20 07:07:42'),
(6, 'PAYPAL', 'PayPal', NULL, 1, '2025-11-20 07:07:42', '2025-11-20 07:07:42'),
(7, 'DOMICIL', 'Domiciliación bancaria', NULL, 1, '2025-11-20 07:07:42', '2025-11-20 07:07:42');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `observacion_general`
--

CREATE TABLE `observacion_general` (
  `id_obs_general` int UNSIGNED NOT NULL,
  `codigo_obs_general` varchar(20) NOT NULL,
  `titulo_obs_general` varchar(100) NOT NULL,
  `title_obs_general` varchar(100) NOT NULL DEFAULT '' COMMENT 'Título en inglés',
  `texto_obs_general` text NOT NULL,
  `text_obs_general` text NOT NULL COMMENT 'Texto en inglés',
  `orden_obs_general` int DEFAULT '0',
  `tipo_obs_general` enum('condiciones','tecnicas','legales','comerciales','otras') DEFAULT 'otras',
  `obligatoria_obs_general` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, siempre aparece en presupuestos',
  `activo_obs_general` tinyint(1) DEFAULT '1',
  `created_at_obs_general` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_obs_general` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `observacion_general`
--

INSERT INTO `observacion_general` (`id_obs_general`, `codigo_obs_general`, `titulo_obs_general`, `title_obs_general`, `texto_obs_general`, `text_obs_general`, `orden_obs_general`, `tipo_obs_general`, `obligatoria_obs_general`, `activo_obs_general`, `created_at_obs_general`, `updated_at_obs_general`) VALUES
(1, 'OBS-11', 'Observación técnica', 'English title', 'Es una prueba de observaciones generales', 'English Observation', 1, 'tecnicas', 1, 1, '2025-11-17 10:23:10', '2025-11-17 18:00:16'),
(2, 'TEST-001', 'Título de prueba', 'Test title', 'Texto de prueba en español', 'Test text in English', 1, 'otras', 1, 1, '2025-11-17 17:45:16', '2025-11-17 17:45:16');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `proveedor`
--

CREATE TABLE `proveedor` (
  `id_proveedor` int UNSIGNED NOT NULL,
  `codigo_proveedor` varchar(20) NOT NULL,
  `nombre_proveedor` varchar(255) NOT NULL,
  `direccion_proveedor` varchar(255) DEFAULT NULL,
  `cp_proveedor` varchar(10) DEFAULT NULL,
  `poblacion_proveedor` varchar(100) DEFAULT NULL,
  `provincia_proveedor` varchar(100) DEFAULT NULL,
  `nif_proveedor` varchar(20) DEFAULT NULL,
  `telefono_proveedor` varchar(255) DEFAULT NULL,
  `fax_proveedor` varchar(50) DEFAULT NULL,
  `web_proveedor` varchar(255) DEFAULT NULL,
  `email_proveedor` varchar(255) DEFAULT NULL,
  `persona_contacto_proveedor` varchar(255) DEFAULT NULL,
  `direccion_sat_proveedor` varchar(255) DEFAULT NULL,
  `cp_sat_proveedor` varchar(10) DEFAULT NULL,
  `poblacion_sat_proveedor` varchar(100) DEFAULT NULL,
  `provincia_sat_proveedor` varchar(100) DEFAULT NULL,
  `telefono_sat_proveedor` varchar(255) DEFAULT NULL,
  `fax_sat_proveedor` varchar(50) DEFAULT NULL,
  `email_sat_proveedor` varchar(255) DEFAULT NULL,
  `observaciones_proveedor` text,
  `activo_proveedor` tinyint(1) DEFAULT '1',
  `created_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `codigo_proveedor`, `nombre_proveedor`, `direccion_proveedor`, `cp_proveedor`, `poblacion_proveedor`, `provincia_proveedor`, `nif_proveedor`, `telefono_proveedor`, `fax_proveedor`, `web_proveedor`, `email_proveedor`, `persona_contacto_proveedor`, `direccion_sat_proveedor`, `cp_sat_proveedor`, `poblacion_sat_proveedor`, `provincia_sat_proveedor`, `telefono_sat_proveedor`, `fax_sat_proveedor`, `email_sat_proveedor`, `observaciones_proveedor`, `activo_proveedor`, `created_at_proveedor`, `updated_at_proveedor`) VALUES
(1, 'PRUEBA001', 'Prueba', '', '', '', '', '12342422N', '', '', '', 'manuelefe@gmail.com', '', 'C/ rio Amadorio, 4', '03013', 'La Pobla de Farnals', 'Valencia', '99300923', '4522666987', 'luis@gmail.com', 'Prueba de observaciones', 1, '2025-11-15 12:00:50', '2025-11-17 19:10:51'),
(2, 'PRUEBA003', 'Prueba 002', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', 1, '2025-11-15 12:09:40', '2025-11-18 19:02:28');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `roles`
--

CREATE TABLE `roles` (
  `id_rol` int NOT NULL,
  `nombre_rol` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `est` tinyint DEFAULT '1' COMMENT 'est = 0 --> Inactivo, est = 1 --> Activo'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='Tabla que contiene los distintos roles de usuario';

--
-- Volcado de datos para la tabla `roles`
--

INSERT INTO `roles` (`id_rol`, `nombre_rol`, `est`) VALUES
(1, 'Empleado', 1),
(2, 'Gestor', 1),
(3, 'Administrador', 1),
(4, 'Comercial', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `unidad_medida`
--

CREATE TABLE `unidad_medida` (
  `id_unidad` int UNSIGNED NOT NULL,
  `nombre_unidad` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `name_unidad` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nombre en ingles.',
  `descr_unidad` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `simbolo_unidad` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_unidad` tinyint(1) DEFAULT '1',
  `created_at_unidad` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_unidad` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `unidad_medida`
--

INSERT INTO `unidad_medida` (`id_unidad`, `nombre_unidad`, `name_unidad`, `descr_unidad`, `simbolo_unidad`, `activo_unidad`, `created_at_unidad`, `updated_at_unidad`) VALUES
(1, 'Metros', 'Meters', 'Unidad de longitud bbbbbbx', 'm', 1, '2025-11-04 18:03:15', '2025-11-24 11:54:07'),
(2, 'Horas', 'Hour', 'Unidad de medida de tiempo equivalente a 60 minutos.', 'H', 1, '2025-11-06 19:21:53', '2025-11-06 19:22:14'),
(4, 'Kilometros', 'Kilometer', 'Es para el control del desplazameinto', 'Km', 1, '2025-11-07 18:06:23', '2025-11-07 18:06:23'),
(5, 'UNIDAD', 'Unidades', 'Es la descripción de unidades', 'Un', 1, '2025-11-21 06:29:55', '2025-11-21 06:29:55');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `usuarios`
--

CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL,
  `email` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `contrasena` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `fecha_crea` datetime DEFAULT CURRENT_TIMESTAMP,
  `est` tinyint DEFAULT NULL COMMENT 'est = 0 --> Inactivo, est =1 --> activo',
  `id_rol` int NOT NULL,
  `tokenUsu` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='Tabla de usuario con contraseñas y roles asociados';

--
-- Volcado de datos para la tabla `usuarios`
--

INSERT INTO `usuarios` (`id_usuario`, `email`, `contrasena`, `nombre`, `fecha_crea`, `est`, `id_rol`, `tokenUsu`) VALUES
(1, 'ale@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Alejandro', '2025-04-25 09:10:45', 1, 4, 'x8v7c3plq9dtfr1b2a6mjohw5esynuz'),
(2, 'luis@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Luis', '2025-04-25 10:23:12', 1, 3, '92fmazrxb8pcl5ghvt6wqyonj3sedu'),
(3, 'jorge@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Jorge', '2025-04-25 10:30:21', 1, 1, 'qlynsjo3vg9rc6tepbxahw4zf7dumk'),
(4, 'hugo@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Hugo', '2025-04-25 10:37:15', 1, 2, 'hmr4dwaj5s9p7b6qcznxfvotlyegku'),
(5, 'alejandrorodriguez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Alejandro Rodríguez Martínez', '2025-05-15 10:18:39', 1, 4, 'k47bzmou1hsyexrfqvwcdjap9nglt0'),
(6, 'carloslopez@email.com', '4af1f0ef2163e4aa57a300a876c5116e', 'Carlos López', '2025-05-15 10:18:39', 1, 4, 'd53gbtnrmyhvlqex7k0fzaosw9pjic'),
(7, 'luisfernandez@email.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Luis Fernández López', '2025-05-15 10:18:39', 1, 4, 'js2htcqrv89lwxuodkgf5n1abzemyp'),
(8, 'luciaperez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Lucía Pérez Sánchez', '2025-05-15 10:18:39', 1, 4, 'nqgs4wkjco7lyh1zvtrfaepx896dbum'),
(9, 'raulromero@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Raúl Romero Álvarez', '2025-05-15 10:18:39', 1, 4, 'mv94eub6zt7hrojwsnxlcgaqkpyd5fi'),
(10, 'margaritagarcia@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Margarita García Castro', '2025-05-15 10:18:39', 1, 4, 'wy1vpfl39zrhedxbjaknugqmc507sto'),
(11, 'carmenmartinez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Carmen Martínez González', '2025-05-15 10:18:39', 1, 4, 'oabwvrls3zyxmpu9tgqekfdh17cnj5i'),
(12, 'albertohernandez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Alberto Hernández García', '2025-05-15 10:18:39', 1, 4, 'zeuhkmdx5p14jgtyrvb9lcawoqnsf78'),
(13, 'nataliasanchez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Natalia Sánchez García', '2025-05-15 10:18:39', 1, 4, 'cbnt1ayqpkj48mfwrxsz7vehgo6dlu9'),
(14, 'lauraramirez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Laura Ramírez Hernández', '2025-05-15 10:18:39', 1, 4, 'rqhlxomciznkv57bfsa4yduw3ptgej8'),
(15, 'franciscomorenomoya@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Francisco Moreno Moya', '2025-05-15 10:18:39', 1, 4, 'yteazwjf1nrhgcbsuvdxkqpm58lo94v'),
(16, 'beatrizmunoz@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Beatriz Muñoz Vázquez', '2025-05-15 10:18:39', 1, 4, 'vk3dqrujwbf5sclm7onhxytz2g9eaip'),
(17, 'pablomoreno@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Pablo Moreno Sánchez', '2025-05-15 10:18:39', 1, 4, 'p6lzexv87mcsyn1htgfaowjrqdkuib9'),
(18, 'mariatorres@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'María Torres García', '2025-05-15 10:18:39', 1, 4, 'gf4n6rctskxepbhmqujz8y1vwlod05a'),
(19, 'martarodriguez@email.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Marta Rodríguez González', '2025-05-15 10:26:12', 1, 4, 'uwg0mspz9bqh16ckafjtynrldove237'),
(20, 'carloshernandez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Ana Hernández Torres', '2025-05-15 10:26:12', 1, 4, 'bsfntmkgp7qhrdwo9viyexczla25uj38'),
(21, 'migueldiaz@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Miguel Díaz Jiménez', '2025-05-15 10:26:13', 1, 4, 'h5frtxw9ek2pimlvcyagjdsobznq47u'),
(22, 'evamoreno@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Eva Moreno Fernández', '2025-05-15 10:26:13', 1, 4, 'nxhtg04f7yqlv1wscprzjomdbkae59u'),
(23, 'teresavazquez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Teresa Vázquez Suárez', '2025-05-15 10:26:13', 1, 4, 'z7mvwg95hrk1nlx68sfcqadotjeupb2'),
(24, 'sergiolopez@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Sergio López Hernández', '2025-05-15 10:26:13', 1, 4, 'jl6vzqsbm97x3c1dheogwftkaurynp45'),
(25, 'alejandrosolvam@gmail.com', 'fca3d97bb6bbd55138f9af6ac121acda', 'Alejandro', '2025-04-25 10:23:12', 1, 3, '92fmazrxb8pcl5ghvt6wqyonj3sedu4'),
(26, 'delafuente@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Laura', '2025-07-11 07:31:08', 1, 2, NULL),
(27, 'tomasgarc@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Tomás García', '2025-07-11 07:45:50', 1, 1, 'lwsdqoud6kk3rm1z61mgtyneckd80m'),
(28, 'geronimosalinas@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Geronimo Salinas', '2025-07-11 10:10:41', 1, 4, 'vbczr73fzf3cvhyg55o8xda0v2hskg'),
(29, 'admin@gmail.com', 'cb4b39a466aaef4652df4a10d50fb8d2', 'Administrador de demostración', '2025-09-16 07:16:37', 1, 3, '6zvo6g8j7r48b53eqg0a8ztv9kmzs4'),
(30, 'carmen@mdr.com', '353885231743fc6a2fa7a6cccee42e43', 'Carmen', '2025-11-19 11:15:48', 1, 3, 'o6kjw9g3at13ul3lyx1sq6wzve1yly');

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `usuarios_con_rol`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `usuarios_con_rol` (
`id_usuario` int
,`nombre` varchar(60)
,`email` varchar(60)
,`contrasena` varchar(255)
,`fecha_crea` datetime
,`est` tinyint
,`id_rol` int
,`nombre_rol` varchar(50)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vacaciones_con_nombre`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vacaciones_con_nombre` (
`id_vacacion` int
,`id_comercial` int
,`fecha_inicio` date
,`fecha_fin` date
,`descripcion` varchar(50)
,`activo_vacacion` tinyint
,`nombre_comercial` varchar(101)
);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `visitas_cerradas`
--

CREATE TABLE `visitas_cerradas` (
  `id_visita_cerrada` int NOT NULL,
  `fecha_visita_cerrada` datetime NOT NULL,
  `id_contacto` int NOT NULL,
  `id_llamada` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `visitas_cerradas`
--

INSERT INTO `visitas_cerradas` (`id_visita_cerrada`, `fecha_visita_cerrada`, `id_contacto`, `id_llamada`) VALUES
(4, '2025-07-10 14:26:00', 9, 4),
(5, '2025-07-09 14:19:00', 2, 1),
(6, '2025-05-31 15:55:00', 7, 10),
(7, '2025-06-05 12:09:00', 14, 20),
(8, '2025-05-17 15:33:00', 17, 22),
(9, '2025-05-30 13:07:00', 18, 24),
(10, '2025-07-15 14:16:00', 8, 2);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_adjuntos_con_comunicante`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_adjuntos_con_comunicante` (
`id_adjunto` int
,`id_llamada` int
,`nombre_archivo` varchar(255)
,`tipo` varchar(20)
,`fecha_subida` datetime
,`estado` tinyint
,`nombre_comunicante` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_articulo_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_articulo_completa` (
`id_articulo` int unsigned
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`name_articulo` varchar(255)
,`imagen_articulo` varchar(255)
,`precio_alquiler_articulo` decimal(10,2)
,`coeficiente_articulo` tinyint(1)
,`es_kit_articulo` tinyint(1)
,`control_total_articulo` tinyint(1)
,`no_facturar_articulo` tinyint(1)
,`notas_presupuesto_articulo` text
,`notes_budget_articulo` text
,`orden_obs_articulo` int
,`observaciones_articulo` text
,`activo_articulo` tinyint(1)
,`created_at_articulo` timestamp
,`updated_at_articulo` timestamp
,`id_familia` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`descr_familia` varchar(255)
,`imagen_familia` varchar(150)
,`coeficiente_familia` tinyint
,`observaciones_presupuesto_familia` text
,`orden_obs_familia` int
,`activo_familia` tinyint(1)
,`id_grupo` int unsigned
,`codigo_grupo` varchar(20)
,`nombre_grupo` varchar(100)
,`descripcion_grupo` varchar(255)
,`activo_grupo` tinyint(1)
,`id_unidad` int unsigned
,`nombre_unidad` varchar(50)
,`descr_unidad` varchar(255)
,`simbolo_unidad` varchar(10)
,`activo_unidad` tinyint(1)
,`coeficiente_efectivo` int
,`imagen_efectiva` varchar(255)
,`tipo_imagen` varchar(10)
,`jerarquia_completa` varchar(461)
,`configuracion_completa` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_elementos_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_elementos_completa` (
`id_elemento` int unsigned
,`codigo_elemento` varchar(50)
,`codigo_barras_elemento` varchar(100)
,`descripcion_elemento` varchar(255)
,`numero_serie_elemento` varchar(100)
,`modelo_elemento` varchar(100)
,`nave_elemento` varchar(50)
,`pasillo_columna_elemento` varchar(50)
,`altura_elemento` varchar(50)
,`ubicacion_completa_elemento` varchar(156)
,`id_articulo` int unsigned
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`name_articulo` varchar(255)
,`precio_alquiler_articulo` decimal(10,2)
,`id_familia` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`id_grupo` int unsigned
,`codigo_grupo` varchar(20)
,`nombre_grupo` varchar(100)
,`id_marca` int unsigned
,`codigo_marca` varchar(20)
,`nombre_marca` varchar(100)
,`id_estado_elemento` int unsigned
,`codigo_estado_elemento` varchar(20)
,`descripcion_estado_elemento` varchar(50)
,`color_estado_elemento` varchar(7)
,`permite_alquiler_estado_elemento` tinyint(1)
,`fecha_compra_elemento` date
,`precio_compra_elemento` decimal(10,2)
,`proveedor_compra_elemento` varchar(255)
,`fecha_alta_elemento` date
,`fecha_fin_garantia_elemento` date
,`proximo_mantenimiento_elemento` date
,`estado_garantia_elemento` varchar(12)
,`estado_mantenimiento_elemento` varchar(13)
,`observaciones_elemento` text
,`activo_elemento` tinyint(1)
,`created_at_elemento` timestamp
,`updated_at_elemento` timestamp
,`jerarquia_completa_elemento` text
,`dias_en_servicio_elemento` int
,`anios_en_servicio_elemento` decimal(13,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_elemento_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_elemento_completa` (
`id_elemento` int unsigned
,`codigo_elemento` varchar(50)
,`codigo_barras_elemento` varchar(100)
,`descripcion_elemento` varchar(255)
,`numero_serie_elemento` varchar(100)
,`modelo_elemento` varchar(100)
,`nave_elemento` varchar(50)
,`pasillo_columna_elemento` varchar(50)
,`altura_elemento` varchar(50)
,`ubicacion_completa_elemento` varchar(156)
,`id_articulo` int unsigned
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`name_articulo` varchar(255)
,`precio_alquiler_articulo` decimal(10,2)
,`id_familia` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`id_grupo` int unsigned
,`codigo_grupo` varchar(20)
,`nombre_grupo` varchar(100)
,`id_marca` int unsigned
,`codigo_marca` varchar(20)
,`nombre_marca` varchar(100)
,`id_estado_elemento` int unsigned
,`codigo_estado_elemento` varchar(20)
,`descripcion_estado_elemento` varchar(50)
,`color_estado_elemento` varchar(7)
,`permite_alquiler_estado_elemento` tinyint(1)
,`fecha_compra_elemento` date
,`precio_compra_elemento` decimal(10,2)
,`proveedor_compra_elemento` varchar(255)
,`fecha_alta_elemento` date
,`fecha_fin_garantia_elemento` date
,`proximo_mantenimiento_elemento` date
,`estado_garantia_elemento` varchar(12)
,`estado_mantenimiento_elemento` varchar(13)
,`observaciones_elemento` text
,`activo_elemento` tinyint(1)
,`created_at_elemento` timestamp
,`updated_at_elemento` timestamp
,`jerarquia_completa_elemento` text
,`dias_en_servicio_elemento` int
,`anios_en_servicio_elemento` decimal(13,2)
);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `adjunto_llamada`
--
ALTER TABLE `adjunto_llamada`
  ADD PRIMARY KEY (`id_adjunto`),
  ADD KEY `fk_id_llamada` (`id_llamada`);

--
-- Indices de la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD PRIMARY KEY (`id_articulo`),
  ADD UNIQUE KEY `codigo_articulo` (`codigo_articulo`),
  ADD KEY `fk_articulo_unidad` (`id_unidad`),
  ADD KEY `idx_codigo_articulo` (`codigo_articulo`),
  ADD KEY `idx_id_familia_articulo` (`id_familia`),
  ADD KEY `idx_nombre_articulo` (`nombre_articulo`),
  ADD KEY `idx_es_kit_articulo` (`es_kit_articulo`);

--
-- Indices de la tabla `categorias`
--
ALTER TABLE `categorias`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD PRIMARY KEY (`id_cliente`),
  ADD UNIQUE KEY `codigo_cliente` (`codigo_cliente`),
  ADD KEY `idx_codigo_cliente` (`codigo_cliente`),
  ADD KEY `idx_nombre_cliente` (`nombre_cliente`),
  ADD KEY `idx_nif_cliente` (`nif_cliente`);

--
-- Indices de la tabla `coeficiente`
--
ALTER TABLE `coeficiente`
  ADD PRIMARY KEY (`id_coeficiente`),
  ADD UNIQUE KEY `jornadas_coeficiente` (`jornadas_coeficiente`);

--
-- Indices de la tabla `comerciales`
--
ALTER TABLE `comerciales`
  ADD PRIMARY KEY (`id_comercial`),
  ADD KEY `fk_comerciales_usuarios` (`id_usuario`);

--
-- Indices de la tabla `com_vacaciones`
--
ALTER TABLE `com_vacaciones`
  ADD PRIMARY KEY (`id_vacacion`),
  ADD KEY `fk_vacaciones_comercial` (`id_comercial`);

--
-- Indices de la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD PRIMARY KEY (`id_contacto`),
  ADD KEY `id_llamada` (`id_llamada`),
  ADD KEY `fk_id_metodo` (`id_metodo`),
  ADD KEY `fk_contactos_visitas_cerradas` (`id_visita_cerrada`);

--
-- Indices de la tabla `contacto_cliente`
--
ALTER TABLE `contacto_cliente`
  ADD PRIMARY KEY (`id_contacto_cliente`),
  ADD KEY `idx_id_cliente_contacto` (`id_cliente`),
  ADD KEY `idx_nombre_contacto_cliente` (`nombre_contacto_cliente`);

--
-- Indices de la tabla `contacto_proveedor`
--
ALTER TABLE `contacto_proveedor`
  ADD PRIMARY KEY (`id_contacto_proveedor`),
  ADD KEY `idx_id_proveedor_contacto` (`id_proveedor`),
  ADD KEY `idx_nombre_contacto_proveedor` (`nombre_contacto_proveedor`);

--
-- Indices de la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  ADD PRIMARY KEY (`id_documento_elemento`),
  ADD KEY `idx_id_elemento_documento` (`id_elemento`),
  ADD KEY `idx_tipo_documento` (`tipo_documento_elemento`),
  ADD KEY `idx_privado_documento` (`privado_documento`);

--
-- Indices de la tabla `elemento`
--
ALTER TABLE `elemento`
  ADD PRIMARY KEY (`id_elemento`),
  ADD UNIQUE KEY `codigo_elemento` (`codigo_elemento`),
  ADD UNIQUE KEY `codigo_barras_elemento` (`codigo_barras_elemento`),
  ADD UNIQUE KEY `numero_serie_elemento` (`numero_serie_elemento`),
  ADD KEY `idx_codigo_elemento` (`codigo_elemento`),
  ADD KEY `idx_codigo_barras_elemento` (`codigo_barras_elemento`),
  ADD KEY `idx_numero_serie_elemento` (`numero_serie_elemento`),
  ADD KEY `idx_id_articulo_elemento` (`id_articulo_elemento`),
  ADD KEY `idx_id_marca_elemento` (`id_marca_elemento`),
  ADD KEY `idx_id_estado_elemento` (`id_estado_elemento`),
  ADD KEY `idx_nave_elemento` (`nave_elemento`),
  ADD KEY `idx_pasillo_columna_elemento` (`pasillo_columna_elemento`),
  ADD KEY `idx_fecha_compra_elemento` (`fecha_compra_elemento`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`);

--
-- Indices de la tabla `estados_llamada`
--
ALTER TABLE `estados_llamada`
  ADD PRIMARY KEY (`id_estado`);

--
-- Indices de la tabla `estado_elemento`
--
ALTER TABLE `estado_elemento`
  ADD PRIMARY KEY (`id_estado_elemento`),
  ADD UNIQUE KEY `codigo_estado_elemento` (`codigo_estado_elemento`),
  ADD KEY `idx_codigo_estado_elemento` (`codigo_estado_elemento`);

--
-- Indices de la tabla `estado_presupuesto`
--
ALTER TABLE `estado_presupuesto`
  ADD PRIMARY KEY (`id_estado_ppto`),
  ADD UNIQUE KEY `codigo_estado_ppto` (`codigo_estado_ppto`),
  ADD KEY `idx_estado_presupuesto_activo` (`activo_estado_ppto`),
  ADD KEY `idx_estado_presupuesto_codigo` (`codigo_estado_ppto`),
  ADD KEY `idx_estado_presupuesto_orden` (`orden_estado_ppto`);

--
-- Indices de la tabla `familia`
--
ALTER TABLE `familia`
  ADD PRIMARY KEY (`id_familia`),
  ADD UNIQUE KEY `codigo_familia` (`codigo_familia`),
  ADD KEY `idx_id_grupo_familia` (`id_grupo`);

--
-- Indices de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  ADD PRIMARY KEY (`id_pago`),
  ADD UNIQUE KEY `codigo_pago` (`codigo_pago`),
  ADD KEY `idx_id_metodo_pago` (`id_metodo_pago`),
  ADD KEY `idx_activo_pago` (`activo_pago`);

--
-- Indices de la tabla `foto_elemento`
--
ALTER TABLE `foto_elemento`
  ADD PRIMARY KEY (`id_foto_elemento`),
  ADD KEY `idx_id_elemento_foto` (`id_elemento`),
  ADD KEY `idx_privado_foto` (`privado_foto`);

--
-- Indices de la tabla `grupo_articulo`
--
ALTER TABLE `grupo_articulo`
  ADD PRIMARY KEY (`id_grupo`),
  ADD UNIQUE KEY `codigo_grupo` (`codigo_grupo`);

--
-- Indices de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  ADD PRIMARY KEY (`id_impuesto`);

--
-- Indices de la tabla `llamadas`
--
ALTER TABLE `llamadas`
  ADD PRIMARY KEY (`id_llamada`);

--
-- Indices de la tabla `marca`
--
ALTER TABLE `marca`
  ADD PRIMARY KEY (`id_marca`),
  ADD UNIQUE KEY `codigo_marca` (`codigo_marca`);

--
-- Indices de la tabla `metodos_contacto`
--
ALTER TABLE `metodos_contacto`
  ADD PRIMARY KEY (`id_metodo`),
  ADD UNIQUE KEY `nombre_UNIQUE` (`nombre`);

--
-- Indices de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  ADD PRIMARY KEY (`id_metodo_pago`),
  ADD UNIQUE KEY `codigo_metodo_pago` (`codigo_metodo_pago`),
  ADD KEY `idx_codigo_metodo_pago` (`codigo_metodo_pago`);

--
-- Indices de la tabla `observacion_general`
--
ALTER TABLE `observacion_general`
  ADD PRIMARY KEY (`id_obs_general`),
  ADD UNIQUE KEY `codigo_obs_general` (`codigo_obs_general`),
  ADD KEY `idx_orden_obs_general` (`orden_obs_general`),
  ADD KEY `idx_obligatoria_obs_general` (`obligatoria_obs_general`);

--
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `codigo_proveedor` (`codigo_proveedor`),
  ADD KEY `idx_codigo_proveedor` (`codigo_proveedor`),
  ADD KEY `idx_nombre_proveedor` (`nombre_proveedor`),
  ADD KEY `idx_nif_proveedor` (`nif_proveedor`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  ADD PRIMARY KEY (`id_unidad`);

--
-- Indices de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD PRIMARY KEY (`id_usuario`),
  ADD KEY `id_rol` (`id_rol`);

--
-- Indices de la tabla `visitas_cerradas`
--
ALTER TABLE `visitas_cerradas`
  ADD PRIMARY KEY (`id_visita_cerrada`),
  ADD KEY `id_contacto` (`id_contacto`),
  ADD KEY `fk_visitas_llamadas` (`id_llamada`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `adjunto_llamada`
--
ALTER TABLE `adjunto_llamada`
  MODIFY `id_adjunto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=86;

--
-- AUTO_INCREMENT de la tabla `articulo`
--
ALTER TABLE `articulo`
  MODIFY `id_articulo` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `coeficiente`
--
ALTER TABLE `coeficiente`
  MODIFY `id_coeficiente` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `comerciales`
--
ALTER TABLE `comerciales`
  MODIFY `id_comercial` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT de la tabla `com_vacaciones`
--
ALTER TABLE `com_vacaciones`
  MODIFY `id_vacacion` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `contactos`
--
ALTER TABLE `contactos`
  MODIFY `id_contacto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT de la tabla `contacto_cliente`
--
ALTER TABLE `contacto_cliente`
  MODIFY `id_contacto_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `contacto_proveedor`
--
ALTER TABLE `contacto_proveedor`
  MODIFY `id_contacto_proveedor` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  MODIFY `id_documento_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `elemento`
--
ALTER TABLE `elemento`
  MODIFY `id_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `estados_llamada`
--
ALTER TABLE `estados_llamada`
  MODIFY `id_estado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `estado_elemento`
--
ALTER TABLE `estado_elemento`
  MODIFY `id_estado_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT de la tabla `estado_presupuesto`
--
ALTER TABLE `estado_presupuesto`
  MODIFY `id_estado_ppto` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `familia`
--
ALTER TABLE `familia`
  MODIFY `id_familia` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  MODIFY `id_pago` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `foto_elemento`
--
ALTER TABLE `foto_elemento`
  MODIFY `id_foto_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `grupo_articulo`
--
ALTER TABLE `grupo_articulo`
  MODIFY `id_grupo` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  MODIFY `id_impuesto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `llamadas`
--
ALTER TABLE `llamadas`
  MODIFY `id_llamada` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id_marca` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `metodos_contacto`
--
ALTER TABLE `metodos_contacto`
  MODIFY `id_metodo` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT de la tabla `metodo_pago`
--
ALTER TABLE `metodo_pago`
  MODIFY `id_metodo_pago` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `observacion_general`
--
ALTER TABLE `observacion_general`
  MODIFY `id_obs_general` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  MODIFY `id_proveedor` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `roles`
--
ALTER TABLE `roles`
  MODIFY `id_rol` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT de la tabla `unidad_medida`
--
ALTER TABLE `unidad_medida`
  MODIFY `id_unidad` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `usuarios`
--
ALTER TABLE `usuarios`
  MODIFY `id_usuario` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT de la tabla `visitas_cerradas`
--
ALTER TABLE `visitas_cerradas`
  MODIFY `id_visita_cerrada` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

-- --------------------------------------------------------

--
-- Estructura para la vista `contactos_con_nombre_comunicante`
--
DROP TABLE IF EXISTS `contactos_con_nombre_comunicante`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `contactos_con_nombre_comunicante`  AS SELECT `c`.`id_contacto` AS `id_contacto`, `c`.`id_llamada` AS `id_llamada`, `c`.`id_metodo` AS `id_metodo`, `c`.`fecha_hora_contacto` AS `fecha_hora_contacto`, `c`.`observaciones` AS `observaciones`, `c`.`id_visita_cerrada` AS `id_visita_cerrada`, (select `vc`.`fecha_visita_cerrada` from `visitas_cerradas` `vc` where (`vc`.`id_visita_cerrada` = `c`.`id_visita_cerrada`)) AS `fecha_visita_cerrada`, `c`.`estado` AS `estado`, (select `l`.`nombre_comunicante` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `nombre_comunicante`, (select `l`.`domicilio_instalacion` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `domicilio_instalacion`, (select `l`.`telefono_fijo` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `telefono_fijo`, (select `l`.`telefono_movil` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `telefono_movil`, (select `l`.`email_contacto` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `email_contacto`, (select `l`.`fecha_hora_preferida` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `fecha_hora_preferida`, (select `l`.`fecha_recepcion` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `fecha_recepcion`, (select `l`.`id_comercial_asignado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `id_comercial_asignado`, (select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `estado_llamada`, (select `l`.`activo_llamada` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `activo_llamada`, (select `m`.`nombre` from `metodos_contacto` `m` where (`m`.`id_metodo` = `c`.`id_metodo`)) AS `nombre_metodo`, (select `m`.`imagen_metodo` from `metodos_contacto` `m` where (`m`.`id_metodo` = `c`.`id_metodo`)) AS `imagen_metodo`, (select `e`.`desc_estado` from `estados_llamada` `e` where (`e`.`id_estado` = (select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)))) AS `descripcion_estado_llamada`, (select `com`.`nombre` from `comerciales` `com` where (`com`.`id_comercial` = (select `l`.`id_comercial_asignado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)))) AS `nombre_comercial`, ifnull((select group_concat(`a`.`nombre_archivo` separator ',') from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `c`.`id_llamada`) and (`a`.`estado` = 1))),'Sin archivos') AS `archivos_adjuntos`, (select (count(0) > 0) from `contactos` `cont` where (`cont`.`id_llamada` = `c`.`id_llamada`)) AS `tiene_contactos`, ((select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) = 3) AS `estado_es_3`, (select (count(0) > 0) from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `c`.`id_llamada`) and (`a`.`estado` = 1))) AS `tiene_adjuntos` FROM `contactos` AS `c` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `contacto_cantidad_cliente`
--
DROP TABLE IF EXISTS `contacto_cantidad_cliente`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `contacto_cantidad_cliente`  AS SELECT `cliente`.`id_cliente` AS `id_cliente`, `cliente`.`codigo_cliente` AS `codigo_cliente`, `cliente`.`nombre_cliente` AS `nombre_cliente`, `cliente`.`direccion_cliente` AS `direccion_cliente`, `cliente`.`cp_cliente` AS `cp_cliente`, `cliente`.`poblacion_cliente` AS `poblacion_cliente`, `cliente`.`provincia_cliente` AS `provincia_cliente`, `cliente`.`nif_cliente` AS `nif_cliente`, `cliente`.`telefono_cliente` AS `telefono_cliente`, `cliente`.`fax_cliente` AS `fax_cliente`, `cliente`.`web_cliente` AS `web_cliente`, `cliente`.`email_cliente` AS `email_cliente`, `cliente`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, `cliente`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`, `cliente`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`, `cliente`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`, `cliente`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`, `cliente`.`observaciones_cliente` AS `observaciones_cliente`, `cliente`.`activo_cliente` AS `activo_cliente`, `cliente`.`created_at_cliente` AS `created_at_cliente`, `cliente`.`updated_at_cliente` AS `updated_at_cliente`, (select count(`contacto_cliente`.`id_contacto_cliente`) from `contacto_cliente` where (`contacto_cliente`.`id_cliente` = `cliente`.`id_cliente`)) AS `cantidad_contactos_cliente` FROM `cliente` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `contacto_cantidad_proveedor`
--
DROP TABLE IF EXISTS `contacto_cantidad_proveedor`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `contacto_cantidad_proveedor`  AS SELECT `proveedor`.`id_proveedor` AS `id_proveedor`, `proveedor`.`codigo_proveedor` AS `codigo_proveedor`, `proveedor`.`nombre_proveedor` AS `nombre_proveedor`, `proveedor`.`direccion_proveedor` AS `direccion_proveedor`, `proveedor`.`cp_proveedor` AS `cp_proveedor`, `proveedor`.`poblacion_proveedor` AS `poblacion_proveedor`, `proveedor`.`provincia_proveedor` AS `provincia_proveedor`, `proveedor`.`nif_proveedor` AS `nif_proveedor`, `proveedor`.`telefono_proveedor` AS `telefono_proveedor`, `proveedor`.`fax_proveedor` AS `fax_proveedor`, `proveedor`.`web_proveedor` AS `web_proveedor`, `proveedor`.`email_proveedor` AS `email_proveedor`, `proveedor`.`persona_contacto_proveedor` AS `persona_contacto_proveedor`, `proveedor`.`direccion_sat_proveedor` AS `direccion_sat_proveedor`, `proveedor`.`cp_sat_proveedor` AS `cp_sat_proveedor`, `proveedor`.`poblacion_sat_proveedor` AS `poblacion_sat_proveedor`, `proveedor`.`provincia_sat_proveedor` AS `provincia_sat_proveedor`, `proveedor`.`telefono_sat_proveedor` AS `telefono_sat_proveedor`, `proveedor`.`fax_sat_proveedor` AS `fax_sat_proveedor`, `proveedor`.`email_sat_proveedor` AS `email_sat_proveedor`, `proveedor`.`observaciones_proveedor` AS `observaciones_proveedor`, `proveedor`.`activo_proveedor` AS `activo_proveedor`, `proveedor`.`created_at_proveedor` AS `created_at_proveedor`, `proveedor`.`updated_at_proveedor` AS `updated_at_proveedor`, (select count(`contacto_proveedor`.`id_contacto_proveedor`) from `contacto_proveedor` where (`contacto_proveedor`.`id_proveedor` = `proveedor`.`id_proveedor`)) AS `cantidad_contacto_proveedor` FROM `proveedor` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `familia_unidad_media`
--
DROP TABLE IF EXISTS `familia_unidad_media`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `familia_unidad_media`  AS SELECT `f`.`id_familia` AS `id_familia`, `f`.`id_grupo` AS `id_grupo`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `f`.`descr_familia` AS `descr_familia`, `f`.`imagen_familia` AS `imagen_familia`, `f`.`activo_familia` AS `activo_familia`, `f`.`coeficiente_familia` AS `coeficiente_familia`, `f`.`created_at_familia` AS `created_at_familia`, `f`.`updated_at_familia` AS `updated_at_familia`, `f`.`id_unidad_familia` AS `id_unidad_familia`, `f`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`, `f`.`orden_obs_familia` AS `orden_obs_familia`, `u`.`nombre_unidad` AS `nombre_unidad`, `u`.`descr_unidad` AS `descr_unidad`, `u`.`simbolo_unidad` AS `simbolo_unidad`, `u`.`activo_unidad` AS `activo_unidad`, `g`.`codigo_grupo` AS `codigo_grupo`, `g`.`nombre_grupo` AS `nombre_grupo`, `g`.`descripcion_grupo` AS `descripcion_grupo` FROM ((`familia` `f` left join `unidad_medida` `u` on((`f`.`id_unidad_familia` = `u`.`id_unidad`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `llamadas_con_comerciales_y_metodos`
--
DROP TABLE IF EXISTS `llamadas_con_comerciales_y_metodos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `llamadas_con_comerciales_y_metodos`  AS SELECT `l`.`id_llamada` AS `id_llamada`, `l`.`id_metodo` AS `id_metodo`, `l`.`nombre_comunicante` AS `nombre_comunicante`, `l`.`domicilio_instalacion` AS `domicilio_instalacion`, `l`.`telefono_fijo` AS `telefono_fijo`, `l`.`telefono_movil` AS `telefono_movil`, `l`.`email_contacto` AS `email_contacto`, `l`.`fecha_hora_preferida` AS `fecha_hora_preferida`, `l`.`observaciones` AS `observaciones`, `l`.`id_comercial_asignado` AS `id_comercial_asignado`, `l`.`estado` AS `estado`, `l`.`fecha_recepcion` AS `fecha_recepcion`, `l`.`activo_llamada` AS `activo_llamada`, (select `c`.`nombre` from `comerciales` `c` where (`c`.`id_comercial` = `l`.`id_comercial_asignado`)) AS `nombre_comercial`, (select `m`.`nombre` from `metodos_contacto` `m` where (`m`.`id_metodo` = `l`.`id_metodo`)) AS `nombre_metodo`, (select `m`.`imagen_metodo` from `metodos_contacto` `m` where (`m`.`id_metodo` = `l`.`id_metodo`)) AS `imagen_metodo`, (select `e`.`desc_estado` from `estados_llamada` `e` where (`e`.`id_estado` = `l`.`estado`)) AS `descripcion_estado`, ifnull((select group_concat(`a`.`nombre_archivo` separator ',') from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `l`.`id_llamada`) and (`a`.`estado` = 1))),'Sin archivos') AS `archivos_adjuntos`, (select (count(0) > 0) from `contactos` `c` where (`c`.`id_llamada` = `l`.`id_llamada`)) AS `tiene_contactos`, (`l`.`estado` = 3) AS `estado_es_3`, (select (count(0) > 0) from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `l`.`id_llamada`) and (`a`.`estado` = 1))) AS `tiene_adjuntos`, (select `c`.`fecha_hora_contacto` from `contactos` `c` where (`c`.`id_llamada` = `l`.`id_llamada`) order by `c`.`fecha_hora_contacto` limit 1) AS `fecha_primer_contacto` FROM `llamadas` AS `l` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `metodo_forma_pago`
--
DROP TABLE IF EXISTS `metodo_forma_pago`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `metodo_forma_pago`  AS SELECT `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`descuento_pago` AS `descuento_pago`, (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' else 'Pago fraccionado' end) AS `tipo_pago` FROM (`forma_pago` `fp` join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) WHERE (`fp`.`activo_pago` = true) ORDER BY (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 1 else 2 end) ASC, `fp`.`nombre_pago` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `usuarios_con_rol`
--
DROP TABLE IF EXISTS `usuarios_con_rol`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `usuarios_con_rol`  AS SELECT `usuarios`.`id_usuario` AS `id_usuario`, `usuarios`.`nombre` AS `nombre`, `usuarios`.`email` AS `email`, `usuarios`.`contrasena` AS `contrasena`, `usuarios`.`fecha_crea` AS `fecha_crea`, `usuarios`.`est` AS `est`, `usuarios`.`id_rol` AS `id_rol`, (select `roles`.`nombre_rol` from `roles` where (`roles`.`id_rol` = `usuarios`.`id_rol`)) AS `nombre_rol` FROM `usuarios` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vacaciones_con_nombre`
--
DROP TABLE IF EXISTS `vacaciones_con_nombre`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `vacaciones_con_nombre`  AS SELECT `com_vacaciones`.`id_vacacion` AS `id_vacacion`, `com_vacaciones`.`id_comercial` AS `id_comercial`, `com_vacaciones`.`fecha_inicio` AS `fecha_inicio`, `com_vacaciones`.`fecha_fin` AS `fecha_fin`, `com_vacaciones`.`descripcion` AS `descripcion`, `com_vacaciones`.`activo_vacacion` AS `activo_vacacion`, (select concat(`comerciales`.`nombre`,' ',`comerciales`.`apellidos`) from `comerciales` where (`comerciales`.`id_comercial` = `com_vacaciones`.`id_comercial`)) AS `nombre_comercial` FROM `com_vacaciones` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_adjuntos_con_comunicante`
--
DROP TABLE IF EXISTS `vista_adjuntos_con_comunicante`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `vista_adjuntos_con_comunicante`  AS SELECT `adjunto_llamada`.`id_adjunto` AS `id_adjunto`, `adjunto_llamada`.`id_llamada` AS `id_llamada`, `adjunto_llamada`.`nombre_archivo` AS `nombre_archivo`, `adjunto_llamada`.`tipo` AS `tipo`, `adjunto_llamada`.`fecha_subida` AS `fecha_subida`, `adjunto_llamada`.`estado` AS `estado`, (select `llamadas`.`nombre_comunicante` from `llamadas` where (`llamadas`.`id_llamada` = `adjunto_llamada`.`id_llamada`)) AS `nombre_comunicante` FROM `adjunto_llamada` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_articulo_completa`
--
DROP TABLE IF EXISTS `vista_articulo_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_articulo_completa`  AS SELECT `a`.`id_articulo` AS `id_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`imagen_articulo` AS `imagen_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `a`.`coeficiente_articulo` AS `coeficiente_articulo`, `a`.`es_kit_articulo` AS `es_kit_articulo`, `a`.`control_total_articulo` AS `control_total_articulo`, `a`.`no_facturar_articulo` AS `no_facturar_articulo`, `a`.`notas_presupuesto_articulo` AS `notas_presupuesto_articulo`, `a`.`notes_budget_articulo` AS `notes_budget_articulo`, `a`.`orden_obs_articulo` AS `orden_obs_articulo`, `a`.`observaciones_articulo` AS `observaciones_articulo`, `a`.`activo_articulo` AS `activo_articulo`, `a`.`created_at_articulo` AS `created_at_articulo`, `a`.`updated_at_articulo` AS `updated_at_articulo`, `f`.`id_familia` AS `id_familia`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `f`.`descr_familia` AS `descr_familia`, `f`.`imagen_familia` AS `imagen_familia`, `f`.`coeficiente_familia` AS `coeficiente_familia`, `f`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`, `f`.`orden_obs_familia` AS `orden_obs_familia`, `f`.`activo_familia` AS `activo_familia`, `g`.`id_grupo` AS `id_grupo`, `g`.`codigo_grupo` AS `codigo_grupo`, `g`.`nombre_grupo` AS `nombre_grupo`, `g`.`descripcion_grupo` AS `descripcion_grupo`, `g`.`activo_grupo` AS `activo_grupo`, `u`.`id_unidad` AS `id_unidad`, `u`.`nombre_unidad` AS `nombre_unidad`, `u`.`descr_unidad` AS `descr_unidad`, `u`.`simbolo_unidad` AS `simbolo_unidad`, `u`.`activo_unidad` AS `activo_unidad`, (case when (`a`.`coeficiente_articulo` is null) then `f`.`coeficiente_familia` when (`a`.`coeficiente_articulo` = 1) then 1 else 0 end) AS `coeficiente_efectivo`, coalesce(`a`.`imagen_articulo`,`f`.`imagen_familia`) AS `imagen_efectiva`, (case when (`a`.`imagen_articulo` is not null) then 'propia' when (`f`.`imagen_familia` is not null) then 'heredada' else 'sin_imagen' end) AS `tipo_imagen`, concat(coalesce(`g`.`nombre_grupo`,'Sin grupo'),' > ',`f`.`nombre_familia`,' > ',`a`.`nombre_articulo`) AS `jerarquia_completa`, (case when ((`a`.`precio_alquiler_articulo` > 0) and (`a`.`imagen_articulo` is not null) and (`a`.`notas_presupuesto_articulo` is not null)) then 1 else 0 end) AS `configuracion_completa` FROM (((`articulo` `a` join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) left join `unidad_medida` `u` on((`a`.`id_unidad` = `u`.`id_unidad`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_elementos_completa`
--
DROP TABLE IF EXISTS `vista_elementos_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_elementos_completa`  AS SELECT `e`.`id_elemento` AS `id_elemento`, `e`.`codigo_elemento` AS `codigo_elemento`, `e`.`codigo_barras_elemento` AS `codigo_barras_elemento`, `e`.`descripcion_elemento` AS `descripcion_elemento`, `e`.`numero_serie_elemento` AS `numero_serie_elemento`, `e`.`modelo_elemento` AS `modelo_elemento`, `e`.`nave_elemento` AS `nave_elemento`, `e`.`pasillo_columna_elemento` AS `pasillo_columna_elemento`, `e`.`altura_elemento` AS `altura_elemento`, concat_ws(' | ',coalesce(`e`.`nave_elemento`,''),coalesce(`e`.`pasillo_columna_elemento`,''),coalesce(`e`.`altura_elemento`,'')) AS `ubicacion_completa_elemento`, `a`.`id_articulo` AS `id_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `f`.`id_familia` AS `id_familia`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `g`.`id_grupo` AS `id_grupo`, `g`.`codigo_grupo` AS `codigo_grupo`, `g`.`nombre_grupo` AS `nombre_grupo`, `m`.`id_marca` AS `id_marca`, `m`.`codigo_marca` AS `codigo_marca`, `m`.`nombre_marca` AS `nombre_marca`, `est`.`id_estado_elemento` AS `id_estado_elemento`, `est`.`codigo_estado_elemento` AS `codigo_estado_elemento`, `est`.`descripcion_estado_elemento` AS `descripcion_estado_elemento`, `est`.`color_estado_elemento` AS `color_estado_elemento`, `est`.`permite_alquiler_estado_elemento` AS `permite_alquiler_estado_elemento`, `e`.`fecha_compra_elemento` AS `fecha_compra_elemento`, `e`.`precio_compra_elemento` AS `precio_compra_elemento`, `e`.`proveedor_compra_elemento` AS `proveedor_compra_elemento`, `e`.`fecha_alta_elemento` AS `fecha_alta_elemento`, `e`.`fecha_fin_garantia_elemento` AS `fecha_fin_garantia_elemento`, `e`.`proximo_mantenimiento_elemento` AS `proximo_mantenimiento_elemento`, (case when (`e`.`fecha_fin_garantia_elemento` < curdate()) then 'Vencida' when (`e`.`fecha_fin_garantia_elemento` between curdate() and (curdate() + interval 30 day)) then 'Por vencer' when (`e`.`fecha_fin_garantia_elemento` > (curdate() + interval 30 day)) then 'Vigente' else 'Sin garantía' end) AS `estado_garantia_elemento`, (case when (`e`.`proximo_mantenimiento_elemento` < curdate()) then 'Atrasado' when (`e`.`proximo_mantenimiento_elemento` between curdate() and (curdate() + interval 15 day)) then 'Próximo' when (`e`.`proximo_mantenimiento_elemento` > (curdate() + interval 15 day)) then 'Al día' else 'Sin programar' end) AS `estado_mantenimiento_elemento`, `e`.`observaciones_elemento` AS `observaciones_elemento`, `e`.`activo_elemento` AS `activo_elemento`, `e`.`created_at_elemento` AS `created_at_elemento`, `e`.`updated_at_elemento` AS `updated_at_elemento`, concat_ws(' > ',coalesce(`g`.`nombre_grupo`,'Sin grupo'),`f`.`nombre_familia`,`a`.`nombre_articulo`,`e`.`descripcion_elemento`) AS `jerarquia_completa_elemento`, (to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) AS `dias_en_servicio_elemento`, round(((to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) / 365.25),2) AS `anios_en_servicio_elemento` FROM (((((`elemento` `e` join `articulo` `a` on((`e`.`id_articulo_elemento` = `a`.`id_articulo`))) join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) left join `marca` `m` on((`e`.`id_marca_elemento` = `m`.`id_marca`))) join `estado_elemento` `est` on((`e`.`id_estado_elemento` = `est`.`id_estado_elemento`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_elemento_completa`
--
DROP TABLE IF EXISTS `vista_elemento_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_elemento_completa`  AS SELECT `e`.`id_elemento` AS `id_elemento`, `e`.`codigo_elemento` AS `codigo_elemento`, `e`.`codigo_barras_elemento` AS `codigo_barras_elemento`, `e`.`descripcion_elemento` AS `descripcion_elemento`, `e`.`numero_serie_elemento` AS `numero_serie_elemento`, `e`.`modelo_elemento` AS `modelo_elemento`, `e`.`nave_elemento` AS `nave_elemento`, `e`.`pasillo_columna_elemento` AS `pasillo_columna_elemento`, `e`.`altura_elemento` AS `altura_elemento`, concat_ws(' | ',coalesce(`e`.`nave_elemento`,''),coalesce(`e`.`pasillo_columna_elemento`,''),coalesce(`e`.`altura_elemento`,'')) AS `ubicacion_completa_elemento`, `a`.`id_articulo` AS `id_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `f`.`id_familia` AS `id_familia`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `g`.`id_grupo` AS `id_grupo`, `g`.`codigo_grupo` AS `codigo_grupo`, `g`.`nombre_grupo` AS `nombre_grupo`, `m`.`id_marca` AS `id_marca`, `m`.`codigo_marca` AS `codigo_marca`, `m`.`nombre_marca` AS `nombre_marca`, `est`.`id_estado_elemento` AS `id_estado_elemento`, `est`.`codigo_estado_elemento` AS `codigo_estado_elemento`, `est`.`descripcion_estado_elemento` AS `descripcion_estado_elemento`, `est`.`color_estado_elemento` AS `color_estado_elemento`, `est`.`permite_alquiler_estado_elemento` AS `permite_alquiler_estado_elemento`, `e`.`fecha_compra_elemento` AS `fecha_compra_elemento`, `e`.`precio_compra_elemento` AS `precio_compra_elemento`, `e`.`proveedor_compra_elemento` AS `proveedor_compra_elemento`, `e`.`fecha_alta_elemento` AS `fecha_alta_elemento`, `e`.`fecha_fin_garantia_elemento` AS `fecha_fin_garantia_elemento`, `e`.`proximo_mantenimiento_elemento` AS `proximo_mantenimiento_elemento`, (case when (`e`.`fecha_fin_garantia_elemento` < curdate()) then 'Vencida' when (`e`.`fecha_fin_garantia_elemento` between curdate() and (curdate() + interval 30 day)) then 'Por vencer' when (`e`.`fecha_fin_garantia_elemento` > (curdate() + interval 30 day)) then 'Vigente' else 'Sin garantía' end) AS `estado_garantia_elemento`, (case when (`e`.`proximo_mantenimiento_elemento` < curdate()) then 'Atrasado' when (`e`.`proximo_mantenimiento_elemento` between curdate() and (curdate() + interval 15 day)) then 'Próximo' when (`e`.`proximo_mantenimiento_elemento` > (curdate() + interval 15 day)) then 'Al día' else 'Sin programar' end) AS `estado_mantenimiento_elemento`, `e`.`observaciones_elemento` AS `observaciones_elemento`, `e`.`activo_elemento` AS `activo_elemento`, `e`.`created_at_elemento` AS `created_at_elemento`, `e`.`updated_at_elemento` AS `updated_at_elemento`, concat_ws(' > ',coalesce(`g`.`nombre_grupo`,'Sin grupo'),`f`.`nombre_familia`,`a`.`nombre_articulo`,`e`.`descripcion_elemento`) AS `jerarquia_completa_elemento`, (to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) AS `dias_en_servicio_elemento`, round(((to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) / 365.25),2) AS `anios_en_servicio_elemento` FROM (((((`elemento` `e` join `articulo` `a` on((`e`.`id_articulo_elemento` = `a`.`id_articulo`))) join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) left join `marca` `m` on((`e`.`id_marca_elemento` = `m`.`id_marca`))) join `estado_elemento` `est` on((`e`.`id_estado_elemento` = `est`.`id_estado_elemento`))) WHERE (`e`.`activo_elemento` = true) ;

--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `adjunto_llamada`
--
ALTER TABLE `adjunto_llamada`
  ADD CONSTRAINT `fk_id_llamada` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`);

--
-- Filtros para la tabla `articulo`
--
ALTER TABLE `articulo`
  ADD CONSTRAINT `fk_articulo_familia` FOREIGN KEY (`id_familia`) REFERENCES `familia` (`id_familia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articulo_unidad` FOREIGN KEY (`id_unidad`) REFERENCES `unidad_medida` (`id_unidad`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `comerciales`
--
ALTER TABLE `comerciales`
  ADD CONSTRAINT `fk_comerciales_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE;

--
-- Filtros para la tabla `com_vacaciones`
--
ALTER TABLE `com_vacaciones`
  ADD CONSTRAINT `fk_vacaciones_comercial` FOREIGN KEY (`id_comercial`) REFERENCES `comerciales` (`id_comercial`) ON DELETE CASCADE;

--
-- Filtros para la tabla `contactos`
--
ALTER TABLE `contactos`
  ADD CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`),
  ADD CONSTRAINT `fk_contactos_visitas_cerradas` FOREIGN KEY (`id_visita_cerrada`) REFERENCES `visitas_cerradas` (`id_visita_cerrada`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_id_metodo` FOREIGN KEY (`id_metodo`) REFERENCES `metodos_contacto` (`id_metodo`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `contacto_cliente`
--
ALTER TABLE `contacto_cliente`
  ADD CONSTRAINT `fk_contacto_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `contacto_proveedor`
--
ALTER TABLE `contacto_proveedor`
  ADD CONSTRAINT `fk_contacto_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  ADD CONSTRAINT `fk_documento_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `elemento`
--
ALTER TABLE `elemento`
  ADD CONSTRAINT `fk_elemento_articulo` FOREIGN KEY (`id_articulo_elemento`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_estado` FOREIGN KEY (`id_estado_elemento`) REFERENCES `estado_elemento` (`id_estado_elemento`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_marca` FOREIGN KEY (`id_marca_elemento`) REFERENCES `marca` (`id_marca`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `familia`
--
ALTER TABLE `familia`
  ADD CONSTRAINT `fk_familia_grupo` FOREIGN KEY (`id_grupo`) REFERENCES `grupo_articulo` (`id_grupo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  ADD CONSTRAINT `fk_forma_pago_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metodo_pago`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `foto_elemento`
--
ALTER TABLE `foto_elemento`
  ADD CONSTRAINT `fk_foto_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `usuarios`
--
ALTER TABLE `usuarios`
  ADD CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `visitas_cerradas`
--
ALTER TABLE `visitas_cerradas`
  ADD CONSTRAINT `fk_visitas_llamadas` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`) ON UPDATE CASCADE,
  ADD CONSTRAINT `visitas_cerradas_ibfk_1` FOREIGN KEY (`id_contacto`) REFERENCES `contactos` (`id_contacto`) ON UPDATE CASCADE;

DELIMITER $$
--
-- Eventos
--
CREATE DEFINER=`administrator`@`%` EVENT `desactivar_fecha_vacaciones_12` ON SCHEDULE EVERY 1 DAY STARTS '2025-04-03 00:01:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL desactivar_vacaciones_pasadas()$$

CREATE DEFINER=`administrator`@`%` EVENT `desactivar_fecha_vacaciones_3` ON SCHEDULE EVERY 1 DAY STARTS '2025-04-03 03:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL desactivar_vacaciones_pasadas()$$

CREATE DEFINER=`administrator`@`%` EVENT `desactivar_vacaciones_pasadas` ON SCHEDULE EVERY 12 HOUR STARTS '2025-05-26 09:34:34' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE com_vacaciones
  SET activo_vacacion = 0
  WHERE fecha_fin < CURDATE()
    AND activo_vacacion = 1$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
