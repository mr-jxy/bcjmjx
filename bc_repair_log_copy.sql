/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306321
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : bcjmjx

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-07-21 11:07:57
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bc_repair_log_copy`
-- ----------------------------
DROP TABLE IF EXISTS `bc_repair_log`;
CREATE TABLE `bc_repair_log` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `change_time` int(11) unsigned NOT NULL COMMENT '改变时间',
  `img` varchar(255) NOT NULL COMMENT '改变图片',
  `state` varchar(255) NOT NULL COMMENT '说明',
  `order_id` int(11) NOT NULL,
  `type` tinyint(1) unsigned NOT NULL COMMENT '改变对应的状态',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_repair_log_copy
-- ----------------------------
