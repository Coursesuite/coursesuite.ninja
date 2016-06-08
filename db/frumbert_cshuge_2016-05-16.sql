# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: frumbert.org (MySQL 5.6.26-cll-lve)
# Database: frumbert_cshuge
# Generation Time: 2016-05-15 23:45:28 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table app_tier_feature
# ------------------------------------------------------------

DROP TABLE IF EXISTS `app_tier_feature`;

CREATE TABLE `app_tier_feature` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app` int(11) NOT NULL,
  `level` int(11) NOT NULL,
  `feature` varchar(100) DEFAULT NULL,
  `details` text,
  `match_label` varchar(50) DEFAULT NULL,
  `mismatch_label` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `app_tier_feature` WRITE;
/*!40000 ALTER TABLE `app_tier_feature` DISABLE KEYS */;

INSERT INTO `app_tier_feature` (`id`, `app`, `level`, `feature`, `details`, `match_label`, `mismatch_label`)
VALUES
	(1,1,1,'Branding',NULL,'Removable','Fixed'),
	(2,1,2,'Layouts',NULL,'Customisable','Fixed'),
	(3,1,1,'Dropbox Integrated',NULL,'Load & Save','No'),
	(4,1,-1,'Scorm',NULL,' Supported','Missing'),
	(5,1,2,'API access',NULL,'Single Sign On','No'),
	(7,2,1,'Branding',NULL,'Removable','Fixed'),
	(8,2,1,'Dropbox Integrated',NULL,'Yes','No'),
	(9,2,-1,'Scorm',NULL,'Supported','Missing'),
	(10,2,2,'API access',NULL,'Single Sign On','No'),
	(11,2,2,'Embedded video',NULL,'HTML5 video types','No');

/*!40000 ALTER TABLE `app_tier_feature` ENABLE KEYS */;
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
	(1,'','','2016-02-25 20:01:06','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(2,'','','2016-02-25 20:01:51','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(3,'','','2016-02-25 20:12:05','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(4,'','','2016-02-25 20:12:48','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(5,'','','2016-02-25 21:00:51','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(6,'','','2016-02-25 21:01:54','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(7,'ApiController::generateApiKey','tokenuser','2016-02-25 21:13:13','','docninja','bobdown','Array',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(8,'ApiController::generateApiKey','tokenuser','2016-02-25 21:14:13','','docninja','bobdown','a:1:{s:5:\"token\";s:162:\"MjE3ZDJjODExZjAxMjY2NTMwNjM1YTU3MDY2YjEwYzA3MzFjYjhhYjcwY2MwMjZlMTgxNjRmMTM0MDQwZmUzMQFEupPPb9%2BSMtwmNETrd1OawPLfNG%2BmeEaUwtM%2BR52EdZd54MSdikF6BNFYNoW4jQ%3D%3D\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(9,'ApiController::generateApiKey','tokenuser','2016-02-25 21:15:10','','clinicians+academy','docninja','a:1:{s:5:\"token\";s:180:\"ZjRjNTllZjVkOWMxNzlkNGViOTQ2ZmUxNWY2MzRjY2NiMGIyMWY0ZDk2ZTk4MDBmOTNmM2U4YmJjZGRiNjRlYp%2Fm9QL40BhxVQsdwzDDUBcfJ%2FI1XME6DJC4VFUFpeeLqxJWf%2FhNTQ3P53tR8hz4wEmtYATywEcuj3LVHlpswIw%3D\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(10,'ApiController::generateApiKey','tokenuser','2016-02-25 21:15:23','','clinicians+academy','docninja','a:1:{s:5:\"token\";s:178:\"YjhkZGRmNjRhNmY5ZjVmMDBkYjg3YjQ1MzNmOTU5NjMxOTA0ZWYwYjhhNjE3MjY1N2M1MjczMjkzMGU5YTAyMFHzYZpKmnDpnAXA2an7YuRt1Ak3lPQo6hFIFlTxdnqp5gmyjiJomVDFYJXjORR8wD6lW%2FVnWrFymibr%2FdZnQzA%3D\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(11,'ApiController::__construct',NULL,'2016-02-25 21:25:47','','authentication','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(12,'ApiController::__construct',NULL,'2016-02-25 21:27:00','','authentication','username=\"tokenuser\", realm=\"CourseSuite\", nonce=\"156ced5fdeb932\", uri=\"/api/generateApiKey/docninja/bupa\", response=\"128576a406b4ba7841e2598e47dbbfea\", opaque=\"9c584a42cba9534c95474a91d5799046\", qop=auth, nc=00000003, cnonce=\"5d052d3c63426e71\"',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(13,'ApiController::__construct',NULL,'2016-02-25 21:28:25','','authentication',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(14,'ApiController::__construct',NULL,'2016-02-25 21:28:25','','authentication','username=\"tokenuser\", realm=\"CourseSuite\", nonce=\"156ced749b9920\", uri=\"/api/generateApiKey/clinicianacad\", response=\"b3ca0d5731aeeace83bc607c78b87554\", opaque=\"9c584a42cba9534c95474a91d5799046\", qop=auth, nc=00000001, cnonce=\"cd56b60ae4650e43\"',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(15,'ApiController::generateApiKey','tokenuser','2016-02-25 21:28:37','','docninja','bupa','a:1:{s:5:\"token\";s:168:\"NGU4NWIxNGEwODFlYTk2OTI5ODYzYmI2YTQ1MzEzYmVkNWRlNjEyNjc0ZmI2NTlmYWYxYzIyYzQzNTYwZTY2ZACkMMgkQ8L61KdUg3B2kInL86sL8Ujl%2BT2b9Ii%2BMZs4%2FJPiUeSrl2%2B5Nx%2FGbiPF%2Bw%3D%3D\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(16,'ApiController::__construct',NULL,'2016-02-25 21:28:49','','authentication',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(17,'ApiController::generateApiKey','tokenuser','2016-02-25 21:28:49','','clinicians+academy','','a:1:{s:5:\"token\";s:162:\"ODY3ODA5YWQyOGE2ODg4MDM3M2ZhYjljMzk5NmViMDgwMTVkZTk5ODdmODE0OGI4MjMyNjM0Mjc1NTgzODZlZS%2BvSnQ25oVv2sxT%2FoVYaOa6Y9mppfjsK2eG%2FPUk6Wzi4MdSGmTAkruqnjBOpufLfQ%3D%3D\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(18,'ApiController::__construct',NULL,'2016-02-25 21:40:50','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(19,'ApiController::generateApiKey','tokenuser','2016-02-25 21:48:58','','avide','docninja','a:1:{s:5:\"token\";s:162:\"YjI2OWMzYjU3ZmRiMzRjODAzNjUyOTk0YTc0ZTIxYjQ2NzU1MDFkNzQzZDU2OTY4NDljZTgwN2VlNThmZTUyMzcFdeWlBoSS%2BV9xdBHG0Axks%2BSq1mOzkZsxKv7hHQHQr2xVl3vrdpTm%2BiuCmW9dKA%3D%3D\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(20,'ApiController::__construct',NULL,'2016-02-26 13:31:12','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(21,'ApiController::__construct',NULL,'2016-02-29 20:49:52','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(22,'ApiController::__construct',NULL,'2016-03-01 11:31:48','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(23,'ApiController::__construct',NULL,'2016-03-01 11:31:48','','authentication','failed','username=\"USERNAME\", realm=\"CourseSuite\", nonce=\"156d4e2f47dd07\", uri=\"/api/verifyToken/docninja/1\", cnonce=\"NjU1MjVkMGIxMGEyODczYTg0NGQ0NTY5NDVkMWI0Njg=\", nc=00000001, qop=auth, response=\"326ddd690a665635200b5edacb42bbfa\", opaque=\"9c584a42cba9534c95474a91d5799046\"',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(24,'ApiController::__construct',NULL,'2016-03-01 11:32:08','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(25,'ApiController::__construct',NULL,'2016-03-01 11:33:09','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(26,'ApiController::__construct',NULL,'2016-03-01 11:34:48','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(27,'ApiController::verifyToken','tokenuser','2016-03-01 11:34:48','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(28,'ApiController::__construct',NULL,'2016-03-01 11:35:04','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(29,'ApiController::verifyToken','tokenuser','2016-03-01 11:35:04','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(30,'ApiController::__construct',NULL,'2016-03-01 11:35:25','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(31,'ApiController::verifyToken','tokenuser','2016-03-01 11:35:25','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(32,'ApiController::__construct',NULL,'2016-03-01 11:36:03','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(33,'ApiController::verifyToken','tokenuser','2016-03-01 11:36:03','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(34,'ApiController::__construct',NULL,'2016-03-01 11:37:01','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(35,'ApiController::verifyToken','tokenuser','2016-03-01 11:37:01','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(36,'ApiController::__construct',NULL,'2016-03-01 11:37:19','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(37,'ApiController::verifyToken','tokenuser','2016-03-01 11:37:19','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(38,'ApiController::__construct',NULL,'2016-03-01 11:37:51','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(39,'ApiController::verifyToken','tokenuser','2016-03-01 11:37:51','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(40,'ApiController::__construct',NULL,'2016-03-01 11:39:19','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(41,'ApiController::verifyToken','tokenuser','2016-03-01 11:39:19','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(42,'ApiController::__construct',NULL,'2016-03-01 11:39:49','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(43,'ApiController::verifyToken','tokenuser','2016-03-01 11:39:49','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(44,'ApiController::__construct',NULL,'2016-03-01 11:40:39','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(45,'ApiController::verifyToken','tokenuser','2016-03-01 11:40:39','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(46,'ApiController::__construct',NULL,'2016-03-01 11:40:58','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(47,'ApiController::verifyToken','tokenuser','2016-03-01 11:40:58','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(48,'ApiController::__construct',NULL,'2016-03-01 12:16:55','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(49,'ApiController::verifyToken','tokenuser','2016-03-01 12:16:55','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(50,'ApiController::__construct',NULL,'2016-03-01 12:17:29','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(51,'ApiController::verifyToken','tokenuser','2016-03-01 12:17:29','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(52,'ApiController::__construct',NULL,'2016-03-01 12:18:12','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(53,'ApiController::verifyToken','tokenuser','2016-03-01 12:18:12','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(54,'ApiController::__construct',NULL,'2016-03-01 12:21:16','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(55,'ApiController::verifyToken','tokenuser','2016-03-01 12:21:16','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(56,'ApiController::__construct',NULL,'2016-03-01 12:25:32','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(57,'ApiController::verifyToken','tokenuser','2016-03-01 12:25:32','','docninja','MDE3NjgwZjFhYzcxMmU4MGFlN2ExMjVhYThhMWUxMzZmNjMwNWM1NmFkZGRjZTgxMWI2ODk1MDA3ZmZjMWQ1YXLu0Xbqo3McRxEuVqa8N1VfgDREPIJwj3gEH1xwICWQzJGuDMCkbhE85mD','','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(58,'ApiController::__construct',NULL,'2016-03-01 12:25:40','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(59,'ApiController::__construct',NULL,'2016-03-01 12:26:09','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(60,'ApiController::__construct',NULL,'2016-03-01 12:29:58','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(61,'ApiController::__construct',NULL,'2016-03-01 12:33:24','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(62,'ApiController::__construct',NULL,'2016-03-01 12:34:37','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(63,'ApiController::__construct',NULL,'2016-03-01 12:50:13','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(64,'ApiController::__construct',NULL,'2016-03-01 12:50:19','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(65,'ApiController::__construct',NULL,'2016-03-01 12:52:00','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(66,'ApiController::__construct',NULL,'2016-03-01 18:39:19','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(67,'ApiController::generateApiKey','tokenuser','2016-03-01 18:39:19','','CourseSuite','','a:1:{s:5:\"token\";s:224:\"35326431633463633534303130353337643565333837396433663663363031376136383133663939303861646165373466353964633338616335363535623863ef05e02de356b6205b259f75b54c3fc50d42070254ff17d13411afbdcb6532b43e1072c06b644bc1c1e21f25a9fbd777\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(68,'ApiController::__construct',NULL,'2016-03-01 18:40:38','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(69,'ApiController::__construct',NULL,'2016-03-01 18:41:47','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(70,'ApiController::verifyApiKey','tokenuser','2016-03-01 18:41:47','',NULL,NULL,'a:1:{s:5:\"valid\";N;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(71,'ApiController::__construct',NULL,'2016-03-01 18:42:34','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(72,'ApiController::verifyApiKey','tokenuser','2016-03-01 18:42:34','',NULL,NULL,'a:1:{s:5:\"valid\";N;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(73,'ApiController::__construct',NULL,'2016-03-01 18:42:40','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(74,'ApiController::verifyApiKey','tokenuser','2016-03-01 18:42:40','',NULL,NULL,'a:1:{s:5:\"valid\";N;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(75,'ApiController::__construct',NULL,'2016-03-01 18:43:11','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(76,'ApiController::verifyApiKey','tokenuser','2016-03-01 18:43:11','',NULL,NULL,'a:1:{s:5:\"valid\";N;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(77,'ApiController::__construct',NULL,'2016-03-01 18:43:55','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(78,'ApiController::verifyApiKey','tokenuser','2016-03-01 18:43:55','',NULL,NULL,'a:1:{s:5:\"valid\";N;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(79,'ApiController::__construct',NULL,'2016-03-01 18:44:20','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(80,'ApiController::verifyApiKey','tokenuser','2016-03-01 18:44:20','','CourseSuite','','a:1:{s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(81,'ApiController::__construct',NULL,'2016-03-01 19:05:12','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(82,'ApiController::verifyApiKey','tokenuser','2016-03-01 19:05:12','','CourseSuite','','a:1:{s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(83,'ApiController::__construct',NULL,'2016-03-01 19:08:56','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(84,'ApiController::verifyToken','tokenuser','2016-03-01 19:08:56','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(85,'ApiController::__construct',NULL,'2016-03-01 19:08:56','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(86,'ApiController::verifyToken','tokenuser','2016-03-01 19:08:56','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(87,'ApiController::__construct',NULL,'2016-03-01 19:10:57','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(88,'ApiController::verifyToken','tokenuser','2016-03-01 19:10:57','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(89,'ApiController::__construct',NULL,'2016-03-01 19:11:47','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(90,'ApiController::verifyToken','tokenuser','2016-03-01 19:11:47','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(91,'ApiController::__construct',NULL,'2016-03-01 19:12:00','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(92,'ApiController::verifyToken','tokenuser','2016-03-01 19:12:00','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(93,'ApiController::__construct',NULL,'2016-03-01 19:15:11','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(94,'ApiController::verifyToken','tokenuser','2016-03-01 19:15:11','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(95,'ApiController::__construct',NULL,'2016-03-01 19:16:46','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(96,'ApiController::verifyToken','tokenuser','2016-03-01 19:16:46','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(97,'ApiController::__construct',NULL,'2016-03-01 19:34:48','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(98,'ApiController::verifyToken','tokenuser','2016-03-01 19:34:48','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(99,'ApiController::__construct',NULL,'2016-03-01 19:35:13','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(100,'ApiController::verifyToken','tokenuser','2016-03-01 19:35:13','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(101,'ApiController::__construct',NULL,'2016-03-01 19:40:21','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(102,'ApiController::verifyToken','tokenuser','2016-03-01 19:40:21','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(103,'ApiController::__construct',NULL,'2016-03-01 19:40:42','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(104,'ApiController::verifyToken','tokenuser','2016-03-01 19:40:42','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(105,'ApiController::__construct',NULL,'2016-03-01 19:41:37','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(106,'ApiController::verifyToken','tokenuser','2016-03-01 19:41:37','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(107,'ApiController::__construct',NULL,'2016-03-01 19:41:59','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(108,'ApiController::verifyToken','tokenuser','2016-03-01 19:41:59','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(109,'ApiController::__construct',NULL,'2016-03-01 19:42:33','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(110,'ApiController::verifyToken','tokenuser','2016-03-01 19:42:33','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(111,'ApiController::__construct',NULL,'2016-03-01 19:42:47','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(112,'ApiController::verifyToken','tokenuser','2016-03-01 19:42:47','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(113,'ApiController::__construct',NULL,'2016-03-01 19:43:04','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(114,'ApiController::verifyToken','tokenuser','2016-03-01 19:43:04','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(115,'ApiController::__construct',NULL,'2016-03-01 19:43:57','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(116,'ApiController::verifyToken','tokenuser','2016-03-01 19:43:57','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(117,'ApiController::__construct',NULL,'2016-03-01 19:46:02','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(118,'ApiController::verifyToken','tokenuser','2016-03-01 19:46:02','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(119,'ApiController::__construct',NULL,'2016-03-01 19:50:44','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(120,'ApiController::verifyToken','tokenuser','2016-03-01 19:50:44','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(121,'ApiController::__construct',NULL,'2016-03-01 19:53:31','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(122,'ApiController::verifyToken','tokenuser','2016-03-01 19:53:31','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(123,'ApiController::__construct',NULL,'2016-03-01 19:53:40','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(124,'ApiController::verifyApiKey','tokenuser','2016-03-01 19:53:40','','7abo12ubrbp0ugm931ifet1ls0','','a:1:{s:5:\"valid\";b:0;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(125,'ApiController::__construct',NULL,'2016-03-01 19:54:08','','authentication','failed',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(126,'ApiController::verifyToken','tokenuser','2016-03-01 19:54:08','','docninja','63343761653733623935373634623136303963373930393633333061393634623733343038656466323631326636656365316431366262336562616664313864ce52ceda5a463bb1e478183c32288343412642a9d0752a52ae79644fbea232a8044d3ed93558e0faf4069ee2347cf681','1','a:3:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(127,'ApiController::generateApiKey','tokenuser','2016-03-07 16:52:55','','gavins+thingo','','a:1:{s:5:\"token\";s:224:\"303735663231666635376335373162383839623237316662633638656161326335323135613539323837336133386161623038396563366261373364373933349dd5357373c0475f2e48a8bd5d9e57278d88a27a03cc69a60be25221288a3426428f01492bd5eb5bdae572e99642863f\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(128,'ApiController::verifyApiKey','tokenuser','2016-03-07 16:53:22','','gavins+thingo','','a:1:{s:5:\"valid\";b:1;}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(129,'ApiController::generateApiKey','tokenuser','2016-03-07 16:56:38','','gavins+thingo','','a:1:{s:5:\"token\";s:224:\"61336135636665363639343934366366336331636637663137366431313266633435623839646536633562363434306338623864633039626462643563386530052d6131568443f8c785e553a8f857bd6df8b1c1ffce8669a70185e801cb6fc19c3a39288023450b1cf0f533b857ffa6\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(130,'ApiController::verifyApiKey','tokenuser','2016-03-10 16:48:44','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(131,'ApiController::verifyApiKey','tokenuser','2016-03-10 16:48:44','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(132,'ApiController::verifyApiKey','tokenuser','2016-03-10 16:49:51','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(133,'ApiController::verifyApiKey','tokenuser','2016-03-10 16:49:51','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(134,'ApiController::verifyApiKey','tokenuser','2016-03-10 16:49:51','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(135,'ApiController::verifyApiKey','tokenuser','2016-03-10 16:49:51','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(136,'ApiController::verifyToken','tokenuser','2016-03-10 17:06:29','','docninja','366561633634323565316366313937666430386664623638633166313735333566336537663335343139656534623934306435626134323661633533323634391c962a79df643a5b2c6cbfc1eecb86da61fc91156e55498049c89444bc873f0647613d76d758cf5e3bdc00ef9cf8f37a','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(137,'ApiController::verifyToken','tokenuser','2016-03-10 17:06:43','','docninja','366561633634323565316366313937666430386664623638633166313735333566336537663335343139656534623934306435626134323661633533323634391c962a79df643a5b2c6cbfc1eecb86da61fc91156e55498049c89444bc873f0647613d76d758cf5e3bdc00ef9cf8f37a','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(138,'ApiController::verifyToken','tokenuser','2016-03-10 17:06:43','','docninja','366561633634323565316366313937666430386664623638633166313735333566336537663335343139656534623934306435626134323661633533323634391c962a79df643a5b2c6cbfc1eecb86da61fc91156e55498049c89444bc873f0647613d76d758cf5e3bdc00ef9cf8f37a','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(139,'ApiController::verifyToken','tokenuser','2016-03-10 17:06:51','','docninja','366561633634323565316366313937666430386664623638633166313735333566336537663335343139656534623934306435626134323661633533323634391c962a79df643a5b2c6cbfc1eecb86da61fc91156e55498049c89444bc873f0647613d76d758cf5e3bdc00ef9cf8f37a','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(140,'ApiController::verifyToken','tokenuser','2016-03-10 17:06:51','','docninja','366561633634323565316366313937666430386664623638633166313735333566336537663335343139656534623934306435626134323661633533323634391c962a79df643a5b2c6cbfc1eecb86da61fc91156e55498049c89444bc873f0647613d76d758cf5e3bdc00ef9cf8f37a','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(141,'ApiController::verifyToken','tokenuser','2016-03-10 17:06:51','','docninja','366561633634323565316366313937666430386664623638633166313735333566336537663335343139656534623934306435626134323661633533323634391c962a79df643a5b2c6cbfc1eecb86da61fc91156e55498049c89444bc873f0647613d76d758cf5e3bdc00ef9cf8f37a','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(142,'ApiController::verifyToken','tokenuser','2016-03-10 17:06:51','','docninja','366561633634323565316366313937666430386664623638633166313735333566336537663335343139656534623934306435626134323661633533323634391c962a79df643a5b2c6cbfc1eecb86da61fc91156e55498049c89444bc873f0647613d76d758cf5e3bdc00ef9cf8f37a','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(143,'ApiController::verifyToken','tokenuser','2016-03-10 17:07:05','','docninja','38666433383365636433303736633338356366373864303535343764366264613864353036376166393261663534383531366136333632383663326439643065eb00781bf46a5b8d15c103f3f0684f9280c9bbd2bed50739c937ea74a5769c72a86af41edc0adc71c574050c35d7e8eb','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(144,'ApiController::verifyToken','tokenuser','2016-03-10 17:07:05','','docninja','38666433383365636433303736633338356366373864303535343764366264613864353036376166393261663534383531366136333632383663326439643065eb00781bf46a5b8d15c103f3f0684f9280c9bbd2bed50739c937ea74a5769c72a86af41edc0adc71c574050c35d7e8eb','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(145,'ApiController::verifyToken','tokenuser','2016-03-10 17:07:05','','docninja','38666433383365636433303736633338356366373864303535343764366264613864353036376166393261663534383531366136333632383663326439643065eb00781bf46a5b8d15c103f3f0684f9280c9bbd2bed50739c937ea74a5769c72a86af41edc0adc71c574050c35d7e8eb','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(146,'ApiController::verifyToken','tokenuser','2016-03-10 17:07:05','','docninja','38666433383365636433303736633338356366373864303535343764366264613864353036376166393261663534383531366136333632383663326439643065eb00781bf46a5b8d15c103f3f0684f9280c9bbd2bed50739c937ea74a5769c72a86af41edc0adc71c574050c35d7e8eb','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(147,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:07:13','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(148,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:07:13','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(149,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:07:13','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(150,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:07:13','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(151,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:20','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(152,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:20','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(153,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:20','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(154,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:20','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(155,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:28','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(156,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:28','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(157,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:28','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(158,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:28','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(159,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:55','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(160,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:55','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(161,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:55','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(162,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:08:55','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(163,'ApiController::verifyToken','tokenuser','2016-03-10 17:08:58','','docninja','38666433383365636433303736633338356366373864303535343764366264613864353036376166393261663534383531366136333632383663326439643065eb00781bf46a5b8d15c103f3f0684f9280c9bbd2bed50739c937ea74a5769c72a86af41edc0adc71c574050c35d7e8eb','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(164,'ApiController::verifyToken','tokenuser','2016-03-10 17:08:58','','docninja','38666433383365636433303736633338356366373864303535343764366264613864353036376166393261663534383531366136333632383663326439643065eb00781bf46a5b8d15c103f3f0684f9280c9bbd2bed50739c937ea74a5769c72a86af41edc0adc71c574050c35d7e8eb','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(165,'ApiController::verifyToken','tokenuser','2016-03-10 17:08:58','','docninja','38666433383365636433303736633338356366373864303535343764366264613864353036376166393261663534383531366136333632383663326439643065eb00781bf46a5b8d15c103f3f0684f9280c9bbd2bed50739c937ea74a5769c72a86af41edc0adc71c574050c35d7e8eb','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(166,'ApiController::verifyToken','tokenuser','2016-03-10 17:08:58','','docninja','38666433383365636433303736633338356366373864303535343764366264613864353036376166393261663534383531366136333632383663326439643065eb00781bf46a5b8d15c103f3f0684f9280c9bbd2bed50739c937ea74a5769c72a86af41edc0adc71c574050c35d7e8eb','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(167,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:09:06','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(168,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:09:06','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(169,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:09:06','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(170,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:09:06','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(171,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:11:08','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(172,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:11:08','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(173,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:11:08','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(174,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:11:08','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(175,'ApiController::verifyApiKey','tokenuser','2016-03-10 17:11:08','','avide','docninja','a:6:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:1;s:4:\"tier\";i:4;s:3:\"org\";s:5:\"avide\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(176,'ApiController::verifyToken','tokenuser','2016-03-21 11:18:40','','docninja','63343234653935396533666339636135363164303134336330353164623761316538393461396230616561613131333336343530656637646135646265386532184699dd7d46d5b64315b4c3622ada0e8d736950dbe3de06e2d406a5cc405e7ad480d1b646159d24e2564074d6e99edc','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(177,'ApiController::verifyToken','tokenuser','2016-03-21 11:18:40','','docninja','63343234653935396533666339636135363164303134336330353164623761316538393461396230616561613131333336343530656637646135646265386532184699dd7d46d5b64315b4c3622ada0e8d736950dbe3de06e2d406a5cc405e7ad480d1b646159d24e2564074d6e99edc','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(178,'ApiController::verifyToken','tokenuser','2016-03-21 11:18:40','','docninja','63343234653935396533666339636135363164303134336330353164623761316538393461396230616561613131333336343530656637646135646265386532184699dd7d46d5b64315b4c3622ada0e8d736950dbe3de06e2d406a5cc405e7ad480d1b646159d24e2564074d6e99edc','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(179,'ApiController::verifyToken','tokenuser','2016-03-21 11:18:40','','docninja','63343234653935396533666339636135363164303134336330353164623761316538393461396230616561613131333336343530656637646135646265386532184699dd7d46d5b64315b4c3622ada0e8d736950dbe3de06e2d406a5cc405e7ad480d1b646159d24e2564074d6e99edc','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"2\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(180,'','','2016-04-07 15:13:30','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(181,'','','2016-04-07 15:13:52','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(182,'','','2016-04-07 15:13:52','ApiController::__constructed properly; user was apiuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(183,'ApiController::generateApiKey','apiuser','2016-04-07 15:13:52','','docninja','','a:1:{s:5:\"token\";s:224:\"353363396632316538383834303737363930316632383463386165373437653463396661373332343335643466306334393134343630636162393931663262615cea7d9b5805123ab8bfae88812ef0b89a68997d5864379e132d6449890f231bcca6f55db811065dced67a537fd9e3f9\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(184,'','','2016-04-07 15:14:41','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(185,'','','2016-04-07 15:14:42','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(186,'','','2016-04-07 15:14:42','ApiController::__constructed properly; user was fastspring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(187,'ApiController::subscriptionActivated','fastspring','2016-04-07 15:14:42','','a:0:{}','Array\n(\n    [ProductName] => \n    [SubscriptionCustomerFullName] => Craig Aldridge\n    [SubscriptionCustomerEmail] => craig@coursesuite.com.au\n    [SubscriptionIsTest] => true\n    [SubscriptionReferrer] => \n    [SubscriptionQuantity] => 1\n    [SubscriptionReference] => COU160407-9674-21268S\n    [SubscriptionEndDate] => \n    [SubscriptionCustomerUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160407-9674-15278S\n    [SubscriptionStatus] => active\n    [security_data] => 1460006081796COU160407-9674-21268S\n    [security_hash] => 3711bc342cedcf955c64cc627f814f51\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(188,'','','2016-04-07 15:14:42','subscriptionActivated passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(189,'','','2016-04-07 22:00:47','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(190,'','','2016-04-08 16:15:34','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(191,'','','2016-04-08 16:15:37','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(192,'','','2016-04-08 16:15:37','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(193,'ApiController::verifyToken','tokenuser','2016-04-08 16:15:38','','docninja','316530666230373461313162343162363433353361353739313432383565656330613938353536633639623265653664326230663036353861643031653331635852c7321c0fce10ecdf455919a8b192856a55e2129730c2ac3d75858faec7bb76bddec49107f28709c2657207ee4734','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(194,'','','2016-04-08 16:15:38','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(195,'','','2016-04-08 16:15:38','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(196,'','','2016-04-08 16:15:38','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(197,'ApiController::verifyToken','tokenuser','2016-04-08 16:15:38','','docninja','316530666230373461313162343162363433353361353739313432383565656330613938353536633639623265653664326230663036353861643031653331635852c7321c0fce10ecdf455919a8b192856a55e2129730c2ac3d75858faec7bb76bddec49107f28709c2657207ee4734','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(198,'','','2016-04-08 16:15:39','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(199,'','','2016-04-08 16:15:39','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(200,'','','2016-04-08 16:15:39','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(201,'ApiController::verifyToken','tokenuser','2016-04-08 16:15:39','','docninja','316530666230373461313162343162363433353361353739313432383565656330613938353536633639623265653664326230663036353861643031653331635852c7321c0fce10ecdf455919a8b192856a55e2129730c2ac3d75858faec7bb76bddec49107f28709c2657207ee4734','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(202,'','','2016-04-08 16:15:39','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(203,'','','2016-04-08 16:15:39','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(204,'','','2016-04-08 16:15:39','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(205,'ApiController::verifyToken','tokenuser','2016-04-08 16:15:40','','docninja','316530666230373461313162343162363433353361353739313432383565656330613938353536633639623265653664326230663036353861643031653331635852c7321c0fce10ecdf455919a8b192856a55e2129730c2ac3d75858faec7bb76bddec49107f28709c2657207ee4734','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(206,'','','2016-04-08 17:29:36','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(207,'','','2016-04-08 17:29:36','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(208,'','','2016-04-08 17:29:36','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(209,'ApiController::verifyToken','tokenuser','2016-04-08 17:29:36','','docninja','30633731303666303831663763643133313437336462376531353365363164323930346131366239386137363838366563613638373661663337633461313236335a62c8f166e08061074999b7fe5777fda200417a00375f89337365ec289d39819dd1eb6c926e122e777a18a366e77d','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(210,'','','2016-04-08 17:29:36','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(211,'','','2016-04-08 17:29:36','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(212,'','','2016-04-08 17:29:36','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(213,'ApiController::verifyToken','tokenuser','2016-04-08 17:29:36','','docninja','30633731303666303831663763643133313437336462376531353365363164323930346131366239386137363838366563613638373661663337633461313236335a62c8f166e08061074999b7fe5777fda200417a00375f89337365ec289d39819dd1eb6c926e122e777a18a366e77d','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(214,'','','2016-04-08 17:29:36','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(215,'','','2016-04-08 17:29:37','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(216,'','','2016-04-08 17:29:37','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(217,'ApiController::verifyToken','tokenuser','2016-04-08 17:29:37','','docninja','30633731303666303831663763643133313437336462376531353365363164323930346131366239386137363838366563613638373661663337633461313236335a62c8f166e08061074999b7fe5777fda200417a00375f89337365ec289d39819dd1eb6c926e122e777a18a366e77d','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(218,'','','2016-04-08 17:29:37','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(219,'','','2016-04-08 17:29:37','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(220,'','','2016-04-08 17:29:37','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(221,'ApiController::verifyToken','tokenuser','2016-04-08 17:29:37','','docninja','30633731303666303831663763643133313437336462376531353365363164323930346131366239386137363838366563613638373661663337633461313236335a62c8f166e08061074999b7fe5777fda200417a00375f89337365ec289d39819dd1eb6c926e122e777a18a366e77d','1','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:1;s:3:\"api\";b:0;s:4:\"tier\";s:1:\"1\";}',NULL,NULL,NULL,NULL,NULL,NULL),
	(222,'','','2016-04-08 17:29:54','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(223,'','','2016-04-08 17:29:54','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(224,'','','2016-04-08 17:29:54','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(225,'ApiController::verifyToken','tokenuser','2016-04-08 17:29:54','','docninja','3466636430356162383539376664326333656464313264306634643131633364383035643764633066323433363539623035636165313038613166666534613707bd1784a2afd8950b6318df6d77866a6afe1c5e14dc7a7536c9d911d3d03efd4aceacc4e56513ed29baf2ff285acac9','','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;s:3:\"api\";b:0;s:4:\"tier\";N;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(226,'','','2016-04-08 17:29:54','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(227,'','','2016-04-08 17:29:54','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(228,'','','2016-04-08 17:29:54','ApiController::__constructed properly; user was tokenuser',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(229,'ApiController::verifyToken','tokenuser','2016-04-08 17:29:54','','docninja','3466636430356162383539376664326333656464313264306634643131633364383035643764633066323433363539623035636165313038613166666534613707bd1784a2afd8950b6318df6d77866a6afe1c5e14dc7a7536c9d911d3d03efd4aceacc4e56513ed29baf2ff285acac9','','a:5:{s:8:\"authuser\";s:9:\"tokenuser\";s:6:\"appkey\";s:8:\"docninja\";s:5:\"valid\";b:0;s:3:\"api\";b:0;s:4:\"tier\";N;}',NULL,NULL,NULL,NULL,NULL,NULL),
	(230,'','','2016-04-11 15:09:38','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(231,'','','2016-04-11 15:09:38','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(232,'','','2016-04-11 15:09:39','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(233,'','','2016-04-11 15:09:39','ApiController::__constructed properly; user was fastspring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(234,'ApiController::subscriptionActivated','fastspring','2016-04-11 15:09:39','','a:0:{}','Array\n(\n    [ProductName] => \n    [SubscriptionCustomerFullName] => Craig Aldridge\n    [SubscriptionCustomerEmail] => craig@coursesuite.com.au\n    [SubscriptionIsTest] => true\n    [SubscriptionReferrer] => \n    [SubscriptionQuantity] => 1\n    [SubscriptionReference] => COU160411-9674-33133S\n    [SubscriptionEndDate] => \n    [SubscriptionCustomerUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-39143S\n    [SubscriptionStatus] => active\n    [security_data] => 1460351378469COU160411-9674-33133S\n    [security_hash] => c951e8a4bb07d0c059d40fda0cbd1278\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(235,'','','2016-04-11 15:09:39','subscriptionActivated passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(236,'','','2016-04-11 15:09:39','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(237,'','','2016-04-11 15:09:39','ApiController::__constructed properly; user was fastspring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(238,'ApiController::orderCompleted','fastspring','2016-04-11 15:09:39','','a:0:{}','Array\n(\n    [OrderReferrer] => \n    [AddressPostalCode] => 93101\n    [AddressStreet1] => Test Mode Address\n    [CustomerLastName] => Aldridge\n    [CustomerPhone] => 61 0475063358\n    [OrderSubTotalUSD] => 99.0\n    [OrderProductNames] => copper\n    [CustomerEmail] => craig@coursesuite.com.au\n    [OrderIsTest] => true\n    [AddressCountry] => US\n    [AddressCity] => Santa Barbara\n    [OrderID] => COU160411-9674-30115\n    [AddressRegion] => CA\n    [OrderDiscountTotalUSD] => 0.0\n    [CustomerFirstName] => Craig\n    [OrderShippingTotalUSD] => 0.0\n    [CustomerCompany] => avide elearning\n    [AddressStreet2] => \n    [security_data] => 1460351378475COU160411-9674-30115craig@coursesuite.com.au1460351378458\n    [security_hash] => 24312f51b36afc3895e2a63eab82fc43\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(239,'','','2016-04-11 15:09:39','orderCompleted passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(240,'','','2016-04-11 15:13:35','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(241,'','','2016-04-11 15:13:35','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(242,'','','2016-04-11 15:13:36','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(243,'','','2016-04-11 15:13:36','ApiController::__construct',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(244,'','','2016-04-11 15:13:36','ApiController::__constructed properly; user was fastspring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(245,'','','2016-04-11 15:13:36','ApiController::__constructed properly; user was fastspring',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(246,'ApiController::subscriptionActivated','fastspring','2016-04-11 15:13:36','','a:0:{}','Array\n(\n    [ProductName] => \n    [SubscriptionCustomerFullName] => Craig Aldridge\n    [SubscriptionCustomerEmail] => craig@coursesuite.com.au\n    [SubscriptionIsTest] => true\n    [SubscriptionReferrer] => \n    [SubscriptionQuantity] => 1\n    [SubscriptionReference] => COU160411-9674-33133S\n    [SubscriptionEndDate] => \n    [SubscriptionCustomerUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-39143S\n    [SubscriptionStatus] => active\n    [security_data] => 1460351614878COU160411-9674-33133S\n    [security_hash] => ed2603212d40ad46c6182830a8bfdaf8\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(247,'ApiController::orderCompleted','fastspring','2016-04-11 15:13:36','','a:0:{}','Array\n(\n    [OrderReferrer] => \n    [AddressPostalCode] => 93101\n    [AddressStreet1] => Test Mode Address\n    [CustomerLastName] => Aldridge\n    [CustomerPhone] => 61 0475063358\n    [OrderSubTotalUSD] => 99.0\n    [OrderProductNames] => copper\n    [CustomerEmail] => craig@coursesuite.com.au\n    [OrderIsTest] => true\n    [AddressCountry] => US\n    [AddressCity] => Santa Barbara\n    [OrderID] => COU160411-9674-30115\n    [AddressRegion] => CA\n    [OrderDiscountTotalUSD] => 0.0\n    [CustomerFirstName] => Craig\n    [OrderShippingTotalUSD] => 0.0\n    [CustomerCompany] => avide elearning\n    [AddressStreet2] => \n    [security_data] => 1460351614881COU160411-9674-30115craig@coursesuite.com.au1460351614866\n    [security_hash] => d468c0f63052a3744457107487151519\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(248,'','','2016-04-11 15:13:36','subscriptionActivated passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(249,'','','2016-04-11 15:13:36','orderCompleted passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(250,'ApiController::orderCompleted','fastspring','2016-04-11 15:18:37','','a:0:{}','Array\n(\n    [OrderReferrer] => \n    [AddressPostalCode] => 93101\n    [AddressStreet1] => Test Mode Address\n    [CustomerLastName] => Aldridge\n    [CustomerPhone] => 61 0475063358\n    [OrderSubTotalUSD] => 99.0\n    [OrderProductNames] => copper\n    [CustomerEmail] => craig@coursesuite.com.au\n    [OrderIsTest] => true\n    [AddressCountry] => US\n    [AddressCity] => Santa Barbara\n    [OrderID] => COU160411-9674-30115\n    [AddressRegion] => CA\n    [OrderDiscountTotalUSD] => 0.0\n    [CustomerFirstName] => Craig\n    [OrderShippingTotalUSD] => 0.0\n    [CustomerCompany] => avide elearning\n    [AddressStreet2] => \n    [security_data] => 1460351917195COU160411-9674-30115craig@coursesuite.com.au1460351917180\n    [security_hash] => 52f39685ac78de11201c10c6c6d0891b\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(251,'','','2016-04-11 15:18:37','orderCompleted passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(252,'ApiController::subscriptionActivated','fastspring','2016-04-11 15:20:54','','a:0:{}','Array\n(\n    [ProductName] => \n    [SubscriptionCustomerFullName] => Craig Aldridge\n    [SubscriptionCustomerEmail] => craig@coursesuite.com.au\n    [SubscriptionIsTest] => true\n    [SubscriptionReferrer] => \n    [SubscriptionQuantity] => 1\n    [SubscriptionReference] => COU160411-9674-72169S\n    [SubscriptionEndDate] => \n    [SubscriptionCustomerUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-43184S\n    [SubscriptionStatus] => active\n    [security_data] => 1460352054046COU160411-9674-72169S\n    [security_hash] => 08a585a20371acd6ff6277d04f611ece\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(253,'','','2016-04-11 15:20:54','subscriptionActivated passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(254,'ApiController::orderCompleted','fastspring','2016-04-11 15:20:54','','a:0:{}','Array\n(\n    [OrderReferrer] => \n    [AddressPostalCode] => 93101\n    [AddressStreet1] => Test Mode Address\n    [CustomerLastName] => Aldridge\n    [CustomerPhone] => 61 0475063358\n    [OrderSubTotalUSD] => 99.0\n    [OrderProductNames] => copper\n    [CustomerEmail] => craig@coursesuite.com.au\n    [OrderIsTest] => true\n    [AddressCountry] => US\n    [AddressCity] => Santa Barbara\n    [OrderID] => COU160411-9674-36150\n    [AddressRegion] => CA\n    [OrderDiscountTotalUSD] => 0.0\n    [CustomerFirstName] => Craig\n    [OrderShippingTotalUSD] => 0.0\n    [CustomerCompany] => avide elearning\n    [AddressStreet2] => \n    [security_data] => 1460352054053COU160411-9674-36150craig@coursesuite.com.au1460352054035\n    [security_hash] => cb399741a0044fd3060de6493b9aad29\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(255,'','','2016-04-11 15:20:54','orderCompleted passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(256,'ApiController::orderCompleted','fastspring','2016-04-11 15:21:51','','a:0:{}','Array\n(\n    [OrderReferrer] => \n    [AddressPostalCode] => 93101\n    [AddressStreet1] => Test Mode Address\n    [CustomerLastName] => Aldridge\n    [CustomerPhone] => 61 0475063358\n    [OrderSubTotalUSD] => 99.0\n    [OrderProductNames] => copper\n    [CustomerEmail] => craig@coursesuite.com.au\n    [OrderIsTest] => true\n    [AddressCountry] => US\n    [AddressCity] => Santa Barbara\n    [OrderID] => COU160411-4668-72117B\n    [AddressRegion] => CA\n    [OrderDiscountTotalUSD] => 0.0\n    [CustomerFirstName] => Craig\n    [OrderShippingTotalUSD] => 0.0\n    [CustomerCompany] => avide elearning\n    [AddressStreet2] => \n    [security_data] => 1460352110800COU160411-4668-72117Bcraig@coursesuite.com.au1460352110775\n    [security_hash] => 0c17e4d1c1b8f3d164befeaaf03c92f3\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(257,'','','2016-04-11 15:21:51','orderCompleted passed security hash',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(258,'ApiController::orderCompleted','fastspring','2016-04-11 15:32:50','','a:0:{}','Array\n(\n    [Status] => completed\n    [AddressPostalCode] => 93101\n    [isTest] => true\n    [OrderProductNames] => copper\n    [CustomerPhone] => 61 0475063358\n    [AddressRegion] => CA\n    [CustomerCompany] => avide elearning\n    [CustomerEmail] => craig@coursesuite.com.au\n    [CustomerFirstName] => Craig\n    [OrderSubTotalUSD] => 99.0\n    [OrderIsTest] => true\n    [OrderShippingTotalUSD] => 0.0\n    [OrderDiscountTotalUSD] => 0.0\n    [originIp] => \n    [isRebill] => true\n    [CustomerLastName] => Aldridge\n    [AddressStreet2] => \n    [OrderID] => COU160411-4668-72117B\n    [AddressCountry] => US\n    [AddressStreet1] => Test Mode Address\n    [OrderReferrer] => \n    [AddressCity] => Santa Barbara\n    [security_data] => 1460352770078COU160411-4668-72117Bcraig@coursesuite.com.au1460352770059\n    [security_hash] => b7b41356af61707ac7e05955228c2ecb\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(259,'ApiController::subscriptionActivated','fastspring','2016-04-11 15:35:54','','a:0:{}','Array\n(\n    [ProductName] => \n    [SubscriptionCustomerFullName] => Craig Aldridge\n    [SubscriptionCustomerEmail] => craig@coursesuite.com.au\n    [SubscriptionIsTest] => true\n    [SubscriptionReferrer] => \n    [SubscriptionQuantity] => 1\n    [SubscriptionReference] => COU160411-9674-24209S\n    [SubscriptionEndDate] => \n    [SubscriptionCustomerUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-14212S\n    [SubscriptionStatus] => active\n    [security_data] => 1460352954119COU160411-9674-24209S\n    [security_hash] => 5219b28bbdee1b2d56bd4c99ff2d8a84\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(260,'ApiController::orderCompleted','fastspring','2016-04-11 15:35:54','','a:0:{}','Array\n(\n    [Status] => completed\n    [AddressPostalCode] => 93101\n    [isTest] => true\n    [OrderProductNames] => jade\n    [CustomerPhone] => 61 0475063358\n    [AddressRegion] => CA\n    [CustomerCompany] => avide elearning\n    [CustomerEmail] => craig@coursesuite.com.au\n    [CustomerFirstName] => Craig\n    [OrderSubTotalUSD] => 169.0\n    [OrderIsTest] => true\n    [OrderShippingTotalUSD] => 0.0\n    [OrderDiscountTotalUSD] => 0.0\n    [originIp] => 180.181.86.137\n    [isRebill] => false\n    [CustomerLastName] => Aldridge\n    [AddressStreet2] => \n    [OrderID] => COU160411-9674-63199\n    [AddressCountry] => US\n    [AddressStreet1] => Test Mode Address\n    [OrderReferrer] => \n    [AddressCity] => Santa Barbara\n    [security_data] => 1460352954126COU160411-9674-63199craig@coursesuite.com.au1460352954108\n    [security_hash] => 6d281ad0a6b1ffc9caaaefdd6eb20714\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(261,'ApiController::orderCompleted','fastspring','2016-04-11 15:36:44','','a:0:{}','Array\n(\n    [Status] => completed\n    [AddressPostalCode] => 93101\n    [isTest] => true\n    [OrderProductNames] => jade\n    [CustomerPhone] => 61 0475063358\n    [AddressRegion] => CA\n    [CustomerCompany] => avide elearning\n    [CustomerEmail] => craig@coursesuite.com.au\n    [CustomerFirstName] => Craig\n    [OrderSubTotalUSD] => 169.0\n    [OrderIsTest] => true\n    [OrderShippingTotalUSD] => 0.0\n    [OrderDiscountTotalUSD] => 0.0\n    [originIp] => \n    [isRebill] => true\n    [CustomerLastName] => Aldridge\n    [AddressStreet2] => \n    [OrderID] => COU160411-4668-68131B\n    [AddressCountry] => US\n    [AddressStreet1] => Test Mode Address\n    [OrderReferrer] => \n    [AddressCity] => Santa Barbara\n    [security_data] => 1460353003628COU160411-4668-68131Bcraig@coursesuite.com.au1460353003607\n    [security_hash] => 1194692b9798c7bd202add2710c48701\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(262,'ApiController::orderCompleted','fastspring','2016-04-11 15:37:35','','a:0:{}','Array\n(\n    [Status] => completed\n    [AddressPostalCode] => 93101\n    [isTest] => true\n    [OrderProductNames] => jade\n    [CustomerPhone] => 61 0475063358\n    [AddressRegion] => CA\n    [CustomerCompany] => avide elearning\n    [CustomerEmail] => craig@coursesuite.com.au\n    [CustomerFirstName] => Craig\n    [OrderSubTotalUSD] => 169.0\n    [OrderIsTest] => true\n    [OrderShippingTotalUSD] => 0.0\n    [OrderDiscountTotalUSD] => 0.0\n    [originIp] => \n    [isRebill] => true\n    [CustomerLastName] => Aldridge\n    [AddressStreet2] => \n    [OrderID] => COU160411-4668-14138B\n    [AddressCountry] => US\n    [AddressStreet1] => Test Mode Address\n    [OrderReferrer] => \n    [AddressCity] => Santa Barbara\n    [security_data] => 1460353054569COU160411-4668-14138Bcraig@coursesuite.com.au1460353054536\n    [security_hash] => a816df28c7ed09f15fb2a61e47db73ec\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(263,'ApiController::subscriptionActivated','fastspring','2016-04-11 15:57:05','','a:0:{}','Array\n(\n    [ProductName] => \n    [SubscriptionIsTest] => true\n    [SubscriptionStatus] => active\n    [SubscriptionCustomerUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-97238S\n    [SubscriptionEndDate] => \n    [SubscriptionQuantity] => 1\n    [SubscriptionReferrer] => \n    [SubscriptionCustomerFullName] => Craig Aldridge Derp\n    [SubscriptionReference] => COU160411-9674-44230S\n    [SubscriptionStatusReason] => \n    [SubscriptionCustomerEmail] => craig@coursesuite.com.au\n    [security_data] => 1460354224816COU160411-9674-44230S\n    [security_hash] => ca6f5ba99a8a178b41851f30cb1f343e\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(264,'ApiController::orderCompleted','fastspring','2016-04-11 15:57:05','','a:0:{}','Array\n(\n    [originIp] => 180.181.86.137\n    [CustomerCompany] => avide elearning\n    [AddressStreet2] => \n    [OrderShippingTotalUSD] => 0.0\n    [CustomerEmail] => craig@coursesuite.com.au\n    [OrderSubTotalUSD] => 169.0\n    [CustomerLastName] => Derp\n    [isTest] => true\n    [OrderID] => COU160411-9674-60228\n    [OrderIsTest] => true\n    [CustomerFirstName] => Craig Aldridge\n    [AddressRegion] => CA\n    [OrderDiscountTotalUSD] => 0.0\n    [AddressPostalCode] => 93101\n    [Status] => completed\n    [CustomerPhone] => 61 0475063358\n    [OrderProductNames] => jade\n    [AddressStreet1] => Test Mode Address\n    [AddressCountry] => US\n    [isRebill] => false\n    [OrderReferrer] => \n    [AddressCity] => Santa Barbara\n    [security_data] => 1460354224822COU160411-9674-60228craig@coursesuite.com.au1460354224804\n    [security_hash] => 43f08244da66111cdb79dfa695f65861\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(265,'ApiController::subscriptionActivated','fastspring','2016-04-11 16:03:23','','a:0:{}','Array\n(\n    [SubscriptionCustomerFullName] => Craig Aldridge Herp\n    [SubscriptionStatusReason] => \n    [SubscriptionIsTest] => true\n    [SubscriptionCustomerEmail] => craig@coursesuite.com.au\n    [ProductName] => jade\n    [SubscriptionReference] => COU160411-9674-55256S\n    [SubscriptionStatus] => active\n    [SubscriptionCustomerUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-70272S\n    [SubscriptionEndDate] => \n    [security_data] => 1460354602297COU160411-9674-55256S\n    [security_hash] => 7ed95864457d3861860d344dc587481a\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(266,'ApiController::orderCompleted','fastspring','2016-04-11 16:03:23','','a:0:{}','Array\n(\n    [originIp] => 180.181.86.137\n    [CustomerCompany] => avide elearning\n    [AddressStreet2] => \n    [OrderShippingTotalUSD] => 0.0\n    [CustomerEmail] => craig@coursesuite.com.au\n    [OrderSubTotalUSD] => 169.0\n    [CustomerLastName] => Herp\n    [isTest] => true\n    [OrderID] => COU160411-9674-44255\n    [OrderIsTest] => true\n    [CustomerFirstName] => Craig Aldridge\n    [AddressRegion] => CA\n    [OrderDiscountTotalUSD] => 0.0\n    [AddressPostalCode] => 93101\n    [Status] => completed\n    [CustomerPhone] => 61 0475063358\n    [OrderProductNames] => jade\n    [AddressStreet1] => Test Mode Address\n    [AddressCountry] => US\n    [isRebill] => false\n    [OrderReferrer] => \n    [AddressCity] => Santa Barbara\n    [security_data] => 1460354602302COU160411-9674-44255craig@coursesuite.com.au1460354602284\n    [security_hash] => 0800c84e0e18c43c1d75b6e452e7361f\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(267,'ApiController::subscriptionActivated','fastspring','2016-04-11 16:11:02','','a:0:{}','Array\n(\n    [SubscriptionStatus] => active\n    [SubscriptionStatusReason] => \n    [ProductName] => copper\n    [SubscriptionReference] => COU160411-9674-30299S\n    [SubscriptionIsTest] => true\n    [AccountUrl] => \n    [SubscriptionCustomerEmail] => craig@coursesuite.com.au\n    [SubscriptionReferrer] => 6gficbaujprk1gcuotso6frat4\n    [SubscriptionCustomerUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-67300S\n    [SubscriptionCustomerFullName] => Craig Aldridge WITH ID\n    [SubscriptionEndDate] => \n    [security_data] => 1460355062189COU160411-9674-30299S\n    [security_hash] => 68832cc45ee48825f2e28888b108a4ff\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(268,'-- data format change --','','2016-04-11 16:33:03','',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(269,'ApiController::subscription','fastspring','2016-04-11 16:33:58','','a:1:{i:0;s:9:\"activated\";}','Array\n(\n    [email] => craig@coursesuite.com.au\n    [testmode] => true\n    [subscriptionEndDate] => \n    [referenceId] => COU160411-9674-41328S\n    [status] => active\n    [productName] => copper\n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-97336S\n    [accountUrl] => \n    [referrer] => mycustomreferrer\n    [statusReason] => \n    [security_data] => 1460356438170COU160411-9674-41328S\n    [security_hash] => a8962c5b462412c4262d4a370907632a\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(270,'ApiController::subscription','fastspring','2016-04-11 16:37:59','','a:1:{i:0;s:7:\"changed\";}','Array\n(\n    [subscriptionEndDate] => Apr 24, 2016\n    [productName] => copper\n    [testmode] => true\n    [statusReason] => canceled\n    [referenceId] => COU160411-9674-41328S\n    [email] => craig@coursesuite.com.au\n    [accountUrl] => \n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-97336S\n    [status] => active\n    [referrer] => mycustomreferrer\n    [security_data] => 1460356676440COU160411-9674-41328S\n    [security_hash] => 35d417c1b69b6ebfcf8671f8f0ab07b6\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(271,'ApiController::subscription','fastspring','2016-04-11 16:38:38','','a:1:{i:0;s:7:\"changed\";}','Array\n(\n    [subscriptionEndDate] => \n    [productName] => copper\n    [testmode] => true\n    [statusReason] => \n    [referenceId] => COU160411-9674-41328S\n    [email] => craig@coursesuite.com.au\n    [accountUrl] => \n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-97336S\n    [status] => active\n    [referrer] => mycustomreferrer\n    [security_data] => 1460356717160COU160411-9674-41328S\n    [security_hash] => a1e352ae769b9115ea0fb3af4634f3d3\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(272,'ApiController::subscription','fastspring','2016-04-11 16:38:48','','a:1:{i:0;s:6:\"failed\";}','Array\n(\n    [referenceId] => COU160411-9674-41328S\n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-97336S\n    [statusReason] => \n    [accountUrl] => \n    [productName] => copper\n    [subscriptionEndDate] => \n    [testmode] => true\n    [referrer] => mycustomreferrer\n    [email] => craig@coursesuite.com.au\n    [status] => active\n    [security_data] => 1460356728122COU160411-9674-41328S\n    [security_hash] => 6216d12eec71e9cb6bb5fd97f1b8a119\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(273,'ApiController::subscription','fastspring','2016-04-11 16:41:04','','a:1:{i:0;s:11:\"deactivated\";}','Array\n(\n    [testmode] => true\n    [referrer] => mycustomreferrer\n    [subscriptionEndDate] => May 8, 2016\n    [accountUrl] => \n    [productName] => copper\n    [email] => craig@coursesuite.com.au\n    [statusReason] => canceled-non-payment\n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-97336S\n    [status] => inactive\n    [referenceId] => COU160411-9674-41328S\n    [security_data] => 1460356863842COU160411-9674-41328S\n    [security_hash] => 3e96b990663131cac2b0322fec7d53c3\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(274,'ApiController::subscription','fastspring','2016-04-11 16:42:56','','a:1:{i:0;s:9:\"activated\";}','Array\n(\n    [email] => craig@coursesuite.com.au\n    [testmode] => true\n    [subscriptionEndDate] => \n    [referenceId] => COU160411-9674-18348S\n    [status] => active\n    [productName] => jade\n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-77362S\n    [accountUrl] => \n    [referrer] => myotherreferrer\n    [statusReason] => \n    [security_data] => 1460356975746COU160411-9674-18348S\n    [security_hash] => 83d78dd70f13ef113c52198dfd80ab7e\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(275,'ApiController::subscription','fastspring','2016-04-11 16:43:29','','a:1:{i:0;s:7:\"changed\";}','Array\n(\n    [subscriptionEndDate] => Apr 11, 2016\n    [productName] => jade\n    [testmode] => true\n    [statusReason] => canceled\n    [referenceId] => COU160411-9674-18348S\n    [email] => craig@coursesuite.com.au\n    [accountUrl] => \n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-77362S\n    [status] => inactive\n    [referrer] => myotherreferrer\n    [security_data] => 1460357009236COU160411-9674-18348S\n    [security_hash] => a0ebd5c025e6ee782ee15fc5b04dea7d\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(276,'ApiController::subscription','fastspring','2016-04-11 16:43:29','','a:1:{i:0;s:11:\"deactivated\";}','Array\n(\n    [testmode] => true\n    [referrer] => myotherreferrer\n    [subscriptionEndDate] => Apr 11, 2016\n    [accountUrl] => \n    [productName] => jade\n    [email] => craig@coursesuite.com.au\n    [statusReason] => canceled\n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160411-9674-77362S\n    [status] => inactive\n    [referenceId] => COU160411-9674-18348S\n    [security_data] => 1460357009236COU160411-9674-18348S\n    [security_hash] => a0ebd5c025e6ee782ee15fc5b04dea7d\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(277,'ApiController::generateApiKey','apiuser','2016-04-14 14:49:40','','docninja','','a:1:{s:5:\"token\";s:224:\"323939386335363533323935353165333932643365373064346533386263666435313439323034326639316533336261303538653866623634393261663333654313356d78b88e27ab548e9b9533c68d5e2f9841b72e6dab4bafcef4ae3e56e0c72acc3ba2f6fa47b22206d4ca06b115\";}',NULL,NULL,NULL,NULL,NULL,NULL,NULL),
	(278,'ApiController::subscription','fastspring','2016-04-14 16:04:18','','a:1:{i:0;s:9:\"activated\";}','Array\n(\n    [email] => tim.stclair@gmail.com\n    [testmode] => true\n    [subscriptionEndDate] => \n    [referenceId] => COU160414-9674-12108S\n    [status] => active\n    [productName] => copper\n    [subscriptionUrl] => https://sites.fastspring.com/coursesuite/order/s/COU160414-9674-32125S\n    [accountUrl] => \n    [referrer] => MzlkZDdjYzdmNGQyZWFkZGU3Mjg5YWFiNzYzMTNmODY1NDIyZWY2ZGQxNGQ5N2ViNDE4MzNjNTgyMzUzNzgyZbkpEa9taTuwXC2QRosFqc4zeBU13kV0qbkrLKz8hdNH\n    [statusReason] => \n    [security_data] => 1460613856757COU160414-9674-12108S\n    [security_hash] => 4c91f99f9b0aaf40b4262469b8afa552\n)\n',NULL,NULL,NULL,NULL,NULL,NULL,NULL,NULL);

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
	(1,'docninja','document scormification ninja','Convert (almost) anything to HTML5 & SCORM','/img/apps/docninja_tile.jpg','http://docninja.frumbert.org/','http://docninja.frumbert.org/app/',NULL,0,'2016-04-11 13:38:34',1,NULL,0,'		<h2>You want it done <em>when?</em></h2>\n		<p><b>Scenario:</b> You\'ve been given a 75-page Word document (no doubt full of tables, images and annotations) and been told to make it into a SCORM-compliant package for your LMS, and learners needs to view at least to page 60 before they are considered complete. And you\'ve got two weeks to get it done. Can you?</p>\n		<p>Sure. How about in the next 5 minutes? <em>No worries.</em></p>\n\n		<h3>We convert your documents, media and slides right in your browser.</h3>\n		<p><img src=\"/img/HTML5_Logo_128.png\" class=\"h5\" alt=\"HTML5 logo\">Our Document Scormification Ninja is a special in-browser course wizard. With it you can take your existing content converting it to modern, industry standard HTML5 and give it a SCORM wrapper, making it ready to upload into your LMS platform, all in a matter of minutes, and all by dragging links or files onto your browser.</p>\n		<h3>Your content is what\'s important.</h3>\n		<p>Here\'s the thing: your content is what\'s important, and you probably don\'t have that much time already. So just drag and drop your files or URL\'s onto our app, give your course a name, and press download. It\'s that easy.</p>\n		<p>Most of the time the converted HTML5 documents are pixel-perfect to the original document. You\'ll be surprised how good it is. You can also split multi-page documents into separate navigation items (it\'s really handy, be sure to try it).</p>\n\n		<p class=\"formats\">If a document <em>can</em> convert to HTML5, we probably support it -\n			<span>abw</span> <span>doc</span> <span>docx</span> <span>epub</span> <span>gif</span> <span>html</span> <span>imgur gallery</span> <span>jpeg</span> <span>key</span> <span>lit</span> <span>md</span> <span>mobi</span> <span>moodle book (ims cp)</span> <span>odp</span> <span>odt</span> <span>pages</span> <span>pdf</span> <span>png</span> <span>ppt</span> <span>pptx</span> <span>ps</span> <span>slideshare</span> <span>soundcloud</span> <span>tiff</span> <span>txt</span> <span>vimeo</span> <span>webp</span> <span>youtube</span>\n			+ many other formats.\n		</p>\n\n		<h3>Straightforward commonplace settings with sensible defaults</h3>\n		<p>You can convert and embed multiple files from different sources, including files of different formats (combine full-page images with YouTube videos and PDF documents, for example). We offer a few easily navigable layouts optimised for most e-learning environments (just the basics). We haven\'t gone all out allowing you to customise every detail in the design: the aim of this tool is for you to get your content online in minutes.</p>\n\n		<h3>Embeddable content</h3>\n		<p>Learning material is often best presented using slideshows, video and audio. But video and audio are large files, with complex caveats depending on the learners platform - you often don\'t know if they use an old laptop or a brand-new iPad, and what works on one might not work on the other. So <b>we don\'t let you embed video or audio</b>. When it comes to video and audio, upload them to YouTube, Vimeo or SoundCloud, since those platforms do all the hard work of supporting thousands of end-user devices (and have great privacy options).</p>\n		<p>But we do support images, imgur galleries and files, and even SlideShare. Need a scorm completion after a person views half the slides in a SlideShare? Sure thing.</p>\n\n		<h3>How it works</h3>\n		<p>Your documents, images and slideshows will be converted using a cloud-based web service to HTML5. This service supports most formats such as Microsoft Office (Word, PowerPoint, Excel, Works), OpenOffice, AbiWord, Pages, KeyNote, StarWriter, Lotus Word Pro, as well as PDF, Markdown, and even many DRM-free e-book formats (mobi, epub, azw, lit, etc). In fact, if you find a format that <em>doesn\'t</em> convert then please let us know!</p>\n\n		<ul id=\"block\">\n			<li>Your documents (e.g. PDF)</li>\n			<li>Drag onto Document Ninja</li>\n			<li>Conversion (cloud-based service)</li>\n			<li>Play with Layout & Settings</li>\n			<li>Download (zip file)</li>\n			<li>Upload to your LMS</li>\n			<li>Happy learners!</li>\n		</ul>\n\n		<h3>Completion, Progression, and other options</h3>\n		<p>SCORM is the existing standard that lets reusable, platform-agnostic content communicate data to the host LMS. In most cases this boils down to a single question: <em>Has the user completed the content?</em> And in most courses, this is really what you care about.</p>\n		<p>In many content authoring packages on the market today, SCORM and completion settings seem unnecessarily complicated, but they don\'t have to be. In our tool we have made some fundamental assumptions based on years of experience in what customers require. In short:</p?\n		<p><ul>\n			<li>The score of the course represents how much is completed (as a percentage).</li>\n			<li>Learners may leave and return more than once before they complete. We remember where they are up to, even for most embedded content.</li>\n			<li>Completion occurs either when the user views the last page, or gets to a certain point in the course (such as has completed 7 out of 10 pages).</li>\n			<li>Tracking individual pages and satisfying objectives at a page level generally doesn\'t happen (it\'s usually done at a unit, course or activity level).</li>\n		</ul></p>\n		<p>We also assume there\'s only two ways to progress through a course. Either page-by-page in order (like a book) or any order (you can skip pages if you choose). So there\'s only two settings.</p>\n		<p>Oh, and did we mention you can get a completion for <strong>watching a some or all of a video</strong>, or <strong>viewing some of a SlideShare presentation</strong>? You can still track how much of the media the user has watched or heard and score the page based on that information. You can use your LMS\'s SCORM tracking tool to examine how much of a video a user has watched, plus if they get more than (say) 75%, they get a completion too. How cool is that? (p.s. if that\'s all you\'re looking for <a href=\"http://media.scormification.ninja\" target=\"_blank\">we have a tool for that</a>.)</p>\n\n		<h3>Who owns the content?</h3>\n		<p>We don\'t want your content. And so we don\'t store it. In fact, it\'s never even uploaded to our servers (it\'s temporarily sent to the cloud for document conversion, but is deleted immediately afterwards). So your content is safe with <strong>you</strong>.</p>\n		<p>Your content is stored by your own browser, which is permanent until you reset the app. So after you are finished, remember to reset (especially if you are using a shared computer).\n		<p>We do keep your email address on file, and we track some details about how you <em>use</em> our software (which settings are most popular), but that\'s all we know about you or your data.</p>\n\n		<h3>What about editing?</h3>\n		<p>You can change the order of pages, split long documents into multiple pages, remove or rename individual pages, and change the design or colour theme of the navigation wrapper.</p>\n		<p>This isn\'t an editing platform. Do your edits in your source: Word or Powerpoint or SlideShare or whatever (that is; use the tools are best at the job) and just convert their output. But you can <em>tweak</em> text after the file is converted (with caution, useful for a typo or two, but not a paragraph). We have <a href=\"http://coursesuite.ninja\" target=\"_blank\">other tools</a> with full editing capability.</p>\n\n		<h3><img src=\"/img/pie.png\" alt=\"pie graph showing less time building, more time learning\" class=\"pie\">Spend your time where it counts. </h3>\n		<p>Do the conversion, stick it on your LMS. Let your learners spend time learning.</p>\n\n		<h3>Browser requirements</h3>\n		<p>We have developed the <strong>builder app</strong> to be most compatible with Google Chrome or Mozilla Firefox for <em>desktops</em>. It might work in other browsers, or might not. The <strong>builder</strong> currently doesn\'t work on mobile devices (but we are considering it) because of limitations with their file systems.</p>\n		<p>The <strong>content you produce</strong> should work fine <em>in any modern browser</em>, depending on the capabilities and what you put in yourself, and the host LMS (Don\'t expect a 15MB image to show on a mobile device, or a Vimeo video to play offline).</p>\n		<hr>\n		<h3>Change log</h3>\n		<p><a href=\"changelog.php\" target=\"_blank\">click here</a></p>','[{\"html\":\"<iframe width=\'420\' height=\'315\' src=\'https://www.youtube.com/embed/meUhHRqWkeY\' frameborder=\'0\' allowfullscreen></iframe>\",\"caption\":\"\",\"kind\":\"video\"},{\"html\":\"<iframe width=\'420\' height=\'315\' src=\'https://www.youtube.com/embed/eBwkuouFLms\' frameborder=\'0\' allowfullscreen></iframe>\",\"caption\":\"\",\"kind\":\"video\"},{\"html\":\"<iframe width=\'420\' height=\'315\' src=\'https://www.youtube.com/embed/OF1Q3_r_9mc\' frameborder=\'0\' allowfullscreen></iframe>\",\"caption\":\"\",\"kind\":\"video\"},{\"html\":\"<iframe width=\'420\' height=\'315\' src=\'https://www.youtube.com/embed/Hp_WQEKZRx8\' frameborder=\'0\' allowfullscreen></iframe>\",\"caption\":\"\",\"kind\":\"video\"},{\"html\":\"<img src=\'/img/slides/slide1.png\'>\",\"caption\":\"App tile caption here\",\"kind\":\"image\"},{\"html\":\"<img src=\'/img/slides/slide2.png\'>\",\"caption\":\"App tile 2 with an extended description caption that might be long enough to run onto a second line\",\"kind\":\"image\"},{\"html\":\"<img src=\'/img/slides/slide3.png\'>\",\"caption\":\"\",\"kind\":\"image\"}]'),
	(2,'vidninja','media scormification ninja','Add SCORM completions to video','/img/apps/medianinja_tile.jpg','http://vidninja.frumbert.org/','http://vidninja.frumbert.org/app/',NULL,0,'2016-04-11 13:38:40',0,NULL,0,'<h2 class=\"section-heading\">Info</h2>\n<p>The Media Scormification Ninja converts your streaming video or audio file to a SCORM package, so that you can get a completion after the user watches a certain amount of the media. It does not embed the file directly in the package - it still uses the same capabilities as supplied by the host when simply using their supplied Embed or Share codes, but in such a way as that it can track the amount of time that the media has played. Once you\'ve set the place where you consider the media \"watched\" (we call a Marker), you can then download the ready-made Zip file which contains all the neccesary SCORM files.</p>\n<h3>Why only support streaming media?</h3>\n<p>BrightCove, YouTube, Vimeo and SoundCloud all work very hard to support the widest range of browsers and platforms for their content - its in their best interest. This means that your media will play in desktop, tablet and phone devices alike. We also use their standard players (not custom \'skins\' or players) to ensure the maximum flexibility and compatibility. Conversely, embedding media means also embedding a range of possible players, skins, and script files to account for different devices, different browser capabilities, and so on (in most cases duplicating the media in multiple formats): it\'s much harder, more bug-prone, less customisable, and makes huge files which you then have to deal with.</p>\n<p>Using streaming services is <em>cheaper for you</em>: It saves your servers bandwidth (data-transfer quota and costs per megabyte) from serving potentially large media files. Media is uploaded only once - but can be used in multiple courses and sites, in formats that are correct for the clients browsers or devices. If changes are made to the media, all courses and sites using that media will all automatically get the most current version of the material.</p>\n<h3>Click on a heading below for more informtion.</h3>\n<details>\n      <summary>Why would I want to track videos with SCORM?</summary>\n      <p>Let\'s say your learners need to watch material in order to gain a Continuing Professional Development (CPD) credit. Their industries\' rules state that they need to watch at least 45 minutes of a 1 hour video in order to gain 1 point. </p>\n      <p>The tool lets you do that. You can specify a video URL, set the completion requirement to 75%, and publish it as a SCORM package. You then just drop this into your LMS and start recording the completions.</p>\n      <p>We also record how much of the video has been watched. This has two benefits - <em>for the learner</em> it means they can exit the activity and come back another time and have the video pick back up where they left off; and <em>for the trainer</em> it means you can report on exactly how much of the media an individual is actually watching or listening to.</p>\n</details>\n<details>\n      <summary>What SCORM version do you publish to?</summary>\n      <p>We package using SCORM 1.2, as it is the most widely implemented version.</p>\n</details>\n<details>\n      <summary>What are the video sizes?</summary>\n      <p>It\'s generally recommended to use the Responsive size, as this means the video will scale to fit its container whilst maintaining a 16:9 aspect ratio.</p>\n      <p>Media sizes are taken from the default options for each of the players\' embed options. These are:</p>\n      <ul>\n            <li><b>YouTube</b> - Small: 560px x 315px, Large: 853px x 480px, Responsive: 100% x 56.25%</li>\n            <li><b>Vimeo</b> - Small: 500px x 281px, Large: 960px x 540px, Responsive: 100% x 56.25%</li>\n            <li><b>SoundCloud</b> - Small: 100% x 166px (Standard layout), Large: 100% x 450px (Visual layout), Responsive: 100% x 56.25% (Visual layout)</li>\n            <li><b>BrightCove</b> - Small: 512px x 288px, Large: 768px x 432px, Responsive: 100% x 56.25% (16:9)</li>\n      </ul>\n</details>\n<details>\n      <summary>Which Learning Management Systems do you support?</summary>\n      <p>Any SCORM 1.2 compliant LMS, such as Moodle, Course Cloud, Totara, Scorm Cloud, Blackboard, or hundreds of others.</p>\n</details>\n<details>\n      <summary>What if a learner only watches some of the video and returns later to watch the rest?</summary>\n      <p>They will resume the video or sound file at the exact moment they left the package when they return -and their completion will occur when they reach the desired percentage of media to be viewed.</p>\n</details>\n<details>\n      <summary>What about privacy? I don’t want the whole world being able to access our videos.</summary>\n      <p>You need to consult the options for your video host. For instance, some Vimeo accounts can restrict which domains are able to watch the video.</p>\n</details>\n<details>\n      <summary>Do I need to download any software to use this app?</summary>\n      <p>Since it is web based, it requires no installation or any plugins</p>\n</details>\n<details>\n      <summary>How do I embed BrightCove video?</summary>\n      <p>Ok, this one is a little involved. BrightCove has a concept called Players, which are skins for the various videos. Each video can be published with one or more skins, and you can set up multiple players. One of these is a HTML5 player, and in it you must also specify that the player use API\'s and HTML5 delivery. Please refer to the BrightCove documentation about how to do this.</p>\n      <p class=\"text-center\"><img src=\"/img/brightcove_player.png\"></p>\n      <p>Some brightcove player embed codes look like this: <em>http://bcove.me/u4bjl68t</em> - we can\'t support this format since it doesn\'t identify which player will be used (it *might* be one with an API enabled, but there isn\'t a way to know). So you have to use the <b>javascript</b> embed code, which you can find in the Quick Video Publish tool:</p>\n      <p class=\"text-center\"><img src=\"/img/brightcove_publish.png\"></p>\n</details>\n\n<details>\n      <summary>Why the Ninja?</summary>\n      <p>Because it’s cool. We like ninjas.</p>\n</details>\n\n                <h2>Under the hood</h2>\n<p>So you might be wondering what the innards of this thing actually are (how it works). It uses a minimal SCORM 1.2 based engine that, like most wrappers, expects the SCORM API to exist in a parent frame or window. It also has hand-coded listeners that tap the player API\'s that BrightCove, YouTube, Vimeo and SoundCloud expose on order to capture information about the media being played. These wrappers call SCORM commands as the media plays.</p>\n<details>\n    <summary>Click here for all the gory details</summary></p>\n    <ol>\n        <li><b>onload</b>:<ol>\n            <li>perform a scorm initialise (gets the api)</li>\n            <li>read <em>cmi.core.entry</em> (set to ab-initio on first launch)</li>\n            <li>read <em>cmi.core.lesson_location</em> (last position in video)</li>\n        </ol></li>\n        <li><b>on video start</b>:<ol>\n            <li>set <em>cmi.core.exit</em> to \"suspend\"</li>\n            <li>set <em>cmi.core.lesson_status</em> to \"incomplete\"</li>\n            <li>perform a scorm commit</li>\n        </ol></li>\n        <li><b>periodically</b> (as video is playing):<ol>\n            <li>set <em>cmi.core.lesson_location</em> to the number of seconds the video is up to. You can use this value to check how much a learner actually viewed through to.</li>\n            <li>Check the required amount to be watched, and if it matches or is greater, perform a completion (see below).</li>\n        </ol></li>\n        <li><b>on pause, rebuffer, or end</b>:<ol>\n            <li>Call LMS Commit to persist the changed data (e.g. seconds played)</li>\n        </ol></li>\n        <li><b>on completion</b>:<ol>\n            <li>set <em>cmi.core.exit</em> to \"\" (blank string, effectively means \"logout\" according to the SCORM spec)</li>\n            <li>set <em>cmi.core.lesson_status</em> to \"completed\"</li>\n            <li>set <em>cmi.core.score.min</em> to \"0\"</li>\n            <li>set <em>cmi.core.score.max</em> to \"100\"</li>\n            <li>set <em>cmi.core.score.raw</em> to the required percentage</li>\n            <li>perform a scorm commit</li>\n        </ol></li>\n        <li><b>onunload</b>:<ol>\n            <li>perform a scorm commit</li>\n            <li>perform a scorm finish</li>\n        </ol></li>\n    </ol>\n    <p>Effectively, this means the video is able to be resumed from the point the learner leaves, as <em>lesson_location</em> is being stored. It also means that the score required for completion in your LMS is the same percentage that needs to be watched.</p>\n</details>\n\n<h2>Setting it up in a LMS</h2>\n<ul>\n    <li>Make sure you are embedding this into a SCORM-compliant LMS.</li>\n    <li>Base your completion on the SCORE rather than a lesson_status (because the completed / passed status is buggy in a number of LMS\'s due to ambiguity in the SCORM specification).</li>\n    <li>Set the completion required score to the <u>percentage watched</u> that is set for the video (shown when you set a marker).</li>\n</ul>','[{\"html\":\"<iframe width=\'420\' height=\'315\' src=\'https://www.youtube.com/embed/9lzs50gCfwQ\' frameborder=\'0\' allowfullscreen></iframe>\",\"caption\":\"\",\"kind\":\"video\"},{\"html\":\"<img src=\'/img/slides/media_screenshot.png\'>\",\"caption\":\"a screenshot showing the media ninja doing its thing, so yeah\",\"kind\":\"image\"}]'),
	(3,'coursebuildr','coursebuildr','Powerful, interactive SCORM courses & quizzes','/img/apps/coursebuildr_tile.jpg','http://coursebuildr.coursesuite.ninja/','http://coursebuildr.coursesuite.ninja/',NULL,0,'2016-04-11 13:38:43',0,NULL,0,'<p>CourseBuildr is a full featured only course and quiz editor that lets you create rich, interactive HTML5 courses without know HTML or using esoteric Flash-based builders. It\'s much more than a simple slideshow maker - after all, if that\'s all you need then you can just use presentation software. But courses are ineffective communicators without interaction.</p>\n<p>You can quickly add visual elements to your courses such as tab bars, expanding sections (accordions), slideshows, popup video, references and term definitions, balloon tips, flip-cards, inline quizzes and much more - all without knowing any scripting language or HTML.</p>\n<p>CourseBuildr produces SCORM 1.2 or SCORM 2004 compatible courses that play on all your platforms - no more publishing multiple packages for each platform, or bundling extra code or hacks for just one device. Everything works the same everywhere.</p>\n<p>We use this product internally and have developed it over a number of years and continue to produce course material using the tool. As we need new features or interactions, we add them in. Every time you download or preview a course, it gets updated to the latest code and includes the latest suite of interactions.</p>\n<p>Multiple users can work on the same course at the same time. Publishing a course takes minutes and the courses themselves are typically only a few MB\'s in size. The built-in preview page hosts a simple SCORM API to emulate the package as it appears in a LMS environment, letting you test out suspending the session or how it appears at different screen resolutions.</p>',NULL),
	(4,'wp2moodle','wordpress 2 moodle','Sign in and enrol a user to Moodle, from Wordpress','/img/apps/app_tile4.png','http://wp2moodle.coursesuite.ninja/','',NULL,1,'2016-03-21 16:22:23',1,0,0,NULL,NULL),
	(5,'tokenenrol','token enrolment','Manage or sell places in a course as seats','/img/apps/app_tile5.png','http://wp2moodle.coursesuite.ninja/token-enrolment/','',NULL,1,'2016-03-21 21:03:37',0,0,0,NULL,NULL),
	(6,'coursecatalogue','course catalogue for moodle','Use custom fields on courses to build a catalogue','/img/apps/app_tile6.png','https://github.com/frumbert/moodle-course_meta/tree/Moodle2.8.7-MultiSelect-MetaFilter','',NULL,1,'2016-03-21 16:22:09',0,0,0,NULL,NULL),
	(7,'fakecourse','some fake course we mocked up','Some kind of secret project maybe','/img/apps/app_tile7.png','','',NULL,0,'2016-03-21 16:26:08',0,0,0,NULL,NULL);

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
	('3v23rp1k7b1es5k0jcgtvkqo70','581b2784fe646d57706ad26dbefd9208',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462754248),
	('48klp6jm57kv6l11cf4gks5bp0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461104731),
	('5dk1o2on9vm57d05scqloo1qm2','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461034198),
	('5efiiiim1vljg4vko4jfm79c97','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1460929127),
	('5ghp2c2hdek148kaa8cs0rcm90','46bfbf8e2abb836fd5cb1706a683a843',X'637372665F746F6B656E7C733A33323A223862323235663533653864303636623338333530383539646532396166616232223B637372665F746F6B656E5F74696D657C693A313436333236353935363B',1463267396),
	('5oc4490p2nh02p4gm07cocpbi7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462164001),
	('6o39aagq075197bnvttcbr0i25','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461359574),
	('7qdf2sumscp6u027gebb79e8m3','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1463122855),
	('7qus9rh7hkjn24jc3bq7einqi7','a55db367839d193fa45c98d64047868c',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461216745),
	('8lkjp6j4ra5iomheu9nbpjokq6','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463319365),
	('95stln0cslk51t1ohn26is0cd4','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463289907),
	('97plutdil9gh74mld1lugq8so7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463096540),
	('aeimvfi7k3dgi8eri6k4ntrl21','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463225093),
	('b2o1o7i79ahsh6suchu60ggfp0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461048479),
	('b2sp42kheiaab37kf3ht5tqu00','1586bdaad0b6a0d7d29855ce50308d5d',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462437101),
	('bqa4ni9rm2a81mj25hhm6tkfv4','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1462162761),
	('bqkhd0hgh9v5u0q80q0b2lq1v2','a55db367839d193fa45c98d64047868c',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1460942046),
	('db80e8t3685vohnooier1imf31','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463014755),
	('ehhpuh7q68rnmsgblpmom2vld2','581b2784fe646d57706ad26dbefd9208',X'637372665F746F6B656E7C733A33323A223430656530363464663363336335316135623962333639353265376532313738223B637372665F746F6B656E5F74696D657C693A313436333238383735383B',1463290198),
	('eq63c5qck33mtqgqnk0ssr26c4','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462667971),
	('g5pha2uk7crc9sq8ji4sinjlq6','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461452897),
	('gd0unhl7htvnqp1di7mhihs4d3','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1463356511),
	('gjhvsl86vq4r00c3sqk5phocp5','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462540059),
	('ho9mfk1s9vd9r6g2f8uhbhp4d5','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462318697),
	('iismld7jj13f3rhim6r86llhn2','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462492749),
	('jjsqi5qdq939vch0rolvus2ag2','46bfbf8e2abb836fd5cb1706a683a843',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463356474),
	('k1imegmh02cmgi24p716eca5m2','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461623798),
	('kcrc0j9g0r5bp88isovndu8ka7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461707099),
	('kdcskbihpeeoq735a65k2sjh32','a55db367839d193fa45c98d64047868c',X'5265646972656374546F7C733A31393A2273746F72652F696E666F2F646F636E696E6A61223B',1460940373),
	('klmpjur0phoff1eur2moi6hod2','e5ecbdd3c2000e070635af69c43e4f69',X'757365725F69647C733A313A2231223B757365725F6E616D657C733A343A2264656D6F223B757365725F656D61696C7C733A31333A2264656D6F4064656D6F2E636F6D223B757365725F6163636F756E745F747970657C733A313A2237223B757365725F70726F76696465725F747970657C733A373A2244454641554C54223B757365725F6176617461725F66696C657C733A34353A22687474703A2F2F636F7572736573756974652E6672756D626572742E6F72672F617661746172732F312E6A7067223B757365725F67726176617461725F696D6167655F75726C7C733A37383A22687474703A2F2F7777772E67726176617461722E636F6D2F6176617461722F35333434346639316536393863306337636161326462633362646266393366633F733D343426643D6D6D26723D7067223B757365725F6C6F676765645F696E7C623A313B',1462163362),
	('knnehc4mu7hvvrv3ilhdnr32q3','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461518266),
	('l10blldn0dopg70mac38liodg3','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462227847),
	('msbnho7fqfklr4vrnsuer3qj20','bf9b586494ddb1bdc3a326036758a182',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461154981),
	('n5pgtchmdvrm0boit39pmei5k0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461655811),
	('qocj8nbp12ohpo7uln54k5mj85','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1463349404),
	('qp6dm93gstu3sidnjrnr0pahv5','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1463034821),
	('reaeit1okd31cg9k3bdptan1u5','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462919477),
	('s36ib1n9pfd56c2fmh9bkpljb6','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461186631),
	('s3vofumldm7p26dp22kt9e3ji0','26561cf3a2bd47a1b17c7b2f875eceae',X'637372665F746F6B656E7C733A33323A226661336432366631333635316634346339643533633361653035346562356434223B637372665F746F6B656E5F74696D657C693A313436333131393035323B5265646972656374546F7C733A32333A2273746F72652F696E666F2F636F757273656275696C6472223B',1463120651),
	('s6sp8p7e1kqo5qfok0hhahghc7','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1460946964),
	('s7cebvgkntoo5gveoavc99m0n0','36fbe26d2f9de9abd5c2ddc8a22c2a05','',1463113943),
	('s7eqpgh3td56oa6juuslosenu6','6db04972a6135f93444b5eb7e6c17e69',X'5265646972656374546F7C733A31393A2273746F72652F696E666F2F646F636E696E6A61223B666565646261636B5F706F7369746976657C4E3B666565646261636B5F6E656761746976657C4E3B637372665F746F6B656E7C733A33323A223239613938386633383139656237643665653930633963373064373331383064223B637372665F746F6B656E5F74696D657C693A313436313733313733393B',1461733179),
	('t5qegoen66ikf6fm09fvtgl860','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462611803),
	('t9qr9ak40fc8jefa68bshho1j0','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1461879968),
	('v0n3bkni4176v17di64jq46861','f34c5800ed8bca5c940df91660d80ee3',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462839293),
	('v1pub35q9a2iegokd6gs1rcph5','f34c5800ed8bca5c940df91660d80ee3',X'637372665F746F6B656E7C733A33323A226439396563633162323437656332656337346263386566396534626366343235223B637372665F746F6B656E5F74696D657C693A313436333238383735383B',1463290198),
	('vnilj2ggtlnca6b9frrb2ua4d2','a55db367839d193fa45c98d64047868c','',1460942047),
	('vp6t7uvvnhc4o8mfbe86u0uhh6','a55db367839d193fa45c98d64047868c',X'5265646972656374546F7C733A31323A2273746F72652F696E6465782F223B',1462424381);

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
	(7,3,7,4);

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
	(1,'Rapid SCORM content authoring.','Access all apps with one subscription','section scorm',1,1,'<p>Subscribing will give you access to <i>all</i> the apps listed below; different payment tiers give you more (or less) features in each app. Check the app information pages for more information on tiers and features.</p>\n<div class=\"cs-bracket-design top\"></div>','<div class=\"cs-bracket-design bottom\"></div>\n<p><small><sup>*</sup>Apps are web based and require a modern standards-based desktop browser to run such as Chrome, Safari, Vivaldi or Firefox; others may not be supported.</small></p>'),
	(2,'','Open source software for Moodle sites.','section opensource',1,2,'<p>Extend your Moodle site with our open source plugins. Click the launch links below to be taken to demo pages for each of the plugins, then from there to download the source.</p>',NULL),
	(3,'SCORM Course Packages','','section ourcourses',0,999,'<p>You can buy our online courses here. You want to buy them because they are better than yours. Something something, I made this up. Stop reading now.</p>',NULL);

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
  `status` varchar(20) DEFAULT NULL,
  `statusReason` varchar(20) DEFAULT NULL,
  `testMode` tinyint(1) NOT NULL DEFAULT '0',
  `active` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`subscription_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `subscriptions` WRITE;
/*!40000 ALTER TABLE `subscriptions` DISABLE KEYS */;

INSERT INTO `subscriptions` (`subscription_id`, `user_id`, `tier_id`, `added`, `endDate`, `referenceId`, `status`, `statusReason`, `testMode`, `active`)
VALUES
	(1,3,2,'2016-04-14 16:01:00','2016-03-31',NULL,NULL,NULL,0,0);

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


# Dump of table tiers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `tiers`;

CREATE TABLE `tiers` (
  `tier_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `tier_level` int(11) unsigned NOT NULL DEFAULT '0',
  `app_ids` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL DEFAULT '',
  `description` text,
  `added` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `store_url` varchar(255) DEFAULT NULL,
  `active` int(10) unsigned NOT NULL DEFAULT '0',
  `price` int(11) DEFAULT NULL,
  `currency` varchar(10) DEFAULT NULL,
  `period` varchar(10) DEFAULT NULL,
  PRIMARY KEY (`tier_id`),
  UNIQUE KEY `tier_level` (`tier_level`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

LOCK TABLES `tiers` WRITE;
/*!40000 ALTER TABLE `tiers` DISABLE KEYS */;

INSERT INTO `tiers` (`tier_id`, `tier_level`, `app_ids`, `name`, `description`, `added`, `store_url`, `active`, `price`, `currency`, `period`)
VALUES
	(1,0,'1,2,3','Copper',NULL,'2016-04-11 12:55:25','http://sites.fastspring.com/coursesuite/product/copper-tier',1,99,'USD','m'),
	(2,1,'1,2,3','Jade',NULL,'2016-04-11 12:55:28','http://sites.fastspring.com/coursesuite/product/jade-tier ',1,169,'USD','m'),
	(3,2,'1,2,3','Crystal',NULL,'2016-04-11 12:55:31','http://sites.fastspring.com/coursesuite/product/crystal-tier ',1,1900,'USD','y'),
	(4,3,'7','Anorak',NULL,'2016-03-23 21:21:11',NULL,0,NULL,NULL,NULL),
	(5,4,'7','Parzival',NULL,'2016-03-23 21:21:12',NULL,0,NULL,NULL,NULL),
	(6,5,'7','Artemis',NULL,'2016-03-23 21:21:13',NULL,0,NULL,NULL,NULL);

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
	(1,'vukano4knhne7ncfi1chmhms01','demo','$2y$10$OvprunjvKOOhM1h9bzMPs.vuwGIsOqZbw88rzSyGCTJTcE61g5WXi','demo@demo.com',1,0,7,1,NULL,1422205178,NULL,1463127038,0,NULL,NULL,'1622283e3ff82568b3f60d51c6bcf35a35b679ec',1453943586,'DEFAULT'),
	(2,NULL,'demo2','$2y$10$OvprunjvKOOhM1h9bzMPs.vuwGIsOqZbw88rzSyGCTJTcE61g5WXi','demo2@demo.com',1,0,1,0,NULL,1422205178,NULL,1454738793,0,NULL,NULL,NULL,NULL,'DEFAULT'),
	(3,NULL,'frumbert','$2y$10$KvmVg2B83jl6OgDDKuQKleuk4Ij77XbR3X5Qs5WVESInqN6O.DTr.','tim@avide.com.au',1,0,2,1,NULL,1454886223,NULL,1462159133,0,NULL,NULL,NULL,NULL,'DEFAULT'),
	(4,NULL,'tim','$2y$10$DwqleDfsb3U.Fn2zRFQq8OPMixWsIjgeZS9XifhzIJPck9MsZ9Kg2','tim@coursesuite.com.au',0,0,1,0,NULL,1462158701,NULL,NULL,0,NULL,'5c1d14bf9270189f80329dd6ff1e938aaf107add',NULL,NULL,'DEFAULT'),
	(5,NULL,'Craig','$2y$10$MNBQvaxRXxdGCfLkBWq4de6dZMzAoc113BZQ.SeRdXkUWa3HHHrHy','craig@coursesuite.com.au',0,0,1,0,NULL,1463114713,NULL,NULL,0,NULL,'4a57241dcf9adc6219efc418c09ae9bcd313532a',NULL,NULL,'DEFAULT');

/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
