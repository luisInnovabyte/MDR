-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 217.154.117.83    Database: toldos_db
-- ------------------------------------------------------
-- Server version	9.3.0

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `adjunto_llamada`
--

DROP TABLE IF EXISTS `adjunto_llamada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `adjunto_llamada` (
  `id_adjunto` int NOT NULL AUTO_INCREMENT,
  `id_llamada` int NOT NULL,
  `nombre_archivo` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `tipo` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint DEFAULT '1' COMMENT '0=Inactivo, 1=Activo',
  PRIMARY KEY (`id_adjunto`),
  KEY `fk_id_llamada` (`id_llamada`),
  CONSTRAINT `fk_id_llamada` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`)
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adjunto_llamada`
--

LOCK TABLES `adjunto_llamada` WRITE;
/*!40000 ALTER TABLE `adjunto_llamada` DISABLE KEYS */;
INSERT INTO `adjunto_llamada` VALUES (57,16,'TOLDOS-A-MEDIDA-EN-VALENCIA.jpg','image/jpeg','2025-07-14 09:26:29',1),(58,10,'media.jpg','image/jpeg','2025-07-14 09:50:39',1),(61,1,'slidebg01F.jpg','image/jpeg','2025-07-14 12:47:31',1),(62,4,'toldos-con-cofre-splenbox-400.jpg','image/jpeg','2025-07-14 13:07:00',1),(63,20,'toldo-plano-07.jpg','image/jpeg','2025-07-14 13:11:38',1),(64,21,'zagle-oddychajace-trojkat-niebieski5b55cf931e846_725x725.jpg','image/jpeg','2025-07-14 13:16:58',1),(65,22,'toldos_1.jpg','image/jpeg','2025-07-14 13:21:25',1),(66,23,'Toldos_Veranda-big.jpg','image/jpeg','2025-07-14 13:28:08',1),(67,19,'toldos-1.jpg','image/jpeg','2025-07-14 13:41:09',1),(68,24,'toldo-balcon.jpg','image/jpeg','2025-07-14 13:41:17',1),(69,6,'toldos-con-cofre-splenbox-400_1.jpg','image/jpeg','2025-07-14 13:43:28',1),(77,4,'toldo-balcon_1.jpg','image/jpeg','2025-07-15 09:47:52',1),(78,4,'Toldos_Veranda-big_2.jpg','image/jpeg','2025-07-15 09:47:52',1),(79,4,'toldos_1_1.jpg','image/jpeg','2025-07-15 09:47:52',1),(85,1,'Imagen de WhatsApp 2024-12-12 a las 20.28.37_e103bb03.jpg','image/jpeg','2025-10-13 12:03:40',1);
/*!40000 ALTER TABLE `adjunto_llamada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `articulo`
--

DROP TABLE IF EXISTS `articulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `articulo` (
  `id_articulo` int unsigned NOT NULL AUTO_INCREMENT,
  `id_familia` int unsigned NOT NULL,
  `id_unidad` int unsigned DEFAULT NULL,
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
  `id_impuesto` int DEFAULT NULL COMMENT 'Impuesto aplicable al artículo',
  `created_at_articulo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_articulo` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_articulo`),
  UNIQUE KEY `codigo_articulo` (`codigo_articulo`),
  KEY `fk_articulo_unidad` (`id_unidad`),
  KEY `idx_codigo_articulo` (`codigo_articulo`),
  KEY `idx_id_familia_articulo` (`id_familia`),
  KEY `idx_nombre_articulo` (`nombre_articulo`),
  KEY `idx_es_kit_articulo` (`es_kit_articulo`),
  KEY `idx_id_impuesto` (`id_impuesto`),
  CONSTRAINT `fk_articulo_familia` FOREIGN KEY (`id_familia`) REFERENCES `familia` (`id_familia`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_articulo_impuesto` FOREIGN KEY (`id_impuesto`) REFERENCES `impuesto` (`id_impuesto`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_articulo_unidad` FOREIGN KEY (`id_unidad`) REFERENCES `unidad_medida` (`id_unidad`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `articulo`
--

LOCK TABLES `articulo` WRITE;
/*!40000 ALTER TABLE `articulo` DISABLE KEYS */;
INSERT INTO `articulo` VALUES (21,19,5,'MIC-INAL-001','Micrófono inalámbrico','Wireless Microphone','articulo_6924135645edd.png',25.00,NULL,1,1,0,'Incluye petaca transmisora, micrófono de mano y receptor. Requiere 2 pilas AA (no incluidas). Alcance hasta 100 metros en línea directa.','Includes bodypack transmitter, handheld microphone and receiver. Requires 2 AA batteries (not included). Range up to 100 meters in direct line.',200,'Verificar estado de baterías antes de cada alquiler. Comprobar frecuencias disponibles.',1,1,NULL,'2025-11-20 20:33:47','2026-01-20 17:34:26'),(22,20,5,'KIT-ILU-BASIC','Kit iluminación básica (4 PAR LED + trípodes)','Basic lighting kit (4 LED PAR + tripods)','articulos/kit_iluminacion_basica.jpg',120.00,1,1,0,0,'Kit completo listo para usar. Incluye 4 focos PAR LED RGBW de 54W, 4 trípodes telescópicos hasta 3m, cables DMX, controlador DMX y bolsa de transporte. Consumo total: 220W.','Complete plug-and-play kit. Includes 4x 54W RGBW LED PAR fixtures, 4x telescopic tripods up to 3m, DMX cables, DMX controller and transport bag. Total consumption: 220W.',100,'Revisar estado de LEDs y conexiones DMX antes de entregar.',1,1,NULL,'2025-11-20 20:33:47','2026-01-20 17:33:09'),(23,22,NULL,'MIX-DIG-X32','Consola digital Behringer X32','Behringer X32 Digital Console','articulos/consola_x32.jpg',180.00,NULL,0,1,0,'Consola digital de 32 canales con 16 buses auxiliares, 8 efectos integrados y grabación multipista USB. Incluye flight case y cable de alimentación. Requiere corriente trifásica.','32-channel digital console with 16 aux buses, 8 integrated effects and USB multitrack recording. Includes flight case and power cable. Requires three-phase power.',200,'Verificar configuración de escenas. Resetear a valores de fábrica después de cada uso.',1,1,NULL,'2025-11-20 20:33:47','2026-01-20 17:35:38'),(24,21,5,'CABLE-XLR-10M','Cable XLR 10 metros','10m XLR Cable','articulos/cable_xlr.jpg',3.80,0,0,0,0,'Cable balanceado XLR macho-hembra de 10 metros. Conductor OFC de baja impedancia.','10m balanced XLR male-female cable. Low impedance OFC conductor.',300,'Verificar conectores y continuidad antes de alquilar.',1,1,NULL,'2025-11-20 20:33:47','2026-01-20 17:33:20'),(25,22,5,'LED-PANEL-P3','Pantalla LED modular P3 interior (por m²)','P3 Indoor LED Panel (per sqm)','articulo_6924b18b230b6.png',450.00,0,0,1,0,'Pantalla LED modular de pixel pitch 3mm para interior. Resolución 111.111 píxeles/m². Brillo 1200 nits. Incluye estructura de soporte, procesador de video y cableado. Requiere técnico especializado para montaje.','P3 indoor modular LED screen. Resolution 111,111 pixels/sqm. Brightness 1200 nits. Includes support structure, video processor and cabling. Requires specialized technician for assembly.',50,'Revisar píxeles muertos. Calibrar color antes de cada evento. Requiere montaje 24h antes.',1,1,NULL,'2025-11-20 20:33:47','2025-11-24 19:27:07'),(26,19,5,'RAC','rack efectos','RAC','',150.00,NULL,1,0,0,'','',200,'',1,1,NULL,'2025-12-16 09:47:34','2026-01-03 16:32:09');
/*!40000 ALTER TABLE `articulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `fecha` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (2,'comida','2025-05-01'),(3,'f1','2025-05-29'),(4,'trabajadores','2025-05-30');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb3 */ ;
/*!50003 SET character_set_results = utf8mb3 */ ;
/*!50003 SET collation_connection  = utf8mb3_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `set_default_fecha` BEFORE INSERT ON `categorias` FOR EACH ROW BEGIN
  IF NEW.fecha IS NULL THEN
    SET NEW.fecha = CURDATE();
  END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `cliente`
--

DROP TABLE IF EXISTS `cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente` (
  `id_cliente` int unsigned NOT NULL AUTO_INCREMENT,
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
  `id_forma_pago_habitual` int unsigned DEFAULT NULL COMMENT 'Forma de pago habitual del cliente. Se usará por defecto en nuevos presupuestos',
  `porcentaje_descuento_cliente` decimal(5,2) NOT NULL DEFAULT '0.00' COMMENT 'Porcentaje de descuento habitual acordado con el cliente (0.00 a 100.00). Ejemplo: 10.00 = 10% de descuento',
  `observaciones_cliente` text,
  `activo_cliente` tinyint(1) DEFAULT '1',
  `created_at_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_cliente`),
  UNIQUE KEY `codigo_cliente` (`codigo_cliente`),
  KEY `idx_codigo_cliente` (`codigo_cliente`),
  KEY `idx_nombre_cliente` (`nombre_cliente`),
  KEY `idx_nif_cliente` (`nif_cliente`),
  KEY `idx_id_forma_pago_habitual` (`id_forma_pago_habitual`),
  KEY `idx_porcentaje_descuento_cliente` (`porcentaje_descuento_cliente`),
  CONSTRAINT `fk_cliente_forma_pago_habitual` FOREIGN KEY (`id_forma_pago_habitual`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_porcentaje_descuento_cliente` CHECK (((`porcentaje_descuento_cliente` >= 0.00) and (`porcentaje_descuento_cliente` <= 100.00)))
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente`
--

LOCK TABLES `cliente` WRITE;
/*!40000 ALTER TABLE `cliente` DISABLE KEYS */;
INSERT INTO `cliente` VALUES (1,'MELIA002','Melia Don Jaime','C/ Mayor, 24','28001','Madrid','Madrid','B214515744444','965262384','','','','','','','','',11,10.20,'',1,'2025-11-16 09:46:02','2025-12-19 10:09:38'),(2,'PROV00','Fontaneria Klek','','232244','Madrid','Madrid','1213414B','629995058','','','cliente@gmail.com','','Calle Comandante Martí 6','','','',NULL,0.00,'',0,'2025-11-18 17:20:22','2025-12-03 17:10:20'),(3,'MELIA003','Prueba de nombre','','','','','B21451574','','','','','','','','','',3,0.00,'',1,'2025-12-03 17:35:09','2025-12-03 17:35:09'),(4,'CREA001','Creativemas','C/Pintores, 2','28923','Madrid','Madrid','ES21451574','','','','','','','','','',8,20.00,'',1,'2025-12-11 18:57:58','2025-12-21 10:16:52');
/*!40000 ALTER TABLE `cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cliente_ubicacion`
--

DROP TABLE IF EXISTS `cliente_ubicacion`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cliente_ubicacion` (
  `id_ubicacion` int unsigned NOT NULL AUTO_INCREMENT,
  `id_cliente` int unsigned NOT NULL COMMENT 'Cliente propietario de esta ubicación',
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
  `updated_at_ubicacion` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_ubicacion`),
  KEY `idx_id_cliente_ubicacion` (`id_cliente`),
  KEY `idx_nombre_ubicacion` (`nombre_ubicacion`),
  KEY `idx_poblacion_ubicacion` (`poblacion_ubicacion`),
  KEY `idx_provincia_ubicacion` (`provincia_ubicacion`),
  KEY `idx_es_principal_ubicacion` (`es_principal_ubicacion`),
  KEY `idx_activo_ubicacion` (`activo_ubicacion`),
  CONSTRAINT `fk_ubicacion_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Ubicaciones habituales de eventos para cada cliente';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cliente_ubicacion`
--

LOCK TABLES `cliente_ubicacion` WRITE;
/*!40000 ALTER TABLE `cliente_ubicacion` DISABLE KEYS */;
INSERT INTO `cliente_ubicacion` VALUES (1,4,'Oficina Central','C/ rio Amadorio ,4','03013','Alicante','Alicante','España','','','','Prueba de ubicación principal',1,1,'2025-12-19 17:19:31','2025-12-19 17:21:19'),(2,4,'SalóN Alicante','C/ rio Amadorio ,4','46130','Alicante','Alicante','España','','','','',0,1,'2026-01-20 12:16:23','2026-01-20 12:16:23');
/*!40000 ALTER TABLE `cliente_ubicacion` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `coeficiente`
--

DROP TABLE IF EXISTS `coeficiente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `coeficiente` (
  `id_coeficiente` int unsigned NOT NULL AUTO_INCREMENT,
  `jornadas_coeficiente` int NOT NULL,
  `valor_coeficiente` decimal(10,2) NOT NULL,
  `observaciones_coeficiente` text,
  `activo_coeficiente` tinyint(1) DEFAULT '1',
  `created_at_coeficiente` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_coeficiente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_coeficiente`),
  UNIQUE KEY `jornadas_coeficiente` (`jornadas_coeficiente`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coeficiente`
--

LOCK TABLES `coeficiente` WRITE;
/*!40000 ALTER TABLE `coeficiente` DISABLE KEYS */;
INSERT INTO `coeficiente` VALUES (1,10,8.20,'Es una especie de descuento valorado en 3 Euros',1,'2025-11-14 13:18:26','2025-11-23 10:12:40'),(2,15,12.00,'',1,'2025-11-14 13:23:09','2025-11-14 13:23:09'),(3,20,10.00,'',1,'2025-11-15 08:40:27','2025-11-15 08:40:27'),(4,8,7.25,'Espara la prueba de lineas de presupuestos.',1,'2026-01-20 18:21:11','2026-01-20 18:21:11'),(5,9,8.75,'Prueba e observaciones',1,'2026-01-20 18:34:27','2026-01-20 18:34:27'),(6,14,13.25,'',1,'2026-01-22 18:12:02','2026-01-22 18:12:02');
/*!40000 ALTER TABLE `coeficiente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `com_vacaciones`
--

DROP TABLE IF EXISTS `com_vacaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `com_vacaciones` (
  `id_vacacion` int NOT NULL AUTO_INCREMENT,
  `id_comercial` int NOT NULL,
  `fecha_inicio` date NOT NULL,
  `fecha_fin` date NOT NULL,
  `descripcion` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_vacacion` tinyint DEFAULT '1',
  PRIMARY KEY (`id_vacacion`),
  KEY `fk_vacaciones_comercial` (`id_comercial`),
  CONSTRAINT `fk_vacaciones_comercial` FOREIGN KEY (`id_comercial`) REFERENCES `comerciales` (`id_comercial`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vacaciones`
--

LOCK TABLES `com_vacaciones` WRITE;
/*!40000 ALTER TABLE `com_vacaciones` DISABLE KEYS */;
INSERT INTO `com_vacaciones` VALUES (1,1,'2025-03-01','2025-02-14','Vacaciones por emergencia',0),(2,2,'2025-04-10','2025-04-15','Viaje familiar',0),(3,3,'2025-05-05','2025-05-10','Descanso médico',0),(6,1,'2025-02-01','2025-02-22','Embarazo',0),(9,2,'2025-04-09','2025-04-18','Vacaciones Provisionales',0),(10,2,'2025-04-02','2025-04-12','Vacaciones por emergencia',0),(11,2,'2025-04-12','2025-04-19','Vacaciones por emergencia',0),(12,2,'2025-04-09','2025-04-17','Descanso médico',0),(13,11,'2025-04-10','2025-04-18','Descanso médico',0),(14,2,'2025-04-19','2025-04-30','Viaje familiar',0),(15,11,'2025-04-09','2025-04-09','Viaje familiar',0),(16,24,'2025-04-03','2025-04-17','Vacaciones de semana santa',0),(17,16,'2025-04-10','2025-04-24','Por semana santa',0),(18,14,'2025-04-02','2025-04-19','Vacaciones de semana santa',0),(19,2,'2025-04-02','2025-04-17','Vacaciones de semana santa',0),(20,2,'2025-05-24','2025-05-30','prueba de ejercicio',0),(21,2,'2025-07-03','2025-07-17','Vacaciones de verano',0),(22,9,'2025-07-12','2025-07-30','Vacaciones de verano',0),(23,9,'2025-08-01','2025-08-09','Segundas vacaciones',0);
/*!40000 ALTER TABLE `com_vacaciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comerciales`
--

DROP TABLE IF EXISTS `comerciales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comerciales` (
  `id_comercial` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `apellidos` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `movil` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `telefono` varchar(15) COLLATE utf8mb4_general_ci NOT NULL,
  `activo` tinyint DEFAULT '1',
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_comercial`),
  KEY `fk_comerciales_usuarios` (`id_usuario`),
  CONSTRAINT `fk_comerciales_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comerciales`
--

LOCK TABLES `comerciales` WRITE;
/*!40000 ALTER TABLE `comerciales` DISABLE KEYS */;
INSERT INTO `comerciales` VALUES (1,'Alejandro','Rodríguez Martínez','698689685','698689685',1,5),(2,'Carlos','López','655656841','655656841',0,6),(3,'Marta','Rodríguez González','645567674','645567674',0,19),(4,'Luis','Fernández López','645575741','645575741',1,7),(9,'Lucía','Pérez Sánchez','698898874','698898874',1,8),(11,'Ana','Hernández Torres','635999999','635999999',0,20),(12,'Miguel','Díaz Jiménez','643455441','643455441',0,21),(13,'Raúl','Romero Álvarez','695548744','695548744',1,9),(14,'Eva','Moreno Fernández','698654645','698654645',0,22),(16,'Teresa','Vázquez Suárez','689789454','689789454',0,23),(17,'Margarita','García Castro','616515614','616515614',1,10),(18,'Carmen','Martínez González','623615641','623615641',1,11),(19,'Sergio','López Hernández','634535442','634535442',0,24),(21,'Alberto','Hernández García','644334567','644334567',1,12),(22,'Natalia','Sánchez García','621849484','621849484',1,13),(23,'Laura','Ramírez Hernández','632554779','632554779',1,14),(24,'Francisco','Moreno Moya','660300923','660300923',1,15),(27,'Beatriz','Muñoz Vázquez','477777777777','777777774',1,16),(28,'Pablo','Moreno Sánchez','698544745','698544745',1,17),(30,'María','Torres García','645644211','645644211',1,18),(31,'Marta','Rodríguez González','635224447','633221448',1,19),(32,'Miguel','Díaz Jiménez','646544684','645487775',1,21);
/*!40000 ALTER TABLE `comerciales` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `contacto_cantidad_cliente`
--

DROP TABLE IF EXISTS `contacto_cantidad_cliente`;
/*!50001 DROP VIEW IF EXISTS `contacto_cantidad_cliente`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `contacto_cantidad_cliente` AS SELECT 
 1 AS `id_cliente`,
 1 AS `codigo_cliente`,
 1 AS `nombre_cliente`,
 1 AS `direccion_cliente`,
 1 AS `cp_cliente`,
 1 AS `poblacion_cliente`,
 1 AS `provincia_cliente`,
 1 AS `nif_cliente`,
 1 AS `telefono_cliente`,
 1 AS `fax_cliente`,
 1 AS `web_cliente`,
 1 AS `email_cliente`,
 1 AS `nombre_facturacion_cliente`,
 1 AS `direccion_facturacion_cliente`,
 1 AS `cp_facturacion_cliente`,
 1 AS `poblacion_facturacion_cliente`,
 1 AS `provincia_facturacion_cliente`,
 1 AS `observaciones_cliente`,
 1 AS `activo_cliente`,
 1 AS `created_at_cliente`,
 1 AS `updated_at_cliente`,
 1 AS `porcentaje_descuento_cliente`,
 1 AS `id_forma_pago_habitual`,
 1 AS `codigo_pago`,
 1 AS `nombre_pago`,
 1 AS `descuento_pago`,
 1 AS `porcentaje_anticipo_pago`,
 1 AS `dias_anticipo_pago`,
 1 AS `porcentaje_final_pago`,
 1 AS `dias_final_pago`,
 1 AS `observaciones_pago`,
 1 AS `activo_pago`,
 1 AS `id_metodo_pago`,
 1 AS `codigo_metodo_pago`,
 1 AS `nombre_metodo_pago`,
 1 AS `observaciones_metodo_pago`,
 1 AS `activo_metodo_pago`,
 1 AS `cantidad_contactos_cliente`,
 1 AS `tipo_pago_cliente`,
 1 AS `descripcion_forma_pago_cliente`,
 1 AS `direccion_completa_cliente`,
 1 AS `direccion_facturacion_completa_cliente`,
 1 AS `tiene_direccion_facturacion_diferente`,
 1 AS `estado_forma_pago_cliente`,
 1 AS `categoria_descuento_cliente`,
 1 AS `tiene_descuento_cliente`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `contacto_cantidad_proveedor`
--

DROP TABLE IF EXISTS `contacto_cantidad_proveedor`;
/*!50001 DROP VIEW IF EXISTS `contacto_cantidad_proveedor`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `contacto_cantidad_proveedor` AS SELECT 
 1 AS `id_proveedor`,
 1 AS `codigo_proveedor`,
 1 AS `nombre_proveedor`,
 1 AS `direccion_proveedor`,
 1 AS `cp_proveedor`,
 1 AS `poblacion_proveedor`,
 1 AS `provincia_proveedor`,
 1 AS `nif_proveedor`,
 1 AS `telefono_proveedor`,
 1 AS `fax_proveedor`,
 1 AS `web_proveedor`,
 1 AS `email_proveedor`,
 1 AS `persona_contacto_proveedor`,
 1 AS `direccion_sat_proveedor`,
 1 AS `cp_sat_proveedor`,
 1 AS `poblacion_sat_proveedor`,
 1 AS `provincia_sat_proveedor`,
 1 AS `telefono_sat_proveedor`,
 1 AS `fax_sat_proveedor`,
 1 AS `email_sat_proveedor`,
 1 AS `observaciones_proveedor`,
 1 AS `activo_proveedor`,
 1 AS `created_at_proveedor`,
 1 AS `updated_at_proveedor`,
 1 AS `id_forma_pago_habitual`,
 1 AS `codigo_pago`,
 1 AS `nombre_pago`,
 1 AS `descuento_pago`,
 1 AS `porcentaje_anticipo_pago`,
 1 AS `dias_anticipo_pago`,
 1 AS `porcentaje_final_pago`,
 1 AS `dias_final_pago`,
 1 AS `observaciones_pago`,
 1 AS `activo_pago`,
 1 AS `id_metodo_pago`,
 1 AS `codigo_metodo_pago`,
 1 AS `nombre_metodo_pago`,
 1 AS `observaciones_metodo_pago`,
 1 AS `activo_metodo_pago`,
 1 AS `cantidad_contacto_proveedor`,
 1 AS `tipo_pago_proveedor`,
 1 AS `descripcion_forma_pago_proveedor`,
 1 AS `direccion_completa_proveedor`,
 1 AS `direccion_sat_completa_proveedor`,
 1 AS `tiene_direccion_sat`,
 1 AS `estado_forma_pago_proveedor`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `contacto_cliente`
--

DROP TABLE IF EXISTS `contacto_cliente`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacto_cliente` (
  `id_contacto_cliente` int unsigned NOT NULL AUTO_INCREMENT,
  `id_cliente` int unsigned NOT NULL,
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
  `updated_at_contacto_cliente` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_contacto_cliente`),
  KEY `idx_id_cliente_contacto` (`id_cliente`),
  KEY `idx_nombre_contacto_cliente` (`nombre_contacto_cliente`),
  CONSTRAINT `fk_contacto_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacto_cliente`
--

LOCK TABLES `contacto_cliente` WRITE;
/*!40000 ALTER TABLE `contacto_cliente` DISABLE KEYS */;
INSERT INTO `contacto_cliente` VALUES (1,1,'Luis','Carlos PéRez','','','','','','',1,'Prueba de contacto',1,'2025-11-17 05:59:28','2025-11-17 06:00:09'),(2,1,'Josep','Pastor Segura','Director de comunicación','Administración','','+34622505058','joseppastor22@gmail.com','',0,'',1,'2025-11-18 18:13:17','2025-12-14 10:35:45'),(3,2,'Josep','Pastor Segura','','','+34622505058','','','',1,'',1,'2025-11-18 19:11:09','2025-11-18 19:11:24'),(4,2,'Pepe','Diaz','','','','','','',1,'',1,'2025-11-18 19:11:39','2025-11-18 19:12:31'),(5,2,'Aaron','Sanchez','','','','','','',0,'',0,'2025-11-18 19:12:06','2025-11-18 19:42:09'),(6,4,'Maria','Peñas','Directora de eventos deportivos','Ventas','607244260','647163449','comercial@mdraudiovisuales.comjjjj','',1,'',1,'2025-12-11 18:58:38','2025-12-21 10:47:01'),(7,4,'Prueba de funcionamiento','','','','','6661225447','','',0,'',1,'2025-12-12 20:35:36','2025-12-12 20:35:36'),(8,4,'Prueba de eventos','','','','','','','',0,'',1,'2025-12-12 20:36:06','2025-12-12 20:36:06'),(9,4,'Prueba de eventos','','','','','','','',0,'',1,'2025-12-12 20:39:18','2025-12-12 20:39:18');
/*!40000 ALTER TABLE `contacto_cliente` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contacto_proveedor`
--

DROP TABLE IF EXISTS `contacto_proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contacto_proveedor` (
  `id_contacto_proveedor` int unsigned NOT NULL AUTO_INCREMENT,
  `id_proveedor` int unsigned NOT NULL,
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
  `updated_at_contacto_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_contacto_proveedor`),
  KEY `idx_id_proveedor_contacto` (`id_proveedor`),
  KEY `idx_nombre_contacto_proveedor` (`nombre_contacto_proveedor`),
  CONSTRAINT `fk_contacto_proveedor` FOREIGN KEY (`id_proveedor`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacto_proveedor`
--

LOCK TABLES `contacto_proveedor` WRITE;
/*!40000 ALTER TABLE `contacto_proveedor` DISABLE KEYS */;
INSERT INTO `contacto_proveedor` VALUES (1,1,'Luis Carlos','PéRez Mataix','Director de Ventas','Administración','660300923','660345258','luiscarlospm@gmail.com','123',1,'Es bastante simpático',1,'2025-11-16 06:26:58','2025-11-16 06:27:36'),(2,1,'Alicia','Botella','Directora general','Ventas','649163478','','alicia@gmail.com','123',0,'Es el contacto principal de ventas.',1,'2025-11-16 09:23:27','2025-11-26 09:32:44');
/*!40000 ALTER TABLE `contacto_proveedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `contactos`
--

DROP TABLE IF EXISTS `contactos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `contactos` (
  `id_contacto` int NOT NULL AUTO_INCREMENT,
  `id_llamada` int NOT NULL,
  `fecha_hora_contacto` datetime DEFAULT CURRENT_TIMESTAMP,
  `observaciones` text COLLATE utf8mb4_general_ci,
  `estado` tinyint NOT NULL DEFAULT '1' COMMENT '0=Inactivo, 1=Activo',
  `id_metodo` int NOT NULL,
  `id_visita_cerrada` int DEFAULT NULL,
  PRIMARY KEY (`id_contacto`),
  KEY `id_llamada` (`id_llamada`),
  KEY `fk_id_metodo` (`id_metodo`),
  KEY `fk_contactos_visitas_cerradas` (`id_visita_cerrada`),
  CONSTRAINT `contactos_ibfk_1` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`),
  CONSTRAINT `fk_contactos_visitas_cerradas` FOREIGN KEY (`id_visita_cerrada`) REFERENCES `visitas_cerradas` (`id_visita_cerrada`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_id_metodo` FOREIGN KEY (`id_metodo`) REFERENCES `metodos_contacto` (`id_metodo`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contactos`
--

LOCK TABLES `contactos` WRITE;
/*!40000 ALTER TABLE `contactos` DISABLE KEYS */;
INSERT INTO `contactos` VALUES (1,14,'2025-07-13 14:32:00','En este contacto, Tomás mostró interés en conocer más detalles sobre los tipos de toldos motorizados disponibles. Se le explicó el funcionamiento del sistema con mando a distancia y la opción de incluir sensores de viento para mayor seguridad. También expresó dudas sobre la instalación y el mantenimiento, que fueron aclaradas. Se acordó enviarle información técnica y un presupuesto preliminar para que pueda valorar las opciones con calma antes de la visita técnica.',1,1,NULL),(2,1,'2025-07-10 14:23:00','Solicitó una visita técnica para tomar medidas en su local comercial. Posible proyecto para instalación de toldos tipo cofre en fachada',1,3,5),(3,1,'2025-07-10 14:24:00','<p data-start=\"259\" data-end=\"384\">María mostró interés en los modelos básicos de toldos, pidió información sobre precios y tiempos de entrega.</p>\n<p data-start=\"386\" data-end=\"535\"></p>',1,1,NULL),(4,1,'2025-07-10 14:24:00','María consultó sobre la posibilidad de instalación rápida y pidió asesoramiento sobre el tipo de toldo más adecuado para su terraza.',0,3,NULL),(5,1,'2025-07-10 14:24:00','<p data-start=\"537\" data-end=\"664\">María preguntó sobre opciones de toldos con motor eléctrico y si hay garantía para los mecanismos automáticos.</p>\n<p data-start=\"666\" data-end=\"815\"></p>',1,2,NULL),(6,1,'2025-07-10 14:24:00','<p data-start=\"817\" data-end=\"947\">María manifestó dudas sobre el cuidado y limpieza del toldo, y pidió recomendaciones para prolongar su vida útil.</p>',0,1,NULL),(7,10,'2025-07-13 14:33:00','Comentó que su vecina le recomendó nuestra empresa. Valoró positivamente la reputación y experiencia del equipo técnico.',1,1,6),(8,2,'2025-04-21 06:52:00','El cliente se comunicó por vía telefónica para solicitar información sobre la instalación de un toldo. Mostró interés en conocer precios, tipos de materiales y opciones disponibles. Se tomó nota de sus requerimientos básicos y se ofreció agendar una visita técnica para evaluar el espacio. Se obtuvieron sus datos de contacto para seguimiento.',1,1,10),(9,4,'2025-07-14 14:30:00','<p>Durante este contacto, Luis consultó específicamente sobre la resistencia al viento del modelo propuesto, mostrando preocupación por la durabilidad del sistema. Se le explicó la clasificación de resistencia según normativa UNE-EN 13561 y se le sugirió la opción de incorporar un sensor de viento para mayor seguridad. Mostró interés y solicitó que dicha opción se incluyera en el presupuesto final.</p>',1,3,4),(14,20,'2025-07-12 14:30:00','<p>Luis expresa su intención de renovar varios toldos de la vivienda, priorizando tanto la protección solar como la integración estética con la fachada. Comenta que algunos toldos actuales están deteriorados. Se acuerda una visita para la próxima semana con el fin de tomar medidas y estudiar in situ las opciones más adecuadas. También solicita que en esa visita se le muestren catálogos con diferentes tipos de lona, haciendo especial énfasis en tejidos técnicos y colores neutros.</p>',1,2,7),(17,22,'2025-07-12 14:31:00','<p>Teresa se mostró interesada en renovar el toldo de su terraza debido al desgaste por el sol. Durante la llamada, consultó sobre diferentes tipos de lonas resistentes a la decoloración y preguntó por opciones con sistemas de apertura manual y motorizados. Se le explicó el funcionamiento y ventajas de cada sistema, y se acordó enviarle muestras de tejidos para que pudiera elegir. También solicitó información sobre plazos de instalación y garantías. Quedó pendiente concretar una visita técnica para tomar medidas y avanzar con el presupuesto.</p>',1,2,8),(18,24,'2025-05-22 13:06:00','<p data-start=\"99\" data-end=\"633\">Durante la llamada con Jorge, se confirmó la recepción de la documentación solicitada y se revisaron los detalles técnicos del equipo a instalar. Jorge expresó algunas dudas sobre la configuración del servicio y se le explicó paso a paso el proceso. Además, se acordó que el técnico se pondrá en contacto con él para coordinar la visita en el domicilio. Jorge mostró interés en opciones adicionales, como la instalación de dispositivos complementarios, lo cual será evaluado en próximas comunicaciones.</p>',1,1,9),(19,6,'2025-07-12 14:32:00','<p>Durante el contacto, el cliente solicitó información sobre toldos para un área exterior de aproximadamente 5 metros. Indicó interés en un modelo retráctil, preferiblemente motorizado. Se le brindó una explicación general de los tipos de toldos disponibles, materiales y tiempos estimados de instalación. Se tomaron sus datos para agendar una visita técnica y se le envió catálogo digital por WhatsApp. Cliente receptivo y con intención de avanzar en el proceso.</p>',1,1,NULL),(20,21,'2025-07-14 13:15:00','<p>En este contacto, Tomás mostró interés en un toldo vertical para su galería, haciendo hincapié en la necesidad de controlar la entrada de luz y el calor sin oscurecer demasiado el espacio. Preguntó sobre las diferencias entre los tejidos técnicos disponibles y mostró especial atención a las opciones que incluyen guías laterales para mayor estabilidad. También consultó sobre la automatización con sensores solares, buscando comodidad y eficiencia. Se le explicó brevemente cada opción y se acordó realizar una visita técnica para evaluar medidas y condiciones, así como para tomar una decisión informada.</p>',1,2,NULL),(21,23,'2025-07-13 14:32:00','<p>Alejandro consultó sobre las diferentes opciones de tejidos técnicos para toldos, mostrando especial interés en aquellos que ofrecen mayor resistencia al sol y a la lluvia. Se aclararon dudas sobre los colores disponibles y se le explicó el funcionamiento del sistema motorizado con mando a distancia. Alejandro pidió que se le enviara información adicional por correo electrónico para analizarla con su familia. Se confirmó la visita técnica para la próxima semana para realizar las mediciones necesarias.</p>',1,3,NULL),(22,19,'2025-07-12 14:33:00','<p>En este contacto, Javier mostró interés en las opciones motorizadas, preguntando específicamente por la duración de la batería y la compatibilidad con sistemas domóticos. Se le explicó el funcionamiento del mando a distancia y la posibilidad de integrar sensores de viento para una mayor seguridad. Javier solicitó además información sobre los colores disponibles y el mantenimiento recomendado para prolongar la vida útil del toldo. Se acordó enviarle material informativo y concretar la visita para toma de medidas la próxima semana.</p>',1,3,NULL),(23,16,'2025-07-12 14:31:00','<p>Lorena mostró un gran interés en las diferentes opciones de toldos para su vivienda, especialmente en modelos que ofrezcan protección solar eficaz y resistencia a las condiciones climáticas de Valencia. Preguntó por los materiales disponibles, colores y tiempos de instalación. Además, destacó la importancia de un diseño que combine funcionalidad y estética para su terraza. Se acordó enviarle un catálogo con opciones personalizadas y coordinar una visita técnica para evaluar las medidas exactas y ofrecer un presupuesto detallado.</p>',1,2,NULL),(24,2,'2025-07-17 09:28:00','<p><br></p>',1,1,NULL);
/*!40000 ALTER TABLE `contactos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `contactos_con_nombre_comunicante`
--

DROP TABLE IF EXISTS `contactos_con_nombre_comunicante`;
/*!50001 DROP VIEW IF EXISTS `contactos_con_nombre_comunicante`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `contactos_con_nombre_comunicante` AS SELECT 
 1 AS `id_contacto`,
 1 AS `id_llamada`,
 1 AS `id_metodo`,
 1 AS `fecha_hora_contacto`,
 1 AS `observaciones`,
 1 AS `id_visita_cerrada`,
 1 AS `fecha_visita_cerrada`,
 1 AS `estado`,
 1 AS `nombre_comunicante`,
 1 AS `domicilio_instalacion`,
 1 AS `telefono_fijo`,
 1 AS `telefono_movil`,
 1 AS `email_contacto`,
 1 AS `fecha_hora_preferida`,
 1 AS `fecha_recepcion`,
 1 AS `id_comercial_asignado`,
 1 AS `estado_llamada`,
 1 AS `activo_llamada`,
 1 AS `nombre_metodo`,
 1 AS `imagen_metodo`,
 1 AS `descripcion_estado_llamada`,
 1 AS `nombre_comercial`,
 1 AS `archivos_adjuntos`,
 1 AS `tiene_contactos`,
 1 AS `estado_es_3`,
 1 AS `tiene_adjuntos`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `documento`
--

DROP TABLE IF EXISTS `documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documento` (
  `id_documento` int unsigned NOT NULL AUTO_INCREMENT,
  `titulo_documento` varchar(255) COLLATE utf8mb4_spanish_ci NOT NULL,
  `descripcion_documento` text COLLATE utf8mb4_spanish_ci,
  `ruta_documento` varchar(500) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Ruta relativa del archivo PDF',
  `id_tipo_documento_documento` int unsigned NOT NULL,
  `fecha_publicacion_documento` date DEFAULT NULL,
  `activo_documento` tinyint(1) DEFAULT '1',
  `fecha_creacion_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `fecha_modificacion_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_documento`),
  KEY `idx_tipo_documento` (`id_tipo_documento_documento`),
  KEY `idx_activo_documento` (`activo_documento`),
  CONSTRAINT `documento_ibfk_1` FOREIGN KEY (`id_tipo_documento_documento`) REFERENCES `tipo_documento` (`id_tipo_documento`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documento`
--

LOCK TABLES `documento` WRITE;
/*!40000 ALTER TABLE `documento` DISABLE KEYS */;
INSERT INTO `documento` VALUES (1,'Manual de seguridad','Es una prueba de grabación','documento_6943079f41802.pdf',1,'2025-12-17',1,'2025-12-17 19:42:23','2025-12-17 19:52:15');
/*!40000 ALTER TABLE `documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `documento_elemento`
--

DROP TABLE IF EXISTS `documento_elemento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `documento_elemento` (
  `id_documento_elemento` int unsigned NOT NULL AUTO_INCREMENT,
  `id_elemento` int unsigned NOT NULL,
  `descripcion_documento_elemento` text,
  `tipo_documento_elemento` varchar(100) DEFAULT NULL COMMENT 'Tipo: Manual, Garantía, Factura, Certificado, etc.',
  `archivo_documento` varchar(500) NOT NULL COMMENT 'Ruta completa con nombre del archivo',
  `privado_documento` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, solo visible para administración',
  `observaciones_documento` text,
  `activo_documento` tinyint(1) DEFAULT '1',
  `created_at_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_documento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_documento_elemento`),
  KEY `idx_id_elemento_documento` (`id_elemento`),
  KEY `idx_tipo_documento` (`tipo_documento_elemento`),
  KEY `idx_privado_documento` (`privado_documento`),
  CONSTRAINT `fk_documento_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `documento_elemento`
--

LOCK TABLES `documento_elemento` WRITE;
/*!40000 ALTER TABLE `documento_elemento` DISABLE KEYS */;
INSERT INTO `documento_elemento` VALUES (1,1,'Prueba de documentosssssss','Certificado','documento_elemento_692c8b7dac770.pdf',1,'Prueba de documentossss',1,'2025-11-30 18:22:53','2025-11-30 18:59:41'),(2,1,'Prueba de segundo documento','Certificado','documento_elemento_692c945939d91.pdf',0,NULL,1,'2025-11-30 19:00:41','2025-11-30 19:00:41');
/*!40000 ALTER TABLE `documento_elemento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `elemento`
--

DROP TABLE IF EXISTS `elemento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `elemento` (
  `id_elemento` int unsigned NOT NULL AUTO_INCREMENT,
  `id_articulo_elemento` int unsigned NOT NULL,
  `id_marca_elemento` int unsigned DEFAULT NULL,
  `modelo_elemento` varchar(100) DEFAULT NULL,
  `codigo_elemento` varchar(50) NOT NULL COMMENT 'Formato: codigo_articulo-correlativo',
  `codigo_barras_elemento` varchar(100) DEFAULT NULL,
  `descripcion_elemento` varchar(255) NOT NULL,
  `numero_serie_elemento` varchar(100) DEFAULT NULL,
  `id_estado_elemento` int unsigned NOT NULL DEFAULT '1' COMMENT 'Estado actual del elemento',
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
  `id_proveedor_compra_elemento` int unsigned DEFAULT NULL COMMENT 'Proveedor que vendió el elemento (solo si es_propio = TRUE)',
  `id_proveedor_alquiler_elemento` int unsigned DEFAULT NULL COMMENT 'Proveedor al que se alquila el elemento (solo si es_propio = FALSE)',
  `precio_dia_alquiler_elemento` decimal(10,2) DEFAULT NULL COMMENT 'Precio por día que pagamos al proveedor por alquilar este elemento',
  `id_forma_pago_alquiler_elemento` int unsigned DEFAULT NULL COMMENT 'Forma de pago acordada con el proveedor para el alquiler (solo si es_propio = FALSE)',
  `observaciones_alquiler_elemento` text COMMENT 'Condiciones especiales de alquiler: mínimo de días, restricciones, contacto, etc.',
  `created_at_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_elemento`),
  UNIQUE KEY `codigo_elemento` (`codigo_elemento`),
  UNIQUE KEY `codigo_barras_elemento` (`codigo_barras_elemento`),
  UNIQUE KEY `numero_serie_elemento` (`numero_serie_elemento`),
  KEY `idx_codigo_elemento` (`codigo_elemento`),
  KEY `idx_codigo_barras_elemento` (`codigo_barras_elemento`),
  KEY `idx_numero_serie_elemento` (`numero_serie_elemento`),
  KEY `idx_id_articulo_elemento` (`id_articulo_elemento`),
  KEY `idx_id_marca_elemento` (`id_marca_elemento`),
  KEY `idx_id_estado_elemento` (`id_estado_elemento`),
  KEY `idx_nave_elemento` (`nave_elemento`),
  KEY `idx_pasillo_columna_elemento` (`pasillo_columna_elemento`),
  KEY `idx_fecha_compra_elemento` (`fecha_compra_elemento`),
  KEY `idx_es_propio_elemento` (`es_propio_elemento`),
  KEY `idx_id_proveedor_compra_elemento` (`id_proveedor_compra_elemento`),
  KEY `idx_id_proveedor_alquiler_elemento` (`id_proveedor_alquiler_elemento`),
  KEY `idx_id_forma_pago_alquiler_elemento` (`id_forma_pago_alquiler_elemento`),
  CONSTRAINT `fk_elemento_articulo` FOREIGN KEY (`id_articulo_elemento`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_elemento_estado` FOREIGN KEY (`id_estado_elemento`) REFERENCES `estado_elemento` (`id_estado_elemento`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_elemento_forma_pago_alquiler` FOREIGN KEY (`id_forma_pago_alquiler_elemento`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_elemento_marca` FOREIGN KEY (`id_marca_elemento`) REFERENCES `marca` (`id_marca`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_elemento_proveedor_alquiler` FOREIGN KEY (`id_proveedor_alquiler_elemento`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_elemento_proveedor_compra` FOREIGN KEY (`id_proveedor_compra_elemento`) REFERENCES `proveedor` (`id_proveedor`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `elemento`
--

LOCK TABLES `elemento` WRITE;
/*!40000 ALTER TABLE `elemento` DISABLE KEYS */;
INSERT INTO `elemento` VALUES (1,21,1,'SH-78','MIC-INAL-001-001','123456789','Microfono Senheiser SH-78','123456789',1,'1','a-5','1',NULL,NULL,NULL,NULL,NULL,'Prueba de observaciones',1,0,NULL,1,800.00,14,'','2025-11-25 07:18:30','2025-12-22 10:04:41'),(2,21,1,'SH-79','MIC-INAL-001-002','123456778','Microfono Senheiser SH-79','112345678',1,'1','a-5','1','2025-11-29',1020.00,'2025-11-29','2026-11-29','2025-12-20','Prueba de observaciones',1,1,1,NULL,NULL,NULL,'','2025-12-02 15:49:39','2025-12-18 19:13:14'),(3,25,9,'LED-PROX-1200','LED-PANEL-P3-001','121333311','Módulo LED Philips Lumileds LED-PROX-1200','332444228',1,'1','a-2','1','2025-12-02',217.00,'2025-12-02','2026-12-02','2026-02-01','Prueba de observaciones',1,1,NULL,NULL,NULL,NULL,NULL,'2025-12-02 16:04:40','2025-12-02 16:04:40'),(4,25,9,'LED-PROX-1200','LED-PANEL-P3-002','224111111','Módulo LED Philips Lumileds LED-PROX-1200','123333333',1,'1','a2','1','2024-12-01',217.00,'2024-12-01','2025-12-01','2025-11-01','Prueba de observaciones',1,1,NULL,NULL,NULL,NULL,NULL,'2025-12-02 16:13:59','2025-12-02 16:13:59'),(5,21,4,'EB-2250U','MIC-INAL-001-003','4012831029342','Proyector Epson EB-2250U Alquilado','EP-2024-0082547',1,'','C-12, P-5','Nivel 3',NULL,NULL,NULL,NULL,NULL,'Equipo alquilado en perfecto estado, mantiene calibración óptica. Factura disponible bajo demanda. Datos del cliente: TechSoluciones SL.',1,0,NULL,1,150.00,5,'Mínimo 30 días, incluye seguro de daños, contacto: +34 912 345 678','2025-12-18 20:14:43','2025-12-19 07:38:58');
/*!40000 ALTER TABLE `elemento` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_elemento_before_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_elemento_limpiar_campos_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_elemento_sync_estado_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    -- Si el estado es 4 (Dado de baja)
    IF NEW.id_estado_elemento = 4 THEN
        SET NEW.activo_elemento = FALSE;
    ELSE
        -- Cualquier otro estado
        SET NEW.activo_elemento = TRUE;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_elemento_sync_activo_insert` BEFORE INSERT ON `elemento` FOR EACH ROW BEGIN
    -- Si se está insertando como inactivo
    IF NEW.activo_elemento = FALSE THEN
        SET NEW.id_estado_elemento = 4; -- Dado de baja
    ELSE
        -- Si viene activo y el estado no está definido o es inconsistente
        IF NEW.id_estado_elemento IS NULL OR NEW.id_estado_elemento = 4 THEN
            SET NEW.id_estado_elemento = 1; -- Disponible
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_elemento_limpiar_campos_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_elemento_sync_estado_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_elemento_sync_activo_update` BEFORE UPDATE ON `elemento` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa` (
  `id_empresa` int unsigned NOT NULL AUTO_INCREMENT,
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
  `numero_actual_presupuesto_empresa` int unsigned DEFAULT '0' COMMENT 'Último número de presupuesto emitido',
  `dias_validez_presupuesto_empresa` int unsigned NOT NULL DEFAULT '30' COMMENT 'Días de validez por defecto para los presupuestos emitidos por esta empresa',
  `serie_factura_empresa` varchar(10) DEFAULT 'F' COMMENT 'Serie para facturas (ej: F, FAC, A)',
  `numero_actual_factura_empresa` int unsigned DEFAULT '0' COMMENT 'Último número de factura emitido',
  `serie_abono_empresa` varchar(10) DEFAULT 'R' COMMENT 'Serie para facturas rectificativas/abonos (ej: R, AB, REC)',
  `numero_actual_abono_empresa` int unsigned DEFAULT '0' COMMENT 'Último número de abono emitido',
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
  PRIMARY KEY (`id_empresa`),
  UNIQUE KEY `codigo_empresa` (`codigo_empresa`),
  UNIQUE KEY `nif_empresa` (`nif_empresa`),
  KEY `idx_codigo_empresa` (`codigo_empresa`),
  KEY `idx_nif_empresa` (`nif_empresa`),
  KEY `idx_ficticia_empresa` (`ficticia_empresa`),
  KEY `idx_empresa_ficticia_principal` (`empresa_ficticia_principal`),
  KEY `idx_activo_empresa` (`activo_empresa`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Gestión de empresas del grupo para facturación y presupuestos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
INSERT INTO `empresa` VALUES (1,'FICTICIA','MDR Audiovisuales Group','MDR Group',1,1,'B00000000','Polígono Industrial La Paz','06006','Badajoz','Badajoz','España','+34 924 000 000','','info@mdrgroup.com','','','','','','P',2,30,'F',0,'R',0,1,NULL,NULL,NULL,NULL,'online',NULL,NULL,'/images/logos/mdr_group_logo.png',NULL,NULL,NULL,NULL,NULL,1,'2025-12-01 18:35:27','2026-01-21 09:40:39'),(2,'MDR01','MDR AUDIOVISUALES S.L.','MDR Audiovisuales',0,0,'B06123456','Polígono Industrial La Paz, Nave 15','06006','Badajoz','Badajoz','España','+34 924 123 456','+34 600 123 456','info@mdraudiovisuales.com','facturacion@mdraudiovisuales.com','www.mdraudiovisuales.com','ES91 2100 0418 4502 0005 1332','CAIXESBBXXX','CaixaBank','P',0,30,'F',0,'R',0,1,'MDR ERP Manager','1.0','B00000001','Software Solutions S.L.','online','https://www.agenciatributaria.gob.es/verifactu/api',NULL,'/images/logos/mdr_audiovisuales_logo.png','/images/logos/mdr_audiovisuales_pie.png','MDR AUDIOVISUALES S.L. - B06123456 - Inscrita en el Registro Mercantil de Badajoz, Tomo 123, Folio 456, Hoja BA-7890. Capital Social: 3.000,00 EUR','Oferta sujeta a disponibilidad de equipos. Consulte condiciones generales en www.mdraudiovisuales.com','Factura sujeta a la Ley General Tributaria. Conserve este documento para su contabilidad.','Empresa principal del grupo para alquiler de equipos audiovisuales profesionales.',1,'2025-12-01 18:35:27','2025-12-01 18:35:27'),(3,'MDR02','MDR EVENTOS Y PRODUCCIONES S.L.','MDR Eventos',0,0,'B06654321','Polígono Industrial La Paz, Nave 16','06006','Badajoz','Badajoz','España','+34 924 654 321','+34 600 654 321','info@mdreventos.com','facturacion@mdreventos.com','www.mdreventos.com','ES91 2100 0418 4502 0005 9999','CAIXESBBXXX','CaixaBank','PE',0,30,'FE',0,'RE',0,1,'MDR ERP Manager','1.0','B00000001','Software Solutions S.L.','online',NULL,NULL,'/images/logos/mdr_eventos_logo.png',NULL,'MDR EVENTOS Y PRODUCCIONES S.L. - B06654321 - Inscrita en el Registro Mercantil de Badajoz, Tomo 456, Folio 789, Hoja BA-1234. Capital Social: 5.000,00 EUR',NULL,'Factura sujeta a la Ley General Tributaria. Conserve este documento para su contabilidad.','Empresa especializada en producción y gestión integral de eventos.',1,'2025-12-01 18:35:27','2025-12-01 18:35:27');
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_elemento`
--

DROP TABLE IF EXISTS `estado_elemento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_elemento` (
  `id_estado_elemento` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_estado_elemento` varchar(20) NOT NULL,
  `descripcion_estado_elemento` varchar(50) NOT NULL,
  `color_estado_elemento` varchar(7) DEFAULT NULL COMMENT 'Color hexadecimal para visualización',
  `permite_alquiler_estado_elemento` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, el elemento puede ser alquilado en este estado',
  `observaciones_estado_elemento` text,
  `activo_estado_elemento` tinyint(1) DEFAULT '1',
  `created_at_estado_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_estado_elemento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_estado_elemento`),
  UNIQUE KEY `codigo_estado_elemento` (`codigo_estado_elemento`),
  KEY `idx_codigo_estado_elemento` (`codigo_estado_elemento`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_elemento`
--

LOCK TABLES `estado_elemento` WRITE;
/*!40000 ALTER TABLE `estado_elemento` DISABLE KEYS */;
INSERT INTO `estado_elemento` VALUES (1,'DISP','Disponible','#4CAF50',1,NULL,1,'2025-11-24 08:50:18','2025-11-24 08:50:18'),(2,'ALQU','Alquilado','#2196f3',0,'Esta es una prueba de observaciones de estado de elementos',1,'2025-11-24 08:50:18','2025-11-24 09:43:34'),(3,'REPA','En reparación','#FF9800',0,NULL,1,'2025-11-24 08:50:18','2025-11-24 08:50:18'),(4,'BAJA','Dado de baja','#f44336',0,'',1,'2025-11-24 08:50:18','2025-11-24 09:44:15'),(5,'TERC','De terceros','#9C27B0',1,NULL,1,'2025-11-24 08:50:18','2025-11-24 08:50:18'),(6,'DEPO','En depósito','#607D8B',0,NULL,1,'2025-11-24 08:50:18','2025-11-24 08:50:18'),(7,'MANT','Mantenimiento','#FFC107',0,NULL,1,'2025-11-24 08:50:18','2025-11-24 08:50:18'),(8,'TRAN','En tránsito','#00BCD4',0,NULL,1,'2025-11-24 08:50:18','2025-11-24 08:50:18');
/*!40000 ALTER TABLE `estado_elemento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estado_presupuesto`
--

DROP TABLE IF EXISTS `estado_presupuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estado_presupuesto` (
  `id_estado_ppto` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_estado_ppto` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre_estado_ppto` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `color_estado_ppto` varchar(7) COLLATE utf8mb4_unicode_ci DEFAULT '#007bff',
  `orden_estado_ppto` int DEFAULT '0',
  `observaciones_estado_ppto` text COLLATE utf8mb4_unicode_ci,
  `activo_estado_ppto` tinyint(1) DEFAULT '1',
  `created_at_estado_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_estado_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_estado_ppto`),
  UNIQUE KEY `codigo_estado_ppto` (`codigo_estado_ppto`),
  KEY `idx_estado_presupuesto_activo` (`activo_estado_ppto`),
  KEY `idx_estado_presupuesto_codigo` (`codigo_estado_ppto`),
  KEY `idx_estado_presupuesto_orden` (`orden_estado_ppto`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_presupuesto`
--

LOCK TABLES `estado_presupuesto` WRITE;
/*!40000 ALTER TABLE `estado_presupuesto` DISABLE KEYS */;
INSERT INTO `estado_presupuesto` VALUES (1,'BORRADOR','BORRADOR','#0000ff',20,'Presupuesto todavía no enviado al cliente',1,'2025-11-14 12:35:03','2026-01-20 08:58:58'),(2,'PROC','En Proceso','#17a2b8',10,'Presupuesto en proceso de elaboración',1,'2025-11-14 12:35:03','2025-12-13 11:25:32'),(3,'APROB','Aprobado','#28a745',40,'Presupuesto aprobado por el cliente',1,'2025-11-14 12:35:03','2025-12-13 11:30:19'),(4,'RECH','Rechazado','#dc3545',50,'Presupuesto rechazado por el cliente',1,'2025-11-14 12:35:03','2025-12-13 11:30:30'),(5,'CANC','Cancelado','#6c757d',60,'Presupuesto cancelado',1,'2025-11-14 12:35:03','2025-12-13 11:30:40'),(8,'ESPE-RESP','Esperando respuesta','#ff9b29',30,'Presupuesto enviado en  espera de respuesta por parte del cliente',1,'2025-12-13 11:26:45','2025-12-13 11:49:12');
/*!40000 ALTER TABLE `estado_presupuesto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estados_llamada`
--

DROP TABLE IF EXISTS `estados_llamada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados_llamada` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `desc_estado` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `defecto_estado` tinyint DEFAULT NULL,
  `activo_estado` varchar(200) COLLATE utf8mb4_general_ci NOT NULL,
  `peso_estado` int NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados_llamada`
--

LOCK TABLES `estados_llamada` WRITE;
/*!40000 ALTER TABLE `estados_llamada` DISABLE KEYS */;
INSERT INTO `estados_llamada` VALUES (1,'Recibida sin atención',1,'1',10),(2,'Con contacto',0,'1',20),(3,'Cita Cerrada',0,'1',30),(4,'Perdida',0,'1',40),(12,'En espera',0,'1',60);
/*!40000 ALTER TABLE `estados_llamada` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `familia`
--

DROP TABLE IF EXISTS `familia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `familia` (
  `id_familia` int unsigned NOT NULL AUTO_INCREMENT,
  `id_grupo` int unsigned DEFAULT NULL,
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
  `updated_at_familia` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_familia`),
  UNIQUE KEY `codigo_familia` (`codigo_familia`),
  KEY `idx_id_grupo_familia` (`id_grupo`),
  KEY `idx_permite_descuento_familia` (`permite_descuento_familia`),
  CONSTRAINT `fk_familia_grupo` FOREIGN KEY (`id_grupo`) REFERENCES `grupo_articulo` (`id_grupo`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `familia`
--

LOCK TABLES `familia` WRITE;
/*!40000 ALTER TABLE `familia` DISABLE KEYS */;
INSERT INTO `familia` VALUES (19,2,'AUD-MIC','Microfonía y Sonido','Microphones and Sound','Equipos de captación y procesamiento de audio profesional',1,1,1,1,'familias/audio_microfonia.jpg','Todos los equipos de audio incluyen cables de conexión básicos. El técnico de sonido se cotiza por separado. Se requiere prueba de sonido 2 horas antes del evento.','',100,'2025-11-20 20:28:05','2025-12-19 08:23:45'),(20,2,'ILU-GEN','Iluminación Profesional','Professional Lighting','Equipos de iluminación escénica, arquitectónica y de efectos',1,1,1,1,'familias/iluminacion_profesional.jpg','La iluminación requiere acceso a cuadro eléctrico con tomas trifásicas. Incluye programación básica de escenas. El operador de iluminación se cotiza por separado.',NULL,110,'2025-11-20 20:28:05','2025-11-20 20:28:05'),(21,3,'ACC-CABLE','Cableado y Conectores','Cables and Connectors','Cables de audio, video, datos y alimentación, conectores y adaptadores',1,1,0,2,'familias/cables_conectores.jpg','Los cables se alquilan en tramos estándar. Disponemos de cables especiales bajo pedido. Se recomienda solicitar 20% adicional como backup.','',300,'2025-11-20 20:28:05','2026-01-20 17:44:56'),(22,2,'VID-PROY','Video y Proyección','Video and Projection','Proyectores, pantallas LED, procesadores de video y sistemas de visualización',1,1,0,1,'familias/video_proyeccion.jpg','Los equipos de video requieren visita técnica previa para verificar condiciones de instalación. Montaje de pantallas LED requiere mínimo 24h de antelación. Incluye técnico durante el evento.','',50,'2025-11-20 20:28:05','2026-01-20 17:36:08'),(23,3,'EST-TRUSS','Estructuras y Rigging','Structures and Rigging','Truss, torres de elevación, motores y sistemas de suspensión certificados',1,1,0,2,'familias/estructuras_rigging.jpg','IMPORTANTE: Todas las estructuras requieren certificación de carga y punto de anclaje certificado. Instalación únicamente por personal cualificado. Se requiere seguro de responsabilidad civil. Cálculo de cargas obligatorio.',NULL,20,'2025-11-20 20:28:05','2025-11-20 20:28:05');
/*!40000 ALTER TABLE `familia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `familia_unidad_media`
--

DROP TABLE IF EXISTS `familia_unidad_media`;
/*!50001 DROP VIEW IF EXISTS `familia_unidad_media`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `familia_unidad_media` AS SELECT 
 1 AS `id_familia`,
 1 AS `id_grupo`,
 1 AS `codigo_familia`,
 1 AS `nombre_familia`,
 1 AS `name_familia`,
 1 AS `descr_familia`,
 1 AS `imagen_familia`,
 1 AS `activo_familia`,
 1 AS `coeficiente_familia`,
 1 AS `created_at_familia`,
 1 AS `updated_at_familia`,
 1 AS `id_unidad_familia`,
 1 AS `observaciones_presupuesto_familia`,
 1 AS `orden_obs_familia`,
 1 AS `nombre_unidad`,
 1 AS `descr_unidad`,
 1 AS `simbolo_unidad`,
 1 AS `activo_unidad`,
 1 AS `codigo_grupo`,
 1 AS `nombre_grupo`,
 1 AS `descripcion_grupo`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `forma_pago`
--

DROP TABLE IF EXISTS `forma_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forma_pago` (
  `id_pago` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_pago` varchar(20) NOT NULL COMMENT 'Código único identificador (ej: CONT_TRANS, FRAC40_60)',
  `nombre_pago` varchar(100) NOT NULL COMMENT 'Nombre descriptivo de la forma de pago',
  `id_metodo_pago` int unsigned NOT NULL COMMENT 'Método de pago a utilizar (transferencia, tarjeta, efectivo...)',
  `descuento_pago` decimal(5,2) DEFAULT '0.00' COMMENT 'Descuento por pronto pago en porcentaje (ej: 2.00 = 2%). Solo aplica si porcentaje_anticipo_pago = 100',
  `porcentaje_anticipo_pago` decimal(5,2) DEFAULT '100.00' COMMENT 'Porcentaje del total a pagar como anticipo (ej: 40.00 = 40%). Si es 100.00 = pago único',
  `dias_anticipo_pago` int DEFAULT '0' COMMENT 'Días para pagar el anticipo desde la firma del presupuesto. 0=al firmar, 7=a los 7 días, 30=a los 30 días',
  `porcentaje_final_pago` decimal(5,2) DEFAULT '0.00' COMMENT 'Porcentaje restante del total (ej: 60.00 = 60%). Debe sumar 100% con el anticipo. Si es 0 = pago único',
  `dias_final_pago` int DEFAULT '0' COMMENT 'Días para el pago final. Positivo=días desde firma (30=a 30 días), Negativo=días antes del evento (-7=7 días antes), 0=al finalizar evento',
  `observaciones_pago` text COMMENT 'Observaciones internas sobre esta forma de pago',
  `activo_pago` tinyint(1) DEFAULT '1' COMMENT 'Si FALSE, la forma de pago no estará disponible para nuevos presupuestos',
  `created_at_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pago`),
  UNIQUE KEY `codigo_pago` (`codigo_pago`),
  KEY `idx_id_metodo_pago` (`id_metodo_pago`),
  KEY `idx_activo_pago` (`activo_pago`),
  CONSTRAINT `fk_forma_pago_metodo` FOREIGN KEY (`id_metodo_pago`) REFERENCES `metodo_pago` (`id_metodo_pago`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forma_pago`
--

LOCK TABLES `forma_pago` WRITE;
/*!40000 ALTER TABLE `forma_pago` DISABLE KEYS */;
INSERT INTO `forma_pago` VALUES (1,'CONT_TRANS','Contado transferencia',1,2.00,100.00,0,0.00,0,NULL,1,'2025-11-20 07:08:35','2025-11-20 07:08:35'),(2,'CONT_EFEC','Contado efectivo',3,2.00,100.00,0,0.00,0,'',1,'2025-11-20 07:08:35','2025-11-21 06:16:47'),(3,'CONT_TARJ','Contado tarjeta',2,0.00,100.00,0,0.00,0,NULL,1,'2025-11-20 07:08:35','2025-11-20 07:08:35'),(4,'TRANS7','Transferencia 7 días',1,0.00,100.00,7,0.00,0,'',1,'2025-11-20 07:08:35','2025-12-09 12:23:59'),(5,'TRANS30','Transferencia 30 días',1,0.00,100.00,30,0.00,0,NULL,1,'2025-11-20 07:08:35','2025-11-20 07:08:35'),(6,'TRANS60','Transferencia 60 días',1,0.00,100.00,60,0.00,0,NULL,1,'2025-11-20 07:08:35','2025-11-20 07:08:35'),(7,'TRANS90','Transferencia 90 días',1,0.00,100.00,90,0.00,0,NULL,1,'2025-11-20 07:08:35','2025-11-20 07:08:35'),(8,'FRAC40_60','40% anticipo + 60% al finalizar',1,0.00,40.00,0,60.00,0,'Anticipo al firmar presupuesto, resto al finalizar evento',1,'2025-11-20 07:10:11','2025-11-20 07:10:11'),(9,'FRAC50_50','50% anticipo + 50% al finalizar',1,0.00,50.00,0,50.00,0,'Pago dividido en dos partes iguales',1,'2025-11-20 07:10:11','2025-11-20 07:10:11'),(10,'FRAC50_30','50% anticipo + 50% a 30 días',1,0.00,50.00,0,50.00,30,'Anticipo al firmar, resto a 30 días desde firma',1,'2025-11-20 07:10:11','2025-11-20 07:10:11'),(11,'FRAC30_60','30% anticipo + 70% a 60 días',1,0.00,30.00,0,70.00,60,'Anticipo al firmar, resto a 60 días desde firma',1,'2025-11-20 07:10:11','2025-11-20 07:10:11'),(12,'FRAC30_7','30% anticipo + 70% (7 días antes)',1,0.00,30.00,0,70.00,-7,'Anticipo al firmar, resto 7 días antes del evento',1,'2025-11-20 07:10:11','2025-11-20 07:10:11'),(13,'FRAC40_15','40% anticipo + 60% (15 días antes)',1,0.00,40.00,0,60.00,-15,'Anticipo al firmar, resto 15 días antes del evento',1,'2025-11-20 07:10:11','2025-11-20 07:10:11'),(14,'FRAC50_7_30','50% a 7 días + 50% a 30 días',1,0.00,50.00,7,50.00,30,'Primer pago a los 7 días de firmar, segundo a los 30 días de firmar',1,'2025-11-20 07:10:11','2025-11-20 07:10:11');
/*!40000 ALTER TABLE `forma_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `foto_elemento`
--

DROP TABLE IF EXISTS `foto_elemento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `foto_elemento` (
  `id_foto_elemento` int unsigned NOT NULL AUTO_INCREMENT,
  `id_elemento` int unsigned NOT NULL,
  `descripcion_foto_elemento` text,
  `archivo_foto` varchar(500) NOT NULL COMMENT 'Ruta completa con nombre del archivo de imagen',
  `privado_foto` tinyint(1) DEFAULT '0' COMMENT 'Si TRUE, solo visible para administración',
  `observaciones_foto` text,
  `activo_foto` tinyint(1) DEFAULT '1',
  `created_at_foto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_foto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_foto_elemento`),
  KEY `idx_id_elemento_foto` (`id_elemento`),
  KEY `idx_privado_foto` (`privado_foto`),
  CONSTRAINT `fk_foto_elemento` FOREIGN KEY (`id_elemento`) REFERENCES `elemento` (`id_elemento`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `foto_elemento`
--

LOCK TABLES `foto_elemento` WRITE;
/*!40000 ALTER TABLE `foto_elemento` DISABLE KEYS */;
INSERT INTO `foto_elemento` VALUES (1,1,'Microfonosssss','foto_elemento_692ca10c2a911.png',0,'Prueba de observacionesssssss',1,'2025-11-30 19:54:52','2025-11-30 19:55:26'),(2,3,'Microfono','foto_elemento_69380b27261c8.jpg',0,NULL,1,'2025-12-09 11:42:31','2025-12-09 11:42:31');
/*!40000 ALTER TABLE `foto_elemento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `furgoneta`
--

DROP TABLE IF EXISTS `furgoneta`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `furgoneta` (
  `id_furgoneta` int unsigned NOT NULL AUTO_INCREMENT,
  `matricula_furgoneta` varchar(20) NOT NULL COMMENT 'Matrícula del vehículo',
  `marca_furgoneta` varchar(100) DEFAULT NULL COMMENT 'Marca del vehículo (Renault, Mercedes, Ford, etc.)',
  `modelo_furgoneta` varchar(100) DEFAULT NULL COMMENT 'Modelo del vehículo (Master, Sprinter, Transit, etc.)',
  `anio_furgoneta` int DEFAULT NULL COMMENT 'Año de fabricación',
  `numero_bastidor_furgoneta` varchar(50) DEFAULT NULL COMMENT 'Número de bastidor/chasis (VIN)',
  `kilometros_entre_revisiones_furgoneta` int unsigned DEFAULT '10000' COMMENT 'Kilómetros entre revisiones preventivas (ej: 10000 km)',
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
  `updated_at_furgoneta` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_furgoneta`),
  UNIQUE KEY `matricula_furgoneta` (`matricula_furgoneta`),
  KEY `idx_matricula_furgoneta` (`matricula_furgoneta`),
  KEY `idx_estado_furgoneta` (`estado_furgoneta`),
  KEY `idx_activo_furgoneta` (`activo_furgoneta`),
  KEY `idx_fecha_proxima_itv` (`fecha_proxima_itv_furgoneta`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Vehículos de la empresa (furgonetas de transporte)';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `furgoneta`
--

LOCK TABLES `furgoneta` WRITE;
/*!40000 ALTER TABLE `furgoneta` DISABLE KEYS */;
INSERT INTO `furgoneta` VALUES (1,'123-ABC','Renault','Master',2020,NULL,10000,'2026-10-10','2026-10-10','Mapfre','PL-123456',1500.00,12.50,'Gasolina',9.00,'Prueba de nombre de taller',NULL,'operativa','La segunda no entra bien. Tener cuidadosssss',1,'2025-12-23 09:09:11','2025-12-23 18:34:19'),(2,'ABCD - 1254444','Ford','Sprinter',2010,NULL,2000,'2025-12-31','2026-01-09','AXA','POL-1258888888',100.00,12.00,'Diésel',8.50,'García','965262384','operativa','Cuidado con la marcha atrás que no funciona. Jajajajaja ',1,'2025-12-23 11:02:47','2025-12-23 11:35:39'),(3,'SDFSDFSDFSDF','sdfsdfsdfsdf','sdfsdfsdf',2020,'qweewerw53654684erwe',10000,'2026-02-13','2026-02-13','Mapfre',NULL,1515.00,34534.00,'Diesel',34.00,'Prueba de nombre de taller','965262384','taller','Prueba de observaciones',1,'2025-12-23 18:01:18','2025-12-23 18:01:18');
/*!40000 ALTER TABLE `furgoneta` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `furgoneta_mantenimiento`
--

DROP TABLE IF EXISTS `furgoneta_mantenimiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `furgoneta_mantenimiento` (
  `id_mantenimiento` int unsigned NOT NULL AUTO_INCREMENT,
  `id_furgoneta` int unsigned NOT NULL COMMENT 'Furgoneta a la que pertenece este mantenimiento',
  `fecha_mantenimiento` date NOT NULL COMMENT 'Fecha en que se realizó el mantenimiento',
  `tipo_mantenimiento` enum('revision','reparacion','itv','neumaticos','otros') NOT NULL COMMENT 'Tipo de mantenimiento realizado',
  `descripcion_mantenimiento` text NOT NULL COMMENT 'Descripción detallada del trabajo realizado',
  `kilometraje_mantenimiento` int unsigned DEFAULT NULL COMMENT 'Kilometraje del vehículo en el momento del mantenimiento',
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
  `updated_at_mantenimiento` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_mantenimiento`),
  KEY `idx_id_furgoneta_mantenimiento` (`id_furgoneta`),
  KEY `idx_fecha_mantenimiento` (`fecha_mantenimiento`),
  KEY `idx_tipo_mantenimiento` (`tipo_mantenimiento`),
  KEY `idx_activo_mantenimiento` (`activo_mantenimiento`),
  CONSTRAINT `fk_mantenimiento_furgoneta` FOREIGN KEY (`id_furgoneta`) REFERENCES `furgoneta` (`id_furgoneta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Historial de mantenimientos y reparaciones de furgonetas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `furgoneta_mantenimiento`
--

LOCK TABLES `furgoneta_mantenimiento` WRITE;
/*!40000 ALTER TABLE `furgoneta_mantenimiento` DISABLE KEYS */;
INSERT INTO `furgoneta_mantenimiento` VALUES (1,1,'2025-12-23','revision','Revisión rutinaria por KM',12500,1200.00,NULL,'Taller Hermanos GarcíA',NULL,NULL,NULL,NULL,'2026-01-28',NULL,1,'2025-12-24 09:01:05','2025-12-24 11:10:54');
/*!40000 ALTER TABLE `furgoneta_mantenimiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `furgoneta_registro_kilometraje`
--

DROP TABLE IF EXISTS `furgoneta_registro_kilometraje`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `furgoneta_registro_kilometraje` (
  `id_registro_km` int unsigned NOT NULL AUTO_INCREMENT,
  `id_furgoneta` int unsigned NOT NULL COMMENT 'Furgoneta a la que pertenece este registro',
  `fecha_registro_km` date NOT NULL COMMENT 'Fecha en que se realizó la lectura',
  `kilometraje_registrado_km` int unsigned NOT NULL COMMENT 'Kilometraje leído en el cuentakilómetros',
  `tipo_registro_km` enum('manual','revision','itv','evento') DEFAULT 'manual' COMMENT 'Origen del registro: manual, revisión, ITV, evento',
  `observaciones_registro_km` text COMMENT 'Observaciones sobre este registro específico',
  `created_at_registro_km` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_registro_km` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_registro_km`),
  KEY `idx_id_furgoneta_registro` (`id_furgoneta`),
  KEY `idx_fecha_registro_km` (`fecha_registro_km`),
  KEY `idx_tipo_registro_km` (`tipo_registro_km`),
  CONSTRAINT `fk_registro_km_furgoneta` FOREIGN KEY (`id_furgoneta`) REFERENCES `furgoneta` (`id_furgoneta`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Registro histórico de kilometraje de furgonetas';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `furgoneta_registro_kilometraje`
--

LOCK TABLES `furgoneta_registro_kilometraje` WRITE;
/*!40000 ALTER TABLE `furgoneta_registro_kilometraje` DISABLE KEYS */;
INSERT INTO `furgoneta_registro_kilometraje` VALUES (1,1,'2026-01-01',100,'revision','Prueba de observaciones','2026-01-03 17:33:16','2026-01-03 18:51:09'),(2,1,'2026-01-02',150,'revision',NULL,'2026-01-03 18:18:10','2026-01-03 18:51:14'),(3,1,'2026-01-03',160,'manual',NULL,'2026-01-03 18:42:10','2026-01-03 18:42:10');
/*!40000 ALTER TABLE `furgoneta_registro_kilometraje` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `grupo_articulo`
--

DROP TABLE IF EXISTS `grupo_articulo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupo_articulo` (
  `id_grupo` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_grupo` varchar(20) NOT NULL,
  `nombre_grupo` varchar(100) NOT NULL,
  `descripcion_grupo` varchar(255) DEFAULT NULL,
  `observaciones_grupo` text,
  `activo_grupo` tinyint(1) DEFAULT '1',
  `created_at_grupo` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_grupo` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_grupo`),
  UNIQUE KEY `codigo_grupo` (`codigo_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupo_articulo`
--

LOCK TABLES `grupo_articulo` WRITE;
/*!40000 ALTER TABLE `grupo_articulo` DISABLE KEYS */;
INSERT INTO `grupo_articulo` VALUES (2,'AUD','Audio','Equipos de sonido, megafonía y amplificación','Incluye micrófonos, consolas, altavoces, procesadores de audio, amplificadores y todo el equipamiento relacionado con la captación, procesamiento y reproducción de sonido profesional.',1,'2025-11-20 20:26:48','2025-11-20 20:26:48'),(3,'VID','Video','Equipos de proyección, pantallas y visualización','Incluye proyectores, pantallas LED, monitores, procesadores de video, cámaras, sistemas de videoconferencia y todo el equipamiento relacionado con imagen y video.',1,'2025-11-20 20:26:48','2025-11-20 20:26:48'),(4,'ILU','Iluminación','Equipos de iluminación escénica y arquitectónica','Incluye focos PAR, moving heads, scanners, strobes, controladores DMX, dimmers y todo tipo de iluminación profesional para eventos y espectáculos.',1,'2025-11-20 20:26:48','2025-11-20 20:26:48'),(5,'EST','Estructuras','Truss, torres, escenarios y rigging','Incluye estructuras de aluminio (truss), torres de elevación, motores chain hoist, sistemas de rigging, escenarios modulares y todos los elementos estructurales certificados.',1,'2025-11-20 20:26:48','2025-11-20 20:26:48'),(6,'ACC','Accesorios','Cables, conectores, adaptadores y consumibles','Incluye todo tipo de cableado (audio, video, datos, alimentación), conectores, adaptadores, regletas, cajas de señal, soportes y material auxiliar.',1,'2025-11-20 20:26:48','2025-11-20 20:26:48'),(7,'COM','Comunicaciones','Intercomunicadores y sistemas de coordinación','Incluye sistemas de intercom, walkie-talkies, sistemas de IFB, auriculares de comunicación y todo el equipamiento para coordinación técnica durante eventos.',1,'2025-11-20 20:26:48','2025-11-20 20:26:48'),(8,'ELE','Eléctrico','Distribución eléctrica y cableado de potencia','Incluye cuadros de distribución, cables de potencia, regletas industriales, grupos electrógenos, transformadores y todo el material eléctrico certificado.',1,'2025-11-20 20:26:48','2025-11-20 20:26:48'),(9,'MOB','Mobiliario','Sillas, mesas, vallas y elementos de evento','Incluye mobiliario para eventos, vallas de seguridad, moquetas, tarimas, atriles, stands y elementos decorativos o funcionales para eventos.',1,'2025-11-20 20:26:48','2025-11-20 20:26:48');
/*!40000 ALTER TABLE `grupo_articulo` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `impuesto`
--

DROP TABLE IF EXISTS `impuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `impuesto` (
  `id_impuesto` int NOT NULL AUTO_INCREMENT,
  `tipo_impuesto` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `tasa_impuesto` decimal(5,2) NOT NULL,
  `descr_impuesto` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_impuesto` tinyint(1) DEFAULT '1',
  `coeficiente_familia` tinyint DEFAULT NULL COMMENT '1 = SI APLICA, 0 = NO APLICA COEFICIENTE DE DESCUANTO',
  `created_at_impuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_impuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_impuesto`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impuesto`
--

LOCK TABLES `impuesto` WRITE;
/*!40000 ALTER TABLE `impuesto` DISABLE KEYS */;
INSERT INTO `impuesto` VALUES (1,'IVA',21.00,'Es la tasa del IVA normal',1,NULL,'2025-11-10 07:11:38','2025-11-21 06:14:54'),(2,'IVA_REDUCIDO',10.00,'Iva reducido aplicable en España',1,NULL,'2025-11-11 17:07:29','2025-11-21 06:15:49'),(3,'SIN_IVA',0.00,'Aplicable a empresas intracomunitarias',1,NULL,'2025-11-21 06:16:24','2025-11-21 06:16:24');
/*!40000 ALTER TABLE `impuesto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `kit`
--

DROP TABLE IF EXISTS `kit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `kit` (
  `id_kit` int unsigned NOT NULL AUTO_INCREMENT,
  `cantidad_kit` int unsigned NOT NULL DEFAULT '1' COMMENT 'Cantidad del artículo componente en el kit',
  `id_articulo_maestro` int unsigned NOT NULL COMMENT 'Artículo principal (el KIT)',
  `id_articulo_componente` int unsigned NOT NULL COMMENT 'Artículo que forma parte del kit',
  `activo_kit` tinyint(1) NOT NULL DEFAULT '1',
  `created_at_kit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_kit` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_kit`),
  UNIQUE KEY `uk_kit_componente` (`id_articulo_maestro`,`id_articulo_componente`),
  KEY `idx_id_articulo_maestro` (`id_articulo_maestro`),
  KEY `idx_id_articulo_componente` (`id_articulo_componente`),
  CONSTRAINT `fk_kit_articulo_componente` FOREIGN KEY (`id_articulo_componente`) REFERENCES `articulo` (`id_articulo`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_kit_articulo_maestro` FOREIGN KEY (`id_articulo_maestro`) REFERENCES `articulo` (`id_articulo`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Composición de artículos tipo KIT';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `kit`
--

LOCK TABLES `kit` WRITE;
/*!40000 ALTER TABLE `kit` DISABLE KEYS */;
INSERT INTO `kit` VALUES (2,3,22,24,1,'2026-01-03 13:00:08','2026-01-03 13:01:04'),(4,10,21,24,1,'2026-01-03 16:30:29','2026-01-03 16:30:52'),(5,5,26,25,1,'2026-01-03 16:31:23','2026-01-03 16:31:23'),(6,3,22,23,1,'2026-01-15 09:07:27','2026-01-15 09:07:27');
/*!40000 ALTER TABLE `kit` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_kit_before_insert` BEFORE INSERT ON `kit` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_kit_before_update` BEFORE UPDATE ON `kit` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `linea_presupuesto`
--

DROP TABLE IF EXISTS `linea_presupuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `linea_presupuesto` (
  `id_linea_ppto` int unsigned NOT NULL AUTO_INCREMENT,
  `id_version_presupuesto` int unsigned NOT NULL COMMENT 'FK: Versión del presupuesto a la que pertenece esta línea',
  `id_articulo` int unsigned DEFAULT NULL COMMENT 'FK: Artículo original (NULL para líneas tipo texto/sección)',
  `id_linea_padre` int unsigned DEFAULT NULL COMMENT 'FK: Línea padre para componentes de KIT (NULL si es línea principal)',
  `id_ubicacion` int unsigned DEFAULT NULL COMMENT 'FK: Ubicación específica de montaje',
  `id_coeficiente` int unsigned DEFAULT NULL COMMENT 'FK: Coeficiente reductor aplicado',
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
  `mostrar_obs_articulo_linea_ppto` tinyint(1) DEFAULT '1' COMMENT 'Si mostrar las observaciones del artículo original',
  `ocultar_detalle_kit_linea_ppto` tinyint(1) DEFAULT '0' COMMENT 'TRUE: no mostrar desglose del KIT | FALSE: mostrar componentes',
  `mostrar_en_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si se muestra al cliente en el presupuesto',
  `es_opcional` tinyint(1) DEFAULT '0' COMMENT 'Si es una línea opcional',
  `activo_linea_ppto` tinyint(1) DEFAULT '1' COMMENT 'Estado activo/inactivo',
  `created_at_linea_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creación',
  `updated_at_linea_ppto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de última actualización',
  PRIMARY KEY (`id_linea_ppto`),
  KEY `fk_linea_ppto_coeficiente` (`id_coeficiente`),
  KEY `idx_id_version_presupuesto_linea` (`id_version_presupuesto`),
  KEY `idx_id_articulo_linea` (`id_articulo`),
  KEY `idx_orden_linea_ppto` (`orden_linea_ppto`),
  KEY `idx_tipo_linea` (`tipo_linea_ppto`),
  KEY `idx_linea_padre` (`id_linea_padre`),
  KEY `idx_fecha_montaje` (`fecha_montaje_linea_ppto`),
  KEY `idx_fecha_inicio` (`fecha_inicio_linea_ppto`),
  KEY `idx_ubicacion` (`id_ubicacion`),
  KEY `idx_impuesto` (`id_impuesto`),
  KEY `idx_activo` (`activo_linea_ppto`),
  CONSTRAINT `fk_linea_ppto_articulo` FOREIGN KEY (`id_articulo`) REFERENCES `articulo` (`id_articulo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_linea_ppto_coeficiente` FOREIGN KEY (`id_coeficiente`) REFERENCES `coeficiente` (`id_coeficiente`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_linea_ppto_impuesto` FOREIGN KEY (`id_impuesto`) REFERENCES `impuesto` (`id_impuesto`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_linea_ppto_linea_padre` FOREIGN KEY (`id_linea_padre`) REFERENCES `linea_presupuesto` (`id_linea_ppto`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_linea_ppto_ubicacion` FOREIGN KEY (`id_ubicacion`) REFERENCES `cliente_ubicacion` (`id_ubicacion`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_linea_ppto_version` FOREIGN KEY (`id_version_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish2_ci COMMENT='Líneas de detalle de versiones de presupuesto con soporte para KITs jerárquicos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `linea_presupuesto`
--

LOCK TABLES `linea_presupuesto` WRITE;
/*!40000 ALTER TABLE `linea_presupuesto` DISABLE KEYS */;
INSERT INTO `linea_presupuesto` VALUES (11,3,21,NULL,1,NULL,NULL,1,'articulo',0,0,'MIC-INAL-001','Micrófono inalámbrico','2026-02-01','2026-02-10','2026-02-01','2026-02-14',5.00,25.00,15.00,1,13.25,14,21.00,'Prueba de observaciones',1,0,1,0,1,'2026-01-22 18:01:23','2026-01-23 10:52:48'),(12,3,22,NULL,2,NULL,NULL,1,'articulo',0,0,'KIT-ILU-BASIC','Kit iluminación básica (4 PAR LED + trípodes)','2026-02-01','2026-02-10','2026-02-01','2026-02-15',10.00,120.00,10.00,1,12.00,15,21.00,'Prueba de observaciones pie 2',1,1,1,0,1,'2026-01-22 18:03:09','2026-01-23 10:00:26'),(13,3,24,NULL,1,NULL,NULL,1,'articulo',0,0,'CABLE-XLR-10M','Cable XLR 10 metros','2026-02-01','2026-02-10','2026-02-01','2026-02-10',1.00,3.80,0.00,0,NULL,NULL,21.00,NULL,1,0,1,0,1,'2026-01-22 20:52:17','2026-01-23 10:00:30'),(14,3,23,NULL,1,NULL,NULL,2,'articulo',0,1,'MIX-DIG-X32','Consola digital Behringer X32','2026-02-01','2026-02-10','2026-02-01','2026-02-10',1.00,180.00,0.00,0,NULL,NULL,21.00,NULL,1,0,1,0,1,'2026-01-23 10:04:52','2026-01-23 11:10:19');
/*!40000 ALTER TABLE `linea_presupuesto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_linea_presupuesto_before_update` BEFORE UPDATE ON `linea_presupuesto` FOR EACH ROW BEGIN
    DECLARE estado_version VARCHAR(20);
    
    -- Obtener estado de la versión a la que pertenece esta línea
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = NEW.id_version_presupuesto;
    
    -- Bloquear si no es borrador
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden modificar líneas de versiones que no están en borrador. Para hacer cambios, cree una nueva versión.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_linea_presupuesto_before_delete` BEFORE DELETE ON `linea_presupuesto` FOR EACH ROW BEGIN
    DECLARE estado_version VARCHAR(20);
    
    -- Obtener estado de la versión
    SELECT estado_version_presupuesto INTO estado_version
    FROM presupuesto_version
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si no es borrador
    IF estado_version != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar líneas de versiones que no están en borrador. El histórico debe permanecer inmutable.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `llamadas`
--

DROP TABLE IF EXISTS `llamadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `llamadas` (
  `id_llamada` int NOT NULL AUTO_INCREMENT,
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
  `activo_llamada` tinyint DEFAULT '1',
  PRIMARY KEY (`id_llamada`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llamadas`
--

LOCK TABLES `llamadas` WRITE;
/*!40000 ALTER TABLE `llamadas` DISABLE KEYS */;
INSERT INTO `llamadas` VALUES (1,1,'María García','Calle Sant Vicent, 45, Alzira, Valencia','932345678','622345678','mariagarcia@email.com','2023-11-16 16:30:00','Reclamación sobre factura pendiente.',12,3,'2025-06-10 12:48:00',0),(2,1,'Manolo Gómez Colomer','Calle 45, Valencia, Manises','645121242','645121268','manolo@email.com','2025-05-21 14:21:00','<blockquote data-start=\"128\" data-end=\"722\"><p data-start=\"130\" data-end=\"722\">Se realizó contacto con el Sr. Gómez desde su primera llamada el 20/06, en la cual solicitó información para la instalación de un toldo retráctil en su patio. Se agendó una visita técnica el 15/06, donde se tomaron medidas y se le ofrecieron varias opciones de lona y mecanismos. El 20/06 se le envió la cotización formal por correo electrónico. El cliente solicitó ajustes en el presupuesto (cambio de lona a una de mayor resistencia), lo cual se actualizó y se reenviaron los documentos el 26/06. El 28/06 confirmó aceptación del presupuesto y se agendó la instalación para el 02/07.</p>\r\n</blockquote>',27,3,'2025-06-15 12:08:00',1),(4,1,'Luis Rodríguez','Calle José María Haro, 12, Quart de Poblet, Valencia','645787845','645787874','luisrod@gmail.com',NULL,'<p>Desde el primer contacto, Luis mostró interés en la instalación de un toldo para su terraza. Se le presentaron distintas opciones, destacando el modelo de brazos extensibles con lona acrílica. A lo largo de varias conversaciones, planteó dudas sobre el color de la lona, la resistencia al viento, y finalmente se interesó por incorporar un motor con mando a distancia. Se envió presupuesto actualizado incluyendo instalación y automatización. Tras evaluar todo, Luis confirmó su decisión y se agendó visita técnica para toma de medidas. Actualmente, el proceso está pendiente de la confirmación definitiva del presupuesto o programación de la instalación.</p>',1,3,'2025-06-09 00:00:00',0),(6,1,'Juan Pérez','Calle Mayor, 12, Xirivella, Valencia','645215487','645215454','juanperez@gmail.com','2025-04-10 00:00:00','El cliente realizó el primer contacto el 17/04 solicitando información sobre un toldo tipo brazo invisible para su balcón. Se coordinó una visita técnica para el 18/04, en la cual se tomaron medidas y se recomendaron opciones según el espacio disponible. Posteriormente, se envió la cotización vía WhatsApp el 18/04, incluyendo dos alternativas de lona y sistema de apertura motorizado. El cliente solicitó unos días para evaluar la propuesta con su familia. El 20/04 confirmó la aceptación del presupuesto y se programó la instalación para el 23/04.yyyyyy',4,2,'2025-06-17 13:08:00',1),(10,1,'Alejandro Blasco','Calle de la Paz, 12 Valencia, Valencia','686734132','686734186','alejandroblasco@gmail.com',NULL,'<p data-start=\"87\" data-end=\"627\">Durante la llamada con Alejandro, se revisaron las necesidades actuales del cliente y se aclararon varios puntos sobre los servicios ofrecidos. Alejandro manifestó interés en actualizar su plan y solicitó información adicional sobre promociones vigentes. Se le proporcionaron detalles sobre costos y tiempos de instalación. Quedó pendiente agendar una visita técnica para evaluación in situ y resolver dudas específicas. La comunicación fue cordial y Alejandro mostró disposición para continuar con el proceso.</p>',9,3,'2025-06-18 13:08:00',1),(14,2,'Tomás Jiménez','Carrer del Baró, 10 València, Patraix','614854514','614854532','tomasjim@gmail.com','2025-05-20 16:37:00','<p data-start=\"86\" data-end=\"724\">Se inicia una nueva llamada con Tomás, interesado en la instalación de toldos para su vivienda. Durante el primer contacto, se recogen sus principales necesidades, destacando la búsqueda de soluciones resistentes y fáciles de manejar. Tomás solicita información sobre distintos tipos de toldos, especialmente aquellos con motorización y sensores de viento. Se acuerda realizar una visita técnica para evaluar las medidas y el tipo de instalación más adecuada. El seguimiento incluirá la presentación de presupuestos personalizados y la resolución de dudas sobre los materiales y opciones disponibles.</p>',19,2,'2025-07-17 00:00:00',0),(16,3,'Lorena López','Calle 123, Valencia, Burjassot','623215451','623215474','lorenalopez@gmail.com','2025-05-24 14:21:00','El cliente se comunicó para solicitar información sobre un toldo para su terraza. Mencionó que busca un modelo retráctil, resistente al sol y la lluvia, de aproximadamente 4 metros de largo. Se le explicó el tipo de materiales disponibles (lona acrílica y PVC), opciones de estructura, colores y sistemas de apertura (manual y motorizado). Se le ofreció una visita técnica sin costo para tomar medidas y evaluar el espacio. El cliente mostró interés y quedó en confirmar la fecha para la visita en las próximas 48 horas. Se registraron sus datos de contacto y ubicación.',16,2,'2025-07-11 18:56:00',1),(19,3,'Javier Díaz','12 Calle de la Paz, Valencia, Burjassot','662651515','662651535','javierdiaz@gmail.com','2025-05-23 13:41:00','Javier contactó interesado en la instalación de toldos para su jardín, buscando opciones que combinen funcionalidad y diseño. Durante el primer contacto, explicó que prioriza la resistencia al viento y la facilidad de mantenimiento. Se le informó sobre distintos tipos de tejidos y mecanismos, incluyendo toldos motorizados y manuales. Javier solicitó un presupuesto detallado con opciones de colores y materiales para evaluar. Se coordinó una visita para tomar medidas y ofrecer una propuesta personalizada. Queda pendiente el envío del presupuesto y resolver dudas sobre garantías y tiempos de instalación.',12,2,'2025-07-20 10:55:00',1),(20,3,'Luis Rodríguez','Calle de Colón 45, Manises, Valencia','645787511','645787574','luisrod@gmail.com','2025-05-16 14:11:00','<p>Se inicia una nueva llamada con Luis a raíz del interés en renovar varios toldos en su vivienda. En el primer contacto se toma nota de sus necesidades generales, destacando la preocupación por la protección solar y la estética. Se coordina una primera visita para la semana siguiente, con el objetivo de tomar medidas y valorar las mejores opciones en función de la orientación de la fachada. Luis solicita que se le presenten varias alternativas de lona, incluyendo tejidos técnicos. El seguimiento de esta llamada incluirá la presentación del presupuesto y resolución de dudas sobre motorización y sensores de viento.</p>',19,3,'2025-07-09 09:21:00',1),(21,2,'Tomás Hernández','Calle Mayor, 45, Quart de Poblet, Valencia','678778756','678778773','tomasher@email.com','2025-05-15 17:27:00','<p>Tomás contacta por primera vez interesado en instalar un toldo vertical para cerrar parcialmente una galería exterior. Explica que busca una solución que reduzca el calor directo pero sin perder completamente la entrada de luz. Durante la conversación, plantea la posibilidad de combinar el toldo con guías laterales o sistema tipo screen enrollable. Se agenda una visita técnica para evaluar la viabilidad según dimensiones y orientación. Tomás solicita también opciones de automatización con sensor solar y de viento. El seguimiento de esta llamada incluirá el envío de propuestas con distintos modelos y precios, así como una comparativa entre sistemas manuales y motorizados.</p>',2,2,'2025-07-14 17:27:00',1),(22,3,'Teresa Alarcón','Gran Vía Marqués del Turia, 42, Valencia','645485781','645485724','teresalar@gmail.com','2025-05-21 13:16:00','<p>Se inicia una nueva llamada con Teresa, interesada en la instalación de toldos para su vivienda. Durante el primer contacto, Teresa explicó sus necesidades principales, haciendo énfasis en la durabilidad y diseño de los toldos. Se acordó realizar una visita técnica para evaluar las dimensiones exactas y discutir opciones de motorización y tejidos. Se comprometió a revisar las propuestas que se le enviarán y a resolver cualquier duda en próximas comunicaciones. El seguimiento se centrará en la presentación de presupuestos personalizados y en facilitar asesoramiento técnico para asegurar la satisfacción total del cliente.</p>',2,3,'2025-07-15 13:18:00',1),(23,3,'Alejandro Montero','Carrer Major, 45, Valencia, Paiporta','635356844','635356814','alejandromont@gmail.com','2025-05-17 16:33:00','<p>Alejandro mostró interés en instalar toldos automáticos en su vivienda para mejorar la comodidad y protección solar. Durante el primer contacto, se identificaron sus necesidades específicas respecto al tipo de tejido y al sistema de apertura. Se acordó una visita técnica para evaluar las dimensiones y condiciones de instalación. Alejandro solicitó información detallada sobre opciones de motorización y automatización, así como sobre la garantía y mantenimiento. Se le informó sobre las promociones vigentes y se comprometió a valorar el presupuesto en los próximos días. El seguimiento se centrará en resolver dudas técnicas y confirmar fechas para la instalación.</p>',2,2,'2025-07-15 13:22:00',1),(24,2,'Jorge García','Calle Valencia, 10, Valencia Alaquàs','645864861','645864887','jorgegarcia@gmail.com',NULL,'<p data-start=\"151\" data-end=\"949\">Se ha mantenido contacto frecuente con Jorge para seguimiento del proceso de instalación. Durante las llamadas, se han aclarado dudas sobre el equipo y los servicios incluidos, así como sobre los plazos estimados. Jorge ha manifestado interés en recibir una propuesta personalizada que incluya opciones de financiación. Se ha coordinado la visita técnica inicial y se ha informado al cliente sobre la documentación necesaria. También se ha registrado la preferencia de Jorge por la instalación en horario de tarde. Actualmente, se está a la espera de la confirmación final por parte del cliente para avanzar con la contratación y la instalación. Se recomienda realizar una llamada de seguimiento en los próximos días para confirmar fecha y resolver cualquier inquietud adicional.</p>',2,3,'2025-07-22 13:06:00',1),(26,2,'María García','Avenida Secundaria 45, Barcelona',NULL,NULL,'alej@gmail.com',NULL,'<p><br></p>',2,1,'2025-07-17 09:12:00',1);
/*!40000 ALTER TABLE `llamadas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `llamadas_con_comerciales_y_metodos`
--

DROP TABLE IF EXISTS `llamadas_con_comerciales_y_metodos`;
/*!50001 DROP VIEW IF EXISTS `llamadas_con_comerciales_y_metodos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `llamadas_con_comerciales_y_metodos` AS SELECT 
 1 AS `id_llamada`,
 1 AS `id_metodo`,
 1 AS `nombre_comunicante`,
 1 AS `domicilio_instalacion`,
 1 AS `telefono_fijo`,
 1 AS `telefono_movil`,
 1 AS `email_contacto`,
 1 AS `fecha_hora_preferida`,
 1 AS `observaciones`,
 1 AS `id_comercial_asignado`,
 1 AS `estado`,
 1 AS `fecha_recepcion`,
 1 AS `activo_llamada`,
 1 AS `nombre_comercial`,
 1 AS `nombre_metodo`,
 1 AS `imagen_metodo`,
 1 AS `descripcion_estado`,
 1 AS `archivos_adjuntos`,
 1 AS `tiene_contactos`,
 1 AS `estado_es_3`,
 1 AS `tiene_adjuntos`,
 1 AS `fecha_primer_contacto`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `marca`
--

DROP TABLE IF EXISTS `marca`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `marca` (
  `id_marca` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_marca` varchar(20) NOT NULL,
  `nombre_marca` varchar(100) NOT NULL,
  `name_marca` varchar(100) NOT NULL COMMENT 'nombre en inglés',
  `descr_marca` varchar(255) DEFAULT NULL,
  `activo_marca` tinyint(1) DEFAULT '1',
  `created_at_marca` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_marca` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_marca`),
  UNIQUE KEY `codigo_marca` (`codigo_marca`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marca`
--

LOCK TABLES `marca` WRITE;
/*!40000 ALTER TABLE `marca` DISABLE KEYS */;
INSERT INTO `marca` VALUES (1,'SAMS','Samsung','Samsung (en)','Tecnología innovadora y diseño de vanguardia. Suficiente para el desarrollo.',1,'2025-10-30 16:42:51','2025-11-05 18:22:50'),(4,'ZYYCA44','Dell','Dell (en)','Computadoras y soluciones tecnológicas innovadoras. XXXXXX',1,'2025-10-30 17:47:32','2025-10-31 16:18:10'),(7,'HP0011','Hewlett Packard','HP (en) - English','Prueba de detalles en marcas (es).',1,'2025-10-31 16:41:12','2025-10-31 16:41:41'),(8,'NINT','Nintendo','Nintendo','Nintendo es una empresa multinacional japonesa de entretenimiento, originaria de 1889 como fabricante de naipes y ahora dedicada al desarrollo y distribución de consolas y videojuegos.',1,'2025-11-05 18:22:06','2025-11-05 18:22:06'),(9,'PHLED','Philips Lumileds','Philips Lumileds','Philips Lumileds es un fabricante líder de LEDs de alto rendimiento, reconocido por su eficiencia, durabilidad y calidad lumínica en aplicaciones profesionales y automotrices.',1,'2025-12-02 15:56:18','2025-12-02 15:56:18');
/*!40000 ALTER TABLE `marca` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `metodo_forma_pago`
--

DROP TABLE IF EXISTS `metodo_forma_pago`;
/*!50001 DROP VIEW IF EXISTS `metodo_forma_pago`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `metodo_forma_pago` AS SELECT 
 1 AS `codigo_pago`,
 1 AS `nombre_pago`,
 1 AS `nombre_metodo_pago`,
 1 AS `porcentaje_anticipo_pago`,
 1 AS `dias_anticipo_pago`,
 1 AS `porcentaje_final_pago`,
 1 AS `dias_final_pago`,
 1 AS `descuento_pago`,
 1 AS `tipo_pago`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `metodo_pago`
--

DROP TABLE IF EXISTS `metodo_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodo_pago` (
  `id_metodo_pago` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_metodo_pago` varchar(20) NOT NULL,
  `nombre_metodo_pago` varchar(100) NOT NULL,
  `observaciones_metodo_pago` text,
  `activo_metodo_pago` tinyint(1) DEFAULT '1',
  `created_at_metodo_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_metodo_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_metodo_pago`),
  UNIQUE KEY `codigo_metodo_pago` (`codigo_metodo_pago`),
  KEY `idx_codigo_metodo_pago` (`codigo_metodo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodo_pago`
--

LOCK TABLES `metodo_pago` WRITE;
/*!40000 ALTER TABLE `metodo_pago` DISABLE KEYS */;
INSERT INTO `metodo_pago` VALUES (1,'TRANS','Transferencia bancaria','Es una observación de transferencia bancaria.',1,'2025-11-20 07:07:42','2025-11-21 06:05:11'),(2,'TARJ','Tarjeta de crédito/débito',NULL,1,'2025-11-20 07:07:42','2025-11-20 07:07:42'),(3,'EFEC','Efectivo',NULL,1,'2025-11-20 07:07:42','2025-11-20 07:07:42'),(4,'CHEQ','Cheque',NULL,1,'2025-11-20 07:07:42','2025-11-20 07:07:42'),(5,'BIZUM','Bizum',NULL,1,'2025-11-20 07:07:42','2025-11-20 07:07:42'),(6,'PAYPAL','PayPal',NULL,1,'2025-11-20 07:07:42','2025-11-20 07:07:42'),(7,'DOMICIL','Domiciliación bancaria',NULL,1,'2025-11-20 07:07:42','2025-11-20 07:07:42');
/*!40000 ALTER TABLE `metodo_pago` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `metodos_contacto`
--

DROP TABLE IF EXISTS `metodos_contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodos_contacto` (
  `id_metodo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `permite_adjuntos` tinyint DEFAULT '0',
  `estado` tinyint DEFAULT '1',
  `imagen_metodo` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL COMMENT 'Ruta o nombre de la imagen del método de contacto',
  PRIMARY KEY (`id_metodo`),
  UNIQUE KEY `nombre_UNIQUE` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=40 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_contacto`
--

LOCK TABLES `metodos_contacto` WRITE;
/*!40000 ALTER TABLE `metodos_contacto` DISABLE KEYS */;
INSERT INTO `metodos_contacto` VALUES (1,'Correo Electrónico',1,1,'34-Mail Success.png'),(2,'Llamada Telefónica',1,1,'22-Ringing Phone.png'),(3,'WhatsApp Business',0,1,'41-Chat App.png'),(39,'Presencia en tienda',0,1,'35-Support.png');
/*!40000 ALTER TABLE `metodos_contacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `observacion_general`
--

DROP TABLE IF EXISTS `observacion_general`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `observacion_general` (
  `id_obs_general` int unsigned NOT NULL AUTO_INCREMENT,
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
  `updated_at_obs_general` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_obs_general`),
  UNIQUE KEY `codigo_obs_general` (`codigo_obs_general`),
  KEY `idx_orden_obs_general` (`orden_obs_general`),
  KEY `idx_obligatoria_obs_general` (`obligatoria_obs_general`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `observacion_general`
--

LOCK TABLES `observacion_general` WRITE;
/*!40000 ALTER TABLE `observacion_general` DISABLE KEYS */;
INSERT INTO `observacion_general` VALUES (1,'OBS-11','Observación técnica','English title','Es una prueba de observaciones generales','English Observation',1,'tecnicas',1,1,'2025-11-17 10:23:10','2025-11-17 18:00:16'),(2,'TEST-001','Título de prueba','Test title','Texto de prueba en español','Test text in English',1,'otras',1,1,'2025-11-17 17:45:16','2025-11-17 17:45:16');
/*!40000 ALTER TABLE `observacion_general` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `presupuesto`
--

DROP TABLE IF EXISTS `presupuesto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto` (
  `id_presupuesto` int unsigned NOT NULL AUTO_INCREMENT,
  `id_empresa` int unsigned DEFAULT NULL COMMENT 'Empresa que emite el presupuesto (ficticia o real)',
  `numero_presupuesto` varchar(50) NOT NULL,
  `id_cliente` int unsigned NOT NULL,
  `id_contacto_cliente` int unsigned DEFAULT NULL,
  `id_estado_ppto` int unsigned NOT NULL,
  `version_actual_presupuesto` int unsigned DEFAULT '1' COMMENT 'Número de versión activa actual (la que se muestra/edita)',
  `estado_general_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado') DEFAULT 'borrador' COMMENT 'Estado general del presupuesto (sincronizado con versión actual)',
  `id_forma_pago` int unsigned DEFAULT NULL,
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
  `mostrar_obs_familias_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, muestra observaciones de las familias usadas',
  `mostrar_obs_articulos_presupuesto` tinyint(1) DEFAULT '1' COMMENT 'Si TRUE, muestra observaciones de los artículos usados',
  `observaciones_internas_presupuesto` text COMMENT 'Notas internas, no se imprimen en el PDF',
  `activo_presupuesto` tinyint(1) DEFAULT '1',
  `created_at_presupuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_presupuesto` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_presupuesto`),
  UNIQUE KEY `numero_presupuesto` (`numero_presupuesto`),
  KEY `fk_presupuesto_contacto` (`id_contacto_cliente`),
  KEY `fk_presupuesto_forma_pago` (`id_forma_pago`),
  KEY `fk_presupuesto_metodo_contacto` (`id_metodo`),
  KEY `idx_numero_presupuesto` (`numero_presupuesto`),
  KEY `idx_id_cliente_presupuesto` (`id_cliente`),
  KEY `idx_id_estado_presupuesto` (`id_estado_ppto`),
  KEY `idx_fecha_presupuesto` (`fecha_presupuesto`),
  KEY `idx_fecha_inicio_evento` (`fecha_inicio_evento_presupuesto`),
  KEY `idx_fecha_fin_evento` (`fecha_fin_evento_presupuesto`),
  KEY `idx_numero_pedido_cliente` (`numero_pedido_cliente_presupuesto`),
  KEY `idx_poblacion_evento` (`poblacion_evento_presupuesto`),
  KEY `idx_provincia_evento` (`provincia_evento_presupuesto`),
  KEY `idx_aplicar_coeficientes_presupuesto` (`aplicar_coeficientes_presupuesto`),
  KEY `idx_descuento_presupuesto` (`descuento_presupuesto`),
  KEY `idx_version_actual_presupuesto` (`version_actual_presupuesto`),
  KEY `idx_estado_general_presupuesto` (`estado_general_presupuesto`),
  KEY `idx_id_empresa_presupuesto` (`id_empresa`),
  CONSTRAINT `fk_presupuesto_cliente` FOREIGN KEY (`id_cliente`) REFERENCES `cliente` (`id_cliente`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_presupuesto_contacto` FOREIGN KEY (`id_contacto_cliente`) REFERENCES `contacto_cliente` (`id_contacto_cliente`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_presupuesto_empresa` FOREIGN KEY (`id_empresa`) REFERENCES `empresa` (`id_empresa`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_presupuesto_estado` FOREIGN KEY (`id_estado_ppto`) REFERENCES `estado_presupuesto` (`id_estado_ppto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_presupuesto_forma_pago` FOREIGN KEY (`id_forma_pago`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `fk_presupuesto_metodo_contacto` FOREIGN KEY (`id_metodo`) REFERENCES `metodos_contacto` (`id_metodo`) ON DELETE SET NULL ON UPDATE CASCADE,
  CONSTRAINT `chk_descuento_presupuesto` CHECK (((`descuento_presupuesto` >= 0.00) and (`descuento_presupuesto` <= 100.00)))
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci COMMENT='Cabecera de presupuestos para alquiler de equipos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto`
--

LOCK TABLES `presupuesto` WRITE;
/*!40000 ALTER TABLE `presupuesto` DISABLE KEYS */;
INSERT INTO `presupuesto` VALUES (11,NULL,'P-00002/2026',4,6,1,1,'borrador',8,NULL,'2026-01-21','2026-02-20','2026-02-01','2026-02-10','PD-100',1,20.00,'Concierto anual','Calle Rio Amadorio','Alicante','03013','Alicante','','','','',1,1,'',1,'2026-01-21 09:40:39','2026-01-21 09:43:05');
/*!40000 ALTER TABLE `presupuesto` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_before_insert` BEFORE INSERT ON `presupuesto` FOR EACH ROW BEGIN
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
    
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_after_insert` AFTER INSERT ON `presupuesto` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_before_desactivar` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_before_reactivar` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_estado_cancelado` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
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
    
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_estado_no_cancelado` BEFORE UPDATE ON `presupuesto` FOR EACH ROW BEGIN
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
    
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `presupuesto_version`
--

DROP TABLE IF EXISTS `presupuesto_version`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `presupuesto_version` (
  `id_version_presupuesto` int unsigned NOT NULL AUTO_INCREMENT COMMENT '? ID único de TABLA (AUTO_INCREMENT). NO confundir con numero_version_presupuesto. \r\n            Ejemplo: Si tienes 3 presupuestos con 2 versiones cada uno, tendrás IDs 1-6,\r\n            pero cada presupuesto tendrá sus propias versiones 1 y 2',
  `id_presupuesto` int unsigned NOT NULL COMMENT '? FK a tabla presupuesto (cabecera). \r\n            Indica a qué presupuesto pertenece esta versión.\r\n            Múltiples versiones pueden apuntar al mismo id_presupuesto',
  `numero_version_presupuesto` int unsigned NOT NULL COMMENT '? Número LÓGICO de versión dentro de este presupuesto (1, 2, 3...).\r\n            Es secuencial DENTRO de cada presupuesto.\r\n            Presupuesto A: versiones 1, 2, 3\r\n            Presupuesto B: versiones 1, 2 (independiente de A)\r\n            ⚠️ NO confundir con id_version_presupuesto',
  `version_padre_presupuesto` int unsigned DEFAULT NULL COMMENT '?‍? ID de la versión anterior (genealogía).\r\n            NULL = Versión original (primera)\r\n            Si tiene valor = ID de la versión desde la cual se creó esta\r\n            Ejemplo: Versión 3 creada desde versión 2 → version_padre = id de versión 2\r\n            Permite rastrear el árbol de cambios',
  `estado_version_presupuesto` enum('borrador','enviado','aprobado','rechazado','cancelado') CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci NOT NULL DEFAULT 'borrador' COMMENT '? Estado específico de ESTA versión (workflow).\r\n            - borrador: En edición, se pueden modificar líneas\r\n            - enviado: Enviado al cliente, bloqueado para edición\r\n            - aprobado: Cliente aceptó, bloqueado permanentemente\r\n            - rechazado: Cliente rechazó, se puede crear nueva versión\r\n            - cancelado: Versión cancelada, no se usa\r\n            ⚠️ Solo "borrador" permite editar líneas',
  `motivo_modificacion_version` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '? Razón por la que se creó esta versión.\r\n            - Versión 1: "Versión inicial"\r\n            - Versión 2: "Cliente solicitó cambio de precios"\r\n            - Versión 3: "Reducción de equipos por presupuesto"\r\n            Ayuda a entender el historial de cambios',
  `fecha_creacion_version` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '? Fecha y hora de creación de esta versión.\r\n            Se establece automáticamente al crear el registro.\r\n            Útil para auditoría y timeline del presupuesto',
  `creado_por_version` int unsigned NOT NULL COMMENT '? ID del usuario que creó esta versión.\r\n            Permite rastrear responsabilidades.\r\n            TODO: Vincular con tabla usuarios cuando exista\r\n            Actualmente = 1 por defecto',
  `fecha_envio_version` datetime DEFAULT NULL COMMENT '? Fecha y hora de envío al cliente.\r\n            NULL = Aún no enviado\r\n            Se establece automáticamente al cambiar estado a "enviado"\r\n            Marca el momento en que el cliente recibió esta versión',
  `enviado_por_version` int unsigned DEFAULT NULL COMMENT '? ID del usuario que envió esta versión al cliente.\r\n            NULL = No enviado aún\r\n            Permite rastrear quién realizó el envío',
  `fecha_aprobacion_version` datetime DEFAULT NULL COMMENT '✅ Fecha y hora de aprobación del cliente.\r\n            NULL = No aprobado\r\n            Se establece al cambiar estado a "aprobado"\r\n            Importante para facturación y producción',
  `fecha_rechazo_version` datetime DEFAULT NULL COMMENT '❌ Fecha y hora de rechazo del cliente.\r\n            NULL = No rechazado\r\n            Se establece al cambiar estado a "rechazado"\r\n            Indica que se necesita nueva versión',
  `motivo_rechazo_version` text CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci COMMENT '? Razón por la que el cliente rechazó esta versión.\r\n            NULL = No rechazado o no especificado\r\n            Ejemplo: "Precio muy alto", "Faltan equipos"\r\n            Ayuda a crear la siguiente versión correctamente',
  `ruta_pdf_version` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_spanish_ci DEFAULT NULL COMMENT '? Ruta del archivo PDF generado para esta versión.\r\n            NULL = PDF no generado\r\n            Formato: /documentos/presupuestos/P-00001-2026_v1.pdf\r\n            Se genera automáticamente al enviar.\r\n            Mantiene histórico de PDFs enviados',
  `activo_version` tinyint(1) DEFAULT '1' COMMENT '?️ Soft delete: 1=activo, 0=eliminado lógicamente.\r\n            NO usar DELETE físico, cambiar a 0 para "eliminar"\r\n            Mantiene histórico completo en BD',
  `created_at_version` timestamp NULL DEFAULT CURRENT_TIMESTAMP COMMENT '⏱️ Timestamp de creación del registro en BD.\r\n            Auditoría técnica del sistema',
  `updated_at_version` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '⏱️ Timestamp de última actualización del registro.\r\n            Se actualiza automáticamente en cada UPDATE.\r\n            Auditoría técnica del sistema',
  PRIMARY KEY (`id_version_presupuesto`),
  KEY `idx_id_presupuesto_version` (`id_presupuesto`),
  KEY `idx_numero_version` (`numero_version_presupuesto`),
  KEY `idx_version_padre` (`version_padre_presupuesto`),
  KEY `idx_estado_version` (`estado_version_presupuesto`),
  KEY `idx_fecha_creacion_version` (`fecha_creacion_version`),
  KEY `idx_fecha_envio_version` (`fecha_envio_version`),
  KEY `idx_presupuesto_numero_version` (`id_presupuesto`,`numero_version_presupuesto`),
  KEY `idx_creado_por` (`creado_por_version`),
  KEY `idx_enviado_por` (`enviado_por_version`),
  CONSTRAINT `fk_version_padre` FOREIGN KEY (`version_padre_presupuesto`) REFERENCES `presupuesto_version` (`id_version_presupuesto`) ON DELETE RESTRICT ON UPDATE CASCADE,
  CONSTRAINT `fk_version_presupuesto` FOREIGN KEY (`id_presupuesto`) REFERENCES `presupuesto` (`id_presupuesto`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Tabla de control de versiones de presupuestos. Cada registro representa una versión específica con su historial completo de cambios y estados';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `presupuesto_version`
--

LOCK TABLES `presupuesto_version` WRITE;
/*!40000 ALTER TABLE `presupuesto_version` DISABLE KEYS */;
INSERT INTO `presupuesto_version` VALUES (3,11,1,NULL,'borrador','Versión inicial','2026-01-21 09:40:39',1,NULL,NULL,NULL,NULL,NULL,NULL,1,'2026-01-21 09:40:39','2026-01-21 09:40:39');
/*!40000 ALTER TABLE `presupuesto_version` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_version_before_insert_validar` BEFORE INSERT ON `presupuesto_version` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_version_before_insert_numero` BEFORE INSERT ON `presupuesto_version` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_version_validar_transicion_estado` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    -- Solo validar si cambió el estado
    IF OLD.estado_version_presupuesto != NEW.estado_version_presupuesto THEN
        
        -- DESDE BORRADOR: solo puede ir a 'enviado' o 'cancelado'
        IF OLD.estado_version_presupuesto = 'borrador' 
           AND NEW.estado_version_presupuesto NOT IN ('enviado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde borrador solo se puede pasar a enviado o cancelado. Workflow inválido.';
        END IF;
        
        -- DESDE ENVIADO: solo puede ir a 'aprobado', 'rechazado' o 'cancelado'
        IF OLD.estado_version_presupuesto = 'enviado' 
           AND NEW.estado_version_presupuesto NOT IN ('aprobado', 'rechazado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Desde enviado solo se puede pasar a aprobado, rechazado o cancelado. Workflow inválido.';
        END IF;
        
        -- ESTADOS FINALES: 'aprobado' y 'cancelado' no pueden cambiar
        IF OLD.estado_version_presupuesto IN ('aprobado', 'cancelado') THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: No se puede cambiar el estado de versiones aprobadas o canceladas. Son estados finales e inmutables.';
        END IF;
        
        -- DESDE RECHAZADO: solo puede ir a 'cancelado'
        IF OLD.estado_version_presupuesto = 'rechazado' 
           AND NEW.estado_version_presupuesto != 'cancelado' THEN
            SIGNAL SQLSTATE '45000'
            SET MESSAGE_TEXT = 'ERROR: Una versión rechazada solo puede cancelarse. Para nuevos intentos, cree una nueva versión.';
        END IF;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_version_auto_fechas` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_version_auto_ruta_pdf` BEFORE UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
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
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_version_sync_estado_cabecera` AFTER UPDATE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE version_actual INT;
    
    -- Obtener la versión actual del presupuesto
    SELECT version_actual_presupuesto INTO version_actual
    FROM presupuesto
    WHERE id_presupuesto = NEW.id_presupuesto;
    
    -- Si esta es la versión actual, sincronizar estado en la cabecera
    IF NEW.numero_version_presupuesto = version_actual THEN
        UPDATE presupuesto
        SET estado_general_presupuesto = NEW.estado_version_presupuesto
        WHERE id_presupuesto = NEW.id_presupuesto;
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`%`*/ /*!50003 TRIGGER `trg_presupuesto_version_before_delete` BEFORE DELETE ON `presupuesto_version` FOR EACH ROW BEGIN
    DECLARE num_lineas INT;
    DECLARE tiene_hijos INT;
    
    -- Contar líneas asociadas a esta versión
    SELECT COUNT(*) INTO num_lineas
    FROM linea_presupuesto
    WHERE id_version_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si tiene líneas
    IF num_lineas > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene líneas asociadas. Elimine primero las líneas.';
    END IF;
    
    -- Contar versiones hijas (que tienen esta como padre)
    SELECT COUNT(*) INTO tiene_hijos
    FROM presupuesto_version
    WHERE version_padre_presupuesto = OLD.id_version_presupuesto;
    
    -- Bloquear si tiene versiones hijas
    IF tiene_hijos > 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se puede eliminar una versión que tiene versiones hijas. Esto rompería la cadena genealógica.';
    END IF;
    
    -- Bloquear si no está en borrador (versiones enviadas/aprobadas/rechazadas deben permanecer)
    IF OLD.estado_version_presupuesto != 'borrador' THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'ERROR: No se pueden eliminar versiones que no están en borrador. El histórico debe ser inmutable.';
    END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `proveedor`
--

DROP TABLE IF EXISTS `proveedor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `proveedor` (
  `id_proveedor` int unsigned NOT NULL AUTO_INCREMENT,
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
  `id_forma_pago_habitual` int unsigned DEFAULT NULL COMMENT 'Forma de pago habitual del proveedor. Se usará por defecto en nuevas órdenes de compra',
  `observaciones_proveedor` text,
  `activo_proveedor` tinyint(1) DEFAULT '1',
  `created_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `codigo_proveedor` (`codigo_proveedor`),
  KEY `idx_codigo_proveedor` (`codigo_proveedor`),
  KEY `idx_nombre_proveedor` (`nombre_proveedor`),
  KEY `idx_nif_proveedor` (`nif_proveedor`),
  KEY `idx_id_forma_pago_habitual` (`id_forma_pago_habitual`),
  CONSTRAINT `fk_proveedor_forma_pago_habitual` FOREIGN KEY (`id_forma_pago_habitual`) REFERENCES `forma_pago` (`id_pago`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedor`
--

LOCK TABLES `proveedor` WRITE;
/*!40000 ALTER TABLE `proveedor` DISABLE KEYS */;
INSERT INTO `proveedor` VALUES (1,'PRUEBA001','Prueba','','','','','12342422N','','','','manuelefe@gmail.com','','C/ rio Amadorio, 4','03013','La Pobla de Farnals','Valencia','99300923','4522666987','luis@gmail.com',14,'Prueba de observaciones',1,'2025-11-15 12:00:50','2025-12-09 12:32:09'),(2,'PRUEBA003','Prueba 002','','','','','','','','','','','','','','','','','',NULL,'',1,'2025-11-15 12:09:40','2025-11-18 19:02:28');
/*!40000 ALTER TABLE `proveedor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `roles`
--

DROP TABLE IF EXISTS `roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `roles` (
  `id_rol` int NOT NULL AUTO_INCREMENT,
  `nombre_rol` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci NOT NULL,
  `est` tinyint DEFAULT '1' COMMENT 'est = 0 --> Inactivo, est = 1 --> Activo',
  PRIMARY KEY (`id_rol`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='Tabla que contiene los distintos roles de usuario';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `roles`
--

LOCK TABLES `roles` WRITE;
/*!40000 ALTER TABLE `roles` DISABLE KEYS */;
INSERT INTO `roles` VALUES (1,'Empleado',1),(2,'Gestor',1),(3,'Administrador',1),(4,'Comercial',1),(5,'Técnico',1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tipo_documento`
--

DROP TABLE IF EXISTS `tipo_documento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tipo_documento` (
  `id_tipo_documento` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_tipo_documento` varchar(20) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Código alfanumérico único del tipo (ej: SEG, MAN, PROC)',
  `nombre_tipo_documento` varchar(100) COLLATE utf8mb4_spanish_ci NOT NULL COMMENT 'Nombre descriptivo del tipo de documento',
  `descripcion_tipo_documento` text COLLATE utf8mb4_spanish_ci COMMENT 'Descripción detallada del tipo de documento',
  `activo_tipo_documento` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'Estado del registro: 1=activo, 0=inactivo',
  `created_at_tipo_documento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora de creación del registro',
  `updated_at_tipo_documento` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha y hora de última modificación',
  PRIMARY KEY (`id_tipo_documento`),
  UNIQUE KEY `uk_codigo_tipo_documento` (`codigo_tipo_documento`),
  KEY `idx_nombre_tipo_documento` (`nombre_tipo_documento`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci COMMENT='Catálogo de tipos de documentos para el gestor documental de técnicos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tipo_documento`
--

LOCK TABLES `tipo_documento` WRITE;
/*!40000 ALTER TABLE `tipo_documento` DISABLE KEYS */;
INSERT INTO `tipo_documento` VALUES (1,'MAN_USU','Manual para usuarios','Este sistema almacenará todo tipo de documentos con referencia a manuales de usuarios.',1,'2025-12-17 18:29:23','2025-12-17 18:29:39');
/*!40000 ALTER TABLE `tipo_documento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `unidad_medida`
--

DROP TABLE IF EXISTS `unidad_medida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `unidad_medida` (
  `id_unidad` int unsigned NOT NULL AUTO_INCREMENT,
  `nombre_unidad` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `name_unidad` varchar(50) COLLATE utf8mb4_general_ci NOT NULL COMMENT 'Nombre en ingles.',
  `descr_unidad` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `simbolo_unidad` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `activo_unidad` tinyint(1) DEFAULT '1',
  `created_at_unidad` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_unidad` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_unidad`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidad_medida`
--

LOCK TABLES `unidad_medida` WRITE;
/*!40000 ALTER TABLE `unidad_medida` DISABLE KEYS */;
INSERT INTO `unidad_medida` VALUES (1,'Metros','Meters','Unidad de longitud bbbbbbx','m',1,'2025-11-04 18:03:15','2025-12-01 11:11:22'),(2,'Horas','Hour','Unidad de medida de tiempo equivalente a 60 minutos.','H',1,'2025-11-06 19:21:53','2025-11-06 19:22:14'),(4,'Kilometros','Kilometer','Es para el control del desplazameinto','Km',1,'2025-11-07 18:06:23','2025-11-07 18:06:23'),(5,'UNIDAD','Unidades','Es la descripción de unidades','Un',1,'2025-11-21 06:29:55','2025-11-21 06:29:55');
/*!40000 ALTER TABLE `unidad_medida` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` int NOT NULL AUTO_INCREMENT,
  `email` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `contrasena` varchar(255) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `nombre` varchar(60) CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `fecha_crea` datetime DEFAULT CURRENT_TIMESTAMP,
  `est` tinyint DEFAULT NULL COMMENT 'est = 0 --> Inactivo, est =1 --> activo',
  `id_rol` int NOT NULL,
  `tokenUsu` longtext CHARACTER SET utf8mb3 COLLATE utf8mb3_spanish2_ci,
  PRIMARY KEY (`id_usuario`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='Tabla de usuario con contraseñas y roles asociados';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'ale@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alejandro','2025-04-25 09:10:45',1,4,'x8v7c3plq9dtfr1b2a6mjohw5esynuz'),(2,'luis@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Luis','2025-04-25 10:23:12',1,3,'92fmazrxb8pcl5ghvt6wqyonj3sedu'),(3,'jorge@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Jorge','2025-04-25 10:30:21',1,1,'qlynsjo3vg9rc6tepbxahw4zf7dumk'),(4,'hugo@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Hugo','2025-04-25 10:37:15',1,2,'hmr4dwaj5s9p7b6qcznxfvotlyegku'),(5,'alejandrorodriguez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alejandro Rodríguez Martínez','2025-05-15 10:18:39',1,4,'k47bzmou1hsyexrfqvwcdjap9nglt0'),(6,'carloslopez@email.com','4af1f0ef2163e4aa57a300a876c5116e','Carlos López','2025-05-15 10:18:39',1,4,'d53gbtnrmyhvlqex7k0fzaosw9pjic'),(7,'luisfernandez@email.com','0fcc23c449980e35b30c0f77fd125dc5','Luis Fernández López','2025-05-15 10:18:39',1,4,'js2htcqrv89lwxuodkgf5n1abzemyp'),(8,'luciaperez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Lucía Pérez Sánchez','2025-05-15 10:18:39',1,4,'nqgs4wkjco7lyh1zvtrfaepx896dbum'),(9,'raulromero@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Raúl Romero Álvarez','2025-05-15 10:18:39',1,4,'mv94eub6zt7hrojwsnxlcgaqkpyd5fi'),(10,'margaritagarcia@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Margarita García Castro','2025-05-15 10:18:39',1,4,'wy1vpfl39zrhedxbjaknugqmc507sto'),(11,'carmenmartinez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Carmen Martínez González','2025-05-15 10:18:39',1,4,'oabwvrls3zyxmpu9tgqekfdh17cnj5i'),(12,'albertohernandez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alberto Hernández García','2025-05-15 10:18:39',1,4,'zeuhkmdx5p14jgtyrvb9lcawoqnsf78'),(13,'nataliasanchez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Natalia Sánchez García','2025-05-15 10:18:39',1,4,'cbnt1ayqpkj48mfwrxsz7vehgo6dlu9'),(14,'lauraramirez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Laura Ramírez Hernández','2025-05-15 10:18:39',1,4,'rqhlxomciznkv57bfsa4yduw3ptgej8'),(15,'franciscomorenomoya@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Francisco Moreno Moya','2025-05-15 10:18:39',1,4,'yteazwjf1nrhgcbsuvdxkqpm58lo94v'),(16,'beatrizmunoz@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Beatriz Muñoz Vázquez','2025-05-15 10:18:39',1,4,'vk3dqrujwbf5sclm7onhxytz2g9eaip'),(17,'pablomoreno@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Pablo Moreno Sánchez','2025-05-15 10:18:39',1,4,'p6lzexv87mcsyn1htgfaowjrqdkuib9'),(18,'mariatorres@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','María Torres García','2025-05-15 10:18:39',1,4,'gf4n6rctskxepbhmqujz8y1vwlod05a'),(19,'martarodriguez@email.com','0fcc23c449980e35b30c0f77fd125dc5','Marta Rodríguez González','2025-05-15 10:26:12',1,4,'uwg0mspz9bqh16ckafjtynrldove237'),(20,'carloshernandez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Ana Hernández Torres','2025-05-15 10:26:12',1,4,'bsfntmkgp7qhrdwo9viyexczla25uj38'),(21,'migueldiaz@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Miguel Díaz Jiménez','2025-05-15 10:26:13',1,4,'h5frtxw9ek2pimlvcyagjdsobznq47u'),(22,'evamoreno@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Eva Moreno Fernández','2025-05-15 10:26:13',1,4,'nxhtg04f7yqlv1wscprzjomdbkae59u'),(23,'teresavazquez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Teresa Vázquez Suárez','2025-05-15 10:26:13',1,4,'z7mvwg95hrk1nlx68sfcqadotjeupb2'),(24,'sergiolopez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Sergio López Hernández','2025-05-15 10:26:13',1,4,'jl6vzqsbm97x3c1dheogwftkaurynp45'),(25,'alejandrosolvam@gmail.com','fca3d97bb6bbd55138f9af6ac121acda','Alejandro','2025-04-25 10:23:12',1,3,'92fmazrxb8pcl5ghvt6wqyonj3sedu4'),(26,'delafuente@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Laura','2025-07-11 07:31:08',1,2,NULL),(27,'tomasgarc@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Tomás García','2025-07-11 07:45:50',1,1,'lwsdqoud6kk3rm1z61mgtyneckd80m'),(28,'geronimosalinas@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Geronimo Salinas','2025-07-11 10:10:41',1,4,'vbczr73fzf3cvhyg55o8xda0v2hskg'),(29,'admin@gmail.com','cb4b39a466aaef4652df4a10d50fb8d2','Administrador de demostración','2025-09-16 07:16:37',1,3,'6zvo6g8j7r48b53eqg0a8ztv9kmzs4'),(30,'carmen@mdr.com','353885231743fc6a2fa7a6cccee42e43','Carmen','2025-11-19 11:15:48',1,3,'o6kjw9g3at13ul3lyx1sq6wzve1yly');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `usuarios_con_rol`
--

DROP TABLE IF EXISTS `usuarios_con_rol`;
/*!50001 DROP VIEW IF EXISTS `usuarios_con_rol`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `usuarios_con_rol` AS SELECT 
 1 AS `id_usuario`,
 1 AS `nombre`,
 1 AS `email`,
 1 AS `contrasena`,
 1 AS `fecha_crea`,
 1 AS `est`,
 1 AS `id_rol`,
 1 AS `nombre_rol`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_linea_presupuesto_calculada`
--

DROP TABLE IF EXISTS `v_linea_presupuesto_calculada`;
/*!50001 DROP VIEW IF EXISTS `v_linea_presupuesto_calculada`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_linea_presupuesto_calculada` AS SELECT 
 1 AS `id_linea_ppto`,
 1 AS `id_version_presupuesto`,
 1 AS `id_articulo`,
 1 AS `id_linea_padre`,
 1 AS `id_ubicacion`,
 1 AS `numero_linea_ppto`,
 1 AS `tipo_linea_ppto`,
 1 AS `nivel_jerarquia`,
 1 AS `codigo_linea_ppto`,
 1 AS `descripcion_linea_ppto`,
 1 AS `orden_linea_ppto`,
 1 AS `observaciones_linea_ppto`,
 1 AS `mostrar_obs_articulo_linea_ppto`,
 1 AS `ocultar_detalle_kit_linea_ppto`,
 1 AS `mostrar_en_presupuesto`,
 1 AS `es_opcional`,
 1 AS `activo_linea_ppto`,
 1 AS `fecha_montaje_linea_ppto`,
 1 AS `fecha_desmontaje_linea_ppto`,
 1 AS `fecha_inicio_linea_ppto`,
 1 AS `fecha_fin_linea_ppto`,
 1 AS `cantidad_linea_ppto`,
 1 AS `precio_unitario_linea_ppto`,
 1 AS `descuento_linea_ppto`,
 1 AS `porcentaje_iva_linea_ppto`,
 1 AS `jornadas_linea_ppto`,
 1 AS `id_coeficiente`,
 1 AS `aplicar_coeficiente_linea_ppto`,
 1 AS `valor_coeficiente_linea_ppto`,
 1 AS `jornadas_coeficiente`,
 1 AS `valor_coeficiente`,
 1 AS `observaciones_coeficiente`,
 1 AS `activo_coeficiente`,
 1 AS `dias_evento`,
 1 AS `subtotal_sin_coeficiente`,
 1 AS `base_imponible`,
 1 AS `importe_iva`,
 1 AS `total_linea`,
 1 AS `codigo_articulo`,
 1 AS `nombre_articulo`,
 1 AS `name_articulo`,
 1 AS `imagen_articulo`,
 1 AS `precio_alquiler_articulo`,
 1 AS `es_kit_articulo`,
 1 AS `control_total_articulo`,
 1 AS `activo_articulo`,
 1 AS `id_impuesto`,
 1 AS `tipo_impuesto`,
 1 AS `tasa_impuesto`,
 1 AS `descr_impuesto`,
 1 AS `activo_impuesto`,
 1 AS `id_presupuesto`,
 1 AS `numero_version_presupuesto`,
 1 AS `estado_version_presupuesto`,
 1 AS `fecha_creacion_version`,
 1 AS `fecha_envio_version`,
 1 AS `fecha_aprobacion_version`,
 1 AS `numero_presupuesto`,
 1 AS `fecha_presupuesto`,
 1 AS `fecha_validez_presupuesto`,
 1 AS `nombre_evento_presupuesto`,
 1 AS `fecha_inicio_evento_presupuesto`,
 1 AS `fecha_fin_evento_presupuesto`,
 1 AS `id_cliente`,
 1 AS `id_estado_ppto`,
 1 AS `activo_presupuesto`,
 1 AS `nombre_cliente`,
 1 AS `nif_cliente`,
 1 AS `email_cliente`,
 1 AS `telefono_cliente`,
 1 AS `direccion_cliente`,
 1 AS `cp_cliente`,
 1 AS `poblacion_cliente`,
 1 AS `provincia_cliente`,
 1 AS `duracion_evento_dias`,
 1 AS `created_at_linea_ppto`,
 1 AS `updated_at_linea_ppto`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `v_presupuesto_totales`
--

DROP TABLE IF EXISTS `v_presupuesto_totales`;
/*!50001 DROP VIEW IF EXISTS `v_presupuesto_totales`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `v_presupuesto_totales` AS SELECT 
 1 AS `id_version_presupuesto`,
 1 AS `numero_version_presupuesto`,
 1 AS `estado_version_presupuesto`,
 1 AS `fecha_creacion_version`,
 1 AS `fecha_envio_version`,
 1 AS `fecha_aprobacion_version`,
 1 AS `id_presupuesto`,
 1 AS `numero_presupuesto`,
 1 AS `fecha_presupuesto`,
 1 AS `fecha_validez_presupuesto`,
 1 AS `nombre_evento_presupuesto`,
 1 AS `fecha_inicio_evento_presupuesto`,
 1 AS `fecha_fin_evento_presupuesto`,
 1 AS `id_cliente`,
 1 AS `nombre_cliente`,
 1 AS `nif_cliente`,
 1 AS `email_cliente`,
 1 AS `telefono_cliente`,
 1 AS `duracion_evento_dias`,
 1 AS `total_base_imponible`,
 1 AS `total_iva`,
 1 AS `total_con_iva`,
 1 AS `cantidad_lineas_total`,
 1 AS `cantidad_lineas_con_coeficiente`,
 1 AS `base_iva_21`,
 1 AS `importe_iva_21`,
 1 AS `total_iva_21`,
 1 AS `base_iva_10`,
 1 AS `importe_iva_10`,
 1 AS `total_iva_10`,
 1 AS `base_iva_4`,
 1 AS `importe_iva_4`,
 1 AS `total_iva_4`,
 1 AS `base_iva_0`,
 1 AS `importe_iva_0`,
 1 AS `total_iva_0`,
 1 AS `base_iva_otros`,
 1 AS `importe_iva_otros`,
 1 AS `total_iva_otros`,
 1 AS `ahorro_total_coeficientes`,
 1 AS `fecha_primera_linea_creada`,
 1 AS `fecha_ultima_modificacion_linea`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vacaciones_con_nombre`
--

DROP TABLE IF EXISTS `vacaciones_con_nombre`;
/*!50001 DROP VIEW IF EXISTS `vacaciones_con_nombre`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vacaciones_con_nombre` AS SELECT 
 1 AS `id_vacacion`,
 1 AS `id_comercial`,
 1 AS `fecha_inicio`,
 1 AS `fecha_fin`,
 1 AS `descripcion`,
 1 AS `activo_vacacion`,
 1 AS `nombre_comercial`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `visitas_cerradas`
--

DROP TABLE IF EXISTS `visitas_cerradas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `visitas_cerradas` (
  `id_visita_cerrada` int NOT NULL AUTO_INCREMENT,
  `fecha_visita_cerrada` datetime NOT NULL,
  `id_contacto` int NOT NULL,
  `id_llamada` int DEFAULT NULL,
  PRIMARY KEY (`id_visita_cerrada`),
  KEY `id_contacto` (`id_contacto`),
  KEY `fk_visitas_llamadas` (`id_llamada`),
  CONSTRAINT `fk_visitas_llamadas` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`) ON UPDATE CASCADE,
  CONSTRAINT `visitas_cerradas_ibfk_1` FOREIGN KEY (`id_contacto`) REFERENCES `contactos` (`id_contacto`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitas_cerradas`
--

LOCK TABLES `visitas_cerradas` WRITE;
/*!40000 ALTER TABLE `visitas_cerradas` DISABLE KEYS */;
INSERT INTO `visitas_cerradas` VALUES (4,'2025-07-10 14:26:00',9,4),(5,'2025-07-09 14:19:00',2,1),(6,'2025-05-31 15:55:00',7,10),(7,'2025-06-05 12:09:00',14,20),(8,'2025-05-17 15:33:00',17,22),(9,'2025-05-30 13:07:00',18,24),(10,'2025-07-15 14:16:00',8,2);
/*!40000 ALTER TABLE `visitas_cerradas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Temporary view structure for view `vista_adjuntos_con_comunicante`
--

DROP TABLE IF EXISTS `vista_adjuntos_con_comunicante`;
/*!50001 DROP VIEW IF EXISTS `vista_adjuntos_con_comunicante`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_adjuntos_con_comunicante` AS SELECT 
 1 AS `id_adjunto`,
 1 AS `id_llamada`,
 1 AS `nombre_archivo`,
 1 AS `tipo`,
 1 AS `fecha_subida`,
 1 AS `estado`,
 1 AS `nombre_comunicante`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_articulo_completa`
--

DROP TABLE IF EXISTS `vista_articulo_completa`;
/*!50001 DROP VIEW IF EXISTS `vista_articulo_completa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_articulo_completa` AS SELECT 
 1 AS `id_articulo`,
 1 AS `codigo_articulo`,
 1 AS `nombre_articulo`,
 1 AS `name_articulo`,
 1 AS `imagen_articulo`,
 1 AS `precio_alquiler_articulo`,
 1 AS `coeficiente_articulo`,
 1 AS `es_kit_articulo`,
 1 AS `control_total_articulo`,
 1 AS `no_facturar_articulo`,
 1 AS `notas_presupuesto_articulo`,
 1 AS `notes_budget_articulo`,
 1 AS `orden_obs_articulo`,
 1 AS `observaciones_articulo`,
 1 AS `activo_articulo`,
 1 AS `created_at_articulo`,
 1 AS `updated_at_articulo`,
 1 AS `id_familia`,
 1 AS `codigo_familia`,
 1 AS `nombre_familia`,
 1 AS `name_familia`,
 1 AS `descr_familia`,
 1 AS `imagen_familia`,
 1 AS `coeficiente_familia`,
 1 AS `observaciones_presupuesto_familia`,
 1 AS `orden_obs_familia`,
 1 AS `activo_familia`,
 1 AS `id_grupo`,
 1 AS `codigo_grupo`,
 1 AS `nombre_grupo`,
 1 AS `descripcion_grupo`,
 1 AS `activo_grupo`,
 1 AS `id_unidad`,
 1 AS `nombre_unidad`,
 1 AS `descr_unidad`,
 1 AS `simbolo_unidad`,
 1 AS `activo_unidad`,
 1 AS `coeficiente_efectivo`,
 1 AS `imagen_efectiva`,
 1 AS `tipo_imagen`,
 1 AS `jerarquia_completa`,
 1 AS `configuracion_completa`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_cliente_completa`
--

DROP TABLE IF EXISTS `vista_cliente_completa`;
/*!50001 DROP VIEW IF EXISTS `vista_cliente_completa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_cliente_completa` AS SELECT 
 1 AS `id_cliente`,
 1 AS `codigo_cliente`,
 1 AS `nombre_cliente`,
 1 AS `direccion_cliente`,
 1 AS `cp_cliente`,
 1 AS `poblacion_cliente`,
 1 AS `provincia_cliente`,
 1 AS `nif_cliente`,
 1 AS `telefono_cliente`,
 1 AS `fax_cliente`,
 1 AS `web_cliente`,
 1 AS `email_cliente`,
 1 AS `nombre_facturacion_cliente`,
 1 AS `direccion_facturacion_cliente`,
 1 AS `cp_facturacion_cliente`,
 1 AS `poblacion_facturacion_cliente`,
 1 AS `provincia_facturacion_cliente`,
 1 AS `observaciones_cliente`,
 1 AS `activo_cliente`,
 1 AS `created_at_cliente`,
 1 AS `updated_at_cliente`,
 1 AS `id_forma_pago_habitual`,
 1 AS `codigo_pago`,
 1 AS `nombre_pago`,
 1 AS `descuento_pago`,
 1 AS `porcentaje_anticipo_pago`,
 1 AS `dias_anticipo_pago`,
 1 AS `porcentaje_final_pago`,
 1 AS `dias_final_pago`,
 1 AS `observaciones_pago`,
 1 AS `activo_pago`,
 1 AS `id_metodo_pago`,
 1 AS `codigo_metodo_pago`,
 1 AS `nombre_metodo_pago`,
 1 AS `observaciones_metodo_pago`,
 1 AS `activo_metodo_pago`,
 1 AS `tipo_pago_cliente`,
 1 AS `descripcion_forma_pago_cliente`,
 1 AS `direccion_completa_cliente`,
 1 AS `direccion_facturacion_completa_cliente`,
 1 AS `tiene_direccion_facturacion_diferente`,
 1 AS `estado_forma_pago_cliente`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_cliente_ubicaciones`
--

DROP TABLE IF EXISTS `vista_cliente_ubicaciones`;
/*!50001 DROP VIEW IF EXISTS `vista_cliente_ubicaciones`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_cliente_ubicaciones` AS SELECT 
 1 AS `id_cliente`,
 1 AS `codigo_cliente`,
 1 AS `nombre_cliente`,
 1 AS `nif_cliente`,
 1 AS `telefono_cliente`,
 1 AS `email_cliente`,
 1 AS `activo_cliente`,
 1 AS `id_ubicacion`,
 1 AS `nombre_ubicacion`,
 1 AS `direccion_ubicacion`,
 1 AS `codigo_postal_ubicacion`,
 1 AS `poblacion_ubicacion`,
 1 AS `provincia_ubicacion`,
 1 AS `pais_ubicacion`,
 1 AS `persona_contacto_ubicacion`,
 1 AS `telefono_contacto_ubicacion`,
 1 AS `email_contacto_ubicacion`,
 1 AS `observaciones_ubicacion`,
 1 AS `es_principal_ubicacion`,
 1 AS `activo_ubicacion`,
 1 AS `direccion_completa_cliente`,
 1 AS `direccion_completa_ubicacion`,
 1 AS `tipo_ubicacion`,
 1 AS `estado_completo`,
 1 AS `tiene_contacto_propio`,
 1 AS `total_ubicaciones_cliente`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_costos_furgoneta`
--

DROP TABLE IF EXISTS `vista_costos_furgoneta`;
/*!50001 DROP VIEW IF EXISTS `vista_costos_furgoneta`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_costos_furgoneta` AS SELECT 
 1 AS `id_furgoneta`,
 1 AS `matricula_furgoneta`,
 1 AS `marca_furgoneta`,
 1 AS `modelo_furgoneta`,
 1 AS `anio_furgoneta`,
 1 AS `costo_total`,
 1 AS `costo_anio_actual`,
 1 AS `costo_revisiones`,
 1 AS `costo_reparaciones`,
 1 AS `costo_itv`,
 1 AS `costo_neumaticos`,
 1 AS `total_mantenimientos`,
 1 AS `costo_promedio`,
 1 AS `fecha_ultimo_mantenimiento`,
 1 AS `kilometraje_actual`,
 1 AS `costo_por_km`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_elementos_completa`
--

DROP TABLE IF EXISTS `vista_elementos_completa`;
/*!50001 DROP VIEW IF EXISTS `vista_elementos_completa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_elementos_completa` AS SELECT 
 1 AS `id_elemento`,
 1 AS `codigo_elemento`,
 1 AS `codigo_barras_elemento`,
 1 AS `descripcion_elemento`,
 1 AS `numero_serie_elemento`,
 1 AS `modelo_elemento`,
 1 AS `nave_elemento`,
 1 AS `pasillo_columna_elemento`,
 1 AS `altura_elemento`,
 1 AS `ubicacion_completa_elemento`,
 1 AS `fecha_compra_elemento`,
 1 AS `precio_compra_elemento`,
 1 AS `fecha_alta_elemento`,
 1 AS `fecha_fin_garantia_elemento`,
 1 AS `proximo_mantenimiento_elemento`,
 1 AS `observaciones_elemento`,
 1 AS `es_propio_elemento`,
 1 AS `id_proveedor_compra_elemento`,
 1 AS `id_proveedor_alquiler_elemento`,
 1 AS `precio_dia_alquiler_elemento`,
 1 AS `id_forma_pago_alquiler_elemento`,
 1 AS `observaciones_alquiler_elemento`,
 1 AS `codigo_proveedor_compra`,
 1 AS `nombre_proveedor_compra`,
 1 AS `telefono_proveedor_compra`,
 1 AS `email_proveedor_compra`,
 1 AS `nif_proveedor_compra`,
 1 AS `codigo_proveedor_alquiler`,
 1 AS `nombre_proveedor_alquiler`,
 1 AS `telefono_proveedor_alquiler`,
 1 AS `email_proveedor_alquiler`,
 1 AS `nif_proveedor_alquiler`,
 1 AS `persona_contacto_proveedor_alquiler`,
 1 AS `codigo_forma_pago_alquiler`,
 1 AS `nombre_forma_pago_alquiler`,
 1 AS `porcentaje_anticipo_alquiler`,
 1 AS `dias_anticipo_alquiler`,
 1 AS `porcentaje_final_alquiler`,
 1 AS `dias_final_alquiler`,
 1 AS `descuento_forma_pago_alquiler`,
 1 AS `codigo_metodo_pago_alquiler`,
 1 AS `nombre_metodo_pago_alquiler`,
 1 AS `id_articulo`,
 1 AS `codigo_articulo`,
 1 AS `nombre_articulo`,
 1 AS `name_articulo`,
 1 AS `precio_alquiler_articulo`,
 1 AS `id_familia`,
 1 AS `codigo_familia`,
 1 AS `nombre_familia`,
 1 AS `name_familia`,
 1 AS `id_grupo`,
 1 AS `codigo_grupo`,
 1 AS `nombre_grupo`,
 1 AS `id_marca`,
 1 AS `codigo_marca`,
 1 AS `nombre_marca`,
 1 AS `id_estado_elemento`,
 1 AS `codigo_estado_elemento`,
 1 AS `descripcion_estado_elemento`,
 1 AS `color_estado_elemento`,
 1 AS `permite_alquiler_estado_elemento`,
 1 AS `activo_elemento`,
 1 AS `created_at_elemento`,
 1 AS `updated_at_elemento`,
 1 AS `jerarquia_completa_elemento`,
 1 AS `tipo_propiedad_elemento`,
 1 AS `proveedor_principal_elemento`,
 1 AS `estado_configuracion_alquiler`,
 1 AS `descripcion_forma_pago_alquiler`,
 1 AS `costo_mensual_estimado_alquiler`,
 1 AS `dias_en_servicio_elemento`,
 1 AS `anios_en_servicio_elemento`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_furgoneta_completa`
--

DROP TABLE IF EXISTS `vista_furgoneta_completa`;
/*!50001 DROP VIEW IF EXISTS `vista_furgoneta_completa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_furgoneta_completa` AS SELECT 
 1 AS `id_furgoneta`,
 1 AS `matricula_furgoneta`,
 1 AS `marca_furgoneta`,
 1 AS `modelo_furgoneta`,
 1 AS `anio_furgoneta`,
 1 AS `numero_bastidor_furgoneta`,
 1 AS `kilometros_entre_revisiones_furgoneta`,
 1 AS `fecha_proxima_itv_furgoneta`,
 1 AS `fecha_vencimiento_seguro_furgoneta`,
 1 AS `compania_seguro_furgoneta`,
 1 AS `numero_poliza_seguro_furgoneta`,
 1 AS `capacidad_carga_kg_furgoneta`,
 1 AS `capacidad_carga_m3_furgoneta`,
 1 AS `tipo_combustible_furgoneta`,
 1 AS `consumo_medio_furgoneta`,
 1 AS `taller_habitual_furgoneta`,
 1 AS `telefono_taller_furgoneta`,
 1 AS `estado_furgoneta`,
 1 AS `observaciones_furgoneta`,
 1 AS `activo_furgoneta`,
 1 AS `created_at_furgoneta`,
 1 AS `updated_at_furgoneta`,
 1 AS `kilometraje_actual`,
 1 AS `fecha_ultimo_registro_km`,
 1 AS `total_mantenimientos`,
 1 AS `costo_total_mantenimientos`,
 1 AS `fecha_ultimo_mantenimiento`,
 1 AS `estado_itv`,
 1 AS `estado_seguro`,
 1 AS `km_desde_ultima_revision`,
 1 AS `necesita_revision`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_kilometraje_completo`
--

DROP TABLE IF EXISTS `vista_kilometraje_completo`;
/*!50001 DROP VIEW IF EXISTS `vista_kilometraje_completo`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_kilometraje_completo` AS SELECT 
 1 AS `id_registro_km`,
 1 AS `id_furgoneta`,
 1 AS `fecha_registro_km`,
 1 AS `kilometraje_registrado_km`,
 1 AS `tipo_registro_km`,
 1 AS `observaciones_registro_km`,
 1 AS `created_at_registro_km`,
 1 AS `updated_at_registro_km`,
 1 AS `matricula_furgoneta`,
 1 AS `marca_furgoneta`,
 1 AS `modelo_furgoneta`,
 1 AS `estado_furgoneta`,
 1 AS `km_recorridos`,
 1 AS `dias_transcurridos`,
 1 AS `km_promedio_diario`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_kit_completa`
--

DROP TABLE IF EXISTS `vista_kit_completa`;
/*!50001 DROP VIEW IF EXISTS `vista_kit_completa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_kit_completa` AS SELECT 
 1 AS `id_kit`,
 1 AS `cantidad_kit`,
 1 AS `activo_kit`,
 1 AS `created_at_kit`,
 1 AS `updated_at_kit`,
 1 AS `id_articulo_maestro`,
 1 AS `codigo_articulo_maestro`,
 1 AS `nombre_articulo_maestro`,
 1 AS `name_articulo_maestro`,
 1 AS `precio_articulo_maestro`,
 1 AS `es_kit_articulo_maestro`,
 1 AS `activo_articulo_maestro`,
 1 AS `id_articulo_componente`,
 1 AS `codigo_articulo_componente`,
 1 AS `nombre_articulo_componente`,
 1 AS `name_articulo_componente`,
 1 AS `precio_articulo_componente`,
 1 AS `es_kit_articulo_componente`,
 1 AS `activo_articulo_componente`,
 1 AS `subtotal_componente`,
 1 AS `total_componentes_kit`,
 1 AS `precio_total_kit`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_mantenimiento_completo`
--

DROP TABLE IF EXISTS `vista_mantenimiento_completo`;
/*!50001 DROP VIEW IF EXISTS `vista_mantenimiento_completo`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_mantenimiento_completo` AS SELECT 
 1 AS `id_mantenimiento`,
 1 AS `id_furgoneta`,
 1 AS `fecha_mantenimiento`,
 1 AS `tipo_mantenimiento`,
 1 AS `descripcion_mantenimiento`,
 1 AS `kilometraje_mantenimiento`,
 1 AS `costo_mantenimiento`,
 1 AS `numero_factura_mantenimiento`,
 1 AS `taller_mantenimiento`,
 1 AS `telefono_taller_mantenimiento`,
 1 AS `direccion_taller_mantenimiento`,
 1 AS `resultado_itv`,
 1 AS `fecha_proxima_itv`,
 1 AS `garantia_hasta_mantenimiento`,
 1 AS `observaciones_mantenimiento`,
 1 AS `activo_mantenimiento`,
 1 AS `created_at_mantenimiento`,
 1 AS `updated_at_mantenimiento`,
 1 AS `matricula_furgoneta`,
 1 AS `marca_furgoneta`,
 1 AS `modelo_furgoneta`,
 1 AS `anio_furgoneta`,
 1 AS `estado_furgoneta`,
 1 AS `estado_garantia`,
 1 AS `dias_desde_mantenimiento`,
 1 AS `km_desde_mantenimiento`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_presupuesto_completa`
--

DROP TABLE IF EXISTS `vista_presupuesto_completa`;
/*!50001 DROP VIEW IF EXISTS `vista_presupuesto_completa`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_presupuesto_completa` AS SELECT 
 1 AS `id_presupuesto`,
 1 AS `numero_presupuesto`,
 1 AS `version_actual_presupuesto`,
 1 AS `fecha_presupuesto`,
 1 AS `fecha_validez_presupuesto`,
 1 AS `fecha_inicio_evento_presupuesto`,
 1 AS `fecha_fin_evento_presupuesto`,
 1 AS `numero_pedido_cliente_presupuesto`,
 1 AS `aplicar_coeficientes_presupuesto`,
 1 AS `descuento_presupuesto`,
 1 AS `nombre_evento_presupuesto`,
 1 AS `direccion_evento_presupuesto`,
 1 AS `poblacion_evento_presupuesto`,
 1 AS `cp_evento_presupuesto`,
 1 AS `provincia_evento_presupuesto`,
 1 AS `observaciones_cabecera_presupuesto`,
 1 AS `observaciones_pie_presupuesto`,
 1 AS `observaciones_cabecera_ingles_presupuesto`,
 1 AS `observaciones_pie_ingles_presupuesto`,
 1 AS `mostrar_obs_familias_presupuesto`,
 1 AS `mostrar_obs_articulos_presupuesto`,
 1 AS `observaciones_internas_presupuesto`,
 1 AS `activo_presupuesto`,
 1 AS `created_at_presupuesto`,
 1 AS `updated_at_presupuesto`,
 1 AS `id_cliente`,
 1 AS `codigo_cliente`,
 1 AS `nombre_cliente`,
 1 AS `nif_cliente`,
 1 AS `direccion_cliente`,
 1 AS `cp_cliente`,
 1 AS `poblacion_cliente`,
 1 AS `provincia_cliente`,
 1 AS `telefono_cliente`,
 1 AS `email_cliente`,
 1 AS `porcentaje_descuento_cliente`,
 1 AS `nombre_facturacion_cliente`,
 1 AS `direccion_facturacion_cliente`,
 1 AS `cp_facturacion_cliente`,
 1 AS `poblacion_facturacion_cliente`,
 1 AS `provincia_facturacion_cliente`,
 1 AS `id_contacto_cliente`,
 1 AS `nombre_contacto_cliente`,
 1 AS `apellidos_contacto_cliente`,
 1 AS `telefono_contacto_cliente`,
 1 AS `email_contacto_cliente`,
 1 AS `id_estado_ppto`,
 1 AS `codigo_estado_ppto`,
 1 AS `nombre_estado_ppto`,
 1 AS `color_estado_ppto`,
 1 AS `orden_estado_ppto`,
 1 AS `id_forma_pago`,
 1 AS `codigo_pago`,
 1 AS `nombre_pago`,
 1 AS `porcentaje_anticipo_pago`,
 1 AS `dias_anticipo_pago`,
 1 AS `porcentaje_final_pago`,
 1 AS `dias_final_pago`,
 1 AS `descuento_pago`,
 1 AS `id_metodo_pago`,
 1 AS `codigo_metodo_pago`,
 1 AS `nombre_metodo_pago`,
 1 AS `id_metodo_contacto`,
 1 AS `nombre_metodo_contacto`,
 1 AS `id_forma_pago_habitual`,
 1 AS `nombre_forma_pago_habitual_cliente`,
 1 AS `direccion_completa_evento_presupuesto`,
 1 AS `direccion_completa_cliente`,
 1 AS `direccion_facturacion_completa_cliente`,
 1 AS `nombre_completo_contacto`,
 1 AS `dias_validez_restantes`,
 1 AS `estado_validez_presupuesto`,
 1 AS `duracion_evento_dias`,
 1 AS `dias_hasta_inicio_evento`,
 1 AS `dias_hasta_fin_evento`,
 1 AS `estado_evento_presupuesto`,
 1 AS `prioridad_presupuesto`,
 1 AS `tipo_pago_presupuesto`,
 1 AS `descripcion_completa_forma_pago`,
 1 AS `fecha_vencimiento_anticipo`,
 1 AS `fecha_vencimiento_final`,
 1 AS `comparacion_descuento`,
 1 AS `estado_descuento_presupuesto`,
 1 AS `aplica_descuento_presupuesto`,
 1 AS `diferencia_descuento`,
 1 AS `tiene_direccion_facturacion_diferente`,
 1 AS `dias_desde_emision`,
 1 AS `id_version_actual`,
 1 AS `numero_version_actual`,
 1 AS `estado_version_actual`,
 1 AS `fecha_creacion_version_actual`,
 1 AS `estado_general_presupuesto`*/;
SET character_set_client = @saved_cs_client;

--
-- Temporary view structure for view `vista_registro_kilometraje`
--

DROP TABLE IF EXISTS `vista_registro_kilometraje`;
/*!50001 DROP VIEW IF EXISTS `vista_registro_kilometraje`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `vista_registro_kilometraje` AS SELECT 
 1 AS `id_registro_km`,
 1 AS `id_furgoneta`,
 1 AS `fecha_registro_km`,
 1 AS `kilometraje_registrado_km`,
 1 AS `tipo_registro_km`,
 1 AS `observaciones_registro_km`,
 1 AS `created_at_registro_km`,
 1 AS `matricula_furgoneta`,
 1 AS `marca_furgoneta`,
 1 AS `modelo_furgoneta`,
 1 AS `estado_furgoneta`,
 1 AS `km_recorridos`,
 1 AS `dias_transcurridos`,
 1 AS `km_promedio_diario`*/;
SET character_set_client = @saved_cs_client;

--
-- Dumping events for database 'toldos_db'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `desactivar_fecha_vacaciones_12` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`administrator`@`%`*/ /*!50106 EVENT `desactivar_fecha_vacaciones_12` ON SCHEDULE EVERY 1 DAY STARTS '2025-04-03 00:01:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL desactivar_vacaciones_pasadas() */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
/*!50106 DROP EVENT IF EXISTS `desactivar_fecha_vacaciones_3` */;;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`administrator`@`%`*/ /*!50106 EVENT `desactivar_fecha_vacaciones_3` ON SCHEDULE EVERY 1 DAY STARTS '2025-04-03 03:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL desactivar_vacaciones_pasadas() */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
/*!50106 DROP EVENT IF EXISTS `desactivar_vacaciones_pasadas` */;;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`administrator`@`%`*/ /*!50106 EVENT `desactivar_vacaciones_pasadas` ON SCHEDULE EVERY 12 HOUR STARTS '2025-05-26 09:34:34' ON COMPLETION NOT PRESERVE ENABLE DO UPDATE com_vacaciones
  SET activo_vacacion = 0
  WHERE fecha_fin < CURDATE()
    AND activo_vacacion = 1 */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'toldos_db'
--
/*!50003 DROP PROCEDURE IF EXISTS `desactivar_vacaciones_pasadas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`administrator`@`%` PROCEDURE `desactivar_vacaciones_pasadas`()
BEGIN
    UPDATE com_vacaciones
    SET activo_vacacion = 0
    WHERE fecha_fin < CURDATE()
    AND activo_vacacion = 1
    AND id_vacacion IS NOT NULL; 
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_actualizar_contador_empresa` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `sp_actualizar_contador_empresa`(
    IN p_id_empresa INT UNSIGNED,
    IN p_tipo_documento ENUM('presupuesto', 'factura', 'abono')
)
BEGIN
    IF p_tipo_documento = 'presupuesto' THEN
        UPDATE empresa 
        SET numero_actual_presupuesto_empresa = numero_actual_presupuesto_empresa + 1
        WHERE id_empresa = p_id_empresa;
        
    ELSEIF p_tipo_documento = 'factura' THEN
        UPDATE empresa 
        SET numero_actual_factura_empresa = numero_actual_factura_empresa + 1
        WHERE id_empresa = p_id_empresa;
        
    ELSEIF p_tipo_documento = 'abono' THEN
        UPDATE empresa 
        SET numero_actual_abono_empresa = numero_actual_abono_empresa + 1
        WHERE id_empresa = p_id_empresa;
    END IF;
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_listar_empresas_facturacion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `sp_listar_empresas_facturacion`()
BEGIN
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_obtener_empresa_ficticia_principal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `sp_obtener_empresa_ficticia_principal`()
BEGIN
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
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `sp_obtener_siguiente_numero` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `sp_obtener_siguiente_numero`(
    IN p_codigo_empresa VARCHAR(20),
    IN p_tipo_documento ENUM('presupuesto', 'factura', 'abono'),
    OUT p_numero_completo VARCHAR(50)
)
BEGIN
    DECLARE v_serie VARCHAR(10);
    DECLARE v_numero_actual INT;
    DECLARE v_anio VARCHAR(4);
    
    SET v_anio = YEAR(CURDATE());
    
    -- Obtener datos según tipo de documento
    IF p_tipo_documento = 'presupuesto' THEN
        SELECT 
            serie_presupuesto_empresa,
            numero_actual_presupuesto_empresa + 1
        INTO v_serie, v_numero_actual
        FROM empresa
        WHERE codigo_empresa = p_codigo_empresa
        AND activo_empresa = TRUE;
        
        -- Formato: P2024-0001
        SET p_numero_completo = CONCAT(
            v_serie,
            v_anio,
            '-',
            LPAD(v_numero_actual, 4, '0')
        );
        
    ELSEIF p_tipo_documento = 'factura' THEN
        SELECT 
            serie_factura_empresa,
            numero_actual_factura_empresa + 1
        INTO v_serie, v_numero_actual
        FROM empresa
        WHERE codigo_empresa = p_codigo_empresa
        AND activo_empresa = TRUE;
        
        -- Formato: F2024/0001
        SET p_numero_completo = CONCAT(
            v_serie,
            v_anio,
            '/',
            LPAD(v_numero_actual, 4, '0')
        );
        
    ELSEIF p_tipo_documento = 'abono' THEN
        SELECT 
            serie_abono_empresa,
            numero_actual_abono_empresa + 1
        INTO v_serie, v_numero_actual
        FROM empresa
        WHERE codigo_empresa = p_codigo_empresa
        AND activo_empresa = TRUE;
        
        -- Formato: R2024/0001
        SET p_numero_completo = CONCAT(
            v_serie,
            v_anio,
            '/',
            LPAD(v_numero_actual, 4, '0')
        );
    END IF;
    
END ;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Final view structure for view `contacto_cantidad_cliente`
--

/*!50001 DROP VIEW IF EXISTS `contacto_cantidad_cliente`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `contacto_cantidad_cliente` AS select `c`.`id_cliente` AS `id_cliente`,`c`.`codigo_cliente` AS `codigo_cliente`,`c`.`nombre_cliente` AS `nombre_cliente`,`c`.`direccion_cliente` AS `direccion_cliente`,`c`.`cp_cliente` AS `cp_cliente`,`c`.`poblacion_cliente` AS `poblacion_cliente`,`c`.`provincia_cliente` AS `provincia_cliente`,`c`.`nif_cliente` AS `nif_cliente`,`c`.`telefono_cliente` AS `telefono_cliente`,`c`.`fax_cliente` AS `fax_cliente`,`c`.`web_cliente` AS `web_cliente`,`c`.`email_cliente` AS `email_cliente`,`c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`,`c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`,`c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`,`c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`,`c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`,`c`.`observaciones_cliente` AS `observaciones_cliente`,`c`.`activo_cliente` AS `activo_cliente`,`c`.`created_at_cliente` AS `created_at_cliente`,`c`.`updated_at_cliente` AS `updated_at_cliente`,`c`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`,`c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`,`fp`.`codigo_pago` AS `codigo_pago`,`fp`.`nombre_pago` AS `nombre_pago`,`fp`.`descuento_pago` AS `descuento_pago`,`fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`,`fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`,`fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`,`fp`.`dias_final_pago` AS `dias_final_pago`,`fp`.`observaciones_pago` AS `observaciones_pago`,`fp`.`activo_pago` AS `activo_pago`,`mp`.`id_metodo_pago` AS `id_metodo_pago`,`mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`,`mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`,`mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`,`mp`.`activo_metodo_pago` AS `activo_metodo_pago`,(select count(`cc`.`id_contacto_cliente`) from `contacto_cliente` `cc` where (`cc`.`id_cliente` = `c`.`id_cliente`)) AS `cantidad_contactos_cliente`,(case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_cliente`,(case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_cliente`,concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`,(case when (`c`.`direccion_facturacion_cliente` is not null) then concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) else NULL end) AS `direccion_facturacion_completa_cliente`,(case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`,(case when (`c`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_cliente`,(case when (`c`.`porcentaje_descuento_cliente` = 0.00) then 'Sin descuento' when ((`c`.`porcentaje_descuento_cliente` > 0.00) and (`c`.`porcentaje_descuento_cliente` <= 5.00)) then 'Descuento bajo' when ((`c`.`porcentaje_descuento_cliente` > 5.00) and (`c`.`porcentaje_descuento_cliente` <= 15.00)) then 'Descuento medio' when (`c`.`porcentaje_descuento_cliente` > 15.00) then 'Descuento alto' else 'Sin descuento' end) AS `categoria_descuento_cliente`,(case when (`c`.`porcentaje_descuento_cliente` > 0.00) then true else false end) AS `tiene_descuento_cliente` from ((`cliente` `c` left join `forma_pago` `fp` on((`c`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `contacto_cantidad_proveedor`
--

/*!50001 DROP VIEW IF EXISTS `contacto_cantidad_proveedor`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `contacto_cantidad_proveedor` AS select `p`.`id_proveedor` AS `id_proveedor`,`p`.`codigo_proveedor` AS `codigo_proveedor`,`p`.`nombre_proveedor` AS `nombre_proveedor`,`p`.`direccion_proveedor` AS `direccion_proveedor`,`p`.`cp_proveedor` AS `cp_proveedor`,`p`.`poblacion_proveedor` AS `poblacion_proveedor`,`p`.`provincia_proveedor` AS `provincia_proveedor`,`p`.`nif_proveedor` AS `nif_proveedor`,`p`.`telefono_proveedor` AS `telefono_proveedor`,`p`.`fax_proveedor` AS `fax_proveedor`,`p`.`web_proveedor` AS `web_proveedor`,`p`.`email_proveedor` AS `email_proveedor`,`p`.`persona_contacto_proveedor` AS `persona_contacto_proveedor`,`p`.`direccion_sat_proveedor` AS `direccion_sat_proveedor`,`p`.`cp_sat_proveedor` AS `cp_sat_proveedor`,`p`.`poblacion_sat_proveedor` AS `poblacion_sat_proveedor`,`p`.`provincia_sat_proveedor` AS `provincia_sat_proveedor`,`p`.`telefono_sat_proveedor` AS `telefono_sat_proveedor`,`p`.`fax_sat_proveedor` AS `fax_sat_proveedor`,`p`.`email_sat_proveedor` AS `email_sat_proveedor`,`p`.`observaciones_proveedor` AS `observaciones_proveedor`,`p`.`activo_proveedor` AS `activo_proveedor`,`p`.`created_at_proveedor` AS `created_at_proveedor`,`p`.`updated_at_proveedor` AS `updated_at_proveedor`,`p`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`,`fp`.`codigo_pago` AS `codigo_pago`,`fp`.`nombre_pago` AS `nombre_pago`,`fp`.`descuento_pago` AS `descuento_pago`,`fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`,`fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`,`fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`,`fp`.`dias_final_pago` AS `dias_final_pago`,`fp`.`observaciones_pago` AS `observaciones_pago`,`fp`.`activo_pago` AS `activo_pago`,`mp`.`id_metodo_pago` AS `id_metodo_pago`,`mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`,`mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`,`mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`,`mp`.`activo_metodo_pago` AS `activo_metodo_pago`,(select count(`cp`.`id_contacto_proveedor`) from `contacto_proveedor` `cp` where (`cp`.`id_proveedor` = `p`.`id_proveedor`)) AS `cantidad_contacto_proveedor`,(case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_proveedor`,(case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_proveedor`,concat_ws(', ',`p`.`direccion_proveedor`,concat(`p`.`cp_proveedor`,' ',`p`.`poblacion_proveedor`),`p`.`provincia_proveedor`) AS `direccion_completa_proveedor`,(case when (`p`.`direccion_sat_proveedor` is not null) then concat_ws(', ',`p`.`direccion_sat_proveedor`,concat(`p`.`cp_sat_proveedor`,' ',`p`.`poblacion_sat_proveedor`),`p`.`provincia_sat_proveedor`) else NULL end) AS `direccion_sat_completa_proveedor`,(case when (`p`.`direccion_sat_proveedor` is not null) then true else false end) AS `tiene_direccion_sat`,(case when (`p`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_proveedor` from ((`proveedor` `p` left join `forma_pago` `fp` on((`p`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `contactos_con_nombre_comunicante`
--

/*!50001 DROP VIEW IF EXISTS `contactos_con_nombre_comunicante`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`administrator`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `contactos_con_nombre_comunicante` AS select `c`.`id_contacto` AS `id_contacto`,`c`.`id_llamada` AS `id_llamada`,`c`.`id_metodo` AS `id_metodo`,`c`.`fecha_hora_contacto` AS `fecha_hora_contacto`,`c`.`observaciones` AS `observaciones`,`c`.`id_visita_cerrada` AS `id_visita_cerrada`,(select `vc`.`fecha_visita_cerrada` from `visitas_cerradas` `vc` where (`vc`.`id_visita_cerrada` = `c`.`id_visita_cerrada`)) AS `fecha_visita_cerrada`,`c`.`estado` AS `estado`,(select `l`.`nombre_comunicante` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `nombre_comunicante`,(select `l`.`domicilio_instalacion` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `domicilio_instalacion`,(select `l`.`telefono_fijo` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `telefono_fijo`,(select `l`.`telefono_movil` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `telefono_movil`,(select `l`.`email_contacto` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `email_contacto`,(select `l`.`fecha_hora_preferida` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `fecha_hora_preferida`,(select `l`.`fecha_recepcion` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `fecha_recepcion`,(select `l`.`id_comercial_asignado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `id_comercial_asignado`,(select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `estado_llamada`,(select `l`.`activo_llamada` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `activo_llamada`,(select `m`.`nombre` from `metodos_contacto` `m` where (`m`.`id_metodo` = `c`.`id_metodo`)) AS `nombre_metodo`,(select `m`.`imagen_metodo` from `metodos_contacto` `m` where (`m`.`id_metodo` = `c`.`id_metodo`)) AS `imagen_metodo`,(select `e`.`desc_estado` from `estados_llamada` `e` where (`e`.`id_estado` = (select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)))) AS `descripcion_estado_llamada`,(select `com`.`nombre` from `comerciales` `com` where (`com`.`id_comercial` = (select `l`.`id_comercial_asignado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)))) AS `nombre_comercial`,ifnull((select group_concat(`a`.`nombre_archivo` separator ',') from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `c`.`id_llamada`) and (`a`.`estado` = 1))),'Sin archivos') AS `archivos_adjuntos`,(select (count(0) > 0) from `contactos` `cont` where (`cont`.`id_llamada` = `c`.`id_llamada`)) AS `tiene_contactos`,((select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) = 3) AS `estado_es_3`,(select (count(0) > 0) from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `c`.`id_llamada`) and (`a`.`estado` = 1))) AS `tiene_adjuntos` from `contactos` `c` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `familia_unidad_media`
--

/*!50001 DROP VIEW IF EXISTS `familia_unidad_media`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `familia_unidad_media` AS select `f`.`id_familia` AS `id_familia`,`f`.`id_grupo` AS `id_grupo`,`f`.`codigo_familia` AS `codigo_familia`,`f`.`nombre_familia` AS `nombre_familia`,`f`.`name_familia` AS `name_familia`,`f`.`descr_familia` AS `descr_familia`,`f`.`imagen_familia` AS `imagen_familia`,`f`.`activo_familia` AS `activo_familia`,`f`.`coeficiente_familia` AS `coeficiente_familia`,`f`.`created_at_familia` AS `created_at_familia`,`f`.`updated_at_familia` AS `updated_at_familia`,`f`.`id_unidad_familia` AS `id_unidad_familia`,`f`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`,`f`.`orden_obs_familia` AS `orden_obs_familia`,`u`.`nombre_unidad` AS `nombre_unidad`,`u`.`descr_unidad` AS `descr_unidad`,`u`.`simbolo_unidad` AS `simbolo_unidad`,`u`.`activo_unidad` AS `activo_unidad`,`g`.`codigo_grupo` AS `codigo_grupo`,`g`.`nombre_grupo` AS `nombre_grupo`,`g`.`descripcion_grupo` AS `descripcion_grupo` from ((`familia` `f` left join `unidad_medida` `u` on((`f`.`id_unidad_familia` = `u`.`id_unidad`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `llamadas_con_comerciales_y_metodos`
--

/*!50001 DROP VIEW IF EXISTS `llamadas_con_comerciales_y_metodos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`administrator`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `llamadas_con_comerciales_y_metodos` AS select `l`.`id_llamada` AS `id_llamada`,`l`.`id_metodo` AS `id_metodo`,`l`.`nombre_comunicante` AS `nombre_comunicante`,`l`.`domicilio_instalacion` AS `domicilio_instalacion`,`l`.`telefono_fijo` AS `telefono_fijo`,`l`.`telefono_movil` AS `telefono_movil`,`l`.`email_contacto` AS `email_contacto`,`l`.`fecha_hora_preferida` AS `fecha_hora_preferida`,`l`.`observaciones` AS `observaciones`,`l`.`id_comercial_asignado` AS `id_comercial_asignado`,`l`.`estado` AS `estado`,`l`.`fecha_recepcion` AS `fecha_recepcion`,`l`.`activo_llamada` AS `activo_llamada`,(select `c`.`nombre` from `comerciales` `c` where (`c`.`id_comercial` = `l`.`id_comercial_asignado`)) AS `nombre_comercial`,(select `m`.`nombre` from `metodos_contacto` `m` where (`m`.`id_metodo` = `l`.`id_metodo`)) AS `nombre_metodo`,(select `m`.`imagen_metodo` from `metodos_contacto` `m` where (`m`.`id_metodo` = `l`.`id_metodo`)) AS `imagen_metodo`,(select `e`.`desc_estado` from `estados_llamada` `e` where (`e`.`id_estado` = `l`.`estado`)) AS `descripcion_estado`,ifnull((select group_concat(`a`.`nombre_archivo` separator ',') from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `l`.`id_llamada`) and (`a`.`estado` = 1))),'Sin archivos') AS `archivos_adjuntos`,(select (count(0) > 0) from `contactos` `c` where (`c`.`id_llamada` = `l`.`id_llamada`)) AS `tiene_contactos`,(`l`.`estado` = 3) AS `estado_es_3`,(select (count(0) > 0) from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `l`.`id_llamada`) and (`a`.`estado` = 1))) AS `tiene_adjuntos`,(select `c`.`fecha_hora_contacto` from `contactos` `c` where (`c`.`id_llamada` = `l`.`id_llamada`) order by `c`.`fecha_hora_contacto` limit 1) AS `fecha_primer_contacto` from `llamadas` `l` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `metodo_forma_pago`
--

/*!50001 DROP VIEW IF EXISTS `metodo_forma_pago`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `metodo_forma_pago` AS select `fp`.`codigo_pago` AS `codigo_pago`,`fp`.`nombre_pago` AS `nombre_pago`,`mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`,`fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`,`fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`,`fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`,`fp`.`dias_final_pago` AS `dias_final_pago`,`fp`.`descuento_pago` AS `descuento_pago`,(case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' else 'Pago fraccionado' end) AS `tipo_pago` from (`forma_pago` `fp` join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) where (`fp`.`activo_pago` = true) order by (case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 1 else 2 end),`fp`.`nombre_pago` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `usuarios_con_rol`
--

/*!50001 DROP VIEW IF EXISTS `usuarios_con_rol`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`administrator`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `usuarios_con_rol` AS select `usuarios`.`id_usuario` AS `id_usuario`,`usuarios`.`nombre` AS `nombre`,`usuarios`.`email` AS `email`,`usuarios`.`contrasena` AS `contrasena`,`usuarios`.`fecha_crea` AS `fecha_crea`,`usuarios`.`est` AS `est`,`usuarios`.`id_rol` AS `id_rol`,(select `roles`.`nombre_rol` from `roles` where (`roles`.`id_rol` = `usuarios`.`id_rol`)) AS `nombre_rol` from `usuarios` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_linea_presupuesto_calculada`
--

/*!50001 DROP VIEW IF EXISTS `v_linea_presupuesto_calculada`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_linea_presupuesto_calculada` AS select `lp`.`id_linea_ppto` AS `id_linea_ppto`,`lp`.`id_version_presupuesto` AS `id_version_presupuesto`,`lp`.`id_articulo` AS `id_articulo`,`lp`.`id_linea_padre` AS `id_linea_padre`,`lp`.`id_ubicacion` AS `id_ubicacion`,`lp`.`numero_linea_ppto` AS `numero_linea_ppto`,`lp`.`tipo_linea_ppto` AS `tipo_linea_ppto`,`lp`.`nivel_jerarquia` AS `nivel_jerarquia`,`lp`.`codigo_linea_ppto` AS `codigo_linea_ppto`,`lp`.`descripcion_linea_ppto` AS `descripcion_linea_ppto`,`lp`.`orden_linea_ppto` AS `orden_linea_ppto`,`lp`.`observaciones_linea_ppto` AS `observaciones_linea_ppto`,`lp`.`mostrar_obs_articulo_linea_ppto` AS `mostrar_obs_articulo_linea_ppto`,`lp`.`ocultar_detalle_kit_linea_ppto` AS `ocultar_detalle_kit_linea_ppto`,`lp`.`mostrar_en_presupuesto` AS `mostrar_en_presupuesto`,`lp`.`es_opcional` AS `es_opcional`,`lp`.`activo_linea_ppto` AS `activo_linea_ppto`,`lp`.`fecha_montaje_linea_ppto` AS `fecha_montaje_linea_ppto`,`lp`.`fecha_desmontaje_linea_ppto` AS `fecha_desmontaje_linea_ppto`,`lp`.`fecha_inicio_linea_ppto` AS `fecha_inicio_linea_ppto`,`lp`.`fecha_fin_linea_ppto` AS `fecha_fin_linea_ppto`,`lp`.`cantidad_linea_ppto` AS `cantidad_linea_ppto`,`lp`.`precio_unitario_linea_ppto` AS `precio_unitario_linea_ppto`,`lp`.`descuento_linea_ppto` AS `descuento_linea_ppto`,`lp`.`porcentaje_iva_linea_ppto` AS `porcentaje_iva_linea_ppto`,`lp`.`jornadas_linea_ppto` AS `jornadas_linea_ppto`,`lp`.`id_coeficiente` AS `id_coeficiente`,`lp`.`aplicar_coeficiente_linea_ppto` AS `aplicar_coeficiente_linea_ppto`,`lp`.`valor_coeficiente_linea_ppto` AS `valor_coeficiente_linea_ppto`,`c`.`jornadas_coeficiente` AS `jornadas_coeficiente`,`c`.`valor_coeficiente` AS `valor_coeficiente`,`c`.`observaciones_coeficiente` AS `observaciones_coeficiente`,`c`.`activo_coeficiente` AS `activo_coeficiente`,(case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) AS `dias_evento`,((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) AS `subtotal_sin_coeficiente`,(case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then (((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) else ((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) end) AS `base_imponible`,(case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (`lp`.`porcentaje_iva_linea_ppto` / 100)) else (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (`lp`.`porcentaje_iva_linea_ppto` / 100)) end) AS `importe_iva`,(case when ((`lp`.`aplicar_coeficiente_linea_ppto` = 1) and (`lp`.`valor_coeficiente_linea_ppto` is not null) and (`lp`.`valor_coeficiente_linea_ppto` > 0)) then ((((`lp`.`cantidad_linea_ppto` * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * `lp`.`valor_coeficiente_linea_ppto`) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) else (((((case when ((`lp`.`fecha_inicio_linea_ppto` is not null) and (`lp`.`fecha_fin_linea_ppto` is not null)) then ((to_days(`lp`.`fecha_fin_linea_ppto`) - to_days(`lp`.`fecha_inicio_linea_ppto`)) + 1) else 1 end) * `lp`.`cantidad_linea_ppto`) * `lp`.`precio_unitario_linea_ppto`) * (1 - (`lp`.`descuento_linea_ppto` / 100))) * (1 + (`lp`.`porcentaje_iva_linea_ppto` / 100))) end) AS `total_linea`,`a`.`codigo_articulo` AS `codigo_articulo`,`a`.`nombre_articulo` AS `nombre_articulo`,`a`.`name_articulo` AS `name_articulo`,`a`.`imagen_articulo` AS `imagen_articulo`,`a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`,`a`.`es_kit_articulo` AS `es_kit_articulo`,`a`.`control_total_articulo` AS `control_total_articulo`,`a`.`activo_articulo` AS `activo_articulo`,`lp`.`id_impuesto` AS `id_impuesto`,`imp`.`tipo_impuesto` AS `tipo_impuesto`,`imp`.`tasa_impuesto` AS `tasa_impuesto`,`imp`.`descr_impuesto` AS `descr_impuesto`,`imp`.`activo_impuesto` AS `activo_impuesto`,`pv`.`id_presupuesto` AS `id_presupuesto`,`pv`.`numero_version_presupuesto` AS `numero_version_presupuesto`,`pv`.`estado_version_presupuesto` AS `estado_version_presupuesto`,`pv`.`fecha_creacion_version` AS `fecha_creacion_version`,`pv`.`fecha_envio_version` AS `fecha_envio_version`,`pv`.`fecha_aprobacion_version` AS `fecha_aprobacion_version`,`p`.`numero_presupuesto` AS `numero_presupuesto`,`p`.`fecha_presupuesto` AS `fecha_presupuesto`,`p`.`fecha_validez_presupuesto` AS `fecha_validez_presupuesto`,`p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`,`p`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`,`p`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`,`p`.`id_cliente` AS `id_cliente`,`p`.`id_estado_ppto` AS `id_estado_ppto`,`p`.`activo_presupuesto` AS `activo_presupuesto`,`cl`.`nombre_cliente` AS `nombre_cliente`,`cl`.`nif_cliente` AS `nif_cliente`,`cl`.`email_cliente` AS `email_cliente`,`cl`.`telefono_cliente` AS `telefono_cliente`,`cl`.`direccion_cliente` AS `direccion_cliente`,`cl`.`cp_cliente` AS `cp_cliente`,`cl`.`poblacion_cliente` AS `poblacion_cliente`,`cl`.`provincia_cliente` AS `provincia_cliente`,(case when ((`p`.`fecha_inicio_evento_presupuesto` is not null) and (`p`.`fecha_fin_evento_presupuesto` is not null)) then ((to_days(`p`.`fecha_fin_evento_presupuesto`) - to_days(`p`.`fecha_inicio_evento_presupuesto`)) + 1) else NULL end) AS `duracion_evento_dias`,`lp`.`created_at_linea_ppto` AS `created_at_linea_ppto`,`lp`.`updated_at_linea_ppto` AS `updated_at_linea_ppto` from ((((((`linea_presupuesto` `lp` join `presupuesto_version` `pv` on((`lp`.`id_version_presupuesto` = `pv`.`id_version_presupuesto`))) join `presupuesto` `p` on((`pv`.`id_presupuesto` = `p`.`id_presupuesto`))) join `cliente` `cl` on((`p`.`id_cliente` = `cl`.`id_cliente`))) left join `articulo` `a` on((`lp`.`id_articulo` = `a`.`id_articulo`))) left join `coeficiente` `c` on((`lp`.`id_coeficiente` = `c`.`id_coeficiente`))) left join `impuesto` `imp` on((`lp`.`id_impuesto` = `imp`.`id_impuesto`))) where (`p`.`activo_presupuesto` = true) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `v_presupuesto_totales`
--

/*!50001 DROP VIEW IF EXISTS `v_presupuesto_totales`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `v_presupuesto_totales` AS select `vlpc`.`id_version_presupuesto` AS `id_version_presupuesto`,`vlpc`.`numero_version_presupuesto` AS `numero_version_presupuesto`,`vlpc`.`estado_version_presupuesto` AS `estado_version_presupuesto`,`vlpc`.`fecha_creacion_version` AS `fecha_creacion_version`,`vlpc`.`fecha_envio_version` AS `fecha_envio_version`,`vlpc`.`fecha_aprobacion_version` AS `fecha_aprobacion_version`,`vlpc`.`id_presupuesto` AS `id_presupuesto`,`vlpc`.`numero_presupuesto` AS `numero_presupuesto`,`vlpc`.`fecha_presupuesto` AS `fecha_presupuesto`,`vlpc`.`fecha_validez_presupuesto` AS `fecha_validez_presupuesto`,`vlpc`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`,`vlpc`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`,`vlpc`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`,`vlpc`.`id_cliente` AS `id_cliente`,`vlpc`.`nombre_cliente` AS `nombre_cliente`,`vlpc`.`nif_cliente` AS `nif_cliente`,`vlpc`.`email_cliente` AS `email_cliente`,`vlpc`.`telefono_cliente` AS `telefono_cliente`,max(`vlpc`.`duracion_evento_dias`) AS `duracion_evento_dias`,sum(`vlpc`.`base_imponible`) AS `total_base_imponible`,sum(`vlpc`.`importe_iva`) AS `total_iva`,sum(`vlpc`.`total_linea`) AS `total_con_iva`,count(0) AS `cantidad_lineas_total`,count((case when (`vlpc`.`valor_coeficiente_linea_ppto` is not null) then 1 end)) AS `cantidad_lineas_con_coeficiente`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_21`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_21`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 21.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_21`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_10`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_10`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 10.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_10`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_4`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_4`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 4.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_4`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_0`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_0`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` = 0.00) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_0`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`base_imponible` else 0 end)) AS `base_iva_otros`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`importe_iva` else 0 end)) AS `importe_iva_otros`,sum((case when (`vlpc`.`porcentaje_iva_linea_ppto` not in (21.00,10.00,4.00,0.00)) then `vlpc`.`total_linea` else 0 end)) AS `total_iva_otros`,sum((`vlpc`.`subtotal_sin_coeficiente` - `vlpc`.`base_imponible`)) AS `ahorro_total_coeficientes`,min(`vlpc`.`created_at_linea_ppto`) AS `fecha_primera_linea_creada`,max(`vlpc`.`updated_at_linea_ppto`) AS `fecha_ultima_modificacion_linea` from `v_linea_presupuesto_calculada` `vlpc` group by `vlpc`.`id_version_presupuesto`,`vlpc`.`numero_version_presupuesto`,`vlpc`.`estado_version_presupuesto`,`vlpc`.`fecha_creacion_version`,`vlpc`.`fecha_envio_version`,`vlpc`.`fecha_aprobacion_version`,`vlpc`.`id_presupuesto`,`vlpc`.`numero_presupuesto`,`vlpc`.`fecha_presupuesto`,`vlpc`.`fecha_validez_presupuesto`,`vlpc`.`nombre_evento_presupuesto`,`vlpc`.`fecha_inicio_evento_presupuesto`,`vlpc`.`fecha_fin_evento_presupuesto`,`vlpc`.`id_cliente`,`vlpc`.`nombre_cliente`,`vlpc`.`nif_cliente`,`vlpc`.`email_cliente`,`vlpc`.`telefono_cliente` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vacaciones_con_nombre`
--

/*!50001 DROP VIEW IF EXISTS `vacaciones_con_nombre`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`administrator`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vacaciones_con_nombre` AS select `com_vacaciones`.`id_vacacion` AS `id_vacacion`,`com_vacaciones`.`id_comercial` AS `id_comercial`,`com_vacaciones`.`fecha_inicio` AS `fecha_inicio`,`com_vacaciones`.`fecha_fin` AS `fecha_fin`,`com_vacaciones`.`descripcion` AS `descripcion`,`com_vacaciones`.`activo_vacacion` AS `activo_vacacion`,(select concat(`comerciales`.`nombre`,' ',`comerciales`.`apellidos`) from `comerciales` where (`comerciales`.`id_comercial` = `com_vacaciones`.`id_comercial`)) AS `nombre_comercial` from `com_vacaciones` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_adjuntos_con_comunicante`
--

/*!50001 DROP VIEW IF EXISTS `vista_adjuntos_con_comunicante`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`administrator`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_adjuntos_con_comunicante` AS select `adjunto_llamada`.`id_adjunto` AS `id_adjunto`,`adjunto_llamada`.`id_llamada` AS `id_llamada`,`adjunto_llamada`.`nombre_archivo` AS `nombre_archivo`,`adjunto_llamada`.`tipo` AS `tipo`,`adjunto_llamada`.`fecha_subida` AS `fecha_subida`,`adjunto_llamada`.`estado` AS `estado`,(select `llamadas`.`nombre_comunicante` from `llamadas` where (`llamadas`.`id_llamada` = `adjunto_llamada`.`id_llamada`)) AS `nombre_comunicante` from `adjunto_llamada` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_articulo_completa`
--

/*!50001 DROP VIEW IF EXISTS `vista_articulo_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_articulo_completa` AS select `a`.`id_articulo` AS `id_articulo`,`a`.`codigo_articulo` AS `codigo_articulo`,`a`.`nombre_articulo` AS `nombre_articulo`,`a`.`name_articulo` AS `name_articulo`,`a`.`imagen_articulo` AS `imagen_articulo`,`a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`,`a`.`coeficiente_articulo` AS `coeficiente_articulo`,`a`.`es_kit_articulo` AS `es_kit_articulo`,`a`.`control_total_articulo` AS `control_total_articulo`,`a`.`no_facturar_articulo` AS `no_facturar_articulo`,`a`.`notas_presupuesto_articulo` AS `notas_presupuesto_articulo`,`a`.`notes_budget_articulo` AS `notes_budget_articulo`,`a`.`orden_obs_articulo` AS `orden_obs_articulo`,`a`.`observaciones_articulo` AS `observaciones_articulo`,`a`.`activo_articulo` AS `activo_articulo`,`a`.`created_at_articulo` AS `created_at_articulo`,`a`.`updated_at_articulo` AS `updated_at_articulo`,`f`.`id_familia` AS `id_familia`,`f`.`codigo_familia` AS `codigo_familia`,`f`.`nombre_familia` AS `nombre_familia`,`f`.`name_familia` AS `name_familia`,`f`.`descr_familia` AS `descr_familia`,`f`.`imagen_familia` AS `imagen_familia`,`f`.`coeficiente_familia` AS `coeficiente_familia`,`f`.`observaciones_presupuesto_familia` AS `observaciones_presupuesto_familia`,`f`.`orden_obs_familia` AS `orden_obs_familia`,`f`.`activo_familia` AS `activo_familia`,`g`.`id_grupo` AS `id_grupo`,`g`.`codigo_grupo` AS `codigo_grupo`,`g`.`nombre_grupo` AS `nombre_grupo`,`g`.`descripcion_grupo` AS `descripcion_grupo`,`g`.`activo_grupo` AS `activo_grupo`,`u`.`id_unidad` AS `id_unidad`,`u`.`nombre_unidad` AS `nombre_unidad`,`u`.`descr_unidad` AS `descr_unidad`,`u`.`simbolo_unidad` AS `simbolo_unidad`,`u`.`activo_unidad` AS `activo_unidad`,(case when (`a`.`coeficiente_articulo` is null) then `f`.`coeficiente_familia` when (`a`.`coeficiente_articulo` = 1) then 1 else 0 end) AS `coeficiente_efectivo`,coalesce(`a`.`imagen_articulo`,`f`.`imagen_familia`) AS `imagen_efectiva`,(case when (`a`.`imagen_articulo` is not null) then 'propia' when (`f`.`imagen_familia` is not null) then 'heredada' else 'sin_imagen' end) AS `tipo_imagen`,concat(coalesce(`g`.`nombre_grupo`,'Sin grupo'),' > ',`f`.`nombre_familia`,' > ',`a`.`nombre_articulo`) AS `jerarquia_completa`,(case when ((`a`.`precio_alquiler_articulo` > 0) and (`a`.`imagen_articulo` is not null) and (`a`.`notas_presupuesto_articulo` is not null)) then 1 else 0 end) AS `configuracion_completa` from (((`articulo` `a` join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) left join `unidad_medida` `u` on((`a`.`id_unidad` = `u`.`id_unidad`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_cliente_completa`
--

/*!50001 DROP VIEW IF EXISTS `vista_cliente_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_cliente_completa` AS select `c`.`id_cliente` AS `id_cliente`,`c`.`codigo_cliente` AS `codigo_cliente`,`c`.`nombre_cliente` AS `nombre_cliente`,`c`.`direccion_cliente` AS `direccion_cliente`,`c`.`cp_cliente` AS `cp_cliente`,`c`.`poblacion_cliente` AS `poblacion_cliente`,`c`.`provincia_cliente` AS `provincia_cliente`,`c`.`nif_cliente` AS `nif_cliente`,`c`.`telefono_cliente` AS `telefono_cliente`,`c`.`fax_cliente` AS `fax_cliente`,`c`.`web_cliente` AS `web_cliente`,`c`.`email_cliente` AS `email_cliente`,`c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`,`c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`,`c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`,`c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`,`c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`,`c`.`observaciones_cliente` AS `observaciones_cliente`,`c`.`activo_cliente` AS `activo_cliente`,`c`.`created_at_cliente` AS `created_at_cliente`,`c`.`updated_at_cliente` AS `updated_at_cliente`,`c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`,`fp`.`codigo_pago` AS `codigo_pago`,`fp`.`nombre_pago` AS `nombre_pago`,`fp`.`descuento_pago` AS `descuento_pago`,`fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`,`fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`,`fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`,`fp`.`dias_final_pago` AS `dias_final_pago`,`fp`.`observaciones_pago` AS `observaciones_pago`,`fp`.`activo_pago` AS `activo_pago`,`mp`.`id_metodo_pago` AS `id_metodo_pago`,`mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`,`mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`,`mp`.`observaciones_metodo_pago` AS `observaciones_metodo_pago`,`mp`.`activo_metodo_pago` AS `activo_metodo_pago`,(case when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_cliente`,(case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_forma_pago_cliente`,concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`,(case when (`c`.`direccion_facturacion_cliente` is not null) then concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) else NULL end) AS `direccion_facturacion_completa_cliente`,(case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`,(case when (`c`.`id_forma_pago_habitual` is null) then 'Sin configurar' when (`fp`.`activo_pago` = false) then 'Forma de pago inactiva' when (`mp`.`activo_metodo_pago` = false) then 'Método de pago inactivo' else 'Configurado' end) AS `estado_forma_pago_cliente` from ((`cliente` `c` left join `forma_pago` `fp` on((`c`.`id_forma_pago_habitual` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_cliente_ubicaciones`
--

/*!50001 DROP VIEW IF EXISTS `vista_cliente_ubicaciones`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_cliente_ubicaciones` AS select `c`.`id_cliente` AS `id_cliente`,`c`.`codigo_cliente` AS `codigo_cliente`,`c`.`nombre_cliente` AS `nombre_cliente`,`c`.`nif_cliente` AS `nif_cliente`,`c`.`telefono_cliente` AS `telefono_cliente`,`c`.`email_cliente` AS `email_cliente`,`c`.`activo_cliente` AS `activo_cliente`,`u`.`id_ubicacion` AS `id_ubicacion`,`u`.`nombre_ubicacion` AS `nombre_ubicacion`,`u`.`direccion_ubicacion` AS `direccion_ubicacion`,`u`.`codigo_postal_ubicacion` AS `codigo_postal_ubicacion`,`u`.`poblacion_ubicacion` AS `poblacion_ubicacion`,`u`.`provincia_ubicacion` AS `provincia_ubicacion`,`u`.`pais_ubicacion` AS `pais_ubicacion`,`u`.`persona_contacto_ubicacion` AS `persona_contacto_ubicacion`,`u`.`telefono_contacto_ubicacion` AS `telefono_contacto_ubicacion`,`u`.`email_contacto_ubicacion` AS `email_contacto_ubicacion`,`u`.`observaciones_ubicacion` AS `observaciones_ubicacion`,`u`.`es_principal_ubicacion` AS `es_principal_ubicacion`,`u`.`activo_ubicacion` AS `activo_ubicacion`,concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`,concat_ws(', ',`u`.`direccion_ubicacion`,concat(`u`.`codigo_postal_ubicacion`,' ',`u`.`poblacion_ubicacion`),`u`.`provincia_ubicacion`,(case when (`u`.`pais_ubicacion` <> 'España') then `u`.`pais_ubicacion` else NULL end)) AS `direccion_completa_ubicacion`,(case when (`u`.`es_principal_ubicacion` = true) then 'Principal' else 'Secundaria' end) AS `tipo_ubicacion`,(case when (`c`.`activo_cliente` = false) then 'Cliente inactivo' when (`u`.`activo_ubicacion` = false) then 'Ubicación inactiva' else 'Activa' end) AS `estado_completo`,(case when (`u`.`persona_contacto_ubicacion` is not null) then true else false end) AS `tiene_contacto_propio`,(select count(0) from `cliente_ubicacion` `cu` where ((`cu`.`id_cliente` = `c`.`id_cliente`) and (`cu`.`activo_ubicacion` = true))) AS `total_ubicaciones_cliente` from (`cliente` `c` join `cliente_ubicacion` `u` on((`c`.`id_cliente` = `u`.`id_cliente`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_costos_furgoneta`
--

/*!50001 DROP VIEW IF EXISTS `vista_costos_furgoneta`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_costos_furgoneta` AS select `f`.`id_furgoneta` AS `id_furgoneta`,`f`.`matricula_furgoneta` AS `matricula_furgoneta`,`f`.`marca_furgoneta` AS `marca_furgoneta`,`f`.`modelo_furgoneta` AS `modelo_furgoneta`,`f`.`anio_furgoneta` AS `anio_furgoneta`,coalesce(sum(`m`.`costo_mantenimiento`),0) AS `costo_total`,coalesce(sum((case when (year(`m`.`fecha_mantenimiento`) = year(curdate())) then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_anio_actual`,coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'revision') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_revisiones`,coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'reparacion') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_reparaciones`,coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'itv') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_itv`,coalesce(sum((case when (`m`.`tipo_mantenimiento` = 'neumaticos') then `m`.`costo_mantenimiento` else 0 end)),0) AS `costo_neumaticos`,count(`m`.`id_mantenimiento`) AS `total_mantenimientos`,coalesce(avg(`m`.`costo_mantenimiento`),0) AS `costo_promedio`,max(`m`.`fecha_mantenimiento`) AS `fecha_ultimo_mantenimiento`,(select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) AS `kilometraje_actual`,(case when ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) > 0) then (coalesce(sum(`m`.`costo_mantenimiento`),0) / (select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`))) else NULL end) AS `costo_por_km` from (`furgoneta` `f` left join `furgoneta_mantenimiento` `m` on(((`f`.`id_furgoneta` = `m`.`id_furgoneta`) and (`m`.`activo_mantenimiento` = 1)))) group by `f`.`id_furgoneta`,`f`.`matricula_furgoneta`,`f`.`marca_furgoneta`,`f`.`modelo_furgoneta`,`f`.`anio_furgoneta` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_elementos_completa`
--

/*!50001 DROP VIEW IF EXISTS `vista_elementos_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_elementos_completa` AS select `e`.`id_elemento` AS `id_elemento`,`e`.`codigo_elemento` AS `codigo_elemento`,`e`.`codigo_barras_elemento` AS `codigo_barras_elemento`,`e`.`descripcion_elemento` AS `descripcion_elemento`,`e`.`numero_serie_elemento` AS `numero_serie_elemento`,`e`.`modelo_elemento` AS `modelo_elemento`,`e`.`nave_elemento` AS `nave_elemento`,`e`.`pasillo_columna_elemento` AS `pasillo_columna_elemento`,`e`.`altura_elemento` AS `altura_elemento`,concat_ws(' | ',coalesce(`e`.`nave_elemento`,''),coalesce(`e`.`pasillo_columna_elemento`,''),coalesce(`e`.`altura_elemento`,'')) AS `ubicacion_completa_elemento`,`e`.`fecha_compra_elemento` AS `fecha_compra_elemento`,`e`.`precio_compra_elemento` AS `precio_compra_elemento`,`e`.`fecha_alta_elemento` AS `fecha_alta_elemento`,`e`.`fecha_fin_garantia_elemento` AS `fecha_fin_garantia_elemento`,`e`.`proximo_mantenimiento_elemento` AS `proximo_mantenimiento_elemento`,`e`.`observaciones_elemento` AS `observaciones_elemento`,`e`.`es_propio_elemento` AS `es_propio_elemento`,`e`.`id_proveedor_compra_elemento` AS `id_proveedor_compra_elemento`,`e`.`id_proveedor_alquiler_elemento` AS `id_proveedor_alquiler_elemento`,`e`.`precio_dia_alquiler_elemento` AS `precio_dia_alquiler_elemento`,`e`.`id_forma_pago_alquiler_elemento` AS `id_forma_pago_alquiler_elemento`,`e`.`observaciones_alquiler_elemento` AS `observaciones_alquiler_elemento`,`prov_compra`.`codigo_proveedor` AS `codigo_proveedor_compra`,`prov_compra`.`nombre_proveedor` AS `nombre_proveedor_compra`,`prov_compra`.`telefono_proveedor` AS `telefono_proveedor_compra`,`prov_compra`.`email_proveedor` AS `email_proveedor_compra`,`prov_compra`.`nif_proveedor` AS `nif_proveedor_compra`,`prov_alquiler`.`codigo_proveedor` AS `codigo_proveedor_alquiler`,`prov_alquiler`.`nombre_proveedor` AS `nombre_proveedor_alquiler`,`prov_alquiler`.`telefono_proveedor` AS `telefono_proveedor_alquiler`,`prov_alquiler`.`email_proveedor` AS `email_proveedor_alquiler`,`prov_alquiler`.`nif_proveedor` AS `nif_proveedor_alquiler`,`prov_alquiler`.`persona_contacto_proveedor` AS `persona_contacto_proveedor_alquiler`,`fp_alquiler`.`codigo_pago` AS `codigo_forma_pago_alquiler`,`fp_alquiler`.`nombre_pago` AS `nombre_forma_pago_alquiler`,`fp_alquiler`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_alquiler`,`fp_alquiler`.`dias_anticipo_pago` AS `dias_anticipo_alquiler`,`fp_alquiler`.`porcentaje_final_pago` AS `porcentaje_final_alquiler`,`fp_alquiler`.`dias_final_pago` AS `dias_final_alquiler`,`fp_alquiler`.`descuento_pago` AS `descuento_forma_pago_alquiler`,`mp_alquiler`.`codigo_metodo_pago` AS `codigo_metodo_pago_alquiler`,`mp_alquiler`.`nombre_metodo_pago` AS `nombre_metodo_pago_alquiler`,`a`.`id_articulo` AS `id_articulo`,`a`.`codigo_articulo` AS `codigo_articulo`,`a`.`nombre_articulo` AS `nombre_articulo`,`a`.`name_articulo` AS `name_articulo`,`a`.`precio_alquiler_articulo` AS `precio_alquiler_articulo`,`f`.`id_familia` AS `id_familia`,`f`.`codigo_familia` AS `codigo_familia`,`f`.`nombre_familia` AS `nombre_familia`,`f`.`name_familia` AS `name_familia`,`g`.`id_grupo` AS `id_grupo`,`g`.`codigo_grupo` AS `codigo_grupo`,`g`.`nombre_grupo` AS `nombre_grupo`,`m`.`id_marca` AS `id_marca`,`m`.`codigo_marca` AS `codigo_marca`,`m`.`nombre_marca` AS `nombre_marca`,`est`.`id_estado_elemento` AS `id_estado_elemento`,`est`.`codigo_estado_elemento` AS `codigo_estado_elemento`,`est`.`descripcion_estado_elemento` AS `descripcion_estado_elemento`,`est`.`color_estado_elemento` AS `color_estado_elemento`,`est`.`permite_alquiler_estado_elemento` AS `permite_alquiler_estado_elemento`,`e`.`activo_elemento` AS `activo_elemento`,`e`.`created_at_elemento` AS `created_at_elemento`,`e`.`updated_at_elemento` AS `updated_at_elemento`,concat_ws(' > ',coalesce(`g`.`nombre_grupo`,'Sin grupo'),`f`.`nombre_familia`,`a`.`nombre_articulo`,`e`.`descripcion_elemento`) AS `jerarquia_completa_elemento`,(case when (`e`.`es_propio_elemento` = true) then 'PROPIO' else 'ALQUILADO A PROVEEDOR' end) AS `tipo_propiedad_elemento`,(case when (`e`.`es_propio_elemento` = true) then `prov_compra`.`nombre_proveedor` else `prov_alquiler`.`nombre_proveedor` end) AS `proveedor_principal_elemento`,(case when (`e`.`es_propio_elemento` = true) then 'N/A - Equipo propio' when (`e`.`id_proveedor_alquiler_elemento` is null) then 'Sin proveedor asignado' when ((`e`.`precio_dia_alquiler_elemento` is null) or (`e`.`precio_dia_alquiler_elemento` = 0)) then 'Proveedor asignado - Falta precio' when (`e`.`id_forma_pago_alquiler_elemento` is null) then 'Proveedor y precio OK - Falta forma de pago' else 'Completamente configurado' end) AS `estado_configuracion_alquiler`,(case when ((`e`.`es_propio_elemento` = false) and (`fp_alquiler`.`id_pago` is not null)) then (case when (`fp_alquiler`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp_alquiler`.`nombre_metodo_pago`,' - ',`fp_alquiler`.`nombre_pago`,(case when (`fp_alquiler`.`descuento_pago` > 0) then concat(' (Dto: ',`fp_alquiler`.`descuento_pago`,'%)') else '' end)) else concat(`mp_alquiler`.`nombre_metodo_pago`,' - ',`fp_alquiler`.`porcentaje_anticipo_pago`,'% + ',`fp_alquiler`.`porcentaje_final_pago`,'%') end) else NULL end) AS `descripcion_forma_pago_alquiler`,(case when ((`e`.`es_propio_elemento` = false) and (`e`.`precio_dia_alquiler_elemento` is not null)) then round((`e`.`precio_dia_alquiler_elemento` * 30),2) else NULL end) AS `costo_mensual_estimado_alquiler`,(to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) AS `dias_en_servicio_elemento`,round(((to_days(curdate()) - to_days(`e`.`fecha_alta_elemento`)) / 365.25),2) AS `anios_en_servicio_elemento` from (((((((((`elemento` `e` join `articulo` `a` on((`e`.`id_articulo_elemento` = `a`.`id_articulo`))) join `familia` `f` on((`a`.`id_familia` = `f`.`id_familia`))) left join `grupo_articulo` `g` on((`f`.`id_grupo` = `g`.`id_grupo`))) left join `marca` `m` on((`e`.`id_marca_elemento` = `m`.`id_marca`))) join `estado_elemento` `est` on((`e`.`id_estado_elemento` = `est`.`id_estado_elemento`))) left join `proveedor` `prov_compra` on((`e`.`id_proveedor_compra_elemento` = `prov_compra`.`id_proveedor`))) left join `proveedor` `prov_alquiler` on((`e`.`id_proveedor_alquiler_elemento` = `prov_alquiler`.`id_proveedor`))) left join `forma_pago` `fp_alquiler` on((`e`.`id_forma_pago_alquiler_elemento` = `fp_alquiler`.`id_pago`))) left join `metodo_pago` `mp_alquiler` on((`fp_alquiler`.`id_metodo_pago` = `mp_alquiler`.`id_metodo_pago`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_furgoneta_completa`
--

/*!50001 DROP VIEW IF EXISTS `vista_furgoneta_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_furgoneta_completa` AS select `f`.`id_furgoneta` AS `id_furgoneta`,`f`.`matricula_furgoneta` AS `matricula_furgoneta`,`f`.`marca_furgoneta` AS `marca_furgoneta`,`f`.`modelo_furgoneta` AS `modelo_furgoneta`,`f`.`anio_furgoneta` AS `anio_furgoneta`,`f`.`numero_bastidor_furgoneta` AS `numero_bastidor_furgoneta`,`f`.`kilometros_entre_revisiones_furgoneta` AS `kilometros_entre_revisiones_furgoneta`,`f`.`fecha_proxima_itv_furgoneta` AS `fecha_proxima_itv_furgoneta`,`f`.`fecha_vencimiento_seguro_furgoneta` AS `fecha_vencimiento_seguro_furgoneta`,`f`.`compania_seguro_furgoneta` AS `compania_seguro_furgoneta`,`f`.`numero_poliza_seguro_furgoneta` AS `numero_poliza_seguro_furgoneta`,`f`.`capacidad_carga_kg_furgoneta` AS `capacidad_carga_kg_furgoneta`,`f`.`capacidad_carga_m3_furgoneta` AS `capacidad_carga_m3_furgoneta`,`f`.`tipo_combustible_furgoneta` AS `tipo_combustible_furgoneta`,`f`.`consumo_medio_furgoneta` AS `consumo_medio_furgoneta`,`f`.`taller_habitual_furgoneta` AS `taller_habitual_furgoneta`,`f`.`telefono_taller_furgoneta` AS `telefono_taller_furgoneta`,`f`.`estado_furgoneta` AS `estado_furgoneta`,`f`.`observaciones_furgoneta` AS `observaciones_furgoneta`,`f`.`activo_furgoneta` AS `activo_furgoneta`,`f`.`created_at_furgoneta` AS `created_at_furgoneta`,`f`.`updated_at_furgoneta` AS `updated_at_furgoneta`,(select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) AS `kilometraje_actual`,(select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1) AS `fecha_ultimo_registro_km`,(select count(0) from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1))) AS `total_mantenimientos`,(select sum(`furgoneta_mantenimiento`.`costo_mantenimiento`) from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1))) AS `costo_total_mantenimientos`,(select `furgoneta_mantenimiento`.`fecha_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1) AS `fecha_ultimo_mantenimiento`,(case when ((to_days(`f`.`fecha_proxima_itv_furgoneta`) - to_days(curdate())) < 0) then 'ITV_VENCIDA' when ((to_days(`f`.`fecha_proxima_itv_furgoneta`) - to_days(curdate())) <= 30) then 'ITV_PROXIMA' else 'ITV_OK' end) AS `estado_itv`,(case when ((to_days(`f`.`fecha_vencimiento_seguro_furgoneta`) - to_days(curdate())) < 0) then 'SEGURO_VENCIDO' when ((to_days(`f`.`fecha_vencimiento_seguro_furgoneta`) - to_days(curdate())) <= 30) then 'SEGURO_PROXIMO' else 'SEGURO_OK' end) AS `estado_seguro`,(case when ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) is not null) then ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) - coalesce((select `furgoneta_mantenimiento`.`kilometraje_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`tipo_mantenimiento` = 'revision') and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1),0)) else NULL end) AS `km_desde_ultima_revision`,(case when (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) is not null) and (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `f`.`id_furgoneta`)) - coalesce((select `furgoneta_mantenimiento`.`kilometraje_mantenimiento` from `furgoneta_mantenimiento` where ((`furgoneta_mantenimiento`.`id_furgoneta` = `f`.`id_furgoneta`) and (`furgoneta_mantenimiento`.`tipo_mantenimiento` = 'revision') and (`furgoneta_mantenimiento`.`activo_mantenimiento` = 1)) order by `furgoneta_mantenimiento`.`fecha_mantenimiento` desc limit 1),0)) >= `f`.`kilometros_entre_revisiones_furgoneta`)) then true else false end) AS `necesita_revision` from `furgoneta` `f` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_kilometraje_completo`
--

/*!50001 DROP VIEW IF EXISTS `vista_kilometraje_completo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_kilometraje_completo` AS select `rk`.`id_registro_km` AS `id_registro_km`,`rk`.`id_furgoneta` AS `id_furgoneta`,`rk`.`fecha_registro_km` AS `fecha_registro_km`,`rk`.`kilometraje_registrado_km` AS `kilometraje_registrado_km`,`rk`.`tipo_registro_km` AS `tipo_registro_km`,`rk`.`observaciones_registro_km` AS `observaciones_registro_km`,`rk`.`created_at_registro_km` AS `created_at_registro_km`,`rk`.`updated_at_registro_km` AS `updated_at_registro_km`,`f`.`matricula_furgoneta` AS `matricula_furgoneta`,`f`.`marca_furgoneta` AS `marca_furgoneta`,`f`.`modelo_furgoneta` AS `modelo_furgoneta`,`f`.`estado_furgoneta` AS `estado_furgoneta`,coalesce((`rk`.`kilometraje_registrado_km` - lag(`rk`.`kilometraje_registrado_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) ),0) AS `km_recorridos`,coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),0) AS `dias_transcurridos`,(case when (coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),0) > 0) then round((coalesce((`rk`.`kilometraje_registrado_km` - lag(`rk`.`kilometraje_registrado_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) ),0) / coalesce((to_days(`rk`.`fecha_registro_km`) - to_days(lag(`rk`.`fecha_registro_km`) OVER (PARTITION BY `rk`.`id_furgoneta` ORDER BY `rk`.`fecha_registro_km`,`rk`.`kilometraje_registrado_km` ) )),1)),2) else 0 end) AS `km_promedio_diario` from (`furgoneta_registro_kilometraje` `rk` join `furgoneta` `f` on((`rk`.`id_furgoneta` = `f`.`id_furgoneta`))) order by `rk`.`id_furgoneta`,`rk`.`fecha_registro_km` desc,`rk`.`kilometraje_registrado_km` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_kit_completa`
--

/*!50001 DROP VIEW IF EXISTS `vista_kit_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_kit_completa` AS select `k`.`id_kit` AS `id_kit`,`k`.`cantidad_kit` AS `cantidad_kit`,`k`.`activo_kit` AS `activo_kit`,`k`.`created_at_kit` AS `created_at_kit`,`k`.`updated_at_kit` AS `updated_at_kit`,`k`.`id_articulo_maestro` AS `id_articulo_maestro`,`am`.`codigo_articulo` AS `codigo_articulo_maestro`,`am`.`nombre_articulo` AS `nombre_articulo_maestro`,`am`.`name_articulo` AS `name_articulo_maestro`,`am`.`precio_alquiler_articulo` AS `precio_articulo_maestro`,`am`.`es_kit_articulo` AS `es_kit_articulo_maestro`,`am`.`activo_articulo` AS `activo_articulo_maestro`,`k`.`id_articulo_componente` AS `id_articulo_componente`,`ac`.`codigo_articulo` AS `codigo_articulo_componente`,`ac`.`nombre_articulo` AS `nombre_articulo_componente`,`ac`.`name_articulo` AS `name_articulo_componente`,`ac`.`precio_alquiler_articulo` AS `precio_articulo_componente`,`ac`.`es_kit_articulo` AS `es_kit_articulo_componente`,`ac`.`activo_articulo` AS `activo_articulo_componente`,(`k`.`cantidad_kit` * `ac`.`precio_alquiler_articulo`) AS `subtotal_componente`,(select count(0) from `kit` `k2` where ((`k2`.`id_articulo_maestro` = `k`.`id_articulo_maestro`) and (`k2`.`activo_kit` = 1))) AS `total_componentes_kit`,(select sum((`k2`.`cantidad_kit` * `a2`.`precio_alquiler_articulo`)) from (`kit` `k2` join `articulo` `a2` on((`k2`.`id_articulo_componente` = `a2`.`id_articulo`))) where ((`k2`.`id_articulo_maestro` = `k`.`id_articulo_maestro`) and (`k2`.`activo_kit` = 1) and (`a2`.`activo_articulo` = 1))) AS `precio_total_kit` from ((`kit` `k` join `articulo` `am` on((`k`.`id_articulo_maestro` = `am`.`id_articulo`))) join `articulo` `ac` on((`k`.`id_articulo_componente` = `ac`.`id_articulo`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_mantenimiento_completo`
--

/*!50001 DROP VIEW IF EXISTS `vista_mantenimiento_completo`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_mantenimiento_completo` AS select `m`.`id_mantenimiento` AS `id_mantenimiento`,`m`.`id_furgoneta` AS `id_furgoneta`,`m`.`fecha_mantenimiento` AS `fecha_mantenimiento`,`m`.`tipo_mantenimiento` AS `tipo_mantenimiento`,`m`.`descripcion_mantenimiento` AS `descripcion_mantenimiento`,`m`.`kilometraje_mantenimiento` AS `kilometraje_mantenimiento`,`m`.`costo_mantenimiento` AS `costo_mantenimiento`,`m`.`numero_factura_mantenimiento` AS `numero_factura_mantenimiento`,`m`.`taller_mantenimiento` AS `taller_mantenimiento`,`m`.`telefono_taller_mantenimiento` AS `telefono_taller_mantenimiento`,`m`.`direccion_taller_mantenimiento` AS `direccion_taller_mantenimiento`,`m`.`resultado_itv` AS `resultado_itv`,`m`.`fecha_proxima_itv` AS `fecha_proxima_itv`,`m`.`garantia_hasta_mantenimiento` AS `garantia_hasta_mantenimiento`,`m`.`observaciones_mantenimiento` AS `observaciones_mantenimiento`,`m`.`activo_mantenimiento` AS `activo_mantenimiento`,`m`.`created_at_mantenimiento` AS `created_at_mantenimiento`,`m`.`updated_at_mantenimiento` AS `updated_at_mantenimiento`,`f`.`matricula_furgoneta` AS `matricula_furgoneta`,`f`.`marca_furgoneta` AS `marca_furgoneta`,`f`.`modelo_furgoneta` AS `modelo_furgoneta`,`f`.`anio_furgoneta` AS `anio_furgoneta`,`f`.`estado_furgoneta` AS `estado_furgoneta`,(case when (`m`.`garantia_hasta_mantenimiento` is null) then 'SIN_GARANTIA' when (`m`.`garantia_hasta_mantenimiento` < curdate()) then 'GARANTIA_VENCIDA' when ((to_days(`m`.`garantia_hasta_mantenimiento`) - to_days(curdate())) <= 30) then 'GARANTIA_PROXIMA' else 'GARANTIA_VIGENTE' end) AS `estado_garantia`,(to_days(curdate()) - to_days(`m`.`fecha_mantenimiento`)) AS `dias_desde_mantenimiento`,(case when (((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `m`.`id_furgoneta`)) is not null) and (`m`.`kilometraje_mantenimiento` is not null)) then ((select max(`furgoneta_registro_kilometraje`.`kilometraje_registrado_km`) from `furgoneta_registro_kilometraje` where (`furgoneta_registro_kilometraje`.`id_furgoneta` = `m`.`id_furgoneta`)) - `m`.`kilometraje_mantenimiento`) else NULL end) AS `km_desde_mantenimiento` from (`furgoneta_mantenimiento` `m` join `furgoneta` `f` on((`m`.`id_furgoneta` = `f`.`id_furgoneta`))) where (`m`.`activo_mantenimiento` = 1) order by `m`.`fecha_mantenimiento` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_presupuesto_completa`
--

/*!50001 DROP VIEW IF EXISTS `vista_presupuesto_completa`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_presupuesto_completa` AS select `p`.`id_presupuesto` AS `id_presupuesto`,`p`.`numero_presupuesto` AS `numero_presupuesto`,`p`.`version_actual_presupuesto` AS `version_actual_presupuesto`,`p`.`fecha_presupuesto` AS `fecha_presupuesto`,`p`.`fecha_validez_presupuesto` AS `fecha_validez_presupuesto`,`p`.`fecha_inicio_evento_presupuesto` AS `fecha_inicio_evento_presupuesto`,`p`.`fecha_fin_evento_presupuesto` AS `fecha_fin_evento_presupuesto`,`p`.`numero_pedido_cliente_presupuesto` AS `numero_pedido_cliente_presupuesto`,`p`.`aplicar_coeficientes_presupuesto` AS `aplicar_coeficientes_presupuesto`,`p`.`descuento_presupuesto` AS `descuento_presupuesto`,`p`.`nombre_evento_presupuesto` AS `nombre_evento_presupuesto`,`p`.`direccion_evento_presupuesto` AS `direccion_evento_presupuesto`,`p`.`poblacion_evento_presupuesto` AS `poblacion_evento_presupuesto`,`p`.`cp_evento_presupuesto` AS `cp_evento_presupuesto`,`p`.`provincia_evento_presupuesto` AS `provincia_evento_presupuesto`,`p`.`observaciones_cabecera_presupuesto` AS `observaciones_cabecera_presupuesto`,`p`.`observaciones_pie_presupuesto` AS `observaciones_pie_presupuesto`,`p`.`observaciones_cabecera_ingles_presupuesto` AS `observaciones_cabecera_ingles_presupuesto`,`p`.`observaciones_pie_ingles_presupuesto` AS `observaciones_pie_ingles_presupuesto`,`p`.`mostrar_obs_familias_presupuesto` AS `mostrar_obs_familias_presupuesto`,`p`.`mostrar_obs_articulos_presupuesto` AS `mostrar_obs_articulos_presupuesto`,`p`.`observaciones_internas_presupuesto` AS `observaciones_internas_presupuesto`,`p`.`activo_presupuesto` AS `activo_presupuesto`,`p`.`created_at_presupuesto` AS `created_at_presupuesto`,`p`.`updated_at_presupuesto` AS `updated_at_presupuesto`,`c`.`id_cliente` AS `id_cliente`,`c`.`codigo_cliente` AS `codigo_cliente`,`c`.`nombre_cliente` AS `nombre_cliente`,`c`.`nif_cliente` AS `nif_cliente`,`c`.`direccion_cliente` AS `direccion_cliente`,`c`.`cp_cliente` AS `cp_cliente`,`c`.`poblacion_cliente` AS `poblacion_cliente`,`c`.`provincia_cliente` AS `provincia_cliente`,`c`.`telefono_cliente` AS `telefono_cliente`,`c`.`email_cliente` AS `email_cliente`,`c`.`porcentaje_descuento_cliente` AS `porcentaje_descuento_cliente`,`c`.`nombre_facturacion_cliente` AS `nombre_facturacion_cliente`,`c`.`direccion_facturacion_cliente` AS `direccion_facturacion_cliente`,`c`.`cp_facturacion_cliente` AS `cp_facturacion_cliente`,`c`.`poblacion_facturacion_cliente` AS `poblacion_facturacion_cliente`,`c`.`provincia_facturacion_cliente` AS `provincia_facturacion_cliente`,`cc`.`id_contacto_cliente` AS `id_contacto_cliente`,`cc`.`nombre_contacto_cliente` AS `nombre_contacto_cliente`,`cc`.`apellidos_contacto_cliente` AS `apellidos_contacto_cliente`,`cc`.`telefono_contacto_cliente` AS `telefono_contacto_cliente`,`cc`.`email_contacto_cliente` AS `email_contacto_cliente`,`ep`.`id_estado_ppto` AS `id_estado_ppto`,`ep`.`codigo_estado_ppto` AS `codigo_estado_ppto`,`ep`.`nombre_estado_ppto` AS `nombre_estado_ppto`,`ep`.`color_estado_ppto` AS `color_estado_ppto`,`ep`.`orden_estado_ppto` AS `orden_estado_ppto`,`fp`.`id_pago` AS `id_forma_pago`,`fp`.`codigo_pago` AS `codigo_pago`,`fp`.`nombre_pago` AS `nombre_pago`,`fp`.`porcentaje_anticipo_pago` AS `porcentaje_anticipo_pago`,`fp`.`dias_anticipo_pago` AS `dias_anticipo_pago`,`fp`.`porcentaje_final_pago` AS `porcentaje_final_pago`,`fp`.`dias_final_pago` AS `dias_final_pago`,`fp`.`descuento_pago` AS `descuento_pago`,`mp`.`id_metodo_pago` AS `id_metodo_pago`,`mp`.`codigo_metodo_pago` AS `codigo_metodo_pago`,`mp`.`nombre_metodo_pago` AS `nombre_metodo_pago`,`mc`.`id_metodo` AS `id_metodo_contacto`,`mc`.`nombre` AS `nombre_metodo_contacto`,`c`.`id_forma_pago_habitual` AS `id_forma_pago_habitual`,`fph`.`nombre_pago` AS `nombre_forma_pago_habitual_cliente`,concat_ws(', ',`p`.`direccion_evento_presupuesto`,concat(`p`.`cp_evento_presupuesto`,' ',`p`.`poblacion_evento_presupuesto`),`p`.`provincia_evento_presupuesto`) AS `direccion_completa_evento_presupuesto`,concat_ws(', ',`c`.`direccion_cliente`,concat(`c`.`cp_cliente`,' ',`c`.`poblacion_cliente`),`c`.`provincia_cliente`) AS `direccion_completa_cliente`,concat_ws(', ',`c`.`direccion_facturacion_cliente`,concat(`c`.`cp_facturacion_cliente`,' ',`c`.`poblacion_facturacion_cliente`),`c`.`provincia_facturacion_cliente`) AS `direccion_facturacion_completa_cliente`,concat_ws(' ',`cc`.`nombre_contacto_cliente`,`cc`.`apellidos_contacto_cliente`) AS `nombre_completo_contacto`,(to_days(`p`.`fecha_validez_presupuesto`) - to_days(curdate())) AS `dias_validez_restantes`,(case when (`p`.`fecha_validez_presupuesto` is null) then 'Sin fecha de validez' when (`p`.`fecha_validez_presupuesto` < curdate()) then 'Caducado' when (`p`.`fecha_validez_presupuesto` = curdate()) then 'Caduca hoy' when ((to_days(`p`.`fecha_validez_presupuesto`) - to_days(curdate())) <= 7) then 'Próximo a caducar' else 'Vigente' end) AS `estado_validez_presupuesto`,((to_days(`p`.`fecha_fin_evento_presupuesto`) - to_days(`p`.`fecha_inicio_evento_presupuesto`)) + 1) AS `duracion_evento_dias`,(to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) AS `dias_hasta_inicio_evento`,(to_days(`p`.`fecha_fin_evento_presupuesto`) - to_days(curdate())) AS `dias_hasta_fin_evento`,(case when (`p`.`fecha_inicio_evento_presupuesto` is null) then 'Sin fecha de evento' when ((`p`.`fecha_inicio_evento_presupuesto` < curdate()) and (`p`.`fecha_fin_evento_presupuesto` < curdate())) then 'Evento finalizado' when ((`p`.`fecha_inicio_evento_presupuesto` <= curdate()) and (`p`.`fecha_fin_evento_presupuesto` >= curdate())) then 'Evento en curso' when (`p`.`fecha_inicio_evento_presupuesto` = curdate()) then 'Evento HOY' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) = 1) then 'Evento MAÑANA' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 7) then 'Evento esta semana' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 30) then 'Evento este mes' else 'Evento futuro' end) AS `estado_evento_presupuesto`,(case when (`p`.`fecha_inicio_evento_presupuesto` is null) then 'Sin prioridad' when (`p`.`fecha_inicio_evento_presupuesto` = curdate()) then 'HOY' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) = 1) then 'MAÑANA' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 7) then 'Esta semana' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 15) then 'Próximo' when ((to_days(`p`.`fecha_inicio_evento_presupuesto`) - to_days(curdate())) <= 30) then 'Este mes' else 'Futuro' end) AS `prioridad_presupuesto`,(case when (`fp`.`id_pago` is null) then 'Sin forma de pago' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then 'Pago único' when (`fp`.`porcentaje_anticipo_pago` < 100.00) then 'Pago fraccionado' else 'Sin forma de pago' end) AS `tipo_pago_presupuesto`,(case when (`fp`.`id_pago` is null) then 'Sin forma de pago asignada' when (`fp`.`porcentaje_anticipo_pago` = 100.00) then concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`nombre_pago`,(case when (`fp`.`descuento_pago` > 0) then concat(' (Dto: ',`fp`.`descuento_pago`,'%)') else '' end)) else concat(`mp`.`nombre_metodo_pago`,' - ',`fp`.`porcentaje_anticipo_pago`,'% + ',`fp`.`porcentaje_final_pago`,'%') end) AS `descripcion_completa_forma_pago`,(case when (`fp`.`dias_anticipo_pago` = 0) then `p`.`fecha_presupuesto` else (`p`.`fecha_presupuesto` + interval `fp`.`dias_anticipo_pago` day) end) AS `fecha_vencimiento_anticipo`,(case when ((`fp`.`dias_final_pago` = 0) and (`p`.`fecha_fin_evento_presupuesto` is not null)) then `p`.`fecha_fin_evento_presupuesto` when (`fp`.`dias_final_pago` > 0) then (`p`.`fecha_presupuesto` + interval `fp`.`dias_final_pago` day) when ((`fp`.`dias_final_pago` < 0) and (`p`.`fecha_inicio_evento_presupuesto` is not null)) then (`p`.`fecha_inicio_evento_presupuesto` + interval `fp`.`dias_final_pago` day) else NULL end) AS `fecha_vencimiento_final`,(case when (`p`.`descuento_presupuesto` = `c`.`porcentaje_descuento_cliente`) then 'Igual al habitual' when (`p`.`descuento_presupuesto` > `c`.`porcentaje_descuento_cliente`) then 'Mayor al habitual' when (`p`.`descuento_presupuesto` < `c`.`porcentaje_descuento_cliente`) then 'Menor al habitual' else 'Sin comparar' end) AS `comparacion_descuento`,(case when (`p`.`descuento_presupuesto` = 0.00) then 'Sin descuento' when ((`p`.`descuento_presupuesto` > 0.00) and (`p`.`descuento_presupuesto` <= 5.00)) then concat('Descuento bajo: ',`p`.`descuento_presupuesto`,'%') when ((`p`.`descuento_presupuesto` > 5.00) and (`p`.`descuento_presupuesto` <= 15.00)) then concat('Descuento medio: ',`p`.`descuento_presupuesto`,'%') when (`p`.`descuento_presupuesto` > 15.00) then concat('Descuento alto: ',`p`.`descuento_presupuesto`,'%') else 'Sin descuento' end) AS `estado_descuento_presupuesto`,(case when (`p`.`descuento_presupuesto` > 0.00) then true else false end) AS `aplica_descuento_presupuesto`,(`p`.`descuento_presupuesto` - `c`.`porcentaje_descuento_cliente`) AS `diferencia_descuento`,(case when (`c`.`direccion_facturacion_cliente` is not null) then true else false end) AS `tiene_direccion_facturacion_diferente`,(to_days(curdate()) - to_days(`p`.`fecha_presupuesto`)) AS `dias_desde_emision`,`pv`.`id_version_presupuesto` AS `id_version_actual`,`pv`.`numero_version_presupuesto` AS `numero_version_actual`,`pv`.`estado_version_presupuesto` AS `estado_version_actual`,`pv`.`fecha_creacion_version` AS `fecha_creacion_version_actual`,(case when (`ep`.`codigo_estado_ppto` = 'CANC') then 'Cancelado' when (`ep`.`codigo_estado_ppto` = 'FACT') then 'Facturado' when ((`p`.`fecha_validez_presupuesto` < curdate()) and (`ep`.`codigo_estado_ppto` not in ('ACEP','RECH','CANC','FACT'))) then 'Caducado' when ((`p`.`fecha_inicio_evento_presupuesto` < curdate()) and (`p`.`fecha_fin_evento_presupuesto` < curdate())) then 'Evento finalizado' when ((`p`.`fecha_inicio_evento_presupuesto` <= curdate()) and (`p`.`fecha_fin_evento_presupuesto` >= curdate())) then 'Evento en curso' when (`ep`.`codigo_estado_ppto` = 'ACEP') then 'Aceptado - Pendiente evento' else `ep`.`nombre_estado_ppto` end) AS `estado_general_presupuesto` from ((((((((`presupuesto` `p` join `cliente` `c` on((`p`.`id_cliente` = `c`.`id_cliente`))) left join `contacto_cliente` `cc` on((`p`.`id_contacto_cliente` = `cc`.`id_contacto_cliente`))) join `estado_presupuesto` `ep` on((`p`.`id_estado_ppto` = `ep`.`id_estado_ppto`))) left join `forma_pago` `fp` on((`p`.`id_forma_pago` = `fp`.`id_pago`))) left join `metodo_pago` `mp` on((`fp`.`id_metodo_pago` = `mp`.`id_metodo_pago`))) left join `metodos_contacto` `mc` on((`p`.`id_metodo` = `mc`.`id_metodo`))) left join `forma_pago` `fph` on((`c`.`id_forma_pago_habitual` = `fph`.`id_pago`))) left join `presupuesto_version` `pv` on(((`p`.`id_presupuesto` = `pv`.`id_presupuesto`) and (`pv`.`numero_version_presupuesto` = `p`.`version_actual_presupuesto`)))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;

--
-- Final view structure for view `vista_registro_kilometraje`
--

/*!50001 DROP VIEW IF EXISTS `vista_registro_kilometraje`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_unicode_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `vista_registro_kilometraje` AS select `rk`.`id_registro_km` AS `id_registro_km`,`rk`.`id_furgoneta` AS `id_furgoneta`,`rk`.`fecha_registro_km` AS `fecha_registro_km`,`rk`.`kilometraje_registrado_km` AS `kilometraje_registrado_km`,`rk`.`tipo_registro_km` AS `tipo_registro_km`,`rk`.`observaciones_registro_km` AS `observaciones_registro_km`,`rk`.`created_at_registro_km` AS `created_at_registro_km`,`f`.`matricula_furgoneta` AS `matricula_furgoneta`,`f`.`marca_furgoneta` AS `marca_furgoneta`,`f`.`modelo_furgoneta` AS `modelo_furgoneta`,`f`.`estado_furgoneta` AS `estado_furgoneta`,(`rk`.`kilometraje_registrado_km` - coalesce((select `furgoneta_registro_kilometraje`.`kilometraje_registrado_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),0)) AS `km_recorridos`,(to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`))) AS `dias_transcurridos`,(case when ((to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`))) > 0) then ((`rk`.`kilometraje_registrado_km` - coalesce((select `furgoneta_registro_kilometraje`.`kilometraje_registrado_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),0)) / (to_days(`rk`.`fecha_registro_km`) - to_days(coalesce((select `furgoneta_registro_kilometraje`.`fecha_registro_km` from `furgoneta_registro_kilometraje` where ((`furgoneta_registro_kilometraje`.`id_furgoneta` = `rk`.`id_furgoneta`) and (`furgoneta_registro_kilometraje`.`fecha_registro_km` < `rk`.`fecha_registro_km`)) order by `furgoneta_registro_kilometraje`.`fecha_registro_km` desc limit 1),`rk`.`fecha_registro_km`)))) else 0 end) AS `km_promedio_diario` from (`furgoneta_registro_kilometraje` `rk` join `furgoneta` `f` on((`rk`.`id_furgoneta` = `f`.`id_furgoneta`))) order by `rk`.`fecha_registro_km` desc */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-01-23 18:36:58
