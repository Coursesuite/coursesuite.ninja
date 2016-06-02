# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: demo.avide.com.au (MySQL 5.5.49-0+deb7u1)
# Database: csninja_huge
# Generation Time: 2016-06-02 03:30:14 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table app_feature
# ------------------------------------------------------------

DROP TABLE IF EXISTS `app_feature`;

CREATE TABLE `app_feature` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `min_tier_level` int(11) NOT NULL,
  `feature` varchar(100) DEFAULT NULL,
  `details` text,
  `match_label` varchar(50) DEFAULT NULL,
  `mismatch_label` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `app_feature` WRITE;
/*!40000 ALTER TABLE `app_feature` DISABLE KEYS */;

INSERT INTO `app_feature` (`id`, `app_id`, `min_tier_level`, `feature`, `details`, `match_label`, `mismatch_label`)
VALUES
	(1,1,1,'Branding','Branded courses contain the CourseSuite logo and copyright information. You are able to replace this with your own information in high tiers (or remove it).','Removable','Fixed'),
	(2,1,2,'Layouts','You are able to choose from a few different layouts such as side menu, slideshow, dropdown.','Customisable','Fixed'),
	(3,1,1,'Dropbox Integrated','Whether you are able to load and save resources from Dropbox or other cloud-based storage services','Load & Save','No'),
	(4,1,-1,'Scorm','You are able to choose your SCORM container for the final course - 1.2, 2004 or IMS Content Package (no scorm)',' Supported','Missing'),
	(5,1,2,'API access','API for developer access','Single Sign On','No'),
	(7,2,1,'Branding','Branded courses contain the CourseSuite logo over the top of the video (watermark). Removable in high tiers.','Removable','Fixed'),
	(8,2,1,'Dropbox Integrated','Whether you are able to load and save resources from Dropbox or other cloud-based storage services','Yes','No'),
	(9,2,-1,'Scorm','SCORM 1.2 is supported as the container format for all video packages.','Supported','Missing'),
	(10,2,2,'API access','API access for developers','Single Sign On','No'),
	(11,2,2,'Embedded video','Allow the upload and conversion of video formats to HTML5-compatible mp4, rather than relying on YouTube, BrightCove or Vimeo.','HTML5 video types','No'),
	(12,3,-1,'Scorm','You are able o choose whether your ourses are published with Scorm 1.2 or Scorm 2004','Yes','No'),
	(13,3,2,'Course Sharing','Invite other CourseSuite users to collaboratively edit your course.','Yes','No'),
	(14,3,1,'Branding','Branded courses contain the CourseSuite logo and copyright information. You are able to replace this with your own information in high tiers (or remove it).','Removable','Fixed'),
	(15,3,2,'Templates','Change the underlying visual template to manage the look and feel of your courses','Editable','Fixed');

/*!40000 ALTER TABLE `app_feature` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table app_tiers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `app_tiers`;

CREATE TABLE `app_tiers` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(11) NOT NULL,
  `tier_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `app_tiers` WRITE;
/*!40000 ALTER TABLE `app_tiers` DISABLE KEYS */;

INSERT INTO `app_tiers` (`id`, `app_id`, `tier_id`)
VALUES
	(1,1,1),
	(2,1,2),
	(3,1,3),
	(4,2,1),
	(5,2,2),
	(6,2,3),
	(7,3,2),
	(8,3,3),
	(9,4,4),
	(10,4,5),
	(11,4,6);

/*!40000 ALTER TABLE `app_tiers` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table applog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `applog`;

CREATE TABLE `applog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `method_name` varchar(50) DEFAULT '',
  `digest_user` varchar(20) DEFAULT '',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `message` varchar(255) DEFAULT '',
  `param0` text,
  `param1` text,
  `param2` text,
  `param3` text,
  `param4` text,
  `param5` text,
  `param6` text,
  `param7` text,
  `param8` text,
  `param9` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `applog` WRITE;
/*!40000 ALTER TABLE `applog` DISABLE KEYS */;

INSERT INTO `applog` (`id`, `method_name`, `digest_user`, `added`, `message`, `param0`, `param1`, `param2`, `param3`, `param4`, `param5`, `param6`, `param7`, `param8`, `param9`)
VALUES
	(1,'ApiController::subscription','fastspring','2016-05-26 15:05:07','','a:1:{i:0;s:9:\"activated\";}','Array\n(\n    [referenceId] => COU160526-1894-10824S\n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160526-1894-62835S\n    [accountUrl] => \n    [productName] => copper\n    [testmode] => true\n    [referrer] => MWUyNTYyZTQ0YTg0N2Q3YjYzMzAzOTAzOTg3OWM4OGY2YTUyMjZhYzhlMzQxNTg5MzBiYWFjY2U3ZjQ2NjVmZWZG8P_vih6Gqj4x1XDAL9_GUJBS9Bzm4a8g0qWOqmeG\n    [statusReason] => \n    [subscriptionEndDate] => \n    [email] => craig@coursesuite.com.au\n    [status] => active\n    [security_data] => 1464239105805COU160526-1894-10824S\n    [security_hash] => af4f05c378be9dbcc2e3ba6ff8a68463\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(2,'ApiController::verifyToken','tokenuser','2016-05-26 18:48:41','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(3,'ApiController::verifyToken','tokenuser','2016-05-26 18:48:41','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(4,'ApiController::verifyToken','tokenuser','2016-05-26 18:48:41','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(5,'ApiController::verifyToken','tokenuser','2016-05-26 18:48:41','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(6,'ApiController::verifyToken','tokenuser','2016-05-26 18:49:02','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(7,'ApiController::verifyToken','tokenuser','2016-05-26 18:49:02','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(8,'ApiController::verifyToken','tokenuser','2016-05-26 18:49:02','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(9,'ApiController::verifyToken','tokenuser','2016-05-26 18:49:02','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(10,'ApiController::verifyToken','tokenuser','2016-05-26 18:50:15','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(11,'ApiController::verifyToken','tokenuser','2016-05-26 18:50:15','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(12,'ApiController::verifyToken','tokenuser','2016-05-26 18:50:15','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(13,'ApiController::verifyToken','tokenuser','2016-05-26 18:50:15','','docninja','36616237656361633135376434383737633537396565663561366130353434336664326132326564636134616561343534343066346631326162353965656331e86429d80112936c39af1b8cd686bd58fcf2ef3aa524f5599512541bba90e1dabf77077a8cbb70d6343af5862e9a645e','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(14,'ApiController::verifyToken','tokenuser','2016-05-26 19:47:11','','docninja','64396134613939383763626232323434356537333166353932663135343133653339633035613539643462363030663031636266383832613962343761343434b241c7daec81303372e9c3be059cc54681418bb5d25667d75a3e465944e20618925773a4fdb14a20de401bdd177987de','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(15,'ApiController::verifyToken','tokenuser','2016-05-26 19:47:11','','docninja','64396134613939383763626232323434356537333166353932663135343133653339633035613539643462363030663031636266383832613962343761343434b241c7daec81303372e9c3be059cc54681418bb5d25667d75a3e465944e20618925773a4fdb14a20de401bdd177987de','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(16,'ApiController::verifyToken','tokenuser','2016-05-26 19:47:11','','docninja','64396134613939383763626232323434356537333166353932663135343133653339633035613539643462363030663031636266383832613962343761343434b241c7daec81303372e9c3be059cc54681418bb5d25667d75a3e465944e20618925773a4fdb14a20de401bdd177987de','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(17,'ApiController::verifyToken','tokenuser','2016-05-26 19:47:11','','docninja','64396134613939383763626232323434356537333166353932663135343133653339633035613539643462363030663031636266383832613962343761343434b241c7daec81303372e9c3be059cc54681418bb5d25667d75a3e465944e20618925773a4fdb14a20de401bdd177987de','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(18,'ApiController::verifyToken','tokenuser','2016-05-27 10:06:21','','docninja','363339613462376666353361663964306664316165316630376634386161623361333461313938373430343237633436613434313133373361623466353234644ee3c827c1b7ec233380a04e97d6ad33179bfdecd2a642f93c5759c056bbeda757093b2758ca2b1c4ef5930886fb4dbf','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(19,'ApiController::verifyToken','tokenuser','2016-05-27 10:06:22','','docninja','363339613462376666353361663964306664316165316630376634386161623361333461313938373430343237633436613434313133373361623466353234644ee3c827c1b7ec233380a04e97d6ad33179bfdecd2a642f93c5759c056bbeda757093b2758ca2b1c4ef5930886fb4dbf','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(20,'ApiController::verifyToken','tokenuser','2016-05-27 10:06:22','','docninja','363339613462376666353361663964306664316165316630376634386161623361333461313938373430343237633436613434313133373361623466353234644ee3c827c1b7ec233380a04e97d6ad33179bfdecd2a642f93c5759c056bbeda757093b2758ca2b1c4ef5930886fb4dbf','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(21,'ApiController::verifyToken','tokenuser','2016-05-27 10:06:22','','docninja','363339613462376666353361663964306664316165316630376634386161623361333461313938373430343237633436613434313133373361623466353234644ee3c827c1b7ec233380a04e97d6ad33179bfdecd2a642f93c5759c056bbeda757093b2758ca2b1c4ef5930886fb4dbf','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(22,'ApiController::verifyToken','tokenuser','2016-05-27 10:06:47','','docninja','31346232313639623638666233343039383038343639333261326365646364376338323165323061656537333265373965303138303230383961646431353732f73d6cc696d2dd113b35a7f59a9f5fcd63509409ac34e317960774d8fa167637209a49909857be037b9a571a381ac31c','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(23,'ApiController::verifyToken','tokenuser','2016-05-27 10:06:47','','docninja','31346232313639623638666233343039383038343639333261326365646364376338323165323061656537333265373965303138303230383961646431353732f73d6cc696d2dd113b35a7f59a9f5fcd63509409ac34e317960774d8fa167637209a49909857be037b9a571a381ac31c','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(24,'ApiController::verifyToken','tokenuser','2016-05-27 10:06:47','','docninja','31346232313639623638666233343039383038343639333261326365646364376338323165323061656537333265373965303138303230383961646431353732f73d6cc696d2dd113b35a7f59a9f5fcd63509409ac34e317960774d8fa167637209a49909857be037b9a571a381ac31c','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(25,'ApiController::verifyToken','tokenuser','2016-05-27 10:06:47','','docninja','31346232313639623638666233343039383038343639333261326365646364376338323165323061656537333265373965303138303230383961646431353732f73d6cc696d2dd113b35a7f59a9f5fcd63509409ac34e317960774d8fa167637209a49909857be037b9a571a381ac31c','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(26,'ReCaptcha','','2016-05-27 14:14:28','','03AHJ_VuuhwlFUFLNVpg2Od-5grwX4eZLQGGND3TmkHUaA5bAz_Ewd9RAkzXpdovJXt3JDFb-6dpMbUNratuyGz2zQqkKoArsnxki6M_vFA272MzB7KFGBEYFIPj7BASlKMQS4cS7_vhDdUB60p-yjuUssMLcAUL3AZsLfvwY6DI9E6bQUqZTLKZNbLeOhtIi23r734nHuL4LmPd6TtqaOGV9jAFVD9_ITa7NNOQSHdfu3qTveev0LeD4zAD_M0ZVkFsIpp4e4wGu5Q2nAOGd28dGM9LRwVfrkVy0dQ_DxT07IHVDklFLbR2pKCvZI5co2LZkCxQ3hvu05V_YC3PKdWrH5HltIrJknWGnSIU73neppIx0EAjVrkGbM00GY-Ih7l9OI4eK6s4sBmhDGx4KAEOxxlzr-bJKOueTIng-HYB6MIyUj91cDG9c15xa9F52mu_m-y_Wo3fAe57Ymic0jd660TP4izpYm6VrYkJ1TKFydL-9iXHyN960XxPQLkr_xIvloNUrgO_bEkFHSkVJL0SNio4p4DpwxzqQJRt09rvDwQv1Fm2R-sifgtdf5dav1cEthbJnyHxSTCayYGs-cIYQmxDwQlp9KBcXg2sGypkls7Z147xAbTvQjX4nQnEwc2jpc9KrY9X330TikJvOJpwKadpKkMzsxcBKZ0XPQvRRupMMajX9sD1GTlCHala0K0fVTXpsgtFKGuSChBDpUE_yPex3wOca50cUtXKO7YiBWKoWS_wBdAodwR2hpVwjR76jIeWL5LR9GUZhDCXqOHlHVGq1hZOJE7Ep0M5ju_3HJndtQXDows7VFmE4LXc4F4yNeCjfjjvqOvX8gF2FKKxPHeAhmMEWaCR1OFsOR_w8bKShSJHnutMuLA2BqiWnjRz206_aJuqOi1HAVAmS_jocUbE26_orfDPQGc-L6o2yOgtLzo1lPrSxGY2-aqc2yiXytWRpuq3AX','180.181.86.137',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(27,'ReCaptcha','','2016-05-27 14:14:49','','03AHJ_VutrBPh30zt3TRI9oNmU3gnp3EQk11LgRt36Cof9vMBSoKBCVT8d-B1p2w-1pw9Fvyeb1-29YhcvohEpYnkLXwF9RrpbRH8EEyilCqWQXw3WTsIDb14N7-roOBkitEdt6mmy7weAOBip6CqTCvCVLFD9LojtzJ8MBMES1hyPHDX83mBKS4RcEXzBZWJNl2lQjYNcJoqXKZKbvcoxB_zdO8K4XFGHmiaczi19czi4hpOfp3m-QnNIK3fFlfRaQCE3EsjCVmF3gqbhr-WCk2svyuuN5NJFwTSmd0yFiMeFRv8xqI9mxZT0j7HRwhc_mGk56j7wB8IfLaXwviclVDTuNIHqxS7Hf3XE4vW2VKCpAjLGsacGvtR_e6o-0vgM7PBZal_1QqG0abT9vpL3bFabngRfjZ5foP-J6Yr2jOfalYgo3zwnvL2Ci1upIyOgnF2EVnfhpBOpTcrbIqBWj0qQzc9P9yVP6ayLgwn9HhKQE6hbJmeCPFSljGS6WXz9YURjON5wqRo7qIupLToJzbSOj8tuleGkENljXNdeS_40aBKanEU9MR-WI9fXNmbaUi0yfp54MQ5MzLJezm4rR9YJjLDBKsi2ha8AE1iBfdhmPfsBZNrO1hcVA2Wy2IVUMUJTnqWYC0OyhIrQdCiSOFB-zHO05GBUmC7QQd2n0kr4i20MVhsl_lsAqagYaDg-m3RWNPEoF-1zDcaETfzu-FJzIcbEbdDgf9vHoay3HvFf49sIws6OqtoW6q6VGfOjZPd7IHx-mUz7W_mshbhr5KB9SURokbkm2yPfaqcn6fweKlntif7zXy5u1_l4TXnSCJl1Yl8gxz8FAVIPzYbLlyqC_zTFEjnjWmilkOpckeCuOQAe2ggWtmT8SOgwOXdpoX1WOOE1ZSnXteMTtPFcYEk3NvZAXnOFlWqunPxu_hM5WFx4hXnIeP8','180.181.86.137',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(28,'ApiController::verifyToken','tokenuser','2016-05-27 14:53:18','','docninja','323739326365656434313962306331623664303735656430356230353661643736316433653134343762646532666565333261636562313963373464366162359dfd1b51671bfd62c222c2170bce9fc930b59166ea6514c05402b3baf06849074c351b28ed04d492455e3920264135ad','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(29,'ApiController::verifyToken','tokenuser','2016-05-27 14:53:18','','docninja','323739326365656434313962306331623664303735656430356230353661643736316433653134343762646532666565333261636562313963373464366162359dfd1b51671bfd62c222c2170bce9fc930b59166ea6514c05402b3baf06849074c351b28ed04d492455e3920264135ad','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(30,'ApiController::verifyToken','tokenuser','2016-05-27 14:53:18','','docninja','323739326365656434313962306331623664303735656430356230353661643736316433653134343762646532666565333261636562313963373464366162359dfd1b51671bfd62c222c2170bce9fc930b59166ea6514c05402b3baf06849074c351b28ed04d492455e3920264135ad','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(31,'ApiController::verifyToken','tokenuser','2016-05-27 14:53:18','','docninja','323739326365656434313962306331623664303735656430356230353661643736316433653134343762646532666565333261636562313963373464366162359dfd1b51671bfd62c222c2170bce9fc930b59166ea6514c05402b3baf06849074c351b28ed04d492455e3920264135ad','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(32,'ReCaptcha','','2016-05-27 15:17:10','','03AHJ_VuuSCAnnzN-6BV5ETR-nX_f95UD2dyU49Bg3-LnpYBS0-vCp4adIbt9fpwrHOxo2MSTB-glVxP47CrHDuaSQ63M3Ok86Hgj8v_6KIGSfmGZJVKBB8G-NOmnqpfys3X3a4v4UIm_fS6GhueA2OhvRXQIUIq6JB12b4D0Jx5Q4v55zNDepnXd4i21GifM3SaQdjyZsN70L7g4OzvP6qzhTkrpHftlS0sIO534cUSmbnYchmDytM_DuWvrTO0aEb5ndzL4iJRpdILNrYL7wLkiJ9tSu_BxZNXNixi86utymqz5P7wS8l4h8DhUHICT3J6gdOHjVGTYd6OtNbIwbGDvm_VQrL2PWmj2lqaBneuSFennvUjTKvagPpVa_OgWKTd0bRFn2q1p2fGi11EJlA5OieavMmhoadQHnivY6tAVzkqOQN5t6iod4sAdBfQDTPFBs9ChDQYMhyOqoLx6LL5N5PPRtPFmiNZGMKPqlL7X4Z5WOMOFOd9tZi8drA7TCaL1z3wVIVRV_Ib5zEZy-AzckvHlqLeIaTRafjh_XGoSmb6Vm_FET37wKU0XVcjsOs4qiXrDXB9EO-Keo4e3IFFJd2GkrzmGVKUw32Xa8E_ZLSRg9vmlZ7XUnYd1DmXHBgoNdju5tii-H-4GUmp05lmawSgMpYMTr4z9McuFpHQqg1Uvzo7fWEoVQetTLyTVQgciqDfAh9XDRU0ituEO87RFPBC7uqBWtVPf8m7yCw_w45wQwMcENBSaMpyrhRPQlQBDxqtWSs9sj2vMzhwNoaUNizfsad8TCuDGsqfyo1pvDUZfHI-tPsymmADm0qQiDLjfNPePeuKoEUrCuOYZqh4J-s5aIFOcMD-yewWptNTZmzUX6ku7KYRHj3h_8RelW175AGCanl2TrpBX9EYq5IQHB2HrUKK7tz9TJeZel0PVoqCuaPdCeFQTt3CsNMPSUb7zrvESBzsGWACjN7YcnktW4KtCeLqHDQQ','180.181.86.137',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(33,'sending mail','','2016-05-27 15:17:10','','PHPMailer Object\n(\n    [Version] => 5.2.14\n    [Priority] => \n    [CharSet] => iso-8859-1\n    [ContentType] => text/plain\n    [Encoding] => 8bit\n    [ErrorInfo] => \n    [From] => no-reply@coursesuite.ninja\n    [FromName] => CourseSuite\n    [Sender] => \n    [ReturnPath] => \n    [Subject] => Account activation for CourseSuite\n    [Body] => Please click on this link to activate your account: http://my.coursesuite.ninja/register/verify/13/f3eec44641d1dca7783621b576b9a86d43bd2c2a\n    [AltBody] => \n    [Ical] => \n    [MIMEBody:protected] => \n    [MIMEHeader:protected] => \n    [mailHeader:protected] => \n    [WordWrap] => 0\n    [Mailer] => mail\n    [Sendmail] => /usr/sbin/sendmail\n    [UseSendmailOptions] => 1\n    [PluginDir] => \n    [ConfirmReadingTo] => \n    [Hostname] => \n    [MessageID] => \n    [MessageDate] => \n    [Host] => localhost\n    [Port] => 25\n    [Helo] => \n    [SMTPSecure] => \n    [SMTPAutoTLS] => 1\n    [SMTPAuth] => \n    [SMTPOptions] => Array\n        (\n        )\n\n    [Username] => \n    [Password] => \n    [AuthType] => \n    [Realm] => \n    [Workstation] => \n    [Timeout] => 300\n    [SMTPDebug] => 0\n    [Debugoutput] => echo\n    [SMTPKeepAlive] => \n    [SingleTo] => \n    [SingleToArray] => Array\n        (\n        )\n\n    [do_verp] => \n    [AllowEmpty] => \n    [LE] => \n\n    [DKIM_selector] => \n    [DKIM_identity] => \n    [DKIM_passphrase] => \n    [DKIM_domain] => \n    [DKIM_private] => \n    [action_function] => \n    [XMailer] => \n    [smtp:protected] => \n    [to:protected] => Array\n        (\n            [0] => Array\n                (\n                    [0] => craig1@coursesuite.com.au\n                    [1] => \n                )\n\n        )\n\n    [cc:protected] => Array\n        (\n        )\n\n    [bcc:protected] => Array\n        (\n        )\n\n    [ReplyTo:protected] => Array\n        (\n        )\n\n    [all_recipients:protected] => Array\n        (\n            [craig1@coursesuite.com.au] => 1\n        )\n\n    [RecipientsQueue:protected] => Array\n        (\n        )\n\n    [ReplyToQueue:protected] => Array\n        (\n        )\n\n    [attachment:protected] => Array\n        (\n        )\n\n    [CustomHeader:protected] => Array\n        (\n        )\n\n    [lastMessageID:protected] => \n    [message_type:protected] => \n    [boundary:protected] => Array\n        (\n        )\n\n    [language:protected] => Array\n        (\n        )\n\n    [error_count:protected] => 0\n    [sign_cert_file:protected] => \n    [sign_key_file:protected] => \n    [sign_extracerts_file:protected] => \n    [sign_key_pass:protected] => \n    [exceptions:protected] => \n    [uniqueid:protected] => \n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(34,'ReCaptcha','','2016-05-27 15:23:42','','03AHJ_VutnLzs7-vlW_9-2C9cUPpXwsKnbub3f4tiAQ2P-ko5osv_R6uYso_0If4LbzdnlIWa5jMTnwhhO6UwHerNIMQya6tQ5EFmJARBiJPlZIaIh7xlKdhoVMF9HGs_hjIWJoiWn-BHShaMOrGRtX951Dny907Mlcv5Jba4ZXG8kj4a-B4-OlY1gQxakiY1n2fZef0LXB0wOvdDQd9BrjyCYS5rtapZUrIrsIWTWk13gTiFMU76XCb6bcH-n0l_aDDjFS48J7vURmS379K4GQHfib0Ry9H6-Wl197Kp5LTBNxcfdlcMckqP2MGAXsj6AkrnLgiufHo39dBoLcFwQbDMDLeFGwzNgYcI_bmict379wnSAiuUO4fC5lM5PYCworqVt3ZqqJ7b5Kw9kGlqnZV8W7gPF1szsKuRIoc3Ud0NhP4uJbjMqEnz3sDI9s0IsUpqdMxoWd9ULM0pxjUDJg6EV7QBsZg_sHYzpMAeuG6Yzf3HvTD0SYrk5ZsRWclmyxRaRu7ghdieKLZ0u1GbJ5ZJZzge4Hfh6HbQgI8htoMlFhfyzv4bmFfiXmI7h3C-L0TbMxSqtzBBOavmwdcByezxr58tm5oRDWcD9GzIhL8RrGkRxuViUA0Ufx9OE92OMEsZ-rVcoB7a-VwQJnC6-4MZoxTXiuH8VGwy0Ym2ER7LmWK6pYQ9YEEMKlRJt6mqafhjonUz4-nhWeeQUEb2y6GK6mEg4l9q62dAXsLxPyUTdAt1Ba_1xGD-I3O8CcM8QyZqCEXdwLhOl9eCC6-M6pOtpFdN0DcU0f9jCrmVIoAGKBwBo10Y4dndmW-6QlgoiruSvVzWshP3B_khO_zUxWMbtKRN_3_oe_lBnFCyPdKXxxZC6Tj92kxVn9d5Cy-W9PmmDznzopMrFLzfQWKAAahrTp0xWZwE17pmzSsc78yZ9sgFB3F1tySnb2C8rerrQzwiAvjp7LimQOLG38f_9zTmLFdS1iAsJOblCmzPC3mvXtsMiF2n0UqVbFtYFbUb_TTVWC9RPDnd_','180.181.86.137',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(35,'sending mail','','2016-05-27 15:23:42','','PHPMailer Object\n(\n    [Version] => 5.2.14\n    [Priority] => \n    [CharSet] => iso-8859-1\n    [ContentType] => text/plain\n    [Encoding] => 8bit\n    [ErrorInfo] => \n    [From] => no-reply@coursesuite.ninja\n    [FromName] => My Coursesuite\n    [Sender] => \n    [ReturnPath] => \n    [Subject] => Password reset for CourseSuite\n    [Body] => Please click on this link to reset your password:  http://my.coursesuite.ninja/login/verifypasswordreset/Craig1/718195400971de2c7a486f234a248228c78c8020\n    [AltBody] => \n    [Ical] => \n    [MIMEBody:protected] => \n    [MIMEHeader:protected] => \n    [mailHeader:protected] => \n    [WordWrap] => 0\n    [Mailer] => mail\n    [Sendmail] => /usr/sbin/sendmail\n    [UseSendmailOptions] => 1\n    [PluginDir] => \n    [ConfirmReadingTo] => \n    [Hostname] => \n    [MessageID] => \n    [MessageDate] => \n    [Host] => localhost\n    [Port] => 25\n    [Helo] => \n    [SMTPSecure] => \n    [SMTPAutoTLS] => 1\n    [SMTPAuth] => \n    [SMTPOptions] => Array\n        (\n        )\n\n    [Username] => \n    [Password] => \n    [AuthType] => \n    [Realm] => \n    [Workstation] => \n    [Timeout] => 300\n    [SMTPDebug] => 0\n    [Debugoutput] => echo\n    [SMTPKeepAlive] => \n    [SingleTo] => \n    [SingleToArray] => Array\n        (\n        )\n\n    [do_verp] => \n    [AllowEmpty] => \n    [LE] => \n\n    [DKIM_selector] => \n    [DKIM_identity] => \n    [DKIM_passphrase] => \n    [DKIM_domain] => \n    [DKIM_private] => \n    [action_function] => \n    [XMailer] => \n    [smtp:protected] => \n    [to:protected] => Array\n        (\n            [0] => Array\n                (\n                    [0] => craig1@coursesuite.com.au\n                    [1] => \n                )\n\n        )\n\n    [cc:protected] => Array\n        (\n        )\n\n    [bcc:protected] => Array\n        (\n        )\n\n    [ReplyTo:protected] => Array\n        (\n        )\n\n    [all_recipients:protected] => Array\n        (\n            [craig1@coursesuite.com.au] => 1\n        )\n\n    [RecipientsQueue:protected] => Array\n        (\n        )\n\n    [ReplyToQueue:protected] => Array\n        (\n        )\n\n    [attachment:protected] => Array\n        (\n        )\n\n    [CustomHeader:protected] => Array\n        (\n        )\n\n    [lastMessageID:protected] => \n    [message_type:protected] => \n    [boundary:protected] => Array\n        (\n        )\n\n    [language:protected] => Array\n        (\n        )\n\n    [error_count:protected] => 0\n    [sign_cert_file:protected] => \n    [sign_key_file:protected] => \n    [sign_extracerts_file:protected] => \n    [sign_key_pass:protected] => \n    [exceptions:protected] => \n    [uniqueid:protected] => \n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(36,'ApiController::verifyToken','tokenuser','2016-05-30 12:32:11','','docninja','30363165316534336362663237376632373036333137663236353764626137643734396534626538306137316536363932643439383662356135323435396462aad66c36927d537c876332b673caf90f29b9294c7f6d8a7420f38f794ea0485152c6a226e3286553ca1bc861c9fa2b7d','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(37,'ApiController::verifyToken','tokenuser','2016-05-30 12:32:11','','docninja','30363165316534336362663237376632373036333137663236353764626137643734396534626538306137316536363932643439383662356135323435396462aad66c36927d537c876332b673caf90f29b9294c7f6d8a7420f38f794ea0485152c6a226e3286553ca1bc861c9fa2b7d','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(38,'ApiController::verifyToken','tokenuser','2016-05-30 12:32:11','','docninja','30363165316534336362663237376632373036333137663236353764626137643734396534626538306137316536363932643439383662356135323435396462aad66c36927d537c876332b673caf90f29b9294c7f6d8a7420f38f794ea0485152c6a226e3286553ca1bc861c9fa2b7d','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(39,'ApiController::verifyToken','tokenuser','2016-05-30 12:32:11','','docninja','30363165316534336362663237376632373036333137663236353764626137643734396534626538306137316536363932643439383662356135323435396462aad66c36927d537c876332b673caf90f29b9294c7f6d8a7420f38f794ea0485152c6a226e3286553ca1bc861c9fa2b7d','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(40,'ApiController::verifyToken','tokenuser','2016-05-30 12:47:29','','docninja','64376263343237643531386665373235623966393432343933323337353133303237316531333835663561616166316438356239643338626530653262313661daa3b0b57377e831fc1fa97a9fe9d0b19011b29e717fb0a359dfdf25f7aaf5d2ef1053d4a44989744ba21729bbbfdf21','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(41,'ApiController::verifyToken','tokenuser','2016-05-30 12:47:29','','docninja','64376263343237643531386665373235623966393432343933323337353133303237316531333835663561616166316438356239643338626530653262313661daa3b0b57377e831fc1fa97a9fe9d0b19011b29e717fb0a359dfdf25f7aaf5d2ef1053d4a44989744ba21729bbbfdf21','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(42,'ApiController::verifyToken','tokenuser','2016-05-30 12:47:29','','docninja','64376263343237643531386665373235623966393432343933323337353133303237316531333835663561616166316438356239643338626530653262313661daa3b0b57377e831fc1fa97a9fe9d0b19011b29e717fb0a359dfdf25f7aaf5d2ef1053d4a44989744ba21729bbbfdf21','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(43,'ApiController::verifyToken','tokenuser','2016-05-30 12:47:29','','docninja','64376263343237643531386665373235623966393432343933323337353133303237316531333835663561616166316438356239643338626530653262313661daa3b0b57377e831fc1fa97a9fe9d0b19011b29e717fb0a359dfdf25f7aaf5d2ef1053d4a44989744ba21729bbbfdf21','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(44,'ApiController::verifyToken','tokenuser','2016-05-30 12:47:52','','docninja','6130363862363231663264333264313738613937616166396537336234666637386334323233636462316337323234363863316435363139343766343861346252f3e0a00116ff944a3e53f17705efbb35a27cccd1a3f35557245f8aa45ce0d2393756374cebded6c44f0ef93a348f9f','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(45,'ApiController::verifyToken','tokenuser','2016-05-30 12:47:52','','docninja','6130363862363231663264333264313738613937616166396537336234666637386334323233636462316337323234363863316435363139343766343861346252f3e0a00116ff944a3e53f17705efbb35a27cccd1a3f35557245f8aa45ce0d2393756374cebded6c44f0ef93a348f9f','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(46,'ApiController::verifyToken','tokenuser','2016-05-30 12:47:52','','docninja','6130363862363231663264333264313738613937616166396537336234666637386334323233636462316337323234363863316435363139343766343861346252f3e0a00116ff944a3e53f17705efbb35a27cccd1a3f35557245f8aa45ce0d2393756374cebded6c44f0ef93a348f9f','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(47,'ApiController::verifyToken','tokenuser','2016-05-30 12:47:52','','docninja','6130363862363231663264333264313738613937616166396537336234666637386334323233636462316337323234363863316435363139343766343861346252f3e0a00116ff944a3e53f17705efbb35a27cccd1a3f35557245f8aa45ce0d2393756374cebded6c44f0ef93a348f9f','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(48,'ReCaptcha','','2016-06-02 13:28:37','','03AHJ_VuuqCBip9rykzAja5zQn7_hvn1b81kveqshLR1mQUWGQ469ioTMU_uijG_7jMcG1bnQ_k2lECxKVRRYnt_lZF3nXyQhU8xDw3IE6A4LXskgEFLf334O9ee8Mv6bE3SWgxzrrU98ReU7KLqjaTMO8PiSThZkZeB556f1Of5Y426QBQQPbO5ivFbF52k-xoxcqJ2dFKxlk-fzeK4VFtgbxXVHMcP7BhaV3nDojwcmuCYCzcnT5Z5-KIg355aKW4I-bY56n4MeMcoUwmn1u9dAtJBjuxM-miXy7lVKzmFdW5zMUiRoST1zli6d8-09Kbxnw0jvW3oywGNe1Yrxqcs5GtE2ZTKeqXmDqYEIHNpwTwFLcqI0QGC8uPdbgMXkH9JPaLxcXGzVXHuWUMJqYJMFVe5WCYVk9NrfubTJzBNO6X9Uxm2RCi2JsaXXqgeaGTA6Y_jRZB53DuMdjT3qdkhDjI-y0gj_W6BFJYmJ71jfJlPh-Uf6C7xFb4mkvvUMNTHigGaRsIU9GwHfcbBGk6ZAE_zilfCy6WYsahu0YL37_kadUlK_0fJHEiqmNh0S065Rpeu4opF_JMBvz-1ydkQQqev5oF4BR_3AvOjzPzKK40yT6omfd1O-qRYykGVnfx94pHO_q_NH7z85gr-OZIfcwR4D1oX6bfYNIC2KvmBolJ_4QO7HBG5eFbekvBvC4d4s59oPED-Kw9HRKCzhyjilGahfQuCb_he4dqCOD7zYj2We9MbWaer9FyLplwI5xveOKb_11WbZq4FjBL7XJlHwa7_cxflO7GCWMknglWVZN04NcjjCL_cdIt8LzKy4rrwV-JUeNpJATISdC3xIU3H82GqdPWv_jCohT7Qg2RIbf36NUHyBGFeJ24kqjNHXFBRfl2p753xabVSlxLqP-aWIopbDt0SgQdLELOfzM6lFKEaF8IwD-iYnAbbCnPAIShF-rfVyxIpjm','180.181.86.137',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

/*!40000 ALTER TABLE `applog` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table apps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `apps`;

CREATE TABLE `apps` (
  `app_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_key` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `tagline` varchar(120) DEFAULT NULL,
  `icon` varchar(50) NOT NULL DEFAULT '',
  `url` varchar(255) NOT NULL DEFAULT '',
  `launch` varchar(255) NOT NULL DEFAULT '',
  `feed` varchar(255) DEFAULT NULL,
  `auth_type` int(11) NOT NULL DEFAULT '0',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `active` int(11) NOT NULL DEFAULT '0',
  `status` int(11) DEFAULT '0' COMMENT 'released, beta, open source, etc',
  `apienabled` tinyint(4) NOT NULL DEFAULT '0',
  `description` text,
  `media` text COMMENT 'json object',
  PRIMARY KEY (`app_id`),
  UNIQUE KEY `app_key` (`app_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `apps` WRITE;
/*!40000 ALTER TABLE `apps` DISABLE KEYS */;

INSERT INTO `apps` (`app_id`, `app_key`, `name`, `tagline`, `icon`, `url`, `launch`, `feed`, `auth_type`, `added`, `active`, `status`, `apienabled`, `description`, `media`)
VALUES
	(1,'docninja','Document Scormification Ninja','Convert (almost) anything to HTML5 & SCORM','/img/apps/docninja_tile.jpg','http://docninja.coursesuite.ninja/','http://docninja.coursesuite.ninja/app/','',0,'2016-05-30 12:42:15',1,NULL,0,'## You want it done *when?*\r\n\r\n**Scenario:** You\'ve been given a 75-page Word document (no doubt full of tables, images and annotations) and been told to make it into a SCORM-compliant package for your LMS, and learners needs to view at least to page 60 before they are considered complete. And you\'ve got two weeks to get it done. Can you?\r\nSure. How about in the next 5 minutes? *No worries.*\r\n\r\n### We convert your documents, media and slides right in your browser.\r\n<img src=\"/img/HTML5_Logo_128.png\" class=\"h5\" alt=\"HTML5 logo\">Our Document Scormification Ninja is a special in-browser course wizard. With it you can take your existing content converting it to modern, industry standard HTML5 and give it a SCORM wrapper, making it ready to upload into your LMS platform, all in a matter of minutes, and all by dragging links or files onto your browser.\r\n\r\n### Your content is what\'s important.\r\nHere\'s the thing: your content is what\'s important, and you probably don\'t have that much time already. So just drag and drop your files or URL\'s onto our app, give your course a name, and press download. It\'s that easy.\r\nMost of the time the converted HTML5 documents are pixel-perfect to the original document. You\'ll be surprised how good it is. You can also split multi-page documents into separate navigation items (it\'s really handy, be sure to try it).\r\n\r\n<p class=\"formats\">If a document *can* convert to HTML5, we probably support it - <span>abw</span> <span>doc</span> <span>docx</span> <span>epub</span> <span>gif</span> <span>html</span> <span>imgur gallery</span> <span>jpeg</span> <span>key</span> <span>lit</span> <span>md</span> <span>mobi</span> <span>moodle book (ims cp)</span> <span>odp</span> <span>odt</span> <span>pages</span> <span>pdf</span> <span>png</span> <span>ppt</span> <span>pptx</span> <span>ps</span> <span>slideshare</span> <span>soundcloud</span> <span>tiff</span> <span>txt</span> <span>vimeo</span> <span>webp</span> <span>youtube</span> + many other formats.</p>\r\n\r\n### Straightforward commonplace settings with sensible defaults\r\nYou can convert and embed multiple files from different sources, including files of different formats (combine full-page images with YouTube videos and PDF documents, for example). We offer a few easily navigable layouts optimised for most e-learning environments (just the basics). We haven\'t gone all out allowing you to customise every detail in the design: the aim of this tool is for you to get your content online in minutes.\r\n\r\n### Embeddable content\r\nLearning material is often best presented using slideshows, video and audio. But video and audio are large files, with complex caveats depending on the learners platform - you often don\'t know if they use an old laptop or a brand-new iPad, and what works on one might not work on the other. So **we don\'t let you embed video or audio**. When it comes to video and audio, upload them to YouTube, Vimeo or SoundCloud, since those platforms do all the hard work of supporting thousands of end-user devices (and have great privacy options).\r\nBut we do support images, imgur galleries and files, and even SlideShare. Need a scorm completion after a person views half the slides in a SlideShare? Sure thing.\r\n\r\n### How it works\r\nYour documents, images and slideshows will be converted using a cloud-based web service to HTML5. This service supports most formats such as Microsoft Office (Word, PowerPoint, Excel, Works), OpenOffice, AbiWord, Pages, KeyNote, StarWriter, Lotus Word Pro, as well as PDF, Markdown, and even many DRM-free e-book formats (mobi, epub, azw, lit, etc). In fact, if you find a format that *doesn\'t* convert then please let us know!\r\n\r\n* Your documents (e.g. PDF)\r\n* Drag onto Document Ninja\r\n* Conversion (cloud-based service)\r\n* Play with Layout & Settings\r\n* Download (zip file)\r\n* Upload to your LMS\r\n* Happy learners!\r\n\r\n### Completion, Progression, and other options\r\nSCORM is the existing standard that lets reusable, platform-agnostic content communicate data to the host LMS. In most cases this boils down to a single question: *Has the user completed the content?* And in most courses, this is really what you care about.\r\nIn many content authoring packages on the market today, SCORM and completion settings seem unnecessarily complicated, but they don\'t have to be. In our tool we have made some fundamental assumptions based on years of experience in what customers require. In short:</p?\r\n\r\n* The score of the course represents how much is completed (as a percentage).\r\n* Learners may leave and return more than once before they complete. We remember where they are up to, even for most embedded content.\r\n* Completion occurs either when the user views the last page, or gets to a certain point in the course (such as has completed 7 out of 10 pages).\r\n* Tracking individual pages and satisfying objectives at a page level generally doesn\'t happen (it\'s usually done at a unit, course or activity level).\r\n\r\nWe also assume there\'s only two ways to progress through a course. Either page-by-page in order (like a book) or any order (you can skip pages if you choose). So there\'s only two settings.\r\nOh, and did we mention you can get a completion for **watching a some or all of a video**, or **viewing some of a SlideShare presentation**? You can still track how much of the media the user has watched or heard and score the page based on that information. You can use your LMS\'s SCORM tracking tool to examine how much of a video a user has watched, plus if they get more than (say) 75%, they get a completion too. How cool is that? (p.s. if that\'s all you\'re looking for <a href=\"http://media.scormification.ninja\" target=\"_blank\">we have a tool for that</a>.)\r\n\r\n### Who owns the content?\r\nWe don\'t want your content. And so we don\'t store it. In fact, it\'s never even uploaded to our servers (it\'s temporarily sent to the cloud for document conversion, but is deleted immediately afterwards). So your content is safe with **you**.\r\nYour content is stored by your own browser, which is permanent until you reset the app. So after you are finished, remember to reset (especially if you are using a shared computer).\r\nWe do keep your email address on file, and we track some details about how you *use* our software (which settings are most popular), but that\'s all we know about you or your data.\r\n\r\n### What about editing?\r\nYou can change the order of pages, split long documents into multiple pages, remove or rename individual pages, and change the design or colour theme of the navigation wrapper.\r\nThis isn\'t an editing platform. Do your edits in your source: Word or Powerpoint or SlideShare or whatever (that is; use the tools are best at the job) and just convert their output. But you can *tweak* text after the file is converted (with caution, useful for a typo or two, but not a paragraph). We have <a href=\"http://coursesuite.ninja\" target=\"_blank\">other tools</a> with full editing capability.\r\n\r\n### Spend your time where it counts. \r\n<img src=\"/img/pie.png\" alt=\"pie graph showing less time building, more time learning\" class=\"pie\">\r\nDo the conversion, stick it on your LMS. Let your learners spend time learning.\r\n\r\n### Browser requirements\r\nWe have developed the **builder app** to be most compatible with Google Chrome or Mozilla Firefox for *desktops*. It might work in other browsers, or might not. The **builder** currently doesn\'t work on mobile devices (but we are considering it) because of limitations with their file systems.\r\nThe **content you produce** should work fine *in any modern browser*, depending on the capabilities and what you put in yourself, and the host LMS (Don\'t expect a 15MB image to show on a mobile device, or a Vimeo video to play offline).\r\n','[{\"image\":\"\\/img\\/apps\\/docninja\\/56d3fa0057249762d221acf26300e915.png\",\"thumb\":\"\\/img\\/apps\\/docninja\\/56d3fa0057249762d221acf26300e915.png_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/docninja\\/56d3fa0057249762d221acf26300e915.png_thumb459.jpg\",\"caption\":\"I made a spelling mistake in the caption code, so i had to enter this by hand. As you can read, it appears to have worked now.\",\"bgcolor\":\"rgb(24,34,37)\"},{\"image\":\"\\/img\\/apps\\/docninja\\/d6938fd3708cbb65ef1dd9f718fdf9c4.png\",\"thumb\":\"\\/img\\/apps\\/docninja\\/d6938fd3708cbb65ef1dd9f718fdf9c4.png_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/docninja\\/d6938fd3708cbb65ef1dd9f718fdf9c4.png_thumb459.jpg\",\"caption\":\"some screenshot caption that didn\'t save properly\",\"bgcolor\":\"rgb(238,237,236)\"},{\"video\":\"https:\\/\\/www.youtube.com\\/embed\\/meUhHRqWkeY\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/meUhHRqWkeY\\/default.jpg\",\"caption\":null,\"bgcolor\":\"#000000\"},{\"image\":\"\\/img\\/apps\\/docninja\\/4a9298776e53713154c4366ec863f3f3.jpg\",\"thumb\":\"\\/img\\/apps\\/docninja\\/4a9298776e53713154c4366ec863f3f3.jpg_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/docninja\\/4a9298776e53713154c4366ec863f3f3.jpg_thumb459.jpg\",\"caption\":\"a jetty sticking out in to the &#039;ocean&#039;\",\"bgcolor\":\"rgb(56,113,168)\"},{\"image\":\"\\/img\\/apps\\/docninja\\/9c82377b26fe993d5601ffa18540e626.png\",\"thumb\":\"\\/img\\/apps\\/docninja\\/9c82377b26fe993d5601ffa18540e626.png_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/docninja\\/9c82377b26fe993d5601ffa18540e626.png_thumb459.jpg\",\"caption\":\"\",\"bgcolor\":\"rgb(31,77,130)\"},{\"image\":\"\\/img\\/apps\\/docninja\\/4185af3f36834ea12704068935a21a66.png\",\"thumb\":\"\\/img\\/apps\\/docninja\\/4185af3f36834ea12704068935a21a66.png_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/docninja\\/4185af3f36834ea12704068935a21a66.png_thumb459.jpg\",\"caption\":\"\",\"bgcolor\":\"rgb(62,104,210)\"},{\"video\":\"https:\\/\\/www.youtube.com\\/embed\\/OF1Q3_r_9mc\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/OF1Q3_r_9mc\\/default.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(139,189,89,.5)\"},{\"image\":\"\\/img\\/apps\\/docninja\\/c5d52a033dddf4b7f2bc05de1674c0c7.gif\",\"thumb\":\"\\/img\\/apps\\/docninja\\/c5d52a033dddf4b7f2bc05de1674c0c7.gif_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/docninja\\/c5d52a033dddf4b7f2bc05de1674c0c7.gif_thumb459.jpg\",\"caption\":\"monkey\",\"bgcolor\":\"rgba(40,168,161,.5)\"}]'),
	(2,'vidninja','Media Scormification Ninja','Add SCORM completions to video','/img/apps/medianinja_tile.jpg','http://vidninja.coursesuite.ninja/','http://vidninja.coursesuite.ninja/app/','',0,'2016-05-30 10:59:50',1,NULL,0,'<h2 class=\"section-heading\">Info</h2>\r\n<p>The Media Scormification Ninja converts your streaming video or audio file to a SCORM package, so that you can get a completion after the user watches a certain amount of the media. It does not embed the file directly in the package - it still uses the same capabilities as supplied by the host when simply using their supplied Embed or Share codes, but in such a way as that it can track the amount of time that the media has played. Once you\'ve set the place where you consider the media \"watched\" (we call a Marker), you can then download the ready-made Zip file which contains all the neccesary SCORM files.</p>\r\n<h3>Why only support streaming media?</h3>\r\n<p>BrightCove, YouTube, Vimeo and SoundCloud all work very hard to support the widest range of browsers and platforms for their content - its in their best interest. This means that your media will play in desktop, tablet and phone devices alike. We also use their standard players (not custom \'skins\' or players) to ensure the maximum flexibility and compatibility. Conversely, embedding media means also embedding a range of possible players, skins, and script files to account for different devices, different browser capabilities, and so on (in most cases duplicating the media in multiple formats): it\'s much harder, more bug-prone, less customisable, and makes huge files which you then have to deal with.</p>\r\n<p>Using streaming services is <em>cheaper for you</em>: It saves your servers bandwidth (data-transfer quota and costs per megabyte) from serving potentially large media files. Media is uploaded only once - but can be used in multiple courses and sites, in formats that are correct for the clients browsers or devices. If changes are made to the media, all courses and sites using that media will all automatically get the most current version of the material.</p>\r\n<h3>Click on a heading below for more informtion.</h3>\r\n<details>\r\n      <summary>Why would I want to track videos with SCORM?</summary>\r\n      <p>Let\'s say your learners need to watch material in order to gain a Continuing Professional Development (CPD) credit. Their industries\' rules state that they need to watch at least 45 minutes of a 1 hour video in order to gain 1 point. </p>\r\n      <p>The tool lets you do that. You can specify a video URL, set the completion requirement to 75%, and publish it as a SCORM package. You then just drop this into your LMS and start recording the completions.</p>\r\n      <p>We also record how much of the video has been watched. This has two benefits - <em>for the learner</em> it means they can exit the activity and come back another time and have the video pick back up where they left off; and <em>for the trainer</em> it means you can report on exactly how much of the media an individual is actually watching or listening to.</p>\r\n</details>\r\n<details>\r\n      <summary>What SCORM version do you publish to?</summary>\r\n      <p>We package using SCORM 1.2, as it is the most widely implemented version.</p>\r\n</details>\r\n<details>\r\n      <summary>What are the video sizes?</summary>\r\n      <p>It\'s generally recommended to use the Responsive size, as this means the video will scale to fit its container whilst maintaining a 16:9 aspect ratio.</p>\r\n      <p>Media sizes are taken from the default options for each of the players\' embed options. These are:</p>\r\n      <ul>\r\n            <li><b>YouTube</b> - Small: 560px x 315px, Large: 853px x 480px, Responsive: 100% x 56.25%</li>\r\n            <li><b>Vimeo</b> - Small: 500px x 281px, Large: 960px x 540px, Responsive: 100% x 56.25%</li>\r\n            <li><b>SoundCloud</b> - Small: 100% x 166px (Standard layout), Large: 100% x 450px (Visual layout), Responsive: 100% x 56.25% (Visual layout)</li>\r\n            <li><b>BrightCove</b> - Small: 512px x 288px, Large: 768px x 432px, Responsive: 100% x 56.25% (16:9)</li>\r\n      </ul>\r\n</details>\r\n<details>\r\n      <summary>Which Learning Management Systems do you support?</summary>\r\n      <p>Any SCORM 1.2 compliant LMS, such as Moodle, Course Cloud, Totara, Scorm Cloud, Blackboard, or hundreds of others.</p>\r\n</details>\r\n<details>\r\n      <summary>What if a learner only watches some of the video and returns later to watch the rest?</summary>\r\n      <p>They will resume the video or sound file at the exact moment they left the package when they return -and their completion will occur when they reach the desired percentage of media to be viewed.</p>\r\n</details>\r\n<details>\r\n      <summary>What about privacy? I don’t want the whole world being able to access our videos.</summary>\r\n      <p>You need to consult the options for your video host. For instance, some Vimeo accounts can restrict which domains are able to watch the video.</p>\r\n</details>\r\n<details>\r\n      <summary>Do I need to download any software to use this app?</summary>\r\n      <p>Since it is web based, it requires no installation or any plugins</p>\r\n</details>\r\n<details>\r\n      <summary>How do I embed BrightCove video?</summary>\r\n      <p>Ok, this one is a little involved. BrightCove has a concept called Players, which are skins for the various videos. Each video can be published with one or more skins, and you can set up multiple players. One of these is a HTML5 player, and in it you must also specify that the player use API\'s and HTML5 delivery. Please refer to the BrightCove documentation about how to do this.</p>\r\n      <p class=\"text-center\"><img src=\"/img/brightcove_player.png\"></p>\r\n      <p>Some brightcove player embed codes look like this: <em>http://bcove.me/u4bjl68t</em> - we can\'t support this format since it doesn\'t identify which player will be used (it *might* be one with an API enabled, but there isn\'t a way to know). So you have to use the <b>javascript</b> embed code, which you can find in the Quick Video Publish tool:</p>\r\n      <p class=\"text-center\"><img src=\"/img/brightcove_publish.png\"></p>\r\n</details>\r\n\r\n<details>\r\n      <summary>Why the Ninja?</summary>\r\n      <p>Because it’s cool. We like ninjas.</p>\r\n</details>\r\n\r\n                <h2>Under the hood</h2>\r\n<p>So you might be wondering what the innards of this thing actually are (how it works). It uses a minimal SCORM 1.2 based engine that, like most wrappers, expects the SCORM API to exist in a parent frame or window. It also has hand-coded listeners that tap the player API\'s that BrightCove, YouTube, Vimeo and SoundCloud expose on order to capture information about the media being played. These wrappers call SCORM commands as the media plays.</p>\r\n<details>\r\n    <summary>Click here for all the gory details</summary></p>\r\n    <ol>\r\n        <li><b>onload</b>:<ol>\r\n            <li>perform a scorm initialise (gets the api)</li>\r\n            <li>read <em>cmi.core.entry</em> (set to ab-initio on first launch)</li>\r\n            <li>read <em>cmi.core.lesson_location</em> (last position in video)</li>\r\n        </ol></li>\r\n        <li><b>on video start</b>:<ol>\r\n            <li>set <em>cmi.core.exit</em> to \"suspend\"</li>\r\n            <li>set <em>cmi.core.lesson_status</em> to \"incomplete\"</li>\r\n            <li>perform a scorm commit</li>\r\n        </ol></li>\r\n        <li><b>periodically</b> (as video is playing):<ol>\r\n            <li>set <em>cmi.core.lesson_location</em> to the number of seconds the video is up to. You can use this value to check how much a learner actually viewed through to.</li>\r\n            <li>Check the required amount to be watched, and if it matches or is greater, perform a completion (see below).</li>\r\n        </ol></li>\r\n        <li><b>on pause, rebuffer, or end</b>:<ol>\r\n            <li>Call LMS Commit to persist the changed data (e.g. seconds played)</li>\r\n        </ol></li>\r\n        <li><b>on completion</b>:<ol>\r\n            <li>set <em>cmi.core.exit</em> to \"\" (blank string, effectively means \"logout\" according to the SCORM spec)</li>\r\n            <li>set <em>cmi.core.lesson_status</em> to \"completed\"</li>\r\n            <li>set <em>cmi.core.score.min</em> to \"0\"</li>\r\n            <li>set <em>cmi.core.score.max</em> to \"100\"</li>\r\n            <li>set <em>cmi.core.score.raw</em> to the required percentage</li>\r\n            <li>perform a scorm commit</li>\r\n        </ol></li>\r\n        <li><b>onunload</b>:<ol>\r\n            <li>perform a scorm commit</li>\r\n            <li>perform a scorm finish</li>\r\n        </ol></li>\r\n    </ol>\r\n    <p>Effectively, this means the video is able to be resumed from the point the learner leaves, as <em>lesson_location</em> is being stored. It also means that the score required for completion in your LMS is the same percentage that needs to be watched.</p>\r\n</details>\r\n\r\n<h2>Setting it up in a LMS</h2>\r\n<ul>\r\n    <li>Make sure you are embedding this into a SCORM-compliant LMS.</li>\r\n    <li>Base your completion on the SCORE rather than a lesson_status (because the completed / passed status is buggy in a number of LMS\'s due to ambiguity in the SCORM specification).</li>\r\n    <li>Set the completion required score to the <u>percentage watched</u> that is set for the video (shown when you set a marker).</li>\r\n</ul>','[{\"video\":\"https:\\/\\/www.youtube.com\\/embed\\/9lzs50gCfwQ?rel=0&showinfo=0&iv_load_policy=3\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/9lzs50gCfwQ\\/default.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(94,132,75,.5)\"},{\"image\":\"\\/img\\/apps\\/vidninja\\/d8cea3d11eb88d001c00903d2acd00c2.jpg\",\"thumb\":\"\\/img\\/apps\\/vidninja\\/d8cea3d11eb88d001c00903d2acd00c2.jpg_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/vidninja\\/d8cea3d11eb88d001c00903d2acd00c2.jpg_thumb459.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(178,141,103,.5)\"}]'),
	(3,'coursebuildr','CourseBuildr','Powerful, interactive SCORM courses & quizzes','/img/apps/coursebuildr_tile.jpg','http://coursebuildr.coursesuite.ninja/','http://coursebuildr.coursesuite.ninja/','',0,'2016-05-30 10:59:10',1,NULL,0,'<p>CourseBuildr is a full featured only course and quiz editor that lets you create rich, interactive HTML5 courses without know HTML or using esoteric Flash-based builders. It\'s much more than a simple slideshow maker - after all, if that\'s all you need then you can just use presentation software. But courses are ineffective communicators without interaction.</p>\r\n<p>You can quickly add visual elements to your courses such as tab bars, expanding sections (accordions), slideshows, popup video, references and term definitions, balloon tips, flip-cards, inline quizzes and much more - all without knowing any scripting language or HTML.</p>\r\n<p>CourseBuildr produces SCORM 1.2 or SCORM 2004 compatible courses that play on all your platforms - no more publishing multiple packages for each platform, or bundling extra code or hacks for just one device. Everything works the same everywhere.</p>\r\n<p>We use this product internally and have developed it over a number of years and continue to produce course material using the tool. As we need new features or interactions, we add them in. Every time you download or preview a course, it gets updated to the latest code and includes the latest suite of interactions.</p>\r\n<p>Multiple users can work on the same course at the same time. Publishing a course takes minutes and the courses themselves are typically only a few MB\'s in size. The built-in preview page hosts a simple SCORM API to emulate the package as it appears in a LMS environment, letting you test out suspending the session or how it appears at different screen resolutions.</p>','[{\"video\":\"https:\\/\\/www.youtube.com\\/embed\\/x93hyEq3hUQ?rel=0&showinfo=0&iv_load_policy=3\",\"thumb\":\"https:\\/\\/i.ytimg.com\\/vi\\/x93hyEq3hUQ\\/default.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(5,5,5,.5)\"},{\"image\":\"\\/img\\/apps\\/coursebuildr\\/ea56e96dfebafca5ecb00ed538b516a9.jpg\",\"thumb\":\"\\/img\\/apps\\/coursebuildr\\/ea56e96dfebafca5ecb00ed538b516a9.jpg_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/coursebuildr\\/ea56e96dfebafca5ecb00ed538b516a9.jpg_thumb459.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(185,194,194,.5)\"},{\"image\":\"\\/img\\/apps\\/coursebuildr\\/bb75be573b2c4a6a3771a68a9c100556.jpg\",\"thumb\":\"\\/img\\/apps\\/coursebuildr\\/bb75be573b2c4a6a3771a68a9c100556.jpg_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/coursebuildr\\/bb75be573b2c4a6a3771a68a9c100556.jpg_thumb459.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(73,113,101,.5)\"},{\"image\":\"\\/img\\/apps\\/coursebuildr\\/91449637691efd7368a302a232a152a0.jpg\",\"thumb\":\"\\/img\\/apps\\/coursebuildr\\/91449637691efd7368a302a232a152a0.jpg_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/coursebuildr\\/91449637691efd7368a302a232a152a0.jpg_thumb459.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(201,141,59,.5)\"}]'),
	(4,'wp2moodle','Wordpress 2 Moodle','Sign in and enrol a user to Moodle, from Wordpress','/img/apps/app_tile4.png','http://wp2moodle.coursesuite.ninja/','',NULL,1,'2016-05-30 10:59:41',1,0,0,NULL,NULL),
	(5,'tokenenrol','Token Enrolment','Manage or sell places in a course as seats','/img/apps/app_tile5.png','http://wp2moodle.coursesuite.ninja/token-enrolment/','',NULL,1,'2016-05-30 10:59:53',1,0,0,NULL,NULL),
	(6,'coursecatalogue','Course Catalogue','Use custom fields on courses to build a catalogue','/img/apps/app_tile6.png','https://github.com/frumbert/moodle-course_meta/tree/Moodle2.8.7-MultiSelect-MetaFilter','',NULL,1,'2016-05-30 10:59:54',1,0,0,NULL,NULL),
	(7,'fakecourse','some fake course we mocked up','Some kind of secret project maybe','/img/apps/app_tile7.png','','fakecourse','',0,'2016-05-20 12:42:04',0,0,0,'','[{\"image\":\"\\/img\\/apps\\/fakecourse\\/ef7ecde97d6158846d1ece19fa12ee93.jpg\",\"thumb\":\"\\/img\\/apps\\/fakecourse\\/ef7ecde97d6158846d1ece19fa12ee93.jpg_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/fakecourse\\/ef7ecde97d6158846d1ece19fa12ee93.jpg_thumb459.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(48,50,28,.5)\"},{\"image\":\"\\/img\\/apps\\/fakecourse\\/60ff312948a95fc10a194481e7f93103.jpg\",\"thumb\":\"\\/img\\/apps\\/fakecourse\\/60ff312948a95fc10a194481e7f93103.jpg_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/fakecourse\\/60ff312948a95fc10a194481e7f93103.jpg_thumb459.jpg\",\"caption\":\"boob\",\"bgcolor\":\"rgba(213,213,217,.5)\"},{\"image\":\"\\/img\\/apps\\/fakecourse\\/43e727e863a5a1a960246b26864f6032.jpg\",\"thumb\":\"\\/img\\/apps\\/fakecourse\\/43e727e863a5a1a960246b26864f6032.jpg_thumb120.jpg\",\"preview\":\"\\/img\\/apps\\/fakecourse\\/43e727e863a5a1a960246b26864f6032.jpg_thumb459.jpg\",\"caption\":\"\",\"bgcolor\":\"rgba(187,183,179,.5)\"}]'),
	(8,'activity_tiles','Activity Tile Grid','A grid plugin for activities within a section','/img/apps/activity_tiles/a0a71ff81d456ef8b2f32a8f3','','','',1,'2016-05-30 10:59:34',0,0,0,'','');

/*!40000 ALTER TABLE `apps` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table broadcast
# ------------------------------------------------------------

DROP TABLE IF EXISTS `broadcast`;

CREATE TABLE `broadcast` (
  `broadcast_id` int(11) NOT NULL AUTO_INCREMENT,
  `broadcast_name` varchar(128) NOT NULL,
  `broadcast_desc` varchar(528) NOT NULL,
  `user_id` int(11) NOT NULL,
  `broadcast_date` date NOT NULL,
  PRIMARY KEY (`broadcast_id`),
  KEY `FK_broadcast_users` (`user_id`),
  CONSTRAINT `FK_broadcast_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table broadcastmarkread
# ------------------------------------------------------------

DROP TABLE IF EXISTS `broadcastmarkread`;

CREATE TABLE `broadcastmarkread` (
  `markread_id` int(11) NOT NULL AUTO_INCREMENT,
  `broadcast_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `markasread` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`markread_id`),
  KEY `FK_markread_users` (`user_id`),
  KEY `FK_broadcastmarkread_broadcast` (`broadcast_id`),
  CONSTRAINT `FK_broadcastmarkread_broadcast` FOREIGN KEY (`broadcast_id`) REFERENCES `broadcast` (`broadcast_id`),
  CONSTRAINT `FK_markread_users` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;



# Dump of table news
# ------------------------------------------------------------

DROP TABLE IF EXISTS `news`;

CREATE TABLE `news` (
  `news_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `news_title` text COLLATE utf8_unicode_ci NOT NULL,
  `news_text` text COLLATE utf8_unicode_ci NOT NULL,
  `app_id` int(11) DEFAULT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`news_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user notes';

LOCK TABLES `news` WRITE;
/*!40000 ALTER TABLE `news` DISABLE KEYS */;

INSERT INTO `news` (`news_id`, `news_title`, `news_text`, `app_id`, `user_id`)
VALUES
	(1,'test','testing',NULL,1);

/*!40000 ALTER TABLE `news` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table notes
# ------------------------------------------------------------

DROP TABLE IF EXISTS `notes`;

CREATE TABLE `notes` (
  `note_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `note_text` text COLLATE utf8_unicode_ci NOT NULL,
  `user_id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`note_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user notes';

LOCK TABLES `notes` WRITE;
/*!40000 ALTER TABLE `notes` DISABLE KEYS */;

INSERT INTO `notes` (`note_id`, `note_text`, `user_id`)
VALUES
	(1,'hhere\'s my notte for all to read... no, actually, it\'s only for ME to read.',1),
	(2,'bleh',2),
	(3,'foo1',3);

/*!40000 ALTER TABLE `notes` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table session_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session_data`;

CREATE TABLE `session_data` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `session_data` blob NOT NULL,
  `session_expire` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `session_data` WRITE;
/*!40000 ALTER TABLE `session_data` DISABLE KEYS */;

INSERT INTO `session_data` (`session_id`, `hash`, `session_data`, `session_expire`)
VALUES
	('07f2m0093i80dpf4pr8jhp7uq2','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461274170),
	('0a70ihadekqv61rji19jnoit66','1586bdaad0b6a0d7d29855ce50308d5d',X'5265646972656374546F7C733A31393A2273746F72652F696E666F2F646F636E696E6A61223B',1461212675),
	('0jjid9o8c4g2hee1bl7vd83p17','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1462150134),
	('0ppsrl8mv4c7jldnrvbd9357v5','d152e6f07516e17ca9f45a92b3b871f3',X'757365725F69647C733A313A2231223B757365725F6E616D657C733A343A2264656D6F223B757365725F656D61696C7C733A31333A2264656D6F4064656D6F2E636F6D223B757365725F6163636F756E745F747970657C733A313A2237223B757365725F70726F76696465725F747970657C733A373A2244454641554C54223B757365725F6176617461725F66696C657C733A34353A22687474703A2F2F636F7572736573756974652E6672756D626572742E6F72672F617661746172732F312E6A7067223B757365725F67726176617461725F696D6167655F75726C7C733A38343A2268747470733A2F2F7777772E67726176617461722E636F6D2F6176617461722F35333434346639316536393863306337636161326462633362646266393366633F733D343426643D7761766174617226723D7067223B757365725F6C6F676765645F696E7C623A313B666565646261636B5F706F7369746976657C4E3B666565646261636B5F6E656761746976657C4E3B666565646261636B5F617265617C4E3B',1463964486),
	('1096h05o6lauoh5j9trj9t2fm2','11fe99c0460506108801bd303606546d','',1464232438),
	('11hanlv9i9pddccvkeg26fsrn5','f628356cee6cf4cf5249828feed7fcb3','',1464577912),
	('18q6kqihm3tou8copvbd6a1nb4','11fe99c0460506108801bd303606546d','',1464230932),
	('197ibbmto2b8emid4j1ep9d8m0','11fe99c0460506108801bd303606546d','',1464230372),
	('1msc6se1miv85ur6m5vddtf8g6','11fe99c0460506108801bd303606546d','',1464233932),
	('299jud087ac1n3bp61miaph446','f628356cee6cf4cf5249828feed7fcb3','',1464577912),
	('31dac18si1u38j5qls3ah44gt6','f628356cee6cf4cf5249828feed7fcb3','',1464309022),
	('342p8dg7asrm326g2oj04p3dr4','f628356cee6cf4cf5249828feed7fcb3','',1464253961),
	('3a3l6qucgnqk5soj27fr2ivg13','f628356cee6cf4cf5249828feed7fcb3','',1464576971),
	('3gb6nv4ur4r689nfm38mn87303','f628356cee6cf4cf5249828feed7fcb3','',1464309047),
	('3hursa7882auha4s838b75qh62','f628356cee6cf4cf5249828feed7fcb3','',1464257471),
	('3v23rp1k7b1es5k0jcgtvkqo70','581b2784fe646d57706ad26dbefd9208',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462754248),
	('48klp6jm57kv6l11cf4gks5bp0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461104731),
	('4coktjsbvej9trgu4mmmstave2','f628356cee6cf4cf5249828feed7fcb3','',1464576971),
	('4drc9f7mn42322nv6dg0er9uh7','11fe99c0460506108801bd303606546d','',1464233721),
	('4edbt9dbdu33228fs7uj5dk7a1','11fe99c0460506108801bd303606546d','',1464231153),
	('4f87t8r4kl3id6qeliakcpnvj0','f628356cee6cf4cf5249828feed7fcb3','',1464309022),
	('4na56em76qmpa4ds3rehrdt0f1','f628356cee6cf4cf5249828feed7fcb3','',1464253982),
	('4vtuim8h5bn95rbsk90vmgo894','f628356cee6cf4cf5249828feed7fcb3','',1464257471),
	('52m8p0g3ru6p2pv1iabolabcp4','f628356cee6cf4cf5249828feed7fcb3','',1464576971),
	('53dfsbvdi48meaf9ncmk6spn90','f628356cee6cf4cf5249828feed7fcb3','',1464576971),
	('5anj5kc526flhvs6q2r03jg490','621d2c3b0fc21148823ac07003013b31','',1464220446),
	('5dk1o2on9vm57d05scqloo1qm2','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461034198),
	('5efiiiim1vljg4vko4jfm79c97','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1460929127),
	('5ghp2c2hdek148kaa8cs0rcm90','46bfbf8e2abb836fd5cb1706a683a843',X'637372665F746F6B656E7C733A33323A223862323235663533653864303636623338333530383539646532396166616232223B637372665F746F6B656E5F74696D657C693A313436333236353935363B',1463267396),
	('5j3mv6tv17nuvjctb94d1k8qc6','719a25b100f1e93b0871c8102fee047e',X'757365725F69647C733A323A223131223B757365725F6E616D657C733A363A22717765727479223B757365725F656D61696C7C733A32343A22746573743140636F7572736573756974652E636F6D2E6175223B757365725F6163636F756E745F747970657C733A313A2231223B757365725F70726F76696465725F747970657C733A373A2244454641554C54223B757365725F6176617461725F66696C657C733A34373A22687474703A2F2F6D792E636F7572736573756974652E6E696E6A612F617661746172732F64656661756C742E6A7067223B757365725F67726176617461725F696D6167655F75726C7C733A38343A2268747470733A2F2F7777772E67726176617461722E636F6D2F6176617461722F65656431353636656331363337653663646564373735663265356138613032323F733D343426643D7761766174617226723D7067223B757365725F6C6F676765645F696E7C623A313B',1464577070),
	('5oc4490p2nh02p4gm07cocpbi7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462164001),
	('5tb25j2h8st0tarnelvrc5ql17','11fe99c0460506108801bd303606546d','',1464231801),
	('6bkt2214i0s24dj505j0n2o5k5','f628356cee6cf4cf5249828feed7fcb3','',1464577912),
	('6dugu965gmb33vpjkv9ploo3g3','f628356cee6cf4cf5249828feed7fcb3','',1464326238),
	('6et7aes99b2a1v3bulbpli3dt5','f628356cee6cf4cf5249828feed7fcb3','',1464309021),
	('6gvqvb4189p0h28bue1pb0erm0','f628356cee6cf4cf5249828feed7fcb3','',1464326238),
	('6l6o8dbnappsgo6vippgbpgl00','f628356cee6cf4cf5249828feed7fcb3','',1464253982),
	('6o39aagq075197bnvttcbr0i25','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461359574),
	('6qb8cfo58j47ordv82f9cqnmn3','f628356cee6cf4cf5249828feed7fcb3','',1464577912),
	('725apd6co6803c0frfp0hlril7','f34c5800ed8bca5c940df91660d80ee3',X'637372665F746F6B656E7C733A33323A223964326666363135323631656663353534643038376236333161616264333632223B637372665F746F6B656E5F74696D657C693A313436333532393536373B',1463531007),
	('77r1gqm8ncictv5vdcj3ekrav5','f34c5800ed8bca5c940df91660d80ee3',X'637372665F746F6B656E7C733A33323A223933326130393635623530613037343465376537333664326338346337356235223B637372665F746F6B656E5F74696D657C693A313436333338303638333B',1463382123),
	('78nhdi6ub4kkmvb4hh3avkm862','f628356cee6cf4cf5249828feed7fcb3','',1464326238),
	('7qdf2sumscp6u027gebb79e8m3','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1463122855),
	('7qus9rh7hkjn24jc3bq7einqi7','a55db367839d193fa45c98d64047868c',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461216745),
	('7shp50t84bkh2tev6hoeeo8417','861fd43f5883808b7af29eb425d67fe4',X'757365725F69647C733A323A223131223B757365725F6E616D657C733A363A22717765727479223B757365725F656D61696C7C733A32343A22746573743140636F7572736573756974652E636F6D2E6175223B757365725F6163636F756E745F747970657C733A313A2231223B757365725F70726F76696465725F747970657C733A373A2244454641554C54223B757365725F6176617461725F66696C657C733A34373A22687474703A2F2F6D792E636F7572736573756974652E6E696E6A612F617661746172732F64656661756C742E6A7067223B757365725F67726176617461725F696D6167655F75726C7C733A38343A2268747470733A2F2F7777772E67726176617461722E636F6D2F6176617461722F65656431353636656331363337653663646564373735663265356138613032323F733D343426643D7761766174617226723D7067223B757365725F6C6F676765645F696E7C623A313B666565646261636B5F706F7369746976657C4E3B666565646261636B5F6E656761746976657C4E3B666565646261636B5F617265617C4E3B637372665F746F6B656E7C733A33323A223833353236386336643865353361373363653439623135303338393761343439223B637372665F746F6B656E5F74696D657C693A313436343537363531313B',1464578004),
	('7u2hhal9fau1cici7l2ctu4221','f628356cee6cf4cf5249828feed7fcb3','',1464253961),
	('866kg897kpf6no4m7hdbrggg11','f628356cee6cf4cf5249828feed7fcb3','',1464254055),
	('88i7vdkl0ock1gip5l0nmhr6v7','f628356cee6cf4cf5249828feed7fcb3','',1464254055),
	('8d1shuh9n3h9p1u3nj0i0349s7','d152e6f07516e17ca9f45a92b3b871f3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B637372665F746F6B656E7C733A33323A223766353035323732356638646239353062656132623738646138326464353831223B637372665F746F6B656E5F74696D657C693A313436333936323637323B666565646261636B5F617265617C4E3B666F726D5F646174617C613A313A7B733A31383A22757365725F6E616D655F6F725F656D61696C223B733A32343A22746573743140636F7572736573756974652E636F6D2E6175223B7D666565646261636B5F6E656761746976657C4E3B666565646261636B5F706F7369746976657C613A313A7B693A303B733A34393A22412070617373776F7264207265736574206D61696C20686173206265656E2073656E74207375636365737366756C6C792E223B7D',1463967252),
	('8febchpjm9qn6kl4mq31ol2jo6','f628356cee6cf4cf5249828feed7fcb3','',1464309047),
	('8hh45lvdj9q6rmn0elkvn34o21','f34c5800ed8bca5c940df91660d80ee3',X'637372665F746F6B656E7C733A33323A223663663532323735646631663232653062323734373664353162316265386561223B637372665F746F6B656E5F74696D657C693A313436333631323435383B',1463613898),
	('8lkjp6j4ra5iomheu9nbpjokq6','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463319365),
	('8ojd83bd74cg0u48sc2r6odie1','f628356cee6cf4cf5249828feed7fcb3','',1464326238),
	('8ordo02maqgtpnkh9nmamro182','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463811597),
	('8t7v56l6seoob4husgcu1bru37','f628356cee6cf4cf5249828feed7fcb3','',1464309047),
	('937q0uhjdirhffv1solcigl2k7','11fe99c0460506108801bd303606546d','',1464233569),
	('95stln0cslk51t1ohn26is0cd4','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463289907),
	('97plutdil9gh74mld1lugq8so7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463096540),
	('9b104j5h4c1mn8v9ld2lodv8f4','f628356cee6cf4cf5249828feed7fcb3','',1464576971),
	('9bvinc4vr5q3o4te962g7mi0b4','f628356cee6cf4cf5249828feed7fcb3','',1464577889),
	('a0ikjcg92e5e8gl8ggf4jqfck2','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1464393058),
	('aeciged395r8acs7rdrfmm8kt0','f628356cee6cf4cf5249828feed7fcb3','',1464309047),
	('aeimvfi7k3dgi8eri6k4ntrl21','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463225093),
	('b2o1o7i79ahsh6suchu60ggfp0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461048479),
	('b2sp42kheiaab37kf3ht5tqu00','1586bdaad0b6a0d7d29855ce50308d5d',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462437101),
	('b8i3vgp4gv57qs4gg4ltvlskt2','f628356cee6cf4cf5249828feed7fcb3','',1464309047),
	('bf4k97mf3r6ig0i36obf463vd1','11fe99c0460506108801bd303606546d','',1464232574),
	('blvrugo9n9ktjtgse8p3sajl74','11fe99c0460506108801bd303606546d','',1464232771),
	('bqa4ni9rm2a81mj25hhm6tkfv4','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1462162761),
	('bqkhd0hgh9v5u0q80q0b2lq1v2','a55db367839d193fa45c98d64047868c',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1460942046),
	('c669g2vjaat26cnkioa8cs8ht2','11fe99c0460506108801bd303606546d','',1464142343),
	('ccgcle1filin3a9tf0d6j5dsm3','f628356cee6cf4cf5249828feed7fcb3','',1464577912),
	('cietbs5rvh7edappugldjn4hg0','11fe99c0460506108801bd303606546d','',1464234714),
	('cmbare56psu9i48hpmii42cnv2','f628356cee6cf4cf5249828feed7fcb3','',1464253961),
	('cte3nsplaq0dgo8fp8ufpgn971','f628356cee6cf4cf5249828feed7fcb3','',1464254055),
	('db80e8t3685vohnooier1imf31','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463014755),
	('dqg3qhqsea6rifg5d2crv537e6','11fe99c0460506108801bd303606546d','',1464239322),
	('ehhpuh7q68rnmsgblpmom2vld2','581b2784fe646d57706ad26dbefd9208',X'637372665F746F6B656E7C733A33323A223430656530363464663363336335316135623962333639353265376532313738223B637372665F746F6B656E5F74696D657C693A313436333238383735383B',1463290198),
	('el32vg03j77p9udpi33begbu56','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463619384),
	('ep16rd9ahsg5mdi5rfbbiaju70','11fe99c0460506108801bd303606546d','',1464233118),
	('eq63c5qck33mtqgqnk0ssr26c4','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462667971),
	('erauqp47g01hv70c490p72c8q5','f628356cee6cf4cf5249828feed7fcb3','',1464577888),
	('evsuoj3kej37mshk2gr9cgpd87','621d2c3b0fc21148823ac07003013b31','',1464220148),
	('f3ekj7t4ik10f053vbdel1hvg1','f628356cee6cf4cf5249828feed7fcb3','',1464253982),
	('fecheg3afu1fonte94lnii3rn2','f628356cee6cf4cf5249828feed7fcb3','',1464576971),
	('finv0ng1k2sm2uauf5engvvcv0','11fe99c0460506108801bd303606546d','',1464232318),
	('flbrhk05ku2k248mc7fptnh9t1','f628356cee6cf4cf5249828feed7fcb3','',1464254055),
	('fp4c6g2h1ubq42oc6v831oals2','f628356cee6cf4cf5249828feed7fcb3','',1464577889),
	('fu4nnhnqtbqu2qsjvvhm6nu9t6','11fe99c0460506108801bd303606546d','',1464231337),
	('g5pha2uk7crc9sq8ji4sinjlq6','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461452897),
	('ga145i3h1cp128lthb9olsih34','f628356cee6cf4cf5249828feed7fcb3','',1464253982),
	('gd0unhl7htvnqp1di7mhihs4d3','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1463356511),
	('gjhvsl86vq4r00c3sqk5phocp5','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462540059),
	('gk6192doth5k0vpunughtg9c01','f628356cee6cf4cf5249828feed7fcb3','',1464577889),
	('goafqgp7l66vhubeduruikull0','861fd43f5883808b7af29eb425d67fe4',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1464304150),
	('hapijhmmdhai5eecsj2h0e3nd6','f628356cee6cf4cf5249828feed7fcb3','',1464254055),
	('hjq47rqk1t6afmh37mnu2cofi2','f628356cee6cf4cf5249828feed7fcb3','',1464254055),
	('ho9mfk1s9vd9r6g2f8uhbhp4d5','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462318697),
	('i2hu7j29k8ssa2fgam8dp16hi1','f628356cee6cf4cf5249828feed7fcb3','',1464253961),
	('iismld7jj13f3rhim6r86llhn2','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462492749),
	('iqb9elg90p8tjvuvalrpmintl4','f628356cee6cf4cf5249828feed7fcb3','',1464257471),
	('is0d3afeu0gbiis9s9kq97gha7','11fe99c0460506108801bd303606546d','',1464232820),
	('j9tg918mrag6oe7bpcji1ug691','11fe99c0460506108801bd303606546d','',1463628932),
	('ja5qn6d6qcebq9c2grbulskav1','f628356cee6cf4cf5249828feed7fcb3','',1464577889),
	('jf9epuiot222mtd1k3rrs1occ2','f628356cee6cf4cf5249828feed7fcb3','',1464253961),
	('k1imegmh02cmgi24p716eca5m2','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461623798),
	('k3jmgk0lbq6jig6nbc4uc0mo12','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1464167725),
	('kcrc0j9g0r5bp88isovndu8ka7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461707099),
	('kdcskbihpeeoq735a65k2sjh32','a55db367839d193fa45c98d64047868c',X'5265646972656374546F7C733A31393A2273746F72652F696E666F2F646F636E696E6A61223B',1460940373),
	('kgidprmboe2e605b070nk0svd3','f628356cee6cf4cf5249828feed7fcb3','',1464253982),
	('klipbgd3lu7n0ri7psra0nqh04','f628356cee6cf4cf5249828feed7fcb3','',1464254055),
	('klmpjur0phoff1eur2moi6hod2','e5ecbdd3c2000e070635af69c43e4f69',X'757365725F69647C733A313A2231223B757365725F6E616D657C733A343A2264656D6F223B757365725F656D61696C7C733A31333A2264656D6F4064656D6F2E636F6D223B757365725F6163636F756E745F747970657C733A313A2237223B757365725F70726F76696465725F747970657C733A373A2244454641554C54223B757365725F6176617461725F66696C657C733A34353A22687474703A2F2F636F7572736573756974652E6672756D626572742E6F72672F617661746172732F312E6A7067223B757365725F67726176617461725F696D6167655F75726C7C733A37383A22687474703A2F2F7777772E67726176617461722E636F6D2F6176617461722F35333434346639316536393863306337636161326462633362646266393366633F733D343426643D6D6D26723D7067223B757365725F6C6F676765645F696E7C623A313B',1462163362),
	('knnehc4mu7hvvrv3ilhdnr32q3','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461518266),
	('kqv87d028qlepurs04h8617a36','f628356cee6cf4cf5249828feed7fcb3','',1464577912),
	('ks0jos2k4bo32n4usq9tg64er7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463872959),
	('kscm5mfhp3mh8tqn0lgn7gn260','11fe99c0460506108801bd303606546d','',1464240547),
	('l10blldn0dopg70mac38liodg3','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462227847),
	('l4ukojv2fu50j20l3ekbr2cdo0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1464217412),
	('lc58jattbfvlba856v34vlrba6','d152e6f07516e17ca9f45a92b3b871f3',X'757365725F69647C733A313A2231223B757365725F6E616D657C733A343A2264656D6F223B757365725F656D61696C7C733A31333A2264656D6F4064656D6F2E636F6D223B757365725F6163636F756E745F747970657C733A313A2237223B757365725F70726F76696465725F747970657C733A373A2244454641554C54223B757365725F6176617461725F66696C657C733A34353A22687474703A2F2F636F7572736573756974652E6672756D626572742E6F72672F617661746172732F312E6A7067223B757365725F67726176617461725F696D6167655F75726C7C733A38343A2268747470733A2F2F7777772E67726176617461722E636F6D2F6176617461722F35333434346639316536393863306337636161326462633362646266393366633F733D343426643D7761766174617226723D7067223B757365725F6C6F676765645F696E7C623A313B',1463719667),
	('likbh0ote3dd7a7m1r3v6mfu42','f628356cee6cf4cf5249828feed7fcb3','',1464253982),
	('lm2fht9jsj5iqnrk4q3hp8mhh2','f628356cee6cf4cf5249828feed7fcb3','',1464576971),
	('m0qbtquok1tb65887pkh9k2n26','f628356cee6cf4cf5249828feed7fcb3','',1464326238),
	('m31u6ikmlot8dje0alifresmr2','f628356cee6cf4cf5249828feed7fcb3','',1464577912),
	('menl5c7v70bc5tuj0n0nko61f1','f628356cee6cf4cf5249828feed7fcb3','',1464309047),
	('mqohatn5tgg8ind9bjrffnlc72','f628356cee6cf4cf5249828feed7fcb3','',1464326238),
	('msbnho7fqfklr4vrnsuer3qj20','bf9b586494ddb1bdc3a326036758a182',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461154981),
	('n5pgtchmdvrm0boit39pmei5k0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461655811),
	('n9e8vlchpe860cra4c8d3elsl7','11fe99c0460506108801bd303606546d','',1464233180),
	('nbr8o8psi4htfl1q74s2ikh9m4','f628356cee6cf4cf5249828feed7fcb3','',1464253961),
	('nc6ln9ld7oiiiccjrd4qtqto91','f628356cee6cf4cf5249828feed7fcb3','',1464309022),
	('nff0u0h7ch5218rodqu31gtep1','f628356cee6cf4cf5249828feed7fcb3','',1464253982),
	('ng2oevfvsb5fs7f61iq2k4bha6','f628356cee6cf4cf5249828feed7fcb3','',1464576971),
	('nvghoboundjjf706hfeifqfm21','f628356cee6cf4cf5249828feed7fcb3','',1464257471),
	('nvlj42t8fls7vvp6nsgmsfnvt3','f628356cee6cf4cf5249828feed7fcb3','',1464257471),
	('o11p212mcv4spcohmdh5inlra5','f628356cee6cf4cf5249828feed7fcb3','',1464257471),
	('o4j6do2uesd9tn33jmgdltsjd0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1464297797),
	('ot8brkv17a1tdcurdisa69tur2','f628356cee6cf4cf5249828feed7fcb3','',1464309021),
	('ouamdhkus6ggf8i8fhiqcf7772','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1464830627),
	('p9mjetngned4tk7vs6ekmu47n1','f628356cee6cf4cf5249828feed7fcb3','',1464309021),
	('pfgtghb6p5go6l8dqjrkt8n5q3','f628356cee6cf4cf5249828feed7fcb3','',1464309022),
	('pg7g362kpg5si3p89m01sv8384','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463708806),
	('pgadvl4qjgpip45nt32rj8m1q7','f628356cee6cf4cf5249828feed7fcb3','',1464253961),
	('pjk13c2fsf0t06ac62b55e9h80','e6a693f056a58709218fe12c1025495a',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1464334152),
	('pmq0mmfqu19iadaa24an6q9pk0','11fe99c0460506108801bd303606546d','',1464233308),
	('psualngk7cq63380l473q7b093','f628356cee6cf4cf5249828feed7fcb3','',1464326238),
	('q1pkghtnmr7c09t2ve4dc5i5g4','11fe99c0460506108801bd303606546d','',1464232235),
	('q5d6iani6kg4q5bafoh8u23c86','11fe99c0460506108801bd303606546d','',1464233873),
	('qf5n3klr80381qq6ck1fhapt23','f628356cee6cf4cf5249828feed7fcb3','',1464253961),
	('qm800ib655gjq5btiapjbe8th0','f628356cee6cf4cf5249828feed7fcb3','',1464253982),
	('qobv4103uq6uvmaqr6el09ju94','08233d42442d5f01028d828f3ec79158','',1464232395),
	('qocj8nbp12ohpo7uln54k5mj85','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463349404),
	('qp6dm93gstu3sidnjrnr0pahv5','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1463034821),
	('r82lipuk2kb2b8msh71181pam1','f628356cee6cf4cf5249828feed7fcb3','',1464577889),
	('reaeit1okd31cg9k3bdptan1u5','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462919477),
	('rt3g5gu70l3tsensttppmslal3','f628356cee6cf4cf5249828feed7fcb3','',1464257471),
	('s36ib1n9pfd56c2fmh9bkpljb6','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461186631),
	('s6sp8p7e1kqo5qfok0hhahghc7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1460946964),
	('s7cebvgkntoo5gveoavc99m0n0','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1463113943),
	('s7eqpgh3td56oa6juuslosenu6','6db04972a6135f93444b5eb7e6c17e69',X'5265646972656374546F7C733A31393A2273746F72652F696E666F2F646F636E696E6A61223B666565646261636B5F706F7369746976657C4E3B666565646261636B5F6E656761746976657C4E3B637372665F746F6B656E7C733A33323A223239613938386633383139656237643665653930633963373064373331383064223B637372665F746F6B656E5F74696D657C693A313436313733313733393B',1461733179),
	('shv9iuufh1rtu11l89c7gklfm1','08233d42442d5f01028d828f3ec79158','',1463964360),
	('sjtcqh9rhbsvusm31sofc7nls1','f628356cee6cf4cf5249828feed7fcb3','',1464254055),
	('slckpncc80tpqb8l14timn7bv7','11fe99c0460506108801bd303606546d','',1464232894),
	('sqdb5ccds8e2mmbt9sjl0kod36','11fe99c0460506108801bd303606546d','',1464231998),
	('t5a3jt7uiq11jfggf88ghhmo81','11fe99c0460506108801bd303606546d','',1464233759),
	('t5qegoen66ikf6fm09fvtgl860','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462611803),
	('t7gubbh3i68tb8o54fgrfgmqt2','11fe99c0460506108801bd303606546d','',1464236533),
	('t9ntov196u9phi6bvhousr7cq7','f628356cee6cf4cf5249828feed7fcb3','',1464257471),
	('t9qr9ak40fc8jefa68bshho1j0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461879968),
	('tb2dacc75m4psn4kfbvbldd236','f628356cee6cf4cf5249828feed7fcb3','',1464326238),
	('tkra6urbd09mh3tmdv28snt1o1','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463959124),
	('ts0bomgka2ufgd9idotvmb4s32','11fe99c0460506108801bd303606546d','',1464232504),
	('u5qo9mttritfon5idhp0e2et14','f628356cee6cf4cf5249828feed7fcb3','',1464309047),
	('u7gscpicfum625u1j0c9vsgle0','f628356cee6cf4cf5249828feed7fcb3','',1464577912),
	('ub5rurempirjumvv7e9oil2800','ddf16f511cfca8ecb0cd7bedac85b68b',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463967715),
	('uet1fr7g44aim0coa2p28lhk04','f628356cee6cf4cf5249828feed7fcb3','',1464577888),
	('umo71o9v2n4tvg6ohtojsjb667','f628356cee6cf4cf5249828feed7fcb3','',1464577889),
	('umpfpu4cscqdskou9ah2t7c644','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463476839),
	('unstqkg5g1v2sch59l84k3sio2','f628356cee6cf4cf5249828feed7fcb3','',1464309047),
	('uor4b8h7i6pufjqf13gb2d9214','11fe99c0460506108801bd303606546d','',1464232625),
	('v0n3bkni4176v17di64jq46861','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462839293),
	('v1pub35q9a2iegokd6gs1rcph5','f34c5800ed8bca5c940df91660d80ee3',X'637372665F746F6B656E7C733A33323A226439396563633162323437656332656337346263386566396534626366343235223B637372665F746F6B656E5F74696D657C693A313436333238383735383B',1463290198),
	('v45ulmnc25am7rnaqeodcuhfr6','f628356cee6cf4cf5249828feed7fcb3','',1464309022),
	('v9u80jv970nrnqu18vb795qu32','ddf16f511cfca8ecb0cd7bedac85b68b',X'5265646972656374546F7C733A32333A2273746F72652F696E666F2F636F757273656275696C6472223B637372665F746F6B656E7C733A33323A226130633633373131636131343933656638323564313562313433323762363933223B637372665F746F6B656E5F74696D657C693A313436343833373237353B666565646261636B5F617265617C4E3B666F726D5F646174617C613A353A7B733A393A22757365725F6E616D65223B733A363A22637261696731223B733A31373A22757365725F70617373776F72645F6E6577223B733A363A2239356F623336223B733A32303A22757365725F70617373776F72645F726570656174223B733A363A2239356F623336223B733A31303A22757365725F656D61696C223B733A32363A222063726169673140636F7572736573756974652E636F6D2E6175223B733A31373A22757365725F656D61696C5F726570656174223B733A32363A222063726169673140636F7572736573756974652E636F6D2E6175223B7D666565646261636B5F6E656761746976657C4E3B666565646261636B5F706F7369746976657C4E3B',1464839557),
	('vnilj2ggtlnca6b9frrb2ua4d2','a55db367839d193fa45c98d64047868c','',1460942047),
	('vp6t7uvvnhc4o8mfbe86u0uhh6','a55db367839d193fa45c98d64047868c',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462424381),
	('vuum84p6fhse6tt9dpq4i3t5o4','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463571355);

/*!40000 ALTER TABLE `session_data` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table store_section_apps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `store_section_apps`;

CREATE TABLE `store_section_apps` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `section` int(11) NOT NULL,
  `app` int(11) NOT NULL,
  `sort` int(11) NOT NULL DEFAULT '999',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `store_section_apps` WRITE;
/*!40000 ALTER TABLE `store_section_apps` DISABLE KEYS */;

INSERT INTO `store_section_apps` (`id`, `section`, `app`, `sort`)
VALUES
	(1,1,1,2),
	(2,1,2,1),
	(3,1,3,3),
	(4,2,4,1),
	(5,2,5,2),
	(6,2,6,3),
	(7,3,7,4),
	(8,2,8,4);

/*!40000 ALTER TABLE `store_section_apps` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table store_sections
# ------------------------------------------------------------

DROP TABLE IF EXISTS `store_sections`;

CREATE TABLE `store_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(30) DEFAULT NULL,
  `epiphet` varchar(100) DEFAULT NULL,
  `cssclass` varchar(20) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT '1',
  `sort` int(11) NOT NULL DEFAULT '999',
  `html_pre` text,
  `html_post` text,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `store_sections` WRITE;
/*!40000 ALTER TABLE `store_sections` DISABLE KEYS */;

INSERT INTO `store_sections` (`id`, `label`, `epiphet`, `cssclass`, `visible`, `sort`, `html_pre`, `html_post`)
VALUES
	(1,'Rapid SCORM content authoring.','Access all apps with one subscription','section scorm',1,1,'<p>Subscribing will give you access to <i>all</i> the apps listed below; payment tiers let you <a href=\"/store/tiers/coursesuite\">choose the features you need</a>. Click on the tiles below for more information about each app. As we add new apps, you get them as part of your existing subscription.</p>\r\n<div class=\"cs-bracket-design top\"></div>','<div class=\"cs-bracket-design bottom\"></div>\r\n<p><small><sup>*</sup>Apps are web based and require a desktop browser to run such as Chrome, Safari, Vivaldi or Firefox; other browsers or platforms may work but are not supported.</small></p>'),
	(2,'','Open source software for Moodle sites.','section opensource',1,2,'<p>Extend your Moodle site with our open source plugins. Click the launch links below to be taken to demo pages for each of the plugins, then from there to download the source.</p>',''),
	(3,'SCORM Course Packages','','section ourcourses',0,999,'<p>You can buy our online courses here. You want to buy them because they are better than yours. Something something, I made this up. Stop reading now.</p>','');

/*!40000 ALTER TABLE `store_sections` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table subscriptions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subscriptions`;

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `tier_id` int(11) NOT NULL,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `endDate` date DEFAULT NULL,
  `referenceId` blob,
  `subscriptionUrl` blob,
  `status` varchar(20) DEFAULT NULL,
  `statusReason` varchar(20) DEFAULT NULL,
  `testMode` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;

INSERT INTO `subscriptions` (`subscription_id`, `user_id`, `tier_id`, `added`, `endDate`, `referenceId`, `subscriptionUrl`, `status`, `statusReason`, `testMode`, `active`)
VALUES
	(1,11,2,'2016-05-27 14:24:29',NULL,X'4D545A6D596D526A5A6A52694E6D55315A4749324E446B784E546B7A4D444A684D3245794F546C684F5759334F54646C4F446B784F5464694D6A646A4D3251304D4755784E445A6A5A4451315A4455774D4445324D756F753351624F7373334F4D5052582D65794A476A676A53547949636E6570617741395566522D4F5F4C6D4472593265446E327272784D505974716454776F30512C2C',X'4E7A646B596A45324D4455355A6A566B4F444E6B595451354D6A526B4D3246684D574E6D4E4751324F5756684D7A59315A44466A4D6A5A6C4E544D304E6D45314E4759344E7A6B7859545A6C4D4745324D5459334E66663547457A71457342557A4D32566B304F6A655A73396873743367776B4545324E484F6E3551376B4E7A4E447631575F437937306A476E4679466A49645537355A387867545F496843354E5276756249545047336F782D6B635F496B41316C614652574F48696F2D325F46385F766C304A52506351654F6550416F57377657512C2C','active','',1,1);

/*!40000 ALTER TABLE `subscriptions` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table systasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `systasks`;

CREATE TABLE `systasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `task` varchar(25) NOT NULL DEFAULT '',
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `lastrun` int(11) NOT NULL DEFAULT '0',
  `running` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `systasks` WRITE;
/*!40000 ALTER TABLE `systasks` DISABLE KEYS */;

INSERT INTO `systasks` (`id`, `task`, `added`, `lastrun`, `running`)
VALUES
	(1,'validateSubscriptions','2016-04-14 15:02:36',0,0);

/*!40000 ALTER TABLE `systasks` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tier_packs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tier_packs`;

CREATE TABLE `tier_packs` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL DEFAULT '',
  `kind` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tier_packs` WRITE;
/*!40000 ALTER TABLE `tier_packs` DISABLE KEYS */;

INSERT INTO `tier_packs` (`id`, `name`, `kind`)
VALUES
	(1,'coursesuite',0),
	(2,'packages',0);

/*!40000 ALTER TABLE `tier_packs` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table tiers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tiers`;

CREATE TABLE `tiers` (
  `tier_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tier_level` int(11) unsigned NOT NULL DEFAULT '0',
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `store_url` varchar(255) DEFAULT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '0',
  `price` int(11) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `period` varchar(10) DEFAULT NULL,
  `pack_id` int(11) DEFAULT NULL,
  PRIMARY KEY (`tier_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tiers` WRITE;
/*!40000 ALTER TABLE `tiers` DISABLE KEYS */;

INSERT INTO `tiers` (`tier_id`, `tier_level`, `name`, `description`, `added`, `store_url`, `active`, `price`, `currency`, `period`, `pack_id`)
VALUES
	(1,0,'Copper',NULL,'2016-05-30 11:02:36','http://sites.fastspring.com/coursesuite/product/copper-tier',1,99,'USD','m',1),
	(2,1,'Jade','Most popular!','2016-05-30 11:02:23','http://sites.fastspring.com/coursesuite/product/jade-tier ',1,169,'USD','m',1),
	(3,2,'Crystal',NULL,'2016-05-30 11:02:43','http://sites.fastspring.com/coursesuite/product/crystal-tier ',1,1900,'USD','y',1),
	(4,0,'Anorak',NULL,'2016-05-27 11:02:05',NULL,1,NULL,NULL,NULL,2),
	(5,1,'Parzival',NULL,'2016-05-27 11:02:06',NULL,1,NULL,NULL,NULL,2),
	(6,2,'Artemis',NULL,'2016-05-27 11:02:07',NULL,1,NULL,NULL,NULL,2);

/*!40000 ALTER TABLE `tiers` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `session_id` varchar(48) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'stores session cookie id to prevent session concurrency',
  `user_name` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(64) COLLATE utf8_unicode_ci NOT NULL COMMENT 'user''s email, unique',
  `user_active` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s activation status',
  `user_deleted` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s deletion status',
  `user_account_type` tinyint(1) NOT NULL DEFAULT '1' COMMENT 'user''s account type (basic, premium, etc)',
  `user_has_avatar` tinyint(1) NOT NULL DEFAULT '0' COMMENT '1 if user has a local avatar, 0 if not',
  `user_remember_me_token` varchar(64) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s remember-me cookie token',
  `user_creation_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the creation of user''s account',
  `user_suspension_timestamp` bigint(20) DEFAULT NULL COMMENT 'Timestamp till the end of a user suspension',
  `user_last_login_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of user''s last login',
  `user_failed_logins` tinyint(1) NOT NULL DEFAULT '0' COMMENT 'user''s failed login attempts',
  `user_last_failed_login` int(10) DEFAULT NULL COMMENT 'unix timestamp of last failed login attempt',
  `user_activation_hash` varchar(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s email verification hash string',
  `user_password_reset_hash` char(40) COLLATE utf8_unicode_ci DEFAULT NULL COMMENT 'user''s password reset code',
  `user_password_reset_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the password reset request',
  `user_provider_type` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_name` (`user_name`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='user data';

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;

INSERT INTO `users` (`user_id`, `session_id`, `user_name`, `user_password_hash`, `user_email`, `user_active`, `user_deleted`, `user_account_type`, `user_has_avatar`, `user_remember_me_token`, `user_creation_timestamp`, `user_suspension_timestamp`, `user_last_login_timestamp`, `user_failed_logins`, `user_last_failed_login`, `user_activation_hash`, `user_password_reset_hash`, `user_password_reset_timestamp`, `user_provider_type`)
VALUES
	(1,NULL,'demo','$2y$10$OvprunjvKOOhM1h9bzMPs.vuwGIsOqZbw88rzSyGCTJTcE61g5WXi','demo@demo.com',1,0,7,1,NULL,1422205178,NULL,1464572772,0,NULL,NULL,'1622283e3ff82568b3f60d51c6bcf35a35b679ec',1453943586,'DEFAULT'),
	(2,NULL,'demo2','$2y$10$OvprunjvKOOhM1h9bzMPs.vuwGIsOqZbw88rzSyGCTJTcE61g5WXi','demo2@demo.com',1,0,1,0,NULL,1422205178,NULL,1454738793,0,NULL,NULL,NULL,NULL,'DEFAULT'),
	(3,NULL,'frumbert','$2y$10$KvmVg2B83jl6OgDDKuQKleuk4Ij77XbR3X5Qs5WVESInqN6O.DTr.','tim@avide.com.au',1,0,2,1,NULL,1454886223,NULL,1462159133,0,NULL,NULL,NULL,NULL,'DEFAULT'),
	(4,NULL,'tim','$2y$10$DwqleDfsb3U.Fn2zRFQq8OPMixWsIjgeZS9XifhzIJPck9MsZ9Kg2','tim@coursesuite.com.au',0,0,1,0,NULL,1462158701,NULL,NULL,0,NULL,'5c1d14bf9270189f80329dd6ff1e938aaf107add',NULL,NULL,'DEFAULT'),
	(5,NULL,'Craig','$2y$10$MNBQvaxRXxdGCfLkBWq4de6dZMzAoc113BZQ.SeRdXkUWa3HHHrHy','craig@coursesuite.com.au',0,0,1,0,NULL,1463114713,NULL,NULL,2,1464837219,'4a57241dcf9adc6219efc418c09ae9bcd313532a',NULL,NULL,'DEFAULT'),
	(11,NULL,'qwerty','$2y$10$Rc6cjedEHnUnxBg5dd5jTuDtYCDyGsQV5HIfoYuz60iE5T2owVXF.','test1@coursesuite.com.au',1,0,1,0,NULL,1463982665,NULL,1464837263,0,NULL,NULL,NULL,NULL,'DEFAULT'),
	(13,'kgtvprsvsogs2cnpa344m9h8q7','Craig1','$2y$10$0cAYwrK7bXaRU2GAALxucexQcCmRlrKTEnhhmEH0pvbCRUC0B3gNq','craig1@coursesuite.com.au',1,0,1,0,NULL,1464326230,NULL,1464326678,0,NULL,NULL,NULL,NULL,'DEFAULT');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
