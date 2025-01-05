CREATE TABLE `lms_available_picks` (
  `lms_available_picks_player_id` int(11) NOT NULL,
  `lms_available_picks_game` int(11) NOT NULL,
  `lms_available_picks_team` int(11) NOT NULL,
  `lms_available_picks_count` int(11) NOT NULL DEFAULT 1,
  PRIMARY KEY (`lms_available_picks_player_id`,`lms_available_picks_game`,`lms_available_picks_team`),
  KEY `lms_game_id_idx` (`lms_available_picks_game`),
  KEY `fk_lms_team_id_idx` (`lms_available_picks_team`),
  CONSTRAINT `fk_lms_available_picks_lms_team1` FOREIGN KEY (`lms_available_picks_team`) REFERENCES `lms_team` (`lms_team_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lms_avpick_game` FOREIGN KEY (`lms_available_picks_game`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lms_avpick_player` FOREIGN KEY (`lms_available_picks_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
