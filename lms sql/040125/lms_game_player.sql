CREATE TABLE `lms_game_player` (
  `lms_game_id` int(11) NOT NULL,
  `lms_player_id` int(11) NOT NULL,
  `lms_game_player_status` int(1) NOT NULL DEFAULT 1,
  `lms_game_player_elimination_week` varchar(6) DEFAULT '',
  `lms_game_player_outcome` int(11) DEFAULT 0,
  PRIMARY KEY (`lms_game_id`,`lms_player_id`),
  KEY `lms_player_id_idx` (`lms_player_id`),
  CONSTRAINT `fk_lms_game_player_game` FOREIGN KEY (`lms_game_id`) REFERENCES `lms_game` (`lms_game_id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_lms_game_player_player` FOREIGN KEY (`lms_player_id`) REFERENCES `lms_player` (`lms_player_id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_general_ci;
