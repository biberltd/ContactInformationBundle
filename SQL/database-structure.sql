/*
Navicat MariaDB Data Transfer

Source Server         : localmariadb
Source Server Version : 100108
Source Host           : localhost:3306
Source Database       : bod_core

Target Server Type    : MariaDB
Target Server Version : 100108
File Encoding         : 65001

Date: 2015-12-12 11:55:34
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for contact_information_type
-- ----------------------------
DROP TABLE IF EXISTS `contact_information_type`;
CREATE TABLE `contact_information_type` (
  `id` int(5) unsigned NOT NULL,
  `code` varchar(155) COLLATE utf8_turkish_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUContactInformationTypeCode` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of contact_information_type
-- ----------------------------

-- ----------------------------
-- Table structure for contact_information_type_localization
-- ----------------------------
DROP TABLE IF EXISTS `contact_information_type_localization`;
CREATE TABLE `contact_information_type_localization` (
  `contact_information_type` int(5) unsigned NOT NULL,
  `language` int(5) unsigned NOT NULL,
  `name` varchar(155) COLLATE utf8_turkish_ci NOT NULL,
  `url_key` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  PRIMARY KEY (`contact_information_type`,`language`),
  UNIQUE KEY `idxULocalizedContactInformationType` (`contact_information_type`,`language`),
  UNIQUE KEY `idxULocalizedContactInformationTypeUrlKey` (`contact_information_type`,`language`,`url_key`),
  KEY `idxFContactInformationTypleLocalizationLanguage` (`language`),
  CONSTRAINT `idxFContactInformationTypleLocalizationLanguage` FOREIGN KEY (`language`) REFERENCES `language` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFLocalizedContactInformationType` FOREIGN KEY (`contact_information_type`) REFERENCES `contact_information_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of contact_information_type_localization
-- ----------------------------

-- ----------------------------
-- Table structure for email_address
-- ----------------------------
DROP TABLE IF EXISTS `email_address`;
CREATE TABLE `email_address` (
  `id` int(10) unsigned NOT NULL,
  `email` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUEmailAddress` (`email`),
  UNIQUE KEY `idxUEmailAddressId` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of email_address
-- ----------------------------

-- ----------------------------
-- Table structure for email_addresses_of_member
-- ----------------------------
DROP TABLE IF EXISTS `email_addresses_of_member`;
CREATE TABLE `email_addresses_of_member` (
  `email_address` int(20) unsigned NOT NULL,
  `type` int(5) unsigned DEFAULT NULL,
  `member` int(255) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`email_address`,`member`),
  UNIQUE KEY `idxUEmailAddressOfMember` (`email_address`,`member`),
  KEY `idxFMemberOfEmailAddress` (`member`),
  KEY `idxFTypeOfEmailAddress` (`type`),
  CONSTRAINT `idxFEmailAddressOfMember` FOREIGN KEY (`email_address`) REFERENCES `email_address` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFMemberOfEmailAddress` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFTypeOfEmailAddress` FOREIGN KEY (`type`) REFERENCES `contact_information_type` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of email_addresses_of_member
-- ----------------------------

-- ----------------------------
-- Table structure for phone_number
-- ----------------------------
DROP TABLE IF EXISTS `phone_number`;
CREATE TABLE `phone_number` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT 'System given id.',
  `country_code` varchar(5) COLLATE utf8_turkish_ci NOT NULL,
  `area_code` varchar(4) COLLATE utf8_turkish_ci NOT NULL,
  `number` varchar(7) COLLATE utf8_turkish_ci NOT NULL,
  `extension` varchar(4) COLLATE utf8_turkish_ci DEFAULT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  `type` char(1) COLLATE utf8_turkish_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `idxUPhoneNumber` (`id`,`country_code`,`area_code`,`number`),
  UNIQUE KEY `isxUPhoneNumberId` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of phone_number
-- ----------------------------

-- ----------------------------
-- Table structure for phone_numbers_of_member
-- ----------------------------
DROP TABLE IF EXISTS `phone_numbers_of_member`;
CREATE TABLE `phone_numbers_of_member` (
  `phone_number` bigint(20) unsigned NOT NULL,
  `member` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`phone_number`),
  UNIQUE KEY `idxUPhoneNumberOfMember` (`phone_number`,`member`),
  KEY `idxFMemberOfPhoneNumber` (`member`),
  CONSTRAINT `idxFMemberOfPhoneNumber` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `idxFPhoneNumberOfMember` FOREIGN KEY (`phone_number`) REFERENCES `phone_number` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of phone_numbers_of_member
-- ----------------------------

-- ----------------------------
-- Table structure for social_account
-- ----------------------------
DROP TABLE IF EXISTS `social_account`;
CREATE TABLE `social_account` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(255) COLLATE utf8_turkish_ci NOT NULL,
  `network` char(1) COLLATE utf8_turkish_ci NOT NULL,
  `member` int(10) unsigned NOT NULL,
  `date_added` datetime NOT NULL,
  `date_updated` datetime NOT NULL,
  `date_removed` datetime DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idxFOwnerOfSocialAccount` (`member`),
  CONSTRAINT `idxFOwnerOfSocialAccount` FOREIGN KEY (`member`) REFERENCES `member` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_turkish_ci;

-- ----------------------------
-- Records of social_account
-- ----------------------------
