/*
Navicat MySQL Data Transfer

Source Server         : localhost_3306321
Source Server Version : 50547
Source Host           : 127.0.0.1:3306
Source Database       : bcjmjx

Target Server Type    : MYSQL
Target Server Version : 50547
File Encoding         : 65001

Date: 2017-07-06 15:26:11
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for `bc_admin_user`
-- ----------------------------
DROP TABLE IF EXISTS `bc_admin_user`;
CREATE TABLE `bc_admin_user` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `password` varchar(32) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_admin_user
-- ----------------------------
INSERT INTO `bc_admin_user` VALUES ('1', 'admin', '7fef6171469e80d32c0559f88b377245');

-- ----------------------------
-- Table structure for `bc_equipment`
-- ----------------------------
DROP TABLE IF EXISTS `bc_equipment`;
CREATE TABLE `bc_equipment` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `cid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_equipment
-- ----------------------------
INSERT INTO `bc_equipment` VALUES ('1', '立式加工中心', '4');

-- ----------------------------
-- Table structure for `bc_equipment_category`
-- ----------------------------
DROP TABLE IF EXISTS `bc_equipment_category`;
CREATE TABLE `bc_equipment_category` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_equipment_category
-- ----------------------------
INSERT INTO `bc_equipment_category` VALUES ('1', '立式加工中心');
INSERT INTO `bc_equipment_category` VALUES ('2', '西安市1');
INSERT INTO `bc_equipment_category` VALUES ('4', '加工中心系列');

-- ----------------------------
-- Table structure for `bc_region`
-- ----------------------------
DROP TABLE IF EXISTS `bc_region`;
CREATE TABLE `bc_region` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(150) NOT NULL,
  `fid` int(11) unsigned NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_region
-- ----------------------------
INSERT INTO `bc_region` VALUES ('1', '陕西省', '0');
INSERT INTO `bc_region` VALUES ('2', '西安市', '1');
INSERT INTO `bc_region` VALUES ('4', '山西省', '0');
INSERT INTO `bc_region` VALUES ('5', '山东省', '0');
INSERT INTO `bc_region` VALUES ('6', '渭南市', '1');

-- ----------------------------
-- Table structure for `bc_repair`
-- ----------------------------
DROP TABLE IF EXISTS `bc_repair`;
CREATE TABLE `bc_repair` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `username` varchar(150) NOT NULL,
  `rid` int(11) unsigned NOT NULL,
  `password` varchar(32) NOT NULL,
  `address` varchar(255) NOT NULL COMMENT '地址',
  `contacts` varchar(100) NOT NULL COMMENT '联系人',
  `phone` varchar(15) NOT NULL COMMENT '联系电话',
  `email` varchar(200) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_repair
-- ----------------------------
INSERT INTO `bc_repair` VALUES ('1', '阿达', '2', 'e10adc3949ba59abbe56e057f20f883e', '啊实打实大大', '撒大声地', '15895959595', '2536411@qq.com');
INSERT INTO `bc_repair` VALUES ('2', 'wood', '2', 'e10adc3949ba59abbe56e057f20f883e', '啊实打实大大da', '撒大声地', '15895959595', '2536411@qq.com');

-- ----------------------------
-- Table structure for `bc_repair_list`
-- ----------------------------
DROP TABLE IF EXISTS `bc_repair_list`;
CREATE TABLE `bc_repair_list` (
  `id` int(11) unsigned NOT NULL,
  `username` varchar(100) NOT NULL,
  `rid` int(11) unsigned NOT NULL COMMENT '地区',
  `contacts` varchar(100) NOT NULL COMMENT '联系人',
  `phone` varchar(15) NOT NULL COMMENT '联系电话',
  `email` varchar(200) NOT NULL COMMENT '邮箱',
  `cid` int(11) unsigned NOT NULL COMMENT '产品联动菜单',
  `eq_id` int(11) NOT NULL COMMENT '设备型号',
  `eq_num` varchar(200) NOT NULL COMMENT '设备编号',
  `factory_date` varchar(25) NOT NULL COMMENT '出厂日期',
  `info` text NOT NULL COMMENT '设备故障描述',
  `address` varchar(200) NOT NULL COMMENT '详细地址',
  `eq_info` varchar(200) NOT NULL COMMENT '没有匹配型号的话 备注 写入',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_repair_list
-- ----------------------------
INSERT INTO `bc_repair_list` VALUES ('1', '阿达', '2', '撒大声地', '15895959595', '2536411@qq.com', '1', '0', '撒大大', '2017-06-23', '<p>啊实打实大</p>', '', '');
INSERT INTO `bc_repair_list` VALUES ('2', '阿达', '2', '撒大声地', '15895959595', '2536411@qq.com', '1', '0', '撒大大', '2017-06-23', '<p>啊实打实大</p>', '', '');
INSERT INTO `bc_repair_list` VALUES ('3', '阿达', '2', '撒大声地', '15895959595', '2536411@qq.com', '1', '0', '撒大大', '2017-06-23', '<p>啊实打实大</p>', '', '');
INSERT INTO `bc_repair_list` VALUES ('4', 'wood', '2', '撒大声地', '15895959595', '2536411@qq.com', '1', '0', 'asdasdsd', '2017-06-23', '<p>按时打算打算打算</p>', '', '');
INSERT INTO `bc_repair_list` VALUES ('5', 'wood', '2', '撒大声地', '15895959595', '2536411@qq.com', '1', '0', 'asdasdsd', '2017-06-23', '<p>按时打算打算打算</p>', '', '');
INSERT INTO `bc_repair_list` VALUES ('6', '123456', '2', '撒大声地', '15895959595', '2536411@qq.com', '4', '0', 'asdasdsd', '2017-06-29', '<p>啊实打实大厦上的公司对光反射大公司的公司的</p>', '', '');

-- ----------------------------
-- Table structure for `bc_repair_order`
-- ----------------------------
DROP TABLE IF EXISTS `bc_repair_order`;
CREATE TABLE `bc_repair_order` (
  `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `order_sn` bigint(13) unsigned NOT NULL,
  `mobile` varchar(12) NOT NULL,
  `repair` int(11) unsigned NOT NULL COMMENT '报修人标识',
  `type` tinyint(1) unsigned NOT NULL COMMENT '保修进度',
  `answer` text NOT NULL COMMENT '回复',
  `addtime` int(11) unsigned NOT NULL COMMENT '添加时间',
  `endtime` int(11) unsigned NOT NULL COMMENT '订单完成时间',
  PRIMARY KEY (`id`),
  KEY `repair` (`repair`)
) ENGINE=MyISAM AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of bc_repair_order
-- ----------------------------
INSERT INTO `bc_repair_order` VALUES ('1', '1498117071315', '4294967295', '0', '0', '', '0', '0');
INSERT INTO `bc_repair_order` VALUES ('2', '1498117102121', '4294967295', '0', '0', '', '0', '0');
INSERT INTO `bc_repair_order` VALUES ('3', '1498117148689', '4294967295', '0', '0', '', '0', '0');
INSERT INTO `bc_repair_order` VALUES ('4', '1498117218499', '4294967295', '1', '1', '', '0', '0');
INSERT INTO `bc_repair_order` VALUES ('5', '1498117249860', '4294967295', '2', '4', '<p>asdasdasdasdasdas萨达</p>', '0', '1498878837');
INSERT INTO `bc_repair_order` VALUES ('6', '1498727427628', '4294967295', '0', '0', '', '1498727427', '0');
INSERT INTO `bc_repair_order` VALUES ('7', '1499066246685', '15891497899', '1', '1', '', '1499066246', '0');
