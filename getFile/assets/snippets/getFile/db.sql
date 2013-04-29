/*
Navicat MySQL Data Transfer

Source Server         : localhost
Source Server Version : 50168
Source Host           : localhost:3306
Source Database       : modx

Target Server Type    : MYSQL
Target Server Version : 50168
File Encoding         : 65001

Date: 2013-04-24 10:30:46
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `modx_downloads`
-- ----------------------------
DROP TABLE IF EXISTS `modx_downloads`;
CREATE TABLE `modx_downloads` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `file` varchar(255) DEFAULT NULL,
  `count` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of modx_downloads
-- ----------------------------
