-- MySQL dump 10.13  Distrib 5.7.9, for Win32 (AMD64)
--
-- Host: localhost    Database: new_inventory
-- ------------------------------------------------------
-- Server version	5.6.17

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
-- Table structure for table `BaseQuantity`
--

DROP TABLE IF EXISTS `BaseQuantity`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BaseQuantity` (
  `item_id` int(11) NOT NULL,
  `quantity` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`item_id`),
  UNIQUE KEY `item_id_UNIQUE` (`item_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `BaseQuantity`
--

LOCK TABLES `BaseQuantity` WRITE;
/*!40000 ALTER TABLE `BaseQuantity` DISABLE KEYS */;
INSERT INTO `BaseQuantity` VALUES (44,20.00),(45,26.00),(47,400.00),(1,12.00),(3,10.00),(6,5.00),(5,42.00),(4,5.00),(41,3.00),(48,23.00),(49,2123.00),(50,21.00),(51,11.00),(52,121.00),(53,0.00),(54,0.00),(55,0.00),(57,1.00),(58,1.00);
/*!40000 ALTER TABLE `BaseQuantity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Category` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `creation_date` date NOT NULL,
  `deletion_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=29 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Category`
--

LOCK TABLES `Category` WRITE;
/*!40000 ALTER TABLE `Category` DISABLE KEYS */;
INSERT INTO `Category` VALUES (1,4,'Walk In Fridge','2015-01-01',NULL),(2,2,'Drinks','2015-01-01',NULL),(3,1,'Grocery','2015-01-01',NULL),(4,0,'Frozen','2015-01-01',NULL),(5,3,'Paper Products','2015-01-01',NULL),(6,NULL,'New Thing','2016-01-11','2016-01-11'),(9,NULL,'test','2016-01-10','2016-01-17'),(10,NULL,'Dry Ration','2016-01-20','2016-01-20'),(11,NULL,'test','2016-01-31','2016-01-31'),(12,NULL,'new one','2016-01-31','2016-01-31'),(13,NULL,'new','2016-02-13','2016-02-13'),(14,NULL,'a','2016-02-13','2016-02-13'),(15,NULL,'new','2016-02-14','2016-02-14'),(16,NULL,'test','2016-02-16','2016-02-18'),(17,NULL,'new','2016-02-16','2016-02-18'),(18,NULL,'anothe','2016-02-18','2016-02-18'),(19,NULL,'anothe','2016-02-18','2016-02-18'),(20,NULL,'another','2016-02-18','2016-02-18'),(21,NULL,'newnew','0000-00-00','2016-02-25'),(22,NULL,'this this','2016-02-25','2016-02-25'),(23,NULL,'test cat','2016-03-22','2016-03-22'),(24,NULL,'test c','2016-03-22','2016-03-22'),(25,NULL,'test','2016-03-22','2016-03-22'),(26,NULL,'test','2016-03-23','2016-03-23'),(27,4,'test','2016-03-24','2016-03-25'),(28,0,'tes','2016-03-25','2016-03-25');
/*!40000 ALTER TABLE `Category` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Conversation`
--

DROP TABLE IF EXISTS `Conversation`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Conversation` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime NOT NULL,
  `sender` varchar(45) DEFAULT NULL,
  `receiver` varchar(45) DEFAULT NULL,
  `title` text,
  `sender_conversationStatusId` int(11) DEFAULT NULL,
  `receiver_conversationStatusId` int(11) NOT NULL,
  `sender_destroyDate` date DEFAULT NULL,
  `receiver_destroyDate` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  KEY `FK_sender_idx` (`sender`),
  KEY `FK_receiver_idx` (`receiver`),
  KEY `FK_senderConversationId_idx` (`sender_conversationStatusId`),
  KEY `FK_receiverConversationId_idx` (`receiver_conversationStatusId`)
) ENGINE=MyISAM AUTO_INCREMENT=38 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `ConversationStatus`
--

DROP TABLE IF EXISTS `ConversationStatus`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ConversationStatus` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `status` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ConversationStatus`
--

LOCK TABLES `ConversationStatus` WRITE;
/*!40000 ALTER TABLE `ConversationStatus` DISABLE KEYS */;
INSERT INTO `ConversationStatus` VALUES (1,'unread'),(2,'read'),(3,'deleted'),(4,'destroy');
/*!40000 ALTER TABLE `ConversationStatus` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Inventory`
--

DROP TABLE IF EXISTS `Inventory`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Inventory` (
  `date` date NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` decimal(11,2) DEFAULT NULL,
  `notes` text,
  PRIMARY KEY (`item_id`,`date`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Inventory`
--

LOCK TABLES `Inventory` WRITE;
/*!40000 ALTER TABLE `Inventory` DISABLE KEYS */;
INSERT INTO `Inventory` VALUES ('2016-01-18',1,4.00,''),('2016-01-20',1,10.00,''),('2016-01-16',2,1.00,''),('2016-02-08',2,3.00,''),('2016-01-16',3,0.00,''),('2016-01-12',4,2.00,''),('2016-01-19',4,4.00,''),('2016-02-07',4,1.00,''),('2016-01-15',5,2006.00,''),('2016-01-17',5,20.00,''),('2016-01-18',5,20.00,''),('2016-01-19',5,3.00,''),('2016-01-20',5,10.00,''),('2016-01-22',5,12.00,''),('2016-01-25',5,12.00,''),('2016-01-26',6,0.00,''),('2016-01-16',7,4.00,''),('2016-02-14',4,1.00,''),('2016-02-15',4,7.00,''),('2016-02-15',1,4.00,''),('2016-02-15',5,7.00,''),('2016-02-15',3,7.00,''),('2016-02-15',6,9.00,''),('2016-02-26',4,40.00,''),('2016-02-26',1,20.00,''),('2016-02-26',5,15.00,''),('2016-02-26',3,19.00,''),('2016-02-26',6,2.00,''),('2016-02-27',4,49.00,''),('2016-02-27',1,50.00,''),('2016-02-27',5,50.00,''),('2016-02-27',3,60.00,''),('2016-02-27',6,10.00,''),('2016-03-06',4,5.00,''),('2016-03-06',1,2.50,''),('2016-03-08',4,1.00,''),('2016-03-08',1,400.00,''),('2016-03-08',5,1.00,''),('2016-03-08',3,1.00,''),('2016-03-08',6,12.00,''),('2016-03-23',3,65.00,''),('2016-03-23',44,55.00,''),('2016-03-23',61,131.00,'');
/*!40000 ALTER TABLE `Inventory` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Item`
--

DROP TABLE IF EXISTS `Item`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Item` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(10) DEFAULT NULL,
  `order_id` int(11) DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `unit` varchar(45) NOT NULL,
  `creation_date` date NOT NULL,
  `deletion_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=63 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Item`
--

LOCK TABLES `Item` WRITE;
/*!40000 ALTER TABLE `Item` DISABLE KEYS */;
INSERT INTO `Item` VALUES (1,4,1,'Paratha','un-opened pack','2016-01-11',NULL),(2,5,NULL,'Big Biryani Box','un-opened sleeve','2016-01-11','2016-02-13'),(3,5,2,'Shawarma','kg','2016-01-11',NULL),(4,2,NULL,'Water','bottles','2016-01-11',NULL),(5,4,0,'tandoori chicken','quater leg','2016-01-11',NULL),(6,1,2,'Shezan mango','bottles','2016-01-11',NULL),(7,NULL,NULL,'Pakola','bottles','2016-01-11','2016-02-13'),(8,NULL,NULL,'test','test','2016-01-11','2016-01-26'),(9,NULL,NULL,'Meat','Packaged','2016-01-13','2016-01-13'),(10,1,NULL,'Bread','bags','2016-01-20','2016-01-20'),(12,NULL,NULL,'Mangoes','21','2016-01-31','2016-01-31'),(13,NULL,NULL,'new','new','2016-02-13','2016-02-25'),(14,NULL,NULL,'q','q','2016-02-13','2016-02-13'),(15,NULL,NULL,'new','new','2016-02-14','2016-02-25'),(16,NULL,NULL,'new ','new','2016-02-25','2016-02-25'),(17,NULL,NULL,'this','s','2016-02-25','2016-02-26'),(18,NULL,NULL,'asf','asf','2016-02-25','2016-02-25'),(19,NULL,NULL,'new item','new new','2016-02-25','2016-02-25'),(20,5,1,'something new','sad','2016-02-25',NULL),(21,NULL,NULL,'asd','asd','2016-02-25','2016-02-25'),(22,NULL,NULL,'this','c','2016-02-25','2016-02-26'),(23,NULL,5,'this','ts','2016-02-26',NULL),(45,NULL,8,'1 new testing','new new','2016-03-06',NULL),(24,5,0,'1','1','2016-02-27',NULL),(25,5,9,'2','2','2016-02-27',NULL),(26,NULL,NULL,'3','3','2016-02-27','2016-03-14'),(27,5,3,'4','4','2016-02-27',NULL),(28,NULL,NULL,'5','5','2016-02-27','2016-03-14'),(29,5,15,'6','6','2016-02-27',NULL),(30,5,6,'7','7','2016-02-27',NULL),(31,5,10,'8','8','2016-02-27',NULL),(32,NULL,NULL,'9','9','2016-02-27','2016-03-14'),(33,5,4,'10','10','2016-02-27',NULL),(34,5,5,'11','11','2016-02-27',NULL),(35,5,12,'12','12','2016-02-27',NULL),(36,5,13,'13','13','2016-02-27',NULL),(37,NULL,0,'14','14','2016-02-27',NULL),(38,5,7,'15','15','2016-02-27',NULL),(39,NULL,0,'16','16','2016-02-27',NULL),(40,NULL,0,'17','17','2016-02-27',NULL),(41,NULL,3,'18','18','2016-02-27',NULL),(42,1,1,'19','19','2016-02-27',NULL),(43,1,0,'20','20','2016-02-27',NULL),(44,NULL,4,'testing','testing','2016-03-02',NULL),(47,NULL,NULL,'another testing','newnewne','2016-03-06','2016-03-14'),(46,NULL,NULL,'1 new test','new new','2016-03-06','2016-03-06'),(48,1,3,'21','213','2016-03-18',NULL),(49,NULL,1,'22','213','2016-03-18',NULL),(50,NULL,10,'23','2323','2016-03-18',NULL),(51,NULL,3,'24','12','2016-03-18',NULL),(52,NULL,7,'25','11','2016-03-18',NULL),(53,5,11,'26','as','2016-03-18',NULL),(54,5,18,'27','sd','2016-03-18',NULL),(55,NULL,0,'28','asd','2016-03-18',NULL),(56,5,14,'29','23','2016-03-18',NULL),(57,5,17,'30','2','2016-03-19',NULL),(58,5,8,'31','12','2016-03-19',NULL),(59,5,16,'32','23','2016-03-19',NULL),(60,NULL,2,'33','21','2016-03-19',NULL),(61,NULL,1,'34','1','2016-03-19',NULL),(62,NULL,3,'35','1','2016-03-19',NULL);
/*!40000 ALTER TABLE `Item` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Message`
--

DROP TABLE IF EXISTS `Message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Message` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `timestamp` datetime DEFAULT NULL,
  `sender` varchar(45) DEFAULT NULL,
  `receiver` varchar(45) DEFAULT NULL,
  `message` text NOT NULL,
  `attachment` text,
  `conversation_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=81 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Table structure for table `User`
--

DROP TABLE IF EXISTS `User`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `User` (
  `username` varchar(45) NOT NULL,
  `first_name` varchar(45) DEFAULT NULL,
  `last_name` varchar(45) DEFAULT NULL,
  `password_hash` text NOT NULL,
  `userrole_id` int(11) NOT NULL,
  `time_zone` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`username`),
  UNIQUE KEY `username_UNIQUE` (`username`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `User`
--

LOCK TABLES `User` WRITE;
/*!40000 ALTER TABLE `User` DISABLE KEYS */;
INSERT INTO `User` VALUES ('atif','Atif','Hussain','$2y$10$okObY3TZpUmlHGMlOP8uX.618JISHnpsf/up8Xn9h3tsztlZvkGjS',1,'America/Toronto'),('test','test','1','$2y$10$tjaNFUINXjcKtkruBiyeYeZkaTaEWTbDG5v/w9bRy4DYNWyVzvztm',1,NULL),('user','user','3','$2y$10$Ou/slOrcgQiIUA7JlJslpu5giIXa0Fq8TL8QPWqDgrMgb8HL5hZNK',1,NULL),('wasif','Wasif','Hussain','$2y$10$JmULLgUnC/THK8iaueUmZeKQzKHFJdtIImi0OiYXhcoSYb2tT0L8a',1,'Asia/Karachi');
/*!40000 ALTER TABLE `User` ENABLE KEYS */;
UNLOCK TABLES;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_ENGINE_SUBSTITUTION' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50017 DEFINER=`root`@`localhost`*/ /*!50003 TRIGGER `new_inventory`.`User_AFTER_UPDATE` AFTER UPDATE ON `User` FOR EACH ROW
BEGIN
	UPDATE Conversation SET sender = IF(sender = OLD.username, NEW.username, sender),
							receiver = IF(receiver = OLD.username, NEW.username, receiver);
    UPDATE Message SET sender = IF(sender = OLD.username, NEW.username, sender),
					   receiver = IF(receiver = OLD.username, NEW.username, receiver);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;

--
-- Table structure for table `UserRole`
--

DROP TABLE IF EXISTS `UserRole`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UserRole` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `role` varchar(45) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`),
  UNIQUE KEY `role_UNIQUE` (`role`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `UserRole`
--

LOCK TABLES `UserRole` WRITE;
/*!40000 ALTER TABLE `UserRole` DISABLE KEYS */;
INSERT INTO `UserRole` VALUES (1,'admin'),(2,'data_user');
/*!40000 ALTER TABLE `UserRole` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Variables`
--

DROP TABLE IF EXISTS `Variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Variables` (
  `name` varchar(45) NOT NULL,
  `value` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Variables`
--

LOCK TABLES `Variables` WRITE;
/*!40000 ALTER TABLE `Variables` DISABLE KEYS */;
INSERT INTO `Variables` VALUES ('BaseSales','2609'),('ExpectedSales','6000');
/*!40000 ALTER TABLE `Variables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping events for database 'new_inventory'
--

--
-- Dumping routines for database 'new_inventory'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-03-26 22:26:41
