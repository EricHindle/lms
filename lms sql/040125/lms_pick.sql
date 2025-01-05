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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
