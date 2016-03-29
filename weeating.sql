-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: 2016-03-28 21:02:28
-- 服务器版本： 5.7.9
-- PHP Version: 7.0.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `weeating`
--

-- --------------------------------------------------------

--
-- 表的结构 `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `department` int(2) NOT NULL,
  `nickname` varchar(20) NOT NULL,
  `openid` varchar(30) DEFAULT NULL,
  `amount` int(2) DEFAULT NULL COMMENT '订餐数量',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;

--
-- 转存表中的数据 `members`
--

INSERT INTO `members` (`id`, `department`, `nickname`, `openid`, `amount`) VALUES
(1, 3, 'halion', NULL, 1),
(2, 3, '自愚自樂', NULL, 1),
(3, 3, 'yancy', NULL, 6);

-- --------------------------------------------------------

--
-- 表的结构 `remind`
--

DROP TABLE IF EXISTS `remind`;
CREATE TABLE IF NOT EXISTS `remind` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `type` int(2) NOT NULL COMMENT '提醒类型',
  `is_time` int(1) NOT NULL COMMENT '是否到位',
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COMMENT='事务提醒表';

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
