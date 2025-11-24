-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
--
-- Host: 192.168.31.251    Database: toldos_bd
-- ------------------------------------------------------
-- Server version	8.0.40-0ubuntu0.24.10.1

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
  `nombre_archivo` varchar(255) NOT NULL,
  `tipo` varchar(20) NOT NULL,
  `fecha_subida` datetime DEFAULT CURRENT_TIMESTAMP,
  `estado` tinyint DEFAULT '1' COMMENT '0=Inactivo, 1=Activo',
  PRIMARY KEY (`id_adjunto`),
  KEY `fk_id_llamada` (`id_llamada`),
  CONSTRAINT `fk_id_llamada` FOREIGN KEY (`id_llamada`) REFERENCES `llamadas` (`id_llamada`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `adjunto_llamada`
--

LOCK TABLES `adjunto_llamada` WRITE;
/*!40000 ALTER TABLE `adjunto_llamada` DISABLE KEYS */;
INSERT INTO `adjunto_llamada` VALUES (5,4,'pdf (7).pdf','application/pdf','2025-04-17 08:21:20',1),(6,4,'pdf (3).pdf','application/pdf','2025-04-17 10:27:55',0),(7,4,'imagenPrueba.jpg','image/jpeg','2025-04-17 10:55:04',0),(9,16,'GOOGLE_JIMÉNEZ_CABRERA_ALEJANDRO.pdf','application/pdf','2025-04-17 10:56:38',1),(10,4,'Final Practice 2025.pdf','application/pdf','2025-04-17 11:02:28',1),(11,4,'jQueryNotesForProfessionals.pdf','application/pdf','2025-04-17 11:32:02',1),(12,4,'CRUD TEMPLATE.pdf','application/pdf','2025-04-17 13:22:02',1),(13,4,'CRUD TEMPLATE_1.pdf','application/pdf','2025-04-17 13:26:54',1),(14,4,'pdf (12).pdf','application/pdf','2025-04-17 13:26:54',0),(15,4,'pdf (11).pdf','application/pdf','2025-04-17 13:26:54',1),(18,4,'gretaandthefibers.png','image/png','2025-04-30 11:03:21',1),(20,19,'seguridad-kit.png','image/png','2025-05-05 10:27:14',1),(21,19,'seguridad.png','image/png','2025-05-05 10:27:14',1),(22,19,'transporte-maritimo-de-contenedores.jpg','image/jpeg','2025-05-05 10:27:14',1),(23,19,'internet_1.png','image/png','2025-05-05 10:27:48',1),(24,19,'seguridad-kit_1.png','image/png','2025-05-05 10:27:48',1),(25,19,'seguridad_1.png','image/png','2025-05-05 10:27:48',1),(26,19,'transporte-maritimo-de-contenedores_1.jpg','image/jpeg','2025-05-05 10:27:48',1),(48,4,'863581788514678900713645_2.pdf','application/pdf','2025-05-06 14:59:12',1),(55,16,'Snap 2024-06-04 at 19.40.50.png','image/png','2025-05-14 17:29:51',1),(56,16,'IMG-20250513-WA0006.jpg','image/jpeg','2025-05-15 09:02:58',1);
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
  `fecha` date DEFAULT (curdate()),
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
  `descripcion` varchar(50) DEFAULT NULL,
  `activo_vacacion` tinyint DEFAULT '1',
  PRIMARY KEY (`id_vacacion`),
  KEY `fk_vacaciones_comercial` (`id_comercial`),
  CONSTRAINT `fk_vacaciones_comercial` FOREIGN KEY (`id_comercial`) REFERENCES `comerciales` (`id_comercial`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `com_vacaciones`
--

LOCK TABLES `com_vacaciones` WRITE;
/*!40000 ALTER TABLE `com_vacaciones` DISABLE KEYS */;
INSERT INTO `com_vacaciones` VALUES (1,1,'2025-03-01','2025-02-14','Vacaciones de prueba',0),(2,2,'2025-04-10','2025-04-15','Viaje familiar',0),(3,3,'2025-05-05','2025-05-10','Descanso médico',0),(4,1,'2025-02-23','2025-07-27','ejemplo de descripcion',0),(5,1,'2025-02-23','2025-07-27','ejemplo de descripcion',1),(6,1,'2025-02-01','2025-02-22','ejemplo de descripcion',0),(7,2,'2025-03-22','2025-03-29','prueba de ejerci',0),(8,2,'2025-03-13','2025-03-20','sssssssssssssss',0),(9,2,'2025-04-09','2025-04-18','prueba de ejercicio',0),(10,2,'2025-04-02','2025-04-12','prueba de ejercicio',0),(11,2,'2025-04-12','2025-04-19','prueba de ejercicio',0),(12,2,'2025-04-09','2025-04-17','prueba de ejercicooooooooooo',0),(13,11,'2025-04-10','2025-04-18','prueba de ejercicio',0),(14,2,'2025-04-19','2025-04-30','prueba de ejercicio',0),(15,11,'2025-04-09','2025-04-09','prueba de ejercicio',0),(16,24,'2025-04-03','2025-04-17','prueba de ejercicio',0),(17,16,'2025-04-10','2025-04-24','Por semana santa',0),(18,14,'2025-04-02','2025-04-19','VaCACIONES DE SEMANA SANTA',0),(19,2,'2025-04-02','2025-04-17','Vacaciones de semana santa',0),(20,2,'2025-05-24','2025-05-30','prueba de ejercicio',1);
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
  `nombre` varchar(50) NOT NULL,
  `apellidos` varchar(50) NOT NULL,
  `movil` varchar(15) NOT NULL,
  `telefono` varchar(15) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `activo` tinyint DEFAULT '1',
  `id_usuario` int NOT NULL,
  PRIMARY KEY (`id_comercial`),
  UNIQUE KEY `email` (`email`),
  KEY `fk_comerciales_usuarios` (`id_usuario`),
  CONSTRAINT `fk_comerciales_usuarios` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comerciales`
--

LOCK TABLES `comerciales` WRITE;
/*!40000 ALTER TABLE `comerciales` DISABLE KEYS */;
INSERT INTO `comerciales` VALUES (1,'Alejandro','Rodríguez Martínez','698689685','698689685','alejandrorodriguez@gmail.com',1,5),(2,'Carlos','López','655656841','655656841','carloslopez@email.com',1,6),(3,'Marta','Rodríguez González','645567674','645567674','martarodriguez@email.com',0,19),(4,'Luis','Fernández López','645575741','645575741','luisfernandez@email.com',1,7),(9,'Lucía','Pérez Sánchez','698898874','698898874','luciaperez@gmail.com',1,8),(11,'Ana','Hernández Torres','635999999','635999999','carloshernandez@gmail.com',0,20),(12,'Miguel','Díaz Jiménez','643455441','643455441','migueldiaz@gmail.com',0,21),(13,'Raúl','Romero Álvarez','695548744','695548744','raulromero@gmail.com',1,9),(14,'Eva','Moreno Fernández','698654645','698654645','evamoreno@gmail.com',0,22),(16,'Teresa','Vázquez Suárez','689789454','689789454','teresavazquez@gmail.com',0,23),(17,'Margarita','García Castro','616515614','616515614','margaritagarcia@gmail.com',1,10),(18,'Carmen','Martínez González','623615641','623615641','carmenmartinez@gmail.com',1,11),(19,'Sergio','López Hernández','634535442','634535442','sergiolopez@gmail.com',0,24),(21,'Alberto','Hernández García','644334567','644334567','albertohernandez@gmail.com',1,12),(22,'Natalia','Sánchez García','621849484','621849484','nataliasanchez@gmail.com',1,13),(23,'Laura','Ramírez Hernández','632554779','632554779','lauraramirez@gmail.com',1,14),(24,'Francisco','Moreno Moya','660300923','660300923','franciscomorenomoya@gmail.com',1,15),(27,'Beatriz','Muñoz Vázquez','477777777777','777777774','beatrizmunoz@gmail.com',1,16),(28,'Pablo','Moreno Sánchez','698544745','698544745','pablomoreno@gmail.com',1,17),(30,'María','Torres García','645644211','645644211','mariatorres@gmail.com',1,18);
/*!40000 ALTER TABLE `comerciales` ENABLE KEYS */;
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
  `observaciones` text,
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
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `contactos`
--

LOCK TABLES `contactos` WRITE;
/*!40000 ALTER TABLE `contactos` DISABLE KEYS */;
INSERT INTO `contactos` VALUES (1,14,'2025-04-17 00:00:00','Tomás consultó por toldos retráctiles para uso residencial. Está interesado en modelos motorizados con sensores de viento. Solicita catálogo actualizado',1,1,NULL),(2,1,'2025-04-24 14:25:00','Solicitó una visita técnica para tomar medidas en su local comercial. Posible proyecto para instalación de toldos tipo cofre en fachada',1,3,5),(3,1,'2025-04-06 00:00:00','Cliente potencial con interés en renovar toldos existentes. Mencionó problemas con el sistema actual y busca soluciones más duraderas y estéticas.',1,1,NULL),(4,1,'2025-04-27 10:53:00','Tomás quedó conforme con la atención recibida. Pidió cotización para tres modelos distintos y desea comparar colores y tipos de lona.',0,3,NULL),(5,1,'2025-04-13 00:00:00','Mostró especial interés en toldos con estructura de aluminio y tejido acrílico impermeable. Está evaluando instalar en su terraza antes del verano.',1,2,NULL),(6,1,'2025-04-10 07:50:00','Interesado en instalar un toldo automático con control remoto. Preguntó por integración con sistema domótico que ya tiene en casa.',0,1,NULL),(7,10,'2025-04-18 12:35:00','Comentó que su vecina le recomendó nuestra empresa. Valoró positivamente la reputación y experiencia del equipo técnico.',1,1,6),(8,2,'2025-04-21 06:52:00','Solicitó presupuesto para toldos verticales para proteger ventanas del sol directo en las tardes. Tiene problemas de sobrecalentamiento en el salón.',1,1,NULL),(9,4,'2025-05-18 12:15:00','<p><b><strike>uuuuuuuuuuuuuu</strike></b></p>',1,3,4),(14,20,'2025-05-16 12:09:00','<p><br></p>',1,2,7),(15,21,'2025-05-20 12:17:00','<p><br></p>',1,3,NULL),(16,6,'2025-05-16 14:31:00','<p><br></p>',1,1,NULL),(17,22,'2025-05-16 15:33:00','<p><br></p>',1,2,8),(18,24,'2025-05-22 13:06:00','<p><br></p>',1,1,9);
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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `empresa`
--

LOCK TABLES `empresa` WRITE;
/*!40000 ALTER TABLE `empresa` DISABLE KEYS */;
/*!40000 ALTER TABLE `empresa` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `estados_llamada`
--

DROP TABLE IF EXISTS `estados_llamada`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `estados_llamada` (
  `id_estado` int NOT NULL AUTO_INCREMENT,
  `desc_estado` varchar(100) NOT NULL,
  `defecto_estado` tinyint DEFAULT NULL,
  `activo_estado` varchar(200) NOT NULL,
  `peso_estado` int NOT NULL,
  PRIMARY KEY (`id_estado`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `estados_llamada`
--

LOCK TABLES `estados_llamada` WRITE;
/*!40000 ALTER TABLE `estados_llamada` DISABLE KEYS */;
INSERT INTO `estados_llamada` VALUES (1,'Recibida sin atención',1,'1',10),(2,'Con contacto',0,'1',20),(3,'Cita Cerrada',0,'1',30),(4,'Perdida',0,'1',40),(11,'HJGHJGHJGH',0,'1',23);
/*!40000 ALTER TABLE `estados_llamada` ENABLE KEYS */;
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
  `nombre_comunicante` varchar(100) NOT NULL,
  `domicilio_instalacion` varchar(200) NOT NULL,
  `telefono_fijo` varchar(15) DEFAULT NULL,
  `telefono_movil` varchar(15) DEFAULT NULL,
  `email_contacto` varchar(50) DEFAULT NULL,
  `fecha_hora_preferida` datetime DEFAULT NULL,
  `observaciones` text,
  `id_comercial_asignado` int NOT NULL,
  `estado` int NOT NULL,
  `fecha_recepcion` datetime DEFAULT CURRENT_TIMESTAMP,
  `activo_llamada` tinyint DEFAULT '1',
  PRIMARY KEY (`id_llamada`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `llamadas`
--

LOCK TABLES `llamadas` WRITE;
/*!40000 ALTER TABLE `llamadas` DISABLE KEYS */;
INSERT INTO `llamadas` VALUES (1,1,'María García','Avenida Secundaria 45, Barcelona','932345678','622345678','mariagarcia@email.com','2023-11-16 16:30:00','Reclamación sobre factura pendiente',12,3,'2025-05-08 12:48:00',1),(2,3,'Manolo','Manises',NULL,NULL,'manolo@email.com','2025-05-21 14:21:00',NULL,27,3,'2025-04-15 12:08:00',0),(4,1,'Luis Rodríguez','Quart de Poblet',NULL,NULL,NULL,NULL,'<p>TYUTYUTYUTYUYT</p>',1,3,'2025-05-09 00:00:00',1),(6,1,'Juan Pérez','Xirivella',NULL,NULL,NULL,'2025-04-10 00:00:00',NULL,4,1,'2025-04-17 13:08:00',1),(10,1,'Alejandro','Quart de Poblet',NULL,NULL,NULL,NULL,'<sub><u><strike><i>ejemplo de texto para ver si las observaciones funcionan correctamente&nbsp;</i></strike></u></sub>',9,1,'2025-04-18 13:08:00',1),(14,2,'Tomás','Patraix',NULL,NULL,NULL,NULL,NULL,19,2,'2025-05-17 00:00:00',0),(16,3,'Lorena','Burjassot',NULL,NULL,NULL,'2025-05-24 14:21:00',NULL,16,1,'2025-04-11 18:56:00',1),(19,3,'Alejandro','Quart de Poblet',NULL,NULL,NULL,'2025-05-05 09:19:00',NULL,12,2,'2025-05-20 10:55:00',1),(20,3,'Luis Rodríguez','Avenida Secundaria 45, Barcelona',NULL,NULL,NULL,NULL,'<p><br></p>',19,2,'2025-05-09 09:21:00',1),(21,2,'Tomás Hernández','Quart de Poblet','32434234234234','23423434234',NULL,'2025-05-15 17:27:00','<p>werwerewrewrwerwer</p>',2,1,'2025-05-14 17:27:00',1),(22,3,'Luis Rodríguez','Avenida Secundaria 45, Barcelona',NULL,NULL,NULL,NULL,'<p><br></p>',2,3,'2025-05-15 13:18:00',1),(23,3,'Alejandro','Quart de Poblet',NULL,NULL,NULL,NULL,'<p><br></p>',2,2,'2025-05-15 13:22:00',1),(24,2,'dfsdfsdf','sdfsdfdsfdsf',NULL,NULL,NULL,NULL,'<p><br></p>',2,3,'2025-05-22 13:06:00',1);
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
-- Table structure for table `metodos_contacto`
--

DROP TABLE IF EXISTS `metodos_contacto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `metodos_contacto` (
  `id_metodo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) NOT NULL,
  `permite_adjuntos` tinyint DEFAULT '0',
  `estado` tinyint DEFAULT '1',
  `imagen_metodo` varchar(255) DEFAULT NULL COMMENT 'Ruta o nombre de la imagen del método de contacto',
  PRIMARY KEY (`id_metodo`),
  UNIQUE KEY `nombre_UNIQUE` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `metodos_contacto`
--

LOCK TABLES `metodos_contacto` WRITE;
/*!40000 ALTER TABLE `metodos_contacto` DISABLE KEYS */;
INSERT INTO `metodos_contacto` VALUES (1,'Correo Electrónico',1,1,'mail-email-icon-template-black-color-editable-mail-email-icon-symbol-flat-illustration-for-graphic-and-web-design-free-vector.jpg'),(2,'Llamada Telefónica',1,0,'telefonoContacto.png'),(3,'WhatsApp Business',0,1,'whatsappContacto.png'),(38,'EJEMPLO',0,1,'Fhc5481XwAIkIXM_1.jpg');
/*!40000 ALTER TABLE `metodos_contacto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `productos`
--

DROP TABLE IF EXISTS `productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `productos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  `categoria_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `productos_ibfk_1` (`categoria_id`),
  CONSTRAINT `productos_ibfk_1` FOREIGN KEY (`categoria_id`) REFERENCES `categorias` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `productos`
--

LOCK TABLES `productos` WRITE;
/*!40000 ALTER TABLE `productos` DISABLE KEYS */;
INSERT INTO `productos` VALUES (2,'pollo frito',2),(3,'pollo frito',2),(4,'ejemplo nuevo',3),(5,'manzana',2),(6,'platano',2),(7,'aston martin',3),(8,'tomate',2),(9,'ferraris de senna',3),(10,'Alejandro',4),(11,'Luis',4),(12,'Rubén',4),(13,'Adahi',4);
/*!40000 ALTER TABLE `productos` ENABLE KEYS */;
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
INSERT INTO `roles` VALUES (1,'Empleado',0),(2,'Gestor',0),(3,'Administrador',1),(4,'Comercial',1);
/*!40000 ALTER TABLE `roles` ENABLE KEYS */;
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
  PRIMARY KEY (`id_usuario`),
  KEY `id_rol` (`id_rol`),
  CONSTRAINT `usuarios_ibfk_1` FOREIGN KEY (`id_rol`) REFERENCES `roles` (`id_rol`) ON DELETE RESTRICT ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='Tabla de usuario con contraseñas y roles asociados';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'ale@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alejandro','2025-04-25 09:10:45',1,4),(2,'luis@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Luis','2025-04-25 10:23:12',1,3),(3,'jorge@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Jorge','2025-04-25 10:30:21',1,1),(4,'hugo@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Hugo','2025-04-25 10:37:15',1,2),(5,'alejandrorodriguez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alejandro Rodríguez Martínez','2025-05-15 10:18:39',1,4),(6,'carloslopez@email.com','0fcc23c449980e35b30c0f77fd125dc5','Carlos López','2025-05-15 10:18:39',1,4),(7,'luisfernandez@email.com','0fcc23c449980e35b30c0f77fd125dc5','Luis Fernández López','2025-05-15 10:18:39',1,4),(8,'luciaperez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Lucía Pérez Sánchez','2025-05-15 10:18:39',1,4),(9,'raulromero@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Raúl Romero Álvarez','2025-05-15 10:18:39',1,4),(10,'margaritagarcia@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Margarita García Castro','2025-05-15 10:18:39',1,4),(11,'carmenmartinez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Carmen Martínez González','2025-05-15 10:18:39',1,4),(12,'albertohernandez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Alberto Hernández García','2025-05-15 10:18:39',1,4),(13,'nataliasanchez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Natalia Sánchez García','2025-05-15 10:18:39',1,4),(14,'lauraramirez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Laura Ramírez Hernández','2025-05-15 10:18:39',1,4),(15,'franciscomorenomoya@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Francisco Moreno Moya','2025-05-15 10:18:39',1,4),(16,'beatrizmunoz@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Beatriz Muñoz Vázquez','2025-05-15 10:18:39',1,4),(17,'pablomoreno@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Pablo Moreno Sánchez','2025-05-15 10:18:39',1,4),(18,'mariatorres@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','María Torres García','2025-05-15 10:18:39',1,4),(19,'martarodriguez@email.com','0fcc23c449980e35b30c0f77fd125dc5','Marta Rodríguez González','2025-05-15 10:26:12',1,4),(20,'carloshernandez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Ana Hernández Torres','2025-05-15 10:26:12',1,4),(21,'migueldiaz@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Miguel Díaz Jiménez','2025-05-15 10:26:13',1,4),(22,'evamoreno@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Eva Moreno Fernández','2025-05-15 10:26:13',1,4),(23,'teresavazquez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Teresa Vázquez Suárez','2025-05-15 10:26:13',1,4),(24,'sergiolopez@gmail.com','0fcc23c449980e35b30c0f77fd125dc5','Sergio López Hernández','2025-05-15 10:26:13',1,4);
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
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `visitas_cerradas`
--

LOCK TABLES `visitas_cerradas` WRITE;
/*!40000 ALTER TABLE `visitas_cerradas` DISABLE KEYS */;
INSERT INTO `visitas_cerradas` VALUES (4,'2025-05-25 11:22:00',9,4),(5,'2025-05-31 11:22:00',2,1),(6,'2025-05-31 15:55:00',7,10),(7,'2025-06-05 12:09:00',14,20),(8,'2025-05-17 15:33:00',17,22),(9,'2025-05-30 13:07:00',18,24);
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
-- Dumping events for database 'toldos_bd'
--
/*!50106 SET @save_time_zone= @@TIME_ZONE */ ;
/*!50106 DROP EVENT IF EXISTS `desactivar_fecha_vacaciones_12` */;
DELIMITER ;;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;;
/*!50003 SET character_set_client  = utf8mb4 */ ;;
/*!50003 SET character_set_results = utf8mb4 */ ;;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`%`*/ /*!50106 EVENT `desactivar_fecha_vacaciones_12` ON SCHEDULE EVERY 1 DAY STARTS '2025-04-03 00:01:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL desactivar_vacaciones_pasadas() */ ;;
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
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;;
/*!50003 SET @saved_time_zone      = @@time_zone */ ;;
/*!50003 SET time_zone             = 'SYSTEM' */ ;;
/*!50106 CREATE*/ /*!50117 DEFINER=`root`@`%`*/ /*!50106 EVENT `desactivar_fecha_vacaciones_3` ON SCHEDULE EVERY 1 DAY STARTS '2025-04-03 03:00:00' ON COMPLETION NOT PRESERVE ENABLE DO CALL desactivar_vacaciones_pasadas() */ ;;
/*!50003 SET time_zone             = @saved_time_zone */ ;;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;;
/*!50003 SET character_set_client  = @saved_cs_client */ ;;
/*!50003 SET character_set_results = @saved_cs_results */ ;;
/*!50003 SET collation_connection  = @saved_col_connection */ ;;
DELIMITER ;
/*!50106 SET TIME_ZONE= @save_time_zone */ ;

--
-- Dumping routines for database 'toldos_bd'
--
/*!50003 DROP PROCEDURE IF EXISTS `desactivar_vacaciones_pasadas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8mb4 */ ;
/*!50003 SET character_set_results = utf8mb4 */ ;
/*!50003 SET collation_connection  = utf8mb4_0900_ai_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
CREATE DEFINER=`root`@`%` PROCEDURE `desactivar_vacaciones_pasadas`()
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

--
-- Final view structure for view `contactos_con_nombre_comunicante`
--

/*!50001 DROP VIEW IF EXISTS `contactos_con_nombre_comunicante`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `contactos_con_nombre_comunicante` AS select `c`.`id_contacto` AS `id_contacto`,`c`.`id_llamada` AS `id_llamada`,`c`.`id_metodo` AS `id_metodo`,`c`.`fecha_hora_contacto` AS `fecha_hora_contacto`,`c`.`observaciones` AS `observaciones`,`c`.`id_visita_cerrada` AS `id_visita_cerrada`,(select `vc`.`fecha_visita_cerrada` from `visitas_cerradas` `vc` where (`vc`.`id_visita_cerrada` = `c`.`id_visita_cerrada`)) AS `fecha_visita_cerrada`,`c`.`estado` AS `estado`,(select `l`.`nombre_comunicante` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `nombre_comunicante`,(select `l`.`domicilio_instalacion` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `domicilio_instalacion`,(select `l`.`telefono_fijo` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `telefono_fijo`,(select `l`.`telefono_movil` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `telefono_movil`,(select `l`.`email_contacto` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `email_contacto`,(select `l`.`fecha_hora_preferida` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `fecha_hora_preferida`,(select `l`.`fecha_recepcion` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `fecha_recepcion`,(select `l`.`id_comercial_asignado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `id_comercial_asignado`,(select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `estado_llamada`,(select `l`.`activo_llamada` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) AS `activo_llamada`,(select `m`.`nombre` from `metodos_contacto` `m` where (`m`.`id_metodo` = `c`.`id_metodo`)) AS `nombre_metodo`,(select `m`.`imagen_metodo` from `metodos_contacto` `m` where (`m`.`id_metodo` = `c`.`id_metodo`)) AS `imagen_metodo`,(select `e`.`desc_estado` from `estados_llamada` `e` where (`e`.`id_estado` = (select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)))) AS `descripcion_estado_llamada`,(select `com`.`nombre` from `comerciales` `com` where (`com`.`id_comercial` = (select `l`.`id_comercial_asignado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)))) AS `nombre_comercial`,ifnull((select group_concat(`a`.`nombre_archivo` separator ',') from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `c`.`id_llamada`) and (`a`.`estado` = 1))),'Sin archivos') AS `archivos_adjuntos`,(select (count(0) > 0) from `contactos` `cont` where (`cont`.`id_llamada` = `c`.`id_llamada`)) AS `tiene_contactos`,((select `l`.`estado` from `llamadas` `l` where (`l`.`id_llamada` = `c`.`id_llamada`)) = 3) AS `estado_es_3`,(select (count(0) > 0) from `adjunto_llamada` `a` where ((`a`.`id_llamada` = `c`.`id_llamada`) and (`a`.`estado` = 1))) AS `tiene_adjuntos` from `contactos` `c` */;
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
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
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
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
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
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
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
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
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

-- Dump completed on 2025-05-23  9:17:53
