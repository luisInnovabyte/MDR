-- MySQL dump 10.13  Distrib 8.0.33, for Win64 (x86_64)
--
-- Host: 192.168.31.19    Database: crud2
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
-- Temporary view structure for view `c_productos`
--

DROP TABLE IF EXISTS `c_productos`;
/*!50001 DROP VIEW IF EXISTS `c_productos`*/;
SET @saved_cs_client     = @@character_set_client;
/*!50503 SET character_set_client = utf8mb4 */;
/*!50001 CREATE VIEW `c_productos` AS SELECT 
 1 AS `prod_id`,
 1 AS `prod_nom`,
 1 AS `fech_crea`,
 1 AS `fech_modi`,
 1 AS `fech_elim`,
 1 AS `est`,
 1 AS `estadoProducto`,
 1 AS `paisesId`,
 1 AS `descrPais`*/;
SET character_set_client = @saved_cs_client;

--
-- Table structure for table `paises`
--

DROP TABLE IF EXISTS `paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `paises` (
  `idpaises` int NOT NULL AUTO_INCREMENT,
  `descrPaises` varchar(45) COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`idpaises`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci COMMENT='La tabla representa los paises de los que provienen los productos';
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `paises`
--

LOCK TABLES `paises` WRITE;
/*!40000 ALTER TABLE `paises` DISABLE KEYS */;
INSERT INTO `paises` VALUES (1,'Taiwan'),(2,'China'),(3,'Usa');
/*!40000 ALTER TABLE `paises` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tm_producto`
--

DROP TABLE IF EXISTS `tm_producto`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tm_producto` (
  `prod_id` int NOT NULL AUTO_INCREMENT,
  `prod_nom` varchar(150) COLLATE utf8mb3_spanish2_ci NOT NULL,
  `fech_crea` datetime NOT NULL,
  `fech_modi` datetime DEFAULT NULL,
  `fech_elim` datetime DEFAULT NULL,
  `est` int NOT NULL,
  `paisesId` int DEFAULT NULL COMMENT 'Lo haremos con un selec',
  `oferta` int DEFAULT '0' COMMENT 'Este campo es un checkbox que me indicará si el producto está en oferta. 1=Oferta 0 = No oferta',
  `estadoProducto` int DEFAULT NULL COMMENT '1=Nuevo, 2=Algo usado 3=Segunda mano (Es una radio Button).',
  PRIMARY KEY (`prod_id`),
  KEY `FK_paises_productos_idx` (`paisesId`),
  CONSTRAINT `FK_paises_productos` FOREIGN KEY (`paisesId`) REFERENCES `paises` (`idpaises`)
) ENGINE=InnoDB AUTO_INCREMENT=34 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tm_producto`
--

LOCK TABLES `tm_producto` WRITE;
/*!40000 ALTER TABLE `tm_producto` DISABLE KEYS */;
INSERT INTO `tm_producto` VALUES (1,'Auriculares inalambricos','2025-01-23 18:48:29','2025-03-13 20:21:25',NULL,1,1,1,1),(2,'Mouse inalambrico','2025-01-23 18:49:55','2025-03-13 16:18:57',NULL,1,1,1,1),(3,'Ratón GAMER','2025-01-23 18:50:37','2025-02-24 08:23:09',NULL,1,3,0,2),(4,'Altavoz inteligente','2025-02-24 08:18:13',NULL,NULL,1,2,0,2),(5,'USB 6.0GB','2025-02-24 08:18:48',NULL,NULL,1,3,0,3),(32,'prueba de kk','2025-03-13 20:32:24',NULL,NULL,1,NULL,NULL,NULL),(33,'qwwwwwwwwqwewewew','2025-03-13 20:35:08',NULL,'2025-03-13 20:36:55',0,NULL,1,NULL);
/*!40000 ALTER TABLE `tm_producto` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuario` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(50) COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  `telefono` bigint DEFAULT NULL,
  `email` varchar(60) COLLATE utf8mb3_spanish2_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_spanish2_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuario`
--

LOCK TABLES `usuario` WRITE;
/*!40000 ALTER TABLE `usuario` DISABLE KEYS */;
/*!40000 ALTER TABLE `usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'crud2'
--

--
-- Dumping routines for database 'crud2'
--

--
-- Final view structure for view `c_productos`
--

/*!50001 DROP VIEW IF EXISTS `c_productos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8mb4 */;
/*!50001 SET character_set_results     = utf8mb4 */;
/*!50001 SET collation_connection      = utf8mb4_0900_ai_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`%` SQL SECURITY DEFINER */
/*!50001 VIEW `c_productos` AS select `tm_producto`.`prod_id` AS `prod_id`,`tm_producto`.`prod_nom` AS `prod_nom`,`tm_producto`.`fech_crea` AS `fech_crea`,`tm_producto`.`fech_modi` AS `fech_modi`,`tm_producto`.`fech_elim` AS `fech_elim`,`tm_producto`.`est` AS `est`,`tm_producto`.`estadoProducto` AS `estadoProducto`,`tm_producto`.`paisesId` AS `paisesId`,(select `paises`.`descrPaises` from `paises` where (`paises`.`idpaises` = `tm_producto`.`paisesId`)) AS `descrPais` from `tm_producto` */;
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

-- Dump completed on 2025-03-14  9:59:01
