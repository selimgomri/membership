-- phpMyAdmin SQL Dump
-- version 3.5.8.1
-- http://www.phpmyadmin.net
--
-- Host: chesterlestreetasc.co.uk.mysql:3306
-- Generation Time: Jul 02, 2018 at 11:15 PM
-- Server version: 10.1.30-MariaDB-1~xenial
-- PHP Version: 5.4.45-0+deb7u13

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `chesterlestreetasc_co_uk_membership`
--
CREATE DATABASE `chesterlestreetasc_co_uk_membership` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `chesterlestreetasc_co_uk_membership`;

-- --------------------------------------------------------

--
-- Table structure for table `emergencyContacts`
--

CREATE TABLE IF NOT EXISTS `emergencyContacts` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `ContactNumber` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `extras`
--

CREATE TABLE IF NOT EXISTS `extras` (
  `ExtraID` int(11) NOT NULL AUTO_INCREMENT,
  `ExtraName` varchar(100) NOT NULL,
  `ExtraFee` decimal(6,2) NOT NULL,
  PRIMARY KEY (`ExtraID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `extrasRelations`
--

CREATE TABLE IF NOT EXISTS `extrasRelations` (
  `RelationID` int(11) NOT NULL AUTO_INCREMENT,
  `ExtraID` int(11) NOT NULL,
  `MemberID` int(11) DEFAULT NULL,
  `UserID` int(11) DEFAULT NULL,
  PRIMARY KEY (`RelationID`),
  KEY `ExtraID` (`ExtraID`),
  KEY `MemberID` (`MemberID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `galaEntries`
--

CREATE TABLE IF NOT EXISTS `galaEntries` (
  `EntryID` int(11) NOT NULL AUTO_INCREMENT,
  `GalaID` int(11) NOT NULL,
  `MemberID` int(11) NOT NULL,
  `EntryProcessed` tinyint(1) NOT NULL,
  `TimesRequired` tinyint(1) DEFAULT NULL COMMENT 'If true, times required from coaches',
  `TimesProvided` tinyint(1) DEFAULT NULL COMMENT 'Set true if times provided for a non HyTek Gala',
  `FeeToPay` double DEFAULT NULL,
  `Charged` tinyint(1) NOT NULL DEFAULT '0',
  `50Free` tinyint(1) DEFAULT NULL,
  `100Free` tinyint(1) DEFAULT NULL,
  `200Free` tinyint(1) DEFAULT NULL,
  `400Free` tinyint(1) DEFAULT NULL,
  `800Free` tinyint(1) DEFAULT NULL,
  `1500Free` tinyint(1) DEFAULT NULL,
  `50Breast` tinyint(1) DEFAULT NULL,
  `100Breast` tinyint(1) DEFAULT NULL,
  `200Breast` tinyint(1) DEFAULT NULL,
  `50Fly` tinyint(1) DEFAULT NULL,
  `100Fly` tinyint(1) DEFAULT NULL,
  `200Fly` tinyint(1) DEFAULT NULL,
  `50Back` tinyint(1) DEFAULT NULL,
  `100Back` tinyint(1) DEFAULT NULL,
  `200Back` tinyint(1) DEFAULT NULL,
  `200IM` tinyint(1) DEFAULT NULL,
  `400IM` tinyint(1) DEFAULT NULL,
  `100IM` tinyint(1) DEFAULT NULL,
  `150IM` tinyint(1) DEFAULT NULL,
  `50FreeTime` tinytext,
  `100FreeTime` tinytext,
  `200FreeTime` tinytext,
  `400FreeTime` tinytext,
  `800FreeTime` tinytext,
  `1500FreeTime` tinytext,
  `50BreastTime` tinytext,
  `100BreastTime` tinytext,
  `200BreastTime` tinytext,
  `50FlyTime` tinytext,
  `100FlyTime` tinytext,
  `200FlyTime` tinytext,
  `50BackTime` tinytext,
  `100BackTime` tinytext,
  `200BackTime` tinytext,
  `200IMTime` tinytext,
  `400IMTime` tinytext,
  `100IMTime` tinytext,
  `150IMTime` tinytext,
  PRIMARY KEY (`EntryID`),
  KEY `GalaID` (`GalaID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=137 ;

-- --------------------------------------------------------

--
-- Table structure for table `galas`
--

CREATE TABLE IF NOT EXISTS `galas` (
  `GalaID` int(11) NOT NULL AUTO_INCREMENT,
  `GalaName` varchar(255) DEFAULT NULL,
  `CourseLength` enum('SHORT','LONG','IRREGULAR') DEFAULT NULL,
  `GalaVenue` varchar(255) DEFAULT NULL,
  `GalaFee` decimal(8,2) DEFAULT NULL,
  `GalaFeeConstant` tinyint(1) NOT NULL DEFAULT '1',
  `ClosingDate` date NOT NULL COMMENT 'The closing date of the gala',
  `GalaDate` date NOT NULL COMMENT 'Last day of gala when the event will disappear from accounts',
  `HyTek` tinyint(1) NOT NULL,
  PRIMARY KEY (`GalaID`),
  UNIQUE KEY `GalaID` (`GalaID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `memberMedical`
--

CREATE TABLE IF NOT EXISTS `memberMedical` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `MemberID` int(11) NOT NULL,
  `Conditions` text,
  `Allergies` text,
  `Medication` text,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `memberPhotography`
--

CREATE TABLE IF NOT EXISTS `memberPhotography` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `MemberID` int(11) NOT NULL,
  `Website` tinyint(1) NOT NULL,
  `Social` tinyint(1) NOT NULL,
  `Noticeboard` tinyint(1) NOT NULL,
  `FilmTraining` tinyint(1) NOT NULL,
  `ProPhoto` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `members`
--

CREATE TABLE IF NOT EXISTS `members` (
  `MemberID` int(11) NOT NULL AUTO_INCREMENT,
  `SquadID` int(11) NOT NULL,
  `UserID` int(11) DEFAULT NULL,
  `AccessKey` varchar(20) NOT NULL,
  `MForename` varchar(255) DEFAULT NULL,
  `MSurname` varchar(255) DEFAULT NULL,
  `MMiddleNames` varchar(255) DEFAULT NULL,
  `ASANumber` varchar(255) DEFAULT NULL,
  `ASACategory` int(11) NOT NULL,
  `DateOfBirth` date NOT NULL,
  `Gender` enum('Male','Female') NOT NULL,
  `OtherNotes` text NOT NULL,
  PRIMARY KEY (`MemberID`),
  UNIQUE KEY `MemberID` (`MemberID`),
  KEY `SquadID` (`SquadID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=186 ;

-- --------------------------------------------------------

--
-- Table structure for table `moves`
--

CREATE TABLE IF NOT EXISTS `moves` (
  `MoveID` int(11) NOT NULL AUTO_INCREMENT,
  `MemberID` int(11) NOT NULL,
  `SquadID` int(11) NOT NULL,
  `MovingDate` date NOT NULL,
  PRIMARY KEY (`MoveID`),
  KEY `MemberID` (`MemberID`),
  KEY `MemberID_2` (`MemberID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=29 ;

-- --------------------------------------------------------

--
-- Table structure for table `newUsers`
--

CREATE TABLE IF NOT EXISTS `newUsers` (
  `ID` int(25) NOT NULL AUTO_INCREMENT,
  `AuthCode` text NOT NULL,
  `UserJSON` text NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UserID` (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `notify`
--

CREATE TABLE IF NOT EXISTS `notify` (
  `EmailID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Status` enum('Queued','Sent','No_Sub') NOT NULL,
  `Subject` text NOT NULL,
  `Message` text NOT NULL,
  PRIMARY KEY (`EmailID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=186 ;

-- --------------------------------------------------------

--
-- Table structure for table `passwordTokens`
--

CREATE TABLE IF NOT EXISTS `passwordTokens` (
  `TokenID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Token` text NOT NULL,
  `Date` date DEFAULT NULL,
  `Type` enum('Password_Reset','Account_Verification') NOT NULL,
  PRIMARY KEY (`TokenID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `paymentMandates`
--

CREATE TABLE IF NOT EXISTS `paymentMandates` (
  `MandateID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Mandate` text NOT NULL,
  `Customer` text NOT NULL,
  `BankAccount` text NOT NULL,
  `BankName` text NOT NULL,
  `AccountHolderName` text NOT NULL,
  `AccountNumEnd` text NOT NULL,
  `InUse` tinyint(1) NOT NULL,
  PRIMARY KEY (`MandateID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `paymentMonths`
--

CREATE TABLE IF NOT EXISTS `paymentMonths` (
  `MonthID` int(11) NOT NULL AUTO_INCREMENT,
  `MonthStart` text NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`MonthID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9 ;

-- --------------------------------------------------------

--
-- Table structure for table `paymentPreferredMandate`
--

CREATE TABLE IF NOT EXISTS `paymentPreferredMandate` (
  `PrefID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `MandateID` int(11) NOT NULL,
  PRIMARY KEY (`PrefID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `paymentSchedule`
--

CREATE TABLE IF NOT EXISTS `paymentSchedule` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Day` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `paymentSquadFees`
--

CREATE TABLE IF NOT EXISTS `paymentSquadFees` (
  `SFID` int(11) NOT NULL AUTO_INCREMENT,
  `MonthID` int(11) NOT NULL,
  PRIMARY KEY (`SFID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

-- --------------------------------------------------------

--
-- Table structure for table `paymentTypes`
--

CREATE TABLE IF NOT EXISTS `paymentTypes` (
  `PayTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `PayTypeName` varchar(60) NOT NULL,
  `PayTypeDescription` varchar(200) DEFAULT NULL,
  `PayTypeEnabled` tinyint(1) NOT NULL,
  PRIMARY KEY (`PayTypeID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `paymentWebhookOps`
--

CREATE TABLE IF NOT EXISTS `paymentWebhookOps` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `EventID` text NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE IF NOT EXISTS `payments` (
  `PaymentID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `Status` enum('pending_api_request','pending_customer_approval','pending_submission','submitted','confirmed','paid_out','cancelled','customer_approval_denied','failed','charged_back','cust_not_dd') NOT NULL,
  `UserID` int(11) NOT NULL,
  `MandateID` int(11) NOT NULL,
  `Name` text,
  `Amount` int(11) NOT NULL,
  `Currency` text NOT NULL,
  `PMkey` text NOT NULL,
  `Type` enum('Payment','Refund') NOT NULL,
  PRIMARY KEY (`PaymentID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=173 ;

-- --------------------------------------------------------

--
-- Table structure for table `paymentsPending`
--

CREATE TABLE IF NOT EXISTS `paymentsPending` (
  `PaymentID` int(11) NOT NULL AUTO_INCREMENT,
  `Date` date NOT NULL,
  `Status` enum('Pending','Queued','Requested','Paid','Failed') NOT NULL,
  `UserID` int(11) NOT NULL,
  `Name` text,
  `Amount` int(11) NOT NULL,
  `Currency` text NOT NULL,
  `PMkey` text,
  `Type` enum('Payment','Refund') NOT NULL,
  `MetadataJSON` text,
  PRIMARY KEY (`PaymentID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=435 ;

-- --------------------------------------------------------

--
-- Table structure for table `renewalProgress`
--

CREATE TABLE IF NOT EXISTS `renewalProgress` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `RenewalID` int(11) NOT NULL,
  `Date` date NOT NULL,
  `Stage` int(11) NOT NULL,
  `Substage` int(11) NOT NULL,
  `Part` int(11) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=2 ;

-- --------------------------------------------------------

--
-- Table structure for table `renewals`
--

CREATE TABLE IF NOT EXISTS `renewals` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text NOT NULL,
  `Year` int(11) NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE IF NOT EXISTS `sessions` (
  `SessionID` int(11) NOT NULL AUTO_INCREMENT,
  `SquadID` int(11) NOT NULL,
  `VenueID` int(11) NOT NULL,
  `SessionName` varchar(100) NOT NULL,
  `SessionDay` int(11) NOT NULL,
  `MainSequence` tinyint(1) NOT NULL,
  `StartTime` time DEFAULT NULL,
  `EndTime` time DEFAULT NULL,
  `DisplayFrom` date DEFAULT NULL,
  `DisplayUntil` date DEFAULT NULL,
  PRIMARY KEY (`SessionID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Stores information about sessions for Squads' AUTO_INCREMENT=82 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessionsAttendance`
--

CREATE TABLE IF NOT EXISTS `sessionsAttendance` (
  `AttendanceID` int(11) NOT NULL AUTO_INCREMENT,
  `WeekID` int(11) NOT NULL,
  `SessionID` int(11) NOT NULL,
  `MemberID` int(11) NOT NULL,
  `AttendanceBoolean` int(11) NOT NULL,
  PRIMARY KEY (`AttendanceID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=9024 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessionsVenues`
--

CREATE TABLE IF NOT EXISTS `sessionsVenues` (
  `VenueID` int(11) NOT NULL AUTO_INCREMENT,
  `VenueName` varchar(100) NOT NULL,
  `Location` text,
  PRIMARY KEY (`VenueID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

-- --------------------------------------------------------

--
-- Table structure for table `sessionsWeek`
--

CREATE TABLE IF NOT EXISTS `sessionsWeek` (
  `WeekID` int(11) NOT NULL AUTO_INCREMENT,
  `WeekDateBeginning` date NOT NULL,
  PRIMARY KEY (`WeekID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=26 ;

-- --------------------------------------------------------

--
-- Table structure for table `squads`
--

CREATE TABLE IF NOT EXISTS `squads` (
  `SquadID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) DEFAULT NULL,
  `SquadName` varchar(255) DEFAULT NULL,
  `SquadFee` decimal(8,2) DEFAULT NULL,
  `SquadCoach` varchar(100) NOT NULL,
  `SquadTimetable` varchar(100) NOT NULL,
  `SquadCoC` varchar(100) NOT NULL,
  `SquadKey` varchar(20) NOT NULL,
  PRIMARY KEY (`SquadID`),
  UNIQUE KEY `SquadID` (`SquadID`),
  KEY `UserID` (`UserID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=14 ;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `UserID` int(25) NOT NULL AUTO_INCREMENT,
  `Username` varchar(65) NOT NULL,
  `GID` int(11) DEFAULT NULL COMMENT 'If G Suite User, store the ID here for automatic passwords',
  `Password` varchar(255) NOT NULL,
  `AccessLevel` enum('Parent','Galas','Coach','Committee','Admin') NOT NULL DEFAULT 'Parent',
  `EmailAddress` varchar(255) NOT NULL,
  `EmailComms` tinyint(1) NOT NULL,
  `Forename` text NOT NULL,
  `Surname` text NOT NULL,
  `Mobile` tinytext NOT NULL,
  `MobileComms` tinyint(1) NOT NULL,
  PRIMARY KEY (`UserID`),
  UNIQUE KEY `UserID` (`UserID`),
  UNIQUE KEY `Username` (`Username`),
  UNIQUE KEY `EmailAddress` (`EmailAddress`),
  UNIQUE KEY `Username_2` (`Username`,`EmailAddress`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=101 ;

-- --------------------------------------------------------

--
-- Table structure for table `wallet`
--

CREATE TABLE IF NOT EXISTS `wallet` (
  `WalletID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Balance` double NOT NULL DEFAULT '0',
  PRIMARY KEY (`WalletID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=75 ;

-- --------------------------------------------------------

--
-- Table structure for table `walletHistory`
--

CREATE TABLE IF NOT EXISTS `walletHistory` (
  `WalletHistoryID` int(11) NOT NULL AUTO_INCREMENT,
  `Amount` double NOT NULL,
  `Balance` double NOT NULL,
  `UserID` int(11) NOT NULL,
  `TransactionDesc` varchar(60) NOT NULL,
  PRIMARY KEY (`WalletHistoryID`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `extrasRelations`
--
ALTER TABLE `extrasRelations`
  ADD CONSTRAINT `extrasRelations_ibfk_1` FOREIGN KEY (`ExtraID`) REFERENCES `extras` (`ExtraID`),
  ADD CONSTRAINT `extrasRelations_ibfk_2` FOREIGN KEY (`MemberID`) REFERENCES `members` (`MemberID`),
  ADD CONSTRAINT `extrasRelations_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `galaEntries`
--
ALTER TABLE `galaEntries`
  ADD CONSTRAINT `galaEntries_ibfk_1` FOREIGN KEY (`GalaID`) REFERENCES `galas` (`GalaID`),
  ADD CONSTRAINT `galaEntries_ibfk_2` FOREIGN KEY (`GalaID`) REFERENCES `galas` (`GalaID`);

--
-- Constraints for table `members`
--
ALTER TABLE `members`
  ADD CONSTRAINT `members_ibfk_1` FOREIGN KEY (`SquadID`) REFERENCES `squads` (`SquadID`),
  ADD CONSTRAINT `members_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

--
-- Constraints for table `squads`
--
ALTER TABLE `squads`
  ADD CONSTRAINT `squads_ibfk_1` FOREIGN KEY (`UserID`) REFERENCES `users` (`UserID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
