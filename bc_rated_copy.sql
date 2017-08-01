/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306321
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : bcjmjx

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-07-21 11:36:33
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bc_rated_copy`
-- ----------------------------
DROP TABLE IF EXISTS `bc_rated`;
CREATE TABLE `bc_rated` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_id` int(11) unsigned NOT NULL COMMENT '订单唯一标识',
  `mark` tinyint(1) unsigned NOT NULL COMMENT '评分',
  `content` text COMMENT '内容',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_rated_copy
-- ----------------------------
