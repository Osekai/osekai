-- Adminer 4.8.1 MySQL 5.7.38-log dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';
SET collation_connection = 'latin1_german1_ci';

DELIMITER ;;

DROP PROCEDURE IF EXISTS `FUNC_GetBeatmaps`;;
CREATE PROCEDURE `FUNC_GetBeatmaps`(nUserID INTEGER(20), strName VARCHAR(50))
SELECT Beatmaps.ID As ObjectID,
	BeatmapID,
	MapsetID,
    Gamemode,
    SongTitle,
    Artist,
    MapperID,
    Mapper,
    ROUND(Difficulty, 2) AS Difficulty,
    DifficultyName,
    DownloadUnavailable,
	SubmittedBy,
    SubmissionDate,
    MedalName,
    COALESCE(voteSum, 0) AS VoteSum,
    HasVoted,
    Note
FROM Beatmaps 
LEFT JOIN (SELECT SUM(Vote) As voteSum, ObjectID FROM Votes WHERE Type = '0' GROUP BY ObjectID) AS t ON t.ObjectID = Beatmaps.ID 
LEFT JOIN (SELECT ObjectID AS hasVoted FROM Votes WHERE UserID = nUserID AND Type = '0' GROUP BY ObjectID) AS x ON x.hasVoted = Beatmaps.ID 
WHERE MedalName = strName 
ORDER BY voteSUM DESC, ID DESC;;

DROP PROCEDURE IF EXISTS `FUNC_GetCommentGroups`;;
CREATE PROCEDURE `FUNC_GetCommentGroups`(strMedal VARCHAR(50))
SELECT COALESCE(z.ParentComment, COALESCE(y.ParentComment, COALESCE(x.ParentComment, COALESCE(w.ParentComment, COALESCE(v.ParentComment, COALESCE(u.ParentComment, COALESCE(t.ParentComment, COALESCE(Comments.ParentComment, Comments.ID)))))))) AS GroupID
FROM Comments
LEFT JOIN Comments t ON Comments.ParentComment = t.ID
LEFT JOIN Comments u ON t.ParentComment = u.ID
LEFT JOIN Comments v ON u.ParentComment = v.ID
LEFT JOIN Comments w ON v.ParentComment = w.ID
LEFT JOIN Comments x ON w.ParentComment = x.ID
LEFT JOIN Comments y ON x.ParentComment = y.ID
LEFT JOIN Comments z ON y.ParentComment = z.ID
LEFT JOIN Votes ON Votes.ObjectID = Comments.ID AND Votes.Type = 1
WHERE Comments.MedalName = strMedal AND Comments.Reported < 2 AND (t.ID IS NOT NULL OR NULLIF(Comments.ParentComment, '') IS NULL)
GROUP BY COALESCE(z.ParentComment, COALESCE(y.ParentComment, COALESCE(x.ParentComment, COALESCE(w.ParentComment, COALESCE(v.ParentComment, COALESCE(u.ParentComment, COALESCE(t.ParentComment, COALESCE(Comments.ParentComment, Comments.ID))))))))
ORDER BY SUM(Votes.Vote) DESC, COALESCE(z.ParentComment, COALESCE(y.ParentComment, COALESCE(x.ParentComment, COALESCE(w.ParentComment, COALESCE(v.ParentComment, COALESCE(u.ParentComment, COALESCE(t.ParentComment, COALESCE(Comments.ParentComment, Comments.ID)))))))) DESC;;

DROP PROCEDURE IF EXISTS `FUNC_GetCommentsByGroup`;;
CREATE PROCEDURE `FUNC_GetCommentsByGroup`(strGroup VARCHAR(10))
SELECT Comments.ID,
	Comments.PostText,
    Comments.UserID,
    Comments.PostDate,
    Comments.ParentCommenter,
    Comments.MedalName,
    Roles.RoleName,
    SUM(Votes.Vote) AS VoteSum
FROM Comments
LEFT JOIN Comments t ON Comments.ParentComment = t.ID
LEFT JOIN Comments u ON t.ParentComment = u.ID
LEFT JOIN Comments v ON u.ParentComment = v.ID
LEFT JOIN Comments w ON v.ParentComment = w.ID
LEFT JOIN Comments x ON w.ParentComment = x.ID
LEFT JOIN Comments y ON x.ParentComment = y.ID
LEFT JOIN Comments z ON y.ParentComment = z.ID
LEFT JOIN Votes ON Votes.ObjectID = Comments.ID AND Votes.Type = 1
LEFT JOIN Roles ON Roles.UserID = Comments.UserID
WHERE COALESCE(z.ParentComment, COALESCE(y.ParentComment, COALESCE(x.ParentComment, COALESCE(w.ParentComment, COALESCE(v.ParentComment, COALESCE(u.ParentComment, COALESCE(t.ParentComment, COALESCE(Comments.ParentComment, Comments.ID)))))))) = strGroup
GROUP BY Comments.ID, Comments.PostText, Comments.UserID, Comments.PostDate, Comments.ParentCommenter, Comments.MedalName, Roles.RoleName;;

DROP PROCEDURE IF EXISTS `FUNC_GetMedals`;;
CREATE PROCEDURE `FUNC_GetMedals`(IN `strGrouping` varchar(30), IN `strName` varchar(50))
SELECT Medals.medalid AS MedalID
	, Medals.name AS Name
	, Medals.link AS Link
	, Medals.description AS Description
	, Medals.restriction AS Restriction
	, Medals.grouping AS `Grouping`
	, Medals.instructions AS Instructions
	, Solutions.solution AS Solution
	, Solutions.mods AS Mods
	, MedalStructure.Locked AS Locked
    , Medals.video AS Video
    , Medals.date AS Date
    , Medals.packid as PackID
    , Medals.firstachieveddate as FirstAchievedDate
    , Medals.firstachievedby as FirstAchievedBy
	, (CASE WHEN restriction = 'osu' THEN 2 WHEN restriction = 'taiko' THEN 3 WHEN restriction = 'fruits' THEN 4 WHEN restriction = 'mania' THEN 5 ELSE 1 END) AS ModeOrder 
	, Medals.ordering AS Ordering
    , MedalRarity.frequency As Rarity
FROM Medals 
LEFT JOIN Solutions ON Medals.medalid = Solutions.medalid 
LEFT JOIN MedalStructure ON MedalStructure.MedalID = Medals.medalid 
LEFT JOIN MedalRarity ON MedalRarity.id = Medals.medalid
WHERE Medals.grouping LIKE CONCAT('%', strGrouping, '%') AND LOWER(Medals.name) LIKE CONCAT('%', strName, '%')
ORDER BY ModeOrder, Ordering DESC, MedalID;;

DROP EVENT IF EXISTS `Clean sessions data`;;
CREATE EVENT `Clean sessions data` ON SCHEDULE EVERY 1 DAY STARTS '2022-06-14 09:16:53' ON COMPLETION NOT PRESERVE DISABLE ON SLAVE COMMENT 'Without this, the site gets linearly slower over time.' DO DELETE FROM OsekaiSessions WHERE sessionData NOT LIKE "%token%";;

DROP EVENT IF EXISTS `Update Comment Names`;;
CREATE EVENT `Update Comment Names` ON SCHEDULE EVERY 1 WEEK STARTS '2022-09-29 03:00:00' ON COMPLETION NOT PRESERVE ENABLE COMMENT 'If someone renames. Makes it easier.' DO UPDATE Comments
INNER JOIN Ranking ON Comments.UserID = Ranking.id
SET Comments.Username = Ranking.name
WHERE Comments.Username <> Ranking.name;;

DELIMITER ;

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `AdminFunImages`;
CREATE TABLE `AdminFunImages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `path` text COLLATE utf8mb4_bin NOT NULL,
  `caption` text COLLATE utf8mb4_bin NOT NULL,
  `by` text COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `AdminLogs`;
CREATE TABLE `AdminLogs` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user` int(11) NOT NULL,
  `data` text COLLATE utf8mb4_bin NOT NULL,
  `app` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `importance` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `AdminNotes`;
CREATE TABLE `AdminNotes` (
  `Id` text COLLATE utf8mb4_bin NOT NULL,
  `Author` int(11) NOT NULL,
  `Text` text COLLATE utf8mb4_bin NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `Alerts`;
CREATE TABLE `Alerts` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text COLLATE utf8mb4_bin NOT NULL,
  `StartDate` date NOT NULL,
  `EndDate` date NOT NULL,
  `Permanent` int(11) NOT NULL,
  `Type` text COLLATE utf8mb4_bin NOT NULL,
  `Text` text COLLATE utf8mb4_bin NOT NULL,
  `Apps` text COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `Apps`;
CREATE TABLE IF NOT EXISTS `Apps` (
  `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'apps in the dropdown are ordered by this. feel free to change them up',
  `order` int(11) NOT NULL,
  `name` text NOT NULL,
  `slogan` text NOT NULL,
  `simplename` text NOT NULL,
  `color_dark` text NOT NULL,
  `color` text NOT NULL,
  `logo` text NOT NULL,
  `colour_logo` text NOT NULL,
  `cover` text NOT NULL,
  `visible` int(11) NOT NULL,
  `experimental` int(11) NOT NULL,
  `hascover` int(1) NOT NULL,
  `dark_value_multiplier` float NOT NULL DEFAULT 1,
  `value_mulitplier` float NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- Dumping data for table osekai.Apps: ~13 rows (approximately)
DELETE FROM `Apps`;
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(-1, -1, 'Osekai Home', '??apps.home.slogan??', 'home', '53, 61, 85', '53, 61, 85', 'osekai_light', 'osekai_dark', 'cover/none', 1, 0, 0, 1, 1);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(1, 1, 'Osekai Rankings', '??apps.rankings.slogan??', 'rankings', '0, 66, 79', '0, 194, 224', 'white/rankings', 'coloured/rankings', 'cover/rankings', 1, 0, 1, 0.5, 0.7);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(2, 2, 'Osekai Medals', '??apps.medals.slogan??', 'medals', '102, 34, 68', '255, 102, 170', 'white/medals', 'coloured/medals', 'cover/medals', 1, 0, 1, 1, 1);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(3, 3, 'Osekai Profiles', '??apps.profiles.slogan??', 'profiles', '51, 68, 102', '102, 143, 255', 'white/profiles', 'coloured/profiles', 'cover/profiles', 1, 0, 1, 1, 1);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(5, 5, 'Osekai Snapshots', '??apps.snapshots.slogan??', 'snapshots', '38, 44, 124', '63, 77, 245', 'white/snapshots', 'coloured/snapshots', 'cover/snapshots', 1, 0, 1, 1, 1);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(6, 6, 'Osekai Custom', 'it\'s a mystery', 'custom', '32, 32, 32', '32, 32, 32', 'osekai_light', 'osekai_dark', 'cover/none', 0, 0, 0, 1, 1);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(7, 7, 'Osekai Badges', '??apps.badges.slogan??', 'badges', '89,62,110', '170,102,255', 'white/badges', 'coloured/badges', 'cover/badges', 1, 0, 1, 1, 1);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(9, 9, 'Osekai Azelia', 'coming soon', 'azelia', '38, 44, 124', '63, 77, 245', 'white/snapshots', 'coloured/snapshots', 'cover/snapshots', 1, 1, 0, 1, 1);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(11, 11, 'Osekai Teams', 'WIP', 'teams', '42,94,84', '50,227,195', 'white/teams', 'white/teams', 'cover/teams', 1, 1, 0, 1, 1);
INSERT INTO `Apps` (`id`, `order`, `name`, `slogan`, `simplename`, `color_dark`, `color`, `logo`, `colour_logo`, `cover`, `visible`, `experimental`, `hascover`, `dark_value_multiplier`, `value_mulitplier`) VALUES
	(12, 12, 'Osekai Tools', 'uwu', 'tools', '47,77,104', '45,118,186', 'white/tools', 'coloured/tools', '', 1, 1, 0, 1, 1);

DROP TABLE IF EXISTS `AuthenticatorTokens`;
CREATE TABLE `AuthenticatorTokens` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `token` text NOT NULL,
  `discord_id` bigint(20) NOT NULL,
  `minecraft_uuid` text NOT NULL,
  `type` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AuthenticatorUsers`;
CREATE TABLE `AuthenticatorUsers` (
  `osuID` bigint(20) NOT NULL,
  `DiscordID` bigint(20) NOT NULL,
  `osuUsername` text NOT NULL,
  `MedalCount` int(11) NOT NULL,
  `Percentage` text NOT NULL,
  `MinecraftUUID` text NOT NULL,
  `Type` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `AvailableRoles`;
CREATE TABLE `AvailableRoles` (
  `RoleID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Badge` text NOT NULL,
  `BadgeText` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `AvailableRoles` (`RoleID`, `Name`, `Badge`, `BadgeText`) VALUES
(1,	'Developer',	'dev',	'DEV'),
(2,	'Moderator',	'mod',	'MOD'),
(3,	'Snapshots Moderator',	'snapshots-mod',	'SNAPSHOTS MOD'),
(4,	'Supporter - Tier 1',	'supporter',	'<i class=\"fa fa-heart\" aria-hidden=\"true\"></i>'),
(5,	'Supporter - Tier 2',	'supporter',	'<i class=\"fa fa-heart\" aria-hidden=\"true\"></i><i class=\"fa fa-heart\" aria-hidden=\"true\"></i>'),
(6,	'Supporter - Tier 3',	'supporter',	'<i class=\"fa fa-heart\" aria-hidden=\"true\"></i><i class=\"fa fa-heart\" aria-hidden=\"true\"></i><i class=\"fa fa-heart\" aria-hidden=\"true\"></i>'),
(7,	'Chromb',	'chromb',	'chromb'),
(8,	'Community Management Team',	'cmt',	'CMT'),
(9,	'App Developer',	'appdev',	'APP DEV');

DROP TABLE IF EXISTS `BadgeListing`;
DROP VIEW IF EXISTS `BadgeListing`;
CREATE TABLE `BadgeListing` (`name` text, `badge_count` int(10), `total_pp` int(11), `ProfileID` int(11), `standard_pp` int(11), `mania_pp` int(11), `ctb_pp` int(11), `taiko_pp` int(11), `name_long` varchar(30), `flag` varchar(70));


DROP TABLE IF EXISTS `Badges`;
CREATE TABLE `Badges` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `image_url` text NOT NULL,
  `description` text NOT NULL,
  `awarded_at` date NOT NULL,
  `users` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Badges2`;
CREATE TABLE `Badges2` (
  `user_id` int(11) NOT NULL,
  `image` text NOT NULL,
  `description` text NOT NULL,
  `awarded` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `url` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `BeatmapLengths`;
CREATE TABLE `BeatmapLengths` (
  `Id` int(11) NOT NULL,
  `Length` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

DROP TABLE IF EXISTS `Beatmaps`;
CREATE TABLE `Beatmaps` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `MedalName` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `BeatmapID` int(20) NOT NULL,
  `MapsetID` int(20) NOT NULL,
  `Gamemode` varchar(10) COLLATE latin1_german1_ci NOT NULL,
  `SongTitle` varchar(80) COLLATE latin1_german1_ci NOT NULL,
  `Artist` varchar(80) COLLATE latin1_german1_ci NOT NULL,
  `MapperID` int(10) NOT NULL,
  `Mapper` varchar(40) COLLATE latin1_german1_ci NOT NULL,
  `Source` varchar(200) COLLATE latin1_german1_ci NOT NULL,
  `bpm` double NOT NULL,
  `Difficulty` double NOT NULL,
  `DifficultyName` varchar(80) COLLATE latin1_german1_ci NOT NULL,
  `DownloadUnavailable` tinyint(1) NOT NULL,
  `SubmittedBy` int(20) NOT NULL,
  `SubmissionDate` datetime NOT NULL,
  `Note` varchar(500) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


DELIMITER ;;

CREATE TRIGGER `CheckLock` BEFORE INSERT ON `Beatmaps` FOR EACH ROW
IF new.MedalName IN (SELECT Medals.name FROM Medals INNER JOIN MedalStructure ON MedalStructure.MedalID = Medals.medalid) THEN
    signal SQLSTATE '45000';
End IF;;

DELIMITER ;

DROP TABLE IF EXISTS `Comments`;
CREATE TABLE `Comments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PostText` varchar(1000) NOT NULL,
  `MedalID` int(11) DEFAULT NULL,
  `VersionID` int(10) DEFAULT NULL,
  `ProfileID` int(20) DEFAULT NULL,
  `Username` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `UserID` int(20) NOT NULL,
  `AvatarURL` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci NOT NULL,
  `PostDate` datetime NOT NULL,
  `Reported` int(5) DEFAULT 0,
  `ParentComment` int(10) DEFAULT NULL,
  `ParentCommenter` varchar(100) CHARACTER SET utf8mb3 COLLATE utf8mb3_unicode_ci DEFAULT NULL,
  `Pinned` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  KEY `ParentComment` (`ParentComment`),
  CONSTRAINT `Comments_ibfk_1` FOREIGN KEY (`ParentComment`) REFERENCES `Comments` (`ID`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DELIMITER ;;

CREATE TRIGGER `NotifyComment` AFTER INSERT ON `Comments` FOR EACH ROW
If new.ProfileID IS NOT NULL THEN
	INSERT INTO Notifications(Notifications.UserID, Notifications.Title, Notifications.Message, Notifications.Date) VALUES (new.ProfileID, 'New Comment on your Profile', CONCAT(new.Username, ' has left you a comment on Osekai Profiles'), new.PostDate);
END IF;;

CREATE TRIGGER `ArchiveComment` BEFORE DELETE ON `Comments` FOR EACH ROW
INSERT INTO DeletedComments (DeletedComments.PostText, DeletedComments.MedalID, DeletedComments.VersionID, DeletedComments.ProfileID, DeletedComments.Username, DeletedComments.UserID, DeletedComments.AvatarURL, DeletedComments.PostDate, DeletedComments.Reported, DeletedComments.ParentComment, DeletedComments.ParentCommenter) VALUES (old.PostText, old.MedalID, old.VersionID, old.ProfileID, old.Username, old.UserID, old.AvatarURL, old.PostDate, old.Reported, old.ParentComment, old.ParentCommenter);;

DELIMITER ;

DROP TABLE IF EXISTS `Countries`;
CREATE TABLE `Countries` (
  `name_short` varchar(3) COLLATE latin1_german1_ci NOT NULL,
  `name_long` varchar(30) COLLATE latin1_german1_ci NOT NULL,
  `link` varchar(70) COLLATE latin1_german1_ci NOT NULL,
  PRIMARY KEY (`name_short`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

INSERT INTO `Countries` (`name_short`, `name_long`, `link`) VALUES
('AD',	'Andorra',	'https://osu.ppy.sh/images/flags/AD.png'),
('AE',	'United Arab Emirates',	'https://osu.ppy.sh/images/flags/AE.png'),
('AG',	'Antigua and Barbuda',	'https://osu.ppy.sh/images/flags/AG.png'),
('AL',	'Albania',	'https://osu.ppy.sh/images/flags/AL.png'),
('AM',	'Armenia',	'https://osu.ppy.sh/images/flags/AM.png'),
('AR',	'Argentina',	'https://osu.ppy.sh/images/flags/AR.png'),
('AT',	'Austria',	'https://osu.ppy.sh/images/flags/AT.png'),
('AU',	'Australia',	'https://osu.ppy.sh/images/flags/AU.png'),
('AW',	'Aruba',	'https://osu.ppy.sh/images/flags/AW.png'),
('AX',	'Aland Islands',	'https://osu.ppy.sh/images/flags/AX.png'),
('AZ',	'Azerbaijan',	'https://osu.ppy.sh/images/flags/AZ.png'),
('BA',	'Bosnia and Herzegovina',	'https://osu.ppy.sh/images/flags/BA.png'),
('BB',	'Barbados',	'https://osu.ppy.sh/images/flags/BB.png'),
('BD',	'Bangladesh',	'https://osu.ppy.sh/images/flags/BD.png'),
('BE',	'Belgium',	'https://osu.ppy.sh/images/flags/BE.png'),
('BG',	'Bulgaria',	'https://osu.ppy.sh/images/flags/BG.png'),
('BH',	'Bahrain',	'https://osu.ppy.sh/images/flags/BH.png'),
('BM',	'Bermuda',	'https://osu.ppy.sh/images/flags/BM.png'),
('BN',	'Brunei',	'https://osu.ppy.sh/images/flags/BN.png'),
('BO',	'Bolivia',	'https://osu.ppy.sh/images/flags/BO.png'),
('BR',	'Brazil',	'https://osu.ppy.sh/images/flags/BR.png'),
('BS',	'Bahamas',	'https://osu.ppy.sh/images/flags/BS.png'),
('BY',	'Belarus',	'https://osu.ppy.sh/images/flags/BY.png'),
('BZ',	'Belize',	'https://osu.ppy.sh/images/flags/BZ.png'),
('CA',	'Canada',	'https://osu.ppy.sh/images/flags/CA.png'),
('CF',	'Central African Republic',	'https://osu.ppy.sh/images/flags/CF.png'),
('CH',	'Switzerland',	'https://osu.ppy.sh/images/flags/CH.png'),
('CI',	'Cote D&#039;Ivoire',	'https://osu.ppy.sh/images/flags/CI.png'),
('CK',	'Cook Islands',	'https://osu.ppy.sh/images/flags/CK.png'),
('CL',	'Chile',	'https://osu.ppy.sh/images/flags/CL.png'),
('CN',	'China',	'https://osu.ppy.sh/images/flags/CN.png'),
('CO',	'Colombia',	'https://osu.ppy.sh/images/flags/CO.png'),
('CR',	'Costa Rica',	'https://osu.ppy.sh/images/flags/CR.png'),
('CV',	'Cabo Verde',	'https://osu.ppy.sh/images/flags/CV.png'),
('CY',	'Cyprus',	'https://osu.ppy.sh/images/flags/CY.png'),
('CZ',	'Czech Republic',	'https://osu.ppy.sh/images/flags/CZ.png'),
('DE',	'Germany',	'https://osu.ppy.sh/images/flags/DE.png'),
('DK',	'Denmark',	'https://osu.ppy.sh/images/flags/DK.png'),
('DO',	'Dominican Republic',	'https://osu.ppy.sh/images/flags/DO.png'),
('DZ',	'Algeria',	'https://osu.ppy.sh/images/flags/DZ.png'),
('EC',	'Ecuador',	'https://osu.ppy.sh/images/flags/EC.png'),
('EE',	'Estonia',	'https://osu.ppy.sh/images/flags/EE.png'),
('EG',	'Egypt',	'https://osu.ppy.sh/images/flags/EG.png'),
('ER',	'Eritrea',	'https://osu.ppy.sh/images/flags/ER.png'),
('ES',	'Spain',	'https://osu.ppy.sh/images/flags/ES.png'),
('EU',	'Europe',	'https://osu.ppy.sh/images/flags/EU.png'),
('FI',	'Finland',	'https://osu.ppy.sh/images/flags/FI.png'),
('FO',	'Faroe Islands',	'https://osu.ppy.sh/images/flags/FO.png'),
('FR',	'France',	'https://osu.ppy.sh/images/flags/FR.png'),
('GB',	'United Kingdom',	'https://osu.ppy.sh/images/flags/GB.png'),
('GE',	'Georgia',	'https://osu.ppy.sh/images/flags/GE.png'),
('GF',	'French Guiana',	'https://osu.ppy.sh/images/flags/GF.png'),
('GG',	'Guernsey',	'https://osu.ppy.sh/images/flags/GG.png'),
('GI',	'Gibraltar',	'https://osu.ppy.sh/images/flags/GI.png'),
('GL',	'Greenland',	'https://osu.ppy.sh/images/flags/GL.png'),
('GP',	'Guadeloupe',	'https://osu.ppy.sh/images/flags/GP.png'),
('GR',	'Greece',	'https://osu.ppy.sh/images/flags/GR.png'),
('GT',	'Guatemala',	'https://osu.ppy.sh/images/flags/GT.png'),
('GU',	'Guam',	'https://osu.ppy.sh/images/flags/GU.png'),
('GY',	'Guyana',	'https://osu.ppy.sh/images/flags/GY.png'),
('HK',	'Hong Kong',	'https://osu.ppy.sh/images/flags/HK.png'),
('HN',	'Honduras',	'https://osu.ppy.sh/images/flags/HN.png'),
('HR',	'Croatia',	'https://osu.ppy.sh/images/flags/HR.png'),
('HU',	'Hungary',	'https://osu.ppy.sh/images/flags/HU.png'),
('ID',	'Indonesia',	'https://osu.ppy.sh/images/flags/ID.png'),
('IE',	'Ireland',	'https://osu.ppy.sh/images/flags/IE.png'),
('IL',	'Israel',	'https://osu.ppy.sh/images/flags/IL.png'),
('IM',	'Isle of Man',	'https://osu.ppy.sh/images/flags/IM.png'),
('IN',	'India',	'https://osu.ppy.sh/images/flags/IN.png'),
('IQ',	'Iraq',	'https://osu.ppy.sh/images/flags/IQ.png'),
('IR',	'Iran, Islamic Republic of',	'https://osu.ppy.sh/images/flags/IR.png'),
('IS',	'Iceland',	'https://osu.ppy.sh/images/flags/IS.png'),
('IT',	'Italy',	'https://osu.ppy.sh/images/flags/IT.png'),
('JE',	'Jersey',	'https://osu.ppy.sh/images/flags/JE.png'),
('JM',	'Jamaica',	'https://osu.ppy.sh/images/flags/JM.png'),
('JO',	'Jordan',	'https://osu.ppy.sh/images/flags/JO.png'),
('JP',	'Japan',	'https://osu.ppy.sh/images/flags/JP.png'),
('KE',	'Kenya',	'https://osu.ppy.sh/images/flags/KE.png'),
('KG',	'Kyrgyzstan',	'https://osu.ppy.sh/images/flags/KG.png'),
('KH',	'Cambodia',	'https://osu.ppy.sh/images/flags/KH.png'),
('KR',	'South Korea',	'https://osu.ppy.sh/images/flags/KR.png'),
('KW',	'Kuwait',	'https://osu.ppy.sh/images/flags/KW.png'),
('KZ',	'Kazakhstan',	'https://osu.ppy.sh/images/flags/KZ.png'),
('LA',	'Lao People\'s Democratic Republ',	'https://osu.ppy.sh/images/flags/LA.png'),
('LB',	'Lebanon',	'https://osu.ppy.sh/images/flags/LB.png'),
('LC',	'Saint Lucia',	'https://osu.ppy.sh/images/flags/LC.png'),
('LI',	'Liechtenstein',	'https://osu.ppy.sh/images/flags/LI.png'),
('LK',	'Sri Lanka',	'https://osu.ppy.sh/images/flags/LK.png'),
('LR',	'Liberia',	'https://osu.ppy.sh/images/flags/LR.png'),
('LT',	'Lithuania',	'https://osu.ppy.sh/images/flags/LT.png'),
('LU',	'Luxembourg',	'https://osu.ppy.sh/images/flags/LU.png'),
('LV',	'Latvia',	'https://osu.ppy.sh/images/flags/LV.png'),
('LY',	'Libya',	'https://osu.ppy.sh/images/flags/LY.png'),
('MA',	'Morocco',	'https://osu.ppy.sh/images/flags/MA.png'),
('MD',	'Moldova',	'https://osu.ppy.sh/images/flags/MD.png'),
('ME',	'Montenegro',	'https://osu.ppy.sh/images/flags/ME.png'),
('MG',	'Madagascar',	'https://osu.ppy.sh/images/flags/MG.png'),
('MK',	'Macedonia, the Former Yugoslav',	'https://osu.ppy.sh/images/flags/MK.png'),
('MM',	'Myanmar',	'https://osu.ppy.sh/images/flags/MM.png'),
('MN',	'Mongolia',	'https://osu.ppy.sh/images/flags/MN.png'),
('MO',	'Macau',	'https://osu.ppy.sh/images/flags/MO.png'),
('MP',	'Northern Mariana Islands',	'https://osu.ppy.sh/images/flags/MP.png'),
('MQ',	'Martinique',	'https://osu.ppy.sh/images/flags/MQ.png'),
('MT',	'Malta',	'https://osu.ppy.sh/images/flags/MT.png'),
('MU',	'Mauritius',	'https://osu.ppy.sh/images/flags/MU.png'),
('MV',	'Maldives',	'https://osu.ppy.sh/images/flags/MV.png'),
('MX',	'Mexico',	'https://osu.ppy.sh/images/flags/MX.png'),
('MY',	'Malaysia',	'https://osu.ppy.sh/images/flags/MY.png'),
('NC',	'New Caledonia',	'https://osu.ppy.sh/images/flags/NC.png'),
('NI',	'Nicaragua',	'https://osu.ppy.sh/images/flags/NI.png'),
('NL',	'Netherlands',	'https://osu.ppy.sh/images/flags/NL.png'),
('NO',	'Norway',	'https://osu.ppy.sh/images/flags/NO.png'),
('NP',	'Nepal',	'https://osu.ppy.sh/images/flags/NP.png'),
('NZ',	'New Zealand',	'https://osu.ppy.sh/images/flags/NZ.png'),
('OM',	'Oman',	'https://osu.ppy.sh/images/flags/OM.png'),
('PA',	'Panama',	'https://osu.ppy.sh/images/flags/PA.png'),
('PE',	'Peru',	'https://osu.ppy.sh/images/flags/PE.png'),
('PF',	'French Polynesia',	'https://osu.ppy.sh/images/flags/PF.png'),
('PH',	'Philippines',	'https://osu.ppy.sh/images/flags/PH.png'),
('PK',	'Pakistan',	'https://osu.ppy.sh/images/flags/PK.png'),
('PL',	'Poland',	'https://osu.ppy.sh/images/flags/PL.png'),
('PM',	'Saint Pierre and Miquelon',	'https://osu.ppy.sh/images/flags/PM.png'),
('PR',	'Puerto Rico',	'https://osu.ppy.sh/images/flags/PR.png'),
('PS',	'Palestinian Territory Occupied',	'https://osu.ppy.sh/images/flags/PS.png'),
('PT',	'Portugal',	'https://osu.ppy.sh/images/flags/PT.png'),
('PY',	'Paraguay',	'https://osu.ppy.sh/images/flags/PY.png'),
('QA',	'Qatar',	'https://osu.ppy.sh/images/flags/QA.png'),
('RE',	'Reunion',	'https://osu.ppy.sh/images/flags/RE.png'),
('RO',	'Romania',	'https://osu.ppy.sh/images/flags/RO.png'),
('RS',	'Serbia',	'https://osu.ppy.sh/images/flags/RS.png'),
('RU',	'Russian Federation',	'https://osu.ppy.sh/images/flags/RU.png'),
('SA',	'Saudi Arabia',	'https://osu.ppy.sh/images/flags/SA.png'),
('SD',	'Sudan',	'https://osu.ppy.sh/images/flags/SD.png'),
('SE',	'Sweden',	'https://osu.ppy.sh/images/flags/SE.png'),
('SG',	'Singapore',	'https://osu.ppy.sh/images/flags/SG.png'),
('SI',	'Slovenia',	'https://osu.ppy.sh/images/flags/SI.png'),
('SK',	'Slovakia',	'https://osu.ppy.sh/images/flags/SK.png'),
('SN',	'Senegal',	'https://osu.ppy.sh/images/flags/SN.png'),
('SR',	'Suriname',	'https://osu.ppy.sh/images/flags/SR.png'),
('SV',	'El Salvador',	'https://osu.ppy.sh/images/flags/SV.png'),
('SY',	'Syrian Arab Republic',	'https://osu.ppy.sh/images/flags/SY.png'),
('TH',	'Thailand',	'https://osu.ppy.sh/images/flags/TH.png'),
('TJ',	'Tajikistan',	'https://osu.ppy.sh/images/flags/TJ.png'),
('TN',	'Tunisia',	'https://osu.ppy.sh/images/flags/TN.png'),
('TR',	'Turkey',	'https://osu.ppy.sh/images/flags/TR.png'),
('TT',	'Trinidad and Tobago',	'https://osu.ppy.sh/images/flags/TT.png'),
('TW',	'Taiwan',	'https://osu.ppy.sh/images/flags/TW.png'),
('UA',	'Ukraine',	'https://osu.ppy.sh/images/flags/UA.png'),
('US',	'United States',	'https://osu.ppy.sh/images/flags/US.png'),
('UY',	'Uruguay',	'https://osu.ppy.sh/images/flags/UY.png'),
('UZ',	'Uzbekistan',	'https://osu.ppy.sh/images/flags/UZ.png'),
('VE',	'Venezuela',	'https://osu.ppy.sh/images/flags/VE.png'),
('VI',	'Virgin Islands, U.S.',	'https://osu.ppy.sh/images/flags/VI.png'),
('VN',	'Vietnam',	'https://osu.ppy.sh/images/flags/VN.png'),
('ZA',	'South Africa',	'https://osu.ppy.sh/images/flags/ZA.png');

DROP TABLE IF EXISTS `DeletedComments`;
CREATE TABLE `DeletedComments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `PostText` varchar(1000) NOT NULL,
  `MedalID` int(11) DEFAULT NULL,
  `VersionID` int(10) DEFAULT NULL,
  `ProfileID` int(20) DEFAULT NULL,
  `Username` varchar(100) NOT NULL,
  `UserID` int(20) NOT NULL,
  `AvatarURL` varchar(200) NOT NULL,
  `PostDate` datetime NOT NULL,
  `Reported` int(5) NOT NULL DEFAULT '0',
  `ParentComment` int(10) DEFAULT NULL,
  `ParentCommenter` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `DeletedMaps`;
CREATE TABLE `DeletedMaps` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `MedalName` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `BeatmapID` int(20) NOT NULL,
  `MapsetID` int(20) NOT NULL,
  `Gamemode` varchar(10) COLLATE latin1_german1_ci NOT NULL,
  `SongTitle` varchar(80) COLLATE latin1_german1_ci NOT NULL,
  `Artist` varchar(80) COLLATE latin1_german1_ci NOT NULL,
  `Mapper` varchar(40) COLLATE latin1_german1_ci NOT NULL,
  `Source` varchar(200) COLLATE latin1_german1_ci NOT NULL,
  `bpm` double NOT NULL,
  `Difficulty` double NOT NULL,
  `DifficultyName` varchar(80) COLLATE latin1_german1_ci NOT NULL,
  `DownloadUnavailable` tinyint(1) NOT NULL,
  `Votes` int(20) NOT NULL,
  `SubmittedBy` varchar(30) COLLATE latin1_german1_ci NOT NULL,
  `DeletionDate` date NOT NULL,
  `Note` varchar(500) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


DROP TABLE IF EXISTS `Donations`;
CREATE TABLE `Donations` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `osuID` int(10) DEFAULT NULL,
  `Username` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL,
  `Message` varchar(500) COLLATE latin1_german1_ci DEFAULT NULL,
  `DonoDate` datetime NOT NULL,
  `Code` varchar(20) COLLATE latin1_german1_ci DEFAULT NULL,
  `DonoAmount` decimal(10,2) DEFAULT NULL,
  `Checked` tinyint(1) NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


DROP TABLE IF EXISTS `FAQ`;
CREATE TABLE `FAQ` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `App` int(11) NOT NULL,
  `Title` text NOT NULL,
  `Content` text NOT NULL,
  `LocalizationPrefix` text NOT NULL,
  PRIMARY KEY (`ID`),
  KEY `App` (`App`),
  CONSTRAINT `FAQ_ibfk_1` FOREIGN KEY (`App`) REFERENCES `Apps` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `FAQ` (`ID`, `App`, `Title`, `Content`, `LocalizationPrefix`) VALUES
(1,	-1,	'Does the website\'s name have any meaning?',	'<p>[mulraf]: The name \"Osekai\" is a portmanteau of \"osu!\", the name of the rhythm game we all know and love, and the name of the anime genre isekai. Isekai generally means \"other world\" or \"different world\". While I\'m not the biggest fan of the isekai genre, this website was originally intended for something else. I wanted to build up a \"team\" for osu! with a website that hosts not only its own community, but also leaderboards, minigames and much more. Sort of like a \"different world\" within osu!</p>',	'nameMeaning'),
(2,	-1,	'I found a bug',	'<p>Please contact us directly via the osu! medal hunters Discord server or through osu! DMs and we will try to fix it!</p>',	'foundBug'),
(3,	-1,	'I have an idea!',	'<p>Great! Feel free to share your ideas via the osu! medal hunters Discord server or directly with us in osu! DMs. Keep in mind though that we still have a lot of ideas and plans waiting to come to life as well at the moment.</p>',	'haveIdea'),
(4,	-1,	'What are your future plans?',	'<p>We are currently working on a plethora of feature ideas and new Osekai apps, be on the lookout for any new things!<br><br>If you want to get secret updates on current progress and early access to unfinished features, please consider donating and therefore acquiring access to our super-secret Osekai Development channel.</p>',	'futurePlans'),
(5,	1,	'I can\'t find my name in the rankings but i should be in there!',	'<p>Sorry, but we can\'t collect every user\'s data and regularly keep that data up to date at the same time. Therefore, data on people with lower ranks will not be collected and included by default, though we do have good news for you: you can log in with your osu! credentials and your data will start being regularly updated. You can also do this if you are already one of the people with a rank high enough to get updated so that even if you leave the range of ranks that get updated you will still continue getting regular updates.</p>',	'cantFindName'),
(6,	1,	'My data is there, but it\'s not up-to-date. How frequently do you renew your data?',	'<p>Right now, small data updates are happening every 12 hours or so, with a full sweep every 3 days. If your data hasn\'t been updated in 4 days from now, there\'s probably an issue. Please contact us!</p>',	'dataNotUpdated'),
(7,	1,	'What is SPP?',	'<p>SPP is an abbreviation for \"Standard deviated PP\". SPP is the ranking method all-mode players on their respective discord chose as their main ranking. You can read about standard deviation <a class=\"osekai__button\" href=\"https://en.wikipedia.org/wiki/Standard_deviation\">here</a></p>',	'whatIsSPP'),
(8,	1,	'The percentages for the Medal Rarity don\'t look correct.',	'<p>Sadly we can\'t track everyone\'s stats and we also don\'t have any other means to get this rate, so it\'s just the rarity of the medal among all the players that we have tracked. Since this means that the percentages are calculated with the data of roughly 50.000 people it is a pretty good estimate, but since it\'s mostly the top 10.000 of each gamemode the percentages of harder medals are a bit underestimated.</p>',	'wrongRarityPercentages'),
(9,	1,	'What are the color codes for the user ranking in Osekai Rankings?',	'<ul class=\"faq__mc\">\r\n    <li class=\"col95club\">95% club - over 95% of medals</li>\r\n    <li class=\"col90club\">90% club - over 90% of medals</li>\r\n    <li class=\"col80club\">80% club - over 80% of medals</li>\r\n    <li class=\"col60club\">60% club - over 60% of medals</li>\r\n    <li class=\"col40club\">40% club - over 40% of medals</li>\r\n    <li class=\"colnoclub\">No club - less than 40% of medals</li>\r\n</ul>\r\n',	'colorCodes'),
(10,	2,	'I found an incorrect solution, beatmap or comment.',	'<p>Sometimes - for example when star ratings of beatmaps get changed - this may occur.<br><br>If you think the solution of a medal is incorrect, please don\'t hesitate to contact the developers or moderators via discord or the osu! chat in order to check and correct it. You may also write your findings as a comment if you are uncertain and want to start a discussion about it.<br><br> If you think a beatmap is incorrect, please use the designated report button on the lower left of the panel.<br><br> If you think a comment suggestion is incorrect, please just correct them via a reply in the comments. We don\'t try to verify and delete comments that suggest wrong solutions. We only delete harassment, offensiveness or spam comments.</p>',	'somethingIncorrect'),
(11,	2,	'How can i add beatmap suggestions or comments?',	'<p>You need to log in with your osu! account using the button on the top right corner of the screen. We use osu!\'s secure OAuth2 system to do our logins, so we only get basic profile info.</p>',	'addSuggestionsOrComments'),
(12,	5,	'What files do I need to send?',	'<p>We only need the .exe file and the DLL files for us to be able to upload the version! Here\'s an example:</p><img src=\"img/faqs/snapshots_files.png\"><br>Don\'t worry if you don\'t have all these files! Depending on your osu! version, some may be missing or more may be there. Either way, we\'ll try to make it work anyway :)',	'whatFilesToSend'),
(13,	5,	'Are these old versions safe to run?',	'<p>Yes, they are! We always check versions for any viruses or bad stuff and we quickly remove versions we decide are not safe to run and have been tampered with in a bad way. For your information; yes, we know false positives are a thing!</p>',	'areVersionsSafe'),
(14,	5,	'What do I do if it says an old version of the .NET framework is required to run an old osu! version?',	'<p>Most old osu! versions require old versions of the .NET framework, and what you\'re seeing is just Windows detecting that it requires that version of the .NET framework in order to run your old osu! version of choice. Just let Windows do its job automatically downloading and installing the required .NET framework version and in a few minutes your old osu! version should be up and running!</p>',	'netFrameworkVersion'),
(15,	3,	'What are \'clubs\'?',	'Medal clubs are roles you get placed into when you get to certain percentages of medals! These appear on your Osekai Profile, and are also colour matched across the site\'s medal completion bars.\r\n\r\nThe medal clubs we currently have are:\r\n<ul class=\"faq__mc\">\r\n    <li class=\"col95club\">95% club - over 95% of medals</li>\r\n    <li class=\"col90club\">90% club - over 90% of medals</li>\r\n    <li class=\"col80club\">80% club - over 80% of medals</li>\r\n    <li class=\"col60club\">60% club - over 60% of medals</li>\r\n    <li class=\"col40club\">40% club - over 40% of medals</li>\r\n    <li class=\"colnoclub\">No club - less than 40% of medals</li>\r\n</ul>\r\n\r\nYou can get into each club by getting more medals!',	'whatAreClubs'),
(16,	3,	'How are the numbers in \"All\" mode calculated?',	'<p>\r\nThe basic values are usually additive. So for PP example it would be\r\n<br><code>Standard pp + Taiko pp + Catch pp + Mania pp</code><br>\r\n<br>\r\nThe ranks for medals are drawn from Osekai Rankings which in turn uses our own database that gets updated frequently. For more Information on Osekai Rankings, look at the specific FAQ sections.\r\n<br><br>\r\nLastly the Accuracy is calculated similar to how osu! calculates it for one single gamemode.\r\n<br><code>(pp * accuracy) / Σ(pp * accuracy)</code><br> for all of the 4 gamemodes.\r\n</p>',	'howAllModeNumbersCalculated'),
(17,	3,	'What does \'Show Medals from All Modes\' mean?',	'On Osekai Profiles by default, on the medals panel we show medals from all mode, no matter what mode you selected. This means a catch dedication medal would show up, even if you have standard selected. If you want medals to be mode-specific you can disable this option! Keep in mind this also changes the percentage and clubs.',	'WhatDoesShowMedalsFromAllModesMean'),
(18,	3,	'Why is there no medal rank for me?',	'<p>\r\nThe medal rank is - like the ranks in all mode - calculated via our own database. We don\'t have all users that exist in our database. Only the top 10.000 players of every gamemode are in our Rankings. There are also some manually added players in our Database. If you log in on Osekai you will be added to the process. Also you can manually add other players than yourself on the Osekai Rankings Homepage.\r\n</p>',	'whyNoMedalRankForMe');

DROP TABLE IF EXISTS `GlobalCache`;
CREATE TABLE `GlobalCache` (
  `Title` varchar(250) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `Expiration` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Data` longtext NOT NULL,
  PRIMARY KEY (`Title`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP TABLE IF EXISTS `Goals`;
CREATE TABLE `Goals` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(20) NOT NULL,
  `Value` varchar(200) COLLATE latin1_german1_ci NOT NULL,
  `Type` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `Gamemode` varchar(20) COLLATE latin1_german1_ci DEFAULT NULL,
  `CreationDate` datetime NOT NULL,
  `Claimed` datetime(1) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UserID` (`UserID`,`Value`,`Type`,`Gamemode`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


DROP TABLE IF EXISTS `GroupAssignments`;
CREATE TABLE `GroupAssignments` (
  `UserId` int(11) NOT NULL,
  `GroupId` int(11) NOT NULL,
  KEY `GroupId` (`GroupId`),
  CONSTRAINT `GroupAssignments_ibfk_1` FOREIGN KEY (`GroupId`) REFERENCES `Groups` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `GroupAssignments` (`UserId`, `GroupId`) VALUES
(1309242,	2),
(10379965,	2),
(1699875,	1),
(2330619,	6),
(3357640,	3),
(10238680,	9),
(8923804,	8),
(16487835,	10),
(8438068,	10),
(14311450,	10),
(12572068,	10),
(14125695,	3),
(12433422,	6),
(15657428,	10),
(18152711,	1),
(7671790,	6),
(13175102,	11),
(2211396,	3),
(7279762,	3),
(10504284,	6),
(17416390,	6),
(6291386,	6),
(11539225,	7),
(13641450,	6),
(10238680,	1),
(9350342,	2),
(1309242,	12),
(10379965,	12),
(26544843,	13),
(18152711,	5),
(18152711,	4),
(18152711,	13),
(10238680,	13),
(9350342,	5),
(1699875,	5),
(1309242,	5),
(9507660,	5),
(17279598,	5),
(6291386,	5),
(10249166,	5),
(19471527,	5),
(10504284,	5),
(29534443,	5),
(14398471,	5),
(14522883,	5),
(13771539,	5),
(12985528,	5),
(13903087,	5),
(4687701,	5),
(14694998,	5),
(11578193,	5),
(654296,	5),
(8036887,	5),
(14269506,	5),
(14706035,	5),
(16738509,	5),
(12433422,	5),
(22136262,	5),
(8923804,	5),
(9079969,	5),
(9920144,	5),
(9843286,	5),
(6403393,	5),
(16598079,	5),
(11592579,	5),
(12091015,	5),
(17268434,	5),
(18065598,	5),
(12716143,	5),
(12716143,	5),
(6567341,	5),
(7563700,	5),
(27098548,	5),
(11521543,	5),
(7227109,	5),
(18068988,	5),
(18847055,	5),
(12305683,	5),
(16139008,	5),
(7150814,	5),
(17416390,	5),
(12445773,	5),
(21893727,	5),
(10491903,	5),
(4394183,	5),
(19637339,	5),
(19516462,	5),
(12514014,	5),
(17189658,	5),
(14010215,	5),
(3031177,	5),
(18467846,	5),
(16818802,	5),
(9767342,	5),
(17517577,	5),
(7197172,	2),
(18152711,	6),
(14889628,	6),
(18152711,	14),
(10379965,	13),
(15716075,	6),
(6403393,	6),
(1699875,	4),
(9350342,	4),
(17058819,	4);

DROP TABLE IF EXISTS `Groups`;
CREATE TABLE `Groups` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text COLLATE utf8mb4_bin NOT NULL,
  `ShortName` text COLLATE utf8mb4_bin NOT NULL,
  `Description` text COLLATE utf8mb4_bin,
  `Colour` text COLLATE utf8mb4_bin,
  `Order` int(11) DEFAULT NULL,
  `Hidden` int(1) DEFAULT '0',
  `ForceVisible` int(1) DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `Groups` (`Id`, `Name`, `ShortName`, `Description`, `Colour`, `Order`, `Hidden`, `ForceVisible`) VALUES
(1,	'??groups.communityManager.name??',	'CMT',	'??groups.communityManager.description??',	'255,124,89',	4,	0,	0),
(2,	'??groups.developers.name??',	'DEV',	'??groups.developers.description??',	'99,128,255',	6,	0,	0),
(3,	'??groups.appDeveloper.name??',	'APP DEV',	'??groups.appDeveloper.description??',	'87,200,180',	10,	0,	0),
(4,	'??groups.toolsContributor.name??',	'TOOLS',	'??groups.toolsContributor.description??',	'30,90,144',	16,	0,	0),
(5,	'??groups.translator.name??',	'TRANSLATOR',	'??groups.translator.description??',	'144,30,126',	18,	0,	0),
(6,	'Supporter - Rank 1',	'<i class=\"fas fa-heart\" aria-hidden=\"true\"></i>',	'',	'255,114,241',	24,	1,	1),
(7,	'Supporter - Rank 2',	'<i class=\"fas fa-heart\" aria-hidden=\"true\"></i><i class=\"fas fa-heart\" aria-hidden=\"true\"></i>',	'',	'255,114,241',	22,	1,	1),
(8,	'Supporter - Rank 3',	'<i class=\"fas fa-heart\" aria-hidden=\"true\"></i><i class=\"fas fa-heart\" aria-hidden=\"true\"></i><i class=\"fas fa-heart\" aria-hidden=\"true\"></i>',	'',	'255,114,241',	20,	1,	1),
(9,	'chromb',	'chromb',	'chromb',	'100,100,100',	1,	1,	0),
(10,	'??groups.snapshotsModerator.name??',	'<i class=\"oif-app-snapshots\"></i> MOD',	'??groups.snapshotsModerator.description??',	'28,25,171',	14,	0,	0),
(11,	'??groups.moderator.name??\r\n',	'MOD',	'??groups.moderator.description??\r\n',	'29,74,29',	12,	0,	0),
(12,	'??groups.lead.name??\r\n',	'LEAD',	'??groups.lead.description??\r\n',	'99,199,255',	0,	0,	0),
(13,	'??groups.social.name??',	'SOCIAL',	'??groups.social.description??',	'157,32,255',	8,	0,	0),
(14,	'??groups.teamLead.name??',	'TEAM LEAD',	'??groups.teamLead.description??',	'150,20,20',	2,	0,	0);

DROP VIEW IF EXISTS `MedalListing`;
CREATE TABLE `MedalListing` (`MedalID` int(5), `Name` varchar(50), `Description` varchar(500), `PossessionRate` varchar(50), `link` varchar(70), `restriction` varchar(8), `grouping` varchar(30));


DROP TABLE IF EXISTS `MedalRarity`;
CREATE TABLE `MedalRarity` (
  `id` int(11) NOT NULL,
  `frequency` float NOT NULL,
  `count` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

INSERT INTO `MedalRarity` (`id`, `frequency`, `count`) VALUES
(1,	59.1559,	74214),
(3,	46.195,	57954),
(4,	37.6494,	47233),
(5,	20.8991,	26219),
(6,	13.1721,	16525),
(7,	4.98984,	6260),
(8,	3.5447,	4447),
(9,	3.63078,	4555),
(10,	5.0823,	6376),
(11,	3.83723,	4814),
(12,	4.066,	5101),
(13,	36.0902,	45277),
(14,	3.37013,	4228),
(15,	47.7749,	59936),
(16,	15.5259,	19478),
(17,	6.39831,	8027),
(18,	2.82571,	3545),
(19,	2.72129,	3414),
(20,	37.8327,	47463),
(21,	27.0838,	33978),
(22,	22.1434,	27780),
(23,	19.8581,	24913),
(24,	9.11084,	11430),
(25,	3.36455,	4221),
(26,	2.34905,	2947),
(27,	2.44948,	3073),
(28,	13.9189,	17462),
(31,	28.3464,	35562),
(32,	15.5514,	19510),
(33,	4.61281,	5787),
(34,	2.92296,	3667),
(35,	2.03978,	2559),
(36,	2.15695,	2706),
(37,	2.65753,	3334),
(38,	12.7249,	15964),
(39,	42.8488,	53756),
(40,	29.5732,	37101),
(41,	7.01287,	8798),
(42,	22.416,	28122),
(43,	19.1862,	24070),
(44,	8.05707,	10108),
(45,	0.475868,	597),
(46,	42.3993,	53192),
(47,	27.5653,	34582),
(48,	14.0799,	17664),
(50,	58.4927,	73382),
(51,	53.1218,	66644),
(52,	33.2023,	41654),
(53,	8.01164,	10051),
(54,	56.1731,	70472),
(55,	53.746,	67427),
(56,	56.3636,	70711),
(57,	51.6751,	64829),
(58,	45.064,	56535),
(59,	38.5692,	48387),
(60,	31.1681,	39102),
(61,	25.2234,	31644),
(62,	16.2218,	20351),
(63,	43.4132,	54464),
(64,	44.9014,	56331),
(65,	40.283,	50537),
(66,	35.5115,	44551),
(67,	29.0582,	36455),
(68,	20.8904,	26208),
(69,	11.0677,	13885),
(70,	1.63963,	2057),
(71,	40.1682,	50393),
(72,	41.3519,	51878),
(73,	28.0515,	35192),
(74,	18.4903,	23197),
(75,	14.2641,	17895),
(76,	8.20135,	10289),
(77,	4.73955,	5946),
(78,	2.7755,	3482),
(79,	41.3622,	51891),
(80,	33.6989,	42277),
(81,	19.4755,	24433),
(82,	11.5133,	14444),
(83,	5.85708,	7348),
(84,	3.13021,	3927),
(85,	1.44434,	1812),
(86,	0.558766,	701),
(87,	53.3697,	66955),
(88,	44.3705,	55665),
(89,	33.035,	41444),
(90,	25.1365,	31535),
(91,	19.5584,	24537),
(92,	15.8041,	19827),
(93,	10.0881,	12656),
(94,	2.40564,	3018),
(95,	29.1228,	36536),
(96,	27.2169,	34145),
(97,	18.6306,	23373),
(98,	13.137,	16481),
(99,	7.60113,	9536),
(100,	3.68738,	4626),
(101,	1.70499,	2139),
(102,	1.79108,	2247),
(103,	28.3392,	35553),
(104,	18.2512,	22897),
(105,	10.3455,	12979),
(106,	4.77462,	5990),
(107,	2.59137,	3251),
(108,	1.38297,	1735),
(109,	0.602607,	756),
(110,	0.176159,	221),
(111,	42.0167,	52712),
(112,	30.8955,	38760),
(113,	21.9904,	27588),
(114,	17.2428,	21632),
(115,	8.84062,	11091),
(116,	2.50767,	3146),
(117,	0.286955,	360),
(118,	0.023913,	30),
(119,	33.1784,	41624),
(120,	27.1173,	34020),
(121,	50.7959,	63726),
(122,	55.6997,	69878),
(123,	48.0148,	60237),
(124,	52.0155,	65256),
(125,	32.807,	41158),
(126,	35.4677,	44496),
(127,	51.691,	64849),
(128,	40.7509,	51124),
(131,	17.3425,	21757),
(132,	48.2253,	60501),
(133,	8.6278,	10824),
(134,	8.27149,	10377),
(135,	9.7788,	12268),
(136,	9.33801,	11715),
(137,	31.2311,	39181),
(138,	25.1899,	31602),
(139,	10.3304,	12960),
(140,	9.75649,	12240),
(141,	16.4736,	20667),
(142,	6.92679,	8690),
(143,	11.6879,	14663),
(144,	10.4715,	13137),
(145,	0.68949,	865),
(146,	0.500578,	628),
(147,	7.70236,	9663),
(148,	35.5052,	44543),
(149,	2.28847,	2871),
(150,	8.19736,	10284),
(151,	4.90534,	6154),
(152,	32.5846,	40879),
(153,	6.94034,	8707),
(154,	5.80766,	7286),
(155,	4.74752,	5956),
(156,	7.21454,	9051),
(157,	3.4076,	4275),
(158,	7.18266,	9011),
(159,	2.15854,	2708),
(160,	5.3844,	6755),
(161,	3.62202,	4544),
(162,	1.6492,	2069),
(163,	1.71456,	2151),
(164,	5.70962,	7163),
(165,	2.49412,	3129),
(166,	7.88888,	9897),
(167,	1.83014,	2296),
(168,	22.6886,	28464),
(169,	1.41326,	1773),
(170,	4.27803,	5367),
(171,	3.64991,	4579),
(172,	3.83644,	4813),
(173,	2.0876,	2619),
(174,	4.57614,	5741),
(175,	8.96497,	11247),
(176,	50.5376,	63402),
(177,	24.9109,	31252),
(178,	11.0454,	13857),
(179,	1.64601,	2065),
(180,	5.28476,	6630),
(181,	1.93934,	2433),
(182,	3.59093,	4505),
(183,	1.88036,	2359),
(184,	2.36977,	2973),
(185,	1.93775,	2431),
(186,	1.40688,	1765),
(187,	1.16695,	1464),
(188,	1.41007,	1769),
(189,	1.18688,	1489),
(190,	1.55514,	1951),
(191,	1.42282,	1785),
(192,	0.0518114,	65),
(193,	1.69383,	2125),
(194,	6.55853,	8228),
(195,	0.279782,	351),
(196,	1.08963,	1367),
(197,	3.73281,	4683),
(199,	0.157028,	197),
(200,	3.36854,	4226),
(201,	3.52716,	4425),
(202,	3.57977,	4491),
(204,	3.57578,	4486),
(205,	1.24427,	1561),
(206,	1.17094,	1469),
(207,	1.03703,	1301),
(208,	1.22355,	1535),
(209,	1.1805,	1481),
(210,	1.25065,	1569),
(213,	1.09203,	1370),
(214,	1.03782,	1302),
(215,	1.17652,	1476),
(216,	2.28847,	2871),
(217,	0.0597824,	75),
(218,	3.31115,	4154),
(219,	0.78833,	989),
(220,	1.71854,	2156),
(221,	3.56941,	4478),
(222,	24.2015,	30362),
(223,	2.26695,	2844),
(224,	3.03615,	3809),
(225,	3.39883,	4264),
(226,	1.11195,	1395),
(227,	1.11036,	1393),
(228,	0.997967,	1252),
(229,	0.849707,	1066),
(230,	0.893547,	1121),
(231,	0.836156,	1049),
(232,	0.885576,	1111),
(233,	1.01391,	1272),
(234,	1.03862,	1303),
(235,	0.781954,	981),
(236,	0.950939,	1193),
(237,	0.782751,	982),
(238,	0.737316,	925),
(239,	1.13985,	1430),
(240,	0.836156,	1049),
(241,	1.44833,	1817),
(242,	6.67969,	8380),
(243,	0.0940576,	118),
(244,	1.62927,	2044),
(245,	0.0023913,	3),
(246,	1.11992,	1405),
(247,	0.36268,	455),
(248,	0.747678,	938),
(249,	0.760432,	954),
(250,	1.00833,	1265),
(251,	0.686302,	861),
(252,	0.970866,	1218),
(253,	0.975649,	1224),
(254,	0.986011,	1237),
(255,	1.23152,	1545),
(256,	0.932605,	1170),
(257,	0.902316,	1132),
(258,	0.646447,	811),
(259,	1.40289,	1760),
(260,	0.875214,	1098),
(261,	0.95333,	1196),
(262,	1.22275,	1534),
(263,	0.936591,	1175),
(264,	0.91746,	1151),
(265,	0.491013,	616),
(266,	0.210434,	264),
(267,	0.0589853,	74),
(268,	2.39209,	3001),
(269,	3.27368,	4107),
(270,	2.47101,	3100),
(271,	0.817026,	1025),
(272,	6.71635,	8426),
(273,	1.57028,	1970),
(274,	1.40927,	1768),
(275,	2.19202,	2750),
(276,	3.8747,	4861),
(277,	0.363477,	456),
(278,	0.219202,	275),
(279,	1.48021,	1857),
(280,	0.235144,	295),
(281,	0.0988402,	124),
(282,	0.785142,	985),
(283,	0.654418,	821),
(284,	0.451158,	566),
(285,	0.585867,	735),
(286,	0.355506,	446),
(287,	3.02818,	3799),
(288,	0.593838,	745),
(289,	0.515723,	647),
(290,	0.627317,	787),
(291,	0.0685505,	86),
(292,	0.428839,	538),
(293,	0.306086,	384),
(294,	0.132318,	166),
(295,	0.336376,	422),
(296,	0.298912,	375),
(297,	0.836156,	1049),
(298,	0.200869,	252),
(299,	0.585867,	735);

DROP TABLE IF EXISTS `Medals`;
CREATE TABLE `Medals` (
  `medalid` int(5) NOT NULL,
  `name` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL,
  `link` varchar(70) COLLATE latin1_german1_ci DEFAULT NULL,
  `description` varchar(500) COLLATE latin1_german1_ci DEFAULT NULL,
  `restriction` varchar(8) COLLATE latin1_german1_ci DEFAULT NULL,
  `grouping` varchar(30) COLLATE latin1_german1_ci DEFAULT NULL,
  `instructions` varchar(500) COLLATE latin1_german1_ci DEFAULT NULL,
  `ordering` int(11) NOT NULL,
  `packid` text COLLATE latin1_german1_ci,
  `video` text COLLATE latin1_german1_ci,
  `date` date DEFAULT NULL,
  `firstachieveddate` date DEFAULT NULL,
  `firstachievedby` int(10) DEFAULT NULL,
  PRIMARY KEY (`medalid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

INSERT INTO `Medals` (`medalid`, `name`, `link`, `description`, `restriction`, `grouping`, `instructions`, `ordering`, `packid`, `video`, `date`, `firstachieveddate`, `firstachievedby`) VALUES
(1,	'500 Combo',	'https://assets.ppy.sh/medals/web/osu-combo-500.png',	'500 big ones! You\'re moving up in the world!',	'osu',	'Skill',	'aiming for a combo of 500 or higher on any beatmap',	0,	NULL,	NULL,	'2008-08-02',	NULL,	NULL),
(3,	'750 Combo',	'https://assets.ppy.sh/medals/web/osu-combo-750.png',	'750 notes back to back? Woah.',	'osu',	'Skill',	'aiming for a combo of 750 or higher on any beatmap',	0,	NULL,	NULL,	'2008-08-02',	NULL,	NULL),
(4,	'1,000 Combo',	'https://assets.ppy.sh/medals/web/osu-combo-1000.png',	'A thousand reasons why you rock at this game.',	'osu',	'Skill',	'aiming for a combo of at least 1,000 on any beatmap (try a <a href=\'/p/beatmaplist?q=marathon\'>marathon</a>)',	0,	'',	'',	'2008-08-02',	NULL,	NULL),
(5,	'2,000 Combo',	'https://assets.ppy.sh/medals/web/osu-combo-2000.png',	'Nothing can stop you now.',	'osu',	'Skill',	'aiming for a combo of at least 2,000 on any beatmap (try a <a href=\'/p/beatmaplist?q=marathon\'>marathon</a>)',	0,	NULL,	NULL,	'2008-08-02',	NULL,	NULL),
(6,	'Don\'t let the bunny distract you!',	'https://assets.ppy.sh/medals/web/all-secret-bunny.png',	'The order was indeed, not a rabbit.',	'osu',	'Hush-Hush',	NULL,	0,	'',	'y9NMDXr1y5I',	'2008-08-11',	NULL,	NULL),
(7,	'Video Game Pack vol.1',	'https://assets.ppy.sh/medals/web/all-packs-gamer-1.png',	'A whole pack of video game goodness, done and dusted. Go you!',	'osu',	'Beatmap Packs',	NULL,	0,	'40',	NULL,	'2008-08-26',	NULL,	NULL),
(8,	'Rhythm Game Pack vol.1',	'https://assets.ppy.sh/medals/web/all-packs-rhythm-1.png',	'Many beats were clicked, but the rhythm isn\'t over yet.',	'osu',	'Beatmap Packs',	NULL,	3,	'41',	NULL,	'2008-08-26',	NULL,	NULL),
(9,	'Internet! Pack vol.1',	'https://assets.ppy.sh/medals/web/all-packs-internet-1.png',	'Did somebody say something about IRC and ICQ?',	'osu',	'Beatmap Packs',	NULL,	2,	'42',	NULL,	'2008-08-26',	NULL,	NULL),
(10,	'Anime Pack vol.1',	'https://assets.ppy.sh/medals/web/all-packs-anime-1.png',	'I-it\'s not like I\'m proud of you or anything..',	'osu',	'Beatmap Packs',	NULL,	1,	'43,0,0,0',	'',	'2008-08-26',	'0000-00-00',	0),
(11,	'Video Game Pack vol.2',	'https://assets.ppy.sh/medals/web/all-packs-gamer-2.png',	'The sequel was no match for your skills, obviously.',	'osu',	'Beatmap Packs',	NULL,	0,	'48',	'',	'0000-00-00',	NULL,	NULL),
(12,	'Anime Pack vol.2',	'https://assets.ppy.sh/medals/web/all-packs-anime-2.png',	'Truly dedicated to 2D.',	'osu',	'Beatmap Packs',	NULL,	1,	'49',	NULL,	'0000-00-00',	NULL,	NULL),
(13,	'Catch 20,000 fruits',	'https://assets.ppy.sh/medals/web/fruits-hits-20000.png',	'That is a lot of dietary fiber.',	'fruits',	'Dedication',	NULL,	1,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(14,	'Video Game Pack vol.3',	'https://assets.ppy.sh/medals/web/all-packs-gamer-3.png',	'True dedication to the gaming art.',	'osu',	'Beatmap Packs',	NULL,	0,	'70',	NULL,	'2009-01-19',	NULL,	NULL),
(15,	'S-Ranker',	'https://assets.ppy.sh/medals/web/all-secret-rank-s.png',	'Accuracy is really underrated.',	'osu',	'Hush-Hush',	NULL,	0,	'',	'5G8xFxHh9OM',	'2009-01-19',	NULL,	NULL),
(16,	'Most Improved',	'https://assets.ppy.sh/medals/web/all-secret-improved.png',	'Now THAT is improvement.',	'osu',	'Hush-Hush',	NULL,	0,	'',	'',	'2009-01-19',	NULL,	NULL),
(17,	'Non-stop Dancer',	'https://assets.ppy.sh/medals/web/all-secret-dancer.png',	'Can you still feel your feet after that?',	'osu',	'Hush-Hush',	NULL,	0,	'',	'',	'2009-01-19',	NULL,	NULL),
(18,	'Internet! Pack vol.2',	'https://assets.ppy.sh/medals/web/all-packs-internet-2.png',	'Straight from an albino black sheep. Wait, what?',	'osu',	'Beatmap Packs',	NULL,	2,	'93',	NULL,	'0000-00-00',	NULL,	NULL),
(19,	'Rhythm Game Pack vol.2',	'https://assets.ppy.sh/medals/web/all-packs-rhythm-2.png',	'You just can\'t stop the beat.',	'osu',	'Beatmap Packs',	NULL,	3,	'94',	NULL,	'0000-00-00',	NULL,	NULL),
(20,	'5,000 Plays',	'https://assets.ppy.sh/medals/web/osu-plays-5000.png',	'There\'s a lot more where that came from.',	'osu',	'Dedication',	NULL,	0,	'',	'',	NULL,	'0000-00-00',	0),
(21,	'15,000 Plays',	'https://assets.ppy.sh/medals/web/osu-plays-15000.png',	'Must.. click.. circles..',	'osu',	'Dedication',	NULL,	0,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(22,	'25,000 Plays',	'https://assets.ppy.sh/medals/web/osu-plays-25000.png',	'There\'s no going back.',	'osu',	'Dedication',	NULL,	0,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(23,	'Catch 200,000 fruits',	'https://assets.ppy.sh/medals/web/fruits-hits-200000.png',	'So, I heard you like fruit...',	'fruits',	'Dedication',	NULL,	1,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(24,	'Catch 2,000,000 fruits',	'https://assets.ppy.sh/medals/web/fruits-hits-2000000.png',	'Downright healthy.',	'fruits',	'Dedication',	NULL,	1,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(25,	'Anime Pack vol.3',	'https://assets.ppy.sh/medals/web/all-packs-anime-3.png',	'You did it for the waifus.',	'osu',	'Beatmap Packs',	NULL,	1,	'207',	NULL,	'2010-03-01',	NULL,	NULL),
(26,	'Rhythm Game Pack vol.3',	'https://assets.ppy.sh/medals/web/all-packs-rhythm-3.png',	'Everyone knows the classics play better on osu! anyway.',	'osu',	'Beatmap Packs',	NULL,	3,	'208',	NULL,	'2010-03-01',	NULL,	NULL),
(27,	'Internet! Pack vol.3',	'https://assets.ppy.sh/medals/web/all-packs-internet-3.png',	'You didn\'t stumble upon this one, I\'m guessing.',	'osu',	'Beatmap Packs',	NULL,	2,	'209',	NULL,	'2010-03-01',	NULL,	NULL),
(28,	'50,000 Plays',	'https://assets.ppy.sh/medals/web/osu-plays-50000.png',	'You\'re here forever.',	'osu',	'Dedication',	NULL,	0,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(31,	'30,000 Drum Hits',	'https://assets.ppy.sh/medals/web/taiko-hits-30000.png',	'Did that drum have a face?',	'taiko',	'Dedication',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(32,	'300,000 Drum Hits',	'https://assets.ppy.sh/medals/web/taiko-hits-300000.png',	'The rhythm never stops.',	'taiko',	'Dedication',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(33,	'3,000,000 Drum Hits',	'https://assets.ppy.sh/medals/web/taiko-hits-3000000.png',	'Truly, the Don of dons.',	'taiko',	'Dedication',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(34,	'Anime Pack vol.4',	'https://assets.ppy.sh/medals/web/all-packs-anime-4.png',	'Has your ship not sailed?',	'osu',	'Beatmap Packs',	NULL,	1,	'363',	NULL,	'0000-00-00',	NULL,	NULL),
(35,	'Rhythm Game Pack vol.4',	'https://assets.ppy.sh/medals/web/all-packs-rhythm-4.png',	'A click away? More like, right here.',	'osu',	'Beatmap Packs',	NULL,	3,	'365',	NULL,	'0000-00-00',	NULL,	NULL),
(36,	'Internet! Pack vol.4',	'https://assets.ppy.sh/medals/web/all-packs-internet-4.png',	'Must... have... more... memes...',	'osu',	'Beatmap Packs',	NULL,	2,	'366',	NULL,	'0000-00-00',	NULL,	NULL),
(37,	'Video Game Pack vol.4',	'https://assets.ppy.sh/medals/web/all-packs-gamer-4.png',	'You are Player 1.',	'osu',	'Beatmap Packs',	NULL,	0,	'364',	NULL,	'0000-00-00',	NULL,	NULL),
(38,	'Consolation Prize',	'https://assets.ppy.sh/medals/web/all-secret-consolation_prize.png',	'Well, it could be worse.',	'osu',	'Hush-Hush',	NULL,	0,	'',	'',	'0000-00-00',	NULL,	NULL),
(39,	'Challenge Accepted',	'https://assets.ppy.sh/medals/web/all-secret-challenge_accepted.png',	'Oh, you\'re ON.',	'osu',	'Hush-Hush',	NULL,	0,	'',	'JKb9aeK-Wv4',	'0000-00-00',	NULL,	NULL),
(40,	'Stumbler',	'https://assets.ppy.sh/medals/web/all-secret-stumbler.png',	'No regrets.',	'osu',	'Hush-Hush',	NULL,	0,	'',	'',	'0000-00-00',	NULL,	NULL),
(41,	'Jackpot',	'https://assets.ppy.sh/medals/web/all-secret-jackpot.png',	'Lucky sevens is a mild understatement.',	'NULL',	'Hush-Hush',	NULL,	0,	'',	'',	'2012-08-22',	NULL,	NULL),
(42,	'Quick Draw',	'https://assets.ppy.sh/medals/web/all-secret-quick_draw.png',	'It\'s high noon.',	'NULL',	'Hush-Hush',	NULL,	0,	'',	'',	'2012-08-22',	NULL,	NULL),
(43,	'Obsessed',	'https://assets.ppy.sh/medals/web/all-secret-obsessed.png',	'COMPLETION AT ALL COSTS.',	'NULL',	'Hush-Hush',	NULL,	0,	'',	'',	'2012-08-28',	'0000-00-00',	0),
(44,	'Nonstop',	'https://assets.ppy.sh/medals/web/all-secret-nonstop.png',	'Breaks? What are those?',	'NULL',	'Hush-Hush',	NULL,	0,	'',	'ZPrltmws1Tw',	'2012-08-28',	NULL,	NULL),
(45,	'Jack of All Trades',	'https://assets.ppy.sh/medals/web/all-secret-jack.png',	'Good at everything.',	'NULL',	'Hush-Hush',	NULL,	0,	'',	'',	'2012-08-28',	NULL,	NULL),
(46,	'40,000 Keys',	'https://assets.ppy.sh/medals/web/mania-hits-40000.png',	'Just the start of the rainbow.',	'mania',	'Dedication',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(47,	'400,000 Keys',	'https://assets.ppy.sh/medals/web/mania-hits-400000.png',	'Four hundred thousand and still not even close.',	'mania',	'Dedication',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(48,	'4,000,000 Keys',	'https://assets.ppy.sh/medals/web/mania-hits-4000000.png',	'Is this the end of the rainbow?',	'mania',	'Dedication',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(50,	'I can see the top',	'https://assets.ppy.sh/medals/web/all-skill-highranker-1.png',	'Your dedication has paid off. Welcome to the top 50,000!',	'NULL',	'Skill',	NULL,	1,	'',	'',	NULL,	'0000-00-00',	0),
(51,	'The gradual rise',	'https://assets.ppy.sh/medals/web/all-skill-highranker-2.png',	'There\'s no stopping you, is there? Welcome to the top 10,000!',	'NULL',	'Skill',	NULL,	1,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(52,	'Scaling up',	'https://assets.ppy.sh/medals/web/all-skill-highranker-3.png',	'Welcome to the top 5,000. Never give up!',	'NULL',	'Skill',	NULL,	1,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(53,	'Approaching the summit',	'https://assets.ppy.sh/medals/web/all-skill-highranker-4.png',	'Pro tier. Welcome to the top 1,000!',	'NULL',	'Skill',	NULL,	1,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(54,	'Twin Perspectives',	'https://assets.ppy.sh/medals/web/mania-secret-meganekko.png',	'You met Mani and Mari, our twin osu!mania mascots.',	'mania',	'Hush-Hush',	NULL,	0,	'0',	NULL,	'0000-00-00',	NULL,	NULL),
(55,	'Rising Star',	'https://assets.ppy.sh/medals/web/osu-skill-pass-1.png',	'Can\'t go forward without the first steps.',	'osu',	'Skill',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(56,	'Constellation Prize',	'https://assets.ppy.sh/medals/web/osu-skill-pass-2.png',	'Definitely not a consolation prize. Now things start getting hard!',	'osu',	'Skill',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(57,	'Building Confidence',	'https://assets.ppy.sh/medals/web/osu-skill-pass-3.png',	'Oh, you\'ve SO got this.',	'osu',	'Skill',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(58,	'Insanity Approaches',	'https://assets.ppy.sh/medals/web/osu-skill-pass-4.png',	'You\'re not twitching, you\'re just ready.',	'osu',	'Skill',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(59,	'These Clarion Skies',	'https://assets.ppy.sh/medals/web/osu-skill-pass-5.png',	'Everything seems so clear now.',	'osu',	'Skill',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(60,	'Above and Beyond',	'https://assets.ppy.sh/medals/web/osu-skill-pass-6.png',	'A cut above the rest.',	'osu',	'Skill',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(61,	'Supremacy',	'https://assets.ppy.sh/medals/web/osu-skill-pass-7.png',	'All marvel before your prowess.',	'osu',	'Skill',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(62,	'Absolution',	'https://assets.ppy.sh/medals/web/osu-skill-pass-8.png',	'My god, you\'re full of stars!',	'osu',	'Skill',	NULL,	2,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(63,	'Totality',	'https://assets.ppy.sh/medals/web/osu-skill-fc-1.png',	'All the notes. Every single one.',	'osu',	'Skill',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(64,	'Business As Usual',	'https://assets.ppy.sh/medals/web/osu-skill-fc-2.png',	'Two to go, please.',	'osu',	'Skill',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(65,	'Building Steam',	'https://assets.ppy.sh/medals/web/osu-skill-fc-3.png',	'Hey, this isn\'t so bad.',	'osu',	'Skill',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(66,	'Moving Forward',	'https://assets.ppy.sh/medals/web/osu-skill-fc-4.png',	'Bet you feel good about that.',	'osu',	'Skill',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(67,	'Paradigm Shift',	'https://assets.ppy.sh/medals/web/osu-skill-fc-5.png',	'Surprisingly difficult.',	'osu',	'Skill',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(68,	'Anguish Quelled',	'https://assets.ppy.sh/medals/web/osu-skill-fc-6.png',	'Don\'t choke.',	'osu',	'Skill',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(69,	'Never Give Up',	'https://assets.ppy.sh/medals/web/osu-skill-fc-7.png',	'Excellence is its own reward.',	'osu',	'Skill',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(70,	'Aberration',	'https://assets.ppy.sh/medals/web/osu-skill-fc-8.png',	'They said it couldn\'t be done. They were wrong.',	'osu',	'Skill',	NULL,	3,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(71,	'My First Don',	'https://assets.ppy.sh/medals/web/taiko-skill-pass-1.png',	'Marching to the beat of your own drum. Literally.',	'taiko',	'Skill',	NULL,	4,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(72,	'Katsu Katsu Katsu',	'https://assets.ppy.sh/medals/web/taiko-skill-pass-2.png',	'Hora! Ikuzo!',	'taiko',	'Skill',	NULL,	4,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(73,	'Not Even Trying',	'https://assets.ppy.sh/medals/web/taiko-skill-pass-3.png',	'Muzukashii? Not even.',	'taiko',	'Skill',	NULL,	4,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(74,	'Face Your Demons',	'https://assets.ppy.sh/medals/web/taiko-skill-pass-4.png',	'The first trials are now behind you, but are you a match for the Oni?',	'taiko',	'Skill',	NULL,	4,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(75,	'The Demon Within',	'https://assets.ppy.sh/medals/web/taiko-skill-pass-5.png',	'No rest for the wicked.',	'taiko',	'Skill',	NULL,	4,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(76,	'Drumbreaker',	'https://assets.ppy.sh/medals/web/taiko-skill-pass-6.png',	'Too strong.',	'taiko',	'Skill',	NULL,	4,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(77,	'The Godfather',	'https://assets.ppy.sh/medals/web/taiko-skill-pass-7.png',	'You are the Don of Dons.',	'taiko',	'Skill',	NULL,	4,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(78,	'Rhythm Incarnate',	'https://assets.ppy.sh/medals/web/taiko-skill-pass-8.png',	'Feel the beat. Become the beat.',	'taiko',	'Skill',	NULL,	4,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(79,	'A Slice Of Life',	'https://assets.ppy.sh/medals/web/fruits-skill-pass-1.png',	'Hey, this fruit catching business isn\'t bad.',	'fruits',	'Skill',	NULL,	6,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(80,	'Dashing Ever Forward',	'https://assets.ppy.sh/medals/web/fruits-skill-pass-2.png',	'Fast is how you do it.',	'fruits',	'Skill',	NULL,	6,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(81,	'Zesty Disposition',	'https://assets.ppy.sh/medals/web/fruits-skill-pass-3.png',	'No scurvy for you, not with that much fruit.',	'fruits',	'Skill',	NULL,	6,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(82,	'Hyperdash ON!',	'https://assets.ppy.sh/medals/web/fruits-skill-pass-4.png',	'Time and distance is no obstacle to you.',	'fruits',	'Skill',	NULL,	6,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(83,	'It\'s Raining Fruit',	'https://assets.ppy.sh/medals/web/fruits-skill-pass-5.png',	'And you can catch them all.',	'fruits',	'Skill',	NULL,	6,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(84,	'Fruit Ninja',	'https://assets.ppy.sh/medals/web/fruits-skill-pass-6.png',	'Legendary techniques.',	'fruits',	'Skill',	NULL,	6,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(85,	'Dreamcatcher',	'https://assets.ppy.sh/medals/web/fruits-skill-pass-7.png',	'No fruit, only dreams now.',	'fruits',	'Skill',	NULL,	6,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(86,	'Lord of the Catch',	'https://assets.ppy.sh/medals/web/fruits-skill-pass-8.png',	'Your kingdom kneels before you.',	'fruits',	'Skill',	NULL,	6,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(87,	'First Steps',	'https://assets.ppy.sh/medals/web/mania-skill-pass-1.png',	'It isn\'t 9-to-5, but 1-to-9. Keys, that is.',	'mania',	'Skill',	NULL,	8,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(88,	'No Normal Player',	'https://assets.ppy.sh/medals/web/mania-skill-pass-2.png',	'Not anymore, at least.',	'mania',	'Skill',	NULL,	8,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(89,	'Impulse Drive',	'https://assets.ppy.sh/medals/web/mania-skill-pass-3.png',	'Not quite hyperspeed, but getting close.',	'mania',	'Skill',	NULL,	8,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(90,	'Hyperspeed',	'https://assets.ppy.sh/medals/web/mania-skill-pass-4.png',	'Woah.',	'mania',	'Skill',	NULL,	8,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(91,	'Ever Onwards',	'https://assets.ppy.sh/medals/web/mania-skill-pass-5.png',	'Another challenge is just around the corner.',	'mania',	'Skill',	NULL,	8,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(92,	'Another Surpassed',	'https://assets.ppy.sh/medals/web/mania-skill-pass-6.png',	'Is there no limit to your skills?',	'mania',	'Skill',	NULL,	8,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(93,	'Extra Credit',	'https://assets.ppy.sh/medals/web/mania-skill-pass-7.png',	'See me after class.',	'mania',	'Skill',	NULL,	8,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(94,	'Maniac',	'https://assets.ppy.sh/medals/web/mania-skill-pass-8.png',	'There\'s just no stopping you.',	'mania',	'Skill',	NULL,	8,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(95,	'Keeping Time',	'https://assets.ppy.sh/medals/web/taiko-skill-fc-1.png',	'Don, then katsu. Don, then katsu..',	'taiko',	'Skill',	NULL,	5,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(96,	'To Your Own Beat',	'https://assets.ppy.sh/medals/web/taiko-skill-fc-2.png',	'Straight and steady.',	'taiko',	'Skill',	NULL,	5,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(97,	'Big Drums',	'https://assets.ppy.sh/medals/web/taiko-skill-fc-3.png',	'Bigger scores to match.',	'taiko',	'Skill',	NULL,	5,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(98,	'Adversity Overcome',	'https://assets.ppy.sh/medals/web/taiko-skill-fc-4.png',	'Difficult? Not for you.',	'taiko',	'Skill',	NULL,	5,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(99,	'Demonslayer',	'https://assets.ppy.sh/medals/web/taiko-skill-fc-5.png',	'An Oni felled forevermore.',	'taiko',	'Skill',	NULL,	5,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(100,	'Rhythm\'s Call',	'https://assets.ppy.sh/medals/web/taiko-skill-fc-6.png',	'Heralding true skill.',	'taiko',	'Skill',	NULL,	5,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(101,	'Time Everlasting',	'https://assets.ppy.sh/medals/web/taiko-skill-fc-7.png',	'Not a single beat escapes you.',	'taiko',	'Skill',	NULL,	5,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(102,	'The Drummer\'s Throne',	'https://assets.ppy.sh/medals/web/taiko-skill-fc-8.png',	'Percussive brilliance befitting royalty alone.',	'taiko',	'Skill',	NULL,	5,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(103,	'Sweet And Sour',	'https://assets.ppy.sh/medals/web/fruits-skill-fc-1.png',	'Apples and oranges, literally.',	'fruits',	'Skill',	NULL,	7,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(104,	'Reaching The Core',	'https://assets.ppy.sh/medals/web/fruits-skill-fc-2.png',	'The seeds of future success.',	'fruits',	'Skill',	NULL,	7,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(105,	'Clean Platter',	'https://assets.ppy.sh/medals/web/fruits-skill-fc-3.png',	'Clean only of failure. It is completely full, otherwise.',	'fruits',	'Skill',	NULL,	7,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(106,	'Between The Rain',	'https://assets.ppy.sh/medals/web/fruits-skill-fc-4.png',	'No umbrella needed.',	'fruits',	'Skill',	NULL,	7,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(107,	'Addicted',	'https://assets.ppy.sh/medals/web/fruits-skill-fc-5.png',	'That was an overdose?',	'fruits',	'Skill',	NULL,	7,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(108,	'Quickening',	'https://assets.ppy.sh/medals/web/fruits-skill-fc-6.png',	'A dash above normal limits.',	'fruits',	'Skill',	NULL,	7,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(109,	'Supersonic',	'https://assets.ppy.sh/medals/web/fruits-skill-fc-7.png',	'Faster than is reasonably necessary.',	'fruits',	'Skill',	NULL,	7,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(110,	'Dashing Scarlet',	'https://assets.ppy.sh/medals/web/fruits-skill-fc-8.png',	'Speed beyond mortal reckoning.',	'fruits',	'Skill',	NULL,	7,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(111,	'Keystruck',	'https://assets.ppy.sh/medals/web/mania-skill-fc-1.png',	'The beginning of a new story.',	'mania',	'Skill',	NULL,	9,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(112,	'Keying In',	'https://assets.ppy.sh/medals/web/mania-skill-fc-2.png',	'Finding your groove.',	'mania',	'Skill',	NULL,	9,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(113,	'Hyperflow',	'https://assets.ppy.sh/medals/web/mania-skill-fc-3.png',	'You can *feel* the rhythm.',	'mania',	'Skill',	NULL,	9,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(114,	'Breakthrough',	'https://assets.ppy.sh/medals/web/mania-skill-fc-4.png',	'Many skills mastered, rolled into one.',	'mania',	'Skill',	NULL,	9,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(115,	'Everything Extra',	'https://assets.ppy.sh/medals/web/mania-skill-fc-5.png',	'Giving your all is giving everything you have.',	'mania',	'Skill',	NULL,	9,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(116,	'Level Breaker',	'https://assets.ppy.sh/medals/web/mania-skill-fc-6.png',	'Finesse beyond reason.',	'mania',	'Skill',	NULL,	9,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(117,	'Step Up',	'https://assets.ppy.sh/medals/web/mania-skill-fc-7.png',	'A precipice rarely seen.',	'mania',	'Skill',	NULL,	9,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(118,	'Behind The Veil',	'https://assets.ppy.sh/medals/web/mania-skill-fc-8.png',	'Supernatural!',	'mania',	'Skill',	NULL,	9,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(119,	'Finality',	'https://assets.ppy.sh/medals/web/all-intro-suddendeath.png',	'High stakes, no regrets.',	'NULL',	'Mod Introduction',	'completing a map with the <b>Sudden Death</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(120,	'Perfectionist',	'https://assets.ppy.sh/medals/web/all-intro-perfect.png',	'Accept nothing but the best.',	'NULL',	'Mod Introduction',	'completing a map with the <b>Perfect</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(121,	'Rock Around The Clock',	'https://assets.ppy.sh/medals/web/all-intro-hardrock.png',	'You can\'t stop the rock.',	'NULL',	'Mod Introduction',	'completing a map with the <b>Hard Rock</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(122,	'Time And A Half',	'https://assets.ppy.sh/medals/web/all-intro-doubletime.png',	'Having a right ol\' time. One and a half of them, almost.',	'NULL',	'Mod Introduction',	'completing a map with the <b>DoubleTime</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(123,	'Sweet Rave Party',	'https://assets.ppy.sh/medals/web/all-intro-nightcore.png',	'Founded in the fine tradition of changing things that were just fine as they were.',	'NULL',	'Mod Introduction',	'completing a map with the <b>Nightcore</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(124,	'Blindsight',	'https://assets.ppy.sh/medals/web/all-intro-hidden.png',	'I can see just perfectly.',	'NULL',	'Mod Introduction',	'completing a map with the <b>Hidden</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(125,	'Are You Afraid Of The Dark?',	'https://assets.ppy.sh/medals/web/all-intro-flashlight.png',	'Harder than it looks, probably because it\'s hard to look.',	'NULL',	'Mod Introduction',	'completing a map with the <b>Flashlight</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(126,	'Dial It Right Back',	'https://assets.ppy.sh/medals/web/all-intro-easy.png',	'Sometimes you just want to take it easy.',	'NULL',	'Mod Introduction',	'completing a map with the <b>Easy</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(127,	'Risk Averse',	'https://assets.ppy.sh/medals/web/all-intro-nofail.png',	'Safety nets are fun!',	'NULL',	'Mod Introduction',	'completing a map with the <b>No Fail</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(128,	'Slowboat',	'https://assets.ppy.sh/medals/web/all-intro-halftime.png',	'You got there. Eventually.',	'NULL',	'Mod Introduction',	'completing a map with the <b>HalfTime</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(131,	'Burned Out',	'https://assets.ppy.sh/medals/web/all-intro-spunout.png',	'One cannot always spin to win.',	'NULL',	'Mod Introduction',	'completing a map with the <b>Spun Out</b> mod enabled!',	10,	NULL,	NULL,	'0000-00-00',	NULL,	NULL),
(132,	'Perseverance',	'https://assets.ppy.sh/medals/web/all-secret-perseverance.png',	'Endure.',	'NULL',	'Hush-Hush',	'<i>Endurance is the key.</i>',	0,	'',	'ZPrltmws1Tw',	'2016-08-17',	NULL,	NULL),
(133,	'Feel The Burn',	'https://assets.ppy.sh/medals/web/all-secret-ftb.png',	'It isn\'t all about how fast you manage.',	'NULL',	'Hush-Hush',	'<i>Endurance <strong>and</strong> precision is essential.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(134,	'Time Dilation',	'https://assets.ppy.sh/medals/web/all-secret-tidi.png',	'Longer is shorter when all is said and done.',	'NULL',	'Hush-Hush',	'<i>Time often flies when one is having fun, though rarely does it last forever.</i>',	0,	'',	'ZPrltmws1Tw',	NULL,	'0000-00-00',	0),
(135,	'Just One Second',	'https://assets.ppy.sh/medals/web/all-secret-onesecond.png',	'And suddenly.. gone.',	'NULL',	'Hush-Hush',	'<i>Blink and you\'ll miss it.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(136,	'Afterimage',	'https://assets.ppy.sh/medals/web/osu-secret-afterimage.png',	'But a glimpse of its true self.',	'osu',	'Hush-Hush',	'<i>Always coming behind the original image.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(137,	'To The Core',	'https://assets.ppy.sh/medals/web/all-secret-tothecore.png',	'In for a penny, in for a pound. Pounding bass, that is.',	'NULL',	'Hush-Hush',	'<i>Double negatives sometimes <strong>do</strong> make a positive.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(138,	'Prepared',	'https://assets.ppy.sh/medals/web/all-secret-prepared.png',	'Do it for real next time.',	'NULL',	'Hush-Hush',	'<i>Preparation is key for all successful endeavours.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(139,	'Eclipse',	'https://assets.ppy.sh/medals/web/osu-secret-eclipse.png',	'Something new born from absence.',	'osu',	'Hush-Hush',	'<i>Do it just because you can, even if the stakes are high.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(140,	'Reckless Abandon',	'https://assets.ppy.sh/medals/web/all-secret-reckless.png',	'Throw it all to the wind.',	'NULL',	'Hush-Hush',	'<i>Do it just because you can, even if the stakes are high.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(141,	'Tunnel Vision',	'https://assets.ppy.sh/medals/web/osu-secret-tunnelvision.png',	'But it was right there..',	'osu',	'Hush-Hush',	'<i>Afraid of the dark?</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(142,	'Behold No Deception',	'https://assets.ppy.sh/medals/web/osu-secret-deception.png',	'That wasn\'t easy at all!',	'osu',	'Hush-Hush',	'<i>Sometimes the hard way is in fact, easier.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(143,	'Up For The Challenge',	'https://assets.ppy.sh/medals/web/all-secret-challenge.png',	'Turn it up to eleven.',	'NULL',	'Hush-Hush',	'<i>Everything\'s better at eleven.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(144,	'Lights Out',	'https://assets.ppy.sh/medals/web/all-secret-lightsout.png',	'The party\'s just getting started.',	'NULL',	'Hush-Hush',	'<i>Set the mood.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(145,	'Unstoppable',	'https://assets.ppy.sh/medals/web/osu-secret-superhardhddt.png',	'Holy shit.',	'osu',	'Hush-Hush',	'<i>Reaching the limits of mortal skill.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(146,	'Is This Real Life?',	'https://assets.ppy.sh/medals/web/osu-secret-supersuperhardhddt.png',	'You did NOT just pull that off.',	'osu',	'Hush-Hush',	'<i>The absolute height of perfection.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(147,	'Camera Shy',	'https://assets.ppy.sh/medals/web/all-secret-uguushy.png',	'Stop being cute.',	'NULL',	'Hush-Hush',	'<i>Uguu.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(148,	'The Sum Of All Fears',	'https://assets.ppy.sh/medals/web/all-secret-nuked.png',	'Unfortunate.',	'NULL',	'Hush-Hush',	'<i>The end comes when you least expect it.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(149,	'Dekasight',	'https://assets.ppy.sh/medals/web/osu-secret-deka.png',	'So big, yet so hard to see.',	'osu',	'Hush-Hush',	'<i>Size isn\'t everything.</i>',	0,	'',	'',	'2016-08-17',	NULL,	NULL),
(150,	'Hour Before The Dawn',	'https://assets.ppy.sh/medals/web/all-secret-hourbeforethedawn.png',	'Eleven skies of everlasting sunrise.',	'NULL',	'Hush-Hush',	'<i>The night where we belong.</i>',	0,	'',	'LLFs8AnU77k',	'0000-00-00',	NULL,	NULL),
(151,	'Slow And Steady',	'https://assets.ppy.sh/medals/web/all-secret-slowandsteady.png',	'Win the race, or start again.',	'NULL',	'Hush-Hush',	'<i>Take your time.</i>',	0,	'',	'',	'2016-10-29',	NULL,	NULL),
(152,	'No Time To Spare',	'https://assets.ppy.sh/medals/web/all-secret-ntts.png',	'Places to be, things to do.',	'NULL',	'Hush-Hush',	'<i>Think fast, click fast, be fast.</i>',	0,	'',	'',	'2016-10-29',	NULL,	NULL),
(153,	'Sognare',	'https://assets.ppy.sh/medals/web/all-secret-sognare.png',	'A dream in stop-motion, soon forever gone.',	'NULL',	'Hush-Hush',	'<i>\"I saw three spires all around, and a world in halted time..\"</i>',	0,	'',	'',	'2016-10-29',	NULL,	NULL),
(154,	'Realtor Extraordinaire',	'https://assets.ppy.sh/medals/web/all-secret-realtor.png',	'An acre-wide stride.',	'NULL',	'Hush-Hush',	'<i>The wrong kind of house.</i>',	0,	'0',	'',	'2016-10-29',	NULL,	NULL),
(155,	'Realität',	'https://assets.ppy.sh/medals/web/all-secret-realitat.png',	'A moonlight butterfly, and beacons of three.',	'NULL',	'Hush-Hush',	'<i>Dream of greater heights.</i>',	0,	'0',	'',	'2016-10-29',	NULL,	NULL),
(156,	'Our Mechanical Benefactors',	'https://assets.ppy.sh/medals/web/all-secret-ourbenefactors.png',	'Human, please explain directive \"GREED\".',	'NULL',	'Hush-Hush',	'<i>Close, yet so far.</i>',	0,	'0',	'6PgaKfiqVX4',	'2016-10-29',	NULL,	NULL),
(157,	'Meticulous',	'https://assets.ppy.sh/medals/web/osu-secret-meticulous.png',	'The circle goes here, and then here, and then here..',	'osu',	'Hush-Hush',	'<i>This is the way it\'s SUPPOSED to be.</i>',	0,	'',	'',	'2016-10-29',	NULL,	NULL),
(158,	'Infinitesimal',	'https://assets.ppy.sh/medals/web/osu-secret-infinitesimal.png',	'Big word for something so very, very small.',	'osu',	'Hush-Hush',	'<i>Tiny in scope, big in meaning.</i>',	0,	'',	'',	'2016-10-29',	NULL,	NULL),
(159,	'Equilibrium',	'https://assets.ppy.sh/medals/web/osu-secret-equilibrium.png',	'Balance in all things.',	'osu',	'Hush-Hush',	'<i>Seek the middle ground.</i>',	0,	'',	'tSdp2Fp73nc',	'2016-10-29',	NULL,	NULL),
(160,	'Impeccable',	'https://assets.ppy.sh/medals/web/all-secret-impeccable.png',	'Speed matters not to the exemplary.',	'NULL',	'Hush-Hush',	'<i>Simply superb.</i>',	0,	'',	'',	'2016-10-29',	NULL,	NULL),
(161,	'Elite',	'https://assets.ppy.sh/medals/web/all-secret-elite.png',	'Dangerous beat agents.',	'NULL',	'Hush-Hush',	'<i>A challenge for status, prestige and fame is not always the smartest thing to do in the middle of something.</i>',	0,	'',	'cilGGKZl7_g',	'0000-00-00',	NULL,	NULL),
(162,	'January/February 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-01.png',	'Two for the price of one.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the January/Feburary Spotlights for 2017.</i>',	0,	'1186,1187,1188,1189',	'',	'2017-09-22',	NULL,	NULL),
(163,	'March 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-03.png',	'March ever onwards.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the March Spotlights for 2017.</i>',	0,	'1201,1202,1203,1204',	'',	'2017-09-22',	NULL,	NULL),
(164,	'April 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-04.png',	'Pitch.. WHAT?',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the April Spotlights for 2017.</i>',	0,	'1219,1220,1221,1222',	'',	'2017-09-22',	NULL,	NULL),
(165,	'May 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-05.png',	'May your accuracy forever be swift and true.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the May Spotlights for 2017.</i>',	0,	'1228,1229,1230,1232',	'',	'2017-09-22',	NULL,	NULL),
(166,	'June 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-06.png',	'Innocence destroyed.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the June Spotlights for 2017.</i>',	0,	'1244,1245,1246,1247',	'',	'2017-09-22',	NULL,	NULL),
(167,	'July 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-07.png',	'Where it all begins.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the July Spotlights for 2017.</i>',	0,	'1253,1254,1255,1256',	'',	'2017-09-22',	NULL,	NULL),
(168,	'50/50',	'https://assets.ppy.sh/medals/web/all-secret-5050.png',	'Half full or half empty, that\'s a whole lot of fifty.',	'NULL',	'Hush-Hush',	'<i>Strong opinions about this, one way or another.</i>',	0,	'',	'',	'0000-00-00',	NULL,	NULL),
(169,	'August 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-08.png',	'Ah, yes. Something just like this.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the August Spotlights for 2017.</i>',	0,	'1264,1265,1267,1268',	'',	'2017-09-22',	NULL,	NULL),
(170,	'Thrill of the Chase',	'https://assets.ppy.sh/medals/web/all-secret-thrillofthechase.png',	'My heart\'s beating, my hands are shaking, and I\'m STILL clicking.',	'osu',	'Hush-Hush',	'<i>Is there anything better than the hunt? Such a classic pursuit.</i>',	0,	'0',	'',	'2017-10-31',	NULL,	NULL),
(171,	'The Girl in the Forest',	'https://assets.ppy.sh/medals/web/all-secret-girlintheforest.png',	'Not even the Elite Four could stop you now.',	'NULL',	'Hush-Hush',	'<i>Think back to where it all began, in shades of red and blue..</i>',	0,	'0',	'MOeg0gFgY7E',	'2017-10-31',	'0000-00-00',	587737),
(172,	'You Can\'t Hide',	'https://assets.ppy.sh/medals/web/all-secret-youcanthide.png',	'I will find you, and I will click you. All of you.',	'NULL',	'Hush-Hush',	'<i>Don\'t even try to hide.</i>',	0,	'',	'',	'2017-10-31',	NULL,	NULL),
(173,	'True Torment',	'https://assets.ppy.sh/medals/web/osu-secret-truetorment.png',	'It lasts forever.',	'osu',	'Hush-Hush',	'<i>Note the ghosts.</i>',	0,	'',	'uFMJUTjBWwY',	'2017-10-31',	NULL,	NULL),
(174,	'The Firmament Moves',	'https://assets.ppy.sh/medals/web/all-secret-celestialmovement.png',	'Number fourteen? More like number one.',	'NULL',	'Hush-Hush',	'<i>Three times does it move, but most only know one. What you seek is something different yet again, warped three times over.</i>',	0,	'',	'',	'2017-10-31',	NULL,	NULL),
(175,	'Too Fast, Too Furious',	'https://assets.ppy.sh/medals/web/all-secret-toofasttoofurious.png',	'A march if you have eight feet, maybe!',	'NULL',	'Hush-Hush',	'<i>Something you don\'t do when you\'re afraid.</i>',	0,	'',	'',	'2017-10-31',	NULL,	NULL),
(176,	'Feelin\' It',	'https://assets.ppy.sh/medals/web/all-secret-feelinit.png',	'Got with the times.',	'NULL',	'Hush-Hush',	'<i>Beats multiplied. A lot of them.</i>',	0,	'',	'',	'2017-10-31',	NULL,	NULL),
(177,	'Overconfident',	'https://assets.ppy.sh/medals/web/osu-secret-overconfident.png',	'Try again later, maybe?',	'NULL',	'Hush-Hush',	'<i>The \'s\' in progress stands for hubris.</i>',	0,	'',	'',	'2017-10-31',	NULL,	NULL),
(178,	'Spooked',	'https://assets.ppy.sh/medals/web/osu-secret-spooked.png',	'Something moved. It wasn\'t your cursor!',	'NULL',	'Hush-Hush',	'<i>Don\'t look behind you.</i>',	0,	'',	'',	'2017-10-31',	NULL,	NULL),
(179,	'MOtOLOiD',	'https://assets.ppy.sh/medals/web/all-packs-motoloid.png',	'Legends made manifest, by the mappers of the Guild.',	'NULL',	'Beatmap Packs',	'<i>Play all of the MOtOLOiD Mapper\'s Guild pack.</i>',	4,	'1284',	NULL,	'0000-00-00',	NULL,	NULL),
(180,	'September 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-09.png',	'New beginnings, time travelers, and airborne robots. Oh my!',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the September Spotlights for 2017.</i>',	0,	'1280,1281,1282,1283',	'',	'0000-00-00',	NULL,	NULL),
(181,	'October 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-10.png',	'First, one must distract a punk rock girl. Then, they must put on the radio.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the October Spotlights for 2017.</i>',	0,	'1292,1293,1294,1295',	'',	'0000-00-00',	NULL,	NULL),
(182,	'November 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-11.png',	'The end of an era, and the start of something new!',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the November Spotlights for 2017.</i>',	0,	'1302,1303,1304,1305',	'',	'0000-00-00',	NULL,	NULL),
(183,	'December 2017 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2017-12.png',	'Impulse to end the year!',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the December Spotlights for 2017.</i>',	0,	'1331,1332,1333,1334',	'',	'0000-00-00',	NULL,	NULL),
(184,	'January 2018 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2018-01.png',	'Reality distorts.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the January Spotlights for 2018.</i>',	0,	'1354,1355,1356,1357',	'',	'0000-00-00',	NULL,	NULL),
(185,	'Mappers\' Guild Pack I',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-01.png',	'The first among many to come.',	'NULL',	'Beatmap Packs',	'<i>Complete all of the beatmaps in the Mappers\' Guild I pack..</i>',	5,	'1365',	NULL,	'0000-00-00',	NULL,	NULL),
(186,	'February 2018 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2018-02.png',	'Let\'s jump for dreams of future candy!',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the February Spotlights for 2018.</i>',	0,	'1379,1380,1381,1382',	'',	'0000-00-00',	NULL,	NULL),
(187,	'March 2018 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2018-03.png',	'Transport me to Nirvana on the backs of angels!',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the March Spotlights for 2018.</i>',	0,	'1405,1407,1408,0',	'',	'0000-00-00',	NULL,	NULL),
(188,	'April 2018 Spotlight',	'https://assets.ppy.sh/medals/web/spotlight-2018-04.png',	'A lesson from a DJ: drop kick captivatingly.',	'NULL',	'Beatmap Spotlights',	'<i>Complete any gamemode version of the April Spotlights for 2018.</i>',	0,	'1430,1431,1432,1433',	'',	'0000-00-00',	NULL,	NULL),
(189,	'Cranky',	'https://assets.ppy.sh/medals/web/all-packs-cranky.png',	'The grandfather of rhythm gaming music, brought to life by the mappers of the Guild and guests alike.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Cranky pack.</i>',	4,	'1437',	NULL,	'0000-00-00',	NULL,	NULL),
(190,	'Mappers\' Guild Pack II',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-02.png',	'A variable dream of the future.',	'NULL',	'Beatmap Packs',	'<i>Complete all of the beatmaps in the Mappers\' Guild II pack..</i>',	5,	'1450',	NULL,	'0000-00-00',	NULL,	NULL),
(191,	'High Tea Music',	'https://assets.ppy.sh/medals/web/all-packs-highteamusic.png',	'Silently journeying to invade the skies.',	'NULL',	'Beatmap Packs',	'<i>Complete all of the beatmaps in the Mappers\' Guild III/High Tea Music pack..</i>',	4,	'1480',	NULL,	'0000-00-00',	NULL,	NULL),
(192,	'Skylord',	'https://assets.ppy.sh/medals/web/osu-secret-skylord.png',	'Never miss a wingbeat.',	'osu',	'Hush-Hush',	'<i>Flight among the sky requires nothing but perfection.</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(193,	'B-Rave',	'https://assets.ppy.sh/medals/web/all-secret-brave.png',	'It takes courage to stand before the master.',	'NULL',	'Hush-Hush',	'<i>Ill-tempered bosses make for excellent final battles.</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(194,	'Any%',	'https://assets.ppy.sh/medals/web/all-secret-anypercent.png',	'A speedrunner\'s best friend.',	'NULL',	'Hush-Hush',	'<i>Sometimes you have to break the rules to work with speed.</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(195,	'Mirage',	'https://assets.ppy.sh/medals/web/all-secret-mirage.png',	'The horizon goes on forever, and ever, and ever...',	'NULL',	'Hush-Hush',	'<i>The light is far harder than it seems.</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(196,	'Under The Stars',	'https://assets.ppy.sh/medals/web/all-secret-underthestars.png',	'Onwards, to where the darkness can never stop us.',	'NULL',	'Hush-Hush',	'<i>Walk beneath the stars on a journey to elsewhere.</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(197,	'Senseless',	'https://assets.ppy.sh/medals/web/all-secret-senseless.png',	'I hear nothing. I see nothing.',	'NULL',	'Hush-Hush',	'<i>A song that is, and something that is not.</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(199,	'Aeon',	'https://assets.ppy.sh/medals/web/all-secret-aeon.png',	'In the mire of thawing time, memory shall be your guide.',	'NULL',	'Hush-Hush',	'<i>When time runs slow and sight fails you, how will you succeed?</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(200,	'Upon The Wind',	'https://assets.ppy.sh/medals/web/all-secret-uponthewind.png',	'And in that gale, no eye could hope to follow.',	'NULL',	'Hush-Hush',	'<i>In the heart of a storm of flowers, a world unseen.</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(201,	'Vantage',	'https://assets.ppy.sh/medals/web/osu-secret-vantage.png',	'There we stood, where the spires pierced the sky, and dreamed of the future to come.',	'osu',	'Hush-Hush',	'<i>Where ground was broken to critical acclaim.</i>',	0,	'',	'RQPBfA8WIjw',	'2018-10-23',	NULL,	NULL),
(202,	'Quick Maths',	'https://assets.ppy.sh/medals/web/all-secret-quickmaffs.png',	'Beats per minute over... this isn\'t quick at all!',	'NULL',	'Hush-Hush',	'<i>Where x equals beats per minute, and a variable unknown...</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(204,	'Efflorescence',	'https://assets.ppy.sh/medals/web/all-secret-efflorescence.png',	'A lament for the past, and a glimpse into tomorrow.',	'NULL',	'Hush-Hush',	'<i>The horizon thunders with a portentous bloom.</i>',	0,	'',	'',	'2018-10-23',	NULL,	NULL),
(205,	'Summer 2018 Beatmap Spotlights',	'https://assets.ppy.sh/medals/web/spotlight-2018-summer.png',	'The hottest beatmaps from Summer 2018!',	'NULL',	'Seasonal Spotlights',	'<i>Play all of the maps from one mode of the Summer 2018 Beatmap Spotlights.</i>',	0,	'1508,1509,1510,1511',	'',	'0000-00-00',	NULL,	NULL),
(206,	'Culprate',	'https://assets.ppy.sh/medals/web/all-packs-culprate.png',	'A dream of whispers, light, and things to come.',	'NULL',	'Beatmap Packs',	'<i>Complete all of the beatmaps in the Mappers\' Guild IV/Culprate pack.</i>',	4,	'1535',	NULL,	'0000-00-00',	NULL,	NULL),
(207,	'Fall 2018 Beatmap Spotlights',	'https://assets.ppy.sh/medals/web/spotlight-2018-autumn.png',	'The most fabulous beatmaps from Fall 2018!',	'NULL',	'Seasonal Spotlights',	'<i>Play all of the maps from one mode of the Fall 2018 Beatmap Spotlights.</i>',	0,	'1548,1549,1550,1551',	'',	'0000-00-00',	NULL,	NULL),
(208,	'HyuN',	'https://assets.ppy.sh/medals/web/all-packs-hyun.png',	'Digital punk goes into overdrive!',	'NULL',	'Beatmap Packs',	'<i>Play all of the HyuN pack.</i>',	4,	'1581',	NULL,	'0000-00-00',	NULL,	NULL),
(209,	'Winter 2019 Beatmap Spotlights',	'https://assets.ppy.sh/medals/web/spotlight-2019-winter.png',	'The chillest beatmaps from Winter 2019!',	'NULL',	'Seasonal Spotlights',	'<i>Play all of the maps from one mode of the Winter 2019 Beatmap Spotlights.</i>',	1,	'1623,1624,1625,1626',	'',	'0000-00-00',	NULL,	NULL),
(210,	'Spring 2019 Beatmap Spotlights',	'https://assets.ppy.sh/medals/web/spotlight-2019-spring.png',	'A bloomin\' good time from Spring 2019!',	'NULL',	'Seasonal Spotlights',	'<i>Play all of the maps from one mode of the Spring 2019 Beatmap Spotlights.</i>',	1,	'1670,1671,1672,1673',	'',	'0000-00-00',	NULL,	NULL),
(213,	'Imperial Circus Dead Decadence',	'https://assets.ppy.sh/medals/web/all-packs-icdd.png',	'A little kite once sang me a song...',	'NULL',	'Beatmap Packs',	'<i>Play all of the Imperial Circus Dead Decadence pack.</i>',	4,	'1688',	NULL,	'0000-00-00',	NULL,	NULL),
(214,	'tieff',	'https://assets.ppy.sh/medals/web/all-packs-tieff.png',	'Flow.',	'NULL',	'Beatmap Packs',	'<i>Play all of the tieff pack.</i>',	4,	'1649',	NULL,	'0000-00-00',	NULL,	NULL),
(215,	'Summer 2019 Beatmap Spotlights',	'https://assets.ppy.sh/medals/web/spotlight-2019-summer.png',	'Blaze a trail through the best of Summer 2019!',	'NULL',	'Seasonal Spotlights',	'<i>Play all of the maps from one mode of the Summer 2019 Beatmap Spotlights.</i>',	1,	'1722,1723,1724,1725',	'',	'0000-00-00',	NULL,	NULL),
(216,	'Inundate',	'https://assets.ppy.sh/medals/web/all-secret-inundate.png',	'Swept away.',	'NULL',	'Hush-Hush',	'<i>Across and beyond the tides.</i>',	0,	'',	'',	'2019-10-30',	NULL,	NULL),
(217,	'Not Bluffing',	'https://assets.ppy.sh/medals/web/osu-secret-bluffing.png',	'Did that with my eyes closed.',	'osu',	'Hush-Hush',	'<i>It\'s not arrogance if you can live up to it.</i>',	0,	'',	'LQOSNo_ZquY',	'2019-10-30',	'2020-04-23',	6192650),
(218,	'Eureka!',	'https://assets.ppy.sh/medals/web/all-secret-eureka.png',	'By Jove, you\'ve got it!',	'NULL',	'Hush-Hush',	'<i>When inspiration strikes...</i>',	0,	'',	'',	'2019-10-30',	NULL,	NULL),
(219,	'Regicide',	'https://assets.ppy.sh/medals/web/all-secret-regicide.png',	'A king no more.',	'NULL',	'Hush-Hush',	'<i>No throne lasts forever.</i>',	0,	'',	'',	'2019-10-30',	NULL,	NULL),
(220,	'Permadeath',	'https://assets.ppy.sh/medals/web/all-secret-permadeath.png',	'One life, one shot.',	'NULL',	'Hush-Hush',	'<i>Better make it count! Ware well the example of the white cat.</i>',	0,	'',	'',	'2019-10-30',	NULL,	NULL),
(221,	'The Future Is Now',	'https://assets.ppy.sh/medals/web/all-secret-futureisnow.png',	'The stars showed you all that was to come.',	'NULL',	'Hush-Hush',	'<i>Withstand the primordial.</i>',	0,	'0',	'',	'2019-10-30',	NULL,	NULL),
(222,	'Natural 20',	'https://assets.ppy.sh/medals/web/all-secret-nat20.png',	'Rolled it.',	'NULL',	'Hush-Hush',	'<i>Not just a chance of the dice, this one is all skill.</i>',	0,	'0',	'',	'2019-10-30',	NULL,	NULL),
(223,	'Kaleidoscope',	'https://assets.ppy.sh/medals/web/all-secret-kaleidoscope.png',	'So many pretty colours. Most of them red.',	'NULL',	'Hush-Hush',	'<i>See the tiny colours up through the tube up close and personal, and slow right down.</i>',	0,	'',	'',	'2019-10-30',	NULL,	NULL),
(224,	'AHAHAHAHA',	'https://assets.ppy.sh/medals/web/all-secret-yandere.png',	'TOGETHER FOREVER.',	'NULL',	'Hush-Hush',	'<i>Who am I? Where am I? What the hell IS this place?</i>',	0,	'',	'',	'2019-10-30',	NULL,	NULL),
(225,	'Valediction',	'https://assets.ppy.sh/medals/web/all-secret-valediction.png',	'One last time.',	'NULL',	'Hush-Hush',	'<i>Time stood still as we waved farewell.</i>',	0,	NULL,	NULL,	'2019-10-30',	NULL,	NULL),
(226,	'Mappers\' Guild Pack III',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-03.png',	'Sunrise was the end of a very magical night for one very special girl.',	'NULL',	'Beatmap Packs',	'<i>Complete all maps in the Mappers\' Guild Pack III.</i>',	5,	'1689',	NULL,	'0000-00-00',	NULL,	NULL),
(227,	'Mappers\' Guild Pack IV',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-04.png',	'This freeway is a sensation and boy, does it love you!',	'NULL',	'Beatmap Packs',	'<i>Complete all maps in the Mappers\' Guild Pack IV.</i>',	5,	'1757',	NULL,	'0000-00-00',	NULL,	NULL),
(228,	'Autumn 2019 Beatmap Spotlights',	'https://assets.ppy.sh/medals/web/spotlight-2019-autumn.png',	'Fall straight into the best maps of Autumn 2019!',	'NULL',	'Seasonal Spotlights',	'<i>Play all of the maps from one mode of the Autumn 2019 Beatmap Spotlights.</i>',	1,	'1798,1799,1800,1801',	'',	'0000-00-00',	NULL,	NULL),
(229,	'Afterparty',	'https://assets.ppy.sh/medals/web/all-packs-afterparty.png',	'Encore to the core.',	'NULL',	'Beatmap Packs',	'<i>Complete the Afterparty beatmap pack.</i>',	4,	'1542',	NULL,	'0000-00-00',	NULL,	NULL),
(230,	'Ben Briggs',	'https://assets.ppy.sh/medals/web/all-packs-benbriggs.png',	'Chiptunes, Pokemon, oh my!',	'NULL',	'Beatmap Packs',	'<i>Play all of the Ben Briggs pack.</i>',	4,	'1687',	NULL,	'0000-00-00',	NULL,	NULL),
(231,	'Carpool Tunnel',	'https://assets.ppy.sh/medals/web/all-packs-carpooltunnel.png',	'Not to be confused with an injury common to top-end players and people who watch anime.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Carpool Tunnel pack.</i>',	4,	'1805',	NULL,	'0000-00-00',	NULL,	NULL),
(232,	'Creo',	'https://assets.ppy.sh/medals/web/all-packs-creo.png',	'Dashing geometrically to a beatmap near you.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Creo pack.</i>',	4,	'1807',	NULL,	'0000-00-00',	NULL,	NULL),
(233,	'cYsmix',	'https://assets.ppy.sh/medals/web/all-packs-cysmix.png',	'Dead funky. For real.',	'NULL',	'Beatmap Packs',	'<i>Play all of the cYsmix pack.</i>',	4,	'1808',	NULL,	'0000-00-00',	NULL,	NULL),
(234,	'Fractal Dreamers',	'https://assets.ppy.sh/medals/web/all-packs-fractaldreamers.png',	'Shattered dreams and muted skies never sounded so good.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Fractal Dreamers pack.</i>',	4,	'1809',	NULL,	'0000-00-00',	NULL,	NULL),
(235,	'LukHash',	'https://assets.ppy.sh/medals/web/all-packs-lukhash.png',	'There\'s a ghost in this here machine.',	'NULL',	'Beatmap Packs',	'<i>Play all of the LukHash pack.</i>',	4,	'0,0,1758,0',	'',	'0000-00-00',	NULL,	NULL),
(236,	'*namirin',	'https://assets.ppy.sh/medals/web/all-packs-namirin.png',	'Five colors to make your world sing!',	'NULL',	'Beatmap Packs',	'<i>Play all of the *namirin pack.</i>',	4,	'1704',	NULL,	'0000-00-00',	NULL,	NULL),
(237,	'onumi',	'https://assets.ppy.sh/medals/web/all-packs-onumi.png',	'There\'s a lot going on here.',	'NULL',	'Beatmap Packs',	'<i>Play all of the onumi pack.</i>',	4,	'1804',	NULL,	'0000-00-00',	NULL,	NULL),
(238,	'The Flashbulb',	'https://assets.ppy.sh/medals/web/all-packs-theflashbulb.png',	'Not merely incandescent.',	'NULL',	'Beatmap Packs',	'<i>Play all of The Flashbulb pack.</i>',	4,	'1762',	NULL,	'0000-00-00',	NULL,	NULL),
(239,	'Undead Corporation',	'https://assets.ppy.sh/medals/web/all-packs-undeadcorporation.png',	'Diversifying underground assets. Literally.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Undead Corporation pack.</i>',	4,	'1810',	NULL,	'0000-00-00',	NULL,	NULL),
(240,	'Wisp X',	'https://assets.ppy.sh/medals/web/all-packs-wispx.png',	'Sunset and the moon.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Wisp X pack.</i>',	4,	'1806',	NULL,	'0000-00-00',	NULL,	NULL),
(241,	'Winter 2020 Beatmap Spotlights',	'https://assets.ppy.sh/medals/web/spotlight-2020-winter.png',	'The best of Winter 2020 in beatmap form!',	'NULL',	'Seasonal Spotlights',	'<i>Play all of the maps from one mode of the Winter 2020 Beatmap Spotlights.</i>',	2,	'1896,1897,1898,1899',	'',	'0000-00-00',	NULL,	NULL),
(242,	'Event Horizon',	'https://assets.ppy.sh/medals/web/osu-skill-pass-9.png',	'No force dares to pull you under.',	'osu',	'Skill',	'<i>Stare into the abyss, and pass the trial of any 9 star map.</i>',	2,	NULL,	NULL,	'2020-06-19',	NULL,	NULL),
(243,	'Chosen',	'https://assets.ppy.sh/medals/web/osu-skill-fc-9.png',	'Reign among the Prometheans, where you belong.',	'osu',	'Skill',	'<i>Triumph over one of the hardest beatmaps ever made, and FC a 9 star map.</i>',	3,	NULL,	NULL,	'2020-06-19',	NULL,	NULL),
(244,	'Phantasm',	'https://assets.ppy.sh/medals/web/osu-skill-pass-10.png',	'Fevered is your passion, extraordinary is your skill.',	'osu',	'Skill',	'<i>Approach the limit of human endurance, and pass a 10 star map.</i>',	2,	'',	'gr7Guy7vAEA',	'2020-06-19',	NULL,	NULL),
(245,	'Unfathomable',	'https://assets.ppy.sh/medals/web/osu-skill-fc-10.png',	'You have no equal.',	'osu',	'Skill',	'<i>Cement your place among legends, and FC any 10 star map.</i>',	3,	NULL,	NULL,	'2020-06-19',	NULL,	NULL),
(246,	'Camellia I',	'https://assets.ppy.sh/medals/web/all-packs-camellia-1.png',	'This is the entrance to the jungle.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Camellia Sets pack.</i>',	4,	'2051',	NULL,	'2020-11-20',	NULL,	NULL),
(247,	'Camellia II',	'https://assets.ppy.sh/medals/web/all-packs-camellia-2.png',	'Exit the atmosphere.',	'NULL',	'Beatmap Challenge Packs',	'<i>Play all of the Camellia Challenges pack, which requires no difficulty reduction mods active.</i>',	4,	'2053',	NULL,	'2020-11-20',	NULL,	NULL),
(248,	'Celldweller',	'https://assets.ppy.sh/medals/web/all-packs-celldweller.png',	'The end of an empire is no obstacle to you.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Celldweller pack.</i>',	4,	'2040',	NULL,	'2020-11-20',	NULL,	NULL),
(249,	'Cranky II',	'https://assets.ppy.sh/medals/web/all-packs-cranky2.png',	'Even crankier than before!',	'NULL',	'Beatmap Packs',	'<i>Play all of the Cranky 2 pack.</i>',	4,	'2049',	NULL,	'2020-11-20',	NULL,	NULL),
(250,	'Cute Anime Girls',	'https://assets.ppy.sh/medals/web/all-packs-CuteAnimeGirls.png',	'Not to be confused with cute flambé grills.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Cute Anime Girls pack.</i>',	4,	'2031',	NULL,	'2020-11-20',	NULL,	NULL),
(251,	'ELFENSJoN',	'https://assets.ppy.sh/medals/web/all-packs-ELFENSJoN.png',	'A world all of your own.',	'NULL',	'Beatmap Packs',	'<i>Play all of the ELFENSJoN pack.</i>',	4,	'2047',	NULL,	'2020-11-20',	NULL,	NULL),
(252,	'Hyper Potions',	'https://assets.ppy.sh/medals/web/all-packs-HyperPotions.png',	'Gain 200 HP.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Hyper Potions pack.</i>',	4,	'2037',	NULL,	'2020-11-20',	NULL,	NULL),
(253,	'Kola Kid',	'https://assets.ppy.sh/medals/web/all-packs-KolaKid.png',	'Remember, the Earth is counting on you.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Kola Kid pack.</i>',	4,	'2044',	NULL,	'2020-11-20',	NULL,	NULL),
(254,	'LeaF',	'https://assets.ppy.sh/medals/web/all-packs-leaf.png',	'Calamity was not in your fortune.',	'NULL',	'Beatmap Packs',	'<i>Play all of the LeaF pack.</i>',	4,	'2039',	NULL,	'2020-11-20',	NULL,	NULL),
(255,	'Panda Eyes',	'https://assets.ppy.sh/medals/web/all-packs-PandaEyes.png',	'Embrace the immortal flame.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Panda Eyes pack.</i>',	4,	'2043',	NULL,	'2020-11-20',	NULL,	NULL),
(256,	'PUP',	'https://assets.ppy.sh/medals/web/all-packs-PUP.png',	'Finally, some closure.',	'NULL',	'Beatmap Packs',	'<i>Play all of the PUP pack.</i>',	4,	'2048',	NULL,	'2020-11-20',	NULL,	NULL),
(257,	'Ricky Montgomery',	'https://assets.ppy.sh/medals/web/all-packs-RickyMontgomery.png',	'All hook and sinker.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Ricky Montgomery pack.</i>',	4,	'2046',	NULL,	'2020-11-20',	NULL,	NULL),
(258,	'Rin',	'https://assets.ppy.sh/medals/web/all-packs-Rin.png',	'Two minds, and a house full of ghosts.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Rin/Function Phantom pack.</i>',	4,	'1759',	NULL,	'2020-11-20',	NULL,	NULL),
(259,	'S3RL',	'https://assets.ppy.sh/medals/web/all-packs-S3RL.png',	'You\'ll never get any of these out of your head.',	'NULL',	'Beatmap Packs',	'<i>Play all of the S3RL pack.</i>',	4,	'2045',	NULL,	'2020-11-20',	NULL,	NULL),
(260,	'Sound Souler',	'https://assets.ppy.sh/medals/web/all-packs-SoundSouler.png',	'Return color to your life.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Sound Souler pack.</i>',	4,	'2038',	NULL,	'2020-11-20',	NULL,	NULL),
(261,	'Teminite',	'https://assets.ppy.sh/medals/web/all-packs-Teminite.png',	'It\'s all about the state of mind.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Teminite pack.</i>',	4,	'2042',	NULL,	'2020-11-20',	NULL,	NULL),
(262,	'VINXIS',	'https://assets.ppy.sh/medals/web/all-packs-VINXIS.png',	'Staying on track.',	'NULL',	'Beatmap Packs',	'<i>Play all of the VINXIS pack.</i>',	4,	'2041',	NULL,	'2020-11-20',	NULL,	NULL),
(263,	'Mappers\' Guild Pack V',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-05.png',	'Invisible birdwatching.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Mappers\' Guild V pack.</i>',	5,	'2032',	NULL,	'2020-11-20',	NULL,	NULL),
(264,	'Mappers\' Guild Pack VI',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-06.png',	'Turn your superpower to hyperdrive!',	'NULL',	'Beatmap Packs',	'<i>Play all of the Mappers\' Guild VI pack.</i>',	5,	'2033',	NULL,	'2020-11-20',	NULL,	NULL),
(265,	'Mappers\' Guild Pack VII',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-07.png',	'A new set of vibrant challenges to overcome.',	'NULL',	'Beatmap Challenge Packs',	'<i>Play all of the Mappers\' Guild VII pack, which require no difficulty reduction mods when submitting a score.</i>',	0,	'2034',	'',	'2020-11-20',	NULL,	NULL),
(266,	'Mappers\' Guild Pack VIII',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-08.png',	'Succeed with a chorus of voices.',	'NULL',	'Beatmap Challenge Packs',	'<i>Play all of the Mappers\' Guild VIII pack, which require no difficulty reduction mods when submitting a score.</i>',	0,	'2035,,,',	'',	'2020-11-20',	'0000-00-00',	0),
(267,	'Mappers\' Guild Pack IX',	'https://assets.ppy.sh/medals/web/all-packs-mappersguild-09.png',	'This is no subtle change.',	'NULL',	'Beatmap Challenge Packs',	'<i>Play all of the Mappers\' Guild IX pack, which require no difficulty reduction mods when submitting a score.</i>',	0,	'2036',	'',	'2020-11-20',	NULL,	NULL),
(268,	'Ten To One',	'https://assets.ppy.sh/medals/web/all-secret-tentoone.png',	'From one extreme to another.',	'NULL',	'Hush-Hush',	'<i>From a marathon to a sprint.</i>',	0,	'',	'',	'2021-12-31',	'2021-12-31',	11554464),
(269,	'Exquisite',	'https://assets.ppy.sh/medals/web/all-secret-exquisite.png',	'Indubitably.',	'NULL',	'Hush-Hush',	'<i>Do it once with true calibre, then do it again.</i>',	0,	'0',	'b_veNxs4VXA',	'2021-12-31',	'2021-12-31',	3819901),
(270,	'Persistence Is Key',	'https://assets.ppy.sh/medals/web/all-secret-persistenceiskey.png',	'Don\'t let your dreams be dreams.',	'NULL',	'Hush-Hush',	'<i>Don\'t give up!</i>',	0,	'',	'',	'2021-12-31',	'2021-12-31',	3031177),
(271,	'Mad Scientist',	'https://assets.ppy.sh/medals/web/all-secret-madscientist.png',	'The experiment... it\'s all gone!',	'NULL',	'Hush-Hush',	'<i>Become invisible.</i>',	0,	'',	'85zNEZLZlFU',	'2021-12-31',	'2021-12-31',	4687701),
(272,	'Tribulation',	'https://assets.ppy.sh/medals/web/all-secret-tribulation.png',	'Success is inevitable... eventually.',	'NULL',	'Hush-Hush',	'<i>Struggle and then stop.</i>',	0,	'0',	'9eeMH0_P1lM',	'2022-05-02',	NULL,	NULL),
(273,	'Right On Time',	'https://assets.ppy.sh/medals/web/all-secret-rightontime.png',	'The first minute is always the hardest.',	'NULL',	'Hush-Hush',	'<i>Every hour\'s got sixty, but your timer only wants one.</i>',	0,	'0',	'6MpYdHdLGs8',	'2022-05-02',	'2022-05-02',	10249166),
(274,	'Replica',	'https://assets.ppy.sh/medals/web/all-secret-replica.png',	'One just like the other.',	'NULL',	'Hush-Hush',	'<i>Duplicate.</i>',	0,	'',	'',	'0000-00-00',	'2022-05-03',	5510197),
(275,	'All Good',	'https://assets.ppy.sh/medals/web/all-secret-allgood.png',	'Better now, thanks!',	'NULL',	'Hush-Hush',	'<i>You got this.</i>',	0,	'0',	'',	'2022-05-02',	'2022-05-02',	10249166),
(276,	'Dead Center',	'https://assets.ppy.sh/medals/web/all-secret-deadcenter.png',	'As all things should be.',	'NULL',	'Hush-Hush',	'<i>Perfect balance.</i>',	0,	'0',	'',	'2022-06-09',	NULL,	NULL),
(277,	'In Memoriam',	'https://assets.ppy.sh/medals/web/osu-secret-inmemoriam.png',	'In loving memory of your sanity, long forgotten.',	'osu',	'Hush-Hush',	'<i>Conquer a test of the most frustrating combination of mods imaginable.</i>',	0,	'0',	'',	'2022-06-09',	NULL,	NULL),
(278,	'Sanguine',	'https://assets.ppy.sh/medals/web/osu-secret-sanguine.png',	'Timeless thorns still draw blood.',	'osu',	'Hush-Hush',	'<i>Return to the past, when everything was simpler.</i>',	0,	'0',	'DrvD73w2wdA',	'2022-06-09',	NULL,	NULL),
(279,	'Not Again',	'https://assets.ppy.sh/medals/web/all-secret-notagain.png',	'Regret everything.',	'NULL',	'Hush-Hush',	'<i>You had it all, and then it was gone.</i>',	0,	'0',	'PFgkS7aIYQ4',	'2022-06-09',	NULL,	NULL),
(280,	'Final Boss',	'https://assets.ppy.sh/medals/web/osu-secret-finalboss.png',	'Game over.',	'osu',	'Hush-Hush',	'<i>Let the credits roll.</i>',	0,	'0',	'5VHyPf6cdZo',	'2022-06-09',	'2022-06-10',	12017880),
(281,	'Beast Mode',	'https://assets.ppy.sh/medals/web/osu-secret-beastmode.png',	'Unleash the animal within!',	'osu',	'Hush-Hush',	'<i>Go absolutely feral.</i>',	0,	'0',	'y6M51Y3VE10',	'2022-06-09',	'2022-06-09',	11715109),
(282,	'Touhou Pack',	'https://assets.ppy.sh/medals/web/all-packs-touhou.png',	'The bamboo isn\'t the only thing dancing!',	'NULL',	'Beatmap Packs',	'<i>Play all of the Touhou beatmap pack.</i>',	4,	'2457',	'',	'2022-07-25',	NULL,	NULL),
(283,	'ginkiha Pack',	'https://assets.ppy.sh/medals/web/all-packs-ginkiha.png',	'The night stars shine the brightest of all.',	'NULL',	'Beatmap Packs',	'<i>Play all of the ginkiha beatmap pack.</i>',	4,	'2458',	'',	'2022-07-25',	NULL,	NULL),
(284,	'MUZZ Pack',	'https://assets.ppy.sh/medals/web/all-packs-muzz.png',	'Break away from the endgame.',	'NULL',	'Beatmap Challenge Packs',	'<i>Play all of the MUZZ beatmap pack, which requires no difficulty reduction mods when submitting a score.</i>',	4,	'2459',	'',	'2022-07-25',	NULL,	NULL),
(285,	'Deliberation',	'https://assets.ppy.sh/medals/web/all-secret-deliberation.png',	'The challenge remains.',	'NULL',	'Hush-Hush',	'<i>Turn a short challenge into a long one.</i>',	0,	'',	'YktCZN6Ur_4',	'2022-09-09',	'2022-09-10',	1719471),
(286,	'Lightless',	'https://assets.ppy.sh/medals/web/all-secret-lightless.png',	'Better the devil you know.',	'NULL',	'Hush-Hush',	'<i>I CAN\'T SEE A THING.</i>',	0,	'',	'uE36Go4kQWA',	'2022-09-09',	'2022-09-14',	2229274),
(287,	'When You See It',	'https://assets.ppy.sh/medals/web/all-secret-when-you-see-it.png',	'Three numbers which will haunt you forevermore.',	'NULL',	'Hush-Hush',	'<i>You don\'t need a hint for this one.</i>',	0,	'',	'XI6nV8Qj2gs',	'2022-09-09',	'2022-09-10',	29473368),
(288,	'Vocaloid Pack',	'https://assets.ppy.sh/medals/web/all-packs-vocaloid.png',	'What\'s life without a song?',	'NULL',	'Beatmap Packs',	'<i>Play all of the Vocaloid beatmap pack.</i>',	4,	'2481',	'',	'2022-09-10',	NULL,	NULL),
(289,	'Maduk Pack',	'https://assets.ppy.sh/medals/web/all-packs-maduk.png',	'You took control.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Maduk beatmap pack.</i>',	4,	'2482',	'',	'2022-09-10',	NULL,	NULL),
(290,	'Aitsuki Nakuru Pack',	'https://assets.ppy.sh/medals/web/all-packs-aitsuki.png',	'Took part in the Joker\'s Parade.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Aitsuki Nakuru beatmap pack.</i>',	4,	'2483',	'',	'2022-09-10',	NULL,	NULL),
(291,	'30,000,000 Drum Hits',	'https://assets.ppy.sh/medals/web/taiko-hits-30000000.png',	'Your rhythm, eternal.',	'taiko',	'Dedication',	NULL,	2,	'',	'',	'2022-11-04',	'0000-00-00',	0),
(292,	'Catch 20,000,000 fruits',	'https://assets.ppy.sh/medals/web/fruits-hits-20000000.png',	'Nothing left behind.',	'fruits',	'Dedication',	NULL,	1,	'',	'',	'2022-11-04',	'0000-00-00',	0),
(293,	'40,000,000 Keys',	'https://assets.ppy.sh/medals/web/mania-hits-40000000.png',	'When someone asks which keys you play, the answer is now \'yes\'.',	'mania',	'Dedication',	NULL,	3,	'',	'',	'2022-11-04',	'0000-00-00',	0),
(294,	'Ariabl\'eyeS Pack',	'https://assets.ppy.sh/medals/web/all-packs-ariableyes.png',	'Command the mercurial skies.',	'NULL',	'Beatmap Challenge Packs',	'<i>Play all of the Ariabl\'eyes beatmap pack, which requires no difficulty reduction mods.</i>',	4,	'2521,,,',	'',	'0000-00-00',	'0000-00-00',	0),
(295,	'Omoi Pack',	'https://assets.ppy.sh/medals/web/all-packs-omoi.png',	'Command the mercurial skies.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Omoi beatmap pack.</i>',	4,	'2522,,,',	'',	'2022-11-04',	'2022-11-04',	18152711),
(296,	'Chill Pack',	'https://assets.ppy.sh/medals/web/all-packs-chill.png',	'Just vibin\'.',	'NULL',	'Beatmap Packs',	'<i>Play all of the Chill beatmap pack.</i>',	4,	'2523,,,',	'',	'2022-11-04',	'2022-11-04',	7279762),
(297,	'Mortal Coils',	'https://assets.ppy.sh/medals/web/all-secret-together-apart.png',	'Never one without the other.',	'osu',	'Hush-Hush',	'<i>Prisoners of flesh in an unending apocalypse.</i>',	0,	'',	'',	'2022-11-04',	'0000-00-00',	0),
(298,	'Dark Familiarity',	'https://assets.ppy.sh/medals/web/all-secret-dark-familiarity.png',	'No mistakes, no witnesses.',	'osu',	'Hush-Hush',	'<i>Don\'t second guess yourself.</i>',	0,	'',	'',	'2022-11-04',	'2022-11-04',	11220416),
(299,	'Creator\'s Gambit',	'https://assets.ppy.sh/medals/web/all-secret-trophy.png',	'I made this.',	'NULL',	'Hush-Hush',	'<i>Make your own prize.</i>',	0,	'',	'',	'2022-11-04',	'2022-11-04',	11220416);

-- Adminer 4.8.1 MySQL 10.11.2-MariaDB dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `MedalsBeatmapPacks`;
CREATE TABLE `MedalsBeatmapPacks` (
  `Id` text NOT NULL,
  `Count` int(11) NOT NULL,
  `Ids` longtext NOT NULL CHECK (json_valid(`Ids`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

INSERT INTO `MedalsBeatmapPacks` (`Id`, `Count`, `Ids`) VALUES
('1508',	13,	'[\"585154\",\"671268\",\"745020\",\"767446\",\"776247\",\"777322\",\"782784\",\"785774\",\"790979\",\"793027\",\"793438\",\"800070\",\"813036\"]'),
('1509',	14,	'[\"345990\",\"438839\",\"445365\",\"646093\",\"666764\",\"708312\",\"712825\",\"733461\",\"736594\",\"738600\",\"739116\",\"739136\",\"767708\",\"792231\"]'),
('1510',	6,	'[\"501962\",\"520301\",\"596327\",\"639409\",\"729321\",\"762958\"]'),
('1511',	11,	'[\"540432\",\"624059\",\"650415\",\"678723\",\"701369\",\"702559\",\"765056\",\"776845\",\"788120\",\"788366\",\"791316\"]'),
('1548',	18,	'[\"397764\",\"580596\",\"670449\",\"679935\",\"759524\",\"761140\",\"765801\",\"777597\",\"800638\",\"816542\",\"817505\",\"832836\",\"838383\",\"839256\",\"841634\",\"847126\",\"862156\",\"876259\"]'),
('1549',	9,	'[\"587426\",\"732190\",\"762660\",\"765430\",\"776165\",\"779916\",\"821745\",\"832971\",\"835011\"]'),
('1550',	8,	'[\"539769\",\"576786\",\"699439\",\"715066\",\"797236\",\"812855\",\"840189\",\"842871\"]'),
('1551',	5,	'[\"696380\",\"779906\",\"800663\",\"810911\",\"819153\"]'),
('1186',	11,	'[\"192896\",\"401085\",\"432215\",\"486535\",\"496930\",\"499488\",\"509341\",\"527661\",\"532827\",\"534280\",\"565219\"]'),
('1187',	10,	'[\"319845\",\"425994\",\"474003\",\"507007\",\"526265\",\"532100\",\"538110\",\"539245\",\"540913\",\"553741\"]'),
('1188',	10,	'[\"150054\",\"308633\",\"382400\",\"460422\",\"485700\",\"499488\",\"506237\",\"516494\",\"526626\",\"536049\"]'),
('1189',	11,	'[\"236697\",\"430938\",\"432015\",\"468353\",\"474288\",\"490717\",\"505048\",\"518266\",\"524596\",\"531090\",\"535272\"]'),
('1405',	6,	'[\"127772\",\"508947\",\"692433\",\"696222\",\"707720\",\"716193\"]'),
('1407',	4,	'[\"358799\",\"680739\",\"717359\",\"742538\"]'),
('1408',	2,	'[\"485733\",\"694025\"]'),
('0',	0,	'[]'),
('1228',	7,	'[\"437097\",\"504315\",\"554084\",\"565432\",\"567325\",\"578332\",\"585681\"]'),
('1229',	6,	'[\"316779\",\"373254\",\"427864\",\"559260\",\"568544\",\"603069\"]'),
('1230',	3,	'[\"429184\",\"536749\",\"594326\"]'),
('1232',	5,	'[\"407153\",\"456289\",\"555076\",\"565779\",\"588066\"]'),
('1649',	6,	'[\"929284\",\"938370\",\"940597\",\"941085\",\"949297\",\"958299\"]'),
('1798',	12,	'[\"607948\",\"819752\",\"914754\",\"922249\",\"935839\",\"940377\",\"1001507\",\"1006502\",\"1015203\",\"1020276\",\"1020878\",\"1054045\"]'),
('1799',	6,	'[\"978134\",\"984900\",\"999187\",\"999585\",\"1022545\",\"1026679\"]'),
('1800',	8,	'[\"649285\",\"930513\",\"946446\",\"981762\",\"994994\",\"1005623\",\"1009824\",\"1032859\"]'),
('1801',	8,	'[\"682615\",\"809495\",\"867026\",\"887787\",\"890013\",\"962550\",\"983953\",\"1023425\"]'),
('1896',	8,	'[\"576232\",\"874732\",\"998836\",\"999260\",\"1056144\",\"1067056\",\"1070444\",\"1086289\"]'),
('1897',	4,	'[\"1006568\",\"1013884\",\"1032172\",\"1056653\"]'),
('1898',	4,	'[\"738169\",\"1044462\",\"1061800\",\"1080025\"]'),
('1899',	5,	'[\"539179\",\"877895\",\"905599\",\"914341\",\"1069426\"]'),
('1379',	5,	'[\"546820\",\"587541\",\"604273\",\"660630\",\"706545\"]'),
('1380',	6,	'[\"351126\",\"567140\",\"679686\",\"688928\",\"715185\",\"723229\"]'),
('1381',	4,	'[\"452107\",\"597325\",\"633255\",\"695431\"]'),
('1382',	2,	'[\"416020\",\"619531\"]'),
('',	0,	'[]'),
('40',	14,	'[\"13022\",\"16520\",\"23073\",\"27936\",\"32162\",\"40233\",\"42158\",\"42956\",\"59370\",\"71476\",\"72137\",\"102913\",\"169848\",\"211704\"]'),
('41',	14,	'[\"26489\",\"30229\",\"35068\",\"36849\",\"40826\",\"79271\",\"114488\",\"165202\",\"202036\",\"290339\",\"298245\",\"322481\",\"350295\",\"371569\"]'),
('42',	14,	'[\"13177\",\"17217\",\"17724\",\"18568\",\"23754\",\"31419\",\"31811\",\"45341\",\"102615\",\"116487\",\"148979\",\"239262\",\"332436\",\"435578\"]'),
('43',	14,	'[\"24610\",\"28565\",\"46456\",\"50763\",\"50988\",\"51999\",\"89379\",\"103887\",\"104801\",\"168380\",\"206887\",\"240919\",\"332623\",\"378596\"]'),
('48',	14,	'[\"12952\",\"15972\",\"19350\",\"25339\",\"26196\",\"39348\",\"42457\",\"48461\",\"54599\",\"60060\",\"64808\",\"105081\",\"220675\",\"375668\"]'),
('49',	14,	'[\"21472\",\"24581\",\"27540\",\"37914\",\"38459\",\"44550\",\"59569\",\"69158\",\"88895\",\"91688\",\"95638\",\"104169\",\"127920\",\"409649\"]'),
('70',	14,	'[\"19847\",\"26811\",\"28708\",\"40047\",\"41529\",\"54032\",\"54965\",\"62720\",\"66350\",\"107427\",\"118227\",\"119277\",\"147714\",\"180286\"]'),
('93',	14,	'[\"24152\",\"25198\",\"31471\",\"36920\",\"53810\",\"54631\",\"63500\",\"74110\",\"106500\",\"119980\",\"130725\",\"196930\",\"232505\",\"299643\"]'),
('94',	14,	'[\"22690\",\"25342\",\"39017\",\"40996\",\"43510\",\"51152\",\"54511\",\"67226\",\"146947\",\"178817\",\"249703\",\"256467\",\"272555\",\"372850\"]'),
('207',	14,	'[\"28690\",\"67202\",\"98117\",\"107038\",\"109373\",\"117857\",\"154847\",\"164827\",\"169410\",\"173234\",\"184786\",\"190961\",\"335145\",\"398176\"]'),
('208',	14,	'[\"24320\",\"40740\",\"47664\",\"50017\",\"58422\",\"62537\",\"68926\",\"79838\",\"119447\",\"149648\",\"189825\",\"287554\",\"327825\",\"372200\"]'),
('209',	14,	'[\"15849\",\"28222\",\"28799\",\"36225\",\"47517\",\"53363\",\"53569\",\"70259\",\"105186\",\"192763\",\"221414\",\"336207\",\"347433\",\"386151\"]'),
('363',	14,	'[\"20206\",\"22121\",\"24565\",\"29522\",\"39249\",\"39997\",\"40060\",\"43839\",\"46862\",\"50567\",\"56259\",\"87921\",\"120328\",\"142545\"]'),
('365',	14,	'[\"12177\",\"14016\",\"37090\",\"41860\",\"50772\",\"134656\",\"139525\",\"156235\",\"183003\",\"190754\",\"199535\",\"239387\",\"240733\",\"310401\"]'),
('366',	14,	'[\"13885\",\"14672\",\"21581\",\"22252\",\"23058\",\"24084\",\"25154\",\"37563\",\"45698\",\"47078\",\"155910\",\"176702\",\"213629\",\"247243\"]'),
('364',	14,	'[\"4033\",\"19880\",\"19928\",\"31934\",\"41545\",\"53699\",\"56592\",\"72743\",\"73671\",\"95568\",\"165273\",\"249587\",\"310056\",\"366835\"]'),
('1201',	7,	'[\"162800\",\"325432\",\"352083\",\"408558\",\"551831\",\"561131\",\"572897\"]'),
('1202',	5,	'[\"246705\",\"381103\",\"420395\",\"491791\",\"559371\"]'),
('1203',	3,	'[\"349949\",\"516118\",\"544468\"]'),
('1204',	5,	'[\"407237\",\"494819\",\"529155\",\"539611\",\"571189\"]'),
('1219',	5,	'[\"479108\",\"559928\",\"561693\",\"574929\",\"593883\"]'),
('1220',	4,	'[\"458597\",\"527082\",\"551271\",\"556731\"]'),
('1221',	2,	'[\"476944\",\"512281\"]'),
('1222',	1,	'[\"540175\"]'),
('1244',	5,	'[\"458983\",\"554297\",\"557231\",\"580215\",\"596079\"]'),
('1245',	4,	'[\"536955\",\"547301\",\"615774\",\"619832\"]'),
('1246',	4,	'[\"275991\",\"458983\",\"514144\",\"536001\"]'),
('1247',	3,	'[\"529574\",\"569903\",\"575053\"]'),
('1253',	5,	'[\"516995\",\"554892\",\"559843\",\"564354\",\"603694\"]'),
('1254',	6,	'[\"500752\",\"567504\",\"606364\",\"609391\",\"625493\",\"627934\"]'),
('1255',	3,	'[\"380329\",\"559525\",\"602506\"]'),
('1256',	3,	'[\"207525\",\"426638\",\"459950\"]'),
('1264',	5,	'[\"361306\",\"501677\",\"518426\",\"534800\",\"611301\"]'),
('1265',	7,	'[\"605096\",\"606833\",\"609906\",\"615067\",\"630650\",\"643269\",\"643803\"]'),
('1267',	4,	'[\"392304\",\"519023\",\"569985\",\"648071\"]'),
('1268',	0,	'[]'),
('1284',	0,	'[]'),
('1280',	0,	'[]'),
('1281',	0,	'[]'),
('1282',	0,	'[]'),
('1283',	0,	'[]'),
('1292',	0,	'[]'),
('1293',	0,	'[]'),
('1294',	0,	'[]'),
('1295',	0,	'[]'),
('1302',	0,	'[]'),
('1303',	0,	'[]'),
('1304',	0,	'[]'),
('1305',	0,	'[]'),
('1331',	0,	'[]'),
('1332',	0,	'[]'),
('1333',	0,	'[]'),
('1334',	0,	'[]'),
('1354',	0,	'[]'),
('1355',	0,	'[]'),
('1356',	0,	'[]'),
('1357',	0,	'[]'),
('1365',	0,	'[]'),
('1430',	0,	'[]'),
('1431',	0,	'[]'),
('1432',	0,	'[]'),
('1433',	0,	'[]'),
('1437',	0,	'[]'),
('1450',	0,	'[]'),
('1480',	0,	'[]'),
('1535',	0,	'[]'),
('1581',	0,	'[]'),
('1623',	0,	'[]'),
('1624',	0,	'[]'),
('1625',	0,	'[]'),
('1626',	0,	'[]'),
('1670',	0,	'[]'),
('1671',	0,	'[]'),
('1672',	0,	'[]'),
('1673',	0,	'[]'),
('1688',	0,	'[]'),
('1722',	0,	'[]'),
('1723',	0,	'[]'),
('1724',	0,	'[]'),
('1725',	0,	'[]'),
('1689',	0,	'[]'),
('1757',	0,	'[]'),
('1542',	0,	'[]'),
('1687',	0,	'[]'),
('1805',	0,	'[]'),
('1807',	0,	'[]'),
('1808',	0,	'[]'),
('1809',	0,	'[]'),
('1704',	0,	'[]'),
('1804',	0,	'[]'),
('1762',	0,	'[]'),
('1810',	0,	'[]'),
('1806',	0,	'[]'),
('2051',	0,	'[]'),
('2053',	0,	'[]'),
('2040',	0,	'[]'),
('2049',	0,	'[]'),
('2031',	0,	'[]'),
('2047',	0,	'[]'),
('2037',	0,	'[]'),
('2044',	0,	'[]'),
('2039',	0,	'[]'),
('2043',	0,	'[]'),
('2048',	0,	'[]'),
('2046',	0,	'[]'),
('1759',	0,	'[]'),
('2045',	0,	'[]'),
('2038',	0,	'[]'),
('2042',	0,	'[]'),
('2041',	0,	'[]'),
('2032',	0,	'[]'),
('2033',	0,	'[]'),
('2034',	0,	'[]'),
('2035',	0,	'[]'),
('2036',	0,	'[]'),
('2457',	0,	'[]'),
('2458',	0,	'[]'),
('2459',	0,	'[]'),
('2481',	0,	'[]'),
('2482',	0,	'[]'),
('2483',	0,	'[]'),
('2521',	0,	'[]'),
('2522',	0,	'[]'),
('2523',	0,	'[]');

-- 2023-05-17 16:41:27


DROP TABLE IF EXISTS `MedalStructure`;
CREATE TABLE `MedalStructure` (
  `ID` int(5) NOT NULL AUTO_INCREMENT,
  `MedalID` int(5) NOT NULL,
  `Locked` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `MedalID` (`MedalID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

INSERT INTO `MedalStructure` (`ID`, `MedalID`, `Locked`) VALUES
(2,	51,	1),
(3,	52,	1),
(4,	53,	1),
(5,	7,	1),
(6,	8,	1),
(7,	9,	1),
(8,	10,	1),
(9,	11,	1),
(10,	12,	1),
(11,	14,	1),
(12,	18,	1),
(13,	19,	1),
(14,	25,	1),
(15,	26,	1),
(16,	27,	1),
(17,	34,	1),
(18,	35,	1),
(19,	36,	1),
(20,	37,	1),
(21,	162,	1),
(22,	163,	1),
(25,	166,	1),
(26,	167,	1),
(27,	169,	1),
(28,	179,	1),
(29,	180,	1),
(30,	181,	1),
(31,	182,	1),
(32,	183,	1),
(33,	184,	1),
(34,	185,	1),
(35,	186,	1),
(36,	187,	1),
(37,	188,	1),
(38,	189,	1),
(39,	190,	1),
(40,	191,	1),
(41,	205,	1),
(42,	206,	1),
(43,	207,	1),
(44,	208,	1),
(45,	209,	1),
(46,	210,	1),
(47,	213,	1),
(48,	214,	1),
(49,	215,	1),
(50,	226,	1),
(51,	227,	1),
(52,	228,	1),
(53,	229,	1),
(54,	230,	1),
(55,	231,	1),
(56,	232,	1),
(57,	233,	1),
(58,	234,	1),
(59,	235,	1),
(61,	192,	1),
(62,	173,	1),
(64,	221,	1),
(67,	201,	1),
(68,	156,	1),
(69,	193,	1),
(70,	219,	1),
(71,	223,	1),
(72,	220,	1),
(74,	216,	1),
(75,	225,	1),
(77,	200,	1),
(78,	154,	1),
(79,	175,	1),
(80,	150,	1),
(81,	197,	1),
(82,	174,	1),
(84,	195,	1),
(85,	204,	1),
(86,	153,	1),
(87,	241,	1),
(89,	17,	1),
(90,	45,	1),
(91,	20,	1),
(92,	21,	1),
(93,	22,	1),
(96,	28,	1),
(97,	29,	1),
(98,	30,	1),
(99,	31,	1),
(100,	32,	1),
(101,	33,	1),
(102,	46,	1),
(103,	47,	1),
(104,	48,	1),
(105,	196,	1),
(106,	202,	1),
(108,	42,	1),
(109,	164,	1),
(110,	165,	1),
(117,	248,	1),
(118,	249,	1),
(120,	239,	1),
(121,	236,	1),
(122,	237,	1),
(123,	238,	1),
(124,	240,	1),
(126,	256,	1),
(127,	257,	1),
(130,	258,	1),
(131,	259,	1),
(132,	260,	1),
(133,	261,	1),
(134,	262,	1),
(135,	246,	1),
(137,	247,	1),
(139,	250,	1),
(140,	251,	1),
(142,	253,	1),
(143,	254,	1),
(144,	255,	1),
(145,	252,	1),
(146,	265,	1),
(147,	266,	1),
(213,	263,	1),
(216,	264,	1),
(226,	267,	1),
(238,	6,	1),
(265,	194,	1),
(269,	138,	1),
(272,	148,	1),
(281,	15,	1),
(287,	54,	1),
(299,	56,	1),
(320,	79,	1),
(323,	80,	1),
(332,	87,	1),
(335,	88,	1),
(338,	13,	1),
(341,	23,	1),
(344,	24,	1),
(347,	119,	1),
(350,	120,	1),
(353,	121,	1),
(356,	122,	1),
(359,	123,	1),
(362,	124,	1),
(365,	125,	1),
(368,	126,	1),
(371,	127,	1),
(374,	128,	1),
(377,	131,	1),
(419,	55,	1),
(433,	71,	1),
(436,	72,	1),
(441,	217,	1),
(449,	270,	1),
(452,	271,	1),
(460,	1,	1),
(463,	3,	1),
(503,	95,	1),
(565,	171,	1),
(574,	155,	1),
(577,	273,	1),
(580,	275,	1),
(588,	272,	1),
(599,	224,	1),
(602,	274,	1),
(615,	279,	1),
(629,	278,	1),
(682,	281,	1),
(697,	280,	1),
(708,	0,	1),
(748,	283,	1),
(751,	282,	1),
(798,	286,	1),
(814,	170,	1),
(830,	290,	1),
(833,	288,	1),
(836,	289,	1),
(839,	284,	1),
(842,	299,	1),
(845,	297,	1),
(860,	298,	1),
(861,	294,	1),
(864,	296,	1),
(870,	295,	1),
(874,	293,	1),
(877,	292,	1),
(889,	50,	1),
(902,	291,	1);

DROP TABLE IF EXISTS `Members`;
CREATE TABLE `Members` (
  `id` int(15) NOT NULL,
  `OPT_Experimental` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


DROP TABLE IF EXISTS `MembersRestrictions`;
CREATE TABLE `MembersRestrictions` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(11) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `Reason` text COLLATE utf8mb4_bin NOT NULL,
  `Active` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `Notifications`;
CREATE TABLE `Notifications` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `SystemID` varchar(50) DEFAULT NULL,
  `UserID` int(20) NOT NULL,
  `Title` varchar(50) NOT NULL,
  `Message` varchar(500) NOT NULL,
  `App` int(10) DEFAULT NULL,
  `HTML` varchar(1000) NOT NULL,
  `Link` varchar(250) DEFAULT NULL,
  `Date` datetime NOT NULL,
  `Cleared` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`ID`),
  UNIQUE KEY `SystemID` (`SystemID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `OsekaiSessions`;
CREATE TABLE `OsekaiSessions` (
  `sessionID` mediumint(9) NOT NULL AUTO_INCREMENT,
  `sessionToken` varchar(48) NOT NULL,
  `sessionData` text,
  `sessionLastChange` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`sessionID`),
  KEY `sessionToken` (`sessionToken`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Payments`;
CREATE TABLE `Payments` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `Reason` varchar(200) COLLATE latin1_german1_ci NOT NULL,
  `Amount` decimal(5,2) NOT NULL,
  `PayDate` date NOT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


DROP TABLE IF EXISTS `ProfilesBanners`;
CREATE TABLE `ProfilesBanners` (
  `UserID` int(11) NOT NULL,
  `Background` text,
  `Foreground` text,
  `CustomGradient` text,
  `CustomSolid` text,
  `CustomImage` text,
  `CustomStyle` text,
  PRIMARY KEY (`UserID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ProfilesShowcasePanel`;
CREATE TABLE `ProfilesShowcasePanel` (
  `UserID` int(11) NOT NULL,
  `Type` text NOT NULL,
  `Ids` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ProfilesUserinfo`;
CREATE TABLE `ProfilesUserinfo` (
  `osuID` int(11) NOT NULL,
  `Username` text NOT NULL,
  `Rank` int(11) NOT NULL,
  PRIMARY KEY (`osuID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `ProfilesVisited`;
CREATE TABLE `ProfilesVisited` (
  `visited_by` int(11) NOT NULL,
  `visited_id` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Ranking`;
CREATE TABLE `Ranking` (
  `id` int(11) NOT NULL,
  `name` varchar(30) COLLATE latin1_german1_ci NOT NULL,
  `total_pp` int(11) NOT NULL,
  `stdev_pp` int(11) NOT NULL,
  `standard_pp` int(11) NOT NULL,
  `taiko_pp` int(11) NOT NULL,
  `ctb_pp` int(11) NOT NULL,
  `mania_pp` int(11) NOT NULL,
  `medal_count` int(11) NOT NULL,
  `rarest_medal` int(11) NOT NULL,
  `rarest_medal_achieved` datetime DEFAULT NULL,
  `country_code` varchar(3) COLLATE latin1_german1_ci NOT NULL,
  `standard_global` int(20) DEFAULT NULL,
  `taiko_global` int(20) DEFAULT NULL,
  `ctb_global` int(20) DEFAULT NULL,
  `mania_global` int(20) DEFAULT NULL,
  `badge_count` int(10) NOT NULL,
  `ranked_maps` int(6) NOT NULL,
  `loved_maps` int(6) NOT NULL,
  `subscribers` int(20) NOT NULL DEFAULT '0',
  `followers` int(20) NOT NULL,
  `replays_watched` int(12) NOT NULL,
  `avatar_url` int(11) NOT NULL,
  `stdev_acc` double DEFAULT NULL,
  `standard_acc` double DEFAULT NULL,
  `taiko_acc` double DEFAULT NULL,
  `ctb_acc` double DEFAULT NULL,
  `mania_acc` double DEFAULT NULL,
  `stdev_level` int(11) DEFAULT NULL,
  `standard_level` int(11) DEFAULT NULL,
  `taiko_level` int(11) DEFAULT NULL,
  `ctb_level` int(11) DEFAULT NULL,
  `mania_level` int(11) DEFAULT NULL,
  `kudosu` int(11) DEFAULT NULL,
  `restricted` int(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


DROP TABLE IF EXISTS `RankingLoopHistory`;
CREATE TABLE `RankingLoopHistory` (
  `Id` int(11) NOT NULL,
  `Time` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `LoopType` text COLLATE utf8mb4_bin NOT NULL,
  `Amount` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `RankingLoopInfo`;
CREATE TABLE `RankingLoopInfo` (
  `CurrentLoop` text COLLATE utf8mb4_bin NOT NULL,
  `CurrentCount` bigint(20) NOT NULL,
  `TotalCount` bigint(20) NOT NULL,
  `EtaSeconds` bigint(20) NOT NULL,
  `LastEtaUpdate` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `RankingMedalChampionHistory`;
CREATE TABLE `RankingMedalChampionHistory` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `UserId` int(11) NOT NULL,
  `Date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


DROP TABLE IF EXISTS `Roles`;
CREATE TABLE `Roles` (
  `RoleID` int(10) NOT NULL AUTO_INCREMENT,
  `User` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `UserID` int(20) NOT NULL,
  `RoleName` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `Role` int(11) NOT NULL,
  `Rights` varchar(20) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY (`RoleID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

INSERT INTO `Roles` (`RoleID`, `User`, `UserID`, `RoleName`, `Role`, `Rights`) VALUES
(1,	'mulraf',	1309242,	'DEV',	1,	'2'),
(2,	'Hubz',	10379965,	'DEV',	1,	'2'),
(3,	'Remyria',	1699875,	'CMT',	8,	'1'),
(4,	'Mr HeliX',	2330619,	'&#10084;',	4,	'0'),
(5,	'ElectroYan',	3357640,	'APP DEV',	9,	'2'),
(6,	'chromb',	10238680,	'chromb',	7,	'1'),
(8,	'vexAkita',	8923804,	'&#10084;&#10084;&#10084;',	6,	'0'),
(9,	'ILuvSkins',	16487835,	'SNAPSHOTS MOD',	3,	'1'),
(10,	'Eeveelution',	8438068,	'SNAPSHOTS MOD',	3,	'1'),
(11,	'CrazyCSIW6',	14311450,	'SNAPSHOTS MOD',	3,	'1'),
(12,	'seabeds',	12572068,	'SNAPSHOTS MOD',	3,	'1'),
(13,	'TheEggo',	14125695,	'APP DEV',	9,	'1'),
(15,	'hap',	12433422,	'&#10084;',	4,	'0'),
(17,	'SimOFFICIAL',	15657428,	'SNAPSHOTS MOD',	3,	'1'),
(18,	'MegaMix_Craft',	18152711,	'CMT',	8,	'1'),
(19,	'Komm',	7671790,	'&#10084;',	4,	'0'),
(20,	'bentokage',	13175102,	'MOD',	2,	'1'),
(21,	'Badewanne3',	2211396,	'APP DEV',	9,	'0'),
(22,	'',	7279762,	'APP DEV',	9,	'1'),
(23,	'',	10504284,	'',	4,	'0'),
(25,	'',	17416390,	'',	4,	'0'),
(26,	'TacTic',	6291386,	'<3',	4,	'0'),
(27,	'',	11539225,	'<3',	5,	'0'),
(28,	'CBT',	13641450,	'<3',	4,	'0');

DROP TABLE IF EXISTS `SnapshotGroups`;
CREATE TABLE `SnapshotGroups` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text COLLATE utf32_bin NOT NULL,
  `description` text COLLATE utf32_bin NOT NULL,
  `image` text COLLATE utf32_bin NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin;


DROP TABLE IF EXISTS `SnapshotsAzeliaDownloads`;
CREATE TABLE `SnapshotsAzeliaDownloads` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ReferencedVersion` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Link` text NOT NULL,
  `Recommended` int(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `ReferencedVersion` (`ReferencedVersion`),
  CONSTRAINT `SnapshotsAzeliaDownloads_ibfk_1` FOREIGN KEY (`ReferencedVersion`) REFERENCES `SnapshotsAzeliaVersions` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `SnapshotsAzeliaScreenshots`;
CREATE TABLE `SnapshotsAzeliaScreenshots` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ReferencedVersion` int(11) NOT NULL,
  `Order` int(11) NOT NULL,
  `ImageLink` text NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Order` (`Order`),
  KEY `ReferencedVersion` (`ReferencedVersion`),
  CONSTRAINT `SnapshotsAzeliaScreenshots_ibfk_1` FOREIGN KEY (`ReferencedVersion`) REFERENCES `SnapshotsAzeliaVersions` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `SnapshotsAzeliaSubmissions`;
CREATE TABLE `SnapshotsAzeliaSubmissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `SnapshotsAzeliaVersions`;
CREATE TABLE `SnapshotsAzeliaVersions` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` text,
  `Title` text,
  `ReleaseDate` datetime DEFAULT NULL,
  `ArchivalDate` datetime DEFAULT NULL,
  `Archiver` text,
  `ArchiverID` int(11) DEFAULT NULL,
  `Description` text,
  `ExtraInfo` text,
  `Note` text,
  `AutoUpdates` int(1) DEFAULT NULL,
  `Video` text,
  `Views` int(11) DEFAULT NULL,
  `Downloads` int(11) DEFAULT NULL,
  `Group` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `SnapshotSubmissions`;
CREATE TABLE `SnapshotSubmissions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` text NOT NULL,
  `link` text NOT NULL,
  `info` text NOT NULL,
  `userid` int(11) NOT NULL,
  `date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` int(11) NOT NULL,
  `processing` int(11) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `SnapshotVersions`;
CREATE TABLE `SnapshotVersions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `json` text COLLATE utf32_bin NOT NULL,
  `views` int(11) NOT NULL,
  `downloads` int(11) NOT NULL,
  `release` timestamp NULL DEFAULT NULL,
  `archive_date` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf32 COLLATE=utf32_bin;


DROP TABLE IF EXISTS `Solutions`;
CREATE TABLE `Solutions` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `medalid` int(10) NOT NULL,
  `solution` varchar(500) COLLATE latin1_german1_ci NOT NULL,
  `submittedby` varchar(50) COLLATE latin1_german1_ci NOT NULL,
  `mods` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `medalid` (`medalid`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;

INSERT INTO `Solutions` (`id`, `medalid`, `solution`, `submittedby`, `mods`) VALUES
(1,	119,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'SD'),
(2,	6,	'FC any difficulty of the map \"Chatmonchy - Make Up! Make Up!\".',	'MegaMix_Craft',	''),
(3,	236,	'Get a score on any difficulty of every map in the beatmap pack \"*namirin\". You can use any mods including NF.',	'Hubz',	''),
(4,	4,	'Get at least 1.000 Combo on any beatmap with any mods.',	'bentokage',	''),
(5,	192,	'SS James Portland - Sky [Master].',	'bentokage',	''),
(6,	71,	'Pass any 1 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(7,	53,	'Reach at least rank 1.000 in the pp rankings.',	'mulraf',	NULL),
(8,	32,	'Reach 300.000 Drum Hits. Your profile on the osu! website will show you, how many drum hits you have so far.',	'mulraf',	NULL),
(9,	85,	'Pass any 7 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(10,	72,	'Pass any 2 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(11,	73,	'Pass any 3 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(12,	74,	'Pass any 4 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(13,	75,	'Pass any 5 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(14,	76,	'Pass any 6 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(15,	77,	'Pass any 7 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(16,	78,	'Pass any 8 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(17,	95,	'FC any 1 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(18,	96,	'FC any 2 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(19,	97,	'FC any 3 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(20,	98,	'FC any 4 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(21,	99,	'FC any 5 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(22,	100,	'FC any 6 star map (calculated with mods) without using EZ/NF/HT mods.',	'chromb',	''),
(23,	101,	'FC any 7 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(24,	102,	'FC any 8 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(25,	1,	'Get at least 500 Combo on any beatmap with any mods.',	'mulraf',	NULL),
(26,	3,	'Get at least 750 Combo on any beatmap with any mods.',	'mulraf',	NULL),
(27,	5,	'Get at least 2.000 Combo on any beatmap with any mods.',	'mulraf',	NULL),
(28,	55,	'Pass any 1 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(29,	56,	'Pass any 2 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(30,	57,	'Pass any 3 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(31,	58,	'Pass any 4 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(32,	59,	'Pass any 5 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(33,	60,	'Pass any 6 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(34,	61,	'Pass any 7 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(35,	62,	'Pass any 8 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(36,	63,	'FC any 1 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(37,	64,	'FC any 2 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(38,	65,	'FC any 3 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(39,	66,	'FC any 4 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(40,	67,	'FC any 5 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(41,	68,	'FC any 6 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(42,	69,	'FC any 7 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(43,	70,	'FC any 8 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(44,	50,	'Reach at least rank 50.000 in the pp rankings.',	'Remyria',	''),
(45,	51,	'Reach at least rank 10.000 in the pp rankings.',	'mulraf',	NULL),
(46,	52,	'Reach at least rank 5.000 in the pp rankings.',	'mulraf',	NULL),
(47,	87,	'Pass any 1 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(48,	88,	'Pass any 2 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(49,	89,	'Pass any 3 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(50,	90,	'Pass any 4 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(51,	91,	'Pass any 5 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(52,	92,	'Pass any 6 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(53,	93,	'Pass any 7 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(54,	94,	'Pass any 8 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(55,	111,	'FC any 1 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(56,	112,	'FC any 2 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(57,	113,	'FC any 3 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(58,	114,	'FC any 4 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(59,	115,	'FC any 5 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(60,	116,	'FC any 6 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(61,	117,	'FC any 7 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(62,	118,	'FC any 8 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(63,	79,	'Pass any 1 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(64,	80,	'Pass any 2 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(65,	81,	'Pass any 3 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(66,	82,	'Pass any 4 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(67,	83,	'Pass any 5 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(68,	84,	'Pass any 6 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(69,	86,	'Pass any 8 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(70,	103,	'FC any 1 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(71,	104,	'FC any 2 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(72,	105,	'FC any 3 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(73,	106,	'FC any 4 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(74,	107,	'FC any 5 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(75,	108,	'FC any 6 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(76,	109,	'FC any 7 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(77,	110,	'FC any 8 star map (calculated with mods) without using EZ/NF/HT mods.',	'mulraf',	NULL),
(78,	205,	'Play all the beatmaps in the beatmap pack. Any mode and mod-combination works.',	'MegaMix_Craft',	''),
(79,	207,	'Play all the beatmaps in the beatmap pack. Any mode and mod-combination works.',	'MegaMix_Craft',	''),
(80,	209,	'Play all the beatmaps in the beatmap pack. Any mode and mod-combination works.',	'MegaMix_Craft',	''),
(81,	210,	'Play all the beatmaps in the beatmap pack. Any mode and mod-combination works.',	'MegaMix_Craft',	''),
(82,	215,	'Play all the beatmaps in the beatmap pack. Any mode and mod-combination works.',	'MegaMix_Craft',	''),
(83,	228,	'Play all the beatmaps in the beatmap pack. Any mode and mod-combination works.',	'MegaMix_Craft',	''),
(84,	241,	'Play all the beatmaps in the beatmap pack. Any mode and mod-combination works.',	'MegaMix_Craft',	''),
(85,	120,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'PF'),
(86,	121,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'HR'),
(87,	122,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'DT'),
(88,	123,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'NC'),
(89,	124,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'HD'),
(90,	125,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'FL'),
(91,	126,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'EZ'),
(92,	127,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'NF'),
(93,	128,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'HT'),
(94,	131,	'Pass any map with the following mod. Can\'t use any other mods.',	'mulraf',	'SO'),
(95,	15,	'Get five S ranks in a row.<br><br>\n\nNote: S+ (with HD), SS, and SS+ (with HD) ranks also work.',	'MegaMix_Craft',	''),
(96,	16,	'On any mapset you have never played before, get a D rank with at least 100,000 score on first try (can use NF), and then S on the second try.',	'MegaMix_Craft',	''),
(97,	17,	'Pass Yoko Ishida - paraparaMAX I.',	'MegaMix_Craft',	''),
(98,	38,	'Get a D rank and more than 100,000 points. Using mods is not allowed.<br><br>\n\nNote: This medal is janky, sometimes doesn\'t work, nobody knows why',	'bentokage',	''),
(99,	39,	'Pass any approved map.',	'bentokage',	''),
(100,	40,	'Full combo any map with a B rank or lower.',	'bentokage',	''),
(101,	41,	'Complete a map with a score with all digits being the same and at least 111,111 (222,222 / 7,777,777 / 99,999,999). Using NF is allowed.',	'MegaMix_Craft',	''),
(102,	42,	'Be the first to submit a score on any ranked or qualified map.',	'MegaMix_Craft',	''),
(103,	43,	'Retry a map 100 times, and then pass it.\n\nNote: A score must have at least 10,000 score for it to count as a play.',	'Tanza3D',	''),
(104,	44,	'Full combo 10+ minutes (drain time, not including rest time) of any map.',	'MegaMix_Craft',	''),
(105,	45,	'Get 5000+ playcount in all gamemodes (standard/taiko/mania/catch the beat).',	'MegaMix_Craft',	''),
(106,	132,	'Pass any map that is 7+ minutes long.',	'bentokage',	''),
(107,	133,	'FC any map that is 7+ minutes long.',	'bentokage',	''),
(108,	134,	'Pass any map, using DT, that is 10 minutes or longer (after DT is applied). ',	'Coppertine',	'DT'),
(110,	135,	'Pass any map with AR9, using the mods above.',	'bentokage',	'HD,FL'),
(112,	136,	'Pass any map, using the mods above.',	'bentokage',	'HD,HT'),
(113,	137,	'Pass any \'nightcore\' map, using EITHER of the mods above.',	'bentokage',	'DT,NC'),
(114,	138,	'FC any map, using NF.',	'bentokage',	'NF'),
(115,	139,	'Pass any map, using the mods above.',	'bentokage',	'HD,FL'),
(116,	140,	'FC any 3*+ map, using the mods above.',	'bentokage',	'HR,SD'),
(117,	141,	'Pass any map without getting more than 200 combo, using FL.<br><br>\n\nNote: Using NF is allowed.',	'bentokage',	'FL'),
(118,	142,	'FC any map, using EZ, that is 4*+ after mods.',	'bentokage',	'EZ'),
(119,	143,	'FC a map with AR10+, OD10+ and HP10+.',	'bentokage',	''),
(120,	144,	'Pass any map, using the mods above.',	'bentokage',	'NC,FL'),
(121,	147,	'FC any map, using the mods above.',	'bentokage',	'HD,NF'),
(122,	148,	'FC any map, but miss the last note.',	'bentokage',	''),
(123,	149,	'FC any 3*+ map, using the mods above.',	'bentokage',	'EZ,HD,FL'),
(124,	145,	'Pass any map with AR11 OD11 HP11 and over 260BPM.<br><br>\n\nNote: To get this, the map must be AR8+, OD8+, HP8+, and 174+ BPM, and you must use DT+HR (+HD, optionally).',	'bentokage',	''),
(125,	146,	'FC any map with AR11 OD11 HP11 and over 260BPM.<br><br>\n\nNote: To get this, the map must be AR8+, OD8+, HP8+, and 174+ BPM, and you must use DT+HR (+HD, optionally).',	'bentokage',	''),
(126,	150,	'FC any difficulty of ginkiha - EOS.',	'bentokage',	''),
(127,	151,	'PFC any 3*+ map, using the mods above.',	'MegaMix_Craft',	'HT,PF'),
(128,	152,	'FC any map, using DT, that is 30 seconds or shorter (after mods).',	'MegaMix_Craft',	'DT'),
(129,	153,	'Pass LeaF - Evanescent with the mods above.<br><br>\n\nNotes: NF is allowed. You must get at least 10,000 score.',	'MegaMix_Craft',	'HD,HT'),
(130,	154,	'FC any difficulty of cYsmix - House With Legs, using the mods above.',	'MegaMix_Craft',	'DTHR'),
(131,	155,	'Pass LeaF - Evanescent with 90%+ accuracy.',	'MegaMix_Craft',	''),
(132,	156,	'Pass Traktion - The Near Distant Future with 85%+ accuracy.',	'MegaMix_Craft',	''),
(133,	157,	'Pass any map, using the mods above, that is 3*+ after mods.',	'MegaMix_Craft',	'EZ,PF'),
(134,	158,	'FC any map, using HR, that is CS7.8+ after mods. Using other mods is allowed.',	'MegaMix_Craft',	'HR'),
(135,	159,	'Get an equal amount of 300\'s, 100\'s, and 50\'s. You must have at least 15 of each.',	'MegaMix_Craft',	''),
(136,	160,	'Pass any 4*+ map, using the mods above.',	'MegaMix_Craft',	'DT,PF'),
(137,	168,	'Get 50 50\'s on any map.',	'bentokage',	''),
(138,	161,	'Get 1337 combo on any map.<br><br>\n\nNote: Not all maps work, we don\'t know why. It\'s best to just use one from the listed maps below.',	'bentokage',	''),
(139,	170,	'FC cYsmix - Classic Pursuit [Advanced], using DT, or [Normal], using DTHR.',	'bentokage',	'DT'),
(140,	171,	'Get 151 max combo and 95%+ accuracy on S3RL - Pika Girl.',	'Coppertine',	''),
(142,	173,	'Pass Helblinde - The Solace of Oblivion with 70%+ accuracy.',	'bentokage',	''),
(143,	174,	'Pass cYsmix - Moonlight Sonata [Normal], using the mods above.',	'bentokage',	'HD,DT,HR'),
(144,	175,	'Pass any difficulty of cYsmix - Fright March, with EITHER of the mods above. HD may be used.',	'bentokage',	'DTNC'),
(145,	176,	'Set a score in which your max combo is the same as the map\'s BPM (or half of it).',	'bentokage',	''),
(146,	177,	'Pass any map with <60% accuracy, using any difficulty increasing mod (HD, HR, DT, NC, and/or FL).\n<br><br>\nNote: There are some claims that <70% acc may also work, but this is untested and unconfirmed.',	'Tanza3D',	''),
(147,	178,	'Pass any map with a D rank, using FL.',	'bentokage',	'FL'),
(148,	193,	'Pass Cranky - T&J with 80%+ accuracy.',	'bentokage',	''),
(149,	194,	'Pass any difficulty of LukHash - GLITCH, using DT.',	'bentokage',	'DT'),
(150,	195,	'PFC Culprate - Relucent feat. ZES.',	'MegaMix_Craft',	''),
(151,	196,	'Pass Phonetic - Journey [Collab Insane], using the mods above.',	'bentokage',	'HD,FL'),
(152,	197,	'Pass any 4*+ difficulty Gourski x Himmes - Silence, using HD.',	'bentokage',	'HD'),
(153,	199,	'FC any map from 2011 or before, using the mods above, that is 4*+ after mods.<br><br>Note: \n\nThe map must be at least 3 minutes long after HT is applied.',	'bentokage',	'HD,HT,FL'),
(154,	200,	'Pass any 4*+ difficulty on Cranky - Hanaarashi, using HD.',	'bentokage',	'HD'),
(155,	201,	'Pass Culprate & Au5 - Impulse.',	'bentokage',	''),
(156,	202,	'Get exactly 34 misses on any difficulty of Function Phantom - Variable.',	'bentokage',	''),
(157,	204,	'Pass sakuraburst - cherry blossoms explode across the dying horizon.',	'bentokage',	''),
(158,	216,	'Pass tieff - Waterflow with with 85%+ accuracy, using DT.',	'bentokage',	'DT'),
(160,	218,	'FC any 4*+ difficulty on any map by song artist \"The Flashbulb\".',	'bentokage',	''),
(161,	219,	'FC any 4*+ difficulty on Cranky - Chandelier - King.',	'bentokage',	''),
(162,	220,	'Pass any 4*+ difficulty on antiPLUR - One Life Left to Live, using SD.',	'bentokage',	'SD'),
(163,	221,	'Pass Camellia - Exit This Earth\'s Atomosphere (Camellia\'s \'\'PLANETARY//200STEP\'\' Remix) with 70%+ accuracy.\n',	'bentokage',	''),
(164,	222,	'FC any 5*+ map with a play in which your 300 count is a multiple of 20.',	'bentokage',	''),
(165,	223,	'Pass The Flashbulb - DIDJ PVC [EX III] with 80%+ accuracy, using the mods above.',	'bentokage',	'EZ,HT'),
(166,	224,	'Pass one difficulty on each of the listed beatmaps.<br><br>\n\nNote: Using NF is allowed.',	'bentokage',	''),
(167,	225,	'Pass a_hisa - Alexithymia | Lupinus | Tokei no Heya to Seishin Sekai with 90%+ acc.',	'mulraf',	NULL),
(168,	54,	'Pass any mania map with over 100 combo.',	'chromb',	''),
(169,	31,	'Reach 30.000 Drum Hits. Your profile on the osu! website will show you, how many drum hits you have so far.',	'mulraf',	NULL),
(170,	33,	'Reach 3.000.000 Drum Hits. Your profile on the osu! website will show you, how many drum hits you have so far.',	'mulraf',	NULL),
(171,	20,	'Reach 5,000 plays. Your profile on the osu! website will show you, how many plays you have so far.',	'Coppertine',	''),
(172,	21,	'Reach 15.000 plays. Your profile on the osu! website will show you, how many plays you have so far.',	'mulraf',	NULL),
(173,	22,	'Reach 25.000 plays. Your profile on the osu! website will show you, how many plays you have so far.',	'mulraf',	NULL),
(174,	28,	'Reach 50.000. Your profile on the osu! website will show you, how many plays you have so far.',	'mulraf',	NULL),
(175,	46,	'Reach 40.000 key hits. Your profile on the osu! website will show you, how many keys you hit so far.',	'mulraf',	NULL),
(176,	47,	'Reach 400.000 key hits. Your profile on the osu! website will show you, how many keys you hit so far.',	'mulraf',	NULL),
(177,	48,	'Reach 4.000.000 key hits. Your profile on the osu! website will show you, how many keys you hit so far.',	'mulraf',	NULL),
(178,	13,	'Catch 20.000 fruits. Your profile on the osu! website will show you, how many fruits you catched so far.',	'mulraf',	NULL),
(179,	23,	'Catch 200.000 fruits. Your profile on the osu! website will show you, how many fruits you catched so far.',	'mulraf',	NULL),
(180,	24,	'Catch 2.000.000 fruits. Your profile on the osu! website will show you, how many fruits you catched so far.',	'mulraf',	NULL),
(181,	7,	'Get a score on any difficulty of every map in the beatmap pack \"Video Game Pack vol. 1\". You can use any mods including NF.',	'Hubz',	''),
(182,	8,	'Get a score on any difficulty of every map in the beatmap pack \"Rhythm Game Pack vol. 1\". You can use any mods including NF.',	'Hubz',	''),
(183,	9,	'Get a score on any difficulty of every map in the beatmap pack \"Internet! Pack vol. 1\" lmao. You can use any mods including NF.',	'Hubz',	''),
(184,	18,	'Get a score on any difficulty of every map in the beatmap pack \"Internet! Pack vol. 2\" roflcopter. You can use any mods including NF.',	'Hubz',	''),
(185,	27,	'Get a score on any difficulty of every map in the beatmap pack \"Internet! Pack vol. 3\" XD. You can use any mods including NF.',	'Hubz',	''),
(186,	36,	'Get a score on any difficulty of every map in the beatmap pack \"Internet! Pack vol. 4\" Holy Guacamole. You can use any mods including NF.',	'Hubz',	''),
(187,	10,	'Get a score on any difficulty of every map in the beatmap pack \"Anime Pack vol. 1\" uwu. You can use any mods including NF.',	'Tanza3D',	''),
(188,	11,	'Get a score on any difficulty of every map in the beatmap pack \"Video Game Pack vol. 2\". You can use any mods including NF.',	'MegaMix_Craft',	''),
(189,	12,	'Get a score on any difficulty of every map in the beatmap pack \"Anime Pack vol. 2\" <...<. You can use any mods including NF.',	'Hubz',	''),
(190,	14,	'Get a score on any difficulty of every map in the beatmap pack \"Video Game Pack vol. 3\". You can use any mods including NF.',	'Hubz',	''),
(191,	19,	'Get a score on any difficulty of every map in the beatmap pack \"Rhythm Game Pack vol. 2\". You can use any mods including NF.',	'Hubz',	''),
(192,	25,	'Get a score on any difficulty of every map in the beatmap pack \"Anime Pack vol. 3\" ^_^. You can use any mods including NF.',	'Hubz',	''),
(193,	26,	'Get a score on any difficulty of every map in the beatmap pack \"Rhythm Game Pack vol. 3\". You can use any mods including NF.',	'Hubz',	''),
(194,	34,	'Get a score on any difficulty of every map in the beatmap pack \"Anime Pack vol. 4\" OwO. You can use any mods including NF.',	'Hubz',	''),
(195,	35,	'Get a score on any difficulty of every map in the beatmap pack \"Rhythm Game Pack vol. 4\". You can use any mods including NF.',	'Hubz',	''),
(196,	37,	'Get a score on any difficulty of every map in the beatmap pack \"Video Game Pack vol. 4\". You can use any mods including NF.',	'Hubz',	''),
(197,	179,	'Get a score on any difficulty of every map in the beatmap pack \"MOtOLOiD\". You can use any mods including NF.',	'Hubz',	''),
(198,	185,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers Guild Pack I\". You can use any mods including NF.',	'Hubz',	''),
(199,	189,	'Get a score on any difficulty of every map in the beatmap pack \"Cranky\". You can use any mods including NF.',	'Hubz',	''),
(200,	190,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers Guild Pack II\". You can use any mods including NF.',	'Hubz',	''),
(201,	191,	'Get a score on any difficulty of every map in the beatmap pack \"High Tea Music\". You can use any mods including NF.',	'Hubz',	''),
(202,	206,	'Get a score on any difficulty of every map in the beatmap pack \"Culprate\". You can use any mods including NF.',	'Hubz',	''),
(203,	208,	'Get a score on any difficulty of every map in the beatmap pack \"HyuN\". You can use any mods including NF.',	'Hubz',	''),
(204,	214,	'Get a score on any difficulty of every map in the beatmap pack \"tieff\". You can use any mods including NF.',	'Hubz',	''),
(205,	226,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers Guild Pack III\". You can use any mods including NF.',	'Hubz',	''),
(206,	227,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers Guild Pack IV\". You can use any mods including NF.',	'Hubz',	''),
(207,	229,	'Get a score on any difficulty of every map in the beatmap pack \"Afterparty\". You can use any mods including NF.',	'Hubz',	''),
(208,	230,	'Get a score on any difficulty of every map in the beatmap pack \"Ben Briggs\". You can use any mods including NF.',	'Hubz',	''),
(209,	231,	'Get a score on any difficulty of every map in the beatmap pack \"Carpool Tunnel\". You can use any mods including NF.',	'Hubz',	''),
(210,	232,	'Get a score on any difficulty of every map in the beatmap pack \"Creo\". You can use any mods including NF.',	'Hubz',	''),
(211,	233,	'Get a score on any difficulty of every map in the beatmap pack \"cYsmix\". You can use any mods including NF.',	'Hubz',	''),
(212,	234,	'Get a score on any difficulty of every map in the beatmap pack \"Fractal Dreamers\". You can use any mods including NF.',	'Hubz',	''),
(213,	235,	'Get a score on any difficulty of every map in the beatmap pack \"LukHash\". You can use any mods including NF.',	'MegaMix_Craft',	''),
(214,	237,	'Get a score on any difficulty of every map in the beatmap pack \"onumi\". You can use any mods including NF.',	'Hubz',	''),
(215,	238,	'Get a score on any difficulty of every map in the beatmap pack \"The Flashbulb\". You can use any mods including NF.',	'Hubz',	''),
(216,	239,	'Get a score on any difficulty of every map in the beatmap pack \"Undead Corporation\". You can use any mods including NF.',	'Hubz',	''),
(217,	240,	'Get a score on any difficulty of every map in the beatmap pack \"Wisp X\". You can use any mods including NF.',	'Hubz',	''),
(218,	172,	'FC any 4*+ map, using the mods above.',	'bentokage',	'HD,FL'),
(219,	217,	'PFC onumi - ARROGANCE [Hard] or higher with the mods above.',	'MegaMix_Craft',	'FL,HD'),
(220,	243,	'FC any 9 star map (calculated with mods) without using EZ/NF/HT mods. ',	'mulraf',	NULL),
(221,	245,	'FC any 10 star map (calculated with mods) without using EZ/NF/HT mods. ',	'mulraf',	NULL),
(222,	242,	'Pass any 9 star map (calculated with mods) without using EZ/NF/HT mods. ',	'mulraf',	NULL),
(223,	244,	'Pass any 10 star map (calculated with mods) without using EZ/NF/HT mods.',	'Tanza3D',	''),
(224,	246,	'Get a score on any difficulty of every map in the beatmap pack \"Camellia Sets Pack\". You can use any mods including NF.',	'Hubz',	''),
(225,	247,	'Get a score on any difficulty of every map in the beatmap pack \"Camellia Challenges Pack\". Difficulty reducing mods are not allowed for this pack!',	'Hubz',	''),
(226,	248,	'Get a score on any difficulty of every map in the beatmap pack \"Celldweller\". You can use any mods including NF.',	'Hubz',	''),
(227,	249,	'Get a score on any difficulty of every map in the beatmap pack \"Cranky II\". You can use any mods including NF.',	'Hubz',	''),
(228,	265,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers\' Guild Pack VII\". Difficulty reducing mods are not allowed for this pack!',	'MegaMix_Craft',	''),
(229,	266,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers\' Guild Pack VIII\". Difficulty reducing mods are not allowed for this pack! uwu',	'Tanza3D',	''),
(230,	267,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers\' Guild Pack IX\". Difficulty reducing mods are not allowed for this pack!',	'MegaMix_Craft',	''),
(231,	263,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers Guild Pack V\". You can use any mods including NF.',	'Hubz',	''),
(232,	264,	'Get a score on any difficulty of every map in the beatmap pack \"Mappers Guild Pack VI\". You can use any mods including NF.',	'Hubz',	''),
(233,	213,	'Get a score on any difficulty of every map in the beatmap pack \"Imperial Circus Dead Decadence\". You can use any mods including NF.',	'Hubz',	''),
(234,	250,	'Get a score on any difficulty of every map in the beatmap pack \"Cute Anime Girls\". You can use any mods including NF.',	'Hubz',	''),
(235,	251,	'Get a score on any difficulty of every map in the beatmap pack \"ELFENSJoN\". You can use any mods including NF.',	'Hubz',	''),
(236,	252,	'Get a score on any difficulty of every map in the beatmap pack \"Hyper Potions\". You can use any mods including NF.',	'Hubz',	''),
(237,	253,	'Get a score on any difficulty of every map in the beatmap pack \"Kola Kid\". You can use any mods including NF.',	'Hubz',	''),
(238,	254,	'Get a score on any difficulty of every map in the beatmap pack \"LeaF\". You can use any mods including NF.',	'Hubz',	''),
(239,	255,	'Get a score on any difficulty of every map in the beatmap pack \"Panda Eyes\". You can use any mods including NF.',	'Hubz',	''),
(240,	256,	'Get a score on any difficulty of every map in the beatmap pack \"PUP\". You can use any mods including NF.',	'Hubz',	''),
(241,	257,	'Get a score on any difficulty of every map in the beatmap pack \"Ricky Montgomery\". You can use any mods including NF.',	'Hubz',	''),
(242,	258,	'Get a score on any difficulty of every map in the beatmap pack \"Rin\". You can use any mods including NF.',	'Hubz',	''),
(243,	259,	'Get a score on any difficulty of every map in the beatmap pack \"S3RL\". You can use any mods including NF.',	'Hubz',	''),
(244,	260,	'Get a score on any difficulty of every map in the beatmap pack \"Sound Souler\". You can use any mods including NF.',	'Hubz',	''),
(245,	261,	'Get a score on any difficulty of every map in the beatmap pack \"Teminite\". You can use any mods including NF.',	'Hubz',	''),
(246,	262,	'Get a score on any difficulty of every map in the beatmap pack \"VINXIS\". You can use any mods including NF.',	'Hubz',	''),
(250,	268,	'Pass a 10+ minute map, then pass map shorter than 1 minute.<br><br>\n\nNote: DT is allowed, but the map must be over 10 minutes / under 1 minute AFTER DT.',	'mulraf',	''),
(251,	269,	'FC 2 3*+ maps in a row with SD or PF mod.',	'mulraf',	'PFSD'),
(252,	270,	'Fail any difficulty of Thaehan - Never Give Up five times, and then pass it.<br><br>\n\nNotes: Using NF is allowed for the pass. Failed plays must have at least 10,000 (or 100,000, we\'re unsure) score in order to count.',	'Tanza3D',	''),
(253,	271,	'Pass EVERY difficulty of Thank You Scientist - Mr. Invisible using HD.<br><br>\n\nNote: DT is allowed.',	'mulraf',	'HD'),
(256,	275,	'Set two scores on Carpool Tunnel - Better Now in a row.<br><br>\n\nNote: You must use the same mods on both scores, if you are using any. NF is allowed.',	'mulraf',	''),
(259,	274,	'Get the same accuracy on two different difficulties of Zekk - Duplication.\n\nNote: \n1. Plays must be on same mapset and same gamemode.\n2. This can only be done on the beatmaps to the right. No other ranked beatmps of the same artist and title give the medal.',	'Tanza3D',	''),
(260,	273,	'Submit a score on Kola Kid - timer on the first minute of any hour (ex: 5:00pm, 6:00pm). Seconds do not matter.<br><br>\n\nNote: The play must END at this time, so make sure to time this properly! For example, a DT play will need to be started in the first half of XX:59, NOT at XX:00.',	'mulraf',	''),
(261,	272,	'Pass any map that you have 100+ playcount on and have not passed yet.<br><br>\n\nNote: Due to some coding weirdness, if you play a map you have 100+ playcount on in a converted gamemode you have not yet passed it on (e.x. a standard map on taiko), it will count.',	'bentokage',	''),
(262,	276,	'FC any 3*+ map that has an equal amount of circles and sliders.',	'bentokage',	''),
(263,	279,	'Get 1xMiss and 99%+ accuracy on ARForest - Regret.',	'bentokage',	''),
(265,	281,	'Pass meganeko - Feral [Veracious] with 98%+ accuracy.',	'mulraf',	''),
(267,	280,	'Pass Yooh - RPG [Divinity] with 92%+ accuracy.',	'mulraf',	''),
(268,	278,	'Pass Lily - Scarlet Rose [0108 style] with 92%+ accuracy, using EZ.',	'bentokage',	'EZ'),
(269,	277,	'FC any map, using the mods above, that is 4*+ after mods.',	'bentokage',	'EZHDHTFL'),
(296,	0,	'chromb',	'Hubz',	'SDPF'),
(310,	187,	'',	'MegaMix_Craft',	''),
(313,	188,	'',	'MegaMix_Craft',	''),
(315,	186,	'',	'MegaMix_Craft',	''),
(316,	184,	'',	'MegaMix_Craft',	''),
(317,	183,	'',	'MegaMix_Craft',	''),
(318,	182,	'',	'MegaMix_Craft',	''),
(319,	181,	'',	'MegaMix_Craft',	''),
(320,	180,	'',	'MegaMix_Craft',	''),
(321,	169,	'',	'MegaMix_Craft',	''),
(322,	167,	'',	'MegaMix_Craft',	''),
(323,	166,	'',	'MegaMix_Craft',	''),
(324,	165,	'',	'mulraf',	''),
(325,	164,	'',	'MegaMix_Craft',	''),
(326,	163,	'',	'MegaMix_Craft',	''),
(327,	162,	'',	'MegaMix_Craft',	''),
(341,	283,	'',	'Tanza3D',	''),
(342,	282,	'',	'Tanza3D',	''),
(343,	284,	'guess',	'Tanza3D',	''),
(349,	285,	'FC any map, with HT, that is 6*+ after mods.<br><br>\n\nNote: Map can be either Ranked or Loved. Using additional mods (e.x. HR) is allowed.',	'MegaMix_Craft',	'HT'),
(351,	286,	'Pass the top difficulty of LeaF - MEPHISTO, using FL.<br><br>\n\nNote: Even though this mapset has maps in all 4 gamemodes, the top difficulty for every gamemode won\'t give you a medal. You\'ll have to play the top difficulty for standard ([Extra]) as a converted map in any gamemode to get the medal.',	'MegaMix_Craft',	'FL'),
(352,	287,	'Pass any map by song artist \"xi\" with X7.27% accuracy (97.27%, 87.27%, etc.).\n\nNote: All mods are allowed. Note that there is also an artist named \"Xi\", with a capital X -- that is a different person and their maps do not count for this medal.',	'Tanza3D',	''),
(353,	290,	'Get a score on any difficulty of every map in the beatmap pack \"Aitsuki Nakuru Pack\". You can use any mods including NF.',	'Tanza3D',	''),
(354,	289,	'Get a score on any difficulty of every map in the beatmap pack \"Maduk Pack\". You can use any mods including NF.',	'Tanza3D',	''),
(355,	288,	'Get a score on any difficulty of every map in the beatmap pack \"Vocaloid Pack\". You can use any mods including NF.',	'Tanza3D',	''),
(435,	299,	'Pass a Ranked, Qualified or Loved map that you have published or made a Guest Difficulty yourself.\n\nNote: The guest difficulty must have the mapper stated on the beatmap page.',	'MegaMix_Craft',	''),
(439,	297,	'Pass any difficulty of the ranked mapsets of \nFleshgod Apocalypse - The Deceit and Fleshgod Apocalypse - The Violation',	'Tanza3D',	''),
(441,	298,	'PFC Maduk ft. Veela - \nGhost Assassin (Hourglass Bonusmix) [Lumiere] with the mods above.',	'MegaMix_Craft',	'SD,HD'),
(450,	296,	'Get a score on any difficulty of every map in the beatmap pack \"Chill Pack\". You can use any mods including NF.',	'MegaMix_Craft',	''),
(451,	295,	'Get a score on any difficulty of every map in the beatmap pack \"Omoi Pack\". You can use any mods including NF.',	'MegaMix_Craft',	''),
(452,	294,	'Don\'t guess',	'MegaMix_Craft',	''),
(463,	292,	'Catch 20.000.000 fruits. Your profile on the osu! website will show you, how many fruits you catched so far.',	'MegaMix_Craft',	''),
(464,	293,	'Reach 40.000.000 key hits. Your profile on the osu! website will show you, how many keys you hit so far.',	'MegaMix_Craft',	''),
(465,	291,	'Reach 30.000.000 Drum Hits. Your profile on the osu! website will show you, how many drum hits you have so far.\n\n<img src=\"https://c.tenor.com/W-Vpc3IOfgYAAAAC/just-play-more-your-bad.gif\">',	'Tanza3D',	'');

DROP TABLE IF EXISTS `StatsPageViews`;
CREATE TABLE `StatsPageViews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `date` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  `location` text NOT NULL,
  `app` text NULL,
  `query` text NULL,
  `url` text NULL,
  `generation_time` float NOT NULL,
  `ip_grab_time` float NOT NULL,
  `devicetype` text NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Timeline`;
CREATE TABLE `Timeline` (
  `ID` int(20) NOT NULL AUTO_INCREMENT,
  `UserID` int(20) NOT NULL,
  `Date` date NOT NULL,
  `Note` varchar(500) NOT NULL,
  `Mode` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `UserID` (`UserID`,`Date`,`Note`,`Mode`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `Tools`;
CREATE TABLE `Tools` (
  `Key` varchar(50) NOT NULL DEFAULT 'AUTO_INCREMENT',
  `Name` text DEFAULT NULL,
  `Creators` text DEFAULT NULL,
  `Description` text DEFAULT NULL,
  `Featured` int(1) DEFAULT 0,
  PRIMARY KEY (`Key`) USING BTREE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;


INSERT INTO `Tools` (`Key`, `Name`, `Creators`, `Description`, `Featured`) VALUES
	('comparer', 'Comparer', '[0]', 'temporary', 0);
INSERT INTO `Tools` (`Key`, `Name`, `Creators`, `Description`, `Featured`) VALUES
	('medal-name-quiz', 'Medal Name Quiz', '[0]', NULL, 0);
INSERT INTO `Tools` (`Key`, `Name`, `Creators`, `Description`, `Featured`) VALUES
	('medal-percentage-calc', 'Medal Percentage Calculator', '[18152711]', 'Quickly calculate how many medals you need to get to a certain percentage, or how many medals a certain percentage is. Based on current medal count.', 0);
INSERT INTO `Tools` (`Key`, `Name`, `Creators`, `Description`, `Featured`) VALUES
	('pp-calc', 'Play PP Calculator', '[13581430,21203707]', 'Calculate your plays across all gamemodes, quick and easy, completely locally!', 1);
INSERT INTO `Tools` (`Key`, `Name`, `Creators`, `Description`, `Featured`) VALUES
	('stdev-pp-calc', 'Standard Deviation PP Calculator', '[0]', 'Calculate how much standard deviated pp you have, either based on your profile’s statistics, or manually inputed values.', 0);

DROP TABLE IF EXISTS `TPPListing`;
CREATE TABLE `TPPListing` (`name` text, `totalpp` varchar(47), `ProfileID` int(11), `standardpp` varchar(47), `maniapp` varchar(47), `ctbpp` varchar(47), `taikopp` varchar(47), `name_long` varchar(30), `flag` varchar(70));


DROP TABLE IF EXISTS `Translators`;
CREATE TABLE `Translators` (
  `Id` int(11) NOT NULL,
  `LanguageCode` tinytext NOT NULL,
  `Role` text NOT NULL,
  `Native` int(11) NOT NULL DEFAULT '1',
  `Username` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP TABLE IF EXISTS `UserListing`;
CREATE TABLE `UserListing` (`id` int(11), `name` text, `Medalcount` int(11), `Completion` varchar(58), `rarest_medal` varchar(50), `link` varchar(70), `flag` varchar(70), `name_long` varchar(30));


DROP TABLE IF EXISTS `Votes`;
CREATE TABLE `Votes` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `UserID` int(20) NOT NULL,
  `ObjectID` int(20) NOT NULL,
  `Vote` tinyint(1) NOT NULL,
  `Type` int(11) NOT NULL DEFAULT '0',
  `PollID` varchar(50) COLLATE latin1_german1_ci DEFAULT NULL,
  PRIMARY KEY (`ID`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;


DROP TABLE IF EXISTS `BadgeListing`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `BadgeListing` AS select `Ranking`.`name` AS `name`,`Ranking`.`badge_count` AS `badge_count`,`Ranking`.`total_pp` AS `total_pp`,`Ranking`.`id` AS `ProfileID`,`Ranking`.`standard_pp` AS `standard_pp`,`Ranking`.`mania_pp` AS `mania_pp`,`Ranking`.`ctb_pp` AS `ctb_pp`,`Ranking`.`taiko_pp` AS `taiko_pp`,`Countries`.`name_long` AS `name_long`,`Countries`.`link` AS `flag` from (`Ranking` join `Countries` on((`Ranking`.`country_code` = `Countries`.`name_short`))) order by `Ranking`.`badge_count` desc limit 1000;

DROP TABLE IF EXISTS `MedalListing`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `MedalListing` AS select `Medals`.`medalid` AS `MedalID`,`Medals`.`name` AS `Name`,`Medals`.`description` AS `Description`,concat(format(`MedalRarity`.`frequency`,2),'%') AS `PossessionRate`,`Medals`.`link` AS `link`,`Medals`.`restriction` AS `restriction`,`Medals`.`grouping` AS `grouping` from (`Medals` left join `MedalRarity` on((`Medals`.`medalid` = `MedalRarity`.`id`))) group by `Medals`.`medalid` order by (`PossessionRate` + 0) limit 1000;

DROP TABLE IF EXISTS `TPPListing`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `TPPListing` AS select `Ranking`.`name` AS `name`,format(`Ranking`.`total_pp`,0) AS `totalpp`,`Ranking`.`id` AS `ProfileID`,format(`Ranking`.`standard_pp`,0) AS `standardpp`,format(`Ranking`.`mania_pp`,0) AS `maniapp`,format(`Ranking`.`ctb_pp`,0) AS `ctbpp`,format(`Ranking`.`taiko_pp`,0) AS `taikopp`,`Countries`.`name_long` AS `name_long`,`Countries`.`link` AS `flag` from (`Ranking` join `Countries` on((`Ranking`.`country_code` = `Countries`.`name_short`))) order by `Ranking`.`total_pp` desc limit 1000;

DROP TABLE IF EXISTS `UserListing`;
CREATE ALGORITHM=UNDEFINED SQL SECURITY INVOKER VIEW `UserListing` AS select `Ranking`.`id` AS `id`,`Ranking`.`name` AS `name`,`Ranking`.`medal_count` AS `Medalcount`,format(((`Ranking`.`medal_count` * 100) / (select count(0) from `Medals`)),2) AS `Completion`,`Medals`.`name` AS `rarest_medal`,`Medals`.`link` AS `link`,`Countries`.`link` AS `flag`,`Countries`.`name_long` AS `name_long` from (((`Ranking` join `Medals` on((`Ranking`.`rarest_medal` = `Medals`.`medalid`))) join `MedalRarity` on((`Medals`.`medalid` = `MedalRarity`.`id`))) join `Countries` on((`Ranking`.`country_code` = `Countries`.`name_short`))) order by `Ranking`.`medal_count` desc,`MedalRarity`.`frequency` limit 1000;

-- 2022-11-27 11:58:31

/* 2022-11-27 add clickable alerts */
ALTER TABLE `Alerts`
ADD `Link` text COLLATE 'utf8mb4_bin' NOT NULL;

/* 2022-12-10 add changelog */
DROP TABLE IF EXISTS `ChangelogEntries`;
CREATE TABLE `ChangelogEntries` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ChangelogId` int(11) NOT NULL,
  `Name` text COLLATE utf8mb4_bin NOT NULL,
  `Tags` json NOT NULL,
  `User` text COLLATE utf8mb4_bin NOT NULL,
  `Link` text COLLATE utf8mb4_bin NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

TRUNCATE `ChangelogEntries`;
INSERT INTO `ChangelogEntries` (`Id`, `ChangelogId`, `Name`, `Tags`, `User`, `Link`) VALUES
(1,	1,	'Addition Test for 2022-12-09',	'[\"Backend\", \"size/XS\", \"changelog:Addition\"]',	'Tanza3D',	'https://github.com/Osekai/osekai/pull/101'),
(2,	1,	'Change test for 2022-12-09',	'[\"Code\", \"Design\", \"changelog:Change\"]',	'Tanza3D',	'https://github.com/Osekai/osekai/pull/101'),
(3,	1,	'Removal test for 2022-12-09',	'[\"Bug\", \"Design\", \"Code\", \"changelog:Removal\"]',	'Someone Else',	'https://github.com/Osekai/osekai/pull/101'),
(4,	2,	'Addition test for 2022-12-10',	'[\"Enhancement\", \"Documentation\", \"changelog:Addition\"]',	'jiniux',	'https://github.com/Osekai/osekai/pull/101'),
(5,	2,	'Change test for 2022-12-10',	'[\"changelog:Change\", \"Code\", \"Design\"]',	'EXtremeExploit',	'https://github.com/Osekai/osekai/pull/101'),
(6,	2,	'Removal test for 2022-12-10',	'[\"Enhancement\", \"Bug\", \"changelog:Removal\"]',	'Tanza3D',	'https://github.com/Osekai/osekai/pull/101');

DROP TABLE IF EXISTS `Changelogs`;
CREATE TABLE `Changelogs` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Name` int(11) NOT NULL,
  `Date` date NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_bin;

TRUNCATE `Changelogs`;
INSERT INTO `Changelogs` (`Id`, `Name`, `Date`) VALUES
(1,	20221209,	'2022-12-09'),
(2,	20221210,	'2022-12-10');

DROP TABLE IF EXISTS `Reports`;
CREATE TABLE `Reports` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `ReporterId` int(11) NOT NULL,
  `Type` int(11) NOT NULL,
  `Status` int(11) NOT NULL,
  `Text` text NOT NULL,
  `Link` text NOT NULL,
  `ReferenceId` int(11) NOT NULL,
  `Date` datetime NOT NULL,
  PRIMARY KEY (`Id`)
);

DROP TABLE IF EXISTS `FavouriteMedals`;
CREATE TABLE `FavouriteMedals` (
  `user_id` int(11) NOT NULL,
  `medal_id` int(11) NOT NULL,
  PRIMARY KEY (`user_id`,`medal_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_german1_ci;