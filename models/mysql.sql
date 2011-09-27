-- Adminer 3.3.3 MySQL dump

SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `gallery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(50) NOT NULL DEFAULT '',
  `description` text,
  `namespace_id` int(10) unsigned NOT NULL DEFAULT '1',
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`gallery_id`),
  KEY `namespace` (`namespace_id`),
  CONSTRAINT `gallery_ibfk_1` FOREIGN KEY (`namespace_id`) REFERENCES `gallery_namespace` (`namespace_id`) ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `gallery_namespace`;
CREATE TABLE `gallery_namespace` (
  `namespace_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`namespace_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `gallery_namespace` (`namespace_id`, `name`) VALUES
(1,	'default');

DROP TABLE IF EXISTS `gallery_photo`;
CREATE TABLE `gallery_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gallery_id` int(10) unsigned NOT NULL,
  `filename` varchar(100) NOT NULL,
  `title` varchar(255) NOT NULL DEFAULT '',
  `ordering` int(10) unsigned NOT NULL,
  `is_active` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`photo_id`),
  KEY `gallery_id` (`gallery_id`),
  CONSTRAINT `gallery_photo_ibfk_2` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`gallery_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
