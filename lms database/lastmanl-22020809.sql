-- phpMyAdmin SQL Dump
-- version 4.9.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 09, 2022 at 12:31 PM
-- Server version: 10.3.35-MariaDB-log-cll-lve
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lastmanl_lms`
--
CREATE DATABASE IF NOT EXISTS `lastmanl_lms` DEFAULT CHARACTER SET utf8 COLLATE utf8_general_ci;
USE `lastmanl_lms`;

-- --------------------------------------------------------

--
-- Table structure for table `lms_available_picks`
--

CREATE TABLE `lms_available_picks` (
  `lms_available_picks_player_id` int(11) NOT NULL,
  `lms_available_picks_game` int(11) NOT NULL,
  `lms_available_picks_team` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `lms_game`
--

CREATE TABLE `lms_game` (
  `lms_game_id` int(11) NOT NULL,
  `lms_game_start_wkno` varchar(6) NOT NULL,
  `lms_game_name` varchar(45) NOT NULL,
  `lms_game_status` int(1) NOT NULL DEFAULT 1,
  `lms_game_week_count` int(11) NOT NULL DEFAULT 0,
  `lms_game_total_players` int(11) NOT NULL DEFAULT 0,
  `lms_game_still_active` int(11) NOT NULL DEFAULT 0,
  `lms_game_manager` int(11) NOT NULL,
  `lms_game_code` varchar(6) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------



--
-- Table structure for table `lms_game_player`
--

CREATE TABLE `lms_game_player` (
  `lms_game_id` int(11) NOT NULL,
  `lms_player_id` int(11) NOT NULL,
  `lms_game_player_status` int(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `lms_game_player_status`
--

CREATE TABLE `lms_game_player_status` (
  `lms_game_player_status_id` int(11) NOT NULL,
  `lms_game_player_status_text` varchar(20) NOT NULL DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lms_game_player_status`
--

INSERT INTO `lms_game_player_status` (`lms_game_player_status_id`, `lms_game_player_status_text`) VALUES
(1, 'active'),
(2, 'out'),
(3, 'left');

-- --------------------------------------------------------

--
-- Table structure for table `lms_game_status`
--

CREATE TABLE `lms_game_status` (
  `lms_game_status_id` int(11) NOT NULL,
  `lms_game_status_text` varchar(20) NOT NULL DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lms_game_status`
--

INSERT INTO `lms_game_status` (`lms_game_status_id`, `lms_game_status_text`) VALUES
(1, 'recruiting'),
(2, 'in-play'),
(3, 'complete'),
(4, 'cancelled');

-- --------------------------------------------------------

--
-- Table structure for table `lms_info`
--

CREATE TABLE `lms_info` (
  `lms_info_id` varchar(24) NOT NULL,
  `lms_info_value` varchar(256) DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lms_info`
--

INSERT INTO `lms_info` (`lms_info_id`, `lms_info_value`) VALUES
('admin_email_address', 'lmladmin@lastmanlive.co.uk'),
('create_email_subject', 'Last Man Live  account created'),
('currseason', '2223'),
('currweek', '01'),
('fixtures url epl', 'https://www.thesportsman.com/football/competitions/england/premier-league/fixtures'),
('fixtures url eu20', 'https://www.thesportsman.com/football/competitions/europe/european-championship'),
('fixtures url wc22', 'https://www.thesportsman.com/football/competitions/world/world-cup-qatar-2022'),
('invite_email_subject', 'Join my Last Man Live game '),
('lml_url', 'https://lastmanlive.co.uk'),
('match_week_complete', '202107'),
('results url epl', 'https://www.thesportsman.com/football/competitions/england/premier-league/results'),
('results url eu20', 'https://www.thesportsman.com/football/competitions/europe/european-championship/results'),
('selectweek', '02'),
('smtp_from_address', 'lmladmin@lastmanlive.co.uk'),
('smtp_from_name', 'Last Man Live'),
('smtp_host', 'mail.lastmanlive.co.uk'),
('smtp_port', '465'),
('smtp_pwd', ''),
('smtp_reply_address', 'lml-reply@lastmanlive.co.uk'),
('smtp_reply_name', 'LML Replies'),
('smtp_user', 'lmladmin@lastmanlive.co.uk');

-- --------------------------------------------------------

--
-- Table structure for table `lms_league`
--

CREATE TABLE `lms_league` (
  `lms_league_id` int(11) NOT NULL,
  `lms_league_name` varchar(255) NOT NULL DEFAULT '',
  `lms_league_abbr` varchar(4) NOT NULL DEFAULT '',
  `lms_league_supported` tinyint(1) DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lms_league`
--

INSERT INTO `lms_league` (`lms_league_id`, `lms_league_name`, `lms_league_abbr`, `lms_league_supported`) VALUES
(1, 'English Premier League', 'EPL', 1),
(2, 'English Championship', 'ECH', 0),
(3, 'English League One', 'EL1', 0),
(4, 'English League Two', 'EL2', 0),
(5, 'Scottish Premiership', 'SPL', 0),
(6, 'Scottish Championship', 'SCH', 0),
(7, 'Scottish League One', 'SL1', 0),
(8, 'Scottish League Two', 'SL2', 0),
(9, 'UEFA Champions League', 'UCL', 0),
(10, 'UEFA Europa League', 'UEL', 0),
(11, 'EURO 2020', 'EU20', 0),
(12, 'FA Cup', 'FACP', 0),
(13, 'EFL Cup', 'FLCP', 0),
(14, 'World Cup 2022', 'WC22', 0);

-- --------------------------------------------------------

--
-- Table structure for table `lms_league_team`
--

CREATE TABLE `lms_league_team` (
  `lms_league_team_league_id` int(11) NOT NULL,
  `lms_league_team_team_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data for table `lms_league_team`
--

INSERT INTO `lms_league_team` (`lms_league_team_league_id`, `lms_league_team_team_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),
(1, 6),
(1, 7),
(1, 8),
(1, 14),
(1, 15),
(1, 16),
(1, 17),
(1, 18),
(1, 19),
(1, 21),
(1, 23),
(1, 24),
(1, 25),
(1, 27),
(1, 28),
(1, 53),
(1, 54),
(2, 9),
(2, 20),
(2, 22),
(2, 26),
(14, 31),
(14, 32),
(14, 33),
(14, 35),
(14, 37),
(14, 38),
(14, 41),
(14, 45),
(14, 47),
(14, 50),
(14, 51),
(14, 52),
(14, 55),
(14, 56),
(14, 57),
(14, 58),
(14, 59),
(14, 60),
(14, 61),
(14, 62),
(14, 63),
(14, 64),
(14, 65),
(14, 66),
(14, 67),
(14, 68),
(14, 69),
(14, 70),
(14, 71),
(14, 72),
(14, 73),
(14, 74);

-- --------------------------------------------------------

--
-- Table structure for table `lms_match`
--

CREATE TABLE `lms_match` (
  `lms_match_id` int(11) NOT NULL,
  `lms_match_weekno` varchar(6) NOT NULL,
  `lms_match_team` int(11) NOT NULL,
  `lms_match_date` datetime NOT NULL,
  `lms_match_result` char(1) NOT NULL DEFAULT '',
  `lms_match_league` int(11) NOT NULL DEFAULT 0,
  `lms_match_opp` int(11) DEFAULT NULL,
  `lms_match_ha` char(1) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Table structure for table `lms_pick`
--

CREATE TABLE `lms_pick` (
  `lms_pick_player_id` int(11) NOT NULL,
  `lms_pick_game_id` int(11) NOT NULL,
  `lms_pick_match_id` int(11) NOT NULL,
  `lms_pick_wl` char(1) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


--
-- Table structure for table `lms_player`
--

CREATE TABLE `lms_player` (
  `lms_player_id` int(11) NOT NULL,
  `lms_player_login` varchar(255) NOT NULL,
  `lms_player_password` varchar(100) NOT NULL,
  `lms_player_forename` varchar(45) NOT NULL DEFAULT '',
  `lms_player_surname` varchar(45) NOT NULL DEFAULT '',
  `lms_player_screen_name` varchar(100) NOT NULL DEFAULT '',
  `lms_player_email` varchar(250) NOT NULL DEFAULT '',
  `lms_access` int(11) NOT NULL DEFAULT 0,
  `lms_active` tinyint(1) DEFAULT 1,
  `lms_player_send_email` tinyint(1) DEFAULT 1,
  `lms_player_created` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


-- --------------------------------------------------------

--
-- Table structure for table `lms_player_temp_password`
--

CREATE TABLE `lms_player_temp_password` (
  `lms_player_id` int(11) NOT NULL,
  `lms_player_temp_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `lms_results`
--

CREATE TABLE `lms_results` (
  `lms_match_date` datetime NOT NULL,
  `lms_match_team` int(11) NOT NULL,
  `lms_match_team_score` int(11) DEFAULT NULL,
  `lms_match_team_wl` char(1) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;



--
-- Table structure for table `lms_team`
--

CREATE TABLE `lms_team` (
  `lms_team_id` int(11) NOT NULL,
  `lms_team_name` varchar(45) NOT NULL,
  `lms_team_active` tinyint(1) DEFAULT 1,
  `lms_team_wins` int(2) DEFAULT 0,
  `lms_team_abbr` char(3) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lms_team`
--

INSERT INTO `lms_team` (`lms_team_id`, `lms_team_name`, `lms_team_active`, `lms_team_wins`, `lms_team_abbr`) VALUES
(1, 'Arsenal', 1, 0, 'ARS'),
(2, 'Manchester United', 1, 0, 'MUN'),
(3, 'Liverpool', 1, 0, 'LIV'),
(4, 'Manchester City', 1, 0, 'MCI'),
(5, 'Aston Villa', 1, 0, 'AVI'),
(6, 'Leicester City', 1, 0, 'LEI'),
(7, 'Chelsea', 1, 0, 'CHE'),
(8, 'Wolverhampton Wanderers', 1, 0, 'WOL'),
(9, 'Sheffield United', 0, 0, 'SHU'),
(14, 'Crystal Palace', 1, 0, 'CPA'),
(15, 'Tottenham Hotspur', 1, 0, 'TOT'),
(16, 'Everton', 1, 0, 'EVE'),
(17, 'Southampton', 1, 0, 'SOU'),
(18, 'Newcastle United', 1, 0, 'NEW'),
(19, 'Brighton & Hove Albion', 1, 0, 'BRI'),
(20, 'Burnley', 0, 0, 'BUR'),
(21, 'West Ham United', 1, 0, 'WHA'),
(22, 'Watford', 0, 0, 'WAT'),
(23, 'Bournemouth', 1, 0, 'BOU'),
(24, 'Norwich City', 0, 0, 'NCI'),
(25, 'Fulham', 1, 0, 'FUL'),
(26, 'West Brom', 0, 0, 'WBR'),
(27, 'Leeds United', 1, 0, 'LEE'),
(29, 'Turkey', 0, 0, 'TUR'),
(30, 'Italy', 0, 0, 'ITA'),
(31, 'Wales', 0, 0, 'WAL'),
(32, 'Switzerland', 0, 0, 'SWI'),
(33, 'Denmark', 0, 0, 'DEN'),
(34, 'Finland', 0, 0, 'FIN'),
(35, 'Belgium', 0, 0, 'BEL'),
(36, 'Russia', 0, 0, 'RUS'),
(37, 'England', 0, 0, 'ENG'),
(38, 'Croatia', 0, 0, 'CRO'),
(39, 'Austria', 0, 0, 'AUT'),
(40, 'North Macedonia', 0, 0, 'NMA'),
(41, 'Netherlands', 0, 0, 'NET'),
(42, 'Ukraine', 0, 0, 'UKR'),
(43, 'Scotland', 0, 0, 'SCO'),
(44, 'Czech Republic', 0, 0, 'CRE'),
(45, 'Poland', 0, 0, 'POL'),
(46, 'Slovakia', 0, 0, 'SLO'),
(47, 'Spain', 0, 0, 'SPA'),
(48, 'Sweden', 0, 0, 'SWE'),
(49, 'Hungary', 0, 0, 'HUN'),
(50, 'Portugal', 0, 0, 'POR'),
(51, 'France', 0, 0, 'FRA'),
(52, 'Germany', 0, 0, 'GER'),
(53, 'Brentford', 1, 0, 'BRE'),
(54, 'Nottingham Forrest', 1, 0, 'NFO'),
(55, 'Qatar', 1, 0, 'QAT'),
(56, 'Brazil', 1, 0, 'BRA'),
(57, 'Argentina', 1, 0, 'ARG'),
(58, 'Mexico', 1, 0, 'MEX'),
(59, 'Uruguay', 1, 0, 'URU'),
(60, 'USA', 1, 0, 'USA'),
(61, 'Senegal', 1, 0, 'SEN'),
(62, 'Iran', 1, 0, 'IRA'),
(63, 'Japan', 1, 0, 'JAP'),
(64, 'Morocco', 1, 0, 'MOR'),
(65, 'Serbia', 1, 0, 'SER'),
(66, 'South Korea', 1, 0, 'SKO'),
(67, 'Tunisia', 1, 0, 'TUN'),
(68, 'Cameroon', 1, 0, 'CAM'),
(69, 'Canada', 1, 0, 'CAN'),
(70, 'Ecuador', 1, 0, 'ECU'),
(71, 'Saudi Arabia', 1, 0, 'SAR'),
(72, 'Ghana', 1, 0, 'GHA'),
(73, 'Costa Rica', 1, 0, 'CRI'),
(74, 'Australia', 1, 0, 'AUS');

-- --------------------------------------------------------

--
-- Table structure for table `lms_team_abbr`
--

CREATE TABLE `lms_team_abbr` (
  `lms_team_abbr_abbr` varchar(3) NOT NULL,
  `lms_team_abbr_team_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Dumping data for table `lms_team_abbr`
--

INSERT INTO `lms_team_abbr` (`lms_team_abbr_abbr`, `lms_team_abbr_team_id`) VALUES
('ARS', 1),
('MUN', 2),
('LIV', 3),
('MCI', 4),
('AST', 5),
('AVI', 5),
('LEI', 6),
('LCI', 6),
('CHE', 7),
('WWA', 8),
('WOL', 8),
('SUN', 9),
('CRY', 14),
('CPA', 14),
('TOT', 15),
('THO', 15),
('EVE', 16),
('SOU', 17),
('NUN', 18),
('B&', 19),
('BUR', 20),
('WHA', 21),
('WAT', 22),
('BOU', 23),
('NCI', 24),
('FUL', 25),
('WBR', 26),
('LUN', 27),
('TUR', 29),
('ITA', 30),
('WAL', 31),
('SWI', 32),
('DEN', 33),
('FIN', 34),
('BEL', 35),
('RUS', 36),
('ENG', 37),
('CRO', 38),
('AUS', 74),
('NMA', 40),
('NET', 41),
('UKR', 42),
('SCO', 43),
('CRE', 44),
('POL', 45),
('SLO', 46),
('SPA', 47),
('SWE', 48),
('HUN', 49),
('POR', 50),
('FRA', 51),
('GER', 52),
('BRE', 53),
('BRI', 19),
('NOR', 24),
('LEE', 27),
('NEW', 18),
('SHU', 9),
('NFO', 54),
('BRA', 56),
('CAN', 69),
('ARG', 57),
('CAM', 68),
('CRI', 73),
('ECU', 70),
('GHA', 72),
('IRA', 62),
('JAP', 63),
('MEX', 58),
('MOR', 64),
('COS', 73),
('QAT', 55),
('SAR', 71),
('SAU', 71),
('SEN', 61),
('SER', 65),
('SKO', 66),
('TUN', 67),
('URU', 59),
('USA', 60),
('UST', 60),
('AUT', 39);

-- --------------------------------------------------------

--
-- Table structure for table `lms_week`
--

CREATE TABLE `lms_week` (
  `lms_week_no` varchar(6) NOT NULL,
  `lms_week` int(2) NOT NULL,
  `lms_year` int(4) NOT NULL,
  `lms_week_start` datetime NOT NULL,
  `lms_week_end` datetime NOT NULL,
  `lms_week_deadline` datetime NOT NULL,
  `lms_week_state` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8;



--
-- Stand-in structure for view `v_lms_available_picks`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_available_picks` (
`lms_available_picks_player_id` int(11)
,`lms_available_picks_game` int(11)
,`lms_available_picks_team` int(11)
,`lms_team_name` varchar(45)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_fixture`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_fixture` (
`lms_match_id` int(11)
,`lms_match_weekno` varchar(6)
,`lms_match_team` int(11)
,`lms_match_date` datetime
,`lms_match_result` char(1)
,`lms_match_league` int(11)
,`lms_match_opp` int(11)
,`lms_team_id` int(11)
,`lms_team_name` varchar(45)
,`lms_team_active` tinyint(1)
,`lms_team_wins` int(2)
,`lms_team_abbr` char(3)
,`lms_opp_abbr` char(3)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_game`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_game` (
`lms_game_id` int(11)
,`lms_game_start_wkno` varchar(6)
,`lms_game_name` varchar(45)
,`lms_game_status` int(1)
,`lms_game_week_count` int(11)
,`lms_game_total_players` int(11)
,`lms_game_still_active` int(11)
,`lms_game_manager` int(11)
,`lms_game_code` varchar(6)
,`lms_player_screen_name` varchar(100)
,`lms_week` int(2)
,`lms_year` int(4)
,`lms_week_start` datetime
,`lms_game_status_text` varchar(20)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_match`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_match` (
`lms_match_id` int(11)
,`lms_match_weekno` varchar(6)
,`lms_match_ha` char(1)
,`lms_week` int(2)
,`lms_year` int(4)
,`lms_match_team` int(11)
,`lms_match_date` datetime
,`lms_match_result` char(1)
,`lms_team_name` varchar(45)
,`lms_match_opp` varchar(45)
,`lms_week_start` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_player_games`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_player_games` (
`lms_player_id` int(11)
,`lms_game_id` int(11)
,`lms_game_player_status` int(1)
,`lms_game_name` varchar(45)
,`lms_game_start_wkno` varchar(6)
,`lms_game_status` int(1)
,`lms_game_total_players` int(11)
,`lms_game_still_active` int(11)
,`lms_game_week_count` int(11)
,`lms_player_screen_name` varchar(100)
,`lms_active` tinyint(1)
,`lms_game_player_status_text` varchar(20)
,`lms_game_status_text` varchar(20)
,`lms_week` int(2)
,`lms_year` int(4)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_player_picks`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_player_picks` (
`lms_pick_player_id` int(11)
,`lms_pick_game_id` int(11)
,`lms_pick_match_id` int(11)
,`lms_pick_wl` char(1)
,`lms_player_screen_name` varchar(100)
,`lms_game_name` varchar(45)
,`lms_match_weekno` varchar(6)
,`lms_team_id` int(11)
,`lms_team_name` varchar(45)
,`lms_game_status_text` varchar(20)
,`lms_week` int(2)
,`lms_year` int(4)
,`lms_match_date` datetime
,`lms_match_result` char(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_results`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_results` (
`lms_match_id` int(11)
,`lms_match_weekno` varchar(6)
,`lms_match_date` datetime
,`lms_match_league` int(11)
,`lms_match_ha` char(1)
,`home_team_id` int(11)
,`home_team_name` varchar(45)
,`home_team_abbr` char(3)
,`away_team_id` int(11)
,`away_team_name` varchar(45)
,`away_team_abbr` char(3)
,`home_score` int(11)
,`away_score` int(11)
,`home_result` char(1)
,`away_result` char(1)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_team_lookup`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_team_lookup` (
`lms_team_abbr_abbr` varchar(3)
,`lms_team_abbr_team_id` int(11)
,`lms_team_id` int(11)
,`lms_team_name` varchar(45)
,`lms_team_active` tinyint(1)
,`lms_team_wins` int(2)
,`lms_team_abbr` char(3)
);

-- --------------------------------------------------------

--
-- Structure for view `v_lms_available_picks`
--
DROP TABLE IF EXISTS `v_lms_available_picks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `v_lms_available_picks`  AS SELECT `a`.`lms_available_picks_player_id` AS `lms_available_picks_player_id`, `a`.`lms_available_picks_game` AS `lms_available_picks_game`, `a`.`lms_available_picks_team` AS `lms_available_picks_team`, `t`.`lms_team_name` AS `lms_team_name` FROM (`lms_available_picks` `a` join `lms_team` `t` on(`a`.`lms_available_picks_team` = `t`.`lms_team_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_fixture`
--
DROP TABLE IF EXISTS `v_lms_fixture`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `v_lms_fixture`  AS SELECT `m`.`lms_match_id` AS `lms_match_id`, `m`.`lms_match_weekno` AS `lms_match_weekno`, `m`.`lms_match_team` AS `lms_match_team`, `m`.`lms_match_date` AS `lms_match_date`, `m`.`lms_match_result` AS `lms_match_result`, `m`.`lms_match_league` AS `lms_match_league`, `m`.`lms_match_opp` AS `lms_match_opp`, `t`.`lms_team_id` AS `lms_team_id`, `t`.`lms_team_name` AS `lms_team_name`, `t`.`lms_team_active` AS `lms_team_active`, `t`.`lms_team_wins` AS `lms_team_wins`, `t`.`lms_team_abbr` AS `lms_team_abbr`, `o`.`lms_team_abbr` AS `lms_opp_abbr` FROM ((`lms_match` `m` join `lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) left join `lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_game`
--
DROP TABLE IF EXISTS `v_lms_game`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `v_lms_game`  AS SELECT `g`.`lms_game_id` AS `lms_game_id`, `g`.`lms_game_start_wkno` AS `lms_game_start_wkno`, `g`.`lms_game_name` AS `lms_game_name`, `g`.`lms_game_status` AS `lms_game_status`, `g`.`lms_game_week_count` AS `lms_game_week_count`, `g`.`lms_game_total_players` AS `lms_game_total_players`, `g`.`lms_game_still_active` AS `lms_game_still_active`, `g`.`lms_game_manager` AS `lms_game_manager`, `g`.`lms_game_code` AS `lms_game_code`, `p`.`lms_player_screen_name` AS `lms_player_screen_name`, `w`.`lms_week` AS `lms_week`, `w`.`lms_year` AS `lms_year`, `w`.`lms_week_start` AS `lms_week_start`, `gl`.`lms_game_status_text` AS `lms_game_status_text` FROM (((`lms_game` `g` join `lms_player` `p` on(`g`.`lms_game_manager` = `p`.`lms_player_id`)) join `lms_week` `w` on(`w`.`lms_week_no` = `g`.`lms_game_start_wkno`)) join `lms_game_status` `gl` on(`g`.`lms_game_status` = `gl`.`lms_game_status_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_match`
--
DROP TABLE IF EXISTS `v_lms_match`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`82.9.248.136` SQL SECURITY DEFINER VIEW `v_lms_match`  AS SELECT `m`.`lms_match_id` AS `lms_match_id`, `m`.`lms_match_weekno` AS `lms_match_weekno`, `m`.`lms_match_ha` AS `lms_match_ha`, `w`.`lms_week` AS `lms_week`, `w`.`lms_year` AS `lms_year`, `m`.`lms_match_team` AS `lms_match_team`, `m`.`lms_match_date` AS `lms_match_date`, `m`.`lms_match_result` AS `lms_match_result`, `t`.`lms_team_name` AS `lms_team_name`, `o`.`lms_team_name` AS `lms_match_opp`, `w`.`lms_week_start` AS `lms_week_start` FROM (((`lms_match` `m` join `lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lms_week` `w` on(`m`.`lms_match_weekno` = `w`.`lms_week_no`)) join `lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_player_games`
--
DROP TABLE IF EXISTS `v_lms_player_games`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `v_lms_player_games`  AS SELECT `gp`.`lms_player_id` AS `lms_player_id`, `gp`.`lms_game_id` AS `lms_game_id`, `gp`.`lms_game_player_status` AS `lms_game_player_status`, `g`.`lms_game_name` AS `lms_game_name`, `g`.`lms_game_start_wkno` AS `lms_game_start_wkno`, `g`.`lms_game_status` AS `lms_game_status`, `g`.`lms_game_total_players` AS `lms_game_total_players`, `g`.`lms_game_still_active` AS `lms_game_still_active`, `g`.`lms_game_week_count` AS `lms_game_week_count`, `p`.`lms_player_screen_name` AS `lms_player_screen_name`, `p`.`lms_active` AS `lms_active`, `pl`.`lms_game_player_status_text` AS `lms_game_player_status_text`, `gl`.`lms_game_status_text` AS `lms_game_status_text`, `w`.`lms_week` AS `lms_week`, `w`.`lms_year` AS `lms_year` FROM (((((`lms_game_player` `gp` join `lms_game` `g` on(`gp`.`lms_game_id` = `g`.`lms_game_id`)) join `lms_player` `p` on(`gp`.`lms_player_id` = `p`.`lms_player_id`)) join `lms_game_player_status` `pl` on(`gp`.`lms_game_player_status` = `pl`.`lms_game_player_status_id`)) join `lms_game_status` `gl` on(`g`.`lms_game_status` = `gl`.`lms_game_status_id`)) join `lms_week` `w` on(`w`.`lms_week_no` = `g`.`lms_game_start_wkno`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_player_picks`
--
DROP TABLE IF EXISTS `v_lms_player_picks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `v_lms_player_picks`  AS SELECT `p`.`lms_pick_player_id` AS `lms_pick_player_id`, `p`.`lms_pick_game_id` AS `lms_pick_game_id`, `p`.`lms_pick_match_id` AS `lms_pick_match_id`, `p`.`lms_pick_wl` AS `lms_pick_wl`, `pl`.`lms_player_screen_name` AS `lms_player_screen_name`, `g`.`lms_game_name` AS `lms_game_name`, `m`.`lms_match_weekno` AS `lms_match_weekno`, `t`.`lms_team_id` AS `lms_team_id`, `t`.`lms_team_name` AS `lms_team_name`, `g`.`lms_game_status_text` AS `lms_game_status_text`, `w`.`lms_week` AS `lms_week`, `w`.`lms_year` AS `lms_year`, `m`.`lms_match_date` AS `lms_match_date`, `m`.`lms_match_result` AS `lms_match_result` FROM (((((`lms_pick` `p` join `lms_player` `pl` on(`p`.`lms_pick_player_id` = `pl`.`lms_player_id`)) join `v_lms_game` `g` on(`p`.`lms_pick_game_id` = `g`.`lms_game_id`)) join `lms_match` `m` on(`p`.`lms_pick_match_id` = `m`.`lms_match_id`)) join `lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lms_week` `w` on(`w`.`lms_week_no` = `m`.`lms_match_weekno`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_results`
--
DROP TABLE IF EXISTS `v_lms_results`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`82.9.248.136` SQL SECURITY DEFINER VIEW `v_lms_results`  AS SELECT `m`.`lms_match_id` AS `lms_match_id`, `m`.`lms_match_weekno` AS `lms_match_weekno`, `m`.`lms_match_date` AS `lms_match_date`, `m`.`lms_match_league` AS `lms_match_league`, `m`.`lms_match_ha` AS `lms_match_ha`, `t`.`lms_team_id` AS `home_team_id`, `t`.`lms_team_name` AS `home_team_name`, `t`.`lms_team_abbr` AS `home_team_abbr`, `o`.`lms_team_id` AS `away_team_id`, `o`.`lms_team_name` AS `away_team_name`, `o`.`lms_team_abbr` AS `away_team_abbr`, `rh`.`lms_match_team_score` AS `home_score`, `ra`.`lms_match_team_score` AS `away_score`, `rh`.`lms_match_team_wl` AS `home_result`, `ra`.`lms_match_team_wl` AS `away_result` FROM ((((`lms_match` `m` join `lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`)) left join `lms_results` `rh` on(`m`.`lms_match_date` = `rh`.`lms_match_date` and `t`.`lms_team_id` = `rh`.`lms_match_team`)) left join `lms_results` `ra` on(`m`.`lms_match_date` = `ra`.`lms_match_date` and `o`.`lms_team_id` = `ra`.`lms_match_team`)) WHERE `m`.`lms_match_ha` = 'h' ORDER BY `m`.`lms_match_weekno` ASC, `m`.`lms_match_date` ASC, `t`.`lms_team_name` ASC ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_team_lookup`
--
DROP TABLE IF EXISTS `v_lms_team_lookup`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl`@`localhost` SQL SECURITY DEFINER VIEW `v_lms_team_lookup`  AS SELECT `a`.`lms_team_abbr_abbr` AS `lms_team_abbr_abbr`, `a`.`lms_team_abbr_team_id` AS `lms_team_abbr_team_id`, `t`.`lms_team_id` AS `lms_team_id`, `t`.`lms_team_name` AS `lms_team_name`, `t`.`lms_team_active` AS `lms_team_active`, `t`.`lms_team_wins` AS `lms_team_wins`, `t`.`lms_team_abbr` AS `lms_team_abbr` FROM (`lms_team_abbr` `a` join `lms_team` `t` on(`a`.`lms_team_abbr_team_id` = `t`.`lms_team_id`)) ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lms_available_picks`
--
ALTER TABLE `lms_available_picks`
  ADD PRIMARY KEY (`lms_available_picks_player_id`,`lms_available_picks_game`,`lms_available_picks_team`),
  ADD KEY `lms_game_id_idx` (`lms_available_picks_game`),
  ADD KEY `fk_lms_team_id_idx` (`lms_available_picks_team`);

--
-- Indexes for table `lms_game`
--
ALTER TABLE `lms_game`
  ADD PRIMARY KEY (`lms_game_id`),
  ADD UNIQUE KEY `lms_game_id_UNIQUE` (`lms_game_id`);

--
-- Indexes for table `lms_game_league`
--
ALTER TABLE `lms_game_league`
  ADD PRIMARY KEY (`lms_game_league_game_id`,`lms_game_league_league_id`);

--
-- Indexes for table `lms_game_player`
--
ALTER TABLE `lms_game_player`
  ADD PRIMARY KEY (`lms_game_id`,`lms_player_id`),
  ADD KEY `lms_player_id_idx` (`lms_player_id`);

--
-- Indexes for table `lms_game_player_status`
--
ALTER TABLE `lms_game_player_status`
  ADD PRIMARY KEY (`lms_game_player_status_id`),
  ADD UNIQUE KEY `lms_game_player_status_id_UNIQUE` (`lms_game_player_status_id`);

--
-- Indexes for table `lms_game_status`
--
ALTER TABLE `lms_game_status`
  ADD PRIMARY KEY (`lms_game_status_id`),
  ADD UNIQUE KEY `lms_game_status_id_UNIQUE` (`lms_game_status_id`);

--
-- Indexes for table `lms_info`
--
ALTER TABLE `lms_info`
  ADD PRIMARY KEY (`lms_info_id`);

--
-- Indexes for table `lms_league`
--
ALTER TABLE `lms_league`
  ADD PRIMARY KEY (`lms_league_id`),
  ADD UNIQUE KEY `lms_league_id_UNIQUE` (`lms_league_id`);

--
-- Indexes for table `lms_league_team`
--
ALTER TABLE `lms_league_team`
  ADD PRIMARY KEY (`lms_league_team_league_id`,`lms_league_team_team_id`);

--
-- Indexes for table `lms_match`
--
ALTER TABLE `lms_match`
  ADD PRIMARY KEY (`lms_match_id`),
  ADD KEY `fk_weekno_idx` (`lms_match_weekno`),
  ADD KEY `fk_team_idx` (`lms_match_team`);

--
-- Indexes for table `lms_pick`
--
ALTER TABLE `lms_pick`
  ADD PRIMARY KEY (`lms_pick_player_id`,`lms_pick_game_id`,`lms_pick_match_id`),
  ADD KEY `fk_lms_game_id_idx` (`lms_pick_game_id`),
  ADD KEY `fk_lms_match_idx` (`lms_pick_match_id`);

--
-- Indexes for table `lms_player`
--
ALTER TABLE `lms_player`
  ADD PRIMARY KEY (`lms_player_id`),
  ADD UNIQUE KEY `lms_player_id_UNIQUE` (`lms_player_id`);

--
-- Indexes for table `lms_player_temp_password`
--
ALTER TABLE `lms_player_temp_password`
  ADD PRIMARY KEY (`lms_player_id`),
  ADD UNIQUE KEY `idlms_player_id_UNIQUE` (`lms_player_id`);

--
-- Indexes for table `lms_results`
--
ALTER TABLE `lms_results`
  ADD PRIMARY KEY (`lms_match_date`,`lms_match_team`);

--
-- Indexes for table `lms_team`
--
ALTER TABLE `lms_team`
  ADD PRIMARY KEY (`lms_team_id`);

--
-- Indexes for table `lms_team_abbr`
--
ALTER TABLE `lms_team_abbr`
  ADD PRIMARY KEY (`lms_team_abbr_abbr`);

--
-- Indexes for table `lms_week`
--
ALTER TABLE `lms_week`
  ADD PRIMARY KEY (`lms_week_no`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lms_game`
--
ALTER TABLE `lms_game`
  MODIFY `lms_game_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `lms_league`
--
ALTER TABLE `lms_league`
  MODIFY `lms_league_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `lms_match`
--
ALTER TABLE `lms_match`
  MODIFY `lms_match_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9924;

--
-- AUTO_INCREMENT for table `lms_player`
--
ALTER TABLE `lms_player`
  MODIFY `lms_player_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=400;

--
-- AUTO_INCREMENT for table `lms_team`
--
ALTER TABLE `lms_team`
  MODIFY `lms_team_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=75;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `lms_available_picks`
--
ALTER TABLE `lms_available_picks`
  ADD CONSTRAINT `fk_lms_available_picks_lms_team1` FOREIGN KEY (`lms_available_picks_team`) REFERENCES `lms_team` (`lms_team_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_lms_avpick_game` FOREIGN KEY (`lms_available_picks_game`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_lms_avpick_player` FOREIGN KEY (`lms_available_picks_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `lms_game_player`
--
ALTER TABLE `lms_game_player`
  ADD CONSTRAINT `fk_lms_game_player_game` FOREIGN KEY (`lms_game_id`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_lms_game_player_player` FOREIGN KEY (`lms_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `lms_match`
--
ALTER TABLE `lms_match`
  ADD CONSTRAINT `fk_lms_match_lms_team1` FOREIGN KEY (`lms_match_team`) REFERENCES `lms_team` (`lms_team_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_match_weekno` FOREIGN KEY (`lms_match_weekno`) REFERENCES `lms_week` (`lms_week_no`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `lms_pick`
--
ALTER TABLE `lms_pick`
  ADD CONSTRAINT `fk_lms_pick_game` FOREIGN KEY (`lms_pick_game_id`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_lms_pick_match` FOREIGN KEY (`lms_pick_match_id`) REFERENCES `lms_match` (`lms_match_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_lms_pick_player` FOREIGN KEY (`lms_pick_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
