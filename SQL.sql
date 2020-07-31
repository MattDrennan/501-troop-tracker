-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:8889
-- Generation Time: Jul 31, 2020 at 03:13 PM
-- Server version: 5.7.25
-- PHP Version: 7.3.1

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `501`
--

-- --------------------------------------------------------

--
-- Table structure for table `awards`
--

CREATE TABLE `awards` (
  `id` int(100) UNSIGNED NOT NULL,
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
  `id` int(100) UNSIGNED NOT NULL,
  `troopid` int(100) NOT NULL,
  `trooperid` int(100) NOT NULL,
  `comment` text NOT NULL,
  `important` int(1) NOT NULL,
  `posted` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `costumes`
--

CREATE TABLE `costumes` (
  `id` int(100) UNSIGNED NOT NULL,
  `costume` varchar(50) NOT NULL,
  `era` int(2) NOT NULL,
  `club` int(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(100) UNSIGNED NOT NULL,
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
  `squad` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_comments`
--

CREATE TABLE `event_comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `trooperid` int(11) NOT NULL,
  `troopid` int(10) NOT NULL,
  `comment` text,
  `datetime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `event_sign_up`
--

CREATE TABLE `event_sign_up` (
  `id` int(100) UNSIGNED NOT NULL,
  `trooperid` int(100) NOT NULL,
  `troopid` int(100) NOT NULL,
  `costume` varchar(50) NOT NULL,
  `costume_backup` varchar(50) NOT NULL DEFAULT '-1',
  `reason` text,
  `status` int(2) NOT NULL DEFAULT '0',
  `attend` int(2) NOT NULL DEFAULT '0',
  `attended_costume` varchar(100) NOT NULL DEFAULT '-1',
  `signuptime` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shifts`
--

CREATE TABLE `shifts` (
  `id` int(11) NOT NULL,
  `troopid` int(11) NOT NULL,
  `starttime` time NOT NULL,
  `endtime` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `shift_trooper`
--

CREATE TABLE `shift_trooper` (
  `id` int(11) NOT NULL,
  `troopid` int(11) NOT NULL,
  `trooperid` int(11) NOT NULL,
  `shift` varchar(1000) NOT NULL,
  `status` int(11) NOT NULL DEFAULT '0',
  `attend` varchar(1000) DEFAULT '-1',
  `didNotAttend` varchar(1000) NOT NULL DEFAULT '-1',
  `costume` varchar(1000) NOT NULL DEFAULT '-1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `troopers`
--

CREATE TABLE `troopers` (
  `id` int(100) UNSIGNED NOT NULL,
  `name` varchar(20) NOT NULL,
  `email` varchar(240) NOT NULL,
  `phone` varchar(10) NOT NULL,
  `squad` int(10) NOT NULL,
  `permissions` int(2) NOT NULL,
  `tkid` varchar(20) NOT NULL,
  `password` varchar(50) DEFAULT NULL,
  `last_active` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `approved` int(1) NOT NULL DEFAULT '0',
  `subscribe` int(11) NOT NULL DEFAULT '1',
  `datecreated` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

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
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_comments`
--
ALTER TABLE `event_comments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `event_sign_up`
--
ALTER TABLE `event_sign_up`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shifts`
--
ALTER TABLE `shifts`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `shift_trooper`
--
ALTER TABLE `shift_trooper`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `troopers`
--
ALTER TABLE `troopers`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `awards`
--
ALTER TABLE `awards`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `award_troopers`
--
ALTER TABLE `award_troopers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `costumes`
--
ALTER TABLE `costumes`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_comments`
--
ALTER TABLE `event_comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `event_sign_up`
--
ALTER TABLE `event_sign_up`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shifts`
--
ALTER TABLE `shifts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `shift_trooper`
--
ALTER TABLE `shift_trooper`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `troopers`
--
ALTER TABLE `troopers`
  MODIFY `id` int(100) UNSIGNED NOT NULL AUTO_INCREMENT;
