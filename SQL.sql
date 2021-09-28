-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 28, 2021 at 02:41 PM
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
  `oldid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `dateStart` datetime DEFAULT NULL,
  `dateEnd` datetime DEFAULT NULL,
  `website` varchar(500) DEFAULT NULL,
  `numberOfAttend` int(10) DEFAULT NULL,
  `requestedNumber` int(10) DEFAULT NULL,
  `requestedCharacter` text,
  `secureChanging` tinyint(1) DEFAULT NULL,
  `blasters` tinyint(1) DEFAULT NULL,
  `lightsabers` tinyint(1) DEFAULT NULL,
  `parking` tinyint(1) DEFAULT NULL,
  `mobility` tinyint(1) DEFAULT NULL,
  `amenities` text,
  `referred` text,
  `comments` text,
  `location` varchar(500) DEFAULT NULL,
  `label` varchar(100) DEFAULT NULL,
  `postComment` text,
  `notes` text,
  `limitedEvent` tinyint(1) DEFAULT NULL,
  `limitTo` int(2) DEFAULT NULL,
  `limitRebels` int(10) NOT NULL DEFAULT '9999',
  `limit501st` int(10) NOT NULL DEFAULT '9999',
  `limitMando` int(10) NOT NULL DEFAULT '9999',
  `limitDroid` int(10) NOT NULL DEFAULT '9999',
  `limitTotal` int(10) NOT NULL DEFAULT '9999',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `moneyRaised` int(100) NOT NULL DEFAULT '0',
  `squad` int(1) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_sign_up`
--

DROP TABLE IF EXISTS `event_sign_up`;
CREATE TABLE IF NOT EXISTS `event_sign_up` (
  `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trooperid` int(100) DEFAULT NULL,
  `troopid` int(100) NOT NULL,
  `costume` varchar(50) DEFAULT NULL,
  `costume_backup` varchar(50) NOT NULL DEFAULT '0',
  `reason` text,
  `status` int(2) NOT NULL DEFAULT '0',
  `attend` int(2) NOT NULL DEFAULT '0',
  `attended_costume` varchar(100) NOT NULL DEFAULT '0',
  `addedby` int(11) NOT NULL DEFAULT '0',
  `signuptime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `message` varchar(100) NOT NULL,
  `trooperid` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

DROP TABLE IF EXISTS `settings`;
CREATE TABLE IF NOT EXISTS `settings` (
  `lastidtrooper` int(11) NOT NULL DEFAULT '0',
  `lastidevent` int(11) NOT NULL DEFAULT '0',
  `lastidlink` int(11) NOT NULL DEFAULT '0',
  `siteclosed` int(11) NOT NULL DEFAULT '0',
  `signupclosed` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `troopers`
--

DROP TABLE IF EXISTS `troopers`;
CREATE TABLE IF NOT EXISTS `troopers` (
  `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT,
  `oldid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(240) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `squad` int(10) NOT NULL,
  `permissions` int(2) NOT NULL DEFAULT '0',
  `tkid` varchar(20) NOT NULL,
  `forum_id` varchar(255) NOT NULL,
  `password` varchar(255) DEFAULT NULL,
  `last_active` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved` int(1) NOT NULL DEFAULT '0',
  `subscribe` int(11) NOT NULL DEFAULT '1',
  `theme` int(11) NOT NULL DEFAULT '0',
  `datecreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
