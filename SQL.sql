-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 30, 2020 at 08:59 AM
-- Server version: 5.7.31
-- PHP Version: 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `troop`
--

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

DROP TABLE IF EXISTS `awards`;
CREATE TABLE IF NOT EXISTS `awards` (
  `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT,
  `title` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `award_troopers`
--

DROP TABLE IF EXISTS `award_troopers`;
CREATE TABLE IF NOT EXISTS `award_troopers` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `trooperid` int(11) NOT NULL,
  `awardid` int(11) NOT NULL,
  `awarded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
CREATE TABLE IF NOT EXISTS `comments` (
  `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT,
  `troopid` int(100) NOT NULL,
  `trooperid` int(100) NOT NULL,
  `comment` text NOT NULL,
  `important` int(1) NOT NULL,
  `posted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `costumes`
--

DROP TABLE IF EXISTS `costumes`;
CREATE TABLE IF NOT EXISTS `costumes` (
  `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT,
  `costume` varchar(50) NOT NULL,
  `era` int(2) NOT NULL,
  `club` int(2) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `venue` varchar(100) NOT NULL,
  `dateStart` datetime NOT NULL,
  `dateEnd` datetime NOT NULL,
  `website` varchar(500) NOT NULL,
  `numberOfAttend` int(10) NOT NULL,
  `requestedNumber` int(10) NOT NULL,
  `requestedCharacter` text,
  `secureChanging` tinyint(1) NOT NULL,
  `blasters` tinyint(1) NOT NULL,
  `lightsabers` tinyint(1) NOT NULL,
  `parking` tinyint(1) NOT NULL,
  `mobility` tinyint(1) NOT NULL,
  `amenities` text,
  `referred` text,
  `comments` text,
  `location` varchar(500) NOT NULL,
  `label` varchar(100) NOT NULL,
  `postComment` text,
  `notes` text,
  `limitedEvent` tinyint(1) NOT NULL,
  `limitTo` int(2) NOT NULL,
  `limitRebels` int(10) NOT NULL DEFAULT '9999',
  `limit501st` int(10) NOT NULL DEFAULT '9999',
  `limitMando` int(10) NOT NULL DEFAULT '9999',
  `limitDroid` int(10) NOT NULL DEFAULT '9999',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `moneyRaised` int(100) NOT NULL DEFAULT '0',
  `squad` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_comments`
--

DROP TABLE IF EXISTS `event_comments`;
CREATE TABLE IF NOT EXISTS `event_comments` (
  `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trooperid` int(11) NOT NULL,
  `troopid` int(10) NOT NULL,
  `comment` text,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_sign_up`
--

DROP TABLE IF EXISTS `event_sign_up`;
CREATE TABLE IF NOT EXISTS `event_sign_up` (
  `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trooperid` int(100) NOT NULL,
  `troopid` int(100) NOT NULL,
  `costume` varchar(50) NOT NULL,
  `costume_backup` varchar(50) NOT NULL DEFAULT '-1',
  `reason` text,
  `status` int(2) NOT NULL DEFAULT '0',
  `attend` int(2) NOT NULL DEFAULT '0',
  `attended_costume` varchar(100) NOT NULL DEFAULT '-1',
  `signuptime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `troopers`
--

DROP TABLE IF EXISTS `troopers`;
CREATE TABLE IF NOT EXISTS `troopers` (
  `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT,
  `name` varchar(20) NOT NULL,
  `email` varchar(240) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `squad` int(10) NOT NULL,
  `permissions` int(2) NOT NULL DEFAULT '0',
  `tkid` varchar(20) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `last_active` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved` int(1) NOT NULL DEFAULT '0',
  `subscribe` int(11) NOT NULL DEFAULT '1',
  `datecreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
