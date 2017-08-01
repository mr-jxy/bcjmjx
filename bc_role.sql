/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306321
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : bcjmjx

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-07-13 15:03:56
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bc_role`
-- ----------------------------
DROP TABLE IF EXISTS `bc_role`;
CREATE TABLE `bc_role` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `rules` varchar(200) NOT NULL COMMENT '权限组',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_role
-- ----------------------------
INSERT INTO `bc_role` VALUES ('1', '洪七', '1');

-- ----------------------------
-- Table structure for `bc_rule`
-- ----------------------------
DROP TABLE IF EXISTS `bc_rule`;
CREATE TABLE `bc_rule` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `title` varchar(100) NOT NULL,
  `fid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_rule
-- ----------------------------
INSERT INTO `bc_rule` VALUES ('1', 'admin/index', '管理员模块', '0');
INSERT INTO `bc_rule` VALUES ('3', 'admin/add', '管理员添加', '1');
