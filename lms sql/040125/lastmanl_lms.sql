-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 05, 2025 at 08:44 PM
-- Server version: 10.6.20-MariaDB-cll-lve-log
-- PHP Version: 8.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `lastmanl_lms`
--
CREATE DATABASE IF NOT EXISTS `lastmanl_lms` DEFAULT CHARACTER SET utf8mb3 COLLATE utf8mb3_general_ci;
USE `lastmanl_lms`;

-- --------------------------------------------------------

--
-- Table structure for table `lms_available_picks`
--

CREATE TABLE `lms_available_picks` (
  `lms_available_picks_player_id` int(11) NOT NULL,
  `lms_available_picks_game` int(11) NOT NULL,
  `lms_available_picks_team` int(11) NOT NULL,
  `lms_available_picks_count` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_calendar`
--

CREATE TABLE `lms_calendar` (
  `lms_calendar_id` int(11) NOT NULL,
  `lms_calendar_name` varchar(45) NOT NULL,
  `lms_calendar_season` int(11) NOT NULL,
  `lms_calendar_current_week` int(11) NOT NULL DEFAULT 0,
  `lms_calendar_select_week` int(11) NOT NULL DEFAULT 0,
  `lms_calendar_api_season` varchar(4) NOT NULL DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

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
  `lms_game_code` varchar(6) NOT NULL,
  `lms_game_calendar` int(11) NOT NULL,
  `lms_game_pick_count` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_game_league`
--

CREATE TABLE `lms_game_league` (
  `lms_game_league_game_id` int(11) NOT NULL,
  `lms_game_league_league_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_game_outcome`
--

CREATE TABLE `lms_game_outcome` (
  `lml_game_outcome_id` int(11) NOT NULL,
  `lml_game_outcome_text` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_game_player`
--

CREATE TABLE `lms_game_player` (
  `lms_game_id` int(11) NOT NULL,
  `lms_player_id` int(11) NOT NULL,
  `lms_game_player_status` int(1) NOT NULL DEFAULT 1,
  `lms_game_player_elimination_week` varchar(6) DEFAULT '',
  `lms_game_player_outcome` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_game_player_status`
--

CREATE TABLE `lms_game_player_status` (
  `lms_game_player_status_id` int(11) NOT NULL,
  `lms_game_player_status_text` varchar(20) NOT NULL DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_game_status`
--

CREATE TABLE `lms_game_status` (
  `lms_game_status_id` int(11) NOT NULL,
  `lms_game_status_text` varchar(20) NOT NULL DEFAULT 'unknown'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_info`
--

CREATE TABLE `lms_info` (
  `lms_info_id` varchar(24) NOT NULL,
  `lms_info_value` varchar(256) DEFAULT '',
  `lms_info_enc` tinyint(4) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_league`
--

CREATE TABLE `lms_league` (
  `lms_league_id` int(11) NOT NULL,
  `lms_league_name` varchar(255) NOT NULL DEFAULT '',
  `lms_league_abbr` varchar(4) NOT NULL DEFAULT '',
  `lms_league_supported` tinyint(1) DEFAULT 0,
  `lms_league_api_id` int(11) NOT NULL DEFAULT 0,
  `lms_league_current_calendar` int(11) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_league_team`
--

CREATE TABLE `lms_league_team` (
  `lms_league_team_league_id` int(11) NOT NULL,
  `lms_league_team_team_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

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
  `lms_match_ha` char(1) NOT NULL DEFAULT '',
  `lms_match_calendar` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_pick`
--

CREATE TABLE `lms_pick` (
  `lms_pick_player_id` int(11) NOT NULL,
  `lms_pick_game_id` int(11) NOT NULL,
  `lms_pick_match_id` int(11) NOT NULL,
  `lms_pick_wl` char(1) NOT NULL DEFAULT ''
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

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
  `lms_player_created` datetime DEFAULT NULL,
  `lms_player_mobile` varchar(45) DEFAULT '0',
  `lms_player_email_verified` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_player_temp_password`
--

CREATE TABLE `lms_player_temp_password` (
  `lms_player_id` int(11) NOT NULL,
  `lms_player_temp_password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_results`
--

CREATE TABLE `lms_results` (
  `lms_match_date` datetime NOT NULL,
  `lms_match_team` int(11) NOT NULL,
  `lms_match_team_score` int(11) DEFAULT NULL,
  `lms_match_team_wl` char(1) DEFAULT NULL,
  `lms_match_status` varchar(4) DEFAULT ''
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_result_type`
--

CREATE TABLE `lms_result_type` (
  `lms_result_type` varchar(1) NOT NULL DEFAULT 'n',
  `lms_result_type_desc` varchar(45) NOT NULL DEFAULT 'not played',
  `lms_result_type_wl` varchar(1) NOT NULL DEFAULT '-',
  `lms_result_type_noresult` tinyint(4) NOT NULL DEFAULT 0
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_team`
--

CREATE TABLE `lms_team` (
  `lms_team_id` int(11) NOT NULL,
  `lms_team_name` varchar(45) NOT NULL,
  `lms_team_active` tinyint(1) DEFAULT 1,
  `lms_team_wins` int(2) DEFAULT 0,
  `lms_team_abbr` char(3) NOT NULL DEFAULT '',
  `lms_team_api_id` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_team_abbr`
--

CREATE TABLE `lms_team_abbr` (
  `lms_team_abbr_abbr` varchar(3) NOT NULL,
  `lms_team_abbr_team_id` int(11) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_verify`
--

CREATE TABLE `lms_verify` (
  `lms_verify_code` varchar(50) NOT NULL,
  `lms_verify_player` int(11) NOT NULL,
  `lms_verify_email` varchar(100) NOT NULL DEFAULT '',
  `lms_verify_date` datetime NOT NULL,
  `lms_verify_ok` tinyint(4) NOT NULL DEFAULT 0,
  `lms_create_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lms_week`
--

CREATE TABLE `lms_week` (
  `lms_week_no` varchar(6) NOT NULL,
  `lms_week_calendar` int(11) NOT NULL DEFAULT 0,
  `lms_week` int(2) NOT NULL,
  `lms_year` int(4) NOT NULL,
  `lms_week_start` datetime NOT NULL,
  `lms_week_end` datetime NOT NULL,
  `lms_week_deadline` datetime NOT NULL,
  `lms_week_state` int(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_league_teams`
-- (See below for the actual view)
--
CREATE TABLE `v_league_teams` (
`lms_league_id` int(11)
,`lms_league_name` varchar(255)
,`lms_league_abbr` varchar(4)
,`lms_league_supported` tinyint(1)
,`lms_league_api_id` int(11)
,`lms_league_current_calendar` int(11)
,`lms_team_id` int(11)
,`lms_team_name` varchar(45)
,`lms_team_active` tinyint(1)
,`lms_team_abbr` char(3)
,`lms_team_api_id` int(11)
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_available_picks`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_available_picks` (
`lms_available_picks_player_id` int(11)
,`lms_available_picks_game` int(11)
,`lms_available_picks_team` int(11)
,`lms_available_picks_count` int(11)
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
,`lms_game_calendar` int(11)
,`lms_game_pick_count` int(11)
,`lms_player_screen_name` varchar(100)
,`lms_week` int(2)
,`lms_year` int(4)
,`lms_week_start` datetime
,`lms_game_status_text` varchar(20)
,`lms_calendar_season` int(11)
,`lms_calendar_current_week` int(11)
,`lms_calendar_select_week` int(11)
,`lms_select_deadline` datetime
);

-- --------------------------------------------------------

--
-- Stand-in structure for view `v_lms_league_calendar`
-- (See below for the actual view)
--
CREATE TABLE `v_lms_league_calendar` (
`lms_league_id` int(11)
,`lms_league_name` varchar(255)
,`lms_league_abbr` varchar(4)
,`lms_league_supported` tinyint(1)
,`lms_league_api_id` int(11)
,`lms_league_current_calendar` int(11)
,`lms_calendar_id` int(11)
,`lms_calendar_name` varchar(45)
,`lms_calendar_season` int(11)
,`lms_calendar_current_week` int(11)
,`lms_calendar_select_week` int(11)
,`lms_calendar_api_season` varchar(4)
,`lms_select_deadline` datetime
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
,`lms_match_calendar` int(11)
,`lms_week` int(2)
,`lms_year` int(4)
,`lms_week_calendar` int(11)
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
,`lms_game_player_elimination_week` varchar(6)
,`lml_game_outcome_text` varchar(45)
,`lms_game_name` varchar(45)
,`lms_game_start_wkno` varchar(6)
,`lms_game_status` int(1)
,`lms_game_total_players` int(11)
,`lms_game_still_active` int(11)
,`lms_game_week_count` int(11)
,`lms_game_code` varchar(6)
,`lms_game_calendar` int(11)
,`lms_player_screen_name` varchar(100)
,`lms_active` tinyint(1)
,`lms_game_player_status_text` varchar(20)
,`lms_game_status_text` varchar(20)
,`lms_week` int(2)
,`lms_year` int(4)
,`lms_calendar_season` int(11)
,`lms_calendar_current_week` int(11)
,`lms_calendar_select_week` int(11)
,`lms_select_deadline` datetime
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
,`lms_match_ha` char(1)
,`lms_match_league` int(11)
,`lms_team_id` int(11)
,`lms_team_name` varchar(45)
,`lms_game_status` int(1)
,`lms_game_status_text` varchar(20)
,`lms_game_calendar` int(11)
,`lms_week` int(2)
,`lms_year` int(4)
,`lms_match_date` datetime
,`lms_match_result` char(1)
,`lms_match_opp` varchar(45)
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
,`lms_match_calendar` int(11)
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
,`home_result_type` varchar(45)
,`no_result` tinyint(4)
,`match_status` varchar(4)
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
-- Structure for view `v_league_teams`
--
DROP TABLE IF EXISTS `v_league_teams`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`80.7.51.177` SQL SECURITY DEFINER VIEW `v_league_teams`  AS SELECT `l`.`lms_league_id` AS `lms_league_id`, `l`.`lms_league_name` AS `lms_league_name`, `l`.`lms_league_abbr` AS `lms_league_abbr`, `l`.`lms_league_supported` AS `lms_league_supported`, `l`.`lms_league_api_id` AS `lms_league_api_id`, `l`.`lms_league_current_calendar` AS `lms_league_current_calendar`, `t`.`lms_team_id` AS `lms_team_id`, `t`.`lms_team_name` AS `lms_team_name`, `t`.`lms_team_active` AS `lms_team_active`, `t`.`lms_team_abbr` AS `lms_team_abbr`, `t`.`lms_team_api_id` AS `lms_team_api_id` FROM ((`lms_league` `l` join `lms_league_team` `lt` on(`l`.`lms_league_id` = `lt`.`lms_league_team_league_id`)) join `lms_team` `t` on(`lt`.`lms_league_team_team_id` = `t`.`lms_team_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_available_picks`
--
DROP TABLE IF EXISTS `v_lms_available_picks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`80.7.51.177` SQL SECURITY DEFINER VIEW `v_lms_available_picks`  AS SELECT `a`.`lms_available_picks_player_id` AS `lms_available_picks_player_id`, `a`.`lms_available_picks_game` AS `lms_available_picks_game`, `a`.`lms_available_picks_team` AS `lms_available_picks_team`, `a`.`lms_available_picks_count` AS `lms_available_picks_count`, `t`.`lms_team_name` AS `lms_team_name` FROM (`lms_available_picks` `a` join `lms_team` `t` on(`a`.`lms_available_picks_team` = `t`.`lms_team_id`)) ;

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

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`80.7.51.177` SQL SECURITY DEFINER VIEW `v_lms_game`  AS SELECT `g`.`lms_game_id` AS `lms_game_id`, `g`.`lms_game_start_wkno` AS `lms_game_start_wkno`, `g`.`lms_game_name` AS `lms_game_name`, `g`.`lms_game_status` AS `lms_game_status`, `g`.`lms_game_week_count` AS `lms_game_week_count`, `g`.`lms_game_total_players` AS `lms_game_total_players`, `g`.`lms_game_still_active` AS `lms_game_still_active`, `g`.`lms_game_manager` AS `lms_game_manager`, `g`.`lms_game_code` AS `lms_game_code`, `g`.`lms_game_calendar` AS `lms_game_calendar`, `g`.`lms_game_pick_count` AS `lms_game_pick_count`, `p`.`lms_player_screen_name` AS `lms_player_screen_name`, `w`.`lms_week` AS `lms_week`, `w`.`lms_year` AS `lms_year`, `w`.`lms_week_start` AS `lms_week_start`, `gl`.`lms_game_status_text` AS `lms_game_status_text`, `c`.`lms_calendar_season` AS `lms_calendar_season`, `c`.`lms_calendar_current_week` AS `lms_calendar_current_week`, `c`.`lms_calendar_select_week` AS `lms_calendar_select_week`, `s`.`lms_week_deadline` AS `lms_select_deadline` FROM (((((`lms_game` `g` join `lms_player` `p` on(`g`.`lms_game_manager` = `p`.`lms_player_id`)) join `lms_week` `w` on(`w`.`lms_week_no` = `g`.`lms_game_start_wkno` and `w`.`lms_week_calendar` = `g`.`lms_game_calendar`)) join `lms_game_status` `gl` on(`g`.`lms_game_status` = `gl`.`lms_game_status_id`)) join `lms_calendar` `c` on(`g`.`lms_game_calendar` = `c`.`lms_calendar_id`)) left join `lms_week` `s` on(`s`.`lms_week` = `c`.`lms_calendar_select_week` and `s`.`lms_week_calendar` = `c`.`lms_calendar_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_league_calendar`
--
DROP TABLE IF EXISTS `v_lms_league_calendar`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`82.9.248.136` SQL SECURITY DEFINER VIEW `v_lms_league_calendar`  AS SELECT `l`.`lms_league_id` AS `lms_league_id`, `l`.`lms_league_name` AS `lms_league_name`, `l`.`lms_league_abbr` AS `lms_league_abbr`, `l`.`lms_league_supported` AS `lms_league_supported`, `l`.`lms_league_api_id` AS `lms_league_api_id`, `l`.`lms_league_current_calendar` AS `lms_league_current_calendar`, `c`.`lms_calendar_id` AS `lms_calendar_id`, `c`.`lms_calendar_name` AS `lms_calendar_name`, `c`.`lms_calendar_season` AS `lms_calendar_season`, `c`.`lms_calendar_current_week` AS `lms_calendar_current_week`, `c`.`lms_calendar_select_week` AS `lms_calendar_select_week`, `c`.`lms_calendar_api_season` AS `lms_calendar_api_season`, `s`.`lms_week_deadline` AS `lms_select_deadline` FROM ((`lms_league` `l` join `lms_calendar` `c` on(`l`.`lms_league_current_calendar` = `c`.`lms_calendar_id`)) left join `lms_week` `s` on(`s`.`lms_week` = `c`.`lms_calendar_select_week` and `s`.`lms_week_calendar` = `c`.`lms_calendar_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_match`
--
DROP TABLE IF EXISTS `v_lms_match`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`82.9.248.136` SQL SECURITY DEFINER VIEW `v_lms_match`  AS SELECT `m`.`lms_match_id` AS `lms_match_id`, `m`.`lms_match_weekno` AS `lms_match_weekno`, `m`.`lms_match_ha` AS `lms_match_ha`, `m`.`lms_match_calendar` AS `lms_match_calendar`, `w`.`lms_week` AS `lms_week`, `w`.`lms_year` AS `lms_year`, `w`.`lms_week_calendar` AS `lms_week_calendar`, `m`.`lms_match_team` AS `lms_match_team`, `m`.`lms_match_date` AS `lms_match_date`, `m`.`lms_match_result` AS `lms_match_result`, `t`.`lms_team_name` AS `lms_team_name`, `o`.`lms_team_name` AS `lms_match_opp`, `w`.`lms_week_start` AS `lms_week_start` FROM (((`lms_match` `m` join `lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lms_week` `w` on(`m`.`lms_match_weekno` = `w`.`lms_week_no` and `m`.`lms_match_calendar` = `w`.`lms_week_calendar`)) join `lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_player_games`
--
DROP TABLE IF EXISTS `v_lms_player_games`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`82.9.248.136` SQL SECURITY DEFINER VIEW `v_lms_player_games`  AS SELECT `gp`.`lms_player_id` AS `lms_player_id`, `gp`.`lms_game_id` AS `lms_game_id`, `gp`.`lms_game_player_status` AS `lms_game_player_status`, `gp`.`lms_game_player_elimination_week` AS `lms_game_player_elimination_week`, `o`.`lml_game_outcome_text` AS `lml_game_outcome_text`, `g`.`lms_game_name` AS `lms_game_name`, `g`.`lms_game_start_wkno` AS `lms_game_start_wkno`, `g`.`lms_game_status` AS `lms_game_status`, `g`.`lms_game_total_players` AS `lms_game_total_players`, `g`.`lms_game_still_active` AS `lms_game_still_active`, `g`.`lms_game_week_count` AS `lms_game_week_count`, `g`.`lms_game_code` AS `lms_game_code`, `g`.`lms_game_calendar` AS `lms_game_calendar`, `p`.`lms_player_screen_name` AS `lms_player_screen_name`, `p`.`lms_active` AS `lms_active`, `pl`.`lms_game_player_status_text` AS `lms_game_player_status_text`, `gl`.`lms_game_status_text` AS `lms_game_status_text`, `w`.`lms_week` AS `lms_week`, `w`.`lms_year` AS `lms_year`, `c`.`lms_calendar_season` AS `lms_calendar_season`, `c`.`lms_calendar_current_week` AS `lms_calendar_current_week`, `c`.`lms_calendar_select_week` AS `lms_calendar_select_week`, `s`.`lms_week_deadline` AS `lms_select_deadline` FROM ((((((((`lms_game_player` `gp` join `lms_game` `g` on(`gp`.`lms_game_id` = `g`.`lms_game_id`)) join `lms_player` `p` on(`gp`.`lms_player_id` = `p`.`lms_player_id`)) join `lms_game_player_status` `pl` on(`gp`.`lms_game_player_status` = `pl`.`lms_game_player_status_id`)) join `lms_game_status` `gl` on(`g`.`lms_game_status` = `gl`.`lms_game_status_id`)) join `lms_week` `w` on(`w`.`lms_week_no` = `g`.`lms_game_start_wkno` and `w`.`lms_week_calendar` = `g`.`lms_game_calendar`)) join `lms_game_outcome` `o` on(`gp`.`lms_game_player_outcome` = `o`.`lml_game_outcome_id`)) join `lms_calendar` `c` on(`g`.`lms_game_calendar` = `c`.`lms_calendar_id`)) join `lms_week` `s` on(`s`.`lms_week` = `c`.`lms_calendar_select_week` and `s`.`lms_week_calendar` = `c`.`lms_calendar_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_player_picks`
--
DROP TABLE IF EXISTS `v_lms_player_picks`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`82.9.248.136` SQL SECURITY DEFINER VIEW `v_lms_player_picks`  AS SELECT `p`.`lms_pick_player_id` AS `lms_pick_player_id`, `p`.`lms_pick_game_id` AS `lms_pick_game_id`, `p`.`lms_pick_match_id` AS `lms_pick_match_id`, `p`.`lms_pick_wl` AS `lms_pick_wl`, `pl`.`lms_player_screen_name` AS `lms_player_screen_name`, `g`.`lms_game_name` AS `lms_game_name`, `m`.`lms_match_weekno` AS `lms_match_weekno`, `m`.`lms_match_ha` AS `lms_match_ha`, `m`.`lms_match_league` AS `lms_match_league`, `t`.`lms_team_id` AS `lms_team_id`, `t`.`lms_team_name` AS `lms_team_name`, `g`.`lms_game_status` AS `lms_game_status`, `g`.`lms_game_status_text` AS `lms_game_status_text`, `g`.`lms_game_calendar` AS `lms_game_calendar`, `w`.`lms_week` AS `lms_week`, `w`.`lms_year` AS `lms_year`, `m`.`lms_match_date` AS `lms_match_date`, `m`.`lms_match_result` AS `lms_match_result`, `o`.`lms_team_name` AS `lms_match_opp` FROM ((((((`lms_pick` `p` join `lms_player` `pl` on(`p`.`lms_pick_player_id` = `pl`.`lms_player_id`)) join `v_lms_game` `g` on(`p`.`lms_pick_game_id` = `g`.`lms_game_id`)) join `lms_match` `m` on(`p`.`lms_pick_match_id` = `m`.`lms_match_id`)) join `lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lms_week` `w` on(`w`.`lms_week_no` = `m`.`lms_match_weekno` and `g`.`lms_game_calendar` = `w`.`lms_week_calendar`)) join `lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`)) ;

-- --------------------------------------------------------

--
-- Structure for view `v_lms_results`
--
DROP TABLE IF EXISTS `v_lms_results`;

CREATE ALGORITHM=UNDEFINED DEFINER=`lastmanl_lmsadmin`@`80.7.51.177` SQL SECURITY DEFINER VIEW `v_lms_results`  AS SELECT `m`.`lms_match_id` AS `lms_match_id`, `m`.`lms_match_weekno` AS `lms_match_weekno`, `m`.`lms_match_date` AS `lms_match_date`, `m`.`lms_match_league` AS `lms_match_league`, `m`.`lms_match_ha` AS `lms_match_ha`, `m`.`lms_match_calendar` AS `lms_match_calendar`, `t`.`lms_team_id` AS `home_team_id`, `t`.`lms_team_name` AS `home_team_name`, `t`.`lms_team_abbr` AS `home_team_abbr`, `o`.`lms_team_id` AS `away_team_id`, `o`.`lms_team_name` AS `away_team_name`, `o`.`lms_team_abbr` AS `away_team_abbr`, `rh`.`lms_match_team_score` AS `home_score`, `ra`.`lms_match_team_score` AS `away_score`, `rh`.`lms_match_team_wl` AS `home_result`, `ra`.`lms_match_team_wl` AS `away_result`, `rt`.`lms_result_type_desc` AS `home_result_type`, `rt`.`lms_result_type_noresult` AS `no_result`, `rh`.`lms_match_status` AS `match_status` FROM (((((`lms_match` `m` join `lms_team` `t` on(`m`.`lms_match_team` = `t`.`lms_team_id`)) join `lms_team` `o` on(`m`.`lms_match_opp` = `o`.`lms_team_id`)) left join `lms_results` `rh` on(`m`.`lms_match_date` = `rh`.`lms_match_date` and `t`.`lms_team_id` = `rh`.`lms_match_team`)) left join `lms_results` `ra` on(`m`.`lms_match_date` = `ra`.`lms_match_date` and `o`.`lms_team_id` = `ra`.`lms_match_team`)) left join `lms_result_type` `rt` on(`rh`.`lms_match_team_wl` = `rt`.`lms_result_type`)) ORDER BY `m`.`lms_match_weekno` ASC, `m`.`lms_match_date` ASC, `t`.`lms_team_name` ASC ;

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
-- Indexes for table `lms_calendar`
--
ALTER TABLE `lms_calendar`
  ADD PRIMARY KEY (`lms_calendar_id`);

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
-- Indexes for table `lms_game_outcome`
--
ALTER TABLE `lms_game_outcome`
  ADD PRIMARY KEY (`lml_game_outcome_id`),
  ADD UNIQUE KEY `lml_game_outcome_id_UNIQUE` (`lml_game_outcome_id`);

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
-- Indexes for table `lms_result_type`
--
ALTER TABLE `lms_result_type`
  ADD PRIMARY KEY (`lms_result_type`);

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
-- Indexes for table `lms_verify`
--
ALTER TABLE `lms_verify`
  ADD PRIMARY KEY (`lms_verify_code`);

--
-- Indexes for table `lms_week`
--
ALTER TABLE `lms_week`
  ADD PRIMARY KEY (`lms_week_no`,`lms_week_calendar`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lms_game`
--
ALTER TABLE `lms_game`
  MODIFY `lms_game_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_league`
--
ALTER TABLE `lms_league`
  MODIFY `lms_league_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_match`
--
ALTER TABLE `lms_match`
  MODIFY `lms_match_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_player`
--
ALTER TABLE `lms_player`
  MODIFY `lms_player_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lms_team`
--
ALTER TABLE `lms_team`
  MODIFY `lms_team_id` int(11) NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `fk_lms_match_lms_team1` FOREIGN KEY (`lms_match_team`) REFERENCES `lms_team` (`lms_team_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Constraints for table `lms_pick`
--
ALTER TABLE `lms_pick`
  ADD CONSTRAINT `fk_lms_pick_game` FOREIGN KEY (`lms_pick_game_id`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_lms_pick_match` FOREIGN KEY (`lms_pick_match_id`) REFERENCES `lms_match` (`lms_match_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  ADD CONSTRAINT `fk_lms_pick_player` FOREIGN KEY (`lms_pick_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION;
