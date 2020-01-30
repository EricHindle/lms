-- MySQL dump 10.13  Distrib 5.7.17, for Win64 (x86_64)
--
-- Host: localhost    Database: lms
-- ------------------------------------------------------
-- Server version	5.7.18-log

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `lms_team`
--

DROP TABLE IF EXISTS `lms_team`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `lms_team` (
  `lms_team_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_team_name` varchar(45) NOT NULL,
  `lms_team_active` tinyint(1) DEFAULT '1',
  `lms_team_wins` int(2) DEFAULT '0',
  PRIMARY KEY (`lms_team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `lms_team`
--

LOCK TABLES `lms_team` WRITE;
/*!40000 ALTER TABLE `lms_team` DISABLE KEYS */;
INSERT INTO `lms_team` VALUES (1,'Arsenal',1,0),(2,'Manchester United',1,0),(3,'Liverpool',1,0),(4,'Manchester City',1,0),(5,'Aston Villa',1,0),(6,'Leicester',1,0),(7,'Chelsea',1,0),(8,'Wolverhampton Wanderers',1,0),(9,'Sheffield United',1,0),(14,'Crystal Palace',1,0),(15,'Tottenham',1,0),(16,'Everton',1,0),(17,'Southampton',1,0),(18,'Newcastle',1,0),(19,'Brighton',1,0),(20,'Burnley',1,0),(21,'West Ham',1,0),(22,'Watford',1,0),(23,'Bournemouth',1,0),(24,'Norwich',1,0);
/*!40000 ALTER TABLE `lms_team` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2020-01-30 16:25:10
