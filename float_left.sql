/*
Navicat MySQL Data Transfer

Source Server         : tencent
Source Server Version : 50723
Source Host           : 118.25.212.166:3304
Source Database       : float_left

Target Server Type    : MYSQL
Target Server Version : 50723
File Encoding         : 65001

Date: 2019-01-14 17:56:48
*/

SET FOREIGN_KEY_CHECKS=0;

-- ----------------------------
-- Table structure for character
-- ----------------------------
DROP TABLE IF EXISTS `character`;
CREATE TABLE `character` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `health` int(10) DEFAULT NULL COMMENT '身体健康程度，这是一个百分比',
  `money` decimal(20,2) DEFAULT NULL COMMENT '角色的金钱',
  `stock` int(10) DEFAULT NULL COMMENT '仓库空间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for debt
-- ----------------------------
DROP TABLE IF EXISTS `debt`;
CREATE TABLE `debt` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `total_amount` decimal(10,2) NOT NULL COMMENT '全款',
  `principal` decimal(10,2) NOT NULL COMMENT '本金',
  `interest` decimal(10,2) NOT NULL COMMENT '利息',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for event
-- ----------------------------
DROP TABLE IF EXISTS `event`;
CREATE TABLE `event` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `context` varchar(128) NOT NULL COMMENT '内容',
  `buff` float(10,2) NOT NULL COMMENT '加成',
  `product_id` int(10) NOT NULL COMMENT '商品id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for inventory
-- ----------------------------
DROP TABLE IF EXISTS `inventory`;
CREATE TABLE `inventory` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `buy_price` decimal(10,2) NOT NULL COMMENT '购买价格',
  `quantity` int(10) NOT NULL COMMENT '数额',
  `product_id` int(11) NOT NULL COMMENT '商品的id',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for place
-- ----------------------------
DROP TABLE IF EXISTS `place`;
CREATE TABLE `place` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) NOT NULL COMMENT '地名',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for product
-- ----------------------------
DROP TABLE IF EXISTS `product`;
CREATE TABLE `product` (
  `id` int(10) NOT NULL,
  `name` varchar(30) NOT NULL COMMENT '商品名',
  `base_price` decimal(10,2) NOT NULL COMMENT '基本价格',
  `current_price` decimal(10,2) NOT NULL COMMENT '当前价格',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- ----------------------------
-- Table structure for product_of_place
-- ----------------------------
DROP TABLE IF EXISTS `product_of_place`;
CREATE TABLE `product_of_place` (
  `id` int(10) NOT NULL,
  `place_id` int(10) NOT NULL COMMENT '地点',
  `product_id` int(10) NOT NULL COMMENT '商品',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
