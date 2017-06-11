CREATE TABLE `app_bundles` (
  `bundle_id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL COMMENT 'REDUNDANT',
  `display_name` varchar(50) NOT NULL,
  `description` text NOT NULL COMMENT 'html description',
  PRIMARY KEY (`bundle_id`)
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

INSERT INTO `app_bundles` (`bundle_id`, `product_id`, `display_name`, `description`)
VALUES
	(1, 7, 'Bundle 1', 'For when you want items wrapped fast with no fuss. Get SCORM compliant content in mere minutes with these easy content wrappers.'),
	(6, 12, 'Bundle 2', 'SDFadsfasdfasdf'),
	(7, 14, 'Ninja Suite', '3 main apps');

CREATE TABLE `bundle_products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bundle_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

INSERT INTO `bundle_products` (`id`, `bundle_id`, `product_id`)
VALUES
	(1, 1, 7),
	(2, 1, 13),
	(3, 6, 12),
	(4, 7, 14);

CREATE TABLE `store_product` (
  `product_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `purchase_url` varchar(255) DEFAULT NULL,
  `active` tinyint(1) NOT NULL DEFAULT '0',
  `name` varchar(100) DEFAULT NULL,
  `type` tinyint(3) unsigned NOT NULL DEFAULT '1' COMMENT '1 For single app, 2 for bundle',
  `price` int(10) unsigned NOT NULL DEFAULT '1',
  `tier` varchar(50) DEFAULT '1',
  PRIMARY KEY (`product_id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;

INSERT INTO `store_product` (`product_id`, `purchase_url`, `active`, `name`, `type`, `price`, `tier`)
VALUES
	(1, 'http://sites.fastspring.com/coursesuite/product/cs-document-t1', 0, 'cs-document-t1', 1, 1, 'Basic'),
	(2, 'http://sites.fastspring.com/coursesuite/product/cs-document-t2 ', 0, 'cs-document-t2', 1, 1, 'Pro'),
	(3, 'http://sites.fastspring.com/coursesuite/product/cs-media-t1', 0, 'cs-media-t1', 1, 1, 'Basic'),
	(4, 'http://sites.fastspring.com/coursesuite/product/cs-media-t2', 0, 'cs-media-t2', 1, 1, 'Pro'),
	(5, 'http://sites.fastspring.com/coursesuite/product/cs-coursebuilder-t1', 0, 'cs-coursebuilder-t1', 1, 1, 'Basic'),
	(6, 'http://sites.fastspring.com/coursesuite/product/cs-coursebuilder-t2', 0, 'cs-coursebuilder-t2', 1, 1, 'Pro'),
	(7, 'bundle1-t1', 0, 'cs-bundle1-t1', 2, 1, 'Basic'),
	(12, 'bundle2-t1', 0, 'cs-bundle2-t1', 2, 1, 'Basic'),
	(13, 'bundle1-t2', 0, 'cs-bundle1-t2', 2, 1, 'Pro'),
	(14, 'ninjasuite-t1', 1, 'cs-ninjasuite-t1', 2, 1, 'Basic');

CREATE TABLE `store_product_apps` (
  `row_id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `app_id` int(11) unsigned NOT NULL DEFAULT '0',
  `product_id` int(11) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`row_id`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

INSERT INTO `store_product_apps` (`row_id`, `app_id`, `product_id`)
VALUES
    (1, 1, 1),
	(2, 1, 2),
	(3, 2, 3),
	(4, 2, 4),
	(5, 3, 5),
	(6, 3, 6),
	(7, 1, 7),
	(8, 2, 7),
	(9, 3, 7),
	(21, 4, 7),
	(22, 1, 12),
	(23, 5, 12),
	(24, 3, 12),
	(25, 2, 12),
	(26, 3, 14),
	(27, 1, 14),
	(28, 2, 14),
	(59, 1, 13),
	(60, 2, 13),
	(61, 3, 13),
	(62, 4, 13);

INSERT INTO `orgs` (`org_id`, `name`, `logo_url`, `active`, `tier`, `header`, `css`)
VALUES
	(25, 'anmf', NULL, 1, 1, '{\"coursebuildr\":\"\"}', '{\"coursebuildr\":\"\"}');


