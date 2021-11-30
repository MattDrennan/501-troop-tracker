-- phpMyAdmin SQL Dump
-- version 5.1.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Nov 30, 2021 at 01:22 PM
-- Server version: 5.7.34
-- PHP Version: 7.4.21

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

CREATE TABLE `501st_costumes` (
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

CREATE TABLE `501st_troopers` (
  `legionid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `thumbnail` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL,
  `squad` int(11) NOT NULL,
  `approved` int(11) NOT NULL DEFAULT '0',
  `status` int(11) NOT NULL DEFAULT '0',
  `standing` int(11) NOT NULL DEFAULT '0',
  `joindate` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` int(10) UNSIGNED NOT NULL,
  `title` varchar(100) NOT NULL,
  `icon` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `award_troopers`
--

CREATE TABLE `award_troopers` (
  `id` int(11) NOT NULL,
  `trooperid` int(11) NOT NULL,
  `awardid` int(11) NOT NULL,
  `awarded` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `comments`
--

CREATE TABLE `comments` (
  `id` int(10) UNSIGNED NOT NULL,
  `troopid` int(11) NOT NULL,
  `trooperid` int(11) NOT NULL,
  `comment` text NOT NULL,
  `important` int(11) NOT NULL,
  `posted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `costumes`
--

CREATE TABLE `costumes` (
  `id` int(10) UNSIGNED NOT NULL,
  `costume` varchar(50) NOT NULL,
  `era` int(11) NOT NULL,
  `club` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `donations`
--

CREATE TABLE `donations` (
  `trooperid` int(11) NOT NULL,
  `amount` decimal(11,2) NOT NULL,
  `txn_id` varchar(255) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `droid_troopers`
--

CREATE TABLE `droid_troopers` (
  `forum_id` varchar(255) NOT NULL,
  `droidname` varchar(255) NOT NULL,
  `imageurl` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `link` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_sign_up`
--

CREATE TABLE `event_sign_up` (
  `id` int(10) UNSIGNED NOT NULL,
  `trooperid` int(11) DEFAULT NULL,
  `troopid` int(11) NOT NULL,
  `costume` varchar(50) DEFAULT NULL,
  `costume_backup` varchar(50) NOT NULL DEFAULT '0',
  `reason` text,
  `status` int(11) NOT NULL DEFAULT '0',
  `attended_costume` varchar(100) NOT NULL DEFAULT '0',
  `addedby` int(11) NOT NULL DEFAULT '0',
  `signuptime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `mando_costumes`
--

CREATE TABLE `mando_costumes` (
  `mandoid` int(11) NOT NULL,
  `costumeurl` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `mando_troopers`
--

CREATE TABLE `mando_troopers` (
  `mandoid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `costume` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `message` varchar(100) NOT NULL,
  `trooperid` int(11) NOT NULL,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rebel_costumes`
--

CREATE TABLE `rebel_costumes` (
  `rebelid` int(11) NOT NULL,
  `costumename` varchar(255) NOT NULL,
  `costumeimage` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `rebel_troopers`
--

CREATE TABLE `rebel_troopers` (
  `rebelid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `rebelforum` varchar(255) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `settings`
--

CREATE TABLE `settings` (
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

CREATE TABLE `sg_troopers` (
  `sgid` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `image` varchar(255) NOT NULL,
  `link` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `troopers`
--

CREATE TABLE `troopers` (
  `id` int(10) UNSIGNED NOT NULL,
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
  `esquad1` tinyint(1) DEFAULT '1',
  `esquad2` tinyint(1) DEFAULT '1',
  `esquad3` tinyint(1) DEFAULT '1',
  `esquad4` tinyint(1) DEFAULT '1',
  `esquad5` tinyint(1) DEFAULT '1',
  `ecomments` tinyint(1) DEFAULT '1',
  `efast` tinyint(1) DEFAULT '0',
  `ecommandnotify` tinyint(1) DEFAULT '1',
  `econfirm` tinyint(1) DEFAULT '1',
  `datecreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `uploads`
--

CREATE TABLE `uploads` (
  `id` int(11) NOT NULL,
  `troopid` int(11) NOT NULL,
  `trooperid` int(11) NOT NULL,
  `filename` varchar(255) NOT NULL,
  `admin` int(11) NOT NULL DEFAULT '0',
  `date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `501st_troopers`
--
ALTER TABLE `501st_troopers`
  ADD PRIMARY KEY (`legionid`);

--
-- Indexes for table `awards`
--
ALTER TABLE `awards`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `award_troopers`
--
ALTER TABLE `award_troopers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `costumes`
--
ALTER TABLE `costumes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `donations`
--
ALTER TABLE `donations`
  ADD PRIMARY KEY (`txn_id`);

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_sign_up`
--
ALTER TABLE `event_sign_up`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `mando_troopers`
--
ALTER TABLE `mando_troopers`
  ADD PRIMARY KEY (`mandoid`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sg_troopers`
--
ALTER TABLE `sg_troopers`
  ADD PRIMARY KEY (`sgid`);

--
-- Indexes for table `troopers`
--
ALTER TABLE `troopers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `uploads`
--
ALTER TABLE `uploads`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `award_troopers`
--
ALTER TABLE `award_troopers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `costumes`
--
ALTER TABLE `costumes`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_sign_up`
--
ALTER TABLE `event_sign_up`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `troopers`
--
ALTER TABLE `troopers`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `uploads`
--
ALTER TABLE `uploads`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
