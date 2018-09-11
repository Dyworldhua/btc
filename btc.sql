/*
Navicat MySQL Data Transfer

Source Server         : root
Source Server Version : 50553
Source Host           : localhost:3306
Source Database       : btc

Target Server Type    : MYSQL
Target Server Version : 50553
File Encoding         : 65001

Date: 2017-12-29 18:14:02
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for btc_member
-- ----------------------------
DROP TABLE IF EXISTS `btc_member`;
CREATE TABLE `btc_member` (
  `uid` int(10) NOT NULL AUTO_INCREMENT,
  `username` char(10) NOT NULL COMMENT '用户名',
  `password` char(32) NOT NULL COMMENT '密码',
  `addTime` int(10) NOT NULL COMMENT '注册时间',
  `balance` char(10) NOT NULL COMMENT '账户余额',
  `sort` char(10) NOT NULL COMMENT '加密盐',
  PRIMARY KEY (`uid`),
  KEY `user_login_key` (`username`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- ----------------------------
-- Records of btc_member
-- ----------------------------
