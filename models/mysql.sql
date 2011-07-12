SET NAMES utf8;
SET foreign_key_checks = 0;
SET time_zone = 'SYSTEM';
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

DROP TABLE IF EXISTS `gallery`;
CREATE TABLE `gallery` (
  `gallery_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `namespace` varchar(50) DEFAULT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`gallery_id`),
  KEY `namespace` (`namespace`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `gallery_extended`;
CREATE TABLE `gallery_extended` (
  `gallery_id` int(10) unsigned NOT NULL,
  `title` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL DEFAULT '',
  UNIQUE KEY `gallery_id` (`gallery_id`),
  CONSTRAINT `gallery_extended_ibfk_2` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`gallery_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `gallery_photo`;
CREATE TABLE `gallery_photo` (
  `photo_id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `gallery_id` int(10) unsigned NOT NULL,
  `filename` varchar(100) NOT NULL,
  `ordering` int(10) unsigned NOT NULL,
  `is_active` bit(1) NOT NULL DEFAULT b'1',
  PRIMARY KEY (`photo_id`),
  KEY `gallery_id` (`gallery_id`),
  CONSTRAINT `gallery_photo_ibfk_2` FOREIGN KEY (`gallery_id`) REFERENCES `gallery` (`gallery_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `gallery_photo_extended`;
CREATE TABLE `gallery_photo_extended` (
  `photo_id` int(10) unsigned NOT NULL,
  `title` varchar(255) NOT NULL,
  UNIQUE KEY `photo_id` (`photo_id`),
  CONSTRAINT `gallery_photo_extended_ibfk_2` FOREIGN KEY (`photo_id`) REFERENCES `gallery_photo` (`photo_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
