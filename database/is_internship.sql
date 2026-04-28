-- MariaDB dump 10.19  Distrib 10.4.28-MariaDB, for osx10.10 (x86_64)
--
-- Host: localhost    Database: is_internship
-- ------------------------------------------------------
-- Server version	10.4.28-MariaDB

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `request`
--

DROP TABLE IF EXISTS `request`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `request` (
  `req_id` int(11) NOT NULL AUTO_INCREMENT,
  `stu_id` varchar(20) DEFAULT NULL,
  `com_name` varchar(200) DEFAULT NULL,
  `req_date` date DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `status_now` int(11) DEFAULT 1,
  PRIMARY KEY (`req_id`),
  KEY `request_student_FK` (`stu_id`),
  CONSTRAINT `request_student_FK` FOREIGN KEY (`stu_id`) REFERENCES `student` (`stu_id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `request`
--

LOCK TABLES `request` WRITE;
/*!40000 ALTER TABLE `request` DISABLE KEYS */;
INSERT INTO `request` VALUES (1,'67101010687','Google','2026-04-09','2026-04-01','2026-04-30',1),(2,'67101010687','Google','2026-04-09','2026-04-01','2026-04-30',1);
/*!40000 ALTER TABLE `request` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `staff`
--

DROP TABLE IF EXISTS `staff`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `staff` (
  `sff_id` varchar(20) NOT NULL,
  `sff_pass` varchar(100) DEFAULT NULL,
  `sff_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`sff_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `staff`
--

LOCK TABLES `staff` WRITE;
/*!40000 ALTER TABLE `staff` DISABLE KEYS */;
INSERT INTO `staff` VALUES ('SFF001','1234','มานะ ขยันยิ่ง'),('SFF002','1234','เวลโดร่า เทมเพส');
/*!40000 ALTER TABLE `staff` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `status`
--

DROP TABLE IF EXISTS `status`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `status` (
  `status_code` int(11) NOT NULL,
  `status_name` varchar(100) DEFAULT NULL,
  `changed_by` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`status_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `status`
--

LOCK TABLES `status` WRITE;
/*!40000 ALTER TABLE `status` DISABLE KEYS */;
INSERT INTO `status` VALUES (1,'รับเรื่องเข้าระบบ','student'),(2,'อาจารย์อนุมัติ','teacher'),(3,'ออกใบส่งตัวแล้ว','staff'),(4,'ฝึกงานเสร็จสิ้น','staff'),(9,'ยกเลิกเอกสาร','teacher/staff');
/*!40000 ALTER TABLE `status` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `student`
--

DROP TABLE IF EXISTS `student`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `student` (
  `stu_id` varchar(20) NOT NULL,
  `stu_pass` varchar(100) DEFAULT NULL,
  `stu_name` varchar(100) DEFAULT NULL,
  `stu_year` varchar(10) DEFAULT NULL,
  `stu_email` varchar(100) DEFAULT NULL,
  `stu_tel` varchar(20) DEFAULT NULL,
  PRIMARY KEY (`stu_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `student`
--

LOCK TABLES `student` WRITE;
/*!40000 ALTER TABLE `student` DISABLE KEYS */;
INSERT INTO `student` VALUES ('67101010660','1234','ช่อผกา มาลัยจรูญ','2','chorpaka.malaicharoon@g.swu.ac.th','0646606540'),('67101010662','1234','ณฤเมธ นฤพัคโภคิน','2','Nalumate.nine@g.swu.ac.th','0946754803'),('67101010677','1234','พรธนาศักดิ์ คำฟัก','2','phontanasak.ohm@gmail.com','0631797973'),('67101010687','1234','รพีภัทร เรืองทอง','2','raphiphat.mit@g.swu.ac.th','0988509224'),('67101010692','1234','อนุสรณ์ เชียงหนุ้น','2','anuson.chaengnoon@g.swu.ac.th','0996165899'),('67101010695','1234','อิสรีย์ วัฒนเกษมวงศ์','2','itsaree.watta@g.swu.ac.th','0933929145');
/*!40000 ALTER TABLE `student` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `teacher`
--

DROP TABLE IF EXISTS `teacher`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `teacher` (
  `tea_id` varchar(20) NOT NULL,
  `tea_pass` varchar(100) DEFAULT NULL,
  `tea_name` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`tea_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `teacher`
--

LOCK TABLES `teacher` WRITE;
/*!40000 ALTER TABLE `teacher` DISABLE KEYS */;
INSERT INTO `teacher` VALUES ('TEA001','1234','ผศ.ดร.วิภา ใจดี'),('TEA002','1234','ริมุรุ เทมเพส');
/*!40000 ALTER TABLE `teacher` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'is_internship'
--
