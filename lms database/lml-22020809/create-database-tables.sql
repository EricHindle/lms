CREATE TABLE `lms_available_picks` (
  `lms_available_picks_player_id` int(11) NOT NULL,
  `lms_available_picks_game` int(11) NOT NULL,
  `lms_available_picks_team` int(11) NOT NULL,
  PRIMARY KEY (`lms_available_picks_player_id`,`lms_available_picks_game`,`lms_available_picks_team`),
  KEY `lms_game_id_idx` (`lms_available_picks_game`),
  KEY `fk_lms_team_id_idx` (`lms_available_picks_team`),
  CONSTRAINT `fk_lms_available_picks_lms_team1` FOREIGN KEY (`lms_available_picks_team`) REFERENCES `lms_team` (`lms_team_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lms_avpick_game` FOREIGN KEY (`lms_available_picks_game`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lms_avpick_player` FOREIGN KEY (`lms_available_picks_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lms_game` (
  `lms_game_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_game_start_wkno` varchar(6) NOT NULL,
  `lms_game_name` varchar(45) NOT NULL,
  `lms_game_status` int(1) NOT NULL DEFAULT 1,
  `lms_game_week_count` int(11) NOT NULL DEFAULT 0,
  `lms_game_total_players` int(11) NOT NULL DEFAULT 0,
  `lms_game_still_active` int(11) NOT NULL DEFAULT 0,
  `lms_game_manager` int(11) NOT NULL,
  `lms_game_code` varchar(6) NOT NULL,
  PRIMARY KEY (`lms_game_id`),
  UNIQUE KEY `lms_game_id_UNIQUE` (`lms_game_id`)
) ENGINE=InnoDB AUTO_INCREMENT=72 DEFAULT CHARSET=utf8;

CREATE TABLE `lms_game_league` (
  `lms_game_league_game_id` int(11) NOT NULL,
  `lms_game_league_league_id` int(11) NOT NULL,
  PRIMARY KEY (`lms_game_league_game_id`,`lms_game_league_league_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `lms_game_player` (
  `lms_game_id` int(11) NOT NULL,
  `lms_player_id` int(11) NOT NULL,
  `lms_game_player_status` int(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`lms_game_id`,`lms_player_id`),
  KEY `lms_player_id_idx` (`lms_player_id`),
  CONSTRAINT `fk_lms_game_player_game` FOREIGN KEY (`lms_game_id`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lms_game_player_player` FOREIGN KEY (`lms_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lms_game_player_status` (
  `lms_game_player_status_id` int(11) NOT NULL,
  `lms_game_player_status_text` varchar(20) NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`lms_game_player_status_id`),
  UNIQUE KEY `lms_game_player_status_id_UNIQUE` (`lms_game_player_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lms_game_status` (
  `lms_game_status_id` int(11) NOT NULL,
  `lms_game_status_text` varchar(20) NOT NULL DEFAULT 'unknown',
  PRIMARY KEY (`lms_game_status_id`),
  UNIQUE KEY `lms_game_status_id_UNIQUE` (`lms_game_status_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lms_info` (
  `lms_info_id` varchar(24) NOT NULL,
  `lms_info_value` varchar(256) DEFAULT '',
  PRIMARY KEY (`lms_info_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lms_league` (
  `lms_league_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_league_name` varchar(255) NOT NULL DEFAULT '',
  `lms_league_abbr` varchar(4) NOT NULL DEFAULT '',
  `lms_league_supported` tinyint(1) DEFAULT 0,
  PRIMARY KEY (`lms_league_id`),
  UNIQUE KEY `lms_league_id_UNIQUE` (`lms_league_id`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

CREATE TABLE `lms_league_team` (
  `lms_league_team_league_id` int(11) NOT NULL,
  `lms_league_team_team_id` int(11) NOT NULL,
  PRIMARY KEY (`lms_league_team_league_id`,`lms_league_team_team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `lms_league_team` (
  `lms_league_team_league_id` int(11) NOT NULL,
  `lms_league_team_team_id` int(11) NOT NULL,
  PRIMARY KEY (`lms_league_team_league_id`,`lms_league_team_team_id`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `lms_match` (
  `lms_match_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_match_weekno` varchar(6) NOT NULL,
  `lms_match_team` int(11) NOT NULL,
  `lms_match_date` datetime NOT NULL,
  `lms_match_result` char(1) NOT NULL DEFAULT '',
  `lms_match_league` int(11) NOT NULL DEFAULT 0,
  `lms_match_opp` int(11) DEFAULT NULL,
  `lms_match_ha` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`lms_match_id`),
  KEY `fk_weekno_idx` (`lms_match_weekno`),
  KEY `fk_team_idx` (`lms_match_team`),
  CONSTRAINT `fk_lms_match_lms_team1` FOREIGN KEY (`lms_match_team`) REFERENCES `lms_team` (`lms_team_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_match_weekno` FOREIGN KEY (`lms_match_weekno`) REFERENCES `lms_week` (`lms_week_no`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=9924 DEFAULT CHARSET=utf8;

CREATE TABLE `lms_pick` (
  `lms_pick_player_id` int(11) NOT NULL,
  `lms_pick_game_id` int(11) NOT NULL,
  `lms_pick_match_id` int(11) NOT NULL,
  `lms_pick_wl` char(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`lms_pick_player_id`,`lms_pick_game_id`,`lms_pick_match_id`),
  KEY `fk_lms_game_id_idx` (`lms_pick_game_id`),
  KEY `fk_lms_match_idx` (`lms_pick_match_id`),
  CONSTRAINT `fk_lms_pick_game` FOREIGN KEY (`lms_pick_game_id`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lms_pick_match` FOREIGN KEY (`lms_pick_match_id`) REFERENCES `lms_match` (`lms_match_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lms_pick_player` FOREIGN KEY (`lms_pick_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lms_player` (
  `lms_player_id` int(11) NOT NULL AUTO_INCREMENT,
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
  PRIMARY KEY (`lms_player_id`),
  UNIQUE KEY `lms_player_id_UNIQUE` (`lms_player_id`)
) ENGINE=InnoDB AUTO_INCREMENT=400 DEFAULT CHARSET=utf8;

CREATE TABLE `lms_player_temp_password` (
  `lms_player_id` int(11) NOT NULL,
  `lms_player_temp_password` varchar(100) NOT NULL,
  PRIMARY KEY (`lms_player_id`),
  UNIQUE KEY `idlms_player_id_UNIQUE` (`lms_player_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

CREATE TABLE `lms_results` (
  `lms_match_date` datetime NOT NULL,
  `lms_match_team` int(11) NOT NULL,
  `lms_match_team_score` int(11) DEFAULT NULL,
  `lms_match_team_wl` char(1) DEFAULT NULL,
  PRIMARY KEY (`lms_match_date`,`lms_match_team`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

CREATE TABLE `lms_team` (
  `lms_team_id` int(11) NOT NULL AUTO_INCREMENT,
  `lms_team_name` varchar(45) NOT NULL,
  `lms_team_active` tinyint(1) DEFAULT 1,
  `lms_team_wins` int(2) DEFAULT 0,
  `lms_team_abbr` char(3) NOT NULL DEFAULT '',
  PRIMARY KEY (`lms_team_id`)
) ENGINE=InnoDB AUTO_INCREMENT=75 DEFAULT CHARSET=utf8;

CREATE TABLE `lms_team_abbr` (
  `lms_team_abbr_abbr` varchar(3) NOT NULL,
  `lms_team_abbr_team_id` int(11) NOT NULL,
  PRIMARY KEY (`lms_team_abbr_abbr`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE `lms_week` (
  `lms_week_no` varchar(6) NOT NULL,
  `lms_week` int(2) NOT NULL,
  `lms_year` int(4) NOT NULL,
  `lms_week_start` datetime NOT NULL,
  `lms_week_end` datetime NOT NULL,
  `lms_week_deadline` datetime NOT NULL,
  `lms_week_state` int(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`lms_week_no`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

