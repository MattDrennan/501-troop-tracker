-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Nov 27, 2021 at 01:23 PM
-- Server version: 5.7.31
-- PHP Version: 8.0.11

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
-- Table structure for table `501st_costumes`
--

DROP TABLE IF EXISTS `501st_costumes`;
CREATE TABLE IF NOT EXISTS `501st_costumes` (
  `legionid` int(11) NOT NULL,
  `costumeid` int(11) NOT NULL,
  `prefix` varchar(2) NOT NULL,
  `costumename` varchar(255) NOT NULL,
  `photo` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `bucketoff` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `501st_troopers`
--

DROP TABLE IF EXISTS `501st_troopers`;
CREATE TABLE IF NOT EXISTS `501st_troopers` (
  `legionid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `squad` int(11) NOT NULL,
  `approved` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `standing` int(11) NOT NULL DEFAULT '0',
  `joindate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`legionid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

DROP TABLE IF EXISTS `awards`;
CREATE TABLE IF NOT EXISTS `awards` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
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
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `troopid` int(11) NOT NULL,
  `trooperid` int(11) NOT NULL,
  `comment` text NOT NULL,
  `important` int(11) NOT NULL,
  `posted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `costumes`
--

DROP TABLE IF EXISTS `costumes`;
CREATE TABLE IF NOT EXISTS `costumes` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `costume` varchar(50) NOT NULL,
  `era` int(11) NOT NULL,
  `club` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

DROP TABLE IF EXISTS `donations`;
CREATE TABLE IF NOT EXISTS `donations` (
  `trooperid` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`txn_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `droid_troopers`
--

DROP TABLE IF EXISTS `droid_troopers`;
CREATE TABLE IF NOT EXISTS `droid_troopers` (
  `forum_id` varchar(255) NOT NULL,
  `droidname` varchar(255) NOT NULL,
  `imageurl` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `oldid` int(11) NOT NULL DEFAULT '0',
  `name` varchar(255) NOT NULL,
  `venue` varchar(255) DEFAULT NULL,
  `dateStart` datetime DEFAULT NULL,
  `dateEnd` datetime DEFAULT NULL,
  `website` varchar(500) DEFAULT NULL,
  `numberOfAttend` int(11) DEFAULT NULL,
  `requestedNumber` int(11) DEFAULT NULL,
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
  `limitTo` int(11) DEFAULT NULL,
  `limitRebels` int(11) NOT NULL DEFAULT '500',
  `limit501st` int(11) NOT NULL DEFAULT '500',
  `limitMando` int(11) NOT NULL DEFAULT '500',
  `limitDroid` int(11) NOT NULL DEFAULT '500',
  `limitOther` int(11) NOT NULL DEFAULT '500',
  `closed` tinyint(1) NOT NULL DEFAULT '0',
  `moneyRaised` int(11) NOT NULL DEFAULT '0',
  `squad` int(11) NOT NULL,
  `link` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_sign_up`
--

DROP TABLE IF EXISTS `event_sign_up`;
CREATE TABLE IF NOT EXISTS `event_sign_up` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `trooperid` int(11) DEFAULT NULL,
  `troopid` int(11) NOT NULL,
  `costume` varchar(50) DEFAULT NULL,
  `costume_backup` varchar(50) NOT NULL DEFAULT '0',
  `reason` text,
  `status` int(11) NOT NULL DEFAULT '0',
  `attended_costume` varchar(100) NOT NULL DEFAULT '0',
  `addedby` int(11) NOT NULL DEFAULT '0',
  `signuptime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mando_costumes`
--

DROP TABLE IF EXISTS `mando_costumes`;
CREATE TABLE IF NOT EXISTS `mando_costumes` (
  `mandoid` int(11) NOT NULL,
  `costumeurl` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mando_troopers`
--

DROP TABLE IF EXISTS `mando_troopers`;
CREATE TABLE IF NOT EXISTS `mando_troopers` (
  `mandoid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `costume` varchar(255) NOT NULL,
  PRIMARY KEY (`mandoid`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

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
-- Table structure for table `rebel_costumes`
--

DROP TABLE IF EXISTS `rebel_costumes`;
CREATE TABLE IF NOT EXISTS `rebel_costumes` (
  `rebelid` int(11) NOT NULL,
  `costumename` varchar(255) NOT NULL,
  `costumeimage` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rebel_troopers`
--

DROP TABLE IF EXISTS `rebel_troopers`;
CREATE TABLE IF NOT EXISTS `rebel_troopers` (
  `rebelid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rebelforum` varchar(255) NOT NULL
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
  `signupclosed` int(11) NOT NULL DEFAULT '0',
  `lastnotification` int(11) NOT NULL DEFAULT '0',
  `supportgoal` int(11) NOT NULL DEFAULT '0',
  `notifyevent` int(11) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `sg_troopers`
--

DROP TABLE IF EXISTS `sg_troopers`;
CREATE TABLE IF NOT EXISTS `sg_troopers` (
  `sgid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  PRIMARY KEY (`sgid`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `troopers`
--

DROP TABLE IF EXISTS `troopers`;
CREATE TABLE IF NOT EXISTS `troopers` (
  `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `oldid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(240) DEFAULT NULL,
  `phone` varchar(10) DEFAULT NULL,
  `squad` int(11) NOT NULL,
  `permissions` int(11) NOT NULL DEFAULT '0',
  `tkid` varchar(20) NOT NULL,
  `forum_id` varchar(255) NOT NULL,
  `rebelforum` varchar(255) NOT NULL,
  `mandoid` int(11) NOT NULL,
  `sgid` varchar(10) NOT NULL DEFAULT '0',
  `password` varchar(255) DEFAULT NULL,
  `last_active` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved` int(11) NOT NULL DEFAULT '0',
  `subscribe` int(11) NOT NULL DEFAULT '1',
  `theme` int(11) NOT NULL DEFAULT '0',
  `supporter` int(11) NOT NULL DEFAULT '0',
  `datecreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

DROP TABLE IF EXISTS `uploads`;
CREATE TABLE IF NOT EXISTS `uploads` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `troopid` int(11) NOT NULL,
  `trooperid` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
