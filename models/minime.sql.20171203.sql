-- MySQL dump 10.13  Distrib 5.6.37, for Linux (x86_64)
--
-- Host: localhost    Database: ClassScheduler
-- ------------------------------------------------------
-- Server version	5.6.37

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
-- Table structure for table `course`
--

DROP TABLE IF EXISTS `course`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course` (
  `course_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_fk` int(11) NOT NULL,
  `department_fk` int(11) NOT NULL,
  `prof_fk` int(11) NOT NULL,
  `course_num` int(11) DEFAULT NULL,
  `course_desc` varchar(999) DEFAULT NULL,
  `course_homepage` varchar(299) DEFAULT 'https://www.clemson.edu/canvas/',
  `course_days` int(11) NOT NULL,
  PRIMARY KEY (`course_id`),
  KEY `event_fk` (`event_fk`),
  KEY `department_fk` (`department_fk`),
  KEY `prof_fk` (`prof_fk`),
  CONSTRAINT `course_ibfk_1` FOREIGN KEY (`event_fk`) REFERENCES `event` (`event_id`),
  CONSTRAINT `course_ibfk_2` FOREIGN KEY (`department_fk`) REFERENCES `department` (`department_id`),
  CONSTRAINT `course_ibfk_3` FOREIGN KEY (`prof_fk`) REFERENCES `professor` (`prof_id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course`
--

LOCK TABLES `course` WRITE;
/*!40000 ALTER TABLE `course` DISABLE KEYS */;
INSERT INTO `course` VALUES (1,1,30,1,4620,'Database design','https://people.cs.clemson.edu/~srimani/4620_6620_F17/',20),(2,39,30,42,4820,'Web Programming','https://www.clemson.edu/canvas/',42),(3,40,30,28,4820,'Mobile Development','https://www.clemson.edu/canvas/',20),(4,41,30,33,3500,'Computational Theory','https://www.clemson.edu/canvas/',20),(5,42,30,24,4160,'2D Game Development','https://www.clemson.edu/canvas/',20),(6,43,30,31,3620,'DevOps','https://www.clemson.edu/canvas/',42),(7,44,30,3,3120,'Complex Algorithms','https://www.clemson.edu/canvas/',42),(8,49,30,14,4200,'Computer Security Principles','https://www.clemson.edu/canvas/',42);
/*!40000 ALTER TABLE `course` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `course_semester`
--

DROP TABLE IF EXISTS `course_semester`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `course_semester` (
  `cs_id` int(11) NOT NULL AUTO_INCREMENT,
  `cs_cid` int(11) NOT NULL,
  `cs_sid` int(11) NOT NULL,
  PRIMARY KEY (`cs_id`),
  KEY `cs_cid` (`cs_cid`),
  KEY `cs_sid` (`cs_sid`),
  CONSTRAINT `course_semester_ibfk_1` FOREIGN KEY (`cs_cid`) REFERENCES `course` (`course_id`) ON DELETE CASCADE,
  CONSTRAINT `course_semester_ibfk_2` FOREIGN KEY (`cs_sid`) REFERENCES `semester` (`semester_id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `course_semester`
--

LOCK TABLES `course_semester` WRITE;
/*!40000 ALTER TABLE `course_semester` DISABLE KEYS */;
INSERT INTO `course_semester` VALUES (1,1,3);
/*!40000 ALTER TABLE `course_semester` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `department`
--

DROP TABLE IF EXISTS `department`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `department` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_short` varchar(99) NOT NULL,
  `department_long` varchar(299) NOT NULL,
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `department_short` (`department_short`)
) ENGINE=InnoDB AUTO_INCREMENT=142 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `department`
--

LOCK TABLES `department` WRITE;
/*!40000 ALTER TABLE `department` DISABLE KEYS */;
INSERT INTO `department` VALUES (1,'AAH','Art and Architectural Hist'),(2,'ACCT','Accounting'),(3,'AGED','Agricultural Education'),(4,'AGM','Agricultural Mechanization'),(5,'AGRB','Agribusiness'),(6,'AL','Athletic Leadership'),(7,'ANTH','Anthropology'),(8,'APEC','Applied Economics'),(9,'ARCH','Architecture'),(10,'ART','Art'),(11,'AS','Aerospace Studies'),(12,'ASL','American Sign Language'),(13,'ASTR','Astronomy'),(14,'AUD','Audio Technology'),(15,'AUE','Automotive Engineering'),(16,'AVS','Animal and Vet Sciences'),(17,'BCHM','Biochemistry'),(18,'BE','Biosystems Engineering'),(19,'BIOE','Bioengineering'),(20,'BIOL','Biology'),(21,'BMOL','Biomolecular Engineering'),(22,'BUS','Business'),(23,'CE','Civil Engineering'),(24,'CES','Coll of Eng and Science'),(25,'CH','Chemistry'),(26,'CHE','Chemical Engineering'),(27,'CHIN','Chinese'),(28,'COMM','Communication'),(29,'COOP','Cooperative Education'),(30,'CPSC','Computer Science'),(31,'CRP','City and Regional Planning'),(32,'CSM','Construction Sci and Mgt'),(33,'CU','Clemson University'),(34,'CVT','Cardiovascular Technology'),(35,'DANC','Dance'),(36,'DPA','Digital Production Arts'),(37,'ECE','Electrical and Comp Engr'),(38,'ECON','Economics'),(39,'ED','Education'),(40,'EDC','Educational Counseling'),(41,'EDEC','Early Childhood Education'),(42,'EDEL','Elementary Education'),(43,'EDF','Educational Foundations'),(44,'EDHD','Education and Human Devel'),(45,'EDL','Educational Leadership'),(46,'EDLT','Literacy'),(47,'EDML','Middle-Level Education'),(48,'EDSA','Education Student Affairs'),(49,'EDSC','Secondary Education'),(50,'EDSP','Special Education'),(51,'EES','Env Engr and Science'),(52,'ELE','Exec Lead and Entrepren'),(53,'ENGL','English'),(54,'ENGR','Engineering'),(55,'ENR','Environmental and Nat Res'),(56,'ENSP','Environ Sci and Policy'),(57,'ENT','Entomology'),(58,'ENTR','Entrepreneurship'),(59,'ESED','Eng & Science Educ'),(60,'ETOX','Environmental Toxicology'),(61,'FCS','Family and Comm Studies'),(62,'FDSC','Food Science'),(63,'FDTH','Food Technology'),(64,'FIN','Finance'),(65,'FNR','Forestry and Nat Resources'),(66,'FOR','Forestry'),(67,'FR','French'),(68,'GC','Graphic Communications'),(69,'GEN','Genetics'),(70,'GEOG','Geography'),(71,'GEOL','Geology'),(72,'GER','German'),(73,'GRAD','Graduate Studies'),(74,'GW','Great Works'),(75,'HCC','Human-Centered Computing'),(76,'HCG','Health Care Genetics'),(77,'HEHD','Health, Ed and Human Dev'),(78,'HIST','History'),(79,'HLTH','Health'),(80,'HON','Honors'),(81,'HORT','Horticulture'),(82,'HP','Historic Preservation'),(83,'HRD','Human Resource Development'),(84,'HSPV','Historic Preserv (CofC)'),(85,'HUM','Humanities'),(86,'IE','Industrial Engineering'),(87,'INNO','Innovation'),(88,'INT','Career Ctr Internship Prog'),(89,'IS','International Studies'),(90,'ITAL','Italian'),(91,'JAPN','Japanese'),(92,'JUST','Justice Studies'),(93,'LANG','Language'),(94,'LARC','Landscape Architecture'),(95,'LAW','Law'),(96,'LIB','Library'),(97,'LIH','Language and Int Health'),(98,'LIT','Language and Int Trade'),(99,'LS','Leisure Skills'),(100,'MATH','Mathematical Sciences'),(101,'MBA','Business Administration'),(102,'ME','Mechanical Engineering'),(103,'MGT','Management'),(104,'MICR','Microbiology'),(105,'MKT','Marketing'),(106,'ML','Military Leadership'),(107,'MSE','Materials Sci and Eng'),(108,'MUSC','Music'),(109,'NPL','Nonprofit Leadership'),(110,'NURS','Nursing'),(111,'NUTR','Nutrition'),(112,'PA','Performing Arts'),(113,'PADM','Public Administration'),(114,'PAS','Pan African Studies'),(115,'PDBE','Plan Design and Built Env'),(116,'PES','Plant and Env Sci'),(117,'PHIL','Philosophy'),(118,'PHSC','Physical Science'),(119,'PHYS','Physics'),(120,'PKSC','Packaging Science'),(121,'PLPA','Plant Pathology'),(122,'POSC','Political Science'),(123,'POST','Policy Studies'),(124,'PRTM','Parks Rec and Tourism Mgt'),(125,'PSYC','Psychology'),(126,'RCID','Rhet Comm and Info Design'),(127,'RED','Real Estate Development'),(128,'REL','Religion'),(129,'RS','Rural Sociology'),(130,'RUSS','Russian'),(131,'SAP','Study Abroad Program'),(132,'SOC','Sociology'),(133,'SPAN','Spanish'),(134,'STAT','Statistics'),(135,'STS','Sci and Tech in Society'),(136,'THEA','Theatre'),(137,'TSAP','Transfer Study Abroad Prg'),(138,'WCIN','World Cinema'),(139,'WFB','Wildlife and Fish Biology'),(140,'WS','Women\'s Studies'),(141,'YDP','Youth Development Programs');
/*!40000 ALTER TABLE `department` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `event`
--

DROP TABLE IF EXISTS `event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `event` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `event_start` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_end` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `event_name` varchar(299) NOT NULL,
  `event_desc` varchar(999) DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=51 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `event`
--

LOCK TABLES `event` WRITE;
/*!40000 ALTER TABLE `event` DISABLE KEYS */;
INSERT INTO `event` VALUES (1,'2017-11-28 09:30:00','2017-11-28 10:45:00','CPSC 4620','Database design'),(12,'2017-11-20 01:03:00','2017-11-20 01:03:00','helloworld','postedddd'),(24,'2017-11-21 00:10:00','2017-11-21 01:10:00','Study for test','hello'),(26,'2017-11-21 01:49:00','2017-11-21 02:49:00','Cannot be empty','Also Cannot be empty and the end time must be later than the start time'),(30,'2017-11-30 09:41:00','2017-11-30 10:41:00','not blank','not blank2'),(31,'2017-11-30 09:46:00','2017-11-30 10:46:00','test1','test2'),(32,'2017-11-30 09:46:00','2017-11-30 10:46:00','test1','test2'),(33,'2017-11-30 10:49:00','2017-11-30 10:49:00','csteele event1','testing1'),(38,'2017-11-30 09:56:00','2017-11-30 10:56:00','it should work','yay'),(39,'2017-11-28 16:40:00','2017-11-28 17:30:00','CPSC 4820','Web programming'),(40,'2017-11-28 12:30:00','2017-11-28 13:45:00','CPSC 4820','Mobile Development'),(41,'2017-11-28 11:00:00','2017-11-28 12:15:00','CPSC 3500','Computational Theory'),(42,'2017-11-28 14:00:00','2017-11-28 15:15:00','CPSC 4160','2D Game Development'),(43,'2017-11-27 13:25:00','2017-11-27 14:15:00','CPSC 3620','DevOps'),(44,'2017-12-04 14:30:00','2017-12-04 15:20:00','CPSC 3120','Complex Algorithms'),(45,'2017-11-30 15:30:00','2017-11-30 16:10:00','Late lunch','Grab late lunch after classes'),(46,'2017-11-30 16:30:00','2017-11-30 18:30:00','Study','Study for any tests/quiz coming up, work on homework'),(47,'2017-11-28 15:30:00','2017-11-28 16:10:00','Late lunch','Grab late lunch after classes'),(48,'2017-11-28 16:30:00','2017-11-28 18:30:00','Study','Study for any tests/quiz coming up, work on homework'),(49,'2017-11-27 08:00:00','2017-11-27 08:50:00','CPSC 4200','Computer Security Principles'),(50,'2017-11-29 15:00:00','2017-11-29 16:30:00','Study before class','General study sesh before web programming class');
/*!40000 ALTER TABLE `event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `professor`
--

DROP TABLE IF EXISTS `professor`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `professor` (
  `prof_id` int(11) NOT NULL AUTO_INCREMENT,
  `prof_name` varchar(199) NOT NULL,
  `office_loc` varchar(199) DEFAULT NULL,
  `prof_email` varchar(299) DEFAULT NULL,
  PRIMARY KEY (`prof_id`),
  UNIQUE KEY `prof_email` (`prof_email`)
) ENGINE=InnoDB AUTO_INCREMENT=43 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `professor`
--

LOCK TABLES `professor` WRITE;
/*!40000 ALTER TABLE `professor` DISABLE KEYS */;
INSERT INTO `professor` VALUES (1,'Srimani','McAdams 121','srimani@clemson.edu'),(2,'Kelly Caine','McAdams 213','caine@clemson.edu'),(3,'Brian C Dean','McAdams 205','bcdean@clemson.edu'),(4,'David Donar','McAdams 213','ddonar@clemson.edu'),(5,'Andrew T. Duchowski','McAdams 309','andrewd@cs.clemson.edu'),(6,'Yvon Feaster','McAdams 301','yfeaste@clemson.edu'),(7,'Rong Ge','McAdams 209','rge@clemson.edu'),(8,'Robert M. Geist III','Barre 2027','geist@cs.clemson.edu'),(9,'Wayne Goddard','McAdams 311','goddard@cs.clemson.edu'),(10,'Sandra M. Hedetniemi','McAdams 203','shedet@cs.clemson.edu'),(11,'Alexander Herzog','McAdams 227','aherzog@clemson.edu'),(12,'Catherine Hochrine','McAdams 315','chochri@clemson.edu'),(13,'Larry F. Hodges','McAdams 207','lfh@clemson.edu'),(14,'Hongxin Hu','McAdams 217','hongxih@clemson.edu'),(15,'Shuangshuang Jin','Based In Charleston','jin6@clemson.edu'),(16,'Sophie Joerg','McAdams 318','sjoerg@clemson.edu'),(17,'Ioannis Karamouzas','McAdams 306','ioannis@clemson.edu'),(18,'Bart Knijnenburg','McAdams 215','bartk@clemson.edu'),(19,'Eileen Kraemer','McAdams 104','etkraem@clemson.edu'),(20,'Insun Kwon','McAdams 317','insunk@clemson.edu'),(21,'Sal LaMarca','McAdams 306','slamarc@clemson.edu'),(22,'Rose M. Lowe','McAdams 316','rlowe@clemson.edu'),(23,'Feng Luo','McAdams 210','luofeng@cs.clemson.edu'),(24,'Brian Malloy','McAdams 313','malloy@cs.clemson.edu'),(25,'Jim Martin','McAdams 211','jmarty@cs.clemson.edu'),(26,'John McGregor','McAdams 312','johnmc@cs.clemson.edu'),(27,'Nathan McNeese','McAdams 218','mcneese@clemson.edu'),(28,'Roy P. Pargas','Barre B102A','pargas@cs.clemson.edu'),(29,'Eric Patterson','McAdams 307','ekp@clemson.edu'),(30,'Chris Plaue','McAdams 106','cplaue@clemson.edu'),(31,'Kevin Plis','McAdams 214','kplis@clemson.edu'),(32,'Andrew Robb','McAdams 127','arobb@clemson.edu'),(33,'IIya Safro','McAdams 228','isafro@clemson.edu'),(34,'Murali Sitaraman','McAdams 212','murali@cs.clemson.edu'),(35,'Mark Smotherman','McAdams 108','mark@cs.clemson.edu'),(36,'Jacob Sorber','McAdams 225','jsorber@clemson.edu'),(37,'Jerry Tessedorf','McAdams 302','jtessen@cs.clemson.edu'),(38,'Brygg Ullmer','McAdams 208','bullmer@clemson.edu'),(39,'James Wang','McAdams 305','jzwang@cs.clemson.edu'),(40,'J. Mike Westall','McAdams 310','westall@cs.clemson.edu'),(41,'Victor Zordan','McAdams 131','vbz@clemson.edu'),(42,'Craig Baker','Duke Energy Innovation Center S117','cbaker@clemson.edu');
/*!40000 ALTER TABLE `professor` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `semester`
--

DROP TABLE IF EXISTS `semester`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `semester` (
  `semester_id` int(11) NOT NULL AUTO_INCREMENT,
  `semester` varchar(99) NOT NULL,
  PRIMARY KEY (`semester_id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `semester`
--

LOCK TABLES `semester` WRITE;
/*!40000 ALTER TABLE `semester` DISABLE KEYS */;
INSERT INTO `semester` VALUES (1,'Spring 2017'),(2,'Summer 2017'),(3,'Fall 2017'),(4,'Spring 2018');
/*!40000 ALTER TABLE `semester` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_username` varchar(199) NOT NULL,
  `user_password` varchar(299) NOT NULL,
  `user_fname` varchar(99) DEFAULT NULL,
  `user_lname` varchar(99) DEFAULT NULL,
  `user_email` varchar(299) NOT NULL,
  `user_key` varchar(199) DEFAULT NULL,
  `user_keyexpire` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `user_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_token` varchar(199) DEFAULT NULL,
  `user_tokenexpire` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `user_lastlogin` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_verified` tinyint(1) DEFAULT '0',
  `user_key_create` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_username` (`user_username`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'joeycosta3','$2y$10$vtQfW6JfH3JmHaC4LUVA2eQVCDwMV7ThLhVC24He.2BFP84M.a.Va','Joey','Costa','jacsoccerdude3@gmail.com','','2017-12-03 00:52:28','2017-10-09 21:08:57','2017-10-09 21:08:57','865ca823fc833a14e9e8279f924e9e35','2017-12-03 01:52:28','2017-10-09 21:08:57',1,'2017-10-09 17:08:57'),(39,'bsteele','$2y$10$amvtmeml2fTQm93OKhSuUOV05t5hC3Q7p4Ol7Tf5D2t46xSOM.B36','Carl','Steele','carlbretsteele@gmail.com','7577478f77','2017-12-03 00:52:20','2017-10-09 21:11:09','2017-10-09 21:11:09','','2017-12-02 19:52:20','2017-10-09 21:11:09',1,'2017-10-09 17:11:09'),(40,'jacosta','$2y$10$YN8Wknf2H6jR2OPXrd/xy.0HDycdGNvvmV8yuRJgiYagfsLHEyG9e','Joseph','Costa','joey.costa3@gmail.com','51ff6a8abf','2017-12-03 00:52:20','2017-10-09 21:12:23','2017-10-09 21:12:23','','2017-12-02 19:52:20','2017-10-09 21:12:23',1,'2017-10-09 17:12:23'),(44,'csteele','$2y$10$PixjMBepvO.nIRm9tr74leY/l9FrdewV5sKmwE4AuPFjO4b0KlZ/y','Christian','Steele','csteel2@g.clemson.edu','','2017-12-03 00:52:20','2017-11-30 04:36:33','2017-11-30 04:36:33','','2017-12-02 19:52:20','2017-11-30 04:36:33',1,'2017-11-29 23:36:33');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_event`
--

DROP TABLE IF EXISTS `user_event`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_event` (
  `user_event_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_fk` int(11) NOT NULL,
  `event_fk` int(11) NOT NULL,
  `date_created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`user_event_id`),
  KEY `user_fk` (`user_fk`),
  KEY `event_fk` (`event_fk`),
  CONSTRAINT `user_event_ibfk_1` FOREIGN KEY (`user_fk`) REFERENCES `user` (`user_id`),
  CONSTRAINT `user_event_ibfk_2` FOREIGN KEY (`event_fk`) REFERENCES `event` (`event_id`)
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_event`
--

LOCK TABLES `user_event` WRITE;
/*!40000 ALTER TABLE `user_event` DISABLE KEYS */;
INSERT INTO `user_event` VALUES (1,1,1,'2017-10-26 19:35:00'),(7,44,30,'2017-11-30 09:50:00'),(8,44,31,'2017-11-30 09:50:00'),(9,44,32,'2017-11-30 09:50:00'),(10,44,33,'2017-11-30 09:50:00'),(11,44,38,'2017-11-30 14:56:47'),(12,1,39,'2017-12-01 12:56:47'),(13,1,40,'2017-12-01 12:56:47'),(14,1,41,'2017-12-01 12:56:47'),(15,1,42,'2017-12-01 12:56:47'),(16,1,43,'2017-12-01 12:56:47'),(17,1,44,'2017-12-01 12:56:47'),(18,39,44,'2017-12-01 12:56:47'),(19,39,1,'2017-12-01 12:56:47'),(20,39,39,'2017-12-01 12:56:47'),(21,39,42,'2017-12-01 12:56:47'),(22,39,44,'2017-12-01 12:56:47'),(23,39,50,'2017-11-27 08:00:00');
/*!40000 ALTER TABLE `user_event` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user_roles`
--

DROP TABLE IF EXISTS `user_roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user_roles` (
  `useruser_id` int(11) NOT NULL AUTO_INCREMENT,
  `useradmin_fk` int(11) NOT NULL,
  `userview_fk` int(11) NOT NULL,
  `accepted` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`useruser_id`),
  KEY `useradmin_fk` (`useradmin_fk`),
  KEY `userview_fk` (`userview_fk`),
  CONSTRAINT `user_roles_ibfk_1` FOREIGN KEY (`useradmin_fk`) REFERENCES `user` (`user_id`),
  CONSTRAINT `user_roles_ibfk_2` FOREIGN KEY (`userview_fk`) REFERENCES `user` (`user_id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user_roles`
--

LOCK TABLES `user_roles` WRITE;
/*!40000 ALTER TABLE `user_roles` DISABLE KEYS */;
INSERT INTO `user_roles` VALUES (1,1,1,1),(2,39,39,1),(3,1,39,1),(5,39,1,1),(6,40,1,1),(7,44,44,1);
/*!40000 ALTER TABLE `user_roles` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2017-12-03  3:03:37
