-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Servidor: mysql:3306
-- Tiempo de generación: 11-03-2026 a las 16:59:13
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

CREATE DEFINER=`administrator`@`%` PROCEDURE `sp_actualizar_contador_empresa` (IN `p_id_empresa` INT UNSIGNED, IN `p_tipo_documento` VARCHAR(30))   BEGIN
    IF p_tipo_documento = 'presupuesto' THEN
        UPDATE empresa SET numero_actual_presupuesto_empresa = numero_actual_presupuesto_empresa + 1 WHERE id_empresa = p_id_empresa;
    ELSEIF p_tipo_documento = 'factura' THEN
        UPDATE empresa SET numero_actual_factura_empresa = numero_actual_factura_empresa + 1 WHERE id_empresa = p_id_empresa;
    ELSEIF p_tipo_documento = 'abono' THEN
        UPDATE empresa SET numero_actual_abono_empresa = numero_actual_abono_empresa + 1 WHERE id_empresa = p_id_empresa;
    ELSEIF p_tipo_documento = 'abono_proforma' THEN
        UPDATE empresa SET numero_actual_abono_factura_proforma_empresa = numero_actual_abono_factura_proforma_empresa + 1 WHERE id_empresa = p_id_empresa;
    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        UPDATE empresa SET numero_actual_factura_proforma_empresa = numero_actual_factura_proforma_empresa + 1 WHERE id_empresa = p_id_empresa;
    END IF;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_listar_empresas_facturacion` ()   BEGIN
    SELECT 
        id_empresa,
        codigo_empresa,
        nombre_empresa,
        nombre_comercial_empresa,
        nif_empresa,
        serie_factura_empresa,
        serie_abono_empresa,
        logotipo_empresa,
        verifactu_activo_empresa
    FROM empresa
    WHERE ficticia_empresa = FALSE
    AND activo_empresa = TRUE
    ORDER BY nombre_empresa;
END$$

CREATE DEFINER=`root`@`%` PROCEDURE `sp_obtener_empresa_ficticia_principal` ()   BEGIN
    SELECT 
        id_empresa,
        codigo_empresa,
        nombre_empresa,
        nombre_comercial_empresa,
        serie_presupuesto_empresa,
        numero_actual_presupuesto_empresa,
        logotipo_empresa,
        texto_pie_presupuesto_empresa
    FROM empresa
    WHERE empresa_ficticia_principal = TRUE
    AND activo_empresa = TRUE
    LIMIT 1;
END$$

CREATE DEFINER=`administrator`@`%` PROCEDURE `sp_obtener_siguiente_numero` (IN `p_codigo_empresa` VARCHAR(20), IN `p_tipo_documento` VARCHAR(30), OUT `p_numero_completo` VARCHAR(50))   BEGIN
    DECLARE v_serie         VARCHAR(10);
    DECLARE v_numero_actual INT;
    DECLARE v_anio          VARCHAR(4);
    SET v_anio = YEAR(CURDATE());
    IF p_tipo_documento = 'presupuesto' THEN
        SELECT serie_presupuesto_empresa, numero_actual_presupuesto_empresa + 1
        INTO   v_serie, v_numero_actual FROM empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);
    ELSEIF p_tipo_documento = 'factura' THEN
        SELECT serie_factura_empresa, numero_actual_factura_empresa + 1
        INTO   v_serie, v_numero_actual FROM empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);
    ELSEIF p_tipo_documento = 'abono' THEN
        SELECT serie_abono_empresa, numero_actual_abono_empresa + 1
        INTO   v_serie, v_numero_actual FROM empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);
    ELSEIF p_tipo_documento = 'abono_proforma' THEN
        SELECT serie_abono_factura_proforma_empresa, numero_actual_abono_factura_proforma_empresa + 1
        INTO   v_serie, v_numero_actual FROM empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);
    ELSEIF p_tipo_documento = 'factura_proforma' THEN
        SELECT serie_factura_proforma_empresa, numero_actual_factura_proforma_empresa + 1
        INTO   v_serie, v_numero_actual FROM empresa
        WHERE  codigo_empresa = p_codigo_empresa COLLATE utf8mb4_general_ci AND activo_empresa = TRUE;
        SET p_numero_completo = CONCAT(v_serie, '-', LPAD(v_numero_actual, 4, '0'), '/', v_anio);
    END IF;
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
  `permitir_descuentos_articulo` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Indica si el artículo acepta descuentos (1=Sí permite, 0=No permite)',
  `precio_editable_articulo` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Si 1, permite editar el precio unitario directamente en la línea de presupuesto',
  `id_impuesto` int DEFAULT NULL COMMENT 'Impuesto aplicable al artículo',
  `created_at_articulo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_articulo` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Tabla de articulos generales';

--
-- Volcado de datos para la tabla `articulo`
--

INSERT INTO `articulo` (`id_articulo`, `id_familia`, `id_unidad`, `codigo_articulo`, `nombre_articulo`, `name_articulo`, `imagen_articulo`, `precio_alquiler_articulo`, `coeficiente_articulo`, `es_kit_articulo`, `control_total_articulo`, `no_facturar_articulo`, `notas_presupuesto_articulo`, `notes_budget_articulo`, `orden_obs_articulo`, `observaciones_articulo`, `activo_articulo`, `permitir_descuentos_articulo`, `precio_editable_articulo`, `id_impuesto`, `created_at_articulo`, `updated_at_articulo`) VALUES
(21, 19, 5, 'MIC-INAL-001', 'Micrófono inalámbrico', 'Wireless Microphone', 'articulo_69833f1de4f2b.jpg', 25.00, NULL, 1, 0, 0, 'Incluye petaca transmisora, micrófono de mano y receptor. Requiere 2 pilas AA (no incluidas). Alcance hasta 100 metros en línea directa.', 'Includes bodypack transmitter, handheld microphone and receiver. Requires 2 AA batteries (not included). Range up to 100 meters in direct line.', 200, 'Verificar estado de baterías antes de cada alquiler. Comprobar frecuencias disponibles.', 1, 1, 0, 1, '2025-11-20 20:33:47', '2026-02-13 16:33:55'),
(23, 22, NULL, 'MIX-DIG-X32', 'Consola digital Behringer X32', 'Behringer X32 Digital Console', 'articulos/consola_x32.jpg', 180.00, NULL, 0, 1, 0, 'Consola digital de 32 canales con 16 buses auxiliares, 8 efectos integrados y grabación multipista USB. Incluye flight case y cable de alimentación. Requiere corriente trifásica.', '32-channel digital console with 16 aux buses, 8 integrated effects and USB multitrack recording. Includes flight case and power cable. Requires three-phase power.', 200, 'Verificar configuración de escenas. Resetear a valores de fábrica después de cada uso.', 1, 1, 0, NULL, '2025-11-20 20:33:47', '2026-01-20 17:35:38'),
(24, 21, 5, 'CABLE-XLR-10M', 'Cable XLR 10 metros', '10m XLR Cable', 'articulos/cable_xlr.jpg', 3.80, 0, 0, 0, 1, 'Cable balanceado XLR macho-hembra de 10 metros. Conductor OFC de baja impedancia.', '10m balanced XLR male-female cable. Low impedance OFC conductor.', 300, 'Verificar conectores y continuidad antes de alquilar.', 1, 0, 0, 1, '2025-11-20 20:33:47', '2026-02-13 16:43:03'),
(25, 22, 5, 'LED-PANEL-P3', 'Pantalla LED modular P3 interior (por m²)', 'P3 Indoor LED Panel (per sqm)', 'articulo_6924b18b230b6.png', 450.00, 0, 0, 1, 0, 'Pantalla LED modular de pixel pitch 3mm para interior. Resolución 111.111 píxeles/m². Brillo 1200 nits. Incluye estructura de soporte, procesador de video y cableado. Requiere técnico especializado para montaje.', 'P3 indoor modular LED screen. Resolution 111,111 pixels/sqm. Brightness 1200 nits. Includes support structure, video processor and cabling. Requires specialized technician for assembly.', 50, 'Revisar píxeles muertos. Calibrar color antes de cada evento. Requiere montaje 24h antes.', 1, 1, 0, NULL, '2025-11-20 20:33:47', '2025-11-24 19:27:07'),
(38, 61, NULL, 'LED-001', 'FOCO PAR LED RGB/W BATERIA + WIFI', 'BATTERY LED PAR RGBW WIFI', NULL, 30.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(39, 61, NULL, 'LED-002', 'FOCO PAR LED RGB PC90', 'LED PAR RGB PC90', NULL, 19.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(40, 61, NULL, 'LED-003', 'FOCO RECORTE DE LED 20/45', 'LED PROFILE SPOT 20/45', NULL, 60.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(41, 61, NULL, 'LED-004', 'FOCO PAR LED SPOT BATERÍA', 'BATTERY LED PAR SPOT', NULL, 25.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(42, 61, NULL, 'LED-005', 'FOCO PAR LED RGB/W BATERIA + WIFI TRITON B60', 'LED PAR TRITON B60', NULL, 30.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(43, 61, NULL, 'LED-006', 'TUBO 80 PARA PAR LED TRITON B60', 'TUBE 80 FOR TRITON B60', NULL, 25.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(44, 61, NULL, 'LED-007', 'FOCO PAR LED RGB', 'LED PAR RGB', NULL, 19.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(45, 62, NULL, 'ROB-001', 'PROYECTOR ROBOTIZADO BEAM', 'BEAM MOVING HEAD', NULL, 90.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(46, 62, NULL, 'ROB-002', 'PROYECTOR ROBOTIZADO WASH', 'WASH MOVING HEAD', NULL, 77.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(47, 62, NULL, 'ROB-003', 'PROYECTOR ROBOTIZADO SPOT', 'SPOT MOVING HEAD', NULL, 77.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(48, 63, NULL, 'CTR-001', 'MESA DE LUCES EUROLITE DMX OPERATOR 192', 'EUROLITE DMX CONTROLLER 192', NULL, 36.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(49, 63, NULL, 'CTR-002', 'MESA DE ILUMINACIÓN CHAMSYS MAGICQ MQ80', 'CHAMSYS MAGICQ MQ80', NULL, 178.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(50, 63, NULL, 'CTR-003', 'MAQUINA DE NIEBLA DMX', 'DMX FOG MACHINE', NULL, 55.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(51, 64, NULL, 'SOP-001', 'TORRE ELEVABLE 3 mts. MANUAL 30 kg CARGA / NEGRA', 'MANUAL LIFT TOWER 3M 30KG', NULL, 36.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(52, 64, NULL, 'SOP-002', 'PIE CON PEANA REDONDA', 'ROUND BASE STAND', NULL, 12.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(54, 65, NULL, 'PAN-001', 'PANTALLA PLASMA 65\"', 'PLASMA SCREEN 65\"', NULL, 285.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(55, 65, NULL, 'PAN-002', 'PANTALLA PLASMA 75\"', 'PLASMA SCREEN 75\"', NULL, 415.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(56, 65, NULL, 'PAN-003', 'PANTALLA PLASMA 55\"', 'PLASMA SCREEN 55\"', NULL, 180.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(57, 65, NULL, 'PAN-004', 'PANTALLA PLASMA 42\"', 'PLASMA SCREEN 42\"', NULL, 120.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(58, 65, NULL, 'PAN-005', 'SOPORTE SUELO PLASMA', 'PLASMA FLOOR STAND', NULL, 0.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(59, 65, NULL, 'PAN-006', 'PANTALLA DE LED 2,6 MM. 4X3 (48 MODULOS)', 'LED SCREEN 2.6MM 4X3', NULL, 2835.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(60, 65, NULL, 'PAN-007', 'PANTALLA DE LED 2,6 MM. 10X3 (120 MODULOS)', 'LED SCREEN 2.6MM 10X3', NULL, 7080.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(61, 66, NULL, 'CAM-001', 'CÁMARA SONY NX-200 4K', 'SONY NX-200 4K CAMERA', NULL, 180.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(62, 66, NULL, 'CAM-002', 'UNIDAD DE REALIZACIÓN (2 CÁMARAS) 4K', 'PRODUCTION UNIT 2 CAMERAS 4K', NULL, 1075.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(64, 67, NULL, 'VID-001', 'SPLITTER HDMI 1X4', 'HDMI SPLITTER 1X4', NULL, 25.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(65, 67, NULL, 'VID-002', 'MESA DE MEZCLAS VÍDEO ROLAND V-1HD', 'ROLAND V-1HD VIDEO MIXER', NULL, 120.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(66, 67, NULL, 'VID-003', 'SERVIDOR DE VÍDEO', 'VIDEO SERVER', NULL, 212.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(67, 67, NULL, 'VID-004', 'PROCESADOR P20 4K + CONSOLA DIRECTO U5', 'P20 4K PROCESSOR + U5 CONSOLE', NULL, 2125.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(71, 68, NULL, 'GRA-001', 'GRABADOR DIGITAL DE VIDEO HYPERDECK', 'HYPERDECK VIDEO RECORDER', NULL, 107.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(72, 68, NULL, 'GRA-002', 'DISCO DURO EXTERNO 1TB', 'EXTERNAL HARD DRIVE 1TB', NULL, 60.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(74, 69, NULL, 'TAR-001', 'TARIMA MODULAR 3X2X040 MOQUETA NEGRA', 'MODULAR STAGE 3X2X0.4M BLACK', NULL, 1150.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(75, 69, NULL, 'TAR-002', 'TARIMA MODULAR 16,5X2,5X0,60 + INTEGRADA', 'MODULAR STAGE 16.5X2.5X0.6M COMPLEX', NULL, 3760.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(76, 69, NULL, 'TAR-003', 'TARIMA MODULAR 22X2,50X1,50 PARA LED', 'MODULAR STAGE 22X2.5X1.5M FOR LED', NULL, 1050.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(77, 69, NULL, 'TAR-004', 'TARIMA MODULAR 2X2X150 CON MOQUETA NEGRA', 'MODULAR STAGE 2X2X1.5M', NULL, 250.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(81, 70, NULL, 'TRU-001', 'TRAMO DE TRUSS CUADRADO 29X29 GLOBAL 2 Mts', 'SQUARE TRUSS 29X29 2M', NULL, 18.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(82, 70, NULL, 'TRU-002', 'TRAMO DE TRUSS CUADRADO 29X29 GLOBAL 3 Mts', 'SQUARE TRUSS 29X29 3M', NULL, 18.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(83, 70, NULL, 'TRU-003', 'BASE PARA TRUSS CUADRADO 29X29 GLOBAL', 'BASE FOR SQUARE TRUSS 29X29', NULL, 25.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(84, 70, NULL, 'TRU-004', 'TRAMO DE TRUSS CUADRADO 29X29 GLOBAL (CUBO)', 'SQUARE TRUSS 29X29 CUBE', NULL, 36.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(85, 70, NULL, 'TRU-005', 'UFRAME 50 3MTS', 'UFRAME 50 3M', NULL, 120.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(86, 70, NULL, 'TRU-006', 'BASE PARA UFRAME', 'BASE FOR UFRAME', NULL, 25.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(88, 71, NULL, 'RIG-001', 'MOTOR VICINAY 500 KG', 'VICINAY MOTOR 500KG', NULL, 71.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(89, 71, NULL, 'RIG-002', 'RACK CONTROL MOTORES', 'MOTOR CONTROL RACK', NULL, 107.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(91, 72, 5, 'TEC-001', 'TÉCNICO AUDIOVISUALES (MAX. 8 HORAS)', 'AV TECHNICIAN 8H', '', 220.00, 0, 0, 0, 0, '', '', 200, '', 1, 0, 0, NULL, '2026-02-05 18:00:34', '2026-02-06 18:52:31'),
(92, 72, NULL, 'TEC-002', 'TÉCNICO AUDIOVISUALES LED (MAX. 8 HORAS)', 'LED TECHNICIAN 8H', NULL, 290.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(93, 72, NULL, 'TEC-003', 'TÉCNICO AUDIOVISUALES P20 (MAX. 8 HORAS)', 'P20 TECHNICIAN 8H', NULL, 315.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(94, 72, NULL, 'TEC-004', 'TÉCNICO AUDIOVISUALES OPERADOR CÁMARA (MAX. 8 HORAS)', 'CAMERA OPERATOR 8H', NULL, 220.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(95, 72, NULL, 'TEC-005', 'TÉCNICO AUDIOVISUALES REALIZADOR (MAX. 8 HORAS)', 'DIRECTOR 8H', NULL, 290.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(96, 72, NULL, 'TEC-006', 'DIETA TÉCNICO', 'TECHNICIAN PER DIEM', NULL, 25.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(98, 73, NULL, 'MAQ-001', 'GENI PLATAFORMA ELEVADORA', 'GENIE LIFT PLATFORM', NULL, 107.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(99, 74, NULL, 'VAR-001', 'MONTAJE Y DESMONTAJE', 'ASSEMBLY AND DISASSEMBLY', NULL, 2050.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(100, 74, NULL, 'VAR-002', 'MONTAJE Y DESMONTAJE CAJAS DE LUZ X 6', 'ASSEMBLY LIGHT BOXES X6', NULL, 1160.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(101, 74, NULL, 'VAR-003', 'DESPLAZAMIENTO', 'DISPLACEMENT', NULL, 950.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(102, 74, NULL, 'VAR-004', 'ORDENADOR PORTATIL', 'LAPTOP COMPUTER', NULL, 107.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(103, 74, NULL, 'VAR-005', 'PASA PAGINA', 'PAGE TURNER', NULL, 35.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(104, 74, NULL, 'VAR-006', 'PASA PAGINA MICROCUE2', 'MICROCUE2 PAGE TURNER', NULL, 70.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(105, 74, NULL, 'VAR-007', 'INTERCOM 5 PUESTOS ALAMBRICO', 'WIRED INTERCOM 5 STATIONS', NULL, 85.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(106, 74, NULL, 'VAR-008', 'INTERCOM INALAMBRICO 5 PUESTOS', 'WIRELESS INTERCOM 5 STATIONS', NULL, 285.00, 1, 0, 0, 0, NULL, NULL, 200, NULL, 1, 1, 0, NULL, '2026-02-05 18:00:34', '2026-02-05 18:00:34'),
(114, 19, 5, 'MIC-DINA-001', 'Micrófono Dinámico SM58', 'Dynamic Microphone', '', 12.00, NULL, 0, 0, 0, '', '', 200, '', 1, 1, 0, 1, '2026-02-05 18:04:13', '2026-02-05 18:04:13'),
(115, 74, 5, 'CAJ-INYEC-001', 'Caja de inyección', 'Injection box', '', 12.00, NULL, 0, 0, 0, '', '', 200, '', 1, 1, 0, 1, '2026-02-05 18:19:26', '2026-02-10 12:42:58'),
(116, 19, 5, 'EQUIP-MEGA-001', 'Equipo de megafonía 12 cajas (S/TCO)', 'PA SYSTEM 12 SPEAKERS (W/O TECH)', '', 445.00, NULL, 1, 0, 0, '', '', 200, '', 1, 1, 0, 1, '2026-02-05 18:24:58', '2026-02-06 12:17:34'),
(117, 19, 5, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '', 57.00, NULL, 0, 0, 0, '', '', 200, '', 1, 1, 0, 1, '2026-02-05 18:29:38', '2026-02-05 18:29:38'),
(118, 74, 5, 'SPOTIFY', 'REPRODUCTOR SPOTIFY', 'REPRODUCTOR SPOTIFY', '', 36.00, NULL, 0, 0, 0, '', '', 200, '', 1, 1, 0, 1, '2026-02-05 18:30:24', '2026-02-05 18:30:24'),
(119, 19, 5, 'CAJACUSTICA', 'CAJA ACUSTICA - KIT', 'INGLES - CAJA ACUSTICA - KIT', '', 100.00, NULL, 0, 0, 0, '', '', 200, '', 1, 1, 0, 1, '2026-02-06 12:15:15', '2026-02-23 18:58:28'),
(120, 19, 5, 'TRIPOCAJAACUS', 'TRIPODE CAJA ACUSTICA', 'TRIPODE CAJA ACUSTICA', '', 120.00, NULL, 0, 0, 0, '', '', 200, '', 1, 1, 0, 1, '2026-02-06 12:15:57', '2026-02-06 12:15:57'),
(122, 74, NULL, 'DESCUENTO', 'Descuento', 'Discount', '', 0.00, 0, 0, 0, 0, '', '', 200, '', 1, 0, 1, 1, '2026-02-24 19:49:21', '2026-02-25 05:49:55');

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
  `id_forma_pago_habitual` int UNSIGNED DEFAULT NULL COMMENT 'Forma de pago habitual del cliente. Se usará por defecto en nuevos presupuestos',
  `porcentaje_descuento_cliente` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de descuento habitual acordado con el cliente (0.00 a 100.00). Ejemplo: 10.00 = 10% de descuento',
  `observaciones_cliente` text,
  `exento_iva_cliente` tinyint(1) DEFAULT '0' COMMENT 'TRUE si el cliente está exento de IVA (operaciones intracomunitarias)',
  `justificacion_exencion_iva_cliente` text COMMENT 'Justificación legal de la exención de IVA (ARt. 25 Ley 37/1992, etc.)',
  `activo_cliente` tinyint(1) DEFAULT '1',
  `created_at_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Volcado de datos para la tabla `cliente`
--

INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(1, 'MELIA002', 'Melia Don Jaime', 'C/ Mayor, 24', '28001', 'Madrid', 'Madrid', 'B214515744444', '965262384', '', '', '', '', '', '', '', '', 11, 10.20, '', 0, NULL, 1, '2025-11-16 09:46:02', '2025-12-19 10:09:38'),
(2, 'PROV00', 'Fontaneria Klek', '', '232244', 'Madrid', 'Madrid', '1213414B', '629995058', '', '', 'cliente@gmail.com', '', 'Calle Comandante Martí 6', '', '', '', NULL, 0.00, '', 0, NULL, 0, '2025-11-18 17:20:22', '2025-12-03 17:10:20'),
(3, 'MELIA003', 'Prueba de nombre', '', '', '', '', 'B21451574', '', '', '', '', '', '', '', '', '', 3, 0.00, '', 0, NULL, 1, '2025-12-03 17:35:09', '2025-12-03 17:35:09'),
(4, 'CREA001', 'Hotel Asia Gardens', 'Avda. Eduardo Zaplana, S/N', '03502', 'Benidorm', 'Alicante', 'A-83058537', '', '', '', '', '', '', '', '', '', 8, 20.00, '', 0, NULL, 1, '2025-12-11 18:57:58', '2026-02-05 09:54:27'),
(5, 'CLIEXEC', 'Cliente exento de IVA (Intracomunitario)', 'Calle Mayor, 23', '18001', 'Madrid', 'Madrid', 'B12345668', '', '', '', '', '', '', '', '', '', 2, 0.00, 'Art 25 Ley 37/1992 - Operaciones introcomunitarias', 1, 'Art 25 Ley 37/1992 - Operaciones intracomunitarias', 1, '2026-02-13 18:13:57', '2026-02-14 12:28:38'),
(6, '0000', 'MDR MEETING SERVICES, S.L.U.', 'C/ Torno, 18 Nave 2 - P.I. El Canastell', '03690', 'San Vicente Del Raspeig', 'ALICANTE', 'B-54660345', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 60.00, NULL, 0, NULL, 1, '2026-02-18 13:02:47', '2026-03-02 10:17:54'),
(7, '0010', 'CAJA DE AHORROS DEL MEDITERRÁNEO (OBRA SOCIAL)', 'C/ SAN FERNANDO, 40', '03001', 'ALICANTE', 'ALICANTE', 'G-03046562', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:47', '2026-02-18 13:02:47'),
(8, '0013', '* HOTEL NH RAMBLA DE ALICANTE', 'Tomas López Torregrosa, 9', '03002', 'Alicante', 'Alicante', 'B-58511882', '965143659', '965206696', NULL, 'nhcristal@nh-hotels.com', 'NH HOTELES ESPAÑA, S.L. 0007 HOTEL NH RAMBLA DE ALICANTE', 'AVDA. TRAVESSERA DE LES CORTS, 144', '08028', 'BARCELONA', 'BARCELONA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:47', '2026-02-18 13:02:47'),
(9, '0021', 'SIDI SAN JUAN', 'La Doblada, S/N', '03540', 'Playa San Juan', 'Alicante', 'A-03/041092', '965161300', '965163346', NULL, 'comercial@sidisanjuan.com', 'SIDI HOTELES, S.A. HOTEL SIDI SAN JUAN', 'La Doblada, S/N', '03540', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:47', '2026-02-18 13:02:47'),
(10, '0033', '* VIAJES HISPANIA, S.A.', 'Avda. Maisonnave, 11 - 7º', '03003', 'Alicante', 'Alicante', 'A-03058799', '965228393 - (965141125 EXPLANADA)', '965229888', NULL, 'congresos@viajeshispania.es', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(11, '0039', 'SONIPROF, S.L.', 'Grupo Ind.l San Vicente, Nave 16 Los Juncos,11', '03690', 'San Vicente del Raspeig', 'Alicante', 'B-03507647', '965661616/639619671', '965666505', 'soniprof.com', 'info@soniprof.com', NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(12, '0045', 'LILLY, S.A.', 'Avda. de la Industria, 30', '28108', 'Alcobendas', 'Madrid', 'A-28/058386', '916635192', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(13, '0055', '* VIDEOGENIC BROADCAST, S.L.', 'C/ Maestro Marqués, 70', '03004', 'Alicante', 'Alicante', 'F54783535', '965215409', '965202952', NULL, NULL, 'Mistos CC coop. V.', 'C/. Maestro Marques, nun 70 Bjo.', '03004', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(14, '0056', 'EXCMO. AYUNTAMIENTO DE ALICANTE', 'Plaza del Ayuntamineto, S/N', '03001', 'Alicante', 'Alicante', 'P-0301400-H', '965149106 ALCALDIA 965148127 ESTHER PROTOCOLO', '965149233', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(15, '0075', 'PARADOR DE TURISMO DE JAVEA', 'Arenal, 2', '03730', 'Javea', 'Alicante', 'A-79855201', '965790200', '965790308', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(16, '0076', 'colegio', 'Avda. de Andalucia, 17', '03016', 'Alicante', 'Alicante', 'A-03452588', '965261899', '965261900', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(17, '0079', 'EXCMA. DIPUTACION PROVINCIAL DE ALICANTE', 'Avda. de la Estación, 6', '03002', 'Alicante', 'Alicante', 'P-0300000-G', '618682384', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(18, '0084', 'ESTUDIO CREACIÓN GENTE, S.L.', 'Plaza Stma. Faz, 3 - 2º', '03002', 'Alicante', 'Alicante', 'B-03414844', '902141288', '965201164', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(19, '0111', 'BACKUP EVENTS & COMMUN', 'c/. TUSET , 19 P.6º PTA. 3ª', '08006', 'BARCELONA', 'BARCELONA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(20, '0134', 'HOTEL MELIA ALICANTE', 'Plaza del Puerto, 3', '03001', 'Alicante', 'Alicante', 'E-03033842', '965148060 ML 965 205 000', '965140296 E', NULL, NULL, 'C.P. EXPLOTACIÓN HOTELERA MELIA ALICANTE', 'Plaza del Puerto, 3', '03001', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(21, '0135', 'FEDERACIO DE LES FOGUERES DE SANT JOAN', 'Bailen, 20 - 1º', '03002', 'Alicante', 'Alicante', 'G-54257548', '965145499', '965146383', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(22, '0141', 'VALENCIA VISIÓN, S.L.', 'Filipinas, 17 B', '46006', 'Valencia', 'Valencia', 'B-46443990', '963413153', '963417291', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:48', '2026-02-18 13:02:48'),
(23, '0146', 'RADIO POPULAR, S.A. (CADENA COPE)', 'la de Mendez Nuñez, 45 Entlo.', '03002', 'Alicante', 'Alicante', 'A-28281368', '965145268', '965145329', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(24, '0160', 'KIU IMAGEN Y COMUNICACIÓN, S.L.', 'Avda. Paías Valenciá, 9', '03201', 'Elche', 'Alicante', 'B-03621117', '965441000', '966662088', NULL, 'info@kiu.es', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(25, '0163', 'CARLSON WAGONLIT TRAVEL', 'C/ Princesa, nº 3 - 4ª planta', '28008', 'Madrid', 'Madrid', 'B-81861304', NULL, NULL, NULL, 'rambit@carlsonwagonlit.es', 'CARLSON WAGONLIT ESPAÑA, S.L. UNIPERSONAL', 'C/ JULIAN CAMARILLO Nº 4 EDIFICIO II', '28037', 'Madrid', 'Madrid', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(26, '0170', 'HOTEL ABBA CENTRUM', 'Pintor Lorenzo Casanova, 33', '03003', 'Alicante', 'Alicante', 'A-03468865', '965130440', '965928323', NULL, NULL, 'PROMOCIONES EURHOTEL', 'Pintor Lorenzo Casanova, 33', '03003', 'Alicante', 'Alicante', NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(27, '0171', 'PATRONATO MUNICIPAL DE TURISMO', 'C/ Cervantes, 3', '03002', 'Alicante', 'Alicante', 'P-0300035-C', '965149241 - 965147052', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(28, '0178', 'PSPV-PSOE', 'Blanquerias, 4', '46003', 'Valencia', 'Valencia', 'G-28477727', '600946306', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 6.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(29, '0194', 'UNIVERSIDAD DE ALICANTE', 'Carretera de San Vicente, S/Nº', '03690', 'San Vicente del Raspeig', 'ALICANTE', 'Q0332001G', '965909616 / 610488861', '965909649', NULL, 'tecnico.cultura@ua.es', 'UNIVERSIDAD DE ALICANTE', 'Carretera de San Vicente, S/Nº', '03690', 'San Vicente del Raspeig', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(30, '0198', 'HOTEL MEDITERRÁNEA PLAZA', 'Plaza del Ayuntamiento, 6', '03002', 'Alicante', 'Alicante', 'B-53402418', '965210188', '965206750', NULL, NULL, 'SEÑORIAL HOTELES, S.L.', 'Plaza del ayuntamiento, 6', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(31, '0220', 'FUNDESEM', 'Deportista Hermanos Torres, 4', '03016', 'Alicante', 'Alicante', 'G-03174018', '965266800 - 658794634 PEPE', '965165411', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(32, '0222', 'HOTEL ALBAHIA', 'Sol Naciente, 6', '03016', 'Alicante', 'Alicante', 'B-73083446', '965155979', '965155373', NULL, NULL, 'VIS HOTELES, S.L.', 'Avda. Antonio Fuentes, 1', '30840', 'Alhama de Murcia', 'Murcia', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(33, '0228', 'OBISPADO DE ORIHUELA - ALICANTE', 'C/ MARCO OLIVER, 5', '03009', 'Alicante', 'Alicante', 'Q-0300002-C', '965200472', '966758279', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 75.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(34, '0236', 'OFICINA DE ARMONIZACIÓN DEL MERCADO INTERIOR', 'Avda. de Europa, 4 Apdo. Correos, 77', '03008', 'Alicante', 'Alicante', 'V-03965324', '965139100 / 965139727 / 607110972 Pierre', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(35, '0248', 'COEPA', 'Plaza Ruperto Chapi', '03001', 'Alicante', 'Alicante', 'G-03085164', '965140267 / 630107255', '965140346', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(36, '0252', '*HOTEL TRYP CIUDAD DE ELCHE', 'Avda. Juan Carlos I, 5', '03203', 'Elche', 'Alicante', 'A-28674265', '966610033', '966610110', NULL, NULL, 'NUOVA, S.A.', 'Avda. Juan Carlos I, 5', '03203', 'Elche', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:49', '2026-02-18 13:02:49'),
(37, '0255', 'FUNDACIÓN OVSI', 'Bazan, 57', '03001', 'Alicante', 'Alicante', 'G-53032298', '965145454 / 639650264', NULL, NULL, 'gforner@ovsi.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(38, '0280', '*HOTEL TRYP GRAN SOL', 'Rambla de Mendez Nuñez, 3', '03002', 'Alicante', 'Alicante', 'A-78304516', '965203000', NULL, NULL, 'comer2sol@trypnet.com', 'MELIA HOTELS INTERNACIONAL, S.A.', 'Rambla de Mendez Nuñez, 3', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(39, '0301', 'ELECTRONICA D\'ALACANT, S.C.U.V.', 'Rio Serpis, S/N', '03013', 'Alicante', 'Alicante', 'F-53450789', '965211897 / 616943927', '965211897', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(40, '0306', 'MIGUEL DE GEA MATEO', 'Nueva Alta, 47', '03004', 'Alicante', 'Alicante', '21509692-T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(41, '0309', 'COLEGIO OFICIAL DE APAREJADORES Y ARQUITECTOS TECNICOS DE ALICANTE', 'C/ Catedratico Ferre Vidiella, 7', '03005', 'Alicante', 'Alicante', 'Q-0375006-D', '965924840', '965124404', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(42, '0314', 'SONIDO E ILUMINACIÓN LIMÓN, S.L.', 'C/ VILLAJOYOSA, 80 POL. IND. LA ALBERCA', '03530', 'Benidorm', 'Alicante', 'B-53123303', '966896197/ 629479363', '966807911', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(43, '0317', 'VIAJES OASIS', 'SOR ANGELA DE LA CRUZ, 8 - 1ºA', '28020', 'Madrid', 'Madrid', 'A/28854180', '915551119', '915553581', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(44, '0340', 'HOTEL HESPERIA DEL GOLF, S.L.', 'Avda. de las Naciones, S/N', '03540', 'Playa de San Juan', 'Alicante', 'B-62704036', '965268600', '965235008', NULL, 'eventos@hesperia-alicante.com', NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(45, '0351', 'HOTEL MELIA BENIDORM', 'Avda. Dr. Severo Ochoa, 1', '03503', 'Benidorm', 'Alicante', 'A-03189024', '966813710', '966802169', NULL, NULL, 'PROFITUR, S.A.', 'Avda. Dr. Severo Ochoa, 1', '03503', 'Benidorm', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(46, '0366', '*HOTEL SPA PORTA MARIS', 'Plaza puerta del mar,3', '03002', 'Alicante', 'Alicante', 'A-53331609', '965147021', '9652156945', NULL, 'c.galvan@hotelspaportamaris.com', 'PLAZA PUERTA DEL MAR, S.A.', 'Plaza puerta del Mar, 3', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(47, '0383', 'RESTAURANTE EL MAESTRAL', 'Andalucia, 18', '03013', 'Vistahermosa', 'Alicante', 'A-03462272', '965150376', '965161888', NULL, NULL, 'MAESTRAL, S.A.', 'Andalucia, 18', '03013', 'Vistahermosa', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(48, '0386', 'GENERALITAT VALENCIANA (DELEGACION DE ALICANTE)', 'Avda. Dr. Gadea, 10', '03003', 'Alicante', 'Alicante', 'S-4611001-A', '965935370 / 639636518', '965935391', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(49, '0391', 'COLEGIO LA MILAGROSA', 'Juan XXIII, 2', '03130', 'Agost', 'Alicante', 'R-0300117-I', '965694311', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:50', '2026-02-18 13:02:50'),
(50, '0402', 'ELECTRONICA Y ANTENAS NEGRI, S.L.', 'Blasco Ibañez, 35', '03140', 'Guardamar del Segura', 'Alicante', 'B-03951456', '966725970 / 607847766', '966725970', NULL, 'antenasnegri@terra.es', NULL, NULL, NULL, NULL, NULL, NULL, 35.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(51, '0412', 'KEY-PRO SERVICIOS TCOS PARA EL ESPECTACULO, S.L.', 'Rubens, 11 (Poligono Industrial Rabasa)', '03009', 'Alicante', 'Alicante', 'B-53755229', '965246160 / 616923095', '965910908', NULL, 'alquiler@key-pro.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(52, '0500', 'ASOCIACION DE LA EMPRESA FAMILIAR DE ALICANTE', 'C/ ORENSE Nº 10 (COEPA PUERTA 10)', '03003', 'Alicante', 'Alicante', 'G-53045621', '965131400 / 646556716', '965986921', NULL, 'aefa@alc.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(53, '0510', 'MARQ', 'Plaza Gomez Ulla, S/N', '03013', 'Alicante', 'Alicante', 'G-53491775', '965149002', '965149056', NULL, NULL, 'FUNDACIÓN COMUNIDAD VALENCIANA - MUSEO ARQUEOLÓGICO DE ALICANTE', 'Plaza Gomez Ulla, S/N', '03013', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(54, '0518', 'FEMPA', 'POL. AGUA AMARGA -  C/ BENIJÓFAR, 4- 6', '03008', 'Alicante', 'Alicante', 'G-03096963', '965150300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(55, '0527', 'LIEM - CDMC', 'C/ Santa Isabel, 52', '28012', 'Madrid', 'Madrid', 'Q-2818024-H', '917741000', '917741075', NULL, NULL, 'CDMC - INAEM', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(56, '0529', 'DISEÑO STUDIO 17, S.L.', 'Fontaneros, 29 Poligono Industrial Nº 2', '03130', 'Santa Pola', 'Alicante', NULL, '965414502 / 659455865', '965414502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(57, '0531', 'SINERESIS', 'Enriqueta Elizaizin, 6', '03007', 'Alicante', 'Alicante', 'B-53630778', '965107201 / 605179101 S/615229985 C', '965114998', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(58, '0537', 'EVENTO', 'Pintor Agrasot, 6 - 2º Izda.', '03001', 'Alicante', 'Alicante', 'B-53502506', '965145569', '965145568', NULL, NULL, 'PROMOCIONES Y EVENTOS DHAULAGIRI, S.L.', 'Pintor Agrasot, 6 - 2º Izda', '03001', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(59, '0538', 'CARPALICANTE, S.L.', 'German Bernacer, 27', '03203', 'Elche', 'Alicante', 'B-53067146', '965280030 / 686 461210', '965280030', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(60, '0547', 'PUBLICIDAD BENGALA', 'Rafael Altamira, 14', '03002', 'Alicante', 'Alicante', 'B-03960697', '965202600', '965202732', NULL, NULL, 'ESPADAS BELDA PUBLICIDAD, S.L', 'Rafael Altamira, 14', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(61, '0561', 'JLC CREATIVOS ASESORES, S.L.', 'Ctra. de Agost, 132', '03690', 'San Vicente del Raspeig', 'Alicante', 'B-53017760', '965666444', '965661700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(62, '0677', 'COORDINADORES DE TRASPLANTES', 'Pintor Baeza, S/N', '03010', 'Alicante', 'Alicante', 'S-4611001-A', '965933128  - 608462597 PURI - 647368670 MERCEDES', '965933130 - 965933129', NULL, NULL, 'GENERALITAT VALENCIANA', 'PLAZA DE MANISES S/N', '46003', 'VALENCIA', 'VALENCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:51', '2026-02-18 13:02:51'),
(63, '0678', 'PORTES ILUMINACIÓN Y SONIDO, S.L.', 'Plutón, 6', '03006', 'Alicante', 'Alicante', 'B-53848503', '965115188 / 607308303', '965115962', '607308305 MERE', 'portes@ilportes.com', NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(64, '0684', 'HOTEL HUERTO DEL CURA, S.L.', 'Porta de la Morera, 14', '03203', 'Elche', 'Alicante', 'B-53147799', '966612050', NULL, NULL, 'eventos@huertodelcura.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(65, '0698', 'PSA CENTRO', 'San Bernardo, 7 - 2º G', '03690', 'San Vicente del Raspeig', 'Alicante', 'X-446689-D', '966114064 / 661719083', NULL, 'www.psacentro.com', 'info@psacentro.com', 'FRANCISCO JAVIER YARDIN', 'San Bernardo, 7 - 2º G', '03690', 'San Vicente del Raspeig', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(66, '0699', 'AV MEDIOS, S.L.', 'Laguna del Marquesado, 19', '28021', 'Madrid', 'Madrid', 'B-80079114', '915064780 - Angel 606416725', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(67, '0700', 'CEMON', 'Plaza la Montañeta, 4 Entlo.', '03001', 'Alicante', 'Alicante', 'B-03819471', '965140413', '965140953', NULL, NULL, 'CENTRO EMPRESARIAL LA MONTAÑETA', 'Plaza Calvo Sotelo, 15 - 2º', '03001', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(68, '0713', 'HOTEL AMERIGO', 'Rafael Altamira, 7', '03002', 'Alicante', 'Alicante', 'B-31702053', '965146570', '965146571', NULL, 'amerigo.comercial@hospes.es', 'HESTIA HOTELES, S.L.', 'Rafael Altamira, 7', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(69, '0729', 'MBC SISTEMAS AUDIOVISUALES, S.L.', 'C/ Somontín, 104 - 4º C', '28033', 'Madrid', 'Madrid', 'B-83298760', '667652122', NULL, NULL, 'acanovas@mbcsistemas.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(70, '0746', 'CONSTRUCTORA SAN JOSE', 'Severo Ochoa, 20 - 1º C Pol. Ind. de Torrellano', NULL, 'Torrellano', 'Alicante', 'A-36006666', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(71, '0754', 'HOTEL ALBIR PLAYA', 'Carretera Vieja de Altea, 51', '03581', 'El Albir (Alfaz del Pi)', 'Alicante', 'B-53248944', '966864943 / 619763872 E', '966865570', NULL, NULL, 'MARSILANT PROMOCIONES, S.L.', 'C/ Camino Viejo de Altea, 51', '03581', 'Alfaz de Pi', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(72, '0758', 'ASOCIACION DE MUJERES PROGRESISTAS DE ASPE', 'San Pedro, 40', '03680', 'Aspe', 'Alicante', 'G-03978657', '610949468', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(73, '0765', 'EVENTOS MEDICOS Y SOCIALES, S.L', 'C/ Mayor de la Vila, 1', '03202', 'Elche', 'Alicante', 'B-53962163', '966610100 / 966615270', '965424802 - 966613495', NULL, NULL, 'EVENTOS MEDICOS Y SOCIALES, S.L', 'Mayor de la Vila, 1', '03202', 'Elche', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(74, '0772', 'C.P. TEATRO PRINCIPAL', 'Plaza Ruperto Chapí, S/N', '03001', 'Alicante', 'Alicante', 'R-03177418', '965203100', '965209723', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(75, '0773', 'ICEX', 'Alfonso X, 6 - 1º', '30008', 'Murcia', 'Murcia', 'Q-2891001-F', '968272238', '968234653', NULL, NULL, 'INSTITUTO ESPAÑOL DE COMERCIO EXTERIOR', 'Alfonso X, 6 - 1º', '30008', 'Murcia', 'Murcia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:52', '2026-02-18 13:02:52'),
(76, '0774', 'HOTEL LEUKA, C.B.', 'Segura, 23', '03004', 'Alicante', 'Alicante', 'E-03027182', '965202744', '965141222', NULL, 'info@hotelleuka.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(77, '0788', 'FEDERACIÓN PROVINCIAL DE COFRADIA DE PESCADORES DE ALICANTE', 'Pintor Aparicio, 3 Entlo.', '03003', 'Alicante', 'Alicante', 'G-03082427', '629708801 - 965928730', '965131358', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(78, '0791', 'PROTRACK 3, S.L.', 'C/. GENERAL ESPARTERO, 32-34', '03012', 'Alicante', 'Alicante', 'B-54000476', '965254462/ 607682434 JOSE / 661864370 MANOLO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(79, '0793', 'HOTEL SH VILLA GADEA', 'Partida. Villa Gadea, S/N', '03599', 'Altea', 'Alicante', 'B-96683347', '966817100 SILVIA 607759270', '965845541', NULL, 'banquetes.villa.gadea@sh-hoteles.com', 'VILLA GADEA ALTEA, S.L.', 'C/ DOCTOR ROMAGOSA Nº 1 ENTRESUELO', '46002', 'VALENCIA', 'VALENCIA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(80, '0794', 'HOTEL MELIA ALTEA HILLS', 'Urbanización Altea Hills', '03599', 'Altea', 'Alicante', 'B-97387294', '966881006', '966881024', NULL, 'comercial.altea.hills@sh-hoteles.com', 'NEREIDA MEDITERRÁNEA, SLU', 'Doctor Romagosa, 1', '46002', 'Valencia', 'Valencia', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(81, '0795', 'C.B. MAR ALICANTE', 'Padre Recaredo de los Rios, 19', '03005', 'Alicante', 'Alicante', 'G-53137006', '966387470 / 610437322', '966387471', NULL, 'antonio-nieto@maralicante.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(82, '0798', 'ESPECTACLES MARINA ALTA, S.L.', 'Princep, 4', '03750', 'Pedreguer', 'Alicante', 'B-03908100', '902160011', '966456705', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(83, '0808', 'HOTEL EL RODAT', 'Ctra. Cabo de la Nao, S/N', '03730', 'Javea', 'Alicante', 'B-53387353', '966470710', '966471550', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(84, '0811', 'SINDI PRODUCCIONES', 'Ptda. Baya Alta, Poligono 1 Nº 175', '03292', 'Elche', 'Alicante', 'B-53833042', '965456601', '965455113', NULL, 'gerencia@sindiproducciones.es', 'SINDI CREATIVE GROUP, S.L.', 'Ptda. Baya Alta, Poligono 1 Nº 175', '03292', 'Elche', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(85, '0812', 'HOTEL TORRE SANT JOAN', 'La Huerta, 1', '03550', 'Playa San Juan', 'Alicante', 'B-03923646', '965940973', '965658519', NULL, NULL, 'RESIDENCIA SUIZA, S.L.', 'La Huerta, 1', '03550', 'Playa San Juan', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(86, '0813', 'ASOCIACIÓN PROVINCIAL DE DIABETICOS DE ALICANTE', 'Aaiun, 21 Local 12', '03010', 'Alicante', 'Alicante', 'G-03311131', '965257493', '965257493', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(87, '0816', 'VENTA VISUAL, S.A.', 'Ctra. Sta Catalina, Km 1200 Torre los Morenos, 128', '30012', 'Murcia', 'Murcia', 'A-30472971', '968842084 / 609228542 F/ 609229568 J/ 609223117 Q', '968842006', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(88, '0825', 'PORTAVOZ COMUNICACIONES INTEGRADAS, S.L.', 'Plaza de los Apostoles, 5 Entlo.', '30001', 'Murcia', 'Murcia', 'B-30474100', '968217060 // 629859828 CARLOS RECIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:53', '2026-02-18 13:02:53'),
(89, '0826', 'MOANA PRODUCCIONES, S.L.', 'Pintor Velazquez, 58 - 4º B', '03004', 'Alicante', 'Alicante', 'B-53933511', '966355006 / 608072072', NULL, NULL, 'info@moanasl.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(90, '0829', 'PARROQUIA SAN PABLO', 'Aurelio Ibarra, 11', '03009', 'Alicante', 'Alicante', 'R-0300206-J', '965242635', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(91, '0830', 'TEMPE, S.A.', 'Severo Ochoa, 22 - 28 Elche Parque Industrial', '03203', 'Elche', 'Alicante', 'A-15234065', '966657508', '966657002', NULL, 'belenag@tempe.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(92, '0837', 'HABILIS BUREAU, S.L.', 'Almorida, 2 4º - 1º', '03203', 'Elche', 'Alicante', 'B-54065313', '966615307', '966615798', NULL, 'enriquebueno@habilisbureau.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(93, '0843', 'PEMARSA, S.A.', 'Partida Canastell, I-98-100', '03690', 'San Vicente del Raspeig', 'Alicante', 'A-03073756', '965675070', '965666793', NULL, 'esperanza.lorenzo@pcg.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(94, '0852', 'HOTEL LEVANTE CLUB', 'Avda. Dr. Severo Ochoa, 3 - B', '03503', 'Benidorm', 'Alicante', 'B-03164126', '966830000', '966830086', NULL, NULL, 'LEVANTE CLUB, S.L.', 'Avda. Dr. Severo Ochoa, 3 - B', '03503', 'Benidorm', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(95, '0857', 'VIAJES VINCIT, S.L.', 'Canovas del Castillo, 22 2ª Planta', '36202', 'Vigo', 'Pontevedra', 'B-36860914', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(96, '0868', 'PUBLIASA', 'Avda. Ramon y Cajal, 8', '03013', 'Alicante', 'Alicante', 'A-03291937', '965229890 / 696910169', '965922721', NULL, NULL, 'PUBLICIDAD ALICANTINA, S.A.', 'Avda. Denia, 155', '03015', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(97, '0869', 'HOTEL HESPERIA LUCENTUM', 'Avda. Alfonso X el Sabio, 11', '03002', 'Alicante', 'Alicante', 'A-08817702', '966590700', '966590710', NULL, 'eventos@hesperia-lucentum.com', 'HOTELERA DE LEVANTE, S.A.', 'Avda. Alfonso X el Sabio, 11', '03002', 'Alicante', 'Alicante', NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(98, '0872', 'SOROLLA FILM, S.A.', 'Plaza del Ayuntamiento, 2 14', '46002', 'Valencia', 'Valencia', 'A-97372189', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(99, '0873', 'CENTRO DE ESTUDIOS CIUDAD DE LA LUZ', 'Avda. de la Libertad, 79 - 3º A', '03140', 'Guardamar del Segura', 'Alicante', 'B-54078035', NULL, NULL, NULL, NULL, 'NUCT MEDITERRÁNEO, S.L.', 'Avda. de la Libertad, 79 - 3º A', '03140', 'Guardamar del Segura', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(100, '0879', 'CLUB DE TENIS T-7', 'Partida de Orgegia, S/N', '03015', 'Alicante', 'Alicante', 'B-82763889', NULL, NULL, NULL, NULL, 'T-7 RIVIERA CENTRO DE ALTA TECNIFICACIÓN DE TENIS, S.L.', 'Partida de Orgegia, S/N', '03015', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(101, '0882', 'VERONICA GONZALEZ PEREZ', 'Alfonso de Rojas, 6 Entlo. 15', '03004', 'Alicante', 'Alicante', '25129127-V', '965216929', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(102, '0886', 'FASCINA-T PRODUCCIONES, S.L.', 'Paseo de los Olmos, 14 - 1º A', '20016', 'San Sebastian', NULL, 'B20707881', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:54', '2026-02-18 13:02:54'),
(103, '0890', 'METODOS CAMPO, S.L.', 'Bazan, 21 Entlo. Dcha.', '03001', 'Alicante', 'Alicante', 'B-53990834', '965147360', '965147361', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(104, '0896', '*HOTEL VILLA AITANA', 'Avda. Alcalde Eduardo Zaplana Hernandez Soro, 7', '03502', 'Benidorm', 'Alicante', 'A-96730254', '966815000', '966870113', NULL, NULL, 'XERESA GOLF, S.A.', 'Antonio Maura, 16', '28014', 'Madrid', 'Madrid', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(105, '0903', 'SALONES DEL MAR', 'Plaza Puerta del mar, 3', '03002', 'Alicante', 'Alicante', 'B-53642609', '965212744', '965200462', NULL, 'comercial@salones del mar.com', 'ESCOLLERA DE LEVANTE, S.L.', 'Plaza Puerta del mar, 3', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(106, '0905', 'HORIZONTE MUSICAL', 'Capitan Amador, 9 - 1º Izqda', '03004', 'Alicante', 'Alicante', 'B-53187365', '965251755 / 650927729', NULL, NULL, NULL, 'ERNESTO R. HUESCA GARCIA PRODUCCIONES ARTISTICAS, S.L.', 'Capitan Amador, 9 - 1º Izqda', '03004', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(107, '0916', 'ARROYO SONIDO, S.L.', 'Prolongación Paseo Alfonso XIII, 12', '30203', 'Cartagena', 'Murcia', 'B-30695597', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 35.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(108, '0941', 'ACCIÓN VISUAL, S.L.', 'Asturias  Nave 6 Apdo. Correos, 84 Pol. Ind. LT2', '30562', 'El ceuti', 'Murcia', 'B-73190035', '968690251/689396039/676959768 ANA/CARLOS 646440863', '968690536', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(109, '0948', 'VIDEOCENTER', 'Cerrajeros, 10 Pol. Ind. Pinares', '28670', 'Villaviciosa de Odon', 'Madrid', 'A-78606985', NULL, NULL, NULL, NULL, 'VIDEO CENTER INTERNACIONAL, S.A.', 'Cerrajeros, 10 Pol. Ind. Pinares', '28670', 'Villaviciosa de Odon', 'Madrid', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(110, '0951', 'GRUPO GEYSECO', 'Universidad, 4', '46003', 'Valencia', 'Valencia', 'B65687733', '963524889 / 638405578 SARA', '963942558', NULL, NULL, 'GEYSECO, S.L.', 'Universidad, 4', '46003', 'Valencia', 'Valencia', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(111, '0962', 'SUPER FOTO GINER, S.L.', 'C/ Gerona, 11', '03001', 'Alicante', 'Alicante', 'B-03996980', '965216858', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(112, '0968', 'ASOCIACIÓN FUTURA FILMS', 'Avda. de Alcoy, 9', '03004', 'Alicante', 'Alicante', 'G-53187399', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(113, '0969', 'AMAZING LAYINO, S.L.', 'Pza. Manuel Gomez Moreno, 2 Edif Alfredo Mahou P23', '28020', 'Madrid', 'Madrid', 'B-84785443', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(114, '0970', 'OICE CONGRESOS Y EVENTOS, S.L.', 'Cura Femenia, 14 Bajo', '46006', 'Valencia', 'Valencia', 'B-97453947', '963819912', '963819913', NULL, 'direccion@oicecongresos.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(115, '0971', 'ANTONIO ALARCON CERDAN', 'Lepanto, 3 - 20', '03688', 'Hondon de la nieves', 'Alicante', '22115356-M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:55', '2026-02-18 13:02:55'),
(116, '0972', 'ALQUIBLA, S.L.', 'Pintor Villacis, 4 Entresuelo', '30003', 'Murcia', 'Murcia', 'B-30160246', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(117, '0973', 'FD CONSULTORES, S.L.', 'Avda. Federico Soto, 13 - 1º E', '03003', 'Alicante', 'Alicante', 'B-53017075', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(118, '0974', 'LAQANT PRODUCCIONES TÉCNICAS, S.L.', 'Rubens, 11 Poligono Industrial Rabasa', '03009', 'Alicante', 'Alicante', 'B-54226725', '965254462', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(119, '0975', 'NUEVA ECONOMIA FORUM, S.L.', 'Sevilla, 6 - 4º Planta', '28014', 'Madrid', 'Madrid', 'B-82646217', NULL, NULL, NULL, 'ana.murillo@foronuevaeconomia.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(120, '0976', 'N.V. SOVIFO PRODUCTION, S.A.', 'BDL SCHMIDT, 26', '1040', 'BRUSSELS', 'BRUSSELS', 'BE42618267', NULL, NULL, NULL, 'cathy@sovifo.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(121, '0977', 'YOLANDA ROSELLO NAVARRO', 'Abeto, 2 Urb. Los Girasoles', '03690', 'San Vicente del Raspeig', 'Alicante', '21471288-Y', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(122, '0978', 'CONCEPTO NAU EQUIPO VIRTUAL, S.L.', 'San Antonio, 11', '46817', 'Estubery', 'Valencia', 'B-97874879', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(123, '0979', 'MALVARROSA MEDIA, S.L.', 'Avda. Ausias March, 21 Bajo', '46016', 'Tabernes Blanques', 'Valencia', 'B-97030761', '961864031 / 616747756-615432995 OCTAVIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(124, '0980', 'AGENTUR KARLINA, S.L.', 'Avda. de España, 41 - 10º G', '02002', 'Albacete', 'Albacete', 'B-02198299', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(125, '0981', 'NUESTRO PEQUEÑO MUNDO VIAJES, S.L.', 'C/ Pérez Medina, 16', '03007', 'Alicante', 'Alicante', 'B-53056636', '965921939-D  965130228', '965229907', NULL, 'josema@npmundo.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(126, '0982', 'HIJO DE TOLDOS VAZQUEZ, S.L.U.', 'Avda. Novelda, 237', '03009', 'Alicante', 'Alicante', 'B-53770988', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(127, '0983', '*HOTEL NH ALICANTE', 'Avda. Mexico, 18', '03008', 'Alicante', 'Alicante', 'B-58511882', '965108140', '965110655', NULL, 'p.nogales@nh-hotels.com', 'NH HOTELES ESPAÑA, S.L. 0322 HOTEL NH ALICANTE', 'Travessera de les Corts, 144', '08028', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(129, '0984', 'DIERESIS & CCI PUBLICIDAD Y MEDIOS, S.L.', 'Rambla de Mendez Nuñez, 18 - 1º', '03002', 'Alicante', 'Alicante', 'B-54336912', '965206699 / 965206235 679616719 E JR.659391228 E.', '965202224', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:56', '2026-02-18 13:02:56'),
(131, '0985', 'MEDICAPRO, S.L.', 'Galera, 18', '28042', 'Madrid', 'Madrid', 'B-82055104', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(133, '0986', 'IGNIS ARDENS', 'Avda. de Andalucia, 17', '03016', 'Alicante', 'Alicante', 'Q-0300482-G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(135, '0987', 'PUMA', 'Avda. de Novelda, 169', '03206', 'Elche', 'Alicante', 'A-28368769', '966660074 // 676483767', '965449494', NULL, NULL, 'ESTUDIO 2000, S.A.', 'Avda. de Novelda, 169', '03205', 'Elche', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(136, '0988', 'PROTECTIVE COMFORT GROUP, S.L.', 'Partida de Canastell, I-98-100', '03690', 'San Vicente del Raspeig', 'Alicante', 'B-53463733', '965675070', '965666793', NULL, 'peter.gorne@pcg.es', 'PROTECTIVE COMFORT GROUP, S.L.', 'Partida de Canastell, I-98-100', '03690', 'San Vicente del Raspeig', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(138, '0989', 'VISISONOR COOP V', 'C/ Santa Maria Mazarelo, 25 - 4º C', '03007', 'Alicante', 'Alicante', 'F-53350518', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(140, '0990', 'SPAT MODULAR, S.L.', 'C/ Princep, 4', '03750', 'Pedreguer', 'Alicante', 'B-03837929', '902160011', '966456705', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 35.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(142, '0991', 'SOL BANK', 'Plaza San Roc, 20', NULL, 'Sabadell', NULL, 'A-08000143', NULL, NULL, NULL, NULL, 'BANCO SABADELL, S.A.', 'Plaza San Roc, 20', NULL, 'Sabadell', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(144, '0992', 'TELE 7', 'C/ Concepción Arenal, 39', '03201', 'Elche', 'Alicante', 'B-53186474', NULL, NULL, NULL, NULL, 'COMUNICACIÓN AUDIOVISUAL EDITORES, S.L.', 'C/ Concepción Arenal, 39', '03201', 'Elche', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(145, '0993', 'OPTICAS CONDE LUMIARES, S.L.', 'Avda. Conde Lumiares, 35', '03010', 'Alicante', NULL, 'B-03481335', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(147, '0994', 'ASOCIACION DE PARKINSON DE ALICANTE', 'C/ Andromeda, 26 B', '03007', 'Alicante', 'Alicante', 'G-53615795', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(149, '0995', 'GRUPO ZAMBOO', 'Francisco Verdu, 30 Bajo Dcha.', '03010', 'Alicante', 'Alicante', 'B-53935136', NULL, NULL, NULL, NULL, 'RAYDESPA, S.L.', 'Francisco Verdu, 30 Bajo Dcha.', '03010', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(151, '0996', 'INTERPROFIT', 'Passatge de la Concepción, 7-9 2º', '08008', 'Barcelona', 'Barcelona', 'B-60248127', '934670232 - 615408674 ANA FERNANDEZ', '934670233', NULL, 'ana.crespo@interprofit.es', 'R.P. UNO, S.L.', 'Passatge de la Concepción, 7-9 2º', '08008', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(152, '0997', 'BLOCK DE IDEAS, S.L.', 'C/ Diputación, 180 4ª Planta', '08011', 'Barcelona', 'Barcelona', 'B-59562199', '934155228', '934159723', 'www.blockdeideas.es', 'jpratt@blockdeideas.es', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(154, '0998', '*GRAN HOTEL SOL Y MAR', 'C/ Benidorm, 1', '03710', 'Calpe', 'Alicante', 'B-54211834', '965831762', '965833182', 'www.granhotelsolymar.com', NULL, 'TRADIA HOTEL, S.L.', 'C/ Benidorm, 1', '03710', 'Calpe', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:57', '2026-02-18 13:02:57'),
(156, '0999', 'AUTOMOVILES SALA RODRIGUEZ, S.A.', 'Avda. de Denia, 145', '03015', 'Alicante', 'Alicante', 'A-03063674', '663047835', '965266923', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(158, '1000', 'COLOR COMUNICACIÓN', 'Pol. Ind. Atalayas Avda. de la Antigua Peseta, 136', '03114', 'Alicante', 'Alicante', 'B-54100938', '965102511 / 651813198 P / 666524970 E', '965113578', NULL, NULL, 'GESTIÓN DE PROYECTOS COLOR COMUNICACIÓN, S.L.', 'Pol. Ind. Atalayas Avda. de la Antigua Peseta, 136', '03114', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(159, '1001', 'ILUSTRE COLEGIO DE ECONOMISTAS DE ALICANTE', 'C/ San Isidro, 5', '03003', 'Alicante', 'Alicante', 'Q-0361002-I', '965140887', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(161, '1002', 'PROTOCOL EASY, S.L.', 'Avda. Pintor Xavier Soler, 11 - 4º B', '03015', 'Alicante', 'Alicante', 'B-54242359', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(163, '1003', 'ELBA EVENTOS, S.L.', 'C/ Miguel Yuste, 11', '28037', 'Madrid', 'Madrid', 'B-80544372', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(165, '1004', 'EL MUNDO', 'C/ Eduardo Bosca, 33', '46023', 'Valencia', 'Valencia', 'A-81819179', NULL, NULL, NULL, 'clara.civera@elmundo.es', 'EDITORA DE MEDIOS DE VALENCIA, ALICANTE Y CASTELLON, S.A.', 'C/ Eduardo Bosca, 33', '46023', 'Valencia', 'Valencia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(166, '1005', 'SOCIEDAD ESPAÑOLA DE RADIODIFUSION, S.A. EMISORA RADIO ALICANTE', 'C/ Calderon de la Barca, 26', '03004', 'Alicante', 'Alicante', 'A-28016970', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(168, '1006', 'RESPIRA VIDEO, S.L.', 'C/ Alexander Felming, 12 Nave 10', '46980', 'PARQUE TECNOLOGICO PATERNA', 'Valencia', 'B-97877211', '961366534/676512130', '961318544', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(170, '1007', 'AQUALANDIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(172, '1008', 'TRADUCCIONES TRAYMA, S.L.U.', 'AVDA. DE LA ESTACIÓN, Nº 27', '03003', 'Alicante', 'Alicante', 'B-54297593', '965916929', NULL, NULL, 'admin@trayma.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(174, '1009', 'ALIEVENT, S.L.', 'C/. Lira, 3 Bjo.', '03010', 'Alicante', 'Alicante', 'B-53907721', '965105149', NULL, NULL, 'comercial@alievent.net', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(175, '1010', 'HOTEL VACANZA SUN BEACH', 'Avda. del Puerto, 3', NULL, 'Guardamar', 'Alicante', 'B-53351243', NULL, NULL, NULL, NULL, 'VACANZA RENT, S.L.', 'Avda. del Puerto, 3', NULL, 'Guardamar', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(177, '1011', 'GENERAL DE AUDIOVISUALES, S.L.', 'Ciudad de Bari, 6', '03011', 'Alicante', 'Alicante', 'B-53906715', '965256698', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:58', '2026-02-18 13:02:58'),
(179, '1012', 'VOLVO EVENT MANAGEMENT UK TLD', NULL, '03001', 'WHITELEY', 'REINO UNIDO', 'GB697 605093', '966011173 - 677888691 MARTA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(181, '1013', 'COFRUSA', 'Ctra. de Pliego, 53', '30170', 'Mula', 'Murcia', 'A-30012371', '968395400', NULL, NULL, NULL, 'CONSERVAS Y FRUTAS, S.A.', 'Ctra. de Pliego, 53', '03170', 'Mula', 'Murcia', NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(182, '1020', 'G.V. C. DE GASTOS SERVICIO TERRITORIAL DE CONSUMO DE ALICANTE', 'Rambla de Medez Nuñez, 41 - 5º Planta', '03001', 'Alicante', 'Alicante', 'S-4611001-A', '966478166', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(184, '1021', 'ELECTRONICA BERNA, S.L.', 'C/ Cardenal Cisneros, 3 Bajo', '03400', 'Villena', 'Alicante', 'B-53075107', '965806440 / 625625999', '965340426', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(186, '1022', 'CONCEJALIA DE FIESTAS DEL AYTO. ALICANTE', 'C/. RAFAEL ALTAMIRA 2, 5º', '03001', 'ALICANTE', 'ALICANTE', 'P-0301400-H', '965230722 - 629480315 E', '965230725', NULL, NULL, 'EXCMO. AYUNTAMIENTO DE ALICANTE CONCEJALIA DE FIESTAS', 'PLAZA DEL AYUNTAMIENTO, S/N', '03002', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(188, '1023', 'SANOFI-AVENTIS', NULL, NULL, NULL, NULL, NULL, '617409103', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(190, '1024', 'INSTITUTO  CYMA  (HOSPITAL MEDIMAR)', 'Avda. de Denia, 78 - 4º', '03016', 'Alicante', 'Alicante', 'B-54459516', '965269104', NULL, NULL, NULL, 'INSTITUTO DAVO, S.L', 'Avda. de Denia, 78 - 4º planta', '03016', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(191, '1025', 'TACTICS MD', 'C/. PARÍS 162, PRAL. 1ª', '08036', 'BARCELONA', 'BARCELONA', 'B-63690846', '934511724', '934514366', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(193, '1026', 'VIAJES IBERIA', 'Gran Via Ramón y Cajal, 61', '46007', 'Valencia', 'Valencia', 'A-07001415', '963826164 / 659802514', '963826328', NULL, NULL, 'VIAJES IBERIA, S.A.U.', 'C/ RITA LEVI - EDIF.ORIZONA CORPORACIÓN - PARC BIT', '07121', 'PALMA DE MALLORCA', 'ILLES BALEARS', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(195, '1027', 'GRUPOSKALA', 'AVDA. DE LA ESTACION , 13 3º DCHA', '03003', 'ALICANTE', 'ALICANTE', NULL, '965923409', '965925841', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(197, '1028', 'FOGUERA SANT BLAI-LA TORRETA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(199, '1029', 'AZTESS & CONGRESOS', 'GRAN VIA MARQUES DEL TURIA, 63-PTA 7', '46005', NULL, 'VALENCIA', NULL, '963748176', '96352352', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(200, '1030', 'AULA DE CULTURA DE LA CAM', 'Dr. Gadea, 1', '03003', 'Alicante', 'Alicante', 'G-03046562', '607308305', NULL, NULL, NULL, 'AULA DE CULTURA CAJA DE AHORROS DEL MEDITERRÁNEO', 'Dr. Gadea, 1', '03003', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:02:59', '2026-02-18 13:02:59'),
(202, '1031', 'EVENTISIMO, S.L.U.', 'C/ Balance, 16 Pol. Ind. Pisa Mairena del Aljarafe', '41927', 'Sevilla', 'Sevilla', 'ESB38651626', '902101390', NULL, 'www.eventisimo.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(204, '1032', 'G.V. CENTRO DE GASTOS SERVICIO TERRITORIAL DE COMERCIO DE ALICANTE', 'C/. RAMBLA DE MENDE NUÑEZ, 41 4ª PLANTA', '03001', 'ALICANTE', 'ALICANTE', 'S-4611001-A', '966478140', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(206, '1033', 'CA & COACHING Y EVENTOS, S.L.', NULL, NULL, NULL, NULL, NULL, '952060106', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(207, '1034', 'VIDEOS MURCIA', NULL, NULL, NULL, NULL, NULL, '968342245 / 626136065', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(209, '1035', 'DITEC COMUNICACIÓN, S.L.', 'C/ Sicília, 368 Bjos.', '08025', 'Barcelona', 'Barcelona', 'B-61130019', '932656517', '932312413', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00');
INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(211, '1036', 'ANA ROCA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(213, '1037', '* KT EVENTS', 'C/ Alegre de Dalt, 55 - 4º B1', '08024', 'Barcelona', 'Barcelona', NULL, '932405240', '932853757', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(215, '1038', 'BIGUENT, S.L.', 'Arco de la Frontera, 21', '28023', 'Aravaca', 'Madrid', NULL, '913573978', '913071129', 'www.biguent.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(216, '1039', 'NOBEL BIOCARE IBERICA, S.A.', 'Moll de Barcelona, S/N w Trade Center Edif. Est 7ª', '08039', 'Barcelona', 'Barcelona', 'A-58384397', '935088800', '935088801', 'www.nobelbiocare.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(218, '1040', 'FUNDACIÓN MANUEL PELAEZ CASTILLO', 'Avda. Alfonso el Sabio, 37 - 7º', '03001', 'Alicante', 'Alicante', 'G-53934279', '965143835', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(220, '1041', 'TELEMAG DE LORCA', 'CAMPILLO-PUENTE BOTERO 104', '30813', 'LORCA', 'MURCIA', NULL, '968406819 / 609900466', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(222, '1042', 'RPA IMAGEN Y COMUNICACIÓN', 'C/ Columela, 9', '28001', 'Madrid', 'Madrid', NULL, '915781066', '915775148', 'www.rpaevents.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(223, '1043', 'RAFAEL BONET', NULL, NULL, NULL, NULL, NULL, '965205533 // 657285079', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:00', '2026-02-18 13:03:00'),
(225, '1044', 'SONOSTUDI', 'C/ Almogavers, 166', '08018', 'Barcelona', 'Barcelona', NULL, '938207863 / 670245498', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(227, '1045', 'AUDISHOW', NULL, NULL, NULL, NULL, NULL, '965560184 // 650921516', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(229, '1046', 'JUAN CARLOS ALCOLEA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(230, '1047', 'UNIVERSIDAD DE ALICANTE (DPTO. ANALISIS ECONOMICO APLICADO)', 'Campus de San Vicente del Raspeig, Apdo. 99', '03080', 'San Vicente del Raspeig', 'Alicante', 'Q-0332001-G', '965903400 (EXT 2627)', '965909487', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(232, '1048', 'UNIVERSIDAD MIGUEL HERNANDEZ DE ELCHE', 'Avenida de la Universidad, S/N', '03202', 'Elche', 'Alicante', 'Q-5350015-C', '966658781', NULL, NULL, NULL, 'UNIVERSIDAD MIGUEL HERNANDEZ DE ELCHE, V ICERRECTORADO DE ASUNTOS ECONOMICOS, EMPLEO Y RELACIÓN CON LA EMPRESA', 'Avenida de la Universidad, S/N', '03202', 'Elche', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(234, '1049', 'CYMATIC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(236, '1050', 'SONORIZACIONES ANGEL', 'AVDA. DE LOS PESCADOS,10', '03699', 'MORALET', 'ALICANTE', 'B54691506', '609631271 - 965673491', NULL, NULL, NULL, 'SONORIZACIONES ANGEL, S.L.', 'AVDA. DE LOS PESCADOS,10', '03699', 'MORALET', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(238, '1051', 'DATA VIDEO', 'C/ SAN VICENTE, 20 1º', '03004', 'ALICANTE', 'ALICANTE', 'B-53778189', '965210596', '966590330', NULL, NULL, 'DATAVIDEO NUEVAS TECNOLOGIAS, S.L.', 'C/ TAQUIGRAFO MARTI,14', '03004', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(239, '1052', 'SH HOTELES MELIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(241, '1053', 'EL TRICICLE COMPAÑÍA TEATRAL, S.L.', 'Paseig de Gracia, 20 - 1º 2ª', '08007', 'Barcelona', 'Barcelona', 'B-58250838', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(243, '1054', 'CYMA METODOS Y MEDIOS, S.L.', 'AVDA. MAISONAVE 27 - 29 , 7º IZQ', '03003', 'ALICANTE', 'ALICANTE', 'B-53694337', '965229039 // gonzalo 650396385', '965132685', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(245, '1055', 'RR MUSIC', 'PARTIDA DE LA DEVESA 111 A, APDO CORREOS 198', '03440', 'IBI', 'ALICANTE', '21652411-G', '607143983', NULL, NULL, 'rr.music@hotmail.com', 'RAFAEL PERONA MOLINA', 'PARTIDA DE LA DEVESA 111 A, APDO CORREOS 198', '03440', 'IBI', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(246, '1056', 'HOTEL MIO CID', NULL, NULL, NULL, NULL, NULL, '965152700', '965265226', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(248, '1057', 'IMAGINARTE', 'Avda. Ciudad de León de Nicaragua, 15', '03015', 'Alicante', 'Alicante', 'B-53541157', '965916470', '965257384', NULL, NULL, 'GIRO CREATIVO, S.L.', 'AVDA. CIUDAD DE LEÓN DE NICARAGUA, 15', '03015', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:01', '2026-02-18 13:03:01'),
(250, '1058', 'RECABUS', NULL, NULL, NULL, NULL, NULL, NULL, '965666945', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(252, '1059', 'SONIVISION', 'C/ LARRAMENDI, 15', '03160', 'ALMORADI', NULL, '74170630T', '607325444', NULL, NULL, NULL, 'PASCUAL F. PARRES GALINDO (SONIVISIÓN)', 'C/ LARRAMENDI, 15', '03160', 'ALMORADI', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(254, '1060', 'ASOCIACION CULTURAL MODELISMO NAVAL', NULL, NULL, NULL, NULL, NULL, '965926728', '965926728', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(255, '1061', 'GAED GRUPO ALICANTINO DE ESTUDIOS DENTALES', 'Avda. Federico Soto, 11 - 2º A', '03003', 'Alicante', 'Alicante', 'G-03385564', '965203060 // 629313607 // 965140430', '965218320', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(257, '1062', 'FUNDACIÓN VALENCIANA DE LA CALIDAD', 'PLAZA DEL AYUNTAMIENTO, 7 - 1º - PUERTA 5', '46002', 'VALENCIA', NULL, 'G-96746300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(259, '1063', 'GENERALITAT VALENCIANA (CONSELLERIA)', 'C/.CABALLEROS, 9', '46001', 'VALENCIA', 'VALENCIA', 'S-4611001-A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(261, '1064', 'PGN GRUPO AUDIOVISUAL', 'C/ Pedrezuela, 21A Nave 12 P. I. Ventorro del Cano', '28925', 'Alarcon', 'Madrid', NULL, '917081090', '913077611', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(263, '1065', 'HOTEL AC CIUDAD DE ALCOY', NULL, NULL, NULL, NULL, NULL, '965333606', '965333636', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(264, '1066', 'J. URIACH Y COMPAÑÍA,S.A.', 'AVDA. CAMÍ REIAL, 51-57- POL. IND. RIERA DE CALDES', '08184', 'PALAU - SOLITÁ I PLEGAMANS', 'BARCELONA', 'A-63279152', '963411603 - 646481279', '963800301', NULL, 'jc.alcolea@uriach.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(266, '1067', 'PRODUCCIONES YLLANA', NULL, NULL, NULL, NULL, NULL, '915233301', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(267, '1068', 'LEVANTINA Y ASOCIADOS DE MINERALES, S.A.', 'AUTOVÍA DE MADRID - ALICANTE, KM 382', '03660', 'NOVELDA', 'ALICANTE', 'A-84433515', '965609184', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(269, '1069', 'UNIVERSIDAD DE ALICANTE - VICERRECTORADO RELACIONES INSTITUCIONALES - OFICINA DE COMUNICACIÓN', 'Campus de San Vicente del Raspeig, Apto. 99', '03080', 'Alicante', 'Alicante', 'Q-0332001-G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(271, '1070', 'SEUR', 'Pol. Ind. Atalaya Avda. Euro, 9', '03114', 'Alicante', 'Alicante', 'A-03176864', '686379357', NULL, NULL, NULL, 'LOGISLAND, S.A.', 'Pol. Ind. La Atalaya, Avda. Euro, 9', '03114', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:02', '2026-02-18 13:03:02'),
(272, '1071', 'ECOLOGISTAS EN ACCIÓN', NULL, NULL, NULL, NULL, NULL, '965255270 / 966308807', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(274, '1072', 'HNOS. MARISTAS PROV. MEDITERRANEA', 'AVDA. ISLA DE CORFU, 5', '03005', 'ALICANTE', 'ALICANTE', 'R-4601087-B', '965130941  EXT. 125// 676168402 millan /651842590N', NULL, '651842590 HMNO. NACHO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(276, '1073', 'GENESIS MEDIA FILMS, S.L.', 'Pol. Industrial Buenavista C/ Dos, 24', '30152', 'Aljúcer', 'Murcia', 'B-73455305', '968352411 // 687973413', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(278, '1074', 'MINSITERIO DE FOMENTO (DIRECCION GRAL. DE TRANSPORTES POR CARRETERA)', NULL, NULL, NULL, NULL, NULL, '915975308', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(279, '1075', '1ª IGLESIA EVANGELICA BAUTISTA DE ALICANTE', 'PZA. PIO XII, 3', NULL, 'ALICANTE', NULL, 'Q-0300259-I', '600311810', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(281, '1076', 'HOTEL BONALBA', 'C/ VESPRE, Nº 10', '03110', 'MUTXAMEL', 'ALICANTE', 'A-54024401', '965959595', '965959272', NULL, NULL, 'HOTEL MANAGEMENT CONCEP IBERIA,S.A. HOTEL DEL ALBA - CAMPO DE GOLF BONALBA', 'C/ VESPRE, Nº 10', '03110', 'MUTXAMEL', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(283, '1077', 'ECISA CORPORACION', NULL, NULL, NULL, NULL, 'B-54045190', '616467162', '965165323', NULL, NULL, 'ECISA CORPORACIÓN EMPRESARIAL, S.L.', 'AVDA. COSTA BLANCA, 139', '03540', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(285, '1078', 'EVA RICO', NULL, NULL, NULL, NULL, NULL, '695117529', '966478352', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(287, '1079', 'MDR AUDIOVISUALES, S.L.', 'C/ TORNO, 18 NAVE 2 POL. IND. EL CANASTELL', '03690', 'San Vicente del Raspeig', 'ALICANTE', 'B-53098539', '902431217/965253680', '965910073', 'www.mdraudiovisuales.com', 'mdr@mdraudiovisuales.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(288, '1080', 'FOGUERA AVDA. LORING ESTACION', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(290, '1081', 'VIDEOREPORT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(292, '1082', 'ARGOS COMUNICACIÓN', NULL, NULL, NULL, NULL, NULL, '600755543', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(294, '1083', 'SUPERMECADOS MAS Y MAS', 'CTRA. NACIONAL 332, KM 191', '03750', 'PEDREGUER', 'ALICANTE', 'A-03140456', '965760450 / 649472422', NULL, NULL, NULL, 'JUAN FORNES FORNES, S.A.', 'CTRA. NACIONAL 332, KM 191', '03750', 'PEDREGUER', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:03', '2026-02-18 13:03:03'),
(296, '1084', 'CCA PROMOCIONES', NULL, NULL, NULL, NULL, NULL, '690619622', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(297, '1085', 'SI QUIERO', 'C/ César Elguezabal, 32 - 4 A', '03001', 'Alicante', 'Alicante', NULL, NULL, NULL, NULL, 'alicante@siquiero.es', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(299, '1086', 'COSMETICOS LUACES', 'Berenguer de Marquina, 18 - Entresuelo Izq', '03004', 'Alicante', 'Alicante', '48533332-C', '965203466 - 610781980', '965144114', NULL, NULL, 'ROSA LUACES TRUJILLO', 'Berenguer de Marquina, 18 - Entresuelo Izq', '03004', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(301, '1087', 'TEATRES GENERALITAT', NULL, NULL, NULL, NULL, NULL, '609603241', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(302, '1088', 'MEDIOS AUDIOVISUALES DE MURCIA, S.L.', 'Ctra. Alcantarilla, 64', '30166', 'Nonduermas', 'Murcia', '21897454B', '968350807', '968340625', NULL, NULL, 'JOSE Mª BOTI PEREZ', 'Ctra. Alcantarilla, 64', '30166', 'Murcia', 'Murcia', NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(304, '1089', 'EUROFICINA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(306, '1090', 'AGENCIA LOCAL DE DESARROLLO ECONÓMICO Y SOCIAL DEL AYUNTAMIENTO DE ALICANTE', 'C/. Jorge Juan, 21', '03002', 'ALICANTE', 'ALICANTE', 'P0300039E', '965145700 / 965980537', '965146212', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(308, '1091', 'TECNOGRAMA SERVICIOS COMPUTERIZADOS, S.L.', 'C/ Alvarez Quintero, 19', '03690', 'San Vicente del Raspeig', 'Alicante', NULL, '902103132', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(310, '1092', 'BODAS CARTAGENA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(311, '1093', 'CLICHE MEDIA', 'C/ SAN ROQUE, 13', '03600', 'ELDA', 'ALICANTE', '22.140.396-K', '615295717', NULL, NULL, NULL, 'JUAN JOSE FERNANDEZ GARCIA', 'C/ SAN ROQUE,13', '03600', 'ELDA', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(313, '1094', 'ESTEFANIA', NULL, NULL, NULL, NULL, NULL, '676349870', '965141353', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(315, '1095', 'ATIENZA', 'Primitivo Perez, 22 Bajos', '03010', 'Alicante', 'Alicante', NULL, '655841389', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(317, '1096', 'NUEVA IMAGEN', NULL, NULL, NULL, NULL, NULL, '962920399', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:04', '2026-02-18 13:03:04'),
(318, '1097', 'CORPORATE SAILING S.L.', 'C/. Ayala, 7 Bajo', '28001', 'Madrid', 'Madrid', 'B-84204015', '914011997', '914261293', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(320, '1098', 'RMS AUDIO, S.L.', NULL, NULL, NULL, NULL, NULL, '965525615 / 619787875', '965525615', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(322, '1099', 'CAJA MEDITERRÁNEO (MICROINFORMATICA Y OFICINAS)', NULL, NULL, NULL, NULL, NULL, '965905120', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(324, '1100', 'ELECTRONICA FERRANDIZ', NULL, NULL, NULL, NULL, NULL, '965451161 / 617080104', '965451161', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(326, '1101', 'FEDERACION DE INDUSTRIA DEL CALZADO ESPAÑOL (F.I.C.E.)', 'Nuñez de Balboa, 116 - 3º', '28006', 'Madrid', 'Madrid', NULL, '915629289', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(327, '1102', 'CATEDRA DEMETRIO RIBES UVEG-FGV', 'C/. UNIVERSIDAD, 2', '46003', 'VALENCIA', 'VALENCIA', NULL, '963864986', '963864986', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(329, '1103', 'CONSELLERIA DE INFRAESTRUCTURAS Y TRANSPORTE, DIRECCIÓN GRAL. DE TRANSPORTE Y LOGISTICA', 'Avda. Blasco Ibanez, 50', '46010', 'Valencia', 'Valencia', 'S-4611001-A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(331, '1104', 'SONNENKRAFT ESPAÑA, S.L.', 'C/ La Resina, 41 Nave 5', '28021', 'Madrid', 'Madrid', 'B-84647437', '915052940', '917955632', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(333, '1105', 'AYUNTAMIENTO DE AGOST', 'PZA. DE ESPAÑA, S/N', NULL, NULL, NULL, 'P-0300-C200', '610458247', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(334, '1106', 'CONCHA LÓPEZ SARASUA', NULL, NULL, NULL, NULL, NULL, '965130581', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(336, '1107', 'ARENA TEATRO, S.L.', 'C/ Vinader, 13 - 2º A', '30004', 'Murcia', 'Murcia', 'B-30235790', '968678947 - 650532740 CARLOS', '968678946', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(338, '1108', 'MEG VAN AMSTEL', NULL, NULL, NULL, NULL, NULL, '965840660', '966885843', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(339, '1109', 'OJO PUBLICO PRODUCCIONES AUDIOVISUALES. S.L.', 'C/. ALCALDE MARIANO BEVIÁ, 4-2º', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', 'B-54163449', '965164608 / 625101277', '966370180', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:05', '2026-02-18 13:03:05'),
(340, '1110', 'MAN RAY', 'AVDA. PAÍS VALENCIANO, 55 ATICO', '03201', NULL, 'ALICANTE', NULL, '966093770', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(341, '1111', 'HOTEL KRIS MAYA', 'C/ Canonigo M.L. Penalva, S/N', '03002', 'Alicante', 'Alicante', NULL, '965261211', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(343, '1112', 'STEREO RENT', 'C/ Fray Junipero Serra, 44 bis', '08030', 'Barcelona', 'Barcelona', 'A-58144387', '934980980', NULL, NULL, NULL, 'HISPART, S.A.', 'C/ Fray Junipero Serra, 44 bis', '08030', 'Barcelona', 'Barcelona', NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(345, '1113', 'VITELSA', 'C/ Barón de Cárcer, 50 1º Dcha - C. de Negocios', '46001', 'Valencia', 'Valencia', 'A28872133', '963904790   630932756 GUILLERMO', '963646596', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(347, '1114', 'ELISA SOTO', 'C/. CURRICAN, 24 BUNG. 5', NULL, NULL, NULL, NULL, '639617988', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(349, '1115', 'HOTEL ASIA GARDENS', 'Avda. Eduardo Zaplana, S/N', '03502', 'Benidorm', 'Alicante', 'A-83058537', '966818400', NULL, 'www.asiagardens.es', NULL, 'ROYAL MEDITERRÁNEA, S.A.', 'C/ Estafeta nº 2, Portal 2 - Planta 1ª', '28109', 'Alcobendas', 'Madrid', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(350, '1116', 'VISUAL SONORA', 'Avda. Alicante, 77', '03400', 'Villena', 'Alicante', NULL, '965800710', '965343052', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(352, '1117', 'SONIDO AULLÓ', NULL, NULL, NULL, NULL, NULL, '617341498', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(354, '1118', 'AM SONIDO', 'Jaime I, 102 - 4ª C', '03440', 'IBI', 'Alicante', '21676040-N', '965551808 / 661305073', NULL, NULL, NULL, 'ANGEL MANUEL CACERES VALLS', 'Jaime I, 102 - 4ª C', '03440', 'IBI', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(356, '1119', 'ALAVES, MONTAJE Y REALIZACIÓN, S.L.', 'Ctra. Alicante, 129', '03690', 'San Vicente del Raspeig', 'Alicante', 'B-53441366', '902190501', '965675730', NULL, NULL, 'ALAVES, MONTAJE Y REALIZACIÓN, S.L.', 'C/ PINOSO Nº 21', '03012', 'ALICANTE', 'ALICANTE', NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(358, '1120', 'AYUNTAMIENTO DE ALICANTE (AREA DE SERVICIOS)', NULL, NULL, NULL, NULL, NULL, '965149256  TFNO ANDRES LLORENS 606435425', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(359, '1121', 'SONAR STEREO', NULL, NULL, NULL, NULL, NULL, '637712973 //0965600310', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(361, '1122', 'AYUNTAMIENTO DE VILLENA', NULL, NULL, 'Villena', NULL, NULL, '666311047', '965801150', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(363, '1123', 'CARPETAS ABADIAS', NULL, NULL, NULL, NULL, NULL, '902100825', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:06', '2026-02-18 13:03:06'),
(365, '1124', 'SALVADOR MELLADO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(367, '1125', 'STAFF EVENTOS', NULL, NULL, NULL, NULL, NULL, '914740816', '914742789', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(368, '1126', 'BRENT & TRADING, S.L.', 'Manuel Nuñez, 4 4ª Planta', '36203', 'Vigo', 'Pontevedra', 'B-62873856', '986443072 / 686029851', '986222126', NULL, 'eventos3@globalenergy.es', 'BRENT & TRADING, S.L.', 'Manuel Nuñez, 4 4ª Planta', '36203', 'Vigo', 'Vigo', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(370, '1127', 'AYUNTAMIENTO DE ELCHE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(372, '1128', 'DIFUSION Y EVENTOS, S.L.', 'C/ Guillem de Castro, 83', '46008', 'Valencia', 'Valencia', 'B-96973771', '963153070', '963921188', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(374, '1129', 'GRUPO OSBORNE', NULL, NULL, NULL, NULL, NULL, '670922028', NULL, NULL, NULL, 'YABLEIDY TRIANA RUEDA', 'C/ Mas Pujol, 14 - 3ª 1ª', '08032', 'Barcelona', 'Barcelona', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(375, '1130', 'C.P. CLUB DEL MAR', 'AVDA. BENIDORM, 18 L - 19', '03540', 'PLAYA SAN JUAN', 'ALICANTE', 'E03211653', '658912894', '965160621 TELF + FAX', NULL, NULL, 'COMUNIDAD DE PROPIETARIOS CLUB DEL MAR', 'AVDA. BENIDORM, 18 L - 19', '03540', 'PLAYA SAN JUAN', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(377, '1131', 'FERROCARRILES GENERALITAT VALENCIANA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(379, '1132', 'UTE TRANVIA LUCEROS-MERCADO ALICANTE', 'Avda. Salamanca, 14 6º Piso', '03005', 'Alicante', 'Alicante', 'G-97583942', NULL, NULL, NULL, NULL, 'UTE TRANVIA LUCEROS-MERCADO ALICANTE', 'Avda. Blasco Ibanez, 25 Entlo', '46010', 'Valencia', 'Valencia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(381, '1133', 'UTE TRANVIA - GOTETA', 'C/ BAZAN, 57 4º', '03001', 'Alicante', 'Alicante', 'G-54146295', '647389390', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(383, '1134', 'UTE AVENIDA DE DENIA', 'C/ Alvaro de Bazan, 10', '46010', 'Valencia', 'Valencia', 'U-97769582', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(384, '1135', 'LUNDSTROM EVENT PRODUCTION AB', 'Stora Avagen, 21', NULL, 'S-436 34 Goteborg', 'Suecia', '556666-6623', '966875052 / 650643036 /0046317232194', '0046317232199', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(386, '1136', 'INK CIEN POR CIEN EVENTOS, S.L.', 'Miguel Yuste, 32', '28037', 'Madrid', 'Madrid', 'B-81055287', '914402730', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:07', '2026-02-18 13:03:07'),
(387, '1137', 'EMILIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(389, '1138', 'FLOW MMC', 'C/ Valverde, 33 Bajo B', '28004', 'Madrid', 'Madrid', 'B-83812891', '913080558', '917011511', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(391, '1139', 'ATICA IDIOMAS', 'C/ Ramón y Cajal, 77', '30300', 'Cartagena', 'Murcia', NULL, '660686928', '968521593', 'www.aticaidiomas.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(393, '1140', 'RAUL MERCURI', NULL, NULL, NULL, NULL, NULL, '670336679', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(394, '1141', 'CAROLINA  (TEMPE)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(396, '1142', 'CENTRO MARISTAS GUARDAMAR', 'CTRA. DE CARTAGENA-ALICANTE KM 75,7', '03140', 'GUARDAMAR DEL SEGURA', 'ALICANTE', 'Q0300328B', '966725109', '966725110', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(398, '1143', 'CAJA DE AHORROS DEL MEDITERRÁNEO (RELACIONES EXTERNAS)', 'San Fernando, 40', '03001', 'Alicante', 'Alicante', 'G-03046562', 'MARIA  675545934', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(400, '1144', 'HOTEL VENUS ALBIR', NULL, NULL, NULL, NULL, NULL, '637832825', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(401, '1145', 'SPRINT S.L', NULL, NULL, 'GARRUCHA', 'ALMERIA', NULL, '902119675', '950132248', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(403, '1146', 'DARSENA ALICANTE, S.L.', 'Marina Deportiva, Muelle Levante, 6', '03001', 'ALICANTE', 'ALICANTE', 'B-53085312', '965207589', '965143745', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(405, '1147', 'RENT MULTIMEDIA, S.L.', 'C/. MANUEL FERNÁNDEZ MÁRQUEZ, 27', '08918', 'BADALONA', 'BARCELONA', 'B-61851382', '934342174', '934173015', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(407, '1148', 'ALICANTE 2008', NULL, NULL, NULL, NULL, 'A-97658298', '647411589 Paco/ 677888691M /696360119P/963875390', NULL, NULL, NULL, 'SOCIEDAD GESTORA PARA LA IMAGEN ESTRATEGICA Y PROMOCIONAL DE LA COMUNIDAD VALENCIANA', 'C/ Conde de Almodovar, 1 Pta. 1', '46003', 'Valencia', 'Valencia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(409, '1149', 'JPA, S.L.', NULL, NULL, NULL, NULL, NULL, '647734265', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:08', '2026-02-18 13:03:08'),
(410, '1150', 'COLEGIO DE ENFERMERÍA DE ALICANTE', 'CAPITAL DEMA, 16', '03007', 'ALICANTE', 'ALICANTE', 'Q-036003-B', '965121372', '965228407', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(412, '1151', 'PROTOCOL EVENTS', 'JOAN GUEL, 43', '08028', 'BARCELONA', 'BARCELONA', NULL, '934906766', '934908715', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(414, '1152', 'MUNDALIA', 'Ctra. De Málaga, 218- 3º OFICINA 5', '04700', 'EL EJIDO', 'ALMERIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(416, '1153', 'GARRIGUES', NULL, NULL, NULL, NULL, NULL, NULL, '965982201', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(417, '1154', 'VIAJES EL CORTE INGLES S.A', 'C/. PRINCESA, 47-3ª PLANTA', '28008', 'MADRID', 'MADRID', 'A-28229813', '912042600  EXT. 1568', '915415881', NULL, NULL, 'VIAJES EL CORTE INGLES, S.A', 'AVD. CANTABRIA, 51', '28042', 'MADRID', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(419, '1155', 'ING NATIONALE-NEDERLANDEN', 'Avda. Oscar Espla, 14 Bajos', '03003', 'Alicante', 'Alicante', 'A-81946485', '965921311', '965124411', NULL, NULL, 'NATIONALE-NEDERLANDEN, COMPAÑÍA DE SEGUROS Y REASEGUROS, S.A.E.', 'Avda. Oscar Espla, 14 Bajos', '03003', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(421, '1157', 'CITITRAVEL DMC', 'Av Corts Valencianes 58 Ed. Sorolla Center of. 406', '46014', 'Valencia', 'Valencia', 'A-60994274', '963533838', '963943306', NULL, NULL, 'KONCITI VIAJES, S.A.', 'Av Corts Valencianes 58 Ed. Sorolla Center of. 406', '46015', 'Valencia', 'Valencia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(423, '1556', 'HERSAMOTOR (HONDA ALICANTE)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(425, '1557', 'ASOCIACION APSA', 'AVDA. DE SALAMANCA, 27', '03005', NULL, 'ALICANTE', 'G-030.049.038', '965257112', '965123778', NULL, NULL, 'ASOCIACIÓN PRO - DEFICIENTES PSÍQUICOS DE ALICANTE', 'AVDA. DE SALAMANCA, 27', '03005', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(426, '1558', 'ATP THE ADVANCED TRAVEL PARTNER', 'Beechavenue 101', '1119', 'RB Schiphol-Rijk', 'The Netherlands', NULL, 'NL806626045B01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(428, '1559', 'GEVALMEDIA', 'Alqueria de Raga, 1 P.L. Raga', '46210', 'Pincaya', 'Valencia', 'B-97842918', '961594455', '961590516', 'www.gevalmedia.es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(430, '1560', 'DEXTRO MEDICA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(432, '1561', 'PATRONATO MUNICIPAL DE CULTURA', 'Plaza Quijano, 2', '03001', 'Alicante', 'Alicante', 'P-0300038-G', '965147160', '965200643', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:09', '2026-02-18 13:03:09'),
(434, '1562', 'RAFAEL PASTOR', NULL, NULL, NULL, NULL, NULL, '667441119', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(435, '1563', 'CARLOS CONTRERAS', NULL, NULL, NULL, NULL, NULL, '619055690', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(437, '1564', 'ALICANTINA DISTRIBUCIONES MEDICAS, S.A', 'Avda. Conde Lumiares, 29', '03010', 'Alicante', 'Alicante', 'A-03078672', NULL, '965255950', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(439, '1565', 'ECISA, CÍA GENERAL DE CONSTRUCCIONES, S.A. OBRA C-282, VILLAGE PUERTO DE ALICANTE', 'Avda. Costabllanca, 139', '03540', 'Playa San Juan', 'Alicante', 'A-35009802', '965155855', '965162031', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(441, '1566', 'CURSOS INTENSIVOS MIR ASTURIAS', NULL, NULL, NULL, NULL, NULL, '985205232', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(442, '1567', 'VIDEO IEC ESPAÑA, S.L.', 'Narcis Monturiol, 4 Ofic. 20A Parque Tecnológico', '46980', 'Paterna', 'Valencia', 'B-80365638', '961366728 - 669565328 Jaime', '961318642', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(444, '1568', 'FUSIONARTE COMUNICACIÓN.COM', 'AVDA. AGUILERA, 38-2º B', '03006', 'ALICANTE', 'ALICANTE', '44768545-B', '965120121 / 657 801 536 J', NULL, NULL, NULL, 'Mª MERCEDES ORTEGA MONTALBEZ', 'AVDA. AGUILERA, 38 - 2º B', '03006', 'ALICANTE', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(446, '1569', 'CONCHI ROMERO', 'C/ MEDICO PEDRO HERRERO, Nº 25 - 2º', '03006', 'ALICANTE', 'ALICANTE', '21383478-X', '654483665', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(448, '1570', 'HOTEL DON PANCHO', NULL, NULL, NULL, NULL, NULL, '965852950', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(450, '1571', 'EDITORIAL PLANETA', 'C/ DEL MERCADO, Nº 59', NULL, 'SAN JUAN', 'ALICANTE', 'A-08186249', '965940620 - 650059980', '965940141', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(451, '1572', 'CYOP LEVANTE', NULL, NULL, NULL, NULL, NULL, '638033365', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(453, '1573', 'ONFF MEDIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(455, '1574', 'ZENIT', 'C/. VALDEMORILLO, 31', '28925', 'ALCORCON', 'MADRID', NULL, '91 633 88 47', '916635411', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:10', '2026-02-18 13:03:10'),
(457, '1575', 'ACSA OBRAS E INFRAESTRUCTURAS', 'PZA. ALCALDE AGATANGELO SOLER, 7 , OFICINA A', '03015', 'ALICANTE', 'ALICANTE', 'A08112716', '965916312', '965916313', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(459, '1576', 'HALCON VIAJES S.A.U', 'AVDA. PLATA, 20', '46013', 'VALENCIA', 'VALENCIA', 'A-10005510', '963353022', NULL, NULL, NULL, 'VIAJES HALCON,S.A.U.', 'C/ JOSÉ ROVER MOTTA,27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(462, '1577', 'EXCMO. AYUNTAMIENTO DE SANT JOAN', 'Rambla, 56', '03550', 'San Juan de Alicante', 'Alicante', NULL, '965651353 / 627489389 Pablo', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(465, '1578', 'POLYANGUAS', NULL, NULL, NULL, NULL, NULL, '687490045 // 667518396', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(468, '1579', 'NOVOIMPLANT', NULL, NULL, NULL, NULL, 'B-53047940', NULL, NULL, NULL, NULL, 'LEV MEDICAL, S.L', 'C/ GENERAL ESPARTERO, 92', '03012', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(471, '1580', 'HOGUERA ALTOZANO SUR LAS PLAZAS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(473, '1581', 'COLEGIO DE  AUTISTAS', NULL, NULL, NULL, NULL, NULL, '965134000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(477, '1582', 'SONORIZACIONES ALICANTE SC', 'C/ Moli Nou, Nave 17 Camara comercio, Pol. Riodel', '03110', 'Muchamiel', 'Alicante', 'J54159892', '678574220 / 639660000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(479, '1583', 'IDEX, IDEAS Y EXPANSIÓN, S.L.', 'C/ Deportista Manuel Suarez, 11 - 4º c', '03006', 'Alicante', 'Alicante', 'B-53093837', '902929202 - 630147200 CARLOS ROBLES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(482, '1584', 'CARLOS LEVI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(485, '1585', 'PANORAMA SISTEMAS AUDIOVISUALES, S.L.', NULL, NULL, NULL, NULL, NULL, '902365150', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(487, '1586', 'SOLTEC RENOVABLES', NULL, NULL, NULL, NULL, NULL, '618651551', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(490, '1587', 'CENTRO EUROPEO DE EMPRESAS E INNOVACIÓN DE VALENCIA', 'AVDA. BENJAMÍN FRANKLIN 12 - PARQUE TECNOLÓGICO', '46980', 'PATERNA', 'VALENCIA', 'G-46948238', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(493, '1588', 'CARLSON WAGONLIT TRAVEL', 'C/ Condesa de Venadito, 1 - 5º', '28027', 'Madrid', 'Madrid', 'B-81861304', '912058947- 912058952 BELEN - 917249936 CLARA', '917249944', NULL, NULL, 'CARLSON WAGONLIT ESPAÑA, S.L.U.', 'C/ Trespaderne, 29 Edificio Barajas I', '28042', 'Madrid', 'Madrid', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:11', '2026-02-18 13:03:11'),
(496, '1589', 'CARLSON WAGONLIT TRAVEL (ALICANTE)', 'C/. REYES CATOLICOS, 31-1º B', '03003', 'ALICANTE', 'ALICANTE', 'B-81861304', '965130824', '965925062', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(499, '1590', 'EMUASA (AGUAS DE MURCIA)', 'PZA. CIRCULAR, 9', '30008', 'MURCIA', 'MURCIA', 'A30054209', '968278000 // 659851160', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(501, '1591', 'PANORAMIS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(504, '1592', 'ALBERTO JESUS GONZALEZ ROSALES', 'URANIZACIÓN COSTA HISPANIA, 127', '03130', 'SANTA POLA', NULL, '70577064-T', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(507, '1593', 'COLEGIO CALASANCIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(510, '1594', 'CENTRO DE NEGOCIOS ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(513, '1595', 'IDEA INVESTIGACIÓN Y MARKETING, S.L.', 'C/ De las Moras, 16 Bajo A', '28032', 'Madrid', 'Madrid', 'B-82565888', '629291956', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(515, '1596', 'VIOLETA TRIVES', NULL, NULL, NULL, NULL, NULL, '617403949', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(519, '1597', 'AFID CONGRESOS, S.L.', NULL, NULL, NULL, NULL, NULL, '942318180', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(521, '1598', 'IGLESIA CRISTO VIVE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(524, '1599', 'PRODUCCIONES VIDEOMED, S.L.', 'C/ Cigarral, 2 - 6º B', '30003', 'Murcia', 'Murcia', 'B-30498232', '968273336 / 609623773 M.A.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(526, '1600', 'SOUND LINE, S.L.', 'Pol. Ind. Campollano, Avda. 3ª nº 2 Nave 6', '02007', 'Albacete', 'Albacete', 'B-02255917', '967508984', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(529, '1601', 'INSTITUTO BERNABEU', 'Avda. Albufereta, 31', '03016', 'Alicante', 'Alicante', 'B-54400171', '965154000', NULL, NULL, NULL, 'INSTITUTO BERNABEU BIOTECH, S.L.', 'AVDA. ALBUFERETA, 31', '03016', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:12', '2026-02-18 13:03:12'),
(532, '1602', 'HARTFORD, S.L. EA SOCIAL', 'Pza. Sta. María Soledad Torres Acosta, 1 - 2ª Plta', '28004', 'Madrid', 'Madrid', NULL, '913500300 / 914179227 / 696255544', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(535, '1603', 'SERILEV, S.L.', 'C/ Sevilla, 27 Bajo', '03012', 'Alicante', 'Alicante', 'B-53158887', '965986210/ 677473663', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(538, '1604', 'BESTOURS CORPORATE PLUS', 'Consell de Cent, 334/336', '08009', 'Barcelona', 'Barcelona', NULL, '934967405', '934880084', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(541, '1605', 'AUTORIDAD PORTUARIA DE ALICANTE', 'MUELLE DE PONIENTE, 11', '03001', 'ALICANTE', 'ALICANTE', 'Q0367005F', '965130095 /608449492 clauido', '965130034', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(543, '1606', '* INFORMACIÓN TV', 'DOCTOR RICO, 17', '03005', 'ALICANTE', 'ALICANTE', 'B-54217559', '699483759', NULL, NULL, NULL, 'PRENSA ALICANTINA MEDIA S.L.U.', 'DOCTOR RICO, 17', '03005', 'ALICANTE', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(546, '1607', 'USP HOSPITAL SAN JAIME SAU', 'PTDA. DE LA LOMA, S/N', '03180', 'TORREVIEJA', 'ALICANTE', 'A-53063236', '966921313', '966922706', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(549, '1608', 'BARRACA PARES I FILLS', 'AV. CONDE LUMIERAS, 43-45', '03000', 'ALICANTE', 'ALICANTE', 'G03539095', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(551, '1609', 'JORGE ANTONIO SCABECE', 'C/ CENTRO, 1 P01 IZQ', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', 'X-5724598-J', '656748971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(554, '1610', 'SOLEIL TRADUCCIONES', NULL, NULL, NULL, NULL, NULL, '966656706 / 676281656', '966656706', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(557, '1611', 'PRODUCCIONES 3 EN RAYA , S.L.', 'C/ FUENTENUEVA Nº 6 ,5º D', '45006', 'TOLEDO', 'TOLEDO', 'B-45553534', '925228552 / 687971201', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(560, '1612', 'PROMOVIATGES', NULL, NULL, NULL, NULL, '36976837-J', '934531014  // JOSEP MARIA 625385252', NULL, NULL, NULL, 'JOSE MARIA MONTORO LEAL', 'Puig Castellar, 40', '08758', 'Cervello', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(563, '1613', 'MAKEVENT', 'Canalejas, 13 - 1º', NULL, 'Alicante', 'Alicante', NULL, '692166911', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(565, '1614', 'VIAJES MARSANS  HRG SPAIN', NULL, NULL, 'Madrid', 'Madrid', NULL, '914068718', '912587720', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:13', '2026-02-18 13:03:13'),
(568, '1615', 'ILUMINACIONES ALMENA', NULL, NULL, NULL, NULL, NULL, '965248277        FELIX 607284086', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(571, '1616', 'NOVA RED', 'AVDA. DOCTOR RAMÓN Y CAJAL, 13, ENTLO. D', '03003', 'ALICANTE', NULL, NULL, '965131994', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(574, '1617', 'FUNDACIÓN RAFAEL BERNABEU OBRA SOCIAL', 'AVDA. ALBUFERETA, 31', '03016', 'ALICANTE', NULL, 'G-54189782', '965154000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(576, '1618', 'FUNDACION COMUNIDAD VALENCIANA- REGIÓN EUROPEA', 'PLAZA MANISES, Nº 1', '46003', 'VALENCIA', 'VALENCIA', 'G-97374771', '32 22824169  EXT. 22174', '32 22824161', NULL, NULL, 'FUNDACION COMUNIDAD VALENCIANA- REGIÓN EUROPEA', 'PLAZA MANISES, Nº 1', '46003', 'VALENCIA', 'VALENCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(580, '1619', 'KANZAMAN PRODUCTIONS, S.L.', 'C/ Cirilo Amorós, 6', '46004', 'Valencia', 'Valencia', 'B-97900195', '672470154', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(582, '1620', 'C.P. CAMPOS ELISEOS 1', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(585, '1621', 'TABERNA LA VENDIMIA', NULL, NULL, NULL, NULL, NULL, '965248490 // 678662141', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(588, '1622', 'ASOCIACIÓN DE OCIO PARA DISCAPACITADOS INTELECTUALES', 'C/ Arpón, 13 4º - 3ª', '03540', 'Alicante', 'Alicante', NULL, '656903971', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(590, '1623', 'SOLD OUT', 'C/ Velazquez, 112 - 3º', '28006', 'Madrid', 'Madrid', NULL, '914358478', '914318185', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(594, '1624', 'HERMANDAD DEL PRENDIMIENTO', 'C/ MAYOR, 56', '03160', 'ALMORADI', NULL, 'G-03686458', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(596, '1625', 'MOTOS MEDINA, S.L.', 'Avda. de Denia, 13', '03002', 'Alicante', 'Alicante', 'B-03305240', '965132558', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(599, '1626', 'FAMOSA', NULL, NULL, NULL, NULL, NULL, '917401123 //', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(602, '1627', '*GRUPO GEYSECO, S.L.', 'C/ Marina, 27 Bajos', '08005', 'Barcelona', 'Barcelona', 'B-39037874', '932212242', '932217005', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:14', '2026-02-18 13:03:14'),
(604, '1628', 'ABSOLUTE GROUPS & INCENTIVES', 'Carretera Militar, 299', '07600', 'S\'Arenal de Palma', 'Mallorca', 'B-57602328', '971745530', '971743747', NULL, NULL, 'VAN GENT INCENTIVES, S.L.', 'Carretera Militar, 299', '07600', 'S\'Arenal de Palma', 'Mallorca', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15');
INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(607, '1629', 'ULTRAMAR', 'AVDA. MEDITERRÁNEO, 15      EDIF. TORRE BENIDORM', '03503', 'BENIDORM', 'ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(610, '1630', 'ALICANTE CONVENTION BUREAU', NULL, NULL, NULL, NULL, NULL, '965143452', '965215694', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(613, '1631', 'JOHN DEERE', 'Jonh Deere Str. 70', '68163', 'Mannheim', 'Germany', NULL, '+496218298408 / +49 16090765987', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(616, '1632', 'PRODUCCIONES ARTISTICAS', 'C/ CAPITAN AMADOR, Nº 9 1º IZ', '03004', 'ALICANTE', NULL, 'B-53187365', '618768222', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(618, '1633', 'ELANCO VALQUIMICA, S.A.', 'Avda. de la Industria, 30', '28108', 'Alcobendas', 'Madrid', 'A-28395549', '628209296', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(622, '1634', 'PATRONATO PROVINCIAL DE TURISMO', 'C/ Bilbao, 1- 5ª Planta', '03001', 'Alicante', 'Alicante', 'P-5300004-H', '667033187- M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(624, '1635', 'CAROLINA MIRALLES MARTIN', 'C/ MADRID,18 - 1º D', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', '53234197-F', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(627, '1636', 'INTERPRETES DE CONFERENCIAS, S.L.', 'Dr. Gomez Ferrer, 15 Pta. 23', '46010', 'Valencia', 'Valencia', 'B-97581342', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(629, '1637', 'THRE EVENTS', 'La Palma, 45 Local Izdo.', '28004', 'Madrid', 'Madrid', NULL, '629021775', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(632, '1638', 'BARCELÓ GESTIÓN HOTELERA', 'C/ Jose Rover Motta, 27', '07006', 'Palma de Mallorca', NULL, 'B-07918287', '966818400', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(635, '1639', 'INSTITUCIÓN FERIAL ALICANTINA', 'Ctra. Alicante-Elche, km. 731', '03200', 'Elche', 'Alicante', 'G-03021730', '966657600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(638, '1640', 'MICRORENT, S.A.', 'C/ Sepulveda, 6 nave 27 Pol. Ind. Alcobendas', '28108', 'Alcobendas', 'Madrid', 'A-78893286', '916620643', '916620785', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(641, '1641', 'MAYDAY GESTIÓN CULTURAL', NULL, NULL, NULL, NULL, NULL, '966376002 / 622433499', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:15', '2026-02-18 13:03:15'),
(643, '1642', 'ALLERGY THERAPEUTICS IBERICA S.L.', 'JOAN XXIII, 15 - 19, 1º 2ª', '08950', 'ESPLUGUES DE LLOBREGAT', 'BARCELONA', 'B-62034988', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(646, '1643', 'ENMANUEL', NULL, NULL, NULL, NULL, NULL, '961318500', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(649, '1644', 'FOTO CINE MAYAR', NULL, NULL, NULL, NULL, NULL, '965123024 / 678600877', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(652, '1645', 'TOP A DMC VIAJES, S.A.', 'Travessera de Gracia, 85 3º 1ª', '08006', 'Barcelona', 'Barcelona', 'A-64048325', '933680138', '933680079', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(654, '1646', 'EVENTCOMLIVE JUSOVA, S.L.', 'C/. GABRIEL MIRÓ,32', '03804', 'ALCOY', 'ALICANTE', 'B-54334354', '966440219 /689009031 L', '966540043', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(657, '1647', 'INTERPRÉTE DE CONFÉRENCE & TRADUCTEUR', '3 rue de la Mare de Troux', '78280', 'Guyancourt', 'FRANCE', 'FR02429162464', '+33161381804 / +33660187698', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(660, '1648', 'ASOCIACION DE VECINOS CAMPOAMOR-PZA. DE AMERICA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(663, '1649', 'CANON BUSINESS CENTER ALICANTE', 'Miguel Servet, 3', '03203', 'Elche (Parque Industrial)', 'Alicante', 'A-03297033', '655921064 / 965685280', '965685288', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(666, '1650', 'AUDIO RIVERA 7', 'Mariano Luiña, 18', '03201', 'Elche', 'Alicante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(668, '1651', 'FEDERACIÓN JUNTA FESTERA DE MOROS Y CRISTIANOS EL CAMPELLO', 'Avda. Generalitat, 2', '03560', 'Campello', 'Alicante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(671, '1652', 'MARISOL ALONSO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(674, '1653', 'MUNDIESPAÑA AGENCIA DE VIAJES', 'C/ ALVAREZ DE CASTRO, Nº 38', '28010', 'Madrid', 'Madrid', 'A-79351573', '915473901', '915472118', NULL, NULL, 'MUNDIESPAÑA, S.A.L.', NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(677, '1654', 'PRINCETON PHARMACEUTICAL, S.L.', 'C/ ALMASA,101', '28040', 'MADRID', 'MADRID', 'B-20394128', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:16', '2026-02-18 13:03:16'),
(680, '1655', 'EDWARDS LIFESCIENCES', 'AVDA. JUAN DE LA CIERVA, 27   PARQUE TECNOLÓGICO', '46980', 'PATERNA', 'VALENCIA', NULL, '963053706', '963053707', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(682, '1656', 'ESCUELA INFANTIL SOLY LUNA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(685, '1657', 'COLEGIO OFICIAL DE FARMACEUTICOS DE LA PROVINCIA DE ALICANTE', 'C/ Jorge Juan, 8', '03002', 'Alicante', 'Alicante', 'Q036002D', '965204033 - 965123123 - EXT 3 MACU 7', '965207587', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(688, '1658', 'SOCIEDAD ESPAÑOLA DE TOXICOMANIAS', 'C/ SAN VICENTE MÁRTIR, Nº 85 - PUERTA 11', '46007', 'VALENCIA', 'VALENCIA', 'G-58848714', '963130027 - 699989965', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(691, '1659', 'TELEFONICA ESPAÑA S.A.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(693, '1660', 'DAVID MANZANARES', NULL, NULL, NULL, NULL, '48561077-G', '650632108', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(696, '1661', 'MANUEL BORDONADO SANTAMARIA (ESPECTACULOS WILVUR)', 'Filet de Fora, 100', '03203', 'Elche', 'Alicante', '33492104-X', '966640662 / 661304930', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(699, '1662', 'DISCOMOVIL AUDIOVISUALES, S.L.', 'Azagador de la Torre, 77 P. Ind. Horno del Alcedo', '46026', 'Valencia', 'Valencia', 'B-96094263', '902666888', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(702, '1663', 'MANOLO CARRASCO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(705, '1664', '*KUONI DESTINATIÓN MANAGEMEN', 'Pau Claris, 138 - 1º puerta 3', '08009', 'Barcelona', 'Barcelona', NULL, '935052510', '934883703', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(707, '1665', 'COTA CERO', 'SAN FERNANDO, 49 2º 1ª', '03001', NULL, 'ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(710, '1666', 'INSTITUTO BERNABEU, S.L.', 'AVDA. ALBUFERETA, 31', '03016', 'ALICANTE', 'ALICANTE', 'B-53409439', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(713, '1667', 'MIGUEL ANGEL CASTILLO', NULL, NULL, NULL, NULL, '52766509R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:17', '2026-02-18 13:03:17'),
(716, '1668', 'CUATROJOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(718, '1669', 'JEFATURA PROVINCIAL DE TRAFICO DE ALICANTE', 'SAN JUAN BOSCO  14', '03005', 'ALICANTE', 'ALICANTE', 'Q2816003D', '965120229 EXT 123', NULL, NULL, 'olucas@dgt.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(721, '1670', 'GENERALITAT VALENCIANA CONSELLERIA DE GOBERNACIÓ', 'HISTORIADOR CHABÀS, 2', '46003', 'VALENCIA', 'VALENCIA', 'S-4611001-A', '963986902', '963985469', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(724, '1671', 'AS VÍDEO', 'C/ BATALLA DE BELCHITE, 5 - 4ª', '28045', 'MADRID', 'MADRID', 'B80346810', '915598757', NULL, NULL, 'asvideo@asvideo.net', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(727, '1672', 'UHURA FILMS', 'C/ Costanilla Santo Domingo, 3', '28013', 'Madrid', 'Madrid', NULL, '690164814', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(730, '1673', 'OPTICAL MD', 'C/ Tomas Luis de Victoria, 23', '03203', 'Elche', 'Elche', NULL, '902999304', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(732, '1674', 'SILVIA PONCE GONZALEZ', 'C/ Burriana, 6 - 12', '46005', 'Valencia', 'Valencia', '22633315-G', '963745638 / 627959701', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(735, '1675', 'TUWYNCAR SPORT, S.L.', 'C/ Rio Turia, 16', '03006', 'Alicante', 'Alicante', 'B-53413571', '965110533', '965107539', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(738, '1676', 'VIAJES COMETA, S.A.', 'C/ Espalter, 6', '28014', 'Madrid', 'Madrid', 'A-78226639', '914200070 / 607965644', '914291648', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(741, '1677', 'JAVIER SALA', 'Alferez Díaz Sanchiz, nº 79 - Atico', '03009', 'ALICANTE', 'ALICANTE', '48576723-X', '637545757 - 965122655', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(744, '1678', 'JULIA TORRES CASTILLO', NULL, NULL, NULL, NULL, NULL, '635966409', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(746, '1679', 'GRUPOER', 'AVDA. DEPORTISTA MIRIAM BLASCO, 1', '03016', 'ALICANTE', 'ALICANTE', NULL, '902100406  // RAQUEL 637472549', '965268431', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(749, '168', 'AMERICAN MEDICAL SYSTEMS IBERICA, S.L.', 'C/ Joaquín Turina, 2 Planta 1- of. 6', '28224', 'Pozuelo de alacrcón', 'Madrid', 'B-82121161', '917994972', '917157526', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:18', '2026-02-18 13:03:18'),
(752, '1680', 'HV INGENIEROS, S.L.', 'Pol. Ind. Apatel C/ de la industria, 3', '03380', 'Bigastro', 'Alicante', 'B-03715976', '966772120', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(755, '1681', 'SUGESTION AUDIOVISUAL,S.L.', 'C/ JULIAN CAMARILLO, 4', '28037', 'MADRID', 'MADRID', 'B-82919663', '914402720 - 699088851', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(757, '1682', 'NOEMÍ DÍAZ', NULL, NULL, NULL, NULL, NULL, '626193684', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(760, '1683', 'JOSÉ MARIA MARTINEZ', NULL, NULL, NULL, NULL, NULL, '629304822', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(763, '1684', 'VIAJES IBERIA INCENTIVOS Y CONGRESOS', 'Pallars, 193 - 2ª Pl - Edificio Orizonia', '08005', 'Barcelona', 'Barcelona', 'A-07001415', '933442308 ext. 4257', '933442313', NULL, NULL, 'VIAJES IBERIA, S.A.', 'CTRA. DE VALLDEMOSA, KM 7,4 PARC BIT EDIF. ORIZONA', '07121', 'PALMA DE MALLORCA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(766, '1685', 'TECNOLUZ', NULL, NULL, NULL, NULL, NULL, '617227523', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(769, '1686', 'ACCIONA INFRAESTREUCTURAS, S.A', 'C/ JORGE JUAN, 6 ENTLO', '03002', 'ALICANTE', NULL, 'A-81638108', '965145555', NULL, NULL, NULL, 'ACCIONA INFRAESTREUCTURAS, S.A', 'AVDA. DE EUROPA, 18 - P.E. LA MORALEJA', '28108', 'ALCOBENDAS', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(771, '1687', 'AGENCIA VALENCIANA DE TURISMO (CDT ALICANTE)', 'Monte Tosal, S/N', '03005', 'Alicante', 'Alicante', 'Q-9655770-G', '965935490', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(774, '1689', 'JPG PRODUCCIONES LED, S.L.', 'EXPLORADOR ANDRES 29 - 7º PTA 25', '46022', 'VALENCIA', 'VALENCIA', 'B-97752323', '963726038 / 620826997', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(777, '1690', 'MIGUEL GANDIA MICO', 'C/ VICTORIANO XIMENEZ 12-14', '03006', 'Alicante', 'Alicante', '21496386-B', '696936026', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(780, '1691', 'ALFONSO LÓPEZ LOSA (DISCO BAR EL PATIO)', 'C/ Monte, 3', '13700', 'Tomelloso', NULL, '06243586-Y', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(783, '1692', 'ANA BENNETT BAXTER', 'Avda. Deportista Mirian Blasco, 18 A - P4, 8º A', '03540', 'Playa San Juan', 'Alicante', '21409053-D', '965150484 / 670599527', NULL, NULL, 'annietass@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(785, '1693', 'VIAJES EL CORTE INGLES, S.A.', 'Gran Vía Fernando el Catolico, 3 bajo', '46008', 'Valencia', 'Valencia', NULL, '963107189', '963411046', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(788, '1694', 'GIOSEPPO S.L.U.', 'C/ MARIE CURIE, 38 - APARTADO CORREOS 5006', '03201', 'ELCHE', 'ALICANTE', 'B-03503034', '902407408 - 965682767', '965683068', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:19', '2026-02-18 13:03:19'),
(791, '1695', 'LABORATORIOS DIDIER RASE ESPAÑA', 'PLAZA DE CALVO SOTELO, 15-2º', '03001', 'ALICANTE', 'ALICANTE', NULL, '965140413 // 617306096', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(794, '1696', 'IBERDROLA', 'C/. MENORCA, 19 (EDIF. AQUA)', '46023', 'VALENCIA', 'VALENCIA', NULL, '963885418', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(797, '1697', 'ADALBERTO CASTRO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(799, '1698', 'G .V. CONSELLERIA DE MEDIO AMBIENTE, AGUA, URBANISMO Y VIVIENDA - D.G DE TERRITORIO Y PAISAJE', 'C/ FRANCISCO CUBELLS, 7', '46011', NULL, 'VALENCIA', 'S-46/11001-A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(802, '1699', 'NECOMPLUS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(805, '1700', 'CONCEJALIA DE MODERNIZACIÓN DE ESTRUCTURAS MUNICIPALES', 'C/ JORGE JUAN, 4', '03002', 'ALICANTE', 'ALICANTE', 'P-0301400-H', '965149577', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(808, '1701', 'EUROPEAN MOVEMENT INTERNATIONAL', 'Square de Meeus, 25', '1000', 'Brussels', 'Bruxelles', '0408310216', '3225083080', '3225083089', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(810, '1702', 'Mª VICTORIA COLLADO VIVES', 'C/. REYES CATOLICOS,17 ENTLO.', '03003', 'ALICANTE', 'ALICANTE', '21400561-G', '965929176', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(813, '1703', 'FOTOS Y FOTOGRAFOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(816, '1704', 'GRAN TEATRO DE ELCHE', 'c/ Kursaal, 3', '03203', 'Elche', 'Alicante', 'P-0300036-A', '699982121', NULL, NULL, NULL, 'INSTITUTO MUNICIPAL DE CULTURA', 'C/ Santos Medicos, 3', '03203', 'Elche', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(819, '1705', 'ADECCO TT, S.A.', 'C/ Reyes Catolicos, 50 Entlo.', '03003', 'ALICANTE', 'ALICANTE', 'A-80903180', '965123120', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(822, '1706', 'ALIAD CONOCIMIENTO Y SERVICIO, S.L.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(824, '1707', 'AYUNTAMIENTO L\'ALFAS DEL PI', 'C/ Federico Garcia Lorca, 11', '03580', 'L\'Alfas del Pi', 'Alicante', 'P-0301100-D', '619376464', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:20', '2026-02-18 13:03:20'),
(827, '1708', 'FUNDACIÓN BIBLIOTECA VIRTUAL MIGUEL DE CERVANTES', 'PASEO DE LA CASTELLANA, 24', '28046', 'MADRID', 'MADRID', 'G-82975350', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(830, '1709', 'GRUPO 7 VIAJES, S.A.', 'C/Treviño, 1 Entreplanta G', '28003', 'Madrid', 'Madrid', 'A-79438230', '913650008', '913650450', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(833, '1710', 'IMEX', 'C/ Charles Robert Darwin, 22 Parque Técnologico', '46980', 'Paterna', 'Valencia', NULL, '902901514', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(835, '1711', 'FACTOR CLAVE COMUNICACIÓN, S.L.', 'C/ Fernandez de la Hoz, 33 - 6º CTRO. DCHA.', '28010', 'Madrid', 'Madrid', 'B-83279513', '914111419 - 637360025 CLARA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(838, '1712', 'DOMOTICA LEVANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(841, '1713', 'CASA MEDITERRÁNEO', 'Avda. Elche, 5', '03008', 'Alicante', 'Alicante', 'V-54441977', '965986464', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(844, '1714', 'ESTUDIO FOTOGRAFICO JUAN CATALA, S.L.', 'C/ Roques, 17', '03730', 'Javea', 'Alicante', 'B-53016341', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(846, '1715', 'SANDRA KIEFT', NULL, NULL, NULL, NULL, 'X0318155-L', NULL, NULL, NULL, NULL, 'CARL ROBERT KIEFT', 'C/ NAPOLES 15', NULL, 'BENIDORM', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(849, '1716', 'EVENTOS Y COMUNICACIÓN, S.L.', 'C/. ISLA GRACIOSA, 2 LOFT 58', '28703', 'S. S. DE LOS REYES', 'MADRID', NULL, '916238814', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(852, '1717', 'UNIVERSIDAD DE ALICANTE - VICERRECTOR DE EXTENSIÓN UNIVERSITARIA', 'Campus de San Vicente del Raspeig, Apdo. 99', '03080', 'Alicante', 'Alicante', 'Q-0332001-G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(855, '1718', 'INICIATIVAS ENCUENTRO MEDITERRÁNEO, S.L.U.', 'C/ Portugal, 29 Entlo. Dcha.', '03001', 'Alicante', 'Alicante', 'B-54198163', '628178632', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(858, '1719', 'IMAGINA EXPERIENCIAS', 'C/ Los Caminos 7A', '28043', 'Madrid', 'Madrid', 'B-83380253', '917160980 / 660324100', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(860, '1720', 'ATM', NULL, NULL, NULL, NULL, NULL, '630368390', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:21', '2026-02-18 13:03:21'),
(863, '1721', 'RESTAURANTE MONASTRELL', 'C/ Rafael Altamira, 7', '03002', 'Alicante', 'Alicante', 'B-53145546', '965980136', NULL, NULL, NULL, 'BENIMAGREL 52, S.L.', 'C/ Rafael Altamira, 7', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(866, '1722', 'ADDING-OMNICOM, S.L.', 'C/ Viriato, 47 Planta 12 Edificio Numancia 1', '08014', 'Barcelona', 'Barcelona', 'B-60364676', '934192814', '934193769', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(869, '1723', 'STRATEGYCOMM', 'C/ Montserrat, 60 4ª Planta', '08302', 'Mataró', 'Barcelona', 'B-08863508', '937901253', '937904999', NULL, NULL, 'JUAN LOPEZ COMUNICACIÓN, S.L.U.', 'C/ Montserrat, 60 4P', '08302', 'Mataró', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(871, '1724', 'AMERI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(874, '1725', 'PLANTA 18 by SERVIBROKER, S.L.', 'Paseo de la Cstellana, 140 Planta 18', '28046', 'Madrid', 'Madrid', 'B-82856402', '915644947', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(877, '1726', 'PRESSTOUR VIAJES', 'C/ Ayala, 83', '28006', 'Madrid', 'Madrid', NULL, '902404033', '915549123', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(880, '1727', 'CARPETAS ABADIAS, S.L.', 'POL. IND. LA ESCANDELLA - C/ FRANCIA, 2 - 4', '03698', 'AGOST', 'ALICANTE', 'B-53286290', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(883, '1728', 'EDITORIAL PRENSA ALICANTINA, S.A.', 'Avda. Dr. Rico, 17', '03005', 'Alicante', 'Alicante', 'A-08884439', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(885, '1729', 'IGNACIO FAULIN, S.L.', 'Avda. de Portugal, 18 - 3º I', '26001', 'Logroño', 'La Rioja', 'B-26294777', '941223158 / 677563858', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(888, '1730', 'PIERRE VACANCES ALTEA HILLS', 'C/ SUECIA S/N', '03599', 'ALTEA', NULL, 'B-84078419', '966881006 - 620804777 BIANCA', NULL, NULL, NULL, 'SOCIEDAD DE EXPLOTACIÓN TURÍSTICA PIERRE ET VACANCES ESPAÑA, S.L.', 'AVDA. DIAGONAL 449, 1º PLANTA', '08036', 'BARCELONA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(891, '1731', 'HOTEL SH IFACH', 'C/ JUAN CARLOS I, S/N', '03710', 'CALPE', NULL, 'B-97387294', '965874500', NULL, NULL, NULL, 'NEREIDA MEDITERRÁNEA, S.L.', 'C/ JUAN CARLOS I, S/N', '03710', 'CALPE', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(894, '1732', 'ICCC ESPAÑA', 'BUZON 79 SIERRA DE ALTEA', '03599', 'ALTEA', 'ALICANTE', 'X1097501X', '639643637', NULL, NULL, NULL, 'DR. ELIN RIEGEL', 'BUZON 79 SIERRA DE ALTEA', '03599', 'ALTEA', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(897, '1733', 'JOSE MIGUEL LINARES BALLESTEROS', 'C/ CORREDERA,8', NULL, 'VÉLEZ - BLANCO', 'ALMERÍA', '44273869-L', '658129395', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(899, '1734', 'IBEROCRUCEROS', 'Avda. de Burgos 89, 4ª planta, Edif. 3', '28050', 'Las Tablas (Ciudad empresarial Adequa)', 'Madrid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:22', '2026-02-18 13:03:22'),
(902, '1735', 'CENTRO GASTÓN CASTELLO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(905, '1736', 'ALTAE BANCO PRIVADO (GRUPO CAJA MADRID)', NULL, NULL, NULL, NULL, NULL, '913915380', '913915404', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(908, '1737', 'LABORATORIOS FARMACEUTICOS ROVI, S.A.', 'C/ JULIÁN CAMARILLO, 35', '28037', NULL, 'MADRID', 'A-28041283', '913756238', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(910, '1738', 'DELLAR DAVIES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(913, '1739', 'HOTEL SPA LA ROMANA', 'CV-840 LA ROMANA - CAMINO JUNTO COOPERATIVA', '03669', 'LA ROMANA', 'ALICANTE', 'B-53936423', '966192600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(916, '1740', 'APPLIED BIOSYSTEMS EUROPE', NULL, '2913', 'LV Nieuwerkerk aan den Ijssel', 'Holanda', 'BTWNL007.123.838.B01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(919, '1741', 'PATRONATO DEL MISTERI DE ELCHE', 'C/ MAJOR DE LA VILA, 27', '03202', 'ELCHE', 'ALICANTE', 'Q-0300679-H', NULL, NULL, NULL, NULL, 'PATRONATO DEL MISTERI DE ELCHE', 'C/ MAJOR DE LA VILA, 27', '03202', 'ELCHE', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(922, '1742', 'WORD PERFECT TRADUCTORES', 'C/ Juan Ramón Jimenez, 14 - 1º Centro', '03203', 'Elche', 'Alicante', '74391543-K', '965426812', NULL, NULL, NULL, 'MARIA FERNANDA BOZIO DEL MASTRO', 'C/ Juan Ramón Jimenez, 14 - 1º Centro', '03203', 'Elche', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(924, '1743', 'EDUCANOVA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(927, '1744', 'NATURBUS, S.L', 'AVDA. MENENDEZ PIDAL, Nº 13', '46009', 'VALENCIA', 'VALENCIA', 'B-01271469', '965984184', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(930, '1745', 'ERGON TIME, S.A.', 'C/ Arbolera, 1', '28220', 'Majadahonda', 'Madrid', 'A-81440273', '916362930', NULL, NULL, NULL, 'ERGON TIME, S.A.', 'Plaza Josep Pallach, 12', '08035', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(933, '1746', 'BARRACA ALACANT JOVE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(935, '1747', 'LASTMINUTE.COM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:23', '2026-02-18 13:03:23'),
(938, '1748', 'ROYAL CARIBDN CRUISES', 'Pza. Urquinaona 6, 8º planta', '08010', 'Barcelona', 'Barcelona', NULL, '629458074', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(941, '1749', 'DISTRIBUIDORA GELMA, S.L.', 'AVDA. NEPTUNO, 5 NAVE G', '03006', 'ALICANTE', 'ALICANTE', 'B-03116589', '657839002', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(944, '1750', 'ANDREO, S.A.', 'C/ MERCURIO, S/N', '04230', 'HUERCAL', 'ALMERIA', 'A-30051353', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(946, '1751', 'VIDEAC, S.A.', 'Avda. Aguilera, 36', '03006', 'Alicante', 'Alicante', NULL, '609561242', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(949, '1752', 'HOSPITAL NISA AGUAS VIVAS', 'C/ MARÍA DE MAEZTU,5', NULL, 'ELCHE', 'ALICANTE', 'A-46663324', '618731794', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(952, '1753', 'LABORATORIOS ROBERT, S.A.', 'AVDA. DIAGONAL, 549, 5ª PLANTA', '08029', NULL, 'BARCELONA', 'A-08244154', '606342713', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(955, '1754', 'GRU', 'C/. GENERAL MARTINEZ CAMPOS, 44-1º', '28010', 'MADRID', 'MADRID', 'A-08644932', '913836000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(958, '1755', 'ENCLAVADOS ASOCIACIÓN CULTURAL', NULL, NULL, NULL, NULL, NULL, '675192969', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(960, '1756', 'PETER DIECKMANN', NULL, NULL, NULL, NULL, NULL, '966873036', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(963, '1757', 'EQUIPO KAPTA', 'Pza. Sta. Catalina de los donados, 2 1º izq.', '28013', 'Madrid', 'Madrid', NULL, '915427252', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(966, '1758', 'ADA COMMUNICATIONS', 'Rambla de Poblenou, 35, 3-3', '08005', 'Barcelona', 'Barcelona', '51462172-V', '932953226', NULL, NULL, NULL, 'MANUELA PEREZ BERROCAL', 'Rambla de Poblenou, 35, 3-3', '08005', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(968, '1759', 'CARLOS ORTEGA GONZALEZ', NULL, NULL, NULL, NULL, 'B-58881954', '629860946', NULL, NULL, NULL, 'WARNER CHILCOTT', 'WTC ALMEDA PARK ED. 1 -  2º  PLACA DE LA PAU S/N', '08940', 'CORNELLA DE LLOBREGAT', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(971, '1760', 'ANTONIO PUIG, S.A.', 'Travesera de Gracia, 9', '08021', 'Barcelona', 'Barcelona', 'A-08158289', '932074253', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:24', '2026-02-18 13:03:24'),
(974, '1761', 'ENCARNI LÓPEZ', NULL, NULL, NULL, NULL, NULL, '647659241', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(976, '1762', 'NOVARTIS FARMACEUTICA, S.A.', 'GRAN VÍA CORTS CATALANES, 764', '08013', NULL, 'BARCELONA', 'A-08011074', '629059970', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(979, '1763', 'ASOCIACIÓN AHASALAM', NULL, NULL, NULL, NULL, NULL, '667680602', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(982, '1764', 'INFORMACIÓN TV', 'AVDA. DOCTOR RICO, 17', '03005', NULL, 'ALICANTE', 'B-54496815', '699483759', NULL, NULL, NULL, 'PRIME TV ALICANTINA, S.L.', 'AVDA. DOCTOR RICO, 17', '03005', 'ALICANTE', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(985, '1765', 'VIAJES HISPANIA (BENIDORM)', 'C/ Gambo, 6 - 2º - 6º H', '03503', 'Benidorm', 'Alicante', NULL, '965866080', '966804000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(988, '1766', 'RESTAURANTE TORRE DE REJAS', 'CAMINO DE BENIMAGRELL, 47', '03559', 'Alicante', NULL, 'B-03732195', '965262631', NULL, NULL, NULL, 'TORRE DE REIXES, S.L.U.', 'CAMINO DE BENIMAGRELL, 47', '03559', 'ALICANTE', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(990, '1767', '*QUALITY EVENT, S.L.', 'Avda. de Cordoba, 9 Esc B 1º D', '28026', 'MADRID', 'MADRID', 'B-83215970', '915005859 - 915006044 F', '914765090', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(993, '1768', 'DEEVENTOSS', 'Avda. Juan Gil Albert, 3', '03804', 'ALCOY', 'ALCOY', '21667087-Y', '678971209', NULL, NULL, NULL, 'LUIS MIGUEL VERDU JORDA', 'Avda. Juan Gil Albert, 3', '03804', 'Alcoy', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(996, '1769', 'UNIVERSIDAD DE ALICANTE - INSTITUTO SINTESIS ORGANICA DE CIENCIAS', 'Campus de San Vicente del Raspeig, Apdo. 99', '03080', 'Alicante', 'Alicante', 'Q-0332001-G', '666737712', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(998, '1770', 'COMPARSA DE MOROS SAUDITAS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(1001, '1771', 'EUROMONTAJES ELCA  S.L.', 'LEPOLDO NAVARRO 11', '03390', 'BENEJUZAR', 'ALICANTE', 'B 53244091', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(1004, '1772', 'I:VENTECH', 'LUDWIGSTRABE 17-19', NULL, 'NEU-ISENBURG', NULL, NULL, '4961028829790', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(1007, '1773', 'PATRICK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:25', '2026-02-18 13:03:25'),
(1008, '1774', 'SANVITRANS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1011, '1775', 'JOHN DOE', 'Rambla de Catalunya, 53 4º Fu', '08007', 'Barcelona', 'Barcelona', 'B-64372675', '934870892 / 655552148 J', NULL, NULL, NULL, 'JOHN DOE, S.L.U.', 'Avinguda Princep D\'Asturies, 4 2º 2ª A', '08012', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1014, '1776', 'SERVICIOS AUDIOVISUALES AREA TECNICA, S.L.', 'C/ Maracaibo, 30', '08030', 'Barcelona', 'Barcelona', NULL, '933404268 / 615169522', '933529437', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1017, '1777', 'COMUNIDAD DE PROPIETARIOS CENTRO COMERCIAL GRAN VIA', 'C/ JOSÉ GARCÍA SELLÉS, Nº 2', '03015', 'ALICANTE', 'ALICANTE', 'H-53314811', '965250642', '965257158', NULL, 'gerente@ccgranvia.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1019, '1778', 'INVITROGEN', '102 FOUNTAIN CRESCENT, INCHINNAN BUSINESS PARK', NULL, 'PAISLEY PA4 9RE', 'UNITED KINGDOM', 'GB 263 3947 41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1022, '1779', 'SISTEMAS AVANZADOS TELECOM-LEVANTE, S.L.', 'C/ Pintor Manuel Baeza, 47', '03550', 'San Juan', 'Alicante', 'B-03121225', '965654321 / 609410536', '965654448', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1025, '1780', '* VOLVO OCEAN RACE, S.L.U.', 'MUELLE Nº 10 DE LEVANTE, PUERTO  DE ALICANTE', '03001', 'Alicante', 'Alicante', 'B-54460910', '966011100 - 966011173 M - 677888691 MARTA', NULL, NULL, NULL, NULL, 'MUELLE Nº 10 DE LEVANTE, PUERTO  DE ALICANTE', '03001', 'ALICANTE (SPAIN)', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1028, '1781', '*HOTEL HUSA ALICANTE GOLF', 'Avda. de las Naciones, S/N', '03540', 'Alicante', 'Alicante', 'A-08791790', '965235000 EVENTOS 965235009', NULL, NULL, NULL, 'HOSTELERIA UNIDA S.A.', 'C/ Sabino de Arana, 27', '08027', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1030, '1782', 'DAVID CARBONE', NULL, NULL, NULL, NULL, NULL, '625154729', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1033, '1783', 'HALCON VIAJES', 'C/ Enrique Granados, 6 Edificio Globalia - Letra A', '28224', 'Pozuelo de Alarcón', 'Madrid', 'A10005510', '915425064', NULL, NULL, NULL, 'VIAJES HALCON,S.A.U.', 'C/ JOSÉ ROVER MOTTA,27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1036, '1786', 'IZASA, S.A.', 'AVDA. DE LA CONSTITUCIÓN. 6 1º B', '46009', 'VALENCIA', 'VALENCIA', 'A-28114742', '625154729 -  DAVID', '902223366', NULL, 'david.carbone@izasa.es', 'IZASA, S.A.', 'PLAZA DE EUROPA, 21-23', '08908', 'L\'HOSPITALET DE LLOBREGAT', 'BARCELONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1038, '1787', 'INCOMING SERVICES ALICANTE', 'Centro Negocios Alicante, Muelle de Poniente, S/N', '03001', 'Alicante', 'Alicante', NULL, '965928616 / 669477561', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1041, '1788', 'JV GRACIA', NULL, NULL, NULL, NULL, NULL, '965112458', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:26', '2026-02-18 13:03:26'),
(1044, '1789', 'GLOBAL DMC', 'C/ Teniente Aguado, 2 Bajo', '03009', 'Alicante', 'Alicante', 'B-53961595', '965242622 / 610200812', '965244917', NULL, NULL, 'GLOBAL DMC VIAJES Y AVENTURA, S.L.', NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1047, '1790', '*RESTAURANTE ROS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1049, '1791', 'CARLSON WAGONLIT TRAVEL (BCN)', 'C/ Aragon, 182 Entlo.', '08011', 'Barcelona', 'Barcelona', 'B-81861304', '936039602', '934535672', NULL, NULL, 'CARLSON WAGONLIT ESPAÑA, S.L.U.', 'C/ PRINCESA, 3 4º PLANTA', '28008', 'MADRID', 'MADRID', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1051, '1792', 'PARROQUIA SAN PEDRO APÓSTOL', 'Plaza Constitución, 12', '03698', 'Agost', 'Alicante', 'R-0300117-I', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1054, '1793', 'ICCC INTERNACIONAL', 'HJELMARBERGETS FORETAGSCENTER, GRISGROPVEGEN, 5', 'SE-70236', 'OREBRO', 'SUECIA', 'SE875002494801', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1057, '1794', 'PARROQUIA DE LA SANTA CRUZ', 'AVDA. DE ELDA, Nº 17', '03610', 'PETRER', 'ALICANTE', 'R0300240I', '669605300 JESUS', NULL, NULL, NULL, 'PARROQUIA DE LA SANTA CRUZ', 'AVDA. DE ELDA, Nº 17', '03610', 'PETRER', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1060, '1795', 'THE EVENTS COMPANY', 'Unit, 7 Thornhill Road', 'B98 9ND', 'North Moons Moat', 'Reddith', 'GB799795424', '0800 0685707', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1062, '1796', 'EVENTOS & CONGRESOS', NULL, NULL, NULL, NULL, NULL, '620066799', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1064, '1797', 'CECAUTO LEVANTE, S.A.', 'AVDA. CARLOS MARX, 59', '46026', 'HORNO DE ALCEDO', 'VALENCIA', 'A-46893707', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1066, '1798', '4GLOBAL SOURCING S.L.', 'AVDA. MAISONAVE 41, 6 F', '03003', 'ALICANTE', NULL, 'B53878229', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1069, '1799', 'LUFTHANSA CITY CENTER REISEBÜROPARTHNER GMBH', 'LYONER STR. 36', 'D-60528', 'FRANKFURT', 'FRANKFURT', 'DE814049877', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1071, '1800', 'AKTIVE-LIfE SPORTS & EVENTS', 'C/ Cataluña, 34', '03130', 'Santa Pola', 'Alicante', 'B-54528641', '966697686/669497127', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1074, '1801', 'GENERAL DE PRODUCCIONES Y DISEÑO, S.A.', NULL, NULL, NULL, NULL, NULL, '625356205', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1077, '1802', 'JOSANZ', 'Avda. Independencia, 8', '24001', 'Leon', 'Leon', NULL, '620255363', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:27', '2026-02-18 13:03:27'),
(1080, '1803', 'LA NUEZ PRODUCCIONES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1083, '1804', 'CREATIVA MEDIA', 'C/ MIGUEL HERNANDEZ, Nº 32', '03680', 'ASPE', NULL, '48354519-D', '966110202 - 654067274', NULL, NULL, NULL, 'JOSÉ LUIS VICENTE PAVÍA', 'C/ MIGUEL HERNANDEZ, Nº 32', '03680', 'ASPE', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1085, '1805', 'HOTEL ARECA', 'C/ DEL LIMÓN, Nº 2', '03320', NULL, 'ELCHE', 'B53587390', 'ALICANTE 965685477', NULL, NULL, NULL, 'HOTEL ILLANOS, S.L.U.', 'C/ DEL LIMÓN, Nº 2', '03320', 'ELCHE', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1088, '1806', 'EMILIO SEVILLA LORENZO', NULL, NULL, NULL, NULL, NULL, '636495117', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1092, '1807', 'ESTUDIO MEDIA', NULL, NULL, NULL, NULL, NULL, '968352411/615389081/868972586A', '9683524411', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1094, '1808', 'RESTAIRANTE JUAN XXIII', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1097, '1809', 'AB BRANDON', 'P.O. BOX 48030', 'SE-41821', 'GOTHENBURG', 'SWEDEN', 'SE55644703901', '+46317644700', '+467644701', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1099, '1810', 'VICENTE HENRY', NULL, NULL, NULL, NULL, NULL, '687458621', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1102, '1811', 'JOSE SAVALL, S.L.', 'AVDA. JIJONA, 8', NULL, 'ALICANTE', 'ALICANTE', 'B-03115003', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1105, '1812', 'SHOWSPORTS, S.L.', 'C/ Bristol, 28 bis Pol. Europolis', '28232', 'Las Rozas', 'Madrid', NULL, '916374345', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1108, '1813', 'INSTITUT MUNICIPAL DE TURISME D\'ELX', 'C/ Filet de Fora, 1', '03203', 'Elche', 'Alicante', 'P-0300044-E', '966658140 / 647310670', '966658141', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1110, '1814', 'COLEGIO DE GRADUADOS SOCIALES DE ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1113, '1815', 'CHIESI ESPAÑA, S.A.', 'TORRE REALIA PLCA DE\'EUROPA, 41-43 PLANTA 10', '08029', 'L´HOSPITATLET DE LLOBREGAT', 'BARCELONA', 'A-08017204', '646064497', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:28', '2026-02-18 13:03:28'),
(1116, '1816', 'ALOHA CONGRESS', 'Joan Güell, 144-148', '08028', 'Barcelona', 'Barcelona', NULL, '933633954', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1119, '1817', 'ISLA MARINA ALICANTE', 'Avda. Villajoyosa, 4', NULL, 'Alicante', 'Alicante', 'B-53516860', '607355818', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1121, '1818', 'TAFE PUBLICIDAD', NULL, NULL, NULL, NULL, NULL, '915647808', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1124, '1819', 'GRUPO MOLCA', 'P.I. Enchilagar del Rullo, calle 7 parcela 117 A', '46191', 'Vilamarzant', 'Valencia', 'B-97238927', '915647808 / 962712284', NULL, NULL, NULL, 'MOLCA SPORTS & EVENTS, S.L.', 'P.I. Enchilagar del Rullo, calle 7 parcela 117 A', '46191', 'Vilamarzant', 'Valencia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1127, '1820', 'BLINKER ESPAÑA, S. A. U.', 'POL. INDUSTRIAL LAS ATALAYAS, PARCELAS 11-12-13', '03114', 'ALICANTE', 'ALICANTE', 'A-03813474', '966102500', '966102501', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1130, '1821', 'CPM EXPERTUS FIELD MARKETING, S.A.', 'HENRI DUNANT 9-11 3\" PLANTA PARQUE EMPRESARIAL A7', '08174', 'BARCELONA', 'SANT CUGAT DEL VALLÉS', 'A-61777637', '965107201 / 605179101 S', '965114998', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1133, '1822', 'ALBERTO SANTACRUZ RODRIGUEZ', 'C/ SEVILLA 108 - 3º DERECHA', NULL, 'ALICANTE', 'ALICANTE', '21477102-R', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1135, '1823', 'VIAJES EL CORTE INGLES, S.A. (ALICANTE)', 'C/ Arquitecto Morell, 4 Bajo', '03003', 'Alicante', 'Alicante', NULL, '965132419', '965921706', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1138, '1824', 'MCI GROUP', 'Alcalde Sanz de Baranda, 45 bajos', '2809', 'Madrid', 'Madrid', NULL, '914009384', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29');
INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(1141, '1825', 'HOGUERA MONJAS SANTA FAZ', NULL, NULL, NULL, NULL, NULL, '616513336 -  965116731', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1144, '1826', 'STAGG & FRIENDS GMBH', 'TERSTEEGENSTRASSE, 28', '40474', 'DUSSELDORF', 'GERMANY', 'DE-230388248', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1147, '1827', 'GARU DISEÑO Y SERVICIOS, S.L.', 'ACACIA, 23', '28850', 'TORREJON DE ARDOZ', 'MADRID', 'B-80380397', '916778202', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1149, '1828', 'EVENTS & CO', 'Gran vía Marq. del Turia, 71 - 3º', '46005', 'Valencia', 'Valencia', NULL, '963519319 / 636256841', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:29', '2026-02-18 13:03:29'),
(1152, '1829', 'AUVYCOM', 'C/. MADRESELVA, 32 C. COMERCIAL  URB. TORRE GUILL', '30833', 'SANGONERA LA VERDE', 'MURCIA', NULL, '968349285/  679861577', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 35.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1155, '1830', 'CASA MEDITERRANEA CAMECO,S.L.', 'C/ SAN FERNANDO 53', '03001', NULL, 'ALICANTE', 'B-53628178', '618580778', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1158, '1831', '* HOTEL EUROSTARS LUCENTUM', 'AVDA. ALFONSO X EL SABIO, 11', '03002', 'ALICANTE', 'ALICANTE', 'B-65440109', '966590700', '966590710', NULL, NULL, 'GALENA HOTELS, S.L.', 'C/ PRINCESA 58', '08003', 'BARCELONA', 'BARCELONA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1160, '1832', 'B BIOSCA', NULL, NULL, NULL, NULL, NULL, '965636000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1163, '1833', 'MERCEDES - BENZ ESPAÑA, S.A.', 'Avda. de Bruselas, 30', '28108', 'Alcobendas', 'Madrid', 'A-79380465', '914846286', '914846272', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1166, '1834', 'SERGIO LÓPEZ', NULL, NULL, NULL, NULL, NULL, '650448022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1169, '1835', 'CONGRESOS XXI, S.L.', 'C/. GRAN VIA, 81 5º DPTO. 5', '48011', 'BILBAO', 'BILBAO', 'B95179537', '944278855', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1172, '1886', 'GARDINER & CO', '95 WEST REGENT STREET', 'G2 2BA', 'GKASGOW', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1174, '1887', 'ITACA COMUNICACIÓN Y MARKETING, S.L.', 'C/. RIO GUADALIX, 16', '28791', 'SOTO DEL REAL', 'MADRID', 'B-85985661', '918477348', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1177, '1888', 'TABIMED', NULL, NULL, 'ALICANTE', 'ALICANTE', NULL, '965901433', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1180, '1889', 'ITB DMC', 'San Vicente, 16 3º 1ª', '46002', 'Valencia', 'Valencia', NULL, '963921288 / 606342028', '963921402', 'www.itbdmc.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1183, '1890', 'JOSE', NULL, NULL, NULL, NULL, NULL, '607034417', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1185, '1891', 'ROTH&LORENZ GmbH', 'Waldburgstr. 17/19', '70563', 'Stuttgart', 'Germany', '147798181', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1188, '1892', 'ENGLOBA', 'Gobernador Viejo, 29', '46003', 'Valencia', 'Valencia', NULL, '902760755', '963158910', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:30', '2026-02-18 13:03:30'),
(1191, '1893', 'MARISA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1194, '1894', 'CLAMAJE', NULL, NULL, NULL, NULL, NULL, '966093291 - 639745458', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1197, '1895', 'SINDICATO DE EMPLEADOS PUBLICOS', 'Alcalde Alfonso de Rojas, 2 Entreplanta', '03004', 'Aliacnte', 'Alicante', 'G-54081807', '965219211', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1199, '1896', 'GRUPO HELADOS ALACANT', NULL, NULL, NULL, NULL, NULL, '965666403', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1202, '1897', 'COLEGIO SANTA TERESA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1205, '1898', 'AUREA COMUNICACIÓN Y EVENTOS', 'C/ Treviño, 1 Entreplanta G', '28003', 'Madrid', 'Madrid', 'B-84204262', '913650008', NULL, NULL, NULL, 'A2R TRAVEL SYSTEM, S.L.', 'C/ Treviño, 1 Entreplanta G', '28003', 'Madrid', 'Madrid', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1208, '1899', 'APC AUDIO C.B.', 'C/ MANUEL ARNALDOS PEREZ, 27', '30500', 'MOLINA DE SEGURA', 'MURCIA', 'E-73537839', '606537140 - 639550800', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1210, '1900', 'REBECA CALDERÓN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1213, '1901', 'RAUL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1216, '1902', 'SUMINISTROS SOLUTIUM', NULL, NULL, 'ALICANTE', 'ALICANTE', NULL, '902636108 / 616490378', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1219, '1903', 'GENERALITAT VALENCIANA - OFICINA ELECTORAL - CONSELLERIA DE GOBERNACIÓN', 'C/ HISTORIADOR CHABÁS, 2', '46003', 'VALENCIA', 'VALENCIA', 'S-4611001-A', '638123838 - Araceli', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1222, '1904', 'IDEAS DE PUBLICIDAD & COMUNICACIÓN', 'Ganduxer, 14-16 entlo. 14', '08021', 'Barcelona', 'Barcelona', NULL, '932005739', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1224, '1905', 'NOVEX PHARMA LABORATORIOS, S. L.', 'VÍA DE LOS POBLADOS, 3 EDIF. 7- 8 PLANTA 5', '28033', 'MADRID', 'MADRID', 'B-83408658', '682730934', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:31', '2026-02-18 13:03:31'),
(1227, '1906', 'AMERICAN EXPRESS BARCELO VIAJES.', 'C/ Llull, 321-329 7º Edif. Cinc', '08019', 'Barcelona', 'Barcelona', 'B85376630', '932550008 / 78229292', '902517841', NULL, NULL, 'AMERICAN EXPRESS BARCELO VIAJES, S.L.', 'Juan Ignacio Luca de Tena, 17 - 6º', '28027', 'Madrid', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1230, '1907', 'PROYECSON, S.A.', 'Ronda Guglielmo Marconi, 4 Parque Técnologico', '46980', 'Paterna', 'Valencia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1233, '1908', 'CINES ABC', NULL, NULL, NULL, NULL, 'A-46086773', '963531860', NULL, NULL, NULL, 'EXCIN, S.A.', 'ROGEL DE LAURIA, Nº 21 - 3º PISO', '46002', 'VALENCIA', 'VALENCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1235, '1909', 'HALCON VIAJES', 'Avda. de Denia, 47', '03013', 'Alicante', 'Alicante', NULL, '965269962', '965263797', NULL, NULL, 'VIAJES HALCON,S.A.U.', 'C/ JOSÉ ROVER MOTTA,27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1238, '1910', 'ESTEFANÍA AGUADO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1241, '1911', 'VIAJES EL CORTE INGLES (BCN)', NULL, NULL, NULL, NULL, NULL, '933635760', '933210143', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1244, '1912', 'CLUB ROTARIOS', NULL, NULL, NULL, NULL, NULL, '649648238', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1247, '1913', 'GROUPE SEB', NULL, NULL, NULL, NULL, NULL, '933063736', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1249, '1914', 'DYLVIAN, S.L.', 'C/ DOCTOR SAPENA 54, 7º A', '03013', 'ALICANTE', 'ALICANTE', 'B-64911720', '965266161', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1252, '1915', 'GIRONA STUDIO', NULL, NULL, NULL, NULL, NULL, '661572607', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1255, '1916', 'HOSPITAL DE ALICANTE (DRA. CARRIÓN)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1258, '1917', 'AMERICAN EXPRESS BARCELO VIAJES', 'C/. JUAN IGNACIO LUCA DE TENA, 17 6º NORTE', '28027', 'MADRID', 'MADIRD', NULL, '913858647', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1260, '1918', 'ANA PASTOR', 'MARIAN BLASCO Nº 7 PORTAL 6 8º PUERTA 3', '03016', 'SAN JUAN', 'ALICANTE', '52649621-E', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:32', '2026-02-18 13:03:32'),
(1263, '1919', 'AUDIOVISUALES BEAMER, S.L.', 'Calle Lepanto, 151 Local', '08013', 'Barcelona', 'Barcelona', 'B-62956933', '932469022 / 689046502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1266, '1920', 'VICTOR MIRANDA REY', NULL, NULL, 'Benidorm', 'Alicante', NULL, '678566918', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1268, '1921', 'HELANA SALVADOR', NULL, NULL, NULL, NULL, NULL, '655569381 - Marcos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1271, '1922', 'CALZADOS DANUVIO, S.L.', 'S Ramon Y Cajal (Pol Ind Las Saladas), 17', '03320', 'TORRELLANO', 'ALICANTE', NULL, '965683911', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1274, '1923', 'UNIVERSIDAD DE  ALICANTE DPTO. DE TRADUCCIÓN E INTERPRETACIÓN', 'CAMPUS DE SAN VICENTE DEL RASPEIG APTDO. 99', '0380', NULL, 'ALICANTE', 'Q-0332001-G', '965261822 // 607316227 movil Irene', NULL, NULL, 'irene.prufer@ua.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1277, '1924', 'JOAN VICENT HERNANDEZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1280, '1925', 'ETI GROUP EUROPE, S.L.', 'C/ RAFAEL ALTAMIRA, Nº 2 PLANTA PRINCIPAL', '03002', 'ALICANTE', NULL, 'B-54510185', '965202539', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1282, '1926', 'COMUNIDADD DE PROPIETARIOS LA ERMITA', 'C/ POSTIGOS ESQUINA CAMINO LAS PARRAS', NULL, NULL, NULL, 'H-54390208', '966083998', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1285, '1927', 'SEMFYC CONGRESOS', 'CARRER DEL PI, Nº 11, PLANTA 2ª  OFICINA 13', '08002', 'BARCELONA', 'BARCELONA', 'B-61444766', '933177129', NULL, NULL, NULL, 'CONGRESOWS Y EDICIONES SEMFYC, S.L.', NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1288, '1928', 'FUNDACIÓN KONRAD ADENAUER', 'C/VILLANUEVA Nº 43 - 2º DCHA', '28001', 'MADRID', 'MADRID', 'N-0041494-F', '917811202', '915756066', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1291, '1929', 'GRUPO PROCESS', 'C/ CRONOS, Nº 20 , EDIFICIO 4 , 1 , 6', '28037', 'MADRID', 'MADRID', 'B-83293282', '913771423', '913774965', NULL, NULL, 'BETAPROCESS, S.L.', 'CRONOS, 20 - EDIFICIO IV -PLANTA 1ª - 6', '28037', 'MADRID', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1294, '1930', 'LEFTERIS TAVELIS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1296, '1931', 'VOLVO CARS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:33', '2026-02-18 13:03:33'),
(1299, '1932', 'LA METRO TELEVISIÓN DE ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1302, '1933', 'AUDIOVISUAL SOLUTIONS (GB) LIMITED', 'The stores cottege, 1 High street', 'TN 19 7 AP', 'Etchingham', 'East Sussex', '782448695', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1305, '1934', 'GRUPO ESOC', 'C/ Rio Duero, 30 local 10', '03690', 'San Vicente del Raspeig', 'Alicante', 'B-03168820', '965229940', '965672752', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1308, '1935', 'COMUNIDAD DE PROPIETARIOS MIRADOR DE LOTORREN', NULL, NULL, 'SAN VICENTE', 'ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1310, '1936', 'SOCIEDAD PROYECTOS TEMATICOS DE LA COMUNIDAD VALENCIANA', 'Plaza del Temple, 6 4º Planta', '46003', 'Valencia', 'Valencia', NULL, '608360536', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1313, '1937', 'DIERESIS & CCI PUBLICIDAD Y MEDIOS, S.L.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1316, '1938', 'CLUB DEPORTIVO AGUSTINOS ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1319, '1939', 'USAC ALFEREZ ROJAS NAVARRETE', 'CAMINO FONDO PIQUERES, S/N', '03009', 'ALICANTE', 'ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1322, '1940', 'HERMANDAD', NULL, NULL, 'Alicante', 'Alicante', NULL, '695026620', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1324, '1941', 'CUADRIFOLIO DISEÑO, S.L.', 'C/ Olimpo, 48', '28043', 'Madrid', 'Madrid', 'B-82459652', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1327, '1942', 'AMYC, ALQUILER MODULOS Y CASETAS, S.L.', 'Poligono Industrial, 12 parcela 3 y 4', '03690', 'San Vicente del Raspeig', 'Alicante', NULL, '635900600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1330, '1943', 'GRUPO HEFAME', NULL, NULL, 'Santomera', 'Murcia', NULL, '968277810 / 629727051', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1333, '1944', 'VICENTE SOLER HERNANDEZ, S.L.', 'C/ Rubens, 17', '03009', 'Alicante', 'Alicante', 'B-53266250', '965170827 / 629882165', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:34', '2026-02-18 13:03:34'),
(1335, '1945', 'DAD AUDIOVISUALES', 'AVDA. CATALUÑA, Nº 14', '03540', NULL, NULL, '54205618- T', '965264288 / 645400898 / 625910633', NULL, NULL, NULL, 'DAVID ANIBAL ABUD ACUÑA', NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1338, '1946', 'FUNDACIÓN DE LA COMUNIDAD VALENCIANA AUDITORIO DE LA DIPUTACIÓN DE ALICANTE, ADDA', 'AVDA. DE LA ESTACIÓN, Nº 6', '03005', 'ALICANTE', 'ALICANTE', 'G-54.526.645', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1342, '1947', 'GRUPO SÖRENSEN SBA, S.L.', 'C/ Garibay, 7 - 2ª Planta', '28007', 'Madrid', 'Madrid', 'B-79838264', '915798230', '915796959', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1344, '1948', 'PLEAMAR', NULL, NULL, NULL, NULL, NULL, '608927687', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1347, '1949', 'GRUPO TAVEX, S.A.', 'C/ Genova, 17 - 6ª  Planta', '28004', 'Madrid', 'Madrid', 'A20000162', '913911350 / 689364256', NULL, 'www.tavex.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1349, '1950', 'NUEVA ESTRATEGIA DE COMUNICACIÓN INTEGRAL, S.L.U.', 'Plaza del Ayuntamiento, 2 3º Planta 2', '03002', 'Alicante', 'Alicante', 'B-53539961', '965203177 /677587044', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1352, '1951', 'GRUPO VECTALIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1355, '1952', 'VIAJES 2000, S.A.', 'PASEO DE LA CASTELLANA, 228-230', '28046', 'MADRID', 'MADRID', 'A-07055445', '913237814', '913147307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1358, '1953', 'CENTRAL DE VIAJES', 'C/ BELGICA, 12', '46021', 'VALENCIA', 'VALENCIA', 'A08323404', '963480747', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1360, '1954', 'WENS', NULL, NULL, NULL, NULL, NULL, '667542843', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1363, '1955', 'MOTIVATION', 'C/ARIBAU, 112 - 6º - 1ª', '08036', 'BARCELONA', 'BARCELONA', 'A-07550593', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1366, '1956', 'INTERPRETES DE CONFERENCIAS, S.L.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1369, '1958', 'TOURIST-PARTNERS', NULL, NULL, 'Valencia', NULL, NULL, '963529353/663935636', '963940035', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1372, '1959', 'CHARMED MULTIMEDIA', 'C/ Pablo Sarasate, 12 local', '28047', 'Madrid', 'Madrid', NULL, '915265501', '915265510', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:35', '2026-02-18 13:03:35'),
(1373, '1960', 'ANA HOTELS RESORTS', 'Avda. Vicente Savall Pascual, 16', '03690', 'San Vicente del Raspeig', 'Alicante', NULL, '965268503 / 609129682', '965269130', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1374, '1961', 'EXCMO. AYUNTAMIENTO DE EL CAMPELLO', NULL, '03560', 'EL CAMPELLO', 'ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1377, '1962', 'GRASS ROOTS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1380, '1963', 'ASOCIACION DE PELUQUEROS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1382, '1964', 'CENTRO SUPERIOR DE IDIOMAS UNIVERSIDAD DE ALICANTE', 'CTRA. ALICANTE - SAN VICENTE S/N', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', 'A-53013355', '965903811 - 610488978 OLIMPIA', '965903794', NULL, NULL, 'UNIVERSIDAD DE ALICANTE - CENTRO SUPERIOR DE IDIOMAS', 'CTRA SAN VICENTE S/N - EDIFICIO GERMÁN BERNÁCER', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1385, '1965', 'BOSTON SCIENTIFIC IBERICA, S.A.', 'C/. RIBERA DEL LOIRA 46- ED 2 PTA BAJA', '28042', 'MADRID', 'MADRID', 'A-80401821', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1388, '1966', '* HOTEL MELIA VILLA AITANA', 'AVDA. ALCALDE EDUARDO ZAPLANA, Nº 7', '03502', 'BENIDORM', 'ALICANTE', 'A78304516', '966815000', '966870113', NULL, NULL, 'MELIA HOTELS INTERNATIONAL, S.A.', 'GREMIO DE TONELEROS 24 POLÍGONO SON CASTELLÓ', '07009', 'PALMA DE MALLORCA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1391, '1967', 'FUNDACIÓN FRANCISCO CORELL', 'C/ FERNÁNDEZ DE LA HOZ,78 ENTREPLANTA', '28003', 'MADRID', 'MADRID', NULL, '914514816', '913952823', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1394, '1968', 'GRUPO PROCESS', 'C/ CRONOS, Nº 20, EDIFICIO 4, 1, 6', '28037', 'MADRID', 'MADRID', 'B-80357064', '913771423', NULL, NULL, NULL, 'MEGAPROCESS, S.L.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1396, '1969', 'SPORT & BUSINESS EVENTS', NULL, NULL, 'Valencia', 'Alicante', NULL, '963923321/660550923', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1399, '1970', 'DRAGO EVENTOS, S.L.', 'C/RUFINO GONZALEZ, 8 1º A', '28037', 'MADRID', 'MADRID', 'B85853588', '913759006   TATIANA 639752154', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1402, '1971', 'CENTRO JUVENIL TUCUMAN 7', 'C/ Tucuman, 7 bajos', '03005', 'Alicante', 'Alicante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1405, '1972', 'COLEGIO OFICIAL DE INGENIEROS DE TELECOMUNICACIÓN C.V.', 'AVDA. JACINTO BENAVENTE, 12 1º B', '46005', 'VALENCIA', 'VALENCIA', NULL, '963509494', '963950382', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:36', '2026-02-18 13:03:36'),
(1408, '1973', 'FREDDY  ELIAS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1410, '1974', 'UNIACORDS, S.L.', 'C/ANTONIO JOSE CAVANILLES, Nº 9', '03203', 'ELCHE', 'ALICANTE', 'B53907424', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1413, '1975', 'GRUPO BLM', 'Pedro Lain Entralgo, 3 ch6', '28660', 'Boadilla del Monte', 'Madrid', 'B-86222536', '626305149 JOSE M - EVA 657205494', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1416, '1976', 'DOCTAFORUM', 'C/. MONASTERIOS DE SUSO Y YUSO 34, 4-14-2', '28049', 'MADRID', 'MADRID', NULL, '913720203', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1419, '1977', 'ANA JAMILA ALI OBON', 'COLONIA ROMANA, 2', '03016', 'ALICANTE', 'ALICANTE', 'X-46681044-H', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1421, '1978', 'PROYECTOS Y PERSONAS, EVENTOS S.L.', 'Plaza de Roma, 6 1º E', '50010', 'Zaragoza', 'Zaragoza', NULL, '976467898', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1424, '1979', 'ALTEA CLUB DE GOLF', 'Sierra Altea Golf, S/N', '03599', 'Altea', 'Alicante', 'G-03166949', '965848046', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1427, '1980', 'ALD AUTOMOTIVE, S.A.', 'Ctra. De la Coruña, Km 17,100', '28231', 'Las Rozas', 'Madrid', 'A-80292667', '917097162', '917097114', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1430, '1981', 'CDROM, S.A.', 'C/ Pintor Pedro Flores, 6', NULL, 'Murcia', 'Murcia', NULL, '968350036', '968266158', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1433, '1982', 'CARMELO ESCUDERO', NULL, NULL, NULL, NULL, NULL, '669741621', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1435, '1983', 'DUCATALIA, S.L.', 'AVDA. DE DENIA, Nº 13', '03002', 'ALICANTE', 'ALICANTE', 'B-53250924', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1438, '1984', 'ERNESTO ALVAREZ CABADAS', 'Humanes, 29 - 4º B', '28038', 'Madrid', 'Madrid', '50287979-C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1441, '1985', 'EL CORTE INGLES', 'Federico soto, 1-3', '03003', 'Alicante', 'Alicante', 'A28017895', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:37', '2026-02-18 13:03:37'),
(1444, '1986', 'Mercure Thalasia Costa de Murcia I', 'Avda. del Puerto, 327-329', '30740', 'San Pedro del Pinatar (Murcia)', 'murcia', NULL, '968182007', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1446, '1987', 'CENTRO SUPERIOR DE IDIOMAS, S.A.U. (UNIVERSIDAD DE ALICANTE)', 'CTRA. ALICANTE - SAN VICENTE S/N', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', 'A-53013355', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1449, '1988', 'HOTEL TORRE JOVEN', 'Ctra. Torrevieja-Cartagena, Km 4,7', '03185', 'Torrevieja', 'Alicante', NULL, '965707145', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1452, '1989', 'MATERIALES FRANS BONHOMME', 'Pare Rodes, 5 5ª', '08280', 'Sabadell', 'Barcelona', 'B64066996', '965932117', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1455, '1990', 'INSTITUTO UNIVERSITARIO DE MATERIALES DE ALICANTE - UNIVERSIDAD DE ALICANTE', 'CARRETERA ALICANTE - SAN VICENTE, S/N', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', 'Q0332001G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1458, '1991', 'RESTAURANTE EL SORELL', 'AVDA. DE DENIA, Nº 47', NULL, 'ALICANTE', 'ALICANTE', NULL, '965264426', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1460, '1992', 'COMUNICACIÓN PÚBLICA VALENCIANA, S.L.', 'C/ DIOGENES LÓPEZ MECHO, Nº 6 - 14', '46020', 'VALENCIA', 'VALENCIA', 'B96717400', '606987172', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1463, '1993', 'DRAC PIXEL MULTIMEDIA', 'Graus, 8 Bajos', '08017', 'Barcelona', 'Barcelona', NULL, '932800385', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1466, '1994', 'HOTEL SERENA GOLF', 'C/ Infanta Cristina, 44', '30710', 'Los alcazares', 'Murcia', NULL, '968583060', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1469, '1995', 'QUUM COMUNICACIÓN, S.A.', 'C/ Joaquin Costa, 14', '28002', 'Madrid', 'Madrid', 'A-78268794', '616102113/914426026', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1471, '1996', 'CARRANC SEGONTXAC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1473, '1997', 'CRUZ ROJA ESPAÑOLA EN ALICANTE', NULL, NULL, 'Alicante', 'Alicante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1475, '1998', 'SORD', NULL, NULL, NULL, NULL, NULL, '670750576', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:38', '2026-02-18 13:03:38'),
(1477, '1999', 'ISLA PUERTO ALICANTE, S.L.', 'Avda. Villajoyosa, 4', '03016', 'Alicante', 'Alicante', 'B-53768644', '607610260', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1480, '2000', 'DEVELOPING IDEAS', NULL, NULL, 'Alicante', 'Alicante', NULL, '622112402', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1483, '2001', 'KELME', 'C/ Miguel Servet, 10', '03203', 'Elche', 'Alicante', NULL, '966657900', '966657930', NULL, NULL, 'NEW MILLENNIUM SPORTS, S.L.', 'C/ Miguel Servet, 10', '03203', 'Elche', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1486, '2002', 'GREYGROUP', 'PASEO DE LA CASELLANA, 53', '28046', 'MADRID', 'MADRID', 'B-08667602', '914187391', NULL, NULL, NULL, 'GREY ESPAÑA, S.L.U.', 'C/ SANTALÓ, Nº 10', '08021', 'BARCELONA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1488, '2003', 'VERTICAL MARKETING', 'C/ PONZANO, Nº 39 - 41, 1º E', '28003', 'MADRID', 'MADRID', 'B-83110684', '913991515 - 667869264', NULL, NULL, NULL, 'VERTICAL PROMOTIONAL MARKETING, S.L.U.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1491, '2004', 'HOSTAL SAN JUAN', NULL, NULL, 'El Campello', 'Aliucante', NULL, '965652308', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1493, '2005', 'COLEO COMUNICACIÓN, S.L.', 'C/ Enriq Grados, 60 5º - 2ª', '08008', 'Barcelona', 'Barcelona', 'B62371836', '934522497', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1496, '2006', 'TALISMAN GROUP', 'Vía Augusta, 251 1º', '08017', 'Barcelona', NULL, NULL, '932090919', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1499, '2007', 'MAVERICK PRODUCCIONES', 'C/ Moratines, 3', '28005', 'Madrid', 'Madrid', NULL, '911437443', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1501, '2008', 'EVENTOS EGO, S.L.', NULL, NULL, 'Madrid', 'Madrid', NULL, '914362489', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1504, '2009', 'EXPEDIA PATNER SERVICES SARL', 'RUE DU LAC 12', '1207', 'GENEVA', 'SWITZERLAND', '060.324.293', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1507, '2010', 'CANDIDATURA JOSE LUIS TORRES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1510, '2011', 'SUSI MELLADO', NULL, NULL, NULL, NULL, NULL, '666789677', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1512, '2012', 'PRINCIPAL PROMOTIONS, Ltd', '2 WILMOT PLACE', NULL, 'LONDON', 'LONDON', '649 859 760', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:39', '2026-02-18 13:03:39'),
(1515, '2013', 'PASCUAL BORDES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1518, '2014', 'DANZA CABALLO, S.L.', 'Ctra. De Requena, Km 3', NULL, 'Albacete', 'Albacete', NULL, '609245346', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1521, '2015', 'PUBLIESCENA', NULL, NULL, NULL, NULL, NULL, '913605982', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1525, '2016', 'CANDIDATURA MANOLO JIMENEZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1526, '2017', 'HAYS TRAVEL LIMITED', '9-11 VINE PLACE', 'SR1 3NE', 'SUNDERLAND', NULL, '440 932 757', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1529, '2018', 'VM BROADCAST SERVICES GLOBAL, S.L.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1532, '2019', 'INMACULADA FLORES VALDES', 'Avda. Fabraquer, 3 bloque 2 esc. 4 2ª G', '03560', 'el Campello', 'Alicante', '21510568-W', '677418699', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1535, '2020', 'JOAN ESTRADA SPECIAL EVENTS & COMUNICACIÓN', 'C/. BAILEN 49, 1º1º', '08009', 'BARCELONA', 'BARCELONA', 'B-62055603', '932688614', '932688615', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1537, '2021', 'TEATRO PRINCIPAL DE ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1540, '2022', 'ASISA', 'C/. Juan Ignacio Luca de Tena, 10', '28027', 'Madrid', 'Madrid', 'A-08169294', '915957613', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1543, '2023', 'HOTEL BEDS', NULL, NULL, NULL, NULL, NULL, '609768157', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1546, '2024', 'BUENA ONDA', NULL, NULL, 'Madrid', 'Madrid', NULL, '654687660', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1549, '2025', 'PABLO HURTADO CARRASCO', 'C/ Cerda, 36 bis', '03009', 'Alicante', 'Alicante', '48343098L', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:40', '2026-02-18 13:03:40'),
(1551, '2026', 'AIB', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1554, '2027', 'UNITS ELEMENTS SL', 'C/ Roger, 65 Bajos', '08028', 'Barcelona', 'Barcelona', 'B25300526', '902190415', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1557, '2028', 'HOTEL ALICANTE MAYA', 'C/ Canonigo Manuel Penalva, 2', '03002', 'Alicante', 'Alicante', 'B-03702065', '965261211', NULL, NULL, NULL, 'MAYAPAN, S.L.', 'C/ Canonigo Manuel Penalva, 2', '03002', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1560, '2029', 'TURISOPEN', 'AGURAON, 29', '28023', 'MADRID', NULL, 'B83122481', '914460822 / 699320629 A.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1563, '2030', 'SEATRA', 'C/ Del arte, 21 - 1º', '28033', 'Madrid', 'Madrid', NULL, '915359617', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1565, '2031', 'C&EVENTS, S.L.', 'Pl. Alqueria de la Culla, 4 Edif. Albufera Center', '46910', 'Alfafar', 'Valencia', 'B-98261001', '960914545', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1568, '2032', 'JOEL PEROY FEBREO', NULL, NULL, NULL, NULL, '39869905-A', '676514151', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1572, '2033', 'COMERCIAL TABARCA', NULL, NULL, NULL, NULL, NULL, '608812761', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1574, '2034', 'TODOLUZ', NULL, NULL, 'ALICANTE', 'ALICANTE', NULL, '965200949', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1577, '2035', 'GRUPOIDEX CONSULTORÍA DE COMUNICACIÓN', 'C/. Antonio José Cavanilles, 9  (P. Empresarial)', '03203', 'Elche', 'Alicante', 'B53093837', '902929202', NULL, NULL, NULL, 'IDEX, IDEAS Y EXPANSION, S.L.', 'C/ Deportista Manuel Suarez, 11-4º C', '03006', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1579, '2036', 'CÁBALA CREATIVOS', 'C/ Jorge Juan, 13 - 3º', '03002', 'Alicante', 'Alicante', '21488656-D', '965146417//  670715649', NULL, NULL, NULL, 'MIGUEL MORENO MELERO', 'C/ Jorge Juan, 13 - 3º', '03002', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1582, '2037', 'TECNICAS REUNIDAS', 'Arapiles, 13', '28015', 'Madrid', 'Madrid', NULL, '915924884', '914480456', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1585, '2038', 'EUROMEDIA CREATIVOS, S.A.', 'A-VI KM. 399', '24549', 'CARRACEDELO', 'LEÓN', 'A-24360356', '609230032 / 987400610', '987402299', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:41', '2026-02-18 13:03:41'),
(1589, '2039', 'DREINULL AGENTUR FÜR MEDIATEINMENT', 'WALLSTRASSE 16', '10179', 'AUFGANG E, ERDGESCHOSS', 'BERLIN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1590, '2040', 'CARLSON WAGONLIT TRAVEL', 'PZA. AYUNTAMIENTO 8-2º PTA 4', '46002', 'VALENCIA', 'VALENCIA', 'B-81861304', NULL, NULL, NULL, NULL, 'CARLSON WAGONLIT ESPAÑA', 'C/ Princesa, 3 - 4ª Planta', '28008', 'Madrd', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1593, '2041', 'SAATCHI & SAATCHI, S.L.U.', 'GRAN VIA , 16-20 5ª PLANTA', '08902', 'l\'HOSPITALET DE LLOBREGAT', 'BARCELONA', 'B-28518603', '932419150', '932387810', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1596, '2042', 'BE&ME DISEÑO DE ESPACIOS,S.L.', 'C/ Cuidad de Sevilla, 59', '46988', 'Paterna', 'Valencia', 'B98425200', '961343923', NULL, NULL, NULL, 'BE&ME DISEÑO DE ESPACIOS,S.L.', 'c/ 612, nº 26', '46982', 'Paterna', 'Valencia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1599, '2043', 'ZENITH CORPORATE COMMUNICATIONS, LTD', '41 BALHAN HIGH ROAD', 'SW12 9 AN', 'LONDON', 'LONDON', '237967711', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1601, '2044', 'SALESIANOS DE CAMPELLO', NULL, NULL, NULL, NULL, 'G-03099843', NULL, NULL, NULL, NULL, 'Fundación de la C. V. Nuestra Señora de la Piedad', 'C/: Bernat metge, 8', '03560', 'Campello', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1604, '2045', 'FLORIDA UNIVERSITARIA', 'C/ Rei en Jaume I, 2 - 2º', '46470', 'Cataroja', 'Valencia', NULL, '961220391 ext. 175', '961269933', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1607, '2046', 'ELISA CUENCA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1610, '2047', 'EFERSON', 'c/. Artesanía, 23  planta 1, ofc. 1', '41927', 'Maírena del Aljarafe', NULL, NULL, '66599920', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1614, '2048', 'B-NEWS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1616, '2049', 'BRISTOL MYERS SQUIBB', 'C/ Qquintanavides, 15', '28050', 'Madrid', 'Madrid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1619, '2050', 'LA FINCA GOLF RESORT', 'Ctra. Algorfa-Los Montesinos, km 3', '03169', 'Algorfa-Torrevieja', NULL, NULL, '966729055', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1621, '2051', 'DAVID FONT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:42', '2026-02-18 13:03:42'),
(1624, '2052', 'VECTOR 001 S.L.', 'C. Angel Muñoz, 18 bjo.', '28043', 'MADRID', 'MADRID', NULL, '913005443', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1627, '2053', 'MADISON EVENTS EUROPE, S.L.', 'C/ Pins, 31', '08188', 'Vallromanes', 'Barcelona', 'B65844722', '698718852 / 935149449', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1629, '2054', 'GERSON CHEK', NULL, NULL, NULL, NULL, NULL, '649491381', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1632, '2055', 'INDEX  PRODUCCIONES', NULL, NULL, NULL, NULL, NULL, '637728245', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1635, '2056', 'JAKALA EVENTS', 'VIA S. GREGORIO, 34', '20124', 'MILANO', 'MILANO', NULL, '390236672202', '390236672306', 'www.jakalaevents.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1638, '2057', 'ESCOLA VALENCIANA', NULL, NULL, NULL, NULL, NULL, '963472783', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1642, '2058', 'DIRECCIÓN GENERAL DE LA FAMILIA Y MUJER', 'C/ Naquera, 9', NULL, 'Valencia', 'Valencia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1643, '2059', 'MILAR ALEJO', NULL, NULL, NULL, NULL, NULL, '609667303º', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1646, '2060', 'JAVIER AVILES PIQUERAS', NULL, NULL, NULL, NULL, NULL, '609229568', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1649, '2061', 'INFORVISUAL', 'Avd. del Cardenal Herrera Oria, 326D', '28035', 'Madrid', 'Madrid', NULL, '666402421', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1653, '2062', 'AUDIOVISUALES TV', 'C/ Musico Cabanilles, 22 - B', '46017', 'Valencia', 'Valencia', 'ES24313071R', '962062423 // 685988914', NULL, NULL, NULL, 'JOSE BLASCO PALAGUERRI', 'C/ Musico Cabanilles, 22 - B', '46107', 'VALENCIA', 'VALENCIA', NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1654, '2063', '*HOTEL NH AMISTAD', 'C/. Condestable, 1', '30009', 'Murcia', 'Murcia', 'B-58511882', '968282929', NULL, NULL, NULL, 'NH HOTELES ESPAÑA, S.L. 0128 HOTEL NH AMISTAD MURCIA', 'Travesera de les Corts, 144', '08028', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1657, '2064', 'EVENTUAL, S.L.', 'CALLE BRONCE, 8 - B', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', 'B54664743', '965671028 // 678574220', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1660, '2065', 'HOTEL HUSA ALICANTE GOLF', 'Avda. de  las Naciones, S/N', '03540', 'Alicante', 'Alicante', 'A 08130320', '965235000  //eventos 965235009', NULL, NULL, NULL, 'INSTALACIONES HOTELERAS S.A.', 'c/ Sabino de arana, 27', '08028', 'Barcelona', 'Barcelona', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:43', '2026-02-18 13:03:43'),
(1663, '2066', 'HOTEL AC ALICANTE', 'Avda. Elche, 3', '03008', 'Alicante', 'Alicante', 'B82751678', '965120178', NULL, NULL, NULL, 'INVERHOTEL 2000, S.L.', 'Paseo del Club Deportivo, 1 - Edificio 17', '28223', 'Pozuelo de Alarcón', 'Madrid', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1667, '2067', 'PABLO SANCHEZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1668, '2068', 'OVATION GLOBAL DMC', 'C/ Tuset 32 planta 5º y 6º', '08006', 'Barcelona', 'Barcelona', 'A64193865', '934459734 / 647386319', '934459721', NULL, NULL, 'MCI SPAIN EVENT SERVICES', 'Calle Tuset, 32 5ª Planta', '08006', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1671, '2069', 'i-TIC SOLUCIONES', NULL, NULL, 'MURCIA', 'MURCIA', NULL, '609229568', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1674, '2070', 'MIGUEL ANGEL PEREZ', NULL, NULL, NULL, NULL, NULL, '610913825', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1677, '2071', 'ARL PUBLICIDAD', NULL, NULL, NULL, 'Madrid', NULL, '913114894 / 629106688', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1680, '2072', 'ASOCIACIÓN PARKINSON ALICANTE', 'c/. Lira 5, local A', '03006', 'Alicante', 'Alicante', 'G 53615795', '966351951', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1682, '2073', 'GRUPO CALICHE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1685, '2074', 'HALCON VIAJES', 'C/. La Paz, 41', '46003', 'Valencia', 'Valencia', 'A10005510', '963358496', NULL, NULL, NULL, 'VIAJES HALCON, S.A.U.', 'C/ JOSÉ ROVER MOTTA,27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1688, '2075', 'LINEA BASE CONGRESOS Y ASOCIACIONES COOP. V.', 'C/ ALQUERIA DEL GORDO, Nº 6', '46113', 'MONCADA', 'VALENCIA', 'F98525785', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1691, '2076', 'OVERSUN', NULL, NULL, NULL, NULL, NULL, '607424286', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1693, '2077', 'B-NEWS', NULL, NULL, NULL, NULL, NULL, '663384216', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44');
INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(1696, '2078', 'CARPAS MEDITERRANEO', NULL, NULL, NULL, NULL, NULL, '965461064 // 666476463', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:44', '2026-02-18 13:03:44'),
(1699, '2079', 'JAVIER PITALU', NULL, NULL, NULL, NULL, NULL, '965652240 // 639634610', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1702, '2080', 'ALBERTO OLIVER AYÉN', NULL, NULL, NULL, NULL, '53231613 E', '675257061', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1704, '2081', 'BARRICARTE DEPORTE Y OCIO S.L.', NULL, NULL, NULL, NULL, NULL, '616663618', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1707, '2082', 'CLAU EVENTS', 'Avda. Reyes Catolicos, 60 oficina 413', '46910', 'Alfafar', 'Valencia', NULL, '963764364', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1710, '2083', 'DOBLE M AUDIOVISUALES', 'Parque Industrial P.I.B.O. Avda. de pilas, 11', '41110', 'Bollullos de la Mitacion', 'Sevilla', NULL, '955776968', '955692499', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1713, '2084', 'ACTION SERVICIOS AUDIOVISUALES, S.L.', 'C/ Miguel Hernandez, 7', '08908', 'L\'hospitalet de Llobregat', 'Barcelona', 'B64716327', '934080793', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1716, '2085', 'CERTAMEN CHICA 10', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1718, '2086', 'N.L. VIAJES', 'Camino de la Cruz, 1', '28023', 'Aravaca', 'Madrid', 'B-79827382', '913572326', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1721, '2087', 'PEDRO GIL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1724, '2088', 'HI END (SERVICIOS AUDIOVISUALES)', 'C/. Berlanga de Duero, 34', '28033', 'Madrid', 'Madrid', NULL, '672366511', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1727, '2089', 'GRUPO AZARBE', NULL, NULL, NULL, NULL, NULL, '636660857', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1729, '2090', 'WORLDSPAN EVENTS', 'North Wales Business Park', 'LL22 8LJ', 'North Wales', 'United Kingdom', '372 4554 46', '+441745828400', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1732, '2091', 'VOLVO CARS CORPORATION', 'BOX 887', 'SE-833 26', 'STRÖMSUND', 'SWEDEN', 'SE556074308901', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:45', '2026-02-18 13:03:45'),
(1735, '2092', 'JAVIER', NULL, NULL, NULL, NULL, NULL, '669028521', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1738, '2093', 'M.A.R.  SERVICIOS Y CONGRESOS', 'Rda. Narciso Monturiol y Estarriol, 7-9 Desp 5 y 9', '46980', 'Paterna', 'Valencia', NULL, '963216333', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1741, '2094', 'L\'OREAL ESPAÑA', 'C/ JOSEFA VALCARCEL, 48', '28027', 'MADRID', 'MADRID', 'A28050359', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1743, '2095', 'DRUG FARMA CONGRESOS, S.L.U.', 'Avda. de Cordoba, 21 - 3ª B', '28026', 'Madrid', 'Madrid', 'b84483064', '917921365', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1746, '2096', 'COMPARSA DE ALAGONESES', 'c/. AURORA, 15', '03630', 'SAX', 'ALICANTE', 'G-54434410', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1749, '2097', 'ENTIDAD URBANISTICA ALTEA HILLS', 'POLIGONO LA MALLA ALTEA HILLS', NULL, NULL, NULL, 'G-03897014', '965845781', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1752, '2098', 'THE ENTERTAINMENT FACTORY', 'EINSTEINWEG 33', NULL, NULL, NULL, NULL, '+31 306880496', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1755, '2099', 'WASABIEVENTS', NULL, NULL, NULL, NULL, NULL, '933063450  // 695666633', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1757, '2100', 'AMERICAN EXPRESS BARCELO VIAJES', 'c/. Albasanz 14, planta 2', '28037', 'Madrid', 'Madrid', 'B85376630', '91 385 86 30  // movil Daniel 660565376', NULL, NULL, NULL, 'GLOBAL BUSINESS TRAVEL SPAIN, S.L.', 'Calle Albasanz, 14', '28037', 'Madrid', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1760, '2101', 'TEAM ANDALUCES S.L.U', 'Urb. Novo Sancti Petri, C.C. N. Center, local A 10', '11139', 'Chiclana', 'Cadiz', 'B 11783479', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1763, '2102', 'GLOBAL DRAWING, S.L.', 'C/ López Aranda, 22', '28027', 'Madrid', 'Madrid', 'B-81788960', '917428420 // 636482381', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1766, '2103', 'EVEREST SCIENCES', '7737 East 42nd Place, Suite H', 'OK 74145', 'Tulsa', 'USA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1768, '2104', 'POPIN GROUP', 'C/. Columbia 64, 5º A', '28016', 'Madrid', 'Madrid', NULL, '917161144', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:46', '2026-02-18 13:03:46'),
(1770, '2105', 'IN OUT TRAVEL SPAIN', NULL, NULL, NULL, NULL, NULL, '911878393', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1773, '2106', 'PACIFIC WORLD', 'C/. General Urrutia, 75', '46013', 'Valencia', 'Valencia', NULL, '963528161', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1776, '2108', 'GROUP M Internation EMEA', 'Karperstraat, 8', '1075KZ', 'Amsterdam', 'Netherlands', '808697249B01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1777, '2109', 'Q-LINARIA CATERING', NULL, NULL, NULL, 'Alicante', NULL, '617536753', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1780, '2110', 'AOK EVENTS Ltd', 'The Coast House, 1 playfair Street', 'W6 PSA', 'LONDON', NULL, '756 6052 18', '+44 0 2082228420', NULL, NULL, NULL, 'AOK EVENTS Ltd', 'THE ENGINE ROOMS 150A FALCON ROAD', 'SW4 9EF', 'LONDON', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1783, '2111', 'SECOIR', 'C/ Donoso, 73- 1º', '28015', 'Madrid', 'Madrid', NULL, '915448035', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1785, '2112', 'COINFER S.COOP.', 'Ctra. Ademuz, Km. 11', '46980', 'Paterna', 'Valencia', 'F46036851', '961320244', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1788, '2113', 'TALENMO', 'C/. Guillem de Castro, 13 pta.', '46007', 'Valencia', NULL, NULL, '960725026', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1790, '2114', 'BBVA', NULL, NULL, NULL, NULL, NULL, '659951260', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1793, '2115', 'Viajes Salamanca, S.L.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1796, '2116', 'ENRIQUE DEL REY PASTRANA', 'PASEO DE LAS AZUCENAS, 1', '03690', 'San Vicente del Raspeig', 'Alicante', '48577759B', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1799, '2117', 'AVI', 'Avda. Alcantarilla, 64 - 2º', '30166', 'Nonduermas', 'Murcia', '22987454B', '868083559', NULL, NULL, NULL, 'JOSE MARIA BOTI PEREZ', 'Avda. Alcantarilla, 64 - 2º', '30166', 'Nonduermas', 'Murcia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1801, '2118', 'BODEGAS BOCOPA', 'Paraje les Pedreres, Autovia A31 Km200', '03610', 'Petrer', 'Alicante', 'F03290871', '673993899', NULL, NULL, NULL, 'BODEGAS COOPERATIVAS DE ALICANTE COOP. V', 'Paraje les Pedreres, Autovia A31 Km200', '03610', 'Petrer', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1804, '2119', 'JUAN PEDRO MARIN SANTAMARIA', NULL, NULL, 'Alicante', 'Alicante', NULL, '633275636', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:47', '2026-02-18 13:03:47'),
(1807, '2120', 'RED HAT, S.L.', 'C/ Jose Bardasano Baos 9, Edif. Gorbea 3, Planta 3', '28016', 'Madrid', 'Madrid', 'B82657941', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1810, '2121', 'SIGMA-ALDRICH INTERNATIONAL GmbH', 'Wassergasse, 7 9000 St. Gallen', NULL, 'Switzerland', 'Switzerland', 'CHE-116.309.372', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1812, '2123', 'TREBOL PRODUCCIONES', NULL, NULL, NULL, NULL, NULL, '965216141', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1815, '2124', 'VIAJES PMI EVENTS', 'Magalhaes, 5A', '07014', 'Palma de Mallorca', 'Palma de Mallorca', NULL, '971701488', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1818, '2125', 'ESCUELA DE DANZA STUDIO 30', 'Avda. Historiador Vicente Ramos, 30', NULL, 'Alicante', NULL, NULL, '677464020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1821, '2126', 'TATIANA CARCELES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1824, '2127', 'RIBASOUND', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1826, '2128', 'WEZINK IDEAS Y COMUNICACIÓN, S.C.', 'C/ Coles, 1 3º', '03501', 'Benidorm', 'Alicante', 'J54628805', '633700705', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1829, '2129', 'SVENSKA CELLULOSA AKTIEBOLAGET SCA', 'BOX 200 , SE-1013', NULL, 'STOCKHOLM', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1832, '2130', 'FUNDACIÓN ESPAÑOLA DE REUMATOLOGIA', 'C/ Marques del Duero, 5 1ª Planta', '28001', 'Madrid', 'Madrid', 'G82449323', '915767799', '915781133', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1835, '2131', 'MULTISTREAM, S.L.', 'C/ COLÓN, 8 OFICINA 3', '46004', 'VALENCIA', 'VALENCIA', 'B97875629', '961822020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1837, '2132', 'BLINKER ESPAÑA, S.A.U.', 'POL. INDUSTRIAL LAS ATALAYAS, PAR. 11,12 Y 13', '03114', 'ALICANTE', 'ALICANTE', NULL, '966102500', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1840, '2133', 'AUTOCARES MARTINEZ', 'C/ Esperanto, 23 Edif. San Francisco, fase III', '03503', 'Benidorm', 'Alicante', 'B03486487', '965857780', NULL, NULL, NULL, 'COSTABUS, S.L.', 'C/ Esperanto, 23 Edif. San Francisco, fase III', '03503', 'Benidorm', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:48', '2026-02-18 13:03:48'),
(1843, '2134', 'CENTRO CIRUJIA COSMETICA BEJUCAL, S.L.', 'Avda. Historiador Vicente Ramos, 28 local 23', '03540', 'Playa San Juan', 'Alicante', 'B53891560', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1846, '2135', 'FATIMA MARTINEZ PITTO', 'C/ ARPON, 17', '03540', 'PLAYA SAN JUAN', 'ALICANTE', '21482033X', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1849, '2136', 'ABBOT MEDICAL OPTICS SPAIN, S.L.U.', 'Ctra. Fuencarral-<alcobendas, Km 15,400', '28100', 'Alcobendas', 'Madrid', 'B83255372', '618387256', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1851, '2137', 'BAUSCH & LOMB, SA', 'Avda. Valdelaparra, 4', '28108', 'Alcobendas', 'Madrid', 'A60567922', 'Gonzalo Andrio - 629183672', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1854, '2138', 'ENTERPRISE ATESA', 'Avd. del Ensanche de Vallecas, 37 - 3º Planta', '28051', 'Madrid', 'Madrid', 'A28047884', NULL, NULL, NULL, NULL, 'AUTOTRANSPORTE TURISTICO ESPAÑOL, S.A. (ENTERPRISE ATESA)', 'Avd. del Ensanche de Vallecas, 37 - 3º Planta', '28051', 'Madrid', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1857, '2139', 'SAMARCANDE', '175 RUE BLOMET', '75015', 'PARIS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1861, '2140', 'PUEBLO ACANTILADO SUITES', 'Plaza de la Ciutat de L\'Alguer, 10', '03560', 'EL Campello', 'Alicante', 'B-73083446', '965638146 / 628982310', NULL, NULL, NULL, 'VIS HOTELES SL', 'AVDA. ANTNIO FUERTES, 1', '30840', 'ALHAMA DE MURCIA', 'MURCIA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1862, '2141', 'CONSOFT', NULL, NULL, NULL, NULL, NULL, '966426030', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1865, '2142', 'GOLDCAR RENTAL', 'Ctra. Valencia N332 kM', '03550', 'San Juan', 'Alicante', 'B03403169', '965652482', NULL, NULL, NULL, 'GOLDCAR SPAIN SLU', 'Ctra. Valencia N332 Km 115', '03550', 'San Juan', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1868, '2143', 'SONOIDEA', 'C/ Font Roja, 6 bajo', '46007', 'Valencia', 'Valencia', NULL, '9633738245', '963743973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1871, '2144', 'TU BODA ES UNICA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1874, '2145', 'GRUPO PACIFICO', 'C/ Maria Cubi, 4 Pral.', '08006', 'Barcelona', 'Barcelona', 'A08644932', '932388777 ext. 222', NULL, NULL, NULL, 'VIAJES PACIFICO, S.A.', 'C/ Maria Cubi, 4 Pral.', '08006', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1876, '2146', 'EVENTS BY TLC', NULL, NULL, NULL, '914320073', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:49', '2026-02-18 13:03:49'),
(1879, '2147', 'FERNANDO FORNOS MORON', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1882, '2148', 'JOSE ANTONIO BUENO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1885, '2149', 'BBO SUBTITULADO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1888, '2150', 'ESATUR SERVICIOS', 'C/ Garcia Andreu, 10', '03007', '03007', 'Alicante', 'B53874145', '966 377 034 - 606376590', '966 377 035', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1890, '2151', 'Gfk  Emer Ad Hoc Research, S.L.', 'Plaza Tetuan, 1 2ª', '46003', 'Valencia', 'Valencia', 'B46175931', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1893, '2152', 'FABULA CONGRESOS, S.L.', 'C/. LAS NAVES, 13', '28005', 'MARDID', 'MADRID', NULL, '914735042', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1896, '2153', 'CONGRESO NACIONAL DE PODOLOGIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1899, '2154', 'ASOCIACIÓN VALENCIANA DE INGENIEROS EN TELECOMUNICACION', 'Avda. Jacinto Benavente, 12 1º D', '46005', 'Valencia', NULL, 'G96924931', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1901, '2155', 'GRUPO ARÁN DE COMUNICACIÓN', 'C/. CASTELLÓ, 128 1ª PLANA', '28006', 'MADRID', NULL, NULL, '917820033', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1904, '2156', 'VIAJES ANDRÓMEDA, S.A.', 'PZA. CASTILLA 3 PLT 19, E1', '28046', 'MADIRD', NULL, NULL, '917695975', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1907, '2157', 'LABORATORIO GADETEC', 'C/. ALCALÁ GALIANO 75, BAJO', '03012', 'ALICANTE', 'ALICANTE', NULL, '965855685        689545347', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1910, '2158', 'LE PUBLIC SYSTEME', '40, RUE ANATOLE FRANCE', '92594', 'LEVALLOIS-PERRET CEDEX', 'FRANCE', 'FR26 602 063 323', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1914, '2159', 'HOTEL DENIA LA SELLA', 'Alquería de Ferrando, s/n', '03749', 'Jesús Pobre', 'Alicante', 'A53388187', '966454054', NULL, NULL, NULL, 'RESORT LA SELLA, S.A. HOTEL DENIA LA SELLA', 'Alquería de Ferrando, s/n Plan parcial La Sella', '03749', 'Jesus Pobre', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:50', '2026-02-18 13:03:50'),
(1915, '2160', 'COMPLEJO DR. PEREZ MATEOS', 'c/. D. Perez Mateos, 2', '03550', 'SAN JUAN', 'ALICANTE', 'A82520321', '965653300  ENRIQUE 626857666 PATRICIA 650041720', NULL, NULL, NULL, 'DOCTOR PEREZ MATEOS, S.A.', 'C/ Doctor Perez Mateos, 2', '03550', 'San Juan Pueblo', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1918, '2161', 'AUDIOVISUALES REAL', NULL, NULL, NULL, NULL, NULL, '926242558', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1921, '2162', 'HOGUERA DIPUTACIÓN RENFE', NULL, NULL, NULL, NULL, NULL, '628131786', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1924, '2163', 'ALBERTO', NULL, NULL, NULL, NULL, NULL, '635974448', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1926, '2164', 'DKV SEGUROS', 'PASEO DE GRACIA, 55-57 6ª PLANTA', '08007', 'BARCELONA', 'BARCELONA', NULL, '932140058', '932140034', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1929, '2165', '* RITMOVIL', NULL, NULL, NULL, NULL, '21512869-A', '629304822', NULL, NULL, NULL, 'JOSE MARIA MARTINEZ MARTINEZ', 'C/ Cervantes, 17', '03550', 'Sant Joan d\'Alacant', NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1932, '2166', 'E & TB GROUP', 'Rambla de Catalunya, 5 Pral. 3ª', '08007', 'Barcelona', 'Barcelona', 'B62424080', '670990662', NULL, NULL, NULL, 'EVENTS AND TRAVEL BARCELONA, S.L.', 'Rambla de Catalunya, 5 Pral. 3ª', '08007', 'Barcelona', 'Barcelona', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1935, '2167', 'ELENCO AUDIOVISION', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1938, '2168', 'LA LECHE COMUNICACIÓN', 'C/. Penaguila, 3', '03201', 'Elche', 'Alicante', 'B03621117', '655839655', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1940, '2169', 'PEPE JEANS', 'Calle Isaac Newton, 8', '03203', 'Elche', 'Alicante', NULL, '965685117', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1943, '2170', 'HERGA HOSTELERIA, S.L.', 'C/ Rafael Altamira, 14', '03002', 'Alicante', 'Alicante', 'B-53661237', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1946, '2171', 'HALCON EVENTOS', 'c/. Enriqueta Granados, 6 edi. A', '28224', 'Pozuelo de Alarcón', 'Madrid', 'A-10005510', '915125064', NULL, NULL, NULL, 'VIAJES HALCON,S.A.U.', 'C/ JOSÉ ROVER MOTTA,27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1949, '2172', 'PCI PYME MARKETING, S.L.', 'c/. Pastora Imperio, 5-14º d', '28036', 'Madrid', 'Madrid', 'ESB81981631', '915358192  CARLOS 670788402', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:51', '2026-02-18 13:03:51'),
(1951, '2173', 'CUTTINGEDGE-EVENTS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1954, '2174', 'C.P. ALTEA DORADA', NULL, NULL, NULL, NULL, NULL, '965844022', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1957, '2175', 'JOSANZ FOTOGRAFIA, S.A.', 'C/ Independencia, 8', '24001', 'LEON', NULL, 'A24015307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1960, '2176', 'AV EXPRESS', NULL, NULL, NULL, NULL, NULL, '628528298', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1963, '2177', 'BATASONI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1965, '2178', 'PUNTUAL', NULL, NULL, NULL, NULL, NULL, '965211125', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1968, '2179', 'TA DMC', 'c/. Travesera de  Gracia, 85,3º 1ª', '08006', 'Barcelona', NULL, NULL, '933680138', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1971, '2180', 'FYRST TRADUCCIONES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1974, '2181', 'PANESCO FOOD', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1977, '2182', 'DAVID CRICK', NULL, NULL, NULL, NULL, NULL, '00447899702925', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1979, '2183', 'INTEGRACIÓN AGENCIAS DE VIAJES, S.A.', 'C/ Doctor Esquerdo 136,7ª Planta', '28007', 'Madrid', 'Madrid', 'A84523505', '915416665', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1982, '2184', 'SRT ROUND THE WORLD LIMITED', 'Commerce House, 1 Bowring Road', 'IM8 2LQ', 'Ramsey , Isle of Man', 'United Kingdom', 'GB003681602', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1985, '2185', 'VERTICE SUR EVENT&TRAVEL', 'C/. PLATERIA, 29-1º c', NULL, 'MURCIA', NULL, NULL, '968225476', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1988, '2186', 'VIDEO ESTUDIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:52', '2026-02-18 13:03:52'),
(1990, '2187', 'SONO TECNOLOGIA AUDIOVISUAL, S.L.', 'C/ Sepulveda, 6 Nave 26 Pol. Ind. De Alcobendas', '28108', 'Alcobendas', 'Madrid', 'b61906103', '916624217', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(1993, '2188', 'SINCROVISUAL', NULL, NULL, NULL, NULL, NULL, '678767989', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(1996, '2189', 'AMPA LA MILAGROSA', 'C/ JUAN XXIII, 2', '03698', 'AGOST', 'ALICANTE', '1G03733573', '965691143', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(1999, '2190', 'RMR MULTIMEDIA AUDIOVISUAL, S.L.', 'Avda. Isabel de Valois, 4 4º A', '28050', 'Madrid', 'Madrid', 'B83839191', '912335400 / 695163634', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2001, '2191', 'DISSIMILITY', 'Avda. de Brasil, 29', '28020', 'Madrid', 'Madrid', NULL, '914252577', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2004, '2192', 'IBERMUTUAMUR', NULL, NULL, 'Alicante', 'Alicante', NULL, '965145748', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2007, '2193', 'ESATUR', 'C/ Garcia Andreu, 10', '03007', 'Alicante', 'Alicante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2010, '2194', 'MARISA GAYO', NULL, NULL, NULL, NULL, NULL, '609641855', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2013, '2195', 'FORMACIÓN GINER Y MIRA S.L.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2015, '2196', 'TRADUCTORES ESPAÑOLES', 'C/ Musgo, 3 Planta Baja Oficina 4', '28023', 'Madrid', 'Madrid', 'B849533997', '911861170 - 674073542', NULL, NULL, NULL, 'SUCCESFUL SPANISH TRANSLATORS, S.L.', 'C/ Musgo, 3 Planta Baja Oficina 4', '28023', 'Madrid', 'Madrid', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2018, '2197', 'LABORATORIOS DOCTOR ESTEVE, S.A.', 'AVDA. MARE DE  DEU MONTSERRAT, 221', NULL, NULL, NULL, 'A-08037236', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2021, '2198', 'PROTOCOL DMC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2024, '2200', 'ASOC. CULTURAL DE AUDIOVISUALES DE ESPAÑA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:53', '2026-02-18 13:03:53'),
(2027, '2201', 'LISANDRO ESTRADA MENDES', 'C/ JAVEA 7, ESC 1,4º D', '03690', 'SAN VICENTE DEL RASPEIG', 'ALICANTE', '48797012M', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2029, '2202', 'ARCA PRODUCCIONES', NULL, NULL, NULL, NULL, NULL, '868050576 / 682543275', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2032, '2203', 'AUDITORIO Y CENTRO DE CONGRESOS DE LA REGION DE MURCIA', NULL, NULL, NULL, NULL, NULL, '968341060', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2035, '2204', '*LIME DMC', 'C/ Recogidas, 15 - 4º', '18005', 'Granada', NULL, 'B18961243', '958773865', NULL, NULL, NULL, 'THE LIME GROUP IN SPAIN, S.L.U.', 'Acera del Darro, 10 3º', '18005', 'GRANADA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2038, '2205', 'POLARIS SALES SPAIN, S.L.U.', 'JOSEP Mª SERT, 17 2º', '08530', 'LA GARRIGA', 'BARCELONA', 'B85473296', '902160606 EXT. 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2041, '2206', 'BCD TRAVEL', 'C/ Profesor Beltran Baguena, 4,204', '46009', 'Valencia', 'VALENCIA', 'B07012107', NULL, NULL, NULL, NULL, 'BARCELO VIAJES, S.L.', NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2043, '2207', 'VIDEOMEDICA', NULL, NULL, NULL, NULL, NULL, '670340739', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2046, '2208', 'PRO-EXPO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2049, '2209', 'CUTTING EDGE EVENTS', NULL, NULL, NULL, NULL, NULL, '931514535 - 601098468', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2052, '2210', 'TECNICA VIAJES, S.L.', 'Calle Granados, 6 2º A', '29008', 'Malaga', 'Malaga', 'B29657053', '952214439', '952602552', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2054, '2211', 'BRAND_COMUNICACIÓN', 'C/. Truset, 21 -1er 4a', '08006', 'Barcelona', 'Barcelona', 'B63149769', '932700909', NULL, NULL, NULL, 'BRAND SOLUCUIONES AVANZADAS MARKETING', 'Truset, 21 1er 4a', '08006', 'Barcelona', 'BARCELONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2057, '2212', 'DOS TINTAS S.C.', 'C/. EUGENIO D\'ORS 5, ENTLO', '03203', 'ELCHE', 'ALICANTE', 'J531956028', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2060, '2213', 'OPC CONGRESS', 'C/ Escultor Octavio Vicent, 3 Esc. 2 Pta. 3', '46023', 'Valencia', 'Valencia', NULL, '963286500', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:54', '2026-02-18 13:03:54'),
(2063, '2214', 'TRONICSA, S.A.', NULL, NULL, NULL, NULL, NULL, '607246035', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2066, '2215', 'SMARTWORKS, S. L.', 'C/ DECANO ANTONIO ZEDANO 3 OFICINA 16', '29620', 'TORREMOLINOS', 'MALAGA', 'B-92914662', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2069, '2216', 'ALMA CONEXIÓN GRAFICA, S.L.', 'Urb. Sierramar, 169', '46220', 'Pcassent', 'Valencia', NULL, '868970287', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2071, '2217', 'MAXXIUM ESPAÑA', 'C/ Mahonia, 2 Edif. Portico, Planta 2', '28043', 'Madrid', 'Madrid', NULL, '913534705/ 620608096', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2074, '2218', 'UNIVERSIDAD CARDENAL HERRERA - CEU', 'C/ CARMELITAS, 1', '03203', 'ELCHE', 'ALICANTE', 'G-28423275', NULL, NULL, NULL, NULL, 'FUNDACIÓN UNIVERSITARIA SAN PABLO CEU', 'C/ SEMINARIO, S/N', '46113', 'MONCADA', 'VALENCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2077, '2219', 'CARO & ZAMÁCOLA', NULL, NULL, NULL, NULL, NULL, '629272747', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2080, '2220', 'GRUPO IDEA', 'Pol. Ind. Oeste, Avda. Pricipal Parc. 30/1', '30169', 'San Gines', 'MURCIA', NULL, '968886764 / 631 634 682', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2082, '2221', 'P&I', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2085, '2222', 'THE ORGANISED LIFE LTD', '19 St. Johns Avenue', 'WA160DH', 'Knutsford', 'Cheshire', '824 0521 64', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2088, '2223', 'NOVARTIS PHARMACEITICALS UK LIMITEDh', '200 Frimley Business Park', 'GU 16  7SR', 'Frimley/Camberley', 'UNITED KINGDOM', NULL, '+441276698008', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2091, '2224', 'TIBA INTERNACIONAL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2093, '2225', 'IBERIA VILLAGE', 'Ctra. Benidorm a Finestrat, Pda del Moralet, S/N', '03502', 'Benidorm', 'ALICANTE', 'B54774088', '965004300 / 647339718', NULL, NULL, NULL, 'HOTEL MITICA, S.L.', 'Ctra. Benidorm a Finestrat, Pda del Moralet, S/N', '03502', 'Benidorm', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2096, '2226', 'IP ATELIER', 'CALLE GERONA 17, 1º A-B', '03001', 'ALICANTE', 'ALICANTE', NULL, '965145809', NULL, 'www.padima.es', 'info@padima.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:55', '2026-02-18 13:03:55'),
(2099, '2227', 'GRANDSLAM PRODUCCIONS, S.L.', 'Passatge Toledo, 11 Local', '08014', 'Barcelona', 'Barcelona', NULL, '932965084/ 609770191 Sam', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2102, '2228', '*HOTEL CAP NEGRET', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2105, '2229', 'PABLO SOLER', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2107, '2230', 'POLARIS SALES EUROPE SARL', 'ROUTE DE L ETRAZ - BUSINESS CENTER BULDING A5', 'CH 1180', 'ROLLE', 'SWITZERLAND', 'CHE115652695', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2110, '2231', 'VERBALIA TRADUCCIONES', NULL, NULL, NULL, NULL, NULL, '637457471 - 693471953', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2113, '2232', 'AUDIOVISUALES AG', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2116, '2233', 'ABSTRACT EVENTS', 'ROSELLO, 516-518', '08026', 'BARCELONA', 'BARCELONA', NULL, '934592809', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2119, '2234', 'PALMLUNDS', NULL, NULL, NULL, NULL, NULL, '669162044', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2121, '2235', 'DORIER GROUP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2124, '2236', 'THEORIA CONGRESOS', NULL, NULL, NULL, NULL, NULL, '607330900', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2127, '2237', 'CL COMUNICACIÓN Y BRANDING', 'C/ Nicolas Copernico, 8-2', '46950', 'Valencia', 'Valencia', NULL, '618279727', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2130, '2238', 'ASICS SEUROPE, B.V.', 'Taurusavenue, 125', '2132 LS', 'Hoofdurp', 'The Netherlands', 'NL8033.24.273.B.01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2133, '2239', 'SARAROSSO INCENTIVE', 'Via Cantonale, 3', '6900', 'LUGANO', 'SWITZERLAND', 'CHE 109.559.490', '+41919113330', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2135, '2240', 'CENTRO DIOCESANO NUESTRA SEÑORA DEL CARMEN DE CASALARGA', 'PLZA GARCIA LORCA S/N', '03015', NULL, 'ALICANTE', 'R 0300102 A', '673224353 - 965183537', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:56', '2026-02-18 13:03:56'),
(2138, '2241', 'BRANDS AT WORK LTD', '83 Victoria Street', 'SW1H 0HW', 'LONDRES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2141, '2242', 'PURI (COORDINADORES)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2144, '2243', 'DOBLE HÉLICE', NULL, NULL, NULL, NULL, NULL, '658935572', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2146, '2244', 'HOSS INTROPIA', 'C/ Pantoja, 14', '28002', 'Madrid', 'Madrid', 'A80915804', '626263154', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2149, '2245', 'CARLSON WAGONTLIT TRAVEL MEETINGS & EVENTS', 'Trespaderne, 29', '28042', 'Madrid', 'Madrid', NULL, '917249950 / 610562862', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2152, '2246', 'ZYCKO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2155, '2247', 'MARCOS ESCORZA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2158, '2248', 'ABANTE ASESORES DISTRIBUCIÓN', 'C/. Padilla, 32', '28006', 'Madrid', 'Madrid', NULL, '917815750', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2160, '2249', 'MK MEDIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2163, '2250', 'AESLA 2016', NULL, NULL, NULL, NULL, NULL, '634571967', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2166, '2251', 'CATALINA ILIESCU', NULL, NULL, 'ALICANTE', 'ALICANTE', NULL, '965909827', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2169, '2252', 'RIGHT ON TARGET, S.L.   (R&D MEETING PLANNERS)', 'C/. LAS AMAPOLAS, 222', '29660', 'MARBELLA', NULL, 'B92710847', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2172, '2253', 'ZYCKO LIMITED', 'INDA HOUSE, The Mallards, Brodway Lane', 'GL7 5TQ', 'South Cerney', 'CIRENCESTER', 'GB158256979', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:57', '2026-02-18 13:03:57'),
(2174, '2254', 'NOVARTIS PHARMACEUTICALS UK Ltd', 'C/O GBT Travel Services UK Limited d/b/a', NULL, 'American Express Global Business Travel', '10-16 Market Square South Woodham Frrers', 'CM3 5XA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2177, '2255', 'COMANDO CUATTRO', 'C/ O´DONNELL, 4 PLANTA 1 OFICINA 7 TORRE DE VALENC', '28009', NULL, 'MADRID', NULL, '639757820', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2180, '2256', 'ANA SABATER', NULL, NULL, NULL, NULL, NULL, '639579178', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2183, '2257', 'RAFA ALAVES', NULL, NULL, NULL, NULL, NULL, '965175232', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2185, '2258', 'KERNPHARMA', NULL, NULL, NULL, NULL, NULL, '659972472', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2188, '2259', 'AENA', 'ARTURO SORIA, 109', '28043', 'MADRID', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2191, '2260', 'FRANCISCO JUAN CHELIN', NULL, NULL, NULL, NULL, NULL, '660325902', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2194, '2262', 'CAR AUDIO EXTREME', NULL, NULL, NULL, NULL, 'B54627971', '607463656 - 965207721', NULL, NULL, NULL, 'AUDIO EXTREM APLICACIONES, S.L.', 'c/ Vazquez de mella, 15', '03013', 'Alicante', 'Alicante', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2197, '2663', 'HOTEL BALI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2199, '2664', 'OMICS INTERNATIONAL', '2360 COORPORATE CIRCLE, SUITE 400', NULL, 'HENDERSON', 'NV 89074, USA', NULL, '+1-650-618-1417', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2202, '2665', 'VIAJES TRANSVIA', 'PJE. VENTURA FELIU, 16', '46007', 'VALENCIA', 'VALENCIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2205, '2666', 'VIAJES VILLARREAL', 'Avda. Garcia Lorca, s/n Edif. Club de Hielo', '29630', 'Benalmádena', 'Málaga', 'B92993443', '952445586', NULL, NULL, NULL, 'LOPEZ GARRIDO VIAJES Y CONGRESOS, S.L.', 'Avda. Garcia Lorca, s/n Edif. Club de Hielo', '29630', 'Benalmadena', 'Malaga', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2208, '2667', 'KRISTIYAN DJ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:58', '2026-02-18 13:03:58'),
(2211, '2668', 'JAIME GASPAR LLINARES', NULL, NULL, NULL, NULL, NULL, '626729576', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2213, '2669', 'COLEGIO PROFESIONAL DE TÉCNICOS SUPERIORES SANITARIOS COMUNIDAD VALENCIANA', 'C/ PERIODISTA RODOLFO SALAZAR, 20 ENTRESUELO OF 2', '03012', 'ALICANTE', 'ALICANTE', 'V 54352539', '601330705', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2216, '2670', 'POWER AV', 'GRABADORES 2, M. 10-3-6 POL. IND. PUERTA DE MADRID', '28830', 'SAN FERNANDO DE HENARES', 'MADRID', 'B-81497026', '660484152', NULL, NULL, NULL, 'POWER AUDIOVISUAL RENTAL COMPANY', 'GRABADORES 2, M. 10-3-6 POL. IND. PUERTA DE MADRID', '28830', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2219, '2671', 'REDFLEXIÓN CONSULTORES', 'C/ Isaac Albeniz, 9 - 4ª Esc. 1º B', '30009', 'Murcia', 'Murcia', 'B-73182925', '968223867 / 627897860', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2222, '2672', 'CONSORFRUT, S.L.', 'C/ Eslida, 7 Entresuelo', '46026', 'Valencia', 'Valencia', 'B96990015', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2224, '2673', 'CONCEJALIA DE DEPORTES ALICANTE', NULL, NULL, NULL, NULL, NULL, '965147052', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2227, '2674', 'EUROANTENA2000', NULL, NULL, NULL, NULL, NULL, '607606858', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2230, '2675', 'JAIME CRESPI LEGUA', 'PASEIG DEL PONT, 7', '08916', 'BADALONA', 'BADALONA', '37323705 -H', '629210595', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2233, '2676', 'NUESTRA ESCUELA, S.L.', 'C/ JAVEA, 15', '03009', 'ALICANTE', 'ALICANTE', 'B53167441', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2235, '2677', 'RESTAURANTE ROS, S.L.', 'C/ DEPORTISTA JUAN MATOS, PTL. 4 PISO 8 IZQ', '03016', NULL, 'ALICANTE', 'B53688040', '610208878 LOLI', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2238, '2678', 'ALSON ESPECTACULOS, S.L.', 'C/ LOS CINCUENTA, 14 BAJO', '03008', 'ALICANTE', 'ALICANTE', 'B-03519584', '626148843', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2241, '2679', 'SPAINTACULAR', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2244, '2680', 'AIM GROUP INTERNATIONAL', 'C/ Arturo Soria, 55 Local 1', '28027', 'Madrid', 'Madrid', NULL, '912873400', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:03:59', '2026-02-18 13:03:59'),
(2248, '2681', 'MacGuffin,  S.L.', 'C/ Santa Engracia, 42 3', '28010', 'Madrid', 'Madrid', 'B80089295', '673313512', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2249, '2682', 'PRISA RADIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2252, '2683', 'IX CONGRESO NACIONAL DE ESTUDIANTES DE PODOLOGIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2255, '2684', 'D-SIDEGROUP', '24 Avenue Léonard Mommaerts', '1140', 'Brussels', 'Belgium', 'BE0472232523', '+32027300611', NULL, NULL, NULL, 'D-SIDE SA', '24 Avenue Léonard Mommaerts', '1140', 'Brussels', 'Belgium', NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2258, '2685', 'SERGIO GISBERT FOTOGRAFIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2261, '2686', 'APPLE TREE COMMUNICATIONS, S.L.', 'Avinguda Marques de l\'Argentera, 17', '08003', 'Barcelona', 'Barcelona', 'B64497902', '933184669', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2263, '2687', 'KORSARY', NULL, NULL, NULL, NULL, NULL, '609031019', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00');
INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(2266, '2688', 'BUSINESS SERVICE CLUB', 'BOX 5148', '121 17', 'JOHANNESHOV', 'SWEDEN', 'SE559012004301', NULL, NULL, NULL, NULL, 'BSC TRAVEL AB', 'BOX 5148', '121 17', 'JOHANNESHOV', 'SWEDEN', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2269, '2689', 'ABBVIE', 'AVDA. BURGOS, 91', '28050', 'MADRID', 'MADRID', 'B-86418787', '699343094', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2272, '2690', 'NEWISCOM', 'Ntra. Señora del Carmen, 11', '28250', 'Torrelodones', 'Madrid', NULL, '918595992 / 657022692', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2274, '2691', 'COSMOVISUAL EVENTOS Y COMUNICACIÓN, S.L.', 'C/ Doctor Fleming, 35 2F', '28036', 'Madrid', 'Madrid', 'B86974086', '914126442', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2277, '2692', 'SEAUTON INTERNATIONAL CONGRESSES & INCENTIVES', 'Vaartdijk, 3-002', '3018', 'Wijgmaal', 'Belgium', 'BE 0464 882 990', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2280, '2693', 'UNICEF', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'UNITED NATIONS CHILDREN´S FUND, SPECIALIST FUNDRAISING SERVICES, PRIVATE SECTOR FUNDRAISING', 'PALAIS DES NATIONS', 'CH-1211', 'GENEVA 10', 'SWITZERLAND', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:00', '2026-02-18 13:04:00'),
(2283, '2694', 'SPLASH EVENT SOLUTIONS LTD', '17 Glenmore Business Park, Ely Road, Weterbeach', 'CB25 9PG', 'Cambridgeshire', 'United Kingdom', '994 2730 85', '+44 0 8455196515', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2285, '2695', 'VIAJES BARCELO', 'C/ JOSE ROVER MOTTA, Nº 27', '07006', NULL, 'PALMA DE MALLORCA', 'B-07012107', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2288, '2696', 'ANT- PRODUCTIONS', 'Lumen, 6', '7880', 'Flobecq', 'BELGIUM', 'BE 0818745623', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2291, '2697', 'BMC TRAVEL', 'Passeig de Gràcia, 55, 5º, 4ª', '08007', 'Barcelona', NULL, 'B62016720', NULL, NULL, NULL, NULL, 'PROMOINVERSORA DEL VALLES, S.L.', 'C/ Arimon, 60-64', '08202', 'Sabadell', 'BARCELONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2293, '2698', 'CABINA 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2296, '2699', 'OMNIPREX INTERNATIONAL GROUP', 'AVDA. DIAGONAL 401 2º', '08008', 'BARCELONA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2299, '2700', 'COMPO EXPERT SPAIN, S.L.', 'C/ Joan d\'Austria, 39-47', '08005', 'Barcelona', 'Barcelona', NULL, '932247241', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2302, '2701', 'BCD TRAVEL SPAIN', 'AVDA. DE LA COSTA 100, BAJO', '33205', 'GIJON', NULL, 'B07012107', '985350901', NULL, NULL, NULL, 'BARCELO VIAJES, S.L.', 'Avda. de la Costa, 100', '33206', 'Gijón', 'ASTURIAS', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2304, '2702', 'VIAJES ANDROMEDA, S.A.', 'PLAZA CASTILLA, 3 PLANTA 16 E1', '28046', 'MADRID', 'MADRID', NULL, '917695977', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2306, '2703', 'NUNSYS', 'C/ GUSTAVE EIFFEL, 3 (PARQUE TECNOLOGICO)', '46980', 'PATERNA', 'VALENCIA', 'B97929566', '902881626', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2309, '2704', 'ACCIONA REAL STATE', NULL, NULL, NULL, NULL, NULL, '916632350', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2312, '2705', 'RTE. NOU MANOLIN', NULL, '03001', 'ALICANTE', 'ALICANTE', 'A-03285848', '965200368', NULL, NULL, NULL, 'NOU MANOLIN, S.A.', 'C/ VILLEGAS, Nº 3', '03001', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2315, '2706', 'VALCRES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 25.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2317, '2707', 'S&H MEDICAL SCIENCE SERVICE', NULL, NULL, NULL, NULL, '915544114', '915357183', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:01', '2026-02-18 13:04:01'),
(2320, '2708', 'EXPENSE REDUCTION ANALYSTS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2323, '2709', 'CORPORATE BLAZING EVENTS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2324, '2710', 'EVENTWORKS', 'AVDA. ORTEGA Y GASSET 210 - OFICINA 21', '29006', 'MALAGA', 'MALAGA', 'B-29832433', '952576033', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2327, '2711', 'LA TABERNA DEL GOURMET, S.L.', 'C/ SAN FERNANDO, Nº 10', '03002', 'ALICANTE', 'ALICANTE', 'B-54232152', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2330, '2712', 'CAMBRIDGE UNIVERSITY PRESS', 'C/ JOSÉ ABASCAL, 56 PLANTA 1', '28003', 'MADRID', 'MADRID', 'W-0064249-F', '911715819', NULL, NULL, 'rmisas@cambridge.org', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2332, '2713', 'NILSEN EVENT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2335, '2714', 'EXIT AUDIOVISUALES', 'C/ JOSEP FINESTRES Nº 7', '08030', 'BARCELONA', 'BARCELONA', 'B-08715666', NULL, NULL, NULL, 'regi@stereorent.es', NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2337, '2715', 'CONTADO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2338, '2716', 'CABLE DESIGN', 'CEEI PARQUE EMPRESARIAL CAMPOLLANO AVD 4ª Nº 3', '02007', 'ALBACETE', 'ALBACETE', NULL, NULL, NULL, NULL, NULL, 'CANEXIÓN DESIGN, S.L.L.', NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2340, '2717', 'PRO EVENTS Ltd', '54 Salaminos Str', '17676', 'Kallithea', 'Greece', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2343, '2718', 'BCD TRAVEL SPAIN (MADRID)', 'Camino Cerro de los Gamos,1', '28224', 'Pozuelo de Alarcón', 'Madrid', 'B07012107', '914449595', NULL, NULL, NULL, 'VIAJES BARCELO', 'C/ Jose Rover Motta, 27', '07006', 'Palma de Mallorca', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2346, '2719', 'MANOLO', NULL, NULL, NULL, NULL, NULL, '659116309', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2348, '2720', 'MUNDIPHARMA PHARMACEUTICAL', 'C/ BAHÍA DE POLLENSA, Nº 11 PB', '28042', 'MADRID', 'MADRID', 'B-82612896', '669047099', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:02', '2026-02-18 13:04:02'),
(2351, '2721', 'GASTRO PORTAL, S. L', 'CALLE BILBAO, Nº 2 BAJO', '03001', 'ALICANTE', 'ALICANTE', NULL, '965129410', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2354, '2722', 'HOTEL LA CITY', 'AVENIDA DE SALAMANCA', '03005', 'ALICANTE', 'ALICANTE', 'B-03205507', '965131973', NULL, NULL, NULL, 'HOTANTE, S.L.', 'C/ BAZAN, Nº 46 - 2º B', '03002', NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2357, '2723', 'LIMAGRAIN', 'CS 20001 SAINT BEAUZIRE', '63360', 'FRANCE', NULL, 'FR55 377913728', NULL, NULL, NULL, NULL, 'VILMORIN & CIE', '4 QUAI DE LA MÉGISSERIE', '75001', 'PARIS', 'FRANCE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2360, '2724', 'HOTEL ALMIRANTE', NULL, NULL, NULL, NULL, 'B-50671304', '965650112 - 619891137', NULL, NULL, NULL, 'ECONOMÍA Y SALUD, SL', 'Avda. NIZA 38  PLAYA DE SAN JUAN', '03540', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2362, '2725', 'COW EVENTS GROUP', 'C/. BARRIO DE LA SUIZA, 15', '28231', 'LAS ROZAS', 'MADRID', NULL, '652923489', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2365, '2726', 'MARIA PUERTO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2369, '2727', 'LABORATOIRES QUINTON INT. S.L.', 'c/. Aznar, 6', '03350', 'COX (ALICANTE)', NULL, 'B53104865', '669679150', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2371, '2728', 'WILD HIGHLANDER', '23 Woodend Park', 'KT11 3BX', 'Cobham', 'UNITED KINGDOM', 'GB876019700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2373, '2729', 'TECH&COM ALICANTE S.L.', 'Lope de Vega, 54 bajo Local 2', '03690', 'San Vicente del Raspeig', 'Alicante', 'B54603543', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2376, '2730', 'INFORMATIONSTEKNIK AB', 'källvattengatan 9', '21223', 'Malmö', 'SWEDEN', 'SE5563365703901', '+46406142679', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2379, '2731', 'MT GLOBAL', 'DR. FLEMING 3, 2º', '28036', 'MADRID', 'ESPAÑA', NULL, '915340540', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2382, '2732', 'MD COMUNICACIÓN AUDIOVISUAL', NULL, NULL, NULL, NULL, NULL, '934303303 - 633013106', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2384, '2733', 'BAC EVENTS', 'Francesc Tarrega, 11-13 local B', '08027', 'Barcelona', NULL, 'B64770118', '931138272', NULL, NULL, NULL, 'GOXOL CONSULTING, S. L.', 'CARRER FRANCESC TARREGA, 11-13', '08027', 'BARCELONA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:03', '2026-02-18 13:04:03'),
(2387, '2734', 'IAG7 EVENTS & CONGRESSES', 'Pz. Santo Domingo, 2', '28013', 'Madrid', 'Madrid', 'A84523505', '915670197', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2390, '2735', 'HATTON EVENTS, S.L.L.', 'C/ PRINCESA , Nº 22 - 2 DCHA', '28008', 'MADRID', 'MADRID', 'B-86181088', '910006010 //637308122', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2393, '2736', 'WOLF & WHITE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2395, '2737', 'ASOCIACIÓN PROF. DE FOTOGRAFOS Y VIEOGRAFOS DE ALICANTE', 'C/. Isable la catolica , 28', '03007', 'Alicante', 'Alicante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2398, '2738', 'PHONIA AUDIO ELDA, SL.', NULL, NULL, NULL, NULL, NULL, '965381080', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2401, '2739', 'B THE TRAVEL BRAND', 'C/ Miguel Angel, 33', '28010', 'Madrid', 'MADRID', 'B07012107', '914841111', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2404, '2740', 'MANUEL RADOS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2406, '2741', 'VALERIE GOLL DUPONT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2409, '2742', 'ANTONIO MARTINEZ', NULL, NULL, NULL, NULL, NULL, '689595550', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2412, '2743', 'TRIDIOM', 'C/. Principe 12,3ºC', '280812', 'Madrid', 'Madrid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2415, '2744', 'BRAND MANAGEMENT & MARKETING COMMUNICATION', NULL, NULL, NULL, NULL, 'DE-182676993', NULL, NULL, NULL, NULL, 'WILO SE', 'NortkirchenstraBe, 100', '44263', 'Dortmund', 'GERMANY', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2418, '2745', 'SIS TOURS', 'AVDA. DEPORTISTA MIRIAM BLASCO, Nº 2 - 7º D', '03540', 'ALICANTE', 'ALICANTE', 'B-54721667', '609291993 MARISA - SANDRA - 627955608', NULL, NULL, NULL, 'AMPLIO EXPECTRO, S.L.', 'AVDA. DEPORTISTA MIRIAM BLASCO, Nº 2 - 7º D', '03540', 'ALICANTE', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2420, '2746', 'ES-CULTURA EVENTOS', NULL, NULL, NULL, NULL, NULL, '629038630', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:04', '2026-02-18 13:04:04'),
(2423, '2747', 'IVAN GARCIL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2426, '2748', 'GYRO AS', 'Pb 3403 Bjolsen', '0462', 'OSLO', 'NORWAY', '9781286222MVA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2429, '2749', 'FEDELE', 'C/. ALMANSA, 9 1º', '29007', 'MALAGA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2432, '2750', 'HSIXTEM PROACTIVE WORK, S.L.', 'C/. Caléndula nº 93 edf. F 1º', NULL, 'Soto de la Moraleja (Alcobendas)', 'Madrid', 'B 87426383', '911697168', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2434, '2751', 'RED HOT PRODUCTS, LTD', '3-4 Barton court, Jacks Way, Hill Barton Business', 'EX5 1FG', 'Clyst St. Mary, Exeter', 'UNITED KINGDOM', 'GB946720113', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2437, '2752', 'VIAJES LEVANTE', 'C/. ALBACETE 14', '46007', 'VALENCIA', NULL, 'A46396735', '963288399', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2440, '2753', 'SALON DIRECT', 'SKOVVEJ 35', '3400', 'HILLEROED', 'DENMARK', 'DK34230307', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2443, '2754', 'SCANWORLD AB', 'BOX 6086', '171 06', 'SOLNA', 'SWEDEN', 'SE556301457901', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2445, '2755', 'MONSANTO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2448, '2756', 'PANZERI DIFFUSION, Srl', 'Via Brodolini, 30', '21046', 'Malnate (VA)', 'ITALIA', 'IT02879900120', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2451, '2757', 'GRUPO AGENCIAS DE VIAJE INDEPENDIENTE', 'C/, LAS MERCEDES 28', NULL, 'TORREMOLINOS', 'MALAGA', 'B29661527', '952 37 66 55 / 678 78 71 79', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2454, '2758', 'HOTEL HESPERIA MURCIA', NULL, NULL, NULL, NULL, NULL, '968 21 77 89 EXT. 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2457, '2759', 'CWT MEETINGS&EVENTS', 'Avda. Albert Bastardas, 33 2ª Planta', '08028', 'Barcelona', 'España', 'B81861304', '93 603 96 02', NULL, NULL, NULL, 'CWT GLOBAL ESPAÑA, S.L.', 'C/ Julian Camarillo, 4 Edificio II', '28037', 'MADIRD', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:05', '2026-02-18 13:04:05'),
(2459, '2760', 'COFELY ESPAÑA, S.A.', 'C/ MILENIO, 11 (POL. IND. OESTE)', '30820', 'ALCANTARILLA', 'MURCIA', 'A-28368132', '968271265 650976004 JOSE MANUEL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2462, '2761', 'CE CONSULTING EMPRESARIAL', 'C. Princesa 24', '28008', 'Madrid', 'Madrid\r\nMadrid', NULL, '915410000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2465, '2762', 'LUXORA PRODUCCIONES AUDIOVISUALES, S.L.', 'C/ SAN NORBERTO, 38', '28021', 'MADRID', 'MADRID', 'B84563105', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2468, '2763', 'MAIER SOUND DESIGN', 'MERBERT WHENER Str. 19', '59174', 'KAMEN', NULL, NULL, '02307240233', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2470, '2764', 'CAIXABANK EQUIPMENT FINANCE, S.A.U.', 'Gran Vía Carles III 87', '08028', 'Barcelona', 'BARCELONA', 'A58662081', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2473, '2765', 'RICARDO PORTES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2477, '2766', 'ACZEDA, S.L.', 'C/ Velazquez, 114 Bajo derecha', '28006', 'Madrid', 'MADRID', 'B-61322301', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2479, '2767', 'SECRETARIA DE ESTADO DE ASUNTOS EXTERIORES', 'C/ Serrano Galvache, 26 Planta 12', '28071', NULL, 'Madrid', 'S2812001B', '913798795', '913948621', NULL, NULL, 'SECRETARIA DE ESTADO DE ASUNTOS EXTERIORES', 'C/ Serrano Galvache, 26 Torre Norte  Planta 12', '28033', 'Madrid', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2481, '2768', 'FOOT LOCKER SPAIN SLU', 'C/ BALMES, 195 8-1', '08006', 'BARCELONA', 'BARCELONA', 'B80030315', '932387325', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2485, '2769', 'ASOCIACIÓN ESPAÑOLA DE FABRICANTES DE JUGUETES', 'C/ La Ballaora, nº 1', '03440', 'Alicante', 'Ibi', 'G-28509131', '966554977 - 651862115 Jose Manuel', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2487, '2770', 'AFID CONGRESOS S.L.', 'C/ Menéndez Pelayo 6 - entresuelo A', '39006', 'SANTANDER', 'SANTANDER', 'B39480991', '942318180', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2490, '2771', 'EVENTOS 7 IN PROGRESS, S.L.', 'C/ VIRGEN DEL PERPETUO SOCORRO, Nº 1', '41010', 'SEVILLA', 'SEVILLA', 'B-90286402', '639713473', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2493, '2772', 'KYOWA KIRIN FARMACEUTICA, S.L.U', 'AVDA. DE BURGOS 17, PLANTA 1', '28036', 'MADRID', 'MADRID', 'B83788950', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2495, '2773', 'VIAJES EL CORTE INGLES S.A', 'AVDA. CANTABRIA, 51', '28042', 'MADRID', 'MADRID', 'A-28229813', '917455524 - EXT 124', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:06', '2026-02-18 13:04:06'),
(2498, '2774', 'ORIGEN WORLS WIDE, S.L.U.', 'C/ Orense, 68 - 2º Izda.', '28020', 'Madrid', 'Madrid', 'B-86064581', '608966360', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2501, '2775', 'UNIONTOURS, S. L.', 'C/ GALILEO, 306', '08028', 'BARCELONA', 'BARCELONA', 'B-61417788', '934192030', NULL, NULL, 'viajes@uniontours.es', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2504, '2776', 'TRASSCENA SOLUCIONES TÉCNICAS, S.L.', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2507, '2777', 'SBD TRAVEL', 'De L\'Estrella, 163 Bis', '08201', 'Sabadell', 'Barcelona', NULL, '937451505', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2509, '2778', 'JEUNESSE GLOBAL (EUROPE) LIMITED', 'FRODSHAM BUSINESS CENTER BRIDGE LANE', NULL, 'FRODSHAM', 'UK Wa6 7FZ', 'GB 103 3515 65', '638430820', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2512, '2779', 'HOGAN LOVELLS ALICANTE CL & CIA', 'AVDA.  MAISONAVE, Nº22', '03003', 'ALICANTE', 'ALICANTE', 'B53510046', '965138300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2515, '2780', 'TIG SPORTS', 'Valschermkade, 30', '1059 CD', 'Amsterdam', 'HOLLAND', '8196.24.640.B01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2518, '2781', 'INDEX VIDEO', 'FONT DE LA UXOLA, 4', '03803', 'ALCOY', 'ALICANTE', NULL, '965522675 // 670 638 921', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2520, '2782', 'ERUM GROUP', 'C/ Banyeres, 1 Pol. Ind. El Clérigo, S/N Parc. 2', '03802', 'ALCOY', 'ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2523, '2783', 'CASCADE PRODUCTIONS', 'CROSSFORD COURT, DANE ROAD', 'M33 7BZ', 'SALE', 'MANCHESTER', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2526, '2784', 'COSTABLANCA PORTUARIA, S.L.', 'MUELLE DE LEVANTE, 14', '03001', 'ALICANTE', 'ALICANTE', 'G-53053534', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2529, '2785', 'SUBCIELO', NULL, NULL, NULL, NULL, NULL, '620180649', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2532, '2786', 'GRUPO PIKOLINOS', 'C/GALILEO GALILEI, Nº 1', '03203', 'ELCHE PARQUE EMPRESARIAL', 'ALICANTE', 'A-53238713', NULL, NULL, NULL, NULL, 'PIKOLINOS INTERCONTINENTAL, S.A.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:07', '2026-02-18 13:04:07'),
(2534, '2787', 'BCD MEETINGS & EVENTS', 'Grafenberger Allee 295', '40237', 'Düsseldorf', 'Germany', 'DE248864645', NULL, NULL, NULL, NULL, 'BCD TRAVEL GERMANY GmbH, BCD MEETINGS & EVENTS', 'Grafenberger Allee 295', '40237', 'Düsseldorf', 'Gemany', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2537, '2788', 'EVENTS & CO', 'PLZ. PADRE JERONIMO DE CORDOBA 7, 2º', '41003', 'SEVILLA', 'SEVILLA', NULL, '954215663', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2540, '2789', 'QUICKSAIL', 'MARINA DE VALENCIA LOCAL 8', '46024', 'VALENCIA', 'VALENCIA', 'U72587439', '669777565 PABLO 687353189 DEBORAH 677444662 ALFONS', NULL, NULL, NULL, 'UTE QUICKSAILCHARTER,S.L.', 'MARINA DE VALENCIA, LOCAL 8', '46024', 'VALENCIA', 'VALENCIA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2544, '2790', 'FERRETERIA ENCARNA, S.L.', 'C/ FERIA S/N', '30170', 'MULA', 'MURCIA', 'B-73484057', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2545, '2791', 'TARSA COM. SERVEIS DE COMUNICACIÓ i PROTOCOL, S.L.', 'Porta Oriola, 6 Bajo', '03203', 'Elche', 'Alicante', 'B53257382', '902365735', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2548, '2792', 'X-TERNAL', 'C/ Berlin 4 PORTAL 2', '28224', 'Pozuelo de Alarcon', 'Madrid', NULL, '914008433/ 608367838', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2551, '2793', 'IGLESIA NUEVA APOSTOLICA', NULL, NULL, NULL, NULL, NULL, '605675094', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2554, '2794', 'HOTEL CAP NEGRET', 'Pda. Cap Negret., 7 CN 332 Alicante-Valencia Km159', '03590', 'Altea', 'Alicante', NULL, '965841200 - 965841250 - 690948791', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2557, '2795', 'RESTAURANTE EL PORTAL', 'C/ BILBAO, Nº 2', NULL, 'ALICANTE', 'ALICANTE', 'B54421953', NULL, NULL, NULL, NULL, 'GASTROPORTAL, S.L.', 'C/ BAILEN, 15 1º IZQUIERDA', '03001', 'ALICANTE', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2559, '2796', 'ROMAVEL CONSULTING', 'GALILEO 110', '28003', 'MADRID', 'MADRID', 'B86022621', '605080454', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2562, '2797', 'EGAL ENTERTAINMENT', NULL, NULL, NULL, NULL, NULL, '646294354', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2566, '2798', 'B THE TRAVEL BRAND', 'C/SAN TELMO, 9', '03002', NULL, 'ALICANTE', 'B07012107', '965210011', NULL, NULL, NULL, 'VIAJES BARCELO, SL', 'C/ JOSE ROVER MOTTA, 27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2568, '2799', 'WORLD BUSINESS TRAVEL EVENTS', 'C/. Sant Josep, 6', '08291', 'RIPOLLET', 'BARCELONA', NULL, '93 5863580 EXT. 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:08', '2026-02-18 13:04:08'),
(2570, '2800', 'ASEMWORK ETT', 'AVDA. BARON DE CARCER Nº 19 - 5º PISO- PTA 9', '46001', NULL, 'VALENCIA', 'B-97634430', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2573, '2801', 'IAG7 VIAJES - EVENTS (BCN)', 'C/. Vilamarí 110-112', '08015', 'BARCELONA', 'BARCELONA', NULL, '931599695', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2576, '2802', '*AV INSIGNIA', 'AVDA. GRAL. ORTIN, Nº 3 - 1º H', '30010', 'MURCIA', 'MURCIA', '23053360-T', '868083559', NULL, NULL, NULL, 'Mª DE LOS ANGELES NAVARRO MORALES', 'AVDA. GRAL. ORTIN, Nº 3 - 1º H', '30010', 'MURCIA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2579, '2803', 'ENTERTAINMENT HISPALIS, S.L.', 'C/ BALANCE 16 - PLIGONO PISA', '41927', 'MAIRENA DEL ALJARAFE', 'SEVILLA', 'B-91545350', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2582, '2804', 'BRANCHOUT PRODUCTIONS, Ltd', '38 Forest Edge', 'IG9 5AA', 'ESSEX', 'Buckhurst Hill', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2584, '2805', 'CREATIVESPIRIT', 'Rosselló 255, principal 1ª', '08008', 'Barcelona', 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2587, '2806', 'INTERVENCIÓN CENTRAL DE ARMAS Y EXPLOSIVOS DE LA GUARDIA CIVIL', NULL, NULL, NULL, NULL, NULL, '915142942', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2590, '2807', 'NTT EUROPE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2593, '2808', 'ASOCIACIÓN INTERNACIONAL FE Y LUZ', NULL, NULL, NULL, NULL, 'SIRET 32945053000020', NULL, NULL, NULL, NULL, 'FOI ET LUMIERE INTERNATIONAL', '3 RUE DU LAOS', '75015', 'PARIS', 'FRANCE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2595, '2809', 'EXCLUSIVE PRODUCTS', 'PASSEIG DE GRACIA 118. PRINCIPAL', '08008', NULL, 'BARCELONA', 'b65425373', '932553173 - 661232320 SILVIA', NULL, NULL, NULL, 'Exclusive Products, Exclusive Publicitarias, S.L.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2598, '2810', 'VIDEO PROMOCION S.L.', 'c/. Antonio de Nebrija, 35', '29620', 'TORREMOLINOS', 'MALAGA', 'B-29071784', '952378391', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2601, '2811', 'AV ICENTER', 'C/ RUBÉN DARÍO, 38, 9º - 21ª', '46021', 'VALENCIA', 'VALENCIA', 'ES73377813Q', '644055262', NULL, NULL, NULL, 'ALICIA BOSH BALLESTER', 'C/ RUBÉN DARÍO, 38, 9º - 21ª', '46021', 'VALENCIA', 'VALENCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2604, '2812', 'COLEGIO TERRITORIAL DE ARQUITECTOS DE ALICANTE', 'Pza. Gabriel Miró,2', '03001', 'Alicante', 'Alicante', NULL, '965218400', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:09', '2026-02-18 13:04:09'),
(2606, '2813', 'INTERSPORT', '13 rue Raspail', '92300', 'Levallois Perret', NULL, 'FR68802268136', '33972390555', NULL, NULL, NULL, 'GOOD STORY EVENTS', '13 rue Raspail', '92300', 'Levallois Perret', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2609, '2814', 'BRAHLER ICS España', 'C/. Forjadores 32,  P. E. Prado del Espino', '28660', 'Boadilla del Monte', 'Madrid', 'B59825075', '91 6324515', NULL, NULL, NULL, 'BMOTION AUDIOVISUAL, S.L.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2612, '2815', 'ITB DMC', 'Avda. Diagonal 327 c 2ª', '08009', 'Barcelona', 'Barcelona', 'B-66520701', '936898863', NULL, NULL, NULL, 'INNOVATIVE TALENT BUREAU, SL', 'AVDA. DIAGONAL 327, C2', '08009', 'BARCELONA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2615, '2816', 'YOUR EVENT SOLUTIONS', '45 Clarges Street', 'W1J 7EP', 'Mayfair', 'LONDON', '755713025', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2617, '2817', 'SGS ESPAÑA', NULL, NULL, NULL, NULL, NULL, '619787928', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2620, '2818', 'SEVEN-SM', 'C/. Nuñez Morgado, 5  local', '28036', 'Madrid', 'Madrid', NULL, '913237483', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2623, '2819', 'ASOC. PROFESIONAL DE FOTOGRAFOS Y VIDEOGRAFOS DE ALICANTE', 'C/ ISABEL LA CATALICA, Nº 28', '03007', 'ALICANTE', 'ALICANTE', 'G03691557', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2626, '2820', '*KYOWA KIRIN FARMACEUTICA, S.L.U', 'AVDA. DE BURGOS 17, 1ª PLANTA', '28036', 'MADRID', 'MADRID', 'B-83788950', '620517249', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2629, '2821', 'BERTRAN MUSIC', NULL, NULL, NULL, NULL, '52771763B', NULL, NULL, NULL, NULL, 'MANUEL ENRIQUE GIRONA ORTIZ', 'C/ CASIOPEA Nº 21', '03110', 'MUCHAMIEL', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2631, '2822', 'AYUNTAMIENTO DE BENIDORM', 'PL. SSMM. REYES DE ESPAÑA, 1', '03501', 'BENIDORM', 'BENIDORM', NULL, '966815518', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2634, '2823', 'IVAN STANIMIROV', 'C/. Irlanda 16 BL. 2 Esc. 1 7 E', '03540', 'San Juan Playa', 'Alicante', '642302709', '632856965', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2637, '2824', 'JOSÉ LUIS MARTINEZ (J. MTO.  ASIA G.)', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2640, '2825', 'ALBENTIA SYSTEMS', 'C/ MARGARITA SALAS 22 (PARQUE TECNOLOGICO LEGANES)', '28918', 'MADRID', 'MADRID', 'A-84179514', '914400213', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2643, '2826', 'HOTEL NH ALICANTE', 'Avda. Mexico, 18', '03008', 'Alicante', 'Alicante', 'ESA-58511882', '965108140', NULL, NULL, NULL, 'NH HOTELES ESPAÑA, S.A. 0322 ES10 HOTEL NH ALICANTE', 'ALFONSO GOMEZ, 32', '28037', 'MADRID', 'MADRID', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:10', '2026-02-18 13:04:10'),
(2645, '2827', '*HOTEL NH AMISTAD', 'C/. Condestable, 1', '30009', 'Murcia', 'Murcia', 'ESA58511882', '968282929', NULL, NULL, NULL, 'NH HOTELES ESPAÑA, S.A. 0128 ES10 NH AMISTAD  MURCIA', 'ALFONSO GOMEZ, 32', '28037', 'MADRID', 'MADRID', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2648, '2828', 'EVENTICA ORGANISATION, S.L.', 'C/ AVILA, 71, 6ª PLANTA', '08005', 'BARCELONA', 'BARCELONA', 'B65360513', '931863228  //  622544197', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2651, '2829', 'GLOBAL BUSINESS TRAVEL SPAIN', 'C/ ALBASANZ, 14 - 2ª PLANTA', '28037', 'MADRID', 'MADRID', 'B-85376630', '699055597 ROSA LUZ', NULL, NULL, NULL, 'GLOBAL BUSINESS TRAVEL SPAIN, S.L.', 'C/ VÍA DE LOS POBLADOS,1 - EDIFICIO D - PLANTA 6', '28033', 'MADRID', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2654, '2830', 'VOS VAN LOON & PARTNERS', 'Sumatrake  689', '1019', 'Amsterdam', 'The Netherlands', 'NL 8095.75.000.B.01', '0031 0 20 4197171', NULL, NULL, NULL, 'GENMAB B.V.', 'YALELAAN 60', '3584 CM UT', 'THE NETHERLANDS', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2656, '2831', 'NINEYARDS', 'VÄSTRA HAMNGATAN 21, 411 17', NULL, 'GOTHENBURG', 'SWEDEN', 'SE556652528201', '+46 73 416 73 07', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2659, '2832', 'HOTEL NH RAMBLA DE ALICANTE', 'Tomas López Torregrosa, 9', '03002', 'ALICANTE', 'ALICANTE', 'ESA-58511882', '965143659', NULL, NULL, NULL, 'NH HOTELES ESPAÑA, S.A. 0007 ES10 NH RAMBLA DE ALICANTE', 'ALFONSO GÓMEZ, 32', '28037', 'MADRID', 'MADRID', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2662, '2833', 'AME MATERIAL ELECTRICO, S.A.U.', 'POL. IND. CATARROJA - CALLE 32 - Nº 208', '46470', 'CATARROJA', 'VALENCIA', 'A-96933510', '647537872', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2665, '2834', 'FN7 COMUNICACIÓN', 'C/ INDUSTRIA 22', NULL, 'VALENCIA', 'VALENCIA', NULL, '902627127 - 608344229', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2669, '2835', 'B MOTION AV', 'C/ FORJADORES, 32', '28660', 'MADRID', 'BOADILLA DEL MONTE', 'B-59825075', '916324515 / 649072081', NULL, NULL, NULL, 'BRAHLER ICS, S.L.', 'C/ FORJADORES, 32', '28660', 'Boadilla del Monte', 'MADRID', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2670, '2836', 'PROLOGIS', 'Gustav Mahlerplein 17-21', '1082', 'MS Amterdam', 'The Nedherlands', 'NL810757850B01', '+31 20 6556638', NULL, NULL, NULL, 'PROLOGIS MANAGEMENT BV', 'Gustav Mahlerplein 17-21', '1082', 'MS Amterdam', 'The Netherlands', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2673, '2837', 'EUROPEAN UNION INTELLECTUAL PROPERTY OFFICE', NULL, NULL, NULL, NULL, NULL, '965137247', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2677, '2838', 'ALBERTO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2679, '2839', 'BLUE MEETINGS & EVENTS S.L.', 'HANGAR NAUTICO S/N   P.  DEPORTIVO V. DEL CARMEN', '29602', 'MARBELLA', 'MALAGA', 'ESB 93543155', '647717030', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:11', '2026-02-18 13:04:11'),
(2682, '2840', 'COLAS Y ADHESIVOS OBRADOR, S.A.', 'Ctra. De Agost, 59 Pol. Ind. El Canastell', '03690', 'San Vicente del Raspeig', 'Alicante', NULL, '965663348', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2685, '2841', 'T2 EVENTS GMBH', 'Limmatquai, 84', '8001', 'ZURICH (SWITZERLAND)', NULL, 'CHE-427.028.111', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2687, '2842', 'DOBLE M AUDIOVISUALES', 'Avda. de Pilas, Parcelas 9-11  Parque Ind. P.I.B.O', '41110', 'Bolullos de la Mitación', 'Sevilla', 'B-91660100', '955776968', NULL, NULL, NULL, 'DOBLE M AUDIOVISUALES CORPORATION, S.L.', 'AVDA. DE PILAS, Nº 9-11', '41110', 'SEVILLA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2690, '2843', 'DIVERGENT ANIMACIÓ I ESDEVENIMENTS', 'PARTIDA LA XARA, Nº 18', '03709', 'LA XARA', 'ALICANTE', NULL, '664130520', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2693, '2844', 'QUUM COMUNICACIÓN, S.A.', 'Paseo General Martinez Campos 15, piso 6 centro dr', '28010', 'MADRID', 'MADRID', NULL, '616102113', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2696, '2845', 'AMERICAN EXPRESS MEETINGS & EVENTS', 'E. VERONA. C/. ALBASANZ, 14-2 PTA.', '28037', 'MADRID', 'MADRID', 'B-85376630', '913858621', NULL, NULL, NULL, 'GLOBAL BUSINESS TRAVEL SPAIN, S.L.', 'C/ ALBASANZ, 14 - 2ª PLANTA', '28037', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2698, '2846', 'GLOBALIA MEETINGS & EVENTS', 'Enrique Granados, 6 Edficio A', '28224', 'Pozuelo de Alacrcon', 'Madrid', 'B57986846', '915425064', NULL, NULL, NULL, 'SEKAI CORPORATE TRAVEL, S.L.U', 'C/ JOSÉ MOTTA,27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2701, '2847', 'BEIJING D&S CONSULTING CO, Ltd', 'C/7 Building Dongyl Media Park, 8', '100123', 'BEIJING', 'CHINA', NULL, '138 1038 5970', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2704, '2848', 'BE ON WORLD WIDE', NULL, NULL, NULL, NULL, NULL, '915917830 / 722347719', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2707, '2849', 'QUEENS AGENCY EVENTS ORGANIZATION', 'Infanta Mercedes, nº 12', '28020', 'MADRID', 'MADRID', 'B82760695', '915644449 - 686961436', NULL, NULL, NULL, 'QUUENS PUBLICIDAD, SL', 'C/ INFANTA MERCEDES, 12', '28020', NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2710, '2850', 'GRUPO ORENES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2713, '2851', 'SERGIO PEREZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2716, '2852', 'AGROEVENT, S.L.', 'AVDA. MAISONAVE Nº 33, 3º A', '03003', 'ALICANTE', 'ALICANTE', 'B-42504530', '600726526', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:12', '2026-02-18 13:04:12'),
(2718, '2853', 'ASOCIACIÓN DE CORREDORES DE SEGUROS DE LA COMUNIDAD VALENCIANA', 'PZA. ALQUERIA DE LA CULLA, Nº 4 OFICINA 501', '46910', 'ALFAFAR', 'VALENCIA', 'G96217773', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2721, '2854', 'TRAVEL PARTNERS', 'C/. Provença, 122', '08029', 'Barcelona', 'Barcelona', NULL, '934511909    667876958', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2723, '2855', 'POP UP EVENTS', 'Vía Augusta 6, Principal', '08006', 'BARCELONA', NULL, 'B66845587', NULL, NULL, NULL, NULL, 'TROCOLA CORP, S.L.', 'Vía Augusta 6, Principal', '08006', 'BARCELONA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2726, '2856', 'INTERBAN NETWORK', 'C/. Ulises,  108 planta 1ª', '28043', 'Madrid', 'Madrid', NULL, '917638711  // 690641510', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2729, '2857', 'PAUL EVENTS GMBH', 'Gottieb-Binder-Strabe 17', '71088', 'HOLZGERINGEN', NULL, 'DE129433727', '+49-173-6632342', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2732, '2858', 'INMOBILIARIA CAMBERNIA, S.A.', 'URB. SIERRA DE ALTEA GOLF, S/N', '03599', 'ALTEA LA VELLA', 'ALICANTE', 'A-03034816', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2734, '2859', 'INTERNATIONAL MEETINGS', 'CLARA DEL REY,2', '28002', 'MADRID', 'MADRID', NULL, '915932953', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2737, '2860', 'DOMINIQUE RATER DE JOYBERT', NULL, NULL, NULL, NULL, NULL, '656348566', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2740, '2861', 'VMR', 'Ul. Bednarska 1', '60-571', 'Poznan', 'Poland', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2743, '2862', 'LABORATORIOS  LETI , S.L. UNIPERSONAL', 'Gran Vía de les Corts Catalanes, 184 7º 1ª', '08038', 'BARCELONA', 'BARCELONA', 'B78152725', '933940536 / 600966321 Pepa', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2745, '2863', 'IMPRIVIC, S.L.', 'Av. Miguel Hernández, nº62, nave 4', '03550', 'SAN JUAN DE ALICANTE', 'ALICANTE', NULL, NULL, NULL, NULL, 'imprivic@imprivic.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2748, '2864', 'VIAJES IN OUT TRAVEL, S.L.', 'C/ Cristobal Bordiu, nº 53', '28003', 'Madrid', 'Madrid', 'B-87083481', NULL, NULL, NULL, NULL, 'AVEXPRESS MEDIA SOUND SL.', 'C/ Cristobal Bordiu, nº 53', '28003', 'Madrid', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2752, '2865', 'VIAJES EL CORTE INGLES (SEVILLA)', 'AVDA. DE CANTABRIA Nº 51', '28042', 'MADRID', 'MADRID', 'A28229813', '954506602', NULL, NULL, NULL, 'VIAJES EL CORTE INGLES, S.A.', 'AVDA. DE CANTABRIA Nº 51', '28042', 'MADRID', 'MADRID', NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:13', '2026-02-18 13:04:13'),
(2754, '2866', 'MERCADOS CENTRALES DE ABASTECIMIENTO DE ALICANTE, S.A.', 'Carretera de Madrid, Km 4', '03007', 'ALICANTE', 'ALICANTE', 'A-03021961', '966081001', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2757, '2867', 'OMNITEL', NULL, NULL, NULL, NULL, NULL, '902194230 (ext 1002) 628085478', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2759, '2868', 'STOP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2762, '2869', 'SOLUTIONS EVENTOS', NULL, NULL, NULL, NULL, NULL, '678424340 // 687387875', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2765, '2870', 'DSM NUTRICIONAL PRODUCTS, AG', 'Wurmisweg, 576', 'CH-4303', 'Kaiseraugst', 'Switzerland', 'CHE-116.320.592', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2768, '2871', 'L´EVENT GROUP', 'C/. Pico de la Miel, 19', '28023', 'Madrid', 'Madrid', 'B84416213', '619321875', NULL, NULL, NULL, 'PARIMPAR GRUPO L\'EVENT', 'C/. Pico de la Miel, 19', '28023', 'Aravaca', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2770, '2872', 'KT EVENTS', 'C/ Rocafort, 133 Entlo.', '08015', 'Barcelona', 'BARCELONA', 'B-65302051', '932405240 / 696948455 (Catherine)', NULL, NULL, NULL, 'PAJARO QUE VUELA, S.L.', 'C/ MUNTANER, 202 - PRAL. E', '08036', 'BARCELONA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2773, '2873', 'A COMPANY SWEDEN, AB', 'Upplandsgatan, 7', '11123', 'Stockholm', 'Sweden', 'SE556750540801', '+46 8 58815305  // +46 8 588 15300', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2776, '2874', 'VFI 360º', 'The Loft, 18 Grove Place', 'MK40 3JJ', 'Bedford', 'Reino Unido', 'GB126235828', NULL, NULL, NULL, NULL, 'Beverage Services Ltd c/o Vfi360 Ltd', '1A Wimpole Sstreet', 'W1A 0EA', 'Marylebone', 'LONDON', NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2779, '2875', 'DIRECTO SPAIN', 'Urb. Los Naranjos, Manzana2 Casa 10', '29660', 'Marbella', 'Marbella', NULL, '952828304  //  677436842', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2782, '2876', 'EVENTOS 4', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2784, '2877', 'ESET, spol. S.r.o.', 'Einsteinova, 24 Aupark Tower', '851 01', 'Bratislava', 'Slovak Republic', NULL, NULL, NULL, 'www.eset.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2787, '2878', 'THERAKOS (UK), Ltd', '3 Lotus Park, Staines Upon Thames, TW18 3AG', 'GBR006', NULL, 'United Kingdom', 'GB287249363', '+447717700692', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:14', '2026-02-18 13:04:14'),
(2790, '2879', 'RADIO BENIDORM INTERNACIONAL, S.L.', 'AVDA. MEDITERRANEO Nº 53, EDF. EDIMAR BAJO', '03503', 'BENIDORM', 'ALICANTE', 'B-03195716', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2793, '2880', 'CAMELEON EVENT', 'Rue de l´Est, 4', '1207', 'Geneva', 'Switzerland', 'CH114273036', '+41 o 227354428', NULL, NULL, NULL, 'CAMELEON ORGANISATIONS', '4 Rue de l\'Est', '1207', 'GENEVE', 'SWITZERLAND', NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2796, '2881', 'REED & MACKAY ESPAÑA S.A.U.', 'C/. Carretas, 14-8º H', '28012', 'Madrid', 'Madrid', 'A-08649477', '913104376', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15');
INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(2799, '2882', 'AOK EVENTS', 'The Engine Rooms, 150 A', 'SW112LW', 'Falcon RD', 'London (REINO UNIDO)', '756 6052 18', NULL, NULL, NULL, NULL, 'AOK EVENTS', 'THE ENGINE ROOMS 150A FALCON ROAD', 'SW4 9EF', 'LONDON', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2801, '2883', 'VICKY APOSTOLOPOULOS', NULL, NULL, NULL, NULL, '94059900152', NULL, NULL, NULL, NULL, 'ARGON 18 Inc', '6833 Avenue de L\'Epee Suite 208', 'H3N 2C7', 'MONTREAL', 'CANADA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2804, '2884', 'LIMENIUS, SL', 'CAMI VELL DE BATOI, 10, 3B', '03802', 'ALCOY', 'ALICANTE', 'B54798632', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2806, '2886', 'BUSINESS SHOWS LIMITED', 'Foresters Hall 25 Clyde Vale', 'SE23 3JG', 'Foresters Hill', 'LONDON', 'GB133750233', '+442086998888    //  +4479731940470', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2809, '2887', 'GRUPO VAPF', 'AVDA. PAIS VALENCIA, Nº22', '03720', 'BENISSA', 'ALICANTE', NULL, '965734017', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2812, '2888', 'SINDICATO REGIONAL FASGA LEVANTE', 'C/ Marqués de Dos Aguas, 5 - 1', '46004', 'Valencia', 'Valencia', 'g-96324850', '963518086', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2815, '2889', 'WIDE TRAVEL, VIAGENS E TURISMO, LDA', 'Av. Almirante Gago Coutinho, 28 C, Areeiro', '1000-017', 'LISBOA', 'PORTUGAL', '508773911', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2818, '2890', 'VIAJES EL CORTE INGLES (VALENCIA)', 'AVDA. PIO XII, 51 - C.C. ADEMUZ 4ª PLANTA', '46015', 'VALENCIA', 'VALENCIA', 'A28229813', '963107660', NULL, NULL, NULL, 'VIAJES EL CORTE INGLÉS, SA', 'Av/ CANTABRIA, 51', '28042', 'MADRID', 'MADRID', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2820, '2891', 'INUSUAL EVENTS', 'C/ VALDERREY, 5 - LOCAL', '28035', 'MADRID', 'MADRID', NULL, '913510540', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2823, '2892', 'MANAGEMENT ACTIVO', 'C/ TRINIDAD GRUND, 4 PLANTA 3 OFICINA 34', '29001', 'MALAGA', 'MALAGA', 'B-93089357', NULL, NULL, NULL, NULL, 'THINKVALUE, S. L.', 'C/ TRINIDAD GRUND, 4 PLANTA 3 OFICINA 34', '29001', 'MALAGA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2826, '2893', 'OH YES GROUP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:15', '2026-02-18 13:04:15'),
(2829, '2894', 'HOTEL LA TORRE GOLF RESORT & SPA', NULL, NULL, NULL, NULL, NULL, '968031973', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2832, '2895', 'INTERPROFIT (MADRID)', 'C/. ALMIRANTE, 5 1º DCHA', '28004', 'MADRID', 'MADRID', NULL, '915159510', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2834, '2896', 'LABORATORIOS NUTERGIA, S.L.', 'PASEO FRANCIA 14 BAJO', '20012', 'SAN SEBASTIAN', 'GUIPUZCUA', 'B-75040477', NULL, NULL, NULL, NULL, 'CELIDYN, S.L.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2837, '2897', 'DM EUROPE B.V.', 'P.O. BOX 8744', '5605', 'LS EINDHOVEN', 'THE NETHERLANDS', 'NL. 800594757 B01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2840, '2898', 'NOISY STUDIO, S.L.', 'AVDA. QUITAPESARES, 32 - NAVE 20', '28670', 'VILLAVICIOSA DE ODÓN', NULL, 'B-82294018', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2844, '2899', 'CAPITAL RADIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2845, '2900', 'TEAM TRAVEL', 'STATSRAD TANKS GATE, 7', '1777', 'NORUEGA', 'HALDEN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2848, '2901', 'AVEPA', 'PASEO SAN GERVASIO 46-48, E-7', '08022', 'BARCELONA', 'BARCELONA', 'G-58306531', '932531522', NULL, NULL, 'midelsohn@avepa.org', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2851, '2902', 'RD EVENTOS', 'C/. Francisco José Arroyo, 6', '28042', 'Madrid', 'Madrid', 'B-85793024', '913562950  //654068874', NULL, NULL, NULL, 'ERREYDE EVENTOS SLU', 'C/RUIZ OCAÑA 3', '28028', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2854, '2903', 'IRE VIAJES, S.L.', 'C/ BALMES,301 PRAL 2', '08006', 'BARCELONA', 'BARCELONA', NULL, '932387455', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2856, '2904', 'GRUPO ANTON', 'CAMILO FLAMMARION, 1', '03201', 'ELCHE', 'ALICANTE', NULL, '965442612', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2859, '2905', 'ACCURACY', 'Paseo de la Castallna 53, 6', '28046', 'Madrid', 'Madrid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2862, '2906', 'VIDEOGENIC BROADCAST, S.L.', 'C/ MAESTRO  MARQUES 70', '03004', 'ALICANTE', 'ALICANTE', '21483164 -Z', NULL, NULL, NULL, NULL, 'MIGUEL ANGEL GARVI BAUTISTA', 'C/ MAESTRO  MARQUES 70', '03004', 'ALICANTE', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:16', '2026-02-18 13:04:16'),
(2865, '2907', 'ANGELA CANTOS GARCÍA', 'C/ CASTAÑOS 30, 1', '03130', 'SANTA POLA', 'ALICANTE', '48673114-P', '680195020', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2868, '2908', 'PARTYTECTURE LTD', '96a FORTESS ROAD', 'NW5 2HJ', 'LONDON', 'LONDON', 'GB798976523', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2870, '2910', 'DCAIMAN', 'CALLE CABALLEROS, 5 3º 2ª', '08014', 'BARCELONA', 'BARCELONA', '34744412-Z', NULL, NULL, NULL, NULL, 'DANIEL JOVÉ DEGRACIA', 'CALLE CABALLEROS, 5 3º 2ª', '08014', 'BARCELONA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2873, '2911', 'KERAPLUS, S.L.', 'POL. INDUSTRIAL EL RUBIAL C/. 1 P.10', '03400', 'VILLENA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2876, '2912', 'URBINCASA', 'C/JUAN FERNANDEZ, 61', '30204', 'CARTAGENA', 'MURCIA', 'A-30603716', '968510380', NULL, NULL, NULL, 'URBANIZADORA E INMOBILIARIA CARTAGENA, S.A', 'C/JUAN FERNANDEZ, 61', '30204', 'CARTAGENA', 'MURCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2879, '2913', 'ABOUT EVENTS AND TRAVELS, S.L.U', 'C/ Numancia, 39 Local 4', '08029', 'Barcelona', 'BARCELONA', 'B64832512', '936111999', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2882, '2914', '** VIAJES HISPANIA', 'AVDA. MAISONAVE, 11 - 7ª PLANTA', '03003', 'ALICANTE', 'ALICANTE', 'B-07012107', '965228393 - (965141125 EXPLANADA)', NULL, NULL, NULL, 'VIAJES HISPANIA by BCD', 'AVDA. MAISONAVE, 11 - 7ª PLANTA', '03003', 'ALICANTE', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2884, '2915', 'HOTEL MELIA VILLAITANA', 'AVDA. ALCALDE EDUARDO ZAPLANA, 7', '03502', 'BENIDORM', 'ALICANTE', 'A-96730254', '966815000 - 966815025', '966870113', NULL, NULL, 'XERESA VILLAITANA, S.A.', 'EDIF.SARRIA FORUM AVDA. SARRIA 102-106 PLANTA 11', '08017', 'BARCELONA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2887, '2916', 'ARTIÑANO POCHEVILLE HERMANOS, S.L.', 'C/. CLAUDIO COELLO, 22, 1ºC', '28001', 'MADRID', 'MADRID', 'B81014953', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2890, '2917', 'LEF MARKETING & EVENTS', 'MARKT 7', '4527', 'CM AARDENBURG', 'THE NETHERLANDS', 'NL852962289B01', '0031 117 222 010', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2893, '2918', 'FLUGE LEVANTE, SL', 'CALLE JAIME I EL CONQUISTADOR, 94', '46460', 'SILLA', 'VALENCIA', 'B98646680', '630606339 - Carlos', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2896, '2919', 'PRODUCCIONES ARTÍSTICAS HORIZONTE MUSICA, SL', 'C/CAPITÁN AMADOR 9 1º IZQD', '03004', 'ALICANTE', 'ALICANTE', 'B03909264', '618768222-ANDRÉS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2898, '2920', 'PRODUCTION BUREAU', 'MAIN ROAD, SWARDESTON,NORWICH,NORFOLK, NR14 8AD', NULL, NULL, NULL, '765081619', '+44 (0) 788964502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:17', '2026-02-18 13:04:17'),
(2901, '2921', 'FIT FOR WEDDINGS', NULL, NULL, NULL, NULL, '20482312F', NULL, NULL, NULL, NULL, 'ANDREA MARTINEZ TENA', 'AVDA. ESPRONCEDA 20', '12004', 'CASTELLON', 'CASTELLON', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2904, '2922', 'GARRIGUES', NULL, NULL, NULL, NULL, NULL, '93 369 3668', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2907, '2923', 'MIGUEL ULIARTE HERNANDEZ', 'CALLE JIJONA 8, 2ª A', NULL, 'SAN VICENTE DEL RASPEIG', 'ALICANTE', NULL, '696766485', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2909, '2924', 'FORMULA E RACE OPERATIONS LTD', '3 Shortlands 9th floor', 'W6 8DA', NULL, 'LONDON (UK)', 'ESN0077825H', NULL, NULL, NULL, NULL, 'FORMULA E RACE OPERATIONS LTD', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2912, '2925', 'BEATRIZ SANCHEZ ALMANSA', 'C/ DOCTOR FLEMING 38-40', '03560', 'EL CAMPELLO', 'ALICANTE', '03899787E', '625027456', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2915, '2926', 'CASINO MEDITERRÁNEO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2918, '2927', 'SINDIC DE GREUGES COMUNITAT VALENCIANA', 'C/ PASCUAL BLASCO, 1', '03001', 'ALICANTE', 'ALICANTE', 'Q-5350006-B', '965937505', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2920, '2928', 'Ajuntament d´Elx', 'C/. Diagonal del Palau, 1', '03202', 'Elche', 'Alicante', 'P0306500J', '966658000 ext. 2447  //  672685656', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2922, '2929', 'MOTIVATION SERVICES AB', 'VENDEVÄGEN 85ª   P.O. BOX 2009', 'S-18202', 'DANDERYD', 'SWEDEN', 'SE5561939298', NULL, NULL, NULL, NULL, 'MOTIVATION SERVICES LANDGREN AB', 'VENDEVÄGEN 85ª   P.O. BOX 2009', '182 02', 'DANDERYD', 'SWEDEN', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2925, '2930', 'SAB EVENTS EUROPE LTD', '2A THE QUADRANT EPSON', 'KT17 4RH', 'SURREY', 'UK', 'GB 287796719', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2927, '2931', 'OZ MARKETING & COMUNICATION GROUP', 'C/ ALBADALEJO 6-1 - 16', '28037', 'MADRID', NULL, 'B-87116430', NULL, NULL, NULL, NULL, 'OZ MARKETING & COMUNICATION', 'C/ ALBADALEJO 6-1 - 16', '28037', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2929, '2932', 'PRODISA TELEVISION SL', 'CTRA. DE MOTRIL Nº 38', '18620', 'ALHENDIN', 'GRANADA', 'B18425074', '670597733 - 958132269', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2932, '2933', 'RITMOVIL', 'C/ FEDERICO GARCIA LORCA 19', '03550', 'SAN JUAN', 'ALICANTE', 'B-54843289', '629304822', NULL, NULL, NULL, 'NATOYDANI SLU', 'C/ FEDERICO GARCIA LORCA 19', '03550', 'SAN JUAN', 'ALICANTE', NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:18', '2026-02-18 13:04:18'),
(2935, '2934', '*HOTEL SUITOPIA', 'AVDA. EUROPA  2', '03710', 'CALPE', 'ALICANTE', 'B54929138', '865751111 - 965831762', NULL, NULL, NULL, 'SUITOPIA HOTEL S.L.U.', 'AVDA. EUROPA  2', '03710', 'CALPE', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2937, '2935', 'RPA MARKETING Y COMUNICACIÓN', 'CLAUDIO COELLO 41', '28001', 'MADRID', 'MADRID', 'B-82843699', '915781066', NULL, NULL, NULL, 'RPA EVENTS, S.L.', 'CLAUDIO COELLO 41', '28001', 'MADRID', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2940, '2936', 'TERRA COSULTORIA DE INCENTIVOS', 'C/ RIOS ROSAS 44 A -  7º B', '28003', 'MADRID', 'MADRID', NULL, '916028611', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2943, '2937', 'INFORMACIONES DIGITALES Y COMUNICACIÓN, S.L.', 'C/ Urano, 8 Eenteplanta D', '28850', 'Torrejon de Ardoz', 'Madrid', 'B 84277318', '653225758', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2945, '2938', 'PATRICIA REQUENA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2948, '2939', 'M.I.C.E.  BARCELONA (VIAJES EL CORTE INGLES)', 'C/. BOLIVIA 234-236', '08200', 'BARCELONA', 'BARCELONA', 'A-28229813', '993365760', NULL, NULL, NULL, 'VIAJES EL CORTE INGLES', 'AVDA. CANTABRIA Nº 51', '28042', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2950, '2940', 'CUTTING EDGE EVENTS', NULL, NULL, NULL, NULL, NULL, '931910054', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2952, '2941', 'PURATOS', 'CTRA. COMARCAL 63 KM 13,5', '17410', 'SILS', 'GIRONA', 'A08135014', NULL, NULL, NULL, NULL, 'T500 PURATOS, S.A', 'CTRA. COMARCAL 63 KM 13,5', '17410', 'SILS', 'GIRONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2955, '2942', 'AV SERVICIES BARCELONA', 'C/. Pamplona 96-104, local 5', '08018', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2958, '2943', 'KUONI DESTINATION MANAGEMENT SL', 'AVDA. DIAGONAL, 416-3ª1º', '08037', 'BARCELONA', NULL, 'B-84477942', '931512900', NULL, NULL, NULL, 'KUONI DESTINATION MANAGEMENT S.L.', 'C/ JACOMETRO, 15 4ª PLANTA', '28013', 'MADRID', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2959, '2944', 'SOCIEDAD ESPAÑOLA DE SALUD Y MEDICINA INTEGRAL', 'GOYA 7 ENTREPLANTA 1ª', '28001', 'MADRID', 'MADRID', 'G-87548954', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2962, '2945', 'TEODORO NÚÑEZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'teo.nunez@melia.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2965, '2946', 'SARGA SONIDO', 'C/  METGE ANTONIO ANGUIZ , 2 2A 4', '03440', 'IBI', 'ALICANTE', '15424612 F', '615213974', NULL, NULL, NULL, 'JUAN JOSE SANCHIS MARTINEZ', 'C/  METGE ANTONIO ANGUIZ , 2 2A 4', '03440', 'IBI', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:19', '2026-02-18 13:04:19'),
(2967, '2947', 'ITB EVENTS', 'PASAJE INDEPENDENCIA 30, 4-1', '08026', 'BARCELONA', 'BARCELONA', 'B-66520701', '934815859', NULL, NULL, NULL, 'INNOVATIVE TALENT BUREAU SL', 'PASAJE INDEPENDENCIA 30, 4-1', '08026', 'BARCELONA', 'BARCELONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2970, '2948', 'COLEGIO AGUSTINOS ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2973, '2949', 'HOTEL SOL PELÍCANOS OCAS', 'C/ GERONA, Nº 45-47', '03503', 'BENIDORM', 'ALICANTE', 'A78304516', '965852350', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2976, '2950', 'VIVIENDO DEL CUENTO', 'C/. JACINTO LABAILA, 33', NULL, NULL, NULL, NULL, '963290647 // 67037478281', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2978, '2951', 'WTG SPAIN', NULL, NULL, NULL, NULL, NULL, '611058253', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2981, '2952', 'HAVAS LIFE MADRID', NULL, NULL, NULL, NULL, NULL, '913302121', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2984, '2953', 'AG VIAJES 021 CONGRESOS MADRID', 'C/. Alberto Bosch, 13-2 ª planta', '28014', 'Madrid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2987, '2954', 'CONSELLERIA DE HACIENDA Y MODELO ECONOMICO DIR. GRAL. DE FONDOS EUROPEOS', 'Plaza Napoles y Sicilia, 10 - 1º', '46003', 'VALENCIA', 'VALENCIA', 'S4611001A', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2990, '2955', 'A COMPANY MAN ENTERTAIMENT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2992, '2956', 'TED FEST', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2995, '2957', 'VIAJES HISPANIA', 'Avda. Maisonnave, 11 -7º', '03003', 'ALICANTE', 'ALICANTE', 'B-07012107', '965228393 - (965141125 EXPLANADA)', NULL, NULL, NULL, 'AVORIS RETAIL DIVISION S.L', 'C/ ROVER MOTTA 27', '07006', 'PALMA DE MALLORCA', 'PALMA DE MALLORCA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(2998, '2958', 'keyDM', NULL, NULL, NULL, NULL, NULL, '952574053', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(3001, '2959', 'NATURAL MORNING', NULL, NULL, 'SAN VICENTE DEL RASPEIG', 'ALICANTE', NULL, '663217806', NULL, NULL, 'naturalmorningevent@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(3004, '2960', 'CARLSON WAGONLIT ESPAÑA SL', 'C/ JULIAN CAMARILLO Nº 4 EDIFICIO 2', '28037', 'MADRID', NULL, 'B-81861304', NULL, NULL, NULL, NULL, 'CARLSON WAGONLIT ESPAÑA SL', 'C/ JULIAN CAMARILLO Nº 4 EDIFICIO 2', '28037', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:20', '2026-02-18 13:04:20'),
(3006, '2961', 'SOCIEDAD VALENCIANA DE CARDIOLOGÍA', 'AV DE LA PLATA 20', '46013', 'VALENCIA', 'VALENCIA', 'G-46335048', '679548742', NULL, NULL, 'angela.cantos@boehringer-ingelheim.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3009, '2962', 'MARBET', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3012, '2963', 'AZULMARINO PRODUCCIONES S.L.', 'Calle Porción, 7', '28023', 'MADRID', 'MADRID', 'B-83790899', '913729681', NULL, NULL, NULL, 'AZULMARINO PRODUCCIONES S.L.', 'CALLE PROCIÓN 7, PORTAL 1,2ºA. EDIFICIO AMERICA II', '28023', 'MADRID', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3015, '2964', 'ESTEFANIA AGUADO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3017, '2965', 'ATLANT OCEAN RACING SPAIN, SLU', 'Muelle nº 10 de Levante - Puerto de Alicante', '03001', 'Alicante', 'Alicante', 'B-76239177', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3020, '2966', 'ACCEM', 'PLAZA SANTA MARIA SOLEDAD TORRES ACOSTA Nº 2', '28004', 'MADRID', NULL, 'G-79963237', '675892723 - 673369984', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3023, '2967', 'HOTEL ELCHE CENTRO', 'AVDA. JUAN CARLOS I, Nº 5', '03203', NULL, 'ELCHE', 'B54790332', '966610033', NULL, NULL, NULL, 'HOTEL ALADIA SL', 'AVDA. JUAN CARLOS I, Nº 5', '03203', 'ELCHE', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3026, '2968', '*ALICIA BOSCH BALLESTER', 'RUBEN DARIO 38-27', '46021', 'VALENCIA', 'VALENCIA', 'ES73377813Q', '644055262', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3029, '2969', 'ALVAREZ & MARSAL SPAIN, S.L.', 'PASEO DE LA CASTELLANA 95, PANTA 13', '28046', 'MADRID', 'MADRID', 'B-85510840', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3031, '2970', 'IDE_marketing', 'C/. Rios Rosas 47 nave 1C', '28003', 'Madrid', 'Madrid', NULL, '911925180', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3034, '2971', 'SUMA GESTIÓN TRIBUTARIA', 'PLAZA SAN CRISTOBAL 1', '03002', 'ALICANTE', 'ALICANTE', 'P5300003J', '965148563', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3037, '2972', 'OXIGEN SONIDO E ILUMINACION SL', 'POL INDUSTRIAL GARRACHICO, CALLE MARMOL 10', '03112', 'ALICANTE', 'ALICANTE', 'B-54917307', '965656580/693226649 - 664245511 JORGE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3040, '2973', 'HOSBEC', 'PASEO ELS TOLLS, 2 EDIFICIO INVAT-TUR, 3ª PLANTA', '03502', 'BENIDORM', 'ALICANTE', 'G-03270014', '965855516', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:21', '2026-02-18 13:04:21'),
(3043, '2974', 'LIN XIAO', NULL, NULL, NULL, NULL, 'E26922427', '657013587', NULL, NULL, 'linxiao1986@hotmail.com', 'CHEN HONGSONG', 'C/ JOSE ANTONIO CAÑETE 8-3-2', '03202', 'ELCHE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3045, '2975', 'MICE TRAVEL', 'Stortorget 2', '252 23', 'Helsingborg', 'SWEDEN', 'SE556627802301', '+460424499865', NULL, NULL, NULL, 'MICEtravel AB', 'Stortorget 2', '252 23', 'Helsingborg', 'SWEDEN', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3048, '2976', 'ACHM SPAIN MANAGEMENT, S.L.', 'Pso. Club Deportivo, 1 Edif. 17 P. Emp. La Finca', '28223', 'Pozuelo de Alarcon', 'MADRID', 'B-86107406', '916260700', '916260701', NULL, 'achotels@ac-hotels.com', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3051, '2977', 'THE OCEAN RACE 1973, S.L.', 'MUELLE Nº 10 DE LEVANTE', '03001', 'PUERTO DE ALICANTE', NULL, 'B76239177', '966011100966080389', NULL, NULL, 'info@theoceanrace.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3054, '2978', 'GRUPO ASV', NULL, NULL, NULL, NULL, NULL, '965988110  EXT. 59182', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3056, '2979', 'EXPERIENCIAS MPA, S.L.', 'AVENIDA DE LA VICTORIA, 134', '28023', 'EL PLANTIO', 'MADRID', 'B-81752792', 'DAVID - 617428502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3059, '2980', 'GRUPO AC', 'C/ RAFAEL DE LA HOZ ARDERIUS Nº 4 PORTAL 6 - 1 -1', '14006', 'CORDOBA', 'CORDOBA', 'B-14573018', '957497679', NULL, NULL, NULL, 'GESTORA DE VIAJES Y NEGOCIOS, S.L.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3062, '2981', 'COLEGIO OFICIAL DE NOTARIOS VALENCIA', 'CALLE PASCUAL Y GENIS 21 - 2º', '46002', 'VALENCIA', 'VALENCIA', NULL, '963512585', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3065, '2982', '*FACTOR 3 EVENTS', 'C/. Gran Via de les Corts Catalanes, 669 Bis 4º 1ª', '08013', 'Barcelona', 'Barcelona', NULL, '93 2502337', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3068, '2983', 'EVENT.ONE DMC', 'C/ Ruzafa, 28 - 1', '627888801', 'Valencia', 'VALENCIA', NULL, '960046272 /627888801', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3070, '2984', 'CORPORATE TRAVEL MANAGEMENT, Ltd', 'One Carter Lane', 'EC4V 5ER', 'LONDON', NULL, 'GB836195115', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3073, '2985', 'TPC COMUNICACIÓN & EVENTS', 'AVDA. DIAGONAL 309, 1ª PLANTA.', '08013', 'BARCELONA', 'BARCELONA', 'B-64948292', '934585928', NULL, NULL, NULL, 'TPC CORPORATE EVENTS, S.L.', 'AVDA. DIAGONAL 309, 1 A', '08013', NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3076, '2986', 'LG ELECTRONICS SPAIN', 'C/. CHILE 1 KM 24 - A6', '28290', 'LAS ROZAS', 'MADRID', NULL, '912112671', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:22', '2026-02-18 13:04:22'),
(3079, '2987', 'CREANDO ESTRATEGIAS PARA EL ÉXITO, S.L.', 'C/ ALCALDE JOSE LUIS LASSALETTA, 17 DESPACHO 15', '03008', 'ALICANTE', 'ALICANTE', 'B-54867486', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3081, '2988', 'PADRE DIEGO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3084, '2989', 'ENCAR MONTES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3087, '2990', 'IDEASTORM', '28 Rue Paul Bounin, Le Castel Fleuri', '06100', 'Nice', 'FRANCE', 'FR32537669293', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3090, '2991', 'COW EVENTS GROUP', 'C/. PRINCESA, 22 6º-D', '28008', 'MADRID', 'MADRID', NULL, '670393279', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3093, '2992', 'SANITARIA 2000 S.L.', 'RUFINO GONZALEZ 23 BIS Piso 2', '28037', 'MADRID', 'MADRID', 'B82814732', '910685088/663709313', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3095, '2993', 'ASESORES FISCALES DE LA COMUNIDAD VALENCIANA', 'AV/ DEL CID 2 5º B', '46018', 'VALENCIA', 'VALENCIA', NULL, '965131682', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3098, '2994', 'MICE TRAVEL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3101, '2995', 'POMILIO BLUMM S.L', 'EDIFICIO ASV, AV/ JEAN-CLAUDE COMBALDIEU, 5', '03008', 'ALICANTE', 'ALICANTE', NULL, '662697819', NULL, NULL, NULL, 'POMILIO BLUMM S.L.R.', 'VIA VENEZIA 4', '65121', 'PESCARA - ITALIA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3104, '2996', 'EXCMO. AYUNTAMIENTO DE ALICANTE, CONCEJALIA DE DEPORTES', 'C/ FOGUERER ROMEU ZARANDIETA, Nº 2', '03005', 'ALICANTE', 'ALICANTE', 'P-0301400H', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3107, '2997', 'REAL CLUB DE REGATAS DE ALICANTE', 'MUELLE DE PONIENTE, 3', '03001', 'ALICANTE', 'ALICANTE', NULL, '678782241', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3109, '2998', 'LA FRANCO AMERICAN IMAGE', 'RUE DE LA RÉPUBLIQUE, 99', '928000', 'PUTEAUX', NULL, 'FR.26411032105', '+33 (0) 1 41 45 09 55 / +33(0)6 09 45 53 11', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3112, '2999', 'ALO SPAIN CONGRESS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:23', '2026-02-18 13:04:23'),
(3115, '3000', 'MONDLIRONDO', 'PZA. JESÚS, 3', NULL, 'MADRID', NULL, NULL, '650914099', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3118, '3001', 'HILLROM', '130 E Randolph St, Suite 1000', 'IL 60601', 'CHICAGO', NULL, 'ESB62552732', '+13128199359 / +13174379602', NULL, NULL, NULL, 'HILL-ROM IBERIA, S.L.U.', 'Plaza Europa, 9-11 Planta 17 Torre Inbisa', '08908', 'Hospitalet de Llobregat', 'Barcelona', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3120, '3002', 'ALDIMESA', 'AVDA. ALBUFERETA Nº 44', '03016', 'Alicante', 'Alicante', 'A03078672', '965253211 / 686712077', NULL, NULL, NULL, 'ALICANTINA DISTRIBUCIONES MEDICAS, S.A.', 'AVDA. ALBUFERETA Nº 44', '03016', 'ALICANTE', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3123, '3003', 'CASANOVA AGENCY', NULL, NULL, NULL, NULL, NULL, '966675445', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3126, '3004', 'TOPTOUR EUROPE LIMITED', 'UNIT 108, 1 QUALITY COURT, Off Chancery Lane', 'WC2A 1HR', 'LONDON', 'UK', 'GB 446 0312 79', '020-7430-2458', NULL, NULL, NULL, 'TOPTOUR EUROPE LTD', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3129, '3005', 'ALFONSO Y CHRISTIAN S.L.', 'Avda. de Nicaragua, 49   Torre VI A', '03502', 'Benidorm', 'Alicante', 'B-42616797', '609226391', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3133, '3006', 'MICE VENUES', NULL, NULL, NULL, NULL, NULL, '+44 (0) 7899918035', NULL, 'www.micevenues.co.uk', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3135, '3007', 'AV SERVICES BARCELONA', 'CARRER DE PAMPLONA, Nº 104', '08018', 'BARCELONA', 'BARCELONA', 'B-65124083', '935197904 - 649648442', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3137, '3008', 'VIAJES EL CORTE INGLES', 'C/ CASTELAR, 41-43', '39004', 'CANTABRIA', 'SANTANDER', NULL, '942362993', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3140, '3009', 'LABORATORIOS BOEHRINGER', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3144, '3010', 'SCP CREACION Y PRODUCCION DE EVENTOS', 'C/. Del Gas, 4 Pol. San José de Valderas', '28918', 'Leganés', 'Madrid', NULL, '913081840', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3145, '3011', 'AudiovisualesPc', NULL, NULL, NULL, NULL, NULL, '915605930  // 639181668', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3148, '3012', 'D-SIDE GROUP', 'Avenue L. Mommaertslaan , 24', '1140', 'Brussels', 'Belgium', 'BE0472232523', '+32 0 27300645', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:24', '2026-02-18 13:04:24'),
(3151, '3013', 'Vistalia Grupo Óptico S.A.', 'Benicanena 11 Bajo', '46702', 'Gandia', 'Valencia', 'A-97897326', '962966698', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3154, '3014', 'PENGUINS THE EVENT AGENCY', '1 WINDSOR BUSINESS CENTRE VANSITTART', 'SL4 1SP', 'BERKSHIRE', 'WINDSOR', NULL, '+44 (0) 1753839694', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3156, '3015', 'HOTEL SUITOPIA', 'Avda. de Europa, 2', '03710', 'CALPE', 'ALICANTE', 'B-54211834', '865751111/691696021', NULL, NULL, NULL, 'TRADIA HOTEL, S.L.U', 'C/ BENIDORM, 1', '03710', 'CALPE', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3159, '3016', 'HOTEL SOL Y MAR', 'C/ BENIDORM, 1', '03710', 'CALPE', 'ALICANTE', 'B-46077889', '965831762', NULL, 'www.granhotelsol ymar.com', NULL, 'CONSTRUCCIONES Y SERVICIOS SOLYMAR S.L.U.', 'C/ BENIDORM, 1', '03710', 'CALPE', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3162, '3017', 'ESATUR XXI, S.L.', 'C/ ARZOBISPO LOACES, Nº 3', '03003', 'ALICANTE', 'ALICANTE', 'B-53874145', '680816486 - 684416085', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3165, '3018', 'GLAUKOS MEDICAL SPAIN, S.L.', 'RAMBLA CATALUNYA 53, ATICO', '08007', 'BARCELONA', 'BARCELONA', 'B-66932914', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3168, '3019', 'WHITE AND COLORS EVENTS, SL', 'Avda. Puente Cultural, 10 - Bloque A, 4º 9', '28700', 'San Sebastian de los Reyes', 'Madrid', 'B-86522166', '912686795', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3170, '3020', 'JJ Surgical Visión Spain, S.L.', 'Paseo de las Doce Estrellas, 5-7', '28042', 'Madrid', 'Madrid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3173, '3021', 'FUNDACIÓN JORGE ALIÓ PARA LA PREVENCIÓN DE LA CEGUERA', 'C/ CRUZ DE LA PIEDRA 8', '03003', 'ALICANTE', 'ALICANTE', 'G53102950', '965266919', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3176, '3022', 'CRIVELSA', 'PILGONO ARGUALAS, NAVE 31', '50012', 'ZARAGOZA', 'ZARAGOZA', 'A-50060011', '636459386 - 976566677', NULL, NULL, NULL, 'CRIVEL, S.A.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3179, '3023', 'RESTAURANTE ALTRAMUZ', 'AVDA. DE HOLANDA, Nº 18', '03540', 'PLAYA SAN JUAN', 'ALICANTE', NULL, '639654096', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3181, '3024', 'TOPCON', 'C/ FREDERIC MOMPOU Nº 4, ESC. A BAJOS 3', '08960', 'ST. JUST DESVERN', 'BARCELONA', 'A-58637851', NULL, NULL, NULL, NULL, 'TOPCON ESPAÑA, S.A.', NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3184, '3025', 'DIGITAL LOGIC SYSTEM, S.L.', 'C/. Fuenteventura, 4 Pta. Baja', '28703', 'Madrid', 'Madrid', 'B-88162151', '918298137', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3187, '3026', 'BIOCHEMICAL SOCIETY', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:25', '2026-02-18 13:04:25'),
(3190, '3027', 'NESTHOLMA, S.L.', 'C/JEAN CLAUDE COMBALDIE S/N', '03008', 'ALICANTE', 'ALICANTE', 'B-42630319', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3193, '3028', 'RTA GROUP', NULL, NULL, NULL, NULL, NULL, '952376250', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3195, '3030', 'ALICOACH INNOVA, S.L.', 'C/ Maria del Maeztu, 10', '03203', 'Elche', 'Alicante', 'B54923669', '646818700', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3198, '3031', 'AGRUPACIÓN DE INTÉRPRETES DE BARCELONA', NULL, NULL, NULL, NULL, NULL, '935442727', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3201, '3032', 'FOGUERA  PLA DEL BON REPOS-LA GOTETA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3204, '3033', 'CANNIZZO PRODUZIONI SRL', 'Viale Casrso, 57', '00195', 'Roma', 'Italia', NULL, '+39 06 58300214', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3206, '3034', 'TA SPAIN', 'Avda- Enric Granados, 135, 3º 1ª', '08008', 'Barcelona', 'Barcelona', NULL, '933680138    // 674687894', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3209, '3035', 'VIAJES EL CORTE INGLES', 'C/ Alberto Bosch, 13 2ª Planta', '28014', 'Madrid', 'Madrid', 'A-28229813', '911030908', NULL, NULL, NULL, 'VIAJES EL CORTE INGLES', 'AVDA. DE CANTABRIA, Nº 51', '28042', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3212, '3036', 'SALTARINAS, S.L.', 'C/ Cadiz, 86 - P.5 - Pta. 19', '46006', 'Valencia', 'Valencia', 'B98390305', '963341120', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3215, '3037', 'CAROL SÁNCHEZ OPAZO', NULL, NULL, NULL, NULL, NULL, '653571196', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3217, '3038', 'VOALA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3220, '3040', 'AMCOFF ENTERTAINMENT', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3223, '3041', 'INTERNATIONAL OPHTALMOLOGY CONSULTING, S.L.', 'C/ Cruz de la Piedra, 8', '03015', 'Alicante', 'ALICANTE', 'B54265848', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:26', '2026-02-18 13:04:26'),
(3226, '3042', 'HOTEL EUROSTARS CENTRUM ALICANTE', 'C/ Pintor Lorenzo Casanova, 33-35', '03003', 'ALICANTE', 'ALICANTE', 'B66872706', '965988008', NULL, NULL, NULL, 'BALAN HOTELS, S.L.U.', 'Calle Vall D\'Alora, 3', '46015', 'Valencia', 'VALENCIA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3229, '3043', 'VIVIR DE TU PASIÓN, S.L.', 'Carrer del Marge, 1', '03110', 'Mutxamel', 'ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3231, '3044', 'ROLDAN TV', 'Avda. Dr. Severo Ocho 41', '28100', 'ALCOBENDAS', 'MADRID', 'B-86192002', '649305122', NULL, NULL, NULL, 'ROLDANTV COMUNICACIÓN Y SERVICIOS, S.L.', 'Avda. del Doctor Severo Ochoa, 41 C', '28100', 'Alcobendas', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3234, '3045', 'EPIC CREATIVOS', 'Pza. Fuensanta 2,  5 B', '30008', 'MURCIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3237, '3046', 'VIRTUAL LEMON', 'PLAZA AVILÉS, 1 POL. FUENTE DEL JARRO', '46988', 'PATERNA', 'VALENCIA', 'B73930109', '960266266', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3240, '3047', 'MENTALIDAD INVENCIBLE, S.L.', NULL, NULL, NULL, NULL, NULL, '627796091', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3243, '3048', 'HOTELES BARCELÓ LA NUCÍA', 'Partida Buena Vista, 2', '03530', 'La Nucía', 'Alicante', 'B15967110', '966942796// 618554300', NULL, NULL, NULL, 'BARCELÓ ARRENDAMIENTO PENINSULA, S.L.', 'C/ José Rover Motta, 27', '07006', 'MALLORCA', 'ISLAS BALEARES', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3245, '3049', 'TEATRO RÍO', 'Plaza S. Vicente, S/N', '03440', 'IBI', 'ALICANTE', NULL, '965554650', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3248, '3050', 'PROGRESIÓN AUDIOVISUAL', 'C/ Cenicientos, 5 Pol. Ind. Ventorro del Cano', '28925', 'Alcorcon', 'Madrid', 'B86219748', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3251, '3051', 'OHL INGESAN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3254, '3052', 'FICE - Federación of Spanish Footwear Industries', 'C/. Nuñez de Bolboa 116 3-6', '28006', 'Madrid', 'Madrid', 'B78290079', '915627001', NULL, NULL, NULL, 'FICE Servicios, S.L.', 'C/. Nuñez de Bolboa 116  Planta 3ª Oficinas 5 y 6', '28006', 'Madrid', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3256, '3053', 'PANGEA THE TRAVEL STORE', 'HERNÁN CORTÉS, 19', '46004', NULL, 'VALENCIA', NULL, '608865370', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3259, '3054', 'CREATIVEMAS', 'C/. Pintores 2 (P.I. Urtinsa)', '28923', 'Alcorcón', 'Madrid', 'B81560195', '607678707', NULL, NULL, NULL, 'CREATIVE ARTS COMMUNICATION, S.L.', 'C/. Pintores 2 (P.I. Urtinsa)', '28923', 'Alcorcón', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:27', '2026-02-18 13:04:27'),
(3262, '3055', 'VIVO RECUERDO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3265, '3056', 'MEET & FORUM', NULL, NULL, NULL, NULL, NULL, '610566283', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3267, '3057', 'Apples & Pears', NULL, NULL, NULL, NULL, NULL, '662604840', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3270, '3058', 'VIAJES 9PUNTO9 S.L.', 'C/. Nuremberg 9', '28032', 'Madrid', 'Madrid', 'B-86588092', '912189257', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3273, '3059', 'ESCAPE TRAVEL SWEDEN AB', 'Gamla landsvägen, 10', '518 31', 'Sandared', 'SWEDEN', 'SE556739838201', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3276, '3060', 'BLUE CONNECTION DREAM EU CR SL', 'CALLE BAC DE RODA 63 BAJOS', '08005', 'BARCELONA', NULL, 'B67311605', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3279, '3061', 'PEARL STREET', NULL, NULL, NULL, NULL, 'SE556853833301', NULL, NULL, NULL, NULL, 'PARAPLY PRODUKTION AB', 'c/o World Trade Center / The Pot Blekingegatan, 1', '37134', 'Karlskrona', 'SWEDEN', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3281, '3062', 'IMPORTACO CASA PONS', NULL, NULL, NULL, NULL, 'B-98256696', '669965868 - 961223000', NULL, NULL, NULL, 'IMPORTACOFOOD SERVICE SL', 'CALLE 2 Y 6, S/N - POL.IND. DE PICASSENT', '46220', 'PICASSENT', 'VALENCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3284, '3063', 'HOTEL DESIGN EMEA', NULL, NULL, NULL, NULL, 'B-16590531', NULL, NULL, NULL, NULL, 'BUENA VISTA FORUMS S.L', 'SANT AGUSTIN, 19', '07813', 'IBIZA', 'SPAIN', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3287, '3064', 'EXPOSICIONES Y SISTEMAS, S.L.', 'Calle de la Cuesta, 6', '28026', 'MADRID', 'MADRID', 'B78601523', '915608815 / 696926488', '915608718', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3290, '3065', 'MEDITERRA CINEMA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3292, '3066', 'AV INSIGNIA', NULL, NULL, NULL, NULL, '23053360-T', NULL, NULL, NULL, NULL, 'MARIA DE LOS ANGELES NAVARRO MORALES', 'C/ LOMA DEL AIRE, Nº 8', '30394', 'CARTAGENA', 'MURCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3295, '3067', 'BCD TRAVEL', 'C/ ENRIQUE GRANADOS Nº 6', '28224', 'POZUELO DE ALARCON', 'MADRID', 'B57986846', '915425064', NULL, NULL, NULL, 'SEKAI CORPORATE TRAVEL, S.L.U', 'C/ Josep Rover Motta, 27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:28', '2026-02-18 13:04:28'),
(3298, '3068', 'FERNANDO PLAZA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3301, '3069', 'ASSURED FLEET SERVICES, S.L.', 'CARRER DE LA SELVA Nº 18 BJ IZQ', '08820', 'BARCELONA', 'EL PRAT DE LLOBREGAT', 'B-02846558', NULL, NULL, NULL, NULL, 'ASSURED FLEET SERVICES, S.L.', 'FERNANDO EL CATOLICO Nº 63', '28015', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3304, '3070', 'FRESENIUS KABI ESPAÑA, S.A.U.', 'C/. Marina 16-18   Torre Mapfre - Vila Olímpica', '08005', 'Barcelona', NULL, 'B-85376630', '629081075', NULL, NULL, NULL, 'GLOBAL BUSINESS TRAVEL, S.L.', 'C/ ALBASANZ Nº 14, 2ª PLANTA', '28037', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3306, '3071', 'VEGENAT HEALTHCARTE, S.L.', 'AVDA. DE EUROPA 26, EDIFICIO ÁTICA 5, 2º PLANTA', '28224', 'MADRID', 'POZUELO DE ALARCÓN', 'B-87772638', NULL, NULL, NULL, NULL, 'VEGENAT HEALTHCARTE, S.L.', 'CTRA. BADAJOZ-MONTIJO, KM. 24', '06184', 'PUEBLONUEVO DEL GUADIANA', 'BADAJOZ', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3309, '3072', 'SYNCROSFERA', 'Avda. de la Marina Alta, 2', '03750', 'Alicante', 'Pedreguer', NULL, '965648619', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3312, '3073', 'COMUNIDAD DE PROPIETARIOS RESIDENCIAL SIDI', 'C/ CORALES, 1', '03540', 'PLAYA SAN JUAN', 'ALICANTE', 'H-06780688', '965637005', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3315, '3074', 'HOTEL OPERATIONS, S.L.', 'Placeta Castillejos, 4 - 1º C', '18001', 'GRANADA', 'GRANADA', 'B19667302', '971034451', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3317, '3075', 'PRECOR FITNESS S.L.U.', 'P. de Negocios Mas Blau II, Conca de Barberá, 4-6', '08820', 'El Parat de Llobregat', 'BARCELONA', 'B67447524', '932625100 / 669724778', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3320, '3076', 'FORST Escuela Negocios Turisticos', NULL, NULL, NULL, NULL, 'B-54883715', '685750404', NULL, NULL, NULL, 'FORMACIÓN Y TRANSFORMACIÓN, S.L.U', 'CALLE CIUDAD DE TOYOOKA, 3 BAJO', '03005', 'ALICANTE', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29');
INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(3323, '3077', 'HOTEL EUROSTARS LUCENTUM', 'AVDA. ALFONSO X EL SABIO, 11', '03002', 'ALICANTE', 'ALICANTE', 'B-42782961', '966590700', NULL, NULL, NULL, 'HOTEL LUCENTUM ALICANTE SL', 'MALLORCA , 351', '08013', 'BARCELONA', 'BARCELONA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3326, '3078', 'BCD MEETING & EVENTS', 'Avinguda Josep Tarradellas, 20', '08029', 'Barcelona', 'BARCELONA', 'B-57986846', '934563992', NULL, NULL, NULL, 'SEKAI CORPORATE TRAVEL, SLU', 'C/ JOSÉ ROVER MOTTA, 27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3329, '3079', 'HOTEL AJ GRAN ALACANT', 'C/ Clemente Gonzalvez Valls, 38 - Bajo', '03202', 'Elche', 'Alicane', 'A-03350493', 'Fran 603717238 - 965087558', NULL, NULL, NULL, 'NOU MEDITERRANI, S.A', 'C/ Clemente Gonzalvez Valls, 38 - Bajo', '03202', 'Elche', 'Alicane', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3331, '3080', 'HOTEL AC ELDA', 'Avda. de Chapí, 38 Pza. de la Ficia 3', '03600', 'ELDA', 'ALICANTE', 'B-83246439', '966981221', NULL, NULL, NULL, 'HOTEL AC ELDA, SL', 'PASEO CLUB DEPORTIVO Nº 1 EDF. 17', '28223', 'POZUELO ALARCON', 'MADRID', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:29', '2026-02-18 13:04:29'),
(3334, '3081', 'HOTEL ALICANTE GRAN SOL AFFILIATED BY MELIA', 'Rambla de Mendez Nuñez, 3', '03002', 'Alicante', 'Alicante', 'A-78304516', '965203000', NULL, NULL, NULL, 'MELIA HOTELS INTERNACIONAL, S.A.', 'Rambla de Mendez Nuñez, 3', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3337, '3082', 'TALENTUM GROUP', 'Carrer de Muñoz Degrain, 3 7ª', '46003', 'Valencia', 'VALENCIA', NULL, '963912631 - 672340885', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3340, '3083', 'MCI SPAIN & PORTUGAL', 'CALLE TUSET 32, PLANTA 5º', '08006', 'BARCELONA', 'BARCELONA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3343, '3084', 'SCALITY', '11 RUE TRONCHET', '75008', 'PARIS', 'FRANCE', 'FR49512955089', '+1 925 323 3028', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3345, '3085', 'FORMULA E OPERATIONS LTD', '3 Shortlands 9th floor', 'W6 8DA', NULL, 'LONDON(UK)', 'N8268326I', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3348, '3086', 'ALICANTE PLAZA, S.L.', 'Roger de Lauria, 19-2C', '46002', 'VALENCIA', NULL, 'B98841232', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3351, '3087', 'DECEUNINCK-QUICKSTEP', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3354, '3088', 'ANDROMEDA EVENTOS S.L.', 'C/ LONGARES 48', '28022', NULL, 'MADRID', 'B-88169545', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3356, '3089', 'TACO BELL RAMBLA DE ALICANTE', 'C/RAMBLA DE MÉNDEZ NÚÑEZ, 21', '03002', 'ALICANTE', 'ALICANTE', 'B-85325579', '673592599 - 965230270', NULL, NULL, NULL, 'RESATABELL FRANQUICIAS, S.L.', 'CERRO DE LOS GAMOS 1, EDIF 3 -  PLANTA 2', '28224', 'POZUELO DE ALARCÓN', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3359, '3090', 'ROTARY CLUB', 'CALLE LOCUTOR VICENTE HIPÓLITO, 37', '03540', 'ALICANTE', 'ALICANTE', 'G03516390', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3362, '3091', 'GRUPO MITO ESTRATEGIAS, S.L.U.', 'C/ ALEMANIA 17 - 3º C', '03003', 'ALICANTE', 'ALICANTE', 'B54896923', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3365, '3092', 'PORT HOTELS', 'Avda. Estocolmo, 4', '03503', 'Benidorm', 'ALICANTE', NULL, '965867863/645076419', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3367, '3093', 'TRANSVIA', 'C/ San Fernando, 5', '03002', 'Alicante', 'ALICANTE', 'B-46178364', '965143950', NULL, NULL, NULL, 'VIAJES TRANSVIA TOURS, S.L.', 'GRAN VIA RAMÓN Y CAJAL,17', '46007', 'VALENCIA', 'VALENCIA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3370, '3094', 'UMIVALE', 'Avda. Real Monasterio de Poblet, 20', '46930', 'Quart de Poblet', 'Valencia', 'A-84523505', '963181191', NULL, NULL, NULL, 'IAG7 VIAJES, S.A.', 'C/ DOCTOR ESQUERDO, 136 PLANTA 7', '28007', 'MADRID', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:30', '2026-02-18 13:04:30'),
(3373, '3095', 'INCONF Ltd', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3376, '3096', 'BIOGEN PHARMA', 'C/ VÍA DE LOS POBLADOS 13 -EDIF. MILENIUM - PLTA 3', '28033', NULL, 'MADRID', NULL, '913277710', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3379, '3097', 'BCD MEETING & EVENTS', 'Calle Enrique Granados, 6 Ed. A', '28224', 'Pozuelo de Alacrcón', 'Madrid', 'B07012107', NULL, NULL, NULL, NULL, 'ÁVORIS RETAIL DIVISION, S.L.', 'C/ JOSÉ ROVER MOTTA, 27', '07006', 'PALMA DE MALLORCA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3381, '3098', 'GASTROMEDIOS', 'PORTAL DE ELCHE 6 1', '03001', 'ALICANTE', NULL, '44762720-M', NULL, NULL, NULL, NULL, 'DESEADA PACHECO DÍEZ', 'PORTAL DE ELCHE 6 1', '03001', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3384, '3099', 'ANDROMEDA VIAJES', 'Avda. Diagonal 618, 4-E', '08021', 'BARCELONA', 'BARCELONA', NULL, '932406153', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 10.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3387, '3100', 'ASOCIACIÓN EPSPAÑOLA DE CONTACTOLOGÍA Y SUPERFICIE OCULAR', 'C/ Alcalde Sainz de Baranda, 29', '28009', 'Madrid', 'MADRID', 'G-88004064', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3390, '3101', 'KOLOKIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3392, '3102', 'JAÉN CONGRESOS', 'C/ NAVAS DE TOLOSA, 4 LOCAL 28 PASAJE LIS PALACE', '23001', 'JAÉN', NULL, 'B23443450', '685899886', NULL, NULL, NULL, 'IMPLANT VIAJES, S.L.', 'C/ NAVAS DE TOLOSA, 4 LOCAL 28 PASAJE LIS PALACE', '23001', 'JAÉN', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3395, '3103', 'PATTERSON_TRAVEL', 'C/. Álaba, 61 4º 1ª', '08005', 'Barcelona', 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3398, '3104', 'ENEAS CONSULTORES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3401, '3105', 'J&H SOLUCIONES DE CAMPO S.L.U.', 'C/ PINTOR EL GRECO 4 12', '03110', 'MUTXAMEL', 'ALICANTE', 'B-16824047', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 60.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3404, '3106', 'SYTELMEDIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3406, '3107', 'GRUPO MARCOS', 'CTRA. DE ALICANTE - MURCIA KM26', '03300', 'ORIHUELA', 'ALICANTE', 'B-03071164', NULL, NULL, NULL, NULL, 'GM NEOLOGIC S.L.U.', 'CTRA. DE ALICANTE - MURCIA KM26', '03300', 'ORIHUELA', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:31', '2026-02-18 13:04:31'),
(3409, '3108', 'IDOIA ELOSUA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3412, '3109', 'THE BUTTERFLIES HEALTHCARE', 'PJE. CARSI 13 BIS', '08025', 'BARCELONA', 'BARCELONA', 'B- 63216972', '626806639', NULL, NULL, NULL, 'THE BUTTERFLIES ADVERTISING, S.L.', 'C/ PASAJE CARSÍ, 13 BIS', '08025', 'BARCELONA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3415, '3110', 'INTEGRITY EVENTS', '93 GEORGE STREET', 'EH3ES', 'EDINBURGH', 'UNITED KINGDOM', 'GB 328 5212 13', '++44(0)7764857714', NULL, NULL, NULL, 'INTEGRITY CORPORATE EVENTS LTD', '93 GEORGE STREET', 'EH3ES', 'EDINBURGH', 'UNITED KINGDOM', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3417, '3111', 'HERMES GROUP AB', 'Gallringssvägen 15 Bro', '19736', 'Sweden', NULL, 'SE556979608801', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3420, '3112', 'ZOE-T', 'C/ Traginers nave 36 Pol. Ind. L\'Horta Vella', '46980', 'PATERNA', 'VALENCIA', 'B98844772', '961937045', NULL, NULL, NULL, 'SPG COSMETICS, S.L.', 'C/ Traginers NAVE 36 - POL. IND. L\'HORTA VELLA', '46117', 'BÉTERA', 'VALENCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3423, '3113', 'ALO CONGRESS', 'c/.Numancia, 73', '08029', 'Barcelona', NULL, NULL, '670010368', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3426, '3114', 'APQ STAGE IBERICA SL', 'C/ ELS PORTS, 9 -  P.IND. BOVALAR', '46970', 'ALAQUÁS', 'VALENCIA', 'B-97353650', '961578002 - 617431553', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3429, '3115', 'SPICE UP', NULL, NULL, NULL, NULL, NULL, '+33497211217 / +33624175756', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3431, '3116', 'UNIVERSIDAD INTERNACIONAL DE VALENCIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3434, '3117', 'QUALITY TRAVEL, S.L.', 'AVDA. DE CORDOBA Nº 9 - ESCALERA B - 1º D', '28026', 'MADRID', 'MADRID', 'B-83215996', '915003710 - 609204280', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3437, '3118', 'RELEVANCIA, S. COOP.', 'C/. Talleres, 5', '30800', 'Lorca', 'Murcia', 'F-05566617', '608132314', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3440, '3119', 'LUDIK', NULL, NULL, NULL, NULL, NULL, '695905138', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3442, '3120', 'ALTRIAM MEDIA&EVENTS, S.L.', 'C/. Pare Tomás de Montaña, 22 1-D', '46023', 'Valencia', 'Valencia', NULL, '629709630', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:32', '2026-02-18 13:04:32'),
(3445, '3121', 'AMERICAN EXPRESS GLOBAL BUSINESS TRAVEL', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3448, '3122', 'JAUSAS LEGAL AND FISCAL S.L.P.', 'Paseo de Gracia, 103 - 7ª Planta', '08008', 'Barcelona', 'BARCELONA', 'B61466868', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3451, '3123', 'NOU MEDITERRANI', NULL, NULL, NULL, NULL, 'B54776331', NULL, NULL, NULL, NULL, 'MAISOGESTION, S.L.', 'Vicente Clavel Florentino, 13', '03203', 'Elche', 'ALICANTE', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3453, '3124', 'NUOVA, S.A. (HOTEL DORMIR DE CINE ALICANTE)', 'C/ Principe de Vergara, 87', '28006', 'MADRID', 'MADRID', 'A28674265', '665066494', NULL, NULL, NULL, 'NUOVA, S.A.', 'C/ Principe de Vergara, 87', '28006', 'MADRID', 'MADRID', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3456, '3125', 'SWISSLENS, S.A.', 'Ch. des Creuses, 9', '1008', 'Prilly - Lausanne (CH)', NULL, 'CHE-108.645.810', '919012113', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3459, '3126', 'ANTONIO SERRANO AZNAR, S.L.', 'Vicente Clavel Florentino, 13', '03203', 'Elche', 'ALICANTE', 'B53839304', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3462, '3127', 'ACTIVOS CONCURSALES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3464, '3128', 'CRAMBO ALQUILERES, S.L.', 'Parque Empresarial Tactica', '46980', 'Paterna', 'Valencia', 'B82408428', NULL, NULL, NULL, NULL, 'CRAMBO ALQUILERES, S.L.', 'C/ Verano, 34-36', '28850', 'Torrejon de Ardoz', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3467, '3129', 'HOYA LENS IBERIA, SAU', 'Paseo de las Flores, 23', '28823', 'Coslada', 'Madrid', 'A28530467', '626269347', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3470, '3130', 'SERCOTEL AMISTAD MURCIA', 'C/. Condestable, 1', '30009', NULL, 'MURCIA', 'B-63566244', '968282929', NULL, NULL, NULL, 'INVERSIONES NARON 2003, S.L.U', 'CALLE PARIS Nº 120', '08036', 'BARCELONA', 'BARCELONA', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3473, '3131', 'AMAZON BUSSSINES', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3476, '3132', 'HOSPITAL MORALES MESEGUER', 'Avda. Teniente Montesinos, 8 Edf. A-7ª Pl.', '30100', 'MURCIA', 'MURCIA', 'B73829632', '968969340', NULL, NULL, NULL, 'GADE EVENTOS, S.L.', 'Avda. Teniente Montesinos, 8 Edf. A-7ª Pl.', '30100', 'MURCIA', 'MURCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3477, '3133', 'TRIDIOM', 'C/. Del Principe, 12 3º C', '28012', 'Madrid', NULL, NULL, '915230258', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:33', '2026-02-18 13:04:33'),
(3478, '3134', 'INSTITUTO NEUROCIENCIAS (UMH-CSIC)', 'Avda. Ramón y Cajal, s/n', '03550', 'San Juan', 'Alicante', NULL, '965919527', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3481, '3135', 'ALGODONES', NULL, NULL, NULL, NULL, NULL, '663451502', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3484, '3136', 'FUNDACION PARA LA FORMACIÓN E INVESTIGACIÓN SANITARIAS DE LA REGIÓN DE MURCIA', 'Pabellón Docente del Hospital Clínico', '30120', 'Universitario Virgen de la Arrixaca, 3ª', 'Murcia', 'G73338857', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3487, '3137', 'RGB-AV AUIDIOVISUALES , S.L.', 'Avda. Cortes Valencianas, 52', '46015', 'Valencia', 'Valencia', 'B98150329', '638156027', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3489, '3138', 'INFO WORLD WHITE WEB, S.L.', 'Avda. Primado Reig, 32', '46009', 'Valencia', NULL, 'B 97137129', '620247359', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3492, '3139', 'CAXTON MANOR LIMITED', '25 Wilton Road', 'SW1V 1LW', 'VICTORIA', 'LONDON', 'GB242020075', NULL, NULL, NULL, NULL, 'ARUK GLOBAL Ltd', '25 Wilton Road', 'SW1V 1LW', 'VICTORIA', 'LONDON', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3495, '3140', 'BENIDORM DMCEVENTS, S.L.', 'AVDA. Aigüera, 3 Ed. Atrium Plaza, Oficina 1', '03501', 'Benidorm', 'ALICANTE', 'B42622977', '966831770', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3498, '3141', 'HEATHER ROBINSON Ltd', 'Minerva Mill Station Road', 'B49 5ET', 'Alcester', 'REINO UNIDO', 'GB971485391', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3500, '3142', 'Healthech HTBA Holding, S.L.', 'Avinguda Diagonal, 567 Planta 4', '08029', 'Barcelona', 'BARCELONA', 'B88441282', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3503, '3143', 'LASER AUDIOVISUALES, S.L.', 'C/ Fernando Mugica, 13 Pabellón 5 - Planta 1', '20018', 'Donostia', 'San Sebastian', 'B75053470', '670492404', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3506, '3144', 'ONSITE DMC', 'Rambla Catalunya 92, 6-30', '08008', 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3509, '3145', 'GO GROUP SERVICIOS INTEGRALES DE MARKETING, S.L.', NULL, NULL, NULL, NULL, NULL, '605179101', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3511, '3146', 'HOTEL ALICANTE GOLF', 'Calle Escultor José Gutiérrez, 23', '03540', 'Alicante', 'Alicante', NULL, '965235000 /692125287', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:34', '2026-02-18 13:04:34'),
(3514, '3147', 'MEDLINE INTERNATIONAL GERMANY GmbH', 'Medline-Strasse 1-3', '47533', 'Kleve', 'Germany', 'ESN0038048E', '+49282175107643', NULL, NULL, NULL, 'MEDLINE INTERNATIONAL B.V.', 'Nieuwe Stationsstraat 10', '6811', 'KS Arnhem', 'The Netherlands', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3517, '3148', 'L\'AGENCE OUATE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3520, '3149', 'ATAMA ESTRATEGIA RESPONSABLE, S.L.', 'Roda Vall d\'Uixó. 125', '03206', 'Elche', 'Alicante', 'B54950001', '619268536', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3522, '3150', 'MEDIABLENDING AUDIOVISUALES, S.L.U.', 'C/. De la Senyera, nº 17', '49183', 'L\'Eiana', 'Valencia', NULL, '649494825', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3525, '3151', 'FERVOR ESTUDIO', NULL, NULL, NULL, NULL, NULL, '623248791', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3527, '3152', 'NECOMPLUS, S.L.', 'Avda. Dr. Jimenez Díaz, 18 1ª Planta Box 1-2', '03005', 'Alicante', 'ALICANTE', 'B53900099', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3530, '3153', 'MCV COMMUNICATIONS DE EVENTS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3533, '3154', 'THE LIME GROUP IN SPAIN, S.L.U.', 'Acera del Darro, 10 - 3ª', '18005', 'Granada', NULL, 'B18961243', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3536, '3155', 'RTA SPANISH EVENTS & INCENTIVES ORGANIZERS', 'C/ Francia, Local 18', '29620', 'TORREMOLINOS', 'MALAGA', 'B92628866', '67625518', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3539, '3156', 'ASTRA ZENECA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3541, '3157', 'L\'ACUITÉ LU, S.L.U.', 'Avenida de la industria, 8', '28108', 'Alcobendas', 'MADRID', 'B88365515', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3544, '3158', 'WEBPOINT SISTEMAS SL', 'Travessera Bovalar, 55', '46970', 'Alaquas', 'Valencia', 'B97357412', '961115661', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3547, '3159', 'HOTEL PORT ALICANTE CITY & BEACH', 'Av de Cataluña, 20, 03540 Alicante', '03540', 'PLAYA SAN JUAN', 'ALICANTE', NULL, '607563037', NULL, NULL, 'caminohp@porthotels.es', NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3550, '3160', 'ESCAPE TRAVEL SWEDEN AB', 'GAMLA LANDSVÄGEN 10', '518 31', 'SANDARED', 'BORLÄNGE - SUECIA', 'SE556739838201', '+46(0)730469584    +46(0)33-258850', NULL, NULL, 'johan.dahlstrand@escapetravel.se', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:35', '2026-02-18 13:04:35'),
(3552, '3161', 'RAYNER SURGICAL, S.L.', 'Paseo de la Castellana, 200 - 7ª P - oficina 703', '28046', 'Madrid', 'MADRID', 'B87630547', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3555, '3162', 'NEGRO Y AMARILLO EVENTS, S.L.', 'GV Marques del Turia, 49 1 1', '46005', 'Valencia', 'VALENCIA', 'B98808919', '663281943', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3558, '3163', 'UNITEC Ibérica S.L.', 'C/. Amistad, Par. 19, Mod A-2 Pol. Ind. Oeste', '30169', 'San Ginés', 'Murcia', 'B30551543', NULL, NULL, NULL, NULL, 'Sistemas Hortofruticolas Unitec Iberia, S.L.', 'C/ Amistad, Parc. 19, Mod. A-2 Pol. Ind. Oeste', '30169', 'San Ginés', 'Murcia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3561, '3164', 'AUDIO-NET ALQUILER PROFESIONAL, S.L.', 'C/Foia, 4 Pol. Ind. Xara', '46680', 'Algemesi', 'VALENCIA', NULL, '962423900', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3563, '3165', 'EVA BOLAÑOS', NULL, NULL, 'ALICANTE', 'ALICANTE', NULL, '620291713', NULL, NULL, 'evabolanyos@msn.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3566, '3166', 'AGESMER, S.l.', 'Av. Jean Claude Combaldieu, 5', '03008', 'Alicante', NULL, 'B-29709573', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3569, '3167', 'SERVIASV SERVICIOS COMPARTIDOS, S.L.', 'Av. Jean Claude Combaldieu, 5', '03008', 'Alicante', NULL, 'B-54888276', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3572, '3168', 'FUNSURESTE, S.L.', 'Av. Jean Claude Combaldieu, 5', '03008', 'Alicante', NULL, 'B30134639', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3575, '3169', 'MERIDIANO, S.A. CÍA. ESPAÑOLA DE SEGUROS', 'Av. Jean Claude Combaldieu, 5', '03008', 'Alicante', NULL, 'A-18006296', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3577, '3170', 'ASV FUNESER, S.L.U.', 'Av. Jean Claude Combaldieu, 5', '03008', 'Alicante', NULL, 'B-54305578', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3580, '3171', 'BOSTON SCIENTIFIC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3583, '3172', 'SWEET ANGELL', NULL, NULL, NULL, NULL, '48721523W', '635096657', NULL, 'www.sweetangellwp.com', 'sweetangelevents@gmail.com', 'CLAUDIA MARTINELLI MARTINEZ', 'C/ SAN BARTOLOME, 5 -2º', '03560', 'EL CAMPELLO', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3587, '3173', 'EL CÍRCULO DIRECTIVOS ALICANTE', 'Paseo de Campoamor s/n.', '03010', 'ALICANTE', 'ALICANTE', '53194098G', '650447853', NULL, NULL, 'direccion@opendir.es', 'EL CIRCULO DIRECTIVOS ALICANTE', 'Avda. Perfecto Palacio de la fuente, 6', '03003', 'Alicante', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:36', '2026-02-18 13:04:36'),
(3589, '3174', 'CWT ITALIA, S.R.L.', 'Via S. Cannizazzaro, 83', '00156', 'Roma', 'ITALIA', 'PI01325201000', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3591, '3175', 'PRODUCCIÓN TÉCNICA AUDIOVISUAL PARA ESPECTÁCULOS MULTI-FORMATO', 'C/ NOU DE LA RAMBLA, 141 PRINCIPAL 2', '08004', 'BARCELONA', 'BARCELONA', '45100280V', '644558106', NULL, NULL, NULL, 'PENÉLOPE HUETO RICO', 'C/ NOU DE LA RAMBLA, 141 PRINCIPAL 2', '08004', 'BARCELONA', 'BARCELONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3594, '3176', 'ALPHYR SAS', '5-7 BOULEVARD VICTOR HUGO', '92110', 'CLICHY', 'FRANCIA', 'FR07512098468', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3597, '3177', 'SOUND & SYSTEMS AUDIOVISUALES, S.L.', 'C/. Concertista Gil Orozco, 14 Bjo.', '46340', 'Requena', 'Valencia', 'B-98125669', '962301110 -  622622037', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3601, '3178', 'GRUPO PACIFICO INTERNATIONAL DMC', NULL, NULL, NULL, NULL, NULL, '932388777', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3603, '3179', 'OMAYRA SEVA', NULL, NULL, NULL, NULL, NULL, '675665327', NULL, NULL, 'omayrasg90@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3605, '3180', 'LED COM SISTEMAS ELECTRÓNICOS', 'CALLE PALANGRE 13', '03540', 'ALICANTE', 'ALICANTE', '48574442Y', '965264886/605274284', NULL, NULL, 'ignacio@ledcom.es', 'IGNACIO MARTÍNEZ LORENTE', 'CALLE PALANGRE 13', '03540', 'ALICANTE', 'ALICANTE', NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3608, '3181', 'IG FORMACIÓN', 'C/ENRIQUETA ORTEGA, Nº17 BAJO', '03005', 'ALICANTE', 'ALICANTE', 'B54886163', '965120039', NULL, 'http://www.igformacion.com', 'sergio@igformacion.com', 'INGEST LEVANTE S.L', 'Calle Enriqueta Ortega 17 bajo', '03005', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3611, '3182', 'HOTEL MERCURE BENIDORM', 'Avda. Panamá 5', '03052', 'Benidorm', 'Alicante', 'A03028321', NULL, NULL, NULL, NULL, 'MARACAIBO S.A', 'AVENIDA PANAMA, 5', '03052', 'BENIDORM', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3614, '3183', 'GREENER', 'Pl. del Nord, 12-13 Bajo Esquina', '08024', 'Barcelona', 'BARCELONA', 'A64652639', '678424123', NULL, NULL, NULL, 'XAGA CREATIVIDAD INTERACTIVA, S.A.', 'Pl. del Nord, 12-13 Bajo Esquina', '08024', 'Barcelona', 'BARCELONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3616, '3184', 'HOTEL DANIYA ALICANTE', 'AVDA DENIA 133', '03015', 'ALICANTE', 'ALICANTE', 'B42656041', '635490511', NULL, NULL, 'comercial@alicante.daniyahotels.es', 'TINOCO URBANO S.L', 'AVDA DENIA 133', '03015', 'ALICANTE', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3619, '3185', 'ATLANTA', 'Calvet 55', '08021', 'Barcelona', 'Barcelona', NULL, '933672422', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3622, '3186', 'COLEGIO PSICÓLOGOS REGIÓN DE MURCIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:37', '2026-02-18 13:04:37'),
(3625, '3187', 'THE KWAN CONCEPT, S.L.', 'C/ DÉU I MATA 62-68 ENTL.B', '08029', 'BARCELONA', 'BARCELONA', 'B66728775', NULL, NULL, NULL, 'qsans@thekwanconcept.com', 'THE KWAN CONCEPT, S.L.', 'C/ DÉU I MATA 62-68 ENTL.B', '08029', 'BARCELONA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3628, '3188', 'FIRST EVENT', NULL, NULL, NULL, NULL, NULL, '0788837007463', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3630, '3189', 'JB PRODUCTIONS', '20 Avenue  Victor Hugo', '75116', 'Paris', 'France', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3633, '3190', 'INVICTUS-CORPORATE', 'Boulevard Voltaire, 226', '75011', 'Paris', NULL, 'FR03422650531', '+33(0)637773960', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3636, '3191', 'AG VIAJES 1511 GRUPO 4 (VIAJES EL CORTE INGLES)', 'C/. San Severo, 10 Planta Baja', '28042', 'Madrid', NULL, 'A28229813', '91103908', NULL, NULL, NULL, 'VIAJES EL CORTE INGLÉS, S.A.', 'Avda, Cantabria,  51', '28042', 'Madrid', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3639, '3192', 'GSS POWER SL', 'APARTADO DE CORREOS 244', '46650', 'CANALS', 'VALENCIA', 'B72921836', NULL, NULL, NULL, 'gsspowersl@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3641, '3193', 'HELL PRODUKTION', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3644, '3194', 'JULIA NAVARRO ROPERO', 'C/. Auditórium 2 -5º f', '3008', 'Murcia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3647, '3195', 'MOMAP STUDIO SAFOR S.L.', 'Passeig Germanies, 96', '46702', 'Gandia', 'VALENCIA', 'B97581425', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 30.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3650, '3196', 'BEON EVENTS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3652, '3197', 'GREEN LIGHT CONSULTING AND SERVICES', 'C/. Pollensa 2 Oficina 6', '28230', 'Madrid', NULL, NULL, '916266621 - 664364919', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3655, '3198', 'COLEGIO OFICIAL PROTÉSICOS DE ALICANTE', 'C/ SAN CARLOS 134, BAJO', '03013', 'ALICANTE', 'ALICANTE', 'Q0300581F', '965146937', NULL, 'www.coprada.es', 'info@coproda.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3658, '3199', 'AMERICAN EXPRESS GLOBAL BUSINESS TRAVEL', NULL, NULL, 'MUNICH GERMANY', NULL, NULL, '49 89 35646403', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:38', '2026-02-18 13:04:38'),
(3661, '3200', 'JAMEPASA RENAULT', 'Avda. de Denia 117', '03015', 'Alicante', NULL, NULL, '965223934', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3664, '3201', 'SOLEAR EVENTS', 'C/. Marq Ahumada Nº 11,3-C', '28028', NULL, 'Madrid', NULL, '910 48 62 84  // 695 99 25 58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3666, '3202', 'AMIGOS DE UCRANIA ASOCIACIÓN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3669, '3203', 'ASOCIACIÓN VALENCIANA DE INFORMÁTICA SANITARIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3672, '3204', 'SARA ZEREG', NULL, NULL, NULL, NULL, '55154702-N', '604220203', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3675, '3205', 'GALAN&ASOCIADOS', NULL, NULL, NULL, NULL, NULL, '965920877', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3678, '3206', 'VALORACIONES MEDITERRÁNEO S.L.', 'Urbanización Barrina Norte, 36', NULL, 'Benidorm', NULL, NULL, '900420200 - 649425195', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3681, '3207', 'BOEHRINGER INGELHEIM FARMAPRESCRIPCIÓN', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3683, '3208', 'SKI UNLIMITED', NULL, NULL, NULL, NULL, NULL, '+46(0)70-9450889', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3686, '3209', 'BCD MEETING & EVENTS', 'C/ Enrique Granados, 6 Edificio A', '28224', 'Pozuelo de Alarcon', 'Madrid', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3689, '3210', 'EUROMOTIVATION', NULL, NULL, NULL, NULL, NULL, '971757291', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3691, '3211', 'ASTANA PRO TEAM', NULL, NULL, NULL, NULL, 'LU23898116', NULL, NULL, NULL, NULL, 'ASTANA QAZAGSTAN TEAM, S.A.', '5, RUE GOETHE', 'L-1637', 'LUXEMBOURG', 'LUXEMBOURG', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3694, '3213', 'NEOKONCEPTS DMD', NULL, NULL, NULL, NULL, NULL, '952838664', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:39', '2026-02-18 13:04:39'),
(3697, '3214', 'Cynthia Doboová', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3700, '3215', 'PREFERENCE EVENTS', '10 Rue Jean-Baptiste Say', '75009', 'Paris', 'FRANCE', 'FR75497770750', '33177379384', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3703, '3216', 'EMIRAL CONGRESS BUREAU, S.L.', 'Avda. Gran Capitán 46, 4ª oficina 1', '14006', 'Córdoba', NULL, NULL, '957080733', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3705, '3217', 'SCHEIDT & BACHMANN IBERICA, S.L.U.', 'Avda. de  Valdelaparra, 39 Nave C', '28108', 'Alcobendas', 'Madrid', 'B82511700', '914841031     639772136', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3708, '3218', 'ESTHER RODRIGUEZ', NULL, NULL, NULL, NULL, NULL, '647591723', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3711, '3219', 'LABORATORIO OCULUS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3714, '3220', 'TEAM GROUP CONSULTORS TURISTICS, S.L.', 'Gran Vía de les Cortes Catalanes, 630, 4 PL', '08007', 'Barcelolna', 'BARCELONA', 'B63732747', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3716, '3221', 'COCA-COLA EUROPACIFICS PARTNERS', 'C/ Ribera de Loira, 20-22', '28042', 'Madrid', 'Madrid', NULL, '650610051', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3719, '3222', 'DISSIMILITY COMUNICACIÓN, S.L.', 'C/ Josefa Valcárcel, 3 2º A', '28027', 'Madrid', 'MADRID', 'B86934015', '677577910', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3722, '3223', 'NEXUS AGENCY NEWCO, S.L.', 'C/Antigua Senda de Senet, 3 Oficina 2', '46023', 'VALENCIA', NULL, 'B98033673', '963580542', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3725, '3224', 'PULITEC LIMPIEZAS E HIGIENE, S.L.', 'CALLE PORTA DE LA MORERA, 33 - 3º', '03203', 'ELCHE', 'ALICANTE', 'B42523670', '966632079', NULL, 'www.acuamar.es', 'info@acuamar.es', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3728, '3225', 'DDMC EVENT DESIGN NV/SA', 'Oude Vijversstraat, 55 Rue des Anciens Etangs', 'B-1190', 'BRUSSELS', NULL, 'BE 0439 572 722', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3730, '3226', 'SOUL EVENTS GROUP, S.L.', 'C/. Plata n 35 Pol. Ind. La Ermita', '29603', 'Málaga', 'Málaga', 'B93068484', '952821978', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3733, '3227', 'SILE CONSULTORIA DE EVENTOS, S.L.', 'Avda. Jaime I, 10, 4º Izda.', '03550', 'SAN JUAN PUEBLO', 'Alicante', 'B16680431', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:40', '2026-02-18 13:04:40'),
(3736, '3228', 'EQUIP D INTERPRETS, S.L.U.', 'Pl. Soler i Carnbonell 18, 5-1', '08800', 'Vilanova I la Geltrú', 'BARCELONA', 'B64142904', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3739, '3229', 'SPAIN FOR EVENTS MEETINGS & INCENTIVES', NULL, NULL, NULL, NULL, 'B44734135', '607755425', NULL, NULL, NULL, 'SPAIN FOR EVENTS GRABIT, S.L.', 'Calle Salvador Rueda, 6 C2', '29630', 'Benalmádena-Costa', 'MALAGA', NULL, 15.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3742, '3230', 'MAXIMICE EVENTS GROUP, S.L.', 'C/. Gremi d\'Hortelans, 11 Planta 3, Ofc. 9-10-11', '07009', 'Palma de Mallorca', NULL, 'ES B57886186', '618229313', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3744, '3231', 'MC GESTION', 'C/. Rioja n 5,Etlo. Pta. 10', '03501', 'Benidorm', 'Alicante', 'H-06964019', '966808733', NULL, NULL, NULL, 'COMUNIDAD DE PRO. SEASCAPE RESORT', 'C.COLOMBIA Nº7', '03509', 'FINESTRAT', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3747, '3232', 'SPANISH INSTITUTE OF LIFESTYLE MEDICINE', NULL, NULL, 'MADRID', 'MADRID', NULL, '655578821', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3750, '3233', 'L\'OCCITANE INTERNATIONAL SUISSE S.A.', 'Chemin du Pre fleuri 5', '1228', 'Plan Les Ouates', 'SWITZERLAND', 'CHE 355 438 577', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3753, '3234', 'STANDFY', 'Plaça de la Constitució', '03410', 'Biar', 'Alicante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3755, '3235', 'EGG EVENTS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3758, '3236', 'MICE TRAVEL CONSULTING', 'C/CORAZÓN DE MARÍA, 64', '28002', 'Madrid', 'MADRID', 'B42956946', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3761, '3237', 'BAKER TILLY IBERIA, S.L.', 'Paseo de la Castellana, 137 4ª planta', '28046', 'Madrid', NULL, 'B08749152', '913650542', NULL, NULL, NULL, 'BAKER TILLY IBERIA, S.L.', 'Av. Josep Tarradellas, 123 - 9ª planta', '08029', 'Barcelona', 'BARCELONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3764, '3238', 'AMEX GLOBAL BUSINESS TRAVEL', 'C/ Vía de los Poblados, 1 Bloque D, Planta 6 Ed. A', '28033', 'MADRID', 'MADRID', 'B85376630', NULL, NULL, NULL, NULL, 'GLOBAL BUSSINESS TRAVEL SPAIN, S.L.', 'C/ Vía de los Poblados, 1 Bloque D, Planta 6 Ed. A', '28033', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3767, '3239', 'COSTA BLANCA DMC', 'Ptda. Caus Vells, 23', '03749', 'Denia', 'ALICANTE', '12480567P', NULL, NULL, NULL, NULL, 'MIHAELA MURESAN POPA', 'Ptda. Caus Vells, 23', '03749', 'Denia', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3769, '3240', 'B MICE EVENTS', 'Av. Comunidad Valenciana, 7 Local 7', '03503', 'Benidrom', 'ALICANTE', 'E44899649', '636606688', NULL, NULL, NULL, 'BMICE OE', 'Av. Comunidad Valenciana, 7 Local 7', '03503', 'Benidrom', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:41', '2026-02-18 13:04:41'),
(3772, '3242', 'EVENTOPIA LIVE, S.L.', 'C/. Gomera 12, 2 C', '28703', 'San Sebastian de los Reyes', 'Madrid', 'B56206436', '663203419', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3775, '3243', 'WOLF COMUNICACIÓN', 'Ayala, 83', '28006', 'Madrid', 'MADRID', 'A81821928', '911590491', NULL, NULL, NULL, 'VIAJES PRESSTOUR ESPAÑA, S.A.', 'Ayala, 83', '28006', 'Madrid', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3778, '3244', 'GRUPO CASA VERDE', NULL, NULL, NULL, NULL, NULL, '689788411', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3780, '3245', 'NIPPON GASES EURO-HOLDING S.L.U', 'C/. Orense 11', '28020', 'Madrid', NULL, NULL, '682604529', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3783, '3246', 'OCCIDENTAL PUEBLO ACANTILADO', NULL, NULL, 'CAMPELLO', 'ALICANTE', 'B15967110', NULL, NULL, NULL, 'comercial3.alicante@barcelo.com', 'BARCELÓ ARRENDAMIENTOS PENINSULA S.L.', 'Calle José Rover Motta 27', '07006', 'PALMA', 'ILLES BALEARS', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3787, '3247', 'VIDRALA S.A.', NULL, NULL, NULL, NULL, 'B86049137', '649221232', NULL, NULL, NULL, 'VIAJES NAUTALIA, S.L.', 'C/ Mahonia, 2', '28043', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3789, '3248', 'MARTA OIL, S.L.', 'AVENIDA JIJONA, 88', '03012', 'ALICANTE', 'ALICANTE', 'B44609915', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3792, '3249', 'DI TRAVEL', 'C/. Séneca 15 Local 1', '08006', 'Barcelona', 'BARCELONA', 'B16738296', '607239760', NULL, NULL, NULL, 'INCENTIVES BY DICOM, S.L.', 'C/. Séneca 15 Local 1', '08006', 'Barcelona', 'BARCELONA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3794, '3250', 'EC AZAFATAS', NULL, NULL, NULL, NULL, 'B05294541', '685549672', NULL, NULL, NULL, 'CORZO AZAFATAS ETT, S.L.U.', 'Estudantado Salesionos, 18', '41710', 'UTRERA', 'SEVILLA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3797, '3251', 'FIRST EVENT', NULL, NULL, NULL, NULL, 'GB231947602', NULL, NULL, NULL, NULL, 'Morgan Travel ltd t/a First Event', 'Ghyll Beck House', 'LS19 7SE', 'Gill Lane', 'Leeds (UK)', NULL, 15.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3800, '3252', 'INDUSTRIAL FARMACEUTICA CANTABRIA, S.A.', 'C/AREQUIPA, 1-5ª PLANTA', '28043', 'MADRID', 'MADRID', 'A39000914', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3803, '3253', 'FUNDACIÓN EUROFIRMS', 'PLA DE L\'ESTANY, 17', '17244', 'CASSÀ DE LA SELVA', 'GIRONA', 'G17966532', '972181010', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3805, '3254', 'LEASEUROPE AISBL', 'Boulevard Louis Schmidt 87 B', '1040', 'Etterbeek', 'BRUSELAS', 'BE 0413.032.334', '+32471391142', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:42', '2026-02-18 13:04:42'),
(3808, '3255', 'WOW MEETINGS, S.R.L.', 'VÍA ROMA, 42', '47921', 'Rimini', 'Italy', '04653560401', '+393204410565', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3811, '3256', 'MERCEDES GARCÍA RAMÍREZ', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3814, '3257', 'ZKYROCKET', 'The Straw Barn, Upton End BP', 'SG5 3PF', 'Meppershall Road', 'Shullington (UK)', NULL, '+4407940576085', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3817, '3258', 'COLEGIO INMACULADA JESUITAS', 'Avda. de Denia, 98', '03016', 'Alicante', 'Alicante', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3819, '3259', 'AVOCO COMUNICACIÓN', NULL, NULL, NULL, NULL, 'B85473205', '647460266', NULL, NULL, NULL, 'DISTRIBUIDORES DE PARTES Y ACCESORIOS PARA LA AUTOMOCIÓN, S.L.', 'Carabaña 63 N.5 P I. Ampl.Oeste Ventorro del Cano', '28925', 'Madrid', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3822, '3260', 'GRUPO BALI HOTELES', 'C/. Actor Luis Prendes, 4', '03502', 'Benidorm', 'Alicante', NULL, '966815200', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3826, '3261', 'LE CLUB GOLF', NULL, NULL, NULL, NULL, 'B95594362', NULL, NULL, NULL, NULL, 'UGOLF IBERIA, S.L.', 'C/ Máximo Aguirre, 18bis, 6ª', '48011', 'Bilbao', 'BIZKAIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3828, '3262', 'MIGHTY FINE PRODUCTIONS, Ltd', 'Eagle House, 163 City Road', 'EC1V 1NR', 'LONDON', 'UK', '986574160', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3830, '3263', 'GONZALO MIRANDA CARRERA', 'C/SAN VICENTE 38', '46171', 'CASINOS', NULL, 'X4292897Q', '689820479', NULL, NULL, 'gonzalo@laantenafilms.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3833, '3264', 'SOUNDFIELD', NULL, NULL, NULL, NULL, 'BE0799 416 689', NULL, NULL, NULL, NULL, 'FOGES NV', 'Route de Lennik 451', '1070', 'Brussels', 'BELGIUM', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3836, '3265', 'HALCÓN VIAJES', 'CALLE REYES CATÓLICOS 16', '04001', 'ALMERIA', 'ALMERIA', NULL, '950275600', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3839, '3266', 'BANQSOFT AS', 'Østensjøveien 32', '0667', 'Oslo', 'Norway', '871579792', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3842, '3267', 'AMBIPER SCENT AND CLEANING SL', 'PLAZA ESPAÑA 1, TOUS', NULL, 'VALENCIA', 'VALENCIA', 'B72983182', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:43', '2026-02-18 13:04:43'),
(3844, '3268', 'NANOOK-NEVER BLINK, S.L.', NULL, NULL, NULL, NULL, NULL, '910065008 / 639061200', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3847, '3269', 'CLÁSICOS ALICANTE', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3850, '3271', 'CONNEX CONSULTING', '28, AVENUE SURCOUF', '33600', 'PESSAC', NULL, 'B95594362', NULL, NULL, NULL, NULL, 'UGOLF IBERIA, S.L.', 'C/ Máximo Aguirre, 18 bis, 6ª', '48011', 'Bilbao', 'BIZKAIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3853, '3272', 'TRAVEL SENSE A/S', 'Amager Strandvej 390', '2770', 'Kastrup', 'Denmark', '26381967', '+45 70230656', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44');
INSERT INTO `cliente` (`id_cliente`, `codigo_cliente`, `nombre_cliente`, `direccion_cliente`, `cp_cliente`, `poblacion_cliente`, `provincia_cliente`, `nif_cliente`, `telefono_cliente`, `fax_cliente`, `web_cliente`, `email_cliente`, `nombre_facturacion_cliente`, `direccion_facturacion_cliente`, `cp_facturacion_cliente`, `poblacion_facturacion_cliente`, `provincia_facturacion_cliente`, `id_forma_pago_habitual`, `porcentaje_descuento_cliente`, `observaciones_cliente`, `exento_iva_cliente`, `justificacion_exencion_iva_cliente`, `activo_cliente`, `created_at_cliente`, `updated_at_cliente`) VALUES
(3856, '3273', 'KEMON', NULL, NULL, NULL, NULL, 'A50114677', '976459135', NULL, NULL, NULL, 'SANAGUSTÍN INDUSTRIAL DE PELUQUERÍA S.A.', 'Pol. Industrial Ciudad del Transporte, C/H Nave 16', '50820', 'San Juande Mozarrifar', 'ZARAGOZA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3858, '3274', 'MEDITERRANEAN MICE SERVICES', 'C/. Trueno, 53 Pol. Pla de la Vallonga, 03006', NULL, 'Alicante', NULL, NULL, '669497127', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3861, '3275', 'OLE SPECIAL EVENTS, S.L.', 'Paseo de la Habana, 9', '28036', NULL, 'Madrid', 'B83052050', '673366158', NULL, 'marta.ales@voqin.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3864, '3276', 'MTS INCOMING S.L.', 'C/. Fluviá, 1, 1º Izq.', '07009', 'Palma de Mallorca', 'Islas Baleares', 'B07844764', '686826184', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3867, '3277', 'CARLES TORÁ (ATMOSH)', 'LES VOLTES, 6', '03579', 'SELLA', 'ALICANTE', '39336268N', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3869, '3278', 'Mari Carmen Sánchez Ruiz', 'Avenida Estocolmo, 87', '03503', 'Benidorm', 'Alicante', '25124923E', '677781274', NULL, NULL, 'm.c.s.r.67@hotmail.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3872, '3279', 'BASIKON', '17 Rue DE KRONSTADT', '92380', 'GARCHES', NULL, 'FR87851197335', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3875, '3280', 'SERAWA HOSPITALITY S.L.', 'Calle Cabo Estaca de Bares, 11', NULL, 'TEULADA', 'MORAIRA', NULL, '632534026', NULL, 'www.proceden.es', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3878, '3281', 'CLUB BALONMANO EÓN ALICANTE', 'CALLE RÍO JUCAR, NUM 7', '03007', 'ALICANTE', 'ALICANTE', 'G03780798', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:44', '2026-02-18 13:04:44'),
(3881, '3282', 'DATA TRICKS', 'Bâtiment C, 15 rue Jean Jaurès', '92800', 'Puteaux', 'Paris', 'FR37828595413', '+33(0)140907863', NULL, 'www.data-tricks.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3883, '3283', 'KUONI TUMLARE', 'Avda. Diagonal, 416-3º 1º', '08037', 'Barcelona', NULL, 'A78440856', '672286601', NULL, NULL, NULL, 'JBT Viajes Spain, S.A.U.', 'C/ Jacometrezo, 15 4ª Planta', '28013', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3886, '3284', 'FACTOR 3 EVENTS', 'Ausias Marc, 109 Atic 2ª Esc. A', '08013', 'Barcelona', 'BARCELONA', 'B63603880', NULL, NULL, NULL, NULL, 'CREAM EVENTS, S.L.', 'Ausias Marc, 109 Atic 2ª Esc. A', '08013', 'Barcelona', 'Barcelona', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3889, '3285', 'IMAGIN STUDIO B.V.', 'De Entree 201', '1101 HG', 'Amsterdam', NULL, 'NL858434015B01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3892, '3286', 'OPEN SAS', '10 Rue Lavoisier', '38330', 'Monbonnot', NULL, 'FR 83 381 031 285', '+33(0)650384949', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3894, '3287', 'EVEN TO EVENTS', NULL, NULL, NULL, NULL, NULL, '639023561', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3898, '3288', 'MEDIALINE EURO TRADE', 'Zehntenhofstrabe 5b', '65201', 'Wiesbaden', NULL, 'DE246410157', '+49 611 9881670307', NULL, NULL, NULL, 'Medialine EuroTrade AG', 'Breitelrstraße 43', '55566', 'Bad Sobernheim', 'Germany', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3900, '3289', 'LG Energy Solution, Ltd', '108 Yeoui-daero, yeongdeungpo-gu', NULL, 'Seoul', 'Republic of Korea', '851-81-02050', '+82-10-4111-5190', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3903, '3290', 'Odessa Private Limited', 'Aviation House 125 Kingsway', 'WC2B 6NH', 'London', 'United Kingdom', 'GB442177796', '+1 215 231 9800', NULL, 'www.odessainc.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3906, '3291', 'OMADA A/S', 'Oesterbrogade, 135', 'DK-2100', 'Kobenhavn O', 'Denmark', 'DK25357469', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3908, '3292', 'Novelend Tech Solutions', '12 rue Vivienne', '75002', NULL, 'París', 'FR23888979622', '0688985173', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3911, '3293', 'FRP ADVISORY TRADING LIMITED', 'Jupiter House Warley Hill Business Park', 'CM13 3BE', 'Brentwood', 'UK', '182 5699 66', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3914, '3294', 'WIDE TRAVEL & EVENTS', 'R. Margarida de Abreu, 11D', '1900-314', 'LISBOA', 'LISBOA', '508773911', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3917, '3295', 'TUREVENTS & GO', 'C/ Santa Teresa 12 Puerta 3', '46001', 'Valencia', 'VALENCIA', 'B97321996', '963528181 / 672055548', NULL, NULL, NULL, 'SHOPPING AND INCOMING SERVICES VALENCIA, S.L.', 'C/ Santa Teresa 12 Puerta 3', '46001', 'Valencia', 'VALENCIA', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:45', '2026-02-18 13:04:45'),
(3919, '3296', 'UNIVERSIDAD MIGUEL HERNANDEZ (Dpto. Patologia y Cirugia)', 'Ctra. Valencia - Alicante, Km 87', '03550', 'San Juan de Alicante', 'ALICANTE', 'Q5350015C', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3922, '3297', 'BABALU GROUP', NULL, NULL, NULL, NULL, NULL, '639138991', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3925, '3298', 'SOUDAL QUICK-STEP', 'Kouterstraat 14', 'B-8560', 'Wevelgem', 'Belgium', 'BE 0837698136', '+32 499 51 46', NULL, NULL, 'tegner@decolef.com', 'Decolef Lux Belgian Branch', 'Kouterstraat 14', 'B-8560', 'Wevelgem', 'Belgium', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3928, '3299', '2GT-GLOBAL GOLF TECHNOLOGY', '1180 avenue Vicrtor Hugo', '69140', 'Rillieux la Pape', 'France', 'B798834131', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3931, '3300', 'Husqvarna France', 'Parc les Barbenniers', '92635', 'Gennevilliers cedex', 'France', NULL, '+33 07 89 64 29 17', NULL, 'www.husqvarnagroup.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3933, '3301', 'ADECCO LEARNING & CONSULTING', 'C/ Estocolmo, 4', '03503', 'Benidorm', 'ALICANTE', 'B03495116', NULL, NULL, NULL, NULL, 'ONA SOL, S.L.U.', 'C/ Estocolmo, 4', '03503', 'Benidorm', 'ALICANTE', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3936, '3302', 'ANISSA Y FRANK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3939, '3303', 'OFISURESTE', NULL, NULL, NULL, NULL, NULL, '966931183 / 656806289', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3942, '3304', 'Centro Médico Salus Baleares S.L', 'Av. Alfonso Puchades 8', '03501', 'Alicante', 'Benidorm', 'B-07060478', '665959728', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3945, '3305', 'ADECCO FORMACIÓN S.A.', 'Camino Cerro de los Gamos 3', '28224', 'Pozuelo de Alarcon', 'MADRID', 'A58467341', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3947, '3306', 'ESTRATAGEMA HEALTHCARE', 'C/. Rodriguez San Pedro, 2-7º', '28015', 'Madrid', NULL, 'B86655784', '644083020 / 918371302', NULL, NULL, NULL, 'ESTRATAGEMA, S.L.', 'C/ Rodriguez San Pedro, 2', '28015', 'Madrid', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3950, '3307', 'Hotel INNSiDE Alicante Porta Maris', 'Plaza Puerta del Mar. Nº3', '03002', 'Alicante', 'Alicante', 'A-53331609', '965147021', NULL, NULL, NULL, 'PLAZA PUERTA DEL MAR, S.A.', 'Plaza Puerta del Mar. Nº3', '03002', 'Alicante', 'Alicante', NULL, 20.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3953, '3308', 'Industrias Metalicas Collado Andreu S.L.', 'Ctra. Nules, 123', '12530', 'Burriana', 'Castellón', 'B-12231205', '617 316 659', NULL, NULL, 'administracion@colladoandreu.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:46', '2026-02-18 13:04:46'),
(3956, '3309', 'ACT-WISE', 'Tentoonstellingslaan, 134', '9000', 'Gent', 'BELGICA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3959, '3310', 'TRAVELSENS, S.L.', 'C/. Gremi de Fuster, nº 23', '07009', 'Palma de Mallorca (Islas Baleares)', NULL, 'B-57727901', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3961, '3311', 'BARCELONA EXPERIENCIES', 'C/. Balmes 354,2º 2ª', '08006', 'Barcelona', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3964, '3312', 'ATFRIE', 'Paseo Delicias, 30', '28045', 'Madrid', 'MADRID', 'g-28600658', '607300969', NULL, NULL, NULL, 'ASOCIACIÓN ESPAÑOLA DE EMPRESARIOS DE TRANSPORTE BAJO TEMPERATURA DIRIGIDA (ATFRIE)', 'Paseo Delicias, 30', '28045', 'Madrid', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3967, '3313', 'EL CASÓN DE LA VEGA', 'Ctra. Abanilla, Km 2,8', '30140', 'Santomera', 'Murcia', NULL, '968277171', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3970, '3314', 'GERMÁN LLEDÓ', NULL, NULL, NULL, NULL, NULL, '680422184', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3972, '3315', 'ARROW ECS SAU', 'Avda. de Europa, 21', '28108', 'Alcobendas', 'MADRID', 'A14113500', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3975, '3316', 'AMBULANCIAS AYUDA, S.L.U.', 'CTRA. DE MADRID, KM 406, 800', '03006', 'ALICANTE', 'ALICANTE', 'B03677937', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3978, '3317', 'LIMONAR DE SANTOMERA, SOC. COOP.', 'Carretera de Abanilla, km.2', '30140', 'Santomera', 'Murcia', 'F30051643', '636483225', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3981, '3318', 'MIRA DIGITAL, S .L.', 'C Río Eume, 2 Bajo comercial', '30508', 'Molina del Segura', 'MURCIA', 'B73408080', '637847087 / 968308677', NULL, NULL, 'jmi@miradigital.es', NULL, NULL, NULL, NULL, NULL, NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3984, '3319', 'SOPHIA CALO', NULL, NULL, NULL, NULL, NULL, '622322803', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3986, '3320', 'PAULA BARRIO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3989, '3321', 'GRUPO EVENTO', NULL, NULL, NULL, NULL, NULL, '669816136', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:47', '2026-02-18 13:04:47'),
(3992, '3322', 'VALENCIA INTERPRETERS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(3995, '3323', 'ASOCIACIÓN ESPAÑOLA DE GERENTES DE GOLF', 'Avda. Haciendfa del Álamo, 11', '30320', 'Fuente Álamo', NULL, 'G92990647', '679815413', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(3998, '3324', 'GOLF PLAISIR', 'Ynglingagatan, 2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4000, '3325', 'TRIUMPH GROUP', 'Via Lucilio, 60', '00136', 'Roma RM', NULL, '10198371006', NULL, NULL, NULL, NULL, 'Triumph Italy srl Societa Benefit', 'Via Lucilio, 60', '00136', 'Roma RM', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4003, '3326', 'REGENERON SPAIN S.L.', 'C/Camino Fuente de la Mora, 9', '28050', NULL, 'Madrid', 'B87417044', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4006, '3327', 'CELLA MEDICAL SOLUTIONS', 'Avda. Teniente Montesinos 10', '30100', 'Espinardo', 'Murcia', NULL, '648231983', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4009, '3328', 'TECH FOR MICE SL', 'C/. Esteban Mora 53, Esc. D 7 D', '28027', 'Madrid', 'Madrid', 'B56509961', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4011, '3329', 'DAINS ACCOUNTANTS', '2 Chamberlain Square', 'B3 3AX', 'Birmingham', NULL, '135566307', '+44 (0) 1797 223626', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4014, '3330', 'RACCORD IMAGE', '10 rue de Penthièvre', '75008', 'Paris', 'Francia', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4017, '3331', 'VALORIAN', 'C/ Colón, 52 - 4º - 7ª', '46004', 'Valencia', 'VALENCIA', 'G28804292', '963518086', NULL, NULL, NULL, 'VALORIAN', 'C/ Maudes 51 -5º', '28003', 'MADRID', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4020, '3332', 'CRITEO', '32 RUE BLANCHE', '75009', 'Valencia', 'PARIS', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4023, '3333', 'MDA EVENTOS', 'C/. Puerto de Guadarrama, 52', '28935', 'Móstoles', 'Madrid', NULL, '638983591', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4025, '3334', 'BYPILLOW', 'C/. Marqués de Molins, 63', '03004', 'Alicante', NULL, NULL, '626771997', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:48', '2026-02-18 13:04:48'),
(4028, '3335', 'AICO MEETINGS', NULL, NULL, NULL, NULL, NULL, '669553202', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4031, '3336', 'GRUPO BES', 'C/ San Mateo n º 4- 3 izq.', '03330', 'Crevillent', 'Alicante', '48368394-S', '649108756', NULL, NULL, NULL, 'FRANCISCO JOSE GALVAÑ CANDELA', 'C/. San Mateo nº 4-3izq.', '03330', 'Crevillent', 'Alicante', NULL, 40.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4033, '3337', 'FEVER UP', 'C/ FERNANDO EL SANTO, 16', '28010', 'MADRID', 'MADRID', 'B21760699', NULL, NULL, NULL, NULL, 'EVENTOS SINGULARES MEDITERRÁNEO, S.L.U.', 'C/ FERNANDO EL SANTO, 16', '28010', 'MADRID', 'MADRID', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4036, '3338', 'DELREVES STUDIO, S.L.', 'C/ corsega, 257 1º - 2ª B', '08036', 'BARCELONA', NULL, 'B67829523', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4040, '3339', 'TECHNICHE EMEA Ltd.', 'The pinnacle, 170 midsummer boulevard', 'MK9 1BP', 'Milton Keynes', 'UK', 'GB697511792', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4042, '3340', 'SAFIR VIAJES', 'C/. Casa Pedro, nº 7', '41807', 'Espartinas', 'SEVILLA', '27318412-R', '686474991', NULL, NULL, NULL, 'FÁTIMA PÉREZ SILVA', 'C/. Casa Pedro, nº 7', '41807', 'SEVILLA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4045, '3341', 'CREATIVEMAS', 'C/. Pintores 2', '28923', 'Alcorcón', 'Madrid', 'B83447284', '680755978 // 902253253', NULL, NULL, NULL, 'Creative Marcom Advertising Service, S.L.', 'Avda. Europa 31, B1 P1 3A', '28223', 'Pozuelo de Alarcón', 'Madrid', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4047, '3342', 'MAYA GLOBAL', NULL, NULL, NULL, NULL, 'B66310442', '625972852', NULL, NULL, NULL, 'AFG Consulting Spain, S.L.', 'Paseo de Gracia, 101, 4-1', '08008', 'Barcelona', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4050, '3343', 'MVNO NATION', 'SUITE 212, 95 MORTIMER STREET', 'W1W 7GB', 'London', 'UNITED KINGDOM', '13214153', NULL, NULL, NULL, NULL, 'CLT Media Ltd', 'SUITE 212 95 MORTIMER STREET', 'W1W 7GB', 'London', 'UNITED KINGDOM', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4053, '3344', 'BACK4MORE', NULL, NULL, NULL, NULL, 'B67393041', '675624399', NULL, NULL, NULL, 'FLIRT CREATIVE, S.L.', 'Passeig St. Joan 89', '08009', 'Barcelona', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4056, '3345', 'CRAWFIELD BV', 'Visserstuin 168', '3319', 'LN Dordrecht', 'The Netherlands', 'NL865611269B01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4058, '3346', 'ARI CONGRESO, S.L.U.', 'C/ Cruz de Piedra, 8 1º', '03015', 'ALICANTE', NULL, 'B53589099', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4061, '3347', 'KCS Group Limited', 'Clarke House, Brunel Road,', 'NN17 4JW', 'Corby', 'England', '231961612', NULL, NULL, NULL, NULL, 'KCS Group Limited', '3 Weekley Wood Close', 'NN14 1UQ', 'Kettering', 'Northamptonshire', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:49', '2026-02-18 13:04:49'),
(4064, '3348', 'IES BEATRIU FAJARDO DE MENDOZA', 'C/. De la Fragata, 2', '03503', 'Benidorm', 'Alicante', 'S-0300001-E', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4067, '3349', 'GRUPPO PERONI EVENTI', 'Via del Prati Della Farmesina 57', '00135', 'Roma', 'ITALIA', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4069, '3350', 'GOLFMANAGER, S.L.', 'C/ Velazquez, 86 Bajo', '28006', 'MADRID', NULL, 'B88096292', '685350968', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4072, '3351', 'HOTEL SH JÁVEA', 'Carrer Val D´Alcalà, s/n', '03730', 'Jávea', 'Alicante', 'B13844642', '661760375', NULL, NULL, NULL, 'CIRCE MEDITERRANEA, S.L.', 'c/. San Roc 6 Bajo', NULL, 'Gandia', 'Valencia', NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4075, '3352', 'WEPLEIA', 'Av. Paral.lel, 54 3º 1ª', '08001', 'BARCELONA', NULL, 'B13710397', '646780540', NULL, NULL, NULL, 'LOCALTREE ONLINE SERVICES, S.L.', 'Av. Paral.lel, 54 3º 1ª', '08001', 'BARCELONA', NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4078, '3353', 'GP MASCOMUNICACION S.L.', 'C/. Castelló 128-7ª', '28006', 'Madrid', 'Madrid', 'B86782349', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4080, '3354', 'Bazelmans AV - Veldhoven', 'De Run 4537', '5503LT', 'Veldhoven', 'The Netherlands', 'NL807964487B01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4083, '3355', 'NUFARM', 'C/. Balmes 200,1-4', '08006', 'Barcelona', NULL, NULL, '681275624', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4086, '3356', 'BMC GLOBAL', 'C/. Còrtsega, 301 Sobreàticf 1er', NULL, 'Barcelona', NULL, NULL, '621142932', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4089, '3357', 'EVENTOS EL XATO', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50'),
(4090, '6223', 'SPORT & BUSINESS EVENTS, S.L.', 'C/DECANO PEDRO NAVARRETE, LOCAL 19', '29620', 'TORREMOLINOS', 'MALAGA', 'B92910009', NULL, NULL, NULL, 'events9@dmc-rtaspain.com', NULL, NULL, NULL, NULL, NULL, NULL, 0.00, NULL, 0, NULL, 1, '2026-02-18 13:04:50', '2026-02-18 13:04:50');

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
(1, 4, 'Oficina Central', 'C/ rio Amadorio ,4', '03013', 'Alicante', 'Alicante', 'España', '', '', '', 'Prueba de ubicación principal', 1, 0, '2025-12-19 17:19:31', '2026-02-05 18:11:27'),
(2, 4, 'SalóN Alicante', 'C/ rio Amadorio ,4', '46130', 'Alicante', 'Alicante', 'España', '', '', '', '', 0, 0, '2026-01-20 12:16:23', '2026-02-05 18:11:33'),
(3, 4, 'HALL - 1', 'Avda. Eduardo Zaplana', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:06:29', '2026-02-05 18:06:29'),
(4, 4, 'BONSAI', 'Avda. Eduardo Zaplana', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:07:03', '2026-02-05 18:07:03'),
(5, 4, 'HANOI - 2 ', 'Avda. Eduardo Zaplana', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:07:26', '2026-02-05 18:07:26'),
(6, 4, 'HANOI - 3', 'Avda. Eduardo Zaplana', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:07:52', '2026-02-05 18:07:52'),
(7, 4, 'HANOI - 4', 'Avda. Eduardo Zaplana', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:08:12', '2026-02-05 18:08:12'),
(8, 4, 'PASILLO SHANGHAI', 'Avda. Eduardo Zaplana', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:08:44', '2026-02-05 18:08:44'),
(9, 4, 'TERRAZA HALL', 'Avda. Eduardo Zaplana', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:09:05', '2026-02-05 18:09:05'),
(10, 4, 'DESMONTAJE', 'Avda. Eduardo Zaplana , S/N', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:09:31', '2026-02-05 18:11:14'),
(11, 4, 'MONTAJE  Y PRUEBAS', 'Avda. Eduardo Zaplana , S/N', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:10:24', '2026-02-05 18:10:24'),
(12, 4, 'VARIOS', 'Avda. Eduardo Zaplana , S/N', '03502', 'Benidorm', 'Alicante', 'España', '', '', '', '', 0, 1, '2026-02-05 18:10:59', '2026-02-05 18:10:59'),
(13, 6, 'Salon Cupula', '', '', '', '', 'España', '', '', '', '', 0, 1, '2026-03-05 10:06:40', '2026-03-05 10:06:40');

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
(3, 20, 10.00, '', 1, '2025-11-15 08:40:27', '2025-11-15 08:40:27'),
(4, 8, 7.25, 'Espara la prueba de lineas de presupuestos.', 1, '2026-01-20 18:21:11', '2026-01-20 18:21:11'),
(5, 9, 8.75, 'Prueba e observaciones', 1, '2026-01-20 18:34:27', '2026-01-20 18:34:27'),
(6, 14, 13.25, '', 1, '2026-01-22 18:12:02', '2026-01-22 18:12:02'),
(7, 12, 11.95, '', 1, '2026-01-25 16:16:53', '2026-01-25 16:16:53'),
(8, 5, 4.75, '', 1, '2026-01-30 18:56:52', '2026-01-30 18:56:52');

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
  `id_usuario` int NOT NULL,
  `firma_comercial` text COLLATE utf8mb4_general_ci COMMENT 'Firma digital del comercial en formato base64 PNG'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Volcado de datos para la tabla `comerciales`
--

INSERT INTO `comerciales` (`id_comercial`, `nombre`, `apellidos`, `movil`, `telefono`, `activo`, `id_usuario`, `firma_comercial`) VALUES
(1, 'Alejandro', 'Rodríguez Martínez', '698689685', '698689685', 1, 5, NULL),
(2, 'Carlos', 'López', '655656841', '655656841', 0, 6, NULL),
(3, 'Marta', 'Rodríguez González', '645567674', '645567674', 0, 19, NULL),
(4, 'Luis', 'Fernández López', '645575741', '645575741', 1, 7, NULL),
(9, 'Lucía', 'Pérez Sánchez', '698898874', '698898874', 1, 8, NULL),
(11, 'Ana', 'Hernández Torres', '635999999', '635999999', 0, 20, NULL),
(12, 'Miguel', 'Díaz Jiménez', '643455441', '643455441', 0, 21, NULL),
(13, 'Raúl', 'Romero Álvarez', '695548744', '695548744', 1, 9, NULL),
(14, 'Eva', 'Moreno Fernández', '698654645', '698654645', 0, 22, NULL),
(16, 'Teresa', 'Vázquez Suárez', '689789454', '689789454', 0, 23, NULL),
(17, 'Margarita', 'García Castro', '616515614', '616515614', 1, 10, NULL),
(18, 'Carmen', 'Martínez González', '623615641', '623615641', 1, 11, NULL),
(19, 'Sergio', 'López Hernández', '634535442', '634535442', 0, 24, NULL),
(21, 'Alberto', 'Hernández García', '644334567', '644334567', 1, 12, NULL),
(22, 'Natalia', 'Sánchez García', '621849484', '621849484', 1, 13, NULL),
(23, 'Laura', 'Ramírez Hernández', '632554779', '632554779', 1, 14, NULL),
(24, 'Francisco', 'Moreno Moya', '660300923', '660300923', 1, 15, NULL),
(27, 'Beatriz', 'Muñoz Vázquez', '477777777777', '777777774', 1, 16, NULL),
(28, 'Pablo', 'Moreno Sánchez', '698544745', '698544745', 1, 17, NULL),
(30, 'María', 'Torres García', '645644211', '645644211', 1, 18, NULL),
(31, 'Marta', 'Rodríguez González', '635224447', '633221448', 1, 19, NULL),
(32, 'Miguel', 'Díaz Jiménez', '646544684', '645487775', 1, 21, NULL),
(33, 'Luis Carlos', 'Pérez', '660300923', '965262384', 1, 2, 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAASwAAACWCAYAAABkW7XSAAAdsElEQVR4Xu3dCdh17VQHcDIkmSKF0GvKGGUe82bK9Bki8/AaIhUJSSSvqAxJyRCJ15AMRSJj9CqZI2Qo0/Mh81hoMrR+2fd17e8453vOPs+e91rXta5znvPsfQ//+5y1173uNZzyFEmJQCKQCEwEgVNOZJxzHeZ5Y2LnD7548NmDfyT4XCcz2a/E/95W/f+L8frO6v3X4/X1cwUp55UIFARSYB38u/D90cQlgk8bfOWque+J1ytsaPr7quu/6+Bdf0cL34pPrOl7gz9bvX46Xv8++P3Bn+igz2wyEegNgRRY20NN+7lY8I8FXySYdnSl7W8/yZXfjL8IrG8E04yK5uRzf3tdJWt1leBTB58z+MLVBeeO1wtU74vA2jSsf4t/PC/4CcEf3nHseVsiMBgCKbC+E/ozxEeXrYTDGeP1qtX7k1ukD8U/Px5Mm3lfTXj8Q7y3XVulN8QH/9PBqp8p2rx08GmCaXtnC75k8I8H+1+dnhl//HrwxzoYRzaZCHSCwBIF1g8EkjSlKwafLpiGcsEKXT92QmoTfTT+YWv1xuA3Be9Vf3eyOC03SkM8HHzPYFtY9NXgBwTTuGhnSYnAqBFYgsA6a6zAzYNvFnyZYFrHfmTr9MHgfwxm5Gb7sVWzhZs6nSomcNvgPwg+SzWZd8frPYJphEmJwGgRmLPAum6gfofgWwT7kdbpA5UQele8fiHYD/bzwbZvhNR/jnbF2hsYTO4X/NDg7w7+3+CnB9O4nEAmJQKjQ2COAsvp3KODr1ZD21buucG2cS+tfpyjW4yBBnTN6Je2xbUCEeAwpGEmJQKjQmBOAuvygezjgy9XQ9h2jmH5laNCfXyDcWL5wOAHB3PP+GTwCcG0zaREYDQIzEFgXTTQPBps61fo+fHmd4PfOhqkpzGQ88Uw/y7YQcR/B986+EXTGHqOcgkITFlg3TgW6GeDb1BbKD5Gx4JfsYTF62iOh6Jdbhd8vQgtxni2raREYHAEpiiwCKinBXNPQI7j2aceHszDO+ngCPxoNEGz4pDqIOLXgmmsSYnAoAhMSWCJsfu94FtWiDnV8iN6YjCnzaR2EaBhvSCYdz06GuxEMSkRGAyBqQgsflScG4tW9fJ4f+fgTw2G3DI65qf1rOAbptBaxoKPfZZjF1i8s2lRTqwQh042lZeMHdgZjc8J4qOC71vN6UHx+tszml9OZUIIjFlg/Vzg+LhgcXHohcH3qoTWhCCezVAfETP51Wo2t4/XZ89mZjmRySAwRoF15kDvOcHXr1D8Wrz+RvBjJoPqPAcqZc5bgsUhfi5YgPiJ85xqzmqsCIxNYMkVJXdT8bp+T7y/TnDmcRrHN+gcMYyPBAsap/GKz0xKBHpDYEwCS34pxnSZFBAHxpsGCxVJGg8Ct4mh/Gk1HA+TV49naDmSuSMwFoH1vQG0bAiS46EXB/Nc7yJn1NzXtI/5ebAILhdvKNJgXc6vPsaRfSwMgbEIrIcE7kcr7Hmp3yiYn1XSOBFgv/KAkeXhrsF/Ms5h5qjmhsAYBNb1AtSXVcAqqsBRUWK5pHEj8PsxvF8KlmqZZ7zDkaREoFMEhhZY0vb+c/B5gsWtyVOeJ0+dLnlrjfOEl7ZHjvlfDibAkhKBThEYWmCVp7RJ8vHhoJg0HQSKb5aEh6oHpZY1nbWb5EiHFFi2EW8O5t/zL8EKJSwh0+ckvygbBn0oPpfjPm1Zc1rVEc9lSIHFbaFkBb16vPd30vQQKFrWXgz9QsF5Yji9NZzMiIcSWHVfHqli7jIZxHKgqwiI96Qho9sFFx+tRCoRaB2BIQSWLaCiD3It8WD3VE7bR+tL22uDT4neJFN0YGI90yWlV/iX09kQAuvJAe/dKojT0D6P7xohxZYls0MGRs9jTUc5i74FFrcF8YFKTKmBp2ILd4ak6SPw5zEFsYXSK5ekf9OfVc5gVAj0LbCk2i25lCTl+4tRoZGDOQgCPxk3v7ZqQLgOjSspEWgVgb4F1h/F6O9ezeD08ZpuDK0u56CN2Q4qDyYrbDqSDroU8+28b4HlNMmpknp34tGS5oUADYumJRd8vezavGaZsxkMgT4FliISUhyjJwX/fE+zZgQ+W3CGjnQPuIIgUljvBatxmJQItIpAnwJL0YgS1a9UVwl4bnVCK42p+vyw6jPl2O/dZWfZ9v/j+9gKBymD0l0lvxStItCnwCqnSHJcsXN8udWZrG9MAYtSPOGN8f7KPfS55C7YrpRiQ9cKfs2Swci5t49AnwKLQVaK3b8Nvkb7U1nbolS+nwk+Y/B/BUvB7DWpGwSuEM2+qWraibCwnaREoDUE+hJYp40RF3+rP4z3qt/0RX8WHd2q6uza8fo3fXW8wH74130+WCGR48EM8EmJQGsI9CWw2DO+Uo36t+KVbakvOhIdPb3qjFsFo3BSdwg8L5p2QviN4LMG/3t3XWXLS0OgL4HFR0cWUVu0ZwbfsUegD0VfKr2gveA8veoWfDGFYgtR2rG6xXpxrfclsAD71mC+Vx8PlmG0T3pfdHaRqsP0wu4WeYVE3lF14dTQ6WxSItAKAn0KrIfGiBVERRcM/lArM9iuEVvCI9Wl6YW9HWa7XnXuuPFj1c3CsJS2T0oEWkGgT4HlZLAccyteoAx9X6TsPWdV9NLgE/rqeIH91B2EHxnzf8ACMcgpd4RAnwLrNDGHTwUzxPbtE3XV6FNFafSlYO4NSd0gYMv93qppTrtFq+6mt2x1UQj0KbAA+5jg+1QIM37v9YS2AgmfrfUlNbO6ekntIwDbku76V+I9592kRKAVBPoWWIdj1BxHEV8sPll90Rejo7NUnT08Xh/cV8cL6+fGMd+/rOYsXrRsxRcGQ063CwT6FlgcSKWU4eZwNJghvi+qnxSqhahqT1L7CMgiWzzc+dvxu0tKBFpBoG+BZdAyjYrpe27wrVuZxXaNvCguu0nt0j63pNuNcB5X2QaW+pIHSZcs3tSD5XPBF5sHNDmLgyIwhMD6nRi0kyOFKC550Ak0uL/0W25J+0oD8BpcWlLMuIVP1jsb3FsuPXv1QCsxp16LKWGH5vKWuSAwhMASz/eqYKEbZwjuKxi57tpg/VTs+aG5LOSI5vGSGMsNq/HIQ/aFhmM7FNe/MFhhXdERwro+EHyZ4P9o2FZePjMEhhBYqgQzgCv35Yv91z1hqjDC6skg7/dSU6+nYcy+G2FQhI7YUVkympBgabGINCz3K2oheJ0rzD8F08xf2aTBvHZeCAwhsCCo+MRPB78i+Ho9Qcq73pO6TmxobGlJ7SCg3Ne/Vk3Ron9qy2aFbP1i8G2DTx3MBeXywXvB1khx1vJd9eBRmSdpgQgMJbAuFVh7YiKlvkq1lS6X4FA0XoKgSz93ijfHuux0YW3/ccz3rtWctwmBunpcezT4cA0nGrcAavnTCgmitk628ATiJYKzWOvCvlymO5TA0jc7xU2DbckIsK7rE8oUsVqlJ5PMtfelJ3SKYXybrLL3jOvr4Vlvib95xgudWke2h7LWIpobDS5pYQgMKbDEnH04mE2rr5gzW0Jbw0L8wI4ubM27mC575NuDS0YM27snnExHdU1MQkXaGBeGkyP2sJJbS9v6SFoYAkMKLFDTsGha6IrBb+4Y/5LipnSTW8J2AC++dVqDsfCcTRqz6kWC323p7hL8rAZDEIv6g8G0MemYkxaGwNACS/8EFofOTwdfPFiK3a5ImTGaXSFG4g921dlC2iV0nlrNlRBiLC/2yVUIGNCfE8yVxfsSwrMtVHy6+O7tcgK5bR953YgRGFpggYZHs9xYfLJUXClVbrqAjTB0RI5oAOxaSbsjoCiurSBfKSTvmHJu64jW9fLq2v22jJtGZPvokIYP33mD+dIlLQiBMQgscJcnr/ddnhrW4wkZ+4vNZUFL3tpUTx8tqeBdMJS+5yc2tO50T2SDtD5PDubEuwsVdxj3OmEsWSF2aSvvmSACYxFYoHtI8NFgT03bilIluk1Yi1OjNveCM7/77ugSPHerbrdmwnDqKXxKy/yqaEYEjDxoNC0a0i5UT0/EfaIU5t2lrbxnggiMSWAZC090gdEMt57WbYftpMBq50t6h2jmGVVT36yE0ab8YlxHpEo+MdjBCsP5rlSK8bo/K3nviuKE7xuTwAKj2DNbh3N29IWUa1zOcbQXnBpW8y8v7ZdwkkEWnVxWUS4krnWyJ+zmePPuTnIH95f719bvAvGewExaCAJjE1hgv1wwB0SGXLYOW4+2qG7Den80Kp1v0vYIeKDATQZXdHJOv4QVdweHKjI4/ML23Wy8koe7B1qh68SbV7fQbjYxEQTGKLBAx0+Hvw46Ely2HweFNQXW7ggSVh4kJfGhAHYPl3XVj8QE8mJ3IqusG+HVViTDe6Ktkh+r75xqu6OXd7aCwFgFlsnxfubmgFRrVrX5oOQkS0EKdDzYNiVpfwRoSeI9+ckhRnPazWoMKG90tiUOuQVjKZPbrP5sS2hriGwHzxG8zti//6zyiskhMGaBBcy60DoafwulOQjVs45mua/tkCRwhMKU3GEOQmTa4FNVp/PHH7zWHZrIYyU9ctGSt+tpu6vOHJfJQur0Ee3q07Vdb3nVqBAYu8AC1u2CnxbMyPviYLaQXV0e6gVVtclLO2k9AiXvfr1YhxO+WwW/rnaL79D9ghX2kLNfloVbBHdZlUjOLH0gWh7fvaQFIDAFgWUZZJuUO4uxl83EsfouOZGEhZQ88n50hxewxrtM8VDcxD5Uj9c7Hn/fPbjku9IuR1DaV8H0r+I9jadUft6l723uqVfmsS2U8K9pZtNt+slrRobAVAQW2Gw5jgVzPPxasGos/HuaEDuYHx2yPbS1STopAnJP2dqxDRVSW5A/1ddrn3EUFQv4w8FfDuZE+vyewJTh4zPBZ6r6OxKvbR3M9DSF7GYXBKYksMr8lJH6zWDbD1H78iQ5idqG6oUo/AgVokj6NgK23DBRS7DYh2zvbJvr9qpTVdcRUCU85+bxfq9nIOtryVRQr4jU81Cyu74QmKLAgo3TPV9YWxYGWMngtkl1rHBryaPENsPuknSKU5wnQLDlLu4CEvA9PtiDgfZUSA5+uHEeRbQaGmtbLgtN1sJYuTggmh9tazVBY5P28toJIDBVgQVajqWMvUeCDwVvk9TNVocRHy0926htFVsgn7firgAXcYHi9OpaFX+qx1bXl2vkMqPhDklSA/F2R0eDD3qKPORcsu8tEJiywCrTY0NRSeXCwW8Ldoq46YdUPyVcal1C6YXlTLeVXiW5yeBX4v1s/0QbEAQcR/lfCVIXfUCzHZpKMkDjkMnB1jRpxgjsIrAeHXgcCf5SsBTH8iH5YnPyPEhg60Fgpi3YIvLbQpuq4SgZ5Vge+REePUinE7rXdokbgFxjqyl1OF3KeuAAo9T9Yx+8Y/CDgj0QkGyw7FbvGtG8xZyWnFiS+nFwzW3hiBao7aHsIrDqPjDbjMcXyYkOoy4h5+83BbOTrGZjeG917X7tysO0rqimIFu2rMPBcoT7kQrHKfSkeFNyMbFf1X2M9utzzP8nhITM1D2+JdcT6M2zH5dg5TIPWz4ayvFqLcrn1483BFg5JfxovGd4lyJmjCQnlpNjlGXbxrhCLY5pF4Gle7Fh6gmqdiOAWJVehQjGQnsxEIZk8/MELvFuBFrRMGwfSxFXjqirNQv5Eq2LkxvDHPkdXan6gUoZXIzl+42NrxKBThtdPVk9HJ+p/1dSSBP0Tg0Z1nfNX7XfeNr4P6N/CdviZyeOMWmmCOwqsDbB4YlOmElfy2/Kj4nBlqo+9cwIfL/k6SrEYXE146W/S7oT2uSm3OZNvk6H4uLzBcsP5sHAVtckU+q34npe54QzgbQqqGhix4IJQEQbdoDBz23MgqpgSBMsNQzFLHIuzpqFTb5hE7q2bYG139TZRoRRcEfwJPdjWUeu4+PDH4gNRb73qZOtMJsfAYJs38o2zYmnOZf/EfI0xG2I24G4yL1gx/vakNnTlpthfFP5LA+To8FO+xBBdaz6bGp2oHrVHlva1TjHbXDMayaAQN8Cq01IDleN2Q5Jc0K4yZdkTgJkD7XZ2cjaIqTY+1SRUW2oCByfIfOnHTGUr2YyoAU/KriE0xBwzw5+QPBUizpwJn5ENXd2uXL4MrJly+EcFIEpC6wmc79KXEybuU2wI/1CfvByPNlKSN/rtHET0YD4K5VwkCb9j+VaQsyJbiF2OhgQgA5CaGhskQ5DPACwzAvsfba67hUU7Tr/q2+Rh5zjZWtjoVGWnF1Djin77gCBpQisAh2HSFWHES9pAorNTUwcwcV2c1C7zaFoA6+jYuPzoydAZcskCGzdShbPTct8lviH+L1tiGAhgG3tbA0J2rrdi4ByyEAYIfY5W3DEoL+tEX/1Xn8TdiVTg3kaC3ueYHX5skrYD2FnbObvuuPBts27kO+xgxNuDtoTlN1mDq5dxpT3dIDAkgUWu8+NgkX+3ztYVRdkeyQJHa1iyiR/FSdQGmURhu+I9xLg7eqiwOBPKKzSus9popdeuZCB3LX70YlxgYymhB0N0HvfVe/VliSUxA/WSbqgO1UfOMEWapQ0MwSWLLD8aK9dW08aCL8s20bEm1s4irzlUyKns476FTQtgoqGIwSH4+zYTtDKdh3GdS2S9ktL9B0t79etg/AcmqStLG0ZOQ3l4kCrM/ekmSCwNIF13Vi3coJ0PN4Lol4lhns/eJ7e8BF/6AfAw3uds+pYvgoE1e2DZfos2y5bP+4JDNFjE1QHwe1wdbM5c6GxTpxH162nSzkaE2pCtmw7vXeyaEvqYCJpIggsTWDx+JbXHXkyX+hk1omrQQlP4YLBf8mPv43c8m1+PTjD/kYwD362IMQvyUmgjAv1HFZt9jvGtlTU8cBx8mnrWnz/2P5ob2Xruro1LXY3WFlnSQrZFfnRcba1RU0aAQJLE1iM3iUbpi/narjKuiWxrToS7Ojce/ezb4lFZFMZik6Iju8VzPZW5sGFgYb1gmB2n6URL/5bVpP+mXhVeHU/gp/fAZ9ADyZ2Mw82nxW7JuHFduaghnbmwIBR34NBNIQY2jFr3/thMJn/L01gWRg/6mLb4UG+t+VqceZkwLZddB9ixKbdMOD3RQ4KjgQXh0/9CqN5WDBv9iWfjrHblfL1HihHW1qUIsz4t4k2QF79zXbGCdf3iibG5uk0lPMuje81LY0hmwkEliiwaB8lDQmbj1PBpuQ+J1LFZqK46LFgtfi68hJ3mukkjOAsRFA60dz11K/pvMd+PeHBRmVrbOsvnKlPEpXhYWabLhyNI28KrBZXYIkCS8ZRmUfRQdMkS4AnLUspgcWfSZodebeE4bRBnt5yUJX887ayzwhmT/tIGx3MrA0+YE4e0x9rZgtrOksUWHU7Fu/uEvR7kOWVJ8pWse53VE4XJRfchWw3GNK5WjgAQLYZbDRdV6XZZbxjuccDRDZZxG0ltc+xrEwL41iiwAJbOU3io8Mrui26QTTEplVynmvXkTq7ilxc2xDhJBZOsj0nW4WcTioQUQKkt2lridccjkmLWkBt2rGWiOXo5rxUgcUhlHc7Ene2KaPBrgtG0yJ0Sv547TCGPzGYT1QJWF5tn2c6jYoNpJATKoKK7S1pfwROF5fwPxP7eTx4k2/W/i3lFaNDYKkCq16Ik7sCn6UuiAFWVgTFMuqanNM8R+7HghmG9c8JUhxfnWxnnAbuGmPXxZym0Ca3AwHRDkBoqRxEk2aAwFIFFiM5O5D5264Jiu6anCpyi6jbzFazJ5QxCEq2nZE/P6k5AhxmaatI6qGpx4U2R2CmdyxVYFlOdo7DwexYNJsuPcI5I9qCXiuYM+KmdNJOthjpxf2tpmye6Vewk2nJ5a/2AHIY8pROeslGe0dgyQKLAGHLQuxKNJq2A2WvEW0K71Faq26X2m+hxS4+M/hV+12Y/1+LgFqFQq8QFxYRAUkzQGDJAottgx9TOYlTvZjtQ0jHQcqVae8+wXy0SomsTV8VMWzq6QnKlYNKAYV6OmjhHgz1Tgj3ZvB962sKHEc9fHicc9yk2SbNAIElCyzLdyRYQr+S3cBn7Ep8qHiQNykiwcBOa2Or8kPZRNwSXhvMw57hfdWgziXClsbYFC8t9JJ4QyMsR/Yz+Pp1OoXj0brtt4fPuhxenXaejXeDwNIFFlSlnKHllIybdaQ5ljrRkwyuVDoWh3go2Kuof6d7Ch/Uhcu61RK+Q1t6fvAmt4bV+ySiOxJMgBWidT0mWG4rWQWS1iMAI5ou4oS75BjL2XxHUmB9eyltx0T6t50LXIS/MB1tcyDdlXjnc41wylgXrHJ78c/SR9JJEaDpir1EtNax5J/PdToAAimwTgqeXEr8sgRHc0DchZz00dhsKSWJa5vEFHJKlQKlkBJd+hRfKLd50rezKby9AkIWWRpp0sQRSIG1fgFt9Y4EM4ITYtuQRG8qKnuqe981GZcfohOwEmuoz5cFlxTBXY9hzO3zdBeMzqXkaLBT4KSJI5ACa/8FlCdcxsoiuJzsyUq5F+wkilGX4dznQ5AfJm/6BwbXC9PSutjMxDB6v0TiMHqZYAccUgIlTRyBFFgTX8CV4bPBKYhqS0uQFbI1fXjw0irJKERBmJt/fQs9r1Vf0GxSYM1zsblpMNDb0soNVcipIq1DPq0lOKXeI+ZJy6RhNnHcnee3YgazSoE1g0XcZwqqyghP8eOtB2DbyvLCn7PgumHMj/8a4huXQeQT/76nwJr4AjYYvtp+1wy+TrC0N8VvbC/eq3jNNYIbxpzoPDGZE4N9z1XMSb+1ia9uCqyJL+COwxf+o2gFQ3Td4VWQsFQ3KsHMgQjpchhy5XgvY2vShBFIgTXhxWth6EJWuGLYGtaJoys3AN75Uyce7raDtEruHkkTRiAF1oQXr8Whyw8mtbPc9HUSS+l0kUc9n6Yp0l4MWhC6SAHhOkkTRiAF1oQXr4OhO0k7Fiy+sk6fiD9oXbJGTC1PVxFYj4ixl+IUHUCXTfaBQAqsPlCeXh83iSHLPFEqH9dnoGgrtwgJ8giDMZOK2DRDbh4HLek25nkuZmwpsBaz1DtNlJc4d4i7bLhbHq+nBkuT88Wdeuj2JkHtyssjtjrRAEkTRiAF1oQXr8ehnyn6krZZupZ66bH6EGSNeGGwreNY6JExkPtXg8nwnLGsygHGkQLrAOAt9FZhP7Qu6Z/XkS3Yi4JVwC7ZEoaCituGVNPocZXQHWos2W8LCKTAagHEhTbBEZMRW0rpdckPwcJYL/j6ycGfHQAnJdReV/VLiErNkzRhBFJgTXjxRjR0Wtctg71uouPxDymeCRCFTvuguob10ujwhD46zT66QyAFVnfYLrHli8SkbxWsUK2UPOuIsKJ12aJ9skOQVCqS3lo+LHTfYNvUpAkjkAJrwos38qFfrdJonDCedcNYpX8hvF7f8lx47tPmSrC3ys/eT9X5tWV4pttcCqzprt1URq4AxLWDZUYlxNaRGoJcI/h37RoORJOSTkc/3DHq9O7445JTASzHuRmBFFj57egTgUtFZ5xS7xws7c0mUhmIg+pXg4UFEXqcP+XZF7h9ruCLBqvYXU8Pva69N8SHNw1eatbVPte3875SYHUOcXawAQFC5EjwjTpCSCoZvmNLy7LaEZzjaDYF1jjWYcmjoGkpZisz6n7a0rY4SSNjG0pDS5oRAimwZrSYM5jKBWIOYhhvFrxNteavx3XKqr05WKI+jqoM+FmDcAZfhnVTSIE104WdwbScLAq+ZveqE61JXnrZUd81g3nmFBogkAKrAVh5aSKQCAyLQAqsYfHP3hOBRKABAimwGoCVlyYCicCwCKTAGhb/7D0RSAQaIJACqwFYeWkikAgMi0AKrGHxz94TgUSgAQIpsBqAlZcmAonAsAikwBoW/+w9EUgEGiCQAqsBWHlpIpAIDItACqxh8c/eE4FEoAECKbAagJWXJgKJwLAIpMAaFv/sPRFIBBogkAKrAVh5aSKQCAyLQAqsYfHP3hOBRKABAimwGoCVlyYCicCwCKTAGhb/7D0RSAQaIJACqwFYeWkikAgMi0AKrGHxz94TgUSgAQIpsBqAlZcmAonAsAikwBoW/+w9EUgEGiCQAqsBWHlpIpAIDItACqxh8c/eE4FEoAECKbAagJWXJgKJwLAIpMAaFv/sPRFIBBog8H8eXJfT1ncRhgAAAABJRU5ErkJggg==');

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
,`porcentaje_descuento_cliente` decimal(5,2)
,`id_forma_pago_habitual` int unsigned
,`exento_iva_cliente` tinyint(1)
,`justificacion_exencion_iva_cliente` text
,`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`descuento_pago` decimal(5,2)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`observaciones_pago` text
,`activo_pago` tinyint(1)
,`id_metodo_pago` int unsigned
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
,`observaciones_metodo_pago` text
,`activo_metodo_pago` tinyint(1)
,`cantidad_contactos_cliente` bigint
,`tipo_pago_cliente` varchar(17)
,`descripcion_forma_pago_cliente` varchar(219)
,`direccion_completa_cliente` varchar(470)
,`direccion_facturacion_completa_cliente` varchar(470)
,`tiene_direccion_facturacion_diferente` int
,`estado_forma_pago_cliente` varchar(23)
,`categoria_descuento_cliente` varchar(15)
,`tiene_descuento_cliente` int
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
,`id_forma_pago_habitual` int unsigned
,`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`descuento_pago` decimal(5,2)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`observaciones_pago` text
,`activo_pago` tinyint(1)
,`id_metodo_pago` int unsigned
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
,`observaciones_metodo_pago` text
,`activo_metodo_pago` tinyint(1)
,`cantidad_contacto_proveedor` bigint
,`tipo_pago_proveedor` varchar(17)
,`descripcion_forma_pago_proveedor` varchar(219)
,`direccion_completa_proveedor` varchar(470)
,`direccion_sat_completa_proveedor` varchar(470)
,`tiene_direccion_sat` int
,`estado_forma_pago_proveedor` varchar(23)
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
(2, 1, 'Josep', 'Pastor Segura', 'Director de comunicación', 'Administración', '', '+34622505058', 'joseppastor22@gmail.com', '', 0, '', 1, '2025-11-18 18:13:17', '2025-12-14 10:35:45'),
(3, 2, 'Josep', 'Pastor Segura', '', '', '+34622505058', '', '', '', 1, '', 1, '2025-11-18 19:11:09', '2025-11-18 19:11:24'),
(4, 2, 'Pepe', 'Diaz', '', '', '', '', '', '', 1, '', 1, '2025-11-18 19:11:39', '2025-11-18 19:12:31'),
(5, 2, 'Aaron', 'Sanchez', '', '', '', '', '', '', 0, '', 0, '2025-11-18 19:12:06', '2025-11-18 19:42:09'),
(6, 4, 'Ana', 'Escribano', 'Directora de eventos', 'Ventas', '607244260', '647163449', 'comercial@mdraudiovisuales.comjjjj', '', 1, '', 1, '2025-12-11 18:58:38', '2026-02-05 09:53:57'),
(7, 4, 'Prueba de funcionamiento', '', '', '', '', '6661225447', '', '', 0, '', 0, '2025-12-12 20:35:36', '2026-02-05 09:53:30'),
(8, 4, 'Prueba de eventos', '', '', '', '', '', '', '', 0, '', 0, '2025-12-12 20:36:06', '2026-02-05 09:53:22'),
(9, 4, 'Prueba de eventos', '', '', '', '', '', '', '', 0, '', 0, '2025-12-12 20:39:18', '2026-02-05 09:53:26'),
(10, 1158, 'Luis', '', 'Direcetor genral', '', '', '', 'contac@adsdfaseda.com', '', 0, '', 1, '2026-03-05 11:29:56', '2026-03-05 11:30:31');

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
-- Estructura de tabla para la tabla `documento`
--

CREATE TABLE `documento` (
  `id_documento` int UNSIGNED NOT NULL,
  `titulo_documento` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `descripcion_documento` text COLLATE utf8mb4_spanish_ci,
  `ruta_documento` varchar(500) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Ruta relativa del archivo PDF',
  `id_tipo_documento_documento` int UNSIGNED NOT NULL,
  `fecha_publicacion_documento` date DEFAULT NULL,
  `activo_documento` tinyint(1) DEFAULT '1',
  `fecha_creacion_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;

--
-- Volcado de datos para la tabla `documento`
--

INSERT INTO `documento` (`id_documento`, `titulo_documento`, `descripcion_documento`, `ruta_documento`, `id_tipo_documento_documento`, `fecha_publicacion_documento`, `activo_documento`, `fecha_creacion_documento`, `fecha_modificacion_documento`) VALUES
(1, 'Manual de seguridad', 'Es una prueba de grabación', 'documento_6943079f41802.pdf', 1, '2025-12-17', 1, '2025-12-17 19:42:23', '2025-12-17 19:52:15');

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
-- Estructura de tabla para la tabla `documento_presupuesto`
--

CREATE TABLE `documento_presupuesto` (
  `id_documento_ppto` int UNSIGNED NOT NULL,
  `id_presupuesto` int UNSIGNED NOT NULL COMMENT 'FK a presupuesto',
  `id_version_presupuesto` int UNSIGNED DEFAULT NULL COMMENT 'Versión del presupuesto usada al generar el documento',
  `id_empresa` int UNSIGNED NOT NULL COMMENT 'Empresa emisora - DEBE SER REAL (no ficticia) para facturas',
  `seleccion_manual_empresa_documento_ppto` tinyint(1) DEFAULT '0' COMMENT 'TRUE si empresa fue seleccionada manualmente (facturas), FALSE si heredada (presupuesto, parte_trabajo)',
  `tipo_documento_ppto` enum('presupuesto','parte_trabajo','factura_proforma','factura_anticipo','factura_final','factura_rectificativa') COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Tipo de documento generado',
  `numero_documento_ppto` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Número generado (P2024-001, FP2024/001, F2024/001, R2024/001)',
  `serie_documento_ppto` varchar(10) COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT 'Serie usada (P, FP, F, R)',
  `id_documento_origen` int UNSIGNED DEFAULT NULL COMMENT 'FK al documento_presupuesto que se rectifica (solo para factura_rectificativa)',
  `motivo_abono_documento_ppto` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT 'Motivo del abono/rectificativa (obligatorio si tipo=factura_rectificativa)',
  `subtotal_documento_ppto` decimal(10,2) DEFAULT NULL COMMENT 'Base imponible (negativo si abono)',
  `total_iva_documento_ppto` decimal(10,2) DEFAULT NULL COMMENT 'Total IVA (negativo si abono)',
  `total_documento_ppto` decimal(10,2) DEFAULT NULL COMMENT 'Total con IVA (negativo si abono)',
  `ruta_pdf_documento_ppto` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT 'Ruta relativa: public/documentos/presupuestos/[id_ppto]/[tipo]_[numero].pdf',
  `tamano_pdf_documento_ppto` int UNSIGNED DEFAULT NULL COMMENT 'Tamaño del PDF en bytes',
  `fecha_emision_documento_ppto` date NOT NULL COMMENT 'Fecha de emisión del documento',
  `fecha_generacion_documento_ppto` datetime DEFAULT CURRENT_TIMESTAMP COMMENT 'Timestamp de generación del PDF',
  `observaciones_documento_ppto` text COLLATE utf8mb4_spanish_ci,
  `activo_documento_ppto` tinyint(1) DEFAULT '1' COMMENT 'Soft delete: TRUE=activo, FALSE=anulado/reemplazado',
  `created_at_documento_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_documento_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Documentos generados a partir de presupuestos: partes de trabajo, facturas proforma, anticipos, abonos';

--
-- Volcado de datos para la tabla `documento_presupuesto`
--

INSERT INTO `documento_presupuesto` (`id_documento_ppto`, `id_presupuesto`, `id_version_presupuesto`, `id_empresa`, `seleccion_manual_empresa_documento_ppto`, `tipo_documento_ppto`, `numero_documento_ppto`, `serie_documento_ppto`, `id_documento_origen`, `motivo_abono_documento_ppto`, `subtotal_documento_ppto`, `total_iva_documento_ppto`, `total_documento_ppto`, `ruta_pdf_documento_ppto`, `tamano_pdf_documento_ppto`, `fecha_emision_documento_ppto`, `fecha_generacion_documento_ppto`, `observaciones_documento_ppto`, `activo_documento_ppto`, `created_at_documento_ppto`, `updated_at_documento_ppto`) VALUES
(92, 16, NULL, 3, 0, 'factura_proforma', 'FPO-0007/2026', NULL, NULL, NULL, 826.45, 173.55, 1000.00, 'public/documentos/proformas/FPO-0007_2026.pdf', 87202, '2026-03-09', '2026-03-09 19:16:25', NULL, 1, '2026-03-09 18:16:25', '2026-03-09 18:16:26'),
(93, 16, NULL, 3, 0, 'factura_anticipo', 'FE-0024/2026', NULL, NULL, NULL, 991.74, 208.26, 1200.00, 'public/documentos/anticipos/FE-0024_2026.pdf', 87299, '2026-03-09', '2026-03-09 19:16:52', NULL, 1, '2026-03-09 18:16:52', '2026-03-09 18:16:52'),
(94, 16, NULL, 3, 0, 'factura_final', 'FE-0025/2026', NULL, NULL, NULL, 18057.26, 3792.02, 21849.28, 'public/documentos/facturas/FE-0025_2026.pdf', 98667, '2026-03-09', '2026-03-09 19:17:22', NULL, 1, '2026-03-09 18:17:22', '2026-03-09 18:17:22'),
(95, 16, NULL, 3, 0, 'factura_rectificativa', 'RE-0009/2026', NULL, 94, 'Cambio de importe', -18057.26, -3792.02, -21849.28, 'public/documentos/abonos/RE-0009_2026.pdf', 87201, '2026-03-09', '2026-03-09 19:21:54', 'Cambio de importe', 1, '2026-03-09 18:21:54', '2026-03-09 18:21:54'),
(96, 15, NULL, 3, 0, 'factura_final', 'FE-0026/2026', NULL, NULL, NULL, 26441.25, 0.00, 26441.25, 'public/documentos/facturas/FE-0026_2026.pdf', 88375, '2026-03-10', '2026-03-10 18:25:34', NULL, 1, '2026-03-10 17:25:34', '2026-03-10 17:25:34'),
(97, 13, NULL, 3, 0, 'factura_proforma', 'FPO-0008/2026', NULL, NULL, NULL, 82.64, 17.36, 100.00, 'public/documentos/proformas/FPO-0008_2026.pdf', 87374, '2026-03-11', '2026-03-11 10:16:50', NULL, 1, '2026-03-11 09:16:50', '2026-03-11 09:16:50'),
(98, 13, NULL, 3, 0, 'factura_anticipo', 'FE-0027/2026', NULL, NULL, NULL, 123.97, 26.03, 150.00, 'public/documentos/anticipos/FE-0027_2026.pdf', 87175, '2026-03-11', '2026-03-11 10:26:32', NULL, 1, '2026-03-11 09:26:32', '2026-03-11 09:26:32'),
(99, 13, NULL, 3, 0, 'factura_final', 'FE-0028/2026', NULL, NULL, NULL, 2030.00, 426.30, 2456.30, 'public/documentos/facturas/FE-0028_2026.pdf', 87212, '2026-03-11', '2026-03-11 10:27:41', NULL, 1, '2026-03-11 09:27:41', '2026-03-11 09:27:41');

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
  `fecha_alta_elemento` date DEFAULT NULL COMMENT 'Fecha de puesta en servicio',
  `fecha_fin_garantia_elemento` date DEFAULT NULL,
  `proximo_mantenimiento_elemento` date DEFAULT NULL,
  `observaciones_elemento` text,
  `activo_elemento` tinyint(1) DEFAULT '1',
  `es_propio_elemento` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'TRUE: Equipo propio de la empresa | FALSE: Equipo alquilado a proveedor',
  `id_proveedor_compra_elemento` int UNSIGNED DEFAULT NULL COMMENT 'Proveedor que vendió el elemento (solo si es_propio = TRUE)',
  `id_proveedor_alquiler_elemento` int UNSIGNED DEFAULT NULL COMMENT 'Proveedor al que se alquila el elemento (solo si es_propio = FALSE)',
  `precio_dia_alquiler_elemento` decimal(10,2) DEFAULT NULL COMMENT 'Precio por día que pagamos al proveedor por alquilar este elemento',
  `id_forma_pago_alquiler_elemento` int UNSIGNED DEFAULT NULL COMMENT 'Forma de pago acordada con el proveedor para el alquiler (solo si es_propio = FALSE)',
  `observaciones_alquiler_elemento` text COMMENT 'Condiciones especiales de alquiler: mínimo de días, restricciones, contacto, etc.',
  `created_at_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `peso_elemento` decimal(10,3) DEFAULT NULL COMMENT 'Peso en kilogramos (NULL=no aplica o desconocido)'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `elemento`
--

INSERT INTO `elemento` (`id_elemento`, `id_articulo_elemento`, `id_marca_elemento`, `modelo_elemento`, `codigo_elemento`, `codigo_barras_elemento`, `descripcion_elemento`, `numero_serie_elemento`, `id_estado_elemento`, `nave_elemento`, `pasillo_columna_elemento`, `altura_elemento`, `fecha_compra_elemento`, `precio_compra_elemento`, `fecha_alta_elemento`, `fecha_fin_garantia_elemento`, `proximo_mantenimiento_elemento`, `observaciones_elemento`, `activo_elemento`, `es_propio_elemento`, `id_proveedor_compra_elemento`, `id_proveedor_alquiler_elemento`, `precio_dia_alquiler_elemento`, `id_forma_pago_alquiler_elemento`, `observaciones_alquiler_elemento`, `created_at_elemento`, `updated_at_elemento`, `peso_elemento`) VALUES
(1, 21, 1, 'SH-78', 'MIC-INAL-001-001', '123456789', 'Microfono Senheiser SH-78', '123456789', 1, '1', 'a-5', '1', NULL, 0.00, NULL, NULL, NULL, 'Prueba de observaciones', 1, 1, NULL, NULL, NULL, NULL, NULL, '2025-11-25 07:18:30', '2026-02-15 11:37:50', 2.500),
(2, 21, 1, 'SH-79', 'MIC-INAL-001-002', '123456778', 'Microfono Senheiser SH-79', '112345678', 1, '1', 'a-5', '1', '2025-11-29', 1020.00, '2025-11-29', '2026-11-29', '2025-12-20', 'Prueba de observaciones', 1, 1, 1, NULL, NULL, NULL, NULL, '2025-12-02 15:49:39', '2026-02-15 12:00:56', 2.500),
(3, 25, 9, 'LED-PROX-1200', 'LED-PANEL-P3-001', '121333311', 'Módulo LED Philips Lumileds LED-PROX-1200', '332444228', 1, '1', 'a-2', '1', '2025-12-02', 217.00, '2025-12-02', '2026-12-02', '2026-02-01', 'Prueba de observaciones', 1, 1, NULL, NULL, NULL, NULL, NULL, '2025-12-02 16:04:40', '2025-12-02 16:04:40', NULL),
(4, 25, 9, 'LED-PROX-1200', 'LED-PANEL-P3-002', '224111111', 'Módulo LED Philips Lumileds LED-PROX-1200', '123333333', 1, '1', 'a2', '1', '2024-12-01', 217.00, '2024-12-01', '2025-12-01', '2025-11-01', 'Prueba de observaciones', 1, 1, NULL, NULL, NULL, NULL, NULL, '2025-12-02 16:13:59', '2025-12-02 16:13:59', NULL),
(5, 21, 4, 'EB-2250U', 'MIC-INAL-001-003', '4012831029342', 'Proyector Epson EB-2250U Alquilado', 'EP-2024-0082547', 1, '', 'C-12, P-5', 'Nivel 3', NULL, NULL, NULL, NULL, NULL, 'Equipo alquilado en perfecto estado, mantiene calibración óptica. Factura disponible bajo demanda. Datos del cliente: TechSoluciones SL.', 1, 0, NULL, 1, 150.00, 5, 'Mínimo 30 días, incluye seguro de daños, contacto: +34 912 345 678', '2025-12-18 20:14:43', '2026-02-15 12:28:04', 10.000),
(6, 21, 4, NULL, 'MIC-INAL-001-004', NULL, 'Micrófono', 'GGGDFR112', 1, 'nave2', '', '', NULL, 0.00, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-11 08:33:51', '2026-02-15 12:28:20', 2.500),
(8, 114, 1, '345', 'MIC-DINA-001-001', NULL, 'Microfono mod. 345-1', NULL, 1, 'Nave 1', 'A-5', 'Planta baja', NULL, 0.00, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-15 12:23:50', '2026-02-15 12:23:50', 1.000),
(9, 114, 4, '456', 'MIC-DINA-001-002', NULL, 'Microfono Sen - 456', NULL, 1, '', '', '', NULL, 0.00, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-15 12:26:02', '2026-02-15 12:26:02', 3.000),
(10, 24, NULL, NULL, 'CABLE-XLR-10M-001', NULL, 'Cable s varios', NULL, 1, '', '', '', NULL, 0.00, NULL, NULL, NULL, NULL, 1, 1, NULL, NULL, NULL, NULL, NULL, '2026-02-15 12:41:08', '2026-02-15 12:41:08', 10.000);

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
DELIMITER $$
CREATE TRIGGER `trg_elemento_limpiar_campos_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    -- Si el elemento es ALQUILADO (es_propio_elemento = FALSE)
    -- Vaciar campos de COMPRA
    IF NEW.es_propio_elemento = FALSE THEN
        SET NEW.fecha_compra_elemento = NULL;
        SET NEW.precio_compra_elemento = NULL;
        SET NEW.fecha_alta_elemento = NULL;
        SET NEW.id_proveedor_compra_elemento = NULL;
        SET NEW.fecha_fin_garantia_elemento = NULL;
        SET NEW.proximo_mantenimiento_elemento = NULL;
    END IF;
    
    -- Si el elemento es PROPIO (es_propio_elemento = TRUE)
    -- Vaciar campos de ALQUILER
    IF NEW.es_propio_elemento = TRUE THEN
        SET NEW.id_proveedor_alquiler_elemento = NULL;
        SET NEW.precio_dia_alquiler_elemento = NULL;
        SET NEW.id_forma_pago_alquiler_elemento = NULL;
        SET NEW.observaciones_alquiler_elemento = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_limpiar_campos_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
    -- Si el elemento es ALQUILADO (es_propio_elemento = FALSE)
    -- Vaciar campos de COMPRA
    IF NEW.es_propio_elemento = FALSE THEN
        SET NEW.fecha_compra_elemento = NULL;
        SET NEW.precio_compra_elemento = NULL;
        SET NEW.fecha_alta_elemento = NULL;
        SET NEW.id_proveedor_compra_elemento = NULL;
        SET NEW.fecha_fin_garantia_elemento = NULL;
        SET NEW.proximo_mantenimiento_elemento = NULL;
    END IF;
    
    -- Si el elemento es PROPIO (es_propio_elemento = TRUE)
    -- Vaciar campos de ALQUILER
    IF NEW.es_propio_elemento = TRUE THEN
        SET NEW.id_proveedor_alquiler_elemento = NULL;
        SET NEW.precio_dia_alquiler_elemento = NULL;
        SET NEW.id_forma_pago_alquiler_elemento = NULL;
        SET NEW.observaciones_alquiler_elemento = NULL;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_sync_activo_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    -- Si se está insertando como inactivo
    IF NEW.activo_elemento = FALSE THEN
        SET NEW.id_estado_elemento = 4; -- Dado de baja
    ELSE
        -- Si viene activo y el estado no está definido o es inconsistente
        IF NEW.id_estado_elemento IS NULL OR NEW.id_estado_elemento = 4 THEN
            SET NEW.id_estado_elemento = 1; -- Disponible
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_sync_activo_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
    -- Solo actuar si cambió el activo_elemento
    IF NEW.activo_elemento != OLD.activo_elemento THEN
        -- Si se está desactivando
        IF NEW.activo_elemento = FALSE THEN
            SET NEW.id_estado_elemento = 4; -- Dado de baja
        ELSE
            -- Si se está reactivando
            SET NEW.id_estado_elemento = 1; -- Disponible
        END IF;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_sync_estado_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    -- Si el estado es 4 (Dado de baja)
    IF NEW.id_estado_elemento = 4 THEN
        SET NEW.activo_elemento = FALSE;
    ELSE
        -- Cualquier otro estado
        SET NEW.activo_elemento = TRUE;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_elemento_sync_estado_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
    -- Solo actuar si cambió el id_estado_elemento
    IF NEW.id_estado_elemento != OLD.id_estado_elemento THEN
        -- Si el nuevo estado es 4 (Dado de baja)
        IF NEW.id_estado_elemento = 4 THEN
            SET NEW.activo_elemento = FALSE;
        ELSE
            -- Cualquier otro estado
            SET NEW.activo_elemento = TRUE;
        END IF;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `empresa`
--

CREATE TABLE `empresa` (
  `id_empresa` int UNSIGNED NOT NULL,
  `codigo_empresa` varchar(20) NOT NULL COMMENT 'Código único identificador (ej: MDR01, MDR02, FICTICIA)',
  `nombre_empresa` varchar(255) NOT NULL COMMENT 'Razón social completa',
  `nombre_comercial_empresa` varchar(255) DEFAULT NULL COMMENT 'Nombre comercial si difiere de la razón social',
  `ficticia_empresa` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, empresa ficticia solo para presupuestos. Si FALSE, empresa real que factura',
  `empresa_ficticia_principal` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, esta es la empresa ficticia por defecto para presupuestos',
  `nif_empresa` varchar(20) NOT NULL COMMENT 'NIF/CIF de la empresa',
  `direccion_fiscal_empresa` varchar(255) NOT NULL COMMENT 'Dirección completa del domicilio fiscal',
  `cp_fiscal_empresa` varchar(10) NOT NULL COMMENT 'Código postal',
  `poblacion_fiscal_empresa` varchar(100) NOT NULL COMMENT 'Población/Ciudad',
  `provincia_fiscal_empresa` varchar(100) NOT NULL COMMENT 'Provincia',
  `pais_fiscal_empresa` varchar(100) DEFAULT 'España' COMMENT 'País',
  `telefono_empresa` varchar(50) DEFAULT NULL COMMENT 'Teléfono principal',
  `movil_empresa` varchar(50) DEFAULT NULL COMMENT 'Teléfono móvil',
  `email_empresa` varchar(255) DEFAULT NULL COMMENT 'Email general',
  `email_facturacion_empresa` varchar(255) DEFAULT NULL COMMENT 'Email específico para facturación',
  `web_empresa` varchar(255) DEFAULT NULL COMMENT 'Sitio web',
  `iban_empresa` varchar(34) DEFAULT NULL COMMENT 'IBAN para domiciliaciones y transferencias',
  `swift_empresa` varchar(11) DEFAULT NULL COMMENT 'Código SWIFT/BIC',
  `banco_empresa` varchar(100) DEFAULT NULL COMMENT 'Nombre del banco',
  `serie_presupuesto_empresa` varchar(10) DEFAULT 'P' COMMENT 'Serie para presupuestos (ej: P, PPTO, MDR-P)',
  `numero_actual_presupuesto_empresa` int UNSIGNED DEFAULT '0' COMMENT 'Último número de presupuesto emitido',
  `serie_factura_proforma_empresa` varchar(10) DEFAULT 'FP' COMMENT 'Serie para facturas proforma (ej: FP, PRO, FPR)',
  `numero_actual_factura_proforma_empresa` int UNSIGNED DEFAULT '0' COMMENT 'Último número de factura proforma emitida',
  `dias_validez_presupuesto_empresa` int UNSIGNED NOT NULL DEFAULT '30' COMMENT 'Días de validez por defecto para los presupuestos emitidos por esta empresa',
  `serie_factura_empresa` varchar(10) DEFAULT 'F' COMMENT 'Serie para facturas (ej: F, FAC, A)',
  `numero_actual_factura_empresa` int UNSIGNED DEFAULT '0' COMMENT 'Último número de factura emitido',
  `serie_abono_empresa` varchar(10) DEFAULT 'R' COMMENT 'Serie para facturas rectificativas/abonos (ej: R, AB, REC)',
  `numero_actual_abono_empresa` int UNSIGNED DEFAULT '0' COMMENT 'Último número de abono emitido',
  `serie_abono_factura_proforma_empresa` varchar(10) DEFAULT 'RP' COMMENT 'Serie para abonos de facturas proforma',
  `numero_actual_abono_factura_proforma_empresa` int UNSIGNED DEFAULT '0' COMMENT 'Contador de abonos de facturas proforma',
  `verifactu_activo_empresa` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, esta empresa debe cumplir con VeriFact',
  `verifactu_software_empresa` varchar(100) DEFAULT NULL COMMENT 'Nombre del software de facturación',
  `verifactu_version_empresa` varchar(50) DEFAULT NULL COMMENT 'Versión del software',
  `verifactu_nif_desarrollador_empresa` varchar(20) DEFAULT NULL COMMENT 'NIF del desarrollador del software',
  `verifactu_nombre_desarrollador_empresa` varchar(255) DEFAULT NULL COMMENT 'Nombre del desarrollador',
  `verifactu_sistema_empresa` enum('online','offline') DEFAULT 'online' COMMENT 'online=envío inmediato | offline=envío diferido',
  `verifactu_url_empresa` varchar(255) DEFAULT NULL COMMENT 'URL del endpoint de VeriFact',
  `verifactu_certificado_empresa` text COMMENT 'Ruta o datos del certificado digital',
  `logotipo_empresa` varchar(255) DEFAULT NULL COMMENT 'Ruta al archivo del logotipo (para facturas y presupuestos)',
  `logotipo_pie_empresa` varchar(255) DEFAULT NULL COMMENT 'Logotipo secundario para pie de página',
  `texto_legal_factura_empresa` text COMMENT 'Texto legal que aparece en facturas (registro mercantil, etc.)',
  `texto_pie_presupuesto_empresa` text COMMENT 'Texto que aparece en el pie de los presupuestos',
  `texto_pie_factura_empresa` text COMMENT 'Texto que aparece en el pie de las facturas',
  `observaciones_empresa` text COMMENT 'Observaciones internas sobre la empresa',
  `activo_empresa` tinyint(1) DEFAULT '1' COMMENT 'Si FALSE, la empresa no estará disponible para nuevos documentos',
  `created_at_empresa` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_empresa` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `id_plantilla_default` int UNSIGNED DEFAULT NULL COMMENT 'Plantilla de impresión por defecto para esta empresa',
  `modelo_impresion_empresa` varchar(50) DEFAULT 'impresionpresupuesto_m1_es.php' COMMENT 'Nombre del archivo controller usado para imprimir presupuestos',
  `configuracion_pdf_presupuesto_empresa` text COMMENT 'Configuración JSON para personalizar PDFs de presupuesto',
  `observaciones_cabecera_presupuesto_empresa` text COMMENT 'Texto por defecto para observaciones de cabecera (español) en nuevos presupuestos',
  `observaciones_cabecera_ingles_presupuesto_empresa` text COMMENT 'Texto por defecto para observaciones de cabecera (inglés) en nuevos presupuestos',
  `mostrar_subtotales_fecha_presupuesto_empresa` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Controla si se muestran subtotales por fecha en PDF de presupuestos. TRUE=mostrar, FALSE=ocultar',
  `cabecera_firma_presupuesto_empresa` varchar(255) DEFAULT 'Departamento comercial' COMMENT 'Texto de cabecera para la firma en PDF de presupuestos',
  `mostrar_cuenta_bancaria_pdf_presupuesto_empresa` tinyint(1) DEFAULT '1' COMMENT 'Mostrar cuenta bancaria en PDF si forma pago es transferencia: 1=Sí, 0=No',
  `mostrar_kits_albaran_empresa` tinyint(1) DEFAULT '1' COMMENT 'Mostrar componentes detallados de KITs en Albarán de Carga',
  `mostrar_obs_familias_articulos_albaran_empresa` tinyint(1) DEFAULT '1' COMMENT 'Mostrar observaciones técnicas de familias/artículos en Albarán de Carga',
  `mostrar_obs_pie_albaran_empresa` tinyint(1) DEFAULT '1' COMMENT 'Mostrar observaciones de pie del presupuesto en Albarán de Carga',
  `obs_linea_alineadas_descripcion_empresa` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Alinear obs. de línea bajo columna Descripción en PDF: 1=Sí, 0=No (margen izq.)',
  `permitir_descuentos_lineas_empresa` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Si 0: %Descuento bloqueado a 0 en líneas y oculto en PDF. Si 1 (default): comportamiento estándar.'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Gestión de empresas del grupo para facturación y presupuestos';

--
-- Volcado de datos para la tabla `empresa`
--

INSERT INTO `empresa` (`id_empresa`, `codigo_empresa`, `nombre_empresa`, `nombre_comercial_empresa`, `ficticia_empresa`, `empresa_ficticia_principal`, `nif_empresa`, `direccion_fiscal_empresa`, `cp_fiscal_empresa`, `poblacion_fiscal_empresa`, `provincia_fiscal_empresa`, `pais_fiscal_empresa`, `telefono_empresa`, `movil_empresa`, `email_empresa`, `email_facturacion_empresa`, `web_empresa`, `iban_empresa`, `swift_empresa`, `banco_empresa`, `serie_presupuesto_empresa`, `numero_actual_presupuesto_empresa`, `serie_factura_proforma_empresa`, `numero_actual_factura_proforma_empresa`, `dias_validez_presupuesto_empresa`, `serie_factura_empresa`, `numero_actual_factura_empresa`, `serie_abono_empresa`, `numero_actual_abono_empresa`, `serie_abono_factura_proforma_empresa`, `numero_actual_abono_factura_proforma_empresa`, `verifactu_activo_empresa`, `verifactu_software_empresa`, `verifactu_version_empresa`, `verifactu_nif_desarrollador_empresa`, `verifactu_nombre_desarrollador_empresa`, `verifactu_sistema_empresa`, `verifactu_url_empresa`, `verifactu_certificado_empresa`, `logotipo_empresa`, `logotipo_pie_empresa`, `texto_legal_factura_empresa`, `texto_pie_presupuesto_empresa`, `texto_pie_factura_empresa`, `observaciones_empresa`, `activo_empresa`, `created_at_empresa`, `updated_at_empresa`, `id_plantilla_default`, `modelo_impresion_empresa`, `configuracion_pdf_presupuesto_empresa`, `observaciones_cabecera_presupuesto_empresa`, `observaciones_cabecera_ingles_presupuesto_empresa`, `mostrar_subtotales_fecha_presupuesto_empresa`, `cabecera_firma_presupuesto_empresa`, `mostrar_cuenta_bancaria_pdf_presupuesto_empresa`, `mostrar_kits_albaran_empresa`, `mostrar_obs_familias_articulos_albaran_empresa`, `mostrar_obs_pie_albaran_empresa`, `obs_linea_alineadas_descripcion_empresa`, `permitir_descuentos_lineas_empresa`) VALUES
(1, 'FICTICIA', 'MDR Audiovisuales Group', 'MDR Group', 1, 1, 'B00000000', 'C/Torno, 18 Nave 2 P.I. El Canastell', '03690', 'San Vicente del Raspeig', 'Alicante', 'España', '965 253 680', '', '', '', '', 'ES1234567890123456789012', 'BSCHESMMXXX', 'BANCO SANTANDER', 'P', 7, 'FP', 0, 30, 'F', 0, 'R', 0, 'RP', 0, 1, NULL, NULL, NULL, NULL, 'online', NULL, NULL, '/public/img/logo/Logo2.png', '/public/img/logo/Logo2.png', '', 'MDR SE RESERVA EL DERECHO DE RECONFIRMAR LA DISPONIBILIDAD DEL MATERIAL A LA CONFIRMACIÓN DEL MISMO POR PARTE DE UDS.', '', NULL, 1, '2025-12-01 18:35:27', '2026-03-05 18:37:45', 1, 'impresionpresupuesto_m1_es.php', NULL, 'Montaje de material audiovisual en régimen de alquiler', 'Installation of audiovisual equipment for rent', 0, 'DEPARTAMENTO COMERCIAL', 1, 0, 0, 0, 1, 0),
(3, 'MDR02', 'MDR EVENTOS Y PRODUCCIONES S.L.', 'MDR Eventos', 0, 0, 'B06654321', 'Polígono Industrial La Paz, Nave 16', '06006', 'Badajoz', 'Badajoz', 'España', '+34 924 654 321', '+34 600 654 321', 'info@mdreventos.com', 'facturacion@mdreventos.com', 'www.mdreventos.com', 'ES91 2100 0418 4502 0005 9999', 'CAIXESBBXXX', 'CaixaBank', 'PE', 0, 'FPO', 8, 30, 'FE', 28, 'RE', 9, 'RP', 3, 1, NULL, NULL, NULL, NULL, 'online', NULL, NULL, '/public/img/logo/Logo2.png', '', 'MDR EVENTOS Y PRODUCCIONES S.L. - B06654321 - Inscrita en el Registro Mercantil de Badajoz, Tomo 456, Folio 789, Hoja BA-1234. Capital Social: 5.000,00 EUR', '', 'Inscrita en el Registro Mercantil de Alicante, Libro 0, Folio 194, Hoja A-131798, Inscripción 1ª', NULL, 1, '2025-12-01 18:35:27', '2026-03-11 09:27:41', 1, 'impresionpresupuesto_m1_es.php', NULL, 'Montaje de material audiovisual en regimen de alquiler', NULL, 0, 'Departamento comercial', 1, 0, 0, 0, 0, 0);

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
(18, 'PREP', 'En preparación', '#795548', 0, NULL, 1, '2026-03-11 07:17:17', '2026-03-11 07:17:17');

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
  `es_sistema_estado_ppto` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'Gestionado automaticamente por el sistema: 1=sistema, 0=usuario',
  `created_at_estado_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_estado_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Volcado de datos para la tabla `estado_presupuesto`
--

INSERT INTO `estado_presupuesto` (`id_estado_ppto`, `codigo_estado_ppto`, `nombre_estado_ppto`, `color_estado_ppto`, `orden_estado_ppto`, `observaciones_estado_ppto`, `activo_estado_ppto`, `es_sistema_estado_ppto`, `created_at_estado_ppto`, `updated_at_estado_ppto`) VALUES
(1, 'BORRADOR', 'BORRADOR', '#0000ff', 20, 'Presupuesto todavía no enviado al cliente', 1, 1, '2025-11-14 12:35:03', '2026-02-19 16:35:36'),
(2, 'PROC', 'En Proceso', '#17a2b8', 10, 'Presupuesto en proceso de elaboración', 1, 0, '2026-02-19 19:50:38', '2026-02-19 19:50:38'),
(3, 'APROB', 'Aprobado', '#28a745', 40, 'Presupuesto aprobado por el cliente', 1, 1, '2025-11-14 12:35:03', '2026-02-19 16:35:36'),
(4, 'RECH', 'Rechazado', '#dc3545', 50, 'Presupuesto rechazado por el cliente', 1, 0, '2026-02-19 19:50:38', '2026-02-19 19:50:38'),
(5, 'CANC', 'Cancelado', '#6c757d', 60, 'Presupuesto cancelado', 1, 1, '2025-11-14 12:35:03', '2026-02-19 16:35:36'),
(8, 'ESPE-RESP', 'Esperando respuesta', '#ff9b29', 30, 'Presupuesto enviado en  espera de respuesta por parte del cliente', 1, 1, '2025-12-13 11:26:45', '2026-02-19 16:35:36');

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
  `permite_descuento_familia` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'TRUE: Los artículos de esta familia pueden tener descuento | FALSE: Familia sin descuentos (consumibles, servicios especiales, etc.)',
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

INSERT INTO `familia` (`id_familia`, `id_grupo`, `codigo_familia`, `nombre_familia`, `name_familia`, `descr_familia`, `activo_familia`, `permite_descuento_familia`, `coeficiente_familia`, `id_unidad_familia`, `imagen_familia`, `observaciones_presupuesto_familia`, `observations_budget_familia`, `orden_obs_familia`, `created_at_familia`, `updated_at_familia`) VALUES
(19, 2, 'AUD-MIC', 'Microfonía y Sonido', 'Microphones and Sound', 'Equipos de captación y procesamiento de audio profesional', 1, 1, 1, 1, 'familias/audio_microfonia.jpg', 'Todos los equipos de audio incluyen cables de conexión básicos. El técnico de sonido se cotiza por separado. Se requiere prueba de sonido 2 horas antes del evento.', 'en ingles - Todos los equipos de audio incluyen cables de conexión básicos. El técnico de sonido se cotiza por separado. Se requiere prueba de sonido 2 horas antes del evento.', 100, '2025-11-20 20:28:05', '2026-02-23 19:27:18'),
(21, 3, 'ACC-CABLE', 'Cableado y Conectores', 'Cables and Connectors', 'Cables de audio, video, datos y alimentación, conectores y adaptadores', 1, 1, 0, 2, 'familias/cables_conectores.jpg', 'Los cables se alquilan en tramos estándar. Disponemos de cables especiales bajo pedido. Se recomienda solicitar 20% adicional como backup.', '', 300, '2025-11-20 20:28:05', '2026-01-20 17:44:56'),
(22, 2, 'VID-PROY', 'Video y Proyección', 'Video and Projection', 'Proyectores, pantallas LED, procesadores de video y sistemas de visualización', 1, 1, 0, 1, 'familias/video_proyeccion.jpg', 'Los equipos de video requieren visita técnica previa para verificar condiciones de instalación. Montaje de pantallas LED requiere mínimo 24h de antelación. Incluye técnico durante el evento.', '', 50, '2025-11-20 20:28:05', '2026-01-20 17:36:08'),
(61, 4, 'FAM-LED', 'Focos LED', 'LED Lights', 'Focos LED fijos y con batería', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(62, 4, 'FAM-ROB', 'Robótica', 'Moving Heads', 'Proyectores robotizados', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(63, 4, 'FAM-CTL', 'Control Luces', 'Light Control', 'Mesas de control de iluminación', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(64, 4, 'FAM-SOP', 'Soportes Luz', 'Light Stands', 'Torres y soportes de iluminación', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(65, 3, 'FAM-PAN', 'Pantallas', 'Screens', 'Pantallas plasma y LED', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(66, 3, 'FAM-CAM', 'Cámaras', 'Cameras', 'Cámaras de video profesionales', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(67, 3, 'FAM-VID', 'Control Video', 'Video Control', 'Mezcladoras y procesadores de video', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(68, 3, 'FAM-GRA', 'Grabación', 'Recording', 'Equipos de grabación', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(69, 5, 'FAM-TAR', 'Tarimas', 'Stages', 'Tarimas modulares', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(70, 5, 'FAM-TRU', 'Truss', 'Truss', 'Estructuras de truss', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(71, 5, 'FAM-RIG', 'Rigging', 'Rigging', 'Motores y sistemas de elevación', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(72, 5, 'FAM-TEC', 'Técnicos', 'Technicians', 'Personal técnico especializado', 1, 0, 0, 5, '', 'LA JORNADA DEL PERSONAL TÉCNICO ES LA ESPECIFICADA EN EL PRESUPUESTO, EN EL CASO DE QUE ESTA SE SUPERE SE COBRARAN HORAS EXTRAS , PRECIO HORA EXTRA: 50,00.-€. DE LUNES A VIERNES Y 70,00.-€. SABADO, DOMINGO Y FESTIVO. \r\nLOS TÉCNICOS PODRÁN HACER UN MAXIMO DE 10 HORAS POR JORNADA.\r\nEL PERSONAL TÉCNICO TENDRA QUE DISPONER DE AL MENOS 1:30Hr PARA COMER, EN CASO CONTRARIO EL CLIENTE DEBERÁ HACERSE CARGO DE LA COMIDA DE ESTOS.', 'En INGLES - LA JORNADA DEL PERSONAL TÉCNICO ES LA ESPECIFICADA EN EL PRESUPUESTO, EN EL CASO DE QUE ESTA SE SUPERE SE COBRARAN HORAS EXTRAS , PRECIO HORA EXTRA: 50,00.-€. DE LUNES A VIERNES Y 70,00.-€. SABADO, DOMINGO Y FESTIVO. \r\nLOS TÉCNICOS PODRÁN HACER UN MAXIMO DE 10 HORAS POR JORNADA.\r\nEL PERSONAL TÉCNICO TENDRA QUE DISPONER DE AL MENOS 1:30Hr PARA COMER, EN CASO CONTRARIO EL CLIENTE DEBERÁ HACERSE CARGO DE LA COMIDA DE ESTOS.', 100, '2026-02-05 17:56:55', '2026-02-23 19:26:59'),
(73, 5, 'FAM-MAQ', 'Maquinaria', 'Machinery', 'Maquinaria técnica', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55'),
(74, 5, 'FAM-VAR', 'Varios', 'Miscellaneous', 'Servicios varios y accesorios', 1, 1, 1, NULL, NULL, NULL, NULL, 100, '2026-02-05 17:56:55', '2026-02-05 17:56:55');

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
(4, 'TRANS7', 'Transferencia 7 días', 1, 0.00, 100.00, 7, 0.00, 0, '', 1, '2025-11-20 07:08:35', '2025-12-09 12:23:59'),
(5, 'TRANS30', 'Transferencia 30 días', 1, 0.00, 100.00, 30, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(6, 'TRANS60', 'Transferencia 60 días', 1, 0.00, 100.00, 60, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(7, 'TRANS90', 'Transferencia 90 días', 1, 0.00, 100.00, 90, 0.00, 0, NULL, 1, '2025-11-20 07:08:35', '2025-11-20 07:08:35'),
(8, 'FRAC40_60', '40% anticipo + 60% al finalizar', 1, 0.00, 40.00, -1, 60.00, 1, 'Anticipo al firmar presupuesto, resto al finalizar evento', 1, '2025-11-20 07:10:11', '2026-02-05 11:35:02'),
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
(1, 1, 'Microfonosssss', 'foto_elemento_692ca10c2a911.png', 0, 'Prueba de observacionesssssss', 1, '2025-11-30 19:54:52', '2025-11-30 19:55:26'),
(2, 3, 'Microfono', 'foto_elemento_69380b27261c8.jpg', 0, NULL, 1, '2025-12-09 11:42:31', '2025-12-09 11:42:31');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `furgoneta`
--

CREATE TABLE `furgoneta` (
  `id_furgoneta` int UNSIGNED NOT NULL,
  `matricula_furgoneta` varchar(20) NOT NULL COMMENT 'Matrícula del vehículo',
  `marca_furgoneta` varchar(100) DEFAULT NULL COMMENT 'Marca del vehículo (Renault, Mercedes, Ford, etc.)',
  `modelo_furgoneta` varchar(100) DEFAULT NULL COMMENT 'Modelo del vehículo (Master, Sprinter, Transit, etc.)',
  `anio_furgoneta` int DEFAULT NULL COMMENT 'Año de fabricación',
  `numero_bastidor_furgoneta` varchar(50) DEFAULT NULL COMMENT 'Número de bastidor/chasis (VIN)',
  `kilometros_entre_revisiones_furgoneta` int UNSIGNED DEFAULT '10000' COMMENT 'Kilómetros entre revisiones preventivas (ej: 10000 km)',
  `fecha_proxima_itv_furgoneta` date DEFAULT NULL COMMENT 'Fecha de vencimiento de la ITV',
  `fecha_vencimiento_seguro_furgoneta` date DEFAULT NULL COMMENT 'Fecha de vencimiento del seguro',
  `compania_seguro_furgoneta` varchar(255) DEFAULT NULL COMMENT 'Compañía aseguradora',
  `numero_poliza_seguro_furgoneta` varchar(100) DEFAULT NULL COMMENT 'Número de póliza del seguro',
  `capacidad_carga_kg_furgoneta` decimal(10,2) DEFAULT NULL COMMENT 'Capacidad de carga en kilogramos',
  `capacidad_carga_m3_furgoneta` decimal(10,2) DEFAULT NULL COMMENT 'Capacidad de carga en metros cúbicos',
  `tipo_combustible_furgoneta` varchar(50) DEFAULT NULL COMMENT 'Tipo de combustible (Diesel, Gasolina, Eléctrico, Híbrido)',
  `consumo_medio_furgoneta` decimal(5,2) DEFAULT NULL COMMENT 'Consumo medio en L/100km',
  `taller_habitual_furgoneta` varchar(255) DEFAULT NULL COMMENT 'Taller donde se realizan los mantenimientos habitualmente',
  `telefono_taller_furgoneta` varchar(50) DEFAULT NULL COMMENT 'Teléfono del taller habitual',
  `estado_furgoneta` enum('operativa','taller','baja') DEFAULT 'operativa' COMMENT 'Estado actual del vehículo',
  `observaciones_furgoneta` text COMMENT 'Observaciones generales sobre el vehículo',
  `activo_furgoneta` tinyint(1) DEFAULT '1' COMMENT 'TRUE: Vehículo activo | FALSE: Vehículo dado de baja',
  `created_at_furgoneta` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_furgoneta` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Vehículos de la empresa (furgonetas de transporte)';

--
-- Volcado de datos para la tabla `furgoneta`
--

INSERT INTO `furgoneta` (`id_furgoneta`, `matricula_furgoneta`, `marca_furgoneta`, `modelo_furgoneta`, `anio_furgoneta`, `numero_bastidor_furgoneta`, `kilometros_entre_revisiones_furgoneta`, `fecha_proxima_itv_furgoneta`, `fecha_vencimiento_seguro_furgoneta`, `compania_seguro_furgoneta`, `numero_poliza_seguro_furgoneta`, `capacidad_carga_kg_furgoneta`, `capacidad_carga_m3_furgoneta`, `tipo_combustible_furgoneta`, `consumo_medio_furgoneta`, `taller_habitual_furgoneta`, `telefono_taller_furgoneta`, `estado_furgoneta`, `observaciones_furgoneta`, `activo_furgoneta`, `created_at_furgoneta`, `updated_at_furgoneta`) VALUES
(1, '123-ABC', 'Renault', 'Master', 2020, NULL, 10000, '2026-10-10', '2026-10-10', 'Mapfre', 'PL-123456', 1500.00, 12.50, 'Gasolina', 9.00, 'Prueba de nombre de taller', NULL, 'operativa', 'La segunda no entra bien. Tener cuidadosssss', 1, '2025-12-23 09:09:11', '2025-12-23 18:34:19'),
(2, 'ABCD - 1254444', 'Ford', 'Sprinter', 2010, NULL, 2000, '2025-12-31', '2026-01-09', 'AXA', 'POL-1258888888', 100.00, 12.00, 'Diésel', 8.50, 'García', '965262384', 'operativa', 'Cuidado con la marcha atrás que no funciona. Jajajajaja ', 1, '2025-12-23 11:02:47', '2025-12-23 11:35:39'),
(3, 'SDFSDFSDFSDF', 'sdfsdfsdfsdf', 'sdfsdfsdf', 2020, 'qweewerw53654684erwe', 10000, '2026-02-13', '2026-02-13', 'Mapfre', NULL, 1515.00, 34534.00, 'Diesel', 34.00, 'Prueba de nombre de taller', '965262384', 'taller', 'Prueba de observaciones', 1, '2025-12-23 18:01:18', '2025-12-23 18:01:18');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `furgoneta_mantenimiento`
--

CREATE TABLE `furgoneta_mantenimiento` (
  `id_mantenimiento` int UNSIGNED NOT NULL,
  `id_furgoneta` int UNSIGNED NOT NULL COMMENT 'Furgoneta a la que pertenece este mantenimiento',
  `fecha_mantenimiento` date NOT NULL COMMENT 'Fecha en que se realizó el mantenimiento',
  `tipo_mantenimiento` enum('revision','reparacion','itv','neumaticos','otros') NOT NULL COMMENT 'Tipo de mantenimiento realizado',
  `descripcion_mantenimiento` text NOT NULL COMMENT 'Descripción detallada del trabajo realizado',
  `kilometraje_mantenimiento` int UNSIGNED DEFAULT NULL COMMENT 'Kilometraje del vehículo en el momento del mantenimiento',
  `costo_mantenimiento` decimal(10,2) DEFAULT '0.00' COMMENT 'Coste total del mantenimiento/reparación',
  `numero_factura_mantenimiento` varchar(100) DEFAULT NULL COMMENT 'Número de factura del taller',
  `taller_mantenimiento` varchar(255) DEFAULT NULL COMMENT 'Nombre del taller que realizó el trabajo',
  `telefono_taller_mantenimiento` varchar(50) DEFAULT NULL COMMENT 'Teléfono del taller',
  `direccion_taller_mantenimiento` varchar(255) DEFAULT NULL COMMENT 'Dirección del taller',
  `resultado_itv` enum('favorable','desfavorable','negativa') DEFAULT NULL COMMENT 'Resultado de la ITV (solo si tipo_mantenimiento = itv)',
  `fecha_proxima_itv` date DEFAULT NULL COMMENT 'Nueva fecha de ITV (si aplica)',
  `garantia_hasta_mantenimiento` date DEFAULT NULL COMMENT 'Fecha hasta la que cubre la garantía del trabajo',
  `observaciones_mantenimiento` text COMMENT 'Observaciones adicionales sobre el mantenimiento',
  `activo_mantenimiento` tinyint(1) DEFAULT '1' COMMENT 'TRUE: Registro activo | FALSE: Registro anulado',
  `created_at_mantenimiento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_mantenimiento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Historial de mantenimientos y reparaciones de furgonetas';

--
-- Volcado de datos para la tabla `furgoneta_mantenimiento`
--

INSERT INTO `furgoneta_mantenimiento` (`id_mantenimiento`, `id_furgoneta`, `fecha_mantenimiento`, `tipo_mantenimiento`, `descripcion_mantenimiento`, `kilometraje_mantenimiento`, `costo_mantenimiento`, `numero_factura_mantenimiento`, `taller_mantenimiento`, `telefono_taller_mantenimiento`, `direccion_taller_mantenimiento`, `resultado_itv`, `fecha_proxima_itv`, `garantia_hasta_mantenimiento`, `observaciones_mantenimiento`, `activo_mantenimiento`, `created_at_mantenimiento`, `updated_at_mantenimiento`) VALUES
(1, 1, '2025-12-23', 'revision', 'Revisión rutinaria por KM', 12500, 1200.00, NULL, 'Taller Hermanos GarcíA', NULL, NULL, NULL, NULL, '2026-01-28', NULL, 1, '2025-12-24 09:01:05', '2025-12-24 11:10:54');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `furgoneta_registro_kilometraje`
--

CREATE TABLE `furgoneta_registro_kilometraje` (
  `id_registro_km` int UNSIGNED NOT NULL,
  `id_furgoneta` int UNSIGNED NOT NULL COMMENT 'Furgoneta a la que pertenece este registro',
  `fecha_registro_km` date NOT NULL COMMENT 'Fecha en que se realizó la lectura',
  `kilometraje_registrado_km` int UNSIGNED NOT NULL COMMENT 'Kilometraje leído en el cuentakilómetros',
  `tipo_registro_km` enum('manual','revision','itv','evento') DEFAULT 'manual' COMMENT 'Origen del registro: manual, revisión, ITV, evento',
  `observaciones_registro_km` text COMMENT 'Observaciones sobre este registro específico',
  `created_at_registro_km` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_registro_km` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Registro histórico de kilometraje de furgonetas';

--
-- Volcado de datos para la tabla `furgoneta_registro_kilometraje`
--

INSERT INTO `furgoneta_registro_kilometraje` (`id_registro_km`, `id_furgoneta`, `fecha_registro_km`, `kilometraje_registrado_km`, `tipo_registro_km`, `observaciones_registro_km`, `created_at_registro_km`, `updated_at_registro_km`) VALUES
(1, 1, '2026-01-01', 100, 'revision', 'Prueba de observaciones', '2026-01-03 17:33:16', '2026-01-03 18:51:09'),
(2, 1, '2026-01-02', 150, 'revision', NULL, '2026-01-03 18:18:10', '2026-01-03 18:51:14'),
(3, 1, '2026-01-03', 160, 'manual', NULL, '2026-01-03 18:42:10', '2026-01-03 18:42:10');

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
(9, 'MOB', 'Mobiliario', 'Sillas, mesas, vallas y elementos de evento', 'Incluye mobiliario para eventos, vallas de seguridad, moquetas, tarimas, atriles, stands y elementos decorativos o funcionales para eventos.', 1, '2025-11-20 20:26:48', '2025-11-20 20:26:48'),
(10, 'GRP-001', 'AUDIO', 'Equipamiento de audio y sonido', NULL, 1, '2026-02-05 17:50:38', '2026-02-05 17:50:38'),
(11, 'GRP-002', 'ILUMINACIÓN', 'Equipamiento de iluminación', NULL, 1, '2026-02-05 17:50:38', '2026-02-05 17:50:38'),
(12, 'GRP-003', 'VIDEO', 'Equipamiento de video y proyección', NULL, 1, '2026-02-05 17:50:38', '2026-02-05 17:50:38'),
(13, 'GRP-004', 'ESTRUCTURAS', 'Tarimas, truss y estructuras', NULL, 1, '2026-02-05 17:50:38', '2026-02-05 17:50:38'),
(14, 'GRP-005', 'SERVICIOS', 'Servicios técnicos y personal', NULL, 1, '2026-02-05 17:50:38', '2026-02-05 17:50:38');

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
(3, 'SIN_IVA', 0.00, 'Aplicable a empresas intracomunitarias', 1, NULL, '2025-11-21 06:16:24', '2025-11-21 06:16:24'),
(4, 'IVAESPECIAL', 10.25, 'Es uan tasa inventada para hacer pruebas.', 1, NULL, '2026-01-24 17:49:23', '2026-01-24 17:49:23');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `kit`
--

CREATE TABLE `kit` (
  `id_kit` int UNSIGNED NOT NULL,
  `cantidad_kit` int UNSIGNED NOT NULL DEFAULT '1' COMMENT 'Cantidad del artículo componente en el kit',
  `id_articulo_maestro` int UNSIGNED NOT NULL COMMENT 'Artículo principal (el KIT)',
  `id_articulo_componente` int UNSIGNED NOT NULL COMMENT 'Artículo que forma parte del kit',
  `activo_kit` tinyint(1) NOT NULL DEFAULT '1',
  `created_at_kit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_kit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Composición de artículos tipo KIT';

--
-- Volcado de datos para la tabla `kit`
--

INSERT INTO `kit` (`id_kit`, `cantidad_kit`, `id_articulo_maestro`, `id_articulo_componente`, `activo_kit`, `created_at_kit`, `updated_at_kit`) VALUES
(4, 10, 21, 24, 1, '2026-01-03 16:30:29', '2026-02-15 12:38:49'),
(7, 12, 116, 119, 1, '2026-02-06 12:16:40', '2026-02-06 12:16:40'),
(8, 12, 116, 120, 1, '2026-02-06 12:16:58', '2026-02-06 12:16:58'),
(9, 1, 116, 42, 1, '2026-02-11 08:29:20', '2026-02-11 08:29:20'),
(10, 4, 21, 44, 1, '2026-03-04 11:28:53', '2026-03-04 11:28:53');

--
-- Disparadores `kit`
--
DELIMITER $$
CREATE TRIGGER `trg_kit_before_insert` BEFORE INSERT ON `kit` FOR EACH ROW BEGIN
    DECLARE v_es_kit_maestro TINYINT(1);
    DECLARE v_es_kit_componente TINYINT(1);
    
    -- ----------------------------------------
    -- VALIDACIÓN 1: Auto-referencia
    -- ----------------------------------------
    IF NEW.id_articulo_maestro = NEW.id_articulo_componente THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Un artículo no puede ser componente de sí mismo';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 2: El maestro debe tener es_kit_articulo = 1
    -- ----------------------------------------
    SELECT es_kit_articulo INTO v_es_kit_maestro
    FROM articulo 
    WHERE id_articulo = NEW.id_articulo_maestro;
    
    IF v_es_kit_maestro = 0 OR v_es_kit_maestro IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: El artículo maestro debe tener es_kit_articulo = 1';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 3: El componente NO puede ser un KIT (evitar recursividad)
    -- ----------------------------------------
    SELECT es_kit_articulo INTO v_es_kit_componente
    FROM articulo 
    WHERE id_articulo = NEW.id_articulo_componente;
    
    IF v_es_kit_componente = 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Un componente no puede ser a su vez un KIT (evitar recursividad)';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 4: Cantidad debe ser positiva
    -- ----------------------------------------
    IF NEW.cantidad_kit <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: La cantidad debe ser mayor a 0';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_kit_before_update` BEFORE UPDATE ON `kit` FOR EACH ROW BEGIN
    DECLARE v_es_kit_maestro TINYINT(1);
    DECLARE v_es_kit_componente TINYINT(1);
    
    -- ----------------------------------------
    -- VALIDACIÓN 1: Auto-referencia
    -- ----------------------------------------
    IF NEW.id_articulo_maestro = NEW.id_articulo_componente THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Un artículo no puede ser componente de sí mismo';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 2: El maestro debe tener es_kit_articulo = 1
    -- ----------------------------------------
    SELECT es_kit_articulo INTO v_es_kit_maestro
    FROM articulo 
    WHERE id_articulo = NEW.id_articulo_maestro;
    
    IF v_es_kit_maestro = 0 OR v_es_kit_maestro IS NULL THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: El artículo maestro debe tener es_kit_articulo = 1';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 3: El componente NO puede ser un KIT
    -- ----------------------------------------
    SELECT es_kit_articulo INTO v_es_kit_componente
    FROM articulo 
    WHERE id_articulo = NEW.id_articulo_componente;
    
    IF v_es_kit_componente = 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Un componente no puede ser a su vez un KIT (evitar recursividad)';
    END IF;
    
    -- ----------------------------------------
    -- VALIDACIÓN 4: Cantidad debe ser positiva
    -- ----------------------------------------
    IF NEW.cantidad_kit <= 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: La cantidad debe ser mayor a 0';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea_presupuesto`
--

CREATE TABLE `linea_presupuesto` (
  `id_linea_ppto` int UNSIGNED NOT NULL,
  `id_version_presupuesto` int UNSIGNED NOT NULL COMMENT 'FK: Versión del presupuesto a la que pertenece esta línea',
  `id_articulo` int UNSIGNED DEFAULT NULL COMMENT 'FK: Artículo original (NULL para líneas tipo texto/sección)',
  `id_linea_padre` int UNSIGNED DEFAULT NULL COMMENT 'FK: Línea padre para componentes de KIT (NULL si es línea principal)',
  `id_ubicacion` int UNSIGNED DEFAULT NULL COMMENT 'FK: Ubicación específica de montaje',
  `id_coeficiente` int UNSIGNED DEFAULT NULL COMMENT 'FK: Coeficiente reductor aplicado',
  `id_impuesto` int DEFAULT NULL COMMENT 'FK: Tipo de impuesto/IVA aplicado (INT sin UNSIGNED por compatibilidad con tabla impuesto)',
  `numero_linea_ppto` int NOT NULL COMMENT 'Número de línea visual en el presupuesto',
  `tipo_linea_ppto` enum('articulo','kit','componente_kit','seccion','texto','subtotal') COLLATE utf8mb4_spanish2_ci DEFAULT 'articulo' COMMENT 'Tipo de línea',
  `nivel_jerarquia` tinyint DEFAULT '0' COMMENT 'Nivel de anidamiento: 0=principal, 1=componente KIT, 2=sub-componente',
  `orden_linea_ppto` int DEFAULT '0' COMMENT 'Orden de visualización',
  `codigo_linea_ppto` varchar(50) COLLATE utf8mb4_spanish2_ci DEFAULT NULL COMMENT 'Código del artículo',
  `descripcion_linea_ppto` text COLLATE utf8mb4_spanish2_ci NOT NULL COMMENT 'Descripción de la línea',
  `fecha_montaje_linea_ppto` date DEFAULT NULL COMMENT 'Fecha orientativa de montaje (informativa para planning)',
  `fecha_desmontaje_linea_ppto` date DEFAULT NULL COMMENT 'Fecha orientativa de desmontaje (informativa para planning)',
  `fecha_inicio_linea_ppto` date DEFAULT NULL COMMENT 'Fecha REAL de inicio para el cobro (heredada pero modificable)',
  `fecha_fin_linea_ppto` date DEFAULT NULL COMMENT 'Fecha REAL de fin para el cobro (heredada pero modificable)',
  `cantidad_linea_ppto` decimal(10,2) DEFAULT '1.00' COMMENT 'Cantidad de unidades',
  `precio_unitario_linea_ppto` decimal(10,2) DEFAULT '0.00' COMMENT 'Precio unitario base (heredado del artículo pero modificable)',
  `descuento_linea_ppto` decimal(5,2) DEFAULT '0.00' COMMENT 'Descuento porcentual específico de la línea (%)',
  `aplicar_coeficiente_linea_ppto` tinyint(1) DEFAULT '0' COMMENT 'Si se aplica coeficiente reductor (Sí/No)',
  `valor_coeficiente_linea_ppto` decimal(10,2) DEFAULT NULL COMMENT 'Valor del coeficiente aplicado',
  `jornadas_linea_ppto` int DEFAULT NULL COMMENT 'Número de jornadas para cálculo del coeficiente',
  `porcentaje_iva_linea_ppto` decimal(5,2) DEFAULT '21.00' COMMENT 'Porcentaje de IVA aplicado',
  `observaciones_linea_ppto` text COLLATE utf8mb4_spanish2_ci COMMENT 'Observaciones específicas de esta línea',
  `observaciones_linea_ppto_en` text COLLATE utf8mb4_spanish2_ci,
  `mostrar_obs_articulo_linea_ppto` tinyint(1) DEFAULT '1' COMMENT 'Si mostrar las observaciones del artículo original',
  `ocultar_detalle_kit_linea_ppto` tinyint(1) DEFAULT '0' COMMENT 'TRUE: no mostrar desglose del KIT | FALSE: mostrar componentes',
  `mostrar_en_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si se muestra al cliente en el presupuesto',
  `es_opcional` tinyint(1) DEFAULT '0' COMMENT 'Si es una línea opcional',
  `activo_linea_ppto` tinyint(1) DEFAULT '1' COMMENT 'Estado activo/inactivo',
  `created_at_linea_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
  `updated_at_linea_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci COMMENT='Líneas de detalle de versiones de presupuesto con soporte para KITs jerárquicos';

--
-- Volcado de datos para la tabla `linea_presupuesto`
--

INSERT INTO `linea_presupuesto` (`id_linea_ppto`, `id_version_presupuesto`, `id_articulo`, `id_linea_padre`, `id_ubicacion`, `id_coeficiente`, `id_impuesto`, `numero_linea_ppto`, `tipo_linea_ppto`, `nivel_jerarquia`, `orden_linea_ppto`, `codigo_linea_ppto`, `descripcion_linea_ppto`, `fecha_montaje_linea_ppto`, `fecha_desmontaje_linea_ppto`, `fecha_inicio_linea_ppto`, `fecha_fin_linea_ppto`, `cantidad_linea_ppto`, `precio_unitario_linea_ppto`, `descuento_linea_ppto`, `aplicar_coeficiente_linea_ppto`, `valor_coeficiente_linea_ppto`, `jornadas_linea_ppto`, `porcentaje_iva_linea_ppto`, `observaciones_linea_ppto`, `observaciones_linea_ppto_en`, `mostrar_obs_articulo_linea_ppto`, `ocultar_detalle_kit_linea_ppto`, `mostrar_en_presupuesto`, `es_opcional`, `activo_linea_ppto`, `created_at_linea_ppto`, `updated_at_linea_ppto`) VALUES
(42, 4, 21, NULL, NULL, NULL, 2, 1, 'articulo', 0, 0, 'MIC-INAL-001', 'Micrófono inalámbrico', '2026-02-01', '2026-02-05', '2026-02-01', '2026-02-05', 1.00, 25.00, 0.00, 1, 4.75, 5, 10.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-01-30 18:56:01', '2026-02-13 11:43:24'),
(43, 4, 21, NULL, NULL, NULL, 2, 1, 'articulo', 0, 0, 'MIC-INAL-001', 'Micrófono inalámbrico', '2026-02-01', '2026-02-05', '2026-02-01', '2026-02-05', 1.00, 25.00, 0.00, 1, 4.75, 5, 10.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-02 13:40:05', '2026-02-02 13:40:05'),
(48, 3, 74, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-001', 'TARIMA MODULAR 3X2X040 MOQUETA NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 1150.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:20:45', '2026-02-23 16:19:17'),
(49, 3, 116, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'EQUIP-MEGA-001', 'Equipo de megafonía 12 cajas (S/TCO)', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 445.00, 0.00, 0, NULL, NULL, 21.00, 'esto sale', NULL, 1, 0, 1, 0, 1, '2026-02-05 18:25:52', '2026-02-23 16:20:40'),
(50, 3, 117, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:31:50', '2026-02-23 16:19:24'),
(51, 3, 117, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:33:18', '2026-02-23 16:19:29'),
(52, 3, 118, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'SPOTIFY', 'REPRODUCTOR SPOTIFY', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:33:51', '2026-02-23 16:19:34'),
(53, 3, 38, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-001', 'FOCO PAR LED RGB/W BATERIA + WIFI', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 10.00, 30.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:34:37', '2026-02-23 16:19:38'),
(54, 3, 39, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-002', 'FOCO PAR LED RGB PC90', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 4.00, 19.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:35:45', '2026-02-23 16:19:43'),
(55, 3, 51, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-001', 'TORRE ELEVABLE 3 mts. MANUAL 30 kg CARGA / NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 4.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:37:07', '2026-02-23 16:19:48'),
(56, 3, 40, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-003', 'FOCO RECORTE DE LED 20/45', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 60.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:39:35', '2026-02-23 16:19:52'),
(57, 3, 51, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-001', 'TORRE ELEVABLE 3 mts. MANUAL 30 kg CARGA / NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:40:10', '2026-02-23 16:19:56'),
(58, 3, 48, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'CTR-001', 'MESA DE LUCES EUROLITE DMX OPERATOR 192', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:41:18', '2026-02-23 16:20:01'),
(59, 3, 91, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'TEC-001', 'TÉCNICO AUDIOVISUALES (MAX. 8 HORAS)', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 2.00, 220.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:43:56', '2026-02-11 12:03:38'),
(60, 3, 41, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-004', 'FOCO PAR LED SPOT BATERÍA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 24.00, 25.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:44:50', '2026-02-23 16:20:06'),
(61, 3, 52, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-002', 'PIE CON PEANA REDONDA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 12.00, 12.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:45:28', '2026-02-23 16:20:11'),
(62, 3, 114, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-DINA-001', 'Micrófono Dinámico SM58', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 12.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-05 18:49:43', '2026-02-23 16:20:15'),
(64, 3, 75, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-002', 'TARIMA MODULAR 16,5X2,5X0,60 + INTEGRADA', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 3760.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-06 09:37:08', '2026-02-23 16:20:19'),
(65, 3, 76, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-003', 'TARIMA MODULAR 22X2,50X1,50 PARA LED', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 1050.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-06 09:39:35', '2026-02-23 16:20:24'),
(66, 3, 77, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-004', 'TARIMA MODULAR 2X2X150 CON MOQUETA NEGRA', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 6.00, 250.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-06 09:40:15', '2026-02-23 16:20:28'),
(67, 3, 99, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'VAR-001', 'MONTAJE Y DESMONTAJE', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 2050.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-06 09:41:01', '2026-02-06 09:41:01'),
(68, 3, 54, NULL, 8, NULL, NULL, 1, 'articulo', 0, 0, 'PAN-001', 'PANTALLA PLASMA 65\"', '2026-01-28', '2026-01-31', '2026-01-30', '2026-01-31', 3.00, 285.00, 0.00, 1, 4.75, 2, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-11 09:35:26', '2026-02-11 09:37:18'),
(69, 3, 48, NULL, 6, NULL, NULL, 2, 'articulo', 0, 1, 'CTR-001', 'MESA DE LUCES EUROLITE DMX OPERATOR 192', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-31', 2.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-11 11:32:29', '2026-02-23 16:20:33'),
(70, 3, 91, NULL, 8, NULL, NULL, 1, 'articulo', 0, 0, 'TEC-001', 'TÉCNICO AUDIOVISUALES (MAX. 8 HORAS)', '2026-01-27', '2026-01-28', '2026-01-27', '2026-01-28', 3.00, 220.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-11 11:45:09', '2026-02-11 11:45:52'),
(72, 5, 91, NULL, 4, NULL, NULL, 1, 'articulo', 0, 0, 'TEC-001', 'TÉCNICO AUDIOVISUALES (MAX. 8 HORAS)', '2026-02-12', '2026-02-15', '2026-02-12', '2026-02-15', 1.00, 220.00, 0.00, 0, NULL, NULL, 21.00, NULL, 'Prueba de montaje el dia anterior', 1, 0, 1, 0, 1, '2026-02-11 12:12:43', '2026-03-05 11:53:03'),
(73, 5, 74, NULL, 7, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-001', 'TARIMA MODULAR 3X2X040 MOQUETA NEGRA', '2026-02-12', '2026-02-15', '2026-02-12', '2026-02-12', 1.00, 1150.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-11 12:13:09', '2026-02-20 08:52:52'),
(74, 3, 117, NULL, 9, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-27', '2026-01-31', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, 'cfr4cfre', NULL, 1, 0, 1, 0, 1, '2026-02-12 10:30:26', '2026-02-12 10:30:26'),
(76, 3, 116, NULL, 4, NULL, 1, 1, 'articulo', 0, 0, 'EQUIP-MEGA-001', 'Equipo de megafonía 12 cajas (S/TCO)', '2026-01-27', '2026-01-31', '2026-01-27', '2026-01-31', 1.00, 445.00, 0.00, 1, 4.75, 5, 21.00, 'El cliente será responsable de facilitar los anclajes para las cajas acústicas.', 'En inglés: El cliente será responsable de facilitar los anclajes para las cajas acústicas.', 1, 0, 1, 0, 1, '2026-02-13 16:46:46', '2026-02-26 16:47:02'),
(78, 6, 21, NULL, NULL, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-001', 'Micrófono inalámbrico', '2026-02-20', '2026-02-26', '2026-02-20', '2026-02-26', 1.00, 25.00, 0.00, 1, 7.25, 7, 0.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-14 10:22:48', '2026-02-14 10:22:48'),
(94, 14, 21, NULL, NULL, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-001', 'Micrófono inalámbrico', '2026-02-20', '2026-02-26', '2026-02-20', '2026-02-26', 1.00, 25.00, 100.00, 1, 7.25, 7, 0.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:19:05', '2026-02-26 17:41:03'),
(95, 14, 75, NULL, NULL, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-002', 'TARIMA MODULAR 16,5X2,5X0,60 + INTEGRADA', '2026-02-20', '2026-02-26', '2026-02-20', '2026-02-26', 1.00, 3760.00, 0.00, 1, 7.25, 7, 0.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:19:06', '2026-02-21 10:19:06'),
(96, 15, 41, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-004', 'FOCO PAR LED SPOT BATERÍA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 24.00, 25.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:11', '2026-02-26 16:47:06'),
(97, 15, 116, NULL, 4, NULL, 1, 1, 'articulo', 0, 0, 'EQUIP-MEGA-001', 'Equipo de megafonía 12 cajas (S/TCO)', '2026-01-27', '2026-01-31', '2026-01-27', '2026-01-31', 1.00, 445.00, 10.00, 1, 4.75, 5, 21.00, 'El cliente será responsable de facilitar los anclajes para las cajas acústicas.', NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:11', '2026-03-01 10:59:21'),
(98, 15, 117, NULL, 9, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-27', '2026-01-31', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, 'cfr4cfre', NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:11', '2026-02-21 10:20:11'),
(99, 15, 91, NULL, 8, NULL, NULL, 1, 'articulo', 0, 0, 'TEC-001', 'TÉCNICO AUDIOVISUALES (MAX. 8 HORAS)', '2026-01-27', '2026-01-28', '2026-01-27', '2026-01-28', 3.00, 220.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:11', '2026-02-21 10:20:11'),
(100, 15, 54, NULL, 8, NULL, NULL, 1, 'articulo', 0, 0, 'PAN-001', 'PANTALLA PLASMA 65\"', '2026-01-28', '2026-01-31', '2026-01-30', '2026-01-31', 3.00, 285.00, 0.00, 1, 4.75, 2, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:11', '2026-02-21 10:20:11'),
(101, 15, 99, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'VAR-001', 'MONTAJE Y DESMONTAJE', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 2050.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-21 10:20:12'),
(102, 15, 77, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-004', 'TARIMA MODULAR 2X2X150 CON MOQUETA NEGRA', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 6.00, 250.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:12'),
(103, 15, 76, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-003', 'TARIMA MODULAR 22X2,50X1,50 PARA LED', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 1050.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:16'),
(104, 15, 75, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-002', 'TARIMA MODULAR 16,5X2,5X0,60 + INTEGRADA', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 3760.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:20'),
(105, 15, 114, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-DINA-001', 'Micrófono Dinámico SM58', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 12.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:25'),
(106, 15, 52, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-002', 'PIE CON PEANA REDONDA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 12.00, 12.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:30'),
(107, 15, 74, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-001', 'TARIMA MODULAR 3X2X040 MOQUETA NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 1150.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:34'),
(108, 15, 91, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'TEC-001', 'TÉCNICO AUDIOVISUALES (MAX. 8 HORAS)', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 2.00, 220.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-21 10:20:12'),
(109, 15, 48, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'CTR-001', 'MESA DE LUCES EUROLITE DMX OPERATOR 192', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:40'),
(110, 15, 51, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-001', 'TORRE ELEVABLE 3 mts. MANUAL 30 kg CARGA / NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:44'),
(111, 15, 40, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-003', 'FOCO RECORTE DE LED 20/45', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 60.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:49'),
(112, 15, 51, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-001', 'TORRE ELEVABLE 3 mts. MANUAL 30 kg CARGA / NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 4.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:12', '2026-02-26 16:47:53'),
(113, 15, 39, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-002', 'FOCO PAR LED RGB PC90', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 4.00, 19.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:13', '2026-02-26 16:47:57'),
(114, 15, 38, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-001', 'FOCO PAR LED RGB/W BATERIA + WIFI', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 10.00, 30.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:13', '2026-02-26 16:48:00'),
(115, 15, 118, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'SPOTIFY', 'REPRODUCTOR SPOTIFY', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:13', '2026-02-26 16:56:10'),
(116, 15, 117, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:13', '2026-02-26 16:56:10'),
(117, 15, 117, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:13', '2026-02-26 16:56:10'),
(118, 15, 116, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'EQUIP-MEGA-001', 'Equipo de megafonía 12 cajas (S/TCO)', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 445.00, 0.00, 0, NULL, NULL, 21.00, 'esto sale', NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:13', '2026-02-26 16:56:10'),
(119, 15, 48, NULL, 6, NULL, NULL, 2, 'articulo', 0, 1, 'CTR-001', 'MESA DE LUCES EUROLITE DMX OPERATOR 192', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-31', 2.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-21 10:20:13', '2026-02-26 16:56:10'),
(121, 15, 122, NULL, NULL, NULL, 1, 1, 'articulo', 0, 0, 'DESCUENTO', 'Descuento', '2026-01-27', '2026-01-31', '2026-01-27', '2026-01-27', 1.00, -1100.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-25 05:50:46', '2026-02-25 05:51:11'),
(153, 14, 122, NULL, NULL, NULL, 1, 1, 'articulo', 0, 0, 'DESCUENTO', 'Descuento', '2026-02-20', '2026-02-26', '2026-02-20', '2026-02-20', 1.00, -1000.00, 0.00, 0, NULL, NULL, 0.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-02-26 17:09:17', '2026-02-26 17:09:17'),
(154, 17, 91, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'TEC-001', 'TÉCNICO AUDIOVISUALES (MAX. 8 HORAS)', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 2.00, 220.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(155, 17, 122, NULL, NULL, NULL, 1, 1, 'articulo', 0, 0, 'DESCUENTO', 'Descuento', '2026-01-27', '2026-01-31', '2026-01-27', '2026-01-27', 1.00, -900.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:50'),
(156, 17, 116, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'EQUIP-MEGA-001', 'Equipo de megafonía 12 cajas (S/TCO)', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 445.00, 0.00, 0, NULL, NULL, 21.00, 'esto sale', NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(157, 17, 117, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(158, 17, 117, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(159, 17, 118, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'SPOTIFY', 'REPRODUCTOR SPOTIFY', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(160, 17, 38, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-001', 'FOCO PAR LED RGB/W BATERIA + WIFI', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 10.00, 30.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(161, 17, 39, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-002', 'FOCO PAR LED RGB PC90', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 4.00, 19.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(162, 17, 51, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-001', 'TORRE ELEVABLE 3 mts. MANUAL 30 kg CARGA / NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 4.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(163, 17, 40, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-003', 'FOCO RECORTE DE LED 20/45', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 60.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(164, 17, 51, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-001', 'TORRE ELEVABLE 3 mts. MANUAL 30 kg CARGA / NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(165, 17, 48, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'CTR-001', 'MESA DE LUCES EUROLITE DMX OPERATOR 192', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(166, 17, 41, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'LED-004', 'FOCO PAR LED SPOT BATERÍA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 24.00, 25.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(167, 17, 74, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-001', 'TARIMA MODULAR 3X2X040 MOQUETA NEGRA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 1150.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(168, 17, 52, NULL, 3, NULL, NULL, 1, 'articulo', 0, 0, 'SOP-002', 'PIE CON PEANA REDONDA', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 12.00, 12.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(169, 17, 114, NULL, 3, NULL, 1, 1, 'articulo', 0, 0, 'MIC-DINA-001', 'Micrófono Dinámico SM58', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-29', 1.00, 12.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(170, 17, 75, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-002', 'TARIMA MODULAR 16,5X2,5X0,60 + INTEGRADA', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 3760.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(171, 17, 76, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-003', 'TARIMA MODULAR 22X2,50X1,50 PARA LED', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 1050.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(172, 17, 77, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'TAR-004', 'TARIMA MODULAR 2X2X150 CON MOQUETA NEGRA', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 6.00, 250.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(173, 17, 99, NULL, 12, NULL, NULL, 1, 'articulo', 0, 0, 'VAR-001', 'MONTAJE Y DESMONTAJE', '2026-01-27', '2026-01-31', '2026-01-30', '2026-01-30', 1.00, 2050.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(174, 17, 54, NULL, 8, NULL, NULL, 1, 'articulo', 0, 0, 'PAN-001', 'PANTALLA PLASMA 65\"', '2026-01-28', '2026-01-31', '2026-01-30', '2026-01-31', 3.00, 285.00, 0.00, 1, 4.75, 2, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(175, 17, 91, NULL, 8, NULL, NULL, 1, 'articulo', 0, 0, 'TEC-001', 'TÉCNICO AUDIOVISUALES (MAX. 8 HORAS)', '2026-01-27', '2026-01-28', '2026-01-27', '2026-01-28', 3.00, 220.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(176, 17, 117, NULL, 9, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-SEN-001', 'MICRÓFONO INALÁMBRICO SENNHEISER XSW2 MANO', '2026-01-27', '2026-01-31', '2026-01-27', '2026-01-31', 1.00, 57.00, 0.00, 0, NULL, NULL, 21.00, 'cfr4cfre', NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(177, 17, 116, NULL, 4, NULL, 1, 1, 'articulo', 0, 0, 'EQUIP-MEGA-001', 'Equipo de megafonía 12 cajas (S/TCO)', '2026-01-27', '2026-01-31', '2026-01-27', '2026-01-31', 1.00, 445.00, 10.00, 1, 4.75, 5, 21.00, 'El cliente será responsable de facilitar los anclajes para las cajas acústicas.', NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(178, 17, 48, NULL, 6, NULL, NULL, 2, 'articulo', 0, 1, 'CTR-001', 'MESA DE LUCES EUROLITE DMX OPERATOR 192', '2026-01-27', '2026-01-31', '2026-01-29', '2026-01-31', 2.00, 36.00, 0.00, 0, NULL, NULL, 21.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-01 11:03:21', '2026-03-01 11:03:21'),
(185, 18, 21, NULL, NULL, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-001', 'Micrófono inalámbrico', '2026-02-20', '2026-02-26', '2026-02-20', '2026-02-26', 1.00, 25.00, 0.00, 1, 7.25, 7, 0.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-04 12:12:07', '2026-03-04 12:12:07'),
(186, 19, 21, NULL, NULL, NULL, 1, 1, 'articulo', 0, 0, 'MIC-INAL-001', 'Micrófono inalámbrico', '2026-02-20', '2026-02-26', '2026-02-20', '2026-02-26', 1.00, 25.00, 0.00, 1, 7.25, 7, 0.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-04 12:13:04', '2026-03-04 12:13:04'),
(187, 19, 103, NULL, NULL, NULL, NULL, 1, 'articulo', 0, 0, 'VAR-005', 'PASA PAGINA', '2026-02-20', '2026-02-26', '2026-02-20', '2026-02-26', 1.00, 35.00, 0.00, 1, 7.25, 7, 0.00, NULL, NULL, 1, 0, 1, 0, 1, '2026-03-04 12:13:24', '2026-03-04 12:13:24');

--
-- Disparadores `linea_presupuesto`
--
DELIMITER $$
CREATE TRIGGER `trg_linea_presupuesto_before_delete` BEFORE DELETE ON `linea_presupuesto` FOR EACH ROW BEGIN
    DECLARE estado_version VARCHAR(20);
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar líneas de versiones que no están en borrador. El histórico debe permanecer inmutable.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_linea_presupuesto_before_update` BEFORE UPDATE ON `linea_presupuesto` FOR EACH ROW BEGIN
    DECLARE estado_version VARCHAR(20);
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = NEW.id_version_presupuesto;
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden modificar líneas de versiones que no están en borrador. Para hacer cambios, cree una nueva versión.';
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `linea_salida_almacen`
--

CREATE TABLE `linea_salida_almacen` (
  `id_linea_salida` int UNSIGNED NOT NULL,
  `id_salida_almacen` int UNSIGNED NOT NULL,
  `id_elemento` int UNSIGNED NOT NULL,
  `id_articulo` int UNSIGNED NOT NULL,
  `id_linea_ppto` int UNSIGNED DEFAULT NULL,
  `es_backup_linea_salida` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 = material de backup',
  `orden_escaneo` int UNSIGNED DEFAULT NULL,
  `fecha_escaneo_linea_salida` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones_linea_salida` text COLLATE utf8mb4_spanish_ci,
  `activo_linea_salida` tinyint(1) NOT NULL DEFAULT '1',
  `created_at_linea_salida` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_linea_salida` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Cada elemento físico escaneado en una salida de almacén';

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
(4, 'ZYYCA44', 'Dell', 'Dell (en)', 'Computadoras y soluciones tecnológicas innovadoras. XXXXXX', 1, '2025-10-30 17:47:32', '2026-03-11 12:44:05'),
(7, 'HP0011', 'Hewlett Packard', 'HP (en) - English', 'Prueba de detalles en marcas (es).', 1, '2025-10-31 16:41:12', '2025-10-31 16:41:41'),
(8, 'NINT', 'Nintendo', 'Nintendo', 'Nintendo es una empresa multinacional japonesa de entretenimiento, originaria de 1889 como fabricante de naipes y ahora dedicada al desarrollo y distribución de consolas y videojuegos.', 1, '2025-11-05 18:22:06', '2025-11-05 18:22:06'),
(9, 'PHLED', 'Philips Lumileds', 'Philips Lumileds', 'Philips Lumileds es un fabricante líder de LEDs de alto rendimiento, reconocido por su eficiencia, durabilidad y calidad lumínica en aplicaciones profesionales y automotrices.', 1, '2025-12-02 15:56:18', '2025-12-02 15:56:18');

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
-- Estructura de tabla para la tabla `movimiento_elemento_salida`
--

CREATE TABLE `movimiento_elemento_salida` (
  `id_movimiento` int UNSIGNED NOT NULL,
  `id_linea_salida` int UNSIGNED NOT NULL,
  `id_ubicacion_origen` int UNSIGNED DEFAULT NULL COMMENT 'NULL = primera colocación desde almacén',
  `id_ubicacion_destino` int UNSIGNED NOT NULL,
  `id_usuario_movimiento` int NOT NULL,
  `fecha_movimiento` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `observaciones_movimiento` text COLLATE utf8mb4_spanish_ci,
  `activo_movimiento` tinyint(1) NOT NULL DEFAULT '1',
  `created_at_movimiento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_movimiento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Historial de desplazamientos físicos de elementos en un evento';

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
-- Estructura de tabla para la tabla `pago_presupuesto`
--

CREATE TABLE `pago_presupuesto` (
  `id_pago_ppto` int UNSIGNED NOT NULL,
  `id_presupuesto` int UNSIGNED NOT NULL COMMENT 'FK a presupuesto',
  `id_documento_ppto` int UNSIGNED DEFAULT NULL COMMENT 'FK a documento_presupuesto (factura vinculada al pago, si se generó)',
  `id_empresa_pago` int UNSIGNED DEFAULT NULL COMMENT 'Empresa emisora fijada en el momento del pago',
  `tipo_pago_ppto` enum('anticipo','total','resto','devolucion') COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Tipo de pago registrado',
  `importe_pago_ppto` decimal(10,2) NOT NULL COMMENT 'Importe recibido (negativo si devolución)',
  `porcentaje_pago_ppto` decimal(5,2) DEFAULT NULL COMMENT 'Porcentaje sobre el total del presupuesto (calculado)',
  `id_metodo_pago` int UNSIGNED DEFAULT NULL COMMENT 'FK a metodo_pago (efectivo, transferencia, tarjeta, etc.)',
  `referencia_pago_ppto` varchar(100) COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT 'Número de transferencia, recibo, cheque, etc.',
  `fecha_pago_ppto` date NOT NULL COMMENT 'Fecha en que se recibió el pago',
  `fecha_valor_pago_ppto` date DEFAULT NULL COMMENT 'Fecha valor bancaria (puede diferir de fecha_pago)',
  `estado_pago_ppto` enum('pendiente','recibido','conciliado','anulado') COLLATE utf8mb4_spanish_ci DEFAULT 'recibido' COMMENT 'Estado actual del pago',
  `observaciones_pago_ppto` text COLLATE utf8mb4_spanish_ci,
  `activo_pago_ppto` tinyint(1) DEFAULT '1' COMMENT 'Soft delete: TRUE=activo, FALSE=eliminado',
  `created_at_pago_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_pago_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Registro de pagos a cuenta sobre presupuestos aprobados (anticipos, totales, resto, devoluciones)';

--
-- Volcado de datos para la tabla `pago_presupuesto`
--

INSERT INTO `pago_presupuesto` (`id_pago_ppto`, `id_presupuesto`, `id_documento_ppto`, `id_empresa_pago`, `tipo_pago_ppto`, `importe_pago_ppto`, `porcentaje_pago_ppto`, `id_metodo_pago`, `referencia_pago_ppto`, `fecha_pago_ppto`, `fecha_valor_pago_ppto`, `estado_pago_ppto`, `observaciones_pago_ppto`, `activo_pago_ppto`, `created_at_pago_ppto`, `updated_at_pago_ppto`) VALUES
(77, 16, 92, 3, 'anticipo', 1000.00, 4.34, 1, NULL, '2026-03-09', NULL, 'pendiente', NULL, 1, '2026-03-09 18:16:23', '2026-03-09 18:16:25'),
(78, 16, 93, 3, 'anticipo', 1200.00, 5.21, 1, NULL, '2026-03-09', NULL, 'pendiente', NULL, 1, '2026-03-09 18:16:50', '2026-03-09 18:16:52'),
(79, 16, 94, 3, 'total', 20849.29, 90.46, 1, NULL, '2026-03-09', NULL, 'anulado', NULL, 1, '2026-03-09 18:17:19', '2026-03-09 18:21:55'),
(80, 15, 96, 3, 'total', 26441.25, 100.00, 1, NULL, '2026-03-10', NULL, 'pendiente', NULL, 1, '2026-03-10 17:25:31', '2026-03-10 17:25:34'),
(81, 13, 97, 3, 'anticipo', 100.00, 4.07, 1, 'PDC-1000', '2026-03-11', NULL, 'anulado', NULL, 1, '2026-03-11 09:16:48', '2026-03-11 09:24:45'),
(82, 13, 98, 3, 'anticipo', 150.00, 6.11, 1, NULL, '2026-03-11', NULL, 'recibido', NULL, 1, '2026-03-11 09:26:30', '2026-03-11 09:26:32'),
(83, 13, 99, 3, 'total', 2306.30, 93.89, 1, NULL, '2026-03-11', NULL, 'anulado', NULL, 1, '2026-03-11 09:27:39', '2026-03-11 09:28:39');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `plantilla_impresion`
--

CREATE TABLE `plantilla_impresion` (
  `id_plantilla` int UNSIGNED NOT NULL,
  `codigo_plantilla` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Código único: cli_esp, cli_eng, int_esp, int_eng, etc.',
  `nombre_plantilla` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Nombre descriptivo de la plantilla',
  `descripcion_plantilla` text COLLATE utf8mb4_spanish_ci COMMENT 'Descripción detallada del uso de la plantilla',
  `idioma_plantilla` enum('es','en','fr','de','it','pt') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'es' COMMENT 'Idioma principal del template',
  `tipo_cliente_plantilla` enum('nacional','internacional') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'nacional' COMMENT 'Tipo de cliente al que está dirigida la plantilla',
  `archivo_template` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Ruta relativa al archivo PHP del template',
  `archivo_css_plantilla` varchar(255) COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT 'Archivo CSS específico de la plantilla (opcional)',
  `config_json` text COLLATE utf8mb4_spanish_ci COMMENT 'Parámetros configurables en formato JSON: {mostrar_logo, mostrar_precios, formato_fecha, etc.}',
  `mostrar_precios_plantilla` tinyint(1) DEFAULT '1' COMMENT 'Mostrar o ocultar precios en la impresión',
  `mostrar_totales_plantilla` tinyint(1) DEFAULT '1' COMMENT 'Mostrar o ocultar totales y desglose IVA',
  `mostrar_logo_plantilla` tinyint(1) DEFAULT '1' COMMENT 'Mostrar o ocultar logo de empresa',
  `mostrar_observaciones_plantilla` tinyint(1) DEFAULT '1' COMMENT 'Mostrar o ocultar sección de observaciones',
  `formato_pagina_plantilla` enum('A4','Letter','Legal') COLLATE utf8mb4_spanish_ci DEFAULT 'A4' COMMENT 'Formato de página para impresión',
  `orientacion_plantilla` enum('portrait','landscape') COLLATE utf8mb4_spanish_ci DEFAULT 'portrait' COMMENT 'Orientación de la página',
  `orden_plantilla` int UNSIGNED DEFAULT '100' COMMENT 'Orden de aparición en listas de selección',
  `activo_plantilla` tinyint(1) DEFAULT '1' COMMENT 'Soft delete: TRUE=activa, FALSE=eliminada',
  `created_at_plantilla` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación del registro',
  `updated_at_plantilla` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Plantillas personalizables para impresión de presupuestos';

--
-- Volcado de datos para la tabla `plantilla_impresion`
--

INSERT INTO `plantilla_impresion` (`id_plantilla`, `codigo_plantilla`, `nombre_plantilla`, `descripcion_plantilla`, `idioma_plantilla`, `tipo_cliente_plantilla`, `archivo_template`, `archivo_css_plantilla`, `config_json`, `mostrar_precios_plantilla`, `mostrar_totales_plantilla`, `mostrar_logo_plantilla`, `mostrar_observaciones_plantilla`, `formato_pagina_plantilla`, `orientacion_plantilla`, `orden_plantilla`, `activo_plantilla`, `created_at_plantilla`, `updated_at_plantilla`) VALUES
(1, 'cli_esp', 'Cliente España - Estándar', 'Plantilla estándar para clientes españoles. Incluye logo, datos fiscales completos, desglose de IVA y observaciones.', 'es', 'nacional', 'templates/cli_esp.php', 'base.css', '{\"moneda\": \"€\", \"formato_fecha\": \"d/m/Y\", \"separador_decimal\": \",\", \"separador_miles\": \".\"}', 1, 1, 1, 1, 'A4', 'portrait', 10, 1, '2026-02-10 09:27:48', '2026-02-10 09:27:48'),
(2, 'cli_eng', 'Client England - Standard', 'Standard template for English-speaking clients. Includes logo, full tax details, VAT breakdown and observations.', 'en', 'nacional', 'templates/cli_eng.php', 'base.css', '{\"moneda\": \"€\", \"formato_fecha\": \"d/m/Y\", \"separador_decimal\": \".\", \"separador_miles\": \",\"}', 1, 1, 1, 1, 'A4', 'portrait', 20, 0, '2026-02-10 09:27:48', '2026-02-10 09:27:48'),
(3, 'int_esp', 'Internacional España - Simplificado', 'Plantilla simplificada para clientes internacionales en español. Sin IVA, formato internacional.', 'es', 'internacional', 'templates/int_esp.php', 'base.css', '{\"moneda\": \"€\", \"formato_fecha\": \"Y-m-d\", \"separador_decimal\": \".\", \"separador_miles\": \",\", \"mostrar_iva\": false}', 1, 1, 1, 1, 'A4', 'portrait', 30, 0, '2026-02-10 09:27:48', '2026-02-10 09:27:48'),
(4, 'int_eng', 'International England - Simplified', 'Simplified template for international clients in English. No VAT, international format.', 'en', 'internacional', 'templates/int_eng.php', 'base.css', '{\"moneda\": \"€\", \"formato_fecha\": \"Y-m-d\", \"separador_decimal\": \".\", \"separador_miles\": \",\", \"mostrar_iva\": false}', 1, 1, 1, 1, 'A4', 'portrait', 40, 0, '2026-02-10 09:27:48', '2026-02-10 09:27:48'),
(5, 'cli_esp_sin_precios', 'Cliente España - Sin Precios', 'Plantilla para presupuestos estimativos sin mostrar precios. Útil para presupuestos preliminares o de referencia.', 'es', 'nacional', 'templates/cli_esp.php', 'base.css', '{\"moneda\": \"€\", \"formato_fecha\": \"d/m/Y\", \"separador_decimal\": \",\", \"separador_miles\": \".\", \"ocultar_columnas\": [\"precio\", \"descuento\", \"total\"]}', 0, 0, 1, 1, 'A4', 'portrait', 50, 0, '2026-02-10 09:27:48', '2026-02-10 09:27:48');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto`
--

CREATE TABLE `presupuesto` (
  `id_presupuesto` int UNSIGNED NOT NULL,
  `id_empresa` int UNSIGNED DEFAULT NULL COMMENT 'Empresa que emite el presupuesto (ficticia o real)',
  `numero_presupuesto` varchar(50) NOT NULL,
  `id_cliente` int UNSIGNED NOT NULL,
  `id_contacto_cliente` int UNSIGNED DEFAULT NULL,
  `id_estado_ppto` int UNSIGNED NOT NULL,
  `version_actual_presupuesto` int UNSIGNED DEFAULT '1' COMMENT 'Número de versión activa actual (la que se muestra/edita)',
  `estado_general_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado') DEFAULT 'borrador' COMMENT 'Estado general del presupuesto (sincronizado con versión actual)',
  `id_forma_pago` int UNSIGNED DEFAULT NULL,
  `id_metodo` int DEFAULT NULL,
  `fecha_presupuesto` date NOT NULL COMMENT 'Fecha de emisión del presupuesto',
  `fecha_validez_presupuesto` date DEFAULT NULL COMMENT 'Fecha hasta la que es válido el presupuesto',
  `fecha_inicio_evento_presupuesto` date DEFAULT NULL COMMENT 'Fecha de inicio del evento/servicio',
  `fecha_fin_evento_presupuesto` date DEFAULT NULL COMMENT 'Fecha de finalización del evento/servicio',
  `numero_pedido_cliente_presupuesto` varchar(80) DEFAULT NULL COMMENT 'Número de pedido del cliente (si lo proporciona)',
  `aplicar_coeficientes_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'TRUE: aplicar coeficientes reductores por días. FALSE: usar precio base sin reducción',
  `descuento_presupuesto` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de descuento aplicado en este presupuesto (0.00 a 100.00). Se hereda de porcentaje_descuento_cliente pero puede modificarse',
  `nombre_evento_presupuesto` varchar(255) DEFAULT NULL COMMENT 'Nombre del evento o proyecto',
  `direccion_evento_presupuesto` varchar(100) DEFAULT NULL COMMENT 'Dirección del evento',
  `poblacion_evento_presupuesto` varchar(80) DEFAULT NULL COMMENT 'Población/Ciudad del evento',
  `cp_evento_presupuesto` varchar(10) DEFAULT NULL COMMENT 'Código postal del evento',
  `provincia_evento_presupuesto` varchar(80) DEFAULT NULL COMMENT 'Provincia del evento',
  `observaciones_cabecera_presupuesto` text COMMENT 'Observaciones iniciales del presupuesto',
  `observaciones_cabecera_ingles_presupuesto` text COMMENT 'Observaciones iniciales del presupuesto en inglés',
  `observaciones_pie_presupuesto` text COMMENT 'Observaciones específicas adicionales al pie',
  `observaciones_pie_ingles_presupuesto` text COMMENT 'Observaciones específicas adicionales al pie en inglés',
  `destacar_observaciones_pie_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Destacar observaciones de pie: 1=Con línea separadora, 0=Continuación justificada izquierda',
  `mostrar_obs_familias_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, muestra observaciones de las familias usadas',
  `mostrar_obs_articulos_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, muestra observaciones de los artículos usados',
  `observaciones_internas_presupuesto` text COMMENT 'Notas internas, no se imprimen en el PDF',
  `activo_presupuesto` tinyint(1) DEFAULT '1',
  `created_at_presupuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_presupuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ;

--
-- Volcado de datos para la tabla `presupuesto`
--

INSERT INTO `presupuesto` (`id_presupuesto`, `id_empresa`, `numero_presupuesto`, `id_cliente`, `id_contacto_cliente`, `id_estado_ppto`, `version_actual_presupuesto`, `estado_general_presupuesto`, `id_forma_pago`, `id_metodo`, `fecha_presupuesto`, `fecha_validez_presupuesto`, `fecha_inicio_evento_presupuesto`, `fecha_fin_evento_presupuesto`, `numero_pedido_cliente_presupuesto`, `aplicar_coeficientes_presupuesto`, `descuento_presupuesto`, `nombre_evento_presupuesto`, `direccion_evento_presupuesto`, `poblacion_evento_presupuesto`, `cp_evento_presupuesto`, `provincia_evento_presupuesto`, `observaciones_cabecera_presupuesto`, `observaciones_cabecera_ingles_presupuesto`, `observaciones_pie_presupuesto`, `observaciones_pie_ingles_presupuesto`, `destacar_observaciones_pie_presupuesto`, `mostrar_obs_familias_presupuesto`, `mostrar_obs_articulos_presupuesto`, `observaciones_internas_presupuesto`, `activo_presupuesto`, `created_at_presupuesto`, `updated_at_presupuesto`) VALUES
(11, NULL, 'P-00002/2026', 4, 6, 1, 1, 'borrador', 8, NULL, '2026-01-21', '2026-02-20', '2026-01-27', '2026-01-31', '', 1, 20.00, '', '', '', '', '', 'Montaje de material audiovisual en régimen de alquiler', 'Installation of audiovisual equipment for rent', 'EN CASO DE PÉRDIDA, DESAPARICIÓN INCLUIDO ROBO, INCENDIO, ETC..., ASÍ COMO CUALQUIER DESPERFECTO CAUSADO AL MATERIAL\nOBJETO DE ESTE PRESUPUESTO, LA REPOSICIÓN DE LOS MISMOS CORRERÁ POR CUENTA DEL CLIENTE.', 'EN INGLES - EN CASO DE PÉRDIDA, DESAPARICIÓN INCLUIDO ROBO, INCENDIO, ETC..., ASÍ COMO CUALQUIER DESPERFECTO CAUSADO AL MATERIAL\nOBJETO DE ESTE PRESUPUESTO, LA REPOSICIÓN DE LOS MISMOS CORRERÁ POR CUENTA DEL CLIENTE.', 0, 1, 1, '', 1, '2026-01-21 09:40:39', '2026-02-26 16:53:54'),
(12, NULL, 'P-00003/2026', 1, 1, 1, 1, 'borrador', 11, NULL, '2026-01-30', '2026-03-01', '2026-02-01', '2026-02-05', 'PD-110', 1, 10.20, 'Conferencia Tech 2025', '', '', '', '', 'Observaciones de inicio', 'Observations', 'Observaciones del final', 'Footer observations', 1, 1, 1, '', 1, '2026-01-30 18:55:28', '2026-02-26 16:53:54'),
(13, NULL, 'P-00004/2026', 4, 6, 3, 1, 'aprobado', 8, NULL, '2026-02-11', '2026-03-13', '2026-02-12', '2026-02-15', '', 1, 20.00, '', '', '', '', '', '', '', '', '', 1, 1, 1, '', 1, '2026-02-11 12:10:15', '2026-03-11 09:08:32'),
(14, NULL, 'P-00005/2026', 5, NULL, 4, 3, 'rechazado', 2, NULL, '2026-02-14', '2026-03-16', '2026-02-20', '2026-02-26', '', 0, 30.00, '', '', '', '', '', 'Montaje de material audiovisual en régimen de alquiler', 'Installation of audiovisual equipment for rent', '', '', 1, 1, 1, '', 1, '2026-02-14 09:50:20', '2026-03-04 12:15:54'),
(15, NULL, 'P-00006/2026', 5, NULL, 3, 1, 'aprobado', 2, NULL, '2026-02-21', '2026-03-16', '2026-02-20', '2026-02-26', '', 0, 20.00, '', '', '', '', '', 'Montaje de material audiovisual en régimen de alquiler', 'Installation of audiovisual equipment for rent', '', '', 0, 1, 1, '', 1, '2026-02-21 10:19:05', '2026-03-10 17:22:04'),
(16, NULL, 'P-00007/2026', 4, 6, 3, 2, 'aprobado', 8, NULL, '2026-02-21', '2026-02-20', '2026-01-27', '2026-01-31', '', 1, 20.00, '', '', '', '', '', 'Montaje de material audiovisual en régimen de alquiler', 'Installation of audiovisual equipment for rent', 'EN CASO DE PÉRDIDA, DESAPARICIÓN INCLUIDO ROBO, INCENDIO, ETC..., ASÍ COMO CUALQUIER DESPERFECTO CAUSADO AL MATERIAL\nOBJETO DE ESTE PRESUPUESTO, LA REPOSICIÓN DE LOS MISMOS CORRERÁ POR CUENTA DEL CLIENTE.', '', 1, 1, 1, '', 1, '2026-02-21 10:20:11', '2026-03-05 12:05:34');

--
-- Disparadores `presupuesto`
--
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_after_insert` AFTER INSERT ON `presupuesto` FOR EACH ROW BEGIN
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
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_before_desactivar` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
    -- Si se está desactivando (1 → 0)
    IF OLD.activo_presupuesto = 1 AND NEW.activo_presupuesto = 0 THEN
        
        -- Cambiar a CANCELADO
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'CANC' 
            LIMIT 1
        );
        
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_before_insert` BEFORE INSERT ON `presupuesto` FOR EACH ROW BEGIN
    DECLARE v_serie VARCHAR(10);
    DECLARE v_numero_actual INT;
    DECLARE v_anio VARCHAR(4);
    DECLARE v_id_empresa INT UNSIGNED;
    
    -- Obtener el año actual
    SET v_anio = YEAR(CURDATE());
    
    -- Obtener la empresa ficticia principal (para presupuestos)
    SELECT 
        id_empresa,
        serie_presupuesto_empresa,
        numero_actual_presupuesto_empresa + 1
    INTO 
        v_id_empresa,
        v_serie,
        v_numero_actual
    FROM empresa
    WHERE empresa_ficticia_principal = TRUE
    AND activo_empresa = TRUE
    LIMIT 1;
    
    -- Generar el número de presupuesto
    -- Formato: SERIE-NUMERO/AÑO (Ejemplo: P-00001/2025)
    SET NEW.numero_presupuesto = CONCAT(
        v_serie,
        '-',
        LPAD(v_numero_actual, 5, '0'),
        '/',
        v_anio
    );
    
    -- Actualizar el contador en la tabla empresa
    UPDATE empresa 
    SET numero_actual_presupuesto_empresa = v_numero_actual
    WHERE id_empresa = v_id_empresa;
    
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_before_reactivar` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
    -- Si se está reactivando (0 → 1)
    IF OLD.activo_presupuesto = 0 AND NEW.activo_presupuesto = 1 THEN
        
        -- Cambiar a EN PROCESO
        SET NEW.id_estado_ppto = (
            SELECT id_estado_ppto 
            FROM estado_presupuesto 
            WHERE codigo_estado_ppto = 'PROC' 
            LIMIT 1
        );
        
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_estado_cancelado` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
    DECLARE v_codigo_cancelado VARCHAR(20);
    
    -- Obtener el código de estado CANCELADO
    SELECT codigo_estado_ppto 
    INTO v_codigo_cancelado
    FROM estado_presupuesto 
    WHERE id_estado_ppto = NEW.id_estado_ppto;
    
    -- Si el nuevo estado es CANCELADO (código 'CANC')
    IF v_codigo_cancelado = 'CANC' THEN
        -- Desactivar el presupuesto automáticamente
        SET NEW.activo_presupuesto = 0;
    END IF;
    
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_estado_no_cancelado` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
    DECLARE v_codigo_viejo VARCHAR(20);
    DECLARE v_codigo_nuevo VARCHAR(20);
    
    -- Obtener el código del estado antiguo
    SELECT codigo_estado_ppto 
    INTO v_codigo_viejo
    FROM estado_presupuesto 
    WHERE id_estado_ppto = OLD.id_estado_ppto;
    
    -- Obtener el código del estado nuevo
    SELECT codigo_estado_ppto 
    INTO v_codigo_nuevo
    FROM estado_presupuesto 
    WHERE id_estado_ppto = NEW.id_estado_ppto;
    
    -- Si el estado anterior era CANCELADO y el nuevo NO es CANCELADO
    IF v_codigo_viejo = 'CANC' AND v_codigo_nuevo != 'CANC' THEN
        -- Reactivar el presupuesto automáticamente
        SET NEW.activo_presupuesto = 1;
    END IF;
    
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `presupuesto_version`
--

CREATE TABLE `presupuesto_version` (
  `id_version_presupuesto` int UNSIGNED NOT NULL COMMENT '? ID único de TABLA (AUTO_INCREMENT). NO confundir con numero_version_presupuesto. \r\n            Ejemplo: Si tienes 3 presupuestos con 2 versiones cada uno, tendrás IDs 1-6,\r\n            pero cada presupuesto tendrá sus propias versiones 1 y 2',
  `id_presupuesto` int UNSIGNED NOT NULL COMMENT '? FK a tabla presupuesto (cabecera). \r\n            Indica a qué presupuesto pertenece esta versión.\r\n            Múltiples versiones pueden apuntar al mismo id_presupuesto',
  `numero_version_presupuesto` int UNSIGNED NOT NULL COMMENT '? Número LÓGICO de versión dentro de este presupuesto (1, 2, 3...).\r\n            Es secuencial DENTRO de cada presupuesto.\r\n            Presupuesto A: versiones 1, 2, 3\r\n            Presupuesto B: versiones 1, 2 (independiente de A)\r\n            ⚠️ NO confundir con id_version_presupuesto',
  `version_padre_presupuesto` int UNSIGNED DEFAULT NULL COMMENT '?‍? ID de la versión anterior (genealogía).\r\n            NULL = Versión original (primera)\r\n            Si tiene valor = ID de la versión desde la cual se creó esta\r\n            Ejemplo: Versión 3 creada desde versión 2 → version_padre = id de versión 2\r\n            Permite rastrear el árbol de cambios',
  `estado_version_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'borrador' COMMENT '? Estado específico de ESTA versión (workflow).\r\n            - borrador: En edición, se pueden modificar líneas\r\n            - enviado: Enviado al cliente, bloqueado para edición\r\n            - aprobado: Cliente aceptó, bloqueado permanentemente\r\n            - rechazado: Cliente rechazó, se puede crear nueva versión\r\n            - cancelado: Versión cancelada, no se usa\r\n            ⚠️ Solo "borrador" permite editar líneas',
  `motivo_modificacion_version` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '? Razón por la que se creó esta versión.\r\n            - Versión 1: "Versión inicial"\r\n            - Versión 2: "Cliente solicitó cambio de precios"\r\n            - Versión 3: "Reducción de equipos por presupuesto"\r\n            Ayuda a entender el historial de cambios',
  `fecha_creacion_version` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '? Fecha y hora de creación de esta versión.\r\n            Se establece automáticamente al crear el registro.\r\n            Útil para auditoría y timeline del presupuesto',
  `creado_por_version` int UNSIGNED NOT NULL COMMENT '? ID del usuario que creó esta versión.\r\n            Permite rastrear responsabilidades.\r\n            TODO: Vincular con tabla usuarios cuando exista\r\n            Actualmente = 1 por defecto',
  `fecha_envio_version` datetime DEFAULT NULL COMMENT '? Fecha y hora de envío al cliente.\r\n            NULL = Aún no enviado\r\n            Se establece automáticamente al cambiar estado a "enviado"\r\n            Marca el momento en que el cliente recibió esta versión',
  `enviado_por_version` int UNSIGNED DEFAULT NULL COMMENT '? ID del usuario que envió esta versión al cliente.\r\n            NULL = No enviado aún\r\n            Permite rastrear quién realizó el envío',
  `fecha_aprobacion_version` datetime DEFAULT NULL COMMENT '✅ Fecha y hora de aprobación del cliente.\r\n            NULL = No aprobado\r\n            Se establece al cambiar estado a "aprobado"\r\n            Importante para facturación y producción',
  `fecha_rechazo_version` datetime DEFAULT NULL COMMENT '❌ Fecha y hora de rechazo del cliente.\r\n            NULL = No rechazado\r\n            Se establece al cambiar estado a "rechazado"\r\n            Indica que se necesita nueva versión',
  `motivo_rechazo_version` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '? Razón por la que el cliente rechazó esta versión.\r\n            NULL = No rechazado o no especificado\r\n            Ejemplo: "Precio muy alto", "Faltan equipos"\r\n            Ayuda a crear la siguiente versión correctamente',
  `ruta_pdf_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT '? Ruta del archivo PDF generado para esta versión.\r\n            NULL = PDF no generado\r\n            Formato: /documentos/presupuestos/P-00001-2026_v1.pdf\r\n            Se genera automáticamente al enviar.\r\n            Mantiene histórico de PDFs enviados',
  `activo_version` tinyint(1) DEFAULT '1' COMMENT '?️ Soft delete: 1=activo, 0=eliminado lógicamente.\r\n            NO usar DELETE físico, cambiar a 0 para "eliminar"\r\n            Mantiene histórico completo en BD',
  `created_at_version` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '⏱️ Timestamp de creación del registro en BD.\r\n            Auditoría técnica del sistema',
  `updated_at_version` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '⏱️ Timestamp de última actualización del registro.\r\n            Se actualiza automáticamente en cada UPDATE.\r\n            Auditoría técnica del sistema'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Tabla de control de versiones de presupuestos. Cada registro representa una versión específica con su historial completo de cambios y estados';

--
-- Volcado de datos para la tabla `presupuesto_version`
--

INSERT INTO `presupuesto_version` (`id_version_presupuesto`, `id_presupuesto`, `numero_version_presupuesto`, `version_padre_presupuesto`, `estado_version_presupuesto`, `motivo_modificacion_version`, `fecha_creacion_version`, `creado_por_version`, `fecha_envio_version`, `enviado_por_version`, `fecha_aprobacion_version`, `fecha_rechazo_version`, `motivo_rechazo_version`, `ruta_pdf_version`, `activo_version`, `created_at_version`, `updated_at_version`) VALUES
(3, 11, 1, NULL, 'borrador', 'Versión inicial', '2026-01-21 09:40:39', 1, NULL, NULL, NULL, NULL, NULL, '/documentos/presupuestos/P-00002/2026_v1.pdf', 1, '2026-01-21 09:40:39', '2026-02-26 16:53:54'),
(4, 12, 1, NULL, 'borrador', 'Versión inicial', '2026-01-30 18:55:28', 1, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2026-01-30 18:55:28', '2026-02-26 16:53:54'),
(5, 13, 1, NULL, 'aprobado', 'Versión inicial', '2026-02-11 12:10:15', 1, '2026-03-11 10:08:08', NULL, '2026-03-11 10:08:32', NULL, NULL, '/documentos/presupuestos/P-00004/2026_v1.pdf', 1, '2026-02-11 12:10:15', '2026-03-11 09:08:32'),
(6, 14, 1, NULL, 'rechazado', 'Versión inicial', '2026-02-14 09:50:20', 1, '2026-03-04 13:12:23', NULL, NULL, '2026-03-04 13:14:26', 'Precio', '/documentos/presupuestos/P-00005/2026_v1.pdf', 1, '2026-02-14 09:50:20', '2026-03-04 12:14:26'),
(14, 15, 1, NULL, 'aprobado', 'Copia del presupuesto P-00005/2026', '2026-02-21 10:19:05', 1, '2026-03-10 18:21:58', NULL, '2026-03-10 18:22:04', NULL, NULL, '/documentos/presupuestos/P-00006/2026_v1.pdf', 1, '2026-02-21 10:19:05', '2026-03-10 17:22:04'),
(15, 16, 1, NULL, 'rechazado', 'Copia del presupuesto P-00002/2026', '2026-02-21 10:20:11', 1, '2026-03-01 12:02:58', NULL, NULL, '2026-03-01 12:05:39', 'Por cambio en el descuento', '/documentos/presupuestos/P-00007/2026_v1.pdf', 1, '2026-02-21 10:20:11', '2026-03-01 11:05:39'),
(17, 16, 2, 15, 'aprobado', 'cambios de focos', '2026-03-01 11:03:21', 1, '2026-03-01 12:04:11', NULL, '2026-03-01 12:05:47', NULL, NULL, '/documentos/presupuestos/P-00007/2026_v2.pdf', 1, '2026-03-01 11:03:21', '2026-03-01 11:05:47'),
(18, 14, 2, 6, 'aprobado', 'Los focos de 500 por 250', '2026-03-04 12:12:07', 1, '2026-03-04 13:12:27', NULL, '2026-03-04 13:14:34', NULL, NULL, '/documentos/presupuestos/P-00005/2026_v2.pdf', 1, '2026-03-04 12:12:07', '2026-03-04 12:14:34'),
(19, 14, 3, 18, 'rechazado', 'Microfos', '2026-03-04 12:13:04', 1, '2026-03-04 13:15:47', NULL, NULL, '2026-03-04 13:15:54', 'ffff', '/documentos/presupuestos/P-00005/2026_v3.pdf', 1, '2026-03-04 12:13:04', '2026-03-04 12:15:54');

--
-- Disparadores `presupuesto_version`
--
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_version_before_delete` BEFORE DELETE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE num_lineas INT;
    DECLARE tiene_hijos INT;
    SELECT COUNT(*) INTO num_lineas FROM linea_presupuesto WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    IF num_lineas > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene líneas asociadas. Elimine primero las líneas.';
    END IF;
    SELECT COUNT(*) INTO tiene_hijos FROM presupuesto_version WHERE version_padre_presupuesto = OLD.id_version_presupuesto;
    IF tiene_hijos > 0 THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene versiones hijas. Esto rompería la cadena genealógica.';
    END IF;
    IF OLD.estado_version_presupuesto != 'borrador' THEN
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar versiones que no están en borrador. El histórico debe ser inmutable.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_version_before_insert_numero` BEFORE INSERT ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE max_version INT;
    
    -- Si no se especificó número de versión, calcularlo automáticamente
    IF NEW.numero_version_presupuesto IS NULL OR NEW.numero_version_presupuesto = 0 THEN
        
        -- Obtener el número de versión más alto actual para este presupuesto
        SELECT COALESCE(MAX(numero_version_presupuesto), 0) INTO max_version
        FROM presupuesto_version
        WHERE id_presupuesto = NEW.id_presupuesto;
        
        -- Asignar el siguiente número de versión
        SET NEW.numero_version_presupuesto = max_version + 1;
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_presupuesto_version_before_insert_validar` BEFORE INSERT ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE estado_actual VARCHAR(20);
    DECLARE version_actual INT;
    
    -- Obtener estado y número de la versión actual
    SELECT 
        pv.estado_version_presupuesto,
        p.version_actual_presupuesto
    INTO 
        estado_actual,
        version_actual
    FROM presupuesto p
    LEFT JOIN presupuesto_version pv 
        ON pv.id_presupuesto = p.id_presupuesto 
        AND pv.numero_version_presupuesto = p.version_actual_presupuesto
    WHERE p.id_presupuesto = NEW.id_presupuesto;
    
    -- REGLA 1: No crear versiones si está aprobada o cancelada
    IF estado_actual IN ('aprobado', 'cancelado') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden crear nuevas versiones de presupuestos aprobados o cancelados. El presupuesto está cerrado.';
    END IF;
    
    -- REGLA 2: No crear nueva versión si existe una en borrador
    IF estado_actual = 'borrador' AND NEW.numero_version_presupuesto > 1 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede crear una nueva versión mientras existe una versión en borrador. Complete o envíe la versión actual primero.';
    END IF;
    
    -- REGLA 3: Solo se pueden crear versiones desde estados 'enviado' o 'rechazado'
    IF NEW.numero_version_presupuesto > 1 
       AND estado_actual NOT IN ('enviado', 'rechazado') THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: Solo se pueden crear nuevas versiones desde estados enviado o rechazado.';
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_version_auto_fechas` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    -- Cuando se marca como 'enviado'
    IF NEW.estado_version_presupuesto = 'enviado' 
       AND OLD.estado_version_presupuesto != 'enviado' 
       AND NEW.fecha_envio_version IS NULL THEN
        SET NEW.fecha_envio_version = NOW();
    END IF;
    
    -- Cuando se marca como 'aprobado'
    IF NEW.estado_version_presupuesto = 'aprobado' 
       AND OLD.estado_version_presupuesto != 'aprobado' 
       AND NEW.fecha_aprobacion_version IS NULL THEN
        SET NEW.fecha_aprobacion_version = NOW();
    END IF;
    
    -- Cuando se marca como 'rechazado'
    IF NEW.estado_version_presupuesto = 'rechazado' 
       AND OLD.estado_version_presupuesto != 'rechazado' 
       AND NEW.fecha_rechazo_version IS NULL THEN
        SET NEW.fecha_rechazo_version = NOW();
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_version_auto_ruta_pdf` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE numero_ppto VARCHAR(50);
    
    -- Solo generar ruta cuando se envía y no existe ruta
    IF NEW.estado_version_presupuesto = 'enviado' 
       AND OLD.estado_version_presupuesto != 'enviado'
       AND (NEW.ruta_pdf_version IS NULL OR NEW.ruta_pdf_version = '') THEN
        
        -- Obtener número de presupuesto
        SELECT numero_presupuesto INTO numero_ppto
        FROM presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
        
        -- Generar ruta: /documentos/presupuestos/PPTO-2025-001_v2.pdf
        SET NEW.ruta_pdf_version = CONCAT(
            '/documentos/presupuestos/',
            numero_ppto,
            '_v',
            NEW.numero_version_presupuesto,
            '.pdf'
        );
    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_version_sync_estado_cabecera` AFTER UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE version_actual INT;
    DECLARE nuevo_id_estado INT;

    SELECT version_actual_presupuesto
    INTO version_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto;

    IF NEW.numero_version_presupuesto = version_actual THEN

        SELECT id_estado_ppto
        INTO nuevo_id_estado
        FROM estado_presupuesto
        WHERE codigo_estado_ppto = CASE NEW.estado_version_presupuesto
            WHEN 'borrador'  THEN 'BORRADOR'
            WHEN 'enviado'   THEN 'ESPE-RESP'
            WHEN 'aprobado'  THEN 'APROB'
            WHEN 'rechazado' THEN 'RECH'
            WHEN 'cancelado' THEN 'CANC'
            ELSE 'BORRADOR'
        END
        LIMIT 1;

        UPDATE presupuesto
        SET
            estado_general_presupuesto = NEW.estado_version_presupuesto,
            id_estado_ppto             = nuevo_id_estado
        WHERE id_presupuesto = NEW.id_presupuesto;

    END IF;
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `trg_version_validar_transicion_estado` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    IF OLD.estado_version_presupuesto != NEW.estado_version_presupuesto THEN
        IF OLD.estado_version_presupuesto = 'borrador'
           AND NEW.estado_version_presupuesto NOT IN ('enviado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: Desde borrador solo se puede pasar a enviado o cancelado. Workflow inválido.';
        END IF;
        IF OLD.estado_version_presupuesto = 'enviado'
           AND NEW.estado_version_presupuesto NOT IN ('aprobado', 'rechazado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: Desde enviado solo se puede pasar a aprobado, rechazado o cancelado. Workflow inválido.';
        END IF;
        IF OLD.estado_version_presupuesto IN ('aprobado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: No se puede cambiar el estado de versiones aprobadas o canceladas. Son estados finales e inmutables.';
        END IF;
        IF OLD.estado_version_presupuesto = 'rechazado'
           AND NEW.estado_version_presupuesto != 'cancelado' THEN
            SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'ERROR: Una versión rechazada solo puede cancelarse. Para nuevos intentos, cree una nueva versión.';
        END IF;
    END IF;
END
$$
DELIMITER ;

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
  `id_forma_pago_habitual` int UNSIGNED DEFAULT NULL COMMENT 'Forma de pago habitual del proveedor. Se usará por defecto en nuevas órdenes de compra',
  `observaciones_proveedor` text,
  `activo_proveedor` tinyint(1) DEFAULT '1',
  `created_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Volcado de datos para la tabla `proveedor`
--

INSERT INTO `proveedor` (`id_proveedor`, `codigo_proveedor`, `nombre_proveedor`, `direccion_proveedor`, `cp_proveedor`, `poblacion_proveedor`, `provincia_proveedor`, `nif_proveedor`, `telefono_proveedor`, `fax_proveedor`, `web_proveedor`, `email_proveedor`, `persona_contacto_proveedor`, `direccion_sat_proveedor`, `cp_sat_proveedor`, `poblacion_sat_proveedor`, `provincia_sat_proveedor`, `telefono_sat_proveedor`, `fax_sat_proveedor`, `email_sat_proveedor`, `id_forma_pago_habitual`, `observaciones_proveedor`, `activo_proveedor`, `created_at_proveedor`, `updated_at_proveedor`) VALUES
(1, 'PRUEBA001', 'Prueba', '', '', '', '', '12342422N', '', '', '', 'manuelefe@gmail.com', '', 'C/ rio Amadorio, 4', '03013', 'La Pobla de Farnals', 'Valencia', '99300923', '4522666987', 'luis@gmail.com', 14, 'Prueba de observaciones', 1, '2025-11-15 12:00:50', '2025-12-09 12:32:09'),
(2, 'PRUEBA003', 'Prueba 002', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', '', NULL, '', 1, '2025-11-15 12:09:40', '2025-11-18 19:02:28');

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
(4, 'Comercial', 1),
(5, 'Técnico', 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `salida_almacen`
--

CREATE TABLE `salida_almacen` (
  `id_salida_almacen` int UNSIGNED NOT NULL,
  `id_presupuesto` int UNSIGNED NOT NULL,
  `id_version_presupuesto` int UNSIGNED NOT NULL,
  `id_usuario_salida` int NOT NULL,
  `numero_presupuesto_salida` varchar(50) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Desnormalizado para histórico',
  `estado_salida` enum('en_proceso','completada','cancelada') COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'en_proceso',
  `fecha_inicio_salida` datetime DEFAULT NULL,
  `fecha_fin_salida` datetime DEFAULT NULL,
  `observaciones_salida` text COLLATE utf8mb4_spanish_ci,
  `activo_salida_almacen` tinyint(1) NOT NULL DEFAULT '1',
  `created_at_salida_almacen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_salida_almacen` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Cabecera de cada operación de picking de almacén';

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tipo_documento`
--

CREATE TABLE `tipo_documento` (
  `id_tipo_documento` int UNSIGNED NOT NULL,
  `codigo_tipo_documento` varchar(20) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Código alfanumérico único del tipo (ej: SEG, MAN, PROC)',
  `nombre_tipo_documento` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Nombre descriptivo del tipo de documento',
  `descripcion_tipo_documento` text COLLATE utf8mb4_spanish_ci COMMENT 'Descripción detallada del tipo de documento',
  `activo_tipo_documento` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Estado del registro: 1=activo, 0=inactivo',
  `created_at_tipo_documento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro',
  `updated_at_tipo_documento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de última modificación'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Catálogo de tipos de documentos para el gestor documental de técnicos';

--
-- Volcado de datos para la tabla `tipo_documento`
--

INSERT INTO `tipo_documento` (`id_tipo_documento`, `codigo_tipo_documento`, `nombre_tipo_documento`, `descripcion_tipo_documento`, `activo_tipo_documento`, `created_at_tipo_documento`, `updated_at_tipo_documento`) VALUES
(1, 'MAN_USU', 'Manual para usuarios', 'Este sistema almacenará todo tipo de documentos con referencia a manuales de usuarios.', 1, '2025-12-17 18:29:23', '2025-12-17 18:29:39');

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
(1, 'Metros', 'Meters', 'Unidad de longitud bbbbbbx', 'm', 1, '2025-11-04 18:03:15', '2026-03-04 10:28:14'),
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
(2, 'luis@gmail.com', '0fcc23c449980e35b30c0f77fd125dc5', 'Luis Carlos Pérez', '2025-04-25 10:23:12', 1, 3, '92fmazrxb8pcl5ghvt6wqyonj3sedu'),
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
,`id_familia` int unsigned
,`id_unidad` int unsigned
,`id_impuesto` int
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
,`permitir_descuentos_articulo` tinyint(1)
,`precio_editable_articulo` tinyint(1)
,`activo_articulo` tinyint(1)
,`created_at_articulo` timestamp
,`updated_at_articulo` timestamp
,`id_grupo` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`descr_familia` varchar(255)
,`imagen_familia` varchar(150)
,`coeficiente_familia` tinyint
,`observaciones_presupuesto_familia` text
,`observations_budget_familia` text
,`orden_obs_familia` int
,`permite_descuento_familia` tinyint(1)
,`activo_familia_relacionada` tinyint(1)
,`tipo_impuesto` varchar(20)
,`tasa_impuesto` decimal(5,2)
,`descr_impuesto` varchar(255)
,`activo_impuesto_relacionado` tinyint(1)
,`nombre_unidad` varchar(50)
,`name_unidad` varchar(50)
,`descr_unidad` varchar(255)
,`simbolo_unidad` varchar(10)
,`activo_unidad_relacionada` tinyint(1)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_articulo_peso`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_articulo_peso` (
`id_articulo` int unsigned
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`es_kit_articulo` tinyint(1)
,`precio_alquiler_articulo` decimal(10,2)
,`peso_articulo_kg` decimal(46,7)
,`metodo_calculo` varchar(16)
,`tiene_datos_peso` int
,`items_con_peso` bigint
,`total_items` bigint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_articulo_peso_medio`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_articulo_peso_medio` (
`id_articulo` int unsigned
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`es_kit_articulo` tinyint(1)
,`elementos_con_peso` bigint
,`total_elementos` bigint
,`peso_medio_kg` decimal(14,7)
,`peso_suma_total_kg` decimal(32,3)
,`peso_min_kg` decimal(10,3)
,`peso_max_kg` decimal(10,3)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_cliente_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_cliente_completa` (
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
,`id_forma_pago_habitual` int unsigned
,`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`descuento_pago` decimal(5,2)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`observaciones_pago` text
,`activo_pago` tinyint(1)
,`id_metodo_pago` int unsigned
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
,`observaciones_metodo_pago` text
,`activo_metodo_pago` tinyint(1)
,`tipo_pago_cliente` varchar(17)
,`descripcion_forma_pago_cliente` varchar(219)
,`direccion_completa_cliente` varchar(470)
,`direccion_facturacion_completa_cliente` varchar(470)
,`tiene_direccion_facturacion_diferente` int
,`estado_forma_pago_cliente` varchar(23)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_cliente_ubicaciones`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_cliente_ubicaciones` (
`id_cliente` int unsigned
,`codigo_cliente` varchar(20)
,`nombre_cliente` varchar(255)
,`nif_cliente` varchar(20)
,`telefono_cliente` varchar(255)
,`email_cliente` varchar(255)
,`activo_cliente` tinyint(1)
,`id_ubicacion` int unsigned
,`nombre_ubicacion` varchar(255)
,`direccion_ubicacion` varchar(255)
,`codigo_postal_ubicacion` varchar(10)
,`poblacion_ubicacion` varchar(100)
,`provincia_ubicacion` varchar(100)
,`pais_ubicacion` varchar(100)
,`persona_contacto_ubicacion` varchar(255)
,`telefono_contacto_ubicacion` varchar(50)
,`email_contacto_ubicacion` varchar(255)
,`observaciones_ubicacion` text
,`es_principal_ubicacion` tinyint(1)
,`activo_ubicacion` tinyint(1)
,`direccion_completa_cliente` varchar(470)
,`direccion_completa_ubicacion` text
,`tipo_ubicacion` varchar(10)
,`estado_completo` varchar(18)
,`tiene_contacto_propio` int
,`total_ubicaciones_cliente` bigint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_control_pagos`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_control_pagos` (
`id_presupuesto` int unsigned
,`numero_presupuesto` varchar(50)
,`fecha_presupuesto` date
,`fecha_inicio_evento_presupuesto` date
,`fecha_fin_evento_presupuesto` date
,`nombre_evento_presupuesto` varchar(255)
,`numero_pedido_cliente_presupuesto` varchar(80)
,`id_cliente` int unsigned
,`nombre_completo_cliente` varchar(255)
,`id_estado_ppto` int unsigned
,`nombre_estado_ppto` varchar(100)
,`codigo_estado_ppto` varchar(20)
,`color_estado_ppto` varchar(7)
,`nombre_forma_pago` varchar(100)
,`total_presupuesto` decimal(32,2)
,`total_pagado` decimal(32,2)
,`total_conciliado` decimal(32,2)
,`saldo_pendiente` decimal(33,2)
,`porcentaje_pagado` decimal(38,2)
,`fecha_ultimo_pago` date
,`metodos_pago_usados` text
,`num_pagos` bigint
,`tipos_documentos` text
,`fecha_ultima_factura` datetime
,`created_at_presupuesto` timestamp
,`updated_at_presupuesto` timestamp
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_costos_furgoneta`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_costos_furgoneta` (
`id_furgoneta` int unsigned
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`anio_furgoneta` int
,`costo_total` decimal(32,2)
,`costo_anio_actual` decimal(32,2)
,`costo_revisiones` decimal(32,2)
,`costo_reparaciones` decimal(32,2)
,`costo_itv` decimal(32,2)
,`costo_neumaticos` decimal(32,2)
,`total_mantenimientos` bigint
,`costo_promedio` decimal(14,6)
,`fecha_ultimo_mantenimiento` date
,`kilometraje_actual` bigint unsigned
,`costo_por_km` decimal(36,6)
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
,`peso_elemento` decimal(10,3)
,`fecha_compra_elemento` date
,`precio_compra_elemento` decimal(10,2)
,`fecha_alta_elemento` date
,`fecha_fin_garantia_elemento` date
,`proximo_mantenimiento_elemento` date
,`observaciones_elemento` text
,`es_propio_elemento` tinyint(1)
,`id_proveedor_compra_elemento` int unsigned
,`id_proveedor_alquiler_elemento` int unsigned
,`precio_dia_alquiler_elemento` decimal(10,2)
,`id_forma_pago_alquiler_elemento` int unsigned
,`observaciones_alquiler_elemento` text
,`codigo_proveedor_compra` varchar(20)
,`nombre_proveedor_compra` varchar(255)
,`telefono_proveedor_compra` varchar(255)
,`email_proveedor_compra` varchar(255)
,`nif_proveedor_compra` varchar(20)
,`codigo_proveedor_alquiler` varchar(20)
,`nombre_proveedor_alquiler` varchar(255)
,`telefono_proveedor_alquiler` varchar(255)
,`email_proveedor_alquiler` varchar(255)
,`nif_proveedor_alquiler` varchar(20)
,`persona_contacto_proveedor_alquiler` varchar(255)
,`codigo_forma_pago_alquiler` varchar(20)
,`nombre_forma_pago_alquiler` varchar(100)
,`porcentaje_anticipo_alquiler` decimal(5,2)
,`dias_anticipo_alquiler` int
,`porcentaje_final_alquiler` decimal(5,2)
,`dias_final_alquiler` int
,`descuento_forma_pago_alquiler` decimal(5,2)
,`codigo_metodo_pago_alquiler` varchar(20)
,`nombre_metodo_pago_alquiler` varchar(100)
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
,`activo_elemento` tinyint(1)
,`created_at_elemento` timestamp
,`updated_at_elemento` timestamp
,`jerarquia_completa_elemento` text
,`tipo_propiedad_elemento` varchar(21)
,`proveedor_principal_elemento` varchar(255)
,`estado_configuracion_alquiler` varchar(43)
,`descripcion_forma_pago_alquiler` varchar(219)
,`costo_mensual_estimado_alquiler` decimal(12,2)
,`dias_en_servicio_elemento` int
,`anios_en_servicio_elemento` decimal(13,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_furgoneta_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_furgoneta_completa` (
`id_furgoneta` int unsigned
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`anio_furgoneta` int
,`numero_bastidor_furgoneta` varchar(50)
,`kilometros_entre_revisiones_furgoneta` int unsigned
,`fecha_proxima_itv_furgoneta` date
,`fecha_vencimiento_seguro_furgoneta` date
,`compania_seguro_furgoneta` varchar(255)
,`numero_poliza_seguro_furgoneta` varchar(100)
,`capacidad_carga_kg_furgoneta` decimal(10,2)
,`capacidad_carga_m3_furgoneta` decimal(10,2)
,`tipo_combustible_furgoneta` varchar(50)
,`consumo_medio_furgoneta` decimal(5,2)
,`taller_habitual_furgoneta` varchar(255)
,`telefono_taller_furgoneta` varchar(50)
,`estado_furgoneta` enum('operativa','taller','baja')
,`observaciones_furgoneta` text
,`activo_furgoneta` tinyint(1)
,`created_at_furgoneta` timestamp
,`updated_at_furgoneta` timestamp
,`kilometraje_actual` bigint unsigned
,`fecha_ultimo_registro_km` date
,`total_mantenimientos` bigint
,`costo_total_mantenimientos` decimal(32,2)
,`fecha_ultimo_mantenimiento` date
,`estado_itv` varchar(11)
,`estado_seguro` varchar(14)
,`km_desde_ultima_revision` bigint unsigned
,`necesita_revision` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_kilometraje_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_kilometraje_completo` (
`id_registro_km` int unsigned
,`id_furgoneta` int unsigned
,`fecha_registro_km` date
,`kilometraje_registrado_km` int unsigned
,`tipo_registro_km` enum('manual','revision','itv','evento')
,`observaciones_registro_km` text
,`created_at_registro_km` timestamp
,`updated_at_registro_km` timestamp
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`estado_furgoneta` enum('operativa','taller','baja')
,`km_recorridos` decimal(11,0)
,`dias_transcurridos` int
,`km_promedio_diario` decimal(14,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_kit_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_kit_completa` (
`id_kit` int unsigned
,`cantidad_kit` int unsigned
,`activo_kit` tinyint(1)
,`created_at_kit` timestamp
,`updated_at_kit` timestamp
,`id_articulo_maestro` int unsigned
,`codigo_articulo_maestro` varchar(50)
,`nombre_articulo_maestro` varchar(255)
,`name_articulo_maestro` varchar(255)
,`precio_articulo_maestro` decimal(10,2)
,`es_kit_articulo_maestro` tinyint(1)
,`activo_articulo_maestro` tinyint(1)
,`id_articulo_componente` int unsigned
,`codigo_articulo_componente` varchar(50)
,`nombre_articulo_componente` varchar(255)
,`name_articulo_componente` varchar(255)
,`precio_articulo_componente` decimal(10,2)
,`es_kit_articulo_componente` tinyint(1)
,`activo_articulo_componente` tinyint(1)
,`subtotal_componente` decimal(20,2)
,`total_componentes_kit` bigint
,`precio_total_kit` decimal(42,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_kit_peso_total`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_kit_peso_total` (
`id_articulo` int unsigned
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`es_kit_articulo` tinyint(1)
,`total_componentes` bigint
,`componentes_con_peso` bigint
,`peso_total_kit_kg` decimal(46,7)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_linea_peso`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_linea_peso` (
`id_linea_ppto` int unsigned
,`id_version_presupuesto` int unsigned
,`id_articulo` int unsigned
,`numero_linea_ppto` int
,`tipo_linea_ppto` enum('articulo','kit','componente_kit','seccion','texto','subtotal')
,`cantidad_linea_ppto` decimal(10,2)
,`descripcion_linea_ppto` text
,`codigo_linea_ppto` varchar(50)
,`codigo_articulo` varchar(50)
,`nombre_articulo` varchar(255)
,`es_kit_articulo` tinyint(1)
,`peso_articulo_kg` decimal(46,7)
,`metodo_calculo` varchar(16)
,`tiene_datos_peso` int
,`peso_total_linea_kg` decimal(56,9)
,`linea_tiene_peso` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_mantenimiento_completo`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_mantenimiento_completo` (
`id_mantenimiento` int unsigned
,`id_furgoneta` int unsigned
,`fecha_mantenimiento` date
,`tipo_mantenimiento` enum('revision','reparacion','itv','neumaticos','otros')
,`descripcion_mantenimiento` text
,`kilometraje_mantenimiento` int unsigned
,`costo_mantenimiento` decimal(10,2)
,`numero_factura_mantenimiento` varchar(100)
,`taller_mantenimiento` varchar(255)
,`telefono_taller_mantenimiento` varchar(50)
,`direccion_taller_mantenimiento` varchar(255)
,`resultado_itv` enum('favorable','desfavorable','negativa')
,`fecha_proxima_itv` date
,`garantia_hasta_mantenimiento` date
,`observaciones_mantenimiento` text
,`activo_mantenimiento` tinyint(1)
,`created_at_mantenimiento` timestamp
,`updated_at_mantenimiento` timestamp
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`anio_furgoneta` int
,`estado_furgoneta` enum('operativa','taller','baja')
,`estado_garantia` varchar(16)
,`dias_desde_mantenimiento` int
,`km_desde_mantenimiento` bigint unsigned
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_presupuesto_completa`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_presupuesto_completa` (
`id_presupuesto` int unsigned
,`numero_presupuesto` varchar(50)
,`version_actual_presupuesto` int unsigned
,`fecha_presupuesto` date
,`fecha_validez_presupuesto` date
,`fecha_inicio_evento_presupuesto` date
,`fecha_fin_evento_presupuesto` date
,`numero_pedido_cliente_presupuesto` varchar(80)
,`aplicar_coeficientes_presupuesto` tinyint(1)
,`descuento_presupuesto` decimal(5,2)
,`nombre_evento_presupuesto` varchar(255)
,`direccion_evento_presupuesto` varchar(100)
,`poblacion_evento_presupuesto` varchar(80)
,`cp_evento_presupuesto` varchar(10)
,`provincia_evento_presupuesto` varchar(80)
,`observaciones_cabecera_presupuesto` text
,`observaciones_pie_presupuesto` text
,`observaciones_cabecera_ingles_presupuesto` text
,`observaciones_pie_ingles_presupuesto` text
,`destacar_observaciones_pie_presupuesto` tinyint(1)
,`mostrar_obs_familias_presupuesto` tinyint(1)
,`mostrar_obs_articulos_presupuesto` tinyint(1)
,`observaciones_internas_presupuesto` text
,`activo_presupuesto` tinyint(1)
,`created_at_presupuesto` timestamp
,`updated_at_presupuesto` timestamp
,`id_cliente` int unsigned
,`codigo_cliente` varchar(20)
,`nombre_cliente` varchar(255)
,`nif_cliente` varchar(20)
,`direccion_cliente` varchar(255)
,`cp_cliente` varchar(10)
,`poblacion_cliente` varchar(100)
,`provincia_cliente` varchar(100)
,`telefono_cliente` varchar(255)
,`email_cliente` varchar(255)
,`porcentaje_descuento_cliente` decimal(5,2)
,`nombre_facturacion_cliente` varchar(255)
,`direccion_facturacion_cliente` varchar(255)
,`cp_facturacion_cliente` varchar(10)
,`poblacion_facturacion_cliente` varchar(100)
,`provincia_facturacion_cliente` varchar(100)
,`exento_iva_cliente` tinyint(1)
,`justificacion_exencion_iva_cliente` text
,`id_contacto_cliente` int unsigned
,`nombre_contacto_cliente` varchar(100)
,`apellidos_contacto_cliente` varchar(150)
,`telefono_contacto_cliente` varchar(50)
,`email_contacto_cliente` varchar(255)
,`id_estado_ppto` int unsigned
,`codigo_estado_ppto` varchar(20)
,`nombre_estado_ppto` varchar(100)
,`color_estado_ppto` varchar(7)
,`orden_estado_ppto` int
,`id_forma_pago` int unsigned
,`codigo_pago` varchar(20)
,`nombre_pago` varchar(100)
,`porcentaje_anticipo_pago` decimal(5,2)
,`dias_anticipo_pago` int
,`porcentaje_final_pago` decimal(5,2)
,`dias_final_pago` int
,`descuento_pago` decimal(5,2)
,`id_metodo_pago` int unsigned
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
,`id_metodo_contacto` int
,`nombre_metodo_contacto` varchar(50)
,`id_forma_pago_habitual` int unsigned
,`nombre_forma_pago_habitual_cliente` varchar(100)
,`direccion_completa_evento_presupuesto` varchar(275)
,`direccion_completa_cliente` varchar(470)
,`direccion_facturacion_completa_cliente` varchar(470)
,`nombre_completo_contacto` varchar(251)
,`dias_validez_restantes` int
,`estado_validez_presupuesto` varchar(20)
,`duracion_evento_dias` bigint
,`dias_hasta_inicio_evento` int
,`dias_hasta_fin_evento` int
,`estado_evento_presupuesto` varchar(19)
,`prioridad_presupuesto` varchar(13)
,`tipo_pago_presupuesto` varchar(17)
,`descripcion_completa_forma_pago` varchar(219)
,`fecha_vencimiento_anticipo` date
,`fecha_vencimiento_final` date
,`comparacion_descuento` varchar(17)
,`estado_descuento_presupuesto` varchar(25)
,`aplica_descuento_presupuesto` int
,`diferencia_descuento` decimal(6,2)
,`tiene_direccion_facturacion_diferente` int
,`dias_desde_emision` int
,`id_version_actual` int unsigned
,`numero_version_actual` int unsigned
,`estado_version_actual` enum('borrador','enviado','aprobado','rechazado','cancelado')
,`fecha_creacion_version_actual` timestamp
,`estado_general_presupuesto` varchar(100)
,`peso_total_kg` decimal(65,9)
,`peso_articulos_normales_kg` decimal(65,9)
,`peso_kits_kg` decimal(65,9)
,`total_lineas` bigint
,`lineas_con_peso` bigint
,`lineas_sin_peso` bigint
,`porcentaje_completitud_peso` decimal(26,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_presupuesto_peso`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_presupuesto_peso` (
`id_version_presupuesto` int unsigned
,`id_presupuesto` int unsigned
,`peso_total_kg` decimal(65,9)
,`peso_articulos_normales_kg` decimal(65,9)
,`peso_kits_kg` decimal(65,9)
,`total_lineas` bigint
,`lineas_con_peso` bigint
,`lineas_sin_peso` bigint
,`porcentaje_completitud` decimal(26,2)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_progreso_salida`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_progreso_salida` (
`id_salida_almacen` int unsigned
,`id_articulo` int unsigned
,`nombre_articulo` varchar(255)
,`codigo_articulo` varchar(50)
,`cantidad_requerida` decimal(32,2)
,`cantidad_escaneada` decimal(23,0)
,`cantidad_backup` decimal(23,0)
,`esta_completo` int
,`id_ubicacion_linea` int unsigned
,`nombre_ubicacion_linea` varchar(255)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_registro_kilometraje`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_registro_kilometraje` (
`id_registro_km` int unsigned
,`id_furgoneta` int unsigned
,`fecha_registro_km` date
,`kilometraje_registrado_km` int unsigned
,`tipo_registro_km` enum('manual','revision','itv','evento')
,`observaciones_registro_km` text
,`created_at_registro_km` timestamp
,`matricula_furgoneta` varchar(20)
,`marca_furgoneta` varchar(100)
,`modelo_furgoneta` varchar(100)
,`estado_furgoneta` enum('operativa','taller','baja')
,`km_recorridos` bigint unsigned
,`dias_transcurridos` int
,`km_promedio_diario` decimal(15,4)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `vista_ubicacion_actual_elemento`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `vista_ubicacion_actual_elemento` (
`id_salida_almacen` int unsigned
,`id_linea_salida` int unsigned
,`id_elemento` int unsigned
,`codigo_elemento` varchar(50)
,`nombre_articulo` varchar(255)
,`codigo_articulo` varchar(50)
,`es_backup_linea_salida` tinyint(1)
,`id_ubicacion_actual` int unsigned
,`nombre_ubicacion_actual` varchar(255)
,`fecha_ultimo_movimiento` datetime
,`id_usuario_movimiento` int
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_documentos_presupuesto`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_documentos_presupuesto` (
`id_documento_ppto` int unsigned
,`id_presupuesto` int unsigned
,`id_version_presupuesto` int unsigned
,`id_empresa` int unsigned
,`seleccion_manual_empresa_documento_ppto` tinyint(1)
,`tipo_documento_ppto` enum('presupuesto','parte_trabajo','factura_proforma','factura_anticipo','factura_final','factura_rectificativa')
,`numero_documento_ppto` varchar(50)
,`serie_documento_ppto` varchar(10)
,`id_documento_origen` int unsigned
,`motivo_abono_documento_ppto` varchar(255)
,`subtotal_documento_ppto` decimal(10,2)
,`total_iva_documento_ppto` decimal(10,2)
,`total_documento_ppto` decimal(10,2)
,`ruta_pdf_documento_ppto` varchar(255)
,`tamano_pdf_documento_ppto` int unsigned
,`fecha_emision_documento_ppto` date
,`fecha_generacion_documento_ppto` datetime
,`observaciones_documento_ppto` text
,`activo_documento_ppto` tinyint(1)
,`created_at_documento_ppto` timestamp
,`updated_at_documento_ppto` timestamp
,`numero_presupuesto` varchar(50)
,`nombre_evento_presupuesto` varchar(255)
,`total_presupuesto` decimal(65,18)
,`id_cliente` int unsigned
,`nombre_cliente` varchar(255)
,`nombre_facturacion_cliente` varchar(255)
,`nombre_completo_cliente` varchar(255)
,`nombre_empresa` varchar(255)
,`nombre_comercial_empresa` varchar(255)
,`nif_empresa` varchar(20)
,`ficticia_empresa` tinyint(1)
,`numero_documento_origen` varchar(50)
,`tipo_documento_origen` enum('presupuesto','parte_trabajo','factura_proforma','factura_anticipo','factura_final','factura_rectificativa')
,`total_documento_origen` decimal(10,2)
,`fecha_emision_origen` date
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_linea_presupuesto_calculada`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_linea_presupuesto_calculada` (
`id_linea_ppto` int unsigned
,`id_version_presupuesto` int unsigned
,`id_articulo` int unsigned
,`id_linea_padre` int unsigned
,`id_ubicacion` int unsigned
,`numero_linea_ppto` int
,`tipo_linea_ppto` enum('articulo','kit','componente_kit','seccion','texto','subtotal')
,`nivel_jerarquia` tinyint
,`codigo_linea_ppto` varchar(50)
,`descripcion_linea_ppto` text
,`orden_linea_ppto` int
,`observaciones_linea_ppto` text
,`mostrar_obs_articulo_linea_ppto` tinyint(1)
,`ocultar_detalle_kit_linea_ppto` tinyint(1)
,`mostrar_en_presupuesto` tinyint(1)
,`es_opcional` tinyint(1)
,`activo_linea_ppto` tinyint(1)
,`fecha_montaje_linea_ppto` date
,`fecha_desmontaje_linea_ppto` date
,`fecha_inicio_linea_ppto` date
,`fecha_fin_linea_ppto` date
,`cantidad_linea_ppto` decimal(10,2)
,`precio_unitario_linea_ppto` decimal(10,2)
,`descuento_linea_ppto` decimal(5,2)
,`porcentaje_iva_linea_ppto` decimal(5,2)
,`jornadas_linea_ppto` int
,`id_coeficiente` int unsigned
,`aplicar_coeficiente_linea_ppto` tinyint(1)
,`valor_coeficiente_linea_ppto` decimal(10,2)
,`jornadas_coeficiente` int
,`valor_coeficiente` decimal(10,2)
,`observaciones_coeficiente` text
,`activo_coeficiente` tinyint(1)
,`dias_linea` bigint
,`subtotal_sin_coeficiente` decimal(39,10)
,`base_imponible` decimal(41,12)
,`importe_iva` decimal(50,18)
,`total_linea` decimal(51,18)
,`precio_unitario_linea_ppto_hotel` decimal(20,8)
,`base_imponible_hotel` decimal(41,12)
,`importe_descuento_linea_ppto_hotel` decimal(50,18)
,`TotalImporte_descuento_linea_ppto_hotel` decimal(51,18)
,`importe_iva_linea_ppto_hotel` decimal(60,24)
,`TotalImporte_iva_linea_ppto_hotel` decimal(61,24)
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
,`permitir_descuentos_articulo` tinyint(1)
,`precio_editable_articulo` tinyint(1)
,`id_familia` int unsigned
,`created_at_articulo` timestamp
,`updated_at_articulo` timestamp
,`id_impuesto_articulo` int
,`tipo_impuesto_articulo` varchar(20)
,`tasa_impuesto_articulo` decimal(5,2)
,`descr_impuesto_articulo` varchar(255)
,`activo_impuesto_articulo` tinyint(1)
,`id_unidad` int unsigned
,`nombre_unidad` varchar(50)
,`name_unidad` varchar(50)
,`descr_unidad` varchar(255)
,`simbolo_unidad` varchar(10)
,`activo_unidad` tinyint(1)
,`id_grupo` int unsigned
,`codigo_familia` varchar(20)
,`nombre_familia` varchar(100)
,`name_familia` varchar(100)
,`descr_familia` varchar(255)
,`imagen_familia` varchar(150)
,`coeficiente_familia` tinyint
,`observaciones_presupuesto_familia` text
,`observations_budget_familia` text
,`orden_obs_familia` int
,`permite_descuento_familia` tinyint(1)
,`activo_familia_relacionada` tinyint(1)
,`id_impuesto` int
,`tipo_impuesto` varchar(20)
,`tasa_impuesto` decimal(5,2)
,`descr_impuesto` varchar(255)
,`activo_impuesto` tinyint(1)
,`id_presupuesto` int unsigned
,`numero_version_presupuesto` int unsigned
,`estado_version_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado')
,`fecha_creacion_version` timestamp
,`fecha_envio_version` datetime
,`fecha_aprobacion_version` datetime
,`numero_presupuesto` varchar(50)
,`fecha_presupuesto` date
,`fecha_validez_presupuesto` date
,`nombre_evento_presupuesto` varchar(255)
,`fecha_inicio_evento_presupuesto` date
,`fecha_fin_evento_presupuesto` date
,`id_cliente` int unsigned
,`id_estado_ppto` int unsigned
,`activo_presupuesto` tinyint(1)
,`nombre_cliente` varchar(255)
,`nif_cliente` varchar(20)
,`email_cliente` varchar(255)
,`telefono_cliente` varchar(255)
,`direccion_cliente` varchar(255)
,`cp_cliente` varchar(10)
,`poblacion_cliente` varchar(100)
,`provincia_cliente` varchar(100)
,`porcentaje_descuento_cliente` decimal(5,2)
,`duracion_evento_dias` bigint
,`dias_hasta_inicio_evento` int
,`dias_hasta_fin_evento` int
,`estado_evento_presupuesto` varchar(19)
,`prioridad_presupuesto` varchar(13)
,`tipo_pago_presupuesto` varchar(17)
,`descripcion_completa_forma_pago` varchar(219)
,`fecha_vencimiento_anticipo` date
,`fecha_vencimiento_final` date
,`comparacion_descuento` varchar(17)
,`estado_descuento_presupuesto` varchar(25)
,`aplica_descuento_presupuesto` int
,`diferencia_descuento` decimal(6,2)
,`tiene_direccion_facturacion_diferente` int
,`dias_desde_emision` int
,`id_version_actual` int unsigned
,`numero_version_actual` int unsigned
,`estado_version_actual` enum('borrador','enviado','aprobado','rechazado','cancelado')
,`fecha_creacion_version_actual` timestamp
,`estado_general_presupuesto` varchar(100)
,`mostrar_obs_familias_presupuesto` tinyint(1)
,`mostrar_obs_articulos_presupuesto` tinyint(1)
,`nombre_ubicacion` varchar(255)
,`direccion_ubicacion` varchar(255)
,`codigo_postal_ubicacion` varchar(10)
,`poblacion_ubicacion` varchar(100)
,`provincia_ubicacion` varchar(100)
,`pais_ubicacion` varchar(100)
,`persona_contacto_ubicacion` varchar(255)
,`telefono_contacto_ubicacion` varchar(50)
,`email_contacto_ubicacion` varchar(255)
,`observaciones_ubicacion` text
,`es_principal_ubicacion` tinyint(1)
,`activo_ubicacion` tinyint(1)
,`ubicacion_agrupacion` varchar(255)
,`ubicacion_completa_agrupacion` text
,`created_at_linea_ppto` timestamp
,`updated_at_linea_ppto` timestamp
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_observaciones_presupuesto`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_observaciones_presupuesto` (
`id_presupuesto` int unsigned
,`id_familia` int unsigned
,`id_articulo` int unsigned
,`tipo_observacion` varchar(8)
,`codigo_familia` varchar(50)
,`nombre_familia` varchar(255)
,`name_familia` varchar(255)
,`observacion_es` mediumtext
,`observacion_en` mediumtext
,`orden_observacion` int
,`mostrar_observacion` tinyint
,`numero_presupuesto` varchar(50)
,`nombre_evento_presupuesto` varchar(255)
,`id_cliente` int unsigned
,`nombre_cliente` varchar(255)
,`activo_origen` tinyint
,`activo_presupuesto` tinyint
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_pagos_presupuesto`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_pagos_presupuesto` (
`id_pago_ppto` int unsigned
,`id_presupuesto` int unsigned
,`id_documento_ppto` int unsigned
,`tipo_pago_ppto` enum('anticipo','total','resto','devolucion')
,`importe_pago_ppto` decimal(10,2)
,`porcentaje_pago_ppto` decimal(5,2)
,`id_metodo_pago` int unsigned
,`referencia_pago_ppto` varchar(100)
,`fecha_pago_ppto` date
,`fecha_valor_pago_ppto` date
,`estado_pago_ppto` enum('pendiente','recibido','conciliado','anulado')
,`observaciones_pago_ppto` text
,`activo_pago_ppto` tinyint(1)
,`created_at_pago_ppto` timestamp
,`updated_at_pago_ppto` timestamp
,`numero_presupuesto` varchar(50)
,`nombre_evento_presupuesto` varchar(255)
,`fecha_presupuesto` date
,`total_presupuesto` decimal(65,18)
,`id_cliente` int unsigned
,`nombre_cliente` varchar(255)
,`nombre_facturacion_cliente` varchar(255)
,`nombre_completo_cliente` varchar(255)
,`tipo_documento_vinculado` enum('presupuesto','parte_trabajo','factura_proforma','factura_anticipo','factura_final','factura_rectificativa')
,`numero_documento_vinculado` varchar(50)
,`subtotal_documento_vinculado` decimal(10,2)
,`iva_cuota_documento_vinculado` decimal(10,2)
,`total_documento_vinculado` decimal(10,2)
,`ruta_pdf_vinculado` varchar(255)
,`codigo_metodo_pago` varchar(20)
,`nombre_metodo_pago` varchar(100)
);

-- --------------------------------------------------------

--
-- Estructura Stand-in para la vista `v_presupuesto_totales`
-- (Véase abajo para la vista actual)
--
CREATE TABLE `v_presupuesto_totales` (
`id_version_presupuesto` int unsigned
,`numero_version_presupuesto` int unsigned
,`estado_version_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado')
,`fecha_creacion_version` timestamp
,`fecha_envio_version` datetime
,`fecha_aprobacion_version` datetime
,`id_presupuesto` int unsigned
,`numero_presupuesto` varchar(50)
,`fecha_presupuesto` date
,`fecha_validez_presupuesto` date
,`nombre_evento_presupuesto` varchar(255)
,`fecha_inicio_evento_presupuesto` date
,`fecha_fin_evento_presupuesto` date
,`id_cliente` int unsigned
,`nombre_cliente` varchar(255)
,`nif_cliente` varchar(20)
,`email_cliente` varchar(255)
,`telefono_cliente` varchar(255)
,`duracion_evento_dias` bigint
,`total_base_imponible` decimal(63,12)
,`total_iva` decimal(65,18)
,`total_con_iva` decimal(65,18)
,`total_base_imponible_hotel` decimal(63,12)
,`total_importe_descuento_linea_hotel` decimal(65,18)
,`total_despues_descuento_linea_hotel` decimal(65,18)
,`total_iva_hotel` decimal(65,24)
,`total_con_iva_hotel` decimal(65,24)
,`cantidad_lineas_total` bigint
,`cantidad_lineas_con_coeficiente` bigint
,`base_iva_21` decimal(63,12)
,`importe_iva_21` decimal(65,18)
,`total_iva_21` decimal(65,18)
,`base_iva_10` decimal(63,12)
,`importe_iva_10` decimal(65,18)
,`total_iva_10` decimal(65,18)
,`base_iva_4` decimal(63,12)
,`importe_iva_4` decimal(65,18)
,`total_iva_4` decimal(65,18)
,`base_iva_0` decimal(63,12)
,`importe_iva_0` decimal(65,18)
,`total_iva_0` decimal(65,18)
,`base_iva_otros` decimal(63,12)
,`importe_iva_otros` decimal(65,18)
,`total_iva_otros` decimal(65,18)
,`base_iva_21_hotel` decimal(63,12)
,`importe_iva_21_hotel` decimal(65,24)
,`total_iva_21_hotel` decimal(65,24)
,`base_iva_10_hotel` decimal(63,12)
,`importe_iva_10_hotel` decimal(65,24)
,`total_iva_10_hotel` decimal(65,24)
,`base_iva_4_hotel` decimal(63,12)
,`importe_iva_4_hotel` decimal(65,24)
,`total_iva_4_hotel` decimal(65,24)
,`base_iva_0_hotel` decimal(63,12)
,`importe_iva_0_hotel` decimal(65,24)
,`total_iva_0_hotel` decimal(65,24)
,`base_iva_otros_hotel` decimal(63,12)
,`importe_iva_otros_hotel` decimal(65,24)
,`total_iva_otros_hotel` decimal(65,24)
,`ahorro_total_coeficientes` decimal(64,12)
,`diferencia_base_imponible_cliente` decimal(64,12)
,`diferencia_total_con_iva_cliente` decimal(65,24)
,`fecha_primera_linea_creada` timestamp
,`fecha_ultima_modificacion_linea` timestamp
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
  ADD KEY `idx_es_kit_articulo` (`es_kit_articulo`),
  ADD KEY `idx_id_impuesto` (`id_impuesto`),
  ADD KEY `idx_es_kit_activo_peso` (`es_kit_articulo`,`activo_articulo`);

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
  ADD KEY `idx_nif_cliente` (`nif_cliente`),
  ADD KEY `idx_id_forma_pago_habitual` (`id_forma_pago_habitual`),
  ADD KEY `idx_porcentaje_descuento_cliente` (`porcentaje_descuento_cliente`),
  ADD KEY `idx_exento_iva_cliente` (`exento_iva_cliente`);

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
-- Indices de la tabla `documento`
--
ALTER TABLE `documento`
  ADD PRIMARY KEY (`id_documento`),
  ADD KEY `idx_tipo_documento` (`id_tipo_documento_documento`),
  ADD KEY `idx_activo_documento` (`activo_documento`);

--
-- Indices de la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  ADD PRIMARY KEY (`id_documento_elemento`),
  ADD KEY `idx_id_elemento_documento` (`id_elemento`),
  ADD KEY `idx_tipo_documento` (`tipo_documento_elemento`),
  ADD KEY `idx_privado_documento` (`privado_documento`);

--
-- Indices de la tabla `documento_presupuesto`
--
ALTER TABLE `documento_presupuesto`
  ADD PRIMARY KEY (`id_documento_ppto`),
  ADD UNIQUE KEY `uk_numero_tipo_doc_ppto` (`numero_documento_ppto`,`tipo_documento_ppto`),
  ADD KEY `fk_doc_ppto_version` (`id_version_presupuesto`),
  ADD KEY `idx_presupuesto_doc_ppto` (`id_presupuesto`),
  ADD KEY `idx_tipo_doc_ppto` (`tipo_documento_ppto`),
  ADD KEY `idx_numero_doc_ppto` (`numero_documento_ppto`),
  ADD KEY `idx_fecha_emision_doc_ppto` (`fecha_emision_documento_ppto`),
  ADD KEY `idx_activo_doc_ppto` (`activo_documento_ppto`),
  ADD KEY `idx_doc_ppto_origen` (`id_documento_origen`),
  ADD KEY `idx_empresa_doc_ppto` (`id_empresa`);

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
  ADD KEY `idx_fecha_compra_elemento` (`fecha_compra_elemento`),
  ADD KEY `idx_es_propio_elemento` (`es_propio_elemento`),
  ADD KEY `idx_id_proveedor_compra_elemento` (`id_proveedor_compra_elemento`),
  ADD KEY `idx_id_proveedor_alquiler_elemento` (`id_proveedor_alquiler_elemento`),
  ADD KEY `idx_id_forma_pago_alquiler_elemento` (`id_forma_pago_alquiler_elemento`),
  ADD KEY `idx_peso_elemento` (`peso_elemento`),
  ADD KEY `idx_articulo_peso` (`id_articulo_elemento`,`activo_elemento`,`peso_elemento`);

--
-- Indices de la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD PRIMARY KEY (`id_empresa`),
  ADD UNIQUE KEY `codigo_empresa` (`codigo_empresa`),
  ADD UNIQUE KEY `nif_empresa` (`nif_empresa`),
  ADD KEY `idx_codigo_empresa` (`codigo_empresa`),
  ADD KEY `idx_nif_empresa` (`nif_empresa`),
  ADD KEY `idx_ficticia_empresa` (`ficticia_empresa`),
  ADD KEY `idx_empresa_ficticia_principal` (`empresa_ficticia_principal`),
  ADD KEY `idx_activo_empresa` (`activo_empresa`),
  ADD KEY `idx_plantilla_default` (`id_plantilla_default`),
  ADD KEY `idx_modelo_impresion` (`modelo_impresion_empresa`),
  ADD KEY `idx_mostrar_kits_albaran` (`mostrar_kits_albaran_empresa`),
  ADD KEY `idx_mostrar_obs_fam_art_albaran` (`mostrar_obs_familias_articulos_albaran_empresa`),
  ADD KEY `idx_mostrar_obs_pie_albaran` (`mostrar_obs_pie_albaran_empresa`),
  ADD KEY `idx_serie_fp_empresa` (`serie_factura_proforma_empresa`);

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
  ADD KEY `idx_id_grupo_familia` (`id_grupo`),
  ADD KEY `idx_permite_descuento_familia` (`permite_descuento_familia`);

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
-- Indices de la tabla `furgoneta`
--
ALTER TABLE `furgoneta`
  ADD PRIMARY KEY (`id_furgoneta`),
  ADD UNIQUE KEY `matricula_furgoneta` (`matricula_furgoneta`),
  ADD KEY `idx_matricula_furgoneta` (`matricula_furgoneta`),
  ADD KEY `idx_estado_furgoneta` (`estado_furgoneta`),
  ADD KEY `idx_activo_furgoneta` (`activo_furgoneta`),
  ADD KEY `idx_fecha_proxima_itv` (`fecha_proxima_itv_furgoneta`);

--
-- Indices de la tabla `furgoneta_mantenimiento`
--
ALTER TABLE `furgoneta_mantenimiento`
  ADD PRIMARY KEY (`id_mantenimiento`),
  ADD KEY `idx_id_furgoneta_mantenimiento` (`id_furgoneta`),
  ADD KEY `idx_fecha_mantenimiento` (`fecha_mantenimiento`),
  ADD KEY `idx_tipo_mantenimiento` (`tipo_mantenimiento`),
  ADD KEY `idx_activo_mantenimiento` (`activo_mantenimiento`);

--
-- Indices de la tabla `furgoneta_registro_kilometraje`
--
ALTER TABLE `furgoneta_registro_kilometraje`
  ADD PRIMARY KEY (`id_registro_km`),
  ADD KEY `idx_id_furgoneta_registro` (`id_furgoneta`),
  ADD KEY `idx_fecha_registro_km` (`fecha_registro_km`),
  ADD KEY `idx_tipo_registro_km` (`tipo_registro_km`);

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
-- Indices de la tabla `kit`
--
ALTER TABLE `kit`
  ADD PRIMARY KEY (`id_kit`),
  ADD UNIQUE KEY `uk_kit_componente` (`id_articulo_maestro`,`id_articulo_componente`),
  ADD KEY `idx_id_articulo_maestro` (`id_articulo_maestro`),
  ADD KEY `idx_id_articulo_componente` (`id_articulo_componente`),
  ADD KEY `idx_maestro_activo_peso` (`id_articulo_maestro`,`activo_kit`);

--
-- Indices de la tabla `linea_presupuesto`
--
ALTER TABLE `linea_presupuesto`
  ADD PRIMARY KEY (`id_linea_ppto`),
  ADD KEY `fk_linea_ppto_coeficiente` (`id_coeficiente`),
  ADD KEY `idx_id_version_presupuesto_linea` (`id_version_presupuesto`),
  ADD KEY `idx_id_articulo_linea` (`id_articulo`),
  ADD KEY `idx_orden_linea_ppto` (`orden_linea_ppto`),
  ADD KEY `idx_tipo_linea` (`tipo_linea_ppto`),
  ADD KEY `idx_linea_padre` (`id_linea_padre`),
  ADD KEY `idx_fecha_montaje` (`fecha_montaje_linea_ppto`),
  ADD KEY `idx_fecha_inicio` (`fecha_inicio_linea_ppto`),
  ADD KEY `idx_ubicacion` (`id_ubicacion`),
  ADD KEY `idx_impuesto` (`id_impuesto`),
  ADD KEY `idx_activo` (`activo_linea_ppto`),
  ADD KEY `idx_version_articulo_peso` (`id_version_presupuesto`,`id_articulo`,`activo_linea_ppto`);

--
-- Indices de la tabla `linea_salida_almacen`
--
ALTER TABLE `linea_salida_almacen`
  ADD PRIMARY KEY (`id_linea_salida`),
  ADD KEY `idx_salida_linea` (`id_salida_almacen`),
  ADD KEY `idx_elemento_linea` (`id_elemento`),
  ADD KEY `idx_articulo_linea` (`id_articulo`),
  ADD KEY `idx_activo_linea_salida` (`activo_linea_salida`),
  ADD KEY `fk_linea_salida_linea_ppto` (`id_linea_ppto`);

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
-- Indices de la tabla `movimiento_elemento_salida`
--
ALTER TABLE `movimiento_elemento_salida`
  ADD PRIMARY KEY (`id_movimiento`),
  ADD KEY `idx_linea_movimiento` (`id_linea_salida`),
  ADD KEY `idx_destino_movimiento` (`id_ubicacion_destino`),
  ADD KEY `idx_fecha_movimiento` (`fecha_movimiento`),
  ADD KEY `idx_activo_movimiento` (`activo_movimiento`),
  ADD KEY `fk_mov_ubicacion_origen` (`id_ubicacion_origen`);

--
-- Indices de la tabla `observacion_general`
--
ALTER TABLE `observacion_general`
  ADD PRIMARY KEY (`id_obs_general`),
  ADD UNIQUE KEY `codigo_obs_general` (`codigo_obs_general`),
  ADD KEY `idx_orden_obs_general` (`orden_obs_general`),
  ADD KEY `idx_obligatoria_obs_general` (`obligatoria_obs_general`);

--
-- Indices de la tabla `pago_presupuesto`
--
ALTER TABLE `pago_presupuesto`
  ADD PRIMARY KEY (`id_pago_ppto`),
  ADD KEY `fk_pago_ppto_metodo` (`id_metodo_pago`),
  ADD KEY `idx_presupuesto_pago_ppto` (`id_presupuesto`),
  ADD KEY `idx_tipo_pago_ppto` (`tipo_pago_ppto`),
  ADD KEY `idx_fecha_pago_ppto` (`fecha_pago_ppto`),
  ADD KEY `idx_estado_pago_ppto` (`estado_pago_ppto`),
  ADD KEY `idx_activo_pago_ppto` (`activo_pago_ppto`),
  ADD KEY `idx_documento_pago_ppto` (`id_documento_ppto`),
  ADD KEY `idx_empresa_pago_ppto` (`id_empresa_pago`);

--
-- Indices de la tabla `plantilla_impresion`
--
ALTER TABLE `plantilla_impresion`
  ADD PRIMARY KEY (`id_plantilla`),
  ADD UNIQUE KEY `codigo_plantilla` (`codigo_plantilla`),
  ADD KEY `idx_codigo_plantilla` (`codigo_plantilla`),
  ADD KEY `idx_idioma_plantilla` (`idioma_plantilla`),
  ADD KEY `idx_tipo_cliente_plantilla` (`tipo_cliente_plantilla`),
  ADD KEY `idx_activo_plantilla` (`activo_plantilla`),
  ADD KEY `idx_orden_plantilla` (`orden_plantilla`);

--
-- Indices de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD PRIMARY KEY (`id_presupuesto`),
  ADD UNIQUE KEY `numero_presupuesto` (`numero_presupuesto`),
  ADD KEY `fk_presupuesto_contacto` (`id_contacto_cliente`),
  ADD KEY `fk_presupuesto_forma_pago` (`id_forma_pago`),
  ADD KEY `fk_presupuesto_metodo_contacto` (`id_metodo`),
  ADD KEY `idx_numero_presupuesto` (`numero_presupuesto`),
  ADD KEY `idx_id_cliente_presupuesto` (`id_cliente`),
  ADD KEY `idx_id_estado_presupuesto` (`id_estado_ppto`),
  ADD KEY `idx_fecha_presupuesto` (`fecha_presupuesto`),
  ADD KEY `idx_fecha_inicio_evento` (`fecha_inicio_evento_presupuesto`),
  ADD KEY `idx_fecha_fin_evento` (`fecha_fin_evento_presupuesto`),
  ADD KEY `idx_numero_pedido_cliente` (`numero_pedido_cliente_presupuesto`),
  ADD KEY `idx_poblacion_evento` (`poblacion_evento_presupuesto`),
  ADD KEY `idx_provincia_evento` (`provincia_evento_presupuesto`),
  ADD KEY `idx_aplicar_coeficientes_presupuesto` (`aplicar_coeficientes_presupuesto`),
  ADD KEY `idx_descuento_presupuesto` (`descuento_presupuesto`),
  ADD KEY `idx_version_actual_presupuesto` (`version_actual_presupuesto`),
  ADD KEY `idx_estado_general_presupuesto` (`estado_general_presupuesto`),
  ADD KEY `idx_id_empresa_presupuesto` (`id_empresa`);

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
-- Indices de la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD PRIMARY KEY (`id_proveedor`),
  ADD UNIQUE KEY `codigo_proveedor` (`codigo_proveedor`),
  ADD KEY `idx_codigo_proveedor` (`codigo_proveedor`),
  ADD KEY `idx_nombre_proveedor` (`nombre_proveedor`),
  ADD KEY `idx_nif_proveedor` (`nif_proveedor`),
  ADD KEY `idx_id_forma_pago_habitual` (`id_forma_pago_habitual`);

--
-- Indices de la tabla `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id_rol`);

--
-- Indices de la tabla `salida_almacen`
--
ALTER TABLE `salida_almacen`
  ADD PRIMARY KEY (`id_salida_almacen`),
  ADD KEY `idx_presupuesto_salida` (`id_presupuesto`),
  ADD KEY `idx_usuario_salida` (`id_usuario_salida`),
  ADD KEY `idx_estado_salida` (`estado_salida`),
  ADD KEY `idx_activo_salida_almacen` (`activo_salida_almacen`),
  ADD KEY `fk_salida_version` (`id_version_presupuesto`);

--
-- Indices de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  ADD PRIMARY KEY (`id_tipo_documento`),
  ADD UNIQUE KEY `uk_codigo_tipo_documento` (`codigo_tipo_documento`),
  ADD KEY `idx_nombre_tipo_documento` (`nombre_tipo_documento`);

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
  MODIFY `id_articulo` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;

--
-- AUTO_INCREMENT de la tabla `categorias`
--
ALTER TABLE `categorias`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `cliente`
--
ALTER TABLE `cliente`
  MODIFY `id_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `cliente_ubicacion`
--
ALTER TABLE `cliente_ubicacion`
  MODIFY `id_ubicacion` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT de la tabla `coeficiente`
--
ALTER TABLE `coeficiente`
  MODIFY `id_coeficiente` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT de la tabla `comerciales`
--
ALTER TABLE `comerciales`
  MODIFY `id_comercial` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

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
  MODIFY `id_contacto_cliente` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `contacto_proveedor`
--
ALTER TABLE `contacto_proveedor`
  MODIFY `id_contacto_proveedor` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `documento`
--
ALTER TABLE `documento`
  MODIFY `id_documento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  MODIFY `id_documento_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `documento_presupuesto`
--
ALTER TABLE `documento_presupuesto`
  MODIFY `id_documento_ppto` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT de la tabla `elemento`
--
ALTER TABLE `elemento`
  MODIFY `id_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `empresa`
--
ALTER TABLE `empresa`
  MODIFY `id_empresa` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `estados_llamada`
--
ALTER TABLE `estados_llamada`
  MODIFY `id_estado` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT de la tabla `estado_elemento`
--
ALTER TABLE `estado_elemento`
  MODIFY `id_estado_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT de la tabla `estado_presupuesto`
--
ALTER TABLE `estado_presupuesto`
  MODIFY `id_estado_ppto` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT de la tabla `familia`
--
ALTER TABLE `familia`
  MODIFY `id_familia` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=171;

--
-- AUTO_INCREMENT de la tabla `forma_pago`
--
ALTER TABLE `forma_pago`
  MODIFY `id_pago` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `foto_elemento`
--
ALTER TABLE `foto_elemento`
  MODIFY `id_foto_elemento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `furgoneta`
--
ALTER TABLE `furgoneta`
  MODIFY `id_furgoneta` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `furgoneta_mantenimiento`
--
ALTER TABLE `furgoneta_mantenimiento`
  MODIFY `id_mantenimiento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT de la tabla `furgoneta_registro_kilometraje`
--
ALTER TABLE `furgoneta_registro_kilometraje`
  MODIFY `id_registro_km` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT de la tabla `grupo_articulo`
--
ALTER TABLE `grupo_articulo`
  MODIFY `id_grupo` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT de la tabla `impuesto`
--
ALTER TABLE `impuesto`
  MODIFY `id_impuesto` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT de la tabla `kit`
--
ALTER TABLE `kit`
  MODIFY `id_kit` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT de la tabla `linea_presupuesto`
--
ALTER TABLE `linea_presupuesto`
  MODIFY `id_linea_ppto` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=188;

--
-- AUTO_INCREMENT de la tabla `linea_salida_almacen`
--
ALTER TABLE `linea_salida_almacen`
  MODIFY `id_linea_salida` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `llamadas`
--
ALTER TABLE `llamadas`
  MODIFY `id_llamada` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT de la tabla `marca`
--
ALTER TABLE `marca`
  MODIFY `id_marca` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

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
-- AUTO_INCREMENT de la tabla `movimiento_elemento_salida`
--
ALTER TABLE `movimiento_elemento_salida`
  MODIFY `id_movimiento` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `observacion_general`
--
ALTER TABLE `observacion_general`
  MODIFY `id_obs_general` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT de la tabla `pago_presupuesto`
--
ALTER TABLE `pago_presupuesto`
  MODIFY `id_pago_ppto` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT de la tabla `plantilla_impresion`
--
ALTER TABLE `plantilla_impresion`
  MODIFY `id_plantilla` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT de la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  MODIFY `id_presupuesto` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `presupuesto_version`
--
ALTER TABLE `presupuesto_version`
  MODIFY `id_version_presupuesto` int UNSIGNED NOT NULL AUTO_INCREMENT COMMENT '? ID único de TABLA (AUTO_INCREMENT). NO confundir con numero_version_presupuesto. \r\n            Ejemplo: Si tienes 3 presupuestos con 2 versiones cada uno, tendrás IDs 1-6,\r\n            pero cada presupuesto tendrá sus propias versiones 1 y 2', AUTO_INCREMENT=20;

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
-- AUTO_INCREMENT de la tabla `salida_almacen`
--
ALTER TABLE `salida_almacen`
  MODIFY `id_salida_almacen` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT de la tabla `tipo_documento`
--
ALTER TABLE `tipo_documento`
  MODIFY `id_tipo_documento` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `contacto_cantidad_cliente`  AS SELECT `c`.`id_cliente` AS `id_cliente`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`direccion_cliente` AS `direccion_cliente`, `c`.`cp_cliente` AS `cp_cliente`, `c`.`poblacion_cliente` AS `poblacion_cliente`, `c`.`provincia_cliente` AS `provincia_cliente`, `c`.`nif_cliente` AS `nif_cliente`, `c`.`telefono_cliente` AS `telefono_cliente`, `c`.`fax_cliente` AS `fax_cliente`, `c`.`web_cliente` AS `web_cliente`, `c`.`email_cliente` AS `email_cliente`, `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, `c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`, `c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`, `c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`, `c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`, `c`.`observaciones_cliente` AS `observaciones_cliente`, `c`.`activo_cliente` AS `activo_cliente`, `c`.`created_at_cliente` AS `created_at_cliente`, `c`.`updated_at_cliente` AS `updated_at_cliente`, `c`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`, `c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`, `c`.`exento_iva_cliente` AS `exento_iva_cliente`, `c`.`justificacion_exencion_iva_cliente` AS `justificacion_exencion_iva_cliente`, `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `fp`.`descuento_pago` AS `descuento_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`observaciones_pago` AS `observaciones_pago`, `fp`.`activo_pago` AS `activo_pago`, `mp`.`id_metodo_pago` AS `id_metodo_pago`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`, `mp`.`activo_metodo_pago` AS `activo_metodo_pago`, (select count(`cc`.`id_contacto_cliente`) from `contacto_cliente` `cc` where (`cc`.`id_cliente` = `c`.`id_cliente`)) AS `cantidad_contactos_cliente`, (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_cliente`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_cliente`, concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`, (case when (`c`.`direccion_facturacion_cliente` is not null) then concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) else NULL end) AS `direccion_facturacion_completa_cliente`, (case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`, (case when (`c`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_cliente`, (case when (`c`.`porcentaje_descuento_cliente` = 0.00) then 'Sin descuento' when ((`c`.`porcentaje_descuento_cliente` > 0.00) and (`c`.`porcentaje_descuento_cliente` <= 5.00)) then 'Descuento bajo' when ((`c`.`porcentaje_descuento_cliente` > 5.00) and (`c`.`porcentaje_descuento_cliente` <= 15.00)) then 'Descuento medio' when (`c`.`porcentaje_descuento_cliente` > 15.00) then 'Descuento alto' else 'Sin descuento' end) AS `categoria_descuento_cliente`, (case when (`c`.`porcentaje_descuento_cliente` > 0.00) then true else false end) AS `tiene_descuento_cliente` FROM ((`cliente` `c` left join `forma_pago` `fp` on((`c`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `contacto_cantidad_proveedor`
--
DROP TABLE IF EXISTS `contacto_cantidad_proveedor`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `contacto_cantidad_proveedor`  AS SELECT `p`.`id_proveedor` AS `id_proveedor`, `p`.`codigo_proveedor` AS `codigo_proveedor`, `p`.`nombre_proveedor` AS `nombre_proveedor`, `p`.`direccion_proveedor` AS `direccion_proveedor`, `p`.`cp_proveedor` AS `cp_proveedor`, `p`.`poblacion_proveedor` AS `poblacion_proveedor`, `p`.`provincia_proveedor` AS `provincia_proveedor`, `p`.`nif_proveedor` AS `nif_proveedor`, `p`.`telefono_proveedor` AS `telefono_proveedor`, `p`.`fax_proveedor` AS `fax_proveedor`, `p`.`web_proveedor` AS `web_proveedor`, `p`.`email_proveedor` AS `email_proveedor`, `p`.`persona_contacto_proveedor` AS `persona_contacto_proveedor`, `p`.`direccion_sat_proveedor` AS `direccion_sat_proveedor`, `p`.`cp_sat_proveedor` AS `cp_sat_proveedor`, `p`.`poblacion_sat_proveedor` AS `poblacion_sat_proveedor`, `p`.`provincia_sat_proveedor` AS `provincia_sat_proveedor`, `p`.`telefono_sat_proveedor` AS `telefono_sat_proveedor`, `p`.`fax_sat_proveedor` AS `fax_sat_proveedor`, `p`.`email_sat_proveedor` AS `email_sat_proveedor`, `p`.`observaciones_proveedor` AS `observaciones_proveedor`, `p`.`activo_proveedor` AS `activo_proveedor`, `p`.`created_at_proveedor` AS `created_at_proveedor`, `p`.`updated_at_proveedor` AS `updated_at_proveedor`, `p`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`, `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `fp`.`descuento_pago` AS `descuento_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`observaciones_pago` AS `observaciones_pago`, `fp`.`activo_pago` AS `activo_pago`, `mp`.`id_metodo_pago` AS `id_metodo_pago`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`, `mp`.`activo_metodo_pago` AS `activo_metodo_pago`, (select count(`cp`.`id_contacto_proveedor`) from `contacto_proveedor` `cp` where (`cp`.`id_proveedor` = `p`.`id_proveedor`)) AS `cantidad_contacto_proveedor`, (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_proveedor`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_proveedor`, concat_ws(', ',`p`.`direccion_proveedor`,concat(`p`.`cp_proveedor`,' ',`p`.`poblacion_proveedor`),`p`.`provincia_proveedor`) AS `direccion_completa_proveedor`, (case when (`p`.`direccion_sat_proveedor` is not null) then concat_ws(', ',`p`.`direccion_sat_proveedor`,concat(`p`.`cp_sat_proveedor`,' ',`p`.`poblacion_sat_proveedor`),`p`.`provincia_sat_proveedor`) else NULL end) AS `direccion_sat_completa_proveedor`, (case when (`p`.`direccion_sat_proveedor` is not null) then true else false end) AS `tiene_direccion_sat`, (case when (`p`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_proveedor` FROM ((`proveedor` `p` left join `forma_pago` `fp` on((`p`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) ;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_articulo_completa`  AS SELECT `a`.`id_articulo` AS `id_articulo`, `a`.`id_familia` AS `id_familia`, `a`.`id_unidad` AS `id_unidad`, `a`.`id_impuesto` AS `id_impuesto`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`imagen_articulo` AS `imagen_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `a`.`coeficiente_articulo` AS `coeficiente_articulo`, `a`.`es_kit_articulo` AS `es_kit_articulo`, `a`.`control_total_articulo` AS `control_total_articulo`, `a`.`no_facturar_articulo` AS `no_facturar_articulo`, `a`.`notas_presupuesto_articulo` AS `notas_presupuesto_articulo`, `a`.`notes_budget_articulo` AS `notes_budget_articulo`, `a`.`orden_obs_articulo` AS `orden_obs_articulo`, `a`.`observaciones_articulo` AS `observaciones_articulo`, `a`.`permitir_descuentos_articulo` AS `permitir_descuentos_articulo`, `a`.`precio_editable_articulo` AS `precio_editable_articulo`, `a`.`activo_articulo` AS `activo_articulo`, `a`.`created_at_articulo` AS `created_at_articulo`, `a`.`updated_at_articulo` AS `updated_at_articulo`, `f`.`id_grupo` AS `id_grupo`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `f`.`descr_familia` AS `descr_familia`, `f`.`imagen_familia` AS `imagen_familia`, `f`.`coeficiente_familia` AS `coeficiente_familia`, `f`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`, `f`.`observations_budget_familia` AS `observations_budget_familia`, `f`.`orden_obs_familia` AS `orden_obs_familia`, `f`.`permite_descuento_familia` AS `permite_descuento_familia`, `f`.`activo_familia` AS `activo_familia_relacionada`, `imp`.`tipo_impuesto` AS `tipo_impuesto`, `imp`.`tasa_impuesto` AS `tasa_impuesto`, `imp`.`descr_impuesto` AS `descr_impuesto`, `imp`.`activo_impuesto` AS `activo_impuesto_relacionado`, `u`.`nombre_unidad` AS `nombre_unidad`, `u`.`name_unidad` AS `name_unidad`, `u`.`descr_unidad` AS `descr_unidad`, `u`.`simbolo_unidad` AS `simbolo_unidad`, `u`.`activo_unidad` AS `activo_unidad_relacionada` FROM (((`articulo` `a` join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `impuesto` `imp` on((`a`.`id_impuesto` = `imp`.`id_impuesto`))) left join `unidad_medida` `u` on((`a`.`id_unidad` = `u`.`id_unidad`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_articulo_peso`
--
DROP TABLE IF EXISTS `vista_articulo_peso`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_articulo_peso`  AS SELECT `a`.`id_articulo` AS `id_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`es_kit_articulo` AS `es_kit_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, (case when (`a`.`es_kit_articulo` = 0) then coalesce(`vpm`.`peso_medio_kg`,0.000) when (`a`.`es_kit_articulo` = 1) then coalesce(`vkp`.`peso_total_kit_kg`,0.000) else 0.000 end) AS `peso_articulo_kg`, (case when (`a`.`es_kit_articulo` = 0) then 'MEDIA_ELEMENTOS' when (`a`.`es_kit_articulo` = 1) then 'SUMA_COMPONENTES' else 'SIN_METODO' end) AS `metodo_calculo`, (case when ((`a`.`es_kit_articulo` = 0) and (`vpm`.`elementos_con_peso` > 0)) then true when ((`a`.`es_kit_articulo` = 1) and (`vkp`.`componentes_con_peso` > 0)) then true else false end) AS `tiene_datos_peso`, (case when (`a`.`es_kit_articulo` = 0) then `vpm`.`elementos_con_peso` when (`a`.`es_kit_articulo` = 1) then `vkp`.`componentes_con_peso` else 0 end) AS `items_con_peso`, (case when (`a`.`es_kit_articulo` = 0) then `vpm`.`total_elementos` when (`a`.`es_kit_articulo` = 1) then `vkp`.`total_componentes` else 0 end) AS `total_items` FROM ((`articulo` `a` left join `vista_articulo_peso_medio` `vpm` on(((`a`.`id_articulo` = `vpm`.`id_articulo`) and (`a`.`es_kit_articulo` = 0)))) left join `vista_kit_peso_total` `vkp` on(((`a`.`id_articulo` = `vkp`.`id_articulo`) and (`a`.`es_kit_articulo` = 1)))) WHERE (`a`.`activo_articulo` = 1) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_articulo_peso_medio`
--
DROP TABLE IF EXISTS `vista_articulo_peso_medio`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_articulo_peso_medio`  AS SELECT `a`.`id_articulo` AS `id_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`es_kit_articulo` AS `es_kit_articulo`, count((case when (`e`.`peso_elemento` is not null) then 1 end)) AS `elementos_con_peso`, count(`e`.`id_elemento`) AS `total_elementos`, avg(`e`.`peso_elemento`) AS `peso_medio_kg`, sum(`e`.`peso_elemento`) AS `peso_suma_total_kg`, min(`e`.`peso_elemento`) AS `peso_min_kg`, max(`e`.`peso_elemento`) AS `peso_max_kg` FROM (`articulo` `a` left join `elemento` `e` on(((`a`.`id_articulo` = `e`.`id_articulo_elemento`) and (`e`.`activo_elemento` = 1) and (`e`.`peso_elemento` is not null)))) WHERE ((`a`.`activo_articulo` = 1) AND (`a`.`es_kit_articulo` = 0)) GROUP BY `a`.`id_articulo`, `a`.`codigo_articulo`, `a`.`nombre_articulo`, `a`.`es_kit_articulo` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_cliente_completa`
--
DROP TABLE IF EXISTS `vista_cliente_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_cliente_completa`  AS SELECT `c`.`id_cliente` AS `id_cliente`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`direccion_cliente` AS `direccion_cliente`, `c`.`cp_cliente` AS `cp_cliente`, `c`.`poblacion_cliente` AS `poblacion_cliente`, `c`.`provincia_cliente` AS `provincia_cliente`, `c`.`nif_cliente` AS `nif_cliente`, `c`.`telefono_cliente` AS `telefono_cliente`, `c`.`fax_cliente` AS `fax_cliente`, `c`.`web_cliente` AS `web_cliente`, `c`.`email_cliente` AS `email_cliente`, `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, `c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`, `c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`, `c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`, `c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`, `c`.`observaciones_cliente` AS `observaciones_cliente`, `c`.`activo_cliente` AS `activo_cliente`, `c`.`created_at_cliente` AS `created_at_cliente`, `c`.`updated_at_cliente` AS `updated_at_cliente`, `c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`, `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `fp`.`descuento_pago` AS `descuento_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`observaciones_pago` AS `observaciones_pago`, `fp`.`activo_pago` AS `activo_pago`, `mp`.`id_metodo_pago` AS `id_metodo_pago`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`, `mp`.`activo_metodo_pago` AS `activo_metodo_pago`, (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_cliente`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_cliente`, concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`, (case when (`c`.`direccion_facturacion_cliente` is not null) then concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) else NULL end) AS `direccion_facturacion_completa_cliente`, (case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`, (case when (`c`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_cliente` FROM ((`cliente` `c` left join `forma_pago` `fp` on((`c`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_cliente_ubicaciones`
--
DROP TABLE IF EXISTS `vista_cliente_ubicaciones`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_cliente_ubicaciones`  AS SELECT `c`.`id_cliente` AS `id_cliente`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`nif_cliente` AS `nif_cliente`, `c`.`telefono_cliente` AS `telefono_cliente`, `c`.`email_cliente` AS `email_cliente`, `c`.`activo_cliente` AS `activo_cliente`, `u`.`id_ubicacion` AS `id_ubicacion`, `u`.`nombre_ubicacion` AS `nombre_ubicacion`, `u`.`direccion_ubicacion` AS `direccion_ubicacion`, `u`.`codigo_postal_ubicacion` AS `codigo_postal_ubicacion`, `u`.`poblacion_ubicacion` AS `poblacion_ubicacion`, `u`.`provincia_ubicacion` AS `provincia_ubicacion`, `u`.`pais_ubicacion` AS `pais_ubicacion`, `u`.`persona_contacto_ubicacion` AS `persona_contacto_ubicacion`, `u`.`telefono_contacto_ubicacion` AS `telefono_contacto_ubicacion`, `u`.`email_contacto_ubicacion` AS `email_contacto_ubicacion`, `u`.`observaciones_ubicacion` AS `observaciones_ubicacion`, `u`.`es_principal_ubicacion` AS `es_principal_ubicacion`, `u`.`activo_ubicacion` AS `activo_ubicacion`, concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`, concat_ws(', ',`u`.`direccion_ubicacion`,concat(`u`.`codigo_postal_ubicacion`,' ',`u`.`poblacion_ubicacion`),`u`.`provincia_ubicacion`,(case when (`u`.`pais_ubicacion` <> 'España') then `u`.`pais_ubicacion` else NULL end)) AS `direccion_completa_ubicacion`, (case when (`u`.`es_principal_ubicacion` = true) then 'Principal' else 'Secundaria' end) AS `tipo_ubicacion`, (case when (`c`.`activo_cliente` = false) then 'Cliente inactivo' when (`u`.`activo_ubicacion` = false) then 'Ubicación inactiva' else 'Activa' end) AS `estado_completo`, (case when (`u`.`persona_contacto_ubicacion` is not null) then true else false end) AS `tiene_contacto_propio`, (select count(0) from `cliente_ubicacion` `cu` where ((`cu`.`id_cliente` = `c`.`id_cliente`) and (`cu`.`activo_ubicacion` = true))) AS `total_ubicaciones_cliente` FROM (`cliente` `c` join `cliente_ubicacion` `u` on((`c`.`id_cliente` = `u`.`id_cliente`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_control_pagos`
--
DROP TABLE IF EXISTS `vista_control_pagos`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `vista_control_pagos`  AS SELECT `p`.`id_presupuesto` AS `id_presupuesto`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`fecha_presupuesto` AS `fecha_presupuesto`, `p`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`, `p`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `p`.`numero_pedido_cliente_presupuesto` AS `numero_pedido_cliente_presupuesto`, `c`.`id_cliente` AS `id_cliente`, coalesce(nullif(trim(`c`.`nombre_facturacion_cliente`),''),`c`.`nombre_cliente`) AS `nombre_completo_cliente`, `ep`.`id_estado_ppto` AS `id_estado_ppto`, `ep`.`nombre_estado_ppto` AS `nombre_estado_ppto`, `ep`.`codigo_estado_ppto` AS `codigo_estado_ppto`, `ep`.`color_estado_ppto` AS `color_estado_ppto`, `fp`.`nombre_pago` AS `nombre_forma_pago`, coalesce(`plan`.`total_acordado`,0) AS `total_presupuesto`, coalesce(`ag`.`total_pagado`,0) AS `total_pagado`, coalesce(`ag`.`total_conciliado`,0) AS `total_conciliado`, (coalesce(`plan`.`total_acordado`,0) - coalesce(`ag`.`total_pagado`,0)) AS `saldo_pendiente`, round((case when (coalesce(`ag`.`total_pagado`,0) > 0) then ((coalesce(`ag`.`total_conciliado`,0) / `ag`.`total_pagado`) * 100) else 0 end),2) AS `porcentaje_pagado`, `ag`.`fecha_ultimo_pago` AS `fecha_ultimo_pago`, `ag`.`metodos_pago_usados` AS `metodos_pago_usados`, coalesce(`ag`.`num_pagos`,0) AS `num_pagos`, `docs`.`tipos_documentos` AS `tipos_documentos`, `docs`.`fecha_ultima_factura` AS `fecha_ultima_factura`, `p`.`created_at_presupuesto` AS `created_at_presupuesto`, `p`.`updated_at_presupuesto` AS `updated_at_presupuesto` FROM ((((((`presupuesto` `p` join `estado_presupuesto` `ep` on(((`p`.`id_estado_ppto` = `ep`.`id_estado_ppto`) and (`ep`.`codigo_estado_ppto` = 'APROB')))) join `cliente` `c` on((`p`.`id_cliente` = `c`.`id_cliente`))) left join `forma_pago` `fp` on((`p`.`id_forma_pago` = `fp`.`id_pago`))) left join (select `pago_presupuesto`.`id_presupuesto` AS `id_presupuesto`,sum(`pago_presupuesto`.`importe_pago_ppto`) AS `total_acordado` from `pago_presupuesto` where (`pago_presupuesto`.`activo_pago_ppto` = 1) group by `pago_presupuesto`.`id_presupuesto`) `plan` on((`plan`.`id_presupuesto` = `p`.`id_presupuesto`))) left join (select `pp`.`id_presupuesto` AS `id_presupuesto`,sum((case when (`pp`.`tipo_pago_ppto` = 'devolucion') then -(`pp`.`importe_pago_ppto`) else `pp`.`importe_pago_ppto` end)) AS `total_pagado`,sum((case when ((`pp`.`estado_pago_ppto` = 'conciliado') and (`pp`.`tipo_pago_ppto` = 'devolucion')) then -(`pp`.`importe_pago_ppto`) when (`pp`.`estado_pago_ppto` = 'conciliado') then `pp`.`importe_pago_ppto` else 0 end)) AS `total_conciliado`,max(`pp`.`fecha_pago_ppto`) AS `fecha_ultimo_pago`,group_concat(distinct `mp`.`nombre_metodo_pago` order by `mp`.`nombre_metodo_pago` ASC separator ', ') AS `metodos_pago_usados`,count(`pp`.`id_pago_ppto`) AS `num_pagos` from (`pago_presupuesto` `pp` left join `metodo_pago` `mp` on((`pp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) where ((`pp`.`activo_pago_ppto` = 1) and (`pp`.`estado_pago_ppto` <> 'anulado')) group by `pp`.`id_presupuesto`) `ag` on((`ag`.`id_presupuesto` = `p`.`id_presupuesto`))) left join (select `dp`.`id_presupuesto` AS `id_presupuesto`,group_concat(distinct `dp`.`tipo_documento_ppto` order by `dp`.`tipo_documento_ppto` ASC separator ',') AS `tipos_documentos`,max((case when (`dp`.`tipo_documento_ppto` in ('factura_proforma','factura_anticipo','factura_final','factura_rectificativa')) then `dp`.`fecha_generacion_documento_ppto` end)) AS `fecha_ultima_factura` from `documento_presupuesto` `dp` where (`dp`.`activo_documento_ppto` = 1) group by `dp`.`id_presupuesto`) `docs` on((`docs`.`id_presupuesto` = `p`.`id_presupuesto`))) WHERE (`p`.`activo_presupuesto` = 1) ORDER BY `p`.`fecha_inicio_evento_presupuesto` ASC, `p`.`id_presupuesto` ASC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_costos_furgoneta`
--
DROP TABLE IF EXISTS `vista_costos_furgoneta`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_costos_furgoneta`  AS SELECT `f`.`id_furgoneta` AS `id_furgoneta`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`anio_furgoneta` AS `anio_furgoneta`, coalesce(sum(`m`.`costo_mantenimiento`),0) AS `costo_total`, coalesce(sum((case when (year(`m`.`fecha_mantenimiento`) = year(curdate())) then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_anio_actual`, coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'revision') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_revisiones`, coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'reparacion') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_reparaciones`, coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'itv') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_itv`, coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'neumaticos') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_neumaticos`, count(`m`.`id_mantenimiento`) AS `total_mantenimientos`, coalesce(avg(`m`.`costo_mantenimiento`),0) AS `costo_promedio`, max(`m`.`fecha_mantenimiento`) AS `fecha_ultimo_mantenimiento`, (select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) AS `kilometraje_actual`, (case when ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) > 0) then (coalesce(sum(`m`.`costo_mantenimiento`),0) / (select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`))) else NULL end) AS `costo_por_km` FROM (`furgoneta` `f` left join `furgoneta_mantenimiento` `m` on(((`f`.`id_furgoneta` = `m`.`id_furgoneta`) and (`m`.`activo_mantenimiento` = 1)))) GROUP BY `f`.`id_furgoneta`, `f`.`matricula_furgoneta`, `f`.`marca_furgoneta`, `f`.`modelo_furgoneta`, `f`.`anio_furgoneta` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_elementos_completa`
--
DROP TABLE IF EXISTS `vista_elementos_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_elementos_completa`  AS SELECT `e`.`id_elemento` AS `id_elemento`, `e`.`codigo_elemento` AS `codigo_elemento`, `e`.`codigo_barras_elemento` AS `codigo_barras_elemento`, `e`.`descripcion_elemento` AS `descripcion_elemento`, `e`.`numero_serie_elemento` AS `numero_serie_elemento`, `e`.`modelo_elemento` AS `modelo_elemento`, `e`.`nave_elemento` AS `nave_elemento`, `e`.`pasillo_columna_elemento` AS `pasillo_columna_elemento`, `e`.`altura_elemento` AS `altura_elemento`, concat_ws(' | ',coalesce(`e`.`nave_elemento`,''),coalesce(`e`.`pasillo_columna_elemento`,''),coalesce(`e`.`altura_elemento`,'')) AS `ubicacion_completa_elemento`, `e`.`peso_elemento` AS `peso_elemento`, `e`.`fecha_compra_elemento` AS `fecha_compra_elemento`, `e`.`precio_compra_elemento` AS `precio_compra_elemento`, `e`.`fecha_alta_elemento` AS `fecha_alta_elemento`, `e`.`fecha_fin_garantia_elemento` AS `fecha_fin_garantia_elemento`, `e`.`proximo_mantenimiento_elemento` AS `proximo_mantenimiento_elemento`, `e`.`observaciones_elemento` AS `observaciones_elemento`, `e`.`es_propio_elemento` AS `es_propio_elemento`, `e`.`id_proveedor_compra_elemento` AS `id_proveedor_compra_elemento`, `e`.`id_proveedor_alquiler_elemento` AS `id_proveedor_alquiler_elemento`, `e`.`precio_dia_alquiler_elemento` AS `precio_dia_alquiler_elemento`, `e`.`id_forma_pago_alquiler_elemento` AS `id_forma_pago_alquiler_elemento`, `e`.`observaciones_alquiler_elemento` AS `observaciones_alquiler_elemento`, `prov_compra`.`codigo_proveedor` AS `codigo_proveedor_compra`, `prov_compra`.`nombre_proveedor` AS `nombre_proveedor_compra`, `prov_compra`.`telefono_proveedor` AS `telefono_proveedor_compra`, `prov_compra`.`email_proveedor` AS `email_proveedor_compra`, `prov_compra`.`nif_proveedor` AS `nif_proveedor_compra`, `prov_alquiler`.`codigo_proveedor` AS `codigo_proveedor_alquiler`, `prov_alquiler`.`nombre_proveedor` AS `nombre_proveedor_alquiler`, `prov_alquiler`.`telefono_proveedor` AS `telefono_proveedor_alquiler`, `prov_alquiler`.`email_proveedor` AS `email_proveedor_alquiler`, `prov_alquiler`.`nif_proveedor` AS `nif_proveedor_alquiler`, `prov_alquiler`.`persona_contacto_proveedor` AS `persona_contacto_proveedor_alquiler`, `fp_alquiler`.`codigo_pago` AS `codigo_forma_pago_alquiler`, `fp_alquiler`.`nombre_pago` AS `nombre_forma_pago_alquiler`, `fp_alquiler`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_alquiler`, `fp_alquiler`.`dias_anticipo_pago` AS `dias_anticipo_alquiler`, `fp_alquiler`.`porcentaje_final_pago` AS `porcentaje_final_alquiler`, `fp_alquiler`.`dias_final_pago` AS `dias_final_alquiler`, `fp_alquiler`.`descuento_pago` AS `descuento_forma_pago_alquiler`, `mp_alquiler`.`codigo_metodo_pago` AS `codigo_metodo_pago_alquiler`, `mp_alquiler`.`nombre_metodo_pago` AS `nombre_metodo_pago_alquiler`, `a`.`id_articulo` AS `id_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `f`.`id_familia` AS `id_familia`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `g`.`id_grupo` AS `id_grupo`, `g`.`codigo_grupo` AS `codigo_grupo`, `g`.`nombre_grupo` AS `nombre_grupo`, `m`.`id_marca` AS `id_marca`, `m`.`codigo_marca` AS `codigo_marca`, `m`.`nombre_marca` AS `nombre_marca`, `est`.`id_estado_elemento` AS `id_estado_elemento`, `est`.`codigo_estado_elemento` AS `codigo_estado_elemento`, `est`.`descripcion_estado_elemento` AS `descripcion_estado_elemento`, `est`.`color_estado_elemento` AS `color_estado_elemento`, `est`.`permite_alquiler_estado_elemento` AS `permite_alquiler_estado_elemento`, `e`.`activo_elemento` AS `activo_elemento`, `e`.`created_at_elemento` AS `created_at_elemento`, `e`.`updated_at_elemento` AS `updated_at_elemento`, concat_ws(' > ',coalesce(`g`.`nombre_grupo`,'Sin grupo'),`f`.`nombre_familia`,`a`.`nombre_articulo`,`e`.`descripcion_elemento`) AS `jerarquia_completa_elemento`, (case when (`e`.`es_propio_elemento` = true) then 'PROPIO' else 'ALQUILADO A PROVEEDOR' end) AS `tipo_propiedad_elemento`, (case when (`e`.`es_propio_elemento` = true) then `prov_compra`.`nombre_proveedor` else `prov_alquiler`.`nombre_proveedor` end) AS `proveedor_principal_elemento`, (case when (`e`.`es_propio_elemento` = true) then 'N/A - Equipo propio' when (`e`.`id_proveedor_alquiler_elemento` is null) then 'Sin proveedor asignado' when ((`e`.`precio_dia_alquiler_elemento` is null) or (`e`.`precio_dia_alquiler_elemento` = 0)) then 'Proveedor asignado - Falta precio' when (`e`.`id_forma_pago_alquiler_elemento` is null) then 'Proveedor y precio OK - Falta forma de pago' else 'Completamente configurado' end) AS `estado_configuracion_alquiler`, (case when ((`e`.`es_propio_elemento` = false) and (`fp_alquiler`.`id_pago` is not null)) then (case when (`fp_alquiler`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp_alquiler`.`nombre_metodo_pago`,' - ',`fp_alquiler`.`nombre_pago`,(case when (`fp_alquiler`.`descuento_pago` > 0) then concat(' (Dto: ',`fp_alquiler`.`descuento_pago`,'%)') else '' end)) else concat(`mp_alquiler`.`nombre_metodo_pago`,' - ',`fp_alquiler`.`porcentaje_anticipo_pago`,'% + ',`fp_alquiler`.`porcentaje_final_pago`,'%') end) else NULL end) AS `descripcion_forma_pago_alquiler`, (case when ((`e`.`es_propio_elemento` = false) and (`e`.`precio_dia_alquiler_elemento` is not null)) then round((`e`.`precio_dia_alquiler_elemento` * 30),2) else NULL end) AS `costo_mensual_estimado_alquiler`, (to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) AS `dias_en_servicio_elemento`, round(((to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) / 365.25),2) AS `anios_en_servicio_elemento` FROM (((((((((`elemento` `e` join `articulo` `a` on((`e`.`id_articulo_elemento` = `a`.`id_articulo`))) join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) left join `marca` `m` on((`e`.`id_marca_elemento` = `m`.`id_marca`))) join `estado_elemento` `est` on((`e`.`id_estado_elemento` = `est`.`id_estado_elemento`))) left join `proveedor` `prov_compra` on((`e`.`id_proveedor_compra_elemento` = `prov_compra`.`id_proveedor`))) left join `proveedor` `prov_alquiler` on((`e`.`id_proveedor_alquiler_elemento` = `prov_alquiler`.`id_proveedor`))) left join `forma_pago` `fp_alquiler` on((`e`.`id_forma_pago_alquiler_elemento` = `fp_alquiler`.`id_pago`))) left join `metodo_pago` `mp_alquiler` on((`fp_alquiler`.`id_metodo_pago` = `mp_alquiler`.`id_metodo_pago`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_furgoneta_completa`
--
DROP TABLE IF EXISTS `vista_furgoneta_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_furgoneta_completa`  AS SELECT `f`.`id_furgoneta` AS `id_furgoneta`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`anio_furgoneta` AS `anio_furgoneta`, `f`.`numero_bastidor_furgoneta` AS `numero_bastidor_furgoneta`, `f`.`kilometros_entre_revisiones_furgoneta` AS `kilometros_entre_revisiones_furgoneta`, `f`.`fecha_proxima_itv_furgoneta` AS `fecha_proxima_itv_furgoneta`, `f`.`fecha_vencimiento_seguro_furgoneta` AS `fecha_vencimiento_seguro_furgoneta`, `f`.`compania_seguro_furgoneta` AS `compania_seguro_furgoneta`, `f`.`numero_poliza_seguro_furgoneta` AS `numero_poliza_seguro_furgoneta`, `f`.`capacidad_carga_kg_furgoneta` AS `capacidad_carga_kg_furgoneta`, `f`.`capacidad_carga_m3_furgoneta` AS `capacidad_carga_m3_furgoneta`, `f`.`tipo_combustible_furgoneta` AS `tipo_combustible_furgoneta`, `f`.`consumo_medio_furgoneta` AS `consumo_medio_furgoneta`, `f`.`taller_habitual_furgoneta` AS `taller_habitual_furgoneta`, `f`.`telefono_taller_furgoneta` AS `telefono_taller_furgoneta`, `f`.`estado_furgoneta` AS `estado_furgoneta`, `f`.`observaciones_furgoneta` AS `observaciones_furgoneta`, `f`.`activo_furgoneta` AS `activo_furgoneta`, `f`.`created_at_furgoneta` AS `created_at_furgoneta`, `f`.`updated_at_furgoneta` AS `updated_at_furgoneta`, (select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) AS `kilometraje_actual`, (select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1) AS `fecha_ultimo_registro_km`, (select count(0) from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1))) AS `total_mantenimientos`, (select sum(`furgoneta_mantenimiento`.`costo_mantenimiento`) from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1))) AS `costo_total_mantenimientos`, (select `furgoneta_mantenimiento`.`fecha_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1) AS `fecha_ultimo_mantenimiento`, (case when ((to_days(`f`.`fecha_proxima_itv_furgoneta`) - to_days(curdate())) < 0) then 'ITV_VENCIDA' when ((to_days(`f`.`fecha_proxima_itv_furgoneta`) - to_days(curdate())) <= 30) then 'ITV_PROXIMA' else 'ITV_OK' end) AS `estado_itv`, (case when ((to_days(`f`.`fecha_vencimiento_seguro_furgoneta`) - to_days(curdate())) < 0) then 'SEGURO_VENCIDO' when ((to_days(`f`.`fecha_vencimiento_seguro_furgoneta`) - to_days(curdate())) <= 30) then 'SEGURO_PROXIMO' else 'SEGURO_OK' end) AS `estado_seguro`, (case when ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) is not null) then ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) - coalesce((select `furgoneta_mantenimiento`.`kilometraje_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`tipo_mantenimiento` = 'revision') and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1),0)) else NULL end) AS `km_desde_ultima_revision`, (case when (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) is not null) and (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) - coalesce((select `furgoneta_mantenimiento`.`kilometraje_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`tipo_mantenimiento` = 'revision') and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1),0)) >= `f`.`kilometros_entre_revisiones_furgoneta`)) then true else false end) AS `necesita_revision` FROM `furgoneta` AS `f` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_kilometraje_completo`
--
DROP TABLE IF EXISTS `vista_kilometraje_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_kilometraje_completo`  AS SELECT `rk`.`id_registro_km` AS `id_registro_km`, `rk`.`id_furgoneta` AS `id_furgoneta`, `rk`.`fecha_registro_km` AS `fecha_registro_km`, `rk`.`kilometraje_registrado_km` AS `kilometraje_registrado_km`, `rk`.`tipo_registro_km` AS `tipo_registro_km`, `rk`.`observaciones_registro_km` AS `observaciones_registro_km`, `rk`.`created_at_registro_km` AS `created_at_registro_km`, `rk`.`updated_at_registro_km` AS `updated_at_registro_km`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`estado_furgoneta` AS `estado_furgoneta`, coalesce((`rk`.`kilometraje_registrado_km` - lag(`rk`.`kilometraje_registrado_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) ),0) AS `km_recorridos`, coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),0) AS `dias_transcurridos`, (case when (coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),0) > 0) then round((coalesce((`rk`.`kilometraje_registrado_km` - lag(`rk`.`kilometraje_registrado_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) ),0) / coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),1)),2) else 0 end) AS `km_promedio_diario` FROM (`furgoneta_registro_kilometraje` `rk` join `furgoneta` `f` on((`rk`.`id_furgoneta` = `f`.`id_furgoneta`))) ORDER BY `rk`.`id_furgoneta` ASC, `rk`.`fecha_registro_km` DESC, `rk`.`kilometraje_registrado_km` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_kit_completa`
--
DROP TABLE IF EXISTS `vista_kit_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_kit_completa`  AS SELECT `k`.`id_kit` AS `id_kit`, `k`.`cantidad_kit` AS `cantidad_kit`, `k`.`activo_kit` AS `activo_kit`, `k`.`created_at_kit` AS `created_at_kit`, `k`.`updated_at_kit` AS `updated_at_kit`, `k`.`id_articulo_maestro` AS `id_articulo_maestro`, `am`.`codigo_articulo` AS `codigo_articulo_maestro`, `am`.`nombre_articulo` AS `nombre_articulo_maestro`, `am`.`name_articulo` AS `name_articulo_maestro`, `am`.`precio_alquiler_articulo` AS `precio_articulo_maestro`, `am`.`es_kit_articulo` AS `es_kit_articulo_maestro`, `am`.`activo_articulo` AS `activo_articulo_maestro`, `k`.`id_articulo_componente` AS `id_articulo_componente`, `ac`.`codigo_articulo` AS `codigo_articulo_componente`, `ac`.`nombre_articulo` AS `nombre_articulo_componente`, `ac`.`name_articulo` AS `name_articulo_componente`, `ac`.`precio_alquiler_articulo` AS `precio_articulo_componente`, `ac`.`es_kit_articulo` AS `es_kit_articulo_componente`, `ac`.`activo_articulo` AS `activo_articulo_componente`, (`k`.`cantidad_kit` * `ac`.`precio_alquiler_articulo`) AS `subtotal_componente`, (select count(0) from `kit` `k2` where ((`k2`.`id_articulo_maestro` = `k`.`id_articulo_maestro`) and (`k2`.`activo_kit` = 1))) AS `total_componentes_kit`, (select sum((`k2`.`cantidad_kit` * `a2`.`precio_alquiler_articulo`)) from (`kit` `k2` join `articulo` `a2` on((`k2`.`id_articulo_componente` = `a2`.`id_articulo`))) where ((`k2`.`id_articulo_maestro` = `k`.`id_articulo_maestro`) and (`k2`.`activo_kit` = 1) and (`a2`.`activo_articulo` = 1))) AS `precio_total_kit` FROM ((`kit` `k` join `articulo` `am` on((`k`.`id_articulo_maestro` = `am`.`id_articulo`))) join `articulo` `ac` on((`k`.`id_articulo_componente` = `ac`.`id_articulo`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_kit_peso_total`
--
DROP TABLE IF EXISTS `vista_kit_peso_total`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_kit_peso_total`  AS SELECT `k`.`id_articulo_maestro` AS `id_articulo`, `am`.`codigo_articulo` AS `codigo_articulo`, `am`.`nombre_articulo` AS `nombre_articulo`, `am`.`es_kit_articulo` AS `es_kit_articulo`, count(distinct `k`.`id_articulo_componente`) AS `total_componentes`, count(distinct (case when (`vpm`.`peso_medio_kg` is not null) then `k`.`id_articulo_componente` end)) AS `componentes_con_peso`, sum((`k`.`cantidad_kit` * coalesce(`vpm`.`peso_medio_kg`,0))) AS `peso_total_kit_kg` FROM ((`articulo` `am` join `kit` `k` on(((`am`.`id_articulo` = `k`.`id_articulo_maestro`) and (`k`.`activo_kit` = 1)))) left join `vista_articulo_peso_medio` `vpm` on((`k`.`id_articulo_componente` = `vpm`.`id_articulo`))) WHERE ((`am`.`activo_articulo` = 1) AND (`am`.`es_kit_articulo` = 1)) GROUP BY `k`.`id_articulo_maestro`, `am`.`codigo_articulo`, `am`.`nombre_articulo`, `am`.`es_kit_articulo` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_linea_peso`
--
DROP TABLE IF EXISTS `vista_linea_peso`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_linea_peso`  AS SELECT `lp`.`id_linea_ppto` AS `id_linea_ppto`, `lp`.`id_version_presupuesto` AS `id_version_presupuesto`, `lp`.`id_articulo` AS `id_articulo`, `lp`.`numero_linea_ppto` AS `numero_linea_ppto`, `lp`.`tipo_linea_ppto` AS `tipo_linea_ppto`, `lp`.`cantidad_linea_ppto` AS `cantidad_linea_ppto`, `lp`.`descripcion_linea_ppto` AS `descripcion_linea_ppto`, `lp`.`codigo_linea_ppto` AS `codigo_linea_ppto`, `vap`.`codigo_articulo` AS `codigo_articulo`, `vap`.`nombre_articulo` AS `nombre_articulo`, `vap`.`es_kit_articulo` AS `es_kit_articulo`, `vap`.`peso_articulo_kg` AS `peso_articulo_kg`, `vap`.`metodo_calculo` AS `metodo_calculo`, `vap`.`tiene_datos_peso` AS `tiene_datos_peso`, (`lp`.`cantidad_linea_ppto` * coalesce(`vap`.`peso_articulo_kg`,0)) AS `peso_total_linea_kg`, (case when ((`lp`.`tipo_linea_ppto` in ('articulo','kit')) and (`vap`.`tiene_datos_peso` = true)) then true else false end) AS `linea_tiene_peso` FROM (`linea_presupuesto` `lp` left join `vista_articulo_peso` `vap` on((`lp`.`id_articulo` = `vap`.`id_articulo`))) WHERE ((`lp`.`activo_linea_ppto` = 1) AND (`lp`.`mostrar_en_presupuesto` = 1)) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_mantenimiento_completo`
--
DROP TABLE IF EXISTS `vista_mantenimiento_completo`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_mantenimiento_completo`  AS SELECT `m`.`id_mantenimiento` AS `id_mantenimiento`, `m`.`id_furgoneta` AS `id_furgoneta`, `m`.`fecha_mantenimiento` AS `fecha_mantenimiento`, `m`.`tipo_mantenimiento` AS `tipo_mantenimiento`, `m`.`descripcion_mantenimiento` AS `descripcion_mantenimiento`, `m`.`kilometraje_mantenimiento` AS `kilometraje_mantenimiento`, `m`.`costo_mantenimiento` AS `costo_mantenimiento`, `m`.`numero_factura_mantenimiento` AS `numero_factura_mantenimiento`, `m`.`taller_mantenimiento` AS `taller_mantenimiento`, `m`.`telefono_taller_mantenimiento` AS `telefono_taller_mantenimiento`, `m`.`direccion_taller_mantenimiento` AS `direccion_taller_mantenimiento`, `m`.`resultado_itv` AS `resultado_itv`, `m`.`fecha_proxima_itv` AS `fecha_proxima_itv`, `m`.`garantia_hasta_mantenimiento` AS `garantia_hasta_mantenimiento`, `m`.`observaciones_mantenimiento` AS `observaciones_mantenimiento`, `m`.`activo_mantenimiento` AS `activo_mantenimiento`, `m`.`created_at_mantenimiento` AS `created_at_mantenimiento`, `m`.`updated_at_mantenimiento` AS `updated_at_mantenimiento`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`anio_furgoneta` AS `anio_furgoneta`, `f`.`estado_furgoneta` AS `estado_furgoneta`, (case when (`m`.`garantia_hasta_mantenimiento` is null) then 'SIN_GARANTIA' when (`m`.`garantia_hasta_mantenimiento` < curdate()) then 'GARANTIA_VENCIDA' when ((to_days(`m`.`garantia_hasta_mantenimiento`) - to_days(curdate())) <= 30) then 'GARANTIA_PROXIMA' else 'GARANTIA_VIGENTE' end) AS `estado_garantia`, (to_days(curdate()) - to_days(`m`.`fecha_mantenimiento`)) AS `dias_desde_mantenimiento`, (case when (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `m`.`id_furgoneta`)) is not null) and (`m`.`kilometraje_mantenimiento` is not null)) then ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `m`.`id_furgoneta`)) - `m`.`kilometraje_mantenimiento`) else NULL end) AS `km_desde_mantenimiento` FROM (`furgoneta_mantenimiento` `m` join `furgoneta` `f` on((`m`.`id_furgoneta` = `f`.`id_furgoneta`))) WHERE (`m`.`activo_mantenimiento` = 1) ORDER BY `m`.`fecha_mantenimiento` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_presupuesto_completa`
--
DROP TABLE IF EXISTS `vista_presupuesto_completa`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `vista_presupuesto_completa`  AS SELECT `p`.`id_presupuesto` AS `id_presupuesto`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`version_actual_presupuesto` AS `version_actual_presupuesto`, `p`.`fecha_presupuesto` AS `fecha_presupuesto`, `p`.`fecha_validez_presupuesto` AS `fecha_validez_presupuesto`, `p`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`, `p`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`, `p`.`numero_pedido_cliente_presupuesto` AS `numero_pedido_cliente_presupuesto`, `p`.`aplicar_coeficientes_presupuesto` AS `aplicar_coeficientes_presupuesto`, `p`.`descuento_presupuesto` AS `descuento_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `p`.`direccion_evento_presupuesto` AS `direccion_evento_presupuesto`, `p`.`poblacion_evento_presupuesto` AS `poblacion_evento_presupuesto`, `p`.`cp_evento_presupuesto` AS `cp_evento_presupuesto`, `p`.`provincia_evento_presupuesto` AS `provincia_evento_presupuesto`, `p`.`observaciones_cabecera_presupuesto` AS `observaciones_cabecera_presupuesto`, `p`.`observaciones_pie_presupuesto` AS `observaciones_pie_presupuesto`, `p`.`observaciones_cabecera_ingles_presupuesto` AS `observaciones_cabecera_ingles_presupuesto`, `p`.`observaciones_pie_ingles_presupuesto` AS `observaciones_pie_ingles_presupuesto`, `p`.`destacar_observaciones_pie_presupuesto` AS `destacar_observaciones_pie_presupuesto`, `p`.`mostrar_obs_familias_presupuesto` AS `mostrar_obs_familias_presupuesto`, `p`.`mostrar_obs_articulos_presupuesto` AS `mostrar_obs_articulos_presupuesto`, `p`.`observaciones_internas_presupuesto` AS `observaciones_internas_presupuesto`, `p`.`activo_presupuesto` AS `activo_presupuesto`, `p`.`created_at_presupuesto` AS `created_at_presupuesto`, `p`.`updated_at_presupuesto` AS `updated_at_presupuesto`, `c`.`id_cliente` AS `id_cliente`, `c`.`codigo_cliente` AS `codigo_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`nif_cliente` AS `nif_cliente`, `c`.`direccion_cliente` AS `direccion_cliente`, `c`.`cp_cliente` AS `cp_cliente`, `c`.`poblacion_cliente` AS `poblacion_cliente`, `c`.`provincia_cliente` AS `provincia_cliente`, `c`.`telefono_cliente` AS `telefono_cliente`, `c`.`email_cliente` AS `email_cliente`, `c`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`, `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, `c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`, `c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`, `c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`, `c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`, `c`.`exento_iva_cliente` AS `exento_iva_cliente`, `c`.`justificacion_exencion_iva_cliente` AS `justificacion_exencion_iva_cliente`, `cc`.`id_contacto_cliente` AS `id_contacto_cliente`, `cc`.`nombre_contacto_cliente` AS `nombre_contacto_cliente`, `cc`.`apellidos_contacto_cliente` AS `apellidos_contacto_cliente`, `cc`.`telefono_contacto_cliente` AS `telefono_contacto_cliente`, `cc`.`email_contacto_cliente` AS `email_contacto_cliente`, `ep`.`id_estado_ppto` AS `id_estado_ppto`, `ep`.`codigo_estado_ppto` AS `codigo_estado_ppto`, `ep`.`nombre_estado_ppto` AS `nombre_estado_ppto`, `ep`.`color_estado_ppto` AS `color_estado_ppto`, `ep`.`orden_estado_ppto` AS `orden_estado_ppto`, `fp`.`id_pago` AS `id_forma_pago`, `fp`.`codigo_pago` AS `codigo_pago`, `fp`.`nombre_pago` AS `nombre_pago`, `fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`, `fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`, `fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`, `fp`.`dias_final_pago` AS `dias_final_pago`, `fp`.`descuento_pago` AS `descuento_pago`, `mp`.`id_metodo_pago` AS `id_metodo_pago`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`, `mc`.`id_metodo` AS `id_metodo_contacto`, `mc`.`nombre` AS `nombre_metodo_contacto`, `c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`, `fph`.`nombre_pago` AS `nombre_forma_pago_habitual_cliente`, concat_ws(', ',`p`.`direccion_evento_presupuesto`,concat(`p`.`cp_evento_presupuesto`,' ',`p`.`poblacion_evento_presupuesto`),`p`.`provincia_evento_presupuesto`) AS `direccion_completa_evento_presupuesto`, concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`, concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) AS `direccion_facturacion_completa_cliente`, concat_ws(' ',`cc`.`nombre_contacto_cliente`,`cc`.`apellidos_contacto_cliente`) AS `nombre_completo_contacto`, (to_days(`p`.`fecha_validez_presupuesto`) - to_days(curdate())) AS `dias_validez_restantes`, (case when (`p`.`fecha_validez_presupuesto` is null) then 'Sin fecha de validez' when (`p`.`fecha_validez_presupuesto` < curdate()) then 'Caducado' when (`p`.`fecha_validez_presupuesto` = curdate()) then 'Caduca hoy' when ((to_days(`p`.`fecha_validez_presupuesto`) - to_days(curdate())) <= 7) then 'Próximo a caducar' else 'Vigente' end) AS `estado_validez_presupuesto`, ((to_days(`p`.`fecha_fin_evento_presupuesto`) - to_days(`p`.`fecha_inicio_evento_presupuesto`)) + 1) AS `duracion_evento_dias`, (to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) AS `dias_hasta_inicio_evento`, (to_days(`p`.`fecha_fin_evento_presupuesto`) - to_days(curdate())) AS `dias_hasta_fin_evento`, (case when (`p`.`fecha_inicio_evento_presupuesto` is null) then 'Sin fecha de evento' when ((`p`.`fecha_inicio_evento_presupuesto` < curdate()) and (`p`.`fecha_fin_evento_presupuesto` < curdate())) then 'Evento finalizado' when ((`p`.`fecha_inicio_evento_presupuesto` <= curdate()) and (`p`.`fecha_fin_evento_presupuesto` >= curdate())) then 'Evento en curso' when (`p`.`fecha_inicio_evento_presupuesto` = curdate()) then 'Evento HOY' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) = 1) then 'Evento MAÑANA' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 7) then 'Evento esta semana' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 30) then 'Evento este mes' else 'Evento futuro' end) AS `estado_evento_presupuesto`, (case when (`p`.`fecha_inicio_evento_presupuesto` is null) then 'Sin prioridad' when (`p`.`fecha_inicio_evento_presupuesto` = curdate()) then 'HOY' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) = 1) then 'MAÑANA' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 7) then 'Esta semana' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 15) then 'Próximo' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 30) then 'Este mes' else 'Futuro' end) AS `prioridad_presupuesto`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_presupuesto`, (case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_completa_forma_pago`, (case when (`fp`.`dias_anticipo_pago` = 0) then `p`.`fecha_presupuesto` else (`p`.`fecha_presupuesto` + interval `fp`.`dias_anticipo_pago` day) end) AS `fecha_vencimiento_anticipo`, (case when ((`fp`.`dias_final_pago` = 0) and (`p`.`fecha_fin_evento_presupuesto` is not null)) then `p`.`fecha_fin_evento_presupuesto` when (`fp`.`dias_final_pago` > 0) then (`p`.`fecha_presupuesto` + interval `fp`.`dias_final_pago` day) when ((`fp`.`dias_final_pago` < 0) and (`p`.`fecha_inicio_evento_presupuesto` is not null)) then (`p`.`fecha_inicio_evento_presupuesto` + interval `fp`.`dias_final_pago` day) else NULL end) AS `fecha_vencimiento_final`, (case when (`p`.`descuento_presupuesto` = `c`.`porcentaje_descuento_cliente`) then 'Igual al habitual' when (`p`.`descuento_presupuesto` > `c`.`porcentaje_descuento_cliente`) then 'Mayor al habitual' when (`p`.`descuento_presupuesto` < `c`.`porcentaje_descuento_cliente`) then 'Menor al habitual' else 'Sin comparar' end) AS `comparacion_descuento`, (case when (`p`.`descuento_presupuesto` = 0.00) then 'Sin descuento' when ((`p`.`descuento_presupuesto` > 0.00) and (`p`.`descuento_presupuesto` <= 5.00)) then concat('Descuento bajo: ',`p`.`descuento_presupuesto`,'%') when ((`p`.`descuento_presupuesto` > 5.00) and (`p`.`descuento_presupuesto` <= 15.00)) then concat('Descuento medio: ',`p`.`descuento_presupuesto`,'%') when (`p`.`descuento_presupuesto` > 15.00) then concat('Descuento alto: ',`p`.`descuento_presupuesto`,'%') else 'Sin descuento' end) AS `estado_descuento_presupuesto`, (case when (`p`.`descuento_presupuesto` > 0.00) then true else false end) AS `aplica_descuento_presupuesto`, (`p`.`descuento_presupuesto` - `c`.`porcentaje_descuento_cliente`) AS `diferencia_descuento`, (case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`, (to_days(curdate()) - to_days(`p`.`fecha_presupuesto`)) AS `dias_desde_emision`, `pv`.`id_version_presupuesto` AS `id_version_actual`, `pv`.`numero_version_presupuesto` AS `numero_version_actual`, `pv`.`estado_version_presupuesto` AS `estado_version_actual`, `pv`.`fecha_creacion_version` AS `fecha_creacion_version_actual`, (case when (`ep`.`codigo_estado_ppto` = 'CANC') then 'Cancelado' when (`ep`.`codigo_estado_ppto` = 'FACT') then 'Facturado' when ((`p`.`fecha_validez_presupuesto` < curdate()) and (`ep`.`codigo_estado_ppto` not in ('ACEP','RECH','CANC','FACT'))) then 'Caducado' when ((`p`.`fecha_inicio_evento_presupuesto` < curdate()) and (`p`.`fecha_fin_evento_presupuesto` < curdate())) then 'Evento finalizado' when ((`p`.`fecha_inicio_evento_presupuesto` <= curdate()) and (`p`.`fecha_fin_evento_presupuesto` >= curdate())) then 'Evento en curso' when (`ep`.`codigo_estado_ppto` = 'ACEP') then 'Aceptado - Pendiente evento' else `ep`.`nombre_estado_ppto` end) AS `estado_general_presupuesto`, `vpp`.`peso_total_kg` AS `peso_total_kg`, `vpp`.`peso_articulos_normales_kg` AS `peso_articulos_normales_kg`, `vpp`.`peso_kits_kg` AS `peso_kits_kg`, `vpp`.`total_lineas` AS `total_lineas`, `vpp`.`lineas_con_peso` AS `lineas_con_peso`, `vpp`.`lineas_sin_peso` AS `lineas_sin_peso`, `vpp`.`porcentaje_completitud` AS `porcentaje_completitud_peso` FROM (((((((((`presupuesto` `p` join `cliente` `c` on((`p`.`id_cliente` = `c`.`id_cliente`))) left join `contacto_cliente` `cc` on((`p`.`id_contacto_cliente` = `cc`.`id_contacto_cliente`))) join `estado_presupuesto` `ep` on((`p`.`id_estado_ppto` = `ep`.`id_estado_ppto`))) left join `forma_pago` `fp` on((`p`.`id_forma_pago` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) left join `metodos_contacto` `mc` on((`p`.`id_metodo` = `mc`.`id_metodo`))) left join `forma_pago` `fph` on((`c`.`id_forma_pago_habitual` = `fph`.`id_pago`))) left join `presupuesto_version` `pv` on(((`p`.`id_presupuesto` = `pv`.`id_presupuesto`) and (`pv`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) left join `vista_presupuesto_peso` `vpp` on((`pv`.`id_version_presupuesto` = `vpp`.`id_version_presupuesto`))) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_presupuesto_peso`
--
DROP TABLE IF EXISTS `vista_presupuesto_peso`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_presupuesto_peso`  AS SELECT `pv`.`id_version_presupuesto` AS `id_version_presupuesto`, `pv`.`id_presupuesto` AS `id_presupuesto`, coalesce(sum(`vlp`.`peso_total_linea_kg`),0.000) AS `peso_total_kg`, sum((case when (`vlp`.`metodo_calculo` = 'MEDIA_ELEMENTOS') then `vlp`.`peso_total_linea_kg` else 0 end)) AS `peso_articulos_normales_kg`, sum((case when (`vlp`.`metodo_calculo` = 'SUMA_COMPONENTES') then `vlp`.`peso_total_linea_kg` else 0 end)) AS `peso_kits_kg`, count(`vlp`.`id_linea_ppto`) AS `total_lineas`, count((case when (`vlp`.`linea_tiene_peso` = true) then 1 end)) AS `lineas_con_peso`, count((case when (`vlp`.`linea_tiene_peso` = false) then 1 end)) AS `lineas_sin_peso`, round(((count((case when (`vlp`.`linea_tiene_peso` = true) then 1 end)) * 100.0) / nullif(count(`vlp`.`id_linea_ppto`),0)),2) AS `porcentaje_completitud` FROM (`presupuesto_version` `pv` left join `vista_linea_peso` `vlp` on((`pv`.`id_version_presupuesto` = `vlp`.`id_version_presupuesto`))) GROUP BY `pv`.`id_version_presupuesto`, `pv`.`id_presupuesto` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_progreso_salida`
--
DROP TABLE IF EXISTS `vista_progreso_salida`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `vista_progreso_salida`  AS SELECT `sa`.`id_salida_almacen` AS `id_salida_almacen`, `lp`.`id_articulo` AS `id_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, sum(`lp`.`cantidad_linea_ppto`) AS `cantidad_requerida`, coalesce(sum((case when ((`lsa`.`id_linea_salida` is not null) and (`lsa`.`es_backup_linea_salida` = 0) and (`lsa`.`activo_linea_salida` = 1)) then 1 else 0 end)),0) AS `cantidad_escaneada`, coalesce(sum((case when ((`lsa`.`id_linea_salida` is not null) and (`lsa`.`es_backup_linea_salida` = 1) and (`lsa`.`activo_linea_salida` = 1)) then 1 else 0 end)),0) AS `cantidad_backup`, (case when (coalesce(sum((case when ((`lsa`.`id_linea_salida` is not null) and (`lsa`.`es_backup_linea_salida` = 0) and (`lsa`.`activo_linea_salida` = 1)) then 1 else 0 end)),0) >= sum(`lp`.`cantidad_linea_ppto`)) then 1 else 0 end) AS `esta_completo`, `cu`.`id_ubicacion` AS `id_ubicacion_linea`, `cu`.`nombre_ubicacion` AS `nombre_ubicacion_linea` FROM (((((`salida_almacen` `sa` join `presupuesto_version` `pv` on((`sa`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`))) join `linea_presupuesto` `lp` on(((`lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`) and (`lp`.`tipo_linea_ppto` in ('articulo','kit')) and (`lp`.`activo_linea_ppto` = 1)))) join `articulo` `a` on((`lp`.`id_articulo` = `a`.`id_articulo`))) left join `linea_salida_almacen` `lsa` on(((`lsa`.`id_salida_almacen` = `sa`.`id_salida_almacen`) and (`lsa`.`id_articulo` = `lp`.`id_articulo`)))) left join `cliente_ubicacion` `cu` on((`lp`.`id_ubicacion` = `cu`.`id_ubicacion`))) WHERE (`sa`.`activo_salida_almacen` = 1) GROUP BY `sa`.`id_salida_almacen`, `lp`.`id_articulo`, `a`.`nombre_articulo`, `a`.`codigo_articulo`, `cu`.`id_ubicacion`, `cu`.`nombre_ubicacion` ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_registro_kilometraje`
--
DROP TABLE IF EXISTS `vista_registro_kilometraje`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `vista_registro_kilometraje`  AS SELECT `rk`.`id_registro_km` AS `id_registro_km`, `rk`.`id_furgoneta` AS `id_furgoneta`, `rk`.`fecha_registro_km` AS `fecha_registro_km`, `rk`.`kilometraje_registrado_km` AS `kilometraje_registrado_km`, `rk`.`tipo_registro_km` AS `tipo_registro_km`, `rk`.`observaciones_registro_km` AS `observaciones_registro_km`, `rk`.`created_at_registro_km` AS `created_at_registro_km`, `f`.`matricula_furgoneta` AS `matricula_furgoneta`, `f`.`marca_furgoneta` AS `marca_furgoneta`, `f`.`modelo_furgoneta` AS `modelo_furgoneta`, `f`.`estado_furgoneta` AS `estado_furgoneta`, (`rk`.`kilometraje_registrado_km` - coalesce((select `furgoneta_registro_kilometraje`.`kilometraje_registrado_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),0)) AS `km_recorridos`, (to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`))) AS `dias_transcurridos`, (case when ((to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`))) > 0) then ((`rk`.`kilometraje_registrado_km` - coalesce((select `furgoneta_registro_kilometraje`.`kilometraje_registrado_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),0)) / (to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`)))) else 0 end) AS `km_promedio_diario` FROM (`furgoneta_registro_kilometraje` `rk` join `furgoneta` `f` on((`rk`.`id_furgoneta` = `f`.`id_furgoneta`))) ORDER BY `rk`.`fecha_registro_km` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `vista_ubicacion_actual_elemento`
--
DROP TABLE IF EXISTS `vista_ubicacion_actual_elemento`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `vista_ubicacion_actual_elemento`  AS SELECT `lsa`.`id_salida_almacen` AS `id_salida_almacen`, `lsa`.`id_linea_salida` AS `id_linea_salida`, `lsa`.`id_elemento` AS `id_elemento`, `e`.`codigo_elemento` AS `codigo_elemento`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`codigo_articulo` AS `codigo_articulo`, `lsa`.`es_backup_linea_salida` AS `es_backup_linea_salida`, `ult`.`id_ubicacion_destino` AS `id_ubicacion_actual`, `cu_dest`.`nombre_ubicacion` AS `nombre_ubicacion_actual`, `ult`.`fecha_movimiento` AS `fecha_ultimo_movimiento`, `ult`.`id_usuario_movimiento` AS `id_usuario_movimiento` FROM ((((`linea_salida_almacen` `lsa` join `elemento` `e` on((`lsa`.`id_elemento` = `e`.`id_elemento`))) join `articulo` `a` on((`lsa`.`id_articulo` = `a`.`id_articulo`))) left join (select `m1`.`id_movimiento` AS `id_movimiento`,`m1`.`id_linea_salida` AS `id_linea_salida`,`m1`.`id_ubicacion_origen` AS `id_ubicacion_origen`,`m1`.`id_ubicacion_destino` AS `id_ubicacion_destino`,`m1`.`id_usuario_movimiento` AS `id_usuario_movimiento`,`m1`.`fecha_movimiento` AS `fecha_movimiento`,`m1`.`observaciones_movimiento` AS `observaciones_movimiento`,`m1`.`activo_movimiento` AS `activo_movimiento`,`m1`.`created_at_movimiento` AS `created_at_movimiento`,`m1`.`updated_at_movimiento` AS `updated_at_movimiento` from (`movimiento_elemento_salida` `m1` join (select `movimiento_elemento_salida`.`id_linea_salida` AS `id_linea_salida`,max(`movimiento_elemento_salida`.`fecha_movimiento`) AS `max_fecha` from `movimiento_elemento_salida` where (`movimiento_elemento_salida`.`activo_movimiento` = 1) group by `movimiento_elemento_salida`.`id_linea_salida`) `m2` on(((`m1`.`id_linea_salida` = `m2`.`id_linea_salida`) and (`m1`.`fecha_movimiento` = `m2`.`max_fecha`)))) where (`m1`.`activo_movimiento` = 1)) `ult` on((`lsa`.`id_linea_salida` = `ult`.`id_linea_salida`))) left join `cliente_ubicacion` `cu_dest` on((`ult`.`id_ubicacion_destino` = `cu_dest`.`id_ubicacion`))) WHERE (`lsa`.`activo_linea_salida` = 1) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_documentos_presupuesto`
--
DROP TABLE IF EXISTS `v_documentos_presupuesto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `v_documentos_presupuesto`  AS SELECT `dp`.`id_documento_ppto` AS `id_documento_ppto`, `dp`.`id_presupuesto` AS `id_presupuesto`, `dp`.`id_version_presupuesto` AS `id_version_presupuesto`, `dp`.`id_empresa` AS `id_empresa`, `dp`.`seleccion_manual_empresa_documento_ppto` AS `seleccion_manual_empresa_documento_ppto`, `dp`.`tipo_documento_ppto` AS `tipo_documento_ppto`, `dp`.`numero_documento_ppto` AS `numero_documento_ppto`, `dp`.`serie_documento_ppto` AS `serie_documento_ppto`, `dp`.`id_documento_origen` AS `id_documento_origen`, `dp`.`motivo_abono_documento_ppto` AS `motivo_abono_documento_ppto`, `dp`.`subtotal_documento_ppto` AS `subtotal_documento_ppto`, `dp`.`total_iva_documento_ppto` AS `total_iva_documento_ppto`, `dp`.`total_documento_ppto` AS `total_documento_ppto`, `dp`.`ruta_pdf_documento_ppto` AS `ruta_pdf_documento_ppto`, `dp`.`tamano_pdf_documento_ppto` AS `tamano_pdf_documento_ppto`, `dp`.`fecha_emision_documento_ppto` AS `fecha_emision_documento_ppto`, `dp`.`fecha_generacion_documento_ppto` AS `fecha_generacion_documento_ppto`, `dp`.`observaciones_documento_ppto` AS `observaciones_documento_ppto`, `dp`.`activo_documento_ppto` AS `activo_documento_ppto`, `dp`.`created_at_documento_ppto` AS `created_at_documento_ppto`, `dp`.`updated_at_documento_ppto` AS `updated_at_documento_ppto`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `vt`.`total_con_iva` AS `total_presupuesto`, `c`.`id_cliente` AS `id_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, ifnull(`c`.`nombre_facturacion_cliente`,`c`.`nombre_cliente`) AS `nombre_completo_cliente`, `e`.`nombre_empresa` AS `nombre_empresa`, `e`.`nombre_comercial_empresa` AS `nombre_comercial_empresa`, `e`.`nif_empresa` AS `nif_empresa`, `e`.`ficticia_empresa` AS `ficticia_empresa`, `dorg`.`numero_documento_ppto` AS `numero_documento_origen`, `dorg`.`tipo_documento_ppto` AS `tipo_documento_origen`, `dorg`.`total_documento_ppto` AS `total_documento_origen`, `dorg`.`fecha_emision_documento_ppto` AS `fecha_emision_origen` FROM (((((`documento_presupuesto` `dp` join `presupuesto` `p` on((`dp`.`id_presupuesto` = `p`.`id_presupuesto`))) join `cliente` `c` on((`p`.`id_cliente` = `c`.`id_cliente`))) join `empresa` `e` on((`dp`.`id_empresa` = `e`.`id_empresa`))) left join `v_presupuesto_totales` `vt` on(((`dp`.`id_presupuesto` = `vt`.`id_presupuesto`) and (`vt`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) left join `documento_presupuesto` `dorg` on((`dp`.`id_documento_origen` = `dorg`.`id_documento_ppto`))) WHERE (`dp`.`activo_documento_ppto` = 1) ORDER BY `dp`.`fecha_emision_documento_ppto` DESC, `dp`.`id_documento_ppto` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_linea_presupuesto_calculada`
--
DROP TABLE IF EXISTS `v_linea_presupuesto_calculada`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_linea_presupuesto_calculada`  AS SELECT `lp`.`id_linea_ppto` AS `id_linea_ppto`, `lp`.`id_version_presupuesto` AS `id_version_presupuesto`, `lp`.`id_articulo` AS `id_articulo`, `lp`.`id_linea_padre` AS `id_linea_padre`, `lp`.`id_ubicacion` AS `id_ubicacion`, `lp`.`numero_linea_ppto` AS `numero_linea_ppto`, `lp`.`tipo_linea_ppto` AS `tipo_linea_ppto`, `lp`.`nivel_jerarquia` AS `nivel_jerarquia`, `lp`.`codigo_linea_ppto` AS `codigo_linea_ppto`, `lp`.`descripcion_linea_ppto` AS `descripcion_linea_ppto`, `lp`.`orden_linea_ppto` AS `orden_linea_ppto`, `lp`.`observaciones_linea_ppto` AS `observaciones_linea_ppto`, `lp`.`mostrar_obs_articulo_linea_ppto` AS `mostrar_obs_articulo_linea_ppto`, `lp`.`ocultar_detalle_kit_linea_ppto` AS `ocultar_detalle_kit_linea_ppto`, `lp`.`mostrar_en_presupuesto` AS `mostrar_en_presupuesto`, `lp`.`es_opcional` AS `es_opcional`, `lp`.`activo_linea_ppto` AS `activo_linea_ppto`, `lp`.`fecha_montaje_linea_ppto` AS `fecha_montaje_linea_ppto`, `lp`.`fecha_desmontaje_linea_ppto` AS `fecha_desmontaje_linea_ppto`, `lp`.`fecha_inicio_linea_ppto` AS `fecha_inicio_linea_ppto`, `lp`.`fecha_fin_linea_ppto` AS `fecha_fin_linea_ppto`, `lp`.`cantidad_linea_ppto` AS `cantidad_linea_ppto`, `lp`.`precio_unitario_linea_ppto` AS `precio_unitario_linea_ppto`, `lp`.`descuento_linea_ppto` AS `descuento_linea_ppto`, `lp`.`porcentaje_iva_linea_ppto` AS `porcentaje_iva_linea_ppto`, `lp`.`jornadas_linea_ppto` AS `jornadas_linea_ppto`, `lp`.`id_coeficiente` AS `id_coeficiente`, `lp`.`aplicar_coeficiente_linea_ppto` AS `aplicar_coeficiente_linea_ppto`, `lp`.`valor_coeficiente_linea_ppto` AS `valor_coeficiente_linea_ppto`, `c`.`jornadas_coeficiente` AS `jornadas_coeficiente`, `c`.`valor_coeficiente` AS `valor_coeficiente`, `c`.`observaciones_coeficiente` AS `observaciones_coeficiente`, `c`.`activo_coeficiente` AS `activo_coeficiente`, (case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) AS `dias_linea`, ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) AS `subtotal_sin_coeficiente`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * `lp`.`valor_coeficiente_linea_ppto`) else ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) end) AS `base_imponible`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (`lp`.`porcentaje_iva_linea_ppto` / 100)) else (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * (`lp`.`porcentaje_iva_linea_ppto` / 100)) end) AS `importe_iva`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) else (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) end) AS `total_linea`, (case when (`a`.`permitir_descuentos_articulo` = 1) then (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) else `lp`.`precio_unitario_linea_ppto` end) AS `precio_unitario_linea_ppto_hotel`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then (((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) else ((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then (((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) else (((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) end) end) AS `base_imponible_hotel`, (((case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then (((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) else ((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then (((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) else (((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) end) end) * if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0)) / 100) AS `importe_descuento_linea_ppto_hotel`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then ((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) else (((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) else ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) end) end) AS `TotalImporte_descuento_linea_ppto_hotel`, (((case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then ((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) else (((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) else ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) end) end) * `lp`.`porcentaje_iva_linea_ppto`) / 100) AS `importe_iva_linea_ppto_hotel`, (case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (case when (`a`.`permitir_descuentos_articulo` = 1) then (((((`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100)) * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) else ((((`lp`.`precio_unitario_linea_ppto` * `lp`.`valor_coeficiente_linea_ppto`) * `lp`.`cantidad_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) end) else (case when (`a`.`permitir_descuentos_articulo` = 1) then (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * (`lp`.`precio_unitario_linea_ppto` - ((`lp`.`precio_unitario_linea_ppto` * `p`.`porcentaje_descuento_cliente`) / 100))) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) else (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (if((coalesce(`e`.`permitir_descuentos_lineas_empresa`,1) = 1),`lp`.`descuento_linea_ppto`,0) / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) end) end) AS `TotalImporte_iva_linea_ppto_hotel`, `a`.`codigo_articulo` AS `codigo_articulo`, `a`.`nombre_articulo` AS `nombre_articulo`, `a`.`name_articulo` AS `name_articulo`, `a`.`imagen_articulo` AS `imagen_articulo`, `a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`, `a`.`coeficiente_articulo` AS `coeficiente_articulo`, `a`.`es_kit_articulo` AS `es_kit_articulo`, `a`.`control_total_articulo` AS `control_total_articulo`, `a`.`no_facturar_articulo` AS `no_facturar_articulo`, `a`.`notas_presupuesto_articulo` AS `notas_presupuesto_articulo`, `a`.`notes_budget_articulo` AS `notes_budget_articulo`, `a`.`orden_obs_articulo` AS `orden_obs_articulo`, `a`.`observaciones_articulo` AS `observaciones_articulo`, `a`.`activo_articulo` AS `activo_articulo`, `a`.`permitir_descuentos_articulo` AS `permitir_descuentos_articulo`, `a`.`precio_editable_articulo` AS `precio_editable_articulo`, `a`.`id_familia` AS `id_familia`, `a`.`created_at_articulo` AS `created_at_articulo`, `a`.`updated_at_articulo` AS `updated_at_articulo`, `a`.`id_impuesto` AS `id_impuesto_articulo`, `a`.`tipo_impuesto` AS `tipo_impuesto_articulo`, `a`.`tasa_impuesto` AS `tasa_impuesto_articulo`, `a`.`descr_impuesto` AS `descr_impuesto_articulo`, `a`.`activo_impuesto_relacionado` AS `activo_impuesto_articulo`, `a`.`id_unidad` AS `id_unidad`, `a`.`nombre_unidad` AS `nombre_unidad`, `a`.`name_unidad` AS `name_unidad`, `a`.`descr_unidad` AS `descr_unidad`, `a`.`simbolo_unidad` AS `simbolo_unidad`, `a`.`activo_unidad_relacionada` AS `activo_unidad`, `a`.`id_grupo` AS `id_grupo`, `a`.`codigo_familia` AS `codigo_familia`, `a`.`nombre_familia` AS `nombre_familia`, `a`.`name_familia` AS `name_familia`, `a`.`descr_familia` AS `descr_familia`, `a`.`imagen_familia` AS `imagen_familia`, `a`.`coeficiente_familia` AS `coeficiente_familia`, `a`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`, `a`.`observations_budget_familia` AS `observations_budget_familia`, `a`.`orden_obs_familia` AS `orden_obs_familia`, `a`.`permite_descuento_familia` AS `permite_descuento_familia`, `a`.`activo_familia_relacionada` AS `activo_familia_relacionada`, `lp`.`id_impuesto` AS `id_impuesto`, `imp`.`tipo_impuesto` AS `tipo_impuesto`, `imp`.`tasa_impuesto` AS `tasa_impuesto`, `imp`.`descr_impuesto` AS `descr_impuesto`, `imp`.`activo_impuesto` AS `activo_impuesto`, `pv`.`id_presupuesto` AS `id_presupuesto`, `pv`.`numero_version_presupuesto` AS `numero_version_presupuesto`, `pv`.`estado_version_presupuesto` AS `estado_version_presupuesto`, `pv`.`fecha_creacion_version` AS `fecha_creacion_version`, `pv`.`fecha_envio_version` AS `fecha_envio_version`, `pv`.`fecha_aprobacion_version` AS `fecha_aprobacion_version`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`fecha_presupuesto` AS `fecha_presupuesto`, `p`.`fecha_validez_presupuesto` AS `fecha_validez_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `p`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`, `p`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`, `p`.`id_cliente` AS `id_cliente`, `p`.`id_estado_ppto` AS `id_estado_ppto`, `p`.`activo_presupuesto` AS `activo_presupuesto`, `p`.`nombre_cliente` AS `nombre_cliente`, `p`.`nif_cliente` AS `nif_cliente`, `p`.`email_cliente` AS `email_cliente`, `p`.`telefono_cliente` AS `telefono_cliente`, `p`.`direccion_cliente` AS `direccion_cliente`, `p`.`cp_cliente` AS `cp_cliente`, `p`.`poblacion_cliente` AS `poblacion_cliente`, `p`.`provincia_cliente` AS `provincia_cliente`, `p`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`, `p`.`duracion_evento_dias` AS `duracion_evento_dias`, `p`.`dias_hasta_inicio_evento` AS `dias_hasta_inicio_evento`, `p`.`dias_hasta_fin_evento` AS `dias_hasta_fin_evento`, `p`.`estado_evento_presupuesto` AS `estado_evento_presupuesto`, `p`.`prioridad_presupuesto` AS `prioridad_presupuesto`, `p`.`tipo_pago_presupuesto` AS `tipo_pago_presupuesto`, `p`.`descripcion_completa_forma_pago` AS `descripcion_completa_forma_pago`, `p`.`fecha_vencimiento_anticipo` AS `fecha_vencimiento_anticipo`, `p`.`fecha_vencimiento_final` AS `fecha_vencimiento_final`, `p`.`comparacion_descuento` AS `comparacion_descuento`, `p`.`estado_descuento_presupuesto` AS `estado_descuento_presupuesto`, `p`.`aplica_descuento_presupuesto` AS `aplica_descuento_presupuesto`, `p`.`diferencia_descuento` AS `diferencia_descuento`, `p`.`tiene_direccion_facturacion_diferente` AS `tiene_direccion_facturacion_diferente`, `p`.`dias_desde_emision` AS `dias_desde_emision`, `p`.`id_version_actual` AS `id_version_actual`, `p`.`numero_version_actual` AS `numero_version_actual`, `p`.`estado_version_actual` AS `estado_version_actual`, `p`.`fecha_creacion_version_actual` AS `fecha_creacion_version_actual`, `p`.`estado_general_presupuesto` AS `estado_general_presupuesto`, `p`.`mostrar_obs_familias_presupuesto` AS `mostrar_obs_familias_presupuesto`, `p`.`mostrar_obs_articulos_presupuesto` AS `mostrar_obs_articulos_presupuesto`, `cu`.`nombre_ubicacion` AS `nombre_ubicacion`, `cu`.`direccion_ubicacion` AS `direccion_ubicacion`, `cu`.`codigo_postal_ubicacion` AS `codigo_postal_ubicacion`, `cu`.`poblacion_ubicacion` AS `poblacion_ubicacion`, `cu`.`provincia_ubicacion` AS `provincia_ubicacion`, `cu`.`pais_ubicacion` AS `pais_ubicacion`, `cu`.`persona_contacto_ubicacion` AS `persona_contacto_ubicacion`, `cu`.`telefono_contacto_ubicacion` AS `telefono_contacto_ubicacion`, `cu`.`email_contacto_ubicacion` AS `email_contacto_ubicacion`, `cu`.`observaciones_ubicacion` AS `observaciones_ubicacion`, `cu`.`es_principal_ubicacion` AS `es_principal_ubicacion`, `cu`.`activo_ubicacion` AS `activo_ubicacion`, coalesce(`cu`.`nombre_ubicacion`,`p`.`nombre_evento_presupuesto`,'Sin ubicación') AS `ubicacion_agrupacion`, coalesce(concat_ws(', ',`cu`.`nombre_ubicacion`,`cu`.`direccion_ubicacion`,concat(`cu`.`codigo_postal_ubicacion`,' ',`cu`.`poblacion_ubicacion`),`cu`.`provincia_ubicacion`),`p`.`direccion_completa_evento_presupuesto`,'Sin ubicación') AS `ubicacion_completa_agrupacion`, `lp`.`created_at_linea_ppto` AS `created_at_linea_ppto`, `lp`.`updated_at_linea_ppto` AS `updated_at_linea_ppto` FROM (((((((`linea_presupuesto` `lp` join `presupuesto_version` `pv` on((`lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`))) join `vista_presupuesto_completa` `p` on((`pv`.`id_presupuesto` = `p`.`id_presupuesto`))) left join `empresa` `e` on(((`e`.`empresa_ficticia_principal` = 1) and (`e`.`activo_empresa` = 1)))) left join `vista_articulo_completa` `a` on((`lp`.`id_articulo` = `a`.`id_articulo`))) left join `coeficiente` `c` on((`lp`.`id_coeficiente` = `c`.`id_coeficiente`))) left join `impuesto` `imp` on((`lp`.`id_impuesto` = `imp`.`id_impuesto`))) left join `cliente_ubicacion` `cu` on((`lp`.`id_ubicacion` = `cu`.`id_ubicacion`))) WHERE (`p`.`activo_presupuesto` = true) ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_observaciones_presupuesto`
--
DROP TABLE IF EXISTS `v_observaciones_presupuesto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_observaciones_presupuesto`  AS SELECT `p`.`id_presupuesto` AS `id_presupuesto`, `f`.`id_familia` AS `id_familia`, NULL AS `id_articulo`, 'familia' AS `tipo_observacion`, `f`.`codigo_familia` AS `codigo_familia`, `f`.`nombre_familia` AS `nombre_familia`, `f`.`name_familia` AS `name_familia`, `f`.`observaciones_presupuesto_familia` AS `observacion_es`, `f`.`observations_budget_familia` AS `observacion_en`, `f`.`orden_obs_familia` AS `orden_observacion`, `p`.`mostrar_obs_familias_presupuesto` AS `mostrar_observacion`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `p`.`id_cliente` AS `id_cliente`, `vp`.`nombre_cliente` AS `nombre_cliente`, `f`.`activo_familia` AS `activo_origen`, `p`.`activo_presupuesto` AS `activo_presupuesto` FROM (((((`presupuesto` `p` join `vista_presupuesto_completa` `vp` on((`p`.`id_presupuesto` = `vp`.`id_presupuesto`))) join `presupuesto_version` `pv` on(((`p`.`id_presupuesto` = `pv`.`id_presupuesto`) and (`pv`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) join `linea_presupuesto` `lp` on(((`pv`.`id_version_presupuesto` = `lp`.`id_version_presupuesto`) and (`lp`.`activo_linea_ppto` = 1)))) join `articulo` `a` on(((`lp`.`id_articulo` = `a`.`id_articulo`) and (`a`.`activo_articulo` = 1)))) join `familia` `f` on(((`a`.`id_familia` = `f`.`id_familia`) and (`f`.`activo_familia` = 1)))) WHERE ((`p`.`activo_presupuesto` = 1) AND (`p`.`mostrar_obs_familias_presupuesto` = 1) AND ((`f`.`observaciones_presupuesto_familia` is not null) OR (`f`.`observations_budget_familia` is not null))) GROUP BY `p`.`id_presupuesto`, `f`.`id_familia`, `p`.`numero_presupuesto`, `p`.`nombre_evento_presupuesto`, `p`.`id_cliente`, `vp`.`nombre_cliente`, `p`.`mostrar_obs_familias_presupuesto`, `p`.`activo_presupuesto`, `f`.`codigo_familia`, `f`.`nombre_familia`, `f`.`name_familia`, `f`.`observaciones_presupuesto_familia`, `f`.`observations_budget_familia`, `f`.`orden_obs_familia`, `f`.`activo_familia`union all select `p`.`id_presupuesto` AS `id_presupuesto`,NULL AS `id_familia`,`a`.`id_articulo` AS `id_articulo`,'articulo' AS `tipo_observacion`,`a`.`codigo_articulo` AS `codigo_articulo`,`a`.`nombre_articulo` AS `nombre_articulo`,`a`.`name_articulo` AS `name_articulo`,`a`.`notas_presupuesto_articulo` AS `observacion_es`,`a`.`notes_budget_articulo` AS `observacion_en`,`a`.`orden_obs_articulo` AS `orden_observacion`,`p`.`mostrar_obs_articulos_presupuesto` AS `mostrar_observacion`,`p`.`numero_presupuesto` AS `numero_presupuesto`,`p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`,`p`.`id_cliente` AS `id_cliente`,`vp`.`nombre_cliente` AS `nombre_cliente`,`a`.`activo_articulo` AS `activo_origen`,`p`.`activo_presupuesto` AS `activo_presupuesto` from ((((`presupuesto` `p` join `vista_presupuesto_completa` `vp` on((`p`.`id_presupuesto` = `vp`.`id_presupuesto`))) join `presupuesto_version` `pv` on(((`p`.`id_presupuesto` = `pv`.`id_presupuesto`) and (`pv`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) join `linea_presupuesto` `lp` on(((`pv`.`id_version_presupuesto` = `lp`.`id_version_presupuesto`) and (`lp`.`activo_linea_ppto` = 1)))) join `articulo` `a` on(((`lp`.`id_articulo` = `a`.`id_articulo`) and (`a`.`activo_articulo` = 1)))) where ((`p`.`activo_presupuesto` = 1) and (`p`.`mostrar_obs_articulos_presupuesto` = 1) and ((`a`.`notas_presupuesto_articulo` is not null) or (`a`.`notes_budget_articulo` is not null)) and (`lp`.`mostrar_obs_articulo_linea_ppto` = 1)) group by `p`.`id_presupuesto`,`a`.`id_articulo`,`p`.`numero_presupuesto`,`p`.`nombre_evento_presupuesto`,`p`.`id_cliente`,`vp`.`nombre_cliente`,`p`.`mostrar_obs_articulos_presupuesto`,`p`.`activo_presupuesto`,`a`.`codigo_articulo`,`a`.`nombre_articulo`,`a`.`name_articulo`,`a`.`notas_presupuesto_articulo`,`a`.`notes_budget_articulo`,`a`.`orden_obs_articulo`,`a`.`activo_articulo` order by `id_presupuesto`,`orden_observacion`,`tipo_observacion`  ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_pagos_presupuesto`
--
DROP TABLE IF EXISTS `v_pagos_presupuesto`;

CREATE ALGORITHM=UNDEFINED DEFINER=`administrator`@`%` SQL SECURITY DEFINER VIEW `v_pagos_presupuesto`  AS SELECT `pp`.`id_pago_ppto` AS `id_pago_ppto`, `pp`.`id_presupuesto` AS `id_presupuesto`, `pp`.`id_documento_ppto` AS `id_documento_ppto`, `pp`.`tipo_pago_ppto` AS `tipo_pago_ppto`, `pp`.`importe_pago_ppto` AS `importe_pago_ppto`, `pp`.`porcentaje_pago_ppto` AS `porcentaje_pago_ppto`, `pp`.`id_metodo_pago` AS `id_metodo_pago`, `pp`.`referencia_pago_ppto` AS `referencia_pago_ppto`, `pp`.`fecha_pago_ppto` AS `fecha_pago_ppto`, `pp`.`fecha_valor_pago_ppto` AS `fecha_valor_pago_ppto`, `pp`.`estado_pago_ppto` AS `estado_pago_ppto`, `pp`.`observaciones_pago_ppto` AS `observaciones_pago_ppto`, `pp`.`activo_pago_ppto` AS `activo_pago_ppto`, `pp`.`created_at_pago_ppto` AS `created_at_pago_ppto`, `pp`.`updated_at_pago_ppto` AS `updated_at_pago_ppto`, `p`.`numero_presupuesto` AS `numero_presupuesto`, `p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`, `p`.`fecha_presupuesto` AS `fecha_presupuesto`, `vt`.`total_con_iva` AS `total_presupuesto`, `c`.`id_cliente` AS `id_cliente`, `c`.`nombre_cliente` AS `nombre_cliente`, `c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`, ifnull(`c`.`nombre_facturacion_cliente`,`c`.`nombre_cliente`) AS `nombre_completo_cliente`, `dp`.`tipo_documento_ppto` AS `tipo_documento_vinculado`, `dp`.`numero_documento_ppto` AS `numero_documento_vinculado`, `dp`.`subtotal_documento_ppto` AS `subtotal_documento_vinculado`, `dp`.`total_iva_documento_ppto` AS `iva_cuota_documento_vinculado`, `dp`.`total_documento_ppto` AS `total_documento_vinculado`, `dp`.`ruta_pdf_documento_ppto` AS `ruta_pdf_vinculado`, `mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`, `mp`.`nombre_metodo_pago` AS `nombre_metodo_pago` FROM (((((`pago_presupuesto` `pp` join `presupuesto` `p` on((`pp`.`id_presupuesto` = `p`.`id_presupuesto`))) join `cliente` `c` on((`p`.`id_cliente` = `c`.`id_cliente`))) left join `v_presupuesto_totales` `vt` on(((`pp`.`id_presupuesto` = `vt`.`id_presupuesto`) and (`vt`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) left join `documento_presupuesto` `dp` on((`pp`.`id_documento_ppto` = `dp`.`id_documento_ppto`))) left join `metodo_pago` `mp` on((`pp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) WHERE (`pp`.`activo_pago_ppto` = 1) ORDER BY `pp`.`fecha_pago_ppto` DESC, `pp`.`id_pago_ppto` DESC ;

-- --------------------------------------------------------

--
-- Estructura para la vista `v_presupuesto_totales`
--
DROP TABLE IF EXISTS `v_presupuesto_totales`;

CREATE ALGORITHM=UNDEFINED DEFINER=`root`@`%` SQL SECURITY DEFINER VIEW `v_presupuesto_totales`  AS SELECT `vlpc`.`id_version_presupuesto` AS `id_version_presupuesto`, min(`vlpc`.`numero_version_presupuesto`) AS `numero_version_presupuesto`, min(`vlpc`.`estado_version_presupuesto`) AS `estado_version_presupuesto`, min(`vlpc`.`fecha_creacion_version`) AS `fecha_creacion_version`, min(`vlpc`.`fecha_envio_version`) AS `fecha_envio_version`, min(`vlpc`.`fecha_aprobacion_version`) AS `fecha_aprobacion_version`, min(`vlpc`.`id_presupuesto`) AS `id_presupuesto`, min(`vlpc`.`numero_presupuesto`) AS `numero_presupuesto`, min(`vlpc`.`fecha_presupuesto`) AS `fecha_presupuesto`, min(`vlpc`.`fecha_validez_presupuesto`) AS `fecha_validez_presupuesto`, min(`vlpc`.`nombre_evento_presupuesto`) AS `nombre_evento_presupuesto`, min(`vlpc`.`fecha_inicio_evento_presupuesto`) AS `fecha_inicio_evento_presupuesto`, min(`vlpc`.`fecha_fin_evento_presupuesto`) AS `fecha_fin_evento_presupuesto`, min(`vlpc`.`id_cliente`) AS `id_cliente`, min(`vlpc`.`nombre_cliente`) AS `nombre_cliente`, min(`vlpc`.`nif_cliente`) AS `nif_cliente`, min(`vlpc`.`email_cliente`) AS `email_cliente`, min(`vlpc`.`telefono_cliente`) AS `telefono_cliente`, max(`vlpc`.`duracion_evento_dias`) AS `duracion_evento_dias`, sum(`vlpc`.`base_imponible`) AS `total_base_imponible`, sum(`vlpc`.`importe_iva`) AS `total_iva`, sum(`vlpc`.`total_linea`) AS `total_con_iva`, sum(`vlpc`.`base_imponible_hotel`) AS `total_base_imponible_hotel`, sum(`vlpc`.`importe_descuento_linea_ppto_hotel`) AS `total_importe_descuento_linea_hotel`, sum(`vlpc`.`TotalImporte_descuento_linea_ppto_hotel`) AS `total_despues_descuento_linea_hotel`, sum(`vlpc`.`importe_iva_linea_ppto_hotel`) AS `total_iva_hotel`, sum(`vlpc`.`TotalImporte_iva_linea_ppto_hotel`) AS `total_con_iva_hotel`, count(0) AS `cantidad_lineas_total`, count((case when (`vlpc`.`valor_coeficiente_linea_ppto` is not null) then 1 end)) AS `cantidad_lineas_con_coeficiente`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_21`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_21`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_21`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_10`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_10`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_10`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_4`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_4`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_4`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_0`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_0`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_0`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_otros`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_otros`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_otros`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_21_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_21_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_21_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_10_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_10_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_10_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_4_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_4_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_4_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_0_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_0_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_0_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`base_imponible_hotel` else 0 end)) AS `base_iva_otros_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`importe_iva_linea_ppto_hotel` else 0 end)) AS `importe_iva_otros_hotel`, sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`TotalImporte_iva_linea_ppto_hotel` else 0 end)) AS `total_iva_otros_hotel`, sum((`vlpc`.`subtotal_sin_coeficiente` - `vlpc`.`base_imponible`)) AS `ahorro_total_coeficientes`, (sum(`vlpc`.`base_imponible`) - sum(`vlpc`.`base_imponible_hotel`)) AS `diferencia_base_imponible_cliente`, (sum(`vlpc`.`total_linea`) - sum(`vlpc`.`TotalImporte_iva_linea_ppto_hotel`)) AS `diferencia_total_con_iva_cliente`, min(`vlpc`.`created_at_linea_ppto`) AS `fecha_primera_linea_creada`, max(`vlpc`.`updated_at_linea_ppto`) AS `fecha_ultima_modificacion_linea` FROM `v_linea_presupuesto_calculada` AS `vlpc` GROUP BY `vlpc`.`id_version_presupuesto` ;

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
  ADD CONSTRAINT `fk_articulo_impuesto` FOREIGN KEY (`id_impuesto`) REFERENCES `impuesto` (`id_impuesto`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_articulo_unidad` FOREIGN KEY (`id_unidad`) REFERENCES `unidad_medida` (`id_unidad`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente`
--
ALTER TABLE `cliente`
  ADD CONSTRAINT `fk_cliente_forma_pago_habitual` FOREIGN KEY (`id_forma_pago_habitual`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `cliente_ubicacion`
--
ALTER TABLE `cliente_ubicacion`
  ADD CONSTRAINT `fk_ubicacion_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE;

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
-- Filtros para la tabla `documento`
--
ALTER TABLE `documento`
  ADD CONSTRAINT `documento_ibfk_1` FOREIGN KEY (`id_tipo_documento_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `documento_elemento`
--
ALTER TABLE `documento_elemento`
  ADD CONSTRAINT `fk_documento_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `documento_presupuesto`
--
ALTER TABLE `documento_presupuesto`
  ADD CONSTRAINT `fk_doc_ppto_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doc_ppto_origen` FOREIGN KEY (`id_documento_origen`) REFERENCES `documento_presupuesto` (`id_documento_ppto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doc_ppto_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_doc_ppto_version` FOREIGN KEY (`id_version_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `elemento`
--
ALTER TABLE `elemento`
  ADD CONSTRAINT `fk_elemento_articulo` FOREIGN KEY (`id_articulo_elemento`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_estado` FOREIGN KEY (`id_estado_elemento`) REFERENCES `estado_elemento` (`id_estado_elemento`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_forma_pago_alquiler` FOREIGN KEY (`id_forma_pago_alquiler_elemento`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_marca` FOREIGN KEY (`id_marca_elemento`) REFERENCES `marca` (`id_marca`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_proveedor_alquiler` FOREIGN KEY (`id_proveedor_alquiler_elemento`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_elemento_proveedor_compra` FOREIGN KEY (`id_proveedor_compra_elemento`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `empresa`
--
ALTER TABLE `empresa`
  ADD CONSTRAINT `fk_empresa_plantilla` FOREIGN KEY (`id_plantilla_default`) REFERENCES `plantilla_impresion` (`id_plantilla`) ON DELETE SET NULL ON UPDATE CASCADE;

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
-- Filtros para la tabla `furgoneta_mantenimiento`
--
ALTER TABLE `furgoneta_mantenimiento`
  ADD CONSTRAINT `fk_mantenimiento_furgoneta` FOREIGN KEY (`id_furgoneta`) REFERENCES `furgoneta` (`id_furgoneta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `furgoneta_registro_kilometraje`
--
ALTER TABLE `furgoneta_registro_kilometraje`
  ADD CONSTRAINT `fk_registro_km_furgoneta` FOREIGN KEY (`id_furgoneta`) REFERENCES `furgoneta` (`id_furgoneta`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `kit`
--
ALTER TABLE `kit`
  ADD CONSTRAINT `fk_kit_articulo_componente` FOREIGN KEY (`id_articulo_componente`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_kit_articulo_maestro` FOREIGN KEY (`id_articulo_maestro`) REFERENCES `articulo` (`id_articulo`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `linea_presupuesto`
--
ALTER TABLE `linea_presupuesto`
  ADD CONSTRAINT `fk_linea_ppto_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_coeficiente` FOREIGN KEY (`id_coeficiente`) REFERENCES `coeficiente` (`id_coeficiente`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_impuesto` FOREIGN KEY (`id_impuesto`) REFERENCES `impuesto` (`id_impuesto`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_linea_padre` FOREIGN KEY (`id_linea_padre`) REFERENCES `linea_presupuesto` (`id_linea_ppto`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_ubicacion` FOREIGN KEY (`id_ubicacion`) REFERENCES `cliente_ubicacion` (`id_ubicacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_ppto_version` FOREIGN KEY (`id_version_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `linea_salida_almacen`
--
ALTER TABLE `linea_salida_almacen`
  ADD CONSTRAINT `fk_linea_salida_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_salida_cabecera` FOREIGN KEY (`id_salida_almacen`) REFERENCES `salida_almacen` (`id_salida_almacen`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_salida_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_linea_salida_linea_ppto` FOREIGN KEY (`id_linea_ppto`) REFERENCES `linea_presupuesto` (`id_linea_ppto`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `movimiento_elemento_salida`
--
ALTER TABLE `movimiento_elemento_salida`
  ADD CONSTRAINT `fk_mov_linea_salida` FOREIGN KEY (`id_linea_salida`) REFERENCES `linea_salida_almacen` (`id_linea_salida`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mov_ubicacion_destino` FOREIGN KEY (`id_ubicacion_destino`) REFERENCES `cliente_ubicacion` (`id_ubicacion`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_mov_ubicacion_origen` FOREIGN KEY (`id_ubicacion_origen`) REFERENCES `cliente_ubicacion` (`id_ubicacion`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `pago_presupuesto`
--
ALTER TABLE `pago_presupuesto`
  ADD CONSTRAINT `fk_pago_ppto_documento` FOREIGN KEY (`id_documento_ppto`) REFERENCES `documento_presupuesto` (`id_documento_ppto`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pago_ppto_empresa` FOREIGN KEY (`id_empresa_pago`) REFERENCES `empresa` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pago_ppto_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metodo_pago`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_pago_ppto_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE;

--
-- Filtros para la tabla `presupuesto`
--
ALTER TABLE `presupuesto`
  ADD CONSTRAINT `fk_presupuesto_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_contacto` FOREIGN KEY (`id_contacto_cliente`) REFERENCES `contacto_cliente` (`id_contacto_cliente`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_estado` FOREIGN KEY (`id_estado_ppto`) REFERENCES `estado_presupuesto` (`id_estado_ppto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_forma_pago` FOREIGN KEY (`id_forma_pago`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_presupuesto_metodo_contacto` FOREIGN KEY (`id_metodo`) REFERENCES `metodos_contacto` (`id_metodo`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `presupuesto_version`
--
ALTER TABLE `presupuesto_version`
  ADD CONSTRAINT `fk_version_padre` FOREIGN KEY (`version_padre_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_version_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `proveedor`
--
ALTER TABLE `proveedor`
  ADD CONSTRAINT `fk_proveedor_forma_pago_habitual` FOREIGN KEY (`id_forma_pago_habitual`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Filtros para la tabla `salida_almacen`
--
ALTER TABLE `salida_almacen`
  ADD CONSTRAINT `fk_salida_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_salida_version` FOREIGN KEY (`id_version_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE;

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
