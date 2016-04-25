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
INSERT INTO `BaseQuantity` VALUES (44,20.00),(45,26.00),(47,400.00),(1,20.00),(3,100.00),(6,50.00),(5,18.00),(4,200.00),(41,3.00),(48,23.00),(49,2123.00),(50,21.00),(51,11.00),(52,121.00),(53,0.00),(54,0.00),(55,0.00),(57,1.00),(58,1.00),(64,0.00),(65,0.00),(66,0.00),(35,1.00),(0,12.00),(36,12.00),(34,50.00);
/*!40000 ALTER TABLE `BaseQuantity` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Category`
--

DROP TABLE IF EXISTS `Category`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Category` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `creation_date` date NOT NULL,
  `deletion_date` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=45 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Category`
--

LOCK TABLES `Category` WRITE;
/*!40000 ALTER TABLE `Category` DISABLE KEYS */;
INSERT INTO `Category` VALUES (1,3,'Walk In Fridge','2015-01-01',NULL),(2,0,'Drinks','2015-01-01',NULL),(3,2,'Grocery','2015-01-01',NULL),(4,1,'Frozen','2015-01-01',NULL),(5,4,'Paper Products','2015-01-01',NULL),(6,NULL,'New Thing','2016-01-11','2016-01-11'),(9,NULL,'test','2016-01-10','2016-01-17'),(10,NULL,'Dry Ration','2016-01-20','2016-01-20'),(11,NULL,'test','2016-01-31','2016-01-31'),(12,NULL,'new one','2016-01-31','2016-01-31'),(13,NULL,'new','2016-02-13','2016-02-13'),(14,NULL,'a','2016-02-13','2016-02-13'),(15,NULL,'new','2016-02-14','2016-02-14'),(16,NULL,'test','2016-02-16','2016-02-18'),(17,NULL,'new','2016-02-16','2016-02-18'),(18,NULL,'anothe','2016-02-18','2016-02-18'),(19,NULL,'anothe','2016-02-18','2016-02-18'),(20,NULL,'another','2016-02-18','2016-02-18'),(21,NULL,'newnew','0000-00-00','2016-02-25'),(22,NULL,'this this','2016-02-25','2016-02-25'),(23,NULL,'test cat','2016-03-22','2016-03-22'),(24,NULL,'test c','2016-03-22','2016-03-22'),(25,NULL,'test','2016-03-22','2016-03-22'),(26,NULL,'test','2016-03-23','2016-03-23'),(27,4,'test','2016-03-24','2016-03-25'),(28,0,'tes','2016-03-25','2016-03-25'),(29,5,'test','2016-03-26','2016-03-26'),(30,0,'test','2016-03-27','2016-03-27'),(31,13,'1','2016-03-27','2016-03-27'),(32,12,'2','2016-03-27','2016-03-27'),(33,7,'3','2016-03-27',NULL),(34,12,'4','2016-03-27',NULL),(35,12,'5','2016-03-27','2016-03-31'),(36,13,'6','2016-03-27',NULL),(37,5,'7','2016-03-27','2016-03-27'),(38,11,'8','2016-03-27',NULL),(39,8,'9','2016-03-27',NULL),(44,6,'7','2016-04-24',NULL),(40,10,'1','2016-03-29',NULL),(41,2,'2','2016-03-29','2016-03-31'),(42,9,'12','2016-03-29',NULL),(43,5,'5','2016-04-24',NULL);
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
) ENGINE=MyISAM AUTO_INCREMENT=41 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Conversation`
--

LOCK TABLES `Conversation` WRITE;
/*!40000 ALTER TABLE `Conversation` DISABLE KEYS */;
INSERT INTO `Conversation` VALUES (1,'2016-02-13 06:34:18','wasif','atif','new',4,4,'2016-02-23','2016-02-25'),(2,'2016-02-13 06:39:33','wasif','atif','testing',4,4,'2016-03-14','2016-02-25'),(3,'2016-02-19 18:52:27','wasif','atif','testing testing',4,4,'2016-03-07','2016-02-29'),(4,'2016-02-14 17:19:22','wasif','atif','newne',4,4,'2016-03-14','2016-02-25'),(5,'2016-02-16 13:32:01','atif','wasif','asd',4,4,'2016-02-25','2016-02-24'),(6,'2016-02-16 16:26:20','atif','wasif','asd',4,4,'2016-02-25','2016-02-24'),(7,'2016-02-16 16:28:08','atif','wasif','dsf',4,4,'2016-02-25','2016-02-24'),(8,'2016-02-16 17:15:54','atif','wasif','asd',4,4,'2016-02-25','2016-02-24'),(9,'2016-02-16 17:29:32','wasif','atif','asd',4,4,'2016-02-23','2016-02-25'),(10,'2016-02-16 17:30:02','wasif','atif','fdf',4,4,'2016-02-23','2016-03-04'),(11,'2016-02-16 17:31:14','wasif','atif','asfsf',4,4,'2016-02-24','2016-03-04'),(12,'2016-02-16 17:32:13','atif','wasif','asda',4,4,'2016-03-04','2016-02-24'),(13,'2016-02-16 17:42:48','atif','wasif','asd',4,4,'2016-03-04','2016-02-24'),(14,'2016-02-16 17:43:43','atif','wasif','asd',4,4,'2016-03-04','2016-02-24'),(15,'2016-02-16 17:46:23','atif','wasif','asds',4,2,'2016-03-04',NULL),(16,'2016-02-16 17:49:59','atif','wasif','asd',4,2,'2016-03-04',NULL),(17,'2016-02-17 06:54:54','wasif','atif','qwe',2,4,NULL,'2016-03-04'),(18,'2016-02-17 08:57:16','wasif','atif','khsd',2,4,NULL,'2016-03-04'),(35,'2016-03-07 12:34:19','wasif','atif','NEW CONVO',2,1,NULL,NULL),(19,'2016-02-17 09:02:08','wasif','atif','qwe`q`qwe',2,4,NULL,'2016-03-04'),(20,'2016-02-17 09:02:15','wasif','atif','sdf',2,4,NULL,'2016-03-04'),(21,'2016-02-17 09:02:33','wasif','atif','asd',4,4,'2016-03-14','2016-03-04'),(22,'2016-02-17 11:29:25','wasif','atif','submit test',4,4,'2016-03-14','2016-03-04'),(23,'2016-02-17 11:29:40','wasif','atif','another test',4,4,'2016-03-14','2016-03-04'),(24,'2016-02-17 11:30:41','wasif','atif','one more',2,2,NULL,NULL),(25,'2016-02-17 11:30:51','wasif','atif','again',2,2,NULL,NULL),(26,'2016-02-17 11:31:19','wasif','atif','asd',2,2,NULL,NULL),(27,'2016-02-17 11:36:28','wasif','atif','lkj',4,2,'2016-02-27',NULL),(28,'2016-02-17 11:38:09','wasif','atif','asdsd',2,2,NULL,NULL),(29,'2016-02-17 11:39:49','wasif','atif','asd',4,2,'2016-03-06',NULL),(30,'2016-02-17 11:42:29','wasif','atif','mmm',4,2,'2016-03-06',NULL),(31,'2016-02-17 11:45:43','wasif','atif','tt',4,2,'2016-03-06',NULL),(32,'2016-02-19 17:29:01','wasif','atif','sdf',2,2,NULL,NULL),(33,'2016-02-19 17:28:34','wasif','atif','asd',2,1,NULL,NULL),(34,'2016-02-17 12:03:58','wasif','atif','sds',4,2,'2016-03-06',NULL),(37,'2016-03-23 06:33:39','wasif','atif','hgdh',2,1,NULL,NULL),(36,'2016-03-08 13:30:55','wasif','atif','PTEST',2,1,NULL,NULL),(38,'2016-04-23 16:26:22','wasif','atif','jj',2,1,NULL,NULL),(39,'2016-04-24 07:59:21','wasif','atif','d',2,1,NULL,NULL),(40,'2016-04-24 08:02:07','wasif','atif','asd',2,1,NULL,NULL);
/*!40000 ALTER TABLE `Conversation` ENABLE KEYS */;
UNLOCK TABLES;

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
INSERT INTO `Inventory` VALUES ('2016-01-18',1,4.00,''),('2016-01-20',1,10.00,''),('2016-01-16',2,1.00,''),('2016-02-08',2,3.00,''),('2016-01-16',3,0.00,''),('2016-01-12',4,2.00,''),('2016-01-19',4,4.00,''),('2016-02-07',4,1.00,''),('2016-01-15',5,2006.00,''),('2016-01-17',5,20.00,''),('2016-01-18',5,20.00,''),('2016-01-19',5,3.00,''),('2016-01-20',5,10.00,''),('2016-01-22',5,12.00,''),('2016-01-25',5,12.00,''),('2016-01-26',6,0.00,''),('2016-01-16',7,4.00,''),('2016-02-14',4,1.00,''),('2016-02-15',4,7.00,''),('2016-02-15',1,4.00,''),('2016-02-15',5,7.00,''),('2016-02-15',3,7.00,''),('2016-02-15',6,9.00,''),('2016-02-26',4,40.00,''),('2016-02-26',1,20.00,''),('2016-02-26',5,15.00,''),('2016-02-26',3,19.00,''),('2016-02-26',6,2.00,''),('2016-02-27',4,49.00,''),('2016-02-27',1,50.00,''),('2016-02-27',5,50.00,''),('2016-02-27',3,60.00,''),('2016-02-27',6,10.00,''),('2016-03-06',4,5.00,''),('2016-03-06',1,2.50,''),('2016-03-08',4,1.00,''),('2016-03-08',1,400.00,''),('2016-03-08',5,1.00,''),('2016-03-08',3,1.00,''),('2016-03-08',6,12.00,''),('2016-03-23',3,65.00,''),('2016-03-23',44,55.00,''),('2016-03-23',61,131.00,''),('2016-04-23',3,80.00,''),('2016-04-21',4,876.00,''),('2016-04-21',6,987.00,''),('2016-04-21',5,8.00,''),('2016-04-21',1,7.00,''),('2016-04-21',3,5.00,''),('2016-04-21',27,8.00,''),('2016-04-21',33,2.00,''),('2016-04-21',34,5.00,''),('2016-04-21',30,7.00,''),('2016-04-21',38,9.00,''),('2016-04-21',58,9.00,''),('2016-04-21',25,0.00,''),('2016-04-23',4,10.00,''),('2016-04-23',5,50.00,''),('2016-04-23',1,20.00,''),('2016-04-23',6,14.00,'');
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
  `order_id` int(10) DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `unit` varchar(45) NOT NULL,
  `creation_date` date NOT NULL,
  `deletion_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=71 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Item`
--

LOCK TABLES `Item` WRITE;
/*!40000 ALTER TABLE `Item` DISABLE KEYS */;
INSERT INTO `Item` VALUES (1,4,1,'Paratha','un-opened pack','2016-01-11',NULL),(2,5,NULL,'Big Biryani Box','un-opened sleeve','2016-01-11','2016-02-13'),(3,5,2,'Shawarma','kg','2016-01-11',NULL),(4,2,0,'Water','bottles','2016-01-11',NULL),(5,4,0,'tandoori chicken','quater leg','2016-01-11',NULL),(6,1,0,'Shezan mango','bottles','2016-01-11',NULL),(7,NULL,NULL,'Pakola','bottles','2016-01-11','2016-02-13'),(8,NULL,NULL,'test','test','2016-01-11','2016-01-26'),(9,NULL,NULL,'Meat','Packaged','2016-01-13','2016-01-13'),(10,1,NULL,'Bread','bags','2016-01-20','2016-01-20'),(12,NULL,NULL,'Mangoes','21','2016-01-31','2016-01-31'),(13,NULL,NULL,'new','new','2016-02-13','2016-02-25'),(14,NULL,NULL,'q','q','2016-02-13','2016-02-13'),(15,NULL,NULL,'new','new','2016-02-14','2016-02-25'),(16,NULL,NULL,'new ','new','2016-02-25','2016-02-25'),(17,3,NULL,'this','s','2016-02-25','2016-02-26'),(18,NULL,NULL,'asf','asf','2016-02-25','2016-02-25'),(19,NULL,NULL,'new item','new new','2016-02-25','2016-02-25'),(20,5,1,'something new','sad','2016-02-25','2016-04-15'),(21,NULL,NULL,'asd','asd','2016-02-25','2016-02-25'),(22,3,NULL,'this','c','2016-02-25','2016-02-26'),(23,3,0,'this','ts','2016-02-26','2016-04-14'),(45,NULL,8,'1 new testing','new new','2016-03-06','2016-04-14'),(24,5,0,'1','1','2016-02-27','2016-04-14'),(25,5,9,'2','2','2016-02-27',NULL),(26,NULL,NULL,'3','3','2016-02-27','2016-03-14'),(27,5,3,'4','4','2016-02-27','2016-04-23'),(28,NULL,NULL,'5','5','2016-02-27','2016-03-14'),(29,5,15,'6','1','2016-02-27','2016-04-25'),(30,5,6,'7','7','2016-02-27','2016-04-25'),(31,5,10,'8','8','2016-02-27',NULL),(32,NULL,NULL,'9','9','2016-02-27','2016-03-14'),(34,5,5,'19','11','2016-02-27','2016-04-21'),(35,5,12,'12','12','2016-02-27',NULL),(36,5,13,'13','13','2016-02-27',NULL),(37,2,1,'14','14','2016-02-27',NULL),(38,5,7,'15','15','2016-02-27',NULL),(39,2,4,'16','16','2016-02-27',NULL),(41,NULL,3,'18','18','2016-02-27',NULL),(42,NULL,4,'19','19','2016-02-27','2016-04-18'),(43,NULL,0,'20','20','2016-02-27',NULL),(44,NULL,4,'testing','testing','2016-03-02','2016-04-14'),(47,NULL,NULL,'another testing','newnewne','2016-03-06','2016-03-14'),(46,NULL,NULL,'1 new test','new new','2016-03-06','2016-03-06'),(48,2,3,'21','213','2016-03-18',NULL),(49,3,2,'22','213','2016-03-18',NULL),(50,3,1,'23','2323','2016-03-18',NULL),(51,3,3,'24','12','2016-03-18',NULL),(52,3,4,'25','11','2016-03-18',NULL),(53,5,11,'26','as','2016-03-18',NULL),(54,5,18,'27','sd','2016-03-18',NULL),(55,2,5,'28','asd','2016-03-18',NULL),(56,5,14,'29','23','2016-03-18',NULL),(57,5,17,'30','2','2016-03-19',NULL),(58,5,8,'31','12','2016-03-19',NULL),(59,5,16,'32','23','2016-03-19',NULL),(60,NULL,2,'33','21','2016-03-19',NULL),(61,2,7,'34','1','2016-03-19',NULL),(62,2,6,'35','1','2016-03-19',NULL),(63,NULL,0,'new test item','new test unit','2016-04-14','2016-04-14'),(70,2,6,'thisthatwho','a','2016-04-23','2016-04-23'),(64,NULL,0,'new new test','new new test ','2016-04-14','2016-04-14'),(65,NULL,0,'abdvsa','qweqweqw','2016-04-14','2016-04-14'),(66,NULL,0,'new new new ne a','asda','2016-04-14','2016-04-14'),(67,NULL,0,'asdasdasd','asdas','2016-04-14','2016-04-14'),(68,NULL,0,'testing item','blabla','2016-04-15','2016-04-15'),(69,2,2,'107','17','2016-04-23',NULL);
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
) ENGINE=MyISAM AUTO_INCREMENT=84 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Message`
--

LOCK TABLES `Message` WRITE;
/*!40000 ALTER TABLE `Message` DISABLE KEYS */;
INSERT INTO `Message` VALUES (1,'2016-02-13 06:34:18','wasif','atif','test','',1),(2,'2016-02-13 06:39:33','wasif','atif','...','',2),(3,'2016-02-14 12:57:22','wasif','atif','asd','',3),(4,'2016-02-14 12:58:20','wasif','atif','new new\r\n',NULL,3),(5,'2016-02-14 17:19:22','wasif','atif','new','',4),(6,'2016-02-16 13:32:01','atif','wasif','asd','',5),(7,'2016-02-16 16:26:20','atif','wasif','asds','',6),(8,'2016-02-16 16:28:08','atif','wasif','asfa','',7),(9,'2016-02-16 17:15:54','atif','wasif','asd','',8),(10,'2016-02-16 17:29:32','wasif','atif','asdas','',9),(11,'2016-02-16 17:30:02','wasif','atif','dfd','',10),(12,'2016-02-16 17:31:14','wasif','atif','asfsf','',11),(13,'2016-02-16 17:32:13','atif','wasif','asda\r\n','',12),(14,'2016-02-16 17:42:48','atif','wasif','asdasd','',13),(15,'2016-02-16 17:43:43','atif','wasif','sda','',14),(16,'2016-02-16 17:46:23','atif','wasif','asd\r\n','',15),(17,'2016-02-16 17:49:59','atif','wasif','asd','',16),(18,'2016-02-17 06:54:54','wasif','atif','asd','',17),(19,'2016-02-17 08:57:16','wasif','atif','sdfs','',18),(20,'2016-02-17 09:02:08','wasif','atif','qwe\r\n','',19),(21,'2016-02-17 09:02:15','wasif','atif','adf','',20),(22,'2016-02-17 09:02:33','wasif','atif','asd\r\n','',21),(23,'2016-02-17 11:29:25','wasif','atif','asdsds','',22),(24,'2016-02-17 11:29:40','wasif','atif','as\r\n','',23),(25,'2016-02-17 11:30:41','wasif','atif','asd\r\n','',24),(26,'2016-02-17 11:30:51','wasif','atif','asd','',25),(27,'2016-02-17 11:31:19','wasif','atif','asd','',26),(28,'2016-02-17 11:36:28','wasif','atif','lkj',' ',27),(29,'2016-02-17 11:38:09','wasif','atif','asd','',28),(30,'2016-02-17 11:39:49','wasif','atif','asdas','',29),(31,'2016-02-17 11:42:29','wasif','atif','nn','\r\n            <tbody><tr id=\"print_date\">\r\n                <th colspan=\"5\">Wed, Feb 17 2016</th>\r\n            </tr>\r\n                                                                                <tr id=\"category\"><th colspan=\"5\">Drinks</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Water</td>\r\n                    <td>bottles</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Frozen</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Paratha</td>\r\n                    <td>un-opened pack</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr>\r\n                                        <td>tandoori chicken</td>\r\n                    <td>quater leg</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Paper Products</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Shawarma</td>\r\n                    <td>kg</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Walk In Fridge</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Shezan mango</td>\r\n                    <td>bottles</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                    </tbody>',30),(32,'2016-02-17 11:45:43','wasif','atif','tt','\r\n        <table class=\"user_table\" id=\"print\">\r\n            <tbody><tr id=\"print_date\">\r\n                <th colspan=\"5\">Wed, Feb 17 2016</th>\r\n            </tr>\r\n                                                                                <tr id=\"category\"><th colspan=\"5\">Drinks</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Water</td>\r\n                    <td>bottles</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Frozen</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Paratha</td>\r\n                    <td>un-opened pack</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr>\r\n                                        <td>tandoori chicken</td>\r\n                    <td>quater leg</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Paper Products</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Shawarma</td>\r\n                    <td>kg</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Walk In Fridge</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Shezan mango</td>\r\n                    <td>bottles</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                    </tbody></table>\r\n    ',31),(33,'2016-02-17 11:45:58','wasif','atif','sdf','',32),(34,'2016-02-17 12:03:39','wasif','atif','asd','',33),(35,'2016-02-17 12:03:58','wasif','atif','asd','\r\n        <table class=\"user_table\" id=\"print\">\r\n            <tbody><tr id=\"print_date\">\r\n                <th colspan=\"5\">Wed, Feb 17 2016</th>\r\n            </tr>\r\n                                                                                <tr id=\"category\"><th colspan=\"5\">Drinks</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Water</td>\r\n                    <td>bottles</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Frozen</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Paratha</td>\r\n                    <td>un-opened pack</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr>\r\n                                        <td>tandoori chicken</td>\r\n                    <td>quater leg</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Paper Products</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Shawarma</td>\r\n                    <td>kg</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\"><th colspan=\"5\">Walk In Fridge</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr>\r\n                                        <td>Shezan mango</td>\r\n                    <td>bottles</td>\r\n                    <td>-</td>\r\n                    <td>-</td>\r\n                    <td></td>\r\n                </tr>\r\n                    </tbody></table>\r\n    ',34),(36,'2016-02-18 13:55:41','wasif','atif','asdasdasdasdlaksjlaksjvasjvaspovjaspvoasjvpoasjvpaosvjaposvjapsovjaspvoajspvoajsvpoasjvpaosjvpaosjvapsovjaspovjaspovajsvpoasjvpaosjvaposvjaposvjasovpjapsovjapsovjaspvojaspovjaspvosjapovasjpovajsvpoajsvpaosjvpaosvjaspovjaspov',NULL,33),(37,'2016-02-18 13:55:41','wasif','atif','asdasdasdasdlaksjlaksjvasjvaspovjaspvoasjvpoasjvpaosvjaposvjapsovjaspvoajspvoajsvpoasjvpaosjvpaosjvapsovjaspovjaspovajsvpoasjvpaosjvaposvjaposvjasovpjapsovjapsovjaspvojaspovjaspvosjapovasjpovajsvpoajsvpaosjvpaosvjaspovjaspov',NULL,33),(38,'2016-02-18 14:32:22','atif','wasif','messaging messaging\r\n',NULL,33),(39,'2016-02-18 14:32:22','atif','wasif','messaging messaging\r\n',NULL,33),(40,'2016-02-18 14:36:58','atif','wasif','another message',NULL,33),(41,'2016-02-18 14:36:58','atif','wasif','another message',NULL,33),(42,'2016-02-18 14:38:30','atif','wasif','asd\r\n',NULL,33),(43,'2016-02-18 14:38:30','atif','wasif','asd\r\n',NULL,33),(44,'2016-02-18 14:40:48','atif','wasif','new',NULL,33),(45,'2016-02-18 14:40:48','atif','wasif','new',NULL,33),(58,'2016-02-19 18:25:35','wasif','atif','new this thing\r\n',NULL,3),(47,'2016-02-18 16:02:48','atif','wasif','two messages',NULL,33),(48,'2016-02-18 16:04:53','wasif','atif','testing\r\n',NULL,33),(49,'2016-02-18 17:07:14','atif','wasif','more testing\r\n',NULL,33),(50,'2016-02-18 17:07:37','wasif','atif','more more testing',NULL,33),(51,'2016-02-18 17:08:34','atif','wasif','hello how are you ',NULL,33),(52,'2016-02-18 17:08:47','wasif','atif','im good how are u\r\n ',NULL,33),(53,'2016-02-18 17:09:29','atif','wasif','not bad \r\nhow goes things ',NULL,33),(54,'2016-02-18 17:10:07','atif','wasif','this is a break test.\r\n\r\nits doesnt seem like it breaks things up when we type.\r\n\r\njust checking if it really works or not\r\n',NULL,33),(55,'2016-02-19 16:54:06','wasif','atif','something something\r\n',NULL,3),(56,'2016-02-19 17:28:34','wasif','atif','max test',NULL,33),(57,'2016-02-19 17:29:01','wasif','atif','blah blah',NULL,32),(59,'2016-02-19 18:52:27','wasif','atif','another new message\r\n',NULL,3),(60,'2016-02-25 05:46:40','wasif','atif','la la la\r\n','',35),(61,'2016-03-07 07:46:58','wasif','atif','azd\r\n',NULL,35),(62,'2016-03-07 07:47:01','wasif','atif','sdv',NULL,35),(63,'2016-03-07 07:47:07','wasif','atif','sdvdsv',NULL,35),(64,'2016-03-07 07:47:10','wasif','atif','sdvsdv',NULL,35),(65,'2016-03-07 07:47:13','wasif','atif','dvsdvsdv',NULL,35),(66,'2016-03-07 07:47:16','wasif','atif','sdvsdvsdvsdv',NULL,35),(67,'2016-03-07 07:47:19','wasif','atif','sdvsdvsdv',NULL,35),(68,'2016-03-07 07:47:23','wasif','atif','dsvsdvsdvsdv',NULL,35),(69,'2016-03-07 07:47:28','wasif','atif','dvsdvsdvsdvsdv',NULL,35),(70,'2016-03-07 08:00:24','wasif','atif','asdasdasdasdaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',NULL,35),(71,'2016-03-07 08:01:23','wasif','atif','asdasdasdasdaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa',NULL,35),(72,'2016-03-07 10:08:13','wasif','atif','asdasdas',NULL,35),(73,'2016-03-07 10:08:53','wasif','atif','asdasd',NULL,35),(74,'2016-03-07 12:22:59','wasif','atif','testing new ',NULL,35),(75,'2016-03-07 12:23:13','wasif','atif','this is a test of white space retention',NULL,35),(76,'2016-03-07 12:23:27','wasif','atif','the test is to be dont\r\n\r\nwith paragraphs\r\n\r\nlike this one\r\n',NULL,35),(77,'2016-03-07 12:23:39','wasif','atif','where we test with test\r\n>\r\n>\r\n>\r\nnew para\r\n',NULL,35),(78,'2016-03-07 12:34:19','wasif','atif','Hello,\r\n          I am writing this as a text to see if i can write anything i want properly now.\r\nThis email is meant to be a reminder to all that we are just plain awesome.\r\n\r\n\r\nP.S. this is just a test.\r\n',NULL,35),(79,'2016-03-08 13:30:55','wasif','atif','lalala','\r\n        <table class=\"user_table\" id=\"print\">\r\n            <tbody><tr id=\"print_date\" class=\"row\" style=\"\">\r\n                <th colspan=\"5\">Tue, Mar 08 2016</th>\r\n            </tr>\r\n                                                                                <tr id=\"category\" style=\"display: none;\"><th colspan=\"5\">Drinks</th></tr>\r\n                    <tr id=\"category_columns\" style=\"display: none;\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr id=\"column_data\" class=\"row\" style=\"display: none;\">\r\n                                        <td>Water</td>\r\n                    <td>bottles</td>\r\n                    <td>24.00</td>\r\n                    <td class=\"quantity_required\">-16.31</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\" style=\"\"><th colspan=\"5\">Frozen</th></tr>\r\n                    <tr id=\"category_columns\" style=\"\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr id=\"column_data\" class=\"row\" style=\"\">\r\n                                        <td>Paratha</td>\r\n                    <td>un-opened pack</td>\r\n                    <td>1.00</td>\r\n                    <td class=\"quantity_required\">17.46</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\" style=\"display: none;\">\r\n                                        <td>tandoori chicken</td>\r\n                    <td>quater leg</td>\r\n                    <td>400.00</td>\r\n                    <td class=\"quantity_required\">-335.38</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\" style=\"display: none;\"><th colspan=\"5\">Paper Products</th></tr>\r\n                    <tr id=\"category_columns\" style=\"display: none;\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr id=\"column_data\" class=\"row\" style=\"display: none;\">\r\n                                        <td>Shawarma</td>\r\n                    <td>kg</td>\r\n                    <td>24.00</td>\r\n                    <td class=\"quantity_required\">-8.62</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                    <tr id=\"category\" style=\"\"><th colspan=\"5\">Walk In Fridge</th></tr>\r\n                    <tr id=\"category_columns\" style=\"\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr id=\"column_data\" class=\"row\">\r\n                                        <td>Shezan mango</td>\r\n                    <td>bottles</td>\r\n                    <td>4.00</td>\r\n                    <td class=\"quantity_required\">3.69</td>\r\n                    <td></td>\r\n                </tr>\r\n                    </tbody></table>\r\n    ',36),(80,'2016-03-23 06:33:39','wasif','atif','hf','\r\n        <table class=\"user_table\" id=\"print\">\r\n            <tbody><tr id=\"print_date\" class=\"row\">\r\n                <th colspan=\"5\">Wed, Mar 23 2016</th>\r\n            </tr>\r\n                                                                            </tbody><tbody class=\"print_tbody\">\r\n                    <tr id=\"category\"><th colspan=\"5\">Drinks</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr id=\"column_data\" class=\"row\">\r\n                                        <td>Water</td>\r\n                    <td>bottles</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                </tbody><tbody class=\"print_tbody\">\r\n                    <tr id=\"category\"><th colspan=\"5\">Frozen</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr id=\"column_data\" class=\"row\">\r\n                                        <td>Paratha</td>\r\n                    <td>un-opened pack</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>tandoori chicken</td>\r\n                    <td>quater leg</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                </tbody><tbody class=\"print_tbody\">\r\n                    <tr id=\"category\"><th colspan=\"5\">Paper Products</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr id=\"column_data\" class=\"row\">\r\n                                        <td>1</td>\r\n                    <td>1</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>10</td>\r\n                    <td>10</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>11</td>\r\n                    <td>11</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>12</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>13</td>\r\n                    <td>13</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>14</td>\r\n                    <td>14</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>15</td>\r\n                    <td>15</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>16</td>\r\n                    <td>16</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>2</td>\r\n                    <td>2</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>22</td>\r\n                    <td>213</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>25</td>\r\n                    <td>11</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>27</td>\r\n                    <td>sd</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>30</td>\r\n                    <td>2</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>31</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>32</td>\r\n                    <td>23</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>33</td>\r\n                    <td>21</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>34</td>\r\n                    <td>1</td>\r\n                    <td>131.00</td>\r\n                    <td class=\"quantity_required\">-131</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>4</td>\r\n                    <td>4</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>6</td>\r\n                    <td>6</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>7</td>\r\n                    <td>7</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>8</td>\r\n                    <td>8</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>Shawarma</td>\r\n                    <td>kg</td>\r\n                    <td>65.00</td>\r\n                    <td class=\"quantity_required\">-42</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>something new</td>\r\n                    <td>sad</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>testing</td>\r\n                    <td>testing</td>\r\n                    <td>55.00</td>\r\n                    <td class=\"quantity_required\">-9.01</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>this</td>\r\n                    <td>ts</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                                                </tbody><tbody class=\"print_tbody\">\r\n                    <tr id=\"category\"><th colspan=\"5\">Walk In Fridge</th></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr>\r\n                                <tr id=\"column_data\" class=\"row\">\r\n                                        <td>19</td>\r\n                    <td>19</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>20</td>\r\n                    <td>20</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>21</td>\r\n                    <td>213</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                                            <tr id=\"column_data\" class=\"row\">\r\n                                        <td>Shezan mango</td>\r\n                    <td>bottles</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n                        </tbody>\r\n        </table>\r\n    ',37),(81,'2016-04-23 16:26:22','wasif','atif','lkj','\r\n        <div class=\"div_left_tabs\">\r\n            <ul class=\"tab_ul\">\r\n                <li class=\"tab_li selected\"><span id=\"day_tab\" onclick=\"getTab(this)\">Full Day</span></li>\r\n            </ul>\r\n        </div>\r\n        <div class=\"div_right_tabs\">\r\n            <ul class=\"tab_ul inline\" id=\"timeslot_ul\">\r\n                                        <div class=\"tab_div\" timeslot-name=\"Morning\">\r\n                    <li class=\"tab_li\"><span onclick=\"getTab(this)\">Morning</span></li>\r\n                </div>\r\n                            <div class=\"tab_div\" timeslot-name=\"Lunch\">\r\n                    <li class=\"tab_li\"><span onclick=\"getTab(this)\">Lunch</span></li>\r\n                </div>\r\n                            <div class=\"tab_div\" timeslot-name=\"Evening\">\r\n                    <li class=\"tab_li\"><span onclick=\"getTab(this)\">Evening</span></li>\r\n                </div>\r\n                        </ul>\r\n        </div>\r\n        <table class=\"table_view\" id=\"print\"><tbody class=\"print_tbody\" id=\"print_tbody\"><tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Drinks</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\">     <td>Water</td>\r\n                    <td>bottles</td>\r\n                    <td>10.00</td>\r\n                    <td class=\"quantity_required\">323.33</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>14</td>\r\n                    <td>14</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>21</td>\r\n                    <td>213</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>107</td>\r\n                    <td>17</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>16</td>\r\n                    <td>16</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>28</td>\r\n                    <td>asd</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>35</td>\r\n                    <td>1</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>34</td>\r\n                    <td>1</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Frozen</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>tandoori chicken</td>\r\n                    <td>quater leg</td>\r\n                    <td>50.00</td>\r\n                    <td class=\"quantity_required\">-20</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\"><tr id=\"column_data\" class=\"row\">     <td>Paratha</td>\r\n                    <td>un-opened pack</td>\r\n                    <td>20.00</td>\r\n                    <td class=\"quantity_required\">13.33</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Grocery</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>23</td>\r\n                    <td>2323</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>22</td>\r\n                    <td>213</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>24</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>25</td>\r\n                    <td>11</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\"><tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Walk In Fridge</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\">     <td>Shezan mango</td>\r\n                    <td>bottles</td>\r\n                    <td>14.00</td>\r\n                    <td class=\"quantity_required\">69.33</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\"><tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Paper Products</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\">     <td>Shawarma</td>\r\n                    <td>kg</td>\r\n                    <td>80.00</td>\r\n                    <td class=\"quantity_required\">86.67</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>7</td>\r\n                    <td>7</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>15</td>\r\n                    <td>15</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>31</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>2</td>\r\n                    <td>2</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>8</td>\r\n                    <td>8</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>26</td>\r\n                    <td>as</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>12</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>13</td>\r\n                    <td>13</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>29</td>\r\n                    <td>23</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>6</td>\r\n                    <td>6</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>32</td>\r\n                    <td>23</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>30</td>\r\n                    <td>2</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: table-row-group;\"><tr id=\"column_data\" class=\"row\" style=\"display: table-row;\">     <td>27</td>\r\n                    <td>sd</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td></td>\r\n                </tr>\r\n            </tbody></table>\r\n    ',38),(82,'2016-04-24 07:59:21','wasif','atif','f','\r\n        <div class=\"div_left_tabs\">\r\n            <ul class=\"tab_ul\">\r\n                <li class=\"tab_li\"><span id=\"day_tab\" onclick=\"getTab(this)\">Full Day</span></li>\r\n            </ul>\r\n        </div>\r\n        <div class=\"div_right_tabs\">\r\n            <ul class=\"tab_ul inline\" id=\"timeslot_ul\">\r\n                                        <div class=\"tab_div\" timeslot-name=\"Morning\">\r\n                    <li class=\"tab_li selected\"><span onclick=\"getTab(this)\">Morning</span></li>\r\n                </div>\r\n                            <div class=\"tab_div\" timeslot-name=\"Lunch\">\r\n                    <li class=\"tab_li\"><span onclick=\"getTab(this)\">Lunch</span></li>\r\n                </div>\r\n                            <div class=\"tab_div\" timeslot-name=\"Evening\">\r\n                    <li class=\"tab_li\"><span onclick=\"getTab(this)\">Evening</span></li>\r\n                </div>\r\n                        </ul>\r\n        </div>\r\n        <table class=\"table_view\" id=\"print\"><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: none;\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Drinks</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>16</td>\r\n                    <td>16</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: none;\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Grocery</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>23</td>\r\n                    <td>2323</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>22</td>\r\n                    <td>213</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody><tbody class=\"print_tbody\" id=\"print_tbody\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Walk In Fridge</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\">     <td>Shezan mango</td>\r\n                    <td>bottles</td>\r\n                    <td>14.00</td>\r\n                    <td class=\"quantity_required\">62.397</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody><tbody class=\"print_tbody\" id=\"print_tbody\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Paper Products</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\">     <td>Shawarma</td>\r\n                    <td>kg</td>\r\n                    <td>80.00</td>\r\n                    <td class=\"quantity_required\">17.334</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>12</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody></table>\r\n    ',39),(83,'2016-04-24 08:02:07','wasif','atif','d','\r\n            <table class=\"table_view\" id=\"print\"><tbody class=\"print_tbody\" id=\"print_tbody\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Drinks</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\">     <td>Water</td>\r\n                    <td>bottles</td>\r\n                    <td>10.00</td>\r\n                    <td class=\"quantity_required\">323.33</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>14</td>\r\n                    <td>14</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>21</td>\r\n                    <td>213</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>107</td>\r\n                    <td>17</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>16</td>\r\n                    <td>16</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>28</td>\r\n                    <td>asd</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>35</td>\r\n                    <td>1</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>34</td>\r\n                    <td>1</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody><tbody class=\"print_tbody\" id=\"print_tbody\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Frozen</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>tandoori chicken</td>\r\n                    <td>quater leg</td>\r\n                    <td>50.00</td>\r\n                    <td class=\"quantity_required\">-20</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\">     <td>Paratha</td>\r\n                    <td>un-opened pack</td>\r\n                    <td>20.00</td>\r\n                    <td class=\"quantity_required\">13.33</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody><tbody class=\"print_tbody\" id=\"print_tbody\" style=\"display: none;\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Grocery</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>23</td>\r\n                    <td>2323</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>22</td>\r\n                    <td>213</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>24</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>25</td>\r\n                    <td>11</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody><tbody class=\"print_tbody\" id=\"print_tbody\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Walk In Fridge</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\">     <td>Shezan mango</td>\r\n                    <td>bottles</td>\r\n                    <td>14.00</td>\r\n                    <td class=\"quantity_required\">69.33</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody><tbody class=\"print_tbody\" id=\"print_tbody\">\r\n                    <tr id=\"category\"><td colspan=\"5\" class=\"none\"><h4>Paper Products</h4></td></tr>\r\n                    <tr id=\"category_columns\">\r\n                        <th>Item</th>\r\n                        <th>Unit</th>\r\n                        <th>Quantity Present</th>\r\n                        <th>Quantity Required</th>\r\n                        <th>Notes</th>\r\n                    </tr><tr id=\"column_data\" class=\"row\">     <td>Shawarma</td>\r\n                    <td>kg</td>\r\n                    <td>80.00</td>\r\n                    <td class=\"quantity_required\">86.67</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>7</td>\r\n                    <td>7</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>15</td>\r\n                    <td>15</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>31</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>2</td>\r\n                    <td>2</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>8</td>\r\n                    <td>8</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>26</td>\r\n                    <td>as</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>12</td>\r\n                    <td>12</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>13</td>\r\n                    <td>13</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>29</td>\r\n                    <td>23</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>6</td>\r\n                    <td>6</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>32</td>\r\n                    <td>23</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>30</td>\r\n                    <td>2</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr><tr id=\"column_data\" class=\"row\" style=\"display: none;\">     <td>27</td>\r\n                    <td>sd</td>\r\n                    <td>-</td>\r\n                    <td class=\"quantity_required\">-</td>\r\n                    <td class=\"align_left\"></td>\r\n                </tr></tbody></table>\r\n        ',40);
/*!40000 ALTER TABLE `Message` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `Recipe`
--

DROP TABLE IF EXISTS `Recipe`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Recipe` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(10) DEFAULT '0',
  `name` varchar(45) NOT NULL,
  `creation_date` date NOT NULL,
  `deletion_date` date DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `Recipe`
--

LOCK TABLES `Recipe` WRITE;
/*!40000 ALTER TABLE `Recipe` DISABLE KEYS */;
INSERT INTO `Recipe` VALUES (1,0,'First recipe','2016-04-24',NULL),(2,0,'Second Recipe','2016-04-24',NULL);
/*!40000 ALTER TABLE `Recipe` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `RecipeItems`
--

DROP TABLE IF EXISTS `RecipeItems`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RecipeItems` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `recipe_id` int(10) NOT NULL,
  `item_id` int(10) NOT NULL,
  `quantity` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `RecipeItems`
--

LOCK TABLES `RecipeItems` WRITE;
/*!40000 ALTER TABLE `RecipeItems` DISABLE KEYS */;
/*!40000 ALTER TABLE `RecipeItems` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TimeSlotItem`
--

DROP TABLE IF EXISTS `TimeSlotItem`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TimeSlotItem` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `item_id` int(11) NOT NULL,
  `timeslot_id` int(11) NOT NULL,
  `factor` decimal(11,2) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TimeSlotItem`
--

LOCK TABLES `TimeSlotItem` WRITE;
/*!40000 ALTER TABLE `TimeSlotItem` DISABLE KEYS */;
INSERT INTO `TimeSlotItem` VALUES (1,39,5,0.03),(2,35,5,NULL),(3,49,5,NULL),(4,50,5,0.25),(5,6,5,0.90),(6,3,5,0.20),(7,5,3,1.00),(8,3,3,0.30),(9,4,3,0.60),(10,59,3,NULL),(11,5,2,0.00),(12,3,2,0.80),(13,38,2,NULL),(14,36,2,NULL);
/*!40000 ALTER TABLE `TimeSlotItem` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `TimeSlots`
--

DROP TABLE IF EXISTS `TimeSlots`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TimeSlots` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(45) NOT NULL,
  `order_id` int(11) DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name_UNIQUE` (`name`),
  UNIQUE KEY `id_UNIQUE` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `TimeSlots`
--

LOCK TABLES `TimeSlots` WRITE;
/*!40000 ALTER TABLE `TimeSlots` DISABLE KEYS */;
INSERT INTO `TimeSlots` VALUES (2,'Lunch',1),(3,'Evening',3),(5,'Morning',0);
/*!40000 ALTER TABLE `TimeSlots` ENABLE KEYS */;
UNLOCK TABLES;

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
INSERT INTO `Variables` VALUES ('BaseSales','3000'),('ExpectedSales','4000');
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

-- Dump completed on 2016-04-25 19:21:57
