# ************************************************************
# Sequel Pro SQL dump
# Version 4541
#
# http://www.sequelpro.com/
# https://github.com/sequelpro/sequelpro
#
# Host: 127.0.0.1 (MySQL 5.5.5-10.2.10-MariaDB)
# Database: cs_preprod
# Generation Time: 2018-04-17 06:13:37 +0000
# ************************************************************


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table _app_tiers
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_app_tiers`;



# Dump of table _bundle
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_bundle`;



# Dump of table _bundle_apps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_bundle_apps`;



# Dump of table _categories
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_categories`;



# Dump of table _mail_templates
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_mail_templates`;



# Dump of table _mail_templates_published
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_mail_templates_published`;



# Dump of table _orgs
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_orgs`;



# Dump of table _product
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_product`;



# Dump of table _store_section_apps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `_store_section_apps`;



# Dump of table api_limits
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_limits`;

CREATE TABLE `api_limits` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `digest_user` varchar(20) NOT NULL DEFAULT '',
  `usage_cap` smallint(3) unsigned NOT NULL DEFAULT 65535,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table api_requests
# ------------------------------------------------------------

DROP TABLE IF EXISTS `api_requests`;

CREATE TABLE `api_requests` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `digest_user` varchar(64) DEFAULT NULL,
  `token` varchar(255) DEFAULT NULL,
  `publish_url` varchar(255) DEFAULT NULL,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `month` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table app_section
# ------------------------------------------------------------

DROP TABLE IF EXISTS `app_section`;

CREATE TABLE `app_section` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(10) unsigned NOT NULL,
  `sort` smallint(5) unsigned NOT NULL DEFAULT 999,
  `classname` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `colour` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `content` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `app_id` (`app_id`),
  CONSTRAINT `app_section_ibfk_1` FOREIGN KEY (`app_id`) REFERENCES `apps` (`app_id`) ON DELETE CASCADE ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table applog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `applog`;

CREATE TABLE `applog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `method_name` varchar(50) DEFAULT '',
  `digest_user` varchar(32) DEFAULT '',
  `added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `message` varchar(255) DEFAULT '',
  `param0` text DEFAULT NULL,
  `param1` text DEFAULT NULL,
  `param2` text DEFAULT NULL,
  `param3` text DEFAULT NULL,
  `param4` text DEFAULT NULL,
  `param5` text DEFAULT NULL,
  `param6` text DEFAULT NULL,
  `param7` text DEFAULT NULL,
  `param8` text DEFAULT NULL,
  `param9` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table apps
# ------------------------------------------------------------

DROP TABLE IF EXISTS `apps`;

CREATE TABLE `apps` (
  `app_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_key` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL DEFAULT '',
  `tagline` varchar(120) DEFAULT NULL,
  `whatisit` varchar(255) DEFAULT NULL,
  `icon` varchar(255) NOT NULL DEFAULT '',
  `url` varchar(255) DEFAULT '',
  `launch` varchar(255) NOT NULL DEFAULT '',
  `guide` varchar(255) DEFAULT NULL,
  `auth_type` tinyint(11) unsigned NOT NULL DEFAULT 0,
  `added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `active` tinyint(11) NOT NULL DEFAULT 0,
  `status` tinyint(11) DEFAULT 0 COMMENT 'released, beta, open source, etc',
  `description` text DEFAULT NULL COMMENT 'the nitty gritty',
  `media` text DEFAULT NULL COMMENT 'json object',
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `meta_title` varchar(100) DEFAULT NULL,
  `popular` tinyint(1) NOT NULL DEFAULT 0,
  `colour` varchar(10) DEFAULT NULL COMMENT 'base theme colour',
  `glyph` text DEFAULT NULL COMMENT 'graphic icon as svg',
  `files` text DEFAULT NULL COMMENT 'json object',
  `mods` text DEFAULT NULL COMMENT 'json object, api mods',
  `cssproperties` text DEFAULT NULL,
  PRIMARY KEY (`app_id`),
  UNIQUE KEY `app_key` (`app_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table blacklist
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blacklist`;

CREATE TABLE `blacklist` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `domain` varchar(100) NOT NULL DEFAULT '',
  `attempts` int(10) unsigned NOT NULL DEFAULT 0,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table blogentries
# ------------------------------------------------------------

DROP TABLE IF EXISTS `blogentries`;

CREATE TABLE `blogentries` (
  `entry_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL DEFAULT '',
  `slug` varchar(100) DEFAULT NULL,
  `long_entry` text DEFAULT NULL,
  `short_entry` text DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  `entry_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `published` tinyint(3) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`entry_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table changelog
# ------------------------------------------------------------

DROP TABLE IF EXISTS `changelog`;

CREATE TABLE `changelog` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(10) unsigned NOT NULL,
  `added` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `value` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table conversion_stats
# ------------------------------------------------------------

DROP TABLE IF EXISTS `conversion_stats`;

CREATE TABLE `conversion_stats` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `timetaken` int(10) unsigned NOT NULL COMMENT 'endtime-starttime',
  `filename` varchar(255) NOT NULL DEFAULT '',
  `extension` varchar(255) NOT NULL DEFAULT '',
  `size` int(10) unsigned DEFAULT NULL,
  `minutes` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table keystore
# ------------------------------------------------------------

DROP TABLE IF EXISTS `keystore`;

CREATE TABLE `keystore` (
  `key` varchar(40) NOT NULL DEFAULT '',
  `value` text NOT NULL,
  `lastupdated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table logons
# ------------------------------------------------------------

DROP TABLE IF EXISTS `logons`;

CREATE TABLE `logons` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `created` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `user` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `cookie` char(32) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT '',
  `ip` char(15) CHARACTER SET ascii DEFAULT NULL,
  `seen` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `source` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL COMMENT 'md5 of source account',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `message`;

CREATE TABLE `message` (
  `message_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned DEFAULT NULL COMMENT '0 = all',
  `created` timestamp NOT NULL DEFAULT current_timestamp(),
  `expires` timestamp NULL DEFAULT NULL,
  `level` tinyint(1) unsigned DEFAULT NULL,
  `text` text DEFAULT NULL,
  PRIMARY KEY (`message_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table messageread
# ------------------------------------------------------------

DROP TABLE IF EXISTS `messageread`;

CREATE TABLE `messageread` (
  `messageread_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `message_id` int(11) unsigned NOT NULL,
  `user_id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`messageread_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table product_bundle
# ------------------------------------------------------------

DROP TABLE IF EXISTS `product_bundle`;

CREATE TABLE `product_bundle` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `sort` smallint(5) unsigned NOT NULL DEFAULT 999,
  `product_key` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `app_ids` varchar(50) CHARACTER SET utf8 DEFAULT NULL,
  `store_url` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  `active` tinyint(3) unsigned NOT NULL DEFAULT 1,
  `label` varchar(100) CHARACTER SET utf8 DEFAULT NULL,
  `description` text CHARACTER SET utf8 DEFAULT NULL,
  `price` decimal(8,2) NOT NULL DEFAULT 0.00,
  `concurrency` smallint(5) unsigned NOT NULL DEFAULT 1,
  `icon` varchar(255) CHARACTER SET utf8 DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table session_data
# ------------------------------------------------------------

DROP TABLE IF EXISTS `session_data`;

CREATE TABLE `session_data` (
  `session_id` varchar(32) NOT NULL DEFAULT '',
  `hash` varchar(32) NOT NULL DEFAULT '',
  `session_data` blob NOT NULL,
  `session_expire` int(11) NOT NULL DEFAULT 0,
  `useragent` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table static_pages
# ------------------------------------------------------------

DROP TABLE IF EXISTS `static_pages`;

CREATE TABLE `static_pages` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `page_key` varchar(50) DEFAULT NULL,
  `body_classes` varchar(50) DEFAULT NULL,
  `content` text DEFAULT NULL,
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT '',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table store_sections
# ------------------------------------------------------------

DROP TABLE IF EXISTS `store_sections`;

CREATE TABLE `store_sections` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `label` varchar(100) DEFAULT NULL,
  `epiphet` varchar(255) DEFAULT NULL,
  `cssclass` varchar(100) DEFAULT NULL,
  `visible` tinyint(1) NOT NULL DEFAULT 1,
  `sort` smallint(2) unsigned NOT NULL DEFAULT 999,
  `html_pre` text DEFAULT NULL,
  `html_post` text DEFAULT NULL,
  `route` varchar(20) DEFAULT NULL COMMENT 'breadcrumb route',
  `routeLabel` varchar(50) DEFAULT NULL COMMENT 'breadcrumb label',
  `app_ids` varchar(50) DEFAULT NULL COMMENT 'csv of app ids',
  `meta_title` varchar(100) DEFAULT NULL,
  `meta_keywords` varchar(255) DEFAULT NULL,
  `meta_description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table subscriptions
# ------------------------------------------------------------

DROP TABLE IF EXISTS `subscriptions`;

CREATE TABLE `subscriptions` (
  `subscription_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `user_id` int(11) unsigned NOT NULL,
  `added` timestamp NOT NULL DEFAULT current_timestamp(),
  `endDate` date DEFAULT NULL,
  `referenceId` text DEFAULT NULL,
  `subscriptionUrl` text DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `statusReason` varchar(20) DEFAULT NULL,
  `testMode` tinyint(1) NOT NULL DEFAULT 0,
  `active` tinyint(1) NOT NULL DEFAULT 1,
  `info` varchar(250) DEFAULT NULL,
  `product_id` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`subscription_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `subscriptions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table systasks
# ------------------------------------------------------------

DROP TABLE IF EXISTS `systasks`;

CREATE TABLE `systasks` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `method` varchar(100) NOT NULL DEFAULT '',
  `frequency` varchar(100) NOT NULL DEFAULT '',
  `running` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `lastrun` bigint(11) unsigned NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



# Dump of table testimonials
# ------------------------------------------------------------

DROP TABLE IF EXISTS `testimonials`;

CREATE TABLE `testimonials` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `avatar` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `title` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `entry` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `published` tinyint(1) unsigned NOT NULL DEFAULT 0,
  `link` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `handle` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



# Dump of table users
# ------------------------------------------------------------

DROP TABLE IF EXISTS `users`;

CREATE TABLE `users` (
  `user_id` int(11) unsigned NOT NULL AUTO_INCREMENT COMMENT 'auto incrementing user_id of each user, unique index',
  `session_id` varchar(48) DEFAULT NULL,
  `user_name` varchar(255) DEFAULT '' COMMENT 'user''s name, unique',
  `user_password_hash` varchar(255) DEFAULT NULL COMMENT 'user''s password in salted and hashed format',
  `user_email` varchar(255) NOT NULL DEFAULT '' COMMENT 'user''s email, unique',
  `user_active` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'user''s activation status',
  `user_deleted` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'user''s deletion status',
  `user_account_type` tinyint(1) NOT NULL DEFAULT 1 COMMENT 'user''s account type (basic, premium, etc)',
  `user_has_avatar` tinyint(1) NOT NULL DEFAULT 0 COMMENT '1 if user has a local avatar, 0 if not',
  `user_remember_me_token` varchar(64) DEFAULT NULL COMMENT 'user''s remember-me cookie token',
  `user_creation_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the creation of user''s account',
  `user_suspension_timestamp` bigint(20) DEFAULT NULL COMMENT 'Timestamp till the end of a user suspension',
  `user_last_login_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of user''s last login',
  `user_failed_logins` tinyint(1) NOT NULL DEFAULT 0 COMMENT 'user''s failed login attempts',
  `user_last_failed_login` int(10) DEFAULT NULL COMMENT 'unix timestamp of last failed login attempt',
  `user_activation_hash` varchar(40) DEFAULT NULL COMMENT 'user''s email verification hash string',
  `user_password_reset_hash` char(40) DEFAULT NULL COMMENT 'user''s password reset code',
  `user_password_reset_timestamp` bigint(20) DEFAULT NULL COMMENT 'timestamp of the password reset request',
  `user_provider_type` text DEFAULT NULL,
  `user_newsletter_subscribed` tinyint(1) NOT NULL DEFAULT 0,
  `user_logon_count` bigint(20) unsigned NOT NULL DEFAULT 0,
  `user_logon_cap` tinyint(4) NOT NULL DEFAULT -1,
  `user_free_trial_available` tinyint(3) unsigned DEFAULT 1,
  `user_email_update` varchar(255) DEFAULT NULL COMMENT 'temporary email during update reverification',
  `change_verification_hash` varchar(40) DEFAULT NULL,
  `last_browser` varchar(255) DEFAULT NULL,
  `last_ip` varchar(255) DEFAULT NULL,
  `secret_key` text DEFAULT NULL,
  `user_parent_id` int(10) unsigned NOT NULL DEFAULT 0,
  `user_container` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='user data';



# Dump of table whitelabel
# ------------------------------------------------------------

DROP TABLE IF EXISTS `whitelabel`;

CREATE TABLE `whitelabel` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `subscription_id` int(10) unsigned NOT NULL,
  `app_key` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `html` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `css` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `template` blob DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `subscription_id` (`subscription_id`,`app_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;




/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
