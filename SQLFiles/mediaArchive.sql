-- Customers
-- -------------------------------------------------
DROP TABLE IF EXISTS `customers`;
CREATE TABLE `customers`(
    `ID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `companyName` varchar(50) NOT NULL,
    `firstName` varchar(50) NOT NULL,
    `lastName` varchar(50) NOT NULL,
    `email` varchar(50) NOT NULL,
    `phone` varchar(15) NOT NULL,
    `website` varchar(200) NOT NULL,
    PRIMARY KEY(`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- Projects
-- -------------------------------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects`(
    `projectID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `customerID` tinyint(3) unsigned NOT NULL,
    `projectName` varchar(50) NOT NULL,
    `scope` varchar(50) NOT NULL,
    `type` varchar(50) NOT NULL,
    `completed` boolean NOT NULL,
    `description` text NOT NULL,
    PRIMARY KEY(`projectID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- timeTracking
-- -------------------------------------------------
DROP TABLE IF EXISTS `timeTracking`;
CREATE TABLE `timeTracking`(
    `timeID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `projectIdLink` tinyint(3) unsigned NOT NULL,
    `customerIdLink` tinyint(3) unsigned NOT NULL,
    `startTime` int(11) NOT NULL,
    `endTime` int(11) NOT NULL,
    `totalHours` int(5) NOT NULL,
    `completed` boolean NOT NULL,
    `descriptionOfWork` text NOT NULL,
    PRIMARY KEY(`timeID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- mediaArchive
-- -------------------------------------------------
DROP TABLE IF EXISTS `mediaArchive`;
CREATE TABLE `mediaArchive`(
    `archiveID` tinyint(3) unsigned NOT NULL AUTO_INCREMENT,
    `filepath` varchar(100),
    `filename` varchar(100) UNIQUE,
    `filetype` varchar(100),
    `version` varchar(100),
    `title` varchar(100),
    `album` varchar(100),
    `author` varchar(100),
    `track` int(4),
    `year` int(5),
    `length` varchar(100),
    `lyric` text,
    PRIMARY KEY(`archiveID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
