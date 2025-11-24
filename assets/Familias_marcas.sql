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
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `coeficiente`
--

LOCK TABLES `coeficiente` WRITE;
/*!40000 ALTER TABLE `coeficiente` DISABLE KEYS */;
INSERT INTO `coeficiente` VALUES (1,10,8.50,'Es una especie de descuento valorado en 3 Euros',1,'2025-11-14 13:18:26','2025-11-15 08:41:03'),(2,15,12.00,'',1,'2025-11-14 13:23:09','2025-11-14 13:23:09'),(3,20,10.00,'',1,'2025-11-15 08:40:27','2025-11-15 08:40:27');
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
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contacto_proveedor`
--

LOCK TABLES `contacto_proveedor` WRITE;
/*!40000 ALTER TABLE `contacto_proveedor` DISABLE KEYS */;
INSERT INTO `contacto_proveedor` VALUES (1,1,'Luis Carlos','PéRez Mataix','Director de Ventas','Administración','660300923','660345258','luiscarlospm@gmail.com','123',1,'Es bastante simpático',1,'2025-11-16 06:26:58','2025-11-16 06:27:36');
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
-- Table structure for table `empresa`
--

DROP TABLE IF EXISTS `empresa`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `empresa` (
  `id_empresa` int NOT NULL AUTO_INCREMENT,
  `criterio_comercial` int NOT NULL,
  PRIMARY KEY (`id_empresa`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estado_presupuesto`
--

LOCK TABLES `estado_presupuesto` WRITE;
/*!40000 ALTER TABLE `estado_presupuesto` DISABLE KEYS */;
INSERT INTO `estado_presupuesto` VALUES (1,'PEND','Pendiente','#ffc107',1,'Presupuesto pendiente de revisión',1,'2025-11-14 12:35:03','2025-11-14 12:35:03'),(2,'PROC','En Proceso','#17a2b8',2,'Presupuesto en proceso de elaboración',1,'2025-11-14 12:35:03','2025-11-14 12:35:03'),(3,'APROB','Aprobado','#28a745',3,'Presupuesto aprobado por el cliente',1,'2025-11-14 12:35:03','2025-11-14 12:35:03'),(4,'RECH','Rechazado','#dc3545',4,'Presupuesto rechazado por el cliente',1,'2025-11-14 12:35:03','2025-11-14 12:35:03'),(5,'CANC','Cancelado','#6c757d',5,'Presupuesto cancelado',1,'2025-11-14 12:35:03','2025-11-14 12:35:03'),(6,'DEN','Denegado por el cliente','#ff0000',100,'Es una prueba e funcionamiento',1,'2025-11-14 12:38:40','2025-11-14 12:38:56');
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
  `codigo_familia` varchar(20) NOT NULL,
  `nombre_familia` varchar(100) NOT NULL,
  `name_familia` varchar(100) NOT NULL COMMENT 'Nombre en inglés',
  `descr_familia` varchar(255) DEFAULT NULL,
  `activo_familia` tinyint(1) DEFAULT '1',
  `coeficiente_familia` tinyint DEFAULT NULL,
  `id_unidad_familia` int DEFAULT NULL COMMENT 'el Id (id_unidad) de la tabla unidad_medida',
  `imagen_familia` varchar(150) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci DEFAULT NULL COMMENT 'Es la foto representativa de la familia',
  `created_at_familia` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_familia` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_familia`),
  UNIQUE KEY `codigo_familia` (`codigo_familia`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `familia`
--

LOCK TABLES `familia` WRITE;
/*!40000 ALTER TABLE `familia` DISABLE KEYS */;
INSERT INTO `familia` VALUES (1,'232131','Monitor','Monitor','Sirve para transmitir video De forma muy activa y posiblermente se encuentre desactivado por que ya no funcionanrAN EL RESTO.',0,1,4,'','2025-10-28 11:08:45','2025-11-14 06:30:00'),(5,'RAT001-CABLE','Ratón con cable','Mouse with cable','Es la familia de todos los ratones con cable',1,NULL,NULL,'','2025-10-31 10:08:56','2025-10-31 10:22:19'),(6,'TABLET','Tablets','Tablet (en)','Es la descripción de las tablet',1,NULL,NULL,'','2025-11-05 10:04:34','2025-11-14 06:30:37'),(7,'MONITORES','Monitores','Monitor (en)','Es de la familia de los monitores',1,NULL,NULL,'familia_690b2395e33e9.png','2025-11-05 10:09:36','2025-11-05 10:14:45'),(8,'RATONESESTILOSOS','Ratones con estilo','Mouse (en)','Es de la famlia con estilo',1,1,NULL,'familia_690b2688730f2.png','2025-11-05 10:27:20','2025-11-14 06:30:17'),(9,'BOTELLA','Botellas varias','Bottle','Esta es una descripción de las botellas',0,NULL,4,'familia_690b2a3d1a531.png','2025-11-05 10:43:09','2025-11-09 20:13:48'),(10,'MIC','Microfono','Microphone','Transmite sonido.',1,NULL,4,'','2025-11-05 18:18:53','2025-11-09 18:01:48'),(11,'AAAAA','aaaaaaa','wwwwww','wreresrfsdfdxfsdfzdsf',1,NULL,4,'familia_690c7a2b9ee16.png','2025-11-06 10:32:04','2025-11-10 07:39:35'),(12,'MAP','Mapa','Map','',1,NULL,NULL,NULL,'2025-11-07 12:52:25','2025-11-07 12:52:25'),(13,'PANTALLAS_PORTATILES','Pantallas portatiles','Portatil monitor','Es uan prueba de pantallas portatiles',1,NULL,4,'familia_6910e24ce92cb.png','2025-11-09 18:49:49','2025-11-09 18:49:49');
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
 1 AS `codigo_familia`,
 1 AS `nombre_familia`,
 1 AS `name_familia`,
 1 AS `descr_familia`,
 1 AS `activo_familia`,
 1 AS `coeficiente_familia`,
 1 AS `id_unidad_familia`,
 1 AS `imagen_familia`,
 1 AS `created_at_familia`,
 1 AS `updated_at_familia`,
 1 AS `id_unidad`,
 1 AS `nombre_unidad`,
 1 AS `name_unidad`,
 1 AS `descr_unidad`,
 1 AS `simbolo_unidad`,
 1 AS `activo_unidad`,
 1 AS `created_at_unidad`,
 1 AS `updated_at_unidad`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `forma_pago`
--

DROP TABLE IF EXISTS `forma_pago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `forma_pago` (
  `id_pago` int unsigned NOT NULL AUTO_INCREMENT,
  `codigo_pago` varchar(20) NOT NULL,
  `nombre_pago` varchar(100) NOT NULL,
  `dias_pago` int DEFAULT '0',
  `descuento_pago` decimal(5,2) DEFAULT '0.00',
  `observaciones_pago` text,
  `activo_pago` tinyint(1) DEFAULT '1',
  `created_at_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_pago` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_pago`),
  UNIQUE KEY `codigo_pago` (`codigo_pago`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `forma_pago`
--

LOCK TABLES `forma_pago` WRITE;
/*!40000 ALTER TABLE `forma_pago` DISABLE KEYS */;
INSERT INTO `forma_pago` VALUES (1,'CONT','Pago por Transferencia',30,0.00,'Resumen de las formas de pago.',1,'2025-11-14 07:28:39','2025-11-14 07:28:39');
/*!40000 ALTER TABLE `forma_pago` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `impuesto`
--

LOCK TABLES `impuesto` WRITE;
/*!40000 ALTER TABLE `impuesto` DISABLE KEYS */;
INSERT INTO `impuesto` VALUES (1,'IVAS',21.00,'Es la tasa del IVA normal',1,NULL,'2025-11-10 07:11:38','2025-11-11 17:04:28'),(2,'IS',25.00,'Impuesto sobre sociedades.',1,NULL,'2025-11-11 17:07:29','2025-11-11 17:07:29');
/*!40000 ALTER TABLE `impuesto` ENABLE KEYS */;
UNLOCK TABLES;

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
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `marca`
--

LOCK TABLES `marca` WRITE;
/*!40000 ALTER TABLE `marca` DISABLE KEYS */;
INSERT INTO `marca` VALUES (1,'SAMS','Samsung','Samsung (en)','Tecnología innovadora y diseño de vanguardia. Suficiente para el desarrollo.',1,'2025-10-30 16:42:51','2025-11-05 18:22:50'),(4,'ZYYCA44','Dell','Dell (en)','Computadoras y soluciones tecnológicas innovadoras. XXXXXX',1,'2025-10-30 17:47:32','2025-10-31 16:18:10'),(7,'HP0011','Hewlett Packard','HP (en) - English','Prueba de detalles en marcas (es).',1,'2025-10-31 16:41:12','2025-10-31 16:41:41'),(8,'NINT','Nintendo','Nintendo','Nintendo es una empresa multinacional japonesa de entretenimiento, originaria de 1889 como fabricante de naipes y ahora dedicada al desarrollo y distribución de consolas y videojuegos.',1,'2025-11-05 18:22:06','2025-11-05 18:22:06');
/*!40000 ALTER TABLE `marca` ENABLE KEYS */;
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
INSERT INTO `metodos_contacto` VALUES (1,'Correo ElectrónicO',1,1,'34-Mail Success.png'),(2,'Llamada Telefónica',1,1,'22-Ringing Phone.png'),(3,'WhatsApp Business',0,1,'41-Chat App.png'),(39,'Presencia en tienda',0,1,'35-Support.png');
/*!40000 ALTER TABLE `metodos_contacto` ENABLE KEYS */;
UNLOCK TABLES;

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
  `observaciones_proveedor` text,
  `activo_proveedor` tinyint(1) DEFAULT '1',
  `created_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at_proveedor` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id_proveedor`),
  UNIQUE KEY `codigo_proveedor` (`codigo_proveedor`),
  KEY `idx_codigo_proveedor` (`codigo_proveedor`),
  KEY `idx_nombre_proveedor` (`nombre_proveedor`),
  KEY `idx_nif_proveedor` (`nif_proveedor`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `proveedor`
--

LOCK TABLES `proveedor` WRITE;
/*!40000 ALTER TABLE `proveedor` DISABLE KEYS */;
INSERT INTO `proveedor` VALUES (1,'PRUEBA001','Prueba','','','','','','','','','','','C/ rio Amadorio, 4','03013','La Pobla de Farnals','Valencia','99300923','4522666987','luis@gmail.com','Prueba de observaciones',1,'2025-11-15 12:00:50','2025-11-15 12:12:02'),(2,'PRUEBA002','Prueba 002','','','','','','','','','','','','','','','','','','',1,'2025-11-15 12:09:40','2025-11-15 12:09:40');
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
INSERT INTO `roles` VALUES (1,'Empleado',1),(2,'Gestor',1),(3,'Administrador',1),(4,'Comercial',1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `unidad_medida`
--

LOCK TABLES `unidad_medida` WRITE;
/*!40000 ALTER TABLE `unidad_medida` DISABLE KEYS */;
INSERT INTO `unidad_medida` VALUES (1,'Metros','Meter','Unidad de longitud','m',1,'2025-11-04 18:03:15','2025-11-10 18:59:48'),(2,'Horas','Hour','Unidad de medida de tiempo equivalente a 60 minutos.','H',1,'2025-11-06 19:21:53','2025-11-06 19:22:14'),(4,'Kilometros','Kilometer','Es para el control del desplazameinto','Km',1,'2025-11-07 18:06:23','2025-11-07 18:06:23');
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='Tabla de usuario con contraseñas y roles asociados';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'ale@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alejandro','2025-04-25 09:10:45',1,4,'x8v7c3plq9dtfr1b2a6mjohw5esynuz'),(2,'luis@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Luis','2025-04-25 10:23:12',1,3,'92fmazrxb8pcl5ghvt6wqyonj3sedu'),(3,'jorge@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Jorge','2025-04-25 10:30:21',1,1,'qlynsjo3vg9rc6tepbxahw4zf7dumk'),(4,'hugo@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Hugo','2025-04-25 10:37:15',1,2,'hmr4dwaj5s9p7b6qcznxfvotlyegku'),(5,'alejandrorodriguez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alejandro Rodríguez Martínez','2025-05-15 10:18:39',1,4,'k47bzmou1hsyexrfqvwcdjap9nglt0'),(6,'carloslopez@email.com','4af1f0ef2163e4aa57a300a876c5116e','Carlos López','2025-05-15 10:18:39',1,4,'d53gbtnrmyhvlqex7k0fzaosw9pjic'),(7,'luisfernandez@email.com','0fcc23c449980e35b30c0f77fd125dc5','Luis Fernández López','2025-05-15 10:18:39',1,4,'js2htcqrv89lwxuodkgf5n1abzemyp'),(8,'luciaperez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Lucía Pérez Sánchez','2025-05-15 10:18:39',1,4,'nqgs4wkjco7lyh1zvtrfaepx896dbum'),(9,'raulromero@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Raúl Romero Álvarez','2025-05-15 10:18:39',1,4,'mv94eub6zt7hrojwsnxlcgaqkpyd5fi'),(10,'margaritagarcia@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Margarita García Castro','2025-05-15 10:18:39',1,4,'wy1vpfl39zrhedxbjaknugqmc507sto'),(11,'carmenmartinez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Carmen Martínez González','2025-05-15 10:18:39',1,4,'oabwvrls3zyxmpu9tgqekfdh17cnj5i'),(12,'albertohernandez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alberto Hernández García','2025-05-15 10:18:39',1,4,'zeuhkmdx5p14jgtyrvb9lcawoqnsf78'),(13,'nataliasanchez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Natalia Sánchez García','2025-05-15 10:18:39',1,4,'cbnt1ayqpkj48mfwrxsz7vehgo6dlu9'),(14,'lauraramirez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Laura Ramírez Hernández','2025-05-15 10:18:39',1,4,'rqhlxomciznkv57bfsa4yduw3ptgej8'),(15,'franciscomorenomoya@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Francisco Moreno Moya','2025-05-15 10:18:39',1,4,'yteazwjf1nrhgcbsuvdxkqpm58lo94v'),(16,'beatrizmunoz@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Beatriz Muñoz Vázquez','2025-05-15 10:18:39',1,4,'vk3dqrujwbf5sclm7onhxytz2g9eaip'),(17,'pablomoreno@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Pablo Moreno Sánchez','2025-05-15 10:18:39',1,4,'p6lzexv87mcsyn1htgfaowjrqdkuib9'),(18,'mariatorres@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','María Torres García','2025-05-15 10:18:39',1,4,'gf4n6rctskxepbhmqujz8y1vwlod05a'),(19,'martarodriguez@email.com','0fcc23c449980e35b30c0f77fd125dc5','Marta Rodríguez González','2025-05-15 10:26:12',1,4,'uwg0mspz9bqh16ckafjtynrldove237'),(20,'carloshernandez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Ana Hernández Torres','2025-05-15 10:26:12',1,4,'bsfntmkgp7qhrdwo9viyexczla25uj38'),(21,'migueldiaz@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Miguel Díaz Jiménez','2025-05-15 10:26:13',1,4,'h5frtxw9ek2pimlvcyagjdsobznq47u'),(22,'evamoreno@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Eva Moreno Fernández','2025-05-15 10:26:13',1,4,'nxhtg04f7yqlv1wscprzjomdbkae59u'),(23,'teresavazquez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Teresa Vázquez Suárez','2025-05-15 10:26:13',1,4,'z7mvwg95hrk1nlx68sfcqadotjeupb2'),(24,'sergiolopez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Sergio López Hernández','2025-05-15 10:26:13',1,4,'jl6vzqsbm97x3c1dheogwftkaurynp45'),(25,'alejandrosolvam@gmail.com','fca3d97bb6bbd55138f9af6ac121acda','Alejandro','2025-04-25 10:23:12',1,3,'92fmazrxb8pcl5ghvt6wqyonj3sedu4'),(26,'delafuente@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Laura','2025-07-11 07:31:08',1,2,NULL),(27,'tomasgarc@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Tomás García','2025-07-11 07:45:50',1,1,'lwsdqoud6kk3rm1z61mgtyneckd80m'),(28,'geronimosalinas@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Geronimo Salinas','2025-07-11 10:10:41',1,4,'vbczr73fzf3cvhyg55o8xda0v2hskg'),(29,'admin@gmail.com','cb4b39a466aaef4652df4a10d50fb8d2','Administrador de demostración','2025-09-16 07:16:37',1,3,'6zvo6g8j7r48b53eqg0a8ztv9kmzs4');
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
/*!50001 VIEW `familia_unidad_media` AS select `familia`.`id_familia` AS `id_familia`,`familia`.`codigo_familia` AS `codigo_familia`,`familia`.`nombre_familia` AS `nombre_familia`,`familia`.`name_familia` AS `name_familia`,`familia`.`descr_familia` AS `descr_familia`,`familia`.`activo_familia` AS `activo_familia`,`familia`.`coeficiente_familia` AS `coeficiente_familia`,`familia`.`id_unidad_familia` AS `id_unidad_familia`,`familia`.`imagen_familia` AS `imagen_familia`,`familia`.`created_at_familia` AS `created_at_familia`,`familia`.`updated_at_familia` AS `updated_at_familia`,`unidad_medida`.`id_unidad` AS `id_unidad`,`unidad_medida`.`nombre_unidad` AS `nombre_unidad`,`unidad_medida`.`name_unidad` AS `name_unidad`,`unidad_medida`.`descr_unidad` AS `descr_unidad`,`unidad_medida`.`simbolo_unidad` AS `simbolo_unidad`,`unidad_medida`.`activo_unidad` AS `activo_unidad`,`unidad_medida`.`created_at_unidad` AS `created_at_unidad`,`unidad_medida`.`updated_at_unidad` AS `updated_at_unidad` from (`familia` left join `unidad_medida` on((`familia`.`id_unidad_familia` = `unidad_medida`.`id_unidad`))) */;
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2025-11-16  9:04:55
